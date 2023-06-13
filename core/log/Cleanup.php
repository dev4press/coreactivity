<?php

namespace Dev4Press\Plugin\CoreActivity\Log;

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

	public function auto_cleanup_log() {
		$data = array(
			'keep-log-months' => coreactivity_settings()->get( 'auto_cleanup_period' ),
			'removed-logs'    => 0
		);

		do_action( 'coreactivity_cleanup_auto_completed', $data );
	}
}
