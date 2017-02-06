<?php
/**
 * Class RequestOptions
 *
 * @filesource   RequestOptions.php
 * @created      15.02.2016
 * @package      chillerlan\TinyCurl
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2016 Smiley
 * @license      MIT
 */

namespace chillerlan\TinyCurl;

/**
 *
 */
class RequestOptions{

	/**
	 * @var string
	 */
	public $user_agent = 'chillerLAN-php-curl';

	/**
	 * @var int
	 */
	public $timeout = 10;

	/**
	 * options for each curl instance
	 *
	 * @var array
	 */
	public $curl_options = [];

	/**
	 * CA Root Certificates for use with CURL/SSL
	 *
	 * @var string
	 * @link https://curl.haxx.se/ca/cacert.pem
	 */
	public $ca_info = null;

	/**
	 * @var int
	 */
	public $max_redirects = 0;

}
