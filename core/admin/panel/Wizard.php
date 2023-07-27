<?php

namespace Dev4Press\Plugin\CoreActivity\Admin\Panel;

use Dev4Press\v43\Core\UI\Admin\PanelWizard;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Wizard extends PanelWizard {
	protected function init_default_subpanels() {
		$this->subpanels = array(
			'intro'         => array(
				'title' => __( "Intro", "gd-knowledge-base" )
			),
			'log'           => array(
				'title' => __( "Log", "gd-knowledge-base" )
			),
			'notifications' => array(
				'title' => __( "Notifications", "gd-knowledge-base" )
			),
			'finish'        => array(
				'title' => __( "Finish", "gd-knowledge-base" )
			)
		);
	}
}
