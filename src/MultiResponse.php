<?php
/**
 * Class MultiResponse
 *
 * @filesource   MultiResponse.php
 * @created      15.02.2016
 * @package      chillerlan\TinyCurl
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2016 Smiley
 * @license      MIT
 */

namespace chillerlan\TinyCurl;

/**
 *
 */
class MultiResponse extends ResponseAbstract{

	/**
	 * Fills self::$response_body and calls self::getInfo()
	 */
	protected function exec(){
		$response = explode("\r\n\r\n", curl_multi_getcontent($this->curl), 2);
		$headers = isset($response[0]) ? explode("\r\n", $response[0]) : null;
		$this->response_body = isset($response[1]) ? $response[1] : null;
		$this->getInfo();

		if(is_array($headers)){
			foreach($headers as $line){
				$this->headerLine($this->curl, $line);
			}
		}

	}

}
