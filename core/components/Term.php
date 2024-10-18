<?php

namespace Dev4Press\Plugin\CoreActivity\Components;

use Dev4Press\Plugin\CoreActivity\Base\Component;
use WP_Term;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Term extends Component {
	protected $plugin = 'coreactivity';
	protected $name = 'term';
	protected $object_type = 'term';
	protected $icon = 'ui-tags';
	protected $exceptions = array();
	protected $do_not_log = array();
	protected $storage = array();

	public function tracking() {
		if ( $this->is_active( 'created' ) ) {
			add_action( 'created_term', array( $this, 'event_created' ) );
		}

		if ( $this->is_active( 'deleted' ) ) {
			add_action( 'delete_term', array( $this, 'event_deleted' ), 10, 4 );
		}

		if ( $this->is_active( 'edited' ) ) {
			add_action( 'edit_terms', array( $this, 'prepare_edit' ) );
			add_action( 'edited_term', array( $this, 'event_edited' ) );
		}

		if ( $this->is_active( 'meta-added' ) ) {
			add_action( 'coreactivity_metas_added_term', array( $this, 'event_meta_added' ), 10, 3 );
		}

		if ( $this->is_active( 'meta-updated' ) ) {
			add_action( 'coreactivity_metas_updated_term', array( $this, 'event_meta_updated' ), 10, 4 );
		}

		if ( $this->is_active( 'meta-deleted' ) ) {
			add_action( 'coreactivity_metas_deleted_term', array( $this, 'event_meta_deleted' ), 10, 3 );
		}
	}

	public function init() {
		/**
		 * Filter the list of taxonomies not to log. Activity related to terms of the taxonomies on this list will not be tracked and logged.
		 *
		 * @param array $taxonomies name of the taxonomies not to log, by default is empty array.
		 *
		 * @return array array with names of taxonomies not to log.
		 */
		$this->do_not_log = apply_filters( 'coreactivity_term_do_not_log_taxonomies', array() );

		$this->exceptions = coreactivity_settings()->get( 'exceptions_term-meta_list' );
	}

	public function is_taxonomy_allowed( string $taxonomy ) : bool {
		return ! in_array( $taxonomy, $this->do_not_log );
	}

	public function label() : string {
		return __( 'Terms', 'coreactivity' );
	}

	protected function get_events() : array {
		return array(
			'created'      => array(
				'label' => __( 'Term Created', 'coreactivity' ),
			),
			'deleted'      => array(
				'label' => __( 'Term Deleted', 'coreactivity' ),
			),
			'edited'       => array(
				'label' => __( 'Term Edited', 'coreactivity' ),
			),
			'meta-added'   => array(
				'label'   => __( 'Term Meta Added', 'coreactivity' ),
				'status'  => 'inactive',
				'version' => '2.0',
			),
			'meta-updated' => array(
				'label'   => __( 'Term Meta Updated', 'coreactivity' ),
				'status'  => 'inactive',
				'version' => '2.0',
			),
			'meta-deleted' => array(
				'label'   => __( 'Term Meta Deleted', 'coreactivity' ),
				'status'  => 'inactive',
				'version' => '2.0',
			),
		);
	}

	public function logs_meta_column_keys( array $meta_column_keys ) : array {
		$meta_column_keys[ $this->code() ] = array(
			'-' => array(
				'term',
				'slug',
				'taxonomy',
			),
		);

		return $meta_column_keys;
	}

	public function prepare_edit( $term_id ) {
		$this->storage[ $term_id ] = get_term( $term_id );
	}

	public function event_created( $term_id ) {
		$term = get_term( $term_id );

		if ( $term instanceof WP_Term ) {
			if ( $this->is_taxonomy_allowed( $term->taxonomy ) ) {
				$this->log( 'created', array(
					'object_id' => $term->term_id,
				), array(
					'term'     => $term->name,
					'slug'     => $term->slug,
					'taxonomy' => $term->taxonomy,
					'parent'   => $term->parent,
				) );
			}
		}
	}

	public function event_deleted( $term_id, $tt_id, $taxonomy, $term ) {
		if ( $term instanceof WP_Term ) {
			if ( $this->is_taxonomy_allowed( $term->taxonomy ) ) {
				$this->log( 'deleted', array(
					'object_id' => $term->term_id,
				), array(
					'term'     => $term->name,
					'slug'     => $term->slug,
					'taxonomy' => $term->taxonomy,
					'parent'   => $term->parent,
				) );
			}
		}
	}

	public function event_edited( $term_id ) {
		$term = get_term( $term_id );

		if ( $term instanceof WP_Term && isset( $this->storage[ $term_id ] ) && $this->storage[ $term_id ] instanceof WP_Term ) {
			if ( $this->is_taxonomy_allowed( $term->taxonomy ) ) {
				$diff = $this->find_differences( (array) $this->storage[ $term_id ], (array) $term );

				if ( ! empty( $diff ) ) {
					$this->log( 'edited', array(
						'object_id' => $term->term_id,
					), $diff );
				}
			}
		}
	}

	public function event_meta_added( $object_id, $meta_key, $meta_value ) {
		if ( $this->is_exception( $meta_key ) ) {
			return;
		}

		$this->log( 'meta-added', array(
			'object_type' => 'term-meta',
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

		$args = array(
			'term_id'        => $object_id,
			'meta_key'       => $meta_key,
			'meta_value'     => $meta_value,
			'meta_value_old' => $old,
		);

		$this->log( 'meta-updated', array(
			'object_type' => 'term-meta',
			'object_name' => $meta_key,
		), $this->validate_old_new( $args, 'meta_value', 'meta_value_old' ) );
	}

	public function event_meta_deleted( $object_id, $meta_key, $meta_value ) {
		if ( $this->is_exception( $meta_key ) ) {
			return;
		}

		$this->log( 'meta-deleted', array(
			'object_type' => 'term-meta',
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
