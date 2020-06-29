<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */


if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

return array(
	'vendor-payments' => array(
		'home' => array(
			'type'   => 'custom_tab',
			'action' => 'yith_wpv_edit_payments',
			'hide_sidebar' => true
		)
	)
);