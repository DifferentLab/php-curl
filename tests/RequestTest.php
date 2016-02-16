<?php
/**
 *
 * @filesource   RequestTest.php
 * @created      13.02.2016
 * @package      chillerlan\TinyCurlTest
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2016 Smiley
 * @license      MIT
 */

namespace chillerlan\TinyCurlTest;

use chillerlan\TinyCurl\Request;
use chillerlan\TinyCurl\RequestOptions;
use chillerlan\TinyCurl\Response;
use chillerlan\TinyCurl\ResponseInterface;

class RequestTest extends \PHPUnit_Framework_TestCase{

	const GW2_APIKEY = '39519066-20B0-5545-9AE2-71109410A2CAF66348DA-50F7-4ACE-9363-B38FD8EE1881';
	const GW2_ACC_ID = 'A9EAD53E-4157-E111-BBF3-78E7D1936222';
	const GW2_GUILD  = '75FD83CF-0C45-4834-BC4C-097F93A487AF';
	const GW2_CHARS  = 'Skin Receiver';

	/**
	 * @var \chillerlan\TinyCurl\Request
	 */
	protected $requestWithCA;

	/**
	 * @var \chillerlan\TinyCurl\Request
	 */
	protected $requestNoCA;

	/**
	 * @var ResponseInterface
	 */
	protected $response;

	protected function setUp(){

		$co = [
			CURLOPT_HTTPHEADER => ['Authorization: Bearer '.self::GW2_APIKEY]
		];

		$o1 = new RequestOptions;
		$o1->curl_options = $co;
		$o1->ca_info = __DIR__.'/test-cacert.pem';
		$this->requestWithCA = new Request($o1);

		$o2 = new RequestOptions;
		$o2->curl_options = $co;
		$this->requestNoCA = new Request($o2);
	}

	public function testInstanceWithoutArgsCoverage(){
		$this->assertInstanceOf(Request::class, new Request); // HA HA.
	}

	public function fetchDataProvider(){
		return [
			['https://api.guildwars2.com/v2/account', []],
			['https://api.guildwars2.com/v2/account?lang=de', []],
			['https://api.guildwars2.com/v2/account?lang=de', ['lang' => 'fr']],
		];
	}

	/**
	 * @dataProvider fetchDataProvider
	 */
	public function testFetchWithCA($url, array $params){
		$this->response = $this->requestWithCA->fetch($url, $params);

		$this->assertEquals(0, $this->response->error->code);
		$this->assertEquals(200, $this->response->info->http_code);
		$this->assertEquals('*', $this->response->headers->{'access-control-allow-origin'});
		$this->assertEquals(self::GW2_ACC_ID, $this->response->json->id);
		$this->assertEquals('application/json; charset=utf-8', $this->response->body->content_type);
	}

	/**
	 * @dataProvider fetchDataProvider
	 */
	public function testFetchNoCA($url, array $params){
		$this->response = $this->requestNoCA->fetch($url, $params);

		$this->assertEquals(0, $this->response->error->code);
		$this->assertEquals(200, $this->response->info->http_code);
		$this->assertEquals('*', $this->response->headers->{'access-control-allow-origin'});
		$this->assertEquals(self::GW2_ACC_ID, $this->response->json->id);
		$this->assertEquals('application/json; charset=utf-8', $this->response->body->content_type);
	}


	/**
	 * @expectedException \chillerlan\TinyCurl\RequestException
	 * @expectedExceptionMessage $url
	 */
	public function testFetchUrlException(){
		$this->requestWithCA->fetch('');
	}

	/**
	 * @expectedException \chillerlan\TinyCurl\ResponseException
	 * @expectedExceptionMessage $curl
	 */
	public function testResponseNoCurlException(){
		$this->response = new Response(null);
	}

	/**
	 * @expectedException \chillerlan\TinyCurl\ResponseException
	 * @expectedExceptionMessage !$property: foobar
	 */
	public function testResponseGetMagicFieldException(){
		var_dump($this->requestWithCA->fetch('https://api.guildwars2.com/v2/build')->foobar);
	}

}
