<?php

namespace Dev4Press\Plugin\CoreActivity\Table;

use Dev4Press\Plugin\CoreActivity\Log\Core;
use Dev4Press\Plugin\CoreActivity\Log\Init;
use Dev4Press\v42\Core\Plugins\DBLite;
use Dev4Press\v42\Core\Quick\Sanitize;
use Dev4Press\v42\Core\UI\Elements;
use Dev4Press\v42\Service\GEOIP\GEOJSIO;
use Dev4Press\v42\WordPress\Admin\Table;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Logs extends Table {
	public $_sanitize_orderby_fields = array( 'l.log_id', 'e.component', 'e.event' );
	public $_table_class_name = 'coreactivity-grid-logs';
	public $_checkbox_field = 'log_id';
	public $_self_nonce_key = 'coreactivity-table-logs';
	public $_display_columns_simplified;
	public $_display_ip_country_flag;

	protected $_filter_lock = array();
	protected $_items_ips = array();

	public function __construct( $args = array() ) {
		parent::__construct( array(
			'singular' => 'event',
			'plural'   => 'events',
			'ajax'     => false
		) );

		$this->_display_columns_simplified = coreactivity_settings()->get( 'display_columns_simplified' );
		$this->_display_ip_country_flag    = coreactivity_settings()->get( 'display_ip_geo_flag' );

		if ( ! is_multisite() ) {
			$this->_filter_lock[ 'blog_id' ] = 0;
		}
	}

	protected function db() : ?DBLite {
		return coreactivity_db();
	}

	protected function process_request_args() {
		$this->_request_args = array(
			'filter-blog_id'     => Sanitize::_get_absint( 'filter-blog_id', '' ),
			'filter-user_id'     => Sanitize::_get_absint( 'filter-user_id', '' ),
			'filter-event_id'    => Sanitize::_get_absint( 'filter-event_id', '' ),
			'filter-component'   => Sanitize::_get_basic( 'filter-component', '' ),
			'filter-context'     => Sanitize::_get_basic( 'filter-context', '' ),
			'filter-method'      => Sanitize::_get_basic( 'filter-method', '' ),
			'filter-object_type' => Sanitize::_get_basic( 'filter-object_type', '' ),
			'search'             => $this->_get_field( 's' ),
			'period'             => $this->_get_field( 'period' ),
			'orderby'            => $this->_get_field( 'orderby', 'l.log_id' ),
			'order'              => $this->_get_field( 'order', 'DESC' ),
			'paged'              => $this->_get_field( 'paged' ),
		);

		foreach ( array_keys( $this->_filter_lock ) as $field ) {
			$key = 'filter-' . $field;

			if ( isset( $this->_request_args[ $key ] ) ) {
				$this->_request_args[ $key ] = '';
			}
		}
	}

	protected function filter_block_top() {
		echo '<div class="alignleft actions">';
		Elements::instance()->select( $this->get_period_dropdown( 'logged', coreactivity_db()->logs ), array(
			'selected' => $this->get_request_arg( 'period' ),
			'name'     => 'period'
		) );

		if ( ! isset( $this->_filter_lock[ 'component' ] ) ) {
			$_components = array(
				'' => __( "All Components" )
			);

			foreach ( Init::instance()->components() as $component => $label ) {
				$_components[ $component ] = $this->_display_columns_simplified ? $label : $component;
			}

			Elements::instance()->select( $_components, array(
				'selected' => $this->get_request_arg( 'filter-component' ),
				'name'     => 'filter-component'
			) );
		}

		if ( ! isset( $this->_filter_lock[ 'event' ] ) ) {
			$_events = array(
				'' => __( "All Events" )
			);

			foreach ( Init::instance()->events_list() as $id => $event ) {
				$_events[ $id ] = $this->_display_columns_simplified ? $event[ 'label' ] : $event[ 'name' ];
			}

			Elements::instance()->select( $_events, array(
				'selected' => $this->get_request_arg( 'filter-event_id' ),
				'name'     => 'filter-event_id'
			) );
		}

		if ( ! isset( $this->_filter_lock[ 'context' ] ) ) {
			$_contexts = array(
				''  => __( "All Contexts" ),
				'-' => __( "Normal" )
			);

			foreach ( Core::instance()->valid_request_contexts() as $context ) {
				$_contexts[ $context ] = $context;
			}

			Elements::instance()->select( $_contexts, array(
				'selected' => $this->get_request_arg( 'filter-context' ),
				'name'     => 'filter-context'
			) );
		}

		if ( ! isset( $this->_filter_lock[ 'method' ] ) ) {
			$_methods = array(
				'' => __( "All Methods" )
			);

			foreach ( Core::instance()->valid_request_methods() as $method ) {
				$_methods[ $method ] = $method;
			}

			Elements::instance()->select( $_methods, array(
				'selected' => $this->get_request_arg( 'filter-method' ),
				'name'     => 'filter-method'
			) );
		}

		if ( ! isset( $this->_filter_lock[ 'object_type' ] ) ) {
			$_types = array(
				'' => __( "All Object Types" )
			);

			foreach ( Init::instance()->object_types() as $type => $value ) {
				$_types[ $type ] = $value;
			}

			Elements::instance()->select( $_types, array(
				'selected' => $this->get_request_arg( 'filter-object_type' ),
				'name'     => 'filter-object_type'
			) );
		}

		submit_button( __( "Filter", "coreactivity" ), 'button', false, false, array( 'id' => 'coreactivity-events-submit' ) );
		echo '</div>';
	}

	public function rows_per_page() : int {
		$per_page = get_user_option( 'coreactivity_events_rows_per_page' );

		if ( empty( $per_page ) || $per_page < 1 ) {
			$per_page = 50;
		}

		return $per_page;
	}

	public function get_columns() : array {
		$columns = array(
			'cb'          => '<input type="checkbox" />',
			'log_id'      => __( "ID", "coreactivity" ),
			'blog_id'     => __( "Blog" ),
			'user_id'     => __( "User" ),
			'component'   => __( "Component", "coreactivity" ),
			'event'       => __( "Event", "coreactivity" ),
			'context'     => __( "Context" ),
			'method'      => __( "Method" ),
			'protocol'    => __( "Protocol" ),
			'ip'          => __( "IP" ),
			'request'     => __( "Request" ),
			'object_type' => __( "Object Type" ),
			'object_name' => __( "Object" ),
			'logged'      => __( "Logged", "coreactivity" )
		);

		foreach ( array_keys( $this->_filter_lock ) as $column ) {
			if ( isset( $columns[ $column ] ) ) {
				unset( $columns[ $column ] );
			}
		}

		return $columns;
	}

	protected function get_sortable_columns() : array {
		return array(
			'log_id'    => array( 'l.log_id', false ),
			'ip'        => array( 'l.ip', false ),
			'component' => array( 'e.component', false ),
			'event'     => array( 'e.event', false )
		);
	}

	protected function get_bulk_actions() : array {
		return array(
			'delete' => __( "Delete", "coreactivity" )
		);
	}

	public function column_component( $item ) : string {
		return $this->_display_columns_simplified ? Init::instance()->get_component_label( $item->component ) : $item->component;
	}

	public function column_ip( $item ) : string {
		$render = $item->ip;

		if ( $this->_display_ip_country_flag ) {
			$ip     = GEOJSIO::instance()->locate( $item->ip );
			$render = $ip->flag() . ' <span>' . $render . '</span>';
		}

		return $render;
	}

	public function column_event( $item ) : string {
		return $this->_display_columns_simplified ? Init::instance()->get_event_label( absint( $item->event_id ), $item->event ) : $item->event;
	}

	public function column_logged( $item ) : string {
		$timestamp = coreactivity()->datetime()->timestamp_gmt_to_local( strtotime( $item->logged ) );

		return date( 'Y.m.d', $timestamp ) . '<br/>@ ' . date( 'H:m:s', $timestamp );
	}

	public function prepare_items() {
		$this->prepare_column_headers();

		$per_page        = $this->rows_per_page();
		$sel_search      = $this->get_request_arg( 'search' );
		$sel_period      = $this->get_request_arg( 'period' );
		$sel_blog_id     = $this->get_request_arg( 'filter-blog_id' );
		$sel_user_id     = $this->get_request_arg( 'filter-user_id' );
		$sel_event_id    = $this->get_request_arg( 'filter-event_id' );
		$sel_component   = $this->get_request_arg( 'filter-component' );
		$sel_context     = $this->get_request_arg( 'filter-context' );
		$sel_method      = $this->get_request_arg( 'filter-method' );
		$sel_object_type = $this->get_request_arg( 'filter-object_type' );

		if ( isset( $this->_filter_lock[ 'blog_id' ] ) ) {
			$sel_blog_id = $this->_filter_lock[ 'blog_id' ];
		}

		if ( isset( $this->_filter_lock[ 'user_id' ] ) ) {
			$sel_user_id = $this->_filter_lock[ 'user_id' ];
		}

		if ( isset( $this->_filter_lock[ 'component' ] ) ) {
			$sel_component = $this->_filter_lock[ 'component' ];

			if ( isset( $this->_filter_lock[ 'event' ] ) ) {
				$sel_event_id = Init::instance()->get_event_id( $sel_component, $this->_filter_lock[ 'event' ] );
			}
		}

		$sql = array(
			'select' => array(
				'l.*',
				'e.`component`',
				'e.`event`'
			),
			'from'   => array(
				coreactivity_db()->logs . ' l ',
				'INNER JOIN ' . coreactivity_db()->events . ' e ON l.`event_id` = e.`event_id`'
			),
			'where'  => array()
		);

		if ( ! empty( $sel_blog_id ) && $sel_blog_id > 0 ) {
			$sql[ 'where' ][] = $this->db()->prepare( 'l.`blog_id` = %d', $sel_blog_id );
		}

		if ( ! empty( $sel_user_id ) && $sel_user_id > 0 ) {
			$sql[ 'where' ][] = $this->db()->prepare( 'l.`user_id` = %d', $sel_user_id );
		}

		if ( ! empty( $sel_event_id ) && $sel_event_id > 0 ) {
			$sql[ 'where' ][] = $this->db()->prepare( 'l.`event_id` = %d', $sel_event_id );
		} else if ( ! empty( $sel_component ) ) {
			$sql[ 'where' ][] = $this->db()->prepare( 'e.`component` = %s', $sel_component );
		}

		if ( ! empty( $sel_context ) ) {
			$sel_context      = $sel_context == '-' ? '' : $sel_context;
			$sql[ 'where' ][] = $this->db()->prepare( 'l.`context` = %s', $sel_context );
		}

		if ( ! empty( $sel_method ) ) {
			$sql[ 'where' ][] = $this->db()->prepare( 'l.`method` = %s', $sel_method );
		}

		if ( ! empty( $sel_object_type ) ) {
			$sql[ 'where' ][] = $this->db()->prepare( 'l.`object_type` = %s', $sel_object_type );
		}

		if ( ! empty( $sel_period ) ) {
			$sql[ 'where' ][] = $this->_get_period_where( $sel_period, 'l.`logged`' );
		}

		if ( ! empty( $sel_search ) ) {
			$sql[ 'where' ][] = $this->_get_search_where( array( 'e.`component`', 'e.`event`, l.`request`, l.`object_name`' ), $sel_search );
		}

		$this->query_items( $sql, $per_page );

		if ( $this->_display_ip_country_flag ) {
			foreach ( $this->items as $item ) {
				$ip = $item->ip;

				if ( ! in_array( $ip, $this->_items_ips ) ) {
					$this->_items_ips[] = $ip;
				}
			}

			if ( ! empty( $this->_items_ips ) ) {
				GEOJSIO::instance()->bulk( $this->_items_ips );
			}
		}
	}
}
