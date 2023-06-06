<?php

namespace Dev4Press\Plugin\CoreActivity\Components;

use Dev4Press\Plugin\CoreActivity\Base\Component;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Theme extends Component {
	protected $plugin = 'coreactivity';
	protected $name = 'theme';
	protected $object_type = 'theme';
	protected $scope = 'both';

	protected $storage = array();

	public function tracking() {
		if ( $this->is_active( 'deleted' ) ) {
			add_action( 'delete_theme', array( $this, 'init_delete' ) );
			add_action( 'deleted_theme', array( $this, 'event_deleted' ), 10, 2 );
		}
	}

	public function label() : string {
		return __( "Plugins", "coreactivity" );
	}

	protected function get_events() : array {
		return array(
			'deleted' => array( 'label' => __( "Theme Deleted", "coreactivity" ) )
		);
	}

	public function init_delete( $stylesheet ) {
		$this->storage[ $stylesheet ] = $this->_get_theme( $stylesheet );
	}

	public function event_deleted( $stylesheet, $deleted ) {
		if ( $deleted ) {
			if ( isset( $this->storage[ $stylesheet ] ) ) {
				$this->log( 'deleted', array( 'object_name' => $stylesheet ), $this->_theme_meta( $stylesheet ) );
			}
		}
	}

	private function _get_theme( $stylesheet ) : array {
		$theme = wp_get_theme( $stylesheet );

		return array(
			'theme_name'        => $theme->get( 'Name' ),
			'theme_version'     => $theme->get( 'Version' ),
			'theme_author'      => $theme->get( 'Author' ),
			'theme_description' => $theme->get( 'Description' ),
			'theme_url'         => $theme->get( 'ThemeURI' )
		);
	}

	private function _theme_meta( $stylesheet ) : array {
		return $this->storage[ $stylesheet ];
	}
}