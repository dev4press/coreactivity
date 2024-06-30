<?php

namespace Dev4Press\Plugin\CoreActivity\Basic;

use Dev4Press\v49\Core\Plugins\Information as BaseInformation;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Information extends BaseInformation {
	public $code = 'coreactivity';

	public $version = '2.3.5';
	public $build = 2350;
	public $edition = 'free';
	public $status = 'stable';
	public $updated = '2024.06.30';
	public $released = '2023.09.06';

	public $github_url = 'https://github.com/dev4press/coreactivity';
	public $wp_org_url = 'https://wordpress.org/plugins/coreactivity';
}
