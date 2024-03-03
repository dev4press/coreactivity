<?php

namespace Dev4Press\Plugin\CoreActivity\Plugins;

use Dev4Press\Plugin\CoreActivity\Base\Content;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WooCommerce extends Content {
	protected $plugin = 'coreactivity';
	protected $category = 'plugin';
	protected $name = 'woocommerce';
	protected $icon = 'logo-woo';
	protected $plugin_file = 'woocommerce/woocommerce.php';
	protected $version = '1.1';

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
		return __( 'WooCommerce', 'coreactivity' );
	}

	public function init_post_types( $post_types ) : array {
		return array_merge( $post_types, $this->_post_types() );
	}

	private function _post_types() : array {
		return array(
			'product',
			'product_variation',
			'shop_order',
			'shop_order_refund',
			'shop_coupon',
		);
	}
}
