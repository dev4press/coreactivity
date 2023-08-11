<?php

namespace Dev4Press\Plugin\CoreActivity\Table;

use Dev4Press\Plugin\CoreActivity\Basic\DB;
use Dev4Press\Plugin\CoreActivity\Log\Core;
use Dev4Press\Plugin\CoreActivity\Log\Display;
use Dev4Press\Plugin\CoreActivity\Log\Activity;
use Dev4Press\v43\Core\Plugins\DBLite;
use Dev4Press\v43\Core\Quick\Sanitize;
use Dev4Press\v43\Core\UI\Elements;
use Dev4Press\v43\Service\GEOIP\GEOJSIO;
use Dev4Press\v43\WordPress\Admin\Table;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Logs extends Table {
	public $_sanitize_orderby_fields = array( 'l.log_id', 'e.component', 'e.event' );
	public $_table_class_name = 'coreactivity-grid-logs';
	public $_checkbox_field = 'log_id';
	public $_self_nonce_key = 'coreactivity-table-logs';
	public $_rows_per_page_key = 'coreactivity_logs_rows_per_page';
	public $_rows_per_page_default = 25;
	public $_views_separator = '';
	public $_display_columns_simplified;
	public $_display_ip_country_flag;
	public $_display_user_avatar;
	public $_display_request_column;
	public $_current_view = '';
	public $_current_ip = '';
	public $_server_ip = '';
	public $_filter_key = 'coreactivity';

	protected $_limit_lock = array();
	protected $_filter_lock = array();
	protected $_filter_remove = array();
	protected $_items_ips = array();

	public function __construct( $args = array() ) {
		Display::instance();

		$this->_current_ip = Core::instance()->get( 'ip' );
		$this->_server_ip  = Core::instance()->get( 'server_ip' );

		$this->_display_columns_simplified = coreactivity_settings()->get( 'display_columns_simplified' );
		$this->_display_ip_country_flag    = coreactivity_settings()->get( 'display_ip_country_flag' );
		$this->_display_user_avatar        = coreactivity_settings()->get( 'display_user_avatar' );
		$this->_display_request_column     = coreactivity_settings()->get( 'display_request_column' );

		parent::__construct( array(
			'singular' => 'log',
			'plural'   => 'logs',
			'ajax'     => false,
		) );
	}

	public function i() : Activity {
		return Activity::instance();
	}

	public function set_limit_lock( $name, $value ) {
		$this->_limit_lock[ $name ] = $value;
	}

	public function set_filter_lock( $name, $value ) {
		$this->_filter_lock[ $name ] = $value;
	}

	public function prepare_items() {
		$this->prepare_column_headers();

		$sql = $this->prepare_query_arguments();

		$this->query_items( $sql, $this->rows_per_page(), true, true, 'log_id' );
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
			'logged'      => __( "Logged", "coreactivity" ),
			'meta'        => '<i class="vers d4p-icon d4p-ui-chevron-square-down d4p-icon-lg" title="' . esc_attr__( "Toggle Meta", "coreactivity" ) . '"></i><span class="screen-reader-text">' . esc_html__( "Toggle Meta", "coreactivity" ) . '</span>',
		);

		if ( ! $this->_display_request_column ) {
			unset( $columns['request'] );
		}

		foreach ( array_keys( $this->_filter_lock ) as $column ) {
			if ( isset( $columns[ $column ] ) ) {
				unset( $columns[ $column ] );
			}
		}

		if ( isset( $this->_filter_lock['event_id'] ) ) {
			unset( $columns['component'] );
		}

		return apply_filters( $this->_filter_key . '_logs_columns', $columns, $this );
	}

	public function single_row( $item ) {
		parent::single_row( $item );

		$classes = $this->get_row_classes( $item, array( 'coreactivity-hidden-row', '__hidden' ) );

		echo '<tr class="' . esc_attr( join( ' ', $classes ) ) . '">';
		$this->single_hidden_row( $item );
		echo '</tr>';
	}

	public function live_attributes() {
		$data = array(
			'lock'   => $this->_filter_lock,
			'atts'   => $this->_request_args,
			'limit'  => $this->_limit_lock,
			'filter' => $this->_filter_key,
			'id'     => DB::instance()->get_last_log_id(),
			'nonce'  => wp_create_nonce( 'coreactivity-live-update' ),
		);

		wp_localize_script( 'd4plib3-coreactivity-admin', 'coreactivity_live', $data );
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
			'min_id'             => 0,
		);

		if ( ! empty( $this->_request_args['filter-component'] ) && ! $this->i()->is_component_valid( $this->_request_args['filter-component'] ) ) {
			$this->_request_args['filter-component'] = '';
		}

		if ( ! empty( $this->_request_args['filter-event_id'] ) && ! $this->i()->is_event_id_valid( $this->_request_args['filter-event_id'] ) ) {
			$this->_request_args['filter-event_id'] = '';
		}

		foreach ( array_keys( $this->_filter_lock ) as $field ) {
			$key = 'filter-' . $field;

			if ( isset( $this->_request_args[ $key ] ) ) {
				$this->_request_args[ $key ] = '';
			}
		}

		if ( isset( $this->_filter_lock['event_id'] ) ) {
			$this->_request_args['filter-component'] = '';
		}

		if ( ! is_multisite() ) {
			$this->_filter_lock['blog_id'] = - 1;
		}

		$this->prepare_the_view();
	}

	protected function prepare_query_settings() : array {
		$sel = array(
			'search'      => $this->get_request_arg( 'search' ),
			'period'      => $this->get_request_arg( 'period' ),
			'blog_id'     => $this->get_request_arg( 'filter-blog_id' ),
			'user_id'     => $this->get_request_arg( 'filter-user_id' ),
			'event_id'    => $this->get_request_arg( 'filter-event_id' ),
			'ip'          => $this->get_request_arg( 'filter-ip' ),
			'component'   => $this->get_request_arg( 'filter-component' ),
			'context'     => $this->get_request_arg( 'filter-context' ),
			'method'      => $this->get_request_arg( 'filter-method' ),
			'object_type' => $this->get_request_arg( 'filter-object_type' ),
			'min_id'      => $this->get_request_arg( 'min_id' ),
		);

		if ( isset( $this->_filter_lock['blog_id'] ) ) {
			$sel['blog_id'] = $this->_filter_lock['blog_id'];
		}

		if ( isset( $this->_filter_lock['user_id'] ) ) {
			$sel['user_id'] = $this->_filter_lock['user_id'];
		}

		if ( isset( $this->_filter_lock['ip'] ) ) {
			$sel['ip'] = $this->_filter_lock['ip'];
		}

		if ( isset( $this->_filter_lock['object_type'] ) ) {
			$sel['object_type'] = $this->_filter_lock['object_type'];
		}

		if ( isset( $this->_filter_lock['component'] ) ) {
			$sel['component'] = $this->_filter_lock['component'];

			if ( isset( $this->_filter_lock['event_id'] ) ) {
				$sel['event_id'] = $this->i()->get_event_id( $sel['component'], $this->_filter_lock['event_id'] );
			}
		} else {
			if ( ! empty( $this->_limit_lock['components'] ) ) {
				$sel['component'] = array_keys( $this->_limit_lock['components'] );
			}
		}

		return apply_filters( $this->_filter_key . '_logs_selection', $sel, $this );
	}

	protected function prepare_query_arguments() : array {
		$sel = $this->prepare_query_settings();

		$sql = array(
			'select' => array(
				'l.*',
				'e.`component`',
				'e.`event`',
			),
			'from'   => array(
				coreactivity_db()->logs . ' l ',
				'INNER JOIN ' . coreactivity_db()->events . ' e ON l.`event_id` = e.`event_id`',
			),
			'where'  => array(),
		);

		if ( ! empty( $sel['blog_id'] ) && $sel['blog_id'] > 0 ) {
			$sql['where'][] = $this->db()->prepare( 'l.`blog_id` = %d', $sel['blog_id'] );
		}

		if ( $sel['min_id'] > 0 ) {
			$sql['where'][] = $this->db()->prepare( 'l.`log_id` > %d', $sel['min_id'] );
		}

		if ( ! empty( $sel['user_id'] ) && $sel['user_id'] > 0 ) {
			$sql['where'][] = $this->db()->prepare( 'l.`user_id` = %d', $sel['user_id'] );
		}

		if ( ! empty( $sel['event_id'] ) && $sel['event_id'] > 0 ) {
			$sql['where'][] = $this->db()->prepare( 'l.`event_id` = %d', $sel['event_id'] );
		} else if ( ! empty( $sel['component'] ) ) {
			$in_components  = $this->db()->prepare_in_list( (array) $sel['component'] );
			$sql['where'][] = 'e.`component` IN (' . $in_components . ')';
		}

		if ( ! empty( $sel['context'] ) ) {
			$sel['context'] = $sel['context'] == '-' ? '' : $sel['context'];
			$sql['where'][] = $this->db()->prepare( 'l.`context` = %s', $sel['context'] );
		}

		if ( ! empty( $sel['method'] ) ) {
			$sql['where'][] = $this->db()->prepare( 'l.`method` = %s', $sel['method'] );
		}

		if ( ! empty( $sel['ip'] ) ) {
			$sql['where'][] = $this->db()->prepare( 'l.`ip` = %s', $sel['ip'] );
		}

		if ( ! empty( $sel['object_type'] ) ) {
			$sql['where'][] = $this->db()->prepare( 'l.`object_type` = %s', $sel['object_type'] );
		}

		if ( ! empty( $sel['period'] ) ) {
			$sql['where'][] = $this->_get_period_where( $sel['period'], 'l.`logged`' );
		}

		if ( ! empty( $sel['search'] ) ) {
			$sql['where'][] = $this->_get_search_where( array( 'e.`component`', 'e.`ip`', 'e.`event`, l.`request`, l.`object_name`' ), $sel['search'] );
		}

		return apply_filters( $this->_filter_key . '_logs_sql', $sql, $this );
	}

	protected function get_select_components() : array {
		if ( ! empty( $this->_limit_lock['components'] ) ) {
			return $this->_limit_lock['components'];
		}

		$components = $this->i()->get_select_event_components( $this->_display_columns_simplified );

		return apply_filters( $this->_filter_key . '_select_components', $components, $this );
	}

	protected function get_select_events() : array {
		$component = $this->_filter_lock['component'] ?? '';

		if ( empty( $component ) && ! empty( $this->_limit_lock['components'] ) ) {
			$component = array_keys( $this->_limit_lock['components'] );
		}

		$events = $this->i()->get_select_events( $this->_display_columns_simplified, empty( $component ) ? array() : (array) $component );

		return apply_filters( $this->_filter_key . '_select_events', $events, $this );
	}

	protected function _view( string $view, string $args ) : string {
		return $this->_url() . '&view=' . $view . '&' . $args;
	}

	protected function _self( $args, $getback = false, $nonce = null ) : string {
		$url = parent::_self( $args, $getback, $nonce );

		if ( ! empty( $this->_current_view ) ) {
			$url .= '&view=' . $this->_current_view;
			$url .= '&filter-' . $this->_current_view . '=' . $this->_filter_lock[ $this->_current_view ];
		}

		return $url;
	}

	protected function db() : ?DBLite {
		return coreactivity_db();
	}

	protected function prepare_the_view() {
		if ( in_array( $this->_request_args['view'], array( 'user_id', 'blog_id', 'event_id', 'ip', 'object_type', 'component' ) ) ) {
			$this->_current_view = $this->_request_args['view'];

			switch ( $this->_current_view ) {
				case 'object_type':
					if ( ! isset( $this->_filter_lock['object_type'] ) && ! empty( $this->_request_args['filter-object_type'] ) ) {
						$this->_filter_lock['object_type'] = $this->_request_args['filter-object_type'];
					} else {
						$this->_current_view = '';
					}
					break;
				case 'component':
					if ( ! isset( $this->_filter_lock['component'] ) && ! empty( $this->_request_args['filter-component'] ) ) {
						$this->_filter_lock['component'] = $this->_request_args['filter-component'];
					} else {
						$this->_current_view = '';
					}
					break;
				case 'event_id':
					if ( ! isset( $this->_filter_lock['event_id'] ) && ! empty( $this->_request_args['filter-event_id'] ) ) {
						$this->_filter_lock['event_id'] = $this->_request_args['filter-event_id'];
					} else {
						$this->_current_view = '';
					}
					break;
				case 'user_id':
					if ( ! isset( $this->_filter_lock['user_id'] ) && ! empty( $this->_request_args['filter-user_id'] ) ) {
						$this->_filter_lock['user_id'] = $this->_request_args['filter-user_id'];
					} else {
						$this->_current_view = '';
					}
					break;
				case 'blog_id':
					if ( ! isset( $this->_filter_lock['blog_id'] ) && ! empty( $this->_request_args['filter-blog_id'] ) ) {
						$this->_filter_lock['blog_id'] = $this->_request_args['filter-blog_id'];
					} else {
						$this->_current_view = '';
					}
					break;
				case 'ip':
					if ( ! isset( $this->_filter_lock['ip'] ) && ! empty( $this->_request_args['filter-ip'] ) ) {
						$this->_filter_lock['ip'] = $this->_request_args['filter-ip'];
					} else {
						$this->_current_view = '';
					}
					break;
			}
		}
	}

	protected function filter_block_top() {
		echo '<div class="alignleft actions">';
		Elements::instance()->select( $this->get_period_dropdown( 'logged', coreactivity_db()->logs ), array(
			'selected' => $this->get_request_arg( 'period' ),
			'name'     => 'period',
		) );

		if ( ! isset( $this->_filter_lock['component'] ) && ! isset( $this->_filter_lock['event_id'] ) ) {
			Elements::instance()->select_grouped( $this->get_select_components(), array(
				'empty'    => __( "All Components", "coreactivity" ),
				'selected' => $this->get_request_arg( 'filter-component' ),
				'name'     => 'filter-component',
			) );
		}

		if ( ! isset( $this->_filter_lock['event_id'] ) ) {
			Elements::instance()->select_grouped( $this->get_select_events(), array(
				'empty'    => __( "All Events", "coreactivity" ),
				'selected' => $this->get_request_arg( 'filter-event_id' ),
				'name'     => 'filter-event_id',
			) );
		}

		if ( ! isset( $this->_filter_lock['context'] ) ) {
			$_contexts = array(
				''  => __( "All Contexts", "coreactivity" ),
				'-' => __( "Normal", "coreactivity" ),
			);

			foreach ( Core::instance()->valid_request_contexts() as $context ) {
				$_contexts[ $context ] = $context;
			}

			Elements::instance()->select( $_contexts, array(
				'selected' => $this->get_request_arg( 'filter-context' ),
				'name'     => 'filter-context',
			) );
		}

		if ( ! isset( $this->_filter_lock['method'] ) ) {
			$_methods = array(
				'' => __( "All Methods", "coreactivity" ),
			);

			foreach ( Core::instance()->valid_request_methods() as $method ) {
				$_methods[ $method ] = $method;
			}

			Elements::instance()->select( $_methods, array(
				'selected' => $this->get_request_arg( 'filter-method' ),
				'name'     => 'filter-method',
			) );
		}

		if ( ! in_array( 'object_type', $this->_filter_remove ) ) {
			if ( ! isset( $this->_filter_lock['object_type'] ) ) {
				$_types = array(
					'' => __( "All Object Types", "coreactivity" ),
				);

				foreach ( $this->i()->get_object_types() as $type => $value ) {
					$_types[ $type ] = $value;
				}

				Elements::instance()->select( $_types, array(
					'selected' => $this->get_request_arg( 'filter-object_type' ),
					'name'     => 'filter-object_type',
				) );
			}
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

			$views['all'] = '<a class="coreactivity-view-button" href="' . $this->_url() . '"><i class="d4p-icon d4p-ui-angles-left"></i> ' . __( "All Logs", "coreactivity" ) . '</a>';

			switch ( $this->_current_view ) {
				case 'object_type':
					$current_view = '<span class="coreactivity-view-button"><i class="d4p-icon d4p-ui-archive"></i> ';
					$current_view .= '<span>' . esc_html__( "Object Type", "coreactivity" ) . '</span>';
					$current_view .= $this->i()->get_object_type_label( $this->_filter_lock['object_type'] );
					$current_view .= '<span>[' . esc_html( $this->_filter_lock['object_type'] ) . ']</span>';
					$current_view .= '</span>';

					$current_key .= 'object_type';
					break;
				case 'component':
					$current_view = '<span class="coreactivity-view-button">';
					$current_view .= '<i class="d4p-icon d4p-' . $this->i()->get_component_icon( $this->_filter_lock['component'] ) . ' d4p-icon-fw"></i>';
					$current_view .= '<span>' . esc_html__( "Component", "coreactivity" ) . '</span>';
					$current_view .= $this->i()->get_component_label( $this->_filter_lock['component'] );
					$current_view .= '<span>[' . esc_html( $this->_filter_lock['component'] ) . ']</span>';
					$current_view .= '</span>';

					$current_key .= 'component';
					break;
				case 'event_id':
					$event = $this->i()->get_event_by_id( $this->_filter_lock['event_id'] );
					$label = $this->i()->get_component_label( $event->component );

					$current_view = '<span class="coreactivity-view-button"><i class="d4p-icon d4p-ui-radar d4p-icon-fw"></i> <span>' . esc_html__( "Event", "coreactivity" ) . '</span>';
					$current_view .= $label . ' / ' . $event->label;
					$current_view .= '<span>[' . $event->component . '/' . $event->event . ']</span>';
					$current_view .= '</span>';

					$current_key .= 'component';
					break;
				case 'ip':
					$current_view = '<span class="coreactivity-view-button"><i class="d4p-icon d4p-ui-cloud d4p-icon-fw"></i> <span>' . esc_html__( "IP", "coreactivity" ) . '</span>';

					if ( $this->_display_ip_country_flag ) {
						$ip           = GEOJSIO::instance()->locate( $this->_filter_lock['ip'] );
						$current_view .= '<span>' . esc_html( $this->_filter_lock['ip'] ) . '</span> ' . $ip->flag();
					} else {
						$current_view .= $this->_filter_lock['ip'];
					}

					$current_view .= '</span>';
					$current_key  .= 'ip';
					break;
				case 'user_id':
					$user = get_user_by( 'id', $this->_filter_lock['user_id'] );
					$name = ! $user ? __( "Not Found", "coreactivity" ) : $user->display_name;

					$current_view = '<span class="coreactivity-view-button"><i class="d4p-icon d4p-ui-user d4p-icon-fw"></i> <span>' . esc_html__( "User", "coreactivity" ) . '</span>';
					$current_view .= '<span>' . esc_html( $this->_filter_lock['user_id'] . ' : ' . $name ) . '</span>';

					if ( $this->_filter_lock['user_id'] > 0 && $this->_display_user_avatar ) {
						$current_view .= ' ' . get_avatar( $this->_filter_lock['user_id'], 30 );
					}

					$current_view .= '</span>';
					$current_key  .= 'user';
					break;
			}

			$views[ $current_key ] = $current_view;
		}

		return $views;
	}

	protected function get_sortable_columns() : array {
		return array(
			'log_id'    => array( 'l.log_id', false ),
			'ip'        => array( 'l.ip', false ),
			'component' => array( 'e.component', false ),
			'event_id'  => array( 'e.event', false ),
		);
	}

	protected function get_bulk_actions() : array {
		return array(
			'delete' => __( "Delete", "coreactivity" ),
		);
	}

	protected function single_hidden_row( $item ) {
		list( $columns, $hidden, $sortable, $primary ) = $this->get_column_info();

		$total = count( $columns ) - count( $hidden );
		$metas = array( 'referer', 'user_agent', 'ajax_action' );

		$left  = array();
		$right = array();

		if ( ! $this->_display_request_column && ! empty( $item->request ) ) {
			$left[] = '<li><strong>' . esc_html__( "request", "coreactivity" ) . ':</strong><span>' . esc_html( $item->request ) . '</span></li>';
		}

		if ( isset( $item->meta ) ) {
			foreach ( $item->meta as $key => $value ) {
				$value = is_scalar( $value ) ? esc_html( $value ) : ( is_array( $value ) && count( $value ) < 20 ? $this->print_array( $value ) : json_encode( $value ) );

				if ( in_array( $key, $metas ) ) {
					$left[] = '<li><strong>' . esc_html( $key ) . ':</strong><span>' . $value . '</span></li>';
				} else {
					$right[] = '<li><strong>' . esc_html( $key ) . ':</strong><span>' . $value . '</span></li>';
				}
			}
		}

		$description = apply_filters( 'coreactivity_logs_log_item_descriptions', '', $item );

		echo '<td colspan="' . esc_attr( $total ) . '">';

		if ( ! empty( $description ) ) {
			echo '<div>' . $this->kses( $description ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		echo '<div>';

		echo '<ul>' . join( '', $right ) . '</ul>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<ul>' . join( '', $left ) . '</ul>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		echo '</div>';
		echo '</td>';
	}

	protected function column_default( $item, $column_name ) : string {
		$render  = isset( $item->$column_name ) ? (string) $item->$column_name : '/';
		$actions = array();

		$render  = apply_filters( 'coreactivity_logs_field_render_' . $column_name, $render, $item, $this );
		$actions = apply_filters( 'coreactivity_logs_field_actions_' . $column_name, $actions, $item, $this );

		return $render . $this->row_actions( $actions );
	}

	protected function column_component( $item ) : string {
		$render = $this->_display_columns_simplified ? $this->i()->get_component_label( $item->component ) : $item->component;

		$actions = array(
			'view' => '<a href="' . $this->_view( 'component', 'filter-component=' . $item->component ) . '">' . __( "Logs", "coreactivity" ) . '</a>',
		);

		$render  = apply_filters( 'coreactivity_logs_field_render_component', $render, $item, $this );
		$actions = apply_filters( 'coreactivity_logs_field_actions_component', $actions, $item, $this );

		return $render . $this->row_actions( $actions );
	}

	protected function column_ip( $item ) : string {
		$render  = '<span>' . $item->ip . '</span>';
		$actions = array(
			'view' => '<a href="' . $this->_view( 'ip', 'filter-ip=' . $item->ip ) . '">' . __( "Logs", "coreactivity" ) . '</a>',
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

		$render  = apply_filters( 'coreactivity_logs_field_render_ip', $render, $item, $this );
		$actions = apply_filters( 'coreactivity_logs_field_actions_ip', $actions, $item, $this );

		return $render . $this->row_actions( $actions );
	}

	protected function column_user_id( $item ) : string {
		$render  = '';
		$actions = array(
			'view' => '<a href="' . $this->_view( 'user_id', 'filter-user_id=' . $item->user_id ) . '">' . esc_html__( "Logs", "coreactivity" ) . '</a>',
		);

		if ( $item->user_id == 0 ) {
			if ( $this->_display_user_avatar ) {
				$render .= '<i class="d4p-icon d4p-ui-user-square"></i>';
			}

			$render .= '<span>ID: <strong>0</strong> &middot; ' . esc_html__( "Not a User", "coreactivity" ) . '</span>';
		} else {
			if ( $this->_display_user_avatar ) {
				$render .= get_avatar( $item->user_id, 20 );
			}

			$user = get_user_by( 'id', $item->user_id );

			$render .= '<span>ID: <strong>' . $item->user_id . '</strong> &middot; ';

			if ( ! $user ) {
				$render .= esc_html__( "Not Found", "coreactivity" );
			} else {
				$render .= $user->display_name;

				$actions['edit'] = '<a href="user-edit.php?user_id=' . $item->user_id . '">' . esc_html__( "Edit", "coreactivity" ) . '</a>';
			}

			$render .= '</span>';
		}

		$render = '<div class="coreactivity-field-wrapper">' . $render . '</div>';

		$render  = apply_filters( 'coreactivity_logs_field_render_user_id', $render, $item, $this );
		$actions = apply_filters( 'coreactivity_logs_field_actions_user_id', $actions, $item, $this );

		return $render . $this->row_actions( $actions );
	}

	protected function column_event_id( $item ) : string {
		$render = $this->_display_columns_simplified ? $this->i()->get_event_label( absint( $item->event_id ), $item->event ) : $item->event;

		$actions = array(
			'view' => '<a href="' . $this->_view( 'event_id', 'filter-event_id=' . $item->event_id ) . '">' . __( "Logs", "coreactivity" ) . '</a>',
		);

		$render  = apply_filters( 'coreactivity_logs_field_render_event_id', $render, $item, $this );
		$actions = apply_filters( 'coreactivity_logs_field_actions_event_id', $actions, $item, $this );

		return $render . $this->row_actions( $actions );
	}

	protected function column_object_type( $item ) : string {
		$render = ! empty( $item->object_type ) ? $this->i()->get_object_type_label( $item->object_type ) : '/';

		$actions = array(
			'view' => '<a href="' . $this->_view( 'object_type', 'filter-object_type=' . $item->object_type ) . '">' . __( "Logs", "coreactivity" ) . '</a>',
		);

		if ( empty( $item->object_type ) ) {
			unset( $actions['view'] );
		}

		$render  = apply_filters( 'coreactivity_logs_field_render_object_type', $render, $item, $this );
		$actions = apply_filters( 'coreactivity_logs_field_actions_object_type', $actions, $item, $this );

		return $render . $this->row_actions( $actions );
	}

	protected function column_object_name( $item ) : string {
		$render = $item->object_name ?? '';
		$render = apply_filters( 'coreactivity_logs_field_render_object_name', (string) $render, $item, $this );

		return $this->kses( $render );
	}

	protected function column_context( $item ) : string {
		$render = ! empty( $item->context ) ? strtoupper( $item->context ) : '/';

		$actions = array();

		$render  = apply_filters( 'coreactivity_logs_field_render_context', $render, $item, $this );
		$actions = apply_filters( 'coreactivity_logs_field_actions_context', $actions, $item, $this );

		return $render . $this->row_actions( $actions );
	}

	protected function column_method( $item ) : string {
		$render = $item->method;

		$actions = array();

		$render  = apply_filters( 'coreactivity_logs_field_render_method', $render, $item, $this );
		$actions = apply_filters( 'coreactivity_logs_field_actions_method', $actions, $item, $this );

		return $render . $this->row_actions( $actions );
	}

	protected function column_protocol( $item ) : string {
		$render = $item->protocol;

		$actions = array();

		$render  = apply_filters( 'coreactivity_logs_field_render_protocol', $render, $item, $this );
		$actions = apply_filters( 'coreactivity_logs_field_actions_protocol', $actions, $item, $this );

		return $render . $this->row_actions( $actions );
	}

	protected function column_logged( $item ) : string {
		$timestamp = coreactivity()->datetime()->timestamp_gmt_to_local( strtotime( $item->logged ) );
		$render    = gmdate( 'Y.m.d', $timestamp ) . '<br/>@ ' . gmdate( 'H:i:s', $timestamp );

		$actions = array();

		$render  = apply_filters( 'coreactivity_logs_field_render_logged', $render, $item, $this );
		$actions = apply_filters( 'coreactivity_logs_field_actions_logged', $actions, $item, $this );

		return $render . $this->row_actions( $actions );
	}

	protected function column_meta( $item ) : string {
		return '<button type="button" aria-label="' . esc_attr__( "Show Log Meta Data", "coreactivity" ) . '"><i class="d4p-icon d4p-ui-chevron-square-down d4p-icon-lg"></i></button>';
	}

	private function print_array( $input ) : string {
		$render = array();

		foreach ( $input as $key => $value ) {
			$render[] = $key . ': ' . esc_html( is_scalar( $value ) ? $value : json_encode( $value ) );
		}

		return join( '<br/>', $render );
	}

	private function kses( $render ) : string {
		$allowed_tags = array(
			'a'      => array( 'href' => true ),
			'br'     => array(),
			'strong' => array(),
		);

		return wp_kses( $render, $allowed_tags );
	}
}
