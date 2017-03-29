<?php
/**
 * Class MultiRequestOptions
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
 *
 */
class MultiRequestOptions extends RequestOptions{

	/**
	 * an optional handler FQCN
	 *
	 * @var string
	 */
	public $handler = null;

	/**
	 * maximum of concurrent requests
	 *
	 * @var int
	 */
	public $window_size = 5;

	/**
	 * sleep timer (milliseconds) between each fired request on startup
	 *
	 * @var int|float
	 */
	public $sleep = null;

}
