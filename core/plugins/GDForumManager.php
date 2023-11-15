<?php

namespace Dev4Press\Plugin\CoreActivity\Plugins;

use Dev4Press\Plugin\CoreActivity\Base\Component;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class GDForumManager extends Component {
	protected $plugin = 'coreactivity';
	protected $category = 'plugin';
	protected $version = '1.4';
	protected $name = 'gd-forum-manager';
	protected $icon = 'plugin-gd-forum-manager-for-bbpress';
	protected $plugin_file = 'gd-forum-manager-for-bbpress/gd-forum-manager-for-bbpress.php';

	public function tracking() {
		if ( $this->is_active( 'forum-edit' ) ) {
			add_action( 'gdfar_ajax_edit_forum_process_end', array( $this, 'event_forum_edit' ), 10, 3 );
		}

		if ( $this->is_active( 'forum-bulk' ) ) {
			add_action( 'gdfar_ajax_bulk_forum_process_end', array( $this, 'event_forum_bulk' ), 10, 3 );
		}

		if ( $this->is_active( 'topic-edit' ) ) {
			add_action( 'gdfar_ajax_edit_topic_process_end', array( $this, 'event_topic_edit' ), 10, 3 );
		}

		if ( $this->is_active( 'topic-bulk' ) ) {
			add_action( 'gdfar_ajax_bulk_topic_process_end', array( $this, 'event_topic_bulk' ), 10, 3 );
		}
	}

	public function label() : string {
		return __( 'GD Forum Manager', 'coreactivity' );
	}

	protected function get_events() : array {
		return array(
			'forum-edit' => array(
				'label' => __( 'Forum Edit', 'coreactivity' ),
			),
			'forum-bulk' => array(
				'label' => __( 'Forum Bulk Edit', 'coreactivity' ),
			),
			'topic-edit' => array(
				'label' => __( 'Topic Edit', 'coreactivity' ),
			),
			'topic-bulk' => array(
				'label' => __( 'Topic Bulk Edit', 'coreactivity' ),
			),
		);
	}

	public function event_forum_edit( $data, $results, $changes ) {
		$object = array(
			'object_type' => 'post',
			'object_id'   => $data['id'],
		);
		$meta   = $this->process_input_edit( $data, $results, $changes );

		$this->log( 'forum-edit', $object, $meta );
	}

	public function event_forum_bulk( $data, $results, $changes ) {
		$meta = $this->process_input_bulk( $data, $results, $changes );

		$this->log( 'forum-bulk', array(), $meta );
	}

	public function event_topic_edit( $data, $results, $changes ) {
		$object = array(
			'object_type' => 'post',
			'object_id'   => $data['id'],
		);
		$meta   = $this->process_input_edit( $data, $results, $changes );

		$this->log( 'topic-edit', $object, $meta );
	}

	public function event_topic_bulk( $data, $results, $changes ) {
		$meta = $this->process_input_bulk( $data, $results, $changes );

		$this->log( 'topic-bulk', array(), $meta );
	}

	private function process_input_edit( $data, $results, $changes ) : array {
		$meta = array(
			'post_type' => $data['type'] == 'topic' ? bbp_get_topic_post_type() : bbp_get_forum_post_type(),
			'input'     => array(),
			'changes'   => $changes,
		);

		if ( ! empty( $results ) ) {
			if ( is_wp_error( $results ) ) {
				$meta['errors'] = $results->get_error_message();
			} else {
				$meta['errors'] = $results;
			}
		}

		foreach ( $data['field'] as $key => $args ) {
			foreach ( $args as $name => $value ) {
				$meta['input'][ $key . '::' . $name ] = $value;
			}
		}

		return $meta;
	}

	private function process_input_bulk( $data, $results, $changes ) : array {
		return $this->process_input_edit( $data, $results, $changes );
	}
}
