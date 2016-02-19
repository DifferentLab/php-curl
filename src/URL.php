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

	public $url;
	protected $host;
	protected $path;
	protected $scheme;
	protected $query;

	protected $params;
	protected $method;

	protected $retries;
	protected $lasterror;

	
	public function __construct($url, array $params = [], $method = null){
		$this->url    = $url;
		$this->params = $params;

		if(in_array(strtoupper($method), ['GET', 'POST'])){
			$this->method = $method;
		}

		$url = parse_url($url);
		$this->host   = !isset($url['host'])   ?: $url['host'];
		$this->path   = !isset($url['path'])   ?: $url['path'];
		$this->scheme = !isset($url['scheme']) ?: $url['scheme'];
		$this->query  = !isset($url['query'])  ?: $url['query'];
	}
	
	/*
	public function __get($name){
		return $this->{$name};
	}

	public function __set($name, $value){
		// TODO: Implement __set() method.
	}

	public function __toString(){
		// TODO: Implement __toString() method.
		return '';
	}
	
	*/
}
