<?php

namespace Dev4Press\Plugin\CoreActivity\Log;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WhoIs {
	public function __construct() {
	}

	public static function instance() : WhoIs {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new WhoIs();
		}

		return $instance;
	}

	public function get( $ip ) : string {
		$result = $this->get_from_server( 'whois.iana.org', $ip );

		preg_match( '/whois:\s*([\w.]*)/si', $result, $output );

		if ( ! empty( $output ) ) {
			$server = $output[1];

			return $this->get_from_server( $server, $ip );
		}

		return $result;
	}

	private function get_from_server( $server, $ip ) : string {
		$result = '';
		$server = trim( $server );

		if ( ! empty( $server ) ) {
			$f = fsockopen( $server, 43, $err_no, $err_str, 10 );

			if ( $f ) {
				fputs( $f, $ip . PHP_EOL );

				stream_set_blocking( $f, 0 );

				while ( ! feof( $f ) ) {
					$result .= fread( $f, 128 );
				}

				fclose( $f );
			}
		}

		return $result;
	}
}
