<?php

namespace Dev4Press\Plugin\CoreActivity\Log;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Core {
	public function __construct() {
		add_action( 'coreactivity_plugin_core_ready', array( $this, 'ready' ), 20 );
	}

	public static function instance() : Core {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new Core();
		}

		return $instance;
	}

	public function ready() {

	}
}
