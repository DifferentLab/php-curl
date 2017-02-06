<?php
/**
 * @filesource   RequestTest.php
 * @created      04.02.2017
 * @package      chillerlan\TinyCurlTest
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2016 Smiley
 * @license      MIT
 */

namespace chillerlan\TinyCurlTest;

use chillerlan\TinyCurl\{Request, RequestOptions, Response, URL};

class RequestTest extends \PHPUnit_Framework_TestCase{

	const CAPATH  = __DIR__.'/test-cacert.pem';
	const HTTPBIN = 'https://httpbin.org';

	/**
	 * @var \chillerlan\TinyCurl\Request
	 */
	protected $request;

	/**
	 * @var \chillerlan\TinyCurl\RequestOptions
	 */
	protected $options;

	protected function setUp(){
		$this->options = new RequestOptions;
		$this->options->ca_info = self::CAPATH;

		$this->request = new Request($this->options);
	}

	public function testInstanceWithoutArgsCoverage(){
		$this->assertInstanceOf(Request::class, $this->request);
	}

	public function testNormalizeHeaders(){
		$headers = [
			'Content-Type' => 'application/x-www-form-urlencoded',
			'Content-Type: application/x-www-form-urlencoded',
		    'what',
		];

		$this->assertSame(['Content-type' => 'Content-type: application/x-www-form-urlencoded'], $this->request->normalizeHeaders($headers));
	}

	public function fetchDataProvider(){
		return [
			['get',    []],
			['post',   []],
			['post',   ['Content-type' => 'multipart/form-data']],
			['put',    []],
			['delete', []],
		];
	}

	/**
	 * @dataProvider fetchDataProvider
	 */
	public function testFetch($method, $extra_headers){
		$url = new URL(self::HTTPBIN.'/'.$method, ['foo' => 'bar'], $method, ['huh' => 'wtf'], ['what' => 'nope'] + $extra_headers);

		$response = $this->request->fetch($url)->json;

		$this->assertSame(self::HTTPBIN.'/'.$method.'?foo=bar', $response->url);
		$this->assertSame('bar', $response->args->foo);
		$this->assertSame('nope', $response->headers->What);

		if(in_array($method, ['post', 'put'])){
			$this->assertSame('wtf', $response->form->huh);

			if(!empty($extra_headers)){
				$this->assertContains('multipart/form-data; boundary=', $response->headers->{'Content-Type'});
			}
		}
	}

	/**
	 * coverage
	 */
	public function testRequestOptions(){
		$this->options = new RequestOptions;

		$this->options->ca_info = self::CAPATH;
		$this->options->user_agent = 'foobar';
		$this->options->max_redirects = 1;
		$this->options->timeout = 1;

		(new Request($this->options))->fetch(new URL(self::HTTPBIN.'/get'));
	}

	/**
	 * coverage
	 */
	public function testResponse(){
		$response = $this->request->fetch(new URL(self::HTTPBIN.'/get?foo=bar'));

		$this->assertSame('application/json', $response->body->content_type);
		$this->assertSame(self::HTTPBIN.'/get?foo=bar', $response->info->url);
		$this->assertSame('bar', $response->json->args->foo);
		$this->assertSame('bar', $response->json_array['args']['foo']);
		$this->assertSame(0, $response->error->code);
		$this->assertSame('application/json', $response->headers->{'content-type'});

		$this->assertSame(false, $response->foo);
	}

	/**
	 * @expectedException \chillerlan\TinyCurl\ResponseException
	 * @expectedExceptionMessage no cURL handle given
	 */
	public function testResponseCurlException(){
		new Response(null);
	}
}
