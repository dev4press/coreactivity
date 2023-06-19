<?php

namespace Dev4Press\Plugin\CoreActivity\Components;

use Dev4Press\Plugin\CoreActivity\Base\Component;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Attachment extends Component {
	protected $plugin = 'coreactivity';
	protected $name = 'attachment';
	protected $object_type = 'attachment';
	protected $icon = 'file-archive';

	public function tracking() {
		if ( $this->is_active( 'uploaded' ) ) {
			add_action( 'add_attachment', array( $this, 'event_add_attachment' ) );
		}

		if ( $this->is_active( 'edited' ) ) {
			add_action( 'edit_attachment', array( $this, 'event_edit_attachment' ) );
		}

		if ( $this->is_active( 'deleted' ) ) {
			add_action( 'delete_attachment', array( $this, 'event_delete_attachment' ) );
		}
	}

	public function label() : string {
		return __( "Attachments", "coreactivity" );
	}

	protected function get_events() : array {
		return array(
			'uploaded' => array( 'label' => __( "Attachment Uploaded", "coreactivity" ) ),
			'edited'   => array( 'label' => __( "Attachment Edited", "coreactivity" ) ),
			'deleted'  => array( 'label' => __( "Attachment Deleted", "coreactivity" ) )
		);
	}

	public function event_add_attachment( $attachment_id ) {
		$this->log( 'uploaded', array( 'object_id' => $attachment_id ), $this->_attachment_meta( $attachment_id ) );
	}

	public function event_edit_attachment( $attachment_id ) {
		$this->log( 'edited', array( 'object_id' => $attachment_id ), $this->_attachment_meta( $attachment_id ) );
	}

	public function event_delete_attachment( $attachment_id ) {
		$this->log( 'deleted', array( 'object_id' => $attachment_id ), $this->_attachment_meta( $attachment_id ) );
	}

	private function _attachment_meta( $attachment_id ) : array {
		$file = get_attached_file( $attachment_id );

		$meta = array(
			'attachment_filename' => basename( $file ),
			'attachment_mime'     => get_post_mime_type( $attachment_id ),
			'attachment_size'     => file_exists( $file ) ? filesize( $file ) : ''
		);

		$parent_id = wp_get_post_parent_id( $attachment_id );

		if ( $parent_id > 0 ) {
			$meta[ 'parent_post_id' ] = $parent_id;
		}

		return $meta;
	}
}
