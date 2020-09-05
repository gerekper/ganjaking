<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

if ( ! WP_UNINSTALL_PLUGIN ) {
	exit();
}

/**
 * Since version 2.14.0 uninstall only works if the user wants to.
 *
 * @see Admin_Uninstall_Controller::uninstall()
 */