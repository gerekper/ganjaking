/*! jQuery UI - v1.12.1 - 2019-02-17
 * http://jqueryui.com
 * Modified version of jQuery Datepicker
 * Copyright jQuery Foundation and other contributors; Licensed MIT */

( function( factory ) {
	'use strict';
	if ( typeof window.define === 'function' && window.define.amd ) {
		// AMD. Register as an anonymous module.
		window.define( [ 'jquery' ], factory );
	} else {
		// Browser globals
		factory( window.jQuery );
	}
}( function( $ ) {
	'use strict';
	/*!
	 * jQuery UI Datepicker 1.12.1 (Modified version of jQuery Datepicker)
	 * http://jqueryui.com
	 *
	 * Copyright jQuery Foundation and other contributors
	 * Released under the MIT license.
	 * http://jquery.org/license
	 *
	 * http://api.jqueryui.com/datepicker/
	 */

	var datepicker_instActive;

	$.ui.tm_datepicker = $.ui.tm_datepicker || {};
	if ( $.ui.tm_datepicker.version ) {
		return;
	}

	$.extend( $.ui, { tm_datepicker: { version: '1.11.4' } } );

	function datepicker_getZindex( elem ) {
		var position, value;
		while ( elem.length && elem[ 0 ] !== document ) {
			// Ignore z-index if position is set to a value where z-index is ignored by the browser
			// This makes behavior of this function consistent across browsers
			// WebKit always returns auto if the element is positioned
			position = elem.css( 'position' );
			if ( position === 'absolute' || position === 'relative' || position === 'fixed' ) {
				// IE returns 0 when zIndex is not specified
				// other browsers return a string
				// we ignore the case of nested elements with an explicit value of 0
				value = parseInt( elem.css( 'zIndex' ), 10 );
				if ( ! isNaN( value ) && value !== 0 ) {
					return value;
				}
			}
			elem = elem.parent();
		}

		return 0;
	}

	/*
	 * Bind hover events for datepicker elements.
	 * Done via delegate so the binding only occurs once in the lifetime of the parent div.
	 * Global datepicker_instActive, set by _updateDatepicker allows the handlers to find their way back to the active picker.
	 */
	function datepicker_handleMouseover( that ) {
		if ( ! $.tm_datepicker._isDisabledDatepicker( datepicker_instActive.inline ? datepicker_instActive.dpDiv.parent()[ 0 ] : datepicker_instActive.input[ 0 ] ) ) {
			$( that ).parents( '.ui-tm-datepicker-calendar' ).find( 'a' ).removeClass( 'ui-state-hover' );
			$( that ).addClass( 'ui-state-hover' );
			if ( that.className.indexOf( 'ui-tm-datepicker-prev' ) !== -1 ) {
				$( that ).addClass( 'ui-tm-datepicker-prev-hover' );
			}
			if ( that.className.indexOf( 'ui-tm-datepicker-next' ) !== -1 ) {
				$( that ).addClass( 'ui-tm-datepicker-next-hover' );
			}
		}
	}

	function datepicker_bindHover( dpDiv ) {
		var selector = 'button, .ui-tm-datepicker-prev, .ui-tm-datepicker-next, .ui-tm-datepicker-calendar td a';
		return dpDiv
			.on( 'mouseout', selector, function() {
				$( this ).removeClass( 'ui-state-hover' );
				if ( this.className.indexOf( 'ui-tm-datepicker-prev' ) !== -1 ) {
					$( this ).removeClass( 'ui-tm-datepicker-prev-hover' );
				}
				if ( this.className.indexOf( 'ui-tm-datepicker-next' ) !== -1 ) {
					$( this ).removeClass( 'ui-tm-datepicker-next-hover' );
				}
			} )
			.on( 'mouseover', selector, function() {
				datepicker_handleMouseover( this );
			} );
	}

	/* jQuery extend now ignores nulls! */
	function datepicker_extendRemove( target, props ) {
		var name;

		$.extend( target, props );
		for ( name in props ) {
			if ( props[ name ] === null ) {
				target[ name ] = props[ name ];
			}
		}
		return target;
	}

	/* Date picker manager.
     Use the singleton instance of this class, $.tm_datepicker, to interact with the date picker.
     Settings for (groups of) date pickers are maintained in an instance object,
     allowing multiple different settings on the same page. */

	function Datepicker() {
		this._curInst = null; // The current instance in use
		this._keyEvent = false; // If the last event was a key event
		this._disabledInputs = []; // List of date picker inputs that have been disabled
		this._datepickerShowing = false; // True if the popup picker is showing , false if not
		this._inDialog = false; // True if showing within a "dialog", false if not
		this._mainDivId = 'ui-tm-datepicker-div'; // The ID of the main datepicker division
		this._inlineClass = 'ui-tm-datepicker-inline'; // The name of the inline marker class
		this._appendClass = 'ui-tm-datepicker-append'; // The name of the append marker class
		this._triggerClass = 'ui-tm-datepicker-trigger'; // The name of the trigger marker class
		this._dialogClass = 'ui-tm-datepicker-dialog'; // The name of the dialog marker class
		this._disableClass = 'ui-tm-datepicker-disabled'; // The name of the disabled covering marker class
		this._unselectableClass = 'ui-tm-datepicker-unselectable'; // The name of the unselectable cell marker class
		this._currentClass = 'ui-tm-datepicker-current-day'; // The name of the current day marker class
		this._dayOverClass = 'ui-tm-datepicker-days-cell-over'; // The name of the day hover marker class
		this.regional = []; // Available regional settings, indexed by language code
		this.regional[ '' ] = {
			// Default regional settings
			closeText: 'Done', // Display text for close link
			prevText: 'Prev', // Display text for previous month link
			nextText: 'Next', // Display text for next month link
			currentText: 'Today', // Display text for current month link
			monthNames: [ 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December' ], // Names of months for drop-down and formatting
			monthNamesShort: [ 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec' ], // For formatting
			dayNames: [ 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday' ], // For formatting
			dayNamesShort: [ 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat' ], // For formatting
			dayNamesMin: [ 'Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa' ], // Column headings for days starting at Sunday
			weekHeader: 'Wk', // Column header for week of the year
			dateFormat: 'mm/dd/yy', // See format options on parseDate
			firstDay: 0, // The first day of the week, Sun = 0, Mon = 1, ...
			isRTL: false, // True if right-to-left language, false if left-to-right
			showMonthAfterYear: false, // True if the year select precedes month, false for month then year
			yearSuffix: '' // Additional text to append to the year in the month headers
		};
		this._defaults = {
			// Global defaults for all the date picker instances
			showOn: 'focus', // "focus" for popup on focus,
			// "button" for trigger button, or "both" for either
			showAnim: 'fadeIn', // Name of jQuery animation for popup
			showOptions: {}, // Options for enhanced animations
			defaultDate: null, // Used when field is blank: actual date,
			// +/-number for offset from today, null for today
			appendText: '', // Display text following the input box, e.g. showing the format
			buttonText: '...', // Text for trigger button
			buttonImage: '', // URL for trigger button image
			buttonImageOnly: false, // True if the image appears alone, false if it appears on a button
			hideIfNoPrevNext: false, // True to hide next/previous month links
			// if not applicable, false to just disable them
			navigationAsDateFormat: false, // True if date formatting applied to prev/today/next links
			gotoCurrent: false, // True if today link goes back to current selection instead
			changeMonth: false, // True if month can be selected directly, false if only prev/next
			changeYear: false, // True if year can be selected directly, false if only prev/next
			yearRange: 'c-10:c+10', // Range of years to display in drop-down,
			// either relative to today's year (-nn:+nn), relative to currently displayed year
			// (c-nn:c+nn), absolute (nnnn:nnnn), or a combination of the above (nnnn:-n)
			showOtherMonths: false, // True to show dates in other months, false to leave blank
			selectOtherMonths: false, // True to allow selection of dates in other months, false for unselectable
			showWeek: false, // True to show week of the year, false to not show it
			calculateWeek: this.iso8601Week, // How to calculate the week of the year,
			// takes a Date and returns the number of the week for it
			shortYearCutoff: '+10', // Short year values < this are in the current century,
			// > this are in the previous century,
			// string value starting with "+" for current year + value
			minDate: null, // The earliest selectable date, or null for no limit
			maxDate: null, // The latest selectable date, or null for no limit
			duration: 'fast', // Duration of display/closure
			beforeShowDay: null, // Function that takes a date and returns an array with
			// [0] = true if selectable, false if not, [1] = custom CSS class name(s) or "",
			// [2] = cell title (optional), e.g. $.tm_datepicker.noWeekends
			beforeShow: null, // Function that takes an input field and
			// returns a set of custom settings for the date picker
			onSelect: null, // Define a callback function when a date is selected
			onChangeMonthYear: null, // Define a callback function when the month or year is changed
			onClose: null, // Define a callback function when the datepicker is closed
			numberOfMonths: 1, // Number of months to show at a time
			showCurrentAtPos: 0, // The position in multipe months at which to show the current month (starting at 0)
			stepMonths: 1, // Number of months to step back/forward
			stepBigMonths: 12, // Number of months to step back/forward for the big links
			altField: '', // Selector for an alternate field to store selected dates into
			altFormat: '', // The date format to use for the alternate field
			constrainInput: true, // The input is constrained by the current date format
			showButtonPanel: false, // True to show button panel, false to not show it
			autoSize: false, // True to size the input for the date format, false to leave as is
			disabled: false // The initial disabled state
		};
		$.extend( this._defaults, this.regional[ '' ] );
		this.regional.en = $.extend( true, {}, this.regional[ '' ] );
		this.regional[ 'en-US' ] = $.extend( true, {}, this.regional.en );
		this.dpDiv = datepicker_bindHover( $( "<div id='" + this._mainDivId + "' class='ui-tm-datepicker ui-widget ui-widget-content ui-helper-clearfix ui-corner-all'></div>" ) );
	}

	$.extend( Datepicker.prototype, {
		/* Class name added to elements to indicate already configured with a date picker. */
		markerClassName: 'hasDatepicker',

		//Keep track of the maximum number of rows displayed (see #7043)
		maxRows: 4,

		// TODO rename to "widget" when switching to widget factory
		_widgetDatepicker: function() {
			return this.dpDiv;
		},

		/* Override the default settings for all instances of the date picker.
		 * @param  settings  object - the new settings to use as defaults (anonymous object)
		 * @return the manager object
		 */
		setDefaults: function( settings ) {
			datepicker_extendRemove( this._defaults, settings || {} );
			return this;
		},

		/* Attach the date picker to a jQuery selection.
		 * @param  target   element - the target input field or division or span
		 * @param  settings  object - the new settings to use for this date picker instance (anonymous)
		 */
		_attachDatepicker: function( target, settings ) {
			var nodeName, inline, inst;
			nodeName = target.nodeName.toLowerCase();
			inline = nodeName === 'div' || nodeName === 'span';
			if ( ! target.id ) {
				this.uuid += 1;
				target.id = 'dp' + this.uuid;
			}
			inst = this._newInst( $( target ), inline );
			inst.settings = $.extend( {}, settings || {} );
			if ( nodeName === 'input' ) {
				this._connectDatepicker( target, inst );
			} else if ( inline ) {
				this._inlineDatepicker( target, inst );
			}
		},

		/* Create a new instance object. */
		_newInst: function( target, inline ) {
			var id = target[ 0 ].id.replace( /([^A-Za-z0-9_-])/g, '\\\\$1' ); // escape jQuery meta chars
			return {
				id: id,
				input: target, // associated target
				selectedDay: 0,
				selectedMonth: 0,
				selectedYear: 0, // current selection
				drawMonth: 0,
				drawYear: 0, // month being drawn
				inline: inline, // is datepicker inline or not
				// presentation div
				dpDiv: ! inline ? this.dpDiv : datepicker_bindHover( $( "<div class='" + this._inlineClass + " ui-tm-datepicker ui-widget ui-widget-content ui-helper-clearfix ui-corner-all'></div>" ) )
			};
		},

		/* Attach the date picker to an input field. */
		_connectDatepicker: function( target, inst ) {
			var input = $( target );
			inst.append = $( [] );
			inst.trigger = $( [] );
			if ( input.hasClass( this.markerClassName ) ) {
				return;
			}
			this._attachments( input, inst );
			input.addClass( this.markerClassName ).on( 'keydown', this._doKeyDown ).on( 'keypress', this._doKeyPress ).on( 'keyup', this._doKeyUp );
			this._autoSize( inst );
			$.data( target, 'datepicker', inst );

			//If disabled option is true, disable the datepicker once it has been attached to the input (see ticket #5665)
			if ( inst.settings.disabled ) {
				this._disableDatepicker( target );
			}
		},

		/* Make attachments based on settings. */
		_attachments: function( input, inst ) {
			var showOn,
				buttonText,
				buttonImage,
				appendText = this._get( inst, 'appendText' ),
				isRTL = this._get( inst, 'isRTL' );

			if ( inst.append ) {
				inst.append.remove();
			}
			if ( appendText ) {
				inst.append = $( "<span class='" + this._appendClass + "'>" + appendText + '</span>' );
				input[ isRTL ? 'before' : 'after' ]( inst.append );
			}

			input.off( 'focus', this._showDatepicker );

			if ( inst.trigger ) {
				inst.trigger.remove();
			}

			showOn = this._get( inst, 'showOn' );
			if ( showOn === 'focus' || showOn === 'both' ) {
				// pop-up date picker when in the marked field
				input.on( 'focus', this._showDatepicker );
			}
			if ( showOn === 'button' || showOn === 'both' ) {
				// pop-up date picker when button clicked
				buttonText = this._get( inst, 'buttonText' );
				buttonImage = this._get( inst, 'buttonImage' );
				inst.trigger = $(
					this._get( inst, 'buttonImageOnly' )
						? $( '<img/>' ).addClass( this._triggerClass ).attr( { src: buttonImage, alt: buttonText, title: buttonText } )
						: $( "<button type='button'></button>" )
							.addClass( this._triggerClass )
							.html( ! buttonImage ? buttonText : $( '<img/>' ).attr( { src: buttonImage, alt: buttonText, title: buttonText } ) )
				);
				input[ isRTL ? 'before' : 'after' ]( inst.trigger );
				inst.trigger.on( 'click', function() {
					if ( $.tm_datepicker._datepickerShowing && $.tm_datepicker._lastInput === input[ 0 ] ) {
						$.tm_datepicker._hideDatepicker();
					} else if ( $.tm_datepicker._datepickerShowing && $.tm_datepicker._lastInput !== input[ 0 ] ) {
						$.tm_datepicker._hideDatepicker();
						$.tm_datepicker._showDatepicker( input[ 0 ] );
					} else {
						$.tm_datepicker._showDatepicker( input[ 0 ] );
					}
					return false;
				} );
			}
		},

		/* Apply the maximum length for the date format. */
		_autoSize: function( inst ) {
			var findMax;
			var max;
			var maxI;
			var i;
			var date;
			var dateFormat = this._get( inst, 'dateFormat' );

			if ( this._get( inst, 'autoSize' ) && ! inst.inline ) {
				date = new Date( 2009, 12 - 1, 20 ); // Ensure double digits
				dateFormat = this._get( inst, 'dateFormat' );

				if ( dateFormat.match( /[DM]/ ) ) {
					findMax = function( names ) {
						max = 0;
						maxI = 0;
						for ( i = 0; i < names.length; i += 1 ) {
							if ( names[ i ].length > max ) {
								max = names[ i ].length;
								maxI = i;
							}
						}
						return maxI;
					};
					date.setMonth( findMax( this._get( inst, dateFormat.match( /MM/ ) ? 'monthNames' : 'monthNamesShort' ) ) );
					date.setDate( findMax( this._get( inst, dateFormat.match( /DD/ ) ? 'dayNames' : 'dayNamesShort' ) ) + 20 - date.getDay() );
				}
				inst.input.attr( 'size', this._formatDate( inst, date ).length );
			}
		},

		/* Attach an inline date picker to a div. */
		_inlineDatepicker: function( target, inst ) {
			var divSpan = $( target );
			if ( divSpan.hasClass( this.markerClassName ) ) {
				return;
			}
			divSpan.addClass( this.markerClassName ).append( inst.dpDiv );
			$.data( target, 'datepicker', inst );
			this._setDate( inst, this._getDefaultDate( inst ), true );
			this._updateDatepicker( inst );
			this._updateAlternate( inst );

			//If disabled option is true, disable the datepicker before showing it (see ticket #5665)
			if ( inst.settings.disabled ) {
				this._disableDatepicker( target );
			}

			// Set display:block in place of inst.dpDiv.show() which won't work on disconnected elements
			// http://bugs.jqueryui.com/ticket/7552 - A Datepicker created on a detached div has zero height
			inst.dpDiv.css( 'display', 'block' );
		},

		/* Pop-up the date picker in a "dialog" box.
		 * @param  input element - ignored
		 * @param  date string or Date - the initial date to display
		 * @param  onSelect  function - the function to call when a date is selected
		 * @param  settings  object - update the dialog date picker instance's settings (anonymous object)
		 * @param  pos int[2] - coordinates for the dialog's position within the screen or
		 *                  event - with x/y coordinates or
		 *                  leave empty for default (screen centre)
		 * @return the manager object
		 */
		_dialogDatepicker: function( input, date, onSelect, settings, pos ) {
			var id,
				browserWidth,
				browserHeight,
				scrollX,
				scrollY,
				inst = this._dialogInst; // internal instance

			if ( ! inst ) {
				this.uuid += 1;
				id = 'dp' + this.uuid;
				this._dialogInput = $( "<input type='text' id='" + id + "' />" );
				this._dialogInput.on( 'keydown', this._doKeyDown );
				$( 'body' ).append( this._dialogInput );
				inst = this._newInst( this._dialogInput, false );
				this._dialogInst = inst;
				inst.settings = {};
				$.data( this._dialogInput[ 0 ], 'datepicker', inst );
			}
			datepicker_extendRemove( inst.settings, settings || {} );
			date = date && date.constructor === Date ? this._formatDate( inst, date ) : date;
			this._dialogInput.val( date );

			this._pos = pos ? ( pos.length ? pos : [ pos.pageX, pos.pageY ] ) : null;
			if ( ! this._pos ) {
				browserWidth = document.documentElement.clientWidth;
				browserHeight = document.documentElement.clientHeight;
				scrollX = document.documentElement.scrollLeft || document.body.scrollLeft;
				scrollY = document.documentElement.scrollTop || document.body.scrollTop;
				this._pos =
					// should use actual width/height below
					[ ( browserWidth / 2 ) - 100 + scrollX, ( browserHeight / 2 ) - 150 + scrollY ];
			}

			// Move input on screen for focus, but hidden behind dialog
			this._dialogInput.css( 'left', this._pos[ 0 ] + 20 + 'px' ).css( 'top', this._pos[ 1 ] + 'px' );
			inst.settings.onSelect = onSelect;
			this._inDialog = true;
			this.dpDiv.addClass( this._dialogClass );
			this._showDatepicker( this._dialogInput[ 0 ] );
			if ( $.blockUI ) {
				$.blockUI( this.dpDiv );
			}
			$.data( this._dialogInput[ 0 ], 'datepicker', inst );
			return this;
		},

		/* Detach a datepicker from its control.
		 * @param  target   element - the target input field or division or span
		 */
		_destroyDatepicker: function( target ) {
			var nodeName,
				$target = $( target ),
				inst = $.data( target, 'datepicker' );

			if ( ! $target.hasClass( this.markerClassName ) ) {
				return;
			}

			nodeName = target.nodeName.toLowerCase();
			$.removeData( target, 'datepicker' );
			if ( nodeName === 'input' ) {
				inst.append.remove();
				inst.trigger.remove();
				$target.removeClass( this.markerClassName ).off( 'focus', this._showDatepicker ).off( 'keydown', this._doKeyDown ).off( 'keypress', this._doKeyPress ).off( 'keyup', this._doKeyUp );
			} else if ( nodeName === 'div' || nodeName === 'span' ) {
				$target.removeClass( this.markerClassName ).empty();
			}

			if ( datepicker_instActive === inst ) {
				datepicker_instActive = null;
			}
		},

		/* Enable the date picker to a jQuery selection.
		 * @param  target   element - the target input field or division or span
		 */
		_enableDatepicker: function( target ) {
			var nodeName,
				inline,
				$target = $( target ),
				inst = $.data( target, 'datepicker' );

			if ( ! $target.hasClass( this.markerClassName ) ) {
				return;
			}

			nodeName = target.nodeName.toLowerCase();
			if ( nodeName === 'input' ) {
				target.disabled = false;
				inst.trigger
					.filter( 'button' )
					.each( function() {
						this.disabled = false;
					} )
					.end()
					.filter( 'img' )
					.css( { opacity: '1.0', cursor: '' } );
			} else if ( nodeName === 'div' || nodeName === 'span' ) {
				inline = $target.children( '.' + this._inlineClass );
				inline.children().removeClass( 'ui-state-disabled' );
				inline.find( 'select.ui-tm-datepicker-month, select.ui-tm-datepicker-year' ).prop( 'disabled', false );
			}
			this._disabledInputs = $.map( this._disabledInputs, function( value ) {
				return value === target ? null : value;
			} ); // delete entry
		},

		/* Disable the date picker to a jQuery selection.
		 * @param  target   element - the target input field or division or span
		 */
		_disableDatepicker: function( target ) {
			var nodeName,
				inline,
				$target = $( target ),
				inst = $.data( target, 'datepicker' );

			if ( ! $target.hasClass( this.markerClassName ) ) {
				return;
			}

			nodeName = target.nodeName.toLowerCase();
			if ( nodeName === 'input' ) {
				target.disabled = true;
				inst.trigger
					.filter( 'button' )
					.each( function() {
						this.disabled = true;
					} )
					.end()
					.filter( 'img' )
					.css( { opacity: '0.5', cursor: 'default' } );
			} else if ( nodeName === 'div' || nodeName === 'span' ) {
				inline = $target.children( '.' + this._inlineClass );
				inline.children().addClass( 'ui-state-disabled' );
				inline.find( 'select.ui-tm-datepicker-month, select.ui-tm-datepicker-year' ).prop( 'disabled', true );
			}
			this._disabledInputs = $.map( this._disabledInputs, function( value ) {
				return value === target ? null : value;
			} ); // delete entry
			this._disabledInputs[ this._disabledInputs.length ] = target;
		},

		/* Is the first field in a jQuery collection disabled as a datepicker?
		 * @param  target   element - the target input field or division or span
		 * @return boolean - true if disabled, false if enabled
		 */
		_isDisabledDatepicker: function( target ) {
			var i;
			if ( ! target ) {
				return false;
			}
			for ( i = 0; i < this._disabledInputs.length; i += 1 ) {
				if ( this._disabledInputs[ i ] === target ) {
					return true;
				}
			}
			return false;
		},

		/* Retrieve the instance data for the target control.
		 * @param  target  element - the target input field or division or span
		 * @return  object - the associated instance data
		 * @throws  error if a jQuery problem getting data
		 */
		_getInst: function( target ) {
			try {
				return $.data( target, 'datepicker' );
			} catch ( err ) {
				throw 'Missing instance data for this datepicker';
			}
		},

		/* Update or retrieve the settings for a date picker attached to an input field or division.
		 * @param  target  element - the target input field or division or span
		 * @param  name object - the new settings to update or
		 *              string - the name of the setting to change or retrieve,
		 *              when retrieving also "all" for all instance settings or
		 *              "defaults" for all global defaults
		 * @param  value   any - the new value for the setting
		 *              (omit if above is an object or to retrieve a value)
		 */
		_optionDatepicker: function( target, name, value ) {
			var settings,
				date,
				minDate,
				maxDate,
				inst = this._getInst( target );

			if ( arguments.length === 2 && typeof name === 'string' ) {
				return name === 'defaults' ? $.extend( {}, $.tm_datepicker._defaults ) : inst ? ( name === 'all' ? $.extend( {}, inst.settings ) : this._get( inst, name ) ) : null;
			}

			settings = name || {};
			if ( typeof name === 'string' ) {
				settings = {};
				settings[ name ] = value;
			}

			if ( inst ) {
				if ( this._curInst === inst ) {
					this._hideDatepicker();
				}

				date = this._getDateDatepicker( target, true );
				minDate = this._getMinMaxDate( inst, 'min' );
				maxDate = this._getMinMaxDate( inst, 'max' );
				datepicker_extendRemove( inst.settings, settings );

				// reformat the old minDate/maxDate values if dateFormat changes and a new minDate/maxDate isn't provided
				if ( minDate !== null && settings.dateFormat !== undefined && settings.minDate === undefined ) {
					inst.settings.minDate = this._formatDate( inst, minDate );
				}
				if ( maxDate !== null && settings.dateFormat !== undefined && settings.maxDate === undefined ) {
					inst.settings.maxDate = this._formatDate( inst, maxDate );
				}
				if ( 'disabled' in settings ) {
					if ( settings.disabled ) {
						this._disableDatepicker( target );
					} else {
						this._enableDatepicker( target );
					}
				}
				this._attachments( $( target ), inst );
				this._autoSize( inst );
				this._setDate( inst, date );
				this._updateAlternate( inst );
				this._updateDatepicker( inst );
			}
		},

		// Change method deprecated
		_changeDatepicker: function( target, name, value ) {
			this._optionDatepicker( target, name, value );
		},

		/* Redraw the date picker attached to an input field or division.
		 * @param  target  element - the target input field or division or span
		 */
		_refreshDatepicker: function( target ) {
			var inst = this._getInst( target );
			if ( inst ) {
				this._updateDatepicker( inst );
			}
		},

		/* Set the dates for a jQuery selection.
		 * @param  target element - the target input field or division or span
		 * @param  date Date - the new date
		 */
		_setDateDatepicker: function( target, date ) {
			var inst = this._getInst( target );
			if ( inst ) {
				this._setDate( inst, date );
				this._updateDatepicker( inst );
				this._updateAlternate( inst );
			}
		},

		/* Get the date(s) for the first entry in a jQuery selection.
		 * @param  target element - the target input field or division or span
		 * @param  noDefault boolean - true if no default date is to be used
		 * @return Date - the current date
		 */
		_getDateDatepicker: function( target, noDefault ) {
			var inst = this._getInst( target );
			if ( inst && ! inst.inline ) {
				this._setDateFromField( inst, noDefault );
			}
			return inst ? this._getDate( inst ) : null;
		},

		/* Handle keystrokes. */
		_doKeyDown: function( event ) {
			var onSelect,
				dateStr,
				sel,
				inst = $.tm_datepicker._getInst( event.target ),
				handled = true,
				isRTL = inst.dpDiv.is( '.ui-tm-datepicker-rtl' );

			inst._keyEvent = true;
			if ( $.tm_datepicker._datepickerShowing ) {
				switch ( event.keyCode ) {
					case 9:
						$.tm_datepicker._hideDatepicker();
						handled = false;
						break; // hide on tab out
					case 13:
						sel = $( 'td.' + $.tm_datepicker._dayOverClass + ':not(.' + $.tm_datepicker._currentClass + ')', inst.dpDiv );
						if ( sel[ 0 ] ) {
							$.tm_datepicker._selectDay( event.target, inst.selectedMonth, inst.selectedYear, sel[ 0 ] );
						}

						onSelect = $.tm_datepicker._get( inst, 'onSelect' );
						if ( onSelect ) {
							dateStr = $.tm_datepicker._formatDate( inst );

							// Trigger custom callback
							onSelect.apply( inst.input ? inst.input[ 0 ] : null, [ dateStr, inst ] );
						} else {
							$.tm_datepicker._hideDatepicker();
						}

						return false; // don't submit the form
					case 27:
						$.tm_datepicker._hideDatepicker();
						break; // hide on escape
					case 33:
						$.tm_datepicker._adjustDate( event.target, event.ctrlKey ? -$.tm_datepicker._get( inst, 'stepBigMonths' ) : -$.tm_datepicker._get( inst, 'stepMonths' ), 'M' );
						break; // previous month/year on page up/+ ctrl
					case 34:
						$.tm_datepicker._adjustDate( event.target, event.ctrlKey ? +$.tm_datepicker._get( inst, 'stepBigMonths' ) : +$.tm_datepicker._get( inst, 'stepMonths' ), 'M' );
						break; // next month/year on page down/+ ctrl
					case 35:
						if ( event.ctrlKey || event.metaKey ) {
							$.tm_datepicker._clearDate( event.target );
						}
						handled = event.ctrlKey || event.metaKey;
						break; // clear on ctrl or command +end
					case 36:
						if ( event.ctrlKey || event.metaKey ) {
							$.tm_datepicker._gotoToday( event.target );
						}
						handled = event.ctrlKey || event.metaKey;
						break; // current on ctrl or command +home
					case 37:
						if ( event.ctrlKey || event.metaKey ) {
							$.tm_datepicker._adjustDate( event.target, isRTL ? +1 : -1, 'D' );
						}
						handled = event.ctrlKey || event.metaKey;
						// -1 day on ctrl or command +left
						if ( event.originalEvent.altKey ) {
							$.tm_datepicker._adjustDate( event.target, event.ctrlKey ? -$.tm_datepicker._get( inst, 'stepBigMonths' ) : -$.tm_datepicker._get( inst, 'stepMonths' ), 'M' );
						}
						// next month/year on alt +left on Mac
						break;
					case 38:
						if ( event.ctrlKey || event.metaKey ) {
							$.tm_datepicker._adjustDate( event.target, -7, 'D' );
						}
						handled = event.ctrlKey || event.metaKey;
						break; // -1 week on ctrl or command +up
					case 39:
						if ( event.ctrlKey || event.metaKey ) {
							$.tm_datepicker._adjustDate( event.target, isRTL ? -1 : +1, 'D' );
						}
						handled = event.ctrlKey || event.metaKey;
						// +1 day on ctrl or command +right
						if ( event.originalEvent.altKey ) {
							$.tm_datepicker._adjustDate( event.target, event.ctrlKey ? +$.tm_datepicker._get( inst, 'stepBigMonths' ) : +$.tm_datepicker._get( inst, 'stepMonths' ), 'M' );
						}
						// next month/year on alt +right
						break;
					case 40:
						if ( event.ctrlKey || event.metaKey ) {
							$.tm_datepicker._adjustDate( event.target, +7, 'D' );
						}
						handled = event.ctrlKey || event.metaKey;
						break; // +1 week on ctrl or command +down
					default:
						handled = false;
				}
			} else if ( event.keyCode === 36 && event.ctrlKey ) {
				// display the date picker on ctrl+home
				$.tm_datepicker._showDatepicker( this );
			} else {
				handled = false;
			}

			if ( handled ) {
				event.preventDefault();
				event.stopPropagation();
			}
		},

		/* Filter entered characters - based on date format. */
		_doKeyPress: function( event ) {
			var chars,
				chr,
				inst = $.tm_datepicker._getInst( event.target );

			if ( $.tm_datepicker._get( inst, 'constrainInput' ) ) {
				chars = $.tm_datepicker._possibleChars( $.tm_datepicker._get( inst, 'dateFormat' ) );
				chr = String.fromCharCode( event.charCode === null ? event.keyCode : event.charCode );
				return event.ctrlKey || event.metaKey || chr < ' ' || ! chars || chars.indexOf( chr ) > -1;
			}
		},

		/* Synchronise manual entry and field/alternate field. */
		_doKeyUp: function( event ) {
			var date,
				inst = $.tm_datepicker._getInst( event.target );

			if ( inst.input.val() !== inst.lastVal ) {
				try {
					date = $.tm_datepicker.parseDate( $.tm_datepicker._get( inst, 'dateFormat' ), inst.input ? inst.input.val() : null, $.tm_datepicker._getFormatConfig( inst ) );

					if ( date ) {
						// only if valid
						$.tm_datepicker._setDateFromField( inst );
						$.tm_datepicker._updateAlternate( inst );
						$.tm_datepicker._updateDatepicker( inst );
					}
				} catch ( err ) {
					return false;
				}
			}
			return true;
		},

		/* Pop-up the date picker for a given input field.
		 * If false returned from beforeShow event handler do not show.
		 * @param  input  element - the input field attached to the date picker or
		 *                  event - if triggered by focus
		 */
		_showDatepicker: function( input ) {
			var inst;
			var beforeShow;
			var beforeShowSettings;
			var isFixed;
			var offset;
			var showAnim;
			var duration;

			input = input.target || input;
			if ( input.nodeName.toLowerCase() !== 'input' ) {
				// find from button/image trigger
				input = $( 'input', input.parentNode )[ 0 ];
			}

			if ( $.tm_datepicker._isDisabledDatepicker( input ) || $.tm_datepicker._lastInput === input ) {
				// already here
				return;
			}

			inst = $.tm_datepicker._getInst( input );
			if ( $.tm_datepicker._curInst && $.tm_datepicker._curInst !== inst ) {
				$.tm_datepicker._curInst.dpDiv.stop( true, true );
				if ( inst && $.tm_datepicker._datepickerShowing ) {
					$.tm_datepicker._hideDatepicker( $.tm_datepicker._curInst.input[ 0 ] );
				}
			}

			beforeShow = $.tm_datepicker._get( inst, 'beforeShow' );
			beforeShowSettings = beforeShow ? beforeShow.apply( input, [ input, inst ] ) : {};
			if ( beforeShowSettings === false ) {
				return;
			}
			datepicker_extendRemove( inst.settings, beforeShowSettings );

			inst.lastVal = null;
			$.tm_datepicker._lastInput = input;
			$.tm_datepicker._setDateFromField( inst );

			if ( $.tm_datepicker._inDialog ) {
				// hide cursor
				input.value = '';
			}
			if ( ! $.tm_datepicker._pos ) {
				// position below input
				$.tm_datepicker._pos = $.tm_datepicker._findPos( input );
				$.tm_datepicker._pos[ 1 ] += input.offsetHeight; // add the height
			}

			isFixed = false;
			$( input )
				.parents()
				.each( function() {
					isFixed = isFixed || $( this ).css( 'position' ) === 'fixed';
					return ! isFixed;
				} );

			offset = { left: $.tm_datepicker._pos[ 0 ], top: $.tm_datepicker._pos[ 1 ] };
			$.tm_datepicker._pos = null;
			//to avoid flashes on Firefox
			inst.dpDiv.empty();
			// determine sizing offscreen
			inst.dpDiv.css( { position: 'absolute', display: 'block', top: '-1000px' } );
			$.tm_datepicker._updateDatepicker( inst );
			// fix width for dynamic number of date pickers
			// and adjust position before showing
			offset = $.tm_datepicker._checkOffset( inst, offset, isFixed );
			inst.dpDiv.css( {
				position: $.tm_datepicker._inDialog && $.blockUI ? 'static' : isFixed ? 'fixed' : 'absolute',
				display: 'none',
				left: offset.left + 'px',
				top: offset.top + 'px'
			} );

			if ( ! inst.inline ) {
				showAnim = $.tm_datepicker._get( inst, 'showAnim' );
				duration = $.tm_datepicker._get( inst, 'duration' );
				inst.dpDiv.css( 'z-index', datepicker_getZindex( $( input ) ) + 1 );
				$.tm_datepicker._datepickerShowing = true;

				if ( $.effects && $.effects.effect[ showAnim ] ) {
					inst.dpDiv.show( showAnim, $.tm_datepicker._get( inst, 'showOptions' ), duration );
				} else {
					inst.dpDiv[ showAnim || 'show' ]( showAnim ? duration : null );
				}

				if ( $.tm_datepicker._shouldFocusInput( inst ) ) {
					inst.input.trigger( 'focus' );
				}

				$.tm_datepicker._curInst = inst;
			}
		},

		/* Generate the date picker content. */
		_updateDatepicker: function( inst ) {
			var origyearshtml;
			var numMonths;
			var cols;
			var width = 17;
			var activeCell;

			this.maxRows = 4; //Reset the max number of rows being displayed (see #7043)
			datepicker_instActive = inst; // for delegate hover events
			inst.dpDiv.empty().append( this._generateHTML( inst ) );
			this._attachHandlers( inst );

			numMonths = this._getNumberOfMonths( inst );
			cols = numMonths[ 1 ];
			activeCell = inst.dpDiv.find( '.' + this._dayOverClass + ' a' );

			if ( activeCell.length > 0 ) {
				datepicker_handleMouseover.apply( activeCell.get( 0 ), [ activeCell.get( 0 ) ] );
			}

			inst.dpDiv.removeClass( 'ui-tm-datepicker-multi-2 ui-tm-datepicker-multi-3 ui-tm-datepicker-multi-4' ).width( '' );
			if ( cols > 1 ) {
				inst.dpDiv.addClass( 'ui-tm-datepicker-multi-' + cols ).css( 'width', ( width * cols ) + 'em' );
			}
			inst.dpDiv[ ( numMonths[ 0 ] !== 1 || numMonths[ 1 ] !== 1 ? 'add' : 'remove' ) + 'Class' ]( 'ui-tm-datepicker-multi' );
			inst.dpDiv[ ( this._get( inst, 'isRTL' ) ? 'add' : 'remove' ) + 'Class' ]( 'ui-tm-datepicker-rtl' );

			if ( inst === $.tm_datepicker._curInst && $.tm_datepicker._datepickerShowing && $.tm_datepicker._shouldFocusInput( inst ) ) {
				inst.input.trigger( 'focus' );
			}

			// deffered render of the years select (to avoid flashes on Firefox)
			if ( inst.yearshtml ) {
				origyearshtml = inst.yearshtml;
				setTimeout( function() {
					//assure that inst.yearshtml didn't change.
					if ( origyearshtml === inst.yearshtml && inst.yearshtml ) {
						inst.dpDiv.find( 'select.ui-tm-datepicker-year:first' ).replaceWith( inst.yearshtml );
					}
					origyearshtml = null;
					inst.yearshtml = null;
				}, 0 );
			}
		},

		// #6694 - don't focus the input if it's already focused
		// this breaks the change event in IE
		// Support: IE and jQuery <1.9
		_shouldFocusInput: function( inst ) {
			return inst.input && inst.input.is( ':visible' ) && ! inst.input.is( ':disabled' ) && ! inst.input.is( ':focus' );
		},

		/* Check positioning to remain on screen. */
		_checkOffset: function( inst, offset, isFixed ) {
			var dpWidth = inst.dpDiv.outerWidth(),
				dpHeight = inst.dpDiv.outerHeight(),
				inputWidth = inst.input ? inst.input.outerWidth() : 0,
				inputHeight = inst.input ? inst.input.outerHeight() : 0,
				viewWidth = document.documentElement.clientWidth + ( isFixed ? 0 : $( document ).scrollLeft() ),
				viewHeight = document.documentElement.clientHeight + ( isFixed ? 0 : $( document ).scrollTop() );

			offset.left -= this._get( inst, 'isRTL' ) ? dpWidth - inputWidth : 0;
			offset.left -= isFixed && offset.left === inst.input.offset().left ? $( document ).scrollLeft() : 0;
			offset.top -= isFixed && offset.top === inst.input.offset().top + inputHeight ? $( document ).scrollTop() : 0;

			// Now check if datepicker is showing outside window viewport - move to a better place if so.
			offset.left -= Math.min( offset.left, offset.left + dpWidth > viewWidth && viewWidth > dpWidth ? Math.abs( offset.left + dpWidth - viewWidth ) : 0 );
			offset.top -= Math.min( offset.top, offset.top + dpHeight > viewHeight && viewHeight > dpHeight ? Math.abs( dpHeight + inputHeight ) : 0 );

			return offset;
		},

		/* Find an object's position on the screen. */
		_findPos: function( obj ) {
			var position,
				inst = this._getInst( obj ),
				isRTL = this._get( inst, 'isRTL' );

			while ( obj && ( obj.type === 'hidden' || obj.nodeType !== 1 || $.expr.filters.hidden( obj ) ) ) {
				obj = obj[ isRTL ? 'previousSibling' : 'nextSibling' ];
			}

			position = $( obj ).offset();
			return [ position.left, position.top ];
		},

		/* Hide the date picker from view.
		 * @param  input  element - the input field attached to the date picker
		 */
		_hideDatepicker: function( input ) {
			var showAnim,
				duration,
				postProcess,
				onClose,
				inst = this._curInst;

			if ( ! inst || ( input && inst !== $.data( input, 'datepicker' ) ) ) {
				return;
			}

			if ( this._datepickerShowing ) {
				showAnim = this._get( inst, 'showAnim' );
				duration = this._get( inst, 'duration' );
				postProcess = function() {
					$.tm_datepicker._tidyDialog( inst );
				};

				// DEPRECATED: after BC for 1.8.x $.effects[ showAnim ] is not needed
				if ( $.effects && ( $.effects.effect[ showAnim ] || $.effects[ showAnim ] ) ) {
					inst.dpDiv.hide( showAnim, $.tm_datepicker._get( inst, 'showOptions' ), duration, postProcess );
				} else {
					inst.dpDiv[ showAnim === 'slideDown' ? 'slideUp' : showAnim === 'fadeIn' ? 'fadeOut' : 'hide' ]( showAnim ? duration : null, postProcess );
				}

				if ( ! showAnim ) {
					postProcess();
				}
				this._datepickerShowing = false;

				onClose = this._get( inst, 'onClose' );
				if ( onClose ) {
					onClose.apply( inst.input ? inst.input[ 0 ] : null, [ inst.input ? inst.input.val() : '', inst ] );
				}

				this._lastInput = null;
				if ( this._inDialog ) {
					this._dialogInput.css( { position: 'absolute', left: '0', top: '-100px' } );
					if ( $.blockUI ) {
						$.unblockUI();
						$( 'body' ).append( this.dpDiv );
					}
				}
				this._inDialog = false;
			}
		},

		/* Tidy up after a dialog display. */
		_tidyDialog: function( inst ) {
			inst.dpDiv.removeClass( this._dialogClass ).off( '.ui-tm-datepicker-calendar' );
		},

		/* Close date picker if clicked elsewhere. */
		_checkExternalClick: function( event ) {
			var $target;
			var inst;

			if ( ! $.tm_datepicker._curInst ) {
				return;
			}

			$target = $( event.target );
			inst = $.tm_datepicker._getInst( $target[ 0 ] );

			if ( ( $target[ 0 ].id !== $.tm_datepicker._mainDivId && $target.parents( '#' + $.tm_datepicker._mainDivId ).length === 0 && ! $target.hasClass( $.tm_datepicker.markerClassName ) && ! $target.closest( '.' + $.tm_datepicker._triggerClass ).length && $.tm_datepicker._datepickerShowing && ! ( $.tm_datepicker._inDialog && $.blockUI ) ) || ( $target.hasClass( $.tm_datepicker.markerClassName ) && $.tm_datepicker._curInst !== inst ) ) {
				$.tm_datepicker._hideDatepicker();
			}
		},

		/* Adjust one of the date sub-fields. */
		_adjustDate: function( id, offset, period ) {
			var target = $( id ),
				inst = this._getInst( target[ 0 ] );

			if ( this._isDisabledDatepicker( target[ 0 ] ) ) {
				return;
			}
			this._adjustInstDate(
				inst,
				offset + ( period === 'M' ? this._get( inst, 'showCurrentAtPos' ) : 0 ), // undo positioning
				period
			);
			this._updateDatepicker( inst );
		},

		/* Action for current link. */
		_gotoToday: function( id ) {
			var date,
				target = $( id ),
				inst = this._getInst( target[ 0 ] );

			if ( this._get( inst, 'gotoCurrent' ) && inst.currentDay ) {
				inst.selectedDay = inst.currentDay;
				inst.drawMonth = inst.currentMonth;
				inst.selectedMonth = inst.currentMonth;
				inst.drawYear = inst.currentYear;
				inst.selectedYear = inst.currentYear;
			} else {
				date = new Date();
				inst.selectedDay = date.getDate();
				inst.drawMonth = date.getMonth();
				inst.selectedMonth = inst.drawMonth;
				inst.drawYear = date.getFullYear();
				inst.selectedYear = inst.drawYear;
			}
			this._notifyChange( inst );
			this._adjustDate( target );
		},

		/* Action for selecting a new month/year. */
		_selectMonthYear: function( id, select, period ) {
			var target = $( id ),
				inst = this._getInst( target[ 0 ] );

			inst[ 'selected' + ( period === 'M' ? 'Month' : 'Year' ) ] = parseInt( select.options[ select.selectedIndex ].value, 10 );
			inst[ 'draw' + ( period === 'M' ? 'Month' : 'Year' ) ] = parseInt( select.options[ select.selectedIndex ].value, 10 );

			this._notifyChange( inst );
			this._adjustDate( target );
		},

		/* Action for selecting a day. */
		_selectDay: function( id, month, year, td ) {
			var inst,
				target = $( id );

			if ( $( td ).hasClass( this._unselectableClass ) || this._isDisabledDatepicker( target[ 0 ] ) ) {
				return;
			}

			inst = this._getInst( target[ 0 ] );
			inst.selectedDay = $( 'a', td ).html();
			inst.currentDay = inst.selectedDay;
			inst.selectedMonth = month;
			inst.currentMonth = month;
			inst.selectedYear = year;
			inst.currentYear = year;
			this._selectDate( id, this._formatDate( inst, inst.currentDay, inst.currentMonth, inst.currentYear ) );
		},

		/* Erase the input field and hide the date picker. */
		_clearDate: function( id ) {
			var target = $( id );
			this._selectDate( target, '' );
		},

		/* Update the input field with the selected date. */
		_selectDate: function( id, dateStr ) {
			var onSelect,
				target = $( id ),
				inst = this._getInst( target[ 0 ] );

			dateStr = dateStr !== null ? dateStr : this._formatDate( inst );
			if ( inst.input ) {
				inst.input.val( dateStr );
			}
			this._updateAlternate( inst );

			onSelect = this._get( inst, 'onSelect' );
			if ( onSelect ) {
				onSelect.apply( inst.input ? inst.input[ 0 ] : null, [ dateStr, inst ] ); // trigger custom callback
			} else if ( inst.input ) {
				inst.input.trigger( 'change' ); // fire the change event
			}

			if ( inst.inline ) {
				this._updateDatepicker( inst );
			} else {
				this._hideDatepicker();
				this._lastInput = inst.input[ 0 ];
				if ( typeof inst.input[ 0 ] !== 'object' ) {
					inst.input.trigger( 'focus' ); // restore focus
				}
				this._lastInput = null;
			}
		},

		/* Update any alternate field to synchronise with the main field. */
		_updateAlternate: function( inst ) {
			var altFormat,
				date,
				dateStr,
				altField = this._get( inst, 'altField' );

			if ( altField ) {
				// update alternate field too
				altFormat = this._get( inst, 'altFormat' ) || this._get( inst, 'dateFormat' );
				date = this._getDate( inst );
				dateStr = this.formatDate( altFormat, date, this._getFormatConfig( inst ) );
				$( altField ).val( dateStr );
			}
		},

		/* Set as beforeShowDay function to prevent selection of weekends.
		 * @param  date  Date - the date to customise
		 * @return [boolean, string] - is this date selectable?, what is its CSS class?
		 */
		noWeekends: function( date ) {
			var day = date.getDay();
			return [ day > 0 && day < 6, '' ];
		},

		/* Set as calculateWeek to determine the week of the year based on the ISO 8601 definition.
		 * @param  date  Date - the date to get the week for
		 * @return  number - the number of the week within the year that contains this date
		 */
		iso8601Week: function( date ) {
			var time,
				checkDate = new Date( date.getTime() );

			// Find Thursday of this week starting on Monday
			checkDate.setDate( checkDate.getDate() + 4 - ( checkDate.getDay() || 7 ) );

			time = checkDate.getTime();
			checkDate.setMonth( 0 ); // Compare with Jan 1
			checkDate.setDate( 1 );
			return Math.floor( Math.round( ( time - checkDate ) / 86400000 ) / 7 ) + 1;
		},

		/* Parse a string value into a date object.
		 * See formatDate below for the possible formats.
		 *
		 * @param  format string - the expected format of the date
		 * @param  value string - the date in the above format
		 * @param  settings Object - attributes include:
		 *                  shortYearCutoff  number - the cutoff year for determining the century (optional)
		 *                  dayNamesShort   string[7] - abbreviated names of the days from Sunday (optional)
		 *                  dayNames        string[7] - names of the days from Sunday (optional)
		 *                  monthNamesShort string[12] - abbreviated names of the months (optional)
		 *                  monthNames      string[12] - names of the months (optional)
		 * @return  Date - the extracted date value or null if value is blank
		 */
		parseDate: function( format, value, settings ) {
			var iFormat;
			var dim;
			var extra;
			var iValue = 0;
			var shortYearCutoffTemp;
			var shortYearCutoff;
			var dayNamesShort;
			var dayNames;
			var monthNamesShort;
			var monthNames;
			var year = -1;
			var month = -1;
			var day = -1;
			var doy = -1;
			var literal = false;
			var date;
			var lookAhead;
			var getNumber;
			var getName;
			var checkLiteral;
			var condition;

			if ( format === null || value === null ) {
				throw 'Invalid arguments';
			}

			value = typeof value === 'object' ? value.toString() : value + '';
			if ( value === '' ) {
				return null;
			}

			shortYearCutoffTemp = ( settings ? settings.shortYearCutoff : null ) || this._defaults.shortYearCutoff;
			shortYearCutoff = typeof shortYearCutoffTemp !== 'string' ? shortYearCutoffTemp : ( new Date().getFullYear() % 100 ) + parseInt( shortYearCutoffTemp, 10 );
			dayNamesShort = ( settings ? settings.dayNamesShort : null ) || this._defaults.dayNamesShort;
			dayNames = ( settings ? settings.dayNames : null ) || this._defaults.dayNames;
			monthNamesShort = ( settings ? settings.monthNamesShort : null ) || this._defaults.monthNamesShort;
			monthNames = ( settings ? settings.monthNames : null ) || this._defaults.monthNames;

			// Check whether a format character is doubled
			lookAhead = function( match ) {
				var matches = iFormat + 1 < format.length && format.charAt( iFormat + 1 ) === match;
				if ( matches ) {
					iFormat = iFormat + 1;
				}
				return matches;
			};

			// Extract a number from the string value
			getNumber = function( match ) {
				var isDoubled = lookAhead( match ),
					size = match === '@' ? 14 : match === '!' ? 20 : match === 'y' && isDoubled ? 4 : match === 'o' ? 3 : 2,
					minSize = match === 'y' ? size : 1,
					digits = new RegExp( '^\\d{' + minSize + ',' + size + '}' ),
					num = value.substring( iValue ).match( digits );
				if ( ! num ) {
					throw 'Missing number at position ' + iValue;
				}
				iValue += num[ 0 ].length;
				return parseInt( num[ 0 ], 10 );
			};

			// Extract a name from the string value and convert to an index
			getName = function( match, shortNames, longNames ) {
				var index = -1,
					names = $.map( lookAhead( match ) ? longNames : shortNames, function( v, k ) {
						return [ [ k, v ] ];
					} ).sort( function( a, b ) {
						return -( a[ 1 ].length - b[ 1 ].length );
					} );

				$.each( names, function( i, pair ) {
					var name = pair[ 1 ];
					if ( value.substr( iValue, name.length ).toLowerCase() === name.toLowerCase() ) {
						index = pair[ 0 ];
						iValue += name.length;
						return false;
					}
				} );
				if ( index !== -1 ) {
					return index + 1;
				}
				throw 'Unknown name at position ' + iValue;
			};

			// Confirm that a literal character matches the string value
			checkLiteral = function() {
				if ( value.charAt( iValue ) !== format.charAt( iFormat ) ) {
					throw 'Unexpected literal at position ' + iValue;
				}
				iValue = iValue + 1;
			};

			for ( iFormat = 0; iFormat < format.length; iFormat += 1 ) {
				if ( literal ) {
					if ( format.charAt( iFormat ) === "'" && ! lookAhead( "'" ) ) {
						literal = false;
					} else {
						checkLiteral();
					}
				} else {
					switch ( format.charAt( iFormat ) ) {
						case 'd':
							day = getNumber( 'd' );
							break;
						case 'D':
							getName( 'D', dayNamesShort, dayNames );
							break;
						case 'o':
							doy = getNumber( 'o' );
							break;
						case 'm':
							month = getNumber( 'm' );
							break;
						case 'M':
							month = getName( 'M', monthNamesShort, monthNames );
							break;
						case 'y':
							year = getNumber( 'y' );
							break;
						case '@':
							date = new Date( getNumber( '@' ) );
							year = date.getFullYear();
							month = date.getMonth() + 1;
							day = date.getDate();
							break;
						case '!':
							date = new Date( ( getNumber( '!' ) - this._ticksTo1970 ) / 10000 );
							year = date.getFullYear();
							month = date.getMonth() + 1;
							day = date.getDate();
							break;
						case "'":
							if ( lookAhead( "'" ) ) {
								checkLiteral();
							} else {
								literal = true;
							}
							break;
						default:
							checkLiteral();
					}
				}
			}

			if ( iValue < value.length ) {
				extra = value.substr( iValue );
				if ( ! /^\s+/.test( extra ) ) {
					throw 'Extra/unparsed characters found in date: ' + extra;
				}
			}

			if ( year === -1 ) {
				year = new Date().getFullYear();
			} else if ( year < 100 ) {
				year += new Date().getFullYear() - ( new Date().getFullYear() % 100 ) + ( year <= shortYearCutoff ? 0 : -100 );
			}

			if ( doy > -1 ) {
				month = 1;
				day = doy;
				condition = true;
				do {
					dim = this._getDaysInMonth( year, month - 1 );
					if ( day <= dim ) {
						condition = false;
					} else {
						month = month + 1;
						day -= dim;
					}
				} while ( condition );
			}

			date = this._daylightSavingAdjust( new Date( year, month - 1, day ) );
			if ( date.getFullYear() !== year || date.getMonth() + 1 !== month || date.getDate() !== day ) {
				throw 'Invalid date'; // E.g. 31/02/00
			}
			return date;
		},

		/* Standard date formats. */
		ATOM: 'yy-mm-dd', // RFC 3339 (ISO 8601)
		COOKIE: 'D, dd M yy',
		ISO_8601: 'yy-mm-dd',
		RFC_822: 'D, d M y',
		RFC_850: 'DD, dd-M-y',
		RFC_1036: 'D, d M y',
		RFC_1123: 'D, d M yy',
		RFC_2822: 'D, d M yy',
		RSS: 'D, d M y', // RFC 822
		TICKS: '!',
		TIMESTAMP: '@',
		W3C: 'yy-mm-dd', // ISO 8601

		_ticksTo1970: ( ( ( 1970 - 1 ) * 365 ) + Math.floor( 1970 / 4 ) - Math.floor( 1970 / 100 ) + Math.floor( 1970 / 400 ) ) * 24 * 60 * 60 * 10000000,

		/* Format a date object into a string value.
		 * The format can be combinations of the following:
		 * d  - day of month (no leading zero)
		 * dd - day of month (two digit)
		 * o  - day of year (no leading zeros)
		 * oo - day of year (three digit)
		 * D  - day name short
		 * DD - day name long
		 * m  - month of year (no leading zero)
		 * mm - month of year (two digit)
		 * M  - month name short
		 * MM - month name long
		 * y  - year (two digit)
		 * yy - year (four digit)
		 * @ - Unix timestamp (ms since 01/01/1970)
		 * ! - Windows ticks (100ns since 01/01/0001)
		 * "..." - literal text
		 * '' - single quote
		 *
		 * @param  format string - the desired format of the date
		 * @param  date Date - the date value to format
		 * @param  settings Object - attributes include:
		 *                  dayNamesShort   string[7] - abbreviated names of the days from Sunday (optional)
		 *                  dayNames        string[7] - names of the days from Sunday (optional)
		 *                  monthNamesShort string[12] - abbreviated names of the months (optional)
		 *                  monthNames      string[12] - names of the months (optional)
		 * @return  string - the date in the above format
		 */
		formatDate: function( format, date, settings ) {
			var iFormat,
				dayNamesShort = ( settings ? settings.dayNamesShort : null ) || this._defaults.dayNamesShort,
				dayNames = ( settings ? settings.dayNames : null ) || this._defaults.dayNames,
				monthNamesShort = ( settings ? settings.monthNamesShort : null ) || this._defaults.monthNamesShort,
				monthNames = ( settings ? settings.monthNames : null ) || this._defaults.monthNames,
				// Check whether a format character is doubled
				lookAhead = function( match ) {
					var matches = iFormat + 1 < format.length && format.charAt( iFormat + 1 ) === match;
					if ( matches ) {
						iFormat = iFormat + 1;
					}
					return matches;
				},
				// Format a number, with leading zero if necessary
				formatNumber = function( match, value, len ) {
					var num = '' + value;
					if ( lookAhead( match ) ) {
						while ( num.length < len ) {
							num = '0' + num;
						}
					}
					return num;
				},
				// Format a name, short or long as requested
				formatName = function( match, value, shortNames, longNames ) {
					return lookAhead( match ) ? longNames[ value ] : shortNames[ value ];
				},
				output = '',
				literal = false;

			if ( ! date ) {
				return '';
			}

			if ( date ) {
				for ( iFormat = 0; iFormat < format.length; iFormat += 1 ) {
					if ( literal ) {
						if ( format.charAt( iFormat ) === "'" && ! lookAhead( "'" ) ) {
							literal = false;
						} else {
							output += format.charAt( iFormat );
						}
					} else {
						switch ( format.charAt( iFormat ) ) {
							case 'd':
								output += formatNumber( 'd', date.getDate(), 2 );
								break;
							case 'D':
								output += formatName( 'D', date.getDay(), dayNamesShort, dayNames );
								break;
							case 'o':
								output += formatNumber( 'o', Math.round( ( new Date( date.getFullYear(), date.getMonth(), date.getDate() ).getTime() - new Date( date.getFullYear(), 0, 0 ).getTime() ) / 86400000 ), 3 );
								break;
							case 'm':
								output += formatNumber( 'm', date.getMonth() + 1, 2 );
								break;
							case 'M':
								output += formatName( 'M', date.getMonth(), monthNamesShort, monthNames );
								break;
							case 'y':
								output += lookAhead( 'y' ) ? date.getFullYear() : ( date.getFullYear() % 100 < 10 ? '0' : '' ) + ( date.getFullYear() % 100 );
								break;
							case '@':
								output += date.getTime();
								break;
							case '!':
								output += ( date.getTime() * 10000 ) + this._ticksTo1970;
								break;
							case "'":
								if ( lookAhead( "'" ) ) {
									output += "'";
								} else {
									literal = true;
								}
								break;
							default:
								output += format.charAt( iFormat );
						}
					}
				}
			}
			return output;
		},

		/* Extract all possible characters from the date format. */
		_possibleChars: function( format ) {
			var iFormat,
				chars = '',
				literal = false,
				// Check whether a format character is doubled
				lookAhead = function( match ) {
					var matches = iFormat + 1 < format.length && format.charAt( iFormat + 1 ) === match;
					if ( matches ) {
						iFormat = iFormat + 1;
					}
					return matches;
				};

			for ( iFormat = 0; iFormat < format.length; iFormat += 1 ) {
				if ( literal ) {
					if ( format.charAt( iFormat ) === "'" && ! lookAhead( "'" ) ) {
						literal = false;
					} else {
						chars += format.charAt( iFormat );
					}
				} else {
					switch ( format.charAt( iFormat ) ) {
						case 'd':
						case 'm':
						case 'y':
						case '@':
							chars += '0123456789';
							break;
						case 'D':
						case 'M':
							return null; // Accept anything
						case "'":
							if ( lookAhead( "'" ) ) {
								chars += "'";
							} else {
								literal = true;
							}
							break;
						default:
							chars += format.charAt( iFormat );
					}
				}
			}
			return chars;
		},

		/* Get a setting value, defaulting if necessary. */
		_get: function( inst, name ) {
			return inst.settings[ name ] !== undefined ? inst.settings[ name ] : this._defaults[ name ];
		},

		/* Parse existing date and initialise date picker. */
		_setDateFromField: function( inst, noDefault ) {
			var dateFormat;
			var dates;
			var defaultDate;
			var date;
			var settings;

			if ( inst.input.val() === inst.lastVal ) {
				return;
			}

			dateFormat = this._get( inst, 'dateFormat' );
			dates = inst.input ? inst.input.val() : null;
			defaultDate = this._getDefaultDate( inst );
			date = defaultDate;
			settings = this._getFormatConfig( inst );

			inst.lastVal = dates;

			try {
				date = this.parseDate( dateFormat, dates, settings ) || defaultDate;
			} catch ( event ) {
				dates = noDefault ? '' : dates;
			}
			inst.selectedDay = date.getDate();
			inst.selectedMonth = date.getMonth();
			inst.drawMonth = inst.selectedMonth;
			inst.selectedYear = date.getFullYear();
			inst.drawYear = inst.selectedYear;
			inst.currentDay = dates ? date.getDate() : 0;
			inst.currentMonth = dates ? date.getMonth() : 0;
			inst.currentYear = dates ? date.getFullYear() : 0;
			this._adjustInstDate( inst );
		},

		/* Retrieve the default date shown on opening. */
		_getDefaultDate: function( inst ) {
			return this._restrictMinMax( inst, this._determineDate( inst, this._get( inst, 'defaultDate' ), new Date() ) );
		},

		/* A date may be specified as an exact value or a relative one. */
		_determineDate: function( inst, date, defaultDate ) {
			var offsetNumeric = function( offset ) {
					var sdate = new Date();
					sdate.setDate( sdate.getDate() + offset );
					return sdate;
				},
				offsetString = function( offset ) {
					var sdate;
					var year;
					var month;
					var day;
					var pattern;
					var matches;

					try {
						return $.tm_datepicker.parseDate( $.tm_datepicker._get( inst, 'dateFormat' ), offset, $.tm_datepicker._getFormatConfig( inst ) );
					} catch ( e ) {
						// Ignore
					}

					sdate = ( offset.toLowerCase().match( /^c/ ) ? $.tm_datepicker._getDate( inst ) : null ) || new Date();
					year = sdate.getFullYear();
					month = sdate.getMonth();
					day = sdate.getDate();
					pattern = /([+-]?[0-9]+)\s*(d|D|w|W|m|M|y|Y)?/g;
					matches = pattern.exec( offset );

					while ( matches ) {
						switch ( matches[ 2 ] || 'd' ) {
							case 'd':
							case 'D':
								day += parseInt( matches[ 1 ], 10 );
								break;
							case 'w':
							case 'W':
								day += parseInt( matches[ 1 ], 10 ) * 7;
								break;
							case 'm':
							case 'M':
								month += parseInt( matches[ 1 ], 10 );
								day = Math.min( day, $.tm_datepicker._getDaysInMonth( year, month ) );
								break;
							case 'y':
							case 'Y':
								year += parseInt( matches[ 1 ], 10 );
								day = Math.min( day, $.tm_datepicker._getDaysInMonth( year, month ) );
								break;
						}
						matches = pattern.exec( offset );
					}
					return new Date( year, month, day );
				},
				newDate = date === null || date === '' ? defaultDate : typeof date === 'string' ? offsetString( date ) : typeof date === 'number' ? ( ! Number.isFinite( date ) ? defaultDate : offsetNumeric( date ) ) : new Date( date.getTime() );

			newDate = newDate && newDate.toString() === 'Invalid Date' ? defaultDate : newDate;
			if ( newDate ) {
				newDate.setHours( 0 );
				newDate.setMinutes( 0 );
				newDate.setSeconds( 0 );
				newDate.setMilliseconds( 0 );
			}
			return this._daylightSavingAdjust( newDate );
		},

		/* Handle switch to/from daylight saving.
		 * Hours may be non-zero on daylight saving cut-over:
		 * > 12 when midnight changeover, but then cannot generate
		 * midnight datetime, so jump to 1AM, otherwise reset.
		 * @param  date  (Date) the date to check
		 * @return  (Date) the corrected date
		 */
		_daylightSavingAdjust: function( date ) {
			if ( ! date ) {
				return null;
			}
			date.setHours( date.getHours() > 12 ? date.getHours() + 2 : 0 );
			return date;
		},

		/* Set the date(s) directly. */
		_setDate: function( inst, date, noChange ) {
			var clear = ! date,
				origMonth = inst.selectedMonth,
				origYear = inst.selectedYear,
				newDate = this._restrictMinMax( inst, this._determineDate( inst, date, new Date() ) );

			inst.currentDay = newDate.getDate();
			inst.selectedDay = inst.currentDay;
			inst.currentMonth = newDate.getMonth();
			inst.drawMonth = inst.currentMonth;
			inst.selectedMonth = inst.currentMonth;
			inst.currentYear = newDate.getFullYear();
			inst.drawYear = inst.currentYear;
			inst.selectedYear = inst.currentYear;
			if ( ( origMonth !== inst.selectedMonth || origYear !== inst.selectedYear ) && ! noChange ) {
				this._notifyChange( inst );
			}
			this._adjustInstDate( inst );
			if ( inst.input ) {
				inst.input.val( clear ? '' : this._formatDate( inst ) );
			}
		},

		/* Retrieve the date(s) directly. */
		_getDate: function( inst ) {
			var startDate = ! inst.currentYear || ( inst.input && inst.input.val() === '' ) ? null : this._daylightSavingAdjust( new Date( inst.currentYear, inst.currentMonth, inst.currentDay ) );
			return startDate;
		},

		/* Attach the onxxx handlers.  These are declared statically so
		 * they work with static code transformers like Caja.
		 */
		_attachHandlers: function( inst ) {
			var stepMonths = this._get( inst, 'stepMonths' ),
				id = '#' + inst.id.replace( /\\\\/g, '\\' );
			inst.dpDiv.find( '[data-handler]' ).map( function() {
				var handler = {
					prevM: function() {
						$.tm_datepicker._adjustDate( id, -1, 'M' );
					},
					nextM: function() {
						$.tm_datepicker._adjustDate( id, +1, 'M' );
					},
					prevY: function() {
						$.tm_datepicker._adjustDate( id, -1, 'Y' );
					},
					nextY: function() {
						$.tm_datepicker._adjustDate( id, +1, 'Y' );
					},
					prev: function() {
						$.tm_datepicker._adjustDate( id, -stepMonths, 'M' );
					},
					next: function() {
						$.tm_datepicker._adjustDate( id, +stepMonths, 'M' );
					},
					hide: function() {
						$.tm_datepicker._hideDatepicker();
					},
					today: function() {
						/*var tempDate = new Date(),
                         today = $.tm_datepicker._daylightSavingAdjust(
                         new Date(tempDate.getFullYear(), tempDate.getMonth(), tempDate.getDate()));
                         $.tm_datepicker._setDate(inst, today);
                         $.tm_datepicker._gotoToday(id);*/
					},
					selectDay: function() {
						$.tm_datepicker._selectDay( id, +this.getAttribute( 'data-month' ), +this.getAttribute( 'data-year' ), this );
						return false;
					},
					selectMonth: function() {
						$.tm_datepicker._selectMonthYear( id, this, 'M' );
						return false;
					},
					selectYear: function() {
						$.tm_datepicker._selectMonthYear( id, this, 'Y' );
						return false;
					}
				};
				$( this ).on( this.getAttribute( 'data-event' ), handler[ this.getAttribute( 'data-handler' ) ] );
				return null;
			} );
		},
		/* Determines if we should allow a "next/prev" month display change. */
		_tm_canAdjustMonth: function( inst, offset, curYear, curMonth ) {
			var date = this._daylightSavingAdjust( new Date( curYear, curMonth + offset, 1 ) );

			if ( offset < 0 ) {
				date.setDate( this._getDaysInMonth( date.getFullYear(), date.getMonth() ) );
			}
			return this._isInRange( inst, date );
		},

		/* Generate the HTML for the current state of the date picker. */
		_generateHTML: function( inst ) {
			var maxDraw,
				prevText,
				prev,
				nextText,
				next,
				currentText,
				gotoDate,
				firstDay,
				showWeek,
				dayNames,
				dayNamesMin,
				monthNames,
				beforeShowDay,
				showOtherMonths,
				selectOtherMonths,
				defaultDate,
				html,
				dow,
				row,
				group,
				col,
				selectedDate,
				cornerClass,
				calender,
				thead,
				day,
				daysInMonth,
				leadDays,
				curRows,
				numRows,
				printDate,
				dRow,
				tbody,
				daySettings,
				otherMonth,
				unselectable,
				tempDate = new Date(),
				today = this._daylightSavingAdjust( new Date( tempDate.getFullYear(), tempDate.getMonth(), tempDate.getDate() ) ), // clear time
				isRTL = this._get( inst, 'isRTL' ),
				showButtonPanel = this._get( inst, 'showButtonPanel' ),
				hideIfNoPrevNext = this._get( inst, 'hideIfNoPrevNext' ),
				navigationAsDateFormat = this._get( inst, 'navigationAsDateFormat' ),
				numMonths = this._getNumberOfMonths( inst ),
				showCurrentAtPos = this._get( inst, 'showCurrentAtPos' ),
				stepMonths = this._get( inst, 'stepMonths' ),
				isMultiMonth = numMonths[ 0 ] !== 1 || numMonths[ 1 ] !== 1,
				currentDate = this._daylightSavingAdjust( ! inst.currentDay ? new Date( 9999, 9, 9 ) : new Date( inst.currentYear, inst.currentMonth, inst.currentDay ) ),
				minDate = this._getMinMaxDate( inst, 'min' ),
				maxDate = this._getMinMaxDate( inst, 'max' ),
				drawMonth = inst.drawMonth - showCurrentAtPos,
				drawYear = inst.drawYear,
				tm_month,
				tm_year,
				tm_prev_arrow_m,
				tm_next_arrow_m,
				tm_prev_arrow_y,
				tm_next_arrow_y,
				tm_controls,
				tm_buttonPanel;
			var can_adjust_1;
			var can_adjust_2;
			var can_adjust_3;
			var can_adjust_4;

			if ( drawMonth < 0 ) {
				drawMonth += 12;
				drawYear -= 1;
			}
			if ( maxDate ) {
				maxDraw = this._daylightSavingAdjust( new Date( maxDate.getFullYear(), maxDate.getMonth() - ( numMonths[ 0 ] * numMonths[ 1 ] ) + 1, maxDate.getDate() ) );
				maxDraw = minDate && maxDraw < minDate ? minDate : maxDraw;
				while ( this._daylightSavingAdjust( new Date( drawYear, drawMonth, 1 ) ) > maxDraw ) {
					drawMonth -= 1;
					if ( drawMonth < 0 ) {
						drawMonth = 11;
						drawYear -= 1;
					}
				}
			}
			inst.drawMonth = drawMonth;
			inst.drawYear = drawYear;

			prevText = this._get( inst, 'prevText' );
			prevText = ! navigationAsDateFormat ? prevText : this.formatDate( prevText, this._daylightSavingAdjust( new Date( drawYear, drawMonth - stepMonths, 1 ) ), this._getFormatConfig( inst ) );

			prev = this._canAdjustMonth( inst, -1, drawYear, drawMonth ) ? "<a class='ui-tm-datepicker-prev ui-corner-all' data-handler='prev' data-event='click'" + " title='" + prevText + "'><span class='ui-icon ui-icon-circle-triangle-" + ( isRTL ? 'e' : 'w' ) + "'>" + prevText + '</span></a>' : hideIfNoPrevNext ? '' : "<a class='ui-tm-datepicker-prev ui-corner-all ui-state-disabled' title='" + prevText + "'><span class='ui-icon ui-icon-circle-triangle-" + ( isRTL ? 'e' : 'w' ) + "'>" + prevText + '</span></a>';

			nextText = this._get( inst, 'nextText' );
			nextText = ! navigationAsDateFormat ? nextText : this.formatDate( nextText, this._daylightSavingAdjust( new Date( drawYear, drawMonth + stepMonths, 1 ) ), this._getFormatConfig( inst ) );

			next = this._canAdjustMonth( inst, +1, drawYear, drawMonth ) ? "<a class='ui-tm-datepicker-next ui-corner-all' data-handler='next' data-event='click'" + " title='" + nextText + "'><span class='ui-icon ui-icon-circle-triangle-" + ( isRTL ? 'w' : 'e' ) + "'>" + nextText + '</span></a>' : hideIfNoPrevNext ? '' : "<a class='ui-tm-datepicker-next ui-corner-all ui-state-disabled' title='" + nextText + "'><span class='ui-icon ui-icon-circle-triangle-" + ( isRTL ? 'w' : 'e' ) + "'>" + nextText + '</span></a>';

			currentText = this._get( inst, 'currentText' );
			gotoDate = this._get( inst, 'gotoCurrent' ) && inst.currentDay ? currentDate : today;
			currentText = ! navigationAsDateFormat ? currentText : this.formatDate( currentText, gotoDate, this._getFormatConfig( inst ) );

			tm_controls = ! inst.inline ? "<div class='tm-ui-dp-btn-wrap tm-ui-dp-close'><div role='button' class='tm-ui-dp-btn ui-tm-datepicker-close' data-handler='hide' data-event='click'>" + this._get( inst, 'closeText' ) + '</div></div>' : '';

			tm_buttonPanel = showButtonPanel ? "<div class='tm-ui-dp-buttonpane'>" + ( isRTL ? tm_controls : '' ) + ( this._isInRange( inst, gotoDate ) ? "<div class='tm-ui-dp-btn-wrap tm-ui-dp-current'><div role='button' class='tm-ui-dp-btn ui-tm-datepicker-current' data-handler='today' data-event='click'" + '>' + currentText + '</div></div>' : '' ) + ( isRTL ? '' : tm_controls ) + '</div>' : '';

			firstDay = parseInt( this._get( inst, 'firstDay' ), 10 );
			firstDay = isNaN( firstDay ) ? 0 : firstDay;

			showWeek = this._get( inst, 'showWeek' );
			dayNames = this._get( inst, 'dayNames' );
			dayNamesMin = this._get( inst, 'dayNamesMin' );
			monthNames = this._get( inst, 'monthNames' );
			//monthNamesShort = this._get(inst, "monthNamesShort");
			beforeShowDay = this._get( inst, 'beforeShowDay' );
			showOtherMonths = this._get( inst, 'showOtherMonths' );
			selectOtherMonths = this._get( inst, 'selectOtherMonths' );
			defaultDate = this._getDefaultDate( inst );
			html = '';

			can_adjust_1 = this._tm_canAdjustMonth( inst, -1, drawYear, drawMonth );
			can_adjust_2 = this._tm_canAdjustMonth( inst, +1, drawYear, drawMonth );
			can_adjust_3 = this._tm_canAdjustMonth( inst, -12, drawYear, drawMonth );
			can_adjust_4 = this._tm_canAdjustMonth( inst, +12, drawYear, drawMonth );

			tm_prev_arrow_m = '<div class="tm-ui-dp-button-prev tm-ui-dp-button">' + '<div ' + ( can_adjust_1 ? 'data-handler="prevM" data-event="click" ' : '' ) + 'aria-label="' + prevText + '" class="' + ( can_adjust_1 ? '' : 'tm-ui-dp-button-disabled ' ) + 'tm-ui-dp-button-arrow tcfa tcfa-angle-left" tabindex="0" role="button"></div>' + '</div>';
			tm_next_arrow_m = '<div class="tm-ui-dp-button-next tm-ui-dp-button">' + '<div ' + ( can_adjust_2 ? 'data-handler="nextM" data-event="click" ' : '' ) + 'aria-label="' + nextText + '" class="' + ( can_adjust_2 ? '' : 'tm-ui-dp-button-disabled ' ) + 'tm-ui-dp-button-arrow tcfa tcfa-angle-right" tabindex="0" role="button"></div>' + '</div>';
			tm_prev_arrow_y = '<div class="tm-ui-dp-button-prev tm-ui-dp-button">' + '<div ' + ( can_adjust_3 ? 'data-handler="prevY" data-event="click" ' : '' ) + 'aria-label="' + prevText + '" class="' + ( can_adjust_3 ? '' : 'tm-ui-dp-button-disabled ' ) + 'tm-ui-dp-button-arrow tcfa tcfa-angle-left" tabindex="0" role="button"></div>' + '</div>';
			tm_next_arrow_y = '<div class="tm-ui-dp-button-next tm-ui-dp-button">' + '<div ' + ( can_adjust_4 ? 'data-handler="nextY" data-event="click" ' : '' ) + 'aria-label="' + nextText + '" class="' + ( can_adjust_4 ? '' : 'tm-ui-dp-button-disabled ' ) + 'tm-ui-dp-button-arrow tcfa tcfa-angle-right" tabindex="0" role="button"></div>' + '</div>';

			tm_month = hideIfNoPrevNext ? '' : '<div class="tm-ui-dp-title-button">' + ( isRTL ? tm_next_arrow_m : tm_prev_arrow_m ) + '<span aria-label="' + monthNames[ drawMonth ] + '" role="button" class="tm-ui-dp-month">' + monthNames[ drawMonth ] + '</span>' + ( isRTL ? tm_prev_arrow_m : tm_next_arrow_m ) + '</div>';

			tm_year = hideIfNoPrevNext ? '' : '<div class="tm-ui-dp-title-button">' + ( isRTL ? tm_next_arrow_y : tm_prev_arrow_y ) + '<span class="tm-ui-dp-year" role="button" aria-label="' + drawYear + '">' + drawYear + '</span>' + ( isRTL ? tm_prev_arrow_y : tm_next_arrow_y ) + '</div>';

			html += "<div class='tm-ui-dp-wrap'>" + "<div class='tm-ui-dp-overlay'></div>" + "<div class='tm-ui-dp'>" + "<div class='tm-ui-dp-container'>" + "<div class='tm-ui-dp-inner-container'>" + "<div class='tm-ui-dp-ui'>" + "<div class='tm-ui-dp-ui-inner'>" + "<div class='tm-ui-dp-main-wrap'>" + "<div class='tm-ui-dp-main'>";

			for ( row = 0; row < numMonths[ 0 ]; row += 1 ) {
				group = '';
				this.maxRows = 4;
				for ( col = 0; col < numMonths[ 1 ]; col += 1 ) {
					selectedDate = this._daylightSavingAdjust( new Date( drawYear, drawMonth, inst.selectedDay ) );
					cornerClass = ' ui-corner-all';
					calender = '';
					if ( isMultiMonth ) {
						calender += "<div class='ui-tm-datepicker-group";
						if ( numMonths[ 1 ] > 1 ) {
							switch ( col ) {
								case 0:
									calender += ' ui-tm-datepicker-group-first';
									cornerClass = ' ui-corner-' + ( isRTL ? 'right' : 'left' );
									break;
								case numMonths[ 1 ] - 1:
									calender += ' ui-tm-datepicker-group-last';
									cornerClass = ' ui-corner-' + ( isRTL ? 'left' : 'right' );
									break;
								default:
									calender += ' ui-tm-datepicker-group-middle';
									cornerClass = '';
									break;
							}
						}
						calender += "'>";
					}
					calender +=
						"<div class='tm-ui-dp-header ui-tm-datepicker-header ui-widget-header ui-helper-clearfix" +
						cornerClass +
						"'>" +
						( /all|left/.test( cornerClass ) && row === 0 ? ( isRTL ? next : prev ) : '' ) +
						( /all|right/.test( cornerClass ) && row === 0 ? ( isRTL ? prev : next ) : '' ) +
						// month headers

						'<div class="ui-tm-datepicker-title tm-ui-dp-title">' +
						( /all|left/.test( cornerClass ) && row === 0 ? ( isRTL ? tm_year + tm_month : tm_month + tm_year ) : '' ) +
						'</div>' +
						'</div>';

					calender += "<div class='tm-ui-dp-main-content'>";

					/* Days start */
					calender += "<div class='tm-ui-dp-main-header-wrap'><div class='tm-ui-dp-main-header'>";
					calender += "<table class='ui-tm-datepicker-calendar'><tbody>" + '<tr>';
					thead = showWeek ? "<th class='ui-tm-datepicker-week-col'>" + this._get( inst, 'weekHeader' ) + '</th>' : '';
					for ( dow = 0; dow < 7; dow += 1 ) {
						// days of the week
						day = ( dow + firstDay ) % 7;
						thead += "<th scope='col'" + ( ( dow + firstDay + 6 ) % 7 >= 5 ? " class='ui-tm-datepicker-week-end'" : '' ) + '>' + "<span title='" + dayNames[ day ] + "'>" + dayNamesMin[ day ] + '</span></th>';
					}
					calender += thead + '</tr></tbody></table>';
					calender += '</div></div>';
					/* Days end */

					calender += "<div class='tm-ui-dp-main-table'>";

					daysInMonth = this._getDaysInMonth( drawYear, drawMonth );
					if ( drawYear === inst.selectedYear && drawMonth === inst.selectedMonth ) {
						inst.selectedDay = Math.min( inst.selectedDay, daysInMonth );
					}
					leadDays = ( this._getFirstDayOfMonth( drawYear, drawMonth ) - firstDay + 7 ) % 7;
					curRows = 6;
					numRows = isMultiMonth ? ( this.maxRows > curRows ? this.maxRows : curRows ) : curRows; //If multiple months, use the higher number of rows
					// (see #7043)
					this.maxRows = numRows;
					printDate = this._daylightSavingAdjust( new Date( drawYear, drawMonth, 1 - leadDays ) );
					for ( dRow = 0; dRow < numRows; dRow += 1 ) {
						// create date picker rows
						calender += "<div class='tm-ui-dp-main-row'>";
						tbody = ! showWeek ? '' : "<div class='ui-tm-datepicker-week-col'>" + this._get( inst, 'calculateWeek' )( printDate ) + '</div>';
						for ( dow = 0; dow < 7; dow += 1 ) {
							// create date picker days
							daySettings = beforeShowDay ? beforeShowDay.apply( inst.input ? inst.input[ 0 ] : null, [ printDate ] ) : [ true, '' ];
							otherMonth = printDate.getMonth() !== drawMonth;
							unselectable = ( otherMonth && ! selectOtherMonths ) || ! daySettings[ 0 ] || ( minDate && printDate < minDate ) || ( maxDate && printDate > maxDate );

							tbody +=
								"<div class='tm-ui-dp-main-cell " +
								( ( dow + firstDay + 6 ) % 7 >= 5 ? ' ui-tm-datepicker-week-end' : '' ) + // highlight weekends
								( otherMonth ? ' ui-tm-datepicker-other-month' : '' ) + // highlight days from other months
								( ( printDate.getTime() === selectedDate.getTime() && drawMonth === inst.selectedMonth && inst._keyEvent ) || ( defaultDate.getTime() === printDate.getTime() && defaultDate.getTime() === selectedDate.getTime() ) ? ' ' + this._dayOverClass : '' ) +
								( unselectable ? ' ' + this._unselectableClass + ' ui-state-disabled' : '' ) + // highlight unselectable days
								( otherMonth && ! showOtherMonths ? '' : ' ' + daySettings[ 1 ] + ( printDate.getTime() === currentDate.getTime() ? ' ' + this._currentClass : '' ) + ( printDate.getTime() === today.getTime() ? ' ui-tm-datepicker-today' : '' ) ) +
								"'" + // highlight today (if different)
								( ( ! otherMonth || showOtherMonths ) && daySettings[ 2 ] ? " title='" + daySettings[ 2 ].replace( /'/g, '&#39;' ) + "'" : '' ) + // cell
								// title
								( unselectable ? '' : " data-handler='selectDay' data-event='click' data-month='" + printDate.getMonth() + "' data-year='" + printDate.getFullYear() + "'" ) +
								'>' + // actions
								"<div class='tm-ui-dp-main-cell-inner'>" +
								"<div class='tm-ui-dp-main-cell-content'>" +
								( otherMonth && ! showOtherMonths ? '&#xa0;' : unselectable ? "<span class='ui-state-default'>" + printDate.getDate() + '</span>' : "<a class='ui-state-default" + ( printDate.getTime() === today.getTime() ? ' ui-state-highlight' : '' ) + ( printDate.getTime() === currentDate.getTime() ? ' ui-state-active' : '' ) + ( otherMonth ? ' ui-priority-secondary' : '' ) + "' href='#'>" + printDate.getDate() + '</a>' ) +
								'</div></div>' +
								'</div>'; // display selectable date
							printDate.setDate( printDate.getDate() + 1 );
							printDate = this._daylightSavingAdjust( printDate );
						}
						calender += tbody + '</div>';
					}
					drawMonth += 1;
					if ( drawMonth > 11 ) {
						drawMonth = 0;
						drawYear += 1;
					}

					calender += '</div>';

					calender += '</div>' + ( isMultiMonth ? '</div>' + ( numMonths[ 0 ] > 0 && col === numMonths[ 1 ] - 1 ? "<div class='ui-tm-datepicker-row-break'></div>" : '' ) : '' );
					group += calender;
				}
				html += group;
			}
			html += '</div></div></div></div></div>';
			html += tm_buttonPanel;
			html += '</div></div></div>';
			inst._keyEvent = false;
			return html;
		},

		/* Generate the month and year header. */
		_generateMonthYearHeader: function( inst, drawMonth, drawYear, minDate, maxDate, secondary, monthNames, monthNamesShort ) {
			var inMinYear,
				inMaxYear,
				month,
				years,
				thisYear,
				determineYear,
				year,
				endYear,
				changeMonth = this._get( inst, 'changeMonth' ),
				changeYear = this._get( inst, 'changeYear' ),
				showMonthAfterYear = this._get( inst, 'showMonthAfterYear' ),
				html = "<div class='ui-tm-datepicker-title'>",
				monthHtml = '';

			// month selection
			if ( secondary || ! changeMonth ) {
				monthHtml += "<span class='ui-tm-datepicker-month'>" + monthNames[ drawMonth ] + '</span>';
			} else {
				inMinYear = minDate && minDate.getFullYear() === drawYear;
				inMaxYear = maxDate && maxDate.getFullYear() === drawYear;
				monthHtml += "<select class='ui-tm-datepicker-month' data-handler='selectMonth' data-event='change'>";
				for ( month = 0; month < 12; month += 1 ) {
					if ( ( ! inMinYear || month >= minDate.getMonth() ) && ( ! inMaxYear || month <= maxDate.getMonth() ) ) {
						monthHtml += "<option value='" + month + "'" + ( month === drawMonth ? " selected='selected'" : '' ) + '>' + monthNamesShort[ month ] + '</option>';
					}
				}
				monthHtml += '</select>';
			}

			if ( ! showMonthAfterYear ) {
				html += monthHtml + ( secondary || ! ( changeMonth && changeYear ) ? '&#xa0;' : '' );
			}

			// year selection
			if ( ! inst.yearshtml ) {
				inst.yearshtml = '';
				if ( secondary || ! changeYear ) {
					html += "<span class='ui-tm-datepicker-year'>" + drawYear + '</span>';
				} else {
					// determine range of years to display
					years = this._get( inst, 'yearRange' ).split( ':' );
					thisYear = new Date().getFullYear();
					determineYear = function( value ) {
						var syear = value.match( /c[+-].*/ ) ? drawYear + parseInt( value.substring( 1 ), 10 ) : value.match( /[+-].*/ ) ? thisYear + parseInt( value, 10 ) : parseInt( value, 10 );
						return isNaN( syear ) ? thisYear : syear;
					};
					year = determineYear( years[ 0 ] );
					endYear = Math.max( year, determineYear( years[ 1 ] || '' ) );
					year = minDate ? Math.max( year, minDate.getFullYear() ) : year;
					endYear = maxDate ? Math.min( endYear, maxDate.getFullYear() ) : endYear;
					inst.yearshtml += "<select class='ui-tm-datepicker-year' data-handler='selectYear' data-event='change'>";
					for ( ; year <= endYear; year += 1 ) {
						inst.yearshtml += "<option value='" + year + "'" + ( year === drawYear ? " selected='selected'" : '' ) + '>' + year + '</option>';
					}
					inst.yearshtml += '</select>';

					html += inst.yearshtml;
					inst.yearshtml = null;
				}
			}

			html += this._get( inst, 'yearSuffix' );
			if ( showMonthAfterYear ) {
				html += ( secondary || ! ( changeMonth && changeYear ) ? '&#xa0;' : '' ) + monthHtml;
			}
			html += '</div>'; // Close datepicker_header
			return html;
		},

		/* Adjust one of the date sub-fields. */
		_adjustInstDate: function( inst, offset, period ) {
			var year = inst.selectedYear + ( period === 'Y' ? offset : 0 ),
				month = inst.selectedMonth + ( period === 'M' ? offset : 0 ),
				day = Math.min( inst.selectedDay, this._getDaysInMonth( year, month ) ) + ( period === 'D' ? offset : 0 ),
				date = this._restrictMinMax( inst, this._daylightSavingAdjust( new Date( year, month, day ) ) );

			inst.selectedDay = date.getDate();
			inst.selectedMonth = date.getMonth();
			inst.drawMonth = inst.selectedMonth;
			inst.selectedYear = date.getFullYear();
			inst.drawYear = inst.selectedYear;
			if ( period === 'M' || period === 'Y' ) {
				this._notifyChange( inst );
			}
		},

		/* Ensure a date is within any min/max bounds. */
		_restrictMinMax: function( inst, date ) {
			var minDate = this._getMinMaxDate( inst, 'min' ),
				maxDate = this._getMinMaxDate( inst, 'max' ),
				newDate = minDate && date < minDate ? minDate : date;
			return maxDate && newDate > maxDate ? maxDate : newDate;
		},

		/* Notify change of month/year. */
		_notifyChange: function( inst ) {
			var onChange = this._get( inst, 'onChangeMonthYear' );
			if ( onChange ) {
				onChange.apply( inst.input ? inst.input[ 0 ] : null, [ inst.selectedYear, inst.selectedMonth + 1, inst ] );
			}
		},

		/* Determine the number of months to show. */
		_getNumberOfMonths: function( inst ) {
			var numMonths = this._get( inst, 'numberOfMonths' );
			return numMonths === null ? [ 1, 1 ] : typeof numMonths === 'number' ? [ 1, numMonths ] : numMonths;
		},

		/* Determine the current maximum date - ensure no time components are set. */
		_getMinMaxDate: function( inst, minMax ) {
			return this._determineDate( inst, this._get( inst, minMax + 'Date' ), null );
		},

		/* Find the number of days in a given month. */
		_getDaysInMonth: function( year, month ) {
			return 32 - this._daylightSavingAdjust( new Date( year, month, 32 ) ).getDate();
		},

		/* Find the day of the week of the first of a month. */
		_getFirstDayOfMonth: function( year, month ) {
			return new Date( year, month, 1 ).getDay();
		},

		/* Determines if we should allow a "next/prev" month display change. */
		_canAdjustMonth: function( inst, offset, curYear, curMonth ) {
			var numMonths = this._getNumberOfMonths( inst ),
				date = this._daylightSavingAdjust( new Date( curYear, curMonth + ( offset < 0 ? offset : numMonths[ 0 ] * numMonths[ 1 ] ), 1 ) );

			if ( offset < 0 ) {
				date.setDate( this._getDaysInMonth( date.getFullYear(), date.getMonth() ) );
			}
			return this._isInRange( inst, date );
		},

		/* Is the given date in the accepted range? */
		_isInRange: function( inst, date ) {
			var yearSplit,
				currentYear,
				minDate = this._getMinMaxDate( inst, 'min' ),
				maxDate = this._getMinMaxDate( inst, 'max' ),
				minYear = null,
				maxYear = null,
				years = this._get( inst, 'yearRange' );
			if ( years ) {
				yearSplit = years.split( ':' );
				currentYear = new Date().getFullYear();
				minYear = parseInt( yearSplit[ 0 ], 10 );
				maxYear = parseInt( yearSplit[ 1 ], 10 );
				if ( yearSplit[ 0 ].match( /[+-].*/ ) ) {
					minYear += currentYear;
				}
				if ( yearSplit[ 1 ].match( /[+-].*/ ) ) {
					maxYear += currentYear;
				}
			}

			return ( ! minDate || date.getTime() >= minDate.getTime() ) && ( ! maxDate || date.getTime() <= maxDate.getTime() ) && ( ! minYear || date.getFullYear() >= minYear ) && ( ! maxYear || date.getFullYear() <= maxYear );
		},

		/* Provide the configuration settings for formatting/parsing. */
		_getFormatConfig: function( inst ) {
			var shortYearCutoff = this._get( inst, 'shortYearCutoff' );
			shortYearCutoff = typeof shortYearCutoff !== 'string' ? shortYearCutoff : ( new Date().getFullYear() % 100 ) + parseInt( shortYearCutoff, 10 );
			return {
				shortYearCutoff: shortYearCutoff,
				dayNamesShort: this._get( inst, 'dayNamesShort' ),
				dayNames: this._get( inst, 'dayNames' ),
				monthNamesShort: this._get( inst, 'monthNamesShort' ),
				monthNames: this._get( inst, 'monthNames' )
			};
		},

		/* Format the given date for display. */
		_formatDate: function( inst, day, month, year ) {
			var date;

			if ( ! day ) {
				inst.currentDay = inst.selectedDay;
				inst.currentMonth = inst.selectedMonth;
				inst.currentYear = inst.selectedYear;
			}
			date = day ? ( typeof day === 'object' ? day : this._daylightSavingAdjust( new Date( year, month, day ) ) ) : this._daylightSavingAdjust( new Date( inst.currentYear, inst.currentMonth, inst.currentDay ) );
			return this.formatDate( this._get( inst, 'dateFormat' ), date, this._getFormatConfig( inst ) );
		}
	} );

	/* Invoke the datepicker functionality.
     @param  options  string - a command, optionally followed by additional parameters or
     Object - settings for attaching new datepicker functionality
     @return  jQuery object */
	$.fn.tm_datepicker = function( options ) {
		var otherArgs;

		/* Verify an empty collection wasn't passed - Fixes #6976 */
		if ( ! this.length ) {
			return this;
		}

		/* Initialise the date picker. */
		if ( ! $.tm_datepicker.initialized ) {
			$( document ).on( 'mousedown', $.tm_datepicker._checkExternalClick );
			$.tm_datepicker.initialized = true;
		}

		/* Append datepicker main container to body if not exist. */
		if ( $( '#' + $.tm_datepicker._mainDivId ).length === 0 ) {
			$( 'body' ).append( $.tm_datepicker.dpDiv );
		}

		otherArgs = Array.prototype.slice.call( arguments, 1 );
		if ( typeof options === 'string' && ( options === 'isDisabled' || options === 'getDate' || options === 'widget' ) ) {
			return $.tm_datepicker[ '_' + options + 'Datepicker' ].apply( $.tm_datepicker, [ this[ 0 ] ].concat( otherArgs ) );
		}
		if ( options === 'option' && arguments.length === 2 && typeof arguments[ 1 ] === 'string' ) {
			return $.tm_datepicker[ '_' + options + 'Datepicker' ].apply( $.tm_datepicker, [ this[ 0 ] ].concat( otherArgs ) );
		}
		return this.each( function() {
			if ( typeof options === 'string' ) {
				$.tm_datepicker[ '_' + options + 'Datepicker' ].apply( $.tm_datepicker, [ this ].concat( otherArgs ) );
			} else {
				$.tm_datepicker._attachDatepicker( this, options );
			}
		} );
	};

	$.tm_datepicker = new Datepicker(); // singleton instance
	$.tm_datepicker.initialized = false;
	$.tm_datepicker.uuid = new Date().getTime();
	$.tm_datepicker.version = '1.11.4';
} ) );
