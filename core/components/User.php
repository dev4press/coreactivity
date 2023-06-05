<?php

namespace Dev4Press\Plugin\CoreActivity\Components;

use Dev4Press\Plugin\CoreActivity\Base\Component;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class User extends Component {
	protected $plugin = 'coreactivity';
	protected $name = 'user';
	protected $object_type = 'user';
	protected $scope = 'both';

	public function tracking() {
		if ( $this->is_active( 'login' ) ) {
			add_action( 'wp_login', array( $this, 'event_login' ), 10, 2 );
		}

		if ( $this->is_active( 'logout' ) ) {
			add_action( 'wp_logout', array( $this, 'event_logout' ) );
		}
	}

	public function label() : string {
		return __( "Users", "coreactivity" );
	}

	protected function get_events() : array {
		return array(
			'login'  => array( 'label' => __( "Login", "coreactivity" ) ),
			'logout' => array( 'label' => __( "Logout", "coreactivity" ) )
		);
	}

	public function event_login( $username, $user = null ) {
		if ( is_null( $user ) ) {
			$user = get_user_by( 'login', $username );
		}

		if ( $user !== false ) {
			$this->log( 'login', array( 'user_id' => $user->ID, 'object_id' => $user->ID ), array( 'username' => $user->user_login, 'email' => $user->user_email ) );
		}
	}

	public function event_logout( $user_id ) {
		$user = get_user_by( 'id', $user_id );

		if ( $user !== false ) {
			$this->log( 'logout', array( 'user_id' => $user->ID, 'object_id' => $user->ID ), array( 'username' => $user->user_login, 'email' => $user->user_email ) );
		}
	}
}
