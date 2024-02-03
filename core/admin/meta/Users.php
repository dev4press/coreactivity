<?php

namespace Dev4Press\Plugin\CoreActivity\Admin\Meta;

use Dev4Press\Plugin\CoreActivity\Log\Users as Feature;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Users {
	private array $meta = array(
		'coreactivity_activity' => 'coreactivity_last_activity',
		'coreactivity_login'    => 'coreactivity_last_login',
	);

	public function __construct() {
		add_action( 'current_screen', array( $this, 'current_screen' ) );
	}

	public static function instance() : Users {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new Users();
		}

		return $instance;
	}

	public function current_screen( $screen ) {
		debugpress_store_object( $screen );

		if ( $screen->id == 'users' ) {
			add_filter( 'manage_users_columns', array( $this, 'manage_columns' ) );
			add_filter( 'manage_users_sortable_columns', array( $this, 'sortable_columns' ) );
			add_filter( 'user_row_actions', array( $this, 'user_row_actions' ), 10, 2 );
		} else if ( $screen->id == 'users-network' ) {
			add_filter( 'wpmu_users_columns', array( $this, 'manage_columns' ) );
			add_filter( 'manage_users-network_sortable_columns', array( $this, 'sortable_columns' ) );
			add_filter( 'ms_user_row_actions', array( $this, 'user_row_actions' ), 10, 2 );
		}

		if ( $screen->id == 'users' || $screen->id == 'users-network' ) {
			add_filter( 'manage_users_custom_column', array( $this, 'column_values' ), 10, 3 );
			add_filter( 'users_list_table_query_args', array( $this, 'users_query_args' ) );
		}
	}

	public function user_row_actions( $actions, $user_object ) {
		$actions['log'] = "<a class='coreactivity-log' href='" . coreactivity_admin()->panel_url( 'logs', '', 'view=user_id&filter-user_id=' . $user_object->ID ) . "'>" . __( 'Log' ) . '</a>';

		return apply_filters( 'coreactivity_users_panel_row_actions', $actions );
	}

	public function manage_columns( $columns ) {
		$columns['coreactivity_online']   = __( 'Status' );
		$columns['coreactivity_activity'] = __( 'Last Active' );
		$columns['coreactivity_login']    = __( 'Last Login' );

		return apply_filters( 'coreactivity_users_panel_columns', $columns );
	}

	public function column_values( $value, $column, $user_id ) {
		switch ( $column ) {
			case 'coreactivity_online':
				if ( Feature::instance()->is_user_online( $user_id ) ) {
					return '<div class="coreactivity-user-status __is-online"><i class="dashicons dashicons-yes-alt"></i> <span>' . __( 'Online' ) . '</span></div>';
				} else {
					return '<div class="coreactivity-user-status __is-offline"><i class="dashicons dashicons-dismiss"></i> <span>' . __( 'Offline' ) . '</span></div>';
				}
			case 'coreactivity_activity':
				$last_activity = Feature::instance()->get_user_last_activity( $user_id );

				if ( $last_activity == 0 ) {
					return '/';
				} else {
					$timestamp = coreactivity()->datetime()->timestamp_gmt_to_local( $last_activity );

					return gmdate( 'Y.m.d', $timestamp ) . '<br/>@ ' . gmdate( 'H:i:s', $timestamp );
				}
			case 'coreactivity_login':
				$last_login = Feature::instance()->get_user_last_login( $user_id );

				if ( $last_login == 0 ) {
					return '/';
				} else {
					$timestamp = coreactivity()->datetime()->timestamp_gmt_to_local( $last_login );

					return gmdate( 'Y.m.d', $timestamp ) . '<br/>@ ' . gmdate( 'H:i:s', $timestamp );
				}
			default:
				$value = apply_filters( 'coreactivity_users_panel_value_column_' . $column, $value, $user_id );
				break;
		}

		return $value;
	}

	public function users_query_args( $args ) {
		if ( ! empty( $args['orderby'] ) && in_array( $args['orderby'], array_keys( $this->meta ) ) ) {
			$meta = $this->meta[ $args['orderby'] ];

			$args['orderby']  = 'meta_value_num';
			$args['meta_key'] = $meta;
		}

		return apply_filters( 'coreactivity_users_panel_query_args', $args );
	}

	public function sortable_columns( $columns ) {
		$columns['coreactivity_activity'] = 'coreactivity_activity';
		$columns['coreactivity_login']    = 'coreactivity_login';

		return apply_filters( 'coreactivity_users_panel_sortable_columns', $columns );
	}
}
