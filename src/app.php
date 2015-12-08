<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Tipsy\Tipsy;

Tipsy::router()
	->post('hook', function($Params, $Request) {
		$package = $_ENV['PACKAGE_NAME'] ? $_ENV['PACKAGE_NAME'] : ($this->tipsy()->config()['update']['package'] ? $this->tipsy()->config()['update']['package'] : $Request->repository->full_name);

		$cmds[] = 'mkdir /tmp/repos';
		$x = 1;
		foreach ($_ENV as $k => $v) {
			if (preg_match('/^GITHUB_REPO[0-9]+/$', $k)) {
				$cmds[] = 'git clone '.$v.' /tmp/repos/'.$x;
				$cmds[] = 'cd /tmp/repos/'.$x;
				$cmds[] = 'composer update '.$package.' --optimize-autoloader';
				$cmds[] = 'git add -A';
				$cmds[] = 'git commit -m "Automatic dependency update for '.$package.'"';
			}
			$x++;
		}

		foreach ($cmds as $cmd) {
			echo shell_exec($cmd.' 2>&1')."\n";
		}
	})
	->otherwise(function() {
		http_response_code(404);
	});

Tipsy::run();
