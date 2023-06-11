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
	protected $icon = 'ui-palette';
	protected $scope = 'both';

	protected $storage = array();

	public function tracking() {
		if ( $this->is_active( 'deleted' ) ) {
			add_action( 'delete_theme', array( $this, 'init_delete' ) );
			add_action( 'deleted_theme', array( $this, 'event_deleted' ), 10, 2 );
		}
	}

	public function label() : string {
		return __( "Themes", "coreactivity" );
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

		$meta = array(
			'theme_name'        => strip_tags( $theme->get( 'Name' ) ),
			'theme_author'      => strip_tags( $theme->get( 'Author' ) ),
			'theme_description' => $theme->get( 'Description' ),
			'theme_version'     => $theme->get( 'Version' ),
			'theme_url'         => $theme->get( 'ThemeURI' )
		);

		if ( ! coreactivity_settings()->get( 'log_if_available_description' ) ) {
			unset( $meta[ 'theme_description' ] );
		}

		return $meta;
	}

	private function _theme_meta( $stylesheet ) : array {
		return $this->storage[ $stylesheet ];
	}
}
