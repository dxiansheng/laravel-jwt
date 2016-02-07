<?php

namespace Pbmedia\Jwt;

use InvalidArgumentException;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
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

    public function generateTokenForUser(AuthenticatableInterface $user)
    {
        $time = time();

        return (new Builder)
            ->setIssuer(app('config')->get('laravel-jwt.issuer'))
            ->setIssuedAt($time)
            ->setNotBefore($time)
            ->setExpiration($time + app('config')->get('laravel-jwt.expiration'))
            ->set('id', $user->getQualifiedKeyForToken())
            ->setId($this->generateHashForUser($user, $time), true)
            ->sign(new Sha256, app('config')->get('laravel-jwt.secret'))
            ->getToken();
    }

    public function findUserByTokenOrFail($token)
    {
        try {
            $token = (new Parser)->parse($token);
        } catch (RuntimeException $e) {
            throw new UserNotFoundException;
        } catch (InvalidArgumentException $e) {
            throw new UserNotFoundException;
        }

        $claims    = $token->getClaims();
        $userClass = app('config')->get('laravel-jwt.model');

        $user = app($userClass)->findByQualifiedKeyForToken($claims['id']->getValue());

        if (!$user) {
            throw new UserNotFoundException;
        }

        $userHash = $this->generateHashForUser($user, $claims['iat']->getValue());

        $validationData = new ValidationData;
        $validationData->setIssuer(app('config')->get('laravel-jwt.issuer'));
        $validationData->setId($userHash);

        if ($token->validate($validationData)) {
            return $user;
        }

        throw new UserNotFoundException;
    }

    public function tokenIsValid($token)
    {
        try {
            $this->getUserByToken($token);
            return true;
        } catch (UserNotFoundException $exception) {
            return false;
        }
    }
}
