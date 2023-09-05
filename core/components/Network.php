<?php

namespace Dev4Press\Plugin\CoreActivity\Components;

use Dev4Press\Plugin\CoreActivity\Base\Component;
use WP_Error;
use WP_User;

class Network extends Component {
	protected $plugin = 'coreactivity';
	protected $name = 'network';
	protected $icon = 'ui-sitemap';
	protected $object_type = 'blog';
	protected $scope = 'network';

	public function is_available() : bool {
		return is_multisite();
	}

	public function registered_object_types( array $object_types ) : array {
		$object_types['blog'] = __( 'Blog', 'coreactivity' );

		return $object_types;
	}

	public function tracking() {
		if ( $this->is_active( 'blog-created' ) ) {
			add_action( 'wp_initialize_site', array( $this, 'event_initialize_site' ) );
		}

		if ( $this->is_active( 'blog-removed' ) ) {
			add_action( 'wp_uninitialize_site', array( $this, 'event_uninitialize_site' ) );
		}

		if ( $this->is_active( 'blog-updated' ) ) {
			add_action( 'wp_update_site', array( $this, 'event_update_site' ) );
		}

		if ( $this->is_active( 'blog-status-delete' ) ) {
			add_action( 'make_delete_blog', array( $this, 'event_delete_blog' ) );
		}

		if ( $this->is_active( 'blog-status-not-delete' ) ) {
			add_action( 'make_undelete_blog', array( $this, 'event_undelete_blog' ) );
		}

		if ( $this->is_active( 'blog-status-archive' ) ) {
			add_action( 'archive_blog', array( $this, 'event_archive_blog' ) );
		}

		if ( $this->is_active( 'blog-status-not-archive' ) ) {
			add_action( 'unarchive_blog', array( $this, 'event_not_archive_blog' ) );
		}

		if ( $this->is_active( 'blog-status-archive' ) ) {
			add_action( 'make_spam_blog', array( $this, 'event_spam_blog' ) );
		}

		if ( $this->is_active( 'blog-status-not-archive' ) ) {
			add_action( 'make_ham_blog', array( $this, 'event_not_spam_blog' ) );
		}

		if ( $this->is_active( 'blog-status-mature' ) ) {
			add_action( 'mature_blog', array( $this, 'event_mature_blog' ) );
		}

		if ( $this->is_active( 'blog-status-not-mature' ) ) {
			add_action( 'unmature_blog', array( $this, 'event_not_mature_blog' ) );
		}

		if ( $this->are_active( array( 'blog-status-public', 'blog-status-private' ) ) ) {
			add_action( 'update_blog_public', array( $this, 'event_blog_public' ) );
		}

		if ( $this->is_active( 'blog-signup' ) ) {
			add_action( 'after_signup_site', array( $this, 'event_after_signup_site' ), 10, 7 );
		}

		if ( $this->is_active( 'failed-blog-signup' ) ) {
			add_filter( 'wpmu_validate_blog_signup', array( $this, 'event_validate_blog_signup' ) );
		}
	}

	public function label() : string {
		return __( 'Multisite Network', 'coreactivity' );
	}

	protected function prepare_data_for_log( string $event, array $data = array() ) : array {
		$data['blog_id'] = 0;

		return parent::prepare_data_for_log( $event, $data );
	}

	protected function get_events() : array {
		return array(
			'blog-created'            => array(
				'label' => __( 'Blog Created', 'coreactivity' ),
			),
			'blog-removed'            => array(
				'label' => __( 'Blog Removed', 'coreactivity' ),
			),
			'blog-updated'            => array(
				'label' => __( 'Blog Updated', 'coreactivity' ),
			),
			'blog-signup'             => array(
				'label' => __( 'Blog Signup', 'coreactivity' ),
			),
			'failed-blog-signup'      => array(
				'label' => __( 'Failed Blog Signup', 'coreactivity' ),
			),
			'blog-status-delete'      => array(
				'label' => __( 'Blog Status Deleted', 'coreactivity' ),
			),
			'blog-status-not-delete'  => array(
				'label' => __( 'Blog Status Not Deleted', 'coreactivity' ),
			),
			'blog-status-archive'     => array(
				'label' => __( 'Blog Status Archive', 'coreactivity' ),
			),
			'blog-status-not-archive' => array(
				'label' => __( 'Blog Status Not Archive', 'coreactivity' ),
			),
			'blog-status-public'      => array(
				'label' => __( 'Blog Status Public', 'coreactivity' ),
			),
			'blog-status-private'     => array(
				'label' => __( 'Blog Status Private', 'coreactivity' ),
			),
			'blog-status-spam'        => array(
				'label' => __( 'Blog Status Spam', 'coreactivity' ),
			),
			'blog-status-not-spam'    => array(
				'label' => __( 'Blog Status Not Spam', 'coreactivity' ),
			),
			'blog-status-mature'      => array(
				'label' => __( 'Blog Status Mature', 'coreactivity' ),
			),
			'blog-status-not-mature'  => array(
				'label' => __( 'Blog Status Not Mature', 'coreactivity' ),
			),
		);
	}

