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
		add_action( 'delete_theme', array( $this, 'delete_theme' ) );
		add_action( 'deleted_theme', array( $this, 'deleted_theme' ), 10, 2 );
	}

	public function label() : string {
		return __( "Plugins", "coreactivity" );
	}

	protected function get_events() : array {
		return array(
			'deleted' => array( 'label' => __( "Theme Deleted", "coreactivity" ) )
		);
	}

	public function delete_theme( $stylesheet ) {
		$this->storage[ $stylesheet ] = $this->_get_theme( $stylesheet );
	}

	public function deleted_theme( $stylesheet, $deleted ) {
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
