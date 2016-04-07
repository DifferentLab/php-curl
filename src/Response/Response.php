<?php
/**
 * Class Response
 *
 * @filesource   Response.php
 * @created      15.02.2016
 * @package      chillerlan\TinyCurl
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2016 Smiley
 * @license      MIT
 */

namespace chillerlan\TinyCurl\Response;

class Response extends ResponseAbstract{

	/**
	 * Fills self::$response_body and calls self::getInfo()
	 */
	protected function exec(){
		curl_setopt($this->curl, CURLOPT_HEADERFUNCTION, [$this, 'headerLine']);
		$this->response_body = curl_exec($this->curl);
		$this->getInfo();
	}

}
