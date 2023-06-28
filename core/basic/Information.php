<?php

namespace Dev4Press\Plugin\CoreActivity\Basic;

use Dev4Press\v43\Core\Plugins\Information as BaseInformation;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Information extends BaseInformation {
	public $code = 'coreactivity';

	public $version = '1.0';
	public $build = 100;
	public $edition = 'free';
	public $status = 'beta';
	public $updated = '2023.06.28';
	public $released = '2023.06.28';

	public $github_url = 'https://github.com/dev4press/coreactivity';
	public $wp_org_url = 'https://wordpress.org/plugins/coreactivity';
}
