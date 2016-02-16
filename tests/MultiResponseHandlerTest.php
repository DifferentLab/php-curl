<?php
/**
 * Class MultiResponseHandlerTest
 *
 * @filesource   MultiResponseHandlerTest.php
 * @created      15.02.2016
 * @package      chillerlan\TinyCurl
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2016 Smiley
 * @license      MIT
 */

namespace chillerlan\TinyCurlTest;

use chillerlan\TinyCurl\MultiResponseHandlerInterface;
use chillerlan\TinyCurl\ResponseInterface;

/**
 *
 */
class MultiResponseHandlerTest implements MultiResponseHandlerInterface{

	/**
	 * @param \chillerlan\TinyCurl\ResponseInterface $response
	 *
	 * @return void
	 */
	public function handleResponse(ResponseInterface $response){
		var_dump([
			'http' =>$response->headers->statuscode,
			'content-length' => $response->headers->{'content-length'},
			'content-language' => $response->headers->{'content-language'},
		]);
	}

}
