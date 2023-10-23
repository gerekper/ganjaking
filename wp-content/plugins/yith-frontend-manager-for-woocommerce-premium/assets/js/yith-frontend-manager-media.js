(function($) {

    $( function() {
        var file_frame; // variable for the wp.media file_frame
        var gallery_frame; // variable for the wp.media file_frame

        // attach a click event (or whatever you want) to some element on your page
        $( '#upload_image_button' ).on( 'click', function( event ) {
            event.preventDefault();

            // if the file_frame has already been created, just reuse it
            if ( file_frame ) {
                file_frame.open();
                return;
            }

            file_frame = wp.media.frames.file_frame = wp.media({
                title: $( this ).data( 'uploader_title' ),
                button: {
                    text: $( this ).data( 'uploader_button_text' ),
                },
                multiple: false // set this to true for multiple file selection
            });

            file_frame.on( 'select', function() {
                attachment = file_frame.state().get('selection').first().toJSON();
                var upload_image_input = $( '#upload_image_input img' );
                $( '#image_url' ).val( attachment.url );
                $( '#attach_id' ).val( attachment.id );
                $( '#upload_image_remove_button' ).fadeIn();
                upload_image_input.attr( 'src', attachment.url );
                upload_image_input.attr( 'srcset', attachment.url );
            });

            file_frame.open();
        });

        $( '#upload_image_remove_button' ).on( 'click', function( event ) {
            $( '#upload_image_input img' ).attr( 'src', '' ).replaceWith( yith_wcfm_media.wc_placeholder_img );
            $( '#upload_image_remove_button' ).fadeOut();
            $( '#attach_id' ).val( '' );
            $( '#image_url' ).val( '' );
            event.preventDefault();
        });

        // Gallery
        $( '#add_product_gallery_image' ).on( 'click', function( event ) {
            event.preventDefault();
            if ( gallery_frame ) {
                gallery_frame.open();
                return;
            }

            gallery_frame = wp.media.frames.file_frame = wp.media({
                title: $( this ).data( 'uploader_title' ),
                button: {
                    text: $( this ).data( 'uploader_button_text' ),
                },
                multiple: true
            });

            gallery_frame.on( 'select', function() {
                image_ids = gallery_frame.state().get('selection');

                image_ids.map( function( attachment ) {
                    attachment = attachment.toJSON();
                    var newImage        = '<span class="image"><span class="dashicons dashicons-dismiss remove_image"></span><img width="150" height="150" src="' + attachment.url + '" class="attachment-thumbnail size-thumbnail"></span>',
                        product_gallery = $('#product_gallery');

                    newImage += '<input type="hidden" name="product_gallery[]" value="' + attachment.id + '" />';

                    product_gallery.append( newImage );

                    var placeholder = product_gallery.find( '.woocommerce-placeholder' );

                    if( typeof placeholder != 'undefined' ){
                        placeholder.remove();
                    }
                });
            });

            gallery_frame.open();
        });

        $( 'body' ).on( 'click', '.remove_image', function( ) {
            var product_gallery      = $( '#product_gallery' ),
                current_shown_images = product_gallery.find( 'span.image' ).length;

            if( ( current_shown_images - 1 ) == 0 ){
                product_gallery.append( yith_wcfm_media.wc_placeholder_img )
            }

            $(this).parent('.image').remove();
        });
    });

})(jQuery);
