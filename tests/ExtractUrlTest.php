<?php
/**
 * @filesource   ExtractUrlTest.php
 * @created      16.02.2016
 * @package      chillerlan\TinyCurlTest
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2016 Smiley
 * @license      MIT
 */

namespace chillerlan\TinyCurlTest;

use chillerlan\TinyCurl\{Request, RequestOptions};

class ExtractUrlTest extends \PHPUnit_Framework_TestCase{

	/**
	 * @var \chillerlan\TinyCurl\Request
	 */
	protected $request;

	protected function setUp(){
		$options = new RequestOptions;
		$options->ca_info = __DIR__.'/test-cacert.pem';
		$this->request = new Request($options);
	}

	public function shortURLDataProvider(){
		return [
			[
				[
					'https://t.co/ZSS6nVOcVp', // i wonder how long twitter will store this URL since the test tweet has been deleted. update: likely forever.
					'http://bit.ly/1oesmr8',
					'http://tinyurl.com/jvc5y98',
					'https://api.guildwars2.com/v2/build',
				],
			],
		];
	}

	/**
	 * @dataProvider shortURLDataProvider
	 */
	public function testExtractShortUrl($expected){
		$this->assertEquals($expected, $this->request->extractShortUrl($expected[0]));
	}

}
