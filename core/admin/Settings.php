<?php

namespace Dev4Press\Plugin\CoreActivity\Admin;

use Dev4Press\Plugin\CoreActivity\Log\Activity;
use Dev4Press\v47\Core\Options\Settings as BaseSettings;
use Dev4Press\v47\Core\Options\Type;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Settings extends BaseSettings {
	private $_tools_cleanup = array(
		'period' => '',
		'events' => true,
	);

	protected function value( $name, $group = 'settings', $default = null ) {
		if ( $group == 'tools-cleanup' ) {
			return $this->_tools_cleanup[ $name ];
		}

		return coreactivity_settings()->get( $name, $group, $default );
	}

	public function tools_cleanup() : array {
		return array(
			'cleanup-basic'  => array(
				'name'     => __( 'Cleanup Period', 'coreactivity' ),
				'sections' => array(
					array(
						'label'    => '',
						'name'     => '',
						'class'    => '',
						'settings' => array(
							$this->i( 'tools-cleanup', 'period', __( 'Log entries age', 'coreactivity' ), '', Type::SELECT )->data( 'array', Data::get_period_list() ),
						),
					),
				),
			),
			'cleanup-events' => array(
				'name'     => __( 'Cleanup Events', 'coreactivity' ),
				'sections' => array(
					array(
						'label'    => '',
						'name'     => '',
						'class'    => '',
						'settings' => array(
							$this->i( 'tools-cleanup', 'events', __( 'Events to remove', 'coreactivity' ), '', Type::CHECKBOXES_GROUP )->data( 'array', Activity::instance()->get_select_events() ),
						),
					),
				),
			),
		);
	}

	protected function init() {
		$this->settings = array(
			'optional'      => array(
				'optional-location' => array(
					'name'     => __( 'IP Location Data', 'coreactivity' ),
					'sections' => array(
						array(
							'label'    => '',
							'name'     => '',
							'class'    => '',
							'settings' => array(
								$this->i( 'settings', 'log_country_code', __( 'Country Code', 'coreactivity' ), __( 'Each log entry IP will be geo-located, and if possible, the country code will be stored in the main log database table.', 'coreactivity' ), Type::BOOLEAN )->more(
									array(
										__( 'Each time logging is requested, plugin will try to determine the country for the IP and save it in the main database.', 'coreactivity' ),
										__( 'Country for IP can change over time (especially for the IP4), and this will add permanent mark of the country into the log at the time it is logged.', 'coreactivity' ),
									)
								)->buttons(
									array(
										array(
											'type'  => 'a',
											'link'  => coreactivity_admin()->panel_url( 'settings', 'geo' ),
											'class' => 'button-secondary',
											'title' => __( 'GEOLocation Settings', 'coreactivity' ),
										),
									)
								),
								$this->i( 'settings', 'log_if_available_expanded_location', __( 'Expanded Meta Information', 'coreactivity' ), __( 'Any additional geolocation data will be logged as a meta data.', 'coreactivity' ), Type::BOOLEAN )->more(
									array(
										__( 'This data, along the country code for the IP will be later used to display IP location in the log.', 'coreactivity' ),
										__( 'Information provided by the geolocation can contain only country, or it can have expanded information.', 'coreactivity' ),
									)
								),
							),
						),
					),
				),
				'optional-metadata' => array(
					'name'     => __( 'Meta Data', 'coreactivity' ),
					'sections' => array(
						array(
							'label'    => __( 'Standard', 'coreactivity' ),
							'name'     => '',
							'class'    => '',
							'settings' => array(
								$this->i( 'settings', 'log_if_available_user_agent', __( 'User Agent', 'coreactivity' ), __( 'If the request has user agent string, it will be logged as the log entry meta data.', 'coreactivity' ), Type::BOOLEAN )->more( array(
									__( 'User agent can be used to extract information about the request source device, OS, browsers, and more.', 'coreactivity' ),
									__( 'User agent data is not always reliable, because request source can spoof the user agent and set it to anything.', 'coreactivity' ),
								) ),
								$this->i( 'settings', 'log_if_available_referer', __( 'Referer', 'coreactivity' ), __( 'If the request has referer, it will be logged as the log entry meta data.', 'coreactivity' ), Type::BOOLEAN ),
							),
						),
						array(
							'label'    => __( 'Device Detection', 'coreactivity' ),
							'name'     => '',
							'class'    => '',
							'settings' => array(
								$this->i( 'settings', 'log_device_detection_data', __( 'Device Information', 'coreactivity' ), __( 'Based on the user agent, detect the information about the device and store it as meta when the request is logged.', 'coreactivity' ), Type::BOOLEAN )->more( array(
									__( 'Device detection will be done at the request log time, and will be stored as meta data.', 'coreactivity' ),
									__( 'Device detection data will take additional space in the meta table.', 'coreactivity' ),
									__( 'If you don\'t save this as metadata, plugin can still display device detection data later, if you enable User Agent logging.', 'coreactivity' ),
								) ),
								$this->i( 'settings', 'log_device_detection_filter', __( 'Filter Information', 'coreactivity' ), __( 'Simplified detected information will be stored as individual meta elements that can be used for the log filtering.', 'coreactivity' ), Type::BOOLEAN )->more( array(
									__( 'At this time, coreActivity plugin doesn\'t have filter by meta data implemented!', 'coreactivity' ),
									__( 'Device detection data will take additional space in the meta table.', 'coreactivity' ),
								) ),
							),
						),
						array(
							'label'    => __( 'Event Specific', 'coreactivity' ),
							'name'     => '',
							'class'    => '',
							'settings' => array(
								$this->i( 'settings', 'log_if_available_description', __( 'Plugin or Theme Descriptions', 'coreactivity' ), __( 'Descriptions are available for plugins and themes, but they can be on the longer side, and usually are not useful for log analysis.', 'coreactivity' ), Type::BOOLEAN ),
								$this->i( 'settings', 'log_transient_value', __( 'Transient Value', 'coreactivity' ), __( 'In most cases, transient values can be huge, since transients are used as a cache of sorts, and it is not a good idea to log the actual value for transient. Transients are often changing and usually serialized.', 'coreactivity' ), Type::BOOLEAN ),
							),
						),
					),
				),
			),
			'exceptions'    => array(
				'exceptions-metadata'     => array(
					'name'     => __( 'Meta Data', 'coreactivity' ),
					'sections' => array(
						array(
							'label'    => '',
							'name'     => '',
							'class'    => '',
							'settings' => array(
								$this->i( 'settings', 'exceptions_post-meta_list', __( 'Post Meta', 'coreactivity' ), __( 'Add one or more options (exact meta key name) to skip from logging.', 'coreactivity' ), Type::EXPANDABLE_TEXT ),
								$this->i( 'settings', 'exceptions_user-meta_list', __( 'User Meta', 'coreactivity' ), __( 'Add one or more options (exact meta key name) to skip from logging.', 'coreactivity' ), Type::EXPANDABLE_TEXT ),
								$this->i( 'settings', 'exceptions_term-meta_list', __( 'Term Meta', 'coreactivity' ), __( 'Add one or more options (exact meta key name) to skip from logging.', 'coreactivity' ), Type::EXPANDABLE_TEXT ),
								$this->i( 'settings', 'exceptions_comment-meta_list', __( 'Comment Meta', 'coreactivity' ), __( 'Add one or more options (exact meta key name) to skip from logging.', 'coreactivity' ), Type::EXPANDABLE_TEXT ),
							),
						),
					),
				),
				'exceptions-option'       => array(
					'name'     => __( 'Component: Options', 'coreactivity' ),
					'sections' => array(
						array(
							'label'    => __( 'General Rules', 'coreactivity' ),
							'name'     => '',
							'class'    => '',
							'settings' => array(
								$this->i( 'settings', 'exceptions_option_action_scheduler_lock', __( 'Action Scheduler Locks', 'coreactivity' ), __( 'Many WordPress plugins use Actions Scheduler (as a plugin or internal component), and it can often change one or more locking options flooding the log with entries. With this enabled, any locking option of the Action Scheduler will be ignored when logging options changes.', 'coreactivity' ), Type::BOOLEAN ),
							),
						),
						array(
							'label'    => __( 'Specific Options', 'coreactivity' ),
							'name'     => '',
							'class'    => '',
							'settings' => array(
								$this->i( 'settings', 'exceptions_option_list', __( 'Options to skip', 'coreactivity' ), __( 'Add one or more options (exact option name) to skip from logging.', 'coreactivity' ), Type::EXPANDABLE_TEXT ),
							),
						),
					),
				),
				'exceptions-sitemeta'     => array(
					'name'     => __( 'Component: Sitemeta', 'coreactivity' ),
					'sections' => array(
						array(
							'label'    => __( 'Specific Options', 'coreactivity' ),
							'name'     => '',
							'class'    => '',
							'settings' => array(
								$this->i( 'settings', 'exceptions_sitemeta_list', __( 'Options to skip', 'coreactivity' ), __( 'Add one or more options (exact option name) to skip from logging.', 'coreactivity' ), Type::EXPANDABLE_TEXT ),
							),
						),
					),
				),
				'exceptions-notification' => array(
					'name'     => __( 'Component: Notification', 'coreactivity' ),
					'sections' => array(
						array(
							'label'    => __( 'Specific Options', 'coreactivity' ),
							'name'     => '',
							'class'    => '',
							'settings' => array(
								$this->i( 'settings', 'exceptions_notification_list', __( 'Notifications to Skip', 'coreactivity' ), __( 'Add one or more notifications (exact option name) to skip from logging.', 'coreactivity' ), Type::EXPANDABLE_TEXT ),
							),
						),
					),
				),
				'exceptions-wordpress'    => array(
					'name'     => __( 'Component: WordPress', 'coreactivity' ),
					'sections' => array(
						array(
							'label'    => __( 'Specific Options', 'coreactivity' ),
							'name'     => '',
							'class'    => '',
							'settings' => array(
								$this->i( 'settings', 'exceptions_cron_list', __( 'CRON to Skip', 'coreactivity' ), __( 'Add one or more CRON jobs (exact option name) to skip from logging.', 'coreactivity' ), Type::EXPANDABLE_TEXT ),
							),
						),
					),
				),
				'exceptions-plugin'       => array(
					'name'     => __( 'Component: Plugin', 'coreactivity' ),
					'sections' => array(
						array(
							'label'    => __( 'Specific Options', 'coreactivity' ),
							'name'     => '',
							'class'    => '',
							'settings' => array(
								$this->i( 'settings', 'exceptions_plugin_list', __( 'Plugins to Skip', 'coreactivity' ), __( 'Add one or more plugins (exact option name) to skip from logging.', 'coreactivity' ), Type::EXPANDABLE_TEXT ),
							),
						),
					),
				),
				'exceptions-theme'        => array(
					'name'     => __( 'Component: Theme', 'coreactivity' ),
					'sections' => array(
						array(
							'label'    => __( 'Specific Options', 'coreactivity' ),
							'name'     => '',
							'class'    => '',
							'settings' => array(
								$this->i( 'settings', 'exceptions_theme_list', __( 'Themes to Skip', 'coreactivity' ), __( 'Add one or more themes (exact option name) to skip from logging.', 'coreactivity' ), Type::EXPANDABLE_TEXT ),
							),
						),
					),
				),
				'exceptions-error'        => array(
					'name'     => __( 'Component: Errors', 'coreactivity' ),
					'sections' => array(
						array(
							'label'    => __( 'File Names', 'coreactivity' ),
							'name'     => '',
							'class'    => '',
							'settings' => array(
								$this->i( 'settings', 'exceptions_error_file_regex_list', __( 'Regular Expressions', 'coreactivity' ), __( 'If the request is for a file, it will be checked with provided regular expressions. If the file matches any of these, it will not be logged.', 'coreactivity' ), Type::EXPANDABLE_TEXT ),
							),
						),
					),
				),
			),
			'geo'           => array(
				'exceptions-geo' => array(
					'name'     => __( 'Geo Location', 'coreactivity' ),
					'sections' => array(
						array(
							'label'    => '',
							'name'     => '',
							'class'    => '',
							'settings' => array(
								$this->i( 'settings', 'geolocation_method', __( 'Method', 'coreactivity' ), __( 'Online geolocation is usually slower, it does make a call to the online service for geo-locating the IP.', 'coreactivity' ), Type::SELECT )->data( 'array', Data::get_geo_location_methods() )->more(
									array(
										__( 'Online geolocation can be slower, since it depends on the external services to work. It can also happen for the request to timeout or fail.', 'coreactivity' ),
										__( 'Database approach is the best solution since the database is on your server, and it is much faster to get the location, without having any issues that online services can have.', 'coreactivity' ),
										__( 'If the selected database method is not working (database file is missing), plugin will fallback to use Online method.', 'coreactivity' ),
									)
								)->switch( array(
									'role' => 'control',
									'type' => 'section',
									'name' => 'coreactivity-switch-method-geo',
								) ),
							),
						),
						array(
							'label'    => __( 'IP2Location Database', 'coreactivity' ),
							'name'     => '',
							'class'    => '',
							'switch'   => array(
								'role'  => 'value',
								'name'  => 'coreactivity-switch-method-geo',
								'value' => 'ip2location',
								'ref'   => $this->value( 'geolocation_method', 'settings', 'online' ),
							),
							'settings' => array(
								$this->i( 'settings', 'geolocation_ip2location_token', __( 'Token', 'coreactivity' ), __( 'Token is required to download and updated database file.', 'coreactivity' ), Type::TEXT )->more(
									array(
										__( 'IP2Location has free and premium services and databases. For this plugin purposes, Lite database is quite sufficient.', 'coreactivity' ),
										__( 'To get the download token, register on the IP2Location Lite website, and once you are logged in there, get the download token and past it in this field.', 'coreactivity' ),
										__( 'Plugin will attempt to download database file once a week during regular weekly maintenance.', 'coreactivity' ),
									)
								)->buttons(
									array(
										array(
											'type'   => 'a',
											'rel'    => 'noopener',
											'target' => '_blank',
											'link'   => 'https://lite.ip2location.com/',
											'class'  => 'button-secondary',
											'title'  => __( 'Register and Get token on IP2Location Lite', 'coreactivity' ),
										),
									)
								),
								$this->i( 'settings', 'geolocation_ip2location_db', __( 'Database', 'coreactivity' ), __( 'Depending on the database you choose, you will get additional information for each IP.', 'coreactivity' ), Type::SELECT )->data( 'array', Data::get_ip2location_db() )->more(
									array(
										__( 'Country database is about 9MB in size, other databases can be up to 200MB in size.', 'coreactivity' ),
										__( 'If you choose Country only database, you will be able to log in only IP country code.', 'coreactivity' ),
									)
								),
							),
						),
						array(
							'label'    => __( 'GEOIP2 Database', 'coreactivity' ),
							'name'     => '',
							'class'    => '',
							'switch'   => array(
								'role'  => 'value',
								'name'  => 'coreactivity-switch-method-geo',
								'value' => 'geoip2',
								'ref'   => $this->value( 'geolocation_method', 'settings', 'online' ),
							),
							'settings' => array(
								$this->i( 'settings', 'geolocation_geoip2_license', __( 'License', 'coreactivity' ), __( 'License is required to download and updated database file.', 'coreactivity' ), Type::TEXT )->more(
									array(
										__( 'GEOIP2 has free and premium services and databases. For this plugin purposes, Lite database is quite sufficient.', 'coreactivity' ),
										__( 'To get the download license, register on the GEOIP2 website, and once you are logged in there, generate new license.', 'coreactivity' ),
										__( 'Plugin will attempt to download database file once a week during regular weekly maintenance.', 'coreactivity' ),
									)
								)->buttons(
									array(
										array(
											'type'   => 'a',
											'rel'    => 'noopener',
											'target' => '_blank',
											'link'   => 'https://www.maxmind.com/en/account/login',
											'class'  => 'button-secondary',
											'title'  => __( 'Register and Get token on GeoIP2', 'coreactivity' ),
										),
									)
								),
								$this->i( 'settings', 'geolocation_geoip2_db', __( 'Database', 'coreactivity' ), __( 'Depending on the database you choose, you will get additional information for each IP.', 'coreactivity' ), Type::SELECT )->data( 'array', Data::get_geoip2_db() )->more(
									array(
										__( 'If you choose Country only database, you will be able to log in only IP country code.', 'coreactivity' ),
									)
								),
							),
						),
					),
				),
			),
			'logs'          => array(
				'logs-content' => array(
					'name'     => __( 'Content Display', 'coreactivity' ),
					'sections' => array(
						array(
							'label'    => '',
							'name'     => '',
							'class'    => '',
							'settings' => array(
								$this->i( 'settings', 'display_columns_simplified', __( 'Simplified values', 'coreactivity' ), __( 'Values for columns Component and Event will be displayed using labels.', 'coreactivity' ), Type::BOOLEAN ),
								$this->i( 'settings', 'display_ip_country_flag', __( 'IP GEO location flag', 'coreactivity' ), __( 'Show country for the logged IP.', 'coreactivity' ), Type::BOOLEAN ),
								$this->i( 'settings', 'display_user_avatar', __( 'User avatar', 'coreactivity' ), __( 'Show user avatar for logs that are related to the user.', 'coreactivity' ), Type::BOOLEAN ),
								$this->i( 'settings', 'display_blog_column_linked', __( 'Linked Blog ID', 'coreactivity' ), __( 'In the multisite network mode, link the Blog ID to the Blog URL', 'coreactivity' ), Type::BOOLEAN ),
							),
						),
					),
				),
				'logs-layout'  => array(
					'name'     => __( 'Table Layout', 'coreactivity' ),
					'sections' => array(
						array(
							'label'    => __( 'Basic Data', 'coreactivity' ),
							'name'     => '',
							'class'    => '',
							'settings' => array(
								$this->i( 'settings', 'display_request_column', __( 'Request as Column', 'coreactivity' ), __( 'Request value can be quite long, and it can cause layout issues if displayed as column. If this option is displayed, Request will be displayed as Meta value in the hidden Meta row.', 'coreactivity' ), Type::BOOLEAN ),
								$this->i( 'settings', 'display_protocol_column', __( 'Protocol as Column', 'coreactivity' ), __( 'Protocol value would most likely be the same for each request, especially if your webserver is behind some sort of proxy setup. If this option is displayed, Protocol will be displayed as Meta value in the hidden Meta row.', 'coreactivity' ), Type::BOOLEAN ),
								$this->i( 'settings', 'display_object_type_column', __( 'Object Type Column', 'coreactivity' ), __( 'If enabled, Object Type will have own column, if disabled, it will be displayed as a part of the Object column.', 'coreactivity' ), Type::BOOLEAN ),
								$this->i( 'settings', 'display_detection_column', __( 'Device Detection Column', 'coreactivity' ), __( 'Show the Device Detection information, if available.', 'coreactivity' ), Type::BOOLEAN ),
							),
						),
						array(
							'label'    => __( 'Meta Data', 'coreactivity' ),
							'name'     => '',
							'class'    => '',
							'settings' => array(
								$this->i( 'settings', 'display_meta_column', __( 'Meta Column', 'coreactivity' ), __( 'By default, all meta data is displayed in the hidden Meta row under main row because a lot of metadata can be quite large and will break table layout or will be unreadable. If you enable Meta column, some of the meta data will be displayed in this column, depending on the event.', 'coreactivity' ), Type::BOOLEAN ),
							),
						),
					),
				),
				'logs-live'    => array(
					'name'     => __( 'Log Panel Updates', 'coreactivity' ),
					'sections' => array(
						array(
							'label'    => '',
							'name'     => '',
							'class'    => '',
							'settings' => array(
								$this->i( 'settings', 'logs_live_updates', __( 'Live Updates', 'coreactivity' ), __( 'Live AJAX requests every 15 seconds to get the latest logged events. The live functionality with take into account the Log panel current filters and view settings.', 'coreactivity' ), Type::BOOLEAN ),
							),
						),
					),
				),
			),
			'tracking'      => array(
				'tracking-users' => array(
					'name'     => __( 'Users Online Status', 'coreactivity' ),
					'sections' => array(
						array(
							'label'    => '',
							'name'     => '',
							'class'    => '',
							'settings' => array(
								$this->i( 'settings', 'users_online_window', __( 'Online Window', 'coreactivity' ), __( 'Knowing if the user is online or not is not easy to achieve without session tracking, which is not advisable in WordPress powered websites. The closest online status is the last activity with some added time for approximation.', 'coreactivity' ), Type::ABSINT )->more( array(
									__( 'Do not set this value too high, 5 to 10 minutes (in seconds) is more than enough.', 'coreactivity' ),
								) )->args( array(
									'label_unit' => __( 'seconds' ),
									'min'        => 30,
									'step'       => 1,
									'max'        => 3600,
								) ),
							),
						),
					),
				),
			),
			'notifications' => array(
				'notifications-instant' => array(
					'name'     => __( 'Instant Notifications', 'coreactivity' ),
					'sections' => array(
						array(
							'label'    => '',
							'name'     => '',
							'class'    => '',
							'settings' => array(
								$this->i( 'notifications', 'instant', __( 'Status', 'coreactivity' ), __( 'If enabled, plugin will send instant notifications when eligible events are logged. The notifications will not be completely instant, because that can lead to the huge number of emails sent if some events occur often. Instead, after instant notification is sent, plugin will not send a new one during the predefined delay, and next one will include all eligible events from that period.', 'coreactivity' ), Type::BOOLEAN ),
								$this->i( 'notifications', 'instant_emails', __( 'Emails', 'coreactivity' ), __( 'One or more emails to send the notifications. If empty, it will use the website admin email.', 'coreactivity' ), Type::EXPANDABLE_TEXT )->args( array( 'type' => 'email' ) ),
							),
						),
						array(
							'label'    => __( 'Log', 'coreactivity' ),
							'name'     => '',
							'class'    => '',
							'settings' => array(
								$this->i( 'notifications', 'instant_skip_log', __( 'Skip Logging', 'coreactivity' ), __( 'If enabled, this email notification will not be logged by the plugin.', 'coreactivity' ), Type::BOOLEAN ),
							),
						),
						array(
							'label'    => __( 'Advanced', 'coreactivity' ),
							'name'     => '',
							'class'    => '',
							'settings' => array(
								$this->i( 'notifications', 'instant_delay_minutes', __( 'Delay', 'coreactivity' ), __( 'This is the shortest delay between two instant notifications emails.', 'coreactivity' ), Type::ABSINT )->args( array(
									'label_unit' => __( 'Minutes', 'coreactivity' ),
									'min'        => 1,
									'step'       => 1,
								) ),
							),
						),
					),
				),
				'notifications-daily'   => array(
					'name'     => __( 'Daily Digest Notifications', 'coreactivity' ),
					'sections' => array(
						array(
							'label'    => '',
							'name'     => '',
							'class'    => '',
							'settings' => array(
								$this->i( 'notifications', 'daily', __( 'Status', 'coreactivity' ), __( 'If enabled, plugin will send daily digest email with all eligible events from the previous day.', 'coreactivity' ), Type::BOOLEAN ),
								$this->i( 'notifications', 'daily_emails', __( 'Emails', 'coreactivity' ), __( 'One or more emails to send the notifications. If empty, it will use the website admin email.', 'coreactivity' ), Type::EXPANDABLE_TEXT )->args( array( 'type' => 'email' ) ),
							),
						),
						array(
							'label'    => __( 'Log', 'coreactivity' ),
							'name'     => '',
							'class'    => '',
							'settings' => array(
								$this->i( 'notifications', 'daily_skip_log', __( 'Skip Logging', 'coreactivity' ), __( 'If enabled, this email notification will not be logged by the plugin.', 'coreactivity' ), Type::BOOLEAN ),
							),
						),
						array(
							'label'    => __( 'Advanced', 'coreactivity' ),
							'name'     => '',
							'class'    => '',
							'settings' => array(
								$this->i( 'notifications', 'daily_hour', __( 'Hour', 'coreactivity' ), __( 'This is the hour of the day when the daily digest is created.', 'coreactivity' ), Type::ABSINT )->args( array(
									'label_unit' => __( 'Hour', 'coreactivity' ),
									'min'        => 0,
									'step'       => 1,
									'max'        => 23,
								) ),
							),
						),
					),
				),
				'notifications-weekly'  => array(
					'name'     => __( 'Weekly Digest Notifications', 'coreactivity' ),
					'sections' => array(
						array(
							'label'    => '',
							'name'     => '',
							'class'    => '',
							'settings' => array(
								$this->i( 'notifications', 'weekly', __( 'Status', 'coreactivity' ), __( 'If enabled, plugin will send weekly digest email with all eligible events from the previous 7 days.', 'coreactivity' ), Type::BOOLEAN ),
								$this->i( 'notifications', 'weekly_emails', __( 'Emails', 'coreactivity' ), __( 'One or more emails to send the notifications. If empty, it will use the website admin email.', 'coreactivity' ), Type::EXPANDABLE_TEXT )->args( array( 'type' => 'email' ) ),
							),
						),
						array(
							'label'    => __( 'Log', 'coreactivity' ),
							'name'     => '',
							'class'    => '',
							'settings' => array(
								$this->i( 'notifications', 'weekly_skip_log', __( 'Skip Logging', 'coreactivity' ), __( 'If enabled, this email notification will not be logged by the plugin.', 'coreactivity' ), Type::BOOLEAN ),
							),
						),
						array(
							'label'    => __( 'Advanced', 'coreactivity' ),
							'name'     => '',
							'class'    => '',
							'settings' => array(
								$this->i( 'notifications', 'weekly_day', __( 'Day', 'coreactivity' ), __( 'This is day of the week when the daily digest is created.', 'coreactivity' ), Type::SELECT )->data( 'array', Data::get_week_days() ),
								$this->i( 'notifications', 'weekly_hour', __( 'Hour', 'coreactivity' ), __( 'This is the hour of the day when the daily digest is created.', 'coreactivity' ), Type::ABSINT )->args( array(
									'label_unit' => __( 'Hour', 'coreactivity' ),
									'min'        => 0,
									'step'       => 1,
									'max'        => 23,
								) ),
							),
						),
					),
				),
			),
			'maintenance'   => array(
				'maintenance-settings' => array(
					'name'     => __( 'Auto Cleanup', 'coreactivity' ),
					'sections' => array(
						array(
							'label'    => '',
							'name'     => '',
							'class'    => '',
							'settings' => array(
								$this->i( 'settings', 'auto_cleanup_active', __( 'Remove old entries', 'coreactivity' ), __( 'If enabled, maintenance will be run once a day, and all old entries will be removed.', 'coreactivity' ), Type::BOOLEAN ),
								$this->i( 'settings', 'auto_cleanup_period', __( 'Log entries to keep', 'coreactivity' ), __( 'All entries older than the number of months specified here, will be removed during the maintenance.', 'coreactivity' ), Type::ABSINT )->args( array(
									'min'        => 1,
									'label_unit' => __( 'months', 'coreactivity' ),
								) ),
							),
						),
					),
				),
			),
			'advanced'      => array(
				'advanced-notices'  => array(
					'name'     => __( 'Notices', 'coreactivity' ),
					'sections' => array(
						array(
							'label'    => '',
							'name'     => '',
							'class'    => '',
							'settings' => array(
								$this->i( 'settings', 'notice_if_logging_is_disabled', __( 'Logging is Disabled', 'coreactivity' ), __( 'If the logging is disabled, on every admin page, plugin will display a notice about it, with link to the plugin dashboard.', 'coreactivity' ), Type::BOOLEAN ),
							),
						),
					),
				),
				'advanced-adminbar' => array(
					'name'     => __( 'Admin Bar Integration', 'coreactivity' ),
					'sections' => array(
						array(
							'label'    => '',
							'name'     => '',
							'class'    => '',
							'settings' => array(
								$this->i( 'settings', 'admin_bar_integration', __( 'Add Menu', 'coreactivity' ), __( 'Simple menu will be added to the WordPress admin bar, on both admin side and front end, only available to website administrators with quick links to the plugin panels.', 'coreactivity' ), Type::BOOLEAN ),
							),
						),
					),
				),
				'advanced-wizard'   => array(
					'name'     => __( 'Setup Wizard', 'coreactivity' ),
					'sections' => array(
						array(
							'label'    => '',
							'name'     => '',
							'class'    => '',
							'settings' => array(
								$this->i( 'settings', 'show_setup_wizard', __( 'Show Setup Wizard', 'coreactivity' ), __( 'If enabled, the Setup Wizard item will be included in the plugin admin side navigation.', 'coreactivity' ), Type::BOOLEAN ),
							),
						),
					),
				),
			),
		);
	}
}
