<?php

namespace Dev4Press\Plugin\CoreActivity\Basic;

use Dev4Press\v43\Core\Plugins\DB as BaseDB;
use Dev4Press\v43\Core\Quick\Sanitize;

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

	public function get_statistics() : array {
		$this->analyze_table( $this->events );
		$this->analyze_table( $this->logs );
		$this->analyze_table( $this->logmeta );

		$prefix  = $this->base_prefix() . 'coreactivity_';
		$sql     = "SHOW TABLE STATUS FROM `" . DB_NAME . "` WHERE `Name` LIKE '" . $prefix . "%'";
		$data    = $this->get_results( $sql, ARRAY_A );
		$results = array(
			'tables' => array()
		);

		foreach ( $data as $row ) {
			$_table_name  = strtolower( $row[ 'Name' ] );
			$_actual_name = str_replace( $prefix, '', $_table_name );
			$_total       = absint( $row[ 'Data_length' ] ) + absint( $row[ 'Index_length' ] ) + absint( $row[ 'Data_free' ] );

			$results[ 'tables' ][ $_actual_name ] = array(
				'table'            => $_table_name,
				'engine'           => $row[ 'Engine' ],
				'total'            => $_total,
				'size'             => absint( $row[ 'Data_length' ] ),
				'free'             => absint( $row[ 'Data_free' ] ),
				'index'            => absint( $row[ 'Index_length' ] ),
				'rows'             => absint( $row[ 'Rows' ] ),
				'average_row_size' => absint( $row[ 'Avg_row_length' ] ),
				'auto_increment'   => $row[ 'Auto_increment' ],
				'created'          => $row[ 'Create_time' ],
				'updated'          => $row[ 'Update_time' ],
				'collation'        => $row[ 'Collation' ],
			);
		}

		$results[ 'size' ] = $results[ 'tables' ][ 'logs' ][ 'total' ] + $results[ 'tables' ][ 'logmeta' ][ 'total' ] + $results[ 'tables' ][ 'events' ][ 'total' ];

		$sql  = "SELECT DATE(MIN(`logged`)) as `oldest`, DATEDIFF(NOW(), MIN(`logged`)) as `range` FROM " . $this->logs;
		$data = $this->get_row( $sql, ARRAY_A );

		if ( ! empty( $data ) ) {
			$results[ 'oldest' ] = $data[ 'oldest' ];
			$results[ 'range' ]  = $data[ 'range' ];
		}

		return $results;
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

	public function statistics_components_log( int $days = 30, int $blog_id = - 1 ) : array {
		$query = array(
			'select' => array(
				'e.`component`',
				'COUNT(l.log_id) AS logs'
			),
			'from'   => array(
				$this->events . ' e',
				'LEFT JOIN ' . $this->logs . ' l ON e.`event_id` = l.`event_id`'
			),
			'where'  => array(
				$this->prepare( 'l.`logged` IS NULL OR l.`logged` > DATE_SUB(NOW(), INTERVAL %d DAY)', $days )
			),
			'group'  => 'e.`component`',
			'order'  => 'e.`component`'
		);

		if ( $blog_id > - 1 ) {
			$query[ 'where' ][] = $this->prepare( 'l.`blog_id` = %d', $blog_id );
		}

		$sql = $this->build_query( $query, false );
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
