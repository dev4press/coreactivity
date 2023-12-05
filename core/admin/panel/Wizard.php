<?php

namespace Dev4Press\Plugin\CoreActivity\Admin\Panel;

use Dev4Press\v45\Core\UI\Admin\PanelWizard;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Wizard extends PanelWizard {
	protected function init_default_subpanels() {
		$this->subpanels = array(
			'intro'         => array(
				'title' => __( 'Intro', 'coreactivity' ),
			),
			'log'           => array(
				'title' => __( 'Log', 'coreactivity' ),
			),
			'notifications' => array(
				'title' => __( 'Notifications', 'coreactivity' ),
			),
			'finish'        => array(
				'title' => __( 'Finish', 'coreactivity' ),
			),
		);
	}
}
