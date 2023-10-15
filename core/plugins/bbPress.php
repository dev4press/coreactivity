<?php

namespace Dev4Press\Plugin\CoreActivity\Plugins;

use Dev4Press\Plugin\CoreActivity\Base\Content;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class bbPress extends Content {
	protected $plugin = 'coreactivity';
	protected $category = 'plugin';
	protected $name = 'bbpress';
	protected $icon = 'logo-bbpress';
	protected $plugin_file = 'bbpress/bbpress.php';

	public function __construct() {
		parent::__construct();

		if ( $this->is_available() ) {
			add_filter( 'coreactivity_post_do_not_log_post_types', array( $this, 'init_post_types' ) );
		}
	}

	public function init() {
		$this->do_log = $this->_post_types();
	}

	public function label() : string {
		return __( 'bbPress', 'coreactivity' );
	}

	public function init_post_types( $post_types ) : array {
		return array_merge( $post_types, $this->_post_types() );
	}

	private function _post_types() : array {
		return array(
			bbp_get_forum_post_type(),
			bbp_get_topic_post_type(),
			bbp_get_reply_post_type(),
		);
	}
}
