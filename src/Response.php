<?php
/**
 * Class Response
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
 * @property mixed body
 * @property mixed error
 * @property mixed headers
 * @property mixed info
 * @property mixed json
 */
class Response{

	/**
	 * @var \stdClass
	 */
	protected $curl_info;

	/**
	 * @var \stdClass
	 */
	protected $response_headers;

	/**
	 * @var \stdClass
	 */
	protected $response_error;

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
		$this->curl_info        = new stdClass;
		$this->response_error   = new stdClass;
		$this->response_headers = new stdClass;
		$this->exec($curl);
	}

	/**
	 * @param string $field
	 *
	 * @return mixed
	 * @throws \chillerlan\TinyCurl\ResponseException
	 */
	public function __get($field){

		switch($field){
			case 'body'   : return $this->getBody();
			case 'info'   : return $this->curl_info;
			case 'json'   : return json_decode($this->response_body);
			case 'error'  : return $this->response_error;
			case 'headers': return $this->response_headers;
			default: throw new ResponseException('!$field: '.$field);
		}

	}

	/**
	 * @param resource $curl
	 *
	 * @throws \chillerlan\TinyCurl\ResponseException
	 * @see self::headerLine()
	 */
	protected function exec($curl){
		if($curl){
			curl_setopt($curl, CURLOPT_HEADERFUNCTION, [$this, 'headerLine']);

			$this->response_body = curl_exec($curl);

			$this->response_error->code    = curl_errno($curl);
			$this->response_error->message = curl_error($curl);
#			$this->response_error->version = curl_version();

			$curl_info = curl_getinfo($curl);

			if(is_array($curl_info)){
				foreach($curl_info as $key => $value){
					$this->curl_info->{$key} = $value;
				}
			}

			curl_close($curl);
		}
		else{
			throw new ResponseException('$curl');
		}
	}

	/**
	 * @param resource $curl
	 * @param string   $header
	 *
	 * @return int
	 *
	 * @link http://php.net/manual/function.curl-setopt.php CURLOPT_HEADERFUNCTION
	 */
	protected function headerLine(/** @noinspection PhpUnusedParameterInspection */ $curl, $header){

		if(substr($header, 0, 4) === 'HTTP'){
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
		$body->length  = strlen($this->response_body);

		if(isset($this->curl_info->content_type) && !empty($this->curl_info->content_type)){
			$body->content_type = $this->curl_info->content_type;
		}
		// @codeCoverageIgnoreStart
		elseif(isset($this->response_headers->content_type) && !empty($this->response_headers->content_type)){
			$body->content_type = $this->response_headers->content_type;
		}
		// @codeCoverageIgnoreEnd

		return $body;
	}

}
