<?php

namespace Dev4Press\Plugin\CoreActivity\Log;

use stdClass;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Display {
	public function __construct() {
		add_filter( 'coreactivity_logs_field_render_object_name', array( $this, 'logs_object_name' ), 10, 2 );
	}

	public static function instance() : Display {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new Display();
		}

		return $instance;
	}

	public function logs_object_name( string $render, stdClass $item ) : string {
		if ( empty( $item->object_type ) ) {
			return '/';
		}

		switch ( $item->object_type ) {
			case 'user':
				$render = $this->_display_user( $item );
				break;
			case 'post':
				$render = $this->_display_post( $item );
				break;
		}

		return $render;
	}

	private function _display_user( stdClass $item ) : string {
		$render = '';
		$meta   = array();

		if ( $item->object_id == 0 ) {
			$render = '<span>ID: <strong>0</strong> &middot; ' . esc_html__( "Invalid", "coreactivity" ) . '</span>';
		} else {
			$user = get_user_by( 'id', $item->object_id );

			$render .= '<span>ID: <strong>' . $item->object_id . '</strong> &middot; ';

			if ( ! $user ) {
				$render .= esc_html__( "Not Found", "coreactivity" );
			} else {
				$render .= $user->display_name;
			}
		}

		foreach ( array( 'login', 'password', 'username', 'email' ) as $meta_key ) {
			if ( isset( $item->meta[ $meta_key ] ) ) {
				$meta[] = '<strong>' . $meta_key . '</strong>: ' . $item->meta[ $meta_key ];
			}
		}

		if ( ! empty( $meta ) ) {
			$render .= '<br/>' . join( ' &middot; ', $meta );
		}

		return $render;
	}

	private function _display_post( stdClass $item ) : string {
		$render = '';

		return $render;
	}
}
