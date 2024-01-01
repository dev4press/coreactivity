<?php

namespace Dev4Press\Plugin\CoreActivity\Log;

use DeviceDetector\DeviceDetector;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Device {
	private $obj;

	public function __construct() {
		require_once COREACTIVITY_PATH . 'vendor/device-detector/autoload.php';

		$this->obj = new DeviceDetector();
	}

	public static function instance() : Device {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new Device();
		}

		return $instance;
	}

	public function detect( $ua ) : array {
		$this->obj->setUserAgent( $ua );
		$this->obj->parse();

		return (array)$this->obj;
	}
}
