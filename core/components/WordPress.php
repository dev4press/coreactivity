<?php

namespace Dev4Press\Plugin\CoreActivity\Components;

use Dev4Press\Plugin\CoreActivity\Base\Component;
use Dev4Press\v47\Core\Helpers\Source;
use Dev4Press\v47\WordPress as LibWordPress;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WordPress extends Component {
	protected $plugin = 'coreactivity';
	protected $name = 'wordpress';
	protected $icon = 'brand-wordpress';
	protected $wp_version = '';
	protected $plugin_name = '';
	protected $do_not_log = array(
		'coreactivity_instant_notification',
	);
	protected $exceptions = array();

	public function init() {
		$this->exceptions = coreactivity_settings()->get( 'exceptions_cron_list' );
	}

	public function tracking() {
		if ( $this->is_active( 'cron-schedule' ) ) {
			add_filter( 'schedule_event', array( $this, 'event_schedule_event' ), 10000 );
		}

		if ( $this->is_active( 'content-export' ) ) {
			add_filter( 'export_wp', array( $this, 'event_content_export' ) );
		}

		if ( $this->is_active( 'database-delta' ) ) {
			add_filter( 'd4p_install_db_delta', array( $this, 'prepare_db_delta' ) );
			add_filter( 'dbdelta_queries', array( $this, 'event_db_delta' ) );
		}

		if ( $this->is_active( 'update-core' ) || $this->is_active( 'update-core-auto' ) ) {
			add_filter( 'update_feedback', array( $this, 'prepare_update' ) );
			add_action( '_core_updated_successfully', array( $this, 'event_update' ) );
		}

		if ( $this->is_active( 'available-update-core' ) ) {
			add_action( 'set_site_transient_update_core', array( $this, 'event_update_core' ) );
		}

		if ( $this->is_active( 'available-update-plugin' ) ) {
			add_action( 'set_site_transient_update_plugins', array( $this, 'event_update_plugins' ) );
		}

		if ( $this->is_active( 'available-update-theme' ) ) {
			add_action( 'set_site_transient_update_themes', array( $this, 'event_update_themes' ) );
		}
	}

	public function label() : string {
		return __( 'WordPress', 'coreactivity' );
	}

	protected function get_events() : array {
		return array(
			'update-core'             => array(
				'label' => __( 'WordPress Core Update', 'coreactivity' ),
				'scope' => 'network',
			),
			'update-core-auto'        => array(
				'label' => __( 'WordPress Core Auto Update', 'coreactivity' ),
				'scope' => 'network',
			),
			'available-update-core'   => array(
				'label'   => __( 'Available WordPress Update', 'coreactivity' ),
				'scope'   => 'network',
				'version' => '1.8',
			),
			'available-update-plugin' => array(
				'label'   => __( 'Available Plugin Updates', 'coreactivity' ),
				'scope'   => 'network',
				'version' => '1.8',
			),
			'available-update-theme'  => array(
				'label'   => __( 'Available Theme Updates', 'coreactivity' ),
				'scope'   => 'network',
				'version' => '1.8',
			),
			'cron-schedule'           => array(
				'label' => __( 'CRON Event Scheduled', 'coreactivity' ),
				'scope' => 'blog',
			),
			'content-export'          => array(
				'label' => __( 'Content Export', 'coreactivity' ),
				'scope' => 'blog',
			),
			'database-delta'          => array(
				'label' => __( 'Database Delta Queries', 'coreactivity' ),
				'scope' => 'both',
			),
		);
	}

	public function prepare_update( $message ) {
		if ( empty( $this->wp_version ) ) {
			$this->wp_version = LibWordPress::instance()->version();
		}

		return $message;
	}

	public function prepare_db_delta( $plugin ) {
		$this->plugin_name = $plugin;
	}

	public function event_schedule_event( $event ) {
		if ( $event ) {
			if ( $this->is_exception( $event->hook ) ) {
				return $event;
			}

			$caller = wp_debug_backtrace_summary( null, 4, false );

			if ( ! in_array( 'wp_reschedule_event', $caller ) && ! in_array( $event->hook, $this->do_not_log ) ) {
				$this->log( 'cron-schedule', array(
					'object_type' => 'cron',
					'object_name' => $event->hook,
				), array(
					'timestamp' => $event->timestamp,
					'schedule'  => $event->schedule,
					'source'    => $this->caller( array( 'wp_schedule_single_event', 'wp_schedule_event' ) ),
				) );
			}
		}

		return $event;
	}

	public function event_content_export( $args ) {
		$this->log( 'content-export', array(), array(
			'export_args' => $args,
		) );
	}

	public function event_update( $wp_version ) {
		$event = isset( $GLOBALS['pagenow'] ) && $GLOBALS['pagenow'] == 'update-core.php' ? 'update-core' : 'update-core-auto';

		if ( $this->is_active( $event ) ) {
			$this->log( $event, array(), array(
				'from' => $this->wp_version,
				'to'   => $wp_version,
			) );
		}
	}

	public function event_db_delta( $queries ) {
		$result = $this->caller( 'dbDelta' );

		if ( ! empty( $result ) && ! empty( $queries ) ) {
			$source = ! empty( $this->plugin_name ) ? array( 'plugin' => $this->plugin_name ) : $result;

			$this->log( 'database-delta', array(),
				array(
					'source' => $source,
				) );
		}

		return $queries;
	}

	public function event_update_core( $updates ) {
		if ( ! empty( $updates->updates[0]->current ) ) {
			global $wp_version;

			$new_version     = $updates->updates[0]->current ?? '';
			$current_version = $updates->version_checked ?? $wp_version;

			if ( $new_version != $current_version ) {
				$this->log( 'available-update-core', array(), array(
					'new_version'  => $new_version,
					'core_version' => $current_version,
				) );
			}
		}
	}

	public function event_update_plugins( $updates ) {
		if ( ! empty( $updates->response ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';

			foreach ( $updates->response as $plugin => $data ) {
				$file = WP_PLUGIN_DIR . '/' . $plugin;

				if ( file_exists( $file ) && is_readable( $file ) ) {
					$plugin_file = get_plugin_data( $file, true, false );

					$new_version     = $data->new_version ?? '';
					$current_version = $plugin_file['Version'] ?? '';

					if ( $new_version != $current_version && ! empty( $new_version ) ) {
						$this->log( 'available-update-plugin', array(
							'object_name' => $plugin,
							'object_type' => 'plugin',
						), array(
							'plugin_name'    => wp_strip_all_tags( $plugin_file['Name'] ?? '' ),
							'plugin_title'   => wp_strip_all_tags( $plugin_file['Title'] ?? '' ),
							'plugin_version' => $current_version,
							'new_version'    => $new_version,
						) );
					}
				}
			}
		}
	}

	public function event_update_themes( $updates ) {
		if ( ! empty( $updates->response ) ) {
			foreach ( $updates->response as $theme => $data ) {
				$theme_data = wp_get_theme( $theme );

				$new_version     = $data['new_version'] ?? ( $data->new_version ?? '' );
				$current_version = $theme_data['Version'] ?? '';

				if ( $new_version != $current_version && ! empty( $new_version ) ) {
					$this->log( 'available-update-theme', array(
						'object_name' => $theme,
						'object_type' => 'theme',
					), array(
						'theme_name'    => wp_strip_all_tags( $theme_data['Name'] ?? '' ),
						'theme_version' => $current_version,
						'new_version'   => $new_version,
					) );
				}
			}
		}
	}

	private function caller( $functions ) : array {
		$backtrace = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS );
		$functions = (array) $functions;
		$file_path = '';
		$file_line = '';

		foreach ( $backtrace as $item ) {
			if ( isset( $item['file'] ) && isset( $item['line'] ) && isset( $item['function'] ) ) {
				if ( in_array( $item['function'], $functions ) ) {
					$file_path = $item['file'];
					$file_line = $item['line'];
					break;
				}
			}
		}

		if ( ! empty( $file_path ) ) {
			$result         = Source::instance()->origin( $file_path );
			$result['line'] = $file_line;

			return $result;
		}

		return array();
	}

	private function is_exception( $option ) : bool {
		return ! empty( $this->exceptions ) && in_array( $option, $this->exceptions );
	}
}
