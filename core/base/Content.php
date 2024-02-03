<?php

namespace Dev4Press\Plugin\CoreActivity\Base;

use Dev4Press\Plugin\CoreActivity\Basic\Helper;
use WP_Post;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

abstract class Content extends Component {
	protected $object_type = 'post';

	protected $do_not_log = array();
	protected $do_log = array();
	protected $skip_statuses = array( 'auto-draft' );

	public function tracking() {
		if ( $this->is_active( 'status-change' ) ) {
			add_action( 'transition_post_status', array( $this, 'event_transition_post_status' ), 10, 3 );
		}

		if ( $this->is_active( 'deleted' ) ) {
			add_action( 'delete_post', array( $this, 'event_delete_post' ), 10, 2 );
		}

		if ( $this->is_active( 'term-relationship-change' ) ) {
			add_action( 'set_object_terms', array( $this, 'event_object_terms' ), 10, 6 );
		}
	}

	public function is_post_allowed( string $post_type, string $post_status = '' ) : bool {
		if ( ! empty( $post_status ) ) {
			if ( in_array( $post_status, $this->skip_statuses ) ) {
				return false;
			}
		}

		if ( empty( $this->do_log ) ) {
			return ! in_array( $post_type, $this->do_not_log );
		} else {
			return in_array( $post_type, $this->do_log );
		}
	}

	public function event_transition_post_status( $new_status, $old_status, $post ) {
		if ( $post instanceof WP_Post && $this->is_post_allowed( $post->post_type, $post->post_status ) ) {
			if ( $old_status != $new_status ) {
				$this->log( 'status-change', array(
					'object_id' => $post->ID,
				), array(
					'old_status' => $old_status,
					'new_status' => $new_status,
				) );
			}
		}
	}

	public function event_delete_post( $post_id, $post ) {
		if ( $post instanceof WP_Post && $this->is_post_allowed( $post->post_type, $post->post_status ) ) {
			$this->log( 'deleted', array(
				'object_id' => $post->ID,
			), array(
				'post_type'   => $post->post_type,
				'post_status' => $post->post_status,
				'post_name'   => $post->post_name,
				'post_title'  => $post->post_title,
			) );
		}
	}

	public function event_object_terms( $object_id, $terms, $tt_ids, $taxonomy, $append, $old_tt_ids ) {
		$post = get_post( $object_id );

		if ( $post instanceof WP_Post && $this->is_post_allowed( $post->post_type, $post->post_status ) ) {
			$old_terms = Helper::get_term_ids_from_taxonomy_term_ids( $old_tt_ids );
			$new_terms = Helper::get_term_ids_from_taxonomy_term_ids( $tt_ids );

			if ( ! empty( $old_terms ) || ! empty( $new_terms ) ) {
				if ( $old_terms != $new_terms ) {
					$this->log( 'term-relationship-change', array(
						'object_id' => $object_id,
					), array(
						'taxonomy'      => $taxonomy,
						'old_terms_ids' => $old_terms,
						'new_terms_ids' => $new_terms,
					) );
				}
			}
		}
	}

	protected function get_events() : array {
		return array(
			'status-change'            => array(
				'label' => __( 'Post Status Change', 'coreactivity' ),
			),
			'deleted'                  => array(
				'label' => __( 'Post Deleted', 'coreactivity' ),
			),
			'term-relationship-change' => array(
				'label' => __( 'Post Term Relationship Changes', 'coreactivity' ),
			),
			'meta-added'               => array(
				'label'  => __( 'Post Meta Added', 'coreactivity' ),
				'status' => 'inactive',
				'version' => '2.0',
			),
			'meta-updated'             => array(
				'label'  => __( 'Post Meta Updated', 'coreactivity' ),
				'status' => 'inactive',
				'version' => '2.0',
			),
			'meta-deleted'             => array(
				'label'  => __( 'Post Meta Deleted', 'coreactivity' ),
				'status' => 'inactive',
				'version' => '2.0',
			),
		);
	}
}
