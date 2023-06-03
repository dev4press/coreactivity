<?php

namespace Dev4Press\Plugin\CoreActivity\Admin;

use Dev4Press\v42\Core\Options\Settings as BaseSettings;
use Dev4Press\v42\Core\Options\Type;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Settings extends BaseSettings {
	protected function value( $name, $group = 'settings', $default = null ) {
		return coreactivity_settings()->get( $name, $group, $default );
	}

	protected function init() {
		$this->settings = array(
			'optional'    => array(
				'optional-settings' => array(
					'name'     => __( "Meta Data", "coreactivity" ),
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
				)
			)
		);
	}
}
