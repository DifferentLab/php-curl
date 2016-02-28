<?php
/**
 *
 * @filesource   RequestTraitTest.php
 * @created      22.02.2016
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2016 Smiley
 * @license      MIT
 */

namespace chillerlan\TinyCurlTest;

use chillerlan\TinyCurl\Traits\RequestTrait;

class RequestTraitTest extends \PHPUnit_Framework_TestCase{
	use RequestTrait;

	const GW2_APIKEY = '39519066-20B0-5545-9AE2-71109410A2CAF66348DA-50F7-4ACE-9363-B38FD8EE1881';
	const GW2_ACC_ID = 'A9EAD53E-4157-E111-BBF3-78E7D1936222';

	protected function setUp(){
		$this->setRequestCA(__DIR__.'/test-cacert.pem');
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
	public function testTraitFetch($url, array $params){
		$response = $this->fetch($url, $params, [CURLOPT_HTTPHEADER => ['Authorization: Bearer '.self::GW2_APIKEY]]);

		$this->assertEquals(0, $response->error->code);
		$this->assertEquals(200, $response->info->http_code);
		$this->assertEquals('*', $response->headers->{'access-control-allow-origin'});
		$this->assertEquals(self::GW2_ACC_ID, $response->json->id);
		$this->assertEquals('application/json; charset=utf-8', $response->body->content_type);
	}

}
