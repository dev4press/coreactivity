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
			'optional' => array(
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
			),
			'logs'     => array(
				'logs-settings' => array(
					'name'     => __( "Content Display", "coreactivity" ),
					'sections' => array(
						array(
							'label'    => '',
							'name'     => '',
							'class'    => '',
							'settings' => array(
								$this->i( 'settings', 'display_columns_simplified', __( "Simplified values", "coreactivity" ), __( "Values for columns Component and Event will be displayed using labels.", "coreactivity" ), Type::BOOLEAN ),
								$this->i( 'settings', 'display_ip_geo_flag', __( "IP GEO location flag", "coreactivity" ), __( "Show country for the logged IP.", "coreactivity" ), Type::BOOLEAN )
							)
						)
					)
				)
			)
		);
	}
}
