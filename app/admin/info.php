<?php
return [
	'info' => [
		'name' => 'Admin',
		'description' => 'Admin area for payment cms',
		'version' => '1.0.0.0',
		'author' => 'erfan ebrahimi',
		'support' => 'http://erfanebrahimi.ir',
	] ,
	'db' => [
		'test1' => [
			'fields' => [
				'testId' => 'INT NOT NULL AUTO_INCREMENT',
				'factorId' => 'INT NOT NULL',
				'name' => 'TEXT NULL DEFAULT NULL',
				'chart' => "VARCHAR(65) NULL DEFAULT 'test'",
			],
			'KEY' => [
			],
			'PRIMARY KEY' => [
				'testId'
			],
			'REFERENCES' => [
				'factorId' => [ 'table' => 'invoice' , 'column' => 'invoiceId' , 'on_delete' => 'RESTRICT' , 'on_update' => 'RESTRICT' ]
			]
		]
	]
];