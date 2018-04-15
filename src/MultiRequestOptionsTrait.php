<?php
/**
 * Trait MultiRequestOptionsTrait
 *
 * @filesource   MultiRequestOptionsTrait.php
 * @created      15.04.2018
 * @package      chillerlan\TinyCurl
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2018 smiley
 * @license      MIT
 */

namespace chillerlan\TinyCurl;

trait MultiRequestOptionsTrait{

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
