<?php
/**
 * Trait RequestOptionsTrait
 *
 * @filesource   RequestOptionsTrait.php
 * @created      15.04.2018
 * @package      chillerlan\TinyCurl
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2018 smiley
 * @license      MIT
 */

namespace chillerlan\TinyCurl;

trait RequestOptionsTrait{

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
