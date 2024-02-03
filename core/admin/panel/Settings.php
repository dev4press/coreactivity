<?php

namespace Dev4Press\Plugin\CoreActivity\Admin\Panel;

use Dev4Press\v47\Core\UI\Admin\PanelSettings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Settings extends PanelSettings {
	public $settings_class = '\\Dev4Press\\Plugin\\CoreActivity\\Admin\\Settings';

	public function __construct( $admin ) {
		parent::__construct( $admin );

		$this->subpanels = $this->subpanels + array(
				'optional'      => array(
					'title' => __( 'Optional Data', 'coreactivity' ),
					'icon'  => 'ui-terms',
					'info'  => __( 'Settings related to logging of optional data.', 'coreactivity' ),
				),
				'exceptions'    => array(
					'title' => __( 'Exceptions', 'coreactivity' ),
					'icon'  => 'ui-ban',
					'info'  => __( 'Some of the components can use these exceptions to limit the logging and skip some events.', 'coreactivity' ),
				),
				'geo'           => array(
					'title' => __( 'Geo Location', 'coreactivity' ),
					'icon'  => 'ui-globe',
					'info'  => __( 'Plugin can use online geolocation services, or the local database for geolocation.', 'coreactivity' ),
				),
				'logs'          => array(
					'title' => __( 'Logs Panel', 'coreactivity' ),
					'icon'  => 'ui-calendar-pen',
					'info'  => __( 'Settings related to some aspects for the Logs panel display.', 'coreactivity' ),
				),
				'tracking'      => array(
					'title' => __( 'Tracking', 'coreactivity' ),
					'icon'  => 'ui-users',
					'info'  => __( 'Additional settings for some of the plugin tracking features.', 'coreactivity' ),
				),
				'notifications' => array(
					'title' => __( 'Notifications', 'coreactivity' ),
					'icon'  => 'ui-envelope',
					'info'  => __( 'Settings related to instant, daily and weekly email notifications.', 'coreactivity' ),
				),
				'maintenance'   => array(
					'title' => __( 'Maintenance', 'coreactivity' ),
					'icon'  => 'ui-trash',
					'info'  => __( 'Settings related to maintenance and cleanup of the log database tables.', 'coreactivity' ),
				),
				'advanced'      => array(
					'title' => __( 'Advanced', 'coreactivity' ),
					'icon'  => 'ui-warning-triangle',
					'info'  => __( 'More advanced settings that should not be changed for most websites.', 'coreactivity' ),
				),
			);
	}
}
