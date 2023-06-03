<?php

namespace Dev4Press\Plugin\CoreActivity\Log;

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
}
