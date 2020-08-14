/**
 * Custom ThankYou page preview (Gutenberg Editor)
 *
 * @category Script
 * @package  Yith Custom Thank You Page for Woocommerce
 * @author    Armando Liccardo
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * @link http://www.yithemes.com
 */

jQuery( function ( $ ) {
	$( document ).ready( function () {
		'use strict';

		/* add plugin sidebar in gutenberg */
		( function ( wp ) {
			var registerPlugin = wp.plugins.registerPlugin
			var PluginSidebar = wp.editPost.PluginSidebar
			var el = wp.element.createElement
			var Text = wp.components.TextControl
			var Button = wp.components.Button
			var preview_button_div_class = yctpw_preview_args.preview_link == '' ? 'yctpw_preview_plugin_sidebar_preview_button hidden' : 'yctpw_preview_plugin_sidebar_preview_button'
			var create_dummy_order_div_class = yctpw_preview_args.preview_link == '' ? 'yctpw_preview_plugin_sidebar_create_dummy_order' : 'yctpw_preview_plugin_sidebar_create_dummy_order hidden'

			const yctpwIcon = wp.element.createElement( 'svg',
				{
					width: 20,
					height: 20
				},
				wp.element.createElement( 'path',
					{
						d: 'M 18.24 7.628 C 17.291 8.284 16.076 8.971 14.587 9.688 C 15.344 7.186 15.765 4.851 15.849 2.684 C 15.912 0.939 15.133 0.045 13.514 0.003 C 11.558 -0.06 10.275 1.033 9.665 3.284 C 10.007 3.137 10.359 3.063 10.723 3.063 C 11.021 3.063 11.267 3.184 11.459 3.426 C 11.651 3.668 11.736 3.947 11.715 4.262 C 11.695 5.082 11.276 5.961 10.46 6.896 C 9.644 7.833 8.918 8.3 8.282 8.3 C 7.837 8.3 7.625 7.922 7.646 7.165 C 7.667 6.765 7.804 5.955 8.056 4.735 C 8.287 3.579 8.403 2.801 8.403 2.401 C 8.403 1.707 8.224 1.144 7.867 0.713 C 7.509 0.282 6.994 0.098 6.321 0.161 C 5.858 0.203 5.175 0.624 4.27 1.422 C 3.596 2.035 2.923 2.644 2.25 3.254 L 2.976 4.106 C 3.564 3.664 3.922 3.443 4.048 3.443 C 4.448 3.443 4.637 3.717 4.617 4.263 C 4.617 4.306 4.427 4.968 4.049 6.251 C 3.671 7.534 3.471 8.491 3.449 9.122 C 3.407 9.985 3.565 10.647 3.924 11.109 C 4.367 11.677 5.106 11.919 6.142 11.835 C 7.366 11.751 8.591 11.298 9.816 10.479 C 10.323 10.142 10.808 9.753 11.273 9.311 C 11.105 10.153 10.905 10.868 10.673 11.457 C 8.402 12.487 6.762 13.37 5.752 14.107 C 4.321 15.137 3.554 16.241 3.449 17.419 C 3.259 19.459 4.29 20.479 6.541 20.479 C 8.055 20.479 9.517 19.554 10.926 17.703 C 12.125 16.126 13.166 14.022 14.049 11.394 C 15.578 10.635 16.87 9.892 17.928 9.164 C 17.894 9.409 18.319 7.308 18.24 7.628 Z  M 7.393 16.095 C 7.056 16.095 6.898 15.947 6.919 15.653 C 6.961 15.106 7.908 14.38 9.759 13.476 C 8.791 15.221 8.002 16.095 7.393 16.095 Z'
					}
				)
			)

			registerPlugin( 'yith-custom-thank-you-page-premium', {
				render: function () {

					return el( PluginSidebar,
						{
							name: 'yctpw-sidebar',
							icon: yctpwIcon,
							title: yctpw_preview_args.sidebar_title,
						},

						/* preview button part */

						el( 'div',
							{ className: preview_button_div_class },
							el( 'p',
								{ className: 'yctpw_preview_message' },
								yctpw_preview_args.preview_message
							),
							el( Button, {
									className: 'yctpw_preview_button',
									label: yctpw_preview_args.button_title,
									title: yctpw_preview_args.button_title,
									url_part: yctpw_preview_args.preview_link,
									isPrimary: true,
									onClick: function () {
										var button = $( 'button.yctpw_preview_button' )
										yctpw_preview.doPreview( button )
									},

								},
								yctpw_preview_args.button_title
							),
						),

						/* create dummy order part */
						el( 'div',
							{ className: create_dummy_order_div_class },
							el( 'p',
								{ className: 'yctpw_dummy_order_info' },
								yctpw_preview_args.create_dummy_order_info
							),
							el( Button, {
									className: 'yctpw_create_dummy_order',
									label: yctpw_preview_args.create_dummy_order_button_title,
									title: yctpw_preview_args.create_dummy_order_button_title,
									isPrimary: true,
									onClick: function () {
										var ajax_url = yctpw_preview_args.admin_ajax_url
										var data = { 'action': 'yith_ctpw_create_dummy_order' }
										$.post( ajax_url, data, function ( resp ) {
											if ( resp['response'] ) {
												$( '.yctpw_preview_plugin_sidebar_create_dummy_order' ).addClass( 'hidden' )
												var preview_url = '&order-received=' + resp['order_id'] + '&key=' + resp['order_key'] + '&ctpw=' + $( '#post_ID' ).val()
												$( 'button.yctpw_preview_button' ).attr( 'url_part', preview_url )
												$( '.yctpw_preview_plugin_sidebar_preview_button p.yctpw_preview_message' ).html( yctpw_preview_args.preview_message_dummy_order + ' ' + resp['order_id'] )
												$( '.yctpw_preview_plugin_sidebar_preview_button' ).removeClass( 'hidden' )
											} else {
												console.log( 'YCTPW error: ' + resp['message'] )
											}

										}, 'json' )
									},

								},
								yctpw_preview_args.create_dummy_order_button_title
							)
						),
					)

				}
			} )
		} )( window.wp )

		var yctpw_preview = {

			yctpw_url_part: '',
			has_preview_link: 'no',
			saving: false,
			p_window: null,
			ajax_url: yctpw_preview_args.admin_ajax_url,
			init: function () {

				wp.data.subscribe( function () {

					//var isSavingPost = wp.data.select('core/editor').isSavingPost();
					//var isAutosavingPost = wp.data.select('core/editor').isAutosavingPost();
					var success = wp.data.select( 'core/editor' ).didPostSaveRequestSucceed()

					if ( yctpw_preview.saving && success ) {
						yctpw_preview.saving = false
						yctpw_preview.openPreview()
					}
				} )

			},
			doPreview: function ( button ) {
				/* get the Yith Thank You Page url arguments */
				this.yctpw_url_part = button.attr( 'url_part' )

				/* check autosave  */
				var autosave = wp.data.select( 'core/editor' ).getAutosave()
				var current_content = wp.data.select( 'core/editor' ).getEditedPostContent()

				if ( autosave !== null ) { this.has_preview_link = 'yes'}

				if ( null === autosave && wp.data.select( 'core/editor' ).hasChangedContent() || wp.data.select( 'core/editor' ).hasChangedContent() && current_content != autosave['content'] ) {
					/* the content is changed or we have no previous autosave, so we make one - the preview will be opened in the subscription in the init of the object */
					this.saving = true /* this is needed so the object can launch the preview when the autosave is stopped */
					wp.data.dispatch( 'core/editor' ).autosave()
					wp.data.dispatch( 'core/editor' ).refreshPost()
					this.has_preview_link = 'yes'

				} else {

					/* no need to do autosave so directly show the preview */
					this.openPreview()

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

		/* initi the preview object */
		yctpw_preview.init()

	} ) //document ready ends
} )