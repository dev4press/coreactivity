<?php

namespace Dev4Press\Plugin\CoreActivity\Log;

use Dev4Press\Plugin\CoreActivity\Basic\DB;
use Dev4Press\v46\Core\Quick\Sanitize;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Cleanup {
	public function __construct() {
	}

	public static function instance() : Cleanup {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new Cleanup();
		}

		return $instance;
	}

	public function cleanup_log( $when, $what ) {
		$interval = substr( $when, 0, 1 );
		$value    = absint( substr( $when, 1 ) );
		$keep_key = 'keep-log-' . ( $interval == 'd' ? 'days' : 'months' );

		$data = array(
			$keep_key      => $value,
			'events-count' => count( $what ),
			'removed-logs' => $this->cleanup( $interval, $value, $what ),
		);

		do_action( 'coreactivity_cleanup_completed', $data );
	}

	public function auto_cleanup_log() {
		$data = array(
			'keep-log-months' => coreactivity_settings()->get( 'auto_cleanup_period' ),
		);

		$data['removed-logs'] = $this->cleanup( 'm', $data['keep-log-months'] );

		do_action( 'coreactivity_cleanup_auto_completed', $data );
	}

	private function cleanup( $interval, $value, $events = null ) : int {
		$sql   = "DELETE FROM " . DB::instance()->logs;
		$where = array();

		if ( $value > 0 ) {
			$where[] = DB::instance()->prepare( "logged < DATE_SUB(NOW(), INTERVAL %d " . ( $interval == 'd' ? 'DAY' : 'MONTH' ) . ")", $value );
		}

		if ( is_array( $events ) && ! empty( $events ) ) {
			$where[] = 'event_id IN (' . DB::instance()->prepare_in_list( $events, '%d' ) . ')';
		}

		if ( ! empty( $where ) ) {
			$sql .= " WHERE " . join( " AND ", $where );
		}

		$rows = DB::instance()->query( $sql );

		DB::instance()->remove_log_meta_orphans();

		return is_numeric( $rows ) ? absint( $rows ) : 0;
	}
}
