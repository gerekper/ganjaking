jQuery(
	function ($) {

		let currentWindow = $( window );
		let modalWidth    = currentWindow.width() * 0.9;
		let modalHeight   = currentWindow.height() * 0.9;

		// Create the dialog for importing the SKUs.
		$( "#check-skus-modal" ).dialog(
			{
				height: modalHeight,
				width: modalWidth,
				autoOpen: false,
				resizable: false,
				modal: true,
				open: function (event, ui) {
					$( ".ui-dialog-titlebar-close" ).text( '' ).append( '<span class="ui-button-icon ui-icon ui-icon-closethick"></span>' );

				}
			}
		);

		// Dynamic POST to force update the marketplace select when service URL changes.
		$( document ).on(
			'change',
			'#woocommerce_fba_ns_fba_service_url',
			function () {
				let amazon_region_url = $( this ).val();
				if (amazon_region_url) {
					$.ajax(
						{
							url      : ajaxurl,
							type     : 'POST',
							dataType : 'json',
							cache    : false,
							data     : {
								action : 'ns_fba_refresh_marketplace_options',
								nonce  : ns_fba.nonce,
								amazon_region_url: amazon_region_url
							},
							success: function (result) {
								let market_selector = $( '#woocommerce_fba_ns_fba_marketplace_id' );
								market_selector.empty().append( result.data );
								refreshMarketPlaceURL();
							}
						}
					);
				}
			}
		);

		// Refresh marketplace url.
		$( document ).on(
			'change',
			'#woocommerce_fba_ns_fba_marketplace_id',
			function () {
				refreshMarketPlaceURL();
			}
		);

		// AJAX action buttons in settings.
		$( '[name=ns_fba_test_api], [name=ns_fba_test_inventory], [name=ns_fba_sync_inventory_manually], [name=ns_fba_clean_logs_now], [name=ns_fba_sp_api_sync_inventory]' ).click(
			function(e){
				let button = $( this );

				button.attr( "disabled","disabled" );
				button.nextAll( '.ns-fba-success-label, .ns-fba-error-label' ).remove();
				button.after( '<div id="shared-spinner" class="spinner is-active"/>' );
				$.post(
					ajaxurl,
					{
						action  : $( this ).attr( 'name' ),
						nonce   : ns_fba.nonce,
						options : $( '#mainform' ).serialize(),
					},
					function (response){
						button.next( '#shared-spinner' ).remove();
						finalizeAjaxCall( response, button );
					}
				);
				e.preventDefault();
			}
		);

		/**
		 * Attach a click event for "Check More SKUs" button.
		 * requests products from Seller Partner,
		 * conforms and presents the modal popup with resulting data */
		$( '[name=ns_fba_sp_api_check_skus]' ).click(
			function (e) {
				let button = $( this );
				let action = 'ns_fba_sp_api_check_skus';

				// Disable the button for preventing new calls.
				button.attr( "disabled","disabled" );

				// Removing previous messages.
				button.nextAll( '.ns-fba-success-label, .ns-fba-error-label' ).remove();

				// Inserting a progress spinner.
				button.after( '<div id="check-for-more-sku-spinner" class="spinner is-active"/>' );
				$( '#check-for-more-sku-spinner' ).show();

				// Request the SKUs to Selling Partner.
				requestSKUs(
					action,
					function (response) {
						// On step callback, triggered when any result page is obtained.
						$( '#check-for-more-sku-spinner' ).remove();
					},
					function (response) {
						// On complete callback, triggered when whole data is loaded.
						$( '#check-for-more-sku-spinner' ).hide();

						// this fragment of code checks for the initial status of "check-all" control, in woocommerce products table;
						// if there is any product, and all of them are checked, the "check-all" control must be checked too.
						if ($( '.ns-fba-fulfill-product:not([value="0"]):checked' ).length === $( '.ns-fba-fulfill-product:not([value="0"])' ).length &&
							$( '.ns-fba-fulfill-product:not([value="0"])' ).length > 0) {
							$( '.ns-fba-fulfill-product[value="0"]' ).attr( 'checked', true );
						}

						// Performs.
						finalizeAjaxCall( response, button );
					},
					function (response) {
						// On error callback, triggered when an error is obtained in the process.
						$( '#check-for-more-sku-spinner' ).hide();
						finalizeAjaxCall( response, button );
					},
					true,
					''
				);

				e.preventDefault();
			}
		);

		/**
		 * Ajax action for disconnecting from Seller Partner */
		$( '[name=ns_fba_sp_api_disconnect_amazon]' ).click(
			function (e) {
				$.post(
					ajaxurl,
					{
						action  : 'ns_fba_sp_api_disconnect_amazon',
						nonce   : ns_fba.nonce,
					},
					function (response){
						location.reload();
					}
				);
				e.preventDefault();
			}
		);

		/**
		 * Make a recursive iteration through successive result pages of the SKUs returned by Selling Partner.
		 *
		 * @onStepCallback is triggered whenever a result page is obtained
		 * @onCompletedCallback is triggered when whole process is completed
		 * @onErrorCallback is triggered only when an error is obtained from the API
		 * @isFirstCall is used to determine when the rendered table, that will be placed on popup, must be cleared for avoid data duplications
		 * @nextToken is used to request the next result page */
		function requestSKUs( action, onStepCallback, onCompleteCallback, onErrorCallback, isFirstCall, nextToken ) {

			$.post(
				ajaxurl,
				{
					action: action,
					nonce: ns_fba.nonce,
					options: $( '#mainform' ).serialize(),
					data: {
						'is_first_call': isFirstCall ? 1 : 0,
						'next_token': nextToken
					}
				},
				function (response) {

					// Trigger the onStepCallback, it doesn't mean that it's not error, this callback is triggered whenever an step is completed.
					onStepCallback( response );

					if (response.success) {

						let nextToken = response.data.next_token;

						let isLastCall = nextToken === '';

						// Fill the table for products that exist in woocommerce.
						fillDataTable( false, response.data, isFirstCall, isLastCall );

						// Fill the table for products that aren't synchronized in woocommerce.
						fillDataTable( true, response.data, isFirstCall, isLastCall );

						if (nextToken !== '') {
							// make a recursive call for iterate next result page.
							requestSKUs( action, onStepCallback, onCompleteCallback, onErrorCallback, false, response.data.next_token );
						} else {
							onCompleteCallback( response );
						}

						if (isFirstCall) {
							$( "#check-skus-modal" ).dialog( "open" );
						}

					} else {
						// notify an error.
						onErrorCallback( response );
					}
				}
			);
		}

		// Process the SKU synchro.
		$( document ).on(
			'click',
			'.synchronize-sku-button',
			function (e) {
				let data = [];

				$( '.sync-spinner' ).addClass( 'is-active' );

				$( '.chk-pending-sku:checked' ).each(
					function (e) {
						data.push( $( this ).val() );
					}
				);

				$.post(
					ajaxurl,
					{
						action: 'import_seller_partner_sku',
						nonce: ns_fba.nonce,
						data: data,
					},
					function (response) {

						$( '.sync-spinner' ).removeClass( 'is-active' );

						if (response.success) {
							// response.data.added has the successfully imported WC products
							// response.data.failure has the encrypted md5 SKUs that throw error, could be used for remark these rows
							// response.data.ignored has the encrypted md5 SKUs that already exists in woocommerce, could be used for remark these rows
							// response.data.wc_data_header has the WC products header.

							// SKUs in failure and ignored, came encrypted with md5 for avoiding rare characters conforming selector.

							if (response.data.added.length > 0) {
								appendTableRows( false, response.data.added, '#existent-sku-table', response.data.wc_data_header );

								response.data.added.forEach(
									function (item, index) {
										$( '#pending-' + item.md5_sku ).closest( 'tr' ).remove();
									}
								);

							}

						}

					}
				);
			}
		);

		/**
		 * Appends the needed row in correct table, according the parameter "forPending", that means the out of sync amazon products
		 *
		 * @isFirstIteration is used to determine whether, the previously rendered table, must be cleared
		 * @isLastIteration is used to determine when to place the Sync button */
		function fillDataTable(forPending, data, isFirstIteration, isLastIteration) {
			let title = forPending ? 'Products only in Amazon' : 'Products already in WooCommerce';

			let groupName = forPending ? 'pending' : 'existent';

			let tableSelector = '#' + groupName + '-sku-table';

			let dataSet = forPending ? data.pending_inventory : data.added_inventory;

			let dataHeader = forPending ? data.sp_data_header : data.wc_data_header;

			if (isFirstIteration) {
				// cleans table in order to avoid data duplication.
				$( tableSelector ).html( '' );
			}

			if (dataSet.length > 0 || ! forPending) {

				var colspan = 1;

				if (isFirstIteration) {
					var rowToAppend = '<tr class="wc-sp-columns-header">';

					if (forPending) {
						rowToAppend += '<th><input type="checkbox" id="pending-check-all" ></th>';
					} else {
						rowToAppend += '<th><label class="switch" style="min-width: 80px">' + genExistentCheckbox( false, 0, true ) + '<div class="slider round"><span class="switch-on" style="min-width: 19px;">On</span><span class="switch-off" style="min-width: 19px;">Off</span></div></label></th>';
					}

					$.each(
						dataHeader,
						function (field, header) {
							rowToAppend += '<th>' + header + '</th>';
							colspan++;
						}
					);

					$( tableSelector ).append( rowToAppend + '</tr>' );

					$( tableSelector ).prepend( '<tr class="wc-sp-table-header"><th colspan="' + colspan + '"><h2>' + title + '</h2><div class="spinner sync-spinner"/></th></tr>' );
				} else {
					// colspan should be the dataHeader.length + 1. Wasn't able to get the length in associative array.
					$.each(
						dataHeader,
						function (field, header) {
							colspan++;
						}
					);
				}

				appendTableRows( forPending, dataSet, tableSelector, dataHeader );

				if (isLastIteration) {
					// Append the action controls.
					if (forPending) {
						$( tableSelector ).append( '<tr><th colspan="' + colspan + '"><input type="button" value="Import Selected Products into WooCommerce" id="synchronize-sku" data="' + groupName + '" class="synchronize-sku-button"></th></tr>' );
					} else {
						// Still unused.
					}
				}
			}

		}

		/**
		 * Appends products data rows in corresponding table, in the "More SKU" modal popup.
		 *
		 * @forPending determine the format of a row to be appended looking at if it is pending product or an existent product
		 * @dataSet is the set of product to be appended
		 * @tableSelector is the jQuery selector for the table tha will be modified
		 * @dataHeader has information about the structure of the table columns */
		function appendTableRows(forPending, dataSet, tableSelector, dataHeader) {
			dataSet.forEach(
				function (item, index) {

					let rowClass = index % 2 == 0 ? 'wc-sp-pair-row' : 'wc-sp-odd-row';

					var rowToAdd = '<tr class="' + rowClass + '">';

					if (forPending) {
						rowToAdd += '<td>' + genPendingCheckbox( 'pending-' + item.md5_sku, item.string_value ) + '</td>';
					} else {
						rowToAdd += '<td><label class="switch" style="min-width: 80px">' + genExistentCheckbox( item.ns_fba_is_fulfill, item.id, false ) + '<div class="slider round"><span class="switch-on" style="min-width: 19px;">On</span><span class="switch-off" style="min-width: 19px;">Off</span></div></label></td>';
					}

					$.each(
						dataHeader,
						function (field, header) {
							rowToAdd += '<td>' + eval( "item." + field ) + '</td>';
						}
					);

					rowToAdd += '</tr>';
					$( tableSelector ).append( rowToAdd );
				}
			);
		}

		/**
		 * Perform commons actions after the ajax callback has been processed
		 *
		 * @param response the ajax response
		 * @param button the control that fired the event */
		function finalizeAjaxCall(response, button) {
			let message_class = response.success ? 'ns-fba-success-label' : 'ns-fba-error-label';
			let message_text  = '';

			let buttonName = button.attr( 'name' );

			// Most of buttons share the ajax post processing functionality defined in default case.
			switch ( buttonName ) {
				case 'ns_fba_sp_api_check_skus':
					message_text = '';

					if ( ! response.success ) {
						message_text = response.data;
					}

					break;
				case 'ns_fba_sp_api_sync_inventory':
					message_text = response.data.message;

					$( '#last-inventory-sync-date-container' ).html( response.data.last_inventory_sync_date );
					break;
				default:
					message_text = response.data ? response.data : 'Error, no response received.';
					break;
			}

			if ( '' !== message_text ) {
				button.after( '<span class="' + message_class + '">' + message_text + ' <u>Dismiss</u></span>' );
			}

			button.removeAttr( "disabled" );
		}

		/**
		 * Return a checkbox, with specific building patterns, to be used in "Woocommerce Products" table (existent products)
		 *
		 * @checkedStatus is a bool parameter for render the control checked|unchecked
		 * @value is the input value
		 * @isGroupControl determine if it's the "check-all" control for the table*/
		function genExistentCheckbox(checkedStatus, value, isGroupControl){
			let checkedStatusStr = checkedStatus ? 'checked="checked"' : '';
			let idStr            = isGroupControl ? 'id="ns-fba-fulfill-all"' : '';

			return '<input ' + idStr + ' type="checkbox" ' + checkedStatusStr + ' class="ns-fba-fulfill-product" value="' + value + '">';
		}

		/**
		 * Return a checkbox, with specific building patterns, to be used in "Seller Partner Products" table (pending products)
		 *
		 * @id the control id
		 * @value the input value */
		function genPendingCheckbox(id, value){
			return '<input type="checkbox" id="' + id + '" value="' + value + '" class="chk-pending-sku">';
		}

		// Handles the "check-all" event in pending table.
		$( document ).on(
			'change',
			'#pending-check-all',
			function () {

				$( '.chk-pending-sku' ).closest( 'td' ).each(
					function(){
						let chkControl = $( this ).find( '.chk-pending-sku' );
						$( this ).html( genPendingCheckbox( chkControl.attr( 'id' ), chkControl.val() ) );
					}
				);

				$( '.chk-pending-sku' ).attr( 'checked', $( this ).prop( 'checked' ) );
			}
		);

		// Dismiss ajax button messages.
		$( document ).on(
			'click',
			'.ns-fba-success-label u, .ns-fba-error-label u',
			function () {
				$( this ).parent().fadeOut();
			}
		);

		$( document ).on(
			'change',
			'.ns-fba-fulfill-product',
			function (event) {
				let wc_product_id = $( this ).val();
				let fulfill       = $( this ).is( ':checked' ) ? 'yes' : 'no';
				let data          = [];

				if (wc_product_id > 0) {
					data = [
						{
							'wc_product_id': wc_product_id,
							'ns_fba_fulfill': fulfill
						}
					];
				} else {
					$( '.ns-fba-fulfill-product' ).each(
						function (e) {
							if ($( this ).val() > 0) {
								data.push(
									{
										'wc_product_id': $( this ).val(),
										'ns_fba_fulfill': fulfill
									}
								);
							}
						}
					);
				}

				$.post(
					ajaxurl,
					{
						action: 'toggle_ns_fba_fulfill',
						nonce: ns_fba.nonce,
						data: data,
					},
					function (response) {
						if (response.success) {
							if ( 0 === wc_product_id ) {

								// wc_product_id == 0 means that the action was originated by "check-all" control
								// so all products toggle must be turned on.
								$( '.ns-fba-fulfill-product' ).closest( 'label' ).each(
									function(){
										let chkControl = $( this ).find( '.ns-fba-fulfill-product' );

										// Avoid change in "check-all" control.
										if (chkControl.val() > 0) {
											// This is a little bit tricky solution.
											// Due an inserted pseudo element, changing the checked property is not enough,
											// for that reason the hole control is removed and rendered again.
											chkControl.remove();
											$( this ).prepend( genExistentCheckbox( $( '#ns-fba-fulfill-all' ).prop( 'checked' ), chkControl.val(), false ) );
										}
									}
								);

							}
						} else {
							// If action fails the checkbox (toggle) controls must be rolled back depending of
							// which control starts the action.
							if ( wc_product_id > 0 ) {
								data.forEach(
									function (item, index) {
										let chkControl = $( '.ns-fba-fulfill-product[value="' + item.wc_product_id + '"]' );
										chkControl.closest( 'label' ).prepend( genExistentCheckbox( fulfill === 'yes' ? false : true, item.wc_product_id ) );
										chkControl.remove();
									}
								);
							} else {
								let chkControl = $( '#ns-fba-fulfill-all' );
								chkControl.closest( 'label' ).prepend( genExistentCheckbox( fulfill === 'yes' ? false : true, 0, true ) );
								chkControl.remove();
							}
						}

					}
				);

			}
		);

		let chk_enable_shipping_method_mapping = $( '#woocommerce_fba_ns_fba_enable_shipping_method_mapping' );

		if ( chk_enable_shipping_method_mapping.length > 0 && ! chk_enable_shipping_method_mapping.attr( 'checked' ) ) {
			$( '#woocommerce_fba_ns_fba_shipping_speed_standard' ).closest( 'tr' ).remove();
			$( '#woocommerce_fba_ns_fba_shipping_speed_expedited' ).closest( 'tr' ).remove();
			$( '#woocommerce_fba_ns_fba_shipping_speed_priority' ).closest( 'tr' ).remove();
		}

		/**
		 * Update the login url to match the region.
		 */
		function refreshMarketPlaceURL() {
			if ( $( '.ns_fba_login_with_amazon' ).length ) {
				let marketplace = $( '#woocommerce_fba_ns_fba_marketplace_id' ).find(":selected").val();
				var button = $( '.ns_fba_login_with_amazon' );
				if (marketplace) {
					$.ajax(
						{
							url      : ajaxurl,
							type     : 'POST',
							dataType : 'json',
							cache    : false,
							data     : {
								action : 'ns_fba_refresh_marketplace_link',
								nonce  : ns_fba.nonce,
								marketplace: marketplace
							},
							success: function (result) {
								if (result.success) {
									button.attr( 'href', result.data );
								}
							}
						}
					);
				}
			}
		}

	}
);
