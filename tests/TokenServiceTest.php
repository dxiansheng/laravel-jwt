<?php

namespace Pbmedia\Jwt\Test;

use Illuminate\Support\Facades\Config;
use Lcobucci\JWT\Token;
use Orchestra\Testbench\TestCase;
use Pbmedia\Jwt\TokenService;

class TokenServiceTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        Config::swap(new ConfigStub);
    }

    private function getService()
    {
        return new TokenService;
    }

    public function testGenerateHashForUser()
    {
        Config::shouldReceive('get')
            ->once()
            ->with('laravel-jwt.issuer')
            ->andReturn('Pascal Baljet Media');

        $hash = $this->getService()->generateHashForUser(new UserStub);

        $this->assertTrue(is_string($hash));
        $this->assertTrue(strlen($hash) === 32);
    }

    public function testGenerateTokenForUser()
    {
        Config::shouldReceive('get')
            ->once()
            ->with('laravel-jwt.expiration')
            ->andReturn(604800);

        Config::shouldReceive('get')
            ->twice()
            ->with('laravel-jwt.issuer')
            ->andReturn('Pascal Baljet Media');

        Config::shouldReceive('get')
            ->once()
            ->with('laravel-jwt.secret')
            ->andReturn('SecretKey');

        $token = $this->getService()->generateTokenForUser(new UserStub);
        $this->assertInstanceOf(Token::class, $token);
    }

    public function testFindUserByTokenOrFail()
    {
        $token = (string) $this->getService()->generateTokenForUser(new UserStub);

        $user = $this->getService()->findUserByTokenOrFail($token);

        $this->assertInstanceOf(UserStub::class, $user);
    }

    /**
     * @expectedException Pbmedia\Jwt\UserNotFoundException
     */
    public function testInvalidToken()
    {
        $this->getService()->findUserByTokenOrFail('Whoops.Not.Valid');
    }
}