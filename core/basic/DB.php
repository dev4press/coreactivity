<?php

namespace Dev4Press\Plugin\CoreActivity\Basic;

use Dev4Press\v42\Core\Plugins\DB as BaseDB;
use Dev4Press\v42\Core\Quick\Sanitize;

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
		$sql = "SELECT * FROM " . $this->events . " ORDER BY `component`, `event`";

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

			$this->insert_meta_data( $this->logmeta, 'log_id', $id, $meta, true );

			return $id;
		}

		return 0;
	}

	public function add_new_event( string $category, string $component, string $event, string $status = 'active', array $rules = array() ) : int {
		$data = array(
			'category'  => $category,
			'component' => $component,
			'event'     => $event,
			'status'    => $status
		);

		if ( ! empty( $rules ) ) {
			$data[ 'rules' ] = json_encode( $rules );
		}

		$result = $this->insert( $this->events, $data );

		return $result == 1 ? absint( $this->get_insert_id() ) : 0;
	}

	public function change_event_status( int $event_id, string $new_status ) {
		$this->update( $this->events, array( 'status' => $new_status ), array( 'event_id' => $event_id ), array( '%s' ), array( '%d' ) );
	}

	public function count_logged_events() : array {
		$sql = "SELECT `event_id`, COUNT(*) as `logs` FROM " . $this->logs . " GROUP BY `event_id`";
		$raw = $this->get_results( $sql );

		return empty( $raw ) ? array() : $this->pluck( $raw, 'logs', 'event_id' );
	}

	public function statistics_components_log( int $days = 30 ) : array {
		$sql = $this->prepare( "SELECT e.`component`, COUNT(l.log_id) AS logs 
				FROM $this->events e LEFT JOIN $this->logs l ON e.`event_id` = l.`event_id`
				WHERE l.`logged` IS NULL OR l.`logged` > DATE_SUB(NOW(), INTERVAL %d DAY)
				GROUP BY e.`component` ORDER BY e.`component`", $days );
		$raw = $this->get_results( $sql );

		return empty( $raw ) ? array() : $this->pluck( $raw, 'logs', 'component' );
	}

	public function get_last_log_id() : int {
		$sql = "SELECT MAX(`log_id`) FROM " . $this->logs;

		return Sanitize::absint( $this->get_var( $sql ) );
	}

	public function remove_log_meta_orphans() {
		$sql = "DELETE m FROM $this->logmeta m LEFT JOIN $this->logs l ON l.log_id = m.log_id WHERE l.log_id IS NULL";

		return $this->query( $sql );
	}
}
