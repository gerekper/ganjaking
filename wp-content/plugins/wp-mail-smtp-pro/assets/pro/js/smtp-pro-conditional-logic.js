/* global wp_mail_smtp_conditional_logic */

'use strict';

var WPMailSMTP = window.WPMailSMTP || {};
WPMailSMTP.Admin = WPMailSMTP.Admin || {};

/**
 * WP Mail SMTP Admin area Conditional Logic module.
 *
 * @since 3.7.0
 */
WPMailSMTP.Admin.Conditionals = WPMailSMTP.Admin.Conditionals || ( function( document, window, $ ) {

	/**
	 * Public functions and properties.
	 *
	 * @since 3.7.0
	 *
	 * @type {object}
	 */
	var app = {

		/**
		 * Start the engine. DOM is not ready yet, use only to init something.
		 *
		 * @since 3.7.0
		 */
		init: function() {

			// Do that when DOM is ready.
			$( app.ready );
		},

		/**
		 * DOM is fully loaded.
		 *
		 * @since 3.7.0
		 */
		ready: function() {

			app.bindActions();
		},

		/**
		 * Element bindings.
		 *
		 * @since 3.7.0
		 */
		bindActions: function() {

			var $holder = $( '#wp-mail-smtp' );

			// Process property select change.
			$holder.on( 'change', '.wp-mail-smtp-conditional__property', app.processPropertyUpdate );

			// Conditional add new rule.
			$holder.on( 'click', '.wp-mail-smtp-conditional__add-rule', app.processRuleAdd );

			// Conditional delete rule.
			$holder.on( 'click', '.wp-mail-smtp-conditional__delete-rule', app.processRuleDelete );

			// Conditional add new group.
			$holder.on( 'click', '.wp-mail-smtp-conditional__add-group', app.processGroupAdd );
		},

		/**
		 * Process property select change. Update value field based on selected property.
		 *
		 * @since 3.7.0
		 *
		 * @param {object} e Event object.
		 */
		processPropertyUpdate: function( e ) {

			e.preventDefault();

			var $rule = $( this ).closest( '.wp-mail-smtp-conditional__row' ),
				$ruleOperator = $rule.find( '.wp-mail-smtp-conditional__operator' ),
				$ruleValue = $rule.find( '.wp-mail-smtp-conditional__value' ),
				data = app.conditionalData( $( this ) ),
				name = data.inputName + '[' + data.groupID + '][' + data.ruleID + '][value]',
				$element;

			// Enable all operators.
			$rule.find( '.wp-mail-smtp-conditional__operator option' ).prop( 'disabled', false );

			if ( ! data.property ) {

				// Placeholder has been selected.
				$element = $( '<select>' );
			} else if ( data.property.type === 'select' ) {
				$element = $( '<select>' ).attr( {name: name, class: 'wp-mail-smtp-conditional__value'} );

				if ( data.property.choices ) {
					$.each( data.property.choices, function( value, label ) {
						$element.append( $( '<option>', {value: value, text: label} ) );
					} );
				}

				// Disable non-select operators.
				var selectOperators = [ '==', '!=' ];

				if ( selectOperators.indexOf( $ruleOperator.val() ) === -1 ) {
					$ruleOperator.find( 'option[value="=="]' ).prop( 'selected', true );
				}

				$ruleOperator.find( 'option' ).each( function() {
					if ( selectOperators.indexOf( $( this ).val() ) === -1 ) {
						$( this ).prop( 'disabled', true );
					}
				} );
			} else {
				var value = $ruleValue.prop( 'tagName' ).toLowerCase() === 'input' ? $ruleValue.val() : '';

				$element = $( '<input>' ).attr( {type: 'text', value: value, name: name, class: 'wp-mail-smtp-conditional__value'} );
			}

			$rule.find( '.wp-mail-smtp-conditional__value-col' ).empty().append( $element );
		},

		/**
		 * Add new conditional rule.
		 *
		 * @since 3.7.0
		 *
		 * @param {object} e Event object.
		 */
		processRuleAdd: function( e ) {

			e.preventDefault();

			var $group = $( this ).closest( '.wp-mail-smtp-conditional__group' ),
				$rule = $group.find( '.wp-mail-smtp-conditional__row' ).last(),
				$newRule = $rule.clone(),
				$property = $newRule.find( '.wp-mail-smtp-conditional__property' ),
				$operator = $newRule.find( '.wp-mail-smtp-conditional__operator' ),
				data = app.conditionalData( $property ),
				ruleID = 'rule-' + ( Number( data.ruleID.replace( 'rule-', '' ) ) + 1 ),
				name = data.inputName + '[' + data.groupID + '][' + ruleID + ']';

			$newRule.find( 'option:disabled' ).prop( 'disabled', false );
			$newRule.find( 'option:selected' ).prop( 'selected', false );
			$newRule.find( 'input' ).val( '' );
			$property.attr( 'name', name + '[property]' ).attr( 'data-ruleid', ruleID );
			$operator.attr( 'name', name + '[operator]' );
			$rule.after( $newRule );
			$property.change();
		},

		/**
		 * Delete conditional rule. If the only rule in group then group will
		 * also be removed.
		 *
		 * @since 3.7.0
		 *
		 * @param {object} e Event object.
		 */
		processRuleDelete: function( e ) {

			e.preventDefault();

			var $group = $( this ).closest( '.wp-mail-smtp-conditional__group' ),
				$rows = $group.find( '.wp-mail-smtp-conditional__row' );

			if ( $rows && $rows.length === 1 ) {
				var $groups = $( this ).closest( '.wp-mail-smtp-conditional' );
				if ( $groups.find( '.wp-mail-smtp-conditional__group' ).length > 1 ) {
					$group.remove();
				} else {
					$rows.find( '.wp-mail-smtp-conditional__operator' ).val( '==' ).trigger( 'change' );
					$rows.find( '.wp-mail-smtp-conditional__value' ).val( '' ).trigger( 'change' );
					$rows.find( '.wp-mail-smtp-conditional__property' )
						.find( 'option:first-child' ).prop( 'selected', true ).trigger( 'change' );
				}
			} else {
				$( this ).closest( '.wp-mail-smtp-conditional__row' ).remove();
			}
		},

		/**
		 * Add new conditional group.
		 *
		 * @since 3.7.0
		 *
		 * @param {object} e Event object.
		 */
		processGroupAdd: function( e ) {

			e.preventDefault();

			var $groupLast = $( this ).closest( '.wp-mail-smtp-conditional' ).find( '.wp-mail-smtp-conditional__group' ).last(),
				$newGroup = $groupLast.clone();

			$newGroup.find( '.wp-mail-smtp-conditional__row' ).not( ':first' ).remove();

			var $property = $newGroup.find( '.wp-mail-smtp-conditional__property' ),
				$operator = $newGroup.find( '.wp-mail-smtp-conditional__operator' ),
				data = app.conditionalData( $property ),
				groupID = 'group-' + ( Number( data.groupID.replace( 'group-', '' ) ) + 1 ),
				ruleID = 'rule-0',
				name = data.inputName + '[' + groupID + '][' + ruleID + ']';

			$newGroup.find( 'input' ).val( '' );
			$newGroup.find( 'option:disabled' ).prop( 'disabled', false );
			$newGroup.find( 'option:selected' ).prop( 'selected', false );
			$property.attr( 'name', name + '[property]' ).attr( 'data-ruleid', ruleID ).attr( 'data-groupid', groupID );
			$operator.attr( 'name', name + '[operator]' );
			$groupLast.after( $newGroup );
			$property.change();
		},

		//--------------------------------------------------------------------//
		// Helper functions
		//--------------------------------------------------------------------//

		/**
		 * Return various data for the conditional property.
		 *
		 * @since 3.7.0
		 *
		 * @param {object} el Property field object.
		 *
		 * @returns {object} Data object.
		 */
		conditionalData: function( el ) {

			var $this = $( el );
			var data = {
				inputBase: $this.closest( '.wp-mail-smtp-conditional__row' ).attr( 'data-input-name' ),
				ruleID: $this.attr( 'data-ruleid' ),
				groupID: $this.attr( 'data-groupid' ),
			};

			data.inputName = data.inputBase + '[conditionals]';

			var selectedValue = $this.find( 'option:selected' ).val() || $this.find( 'option:first-child' ).val();

			if ( selectedValue.length && wp_mail_smtp_conditional_logic.properties[ selectedValue ] !== undefined ) {
				data.property = wp_mail_smtp_conditional_logic.properties[ selectedValue ];
			} else {
				data.property = false;
			}

			return data;
		},
	};

	return app;

}( document, window, jQuery ) );

WPMailSMTP.Admin.Conditionals.init();
