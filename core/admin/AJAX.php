<?php

namespace Dev4Press\Plugin\CoreActivity\Admin;

use Dev4Press\Plugin\CoreActivity\Basic\DB;
use Dev4Press\Plugin\CoreActivity\Log\Init;
use Dev4Press\Plugin\CoreActivity\Table\Live;
use Dev4Press\v43\Core\Quick\Sanitize;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AJAX {
	public function __construct() {
		add_action( 'wp_ajax_coreactivity_toggle_event', array( $this, 'toggle_event' ) );
		add_action( 'wp_ajax_coreactivity_toggle_notification', array( $this, 'toggle_notification' ) );
		add_action( 'wp_ajax_coreactivity_live_logs', array( $this, 'live_logs' ) );
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
				$toggle = $status == 'active' ? 'off' : 'on';

				DB::instance()->change_event_status( $id, $new );
			}
		}

		$this->json_respond( array( 'toggle' => $toggle ) );
	}

	public function toggle_notification() {
		$id  = isset( $_POST[ 'event' ] ) ? Sanitize::absint( $_POST[ 'event' ] ) : 0;
		$key = isset( $_POST[ 'notification' ] ) ? Sanitize::slug( $_POST[ 'notification' ] ) : '';

		$toggle = '';
		if ( $id > 0 && wp_verify_nonce( $_REQUEST[ '_ajax_nonce' ], 'coreactivity-toggle-notification-' . $key . '-' . $id ) ) {
			$change = Init::instance()->event_notification_toggle( $id, $key );

			if ( ! is_null( $change ) ) {
				$toggle = $change ? 'on' : 'off';
			}
		}

		$this->json_respond( array( 'toggle' => $toggle ) );
	}

	public function live_logs() {
		$output = '';

		if ( isset( $_REQUEST[ 'args' ] ) ) {
			$request = json_decode( wp_unslash( $_REQUEST[ 'args' ] ), true );

			if ( isset( $request[ 'nonce' ] ) && wp_verify_nonce( $request[ 'nonce' ], 'coreactivity-live-update' ) ) {
				$request[ 'atts' ][ 'min_id' ] = Sanitize::absint( $request[ 'id' ] );

				$_grid = new Live();
				$_grid->update( $request[ 'atts' ], $request[ 'lock' ], $request[ 'limit' ], $request[ 'filter' ] );
				$_grid->prepare_items();

				ob_start();
				$_grid->display();
				$output = ob_get_contents();
				ob_end_clean();

				if ( ! empty( $output ) ) {
					$output = DB::instance()->get_last_log_id() . '.' . $output;
				}
			}
		}

		die( $output );
	}
}
