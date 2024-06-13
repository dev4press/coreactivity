<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function dev4press_plugin_coreactivity_autoload( $class ) {
	$path = __DIR__ . '/';
	$base = 'Dev4Press\\Plugin\\CoreActivity\\';

	dev4press_v49_autoload_for_plugin( $class, $base, $path );
}

spl_autoload_register( 'dev4press_plugin_coreactivity_autoload' );
