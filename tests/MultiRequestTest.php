<?php
/**
 *
 * @filesource   MultiRequestTest.php
 * @created      16.02.2016
 * @package      chillerlan\TinyCurlTest
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2016 Smiley
 * @license      MIT
 */

namespace chillerlan\TinyCurlTest;

use chillerlan\TinyCurl\MultiRequest;
use chillerlan\TinyCurl\MultiRequestOptions;

class MultiRequestTest extends \PHPUnit_Framework_TestCase{

	protected function getURLs(){

		$ids = [
			[1,2,6,11,15,23,24,56,57,58,59,60,61,62,63,64,68,69,70,71,72,73,74,75,76],
			[77,78,79,80,81,82,83,84,85,86,87,88,89,90,91,92,93,94,95,96,97,98,99,100,101],
		];

		$urls = [];

		foreach($ids as $chunk){
			foreach(['de', 'en', 'es', 'fr', 'zh'] as $lang){
				$urls[] = 'lang='.$lang.'&ids='.implode(',', $chunk);
			}
		}

		return $urls;
	}

	public function testInstance(){
		$options = new MultiRequestOptions;
		$options->handler     = MultiResponseHandlerTest::class;
		$options->ca_info     = __DIR__.'/test-cacert.pem';
		$options->base_url    = 'https://api.guildwars2.com/v2/items?';
		$options->window_size = 3;

		$request = new MultiRequest($options);
		$request->fetch($this->getURLs());

		foreach($request->getResponseData() as $response){

			$this->assertEquals(0, $response->errorcode);
			$this->assertEquals(200, $response->statuscode);
			$this->assertEquals($response->content_length_header, $response->content_length_body);
			$this->assertEquals('application/json; charset=utf-8', $response->content_type);
#			var_dump($response->ids);
		}

	}

	public function testInstanceWindowSize(){
		$options = new MultiRequestOptions;
		$options->handler     = MultiResponseHandlerTest::class;
		$options->ca_info     = __DIR__.'/test-cacert.pem';
		$options->base_url    = 'https://api.guildwars2.com/v2/items?';
		$options->window_size = 30;

		$request = new MultiRequest($options);
		$request->fetch($this->getURLs());
	}

}
