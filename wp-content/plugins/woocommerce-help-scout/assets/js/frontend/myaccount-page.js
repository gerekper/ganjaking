/* global woocommerce_help_scout_myaccount_params */
(function ( $ ) {
	'use strict';

	$(function () {
		function block_ui( msg ) {
			if ( ! msg ) {
				return;
			}

			$.blockUI({
				message: msg,
				baseZ: 99999,
				overlayCSS: {
					background: '#fff',
					opacity:    0.6
				},
				css: {
					padding:         '20px',
					zindex:          '9999999',
					textAlign:       'center',
					color:           '#555',
					border:          '3px solid #aaa',
					backgroundColor: '#fff',
					cursor:          'wait',
					lineHeight:      '24px'
				}
			});
		}

		function scrollToConversation() {
			var wrap = $( '#support-conversation-wrap' );

			$( 'html, body' ).animate({
				scrollTop: wrap.offset().top
			}, 500 );
		}

		/**
		 * Get conversation details.
		 */
		$( '#support-conversations-table .conversation-view' ).on( 'click', function ( e ) {
			e.preventDefault();

			var conversation_id = $( this ).data( 'conversation-id' ),
				wrap            = $( '#support-conversation-wrap' );

			block_ui( woocommerce_help_scout_myaccount_params.getting_conversation );

			$.ajax({
				type: 'GET',
				url: woocommerce_help_scout_myaccount_params.ajax_url,
				cache: false,
				dataType: 'json',
				data: {
					action:         'wc_help_scout_get_conversation',
					security:        woocommerce_help_scout_myaccount_params.security,
					conversation_id: conversation_id
				},
				success: function ( data ) {
					var html          = '',
						error_message = woocommerce_help_scout_myaccount_params.error;

					// Remove the blockUI.
					$.unblockUI();

					if ( null !== data && '' === data.error ) {
						html += '<h3 id="support-conversation-thread-head">"' + data.subject + '":</h3>';
						html += '<ul id="support-conversation-thread">';
						$.each( data.threads, function( key, thread ) {
							html += '<li>';
							html += '<strong>' + thread.author + '</strong> <small>(' + thread.date + ')</small>:';
							html += thread.message;
							html += '</li>';
						});

						html += '</ul>';
						html += '<p><a href="#" data-conversation-id="' + conversation_id + '" data-subject="' + data.subject + '" class="button conversation-reply">' + woocommerce_help_scout_myaccount_params.reply + '</a></p>';

						wrap.empty().prepend( html );

						scrollToConversation();
					} else {
						if ( null !== data && null !== data.error && '' !== data.error ) {
							error_message = data.error;
						}

						$( '.woocommerce-error', wrap ).remove();
						wrap.prepend( '<div class="woocommerce-error">' + error_message + '</div>' );
					}
				},
				error: function () {
					$.unblockUI();

					$( '.woocommerce-error', wrap ).remove();
					wrap.prepend( '<div class="woocommerce-error">' + woocommerce_help_scout_myaccount_params.error + '</div>' );
				}
			});
		});

		/**
		 * Create reply form.
		 */
		$( 'body' ).on( 'click', '.conversation-reply', function ( e ) {
			e.preventDefault();

			var conversation_id = $( this ).data( 'conversation-id' ),
				wrap            = $( '#support-conversation-wrap' ),
				form            = '';

			form += '<h3 id="support-conversation-thread-head">' + woocommerce_help_scout_myaccount_params.reply_to + ' "' + $( this ).data( 'subject' ) + '":</h3>';
			form += '<form id="support-conversation-reply" action="" method="post">';
			form += '<p class="form-row form-row-wide">';
			form += '<label for="conversation-message">' + woocommerce_help_scout_myaccount_params.message + ' <span class="required">*</span></label>';
			form += '<textarea id="conversation-message" class="conversation-field" name="conversation_message" cols="25" rows="5"></textarea>';
			form += '</p>';
			form += '<p class="form-row">';
			form += '<input type="hidden" name="conversation_id" value="' + conversation_id + '" />';
			form += '<input type="hidden" name="user_id" value="' + woocommerce_help_scout_myaccount_params.user_id + '" />';
			form += '<input type="submit" class="button alt" value="' + woocommerce_help_scout_myaccount_params.send + '" />';
			form += '</p>';
			form += '</form>';

			wrap.fadeOut( 500, function() {
				$( this ).empty().show().prepend( form );
			});

			scrollToConversation();
		});

		/**
		 * Send/create the thread/reply.
		 */
		$( 'body' ).on( 'submit', '#support-conversation-reply', function ( e ) {
			e.preventDefault();

			var wrap  = $( '#support-conversation-wrap' ),
				form  = $( '#support-conversation-reply' ),
				title = $( '#support-conversation-thread-head' );

			block_ui( woocommerce_help_scout_myaccount_params.processing );

			$.ajax({
				type: 'POST',
				url: woocommerce_help_scout_myaccount_params.ajax_url,
				cache: false,
				dataType: 'json',
				data: {
					conversation_id:      $( 'input[name="conversation_id"]', form ).val(),
					conversation_message: $( 'textarea[name="conversation_message"]', form ).val(),
					user_id:              $( 'input[name="user_id"]', form ).val(),
					action:               'wc_help_scout_create_thread',
					security:             woocommerce_help_scout_myaccount_params.security
				},
				success: function ( data ) {
					// Remove the blockUI.
					$.unblockUI();

					if ( null !== data && 1 === data.error ) {
						$( '.woocommerce-error, form', wrap ).remove();
						title.after( '<div class="woocommerce-message">' + data.message + '</div>' );
					} else {
						var error_message = woocommerce_help_scout_myaccount_params.error;
						if ( null !== data && null !== data.message && '' !== data.message ) {
							error_message = data.message;
						}

						$( '.woocommerce-error', wrap ).remove();
						title.after( '<div class="woocommerce-error">' + error_message + '</div>' );
					}
				},
				error: function () {
					$.unblockUI();

					$( '.woocommerce-error', wrap ).remove();
					title.after( '<div class="woocommerce-error">' + woocommerce_help_scout_myaccount_params.error + '</div>' );
				}
			});

		});
	});

}( jQuery ));
