<?php

namespace Dev4Press\Plugin\CoreActivity\Table;

use Dev4Press\v53\Core\Quick\Sanitize;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Live extends Logs {
	protected $_is_live_instance = true;

	public function __construct( $args = array() ) {
		parent::__construct( $args );

		$this->_request_args = array(
			'filter-blog_id'      => Sanitize::absint( $this->_request_args['filter-blog_id'] ),
			'filter-user_id'      => Sanitize::absint( $this->_request_args['filter-user_id'] ),
			'filter-event_id'     => Sanitize::absint( $this->_request_args['filter-event_id'] ),
			'filter-ip'           => Sanitize::text( $this->_request_args['filter-ip'] ),
			'filter-component'    => Sanitize::text( $this->_request_args['filter-component'] ),
			'filter-country_code' => strtoupper( Sanitize::slug( $this->_request_args['filter-country_code'] ) ),
			'filter-context'      => strtoupper( Sanitize::slug( $this->_request_args['filter-context'] ) ),
			'filter-method'       => strtoupper( Sanitize::slug( $this->_request_args['filter-method'] ) ),
			'filter-object_type'  => Sanitize::slug( $this->_request_args['filter-object_type'] ),
			'filter-object_id'    => Sanitize::absint( $this->_request_args['filter-object_id'] ),
			'filter-object_name'  => Sanitize::text( $this->_request_args['filter-object_name'] ),
			'view'                => sanitize_key( $this->_request_args['view'] ),
			'search'              => Sanitize::text( $this->_request_args['search'] ),
			'period'              => sanitize_key( $this->_request_args['period'] ),
			'orderby'             => Sanitize::text( $this->_request_args['orderby'] ),
			'order'               => strtoupper( Sanitize::slug( $this->_request_args['order'] ) ),
			'paged'               => Sanitize::absint( $this->_request_args['paged'] ),
			'min_id'              => Sanitize::absint( $this->_request_args['min_id'] ),
		);

		if ( ! in_array( $this->_request_args['orderby'], $this->_sanitize_orderby_fields ) ) {
			$this->_request_args['orderby'] = 'l.log_id';
		}

		if ( ! in_array( $this->_request_args['order'], array( 'ASC', 'DESC' ) ) ) {
			$this->_request_args['order'] = 'DESC';
		}

		$this->prepare_the_view();
	}

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
