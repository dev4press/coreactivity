<?php

namespace Dev4Press\Plugin\CoreActivity\Plugins;

use Dev4Press\Plugin\CoreActivity\Base\Component;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class UserSwitching extends Component {
	protected $plugin = 'coreactivity';
	protected $name = 'user-switching';
	protected $icon = 'ui-user-group';
	protected $category = 'plugin';

	public function tracking() {
		if ( $this->is_active( 'switch-to-user' ) ) {
			add_action( 'switch_to_user', array( $this, 'event_switch_to_user' ), 10, 2 );
		}

		if ( $this->is_active( 'switch-back-user' ) || $this->is_active( 'switch-back' ) ) {
			add_action( 'switch_back_user', array( $this, 'event_switch_back_user' ), 10, 2 );
		}

		if ( $this->is_active( 'switch-off-user' ) ) {
			add_action( 'switch_off_user', array( $this, 'event_switch_off_user' ) );
		}
	}

	public function label() : string {
		return __( "User Switching", "coreactivity" );
	}

	protected function get_events() : array {
		return array(
			'switch-to-user'   => array( 'label' => __( "Switched to User", "coreactivity" ) ),
			'switch-off-user'  => array( 'label' => __( "Switched Off", "coreactivity" ) ),
			'switch-back-user' => array( 'label' => __( "Switched Back from User", "coreactivity" ) ),
			'switch-back'      => array( 'label' => __( "Switched Back", "coreactivity" ) )
		);
	}

	public function event_switch_to_user( $user_id, $old_user_id ) {
		$user = get_user_by( 'id', $user_id );

		$this->log( 'switch-to-user',
			array(
				'user_id'     => $old_user_id,
				'object_type' => 'user',
				'object_id'   => $user_id
			), array(
				'switched_to_user' => $user->user_login ?? ''
			)
		);
	}

	public function event_switch_back_user( $user_id, $old_user_id ) {
		if ( $old_user_id === false ) {
			if ( $this->is_active( 'switch-back' ) ) {
				$this->log( 'switch-back',
					array(
						'user_id'     => $user_id,
						'object_type' => 'user',
						'object_id'   => $user_id
					)
				);
			}
		} else {
			if ( $this->is_active( 'switch-back-user' ) ) {
				$user = get_user_by( 'id', $old_user_id );

				$this->log( 'switch-back-user',
					array(
						'user_id'     => $old_user_id,
						'object_type' => 'user',
						'object_id'   => $user_id
					), array(
						'switched_from_user' => $user->user_login ?? ''
					)
				);
			}
		}
	}

	public function event_switch_off_user( $user_id ) {
		$this->log( 'switch-off-user',
			array(
				'user_id'     => $user_id,
				'object_type' => 'user',
				'object_id'   => $user_id
			)
		);
	}
}
