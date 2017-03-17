PHP Wrapper for the Gauges API
==============================

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]

[Gauges] (http://get.gaug.es/) is a real-time website analytics service.
This small library is designed to make consuming the
[Gauges API] (http://get.gaug.es/documentation/) simple using PHP.

Installation
------------

The recommended way to install this library is using
[Composer] (http://getcomposer.org).

``` bash
$ composer require kevintweber/gauges
```

Usage
----

Consuming the Gauges API with this library is super simple:

```php
use Kevintweber\Gauges\Factory;

// Create the request object.
$request = Factory::createRequest($your_gauges_api_token);

// Optionally, set a PSR-3 logger.
$request->setLogger($logger);

// Make an API call.
$response = $request->gaugeDetail($gauge_id);

// The response is a Psr7 response.
$content = (string) $response->getContent();
$data = json_decode($content, true);
```

This library utilizes the Guzzle 6 library to make the API requests.
To understand what else you can do with the response object, please
refer to the [Guzzle documentation] (http://guzzle.readthedocs.org/en/latest/).

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email kevintweber@gmail.com instead of using the issue tracker.

## Credits

- [Kevin Weber][link-author]
- [All Contributors][link-contributors]

License
-------

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/kevintweber/gauges.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/kevintweber/gauges/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/kevintweber/gauges.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/kevintweber/gauges.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/kevintweber/gauges
[link-travis]: https://travis-ci.org/kevintweber/gauges
[link-scrutinizer]: https://scrutinizer-ci.com/g/kevintweber/gauges/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/kevintweber/gauges
[link-author]: https://github.com/kevintweber
[link-contributors]: ../../contributors
