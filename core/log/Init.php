<?php

namespace Dev4Press\Plugin\CoreActivity\Log;

use Dev4Press\Plugin\CoreActivity\Basic\Cache;
use Dev4Press\Plugin\CoreActivity\Basic\DB;
use Dev4Press\Plugin\CoreActivity\Components\Attachment;
use Dev4Press\Plugin\CoreActivity\Components\Comment;
use Dev4Press\Plugin\CoreActivity\Components\Error;
use Dev4Press\Plugin\CoreActivity\Components\Internal;
use Dev4Press\Plugin\CoreActivity\Components\Network;
use Dev4Press\Plugin\CoreActivity\Components\Notification;
use Dev4Press\Plugin\CoreActivity\Components\Option;
use Dev4Press\Plugin\CoreActivity\Components\Plugin;
use Dev4Press\Plugin\CoreActivity\Components\Post;
use Dev4Press\Plugin\CoreActivity\Components\Sitemeta;
use Dev4Press\Plugin\CoreActivity\Components\Term;
use Dev4Press\Plugin\CoreActivity\Components\Theme;
use Dev4Press\Plugin\CoreActivity\Components\User;
use Dev4Press\Plugin\CoreActivity\Components\WordPress;
use Dev4Press\Plugin\CoreActivity\Plugins\bbPress;
use Dev4Press\Plugin\CoreActivity\Plugins\BuddyPress;
use Dev4Press\Plugin\CoreActivity\Plugins\DebugPress;
use Dev4Press\Plugin\CoreActivity\Plugins\DuplicatePost;
use Dev4Press\Plugin\CoreActivity\Plugins\GravityForms;
use Dev4Press\Plugin\CoreActivity\Plugins\Jetpack;
use Dev4Press\Plugin\CoreActivity\Plugins\SweepPress;
use Dev4Press\Plugin\CoreActivity\Plugins\UserSwitching;
use Dev4Press\v42\Core\Quick\Sanitize;
use Dev4Press\v42\Core\Quick\Str;
use stdClass;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Init {
	public $statistics = array(
		'components' => array(
			'total'     => 0,
			'available' => 0
		),
		'events'     => array(
			'total'     => 0,
			'available' => 0
		)
	);

	private $components = array();
	private $events = array();
	private $categories = array();
	private $list = array();
	private $object_types = array();

	public function __construct() {
		add_action( 'coreactivity_plugin_core_ready', array( $this, 'ready' ), 15 );
		add_action( 'coreactivity_init', array( $this, 'init' ), 1 );
	}

	public static function instance() : Init {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new Init();
		}

		return $instance;
	}

	public function ready() {
		$this->categories = array(
			'wordpress' => __( "WordPress", "coreactivity" ),
			'internal'  => __( "Internal", "coreactivity" ),
			'plugin'    => __( "Plugin", "coreactivity" )
		);

		$this->_init_events();
		$this->_init_components();
		$this->_init_plugins();

		do_action( 'coreactivity_component_registration', $this );
		do_action( 'coreactivity_events_registration', $this );

		Cache::instance()->set( 'events', 'registered', $this->events );

		$this->object_types = apply_filters( 'coreactivity_registered_object_types', array(
			'post'         => __( "Post", "coreactivity" ),
			'attachment'   => __( "Attachment", "coreactivity" ),
			'term'         => __( "Term", "coreactivity" ),
			'comment'      => __( "Comment", "coreactivity" ),
			'user'         => __( "User", "coreactivity" ),
			'plugin'       => __( "Plugin", "coreactivity" ),
			'theme'        => __( "Theme", "coreactivity" ),
			'cron'         => __( "Cron", "coreactivity" ),
			'notification' => __( "Notification", "coreactivity" )
		) );

		foreach ( $this->components as $component ) {
			$this->statistics[ 'components' ][ 'total' ] ++;

			if ( $component->is_available ) {
				$this->statistics[ 'components' ][ 'available' ] ++;
			}
		}

		foreach ( $this->events as $name => $component ) {
			foreach ( $component as $events ) {
				$this->statistics[ 'events' ][ 'total' ] ++;

				if ( $this->components[ $name ]->is_available ) {
					$this->statistics[ 'events' ][ 'available' ] ++;
				}
			}
		}

		do_action( 'coreactivity_tracking_ready', $this );
	}

	public function init() {

	}

	public function get_event( string $component, string $event ) {
		return $this->events[ $component ][ $event ] ?? null;
	}

	public function get_all_events() : array {
		return $this->events;
	}

	public function get_all_components() : array {
		return $this->components;
	}

	public function get_all_categories() : array {
		return $this->categories;
	}

	public function get_object_types() : array {
		return $this->object_types;
	}

	public function get_event_id( string $component, string $event ) : int {
		if ( isset( $this->events[ $component ][ $event ] ) ) {
			return $this->events[ $component ][ $event ]->event_id;
		}

		return 0;
	}

	public function get_event_description( string $component, string $event ) : string {
		if ( isset( $this->events[ $component ][ $event ] ) ) {
			if ( $this->components[ $component ]->is_available ) {
				return $this->components[ $component ]->label . ' / ' . $this->events[ $component ][ $event ]->label;
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

	public function get_component_label( string $component ) : string {
		return $this->components[ $component ] ? $this->components[ $component ]->label : $component;
	}

	public function get_component_icon( string $component ) : string {
		return $this->components[ $component ] ? $this->components[ $component ]->icon : 'ui-folder';
	}

	public function get_object_type_label( string $object_type ) {
		return $this->object_types[ $object_type ] ?? Str::slug_to_name( $object_type );
	}

	public function get_event_label( int $event_id, string $event ) {
		return $this->list[ $event_id ][ 'label' ] ?? $event;
	}

	public function get_select_events( bool $simplified = false, string $only_component = '' ) : array {
		$list = array();

		foreach ( $this->events as $component => $events ) {
			if ( ! empty( $only_component ) && $only_component != $component ) {
				continue;
			}

			foreach ( $events as $event ) {
				if ( ! isset( $list[ $component ] ) ) {
					$list[ $component ] = array(
						'title'  => $simplified ? ( $this->components[ $component ] ? $this->components[ $component ]->label : $component ) : $component,
						'values' => array()
					);
				}

				$list[ $component ][ 'values' ][ $event->event_id ] = $simplified ? ( empty( $event->label ) ? Str::slug_to_name( $event->event, '-' ) : $event->label ) : $event->event;
			}
		}

		return array_values( $list );
	}

	public function get_select_event_components( bool $simplified = false ) : array {
		$list = array(
			'wordpress' => array(
				'title'  => $simplified ? __( "WordPress", "coreactivity" ) : 'wordpress',
				'values' => array()
			),
			'internal'  => array(
				'title'  => $simplified ? __( "Internal", "coreactivity" ) : 'internal',
				'values' => array()
			),
			'plugin'    => array(
				'title'  => $simplified ? __( "Plugins", "coreactivity" ) : 'plugin',
				'values' => array()
			)
		);

		foreach ( $this->components as $component => $obj ) {
			$list[ $obj->category ][ 'values' ][ $component ] = $simplified ? $obj->label : $component;
		}

		if ( empty( $list[ 'plugin' ][ 'values' ] ) ) {
			unset( $list[ 'plugin' ] );
		}

		return array_values( $list );
	}

	public function is_event_available( string $component, string $event ) : bool {
		if ( isset( $this->events[ $component ][ $event ] ) ) {
			return $this->components[ $component ]->is_available;
		}

		return false;
	}

	public function is_event_active( string $component, string $event ) : bool {
		if ( isset( $this->events[ $component ][ $event ] ) ) {
			return $this->components[ $component ]->is_available && $this->events[ $component ][ $event ]->status == 'active';
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

		foreach ( $this->events as $events ) {
			foreach ( $events as $obj ) {
				if ( $obj->event_id == $event_id ) {
					$status = $obj->status;
					break 2;
				}
			}
		}

		return in_array( $status, array( 'active', 'inactive' ) ) ? $status : '';
	}

	public function register_component( string $category, string $component, array $args = array() ) {
		$this->components[ $component ] = (object) array(
			'category'     => $category,
			'component'    => $component,
			'label'        => $args[ 'label' ] ?? $this->_generate_component_label( $component ),
			'icon'         => $args[ 'icon' ] ?? 'ui-folder',
			'is_available' => $args[ 'is_available' ] ?? false
		);
	}

	public function register_event( string $component, string $event, array $args = array(), array $rules = array() ) : bool {
		$obj = (object) array(
			'event_id'     => 0,
			'component'    => $component,
			'event'        => $event,
			'rules'        => $rules,
			'status'       => $args[ 'status' ] ?? 'active',
			'scope'        => $args[ 'scope' ] ?? '',
			'label'        => $args[ 'label' ] ?? Str::slug_to_name( $event, '-' ),
			'object_type'  => $args[ 'object_type' ] ?? '',
			'is_security'  => $args[ 'is_security' ] ?? false,
			'is_malicious' => $args[ 'is_malicious' ] ?? false,
			'level'        => $args[ 'level' ] ?? 0
		);

		if ( isset( $this->events[ $component ][ $event ] ) ) {
			$obj->status   = $this->events[ $component ][ $event ]->status;
			$obj->rules    = $this->events[ $component ][ $event ]->rules;
			$obj->event_id = $this->events[ $component ][ $event ]->event_id;

			$this->events[ $component ][ $event ] = $obj;

			$this->list[ $obj->event_id ] = array(
				'name'  => $event,
				'label' => $obj->label
			);
		} else {
			$category = $this->components[ $component ]->category;

			$id = DB::instance()->add_new_event( $category, $component, $event, $obj->status, $rules );

			if ( $id > 0 ) {
				$obj->event_id = $id;

				$this->events[ $component ][ $event ] = $obj;

				$this->list[ $obj->event_id ] = array(
					'name'  => $event,
					'label' => $obj->label
				);
			}
		}

		return $obj->event_id > 0;
	}

	private function _generate_component_label( string $component ) : string {
		$parts = explode( '/', $component );

		return isset( $parts[ 1 ] ) ? Str::slug_to_name( $parts[ 1 ], '-' ) : $component;
	}

	private function _init_events() {
		$events = Cache::instance()->get_all_registered_events();

		foreach ( $events as $event ) {
			if ( ! isset( $this->events[ $event->component ] ) ) {
				$this->components[ $event->component ] = (object) array(
					'category'     => $event->category,
					'component'    => $event->component,
					'label'        => $this->_generate_component_label( $event->component ),
					'icon'         => 'ui-folder',
					'is_available' => false
				);

				$this->events[ $event->component ] = array();
			}

			$event->event_id = Sanitize::absint( $event->event_id );
			$event->rules    = Str::is_json( $event->rules, false ) ? json_decode( $event->rules, true ) : array();

			$this->events[ $event->component ][ $event->event ] = $event;

			$this->list[ $event->event_id ] = array(
				'name'  => $event->event,
				'label' => $event->event
			);
		}
	}

	private function _init_components() {
		Internal::instance();
		Network::instance();
		Sitemeta::instance();
		WordPress::instance();
		Option::instance();
		Notification::instance();
		Error::instance();
		Plugin::instance();
		Theme::instance();
		User::instance();
		Post::instance();
		Term::instance();
		Comment::instance();
		Attachment::instance();
	}

	private function _init_plugins() {
		DebugPress::instance();
		DuplicatePost::instance();
		UserSwitching::instance();
		SweepPress::instance();
		GravityForms::instance();
		Jetpack::instance();
		BuddyPress::instance();
		bbPress::instance();
	}
}
