<?php
/**
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package YITH WooCommerce Subscription
 * @since   2.0.0
 * @author  YITH
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit;
}

return array(
	'delivery' => array(
		'activities' => array(
			'type'         => 'custom_tab',
			'action'       => 'yith_ywsbs_delivery_schedules_tab',
			'hide_sidebar' => true,
		),
	),
);
