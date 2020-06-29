jQuery( function ( $ ) {
    var wrapper  = $( '#yith-wcgpf-custom-fields-tab-wrapper' ),
        save_btn = $( '#yith-wcgpf-custom-fields-tab-actions-save' ),
        ajax_request;

    wrapper.on( 'click', '.yith-wcgpf-add-row', function ( e ) {
            var current_target = $( e.target ),
                parent         = current_target.closest( '.yith-wcgpf-custom-field-wrap' ),
                parent_clone   = parent.clone();

            parent_clone.find( 'input:text' ).val( '' );
            parent.after( parent_clone );
        } )

        .on( 'click', '.yith-wcgpf-delete-row', function ( e ) {
            var number_of_custom_fields = wrapper.find( '.yith-wcgpf-custom-field-wrap' ).length,
                current_target          = $( e.target ),
                parent                  = current_target.closest( '.yith-wcgpf-custom-field-wrap' );

            if ( number_of_custom_fields > 1 ) {
                parent.remove();
            }else{
                parent.find( 'input:text' ).val( '' );
            }
        } );

    save_btn.on('click',this, function( e ) {
        e.preventDefault(),
        $('#ywcgpf-form').block({message:null, overlayCSS:{background:"#fff",opacity:.6}});
        var custom_fields = $("input[name='yith-wcgpf-custom-field[]']").map(function(){return $(this).val();}).get();
        var post_data = {
            'custom_fields': custom_fields ,
            action: 'yith_wcgpf_save_custom_fields'
        };

        $.ajax({
            type    : "POST",
            data    : post_data,
            url     : yith_wcgpf_custom_fields_tab_js.ajaxurl,
            success : function ( response ) {
                $('#ywcgpf-form').unblock();
            },
            complete: function () {
            }
        });
    });

    $('.yith-wcgpf-download-feed-file').on('click',function(){

        var post_id = $(this).data('feed');
        var type = $(this).data('type');
        proccessfeed(post_id,type);

    });

    function proccessfeed(post_id,type,limit,offset,products) {

        if (typeof(offset)==='undefined') offset = 0;
        if (typeof(limit)==='undefined') limit = 0;
        if (typeof(products)==='undefined') products = 0;
        var post_data = {
            'limit' : limit,
            'offset': offset,
            'products' :products,
            'post_id' : post_id,
            'type'    : type,
            //security: object.search_post_nonce,
            action: 'yith_wcgpf_generate_feed_file'
        };
        $(this).unblock();
        $(this).block({message:'Generating feed...', overlayCSS:{background:"#fff",opacity:.6}});
        console.log(post_data);
        $.ajax({
            url : yith_wcgpf_custom_fields_tab_js.ajaxurl,
            type : 'post',
            data    : post_data,
            success : function(response) {
                if(response.success) {
                    console.log(response.data.limit);
                    console.log(response.data.offset);
                    if( response.data.post_id ){
                        proccessfeed(response.data.post_id,response.data.type,response.data.limit,response.data.offset,response.data.products);

                    }else{
                        location.reload();
                    }
                }
            },
            error:function (response) {
                console.log("ERROR");
                console.log(response);
                return false;
            }
        });
    }
} );