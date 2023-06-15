<?php

namespace Dev4Press\Plugin\CoreActivity\Admin\Panel;

use Dev4Press\v42\Core\UI\Admin\PanelTools;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Tools extends PanelTools {
	protected function init_default_subpanels() {
		parent::init_default_subpanels();

		$this->subpanels = array_slice( $this->subpanels, 0, 2 ) +
		                   array(
			                   'cleanup' => array(
				                   'title'        => __( "Data Cleanup", "coreactivity" ),
				                   'icon'         => 'ui-trash',
				                   'method'       => 'post',
				                   'button_label' => __( "Cleanup", "coreactivity" ),
				                   'info'         => __( "Using this tool, you can cleanup log entries.", "coreactivity" )
			                   )
		                   ) +
		                   array_slice( $this->subpanels, 2 );
	}
}
