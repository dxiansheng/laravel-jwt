<?php

namespace Pbmedia\Jwt\Test;

class ConfigStub
{
    public function get($key, $default = null)
    {
        $config = [
            'issuer'     => 'Pascal Baljet Media',
            'expiration' => 7 * 24 * 60 * 60,
            'secret'     => 'SecretKey',
            'model'      => UserStub::class,
        ];

        $key = explode('.', $key)[1];
        return $config[$key];
    }
}
