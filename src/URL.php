<?php
/**
 *
 * @filesource   URL.php
 * @created      18.02.2016
 * @package      chillerlan\TinyCurl
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2016 Smiley
 * @license      MIT
 */

namespace chillerlan\TinyCurl;

/**
 * Class URL
 *
 * @property string url
 * @property string method
 * @property string scheme
 * @property string host
 * @property string port
 * @property string path
 * @property string query
 * @property string fragment
 * @property array  params
 * @property array  parsedquery
 * @property array  merged_params
 */
class URL{

	/**
	 * @var string
	 */
	protected $url;

	/**
	 * @var string
	 */
	protected $method;

	/**
	 * @var string
	 */
	protected $scheme;

	/**
	 * @var string
	 */
	protected $host;

	/**
	 * @var int
	 */
	protected $port;

	/**
	 * @var string
	 */
	protected $path;

	/**
	 * @var string
	 */
	protected $query;

	/**
	 * @var string
	 */
	protected $fragment;

	/**
	 * @var array
	 */
	protected $parsedquery = [];

	/**
	 * @var array
	 */
	protected $params = [];

	/**
	 * @var array
	 */
	protected $merged_params = [];

	/**
	 * URL constructor.
	 *
	 * @param string $url
	 * @param array  $params
	 * @param string $method
	 */
	public function __construct($url, array $params = [], $method = 'GET'){
		$this->url    = $url;
		$this->params = $params;

		if(in_array(strtoupper($method), ['GET', 'POST'])){
			$this->method = $method;
		}

		$url = parse_url($url);
		$this->host      = !isset($url['host'])      ? null : $url['host'];
		$this->port      = !isset($url['port'])      ? null : $url['port'];
		$this->path      = !isset($url['path'])      ? null : $url['path'];
		$this->scheme    = !isset($url['scheme'])    ? null : $url['scheme'];
		$this->query     = !isset($url['query'])     ? null : $url['query'];
		$this->fragment  = !isset($url['fragment'])  ? null : $url['fragment'];

		if($this->query){
			parse_str($this->query, $this->parsedquery);
			$this->merged_params = array_merge($this->parsedquery, $this->params);
		}

	}

	/**
	 * @param $name
	 *
	 * @return mixed
	 */
	public function __get($name){
		return $this->{$name};
	}

	/**
	 * @return string
	 */
	public function __toString(){
		return $this->mergeParams();
	}

	/**
	 * @return string
	 */
	public function overrideParams(){
		$url  = $this->getURL();
		$url .= '?'.http_build_query($this->params);

		return $url;
	}

	/**
	 * @return string
	 */
	public function mergeParams(){
		$url  = $this->getURL();
		$url .= '?'.http_build_query($this->merged_params);

		return $url;
	}

	/**
	 * @return string
	 */
	protected function getURL(){
		$url = '';

		if($this->scheme){
			$url .= $this->scheme.':';
		}

		if($this->host){
			$url .= '//'.$this->host;

			if($this->port){
				$url .= ':'.$this->port;
			}

		}

		if($this->path){
			$url .= $this->path;
		}


		return $url;
	}

}
