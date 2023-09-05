<?php

namespace Dev4Press\Plugin\CoreActivity\Basic;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AdminBar {
	public function __construct() {
		add_action( 'wp_before_admin_bar_render', array( $this, 'integration' ) );

		add_action( 'admin_head', array( $this, 'style' ) );
		add_action( 'wp_head', array( $this, 'style' ) );
	}

	public static function instance() : AdminBar {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new AdminBar();
		}

		return $instance;
	}

	public function style() {
		?>
        <style>@media screen and (max-width: 782px) {
                #wpadminbar li#wp-admin-bar-coreactivity-menu {
                    display: block;
                }
            }</style>
		<?php
	}

	public function integration() {
		if ( current_user_can( 'manage_options' ) ) {
			global $wp_admin_bar;

			$wp_admin_bar->add_menu( array(
				'id'    => 'coreactivity-menu',
				'title' => '<span style="margin-top: 2px" class="ab-icon dashicons dashicons-database"></span><span class="ab-label">' . __( 'CoreActivity', 'coreactivity' ) . '</span>',
				'href'  => network_admin_url( 'admin.php?page=coreactivity-dashboard' ),
			) );

			$wp_admin_bar->add_menu( array(
				'parent' => 'coreactivity-menu',
				'id'     => 'coreactivity-menu-logs',
				'title'  => __( 'Logs', 'coreactivity' ),
				'href'   => network_admin_url( 'admin.php?page=coreactivity-logs' ),
			) );

			$wp_admin_bar->add_menu( array(
				'parent' => 'coreactivity-menu',
				'id'     => 'coreactivity-menu-events',
				'title'  => __( 'Events', 'coreactivity' ),
				'href'   => network_admin_url( 'admin.php?page=coreactivity-events' ),
			) );

			$wp_admin_bar->add_menu( array(
				'parent' => 'coreactivity-menu',
				'id'     => 'coreactivity-menu-settings',
				'title'  => __( 'Settings', 'coreactivity' ),
				'href'   => network_admin_url( 'admin.php?page=coreactivity-settings' ),
			) );
		}
	}
}
