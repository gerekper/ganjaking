jQuery( function ( $ ) {
    'use strict' ;
    var FPFrontendScripts = {
        init : function ( ) {
            if ( frontendscripts_params.enqueue_footable === '1' ) {
                this.table_as_footable( ) ;
            }

            this.trigger_on_page_load() ;
            this.toggle_birthday_date_field_on_myaccount() ;
            this.toggle_birthday_date_field_on_checkout();
            $( ".rs_success_msg_for_pointurl" ).fadeOut( 10000 ) ;
            $( ".sk_failure_msg_for_pointsurl" ).fadeOut( 10000 ) ;
            $( '.woocommerce_booking_variations' ).hide() ;
            $( '#value_variable_product' ).hide() ;
            $( '.rs-minimum-quantity-error-variable' ).hide();
            $( document ).on( 'change' , 'input, wcva_attribute_radio' , this.message_for_variations ) ;
            $( document ).on( 'click' , '.share_wrapper' , this.fb_share ) ;
            $( document ).on( 'click' , '#refgeneratenow' , this.generated_referral_link ) ;
            $( document ).on( 'click' , '.referrals, .footable-toggle' , this.display_social_icons_reward_table ) ;
            $( document ).on( 'click' , 'a.add_removed_free_product_to_cart' , this.unset_removed_free_products ) ;
            $( document ).on( 'click' , '.rs_copy_clipboard_image' , this.copy_to_clipboard ) ;
            $( '#wc-bookings-booking-form' ).on( 'change' , 'input, select' , this.message_for_booking ) ;
            $( document ).on( 'change' , '#rs_duration_type' , this.toggle_my_reward_table_date_filter ) ;
            $( document ).on( 'click' , '#rs_submit' , this.prevent_submit_in_my_reward_table ) ;
            $( document ).on( 'click' , '.toggle-email-share-button' , this.toggle_referral_email ) ;
            $( document ).on( 'click' , '.email-share-close-button' , this.close_referral_email ) ;
            $( document ).on( 'click' , '.email-share-button' , this.send_referral_email ) ;
            $( document ).on( 'change' , '#rs_enable_earn_points_for_user_in_reg_form' , this.toggle_birthday_date_field_on_myaccount ) ;
            $( document ).on( 'change' , '#enable_reward_prgm' , this.toggle_birthday_date_field_on_checkout ) ;
            $( document ).on( 'click' , '#subscribeoption' , this.subscribe_or_unsubscribe_mail ) ;
        
        } ,
        trigger_on_page_load : function ( ) {

            if ( '1' == frontendscripts_params.is_date_filter_enabled ) {
                // Date picker for my reward table date filter.
                $( '#rs_custom_from_date_field' ).datepicker( { dateFormat : 'yy-mm-dd' } ) ;
                $( '#rs_custom_to_date_field' ).datepicker( { dateFormat : 'yy-mm-dd' } ) ;

                FPFrontendScripts.toggle_my_reward_table_date_filter() ;
            }

            if ( frontendscripts_params.variable_product_earnmessage == 'no' && '1' == frontendscripts_params.is_product_page ) {
                $( '.variableshopmessage' ).next( 'br' ).hide() ;
                $( '.variableshopmessage' ).hide( ) ;
            } else {
                $( '.variableshopmessage' ).next( 'br' ).show() ;
                $( '.variableshopmessage' ).show( ) ;
            }
            
            $( '.displaymessage' ).parent().hide() ;
            
            FPFrontendScripts.unsubscribe_email_link_alert();
            $( '.rs_social_buttons' ).find( '.email-share-field' ).hide();
        } ,
        table_as_footable : function ( ) {
            jQuery( '.my_reward_table' ).footable( ).bind( 'footable_filtering' , function ( e ) {
                var selected = jQuery( '.filter-status' ).find( ':selected' ).text( ) ;
                if ( selected && selected.length > 0 ) {
                    e.filter += ( e.filter && e.filter.length > 0 ) ? ' ' + selected : selected ;
                    e.clear = ! e.filter ;
                }
            } ) ;
            
            jQuery( '#change-page-sizes' ).change( function ( e ) {
                e.preventDefault( ) ;
                var pageSize = jQuery( this ).val( ) ;
                jQuery( '.footable' ).data( 'page-size' , pageSize ) ;
                jQuery( '.footable' ).trigger( 'footable_initialized' ) ;
            } ) ;
            jQuery( '#page_size_for_points' ).change( function ( e ) {
                e.preventDefault( ) ;
                var pageSize = jQuery( this ).val( ) ;
                jQuery( '.footable' ).data( 'page-size' , pageSize ) ;
                jQuery( '.footable' ).trigger( 'footable_initialized' ) ;
            } ) ;

            // Footable for Shortcode 'rs_points_earned_in_a_specific_duration'.
            jQuery( '.rs_points_earned_in_specific_duration' ).footable() ;
            
            jQuery( '.list_of_orders' ).footable() ;
            jQuery( '#change-page-sizesss' ).change( function ( e ) {
                e.preventDefault() ;
                var pageSize = jQuery( this ).val() ;
                jQuery( '.footable' ).data( 'page-size' , pageSize ) ;
                jQuery( '.footable' ).trigger( 'footable_initialized' ) ;
	    } ) ;
        } ,
        display_social_icons_reward_table : function ( ) {
            $( '.rs_social_buttons .fb-share-button span' ).css( "width" , "60px" ) ;
            $( '.rs_social_buttons .fb-share-button span iframe' ).css( { "width" : "59px" , "height" : "29px" , "visibility" : "visible" } ) ;
            $( '.rs_social_buttons iframe.twitter-share-button' ).css( { "width" : "59px" , "height" : "29px" , "visibility" : "visible" } ) ;
            $( '.rs_social_buttons' ).find( '.toggle-email-share-button' ).show();
            $( '.rs_social_buttons' ).find( '.email-share-field' ).hide();
        } ,
        generated_referral_link : function ( ) {
            var urlstring = jQuery( '#generate_referral_field' ).val( ) ;
            var data = ( {
                action : 'generate_referral_link' ,
                url : urlstring ,
                sumo_security : frontendscripts_params.generate_referral
            } ) ;
            $.post( frontendscripts_params.ajaxurl , data , function ( response ) {
                if ( true === response.success ) {
                    window.location.reload( ) ;
                } else {
                    window.alert( response.data.error ) ;
                }
            } ) ;
        } ,
        fb_share : function ( evt ) {
            evt.preventDefault( ) ;
            var a = document.getElementById( 'share_wrapper' )
            var url = a.getAttribute( 'href' ) ;
            var image = a.getAttribute( 'data-image' ) ;
            var title = a.getAttribute( 'data-title' ) ;
            var description = a.getAttribute( 'data-description' ) ;
            if ( image == '' ) {
                FB.ui( {
                    method : 'share_open_graph' ,
                    action_type : 'og.shares' ,
                    action_properties : JSON.stringify( {
                        object : {
                            'og:url' : url ,
                            'og:title' : title ,
                            'og:description' : description ,
                        }
                    } )
                } , function ( response ) {
                    // Action after response
                    if ( response != null ) {
                        alert( 'Sucessfully Shared' ) ;
                    } else {
                        alert( 'Cancelled' ) ;
                    }
                } ) ;
            } else {
                FB.ui( {
                    method : 'share_open_graph' ,
                    action_type : 'og.shares' ,
                    action_properties : JSON.stringify( {
                        object : {
                            'og:url' : url ,
                            'og:title' : title ,
                            'og:description' : description ,
                            'og:image' : image
                        }
                    } )
                } , function ( response ) {
                    // Action after response
                    if ( response != null ) {
                        alert( 'Sucessfully Shared' ) ;
                    } else {
                        alert( 'Cancelled' ) ;
                    }
                } ) ;
            }
            return false ;
        } ,
        unset_removed_free_products : function ( evt ) {
            var data = {
                action : 'unsetproduct' ,
                sumo_security : frontendscripts_params.unset_product ,
                key_to_remove : $( evt.target ).data( 'cartkey' ) ,
            } ;
            $.post( frontendscripts_params.ajaxurl , data , function ( response ) {
                if ( true === response.success ) {
                    window.location.reload( ) ;
                } else {
                    window.alert( response.data.error ) ;
                }
            } ) ;
        } ,
        message_for_booking : function ( ) {
            var xhr ;
            if ( xhr )
                xhr.abort() ;
            var form = jQuery( this ).closest( 'form' ) ;
            var data = ( {
                action : 'messageforbooking' ,
                form : form.serialize() ,
                sumo_security : frontendscripts_params.booking_msg
            } ) ;
            $.post( frontendscripts_params.ajaxurl , data , function ( response ) {
                if ( true === response.success ) {
                    if ( ( response.data.sumorewardpoints !== 0 ) && ( response.data.sumorewardpoints !== '' ) ) {
                        $( '.woocommerce_booking_variations' ).addClass( 'woocommerce-info' ) ;
                        $( '.woocommerce_booking_variations' ).show() ;
                        $( '.sumobookingpoints' ).html( response.data.sumorewardpoints ) ;
                    } else {
                        $( '.woocommerce_booking_variations' ).hide() ;
                    }
                } else {
                    window.alert( response.data.error ) ;
                }
            } ) ;
        } ,
        message_for_variations : function ( ) {
            if ( ! $( this ).closest( 'div.single_variation_wrap' ).find( 'input:hidden[name=variation_id], input.variation_id' ).length )
                return false ;

            if ( 'no' === frontendscripts_params.check_purchase_notice_for_variation && 'no' === frontendscripts_params.check_referral_notice_for_variation && 'no' === frontendscripts_params.check_buying_notice_for_variation ) {
                return false ;
            }

            var variationid = $( this ).closest( 'div.single_variation_wrap' ).find( 'input:hidden[name=variation_id], input.variation_id' ).val() ;
            if ( variationid === '' || variationid === 0 || variationid === undefined ) {
                $( '#value_variable_product' ).hide() ;
                $( '#buy_Point_value_variable_product' ).hide() ;
                $( '.rs_variable_earn_messages' ).hide() ;
                $( '.rs-minimum-quantity-error-variable' ).hide();
                if ( frontendscripts_params.variable_product_earnmessage === 'no' ) {
                    $( '.variableshopmessage' ).hide( ) ;
                    $( '.variableshopmessage' ).next( 'br' ).hide() ;
                }
                return false ;
            } else {
                $( '#value_variable_product1' ).hide() ;
                $( '#value_variable_product2' ).hide() ;
                var data = ( {
                    action : 'getvariationpoints' ,
                    variationid : variationid ,
                    sumo_security : frontendscripts_params.variation_msg
                } ) ;
                $.post( frontendscripts_params.ajaxurl , data , function ( response ) {
                    if ( true === response.success ) {
                            if ( frontendscripts_params.productpurchasecheckbox == 'yes' ) {
                                if ( frontendscripts_params.showreferralmsg == '1' ) {
                                    if ( response.data.showmsg && response.data.earnpointmsg != '' ) {
                                        if ( frontendscripts_params.loggedinuser == "yes" ) {
                                            if ( frontendscripts_params.showearnmsg == '1' ) {
                                                $( '.variableshopmessage' ).show() ;
                                                $( '.variableshopmessage' ).next( 'br' ).show() ;
                                                $( '.variableshopmessage' ).html( response.data.earnpointmsg ) ;

                                                // Troubleshoot option in Earn Messages for Variations
                                                $( '.rs_variable_earn_messages' ).show() ;
                                                $( '.rs_variable_earn_messages' ).html( response.data.earnpointmsg ) ;
                                            }
                                        } else {
                                            if ( frontendscripts_params.showearnmsg_guest == '1' ) {
                                                $( '.variableshopmessage' ).show() ;
                                                $( '.variableshopmessage' ).next( 'br' ).show() ;
                                                $( '.variableshopmessage' ).html( response.data.earnpointmsg ) ;

                                                // Troubleshoot option in Earn Messages for Variations
                                                $( '.rs_variable_earn_messages' ).show() ;
                                                $( '.rs_variable_earn_messages' ).html( response.data.earnpointmsg ) ;
                                            }
                                        }
                                    } else {
                                        $( '.variableshopmessage' ).next( 'br' ).hide() ;
                                        $( '.variableshopmessage' ).hide( ) ;

                                        $( '.rs_variable_earn_messages' ).hide() ;
                                    }
                                    $( '.gift_icon' ).show() ;

                                    $( '#referral_value_variable_product' ).addClass( 'woocommerce-info rs_message_for_single_product' ) ;
                                    $( '#referral_value_variable_product' ).show() ;
                                    $( '#referral_value_variable_product' ).html( response.data.refmsg ) ;
                                }
                                if ( frontendscripts_params.showbuyingmsg == '1' && response.data.buymsg != '' ) {
                                    $( '.variableshopmessage' ).show() ;
                                    $( '.variableshopmessage' ).next( 'br' ).show() ;
                                    if ( response.data.earnpointmsg != '' ) {
                                        if ( frontendscripts_params.loggedinuser == "yes" ) {
                                            if ( frontendscripts_params.showearnmsg == '1' ) {
                                                $( '.variableshopmessage' ).append( '<br>' + response.data.buymsg + '</br>' ) ;
                                            } else {
                                                $( '.variableshopmessage' ).html( '<br>' + response.data.buymsg + '</br>' ) ;
                                            }
                                        } else {
                                            if ( frontendscripts_params.showearnmsg_guest == '1' ) {
                                                $( '.variableshopmessage' ).append( '<br>' + response.data.buymsg + '</br>' ) ;
                                            } else {
                                                $( '.variableshopmessage' ).html( '<br>' + response.data.buymsg + '</br>' ) ;
                                            }
                                        }
                                    } else {
                                        $( '.variableshopmessage' ).html( '<br>' + response.data.buymsg + '</br>' ) ;
                                    }
                                }
                                if ( frontendscripts_params.showpurchasemsg == '1' && response.data.purchasemsg ) {
                                    $( '#value_variable_product' ).addClass( 'woocommerce-info rs_message_for_single_product' ) ;
                                    $( '#value_variable_product' ).show() ;
                                    $( '#value_variable_product' ).html( response.data.purchasemsg ) ;
                                    if ( response.data.earnpointmsg != '' ) {
                                        if ( frontendscripts_params.loggedinuser == "yes" ) {
                                            if ( frontendscripts_params.showearnmsg == '1' ) {
                                                $( '.variableshopmessage' ).show() ;
                                                $( '.variableshopmessage' ).next( 'br' ).show() ;
                                                $( '.variableshopmessage' ).html( response.data.earnpointmsg ) ;
                                            }
                                        } else {
                                            if ( frontendscripts_params.showearnmsg_guest == '1' ) {
                                                $( '.variableshopmessage' ).show() ;
                                                $( '.variableshopmessage' ).next( 'br' ).show() ;
                                                $( '.variableshopmessage' ).html( response.data.earnpointmsg ) ;
                                            }
                                        }
                                    }
                                }
                                if ( response.data.earnpointmsg != '' ) {
                                    if ( frontendscripts_params.loggedinuser == "yes" ) {
                                        if ( frontendscripts_params.showearnmsg == '1' ) {
                                            $( '.variableshopmessage' ).show() ;
                                            $( '.variableshopmessage' ).next( 'br' ).show() ;
                                            $( '.variableshopmessage' ).html( response.data.earnpointmsg ) ;
                                        }
                                    } else {
                                        if ( frontendscripts_params.showearnmsg_guest == '1' ) {
                                            $( '.variableshopmessage' ).show() ;
                                            $( '.variableshopmessage' ).next( 'br' ).show() ;
                                            $( '.variableshopmessage' ).html( response.data.earnpointmsg ) ;
                                        }
                                    }
                                }
                            } else {
                                if ( frontendscripts_params.showreferralmsg == '1' ) {
                                    $( '#referral_value_variable_product' ).addClass( 'woocommerce-info rs_message_for_single_product' ) ;
                                    $( '#referral_value_variable_product' ).show() ;
                                    $( '#referral_value_variable_product' ).html( response.data.refmsg ) ;
                                }
                            }
                            if ( frontendscripts_params.buyingpointscheckbox == 'yes' ) {
                                if ( frontendscripts_params.buyingmsg == '1' && response.data.showbuypoint == '1' && response.data.buying_msg != '' ) {
                                    $( '#buy_Point_value_variable_product' ).addClass( 'woocommerce-info rs_message_for_single_product' ) ;
                                    $( '#buy_Point_value_variable_product' ).show() ;
                                    $( '#buy_Point_value_variable_product' ).html( response.data.buying_msg ) ;
                                } else {
                                    $( '#buy_Point_value_variable_product' ).hide() ;
                                }
                                if ( frontendscripts_params.showbuyingmsg == '1' && response.data.buymsg != '' ) {
                                    $( '.variableshopmessage' ).show() ;
                                    $( '.variableshopmessage' ).next( 'br' ).show() ;
                                    if ( response.data.earnpointmsg != '' && frontendscripts_params.productpurchasecheckbox == 'yes' ) {
                                        if ( frontendscripts_params.loggedinuser == "yes" ) {
                                            if ( frontendscripts_params.showearnmsg == '1' ) {
                                                $( '.variableshopmessage' ).append( '<br>' + response.data.buymsg + '</br>' ) ;
                                            } else {
                                                $( '.variableshopmessage' ).html( '<br>' + response.data.buymsg + '</br>' ) ;
                                            }
                                        } else {
                                            if ( frontendscripts_params.showearnmsg_guest == '1' ) {
                                                $( '.variableshopmessage' ).append( '<br>' + response.data.buymsg + '</br>' ) ;
                                            } else {
                                                $( '.variableshopmessage' ).html( '<br>' + response.data.buymsg + '</br>' ) ;
                                            }
                                        }
                                    } else {
                                        $( '.variableshopmessage' ).html( '<br>' + response.data.buymsg + '</br>' ) ;
                                    }
                                }
                            }
                            
                            if(response.data.min_quantity_error_message){
                                $( '.rs-minimum-quantity-error-variable' ).show();
                                $( '.rs-minimum-quantity-error-variable' ).addClass("woocommerce-error");
                                $( '.rs-minimum-quantity-error-variable' ).html( response.data.min_quantity_error_message ) ;
                            }
                    } else {
                        window.alert( response.data.error ) ;
                    }
                } ) ;
            }
        } ,
        copy_to_clipboard : function () {

            /* Code Improvement for Copy Link Functionality in V23.8 */

            var input = document.createElement( 'input' ) ;
            input.setAttribute( 'readonly' , false ) ;
            input.setAttribute( 'contenteditable' , true ) ;
            input.style.position = 'fixed' ; // prevent scroll from jumping to the bottom when focus is set.
            input.value = $( this ).attr( 'data-referralurl' ) ;
            document.body.appendChild( input ) ;

            var range = document.createRange() ;
            range.selectNodeContents( input ) ;
            var sel = window.getSelection() ;
            sel.removeAllRanges() ;
            sel.addRange( range ) ;
            input.setSelectionRange( 0 , 999999 ) ;

            input.focus() ;
            input.select() ;

            document.execCommand( 'copy' ) ;

            input.contentEditable = false ;
            input.readOnly = false ;

            $( '.rs_alert_div_for_copy' ).css( { display : 'block' } ).delay( 7000 ).fadeOut() ;

            input.remove() ;
        } ,
        toggle_my_reward_table_date_filter : function ( ) {
            if ( '5' == $( '#rs_duration_type' ).val( ) ) {
                $( 'table.rs-my-reward-date-filter' ).find( '#rs_submit' ).removeClass( 'rs_disabled' ) ;
                $( '#rs_custom_from_date_field' ).closest( 'tr' ).show( ) ;
                $( '#rs_custom_to_date_field' ).closest( 'tr' ).show( ) ;
            } else if ( '0' == $( '#rs_duration_type' ).val( ) ) {
                $( 'table.rs-my-reward-date-filter' ).find( '#rs_submit' ).addClass( 'rs_disabled' ) ;
                $( '#rs_custom_from_date_field' ).closest( 'tr' ).hide( ) ;
                $( '#rs_custom_to_date_field' ).closest( 'tr' ).hide( ) ;
            } else {
                $( 'table.rs-my-reward-date-filter' ).find( '#rs_submit' ).removeClass( 'rs_disabled' ) ;
                $( '#rs_custom_from_date_field' ).closest( 'tr' ).hide( ) ;
                $( '#rs_custom_to_date_field' ).closest( 'tr' ).hide( ) ;
            }
        } ,
        prevent_submit_in_my_reward_table : function () {

            var $error_message = false ;

            if ( '0' == $( '#rs_duration_type' ).val( ) ) {
                $error_message = frontendscripts_params.default_selection_error_message ;
            }

            if ( '5' == $( '#rs_duration_type' ).val( ) ) {
                var $from_date = $( '#rs_custom_from_date_field' ).val() ;
                var $to_date = $( '#rs_custom_to_date_field' ).val() ;

                if ( ! $from_date || ! $to_date ) {
                    $error_message = frontendscripts_params.custom_date_error_message ;
                }
            }

            if ( $error_message ) {
                alert( $error_message )
                return false ;
            }
        } ,
        unsubscribe_email_link_alert:function(){                        
            $.urlParam = function( name ) {
				var results = new RegExp( '[\?&]' + name + '=([^&#]*)' ).exec( window.location.href ) ;
				if ( results ) {
					return results[1] ;
				} else {
					return 0 ;
				}
			}
                        
            var $email_user_id = $.urlParam( 'userid' ),
                $email_unsub_link = $.urlParam( 'unsub' ),
                $unsub_link_nonce = $.urlParam( 'nonce' );    
            
            if(!$email_user_id || !$email_unsub_link || !$unsub_link_nonce){
		        return false;
            }
            
            if(!frontendscripts_params.is_user_logged_in){
                alert( frontendscripts_params.loggedinuser_err );
                window.location.href = frontendscripts_params.myaccount_url;
                return false;
            }
            
            if( $email_user_id != frontendscripts_params.user_id || 'yes' != $email_unsub_link){
                alert(frontendscripts_params.unsub_link_error);
		        return false;
            }
            
            let data = ( {
                action : 'unsubscribe_user' ,
                user_id : $email_user_id ,
                sumo_security : frontendscripts_params.unsubscribe_user,
            } ) ;

            $.post( frontendscripts_params.ajaxurl , data , function ( res ) {                
                if ( true === res.success ) {
                    alert(frontendscripts_params.unsub_link_success);
                } else {
                    alert(res.data.error);
                }

                window.location.href = frontendscripts_params.myaccount_url;
            } );

        },
        block : function ( id ) {
            $( id ).block( {
                message : null ,
                overlayCSS : {
                    background : '#fff' ,
                    opacity : 0.6
                }
            } ) ;
        } ,
        unblock : function ( id ) {
            $( id ).unblock( ) ;
        } ,

        toggle_referral_email : function ( e ) {
            e.preventDefault();
            let $this = $( e.currentTarget );
            $( $this ).hide();
            $( '.rs_social_buttons' ).find( '.email-share-field' ).show();
        } ,

        close_referral_email : function ( e ) {
            e.preventDefault();
            let $this = $( e.currentTarget );
            $( $this ).closest('p').find( '#receiver_email_ids' ).val('');
            $( $this ).closest('p').hide();
            $( $this ).closest('div').find( '.toggle-email-share-button' ).show();
            $($this).closest('p').find('.error, .success').remove();
        } ,

        send_referral_email : function ( e ) {
            e.preventDefault();
            let $this = $( e.currentTarget ),
                $ref_link = $($this).closest( '.email-share-field' ).find( '#email_ref_link' ).val(),
                $receiver_ids = $($this).closest( '.email-share-field' ).find( '#receiver_email_ids' ).val();            
            $($this).closest('div').find('.error, .success').remove();
            
            if ( '' === $receiver_ids ){
                $($this).closest('p').append('<p class="error">Enter any email id with comma separate.</p>');
                return;
            }
            var data = ( {
                action : 'send_referral_email' ,
                ref_link : $ref_link,
                receiver_ids : $receiver_ids,
                sumo_security : frontendscripts_params.send_referral_email
            } ) ;
            FPFrontendScripts.block($($this).closest('p'));
            $.post( frontendscripts_params.ajaxurl , data , function ( res ) {                
                if ( true === res.success ) {
                    $($this).closest('p').hide() ;
                    $($this).closest('div').find('.toggle-email-share-button').show();
                    $($this).closest('div').append('<p class="success">'+res.data+'</p>');
                    $($this).closest('div').find('.success').fadeOut( 10000 ) ;
                    $($this).closest('p').find( '#receiver_email_ids' ).val('');
                } else {
                    $($this).closest('p').append( '<p class="error">'+res.data.error+' '+res.data.ids+'</p>' );
                }

                FPFrontendScripts.unblock($($this).closest('p'));
            } );
        } ,

        toggle_birthday_date_field_on_myaccount : function ( ) {
            if ( 'yes' === $('#rs_enable_earn_points_for_user_in_reg_form').data('enable-reward-program') ){
                if( false === $('#rs_enable_earn_points_for_user_in_reg_form').is(':checked') ){
                    $( '#srp_birthday_date' ).closest('p').hide();
                } else {
                    $( '#srp_birthday_date' ).closest('p').show();
                }
            } else {
                $( '#srp_birthday_date' ).closest('p').show();
            }
        } ,

        toggle_birthday_date_field_on_checkout : function ( ) {
            if( 'yes' === $('#enable_reward_prgm').closest('p').find('.checkbox').data('enable-reward-program') ){
                if( false === $('#enable_reward_prgm').is(':checked') ){
                    $( '#srp_birthday_date_field' ).closest('p').hide();
                } else {
                    $( '#srp_birthday_date_field' ).closest('p').show();
                }                
            } else {             
                $( '#srp_birthday_date_field' ).closest('p').show();
            }
        } ,
        subscribe_or_unsubscribe_mail : function () {
            var subscribe = $( '#subscribeoption' ).is( ':checked' ) ? 'yes' : 'no' ;
            var data = {
                action : "subscribemail" ,
                subscribe : subscribe ,
                sumo_security : frontendscripts_params.fp_subscribe_mail
            } ;
            $.post( frontendscripts_params.ajaxurl , data , function ( response ) {
                if ( true === response.success ) {
                    window.alert( response.data.content ) ;
                } else {
                    window.alert( response.data.error ) ;
                }
            } ) ;
        } ,

    } ;
    FPFrontendScripts.init( ) ;
} ) ;
