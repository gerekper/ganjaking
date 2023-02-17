/*!
 * indexer-status.js
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
 * @since 4.11.0
 */

( function( $ ) {
	$( document ).ready( function() {
		if ( typeof woocommerce_product_search_status === 'undefined' ) {
			var woocommerce_product_search_status = {
				completed: 'Completed',
				running: 'Running',
				stopped: 'Stopped',
				warning: 'Warning',
				interval: 28999,
				long_interval: 58999
			};
		}
		var interval = woocommerce_product_search_status.interval;
		var long_interval = woocommerce_product_search_status.long_interval;
		var status_poller = function() {

			$( '.woocommerce-product-search-admin-bar-status' ).each( function() {
				var next_interval = interval;
				const id = $( this ).data( 'id' );
				const url = $( this ).data( 'url' );
				const jqxhr = $.post(
					url,
					{},

					function( data ) {
						var completed = typeof data.pct !== 'undefined' && data.pct >= 100;
						if ( typeof data.status !== 'undefined' ) {
							if ( data.status ) {
								$( '#' + id ).find( '.woocommerce-product-search-indexer-status' ).removeClass( 'status-warning status-stopped' );
								$( '#' + id ).find( '.woocommerce-product-search-indexer-status' ).addClass( 'status-running' );
								if ( !completed ) {
									$( '#' + id ).find( '.woocommerce-product-search-indexer-status' ).text( woocommerce_product_search_status.running );
									$( '#' + id ).find( '.woocommerce-product-search-indexer-status' ).attr( 'title', woocommerce_product_search_status.running );
								} else {
									$( '#' + id ).find( '.woocommerce-product-search-indexer-status' ).text( woocommerce_product_search_status.completed );
									$( '#' + id ).find( '.woocommerce-product-search-indexer-status' ).attr( 'title', woocommerce_product_search_status.completed );
									$( '#' + id ).find( '.woocommerce-product-search-indexer-status' ).addClass( 'status-completed' );
								}
							} else {
								$( '#' + id ).find( '.woocommerce-product-search-indexer-status' ).text( woocommerce_product_search_status.stopped );
								$( '#' + id ).find( '.woocommerce-product-search-indexer-status' ).attr( 'title', woocommerce_product_search_status.stopped );
								$( '#' + id ).find( '.woocommerce-product-search-indexer-status' ).removeClass( 'status-warning status-running status-completed' );
								$( '#' + id ).find( '.woocommerce-product-search-indexer-status' ).addClass( 'status-stopped' );
								if ( completed ) {
									$( '#' + id ).find( '.woocommerce-product-search-indexer-status' ).addClass( 'status-completed' );
								}
							}
						}
						if ( typeof data.percent !== 'undefined' ) {
							$( '#' + id ).find( '.woocommerce-product-search-indexer-percent' ).text( data.percent );
						}
						if ( completed ) {
							next_interval = long_interval;
						}
					},
					'json'
				).fail(
					function( jqXHR, textStatus, errorThrown ) {
						var errmsg = typeof errorThrown !== 'undefined' ? errorThrown : '?';
						var errstat = typeof jqXHR !== 'undefined' && typeof jqXHR.status !== 'undefined' ? ' [' + jqXHR.status + ']' : '';
						$( '#' + id ).find( '.woocommerce-product-search-indexer-status' ).text( woocommerce_product_search_status.warning );
						$( '#' + id ).find( '.woocommerce-product-search-indexer-status' ).attr( 'title', woocommerce_product_search_status.warning + ' : ' + errmsg + errstat );
						$( '#' + id ).find( '.woocommerce-product-search-indexer-status' ).removeClass( 'status-running' );
						$( '#' + id ).find( '.woocommerce-product-search-indexer-status' ).removeClass( 'status-stopped' );
						$( '#' + id ).find( '.woocommerce-product-search-indexer-status' ).removeClass( 'status-completed' );
						$( '#' + id ).find( '.woocommerce-product-search-indexer-status' ).addClass( 'status-warning' );
						console.log( 'Could not obtain the status: ' + errmsg + errstat );
					}
				);
				setTimeout( status_poller, next_interval );
			});
		};
		setTimeout( status_poller, interval );
	} );
} )( jQuery );
