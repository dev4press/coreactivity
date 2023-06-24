<?php

namespace Dev4Press\Plugin\CoreActivity\Admin\Panel;

use Dev4Press\Plugin\CoreActivity\Log\Core;
use Dev4Press\Plugin\CoreActivity\Table\Logs as LogsTable;
use Dev4Press\Plugin\CoreActivity\Admin\Panel;
use Dev4Press\v43\Service\GEOIP\GEOJSIO;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Logs extends Panel {
	protected $table = true;
	protected $sidebar = false;
	protected $form = true;
	protected $form_multiform = false;
	protected $form_method = 'get';

	public function screen_options_show() {
		add_screen_option( 'per_page', array(
			'label'   => __( "Rows", "coreactivity" ),
			'default' => 25,
			'option'  => 'coreactivity_logs_rows_per_page'
		) );

		new LogsTable();
	}

	public function header_fill() : string {
		GEOJSIO::instance()->bulk( array( Core::instance()->get( 'server_ip' ), Core::instance()->get( 'ip' ) ) );

		$server_ip  = GEOJSIO::instance()->locate( Core::instance()->get( 'server_ip' ) );
		$visitor_ip = GEOJSIO::instance()->locate( Core::instance()->get( 'ip' ) );

		$render = '<li class="d4p-nav-button coreactivity-header-ip">';
		$render .= '<i class="d4p-icon d4p-ui-database" title="' . esc_attr__( "Server IP", "coreactivity" ) . '"></i>';
		$render .= '<span>' . Core::instance()->get( 'server_ip' ) . '</span>';
		$render .= $server_ip->flag();
		$render .= '</li>';
		$render .= '<li class="d4p-nav-button coreactivity-header-ip">';
		$render .= '<i class="d4p-icon d4p-ui-user-square" title="' . esc_attr__( "Current Request IP", "coreactivity" ) . '"></i>';
		$render .= '<span>' . Core::instance()->get( 'ip' ) . '</span>';
		$render .= $visitor_ip->flag();
		$render .= '</li>';

		return $render;
	}
}
