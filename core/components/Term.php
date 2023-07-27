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
	}

	public function is_taxonomy_allowed( string $taxonomy ) : bool {
		return ! in_array( $taxonomy, $this->do_not_log );
	}

	public function label() : string {
		return __( "Terms", "coreactivity" );
	}

	protected function get_events() : array {
		return array(
			'created' => array( 'label' => __( "Term Created", "coreactivity" ) ),
			'deleted' => array( 'label' => __( "Term Deleted", "coreactivity" ) ),
			'edited'  => array( 'label' => __( "Term Edited", "coreactivity" ) )
		);
	}

	public function prepare_edit( $term_id ) {
		$this->storage[ $term_id ] = get_term( $term_id );
	}

	public function event_created( $term_id ) {
		$term = get_term( $term_id );

		if ( $term instanceof WP_Term ) {
			if ( $this->is_taxonomy_allowed( $term->taxonomy ) ) {
				$this->log( 'created', array(
					'object_id' => $term->term_id
				), array(
					'term'     => $term->name,
					'slug'     => $term->slug,
					'taxonomy' => $term->taxonomy,
					'parent'   => $term->parent
				) );
			}
		}
	}

	public function event_deleted( $term_id, $tt_id, $taxonomy, $term ) {
		if ( $term instanceof WP_Term ) {
			if ( $this->is_taxonomy_allowed( $term->taxonomy ) ) {
				$this->log( 'deleted', array(
					'object_id' => $term->term_id
				), array(
					'term'     => $term->name,
					'slug'     => $term->slug,
					'taxonomy' => $term->taxonomy,
					'parent'   => $term->parent
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
						'object_id' => $term->term_id
					), $diff );
				}
			}
		}
	}
}
