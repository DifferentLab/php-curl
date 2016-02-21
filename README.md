[![version][packagist-badge]][packagist]
[![license][license-badge]][license]
[![Travis][travis-badge]][travis]
[![Coverage][coverage-badge]][coverage]
[![Issues][issue-badge]][issues]
[![SensioLabsInsight][sensio-badge]][sensio]

[packagist-badge]: https://img.shields.io/packagist/v/chillerlan/php-curl.svg?style=flat-square
[packagist]: https://packagist.org/packages/chillerlan/php-curl
[license-badge]: https://img.shields.io/packagist/l/chillerlan/php-curl.svg?style=flat-square
[license]: https://github.com/codemasher/php-curl/blob/master/LICENSE
[travis-badge]: https://img.shields.io/travis/codemasher/php-curl.svg?style=flat-square
[travis]: https://travis-ci.org/codemasher/php-curl
[coverage-badge]: https://img.shields.io/codecov/c/github/codemasher/php-curl.svg?style=flat-square
[coverage]: https://codecov.io/github/codemasher/php-curl
[issue-badge]: https://img.shields.io/github/issues/codemasher/php-curl.svg?style=flat-square
[issues]: https://github.com/codemasher/php-curl/issues
[sensio-badge]: https://img.shields.io/sensiolabs/i/efcadc7a-c386-4c1b-916d-fc8e1ad7075b.svg?style=flat-square
[sensio]: https://insight.sensiolabs.com/projects/efcadc7a-c386-4c1b-916d-fc8e1ad7075b

# codemasher/php-curl
A [simple](https://twitter.com/andrey_butov/status/654035612513796096) cURL wrapper, mostly for API scraping purposes.

Features:

 - No PSR-7!
 - No 87 extra layers of abstraction!
 - No fancy!
   
In case you're looking for that: go along, use Guzzle instead. 

## Requirements
- **PHP 5.6+** or **PHP 7**
- **MySQL** or **MariaDB** for the GW2 API example

## Documentation
### Installation using [composer](https://getcomposer.org)
You can simply clone the repo and run `composer install` in the root directory

In case you want to include it elsewhere, just add the following to your *composer.json*.
```json
{
	"require": {
		"php": ">=5.6.0",
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

##Disclaimer!
I don't take responsibility for molten phone lines, bloated hard disks, self-induced DDoS, broken screens etc. Use at your own risk! ;)
