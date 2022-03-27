/**
 * GP Auto List Field - Frontend Scripts
 */

( function( $ ) {

    window.GPALFAdmin = {

        init: function() {

            GPALFAdmin.registerFieldSettingsClass();

            $( document ).bind( 'gform_load_field_settings', function( event, field, form ) {

                if( ! GPALFAdmin.isSupportedFieldType( GetInputType( field ) ) ) {
                    return;
                }

                GPALFAdmin.toggleSettings( field.gpalfEnable == true );

            } );

        },

        toggleSettings: function( isEnabled ) {

            var $enable        = $( '#gpalf-enable' ),
                $childSettings = $( '#gpalf-child-settings' ),
                $sourceField   = $( '#gpalf-source-field' ),
                field          = GetSelectedField();

            isEnabled = typeof isEnabled == 'undefined' ? $enable.is( ':checked' ) : isEnabled;

            SetFieldProperty( 'gpalfEnable', isEnabled );
            $enable.prop( 'checked', isEnabled );

            if( isEnabled ) {

                $childSettings.slideDown();

                GPALFAdmin.populateFieldsSelect( $sourceField, GPALFAdmin.getFieldsByType( GPALFAdminData.supportedFieldTypes ) );

                $sourceField.val( field.gpalfSourceField );

            } else {

                $childSettings.slideUp();

                SetFieldProperty( 'gpalfSourceField', '' );

            }

        },

        isSupportedFieldType: function( type ) {
            return $.inArray( type, [ 'list' ] ) != -1;
        },

        registerFieldSettingsClass: function() {
            for( var fieldType in fieldSettings ) {
                if( fieldSettings.hasOwnProperty( fieldType ) && GPALFAdmin.isSupportedFieldType( fieldType ) ) {
                    fieldSettings[ fieldType ] += ', .gpalf-field-setting';
                }
            }
        },

        getFieldsByType: function( types ) {

            if( typeof types != 'object' ) {
                types = [ types ];
            }

            var fields = [];

            for( var i = 0; i < form.fields.length; i++ ) {
                if( $.inArray( GetInputType( form.fields[ i ] ), types ) != -1 && form.fields[i].visibility != 'administrative' && ! form.fields[i].adminOnly ) {
                    fields.push( form.fields[ i ] );
                }
            }

            return fields;
        },

        populateFieldsSelect: function( $elem, fields ) {

            var firstOption = $elem.find( 'option:first' )[0].outerHTML,
                markup      = [ firstOption ];

            for( var i = 0; i < fields.length; i++ ) {
                markup.push( '<option value="{0}">{1}</option>'.format( fields[i].id, fields[i].label ) );
            }

            $elem.html( markup );

        }

    }

    window.GPALFAdmin.init();

} )( jQuery );