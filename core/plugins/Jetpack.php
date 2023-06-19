<?php

namespace Dev4Press\Plugin\CoreActivity\Plugins;

use Dev4Press\Plugin\CoreActivity\Base\Plugin;
use Jetpack as PluginJetpack;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Jetpack extends Plugin {
	protected $plugin = 'coreactivity';
	protected $name = 'jetpack';
	protected $icon = 'ui-rocket';
	protected $object_type = 'jetmodule';
	protected $plugin_file = 'jetpack/jetpack.php';
	protected $modules = array();

	public function registered_object_types( array $object_types ) : array {
		$object_types[ 'jetmodule' ] = __( "Jetpack Module", "coreactivity" );

		return $object_types;
	}

	public function tracking() {
		if ( $this->is_active( 'module-activated' ) ) {
			add_action( 'jetpack_activate_module', array( $this, 'event_activate_module' ), 10, 2 );
		}

		if ( $this->is_active( 'module-deactivated' ) ) {
			add_action( 'jetpack_deactivate_module', array( $this, 'event_deactivate_module' ), 10, 2 );
		}
	}

	public function label() : string {
		return __( "Jetpack", "coreactivity" );
	}

	protected function get_events() : array {
		return array(
			'module-activated'   => array( 'label' => __( "Activated Module", "coreactivity" ) ),
			'module-deactivated' => array( 'label' => __( "Deactivated Module", "coreactivity" ) )
		);
	}

	private function init_modules() {
		if ( empty( $this->modules ) ) {
			$modules = PluginJetpack::get_available_modules();

			foreach ( $modules as $mod ) {
				$module = PluginJetpack::get_module( $mod );
				if ( $module ) {
					$this->modules[ $mod ] = $module;
				}
			}
		}
	}

	public function event_activate_module( $module = null, $success = null ) {
		$this->init_modules();

		if ( $success !== true || ! $module ) {
			return;
		}

		$info = $this->modules[ $module ] ?? array();

		$this->log( 'module-activated', array(
			'object_name' => $module
		), array(
			'module_name' => $info[ 'name' ] ?? ''
		) );
	}

	public function event_deactivate_module( $module = null, $success = null ) {
		$this->init_modules();

		if ( $success !== true || ! $module ) {
			return;
		}

		$info = $this->modules[ $module ] ?? array();

		$this->log( 'module-deactivated', array(
			'object_name' => $module
		), array(
			'module_name' => $info[ 'name' ] ?? ''
		) );
	}
}
