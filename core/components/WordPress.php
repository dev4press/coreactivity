<?php

namespace Dev4Press\Plugin\CoreActivity\Components;

use Dev4Press\Plugin\CoreActivity\Base\Component;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WordPress extends Component {
	protected $plugin = 'coreactivity';
	protected $name = 'wordpress';

	public function tracking() {
		if ( $this->is_active( 'cron-schedule' ) ) {
			add_filter( 'schedule_event', array( $this, 'event_schedule_event' ), 10000 );
		}
	}

	public function label() : string {
		return __( "WordPress", "coreactivity" );
	}

	protected function get_events() : array {
		return array(
			'cron-schedule' => array( 'label' => __( "CRON Event Scheduled", "coreactivity" ), 'scope' => 'blog' )
		);
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
}
