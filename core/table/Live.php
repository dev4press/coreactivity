<?php

namespace Dev4Press\Plugin\CoreActivity\Table;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Live extends Logs {
	protected function process_request_args() {
	}

	public function update( $args, $lock ) {
		$this->_request_args = $args;
		$this->_filter_lock  = $lock;

		$this->prepare_the_view();
	}

	public function display() {
		if ( $this->has_items() ) {
			$this->display_rows_or_placeholder();
		}
	}

	protected function _admin() {
		return coreactivity_admin();
	}

	protected function get_row_classes( $item, $classes = array() ) : array {
		$classes[] = 'coreactivity-live-row';

		return parent::get_row_classes( $item, $classes );
	}
}