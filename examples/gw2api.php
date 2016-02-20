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

use Example\GW2API\ItemMultiResponseHandler;

require_once '../vendor/autoload.php';
require_once 'functions.php';

if(!is_cli()){
	throw new \Exception('no way, buddy.');
}

date_default_timezone_set('UTC');
mb_internal_encoding('UTF-8');

$gw2items = new ItemMultiResponseHandler;
$gw2items->init();

