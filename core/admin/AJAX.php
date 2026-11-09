<?php

namespace Dev4Press\Plugin\CoreActivity\Admin;

use Dev4Press\Plugin\CoreActivity\Basic\DB;
use Dev4Press\Plugin\CoreActivity\Log\Activity;
use Dev4Press\Plugin\CoreActivity\Log\WhoIs;
use Dev4Press\Plugin\CoreActivity\Table\Live;
use Dev4Press\v56\Core\Quick\Sanitize;
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
		if ( ! current_user_can( is_multisite() ? 'manage_network_options' : 'manage_options' ) ) {
			$this->json_respond( array( 'toggle' => '' ), 403 );
		}

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
		if ( ! current_user_can( is_multisite() ? 'manage_network_options' : 'manage_options' ) ) {
			$this->json_respond( array( 'toggle' => '' ), 403 );
		}

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

		if ( isset( $_REQUEST['args'] ) && is_string( $_REQUEST['args'] ) ) {
			$request = json_decode( wp_unslash( $_REQUEST['args'] ), true ); // phpcs:ignore WordPress.Security.NonceVerification,WordPress.Security.ValidatedSanitizedInput

			$scope = $request['scope'] ?? '';
			$allowed = $scope === 'network' ? is_multisite() && current_user_can( 'manage_network_options' ) : $scope === 'site' && current_user_can( 'manage_options' );

			if ( $allowed && isset( $request['nonce'] ) && is_string( $request['nonce'] ) && wp_verify_nonce( $request['nonce'], 'coreactivity-live-update-' . $scope ) && isset( $request['atts'] ) && is_array( $request['atts'] ) ) {
				$defaults = array_fill_keys( array( 'filter-blog_id', 'filter-user_id', 'filter-event_id', 'filter-ip', 'filter-component', 'filter-country_code', 'filter-context', 'filter-method', 'filter-object_type', 'filter-object_id', 'filter-object_name', 'view', 'search', 'period', 'orderby', 'order', 'paged', 'min_id' ), '' );
				$atts = array_merge( $defaults, array_intersect_key( $request['atts'], $defaults ) );

				foreach ( $atts as $key => $value ) {
					if ( ! is_scalar( $value ) ) {
						$atts[ $key ] = '';
					}
				}

				$atts['min_id'] = absint( $request['id'] ?? 0 );
				$lock = is_multisite() && $scope === 'site' ? array( 'blog_id' => get_current_blog_id() ) : array();

				$_grid = new Live( array( '_request_args' => $atts, '_filter_lock' => $lock, '_url_page_name' => 'coreactivity-logs' ) );
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
		if ( ! current_user_can( 'manage_options' ) && ! current_user_can( 'manage_network_options' ) ) {
			status_header( 403 );
			exit;
		}

		$ip  = isset( $_POST['whois'] ) ? Sanitize::text( $_POST['whois'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification,WordPress.Security.ValidatedSanitizedInput
		$out = esc_html__( 'WhoIs check failed', 'coreactivity' );

		if ( ! empty( $ip ) && isset( $_REQUEST['_ajax_nonce'] ) && wp_verify_nonce( sanitize_key( $_REQUEST['_ajax_nonce'] ), 'coreactivity-whois-' . $ip ) ) {
			$out = nl2br( esc_html( trim( WhoIs::i()->get( $ip ) ) ) );
		}

		die( $out ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped above; nl2br adds markup.
	}
}
