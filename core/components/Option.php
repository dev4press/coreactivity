<?php

namespace Dev4Press\Plugin\CoreActivity\Components;

use Dev4Press\Plugin\CoreActivity\Base\Component;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Option extends Component {
	protected $plugin = 'coreactivity';
	protected $name = 'option';
	protected $icon = 'ui-sliders-base-hor';
	protected $object_type = 'option';

	protected $monitor = array(
		'siteurl',
		'home',
		'blogname',
		'blogdescription',
		'users_can_register',
		'admin_email',
		'start_of_week',
		'use_balanceTags',
		'use_smilies',
		'require_name_email',
		'comments_notify',
		'posts_per_rss',
		'rss_use_excerpt',
		'mailserver_url',
		'mailserver_login',
		'mailserver_pass',
		'mailserver_port',
		'default_category',
		'default_comment_status',
		'default_ping_status',
		'default_pingback_flag',
		'posts_per_page',
		'date_format',
		'time_format',
		'links_updated_date_format',
		'comment_moderation',
		'moderation_notify',
		'permalink_structure',
		'rewrite_rules',
		'hack_file',
		'blog_charset',
		'moderation_keys',
		'active_plugins',
		'category_base',
		'ping_sites',
		'comment_max_links',
		'gmt_offset',
		'default_email_category',
		'recently_edited',
		'template',
		'stylesheet',
		'comment_registration',
		'html_type',
		'use_trackback',
		'default_role',
		'db_version',
		'uploads_use_yearmonth_folders',
		'upload_path',
		'blog_public',
		'default_link_category',
		'show_on_front',
		'tag_base',
		'show_avatars',
		'avatar_rating',
		'upload_url_path',
		'thumbnail_size_w',
		'thumbnail_size_h',
		'thumbnail_crop',
		'medium_size_w',
		'medium_size_h',
		'avatar_default',
		'large_size_w',
		'large_size_h',
		'image_default_link_type',
		'image_default_size',
		'image_default_align',
		'close_comments_for_old_posts',
		'close_comments_days_old',
		'thread_comments',
		'thread_comments_depth',
		'page_comments',
		'comments_per_page',
		'default_comments_page',
		'comment_order',
		'sticky_posts',
		'widget_categories',
		'widget_text',
		'widget_rss',
		'uninstall_plugins',
		'timezone_string',
		'page_for_posts',
		'page_on_front',
		'default_post_format',
		'link_manager_enabled',
		'finished_splitting_shared_terms',
		'site_icon',
		'medium_large_size_w',
		'medium_large_size_h',
		'wp_page_for_privacy_policy',
		'show_comments_cookies_opt_in',
		'admin_email_lifespan',
		'disallowed_keys',
		'comment_previously_approved',
		'auto_plugin_theme_update_emails',
		'auto_update_core_dev',
		'auto_update_core_minor',
		'auto_update_core_major',
		'wp_force_deactivated_plugins',
		'theme_switch_menu_locations',
		'theme_switched',
		'current_theme',
		'widget_block',
	);

	protected $partials = array();
	protected $exceptions = array();
	protected $skip = array(
		'cron',
	);
	protected $transient_value;

	public function init() {
		$this->exceptions      = coreactivity_settings()->get( 'exceptions_option_list' );
		$this->transient_value = coreactivity_settings()->get( 'log_transient_value' );

		if ( coreactivity_settings()->get( 'exceptions_option_action_scheduler_lock' ) ) {
			$this->partials[] = 'action_scheduler_lock_';
		}
	}

	public function tracking() {
		if ( $this->is_active( 'core-option-edited' ) || $this->is_active( 'option-edited' ) ) {
			add_action( 'updated_option', array( $this, 'event_updated_option' ), 10, 3 );
		}

		if ( $this->is_active( 'core-option-deleted' ) || $this->is_active( 'option-deleted' ) ) {
			add_action( 'deleted_option', array( $this, 'event_deleted_option' ) );
		}

		if ( $this->is_active( 'option-added' ) ) {
			add_action( 'added_option', array( $this, 'event_added_option' ), 10, 2 );
		}

		if ( $this->is_active( 'transient-set' ) ) {
			add_action( 'setted_transient', array( $this, 'event_set_transient' ), 10, 3 );
		}

		if ( $this->is_active( 'transient-deleted' ) ) {
			add_action( 'deleted_transient', array( $this, 'event_deleted_transient' ) );
		}
	}

	public function label() : string {
		return __( "Options", "coreactivity" );
	}

	protected function get_events() : array {
		return array(
			'core-option-edited'  => array(
				'label' => __( "Core Option Changed", "coreactivity" ),
			),
			'core-option-deleted' => array(
				'label' => __( "Core Option Deleted", "coreactivity" ),
			),
			'option-added'        => array(
				'label' => __( "Option Added", "coreactivity" ),
			),
			'option-edited'       => array(
				'label' => __( "Option Changed", "coreactivity" ),
			),
			'option-deleted'      => array(
				'label' => __( "Option Deleted", "coreactivity" ),
			),
			'transient-set'       => array(
				'label'  => __( "Transient Set", "coreactivity" ),
				'status' => 'inactive',
			),
			'transient-deleted'   => array(
				'label'  => __( "Transient Deleted", "coreactivity" ),
				'status' => 'inactive',
			),
		);
	}

	public function event_updated_option( $option, $old_value, $value ) {
		if ( $this->is_transient( $option ) || $this->is_skippable( $option ) || $this->is_exception( $option ) ) {
			return;
		}

		$event = in_array( $option, $this->monitor ) ? 'core-option-edited' : 'option-edited';

		if ( $this->is_active( $event ) ) {
			$this->log( $event, array(
				'object_name' => $option,
			), array(
				'old' => $old_value,
				'new' => $value,
			) );
		}
	}

	public function event_deleted_option( $option ) {
		if ( $this->is_transient( $option ) ) {
			return;
		}

		$event = in_array( $option, $this->monitor ) ? 'core-option-deleted' : 'option-deleted';

		if ( $this->is_active( $event ) ) {
			$this->log( $event, array(
				'object_name' => $option,
			) );
		}
	}

	public function event_added_option( $option, $value ) {
		if ( $this->is_transient( $option ) ) {
			return;
		}

		$this->log( 'option-added', array(
			'object_name' => $option,
		), array(
			'value' => $value,
		) );
	}

	public function event_set_transient( $transient, $value, $expiration ) {
		$this->log( 'transient-set', array(
			'object_type' => 'transient',
			'object_name' => $transient,
		), array(
			'value'      => $this->transient_value ? $value : '',
			'expiration' => $expiration,
		) );
	}

	public function event_deleted_transient( $transient ) {
		$this->log( 'transient-deleted', array(
			'object_type' => 'transient',
			'object_name' => $transient,
		) );
	}

	private function is_transient( $option ) : bool {
		return substr( $option, 0, 11 ) == '_transient_' || substr( $option, 0, 16 ) == '_site_transient_';
	}

	private function is_exception( $option ) : bool {
		if ( ! empty( $this->exceptions ) && in_array( $option, $this->exceptions ) ) {
			return true;
		}

		if ( ! empty( $this->partials ) ) {
			foreach ( $this->partials as $part ) {
				if ( substr( $option, 0, strlen( $part ) ) == $part ) {
					return true;
				}
			}
		}

		return false;
	}

	private function is_skippable( $option ) : bool {
		return ! empty( $this->skip ) && in_array( $option, $this->skip );
	}
}
