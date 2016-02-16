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

	public function testInstance(){

		$urls = [
			'lang=de&ids=1,2,6,11,15,23,24,56,57,58,59,60,61,62,63,64,68,69,70,71,72,73,74,75,76',
			'lang=en&ids=1,2,6,11,15,23,24,56,57,58,59,60,61,62,63,64,68,69,70,71,72,73,74,75,76',
			'lang=es&ids=1,2,6,11,15,23,24,56,57,58,59,60,61,62,63,64,68,69,70,71,72,73,74,75,76',
			'lang=fr&ids=1,2,6,11,15,23,24,56,57,58,59,60,61,62,63,64,68,69,70,71,72,73,74,75,76',
			'lang=zh&ids=1,2,6,11,15,23,24,56,57,58,59,60,61,62,63,64,68,69,70,71,72,73,74,75,76',
			'lang=de&ids=77,78,79,80,81,82,83,84,85,86,87,88,89,90,91,92,93,94,95,96,97,98,99,100,101',
			'lang=en&ids=77,78,79,80,81,82,83,84,85,86,87,88,89,90,91,92,93,94,95,96,97,98,99,100,101',
			'lang=es&ids=77,78,79,80,81,82,83,84,85,86,87,88,89,90,91,92,93,94,95,96,97,98,99,100,101',
			'lang=fr&ids=77,78,79,80,81,82,83,84,85,86,87,88,89,90,91,92,93,94,95,96,97,98,99,100,101',
			'lang=zh&ids=77,78,79,80,81,82,83,84,85,86,87,88,89,90,91,92,93,94,95,96,97,98,99,100,101',
		];

		$options = new MultiRequestOptions;
		$options->ca_info = __DIR__.'/test-cacert.pem';
		$options->base_url = 'https://api.guildwars2.com/v2/items?';

		(new MultiRequest(new MultiResponseHandlerTest, $options))->fetch($urls);
	}


}
