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
        <style>
            #wpadminbar .coreactivity-adminbar-count {
                display: inline-block;
                text-align: center;
                padding: 0 6px;
                height: 20px;
                border-radius: 10px;
                margin: 6px 0 0 4px;
                vertical-align: top;
                line-height: 20px;
                font-size: 12px
            }

            #wpadminbar .ab-submenu .coreactivity-adminbar-count {
                float: right;
            }

            @media screen and (max-width: 782px) {
                #wpadminbar li#wp-admin-bar-coreactivity-menu {
                    display: block;
                }
            }</style>
		<?php
	}

	public function integration() {
		if ( current_user_can( 'manage_options' ) ) {
			global $wp_admin_bar;

			$show  = '';
			$count = coreactivity_settings()->get( 'admin_bar_indicator' ) ? DB::instance()->get_new_log_entries_since_last_log_visit() : 0;

			$title = '<span style="margin-top: 2px" class="ab-icon dashicons dashicons-database"></span><span class="ab-label">' . __( 'coreActivity', 'coreactivity' ) . '</span>';

			if ( $count > 0 ) {
				$show  = '<span class="wp-ui-notification coreactivity-adminbar-count">' . $count . '</span>';
				$title .= $show;
			}

			$wp_admin_bar->add_menu( array(
				'id'    => 'coreactivity-menu',
				'title' => $title,
				'href'  => network_admin_url( 'admin.php?page=coreactivity-dashboard' ),
			) );

			$wp_admin_bar->add_group( array(
				'parent' => 'coreactivity-menu',
				'id'     => 'coreactivity-menu-top',
			) );

			$wp_admin_bar->add_group( array(
				'parent' => 'coreactivity-menu',
				'id'     => 'coreactivity-menu-bottom',
			) );

			$wp_admin_bar->add_menu( array(
				'parent' => 'coreactivity-menu-top',
				'id'     => 'coreactivity-menu-events',
				'title'  => __( 'Events', 'coreactivity' ),
				'href'   => network_admin_url( 'admin.php?page=coreactivity-events' ),
			) );

			$wp_admin_bar->add_menu( array(
				'parent' => 'coreactivity-menu-top',
				'id'     => 'coreactivity-menu-logs',
				'title'  => __( 'Logs', 'coreactivity' ) . $show,
				'href'   => network_admin_url( 'admin.php?page=coreactivity-logs' ),
			) );

			$wp_admin_bar->add_menu( array(
				'parent' => 'coreactivity-menu-bottom',
				'id'     => 'coreactivity-menu-settings',
				'title'  => __( 'Settings', 'coreactivity' ),
				'href'   => network_admin_url( 'admin.php?page=coreactivity-settings' ),
			) );

			$wp_admin_bar->add_menu( array(
				'parent' => 'coreactivity-menu-bottom',
				'id'     => 'coreactivity-menu-tools',
				'title'  => __( 'Tools', 'coreactivity' ),
				'href'   => network_admin_url( 'admin.php?page=coreactivity-tools' ),
			) );
		}
	}
}
