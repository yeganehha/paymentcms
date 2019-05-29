<?php

lang::addToLangfile('landing');
return [
	'info' => [
		'name' => rlang('landingPageApp'),
		'description' => rlang('landingPageAppDescription'),
		'version' => '1.0.0.0',
		'author' => 'Erfan Ebrahimi',
		'support' => 'http://erfanebrahimi.ir',
	] ,
	'db' => [
		'landingPage' => [
			'fields' => [
				'landingPageId' => 'INT NOT NULL AUTO_INCREMENT',
				'name' => 'VARCHAR(65) NOT NULL',
				'metaDescription' => 'TEXT NULL DEFAULT NULL',
				'template' => 'TEXT NULL DEFAULT NULL',
				'templateName' => "VARCHAR(65) NULL DEFAULT 'default'",
				'useAsDefault' => "BOOLEAN NOT NULL DEFAULT FALSE",
			],
			'KEY' => [
			],
			'PRIMARY KEY' => [
				'landingPageId'
			],
			'REFERENCES' => [
			]
		]
	]
];