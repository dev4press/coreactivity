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
	protected $object_type = 'option';
	protected $scope = 'network';

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
		'WPLANG'
	);

	protected $skip = array();

	public function is_available() : bool {
		return is_multisite();
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
	}

	public function label() : string {
		return __( "Sitemeta", "coreactivity" );
	}

	protected function prepare_data_for_log( string $event, array $data = array() ) : array {
		$data[ 'blog_id' ] = 0;

		return parent::prepare_data_for_log( $event, $data );
	}

	protected function get_events() : array {
		return array(
			'core-sitemeta-edited'  => array( 'label' => __( "Core Sitemeta Changed", "coreactivity" ) ),
			'core-sitemeta-deleted' => array( 'label' => __( "Core Sitemeta Deleted", "coreactivity" ) ),
			'sitemeta-added'        => array( 'label' => __( "Sitemeta Added", "coreactivity" ) ),
			'sitemeta-edited'       => array( 'label' => __( "Sitemeta Changed", "coreactivity" ) ),
			'sitemeta-deleted'      => array( 'label' => __( "Sitemeta Deleted", "coreactivity" ) )
		);
	}

	public function event_updated_option( $option, $old_value, $value ) {
		if ( $this->is_transient( $option ) || in_array( $option, $this->skip ) ) {
			return;
		}

		$event = in_array( $option, $this->monitor ) ? 'core-sitemeta-edited' : 'sitemeta-edited';

		if ( $this->is_active( $event ) ) {
			$this->log( $event, array(
				'object_name' => $option
			), array(
				'old' => $old_value,
				'new' => $value
			) );
		}
	}

	public function event_deleted_option( $option ) {
		if ( $this->is_transient( $option ) ) {
			return;
		}

		$event = in_array( $option, $this->monitor ) ? 'core-sitemeta-deleted' : 'sitemeta-deleted';

		if ( $this->is_active( $event ) ) {
			$this->log( $event, array(
				'object_name' => $option
			) );
		}
	}

	public function event_added_option( $option, $value ) {
		if ( $this->is_transient( $option ) ) {
			return;
		}

		$this->log( 'sitemeta-added', array(
			'object_name' => $option
		), array(
			'value' => $value
		) );
	}

	private function is_transient( $option ) : bool {
		return substr( $option, 0, 11 ) == '_transient_' || substr( $option, 0, 16 ) == '_site_transient_';
	}
}