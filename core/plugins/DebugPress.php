<?php

namespace Dev4Press\Plugin\CoreActivity\Plugins;

use Dev4Press\Plugin\CoreActivity\Base\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class DebugPress extends Plugin {
	protected $plugin = 'coreactivity';
	protected $name = 'debugpress';
	protected $icon = 'plugin-debugpress';
	protected $object_type = 'phperror';
	protected $plugin_file = 'debugpress/debugpress.php';
	protected $skip_duplicates = true;
	protected $skip_duplicates_request = false;

	public function init() {
		add_filter( 'coreactivity_debugpress_ajax_call_log_active', array( $this, 'skip_heartbeat' ), 10, 3 );
	}

	public function registered_object_types( array $object_types ) : array {
		$object_types['phperror'] = __( 'PHP Error', 'coreactivity' );

		return $object_types;
	}

	public function tracking() {
		if ( $this->is_active( 'php-error' ) ) {
			add_action( 'debugpress-tracker-error-logged', array( $this, 'event_php_error' ) );
		}

		if ( $this->is_active( 'doing-it-wrong' ) ) {
			add_action( 'debugpress-tracker-doing-it-wrong-logged', array( $this, 'event_doing_it_wrong' ) );
		}

		if ( $this->is_active( 'deprecated-function' ) ) {
			add_action( 'debugpress-tracker-deprecated-function-logged', array( $this, 'event_deprecated_function' ) );
		}

		if ( $this->is_active( 'deprecated-file' ) ) {
			add_action( 'debugpress-tracker-deprecated-file-logged', array( $this, 'event_deprecated_file' ) );
		}

		if ( $this->is_active( 'deprecated-argument' ) ) {
			add_action( 'debugpress-tracker-deprecated-argument-logged', array( $this, 'event_deprecated_argument' ) );
		}

		if ( $this->is_active( 'deprecated-constructor' ) ) {
			add_action( 'debugpress-tracker-deprecated-constructor-logged', array( $this, 'event_deprecated_constructor' ) );
		}

		if ( $this->is_active( 'deprecated-hook-run' ) ) {
			add_action( 'debugpress-tracker-deprecated-hook-run-logged', array( $this, 'event_deprecated_hook_run' ) );
		}

		if ( $this->is_active( 'admin-ajax-call' ) ) {
			add_action( 'debugpress-tracker-admin-ajax-logged', array( $this, 'event_admin_ajax_call' ) );
		}

		if ( $this->is_active( 'http-api-call' ) ) {
			add_action( 'debugpress-tracker-http-request-call-logged', array( $this, 'event_http_api_call' ) );
		}
	}

	public function label() : string {
		return __( 'DebugPress', 'coreactivity' );
	}

	protected function get_events() : array {
		return array(
			'php-error'              => array(
				'label'  => __( 'PHP Error', 'coreactivity' ),
				'status' => 'inactive',
			),
			'doing-it-wrong'         => array(
				'label' => __( 'Doing It Wrong', 'coreactivity' ),
			),
			'deprecated-function'    => array(
				'label' => __( 'Deprecated Function', 'coreactivity' ),
			),
			'deprecated-file'        => array(
				'label' => __( 'Deprecated File', 'coreactivity' ),
			),
			'deprecated-argument'    => array(
				'label' => __( 'Deprecated Argument', 'coreactivity' ),
			),
			'deprecated-constructor' => array(
				'label' => __( 'Deprecated Constructor', 'coreactivity' ),
			),
			'deprecated-hook-run'    => array(
				'label' => __( 'Deprecated Hook Run', 'coreactivity' ),
			),
			'admin-ajax-call'        => array(
				'label'   => __( 'Admin AJAX Call', 'coreactivity' ),
				'status'  => 'inactive',
				'version' => '1.3',
			),
			'http-api-call'          => array(
				'label'   => __( 'HTTP API Call', 'coreactivity' ),
				'status'  => 'inactive',
				'version' => '1.3',
			),
		);
	}

	public function logs_meta_column_keys( array $meta_column_keys ) : array {
		$meta_column_keys[ $this->code() ] = array(
			'-' => array(
				'errstr',
			),
		);

		return $meta_column_keys;
	}

	public function event_php_error( $error ) {
		if ( isset( $error['errno'] ) && ! empty( $error['caller'] ) ) {
			$this->log( 'php-error', array(
				'object_id' => $error['errno'],
			), $error );
		}
	}

	public function event_doing_it_wrong( $error ) {
		$this->log( 'doing-it-wrong', array(), $error );
	}

	public function event_deprecated_function( $error ) {
		$this->log( 'deprecated-function', array(), $error );
	}

	public function event_deprecated_file( $error ) {
		$this->log( 'deprecated-file', array(), $error );
	}

	public function event_deprecated_argument( $error ) {
		$this->log( 'deprecated-argument', array(), $error );
	}

	public function event_deprecated_constructor( $error ) {
		$this->log( 'deprecated-constructor', array(), $error );
	}

	public function event_deprecated_hook_run( $error ) {
		$this->log( 'deprecated-hook-run', array(), $error );
	}

	public function event_admin_ajax_call( $call ) {
		$ajax_call = $call['ajax-action-call'];

		if ( apply_filters( 'coreactivity_debugpress_ajax_call_log_active', $ajax_call !== 'coreactivity_live_logs', $ajax_call, $call ) ) {
			$this->log( 'admin-ajax-call', array(), $call );
		}
	}

	public function event_http_api_call( $call ) {
		$this->log( 'http-api-call', array(), array(
			'call_url'       => $call['info']['url'] ?? '',
			'call_method'    => $call['args']['method'] ?? '',
			'call_transport' => $call['transport'] ?? '',
			'call_trace'     => $call['trace'],
		) );
	}

	public function skip_heartbeat( $log, $ajax_call, $call ) {
		if ( $ajax_call == 'heartbeat' ) {
			$log = false;
		}

		return $log;
	}
}
