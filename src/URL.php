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
 * @property array  body
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
	protected $body = [];

	/**
	 * URL constructor.
	 *
	 * @param string $url
	 * @param array  $params
	 * @param string $method
	 * @param array  $body
	 */
	public function __construct($url, array $params = [], $method = 'GET', array $body = []){
		$this->url    = $url;
		$this->params = $params;
		$this->body   = $body;

		$method = strtoupper($method);
		if(in_array($method, ['GET', 'POST'], true)){ // @todo
			$this->method = $method;
		}

		$this->parseUrl();
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
	 * @return string URL with merged params
	 */
	public function __toString(){
		return $this->mergeParams();
	}

	/**
	 * @return string
	 */
	public function originalParams(){
		return $this->getURL().'?'.http_build_query($this->parsedquery);
	}

	/**
	 * @return string
	 */
	public function overrideParams(){
		return $this->getURL().'?'.http_build_query($this->params);
	}

	/**
	 * @return string
	 */
	public function mergeParams(){
		return $this->getURL().'?'.http_build_query(array_merge($this->parsedquery, $this->params));
	}

	/**
	 * @return void
	 */
	protected function parseUrl(){
		$url = parse_url($this->url);

		$this->host      = !isset($url['host'])      ? null : $url['host'];
		$this->port      = !isset($url['port'])      ? null : $url['port'];
		$this->path      = !isset($url['path'])      ? null : $url['path'];
		$this->scheme    = !isset($url['scheme'])    ? null : $url['scheme'];
		$this->query     = !isset($url['query'])     ? null : $url['query'];
		$this->fragment  = !isset($url['fragment'])  ? null : $url['fragment'];

		if($this->query){
			parse_str($this->query, $this->parsedquery);
		}

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
