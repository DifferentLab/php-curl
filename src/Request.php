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
		$this->setOptions($options ?: new RequestOptions);
	}

	/**
	 * @param \chillerlan\TinyCurl\RequestOptions $options
	 *
	 * @return \chillerlan\TinyCurl\Request
	 */
	public function setOptions(RequestOptions $options):Request {
		$this->options = $options;

		return $this;
	}

	/**
	 * @param string $url
	 *
	 * @return \chillerlan\TinyCurl\ResponseInterface
	 */
	protected function getResponse(string $url):ResponseInterface {
		curl_setopt($this->curl, CURLOPT_URL, $url);

		return new Response($this->curl);
	}

	/**
	 * @return void
	 */
	protected function initCurl(){
		$this->curl = curl_init();

		curl_setopt_array($this->curl, [
			CURLOPT_HEADER         => false,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_USERAGENT      => $this->options->user_agent,
			CURLOPT_PROTOCOLS      => CURLPROTO_HTTP|CURLPROTO_HTTPS,
			CURLOPT_SSL_VERIFYPEER => true,
			CURLOPT_SSL_VERIFYHOST => 2, // Support for value 1 removed in cURL 7.28.1
			CURLOPT_CAINFO         => is_file($this->options->ca_info) ? $this->options->ca_info : null,
			CURLOPT_TIMEOUT        => $this->options->timeout,
		]);

		curl_setopt_array($this->curl, $this->options->curl_options);
	}

	/**
	 * @param \chillerlan\TinyCurl\URL $url
	 * @param array                    $curl_options
	 *
	 * @return \chillerlan\TinyCurl\ResponseInterface
	 * @throws \chillerlan\TinyCurl\RequestException
	 */
	public function fetch(URL $url, array $curl_options = []):ResponseInterface {
		$this->initCurl();

		$headers = $this->normalizeHeaders($url->headers);

		if(in_array($url->method, ['POST', 'PUT'])){

			$curl_options += $url->method === 'PUT'
				? [CURLOPT_CUSTOMREQUEST => 'PUT']
				: [CURLOPT_POST => true];

			if(!isset($headers['Content-type']) && $url->method === 'POST' && is_array($url->body)){
				$headers += ['Content-type: application/x-www-form-urlencoded'];
				$body     = http_build_query($url->body, '', '&', PHP_QUERY_RFC1738);
			}
			else{
				$body = $url->body;
			}

			$curl_options += [CURLOPT_POSTFIELDS => $body];
		}
		else{
			$curl_options += [CURLOPT_CUSTOMREQUEST => $url->method];
		}

		$headers += [
			'Host: '.$url->host,
			'Connection: close',
		];

		if($this->options->max_redirects > 0){
			$curl_options += [
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_MAXREDIRS      => $this->options->max_redirects,
			];
		}

		$curl_options += [CURLOPT_HTTPHEADER => $headers];

		curl_setopt_array($this->curl, $curl_options);

		return $this->getResponse((string)$url);
	}

	/**
	 * @param array $headers
	 *
	 * @return array
	 */
	public function normalizeHeaders(array $headers):array {
		$normalized_headers = [];

		foreach($headers as $key => $val){

			if(is_numeric($key)){
				$header = explode(':', $val, 2);

				if(count($header) === 2){
					$key = $header[0];
					$val = $header[1];
				}
				else{
					continue;
				}
			}

			$key = ucfirst(strtolower($key));

			$normalized_headers[$key] = trim($key).': '.trim($val);
		}

		return $normalized_headers;
	}

	/**
	 * @param string $url
	 *
	 * @return array<string>
	 */
	public function extractShortUrl(string $url):array {
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
	protected function extract(string $url):string {
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
