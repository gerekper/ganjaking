/**
 * Manage the admin javascript
 *
 * @package YITH\PDF_Invoice\JS
 */

jQuery(
	function ($) {
		var initial_element   = '.yith-plugins_page_yith_woocommerce_pdf_invoice_panel';
		var selected_elements = [ 'ul.yith-plugin-fw-tabs li a',
			'.yith-plugin-fw-sub-tabs-nav .nav-tab-wrapper a',
			'.ywpi-documents-table .page-title-action',
			'.invoice_date_pickers input[type="submit"]',
			'.yith-ywpi-actions-container a.yith-plugin-fw__action-button__menu__item',
			'.tablenav input'
		];

		$.each(
			selected_elements,
			function( key, element ) {
				$( document ).on(
					'click',
					initial_element + ' ' + element ,
					function( e ) {
						window.onbeforeunload = null;
					}
				);
			}
		);

		// ======= click in the upload button to check when the logo is inserted ========
		var upload_button_clicked = false;
		$( "body" ).on(
			"change",
			"#ywpi_company_logo",
			function () {

				var tmpImg = new Image();
				tmpImg.src = $( this ).val();
				$( tmpImg ).one(
					'load',
					function(){
						orgWidth  = tmpImg.width;
						orgHeight = tmpImg.height;

						if ( orgWidth > 300 || orgHeight > 150 ) {

							// alert( "The logo you are uploading is " + orgWidth + "x" + orgHeight + ". Logo must be no bigger than 300 x 150 pixels" );.

							alert( yith_wc_pdf_invoice_free_object.logo_message_1 + orgWidth + "x" + orgHeight + " pixels. " + yith_wc_pdf_invoice_free_object.logo_message_2 );

							$( "body #ywpi_company_logo" ).val( '' );
							$( "body #ywpi_company_logo-container .upload_img_preview img" ).remove();
						}

					}
				);

			}
		);

		/*
			Check data before to proceed with refund
		*/
		if ( yith_wc_pdf_invoice_free_object.electronic_invoice == 'yes' ) {

			var woocommerceOrderItems = $( '.post-type-shop_order' ).find( '#woocommerce-order-items' );

			woocommerceOrderItems.find( '#refund_amount' ).parent().addClass( 'wrap-input ywpi-disabled' );

			$( '.ywpi-disabled' ).on(
				'click',
				function(){
					alert( yith_wc_pdf_invoice_free_object.alert_refund_credit_note );
				}
			);

			woocommerceOrderItems.find( 'label[for="refund_reason"]' ).text( 'Reason for refund(mandatory)' );

				$( '.do-manual-refund' ).click(
					function(e){

						if ( $( '#refund_reason' ).val() === '' ) {
							alert( yith_wc_pdf_invoice_free_object.alert_refund_credit_reason );
							e.stopImmediatePropagation();
						}
					}
				);

		}

		/**
		 * ADMIN OPTIONS
		 */
		// dropbox upload onoff.
		var ywpiFieldsVisibility = {

			conditions               : {
				allow_dropbox_upload: $( '#ywpi_dropbox_allow_upload' ),
			},
			dom               : {
				dropbox_key         : $( '#ywpi_dropbox_key' ),
			},
			init              : function () {
				var self = ywpiFieldsVisibility;

				self.conditions.allow_dropbox_upload.on(
					'change',
					function () {
						self.handle( self.dom.dropbox_key, 'yes' === self.conditions.allow_dropbox_upload.val() );
					}
				).trigger( 'change' );

			},
			handle            : function ( target, condition ) {

				var targetHide = target.closest( '.yith-plugin-fw-panel-wc-row' );

				if ( condition ) {
					$( targetHide ).fadeIn();
				} else {
					$( targetHide ).hide();
				}
			},
		}

		ywpiFieldsVisibility.init();

		initListTables = function () {
			if ( $( window ).width() > 768 ) { // Responsive mode, if it's a mobile, it show the Search button.
				$( '#yith-ywpi-list-table #posts-filter .search-box' ).insertAfter( '#yith-ywpi-list-table #posts-filter .tablenav.top .tablenav-pages' );
			}
			$( '#yith-ywpi-list-table #posts-filter .search-box input[type="search"]' ).attr( "placeholder", yith_wc_pdf_invoice_free_object.search_invoice_placeholder );

			$( '.yith-ywpi-list-table-elements .ywpi_preview_action a.yith-plugin-fw__action-button__link' ).each(
				function() {
					$( this ).attr( "target","_blank" );
				}
			);

			let tablenav_bottom = $( '#yith-ywpi-list-table .tablenav.bottom' );
			if ( tablenav_bottom.find( '.tablenav-pages .pagination-links a.next-page' ).length ) {
				tablenav_bottom.show();
			}
		}

		initListTables();

		/** Bulk actions for invoice and credit note tables */
		$( document ).on(
			'click',
			'#yith-ywpi-list-table div.bulkactions #doaction',
			function( e ) {
				var value      = $( this ).parent().find( 'select option:selected' ).val(),
					is_invoice = $( 'form.invoices-table' ).length;

				if ( 'regenerate' === value ) {
					let regenerate_confirm_message = '';
					if ( is_invoice ) {
						regenerate_confirm_message = yith_wc_pdf_invoice_free_object.regenerate_confirm_message_invoices;
					} else {
						regenerate_confirm_message = yith_wc_pdf_invoice_free_object.regenerate_confirm_message_invoices;
					}

					e.preventDefault();

					yith.ui.confirm(
						{
							title: yith_wc_pdf_invoice_free_object.regenerate_confirm_title,
							message: regenerate_confirm_message,
							confirmButtonType: 'confirm',
							confirmButton: yith_wc_pdf_invoice_free_object.regenerate_confirm_button,
							closeAfterConfirm: true,
							width: 400,
							onConfirm: function () {
								$( '#posts-filter' ).unbind( 'submit' ).submit();
							},
						}
					);
					// Prevent WooCommerce warning for changes without saving.
					window.onbeforeunload = null;
				}
				if ( 'delete' === value ) {
					let delete_confirm_message = '';
					if ( is_invoice ) {
						delete_confirm_message = yith_wc_pdf_invoice_free_object.delete_confirm_message_invoices;
					} else {
						delete_confirm_message = yith_wc_pdf_invoice_free_object.delete_confirm_message_credit_notes;
					}
					e.preventDefault();

					yith.ui.confirm(
						{
							title: yith_wc_pdf_invoice_free_object.delete_confirm_title,
							message: delete_confirm_message,
							confirmButtonType: 'delete',
							confirmButton: yith_wc_pdf_invoice_free_object.delete_confirm_button,
							cancelButton: yith_wc_pdf_invoice_free_object.delete_cancel_button,
							closeAfterConfirm: true,
							width: 400,
							onConfirm: function () {
								$( '#posts-filter' ).unbind( 'submit' ).submit();
							},
						}
					);
					// Prevent WooCommerce warning for changes without saving.
					window.onbeforeunload = null;
				}
			}
		);
	}
);
