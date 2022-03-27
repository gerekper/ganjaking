const $ = window.jQuery;

export default class GPIChoiceInventory {
	public initialized: boolean = false;

	public choicesClaimedInventory: { [choiceValue: string]: string } | undefined

	public alwaysShowInventoryLimit: boolean = window.GPI_ADMIN.alwaysShowInventoryLimitInEditor;

	constructor() {
		$(document).on('gform_load_field_settings', this.onLoadFieldSettings);
	}

	isSupportedFieldType = (field: GravityFormsField) : boolean => {
		return (window as any).GPIInstance.isSupportedChoiceFieldType(field);
	}

	onLoadFieldSettings = () => {
		if (!this.isSupportedFieldType(window.field)) {
			$( 'li.choices_setting' ).removeClass( 'gp-inventory-choices' );
			return;
		} else {
			this.choicesClaimedInventory = undefined;

			// add inventory class to choice setting
			$( 'li.choices_setting' ).addClass( 'gp-inventory-choices' );

			// add inv header if does not exists
			if ( ! $( '.gfield_choice_header_inv' ).length) {
				$( '.gfield_choice_header_price' ).after( '<label class="gfield_choice_header_inv">Inv.</label>' );
			}

			this.toggleEnableInventoryLimits();

			// init choice inputs
			this.addChoiceInventoryInputs();

			if (window.field.gpiInventory) {
				this.getChoicesClaimedInventory();
			}

			// only bind once for sorting action
			if ( ! this.initialized ) {

				$( '#field_choices' ).bind('sortupdate',  () => {
					// was firing before GF's update function
					setTimeout( this.addChoiceInventoryInputs, 1 );
				});

				$( document ).on('gform_load_field_choices', this.addChoiceInventoryInputs);

			}

			this.initialized = true;

		}

		this.initialized = false;
	};

	getChoicesClaimedInventory = () => {
		if (this.alwaysShowInventoryLimit) {
			return;
		}

		const usingScopes: boolean = !!(window.field.gpiInventory === 'advanced'
			&& window.field.gpiResource
			&& window.field.gpiResourcePropertyMap
			&& Object.keys(window.field.gpiResourcePropertyMap).length);

		/**
		 * If there are entries for the choice, then we show a "Current Inventory" setting instead of the
		 * "Inventory Limit" setting. This makes it more intuitive to make inventory adjustments.
		 */
		if ( window.has_entry(window.field.id) && !usingScopes ) {
			$( 'ul#field_choices li .field-choice-inventory-limit' )
				.css({ cursor: 'progress', pointerEvents: 'auto' })
				.attr('disabled', 'disabled');

			$.post(window.ajaxurl, {
				fieldId: window.field.id,
				formId: window.field.formId,
				security: window.GPI_ADMIN.nonce,
				action: 'gpi_get_choices_current_inventory_claimed',
			}, null, 'json').done( (claimedInventories) => {
				this.choicesClaimedInventory = claimedInventories;

				this.addChoiceInventoryInputs();
			// If claimed inventory can't be fetched, re-enable the Inventory Limit input.
			}).fail(() => {
				$( 'ul#field_choices li .field-choice-inventory-limit' )
					.css({ cursor: '', pointerEvents: '' })
					.removeAttr('disabled');
			});
		}
	};

	addChoiceInventoryInputs = () => {
		const self = this;

		jQuery( 'ul#field_choices li' ).each(function (i) {

			var invLimitValue = typeof window.field.choices[i]['inventory_limit'] != 'undefined' ? window.field.choices[i]['inventory_limit'] : '';
			var choiceValue = window.field.choices[i].value;

			jQuery(this).find('.field-choice-current-inventory, .field-choice-inventory-limit').remove();

			if (self.choicesClaimedInventory) {
				let currentInventory = parseInt(invLimitValue ?? 0);
				const claimedInventory = parseInt(self.choicesClaimedInventory?.[choiceValue] ?? 0);

				if (claimedInventory) {
					currentInventory -= claimedInventory;
				}

				// add inventory limit input
				jQuery( this )
					.find( 'input.field-choice-input:last' )
					.after(`<input type="text"
					class="field-choice-input field-choice-current-inventory gform-input"
					value="${currentInventory}"
					data-claimed-inventory="${claimedInventory}"
					onkeyup="window.GPIInstance.choiceInventory.setChoiceLimit(${i}, parseInt(this.value) + parseInt(jQuery(this).data('claimed-inventory')))"
					onblur="window.GPIInstance.choiceInventory.setChoiceLimit(${i}, parseInt(this.value) + parseInt(jQuery(this).data('claimed-inventory')))"
				/>`);
			} else {
				// add inventory limit input
				jQuery( this )
					.find( 'input.field-choice-input:last' )
					.after(`<input type="text"
					class="field-choice-input field-choice-inventory-limit gform-input"
					value="${invLimitValue}"
					onkeyup="window.GPIInstance.choiceInventory.setChoiceLimit(${i}, this.value)"
					onblur="window.GPIInstance.choiceInventory.setChoiceLimit(${i}, this.value)"
				/>`);
			}
		});
	};

	setChoiceLimit = (index: number, value: number) => {
		window.field.choices[index]['inventory_limit'] = value;
	};

	toggleEnableInventoryLimits = () => {
		if (window.field.gpiInventory) {
			jQuery( 'li.gp-inventory-choices.field_setting' ).addClass( 'inventory-limits-enabled limits-enabled' );
		} else {
			jQuery( 'li.gp-inventory-choices.field_setting' ).removeClass( 'inventory-limits-enabled limits-enabled' );
		}
	};
}
