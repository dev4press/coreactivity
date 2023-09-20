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
	}

	public function label() : string {
		return __( 'Plugins', 'coreactivity' );
	}

	protected function get_events() : array {
		return array(
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

	public function init_delete( $plugin_file ) {
		$this->storage[ $plugin_file ] = $this->_get_plugin( $plugin_file );
	}

	public function event_deleted( $plugin_file, $deleted ) {
		if ( $deleted ) {
			if ( isset( $this->storage[ $plugin_file ] ) ) {
				$this->log( 'deleted', array( 'object_name' => $plugin_file ), $this->_plugin_meta( $plugin_file ) );
			}
		}
	}

	public function event_activated( $plugin_file, $network_wide = false ) {
		$this->storage[ $plugin_file ] = $this->_get_plugin( $plugin_file );

		$event = $network_wide ? 'network-activated' : 'activated';

		if ( $this->is_active( $event ) ) {
			$this->log( $event, array( 'object_name' => $plugin_file ), $this->_plugin_meta( $plugin_file ) );
		}
	}

	public function event_deactivated( $plugin_file, $network_wide = false ) {
		$this->storage[ $plugin_file ] = $this->_get_plugin( $plugin_file );

		$event = $network_wide ? 'network-deactivated' : 'deactivated';

		if ( $this->is_active( $event ) ) {
			$this->log( $event, array( 'object_name' => $plugin_file ), $this->_plugin_meta( $plugin_file ) );
		}
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
}
