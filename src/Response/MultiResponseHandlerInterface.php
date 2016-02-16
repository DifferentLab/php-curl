<?php
/**
 *
 * @filesource   MultiResponseHandlerInterface.php
 * @created      16.02.2016
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2016 Smiley
 * @license      MIT
 */

namespace chillerlan\TinyCurl\Response;

use chillerlan\TinyCurl\MultiRequest;

/**
 *
 */
interface MultiResponseHandlerInterface{

	/**
	 * MultiResponseHandlerTest constructor.
	 *
	 * @param \chillerlan\TinyCurl\MultiRequest $request
	 */
	public function __construct(MultiRequest &$request);

	/**
	 * @param \chillerlan\TinyCurl\Response\ResponseInterface $response
	 *
	 * @return mixed
	 */
	public function handleResponse(ResponseInterface $response);

}