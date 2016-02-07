<?php

namespace Pbmedia\Jwt\Test;

use Illuminate\Support\Facades\Config;
use Orchestra\Testbench\TestCase;
use Pbmedia\Jwt\TokenService;

class TokenServiceTest extends TestCase
{
    private function getService()
    {
        return new TokenService;
    }

    public function testGenerateHashForUser()
    {
        Config::shouldReceive('get')
            ->once()
            ->with('laravel-jwt.issuer');

        $hash = $this->getService()->generateHashForUser(new UserStub);

        $this->assertTrue(is_string($hash));
        $this->assertTrue(strlen($hash) === 32);
    }
}