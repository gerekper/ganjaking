/*!
 * settings.js
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
 * @since 5.0.0
 */

( function( $ ) {
	$( document ).ready( function() {

		$( 'table.woocommerce-product-search-cache tbody' ).sortable( {
				items: 'tr:not(.locked)',
				cursor: 'move',
				axis: 'y',
				handle: 'td.sort',
				scrollSensitivity: 40,
				helper: function( event, ui ) {
					ui.children().each( function() {
						$( this ).width( $( this ).width() );
					}
				);
				ui.css( 'left', '0' );
				return ui;
			},
			start: function( event, ui ) {
				ui.item.css( 'background-color', '#f6f6f6' );
			},
			stop: function( event, ui ) {
				ui.item.removeAttr( 'style' );
				ui.item.trigger( 'updateMoveButtons' );
			}
		} );
		function bytesForHumans( bytes ) {
			var suffixes = [ 'B', 'KB', 'MB', 'GB', 'TB', 'PB' ];

			for ( var i = 0; i < suffixes.length - 1; i++ ) {
				if ( bytes < Math.pow( 1024, i + 1) ) {
					break;
				}
			}
			var human = i == 0 ? '' + bytes : ( bytes * 1.0 / Math.pow( 1024, i )).toFixed(2);

			return human.replace( /\B(?=(\d{3})+(?!\d))/g, ',' ) + ' ' + suffixes[i];
		}

		function updateStatusBox( element ) {
			var url = $( element ).data( 'url' );
			var target = $( element );
			var nonce = $( element ).data( 'nonce' );
			if ( typeof window.wpApiSettings !== 'undefined' && typeof window.wpApiSettings.nonce !== 'undefined' ) {
				nonce = window.wpApiSettings.nonce;
			}
			target.addClass( 'blinker' );
			$.ajax( {
				url : url,
				method : 'GET',
				timeout : woocommerce_product_search_settings.timeout,
				data : { _wpnonce : nonce },
				complete : function ( jqXHR, textStatus ) {
					target.removeClass( 'blinker' );
					setTimeout( updateStatusBox, woocommerce_product_search_settings.interval, element );
				},
				success : function ( data, textStatus, jqXHR ) {
					var text = '<div class="storage-status-display">';
					if ( typeof data.max_files !== 'undefined' && data.max_files > 0 ) {
						if ( typeof data.count !== 'undefined' ) {
							let formatted_max_files = ('' + data.max_files).replace( /\B(?=(\d{3})+(?!\d))/g, ',' );

							text +=
								'<div>' +
								woocommerce_product_search_settings.number_of_cache_files + ' : ' +
								woocommerce_product_search_settings.x_of_y.replace( '%1$s', data.count ).replace( '%2$s', formatted_max_files ) +
								'</div>';
						}
					}
					if ( typeof data.max_size !== 'undefined' && data.max_size > 0 ) {
						if ( typeof data.size !== 'undefined' ) {

							text +=
								'<div>' +
								woocommerce_product_search_settings.size_of_cache_files + ' : ' +
								woocommerce_product_search_settings.x_of_y.replace( '%1$s', bytesForHumans( data.size ) ).replace( '%2$s', bytesForHumans( data.max_size ) ) +
								'</div>';
						}
					}
					if ( typeof data.free !== 'undefined' && typeof data.total !== 'undefined' ) {
						if ( data.free !== null && data.total !== null ) {
							text +=
								'<div>' +
								woocommerce_product_search_settings.storage_space + ' : ' +
								woocommerce_product_search_settings.free_of.replace( '%1$s', bytesForHumans( data.free ) ).replace( '%2$s', bytesForHumans( data.total ) )
								'</div>';
						} else {
							text +=
								'<div>' +
								'<span class="warning storage-space-warning">' + woocommerce_product_search_settings.storage_space_info_unavailable + '</span>' +
								'</div>';
							if ( typeof data.infos !== 'undefined' && data.infos.length > 0 ) {
								text += '<ul class="infos">';
								for ( let i = 0; i < data.infos.length; i++ ) {
									text += '<li class="info">' + data.infos[i] + '</li>';
								}
								text += '</ul>';
							}
						}
					}
					if ( typeof data.free_disk_space !== 'undefined' && data.free_disk_space !== null ) {
						text += '<div>';
						text += woocommerce_product_search_settings.free_storage_space + ' : ' + Number.parseFloat( data.free_disk_space ).toFixed(2) + ' %';
						if (
							typeof data.min_free_disk_space !== 'undefined' && typeof data.unit !== 'undefined' && data.unit === 'percent' && data.free_disk_space < data.min_free_disk_space ||
							typeof data.free !== 'undefined' && data.unit !== 'percent' && data.free < data.min_free_disk_space
						) {
							text += ' ' + '<span title="' + woocommerce_product_search_settings.storage_space_exhausted + '" class="warning storage-space-warning">' + woocommerce_product_search_settings.warning + '</span>';
						}
						text += '</div>';
					}
					if ( typeof data.min_free_disk_space !== 'undefined' && typeof data.unit !== 'undefined' ) {
						switch ( data.unit ) {
							case 'percent':
								text += '<div>' + woocommerce_product_search_settings.minimum_free_storage_space + ' : ' + data.min_free_disk_space + ' %' + '</div>';
								break;
							default:
								text += '<div>' + woocommerce_product_search_settings.minimum_free_storage_space + ' : ' + bytesForHumans( data.min_free_disk_space ) + '</div>';
						}
					}
					text += '</div>'; // .storage-status-display
					target.html( text );
				},
				error : function ( jqXHR, textStatus, errorThrown ) {
					target.addClass( 'status-fail' );
					if ( errorThrown ) {
						target.attr( 'title', errorThrown );
					}
				}
			} );
		}
		$( '.wps-cache-status-box' ).each(
			function( index, element ) {
				updateStatusBox( element );
			}
		);

		$( '.wps-cache-flush' ).click(
			function( event ) {
				event.preventDefault();
				event.stopPropagation();
				var target = $( event.target );
				var url = target.data( 'url' );
				var nonce = target.data( 'nonce' );
				if ( typeof window.wpApiSettings !== 'undefined' && typeof window.wpApiSettings.nonce !== 'undefined' ) {
					nonce = window.wpApiSettings.nonce;
				}
				target.addClass( 'blinker' );
				target.removeClass( 'status-ok' );
				target.removeClass( 'status-fail' );
				target.attr( 'title', '' );

				url += ( url.indexOf('?') === -1 ? '?' : '&' ) + $.param( { _wpnonce : nonce } );
				$.ajax( {
					url : url,
					method : 'DELETE',
					complete : function ( jqXHR, textStatus ) {
						target.removeClass( 'blinker' );
					},
					success : function ( data, textStatus, jqXHR ) {
						var flush = false;
						if ( typeof data.flush !== 'undefined' ) {
							flush = data.flush;
						}
						if ( flush ) {
							target.addClass( 'status-ok' );
						} else {
							target.addClass( 'status-fail' );
						}
					},
					error : function ( jqXHR, textStatus, errorThrown ) {
						target.addClass( 'status-fail' );
						if ( errorThrown ) {
							target.attr( 'title', errorThrown );
						}
					}
				} );
			}
		);

		$( '.wps-cache-gc' ).click(
			function( event ) {
				event.preventDefault();
				event.stopPropagation();
				var target = $( event.target );
				var url = target.data( 'url' );
				var nonce = target.data( 'nonce' );
				if ( typeof window.wpApiSettings !== 'undefined' && typeof window.wpApiSettings.nonce !== 'undefined' ) {
					nonce = window.wpApiSettings.nonce;
				}
				target.addClass( 'blinker' );
				target.removeClass( 'status-ok' );
				target.removeClass( 'status-fail' );
				target.attr( 'title', '' );

				url += ( url.indexOf('?') === -1 ? '?' : '&' ) + $.param( { _wpnonce : nonce } );
				$.ajax( {
					url : url,
					method : 'DELETE',
					complete : function ( jqXHR, textStatus ) {
						target.removeClass( 'blinker' );
					},
					success : function ( data, textStatus, jqXHR ) {
						var gc = false;
						if ( typeof data.gc !== 'undefined' ) {
							gc = data.gc;
						}
						if ( gc ) {
							target.addClass( 'status-ok' );
						} else {
							target.addClass( 'status-fail' );
						}
					},
					error : function ( jqXHR, textStatus, errorThrown ) {
						target.addClass( 'status-fail' );
						if ( errorThrown ) {
							target.attr( 'title', errorThrown );
						}
					}
				} );
			}
		);
	} );
} )( jQuery );
