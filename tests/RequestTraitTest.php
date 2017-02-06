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

class RequestTraitTest extends \PHPUnit_Framework_TestCase{
	use RequestTrait;

	protected function setUp(){
		$this->setRequestCA(__DIR__.'/test-cacert.pem');
	}

	public function testFetchCoverage(){
		$this->fetch('https://httpbin.org/get');
	}
}
