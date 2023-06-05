<?php

namespace Dev4Press\Plugin\CoreActivity\Admin\Panel;

use Dev4Press\v42\Core\UI\Admin\PanelSettings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Settings extends PanelSettings {
	public $settings_class = '\\Dev4Press\\Plugin\\CoreActivity\\Admin\\Settings';

	public function __construct( $admin ) {
		parent::__construct( $admin );

		$this->subpanels = $this->subpanels + array(
				'optional' => array(
					'title' => __( "Optional Data", "coreactivity" ),
					'icon'  => 'ui-terms',
					'info'  => __( "Settings related to logging of optional data.", "coreactivity" )
				),
				'logs'     => array(
					'title' => __( "Logs Panel", "coreactivity" ),
					'icon'  => 'ui-calendar-pen',
					'info'  => __( "Settings related to some aspects for the Logs panel display.", "coreactivity" )
				),
				'maintenance'     => array(
					'title' => __( "Maintenance", "coreactivity" ),
					'icon'  => 'ui-trash',
					'info'  => __( "Settings related to maintenance and cleanup of the log database tables.", "coreactivity" )
				)
			);
	}
}