	public function event_initialize_site( $new_site ) {
		$this->log( 'blog-created', array( 'object_id' => $new_site->blog_id ) );
	}

	public function event_uninitialize_site( $old_site ) {
		$this->log( 'blog-removed', array(
			'object_id' => $old_site->blog_id,
		), array(
			'blog_name' => get_blog_option( $old_site->blog_id, 'blogname' ),
			'blog_url'  => get_home_url( $old_site->blog_id ),
		) );
	}

	public function event_update_site( $new_site, $old_site ) {
		$meta = array(
			'old_domain' => $old_site->domain != $new_site->domain ? $old_site->domain : '',
			'old_path'   => $old_site->path != $new_site->path ? $old_site->path : '',
		);

		if ( ! empty( $meta['old_domain'] ) || ! empty( $meta['old_path'] ) ) {
			$this->log( 'blog-updated', array(
				'object_id' => $new_site->blog_id,
			), $meta );
		}
	}

	public function event_after_signup_site( $domain, $path, $title, $user, $user_email, $key, $meta ) {
		$this->log( 'blog-signup', array(), array(
			'domain'     => $domain,
			'path'       => $path,
			'title'      => $title,
			'user_name'  => $user,
			'user_email' => $user_email,
			'signup_key' => $key,
		) );
	}

	public function event_validate_blog_signup( $result ) {
		if ( isset( $results['errors'] ) && $results['errors'] instanceof WP_Error && $results['errors']->has_errors() ) {
			$user = $results['user'];

			$this->log( 'failed-blog-signup', array(), array(
				'domain'     => $results['domain'],
				'path'       => $results['path'],
				'blogname'   => $results['blogname'],
				'blog_title' => $results['blog_title'],
				'user'       => $user instanceof WP_User ? $user->ID : $user,
				'errors'     => $results['errors']->get_error_messages(),
			) );
		}

		return $result;
	}

	public function event_blog_public( $blog_id, $public_status ) {
		$event = $public_status == 1 ? 'blog-status-public' : 'blog-status-private';

		if ( $this->is_active( $event ) ) {
			$this->log( $event, array( 'object_id' => $blog_id ) );
		}
	}

	public function event_delete_blog( $blog_id ) {
		$this->log( 'blog-status-delete', array( 'object_id' => $blog_id ) );
	}

	public function event_undelete_blog( $blog_id ) {
		$this->log( 'blog-status-not-delete', array( 'object_id' => $blog_id ) );
	}

	public function event_archive_blog( $blog_id ) {
		$this->log( 'blog-status-archive', array( 'object_id' => $blog_id ) );
	}

	public function event_not_archive_blog( $blog_id ) {
		$this->log( 'blog-status-not-archive', array( 'object_id' => $blog_id ) );
	}

	public function event_spam_blog( $blog_id ) {
		$this->log( 'blog-status-spam', array( 'object_id' => $blog_id ) );
	}

	public function event_not_spam_blog( $blog_id ) {
		$this->log( 'blog-status-not-spam', array( 'object_id' => $blog_id ) );
	}

	public function event_mature_blog( $blog_id ) {
		$this->log( 'blog-status-mature', array( 'object_id' => $blog_id ) );
	}

	public function event_not_mature_blog( $blog_id ) {
		$this->log( 'blog-status-not-mature', array( 'object_id' => $blog_id ) );
	}
}
