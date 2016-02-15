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
class MultiResponse extends ResponseBase implements ResponseInterface{

	/**
	 * MultiResponse constructor.
	 *
	 * @param resource $curl
	 * @param          $data
	 */
	public function __construct($curl, $data){
		parent::__construct($curl);

		$this->response_body = curl_exec($data);
		$this->getInfo();
		curl_close($this->curl);
	}

}
