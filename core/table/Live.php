<?php

namespace Dev4Press\Plugin\CoreActivity\Table;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Live extends Logs {
	protected $_is_live_instance = true;

	protected function process_request_args() {
	}

	public function update() {
	}

	public function display() {
		if ( $this->has_items() ) {
			$this->display_rows_or_placeholder();
		}
	}

	protected function _admin() {
		return coreactivity_admin();
	}

	protected function rows_per_page() : int {
		return 100;
	}

	protected function get_row_classes( $item, $classes = array() ) : array {
		$classes[] = 'coreactivity-live-row';

		return parent::get_row_classes( $item, $classes );
	}
}
