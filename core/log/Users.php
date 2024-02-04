<?php

namespace Dev4Press\Plugin\CoreActivity\Log;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Users {
	public function __construct() {
		add_action( 'init', array( $this, 'update_last_user_activity' ) );
		add_action( 'wp_login', array( $this, 'update_last_user_login' ), 10, 2 );
		add_action( 'wp_logout', array( $this, 'update_last_user_logout' ) );
	}

	public static function instance() : Users {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new Users();
		}

		return $instance;
	}

	public function is_current_user_online() : bool {
		return $this->is_user_online();
	}

	public function is_user_online( int $user_id = 0 ) : bool {
		$user_id = $user_id === 0 ? get_current_user_id() : $user_id;

		if ( $user_id > 0 ) {
			$last_activity = $this->get_user_last_activity( $user_id );

			if ( $last_activity + coreactivity_settings()->get( 'users_online_window' ) >= time() ) {
				return true;
			}
		}

		return false;
	}

	public function get_user_last_activity( $user_id = 0 ) : int {
		return absint( get_user_option( 'coreactivity_last_activity', $user_id ) );
	}

	public function get_user_last_login( $user_id = 0 ) : int {
		return absint( get_user_option( 'coreactivity_last_login', $user_id ) );
	}

	public function get_user_last_logout( $user_id = 0 ) : int {
		return absint( get_user_option( 'coreactivity_last_logout', $user_id ) );
	}

	public function get_user_last_log_visit( $user_id = 0 ) : int {
		return absint( get_user_option( 'coreactivity_last_log_visit', $user_id ) );
	}

	public function update_last_user_activity() {
		if ( is_user_logged_in() ) {
			update_user_option( get_current_user_id(), 'coreactivity_last_activity', time(), true );
		}
	}

	public function update_last_user_login( $username, $user = null ) {
		if ( is_null( $user ) ) {
			$user = get_user_by( 'login', $username );
		}

		update_user_option( $user->ID, 'coreactivity_last_login', time(), true );

		$this->add_user_to_logged_in( $user->ID, time() );
	}

	public function update_last_user_logout( $user_id ) {
		update_user_option( $user_id, 'coreactivity_last_logout', time(), true );

		$this->remove_user_from_logged_in( $user_id );
	}

	public function update_last_user_log_visit() {
		if ( is_user_logged_in() ) {
			update_user_option( get_current_user_id(), 'coreactivity_last_log_visit', time(), true );
		}
	}

	private function add_user_to_logged_in( $user_id, $timestamp ) {
		$list = coreactivity_settings()->get( 'users_logged_in', 'storage', array() );

		$list[ $user_id ] = $timestamp;

		coreactivity_settings()->set( 'users_logged_in', $list, 'storage', true );
	}

	private function remove_user_from_logged_in( $user_id ) {
		$list = coreactivity_settings()->get( 'users_logged_in', 'storage', array() );

		if ( isset( $list[ $user_id ] ) ) {
			unset( $list[ $user_id ] );

			coreactivity_settings()->set( 'users_logged_in', $list, 'storage', true );
		}
	}
}
