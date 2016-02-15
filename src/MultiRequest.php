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

/**
 * Class MultiRequest
 */
class MultiRequest{

	/**
	 * the base URL for each request, useful if you're hammering the same host all the time
	 * @var string
	 */
	protected $base_url = '';

	/**
	 * the request URLs - make sure to specify the full URL if you don't use $base_url
	 * @var array
	 */
	protected $urls = [];

	/**
	 * maximum of concurrent requests
	 * @var int
	 */
	protected $window_size = 5;

	/**
	 * options for each curl instance - make sure to append CURLOPT_RETURNTRANSFER = true if you specify your own
	 * @var array
	 */
	protected $curl_options = [
		CURLOPT_RETURNTRANSFER => true,
	];

	/**
	 * wtb timeout
	 * @var int
	 */
	protected $timeout = 10;

	/**
	 * callback function to process the incoming data
	 * @var callable
	 */
	protected $callback;

	/**
	 * the curl_multi master handle
	 * @var resource
	 */
	protected $handle;

	/**
	 * concurrent request counter
	 * @var int
	 */
	protected $request_count = 0;


	/**
	 * initializes the curl_multi and sets some needed variables
	 *
	 * @param callable $callback
	 *
	 * array $urls,
	 */
	public function __construct(){
		$this->handle = curl_multi_init();
#		$this->callback = $callback;
	}

	/**
	 * closes the curl instance
	 */
	public function __destruct(){
		curl_multi_close($this->handle);
	}

	/**
	 * creates a new handle for $request[$index]
	 *
	 * @param $index
	 */
	protected function create_handle($index){
		$ch = curl_init($this->base_url.$this->urls[$index]);
		curl_setopt_array($ch, $this->curl_options);
		curl_multi_add_handle($this->handle, $ch);
	}

	/**
	 * processes the requests
	 */
	public function process(){
		if($this->request_count < $this->window_size){
			$this->window_size = $this->request_count;
		}

		for($i = 0; $i < $this->window_size; $i++){
			$this->create_handle($i);
		}

		do{
			if(curl_multi_exec($this->handle, $active) !== CURLM_OK){
				break;
			}
			while($state = curl_multi_info_read($this->handle)){
				if($i < $this->request_count && isset($this->urls[$i])){
					$this->create_handle($i);
					$i++;
				}
				$this->getResponse($state['handle']);
			}
			if($active){
				curl_multi_select($this->handle, $this->timeout);
			}
		}
		while($active);
	}

	/**
	 * @param $curl
	 */
	protected function getResponse($curl){
		call_user_func($this->callback, curl_multi_getcontent($curl), curl_getinfo($curl));
		curl_multi_remove_handle($this->handle, $curl);
	}

}
