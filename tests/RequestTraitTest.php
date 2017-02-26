<?php
/**
 * @filesource   RequestTraitTest.php
 * @created      22.02.2016
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2016 Smiley
 * @license      MIT
 */

namespace chillerlan\TinyCurlTest;

use chillerlan\TinyCurl\RequestTrait;
use PHPUnit\Framework\TestCase;

class RequestTraitTest extends TestCase{
	use RequestTrait;

	protected function setUp(){
		$this->setRequestCA(__DIR__.'/test-cacert.pem');
	}

	public function testFetchCoverage(){
		$url = 'https://httpbin.org/get';
		$response = $this->fetch($url);

		$this->assertSame($url, $response->json->url);
	}
}
