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

class RequestTest extends \PHPUnit_Framework_TestCase{

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

	public function urlDataProvider(){
		return [
			['https://api.guildwars2.com/v2/account', [], [CURLOPT_HTTPHEADER => ['Authorization: Bearer 39519066-20B0-5545-9AE2-71109410A2CAF66348DA-50F7-4ACE-9363-B38FD8EE1881']]],
			['https://api.guildwars2.com/v2/account', ['access_token' => '39519066-20B0-5545-9AE2-71109410A2CAF66348DA-50F7-4ACE-9363-B38FD8EE1881'], []],
		];
	}

	/**
	 * @dataProvider urlDataProvider
	 */
	public function testInstance($url, array $params, array $curl_options){
		$responseWithCA = $this->requestWithCA->fetch($url, $params, $curl_options);
		$responseNoCA = $this->requestNoCA->fetch($url, $params, $curl_options);

		/* ¯\_(ツ)_/¯ */
		$this->assertEquals(200, $responseWithCA->info()->http_code);
		$this->assertEquals(200, $responseNoCA->info()->http_code);
		$this->assertEquals('*', $responseWithCA->headers()->{'access-control-allow-origin'});
		$this->assertEquals('*', $responseNoCA->headers()->{'access-control-allow-origin'});
		$this->assertEquals('A9EAD53E-4157-E111-BBF3-78E7D1936222', $responseWithCA->json()->id);
		$this->assertEquals('A9EAD53E-4157-E111-BBF3-78E7D1936222', $responseNoCA->json()->id);
	}

	public function shortURLDataProvider(){
		return [
			[
				[
					'https://t.co/ZSS6nVOcVp',
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
	public function testExtractShortUrl($expected){
		$this->assertEquals($expected, $this->requestWithCA->extractShortUrl($expected[0]));
	}

}
