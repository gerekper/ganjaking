/*!
 * indexer.js
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
 * @since 2.0.0
 */

var wpsIndexer = {},
	wpsIndexerStatus = {
		running : true,
		polling : false
	};

wpsIndexer.start = function( url, redirect ) {

	var $status  = jQuery( '#wps-index-status' ),
		$update  = jQuery( '#wps-index-update' ),
		$blinker = jQuery( '#wps-index-blinker' );

	$blinker.addClass( 'blinker' );
	$status.html( this.msg_starting );

	jQuery( '.wps-index-start-button' ).prop( 'disabled', true );
	jQuery( '#wps_index_rebuild' ).prop( 'disabled', true );

	jQuery.ajax( {
		type     : 'POST',
		url      : url,
		data     : {},
		complete : function() {
			wpsIndexer.started = true;
			$blinker.removeClass( 'blinker' );
		},
		success  : function ( data ) {
			jQuery( '#wps_index_stop' ).prop( 'disabled', false );
			jQuery( '#wps_index_rebuild' ).prop( 'disabled', false );
			if ( typeof data.time !== 'undefined' ) {
				$update.html( wpsIndexer.msg_started );
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
			}
		},
		dataType : 'json'
	} );

};

wpsIndexer.stop = function( url, redirect ) {

	var $status  = jQuery( '#wps-index-status' ),
		$update  = jQuery( '#wps-index-update' ),
		$blinker = jQuery( '#wps-index-blinker' );

	$blinker.addClass( 'blinker' );
	$status.html( this.msg_stopping );

	jQuery( '.wps-index-stop-button' ).prop( 'disabled', true );
	jQuery( '#wps_index_rebuild' ).prop( 'disabled', true );

	jQuery.ajax( {
		type     : 'POST',
		url      : url,
		data     : {},
		complete : function() {
			wpsIndexer.started = false;
			$blinker.removeClass( 'blinker' );
		},
		success  : function ( data ) {
			jQuery( '#wps_index_start' ).prop( 'disabled', false );
			jQuery( '#wps_index_rebuild' ).prop( 'disabled', false );
			if ( typeof data.time !== 'undefined' ) {
				$update.html( wpsIndexer.msg_stopped );
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
			}
		},
		dataType : 'json'
	} );
};

wpsIndexer.rebuild = function( url, redirect ) {

	var $status  = jQuery( '#wps-index-status' ),
		$update  = jQuery( '#wps-index-update' ),
		$blinker = jQuery( '#wps-index-blinker' );

	$blinker.addClass( 'blinker' );
	$status.html( this.msg_rebuilding );

	jQuery( '.wps-index-rebuild-button' ).prop( 'disabled', true );
	jQuery( '.wps-index-start-button' ).prop( 'disabled', true );
	jQuery( '.wps-index-stop-button' ).prop( 'disabled', true );

	jQuery( '#wps-index-status-display' ).html( '&mdash;' );
	jQuery( '#wps-index-status-total' ).html( '&mdash;' );
	jQuery( '#wps-index-status-processable' ).html( '&mdash;' );
	jQuery( '#wps-index-status-next-scheduled' ).html( '&mdash;' );

	jQuery.ajax( {
		type     : 'POST',
		url      : url,
		data     : {},
		complete : function() {
			wpsIndexer.started = true;
			$blinker.removeClass( 'blinker' );
		},
		success  : function ( data ) {
			jQuery( '.wps-index-rebuild-button' ).prop( 'disabled', false );
			jQuery( '.wps-index-stop-button' ).prop( 'disabled', false );
			if ( typeof data.time !== 'undefined' ) {
				$update.html( wpsIndexer.msg_rebuilt );
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
			}
		},
		dataType : 'json'
	} );
};

wpsIndexer.run_once = function( url, redirect ) {

	var $status  = jQuery( '#wps-index-status' ),
		$update  = jQuery( '#wps-index-update' ),
		$blinker = jQuery( '#wps-index-blinker' );

	$blinker.addClass( 'blinker' );
	$status.html( this.msg_run );

	jQuery( '.wps-index-run-button' ).prop( 'disabled', true );

	jQuery.ajax( {
		type     : 'POST',
		url      : url,
		data     : {},
		complete : function() {
			wpsIndexer.started = true;
			$blinker.removeClass( 'blinker' );
		},
		success  : function ( data ) {
			jQuery( '#wps_index_run' ).prop( 'disabled', false );
			if ( typeof data.time !== 'undefined' ) {
				$update.html( wpsIndexer.msg_ran );
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
			}
		},
		dataType : 'json'
	} );

};

wpsIndexerStatus.exec = function() {
	wpsIndexerStatus.timeout = setTimeout(
		function() {
			if ( wpsIndexerStatus.running ) {
				if ( !wpsIndexerStatus.polling ) {
					wpsIndexerStatus.poll();
				}
				wpsIndexerStatus.exec();
			}
		},
		10000
	);
};

wpsIndexerStatus.poll = function () {

	var url = null,
		display  = jQuery( '#wps-index-status-display' ),
		processable = jQuery( '#wps-index-status-processable' ),
		total = jQuery( '#wps-index-status-total' ),
		next_scheduled = jQuery( '#wps-index-status-next-scheduled' );

	if ( typeof wpsIndexerStatus.url !== 'undefined' ) {
		url = wpsIndexerStatus.url;
	} else {
		return;
	}

	display.addClass( 'blinker' );

	wpsIndexerStatus.polling = true;

	jQuery.ajax( {
		type     : 'POST',
		url      : url,
		data     : {},
		complete : function() {
			wpsIndexerStatus.polling = false;
			display.removeClass( 'blinker' );
		},
		success  : function ( data ) {
			if ( typeof data.pct !== 'undefined' ) {
				display.html( '' + Number.parseFloat(data.pct).toFixed(2) );
			}
			if ( typeof data.processable !== 'undefined' ) {
				processable.html( ' ' + data.processable );
			}
			if ( typeof data.total !== 'undefined' ) {
				total.html( ' ' + data.total );
			}
			if ( typeof data.next_scheduled_datetime !== 'undefined' ) {
				next_scheduled.html( ' ' + data.next_scheduled_datetime );
			} else {
				next_scheduled.html( '&mdash;' );
			}
			if ( typeof data.status !== 'undefined' ) {
				if ( data.status ) {
					jQuery( '#wps_index_start' ).prop( 'disabled', true );
					jQuery( '#wps_index_stop' ).prop( 'disabled', false );
				} else {
					jQuery( '#wps_index_start' ).prop( 'disabled', false );
					jQuery( '#wps_index_stop' ).prop( 'disabled', true );
				}
			}
		},
		dataType : 'json'
	} );

	if ( typeof wpsIndexerStatus.cron !== 'undefined' ) {
		jQuery.ajax( { url : wpsIndexerStatus.cron } );
	}
};

jQuery(document).ready(function(){
	wpsIndexerStatus.exec();
});
