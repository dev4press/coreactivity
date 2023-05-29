<?php

namespace Dev4Press\Plugin\CoreActivity\Admin\Panel;

use Dev4Press\v42\Core\UI\Admin\PanelDashboard;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Dashboard extends PanelDashboard {
	public function __construct( $admin ) {
		parent::__construct( $admin );

		$this->sidebar_links[ 'plugin' ][ 'items' ] = array(
			'icon'  => $this->a()->menu_items[ 'items' ][ 'icon' ],
			'class' => 'button-primary',
			'url'   => $this->a()->panel_url( 'items' ),
			'label' => __( "Shared Items", "coreactivity" )
		);

		$this->sidebar_links[ 'plugin' ][ 'log' ] = array(
			'icon'  => $this->a()->menu_items[ 'log' ][ 'icon' ],
			'class' => 'button-primary',
			'url'   => $this->a()->panel_url( 'log' ),
			'label' => __( "Share Logs", "coreactivity" )
		);
	}
}
