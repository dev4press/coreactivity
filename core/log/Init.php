<?php

namespace Dev4Press\Plugin\CoreActivity\Log;

use Dev4Press\Plugin\CoreActivity\Basic\Cache;
use Dev4Press\Plugin\CoreActivity\Basic\DB;
use Dev4Press\Plugin\CoreActivity\Components\User;
use Dev4Press\v42\Core\Quick\Str;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Init {
	private $events;

	public function __construct() {
		add_action( 'coreactivity_plugin_core_ready', array( $this, 'ready' ), 15 );
	}

	public static function instance() : Init {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new Init();
		}

		return $instance;
	}

	private function _init_events() {
		$events = Cache::instance()->get_all_registered_events();

		foreach ( $events as $event ) {
			if ( ! isset( $this->events[ $event->component ] ) ) {
				$this->events[ $event->component ] = array();
			}

			$event->rules  = Str::is_json( $event->rules, false ) ? json_decode( $event->rules, true ) : array();
			$event->loaded = false;

			$this->events[ $event->component ][ $event->event ] = $event;
		}
	}

	private function _init_components() {
		User::instance();
	}

	public function ready() {
		$this->_init_events();
		$this->_init_components();

		do_action( 'coreactivity_component_registration', $this );

		Cache::instance()->set( 'events', 'registered', $this->events );
	}

	public function register( string $component, string $event, string $label, string $scope = '', string $status = 'active', array $rules = array() ) : bool {
		$obj = (object) array(
			'event_id'  => 0,
			'component' => $component,
			'event'     => $event,
			'scope'     => $scope,
			'status'    => $status,
			'rules'     => $rules,
			'label'     => $label,
			'loaded'    => true
		);

		if ( isset( $this->events[ $component ][ $event ] ) ) {
			$this->events[ $component ][ $event ]->loaded = true;
			$this->events[ $component ][ $event ]->label  = $label;

			$obj->event_id = $this->events[ $component ][ $event ]->event_id;
		} else {
			$id = DB::instance()->add_new_event( $component, $event, $scope, $status, $rules );

			if ( $id > 0 ) {
				$obj->event_id = $id;

				$this->events[ $component ][ $event ] = $obj;
			}
		}

		return $obj->event_id > 0;
	}
}
