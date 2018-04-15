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

use chillerlan\Traits\ContainerAbstract;

/**
 * @property string $user_agent
 * @property int    $timeout
 * @property array  $curl_options
 * @property string $ca_info
 * @property int    $max_redirects
 */
class RequestOptions extends ContainerAbstract{
	use RequestOptionsTrait;

}
