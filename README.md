PHP Wrapper for the Gauges API
==============================

[Gaug.es](http://get.gaug.es/)

(todo)

Documentation
=============

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
use kevintweber\Gauges\Request;

// Create the request object.
$request = new Request($your_gauges_api_token);

// Make an API call.
$response = $request->gauge_detail($gauge_id);

// Do something with the response.
$data = $response->json();
```

About
=====

Author
------

Kevin Weber - <kevintweber@gmail.com>

License
-------

kevintweber/gauges is licensed under the MIT License - see the `LICENSE` file for details
