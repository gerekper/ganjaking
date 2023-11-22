jQuery(function($) {

	const selectorAddonsWrapper = '#product_addons_data';
	const selectorAddonItem = '.wc-pao-addon';
	const selectorLegacyAddonType = '.wc-pao-addon-type-select'; // Up to v6.4.7.
	const selectorAddonType = selectorLegacyAddonType + ', .product_addon_type';
	const selectorToggleManualSecondaryPrices = '.wcml_custom_prices_input';

	$(selectorAddonItem).each(function () {
		setupMulticurrencyDialogForItem($(this));
	});

	insertToggleManualPricesOnGlobalAddons();
	watchChangesOnAddonItems();

	$(document).on('click', selectorAddonsWrapper + ' .js-wcml-dialog-trigger', onTriggerOpenDialog);
	$(document).on('click', '.wcml_product_addons_apply_prices', onDialogClickSave);
	$(document).on('change', selectorToggleManualSecondaryPrices, onToggleManualSecondaryPricesCheckbox);
	$(document).on('change', '.wc-pao-addon-type-select', onChangeAddonType);

	function onTriggerOpenDialog() {
		const dialog = $(this).parent().find('.wcml-dialog');
		const priceInDefaultCurrency = $(this).parent().parent().find('input.wc_input_price').val();
		const addonType = $(this).closest(selectorAddonItem).find(selectorAddonType).val();
		let dialogLabel;

		dialog.find('.default-price strong').html(priceInDefaultCurrency ? priceInDefaultCurrency : 0);

		if (isAddonTypeWithMultipleOptions(addonType)) {
			dialogLabel = $(this).parent().parent().find('.wc-pao-addon-content-label input[type="text"]').val();
		} else {
			dialogLabel = $(this).closest('.wc-pao-addon-content').find('.wc-pao-addon-title input[type="text"]').val();
		}

		if (dialogLabel) {
			dialog.find('p>strong').html(dialogLabel);
		}
	}

	function onDialogClickSave() {
		const dialog = $(this).closest('.wcml-ui-dialog');
		const dialogId = $(this).data('dialog');

		dialog.find('.wc_input_price').each(function () {
			$('.wcml-dialog#' + dialogId).find('input[name="' + $(this).attr('name') + '"]').attr('value', $(this).val());
		});

		dialog.find('.wcml-dialog-close-button').trigger('click');
	}

	function onToggleManualSecondaryPricesCheckbox() {
		const manualPricesInAddons = selectorAddonsWrapper + ' .js-wcml-option-prices';
		const isEnabled = isManualSecondaryPricesEnabled();

		$(manualPricesInAddons).each(function () {
			isEnabled ? $(this).removeClass('hidden') : $(this).addClass('hidden');
		});
	}

	function insertToggleManualPricesOnGlobalAddons() {
		const wrapperInGlobalAddons = $('.wcml_custom_prices');

		if(wrapperInGlobalAddons.length > 0){
			wrapperInGlobalAddons.insertAfter( $('.global-addons-form tr:eq(2)') ).show();
		}
	}

	function setupMulticurrencyDialogForItem(addonItem) {
		const type = addonItem.find(selectorAddonType).val();

		addonItem.find('.wc-pao-addon-adjust-price-settings').append(addonItem.find('.wc-pao-addon-content>.wcml-option-prices'));

		addonItem.find('.wcml-option-prices .wc_input_price').each(function () {
			$(this).prop('disabled', false);
		});
	}

	/**
	 * @deprecated
	 *
	 * Used up to v6.4.7.
	 * Starting from v6.5.0, it's not possible to change an item type anymore.
	 */
	function onChangeAddonType(event) {
		const typeSelect = $(event.target);
		const addonItem = typeSelect.closest(selectorAddonItem);
		const isMultipleOptions = isAddonTypeWithMultipleOptions(typeSelect.val());

		addonItem.find('.wc-pao-addon-content-option-rows .wcml-option-prices .wc_input_price').each(function () {
			jQuery(this).prop('disabled', !isMultipleOptions);
		});
		addonItem.find('.wc-pao-addon-adjust-price-settings .wcml-option-prices .wc_input_price').each(function () {
			jQuery(this).prop('disabled', isMultipleOptions);
		});
	}

	function maybeSetPricesDialogButtonVisible(addonItem) {
		if ( isManualSecondaryPricesEnabled() ) {
			addonItem.find('.js-wcml-option-prices').each(function () {
				$(this).removeClass('hidden');
			});
		}
	}

	function isManualSecondaryPricesEnabled() {
		return $(selectorToggleManualSecondaryPrices + ':checked').val() == 1;
	}

	function isAddonTypeWithMultipleOptions(addonType) {
		return 'multiple_choice' === addonType || 'checkbox' === addonType;
	}

	function watchChangesOnAddonItems() {
		const addonItems = document.querySelector(selectorAddonsWrapper + ' .wc-pao-addons');

		if (addonItems) {
			const onAddonsChange = function(mutations) {
				mutations.forEach(function(mutation) {
					if (mutation.addedNodes.length) {
						mutation.addedNodes.forEach(function(addedNode) {
							const addonItem = $(addedNode);

							setupMulticurrencyDialogForItem(addonItem);
							maybeSetPricesDialogButtonVisible(addonItem);
						})
					}
				})
			};

			const addonsChangeObserver = new MutationObserver(onAddonsChange);
			addonsChangeObserver.observe(addonItems, { childList: true, subtree: true });
		}
	}
});
