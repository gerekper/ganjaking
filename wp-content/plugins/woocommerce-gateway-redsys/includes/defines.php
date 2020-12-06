<?php

/*
* Copyright: (C) 2013 - 2021 José Conti
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! defined( 'REDSYS_PLUGIN_API_REDSYS_PATH' ) ) {
	define( 'REDSYS_PLUGIN_API_REDSYS_PATH', REDSYS_PLUGIN_PATH . 'includes/api-redsys/' );
}

if ( ! defined( 'REDSYS_PLUGIN_CLASS_PATH' ) ) {
	define( 'REDSYS_PLUGIN_CLASS_PATH', REDSYS_PLUGIN_PATH . 'classes/'  );
}

if ( ! defined( 'REDSYS_PLUGIN_METABOXES_PATH' ) ) {
	define( 'REDSYS_PLUGIN_METABOXES_PATH', REDSYS_PLUGIN_PATH . 'includes/metabox/' );
}

if ( ! defined( 'REDSYS_PLUGIN_STATUS_PATH' ) ) {
	define( 'REDSYS_PLUGIN_STATUS_PATH', REDSYS_PLUGIN_PATH . 'includes/woo-status/' );
}

if ( ! defined( 'REDSYS_PLUGIN_NOTICE_PATH' ) ) {
	define( 'REDSYS_PLUGIN_NOTICE_PATH', REDSYS_PLUGIN_PATH . 'includes/notices/' );
}

if ( ! defined( 'REDSYS_PLUGIN_DATA_PATH' ) ) {
	define( 'REDSYS_PLUGIN_DATA_PATH', REDSYS_PLUGIN_PATH . 'includes/data/' );
}

if ( ! defined( 'REDSYS_PLUGIN_DATA_URL' ) ) {
	define( 'REDSYS_PLUGIN_DATA_URL', REDSYS_PLUGIN_URL . 'includes/data/' );
}
