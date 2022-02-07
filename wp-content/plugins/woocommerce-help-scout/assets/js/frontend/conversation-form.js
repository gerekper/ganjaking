/** Global woocommerce_admin_meta_boxes, woocommerce_help_scout_shop_order_params
 *
 * @package converation form
 */

(function ($) {
	"use strict";
	$(
		function () {
			$( '.input-images-1' ).imageUploader(
				{
					maxFiles: 10,
					extensions: ['.jpg', '.jpeg', '.png', '.pdf', '.JPG', '.JPEG', '.PNG', '.PDF'],
					mimes : ['image/jpeg', 'image/png', 'image/gif', 'application/pdf']
				}
			);

			$( '.input-images-2' ).imageUploader(
				{
					maxFiles: 10,
					extensions: ['.jpg', '.jpeg', '.png', '.pdf', '.JPG', '.JPEG', '.PNG', '.PDF'],
					mimes : ['image/jpeg', 'image/png', 'image/gif', 'application/pdf']
				}
			);
			$( '.input-images-3' ).imageUploader(
				{
					maxFiles: 10,
					extensions: ['.jpg', '.jpeg', '.png', '.pdf', '.JPG', '.JPEG', '.PNG', '.PDF'],
					mimes : ['image/jpeg', 'image/png', 'image/gif', 'application/pdf']
				}
			);
			$( '.input-images-4' ).imageUploader(
				{
					maxFiles: 10,
					extensions: ['.jpg', '.jpeg', '.png', '.pdf', '.JPG', '.JPEG', '.PNG', '.PDF'],
					mimes : ['image/jpeg', 'image/png', 'image/gif', 'application/pdf']
				}
			);
			$( '.input-images-5' ).imageUploader(
				{
					maxFiles: 10,
					extensions: ['.jpg', '.jpeg', '.png', '.pdf', '.JPG', '.JPEG', '.PNG', '.PDF'],
					mimes : ['image/jpeg', 'image/png', 'image/gif', 'application/pdf']
				}
			);
			$( '.input-images-order-1' ).imageUploader(
				{
					maxFiles: 10,
					extensions: ['.jpg', '.jpeg', '.png', '.pdf', '.JPG', '.JPEG', '.PNG', '.PDF'],
					mimes : ['image/jpeg', 'image/png', 'image/gif', 'application/pdf']
				}
			);
			$( '.my-account-conversation-file-1' ).imageUploader(
				{
					maxFiles: 10,
					extensions: ['.jpg', '.jpeg', '.png', '.pdf', '.JPG', '.JPEG', '.PNG', '.PDF'],
					mimes : ['image/jpeg', 'image/png', 'image/gif', 'application/pdf']
				}
			);

			$( '.wc-help-scout-conversation-form' ).on(
				'submit',
				function (event) {
					event.preventDefault();
					var conversation_form = $( ".wc-help-scout-conversation-form" );
					var inc = $( this ).data( "inc" );
					var formdata = new FormData();
					var formdata = new FormData( $( "#wc-help-scout-conversation-form-" + inc )[0] );
					formdata.append( "action", "wc_help_scout_create_conversation" );
					formdata.append( "security", woocommerce_help_scout_form_params.security );

					$.blockUI(
						{
							message: woocommerce_help_scout_form_params.processing,
							baseZ: 99999,
							overlayCSS: { background: "#fff", opacity: 0.6 },
							css: { padding: "20px", zindex: "9999999", textAlign: "center", color: "#555", border: "3px solid #aaa", backgroundColor: "#fff", cursor: "wait", lineHeight: "24px" },
						}
					);

					$.ajax(
						{
							type: "POST",
							url: woocommerce_help_scout_form_params.ajax_url,
							cache: false,
							dataType: "json",
							contentType: "application/json",
							data: formdata,
							processData: false,
							contentType: false,
							success: function (data) {
								$( ".wc-help-scout-conversation-form" )[0].reset();
								$.unblockUI();
								if (null !== data && 1 === data.status) {
									conversation_form.empty().prepend( '<div class="woocommerce-message">' + woocommerce_help_scout_form_params.success + "</div>" );
								} else {
									var error_message = woocommerce_help_scout_form_params.error;
									if (data && data.status) {
										error_message = data.status;
									}
									$( ".woocommerce-error", conversation_form ).remove();
									conversation_form.prepend( '<div class="woocommerce-error">' + error_message + "</div>" );
								}
							},
							error: function () {
								$.unblockUI();
								$( ".woocommerce-error", conversation_form ).remove();
								conversation_form.prepend( '<div class="woocommerce-error">' + woocommerce_help_scout_form_params.error + "</div>" );
							},
						}
					);

				}
			);

			$( ".wc-helpscout-order-conversation-form" ).submit(
				function (e) {
					var conversation_form = $( ".wc-helpscout-order-conversation-form" );
					e.preventDefault();
					var formdata = new FormData();
					var formdata = new FormData( $( "#order_conversation_form_1" )[0] );
					$.blockUI(
						{
							message: woocommerce_help_scout_form_params.processing,
							baseZ: 99999,
							overlayCSS: { background: "#fff", opacity: 0.6 },
							css: { padding: "20px", zindex: "9999999", textAlign: "center", color: "#555", border: "3px solid #aaa", backgroundColor: "#fff", cursor: "wait", lineHeight: "24px" },
						}
					);

					formdata.append( "action", "wc_help_scout_create_conversation" );
					formdata.append( "security", woocommerce_help_scout_form_params.security );

					$.ajax(
						{
							type: "POST",
							url: woocommerce_help_scout_form_params.ajax_url,
							cache: false,
							dataType: "json",
							contentType: "application/json",
							data: formdata,
							processData: false,
							contentType: false,
							success: function (data) {
								$( ".wc-helpscout-order-conversation-form" )[0].reset();
								$.unblockUI();
								if (null !== data && 1 === data.status) {
									conversation_form.empty().prepend( '<div class="woocommerce-message">' + woocommerce_help_scout_form_params.success + "</div>" );
								} else {
									var error_message = woocommerce_help_scout_form_params.error;
									if (data && data.status) {
										error_message = data.status;
									}
									$( ".woocommerce-error", conversation_form ).remove();
									conversation_form.prepend( '<div class="woocommerce-error">' + error_message + "</div>" );
								}
							},
							error: function () {
								$.unblockUI();
								$( ".woocommerce-error", conversation_form ).remove();
								conversation_form.prepend( '<div class="woocommerce-error">' + woocommerce_help_scout_form_params.error + "</div>" );
							},
						}
					);
				}
			);
		}
	);
})( jQuery );
