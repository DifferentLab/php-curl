<?php
/**
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
 * Class Request
 */
class Request{

	/**
	 * @var string
	 */
	protected $ca_info;

	/**
	 * @todo whitelist instead or too?
	 * @var array
	 */
	protected $hostBlacklist = [];

	/**
	 * Request constructor.
	 *
	 * @param string $ca_info
	 */
	public function __construct($ca_info = null){
		$this->ca_info = $ca_info;
	}

	/**
	 * @param string $url
	 * @param array  $curl_options
	 *
	 * @return \chillerlan\TinyCurl\Response
	 */
	protected function getResponse($url, array $curl_options){
		$curl = curl_init($url);
		$ca_info = is_file($this->ca_info) ? $this->ca_info : null;

		curl_setopt_array($curl, $curl_options + [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => (bool)$ca_info,
			CURLOPT_SSL_VERIFYHOST => 2, // Support for value 1 removed in cURL 7.28.1
			CURLOPT_CAINFO         => $ca_info,
		]);

		return new Response($curl);
	}

	/**
	 * @param string $url
	 * @param array  $params
	 * @param array  $curl_options
	 *
	 * @return \chillerlan\TinyCurl\Response
	 * @throws \chillerlan\TinyCurl\RequestException
	 */
	public function fetch($url, array $params = [], array $curl_options = []){
		$parsedURL = parse_url($url);

		if(
			   !isset($parsedURL['scheme'])
			|| !isset($parsedURL['host'])
			|| !in_array($parsedURL['scheme'], ['http', 'https', 'ftp'], true)
			|| (!empty($this->hostBlacklist) && in_array($parsedURL['host'], $this->hostBlacklist, true))
		){
			throw new RequestException('$url');
		}

		$request_url = $parsedURL['scheme'].'://'.$parsedURL['host'];

		if(isset($parsedURL['path']) && !empty($parsedURL['path'])){
			$request_url .= $parsedURL['path'];
		}

		if(isset($parsedURL['query']) && !empty($parsedURL['query'])){
			parse_str($parsedURL['query'], $url_params);
			$params = array_merge($url_params, $params);
		}

		if(count($params) > 0){
			$request_url .= '?'.http_build_query($params);
		}

		return $this->getResponse($request_url, $curl_options);
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
		$response = $this->getResponse($url, [CURLOPT_FOLLOWLOCATION => false]);

		$info    = $response->info();
		$headers = $response->headers();

#		preg_match_all('~(http|https)://[^<>[:space:]]+[[:alnum:]#?/&=+%_]~', $response->body(), $body_urls);

		switch(true){
			// check curl_info()
			case in_array($info->http_code, range(300, 308), true) && isset($info->redirect_url) && !empty($info->redirect_url):
				return $info->redirect_url;
			// look for a location header
			case isset($headers->location) && !empty($headers->location):
				return $headers->location;
			// as a fallback, grab the first url we can find in the body - greedy!
#			case isset($body_urls[0]) && !empty($body_urls[0]):
#				return $body_urls[0][0];
			default: return false;
		}

	}

}
