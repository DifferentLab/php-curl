<?php
/**
 *
 * @filesource   MultiRequest.php
 * @created      15.02.2016
 * @package      chillerlan\TinyCurl
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2016 Smiley
 * @license      MIT
 */

namespace chillerlan\TinyCurl;

use chillerlan\TinyCurl\Response\MultiResponse;
use chillerlan\TinyCurl\Response\MultiResponseHandlerInterface;

/**
 * Class MultiRequest
 *
 * @link http://www.onlineaspect.com/2009/01/26/how-to-use-curl_multi-without-blocking/
 */
class MultiRequest{

	/**
	 * the curl_multi master handle
	 *
	 * @var resource
	 */
	protected $curl_multi;

	/**
	 * cURL options for each handle
	 *
	 * @var array
	 */
	protected $curl_options = [];

	/**
	 * the request URLs - make sure to specify the full URL if you don't use $base_url
	 *
	 * @var array
	 */
	protected $urls = [];

	/**
	 * The returned value from MultiResponseHandlerInterface::handleResponse() for each request
	 *
	 * @var array
	 */
	protected $responses = [];

	/**
	 * concurrent request counter
	 *
	 * @var int
	 */
	protected $request_count = 0;

	/**
	 * @var \chillerlan\TinyCurl\MultiRequestOptions
	 */
	protected $options;

	/**
	 * @var \chillerlan\TinyCurl\Response\MultiResponseHandlerInterface
	 */
	protected $multiResponseHandler;

	/**
	 * MultiRequest constructor.
	 *
	 * @param \chillerlan\TinyCurl\MultiRequestOptions $options
	 */
	public function __construct(MultiRequestOptions $options){
		$this->options = $options;
		$this->setHandler();
		$ca_info = is_file($this->options->ca_info) ? $this->options->ca_info : null;
		$this->curl_options = $this->options->curl_options + [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => (bool)$ca_info,
			CURLOPT_SSL_VERIFYHOST => 2, // Support for value 1 removed in cURL 7.28.1
			CURLOPT_CAINFO         => $ca_info,
			CURLOPT_HEADER         => true,
		];
	}

	/**
	 * closes the curl_multi instance
	 *
	 * @codeCoverageIgnore
	 */
	public function __destruct(){
		if($this->curl_multi){
			curl_multi_close($this->curl_multi);
		}
	}

	/**
	 * @param \chillerlan\TinyCurl\Response\MultiResponseHandlerInterface|null $handler
	 *
	 * @return $this
	 * @throws \chillerlan\TinyCurl\RequestException
	 */
	public function setHandler(MultiResponseHandlerInterface $handler = null){

		if(!$handler){

			if(!class_exists($this->options->handler)){
				throw new RequestException('!$this->options->handler');
			}

			$handler = new $this->options->handler($this);

			if(!is_a($handler, MultiResponseHandlerInterface::class)){
				throw new RequestException('!is_a($handler)');
			}

		}

		$this->multiResponseHandler = $handler;

		return $this;
	}

	/**
	 * @param array $urls
	 *
	 * @return $this
	 * @throws \chillerlan\TinyCurl\RequestException
	 */
	public function fetch(array $urls){
		$this->urls = $urls;
		$this->request_count = count($this->urls);
		$this->curl_multi = curl_multi_init();
		$this->getResponse();

		return $this;
	}

	/**
	 * @param mixed $response
	 *
	 * @see \chillerlan\TinyCurl\Response\MultiResponseHandlerInterface
	 * @return $this
	 */
	public function addResponse($response){
		$this->responses[] = $response;

		return $this;
	}

	/**
	 * @return array
	 */
	public function getResponseData(){
		return $this->responses;
	}

	/**
	 * creates a new handle for $request[$index]
	 *
	 * @param $index
	 */
	protected function createHandle($index){
		$curl = curl_init($this->options->base_url.$this->urls[$index]);
		curl_setopt_array($curl, $this->curl_options);
		curl_multi_add_handle($this->curl_multi, $curl);
	}

	/**
	 * processes the requests
	 */
	protected function getResponse(){

		if($this->request_count < $this->options->window_size){
			$this->options->window_size = $this->request_count;
		}

		for($i = 0; $i < $this->options->window_size; $i++){
			$this->createHandle($i);
		}

		do{

			if(curl_multi_exec($this->curl_multi, $active) !== CURLM_OK){
				break; // @codeCoverageIgnore
			}

			while($state = curl_multi_info_read($this->curl_multi)){
				// welcome to callback hell.
				$this->multiResponseHandler->handleResponse(new MultiResponse($state['handle']));

				if($i < $this->request_count && isset($this->urls[$i])){
					$this->createHandle($i);
					$i++;
				}

				curl_multi_remove_handle($this->curl_multi, $state['handle']);
			}

			if($active){
				curl_multi_select($this->curl_multi, $this->options->timeout);
			}

		}
		while($active);

	}

}
