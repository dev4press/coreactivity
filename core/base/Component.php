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

	public function __construct() {
		add_action( 'coreactivity_component_registration', array( $this, 'register' ) );
		add_action( 'coreactivity_tracking_ready', array( $this, 'tracking' ) );
		add_action( 'coreactivity_init', array( $this, 'init' ) );
	}

	/** @return static */
	public static function instance() {
		static $instance = array();

		if ( ! isset( $instance[ static::class ] ) ) {
			$instance[ static::class ] = new static();
		}

		return $instance[ static::class ];
	}

	public function register( Init $init ) {
		foreach ( $this->events() as $event => $data ) {
			$event  = strtolower( $event );
			$status = $init->register( $this->category, $this->code(), $this->label(), $event, $data[ 'label' ], $data[ 'scope' ] ?? $this->scope, $data[ 'status' ] ?? 'active', $data[ 'object_type' ] ?? $this->object_type, $data[ 'rules' ] ?? array() );

			if ( $status ) {
				$this->registered[] = $event;
			}
		}
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
