<?php

namespace Dev4Press\Plugin\CoreActivity\Components;

use Dev4Press\Plugin\CoreActivity\Base\Component;
use Dev4Press\v42\WordPress as LibWordPress;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WordPress extends Component {
	protected $plugin = 'coreactivity';
	protected $name = 'wordpress';
	protected $wp_version = '';

	public function tracking() {
		if ( $this->is_active( 'cron-schedule' ) ) {
			add_filter( 'schedule_event', array( $this, 'event_schedule_event' ), 10000 );
		}

		if ( $this->is_active( 'content-export' ) ) {
			add_filter( 'export_wp', array( $this, 'event_content_export' ) );
		}

		if ( $this->is_active( 'update-core' ) || $this->is_active( 'update-core-auto' ) ) {
			add_filter( 'update_feedback', array( $this, 'prepare_update' ) );
			add_action( '_core_updated_successfully', array( $this, 'event_update' ) );
		}
	}

	public function label() : string {
		return __( "WordPress", "coreactivity" );
	}

	protected function get_events() : array {
		return array(
			'update-core'      => array( 'label' => __( "WordPress Update", "coreactivity" ), 'scope' => 'network' ),
			'update-core-auto' => array( 'label' => __( "WordPress Auto Update", "coreactivity" ), 'scope' => 'network' ),
			'cron-schedule'    => array( 'label' => __( "CRON Event Scheduled", "coreactivity" ), 'scope' => 'blog' ),
			'content-export'   => array( 'label' => __( "Content Export", "coreactivity" ), 'scope' => 'blog' )
		);
	}

	public function prepare_update( $message ) {
		if ( empty( $this->wp_version ) ) {
			$this->wp_version = LibWordPress::instance()->version();
		}

		return $message;
	}

	public function event_schedule_event( $event ) {
		if ( $event ) {
			$caller = wp_debug_backtrace_summary( null, 4, false );

			if ( ! in_array( 'wp_reschedule_event', $caller ) ) {
				$this->log( 'cron-schedule', array( 'object_type' => 'cron', 'object_name' => $event->hook ), array(
					'timestamp' => $event->timestamp,
					'schedule'  => $event->schedule,
					'caller'    => $caller
				) );
			}
		}

		return $event;
	}

	public function event_content_export( $args ) {
		$this->log( 'content-export', array(), array(
			'export_args' => $args
		) );
	}

	public function event_update( $wp_version ) {
		$event = isset( $GLOBALS[ 'pagenow' ] ) && $GLOBALS[ 'pagenow' ] == 'update-core.php' ? 'update-core' : 'update-core-auto';

		if ( $this->is_active( $event ) ) {
			$this->log( $event, array(), array(
				'from' => $this->wp_version,
				'to'   => $wp_version
			) );
		}
	}
}
