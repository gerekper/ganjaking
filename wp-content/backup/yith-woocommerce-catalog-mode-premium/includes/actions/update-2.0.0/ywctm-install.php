<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( defined( 'YWCTM_PREMIUM' ) && YWCTM_PREMIUM ) {

	if ( '' !== get_option( 'ywctm_enable_plugin' ) ) {
		include_once( YWCTM_DIR . 'includes/actions/update-2.0.0/ywctm-update-premium.php' );
	} else {
		include_once( YWCTM_DIR . 'includes/actions/update-2.0.0/ywctm-default-buttons.php' );
	}
} else {
	if ( '' !== get_option( 'ywctm_enable_plugin' ) ) {
		include_once( YWCTM_DIR . 'includes/actions/update-2.0.0/ywctm-update.php' );
	} else {
		update_option( 'ywctm_update_version', YWCTM_VERSION );
	}
}
