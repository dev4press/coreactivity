<?php

namespace Dev4Press\Plugin\CoreActivity\Components;

use Dev4Press\Plugin\CoreActivity\Base\Component;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Plugin extends Component {
	protected $plugin = 'coreactivity';
	protected $name = 'plugin';
	protected $object_type = 'plugin';
	protected $icon = 'ui-plug';
	protected $scope = 'both';

	protected $storage = array();
	protected $exceptions = array();

	public function init() {
		$this->exceptions = coreactivity_settings()->get( 'exceptions_plugin_list' );
	}

	public function tracking() {
		if ( $this->is_active( 'deleted' ) ) {
			add_action( 'delete_plugin', array( $this, 'init_delete' ) );
			add_action( 'deleted_plugin', array( $this, 'event_deleted' ), 10, 2 );
		}

		if ( $this->is_active( 'activated' ) || $this->is_active( 'network-activated' ) ) {
			add_action( 'activated_plugin', array( $this, 'event_activated' ), 1000, 2 );
		}

		if ( $this->is_active( 'deactivated' ) || $this->is_active( 'network-deactivated' ) ) {
			add_action( 'deactivated_plugin', array( $this, 'event_deactivated' ), 1000, 2 );
		}

		if ( $this->is_active( 'installed' ) ) {
			add_action( 'coreactivity_upgrader_plugin_install', array( $this, 'event_installed' ), 10, 2 );
		}

		if ( $this->is_active( 'updated' ) ) {
			add_action( 'coreactivity_upgrader_plugin_update', array( $this, 'event_updated' ), 10, 4 );
		}

		if ( $this->is_active( 'install-error' ) ) {
			add_action( 'coreactivity_upgrader_plugin_install_error', array( $this, 'event_install_error' ), 10, 3 );
		}

		if ( $this->is_active( 'update-error' ) ) {
			add_action( 'coreactivity_upgrader_plugin_update_error', array( $this, 'event_update_error' ), 10, 5 );
		}
	}

	public function label() : string {
		return __( 'Plugins', 'coreactivity' );
	}

	protected function get_events() : array {
		return array(
			'installed'           => array(
				'label'   => __( 'Plugin Installed', 'coreactivity' ),
				'version' => '2.0',
			),
			'updated'             => array(
				'label'   => __( 'Plugin Updated', 'coreactivity' ),
				'version' => '2.0',
			),
			'install-error'       => array(
				'label'   => __( 'Plugin Install Error', 'coreactivity' ),
				'version' => '2.0',
			),
			'update-error'        => array(
				'label'   => __( 'Plugin Updated Error', 'coreactivity' ),
				'version' => '2.0',
			),
			'deleted'             => array(
				'label' => __( 'Plugin Deleted', 'coreactivity' ),
			),
			'activated'           => array(
				'label' => __( 'Plugin Activated', 'coreactivity' ),
				'scope' => 'blog',
			),
			'network-activated'   => array(
				'label' => __( 'Plugin Network Activated', 'coreactivity' ),
				'scope' => 'network',
			),
			'deactivated'         => array(
				'label' => __( 'Plugin Deactivated', 'coreactivity' ),
				'scope' => 'blog',
			),
			'network-deactivated' => array(
				'label' => __( 'Plugin Network Deactivated', 'coreactivity' ),
				'scope' => 'network',
			),
		);
	}

	public function logs_meta_column_keys( array $meta_column_keys ) : array {
		$meta_column_keys[ $this->code() ] = array(
			'-' => array(
				'plugin_version',
				'plugin_author',
			),
		);

		return $meta_column_keys;
	}

	public function init_delete( $plugin_file ) {
		$this->storage[ $plugin_file ] = $this->_get_plugin( $plugin_file );
	}

	public function event_deleted( $plugin_file, $deleted ) {
		if ( $this->is_exception( $plugin_file ) ) {
			return;
		}

		if ( $deleted ) {
			if ( isset( $this->storage[ $plugin_file ] ) ) {
				$this->log( 'deleted', array(
					'object_name' => $plugin_file,
				), $this->_plugin_meta( $plugin_file ) );
			}
		}
	}

	public function event_activated( $plugin_file, $network_wide = false ) {
		if ( $this->is_exception( $plugin_file ) ) {
			return;
		}

		$this->storage[ $plugin_file ] = $this->_get_plugin( $plugin_file );

		$event = $network_wide ? 'network-activated' : 'activated';

		if ( $this->is_active( $event ) ) {
			$this->log( $event, array(
				'object_name' => $plugin_file,
			), $this->_plugin_meta( $plugin_file ) );
		}
	}

	public function event_deactivated( $plugin_file, $network_wide = false ) {
		if ( $this->is_exception( $plugin_file ) ) {
			return;
		}

		$this->storage[ $plugin_file ] = $this->_get_plugin( $plugin_file );

		$event = $network_wide ? 'network-deactivated' : 'deactivated';

		if ( $this->is_active( $event ) ) {
			$this->log( $event, array( 'object_name' => $plugin_file ), $this->_plugin_meta( $plugin_file ) );
		}
	}

	public function event_installed( $plugin_code, $plugin ) {
		$this->storage[ $plugin_code ] = $plugin;

		$this->log( 'installed', array(
			'object_name' => $plugin_code,
		), $this->_plugin_meta( $plugin_code ) );
	}

	public function event_updated( $plugin_code, $plugin, $previous, $package ) {
		$this->storage[ $plugin_code ] = $plugin;

		$meta = $this->_plugin_meta( $plugin_code );

		$meta['plugin_previous'] = $previous;
		$meta['plugin_package']  = $package;

		$this->log( 'updated', array(
			'object_name' => $plugin_code,
		), $meta );
	}

	public function event_install_error( $plugin_code, $plugin, $error ) {
		$this->storage[ $plugin_code ] = $plugin;

		$meta          = $this->_plugin_meta( $plugin_code );
		$meta['error'] = $error->get_error_message();

		$this->log( 'install-error', array(
			'object_name' => $plugin_code,
		), $meta );
	}

	public function event_update_error( $plugin_code, $plugin, $previous, $package, $error ) {
		$this->storage[ $plugin_code ] = $plugin;

		$meta = $this->_plugin_meta( $plugin_code );

		$meta['plugin_previous'] = $previous;
		$meta['plugin_package']  = $package;
		$meta['error']           = $error->get_error_message();

		$this->log( 'update-error', array(
			'object_name' => $plugin_code,
		), $meta );
	}

	private function _get_plugin( $plugin_file ) : array {
		return get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin_file, true, false );
	}

	private function _plugin_meta( $plugin_file ) : array {
		$meta = array(
			'plugin_name'        => wp_strip_all_tags( $this->storage[ $plugin_file ]['Name'] ?? '' ),
			'plugin_title'       => wp_strip_all_tags( $this->storage[ $plugin_file ]['Title'] ?? '' ),
			'plugin_author'      => wp_strip_all_tags( $this->storage[ $plugin_file ]['Author'] ?? '' ),
			'plugin_description' => wp_strip_all_tags( $this->storage[ $plugin_file ]['Description'] ?? '' ),
			'plugin_version'     => $this->storage[ $plugin_file ]['Version'] ?? '',
			'plugin_url'         => $this->storage[ $plugin_file ]['PluginURI'] ?? '',
		);

		if ( $meta['plugin_name'] == $meta['plugin_title'] ) {
			unset( $meta['plugin_name'] );
		}

		if ( ! coreactivity_settings()->get( 'log_if_available_description' ) ) {
			unset( $meta['plugin_description'] );
		}

		return $meta;
	}

	private function is_exception( $option ) : bool {
		return ! empty( $this->exceptions ) && in_array( $option, $this->exceptions );
	}
}
