<?php

namespace Dev4Press\Plugin\CoreActivity\Table;

use Dev4Press\Plugin\CoreActivity\Log\Activity;
use Dev4Press\v49\Core\Quick\Sanitize;
use Dev4Press\v49\Core\Quick\Str;
use Dev4Press\v49\Core\UI\Elements;
use Dev4Press\v49\WordPress\Admin\Table;
use Dev4Press\v49\Core\Plugins\DBLite;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Events extends Table {
	public array $_sanitize_orderby_fields = array( 'e.event_id', 'e.component', 'e.event', 'logs' );
	public string $_table_class_name = 'coreactivity-grid-events';
	public string $_checkbox_field = 'event_id';
	public string $_self_nonce_key = 'coreactivity-table-events';
	public string $_rows_per_page_key = 'coreactivity_events_rows_per_page';
	public int $_rows_per_page_default = 50;
	public $_logged_counts = array();

	public function __construct( $args = array() ) {
		parent::__construct( array(
			'singular' => 'event',
			'plural'   => 'events',
			'ajax'     => false,
		) );
	}

	public function prepare_items() {
		$this->prepare_column_headers();

		$per_page      = $this->rows_per_page();
		$sel_source    = $this->get_request_arg( 'filter-source' );
		$sel_group     = $this->get_request_arg( 'filter-group' );
		$sel_component = $this->get_request_arg( 'filter-component' );
		$sel_search    = $this->get_request_arg( 'search' );

		$sql = array(
			'select' => array(
				'e.event_id',
				'e.category',
				'e.component',
				'e.event',
				'e.status',
				'COUNT(l.`log_id`) AS `logs`',
			),
			'from'   => array(
				coreactivity_db()->events . ' e',
				'LEFT JOIN ' . coreactivity_db()->logs . ' l ON l.`event_id` = e.`event_id`',
			),
			'group'  => 'e.`event_id`',
			'where'  => array(),
		);

		if ( ! empty( $sel_group ) ) {
			$sql['where'][] = coreactivity_db()->prepare( '`category` = %s', $sel_group );
		}

		if ( ! empty( $sel_source ) ) {
			$sql['where'][] = coreactivity_db()->prepare( '`component` LIKE %s', $sel_source . '%' );
		}

		if ( ! empty( $sel_component ) ) {
			$sql['where'][] = coreactivity_db()->prepare( '`component` = %s', $sel_component );
		}

		if ( ! empty( $sel_search ) ) {
			$sql['where'][] = $this->_get_search_where( array( '`component`', '`event`' ), $sel_search );
		}

		$this->query_items( $sql, $per_page );

		foreach ( $this->items as &$item ) {
			$parts     = explode( '/', $item->component );
			$component = Activity::instance()->get_component( $item->component );

			$item->event_id = absint( $item->event_id );
			$item->plugin   = $component->plugin ?? $parts[0];
			$item->source   = $component->source ?? Activity::instance()->get_plugin_label( $parts[0] );

			if ( ! isset( $this->_logged_counts[ $item->component ] ) ) {
				$this->_logged_counts[ $item->component ] = 0;
			}

			$this->_logged_counts[ $item->component ] += $item->logs;
		}
	}

	public function get_columns() : array {
		$columns = array(
			'cb'            => '<input type="checkbox" />',
			'event_id'      => __( 'ID', 'coreactivity' ),
			'status'        => __( 'Status', 'coreactivity' ),
			'category'      => __( 'Category', 'coreactivity' ),
			'source'        => __( 'Source', 'coreactivity' ),
			'component'     => __( 'Component', 'coreactivity' ),
			'event'         => __( 'Event', 'coreactivity' ),
			'logs'          => __( 'Logs', 'coreactivity' ),
			'description'   => __( 'Description', 'coreactivity' ),
			'available'     => __( 'Available', 'coreactivity' ),
			'notifications' => __( 'Notifications', 'coreactivity' ),
		);

		if ( is_network_admin() ) {
			unset( $columns['available'] );
		}

		return $columns;
	}

	protected function db() : ?DBLite {
		return coreactivity_db();
	}

	protected function process_request_args() {
		$this->_request_args = array(
			'filter-source'    => Sanitize::_get_text( 'filter-source', '' ),
			'filter-group'     => Sanitize::_get_text( 'filter-group', '' ),
			'filter-component' => Sanitize::_get_text( 'filter-component', '' ),
			'search'           => $this->_get_field( 's' ),
			'orderby'          => $this->_get_field( 'orderby', 'event_id' ),
			'order'            => $this->_get_field( 'order', 'DESC' ),
			'paged'            => $this->_get_field( 'paged' ),
		);
	}

	protected function filter_block_top() {
		echo '<div class="alignleft actions">';
		Elements::instance()->select( array_merge( array( '' => __( 'All Sources', 'coreactivity' ) ), Activity::instance()->get_all_sources() ), array(
			'selected' => $this->get_request_arg( 'filter-source' ),
			'name'     => 'filter-source',
		) );

		Elements::instance()->select( array_merge( array( '' => __( 'All Categories', 'coreactivity' ) ), Activity::instance()->get_all_categories() ), array(
			'selected' => $this->get_request_arg( 'filter-group' ),
			'name'     => 'filter-group',
		) );

		Elements::instance()->select_grouped( Activity::instance()->get_select_event_components( true ), array(
			'empty'    => __( 'All Components', 'coreactivity' ),
			'selected' => $this->get_request_arg( 'filter-component' ),
			'name'     => 'filter-component',
		) );
		submit_button( __( 'Filter', 'coreactivity' ), 'button', false, false, array( 'id' => 'coreactivity-events-submit' ) );
		echo '</div>';
	}

	protected function get_row_classes( $item, $classes = array() ) : array {
		$classes = array();

		if ( ! is_network_admin() ) {
			if ( ! Activity::instance()->is_event_available( $item->component, $item->event ) ) {
				$classes[] = '__is-not-loaded';
			}
		}

		return $classes;
	}

	protected function get_sortable_columns() : array {
		return array(
			'event_id'  => array( 'e.event_id', false ),
			'component' => array( 'e.component', false ),
			'event'     => array( 'e.event', false ),
			'logs'      => array( 'logs', false ),
		);
	}

	protected function get_bulk_actions() : array {
		return array(
			'enable'                    => __( 'Enable Events', 'coreactivity' ),
			'disable'                   => __( 'Disable Events', 'coreactivity' ),
			'notifications-instant-on'  => __( 'Enable Instant Notifications', 'coreactivity' ),
			'notifications-instant-off' => __( 'Disable Instant Notifications', 'coreactivity' ),
			'notifications-daily-on'    => __( 'Enable Daily Notifications', 'coreactivity' ),
			'notifications-daily-off'   => __( 'Disable Daily Notifications', 'coreactivity' ),
			'notifications-weekly-on'   => __( 'Enable Weekly Notifications', 'coreactivity' ),
			'notifications-weekly-off'  => __( 'Disable Weekly Notifications', 'coreactivity' ),
		);
	}

	protected function column_category( $item ) : string {
		$render = '<div class="coreactivity-field-wrapper">';
		$render .= '<span>' . $item->category . '</span>';
		$render .= '</div>';

		return $render;
	}

	protected function column_source( $item ) : string {
		return '<span>' . $item->source . '</span>';
	}

	protected function column_component( $item ) : string {
		$render = '<div class="coreactivity-field-wrapper">';
		$render .= '<i class="d4p-icon d4p-' . Activity::instance()->get_component_icon( $item->component ) . ' d4p-icon-fw"></i>';
		$render .= '<span>' . $item->component . '</span>';

		if ( $this->_logged_counts[ $item->component ] > 0 ) {
			$render .= '<a href="admin.php?page=coreactivity-logs&filter-component=' . esc_attr( $item->component ) . '"><i class="d4p-icon d4p-ui-filter"></i> <span class="d4p-accessibility-show-for-sr">' . esc_html__( 'Filter', 'coreactivity' ) . '</span></a>';
			$render .= '<a href="admin.php?page=coreactivity-logs&view=component&filter-component=' . esc_attr( $item->component ) . '"><i class="d4p-icon d4p-ui-eye"></i> <span class="d4p-accessibility-show-for-sr">' . esc_html__( 'View', 'coreactivity' ) . '</span></a>';
			$render .= '<a href="admin.php?page=coreactivity-tools&subpanel=cleanup&component=' . esc_attr( $item->component ) . '"><i class="d4p-icon d4p-ui-trash"></i> <span class="d4p-accessibility-show-for-sr">' . esc_html__( 'Clean Up', 'coreactivity' ) . '</span></a>';
		}

		$render .= '</div>';

		return $render;
	}

	protected function column_event( $item ) : string {
		$render = '<div class="coreactivity-field-wrapper">';
		$render .= '<span>' . $item->event . '</span>';

		if ( $item->logs > 0 ) {
			$render .= '<a href="admin.php?page=coreactivity-logs&filter-event_id=' . esc_attr( $item->event_id ) . '"><i class="d4p-icon d4p-ui-filter"></i> <span class="d4p-accessibility-show-for-sr">' . esc_html__( 'Filter', 'coreactivity' ) . '</span></a>';
			$render .= '<a href="admin.php?page=coreactivity-logs&view=event_id&filter-event_id=' . esc_attr( $item->event_id ) . '"><i class="d4p-icon d4p-ui-eye"></i> <span class="d4p-accessibility-show-for-sr">' . esc_html__( 'View', 'coreactivity' ) . '</span></a>';
			$render .= '<a href="admin.php?page=coreactivity-tools&subpanel=cleanup&component=' . esc_attr( $item->component ) . '&event=' . esc_attr( $item->event_id ) . '"><i class="d4p-icon d4p-ui-trash"></i> <span class="d4p-accessibility-show-for-sr">' . esc_html__( 'Clean Up', 'coreactivity' ) . '</span></a>';
		}

		$render .= '</div>';

		return $render;
	}

	protected function column_description( $item ) : string {
		return Activity::instance()->get_event_description( $item->component, $item->event );
	}

	protected function column_available( $item ) : string {
		return Activity::instance()->is_event_available( $item->component, $item->event ) ? __( 'Yes', 'coreactivity' ) : __( 'No', 'coreactivity' );
	}

	protected function column_notifications( $item ) : string {
		$notifications = Activity::instance()->get_event_notifications( $item->component, $item->event );

		$render = '<div class="coreactivity-event-notifications">';
		$render .= '<div>';
		$render .= $this->toggle_switch( $notifications['instant'] ?? false, 'instant', $item->event_id, 'coreactivity-toggle-notification', 'coreactivity-toggle-notification-instant-' . $item->event_id, __( 'Disable Instant Notifications', 'coreactivity' ), __( 'Enable Instant Notifications', 'coreactivity' ) );
		$render .= '<span>' . __( 'Instant', 'coreactivity' ) . '</span>';
		$render .= '</div>';
		$render .= '<div>';
		$render .= $this->toggle_switch( $notifications['daily'] ?? false, 'daily', $item->event_id, 'coreactivity-toggle-notification', 'coreactivity-toggle-notification-daily-' . $item->event_id, __( 'Disable Daily Notifications', 'coreactivity' ), __( 'Enable Daily Notifications', 'coreactivity' ) );
		$render .= '<span>' . __( 'Daily', 'coreactivity' ) . '</span>';
		$render .= '</div>';
		$render .= '<div>';
		$render .= $this->toggle_switch( $notifications['weekly'] ?? false, 'weekly', $item->event_id, 'coreactivity-toggle-notification', 'coreactivity-toggle-notification-weekly-' . $item->event_id, __( 'Disable Weekly Notifications', 'coreactivity' ), __( 'Enable Weekly Notifications', 'coreactivity' ) );
		$render .= '<span>' . __( 'Weekly', 'coreactivity' ) . '</span>';
		$render .= '</div>';
		$render .= '</div>';

		return $render;
	}

	protected function column_status( $item ) : string {
		return $this->toggle_switch( $item->status == 'active', '', $item->event_id, 'coreactivity-toggle-event', 'coreactivity-toggle-event-' . $item->event_id, __( 'Disable Event', 'coreactivity' ), __( 'Enable Event', 'coreactivity' ) );
	}

	protected function toggle_switch( bool $value, string $key, int $id, string $class, string $nonce, string $active, string $inactive ) : string {
		$title  = $value ? $active : $inactive;
		$toggle = $value ? 'd4p-ui-toggle-on' : 'd4p-ui-toggle-off';

		return '<button class="coreactivity-toggle ' . esc_attr( $class ) . '" data-key="' . esc_attr( $key ) . '" data-on="' . esc_attr( $active ) . '" data-off="' . esc_attr( $inactive ) . '" data-id="' . esc_attr( $id ) . '" data-nonce="' . wp_create_nonce( $nonce ) . '" type="button"><i aria-hidden="true" class="d4p-icon ' . $toggle . '"></i><span class="d4p-accessibility-show-for-sr">' . $title . '</span></button>';
	}
}
