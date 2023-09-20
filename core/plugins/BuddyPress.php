<?php

namespace Dev4Press\Plugin\CoreActivity\Plugins;

use Dev4Press\Plugin\CoreActivity\Base\Plugin;
use Dev4Press\v43\Core\Quick\Request;
use Dev4Press\v43\Core\Quick\Sanitize;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BuddyPress extends Plugin {
	protected $plugin = 'coreactivity';
	protected $name = 'buddypress';
	protected $icon = 'logo-buddypress';
	protected $object_type = 'bpgroup';
	protected $plugin_file = 'buddypress/bp-loader.php';
	protected $storage = array();
	protected $group_properties = array(
		'id',
		'creator_id',
		'name',
		'slug',
		'description',
		'status',
		'parent_id',
		'enable_forum',
	);

	public function registered_object_types( array $object_types ) : array {
		$object_types['bpcomponent'] = __( 'BuddyPress Component', 'coreactivity' );
		$object_types['bpgroup']     = __( 'BuddyPress Group', 'coreactivity' );

		return $object_types;
	}

	public function tracking() {
		if ( $this->are_active( array( 'component-activated', 'component-deactivated' ) ) ) {
			add_action( 'update_option_bp-active-components', array( $this, 'event_active_components' ), 10, 2 );
		}

		if ( $this->is_active( 'group-created' ) ) {
			add_action( 'groups_create_group', array( $this, 'event_create_group' ), 10, 3 );
		}

		if ( $this->is_active( 'group-updated' ) ) {
			add_filter( 'bp_after_groups_create_group_parse_args', array( $this, 'prepare_group_update' ) );
			add_action( 'groups_update_group', array( $this, 'event_update_group' ), 10, 2 );
			add_action( 'groups_details_updated', array( $this, 'event_details_updated' ), 10, 2 );

			if ( is_admin() && Request::is_post() && isset( $_REQUEST['gid'] ) && isset( $_REQUEST['page'] ) && $_REQUEST['page'] === 'bp-groups' ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				add_filter( 'groups_get_group', array( $this, 'prepare_get_group' ) );
				add_action( 'bp_group_admin_edit_after', array( $this, 'event_admin_edit_after' ) );
			}
		}
	}

	public function label() : string {
		return __( 'BuddyPress', 'coreactivity' );
	}

	protected function get_events() : array {
		return array(
			'group-created'         => array(
				'label' => __( 'Group Created', 'coreactivity' ),
			),
			'group-updated'         => array(
				'label' => __( 'Group Updated', 'coreactivity' ),
			),
			'component-activated'   => array(
				'label' => __( 'Component Activated', 'coreactivity' ),
			),
			'component-deactivated' => array(
				'label' => __( 'Component Deactivated', 'coreactivity' ),
			),
		);
	}

	public function prepare_group_update( $r ) {
		$group_id = $r['group_id'] ?? 0;

		if ( $group_id > 0 ) {
			$group_id = absint( $group_id );

			$this->storage[ $group_id ] = groups_get_group( $group_id );
		}

		return $r;
	}

	public function prepare_get_group( $group ) {
		if ( ! isset( $this->storage[ $group->id ] ) ) {
			$this->storage[ $group->id ] = $group;
		}

		return $group;
	}

	public function event_create_group( $group_id, $member, $group ) {
		$this->log( 'group-created', array( 'object_id' => $group_id ), array(
			'group_name' => $group->name,
			'group_slug' => $group->slug,
		) );
	}

	public function event_update_group( $group_id, $group ) {
		$group_id = absint( $group_id );

		if ( isset( $this->storage[ $group_id ] ) ) {
			$previous = $this->storage[ $group_id ];
			$changes  = array();

			foreach ( $this->group_properties as $prop ) {
				if ( $previous->$prop !== $group->$prop ) {
					$changes[ 'old_' . $prop ] = $previous->$prop;
					$changes[ 'new_' . $prop ] = $group->$prop;
				}
			}

			if ( ! empty( $changes ) ) {
				$this->log( 'group-updated', array( 'object_id' => $group_id ), $changes );
			}
		}
	}

	public function event_details_updated( $group_id, $previous ) {
		$group   = groups_get_group( $group_id );
		$changes = array();

		foreach ( $this->group_properties as $prop ) {
			if ( $previous->$prop !== $group->$prop ) {
				$changes[ 'old_' . $prop ] = $previous->$prop;
				$changes[ 'new_' . $prop ] = $group->$prop;
			}
		}

		if ( ! empty( $changes ) ) {
			$this->log( 'group-updated', array( 'object_id' => $group_id ), $changes );
		}
	}

	public function event_admin_edit_after( $group_id ) {
		if ( isset( $this->storage[ $group_id ] ) ) {
			$previous = $this->storage[ $group_id ];
			$group    = groups_get_group( $group_id );

			$changes = array();

			foreach ( $this->group_properties as $prop ) {
				if ( $previous->$prop !== $group->$prop ) {
					$changes[ 'old_' . $prop ] = $previous->$prop;
					$changes[ 'new_' . $prop ] = $group->$prop;
				}
			}

			if ( ! empty( $changes ) ) {
				$this->log( 'group-updated', array( 'object_id' => $group_id ), $changes );
			}
		}
	}

	public function event_active_components( $old_value, $value ) {
		$new = array_keys( $value );
		$old = array_keys( $old_value );

		if ( $this->is_active( 'component-deactivated' ) ) {
			foreach ( $old as $component ) {
				if ( ! in_array( $component, $new ) ) {
					$this->log( 'component-deactivated', array(
						'object_type' => 'bpcomponent',
						'object_name' => $component,
					) );
				}
			}
		}

		if ( $this->is_active( 'component-activated' ) ) {
			foreach ( $new as $component ) {
				if ( ! in_array( $component, $old ) ) {
					$this->log( 'component-activated', array(
						'object_type' => 'bpcomponent',
						'object_name' => $component,
					) );
				}
			}
		}
	}
}
