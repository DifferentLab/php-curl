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
	 * wtb timeout
	 *
	 * @var int
	 */
	public $timeout = 10;

}
