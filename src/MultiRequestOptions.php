<?php
/**
 *
 * @filesource   MultiRequestOptions.php
 * @created      15.02.2016
 * @package      chillerlan\TinyCurl
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2016 Smiley
 * @license      MIT
 */

namespace chillerlan\TinyCurl;

/**
 * Class MultiRequestOptions
 */
class MultiRequestOptions extends RequestOptions{

	public $handler = null;

	/**
	 * the base URL for each request, useful if you're hammering the same host all the time
	 *
	 * @var string
	 */
	public $base_url = '';

	/**
	 * maximum of concurrent requests
	 *
	 * @var int
	 */
	public $window_size = 5;

	/**
	 * wtb timeout
	 *
	 * @var int
	 */
	public $timeout = 10;

}
