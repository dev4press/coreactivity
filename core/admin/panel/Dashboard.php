<?php

namespace Dev4Press\Plugin\CoreActivity\Admin\Panel;

use Dev4Press\v42\Core\UI\Admin\PanelDashboard;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Dashboard extends PanelDashboard {
	public function __construct( $admin ) {
		parent::__construct( $admin );

		$this->sidebar_links[ 'plugin' ][ 'events' ] = array(
			'icon'  => $this->a()->menu_items[ 'events' ][ 'icon' ],
			'class' => 'button-primary',
			'url'   => $this->a()->panel_url( 'events' ),
			'label' => __( "Events", "coreactivity" )
		);

		$this->sidebar_links[ 'plugin' ][ 'logs' ] = array(
			'icon'  => $this->a()->menu_items[ 'logs' ][ 'icon' ],
			'class' => 'button-primary',
			'url'   => $this->a()->panel_url( 'logs' ),
			'label' => __( "Logs", "coreactivity" )
		);
	}
}
