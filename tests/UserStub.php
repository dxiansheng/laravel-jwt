<?php

namespace Pbmedia\Jwt\Test;

use Pbmedia\Jwt\AuthenticatableInterface;

class UserStub implements AuthenticatableInterface
{
    public function findByQualifiedKeyForToken($id)
    {
        return new static;
    }

    public function getQualifiedKeyForToken()
    {
        return 1;
    }
}
