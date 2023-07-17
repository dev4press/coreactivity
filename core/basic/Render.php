<?php

namespace Dev4Press\Plugin\CoreActivity\Basic;

use Dev4Press\Plugin\CoreActivity\Log\Core;
use Dev4Press\v43\Service\GEOIP\GEOJSIO;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Render {
	public static function panel_header_ip_block() : string {
		GEOJSIO::instance()->bulk( array( Core::instance()->get( 'server_ip' ), Core::instance()->get( 'ip' ) ) );

		$server_ip  = GEOJSIO::instance()->locate( Core::instance()->get( 'server_ip' ) );
		$visitor_ip = GEOJSIO::instance()->locate( Core::instance()->get( 'ip' ) );

		$render = '<li class="d4p-nav-button d4p-header-special-button">';
		$render .= '<i class="d4p-icon d4p-ui-database" title="' . esc_attr__( "Server IP", "coreactivity" ) . '"></i>';
		$render .= '<span>' . Core::instance()->get( 'server_ip' ) . '</span>';
		$render .= $server_ip->flag();
		$render .= '</li>';
		$render .= '<li class="d4p-nav-button d4p-header-special-button">';
		$render .= '<i class="d4p-icon d4p-ui-user-square" title="' . esc_attr__( "Current Request IP", "coreactivity" ) . '"></i>';
		$render .= '<span>' . Core::instance()->get( 'ip' ) . '</span>';
		$render .= $visitor_ip->flag();
		$render .= '</li>';

		return $render;
	}
}
