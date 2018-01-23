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

namespace chillerlan\TinyCurl;

class Response extends ResponseAbstract{

	/** @inheritdoc */
	protected function exec(){
		curl_setopt($this->curl, CURLOPT_HEADERFUNCTION, [$this, 'headerLine']);
		$this->response_body = curl_exec($this->curl);
		$this->getInfo();
	}

	/**
	 * Farewell.
	 *
	 * @codeCoverageIgnore
	 */
	public function __destruct(){
		if(is_resource($this->curl)){
			curl_close($this->curl);
		}
	}


}
