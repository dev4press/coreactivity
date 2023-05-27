<?php

namespace Dev4Press\Plugin\CoreActivity\Admin;

use Dev4Press\v42\Core\Options\Element as EL;
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
		$this->settings = array();
	}
}
