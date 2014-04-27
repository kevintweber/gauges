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
        "kevintweber/gauges": "~0.1"
    }
}
```

Usage
----

Consuming the Gauges API with this library is super simple.

```php
use kevintweber\Gauges\Factory;

// Create the request object.
$request = Factory::createRequest($your_gauges_api_token);

// Make an API call.
$response = $request->gauge_detail($gauge_id);

// Do something with the response.
$data = $response->json();
```

This library utilizes the Guzzle 4 library to make the API requests.
To understand what else you can do with the response object, please
refer to the [Guzzle documentation] (http://guzzle.readthedocs.org/en/guzzle4/).

Now let's add logging. There is a helper method to allow you to easily add
a PSR-3 logger. Woot!

```php
use kevintweber\Gauges\Factory;

// Create the request object.
$request = Factory::createRequest($your_gauges_api_token);

// Now inject the logger.  (The format is optional.)
$request->attachLogger($logger, $format);
```

If you want to get really fancy, you can access the HTTP client's
event system by calling:

```php
$emitter = $request->getClientEmitter();
```

The emitter is documented in detail in the
[Guzzle documentation] (http://guzzle.readthedocs.org/en/guzzle4/).

Author
------

Kevin Weber - <kevintweber@gmail.com>

License
-------

kevintweber/gauges is licensed under the MIT License - see the `LICENSE` file for details
