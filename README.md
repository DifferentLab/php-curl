# codemasher/php-curl
A [simple](https://twitter.com/andrey_butov/status/654035612513796096) cURL wrapper, mostly for API scraping purposes.

[![version][packagist-badge]][packagist]
[![license][license-badge]][license]
[![Travis][travis-badge]][travis]
[![Coverage][coverage-badge]][coverage]
[![Scrunitizer][scrutinizer-badge]][scrutinizer]
[![Code Climate][codeclimate-badge]][codeclimate]

[packagist-badge]: https://img.shields.io/packagist/v/chillerlan/php-curl.svg
[packagist]: https://packagist.org/packages/chillerlan/php-curl
[license-badge]: https://img.shields.io/packagist/l/chillerlan/php-curl.svg
[license]: https://github.com/codemasher/php-curl/blob/master/LICENSE
[travis-badge]: https://travis-ci.org/codemasher/php-curl.svg?branch=master
[travis]: https://travis-ci.org/codemasher/php-curl
[coverage-badge]: https://codecov.io/github/codemasher/php-curl/coverage.svg?branch=master
[coverage]: https://codecov.io/github/codemasher/php-curl
[scrutinizer-badge]: https://scrutinizer-ci.com/g/codemasher/php-curl/badges/quality-score.png?b=master
[scrutinizer]: https://scrutinizer-ci.com/g/codemasher/php-curl
[codeclimate-badge]: https://codeclimate.com/github/codemasher/php-curl/badges/gpa.svg
[codeclimate]: https://codeclimate.com/github/codemasher/php-curl

##Features:

 - No PSR-7!
 - No 87 extra layers of abstraction!
 - No fancy!
   
In case you're looking for that: go along, use Guzzle instead. 

## Requirements
- **PHP 7+**
- **MySQL** or **MariaDB** for the [GW2 API](https://api.guildwars2.com/v2) example

## Documentation

### Installation using [composer](https://getcomposer.org)
You can simply clone the repo and run `composer install` in the root directory. 
In case you want to include it elsewhere, just add the following to your *composer.json*:
```json
{
	"require": {
		"php": ">=7.0.7",
		"chillerlan/php-curl": "dev-master"
	}
}
```

### Manual installation

Download the desired version of the package from [master](https://github.com/codemasher/php-curl/archive/master.zip) or 
[release](https://github.com/codemasher/php-curl/releases) and extract the contents to your project folder. 
Point the namespace `chillerlan/TinyCurl` to the folder `src` of the package.

Profit!

### Usage

#### `Request`

The most simple way:
```php
use chillerlan\TinyCurl\{Request, URL};

$response = (new Request)->fetch(new URL('http://example.url/path'));

//do stuff
$json = $response->json;
```

Ways to set options:
```php
use chillerlan\TinyCurl\{Request, RequestOptions, URL};

$options = new RequestOptions;
$options->ca_info = '/path/to/cacert.pem';

// while creating the Request instance
$request = new Request($options);

// on an existing Request instance:
$request->setOptions($options);

// ...
```

The `Request` class also features a method to easily extract short URLs:
 ```php
use chillerlan\TinyCurl\Request;

(new Request)->extractShortUrl('https://t.co/ZSS6nVOcVp');
 ```

#### `RequestTrait`

The `RequestTrait` is a somewhat stripped down variant to quick and easy implement. Using the trait in your own classes:
```php
class MyClass{
	use RequestTrait;
	
	public function __construct(){
		$this->setRequestCA('/path/to/cacert.pem');
	}
	
	public function doStuff(){
		$response = $this->fetch(new URL('https://example.url/path'));
	}
}
```

#### `MultiRequest`

The `MultiRequest` is an implementation of `curl_multi` a.k.a. "[rolling cURL](https://github.com/joshfraser/rolling-curl)" to process multiple cURL requests
in parallel, non-blocking. Please refer to the tests & examples and my [GW2 database](https://github.com/codemasher/gw2-database) for further details on the implementation.


####  Properties of `RequestOptions`

property | type | default | allowed | description
-------- | ---- | ------- | ------- | -----------
`$user_agent` | string | 'chillerLAN-php-curl' | * | the user agent used for each request
`$timeout` | int | 10 | * | request timeout
`$curl_options` | array | `[]` | `[*]` | cURL options for each instance
`$ca_info` | string | null | * | path to a [cacert](https://curl.haxx.se/ca/cacert.pem)
`$max_redirects` | int | 0 | * | maximum redirects

####  Properties of `MultiRequestOptions`

The `MultiRequestOptions` object extends `RequestOptions` with the following properties:

property | type | default | allowed | description
-------- | ---- | ------- | ------- | -----------
`$handler` | string | null | * | an optional handler FQCN (implements `MultiResponseHandlerInterface`)
`$window_size` | int | 5 | * | maximum of concurrent requests
`$sleep` | int | 100 | * | sleep timer (milliseconds) between each fired request on startup
 
 
## Disclaimer!
I don't take responsibility for molten phone lines, bloated hard disks, self-induced DDoS, broken screens etc. Use at your own risk! ;)
