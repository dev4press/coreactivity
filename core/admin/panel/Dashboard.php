<?php

namespace Dev4Press\Plugin\CoreActivity\Admin\Panel;

use Dev4Press\Plugin\CoreActivity\Basic\Render;
use Dev4Press\v50\Core\UI\Admin\PanelDashboard;
use Dev4Press\v50\Core\Scope;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Dashboard extends PanelDashboard {
	public function __construct( $admin ) {
		parent::__construct( $admin );

		if ( Scope::instance()->is_master_network_admin() ) {
			$this->sidebar_links['plugin']['events'] = array(
				'icon'  => $this->a()->menu_items['events']['icon'],
				'class' => 'button-primary',
				'url'   => $this->a()->panel_url( 'events', '', '', $this->a()->get_menu_item_network_url_flag( 'events' ) ),
				'label' => __( 'Events', 'coreactivity' ),
				'scope' => $this->a()->menu_items['events']['scope'] ?? array(),
			);
		} else {
			$this->sidebar_links['plugin']['events'] = array(
				'icon'  => $this->a()->menu_items['dashboard']['icon'],
				'class' => 'button-primary',
				'url'   => $this->a()->panel_url( 'dashboard', '', '', true ),
				'label' => __( 'Network Dashboard', 'coreactivity' ),
				'scope' => $this->a()->menu_items['dashboard']['scope'] ?? array(),
			);
		}

		$this->sidebar_links['plugin']['logs'] = array(
			'icon'  => $this->a()->menu_items['logs']['icon'],
			'class' => Scope::instance()->is_master_network_admin() ? 'button-primary' : 'button-secondary',
			'url'   => $this->a()->panel_url( 'logs', '', '', $this->a()->get_menu_item_network_url_flag( 'logs' ) ),
			'label' => __( 'Logs', 'coreactivity' ),
			'scope' => $this->a()->menu_items['logs']['scope'] ?? array(),
		);

		if ( ! Scope::instance()->is_master_network_admin() ) {
			unset( $this->sidebar_links['basic']['settings'], $this->sidebar_links['basic']['tools'] );
		}
	}

	public function header_fill() : string {
		return Render::panel_header_ip_block();
	}
}
