<?php

namespace Pbmedia\Jwt;

use Illuminate\Support\ServiceProvider;

class JwtServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../resources/config/laravel-jwt.php' => config_path('laravel-jwt.php'),
        ], 'config');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../resources/config/laravel-jwt.php',
            'laravel-jwt'
        );

        $this->app->bind('laravel-jwt', function () {
            return new TokenService;
        });
    }

    public function provides()
    {
        return ['laravel-jwt'];
    }
}
