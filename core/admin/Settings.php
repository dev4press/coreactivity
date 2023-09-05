<?php

namespace Dev4Press\Plugin\CoreActivity\Admin;

use Dev4Press\Plugin\CoreActivity\Log\Activity;
use Dev4Press\v43\Core\Options\Settings as BaseSettings;
use Dev4Press\v43\Core\Options\Type;

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
				'name'     => __( "Cleanup Period", "coreactivity" ),
				'sections' => array(
					array(
						'label'    => '',
						'name'     => '',
						'class'    => '',
						'settings' => array(
							$this->i( 'tools-cleanup', 'period', __( "Log entries age", "coreactivity" ), '', Type::SELECT )->data( 'array', $this->get_period_list() ),
						),
					),
				),
			),
			'cleanup-events' => array(
				'name'     => __( "Cleanup Events", "coreactivity" ),
				'sections' => array(
					array(
						'label'    => '',
						'name'     => '',
						'class'    => '',
						'settings' => array(
							$this->i( 'tools-cleanup', 'events', __( "Events to remove", "coreactivity" ), '', Type::CHECKBOXES_GROUP )->data( 'array', Activity::instance()->get_select_events() ),
						),
					),
				),
			),
		);
	}

	protected function init() {
		$this->settings = array(
			'optional'      => array(
				'optional-meta'  => array(
					'name'     => __( "Standard Meta Data", "coreactivity" ),
					'sections' => array(
						array(
							'label'    => '',
							'name'     => '',
							'class'    => '',
							'settings' => array(
								$this->i( 'settings', 'log_if_available_user_agent', __( "User Agent", "coreactivity" ), __( "If the request has user agent string, it will be logged as the log entry meta data.", "coreactivity" ), Type::BOOLEAN ),
								$this->i( 'settings', 'log_if_available_referer', __( "Referer", "coreactivity" ), __( "If the request has referer, it will be logged as the log entry meta data.", "coreactivity" ), Type::BOOLEAN ),
							),
						),
					),
				),
				'optional-event' => array(
					'name'     => __( "Event Specific Meta Data", "coreactivity" ),
					'sections' => array(
						array(
							'label'    => '',
							'name'     => '',
							'class'    => '',
							'settings' => array(
								$this->i( 'settings', 'log_if_available_description', __( "Plugin or Theme Descriptions", "coreactivity" ), __( "Descriptions are available for plugins and themes, but they can be on the longer side, and usually are not useful for log analysis.", "coreactivity" ), Type::BOOLEAN ),
								$this->i( 'settings', 'log_transient_value', __( "Transient Value", "coreactivity" ), __( "In most cases, transient values can be huge, since transients are used as a cache of sorts, and it is not a good idea to log the actual value for transient. Transients are often changing and usually serialized.", "coreactivity" ), Type::BOOLEAN ),
							),
						),
					),
				),
			),
			'exceptions'    => array(
				'exceptions-option'   => array(
					'name'     => __( "Component: Options", "coreactivity" ),
					'sections' => array(
						array(
							'label'    => __( "General Rules", "coreactivity" ),
							'name'     => '',
							'class'    => '',
							'settings' => array(
								$this->i( 'settings', 'exceptions_option_action_scheduler_lock', __( "Action Scheduler Locks", "coreactivity" ), __( "Many WordPress plugins use Actions Scheduler (as a plugin or internal component), and it can often change one or more locking options flooding the log with entries. With this enabled, any locking option of the Action Scheduler will be ignored when logging options changes.", "coreactivity" ), Type::BOOLEAN ),
							),
						),
						array(
							'label'    => __( "Specific Options", "coreactivity" ),
							'name'     => '',
							'class'    => '',
							'settings' => array(
								$this->i( 'settings', 'exceptions_option_list', __( "Options to skip", "coreactivity" ), __( "Add one or more options (exact option name) to skip from logging.", "coreactivity" ), Type::EXPANDABLE_TEXT ),
							),
						),
					),
				),
				'exceptions-sitemeta' => array(
					'name'     => __( "Component: Sitemeta", "coreactivity" ),
					'sections' => array(
						array(
							'label'    => __( "Specific Options", "coreactivity" ),
							'name'     => '',
							'class'    => '',
							'settings' => array(
								$this->i( 'settings', 'exceptions_sitemeta_list', __( "Options to skip", "coreactivity" ), __( "Add one or more options (exact option name) to skip from logging.", "coreactivity" ), Type::EXPANDABLE_TEXT ),
							),
						),
					),
				),
				'exceptions-error'    => array(
					'name'     => __( "Component: Errors", "coreactivity" ),
					'sections' => array(
						array(
							'label'    => __( "File Names", "coreactivity" ),
							'name'     => '',
							'class'    => '',
							'settings' => array(
								$this->i( 'settings', 'exceptions_error_file_regex_list', __( "Regular Expressions", "coreactivity" ), __( "If the request is for a file, it will be checked with provided regular expressions. If the file matches any of these, it will not be logged.", "coreactivity" ), Type::EXPANDABLE_TEXT ),
							),
						),
					),
				),
			),
			'logs'          => array(
				'logs-content' => array(
					'name'     => __( "Content Display", "coreactivity" ),
					'sections' => array(
						array(
							'label'    => '',
							'name'     => '',
							'class'    => '',
							'settings' => array(
								$this->i( 'settings', 'display_columns_simplified', __( "Simplified values", "coreactivity" ), __( "Values for columns Component and Event will be displayed using labels.", "coreactivity" ), Type::BOOLEAN ),
								$this->i( 'settings', 'display_ip_country_flag', __( "IP GEO location flag", "coreactivity" ), __( "Show country for the logged IP.", "coreactivity" ), Type::BOOLEAN ),
								$this->i( 'settings', 'display_user_avatar', __( "User avatar", "coreactivity" ), __( "Show user avatar for logs that are related to the user.", "coreactivity" ), Type::BOOLEAN ),
							),
						),
					),
				),
				'logs-layout'  => array(
					'name'     => __( "Table Layout", "coreactivity" ),
					'sections' => array(
						array(
							'label'    => '',
							'name'     => '',
							'class'    => '',
							'settings' => array(
								$this->i( 'settings', 'display_request_column', __( "Request as Column", "coreactivity" ), __( "Request value can be quite long, and it can cause layout issues if displayed as column. If this option is displayed, Request will be displayed as Meta value in the hidden Meta row.", "coreactivity" ), Type::BOOLEAN ),
								$this->i( 'settings', 'display_protocol_column', __( "Protocol as Column", "coreactivity" ), __( "Protocol value would most likely be the same for each request, especially if your webserver is behind some sort of proxy setup. If this option is displayed, Protocol will be displayed as Meta value in the hidden Meta row.", "coreactivity" ), Type::BOOLEAN ),
							),
						),
					),
				),
				'logs-live'    => array(
					'name'     => __( "Log Panel Updates", "coreactivity" ),
					'sections' => array(
						array(
							'label'    => '',
							'name'     => '',
							'class'    => '',
							'settings' => array(
								$this->i( 'settings', 'logs_live_updates', __( "Live Updates", "coreactivity" ), __( "Live AJAX requests every 15 seconds to get the latest logged events. The live functionality with take into account the Log panel current filters and view settings.", "coreactivity" ), Type::BOOLEAN ),
							),
						),
					),
				),
			),
			'notifications' => array(
				'notifications-instant' => array(
					'name'     => __( "Instant Notifications", "coreactivity" ),
					'sections' => array(
						array(
							'label'    => '',
							'name'     => '',
							'class'    => '',
							'settings' => array(
								$this->i( 'notifications', 'instant', __( "Status", "coreactivity" ), __( "If enabled, plugin will send instant notifications when eligible events are logged. The notifications will not be completely instant, because that can lead to the huge number of emails sent if some events occur often. Instead, after instant notification is sent, plugin will not send a new one during the predefined delay, and next one will include all eligible events from that period.", "coreactivity" ), Type::BOOLEAN ),
								$this->i( 'notifications', 'instant_emails', __( "Emails", "coreactivity" ), __( "One or more emails to send the notifications. If empty, it will use the website admin email.", "coreactivity" ), Type::EXPANDABLE_TEXT )->args( array( 'type' => 'email' ) ),
							),
						),
						array(
							'label'    => __( "Log", "coreactivity" ),
							'name'     => '',
							'class'    => '',
							'settings' => array(
								$this->i( 'notifications', 'instant_skip_log', __( "Skip Logging", "coreactivity" ), __( "If enabled, this email notification will not be logged by the plugin.", "coreactivity" ), Type::BOOLEAN ),
							),
						),
						array(
							'label'    => __( "Advanced", "coreactivity" ),
							'name'     => '',
							'class'    => '',
							'settings' => array(
								$this->i( 'notifications', 'instant_delay_minutes', __( "Delay", "coreactivity" ), __( "This is the shortest delay between two instant notifications emails.", "coreactivity" ), Type::ABSINT )->args( array(
									'label_unit' => __( "Minutes", "coreactivity" ),
									'min'        => 1,
									'step'       => 1,
								) ),
							),
						),
					),
				),
				'notifications-daily'   => array(
					'name'     => __( "Daily Digest Notifications", "coreactivity" ),
					'sections' => array(
						array(
							'label'    => '',
							'name'     => '',
							'class'    => '',
							'settings' => array(
								$this->i( 'notifications', 'daily', __( "Status", "coreactivity" ), __( "If enabled, plugin will send daily digest email with all eligible events from the previous day.", "coreactivity" ), Type::BOOLEAN ),
								$this->i( 'notifications', 'daily_emails', __( "Emails", "coreactivity" ), __( "One or more emails to send the notifications. If empty, it will use the website admin email.", "coreactivity" ), Type::EXPANDABLE_TEXT )->args( array( 'type' => 'email' ) ),
							),
						),
						array(
							'label'    => __( "Log", "coreactivity" ),
							'name'     => '',
							'class'    => '',
							'settings' => array(
								$this->i( 'notifications', 'daily_skip_log', __( "Skip Logging", "coreactivity" ), __( "If enabled, this email notification will not be logged by the plugin.", "coreactivity" ), Type::BOOLEAN ),
							),
						),
						array(
							'label'    => __( "Advanced", "coreactivity" ),
							'name'     => '',
							'class'    => '',
							'settings' => array(
								$this->i( 'notifications', 'daily_hour', __( "Hour", "coreactivity" ), __( "This is the hour of the day when the daily digest is created.", "coreactivity" ), Type::ABSINT )->args( array(
									'label_unit' => __( "Hour", "coreactivity" ),
									'min'        => 0,
									'step'       => 1,
									'max'        => 23,
								) ),
							),
						),
					),
				),
				'notifications-weekly'  => array(
					'name'     => __( "Weekly Digest Notifications", "coreactivity" ),
					'sections' => array(
						array(
							'label'    => '',
							'name'     => '',
							'class'    => '',
							'settings' => array(
								$this->i( 'notifications', 'weekly', __( "Status", "coreactivity" ), __( "If enabled, plugin will send weekly digest email with all eligible events from the previous 7 days.", "coreactivity" ), Type::BOOLEAN ),
								$this->i( 'notifications', 'weekly_emails', __( "Emails", "coreactivity" ), __( "One or more emails to send the notifications. If empty, it will use the website admin email.", "coreactivity" ), Type::EXPANDABLE_TEXT )->args( array( 'type' => 'email' ) ),
							),
						),
						array(
							'label'    => __( "Log", "coreactivity" ),
							'name'     => '',
							'class'    => '',
							'settings' => array(
								$this->i( 'notifications', 'weekly_skip_log', __( "Skip Logging", "coreactivity" ), __( "If enabled, this email notification will not be logged by the plugin.", "coreactivity" ), Type::BOOLEAN ),
							),
						),
						array(
							'label'    => __( "Advanced", "coreactivity" ),
							'name'     => '',
							'class'    => '',
							'settings' => array(
								$this->i( 'notifications', 'weekly_day', __( "Day", "coreactivity" ), __( "This is day of the week when the daily digest is created.", "coreactivity" ), Type::SELECT )->data( 'array', $this->get_week_days() ),
								$this->i( 'notifications', 'weekly_hour', __( "Hour", "coreactivity" ), __( "This is the hour of the day when the daily digest is created.", "coreactivity" ), Type::ABSINT )->args( array(
									'label_unit' => __( "Hour", "coreactivity" ),
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
					'name'     => __( "Auto Cleanup", "coreactivity" ),
					'sections' => array(
						array(
							'label'    => '',
							'name'     => '',
							'class'    => '',
							'settings' => array(
								$this->i( 'settings', 'auto_cleanup_active', __( "Remove old entries", "coreactivity" ), __( "If enabled, maintenance will be run once a day, and all old entries will be removed.", "coreactivity" ), Type::BOOLEAN ),
								$this->i( 'settings', 'auto_cleanup_period', __( "Log entries to keep", "coreactivity" ), __( "All entries older than the number of months specified here, will be removed during the maintenance.", "coreactivity" ), Type::ABSINT )->args( array(
									'min'        => 1,
									'label_unit' => __( "months", "coreactivity" ),
								) ),
							),
						),
					),
				),
			),
			'advanced'      => array(
				'advanced-notices'  => array(
					'name'     => __( "Notices", "coreactivity" ),
					'sections' => array(
						array(
							'label'    => '',
							'name'     => '',
							'class'    => '',
							'settings' => array(
								$this->i( 'settings', 'notice_if_logging_is_disabled', __( "Logging is Disabled", "coreactivity" ), __( "If the logging is disabled, on every admin page, plugin will display a notice about it, with link to the plugin dashboard.", "coreactivity" ), Type::BOOLEAN ),
							),
						),
					),
				),
				'advanced-adminbar' => array(
					'name'     => __( "Admin Bar Integration", "coreactivity" ),
					'sections' => array(
						array(
							'label'    => '',
							'name'     => '',
							'class'    => '',
							'settings' => array(
								$this->i( 'settings', 'admin_bar_integration', __( "Add Menu", "coreactivity" ), __( "Simple menu will be added to the WordPress admin bar, on both admin side and front end, only available to website administrators with quick links to the plugin panels.", "coreactivity" ), Type::BOOLEAN ),
							),
						),
					),
				),
				'advanced-wizard'   => array(
					'name'     => __( "Setup Wizard", "coreactivity" ),
					'sections' => array(
						array(
							'label'    => '',
							'name'     => '',
							'class'    => '',
							'settings' => array(
								$this->i( 'settings', 'show_setup_wizard', __( "Show Setup Wizard", "coreactivity" ), __( "If enabled, the Setup Wizard item will be included in the plugin admin side navigation.", "coreactivity" ), Type::BOOLEAN ),
							),
						),
					),
				),
			),
		);
	}

	protected function get_period_list() : array {
		return array(
			''     => __( "Select the data age", "coreactivity" ),
			'd000' => __( "All logged data", "coreactivity" ),
			'd001' => __( "Logged data older than 1 day", "coreactivity" ),
			'd003' => __( "Logged data older than 3 days", "coreactivity" ),
			'd007' => __( "Logged data older than 7 days", "coreactivity" ),
			'd014' => __( "Logged data older than 14 days", "coreactivity" ),
			'd030' => __( "Logged data older than 30 days", "coreactivity" ),
			'd060' => __( "Logged data older than 60 days", "coreactivity" ),
			'd090' => __( "Logged data older than 90 days", "coreactivity" ),
			'd180' => __( "Logged data older than 180 days", "coreactivity" ),
			'm012' => __( "Logged data older than 1 year", "coreactivity" ),
			'm024' => __( "Logged data older than 2 years", "coreactivity" ),
		);
	}

	protected function get_week_days() : array {
		global $wp_locale;

		$list = array();

		for ( $day_index = 0; $day_index <= 6; $day_index ++ ) {
			$list[ 'D' . $day_index ] = $wp_locale->get_weekday( $day_index );
		}

		return $list;
	}
}
