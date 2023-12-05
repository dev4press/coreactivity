<?php

namespace Dev4Press\Plugin\CoreActivity\Basic;

use Dev4Press\v45\Core\Plugins\DB as BaseDB;

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
		'blog_id'      => '%d',
		'event_id'     => '%d',
		'user_id'      => '%d',
		'logged'       => '%s',
		'ip'           => '%s',
		'context'      => '%s',
		'method'       => '%s',
		'protocol'     => '%s',
		'request'      => '%s',
		'object_type'  => '%s',
		'object_id'    => '%d',
		'object_name'  => '%s',
		'country_code' => '%s',
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
			'tables' => array(),
		);

		foreach ( $data as $row ) {
			$_table_name  = strtolower( $row['Name'] ?? 'Unknown' );
			$_actual_name = str_replace( $prefix, '', $_table_name );
			$_total       = absint( $row['Data_length'] ?? 0 ) + absint( $row['Index_length'] ?? 0 ) + absint( $row['Data_free'] ?? 0 );

			$results['tables'][ $_actual_name ] = array(
				'table'            => $_table_name,
				'engine'           => $row['Engine'] ?? 'Unknown',
				'total'            => $_total,
				'size'             => absint( $row['Data_length'] ?? 0 ),
				'free'             => absint( $row['Data_free'] ?? 0 ),
				'index'            => absint( $row['Index_length'] ?? 0 ),
				'rows'             => absint( $row['Rows'] ?? 0 ),
				'average_row_size' => absint( $row['Avg_row_length'] ?? 0 ),
				'auto_increment'   => $row['Auto_increment'] ?? 0,
				'created'          => $row['Create_time'] ?? '',
				'updated'          => $row['Update_time'] ?? '',
				'collation'        => $row['Collation'] ?? '',
			);
		}

		$results['size'] = $results['tables']['logs']['total'] + $results['tables']['logmeta']['total'] + $results['tables']['events']['total'];

		$sql  = "SELECT DATE(MIN(`logged`)) as `oldest`, DATEDIFF(NOW(), MIN(`logged`)) as `range` FROM " . $this->logs;
		$data = $this->get_row( $sql, ARRAY_A );

		if ( ! empty( $data ) ) {
			$results['oldest'] = $data['oldest'];
			$results['range']  = $data['range'];
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
			'status'    => $status,
		);

		if ( ! empty( $rules ) ) {
			$data['rules'] = json_encode( $rules );
		}

		$result = $this->insert( $this->events, $data );

		return $result == 1 ? absint( $this->get_insert_id() ) : 0;
	}

	public function change_event_rules( int $event_id, array $rules ) {
		$rules = json_encode( $rules );

		$this->update( $this->events, array( 'rules' => $rules ), array( 'event_id' => $event_id ), array( '%s' ), array( '%d' ) );
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
				'COUNT(l.log_id) AS logs',
			),
			'from'   => array(
				$this->events . ' e',
				'LEFT JOIN ' . $this->logs . ' l ON e.`event_id` = l.`event_id`',
			),
			'where'  => array(
				$this->prepare( 'l.`logged` IS NULL OR l.`logged` > DATE_SUB(NOW(), INTERVAL %d DAY)', $days ),
			),
			'group'  => 'e.`component`',
			'order'  => 'e.`component`',
		);

		if ( $blog_id > - 1 ) {
			$query['where'][] = $this->prepare( 'l.`blog_id` = %d', $blog_id );
		}

		$sql = $this->build_query( $query, false );
		$raw = $this->get_results( $sql );

		return empty( $raw ) ? array() : $this->pluck( $raw, 'logs', 'component' );
	}

	public function get_last_log_id() : int {
		$sql = "SELECT MAX(`log_id`) FROM " . $this->logs;

		return absint( $this->get_var( $sql ) );
	}

	public function remove_log_meta_orphans() {
		$sql = "DELETE m FROM $this->logmeta m LEFT JOIN $this->logs l ON l.log_id = m.log_id WHERE l.log_id IS NULL";

		return $this->query( $sql );
	}

	public function get_entries_by_event_ids_and_date_range( array $events_ids, string $from, string $to = '' ) : array {
		$events_in = $this->prepare_in_list( $events_ids, '%d' );

		if ( empty( $to ) ) {
			$to = $this->datetime();
		}

		$sql = $this->prepare( "SELECT * FROM " . $this->logs . " WHERE `event_id` IN (" . $events_in . ") AND (`logged` BETWEEN %s AND %s )", $from, $to );
		$raw = $this->run_and_index( $sql, 'log_id', ARRAY_A );

		$entries = array();

		if ( ! empty( $raw ) ) {
			foreach ( $raw as $id => $entry ) {
				$entry['meta'] = array();

				$entries[ $id ] = $entry;
			}

			$ids = $this->pluck( $raw, 'log_id' );
			$ids = $this->prepare_in_list( $ids, '%d' );

			$sql  = "SELECT * FROM " . $this->logmeta . " WHERE `log_id` IN (" . $ids . ")";
			$meta = $this->get_results( $sql );

			foreach ( $meta as $item ) {
				$id = absint( $item->log_id );

				$entries[ $id ]['meta'][ $item->meta_key ] = maybe_unserialize( $item->meta_value );
			}
		}

		return $entries;
	}

	public function get_entries_counts_by_event_ids_and_date_range( array $events_ids, string $from, string $to = '' ) : array {
		$events_in = $this->prepare_in_list( $events_ids, '%d' );

		if ( empty( $to ) ) {
			$to = $this->datetime();
		}

		$sql = $this->prepare( "SELECT e.`component`, e.`event`, COUNT(*) as `count` FROM " . $this->logs . " l
				INNER JOIN " . $this->events . " e ON e.event_id = l.event_id
				WHERE (l.`logged` BETWEEN %s AND %s) AND l.`event_id` IN (" . $events_in . ")
				GROUP BY e.`component`, e.`event` ORDER BY e.`component`, e.`event`", $from, $to );
		$raw = $this->get_results( $sql );

		$data = array();

		if ( ! empty( $raw ) ) {
			foreach ( $raw as $row ) {
				if ( ! isset( $data[ $row->component ] ) ) {
					$data[ $row->component ] = array(
						'events' => array(),
						'total'  => 0,
					);
				}

				$data[ $row->component ]['total']                 += $row->count;
				$data[ $row->component ]['events'][ $row->event ] = $row->count;
			}
		}

		return $data;
	}

	public function count_entries_by_event_ids( array $events_ids, string $ip, int $seconds = 86400 ) : int {
		$events_in = $this->prepare_in_list( $events_ids, '%d' );
		$now_gmt   = $this->datetime();

		$sql = $this->prepare( "SELECT COUNT(*) FROM " . $this->logs . " WHERE `ip` = %s AND `event_id` IN (" . $events_in . ") AND `logged` > DATE_SUB(%s, INTERVAL %d SECOND)", $ip, $now_gmt, $seconds );
		$raw = $this->get_var( $sql );

		return absint( $raw );
	}

	public function count_entries_by_event_ids_expanded( array $events_ids, string $ip, int $seconds = 86400 ) : array {
		$events_in = $this->prepare_in_list( $events_ids, '%d' );
		$now_gmt   = $this->datetime();

		$sql = $this->prepare( "SELECT COUNT(*) as `entries`, MIN(`logged`) as `start`, MAX(`logged`) as `end`, TIMESTAMPDIFF(SECOND, MIN(`logged`), MAX(`logged`)) as `period` FROM " . $this->logs . " WHERE `ip` = %s AND `event_id` IN (" . $events_in . ") AND `logged` > DATE_SUB(%s, INTERVAL %d SECOND)", $ip, $now_gmt, $seconds );
		$raw = $this->get_row( $sql );

		return array(
			'entries' => absint( $raw->entries ),
			'period'  => absint( $raw->period ),
			'start'   => $raw->start,
			'end'     => $raw->end,
		);
	}

	public function get_events_statistics( $range = 'archive' ) : array {
		switch ( $range ) {
			default:
			case 'archive':
				$where = 'DATE(l.`logged`) < CURDATE()';
				break;
			case 'one':
				$where = 'DATE(l.`logged`) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)';
				break;
			case 'two':
				$where = 'DATE(l.`logged`) = DATE_SUB(CURDATE(), INTERVAL 2 DAY)';
				break;
		}

		$sql = "SELECT e.`component`, e.`event`, DATE(l.`logged`) AS `log_date`, COUNT(l.`log_id`) as `log_count`
				FROM " . $this->logs . " l INNER JOIN " . $this->events . " e ON l.`event_id` = e.`event_id` WHERE " . $where . "
				GROUP BY e.`component`, e.`event`, `log_date` ORDER BY e.`component`, e.`event`, `log_date`;";
		$raw = $this->get_results( $sql );

		$statistics = array();

		foreach ( $raw as $row ) {
			$c = $row->component;
			$e = $row->event;

			if ( ! isset( $statistics[ $c ] ) ) {
				$statistics[ $c ] = array();
			}

			if ( ! isset( $statistics[ $c ][ $e ] ) ) {
				$statistics[ $c ][ $e ] = array();
			}

			$statistics[ $c ][ $e ][ $row->log_date ] = absint( $row->log_count );
		}

		return $statistics;
	}
}
