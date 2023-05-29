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
	public $_log_columns = array(
		'blog_id'     => '%d',
		'event_id'    => '%d',
		'user_id'     => '%d',
		'logged'      => '%s',
		'ip'          => '%s',
		'context'     => '%s',
		'method'      => '%s',
		'protocol'    => '%s',
		'request'     => '%s',
		'object_type' => '%s',
		'object_id'   => '%d',
		'object_name' => '%s'
	);

	public function get_all_registered_events() {
		$sql = "SELECT *, '' as scope, '' as label, '' as object_type, '' as loaded FROM " . $this->events . " ORDER BY `component`, `event`";

		return $this->get_results( $sql );
	}

	public function log_event( array $data = array(), array $meta = array() ) : int {
		$input  = array();
		$format = array();

		foreach ( $this->_log_columns as $column => $replace ) {
			if ( isset( $data[ $column ] ) ) {
				$input[ $column ] = $data[ $column ];
				$format[]         = $replace;
			}
		}

		$result = $this->insert( $this->logs, $input, $format );

		if ( $result !== false ) {
			$id = $this->get_insert_id();

			$this->insert_meta_data( $this->logmeta, 'log_id', $id, $meta );

			return $id;
		}

		return 0;
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
