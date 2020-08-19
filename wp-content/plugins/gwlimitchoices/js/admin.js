
var GPLCAdmin;

( function( $ ) {

    GPLCAdmin = {

        addRuleFieldAfter: function( ruleField, targetLabel, ruleFields ) {

            var targetIndex;

            $.each( ruleFields, function( i, _ruleField ) {
                if( targetLabel == _ruleField.label ) {
                    targetIndex = i;
                    return false;
                }
            } );

            ruleFields.splice( targetIndex + 1, 0, ruleField );

            return ruleFields;
        }

    }

    gform.addFilter( 'gform_conditional_logic_fields', function( ruleFields, form, selectedFieldId ) {

        jQuery.each( form.fields, function( i, field ) {

            var isCondLogicSupportedFieldType = $.inArray( GetInputType( field ), [ 'checkbox', 'multiselect' ] ) == -1;

            if( field['gwlimitchoices_enableLimits'] && isCondLogicSupportedFieldType ) {
                GPLCAdmin.addRuleFieldAfter( {
                    label: '(Remaining) ' + field.label,
                    value: 'gplc_count_remaining_' + field.id
                }, field.label, ruleFields );
            }

        } );

        return ruleFields;
    } );

} )( jQuery );

