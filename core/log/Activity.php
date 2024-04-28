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
use Dev4Press\Plugin\CoreActivity\Components\Privacy;
use Dev4Press\Plugin\CoreActivity\Components\Sitemeta;
use Dev4Press\Plugin\CoreActivity\Components\Term;
use Dev4Press\Plugin\CoreActivity\Components\Theme;
use Dev4Press\Plugin\CoreActivity\Components\User;
use Dev4Press\Plugin\CoreActivity\Components\WordPress;
use Dev4Press\Plugin\CoreActivity\Plugins\bbPress;
use Dev4Press\Plugin\CoreActivity\Plugins\BuddyPress;
use Dev4Press\Plugin\CoreActivity\Plugins\ContactForm7;
use Dev4Press\Plugin\CoreActivity\Plugins\DebugPress;
use Dev4Press\Plugin\CoreActivity\Plugins\DuplicatePost;
use Dev4Press\Plugin\CoreActivity\Plugins\Forminator;
use Dev4Press\Plugin\CoreActivity\Plugins\GDForumManager;
use Dev4Press\Plugin\CoreActivity\Plugins\GravityForms;
use Dev4Press\Plugin\CoreActivity\Plugins\Jetpack;
use Dev4Press\Plugin\CoreActivity\Plugins\SweepPress;
use Dev4Press\Plugin\CoreActivity\Plugins\UserSwitching;
use Dev4Press\Plugin\CoreActivity\Plugins\WooCommerce;
use Dev4Press\v48\Core\Quick\Str;
use stdClass;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Activity {
	public $statistics = array(
		'components' => array(
			'total'     => 0,
			'available' => 0,
		),
		'events'     => array(
			'total'     => 0,
			'available' => 0,
			'active'    => 0,
			'inactive'  => 0,
		),
	);

	private $sources = array();
	private $components = array();
	private $events = array();
	private $categories = array();
	private $list = array();
	private $object_types = array();

	public function __construct() {
		add_action( 'coreactivity_plugin_core_ready', array( $this, 'ready' ), 15 );
		add_action( 'coreactivity_prepare', array( $this, 'prepare' ), 15 );
		add_action( 'coreactivity_init', array( $this, 'init' ), 1 );
	}

	public static function instance() : Activity {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new Activity();
		}

		return $instance;
	}

	public function ready() {
		$this->categories = array(
			'internal'  => __( 'Internal', 'coreactivity' ),
			'wordpress' => __( 'WordPress', 'coreactivity' ),
			'plugin'    => __( 'Plugin', 'coreactivity' ),
		);

		Upgrader::instance();

		$this->_init_events();
		$this->_init_components();
		$this->_init_plugins();
	}

	public function prepare() {
		do_action( 'coreactivity_component_registration', $this );
		do_action( 'coreactivity_events_registration', $this );

		Cache::instance()->set( 'events', 'registered', $this->events );

		$this->object_types = apply_filters( 'coreactivity_registered_object_types', array(
			'post'         => __( 'Post', 'coreactivity' ),
			'post-meta'    => __( 'Post Meta', 'coreactivity' ),
			'attachment'   => __( 'Attachment', 'coreactivity' ),
			'term'         => __( 'Term', 'coreactivity' ),
			'term-meta'    => __( 'Term Meta', 'coreactivity' ),
			'comment'      => __( 'Comment', 'coreactivity' ),
			'comment-meta' => __( 'Comment Meta', 'coreactivity' ),
			'user'         => __( 'User', 'coreactivity' ),
			'user-meta'    => __( 'User Meta', 'coreactivity' ),
			'plugin'       => __( 'Plugin', 'coreactivity' ),
			'theme'        => __( 'Theme', 'coreactivity' ),
			'cron'         => __( 'Cron', 'coreactivity' ),
			'option'       => __( 'Option', 'coreactivity' ),
			'sitemeta'     => __( 'Site Meta', 'coreactivity' ),
			'transient'    => __( 'Transient', 'coreactivity' ),
			'notification' => __( 'Notification', 'coreactivity' ),
		) );

		foreach ( $this->components as $component ) {
			$this->statistics['components']['total'] ++;

			if ( $component->is_available ) {
				$this->statistics['components']['available'] ++;
			}
		}

		foreach ( $this->events as $name => $component ) {
			foreach ( $component as $event ) {
				$this->statistics['events']['total'] ++;

				if ( $this->components[ $name ]->is_available ) {
					$this->statistics['events']['available'] ++;

					if ( $event->status == 'active' ) {
						$this->statistics['events']['active'] ++;
					} else {
						$this->statistics['events']['inactive'] ++;
					}
				}
			}
		}

		do_action( 'coreactivity_tracking_ready', $this );
	}

	public function init() {
	}

	public function is_instant_notification_enabled( int $event_id ) : bool {
		$event = $this->get_event_by_id( $event_id );

		if ( $event ) {
			if ( $event->notifications['instant'] ) {
				return true;
			}
		}

		return false;
	}

	public function get_events_with_notifications( string $notification ) : array {
		$ids = array();

		foreach ( $this->events as $events ) {
			foreach ( $events as $event ) {
				if ( $event->notifications[ $notification ] ) {
					$ids[] = $event->event_id;
				}
			}
		}

		return $ids;
	}

	public function get_component( string $component ) {
		return $this->components[ $component ] ?? null;
	}

	public function get_event( string $component, string $event ) {
		return $this->events[ $component ][ $event ] ?? null;
	}

	public function get_event_id( string $component, string $event ) : int {
		if ( isset( $this->events[ $component ][ $event ] ) ) {
			return $this->events[ $component ][ $event ]->event_id;
		}

		return 0;
	}

	public function get_all_events() : array {
		return $this->events;
	}

	public function get_all_components() : array {
		return $this->components;
	}

	public function get_all_sources() : array {
		return $this->sources;
	}

	public function get_all_categories() : array {
		return $this->categories;
	}

	public function get_object_types() : array {
		return $this->object_types;
	}

	public function get_object_type_label( string $object_type ) {
		return $this->object_types[ $object_type ] ?? Str::slug_to_name( $object_type );
	}

	public function get_event_description( string $component, string $event ) : string {
		if ( isset( $this->events[ $component ][ $event ] ) ) {
			if ( $this->components[ $component ]->is_available ) {
				return $this->components[ $component ]->label . ' / ' . $this->events[ $component ][ $event ]->label;
			}
		}

		return 'N/A';
	}

	public function get_event_notifications( string $component, string $event ) : array {
		if ( isset( $this->events[ $component ][ $event ] ) ) {
			return $this->events[ $component ][ $event ]->notifications ?? array();
		}

		return array();
	}

	public function get_event_display( int $event_id ) : string {
		$render = '';
		$event  = $this->get_event_by_id( $event_id );

		if ( ! is_null( $event ) ) {
			$render = '[' . $event->component . '] ' . $event->event;
		}

		return $render;
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

	public function get_event_label( int $event_id, string $event ) {
		return $this->list[ $event_id ]['label'] ?? $event;
	}

	public function get_plugin_label( string $plugin ) {
		return $this->sources[ $plugin ] ?? $plugin;
	}

	public function get_events_for_component( string $component ) : array {
		return $this->events[ $component ] ?? array();
	}

	public function get_event_ids_for_component( string $component ) : array {
		$ids = array();

		if ( isset( $this->events[ $component ] ) ) {
			foreach ( $this->events[ $component ] as $event ) {
				if ( $event->event_id > 0 ) {
					$ids[] = $event->event_id;
				}
			}
		}

		return $ids;
	}

	public function get_component_label( string $component ) : string {
		return $this->components[ $component ] ? $this->components[ $component ]->label : $component;
	}

	public function get_component_icon( string $component ) : string {
		return $this->components[ $component ] ? $this->components[ $component ]->icon : 'ui-folder';
	}

	public function get_components_by_plugin_source( $plugin ) : array {
		$list = array();

		foreach ( $this->components as $component => $obj ) {
			if ( isset( $obj->plugin ) && $obj->plugin == $plugin ) {
				$list[] = $component;
			}
		}

		return $list;
	}

	public function get_select_events( bool $simplified = false, array $only_components = array() ) : array {
		$list = array();

		$single_component = ! empty( $only_components ) && count( $only_components ) == 1 ? $only_components[0] : '';

		foreach ( $this->events as $component => $events ) {
			if ( ! empty( $only_components ) && ! in_array( $component, $only_components ) ) {
				continue;
			}

			foreach ( $events as $event ) {
				if ( ! isset( $list[ $component ] ) ) {
					$list[ $component ] = array(
						'title'  => $simplified ? ( $this->components[ $component ] ? $this->components[ $component ]->label : $component ) : $component,
						'values' => array(),
					);
				}

				$list[ $component ]['values'][ $event->event_id ] = $simplified ? ( empty( $event->label ) ? Str::slug_to_name( $event->event, '-' ) : $event->label ) : $event->event;
			}
		}

		if ( ! empty( $single_component ) ) {
			return $list[ $single_component ]['values'];
		} else {
			return array_values( $list );
		}
	}

	public function get_select_event_components( bool $simplified = false ) : array {
		$list = array(
			'wordpress' => array(
				'title'  => $simplified ? __( 'WordPress', 'coreactivity' ) : 'wordpress',
				'values' => array(),
			),
			'internal'  => array(
				'title'  => $simplified ? __( 'Internal', 'coreactivity' ) : 'internal',
				'values' => array(),
			),
			'plugin'    => array(
				'title'  => $simplified ? __( 'Plugins', 'coreactivity' ) : 'plugin',
				'values' => array(),
			),
		);

		foreach ( $this->components as $component => $obj ) {
			$list[ $obj->category ]['values'][ $component ] = $simplified ? $obj->label : $component;
		}

		if ( empty( $list['plugin']['values'] ) ) {
			unset( $list['plugin'] );
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

	public function can_event_skip_duplicates( int $event_id ) {
		$event = $this->get_event_by_id( $event_id );

		return $event ? $event->skip_duplicates : false;
	}

	public function get_event_skip_duplicates_request( int $event_id ) {
		$event = $this->get_event_by_id( $event_id );

		return $event ? $event->skip_duplicates_request : false;
	}

	public function is_event_object_linked( int $event_id = 0, string $component = '', string $event = '' ) : bool {
		if ( $event_id > 0 ) {
			$e = $this->list[ $event_id ] ?? array();

			$component = $e['component'] ?? '';
			$event     = $e['name'] ?? '';
		}

		if ( isset( $this->events[ $component ][ $event ] ) ) {
			return ! empty( $this->events[ $component ][ $event ]->object_type );
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

	public function event_notification_toggle( int $event_id, string $notification, string $status = '' ) : ?bool {
		$event = $this->list[ $event_id ] ?? array();

		if ( ! empty( $event ) ) {
			$event = $this->events[ $event['component'] ][ $event['name'] ];

			if ( isset( $event->notifications[ $notification ] ) ) {
				$status = empty( $status ) ? ! $event->notifications[ $notification ] : ( $status === 'on' );

				$event->notifications[ $notification ] = $status;

				$rules                  = $event->rules;
				$rules['notifications'] = $event->notifications;

				DB::instance()->change_event_rules( $event_id, $rules );

				return $event->notifications[ $notification ];
			}
		}

		return null;
	}

	public function events_notification_bulk_control( $instant = 'skip', $daily = 'skip', $weekly = 'skip' ) {
		foreach ( $this->list as $event_id => $ev ) {
			$event = $this->events[ $ev['component'] ][ $ev['name'] ];

			if ( $instant !== 'skip' ) {
				$event->notifications['instant'] = $instant === 'on';
			}

			if ( $daily !== 'skip' ) {
				$event->notifications['daily'] = $daily === 'on';
			}

			if ( $weekly !== 'skip' ) {
				$event->notifications['weekly'] = $weekly === 'on';
			}

			$rules                  = $event->rules;
			$rules['notifications'] = $event->notifications;

			DB::instance()->change_event_rules( $event_id, $rules );
		}
	}

	public function register_component( string $category, string $component, array $args = array() ) {
		$this->components[ $component ] = (object) array(
			'category'     => $category,
			'component'    => $component,
			'source'       => $args['source'],
			'label'        => $args['label'] ?? $this->_generate_component_label( $component ),
			'plugin'       => $args['plugin'] ?? '',
			'icon'         => $args['icon'] ?? 'ui-folder',
			'is_available' => $args['is_available'] ?? false,
		);

		if ( ! isset( $this->sources[ $args['plugin'] ] ) ) {
			$this->sources[ $args['plugin'] ] = $args['source'];
		}
	}

	public function register_event( string $component, string $event, array $args = array(), array $rules = array() ) : bool {
		$obj = (object) array(
			'event_id'                => 0,
			'component'               => $component,
			'event'                   => $event,
			'rules'                   => $rules,
			'version'                 => $args['version'] ?? '1.0',
			'status'                  => $args['status'] ?? 'active',
			'scope'                   => $args['scope'] ?? '',
			'label'                   => $args['label'] ?? Str::slug_to_name( $event, '-' ),
			'object_type'             => $args['object_type'] ?? '',
			'is_security'             => $args['is_security'] ?? false,
			'is_malicious'            => $args['is_malicious'] ?? false,
			'skip_duplicates'         => $args['skip_duplicates'] ?? false,
			'skip_duplicates_request' => $args['skip_duplicates_request'] ?? false,
			'level'                   => $args['level'] ?? 0,
		);

		if ( isset( $this->events[ $component ][ $event ] ) ) {
			$obj->status        = $this->events[ $component ][ $event ]->status;
			$obj->rules         = $this->events[ $component ][ $event ]->rules;
			$obj->event_id      = $this->events[ $component ][ $event ]->event_id;
			$obj->notifications = $this->events[ $component ][ $event ]->notifications;

			$this->events[ $component ][ $event ] = $obj;

			$this->list[ $obj->event_id ] = array(
				'name'      => $event,
				'label'     => $obj->label,
				'component' => $component,
			);
		} else {
			$category = $this->components[ $component ]->category;

			$id = DB::instance()->add_new_event( $category, $component, $event, $obj->status, $rules );

			if ( $id > 0 ) {
				$obj->event_id = $id;

				$this->events[ $component ][ $event ] = $obj;

				$this->list[ $obj->event_id ] = array(
					'name'      => $event,
					'label'     => $obj->label,
					'component' => $component,
				);
			}
		}

		return $obj->event_id > 0;
	}

	private function _generate_component_label( string $component ) : string {
		$parts = explode( '/', $component );

		return isset( $parts[1] ) ? Str::slug_to_name( $parts[1], '-' ) : $component;
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
					'is_available' => false,
				);

				$this->events[ $event->component ] = array();
			}

			$event->event_id      = absint( $event->event_id );
			$event->label         = Str::slug_to_name( $event->event, '-' );
			$event->rules         = ! is_null( $event->rules ) && Str::is_json( $event->rules, false ) ? json_decode( $event->rules, true ) : array();
			$event->notifications = array(
				'daily'   => false,
				'weekly'  => false,
				'instant' => false,
			);

			if ( isset( $event->rules['notifications'] ) ) {
				foreach ( array( 'daily', 'weekly', 'instant' ) as $key ) {
					$value = $event->rules['notifications'][ $key ] ?? false;

					if ( is_bool( $value ) ) {
						$event->notifications[ $key ] = $value;
					}
				}

				unset( $event->rules['notifications'] );
			}

			$this->events[ $event->component ][ $event->event ] = $event;

			$this->list[ $event->event_id ] = array(
				'name'      => $event->event,
				'label'     => $event->label,
				'component' => $event->component,
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
		Privacy::instance();
	}

	private function _init_plugins() {
		bbPress::instance();
		BuddyPress::instance();
		ContactForm7::instance();
		DebugPress::instance();
		DuplicatePost::instance();
		Forminator::instance();
		GDForumManager::instance();
		GravityForms::instance();
		Jetpack::instance();
		SweepPress::instance();
		UserSwitching::instance();
		WooCommerce::instance();
	}
}
