<?php
/**
 *
 * @filesource   MultiResponseHandlerInterface.php
 * @created      16.02.2016
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2016 Smiley
 * @license      MIT
 */

namespace chillerlan\TinyCurl;

/**
 *
 */
interface MultiResponseHandlerInterface{

	/**
	 * @param \chillerlan\TinyCurl\ResponseInterface $response
	 *
	 * @return mixed
	 */
	public function handleResponse(ResponseInterface $response);

}