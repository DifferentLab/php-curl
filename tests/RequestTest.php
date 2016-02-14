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
use chillerlan\TinyCurl\Response;

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
	 * @var \chillerlan\TinyCurl\Response
	 */
	protected $response;

	protected function setUp(){
		$this->requestWithCA = new Request(__DIR__.'/test-cacert.pem');
		$this->requestNoCA = new Request;
	}

	public function testInstance(){
		/* ¯\_(ツ)_/¯ */

	}

	public function fetchDataProvider(){
		return [
			['https://api.guildwars2.com/v2/account', [], [CURLOPT_HTTPHEADER => ['Authorization: Bearer '.self::GW2_APIKEY]]],
			['https://api.guildwars2.com/v2/account', ['access_token' => self::GW2_APIKEY], []],
			['https://api.guildwars2.com/v2/account?lang=de', ['access_token' => self::GW2_APIKEY], []],
		];
	}

	/**
	 * @dataProvider fetchDataProvider
	 */
/*	public function testFetchWithCA($url, array $params, array $curl_options){
		$this->response = $this->requestWithCA->fetch($url, $params, $curl_options);

		$this->assertEquals(0, $this->response->error->code);
		$this->assertEquals(200, $this->response->info->http_code);
		$this->assertEquals('*', $this->response->headers->{'access-control-allow-origin'});
		$this->assertEquals(self::GW2_ACC_ID, $this->response->json->id);
		$this->assertEquals('application/json; charset=utf-8', $this->response->body->content_type);
	}
*/
	/**
	 * @dataProvider fetchDataProvider
	 */
/*	public function testFetchNoCA($url, array $params, array $curl_options){
		$this->response = $this->requestNoCA->fetch($url, $params, $curl_options);

		$this->assertEquals(0, $this->response->error->code);
		$this->assertEquals(200, $this->response->info->http_code);
		$this->assertEquals('*', $this->response->headers->{'access-control-allow-origin'});
		$this->assertEquals(self::GW2_ACC_ID, $this->response->json->id);
		$this->assertEquals('application/json; charset=utf-8', $this->response->body->content_type);
	}
*/
	public function shortURLDataProvider(){
		return [
			[
				[
					'https://t.co/YK4EuyMbl3',
					'http://buff.ly/20TJh3q',
					'http://www.ebay.com/sch/gillianandersoncharity/m.html?utm_content=buffer5675f&utm_medium=social&utm_source=twitter.com&utm_campaign=buffer',
				]
			],
			[
				[
					'https://t.co/ZSS6nVOcVp', // i wonder how long twitter will store this URL since the test tweet has been deleted
					'http://bit.ly/1oesmr8',
					'http://tinyurl.com/jvc5y98',
					'https://api.guildwars2.com/v2/build',
				],
			],
			[
				[
					'http://curl.haxx.se/ca/cacert.pem',
					'https://curl.haxx.se/ca/cacert.pem',

					// grabbing the body is perhaps a little too greedy...
#					'http://hg.mozilla.org/releases/mozilla-release/raw-file/default/security/nss/lib/ckfw/builtins/certdata.txt',
#					'http://mozilla.org/MPL/2.0/',
#					'https://www.mozilla.org/MPL/2.0/',
#					'https://www.mozilla.org/en-US/MPL/2.0/',
#					'http://html5shim.googlecode.com/svn/trunk/html5.js',
				],
			],
		];
	}

	/**
	 * @dataProvider shortURLDataProvider
	 */
/*	public function testExtractShortUrlWithCA($expected){
		$this->assertEquals($expected, $this->requestWithCA->extractShortUrl($expected[0]));
	}
*/
	/**
	 * @dataProvider shortURLDataProvider
	 */
/*	public function testExtractShortUrlNoCA($expected){
		$this->assertEquals($expected, $this->requestNoCA->extractShortUrl($expected[0]));
	}
*/
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
	 * @expectedExceptionMessage !$field: foobar
	 */
	public function testResponseGetMagicFieldException(){
		var_dump($this->requestWithCA->fetch('https://api.guildwars2.com/v2/build')->foobar);
	}

}
