<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package YITH Custom ThankYou Page for Woocommerce
 */

/* check for security check and action*/
if ( isset($_GET['secure_check']) && $_GET['secure_check'] === 'yctpw_sec_check' && isset($_GET['pdf']) && isset($_GET['file_name']) ) { //phpcs:ignore
    $file_name = $_GET['file_name']; //phpcs:ignore

	if ( empty( $file_name ) ) {
		$file_name = 'yctpw.pdf';
	}

	if ( substr( $file_name, -4, 4 ) !== '.pdf' ) {
		$file_name = $file_name . '.pdf';
	}

	header( 'Content-Type: application/pdf' );
	header( 'Content-Disposition: attachment; filename="' . $file_name . '"' );
	if ( strpos($_GET['pdf'],'.pdf') ) { //phpcs:ignore
		echo file_get_contents($_GET['pdf']); //phpcs:ignore
		unlink($_GET['pdf']);//phpcs:ignore
	}
}