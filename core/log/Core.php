<?php

namespace Dev4Press\Plugin\CoreActivity\Log;

use Dev4Press\Plugin\CoreActivity\Basic\DB;
use Dev4Press\v46\Core\DateTime;
use Dev4Press\v46\Core\Helpers\IP;
use Dev4Press\v46\Core\Quick\Sanitize;
use Dev4Press\v46\Core\Quick\URL;
use Dev4Press\v46\Core\Scope;
use Dev4Press\v46\Service\GEOIP\Location;
use Dev4Press\v46\WordPress;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Core {
	private $cached_data;
	private $page_events = array();
	private $geo_code = false;
	private $geo_meta = false;

	private $request_contexts = array(
		'AJAX',
		'CRON',
		'REST',
		'CLI',
	);

	private $request_methods = array(
		'GET',
		'POST',
		'HEAD',
		'PUT',
		'DELETE',
		'CONNECT',
		'OPTIONS',
		'TRACE',
		'PATCH',
		'SEARCH',
	);

	public function __construct() {
		$this->cached_data = array(
			'ip'        => IP::visitor(),
			'server_ip' => isset( $_SERVER['SERVER_ADDR'] ) ? IP::server() : '',
			'ua'        => $this->get_user_agent(),
			'referer'   => $this->get_referer(),
			'method'    => $this->get_request_method(),
			'protocol'  => wp_get_server_protocol(),
			'request'   => URL::current_url_request(),
			'context'   => WordPress::instance()->context(),
		);

		$this->cached_data['anon'] = in_array( $this->cached_data['context'], array( 'CLI', 'CRON' ) );

		if ( coreactivity_settings()->get( 'log_country_code' ) ) {
			$this->geo_code = true;
		}

		if ( coreactivity_settings()->get( 'log_if_available_expanded_location' ) ) {
			$this->geo_meta = true;
		}

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

	public function scope() : Scope {
		return Scope::instance();
	}

	public function wp() : WordPress {
		return WordPress::instance();
	}

	public function log( int $event_id, array $data = array(), array $meta = array() ) : int {
		if ( ! coreactivity()->is_logging_active() ) {
			return - 1;
		}

		$event = Activity::instance()->get_event_by_id( $event_id );

		/**
		 * Main control filter controlling if the event logging will proceed or not. Only hook to this filter if you want to control the logging process.
		 *
		 * @param bool   $skip     return TRUE to skip logging of the event
		 * @param int    $event_id ID of the event that will be logged
		 * @param object $event    event object with all the event information
		 * @param array  $data     main data array to log about the event
		 * @param array  $meta     additional data array to log about the event
		 *
		 * @return bool TRUE to skip logging, FALSE to proceed with logging.
		 */
		if ( apply_filters( 'coreactivity_skip_log', false, $event_id, $event, $data, $meta ) === true ) {
			return 0;
		}

		$data = $this->prepare_data( $data );
		$meta = $this->prepare_meta( $meta );

		if ( $this->geo_code || $this->geo_meta ) {
			$geo = GEO::instance()->locate( $data['ip'] );

			if ( $geo instanceof Location ) {
				if ( ! isset( $data['country_code'] ) && ! empty( $geo->country_code ) ) {
					$data['country_code'] = $geo->country_code;
				}

				if ( $this->geo_meta ) {
					$geo_meta = $geo->meta();

					if ( ! empty( $geo_meta ) ) {
						$meta['geo_location'] = $geo_meta;
					}
				}
			}
		}

		$data['event_id'] = $event_id;

		$id = DB::instance()->log_event( $data, $meta );

		if ( $id ) {
			/**
			 * Action fired after the event has been logged successfully.
			 *
			 * @param int    $log_id ID of the log entry
			 * @param array  $data   main data array
			 * @param array  $meta   additional data array
			 * @param object $event  event object with all the event information
			 */
			do_action( 'coreactivity_event_logged', $id, $data, $meta, $event );

			$this->page_events[ $id ] = array(
				'data' => $data,
				'meta' => $meta,
			);
		} else {
			/**
			 * Action fired if the event has not been logged due to an error while adding log entry to the database.
			 *
			 * @param array  $data  main data array
			 * @param array  $meta  additional data array
			 * @param object $event event object with all the event information
			 */
			do_action( 'coreactivity_event_log_failed', $data, $meta, $event );
		}

		return $id;
	}

	public function get( string $name ) : string {
		return $this->cached_data[ $name ] ?? '';
	}

	public function get_current_page_log() : array {
		return $this->page_events;
	}

	public function valid_request_contexts() : array {
		return $this->request_contexts;
	}

	public function valid_request_methods() : array {
		return $this->request_methods;
	}

	private function get_user_agent() : string {
		if ( coreactivity_settings()->get( 'log_if_available_user_agent' ) && isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
			return Sanitize::basic( trim( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		}

		return '';
	}

	private function get_referer() : string {
		if ( coreactivity_settings()->get( 'log_if_available_referer' ) ) {
			$referer = wp_get_referer();

			return $referer === false ? '' : $referer;
		}

		return '';
	}

	private function get_request_method() : string {
		$method = isset( $_SERVER['REQUEST_METHOD'] ) ? strtoupper( sanitize_key( $_SERVER['REQUEST_METHOD'] ) ) : 'GET'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		return in_array( $method, $this->request_methods ) ? $method : '';
	}

	private function prepare_data( array $data = array() ) : array {
		if ( ! isset( $data['blog_id'] ) ) {
			$data['blog_id'] = get_current_blog_id();
		}

		if ( ! isset( $data['logged'] ) ) {
			$data['logged'] = DateTime::instance()->mysql_date();
		}

		if ( ! isset( $data['ip'] ) ) {
			$data['ip'] = $this->cached_data['ip'];
		}

		if ( ! isset( $data['context'] ) ) {
			$data['context'] = $this->cached_data['context'];
		}

		if ( ! isset( $data['method'] ) ) {
			$data['method'] = $this->cached_data['method'];
		}

		if ( ! isset( $data['protocol'] ) ) {
			$data['protocol'] = $this->cached_data['protocol'];
		}

		if ( ! isset( $data['request'] ) ) {
			$data['request'] = $this->cached_data['request'];
		}

		if ( $this->cached_data['anon'] ) {
			$data['user_id'] = 0;
		} else {
			if ( ! isset( $data['user_id'] ) ) {
				$data['user_id'] = get_current_user_id();
			}
		}

		return $data;
	}

	private function prepare_meta( array $meta = array() ) : array {
		if ( ! isset( $meta['user_agent'] ) ) {
			if ( ! empty( $this->cached_data['ua'] ) ) {
				$meta['user_agent'] = $this->cached_data['ua'];
			}
		}

		if ( ! isset( $meta['referer'] ) ) {
			if ( ! empty( $this->cached_data['referer'] ) ) {
				$meta['referer'] = $this->cached_data['referer'];
			}
		}

		if ( ! isset( $meta['ajax_action'] ) ) {
			if ( $this->cached_data['context'] === 'AJAX' && isset( $_REQUEST['action'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
				$meta['ajax_action'] = sanitize_text_field( wp_unslash( $_REQUEST['action'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped,WordPress.Security.NonceVerification
			}
		}

		return $meta;
	}
}
