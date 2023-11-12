( function( $ ) {
    jQuery( window ).on( 'elementor:init', function() {
        
        var preset_data = elementor.modules.controls.BaseData.extend({

            isUaelPreset: function() {         

                return "presets_options" === this.model.get( "name" ) && -1 !== this.getWidgetName().indexOf( "uael-" );
            },
            onReady: function() {

                window.uaelPresets = window.uaelPresets || {};
                this.fetchPresets();
            },
            getWidgetName: function() {

                return this.container.settings.get( "widgetType" );
            },
            isPresetFetched: function() {

                if( undefined !== window.uaelPresets[this.getWidgetName()] ){

                    return window.uaelPresets[this.getWidgetName()];
                } else {

                    return false;
                }
            },
            fetchPresets: function() {

                if( this.isUaelPreset() && !this.isPresetFetched() && this.getWidgetName() ){

                    var current_widget = this;

                    $.ajax({

                        url: uael_presets.ajaxUrl,
                        type: 'POST',
                        data: {
                            action: "uael_widget_presets",
                            widget: this.getWidgetName(),
                            nonce: uael_presets.nonce,                            
                        }
                    }).done( function( result ) {
                        
                        if( result.success ){
                            current_widget.setPresets( result.data );
                        }

                    });
                    
                }
            },
            setPresets: function( widget_json ) {

                window.uaelPresets[this.getWidgetName()] = JSON.parse( widget_json );
            },
            getPresets: function() {
                    
                if( undefined !== window.uaelPresets[this.getWidgetName()] ){
                    
                    return window.uaelPresets[this.getWidgetName()];
                } else {
                    
                    return {};
                }
            },
            onBaseInputChange: function( event ) {

                this.constructor.__super__.onBaseInputChange.apply( this, arguments );

                if ( this.isUaelPreset() ) {

                    event.stopPropagation();

                    var presets_list = this.getPresets();

                    if( "" == event.currentTarget.value ) {

                        if( undefined !== presets_list["default"] ) {
                            this.applyPresets( presets_list["default"] );  
                        } else {                            
                            this.defaultStyle( this.container.settings.defaults );   
                        }
                    } else if( undefined !== presets_list[event.currentTarget.value] ){

                        this.applyPresets( presets_list[event.currentTarget.value] );                               
                    }
                }
            },
            defaultStyle: function ( style ) {               
                this.applyPresets( style );
            },
            applyPresets: function ( presets_json ) {

                var e = elementor.getPanelView().getCurrentPageView().getOption( "editedElementView" );
                   
                //Reset style.
                $e.run("document/elements/reset-style", {
                        container:e.getContainer()
                    }
                );

                var current_controls = this.container.settings.controls,
                    current_widget = this,
                    data_array = {},
                    settings = this.container.settings,
                    classControls = settings.getClassControls(),
                    current_widget_view = this.container.view.$el;

                var edited_controls = e.model._previousAttributes.settings._previousAttributes;

                _.each( current_controls, function ( current_control, controls_index ) {
                    
                    if ( current_widget.model.get( "name" ) !== controls_index && !_.isUndefined( presets_json[controls_index] ) ) {
                        
                        if ( current_control.is_repeater && current_control.default.length > 1 ) {

                            var cloned_widget = current_widget.container.settings.get( controls_index ).clone();
                            
                            cloned_widget.each( function ( current_control, data_array ) {
                                _.isUndefined(presets_json[controls_index][data_array]) || _.each( current_control.controls, function ( current_control, current_control_index ) {
                                        current_widget.isStyleTransferControl( current_control ) && cloned_widget.at( data_array ).set( current_control_index, presets_json[controls_index][data_array][current_control_index] );
                                    });
                            });

                            data_array[controls_index] = cloned_widget;

                            current_widget.isStyleTransferControl(current_control) && ( data_array[controls_index] = presets_json[controls_index] );


                        } else if( ( '' !== presets_json[controls_index] ) && current_widget.isContentTransferControl( current_control ) ) {
                            var edited_value = edited_controls[controls_index];

                            if( ( undefined !== typeof edited_value && '' !== edited_value && edited_value !== presets_json[controls_index] ) ) {
                                data_array[controls_index] = edited_value;
                            } else {
                                data_array[controls_index] = presets_json[controls_index];
                            }

                        } else {
                            data_array[controls_index] = presets_json[controls_index];
                        }
                    }
                });

                // Remove the previous prefix class.
                _.each(classControls, function (control) {

                    var previousClassValue = settings._previousAttributes[control.name];

                    if (control.classes_dictionary) {

                        if ( undefined !== control.classes_dictionary[previousClassValue] ) {
                            
                            previousClassValue = control.classes_dictionary[previousClassValue];
                        }
                    }

                    current_widget_view.removeClass(control.prefix_class + previousClassValue);
                });

                this.container.settings.setExternalChange( data_array );
                this.container.view.render();
            },
            // Use this only for repeater fields.
            isStyleTransferControl: function ( control ) {

                if ( undefined !== control.style_transfer ) {
                    return control.style_transfer;
                }

                return 'content' !== control.tab || control.selectors || control.prefix_class || control.return_value;
            },
            // Check if current field is non-editable.
            isContentTransferControl: function ( control ) {

                var control_type = control.type;

                if( 'text' === control_type || 'textarea' === control_type || 'icons' === control_type || 'wysiwyg' === control_type || 'media' === control_type || 'url' === control_type ) {

                    if ( true === control.style_transfer ) {
                        return false;
                    }

                    return true;
                }

                return false;
            }
        });

        elementor.addControlView( "uael-presets-select", preset_data );
       
    });
} )( jQuery ); 
