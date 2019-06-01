<?php
return [
	'info' => [
		'name' => 'client system',
		'description' => 'cs area for payment cms',
		'version' => '1.0.0.0',
		'author' => 'erfan ebrahimi',
		'support' => 'http://erfanebrahimi.ir',
	],
	'db' => [
		'user_group' => [
			'fields' => [
				'user_groupId' => 'INT(11) NOT NULL AUTO_INCREMENT',
				'name' => "varchar(65) COLLATE utf8_persian_ci NOT NULL",
				'loginRequired' => "tinyint(1) NOT NULL DEFAULT '0'",
			],
			'KEY' => [
			],
			'PRIMARY KEY' => [
				'user_groupId'
			],
			'REFERENCES' => [
			]
		],
		'user' => [
			'fields' => [
				'userId' => 'INT(11) NOT NULL AUTO_INCREMENT',
				'fname' => "varchar(255) COLLATE utf8_persian_ci DEFAULT NULL",
				'lname' => "varchar(255) COLLATE utf8_persian_ci DEFAULT NULL",
				'email' => "varchar(255) COLLATE utf8_persian_ci DEFAULT NULL",
				'phone' => "varchar(255) COLLATE utf8_persian_ci DEFAULT NULL",
				'register_time' => "datetime NOT NULL DEFAULT CURRENT_TIMESTAMP",
				'block' => "tinyint(1) NOT NULL DEFAULT '0'",
				'admin_note' => "text COLLATE utf8_persian_ci",
				'password' => "varchar(65) COLLATE utf8_persian_ci NOT NULL",
				'user_group_id' => "int(11) NOT NULL"
			],
			'KEY' => [
				'user_group_id',
			],
			'PRIMARY KEY' => [
				'userId'
			],
			'REFERENCES' => [
				'user_group_id' => [ 'table' => 'user_group' , 'column' => 'user_groupId' , 'on_delete' => 'CASCADE' , 'on_update' => 'CASCADE' ]
			]
		],
		'user_group_permission' => [
			'fields' => [
				'user_groupId' => 'INT(11) DEFAULT NULL',
				'accessPage' => "varchar(65) COLLATE utf8_persian_ci NOT NULL",
			],
			'KEY' => [
				'user_groupId'
			],
			'PRIMARY KEY' => [
			],
			'REFERENCES' => [
				'user_groupId' => [ 'table' => 'user_group' , 'column' => 'user_groupId' , 'on_delete' => 'CASCADE' , 'on_update' => 'CASCADE' ]
			]
		]
	]
];