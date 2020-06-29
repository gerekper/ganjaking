<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );


require_once dirname( __FILE__ ) . '/vendor/autoload.php';


if ( class_exists( '\GroovyMenu\WidgetInMenu' ) ) {

	// Self start.
	$widget_in_menu = new \GroovyMenu\WidgetInMenu();

}
