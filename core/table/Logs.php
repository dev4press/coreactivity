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
	public $_views_separator = '';
	public $_display_columns_simplified;
	public $_display_ip_country_flag;
	public $_display_user_avatar;
	public $_current_view = '';
	public $_current_ip = '';
	public $_server_ip = '';

	protected $_filter_lock = array();
	protected $_items_ips = array();

	public function __construct( $args = array() ) {
		parent::__construct( array(
			'singular' => 'event',
			'plural'   => 'events',
			'ajax'     => false
		) );

		$this->_current_ip = Core::instance()->get( 'ip' );
		$this->_server_ip  = Core::instance()->get( 'server_ip' );

		$this->_display_columns_simplified = coreactivity_settings()->get( 'display_columns_simplified' );
		$this->_display_ip_country_flag    = coreactivity_settings()->get( 'display_ip_country_flag' );
		$this->_display_user_avatar        = coreactivity_settings()->get( 'display_user_avatar' );

		if ( ! is_multisite() ) {
			$this->_filter_lock[ 'blog_id' ] = - 1;
		}

		if ( in_array( $this->_request_args[ 'view' ], array( 'user_id', 'blog_id', 'event_id', 'ip', 'object', 'component' ) ) ) {
			$this->_current_view = $this->_request_args[ 'view' ];

			switch ( $this->_current_view ) {
				case 'component':
					if ( ! isset( $this->_filter_lock[ 'component' ] ) && ! empty( $this->_request_args[ 'filter-component' ] ) ) {
						$this->_filter_lock[ 'component' ] = $this->_request_args[ 'filter-component' ];
					} else {
						$this->_current_view = '';
					}
					break;
				case 'event_id':
					if ( ! isset( $this->_filter_lock[ 'event_id' ] ) && ! empty( $this->_request_args[ 'filter-event_id' ] ) ) {
						$this->_filter_lock[ 'event_id' ] = $this->_request_args[ 'filter-event_id' ];
					} else {
						$this->_current_view = '';
					}
					break;
				case 'user_id':
					if ( ! isset( $this->_filter_lock[ 'user_id' ] ) && ! empty( $this->_request_args[ 'filter-user_id' ] ) ) {
						$this->_filter_lock[ 'user_id' ] = $this->_request_args[ 'filter-user_id' ];
					} else {
						$this->_current_view = '';
					}
					break;
				case 'blog_id':
					if ( ! isset( $this->_filter_lock[ 'blog_id' ] ) && ! empty( $this->_request_args[ 'filter-blog_id' ] ) ) {
						$this->_filter_lock[ 'blog_id' ] = $this->_request_args[ 'filter-blog_id' ];
					} else {
						$this->_current_view = '';
					}
					break;
				case 'ip':
					if ( ! isset( $this->_filter_lock[ 'ip' ] ) && ! empty( $this->_request_args[ 'filter-ip' ] ) ) {
						$this->_filter_lock[ 'ip' ] = $this->_request_args[ 'filter-ip' ];
					} else {
						$this->_current_view = '';
					}
					break;
			}
		}
	}

	protected function _view( string $view, string $args ) : string {
		return $this->_url() . '&view=' . $view . '&' . $args;
	}

	protected function _self( $args, $getback = false, $nonce = null ) : string {
		$url = parent::_self( $args, $getback, $nonce );

		if ( ! empty( $this->_current_view ) ) {
			$url .= '&view=' . $this->_current_view;

			switch ( $this->_current_view ) {
				case 'ip':
					$url .= '&filter-ip=' . $this->_filter_lock[ 'ip' ];
					break;
				case 'user':
					$url .= '&filter-user_id=' . $this->_filter_lock[ 'user_id' ];
					break;
			}
		}

		return $url;
	}

	protected function db() : ?DBLite {
		return coreactivity_db();
	}

	protected function process_request_args() {
		$this->_request_args = array(
			'filter-blog_id'     => Sanitize::_get_absint( 'filter-blog_id', '' ),
			'filter-user_id'     => Sanitize::_get_absint( 'filter-user_id', '' ),
			'filter-event_id'    => Sanitize::_get_absint( 'filter-event_id', '' ),
			'filter-ip'          => Sanitize::_get_basic( 'filter-ip', '' ),
			'filter-component'   => Sanitize::_get_basic( 'filter-component', '' ),
			'filter-context'     => Sanitize::_get_basic( 'filter-context', '' ),
			'filter-method'      => Sanitize::_get_basic( 'filter-method', '' ),
			'filter-object_type' => Sanitize::_get_basic( 'filter-object_type', '' ),
			'view'               => $this->_get_field( 'view' ),
			'search'             => $this->_get_field( 's' ),
			'period'             => $this->_get_field( 'period' ),
			'orderby'            => $this->_get_field( 'orderby', 'l.log_id' ),
			'order'              => $this->_get_field( 'order', 'DESC' ),
			'paged'              => $this->_get_field( 'paged' ),
		);

		if ( ! empty( $this->_request_args[ 'filter-component' ] ) && ! Init::instance()->is_component_valid( $this->_request_args[ 'filter-component' ] ) ) {
			$this->_request_args[ 'filter-component' ] = '';
		}

		if ( ! empty( $this->_request_args[ 'filter-event_id' ] ) && ! Init::instance()->is_event_id_valid( $this->_request_args[ 'filter-event_id' ] ) ) {
			$this->_request_args[ 'filter-event_id' ] = '';
		}

		foreach ( array_keys( $this->_filter_lock ) as $field ) {
			$key = 'filter-' . $field;

			if ( isset( $this->_request_args[ $key ] ) ) {
				$this->_request_args[ $key ] = '';
			}
		}

		if ( isset( $this->_filter_lock[ 'event_id' ] ) ) {
			$this->_request_args[ 'filter-component' ] = '';
		}
	}

	protected function filter_block_top() {
		echo '<div class="alignleft actions">';
		Elements::instance()->select( $this->get_period_dropdown( 'logged', coreactivity_db()->logs ), array(
			'selected' => $this->get_request_arg( 'period' ),
			'name'     => 'period'
		) );

		if ( ! isset( $this->_filter_lock[ 'component' ] ) && ! isset( $this->_filter_lock[ 'event_id' ] ) ) {
			$_components = array(
				'' => __( "All Components", "coreactivity" )
			);

			foreach ( Init::instance()->components() as $component => $label ) {
				$_components[ $component ] = $this->_display_columns_simplified ? $label : $component;
			}

			Elements::instance()->select( $_components, array(
				'selected' => $this->get_request_arg( 'filter-component' ),
				'name'     => 'filter-component'
			) );
		}

		if ( ! isset( $this->_filter_lock[ 'event_id' ] ) ) {
			$_events = array(
				'' => __( "All Events", "coreactivity" )
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
				''  => __( "All Contexts", "coreactivity" ),
				'-' => __( "Normal", "coreactivity" )
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
				'' => __( "All Methods", "coreactivity" )
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
				'' => __( "All Object Types", "coreactivity" )
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

	protected function get_views() : array {
		$views = array();

		if ( ! empty( $this->_current_view ) ) {
			?>

            <input type="hidden" name="view" value="<?php echo esc_attr( $this->_current_view ); ?>"/>
            <input type="hidden" name="filter-<?php echo esc_attr( $this->_current_view ); ?>" value="<?php echo esc_attr( $this->_filter_lock[ $this->_current_view ] ); ?>"/>

			<?php

			$current_view = '';
			$current_key  = 'view ';

			$views[ 'all' ] = '<a class="coreactivity-view-button" href="' . $this->_url() . '"><i class="d4p-icon d4p-ui-angles-left"></i> ' . __( "All Logs", "coreactivity" ) . '</a>';

			switch ( $this->_current_view ) {
				case 'component':
					$current_view = '<span class="coreactivity-view-button"><i class="d4p-icon d4p-ui-tag"></i> <span>' . esc_html__( "Component" ) . '</span>';
					$current_view .= Init::instance()->get_component_label( $this->_filter_lock[ 'component' ] );
					$current_view .= '<span>[' . esc_html( $this->_filter_lock[ 'component' ] ) . ']</span>';
					$current_view .= '</span>';

					$current_key .= 'component';
					break;
				case 'event_id':
					$event = Init::instance()->get_event_by_id( $this->_filter_lock[ 'event_id' ] );

					$current_view = '<span class="coreactivity-view-button"><i class="d4p-icon d4p-ui-radar"></i> <span>' . esc_html__( "Event" ) . '</span>';
					$current_view .= $event->component_label . ' / ' . $event->label;
					$current_view .= '<span>[' . $event->component . '/' . $event->event . ']</span>';
					$current_view .= '</span>';

					$current_key .= 'component';
					break;
				case 'ip':
					$current_view = '<span class="coreactivity-view-button"><i class="d4p-icon d4p-ui-cloud"></i> <span>' . esc_html__( "IP" ) . '</span>';

					if ( $this->_display_ip_country_flag ) {
						$ip           = GEOJSIO::instance()->locate( $this->_filter_lock[ 'ip' ] );
						$current_view .= '<span>' . esc_html( $this->_filter_lock[ 'ip' ] ) . '</span> ' . $ip->flag();
					} else {
						$current_view .= $this->_filter_lock[ 'ip' ];
					}

					$current_view .= '</span>';
					$current_key  .= 'ip';
					break;
				case 'user_id':
					$user = get_user_by( 'id', $this->_filter_lock[ 'user_id' ] );
					$name = ! $user ? __( "Not Found", "coreactivity" ) : $user->display_name;

					$current_view = '<span class="coreactivity-view-button"><i class="d4p-icon d4p-ui-user"></i> <span>' . esc_html__( "User" ) . '</span>';
					$current_view .= '<span>' . esc_html( $this->_filter_lock[ 'user_id' ] . ' : ' . $name ) . '</span>';

					if ( $this->_filter_lock[ 'user_id' ] > 0 && $this->_display_user_avatar ) {
						$current_view .= ' ' . get_avatar( $this->_filter_lock[ 'user_id' ], 30 );
					}

					$current_view .= '</span>';
					$current_key  .= 'user';
					break;
			}

			$views[ $current_key ] = $current_view;
		}

		return $views;
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
			'blog_id'     => __( "Blog", "coreactivity" ),
			'user_id'     => __( "User", "coreactivity" ),
			'component'   => __( "Component", "coreactivity" ),
			'event_id'    => __( "Event", "coreactivity" ),
			'context'     => __( "Context", "coreactivity" ),
			'method'      => __( "Method", "coreactivity" ),
			'protocol'    => __( "Protocol", "coreactivity" ),
			'ip'          => __( "IP", "coreactivity" ),
			'request'     => __( "Request", "coreactivity" ),
			'object_type' => __( "Object Type", "coreactivity" ),
			'object_name' => __( "Object", "coreactivity" ),
			'logged'      => __( "Logged", "coreactivity" )
		);

		foreach ( array_keys( $this->_filter_lock ) as $column ) {
			if ( isset( $columns[ $column ] ) ) {
				unset( $columns[ $column ] );
			}
		}

		if ( isset( $this->_filter_lock[ 'event_id' ] ) ) {
			unset( $columns[ 'component' ] );
		}

		return $columns;
	}

	protected function get_sortable_columns() : array {
		return array(
			'log_id'    => array( 'l.log_id', false ),
			'ip'        => array( 'l.ip', false ),
			'component' => array( 'e.component', false ),
			'event_id'  => array( 'e.event', false )
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
		$render  = '<span>' . $item->ip . '</span>';
		$actions = array(
			'view' => '<a href="' . $this->_view( 'ip', 'filter-ip=' . $item->ip ) . '">' . __( "Logs", "coreactivity" ) . '</a>'
		);

		if ( $this->_display_ip_country_flag ) {
			$ip     = GEOJSIO::instance()->locate( $item->ip );
			$render = $ip->flag() . ' ' . $render;
		}

		if ( $item->ip == $this->_server_ip ) {
			$render .= '<i class="d4p-icon d4p-ui-database" title="' . esc_attr__( "Server IP", "coreactivity" ) . '"></i>';
		}

		if ( $item->ip == $this->_current_ip ) {
			$render .= '<i class="d4p-icon d4p-ui-user-square" title="' . esc_attr__( "Current Request IP", "coreactivity" ) . '"></i>';
		}

		$render = '<div class="coreactivity-field-wrapper">' . $render . '</div>';

		$render  = apply_filters( 'coreactivity_logs_field_render_ip', $render, $item );
		$actions = apply_filters( 'coreactivity_logs_field_actions_ip', $actions, $item );

		return $render . $this->row_actions( $actions );
	}

	public function column_user_id( $item ) : string {
		$render  = '';
		$actions = array(
			'view' => '<a href="' . $this->_view( 'user_id', 'filter-user_id=' . $item->user_id ) . '">' . esc_html__( "Logs", "coreactivity" ) . '</a>'
		);

		if ( $item->user_id == 0 ) {
			if ( $this->_display_user_avatar ) {
				$render .= '<i class="d4p-icon d4p-ui-user-square"></i>';
			}

			$render .= '<span>ID: <strong>0</strong> &middot; ' . esc_html__( "Not a User", "coreactivity" ) . '</span>';
		} else {
			if ( $this->_display_user_avatar ) {
				$render .= get_avatar( $item->user_id, 20 );;
			}

			$user = get_user_by( 'id', $item->user_id );

			$render .= '<span>ID: <strong>' . $item->user_id . '</strong> &middot; ';

			if ( ! $user ) {
				$render .= esc_html__( "Not Found", "coreactivity" );
			} else {
				$render .= $user->display_name;

				$actions[ 'edit' ] = '<a href="user-edit.php?user_id=' . $item->user_id . '">' . esc_html__( "Edit", "coreactivity" ) . '</a>';
			}

			$render .= '</span>';
		}

		$render = '<div class="coreactivity-field-wrapper">' . $render . '</div>';

		$render  = apply_filters( 'coreactivity_logs_field_render_user_id', $render, $item );
		$actions = apply_filters( 'coreactivity_logs_field_actions_user_id', $actions, $item );

		return $render . $this->row_actions( $actions );
	}

	public function column_event_id( $item ) : string {
		return $this->_display_columns_simplified ? Init::instance()->get_event_label( absint( $item->event_id ), $item->event ) : $item->event;
	}

	public function column_object_type( $item ) : string {
		return ! empty( $item->object_type ) ? Init::instance()->get_object_type_label( $item->object_type ) : '/';
	}

	public function column_context( $item ) : string {
		return ! empty( $item->context ) ? strtoupper( $item->context ) : '/';
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
		$sel_ip          = $this->get_request_arg( 'filter-ip' );
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

		if ( isset( $this->_filter_lock[ 'ip' ] ) ) {
			$sel_ip = $this->_filter_lock[ 'ip' ];
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

		if ( ! empty( $sel_ip ) ) {
			$sql[ 'where' ][] = $this->db()->prepare( 'l.`ip` = %s', $sel_ip );
		}

		if ( ! empty( $sel_object_type ) ) {
			$sql[ 'where' ][] = $this->db()->prepare( 'l.`object_type` = %s', $sel_object_type );
		}

		if ( ! empty( $sel_period ) ) {
			$sql[ 'where' ][] = $this->_get_period_where( $sel_period, 'l.`logged`' );
		}

		if ( ! empty( $sel_search ) ) {
			$sql[ 'where' ][] = $this->_get_search_where( array( 'e.`component`', 'e.`ip`', 'e.`event`, l.`request`, l.`object_name`' ), $sel_search );
		}

		$this->query_items( $sql, $per_page, true, true, 'log_id' );
		$this->query_metas( coreactivity_db()->logmeta, 'log_id' );

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

		debugpress_store_object( $this );
	}
}
