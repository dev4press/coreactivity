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
		if ( $this->is_active( 'log-cleanup-auto' ) ) {
			add_action( 'coreactivity_cleanup_auto_completed', array( $this, 'event_cleanup_auto' ) );
		}
	}

	public function label() : string {
		return __( "Internal" );
	}

	protected function get_events() : array {
		return array(
			'log-cleanup'      => array( 'label' => __( "Log Cleanup", "coreactivity" ) ),
			'log-cleanup-auto' => array( 'label' => __( "Auto Log Cleanup", "coreactivity" ) )
		);
	}

	public function event_cleanup_auto( $data ) {
		$this->log( 'log-cleanup-auto', array(), $data );
	}
}
