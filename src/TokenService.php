<?php

namespace Pbmedia\Jwt;

use InvalidArgumentException;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\ValidationData;
use RuntimeException;

class TokenService
{
    public function generateHashForUser(AuthenticatableInterface $user, $time = null)
    {
        return md5(json_encode([
            app('config')->get('laravel-jwt.issuer'),
            $user->getQualifiedKeyForToken(),
            ($time ?: time()) + 1800,
        ]));
    }

    public function generateTokenForUser(AuthenticatableInterface $user, array $additionalData = [])
    {
        $time = time();

        $builder = (new Builder)
            ->setIssuer(app('config')->get('laravel-jwt.issuer'))
            ->setIssuedAt($time)
            ->setNotBefore($time)
            ->setExpiration($time + app('config')->get('laravel-jwt.expiration'))
            ->set('id', $user->getQualifiedKeyForToken())
            ->setId($this->generateHashForUser($user, $time), true);

        foreach ($additionalData as $key => $value) {
            $builder = $builder->set($key, $value);
        }

        return $builder->sign(new Sha256, app('config')->get('laravel-jwt.secret'))
            ->getToken();
    }

    public function getData($token, $key)
    {
        if (!$token instanceof Token) {
            $token = $this->getParsedToken($token);
        }

        return $token->getClaims()[$key]->getValue();
    }

    public function getParsedToken($token)
    {
        try {
            return (new Parser)->parse($token);
        } catch (RuntimeException $e) {
            throw new UserNotFoundException;
        } catch (InvalidArgumentException $e) {
            throw new UserNotFoundException;
        }
    }

    public function findUserByTokenOrFail($token)
    {
        $parsedToken = $this->getParsedToken($token);

        $userClass = app('config')->get('laravel-jwt.model');
        $user      = app($userClass)->findByQualifiedKeyForToken($this->getData($parsedToken, 'id'));

        if (!$user) {
            throw new UserNotFoundException;
        }

        $userHash = $this->generateHashForUser($user, $this->getData($parsedToken, 'iat'));

        $validationData = new ValidationData;
        $validationData->setIssuer(app('config')->get('laravel-jwt.issuer'));
        $validationData->setId($userHash);

        if ($parsedToken->validate($validationData)) {
            return $user;
        }

        throw new UserNotFoundException;
    }

    public function tokenIsValid($token)
    {
        try {
            $this->findUserByTokenOrFail($token);
            return true;
        } catch (UserNotFoundException $exception) {
            return false;
        }
    }
}
