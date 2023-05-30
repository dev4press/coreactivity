<?php

namespace Dev4Press\Plugin\CoreActivity\Admin;

use Dev4Press\Plugin\CoreActivity\Basic\Plugin as CorePlugin;
use Dev4Press\Plugin\CoreActivity\Basic\Settings as CoreSettings;
use Dev4Press\v42\Core\Admin\Network\Plugin as BasePlugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Plugin extends BasePlugin {
	public $plugin = 'coreactivity';
	public $plugin_prefix = 'coreactivity';
	public $plugin_menu = 'CoreActivity';
	public $plugin_title = 'CoreActivity';
	public $plugin_settings = 'network-only';

	public $auto_mod_interface_colors = true;
	public $has_widgets = true;
	public $has_metabox = true;

	public $enqueue_wp = array( 'dialog' => true, 'color_picker' => true );
	public $per_page_options = array(
		'coreactivity_log_rows_per_page',
		'coreactivity_items_rows_per_page'
	);

	public function constructor() {
		$this->url  = COREACTIVITY_URL;
		$this->path = COREACTIVITY_PATH;
	}

	public function register_scripts_and_styles() {
		$this->enqueue->register( 'css', 'coreactivity-admin',
			array(
				'path' => 'css/',
				'file' => 'admin',
				'ext'  => 'css',
				'min'  => true,
				'ver'  => coreactivity_settings()->file_version(),
				'src'  => 'plugin'
			) )->register( 'css', 'coreactivity-meta',
			array(
				'path' => 'css/',
				'file' => 'meta',
				'ext'  => 'css',
				'min'  => true,
				'ver'  => coreactivity_settings()->file_version(),
				'src'  => 'plugin'
			) )->register( 'js', 'coreactivity-admin',
			array(
				'path' => 'js/',
				'file' => 'admin',
				'ext'  => 'js',
				'min'  => true,
				'ver'  => coreactivity_settings()->file_version(),
				'src'  => 'plugin'
			) );
	}

	protected function extra_enqueue_scripts_plugin() {
		$this->enqueue->css( 'coreactivity-admin' );
		$this->enqueue->js( 'coreactivity-admin' );
	}

	public function svg_icon() : string {
		return coreactivity()->svg_icon;
	}

	public function admin_menu_items() {
		$this->setup_items = array(
			'install' => array(
				'title' => __( "Install", "coreactivity" ),
				'icon'  => 'ui-traffic',
				'type'  => 'setup',
				'info'  => __( "Before you continue, make sure plugin installation was successful.", "coreactivity" ),
				'class' => '\\Dev4Press\\Plugin\\CoreActivity\\Admin\\Panel\\Install',
				'scope' => array( 'network' )
			),
			'update'  => array(
				'title' => __( "Update", "coreactivity" ),
				'icon'  => 'ui-traffic',
				'type'  => 'setup',
				'info'  => __( "Before you continue, make sure plugin was successfully updated.", "coreactivity" ),
				'class' => '\\Dev4Press\\Plugin\\CoreActivity\\Admin\\Panel\\Update',
				'scope' => array( 'network' )
			)
		);

		$this->menu_items = array(
			'dashboard' => array(
				'title' => __( "Overview", "coreactivity" ),
				'icon'  => 'ui-home',
				'class' => '\\Dev4Press\\Plugin\\CoreActivity\\Admin\\Panel\\Dashboard',
				'scope' => array( 'blog', 'network' )
			),
			'about'     => array(
				'title' => __( "About", "coreactivity" ),
				'icon'  => 'ui-info',
				'class' => '\\Dev4Press\\Plugin\\CoreActivity\\Admin\\Panel\\About',
				'scope' => array( 'blog', 'network' )
			),
			'events'    => array(
				'title' => __( "Events", "coreactivity" ),
				'icon'  => 'ui-radar',
				'info'  => __( "All the events registered for activity tracking and logging.", "coreactivity" ),
				'class' => '\\Dev4Press\\Plugin\\CoreActivity\\Admin\\Panel\\Events',
				'scope' => array( 'network' )
			),
			'logs'      => array(
				'title' => __( "Logs", "coreactivity" ),
				'icon'  => 'ui-calendar-pen',
				'info'  => __( "Detailed log with all the logged activities for all supported events.", "coreactivity" ),
				'class' => '\\Dev4Press\\Plugin\\CoreActivity\\Admin\\Panel\\Logs',
				'scope' => array( 'blog', 'network' )
			),
			'settings'  => array(
				'title' => __( "Settings", "coreactivity" ),
				'icon'  => 'ui-cog',
				'class' => '\\Dev4Press\\Plugin\\CoreActivity\\Admin\\Panel\\Settings',
				'scope' => array( 'network' )
			),
			'tools'     => array(
				'title' => __( "Tools", "coreactivity" ),
				'icon'  => 'ui-wrench',
				'class' => '\\Dev4Press\\Plugin\\CoreActivity\\Admin\\Panel\\Tools',
				'scope' => array( 'network' )
			)
		);
	}

	public function run_getback() {
		new GetBack( $this );
	}

	public function run_postback() {
		new PostBack( $this );
	}

	public function message_process( $code, $msg ) {
		return $msg;
	}

	public function settings() : CoreSettings {
		return coreactivity_settings();
	}

	public function plugin() : CorePlugin {
		return coreactivity();
	}

	public function settings_definitions() : Settings {
		return Settings::instance();
	}
}
