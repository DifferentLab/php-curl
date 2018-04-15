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
 * @property string    $handler
 * @property int       $window_size
 * @property int|float $sleep
 */
class MultiRequestOptions extends RequestOptions{
	use MultiRequestOptionsTrait;

}
