jQuery( document ).ready( function( $ ){
    var stripe_mode = $( '#woocommerce_yith-stripe_mode' ),
        save_cards = $( '#woocommerce_yith-stripe_save_cards' ),
        save_cards_mode = $( '#woocommerce_yith-stripe_save_cards_mode' ),
        billing_hosted_fields = $( '#woocommerce_yith-stripe_add_billing_hosted_fields' ),
        billing_fields = $( '#woocommerce_yith-stripe_add_billing_fields' ),
        elements_show_zip = $( '#woocommerce_yith-stripe_elements_show_zip' ),
        config_webhook = $( '#config_webhook' );

    stripe_mode.on( 'change', function(){
        var t = $(this),
            v = t.val();

        if( v === 'standard' ){
            save_cards.closest( 'tr' ).show();
            save_cards_mode.closest( 'tr' ).show();
            billing_hosted_fields.closest( 'tr' ).hide();
            billing_fields.closest( 'tr' ).show();
            elements_show_zip.closest( 'tr' ).hide();
        }
        else if( v === 'hosted' ){
            save_cards.closest( 'tr' ).hide();
            save_cards_mode.closest( 'tr' ).hide();
            billing_hosted_fields.closest( 'tr' ).show();
            billing_fields.closest( 'tr' ).hide();
            elements_show_zip.closest( 'tr' ).hide();
        }
        else if( v === 'elements' ){
            save_cards.closest( 'tr' ).show();
            save_cards_mode.closest( 'tr' ).show();
            billing_hosted_fields.closest( 'tr' ).hide();
            billing_fields.closest( 'tr' ).hide();
            elements_show_zip.closest( 'tr' ).show();
        }
    } ).change();

    config_webhook.on( 'click', function( ev ){
        var t = $(this),
            p = t.closest('p');

        ev.preventDefault();

        $.ajax( {
            beforeSend: function(){
                p.block({message: null, overlayCSS: {background: "#fff", opacity: .6}});
            },
            complete: function(){
                p.unblock();
            },
            data: {
                action: yith_stripe.actions.set_webhook,
                security: yith_stripe.security.set_webhook
            },
            success: function( data ){
                if( data && typeof data.status != 'undefined' ){
                    var noticeClass = data.status ? 'success' : 'error',
                        noticeContent = $( '<p/>', { text: data.message } ),
                        notice = $( '<div/>', { id: 'webhook_notice', class: 'notice notice-' + noticeClass } ),
                        removeContent = function(){
                            $(this).remove();
                        };

                    $( '#webhook_notice' ).fadeOut( removeContent );

                    p.before( notice.append( noticeContent ) );

                    setTimeout( function(){
                        notice.fadeOut( removeContent );
                    }, 3000 );
                }
            },
            url: ajaxurl
        } );
    } );
} );