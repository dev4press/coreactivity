<?php

namespace Dev4Press\Plugin\CoreActivity\Basic;

use Dev4Press\v42\Core\Plugins\Information as BaseInformation;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Information extends BaseInformation {
	public $code = 'coreactivity';

	public $version = '1.0';
	public $build = 1;
	public $edition = 'free';
	public $status = 'stable';
	public $updated = '2023.06.01';
	public $released = '2023.06.01';
}
