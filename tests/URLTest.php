<?php
/**
 * @filesource   URLTest.php
 * @created      26.12.2016
 * @package      chillerlan\TinyCurlTest
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2016 Smiley
 * @license      MIT
 */

namespace chillerlan\TinyCurlTest;

use chillerlan\TinyCurl\URL;
use PHPUnit\Framework\TestCase;

class URLTest extends TestCase{

	/**
	 * @var \chillerlan\TinyCurl\URL
	 */
	protected $url;

	public function parseDataProvider(){
		return [
			['//localhost', 'localhost', null, null, null, null, null],
			['http://localhost', 'localhost', 'http', null, null, null, null],
			['http://localhost:80', 'localhost', 'http', 80, null, null, null],
			['http://localhost/whatever', 'localhost', 'http', null, '/whatever', null, null],
			['http://localhost?foo=bar', 'localhost', 'http', null, null, 'foo=bar', null],
			['http://localhost#blah', 'localhost', 'http', null, null, null, 'blah'],
			['http://localhost:80/whatever?foo=bar#blah', 'localhost', 'http', 80, '/whatever', 'foo=bar', 'blah'],
		];
	}

	/**
	 * @dataProvider parseDataProvider
	 */
	public function testParse($url, $host, $scheme, $port, $path, $query, $fragment){
		$this->url = new URL($url);

		$this->assertSame($scheme, $this->url->scheme);
		$this->assertSame($host, $this->url->host);
		$this->assertSame($port, $this->url->port);
		$this->assertSame($path, $this->url->path);
		$this->assertSame($query, $this->url->query);
		$this->assertSame($fragment, $this->url->fragment);

		$this->assertSame($url, (string)$this->url);
	}

	public function testGetUnknown(){
		$this->url = new URL('http://localhost');

		$this->assertSame(false, $this->url->foo);
	}

	public function testGetUrl(){
		$this->url = new URL('http://localhost?foo=bar', ['huh' => 'wtf']);

		$this->assertSame('http://localhost?foo=bar', $this->url->originalParams());
		$this->assertSame('http://localhost?huh=wtf', $this->url->overrideParams());
		$this->assertSame('http://localhost?foo=bar&huh=wtf', $this->url->mergeParams());
	}

	/**
	 * @expectedException \chillerlan\TinyCurl\URLException
	 * @expectedExceptionMessage invalid scheme: htps
	 */
	public function testInvalidSchemeException(){
		new URL('htps://foo.bar');
	}

	/**
	 * @expectedException \chillerlan\TinyCurl\URLException
	 * @expectedExceptionMessage no host given
	 */
	public function testNoHostException(){
		new URL('/foo/bar');
	}

	/**
	 * @expectedException \chillerlan\TinyCurl\URLException
	 * @expectedExceptionMessage invalid method: NOPE
	 */
	public function testInvalidMethodException(){
		new URL('https://foo.bar', [], 'nope');
	}

}
