/**
 * product-search-admin.js
 *
 * Copyright (c) "kento" Karim Rahimpur www.itthinx.com
 *
 * This code is provided subject to the license granted.
 * Unauthorized use and distribution is prohibited.
 * See COPYRIGHT.txt and LICENSE.txt
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * This header and all notices must be kept intact.
 *
 * @author itthinx
 * @package woocommerce-product-search
 * @since 1.1.0
 */

jQuery( document ).ready( function(){
	jQuery( '#wps-faq-help-trigger' ).show();
	jQuery( '#wps-faq-help-trigger' ).on( 'click', function( e ){
		e.preventDefault();
		jQuery( '#contextual-help-link' ).trigger( 'click' );
		jQuery( '#tab-link-woocommerce_product_search_tab a' ).trigger( 'click' );
	} );
} );
