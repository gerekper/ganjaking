/* global wp, yith */
( function ( $, wp ) {
	"use strict";

	var __ = wp.i18n.__;

	var initGlobalFields = function () {
		$( document.body ).trigger( 'wc-enhanced-select-init' );
	};

	var bkAvailabilityRules = {
		dom                     : {
			rulesContainer      : $( '.yith-wcbk-availability-rules' ).first(),
			blankState          : $( '.yith-wcbk-availability-rules__blank-state' ),
			rulesList           : false,
			newRuleButton       : $( '.yith-wcbk-availability-rules__new-rule' ),
			expandCollapseButton: $( '.yith-wcbk-availability-rules__expand-collapse' ),
			saveSettingsButton  : $( '#yith-wcbk-settings-tab-actions-save' ),
			form                : $( 'form#post, form#yith-wcbk-global-availability' )
		},
		ruleToAdd               : false,
		addRuleModal            : false,
		indexes                 : {
			rules     : 1,
			dataRanges: 1
		},
		templates               : {
			rule: wp.template( 'yith-wcbk-availability-rule' )
		},
		hasTime                 : false,
		_initParams             : function () {
			this.dom.rulesList = this.dom.rulesContainer.find( '.yith-wcbk-availability-rules__list' );

			this.indexes.rules = this.dom.rulesList.find( '.yith-wcbk-availability-rule' ).length || 0;
		},
		init                    : function () {
			this._initParams();

			$( document ).on( 'change yith_wcbk_admin_booking_availability_rule_type_change', '.yith-wcbk-availability-rule__type', this.handleTypeChange );

			this.dom.newRuleButton.on( 'click', this.openAddRuleModal );
			$( document ).on( 'click', '.yith-wcbk-availability-rule__add-rule', this.addRule );
			$( document ).on( 'click', '.yith-wcbk-availability-rule__delete-rule', this.deleteRule );

			this.dom.expandCollapseButton.on( 'click', this.expandCollapseAll );

			this.expandCollapseVisibility();

			$( '.yith-wcbk-availability-rule__type' ).each( function () {
				bkAvailabilityRules.handleTypeChange.call( $( this ) );
			} );

			this.checkIfHasRules();
		},
		checkIfHasRules         : function () {
			var rules = bkAvailabilityRules.dom.rulesList.find( '.yith-wcbk-availability-rule' );

			if ( rules.length ) {
				bkAvailabilityRules.dom.rulesContainer.addClass( 'yith-wcbk-availability-rules--has-rules' );
			} else {
				bkAvailabilityRules.dom.rulesContainer.removeClass( 'yith-wcbk-availability-rules--has-rules' );
			}

		},
		handleTypeChange        : function () {
			var value                  = $( this ).val(),
				ruleContainer          = $( this ).closest( '.yith-wcbk-availability-rule' ),
				enableIfSpecific       = ruleContainer.find( '.bk_ar_enable_if_type_specific' ),
				enableIfSpecificFields = enableIfSpecific.find( 'input, select, textarea' ),
				enableIfGeneric        = ruleContainer.find( '.bk_ar_enable_if_type_generic' ),
				enableIfGenericFields  = enableIfGeneric.find( 'input, select, textarea' );

			if ( 'specific' === value ) {
				enableIfSpecific.prop( 'disabled', false );
				enableIfSpecificFields.prop( 'disabled', false );
				enableIfGeneric.attr( 'disabled', 'disabled' );
				enableIfGenericFields.attr( 'disabled', 'disabled' );

				ruleContainer.removeClass( 'yith-wcbk-availability-rule--generic-type' ).addClass( 'yith-wcbk-availability-rule--specific-type' );
			} else {
				enableIfGeneric.prop( 'disabled', false );
				enableIfGenericFields.prop( 'disabled', false );
				enableIfSpecific.attr( 'disabled', 'disabled' );
				enableIfSpecificFields.attr( 'disabled', 'disabled' );

				ruleContainer.removeClass( 'yith-wcbk-availability-rule--specific-type' ).addClass( 'yith-wcbk-availability-rule--generic-type' );
			}
		},
		getRuleToAdd            : function () {
			return bkAvailabilityRules.ruleToAdd;
		},
		openAddRuleModal        : function ( event ) {
			event.preventDefault();
			if ( !bkAvailabilityRules.getRuleToAdd() ) {
				var index = bkAvailabilityRules.nextIndex();

				bkAvailabilityRules.ruleToAdd = $( bkAvailabilityRules.templates.rule( { ruleIndex: index } ) );

				bkAvailabilityRules.addRuleModal = yith.ui.modal(
					{
						title   : __( 'Create rule', 'yith-booking-for-woocommerce' ),
						classes : { main: 'yith-wcbk-availability-rules__create-modal' },
						content : bkAvailabilityRules.ruleToAdd,
						width   : 1024,
						onClose : function () {
							bkAvailabilityRules.ruleToAdd = false;
						},
						onCreate: function () {
							bkAvailabilityRules.ruleToAdd.find( '.yith-wcbk-admin-date-picker' ).yith_wcbk_datepicker();
							initGlobalFields();
							bkAvailabilityRules.handleTypeChange.call( bkAvailabilityRules.ruleToAdd.find( '.yith-wcbk-availability-rule__type' ) );

							bkAvailabilityRules.ruleToAdd.find( '.yith-wcbk-availability-rule__title-field' ).focus();
						}
					}
				);
			}
		},
		addRule                 : function ( event ) {
			event.preventDefault();
			var rule = $( this ).closest( '.yith-wcbk-availability-rule' );
			rule.find( '.yith-wcbk-availability-rules__add-rule' ).remove();
			bkAvailabilityRules.dom.rulesList.append( rule );

			bkAvailabilityRules.addRuleModal && bkAvailabilityRules.addRuleModal.close();
			bkAvailabilityRules.addRuleModal = false;

			bkAvailabilityRules.expandCollapseVisibility();
			bkAvailabilityRules.attentionForSaving();
			bkAvailabilityRules.checkIfHasRules();
		},
		deleteRule              : function ( event ) {
			event.preventDefault();
			var rule = $( event.target ).closest( '.yith-wcbk-availability-rule' );
			rule
				.animate( { opacity: 0.3 }, 200 )
				.delay( 200 )
				.slideUp( 300, function () {
					$( this ).remove();
					bkAvailabilityRules.expandCollapseVisibility();
					bkAvailabilityRules.attentionForSaving();
					bkAvailabilityRules.checkIfHasRules();
				} );
		},
		nextIndex               : function () {
			return ++bkAvailabilityRules.indexes.rules;
		},
		expandCollapseAll       : function ( event ) {
			var button     = $( event.target ).closest( '.yith-wcbk-availability-rules__expand-collapse' ),
				rules_list = bkAvailabilityRules.dom.rulesList;

			if ( button.is( '.yith-wcbk-availability-rules__expand-collapse--collapse' ) ) {
				button.removeClass( 'yith-wcbk-availability-rules__expand-collapse--collapse' );
				rules_list.find( '.yith-wcbk-settings-section-box:not(.yith-wcbk-settings-section-box--closed) .yith-wcbk-settings-section-box__toggle' ).click();
			} else {
				button.addClass( 'yith-wcbk-availability-rules__expand-collapse--collapse' );
				rules_list.find( '.yith-wcbk-settings-section-box.yith-wcbk-settings-section-box--closed .yith-wcbk-settings-section-box__toggle' ).click();
			}
		},
		expandCollapseVisibility: function () {
			if ( bkAvailabilityRules.dom.rulesList.find( '.yith-wcbk-availability-rule' ).length ) {
				bkAvailabilityRules.dom.expandCollapseButton.show();
			} else {
				bkAvailabilityRules.dom.expandCollapseButton.hide();
			}
		},
		attentionForSaving      : function () {
			if ( bkAvailabilityRules.dom.saveSettingsButton.length ) {
				bkAvailabilityRules.dom.saveSettingsButton.removeClass( 'yith-wcbk-effect--wiggle' );
				bkAvailabilityRules.dom.saveSettingsButton.outerWidth(); // This is useful to allow restarting animation.
				bkAvailabilityRules.dom.saveSettingsButton.addClass( 'yith-wcbk-effect--wiggle' );
			}
		}
	};

	bkAvailabilityRules.init();

	var rowTemplateHandler = function ( options ) {
		var defaults = {
			template        : '',
			selectors       : {
				main  : false,
				wrap  : false,
				list  : false,
				row   : false,
				add   : false,
				remove: false
			},
			indexes         : {
				main: 'ruleIndex',
				row : 'availabilityIndex'
			},
			filterNewRowData: false,
			onAddRow        : false,
			beforeDeleteRow : false,
			onDeleteRow     : false
		};

		options           = $.extend( {}, defaults, options );
		options.selectors = $.extend( {}, defaults.selectors, options.selectors );
		options.indexes   = $.extend( {}, defaults.indexes, options.indexes );

		var self = {};

		self.template = wp.template( options.template );

		self._init = function () {
			$( document ).on( 'click', options.selectors.add, self.addRow );
			$( document ).on( 'click', options.selectors.remove, self.deleteRow );
		};

		self.getRelatedData = function ( event ) {
			var target        = !!event.target ? $( event.target ) : $( event ),
				main          = options.selectors.main ? target.closest( options.selectors.main ) : false,
				wrap          = main ? main.find( options.selectors.wrap ) : target.closest( options.selectors.wrap ),
				list          = wrap.find( options.selectors.list ),
				rows          = list.find( options.selectors.row ),
				rangeMaxIndex = Math.max.apply( null, rows.get().map( function ( _range ) {
					return parseInt( $( _range ).data( 'index' ), 10 );
				} ) ),
				newRowData    = {};

			if ( main ) {
				newRowData[ options.indexes.main ] = main.data( 'index' );
			}
			newRowData[ options.indexes.row ] = rangeMaxIndex + 1;

			var dom = {
				main: main,
				wrap: wrap,
				list: list,
				rows: rows,
				add : wrap.find( options.selectors.add )
			};

			if ( typeof options.filterNewRowData === 'function' ) {
				newRowData = options.filterNewRowData( $.extend( {}, newRowData ), $.extend( {}, dom ) );
			}

			return {
				dom       : dom,
				newRowData: newRowData
			};
		};

		self.addRow = function ( event ) {
			var data = self.getRelatedData( event ),
				row  = $( self.template( data.newRowData ) );

			data.dom.list.append( row );

			if ( typeof options.onAddRow === 'function' ) {
				options.onAddRow( row, self.getRelatedData( row ) );
			}
		};

		self.deleteRow = function ( event ) {
			var remove = true,
				row    = $( this ).closest( options.selectors.row ),
				data   = self.getRelatedData( event );

			if ( typeof options.beforeDeleteRow === 'function' ) {
				remove = options.beforeDeleteRow( row, data );
			}

			if ( remove ) {
				row.remove();

				if ( typeof options.onDeleteRow === 'function' ) {
					options.onDeleteRow( row, self.getRelatedData( data.dom.wrap ) );
				}
			}
		};

		self._init();

	};

	var onAddRowAvailabilityHandler = function ( row, data ) {
		initGlobalFields();

		var daySelect = row.find( '.yith-wcbk-availability__day' );

		// Trigger change to handle disabling already selected days.
		daySelect.val( '' ).trigger( 'change' );

		if ( data.dom.rows.length >= 8 ) {
			data.dom.add.hide();
		}

		// Retrieve the first available value
		var firstAvailableValue = daySelect.find( 'option:not(:disabled)' ).first().val() || false;
		if ( firstAvailableValue ) {
			daySelect.val( firstAvailableValue ).trigger( 'change' );
		} else {
			row.remove();
		}
	};

	var onDeleteRowAvailabilityHandler = function ( row, data ) {
		data.dom.rows.first().find( '.yith-wcbk-availability__day' ).trigger( 'change' );
		data.dom.add.show();
	};

	rowTemplateHandler(
		{
			template : 'yith-wcbk-availability-rule-date-range',
			selectors: {
				main  : '.yith-wcbk-availability-rule',
				wrap  : '.yith-wcbk-availability-rule__date-ranges',
				list  : '.yith-wcbk-availability-rule__date-ranges__list',
				row   : '.yith-wcbk-availability-rule__date-range',
				add   : '.yith-wcbk-availability-rule__date-ranges__add-range',
				remove: '.yith-wcbk-availability-rule__date-range__action--delete'
			},
			indexes  : {
				main: 'ruleIndex',
				row : 'dateRangeIndex'
			},
			onAddRow : function ( row, data ) {
				bkAvailabilityRules.handleTypeChange.call( data.dom.main.find( '.yith-wcbk-availability-rule__type' ) );
				row.find( '.yith-wcbk-admin-date-picker' ).yith_wcbk_datepicker();
				initGlobalFields();
			}
		}
	);

	rowTemplateHandler(
		{
			template       : 'yith-wcbk-availability-rule-availability',
			selectors      : {
				main  : '.yith-wcbk-availability-rule',
				wrap  : '.yith-wcbk-availability-rule__availabilities',
				list  : '.yith-wcbk-availability-rule__availabilities__list',
				row   : '.yith-wcbk-availability-rule__availability',
				add   : '.yith-wcbk-availability-rule__availabilities__add-availability',
				remove: '.yith-wcbk-availability-rule__availability__action--delete'
			},
			indexes        : {
				main: 'ruleIndex',
				row : 'availabilityIndex'
			},
			onAddRow       : onAddRowAvailabilityHandler,
			onDeleteRow    : onDeleteRowAvailabilityHandler,
			beforeDeleteRow: function ( row, data ) {
				// Prevent deleting row, if the type is 'set-hours'.
				return 'all-day' === row.find( '.yith-wcbk-availability__full-day-type' ).val();
			}
		}
	);

	rowTemplateHandler(
		{
			template        : 'yith-wcbk-availability-rule-availability-time-slot',
			selectors       : {
				main  : '.yith-wcbk-availability-rule__availability',
				wrap  : '.yith-wcbk-availability__time-slots',
				list  : '.yith-wcbk-availability__time-slots__list',
				row   : '.yith-wcbk-availability__time-slot',
				add   : '.yith-wcbk-availability-rule__availability__time-slots__add-time-slot',
				remove: '.yith-wcbk-availability-rule__availability__time-slot__action--delete'
			},
			indexes         : {
				main: 'availabilityIndex',
				row : 'timeSlotIndex'
			},
			onAddRow        : function () {
				initGlobalFields();
			},
			filterNewRowData: function ( data, dom ) {
				data.ruleIndex = dom.main.closest( '.yith-wcbk-availability-rule' ).data( 'index' );
				return data;
			},
			beforeDeleteRow : function ( row, data ) {
				if ( data.dom.rows.length < 2 ) {
					data.dom.main.find( '.yith-wcbk-availability__full-day-type' ).val( 'all-day' ).trigger( 'change' );
					return false;
				}
				return true;
			}
		}
	);

	var bkAvailabilities = {
		init                    : function () {
			$( document ).on( 'change', '.yith-wcbk-availability__full-day-type', this.fullDayTypeChangeHandler );
			$( document ).on( 'change', '.yith-wcbk-availability__day', this.dayChangeHandler );
		},
		fullDayTypeChangeHandler: function () {
			var availability         = $( this ).closest( '.yith-wcbk-availability' ),
				timeSlotsWrapper     = availability.find( '.yith-wcbk-availability__time-slots' ),
				timeSlotsListWrapper = timeSlotsWrapper.find( '.yith-wcbk-availability__time-slots__list' ),
				timeSlotsFields      = timeSlotsListWrapper.find( 'input' ),
				fullDayType          = $( this ).val()

			availability.attr( 'data-full-day-type', fullDayType );
			if ( 'all-day' === fullDayType ) {
				timeSlotsWrapper.hide();
				timeSlotsFields.prop( 'disabled', true );
			} else {
				timeSlotsWrapper.show();
				timeSlotsFields.prop( 'disabled', false );
			}
		},
		dayChangeHandler        : function () {
			var availability     = $( this ).closest( '.yith-wcbk-availability' ),
				availabilityDays = availability.parent().find( '.yith-wcbk-availability__day' ),
				selected         = availabilityDays.map( function () {
					return $( this ).val()
				} ).toArray();

			availabilityDays.each( function () {
				var currentValue = $( this ).val(),
					toDisable    = selected.filter(
						function ( id ) {
							return id !== currentValue;
						}
					),
					options      = $( this ).find( 'option' );

				options.prop( 'disabled', false );

				options.each( function () {
					var option = $( this );
					if ( toDisable.indexOf( option.val() ) >= 0 ) {
						option.prop( 'disabled', true );
					}
				} );
			} );
		}
	}

	bkAvailabilities.init();

	/**
	 * Default Availability in Product.
	 */

	rowTemplateHandler(
		{
			template       : 'yith-wcbk-default-availability',
			selectors      : {
				main  : false,
				wrap  : '.yith-wcbk-default-availabilities',
				list  : '.yith-wcbk-default-availabilities__list',
				row   : '.yith-wcbk-default-availabilities__availability',
				add   : '.yith-wcbk-default-availabilities__actions__add-availability',
				remove: '.yith-wcbk-default-availabilities__availability__action--delete'
			},
			indexes        : {
				row: 'availabilityIndex'
			},
			onAddRow       : onAddRowAvailabilityHandler,
			onDeleteRow    : onDeleteRowAvailabilityHandler,
			beforeDeleteRow: function ( row, data ) {
				// Prevent deleting row, if the type is 'set-hours'.
				return 'all-day' === row.find( '.yith-wcbk-availability__full-day-type' ).val();
			}
		}
	);

	rowTemplateHandler(
		{
			template       : 'yith-wcbk-default-availability-time-slot',
			selectors      : {
				main  : '.yith-wcbk-availability',
				wrap  : '.yith-wcbk-availability__time-slots',
				list  : '.yith-wcbk-availability__time-slots__list',
				row   : '.yith-wcbk-availability__time-slot',
				add   : '.yith-wcbk-default-availabilities__availability__time-slots__add-time-slot',
				remove: '.yith-wcbk-default-availabilities__availability__time-slot__action--delete'
			},
			indexes        : {
				main: 'availabilityIndex',
				row : 'timeSlotIndex'
			},
			onAddRow       : function () {
				initGlobalFields();
			},
			beforeDeleteRow: function ( row, data ) {
				if ( data.dom.rows.length < 2 ) {
					data.dom.main.find( '.yith-wcbk-availability__full-day-type' ).val( 'all-day' ).trigger( 'change' );
					return false;
				}
				return true;
			}
		}
	);

} )( jQuery, wp );