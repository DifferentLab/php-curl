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

use chillerlan\TinyCurl\{MultiRequest, MultiResponseHandlerInterface, ResponseInterface, URL};

/**
 *
 */
class MultiResponseHandlerTest implements MultiResponseHandlerInterface{

	/**
	 * @var \chillerlan\TinyCurl\MultiRequest
	 */
	protected $request;

	/**
	 * @var array
	 */
	protected $retries = [];

	public function __construct(MultiRequest $request){
		$this->request = $request;
	}

	/**
	 * @param \chillerlan\TinyCurl\ResponseInterface $response
	 *
	 * @return bool|\chillerlan\TinyCurl\URL|bool
	 */
	public function handleResponse(ResponseInterface $response){

		$data = new \stdClass;

		$data->errorcode             = $response->error->code;
		$data->statuscode            = $response->info->http_code;
		$data->content_length_header = $response->headers->{'content-length'};
		$data->content_length_body   = $response->body->length;
		$data->content_type          = $response->body->content_type;
		$data->ids                   = array_column($response->json, 'id');
		$data->hash                  = md5($response->info->url);

		sort($data->ids);


		if(!isset($this->retries[$data->hash])){
			$this->retries[$data->hash] = 0;
		}

		$data->retry = $this->retries[$data->hash];

		$this->request->addResponse($data);

		if($this->retries[$data->hash] < 3){
			$this->retries[$data->hash]++;
			return new URL($response->info->url);
		}

		return false;
	}

}
