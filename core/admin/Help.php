<?php

namespace Dev4Press\Plugin\CoreActivity\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Help {
	public function __construct() {
		if ( $this->a()->panel == 'dashboard' ) {
			$this->_for_dashboard();
		} else if ( $this->a()->panel == 'logs' ) {
			$this->_for_logs();
		} else if ( $this->a()->panel == 'events' ) {
			$this->_for_events();
		}
	}

	public static function instance() : Help {
		static $instance = null;

		if ( is_null( $instance ) ) {
			$instance = new Help();
		}

		return $instance;
	}

	private function a() : Plugin {
		return coreactivity_admin();
	}

	private function _for_dashboard() {
		$this->a()->screen()->add_help_tab(
			array(
				'id'      => 'coreactivity-dashboard-events',
				'title'   => __( 'Basics', 'coreactivity' ),
				'content' => '<h2>' . __( 'Components and Events', 'coreactivity' ) . '</h2>' .
				             '<p>' . __( 'Some components and events included in coreActivity are not always registered. Some components are made for WordPress Multisite, and some components require specific third-party plugins. The widget on this panel, and list of all Events show actual status of registered components and available events.', 'coreactivity' ) . '</p>' .
				             '<p>' . __( 'To stop all the logging, you can use the Toggle button on the Dashboard to disable or later enable the logging system. This can be useful if you are testing something, and want to temporarily disable the logging.', 'coreactivity' ) . '</p>' .
				             '<p><a href="' . esc_attr( $this->a()->panel_url( 'events' ) ) . '/" class="button-primary">' . __( 'All Components and Events', 'coreactivity' ) . '</a></p>' .
				             '<h2>' . __( 'Database Statistics', 'coreactivity' ) . '</h2>' .
				             '<p>' . __( 'Plugin has 3 database tables, and the widget on the dashboard shows the current status of those tables, number of records, total size, free space and more.', 'coreactivity' ) . '</p>' .
				             '<h2>' . __( 'GEO Location', 'coreactivity' ) . '</h2>' .
				             '<p>' . __( 'To identify the country for each logged event, plugin can use logged IP to find the country and city. For this process, plugin can use online GEO location service or local database.', 'coreactivity' ) . '</p>' .
				             '<p><a href="' . esc_attr( $this->a()->panel_url( 'settings', 'geo' ) ) . '/" class="button-primary">' . __( 'GEO Location Settings', 'coreactivity' ) . '</a></p>',
			)
		);

		$this->a()->screen()->add_help_tab(
			array(
				'id'      => 'coreactivity-dashboard-stats',
				'title'   => __( 'Statistics', 'coreactivity' ),
				'content' => '<h2>' . __( 'Quick Sweep', 'coreactivity' ) . '</h2>' .
				             '<p>' . __( 'The simple statistical overview, shows number of log entries for all logged components in the last 30 days. Click on the component icon button, to go to the Logs page filtered by the selected component.', 'coreactivity' ) . '</p>' .
				             '<p>' . __( 'Main Events panel lists counts for each component and event based on all the log records currently in the database.', 'coreactivity' ) . '</p>',
			)
		);
	}

	private function _for_logs() {
		$this->a()->screen()->add_help_tab(
			array(
				'id'      => 'coreactivity-logs-help',
				'title'   => __( 'Logs', 'coreactivity' ),
				'content' => '<h2>' . __( 'Logs', 'coreactivity' ) . '</h2>' .
				             '<p>' . __( 'This is the core of the coreActivity plugin, panel where all the logged records are displayed. There are few important things to know.', 'coreactivity' ) . '</p>' .
				             '<ul>' .
				             '<li>' . __( 'Depending on your screen size, the table can have strange layout due to the number of columns it can show. Make sure to check out the Screen Options tab to disable some of the columns. And, some columns related options are available in the plugins settings.', 'coreactivity' ) . '</li>' .
				             '<li>' . __( 'To see all additional information about each event, click on the toggle button on the right side of the table to show hidden Metadata row.', 'coreactivity' ) . '</li>' .
				             '<li>' . __( 'Based on the current view mode, some columns and filter elements can be automatically hidden.', 'coreactivity' ) . '</li>' .
				             '</ul>' .
				             '<p><a href="' . esc_attr( $this->a()->panel_url( 'settings', 'logs' ) ) . '/" class="button-primary">' . __( 'Logs Panel Settings', 'coreactivity' ) . '</a></p>',
			)
		);
	}

	private function _for_events() {
		$this->a()->screen()->add_help_tab(
			array(
				'id'      => 'coreactivity-events-help',
				'title'   => __( 'Events', 'coreactivity' ),
				'content' => '<h2>' . __( 'Events', 'coreactivity' ) . '</h2>' .
				             '<p>' . __( 'This panel shows all the registered components and events, along with the status toggle, and toggles for the notifications.', 'coreactivity' ) . '</p>' .
				             '<ul>' .
				             '<li>' . __( 'For each event, you have additional buttons to filter or view the Logs for the selected component or event, and button to open the Cleanup tools panel to remove selected component or event only.', 'coreactivity' ) . '</li>' .
				             '<li>' . __( 'Log records counts are based on the current number of records in the logs belonging to each event.', 'coreactivity' ) . '</li>' .
				             '</ul>',
			)
		);
	}
}
