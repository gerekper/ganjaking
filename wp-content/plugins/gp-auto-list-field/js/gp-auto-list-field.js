/**
 * GP Auto List Field - Frontend Scripts
 */

( function( $ ) {

	window.GPAutoListField = function( args ) {

		var self = this;

		// copy all args to current object: formId, sourceFieldId, targetFieldId
		for( prop in args ) {
			if( args.hasOwnProperty( prop ) ) {
				self[ prop ] = args[ prop ];
			}
		}

		self.init = function() {

			self.$trigger = $( self.sourceFieldSelector );
			self.$targetField = $( self.targetFieldSelector );

			// update rows on page load
			self.updateListItems( self.$trigger, self.$targetField );

			// update rows when field value changes
			self.$trigger.on('change', function() {
				self.updateListItems( self.$trigger, self.$targetField );
			} );

			self.$trigger.on('keyup', function() {
				self._keyup_timeout = setTimeout( function() {
					clearTimeout(self._keyup_timeout);
					self.updateListItems( self.$trigger, self.$targetField );
				}, 600 );
			} );

			if( self.shouldHideListButtons ) {
				self.hideListButtons( self.$targetField );
			}

		};

		self.updateListItems = function( $trigger, $targetField ) {

			var count    = parseInt( $trigger.val() ),
				rowCount = $targetField.find( '.gfield_list_group' ).length;

			if ( !count ) {
				count = 1;
			}

			var diff = count - rowCount;

			if( diff > 0 ) {
				for( var i = 0; i < diff; i++ ) {
					$targetField.find( '.add_list_item:last' ).click();
				}
			}
			// Enforce row count max if List buttons are hidden.
			else if( self.shouldHideListButtons ) {

				// make sure we never delete all rows
				if( rowCount + diff == 0 ) {
					diff++;
				}

				for( var i = diff; i < 0; i++ ) {
					$targetField.find( '.delete_list_item:last' ).click();
				}

			}
		};

		self.hideListButtons = function( $listField ) {
			$listField.find( '.gfield_header_item--icons, .gfield_list_icons' ).hide();
		};

		self.init();

	}

} )( jQuery );
