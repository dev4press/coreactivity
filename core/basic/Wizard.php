<?php

namespace Dev4Press\Plugin\CoreActivity\Basic;

use Dev4Press\v43\Core\Plugins\Wizard as CoreWizard;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Wizard extends CoreWizard {
	public $default = array(
		'intro' => array(
			'referer'    => array(
				array( 'settings', 'log_if_available_referer', array( 'yes' => true, 'no' => false ) )
			),
			'user_agent' => array(
				array( 'settings', 'log_if_available_user_agent', array( 'yes' => true, 'no' => false ) )
			)
		),
		'log'   => array(
			'flag'   => array(
				array( 'settings', 'display_ip_country_flag', array( 'yes' => true, 'no' => false ) )
			),
			'avatar' => array(
				array( 'settings', 'display_user_avatar', array( 'yes' => true, 'no' => false ) )
			)
		),
	);

	public function a() {
		return coreactivity_admin();
	}

	protected function init_panels() {
		$this->panels = array(
			'intro'         => array( 'label' => __( "Intro", "coresecurity" ) ),
			'log'           => array( 'label' => __( "Log", "coresecurity" ) ),
			'notifications' => array( 'label' => __( "Notifications", "coresecurity" ) ),
			'finish'        => array( 'label' => __( "Finish", "coresecurity" ) )
		);

		$this->setup_panel( $this->a()->subpanel );
	}
}
