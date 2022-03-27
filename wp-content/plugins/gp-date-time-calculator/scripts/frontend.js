( function( $ ) {

	window.GWDTCalc = function( args ) {

		var self = this;

		// copy all args to current object: (list expected props)
		for ( prop in args ) {
			if ( args.hasOwnProperty( prop ) ) {
				self[prop] = args[prop];
			}
		}

		self.rebind = function (_, formId) {
			if (formId !== self.formId) {
				return;
			}

			$.each(self.elementsToBind, function(formId, elements) {
				$.each(elements, function(_, toBind) {
					$inputs = $();

					$.each(toBind.dateFields, function(_, dateField) {
						$inputs = $inputs.add( $( '#field_' + formId + '_' + dateField.id ).find( 'input, select' ) );
					});

					self.bindEvents( $inputs, toBind.calcField, toBind.calcObj, true );
				});
			});
		};

		self.init = function() {

			self.elementsToBind = {};

			/**
			 * Reset on page change to rebind
			 */
			jQuery( document ).on( 'gform_post_render', self.rebind );

			gform.addFilter( 'gform_calculation_formula', function( formula, calcField, formId, calcObj ) {

				if (formId != self.formId) {
					return formula;
				}

				var calcFieldId = calcField.field_id,
					// Parse weekdays/weekendDays merge tags first so nested Date merge tags can be processed accurately.
					customMatches = GFMergeTag.parseMergeTags( formula, /{(weekdays|weekendDays):(?:{.*?:([0-9]+)}),(?:{.*?:([0-9]+)})}/ );

				$.each( customMatches, function( index, match ) {

					var fullMatch  = match[0],
						customType = match[1],
						startDate  = self.getDateFieldValue( self.getDateField( calcFieldId, match[2] ), self.formId ),
						endDate    = self.getDateFieldValue( self.getDateField( calcFieldId, match[3] ), self.formId ),
						value      = 0;

					if ( startDate > 0 && endDate > 0 ) {

						switch ( customType ) {
							case 'weekdays':
								value = self.calcWeekDays( new Date( startDate * 1000 ), new Date( endDate * 1000 ) );
								break;
							case 'weekendDays':
								value = self.calcWeekendDays( new Date( startDate * 1000 ), new Date( endDate * 1000 ) );
								break;
							default:
								break;
						}

					}

					// Wrap value in parentheses to handle double-negatives
					formula = formula.replace( fullMatch, '(' + value + ')' );

				} );

				var matches = typeof window.GFMergeTag ? GFMergeTag.parseMergeTags( formula ) : getMatchGroups( formula, calcObj.patt );
				if ( matches.length > 0 ) {
					var calcFieldData = self.getCalcFieldData( calcFieldId );
				}

				$.each( matches, function( index, match ) {

					var fullMatch = match[0],
						inputId   = match[1],
						modifier  = match[4],
						fieldId   = parseInt( inputId ),
						dateField = self.getDateField( calcFieldId, fieldId );

					if ( ! dateField ) {
						return; // continue
					}

					// Get all inputs sans GP Read Only's hidden capture inputs.
					var $inputs = $( '#field_' + formId + '_' + dateField.id ).find( 'input, select' ).not( '[id*="gwro_hidden_capture"]' ),
						value   = self.getDateFieldValue( dateField, formId, $inputs );

					if ( modifier ) {
						value = self.processModifier( value, modifier );
					}

					if ( modifier != 'age' ) {
						value = self.convertToUnit( value, calcFieldData.unit );
					}

					// Wrap value in parentheses to handle double-negatives
					formula = formula.replace( fullMatch, '(' + value + ')' );

					if ( ! (formId in self.elementsToBind) ) {
						self.elementsToBind[formId] = {};
					}

					// Do not process date fields already bound to events
					if ( self.elementsToBind[formId][calcFieldId] && self.elementsToBind[formId][calcFieldId].dateFields.indexOf( dateField ) > -1 ) {
						return;
					}

					self.bindEvents( $inputs, calcField, calcObj, false );

					if ( self.elementsToBind[formId][calcFieldId] ) {
						self.elementsToBind[formId][calcFieldId].dateFields.push( dateField );
						return;
					}

					/**
					 * Mark field to be bound on page change.
					 *
					 * @type {{calcField: *, dateField: *, calcObj: *}}
					 */
					self.elementsToBind[formId][calcFieldId] = {
						calcField: calcField,
						calcObj: calcObj,
						dateFields: [dateField],
					};

				} );

				var staticMergeTags = [ 'today', 'now' ];

				$.each( staticMergeTags, function( index, mergeTag ) {

					if ( formula.indexOf( '{' + mergeTag + '}' ) === -1 ) {
						return;
					}

					var value         = 0,
						calcFieldData = self.getCalcFieldData( calcFieldId );

					if ( ! calcFieldData ) {
						return;
					}

					switch ( mergeTag ) {
						case 'today':
							value = self.getMomentToday( { hour: 0, minute: 0, seconds: 0 } ).format( 'X' );
							value = self.convertToUnit( value, calcFieldData.unit );
							break;
						case 'now':
							value = self.getMoment( undefined, false ).format( 'X' );
							value = self.convertToUnit( value, calcFieldData.unit );
							break;
						default:
							break;
					}

					var regex = new RegExp( '{' + mergeTag + '}', 'g' );

					// Wrap value in parentheses to handle double-negatives
					formula = formula.replace( regex, '(' + value + ')' );

				} );

				return formula;
			}, 5 ); /* Replace our merge tags first; give other plugins plenty of options to fire after us. */

			// bit of a hack but an easy way to force calculations to run again now that our filters are in place
			$( document ).trigger( 'gform_post_conditional_logic' );

		};

		/**
		 * @param time
		 * @param keepLocalTime We haven't figured out a rule behind toggling this keepLocalTime. We need to figure out
		 * a rule for using this.
		 *
		 * @param sourceTimeFormat
		 * @return {*|number}
		 */
		self.getMoment = function ( time, keepLocalTime, sourceTimeFormat ) {

			if (typeof keepLocalTime === 'undefined') {
				keepLocalTime = true;
			}

			var offset = parseInt( GPDTC.GMT_OFFSET );

			if (offset === 0) {
				return moment.utc( time, sourceTimeFormat );
			}

			return moment( time, sourceTimeFormat ).utcOffset( offset, keepLocalTime );

		};

		self.onChangeFactory = function(calcField, calcObj) {
			return function() {
				calcObj.bindCalcEvent( $( this ).attr( 'id' ), calcField, self.formId, 0 );
			};
		};

		self.bindEvents = function( $inputs, calcField, calcObj, rebind ) {
			// Only disable event listeners when re-binding. Current behavior causes multiple fields
			// using the same calculation to only output to the last field. See HS#27257
			if ( rebind ) {
				$inputs.off( 'change.gpdtc' );
				$inputs.not( 'select' ).off( 'keyup.gpdtc' );
			}

			$inputs.on( 'change.gpdtc', self.onChangeFactory( calcField, calcObj ) );
			$inputs.not( 'select' ).on( 'keyup.gpdtc', self.onChangeFactory( calcField, calcObj ) );
		};

		self.getCalcFieldData = function( fieldId ) {

			var calcFieldData = false;

			$.each( self.dateFieldsData, function( calcFieldId, _calcFieldData ) {
				if ( calcFieldId == fieldId ) {
					calcFieldData = _calcFieldData;
					return false;
				}
			} );

			return calcFieldData;
		};

		self.getDateField = function( calcFieldId, fieldId ) {

			var dateField     = false,
				calcFieldData = self.getCalcFieldData( calcFieldId );

			if ( ! calcFieldData ) {
				return false;
			}

			$.each( calcFieldData.fields, function( i, _dateField ) {
				if ( fieldId == _dateField.id ) {
					dateField = _dateField;
					return false;
				}
			} );

			return dateField;
		};

		self.getDateFieldValue = function( dateField, formId, $inputs ) {

			var isVisible = window['gf_check_field_rule'] ? gf_check_field_rule( formId, dateField.id, true, '' ) == 'show' : true,
				$inputs   = typeof $inputs == 'undefined' ? $( '#field_' + formId + '_' + dateField.id ).find( 'input, select' ) : $inputs,
				value     = 0;

			if ( $inputs.length <= 0 || ! isVisible ) {
				return value;
			}

			switch ( dateField.type ) {

				case 'date':

					var formatBits = dateField.dateFormat.split( '_' ),
						mdy        = formatBits[0];/*,
						separator  = formatBits.length > 1 ? formatBits[1] : 'slash';*/

					switch ( dateField.dateType ) {
						case 'datefield':
						case 'datedropdown':
							var month       = $inputs.eq( mdy.indexOf( 'm' ) ).val(),
								day         = $inputs.eq( mdy.indexOf( 'd' ) ).val(),
								year        = $inputs.eq( mdy.indexOf( 'y' ) ).val(),
								missingData = ! month || ! day || ! year,
								datetime    = missingData ? false : self.getMoment( month + '/' + day + '/' + year, false, 'M/D/YYYY' );
							break;
						case 'datepicker':
							var momentFormat = mdy.toUpperCase().split( '' ).join( '/' ).replace( 'Y', 'YYYY' ),
								datetime     = $inputs.val() ? self.getMoment( $inputs.val(), true, momentFormat ) : false;
							break;
						default:
							break;
					}

					// 'X' is for Unix Timestamp in moment.js
					var timestamp = datetime === false ? 0 : datetime.format( 'X' );

					break;

				case 'time':

					var timestamp   = self.getMoment( undefined, false ).format( 'X' ),
						hour        = parseInt( $inputs.eq( 0 ).val() ),
						min         = $inputs.eq( 1 ).val(),
						ampm        = $inputs.eq( 2 ).val(),
						missingData = ! hour || ! min;

					if ( ! missingData ) {

						if (ampm === 'am' && hour === 12) {
							hour = 0;
						}

						if (ampm === 'pm' && hour === 12) {
							hour = 12;
						}

						if (ampm === 'pm' && hour !== 12) {
							hour += 12;
						}

						datetime  = self.getMomentToday( { hour: hour, minute: parseInt( min ) } );
						timestamp = datetime.toDate().getTime() / 1000;

					}

					break;

				default:
					break;

			}

			value = timestamp;

			return value;
		};

		self.getMomentToday = function (timeObj) {
			var now = self.getMoment( undefined, false );

			return self.getMoment( $.extend( {}, {year: now.year(), month: now.month(), day: now.date()}, timeObj ) );
		};

		self.processModifier = function( value, modifier ) {

			switch ( modifier ) {
				case 'age':

					if ( Math.abs( value ) > 0 ) {
						// Let moment.js handle the calculation
						value = moment().diff( moment( new Date( value * 1000 ) ), 'years' );
					}

					break;
				default:
					break;
			}

			return value;
		};

		self.convertToUnit = function( value, unit ) {
			//return value / unit.unit;
			if ( parseInt( value ) === 0 ) {
				return 0;
			}

			var mo   = self.getMoment( value * 1000 ),
				diff = mo.diff( '1970-01-01', unit.slug, true );

			return diff;
		};

		self.calcWeekDays = function( startDate, endDate ) {
			return self.calcDays( startDate, endDate, 'weekdays' );
		};

		self.calcWeekendDays = function( startDate, endDate ) {
			return self.calcDays( startDate, endDate, 'weekendDays' );
		};

		self.calcDays = function( startDate, endDate, mode ) {

			if ( endDate < startDate ) {
				return 0;
			}

			if ( typeof mode == 'undefined' ) {
				mode = 'weekdays';
			}

			// start just after midnight
			startDate.setHours( 0, 0, 0, 1 );

			// end just before midnight
			endDate.setHours( 23, 59, 59, 999 );

			var weekdays    = 0,
				weekendDays = 0;

			while ( startDate < endDate ) {
				if ( startDate.getDay() === 0 || startDate.getDay() == 6 ) {
					weekendDays++;
				} else {
					weekdays++;
				}
				startDate.setDate( startDate.getDate() + 1 );
			}

			var days = mode == 'weekdays' ? weekdays : weekendDays;

			return Math.round( days );
		};

		self.init();

	}

} )( jQuery );
