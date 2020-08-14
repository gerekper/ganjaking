jQuery(document).ready(function ($) {

    $( document ).on('click', ".yith_wc_ywar_convert_reviews_ajax_method", function (e) {
        e.preventDefault();

        $( '.forminp .convert-reviews' ).hide();
        $( '#yith_wc_ywar_converting_process' ).show();

        if ( $( '#yith_ywar_converting_done_once' ).val() == 1 ){

            $( '#yith_wc_ywar_reviews_checked' ).text( 0 );

            $( '#yith_wc_ywar_reviews_converted' ).text( 0 );

            $( '#yith_wc_ywar_converting_process' ).css( 'margin-top', '0px' );
            $( '#yith_wc_ywar_converting_process .yith_ywar_converting_first_div' ).show();
            $( '#yith_wc_ywar_converting_process .yith_ywar_converting_loader_bar' ).show();

        }

        yith_wc_ywar_convert_previous_reviews( 50, 0 );

    });

    function yith_wc_ywar_convert_previous_reviews( limit, offset ){

        if ( typeof( offset ) === 'undefined' ) offset = 0;
        if ( typeof( limit )=== 'undefined' ) limit = 50;

        var post_data = {
            limit : limit,
            offset: offset,
            metakey : 'rating',
            action: 'yith_wc_ywar_import_previous_reviews'
        };

        $.ajax({
            url : ywar_convert.ajax_url,
            type : 'post',
            data    : post_data,
            success : function( response ) {
                if( response.success ) {

                    if( response.data.continue ){

                        $( '#yith_wc_ywar_reviews_checked' ).text( parseInt( $( '#yith_wc_ywar_reviews_checked' ).text() ) + parseInt( response.data.reviews_checked ) );

                        $( '#yith_wc_ywar_reviews_converted' ).text( parseInt( $( '#yith_wc_ywar_reviews_converted' ).text() ) + parseInt( response.data.reviews_converted ) );

                        yith_wc_ywar_convert_previous_reviews( response.data.limit, response.data.offset );

                    }
                    else{

                        $( '#yith_ywar_converting_done_once' ).val( 1 );

                        $( '#yith_wc_ywar_reviews_checked' ).text( parseInt( $( '#yith_wc_ywar_reviews_checked' ).text() ) + parseInt( response.data.reviews_checked ) );

                        $( '#yith_wc_ywar_reviews_converted' ).text( parseInt( $( '#yith_wc_ywar_reviews_converted' ).text() ) + parseInt( response.data.reviews_converted ) );

                        $( '#yith_wc_ywar_converting_process .yith_ywar_converting_first_div' ).hide();
                        $( '#yith_wc_ywar_converting_process .yith_ywar_converting_loader_bar' ).hide();

                        $( '#yith_wc_ywar_converting_process' ).css( 'margin-top', '20px' );

                        $( '.forminp .convert-reviews' ).show();
                    }
                }
            },
            error: function ( response ) {
                console.log( "ERROR" );
                console.log( response );
                return false;
            }
        });

    }

});