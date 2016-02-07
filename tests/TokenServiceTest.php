<?php

namespace Pbmedia\Jwt\Test;

use Illuminate\Support\Facades\Config;
use Lcobucci\JWT\Token;
use Mockery;
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
        $user = Mockery::mock(UserStub::class);

        $user->shouldReceive('getQualifiedKeyForToken')
            ->andReturn(1);

        $token = (string) $this->getService()->generateTokenForUser($user);

        Config::shouldReceive('get')
            ->twice()
            ->with('laravel-jwt.issuer')
            ->andReturn('Pascal Baljet Media');

        Config::shouldReceive('get')
            ->once()
            ->with('laravel-jwt.model')
            ->andReturn(UserStub::class);

        $user = $this->getService()->findUserByTokenOrFail($token);

        $this->assertInstanceOf(UserStub::class, $user);
    }

    public function testTokenIsValid()
    {
        $token = (string) $this->getService()->generateTokenForUser(new UserStub);
        $this->assertTrue($this->getService()->tokenIsValid($token));
    }

    /**
     * @expectedException Pbmedia\Jwt\UserNotFoundException
     */
    public function testInvalidToken()
    {
        $token = 'Whoops.Not.Valid';

        $this->getService()->findUserByTokenOrFail($token);
        $this->assertFalse($this->getService()->tokenIsValid($token));
    }
}
