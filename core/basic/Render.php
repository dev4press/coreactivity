<?php

namespace Dev4Press\Plugin\CoreActivity\Basic;

use Dev4Press\Plugin\CoreActivity\Log\Core;
use Dev4Press\Plugin\CoreActivity\Log\GEO;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Render {
	public static function panel_header_ip_block() : string {
		GEO::instance()->bulk( array( Core::instance()->get( 'server_ip' ), Core::instance()->get( 'ip' ) ) );

		$_server_ip  = Core::instance()->get( 'server_ip' );
		$_visitor_ip = Core::instance()->get( 'ip' );

		$server_ip  = empty( $_server_ip ) ? null : GEO::instance()->locate( $_server_ip );
		$visitor_ip = empty( $_visitor_ip ) ? null : GEO::instance()->locate( $_visitor_ip );

		$render = '<li class="d4p-nav-button d4p-header-special-button">';
		$render .= '<div class="d4p-nav-button-inner">';
		$render .= '<i class="d4p-icon d4p-ui-database" title="' . esc_html__( 'Server IP', 'coreactivity' ) . '"></i>';
		$render .= '<span>' . ( empty( $_server_ip ) ? esc_html__( 'Unknown', 'coreactivity' ) : Core::instance()->get( 'server_ip' ) ) . '</span>';
		$render .= $server_ip ? $server_ip->flag() : '';
		$render .= '</div>';
		$render .= '<div class="d4p-nav-button-inner">';
		$render .= '<i class="d4p-icon d4p-ui-user-square" title="' . esc_attr__( 'Current Request IP', 'coreactivity' ) . '"></i>';
		$render .= '<span>' . ( empty( $_visitor_ip ) ? esc_html__( 'Unknown', 'coreactivity' ) : Core::instance()->get( 'ip' ) ) . '</span>';
		$render .= $visitor_ip ? $visitor_ip->flag() : '';
		$render .= '</div>';
		$render .= '</li>';

		return $render;
	}

	public static function ip_masked( $ip ) {
		$format = coreactivity_settings()->get( 'log_ipv4_display' );

		if ( $format != 'full' ) {
			$parts = explode( '.', $ip );

			if ( count( $parts ) == 4 ) {
				switch ( $format ) {
					case 'partial':
						return $parts[0] . '.' . $parts[1] . '.' . $parts[2] . '.***';
					case 'half':
						return $parts[0] . '.' . $parts[1] . '.***.***';
					case 'hide':
						return '***.***.***.***';
				}
			}
		}

		return $ip;
	}
}
