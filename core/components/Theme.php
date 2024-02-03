<?php

namespace Dev4Press\Plugin\CoreActivity\Components;

use Dev4Press\Plugin\CoreActivity\Base\Component;
use WP_Theme;

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
	protected $exceptions = array();

	public function init() {
		$this->exceptions = coreactivity_settings()->get( 'exceptions_theme_list' );
	}

	public function tracking() {
		if ( $this->is_active( 'switched' ) ) {
			add_action( 'switch_theme', array( $this, 'event_switched' ), 10, 3 );
		}

		if ( $this->is_active( 'deleted' ) ) {
			add_action( 'delete_theme', array( $this, 'prepare_stylesheet' ) );
			add_action( 'deleted_theme', array( $this, 'event_deleted' ), 10, 2 );
		}
	}

	public function label() : string {
		return __( 'Themes', 'coreactivity' );
	}

	protected function get_events() : array {
		return array(
			'installed' => array(
				'label'   => __( 'Theme Installed', 'coreactivity' ),
				'version' => '2.0',
			),
			'updated'   => array(
				'label'   => __( 'Theme Updated', 'coreactivity' ),
				'version' => '2.0',
			),
			'deleted'   => array(
				'label' => __( 'Theme Deleted', 'coreactivity' ),
			),
			'switched'  => array(
				'label' => __( 'Theme Switched', 'coreactivity' ),
			),
		);
	}

	public function logs_meta_column_keys( array $meta_column_keys ) : array {
		$meta_column_keys[ $this->code() ] = array(
			'deleted'  => array(
				'theme_name',
				'theme_version',
			),
			'switched' => array(
				'theme_name',
				'old_theme_name',
			),
		);

		return $meta_column_keys;
	}

	public function prepare_stylesheet( $stylesheet ) {
		$this->storage[ $stylesheet ] = $this->_get_theme( $stylesheet );
	}

	public function event_deleted( $stylesheet, $deleted ) {
		if ( $this->is_exception( $stylesheet ) ) {
			return;
		}

		if ( $deleted ) {
			if ( isset( $this->storage[ $stylesheet ] ) ) {
				$this->log( 'deleted', array( 'object_name' => $stylesheet ), $this->_theme_meta( $stylesheet ) );
			}
		}
	}

	public function event_switched( $new_name, $new_theme, $old_theme ) {
		if ( $this->is_exception( $new_theme->get_stylesheet() ) ) {
			return;
		}

		$this->prepare_stylesheet( $new_theme->get_stylesheet() );
		$this->prepare_stylesheet( $old_theme->get_stylesheet() );

		$this->log( 'switched', array(
			'object_name' => $new_theme->get_stylesheet(),
		), array_merge(
			array( 'old_stylesheet' => $old_theme->get_stylesheet() ),
			$this->_get_theme( $old_theme->get_stylesheet(), 'old_' ),
			$this->_get_theme( $new_theme->get_stylesheet() )
		) );
	}

	private function _get_theme( $stylesheet, $prefix = '' ) : array {
		$theme = wp_get_theme( $stylesheet );

		$meta = array(
			$prefix . 'theme_name'        => wp_strip_all_tags( $theme->get( 'Name' ) ),
			$prefix . 'theme_author'      => wp_strip_all_tags( $theme->get( 'Author' ) ),
			$prefix . 'theme_description' => $theme->get( 'Description' ),
			$prefix . 'theme_version'     => $theme->get( 'Version' ),
			$prefix . 'theme_url'         => $theme->get( 'ThemeURI' ),
		);

		if ( ! coreactivity_settings()->get( 'log_if_available_description' ) ) {
			unset( $meta[ $prefix . 'theme_description' ] );
		}

		return $meta;
	}

	private function _theme_meta( $stylesheet ) : array {
		return $this->storage[ $stylesheet ];
	}

	private function is_exception( $option ) : bool {
		return ! empty( $this->exceptions ) && in_array( $option, $this->exceptions );
	}
}
