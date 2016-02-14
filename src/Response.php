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
 * @method string        body()
 * @method stdClass      info()
 * @method object|array  json($to_array = false)
 * @method bool          save($path)
 * @method stdClass      headers($name = null)
 */
class Response{

	/**
	 * The cURL connection
	 *
	 * @var resource
	 */
	protected $curl;

	/**
	 * @var \stdClass
	 */
	protected $responseHeaders;

	/**
	 * @var mixed
	 */
	protected $body;

	/**
	 * Response constructor.
	 *
	 * @param resource $curl
	 */
	public function __construct($curl){
		$this->curl = $curl;
		$this->responseHeaders = new stdClass;
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
			$this->body = curl_exec($this->curl);

#			$errno = curl_errno($this->curl);
#			$errstr = curl_error($this->curl);
#			var_dump([$errno, $errstr]);
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
	 * @param string $method
	 * @param array  $arguments
	 *
	 * @return mixed
	 * @throws \chillerlan\TinyCurl\ResponseException
	 */
	public function __call($method, $arguments){

		if(!$this->curl){
			return false;
		}

		$arg = isset($arguments[0]) && !empty($arguments[0]) ? $arguments[0] : null;

		switch($method){
			case 'body'   : return $this->body;
			case 'info'   : return $this->getInfo();
			case 'json'   : return json_decode($this->body, (bool)$arg);
#			case 'save'   : return true; // todo
			case 'headers': return $this->responseHeaders;
			default: throw new ResponseException('$method: '.$method);
		}

	}

	/**
	 * @return \stdClass
	 */
	protected function getInfo(){
		$curl_info = curl_getinfo($this->curl);
		$info = new stdClass;

		if(is_array($curl_info)){
			foreach($curl_info as $key => $value){
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

			$this->responseHeaders->httpversion = explode('/', $status[0], 2)[1];
			$this->responseHeaders->statuscode  = intval($status[1]);
			$this->responseHeaders->statustext  = trim($status[2]);
		}

		$h = explode(':', $header, 2);
		if(count($h) === 2){
			$this->responseHeaders->{trim(strtolower($h[0]))} = trim($h[1]);
		}

		return strlen($header);
	}

}
