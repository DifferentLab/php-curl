<?php
/**
 *
 * @filesource   ExtractUrlTest.php
 * @created      16.02.2016
 * @package      chillerlan\TinyCurlTest
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2016 Smiley
 * @license      MIT
 */

namespace chillerlan\TinyCurlTest;

use chillerlan\TinyCurl\Request;
use chillerlan\TinyCurl\RequestOptions;

class ExtractUrlTest extends \PHPUnit_Framework_TestCase{

	/**
	 * @var \chillerlan\TinyCurl\Request
	 */
	protected $requestWithCA;

	/**
	 * @var \chillerlan\TinyCurl\Request
	 */
	protected $requestNoCA;

	protected function setUp(){
		$co = [CURLOPT_FOLLOWLOCATION => false];

		$o1 = new RequestOptions;
		$o1->curl_options = $co;
		$o1->ca_info = __DIR__.'/test-cacert.pem';
		$this->requestWithCA = new Request($o1);

		$o2 = new RequestOptions;
		$o2->curl_options = $co;
		$this->requestNoCA = new Request($o2);
	}

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
/*			
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
#*/    
		];
	}

	/**
	 * @dataProvider shortURLDataProvider
	 */
	public function testExtractShortUrlWithCA($expected){
			$this->assertEquals($expected, $this->requestWithCA->extractShortUrl($expected[0]));
		}

	/**
	 * @dataProvider shortURLDataProvider
	 */
		public function testExtractShortUrlNoCA($expected){
			$this->assertEquals($expected, $this->requestNoCA->extractShortUrl($expected[0]));
		}

}
