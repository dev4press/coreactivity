<?php

namespace Dev4Press\Plugin\CoreActivity\Basic;

use Dev4Press\v44\Core\Plugins\Settings as BaseSettings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Settings extends BaseSettings {
	public $base = 'coreactivity';
	public $scope = 'network';
	public $has_db = true;

	public $settings = array(
		'core'          => array(
			'installed'             => '',
			'updated'               => '',
			'db_version'            => 0,
			'instant_timestamp'     => 0,
			'instant_datetime'      => '',
			'ip2location_timestamp' => 0,
			'ip2location_db'        => '',
		),
		'settings'      => array(
			'show_setup_wizard'                       => true,
			'admin_bar_integration'                   => true,
			'main_events_log_switch'                  => true,
			'notice_if_logging_is_disabled'           => false,
			'log_transient_value'                     => false,
			'log_if_available_user_agent'             => true,
			'log_if_available_referer'                => false,
			'log_if_available_description'            => false,
			'log_if_available_expanded_location'      => false,
			'log_country_code'                        => false,
			'geolocation_method'                      => 'online',
			'geolocation_ip2location_token'           => '',
			'geolocation_ip2location_db'              => 'DB3LITEBINIPV6',
			'logs_live_updates'                       => true,
			'display_columns_simplified'              => false,
			'display_ip_country_flag'                 => false,
			'display_user_avatar'                     => false,
			'display_request_column'                  => false,
			'display_protocol_column'                 => false,
			'display_meta_column'                     => false,
			'auto_cleanup_active'                     => true,
			'auto_cleanup_period'                     => 24,
			'exceptions_option_action_scheduler_lock' => true,
			'exceptions_plugin_list'                  => array(),
			'exceptions_theme_list'                   => array(),
			'exceptions_cron_list'                    => array(),
			'exceptions_notification_list'            => array(),
			'exceptions_option_list'                  => array(),
			'exceptions_sitemeta_list'                => array(),
			'exceptions_error_file_regex_list'        => array(
				'js\.map$',
				'css\.map$',
			),
		),
		'notifications' => array(
			'daily'                 => false,
			'daily_emails'          => array(),
			'daily_hour'            => 2,
			'daily_skip_log'        => false,
			'weekly'                => false,
			'weekly_emails'         => array(),
			'weekly_hour'           => 2,
			'weekly_day'            => 'D6',
			'weekly_skip_log'       => false,
			'instant'               => false,
			'instant_emails'        => array(),
			'instant_delay_minutes' => 5,
			'instant_skip_log'      => false,
		),
	);

	protected function constructor() {
		$this->info = new Information();

		add_action( 'coreactivity_load_settings', array( $this, 'init' ), 2 );
	}

	protected function _install_db() {
		return InstallDB::instance();
	}

	public function is_in_exception_list( $list, $value ) : bool {
		$option = 'exceptions_' . $list . '_list';
		$values = $this->get( $option );

		return in_array( $value, $values );
	}
}
