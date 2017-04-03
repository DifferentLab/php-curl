<?php
/**
 * Interface ResponseInterface
 *
 * @filesource   ResponseInterface.php
 * @created      15.02.2016
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2016 Smiley
 * @license      MIT
 */

namespace chillerlan\TinyCurl;

/**
 * @property mixed body
 * @property mixed error
 * @property mixed headers
 * @property mixed headers_array
 * @property mixed info
 * @property mixed json
 * @property mixed json_array
 */
interface ResponseInterface{

	/**
	 * @param string $property
	 *
	 * @return mixed
	 * @throws \chillerlan\TinyCurl\ResponseException
	 */
	public function __get($property);

}
