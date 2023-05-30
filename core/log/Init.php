<?php

namespace Dev4Press\Plugin\CoreActivity\Log;

use Dev4Press\Plugin\CoreActivity\Basic\Cache;
use Dev4Press\Plugin\CoreActivity\Basic\DB;
use Dev4Press\Plugin\CoreActivity\Components\Error;
use Dev4Press\Plugin\CoreActivity\Components\Post;
use Dev4Press\Plugin\CoreActivity\Components\User;
use Dev4Press\v42\Core\Quick\Sanitize;
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

			$event->event_id = Sanitize::absint( $event->event_id );
			$event->rules    = Str::is_json( $event->rules, false ) ? json_decode( $event->rules, true ) : array();
			$event->loaded   = false;

			$this->events[ $event->component ][ $event->event ] = $event;
		}
	}

	private function _init_components() {
		Error::instance();
		User::instance();
		Post::instance();
	}

	public function ready() {
		$this->_init_events();
		$this->_init_components();

		do_action( 'coreactivity_component_registration', $this );

		Cache::instance()->set( 'events', 'registered', $this->events );

		do_action( 'coreactivity_tracking_ready', $this );
	}

	public function get_event_id( string $component, string $event ) : int {
		if ( isset( $this->events[ $component ][ $event ] ) ) {
			return $this->events[ $component ][ $event ]->event_id;
		}

		return 0;
	}

	public function register( string $component, string $component_label, string $event, string $label, string $scope = '', string $status = 'active', string $object_type = '', array $rules = array() ) : bool {
		$obj = (object) array(
			'event_id'        => 0,
			'component'       => $component,
			'component_label' => $component_label,
			'event'           => $event,
			'status'          => $status,
			'rules'           => $rules,
			'label'           => $label,
			'loaded'          => true,
			'scope'           => $scope,
			'object_type'     => $object_type
		);

		if ( isset( $this->events[ $component ][ $event ] ) ) {
			$this->events[ $component ][ $event ]->loaded          = true;
			$this->events[ $component ][ $event ]->label           = $label;
			$this->events[ $component ][ $event ]->object_type     = $object_type;
			$this->events[ $component ][ $event ]->component_label = $component_label;

			$obj->event_id = $this->events[ $component ][ $event ]->event_id;
		} else {
			$id = DB::instance()->add_new_event( $component, $event, $status, $rules );

			if ( $id > 0 ) {
				$obj->event_id = $id;

				$this->events[ $component ][ $event ] = $obj;
			}
		}

		return $obj->event_id > 0;
	}
}
