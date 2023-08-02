<?php

namespace Dev4Press\Plugin\CoreActivity\Components;

use Dev4Press\Plugin\CoreActivity\Base\Component;
use Dev4Press\v43\Core\Quick\Request;
use WP_Error;
use WP_User;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class User extends Component {
	protected $plugin = 'coreactivity';
	protected $name = 'user';
	protected $object_type = 'user';
	protected $icon = 'ui-user';
	protected $scope = 'both';

	protected $monitor = array(
		'first_name',
		'last_name',
		'nickname',
		'description',
		'rich_editing',
		'syntax_highlighting',
		'comment_shortcuts',
		'admin_color',
		'show_admin_bar_front',
		'locale'
	);
	protected $storage = array();
	protected $user_login = '';

	public function tracking() {
		add_filter( 'authenticate', array( $this, 'init_authenticate' ), 10, 3 );

		if ( $this->is_active( 'login' ) ) {
			add_action( 'wp_login', array( $this, 'event_login' ), 10, 2 );
		}

		if ( $this->is_active( 'logout' ) ) {
			add_action( 'wp_logout', array( $this, 'event_logout' ) );
		}

		if ( $this->is_active( 'failed-login-cookie' ) ) {
			add_action( 'auth_cookie_malformed', array( $this, 'event_failed_login_cookie_malformed' ), 10, 2 );
			add_action( 'auth_cookie_bad_hash', array( $this, 'event_failed_login_cookie_bad_hash' ) );
			add_action( 'auth_cookie_bad_username', array( $this, 'event_failed_login_cookie_bad_username' ) );
		}

		if ( $this->is_active( 'failed-login' ) ) {
			add_action( 'wp_login_failed', array( $this, 'event_failed_login' ), 10, 2 );
		}

		if ( Request::is_post() && $this->are_active( array( 'password-reset-request', 'password-reset-request-invalid' ) ) ) {
			add_action( 'login_form_lostpassword', array( $this, 'prepare_form_lostpassword' ) );
		}

		if ( $this->is_active( 'password-reset' ) ) {
			add_action( 'login_form_resetpass', array( $this, 'prepare_form_resetpass' ) );
		}

		if ( $this->is_active( 'deleted' ) ) {
			add_action( 'deleted_user', array( $this, 'event_deleted_user' ), 10, 3 );
		}

		if ( $this->is_active( 'role-changed' ) ) {
			add_action( 'set_user_role', array( $this, 'event_set_user_role' ), 10, 3 );
		}

		if ( $this->are_active( array( 'edited-password', 'edited-url', 'edited-email', 'edited-display-name' ) ) ) {
			add_filter( 'wp_pre_insert_user_data', array( $this, 'event_insert_user_data' ), 10, 4 );
		}

		if ( $this->is_active( 'edited-meta-data' ) ) {
			add_filter( 'insert_user_meta', array( $this, 'event_insert_user_meta' ), 10, 4 );
		}

		if ( $this->is_active( 'user-signup' ) ) {
			add_action( 'after_signup_user', array( $this, 'event_after_signup_user' ), 10, 4 );
		}

		if ( $this->is_active( 'failed-user-signup' ) ) {
			add_filter( 'wpmu_validate_user_signup', array( $this, 'event_validate_user_signup' ) );
		}

		if ( $this->is_active( 'activate-user' ) ) {
			add_action( 'wpmu_activate_user', array( $this, 'event_activate_user' ) );
		}

		if ( $this->is_active( 'registered' ) ) {
			add_action( 'user_register', array( $this, 'event_user_register' ) );
		}
	}

	public function init() {
		/**
		 * Filter the list of meta fields for Users to monitor and log if changed. Considering that a lot of user meta fields in WordPress are for things that often change,
		 * like settings for a user, logging every change can be counterproductive, and it is better to have the list of fields to monitor and log.
		 *
		 * @param array $monitor_fields name of the meta fields to monitor, by default is array with common WordPress user meta fields.
		 *
		 * @return array array with names of meta fields to monitor and log changes.
		 */
		$this->monitor = apply_filters( 'coreactivity_user_meta_fields_to_monitor', $this->monitor );
	}

	public function label() : string {
		return __( "Users", "coreactivity" );
	}

	protected function get_events() : array {
		return array(
			'login'                          => array( 'label' => __( "Login", "coreactivity" ) ),
			'logout'                         => array( 'label' => __( "Logout", "coreactivity" ) ),
			'failed-login'                   => array( 'label' => __( "Failed Login", "coreactivity" ), 'is_security' => true ),
			'failed-login-cookie'            => array( 'label' => __( "Failed Login Cookie", "coreactivity" ), 'is_security' => true ),
			'password-reset'                 => array( 'label' => __( "Password Reset", "coreactivity" ), 'is_security' => true ),
			'password-reset-request-invalid' => array( 'label' => __( "Request Password Reset Invalid", "coreactivity" ), 'is_security' => true ),
			'password-reset-request'         => array( 'label' => __( "Request Password Reset", "coreactivity" ) ),
			'registered'                     => array( 'label' => __( "Registered", "coreactivity" ) ),
			'deleted'                        => array( 'label' => __( "User Deleted", "coreactivity" ) ),
			'role-changed'                   => array( 'label' => __( "User Role Changed", "coreactivity" ) ),
			'edited-password'                => array( 'label' => __( "Edited Password", "coreactivity" ) ),
			'edited-email'                   => array( 'label' => __( "Edited Email", "coreactivity" ) ),
			'edited-url'                     => array( 'label' => __( "Edited URL", "coreactivity" ) ),
			'edited-display-name'            => array( 'label' => __( "Edited Display Name", "coreactivity" ) ),
			'edited-meta-data'               => array( 'label' => __( "Edited Meta", "coreactivity" ) ),
			'user-signup'                    => array( 'label' => __( "User Signup", "coreactivity" ), 'scope' => 'network' ),
			'failed-user-signup'             => array( 'label' => __( "Failed User Signup", "coreactivity" ), 'scope' => 'network' ),
			'activate-user'                  => array( 'label' => __( "Activate User", "coreactivity" ), 'scope' => 'network' )
		);
	}

	public function init_authenticate( $user, $username, $password ) {
		$this->storage[ 'username' ] = $username;
		$this->storage[ 'password' ] = $password;

		return $user;
	}

	public function prepare_form_lostpassword() {
		$this->user_login = $_POST[ 'user_login' ] ?? '';
		$this->user_login = trim( wp_unslash( $this->user_login ) );

		add_action( 'lostpassword_post', array( $this, 'event_lostpassword_post' ), 10, 2 );
	}

	public function prepare_form_resetpass() {
		add_action( 'after_password_reset', array( $this, 'event_after_password_reset' ) );
	}

	public function event_login( $username, $user = null ) {
		if ( is_null( $user ) ) {
			$user = get_user_by( 'login', $username );
		}

		if ( $user !== false ) {
			$this->log( 'login', array(
				'user_id'   => $user->ID,
				'object_id' => $user->ID
			), array( 'username' => $user->user_login, 'email' => $user->user_email ) );
		}
	}

	public function event_logout( $user_id ) {
		$user = get_user_by( 'id', $user_id );

		if ( $user !== false ) {
			$this->log( 'logout', array(
				'user_id'   => $user->ID,
				'object_id' => $user->ID
			), array( 'username' => $user->user_login, 'email' => $user->user_email ) );
		}
	}

	public function event_failed_login_cookie_malformed( $cookie, $scheme ) {
		if ( empty( $cookie ) ) {
			return;
		}

		$this->log( 'failed-login-cookie', array( 'user_id' => 0 ), array(
			'reason' => 'malformed',
			'scheme' => $scheme,
			'cookie' => $cookie
		) );

		remove_action( 'auth_cookie_malformed', array( $this, 'event_failed_login_cookie_malformed' ), 10 );
	}

	public function event_failed_login_cookie_bad_hash( $cookie_elements ) {
		$this->log( 'failed-login-cookie', array( 'user_id' => 0 ), array(
			'reason' => 'bad_hash',
			'cookie' => $cookie_elements
		) );

		remove_action( 'auth_cookie_bad_hash', array( $this, 'event_failed_login_cookie_bad_hash' ) );
	}

	public function event_failed_login_cookie_bad_username( $cookie_elements ) {
		$this->log( 'failed-login-cookie', array( 'user_id' => 0 ), array(
			'reason' => 'bad_username',
			'cookie' => $cookie_elements
		) );

		remove_action( 'auth_cookie_bad_username', array( $this, 'event_failed_login_cookie_bad_username' ) );
	}

	public function event_failed_login( $username, $error ) {
		$user    = get_user_by( 'login', $username );
		$user_id = $user->ID ?? 0;

		if ( $user_id == 0 ) {
			$user    = get_user_by( 'email', $username );
			$user_id = $user->ID ?? 0;
		}

		$this->log( 'failed-login', array(
			'object_id' => $user_id
		), array(
			'login'    => $username,
			'password' => $this->storage[ 'password' ]
		) );
	}

	public function event_lostpassword_post( $errors, $user_data ) {
		remove_action( 'lostpassword_post', array( $this, 'event_lostpassword_post' ), 10 );

		if ( $errors->has_errors() || ! ( $user_data instanceof WP_User ) ) {
			if ( $this->is_active( 'password-reset-request-invalid' ) ) {
				$data = array();

				if ( $user_data instanceof WP_User ) {
					$data = array( 'object_id' => $user_data->ID );
					$meta = array( 'error' => strip_tags( $errors->get_error_message() ) );
				} else {
					$meta = array( 'error' => __( "There is no account with that username or email address.", "coreactivity" ) );
				}

				$meta[ 'login' ] = $this->user_login;

				$this->log( 'password-reset-request-invalid', $data, $meta );
			}
		} else {
			if ( $this->is_active( 'password-reset-request' ) ) {
				$this->log( 'password-reset-request', array(
					'object_id' => $user_data->ID
				), array(
					'login' => $this->user_login
				) );
			}
		}
	}

	public function event_after_password_reset( $user ) {
		remove_action( 'after_password_reset', array( $this, 'event_after_password_reset' ) );

		$this->log( 'password-reset', array(
			'object_id' => $user->ID
		) );
	}

	public function event_deleted_user( $id, $reassign, $user ) {
		$this->log( 'deleted', array(
			'object_id' => $id
		), array(
			'user_email'          => $user->user_email,
			'user_login'          => $user->user_login,
			'user_roles'          => $user->roles,
			'reassign_to_user_id' => $reassign
		) );
	}

	public function event_set_user_role( $user_id, $role, $old_roles ) {
		$this->log( 'role-changed', array(
			'object_id' => $user_id
		), array(
			'role'           => $role,
			'previous_roles' => $old_roles
		) );
	}

	public function event_insert_user_data( $data, $update, $user_id, $userdata ) {
		if ( $update ) {
			$user = get_user_by( 'id', $user_id );

			if ( isset( $data[ 'user_pass' ] ) && $user->user_pass != $data[ 'user_pass' ] ) {
				$this->log( 'edited-password', array(
					'object_id' => $user_id
				) );
			}

			if ( isset( $data[ 'user_email' ] ) && $user->user_email != $data[ 'user_email' ] ) {
				$this->log( 'edited-email', array(
					'object_id' => $user_id
				), array(
					'old_email' => $user->user_email,
					'new_email' => $data[ 'user_email' ]
				) );
			}

			if ( isset( $data[ 'user_url' ] ) && $user->user_url != $data[ 'user_url' ] ) {
				$this->log( 'edited-url', array(
					'object_id' => $user_id
				), array(
					'old_url' => $user->user_url,
					'new_url' => $data[ 'user_url' ]
				) );
			}

			if ( isset( $data[ 'display_name' ] ) && $user->display_name != $data[ 'display_name' ] ) {
				$this->log( 'edited-display-name', array(
					'object_id' => $user_id
				), array(
					'old_display_name' => $user->display_name,
					'new_display_name' => $data[ 'display_name' ]
				) );
			}
		}

		return $data;
	}

	public function event_insert_user_meta( $meta, $user, $update, $userdata ) {
		if ( $update ) {
			$changed = array();

			foreach ( $this->monitor as $key ) {
				if ( $meta[ $key ] != $user->get( $key ) ) {
					$changed[] = array(
						'old_' . $key => $user->get( $key ),
						'new_' . $key => $meta[ $key ]
					);
				}
			}

			foreach ( wp_get_user_contact_methods( $user ) as $key => $value ) {
				if ( isset( $userdata[ $key ] ) && $userdata[ $key ] != $user->get( $key ) ) {
					$changed[] = array(
						'old_' . $key => $user->get( $key ),
						'new_' . $key => $userdata[ $key ]
					);
				}
			}

			if ( ! empty( $changed ) ) {
				$this->log( 'edited-meta-data', array(
					'object_id' => $user->ID
				), $changed );
			}
		}

		return $meta;
	}

	public function event_after_signup_user( $user, $user_email, $key, $meta ) {
		$this->log( 'user-signup', array( 'blog_id' => 0 ), array(
			'user_login' => $user,
			'user_email' => $user_email,
			'signup_key' => $key
		) );
	}

	public function event_validate_user_signup( $results ) {
		if ( isset( $results[ 'errors' ] ) && $results[ 'errors' ] instanceof WP_Error && $results[ 'errors' ]->has_errors() ) {
			$this->log( 'failed-user-signup', array( 'blog_id' => 0 ), array(
				'user_login'          => $results[ 'user_name' ],
				'requested_user_name' => $results[ 'orig_username' ],
				'user_email'          => $results[ 'user_email' ],
				'errors'              => $results[ 'errors' ]->get_error_messages()
			) );
		}

		return $results;
	}

	public function event_activate_user( $user_id ) {
		$this->log( 'activate-user', array( 'blog_id' => 0, 'object_id' => $user_id ) );
	}

	public function event_user_register( $user_id ) {
		$this->log( 'registered', array( 'object_id' => $user_id ) );
	}
}
