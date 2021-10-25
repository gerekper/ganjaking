/* global wpf, wpforms_builder */

'use strict';

var WPFormsConditionals = window.WPFormsConditionals || ( function( document, window, $ ) {

	/**
	 * Helper methods.
	 *
	 * @since 1.6.0.2
	 */
	var helpers = {

		/**
		 * Splits an array to chunks of n elements.
		 *
		 * @since 1.6.0.2
		 *
		 * @param {Array}  array Array to split.
		 * @param {number} n     Number of elements in each chunks.
		 *
		 * @returns {Array} Array.
		 */
		arraySplitIntoChunks: function( array, n ) {

			if ( ! array.length ) {
				return [];
			}

			return [ array.slice( 0, n ) ]
				.concat( helpers.arraySplitIntoChunks( array.slice( n ), n ) );
		},
	};

	/**
	 * Conditional rules updating methods.
	 *
	 * @since 1.6.0.2
	 */
	var updater = {

		/**
		 * All form fields.
		 *
		 * @since 1.6.0.2
		 */
		allFields: {},

		/**
		 * Conditional rule rows.
		 *
		 * @since 1.6.0.2
		 */
		$ruleRows: {},

		/**
		 * Form fields supporting conditional logic.
		 *
		 * @since 1.6.0.2
		 */
		conditionalFields: {},

		/**
		 * HTML template containing a list of <option> elements representing available conditional fields.
		 *
		 * @since 1.6.0.2
		 */
		fieldsListTemplate: '',

		/**
		 * HTML templates containing a list of <option> elements representing values of every conditional field.
		 *
		 * @since 1.6.0.2
		 */
		fieldValuesListTemplates: {},

		/**
		 * Cache all form fields.
		 *
		 * @since 1.6.0.2
		 *
		 * @param {Array} allFields List of all fields.
		 */
		cacheAllFields: function( allFields ) {

			updater.allFields = allFields;
		},

		/**
		 * Cache all rule rows.
		 *
		 * @since 1.6.0.2
		 *
		 * @param {jQuery} $rows Collection of all conditional rule rows.
		 */
		cacheRuleRows: function( $rows ) {

			updater.$ruleRows = $rows || $( '.wpforms-conditional-row' );
		},

		/**
		 * Cache allowed form fields supporting conditional logic.
		 *
		 * @since 1.6.0.2
		 */
		setConditionalFields: function() {

			updater.conditionalFields = updater.removeUnsupportedFields();
		},

		/**
		 * Remove field types that are not allowed and whitelisted.
		 *
		 * @since 1.6.0.2
		 *
		 * @returns {Array} Filtered list of fields.
		 */
		removeUnsupportedFields: function() {

			var allowed = wpforms_builder.cl_fields_supported,
				fields = $.extend( {}, updater.allFields ),
				key;

			for ( key in fields ) {
				if ( $.inArray( fields[key].type, allowed ) === -1 ) {
					delete fields[key];
				} else if ( typeof fields[key].dynamic_choices !== 'undefined' && fields[key].dynamic_choices !== '' ) {
					delete fields[key];
				}
			}

			return fields;
		},

		/**
		 * Setup all HTML templates.
		 *
		 * @since 1.6.0.2
		 */
		setTemplates: function() {

			updater.setFieldsListTemplate();

			// Reset cached field values templates before processing.
			updater.fieldValuesListTemplates = {};
		},

		/**
		 * Return an HTML template for a select with all the fields names.
		 *
		 * Doing a jQuery-DOM and copying the underlying HTML makes rendering
		 * twice as fast.
		 *
		 * @since 1.6.0.2
		 */
		setFieldsListTemplate: function() {

			var key,
				label;

			var $fieldsListTemplate = $( '<select>' )
				.append( $( '<option>', { value: '', text: wpforms_builder.select_field } ) );

			for ( key in wpf.orders.fields ) {

				var field_id = wpf.orders.fields[ key ];

				if ( ! updater.conditionalFields[ field_id ] ) {
					continue;
				}

				if ( typeof updater.conditionalFields[ field_id ].label !== 'undefined' && updater.conditionalFields[ field_id ].label.toString().trim() !== '' ) {
					label = wpf.sanitizeHTML( updater.conditionalFields[ field_id ].label.toString().trim() );
				} else {
					label = wpforms_builder.field + ' #' + updater.conditionalFields[ field_id ].id;
				}

				$fieldsListTemplate.append( $( '<option>', {
					value: updater.conditionalFields[ field_id ].id,
					text : label,
					id   : 'option-' + field_id,
				} ) );
			}

			updater.fieldsListTemplate = $fieldsListTemplate.html();
		},

		/**
		 * Return an HTML with a list of options from a given field.
		 *
		 * @since 1.6.0.2
		 *
		 * @param {Array}  fields		 Array of fields.
		 * @param {number} fieldSelected ID of selected field.
		 *
		 * @returns {string} HTML template.
		 */
		getFieldValuesListTemplate: function( fields, fieldSelected ) {

			// Return cached template if possible.
			if ( updater.fieldValuesListTemplates[fieldSelected] ) {
				return updater.fieldValuesListTemplates[fieldSelected];
			}

			var key;
			var items   = wpf.orders.choices['field_' + fieldSelected];
			var $select = $( '<select>' );

			for ( key in items ) {
				var choiceKey = items[key],
					label = typeof fields[ fieldSelected ].choices[ choiceKey ] !== 'undefined' && fields[ fieldSelected ].choices[ choiceKey ].label.toString().trim() !== '' ?
						wpf.sanitizeHTML( fields[ fieldSelected ].choices[ choiceKey ].label.toString().trim() ) :
						wpforms_builder.choice_empty_label_tpl.replace( '{number}', choiceKey );
				$select.append( $( '<option>', {value: choiceKey, text: label, id: 'choice-' + choiceKey} ) );
			}

			// Cache the template for future use and return the HTML.
			return updater.fieldValuesListTemplates[fieldSelected] = $select.html();
		},

		/**
		 * Form fields supporting conditional logic.
		 *
		 * @since 1.6.0.2
		 */
		updateConditionalRuleRows: function() {

			var rowsToProcess  = updater.$ruleRows.length;

			/**
			 * Split all the rows in sets of at most 20 elements.
			 *
			 * The set of 20 rows would then be processed as soon as possible but without blocking
			 * the main thread (thanks to setTimeout).
			 *
			 * When all the groups are processed the function finalize is called.
			 *
			 * @since 1.6.0.2
			 */
			helpers.arraySplitIntoChunks( updater.$ruleRows, 20 ).map( function( elements ) {
				setTimeout( function() {
					for ( var i = 0; i < elements.length; ++i ) {
						updater.updateConditionalRuleRow( elements[i] );
						--rowsToProcess;
					}

				}, 0 );
			} );
		},

		/**
		 * Redraw the conditional rule in the builder.
		 *
		 * @since 1.6.0.2
		 *
		 * @param {object} row Element container.
		 */
		updateConditionalRuleRow: function( row ) {

			var $row           = $( row ),
				fieldID        = $row.attr( 'data-field-id' ),
				$fields        = $row.find( '.wpforms-conditional-field' ),
				fieldSelected  = $fields.val(),
				$value         = $row.find( '.wpforms-conditional-value' );

			// Clone template
			$fields[0].innerHTML = updater.fieldsListTemplate;

			// Remove the current item
			$fields.find( '#option-' + fieldID ).remove();

			if ( ! fieldSelected ) {

				// Remove all ids properties.
				// Querying the DOM by ID is much faster. It is safe to remove the IDs now.
				$fields.find( 'option' ).removeAttr( 'id' );
				return;
			}

			// Check if previous selected field exists in the new options added.
			if ( $fields.find( '#option-' + fieldSelected ).length ) {
				updater.restorePreviousRuleRowSelection( $row, $fields, fieldSelected, $value );
			} else {
				updater.removeRuleRow( $row );
			}

			// Remove all ids properties.
			// Querying the DOM by ID is much faster. It is safe to remove the IDs now.
			$fields.find( 'option' ).removeAttr( 'id' );
			$value.find( 'option' ).removeAttr( 'id' );
		},

		/**
		 * Update delete confirmation alert message.
		 *
		 * @since 1.6.7
		 *
		 * @param {object} fieldData Field Data object.
		 */
		fieldDeleteConfirmAlert: function( fieldData ) {

			var alert = wpforms_builder.conditionals_change + '<br>',
				updateAlert;

			if ( wpf.empty( updater.allFields ) ) {

				updater.cacheAllFields( wpf.getFields() );
			}

			$( '.wpforms-conditional-field' ).each( function() {

				if ( fieldData.id === Number( $( this ).val() ) ) {

					if ( fieldData.choiceId && fieldData.choiceId !== Number( $( this ).closest( '.wpforms-conditional-row' ).find( '.wpforms-conditional-value' ).val() ) ) {
						return;
					}

					alert += updater.getChangedFieldNameForAlert( $( this ).closest( '.wpforms-conditional-group' ).data( 'reference' ) );

					updateAlert = true;
					fieldData.trigger = true;
				}
			} );

			if ( updateAlert ) {

				fieldData.message = '<strong>' + fieldData.message + '</strong>' + '<br><br>' + alert;
			}
		},

		/**
		 * Restore the rule row selection before conditional rules update.
		 *
		 * @since 1.6.0.2
		 *
		 * @param {object} $row  		 Row container.
		 * @param {object} $fields		 Field object.
		 * @param {string} fieldSelected Field selected value.
		 * @param {object} $value		 Field Value.
		 */
		restorePreviousRuleRowSelection: function( $row, $fields, fieldSelected, $value ) {

			var valueSelected  = '';

			// Exists, so restore previous selected value
			$fields.find( '#option-' + fieldSelected ).prop( 'selected', true );

			if ( ! $value.length || ! $value.is( 'select' ) ) {
				return;
			}

			// Since the field exist and was selected, now we must proceed
			// to updating the field values. Luckily, we only have to do
			// this for fields that leverage a select element.

			// Grab the currently selected value to restore later
			valueSelected = $value.val();

			$value[0].innerHTML = updater.getFieldValuesListTemplate( updater.conditionalFields, fieldSelected );

			// Check if previous selected value exists in the new options added
			if ( $value.find( '#choice-' + valueSelected ).length ) {

				$value.find( '#choice-' + valueSelected ).prop( 'selected', true );

			}
		},

		/**
		 * Check if previous selected field exists in the new options added.
		 *
		 * @since 1.6.0.2
		 *
		 * @param {object} $row Row container.
		 */
		removeRuleRow: function( $row ) {

			// Since previously selected field no longer exists, this
			// means this rule is now invalid. So the rule gets
			// deleted as long as it isn't the only rule remaining.
			var $group = $row.closest( '.wpforms-conditional-group' );

			if ( $group.find( 'table >tbody >tr' ).length === 1 ) {
				var $groups = $row.closest( '.wpforms-conditional-groups' );
				if ( $groups.find( '.wpforms-conditional-group' ).length > 1 ) {
					$group.remove();
				} else {
					$row.find( '.wpforms-conditional-value' ).remove();
					$row.find( '.value' ).append( '<select>' );
				}
			} else {
				$row.remove();
			}
		},

		/**
		 * Return HTML with error Message.
		 *
		 * @since 1.6.0.2
		 *
		 * @param  {mixed} field Field ID or field name.
		 * @returns {string} HTML message.
		 */
		getChangedFieldNameForAlert: function( field ) {

			if ( ! wpf.isNumber( field ) ) {

				// Panel
				return '<br>' + field;
			}

			// Field
			if ( ( ( updater.allFields[field] || {} ).label || '' ).length ) {
				return '<br/>' + wpf.sanitizeString( updater.allFields[field].label ) + ' (' + wpforms_builder.field + ' #' + field + ')';
			} else {
				return '<br>' + wpforms_builder.field + ' #' + field;
			}
		},
	};

	var app = {

		/**
		 * Start the engine.
		 *
		 * @since 1.0.0
		 */
		init: function() {

			// Document ready
			$( WPFormsConditionals.ready );

		},

		/**
		 * Document ready.
		 *
		 * @since 1.0.0
		 */
		ready: function() {

			WPFormsConditionals.bindUIActions();
		},


		/**
		 * Element bindings.
		 *
		 * @since 1.0.0
		 */
		bindUIActions: function() {

			var $builder = $( '#wpforms-builder' );

			// Conditional support toggle.
			$builder.on( 'change', '.wpforms-conditionals-enable-toggle input[type=checkbox]', function( e ) {
				WPFormsConditionals.conditionalToggle( this, e );
			} );

			// Conditional process field select.
			$builder.on( 'change', '.wpforms-conditional-field', function( e ) {
				WPFormsConditionals.conditionalField( this, e );
			} );

			// Conditional process operator select.
			$builder.on( 'change', '.wpforms-conditional-operator', function( e ) {
				WPFormsConditionals.conditionalOperator( this, e );
			} );

			// Conditional add new rule.
			$builder.on( 'click', '.wpforms-conditional-rule-add', function( e ) {
				WPFormsConditionals.conditionalRuleAdd( this, e );
			} );

			// Conditional delete rule.
			$builder.on( 'click', '.wpforms-conditional-rule-delete', function( e ) {
				WPFormsConditionals.conditionalRuleDelete( this, e );
			} );

			// Conditional add new group.
			$builder.on( 'click', '.wpforms-conditional-groups-add', function( e ) {
				WPFormsConditionals.conditionalGroupAdd( this, e );
			} );

			// Conditional logic update/refresh.
			$( document ).on( 'wpformsFieldUpdate', WPFormsConditionals.conditionalUpdateOptions );

			$builder.on( 'wpformsBeforeFieldDeleteAlert', function( e, fieldData ) {
				updater.fieldDeleteConfirmAlert( fieldData );
			} );
		},

		/**
		 * Update/refresh the conditional logic fields and associated options.
		 *
		 * @since 1.0.0
		 */
		conditionalUpdateOptions: function( e, allFields, $rows ) {

			if ( wpf.empty( allFields ) ) {
				return;
			}

			updater.cacheAllFields( allFields );
			updater.cacheRuleRows( $rows );

			updater.setConditionalFields();
			updater.setTemplates();

			updater.updateConditionalRuleRows();
		},

		/**
		 * Toggle conditional support.
		 *
		 * @since 1.0.0
		 */
		conditionalToggle: function( el, e ) {

			e.preventDefault();

			var $this      = $( el ),
				$block     = $this.parent().parent(),
				logicBlock = wp.template( 'wpforms-conditional-block' ),
				data       = {
					fieldID    : $this.parent().data( 'field-id' ),
					fieldName  : $this.data( 'name' ),
					actions    : $this.data( 'actions' ),
					actionDesc : $this.data( 'action-desc' ),
				};

			if ( $this.is( ':checked' ) ) {

				// Add conditional logic rules.
				$block.append( logicBlock( data ) );

				// Update fields in the added rule.
				WPFormsConditionals.conditionalUpdateOptions( false, wpf.getFields( false, true ), $block.find( '.wpforms-conditional-row' ) );
			} else {

				// Remove conditional logic rules.
				$.confirm( {
					title: false,
					content: wpforms_builder.conditionals_disable,
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

								// Prompt
								$block.find( '.wpforms-conditional-groups' ).remove();
							},
						},
						cancel: {
							text: wpforms_builder.cancel,
							action: function() {
								$this.prop( 'checked', true );
							},
						},
					},
				} );
			}
		},

		/**
		 * Process conditional field.
		 *
		 * @since 1.0.0
		 */
		conditionalField: function( el, e ) {

			e.preventDefault();

			var $this     = $( el ),
				$rule     = $this.parent().parent(),
				$operator = $rule.find( '.wpforms-conditional-operator' ),
				operator  = $operator.find( 'option:selected' ).val(),
				data      = WPFormsConditionals.conditionalData( $this ),
				name      = data.inputName + '[' + data.groupID + '][' + data.ruleID + '][value]',
				$element;

			if ( ! data.field ) {

				// Placeholder has been selected.
				$element = $( '<select>' );

			} else if (
				data.field.type === 'select' ||
				data.field.type === 'radio' ||
				data.field.type === 'checkbox' ||
				data.field.type === 'payment-multiple' ||
				data.field.type === 'payment-checkbox' ||
				data.field.type === 'payment-select'
			) {

				// Selector type fields use select elements.
				$element = $( '<select>' ).attr( { name: name, class: 'wpforms-conditional-value' } ); // jshint ignore:line
				$element.append( $( '<option>', { value: '', text : wpforms_builder.select_choice } ) );
				if ( data.field.choices ) {
					for ( var key in wpf.orders.choices[ 'field_' + data.field.id ] ) {
						var choiceKey = wpf.orders.choices[ 'field_' + data.field.id ][ key ],
							label = typeof data.field.choices[ choiceKey ].label !== 'undefined' && data.field.choices[ choiceKey ].label.toString().trim() !== '' ?
								wpf.sanitizeHTML( data.field.choices[ choiceKey ].label.toString().trim() ) :
								wpforms_builder.choice_empty_label_tpl.replace( '{number}', choiceKey );
						$element.append( $( '<option>', { value: choiceKey, text: wpf.sanitizeHTML( label ) } ) );
					}
				}
				$operator.find( "option:not([value='=='],[value='!='],[value='e'],[value='!e'])" ).prop( 'disabled', true ).prop( 'selected', false ); // jshint ignore:line

			} else {

				// Text type fields (everything else) use text inputs.

				// Determine input type.
				var inputType = 'text';
				if ( 'rating' === data.field.type || 'net_promoter_score' === data.field.type || 'number-slider' === data.field.type ) {
					inputType = 'number';
				}
				$element = $( '<input>' ).attr( { type: inputType, name: name, class: 'wpforms-conditional-value' } ); // jshint ignore:line
				$operator.find( 'option' ).prop( 'disabled', false );
			}

			if ( operator === 'e' || operator === '!e' ) {

				// Empty/not empty doesn't use input, so we disable it.
				$element.prop( 'disabled', true );
			}

			$rule.find( '.value' ).empty().append( $element );
		},

		/**
		 * Process conditional field.
		 *
		 * @since 1.2.0
		 */
		conditionalOperator: function( el, e ) {

			e.preventDefault();

			var $this    = $( el ),
				$rule    = $this.parent().parent(),
				$value   = $rule.find( '.wpforms-conditional-value' ),
				operator = $this.find( 'option:selected' ).val();

			if ( operator === 'e' || operator === '!e' ) {
				$value.prop( 'disabled', true );
				if ( $value.is( 'select' ) ) {
					$value.find( 'option:selected' ).prop( 'selected', false );
				} else {
					$value.val( '' );
				}
			} else {
				$value.prop( 'disabled', false );
			}
		},

		/**
		 * Add new conditional rule.
		 *
		 * @since 1.0.0
		 */
		conditionalRuleAdd: function( el, e ) {

			e.preventDefault();

			var $this     = $( el ),
				$group    = $this.closest( '.wpforms-conditional-group' ),
				$rule     = $group.find( 'tr' ).last(),
				$newRule  = $rule.clone(),
				$field    = $newRule.find( '.wpforms-conditional-field' ),
				$operator = $newRule.find( '.wpforms-conditional-operator' ),
				data      = WPFormsConditionals.conditionalData( $field ),
				ruleID    = Number( data.ruleID ) + 1,
				name      = data.inputName + '[' + data.groupID + '][' + ruleID + ']';

			$newRule.find( 'option:selected' ).prop( 'selected', false );
			$newRule.find( '.value' ).empty().append( $( '<select>' ) );
			$field.attr( 'name', name + '[field]' ).attr( 'data-ruleid', ruleID );
			$operator.attr( 'name', name + '[operator]' );
			$rule.after( $newRule );
		},

		/**
		 * Delete conditional rule. If the only rule in group then group will
		 * also be removed.
		 *
		 * @since 1.0.0
		 */
		conditionalRuleDelete: function( el, e ) {

			e.preventDefault();

			var $this = $( el ),
				$group = $this.closest( '.wpforms-conditional-group' ),
				$rows  = $group.find( 'table >tbody >tr' );

			if ( $rows && $rows.length === 1 ) {
				var $groups = $this.closest( '.wpforms-conditional-groups' );
				if ( $groups.find( '.wpforms-conditional-group' ).length > 1 ) {
					$group.remove();
				} else {
					return;
				}
			} else {
				$this.parent().parent().remove();
			}
		},

		/**
		 * Add new conditional group.
		 *
		 * @since 1.0.0
		 */
		conditionalGroupAdd: function( el, e ) {

			e.preventDefault();

			var $this      = $( el ),
				$groupLast = $this.parent().find( '.wpforms-conditional-group' ).last(),
				$newGroup  = $groupLast.clone();

			$newGroup.find( 'tr' ).not( ':first' ).remove();

			var $field    = $newGroup.find( '.wpforms-conditional-field' ),
				$operator = $newGroup.find( '.wpforms-conditional-operator' ),
				data      = WPFormsConditionals.conditionalData( $field ),
				groupID   = Number( data.groupID ) + 1,
				ruleID    = 0,
				name      = data.inputName + '[' + groupID + '][' + ruleID + ']';

			$newGroup.find( 'option:selected' ).prop( 'selected', false );
			$newGroup.find( '.value' ).empty().append( $( '<select>' ) );
			$field.attr( 'name', name + '[field]' ).attr( 'data-ruleid', ruleID ).attr( 'data-groupid', groupID );
			$operator.attr( 'name', name + '[operator]' );
			$this.before( $newGroup );
		},


		//--------------------------------------------------------------------//
		// Helper functions
		//--------------------------------------------------------------------//

		/**
		 * Return various data for the conditional field.
		 *
		 * @since 1.0.0
		 */
		conditionalData: function( el ) {

			var $this = $( el );
			var data  = {
				fields     : wpf.getFields( false, true ),
				inputBase  : $this.closest( '.wpforms-conditional-row' ).attr( 'data-input-name' ),
				fieldID    : $this.closest( '.wpforms-conditional-row' ).attr( 'data-field-id' ),
				ruleID     : $this.attr( 'data-ruleid' ),
				groupID    : $this.attr( 'data-groupid' ),
				selectedID : $this.find( ':selected' ).val(),
			};

			data.inputName = data.inputBase + '[conditionals]';

			if ( data.selectedID.length ) {
				data.field = data.fields[ data.selectedID ];
			} else {
				data.field = false;
			}
			return data;
		},
	};

	return app;

}( document, window, jQuery ) );

WPFormsConditionals.init();
