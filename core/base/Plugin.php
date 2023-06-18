<?php

namespace Dev4Press\Plugin\CoreActivity\Base;

use Dev4Press\v42\Core\Quick\WPR;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

abstract class Plugin extends Component {
	protected $category = 'plugin';
	protected $plugin_file = '';

	public function is_available() : bool {
		return WPR::is_plugin_active( 'sweeppress/sweeppress.php' );
	}
}
