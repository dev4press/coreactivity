<?php

namespace Dev4Press\Plugin\CoreActivity\Basic;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Notifications {
	public function __construct() {

	}

	public static function instance() : Notifications {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new Notifications();
		}

		return $instance;
	}

	public function daily_digest() {
		$title = '[%SITE_NAME%] Website Daily Activity Digest';
	}

	public function weekly_digest() {
		$title = '[%SITE_NAME%] Website Weekly Activity Digest';
	}

	public function instant_digest() {
		$title = '[%SITE_NAME%] Website Activity Notice';
	}
}
