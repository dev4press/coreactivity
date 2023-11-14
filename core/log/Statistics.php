<?php

namespace Dev4Press\Plugin\CoreActivity\Log;

use DateTime;
use Dev4Press\Plugin\CoreActivity\Basic\DB;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Statistics {
	public function __construct() {

	}

	public static function instance() : Statistics {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new Statistics();
		}

		return $instance;
	}

	public function a() : Activity {
		return Activity::instance();
	}

	public function db() : DB {
		return DB::instance();
	}

	public function overall() {
		return $this->a()->statistics;
	}

	public function detailed( int $days = 30, int $blog_id = - 1 ) : array {
		$counts  = $this->db()->statistics_components_log( $days, $blog_id );
		$results = array(
			'total'      => 0,
			'components' => array(),
		);

		foreach ( $counts as $component => $count ) {
			$results['total']                    += $count;
			$results['components'][ $component ] = array(
				'label' => $this->a()->get_component_label( $component ),
				'icon'  => $this->a()->get_component_icon( $component ),
				'count' => $count,
			);
		}

		return $results;
	}

	public function count_events_entries( $events, $ip, $seconds = 86400, bool $expanded = false ) {
		$ids = array();

		foreach ( $events as $event ) {
			$event_id = $this->a()->get_event_id( $event[0], $event[1] );

			if ( $event_id > 0 && ! in_array( $event_id, $ids ) ) {
				$ids[] = $event_id;
			}
		}

		if ( $expanded ) {
			return $this->db()->count_entries_by_event_ids_expanded( $ids, $ip, $seconds );
		} else {
			return $this->db()->count_entries_by_event_ids( $ids, $ip, $seconds );
		}
	}

	public function count_event_entries( $component, $event, $ip, $seconds = 86400 ) : int {
		$event_id = $this->a()->get_event_id( $component, $event );

		if ( $event_id == 0 ) {
			return - 1;
		}

		return $this->db()->count_entries_by_event_ids( array( $event_id ), $ip, $seconds );
	}

	public function initial_update() {
		$statistics = DB::instance()->get_events_statistics();

		$this->save_statistics( $statistics );
	}

	public function daily_update() {
		$statistics = coreactivity_settings()->get( 'statistics', 'storage' );

		if ( empty( $statistics ) ) {
			$this->initial_update();
		} else {
			$statistics = json_decode( $statistics, true );

			if ( ! is_array( $statistics ) ) {
				$this->initial_update();
			} else {
				$two = DB::instance()->get_events_statistics( 'two' );
				$one = DB::instance()->get_events_statistics( 'one' );

				$statistics = $this->update_statistics_array( $statistics, $two );
				$statistics = $this->update_statistics_array( $statistics, $one );

				$this->save_statistics( $statistics );
			}
		}
	}

	private function update_statistics_array( $statistics, $input ) : array {
		if ( ! empty( $input ) ) {
			foreach ( $input as $c => $events ) {
				if ( ! isset( $statistics[ $c ] ) ) {
					$statistics[ $c ] = array();
				}

				foreach ( $events as $e => $days ) {
					if ( ! isset( $statistics[ $c ][ $e ] ) ) {
						$statistics[ $c ][ $e ] = array();
					}

					foreach ( $days as $d => $l ) {
						if ( ! isset( $statistics[ $c ][ $e ][ $d ] ) ) {
							$statistics[ $c ][ $e ][ $d ] = 0;
						}

						if ( $l > $statistics[ $c ][ $e ][ $d ] ) {
							$statistics[ $c ][ $e ][ $d ] = $l;
						}
					}
				}
			}
		}

		return $statistics;
	}

	private function save_statistics( $statistics ) {
		coreactivity_settings()->set( 'statistics', json_encode( $statistics ), 'storage' );
		coreactivity_settings()->set( 'statistics_latest', gmdate( 'Y-m-d' ), 'storage' );

		coreactivity_settings()->save( 'storage' );
	}
}
