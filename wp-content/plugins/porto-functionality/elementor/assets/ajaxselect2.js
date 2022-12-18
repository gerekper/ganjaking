jQuery( window ).on( 'elementor:init', function() {
    var $ = jQuery,
        portoAjaxSelect2ControlView = elementor.modules.controls.BaseData.extend( {
            onReady: function() {
                var self = this,
                    $el = self.ui.select,
                    url = $el.attr( 'data-ajax-url' ),
                    condition_name = $el.attr( 'data-condition' );

                $el.select2( {
                    ajax: {
                        url: url,
                        dataType: 'json',
                        data: function( params ) {
                            var args = {
                                s: params.term
                            };
                            if ( typeof $el.attr( 'multiple' ) == 'undefined' ) {
                                args['add_default'] = '1';
                            }
                            if ( condition_name ) {
                                if ( condition_name == 'archive_builder' ) {
                                    args['condition'] = elementor.settings.page.model.get( 'archive_preview_type' );
                                } else {
                                    var $condition_obj = {}, $repeater = $el.closest( '.elementor-repeater-fields' );

                                    if ( $repeater.length ) {
                                        $condition_obj = $repeater.find( 'select[data-setting="' + condition_name + '"]' );
                                    }
                                    if ( $condition_obj.length == 0 ) {
                                        $condition_obj = jQuery( 'select[data-setting="' + condition_name + '"]' );
                                    }
                                    if ( $condition_obj.length ) {
                                        args['condition'] = $condition_obj.val();
                                    } else {
                                        for ( key in elementor.selection.elements ) {
                                            condition_value = elementor.selection.elements[key].settings.attributes[condition_name];
                                            if ( condition_value ) {
                                                args['condition'] = condition_value;
                                            }
                                            break;
                                        }
                                    }
                                }
                            }
                            return args;
                        }
                    },
                    cache: true
                } );

                if ( $el.closest( '.elementor-hidden-control' ).length ) {
                    return;
                }
                var ids = ( typeof self.getControlValue() !== 'undefined' ) ? self.getControlValue() : '';
                if ( ids.isArray ) {
                    ids = self.getControlValue().join();
                }

                var ajax_args = {
                    ids: String( ids )
                };
                $.ajax( {
                    url: url,
                    dataType: 'json',
                    data: ajax_args
                } ).then( function( res ) {

                    if ( null !== res && res.results.length > 0 ) {
                        $.each( res.results, function( i, v ) {
                            $el.append( new Option( v.text, v.id, true, true ) ).trigger( 'change' );
                        } );
                        $el.trigger( {
                            type: 'select2:select',
                            params: {
                                data: res
                            }
                        } );
                    }
                } );
            },
            onBeforeDestroy: function onBeforeDestroy() {
                if ( this.ui.select.data( 'select2' ) ) {
                    this.ui.select.select2( 'destroy' );
                }
                this.$el.remove();
            }
        } );
    elementor.addControlView( 'porto_ajaxselect2', portoAjaxSelect2ControlView );
} );
