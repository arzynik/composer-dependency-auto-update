<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Tipsy\Tipsy;

Tipsy::router()
	->post('hook', function($Params, $Request) {
		$package = $_ENV['PACKAGE_NAME'] ? $_ENV['PACKAGE_NAME'] : ($this->tipsy()->config()['update']['package'] ? $this->tipsy()->config()['update']['package'] : $Request->repository->full_name);
		$email = $_ENV['GITHUB_EMAIL'] ? $_ENV['GITHUB_EMAIL'] : $this->tipsy()->config()['update']['email'];
		$name = $_ENV['GITHUB_NAME'] ? $_ENV['GITHUB_NAME'] : $this->tipsy()->config()['update']['name'];
		$secret = $_ENV['WEBHOOK_SECRET'] ? $_ENV['WEBHOOK_SECRET'] : $this->tipsy()->config()['update']['secret'];

		if (!$package) {
			print_r($Request);
			echo "No PACKAGE_NAME(".$Request->repository->full_name.").\n";
			$error = true;
		}
		if (!$email) {
			echo "No GITHUB_EMAIL.\n";
			$error = true;
		}
		if (!$name) {
			echo "No GITHUB_NAME.\n";
			$error = true;
		}
		if (!$secret) {
			echo "No WEBHOOK_SECRET.\n";
			$error = true;
		}
		if (sha1($secret) != str_replace('sha1=','',$Request->headers()['x-hub-signature'])) {
			echo "Invalid WEBHOOK_SECRET.\n";
			$error = true;
		}

		if ($error) {
			exit;
		}

		$cmds[] = 'rm -Rf /tmp/repos';
		$cmds[] = 'mkdir /tmp/repos';
		$cmds[] = 'git config --global user.email "'.$email.'"';
		$cmds[] = 'git config --global user.name "'.$name.'"';
		$x = 1;

		foreach ($_ENV as $k => $v) {
			if (preg_match('/^GITHUB_REPO[0-9]+$/', $k)) {
				$cmds[] = 'git clone '.$v.' /tmp/repos/'.$x;
				$dir = 'cd /tmp/repos/'.$x.' && ';
				$cmds[] = $dir.'composer update '.$package.' --optimize-autoloader';
				$cmds[] = $dir.'git status -s';
				$cmds[] = $dir.'git add -A';
				$cmds[] = $dir.'git commit -m "Automatic dependency update for '.$package.'"';
				$cmds[] = $dir.'git push origin master';
			}
			$x++;
		}

		$cmds[] = 'rm -Rf /tmp/repos';

		foreach ($cmds as $cmd) {
			echo shell_exec($cmd.' 2>&1')."\n";
		}
	})
	->otherwise(function() {
		http_response_code(404);
	});

Tipsy::run();
