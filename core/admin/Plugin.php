<?php

namespace Dev4Press\Plugin\CoreActivity\Admin;

use Dev4Press\Plugin\CoreActivity\Basic\Plugin as CorePlugin;
use Dev4Press\Plugin\CoreActivity\Basic\Settings as CoreSettings;
use Dev4Press\Plugin\CoreActivity\Basic\Wizard;
use Dev4Press\v45\Core\Admin\Network\Plugin as BasePlugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Plugin extends BasePlugin {
	public $plugin = 'coreactivity';
	public $plugin_prefix = 'coreactivity';
	public $plugin_menu = 'coreActivity';
	public $plugin_title = 'coreActivity';
	public $buy_me_a_coffee = true;
	public $plugin_settings = 'network-only';

	public $auto_mod_interface_colors = true;
	public $has_widgets = true;
	public $has_metabox = true;

	public $enqueue_wp = array(
		'dialog'       => true,
		'color_picker' => true,
	);
	public $per_page_options = array(
		'coreactivity_logs_rows_per_page',
		'coreactivity_events_rows_per_page',
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
				'src'  => 'plugin',
				'int'  => array( 'flags', 'ctrl' ),
			) )->register( 'js', 'coreactivity-admin',
			array(
				'path' => 'js/',
				'file' => 'admin',
				'ext'  => 'js',
				'min'  => true,
				'ver'  => coreactivity_settings()->file_version(),
				'src'  => 'plugin',
				'int'  => array( 'ctrl' ),
			) );
	}

	public function plugins_loaded() {
		parent::plugins_loaded();

		add_filter( 'default_hidden_columns', array( $this, 'hide_columns_default' ), 10, 2 );
	}

	public function hide_columns_default( $columns, $screen ) {
		if ( $screen->id == 'coreactivity_page_coreactivity-logs' ) {
			$columns[] = 'method';
			$columns[] = 'protocol';
			$columns[] = 'object_type';
			$columns[] = 'object_name';
		}

		return $columns;
	}

	public function svg_icon() : string {
		return coreactivity()->svg_icon;
	}

	public function admin_menu_items() {
		$this->setup_items = array(
			'install' => array(
				'title' => __( 'Install', 'coreactivity' ),
				'icon'  => 'ui-traffic',
				'type'  => 'setup',
				'info'  => __( 'Before you continue, make sure plugin installation was successful.', 'coreactivity' ),
				'class' => '\\Dev4Press\\Plugin\\CoreActivity\\Admin\\Panel\\Install',
				'scope' => array( 'network' ),
			),
			'update'  => array(
				'title' => __( 'Update', 'coreactivity' ),
				'icon'  => 'ui-traffic',
				'type'  => 'setup',
				'info'  => __( 'Before you continue, make sure plugin was successfully updated.', 'coreactivity' ),
				'class' => '\\Dev4Press\\Plugin\\CoreActivity\\Admin\\Panel\\Update',
				'scope' => array( 'network' ),
			),
		);

		$this->menu_items = array(
			'dashboard' => array(
				'title' => __( 'Overview', 'coreactivity' ),
				'icon'  => 'ui-home',
				'class' => '\\Dev4Press\\Plugin\\CoreActivity\\Admin\\Panel\\Dashboard',
				'scope' => array( 'blog', 'network' ),
			),
			'about'     => array(
				'title' => __( 'About', 'coreactivity' ),
				'icon'  => 'ui-info',
				'class' => '\\Dev4Press\\Plugin\\CoreActivity\\Admin\\Panel\\About',
				'scope' => array( 'blog', 'network' ),
			),
			'logs'      => array(
				'title' => __( 'Logs', 'coreactivity' ),
				'icon'  => 'ui-calendar-pen',
				'info'  => __( 'Detailed log with all the logged activities for all supported events.', 'coreactivity' ),
				'class' => '\\Dev4Press\\Plugin\\CoreActivity\\Admin\\Panel\\Logs',
				'scope' => array( 'blog', 'network' ),
			),
			'events'    => array(
				'title' => __( 'Events', 'coreactivity' ),
				'icon'  => 'ui-radar',
				'info'  => __( 'All the events registered for activity tracking and logging.', 'coreactivity' ),
				'class' => '\\Dev4Press\\Plugin\\CoreActivity\\Admin\\Panel\\Events',
				'scope' => array( 'network' ),
			),
			'settings'  => array(
				'title' => __( 'Settings', 'coreactivity' ),
				'icon'  => 'ui-cog',
				'class' => '\\Dev4Press\\Plugin\\CoreActivity\\Admin\\Panel\\Settings',
				'scope' => array( 'network' ),
			),
			'tools'     => array(
				'title' => __( 'Tools', 'coreactivity' ),
				'icon'  => 'ui-wrench',
				'class' => '\\Dev4Press\\Plugin\\CoreActivity\\Admin\\Panel\\Tools',
				'scope' => array( 'network' ),
			),
		);

		if ( $this->settings()->get( 'show_setup_wizard' ) ) {
			$this->menu_items['wizard'] = array(
				'title' => __( 'Setup Wizard', 'coreactivity' ),
				'icon'  => 'ui-magic',
				'class' => '\\Dev4Press\\Plugin\\CoreActivity\\Admin\\Panel\\Wizard',
			);
		}
	}

	public function run_getback() {
		new GetBack( $this );
	}

	public function run_postback() {
		new PostBack( $this );
	}

	public function admin_init() {
		if ( ! coreactivity()->is_logging_active() ) {
			if ( coreactivity_settings()->get( 'notice_if_logging_is_disabled' ) ) {
				if ( is_super_admin() ) {
					add_action( 'admin_notices', array( $this, 'notice_disabled' ) );
				}
			}
		}
	}

	public function notice_disabled() {
		?>

        <div class="notice notice-error">
            <p><?php esc_html_e( 'coreActivity events logging has been disabled. You can enable it again from the plugin Dashboard.', 'coreactivity' ); ?>
                <a href="<?php echo esc_url( $this->panel_url() ); ?>"><?php esc_html_e( 'coreActivity Dashboard', 'coreactivity' ); ?></a></p>
        </div>

		<?php
	}

	public function message_process( $code, $msg ) {
		switch ( $code ) {
			case 'cleanup-completed':
				$msg['message'] = __( 'The cleanup operation has been completed.', 'coreactivity' );
				break;
			case 'events-updated':
				$msg['message'] = __( 'Events activity status has been updated.', 'coreactivity' );
				break;
			case 'notifications-updated':
				$msg['message'] = __( 'Events notifications status has been updated.', 'coreactivity' );
				break;
		}

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

	public function enqueue() {
		$this->e()->css( 'coreactivity-admin' );
		$this->e()->js( 'coreactivity-admin' );

		$values = array(
			'live_updates' => $this->settings()->get( 'logs_live_updates' ) ? 'Y' : 'N',
		);

		wp_localize_script( $this->e()->prefix() . 'coreactivity-admin', 'coreactivity_data', $values );
	}

	protected function extra_enqueue_scripts_plugin() {
		$this->enqueue();
	}

	public function wizard() {
		return Wizard::instance();
	}
}
