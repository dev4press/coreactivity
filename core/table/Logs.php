<?php

namespace Dev4Press\Plugin\CoreActivity\Table;

use Dev4Press\Plugin\CoreActivity\Basic\DB;
use Dev4Press\Plugin\CoreActivity\Log\Core;
use Dev4Press\Plugin\CoreActivity\Log\Device;
use Dev4Press\Plugin\CoreActivity\Log\Display;
use Dev4Press\Plugin\CoreActivity\Log\Activity;
use Dev4Press\Plugin\CoreActivity\Log\GEO;
use Dev4Press\v47\Core\Helpers\IP;
use Dev4Press\v47\Core\Plugins\DBLite;
use Dev4Press\v47\Core\Quick\Sanitize;
use Dev4Press\v47\Core\UI\Elements;
use Dev4Press\v47\WordPress\Admin\Table;
use WP_Site;

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
	public $_current_view = '';
	public $_current_ip = '';
	public $_server_ip = '';

	protected $_display_blog_column_linked;
	protected $_display_columns_simplified;
	protected $_display_ip_country_flag;
	protected $_display_user_avatar;
	protected $_display_request_column;
	protected $_display_detection_column;
	protected $_display_protocol_column;
	protected $_display_object_type_column;
	protected $_display_meta_column = null;
	protected $_filter_key = 'coreactivity';
	protected $_logs_instance = 'coreactivity';
	protected $_limit_lock = array();
	protected $_filter_lock = array();
	protected $_filter_remove = array();
	protected $_items_ips = array();
	protected $_meta_column = array();
	protected $_view_pairs = array(
		'object' => array( 'object_type', 'object_id', 'object_name' ),
	);
	protected $_valid_views = array(
		'user_id',
		'blog_id',
		'event_id',
		'ip',
		'object_type',
		'object',
		'country_code',
		'component',
		'context',
		'method',
	);
	protected $_allowed_override_settings = array(
		'_display_meta_column',
		'_logs_instance',
		'_filter_key',
		'_meta_column',
		'_rows_per_page_key',
		'_current_view',
	);

	public function __construct( $args = array() ) {
		Display::instance();

		foreach ( $args as $key => $value ) {
			if ( property_exists( $this, $key ) ) {
				$this->$key = $value;
			}
		}

		$this->_current_ip = Core::instance()->get( 'ip' );
		$this->_server_ip  = Core::instance()->get( 'server_ip' );

		$this->_display_blog_column_linked = coreactivity_settings()->get( 'display_blog_column_linked' );
		$this->_display_columns_simplified = coreactivity_settings()->get( 'display_columns_simplified' );
		$this->_display_ip_country_flag    = coreactivity_settings()->get( 'display_ip_country_flag' );
		$this->_display_user_avatar        = coreactivity_settings()->get( 'display_user_avatar' );
		$this->_display_request_column     = coreactivity_settings()->get( 'display_request_column' );
		$this->_display_protocol_column    = coreactivity_settings()->get( 'display_protocol_column' );
		$this->_display_detection_column   = coreactivity_settings()->get( 'display_detection_column' );
		$this->_display_object_type_column = coreactivity_settings()->get( 'display_object_type_column' );

		if ( is_null( $this->_display_meta_column ) ) {
			$this->_display_meta_column = coreactivity_settings()->get( 'display_meta_column' );
		}

		if ( $this->_display_meta_column ) {
			$this->_meta_column = apply_filters( 'coreactivity_logs_meta_column_keys', array(), $this );
		}

		parent::__construct( array(
			'singular' => 'log',
			'plural'   => 'logs',
			'ajax'     => false,
		) );
	}

	public function i() : Activity {
		return Activity::instance();
	}

	public function get_instance_code() : string {
		return $this->_logs_instance;
	}

	public function get_columns_simplified() : bool {
		return $this->_display_columns_simplified;
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

		foreach ( $this->items as &$item ) {
			$ua = $item->meta['user_agent'] ?? '';

			if ( isset( $item->meta['device'] ) ) {
				$item->{"device"} = $item->meta['device'];

				unset( $item->meta['device'] );
			}

			if ( ! isset( $item->device ) || ( ! isset( $item->device['bot'] ) && empty( $item->device['client'] ) && empty( $item->device['os'] ) ) ) {
				if ( ! empty( $ua ) ) {
					$item->{"device"} = Device::instance()->detect( $ua );
				}
			}

			if ( $this->_display_ip_country_flag ) {
				$ip = $item->ip;

				if ( ! in_array( $ip, $this->_items_ips ) ) {
					$this->_items_ips[] = $ip;
				}
			}
		}

		if ( ! empty( $this->_items_ips ) ) {
			GEO::instance()->bulk( $this->_items_ips );
		}
	}

	public function get_columns() : array {
		$columns = array(
			'cb'          => '<input type="checkbox" />',
			'log_id'      => __( 'ID', 'coreactivity' ),
			'blog_id'     => __( 'Blog', 'coreactivity' ),
			'user_id'     => __( 'User', 'coreactivity' ),
			'component'   => __( 'Component', 'coreactivity' ),
			'event_id'    => __( 'Event', 'coreactivity' ),
			'context'     => __( 'Context', 'coreactivity' ),
			'method'      => __( 'Method', 'coreactivity' ),
			'protocol'    => __( 'Protocol', 'coreactivity' ),
			'request'     => __( 'Request', 'coreactivity' ),
			'object_type' => __( 'Object Type', 'coreactivity' ),
			'object_name' => __( 'Object', 'coreactivity' ),
			'meta_data'   => __( 'Meta', 'coreactivity' ),
			'ip'          => __( 'IP', 'coreactivity' ),
			'detection'   => __( 'Detection', 'coreactivity' ),
			'logged'      => __( 'Logged', 'coreactivity' ),
			'meta'        => '<i class="vers d4p-icon d4p-ui-chevron-square-down d4p-icon-lg" title="' . esc_attr__( 'Toggle Meta', 'coreactivity' ) . '"></i><span class="screen-reader-text">' . esc_html__( 'Toggle Meta', 'coreactivity' ) . '</span>',
		);

		if ( ! $this->_display_request_column ) {
			unset( $columns['request'] );
		}

		if ( ! $this->_display_protocol_column ) {
			unset( $columns['protocol'] );
		}

		if ( ! $this->_display_detection_column ) {
			unset( $columns['detection'] );
		}

		if ( ! $this->_display_meta_column ) {
			unset( $columns['meta_data'] );
		}

		if ( ! $this->_display_object_type_column ) {
			unset( $columns['object_type'] );
		}

		foreach ( array_keys( $this->_filter_lock ) as $column ) {
			if ( isset( $columns[ $column ] ) ) {
				unset( $columns[ $column ] );
			}
		}

		if ( isset( $this->_filter_lock['event_id'] ) ) {
			unset( $columns['component'] );
		}

		if ( $this->_current_view == 'event_id' ) {
			if ( ! Activity::instance()->is_event_object_linked( $this->_filter_lock['event_id'] ) ) {
				unset( $columns['object_name'] );

				if ( isset( $columns['object_type'] ) ) {
					unset( $columns['object_type'] );
				}
			}
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
			'lock'     => $this->_filter_lock,
			'atts'     => $this->_request_args,
			'limit'    => $this->_limit_lock,
			'filter'   => $this->_filter_key,
			'settings' => array(),
			'id'       => DB::instance()->get_last_log_id(),
			'nonce'    => wp_create_nonce( 'coreactivity-live-update' ),
		);

		foreach ( $this->_allowed_override_settings as $key ) {
			$data['settings'][ $key ] = $this->$key;
		}

		wp_localize_script( coreactivity_admin()->e()->prefix() . 'coreactivity-admin', 'coreactivity_live', $data );
	}

	protected function process_request_args() {
		$this->_request_args = array(
			'filter-blog_id'      => Sanitize::_get_absint( 'filter-blog_id' ),
			'filter-user_id'      => Sanitize::_get_absint( 'filter-user_id' ),
			'filter-event_id'     => Sanitize::_get_absint( 'filter-event_id' ),
			'filter-ip'           => Sanitize::_get_basic( 'filter-ip' ),
			'filter-component'    => Sanitize::_get_basic( 'filter-component' ),
			'filter-country_code' => strtoupper( Sanitize::_get_slug( 'filter-country_code' ) ),
			'filter-context'      => strtoupper( Sanitize::_get_slug( 'filter-context' ) ),
			'filter-method'       => strtoupper( Sanitize::_get_slug( 'filter-method' ) ),
			'filter-object_type'  => Sanitize::_get_slug( 'filter-object_type' ),
			'filter-object_id'    => Sanitize::_get_absint( 'filter-object_id' ),
			'filter-object_name'  => Sanitize::_get_basic( 'filter-object_name' ),
			'view'                => $this->_get_field( 'view' ),
			'search'              => $this->_get_field( 's' ),
			'period'              => $this->_get_field( 'period' ),
			'orderby'             => $this->_get_field( 'orderby', 'l.log_id' ),
			'order'               => $this->_get_field( 'order', 'DESC' ),
			'paged'               => $this->_get_field( 'paged' ),
			'min_id'              => 0,
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
			'search'       => $this->get_request_arg( 'search' ),
			'period'       => $this->get_request_arg( 'period' ),
			'blog_id'      => $this->get_request_arg( 'filter-blog_id' ),
			'user_id'      => $this->get_request_arg( 'filter-user_id' ),
			'event_id'     => $this->get_request_arg( 'filter-event_id' ),
			'country_code' => $this->get_request_arg( 'filter-country_code' ),
			'ip'           => $this->get_request_arg( 'filter-ip' ),
			'component'    => $this->get_request_arg( 'filter-component' ),
			'context'      => $this->get_request_arg( 'filter-context' ),
			'method'       => $this->get_request_arg( 'filter-method' ),
			'object_type'  => $this->get_request_arg( 'filter-object_type' ),
			'object_id'    => $this->get_request_arg( 'filter-object_id' ),
			'object_name'  => $this->get_request_arg( 'filter-object_name' ),
			'min_id'       => $this->get_request_arg( 'min_id' ),
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

		if ( isset( $this->_filter_lock['country_code'] ) ) {
			$sel['country_code'] = $this->_filter_lock['country_code'];
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
			$_locked = $this->locked_components();

			if ( ! empty( $_locked ) ) {
				$sel['component'] = in_array( $sel['component'], $_locked ) ? $sel['component'] : $_locked;
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

		if ( ! empty( $sel['country_code'] ) ) {
			$sql['where'][] = $this->db()->prepare( 'l.`country_code` = %s', $sel['country_code'] );
		}

		if ( ! empty( $sel['object_type'] ) ) {
			$sql['where'][] = $this->db()->prepare( 'l.`object_type` = %s', $sel['object_type'] );
		}

		if ( ! empty( $sel['object_id'] ) ) {
			$sql['where'][] = $this->db()->prepare( 'l.`object_id` = %d', $sel['object_id'] );
		}

		if ( ! empty( $sel['object_name'] ) ) {
			$sql['where'][] = $this->db()->prepare( 'l.`object_name` = %s', $sel['object_name'] );
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

		if ( empty( $component ) ) {
			$component = $this->locked_components();
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
		if ( in_array( $this->_request_args['view'], $this->_valid_views ) ) {
			$this->_current_view = $this->_request_args['view'];

			switch ( $this->_current_view ) {
				case 'object':
					if ( ! isset( $this->_filter_lock['object_type'] ) && ! empty( $this->_request_args['filter-object_type'] ) ) {
						$this->_filter_lock['object_type'] = $this->_request_args['filter-object_type'];

						if ( ! isset( $this->_filter_lock['object_name'] ) && ! empty( $this->_request_args['filter-object_name'] ) ) {
							$this->_filter_lock['object_name'] = $this->_request_args['filter-object_name'];
						}

						if ( ! isset( $this->_filter_lock['object_id'] ) && $this->_request_args['filter-object_id'] != 0 ) {
							$this->_filter_lock['object_id'] = $this->_request_args['filter-object_id'];
						}
					} else {
						$this->_current_view = '';
					}
					break;
				default:
					if ( ! isset( $this->_filter_lock[ $this->_current_view ] ) && ! empty( $this->_request_args[ 'filter-' . $this->_current_view ] ) ) {
						$this->_filter_lock[ $this->_current_view ] = $this->_request_args[ 'filter-' . $this->_current_view ];
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
				'empty'    => __( 'All Components', 'coreactivity' ),
				'selected' => $this->get_request_arg( 'filter-component' ),
				'name'     => 'filter-component',
			) );
		}

		if ( ! isset( $this->_filter_lock['event_id'] ) ) {
			Elements::instance()->select_grouped( $this->get_select_events(), array(
				'empty'    => __( 'All Events', 'coreactivity' ),
				'selected' => $this->get_request_arg( 'filter-event_id' ),
				'name'     => 'filter-event_id',
			) );
		}

		if ( ! isset( $this->_filter_lock['context'] ) ) {
			$_contexts = array(
				''  => __( 'All Contexts', 'coreactivity' ),
				'-' => __( 'Normal', 'coreactivity' ),
			);

			foreach ( Core::instance()->valid_request_contexts() as $context ) {
				$_contexts[ $context ] = $context;
			}

			Elements::instance()->select( $_contexts, array(
				'selected' => $this->get_request_arg( 'filter-context' ),
				'name'     => 'filter-context',
			) );
		}

		Elements::instance()->select_grouped( $this->get_country_codes(), array(
			'empty'    => __( 'All Countries', 'coreactivity' ),
			'selected' => $this->get_request_arg( 'filter-country_code' ),
			'name'     => 'filter-country_code',
		) );

		if ( ! isset( $this->_filter_lock['method'] ) ) {
			$_methods = array(
				'' => __( 'All Methods', 'coreactivity' ),
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
					'' => __( 'All Object Types', 'coreactivity' ),
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

		submit_button( __( 'Filter', 'coreactivity' ), 'button', false, false, array( 'id' => 'coreactivity-events-submit' ) );
		echo '</div>';
	}

	protected function get_country_codes() : array {
		$sql = "SELECT DISTINCT `country_code` FROM " . $this->db()->logs . " WHERE `country_code` IS NOT NULL ORDER BY `country_code`";
		$raw = $this->db()->get_results( $sql );

		$list = array(
			'XX' => __( 'Localhost or Private', 'coreactivity' ),
		);

		$has_localhost = false;
		foreach ( $raw as $row ) {
			$code = $row->country_code;

			if ( empty( $code ) ) {
				continue;
			}

			if ( $code == 'XX' ) {
				$has_localhost = true;
			} else {
				$list[ $code ] = trim( '[' . $code . '] ' . GEO::instance()->country( $code ) );
			}
		}

		if ( ! $has_localhost ) {
			unset( $list['XX'] );
		}

		return $list;
	}

	protected function get_views() : array {
		$views = array();

		if ( ! empty( $this->_current_view ) ) {
			?>
            <input type="hidden" name="view" value="<?php echo esc_attr( $this->_current_view ); ?>"/>
			<?php

			if ( isset( $this->_view_pairs[ $this->_current_view ] ) ) {
				foreach ( $this->_view_pairs[ $this->_current_view ] as $element ) {
					if ( ! isset( $this->_filter_lock[ $element ] ) ) {
						continue;
					}
					?>
                    <input type="hidden" name="filter-<?php echo esc_attr( $element ); ?>" value="<?php echo esc_attr( $this->_filter_lock[ $element ] ); ?>"/>
					<?php
				}
			} else {
				?>
                <input type="hidden" name="filter-<?php echo esc_attr( $this->_current_view ); ?>" value="<?php echo esc_attr( $this->_filter_lock[ $this->_current_view ] ); ?>"/>
				<?php
			}

			$current_view = '';
			$current_key  = 'view ';

			$views['all'] = '<a class="coreactivity-view-button" href="' . $this->_url() . '"><i class="d4p-icon d4p-ui-angles-left"></i> ' . __( 'All Logs', 'coreactivity' ) . '</a>';

			switch ( $this->_current_view ) {
				case 'object':
					$current_view = '<span class="coreactivity-view-button"><i class="d4p-icon d4p-ui-archive"></i> ';
					$current_view .= '<span>' . $this->i()->get_object_type_label( $this->_filter_lock['object_type'] ) . '</span>';

					if ( ! empty( $this->_filter_lock['object_name'] ) ) {
						$current_view .= '<span>' . esc_html( $this->_filter_lock['object_name'] ) . '</span>';
					} else {
						$current_view .= '<span>ID: ' . esc_html( $this->_filter_lock['object_id'] ) . '</span>';
					}

					$current_view .= '</span>';

					$current_key .= 'object_type';
					break;
				case 'object_type':
					$current_view = '<span class="coreactivity-view-button"><i class="d4p-icon d4p-ui-archive"></i> ';
					$current_view .= '<span>' . esc_html__( 'Object Type', 'coreactivity' ) . '</span>';
					$current_view .= $this->i()->get_object_type_label( $this->_filter_lock['object_type'] );
					$current_view .= '<span>[' . esc_html( $this->_filter_lock['object_type'] ) . ']</span>';
					$current_view .= '</span>';

					$current_key .= 'object_type';
					break;
				case 'context':
					$display = $this->_filter_lock['context'] == '-' ? esc_html__( 'Normal', 'coreactivity' ) : $this->_filter_lock['context'];

					$current_view = '<span class="coreactivity-view-button"><i class="d4p-icon d4p-ui-traffic"></i> ';
					$current_view .= '<span>' . esc_html__( 'Context', 'coreactivity' ) . '</span>';
					$current_view .= $display;
					$current_view .= '</span>';

					$current_key .= 'context';
					break;
				case 'method':
					$current_view = '<span class="coreactivity-view-button"><i class="d4p-icon d4p-ui-browser"></i> ';
					$current_view .= '<span>' . esc_html__( 'Request Method', 'coreactivity' ) . '</span>';
					$current_view .= $this->_filter_lock['method'];
					$current_view .= '</span>';

					$current_key .= 'method';
					break;
				case 'component':
					$current_view = '<span class="coreactivity-view-button">';
					$current_view .= '<i class="d4p-icon d4p-' . $this->i()->get_component_icon( $this->_filter_lock['component'] ) . ' d4p-icon-fw"></i>';
					$current_view .= '<span>' . esc_html__( 'Component', 'coreactivity' ) . '</span>';
					$current_view .= $this->i()->get_component_label( $this->_filter_lock['component'] );
					$current_view .= '<span>[' . esc_html( $this->_filter_lock['component'] ) . ']</span>';
					$current_view .= '</span>';

					$current_key .= 'component';
					break;
				case 'event_id':
					$event = $this->i()->get_event_by_id( $this->_filter_lock['event_id'] );
					$label = $this->i()->get_component_label( $event->component );

					$current_view = '<span class="coreactivity-view-button"><i class="d4p-icon d4p-ui-radar d4p-icon-fw"></i> <span>' . esc_html__( 'Event', 'coreactivity' ) . '</span>';
					$current_view .= $label . ' / ' . $event->label;
					$current_view .= '<span>[' . $event->component . '/' . $event->event . ']</span>';
					$current_view .= '</span>';

					$current_key .= 'component';
					break;
				case 'blog_id':
					$blog = get_blog_details( array( 'blog_id' => $this->_filter_lock['blog_id'] ) );

					$current_view = '<span class="coreactivity-view-button"><i class="d4p-icon d4p-ui-eye d4p-icon-fw"></i> <span>' . esc_html__( 'Blog', 'coreactivity' ) . '</span>';

					if ( $blog instanceof WP_Site ) {
						$current_view .= $blog->blogname . '<a target="_blank" href="' . $blog->siteurl . '">' . $blog->siteurl . '</a>';
						$current_view .= '<span>[ID: ' . $this->_filter_lock['blog_id'] . ']</span>';
					} else {
						$current_view .= 'ID: ' . $this->_filter_lock['blog_id'];
					}

					$current_view .= '</span>';

					$current_key .= 'blog_id';
					break;
				case 'ip':
					$current_view = '<span class="coreactivity-view-button"><i class="d4p-icon d4p-ui-cloud d4p-icon-fw"></i> <span>' . esc_html__( 'IP', 'coreactivity' ) . '</span>';

					if ( $this->_display_ip_country_flag ) {
						$current_view .= '<span>' . esc_html( $this->_filter_lock['ip'] ) . '</span> ' . GEO::instance()->flag( $this->_filter_lock['ip'] );
					} else {
						$current_view .= $this->_filter_lock['ip'];
					}

					$current_view .= '</span>';
					$current_key  .= 'ip';
					break;
				case 'country_code':
					$current_view = '<span class="coreactivity-view-button"><i class="d4p-icon d4p-ui-globe d4p-icon-fw"></i> <span>' . esc_html__( 'Country', 'coreactivity' ) . '</span>';
					$current_view .= '<span>[' . $this->_filter_lock['country_code'] . '] ' . GEO::instance()->country( $this->_filter_lock['country_code'] ) . '</span>' . GEO::instance()->flag_from_country( $this->_filter_lock['country_code'] );
					$current_view .= '</span>';
					$current_key  .= 'ip';
					break;
				case 'user_id':
					$user = get_user_by( 'id', $this->_filter_lock['user_id'] );
					$name = ! $user ? __( 'Not Found', 'coreactivity' ) : $user->display_name;

					$current_view = '<span class="coreactivity-view-button"><i class="d4p-icon d4p-ui-user d4p-icon-fw"></i> <span>' . esc_html__( 'User', 'coreactivity' ) . '</span>';
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
			'delete' => __( 'Delete', 'coreactivity' ),
		);
	}

	protected function get_meta_column_keys( $component, $event ) : array {
		return $this->_meta_column[ $component ][ $event ] ?? ( $this->_meta_column[ $component ]['-'] ?? array() );
	}

	protected function single_hidden_row( $item ) {
		list( $columns, $hidden, $sortable, $primary ) = $this->get_column_info();

		$total  = count( $columns ) - count( $hidden );
		$metas  = array( 'referer', 'user_agent', 'ajax_action' );
		$skip   = apply_filters( $this->_filter_key . '_meta_row_skip', $this->get_meta_column_keys( $item->component, $item->event ), $item, $this );
		$skip[] = 'geo_location';

		$left  = array();
		$right = array();

		if ( ! $this->_display_protocol_column && ! empty( $item->protocol ) ) {
			$right[] = '<li><strong>' . esc_html__( 'protocol', 'coreactivity' ) . ':</strong><span>' . esc_html( $item->protocol ) . '</span></li>';
		}

		if ( ! $this->_display_request_column && ! empty( $item->request ) && ! in_array( 'request', $skip ) ) {
			$right[] = '<li><strong>' . esc_html__( 'request', 'coreactivity' ) . ':</strong><span>' . esc_html( $item->request ) . '</span></li>';
		}

		if ( isset( $item->meta ) ) {
			foreach ( $item->meta as $key => $value ) {
				if ( in_array( $key, $skip ) ) {
					continue;
				}

				$meta = $this->meta_value( $key, $value );

				if ( in_array( $key, $metas ) ) {
					$right[] = $meta;
				} else {
					$left[] = $meta;
				}
			}
		}

		if ( empty( $left ) ) {
			$_chunk_size = ceil( count( $right ) / 2 );
			$_chunks     = array_chunk( $right, $_chunk_size );

			$left  = $_chunks[0];
			$right = $_chunks[1] ?? array();
		}

		$description = apply_filters( 'coreactivity_logs_log_item_descriptions', '', $item, $this );

		echo '<td colspan="' . esc_attr( $total ) . '">';

		if ( ! empty( $description ) ) {
			echo '<div>' . $this->kses( $description ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		echo '<div>';

		echo '<ul>' . join( '', $left ) . '</ul>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<ul>' . join( '', $right ) . '</ul>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

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

	protected function column_log_id( $item ) : string {
		$render = $item->log_id;

		$actions = array(
			'popup' => '<a href="#" class="coreactivity-show-view-popup" data-log="' . $item->log_id . '">' . __( 'View', 'coreactivity' ) . '</a>',
		);

		$render  = apply_filters( 'coreactivity_logs_field_render_id', $render, $item, $this );
		$actions = apply_filters( 'coreactivity_logs_field_actions_id', $actions, $item, $this );

		return $render . $this->row_actions( $actions );
	}

	protected function column_blog_id( $item ) : string {
		$blog   = get_blog_details( array( 'blog_id' => $item->blog_id ) );
		$render = $item->blog_id;

		$actions = array(
			'view' => '<a href="' . $this->_view( 'blog_id', 'filter-blog_id=' . $item->blog_id ) . '">' . __( 'Logs', 'coreactivity' ) . '</a>',
			'go'   => '<a target="_blank" href="' . get_admin_url( $item->blog_id, '/admin.php?page=coreactivity-logs' ) . '">' . __( 'Go', 'coreactivity' ) . '</a>',
		);

		if ( $this->_display_blog_column_linked ) {
			if ( $blog instanceof WP_Site ) {
				$render = '<a target="_blank" href="' . $blog->siteurl . '" title="' . $blog->blogname . '">' . $render . '</a>';
			}
		}

		$render  = apply_filters( 'coreactivity_logs_field_render_blog_id', $render, $item, $this );
		$actions = apply_filters( 'coreactivity_logs_field_actions_blog_id', $actions, $item, $this );

		return $render . $this->row_actions( $actions );
	}

	protected function column_component( $item ) : string {
		$render = $this->_display_columns_simplified ? $this->i()->get_component_label( $item->component ) : $item->component;

		$actions = array(
			'view' => '<a href="' . $this->_view( 'component', 'filter-component=' . $item->component ) . '">' . __( 'Logs', 'coreactivity' ) . '</a>',
		);

		$render  = apply_filters( 'coreactivity_logs_field_render_component', $render, $item, $this );
		$actions = apply_filters( 'coreactivity_logs_field_actions_component', $actions, $item, $this );

		return $render . $this->row_actions( $actions );
	}

	protected function column_ip( $item ) : string {
		$render  = '<span>' . $item->ip . '</span>';
		$actions = array(
			'view' => '<a href="' . $this->_view( 'ip', 'filter-ip=' . $item->ip ) . '">' . __( 'IP Logs', 'coreactivity' ) . '</a>',
		);

		if ( ! IP::is_private( $item->ip ) ) {
			$actions['whois'] = '<a href="#" class="coreactivity-show-whois-popup" data-nonce="' . wp_create_nonce( 'coreactivity-whois-' . $item->ip ) . '" data-ip="' . $item->ip . '">' . __( 'Who Is', 'coreactivity' ) . '</a>';
		}

		if ( ! empty( $item->country_code ) ) {
			$actions['view-country'] = '<a href="' . $this->_view( 'country_code', 'filter-country_code=' . $item->country_code ) . '">' . __( 'Country Logs', 'coreactivity' ) . '</a>';
		}

		if ( $this->_display_ip_country_flag ) {
			if ( ! empty( $item->country_code ) ) {
				$render = GEO::instance()->flag_from_country( $item->country_code ) . ' ' . $render;
			} else {
				$render = GEO::instance()->flag( $item->ip ) . ' ' . $render;
			}
		}

		if ( $item->ip == $this->_server_ip ) {
			$render .= '<i class="d4p-icon d4p-ui-database" title="' . esc_attr__( 'Server IP', 'coreactivity' ) . '"></i>';
		}

		if ( $item->ip == $this->_current_ip ) {
			$render .= '<i class="d4p-icon d4p-ui-user-square" title="' . esc_attr__( 'Current Request IP', 'coreactivity' ) . '"></i>';
		}

		$render  = apply_filters( 'coreactivity_logs_field_render_ip', $render, $item, $this );
		$actions = apply_filters( 'coreactivity_logs_field_actions_ip', $actions, $item, $this );

		return '<div class="coreactivity-field-wrapper">' . $render . '</div>' . $this->row_actions( $actions );
	}

	protected function column_user_id( $item ) : string {
		$render  = '';
		$actions = array(
			'view' => '<a href="' . $this->_view( 'user_id', 'filter-user_id=' . $item->user_id ) . '">' . esc_html__( 'Logs', 'coreactivity' ) . '</a>',
		);

		if ( $item->user_id == 0 ) {
			if ( $this->_display_user_avatar ) {
				$render .= '<i class="d4p-icon d4p-ui-user-square"></i>';
			}

			$render .= '<span>ID: <strong>0</strong> &middot; ' . esc_html__( 'Not a User', 'coreactivity' ) . '</span>';
		} else {
			if ( $this->_display_user_avatar ) {
				$render .= get_avatar( $item->user_id, 20 );
			}

			$user = get_user_by( 'id', $item->user_id );

			$render .= '<span>ID: <strong>' . $item->user_id . '</strong> &middot; ';

			if ( ! $user ) {
				$render .= esc_html__( 'Not Found', 'coreactivity' );
			} else {
				$render .= $user->display_name;

				$actions['edit'] = '<a href="user-edit.php?user_id=' . $item->user_id . '">' . esc_html__( 'Edit', 'coreactivity' ) . '</a>';
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
			'view' => '<a href="' . $this->_view( 'event_id', 'filter-event_id=' . $item->event_id ) . '">' . __( 'Logs', 'coreactivity' ) . '</a>',
		);

		$render  = apply_filters( 'coreactivity_logs_field_render_event_id', $render, $item, $this );
		$actions = apply_filters( 'coreactivity_logs_field_actions_event_id', $actions, $item, $this );

		return $render . $this->row_actions( $actions );
	}

	protected function column_object_type( $item ) : string {
		$render = ! empty( $item->object_type ) ? $this->i()->get_object_type_label( $item->object_type ) : '/';

		$actions = array(
			'view' => '<a href="' . $this->_view( 'object_type', 'filter-object_type=' . $item->object_type ) . '">' . __( 'Logs', 'coreactivity' ) . '</a>',
		);

		if ( empty( $item->object_type ) ) {
			unset( $actions['view'] );
		}

		$render  = apply_filters( 'coreactivity_logs_field_render_object_type', $render, $item, $this );
		$actions = apply_filters( 'coreactivity_logs_field_actions_object_type', $actions, $item, $this );

		return $this->kses( $render ) . $this->row_actions( $actions );
	}

	protected function column_object_name( $item ) : string {
		$render    = $item->object_name ?? '';
		$exception = false;

		$actions = array();

		if ( ! $this->_display_object_type_column && ! empty( $item->object_type ) ) {
			$actions['view-type'] = '<a href="' . $this->_view( 'object_type', 'filter-object_type=' . $item->object_type ) . '">' . __( 'Type Logs', 'coreactivity' ) . '</a>';
		}

		if ( ! empty( $item->object_name ) || $item->object_id != 0 ) {
			$_args = 'filter-object_type=' . $item->object_type;

			if ( ! empty( $item->object_name ) ) {
				$_args .= '&filter-object_name=' . $item->object_name;
			} else {
				$_args .= '&filter-object_id=' . $item->object_id;
			}

			$actions['view'] = '<a href="' . $this->_view( 'object', $_args ) . '">' . __( 'Object Logs', 'coreactivity' ) . '</a>';
		}

		if ( in_array( $item->object_type, array( 'plugin', 'theme', 'option', 'cron', 'sitemeta', 'notification', 'post-meta', 'user-meta', 'term-meta', 'comment-meta' ) ) ) {
			if ( ! coreactivity_settings()->is_in_exception_list( $item->object_type, $item->object_name ) ) {
				$actions['exclude'] = '<a href="' . $this->_self( 'single-action=do-not-log&object-type=' . urlencode( $item->object_type ) . '&object-name=' . urlencode( $item->object_name ), true, wp_create_nonce( 'coreactivity-do-not-log-' . $item->object_name ) ) . '">' . __( 'Do Not Log', 'coreactivity' ) . '</a>';
			} else {
				$exception = true;
			}
		}

		$render  = apply_filters( 'coreactivity_logs_field_render_object_name', $render, $item, $this );
		$actions = apply_filters( 'coreactivity_logs_field_actions_object_name', $actions, $item, $this );

		if ( $exception ) {
			$render = '<i title="' . __( 'on exception list', 'coreactivity' ) . '" class="d4p-icon d4p-ui-cancel"></i>' . $render;
		}

		if ( ! $this->_display_object_type_column && ! empty( $item->object_type ) ) {
			$render = '<div class="coreactivity-object-type">' . $this->i()->get_object_type_label( $item->object_type ) . '</div>' . $render;
		}

		if ( $render == '/' ) {
			unset( $actions['view'] );
		}

		return $this->kses( $render ) . $this->row_actions( $actions );
	}

	protected function column_context( $item ) : string {
		$render = ! empty( $item->context ) ? strtoupper( $item->context ) : '/';

		$actions = array(
			'view' => '<a href="' . $this->_view( 'context', 'filter-context=' . $item->context ) . '">' . __( 'Logs', 'coreactivity' ) . '</a>',
		);

		$render  = apply_filters( 'coreactivity_logs_field_render_context', $render, $item, $this );
		$actions = apply_filters( 'coreactivity_logs_field_actions_context', $actions, $item, $this );

		return $render . $this->row_actions( $actions );
	}

	protected function column_method( $item ) : string {
		$render = $item->method;

		$actions = array(
			'view' => '<a href="' . $this->_view( 'method', 'filter-method=' . $item->method ) . '">' . __( 'Logs', 'coreactivity' ) . '</a>',
		);

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

	protected function column_detection( $item ) : string {
		$actions = array();
		$items   = array();

		if ( isset( $item->device['bot'] ) ) {
			$items[] = '<strong>' . __( 'Bot', 'coreactivity' ) . '</strong>: ' . $item->device['bot']['name'];

			if ( ! empty( $item->device['bot']['category'] ) ) {
				$items[] = ucwords( $item->device['bot']['category'] );
			}
		} else if ( ! empty( $item->device ) ) {
			$os = trim( ( $item->device['os']['name'] ?? '' ) . ' ' . ( $item->device['os']['version'] ?? '' ) );

			$items[] = '<strong>' . ucwords( $item->device['device'] ?? __( 'Unknown', 'coreactivity' ) ) . '</strong>' . ( ! empty( $os ) ? ': ' . $os : '' );
			$items[] = trim( ( $item->device['client']['name'] ?? '' ) . ' ' . ( $item->device['client']['version'] ?? '' ) );

			if ( ! empty( $item->device['brand'] ) ) {
				$items[] = trim( $item->device['brand'] . ' ' . ( $item->device['model'] ?? '' ) );
			}
		}

		$items = array_filter( $items );

		$render = empty( $items ) ? '/' : '<ul><li>' . join( '</li><li>', $items ) . '</li></ul>';

		$render  = apply_filters( 'coreactivity_logs_field_render_detection', $render, $item, $this );
		$actions = apply_filters( 'coreactivity_logs_field_actions_detection', $actions, $item, $this );

		return $render . $this->row_actions( $actions );
	}

	protected function column_meta( $item ) : string {
		return '<button type="button" aria-label="' . esc_attr__( 'Show Log Meta Data', 'coreactivity' ) . '"><i class="d4p-icon d4p-ui-chevron-square-down d4p-icon-lg"></i></button>';
	}

	protected function column_meta_data( $item ) : string {
		$show = apply_filters( $this->_filter_key . '_meta_column_show', $this->get_meta_column_keys( $item->component, $item->event ), $item, $this );

		$actions = array();
		$items   = array();

		if ( in_array( 'request', $show ) && ! $this->_display_request_column ) {
			$items[] = $this->meta_value( '', $item->request );
		}

		if ( isset( $item->meta ) ) {
			foreach ( $item->meta as $key => $value ) {
				if ( in_array( $key, $show ) ) {
					$items[] = $this->meta_value( $key, $value );
				}
			}
		}

		$render = empty( $items ) ? '/' : '<ul>' . join( '', $items ) . '</ul>';

		$render  = apply_filters( 'coreactivity_logs_field_render_meta', $render, $item, $this );
		$actions = apply_filters( 'coreactivity_logs_field_actions_meta', $actions, $item, $this );

		return $render . $this->row_actions( $actions );
	}

	protected function locked_components() : array {
		$component = array();

		if ( ! empty( $this->_limit_lock['components'] ) ) {
			if ( isset( $this->_limit_lock['components'][0]['values'] ) ) {
				foreach ( $this->_limit_lock['components'] as $group ) {
					$component = array_merge( $component, array_keys( $group['values'] ) );
				}
			} else {
				$component = array_keys( $this->_limit_lock['components'] );
			}
		}

		return $component;
	}

	protected function kses( $render ) : string {
		$allowed_tags = array(
			'a'      => array(
				'href' => true,
			),
			'i'      => array(
				'title' => true,
				'class' => true,
			),
			'br'     => array(),
			'strong' => array(),
			'div'    => array(
				'class' => true,
			),
		);

		return wp_kses( $render, $allowed_tags );
	}

	protected function meta_value( $key, $value ) : string {
		$value = is_scalar( $value ) ? esc_html( $value ) : ( is_array( $value ) && count( $value ) < 20 ? coreactivity_print_array( $value ) : esc_html( wp_json_encode( $value ) ) );

		if ( empty( $key ) ) {
			return '<li><span>' . $value . '</span></li>';
		}

		return '<li><strong>' . esc_html( $key ) . ':</strong><span>' . $value . '</span></li>';
	}
}
