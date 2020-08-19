/**
 * GP Copy Cat JS
 */
( function( $ ) {

	window.gwCopyObj = function( args ) {

		var self = this;

		// copy all args to current object: formId, fields, overwrite, overwriteOnInit
		for( prop in args ) {
			if( args.hasOwnProperty( prop ) )
				self[prop] = args[prop];
		}

		self.init = function() {

			var $formWrapper = $( '#gform_wrapper_{0}'.format( self.formId ) );

			$formWrapper.on( 'click.gpcopycat', '.gwcopy input[type="checkbox"]', function() {
				if( $( this ).is( ':checked' ) ) {
					self.copyValues( this );
				} else {
					self.clearValues( this );
				}
			} );

			$formWrapper.on( 'change.gpcopycat', '.gwcopy input:not(:checkbox), .gwcopy textarea, .gwcopy select', function() {
				self.copyValues( this );
			} );

			$formWrapper.find( '.gwcopy' ).find( 'input, textarea, select' ).each( function() {
				if( ! $( this ).is( ':checkbox, :radio' ) ) {
					self.copyValues( this, self.overwriteOnInit );
				} else if( $( this ).is( ':checked' ) ) {
					self.copyValues( this, self.overwriteOnInit );
				}
			} );

			gform.addAction( 'gform_list_post_item_delete', function( $container ) {
				if( $container.parents( 'li.gwcopy' ).length > 0 ) {
					self.clearValues( $container );
					self.copyValues( $container );
				}
			} );

			$formWrapper.data( 'GPCopyCat', self );

		};

		self.copyValues = function( elem, isOverwrite, forceEmptyCopy ) {

			var fieldId = gf_get_input_id_by_html_id( $( elem ).parents( 'li.gwcopy' ).attr( 'id' ) ),
				fields  = self.fields[ fieldId ];

			isOverwrite    = typeof isOverwrite !== 'undefined' ? isOverwrite : self.overwrite;
			forceEmptyCopy = typeof forceEmptyCopy !== 'undefined' ? forceEmptyCopy : isOverwrite;

			for( var i = 0; i < fields.length; i++ ) {

				var field             = fields[i],
					sourceFieldId     = field['source'],
					targetFieldId     = field['target'],
					sourceGroup       = self.getFieldGroup( field, 'source' ),
					targetGroup       = self.getFieldGroup( field, 'target' ),
					isListToList      = self.isListField( sourceGroup ) && self.isListField( targetGroup ),
					sourceValues      = self.getGroupValues( sourceGroup, 'source', {
						sort:          ! isListToList && self.isListField( targetGroup ) ? self.getGroupValues( targetGroup, 'target', { isListToList: isListToList, sourceInputId: targetFieldId } ) : false,
						isListToList:  isListToList,
						sourceInputId: sourceFieldId,
						targetInputId: targetFieldId
					} );

				// Add new rows for List field - if - we have more than one value to populate - and - our target is a List field.
				if( self.isListField( targetGroup ) ) {

					var targetRowCount   = targetGroup.parents( '.ginput_list' ).find( '.gfield_list_group' ).length/* : targetGroup.length*/,
						sourceRowCount   = self.isListField( sourceGroup ) ? sourceGroup.parents( '.ginput_list' ).find( '.gfield_list_group' ).length : sourceGroup.length,
						//targetInputIndex = self.getListInputIndex( targetFieldId, true ),
						//perRow           = targetGroup.parents( '.ginput_list' ).find( '.gfield_list_group:first-child .gfield_list_cell' ).length,
						rowsRequired     = Math.floor( ( sourceRowCount - targetRowCount ) ),// / ( targetInputIndex.column ? 1 : perRow ) );
						maxRows          = self.getMaxRowCount( targetGroup );

					if( rowsRequired < 0 && targetRowCount > 1 ) {
						// Remove rows from target List field that do not have corresponding source values.
						targetGroup.each( function() {
							var _sourceValues = getObjectValues( sourceValues );
							if( $.inArray( $( this ).val(), _sourceValues ) === -1 && $( this ).parents( '.gfield_list' ).find( 'tbody tr' ).length > 1 ) {
								gformDeleteListItem( $( this ), maxRows );
							}
						} );
					} else if( rowsRequired > 0 ) {
						for( var j = 0; j < rowsRequired; j++ ) {
							if( maxRows > 0 && targetRowCount + j + 1 > maxRows ) {
								break;
							}
							gformAddListItem( targetGroup[ targetGroup.length - 1 ], self.getMaxRowCount( targetGroup ) );
						}
					}

					// Re-fetch the target group so we'll loop through any newly added inputs.
					targetGroup = self.getFieldGroup( field, 'target' );

				}

				targetGroup.each( function( i ) {

					var $targetElem    = $( this ),
						isCheckable    = $targetElem.is( ':checkbox, :radio' ),
						index          = isListToList ? self.getListInputIndex( $targetElem ) : i,
						hasSourceValue = isCheckable || sourceValues[ index ] || ( $.isArray( sourceValues ) /* @todo list field hac */ && sourceValues.join( ' ' ) ),
						hasValue       = false,
						value          = null;

					if( isCheckable ) {
						// NOTE: this is how this should technically work but I don't think anyone is targeting individual
						// inputs for a checkbox or radio button field... so I'm going to wait until I have a real world
						// use case before fussing with this more.
						// if( $targetElem.is( ':radio' ) ) {
						// 	hasValue = targetGroup.is( ':checked' );
						// } else {
						// 	hasValue = $targetElem.is( ':checked' );
						// }

						// for now, if field is checkable, we consider that the "field" has a value if any input
						// is checked
						hasValue = targetGroup.is( ':checked' );
					} else {
						hasValue = $targetElem.val();
					}

					// if overwrite is false and a value exists, skip
					if( ! isOverwrite && hasValue ) {
						return true;
					}

					// if there is no source value for this element, skip
					if( ! hasSourceValue && ! forceEmptyCopy ) {
						return true;
					}

					if( self.isListField( targetGroup ) ) {
						if( isInputSpecific( targetFieldId ) ) {
							value = sourceValues[ i ];
						} else {
							value = sourceValues[ index ];
						}
						value = gform.applyFilters( 'gppc_copied_value', value, $targetElem, field );
						$targetElem.val( value );
					} else if( isCheckable ) {
						if( $.inArray( $targetElem.val(), sourceValues ) != -1 ) {
							$targetElem.prop( 'checked', true );
						}
					} else if( targetGroup.length > 1 ) {
						value = gform.applyFilters( 'gppc_copied_value', sourceValues[ index ], $targetElem, field );
						$targetElem.val( value );
					}
					// if there is only one input, join the source values
					else {
						// filter out empty values
						sourceValues = sourceValues.filter( function( item, pos ) {
							return item != '';
						} );
						value = gform.applyFilters( 'gppc_copied_value', self.cleanValueByInputType( sourceValues.join( ' ' ), $targetElem.attr( 'type' ) ), $targetElem, field, sourceValues );

						// If we're targeting a choice-based Pricing field - and - the source value does not contain a
						// pipe (value|price), find the price-excluded value match in the target.
						if( $targetElem.parents( '.gfield_price' ).length > 0 && $targetElem.is( 'select, input[type="radio"], input[type="checkbox"]' ) && value.indexOf( '|' ) === -1 ) {
							$targetElem.val( $targetElem.find( 'option[value^="' + value + '|"]' ).attr( 'value' ) );
						} else {
							// If copying to a select, make sure the value we are copying exists. If not, select the first option.
							if( $targetElem.is( 'select' ) && $targetElem.find( 'option[value="' + value + '"]' ).length === 0 ) {
								value = $targetElem.find( 'option:first' ).val();
							}
							$targetElem.val( value );
						}
					}

				} );

				// force user events to trigger
				if( targetGroup.is( ':checkbox, :radio' ) ) {
					if( ! isOverwrite ) {
						// trigger 'keypress' on all checked checkboxes to trigger applicable conditional logic
						targetGroup.filter( ':checked' );
					}
					targetGroup.keypress();
				} else {
					targetGroup
						.change()
						// @hack trigger chosen:updated on every change since it doesn't "hurt" anything to do so; alternative is checking if chosen is activated
						.trigger( 'chosen:updated' );
				}

				targetGroup.trigger( 'copy.gpcopycat' );

			}

		};

		/**
		 * Clear values when checkbox has been unselected. Only used by checkbox-triggered copies.
		 *
		 * @param elem
		 */
		self.clearValues = function(elem) {

			var fieldId = $(elem).parents('li.gwcopy').attr('id').replace('field_' + self.formId + '_', '');
			var fields = self.fields[fieldId];

			for( var i = 0; i < fields.length; i++ ) {

				var field        = fields[i],
					sourceValues = [],
					targetGroup  = self.getFieldGroup( field, 'target' ),
					sourceGroup  = self.getFieldGroup( field, 'source' ),
					isListtoList = self.isListField( targetGroup ) && self.isListField( sourceGroup );

				if( isListtoList ) {
					return;
				}

				if( parseInt( field.source ) == fieldId && $( elem ).is( ':checkbox' ) ) {
					if( self.overwrite ) {
						targetGroup.prop( 'checked', false );
					}
					self.copyValues( elem, true, true );
					return;
				}

				sourceGroup.each( function( i ) {
					sourceValues[i] = $(this).val();
				} );

				targetGroup.each( function( i ) {

					var $targetElem = $( this ),
						fieldValue  = $targetElem.val(),
						isCheckable = $targetElem.is( ':checkbox, :radio' ),
						isCheckbox  = $targetElem.is( ':checkbox' );

					if( isCheckbox ) {
						$targetElem.prop( 'checked', $.inArray( fieldValue, sourceValues ) !== -1 );
					} else if( isCheckable ) {
						$targetElem.prop( 'checked', false );
					} else if( fieldValue == sourceValues[i] ) {
						$targetElem.val( '' );
					}

				} ).change();

				// remove empty rows from List fields; only applicable when clearing from a non-List-field to a List field (i.e. Checkbox to List field).
				if( self.isListField( targetGroup ) ) {
					var maxRows = self.getMaxRowCount( targetGroup );
					targetGroup.parents( '.ginput_list' ).find( '.gfield_list_group:not(:first)' ).each( function() {
						if( $( this ).find( '.gfield_list_cell input[value!=""]' ).length === 0 ) {
							gformDeleteListItem( $( this ).find( 'input' ).eq( 0 ), maxRows );
						}
					} );
				}

			}

		};

		self.cleanValueByInputType = function( value, inputType ) {

			if( inputType == 'number' ) {
				value = gformToNumber( value );
			}

			return value;
		};

		self.getFieldGroup = function( field, groupType ) {

			var rawFieldId      = field[ groupType ],
				fieldId         = parseInt( rawFieldId ),
				formId          = field[ groupType + 'FormId' ], // i.e. 'sourceFormId' or 'targetFormId',
				$field          = $( '#field_' + formId + '_' + fieldId ),
				group           = $field.find( 'input[name^="input"]:not( :button ), select[name^="input"], textarea[name^="input"]' ),
				isListField     = self.isListField( group );

			// Many 3rd parties add additional non-capturable inputs to the List field. Let's filter those out.
			if( isListField ) {
				group = group.filter( '[name="input_{0}[]"]'.format( fieldId ) );
			}

			// Handle input-specific fields (excluding List fields).
			if( isInputSpecific( rawFieldId ) && ! isListField ) {

				var inputId       = rawFieldId.split( '.' )[1],
					filteredGroup = group.filter( '[id^="input_' + formId + '_' + fieldId + '_' + inputId + '"], input[name="input_' + rawFieldId + '"]' );

				// some fields (like email with confirmation enabled) have multiple inputs but the first input has no HTML ID (input_1_1 vs input_1_1_1)
				if( filteredGroup.length <= 0 ) {
					group = group.filter( '#input_' + formId + '_' + rawFieldId );
				} else {
					group = filteredGroup;
				}

			}
			// Handle input-specific List fields.
			else if( isInputSpecific( rawFieldId ) && isListField ) {

				group = group.filter( function() {

					var currentListInputIndex = self.getListInputIndex( $( this ) ),
						targetListInputIndex  = self.getListInputIndex( rawFieldId, currentListInputIndex );

					return currentListInputIndex == targetListInputIndex;
				} );

			}

			if( groupType == 'source' && group.is( 'input:radio, input:checkbox' ) ) {
				group = group.filter( ':checked' );
			}

			/**
			 * Add/remove inputs from the field group.
			 *
			 * @since 1.4.22
			 *
			 * @param array  $group    A jQuery object with all field inputs that belong to this group.
			 * @param array  $trigger  An array of data that represents the triggering field and which field to copy to which field.
			 * @param string groupType The type of group; either "source" or "target".
			 * @param array  $field    The jQuery field object for the current group.
			 */
			return gform.applyFilters( 'gpcc_field_group', group, field, groupType, $field );
		};

		self.getGroupValues = function( group, type, args ) {

			if( typeof args == 'undefined' ) {
				args = {};
			}

			args = parseArgs( args, {
				sort:          false,
				isListToList:  false,
				sourceInputId: false,
				targetInputId: false
			} );

			var values = [];

			group.each( function( i ) {

				var index = i;

				if( args.isListToList && ! isInputSpecific( args.targetInputId ) ) {
					index = self.getListInputIndex( $( this ) );
				}

				// UPDATE: we shouldn't need this anymore since getFieldGroup() has been updated to only return the targeted inputs
				// ---
				// if we're copying - to or from - a list field and we've specified a specific column/row, we only want to get
				// values from the specified column/row
				// if( self.isListField( group ) && parseInt( args.sourceInputId ) != args.sourceInputId ) {
				//     var current = self.getListInputIndex( $( this ) ),
				//         source  = self.getListInputIndex( args.sourceInputId, current );
				//     if( current != source ) {
				//         return true;
				//     }
				// }

				// Probably how the values should be to support a more universal format:
				// values.push( {
				 //    index: index,
				 //    value: $( this ).val()
				// } );

				values[ index ] = $( this ).val();

			} );

			if( args.sort !== false ) {

				var sort = args.sort.filter( function( item, pos ) {
					return args.sort.indexOf( item ) == pos && item != '';
				} );

				var sorted = [];

				for( var i = 0; i < sort.length; i++ ) {
					var index = values.indexOf( sort[ i ] );
					if( index !== -1 ) {
						sorted.push( values[ index ] );
						values.splice( index, 1 );
					}
				}

				values = sorted.concat( values );

			}

			return values;
		};

		self.isListField = function( group ) {
			return group.parents( '.ginput_list' ).length > 0;
		};

		self.getListInputIndex = function( $input, currentInputIndex, returnObject ) {

			if( typeof currentInputIndex == 'undefined' ) {
				returnObject = false;
			} else if( typeof currentInputIndex == 'boolean' ) {
				returnObject = currentInputIndex;
				currentInputIndex = false;
			} else if( typeof returnObject == 'undefined' ) {
				returnObject = false;
			}

			if( typeof $input == 'object' ) {
				var fieldId = $input.attr( 'name' ).match( /(\d+)/ )[0], // returns '34' from 'input_34[]'
					$group  = $input.parents( '.gfield_list_group' ),
					$inputs = $group.find( '[name="input_{0}[]"]'.format( fieldId ) ),
					$groups = $input.parents( '.gfield_list_container' ).find( '.gfield_list_group' ),
					column  = $inputs.index( $input ) + 1,
					row     = $groups.index( $group ) + 1;
			} else {
				var inputId = $input,
					bits    = inputId.split( '.' ),
					byts    = currentInputIndex ? currentInputIndex.split( '.' ) : [ 1, 1 ],
					column  = bits[1],
					row     = bits[2] ? bits[2] : byts[1];
			}

			var inputIndex = column + '.' + row;

			return returnObject ? { index: inputIndex, column: column, row: row } : inputIndex;
		};

		self.getMaxRowCount = function( targetGroup ) {
			var classes = targetGroup.parents( 'li.gfield' ).attr( 'class' ).split( ' ' );
			for( var i = 0; i < classes.length; i++ ) {
				if( classes[i].indexOf( 'gp-field-maxrows' ) !== -1 ) {
					return parseInt( classes[i].split( '-' )[3] );
				}
			}
			return 0;
		};

		function isInputSpecific( inputId ) {
			return parseInt( inputId ) != inputId;
		}

		function parseArgs( args, defaults ) {

			for( key in defaults ) {
				if( defaults.hasOwnProperty( key ) && typeof args[ key ] == 'undefined' ) {
					args[ key ] = defaults[ key ];
				}
			}

			return args;
		}

		function getObjectValues( obj ) {

			if( ! ( obj instanceof Object ) ) {
				return obj;
			}

			var values = [];

			for( var prop in obj ) {
				if( obj.hasOwnProperty( prop ) ) {
					values.push( obj[ prop ] );
				}
			}

			return values;
		}

		self.init();

	};

} )( jQuery );