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
use chillerlan\TinyCurl\Response\Response;
use chillerlan\TinyCurl\URL;
use stdClass;

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
	 * @var \chillerlan\TinyCurl\Response\ResponseInterface
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
		$response = $this->requestWithCA->fetch(new URL($url, $params));
		$this->assertApiResponse($response);
	}

	/**
	 * @dataProvider fetchDataProvider
	 */
	public function testFetchNoCA($url, array $params){
		$response = $this->requestNoCA->fetch(new URL($url, $params));
		$this->assertApiResponse($response);
	}


	protected function assertApiResponse($response){
		$this->assertEquals(0, $response->error->code);
		$this->assertEquals(200, $response->info->http_code);
		$this->assertEquals('*', $response->headers->{'access-control-allow-origin'});
		$this->assertEquals(self::GW2_ACC_ID, $response->json->id);
		$this->assertEquals(self::GW2_ACC_ID, $response->json_array['id']);
		$this->assertEquals('application/json; charset=utf-8', $response->body->content_type);
	}

	/**
	 * @expectedException \chillerlan\TinyCurl\RequestException
	 * @expectedExceptionMessage $url
	 */
	public function testFetchUrlSchemeException(){
		$this->requestWithCA->fetch(new URL('htps://whatever.wat'));
	}

	/**
	 * @expectedException \chillerlan\TinyCurl\Response\ResponseException
	 * @expectedExceptionMessage $curl
	 */
	public function testResponseNoCurlException(){
		$this->response = new Response(null);
	}

	/**
	 * @expectedException \chillerlan\TinyCurl\Response\ResponseException
	 * @expectedExceptionMessage !$property: foobar
	 */
	public function testResponseGetMagicFieldException(){
		$this->requestWithCA->fetch(new URL('https://api.guildwars2.com/v2/build'))->foobar;
	}

	public function testURLcoverage(){
		$url = new URL('https://api.guildwars2.com:443/v2/items?lang=de&ids=all', ['lang' => 'fr']);

		$this->assertEquals((string)$url, $url->mergeParams());
		$this->assertEquals('https://api.guildwars2.com:443/v2/items?lang=fr&ids=all', $url->mergeParams());
		$this->assertEquals('https://api.guildwars2.com:443/v2/items?lang=fr', $url->overrideParams());
	}
}
