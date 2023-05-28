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

	public function get_all_registered_events() {
		$sql = "SELECT *, '' as scope, '' as label, '' as object_type, '' as loaded FROM " . $this->events . " ORDER BY `component`, `event`";

		return $this->get_results( $sql );
	}

	public function add_new_event( string $component, string $event, string $status = 'active', array $rules = array() ) : int {
		$data = array(
			'component' => $component,
			'event'     => $event,
			'status'    => $status
		);

		if ( ! empty( $rules ) ) {
			$data[ 'rules' ] = json_encode( $rules );
		}

		$result = $this->insert( $this->events, $data );

		return $result == 1 ? $this->get_insert_id() : 0;
	}
}
