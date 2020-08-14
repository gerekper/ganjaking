/**
 * Custom ThankYou page preview (Classic Editor)
 *
 * @category Script
 * @package  Yith Custom Thank You Page for Woocommerce
 * @author    Armando Liccardo
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * @link http://www.yithemes.com
 */

jQuery( function ( $ ) {
	$( document ).ready( function () {
		'use strict'

		var yctpw_preview = {

			yctpw_url_part: '',
			has_preview_link: 'no',
			saving: false,
			p_window: null,
			ajax_url: yctpw_preview_args.admin_ajax_url,
			pcontent: '',
			init: function () {
				jQuery( document ).on( 'heartbeat-tick.autosave', function ( event, data ) {

					if ( yctpw_preview.saving == true ) {
						yctpw_preview.openPreview()
						yctpw_preview.saving = false
					}

				} )

				//save the starting post content
				yctpw_preview.pcontent = wp.autosave.getPostData()['content']

			},
			doPreview: function ( button ) {
				/* get the Yith Thank You Page url arguments */
				yctpw_preview.yctpw_url_part = button.attr( 'url-part' )

				//if we have new content we will trigger the save action and load the autosave
				if ( this.pcontent != wp.autosave.getPostData()['content'] ) {
					this.saving = true /* this is needed so the object can launch the preview when the autosave is stopped */
					wp.autosave.server.triggerSave()
					this.has_preview_link = 'yes'
				} else {
					//no new content so we simply load the actual post and not the autosave
					yctpw_preview.openPreview()
				}

			},
			/* open preview tab */
			openPreview: function () {
				$.ajax( {
					'async': true,
					'type': 'POST',
					'url': this.ajax_url,
					'data': {
						'action': 'yith_ctpw_get_preview_link',
						'post_id': yctpw_preview_args.post_id,
						'has_preview': this.has_preview_link
					},
					'success': function ( resp ) {
						//if we not have opened a tab/window we open a new one and get the object
						if ( yctpw_preview.p_window == '' || yctpw_preview.p_window == null || yctpw_preview.p_window.closed ) {
							yctpw_preview.p_window = window.open( resp + '&' + yctpw_preview.yctpw_url_part, 'yctpw_preview' )
						} else {
							//try to open the new preview link in the previously opened tab/window
							yctpw_preview.p_window.location.href = resp + '&' + yctpw_preview.yctpw_url_part
						}

						yctpw_preview.p_window.focus()
					}

				} )
			}

		}

		/* init the preview object */
		yctpw_preview.init()

		/* preview button click */
		$( '.yctpw_preview_button a' ).on( 'click', function ( e ) {
			e.preventDefault()
			yctpw_preview.doPreview( $( this ) )
			return
		} )

		/* dummy order creation */
		$( '.button.yctpw_create_dummy_order' ).on( 'click', function ( e ) {
			e.preventDefault()

			var ajax_url = yctpw_preview_args.admin_ajax_url
			var data = { 'action': 'yith_ctpw_create_dummy_order' }
			$.post( ajax_url, data, function ( resp ) {
				if ( resp['response'] ) {
					$( '.yctpw_preview_dummy_order' ).hide()
					var preview_url = 'order-received=' + resp['order_id'] + '&key=' + resp['order_key'] + '&ctpw=' + $( '#post_ID' ).val()
					$( '.yctpw_preview_button a' ).attr( 'url-part', preview_url )
					$( '.yctpw_preview_button p' ).html( yctpw_preview_args.preview_message_dummy_order + ' ' + resp['order_id'] )
					$( '.yctpw_preview_button' ).show()
				} else {
					console.log( resp['message'] )
				}

			}, 'json' )

		} )

	} ) //document ready ends
} )