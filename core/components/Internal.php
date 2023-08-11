<?php

namespace Dev4Press\Plugin\CoreActivity\Components;

use Dev4Press\Plugin\CoreActivity\Base\Component;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Internal extends Component {
	protected $plugin = 'coreactivity';
	protected $name = 'internal';
	protected $icon = 'plugin-coreactivity';
	protected $category = 'internal';

	public function tracking() {
		if ( $this->is_active( 'log-cleanup' ) ) {
			add_action( 'coreactivity_cleanup_completed', array( $this, 'event_cleanup' ) );
		}

		if ( $this->is_active( 'log-cleanup-auto' ) ) {
			add_action( 'coreactivity_cleanup_auto_completed', array( $this, 'event_cleanup_auto' ) );
		}

		if ( $this->is_active( 'digest-daily' ) ) {
			add_action( 'coreactivity_notifications_daily_digest', array( $this, 'event_daily_digest' ), 10, 3 );
		}

		if ( $this->is_active( 'digest-weekly' ) ) {
			add_action( 'coreactivity_notifications_weekly_digest', array( $this, 'event_weekly_digest' ), 10, 3 );
		}
	}

	public function label() : string {
		return __( "Internal", "coreactivity" );
	}

	protected function get_events() : array {
		return array(
			'log-cleanup'      => array(
				'label' => __( "Log Cleanup", "coreactivity" ),
			),
			'log-cleanup-auto' => array(
				'label' => __( "Auto Log Cleanup", "coreactivity" ),
			),
			'digest-daily'     => array(
				'label'  => __( "Daily Digest", "coreactivity" ),
				'status' => 'inactive',
			),
			'digest-weekly'    => array(
				'label'  => __( "Weekly Digest", "coreactivity" ),
				'status' => 'inactive',
			),
		);
	}

	public function event_cleanup_auto( $data ) {
		$this->log( 'log-cleanup-auto', array(), $data );
	}

	public function event_cleanup( $data ) {
		$this->log( 'log-cleanup', array(), $data );
	}

	public function event_daily_digest( $events, $from, $to ) {
		$this->log( 'digest-daily', array(), array(
				'from' => $from,
				'to'   => $to,
				'data' => $events,
			)
		);
	}

	public function event_weekly_digest( $events, $from, $to ) {
		$this->log( 'digest-weekly', array(), array(
				'from' => $from,
				'to'   => $to,
				'data' => $events,
			)
		);
	}
}
