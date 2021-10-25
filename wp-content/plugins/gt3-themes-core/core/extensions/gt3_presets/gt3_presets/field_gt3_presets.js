/* global confirm, redux, redux_change */

/*global redux_change, redux*/

(function( $ ) {
    "use strict";

    redux.field_objects = redux.field_objects || {};
    redux.field_objects.gt3_presets = redux.field_objects.gt3_presets || {};

    $( document ).ready(
        function() {
            //redux.field_objects.image_select.init();
        }
    );

    redux.field_objects.gt3_presets.init = function( selector ) {

        if ( !selector ) {
            selector = $( document ).find( ".redux-group-tab:visible" ).find( '.redux-container-gt3_presets:visible' );
        }

        $( selector ).each(
            function() {
                var el = $( this );
                var parent = el;
                if ( !el.hasClass( 'redux-field-container' ) ) {
                    parent = el.parents( '.redux-field-container:first' );
                }
                if ( parent.is( ":hidden" ) ) { // Skip hidden fields
                    return;
                }
                if ( parent.hasClass( 'redux-field-init' ) ) {
                    parent.removeClass( 'redux-field-init' );
                } else {
                    return;
                }

                /*el.find( '.redux-gt3_presets label .gt3_presets__operation_item.operation_remove' ).click(
                    function( e ) {
                        console.log('remove')
                        var element = jQuery(this);
                        element.parents('li.redux-gt3_presets').remove();

                })*/

                el.find( '.gt3_preset__wrapper .gt3_preset__item_action' ).on(
                    'click', function() {
                        var element = jQuery(this);
                        var element_parent = element.parents('.gt3_preset__wrapper');
                        element_parent.find('.gt3_preset_item_value')

                        var data = element_parent.find('.gt3_preset_item_value').data( 'presets' );
                        var current_item_count = element_parent.find('.gt3_preset_item_value').attr('data-presets-id')
                        var answer = confirm( redux.args.preset_confirm );
                        if ( answer ) {
                            window.onbeforeunload = null;
                            if ( $( '#import-code-value' ).length === 0 ) {
                                $( this ).append( '<textarea id="import-code-value" style="display:none;" name="' + redux.args.opt_name + '[import_code]">' + JSON.stringify( data ) + '</textarea>' );
                            } else {
                                $( '#import-code-value' ).val( JSON.stringify( data ) );
                            }
                            element.parents('.redux-gt3_preset-accordion').find('#gt3_header_builder_presets-current_active').val(parseInt(current_item_count));
                            if ( $( '#publishing-action #publish' ).length !== 0 ) {
                                $( '#publish' ).click();
                            } else {
                                $( '#redux-import' ).click();
                            }
                        }


                    }
                );

                el.find( '.gt3_preset__wrapper .gt3_presets__operation_item.operation_remove' ).on(
                    'click', function() {
                        var answer = confirm( 'Press OK to delete preset, Cancel to leave' );
                        redux_change( $( this ) );

                        var element = jQuery(this);
                        var element_parent = element.parents('.gt3_preset__wrapper');
                        element_parent.remove();
                    }
                );

                el.on(
                    'click', '.gt3_preset__wrapper .gt3_presets__operation_item.operation_edit', function() {
                        var element = jQuery(this);
                        var element_parent = element.parents('.gt3_preset__wrapper');
                        element_parent.addClass('on_edit')
                        var title = element_parent.find('.gt3_preset_item_title')
                        title.removeAttr('readonly').focus();
                    }
                );

                el.find('.gt3_preset_item_title').focusout(function() {
                    var element = jQuery(this);
                    var element_parent = element.parents('.gt3_preset__wrapper');
                    element_parent.removeClass('on_edit')
                    element.attr('readonly', true);
                })

                el.on(
                    'click', '.gt3_preset__wrapper .gt3_presets__operation_item.operation_clone', function() {
                        redux_change( $( this ) );
                        var element = jQuery(this);
                        var element_parent = element.parents('.redux-gt3_preset-accordion');
                        var newItem = element.parents('.gt3_preset__wrapper').clone(true);
                        var title_val = jQuery(newItem).find('.gt3_preset_item_title').val();
                        jQuery(newItem).find('.gt3_preset_item_title').val(title_val+' Clone')

                        var count = element_parent.find('.gt3_preset_def_options .items_count').val();
                        count = parseInt(count) + 1;
                        jQuery(newItem).removeClass('active');
                        var data_presets_id = jQuery(newItem).find('.gt3_preset_item_value').attr('data-presets-id');
                        /*if (data_presets_id == '0') {
                            var def_opt = element_parent.find('.gt3_preset_def_options .gt3_preset_def_preset').val();
                            jQuery(newItem).find('.gt3_preset_item_value').val(def_opt).attr('data-presets', def_opt)
                            console.log(data_presets_id)
                        }*/

                        element_parent.find('.gt3_preset_def_options .items_count').val(parseInt(count))
                        jQuery(newItem).find('.gt3_preset_item_value').attr('data-presets-id',parseInt(count))
                        jQuery(newItem).find('input[type="text"], input[type="hidden"]').each(
                           function() {
                                jQuery( this ).attr(
                                    "name", jQuery( this ).attr( "name" ).replace( /[0-9]+(?!.*[0-9])/, count )
                                ).attr( "id", $( this ).attr( "id" ).replace( /[0-9]+(?!.*[0-9])/, count ) );
                           }
                        );



                        if (!jQuery(newItem).find('.gt3_presets__operation_item.operation_edit').length) {
                            jQuery(newItem).find('.gt3_presets__operation').append("<div class='gt3_presets__operation_item operation_edit' title='Edit'><i class='fa fa-pencil' aria-hidden='true'></i></div>")
                        }

                        if (!jQuery(newItem).find('.gt3_presets__operation_item.operation_remove').length) {
                            jQuery(newItem).find('.gt3_presets__operation').append("<div class='gt3_presets__operation_item operation_remove' title='Remove'><i class='fa fa-times' aria-hidden='true'></i></div>")
                        }

                        jQuery(newItem).insertBefore(jQuery(this).parents('.redux-gt3_preset-accordion').find('.gt3_preset_def_options'))
                    }
                );

                el.on(
                    'click', '.gt3_preset_add_new_container .gt3_preset_add_new__template', function() {
                        var element = jQuery(this);
                        var element_parent = element.parents('.gt3_preset_add_new_container');
                        element.addClass('active').siblings().removeClass('active');
                        var input = element_parent.find('.gt3_preset_add_new__submit_name')
                        input.focus();
                    }
                );

                el.on(
                    'click', '.gt3_preset_add_new_container .gt3_preset_add_new__submit_button', function() {
                        redux_change( $( this ) );
                        var element = jQuery(this);
                        var element_parent = element.parents('.redux-gt3_preset-accordion');
                        var template = element.parents('.gt3_preset_add_new_container').find('.gt3_preset_add_new__template.active');
                        var def_opt = '';

                        var newItem = element_parent.find('.gt3_preset__wrapper').first().clone(true);


                        var title_val = element.prev('.gt3_preset_add_new__submit_name').val();
                        jQuery(newItem).find('.gt3_preset_item_title').val(title_val)

                        var count = element_parent.find('.gt3_preset_def_options .items_count').val();
                        /*if (template.hasClass('default')) {
                            def_opt = element_parent.find('.gt3_preset_def_options .gt3_preset_def_preset').val();
                        }else{
                            def_opt = template.find('.gt3_preset_add_new__template_holder').attr('data-preset');
                        }*/

                        def_opt = template.find('.gt3_preset_add_new__template_holder').attr('data-preset');

                        count = parseInt(count) + 1;
                        jQuery(newItem).removeClass('active');
                        jQuery(newItem).find('.gt3_preset_item_value').val(def_opt).attr('data-presets', def_opt)
                        element_parent.find('.gt3_preset_def_options .items_count').val(parseInt(count))
                        jQuery(newItem).find('.gt3_preset_item_value').attr('data-presets-id',parseInt(count))
                        jQuery(newItem).find('input[type="text"], input[type="hidden"]').each(
                           function() {
                                jQuery( this ).attr(
                                    "name", jQuery( this ).attr( "name" ).replace( /[0-9]+(?!.*[0-9])/, count )
                                ).attr( "id", $( this ).attr( "id" ).replace( /[0-9]+(?!.*[0-9])/, count ) );
                           }
                        );

                        if (!jQuery(newItem).find('.gt3_presets__operation_item.operation_edit').length) {
                            jQuery(newItem).find('.gt3_presets__operation').append("<div class='gt3_presets__operation_item operation_edit' title='Edit'><i class='fa fa-pencil' aria-hidden='true'></i></div>")
                        }

                        if (!jQuery(newItem).find('.gt3_presets__operation_item.operation_remove').length) {
                            jQuery(newItem).find('.gt3_presets__operation').append("<div class='gt3_presets__operation_item operation_remove' title='Remove'><i class='fa fa-times' aria-hidden='true'></i></div>")
                        }

                        jQuery(newItem).insertBefore(jQuery(this).parents('.redux-gt3_preset-accordion').find('.gt3_preset_def_options'))

                        element_parent.find('.gt3_preset_add_new_container').removeClass('active');

                    }
                );

                el.on(
                    'click', '.gt3_preset__container .gt3_preset_add_new', function() {
                    var element = jQuery(this);
                    var element_parent = element.parents('.redux-gt3_preset-accordion');
                    element_parent.find('.gt3_preset_add_new_container').addClass('active');
                });


            }
        );

    };
})( jQuery );
