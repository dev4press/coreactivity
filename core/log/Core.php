<?php

namespace Dev4Press\Plugin\CoreActivity\Log;

use Dev4Press\Plugin\CoreActivity\Basic\DB;
use Dev4Press\v42\Core\DateTime;
use Dev4Press\v42\Core\Helpers\IP;
use Dev4Press\v42\Core\Quick\Sanitize;
use Dev4Press\v42\Core\Quick\URL;
use Dev4Press\v42\Core\Scope;
use Dev4Press\v42\WordPress;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Core {
	private $cached_data;

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
		'SEARCH'
	);

	public function __construct() {
		$this->cached_data = array(
			'ip'       => IP::visitor(),
			'ua'       => $this->get_user_agent(),
			'referer'  => $this->get_referer(),
			'method'   => $this->get_request_method(),
			'protocol' => wp_get_server_protocol(),
			'request'  => URL::current_url_request(),
			'context'  => WordPress::instance()->context()
		);

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
		$data = $this->prepare_data( $data );
		$meta = $this->prepare_meta( $meta );

		$data[ 'event_id' ] = $event_id;

		$id = DB::instance()->log_event( $data, $meta );

		if ( $id ) {
			do_action( 'coreactivity_event_logged', $id, $data, $meta );
		} else {
			do_action( 'coreactivity_event_log_failed', $data, $meta );
		}

		return $id;
	}

	private function get_user_agent() : string {
		if ( coreactivity_settings()->get( 'log_if_available_user_agent' ) && isset( $_SERVER[ 'HTTP_USER_AGENT' ] ) ) {
			return Sanitize::basic( trim( $_SERVER[ 'HTTP_USER_AGENT' ] ) );
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
		$method = strtoupper( trim( $_SERVER[ 'REQUEST_METHOD' ] ) );

		return in_array( $method, $this->request_methods ) ? $method : '';
	}

	private function prepare_data( array $data = array() ) : array {
		if ( ! isset( $data[ 'blog_id' ] ) ) {
			$data[ 'blog_id' ] = get_current_blog_id();
		}

		if ( ! isset( $data[ 'user_id' ] ) ) {
			$data[ 'user_id' ] = get_current_user_id();
		}

		if ( ! isset( $data[ 'logged' ] ) ) {
			$data[ 'logged' ] = DateTime::instance()->mysql_date();
		}

		if ( ! isset( $data[ 'ip' ] ) ) {
			$data[ 'ip' ] = $this->cached_data[ 'ip' ];
		}

		if ( ! isset( $data[ 'context' ] ) ) {
			$data[ 'context' ] = $this->cached_data[ 'context' ];
		}

		if ( ! isset( $data[ 'method' ] ) ) {
			$data[ 'method' ] = $this->cached_data[ 'method' ];
		}

		if ( ! isset( $data[ 'protocol' ] ) ) {
			$data[ 'protocol' ] = $this->cached_data[ 'protocol' ];
		}

		if ( ! isset( $data[ 'request' ] ) ) {
			$data[ 'request' ] = $this->cached_data[ 'request' ];
		}

		return $data;
	}

	private function prepare_meta( array $meta = array() ) : array {
		if ( ! isset( $meta[ 'user_agent' ] ) ) {
			if ( ! empty( $this->cached_data[ 'ua' ] ) ) {
				$meta[ 'user_agent' ] = $this->cached_data[ 'ua' ];
			}
		}

		if ( ! isset( $meta[ 'referer' ] ) ) {
			if ( ! empty( $this->cached_data[ 'referer' ] ) ) {
				$meta[ 'referer' ] = $this->cached_data[ 'referer' ];
			}
		}

		return $meta;
	}
}
