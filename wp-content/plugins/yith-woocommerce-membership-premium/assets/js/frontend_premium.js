/* global yith_wcmbs_frontend */
jQuery( function ( $ ) {
	var tabs      = $( '.yith-wcmbs-tabs' ),
		accordion = $( '.yith-wcmbs-my-account-accordion' );

	tabs.tabs();

	accordion.accordion( {
							 collapsible: true,
							 heightStyle: 'content'
						 } );

	/*
	 MESSAGES
	 */
	var user_id                           = yith_wcmbs_frontend.user_id,
		ajax_url                          = yith_wcmbs_frontend.ajax_url,
		message                           = $( '#yith-wcmbs-message-to-send' ),
		send_button                       = $( '#yith-wcmbs-send-button' ),
		messages_list                     = $( '#yith-wcmbs-widget-messages-list' ),
		messages_list_wrapper             = $( '#yith-wcmbs-widget-messages-list-wrapper' ),
		get_older_btn                     = $( '#yith-wcmbs-get-older-messages' ),
		control_if_all_messages_displayed = function () {
			var displayed_messages = messages_list.find( 'li' ).length;

			if ( displayed_messages >= displayed_messages ) {
				//get_older_btn.hide();
				get_older_btn.addClass( 'yith-wcmbs-get-older-messages-disabled' );
			} else {
				get_older_btn.removeClass( 'yith-wcmbs-get-older-messages-disabled' );
			}
		},
		list_go_to_bottom                 = function () {
			if ( messages_list.length ) {
				messages_list_wrapper.animate( {
												   scrollTop: messages_list.outerHeight()
											   }, 1000, 'swing' );
			}
		};
	send_button.on( 'click', function () {
		if ( user_id && message.val().length > 0 ) {
			var post_data = {
				user_id: user_id,
				message: message.val(),
				action : 'yith_wcmbs_user_send_message'
			};

			$.ajax( {
						type   : "POST",
						data   : post_data,
						url    : ajax_url,
						success: function ( response ) {
							messages_list.append( response );
							message.val( '' );
							list_go_to_bottom();
						}
					} );
		}
	} );

	get_older_btn.on( 'click', function () {
		var message_number = messages_list.find( 'li' ).length;
		var post_data      = {
			user_id: user_id,
			offset : message_number,
			action : 'yith_wcmbs_user_get_older_messages'
		};

		$.ajax( {
					type   : "POST",
					data   : post_data,
					url    : ajax_url,
					success: function ( response ) {
						messages_list.prepend( response );
						control_if_all_messages_displayed();
					}
				} );

	} );

	control_if_all_messages_displayed();
	list_go_to_bottom();


	var getCookie    = function ( name ) {
			var value = "; " + document.cookie,
				parts = value.split( "; " + name + "=" );
			if ( parts.length === 2 ) {
				return parts.pop().split( ";" ).shift();
			}
		},
		removeCookie = function ( name ) {
			document.cookie = name + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC;";
		};

	/**
	 * Download Buttons
	 */

	var block_params = {
		message        : null,
		overlayCSS     : {
			background: '#000',
			opacity   : 0.7
		},
		ignoreIfBlocked: true
	};

	$( document ).on( 'click', '.yith-wcmbs-download-button.locked', function () {
		var button         = $( this ).closest( '.yith-wcmbs-download-button' ),
			key            = button.data( 'key' ),
			product_id     = button.data( 'product-id' ),
			cookie         = 'yith_wcmbs_downloading_' + key,
			container      = button.closest( '.yith-wcmbs-product-download-box' ),
			elementToBlock = container.length ? container : button.parent().find( '.yith-wcmbs-download-button' ),
			type           = elementToBlock.is( '.yith-wcmbs-product-download-box' ) ? 'box' : 'buttons-container',
			loading        = function () {
				block_params.overlayCSS.borderRadius = elementToBlock.css( 'border-radius' );
				elementToBlock.block( block_params );
			},
			complete       = function () {
				$.ajax( {
							type    : "POST",
							data    : {
								product_id: product_id,
								type      : type,
								action    : 'yith_wcmbs_download_product_update'
							},
							url     : ajax_url,
							success : function ( response ) {
								if ( response.html ) {
									if ( 'box' === type ) {
										elementToBlock.replaceWith( response.html );
									} else {
										elementToBlock.first().parent().html( response.html );
									}
								}
							},
							complete: function () {
								elementToBlock.unblock();
							}
						} );
			};

		removeCookie( cookie );
		loading();

		var interval = setInterval( function () {
			if ( getCookie( cookie ) === 'yes' ) {
				removeCookie( 'cookie' );
				clearInterval( interval );
				setTimeout( complete, 200 );
			}
		}, 300 );
	} );


	/**
	 * Items in Membership pagination
	 */

	var itemsPagination = {
		selectors       : {
			container  : '.yith-wcmbs-membership-plan-items',
			prevEnabled: '.yith-wcmbs-membership-plan-items__pagination__prev.yith-wcmbs--enabled',
			nextEnabled: '.yith-wcmbs-membership-plan-items__pagination__next.yith-wcmbs--enabled',
			content    : '.yith-wcmbs-membership-plan-items__content'
		},
		block_params    : {
			message        : null,
			overlayCSS     : {
				background: '#fff',
				opacity   : 0.7
			},
			ignoreIfBlocked: true
		},
		init            : function () {
			$( document ).on( 'click', this.selectors.prevEnabled, this.loadItemsHandler );
			$( document ).on( 'click', this.selectors.nextEnabled, this.loadItemsHandler );
		},
		loadItemsHandler: function () {
			var button    = $( this ),
				container = button.closest( itemsPagination.selectors.container ),
				content   = container.find( itemsPagination.selectors.content ),
				data      = container.data();

			data.page   = button.data( 'page' );
			data.action = 'yith_wcmbs_get_plan_post_type_items';

			content.block( itemsPagination.block_params );

			$.ajax( {
						type    : "POST",
						data    : data,
						url     : ajax_url,
						success : function ( response ) {
							if ( response.success ) {
								container.replaceWith( response.html );
							}
						},
						complete: function () {
							content.unblock();
						}
					} );
		}
	};

	itemsPagination.init();
} );
