<?php

namespace Dev4Press\Plugin\CoreActivity\Basic;

use Dev4Press\v45\Core\Plugins\Wizard as CoreWizard;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Wizard extends CoreWizard {
	public $default = array(
		'intro'         => array(
			'referer'    => array(
				array(
					'settings',
					'log_if_available_referer',
					array(
						'yes' => true,
						'no'  => false,
					),
				),
			),
			'user_agent' => array(
				array(
					'settings',
					'log_if_available_user_agent',
					array(
						'yes' => true,
						'no'  => false,
					),
				),
			),
			'admin_bar'  => array(
				array(
					'settings',
					'admin_bar_integration',
					array(
						'
				yes'         => true,
						'no' => false,
					),
				),
			),
		),
		'geo' => array(
			'country'   => array(
				array(
					'settings',
					'log_country_code',
					array(
						'yes' => true,
						'no'  => false,
					),
				),
			),
			'expanded' => array(
				array(
					'settings',
					'log_if_available_expanded_location',
					array(
						'yes' => true,
						'no'  => false,
					),
				),
			),
		),
		'log'           => array(
			'flag'   => array(
				array(
					'settings',
					'display_ip_country_flag',
					array(
						'yes' => true,
						'no'  => false,
					),
				),
			),
			'avatar' => array(
				array(
					'settings',
					'display_user_avatar',
					array(
						'yes' => true,
						'no'  => false,
					),
				),
			),
		),
		'notifications' => array(
			'instant' => array(
				array(
					'notifications',
					'instant',
					array(
						'yes' => true,
						'no'  => false,
					),
				),
			),
			'daily'   => array(
				array(
					'notifications',
					'daily',
					array(
						'yes' => true,
						'no'  => false,
					),
				),
			),
			'weekly'  => array(
				array(
					'notifications',
					'weekly',
					array(
						'yes' => true,
						'no'  => false,
					),
				),
			),
		),
		'finish'        => array(
			'wizard' => array(
				array(
					'settings',
					'show_setup_wizard',
					array(
						'yes' => false,
						'no'  => true,
					),
				),
			),
		),
	);

	public function a() {
		return coreactivity_admin();
	}

	protected function init_panels() {
		$this->panels = array(
			'intro'         => array( 'label' => __( 'Intro', 'coreactivity' ) ),
			'geo'           => array( 'label' => __( 'Geo Location', 'coreactivity' ) ),
			'log'           => array( 'label' => __( 'Log', 'coreactivity' ) ),
			'notifications' => array( 'label' => __( 'Notifications', 'coreactivity' ) ),
			'finish'        => array( 'label' => __( 'Finish', 'coreactivity' ) ),
		);

		$this->setup_panel( $this->a()->subpanel );
	}

	protected function init_data() {

	}
}
