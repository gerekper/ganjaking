
/**
 * Admin JS
 */

( function ( $ ) {

  initAdmin = function () {
    /**
     * Dependencies Handler for ColorPicker fields
     *
     * @type {{init: ywapoDependenciesHandler.init,
     * dom: {
     * colorpickerShow: string
     * },
     * handle: ywapoDependenciesHandler.handle,
     * conditions: {
     * defaultColorpicker: string,
     * colorpickerPlaceholder: string}
     * }}
     */
    var ywapoDependenciesHandler = {
      dom               : {
        colorpickerShow   : '.option-colorpicker-show',
        addonSelectionType : '#addon-selection-type',
        addonMaxRules : '.select-max-rules',
        addonEnableMinMax : '#addon-enable-min-max',
        advancedSettingsTab : '#tab-advanced-settings',
        addonsImageHeight : '#addon-images-height',
        addonImageEqualHeights : '#addon-image-equal-height',
        timeSlotsContainer : '.time-slots-container',
        enableTimeSlots : '.enable-time-slots',
      },
      conditions          : {
        defaultColorpicker      : '.default-colorpicker',
        colorpickerPlaceholder  : '.colorpicker-placeholder',
        addonHideOptionsImages  : '#addon-hide-options-images',
        showTimeSelector        : '.show_time_selector'
      },

      init              : function () {
        var self = ywapoDependenciesHandler;

        $( document ).on( 'change', self.dom.colorpickerShow, function( event ) {
          self.handle( $( event.target ).closest('.fields').find( self.conditions.defaultColorpicker ), 'default_color' === $( event.target ).val(),   );
          self.handle( $( event.target ).closest('.fields').find( self.conditions.colorpickerPlaceholder ), 'placeholder' === $( event.target ).val(),  );
        } );

        /**
         * The user can select max - Option - Hide/Show it
         */
        $( document ).on( 'change', self.dom.addonSelectionType, function( event ) {
            let targettoHide = $( event.target ).closest( self.dom.advancedSettingsTab ).find( self.dom.addonMaxRules ),
            enableMinMax = $( event.target ).closest( self.dom.advancedSettingsTab ).find( self.dom.addonEnableMinMax ),
            addonSelectionTypeVal = $( 'input[name=addon_selection_type]:checked' ).val();

          if ( 'multiple' != addonSelectionTypeVal || enableMinMax.val() == 'no' ) {
            self.handle( targettoHide, false, 'fade' ); // Hide
          } else {
            self.handle( targettoHide, true, 'fade' ); // Show
          }
        } );

        $( document ).on( 'change', self.dom.addonEnableMinMax, function( event ) {
          $( self.dom.addonSelectionType ).trigger( 'change' );
        } );

        $( document ).on( 'change', self.conditions.addonHideOptionsImages, function( ) {
          self.handle( $( self.dom.addonsImageHeight ).closest( '.field-wrap' ), ! $(this).is(':checked') && $( self.dom.addonImageEqualHeights ).is(':checked') , 'fade' );
        } );

        $( document ).on( 'change', self.conditions.showTimeSelector, function( ) {
          let isChecked = $(this).find( 'input').is(':checked');
          if ( ! isChecked ) {
            self.handle( $(this).closest( '.fields' ).find( $( self.dom.timeSlotsContainer ) ).closest( '.field-wrap' ), $(this).find( 'input').is(':checked') , 'fade' );
          } else {
            if ( $( self.dom.enableTimeSlots ).find( 'input' ).is(':checked') ) {
              self.handle( $(this).closest( '.fields' ).find( $( self.dom.timeSlotsContainer ) ).closest( '.field-wrap' ), true , 'fade' );
            }

            $( self.dom.enableTimeSlots ).removeClass( 'disabled-enabled-by' );

          }
        } );

      // $( self.conditions.showTimeSelector ).find( 'input' ).change()
        //Sortable options.
        sortableOptions();

      },
      /**
       * Hide or Show the target depending on condition.
       * @param target
       * @param condition
       * @param eventType
       */
      handle            : function ( target, condition, eventType = 'show' ) {
        let targetHide    = $(target),
          isFadeEvent = 'fade' === eventType;

        if ( condition ) {
          if ( isFadeEvent ) {
            targetHide.fadeIn();
          } else {
            targetHide.show();
          }
        } else {
          if ( isFadeEvent ) {
            targetHide.fadeOut();
          } else {
            targetHide.hide();
          }
        }
      }

    };

    var domElements = {
      panelBlocks : '#yith_wapo_panel_blocks'
    }

    /**
     * Enable/Disable Add-ons tabs.
     */
    checkAdminTabs      = function( ev ) {
      ev.preventDefault();
      let currentTab = $( this ),
        tabs         = $( '#addon-tabs a' ),
        tab_id = currentTab.attr('id'),
        divs_container =  $( '#addon-container > div' );

      tabs.removeClass('selected');
      currentTab.addClass('selected');
      divs_container.hide();
      $( '#addon-container #tab-' + tab_id ).show();
    },

    keyUpAddonLabel = function() {
      let text = $( this ),
      option = text.closest( '.option.open' ),
      textLabel = option.find( '.addon-label-text' ),
      textValue = text.val(),
        charactersLimit = 55;

      if ( textValue.length > charactersLimit ) {
        textValue = textValue.slice(0, charactersLimit) + '...';
      }
      updateText( textLabel, textValue );

    },

    productSelected = function() {

      let productElement = $( this ),
      productTitle = productElement.find( '.yith-plugin-fw-field-wrapper .select2 .select2-selection .select2-selection__rendered' ).attr( 'title' ),
      option = productElement.closest( '.option.open' ),
      textLabel = option.find( '.addon-label-text' );

      updateText( textLabel, productTitle );

    },

      /** Update a text of an element adding the value */
    updateText = function( element1, element2 ) {
      element1.html( element2 );
    },

    /**
     * Avoid browser Save Popup with existing changes.
     */
    avoidBrowserSave      = function() {
      window.onbeforeunload = null;
    },

    /**
     * Avoid browser Save Popup with existing changes.
     */
    blockRulesShowTo      = function() {
        let option         = $( this ),
          optionVal        = option.val(),
          showToUserRoles  = $( '.yith-wapo-block-rule-show-to-user-roles' ),
          showToMembership = $( '.yith-wapo-block-rule-show-to-membership' );

        if ( 'user_roles' === optionVal ) {
          showToUserRoles.fadeIn();
          showToMembership.hide();
        } else if ( 'membership' === optionVal ) {
          showToUserRoles.hide();
          showToMembership.fadeIn();
        } else {
          showToUserRoles.fadeOut();
          showToMembership.fadeOut();
        }
    },
      /**
       * Check Min/Max Rules
       */
    initMinMaxRules = function () {
        let firstRule           = $('#min-max-rules .field.rule:first-child');
        let firstRuleValue      = firstRule.find( 'select' ).val();
        let extraRulesSelectors = $( '#min-max-rules div.rule.min-max-rule:not(:first)' );
        let addRuleElement      = $( 'div.enabled-by-addon-enable-min-max #add-min-max-rule' );
        if ( 'min' !== firstRuleValue && 'max' !== firstRuleValue || extraRulesSelectors.length ) {
          addRuleElement.hide();
        }
    };

    /**
     * Delete Min/Max rule action
     */
    deleteMinMaxrule = function () {
        let removeButton      = $( this );
        let firstRuleSelector = $( '#min-max-rules div.rule.min-max-rule:first-child select' );
        removeButton.parent().remove();
        firstRuleSelector.change();
    },

      /**
       * First Mix/Max rule action, remove all rules, show Add rule ( hide on 'exa' value )
       */
      firstMixMaxRule = function () {
        let selectorEl        = $( this ),
          selectValue         = selectorEl.val(),
          addRuleElement      = selectorEl.parents( 'div.enabled-by-addon-enable-min-max' ).find( '#add-min-max-rule' ),
          extraRulesSelectors = $( '#min-max-rules div.rule.min-max-rule:not(:first)' );

        extraRulesSelectors.remove();
        addRuleElement.show();

        if ( 'exa' === selectValue ) {
          addRuleElement.hide();
        }
    },
      /**
       * Add New Min/Max rule
       */
      addNewMinMaxRule = function () {
        let addButton               = $( this ),
          min_max_rule              = $( '#min-max-rules' ),
          firstRule                 = $('#min-max-rules .field.rule:first-child'),
          firstRuleValue            = firstRule.find( 'select' ).val(),
          addRuleElement            = addButton.parents( 'div.enabled-by-addon-enable-min-max' ).find( '#add-min-max-rule' );

        let clonedOption            = firstRule.clone(),
          clonedOptionSelect        = clonedOption.find( 'select' ),
          clonedOptionSelectOptions = clonedOptionSelect.find( 'option' );

        clonedOption.find( 'span.select2.select2-container' ).remove();
        clonedOptionSelect.select2(
          {
            minimumResultsForSearch: -1
          }
        );

        clonedOption.find('input[type=number]').val('');
        if ( 'min' === firstRuleValue || 'max' === firstRuleValue ) {
          clonedOptionSelectOptions.each( function () {
              let optionValue  = $( this ).val();
              let removeOption = false;
              if ( firstRuleValue === optionValue ) {
                removeOption = true;
              }
              if ( 'exa' === optionValue ) {
                removeOption = true;
              }
              if ( removeOption ) {
                $( this ).remove();
              }
            }
          );
        }
        min_max_rule.append( clonedOption );
        addRuleElement.hide();

        return false;
      },

      /**
       * Add conditional logic
       */
      addConditionalLogic = function ( ev) {
        ev.preventDefault();
        var ruleTemplate = $( '#conditional-rules .field.rule:first-child'),
        clonedOption     = ruleTemplate.clone( false ),
        addon_options    = JSON.parse( $( this ).closest( '#conditional-rules' ).attr( 'data-addon-options' ) ),
        select           = $( this ).closest( '#conditional-rules' ).find( 'select.addon-conditional-rule-addon' );

        let selectedValues = $( select ).map(function() {
          return $(this).val();
        }).get();

        let options = filterConditLogicOptions( addon_options, selectedValues );
        let selector = createConditionalSelector( options );

        let parent_selector = $( '<div class="yith-plugin-fw-field-wrapper yith-plugin-fw-select-field-wrapper"></div>' );
        parent_selector.append( selector );

        let newOption = clonedOption.insertBefore( '#add-conditional-rule' );

        newOption.find( '.yith-plugin-fw-select-field-wrapper:first' ).remove();
        parent_selector.insertBefore( newOption.find( 'span.is-selection' ) ).change();

        newOption.find('.addon-conditional-rule-addon-is').removeClass( 'select2-hidden-accessible enhanced' );
        newOption.find('.addon-conditional-rule-addon-is')
            .closest( '.yith-plugin-fw-select-field-wrapper' ).find( '.select2' ).remove();

        $( document.body ).trigger( 'wc-enhanced-select-init' );
        $( document.body ).trigger( 'yith-framework-enhanced-select-init' );

        selector.closest( '.yith-plugin-fw-select-field-wrapper' ).find( '.select2-selection__rendered' ).addClass( 'empty-option' );

      },

        createConditionalSelector = function( options ) {

          var selector = $( '<select>' ).attr('id', 'addon-conditional-rule-addon').attr( 'name', 'addon_conditional_rule_addon[]' ).addClass( 'addon-conditional-rule-addon wc-enhanced-select' );

          let emptyOption = $( '<option value="empty">' + yith_wapo.i18n.selectOption + '</option>' );
          selector.append( emptyOption );

          $.each( options, function( i, item ){
            if ( typeof item === 'string' ){
              return false;
            }

            let optgroup = $( '<optgroup label="' + item.label + '">' );
            $.each( item.options, function( opt_value, opt_label ){
              let option = $( '<option value="' + opt_value + '">' + opt_label + '</option>' );
              optgroup.append( option );

            } )
            selector.append( optgroup );
          } )

          return selector;

        }

        filterConditLogicOptions = function (options, selectedValues) {
          for ( var i in options ) {
            if ( $.isNumeric( i ) ){
              for ( var j in options[i].options ) {
                if ( ( $.inArray(j, selectedValues ) > -1  ) ){
                  delete options[i].options[j];
                }
              }
            }
          }
          return options;
        }

      /**
       * Remove conditional logic
       */
      removeConditionalLogic = function () {
        var removeButton = $( this );
        removeButton.parent().remove();

        var selectors    = $( '#conditional-rules' ).find( 'select.addon-conditional-rule-addon' );

        updateOtherCondSelector( selectors );

      },

      /**
       * Update Product Name on hidden input when select a new Product
       */
      updateProductNameOnSelect = function () {
        let selector = $( this ),
          optionLabelSelected = selector.closest( '.field' ).find( '.select2-selection__rendered' ).attr( 'title' ),
          productLabelOption  = selector.closest( '.fields' ).find( '.yith-wapo-product-addon-label' );

        productLabelOption.val( optionLabelSelected );

      },

      /** Close Add-on popup when click outside panel. */
      closeAddonPopup = function ( e ) {

        if ( e.target !== this ) {
          return;
        }
        closeAddonPopupAction();

      },

      /**
       * Close Addon Popup
       */
      closeAddonPopupAction = function ( ) {
        let popup    = $( '#yith-wapo-addon-overlay' ),
          currentURL = window.location.href;
        popup.fadeOut();
        currentURL = currentURL.split('&addon_id');
        window.history.pushState( '', '', currentURL[0] );
      },

      /** Add new option */
      addNewOption = function() {
        var newOptionID = $( this ).closest( '#tab-options-list' ).find( '#addon_options .option' ).length,
            addonOptionsBlock = $( this ).closest( '#addon-container' ).find( '#addon_options' );
        $('.yith-plugin-fw-upload-container button').off();

        var template = wp.template( 'new-option-' + newOptionID );
        var templateAdded = $( template() ).appendTo( addonOptionsBlock );

        templateAdded.find( '.label-in-cart-container' ).hide();


        $( document ).trigger( 'yith_fields_init' );

        adjustAddonsIndex();
      },

      /**
       * Adjust Addons Index for each option
       */
      adjustAddonsIndex = function ( ) {

        const options_array = [ 'addon_enabled', 'default', 'show_image' ];

        $.each( options_array, function( index, value ){
          let inputsSelected = $( 'input[name^="options[' + value + ']" ]' );
          inputsSelected.each( function( index ) {
            $( this ).attr('name', 'options[' + value + '][' + index + ']' );
          });
        } );

      },
      /**
       * Show/Hide Addon price condition ( fixed, percentage, multiplied )
       */
      checkAddonPriceConditions = function( ) {
        let selectedElement = $( this );
        let parentElement   = selectedElement.parents( '.option-cost' );
        let saleElement     = parentElement.find( 'div.option-price-sale' );

        if ( 'multiplied' === selectedElement.val() ) {
          saleElement.fadeOut();
        } else {
          saleElement.fadeIn();
        }
      },

      /**
       * Add a new time slot for Calendar add-on
       **/
      addTimeSlot = function( ) {
        let dateRulesContainer = $(this).closest( '.time-slots-container' ),
          ruleTemplate = dateRulesContainer.find('.slot:first-child'),
          clonedOption = ruleTemplate.clone(),
          clonedOptionSelect = clonedOption.find( 'select' );

        clonedOption.find( 'span.select2.select2-container' ).remove();
        clonedOptionSelect.select2();

        clonedOption.find('.delete-slot').show();
        dateRulesContainer.find( '.time-slots' ).append( clonedOption );

        return false;
      },

      /**
       * Delete a time slot for Calendar add-on
       **/
      deleteTimeSlot = function( ) {
        $(this).parent().remove();
      },

      changeDateRuleSelector = function() {
        var rule = $( this ).closest( '.rule' );
        rule.find('.field:not(.what)').hide();
        if ( $(this).val() == 'years' ) {
          rule.find('.field.years').fadeIn();
        } else if ( $(this).val() == 'months' ) {
          rule.find('.field.months').fadeIn();
        } else if ( $(this).val() == 'daysweek' ) {
          rule.find('.field.daysweek').fadeIn();
        } else {
          rule.find('.field.days').fadeIn();
        }
      },

      /**
       * Delete date rule
       */
      deleteDateRule = function() {
        $(this).parent().remove();
      },

      /** Add a new rule for Datepickers */
      addNewDateRule = function() {
        let ruleId     = $( this ).parents( '.option' ).data( 'index' ),
          ruleOptionId = $( this ).parents( '.date-rules' ).find( '.date-rules-container .rule' ).length,
          template     = wp.template( 'yith-wapo-date-rule-template' ),
          lastRule     = $( this ).parents( '.date-rules' ).find( '.date-rules-container .rule' ).last();

        lastRule.after( template( {
          addon_id: ruleId,
          option_id: ruleOptionId,
        } ) );

        $( document ).trigger( 'yith_fields_init' );

        return false;
      },

      /**
       * Show/Hide options depending on Price Type selection
       * */
        priceTypeOnChange = function() {
          var parent           = $(this).closest( 'div.fields' ),
              priceMethod      = $( this ).closest( 'div.fields' ).find( '.option-price-method' ).val(),
              priceType        = $( this ).val(),
              salePriceField     = parent.find('.option-cost .option-price-sale');

          if ( 'decrease' === priceMethod || 'discount' === priceMethod ) {
            salePriceField.hide();
          }

          }

      /**
       * Show/Hide options depending on Price Method selection
       * */
          priceMethodOnChange = function() {
          var parent           = $(this).closest( 'div.fields' ),
            priceMethod          = $( this ).val(),
            regularPriceField  = parent.find('.option-cost .option-price-method-increase'),
            discountPriceField = parent.find('.option-cost .option-price-method-decrease'),
            salePriceField     = parent.find('.option-cost .option-price-sale'),
            optionCost         = parent.find('.option-cost'),
            optionCostSpan     = parent.find( '.option-cost label span' );

          var label = yith_wapo.i18n.optionCostLabel;

        if ( priceMethod != 'free' && priceMethod != 'product' && priceMethod != 'value_x_product' ) {
            optionCost.fadeIn();
            if ( 'increase' == priceMethod ) {
              regularPriceField.fadeIn();
              discountPriceField.fadeOut();
              salePriceField.fadeIn();
            } else {
              regularPriceField.fadeOut();
              discountPriceField.fadeIn();
              if ( 'discount' == priceMethod ||'decrease' == priceMethod ) {
                label = yith_wapo.i18n.discountLabel;
                salePriceField.fadeOut();
              } else {
                salePriceField.fadeIn();
              }
            }
          } else {
            optionCost.fadeOut();
          }

        optionCostSpan.text( label );

      },

      NumberOptionOnChange = function() {
        let element = $( this ),
          element_val = element.val(),
          defaultElement = element.closest( '.fields' ).find( '.show-number-option-default' );

          if ( 'default' === element_val ) {
            defaultElement.fadeIn();
          } else{
            defaultElement.fadeOut();
          }

      },

      /**
       * Show/Hide options in the Date add-on when select specific or interval methods.
       */
      calendarDatePickers = function() {
        var defaultDateValue = $( this ).val(),
          parent             = $(this).closest( 'div.fields' ),
          defaultDay         = parent.find('.option-date-default-day'),
          defaultInterval    = parent.find('.option-date-default-interval');

        if ( defaultDateValue == 'specific' ) {
          defaultDay.fadeIn();
          defaultInterval.hide();
        } else if ( defaultDateValue == 'interval') {
          defaultDay.hide();
          defaultInterval.fadeIn();
        } else {
          defaultDay.fadeOut();
          defaultInterval.fadeOut();
        }
      },

      /**
       * Dates - Selectable dates onChange
       */
      calendarSelectableDates = function() {
        var selectValue = $(this).val(),
          parent = $(this).closest( 'div.fields' ),
          selectableDaysRanges = parent.find('.option-selectable-days-ranges'),
          selectableDateRanges = parent.find('.option-selectable-date-ranges');
          if ( selectValue == 'days' ) {
            selectableDateRanges.hide();
            selectableDaysRanges.fadeIn();
          } else if ( selectValue == 'date' ) {
            selectableDaysRanges.hide();
            selectableDateRanges.fadeIn();
          } else {
            selectableDaysRanges.fadeOut();
            selectableDateRanges.fadeOut();
          }
      },

      /**
       * Show or hide when select Single color, Double color or Image in the Color Swatches option.
       */
      colorSwatchesShowAs = function () {
          var selectValue = $( this ).val(),
            parent = $( this ).closest( 'div.fields' ),
            colors  = parent.find('.field-wrap.color'),
            colorImage = parent.find('.color_image');


          if ( selectValue == 'image' ) {
            colors.hide();
            colorImage.fadeIn();
          } else {
            colorImage.hide();
            colors.fadeIn();
          }
      },

      /** Enable Color Swatch B */
      addColorBSwatch = function(e) {
        e.preventDefault();

        let linkSelected = $( this ),
          colorBElement = linkSelected.closest( '.yith-color-swatches' ).find( '.color_b' ),
          colorBpicker = colorBElement.find( 'input.yith-plugin-fw-colorpicker--initialized' );

        colorBpicker.val( colorBpicker.data( 'default-color' ) );
        colorBpicker.attr( 'disabled', false );
        colorBpicker.change();

        linkSelected.hide();
        colorBElement.fadeIn();
        colorBpicker.fadeIn();

      },

      hideColorBSwatch = function () {
        let colorBIcon = $( this ),
        colorBElement = colorBIcon.closest( '.color_b' ),
        colorBpicker = colorBElement.find( 'input.yith-plugin-fw-colorpicker--initialized' ),
        linkSelected = colorBElement.closest( '.yith-color-swatches' ).find( '.color_a .color-swatch-add' );

        colorBElement.addClass( 'color-hidden' );
        colorBpicker.attr( 'disabled', true );
        colorBpicker.val('');

        colorBElement.hide();
        colorBpicker.hide();
        linkSelected.fadeIn();

      },

      /** Block rule dependencie - Show in */
      blockRuleShowIn = function () {
        var showInVal = $( this ).val(),
          showInProducts = $('#block-rules .yith-wapo-block-rule-show-in-products'),
          showInCategories = $('#block-rules .yith-wapo-block-rule-show-in-categories'),
          excludeProductsInputVal = $( '#block-rules #yith-wapo-block-rule-exclude-products' ).val(),
          excludeProductsProducts = $('#block-rules .yith-wapo-block-rule-exclude-products-products'),
          excludeProductsCategories = $('#block-rules .yith-wapo-block-rule-exclude-products-categories');

        if ( 'products' === showInVal ) {
          showInProducts.fadeIn();
          showInCategories.fadeOut();
          if ( 'yes' === excludeProductsInputVal ) {
            excludeProductsCategories.fadeOut();
          }
        } else {
          showInProducts.fadeOut();
          showInCategories.fadeOut();
          if ( 'yes' === excludeProductsInputVal ) {
            excludeProductsCategories.fadeIn();
          }
        }

      },

      /** Block rule dependency - Exclude on */
      blockRuleExcludeProducts = function () {

        var excludeProductsInputVal = $('#block-rules #yith-wapo-block-rule-exclude-products').val(),
        showInVal = $('#block-rules #yith-wapo-block-rule-show-in').val(),
        showInProducts = $('#block-rules .yith-wapo-block-rule-show-in-products'),
        showInCategories = $('#block-rules .yith-wapo-block-rule-show-in-categories'),
        excludeProductsProducts = $('#block-rules .yith-wapo-block-rule-exclude-products-products'),
        excludeProductsCategories = $('#block-rules .yith-wapo-block-rule-exclude-products-categories');
        if ( excludeProductsInputVal == 'yes' ) {
          excludeProductsProducts.fadeIn();
          if ( 'products' === showInVal ) {
            excludeProductsCategories.fadeOut();
          } else {
            excludeProductsCategories.fadeIn();
          }
        } else {
          excludeProductsProducts.fadeOut();
          excludeProductsCategories.fadeOut();
        }

      },

        /** Force the user to select one radio ALWAYS */
        selectedByDefaultRadio = function( ev ) {
          console.log( 'check' );

          let clickedRadio = $(this),
            radiosChecked = clickedRadio.closest( '#addon_options' ).find( '.selected-by-default-chbx.checkbox:checked' ),
              addonType = $( this ).closest( '#addon-editor' ).data( 'addon-type' );

          console.log( radiosChecked );

          if ( 'radio' === addonType ) {
            radiosChecked.prop( 'checked', false );
            clickedRadio.prop( 'checked', true );
          }

        },

        /**
         * Uncheck all Selected by default except one if is already checked if the selection type option is SINGLE
         */
        selectedbyDefaultConditions = function( ev ) {

          let selectedOption = $(this).val();

          if ( 'single' === selectedOption ) {
            let one_checked = false;
            $( '#tab-options-list #addon_options .selected-by-default' ).each(function(index) {
              if ( $(this).find( 'input[type="checkbox"]' ).is(':checked') ) {
                if ( one_checked ) {
                  $(this).find( 'input[type="checkbox"]' ).prop( "checked", false );
                } else {
                  one_checked = true;
                }
              }
            });
          }

        },

        /**
         * Check only one 'Selected by default' when the selection type is single.
         */
        selectedbyDefaultChecks = function ( ev ) {

          var selectedCheckbox  = $( this ),
              allCheckboxes     = $( this ).closest( '#addon_options' ).find( '.selected-by-default input[type="checkbox"]' ),
              selectionType     = $( this ).closest( '#addon-editor-type' ).find( '#tab-advanced-settings #addon-selection-type' ).val(),
              addonType         = $( this ).closest( '#addon-editor' ).data( 'addon-type' );

          if ( 'single' === selectionType || 'select' === addonType ) {
            let checkedCheckboxes = $( this ).closest( '#addon_options' ).find( '.selected-by-default input[type="checkbox"]:checked' ).length - 1;
                if ( checkedCheckboxes > 0 ) {
                  allCheckboxes.prop( 'checked', false );
                  selectedCheckbox.prop( 'checked', true );
                }
          }

        },

        sortableOptions = function ( ev ) {
          $( '#addon_options' ).sortable({
            helper: fixWidthHelper,
            revert: true,
            axis: 'y',
            delay: 150,
            update: function( event, ui ) {
              adjustAddonsIndex();
            }

          });
        },
        conditionalLogicColorInit = function ( ev ) {

          var selectors = $( '.addon-conditional-rule-addon' );

          setTimeout(function() {
            conditionalLogicColor( selectors );
          }, 400 );
        },

          conditionalLogicColor = function ( selectors ) {
          if ( selectors instanceof $.Event ) {

            let selectVal = $(this).val(),
                option = $(this).closest( '.yith-plugin-fw-select-field-wrapper' ).find( 'span.select2 .select2-selection__rendered' );

                option.removeClass( 'empty-option' );

                if ( 'empty' === selectVal ) {
                  option.addClass( 'empty-option' );
                }

                updateConditionalSelector( $(this) );

          } else {
            selectors.each(function () {
              let selectVal = $(this).val(),
                  option = $(this).closest('.yith-plugin-fw-select-field-wrapper').find('span.select2 .select2-selection__rendered');
                  option.removeClass('empty-option');
                  if ('empty' === selectVal) {
                    option.addClass('empty-option');
                  }
            });
          }


        },
          updateConditionalSelector = function( currentSelector ) {
          var selectors        = $( '#conditional-rules' ).find( 'select.addon-conditional-rule-addon' );

          updateOtherCondSelector( selectors, currentSelector );

        },
          updateOtherCondSelector = function( selectors, selector = '' ) {
            selectors.not(selector).each(
                function() {
                  var currentSelector = $(this),
                      currentSelectedValue = $(this).val(),
                      addon_options    = JSON.parse( $( '#conditional-rules' ).attr( 'data-addon-options' ) );

                  let selectedValues = $( selectors ).map(function() {
                    if ( 'empty' === $(this).val() || $(this).val() === currentSelectedValue ) {
                      return;
                    }
                    return $(this).val();
                  }).get();

                  let options = filterConditLogicOptions( addon_options, selectedValues );

                  currentSelector.empty();

                  let emptyOption = $( '<option value="empty">' + yith_wapo.i18n.selectOption + '</option>' );
                  currentSelector.append( emptyOption );

                  $.each( options, function( i, item ){
                    if ( typeof item === 'string' ){
                      return false;
                    }

                    let optgroup = $( '<optgroup label="' + item.label + '">' );
                    $.each( item.options, function( opt_value, opt_label ){

                      let option = $( '<option value="' + opt_value + '">' + opt_label + '</option>' );
                      if ( opt_value === currentSelectedValue ) {
                        option.attr( 'selected', 'selected' );
                      }

                      optgroup.append( option );

                    } )
                    currentSelector.append( optgroup );
                  } )
                }
            )
          },
    moveAddBlockButton = function() {
      let panelBlocks = $( domElements.panelBlocks ),
          addBlockButton = panelBlocks.find( '.yith-wapo-add-block' ),
          contentTitle = panelBlocks.find( '.yith-plugin-fw__panel__content__page .yith-plugin-fw__panel__content__page__title' );

          contentTitle.append( addBlockButton );
    }

    prepareItems = function() {
      $( '.option-price-method' ).change();
    }

    saveBlockOptions = function() {
          var panelBlock = $( '#yith-wapo-panel-block' ),
              blockName = panelBlock.find( '#block-name' ),
              blockError = panelBlock.find( '.block-error' );

          if ( 'new' === panelBlock.data( 'block-id' ) ) {
            return;
          }

          if ( panelBlock.length > 0 ) {
            if ( blockError.length > 0 ) {
              blockError.remove();
            }

            if ( '' === blockName.val() ) {
              blockName.addClass( 'block-error-border' );
              $( '<small class="block-error">' + yith_wapo.i18n.blockNameRequired + '</small>' ).insertAfter( blockName.closest( '.block-option' ).find( '.yith-plugin-fw-field-wrapper' ) );

              $([document.documentElement, document.body]).animate({
                scrollTop: $(blockName).offset().top - 50
              }, 1000);
              return false;
            }
          }
    }

    /** Add-ons tabs change */
    $( document ).on( 'click', '#yith-wapo-addon-overlay #addon-tabs a', checkAdminTabs );

    /** On keyup an add-on label */
    $( document ).on( 'keyup', '#yith-wapo-addon-overlay #tab-options-list .addon-option-label', keyUpAddonLabel );
    $( document ).on( 'change', '#yith-wapo-addon-overlay #addon_options .addon-product-selection', productSelected );


    /** Avoid Browser popup when redirect to another page */
    $( document ).on( 'click', '.yith-wapo a, .yith-wapo button, .yith-wapo input', avoidBrowserSave );

    /**
     * Mix/Max triggers
     */
    $( document ).on( 'click', '#add-min-max-rule a', addNewMinMaxRule );
    $( document ).on( 'click', '#min-max-rules .delete-min-max-rule', deleteMinMaxrule );
    $( document ).on( 'change', '#min-max-rules div.rule.min-max-rule:first-child select', firstMixMaxRule );
    initMinMaxRules();

    /** Block dependencies - Show to **/
    $( document ).on( 'change', '#yith-wapo-block-rule-show-to', blockRulesShowTo );

    /** Conditional logic **/
    $( document ).on( 'click', '#add-conditional-rule a', addConditionalLogic );
    $( document ).on( 'click', '#conditional-rules .delete-rule', removeConditionalLogic );
    $( document ).on( 'change', '#addon-conditional-rule-addon', conditionalLogicColor );
    conditionalLogicColorInit();

    /** Populate options tab */
    $( document ).on( 'change', 'select[name="options[product][]"]', updateProductNameOnSelect );

    /** Addon Popup */
    $( document ).on( 'click', '#yith-wapo-addon-overlay', closeAddonPopup );
    $( document ).on( 'click', '#yith-wapo-addon-overlay #close-popup, #yith-wapo-addon-overlay button.cancel', closeAddonPopupAction );

    /** Add-ons index adjust */
    $( document ).on( 'click', '#add-new-option', addNewOption );

    /** Colorpicker dependencies ( Show color / Placeholder ) */
    ywapoDependenciesHandler.init();

    /** Change price option ( fixed, percentage, multiplied ) */
    $( document ).on( 'change', '#option-price-type', checkAddonPriceConditions );

    /** Calendar - Time Slots **/
    $( document ).on( 'click', '.add-time-slot a', addTimeSlot );
    $( document ).on( 'click', '.delete-slot', deleteTimeSlot );

    /** Calendar - Date rules */
    $( document ).on( 'change', '.date-rules .select_what', changeDateRuleSelector );
    $( document ).on( 'click', '.add-date-rule a', addNewDateRule );
    $( document ).on( 'click' , '.date-rules .delete-rule', deleteDateRule );

    /**	Calendar - Date picker*/
    $( document ).on('change', '.fields .field .option-date-default', calendarDatePickers);
    $( document ).on( 'change', '#tab-options-list .option-selectable-dates', calendarSelectableDates );

    /** Price type - onChange */
    $( document ).on( 'change', '#tab-options-list .option-price-method', priceMethodOnChange );
    $( document ).on( 'change', '#tab-options-list .option-price-type', priceTypeOnChange );

    /** Number field - onChange */
    $( document ).on( 'change', '#tab-options-list .show_number_option', NumberOptionOnChange );

    /** Color swatch */
    $( document ).on( 'change', '#tab-options-list .color-show-as select', colorSwatchesShowAs);
    $( document ).on( 'click', '#tab-options-list .yith-color-swatches .color_a .color-swatch-add', addColorBSwatch);
    $( document ).on( 'click', '#tab-options-list .yith-color-swatches .color_b', hideColorBSwatch);

    /** Blocks rules dependencies */
    $( document ).on( 'change', '#block-rules #yith-wapo-block-rule-show-in', blockRuleShowIn );
    $( document ).on( 'change', '#block-rules #yith-wapo-block-rule-exclude-products', blockRuleExcludeProducts );
    blockRuleExcludeProducts();

    /** Selected by default */
    $( document ).on( 'click', '.addon-editor-type-radio .selected-by-default-chbx.checkbox', selectedByDefaultRadio );
    $( document ).on( 'change', '#addon-selection-type', selectedbyDefaultConditions );
    $( document ).on( 'click', '#addon_options .selected-by-default input[type="checkbox"]', selectedbyDefaultChecks );

    /** Add block button*/
    moveAddBlockButton();

    /**
     * On change methods once is updated.
     */
    prepareItems();

    /** Save Block options */
    $( document ).on( 'click', '.yith-plugins_page_yith_wapo_panel .yith-plugin-fw__panel__sidebar .yith-plugin-fw__panel__submenu-item, ' +
        '.yith-plugins_page_yith_wapo_panel .yith-plugin-fw__panel__sidebar #yith-plugin-fw__panel__menu-item-blocks, ' +
        '.yith-plugins_page_yith_wapo_panel .yith-plugin-fw__panel__sidebar #yith-plugin-fw__panel__menu-item-style, ' +
        '.yith-plugins_page_yith_wapo_panel .yith-plugin-fw__panel__sidebar #yith-plugin-fw__panel__menu-item-premium, ' +
        '#yith-wapo-panel-block #save-button .yith-save-button, #yith-wapo-panel-block .back-to-block-list',
        saveBlockOptions );


  };

  /** Init Admin JS */
  initAdmin();


	/*
	 *
	 *	enable/disable
	 *	blocks
	 *
	 * * * * * * * * * * * * * * * * * * * */

	$('#sortable-blocks').on( 'change', '.active .yith-plugin-fw-onoff-container input', function() {

		var blockID = $(this).closest( '.block-element' ).attr( 'data-id' );
		var blockVisibility = 0;

        if ( $(this).is(':checked') ) {
          blockVisibility = 1;
        }

		// Ajax method
		var data = {
			'action'		: 'enable_disable_block',
			'block_id'		: blockID,
			'block_vis'		: blockVisibility,
		};
		$.post( ajaxurl, data, function(response) {
			console.log( '[YITH.LOG] - Block visibility updated' );
		});

	});

	/*
	 *
	 *	enable/disable
	 *	addons
	 *
	 * * * * * * * * * * * * * * * * * * * */

	$( '#sortable-addons' ).on( 'change', '.addon-onoff input', function() {

		var addonID         = $(this).closest( '.addon-element' ).attr( 'data-id' );
		var addonVisibility = 0;

        if ( $( this ).is( ':checked' ) ) {
          addonVisibility = 1;
        }

		// Ajax method
		var data = {
			'action'		: 'enable_disable_addon',
			'addon_id'		: addonID,
			'addon_vis'		: addonVisibility,
		};
		$.post( ajaxurl, data, function(response) {
			console.log( '[YITH.LOG] - Addon visibility updated' );
		});

	});

	/*
	 *
	 *	SORTABLE BLOCKS IN OPTIONS BLOCKS TABLE
	 *
	 *
	 * * * * * * * * * * * * * * * * * * * */

	$( '#sortable-blocks' ).sortable( {
        containment: '#yith_wapo_panel_blocks .yith-plugin-fw-panel-custom-tab-container',
		helper: fixWidthHelper,
		revert: true,
		axis: 'y',
        delay: 300,
		update: function ( event, ui ) {
          var itemID = $( ui.item ).data('id'),
              movedItem = $( ui.item ).attr('data-priority'),
              prevItem  = parseFloat( $( ui.item ).prev().attr('data-priority') ),
              nextItem  = parseFloat( $( ui.item ).next().attr('data-priority') );

			var data = {
				'action'		: 'sortable_blocks',
                'item_id'       : itemID,
				'moved_item'	: movedItem,
				'prev_item'		: prevItem,
				'next_item'		: nextItem,
			};

          $.post( ajaxurl, data, function(response) {
                    var data = response.data;
                    var itemID = data.itemID,
                    itemPR = parseFloat( data.itemPriority );
                    var blockSelected = $( '#sortable-blocks #block-' + itemID );

                    blockSelected.attr( 'data-priority', itemPR );
                    blockSelected.find( 'td.priority' ).html( Math.round( itemPR ) );
          } );
		}
	} );

  /*
  *
  *	SORTABLE ADD-ONS IN ADD-ON OPTIONS MODAL
  *
  *
  * * * * * * * * * * * * * * * * * * * */

	$( '#sortable-addons' ).sortable( {
    containment: '#block-addons-container',
		revert: true,
		axis: 'y',
		update: function ( event, ui ) {
			var movedItem = ui.item.data('id');
			var prevItem  = parseFloat( ui.item.prev().data('priority') );
			var nextItem  = parseFloat( ui.item.next().data('priority') );
			// Ajax method
			var data = {
				'action'		: 'sortable_addons',
				'moved_item'	: movedItem,
				'prev_item'		: prevItem,
				'next_item'		: nextItem,
			};
			$.post( ajaxurl, data, function(response) {
				var res = response.split('-');
				var itemID = res[0];
				var itemPR = parseFloat( res[1] );
				$( '#sortable-addons #addon-' + itemID ).attr( 'data-priority', itemPR );
			} );
		}
	});

	$( 'ul, li, tbody, tr, td' ).disableSelection();
	function fixWidthHelper( e, ui ) {
		ui.children().each(function() { $(this).width( $(this).width() ); });
		return ui;
	}

	/*
	 *
	 *	options dependencies (enablers)
	 *	only for simple onoff options
	 *	function: check enablers
	 *
	 * * * * * * * * * * * * * * * * * * * */

	$( document ).on( 'change', '.enabler input', function() {
    yith_wapo_check_enablers( $( this ) );
  });

  $('.yith-wapo .enabler input').each( function() {
    yith_wapo_check_enablers( $( this ) );
  });

	function yith_wapo_check_enablers( enabler ) {

      let fieldWrap = enabler.closest(' .field-wrap ');

      if ( fieldWrap.hasClass( 'disabled-enabled-by' ) ) {
        return false;
      }

      let reverted = false,
        enabledByElement = $( '.enabled-by-' + enabler.attr('id') );
      if ( enabler.closest( '.enabler' ).hasClass( 'revert' ) ) {
        reverted = true;
      }

      if ( enabler.is(':checked') ) {
        if ( reverted ) {
          enabledByElement.fadeOut();
        } else {
          enabledByElement.fadeIn();
        }
      } else {
        if ( reverted ) {
          enabledByElement.fadeIn();
        } else {
          enabledByElement.fadeOut();
        }
      }
	}

	// HTML Separator
	$('.yith-wapo').on('change', '#option-separator-style', function() {
		if ( $(this).val() == 'empty_space' ) {
			$('.field-wrap.option-separator-color').fadeOut();
		} else {
			$('.field-wrap.option-separator-color').fadeIn();
		}
	});

	/*
	 *
	 *	option toggle
	 *
	 * * * * * * * * * * * * * * * * * * * */

	$('#tab-options-list').on( 'click', '.option .title', function( e ) {
    let itemClicked = jQuery( e.target );
    if ( itemClicked.hasClass( 'selected-by-default-chbx' ) ) {
        return;
    }
    var fieldsContainer = $(this).parent().find('.fields');
    fieldsContainer.toggle();
    if ( fieldsContainer.is(':visible') ) {
        $(this).parent().removeClass('close').addClass('open');
    } else {
        $(this).parent().removeClass('open').addClass('close');
    }

  });

	/*
	 *
	 *	remove option
	 *
	 * * * * * * * * * * * * * * * * * * * */

	$( document ).on( 'click', '#addon-container .yith-plugin-fw__action-button--delete-action', function( ev ) {
      ev.preventDefault();

    let clickedRadio = $( this );
    $( this ).closest( '.option' ).remove();


    let radiosChecked = $( '#addon_options' ).find( '.selected-by-default-chbx.checkbox:checked' ).length,
      firstRadio =      $( '#addon_options' ).find( '.selected-by-default-chbx.checkbox' ).first();

    if ( $( '#addon-editor-type' ).hasClass( 'addon-editor-type-radio' ) ) {

      if ( radiosChecked < 1 ) {
        firstRadio.prop( 'checked', true );
      }
    }

    adjustAddonsIndex();
	});


	$('#tab-options-list').on( 'click', '.option .title', function() {
		$(this).parent().find('.color-show-as select').change();
	});
	$('#tab-options-list').find('.color-show-as select').change();

	/*
	 *
	 *	Conditional logic
	 *
	 * * * * * * * * * * * * * * * * * * * */
	$( document ).on( 'select2:open', function ( e ) {
		$( '.select2-results' ).closest( '.select2-container' ).addClass( 'yith-addons-select2-container' );
	} );
} )( jQuery );
