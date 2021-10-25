/*global redux, redux_opts*/
/*
 * Field Sorter jquery function
 * Based on
 * [SMOF - Slightly Modded Options Framework](http://aquagraphite.com/2011/09/slightly-modded-options-framework/)
 * Version 1.4.2
 */

(function( $ ) {
    "use strict";

    redux = redux || {};

    redux.field_objects = redux.field_objects || {};
    redux.field_objects.gt3_header_builder = redux.field_objects.gt3_header_builder || {};

    var scroll = '';

    $( document ).ready(
        function() {
        	//redux.gt3_header_builder_func();            
            responsive_tab();
            check_is_section_empty();         
        }
    );

    function responsive_tab (){
        jQuery('#gt3_header_builder_id').find('.gt3_header_builder__side.tablet,.gt3_header_builder__side.mobile').hide();
        jQuery('#gt3_header_builder_id').find('.gt3_header_builder__setttings.main_header_cont.tablet,.gt3_header_builder__setttings.main_header_cont.mobile').hide();
        jQuery('body').addClass('gt3_header_desktop');

        jQuery('.gt3_header_builder__responsive').find('.gt3_header_builder__responsive_tab').on('click',function(){
            var element = jQuery(this);
            var tab_name = element.attr('data-tab-name');
            jQuery('body').removeClass('gt3_header_desktop gt3_header_tablet gt3_header_mobile').addClass('gt3_header_'+tab_name);
            element.addClass('active').siblings().removeClass('active');
            element.parents('#gt3_header_builder_id').find('.gt3_header_builder__side').hide();
            element.parents('#gt3_header_builder_id').find('.gt3_header_builder__setttings.main_header_cont').hide();
            if (tab_name == 'desktop') {
                element.parents('#gt3_header_builder_id').find('.gt3_header_builder__side:not(.tablet):not(.mobile)').show();
                element.parents('#gt3_header_builder_id').find('.gt3_header_builder__setttings.main_header_cont:not(.tablet):not(.mobile)').show();
            }else{
                element.parents('#gt3_header_builder_id').find('.gt3_header_builder__side.'+tab_name).show();
                element.parents('#gt3_header_builder_id').find('.gt3_header_builder__setttings.main_header_cont.'+tab_name).show();
            }
            
        })
    }

    function check_is_section_empty(){
        jQuery('.gt3_header_builder__side').each(function(){
            // skip section with all element 
            if (jQuery(this).hasClass('gt3_header_builder__side--all')) {
                return true;
            }
            if (jQuery(this).find('.sortlist_gt3_header_builder_id.section_empty').length == 3) {
                jQuery(this).addClass('empty_section')
            }else{
                jQuery(this).removeClass('empty_section')
            }

        })
    }

    redux.field_objects.gt3_header_builder.init = function( selector ) {



        if ( !selector ) {
            selector = $( document ).find( ".redux-group-tab:visible" ).find( '.redux-container-gt3_header_builder:visible' );
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
                
/*                if ( parent.hasClass( 'redux-field-init' ) ) {
                    parent.removeClass( 'redux-field-init' );
                } else {
                    return;
                }*/
                
                /**    Sorter (Layout Manager) */
                el.find( '.redux-sorter' ).each(
                    function() {
                        var id = $( this ).attr( 'id' );

                        el.find( '#' + id ).find( 'ul' ).sortable(
                            {
                                items: 'li',
                                placeholder: "placeholder",
                                connectWith: '.sortlist_' + id,
                                opacity: 0.95,
                                scroll: false,
                                out: function( event, ui ) {
                                    if ( !ui.helper ) return;
                                    if ( ui.offset.top > 0 ) {
                                        scroll = 'down';
                                    } else {
                                        scroll = 'up';
                                    }
                                    /*redux.field_objects.sorter.scrolling( $( this ).parents( '.redux-field-container:first' ) );*/

                                },
                                over: function( event, ui ) {
                                    scroll = '';
                                },

                                deactivate: function( event, ui ) {
                                    scroll = '';
                                },

                                stop: function( event, ui ) {
                                    /*var sorter = redux.gt3_header_builder[$( this ).attr( 'data-id' )];

                                    console.log(sorter);
                                    var id = $( this ).find( 'h3' ).text();

                                    if ( sorter.limits && id && sorter.limits[id] ) {
                                        if ( $( this ).children( 'li' ).length >= sorter.limits[id] ) {
                                            $( this ).addClass( 'filled' );
                                            if ( $( this ).children( 'li' ).length > sorter.limits[id] ) {
                                                $( ui.sender ).sortable( 'cancel' );
                                            }
                                        } else {
                                            $( this ).removeClass( 'filled' );
                                        }
                                    }*/
                                },

                                update: function( event, ui ) {
                                    //var sorter = redux.gt3_header_builder[$( this ).attr( 'data-id' )];
                                    var id = $( this ).find( 'h3' ).text();
                                    if (!jQuery(event.target).hasClass('all')) {
                                        if (jQuery(event.target).find('li.sortee').length  == 0) {
                                            jQuery(event.target).addClass('section_empty');
                                        }else{
                                            jQuery(event.target).removeClass('section_empty');
                                        }
                                        setTimeout(function(){
                                            check_is_section_empty();
                                        },400)                                        
                                    }
                                    
                                    $( this ).find( '.position' ).each(
                                        function() {
                                            //var listID = $( this ).parent().attr( 'id' );
                                            var listID = $( this ).parent().attr( 'data-id' );
                                            var parentID = $( this ).parent().parent().attr( 'data-group-id' );

                                            redux_change( $( this ) );

                                            var optionID = $( this ).parent().parent().parent().parent().parent().attr( 'id' );

                                            $( this ).prop(
                                                "name",
                                                redux.args.opt_name + '[' + optionID + '][' + parentID + '][content][' + listID + '][title]'
                                            );
                                        }
                                    );                                    
                                    $( this ).find( '.sortee .has_settings ' ).each(
                                        function() {
                                            //var listID = $( this ).parent().attr( 'id' );
                                            var listID = $( this ).parent().attr( 'data-id' );
                                            var parentID = $( this ).parent().parent().attr( 'data-group-id' );

                                            redux_change( $( this ) );

                                            var optionID = $( this ).parent().parent().parent().parent().parent().attr( 'id' );

                                            $( this ).prop(
                                                "name",
                                                redux.args.opt_name + '[' + optionID + '][' + parentID + '][content][' + listID + '][has_settings]'
                                            );
                                        }
                                    );
                                }
                            }
                        );
                        el.find( ".redux-sorter" ).disableSelection();
                    }
                );
            }
        );
    };

})( jQuery );