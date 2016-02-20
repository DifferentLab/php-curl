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
 */
class URL{

	protected $url;

	protected $host;
	protected $path;
	protected $scheme;
	protected $query;

	protected $params;
	protected $method;


	public function __construct($url, array $params = [], $method = 'GET'){
		$this->url    = $url;
		$this->params = $params;

		if(in_array(strtoupper($method), ['GET', 'POST'])){
			$this->method = $method;
		}

		$url = parse_url($url);
		$this->host      = !isset($url['host'])      ? null : $url['host'];
		$this->path      = !isset($url['path'])      ? null : $url['path'];
		$this->scheme    = !isset($url['scheme'])    ? null : $url['scheme'];
		$this->query     = !isset($url['query'])     ? null : $url['query'];
		$this->fragment  = !isset($url['fragment'])  ? null : $url['fragment'];
	}

	public function __toString(){
		return $this->url;
	}
	
	/*
	public function __get($name){
		return $this->{$name};
	}

	public function __set($name, $value){
		// TODO: Implement __set() method.
	}
	*/
}
