<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

if ( class_exists( '\GroovyMenu\WalkerHelper' ) ) {
	new \GroovyMenu\WalkerHelper();
}
