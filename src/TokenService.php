<?php

namespace Pbmedia\Jwt;

use InvalidArgumentException;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\ValidationData;

class TokenService
{
    public function generateTokenForUser(AuthenticatableInterface $user)
    {
        $time = time();

        return (new Builder)
            ->set('id', $user->getQualifiedKeyForToken())
            ->setId($this->generateHashForUser($user, $time), true)
            ->setIssuer(config('laravel-jwt.issuer'))
            ->setIssuedAt($time)
            ->setNotBefore($time)
            ->setExpiration($time + config('laravel-jwt.expiration'))
            ->sign(new Sha256, config('laravel-jwt.secret'))
            ->getToken();
    }

    public function generateHashForUser(AuthenticatableInterface $user, $time = null)
    {
        return md5(json_encode([
            config('laravel-jwt.issuer'),
            $user->getQualifiedKeyForToken(),
            ($time ?: time()) + 1800,
        ]));
    }

    public function getUserByToken($token)
    {
        try {
            $token = (new Parser)->parse($token);
        } catch (InvalidArgumentException $e) {
            throw new UserNotFoundException;
        }

        $claims    = $token->getClaims();
        $userClass = config('laravel-jwt.model');

        $user = app($userClass)->findByQualifiedKeyForToken($claims['id']->getValue());

        if (!$user) {
            throw new UserNotFoundException;
        }

        $userHash = $this->generateHashForUser($user, $claims['iat']->getValue());

        $validationData = new ValidationData;
        $validationData->setIssuer(config('laravel-jwt.issuer'));
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
