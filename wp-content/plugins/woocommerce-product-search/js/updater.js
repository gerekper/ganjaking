/*!
 * updater.js
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
 * @since 1.4.0
 */

var wpsFtUpdater = {};

wpsFtUpdater.start = function( url, redirect ) {

	var $status  = jQuery( '#wps-ft-updater-status' ),
		$update  = jQuery( '#wps-ft-updater-update' ),
		$blinker = jQuery( '#wps-ft-updater-blinker' );

	$blinker.addClass( 'blinker' );
	$status.html( '<p>Database update is in progress &hellip;</p>' );

	jQuery( '.wps-ft-update-button' ).prop( 'disabled', true );

	jQuery.ajax( {
		type     : 'POST',
		url      : url,
		data     : {},
		complete : function() {
			wpsFtUpdater.generating = false;
			$blinker.removeClass( 'blinker' );
		},
		success  : function ( data ) {
			if ( typeof data.time !== 'undefined' ) {
				$update.html( '<p>Database update has been completed (' + data.time + 's).</p>' );
				if ( typeof data.errors !== "undefined" && Array.isArray( data.errors ) ) {
					$update.append(
						'<p style="color:#f00">' +
						data.errors.join( '<br/>' ) +
						'</p>'
					);
				}
				if ( typeof data.notices !== 'undefined' && Array.isArray( data.notices ) ) {
					$update.append(
						'<p style="color:#060">' +
						data.notices.join( '<br/>' ) +
						'</p>'
					);
				}
				if ( typeof redirect !== 'undefined' ) {
					$update.append( '<p>Click <a class="button button-primary" href="' + redirect + '">Continue</a> to proceed.</p>' );
					jQuery( 'input.button.button-primary' ).hide();
					jQuery( 'div.wps-performance-options' ).hide();
					jQuery( 'div.wps-ft-options' ).hide();
				}
			}
		},
		dataType : 'json'
	} );

};
