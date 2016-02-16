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
use stdClass;

/**
 *
 */
class MultiResponseHandlerTest implements MultiResponseHandlerInterface{

	protected $data = [];

	/**
	 * @param \chillerlan\TinyCurl\ResponseInterface $response
	 *
	 * @return void
	 */
	public function handleResponse(ResponseInterface $response){
		$data = new stdClass;
		$data->errorcode             = $response->error->code;
		$data->statuscode            = $response->info->http_code;
		$data->content_length_header = $response->headers->{'content-length'};
		$data->content_length_body   = $response->body->length;
		$data->content_type          = $response->body->content_type;
		$data->ids                   = array_column($response->json, 'id');

		sort($data->ids);

		$this->data[] = $data;
	}

	public function getResponseData(){
		return $this->data;
	}

}
