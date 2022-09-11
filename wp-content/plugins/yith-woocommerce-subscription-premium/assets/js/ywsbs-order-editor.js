/**
 * ywsbs-order-editor.js
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Subscription
 * @version 2.0.0
 */
/* global ywsbs_order_admin */
jQuery(function ($) {

	/**
	 * ORDER EDITOR TITLE
	 */
	if ( $( document ).find( '.woocommerce-order-data__meta' ).length > 0 ) {
		$( '<div class="ywsbs-order-label">' + ywsbs_order_admin.order_label + '</div>' ).insertBefore( '.woocommerce-order-data__meta' );
	}


});