<?php

namespace Dev4Press\Plugin\CoreActivity\Basic;

use Dev4Press\v42\Core\Plugins\DB as BaseDB;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @property string $events
 * @property string $logs
 * @property string $logmeta
 */
class DB extends BaseDB {
	protected $plugin_name = 'coreactivity';
	public $_prefix = 'coreactivity';
	public $_tables = array( 'events', 'logs', 'logmeta' );
	public $_network_tables = array( 'events', 'logs', 'logmeta' );
	public $_metas = array( 'log' => 'log_id' );
}
