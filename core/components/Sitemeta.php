<?php

namespace Dev4Press\Plugin\CoreActivity\Components;

use Dev4Press\Plugin\CoreActivity\Base\Component;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Sitemeta extends Component {
	protected $plugin = 'coreactivity';
	protected $name = 'sitemeta';
	protected $icon = 'ui-sliders-base';
	protected $object_type = 'sitemeta';
	protected $scope = 'network';

	protected $exceptions = array();
	protected $skip = array();

	protected $monitor = array(
		'site_name',
		'admin_email',
		'admin_user_id',
		'registration',
		'upload_filetypes',
		'blog_upload_space',
		'fileupload_maxk',
		'site_admins',
		'allowedthemes',
		'illegal_names',
		'wpmu_upgrade_site',
		'welcome_email',
		'first_post',
		'siteurl',
		'add_new_users',
		'upload_space_check_disabled',
		'subdomain_install',
		'ms_files_rewriting',
		'user_count',
		'initial_db_version',
		'active_sitewide_plugins',
		'WPLANG',
	);
	protected $transient_value;

	public function is_available() : bool {
		return is_multisite();
	}

	public function init() {
		$this->exceptions      = coreactivity_settings()->get( 'exceptions_sitemeta_list' );
		$this->transient_value = coreactivity_settings()->get( 'log_transient_value' );
	}

	public function tracking() {
		if ( $this->is_active( 'core-sitemeta-edited' ) || $this->is_active( 'sitemeta-edited' ) ) {
			add_action( 'update_site_option', array( $this, 'event_updated_option' ), 10, 3 );
		}

		if ( $this->is_active( 'core-sitemeta-deleted' ) || $this->is_active( 'sitemeta-deleted' ) ) {
			add_action( 'delete_site_option', array( $this, 'event_deleted_option' ) );
		}

		if ( $this->is_active( 'sitemeta-added' ) ) {
			add_action( 'add_site_option', array( $this, 'event_added_option' ), 10, 2 );
		}

		if ( $this->is_active( 'transient-set' ) ) {
			add_action( 'setted_site_transient', array( $this, 'event_set_transient' ), 10, 3 );
		}

		if ( $this->is_active( 'transient-deleted' ) ) {
			add_action( 'deleted_site_transient', array( $this, 'event_deleted_transient' ) );
		}
	}

	public function label() : string {
		return __( 'Sitemeta', 'coreactivity' );
	}

	protected function prepare_data_for_log( string $event, array $data = array() ) : array {
		$data['blog_id'] = 0;

		return parent::prepare_data_for_log( $event, $data );
	}

	protected function get_events() : array {
		return array(
			'core-sitemeta-edited'  => array(
				'label' => __( 'Core Sitemeta Changed', 'coreactivity' ),
			),
			'core-sitemeta-deleted' => array(
				'label' => __( 'Core Sitemeta Deleted', 'coreactivity' ),
			),
			'sitemeta-added'        => array(
				'label' => __( 'Sitemeta Added', 'coreactivity' ),
			),
			'sitemeta-edited'       => array(
				'label' => __( 'Sitemeta Changed', 'coreactivity' ),
			),
			'sitemeta-deleted'      => array(
				'label' => __( 'Sitemeta Deleted', 'coreactivity' ),
			),
			'transient-set'         => array(
				'label'  => __( 'Site Transient Set', 'coreactivity' ),
				'status' => 'inactive',
			),
			'transient-deleted'     => array(
				'label'  => __( 'Site Transient Deleted', 'coreactivity' ),
				'status' => 'inactive',
			),
		);
	}

	public function event_updated_option( $option, $old_value, $value ) {
		if ( $this->is_transient( $option ) || $this->is_skippable( $option ) || $this->is_exception( $option ) ) {
			return;
		}

		$event = in_array( $option, $this->monitor ) ? 'core-sitemeta-edited' : 'sitemeta-edited';

		if ( $this->is_active( $event ) ) {
			$this->log( $event, array(
				'object_name' => $option,
			), array(
				'old' => $old_value,
				'new' => $value,
			) );
		}
	}

	public function event_deleted_option( $option ) {
		if ( $this->is_transient( $option ) || $this->is_skippable( $option ) || $this->is_exception( $option ) ) {
			return;
		}

		$event = in_array( $option, $this->monitor ) ? 'core-sitemeta-deleted' : 'sitemeta-deleted';

		if ( $this->is_active( $event ) ) {
			$this->log( $event, array(
				'object_name' => $option,
			) );
		}
	}

	public function event_added_option( $option, $value ) {
		if ( $this->is_transient( $option ) || $this->is_skippable( $option ) || $this->is_exception( $option ) ) {
			return;
		}

		$this->log( 'sitemeta-added', array(
			'object_name' => $option,
		), array(
			'value' => $value,
		) );
	}

	public function event_set_transient( $transient, $value, $expiration ) {
		$this->log( 'transient-set', array(
			'object_type' => 'transient',
			'object_name' => $transient,
		), array(
			'value'      => $this->transient_value ? $value : '',
			'expiration' => $expiration,
		) );
	}

	public function event_deleted_transient( $transient ) {
		$this->log( 'transient-deleted', array(
			'object_type' => 'transient',
			'object_name' => $transient,
		) );
	}

	private function is_transient( $option ) : bool {
		return substr( $option, 0, 11 ) == '_transient_' || substr( $option, 0, 16 ) == '_site_transient_';
	}

	private function is_exception( $option ) : bool {
		return ! empty( $this->exceptions ) && in_array( $option, $this->exceptions );
	}

	private function is_skippable( $option ) : bool {
		return ! empty( $this->skip ) && in_array( $option, $this->skip );
	}
}
