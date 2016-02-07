<?php

namespace Pbmedia\Jwt;

interface AuthenticatableInterface
{
    public function findByQualifiedKeyForToken($id);

    public function getQualifiedKeyForToken();
}
