<?php

namespace Dev4Press\Plugin\CoreActivity\Log;

use Dev4Press\Plugin\CoreActivity\Basic\Cache;
use Dev4Press\Plugin\CoreActivity\Basic\DB;
use Dev4Press\Plugin\CoreActivity\Components\Error;
use Dev4Press\Plugin\CoreActivity\Components\Plugin;
use Dev4Press\Plugin\CoreActivity\Components\Post;
use Dev4Press\Plugin\CoreActivity\Components\Theme;
use Dev4Press\Plugin\CoreActivity\Components\User;
use Dev4Press\v42\Core\Quick\Sanitize;
use Dev4Press\v42\Core\Quick\Str;
use stdClass;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Init {
	private $events = array();
	private $components = array();
	private $list = array();
	private $object_types = array();

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
			$this->list[ $event->event_id ]                     = array( 'name' => $event->event, 'label' => $event->event );
		}
	}

	private function _init_components() {
		Error::instance();
		Plugin::instance();
		Theme::instance();
		User::instance();
		Post::instance();
	}

	public function ready() {
		$this->_init_events();
		$this->_init_components();

		do_action( 'coreactivity_component_registration', $this );

		Cache::instance()->set( 'events', 'registered', $this->events );

		do_action( 'coreactivity_tracking_ready', $this );

		$this->object_types = apply_filters( 'coreactivity_registered_object_types', array(
			'post'    => __( "Post", "coreactivity" ),
			'term'    => __( "Term", "coreactivity" ),
			'comment' => __( "Comment", "coreactivity" ),
			'user'    => __( "User", "coreactivity" ),
			'plugin'  => __( "Plugin", "coreactivity" ),
			'theme'   => __( "Theme", "coreactivity" )
		) );
	}

	public function event( string $component, string $event ) {
		return $this->events[ $component ][ $event ] ?? null;
	}

	public function events() : array {
		return $this->events;
	}

	public function components() : array {
		return $this->components;
	}

	public function object_types() : array {
		return $this->object_types;
	}

	public function events_list() : array {
		return $this->list;
	}

	public function get_event_id( string $component, string $event ) : int {
		if ( isset( $this->events[ $component ][ $event ] ) ) {
			return $this->events[ $component ][ $event ]->event_id;
		}

		return 0;
	}

	public function get_event_description( string $component, string $event ) : string {
		if ( isset( $this->events[ $component ][ $event ] ) ) {
			if ( $this->events[ $component ][ $event ]->loaded ) {
				return $this->events[ $component ][ $event ]->component_label . ' / ' . $this->events[ $component ][ $event ]->label;
			}
		}

		return 'N/A';
	}

	public function get_event_by_id( int $event_id ) : ?stdClass {
		foreach ( $this->events as $events ) {
			foreach ( $events as $obj ) {
				if ( $obj->event_id == $event_id ) {
					return $obj;
				}
			}
		}

		return null;
	}

	public function get_component_label( string $component ) {
		return $this->components[ $component ] ?? $component;
	}

	public function get_object_type_label( string $object_type ) {
		return $this->object_types[ $object_type ] ?? Str::slug_to_name( $object_type );
	}

	public function get_event_label( int $event_id, string $event ) {
		return $this->list[ $event_id ][ 'label' ] ?? $event;
	}

	public function is_event_loaded( string $component, string $event ) : bool {
		if ( isset( $this->events[ $component ][ $event ] ) ) {
			return $this->events[ $component ][ $event ]->loaded;
		}

		return false;
	}

	public function is_component_valid( string $component ) : bool {
		return isset( $this->events[ $component ] );
	}

	public function is_event_id_valid( int $event_id ) : bool {
		return isset( $this->list[ $event_id ] );
	}

	public function event_status( int $event_id ) : string {
		$status = '';

		foreach ( $this->events as $component => $events ) {
			foreach ( $events as $event => $obj ) {
				if ( $obj->event_id == $event_id ) {
					$status = $obj->status;
					break 2;
				}
			}
		}

		return in_array( $status, array( 'active', 'inactive' ) ) ? $status : '';
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

		if ( ! isset( $this->components[ $component ] ) ) {
			$this->components[ $component ] = $component_label;
		}

		if ( isset( $this->events[ $component ][ $event ] ) ) {
			$this->events[ $component ][ $event ]->loaded          = true;
			$this->events[ $component ][ $event ]->label           = $label;
			$this->events[ $component ][ $event ]->scope           = $scope;
			$this->events[ $component ][ $event ]->object_type     = $object_type;
			$this->events[ $component ][ $event ]->component_label = $component_label;

			$obj->event_id                           = $this->events[ $component ][ $event ]->event_id;
			$this->list[ $obj->event_id ][ 'label' ] = $label;
		} else {
			$id = DB::instance()->add_new_event( $component, $event, $status, $rules );

			if ( $id > 0 ) {
				$obj->event_id = $id;

				$this->events[ $component ][ $event ] = $obj;
				$this->list[ $obj->event_id ]         = array( 'name' => $event, 'label' => $label );
			}
		}

		return $obj->event_id > 0;
	}
}
