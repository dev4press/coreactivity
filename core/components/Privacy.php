<?php

namespace Dev4Press\Plugin\CoreActivity\Components;

use Dev4Press\Plugin\CoreActivity\Base\Component;
use WP_Post;
use WP_User_Request;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Privacy extends Component {
	protected $plugin = 'coreactivity';
	protected $name = 'privacy';
	protected $version = '1.8';
	protected $object_type = 'user_request';
	protected $icon = 'ui-user-secret';

	public function tracking() {
		add_action( 'save_post_user_request', array( $this, 'handle_save_post_user_request' ), 10, 3 );
		add_action( 'user_request_action_confirmed', array( $this, 'handle_user_request_action_confirmed' ) );

		add_action( 'load-export-personal-data.php', array( $this, 'handle_load_export_personal_data' ) );
		add_action( 'load-erase-personal-data.php', array( $this, 'handle_load_erase_personal_data' ) );

		if ( $this->is_active( 'erased-personal-data' ) ) {
			add_action( 'wp_privacy_personal_data_erased', array( $this, 'event_erased_personal_data' ) );
		}

		if ( $this->is_active( 'personal-data-file-created' ) ) {
			add_action( 'wp_privacy_personal_data_export_file_created', array( $this, 'event_personal_data_file_created' ), 10, 5 );
		}
	}

	public function label() : string {
		return __( 'Privacy', 'coreactivity' );
	}

	protected function get_events() : array {
		return array(
			'export-personal-data-requested'       => array(
				'label' => __( 'Requested Export of Personal Data', 'coreactivity' ),
			),
			'export-personal-data-completed'       => array(
				'label' => __( 'Completed Export of Personal Data', 'coreactivity' ),
			),
			'export-personal-data-confirmed'       => array(
				'label' => __( 'Confirmed Export of Personal Data', 'coreactivity' ),
			),
			'export-personal-data-request-deleted' => array(
				'label' => __( 'Deleted Request for Export of Personal Data', 'coreactivity' ),
			),
			'remove-personal-data-requested'       => array(
				'label' => __( 'Requested Removal of Personal Data', 'coreactivity' ),
			),
			'remove-personal-data-confirmed'       => array(
				'label' => __( 'Confirmed Removal of Personal Data', 'coreactivity' ),
			),
			'remove-personal-data-completed'       => array(
				'label' => __( 'Completed Removal of Personal Data', 'coreactivity' ),
			),
			'remove-personal-data-request-deleted' => array(
				'label' => __( 'Deleted Request for Removal of Personal Data', 'coreactivity' ),
			),
			'erased-personal-data'                 => array(
				'label' => __( 'Personal Data Erased', 'coreactivity' ),
			),
			'personal-data-file-created'           => array(
				'label' => __( 'Created Personal Data File', 'coreactivity' ),
			),
		);
	}

	public function event_personal_data_file_created( $archive_pathname, $archive_url, $html_report_pathname, $request_id, $json_report_pathname ) {
		require_once ABSPATH . 'wp-admin/includes/user.php';

		$user_request = wp_get_user_request( $request_id );

		if ( $user_request instanceof WP_User_Request ) {
			$this->log( 'personal-data-file-created', array(
				'object_name' => 'export_personal_data',
			), array(
				'user_id'    => $user_request->user_id,
				'user_email' => $user_request->email,
			) );
		}
	}

	public function event_erased_personal_data( $request_id ) {
		require_once ABSPATH . 'wp-admin/includes/user.php';

		$user_request = wp_get_user_request( $request_id );

		if ( $user_request instanceof WP_User_Request ) {
			$this->log( 'erased-personal-data', array(
				'object_name' => 'remove_personal_data',
			), array(
				'request_id' => $request_id,
				'user_id'    => $user_request->user_id,
				'user_email' => $user_request->email,
			) );
		}
	}

	public function handle_save_post_user_request( $post_id, $post, $update ) {
		if ( ! empty( $post_id ) && $post instanceof WP_Post ) {
			require_once ABSPATH . 'wp-admin/includes/user.php';

			$user_request = wp_get_user_request( $post_id );

			if ( $user_request instanceof WP_User_Request ) {
				if ( $update ) {
					if ( 'remove_personal_data' === $user_request->action_name && 'request-completed' === $user_request->status ) {
						$this->log( 'remove-personal-data-completed', array(
							'object_name' => 'remove_personal_data',
						), array(
							'user_id'    => $user_request->user_id,
							'user_email' => $user_request->email,
						) );
					}
				} else {
					if ( 'remove_personal_data' === $user_request->action_name && 'request-pending' === $user_request->status ) {
						$this->log( 'remove-personal-data-requested', array(
							'object_name' => 'remove_personal_data',
						), array(
							'user_id'    => $user_request->user_id,
							'user_email' => $user_request->email,
						) );
					} else if ( 'export_personal_data' === $user_request->action_name && 'request-pending' && $user_request->status ) {
						$this->log( 'export-personal-data-requested', array(
							'object_name' => 'export_personal_data',
						), array(
							'user_id'    => $user_request->user_id,
							'user_email' => $user_request->email,
						) );
					}
				}
			}
		}
	}

	public function handle_user_request_action_confirmed( $request_id ) {
		require_once ABSPATH . 'wp-admin/includes/user.php';

		$user_request = wp_get_user_request( $request_id );

		if ( $user_request instanceof WP_User_Request ) {
			if ( 'export_personal_data' === $user_request->action_name && 'request-confirmed' === $user_request->status ) {
				$this->log( 'export-personal-data-confirmed', array(
					'object_name' => 'export_personal_data',
				), array(
					'user_id'    => $user_request->user_id,
					'user_email' => $user_request->email,
				) );
			} else if ( 'remove_personal_data' === $user_request->action_name && 'request-confirmed' === $user_request->status ) {
				$this->log( 'remove-personal-data-confirmed', array(
					'object_name' => 'remove_personal_data',
				), array(
					'user_id'    => $user_request->user_id,
					'user_email' => $user_request->email,
				) );
			}
		}
	}

	public function handle_load_export_personal_data() {
		add_action( 'before_delete_post', array( $this, 'event_export_personal_data_request_deleted' ) );
		add_action( 'admin_action_complete', array( $this, 'event_export_personal_data_completed' ) );
	}

	public function handle_load_erase_personal_data() {
		add_action( 'before_delete_post', array( $this, 'event_remove_personal_data_request_deleted' ), 10, 1 );
	}

	public function event_export_personal_data_completed() {
		require_once ABSPATH . 'wp-admin/includes/user.php';

		$request_ids = isset( $_REQUEST['request_id'] ) ? wp_parse_id_list( wp_unslash( $_REQUEST['request_id'] ) ) : array();

		foreach ( $request_ids as $request_id ) {
			$user_request = wp_get_user_request( $request_id );

			if ( $user_request instanceof WP_User_Request ) {
				$this->log( 'export-personal-data-completed', array(
					'object_name' => 'export_personal_data',
				), array(
					'request_id' => $user_request->ID,
					'user_id'    => $user_request->user_id,
					'user_email' => $user_request->email,
				) );
			}
		}
	}

	public function event_export_personal_data_request_deleted( $post_id ) {
		require_once ABSPATH . 'wp-admin/includes/user.php';

		$user_request = wp_get_user_request( $post_id );

		if ( $user_request instanceof WP_User_Request ) {
			$action = wp_unslash( sanitize_key( $_REQUEST['action'] ) ?? '' );

			if ( 'delete' === $action ) {
				$this->log( 'remove-personal-data-request-deleted', array(
					'object_name' => 'remove_personal_data',
				), array(
					'request_id' => $user_request->ID,
					'user_id'    => $user_request->user_id,
					'user_email' => $user_request->email,
				) );
			}
		}
	}

	public function event_remove_personal_data_request_deleted( $post_id ) {
		require_once ABSPATH . 'wp-admin/includes/user.php';

		$user_request = wp_get_user_request( $post_id );

		if ( $user_request instanceof WP_User_Request ) {
			$action = wp_unslash( sanitize_key( $_REQUEST['action'] ) ?? '' );

			if ( 'delete' === $action ) {
				$this->log( 'export-personal-data-request-deleted', array(
					'object_name' => 'export_personal_data',
				), array(
					'request_id' => $user_request->ID,
					'user_id'    => $user_request->user_id,
					'user_email' => $user_request->email,
				) );
			}
		}
	}
}
