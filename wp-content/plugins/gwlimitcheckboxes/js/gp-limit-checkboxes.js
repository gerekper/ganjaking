( function( $ ) {

	window.GPLimitCheckboxes = function( args ) {

		var self = this;

		// copy all args to current object: (list expected props)
		for ( prop in args ) {
			if ( args.hasOwnProperty( prop ) ) {
				self[ prop ] = args[ prop ];
			}
		}

		self.init = function() {

			if ( window.GPLimitCheckboxes && ! window.GPLimitCheckboxes.instances ) {
				window.GPLimitCheckboxes.instances = {};
			}

			self.bindTriggerEvents();
			self.listenForChanges();

			window.GPLimitCheckboxes.instances[ self.formId ] = self;

		};

		self.resetCheckboxElemCache = function () {
			self.checkboxElemCache = {};
		};

		self.bindTriggerEvents = function() {

			var selectors = [];
			var triggers  = $.map(self.triggers, function (value) {
				return value;
			});

			for ( var i = 0; i < triggers.length; i++ ) {
				selectors.push( triggers[ i ].selector );
			}

			var $elems = $( selectors.join( ', ' ) );

			self.resetCheckboxElemCache();

			// Exclude choices that were already disabled so that they will always be disabled.
			$elems.each( function() {
				var $parent = $( this ).parents( '.gfield' );
				// On AJAX-enabled forms, GF will evaluate conditional logic *before* GPLC can test for checkboxes which
				// are disabled by default. If the field is hidden by conditional logic, GPLC will incorrectly think that
				// it is disabled by default. Let's account for this...
				if ( ! $parent.data( 'gf-disabled-assessed' ) || $( this ).hasClass( 'gf-default-disabled' ) ) {
					$( this ).filter( ':disabled' ).addClass( 'gplc-pre-disabled' );
				}
			} );

			// Exclude Select All choices.
			$elems.filter( 'input[id$="select_all"]' ).addClass( 'gplc-select-all' );

			$elems.change( function() {
				self.handleCheckboxClick( $( this ) );
			} ).each( function() {
				self.handleCheckboxClick( $( this ) );
			} );

		};

		self.listenForChanges = function() {
			$(document).on('gppa_updated_batch_fields', function(event, formId) {
				if (formId != self.formId) {
					return;
				}

				self.bindTriggerEvents();
			});
		}

		self.handleCheckboxClick = function( $elem ) {

			var disableFieldIds = [],
				enableFieldIds  = [],
				fieldId         = typeof $elem != 'undefined' ? parseInt( $elem.attr( 'id' ).split( '_' )[2] ) : null;

			// loops through ALL groups to make sure that overlapping groups are covered
			for ( var i = 0; i < self.groups.length; i++ ) {

				if ( self.groups[ i ].fields.indexOf( fieldId ) === -1 ) {
					continue;
				}

				/**
				 * Filter the group of checkboxes that are about to be processed.
				 *
				 * @since 1.2
				 *
				 * @param object group The current group.
				 * @param object $elem A jQuery object of the element that triggered the event.
				 * @param object gplc  The current instance of the GPLimitCheckboxes object.
				 */
				var group = gform.applyFilters( 'gplc_group', $.extend( true, {}, self.groups[ i ] ), fieldId, $elem, self );

				if ( self.isGroupMaxed( group ) ) {
					disableFieldIds = $.merge( disableFieldIds, group.fields );
				} else {
					enableFieldIds = $.merge( enableFieldIds, group.fields );
				}

			}

			// remove disabled fields from the enableFieldIds array
			enableFieldIds = GPLimitCheckboxes.diff( enableFieldIds, disableFieldIds );

			if ( enableFieldIds.length ) {

				var $enableFields = self.getCheckboxesByFieldIds( enableFieldIds );

				// Enable applicable checkboxes.
				$enableFields.not( '.gplc-pre-disabled, .gplc-select-all' ).attr( 'disabled', false );

			}

			if ( disableFieldIds.length ) {

				var $disableFields = self.getCheckboxesByFieldIds( disableFieldIds );

				// Disable applicable checkboxes.
				$disableFields.not( ':checked, .gplc-pre-disabled, .gplc-select-all' ).attr( 'disabled', true );

				// Supports GF 2.3 Select All option; uncheck any disabled checkbox that was not pre-disabled. Potential
				// complications: this does not trigger onclick events.
				$disableFields.filter( ':checked:disabled:not( .gplc-pre-disabled )' ).attr( 'checked', false ).trigger( 'change' );

			}

		};

		self.isGroupMaxed = function( group ) {
			var count = 0;
			$( self.getSelector( group.fields ) ).filter( ':checked:not( .gplc-select-all )' ).each( function() {
				var idIndex = this.id.split( '_' );
				/**
				 * Filter the count value of each checkbox in a field.
				 * Useful when trying to specify a custom weighted value for some checkboxes.
				 *
				 * @since 1.3.1
				 *
				 *
				 * @param number value    Value of the current choice, default 1.
				 * @param number formId   The ID of the current form.
				 * @param number fieldId  The ID of the current Checkbox field.
				 * @param number choice   The sequence number of the current Checkbox (starts from 1).
				 */
				count += gform.applyFilters( 'gplcb_checkbox_count', 1, self.formId, parseInt( idIndex[2] ), parseInt( idIndex[3] ) );
			} );
			return count >= group.max;
		};

		self.getSelector = function( fieldIds ) {
			var selectors = [];

			for ( var i = 0; i < fieldIds.length; i++ ) {
				var fieldId = fieldIds[i];

				if ( ! (fieldId in self.triggers)) {
					continue;
				}

				selectors.push( self.triggers[fieldId].selector );
			}

			return selectors.join( ', ' );
		};

		self.getCheckboxesByFieldIds = function( fieldIds ) {
			var fieldIdsJoined = fieldIds.join( ',' );

			if ( ! (fieldIdsJoined in self.checkboxElemCache) ) {
				self.checkboxElemCache[fieldIdsJoined] = $( self.getSelector( fieldIds ) );
			}

			return self.checkboxElemCache[fieldIdsJoined];
		};

		self.isFieldHidden = function( fieldId ) {
			var $field = $( '#input_' + self.formId + '_' + fieldId );
			return gformIsHidden( $field.children( ':first-child' ) );
		};

		GPLimitCheckboxes.diff = function( a, b ) {
			return a.filter( function( i ) {
				return b.indexOf( i ) < 0;
			} );
		};

		self.init();

	};

} )( jQuery );
