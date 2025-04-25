<?php

return [
	'driver' => $_ENV['DB_DRIVER'],
	'host' => $_ENV['DB_HOST'],
	'port' => $_ENV['DB_PORT'],
	'username' => $_ENV['DB_USERNAME'],
	'password' => $_ENV['DB_PASSWORD'],
	'database' => $_ENV['DB_NAME'],
	'charset' => 'utf8mb4',
];