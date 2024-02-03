<?php

namespace Dev4Press\Plugin\CoreActivity\Basic;

use Dev4Press\v47\Core\Plugins\InstallDB as BaseInstallDB;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class InstallDB extends BaseInstallDB {
	protected $version = 20231024;
	protected $prefix = 'coreactivity';
	protected $plugin = 'coreactivity';
	protected $tables = array(
		'events'  => array(
			'name'    => 'events',
			'columns' => 6,
			'scope'   => 'network',
			'data'    => "event_id bigint(20) unsigned NOT NULL AUTO_INCREMENT, \n" .
			             "category varchar(32) NOT NULL DEFAULT 'wordpress', \n" .
			             "component varchar(128) NOT NULL DEFAULT '', \n" .
			             "event varchar(128) NOT NULL DEFAULT '', \n" .
			             "status varchar(32) NOT NULL DEFAULT 'active', \n" .
			             "rules text NULL DEFAULT NULL, \n" .
			             "PRIMARY KEY  (event_id), \n" .
			             "UNIQUE KEY category_component_event (category, component, event), \n" .
			             "KEY category (category), \n" .
			             "KEY component (component), \n" .
			             "KEY event (event), \n" .
			             "KEY status (status)",
		),
		'logs'    => array(
			'name'    => 'logs',
			'columns' => 14,
			'scope'   => 'network',
			'data'    => "log_id bigint(20) unsigned NOT NULL AUTO_INCREMENT, \n" .
			             "blog_id bigint(20) unsigned NOT NULL DEFAULT '0', \n" .
			             "event_id bigint(20) unsigned NOT NULL DEFAULT '0', \n" .
			             "user_id bigint(20) unsigned NOT NULL DEFAULT '0', \n" .
			             "logged datetime NULL DEFAULT NULL, \n" .
			             "ip varchar(64) NULL DEFAULT NULL, \n" .
			             "context varchar(16) NOT NULL DEFAULT '' COMMENT 'REST, CRON, AJAX, CLI', \n" .
			             "method varchar(16) NOT NULL DEFAULT '' COMMENT 'POST, GET, PUT, DELETE ...', \n" .
			             "protocol varchar(16) NOT NULL DEFAULT '' COMMENT 'HTTP/1.0, HTTP/1.1 ...', \n" .
			             "request text NULL DEFAULT NULL, \n" .
			             "object_type varchar(64) NULL DEFAULT NULL, \n" .
			             "object_id bigint(20) unsigned NULL DEFAULT NULL, \n" .
			             "object_name varchar(255) NULL DEFAULT NULL, \n" .
			             "country_code char(2) NULL DEFAULT NULL, \n" .
			             "PRIMARY KEY  (log_id), \n" .
			             "KEY blog_id (blog_id), \n" .
			             "KEY event_id (event_id), \n" .
			             "KEY user_id (user_id), \n" .
			             "KEY logged (logged), \n" .
			             "KEY ip (ip), \n" .
			             "KEY context (context), \n" .
			             "KEY method (method), \n" .
			             "KEY object_type (object_type), \n" .
			             "KEY object_id (object_id), \n" .
			             "KEY object_name (object_name), \n" .
			             "KEY country_code (country_code)",
		),
		'logmeta' => array(
			'name'    => 'logmeta',
			'columns' => 4,
			'scope'   => 'network',
			'data'    => "meta_id bigint(20) unsigned NOT NULL AUTO_INCREMENT, \n" .
			             "log_id bigint(20) unsigned NOT NULL DEFAULT '0', \n" .
			             "meta_key varchar(128) NOT NULL DEFAULT '', \n" .
			             "meta_value longtext NULL DEFAULT NULL, \n" .
			             "PRIMARY KEY  (meta_id), \n" .
			             "KEY log_id (log_id), \n" .
			             "KEY meta_key (meta_key)",
		),
	);
}
