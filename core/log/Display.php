<?php

namespace Dev4Press\Plugin\CoreActivity\Log;

use BP_Groups_Group;
use Dev4Press\v43\Core\Mailer\Detection;
use GFAPI;
use stdClass;
use WP_Comment;
use WP_Post;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Display {
	public $php_errors;

	public function __construct() {
		add_filter( 'coreactivity_logs_field_render_object_name', array( $this, 'logs_object_name' ), 10, 2 );

		$this->php_errors = array(
			E_ERROR             => 'E_ERROR',
			E_WARNING           => 'E_WARNING',
			E_PARSE             => 'E_PARSE',
			E_NOTICE            => 'E_NOTICE',
			E_CORE_ERROR        => 'E_CORE_ERROR',
			E_CORE_WARNING      => 'E_CORE_WARNING',
			E_COMPILE_ERROR     => 'E_COMPILE_ERROR',
			E_COMPILE_WARNING   => 'E_COMPILE_WARNING',
			E_USER_ERROR        => 'E_USER_ERROR',
			E_USER_WARNING      => 'E_USER_WARNING',
			E_USER_NOTICE       => 'E_USER_NOTICE',
			E_STRICT            => 'E_STRICT',
			E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
			E_DEPRECATED        => 'E_DEPRECATED',
			E_USER_DEPRECATED   => 'E_USER_DEPRECATED',
			E_ALL               => 'E_ALL'
		);
	}

	public static function instance() : Display {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new Display();
		}

		return $instance;
	}

	public function logs_object_name( string $render, stdClass $item ) : string {
		if ( empty( $item->object_type ) ) {
			return '/';
		}

		switch ( $item->object_type ) {
			case 'theme':
				$render = $this->_display_theme( $item );
				break;
			case 'plugin':
				$render = $this->_display_plugin( $item );
				break;
			case 'notification':
				$render = $this->_display_notification( $item );
				break;
			case 'user':
				$render = $this->_display_user( $item );
				break;
			case 'post':
				$render = $this->_display_post( $item );
				break;
			case 'term':
				$render = $this->_display_term( $item );
				break;
			case 'comment':
				$render = $this->_display_comment( $item );
				break;
			case 'attachment':
				$render = $this->_display_attachment( $item );
				break;
			case 'bpgroup':
				$render = $this->_display_bpgroup( $item );
				break;
			case 'gform':
				$render = $this->_display_gform( $item );
				break;
			case 'phperror':
				$render = $this->php_errors[ $item->object_id ] ?? '/';
				break;
		}

		return $render;
	}

	private function _display_theme( stdClass $item ) : string {
		$render = '';

		if ( empty( $item->object_name ) ) {
			$render .= __( "Unknown Theme", "coreactivity" );
		} else {
			if ( isset( $item->meta[ 'theme_name' ] ) ) {
				$render .= '<span>' . esc_html__( "Theme", "coreactivity" ) . ': <strong>' . $item->meta[ 'theme_name' ] . '</strong></span><br/>';
			}
			$render .= '<span>' . esc_html__( "Directory", "coreactivity" ) . ': <strong>' . $item->object_name . '</span>';
			if ( isset( $item->meta[ 'theme_version' ] ) ) {
				$render .= '<br/><span>' . esc_html__( "Version", "coreactivity" ) . ': <strong>' . $item->meta[ 'theme_version' ] . '</strong></span>';
			}
		}

		return $render;
	}

	private function _display_plugin( stdClass $item ) : string {
		$render = '';

		if ( empty( $item->object_name ) ) {
			$render .= __( "Unknown Plugin", "coreactivity" );
		} else {
			$plugin = $item->meta[ 'plugin_title' ] ?? '';
			$render .= '<span>' . esc_html__( "Plugin", "coreactivity" ) . ': <strong>' . $plugin . '</strong></span>';
			$render .= '<br/><span>' . esc_html__( "File", "coreactivity" ) . ': <strong>' . $item->object_name . '</strong></span>';
			if ( isset( $item->meta[ 'plugin_version' ] ) ) {
				$render .= '<br/><span>' . esc_html__( "Version", "coreactivity" ) . ': <strong>' . $item->meta[ 'plugin_version' ] . '</strong></span>';
			}
		}

		return $render;
	}

	private function _display_notification( stdClass $item ) : string {
		$render = '';

		if ( empty( $item->object_name ) ) {
			$render .= __( "Unknown Notification Type", "coreactivity" );
		} else {
			$render .= '<strong>' . $item->object_name . '</strong>';

			$data = Detection::instance()->get_data( $item->object_name );

			if ( ! empty( $data ) ) {
				$render .= '<br/><span>' . esc_html__( "Source", "coreactivity" ) . ': <strong>' . $data[ 'source' ] . '</strong></span>';
				$render .= '<br/><span>' . esc_html__( "Name", "coreactivity" ) . ': <strong>' . $data[ 'label' ] . '</strong></span>';
			}
		}

		return $render;
	}

	private function _display_user( stdClass $item ) : string {
		$render = '';
		$meta   = array();

		if ( $item->object_id == 0 ) {
			$render = '<span>ID: <strong>0</strong> &middot; ' . esc_html__( "Invalid", "coreactivity" ) . '</span>';
		} else {
			$user = get_user_by( 'id', $item->object_id );

			$render .= '<span>ID: <strong>' . $item->object_id . '</strong> &middot; ';

			if ( ! $user ) {
				$render .= esc_html__( "Not Found", "coreactivity" );
			} else {
				$render .= $user->display_name;
			}
		}

		foreach ( array( 'login', 'password', 'username', 'email' ) as $meta_key ) {
			if ( isset( $item->meta[ $meta_key ] ) ) {
				$meta[] = '<strong>' . $meta_key . '</strong>: ' . $item->meta[ $meta_key ];
			}
		}

		if ( ! empty( $meta ) ) {
			$render .= '<br/>' . join( ' &middot; ', $meta );
		}

		return $render;
	}

	private function _display_post( stdClass $item ) : string {
		$render = '';

		$post = get_post( $item->object_id );

		if ( $post instanceof WP_Post ) {
			$render .= sprintf( __( "ID: %s &middot; Post: %s<br/>Post Type: %s", "coreactivity" ), '<strong>' . $post->ID . '</strong>', '<strong><a href="' . get_edit_post_link( $post ) . '">' . $post->post_title . '</a></strong>', '<strong>' . $post->post_type . '</strong>' );
		} else {
			$render .= __( "MISSING", "coreactivity" ) . ': <strong>' . $item->object_id . '</strong>';
		}

		return $render;
	}

	private function _display_comment( stdClass $item ) : string {
		$render = '';

		$comment = get_comment( $item->object_id );

		if ( $comment instanceof WP_Comment ) {
			$post = get_post( $comment->comment_post_ID );

			if ( $post instanceof WP_Post ) {
				$render .= sprintf( __( "ID: %s &middot; Author: %s<br/>Post: %s", "coreactivity" ), '<strong><a href="' . get_edit_comment_link( $comment->comment_ID ) . '">' . $comment->comment_ID . '</a></strong>', '<strong>' . $comment->comment_author . '</strong>', '<strong><a href="' . get_edit_post_link( $post ) . '">' . $post->post_title . '</a></strong>' );
			} else {
				$render .= sprintf( __( "ID: %s &middot; Author: %s<br/>Post: MISSING", "coreactivity" ), '<strong>' . get_edit_comment_link( $comment->comment_ID ) . '</strong>', '<strong>' . $post->post_type . '</strong>' );
			}
		} else {
			$render .= __( "MISSING", "coreactivity" ) . ': <strong>' . $item->object_id . '</strong>';
		}

		return $render;
	}

	private function _display_attachment( stdClass $item ) : string {
		$render = '';

		$post = get_post( $item->object_id );

		if ( $post instanceof WP_Post ) {
			$render .= sprintf( __( "ID: %s &middot; MIME/Type: %s<br/>Name: %s", "coreactivity" ), '<strong>' . $post->ID . '</strong>', '<strong>' . $post->post_mime_type . '</strong>', '<strong><a href="' . get_edit_post_link( $post ) . '">' . $post->post_title . '</a></strong>' );
		} else {
			$render .= __( "MISSING", "coreactivity" ) . ': <strong>' . $item->object_id . '</strong>';
		}

		return $render;
	}

	private function _display_bpgroup( stdClass $item ) : string {
		$render = '';

		if ( function_exists( 'groups_get_group' ) ) {
			$group = groups_get_group( $item->object_id );

			if ( $group instanceof BP_Groups_Group ) {
				$render .= sprintf( __( "ID: %s &middot; Slug: %s<br/>Name: %s", "coreactivity" ), '<strong>' . $group->id . '</strong>', '<strong>' . $group->slug . '</strong>', '<strong><a href="' . bp_get_group_permalink( $group ) . '">' . $group->name . '</a></strong>' );
			} else {
				$render .= __( "MISSING", "coreactivity" ) . ': <strong>' . $item->object_id . '</strong>';
			}
		} else {
			$render = __( "ID", "coreactivity" ) . ': <strong>' . $item->object_id . '</strong>';
		}

		return $render;
	}

	private function _display_gform( stdClass $item ) : string {
		$render = '';

		if ( class_exists( '\GFAPI' ) ) {
			if ( GFAPI::form_id_exists( $item->object_id ) ) {
				$form = GFAPI::get_form( $item->object_id );
				$url  = admin_url( '/admin.php?page=gf_edit_forms&id=' . $form[ 'id' ] );

				$render .= sprintf( __( "ID: %s &middot; Slug: %s<br/>Name: %s", "coreactivity" ), '<strong>' . $form[ 'id' ] . '</strong>', '<strong>' . $form[ 'form_slug' ] . '</strong>', '<strong><a href="' . $url . '">' . $form[ 'title' ] . '</a></strong>' );
			} else {
				$render .= __( "MISSING", "coreactivity" ) . ': <strong>' . $item->object_id . '</strong>';
			}
		} else {
			$render = __( "ID", "coreactivity" ) . ': <strong>' . $item->object_id . '</strong>';
		}

		return $render;
	}

	private function _display_term( stdClass $item ) : string {
		$render = '';

		$term = get_term( $item->object_id );

		if ( $term instanceof \WP_Term ) {
			$render .= sprintf( __( "ID: %s &middot; Term: %s<br/>Taxonomy: %s", "coreactivity" ), '<strong>' . $term->term_id . '</strong>', '<strong><a href="' . get_edit_term_link( $term ) . '">' . $term->name . '</a></strong>', '<strong>' . $term->taxonomy . '</strong>' );
		} else {
			$render .= __( "MISSING", "coreactivity" ) . ': <strong>' . $item->object_id . '</strong>';
		}

		return $render;
	}
}
