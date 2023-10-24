<?php

namespace Dev4Press\Plugin\CoreActivity\Log;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Location {
	public function __construct() {

	}

	public static function instance() : Location {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new Location();
		}

		return $instance;
	}
}
