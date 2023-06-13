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
				                   'title'        => __( "Data Cleanup", "gd-bbpress-toolbox" ),
				                   'icon'         => 'ui-trash',
				                   'method'       => 'post',
				                   'button_label' => __( "Cleanup", "gd-bbpress-toolbox" ),
				                   'info'         => __( "Using this tool, you can cleanup log entries.", "gd-bbpress-toolbox" )
			                   )
		                   ) +
		                   array_slice( $this->subpanels, 2 );
	}
}
