<?php

namespace Dev4Press\Plugin\CoreActivity\Basic;

use Dev4Press\v43\Core\Plugins\Settings as BaseSettings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Settings extends BaseSettings {
	public $base = 'coreactivity';
	public $scope = 'network';
	public $has_db = true;

	public $settings = array(
		'core'     => array(
			'installed'  => '',
			'updated'    => '',
			'db_version' => 0
		),
		'settings' => array(
			'log_if_available_user_agent'  => true,
			'log_if_available_referer'     => false,
			'log_if_available_description' => false,
			'logs_live_updates'            => true,
			'display_columns_simplified'   => false,
			'display_ip_country_flag'      => false,
			'display_user_avatar'          => false,
			'display_request_column'       => false,
			'auto_cleanup_active'          => true,
			'auto_cleanup_period'          => 24
		)
	);

	protected function constructor() {
		$this->info = new Information();

		add_action( 'coreactivity_load_settings', array( $this, 'init' ), 2 );
	}

	protected function _install_db() {
		return InstallDB::instance();
	}
}
