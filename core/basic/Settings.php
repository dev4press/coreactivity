<?php

namespace Dev4Press\Plugin\CoreActivity\Basic;

use Dev4Press\v47\Core\Plugins\Settings as BaseSettings;

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
			'ip2location_attempt'   => 0,
			'ip2location_error'     => '',
			'geoip2_timestamp'      => 0,
			'geoip2_db'             => '',
			'geoip2_attempt'        => 0,
			'geoip2_error'          => '',
		),
		'storage'       => array(
			'users_logged_in'   => array(),
			'statistics'        => '',
			'statistics_latest' => '',
		),
		'settings'      => array(
			'show_setup_wizard'                       => true,
			'skip_duplicated'                         => true,
			'users_online_window'                     => 300,
			'admin_bar_integration'                   => true,
			'admin_bar_indicator'                     => true,
			'main_events_log_switch'                  => true,
			'notice_if_logging_is_disabled'           => false,
			'log_transient_value'                     => false,
			'log_if_available_user_agent'             => true,
			'log_device_detection_data'               => false,
			'log_device_detection_filter'             => false,
			'log_if_available_referer'                => false,
			'log_if_available_description'            => false,
			'log_if_available_expanded_location'      => false,
			'log_country_code'                        => false,
			'geolocation_method'                      => 'online',
			'geolocation_ip2location_token'           => '',
			'geolocation_ip2location_db'              => 'DB3LITEBINIPV6',
			'geolocation_geoip2_license'              => '',
			'geolocation_geoip2_db'                   => 'GeoLite2-City',
			'logs_live_updates'                       => true,
			'display_columns_simplified'              => false,
			'display_ip_country_flag'                 => false,
			'display_user_avatar'                     => false,
			'display_request_column'                  => false,
			'display_detection_column'                => false,
			'display_protocol_column'                 => false,
			'display_object_type_column'              => false,
			'display_meta_column'                     => false,
			'display_blog_column_linked'              => true,
			'auto_cleanup_active'                     => true,
			'auto_cleanup_period'                     => 24,
			'exceptions_option_action_scheduler_lock' => true,
			'exceptions_plugin_list'                  => array(),
			'exceptions_theme_list'                   => array(),
			'exceptions_cron_list'                    => array(),
			'exceptions_notification_list'            => array(),
			'exceptions_option_list'                  => array(),
			'exceptions_post-meta_list'               => array(
				'_edit_lock',
			),
			'exceptions_user-meta_list'               => array(
				'coreactivity_last_activity',
				'coreactivity_last_login',
				'coreactivity_last_log_visit',
				'coreactivity_last_logout',
			),
			'exceptions_term-meta_list'               => array(),
			'exceptions_comment-meta_list'            => array(),
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
		add_action( 'coreactivity_settings_value_changed', array( $this, 'settings_has_changed' ), 10, 4 );
	}

	protected function _install_db() {
		return InstallDB::instance();
	}

	public function is_in_exception_list( $list, $value ) : bool {
		$option = 'exceptions_' . $list . '_list';
		$values = $this->get( $option );

		return in_array( $value, $values );
	}

	public function settings_has_changed( $name, $group, $old, $value ) {
		if ( $group == 'settings' ) {
			if ( ( $name == 'geolocation_method' && $value != 'online' ) || $name == 'geolocation_ip2location_db' || $name == 'geolocation_geoip2_db' ) {
				coreactivity()->schedule_geo_db_update();
			}
		}
	}
}
