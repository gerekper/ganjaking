
( function( $ ) {

    window.GPLimitCheckboxes = function( args ) {

        var self = this;

        // copy all args to current object: (list expected props)
        for( prop in args ) {
            if( args.hasOwnProperty( prop ) ) {
                self[ prop ] = args[ prop ];
            }
        }

        self.init = function() {

            if( window.GPLimitCheckboxes && ! window.GPLimitCheckboxes.instances ) {
                window.GPLimitCheckboxes.instances = {};
            }

            self.bindTriggerEvents();

            window.GPLimitCheckboxes.instances[ self.formId ] = self;

        };

        self.resetCheckboxElemCache = function () {
            self.checkboxElemCache = {};
        };

        self.bindTriggerEvents = function() {

            var selectors = [];
            var triggers = $.map(self.triggers, function (value) {
                return value;
            });

            for( var i = 0; i < triggers.length; i++ ) {
                selectors.push( triggers[ i ].selector );
            }

            var $elems = $( selectors.join( ', ' ) );

            self.resetCheckboxElemCache();

            // Exclude choices that were already disabled so that they will always be disabled.
            $elems.filter( ':disabled' ).addClass( 'gplc-pre-disabled' );

            // Exclude Select All choices.
            $elems.filter( 'input[id$="select_all"]' ).addClass( 'gplc-select-all' );

            $elems.change( function() {
                self.handleCheckboxClick( $( this ) );
            } ).each( function() {
                self.handleCheckboxClick( $( this ) );
            } );

        };

        self.handleCheckboxClick = function( $elem ) {

            var disableFieldIds = [],
                enableFieldIds  = [],
                fieldId         = typeof $elem != 'undefined' ? parseInt( $elem.attr( 'id' ).split( '_' )[2] ) : null;

            // loops through ALL groups to make sure that overlapping groups are covered
            for( var i = 0; i < self.groups.length; i++ ) {

                if ( self.groups[ i ].fields.indexOf(fieldId) === -1 ) {
                    continue;
                }

            	/**
	             * Filter the group that is about to be processed.
	             *
	             * @since 1.2
	             *
	             * @param object group             The current group.
	             * @param object $elem             A jQuery object of the element that triggered the event.
	             * @param object GPLimitCheckboxes The current instance of the GPLimitCheckboxes object.
	             */
                var group = gform.applyFilters( 'gplc_group', $.extend( true, {}, self.groups[ i ] ), fieldId, $elem, self );

                if( self.isGroupMaxed( group ) ) {
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
            var count = $( self.getSelector( group.fields ) ).filter( ':checked:not( .gplc-select-all )' ).length;
            return count >= group.max;
        };

        self.getSelector = function( fieldIds ) {
            var selectors = [];

            for( var i = 0; i < fieldIds.length; i++ ) {
                var fieldId = fieldIds[i];

                if (!(fieldId in self.triggers)) {
                    continue;
                }

                selectors.push( self.triggers[fieldId].selector );
            }

            return selectors.join( ', ' );
        };

        self.getCheckboxesByFieldIds = function( fieldIds ) {
            var fieldIdsJoined = fieldIds.join(',');

            if ( !(fieldIdsJoined in self.checkboxElemCache) ) {
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