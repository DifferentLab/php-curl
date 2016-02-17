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
	 * @var \chillerlan\TinyCurl\MultiRequest
	 */
	protected $request;

	/**
	 * @var \chillerlan\Framework\Database\Drivers\MySQLi\MySQLiDriver
	 */
	protected $mySQLiDriver;

	/**
	 * @var \mysqli
	 */
	protected $mysqli;

	/**
	 * MultiResponseHandlerTest constructor.
	 *
	 * @param \chillerlan\TinyCurl\MultiRequest $request
	 */
	public function __construct(MultiRequest $request){
		(new Dotenv(__DIR__.'/../../config'))->load();
		$this->request = $request;
		$dbOptions = new DBOptions([
			'host'     => getenv('DB_MYSQLI_HOST'),
			'port'     => getenv('DB_MYSQLI_PORT'),
			'database' => getenv('DB_MYSQLI_DATABASE'),
			'username' => getenv('DB_MYSQLI_USERNAME'),
			'password' => getenv('DB_MYSQLI_PASSWORD'),
		]);

		$this->mySQLiDriver = $this->dbconnect(MySQLiDriver::class, $dbOptions);
		$this->mysqli = $this->mySQLiDriver->getConnection();
	}

	/**
	 * @param \chillerlan\TinyCurl\Response\ResponseInterface $response
	 *
	 * @return void
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
			$return = new stdClass;
			$return->httpcode = $info->http_code;
			$return->url      = $info->url;

			$this->request->addResponse($info->http_code);
		}
	}

}
