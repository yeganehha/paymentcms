<?php

lang::addToLangfile('eForm');
return [
	'info' => [
		'name' => rlang('eFormApp'),
		'description' => rlang('eFormAppDescription'),
		'version' => '1.0.0.0',
		'author' => 'Erfan Ebrahimi',
		'support' => 'http://erfanebrahimi.ir',
	] ,
	'db' => [
		'eForm' => [
			'fields' => [
				'formId' => 'INT NOT NULL AUTO_INCREMENT',
				'name' => 'VARCHAR(65) NOT NULL',
				'description' => 'TEXT NULL DEFAULT NULL',
				'lastNote' => 'TEXT NULL DEFAULT NULL',
				'templateName' => "VARCHAR(65) NULL DEFAULT 'defaults'",
				'oneTime' => "BOOLEAN NOT NULL DEFAULT FALSE",
				'access' => "TEXT NULL DEFAULT NULL",
				'published' => "BOOLEAN NOT NULL DEFAULT FALSE",
				'public' => "BOOLEAN NOT NULL DEFAULT FALSE",
				'showHistory' => "BOOLEAN NOT NULL DEFAULT FALSE",
				'password' => "VARCHAR(40) NOT NULL",
			],
			'KEY' => [
			],
			'PRIMARY KEY' => [
				'formId'
			],
			'REFERENCES' => [
			]
		],
		'eFormFilled' => [
			'fields' => [
				'fillId' => 'INT NOT NULL AUTO_INCREMENT',
				'formId' => 'INT NOT NULL',
				'userId' => 'INT NOT NULL',
				'adminNote' => 'TEXT NULL DEFAULT NULL',
				'fillStart' => 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP',
				'fillEnd' => "DATETIME NULL DEFAULT NULL",
				'ip' => "VARCHAR(20) NULL DEFAULT NULL",
			],
			'KEY' => [
				'fillId',
				'formId',
				'userId'
			],
			'PRIMARY KEY' => [
				'fillId'
			],
			'REFERENCES' => [
				'formId' => [ 'table' => 'eform' , 'column' => 'formId' , 'on_delete' => 'CASCADE' , 'on_update' => 'CASCADE' ],
				'userId' => [ 'table' => 'user' , 'column' => 'userId' , 'on_delete' => 'CASCADE' , 'on_update' => 'CASCADE' ]
			]
		]
	]
];