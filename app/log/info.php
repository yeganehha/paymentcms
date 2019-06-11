<?php

lang::addToLangfile('log');
return [
	'info' => [
		'name' => rlang('logPageApp'),
		'description' => rlang('logPageAppDescription'),
		'version' => '1.0.0.0',
		'author' => 'Erfan Ebrahimi',
		'support' => 'http://erfanebrahimi.ir',
	] ,
	'db' => [
		'log' => [
			'fields' => [
				'logId' => 'INT NOT NULL AUTO_INCREMENT',
				'userId' => 'INT NULL DEFAULT NULL',
				'app' => 'VARCHAR(65) NOT NULL',
				'app_provider' => 'VARCHAR(65) NULL DEFAULT NULL',
				'controller' => 'VARCHAR(65) NOT NULL',
				'method' => 'VARCHAR(65) NOT NULL',
				'log_name' => 'VARCHAR(65) NOT NULL',
				'description' => 'TEXT NULL DEFAULT NULL',
				'previous_page' => 'TEXT NULL DEFAULT NULL',
				'current_url' => 'TEXT NOT NULL',
				'ip' => 'VARCHAR(32) NULL DEFAULT NULL',
				'platform' => 'VARCHAR(65) NULL DEFAULT NULL',
				'browser' => 'VARCHAR(65) NULL DEFAULT NULL',
				'activity_time' => 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP',
			],
			'KEY' => [
				'logId'
			],
			'PRIMARY KEY' => [
				'logId'
			],
			'REFERENCES' => [
				'userId' => [ 'table' => 'user' , 'column' => 'userId' , 'on_delete' => 'CASCADE' , 'on_update' => 'CASCADE' ]
			]
		]
	]
];