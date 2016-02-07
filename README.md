# pascalbaljetmedia/laravel-jwt

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

Simple JWT service for Laravel

## Install

Via Composer

``` bash
$ composer require pascalbaljetmedia/laravel-jwt
```

## Usage

Add the Laravel Service Provider and Facade to your ```app.php``` config file:

``` php
return [
    'providers' => [
        Pbmedia\Jwt\JwtServiceProvider::class,
    ],

    'aliases' => [
        'Jwt' => Pbmedia\Jwt\JwtFacade::class,
    ]
];
```

Then publish the config file and update it to your needs:
``` bash
$ php artisan vendor:publish --provider=Pbmedia\Jwt\JwtServiceProvider
```


Make sure your User Model implements ```AuthenticatableInterface```:
``` php
use Pbmedia\Jwt\AuthenticatableInterface;

class User extends Model implements AuthenticatableInterface
{
    public function findByQualifiedKeyForToken($id)
    {
        return static::find($id);
    }

    public function getQualifiedKeyForToken()
    {
        return $this->getKey();
    }
}
```

Now you can use ```TokenService``` to generate tokens, find users and validate tokens:
``` php
use \Jwt;

$user = User::first();

$token = (string) Jwt::generateTokenForUser($user);
$user = Jwt::findUserByTokenOrFail($token);
$validToken = Jwt::tokenIsValid($token);
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

## Security

If you discover any security related issues, please email pascal@pascalbaljetmedia.com instead of using the issue tracker.

## Credits

- [Pascal Baljet Media][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/pascalbaljetmedia/laravel-jwt.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/pascalbaljetmedia/laravel-jwt/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/pascalbaljetmedia/laravel-jwt.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/pascalbaljetmedia/laravel-jwt.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/pascalbaljetmedia/laravel-jwt.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/pascalbaljetmedia/laravel-jwt
[link-travis]: https://travis-ci.org/pascalbaljetmedia/laravel-jwt
[link-scrutinizer]: https://scrutinizer-ci.com/g/pascalbaljetmedia/laravel-jwt/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/pascalbaljetmedia/laravel-jwt
[link-downloads]: https://packagist.org/packages/pascalbaljetmedia/laravel-jwt
[link-author]: https://github.com/pascalbaljetmedia
[link-contributors]: ../../contributors