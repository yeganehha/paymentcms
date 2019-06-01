<?php
return [
	'info' => [
		'name' => 'Api',
		'description' => 'Api area for payment cms',
		'version' => '1.0.0.0',
		'author' => 'erfan ebrahimi',
		'support' => 'http://erfanebrahimi.ir',
	] ,
	'db' => [
		'api' => [
			'fields' => [
				'apiId' => 'INT(11) NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(255) COLLATE utf8_persian_ci DEFAULT NULL',
				'active' => "tinyint(1) DEFAULT '1'",
				'domain' => "varchar(60) COLLATE utf8_persian_ci DEFAULT NULL",
				'allowIp' => "varchar(255) COLLATE utf8_persian_ci DEFAULT NULL",
			],
			'KEY' => [
			],
			'PRIMARY KEY' => [
				'apiId'
			],
			'REFERENCES' => [
			]
		]
	]
];