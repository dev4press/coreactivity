<?php

namespace Dev4Press\Plugin\CoreActivity\Admin\Panel;

use Dev4Press\v44\Core\UI\Admin\PanelTools;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Tools extends PanelTools {
	protected function init_default_subpanels() {
		parent::init_default_subpanels();

		$this->subpanels = array_slice( $this->subpanels, 0, 2 ) +
		                   array(
			                   'cleanup' => array(
				                   'title'        => __( 'Data Cleanup', 'coreactivity' ),
				                   'icon'         => 'ui-trash',
				                   'method'       => 'post',
				                   'button_label' => __( 'Cleanup', 'coreactivity' ),
				                   'info'         => __( 'Using this tool, you can cleanup log entries.', 'coreactivity' ),
			                   ),
		                   ) +
		                   array_slice( $this->subpanels, 2 );

		$this->subpanels = array_slice( $this->subpanels, 0, 1 ) +
		                   array(
			                   'notifications' => array(
				                   'title'        => __( 'Notifications', 'coreactivity' ),
				                   'icon'         => 'ui-envelopes',
				                   'method'       => 'post',
				                   'break'        => __( 'Configuration', 'coreactivity' ),
				                   'break-icon'   => 'ui-cogs',
				                   'button_label' => __( 'Update', 'coreactivity' ),
				                   'info'         => __( 'Using this tool, bulk enable or disable events for notifications.', 'coreactivity' ),
			                   ),
		                   ) +
		                   array_slice( $this->subpanels, 1 );
	}
}
