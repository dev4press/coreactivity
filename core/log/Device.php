<?php

namespace Dev4Press\Plugin\CoreActivity\Log;

use DeviceDetector\ClientHints;
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

	public function detect( $ua, $hints = false ) : array {
		$this->obj->setUserAgent( $ua );

		if ( $hints ) {
			$ch = ClientHints::factory( $_SERVER );

			$this->obj->setClientHints( $ch );
		}

		$this->obj->parse();

		if ( $this->obj->isBot() ) {
			$data = array(
				'bot' => $this->obj->getBot()
			);
		} else {
			$data = array(
				'client' => $this->obj->getClient(),
				'os'     => $this->obj->getOs(),
				'device' => $this->obj->getDeviceName(),
				'brand'  => $this->obj->getBrandName(),
				'model'  => $this->obj->getModel()
			);
		}

		return $data;
	}
}
