/* global redux_change, wp, redux */



(function( $ ) {

    "use strict";



    redux.field_objects = redux.field_objects || {};

    redux.field_objects.custom_fonts = redux.field_objects.custom_fonts || {};



    redux.field_objects.custom_fonts.init = function( selector ) {

        if ( !selector ) {

            selector = $( document ).find( ".redux-group-tab:visible" ).find( '.redux-container-custom_fonts:visible' );

        }

        $( selector ).each(

            function() {

                // Upload media button

                $( this ).find( '.media_add_font' ).unbind().on(

                    'click', function( e ) {

                        e.preventDefault();

                        redux.field_objects.custom_fonts.addFont( $( this ).parents( 'fieldset.redux-field:first' ) );

                        return false;

                    }

                );



                $( this ).find( '.fontDelete' ).on(

                    'click', function( e ) {

                        e.preventDefault();

                        redux.field_objects.custom_fonts.removeFont( $( this ) );

                        return false;

                    }

                );

            }

        );



    };





    redux.field_objects.custom_fonts.addFont = function( selector ) {

        // Add a file via the wp.media function

        var parent = selector.parents( 'td:first' );

        var tr = selector.parents( 'tr:first' );

        var frame;

        var jQueryel = jQuery( this );

        // If the media frame already exists, reopen it.

        if ( frame ) {

            frame.open();

            return;

        }

        // Create the media frame.

        frame = wp.media(

            {

                multiple: true,

                library: {

                    type: ['application', 'font'] //Only allow zip files

                },

                // Set the title of the modal.

                title: jQueryel.data( 'choose' ),

                // Customize the submit button.

                button: {

                    // Set the text of the button.

                    text: jQueryel.data( 'update' )

                    // Tell the button not to close the modal, since we're

                    // going to refresh the page when the image is selected.

                }

            }

        );

        frame.on(

            'click', function() {

                //console.log( 'Hello' );

            }

        );

        // When an image is selected, run a callback.

        frame.on(

            'select', function() {

                // Grab the selected attachment.

                var attachment = frame.state().get( 'selection' ).first();



                frame.close();

                if ( attachment.attributes.type !== 'application' && attachment.attributes.type !== 'font' ) {

                    return;

                }



                var nonce = jQuery( selector ).find( '.media_add_font' ).attr( "data-nonce" );

                var data = {

                    action: "redux_custom_fonts",

                    nonce: nonce,

                    attachment_id: attachment.id,

                    title: attachment.attributes.title,

                    mime: attachment.attributes.mime

                };



                if ( data.mime == "application/zip " ) {

                    var status = "Unzipping archive and generating any missing font files.";

                } else {

                    var status = "Converting font file.";

                }

                //jQuery.blockUI( {message: '<h1>' + redux.args.please_wait + '...</h1>'} );



                var overlay = $( document.getElementById( 'redux_ajax_overlay' ) );

                overlay.fadeIn();



                // Add the loading mechanism

                var $notification_bar = jQuery( document.getElementById( 'redux_notification_bar' ) );

                $notification_bar.slideUp();

                jQuery( '.redux-save-warn' ).slideUp();

                jQuery( '.redux_ajax_save_error' ).slideUp(

                    'medium', function() {

                        jQuery( this ).remove();

                    }

                );





                jQuery.ajax(

                    {

                        type: "post",

                        dataType: "json",

                        url: ajaxurl,

                        data: data,

                        error: function( response ) {

                            overlay.fadeOut( 'fast' );

                            jQuery( '.wrap h2:first' ).parent().append( '<div class="error redux_ajax_save_error" style="display:none;"><p>There was an error processing your fonts. Please try again.</p></div>' );

                            jQuery( '.redux_ajax_save_error' ).slideDown();

                            jQuery( "html, body" ).animate( {scrollTop: 0}, "slow" );

                        },

                        success: function( response ) {

                            if ( response.type && response.type == "success" ) {



                                window.location.reload();

                            } else {

                                alert( 'There was an error deleting your font: ' + response.msg );

                            }

                            overlay.fadeOut( 'fast' );

                        }

                    }

                );

            }

        );

        // Finally, open the modal.

        frame.open();



    };



    redux.field_objects.custom_fonts.removeFont = function( selector ) {

        var parent = selector.parents( 'td:first' );

        var tr = selector.parents( 'tr:first' );

        var data = selector.data();

        data.action = "redux_custom_fonts";

        data.nonce = selector.parents( '.redux-container-custom_fonts:first' ).find( '.media_add_font' ).attr( "data-nonce" );



        jQuery.post(

            ajaxurl, data, function( response ) {

                response = JSON.parse( response );



                if ( response.type && response.type == "success" ) {



                    var rowCount = parent.parents( 'tbody:first' ).find( 'tr' ).length;



                    if ( rowCount == 1 ) {

                        parent.parents( 'table:first' ).slideUp().remove();

                    } else {

                        tr.wrap( "<div></div>" );

                        tr.parent().slideUp().remove();

                    }

                } else {

                    alert( 'There was an error deleting your font: ' + response.msg );

                }

            }

        );

    };



})( jQuery );

