PHP Wrapper for the Gauges API
==============================

[Gauges] (http://get.gaug.es/) is a real-time website analytics service.
This small library is designed to make consuming the
[Gauges API] (http://get.gaug.es/documentation/) simple using PHP.

Installation
------------

The recommended way to install this library is using
[Composer] (http://getcomposer.org).

Install composer and update your project's composer.json file to
include this library:

```javascript
{
    "require": {
        "kevintweber/gauges": "~0.2"
    }
}
```

Usage
----

Consuming the Gauges API with this library is super simple.

```php
use Kevintweber\Gauges\Factory;

// Create the request object.
$request = Factory::createRequest($your_gauges_api_token);

// Optionally, attach a PSR-3 logger.
$request->attachLogger($logger);

// Make an API call.
$response = $request->gauge_detail($gauge_id);

// Do something with the response.
$data = $response->json();
```

This library utilizes the Guzzle 5 library to make the API requests.
To understand what else you can do with the response object, please
refer to the [Guzzle documentation] (http://guzzle.readthedocs.org/en/latest/).

If you want to get really fancy, you can access the HTTP client itself
by calling:

```php
$client = $request->getHttpClient();

// Do something with the client.

$request->setHttpClient($client);
```

The client is documented in detail in the
[Guzzle documentation] (http://guzzle.readthedocs.org/en/latest/).

Author
------

Kevin Weber - <kevintweber@gmail.com>

License
-------

kevintweber/gauges is licensed under the MIT License - see the `LICENSE` file for details
