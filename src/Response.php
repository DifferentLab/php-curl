<?php
/**
 *
 * @filesource   Response.php
 * @created      13.02.2016
 * @package      chillerlan\TinyCurl
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2016 Smiley
 * @license      MIT
 */

namespace chillerlan\TinyCurl;

use stdClass;

/**
 * Class Response
 *
 * @property mixed body
 * @property mixed error
 * @property mixed headers
 * @property mixed info
 * @property mixed json
 */
class Response{

	/**
	 * The cURL connection
	 *
	 * @var resource
	 */
	protected $curl;

	/**
	 * @var array
	 */
	protected $curl_info;

	/**
	 * @var \stdClass
	 */
	protected $response_headers;

	/**
	 * @var mixed
	 */
	protected $response_body;

	/**
	 * Response constructor.
	 *
	 * @param resource $curl
	 */
	public function __construct($curl){
		$this->curl             = $curl;
		$this->response_headers = new stdClass;
		$this->exec();
	}

	/**
	 * @throws \chillerlan\TinyCurl\ResponseException
	 */
	protected function exec(){
		if($this->curl){
			$options = [
				CURLOPT_HEADERFUNCTION => [$this, 'headerLine'],
			];

			curl_setopt_array($this->curl, $options);
			$this->response_body = curl_exec($this->curl);
			$this->curl_info     = curl_getinfo($this->curl);
		}
		else{
			throw new ResponseException('$this->curl');
		}
	}

	/**
	 * Farewell
	 */
	public function __destruct(){
		if($this->curl){
			curl_close($this->curl);
		}
	}

	/**
	 * @param string $field
	 *
	 * @return mixed
	 * @throws \chillerlan\TinyCurl\ResponseException
	 */
	public function __get($field){

		if(!$this->curl){
			throw new ResponseException('!$this->curl: '.$field);
		}

		switch($field){
			case 'body'   : return $this->getBody();
			case 'info'   : return $this->getInfo();
			case 'json'   : return json_decode($this->response_body);
#			case 'save'   : return true; // todo
			case 'error'  : return $this->getErrors();
			case 'headers': return $this->response_headers;
			default: throw new ResponseException('!$method: '.$field);
		}

	}

	protected function getErrors(){
		$error = new stdClass;
		$error->code = curl_errno($this->curl);
		$error->message = curl_error($this->curl);

		return $error;
	}

	/**
	 * @return \stdClass
	 */
	protected function getInfo(){
		$info = new stdClass;

		if(is_array($this->curl_info)){
			foreach($this->curl_info as $key => $value){
				$info->{$key} = $value;
			}
		}

		return $info;
	}

	/**
	 * @param resource $curl
	 * @param string   $header
	 *
	 * @return int
	 */
	protected function headerLine($curl, $header){

		if(substr($header, 0, 4) === 'HTTP') {
			$status = explode(' ', $header, 3);

			$this->response_headers->httpversion = explode('/', $status[0], 2)[1];
			$this->response_headers->statuscode  = intval($status[1]);
			$this->response_headers->statustext  = trim($status[2]);
		}

		$h = explode(':', $header, 2);
		if(count($h) === 2){
			$this->response_headers->{trim(strtolower($h[0]))} = trim($h[1]);
		}

		return strlen($header);
	}

	/**
	 * @return \stdClass
	 */
	protected function getBody(){
		$body = new stdClass;

		$body->content = $this->response_body;
		$body->length = strlen($this->response_body);

		if(isset($this->curl_info['content_type']) && !empty($this->curl_info['content_type'])){
			$body->content_type = $this->curl_info['content_type'];
		}
		elseif(isset($this->response_headers->content_type) && !empty($this->response_headers->content_type)){
			$body->content_type = $this->response_headers->content_type;
		}

		return $body;
	}

}
