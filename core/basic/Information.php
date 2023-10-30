<?php

namespace Dev4Press\Plugin\CoreActivity\Basic;

use Dev4Press\v44\Core\Plugins\Information as BaseInformation;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Information extends BaseInformation {
	public $code = 'coreactivity';

	public $version = '1.2';
	public $build = 1200;
	public $edition = 'free';
	public $status = 'stable';
	public $updated = '2023.10.30';
	public $released = '2023.09.06';

	public $github_url = 'https://github.com/dev4press/coreactivity';
	public $wp_org_url = 'https://wordpress.org/plugins/coreactivity';
}
