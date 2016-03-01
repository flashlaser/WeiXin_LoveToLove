<?php

/**
 * 数据库地址
 */
return array(
	'PdoMysql' => array(
		'eye2eye' => array(
			//主库
			'write' => array(
				'host' => SAE_MYSQL_HOST_M,
				'port' => SAE_MYSQL_PORT,
				'name' => SAE_MYSQL_DB,
				'user' => SAE_MYSQL_USER,
				'pass' => SAE_MYSQL_PASS,
			),
			//从库
			'read' => array(
				'host' => SAE_MYSQL_HOST_S,
				'port' => SAE_MYSQL_PORT,
				'name' => SAE_MYSQL_DB,
				'user' => SAE_MYSQL_USER,
				'pass' => SAE_MYSQL_PASS,
			),
		),
	),
);
