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
	protected $scope = 'both';

	protected $storage = array();

	public function tracking() {
		add_action( 'delete_plugin', array( $this, 'delete_plugin' ) );
		add_action( 'deleted_plugin', array( $this, 'deleted_plugin' ), 10, 2 );
	}

	public function label() : string {
		return __( "Plugins", "coreactivity" );
	}

	protected function get_events() : array {
		return array(
			'deleted' => array( 'label' => __( "Plugin Deleted", "coreactivity" ) )
		);
	}

	public function delete_plugin( $plugin_file ) {
		$this->storage[ $plugin_file ] = $this->_get_plugin( $plugin_file );
	}

	public function deleted_plugin( $plugin_file, $deleted ) {
		if ( $deleted ) {
			if ( isset( $this->storage[ $plugin_file ] ) ) {
				$this->log( 'deleted', array( 'object_name' => $plugin_file ), $this->_plugin_meta( $plugin_file ) );
			}
		}
	}

	private function _get_plugin( $plugin_file ) : array {
		return get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin_file, true, false );
	}

	private function _plugin_meta( $plugin_file ) : array {
		return array(
			'plugin_name'        => strip_tags( $this->storage[ $plugin_file ][ 'Name' ] ?? '' ),
			'plugin_title'       => strip_tags( $this->storage[ $plugin_file ][ 'Title' ] ?? '' ),
			'plugin_author'      => strip_tags( $this->storage[ $plugin_file ][ 'Author' ] ?? '' ),
			'plugin_description' => strip_tags( $this->storage[ $plugin_file ][ 'Description' ] ?? '' ),
			'plugin_version'     => $this->storage[ $plugin_file ][ 'Version' ] ?? '',
			'plugin_url'         => $this->storage[ $plugin_file ][ 'PluginURI' ] ?? ''
		);
	}
}
