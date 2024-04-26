<?php

namespace Dev4Press\Plugin\CoreActivity\Components;

use Dev4Press\Plugin\CoreActivity\Base\Component;
use Dev4Press\Plugin\CoreActivity\Log\Core;
use Dev4Press\v47\Core\Helpers\IP;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Error extends Component {
	protected $plugin = 'coreactivity';
	protected $name = 'error';
	protected $icon = 'ui-warning-octagon';
	protected $skip_duplicates = true;

	private $_files_php = array( 'php', 'php3', 'php4', 'php5', 'phtml', 'phps' );
	private $_files_media = array( 'jpg', 'jpeg', 'bmp', 'png', 'gif', 'webp', 'avi', 'mov', 'mp4', 'mp3' );
	private $_files_style = array( 'css' );
	private $_files_script = array( 'js' );

	public function tracking() {
		add_action( 'template_redirect', array( $this, 'event_error' ), 10000000 );
	}

	public function label() : string {
		return __( 'Errors', 'coreactivity' );
	}

	protected function get_events() : array {
		return array(
			'404'        => array(
				'label' => __( '404 Not Found', 'coreactivity' ),
			),
			'404-php'    => array(
				'label' => __( '404 Not Found PHP', 'coreactivity' ),
			),
			'404-file'   => array(
				'label' => __( '404 Not Found File', 'coreactivity' ),
			),
			'404-media'  => array(
				'label' => __( '404 Not Found Media', 'coreactivity' ),
			),
			'404-script' => array(
				'label' => __( '404 Not Found Script', 'coreactivity' ),
			),
			'404-style'  => array(
				'label' => __( '404 Not Found Style', 'coreactivity' ),
			),
		);
	}

	public function logs_meta_column_keys( array $meta_column_keys ) : array {
		$meta_column_keys[ $this->code() ] = array(
			'-' => array(
				'request',
			),
		);

		return $meta_column_keys;
	}

	public function event_error() {
		if ( is_404() ) {
			$url = Core::instance()->get( 'request' );

			$query = wp_parse_url( $url, PHP_URL_QUERY );
			$query = ! empty( $query ) ? trim( $query ) : '';
			$path  = wp_parse_url( $url, PHP_URL_PATH );
			$ext   = ! empty( $path ) ? trim( strtolower( pathinfo( $path, PATHINFO_EXTENSION ) ) ) : '';

			if ( ! empty( $ext ) ) {
				if ( $this->check_for_exceptions( $url ) ) {
					return;
				}
			}

			$php    = in_array( $ext, $this->_files_php );
			$style  = in_array( $ext, $this->_files_style );
			$script = in_array( $ext, $this->_files_script );
			$media  = in_array( $ext, $this->_files_media );
			$file   = ! empty( $ext ) && ! $php && ! $style && ! $script && ! $media;

			$data = array();
			$meta = array(
				'ext'   => $ext,
				'query' => $query,
			);

			if ( $this->is_active( '404-php' ) && $php ) {
				$this->log( '404-php', $data, $meta );
			} else if ( $this->is_active( '404-script' ) && $script ) {
				$this->log( '404-script', $data, $meta );
			} else if ( $this->is_active( '404-style' ) && $style ) {
				$this->log( '404-style', $data, $meta );
			} else if ( $this->is_active( '404-media' ) && $media ) {
				$this->log( '404-media', $data, $meta );
			} else if ( $this->is_active( '404-file' ) && $file ) {
				$this->log( '404-file', $data, $meta );
			} else if ( $this->is_active( '404' ) ) {
				$this->log( '404', $data, $meta );
			}
		}
	}

	private function check_for_exceptions( $input ) : bool {
		$rules = coreactivity_settings()->get( 'exceptions_error_file_regex_list' );
		$regex = '/' . implode( '|', $rules ) . '/';

		if ( preg_match( $regex, $input ) ) {
			return true;
		}

		return false;
	}
}
