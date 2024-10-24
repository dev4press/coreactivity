<?php

namespace Dev4Press\Plugin\CoreActivity\Components;

use Dev4Press\Plugin\CoreActivity\Base\Component;
use WP_Comment;
use WP_Post;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Comment extends Component {
	protected $plugin = 'coreactivity';
	protected $name = 'comment';
	protected $object_type = 'comment';
	protected $icon = 'ui-comment-dots';
	protected $exceptions = array();
	protected $do_not_log = array();

	public function tracking() {
		if ( $this->is_active( 'status-change' ) ) {
			add_action( 'transition_comment_status', array( $this, 'event_transition_comment_status' ), 10, 3 );
		}

		if ( $this->is_active( 'deleted' ) ) {
			add_action( 'delete_comment', array( $this, 'event_delete_comment' ), 10, 2 );
		}

		if ( $this->is_active( 'meta-added' ) ) {
			add_action( 'coreactivity_metas_added_comment', array( $this, 'event_meta_added' ), 10, 3 );
		}

		if ( $this->is_active( 'meta-updated' ) ) {
			add_action( 'coreactivity_metas_updated_comment', array( $this, 'event_meta_updated' ), 10, 4 );
		}

		if ( $this->is_active( 'meta-deleted' ) ) {
			add_action( 'coreactivity_metas_deleted_comment', array( $this, 'event_meta_deleted' ), 10, 3 );
		}
	}

	public function init() {
		/**
		 * Filter the list of post types not to log for the Comments component. Comments belonging to post types on this list will not be tracked or logged.
		 *
		 * @param array $post_types name of the post types not to log, by default is empty array.
		 *
		 * @return array array with names of post types not to log.
		 */
		$this->do_not_log = apply_filters( 'coreactivity_comment_do_not_log_post_types', array() );

		$this->exceptions = coreactivity_settings()->get( 'exceptions_comment-meta_list' );
	}

	public function is_post_type_allowed( string $post_type ) : bool {
		return ! in_array( $post_type, $this->do_not_log );
	}

	public function label() : string {
		return __( 'Comments', 'coreactivity' );
	}

	protected function get_events() : array {
		return array(
			'status-change' => array(
				'label' => __( 'Comment Status Change', 'coreactivity' ),
			),
			'deleted'       => array(
				'label' => __( 'Comment Deleted', 'coreactivity' ),
			),
			'meta-added'    => array(
				'label'   => __( 'Comment Meta Added', 'coreactivity' ),
				'status'  => 'inactive',
				'version' => '2.0',
			),
			'meta-updated'  => array(
				'label'   => __( 'Comment Meta Updated', 'coreactivity' ),
				'status'  => 'inactive',
				'version' => '2.0',
			),
			'meta-deleted'  => array(
				'label'   => __( 'Comment Meta Deleted', 'coreactivity' ),
				'status'  => 'inactive',
				'version' => '2.0',
			),
		);
	}

	public function logs_meta_column_keys( array $meta_column_keys ) : array {
		$meta_column_keys[ $this->code() ] = array(
			'status-change' => array(
				'old_status',
				'new_status',
			),
			'deleted'       => array(
				'comment_status',
				'post_id',
				'comment_author_email',
				'comment_author_url',
			),
		);

		return $meta_column_keys;
	}

	public function event_transition_comment_status( $new_status, $old_status, $comment ) {
		if ( $comment instanceof WP_Comment ) {
			$post = get_post( $comment->comment_post_ID );

			if ( $post instanceof WP_Post && $this->is_post_type_allowed( $post->post_type ) ) {
				$this->log( 'status-change', array(
					'object_id' => $comment->comment_ID,
				), array(
					'old_status' => $old_status,
					'new_status' => $new_status,
				) );
			}
		}
	}

	public function event_delete_comment( $comment_id, $comment ) {
		if ( $comment instanceof WP_Comment ) {
			$post = get_post( $comment->comment_post_ID );

			if ( $post instanceof WP_Post && $this->is_post_type_allowed( $post->post_type ) ) {
				$this->log( 'deleted', array(
					'object_id' => $comment->comment_ID,
				), array(
					'post_id'              => $post->ID,
					'comment_status'       => $comment->comment_approved,
					'comment_user_id'      => $comment->user_id,
					'comment_author'       => $comment->comment_author,
					'comment_author_email' => $comment->comment_author_email,
					'comment_author_url'   => $comment->comment_author_url,
					'comment_author_ip'    => $comment->comment_author_IP,
				) );
			}
		}
	}

	public function event_meta_added( $object_id, $meta_key, $meta_value ) {
		if ( $this->is_exception( $meta_key ) ) {
			return;
		}

		$this->log( 'meta-added', array(
			'object_type' => 'comment-meta',
			'object_name' => $meta_key,
		), array(
			'user_id'    => $object_id,
			'meta_key'   => $meta_key,
			'meta_value' => $meta_value,
		) );
	}

	public function event_meta_updated( $object_id, $meta_key, $meta_value, $old ) {
		if ( $this->is_exception( $meta_key ) ) {
			return;
		}

		$equal = $meta_value === $old || json_encode( $meta_value ) === json_encode( $old );

		if ( ! $equal ) {
			$args = array(
				'comment_id'     => $object_id,
				'meta_key'       => $meta_key,
				'meta_value'     => $meta_value,
				'meta_value_old' => $old,
			);

			$this->log( 'meta-updated', array(
				'object_type' => 'comment-meta',
				'object_name' => $meta_key,
			), $this->validate_old_new( $args, 'meta_value', 'meta_value_old' ) );
		}
	}

	public function event_meta_deleted( $object_id, $meta_key, $meta_value ) {
		if ( $this->is_exception( $meta_key ) ) {
			return;
		}

		$this->log( 'meta-deleted', array(
			'object_type' => 'comment-meta',
			'object_name' => $meta_key,
		), array(
			'user_id'    => $object_id,
			'meta_key'   => $meta_key,
			'meta_value' => $meta_value,
		) );
	}

	private function is_exception( $option ) : bool {
		if ( ! empty( $this->exceptions ) && in_array( $option, $this->exceptions ) ) {
			return true;
		}

		return false;
	}
}
