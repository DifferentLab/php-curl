<?php
/**
 * Interface MultiResponseHandlerInterface
 *
 * @filesource   MultiResponseHandlerInterface.php
 * @created      16.02.2016
 * @package      chillerlan\TinyCurl
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2016 Smiley
 * @license      MIT
 */

namespace chillerlan\TinyCurl;

interface MultiResponseHandlerInterface{

	/**
	 * The response handler.
	 *
	 * This method will be called within a loop in MultiRequest::processStack().
	 * You can either build your class around this MultiResponseHandlerInterface to process
	 * the response during runtime or return the response data to the running
	 * MultiRequest instance via addResponse() and receive the data by calling getResponseData().
	 *
	 * This method may return void or an URL object as a replacement for a failed request,
	 * which then will be re-added to the running queue.
	 *
	 * However, the return value will not be checked, so make sure you return valid URLs. ;)
	 *
	 * @param \chillerlan\TinyCurl\ResponseInterface $response
	 *
	 * @return void|\chillerlan\TinyCurl\URL
	 * @internal
	 */
	public function handleResponse(ResponseInterface $response);

}
