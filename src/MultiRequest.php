<?php
/**
 * Class MultiRequest
 *
 * @filesource   MultiRequest.php
 * @created      15.02.2016
 * @package      chillerlan\TinyCurl
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2016 Smiley
 * @license      MIT
 */

namespace chillerlan\TinyCurl;

/**
 * @link http://www.onlineaspect.com/2009/01/26/how-to-use-curl_multi-without-blocking/
 * @link https://github.com/joshfraser/rolling-curl
 *
 * (there are countless implementations around, just google for "php rolling curl")
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
	 * An array of the request URLs
	 *
	 * @var array<\chillerlan\TinyCurl\URL>
	 */
	protected $stack = [];

	/**
	 * The returned value from MultiResponseHandlerInterface::handleResponse() for each request
	 *
	 * @var array
	 */
	protected $responses = [];

	/**
	 * @var \chillerlan\TinyCurl\MultiRequestOptions
	 */
	protected $options;

	/**
	 * @var \chillerlan\TinyCurl\MultiResponseHandlerInterface
	 */
	protected $multiResponseHandler;

	/**
	 * MultiRequest constructor.
	 *
	 * @param \chillerlan\TinyCurl\MultiRequestOptions|null $options
	 */
	public function __construct(MultiRequestOptions $options = null){
		$this->setOptions($options ?: new MultiRequestOptions);
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
	 * @param \chillerlan\TinyCurl\MultiRequestOptions $options
	 *
	 * @return \chillerlan\TinyCurl\MultiRequest
	 */
	public function setOptions(MultiRequestOptions $options):MultiRequest {
		$this->options = $options;

		if($this->options->handler){
			$this->setHandler();
		}

		$this->curl_options = $this->options->curl_options + [
			CURLOPT_HEADER         => true,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_USERAGENT      => $this->options->user_agent,
			CURLOPT_PROTOCOLS      => CURLPROTO_HTTP|CURLPROTO_HTTPS,
			CURLOPT_SSL_VERIFYPEER => true,
			CURLOPT_SSL_VERIFYHOST => 2, // Support for value 1 removed in cURL 7.28.1
			CURLOPT_CAINFO         => is_file($this->options->ca_info) ? $this->options->ca_info : null,
		];

		return $this;
	}

	/**
	 * @param \chillerlan\TinyCurl\MultiResponseHandlerInterface|null $handler
	 *
	 * @return \chillerlan\TinyCurl\MultiRequest
	 * @throws \chillerlan\TinyCurl\RequestException
	 */
	public function setHandler(MultiResponseHandlerInterface $handler = null):MultiRequest {

		if(!$handler){

			if(!class_exists($this->options->handler)){
				throw new RequestException('no handler set');
			}

			$handler = new $this->options->handler($this);

			if(!is_a($handler, MultiResponseHandlerInterface::class)){
				throw new RequestException('handler is not a MultiResponseHandlerInterface');
			}

		}

		$this->multiResponseHandler = $handler;

		return $this;
	}

	/**
	 * @param array $urls array of \chillerlan\TinyCurl\URL objects
	 *
	 * @return void
	 * @throws \chillerlan\TinyCurl\RequestException
	 */
	public function fetch(array $urls){

		if(empty($urls)){
			throw new RequestException('$urls is empty');
		}

		$this->stack      = $urls;
		$this->curl_multi = curl_multi_init();

		curl_multi_setopt($this->curl_multi, CURLMOPT_PIPELINING, 2);
		curl_multi_setopt($this->curl_multi, CURLMOPT_MAXCONNECTS, $this->options->window_size);

		// shoot out the first batch of requests
		array_map(function(){
			$this->createHandle();
		}, range(1, $this->options->window_size));

		/// ...and start processing the stack
		$this->processStack();
	}

	/**
	 * @see \chillerlan\TinyCurl\MultiResponseHandlerInterface
	 *
	 * @param mixed $response
	 *
	 * @return void
	 */
	public function addResponse($response){
		$this->responses[] = $response;
	}

	/**
	 * @return array
	 */
	public function getResponseData():array {
		return $this->responses;
	}

	/**
	 * creates a new cURL handle
	 *
	 * @return void
	 */
	protected function createHandle(){

		if(!empty($this->stack)){
			$url = array_shift($this->stack);

			if($url instanceof URL){
				$curl = curl_init($url->mergeParams());

				curl_setopt_array($curl, $this->curl_options);
				curl_multi_add_handle($this->curl_multi, $curl);

				if($this->options->sleep){
					usleep($this->options->sleep);
				}

			}
			else{
				// retry on next if we don't get what we expect
				$this->createHandle(); // @codeCoverageIgnore
			}

		}

	}

	/**
	 * processes the requests
	 *
	 * @return void
	 */
	protected function processStack(){

		do{

			do {
				$status = curl_multi_exec($this->curl_multi, $active);
			}
			while($status === CURLM_CALL_MULTI_PERFORM);

			// welcome to callback hell.
			while($state = curl_multi_info_read($this->curl_multi)){
				$url = $this->multiResponseHandler->handleResponse(new MultiResponse($state['handle']));

				if($url instanceof URL){
					$this->stack[] = $url;
				}

				curl_multi_remove_handle($this->curl_multi, $state['handle']);
				curl_close($state['handle']);
				$this->createHandle();
			}

			if($active){
				curl_multi_select($this->curl_multi, $this->options->timeout);
			}

		}
		while($active && $status === CURLM_OK);

	}

}
