<?php

namespace Pbmedia\Jwt;

use Illuminate\Support\Facades\Facade;

class JwtFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-jwt';
    }
}
