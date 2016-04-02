<?php
/**
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
 * Class RequestOptions
 */
class RequestOptions{

	/**
	 * options for each curl instance
	 *
	 * @var array
	 */
	public $curl_options = [];

	/**
	 * whitelist too?
	 *
	 * @var array
	 */
	public $hostBlacklist = [];

	/**
	 * CA Root Certificates for use with CURL/SSL
	 *
	 * @var string
	 * @link https://curl.haxx.se/ca/cacert.pem
	 */
	public $ca_info = null;

}
