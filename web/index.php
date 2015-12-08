<?php

error_reporting(E_ALL ^ (E_NOTICE | E_STRICT));
ini_set('display_errors',true);

require_once __DIR__ . '/../vendor/autoload.php';

use Tipsy\Tipsy;

Tipsy::router()
	->post('hook', function($Params, $Request) {
		print_r($Request);
	})
	->otherwise(function() {
		http_response_code(404);
	});

Tipsy::run();
