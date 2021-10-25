/* global wpforms_builder, wpf, List, jconfirm, wpforms_panel_switch, Choices, WPForms, WPFormsFormEmbedWizard */

var WPFormsBuilder = window.WPFormsBuilder || ( function( document, window, $ ) {

	var s,
		$builder,
		elements = {};

	/**
	 * Whether to show the close confirmation dialog or not.
	 *
	 * @since 1.6.0
	 *
	 * @type {boolean}
	 */
	var closeConfirmation = true;

	var app = {

		settings: {
			spinner:          '<i class="fa fa-spinner fa-spin"></i>',
			spinnerInline:    '<i class="fa fa-spinner fa-spin wpforms-loading-inline"></i>',
			tinymceDefaults:  { tinymce: { toolbar1: 'bold,italic,underline,blockquote,strikethrough,bullist,numlist,alignleft,aligncenter,alignright,undo,redo,link' }, quicktags: true },
			pagebreakTop:     false,
			pagebreakBottom:  false,
			upload_img_modal: false,
		},

		/**
		 * Start the engine.
		 *
		 * @since 1.0.0
		 */
		init: function() {

			var that = this;

			wpforms_panel_switch = true;
			s = this.settings;

			// Document ready.
			$( app.ready );

			// Page load.
			$( window ).on( 'load', function() {

				// In the case of jQuery 3.+, we need to wait for a ready event first.
				if ( typeof $.ready.then === 'function' ) {
					$.ready.then( app.load );
				} else {
					app.load();
				}
			} );

			$( window ).on( 'beforeunload', function() {
				if ( ! that.formIsSaved() && closeConfirmation ) {
					return wpforms_builder.are_you_sure_to_close;
				}
			} );
		},

		/**
		 * Page load.
		 *
		 * @since 1.0.0
		 */
		load: function() {

			// Remove Loading overlay.
			$( '#wpforms-builder-overlay' ).fadeOut();

			// Maybe display informational modal.
			if ( wpforms_builder.template_modal_display == '1' && 'fields' === wpf.getQueryString('view') ) {
				$.alert( {
					title: wpforms_builder.template_modal_title,
					content: wpforms_builder.template_modal_msg,
					icon: 'fa fa-info-circle',
					type: 'blue',
					buttons: {
						confirm: {
							text: wpforms_builder.close,
							btnClass: 'btn-confirm',
							keys: [ 'enter' ],
						},
					},
				} );
			}
		},

		/**
		 * Document ready.
		 *
		 * @since 1.0.0
		 */
		ready: function() {

			if ( app.isVisitedViaBackButton() ) {
				location.reload();

				return;
			}

			// Cache builder element.
			$builder = $( '#wpforms-builder' );

			// Cache other elements.
			elements.$fieldOptions       = $( '#wpforms-field-options' );
			elements.$sortableFieldsWrap = $( '.wpforms-field-wrap' );
			elements.$noFieldsOptions    = $( '.wpforms-no-fields-holder .no-fields' );
			elements.$noFieldsPreview    = $( '.wpforms-no-fields-holder .no-fields-preview' );

			// Remove Embed button if builder opened in popup.
			if ( app.isBuilderInPopup() ) {
				$( '#wpforms-embed' ).remove();
			}

			// Bind all actions.
			app.bindUIActions();

			// Trigger initial save for new forms.
			var newForm = wpf.getQueryString( 'newform' );
			if ( newForm ) {
				app.formSave( false );
			}

			// Setup/cache some vars not available before
			s.formID = $( '#wpforms-builder-form' ).data( 'id' );
			s.pagebreakTop = $( '.wpforms-pagebreak-top' ).length;
			s.pagebreakBottom = $( '.wpforms-pagebreak-bottom' ).length;
			s.templateList = new List( 'wpforms-setup-templates-additional', {
				valueNames: [ 'wpforms-template-name' ],
			} );

			// Disable implicit submission for every form inside the builder.
			// All form values are managed by JS and should not be submitted by pressing Enter.
			$builder.on( 'keypress', '#wpforms-builder-form :input:not(textarea)', function( e ) {
				if ( e.keyCode === 13 ) {
					e.preventDefault();
				}
			} );

			// If there is a section configured, display it.
			// Otherwise we show the first panel by default.
			$( '.wpforms-panel' ).each( function( index, el ) {
				var $this = $( this ),
					$configured = $this.find( '.wpforms-panel-sidebar-section.configured' ).first();

				if ( $configured.length ) {
					var section = $configured.data( 'section' );
					$configured.addClass( 'active' ).find( '.wpforms-toggle-arrow' ).toggleClass( 'fa-angle-down fa-angle-right' );
					$this.find( '.wpforms-panel-content-section-' + section ).show().addClass( 'active' );
				} else {
					$this.find( '.wpforms-panel-content-section:first-of-type' )
						.show()
						.addClass( 'active' );
					$this.find( '.wpforms-panel-sidebar-section:first-of-type' )
						.addClass( 'active' )
						.find( '.wpforms-toggle-arrow' )
						.toggleClass( 'fa-angle-down fa-angle-right' );
				}
			} );

			// Drag and drop sortable elements.
			app.fieldSortable();
			app.fieldChoiceSortable( 'select' );
			app.fieldChoiceSortable( 'radio' );
			app.fieldChoiceSortable( 'checkbox' );
			app.fieldChoiceSortable( 'payment-multiple' );
			app.fieldChoiceSortable( 'payment-checkbox' );
			app.fieldChoiceSortable( 'payment-select' );

			// Load match heights.
			$( '.wpforms-setup-templates.core .wpforms-template-inner' ).matchHeight( {
				byRow: false,
			} );
			$( '.wpforms-setup-templates.additional .wpforms-template-inner' ).matchHeight( {
				byRow: false,
			} );

			// Set field group visibility.
			$( '.wpforms-add-fields-group' ).each( function( index, el ) {
				app.fieldGroupToggle( $( this ), 'load' );
			} );

			app.registerTemplates();

			// Trim long form titles.
			app.trimFormTitle();

			// Load Tooltips.
			wpf.initTooltips();

			// Load Color Pickers.
			app.loadColorPickers();

			// Hide/Show CAPTCHA in form.
			app.captchaToggle();

			// Confirmations initial setup
			app.confirmationsSetup();

			// Notification settings.
			app.notificationToggle();
			app.notificationsByStatusAlerts();

			// Secret builder hotkeys.
			app.builderHotkeys();

			// Clone form title to setup page.
			$( '#wpforms-setup-name' ).val( $( '#wpforms-panel-field-settings-form_title' ).val() );

			// jquery-confirm defaults.
			jconfirm.defaults = {
				closeIcon: true,
				backgroundDismiss: true,
				escapeKey: true,
				animationBounce: 1,
				useBootstrap: false,
				theme: 'modern',
				boxWidth: '400px',
				animateFromElement: false,
			};

			app.dropdownField.init();

			app.initSomeFieldOptions();

			app.dismissNotice();
		},

		/**
		 * Builder was visited via back button in browser.
		 *
		 * @since 1.6.5
		 *
		 * @returns {boolean} True if the builder was visited via back button in browser.
		 */
		isVisitedViaBackButton: function() {

			if ( ! performance ) {
				return false;
			}

			var isVisitedViaBackButton = false;

			performance.getEntriesByType( 'navigation' ).forEach( function( nav ) {

				if ( nav.type === 'back_forward' ) {
					isVisitedViaBackButton = true;
				}
			} );

			return isVisitedViaBackButton;
		},

		/**
		 * Initialize some fields options controls.
		 *
		 * @since 1.6.3
		 */
		initSomeFieldOptions: function() {

			// Show a toggled options groups.
			app.toggleAllOptionGroups( $builder );

			// Date/Time field Date type option.
			$builder.find( '.wpforms-field-option-row-date .type select' ).trigger( 'change' );
		},

		/**
		 * Dropdown field component.
		 *
		 * @since 1.6.1
		 */
		dropdownField: {

			/**
			 * Field configuration.
			 *
			 * @since 1.6.1
			 */
			config: {
				modernClass: 'choicesjs-select',
				args: {
					searchEnabled: false,
					searchChoices: false,
					shouldSort: false,
					callbackOnInit: function() {

						// Turn off disabled styles.
						if ( $( this.containerOuter.element ).hasClass( 'is-disabled' ) ) {
							$( this.containerOuter.element ).removeClass( 'is-disabled' );
						}

						if ( this.passedElement.element.multiple ) {

							// Hide a placeholder if field has selected choices.
							if ( this.getValue( true ).length ) {
								$( this.input.element ).addClass( 'wpforms-hidden' );
							}
						}
					},
				},
			},

			/**
			 * Initialization for field component.
			 *
			 * @since 1.6.1
			 */
			init: function() {

				// Choices.js init.
				$builder.find( '.' + app.dropdownField.config.modernClass ).each( function() {
					app.dropdownField.events.choicesInit( $( this ) );
				} );

				// Multiple option.
				$builder.on(
					'change',
					'.wpforms-field-option-select .wpforms-field-option-row-multiple input',
					app.dropdownField.events.multiple
				);

				// Style option.
				$builder.on(
					'change',
					'.wpforms-field-option-select .wpforms-field-option-row-style select, .wpforms-field-option-payment-select .wpforms-field-option-row-style select',
					app.dropdownField.events.applyStyle
				);
			},

			/**
			 * Field events.
			 *
			 * @since 1.6.1
			 */
			events: {

				/**
				 * Load Choices.js library.
				 *
				 * @since 1.6.1
				 *
				 * @param {object} $element jQuery element selector.
				 */
				choicesInit: function( $element ) {

					var instance = new Choices( $element[0], app.dropdownField.config.args );

					app.dropdownField.helpers.setInstance( $element, instance );
					app.dropdownField.helpers.addPlaceholderChoice( $element, instance );
				},

				/**
				 * Multiple option callback.
				 *
				 * @since 1.6.1
				 *
				 * @param {object} event Event object.
				 */
				multiple: function( event ) {

					var fieldId             = $( this ).closest( '.wpforms-field-option-row-multiple' ).data().fieldId,
						$primary            = app.dropdownField.helpers.getPrimarySelector( fieldId ),
						$optionChoicesItems = $( '#wpforms-field-option-row-' + fieldId + '-choices input.default' ),
						$placeholder        = $primary.find( '.placeholder' ),
						isDynamicChoices    = app.dropdownField.helpers.isDynamicChoices( fieldId ),
						isMultiple          = event.target.checked,
						choicesType         = isMultiple ? 'checkbox' : 'radio',
						selectedChoices;

					// Add/remove a `multiple` attribute.
					$primary.prop( 'multiple', isMultiple );

					// Change a `Choices` fields type:
					//    checkbox - needed for multiple selection
					//    radio - needed for single selection
					$optionChoicesItems.prop( 'type', choicesType );

					// Dynamic Choices doesn't have default choices (selected options) - make all as unselected.
					if ( isDynamicChoices ) {
						$primary.find( 'option:selected' ).prop( 'selected', false );
					}

					// Gets default choices.
					selectedChoices = $optionChoicesItems.filter( ':checked' );

					if ( ! isMultiple && selectedChoices.length ) {

						// Uncheck all choices.
						$optionChoicesItems.prop( 'checked', false );

						// For single selection we can choose only one.
						$( selectedChoices.get( 0 ) ).prop( 'checked', true );
					}

					// Toggle selection for a placeholder option based on a select type.
					if ( $placeholder.length ) {
						$placeholder.prop( 'selected', ! isMultiple );
					}

					// Update a primary field.
					app.dropdownField.helpers.update( fieldId, isDynamicChoices );
				},

				/**
				 * Apply a style to <select> - modern or classic.
				 *
				 * @since 1.6.1
				 */
				applyStyle: function() {

					var $field   = $( this ),
						fieldId  = $field.closest( '.wpforms-field-option-row-style' ).data().fieldId,
						fieldVal = $field.val();

					if ( 'modern' === fieldVal ) {
						app.dropdownField.helpers.convertClassicToModern( fieldId );

					} else {
						app.dropdownField.helpers.convertModernToClassic( fieldId );
					}
				},
			},

			helpers: {

				/**
				 * Get Modern select options and prepare them for the Classic <select>.
				 *
				 * @since 1.6.1
				 *
				 * @param {string} fieldId Field ID.
				 */
				convertModernToClassic: function( fieldId ) {

					var $primary         = app.dropdownField.helpers.getPrimarySelector( fieldId ),
						isDynamicChoices = app.dropdownField.helpers.isDynamicChoices( fieldId ),
						instance         = app.dropdownField.helpers.getInstance( $primary );

					// Destroy the instance of Choices.js.
					instance.destroy();

					// Update a placeholder.
					app.dropdownField.helpers.updatePlaceholderChoice( instance, fieldId );

					// Update choices.
					if ( ! isDynamicChoices ) {
						app.fieldChoiceUpdate( 'select', fieldId );
					}
				},

				/**
				 * Convert a Classic to Modern style selector.
				 *
				 * @since 1.6.1
				 *
				 * @param {string} fieldId Field ID.
				 */
				convertClassicToModern: function( fieldId ) {

					var $primary         = app.dropdownField.helpers.getPrimarySelector( fieldId ),
						isDynamicChoices = app.dropdownField.helpers.isDynamicChoices( fieldId );

					// Update choices.
					if ( ! isDynamicChoices ) {
						app.fieldChoiceUpdate( 'select', fieldId );
					}

					// Call a Choices.js initialization.
					app.dropdownField.events.choicesInit( $primary );
				},

				/**
				 * Update a primary field.
				 *
				 * @since 1.6.1
				 *
				 * @param {string} fieldId Field ID.
				 * @param {boolean} isDynamicChoices True if `Dynamic Choices` is turned on.
				 */
				update: function( fieldId, isDynamicChoices ) {

					var $primary = app.dropdownField.helpers.getPrimarySelector( fieldId );

					if ( app.dropdownField.helpers.isModernSelect( $primary ) ) {

						// If we had a `Modern` select before, then we need to make re-init - destroy() + init().
						app.dropdownField.helpers.convertModernToClassic( fieldId );
						app.dropdownField.events.choicesInit( $primary );

					} else {

						// Update choices.
						if ( ! isDynamicChoices ) {
							app.fieldChoiceUpdate( 'select', fieldId );
						}
					}
				},

				/**
				 * Add a new choice to behave like a placeholder.
				 *
				 * @since 1.6.1
				 *
				 * @param {object} $jquerySelector jQuery primary selector.
				 * @param {object} instance The instance of Choices.js.
				 *
				 * @returns {boolean} False if a fake placeholder wasn't added.
				 */
				addPlaceholderChoice: function( $jquerySelector, instance ) {

					var fieldId     = $jquerySelector.closest( '.wpforms-field' ).data().fieldId,
						hasDefaults = app.dropdownField.helpers.hasDefaults( fieldId );

					if ( app.dropdownField.helpers.isDynamicChoices( fieldId ) ) {
						hasDefaults = false;
					}

					// Already has a placeholder.
					if ( false !== app.dropdownField.helpers.searchPlaceholderChoice( instance ) ) {

						return false;
					}

					// No choices.
					if ( ! instance.config.choices.length ) {

						return false;
					}

					var placeholder = instance.config.choices[0].label,
						isMultiple  = $( instance.passedElement.element ).prop( 'multiple' ),
						selected    = ! ( isMultiple || hasDefaults );

					// Add a new choice as a placeholder.
					instance.setChoices(
						[
							{ value: '', label: placeholder, selected: selected, placeholder: true },
						],
						'value',
						'label',
						false
					);

					// Additional case for multiple select.
					if ( isMultiple ) {
						$( instance.input.element ).prop( 'placeholder', placeholder );
					}

					return true;
				},

				/**
				 * Search a choice-placeholder item.
				 *
				 * @since 1.6.1
				 *
				 * @param {object} instance The instance of Choices.js.
				 *
				 * @returns {boolean|object} False if a field doesn't have a choice-placeholder. Otherwise - return choice item.
				 */
				searchPlaceholderChoice: function( instance ) {

					var find = false;

					instance.config.choices.forEach( function( item, i, choices ) {

						if ( 'undefined' !== typeof item.placeholder && true === item.placeholder ) {
							find = {
								key: i,
								item: item,
							};

							return false;
						}
					} );

					return find;
				},

				/**
				 * Add/update a placeholder.
				 *
				 * @since 1.6.1
				 *
				 * @param {object} instance The instance of Choices.js.
				 * @param {string} fieldId Field ID.
				 */
				updatePlaceholderChoice: function( instance, fieldId ) {

					var $primary           = $( instance.passedElement.element ),
						placeholderValue   = wpf.sanitizeHTML( $( '#wpforms-field-option-' + fieldId + '-placeholder' ).val() ),
						placeholderChoice  = app.dropdownField.helpers.searchPlaceholderChoice( instance ),
						$placeholderOption = {};

					// Get an option with placeholder.
					// Note: `.placeholder` class is skipped when calling Choices.js destroy() method.
					if ( 'object' === typeof placeholderChoice ) {
						$placeholderOption = $( $primary.find( 'option' ).get( placeholderChoice.key ) );
					}

					// We have a placeholder and need to update the UI with it.
					if ( '' !== placeholderValue ) {
						if ( ! $.isEmptyObject( $placeholderOption ) && $placeholderOption.length ) {

							// Update a placeholder option.
							$placeholderOption
								.addClass( 'placeholder' )
								.text( placeholderValue );

						} else {

							// Add a placeholder option.
							$primary.prepend( '<option value="" class="placeholder">' + placeholderValue + '</option>' );
						}

					} else {

						// Remove the placeholder as it's empty.
						if ( $placeholderOption.length ) {
							$placeholderOption.remove();
						}
					}
				},

				/**
				 * Is it a `Modern` style dropdown field?
				 *
				 * @since 1.6.1
				 *
				 * @param {object} $jquerySelector jQuery primary selector.
				 *
				 * @returns {boolean} True if it's a `Modern` style select, false otherwise.
				 */
				isModernSelect: function( $jquerySelector ) {

					var instance = app.dropdownField.helpers.getInstance( $jquerySelector );

					if ( 'object' !== typeof instance ) {
						return false;
					}

					if ( $.isEmptyObject( instance ) ) {
						return false;
					}

					return instance.initialised;
				},

				/**
				 * Save an instance of Choices.js.
				 *
				 * @since 1.6.1
				 *
				 * @param {object} $jquerySelector jQuery primary selector.
				 * @param {object} instance The instance of Choices.js.
				 */
				setInstance: function( $jquerySelector, instance ) {

					$jquerySelector.data( 'choicesjs', instance );
				},

				/**
				 * Retrieve an instance of Choices.js.
				 *
				 * @since 1.6.1
				 *
				 * @param {object} $jquerySelector jQuery primary selector.
				 *
				 * @returns {object} The instance of Choices.js.
				 */
				getInstance: function( $jquerySelector ) {

					return $jquerySelector.data( 'choicesjs' );
				},

				/**
				 * Is `Dynamic Choices` used?
				 *
				 * @since 1.6.1
				 *
				 * @param {string} fieldId Field ID.
				 *
				 * @returns {boolean} True if a `Dynamic Choices` active, false otherwise.
				 */
				isDynamicChoices: function( fieldId ) {

					var $fieldOption = $( '#wpforms-field-option-' + fieldId + '-dynamic_choices' );

					if ( ! $fieldOption.length ) {
						return false;
					}

					return '' !== $fieldOption.val();
				},

				/**
				 * Is a field has default choices?
				 *
				 * @since 1.6.1
				 *
				 * @param {string} fieldId Field ID.
				 *
				 * @returns {boolean} True if a field has default choices.
				 */
				hasDefaults: function( fieldId ) {

					var $choicesList = $( '#wpforms-field-option-row-' + fieldId + '-choices .choices-list' );

					return !! $choicesList.find( 'input.default:checked' ).length;
				},

				/**
				 * Retrieve a jQuery selector for the Primary field.
				 *
				 * @since 1.6.1
				 *
				 * @param {string} fieldId Field ID.
				 *
				 * @returns {object} jQuery primary selector.
				 */
				getPrimarySelector: function( fieldId ) {

					return $( '#wpforms-field-' + fieldId + ' .primary-input' );
				},
			},
		},

		/**
		 * Add number slider events listeners.
		 *
		 * @since 1.5.7
		 *
		 * @param {object} $builder JQuery object.
		 */
		numberSliderEvents: function( $builder ) {

			// Minimum update.
			$builder.on(
				'input',
				'.wpforms-field-option-row-min_max .wpforms-input-row .wpforms-number-slider-min',
				app.fieldNumberSliderUpdateMin
			);

			// Maximum update.
			$builder.on(
				'input',
				'.wpforms-field-option-row-min_max .wpforms-input-row .wpforms-number-slider-max',
				app.fieldNumberSliderUpdateMax
			);

			// Change default input value.
			$builder.on(
				'input',
				'.wpforms-number-slider-default-value',
				_.debounce( app.changeNumberSliderDefaultValue, 500 )
			);

			// Change step value.
			$builder.on(
				'input',
				'.wpforms-number-slider-step',
				_.debounce( app.changeNumberSliderStep, 500 )
			);

			// Check step value.
			$builder.on(
				'focusout',
				'.wpforms-number-slider-step',
				app.checkNumberSliderStep
			);

			// Change value display.
			$builder.on(
				'input',
				'.wpforms-number-slider-value-display',
				_.debounce( app.changeNumberSliderValueDisplay, 500 )
			);

			// Change min value.
			$builder.on(
				'input',
				'.wpforms-number-slider-min',
				_.debounce( app.changeNumberSliderMin, 500 )
			);

			// Change max value.
			$builder.on(
				'input',
				'.wpforms-number-slider-max',
				_.debounce( app.changeNumberSliderMax, 500 )
			);
		},

		/**
		 * Change number slider min option.
		 *
		 * @since 1.5.7
		 *
		 * @param {object} event Input event.
		 */
		changeNumberSliderMin: function( event ) {

			var fieldID = $( event.target ).parents( '.wpforms-field-option-row' ).data( 'fieldId' );
			var value   = parseFloat( event.target.value );

			if ( isNaN( value ) ) {
				return;
			}

			app.updateNumberSliderDefaultValueAttr( fieldID, event.target.value, 'min' );
		},

		/**
		 * Change number slider max option.
		 *
		 * @since 1.5.7
		 *
		 * @param {object} event Input event.
		 */
		changeNumberSliderMax: function( event ) {

			var fieldID = $( event.target ).parents( '.wpforms-field-option-row' ).data( 'fieldId' );
			var value   = parseFloat( event.target.value );

			if ( isNaN( value ) ) {
				return;
			}

			app.updateNumberSliderDefaultValueAttr( fieldID, event.target.value, 'max' )
				.updateNumberSliderStepValueMaxAttr( fieldID, event.target.value );
		},

		/**
		 * Change number slider value display option.
		 *
		 * @since 1.5.7
		 *
		 * @param {object} event Input event.
		 */
		changeNumberSliderValueDisplay: function( event ) {

			var str = event.target.value;
			var fieldID = $( event.target ).parents( '.wpforms-field-option-row' ).data( 'fieldId' );
			var defaultValue = document.getElementById( 'wpforms-field-option-' + fieldID + '-default_value' );

			if ( defaultValue ) {
				app.updateNumberSliderHintStr( fieldID, str )
					.updateNumberSliderHint( fieldID, defaultValue.value );
			}
		},

		/**
		 * Change number slider step option.
		 *
		 * @since 1.5.7
		 *
		 * @param {object} event Input event.
		 */
		changeNumberSliderStep: function( event ) {

			var value = parseFloat( event.target.value );

			if ( isNaN( value ) ) {
				return;
			}

			var max = parseFloat( event.target.max );
			var min = parseFloat( event.target.min );
			var fieldID = $( event.target ).parents( '.wpforms-field-option-row' ).data( 'fieldId' );

			if ( value <= 0 ) {
				return;
			}

			if ( value > max ) {
				event.target.value = max;

				return;
			}

			if ( value < min ) {
				event.target.value = min;

				return;
			}

			app.updateNumberSliderAttr( fieldID, value, 'step' )
				.updateNumberSliderDefaultValueAttr( fieldID, value, 'step' );
		},

		/**
		 * Check number slider step option.
		 *
		 * @since 1.6.2.3
		 *
		 * @param {object} event Focusout event object.
		 */
		checkNumberSliderStep: function( event ) {

			var value = parseFloat( event.target.value ),
				$input = $( this );

			if ( ! isNaN( value ) && value > 0 ) {
				return;
			}

			$.confirm( {
				title: wpforms_builder.heads_up,
				content: wpforms_builder.error_number_slider_increment,
				backgroundDismiss: false,
				closeIcon: false,
				icon: 'fa fa-exclamation-circle',
				type: 'orange',
				buttons: {
					confirm: {
						text: wpforms_builder.ok,
						btnClass: 'btn-confirm',
						keys: [ 'enter' ],
						action: function() {

							$input.val( '' ).focus();
						},
					},
				},
			} );
		},

		/**
		 * Change number slider default value option.
		 *
		 * @since 1.5.7
		 *
		 * @param {object} event Input event.
		 */
		changeNumberSliderDefaultValue: function( event ) {

			var value = parseFloat( event.target.value );

			if ( ! isNaN( value ) ) {
				var max     = parseFloat( event.target.max );
				var min     = parseFloat( event.target.min );
				var fieldID = $( event.target ).parents( '.wpforms-field-option-row-default_value' ).data( 'fieldId' );

				if ( value > max ) {
					event.target.value = max;

					return;
				}

				if ( value < min ) {
					event.target.value = min;

					return;
				}

				app.updateNumberSlider( fieldID, value )
					.updateNumberSliderHint( fieldID, value );
			}
		},

		/**
		 * Update number slider default value attribute.
		 *
		 * @since 1.5.7
		 *
		 * @param {number} fieldID Field ID.
		 * @param {*} newValue Default value attribute.
		 * @param {*} attr Attribute name.
		 *
		 * @returns {object} App instance.
		 */
		updateNumberSliderDefaultValueAttr: function( fieldID, newValue, attr ) {

			var input = document.getElementById( 'wpforms-field-option-' + fieldID + '-default_value' );

			if ( input ) {
				var value = parseFloat( input.value );

				input.setAttribute( attr, newValue );
				newValue = parseFloat( newValue );

				if ( 'max' === attr && value > newValue ) {
					input.value = newValue;
					$( input ).trigger( 'input' );
				}

				if ( 'min' === attr && value < newValue ) {
					input.value = newValue;
					$( input ).trigger( 'input' );
				}
			}

			return this;
		},

		/**
		 * Update number slider value.
		 *
		 * @since 1.5.7
		 *
		 * @param {number} fieldID Field ID.
		 * @param {string} value Number slider value.
		 *
		 * @returns {object} App instance.
		 */
		updateNumberSlider: function( fieldID, value ) {

			var numberSlider = document.getElementById( 'wpforms-number-slider-' + fieldID );

			if ( numberSlider ) {
				numberSlider.value = value;
			}

			return this;
		},

		/**
		 * Update number slider attribute.
		 *
		 * @since 1.5.7
		 *
		 * @param {number} fieldID Field ID.
		 * @param {mixed} value Attribute value.
		 * @param {*} attr Attribute name.
		 *
		 * @returns {object} App instance.
		 */
		updateNumberSliderAttr: function( fieldID, value, attr ) {

			var numberSlider = document.getElementById( 'wpforms-number-slider-' + fieldID );

			if ( numberSlider ) {
				numberSlider.setAttribute( attr, value );
			}

			return this;
		},

		/**
		 * Update number slider hint string.
		 *
		 * @since 1.5.7
		 *
		 * @param {number} fieldID Field ID.
		 * @param {string} str Hint string.
		 *
		 * @returns {object} App instance.
		 */
		updateNumberSliderHintStr: function( fieldID, str ) {

			var hint = document.getElementById( 'wpforms-number-slider-hint-' + fieldID );

			if ( hint ) {
				hint.dataset.hint = str;
			}

			return this;
		},

		/**
		 * Update number slider Hint value.
		 *
		 * @since 1.5.7
		 *
		 * @param {number} fieldID Field ID.
		 * @param {string} value Hint value.
		 *
		 * @returns {object} App instance.
		 */
		updateNumberSliderHint: function( fieldID, value ) {

			var hint = document.getElementById( 'wpforms-number-slider-hint-' + fieldID );

			if ( hint ) {
				hint.innerHTML = wpf.sanitizeHTML( hint.dataset.hint ).replace( '{value}', '<b>' + value + '</b>' );
			}

			return this;
		},

		/**
		 * Update min attribute.
		 *
		 * @since 1.5.7
		 *
		 * @param {object} event Input event.
		 */
		fieldNumberSliderUpdateMin: function( event ) {

			var $options = $( event.target ).parents( '.wpforms-field-option-row-min_max' );
			var max = parseFloat( $options.find( '.wpforms-number-slider-max' ).val() );
			var current = parseFloat( event.target.value );

			if ( isNaN( current ) ) {
				return;
			}

			if ( max <= current ) {
				event.preventDefault();
				this.value = max;

				return;
			}

			var fieldId = $options.data( 'field-id' );
			var numberSlider = $builder.find( '#wpforms-field-' + fieldId + ' input[type="range"]' );

			numberSlider.attr( 'min', current );
		},

		/**
		 * Update max attribute.
		 *
		 * @since 1.5.7
		 *
		 * @param {object} event Input event.
		 */
		fieldNumberSliderUpdateMax: function( event ) {
			var $options = $( event.target ).parents( '.wpforms-field-option-row-min_max' );
			var min = parseFloat( $options.find( '.wpforms-number-slider-min' ).val() );
			var current = parseFloat( event.target.value );

			if ( isNaN( current ) ) {
				return;
			}

			if ( min >= current ) {
				event.preventDefault();
				this.value = min;

				return;
			}

			var fieldId = $options.data( 'field-id' );
			var numberSlider = $builder.find( '#wpforms-field-' + fieldId + ' input[type="range"]' );

			numberSlider.attr( 'max', current );
		},

		/**
		 * Update max attribute for step value.
		 *
		 * @since 1.5.7
		 *
		 * @param {number} fieldID Field ID.
		 * @param {*} newValue Default value attribute.
		 *
		 * @returns {object} App instance.
		 */
		updateNumberSliderStepValueMaxAttr: function( fieldID, newValue ) {

			var input = document.getElementById( 'wpforms-field-option-' + fieldID + '-step' );

			if ( input ) {
				var value = parseFloat( input.value );

				input.setAttribute( 'max', newValue );
				newValue = parseFloat( newValue );

				if ( value > newValue ) {
					input.value = newValue;
					$( input ).trigger( 'input' );
				}
			}

			return this;
		},

		/**
		 * Update upload selector.
		 *
		 * @since 1.5.6
		 *
		 * @param {object} target Changed :input.
		 */
		fieldFileUploadPreviewUpdate: function( target ) {

			var $options = $( target ).parents( '.wpforms-field-option-file-upload' );
			var fieldId = $options.data( 'field-id' );

			var styleOption = $options.find( '#wpforms-field-option-' + fieldId + '-style' ).val();
			var $maxFileNumberRow = $options.find( '#wpforms-field-option-row-' + fieldId + '-max_file_number' );
			var maxFileNumber = parseInt( $maxFileNumberRow.find( 'input' ).val(), 10 );

			var $preview = $( '#wpforms-field-' + fieldId );
			var classicPreview = '.wpforms-file-upload-builder-classic';
			var modernPreview = '.wpforms-file-upload-builder-modern';

			if ( styleOption === 'classic' ) {
				$( classicPreview, $preview ).removeClass( 'wpforms-hide' );
				$( modernPreview, $preview ).addClass( 'wpforms-hide' );
				$maxFileNumberRow.addClass( 'wpforms-row-hide' );
			} else {

				// Change hint and title.
				if ( maxFileNumber > 1 ) {
					$preview
						.find( '.modern-title' )
						.text( wpforms_builder.file_upload.preview_title_plural );
					$preview
						.find( '.modern-hint' )
						.text( wpforms_builder.file_upload.preview_hint.replace( '{maxFileNumber}', maxFileNumber ) )
						.removeClass( 'wpforms-hide' );
				} else {
					$preview
						.find( '.modern-title' )
						.text( wpforms_builder.file_upload.preview_title_single );
					$preview
						.find( '.modern-hint' )
						.text( wpforms_builder.file_upload.preview_hint.replace( '{maxFileNumber}', 1 ) )
						.addClass( 'wpforms-hide' );
				}

				// Display the preview.
				$( classicPreview, $preview ).addClass( 'wpforms-hide' );
				$( modernPreview, $preview ).removeClass( 'wpforms-hide' );
				$maxFileNumberRow.removeClass( 'wpforms-row-hide' );
			}
		},

		/**
		 * Update limit controls by changing checkbox.
		 *
		 * @since 1.5.6
		 *
		 * @param {number} id Field id.
		 * @param {bool} checked Whether an option is checked or not.
		 */
		updateTextFieldsLimitControls: function( id, checked ) {

			if ( ! checked ) {
				$( '#wpforms-field-option-row-' + id + '-limit_controls' ).addClass( 'wpforms-hide' );
			} else {
				$( '#wpforms-field-option-row-' + id + '-limit_controls' ).removeClass( 'wpforms-hide' );
			}
		},

		/**
		 * Update Password Strength controls by changing checkbox.
		 *
		 * @since 1.6.7
		 *
		 * @param {number} id      Field id.
		 * @param {bool}   checked Whether an option is checked or not.
		 */
		updatePasswordStrengthControls: function( id, checked ) {

			var $strengthControls = $( '#wpforms-field-option-row-' + id + '-password-strength-level' );

			if ( checked ) {
				$strengthControls.removeClass( 'wpforms-hidden' );
			} else {
				$strengthControls.addClass( 'wpforms-hidden' );
			}
		},

		/**
		 * Element bindings.
		 *
		 * @since 1.0.0
		 */
		bindUIActions: function() {

			// General Panels.
			app.bindUIActionsPanels();

			// Setup Panel.
			app.bindUIActionsSetup();

			// Fields Panel.
			app.bindUIActionsFields();

			// Settings Panel.
			app.bindUIActionsSettings();

			// Save and Exit.
			app.bindUIActionsSaveExit();

			// General/ global.
			app.bindUIActionsGeneral();
		},

		//--------------------------------------------------------------------//
		// General Panels
		//--------------------------------------------------------------------//

		/**
		 * Element bindings for general panel tasks.
		 *
		 * @since 1.0.0
		 */
		bindUIActionsPanels: function() {

			// Panel switching.
			$builder.on('click', '#wpforms-panels-toggle button, .wpforms-panel-switch', function(e) {
				e.preventDefault();
				app.panelSwitch($(this).data('panel'));
			});

			// Panel sections switching.
			$builder.on('click', '.wpforms-panel .wpforms-panel-sidebar-section', function(e) {
				app.panelSectionSwitch(this, e);
			});
		},

		/**
		 * Switch Panels.
		 *
		 * @since 1.0.0
		 * @since 1.5.9 Added `wpformsPanelSwitched` triger.
		 */
		panelSwitch: function(panel) {

			var $panel    = $('#wpforms-panel-'+panel),
				$panelBtn = $('.wpforms-panel-'+panel+'-button');

			if (!$panel.hasClass('active')) {

				$builder.trigger('wpformsPanelSwitch', panel);

				if (!wpforms_panel_switch) {
					return false;
				}

				$('#wpforms-panels-toggle').find('button').removeClass('active');
				$('.wpforms-panel').removeClass('active');
				$panelBtn.addClass('active');
				$panel.addClass('active');

				history.replaceState({}, null, wpf.updateQueryString('view', panel));

				$builder.trigger( 'wpformsPanelSwitched', panel );
			}
		},

		/**
		 * Switch Panel section.
		 *
		 * @since 1.0.0
		 */
		panelSectionSwitch: function(el, e) {
			if (e) {
				e.preventDefault();
			}

			var $this           = $(el),
				$panel          = $this.parent().parent(),
				section         = $this.data('section'),
				$sectionButtons = $panel.find('.wpforms-panel-sidebar-section'),
				$sectionButton  = $panel.find('.wpforms-panel-sidebar-section-'+section);

			if ( $this.hasClass( 'upgrade-modal' ) || $this.hasClass( 'education-modal' )  ) {
				return;
			}

			if ( ! $sectionButton.hasClass('active') ) {
				$builder.trigger('wpformsPanelSectionSwitch', section);
				$sectionButtons.removeClass('active');
				$sectionButtons.find('.wpforms-toggle-arrow').removeClass('fa-angle-down').addClass('fa-angle-right');
				$sectionButton.addClass('active');
				$sectionButton.find('.wpforms-toggle-arrow').toggleClass('fa-angle-right fa-angle-down');
				$panel.find('.wpforms-panel-content-section').hide();
				$panel.find('.wpforms-panel-content-section-'+section).show();
			}
		},

		//--------------------------------------------------------------------//
		// Setup Panel
		//--------------------------------------------------------------------//

		/**
		 * Element bindings for Setup panel.
		 *
		 * @since 1.0.0
		 */
		bindUIActionsSetup: function() {

			// Focus on the form title field when displaying setup panel.
			$( window ).on( 'load', function( e ) {
				app.setupTitleFocus( e, wpf.getQueryString( 'view' ) );
			} );
			$builder.on( 'wpformsPanelSwitch', app.setupTitleFocus );

			// Select and apply a template
			$builder.on('click', '.wpforms-template-select', function(e) {
				app.templateSelect(this, e);
			});

			// "Blank form" text should trigger template selection
			$builder.on('click', '.wpforms-trigger-blank', function(e) {
				e.preventDefault();
				$('#wpforms-template-blank .wpforms-template-select').trigger('click');
			});

			// Keep Setup title and settings title instances the same
			$builder.on('input ', '#wpforms-panel-field-settings-form_title', function() {
				$('#wpforms-setup-name').val($('#wpforms-panel-field-settings-form_title').val());
			});
			$builder.on('input', '#wpforms-setup-name', function() {
				$('#wpforms-panel-field-settings-form_title').val($('#wpforms-setup-name').val());
			});

			// Additional template searching
			$builder.on('keyup', '#wpforms-setup-template-search' , function() {
				s.templateList.search( $(this).val() );
			});
		},

		/**
		 * Force focus on the form title field when the Setup panel is displaying.
		 *
		 * @since 1.0.0
		 */
		setupTitleFocus: function(e, view) {

			if (typeof view !== 'undefined' && view == 'setup') {
				setTimeout(function (){
					$('#wpforms-setup-name').focus();
				}, 100);
			}
		},

		/**
		 * Select template.
		 *
		 * @since 1.0.0
		 */
		templateSelect: function(el, e) {
			e.preventDefault();

			var $this         = $(el),
				$parent       = $this.parent().parent(),
				$formName     = $('#wpforms-setup-name'),
				$templateBtns = $('.wpforms-template-select'),
				formName      = '',
				labelOriginal = $this.html(),
				template      = $this.data('template'),
				templateName  = $this.data('template-name-raw'),
				title         = '',
				action        = '';

			// Don't do anything for selects that trigger modal
			if ($parent.hasClass('pro-modal')){
				return;
			}

			// Disable all template buttons
			$templateBtns.prop('disabled', true);

			// Display loading indicator
			$this.html(s.spinner+' '+ wpforms_builder.loading);

			$builder.trigger('wpformsTemplateSelect', template);

			// This is an existing form
			if (s.formID) {

				$.confirm({
					title: wpforms_builder.heads_up,
					content: wpforms_builder.template_confirm,
					backgroundDismiss: false,
					closeIcon: false,
					icon: 'fa fa-exclamation-circle',
					type: 'orange',
					buttons: {
						confirm: {
							text: wpforms_builder.ok,
							btnClass: 'btn-confirm',
							keys: [ 'enter' ],
							action: function(){
								// Ajax update form
								var data = {
									title   : $formName.val(),
									action  : 'wpforms_update_form_template',
									template: template,
									form_id : s.formID,
									nonce   : wpforms_builder.nonce
								}
								$.post(wpforms_builder.ajax_url, data, function(res) {
									if (res.success){
										window.location.href = res.data.redirect;
									} else {
										console.log(res);
									}
								}).fail(function(xhr, textStatus, e) {
									console.log(xhr.responseText);
								});
							}
						},
						cancel: {
							text: wpforms_builder.cancel,
							action: function(){
								$templateBtns.prop('disabled', false);
								$this.html(labelOriginal);
							}
						}
					}
				});

			// This is a new form
			} else {

				// Check that form title is provided
				if (!$formName.val()) {
					formName = templateName;
				} else {
					formName = $formName.val();
				}

				// Ajax create new form
				var data = {
					title   : formName,
					action  : 'wpforms_new_form',
					template: template,
					form_id : s.formID,
					nonce   : wpforms_builder.nonce
				}
				$.post(wpforms_builder.ajax_url, data, function(res) {
					if (res.success){
						window.location.href = res.data.redirect;
					} else {
						console.log(res);
					}
				}).fail(function(xhr, textStatus, e) {
					console.log(xhr.responseText);
				});
			}
		},


		//--------------------------------------------------------------------//
		// Fields Panel
		//--------------------------------------------------------------------//

		/**
		 * Element bindings for Fields panel.
		 *
		 * @since 1.0.0
		 */
		bindUIActionsFields: function() {

			// Field sidebar tab toggle
			$builder.on('click', '.wpforms-tab a', function(e) {
				e.preventDefault();
				app.fieldTabToggle($(this).parent().attr('id'));
			});

			// Field sidebar group toggle
			$builder.on('click', '.wpforms-add-fields-heading', function(e) {
				e.preventDefault();
				app.fieldGroupToggle($(this), 'click');
			});

			// Form field preview clicking
			$builder.on('click', '.wpforms-field', function(e) {
				app.fieldTabToggle($(this).data('field-id'));
			});

			// Field delete
			$builder.on('click', '.wpforms-field-delete', function(e) {
				e.preventDefault();
				e.stopPropagation();
				app.fieldDelete($(this).parent().data('field-id'));
			});

			// Field duplicate
			$builder.on('click', '.wpforms-field-duplicate', function(e) {
				e.preventDefault();
				app.fieldDuplicate($(this).parent().data('field-id'));
			});

			// Field add
			$builder.on('click', '.wpforms-add-fields-button', function(e) {
				e.preventDefault();
				app.fieldAdd($(this).data('field-type'));
			});

			// New field choices should be sortable
			$builder.on('wpformsFieldAdd', function(event, id, type) {
				if (type === 'select' || type === 'radio'  || type === 'checkbox' || type === 'payment-multiple' || type === 'payment-checkbox' || type === 'payment-select' ) {
					app.fieldChoiceSortable(type,'#wpforms-field-option-row-' + id + '-choices ul');
				}
			 });

			// Field choice add new
			$builder.on('click', '.wpforms-field-option-row-choices .add', function(e) {
				app.fieldChoiceAdd(e, $(this));
			});

			// Field choice delete
			$builder.on('click', '.wpforms-field-option-row-choices .remove', function(e) {
				app.fieldChoiceDelete(e, $(this));
			});

			// Field choices defaults - before change
			$builder.on('mousedown', '.wpforms-field-option-row-choices input[type=radio]', function(e) {
				var $this = $(this);
				if ( $this.is(':checked') ) {
					$this.attr('data-checked', '1');
				} else {
					$this.attr('data-checked', '0');
				}
			});

			// Field choices defaults
			$builder.on('click', '.wpforms-field-option-row-choices input[type=radio]', function(e) {
				var $this = $(this),
					list  = $this.parent().parent();
				$this.parent().parent().find('input[type=radio]').not(this).prop('checked',false);
				if ( $this.attr('data-checked') === '1' ) {
					$this.prop( 'checked', false );
					$this.attr('data-checked', '0');
				}
				app.fieldChoiceUpdate(list.data('field-type'),list.data('field-id') );
			});

			// Field choices update preview area
			$builder.on('change', '.wpforms-field-option-row-choices input[type=checkbox]', function(e) {
				var list = $(this).parent().parent();
				app.fieldChoiceUpdate(list.data('field-type'),list.data('field-id') );
			});

			// Field choices display value toggle
			$builder.on('change', '.wpforms-field-option-row-show_values input', function(e) {
				$(this).closest('.wpforms-field-option').find('.wpforms-field-option-row-choices ul').toggleClass('show-values');
			});

			// Field choices image toggle.
			$builder.on('change', '.wpforms-field-option-row-choices_images input', function() {

				var $this         = $( this ),
					fieldID       = $this.parent().data( 'field-id' ),
					$fieldOptions = $( '#wpforms-field-option-'+fieldID ),
					checked       = $this.is( ':checked' ),
					type          = $( '#wpforms-field-option-'+fieldID ).find( '.wpforms-field-option-hidden-type' ).val();

				$this.parent().find( '.wpforms-alert' ).toggleClass( 'wpforms-hidden' );
				$fieldOptions.find( '.wpforms-field-option-row-choices ul' ).toggleClass( 'show-images' );
				$fieldOptions.find( '.wpforms-field-option-row-choices_images_style' ).toggleClass( 'wpforms-hidden' );

				if ( checked ) {
					$( '#wpforms-field-option-'+fieldID+'-input_columns' ).val( 'inline' ).trigger( 'change' );
				} else {
					$( '#wpforms-field-option-'+fieldID+'-input_columns' ).val( '' ).trigger( 'change' );
				}

				app.fieldChoiceUpdate( type, fieldID );
			});

			// Field choices image upload add/remove image.
			$builder.on( 'wpformsImageUploadAdd wpformsImageUploadRemove', function( event, $this, $container ) {

				var $list   = $container.closest( '.choices-list' ),
					fieldID = $list.data( 'field-id' ),
					type    = $list.data( 'field-type' );

				app.fieldChoiceUpdate( type, fieldID );
			});

			// Field choices image style toggle.
			$builder.on( 'change', '.wpforms-field-option-row-choices_images_style select', function() {

				var fieldID = $( this ).parent().data( 'field-id' ),
					type    = $( '#wpforms-field-option-'+fieldID ).find( '.wpforms-field-option-hidden-type' ).val();

				app.fieldChoiceUpdate( type, fieldID );
			});

			// Updates field choices text in almost real time.
			$builder.on( 'focusout', '.wpforms-field-option-row-choices input.label, .wpforms-field-option-row-choices input.value', function( e ) {
				var $list = $( this ).parent().parent();
				app.fieldChoiceUpdate( $list.data( 'field-type' ), $list.data( 'field-id' ) );
			} );

			// Field Choices Bulk Add
			$builder.on('click', '.toggle-bulk-add-display', function(e) {
				e.preventDefault();
				app.fieldChoiceBulkAddToggle(this);
			});
			$builder.on('click', '.toggle-bulk-add-presets', function(e) {
				e.preventDefault();
				var $presetList = $(this).closest('.bulk-add-display').find('ul');
				if ( $presetList.css('display') === 'block' ) {
					$(this).text(wpforms_builder.bulk_add_presets_show);
				} else {
					$(this).text(wpforms_builder.bulk_add_presets_hide);
				}
				$presetList.stop().slideToggle();
			});
			$builder.on('click', '.bulk-add-preset-insert', function(e) {
				e.preventDefault();
				var $this         = $(this),
					preset        = $this.data('preset'),
					$container    = $this.closest('.bulk-add-display'),
					$presetList   = $container.find('ul'),
					$presetToggle = $container.find('.toggle-bulk-add-presets'),
					$textarea     = $container.find('textarea');
				$textarea.val('');
				$textarea.insertAtCaret(wpforms_preset_choices[preset].choices.join("\n"));
				$presetToggle.text(wpforms_builder.bulk_add_presets_show);
				$presetList.slideUp();
			});
			$builder.on('click', '.bulk-add-insert', function(e) {
				e.preventDefault();
				app.fieldChoiceBulkAddInsert(this);
			});

			// Field Options group toggle
			$builder.on( 'click', '.wpforms-field-option-group-toggle:not(.education-modal)', function( e ) {
				e.preventDefault();
				var $this = $( this );

				$this
					.parent()
					.toggleClass( 'wpforms-hide' )
					.find( '.wpforms-field-option-group-inner' )
					.stop()
					.slideToggle( '', function() {
						$this.find( 'i' ).toggleClass( 'fa-angle-down fa-angle-right' );
					} );
			} );

			// Display toggle for Address field hide address line 2 option
			$builder.on( 'change', '.wpforms-field-option-address input.wpforms-subfield-hide', function( e ) {
				var $this    = $(this),
					id       = $this.parent().parent().data('field-id'),
					subfield = $this.parent().parent().data('subfield');
				$('#wpforms-field-'+id).find('.wpforms-'+subfield).toggleClass('wpforms-hide');
			});

			// Real-time updates for "Show Label" field option
			$builder.on( 'input', '.wpforms-field-option-row-label input, .wpforms-field-option-row-name input', function( e ) {
				var $this = $( this ),
					value = $this.val(),
					id    = $this.parent().data( 'field-id' );
				$( '#wpforms-field-' + id ).find( '.label-title .text' ).text( value );
			} );

			// Real-time updates for "Description" field option
			$builder.on( 'input', '.wpforms-field-option-row-description textarea', function() {
				var $this = $( this ),
					value = wpf.sanitizeHTML( $this.val() ),
					id    = $this.parent().data( 'field-id' ),
					$desc = $( '#wpforms-field-'+id ).find( '.description' );

				if ( $desc.hasClass( 'nl2br' ) ) {
					$desc.html( value.replace( /\n/g, '<br>' ) );
				} else {
					$desc.html( value );
				}
			});

			// Real-time updates for "Required" field option
			$builder.on('change', '.wpforms-field-option-row-required input', function(e) {
				var id = $(this).parent().data('field-id');
				$('#wpforms-field-'+id).toggleClass('required');
			});

			// Real-time updates for "Confirmation" field option
			$builder.on('change', '.wpforms-field-option-row-confirmation input', function(e) {
				var id = $(this).parent().data('field-id');
				$('#wpforms-field-'+id).find('.wpforms-confirm').toggleClass('wpforms-confirm-enabled wpforms-confirm-disabled');
				$('#wpforms-field-option-'+id).toggleClass('wpforms-confirm-enabled wpforms-confirm-disabled');
			});

			// Real-time updates for "Filter" field option
			$builder.on( 'change', '.wpforms-field-option-row-filter_type select', function() {

				var id = $( this ).parent().data( 'field-id' ),
					$toggledField = $( '#wpforms-field-option-' + id );
				if ( $( this ).val() ) {
					$toggledField.removeClass( 'wpforms-filter-allowlist' );
					$toggledField.removeClass( 'wpforms-filter-denylist' );
					$toggledField.addClass( 'wpforms-filter-' + $( this ).val() );
				} else {
					$toggledField.removeClass( 'wpforms-filter-allowlist' );
					$toggledField.removeClass( 'wpforms-filter-denylist' );
				}
			} );

			$builder.on( 'focusout', '.wpforms-field-option-row-allowlist textarea,.wpforms-field-option-row-denylist textarea', function() {

				var $field = $( this );
				$.get(
					wpforms_builder.ajax_url,
					{
						nonce: wpforms_builder.nonce,
						content: $field.val(),
						action: 'wpforms_sanitize_restricted_rules',
					},
					function( res ) {
						if ( res.success ) {
							$field.val( res.data );
						}
					}
				);
			} );

			// Real-time updates for "Size" field option
			$builder.on('change', '.wpforms-field-option-row-size select', function(e) {
				var $this = $(this),
					value = $this.val(),
					id    = $this.parent().data('field-id');
				$('#wpforms-field-'+id).removeClass('size-small size-medium size-large').addClass('size-'+value);
			});

			// Real-time updates for "Placeholder" field option.
			$builder.on( 'input', '.wpforms-field-option-row-placeholder input', function() {
				var $this    = $( this ),
					value    = wpf.sanitizeHTML( $this.val() ),
					id       = $this.parent().data( 'field-id' ),
					$primary = $( '#wpforms-field-' + id + ' .primary-input' );

				// Set the placeholder value for `input` fields.
				if ( ! $primary.is( 'select' ) ) {
					$primary.prop( 'placeholder', value );
					return;
				}

				// Modern select style.
				if ( app.dropdownField.helpers.isModernSelect( $primary ) ) {
					var choicejsInstance = app.dropdownField.helpers.getInstance( $primary );

					// Additional case for multiple select.
					if ( $primary.prop( 'multiple' ) ) {
						$( choicejsInstance.input.element ).prop( 'placeholder', value );
					} else {

						choicejsInstance.setChoiceByValue( '' );
						$primary.closest( '.choices' ).find( '.choices__inner .choices__placeholder' ).text( value );
					}

					return;
				}

				var $placeholder = $primary.find( '.placeholder' );

				// Classic select style.
				if ( ! value.length && $placeholder.length ) {
					$placeholder.remove();
				} else {

					if ( $placeholder.length ) {
						$placeholder.text( value );
					} else {
						$primary.prepend( '<option value="" class="placeholder">' + value + '</option>' );
					}

					$primary.find( '.placeholder' ).prop( 'selected', ! $primary.prop( 'multiple' ) );
				}
			} );

			// Real-time updates for "Confirmation Placeholder" field option
			$builder.on('input', '.wpforms-field-option-row-confirmation_placeholder input', function(e) {
				var $this   = $(this),
					value   = $this.val(),
					id      = $this.parent().data('field-id');
				$('#wpforms-field-'+id).find('.secondary-input').attr('placeholder', value);
			});

			// Real-time updates for "Hide Label" field option
			$builder.on('change', '.wpforms-field-option-row-label_hide input', function(e) {
				var id = $(this).parent().data('field-id');
				$('#wpforms-field-'+id).toggleClass('label_hide');
			});

			// Real-time updates for Sub Label visbility field option
			$builder.on('change', '.wpforms-field-option-row-sublabel_hide input', function(e) {
				var id = $(this).parent().data('field-id');
				$('#wpforms-field-'+id).toggleClass('sublabel_hide');
			});

			// Real-time updates for Date/Time and Name "Format" option
			$builder.on('change', '.wpforms-field-option-row-format select', function(e) {
				var $this = $(this),
					value = $this.val(),
					id    = $this.parent().data('field-id');
				$('#wpforms-field-'+id).find('.format-selected').removeClass().addClass('format-selected format-selected-'+value);
				$('#wpforms-field-option-'+id).find('.format-selected').removeClass().addClass('format-selected format-selected-'+value);
			});

			// Real-time updates specific for Address "Scheme" option
			$builder.on('change', '.wpforms-field-option-row-scheme select', function(e) {
				var $this = $(this),
					value = $this.val(),
					id    = $this.parent().data('field-id'),
					$field = $('#wpforms-field-'+id);

				$field.find('.wpforms-address-scheme').addClass('wpforms-hide');
				$field.find('.wpforms-address-scheme-'+value).removeClass('wpforms-hide');

				if ( $field.find('.wpforms-address-scheme-'+value+' .wpforms-country' ).children().length == 0 ) {
					$('#wpforms-field-option-'+id).find('.wpforms-field-option-row-country').addClass('wpforms-hidden');
				} else {
					$('#wpforms-field-option-'+id).find('.wpforms-field-option-row-country').removeClass('wpforms-hidden');
				}
			});

			// Real-time updates for Address, Date/Time, and Name "Placeholder" field options
			$builder.on('input', '.wpforms-field-option .format-selected input.placeholder, .wpforms-field-option-address input.placeholder', function(e) {
				var $this    = $(this),
					value    = $this.val(),
					id       = $this.parent().parent().data('field-id'),
					subfield = $this.parent().parent().data('subfield');
				$('#wpforms-field-'+id).find('.wpforms-'+ subfield+' input' ).attr('placeholder', value);
			});

			// Real-time updates for Date/Time date type
			$builder.on( 'change', '.wpforms-field-option-row-date .type select', function( e ) {

				var $this = $( this ),
					value = $this.val(),
					id = $( this ).parent().parent().data( 'field-id' ),
					addClass = value === 'datepicker' ? 'wpforms-date-type-datepicker' : 'wpforms-date-type-dropdown',
					removeClass = value === 'datepicker' ? 'wpforms-date-type-dropdown' : 'wpforms-date-type-datepicker';

				$( '#wpforms-field-' + id ).find( '.wpforms-date' ).addClass( addClass ).removeClass( removeClass );
				$( '#wpforms-field-option-' + id ).addClass( addClass ).removeClass( removeClass );

				var $limitDays = $this.closest( '.wpforms-field-option-group-advanced' )
						.find( '.wpforms-field-option-row-date_limit_days, .wpforms-field-option-row-date_limit_days_options, .wpforms-field-option-row-date_disable_past_dates' ),
					$limitDaysOptions = $( '#wpforms-field-option-row-' + id + '-date_limit_days_options' );

				if ( value === 'dropdown' ) {
					var $dateSelect = $( '#wpforms-field-option-' + id + '-date_format' );

					if ( $dateSelect.find( 'option:selected' ).hasClass( 'datepicker-only' ) ) {
						$dateSelect.prop( 'selectedIndex', 0 ).trigger( 'change' );
					}

					$limitDays.hide();
				} else {
					$limitDays.show();
					$( '#wpforms-field-option-' + id + '-date_limit_days' ).is( ':checked' ) ?
						$limitDaysOptions.show() : $limitDaysOptions.hide();
				}
			} );

			// Real-time updates for Date/Time date select format
			$builder.on('change', '.wpforms-field-option-row-date .format select', function(e) {
				var $this = $(this),
					value = $this.val(),
					id    = $(this).parent().parent().data('field-id');
				if ( value === 'm/d/Y' ) {
					$('#wpforms-field-'+id).find('.wpforms-date-dropdown .first option').text(wpforms_builder.date_select_month);
					$('#wpforms-field-'+id).find('.wpforms-date-dropdown .second option').text(wpforms_builder.date_select_day);
				} else if ( value == 'd/m/Y' ) {
					$('#wpforms-field-'+id).find('.wpforms-date-dropdown .first option').text(wpforms_builder.date_select_day);
					$('#wpforms-field-'+id).find('.wpforms-date-dropdown .second option').text(wpforms_builder.date_select_month);
				}
			});

			// Real-time updates for Date/Time time select format
			$builder.on( 'change', '.wpforms-field-option-row-time .format select', function( e ) {

				var $this = $( this ),
					format = $this.val() === 'g:i A' ? 12 : 24,
					id = $( this ).closest( '.wpforms-field-option-row' ).data( 'field-id' ),
					options = '',
					hh;

				// Generate new set of hours options.
				for ( var i = 0; i < format; i++ ) {
					hh = i < 10 ? '0' + i : i;
					options += '<option value="{hh}">{hh}</option>'.replace( /{hh}/g, hh );
				}

				_.forEach( [ 'start', 'end' ], function( field ) {

					var $hour = $builder.find( '#wpforms-field-option-' + id + '-time_limit_hours_' + field + '_hour' ),
						$ampm = $builder.find( '#wpforms-field-option-' + id + '-time_limit_hours_' + field + '_ampm' ),
						hourValue = parseInt( $hour.val(), 10 ),
						ampmValue = $ampm.val();

					if ( format === 24 ) {
						hourValue = ampmValue === 'pm' ? hourValue + 12 : hourValue;
					} else {
						ampmValue = hourValue > 12 ? 'pm' : 'am';
						hourValue = hourValue > 12 ? hourValue - 12 : hourValue;
					}

					hourValue = hourValue < 10 ? '0' + hourValue : hourValue;
					$hour.html( options ).val( hourValue );
					$ampm.toggleClass( 'wpforms-hidden', format === 24 ).val( ampmValue );
					$ampm.nextAll( 'div' ).toggleClass( 'wpforms-hidden', format === 12 );
				} );

			} );

			// Consider the field active when a disabled nav button is clicked
			$builder.on('click', '.wpforms-pagebreak-button', function(e) {
				e.preventDefault();
				$(this).closest('.wpforms-field').trigger('click');
			});

			/*
			 * Pagebreak field.
			 */
			app.fieldPageBreakInitDisplayPrevious( $builder.find( '.wpforms-field-pagebreak.wpforms-pagebreak-normal:first' ) );

			$builder
				.on( 'input', '.wpforms-field-option-row-next input', function( e ) {

					// Real-time updates for "Next" pagebreak field option.
					var $this = $( this ),
						value = $this.val(),
						$next = $( '#wpforms-field-' + $this.parent().data( 'field-id' ) ).find( '.wpforms-pagebreak-next' );

					if ( value ) {
						$next.css( 'display', 'inline-block' ).text( value );
					} else {
						$next.css( 'display', 'none' ).empty();
					}
				} )
				.on( 'input', '.wpforms-field-option-row-prev input', function( e ) {

					// Real-time updates for "Prev" pagebreak field option.
					var $this = $( this ),
						value = $this.val(),
						$field = $( '#wpforms-field-' + $this.parent().data( 'field-id' ) ),
						$prevBtn = $field.find( '.wpforms-pagebreak-prev' );

					if ( value && $field.prevAll( '.wpforms-field-pagebreak.wpforms-pagebreak-normal' ).length > 0 ) {
						$prevBtn.removeClass( 'wpforms-hidden' ).text( value );
					} else {
						$prevBtn.addClass( 'wpforms-hidden' ).empty();
					}
				} )
				.on( 'change', '.wpforms-field-option-row-prev_toggle input', function( e ) {

					// Real-time updates for "Display Previous" pagebreak field option.
					var $prev      = $( this ).closest( '.wpforms-field-option-group-inner' ).find( '.wpforms-field-option-row-prev' ),
						$prevLabel = $prev.find( 'input' );

					$prev.toggleClass( 'wpforms-hidden' );

					if ( $( this ).prop( 'checked' ) && ! $prevLabel.val() ) {
						$prevLabel.val( wpforms_builder.previous );
					} else {
						$prevLabel.val( '' );
					}
					$prevLabel.trigger( 'input' );
				} )
				.on( 'wpformsFieldAdd', app.fieldPagebreakAdd )
				.on( 'wpformsFieldDelete', app.fieldPagebreakDelete );

			// Update Display Previous option visibility for all Pagebreak fields.
			$builder.on( 'wpformsFieldMove wpformsFieldAdd wpformsFieldDelete', function( e ) {
				$builder.find( '.wpforms-field-pagebreak.wpforms-pagebreak-normal' ).each( function( i ) {
					app.fieldPageBreakInitDisplayPrevious( $( this ) );
				} );
			} );

			// Real-time updates for "Page Title" pagebreak field option
			$builder.on( 'input', '.wpforms-field-option-row-title input', function( e ) {
				var $this = $( this ),
					value = $this.val(),
					id = $this.parent().data( 'field-id' );
				if ( value ) {
					$( '#wpforms-field-' + id ).find( '.wpforms-pagebreak-title' ).text( '(' + value + ')' );
				} else {
					$( '#wpforms-field-' + id ).find( '.wpforms-pagebreak-title' ).empty();
				}
			} );

			// Real-time updates for "Page Navigation Alignment" pagebreak field option
			$builder.on( 'change', '.wpforms-field-option-row-nav_align select', function( e ) {
				var $this = $( this ),
					value = $this.val();
				if ( ! value ) {
					value = 'center';
				}
				$( '.wpforms-pagebreak-buttons' )
					.removeClass( 'wpforms-pagebreak-buttons-center wpforms-pagebreak-buttons-left wpforms-pagebreak-buttons-right wpforms-pagebreak-buttons-split' )
					.addClass( 'wpforms-pagebreak-buttons-' + value );
			} );

			// Real-time updates for Single Item field "Item Price" option.
			$builder.on( 'input', '.wpforms-field-option-row-price input', function( e ) {

				var $this = $( this ),
					value = $this.val(),
					id = $this.parent().data( 'field-id' ),
					sanitized = wpf.amountSanitize( value ),
					formatted = wpf.amountFormat( sanitized ),
					singleItem;

				if ( wpforms_builder.currency_symbol_pos === 'right' ) {
					singleItem = formatted + ' ' + wpforms_builder.currency_symbol;
				} else {
					singleItem = wpforms_builder.currency_symbol + ' ' + formatted;
				}

				$( '#wpforms-field-' + id ).find( '.primary-input' ).val( formatted );
				$( '#wpforms-field-' + id ).find( '.price' ).text( singleItem );
			} );

			// Real-time updates for payment CC icons
			$builder.on( 'change', '.wpforms-field-option-credit-card .payment-icons input', function( e ) {

				var $this = $( this ),
					card = $this.data( 'card' ),
					id = $this.parent().data( 'field-id' );

				$( '#wpforms-field-' + id ).find( 'img.icon-' + card ).toggleClass( 'card_hide' );
			} );

			// Generic updates for various additional placeholder fields
			$builder.on('input', '.wpforms-field-option input.placeholder-update', function(e) {
				var $this    = $(this),
					value    = $this.val(),
					id       = $this.data('field-id'),
					subfield = $this.data('subfield');
				$('#wpforms-field-'+id).find('.wpforms-'+ subfield+' input' ).attr('placeholder', value);
			});

			// Toggle Choice Layout advanced field option.
			$builder.on( 'change', '.wpforms-field-option-row-input_columns select', function() {
				var $this    = $( this ),
					value    = $this.val(),
					cls      = '',
					id       = $this.parent().data( 'field-id' );
				if ( value === '2' ) {
					cls = 'wpforms-list-2-columns';
				} else if ( value === '3' ) {
					cls = 'wpforms-list-3-columns';
				} else if ( value === 'inline' ) {
					cls = 'wpforms-list-inline';
				}
				$( '#wpforms-field-' + id ).removeClass( 'wpforms-list-2-columns wpforms-list-3-columns wpforms-list-inline' ).addClass( cls );
			});

			// Toggle the toggle field.
			$builder.on('click', '.wpforms-field-option-row .wpforms-toggle-icon', function(e) {
				var $this  = $(this),
					$check = $this.find('input[type=checkbox]'),
					$label = $this.find('.wpforms-toggle-icon-label');

				$this.toggleClass('wpforms-off wpforms-on');
				$this.find('i').toggleClass('fa-toggle-off fa-toggle-on');

				if ($this.hasClass('wpforms-on')) {
					$label.text(wpforms_builder.on);
					$check.prop('checked', true);
				} else {
					$label.text(wpforms_builder.off);
					$check.prop('checked', false);
				}
				$check.trigger('change');
			});

			// Real-time updates for "Dynamic Choices" field option, for Dropdown,
			// Checkboxes, and Multiple choice fields
			$builder.on('change', '.wpforms-field-option-row-dynamic_choices select', function(e) {
				app.fieldDynamicChoiceToggle($(this));
			});

			// Real-time updates for "Dynamic [type] Source" field option, for Dropdown,
			// Checkboxes, and Multiple choice fields
			$builder.on('change', '.wpforms-field-option-row-dynamic_taxonomy select, .wpforms-field-option-row-dynamic_post_type select', function(e) {
				app.fieldDynamicChoiceSource($(this));
			});

			// Toggle Layout selector
			$builder.on('click', '.toggle-layout-selector-display', function(e) {
				e.preventDefault();
				app.fieldLayoutSelectorToggle(this);
			});
			$builder.on('click', '.layout-selector-display-layout', function(e) {
				e.preventDefault();
				app.fieldLayoutSelectorLayout(this);
			});
			$builder.on('click', '.layout-selector-display-columns span', function(e) {
				e.preventDefault();
				app.fieldLayoutSelectorInsert(this);
			});

			// Real-time updates for Rating field scale option.
			$( document ).on( 'change', '.wpforms-field-option-row-scale select', function() {

				var $this  = $( this ),
					value  = $this.val(),
					id     = $this.parent().data( 'field-id' ),
					$icons = $( '#wpforms-field-'+id +' .rating-icon' ),
					x      = 1;

				$icons.each( function( index ) {

					if ( x <= value ) {
						$( this ).show();
					} else {
						$( this ).hide();
					}
					x++;
				});
			});

			// Real-time updates for Rating field icon option.
			$( document ).on( 'change', '.wpforms-field-option-row-icon select', function() {

				var $this     = $( this ),
					value     = $this.val(),
					id        = $this.parent().data( 'field-id' ),
					$icons    = $( '#wpforms-field-'+id +' .rating-icon' ),
					iconClass = 'fa-star';

				if ( 'heart' === value ) {
					iconClass = 'fa-heart';
				} else if ( 'thumb' === value ) {
					iconClass = 'fa-thumbs-up';
				} else if ( 'smiley' === value ) {
					iconClass = 'fa-smile-o';
				}

				$icons.removeClass( 'fa-star fa-heart fa-thumbs-up fa-smile-o' ).addClass( iconClass );
			});

			// Real-time updates for Rating field icon size option.
			$( document ).on( 'change', '.wpforms-field-option-row-icon_size select', function() {

				var $this     = $( this ),
					value     = $this.val(),
					id        = $this.parent().data( 'field-id' ),
					$icons    = $( '#wpforms-field-'+id +' .rating-icon' );
					fontSize  = '28';

				if ( 'small' === value ) {
					fontSize = '18';
				} else if ( 'large' === value ) {
					fontSize = '38';
				}

				$icons.css( 'font-size', fontSize + 'px' );
			});

			// Real-time updates for Rating field icon color option.
			$( document ).on( 'input', '.wpforms-field-option-row-icon_color input.wpforms-color-picker', function() {

				var $this     = $( this ),
					value     = $this.val(),
					id        = $this.closest( '.wpforms-field-option-row' ).data( 'field-id' ),
					$icons    = $( '#wpforms-field-'+id +' i.fa' );

				$icons.css( 'color', value );
			});

			// Real-time updates for Checkbox field Disclaimer option.
			$( document ).on( 'change', '.wpforms-field-option-row-disclaimer_format input', function() {

				var $this     = $( this ),
					id        = $this.parent().data( 'field-id' ),
					$desc    = $( '#wpforms-field-'+id +' .description' );

				$desc.toggleClass( 'disclaimer' );
			});

			$builder.on(
				'change',
				'.wpforms-field-option-row-limit_enabled input',
				function( event ) {
					app.updateTextFieldsLimitControls( $( event.target ).parents( '.wpforms-field-option-row-limit_enabled' ).data().fieldId, event.target.checked );
				}
			);

			$builder.on(
				'change',
				'.wpforms-field-option-row-password-strength input',
				function( event ) {
					app.updatePasswordStrengthControls( $( event.target ).parents( '.wpforms-field-option-row-password-strength' ).data().fieldId, event.target.checked );
				}
			);

			// File uploader - change style.
			$builder
				.on(
					'change',
					'.wpforms-field-option-file-upload .wpforms-field-option-row-style select, .wpforms-field-option-file-upload .wpforms-field-option-row-max_file_number input',
					function( event ) {
						app.fieldFileUploadPreviewUpdate( event.target );
					}
				);

			// Real-time updates for Number Slider field.
			app.numberSliderEvents( $builder );

			// Hide image choices if dynamic choices is not off.
			app.fieldDynamicChoiceToggleImageChoices();

			// Real-time updates for Payment field's 'Show price after item label' option.
			$builder.on( 'change', '.wpforms-field-option-row-show_price_after_labels input', function( e ) {

				var $input = $( this ),
					$list  = $input.closest( '.wpforms-field-option-group-basic' ).find( '.wpforms-field-option-row-choices .choices-list' );

				app.fieldChoiceUpdate( $list.data( 'field-type' ), $list.data( 'field-id' ) );
			} );
		},

		/**
		 * Toggle field group visibility in the field sidebar.
		 *
		 * @since 1.0.0
		 */
		fieldGroupToggle: function(el, action) {

			if ( 'click' === action ) {

				var $this      = $(el),
					$buttons   = $this.next('.wpforms-add-fields-buttons'),
					$group     = $buttons.parent(),
					$icon      = $this.find('i'),
					groupName  = $this.data('group'),
					cookieName = 'wpforms_field_group_'+groupName;

				if ($group.hasClass('wpforms-hide')) {
					wpCookies.remove(cookieName);
				} else {
					wpCookies.set(cookieName,'true',2592000); // 1 month
				}
				$icon.toggleClass('fa-angle-down fa-angle-right');
				$buttons.stop().slideToggle( '', function() {
					$group.toggleClass( 'wpforms-hide' );
				} );

			} else if ( 'load' === action ) {

				var $this      = $(el),
					$buttons   = $this.find('.wpforms-add-fields-buttons'),
					$icon      = $this.find('.wpforms-add-fields-heading i'),
					groupName  = $this.find('.wpforms-add-fields-heading').data('group'),
					cookieName = 'wpforms_field_group_'+groupName;

				if (wpCookies.get(cookieName) == 'true') {
					$icon.toggleClass('fa-angle-down fa-angle-right');
					$buttons.hide();
					$this.toggleClass('wpforms-hide');
				}
			}
		},

		/**
		 * Delete field
		 *
		 * @since 1.0.0
		 */
		fieldDelete: function(id) {

			var $field = $('#wpforms-field-'+id),
				type   = $field.data('field-type');

			if ($field.hasClass('no-delete')) {
				$.alert({
					title: wpforms_builder.field_locked,
					content: wpforms_builder.field_locked_msg,
					icon: 'fa fa-info-circle',
					type: 'blue',
					buttons: {
						confirm: {
							text: wpforms_builder.close,
							btnClass: 'btn-confirm',
							keys: [ 'enter' ],
						}
					}
				});
			} else {

				var fieldData = {
					'id'      : id,
					'message' : wpforms_builder.delete_confirm,
				};

				$builder.trigger( 'wpformsBeforeFieldDeleteAlert', [ fieldData ] );

				$.confirm( {
					title             : false,
					content           : fieldData.message,
					backgroundDismiss : false,
					closeIcon         : false,
					icon              : 'fa fa-exclamation-circle',
					type              : 'orange',
					buttons: {
						confirm: {
							text     : wpforms_builder.ok,
							btnClass : 'btn-confirm',
							keys     : [ 'enter' ],
							action: function() {
								$( '#wpforms-field-' + id ).fadeOut( 400, function() {
									$( this ).remove();
									$( '#wpforms-field-option-' + id ).remove();
									$( '.wpforms-field, .wpforms-title-desc' ).removeClass( 'active' );
									app.fieldTabToggle( 'add-fields' );
									if ( $( '.wpforms-field' ).length < 1 ) {
										elements.$fieldOptions.append( elements.$noFieldsOptions.clone() );
										elements.$sortableFieldsWrap.append( elements.$noFieldsPreview.clone() );
										$builder.find( '.wpforms-field-submit' ).hide();
									}
									$builder.trigger( 'wpformsFieldDelete', [ id, type ] );
								} );
							},
						},
						cancel: {
							text: wpforms_builder.cancel,
						},
					},
				} );
			}
		},

		/**
		 * Duplicate field
		 *
		 * @since 1.2.9
		 */
		fieldDuplicate: function(id) {

			var $field = $('#wpforms-field-'+id),
				type   = $field.data('field-type');

			if ($field.hasClass('no-duplicate')) {
				$.alert({
					title: wpforms_builder.field_locked,
					content: wpforms_builder.field_locked_msg,
					icon: 'fa fa-info-circle',
					type: 'blue',
					buttons : {
						confirm : {
							text: wpforms_builder.close,
							btnClass: 'btn-confirm',
							keys: [ 'enter' ],
						}
					}
				});
			} else {
				$.confirm({
					title: false,
					content: wpforms_builder.duplicate_confirm,
					backgroundDismiss: false,
					closeIcon: false,
					icon: 'fa fa-exclamation-circle',
					type: 'orange',
					buttons: {
						confirm: {
							text: wpforms_builder.ok,
							btnClass: 'btn-confirm',
							keys: [ 'enter' ],
							action: function() {
								var $fieldOptions = $( '#wpforms-field-option-' + id );

								// Restore tooltips before cloning.
								wpf.restoreTooltips( $fieldOptions );

								var $newField            = $field.clone(),
									newFieldOptions 	 = $fieldOptions.html(),
									newFieldID           = $('#wpforms-field-id').val(),
									$labelField          = $( '#wpforms-field-option-' + id + '-label' ).length ? $( '#wpforms-field-option-' + id + '-label' ) : $( '#wpforms-field-option-' + id + '-name' ),
									newFieldLabel        = $labelField.length ? $labelField.val() + ' ' + wpforms_builder.duplicate_copy : wpforms_builder.field + ' #' + id + ' ' + wpforms_builder.duplicate_copy,
									nextID               = Number(newFieldID)+1,
									regex_fieldOptionsID = new RegExp( 'ID #'+id, "g"),
									regex_fieldID        = new RegExp( 'fields\\['+id+'\\]', "g"),
									regex_dataFieldID    = new RegExp( 'data-field-id="'+id+'"', "g"),
									regex_referenceID    = new RegExp( 'data-reference="'+id+'"', "g"),
									regex_elementID      = new RegExp( '\\b(id|for)="wpforms-(.*?)'+id+'(.*?)"', "ig");

								// Toggle visibility states
								$field.after($newField);
								$field.removeClass('active');
								$newField.addClass('active').attr({
									'id'           : 'wpforms-field-'+newFieldID,
									'data-field-id': newFieldID
								});

								// Various regex to adjust the field options to work with
								// the new field ID
								function regex_elementID_replace(match, p1, p2, p3, offset, string) {
									return p1+'="wpforms-'+p2+newFieldID+p3+'"';
								}
								newFieldOptions = newFieldOptions.replace(regex_fieldOptionsID, 'ID #'+newFieldID);
								newFieldOptions = newFieldOptions.replace(regex_fieldID, 'fields['+newFieldID+']');
								newFieldOptions = newFieldOptions.replace(regex_dataFieldID, 'data-field-id="'+newFieldID+'"');
								newFieldOptions = newFieldOptions.replace(regex_referenceID, 'data-reference="'+newFieldID+'"');
								newFieldOptions = newFieldOptions.replace(regex_elementID, regex_elementID_replace);

								// Add new field options panel
								$fieldOptions.hide().after( '<div class="' + $fieldOptions.attr( 'class' ) + '" id="wpforms-field-option-' + newFieldID + '" data-field-id="' + newFieldID + '">' + newFieldOptions + '</div>' );
								var $newFieldOptions = $('#wpforms-field-option-'+newFieldID);

								// Copy over values
								$fieldOptions.find(':input').each(function(index, el) {

									var $this = $(this),
										name    = $this.attr('name');

									if ( ! name ) {
										return 'continue';
									}

									var newName = name.replace(regex_fieldID, 'fields['+newFieldID+']'),
										type    = $this.attr('type');

									if ( type === 'checkbox' || type === 'radio' ) {
										if ($this.is(':checked')){
											$newFieldOptions.find('[name="'+newName+'"]').prop('checked', true).attr('checked','checked');
										} else {
											$newFieldOptions.find('[name="'+newName+'"]').prop('checked', false).attr('checked',false);
										}
									} else if ($this.is('select')) {
										if ($this.find('option:selected').length) {
											var optionVal = $this.find('option:selected').val();
											$newFieldOptions.find('[name="'+newName+'"]').find('[value="'+optionVal+'"]').prop('selected',true);
										}
									} else {
										if ($this.val() !== '') {
											$newFieldOptions.find('[name="'+newName+'"]').val( $this.val() );
										} else if ( $this.hasClass( 'wpforms-money-input' ) ) {
											$newFieldOptions.find( '[name="' + newName + '"]' ).val(
												wpf.numberFormat( '0', wpforms_builder.currency_decimals, wpforms_builder.currency_decimal, wpforms_builder.currency_thousands )
											);
										}
									}
								});

								// ID adjustments
								$('#wpforms-field-option-'+newFieldID).find('.wpforms-field-option-hidden-id').val(newFieldID);
								$('#wpforms-field-id').val(nextID);

								// Adjust label to indicate this is a copy
								$('#wpforms-field-option-'+newFieldID+'-label').val(newFieldLabel);
								$newField.find('.label-title .text').text(newFieldLabel);

								// Fire field add custom event
								$builder.trigger('wpformsFieldAdd', [newFieldID, type]);

								// Re-init tooltips for new field options panel.
								wpf.initTooltips();

								// Lastly, update the next ID stored in database
								$.post(wpforms_builder.ajax_url, {form_id : s.formID, nonce : wpforms_builder.nonce, action : 'wpforms_builder_increase_next_field_id'});
							}
						},
						cancel: {
							text: wpforms_builder.cancel
						}
					}
				});
			}
		},

		/**
		 * Add new field.
		 *
		 * @since 1.0.0
		 * @since 1.6.4 Added hCaptcha support.
		 */
		fieldAdd: function(type, options) {

			var $btn = $( '#wpforms-add-fields-' + type );

			if ( $btn.hasClass( 'upgrade-modal' ) || $btn.hasClass( 'education-modal' ) || $btn.hasClass( 'warning-modal' ) ) {
				return;
			}

			if ( -1 !== $.inArray( type, [ 'captcha_hcaptcha', 'captcha_recaptcha', 'captcha_none' ] ) ) {
				app.captchaUpdate();
				return;
			}

			var defaults = {
				position   : 'bottom',
				placeholder: false,
				scroll     : true,
				defaults   : false
			};
			options = $.extend( {}, defaults, options);

			var data = {
				action  : 'wpforms_new_field_'+type,
				id      : s.formID,
				type    : type,
				defaults: options.defaults,
				nonce   : wpforms_builder.nonce
			};

			return $.post(wpforms_builder.ajax_url, data, function(res) {
				if (res.success) {

					var totalFields = $('.wpforms-field').length,
						$preview    = $('#wpforms-panel-fields .wpforms-panel-content-wrap'),
						$lastField  = $('.wpforms-field').last(),
						$newField   = $(res.data.preview),
						$newOptions = $(res.data.options);

					$newField.css('display', 'none');

					if (options.placeholder) {
						options.placeholder.remove();
					}

					// Determine where field gets placed
					if ( 'bottom' === options.position ) {

						if ( $lastField.length && $lastField.hasClass( 'wpforms-field-stick' ) ) {

							// Check to see if the last field we have is configured to
							// be stuck to the bottom, if so add the field above it.
							$( '.wpforms-field-wrap' ).children( ':eq(' + ( totalFields - 1 ) + ')' ).before( $newField );
							$( '.wpforms-field-options' ).children( ':eq(' + ( totalFields - 1 ) + ')' ).before( $newOptions );

						} else {
							// Add field to bottom
							$('.wpforms-field-wrap').append($newField);
							$('.wpforms-field-options').append($newOptions);
						}

						if (options.scroll) {
							$preview.animate({ scrollTop: $preview.prop('scrollHeight') - $preview.height() }, 1000);
						}

					} else if ( 'top' === options.position ) {

						// Add field to top, scroll to
						$('.wpforms-field-wrap').prepend($newField);
						$('.wpforms-field-options').prepend($newOptions);

						if (options.scroll) {
							$preview.animate({ scrollTop: 0 }, 1000);
						}

					} else {

						if ( options.position === totalFields && $lastField.length && $lastField.hasClass( 'wpforms-field-stick' ) ) {

							// Check to see if the user tried to add the field at
							// the end BUT the last field we have is configured to
							// be stuck to the bottom, if so add the field above it.
							$( '.wpforms-field-wrap' ).children( ':eq(' + ( totalFields - 1 ) + ')' ).before( $newField );
							$( '.wpforms-field-options' ).children( ':eq(' + ( totalFields - 1 ) + ')' ).before( $newOptions );

						} else if ( $( '.wpforms-field-wrap' ).children( ':eq(' + options.position + ')' ).length ) {

							// Add field to a specific location
							$( '.wpforms-field-wrap' ).children( ':eq(' + options.position + ')' ).before( $newField );
							$( '.wpforms-field-options' ).children( ':eq(' + options.position + ')' ).before( $newOptions );

						} else {
							// Something's wrong, just add the field. This should never occur.
							$('.wpforms-field-wrap').append($newField);
							$('.wpforms-field-options').append($newOptions);
						}
					}

					$newField.fadeIn();

					$builder.find( '.no-fields, .no-fields-preview' ).remove();
					$builder.find( '.wpforms-field-submit' ).show();

					$('#wpforms-field-id').val(res.data.field.id+1);

					wpf.initTooltips();
					app.loadColorPickers();
					app.toggleAllOptionGroups();

					$builder.trigger('wpformsFieldAdd', [res.data.field.id, type ]);

				} else {
					console.log(res);
				}
			}).fail(function(xhr, textStatus, e) {
				console.log(xhr.responseText);
			});
		},

		/**
		 * Update CAPTCHA form setting.
		 *
		 * @since 1.6.4
		 *
		 * @returns {object} jqXHR
		 */
		captchaUpdate: function() {

			var data = {
				action : 'wpforms_update_field_captcha',
				id     : s.formID,
				nonce  : wpforms_builder.nonce,
			};

			return $.post( wpforms_builder.ajax_url, data, function( res ) {

				if ( res.success ) {
					var args = {
							title: false,
							content: false,
							icon: 'fa fa-exclamation-circle',
							type: 'orange',
							backgroundDismiss: false,
							closeIcon: false,
							boxWidth: '450px',
							buttons: {
								confirm: {
									text: wpforms_builder.ok,
									btnClass: 'btn-confirm',
									keys: [ 'enter' ],
								},
							},
						},
						$enableCheckbox = $( '#wpforms-panel-field-settings-recaptcha' ),
						caseName        = res.data.current;

					$enableCheckbox.data( 'provider', res.data.provider );

					// Possible cases:
					//
					// not_configured - IF CAPTCHA is not configured in the WPForms plugin settings
					// configured_not_enabled - IF CAPTCHA is configured in WPForms plugin settings, but wasn't set in form settings
					// configured_enabled - IF CAPTCHA is configured in WPForms plugin and form settings
					if ( 'configured_not_enabled' === caseName || 'configured_enabled' === caseName ) {

						// Get a correct case name.
						caseName = $enableCheckbox.prop( 'checked' ) ? 'configured_enabled' : 'configured_not_enabled';

						// Check/uncheck a `CAPTCHA` checkbox in form setting.
						args.buttons.confirm.action = function() {
							$enableCheckbox.prop( 'checked', ( 'configured_not_enabled' === caseName ) ).trigger( 'change' );
						};
					}

					args.title = res.data.cases[ caseName ].title;
					args.content = res.data.cases[ caseName ].content;

					// Do you need a Cancel button?
					if ( res.data.cases[ caseName ].cancel ) {
						args.buttons.cancel = {
							text: wpforms_builder.cancel,
							keys: [ 'esc' ],
						};
					}

					// Call a Confirm modal.
					$.confirm( args );
				} else {
					console.log( res );
				}
			} ).fail( function( xhr, textStatus, e ) {
				console.log( xhr.responseText );
			} );
		},

		/**
		 * Sortable fields in the builder form preview area.
		 *
		 * @since 1.0.0
		 */
		fieldSortable: function() {

			var fieldOptions = $('.wpforms-field-options'),
				fieldReceived = false,
				fieldIndex,
				fieldIndexNew,
				field,
				fieldNew;

			$('.wpforms-field-wrap').sortable({
				items  : '> .wpforms-field:not(.wpforms-field-stick):not(.no-fields-preview)',
				axis   : 'y',
				delay  : 100,
				opacity: 0.75,
				start:function(e,ui){
					fieldIndex = ui.item.index();
					field      = fieldOptions[0].children[fieldIndex];
				},
				stop:function(e,ui){
					fieldIndexNew = ui.item.index();
					fieldNew      = fieldOptions[0].children[fieldIndexNew];
					if (fieldIndex < fieldIndexNew){
						$(fieldNew).after(field);
					} else {
						$(fieldNew).before(field);
					}
					$builder.trigger('wpformsFieldMove', ui);
					fieldReceived = false;
				},
				over: function(e, ui){
					var $el = ui.item.first();
					$el.addClass('wpforms-field-dragging');

					if ( $el.hasClass('wpforms-field-drag')){
						var width = $( '.wpforms-field' ).outerWidth() || elements.$sortableFieldsWrap.find( '.no-fields-preview' ).outerWidth();
						$el.addClass('wpforms-field-drag-over').removeClass('wpforms-field-drag-out').css('width', width).css('height', 'auto');
					}
				},
				out: function(e, ui){
					var $el   = ui.item.first();
					$el.removeClass('wpforms-field-dragging');

					if ( !fieldReceived ) {
						var width = $el.attr('data-original-width');
						if ( $el.hasClass('wpforms-field-drag')){
							$el.addClass('wpforms-field-drag-out').removeClass('wpforms-field-drag-over').css('width', width).css('left', '').css('top', '');
						}
					}
					$el.css({
						'top':     '',
						'left':    '',
						'z-index': ''
					});
				},
				receive: function(e, ui) {
					fieldReceived = true;

					var pos  = $(this).data('ui-sortable').currentItem.index(),
						$el  = ui.helper,
						type = $el.attr('data-field-type');

					$el.addClass('wpforms-field-drag-over wpforms-field-drag-pending').removeClass('wpforms-field-drag-out').css('width', '100%');
					$el.append('<i class="fa fa-cog fa-spin"></i>');

					app.fieldAdd(type, {position: pos, placeholder: $el});
				   }
			});

			$( '.wpforms-add-fields-button' )
				.not( '.not-draggable' )
				.not( '.warning-modal' )
				.not( '.education-modal' )
				.draggable( {
					connectToSortable: '.wpforms-field-wrap',
					delay: 200,
					helper: function() {
						var $this = $( this ),
							width = $this.outerWidth(),
							text = $this.html(),
							type = $this.data( 'field-type' ),
							$el = $( '<div class="wpforms-field-drag-out wpforms-field-drag">' );

						return $el.html( text ).css( 'width', width ).attr( 'data-original-width', width ).attr( 'data-field-type', type );
					},
					revert: 'invalid',
					cancel: false,
					scroll: false,
					opacity: 0.75,
					containment: 'document',
				} );
		},

		/**
		 * Add new field choice
		 *
		 * @since 1.0.0
		 */
		fieldChoiceAdd: function( event, el ) {

			event.preventDefault();

			var $this   = $( el ),
				$parent = $this.parent(),
				checked = $parent.find( 'input.default' ).is( ':checked' ),
				fieldID = $this.closest( '.wpforms-field-option-row-choices' ).data( 'field-id' ),
				id      = $parent.parent().attr( 'data-next-id' ),
				type    = $parent.parent().data( 'field-type' ),
				$choice = $parent.clone().insertAfter( $parent );

			$choice.attr( 'data-key', id );
			$choice.find( 'input.label' ).val( '' ).attr( 'name', 'fields['+fieldID+'][choices]['+id+'][label]' );
			$choice.find( 'input.value' ).val( '' ).attr( 'name', 'fields['+fieldID+'][choices]['+id+'][value]' );
			$choice.find( 'input.source' ).val( '' ).attr( 'name', 'fields['+fieldID+'][choices]['+id+'][image]' );
			$choice.find( 'input.default').attr( 'name', 'fields['+fieldID+'][choices]['+id+'][default]' ).prop( 'checked', false );
			$choice.find( '.preview' ).empty();
			$choice.find( '.wpforms-image-upload-add' ).show();
			$choice.find( '.wpforms-money-input' ).trigger( 'focusout' );

			if ( checked === true ) {
				$parent.find( 'input.default' ).prop( 'checked', true );
			}
			id++;
			$parent.parent().attr( 'data-next-id', id );
			$builder.trigger( 'wpformsFieldChoiceAdd' );
			app.fieldChoiceUpdate( type, fieldID );
		},

		/**
		 * Delete field choice
		 *
		 * @since 1.0.0
		 */
		fieldChoiceDelete: function( e, el ) {

			e.preventDefault();

			var $this     = $( el ),
				$list     = $this.parent().parent(),
				total     = $list.find( 'li' ).length,
				fieldData = {
					'id'       : $list.data( 'field-id' ),
					'choiceId' : $this.closest( 'li' ).data( 'key' ),
					'message'  : '<strong>' + wpforms_builder.delete_choice_confirm + '</strong>',
					'trigger'  : false,
				};

			$builder.trigger( 'wpformsBeforeFieldDeleteAlert', [ fieldData ] );

			if ( total === 1 ) {
				app.fieldChoiceDeleteAlert();
			} else {
				var deleteChoice = function() {
					$this.parent().remove();
					app.fieldChoiceUpdate( $list.data( 'field-type' ), $list.data( 'field-id' ) );
					$builder.trigger( 'wpformsFieldChoiceDelete' );
				};

				if ( ! fieldData.trigger ) {
					deleteChoice();

					return;
				}

				$.confirm( {
					title: false,
					content: fieldData.message,
					backgroundDismiss: false,
					closeIcon: false,
					icon: 'fa fa-exclamation-circle',
					type: 'orange',
					buttons: {
						confirm: {
							text: wpforms_builder.ok,
							btnClass: 'btn-confirm',
							keys: [ 'enter' ],
							action: function() {
								deleteChoice();
							},
						},
						cancel: {
							text: wpforms_builder.cancel,
						},
					},
				} );
			}
		},

		/**
		 * Field choice delete error alert.
		 *
		 * @since 1.6.7
		 */
		fieldChoiceDeleteAlert: function() {

			$.alert( {
				title: false,
				content: wpforms_builder.error_choice,
				icon: 'fa fa-info-circle',
				type: 'blue',
				buttons: {
					confirm: {
						text: wpforms_builder.ok,
						btnClass: 'btn-confirm',
						keys: [ 'enter' ],
					},
				},
			} );
		},

		/**
		 * Make field choices sortable.
		 *
		 * Currently used for select, radio, and checkboxes field types
		 *
		 * @since 1.0.0
		 */
		fieldChoiceSortable: function(type, selector) {

			selector = typeof selector !== 'undefined' ? selector : '.wpforms-field-option-'+type+' .wpforms-field-option-row-choices ul';

			$(selector).sortable({
				items  : 'li',
				axis   : 'y',
				delay  : 100,
				opacity: 0.6,
				handle : '.move',
				stop:function(e,ui){
					var id = ui.item.parent().data('field-id');
					app.fieldChoiceUpdate(type, id);
					$builder.trigger('wpformsFieldChoiceMove', ui);
				},
				update:function(e,ui){
				}
			});
		},

		/**
		 * Generate Choice label. Used in field preview template.
		 *
		 * @since 1.6.2
		 *
		 * @param {object}  data     Template data.
		 * @param {numeric} choiceID Choice ID.
		 *
		 * @returns {string} Label.
		 */
		fieldChoiceLabel: function( data, choiceID ) {

			var label = typeof data.settings.choices[choiceID].label !== 'undefined' && data.settings.choices[choiceID].label.length !== 0 ?
				wpf.sanitizeHTML( data.settings.choices[choiceID].label ) :
				wpforms_builder.choice_empty_label_tpl.replace( '{number}', choiceID );

			if ( data.settings.show_price_after_labels ) {
				label += ' - ' + wpf.amountFormatCurrency( data.settings.choices[choiceID].value );
			}

			return label;
		},

		/**
		 * Update field choices in preview area, for the Fields panel.
		 *
		 * Currently used for select, radio, and checkboxes field types.
		 *
		 * @since 1.0.0
		 */
		fieldChoiceUpdate: function( type, id ) {
			var $primary = $( '#wpforms-field-' + id + ' .primary-input' );

			// Radio, Checkbox, and Payment Multiple/Checkbox use _ template.
			if ( 'radio' === type || 'checkbox' === type || 'payment-multiple' === type || 'payment-checkbox' === type ) {

				var tmpl = wp.template( 'wpforms-field-preview-checkbox-radio-payment-multiple' ),
					data = {
						settings: wpf.getField( id ),
						order:    wpf.getChoicesOrder( id ),
						type:     'radio',
					};

				if ( 'checkbox' === type || 'payment-checkbox' === type ) {
					data.type = 'checkbox';
				}

				$( '#wpforms-field-' + id ).find( 'ul.primary-input' ).replaceWith( tmpl( data ) );

				return;
			}

			var isModernSelect = app.dropdownField.helpers.isModernSelect( $primary ),
				newChoice      = '';

			// Multiple payment choices are radio buttons.
			if ( 'payment-multiple' === type ) {
				type = 'radio';
			}

			// Checkbox payment choices are checkboxes.
			if ( 'payment-checkbox' === type ) {
				type = 'checkbox';
			}

			// Dropdown payment choices are selects.
			if ( 'payment-select' === type ) {
				type = 'select';
			}

			if ( 'select' === type ) {
				newChoice = '<option value="{label}">{label}</option>';
				$primary.find( 'option' ).not( '.placeholder' ).remove();

			} else if ( 'radio' === type || 'checkbox' === type || 'gdpr-checkbox' === type ) {
				type = 'gdpr-checkbox' === type ? 'checkbox' : type;
				$primary.find( 'li' ).remove();
				newChoice = '<li><input type="' + type + '" disabled>{label}</li>';
			}

			// Building an inner content for Primary field.
			var $choicesList        = $( '#wpforms-field-option-row-' + id + '-choices .choices-list' ),
				hasDefaults         = !! $choicesList.find( 'input.default:checked' ).length,
				modernSelectChoices = [],
				showPriceAfterLabels = $( '#wpforms-field-option-' + id + '-show_price_after_labels' ).prop( 'checked' );

			$choicesList.find( 'li' ).each( function() {

				var $this    = $( this ),
					label    = wpf.sanitizeHTML( $this.find( 'input.label' ).val().trim() ),
					value    = $this.find( 'input.value' ).val(),
					selected = $this.find( 'input.default' ).is( ':checked' ),
					choiceID = $this.data( 'key' ),
					$choice;

				if ( ! label ) {
					return;
				}

				label = label !== '' ? label : wpforms_builder.choice_empty_label_tpl.replace( '{number}', choiceID );
				label += ( showPriceAfterLabels && value ) ? ' - ' + wpf.amountFormatCurrency( value ) : '';

				// Append a new choice.
				if ( ! isModernSelect ) {
					$choice = $( newChoice.replace( /{label}/g, label ) );
					$primary.append( $choice );
				} else {
					modernSelectChoices.push(
						{
							value: label,
							label: label,
						}
					);
				}

				if ( true === selected ) {
					switch ( type ) {
						case 'select':

							if ( ! isModernSelect ) {
								$choice.prop( 'selected', 'true' );
							} else {
								modernSelectChoices[ modernSelectChoices.length - 1 ].selected = true;
							}
							break;
						case 'radio':
						case 'checkbox':
							$choice.find( 'input' ).prop( 'checked', 'true' );
							break;
					}
				}
			} );

			if ( isModernSelect ) {
				var placeholderClass  = $primary.prop( 'multiple' ) ? 'input.choices__input' : '.choices__inner .choices__placeholder',
					choicesjsInstance = app.dropdownField.helpers.getInstance( $primary );

				// Hide/show a placeholder for Modern select if it has or not default choices.
				$primary
					.closest( '.choices' )
					.find( placeholderClass )
					.toggleClass( 'wpforms-hidden', hasDefaults );

				choicesjsInstance.removeActiveItems();
				choicesjsInstance.setChoices( modernSelectChoices, 'value', 'label', true );
			}
		},

		/**
		 * Field choice bulk add toggling.
		 *
		 * @since 1.3.7
		 */
		fieldChoiceBulkAddToggle: function(el) {

			var $this  = $(el),
				$label = $this.closest('label');

			if ( $this.hasClass('bulk-add-showing') ) {

				// Import details is showing, so hide/remove it
				var $selector = $label.next('.bulk-add-display');
				$selector.slideUp(400, function() {
					$selector.remove();
				});
				$this.find('span').text(wpforms_builder.bulk_add_show);
			} else {

				var importOptions = '<div class="bulk-add-display">';

				importOptions += '<p class="heading wpforms-clear">'+wpforms_builder.bulk_add_heading+' <a href="#" class="toggle-bulk-add-presets">'+wpforms_builder.bulk_add_presets_show+'</a></p>';
				importOptions += '<ul>';
					for(var key in wpforms_preset_choices) {
						importOptions += '<li><a href="#" data-preset="'+key+'" class="bulk-add-preset-insert">'+wpforms_preset_choices[key].name+'</a></li>';
					}
				importOptions += '</ul>';
				importOptions += '<textarea placeholder="'+wpforms_builder.bulk_add_placeholder+'"></textarea>';
				importOptions += '<button class="bulk-add-insert">'+wpforms_builder.bulk_add_button+'</button>';
				importOptions += '</div>';

				$label.after(importOptions);
				$label.next('.bulk-add-display').slideDown(400, function() {
					$(this).find('textarea').focus();
				});
				$this.find('span').text(wpforms_builder.bulk_add_hide);
			}

			$this.toggleClass('bulk-add-showing');
		},

		/**
		 * Field choice bulk insert the new choices.
		 *
		 * @since 1.3.7
		 *
		 * @param {object} el DOM element.
		 */
		fieldChoiceBulkAddInsert: function( el ) {

			var $this = $( el ),
				$container = $this.closest( '.wpforms-field-option-row' ),
				$textarea = $container.find( 'textarea' ),
				$list = $container.find( '.choices-list' ),
				$choice = $list.find( 'li:first-of-type' ).clone().wrap( '<div>' ).parent(),
				choice = '',
				fieldID = $container.data( 'field-id' ),
				type = $list.data( 'field-type' ),
				nextID = Number( $list.attr( 'data-next-id' ) ),
				newValues = $textarea.val().split( '\n' ),
				newChoices = '';

			$this.prop( 'disabled', true ).html( $this.html() + ' ' + s.spinner );
			$choice.find( 'input.value,input.label' ).attr( 'value', '' );
			choice = $choice.html();

			for ( var key in newValues ) {
				if ( ! newValues.hasOwnProperty( key ) ) {
					continue;
				}
				var value     = wpf.sanitizeHTML( newValues[ key ] ).trim().replace( /"/g, '&quot;' ),
					newChoice = choice;
				newChoice = newChoice.replace( /\[choices\]\[(\d+)\]/g, '[choices][' + nextID + ']' );
				newChoice = newChoice.replace( /data-key="(\d+)"/g, 'data-key="' + nextID + '"' );
				newChoice = newChoice.replace( /value="" class="label"/g, 'value="' + value + '" class="label"' );

				// For some reasons IE has its own attribute order.
				newChoice = newChoice.replace( /class="label" type="text" value=""/g, 'class="label" type="text" value="' + value + '"' );
				newChoices += newChoice;
				nextID++;
			}
			$list.attr( 'data-next-id', nextID ).append( newChoices );

			app.fieldChoiceUpdate( type, fieldID );
			$builder.trigger( 'wpformsFieldChoiceAdd' );
			app.fieldChoiceBulkAddToggle( $container.find( '.toggle-bulk-add-display' ) );
		},

		/**
		 * Toggle fields tabs (Add Fields, Field Options.
		 *
		 * @since 1.0.0
		 */
		fieldTabToggle: function(id) {

			$('.wpforms-tab a').removeClass('active').find('i').removeClass('fa-angle-down').addClass('fa-angle-right');
			$('.wpforms-field, .wpforms-title-desc').removeClass('active');

			if (id === 'add-fields') {
				$('#add-fields').find('a').addClass('active').find('i').addClass('fa-angle-down');
				$('.wpforms-field-options').hide();
				$('.wpforms-add-fields').show()
			} else {
				$('#field-options').find('a').addClass('active').find('i').addClass('fa-angle-down');
				if (id === 'field-options') {
					$('.wpforms-field').first().addClass('active');
					id = $('.wpforms-field').first().data('field-id');
				} else {
					$('#wpforms-field-'+id).addClass('active');
				}
				$('.wpforms-field-option').hide();
				$('#wpforms-field-option-'+id).show();
				$('.wpforms-add-fields').hide();
				$('.wpforms-field-options').show();
			}
		},

		/**
		 * Watches fields being added and listens for a pagebreak field.
		 *
		 * If a pagebreak field is added, and it's the first one, then we
		 * automatically add the top and bottom pagebreak elements to the
		 * builder.
		 *
		 * @param {object} event Current DOM event.
		 * @param {number} id    Field ID.
		 * @param {string} type  Field type.
		 *
		 * @since 1.2.1
		 */
		fieldPagebreakAdd: function( event, id, type ) {

			if ( 'pagebreak' !== type ) {
				return;
			}

			var options;

			if ( ! s.pagebreakTop ) {

				s.pagebreakTop = true;
				options = {
					position: 'top',
					scroll: false,
					defaults: {
						position: 'top',
						nav_align: 'left',
					},
				};
				app.fieldAdd( 'pagebreak', options ).done( function( res ) {
					s.pagebreakTop = res.data.field.id;
					var $preview = $( '#wpforms-field-' + res.data.field.id ),
						$options = $( '#wpforms-field-option-' + res.data.field.id );

					$options.find( '.wpforms-field-option-group' ).addClass( 'wpforms-pagebreak-top' );
					$preview.addClass( 'wpforms-field-stick wpforms-pagebreak-top' );
				} );

			} else if ( ! s.pagebreakBottom ) {

				s.pagebreakBottom = true;
				options = {
					position: 'bottom',
					scroll: false,
					defaults: {
						position: 'bottom',
					},
				};
				app.fieldAdd( 'pagebreak', options ).done( function( res ) {
					s.pagebreakBottom = res.data.field.id;
					var $preview = $( '#wpforms-field-' + res.data.field.id ),
						$options = $( '#wpforms-field-option-' + res.data.field.id );

					$options.find( '.wpforms-field-option-group' ).addClass( 'wpforms-pagebreak-bottom' );
					$preview.addClass( 'wpforms-field-stick wpforms-pagebreak-bottom' );
				} );
			}
		},

		/**
		 * Watches fields being deleted and listens for a pagebreak field.
		 *
		 * If a pagebreak field is added, and it's the first one, then we
		 * automatically add the top and bottom pagebreak elements to the
		 * builder.
		 *
		 * @param {object} event Current DOM event.
		 * @param {number} id    Field ID.
		 * @param {string} type  Field type.
		 *
		 * @since 1.2.1
		 */
		fieldPagebreakDelete: function( event, id, type ) {

			if ( 'pagebreak' !== type ) {
				return;
			}

			var pagebreaksRemaining = $( '.wpforms-field-pagebreak' ).not( '.wpforms-pagebreak-top, .wpforms-pagebreak-bottom' ).length;

			if ( pagebreaksRemaining ) {
				return;
			}

			// All pagebreaks, excluding top/bottom, are gone.
			// So we need to remove the top and bottom pagebreak.
			var $preview = $( '.wpforms-preview-wrap' ),
				$top = $preview.find( '.wpforms-pagebreak-top' ),
				topID = $top.data( 'field-id' ),
				$bottom = $preview.find( '.wpforms-pagebreak-bottom' ),
				bottomID = $bottom.data( 'field-id' );

			$top.remove();
			$( '#wpforms-field-option-' + topID ).remove();
			s.pagebreakTop = false;
			$bottom.remove();
			$( '#wpforms-field-option-' + bottomID ).remove();
			s.pagebreakBottom = false;
		},

		/**
		 * Init Display Previous option for Pagebreak field.
		 *
		 * @since 1.5.8
		 *
		 * @param {jQuery} $field Page Break field jQuery object.
		 */
		fieldPageBreakInitDisplayPrevious: function( $field ) {

			var id          = $field.data( 'field-id' ),
				$prevToggle = $( '#wpforms-field-option-row-' + id + '-prev_toggle' ),
				$prev       = $( '#wpforms-field-option-row-' + id + '-prev' ),
				$prevBtn    = $field.find( '.wpforms-pagebreak-prev' );

			if ( $field.prevAll( '.wpforms-field-pagebreak.wpforms-pagebreak-normal' ).length > 0 ) {
				$prevToggle.removeClass( 'hidden' );
				$prev.removeClass( 'hidden' );
				if ( $prevToggle.find( 'input' ).is( ':checked' ) ) {
					$prevBtn.removeClass( 'wpforms-hidden' ).text( $prev.find( 'input' ).val() );
				}
			} else {
				$prevToggle.addClass( 'hidden' );
				$prev.addClass( 'hidden' );
				$prevBtn.addClass( 'wpforms-hidden' );
			}
		},

		/**
		 * Field Dynamic Choice toggle.
		 *
		 * @since 1.2.8
		 */
		fieldDynamicChoiceToggle: function( el ) {

			var $this       = $( el ),
				$thisOption = $this.parent(),
				value       = $this.val(),
				id          = $thisOption.data( 'field-id' ),
				type        = $( '#wpforms-field-option-' + id ).find( '.wpforms-field-option-hidden-type' ).val(),
				$field      = $( '#wpforms-field-' + id ),
				$choices    = $( '#wpforms-field-option-row-' + id + '-choices' ),
				$images     = $( '#wpforms-field-option-' + id + '-choices_images' );

			// Hide image choices if dynamic choices is not off.
			app.fieldDynamicChoiceToggleImageChoices();

			// Fire an event when a field's dynamic choices option was changed.
			$builder.trigger( 'wpformsFieldDynamicChoiceToggle' );

			// Loading
			wpf.fieldOptionLoading( $thisOption );

			// Remove previous dynamic post type or taxonomy source options.
			$( '#wpforms-field-option-row-' + id + '-dynamic_post_type' ).remove();
			$( '#wpforms-field-option-row-' + id + '-dynamic_taxonomy' ).remove();

			/*
			 * Post type or Taxonomy based dynamic populating.
			 */
			if ( '' !== value ) {

				// Hide choice images option, not applicable.
				$images.addClass( 'wpforms-hidden' );

				var data = {
					type    : value,
					field_id: id,
					action  : 'wpforms_builder_dynamic_choices',
					nonce   : wpforms_builder.nonce,
				};

				$.post( wpforms_builder.ajax_url, data, function( res ) {
					if ( res.success ) {

						// New option markup.
						$thisOption.after( res.data.markup );

					} else {
						console.log( res );
					}

					// Hide loading indicator.
					wpf.fieldOptionLoading( $thisOption, true );

					// Re-init tooltips for new field.
					wpf.initTooltips();

					// Trigger Dynamic source updates.
					$( '#wpforms-field-option-' + id + '-dynamic_' + value ).find( 'option:first' ).prop( 'selected', true );
					$( '#wpforms-field-option-' + id + '-dynamic_' + value ).trigger( 'change' );

				} ).fail( function( xhr, textStatus, e ) {
					console.log( xhr.responseText );
				} );

				return; // Nothing more for dynamic populating.
			}

			/*
			 * "Off" - no dynamic populating.
			 */

			// Show choice images option.
			$images.removeClass( 'wpforms-hidden' );
			$( '#wpforms-field-' + id ).find( '.wpforms-alert' ).remove();

			if ( 'checkbox' === type || 'radio' === type || 'payment-multiple' === type || 'payment-checkbox' === type ) {

				app.fieldChoiceUpdate( type, id );

				// Toggle elements and hide loading indicator.
				$choices.find( 'ul' ).removeClass( 'wpforms-hidden' );
				$choices.find( '.wpforms-alert' ).addClass( 'wpforms-hidden' );

				wpf.fieldOptionLoading( $thisOption, true );

				return; // Nothing more for those types.
			}

			// Get original field choices.
			var choices  = [],
				$primary = $field.find( '.primary-input' ),
				key;

			$( '#wpforms-field-option-row-' + id + '-choices li' ).each( function() {
				var $this = $( this );

				choices.push( {
					label: wpf.sanitizeHTML( $this.find( '.label' ).val() ),
					selected: $this.find( '.default' ).is( ':checked' ),
				} );
			} );

			// Restore field to display original field choices.
			if ( $field.hasClass( 'wpforms-field-select' ) ) {
				var isModernSelect = app.dropdownField.helpers.isModernSelect( $primary ),
					$choicesWrap   = isModernSelect ? $primary.closest( '.choices' ) : null,
					optionHTML     = '',
					selected       = false;

				// Remove previous items.
				$primary.find( 'option' ).not( '.placeholder' ).remove();

				// Hide a placeholder input for Modern.
				if ( isModernSelect && choices.length ) {
					$choicesWrap.find( 'input.choices__input' ).addClass( 'wpforms-hidden' );
				}

				// Prepare HTML for options (items) to modern/single select field.
				for ( key in choices ) {
					selected = choices[ key ].selected;

					if ( isModernSelect ) {
						optionHTML = selected ? '<div class="choices__item choices__item--choice">' + choices[ key ].label + '</div>' : '';

						$choicesWrap.find( '.choices__list--multiple' ).append( optionHTML );
					} else {
						optionHTML = '<option';
						optionHTML += selected ? ' selected>' : '>';
						optionHTML += choices[ key ].label + '</option>';

						$primary.append( optionHTML );
					}
				}

			} else {
				type = 'radio';

				if ( $field.hasClass( 'wpforms-field-checkbox' ) ) {
					type = 'checkbox';
				}

				// Remove previous items.
				$primary.empty();

				// Add new items to radio or checkbox field.
				for ( key in choices ) {
					optionHTML = '<li><input type="' + type + '" disabled';
					optionHTML += choices[ key ].selected ? ' selected>' : '>';
					optionHTML += choices[ key ].label + '</li>';

					$primary.append( optionHTML );
				}
			}

			// Toggle elements and hide loading indicator.
			$choices.find( 'ul' ).removeClass( 'wpforms-hidden' );
			$choices.find( '.wpforms-alert' ).addClass( 'wpforms-hidden' );

			wpf.fieldOptionLoading( $thisOption, true );
		},

		/**
		 * Field Dynamic Choice Source toggle.
		 *
		 * @since 1.2.8
		 */
		fieldDynamicChoiceSource: function( el ) {

			var $this       = $( el ),
				$thisOption = $this.parent(),
				value       = $this.val(),
				id          = $thisOption.data( 'field-id' ),
				form_id     = $( '#wpforms-builder-form' ).data( 'id' ),
				$choices    = $( '#wpforms-field-option-row-' + id + '-choices' ),
				$field      = $( '#wpforms-field-' + id ),
				type        = $( '#wpforms-field-option-' + id + '-dynamic_choices option:selected' ).val(),
				limit       = 20;

			// Loading.
			wpf.fieldOptionLoading( $thisOption );

			var data = {
				type    : type,
				source  : value,
				field_id: id,
				form_id : form_id,
				action  : 'wpforms_builder_dynamic_source',
				nonce   : wpforms_builder.nonce
			};

			$.post( wpforms_builder.ajax_url, data, function( res ) {

				if ( ! res.success ) {
					console.log( res );

					// Toggle elements and hide loading indicator.
					wpf.fieldOptionLoading( $thisOption, true );
					return;
				}

				// Update info box and remove old choices.
				$choices.find( '.dynamic-name' ).text( res.data.source_name );
				$choices.find( '.dynamic-type' ).text( res.data.type_name );
				$choices.find( 'ul' ).addClass( 'wpforms-hidden' );
				$choices.find( '.wpforms-alert' ).removeClass( 'wpforms-hidden' );

				// Update items.
				app.fieldDynamicChoiceSourceItems( $field, res.data.items );

				if ( $field.hasClass( 'wpforms-field-select' ) ) {
					limit = 200;
				}

				// If the source has more items than the field type can
				// ideally handle alert the user.
				if ( Number( res.data.total ) > limit ) {
					var msg = wpforms_builder.dynamic_choice_limit;

					msg = msg.replace( '{source}', res.data.source_name );
					msg = msg.replace( '{type}', res.data.type_name );
					msg = msg.replace( '{limit}', limit );
					msg = msg.replace( '{total}', res.data.total );

					$.alert( {
						title: wpforms_builder.heads_up,
						content: msg,
						icon: 'fa fa-info-circle',
						type: 'blue',
						buttons: {
							confirm: {
								text: wpforms_builder.ok,
								btnClass: 'btn-confirm',
								keys: [ 'enter' ],
							},
						},
					} );
				}

				// Toggle elements and hide loading indicator.
				wpf.fieldOptionLoading( $thisOption, true );

			} ).fail( function( xhr, textStatus, e ) {
				console.log( xhr.responseText );
			} );
		},

		/**
		 * Update a Field Items when `Dynamic Choice` Source is toggled.
		 *
		 * @since 1.6.1
		 *
		 * @param {object} $field jQuery selector for current field.
		 * @param {object} items  Items collection.
		 */
		fieldDynamicChoiceSourceItems: function( $field, items ) {

			var $primary = $field.find( '.primary-input' ),
				key      = 0;

			if ( $field.hasClass( 'wpforms-field-select' ) ) {
				var isModernSelect = app.dropdownField.helpers.isModernSelect( $primary );

				if ( isModernSelect ) {
					app.fieldDynamicChoiceSourceForModernSelect( $primary, items );
				} else {
					app.fieldDynamicChoiceSourceForClassicSelect( $primary, items );
				}

			} else {
				var type = 'radio';

				if ( $field.hasClass( 'wpforms-field-checkbox' ) ) {
					type = 'checkbox';
				}

				// Remove previous items.
				$primary.empty();

				// Add new items to radio or checkbox field.
				for ( key in items ) {
					$primary.append( '<li><input type="' + type + '" disabled> ' + wpf.sanitizeHTML( items[ key ] ) + '</li>' );
				}
			}
		},

		/**
		 * Update options for Modern style select when `Dynamic Choice` Source is toggled.
		 *
		 * @since 1.6.1
		 *
		 * @param {object} $jquerySelector jQuery selector for primary input.
		 * @param {object} items Items collection.
		 */
		fieldDynamicChoiceSourceForModernSelect: function( $jquerySelector, items ) {

			var instance = app.dropdownField.helpers.getInstance( $jquerySelector ),
				fieldId  = $jquerySelector.closest( '.wpforms-field' ).data().fieldId;

			// Destroy the instance of Choices.js.
			instance.destroy();

			// Update a placeholder.
			app.dropdownField.helpers.updatePlaceholderChoice( instance, fieldId );

			// Update options.
			app.fieldDynamicChoiceSourceForClassicSelect( $jquerySelector, items );

			// Choices.js init.
			app.dropdownField.events.choicesInit( $jquerySelector );
		},

		/**
		 * Update options for Classic style select when `Dynamic Choice` Source is toggled.
		 *
		 * @since 1.6.1
		 *
		 * @param {object} $jquerySelector jQuery selector for primary input.
		 * @param {object} items Items collection.
		 */
		fieldDynamicChoiceSourceForClassicSelect: function( $jquerySelector, items ) {

			var index     = 0,
				itemsSize = items.length;

			// Clear.
			$jquerySelector.find( 'option' ).not( '.placeholder' ).remove();

			// Add options (items) to a single <select> field.
			for ( ; index < itemsSize; index++ ) {
				var item = wpf.sanitizeHTML( items[ index ] );

				$jquerySelector.append( '<option value="' + item + '">' + item + '</option>' );
			}
		},

		/**
		 * Image choice toggle, hide image choices, image choices style, choices if Dynamic choices is not OFF.
		 *
		 * @since 1.5.8
		 */
		fieldDynamicChoiceToggleImageChoices: function() {

			$( '#wpforms-builder .wpforms-field-options .wpforms-field-option' ).each(
				function( key, value ) {
					var $option = $( value ),
						dynamicSelect = $option.find( '.wpforms-field-option-row-dynamic_choices select' );

					if (
						typeof dynamicSelect.val() !== 'undefined' &&
						'' !== dynamicSelect.val()
					) {
						$option.find( '.wpforms-field-option-row-choices_images' ).hide();
						$option.find( '.wpforms-field-option-row-choices_images_style' ).hide();
					} else {
						$option.find( '.wpforms-field-option-row-choices_images' ).show();
						$option.find( '.wpforms-field-option-row-choices_images_style' ).show();
					}
				}
			);
		},

		/**
		 * Field layout selector toggling.
		 *
		 * @since 1.3.7
		 */
		fieldLayoutSelectorToggle: function(el) {

			var $this   = $(el),
				$label  = $this.closest('label'),
				layouts = {
					'layout-1' : [
						{
							'class': 'one-half',
							'data' : 'wpforms-one-half wpforms-first'
						},
						{
							'class': 'one-half',
							'data' : 'wpforms-one-half'
						}
					],
					'layout-2' : [
						{
							'class': 'one-third',
							'data' : 'wpforms-one-third wpforms-first'
						},
						{
							'class': 'one-third',
							'data' : 'wpforms-one-third'
						},
						{
							'class': 'one-third',
							'data' : 'wpforms-one-third'
						}
					],
					'layout-3' : [
						{
							'class': 'one-fourth',
							'data' : 'wpforms-one-fourth wpforms-first'
						},
						{
							'class': 'one-fourth',
							'data' : 'wpforms-one-fourth'
						},
						{
							'class': 'one-fourth',
							'data' : 'wpforms-one-fourth'
						},
						{
							'class': 'one-fourth',
							'data' : 'wpforms-one-fourth'
						}
					],
					'layout-4' : [
						{
							'class': 'one-third',
							'data' : 'wpforms-one-third wpforms-first'
						},
						{
							'class': 'two-third',
							'data' : 'wpforms-two-thirds'
						}
					],
					'layout-5' : [
						{
							'class': 'two-third',
							'data' : 'wpforms-two-thirds wpforms-first'
						},
						{
							'class': 'one-third',
							'data' : 'wpforms-one-third'
						}
					],
					'layout-6' : [
						{
							'class': 'one-fourth',
							'data' : 'wpforms-one-fourth wpforms-first'
						},
						{
							'class': 'one-fourth',
							'data' : 'wpforms-one-fourth'
						},
						{
							'class': 'two-fourth',
							'data' : 'wpforms-two-fourths'
						}
					],
					'layout-7' : [
						{
							'class': 'two-fourth',
							'data' : 'wpforms-two-fourths wpforms-first'
						},
						{
							'class': 'one-fourth',
							'data' : 'wpforms-one-fourth'
						},
						{
							'class': 'one-fourth',
							'data' : 'wpforms-one-fourth'
						}
					],
					'layout-8' : [
						{
							'class': 'one-fourth',
							'data' : 'wpforms-one-fourth wpforms-first'
						},
						{
							'class': 'two-fourth',
							'data' : 'wpforms-two-fourths'
						},
						{
							'class': 'one-fourth',
							'data' : 'wpforms-one-fourth'
						}
					]
				};

			if ( $this.hasClass('layout-selector-showing') ) {

				// Selector is showing, so hide/remove it
				var $selector = $label.next('.layout-selector-display');
				$selector.slideUp(400, function() {
					$selector.remove();
				});
				$this.find('span').text(wpforms_builder.layout_selector_show);
			} else {

				// Create selector options
				var layoutOptions = '<div class="layout-selector-display">';

				layoutOptions += '<p class="heading">'+wpforms_builder.layout_selector_layout+'</p>';
					for(var key in layouts) {
						var layout = layouts[key];
						layoutOptions += '<div class="layout-selector-display-layout">';
						for(var key in layout) {
							layoutOptions += '<span class="'+layout[key].class+'" data-classes="'+layout[key].data+'"></span>';
						}
						layoutOptions += '</div>';
					}
				layoutOptions += '</div>';

				$label.after(layoutOptions);
				$label.next('.layout-selector-display').slideDown();
				$this.find('span').text(wpforms_builder.layout_selector_hide);
			}

			$this.toggleClass('layout-selector-showing');
		},

		/**
		 * Field layout selector, selecting a layout.
		 *
		 * @since 1.3.7
		 */
		fieldLayoutSelectorLayout: function(el) {

			var $this   = $(el),
				$label  = $this.closest('label');

			$this.parent().find('.layout-selector-display-layout').not($this).remove();
			$this.parent().find('.heading').text(wpforms_builder.layout_selector_column);
			$this.toggleClass('layout-selector-display-layout layout-selector-display-columns')
		},

		/**
		 * Field layout selector, insert into class field.
		 *
		 * @since 1.3.7
		 */
		fieldLayoutSelectorInsert: function(el) {
			var $this     = $(el),
				$selector = $this.closest('.layout-selector-display'),
				$parent   = $selector.parent(),
				$label    = $parent.find('label'),
				$input    = $parent.find('input[type=text]'),
				classes   = $this.data('classes');

			if ( $input.val() ) {
				classes = ' ' + classes;
			}

			$input.insertAtCaret(classes);

			// remove list, all done!
			$selector.slideUp(400, function() {
				$selector.remove();
			});

			$label.find('.toggle-layout-selector-display').removeClass('layout-selector-showing');
			$label.find('.toggle-layout-selector-display span').text(wpforms_builder.layout_selector_show);
		},

		//--------------------------------------------------------------------//
		// Settings Panel
		//--------------------------------------------------------------------//

		/**
		 * Element bindings for Settings panel.
		 *
		 * @since 1.0.0
		 */
		bindUIActionsSettings: function() {

			// Clicking form title/desc opens Settings panel.
			$builder.on( 'click', '.wpforms-title-desc, .wpforms-field-submit-button, .wpforms-center-form-name', function( e ) {
				e.preventDefault();
				app.panelSwitch( 'settings' );
				if ( $( this ).hasClass( 'wpforms-center-form-name' ) || $( this ).hasClass( 'wpforms-title-desc' ) ) {
					setTimeout( function() {
						$( '#wpforms-panel-field-settings-form_title' ).focus();
					}, 300 );
				}
			} );

			// Clicking form previous page break button.
			$builder.on( 'click', '.wpforms-field-pagebreak-last button', function( e ) {
				e.preventDefault();

				app.panelSwitch( 'settings' );
				$( '#wpforms-panel-field-settings-pagebreak_prev' ).focus();
			} );

			// Clicking form last page break button.
			$builder.on( 'input', '#wpforms-panel-field-settings-pagebreak_prev', function() {
				$( '.wpforms-field-pagebreak-last button' ).text( $( this ).val() );
			} );

			// Real-time updates for editing the form title.
			$builder.on( 'input', '#wpforms-panel-field-settings-form_title, #wpforms-setup-name', function() {

				var title = $.trim( $( this ).val() );

				$( '.wpforms-preview .wpforms-form-name' ).text( title );
				$( '.wpforms-center-form-name.wpforms-form-name' ).text( title );
				app.trimFormTitle();
			} );

			// Real-time updates for editing the form description.
			$builder.on( 'input', '#wpforms-panel-field-settings-form_desc', function() {
				$( '.wpforms-form-desc' ).text( $( this ).val() );
			} );

			// Real-time updates for editing the form submit button.
			$builder.on( 'input', '#wpforms-panel-field-settings-submit_text', function() {
				$( '.wpforms-field-submit input[type=submit]' ).val( $( this ).val() );
			} );

			// Toggle form reCAPTCHA setting.
			$builder.on( 'change', '#wpforms-panel-field-settings-recaptcha', function() {
				app.captchaToggle();
			} );

			// Toggle form confirmation setting fields.
			$builder.on( 'change', '.wpforms-panel-field-confirmations-type', function() {
				app.confirmationFieldsToggle( $( this ) );
			} );

			// Toggle form notification setting fields.
			$builder.on( 'change', '#wpforms-panel-field-settings-notification_enable', function() {
				app.notificationToggle();
			} );

			// Add new settings block.
			$builder.on( 'click', '.wpforms-builder-settings-block-add', function( e ) {
				e.preventDefault();

				if ( ! wpforms_builder.pro ) {
					return;
				}

				app.settingsBlockAdd( $( this ) );
			} );

			// Edit settings block name.
			$builder.on( 'click', '.wpforms-builder-settings-block-edit', function( e ) {
				e.preventDefault();

				var $el = $( this );

				if ( $el.parents( '.wpforms-builder-settings-block-header' ).find( '.wpforms-builder-settings-block-name' ).hasClass( 'editing' ) ) {
					app.settingsBlockNameEditingHide( $el );
				} else {
					app.settingsBlockNameEditingShow( $el );
				}
			} );

			// Update settings block name and close editing interface.
			$builder.on( 'blur', '.wpforms-builder-settings-block-name-edit input', function( e ) {

				// Do not fire if for onBlur user clicked on edit button - it has own event processing.
				if ( ! $( e.relatedTarget ).hasClass( 'wpforms-builder-settings-block-edit' ) ) {
					app.settingsBlockNameEditingHide( $( this ) );
				}
			} );

			// Close settings block editing interface with pressed Enter.
			$builder.on( 'keypress', '.wpforms-builder-settings-block-name-edit input', function( e ) {

				// On Enter - hide editing interface.
				if ( e.keyCode === 13 ) {
					app.settingsBlockNameEditingHide( $( this ) );

					// We need this preventDefault() to stop jumping to form name editing input.
					e.preventDefault();
				}
			} );

			// Clone settings block.
			$builder.on( 'click', '.wpforms-builder-settings-block-clone', function( e ) {
				e.preventDefault();

				app.settingsBlockPanelClone( $( this ) );
			} );

			// Toggle settings block - slide up or down.
			$builder.on( 'click', '.wpforms-builder-settings-block-toggle', function( e ) {
				e.preventDefault();

				app.settingsBlockPanelToggle( $( this ) );
			} );

			// Remove settings block.
			$builder.on( 'click', '.wpforms-builder-settings-block-delete', function( e ) {
				e.preventDefault();
				app.settingsBlockDelete( $( this ) );
			} );
		},

		/**
		 * Toggle displaying the CAPTCHA.
		 *
		 * @since 1.6.4
		 */
		captchaToggle: function() {

			var $preview = $builder.find( '.wpforms-field-recaptcha' ),
				$setting = $( '#wpforms-panel-field-settings-recaptcha' ),
				provider = $setting.data( 'provider' );

			provider = provider || 'recaptcha';

			if ( ! $preview.length ) {
				return;
			}

			if ( $setting.is( ':checked' ) ) {
				$preview
					.show()
					.toggleClass( 'is-recaptcha', 'recaptcha' === provider );

			} else {
				$preview.hide();
			}
		},

		/**
		 * Setup the Confirmation blocks.
		 *
		 * @since 1.4.8
		 */
		confirmationsSetup: function() {

			// Toggle the setting fields in each confirmation block.
			$( '.wpforms-panel-field-confirmations-type' ).each( function() {
				app.confirmationFieldsToggle( $( this ) );
			} );

			// Init TinyMCE in each confirmation block.
			$( '.wpforms-panel-field-confirmations-message' ).each( function() {
				if ( typeof tinymce !== 'undefined' && typeof wp.editor !== 'undefined' ) {
					wp.editor.initialize( $( this ).attr( 'id' ), s.tinymceDefaults );
				}
			} );

			// Validate Confirmation Redirect URL.
			$builder.on( 'focusout', '.wpforms-panel-field-confirmations-redirect', function( event ) {

				var $field = $( this ),
					url = $field.val().trim();

				$field.val( url );

				if ( wpf.isURL( url ) || url === '' ) {
					return;
				}

				$.confirm( {
					title: wpforms_builder.heads_up,
					content: wpforms_builder.redirect_url_field_error,
					backgroundDismiss: false,
					closeIcon: false,
					icon: 'fa fa-exclamation-circle',
					type: 'orange',
					buttons: {
						confirm: {
							text: wpforms_builder.ok,
							btnClass: 'btn-confirm',
							keys: [ 'enter' ],
							action: function() {
								$field.focus();
							},
						},
					},
				} );
			} );
		},

		/**
		 * Toggle the different form Confirmation setting fields.
		 *
		 * @since 1.4.8
		 */
		confirmationFieldsToggle: function($el) {
			if ( ! $el.length ) {
				return false;
			}

			var type = $el.val();
			var $block = $el.closest('.wpforms-builder-settings-block-content');

			$block.find('.wpforms-panel-field')
				.not($el.parent())
				.not('.wpforms-conditionals-enable-toggle')
				.hide();
			$block.find('.wpforms-panel-field-confirmations-'+type).closest('.wpforms-panel-field').show();
			if (type === 'message') {
				$block.find('.wpforms-panel-field-confirmations-message_scroll').parent().show();
			}
		},

		/**
		 * Toggle the displaying notification settings depending on if the
		 * notifications are enabled.
		 *
		 * @since 1.1.9
		 */
		notificationToggle: function() {

			var $notification = $( '#wpforms-panel-field-settings-notification_enable' );

			// Toggle Add new notification button.
			$( '.wpforms-notifications-add' ).toggleClass( 'hidden', '0' === $notification.val() );

			if ( $notification.find( 'option:selected' ).val() === '0' ) {
				$notification.parent().parent().find( '.wpforms-builder-settings-block' ).hide();
			} else {
				$notification.parent().parent().find( '.wpforms-builder-settings-block' ).show();
			}
		},

		/**
		 * Notifications by status alerts.
		 *
		 * @since 1.6.6
		 */
		notificationsByStatusAlerts: function() {

			$builder.on( 'change', '.wpforms-panel-content-section-notifications .wpforms-notification-by-status-alert', function( e ) {

				var $input = $( this );

				if ( ! $input.prop( 'checked' ) ) {
					return;
				}

				var $enabled = $( '.wpforms-radio-group-' + $input.attr( 'data-radio-group' ) + ':checked:not(#' + $input.attr( 'id' ) + ')' ),
					alertText = '';

				if ( $enabled.length === 0 ) {
					alertText = wpforms_builder.notification_by_status_enable_alert;
					alertText = alertText.replace( /%s/g, $input.data( 'provider-title' ) );
				} else {
					alertText = wpforms_builder.notification_by_status_switch_alert;
					alertText = alertText.replace( /%2\$s/g, $enabled.data( 'provider-title' ) );
					alertText = alertText.replace( /%1\$s/g, $input.data( 'provider-title' ) );
				}

				$.confirm( {
					title: wpforms_builder.heads_up,
					content: alertText,
					backgroundDismiss: false,
					closeIcon: false,
					icon: 'fa fa-exclamation-circle',
					type: 'orange',
					buttons: {
						confirm: {
							text: wpforms_builder.ok,
							btnClass: 'btn-confirm',
						},
					},
				} );
			} );

		},

		/**
		 * Add new settings block.
		 *
		 * @since 1.4.8
		 * @since 1.6.1 Added processing for Field Map table.
		 * @since 1.6.1.2 Registered `wpformsSettingsBlockAdded` trigger.
		 *
		 * @param {jQuery} $el Settings Block jQuery object.
		 */
		settingsBlockAdd: function($el) {

			var nextID = Number( $el.attr( 'data-next-id' ) ),
				panelID = $el.closest( '.wpforms-panel-content-section' ).data( 'panel' ),
				blockType = $el.data( 'block-type' ),
				namePrompt = wpforms_builder[ blockType + '_prompt' ],
				nameField = '<input autofocus="" type="text" id="settings-block-name" placeholder="' + wpforms_builder[ blockType + '_ph' ] + '">',
				nameError = '<p class="error">' + wpforms_builder[ blockType + '_error' ] + '</p>',
				modalContent = namePrompt + nameField + nameError;

			var modal = $.confirm( {
				container: $builder,
				title: false,
				content: modalContent,
				icon: 'fa fa-info-circle',
				type: 'blue',
				buttons: {
					confirm: {
						text: wpforms_builder.ok,
						btnClass: 'btn-confirm',
						keys: [ 'enter' ],
						action: function() {

							var settingsBlockName = $.trim( this.$content.find( 'input#settings-block-name' ).val() ),
								error = this.$content.find( '.error' );

							if ( settingsBlockName === '' ) {
								error.show();
								return false;
							} else {
								var $firstSettingsBlock = $el.closest( '.wpforms-panel-content-section' ).find( '.wpforms-builder-settings-block' ).first();

								// Restore tooltips before cloning.
								wpf.restoreTooltips( $firstSettingsBlock );

								var $newSettingsBlock = $firstSettingsBlock.clone(),
									blockID = $firstSettingsBlock.data( 'block-id' ),
									newSettingsBlock;

								$newSettingsBlock.attr( 'data-block-id', nextID );
								$newSettingsBlock.find( '.wpforms-builder-settings-block-header span' ).text( settingsBlockName );
								$newSettingsBlock.find( 'input, textarea, select' ).not( '.from-name input' ).not( '.from-email input' ).each( function( index, el ) {
									var $this = $( this );
									if ( $this.attr( 'name' ) ) {
										$this.val( '' ).attr( 'name', $this.attr( 'name' ).replace( /\[(\d+)\]/, '[' + nextID + ']' ) );
										if ( $this.is( 'select' ) ) {
											$this.find( 'option' ).prop( 'selected', false ).attr( 'selected', false );
											$this.find( 'option:first' ).prop( 'selected', true ).attr( 'selected', 'selected' );
										} else if ( $this.attr( 'type' ) === 'checkbox' ) {
											$this.prop( 'checked', false ).attr( 'checked', false ).val( '1' );
										} else {
											$this.val( '' ).attr( 'value', '' );
										}
									}
								} );

								// Update elements IDs.
								var idPrefixPanel = 'wpforms-panel-field-' + panelID + '-',
									idPrefixBlock = idPrefixPanel + blockID;
								$newSettingsBlock.find( '[id^="' + idPrefixBlock + '"], [for^="' + idPrefixBlock + '"]' ).each( function( index, el ) {

									var $el = $( this ),
										attr = $el.prop( 'tagName' ) === 'LABEL' ? 'for' : 'id',
										elID  = $el.attr( attr ).replace( new RegExp( idPrefixBlock, 'g' ), idPrefixPanel + nextID );

									$el.attr( attr, elID );
								} );

								// Update `notification by status` checkboxes.
								var radioGroup = blockID + '-notification-by-status';
								$newSettingsBlock.find( '[data-radio-group="' + radioGroup + '"]' ).each( function( index, el ) {

									$( this )
										.removeClass( 'wpforms-radio-group-' + radioGroup )
										.addClass( 'wpforms-radio-group-' + nextID + '-notification-by-status' )
										.attr( 'data-radio-group', nextID + '-notification-by-status' );
								} );

								$newSettingsBlock.find( '.wpforms-builder-settings-block-header input' ).val( settingsBlockName ).attr( 'value', settingsBlockName );

								if ( blockType === 'notification' ) {
									$newSettingsBlock.find( '.email-msg textarea' ).val( '{all_fields}' ).attr( 'value', '{all_fields}' );
									$newSettingsBlock.find( '.email-recipient input' ).val( '{admin_email}' ).attr( 'value', '{admin_email}' );
								}

								$newSettingsBlock.removeClass( 'wpforms-builder-settings-block-default' );

								if ( blockType === 'confirmation' ) {
									$newSettingsBlock.find( '.wpforms-panel-field-textarea' ).remove();
									if ( typeof WPForms !== 'undefined' ) {
										$newSettingsBlock.find( '.wpforms-panel-field-confirmations-type-wrap' )
											.after( WPForms.Admin.Builder.Templates
												.get( 'wpforms-builder-confirmations-message-field' )( {
													id: nextID,
												} )
											);
									}
								}

								// Conditional logic, if present
								var $conditionalLogic = $newSettingsBlock.find( '.wpforms-conditional-block' );
								if ( $conditionalLogic.length && typeof WPForms !== 'undefined' ) {
									$conditionalLogic
										.html( WPForms.Admin.Builder.Templates
											.get( 'wpforms-builder-conditional-logic-toggle-field' )( {
												id: nextID,
												type: blockType,
												actions: JSON.stringify( $newSettingsBlock.find( '.wpforms-panel-field-conditional_logic-checkbox' ).data( 'actions' ) ),
												actionDesc: $newSettingsBlock.find( '.wpforms-panel-field-conditional_logic-checkbox' ).data( 'action-desc' ),
											} )
										);
								}

								// Fields Map Table, if present.
								var $fieldsMapTable = $newSettingsBlock.find( '.wpforms-field-map-table' );
								if ( $fieldsMapTable.length ) {
									$fieldsMapTable.each( function( index, el ) {

										var $table = $( el );

										// Clean table fields.
										$table.find( 'tr:not(:first-child)' ).remove();

										var $input  = $table.find( '.key input' ),
											$select = $table.find( '.field select' ),
											name    = $select.data( 'name' );

										$input.attr( 'value', '' );
										$select
											.attr( 'name', '' )
											.attr( 'data-name', name.replace( /\[(\d+)\]/, '[' + nextID + ']' ) );
									} );
								}

								newSettingsBlock = $newSettingsBlock.wrap( '<div>' ).parent().html();
								newSettingsBlock = newSettingsBlock.replace( /\[conditionals\]\[(\d+)\]\[(\d+)\]/g, '[conditionals][0][0]' );

								$firstSettingsBlock.before( newSettingsBlock );
								var $addedSettingBlock = $firstSettingsBlock.prev();

								// Reset the confirmation type to the 1st one.
								if ( blockType === 'confirmation' ) {
									app.confirmationFieldsToggle( $( '.wpforms-panel-field-confirmations-type' ).first() );
								}

								// Init the WP Editor.
								if ( typeof tinymce !== 'undefined' && typeof wp.editor !== 'undefined' && blockType === 'confirmation' ) {
									wp.editor.initialize( 'wpforms-panel-field-confirmations-message-' + nextID, s.tinymceDefaults );
								}

								// Init tooltips for new section.
								wpf.initTooltips();

								$builder.trigger( 'wpformsSettingsBlockAdded', [ $addedSettingBlock ] );

								$el.attr( 'data-next-id', nextID + 1 );
							}
						},
					},
					cancel: {
						text: wpforms_builder.cancel,
					},
				},
			} );

			// We need to process this event here, because we need a confirm modal object defined, so we can intrude into it.
			// Pressing Enter will click the Ok button.
			$builder.on( 'keypress', '#settings-block-name', function( e ) {

				if ( e.keyCode === 13 ) {
					$( modal.buttons.confirm.el ).trigger( 'click' );
				}
			} );
		},

		/**
		 * Show settings block editing interface.
		 *
		 * @since 1.4.8
		 */
		settingsBlockNameEditingShow: function ($el) {

			var header_holder = $el.parents('.wpforms-builder-settings-block-header'),
				name_holder   = header_holder.find('.wpforms-builder-settings-block-name');

			name_holder
				.addClass('editing')
				.hide();

			// Make the editing interface active and in focus
			header_holder.find('.wpforms-builder-settings-block-name-edit').addClass('active');
			wpf.focusCaretToEnd(header_holder.find('input'));
		},

		/**
		 * Update settings block name and hide editing interface.
		 *
		 * @since 1.4.8
		 */
		settingsBlockNameEditingHide: function ($el) {

			var header_holder = $el.parents('.wpforms-builder-settings-block-header'),
				name_holder   = header_holder.find('.wpforms-builder-settings-block-name'),
				edit_holder   = header_holder.find('.wpforms-builder-settings-block-name-edit'),
				current_name  = edit_holder.find('input').val().trim(),
				blockType     = $el.closest('.wpforms-builder-settings-block').data('block-type');

			// Provide a default value for empty settings block name.
			if (! current_name.length) {
				current_name = wpforms_builder[blockType + '_def_name'];
			}

			// This is done for sanitizing.
			edit_holder.find('input').val(current_name);
			name_holder.text(current_name);

			// Editing should be hidden, displaying - active.
			name_holder
				.removeClass('editing')
				.show();
			edit_holder.removeClass('active');
		},

		/**
		 * Clone the Notification block with all of its content and events.
		 * Put the newly created clone above the target.
		 *
		 * @since 1.6.5
		 *
		 * @param {object} $el Clone icon DOM element.
		 */
		settingsBlockPanelClone: function( $el ) {

			var $panel               = $el.closest( '.wpforms-panel-content-section' ),
				$addNewSettingButton = $panel.find( '.wpforms-builder-settings-block-add' ),
				$settingsBlock       = $el.closest( '.wpforms-builder-settings-block' ),
				$settingBlockContent = $settingsBlock.find( '.wpforms-builder-settings-block-content' ),
				settingsBlockId      = parseInt( $addNewSettingButton.attr( 'data-next-id' ), 10 ),
				settingsBlockType    = $settingsBlock.data( 'block-type' ),
				settingsBlockName    = $settingsBlock.find( '.wpforms-builder-settings-block-name' ).text() + wpforms_builder[ settingsBlockType + '_clone' ],
				isVisibleContent     = $settingBlockContent.is( ':hidden' );

			// Restore tooltips before cloning.
			wpf.restoreTooltips( $settingsBlock );

			var $clone = $settingsBlock.clone( false, true );

			// Save open/close state while cloning.
			app.settingsBlockUpdateState( isVisibleContent, settingsBlockId, settingsBlockType );

			// Change the cloned setting block ID and name.
			$clone.data( 'block-id', settingsBlockId );
			$clone.find( '.wpforms-builder-settings-block-header span' ).text( settingsBlockName );
			$clone.find( '.wpforms-builder-settings-block-header input' ).val( settingsBlockName );
			$clone.removeClass( 'wpforms-builder-settings-block-default' );

			// Change the Next Settings block ID for "Add new" button.
			$addNewSettingButton.attr( 'data-next-id', settingsBlockId + 1 );

			// Change the name attribute.
			$clone.find( 'input, textarea, select' ).each( function() {
				var $this = $( this );

				if ( $this.attr( 'name' ) ) {
					$this.attr( 'name', $this.attr( 'name' ).replace( /\[(\d+)\]/, '[' + settingsBlockId + ']' ) );
				}
				if ( $this.data( 'name' ) ) {
					$this.data( 'name', $this.data( 'name' ).replace( /\[(\d+)\]/, '[' + settingsBlockId + ']' ) );
				}
				if ( $this.attr( 'class' ) ) {
					$this.attr( 'class', $this.attr( 'class' ).replace( /-(\d+)/, '-' + settingsBlockId ) );
				}
				if ( $this.attr( 'data-radio-group' ) ) {
					$this.attr( 'data-radio-group', $this.attr( 'data-radio-group' ).replace( /(\d+)-/, settingsBlockId + '-' ) );
				}
			} );

			// Change IDs/data-attributes in DOM elements.
			$clone.find( '*' ).each( function() {
				var $this = $( this );

				if ( $this.attr( 'id' ) ) {
					$this.attr( 'id', $this.attr( 'id' ).replace( /-(\d+)/, '-' + settingsBlockId ) );
				}
				if ( $this.attr( 'for' ) ) {
					$this.attr( 'for', $this.attr( 'for' ).replace( /-(\d+)-/, '-' + settingsBlockId + '-' ) );
				}
				if ( $this.data( 'input-name' ) ) {
					$this.data( 'input-name', $this.data( 'input-name' ).replace( /\[(\d+)\]/, '[' + settingsBlockId + ']' ) );
				}
			} );

			// Transfer selected values to copied elements since jQuery doesn't clone the current selected state.
			$settingsBlock.find( 'select' ).each( function() {
				var baseSelectName   = $( this ).attr( 'name' ),
					clonedSelectName = $( this ).attr( 'name' ).replace( /\[(\d+)\]/, '[' + settingsBlockId + ']' );

				$clone.find( 'select[name="' + clonedSelectName + '"]' ).val( $( this ).attr( 'name', baseSelectName ).val() );
			} );

			// Insert before the target settings block.
			$clone
				.css( 'display', 'none' )
				.insertBefore( $settingsBlock )
				.show( 'fast', function() {

					// Init tooltips for new section.
					wpf.initTooltips();
				} );
		},

		/**
		 * Show or hide settings block panel content.
		 *
		 * @since 1.4.8
		 *
		 * @param {object} $el Toggle icon DOM element.
		 */
		settingsBlockPanelToggle: function( $el ) {

			var $settingsBlock = $el.closest( '.wpforms-builder-settings-block' ),
				settingsBlockId = $settingsBlock.data( 'block-id' ),
				settingsBlockType = $settingsBlock.data( 'block-type' ),
				$content = $settingsBlock.find( '.wpforms-builder-settings-block-content' ),
				isVisible = $content.is( ':visible' );

			$content.stop().slideToggle( {
				duration: 400,
				start: function() {

					// Send early to save fast.
					// It's animation start, so we should save the state for animation end (reversed).
					app.settingsBlockUpdateState( isVisible, settingsBlockId, settingsBlockType );
				},
				always: function() {
					if ( $content.is( ':visible' ) ) {
						$el.html( '<i class="fa fa-chevron-up"></i>' );
					} else {
						$el.html( '<i class="fa fa-chevron-down"></i>' );
					}
				},
			} );
		},

		/**
		 * Delete settings block.
		 *
		 * @since 1.4.8
		 * @since 1.6.1.2 Registered `wpformsSettingsBlockDeleted` trigger.
		 *
		 * @param {jQuery} $el Delete button element.
		 */
		settingsBlockDelete: function( $el ) {

			var	$contentSection = $el.closest( '.wpforms-panel-content-section' ),
				$currentBlock = $el.closest( '.wpforms-builder-settings-block' ),
				blockType = $currentBlock.data( 'block-type' );

			// Skip if only one block persist.
			// This condition should not execute in normal circumstances.
			if ( $contentSection.find( '.wpforms-builder-settings-block' ).length < 2 ) {
				return;
			}

			$.confirm( {
				title: false,
				content: wpforms_builder[ blockType + '_delete' ],
				icon: 'fa fa-exclamation-circle',
				type: 'orange',
				buttons: {
					confirm: {
						text: wpforms_builder.ok,
						btnClass: 'btn-confirm',
						keys: [ 'enter' ],
						action: function() {

							var settingsBlockId = $currentBlock.data( 'block-id' ),
								settingsBlockType = $currentBlock.data( 'block-type' );

							/* eslint-disable camelcase */
							$.post( wpforms_builder.ajax_url, {
								action    : 'wpforms_builder_settings_block_state_remove',
								nonce     : wpforms_builder.nonce,
								block_id  : settingsBlockId,
								block_type: settingsBlockType,
								form_id   : s.formID,
							} );
							/* eslint-enable */

							$currentBlock.remove();

							$builder.trigger( 'wpformsSettingsBlockDeleted', [ blockType, settingsBlockId ] );
						},
					},
					cancel: {
						text: wpforms_builder.cancel,
					},
				},
			} );
		},

		/**
		 * Change open/close state for setting block.
		 *
		 * @since 1.6.5
		 *
		 * @param {boolean} isVisible State status.
		 * @param {number} settingsBlockId Block ID.
		 * @param {string} settingsBlockType Block type.
		 *
		 */
		settingsBlockUpdateState: function( isVisible, settingsBlockId, settingsBlockType ) {

			$.post( wpforms_builder.ajax_url, {
				action: 'wpforms_builder_settings_block_state_save',
				state: isVisible ? 'closed' : 'opened',
				form_id: s.formID,
				block_id: settingsBlockId,
				block_type: settingsBlockType,
				nonce: wpforms_builder.nonce,
			} );
		},

		//--------------------------------------------------------------------//
		// Save and Exit
		//--------------------------------------------------------------------//

		/**
		 * Element bindings for Embed and Save/Exit items.
		 *
		 * @since 1.0.0
		 * @since 1.5.8 Added trigger on `wpformsSaved` event to remove a `newform` URL-parameter.
		 */
		bindUIActionsSaveExit: function() {

			// Embed form.
			$builder.on( 'click', '#wpforms-embed', function( e ) {

				e.preventDefault();

				if ( $( this ).hasClass( 'wpforms-disabled' ) ) {
					return;
				}

				WPFormsFormEmbedWizard.openPopup();
			} );

			// Save form.
			$builder.on( 'click', '#wpforms-save', function( e ) {
				e.preventDefault();
				app.formSave( false );
			} );

			// Exit builder.
			$builder.on( 'click', '#wpforms-exit', function( e ) {
				e.preventDefault();
				app.formExit();
			} );

			// After form save.
			$builder.on( 'wpformsSaved', function( e, data ) {

				/**
				 * Remove `newform` parameter, if it's in URL, otherwise we can to get a "race condition".
				 * E.g. form settings will be updated before some provider connection is loaded.
				 */
				wpf.removeQueryParam( 'newform' );
			} );
		},

		/**
		 * Save form.
		 *
		 * @since 1.0.0
		 */
		formSave: function(redirect) {

			var $saveBtn = $('#wpforms-save'),
				$icon    = $saveBtn.find('i'),
				$label   = $saveBtn.find('span'),
				text     = $label.text();

			if (typeof tinyMCE !== 'undefined') {
				tinyMCE.triggerSave();
			}

			$label.text(wpforms_builder.saving);
			$icon.toggleClass('fa-check fa-cog fa-spin');

			var data = {
				action: 'wpforms_save_form',
				data  : JSON.stringify($('#wpforms-builder-form').serializeArray()),
				id    : s.formID,
				nonce : wpforms_builder.nonce
			};

			return $.post( wpforms_builder.ajax_url, data, function( response ) {

				if ( response.success ) {
					wpf.savedState = wpf.getFormState( '#wpforms-builder-form' );
					wpf.initialSave = false;
					$builder.trigger( 'wpformsSaved', response.data );
					if ( true === redirect && app.isBuilderInPopup() ) {
						app.builderInPopupClose( 'saved' );
						return;
					}
					if ( true === redirect ) {
						window.location.href = wpforms_builder.exit_url;
					}
				} else {
					wpf.debug( response );
					app.formSaveError( response.data );
				}
			} ).fail( function( xhr, textStatus, e ) {

				wpf.debug( xhr );
				app.formSaveError();
			} ).always( function() {

				$label.text( text );
				$icon.toggleClass( 'fa-check fa-cog fa-spin' );
			} );
		},

		/**
		 * Form save error.
		 *
		 * @since 1.6.3
		 *
		 * @param {string} error Error message.
		 */
		formSaveError: function( error ) {

			// Default error message.
			if ( wpf.empty( error ) ) {
				error = wpforms_builder.error_save_form;
			}

			// Display error in modal window.
			$.confirm( {
				title: wpforms_builder.heads_up,
				content: '<p>' + error + '</p><p>' + wpforms_builder.error_contact_support + '</p>',
				icon: 'fa fa-exclamation-circle',
				type: 'orange',
				backgroundDismiss: false,
				closeIcon: false,
				buttons: {
					confirm: {
						text: wpforms_builder.ok,
						btnClass: 'btn-confirm',
						keys: [ 'enter' ],
					},
				},
			} );
		},

		/**
		 * Exit form builder.
		 *
		 * @since 1.0.0
		 */
		formExit: function() {

			if ( app.isBuilderInPopup() && app.formIsSaved() ) {
				app.builderInPopupClose( 'saved' );
				return;
			}

			if ( app.formIsSaved() ) {
				window.location.href = wpforms_builder.exit_url;
			} else {
				$.confirm({
					title: false,
					content: wpforms_builder.exit_confirm,
					icon: 'fa fa-exclamation-circle',
					type: 'orange',
					backgroundDismiss: false,
					closeIcon: false,
					buttons: {
						confirm: {
							text: wpforms_builder.save_exit,
							btnClass: 'btn-confirm',
							keys: [ 'enter' ],
							action: function(){
								app.formSave(true);
							}
						},
						cancel: {
							text: wpforms_builder.exit,
							action: function() {

								closeConfirmation = false;

								if ( app.isBuilderInPopup() ) {
									app.builderInPopupClose( 'canceled' );
									return;
								}

								window.location.href = wpforms_builder.exit_url;
							}
						}
					}
				});
			}
		},

		/**
		 * Close confirmation setter.
		 *
		 * @since {VESRSION}
		 *
		 * @param {boolean} confirm Close confirmation flag value.
		 */
		setCloseConfirmation: function( confirm ) {

			closeConfirmation = ! ! confirm;
		},


		/**
		 * Check current form state.
		 *
		 * @since 1.0.0
		 */
		formIsSaved: function() {

			if ( wpf.savedState == wpf.getFormState( '#wpforms-builder-form' ) ) {
				return true;
			} else {
				return false;
			}
		},

		/**
		 * Check if the builder opened in the popup (iframe).
		 *
		 * @since 1.6.2
		 *
		 * @returns {boolean} True if builder opened in the popup.
		 */
		isBuilderInPopup: function() {

			return window.self !== window.parent && window.self.frameElement.id === 'wpforms-builder-iframe';
		},

		/**
		 * Close popup with the form builder.
		 *
		 * @since 1.6.2
		 *
		 * @param {string} action Performed action: saved or canceled.
		 */
		builderInPopupClose: function( action ) {

			var $popup = window.parent.jQuery( '#wpforms-builder-elementor-popup' );

			$popup.find( '#wpforms-builder-iframe' ).attr( 'src', 'about:blank' );
			$popup.fadeOut();

			$popup.trigger( 'wpformsBuilderInPopupClose', [ action, s.formID ] );
		},

		//--------------------------------------------------------------------//
		// General / global
		//--------------------------------------------------------------------//

		/**
		 * Element bindings for general and global items
		 *
		 * @since 1.2.0
		 */
		bindUIActionsGeneral: function() {

			// Toggle Smart Tags
			$builder.on('click', '.toggle-smart-tag-display', function(e) {
				e.preventDefault();
				app.smartTagToggle(this);
			});

			$builder.on('click', '.smart-tags-list-display a', function(e) {
				e.preventDefault();
				app.smartTagInsert(this);
			});

			// Field map table, update key source
			$builder.on('input', '.wpforms-field-map-table .key-source', function(){
				var value = $(this).val(),
					$dest = $(this).parent().parent().find('.key-destination'),
					name  = $dest.data('name');
					if (value) {
						$dest.attr('name', name.replace('{source}', value.replace(/[^0-9a-zA-Z_-]/gi, '')));
					}
			});

			// Field map table, delete row
			$builder.on('click', '.wpforms-field-map-table .remove', function(e) {
				e.preventDefault();
				app.fieldMapTableDeleteRow(e, $(this));
			});

			// Field map table, Add row
			$builder.on('click', '.wpforms-field-map-table .add', function(e) {
				e.preventDefault();
				app.fieldMapTableAddRow(e, $(this));
			});

			// Global select field mapping
			$(document).on('wpformsFieldUpdate', app.fieldMapSelect);

			// Restrict user money input fields
			$builder.on( 'input', '.wpforms-money-input', function( event ) {

				var $this = $( this ),
					amount = $this.val(),
					start = $this[ 0 ].selectionStart,
					end = $this[ 0 ].selectionEnd;

				$this.val( amount.replace( /[^0-9.,]/g, '' ) );
				$this[ 0 ].setSelectionRange( start, end );
			} );

			// Format user money input fields
			$builder.on( 'focusout', '.wpforms-money-input', function( event ) {
				var $this  = $( this ),
					amount = $this.val();

				if ( ! amount ) {
					return amount;
				}

				var sanitized = wpf.amountSanitize( amount ),
					formatted = wpf.amountFormat( sanitized );

				$this.val( formatted );
			} );

			// Show/hide a group of options.
			$builder.on( 'change', '.wpforms-panel-field-toggle', function() {

				var $input = $( this );

				if ( $input.prop( 'disabled' ) ) {
					return;
				}

				$input.prop( 'disabled', true );
				app.toggleOptionsGroup( $input );
			} );

			// Don't allow users to enable payments if storing entries has
			// been disabled in the General settings.
			$builder.on( 'change', '#wpforms-panel-field-stripe-enable, #wpforms-panel-field-paypal_standard-enable, #wpforms-panel-field-authorize_net-enable', function( event ) {

				var $this = $( this ),
					gateway = $this.attr( 'id' ).replace( 'wpforms-panel-field-', '' ).replace( '-enable', '' ),
					$notificationWrap = $( '.wpforms-panel-content-section-notifications [id*="-' + gateway + '-wrap"]' );

				if ( $this.prop( 'checked' ) ) {
					var disabled = $( '#wpforms-panel-field-settings-disable_entries' ).prop( 'checked' );
					if ( disabled ) {
						$.confirm( {
							title: wpforms_builder.heads_up,
							content: wpforms_builder.payments_entries_off,
							backgroundDismiss: false,
							closeIcon: false,
							icon: 'fa fa-exclamation-circle',
							type: 'orange',
							buttons: {
								confirm: {
									text: wpforms_builder.ok,
									btnClass: 'btn-confirm',
									keys: [ 'enter' ],
								},
							},
						} );

						$this.prop( 'checked', false );
					} else {
						$notificationWrap.removeClass( 'wpforms-hidden' );
					}

				} else {
					$notificationWrap.addClass( 'wpforms-hidden' );
					$notificationWrap.find( 'input[id*="-' + gateway + '"]' ).prop( 'checked', false );
				}
			} );

			// Don't allow users to disable entries if payments has been enabled.
			$builder.on( 'change', '#wpforms-panel-field-settings-disable_entries', function( event ) {
				var $this = $( this );
				if ( $this.prop( 'checked' ) ) {

					var paymentsEnabled = $( '#wpforms-panel-field-stripe-enable' ).prop( 'checked' ) || $( '#wpforms-panel-field-paypal_standard-enable' ).prop( 'checked' ) || $( '#wpforms-panel-field-authorize_net-enable' ).prop( 'checked' );
					if ( paymentsEnabled ) {
						$.confirm( {
							title: wpforms_builder.heads_up,
							content: wpforms_builder.payments_on_entries_off,
							backgroundDismiss: false,
							closeIcon: false,
							icon: 'fa fa-exclamation-circle',
							type: 'orange',
							buttons: {
								confirm: {
									text: wpforms_builder.ok,
									btnClass: 'btn-confirm',
									keys: [ 'enter' ],
								},
							},
						} );
						$this.prop( 'checked', false );
					} else {
						$.alert( {
							title: wpforms_builder.heads_up,
							content: wpforms_builder.disable_entries,
							backgroundDismiss: false,
							closeIcon: false,
							icon: 'fa fa-exclamation-circle',
							type: 'orange',
							buttons: {
								confirm: {
									text: wpforms_builder.ok,
									btnClass: 'btn-confirm',
									keys: [ 'enter' ],
								},
							},
						} );
					}
				}
			} );

			// Upload or add an image.
			$builder.on( 'click', '.wpforms-image-upload-add', function( event ) {

				event.preventDefault();

				var $this      = $( this ),
					$container = $this.parent(),
					mediaModal;

					mediaModal = wp.media.frames.wpforms_media_frame = wp.media({
						className: 'media-frame wpforms-media-frame',
						frame:     'select',
						multiple:   false,
						title:      wpforms_builder.upload_image_title,
						library: {
							type: 'image'
						},
						button: {
							text: wpforms_builder.upload_image_button
						}
					});

					mediaModal.on( 'select', function(){

						var media_attachment = mediaModal.state().get( 'selection' ).first().toJSON();

						$container.find( '.source' ).val( media_attachment.url );
						$container.find( '.preview'  ).empty();
						$container.find( '.preview'  ).prepend( '<a href="#" title="'+wpforms_builder.upload_image_remove+'" class="wpforms-image-upload-remove"><img src="'+media_attachment.url+'"></a>' );

						if ( 'hide' === $this.data( 'after-upload' ) ) {
							$this.hide();
						}

						$builder.trigger( 'wpformsImageUploadAdd', [ $this, $container ] );
					});

					// Now that everything has been set, let's open up the frame.
					mediaModal.open();
			});

			// Remove and uploaded image.
			$builder.on( 'click', '.wpforms-image-upload-remove', function( event ) {

				event.preventDefault();

				var $container = $( this ).parent().parent();

				$container.find( '.preview' ).empty();
				$container.find( '.wpforms-image-upload-add' ).show();
				$container.find( '.source' ).val( '' );

				$builder.trigger( 'wpformsImageUploadRemove', [ $( this ), $container ] );
			});

			// Validate email smart tags in Notifications fields.
			$builder.on( 'blur', '.wpforms-notification .wpforms-panel-field-text input', function() {
				app.validateEmailSmartTags( $( this ) );
			});
			$builder.on( 'blur', '.wpforms-notification .wpforms-panel-field-textarea textarea', function() {
				app.validateEmailSmartTags( $( this ) );
			});

			// Mobile notice button click.
			$builder.on( 'click', '#wpforms-builder-mobile-notice button', app.exitBack );

			// License Alert close button click.
			$( '#wpforms-builder-license-alert .close' ).on( 'click', app.exitBack );
		},

		/**
		 * Toggle a options group.
		 *
		 * @since 1.6.3
		 *
		 * @param {object} $input Toggled field.
		 */
		toggleOptionsGroup: function( $input ) {

			var name        = $input.attr( 'name' ),
				type        = $input.attr( 'type' ),
				value       = '',
				$body       = $( '.wpforms-panel-field-toggle-body[data-toggle="' + name + '"]' ),
				enableInput = function() {

					$input.prop( 'disabled', false );
				};

			if ( $body.length === 0 ) {
				enableInput();

				return;
			}

			if ( 'checkbox' === type || 'radio' === type ) {
				value = $input.prop( 'checked' ) ? $input.val() : '0';
			} else {
				value = $input.val();
			}

			$body.each( function() {

				var $this = $( this );

				$this.attr( 'data-toggle-value' ).toString() === value.toString() ?
					$this.slideDown( '', enableInput ) :
					$this.slideUp( '', enableInput );
			} );
		},

		/**
		 * Toggle all option groups.
		 *
		 * @since 1.6.3
		 *
		 * @param {jQuery} $context Context container jQuery object.
		 */
		toggleAllOptionGroups: function( $context ) {

			$context = $context || $builder || $( '#wpforms-builder' ) || $( 'body' );

			if ( ! $context ) {
				return;
			}

			// Show a toggled bodies.
			$context.find( '.wpforms-panel-field-toggle' ).each( function() {

				var $input = $( this );

				$input.prop( 'disabled', true );
				app.toggleOptionsGroup( $input );
			} );
		},

		/**
		 * Smart Tag toggling.
		 *
		 * @since 1.0.1
		 */
		smartTagToggle: function( el ) {

			var $this = $( el ),
				$label = $this.closest( 'label' );

			if ( $this.hasClass( 'smart-tag-showing' ) ) {

				// Smart tags are showing, so hide/remove them
				var $list = $label.next( '.smart-tags-list-display' );
				$list.slideUp( 400, function() {
					$list.remove();
				} );
				$this.find( 'span' ).text( wpforms_builder.smart_tags_show );
			}
			else {

				// Show all fields or narrow to specific field types
				var allowed = $this.data( 'fields' ),
					type = $this.data( 'type' ),
					fields = [];

				if ( allowed && allowed.length ) {
					fields = wpf.getFields( allowed.split( ',' ), true );
				}
				else {
					fields = wpf.getFields( false, true );
				}

				// Create smart tags list
				var smartTagList = '<ul class="smart-tags-list-display">';

				if ( type === 'fields' || type === 'all' ) {
					if ( ! fields ) {
						smartTagList += '<li class="heading">' + wpforms_builder.fields_unavailable + '</li>';
					}
					else {
						smartTagList += '<li class="heading">' + wpforms_builder.fields_available + '</li>';

						for ( var field_key in wpf.orders.fields ) {

							var field_id = wpf.orders.fields[field_key];
							var label = '';

							if ( ! fields[ field_id ] ) {
								continue;
							}

							label = fields[ field_id ].label ?
								wpf.encodeHTMLEntities( wpf.sanitizeHTML( fields[ field_id ].label ) ) :
								wpforms_builder.field + ' #' + fields[ field_id ].id;
							smartTagList += '<li><a href="#" data-type="field" data-meta=\'' + fields[ field_id ].id + '\'>' + label + '</a></li>';
						}
					}
				}

				var isFieldOption = $label.attr( 'for' ).indexOf( 'wpforms-field-option-' ) !== -1;

				if ( type === 'other' || type === 'all' ) {
					smartTagList += '<li class="heading">' + wpforms_builder.other + '</li>';
					for ( var smarttag_key in wpforms_builder.smart_tags ) {
						if ( isFieldOption && wpforms_builder.smart_tags_disabled_for_fields.indexOf( smarttag_key ) > -1 ) {
							continue;
						}
						smartTagList += '<li><a href="#" data-type="other" data-meta=\'' + smarttag_key + '\'>' + wpforms_builder.smart_tags[ smarttag_key ] + '</a></li>';
					}
				}

				smartTagList += '</ul>';

				$label.after( smartTagList );
				$label.next( '.smart-tags-list-display' ).slideDown();
				$this.find( 'span' ).text( wpforms_builder.smart_tags_hide );
			}

			$this.toggleClass( 'smart-tag-showing' );
		},

		/**
		 * Smart Tag insert.
		 *
		 * @since 1.0.1
		 */
		smartTagInsert: function(el) {

			var $this   = $(el),
				$list   = $this.closest('.smart-tags-list-display'),
				$parent = $list.parent(),
				$label  = $parent.find('label'),
				$input  = $parent.find('input[type=text]'),
				meta    = $this.data('meta'),
				type    = $this.data('type');

			if ( ! $input.length ) {
				$input  = $parent.find('textarea');
			}

			// insert smart tag
			if ( type === 'field' ) {
				$input.insertAtCaret('{field_id="'+meta+'"}');
			} else {
				$input.insertAtCaret('{'+meta+'}');
			}

			// remove list, all done!
			$list.slideUp(400, function() {
				$list.remove();
			});

			$label.find('.toggle-smart-tag-display span').text(wpforms_builder.smart_tags_show);
			$label.find('.toggle-smart-tag-display').removeClass('smart-tag-showing');
		},

		/**
		 * Field map table - Delete row.
		 *
		 * @since 1.2.0
		 * @since 1.6.1.2 Registered `wpformsFieldMapTableDeletedRow` trigger.
		 */
		fieldMapTableDeleteRow: function( e, el ) {

			var $this  = $( el ),
				$row   = $this.closest( 'tr' ),
				$table = $this.closest( 'table' ),
				$block = $row.closest( '.wpforms-builder-settings-block' ),
				total  = $table.find( 'tr' ).length;

			if ( total > '1' ) {
				$row.remove();

				$builder.trigger( 'wpformsFieldMapTableDeletedRow', [ $block ] );
			}
		},

		/**
		 * Field map table - Add row.
		 *
		 * @since 1.2.0
		 * @since 1.6.1.2 Registered `wpformsFieldMapTableAddedRow` trigger.
		 */
		fieldMapTableAddRow: function( e, el ) {

			var $this  = $( el ),
				$row   = $this.closest( 'tr' ),
				$block = $row.closest( '.wpforms-builder-settings-block' ),
				choice = $row.clone().insertAfter( $row );

			choice.find( 'input' ).val( '' );
			choice.find( 'select :selected' ).prop( 'selected', false );
			choice.find( '.key-destination' ).attr( 'name', '' );

			$builder.trigger( 'wpformsFieldMapTableAddedRow', [ $block, choice ] );
		},

		/**
		 * Update field mapped select items on form updates.
		 *
		 * @since 1.2.0
		 * @since 1.6.1.2 Registered `wpformsFieldSelectMapped` trigger.
		 */
		fieldMapSelect: function( e, fields ) {

			$( '.wpforms-field-map-select' ).each( function( index, el ) {

				var $this         = $( this ),
					selected      = $this.find( 'option:selected' ).val(),
					allowedFields = $this.data( 'field-map-allowed' ),
					placeholder   = $this.data( 'field-map-placeholder' );

				// Check if custom placeholder was provided.
				if ( typeof placeholder === 'undefined' || ! placeholder ) {
					placeholder = wpforms_builder.select_field;
				}

				// Reset select and add a placeholder option.
				$this.empty().append( $( '<option>', { value: '', text : placeholder } ) );

				// If allowed fields are not defined, bail.
				if ( typeof allowedFields !== 'undefined' && allowedFields ) {
					allowedFields = allowedFields.split( ' ' );
				} else {
					return;
				}

				// Loop through the current fields, if we have fields for the form.
				if ( fields && ! $.isEmptyObject( fields ) ) {
					for ( var key in wpf.orders.fields ) {

						if ( ! Object.prototype.hasOwnProperty.call( wpf.orders.fields, key ) ) {
							continue;
						}

						var fieldID = wpf.orders.fields[ key ],
							label   = '';

						if ( ! fields[ fieldID ] ) {
							continue;
						}

						// Prepare the label.
						if ( typeof fields[ fieldID ].label !== 'undefined' && fields[ fieldID ].label.toString().trim() !== '' ) {
							label = wpf.sanitizeHTML( fields[ fieldID ].label.toString().trim() );
						} else {
							label = wpforms_builder.field + ' #' + fieldID;
						}

						// Add to select if it is a field type allowed.
						if ( $.inArray( fields[ fieldID ].type, allowedFields ) >= 0 || $.inArray( 'all-fields', allowedFields ) >= 0 ) {
							$this.append( $( '<option>', { value: fields[ fieldID ].id, text : label } ) );
						}
					}
				}

				// Restore previous value if found.
				if ( selected ) {
					$this.find( 'option[value="' + selected + '"]' ).prop( 'selected', true );
				}

				// Add a "Custom Value" option, if it support.
				var customValueSupport = $this.data( 'custom-value-support' );
				if ( typeof customValueSupport === 'boolean' && customValueSupport ) {
					$this.append(
						$( '<option>', {
							value: 'custom_value',
							text: wpforms_builder.add_custom_value_label,
							class: 'wpforms-field-map-option-custom-value',
						} )
					);
				}

				$builder.trigger( 'wpformsFieldSelectMapped', [ $this ] );
			} );
		},

		/**
		 * Validate email smart tags in Notifications fields.
		 *
		 * @param {object} $el Input field to check the value for.
		 *
		 * @since 1.4.9
		 */
		validateEmailSmartTags: function( $el ) {

			var val = $el.val();
			if ( ! val ) {
				return;
			}

			// Turns '{email@domain.com}' into 'email@domain.com'.
			// Email RegEx inspired by http://emailregex.com
			val = val.replace( /{(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))}/g, function( x ) {
				return x.slice( 1, -1 );
			} );
			$el.val( val );
		},

		//--------------------------------------------------------------------//
		// Notice.
		//--------------------------------------------------------------------//
		/**
		 * Click on the Dismiss notice button.
		 *
		 * @since 1.6.7
		 */
		dismissNotice: function() {

			$builder.on( 'click', '.wpforms-notice-dismiss', function( e ) {
				e.preventDefault();
				$( this ).closest( '.wpforms-notice' ).remove();
				$( '#wpforms-field-option-' + $( this ).data( 'field-id' ) ).remove();
			} );
		},

		//--------------------------------------------------------------------//
		// Other functions.
		//--------------------------------------------------------------------//

		/**
		 * Trim long form titles.
		 *
		 * @since 1.0.0
		 */
		trimFormTitle: function() {

			var $title = $( '.wpforms-center-form-name' );
			if ( $title.text().length > 38 ) {
				var shortTitle = $.trim( $title.text() ).substring( 0, 38 ).split( ' ' ).slice( 0, -1 ).join( ' ' ) + '...';
				$title.text( shortTitle );
			}
		},

		/**
		 * Load or refresh color picker.
		 *
		 * @since 1.2.1
		 */
		loadColorPickers: function() {
			$('.wpforms-color-picker').minicolors();
		},

		/**
		 * Hotkeys:
		 * Ctrl+P - Preview.
		 * Ctrl+E - Entries.
		 * Ctrl+S - Save.
		 *
		 * @since 1.2.4
		 */
		builderHotkeys: function() {

			$( document ).keydown( function( e ) {

				if ( ! e.ctrlKey ) {
					return;
				}

				switch ( e.keyCode ) {
					case 80: // Open Form Preview tab on Ctrl+P.
						window.open( wpforms_builder.preview_url );
						break;

					case 69: // Open Entries tab on Ctrl+E.
						window.open( wpforms_builder.entries_url );
						break;

					case 83: // Trigger the Builder save on Ctrl+S.
						$( '#wpforms-save', $builder ).click();
						break;

					default:
						return;
				}

				return false;
			} );
		},

		/**
		 * Register JS templates for various elements.
		 *
		 * @since 1.4.8
		 */
		registerTemplates: function () {
			if (typeof WPForms === 'undefined') {
				return;
			}
			WPForms.Admin.Builder.Templates.add([
				'wpforms-builder-confirmations-message-field',
				'wpforms-builder-conditional-logic-toggle-field'
			]);
		},

		/**
		 * Exit builder.
		 *
		 * @since 1.5.7
		 */
		exitBack: function() {

			if ( 1 < window.history.length && document.referrer ) {
				window.history.back();
			} else {
				window.location.href = wpforms_builder.exit_url;
			}
		},
	};

	// Provide access to public functions/properties.
	return app;

}( document, window, jQuery ) );

WPFormsBuilder.init();
