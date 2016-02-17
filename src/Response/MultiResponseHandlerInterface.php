<?php
/**
 * Interface MultiResponseHandlerInterface
 *
 * @filesource   MultiResponseHandlerInterface.php
 * @created      16.02.2016
 * @package      chillerlan\TinyCurl\Response
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2016 Smiley
 * @license      MIT
 */

namespace chillerlan\TinyCurl\Response;

/**
 *
 */
interface MultiResponseHandlerInterface{

	/**
	 * @param \chillerlan\TinyCurl\Response\ResponseInterface $response
	 *
	 * @return void
	 */
	public function handleResponse(ResponseInterface $response);

}
