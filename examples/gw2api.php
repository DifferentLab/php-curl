<?php
/**
 *
 * @filesource   gw2api.php
 * @created      17.02.2016
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2016 Smiley
 * @license      MIT
 */

namespace Example;

use chillerlan\TinyCurl\MultiRequest;
use chillerlan\TinyCurl\MultiRequestOptions;
use chillerlan\TinyCurl\Request;
use Example\GW2API\ItemMultiResponseHandler;

require_once '../vendor/autoload.php';

$response = (new Request)->fetch('https://api.guildwars2.com/v2/items');

if($response->info->http_code !== 200){
	exit('failed to get /v2/items');
}

$urls = [];
foreach(array_chunk($response->json, 100) as $chunk){
	foreach(['de', 'en', 'es', 'fr', 'zh'] as $lang){
		$urls[] = http_build_query(['lang' => $lang, 'ids' => implode(',', $chunk)]);
	}
}


$options = new MultiRequestOptions;
$options->handler     = ItemMultiResponseHandler::class;
$options->ca_info     = __DIR__.'/test-cacert.pem';
$options->base_url    = 'https://api.guildwars2.com/v2/items?';
$options->window_size = 5;

$request = (new MultiRequest($options))->fetch($urls);

var_dump($request->getResponseData());
