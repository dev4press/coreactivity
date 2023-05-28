<?php

namespace Dev4Press\Plugin\CoreActivity\Base;

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
	 * @var array
	 */
	protected $events = array();
	/**
	 * @var array
	 */
	protected $registered = array();

	public function __construct() {
		add_action( 'coreactivity_component_registration', array( $this, 'register' ) );
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
		debugpress_store_object($this);
		foreach ( $this->events() as $event => $data ) {
			$event  = strtolower( $event );
			$status = $init->register( $this->code(), $event, $data[ 'label' ], $data[ 'scope' ] ?? '', $data[ 'status' ] ?? 'active', $data[ 'rules' ] ?? array() );

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

	public function is_registered( string $event ) : bool {
		return in_array( strtolower( $event ), $this->registered );
	}

	public function is_active( string $event ) : bool {
		return $this->is_registered( $event );
	}

	abstract public function label() : string;

	abstract protected function get_events() : array;
}
