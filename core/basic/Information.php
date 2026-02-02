<?php

namespace Dev4Press\Plugin\CoreActivity\Basic;

use Dev4Press\v55\Core\Plugins\Information as BaseInformation;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Information extends BaseInformation {
	public string $code = 'coreactivity';

	public string $version = '3.0';
	public int $build = 3000;
	public string $edition = 'free';
	public string $status = 'stable';
	public string $updated = '2026.02.11';
	public string $released = '2023.09.06';

	public string $github_url = 'https://github.com/dev4press/coreactivity';
	public string $wp_org_url = 'https://wordpress.org/plugins/coreactivity';
}
