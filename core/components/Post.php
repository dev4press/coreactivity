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
		$this->do_not_log = apply_filters( 'coreactivity_post_do_not_log_post_types', array( 'revision', 'attachment' ) );
	}

	public function label() : string {
		return __( "Posts", "coreactivity" );
	}
}
