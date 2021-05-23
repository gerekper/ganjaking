( function( $ ) {

	var $dateInputType                = $( '#field_date_input_type' ),
		$minDateSelect                = $( '#gpld-minimum-date' ),
		$maxDateSelect                = $( '#gpld-maximum-date' ),
		$minDateMod                   = $( '#gpld-min-date-modifier' ),
		$minDateExcludeBeforeToday    = $( '#gpld-min-date-exclude-before-today' ),
		$minDateExcludeBeforeTodayMod = $( '#gpld-min-date-exclude-before-today-modifier' ),
		$maxDateMod                   = $( '#gpld-max-date-modifier' ),
		$minDateInput                 = $( '#gpld-min-date-input' ),
		$maxDateInput                 = $( '#gpld-max-date-input' ),
		$dateInputResetButtons        = $( '.gpld-date-input-reset' ),
		$allDays                      = $( '#gpld-all-days' ),
		$daysOfWeek                   = $( '.gpld-days-of-week-container input' ),
		$exceptionsRepeater           = $( '.gpld-exceptions-repeater' ),
		$addExceoption                = $( '#gpld-add-exception' ),
		$addExceptionInput            = $( '#gpld-add-exception-input' ),
		$inlineDatepicker             = $( '#gpld-inline-datepicker' ),
		key                           = function( key ) { return GPLD_ADMIN.key + key; };

	$dateInputType.change( function() {
		fieldSettingsInit( GetSelectedField() );
		gperk.togglePerksTab();
	} );

	$minDateSelect.change( function() {
		handleDateSelect( $( this ) );
	} );

	$minDateExcludeBeforeToday.change( function() {
		handleExcludeBeforeTodayCheck( $( this ), $minDateSelect );
	} );

	$maxDateSelect.change( function() {
		handleDateSelect( $( this ) );
	} );

	$dateInputResetButtons.click( function() {
		hideDateInput( $( this ) );
	} );

	$allDays.click( function() {
		handleAllDaysClick( $daysOfWeek );
	} );

	$daysOfWeek.change( function() {
		handleDayOfWeekSelection( $daysOfWeek );
	} );

	$addExceoption.click( function() {
		addException();
	} );

	$( document ).bind( 'gform_load_field_settings', function( event, field, form ) {
		fieldSettingsInit( field );
	} );

	function fieldSettingsInit( field ) {

		if ( GetInputType( field ) == 'date' && field.dateType == 'datepicker' ) {
			$( '.gp-limit-dates-field-settings' ).show();
		} else {
			$( '.gp-limit-dates-field-settings' ).hide();
			return;
		}

		resetUI();

		populateDateSelect( $minDateSelect, field );
		$minDateSelect.val( field[ key( 'minDate' ) ] );
		$minDateSelect.change(); /* trigger change() as separate command to avoid conflict with Gravity Slider's jquery.nouislider.all.js */
		$minDateInput.val( field[ key( 'minDateValue' ) ] );
		$minDateMod.val( field[ key( 'minDateMod' ) ] );
		$minDateExcludeBeforeToday.attr( 'checked', field[ key( 'minDateExcludeBeforeToday' ) ] === true ).change();
		$minDateExcludeBeforeTodayMod.val( field[ key( 'minDateExcludeBeforeTodayMod' ) ] );

		populateDateSelect( $maxDateSelect, field );
		$maxDateSelect.val( field[ key( 'maxDate' ) ] );
		$maxDateSelect.change(); /* trigger change() as separate command to avoid conflict with Gravity Slider's jquery.nouislider.all.js */
		$maxDateInput.val( field[ key( 'maxDateValue' ) ] );
		$maxDateMod.val( field[ key( 'maxDateMod' ) ] );

		$minDateInput.datepicker( { dateFormat: 'mm/dd/yy' } );
		$maxDateInput.datepicker( { dateFormat: 'mm/dd/yy' } );

		populateDaysOfWeek();

		initExceptionsRepeater();

		$inlineDatepicker.prop( 'checked', ( field[ key( 'inlineDatepicker' ) ] == true ) );

	}

	function populateDateSelect( $select, field ) {

		$.each( GPLD_ADMIN.dateSelectOptions, function( i, option ) {
			if ( option.value == '_datefields_' ) {
				option.options = []; // reset
				for ( var j = 0; j < form.fields.length; j++ ) {
					if ( field.id != form.fields[j].id && GetInputType( form.fields[j] ) == 'date' && form.fields[j].dateType == 'datepicker' ) {
						option.options.push( {
							label: GetLabel( form.fields[j] ),
							value: form.fields[j].id
						} );
					}
				}
			}
		} );

		$select.html( getDateSelectMarkup( GPLD_ADMIN.dateSelectOptions ) );

	}

	function getDateSelectMarkup( options ) {

		var markup = '';

		$.each( options, function( i, option ) {

			if ( typeof option.options != 'undefined' ) {
				markup += '<optgroup label="' + option.label + '">' + getDateSelectMarkup( option.options ) + '</optgroup>';
			} else {
				markup += '<option value="' + option.value + '">' + option.label + '</option>';
			}

		} );

		return markup;
	}

	function handleDateSelect( $select ) {

		var value     = $select.val(),
			isCustom  = value == '_custom_',
			isMinDate = $select.attr( 'id' ) === 'gpld-minimum-date';

		if ( ! value ) {

			hideModifierInput( $select );
			if ( isMinDate ) {
				hideExcludeBeforeTodayInput( $select );
				hideExcludeBeforeTodayModifierInput( $select );
			}

		} else {

			if ( isCustom ) {
				showDateInput( $select );
				hideModifierInput( $select );
				if ( isMinDate ) {
					showExcludeBeforeTodayInput( $select );
				}
			} else {
				showModifierInput( $select );
				if ( isMinDate ) {
					hideExcludeBeforeTodayInput( $select );
				}
			}

		}

	}

	function showDateInput( $select ) {

		var $group = $select.parents( '.gp-group' );
		var $input = $group.find( '.gpld-date-input-container' );

		$select.parent().hide();
		$input.show();

	}

	function hideDateInput( $button ) {

		var $input  = $button.parents( '.gpld-date-input-container' ),
			$select = $input.parents( '.gp-row' ).find( '.gpld-date-select-container' );

		$input.hide();
		$input.find( 'input' ).val( '' ).change();

		$select.find( 'select' ).val( '' ).change();
		$select.show();


	}

	function resetUI() {

		// min/max dates
		$( '.gpld-date-select-container' ).show();
		$( '.gpld-date-modifier-container' ).hide();
		$( '.gpld-date-input-container' ).hide();

		// days of week
		$( '.gpld-all-days-container' ).show();
		$( '.gpld-days-of-week-container' ).hide();

	}

	function showModifierInput( $select ) {

		var $modifier = $select.parents( '.gp-row' ).find( '.gpld-date-modifier-container' );

		if ( ! $modifier.is( ':visible' ) ) {
			$modifier.show();
		}

	}

	function hideModifierInput( $select ) {

		var $modifier = $select.parents( '.gp-row' ).find( '.gpld-date-modifier-container' );

		$modifier.hide()
			.find( 'input' )
			.val( '' )
			.change()
			.keyup();

	}

	function handleExcludeBeforeTodayCheck( $checkbox, $select ) {
		if ( $checkbox.is( ':checked' ) ) {
			showExcludeBeforeTodayModifierInput( $select );
		} else {
			hideExcludeBeforeTodayModifierInput( $select );
		}
	}

	function showExcludeBeforeTodayInput() {

		var $excludeBeforeToday = jQuery( '#gpld-min-date-exclude-before-today-container' ).parents( '.gp-row' );

		if ( ! $excludeBeforeToday.is( ':visible' ) ) {
			$excludeBeforeToday.show();
		}

	}

	function hideExcludeBeforeTodayInput() {

		var $excludeBeforeToday = jQuery( '#gpld-min-date-exclude-before-today-container' ).parents( '.gp-row' );

		$excludeBeforeToday
			.hide()
			.find( 'input' )
			.attr( 'checked', false );

	}

	function showExcludeBeforeTodayModifierInput() {

		var $excludeBeforeTodayMod = jQuery( '#gpld-min-date-exclude-before-today-modifier-container' );

		if ( ! $excludeBeforeTodayMod.is( ':visible' ) ) {
			$excludeBeforeTodayMod.show();
		}

	}

	function hideExcludeBeforeTodayModifierInput() {

		var $excludeBeforeTodayMod = jQuery( '#gpld-min-date-exclude-before-today-modifier-container' );

		$excludeBeforeTodayMod.hide()
			.find( 'input' )
			.val( '' )
			.change()
			.keyup();

	}

	function populateDaysOfWeek() {

		var daysOfWeek = typeof field[ key( 'daysOfWeek' ) ] == 'object' ? field[ key( 'daysOfWeek' ) ] : [ 0, 1, 2, 3, 4, 5, 6 ];

		$daysOfWeek.each( function() {
			$( this ).prop( 'checked', $.inArray( parseInt( $( this ).val() ), daysOfWeek ) != - 1 );
		} );

		if ( daysOfWeek.length > 0 && daysOfWeek.length < 7 ) {
			toggleDaysOfWeek( true );
			handleDayOfWeekSelection();
		}

	}

	function handleAllDaysClick( $daysOfWeek ) {
		$daysOfWeek.prop( 'checked', false );
		SetFieldProperty( key( 'daysOfWeek' ), getSelectedDaysOfWeek() );
		handleDayOfWeekSelection( $daysOfWeek );
		toggleDaysOfWeek();
	}

	function handleDayOfWeekSelection() {

		var $container   = $( '.gpld-days-of-week-container' ),
			checkedCount = getSelectedDaysOfWeek().length;

		if ( checkedCount > 0 ) {
			$container.addClass( 'has-selection' ).removeClass( 'no-selection' );
		} else {
			$container.addClass( 'no-selection' ).removeClass( 'has-selection' );
		}

		if ( checkedCount >= 7 ) {
			toggleDaysOfWeek();
		}

		SetFieldProperty( key( 'daysOfWeek' ), getSelectedDaysOfWeek() );

	}

	function getSelectedDaysOfWeek() {
		var days = [];
		$.each( $daysOfWeek.filter( ':checked' ), function( i, day ) {
			days.push( parseInt( day.value ) );
		} );
		return days;
	}

	function toggleDaysOfWeek( isInit ) {

		var isInit            = typeof isInit == 'undefined' ? false : isInit,
			$allDaysContainer = $( '.gpld-all-days-container' ),
			$daysContainer    = $( '.gpld-days-of-week-container' ),
			$hide             = $allDaysContainer.is( ':visible' ) || isInit ? $allDaysContainer : $daysContainer,
			$show             = ! $allDaysContainer.is( ':visible' ) && ! isInit ? $allDaysContainer : $daysContainer;

		$hide.hide();
		$show.show();

	}

	function initExceptionsRepeater() {

		var items = getExceptionItems();

		// reset HTML when re-initing repeater
		if ( $exceptionsRepeater.data( 'htmlTemplate' ) ) {
			$exceptionsRepeater.html( $exceptionsRepeater.data( 'htmlTemplate' ) );
		} else {
			$exceptionsRepeater.data( 'htmlTemplate', $exceptionsRepeater.html() );
		}

		$exceptionsRepeater.repeater( {
			limit: 0,
			items: items,
			minItemCount: 0,
			addButtonMarkup: '',
			removeButtonMarkup: '<span class="remove"><i class="fa fa-times"></i></span>',
			callbacks: {
				save: function( obj, data ) {
					var dates = [];
					for ( var i = 0; i < data.length; i++ ) {
						dates.push( data[i].date );
					}
					SetFieldProperty( key( 'exceptions' ), dates );
				}
			}
		} );

		if ( items.length == 1 && ! items[0].date ) {
			$exceptionsRepeater.removeItem( 0 );
		}

	}

	function getExceptionItems() {

		var exceptions = typeof field[ key( 'exceptions' ) ] == 'object' ? field[ key( 'exceptions' ) ] : [],
			items      = [];

		if ( exceptions.length <= 0 ) {
			items.push( { date: '' } );
		} else {
			for ( var i = 0; i < exceptions.length; i++ ) {
				items.push( { date: exceptions[i] } );
			}
		}

		return items;
	}

	function addException() {

		$addExceptionInput.datepicker( {
			dateFormat: 'mm/dd/yy',
			onSelect: function( date ) {
				$exceptionsRepeater.addNewItem( { date: date } );
			}
		} ).datepicker( 'show' );

	}

} )( jQuery );
