<?php
/**
 * Class Request
 *
 * @filesource   Request.php
 * @created      13.02.2016
 * @package      chillerlan\TinyCurl
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2016 Smiley
 * @license      MIT
 */

namespace chillerlan\TinyCurl;

use chillerlan\TinyCurl\Response\Response;

/**
 *
 */
class Request{

	/**
	 * The cURL connection
	 *
	 * @var resource
	 */
	protected $curl;

	/**
	 * @var \chillerlan\TinyCurl\RequestOptions
	 */
	protected $options;

	/**
	 * Request constructor.
	 *
	 * @param \chillerlan\TinyCurl\RequestOptions|null $options
	 */
	public function __construct(RequestOptions $options = null){
		$this->setOptions($options ?: new RequestOptions);
	}

	/**
	 * @param \chillerlan\TinyCurl\RequestOptions $options
	 */
	public function setOptions(RequestOptions $options){
		$this->options = $options;
	}

	/**
	 * @param string $url
	 *
	 * @return \chillerlan\TinyCurl\Response\Response
	 */
	protected function getResponse($url){
		curl_setopt($this->curl, CURLOPT_URL, $url);

		return new Response($this->curl);
	}

	/**
	 * @return void
	 */
	protected function initCurl(){
		$this->curl = curl_init();

		$ca_info = is_file($this->options->ca_info) ? $this->options->ca_info : null;

		// set defaults
		curl_setopt_array($this->curl, [
			CURLOPT_PROTOCOLS => CURLPROTO_HTTP|CURLPROTO_HTTPS|CURLPROTO_FTP|CURLPROTO_FTPS,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => (bool)$ca_info,
			CURLOPT_SSL_VERIFYHOST => 2, // Support for value 1 removed in cURL 7.28.1
			CURLOPT_CAINFO         => $ca_info,
		]);

		// set the global request options
		curl_setopt_array($this->curl, $this->options->curl_options);
	}
	/**
	 * @param \chillerlan\TinyCurl\URL $url
	 * @param array                    $curl_options
	 *
	 * @return \chillerlan\TinyCurl\Response\Response
	 * @throws \chillerlan\TinyCurl\RequestException
	 */
	public function fetch(URL $url, array $curl_options = []){

		if(!$url->host || !in_array($url->scheme, ['http', 'https', 'ftp', 'ftps'], true)){
			throw new RequestException('$url');
		}

		$this->initCurl();

		switch($url->method){
			case 'GET':
				break;
			case 'POST':
				curl_setopt_array($this->curl, [
					CURLOPT_POST        => true,
					/*
					 * "Passing an array to CURLOPT_POSTFIELDS will encode the data as multipart/form-data,
					 *  while passing a URL-encoded string will encode the data as application/x-www-form-urlencoded."
					 * - PHP manual
					 */
					CURLOPT_POSTFIELDS  => $url->body,
				]);

				break;
		}

		// set per-request options
		curl_setopt_array($this->curl, $curl_options);

		return $this->getResponse((string)$url);
	}

	/**
	 * @param string $url
	 *
	 * @return array<string>
	 */
	public function extractShortUrl($url){
		$urls = [$url];

		while($url = $this->extract($url)){
			$urls[] = $url;
		}

		return $urls;
	}

	/**
	 * @param string $url
	 *
	 * @return string
	 * @link http://www.internoetics.com/2012/11/12/resolve-short-urls-to-their-destination-url-php-api/
	 */
	protected function extract($url){
		$this->initCurl();

		curl_setopt_array($this->curl, [
			CURLOPT_FOLLOWLOCATION => false
		]);

		$response = $this->getResponse($url);

		$info    = $response->info;
		$headers = $response->headers;

		switch(true){
			// check curl_info()
			case in_array($info->http_code, range(300, 308), true) && isset($info->redirect_url) && !empty($info->redirect_url):
				return $info->redirect_url;
			// look for a location header
			case isset($headers->location) && !empty($headers->location):
				return $headers->location; // @codeCoverageIgnore
			default:
				return '';
		}

	}

}
