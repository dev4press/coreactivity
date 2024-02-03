<?php

namespace Dev4Press\Plugin\CoreActivity\Log;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Upgrader {
	public function __construct() {
		add_filter( 'upgrader_pre_install', array( $this, 'save_pre_install_versions' ), 10, 2 );
		add_action( 'upgrader_process_complete', array( $this, 'upgrader_process_complete' ), 10, 2 );
	}

	public static function instance() : Upgrader {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new Upgrader();
		}

		return $instance;
	}

	public function save_pre_install_versions( $value ) {
		$plugins = get_site_transient( 'update_plugins' );
		$themes  = get_site_transient( 'update_themes' );

		$backup = array(
			'themes'          => wp_get_themes(),
			'plugins'         => get_plugins(),
			'themes_updates'  => $themes->response ?? array(),
			'plugins_updates' => $plugins->response ?? array(),
		);

		update_site_option( 'coresecurity_temp_plugins_themes', $backup, false );

		return $value;
	}

	public function upgrader_process_complete( $obj, $data ) {
		if ( isset( $data['type'] ) && isset( $data['action'] ) ) {
			$_type   = $data['type'];
			$_action = $data['action'];

			if ( 'plugin' == $_type && 'update' == $_action ) {
				$plugins = isset( $data['plugins'] ) ? (array) $data['plugins'] : array();

				foreach ( $plugins as $plugin_code ) {
					$plugin   = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin_code, true, false );
					$previous = $this->get_plugin_previous_version( $plugin_code );
					$package  = $this->get_plugin_package_url( $plugin_code );

					do_action( 'coreactivity_upgrader_plugin_update', $plugin_code, $plugin, $previous, $package );
				}
			} else if ( 'plugin' == $_type && 'install' == $_action ) {

			}
		}

		delete_site_option( 'coresecurity_temp_plugins_themes' );
	}

	protected function get_plugin_package_url( $plugin_code ) {
		$data   = get_site_option( 'coresecurity_temp_plugins_themes', array() );
		$plugin = $data['plugins_updates'][ $plugin_code ] ?? array();

		return empty( $plugin ) ? '' : ( $plugin->package ?? '' );
	}

	protected function get_plugin_previous_version( $plugin_code ) {
		$data   = get_site_option( 'coresecurity_temp_plugins_themes', array() );
		$plugin = $data['plugins'][ $plugin_code ] ?? array();

		return $plugin['Version'] ?? '';
	}
}
