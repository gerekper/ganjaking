/* global ajaxurl */
/* global wp_smush_msgs */

/**
 * @typedef {Object} jQuery
 * @property
 */
( function( $ ) {
	'use strict';

	/**
	 * S3 support alert.
	 *
	 * @since 3.6.2  Moved from class-s3.php
	 */
	$.get( ajaxurl, { action: 'smush_notice_s3_support_required' }, function(
		r
	) {
		if ( 'undefined' === typeof r.data ) {
			return;
		}

		const noticeOptions = {
			type: 'warning',
			icon: 'info',
			dismiss: {
				show: true,
				label: wp_smush_msgs.noticeDismiss,
				tooltip: wp_smush_msgs.noticeDismissTooltip,
			},
		};

		window.SUI.openNotice(
			'wp-smush-s3support-alert',
			r.data,
			noticeOptions
		);
	} );

	// Dismiss S3 support alert.
	$( '#wp-smush-s3support-alert' ).on( 'click', 'button', () => {
		$.post( ajaxurl, { action: 'dismiss_s3support_alert' } );
	} );

	// Remove API message.
	$( '#wp-smush-api-message button.sui-button-icon' ).on( 'click', function(
		e
	) {
		e.preventDefault();
		const notice = $( '#wp-smush-api-message' );
		notice.slideUp( 'slow', function() {
			notice.remove();
		} );
		$.post( ajaxurl, { action: 'hide_api_message' } );
	} );

	let elNotice = $( '.smush-notice' );
	const btnAct = elNotice.find( '.smush-notice-act' );

	elNotice.fadeIn( 500 );

	// Hide the notice after a CTA button was clicked
	function removeNotice() {
		elNotice.fadeTo( 100, 0, () =>
			elNotice.slideUp( 100, () => elNotice.remove() )
		);
	}

	btnAct.on( 'click', () => {
		removeNotice();
		notifyWordpress( btnAct.data( 'msg' ) );
	} );

	elNotice.find( '.smush-notice-dismiss' ).on( 'click', () => {
		removeNotice();
		notifyWordpress( btnAct.data( 'msg' ) );
	} );

	// Notify WordPress about the users choice and close the message.
	function notifyWordpress( message ) {
		elNotice.attr( 'data-message', message );
		elNotice.addClass( 'loading' );

		// Send a ajax request to save the dismissed notice option.
		$.post( ajaxurl, { action: 'dismiss_upgrade_notice' } );
	}

	// Dismiss the update notice.
	$( '.wp-smush-update-info' ).on( 'click', '.notice-dismiss', ( e ) => {
		e.preventDefault();
		elNotice = $( this );
		removeNotice();
		$.post( ajaxurl, { action: 'dismiss_update_info' } );
	} );
} )( jQuery );
