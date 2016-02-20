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
use chillerlan\TinyCurl\Response\ResponseInterface;

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
	 * @param \chillerlan\TinyCurl\RequestOptions $options
	 */
	public function __construct(RequestOptions $options = null){

		if(!$options){
			$options = new RequestOptions;
		}

		$this->options = $options;
	}

	/**
	 * @param string $url
	 *
	 * @return ResponseInterface
	 */
	protected function getResponse($url){
		$ca_info = is_file($this->options->ca_info) ? $this->options->ca_info : null;

		curl_setopt_array($this->curl, $this->options->curl_options + [
			CURLOPT_URL            => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => (bool)$ca_info,
			CURLOPT_SSL_VERIFYHOST => 2, // Support for value 1 removed in cURL 7.28.1
			CURLOPT_CAINFO         => $ca_info,
		]);

		return new Response($this->curl);
	}

	/**
	 * @param \chillerlan\TinyCurl\URL $url
	 *
	 * @return \chillerlan\TinyCurl\Response\ResponseInterface
	 * @throws \chillerlan\TinyCurl\RequestException
	 */
	public function fetch(URL $url){
		$this->curl = curl_init();

		if(!$url->host || !in_array($url->scheme, ['http', 'https', 'ftp'], true)){
			throw new RequestException('$url');
		}

		return $this->getResponse((string)$url);
	}

	/**
	 * @param string $url
	 *
	 * @return array
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
	 * @return string|bool
	 * @link http://www.internoetics.com/2012/11/12/resolve-short-urls-to-their-destination-url-php-api/
	 */
	protected function extract($url){
		$this->curl = curl_init();

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
			default: return false;
		}

	}

}
