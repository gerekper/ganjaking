
var GPLimitChoices;

( function( $ ) {

    GPLimitChoices = function( args ) {

        var self = this;

        // copy all args to current object: (list expected props)
        for( prop in args ) {
            if( args.hasOwnProperty( prop ) )
                self[prop] = args[prop];
        }

        self.init = function() {

            if( typeof gf_global != 'undefined' ) {
                gf_global[ 'GPLimitChoices_' + self.formId ] = self;
            }

        };

        self.init();

        // # Statics

        GPLimitChoices.getChoiceCountLeft = function( formId, fieldId, choiceValue ) {

            var $input  = $( '#input_' + formId + '_' + fieldId ),
                gplcObj = gf_global[ 'GPLimitChoices_' + formId ];

            // if user deletes field, conditional logic rule can still be applied for field that does not exist
            if( ! gplcObj.data[ fieldId ] ) {
                return;
            }

            var choices = gplcObj.data[ fieldId ].choices;

            if( typeof choiceValue == 'undefined' ) {

                // radio
                if( $input.hasClass( 'gfield_radio' ) ) {
                    choiceValue = $input.find( 'input:checked' ).val();
                }
                // select
                else {
                    choiceValue = $input.val();
                    if( ! choiceValue ) {
                        // ignore disabled options and value-less options (like the GF1.9's placeholder option)
                        choiceValue = $input.find( 'option:not( :disabled, [value=""] )' ).eq(0).val();
                    }
                }

                // split up product-based values (i.e. "value|price") to get just the "value"
                if( choiceValue ) {
                    choiceValue = choiceValue.split( '|' )[0];
                }

            }

            var remaining = false;

            $.each( choices, function( i, choice ) {
                if( choice.value == choiceValue ) {
                    remaining = choice.limit - choice.count < 0 ? 0 : choice.limit - choice.count;
                    return false;
                }
            } );

            return remaining;
        };

        GPLimitChoices.isFieldExhausted = function( formId, fieldId ) {

            var gplcObj     = gf_global[ 'GPLimitChoices_' + formId ],
                isExhausted = gplcObj.data[ fieldId ].isExhausted;

            return isExhausted;
        }

    };

    gform.addFilter( 'gform_is_value_match', function( isMatch, formId, rule ) {

        if( rule.value == '__return_true' ) {
            return true;
        } else if( rule.value == '__return_false' ) {
            return false;
        }

        // check for actual field IDs cheaply
        // also make sure GPLimitChoices has been initialized (will not be if prepopulating a value)
        if( ! isNaN( parseInt( rule.fieldId ) ) || ! GPLimitChoices.getChoiceCountLeft )
            return isMatch;

        // check of our gplc_limit_xx tag
        var regex = /(gplc_count_remaining)_([0-9]+)/,
            match = regex.exec( rule.fieldId );

        if( ! match ) {
            return isMatch;
        }

        var fieldId = match[2],
            limit   = GPLimitChoices.getChoiceCountLeft( formId, fieldId );

        if( limit === false && GPLimitChoices.isFieldExhausted( formId, fieldId ) ) {
            limit = 0;
        }

        return gf_matches_operation( limit + '', rule.value, rule.operator );
    } );

} )( jQuery );