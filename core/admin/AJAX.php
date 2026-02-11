<?php

namespace Dev4Press\Plugin\CoreActivity\Admin;

use Dev4Press\Plugin\CoreActivity\Basic\DB;
use Dev4Press\Plugin\CoreActivity\Log\Activity;
use Dev4Press\Plugin\CoreActivity\Log\WhoIs;
use Dev4Press\Plugin\CoreActivity\Table\Live;
use Dev4Press\v55\Core\Quick\Sanitize;
use JetBrains\PhpStorm\NoReturn;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AJAX {
	private function __construct() {
		add_action( 'wp_ajax_coreactivity_toggle_event', array( $this, 'toggle_event' ) );
		add_action( 'wp_ajax_coreactivity_toggle_notification', array( $this, 'toggle_notification' ) );
		add_action( 'wp_ajax_coreactivity_live_logs', array( $this, 'live_logs' ) );
		add_action( 'wp_ajax_coreactivity_whois_ip', array( $this, 'whois_ip' ) );
	}

	/** @deprecated 3.0 Use self::i() instead. */
	public static function instance() : static {
		return static::i();
	}

	public static function i() : static {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new AJAX();
		}

		return $instance;
	}

	#[NoReturn]
	private function json_respond( $response, $code = 200 ) : void {
		status_header( $code );

		if ( ! headers_sent() ) {
			nocache_headers();
			header( 'Content-Type: application/json' );
		}

		die( wp_json_encode( $response ) );
	}

	#[NoReturn]
	public function toggle_event() : void {
		$id = isset( $_POST['event'] ) ? absint( $_POST['event'] ) : 0;

		$toggle = '';
		if ( $id > 0 && isset( $_REQUEST['_ajax_nonce'] ) && wp_verify_nonce( sanitize_key( $_REQUEST['_ajax_nonce'] ), 'coreactivity-toggle-event-' . $id ) ) {
			$status = Activity::i()->event_status( $id );

			if ( ! empty( $status ) ) {
				$new    = $status == 'active' ? 'inactive' : 'active';
				$toggle = $status == 'active' ? 'off' : 'on';

				DB::i()->change_event_status( $id, $new );
			}
		}

		$this->json_respond( array( 'toggle' => $toggle ) );
	}

	#[NoReturn]
	public function toggle_notification() : void {
		$id  = isset( $_POST['event'] ) ? absint( $_POST['event'] ) : 0;
		$key = isset( $_POST['notification'] ) ? Sanitize::slug( $_POST['notification'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification,WordPress.Security.ValidatedSanitizedInput

		$toggle = '';
		if ( $id > 0 && isset( $_REQUEST['_ajax_nonce'] ) && wp_verify_nonce( sanitize_key( $_REQUEST['_ajax_nonce'] ), 'coreactivity-toggle-notification-' . $key . '-' . $id ) ) {
			$change = Activity::i()->event_notification_toggle( $id, $key );

			if ( ! is_null( $change ) ) {
				$toggle = $change ? 'on' : 'off';
			}
		}

		$this->json_respond( array( 'toggle' => $toggle ) );
	}

	#[NoReturn]
	public function live_logs() : void {
		$output = '';

		if ( isset( $_REQUEST['args'] ) ) {
			$request = json_decode( wp_unslash( $_REQUEST['args'] ), true ); // phpcs:ignore WordPress.Security.NonceVerification,WordPress.Security.ValidatedSanitizedInput

			if ( isset( $request['nonce'] ) && wp_verify_nonce( $request['nonce'], 'coreactivity-live-update' ) ) {
				$request['atts']['min_id'] = absint( $request['id'] );

				$request['settings']['_request_args']  = $request['atts'];
				$request['settings']['_filter_lock']   = $request['lock'];
				$request['settings']['_limit_lock']    = $request['limit'];
				$request['settings']['_filter_key']    = $request['filter'];
				$request['settings']['_url_page_name'] = $request['page'];

				$_grid = new Live( $request['settings'] );
				$_grid->update();
				$_grid->prepare_items();

				ob_start();
				$_grid->display();
				$output = ob_get_contents();
				ob_end_clean();

				if ( ! empty( $output ) ) {
					$output = DB::i()->get_last_log_id() . '.' . $output;
				}
			}
		}

		die( $output ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	#[NoReturn]
	public function whois_ip() : void {
		$ip  = isset( $_POST['whois'] ) ? Sanitize::text( $_POST['whois'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification,WordPress.Security.ValidatedSanitizedInput
		$out = __( 'WhoIs check failed', 'coreactivity' );

		if ( ! empty( $ip ) && isset( $_REQUEST['_ajax_nonce'] ) && wp_verify_nonce( sanitize_key( $_REQUEST['_ajax_nonce'] ), 'coreactivity-whois-' . $ip ) ) {
			$out = nl2br( trim( WhoIs::i()->get( $ip ) ) );
		}

		die( $out ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
