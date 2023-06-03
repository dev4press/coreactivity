<?php

namespace Dev4Press\Plugin\CoreActivity\Admin;

use Dev4Press\Plugin\CoreActivity\Basic\DB;
use Dev4Press\Plugin\CoreActivity\Log\Init;
use Dev4Press\v42\Core\Quick\Sanitize;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AJAX {
	public function __construct() {
		add_action( 'wp_ajax_coreactivity_toggle_event', array( $this, 'toggle_event' ) );
	}

	public static function instance() : AJAX {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new AJAX();
		}

		return $instance;
	}

	private function json_respond( $response, $code = 200 ) {
		status_header( $code );

		if ( ! headers_sent() ) {
			nocache_headers();
			header( 'Content-Type: application/json' );
		}

		die( json_encode( $response ) );
	}

	public function toggle_event() {
		$id = isset( $_POST[ 'event' ] ) ? Sanitize::absint( $_POST[ 'event' ] ) : 0;

		$toggle = '';
		if ( $id > 0 && wp_verify_nonce( $_REQUEST[ '_ajax_nonce' ], 'coreactivity-toggle-event-' . $id ) ) {
			$status = Init::instance()->event_status( $id );

			if ( ! empty( $status ) ) {
				$new    = $status == 'active' ? 'inactive' : 'active';
				$toggle = $status == 'active' ? 'd4p-ui-toggle-off' : 'd4p-ui-toggle-on';

				DB::instance()->change_event_status( $id, $new );
			}
		}

		$this->json_respond( array( 'toggle' => $toggle ) );
	}
}
