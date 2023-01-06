<?php
$wp_installer_instance = WCML_PLUGIN_PATH . '/vendor/otgs/installer/loader.php';

if ( file_exists( $wp_installer_instance ) ) {
	include_once $wp_installer_instance;

	WP_Installer_Setup( $wp_installer_instance, [ 'plugins_install_tab' => 1 ] );
}
