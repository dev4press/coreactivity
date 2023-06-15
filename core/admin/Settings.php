<?php

namespace Dev4Press\Plugin\CoreActivity\Admin;

use Dev4Press\Plugin\CoreActivity\Log\Init;
use Dev4Press\v42\Core\Options\Settings as BaseSettings;
use Dev4Press\v42\Core\Options\Type;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Settings extends BaseSettings {
	private $_tools_cleanup = array(
		'period' => '',
		'events' => true
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
						)
					)
				)
			),
			'cleanup-events' => array(
				'name'     => __( "Cleanup Events", "coreactivity" ),
				'sections' => array(
					array(
						'label'    => '',
						'name'     => '',
						'class'    => '',
						'settings' => array(
							$this->i( 'tools-cleanup', 'events', __( "Events to remove", "coreactivity" ), '', Type::CHECKBOXES_GROUP )->data( 'array', Init::instance()->get_select_events() ),
						)
					)
				)
			)
		);
	}

	protected function init() {
		$this->settings = array(
			'optional'    => array(
				'optional-meta'  => array(
					'name'     => __( "Standard Meta Data", "coreactivity" ),
					'sections' => array(
						array(
							'label'    => '',
							'name'     => '',
							'class'    => '',
							'settings' => array(
								$this->i( 'settings', 'log_if_available_user_agent', __( "User Agent", "coreactivity" ), __( "If the request has user agent string, it will be logged as the log entry meta data.", "coreactivity" ), Type::BOOLEAN ),
								$this->i( 'settings', 'log_if_available_referer', __( "Referer", "coreactivity" ), __( "If the request has referer, it will be logged as the log entry meta data.", "coreactivity" ), Type::BOOLEAN )
							)
						)
					)
				),
				'optional-event' => array(
					'name'     => __( "Event Specific Meta Data", "coreactivity" ),
					'sections' => array(
						array(
							'label'    => '',
							'name'     => '',
							'class'    => '',
							'settings' => array(
								$this->i( 'settings', 'log_if_available_description', __( "Plugin or Theme Descriptions", "coreactivity" ), __( "Descriptions are available for plugins and themes, but they can be on the longer side, and usually are not useful for log analysis.", "coreactivity" ), Type::BOOLEAN )
							)
						)
					)
				)
			),
			'logs'        => array(
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
								$this->i( 'settings', 'display_user_avatar', __( "User avatar", "coreactivity" ), __( "Show user avatar for logs that are related to the user.", "coreactivity" ), Type::BOOLEAN )
							)
						)
					)
				),
				'logs-layout'  => array(
					'name'     => __( "Table Layout", "coreactivity" ),
					'sections' => array(
						array(
							'label'    => '',
							'name'     => '',
							'class'    => '',
							'settings' => array(
								$this->i( 'settings', 'display_request_column', __( "Request as Column", "coreactivity" ), __( "Request value can be quite long, and it can cause layout issues if displayed as column. If this option is displayed, Request will be displayed as Meta value in the hidden Meta row.", "coreactivity" ), Type::BOOLEAN )
							)
						)
					)
				),
				'logs-live'    => array(
					'name'     => __( "Log Panel Updates", "coreactivity" ),
					'sections' => array(
						array(
							'label'    => '',
							'name'     => '',
							'class'    => '',
							'settings' => array(
								$this->i( 'settings', 'logs_live_updates', __( "Live Updates", "coreactivity" ), __( "Live AJAX requests every 15 seconds to get the latest logged events. The live functionality with take into account the Log panel current filters and view settings.", "coreactivity" ), Type::BOOLEAN )
							)
						)
					)
				)
			),
			'maintenance' => array(
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
									'label_unit' => __( "months", "coreactivity" )
								) )
							)
						)
					)
				)
			)
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
			'm024' => __( "Logged data older than 2 years", "coreactivity" )
		);
	}
}
