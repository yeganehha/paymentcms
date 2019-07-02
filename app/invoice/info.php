<?php

lang::addToLangfile('landing');
return [
	'info' => [
		'name' => rlang('invoiceApp'),
		'description' => rlang('invoiceAppDescription'),
		'version' => '1.0.0.0',
		'author' => 'erfan ebrahimi',
		'support' => 'http://erfanebrahimi.ir',
	],
	'db' => [
		'invoice' => [
			'fields' => [
				'invoiceId' => 'INT(11) NOT NULL AUTO_INCREMENT',
				'userId' => 'int(11) DEFAULT NULL',
				'createdDate' => 'datetime DEFAULT NULL',
				'dueDate' => 'datetime DEFAULT NULL',
				'paidDate' => 'datetime DEFAULT NULL',
				'status' => "enum('pending','canceled','refused','failed','paid') COLLATE utf8_persian_ci DEFAULT NULL",
				'module' => 'varchar(255) COLLATE utf8_persian_ci DEFAULT NULL',
				'price' => 'decimal(10,2) DEFAULT NULL',
				'requestAction' => 'varchar(255) COLLATE utf8_persian_ci DEFAULT NULL',
				'backUri' => 'varchar(255) COLLATE utf8_persian_ci DEFAULT NULL',
				'createdIp' => 'varchar(255) COLLATE utf8_persian_ci DEFAULT NULL',
				'apiId' => 'int(11) DEFAULT NULL'
			],
			'KEY' => [
				'userId',
				'apiId'
			],
			'PRIMARY KEY' => [
				'invoiceId'
			],
			'REFERENCES' => [
				'userId' => [ 'table' => 'user' , 'column' => 'userId' , 'on_delete' => 'CASCADE' , 'on_update' => 'CASCADE' ],
				'apiId' => [ 'table' => 'api' , 'column' => 'apiId' , 'on_delete' => 'CASCADE' , 'on_update' => 'CASCADE' ],
			]
		],
		'service' => [
			'fields' => [
				'serviceId'=> 'INT(11) NOT NULL AUTO_INCREMENT',
				'link' => 'varchar(255) COLLATE utf8_persian_ci DEFAULT NULL',
				'price' => 'varchar(10) COLLATE utf8_persian_ci DEFAULT NULL',
				'description' => 'varchar(255) COLLATE utf8_persian_ci DEFAULT NULL',
				'name' => 'varchar(255) COLLATE utf8_persian_ci DEFAULT NULL',
				'status' => "tinyint(1) DEFAULT '1'",
				'lastNameStatus' => "enum('required','visible','invisible') COLLATE utf8_persian_ci DEFAULT 'visible'",
				'firstNameStatus' => "enum('required','visible','invisible') COLLATE utf8_persian_ci DEFAULT 'visible'",
				'emailStatus' => "enum('required','visible','invisible') COLLATE utf8_persian_ci DEFAULT 'visible'",
				'phoneStatus' => "enum('required','visible','invisible') COLLATE utf8_persian_ci DEFAULT 'required'",
			],
			'KEY' => [
			],
			'PRIMARY KEY' => [
				'serviceId'
			],
			'REFERENCES' => [
			]
		],
		'items' => [
			'fields' => [
				'itemId' => 'int(11) NOT NULL AUTO_INCREMENT',
				'invoiceId' => 'int(11) NOT NULL',
				'price' => 'decimal(10,2) DEFAULT NULL',
				'description' => 'varchar(255) COLLATE utf8_persian_ci DEFAULT NULL',
				'time' => 'datetime DEFAULT NULL',
				'serviceId' => 'int(11) DEFAULT NULL'
			],
			'KEY' => [
				'serviceId'
			],
			'PRIMARY KEY' => [
				'itemId'
			],
			'REFERENCES' => [
				'serviceId' => [ 'table' => 'service' , 'column' => 'serviceId' , 'on_delete' => 'CASCADE' , 'on_update' => 'CASCADE' ],
			]
		],
		'transactions' => [
			'fields' => [
				'transactionId' => 'int(11) NOT NULL AUTO_INCREMENT',
				'invoiceId' => 'int(11) DEFAULT NULL',
				'price' => 'decimal(10,2) DEFAULT NULL',
				'time' => 'datetime DEFAULT NULL',
				'ip' => 'varchar(36) COLLATE utf8_persian_ci DEFAULT NULL',
				'module' => 'varchar(255) COLLATE utf8_persian_ci DEFAULT NULL',
				'status' => "enum('pending','feiled','seucced','refused','canceled') COLLATE utf8_persian_ci DEFAULT NULL",
				'transactionCodeOne' => 'varchar(255) COLLATE utf8_persian_ci DEFAULT NULL',
				'transactionCodeTwo' => 'varchar(255) COLLATE utf8_persian_ci DEFAULT NULL',
				'description' => 'varchar(255) COLLATE utf8_persian_ci DEFAULT NULL'
			],
			'KEY' => [
				'invoiceId'
			],
			'PRIMARY KEY' => [
				'transactionId'
			],
			'REFERENCES' => [
				'invoiceId' => [ 'table' => 'invoice' , 'column' => 'invoiceId' , 'on_delete' => 'CASCADE' , 'on_update' => 'CASCADE' ],
			]
		]
	]
];