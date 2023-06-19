<?php

namespace Dev4Press\Plugin\CoreActivity\Base;

use Dev4Press\Plugin\CoreActivity\Log\Core;
use Dev4Press\Plugin\CoreActivity\Log\Init;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

abstract class Component {
	/**
	 * Allows only Lowercase alphabet letters and numbers and minus sign. No spaces or underscores. It has to start with a letter.
	 *
	 * @var string
	 */
	protected $plugin = '';
	/**
	 * Allows only Lowercase alphabet letters and numbers and minus sign. No spaces or underscores. It has to start with a letter.
	 *
	 * @var string
	 */
	protected $name = '';
	/**
	 * Name of the object type to use to add to the logging table. Individual events can override it.
	 *
	 * @var string
	 */
	protected $object_type = '';
	protected $icon = 'ui-folder';
	protected $scope = '';
	protected $category = 'wordpress';
	/**
	 * @var array
	 */
	protected $events = array();
	/**
	 * @var array
	 */
	protected $registered = array();
	protected $is_security = false;
	protected $is_malicious = false;

	public function __construct() {
		add_action( 'coreactivity_component_registration', array( $this, 'register_component' ) );
		add_action( 'coreactivity_events_registration', array( $this, 'register_events' ) );

		if ( $this->is_available() ) {
			add_action( 'coreactivity_registered_object_types', array( $this, 'registered_object_types' ) );
			add_action( 'coreactivity_tracking_ready', array( $this, 'tracking' ) );
			add_action( 'coreactivity_init', array( $this, 'init' ) );
		}
	}

	/** @return static */
	public static function instance() {
		static $instance = array();

		if ( ! isset( $instance[ static::class ] ) ) {
			$instance[ static::class ] = new static();
		}

		return $instance[ static::class ];
	}

	public function is_available() : bool {
		return true;
	}

	public function register_component( Init $init ) {
		$init->register_component( $this->category, $this->code(), array(
			'label'        => $this->label(),
			'icon'         => $this->icon,
			'is_available' => $this->is_available()
		) );
	}

	public function register_events( Init $init ) {
		foreach ( $this->events() as $event => $data ) {
			$event  = strtolower( $event );
			$status = $init->register_event( $this->code(), $event, array(
				'label'        => $data[ 'label' ],
				'status'       => $data[ 'status' ] ?? 'active',
				'scope'        => $data[ 'scope' ] ?? $this->scope,
				'object_type'  => $data[ 'object_type' ] ?? $this->object_type,
				'is_security'  => $data[ 'is_security' ] ?? $this->is_security,
				'is_malicious' => $data[ 'is_malicious' ] ?? $this->is_malicious,
				'level'        => $data[ 'level' ] ?? 0
			), $data[ 'rules' ] ?? array() );

			if ( $status ) {
				$this->registered[] = $event;
			}
		}
	}

	public function registered_object_types( array $object_types ) : array {
		return $object_types;
	}

	public function code() : string {
		return strtolower( $this->plugin . '/' . $this->name );
	}

	public function events() : array {
		if ( empty( $this->events ) ) {
			$this->events = $this->get_events();
		}

		return $this->events;
	}

	public function init() {

	}

	public function log( string $event, array $data = array(), array $meta = array() ) : int {
		if ( $this->is_active( $event ) ) {
			$event_id = Init::instance()->get_event_id( $this->code(), $event );

			if ( $event_id > 0 ) {
				if ( ( isset( $data[ 'object_id' ] ) || isset( $data[ 'object_name' ] ) ) && ! isset( $data[ 'object_type' ] ) ) {
					$data[ 'object_type' ] = $this->object_type;
				}

				return Core::instance()->log( $event_id, $data, $meta );
			}
		}

		return 0;
	}

	public function is_registered( string $event ) : bool {
		return in_array( strtolower( $event ), $this->registered );
	}

	public function is_active( string $event ) : bool {
		return $this->is_registered( $event );
	}

	public function are_active( array $events, bool $any = true ) : bool {
		if ( $any ) {
			$is = false;

			foreach ( $events as $event ) {
				if ( $this->is_active( $event ) ) {
					$is = true;
					break;
				}
			}
		} else {
			$is = true;

			foreach ( $events as $event ) {
				if ( ! $this->is_active( $event ) ) {
					$is = false;
					break;
				}
			}
		}

		return $is;
	}

	protected function find_differences( array $old, array $new ) : array {
		$diff = array(
			'added'    => array(),
			'removed'  => array(),
			'modified' => array()
		);

		foreach ( $new as $key => $value ) {
			if ( ! isset( $old[ $key ] ) ) {
				$diff[ 'added' ][ $key ] = $value;
			}
		}

		foreach ( $old as $key => $value ) {
			if ( ! isset( $new[ $key ] ) ) {
				$diff[ 'removed' ][ $key ] = $value;
			}
		}

		foreach ( $old as $key => $value ) {
			if ( $new[ $key ] != $value ) {
				$diff[ 'modified' ][ $key ] = $value;
			}
		}

		return array_filter( $diff );
	}

	abstract public function tracking();

	abstract public function label() : string;

	abstract protected function get_events() : array;
}
