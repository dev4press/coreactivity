<?php

namespace Dev4Press\Plugin\CoreActivity\Plugins;

use Dev4Press\Plugin\CoreActivity\Base\Component;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SweepPress extends Component {
	protected $plugin = 'coreactivity';
	protected $name = 'sweeppress';
	protected $icon = 'plugin-sweeppress';
	protected $category = 'plugin';

	public function tracking() {
		if ( $this->is_active( 'completed' ) ) {
			add_action( 'sweeppress_sweep_completed', array( $this, 'event_completed' ) );
		}
	}

	public function label() : string {
		return __( "SweepPress" );
	}

	protected function get_events() : array {
		return array(
			'completed' => array( 'label' => __( "Sweeping Completed", "coreactivity" ) )
		);
	}

	public function event_completed( $results ) {
		$this->log( 'completed', array(), array(
			'source'   => $results[ 'stats' ][ 'source' ],
			'sweepers' => array_keys( $results[ 'sweepers' ] )
		) );
	}
}
