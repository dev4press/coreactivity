<?php

namespace Dev4Press\Plugin\CoreActivity\Table;

use Dev4Press\Plugin\CoreActivity\Log\Init;
use Dev4Press\v42\Core\Quick\Sanitize;
use Dev4Press\v42\Core\UI\Elements;
use Dev4Press\v42\WordPress\Admin\Table;
use Dev4Press\v42\Core\Plugins\DBLite;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Events extends Table {
	public $_sanitize_orderby_fields = array( 'event_id', 'component', 'name' );
	public $_table_class_name = 'coreactivity-grid-events';
	public $_checkbox_field = 'event_id';
	public $_self_nonce_key = 'coreactivity-table-events';

	public function __construct( $args = array() ) {
		parent::__construct( array(
			'singular' => 'event',
			'plural'   => 'events',
			'ajax'     => false
		) );
	}

	protected function db() : ?DBLite {
		return coreactivity_db();
	}

	protected function process_request_args() {
		$this->_request_args = array(
			'filter-component' => Sanitize::_get_basic( 'filter-component', '' ),
			'search'           => $this->_get_field( 's' ),
			'orderby'          => $this->_get_field( 'orderby', 'event_id' ),
			'order'            => $this->_get_field( 'order', 'DESC' ),
			'paged'            => $this->_get_field( 'paged' ),
		);
	}

	protected function filter_block_top() {
		echo '<div class="alignleft actions">';
		Elements::instance()->select( array_merge( array( '' => __( "All Components" ) ), Init::instance()->components() ), array(
			'selected' => $this->get_request_arg( 'filter-component' ),
			'name'     => 'filter-component'
		) );
		submit_button( __( "Filter", "coreactivity" ), 'button', false, false, array( 'id' => 'coreactivity-events-submit' ) );
		echo '</div>';
	}

	protected function get_row_classes( $item ) : array {
		$classes = array();

		if ( ! Init::instance()->is_event_loaded( $item->component, $item->event ) ) {
			$classes[] = '__is-not-loaded';
		}

		return $classes;
	}

	public function rows_per_page() : int {
		$per_page = get_user_option( 'coreactivity_events_rows_per_page' );

		if ( empty( $per_page ) || $per_page < 1 ) {
			$per_page = 50;
		}

		return $per_page;
	}

	public function get_columns() : array {
		return array(
			'cb'          => '<input type="checkbox" />',
			'event_id'    => __( "ID", "coreactivity" ),
			'status'      => __( "Status" ),
			'component'   => __( "Component", "coreactivity" ),
			'event'       => __( "Event", "coreactivity" ),
			'description' => __( "Description", "coreactivity" ),
			'loaded'      => __( "Loaded", "coreactivity" )
		);
	}

	protected function get_bulk_actions() : array {
		return array(
			'enable'  => __( "Enable", "coreactivity" ),
			'disable' => __( "Disable", "coreactivity" )
		);
	}

	protected function get_sortable_columns() : array {
		return array(
			'event_id'  => array( 'event_id', false ),
			'component' => array( 'component', false ),
			'event'     => array( 'event', false )
		);
	}

	protected function column_description( $item ) : string {
		return Init::instance()->get_event_description( $item->component, $item->event );
	}

	protected function column_loaded( $item ) : string {
		return Init::instance()->is_event_loaded( $item->component, $item->event ) ? __( "Yes" ) : __( "No" );
	}

	protected function column_status( $item ) : string {
		$title  = $item->status == 'active' ? __( "Active" ) : __( "Disabled" );
		$toggle = $item->status == 'active' ? 'd4p-ui-toggle-on' : 'd4p-ui-toggle-off';

		return '<button class="coreactivity-event-toggle" data-id="' . esc_attr( $item->event_id ) . '" data-nonce="' . wp_create_nonce( 'coreactivity-toggle-event-' . $item->event_id ) . '" type="button"><i aria-hidden="true" class="d4p-icon ' . $toggle . '"></i><span class="d4p-accessibility-show-for-sr">' . $title . '</span></button>';
	}

	public function prepare_items() {
		$this->prepare_column_headers();

		$per_page      = $this->rows_per_page();
		$sel_component = $this->get_request_arg( 'filter-component' );
		$sel_search    = $this->get_request_arg( 'search' );

		$sql = array(
			'select' => array(
				'*'
			),
			'from'   => array(
				coreactivity_db()->events
			),
			'where'  => array()
		);

		if ( ! empty( $sel_component ) ) {
			$sql[ 'where' ][] = coreactivity_db()->prepare( '`component` = %s', $sel_component );
		}

		if ( ! empty( $sel_search ) ) {
			$sql[ 'where' ][] = $this->_get_search_where( array( '`component`', '`event`' ), $sel_search );
		}

		$this->query_items( $sql, $per_page );
	}
}
