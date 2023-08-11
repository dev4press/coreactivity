<?php

namespace Dev4Press\Plugin\CoreActivity\Admin\Panel;

use Dev4Press\Plugin\CoreActivity\Basic\Render;
use Dev4Press\v43\Core\UI\Admin\PanelDashboard;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Dashboard extends PanelDashboard {
	public function __construct( $admin ) {
		parent::__construct( $admin );

		if ( isset( $this->a()->menu_items['events'] ) ) {
			$this->sidebar_links['plugin']['events'] = array(
				'icon'  => $this->a()->menu_items['events']['icon'],
				'class' => 'button-primary',
				'url'   => $this->a()->panel_url( 'events', '', '', $this->a()->get_menu_item_network_url_flag( 'events' ) ),
				'label' => __( "Events", "coreactivity" ),
				'scope' => $this->a()->menu_items['events']['scope'] ?? array(),
			);
		}

		$this->sidebar_links['plugin']['logs'] = array(
			'icon'  => $this->a()->menu_items['logs']['icon'],
			'class' => 'button-primary',
			'url'   => $this->a()->panel_url( 'logs', '', '', $this->a()->get_menu_item_network_url_flag( 'logs' ) ),
			'label' => __( "Logs", "coreactivity" ),
			'scope' => $this->a()->menu_items['logs']['scope'] ?? array(),
		);
	}

	public function header_fill() : string {
		return Render::panel_header_ip_block();
	}
}
