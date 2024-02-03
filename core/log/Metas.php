<?php

namespace Dev4Press\Plugin\CoreActivity\Log;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Metas {
	private array $scopes = array( 'post', 'comment', 'term', 'user' );
	private array $previous = array();

	public function __construct() {
		foreach ( $this->scopes as $scope ) {
			add_action( 'added_' . $scope . '_meta', array( $this, 'added_meta' ), 10, 4 );
			add_action( 'deleted_' . $scope . '_meta', array( $this, 'deleted_meta' ), 10, 4 );
			add_action( 'update_' . $scope . '_meta', array( $this, 'update_meta' ), 10, 4 );
			add_action( 'updated_' . $scope . '_meta', array( $this, 'updated_meta' ), 10, 4 );
		}
	}

	public static function instance() : Metas {
		static $instance = null;

		if ( ! isset( $instance ) ) {
			$instance = new Metas();
		}

		return $instance;
	}

	private function get_scope( $offset ) {
		$action = current_action();
		$minus  = $offset + 5;

		return substr( $action, $offset, strlen( $action ) - $minus );
	}

	public function added_meta( $mid, $object_id, $meta_key, $_meta_value ) {
		$scope = $this->get_scope( 6 );
	}

	public function deleted_meta( $meta_ids, $object_id, $meta_key, $_meta_value ) {
		$scope = $this->get_scope( 8 );
	}

	public function update_meta( $meta_id, $object_id, $meta_key, $_meta_value ) {
		$scope = $this->get_scope( 7 );
	}

	public function updated_meta( $meta_id, $object_id, $meta_key, $_meta_value ) {
		$scope = $this->get_scope( 8 );
	}
}
