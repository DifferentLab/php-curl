<?php
/**
 *
 * @filesource   ItemMultiResponseHandler.php
 * @created      16.02.2016
 * @package      Example\GW2API
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2016 Smiley
 * @license      MIT
 */

namespace Example\GW2API;

use chillerlan\TinyCurl\MultiRequest;
use chillerlan\TinyCurl\MultiRequestOptions;
use chillerlan\TinyCurl\Request;
use chillerlan\TinyCurl\Response\MultiResponseHandlerInterface;
use chillerlan\TinyCurl\Response\ResponseInterface;
use chillerlan\Framework\Core\Traits\DatabaseTrait;
use chillerlan\Framework\Database\DBOptions;
use chillerlan\Framework\Database\Drivers\MySQLi\MySQLiDriver;
use Dotenv\Dotenv;
use stdClass;

/**
 * Class ItemMultiResponseHandler
 */
class ItemMultiResponseHandler implements MultiResponseHandlerInterface{
	use DatabaseTrait;

	/**
	 * class options
	 * play around with chunksize and concurrent requests to get best performance results
	 */
	const CONCURRENT    = 5;
	const CHUNK_SIZE    = 100;
	const API_LANGUAGES = ['de', 'en', 'es', 'fr', 'zh'];

	/**
	 * @var \chillerlan\TinyCurl\MultiRequest
	 */
	protected $multiRequest;

	/**
	 * @var \chillerlan\Framework\Database\Drivers\MySQLi\MySQLiDriver
	 */
	protected $mySQLiDriver;

	/**
	 * @var \mysqli
	 */
	protected $mysqli;

	/**
	 * @var array
	 */
	protected $urls = [];

	/**
	 * MultiResponseHandlerTest constructor.
	 *
	 * @param \chillerlan\TinyCurl\MultiRequest $multiRequest
	 */
	public function __construct(MultiRequest $multiRequest = null){
		$this->multiRequest = $multiRequest;

		(new Dotenv(__DIR__.'/../../config'))->load();

		$dbOptions = new DBOptions([
			'host'     => getenv('DB_MYSQLI_HOST'),
			'port'     => getenv('DB_MYSQLI_PORT'),
			'database' => getenv('DB_MYSQLI_DATABASE'),
			'username' => getenv('DB_MYSQLI_USERNAME'),
			'password' => getenv('DB_MYSQLI_PASSWORD'),
		]);

		$this->mySQLiDriver = $this->dbconnect(MySQLiDriver::class, $dbOptions);
		$this->mysqli = $this->mySQLiDriver->getDBResource();
	}

	/**
	 * The response handler.
	 *
	 * This method will be called within a loop in MultiRequest::getResponse().
	 * You can either build your class around this MultiResponseHandlerInterface to process
	 * the response during runtime or return the response data to the running
	 * MultiRequest instance via addResponse() and receive the data by calling getResponseData().
	 *
	 * You can either run this method void or return an URL as a replacement for a failed request,
	 * which then will be re-added to the running queue.
	 * However, the return value will not be checked, so make sure you return valid URLs. ;)
	 *
	 * @param \chillerlan\TinyCurl\Response\ResponseInterface $response
	 *
	 * @return bool|string $url
	 */
	public function handleResponse(ResponseInterface $response){
		$info = $response->info;

		// get the current request params
		parse_str(parse_url($info->url, PHP_URL_QUERY), $params);

		if(in_array($info->http_code, [200, 206], true)){
			// there be dragons.

			foreach($response->json as $item){
				echo $item->id.' - '.$item->name.PHP_EOL;
			}

		}
		else{
			// add the failed response to retry later @todo
			return null;
		}

		// not adding a response if everything was fine ('s ok, PhpStorm...)
		return false;
	}

	/**
	 *
	 */
	public function init(){
		$options = new MultiRequestOptions;
		$options->ca_info     = __DIR__.'/test-cacert.pem';
		$options->base_url    = 'https://api.guildwars2.com/v2/items?';
		$options->window_size = self::CONCURRENT;

		$this->getURLs();
		$request = new MultiRequest($options);
		// solving the hen-egg problem, feed the hen with the egg!
		$request->setHandler($this);
		$request->fetch($this->urls);
	}

	/**
	 * @throws \chillerlan\TinyCurl\RequestException
	 */
	protected function getURLs(){
		$response = (new Request)->fetch('https://api.guildwars2.com/v2/items');

		if($response->info->http_code !== 200){
			exit('failed to get /v2/items');
		}

		foreach(array_chunk($response->json, self::CHUNK_SIZE) as $chunk){
			foreach(self::API_LANGUAGES as $lang){
				$this->urls[] = http_build_query(['lang' => $lang, 'ids' => implode(',', $chunk)]);
			}
		}

	}

}
