<?php

namespace Dev4Press\Plugin\CoreActivity\Table;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class LogsBlog extends Logs {
	public function __construct( $args = array() ) {
		$this->_filter_lock[ 'blog_id' ] = get_current_blog_id();

		parent::__construct( $args );
	}
}
