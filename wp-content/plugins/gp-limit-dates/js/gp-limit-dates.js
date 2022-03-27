/**
 * GP Limit Dates - Frontend Scripts
 */

( function( $ ) {

	window.GPLimitDates = {

		init: function( formId, fieldId, data ) {

			var $input = $( '#input_' + formId + '_' + fieldId );

			$input.change( function() {
				// Trigger onSelect functionality when the Date field value changes. This can happen when the user
				// manually enters a date value (rather than selecting it from the datepicker) or when the field value
				// is changed programmatically.
				if ( ! $( this ).data( 'gpldIgnoreChange' ) ) {
					var inst = $.datepicker._getInst( $( this )[0] );
					if ( inst ) {
						$( this ).data( 'gpldIgnoreChange', true );
						$.datepicker._get( inst, 'onSelect' ).apply( inst.input[0], [ $( this ).datepicker( 'getDate' ), inst ] );
						$( this ).data( 'gpldIgnoreChange', false );
					}
				}

				GPLimitDates.validateDate( $( this ), fieldId, data );

			} );

			GPLimitDates.validateDate( $input, fieldId, data );

		},

		isDateInRange: function( date, min, max ) {

			if ( GPLimitDates.isValidDateObject( min ) && GPLimitDates.isValidDateObject( max ) ) {
				return date >= min && date <= max;
			} else if ( GPLimitDates.isValidDateObject( min ) ) {
				return date >= min;
			} else if ( GPLimitDates.isValidDateObject( max ) ) {
				return date <= max;
			}

			return true;
		},

		strToTime: function( text, now ) {
			/*discuss at: http://phpjs.org/functions/strtotime/
			 version: 1109.2016
			 original by: Caio Ariede (http://caioariede.com)
			 improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
			 improved by: Caio Ariede (http://caioariede.com)
			 improved by: A. Matías Quezada (http://amatiasq.com)
			 improved by: preuter
			 improved by: Brett Zamir (http://brett-zamir.me)
			 improved by: Mirko Faber
			 input by: David
			 bugfixed by: Wagner B. Soares
			 bugfixed by: Artur Tchernychev
			 bugfixed by: Stephan Bösch-Plepelits (http://github.com/plepe)*/
			var parsed, match, today, year, date, days, ranges, len, times, regex, i, fail = false;

			if ( ! text) {
				return fail;
			}

			// Unecessary spaces
			text = text.replace( /^\s+|\s+$/g, '' )
				.replace( /\s{2,}/g, ' ' )
				.replace( /[\t\r\n]/g, '' )
				.toLowerCase();

			// in contrast to php, js Date.parse function interprets:
			// dates given as yyyy-mm-dd as in timezone: UTC,
			// dates with "." or "-" as MDY instead of DMY
			// dates with two-digit years differently
			// etc...etc...
			// ...therefore we manually parse lots of common date formats
			match = text.match(
			/^(\d{1,4})([\-\.\/\:])(\d{1,2})([\-\.\/\:])(\d{1,4})(?:\s(\d{1,2}):(\d{2})?:?(\d{2})?)?(?:\s([A-Z]+)?)?$/);

			if (match && match[2] === match[4]) {
				if (match[1] > 1901) {
					switch (match[2]) {
						case '-':
							{
								// YYYY-M-D
								if (match[3] > 12 || match[5] > 31) {
									return fail;
								}

								return new Date(match[1], parseInt( match[3], 10 ) - 1, match[5],
								match[6] || 0, match[7] || 0, match[8] || 0, match[9] || 0) / 1000;
						}
						case '.':
							{
								// YYYY.M.D is not parsed by strtotime()
								return fail;
						}
						case '/':
							{
								// YYYY/M/D
								if (match[3] > 12 || match[5] > 31) {
									return fail;
								}

								return new Date(match[1], parseInt( match[3], 10 ) - 1, match[5],
								match[6] || 0, match[7] || 0, match[8] || 0, match[9] || 0) / 1000;
						}
					}
				} else if (match[5] > 1901) {
					switch (match[2]) {
						case '-':
							{
								// D-M-YYYY
								if (match[3] > 12 || match[1] > 31) {
									return fail;
								}

								return new Date(match[5], parseInt( match[3], 10 ) - 1, match[1],
								match[6] || 0, match[7] || 0, match[8] || 0, match[9] || 0) / 1000;
						}
						case '.':
							{
								// D.M.YYYY
								if (match[3] > 12 || match[1] > 31) {
									return fail;
								}

								return new Date(match[5], parseInt( match[3], 10 ) - 1, match[1],
								match[6] || 0, match[7] || 0, match[8] || 0, match[9] || 0) / 1000;
						}
						case '/':
							{
								// M/D/YYYY
								if (match[1] > 12 || match[3] > 31) {
									return fail;
								}

								return new Date(match[5], parseInt( match[1], 10 ) - 1, match[3],
								match[6] || 0, match[7] || 0, match[8] || 0, match[9] || 0) / 1000;
						}
					}
				} else {
					switch (match[2]) {
						case '-':
							{
								// YY-M-D
								if (match[3] > 12 || match[5] > 31 || (match[1] < 70 && match[1] > 38)) {
									return fail;
								}

								year = match[1] >= 0 && match[1] <= 38 ? +match[1] + 2000 : match[1];
								return new Date(year, parseInt( match[3], 10 ) - 1, match[5],
								match[6] || 0, match[7] || 0, match[8] || 0, match[9] || 0) / 1000;
						}
						case '.':
							{
								// D.M.YY or H.MM.SS
								if (match[5] >= 70) {
									// D.M.YY
									if (match[3] > 12 || match[1] > 31) {
										return fail;
									}

									return new Date(match[5], parseInt( match[3], 10 ) - 1, match[1],
									match[6] || 0, match[7] || 0, match[8] || 0, match[9] || 0) / 1000;
								}
								if (match[5] < 60 && ! match[6]) {
									// H.MM.SS
									if (match[1] > 23 || match[3] > 59) {
										return fail;
									}

									today = new Date();
									return new Date(today.getFullYear(), today.getMonth(), today.getDate(),
									match[1] || 0, match[3] || 0, match[5] || 0, match[9] || 0) / 1000;
								}

								// invalid format, cannot be parsed
								return fail;
						}
						case '/':
							{
								// M/D/YY
								if (match[1] > 12 || match[3] > 31 || (match[5] < 70 && match[5] > 38)) {
									return fail;
								}

								year = match[5] >= 0 && match[5] <= 38 ? +match[5] + 2000 : match[5];
								return new Date(year, parseInt( match[1], 10 ) - 1, match[3],
								match[6] || 0, match[7] || 0, match[8] || 0, match[9] || 0) / 1000;
						}
						case ':':
							{
								// HH:MM:SS
								if (match[1] > 23 || match[3] > 59 || match[5] > 59) {
									return fail;
								}

								today = new Date();
								return new Date(today.getFullYear(), today.getMonth(), today.getDate(),
								match[1] || 0, match[3] || 0, match[5] || 0) / 1000;
						}
					}
				}
			}

			// other formats and "now" should be parsed by Date.parse()
			if (text === 'now') {
				return now === null || isNaN( now ) ? new Date()
					.getTime() / 1000 | 0 : now | 0;
			}
			if ( ! isNaN( parsed = Date.parse( text ) )) {
				return parsed / 1000 | 0;
			}
			// Browsers != Chrome have problems parsing ISO 8601 date strings, as they do
			// not accept lower case characters, space, or shortened time zones.
			// Therefore, fix these problems and try again.
			// Examples:
			//   2015-04-15 20:33:59+02
			//   2015-04-15 20:33:59z
			//   2015-04-15t20:33:59+02:00
			if (match = text.match( /^([0-9]{4}-[0-9]{2}-[0-9]{2})[ t]([0-9]{2}:[0-9]{2}:[0-9]{2}(\.[0-9]+)?)([\+-][0-9]{2}(:[0-9]{2})?|z)/ )) {
				// fix time zone information
				if (match[4] == 'z') {
					match[4] = 'Z';
				} else if (match[4].match( /^([\+-][0-9]{2})$/ )) {
					match[4] = match[4] + ':00';
				}

				if ( ! isNaN( parsed = Date.parse( match[1] + 'T' + match[2] + match[4] ) )) {
					return parsed / 1000 | 0;
				}
			}

			date   = now ? new Date( now * 1000 ) : new Date();
			days   = {
				'sun': 0,
				'mon': 1,
				'tue': 2,
				'wed': 3,
				'thu': 4,
				'fri': 5,
				'sat': 6
			};
			ranges = {
				'yea': 'FullYear',
				'mon': 'Month',
				'day': 'Date',
				'hou': 'Hours',
				'min': 'Minutes',
				'sec': 'Seconds'
			};

			function lastNext(type, range, modifier) {
				var diff, day = days[range];

				if (typeof day !== 'undefined') {
					diff = day - date.getDay();

					if (diff === 0) {
						diff = 7 * modifier;
					} else if (diff > 0 && type === 'last') {
						diff -= 7;
					} else if (diff < 0 && type === 'next') {
						diff += 7;
					}

					date.setDate( date.getDate() + diff );
				}
			}

			function process(val) {
				var splt         = val.split( ' ' ), // Todo: Reconcile this with regex using \s, taking into account browser issues with split and regexes
					type         = splt[0],
					range        = splt[1].substring( 0, 3 ),
					typeIsNumber = /\d+/.test( type ),
					ago          = splt[2] === 'ago',
					num          = (type === 'last' ? -1 : 1) * (ago ? -1 : 1);

				if (typeIsNumber) {
					num *= parseInt( type, 10 );
				}

				if (ranges.hasOwnProperty( range ) && ! splt[1].match( /^mon(day|\.)?$/i )) {
					return date['set' + ranges[range]]( date['get' + ranges[range]]() + num );
				}

				if (range === 'wee') {
					return date.setDate( date.getDate() + (num * 7) );
				}

				if (type === 'next' || type === 'last') {
					lastNext( type, range, num );
				} else if ( ! typeIsNumber) {
					return false;
				}

				return true;
			}

			times = '(years?|months?|weeks?|days?|hours?|minutes?|min|seconds?|sec' +
			'|sunday|sun\\.?|monday|mon\\.?|tuesday|tue\\.?|wednesday|wed\\.?' +
			'|thursday|thu\\.?|friday|fri\\.?|saturday|sat\\.?)';
			regex = '([+-]?\\d+\\s' + times + '|' + '(last|next)\\s' + times + ')(\\sago)?';

			match = text.match( new RegExp( regex, 'gi' ) );
			if ( ! match) {
				return fail;
			}

			for (i = 0, len = match.length; i < len; i++) {
				if ( ! process( match[i] )) {
					return fail;
				}
			}

			// ECMAScript 5 only
			// if (!match.every(process))
			//    return false;

			return (date.getTime() / 1000);
		},

		/**
		 * Get modified date based on provided modifier.
		 *
		 * @param modifier
		 * @param date
		 * @param data
		 * @param fieldId
		 * @returns {Date}
		 */
		getModifiedDate: function( modifier, date, data, fieldId ) {
			var modifiedDate = new Date( GPLimitDates.strToTime( modifier, date.getTime() / 1000 ) * 1000 );
			return gform.applyFilters( 'gpld_modified_date', modifiedDate, modifier, date, data, fieldId );
		},

		getDateValue: function( value, key, fieldId, formId, data ) {

			var date      = null,
				isFieldId = GPLimitDates.isNumeric( value );

			if ( value == '{today}' ) {
				date = new Date();
			} else if ( isFieldId ) {
				var $input = $( '#input_' + formId + '_' + value );
				if ( $input.val() ) {
					date = GPLimitDates.parseDate( $input.val(), value, data );
				} else {
					// This introduces the potential for an infinite loop if two or more fields are configured with minDates dependent on each other.
					// Not sure there is elegant way to account for this...
					date = GPLimitDates.getDateValue( data[ value ][ key ], key, value, formId, data );
				}
			} else {
				date = new Date( value );
			}

			var modifier = key == 'minDate' ? data[ fieldId ].minDateMod : data[ fieldId ].maxDateMod;
			if ( modifier ) {
				date = GPLimitDates.getModifiedDate( modifier, date, data, fieldId );
			}

			// only convert to server time for {today}
			if ( value == '{today}' ) {
				date = GPLimitDates.convertDateToServerTime( date, GPLimitDatesData.serverTimezoneOffset );
				date.setHours( 0, 0, 0, 0 );
			}

			return date;
		},

		setMinMaxDate: function( $input, fieldId, formId, data ) {

			var minFieldIds = data[ fieldId ].setsMinDateFor,
				maxFieldIds = data[ fieldId ].setsMaxDateFor;

			if ( minFieldIds ) {
				$.each( minFieldIds, function( index, minFieldId ) {
					GPLimitDates.setMinDate( $input.datepicker( 'getDate' ), minFieldId, formId, data );
				} );
			}

			if ( maxFieldIds ) {
				$.each( maxFieldIds, function( index, maxFieldId ) {
					GPLimitDates.setMaxDate( $input.datepicker( 'getDate' ), maxFieldId, formId, data );
				} );
			}

		},

		setMinDate: function( selectedDate, fieldId, formId, data ) {

			if ( ! selectedDate ) {
				return;
			}

			var isInline = data[ fieldId ]['inlineDatepicker'],
				$input   = isInline ? $( '#datepicker_' + formId + '_' + fieldId ) : $( '#input_' + formId + '_' + fieldId ),
				modifier = data[ fieldId ].minDateMod,
				date     = selectedDate;

			if ( modifier ) {
				date = GPLimitDates.getModifiedDate( modifier, date, data, fieldId );
			}

			// Make sure we're setting the minimum date to a valid one
			if ( !isNaN( date ) ) {
				while ( GPLimitDates.isDateShown( date, data, fieldId )[0] === false ) {
					date.setDate( date.getDate() + 1 );
				}
			}

			$input.datepicker( 'option', 'minDate', date );
			$input.datepicker( 'refresh' );

			if ( isInline ) {
				$( $input.datepicker( 'option', 'altField' ) ).change();
			}

			/**
			 * Do something after the minimum date has been set for a Date field.
			 *
			 * @since 1.0.19
			 *
			 * @param {jQuery} $input       The current Date input.
			 * @param {Date}   date         The minimum date that was set.
			 * @param {Date}   selectedDate The date selected in another Date field on which the current Date field's minimum date is dependent.
			 * @param {int}    fieldId      The ID of the current Date field.
			 * @param {int}    formId       The ID of the current form.
			 * @param {object} data         All Limit Dates data for the current form.
			 */
			gform.doAction( 'gpld_after_set_min_date', $input, date, selectedDate, fieldId, formId, data );

			return date;
		},

		setMaxDate: function( selectedDate, fieldId, formId, data ) {

			if ( ! selectedDate ) {
				return;
			}

			var isInline = data[ fieldId ]['inlineDatepicker'],
				$input   = isInline ? $( '#datepicker_' + formId + '_' + fieldId ) : $( '#input_' + formId + '_' + fieldId ),
				modifier = data[ fieldId ].maxDateMod,
				date     = selectedDate;

			if ( modifier ) {
				date = GPLimitDates.getModifiedDate( modifier, new Date( date ), data, fieldId );
			}

			$input.datepicker( 'option', 'maxDate', date );
			$input.datepicker( 'refresh' );

			if ( isInline ) {
				$( $input.datepicker( 'option', 'altField' ) ).change();
			}

			/**
			 * Do something after the maximum date has been set for a Date field.
			 *
			 * @since 1.1.4
			 *
			 * @param {jQuery} $input       The current Date input.
			 * @param {Date}   date         The maximum date that was set.
			 * @param {Date}   selectedDate The date selected in another Date field on which the current Date field's maximum date is dependent.
			 * @param {int}    fieldId      The ID of the current Date field.
			 * @param {int}    formId       The ID of the current form.
			 * @param {object} data         All Limit Dates data for the current form.
			 */
			gform.doAction( 'gpld_after_set_max_date', $input, date, selectedDate, fieldId, formId, data );

			return date;
		},

		isDateShown: function( date, data, fieldId ) {

			if ( ! date ) {
				return [ true ];
			}

			var daysOfWeek       = data[ fieldId ].daysOfWeek,
				exceptions       = data[ fieldId ].exceptions,
				dateString       = $.datepicker.formatDate( 'mm/dd/yy', date ),
				day              = date.getDay(),
				isValidDayOfWeek = typeof daysOfWeek != 'object' || daysOfWeek.length <= 0 || daysOfWeek.indexOf( day ) != -1,
				shouldDisplay    = isValidDayOfWeek && ! data[ fieldId ].disableAll;

			if ( exceptions.indexOf( dateString ) != -1 ) {

				// if date is excepted, we'll need to check if it is within the date range to determine if it should display by default
				var inDateRange = GPLimitDates.isDateInRange( date, $( this ).datepicker( 'option', 'minDate' ), $( this ).datepicker( 'option', 'maxDate' ) );
				shouldDisplay   = inDateRange && isValidDayOfWeek;

				switch ( data[ fieldId ].exceptionMode ) {
					case 'enable':
						shouldDisplay = true;
						break;
					case 'disable':
						shouldDisplay = false;
						break;
					default:
						shouldDisplay = ! shouldDisplay;
						break;
				}

			}

			return [ shouldDisplay ];
		},

		validateDate: function( $input, fieldId, data ) {

			var date          = $input.datepicker( 'getDate' ),
				formattedDate = $.datepicker.formatDate( $input.datepicker( 'option', 'dateFormat' ), date ),
				dateVal       = $input.val();

			var $container = $input.parents( '.ginput_container' ),
				$error     = $container.find( 'span.gpld-error-message' );

			if ( ! date ) {
				$error.hide();
				$input.removeClass( 'gpld-error' );
				return;
			}

			// force $input to have a correctly formatted date
			if ( dateVal != formattedDate ) {
				$input.val( formattedDate );
			}

			var isValid = ! date || ( GPLimitDates.isDateShown( date, data, fieldId )[0] && GPLimitDates.isDateInRange( date, $input.datepicker( 'option', 'minDate' ), $input.datepicker( 'option', 'maxDate' ) ) );

			if ( ! isValid ) {

				if ( $error.length <= 0 ) {
					/**
					 * Filter to modify the Invalid Date string that's show beside the date picker if an invalid date
					 * is chosen.
					 *
					 * @since 1.0.22
					 *
					 * @param string Invalid date error message
					 * @param string|number Field ID
					 * @param jQuery Object of the Input
					 * @param Date Selected date
					 * @param Object Settings pertaining to GP Limit Dates for the current field
					 *
					 * @type string
					 */
					var invalidDateStr = window.gform.applyFilters( 'gpld_invalid_date_error', GPLimitDatesData.strings.invalidDate, fieldId, $input, date, data[fieldId] );

					var iconSvgUrl = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTc5MiIgaGVpZ2h0PSIxNzkyIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnN2Zz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPgogPGc+CiAgPHRpdGxlPkxheWVyIDE8L3RpdGxlPgogIDxwYXRoIGZpbGw9IiM3OTAwMDAiIGQ9Im0xNDkwLDEzMjJxMCw0MCAtMjgsNjhsLTEzNiwxMzZxLTI4LDI4IC02OCwyOHQtNjgsLTI4bC0yOTQsLTI5NGwtMjk0LDI5NHEtMjgsMjggLTY4LDI4dC02OCwtMjhsLTEzNiwtMTM2cS0yOCwtMjggLTI4LC02OHQyOCwtNjhsMjk0LC0yOTRsLTI5NCwtMjk0cS0yOCwtMjggLTI4LC02OHQyOCwtNjhsMTM2LC0xMzZxMjgsLTI4IDY4LC0yOHQ2OCwyOGwyOTQsMjk0bDI5NCwtMjk0cTI4LC0yOCA2OCwtMjh0NjgsMjhsMTM2LDEzNnEyOCwyOCAyOCw2OHQtMjgsNjhsLTI5NCwyOTRsMjk0LDI5NHEyOCwyOCAyOCw2OHoiIGlkPSJzdmdfMSIvPgogPC9nPgo8L3N2Zz4=';
					$error         = $( '<span class="gpld-error-message" style="background: transparent url( \'' + iconSvgUrl + '\' ) no-repeat center left; background-size: 18px; padding-left: 19px; color: #790000; font-size: 12px;">' +
					invalidDateStr + '</span>' );
					$container.append( $error );
				}

				$error.show();
				$input.addClass( 'gpld-error' );

			} else {

				$error.hide();
				$input.removeClass( 'gpld-error' );

			}

		},

		convertDateToServerTime: function( date, offset ) {

			var serverTimezoneOffset = offset,
				userTime             = date,
				userTimezoneOffset   = userTime.getTimezoneOffset();

			var utcTime = new Date( userTime );
			utcTime.setHours( utcTime.getHours() + Math.floor( userTimezoneOffset / 60 ) );

			var serverTime = new Date( utcTime );
			serverTime.setHours( serverTime.getHours() + Math.floor( serverTimezoneOffset / 60 ) );

			return serverTime;
		},

		isNumeric: function( value ) {
			return ! isNaN( parseFloat( value ) ) && isFinite( value );
		},

		isValidDateObject: function( date ) {
			return Object.prototype.toString.call( date ) === '[object Date]' && ! isNaN( date.getTime() );
		},

		/**
		 * Parse a JS date object from a date value based on a specific field's properties. Based on code from the
		 * GWDates class.
		 */
		parseDate: function( date, fieldId, data ) {

			// date will be undefined if date field does not exist; this can occur if date field mapped to min/max date settings is deleted
			if ( typeof date == 'undefined' ) {
				return date;
			}

			try {
				var timestamp  = false,
					formatBits = data[fieldId].dateFormat.split( '_' ),
					mdy        = formatBits[0],
					separator  = formatBits[1] ? formatBits[1] : 'slash',
					sepChars   = {slash: '/', dot: '.', dash: '-'},
					sepChar    = sepChars[separator];

				var dateArr     = date.split( sepChar ),
					month       = dateArr[mdy.indexOf( 'm' )],
					day         = dateArr[mdy.indexOf( 'd' )],
					year        = dateArr[mdy.indexOf( 'y' )],
					missingData = ! month || ! day || ! year;

				date = new Date( year, month - 1, day, 0, 0, 0, 0 );

				return date;
			} catch (err) {
			} // Field was probably deleted, fail silently to allow rest of the form to render.
		},

		formatDate: function( date, fieldId, data ) {

			var formatBits = data[ fieldId ].dateFormat.split( '_' ),
				separator  = formatBits[1] ? formatBits[1] : 'slash',
				sepChars   = { slash: '/', dot: '.', dash: '-' },
				sepChar    = sepChars[ separator ],
				mdy        = formatBits[0].split( '' ).join( sepChar ), // example: 'mdy' => 'm-d-y'
				rawMonth   = date.getMonth() + 1 + '',
				month      = '00'.substring( 0, 2 - rawMonth.length ) + rawMonth,
				day        = date.getDate(),
				year       = date.getFullYear(),
				formatted  = mdy.replace( 'm', month ).replace( 'd', day ).replace( 'y', year );

			return formatted;
		},

		setInlineDate: function( $input, fieldId, data ) {

			var currentDate = new Date(),
				defaultDate = $input.val() ? $input.val() : GPLimitDates.formatDate( currentDate, fieldId, data );

			// Default date is always selected; if not default date is specified, current date is used. Update
			// the $input value so conditional logic and Copy Cat will work.
			$input.val( defaultDate ).change();

		},

		// Deprecated in favor of the overridden _attachDatepicker method on the datepicker below.
		initDisabledDatepicker : function( _deprecated ) {
			// deprecated - noop. Kept as some other Perks such as Populate Anything call this method.
		}

	};

	gform.addFilter( 'gform_datepicker_options_pre_init', function( optionsObj, formId, fieldId ) {

		if ( typeof formId == 'undefined' ) {
			return optionsObj;
		}

		var data = window[ 'GPLimitDatesData' + formId ];

		if ( ! data || ! data[ fieldId ] ) {
			return optionsObj;
		}

		var onClose    = optionsObj.onClose,
			beforeShow = optionsObj.beforeShow,
			onSelects  = [], // Build an array of onSelect functions; combine them into a single function before returning.
			$dp        = $( '#ui-datepicker-div' );

		/**
		 * Regular Datepickers generate a #ui-datepicker-div which hosts the actual calendar. It is hidden by default.
		 * When a date is selected, our onSelect functionality waits for this div to be hidden before populating the
		 * min/max date for any Date field that may be dependent on this Date field.
		 *
		 * When there are only Inline Datepickers on a form, the #ui-datepicker-div is still generated but, while it is
		 * empty, it is not hidden. This causes the functionality described above to fail. The div is technically visible
		 * and therefor the min/max dates are not set.
		 *
		 * Let's hide this div by default to avoid this issue.
		 */
		$dp.hide();

		// Check for an existing onSelect func.
		if ( optionsObj.onSelect ) {
			onSelects.push( optionsObj.onSelect );
		}

		// modify optionsObj
		$.each( data[ fieldId ], function( key, value ) {

			if ( ! value || value.length <= 0 ) {
				return true; // continue
			}

			switch ( key ) {
				case 'inlineDatepicker':
					var $input             = $( '#input_{0}_{1}'.format( formId, fieldId ) ),
						currentDate        = new Date(),
						defaultValue       = $input.val(),
						defaultDate        = defaultValue ? defaultValue : GPLimitDates.formatDate( currentDate, fieldId, data );
					optionsObj.altField    = '#' + $input.attr( 'id' );
					optionsObj.defaultDate = defaultDate;
					// Default date is always selected; if no default date is specified, current date is used. Update
					// the $input value so conditional logic and Copy Cat will work.
					optionsObj.beforeShow = function( input, inst ) {
						beforeShow( input, inst );
						var $inline    = $( '#datepicker_{0}_{1}'.format( formId, fieldId ) );
						var dateString = GPLimitDates.formatDate( $inline.datepicker( 'getDate' ), fieldId, data );
						if ( ! defaultValue || $input.val() !== dateString ) {
							$input.val( dateString ).change();
						}
					}
					// Inline datepicker fails to show selected date when rendered while hidden via conditional logic;
					// force datepicker to reset the selected date after conditional logic is evaluated.
					$( document ).on( 'gform_post_conditional_logic', function() {
						var $inline = $( '#datepicker_{0}_{1}'.format( formId, fieldId ) );
						$inline.datepicker( 'setDate', $inline.datepicker( 'getDate' ) );
					} );
					break;
				case 'minDate':
					optionsObj.minDate = GPLimitDates.getDateValue( value, key, fieldId, formId, data );
					// Make sure the minimum date is selectable
					if ( !isNaN( optionsObj.minDate ) ) {
						while ( GPLimitDates.isDateShown( optionsObj.minDate, data, fieldId )[0] === false ) {
							optionsObj.minDate.setDate( optionsObj.minDate.getDate() + 1 );
						}
					}
					break;
				case 'maxDate':
					optionsObj.maxDate = GPLimitDates.getDateValue( value, key, fieldId, formId, data );
					break;
				case 'setsMinDateFor':
				case 'setsMaxDateFor':
					// Note: this previously used onClose(); it was updated so that it would work for inline datepickers
					// as well, since they never trigger the onClose event.
					onSelects.push( function() {

						if ( typeof onSelect == 'function' ) {
							onSelect();
						}

						var $input       = $( this ),
							isOnlyInline = true;

						if ( $.isArray( value ) ) {
							$.each( value, function( i ) {
								if ( ! data[ value[ i ] ]['inlineDatepicker'] ) {
									isOnlyInline = false;
									return false;
								}
							} );
						} else {
							isOnlyInline = data[ value ]['inlineDatepicker'];
						}

						var closeInterval = setInterval( function()  {
							if ( ! $dp.is( ':visible' ) ) {
								GPLimitDates.setMinMaxDate( $input, fieldId, formId, data );
								clearInterval( closeInterval );
							}
						}, isOnlyInline ? 0 : 200 );

					} );
					break;
				case 'daysOfWeek':
				case 'exceptions':
					optionsObj.beforeShowDay = function( date ) {
						return GPLimitDates.isDateShown( date, data, fieldId );
					};
					break;
			}
		} );

		// Build our custom onSelect function which calls each onSelect function specified above and any default onSelect
		// passed to this filter.
		optionsObj.onSelect = function( dateText, inst ) {
			for ( var i = 0; i < onSelects.length; i++ ) {
				if ( typeof onSelects[ i ] == 'function' ) {
					onSelects[ i ].call( this, dateText, inst );
				}
			}
			// If any onSelect option is passed, we must trigger the $input change event manually; not sure why.
			var $input = $( this ).is( 'input' ) ? $( this ) : $( $( this ).datepicker( 'option', 'altField' ) );
			if ( ! $input.data( 'gpldIgnoreChange' ) ) {
				$input
					.data( 'gpldIgnoreChange', true )
					.change()
					.data( 'gpldIgnoreChange', false );
			}
		};

		// initialize related functionality
		GPLimitDates.init( formId, fieldId, data );

		return optionsObj;
	} );

	/**
	 * jQuery Datepicker doesn't fire the beforeShow method for inline datepickers. Let's override datepicker class with
	 * our own version that triggers the beforeShow method.
	 *
	 * Solution provided by StackOverflow: https://stackoverflow.com/a/12286320/227711
	 */
	$.extend( $.datepicker, {

		// Reference the original function so we can override it and call it later.
		_origInlineDatepicker: $.datepicker._inlineDatepicker,
		_origAttachDatepicker: $.datepicker._attachDatepicker,

		// Override the _inlineDatepicker method.
		_inlineDatepicker: function (target, inst) {
			this._origInlineDatepicker( target, inst );
			var beforeShow = $.datepicker._get( inst, 'beforeShow' );
			if ( beforeShow ) {
				beforeShow.apply( target, [ target, inst ] );
			}
		},

		/// Override the _attachDatepicker method so we can disable the Datepicker using the class.
		_attachDatepicker: function ( target, settings ) {
			var $input = $(target);

			if ($input.hasClass('gpro-disabled-datepicker')) {
				settings.disabled = true;
			}

			$.datepicker._origAttachDatepicker( target, settings );

			// If the datepicker is disabled, jQuery UI will also disable the input. We don't want that as we already
			// have the readonly attribute set on it.
			$input.prop('disabled', false);
		}
	} );

} )( jQuery );
