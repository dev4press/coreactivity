<?php

namespace Dev4Press\Plugin\CoreActivity\Plugins;

use Dev4Press\Plugin\CoreActivity\Base\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class DebugPress extends Plugin {
	protected $plugin = 'coreactivity';
	protected $name = 'debugpress';
	protected $icon = 'plugin-debugpress';
	protected $object_type = 'phperror';
	protected $plugin_file = 'debugpress/debugpress.php';

	public function registered_object_types( array $object_types ) : array {
		$object_types[ 'phperror' ] = __( "PHP Error", "coreactivity" );

		return $object_types;
	}

	public function tracking() {
		if ( $this->is_active( 'doing-it-wrong' ) ) {
			add_action( 'debugpress-tracker-doing-it-wrong-logged', array( $this, 'event_doing_it_wrong' ) );
		}

		if ( $this->is_active( 'deprecated-function' ) ) {
			add_action( 'debugpress-tracker-deprecated-function-logged', array( $this, 'event_deprecated_function' ) );
		}

		if ( $this->is_active( 'deprecated-file' ) ) {
			add_action( 'debugpress-tracker-deprecated-file-logged', array( $this, 'event_deprecated_file' ) );
		}

		if ( $this->is_active( 'deprecated-argument' ) ) {
			add_action( 'debugpress-tracker-deprecated-argument-logged', array( $this, 'event_deprecated_argument' ) );
		}

		if ( $this->is_active( 'deprecated-constructor' ) ) {
			add_action( 'debugpress-tracker-deprecated-constructor-logged', array( $this, 'event_deprecated_constructor' ) );
		}

		if ( $this->is_active( 'deprecated-hook-run' ) ) {
			add_action( 'debugpress-tracker-deprecated-hook-run-logged', array( $this, 'event_deprecated_hook_run' ) );
		}
	}

	public function label() : string {
		return __( "DebugPress", "coreactivity" );
	}

	protected function get_events() : array {
		return array(
			'php-error'              => array( 'label' => __( "PHP Error", "coreactivity" ) ),
			'doing-it-wrong'         => array( 'label' => __( "Doing It Wrong", "coreactivity" ) ),
			'deprecated-function'    => array( 'label' => __( "Deprecated Function", "coreactivity" ) ),
			'deprecated-file'        => array( 'label' => __( "Deprecated File", "coreactivity" ) ),
			'deprecated-argument'    => array( 'label' => __( "Deprecated Argument", "coreactivity" ) ),
			'deprecated-constructor' => array( 'label' => __( "Deprecated Constructor", "coreactivity" ) ),
			'deprecated-hook-run'    => array( 'label' => __( "Deprecated Hook Run", "coreactivity" ) )
		);
	}

	public function event_doing_it_wrong( $error ) {
		$this->log( 'doing-it-wrong', array(), $error );
	}

	public function event_deprecated_function( $error ) {
		$this->log( 'deprecated-function', array(), $error );
	}

	public function event_deprecated_file( $error ) {
		$this->log( 'deprecated-file', array(), $error );
	}

	public function event_deprecated_argument( $error ) {
		$this->log( 'deprecated-argument', array(), $error );
	}

	public function event_deprecated_constructor( $error ) {
		$this->log( 'deprecated-constructor', array(), $error );
	}

	public function event_deprecated_hook_run( $error ) {
		$this->log( 'deprecated-hook-run', array(), $error );
	}
}
