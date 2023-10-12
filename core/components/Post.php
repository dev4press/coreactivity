<?php

namespace Dev4Press\Plugin\CoreActivity\Components;

use Dev4Press\Plugin\CoreActivity\Base\Content;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Post extends Content {
	protected $plugin = 'coreactivity';
	protected $name = 'post';
	protected $icon = 'ui-memo-pad';

	public function init() {
		/**
		 * Filter the list of post types not to log for the Posts component. Posts belonging to post types on this list will not be tracked or logged.
		 *
		 * @param array $post_types name of the post types not to log
		 *
		 * @return array array with names of post types not to log.
		 */
		$this->do_not_log = apply_filters( 'coreactivity_post_do_not_log_post_types', array( 'revision', 'attachment', 'shop_order_placehold' ) );
	}

	public function label() : string {
		return __( 'Posts', 'coreactivity' );
	}
}
