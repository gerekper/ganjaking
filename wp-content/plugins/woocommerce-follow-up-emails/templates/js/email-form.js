var fue_editor_events_attached = false, fue_timer = null;
jQuery( function ( $ ) {
    $( 'form#post' ).on( 'submit', function() {
        var content = fue_get_editor_content();
        if (!content.trim()) {
            // is empty or whitespace
            if ( !confirm("Warning: Your email has no content. Emails without content do not get sent.\n\nDo you want to continue?") ) {
                return false;
            }
        }

        return true;
    } );

    // Inject a label before the Title textfield
    $("#titlewrap").prepend('<label for="title" class="fue-label">Name</label>');

    init_select2_fields();

    // TABS
    $('ul.fue-tabs').show();
    $('div.panel-wrap').each(function(){
        $(this).find('div.panel').slice(1).hide();
    });
    $( '#fue-email-details' ).on( 'click', 'ul.fue-tabs a', function() {
        var panel_wrap =  $(this).closest('div.panel-wrap');
        $('ul.fue-tabs li', panel_wrap).removeClass('active');
        $(this).parent().addClass('active');
        $('div.panel', panel_wrap).hide();
        $( $(this).attr('href') ).show();

        return false;
    } );

    // Post Status selector
    $(".misc-pub-post-status").remove();
    $( '.misc-pub-email-status a.edit-post-status' ).on( 'click', function( event ) {
        if ( $("#post_status").is( ':hidden' ) ) {
            $("#post_status").slideDown('fast').find('select').trigger( 'focus' );
            $(this).hide();
        }
        event.preventDefault();
    } );

    // Email Type
    $( '#email_type' ).on( 'change', function() {
        var val = $(this).val();
        var nonce = $( this ).data( 'nonce' );

        // Description switcher
        $(".email-type-description").hide();
        $("#"+ val + "_desc").show();

        // Refresh the email-details meta-box
        if ( val !== "" ) {
            fue_update_email_type(val, nonce);
        }

    } ).trigger( 'change' );

    // Email Template
    $( '#template' ).on( 'change', function() {
        var val = $(this).val();
        var nonce = $( this ).data( 'nonce' );

        if ( val !== "" ) {
            fue_update_email_template( val, nonce );
        }
    } ).trigger( 'change' );

    // Interval Type
    jQuery( '#fue-email-details' ).on( 'change', '#interval_type', function() {

        jQuery(".adjust_date_tr").hide();

        if (jQuery(this).val() === "cart") {
            jQuery(".condition-payment_method").hide();
            jQuery(".condition-shipping_method").hide();
            jQuery(".var_cart").show();
            jQuery(".var_order").hide();
        } else {
            jQuery(".condition-payment_method").show();
            jQuery(".condition-shipping_method").show();
            jQuery(".var_cart").hide();
            jQuery(".var_order").show();
        }

        if (jQuery(this).val() === "after_last_purchase" ) {
            jQuery(".adjust_date_tr").show();
        }

        if (jQuery(this).val() !== "order_total_above" ) {
            jQuery(".show-if-order_total_above").hide();
        } else {
            jQuery(".show-if-order_total_above").show();
        }

        if (jQuery(this).val() !== "order_total_below" ) {
            jQuery(".show-if-order_total_below").hide();
        } else {
            jQuery(".show-if-order_total_below").show();
        }

        if (jQuery(this).val() !== "total_orders") {
            jQuery(".show-if-total_orders").hide();
        } else {
            jQuery(".show-if-total_orders").show();
        }

        if (jQuery(this).val() !== "total_purchases") {
            jQuery(".show-if-total_purchases").hide();
        } else {
            jQuery(".show-if-total_purchases").show();
        }

        if (jQuery(this).val() === "total_orders" || jQuery(this).val() === "total_purchases") {
            jQuery(".meta_one_time_tr").show();
        } else {
            jQuery(".meta_one_time_tr").hide();
        }

        if ( jQuery("#interval_type").val() === "list_signup" ) {
            jQuery(".show-if-list_signup").show();
        } else {
            jQuery(".show-if-list_signup").hide();
        }

        jQuery("body").trigger("fue_interval_type_changed", [jQuery(this).val()]);

    } ).trigger( 'change' );

    // Interval Duration
    jQuery( '#fue-email-details' ).on( 'change', '#interval_duration', function() {
        fue_toggle_elements();
    } ).trigger( 'change' );

    // Test Email.
    jQuery( '#fue-email-test' ).on( 'click', '#test_send', function() {
        var $btn    = jQuery(this);
        var old_val = $btn.val();
        var $email  = jQuery( '#email' );

        if ( $email.length && '' === $email.val() ) {
            return;
        }

        $btn
            .val("Please wait...")
            .prop( 'disabled', true );

        var data = {
            'action' : 'fue_send_test_email',
            'id'     : jQuery("#post_ID").val(),
            'message': fue_get_editor_content(),
            'subject': jQuery("#post_excerpt").val(),
            'nonce'  : jQuery( '#fue-send-manual-meta-box' ).val()
        };

        jQuery(".test-email-field").each(function() {
            var field = jQuery(this).data("key") || jQuery(this).attr("id");
            data[field] = jQuery(this).val();
        });

        jQuery.post(ajaxurl, data, function(resp) {
            if (resp === "OK")
                alert("Email sent!");
            else
                alert(resp);

            $btn
                .val(old_val)
                .prop( 'disabled', false );
        });
    } );

    // Move focus to the Subject field after pressing the TAB key on the Title field
    $( '#post-body-content' ).on( 'keydown', '#title', function( e ) {
        var keyCode = e.keyCode || e.which;

        if (keyCode === 9) {
            e.preventDefault();

            // move focus to the subject field
            $( '#post_excerpt' ).trigger( 'focus' );
        }
    } );

    // GA Tracking switch
    $( '#fue-email-details').on( 'change', '#tracking_on', function() {
        if (jQuery(this).prop("checked")) {
            jQuery(".tracking_on").show();
        } else {
            jQuery(".tracking_on").hide();
        }
    } );

    // Custom Fields
    jQuery( '#use_custom_field' ).on( 'change', function() {
        if (jQuery(this).prop("checked")) {
            jQuery(".show-if-custom-field").show();
        } else {
            jQuery(".show-if-custom-field").hide();
        }
    } ).trigger( 'change' );

    jQuery( '#custom_fields' ).on( 'change', function() {
        if (jQuery(this).val() === "Select a product first.") return;
        jQuery(".show-if-cf-selected").show();
        jQuery("#custom_field").val("{cf "+ jQuery("#product_id").val() +" "+ jQuery(this).val() +"}");
    } ).trigger( 'change' );

    // Event for updating the metaboxes
    $('body').on( 'updated_email_type updated_email', function() {
        fue_refresh_email_details();
        fue_refresh_email_variables();
        fue_toggle_elements();

        $("body").trigger( 'fue_email_type_changed', [$("#email_type").val()] );
    });

    $('body').on( 'fue_update_variables', function() {
        fue_refresh_email_variables();
    });

    function fue_update_email_type( type, nonce ) {
        $("#fue-email-type").block({ message: null, overlayCSS: { background: '#fff url('+ FUE.ajax_loader +') no-repeat center', opacity: 0.6 } });
        var args = {
            "action":   "fue_update_email_type",
            "id":       $("#email_id").val(),
            "type":     type,
            'nonce': nonce
        }
        $.post( ajaxurl, args, function() {
            $( 'body' ).trigger( 'updated_email_type', [type] );
            $("#fue-email-type").unblock();
        });
    }

    function fue_update_email_template( template, nonce ) {
        $("#fue-email-template").block({ message: null, overlayCSS: { background: '#fff url('+ FUE.ajax_loader +') no-repeat center', opacity: 0.6 } });
        var args = {
            "action":   "fue_update_email",
            "id":       $("#email_id").val(),
            "template": template,
            nonce: nonce
        }
        $.post( ajaxurl, args, function() {
            $( 'body' ).trigger( 'updated_email_template', [template] );
            fue_refresh_email_variables();
            $("#fue-email-template").unblock();
        });
    }

    function fue_toggle_elements() {
        var type                = $("#email_type").val();
        var interval_type       = $("#interval_type").val();
        var interval_duration   = $("#interval_duration").val();

        if (type === "storewide") {
            var show = ['.always_send_tr', '.interval_type_option', '.interval_type_span'];
            var hide = ['.adjust_date_tr', '.signup_description', '.email_receipient_tr', '.btn_send_save', '.interval_type_order_total_above', '.interval_type_order_total_below', '.interval_type_purchase_above_one', '.interval_type_total_purchases', '.interval_type_total_orders', '.interval_type_after_last_purchase'];

            for ( var x = 0; x < show.length; x++ ) {
                jQuery(show[x]).show();
            }

            for (x = 0; x < hide.length; x++) {
                jQuery(hide[x]).hide();
            }

        }

        if (type === "signup") {
            show = ['.interval_type_option', '.signup_description', '.interval_type_span'];
            hide = [
                '.settings_options', '.non-signup', '.always_send_tr', '.adjust_date_tr', '.btn_send_save',
                '.email_receipient_tr', '.product_description_tr', '.product_tr', '.category_tr',
                '.use_custom_field_tr', '.interval_type_order_total_above', '.interval_type_order_total_below',
                '.interval_type_purchase_above_one', '.interval_type_after_last_purchase',
                '.interval_type_total_purchases', '.interval_type_total_orders', '.interval_type_after_last_purchase',
                '.interval_duration_date', '.var_customer_name', '.var_customer_username', '.var_customer_first_name',
                '.var_customer_email', '#fue-email-variables-list .non-signup'
            ];

            jQuery( 'option.interval_duration_date' ).prop( 'disabled', true );

            for (x = 0; x < hide.length; x++) {
                jQuery(hide[x]).hide();
            }

            for (x = 0; x < show.length; x++) {
                jQuery(show[x]).show();
            }

        } else {
            hide = ['.signup_description'];

            for (x = 0; x < hide.length; x++) {
                jQuery(hide[x]).hide();
            }
        }

        if (type === "manual") {
            hide = ['.settings_options', '.interval-field', '.always_send_tr', '.interval_tr', '.adjust_date_tr', '.product_description_tr', '.product_tr', '.category_tr', '.use_custom_field_tr', '.interval_type_order_total_above', '.interval_type_order_total_below', '.interval_type_purchase_above_one', '.interval_type_total_purchases', '.interval_type_total_orders', '.interval_type_after_last_purchase'];

            for (x = 0; x < hide.length; x++) {
                jQuery(hide[x]).hide();
            }

            $(".wc-coupons").addClass('active');
            $("#coupons_details").show();

        }

        if (type === "customer") {
            show = ['.always_send_tr', '.interval_type_order_total_above', '.interval_type_order_total_below', '.interval_type_purchase_above_one', '.interval_type_total_purchases', '.interval_type_total_orders', '.interval_type_total_purchases', '.interval_type_after_last_purchase', '.interval_type_span'];
            hide = ['.adjust_date_tr', '.interval_type_option', '.always_send_tr', '.signup_description', '.product_description_tr', '.product_tr', '.category_tr', '.use_custom_field_tr', '.custom_field_tr', '.interval_duration_date'];

            jQuery( 'option.interval_duration_date' ).prop( 'disabled', true );

            for (x = 0; x < hide.length; x++) {
                jQuery(hide[x]).hide();
            }

            for (x = 0; x < show.length; x++) {
                jQuery(show[x]).show();
            }

        }

        jQuery(".adjust_date_tr").hide();

        jQuery(".hide-if-date").show();
        jQuery(".show-if-date").hide();

        if ( interval_duration === "date") {
            $(".hide-if-date").hide();
            $(".show-if-date").show();
        }

        $(".show-if-order_total_above").hide();
        if ( interval_type === "order_total_above" ) {
            jQuery(".show-if-order_total_above").show();
        }

        $(".show-if-order_total_below").hide();
        if ( interval_type === "order_total_below" ) {
            $(".show-if-order_total_below").show();
        }

        $(".show-if-total_orders").hide();
        if ( interval_type === "total_orders" ) {
            $(".show-if-total_orders").show();
        }

        $(".show-if-total_purchases").hide();
        if ( interval_type === "total_purchases" ) {
            $(".show-if-total_purchases").show();
        }

        $(".show-if-list_signup").hide();
        if ( interval_type === "list_signup" ) {
            $(".show-if-list_signup").show();
        }

        if ( $("#interval_type option:selected").css("display") === "none" ) {
            $("#interval_type option").each(function() {
                if ($(this).css("display") !== "none") {
                    $("#interval_type").val($(this).val());
                    return false;
                }
            });
        }

        if ($("#interval_type").val() === "after_last_purchase" ) {
            $(".adjust_date_tr").show();
        }

        $( '#send_coupon' ).trigger( 'change' );
        $( '#tracking_on' ).trigger( 'change' );
        $( '.save_email.button-primary' ).prop( 'disabled', false );
    }

    function fue_refresh_email_details() {
        $("#fue-email-details").block({ message: null, overlayCSS: { background: '#fff url('+ FUE.ajax_loader +') no-repeat center', opacity: 0.6 } });

        var args = {
            "action":   "fue_get_email_details_html",
            "id":       $("#email_id").val()
        }
        $.getJSON( ajaxurl, args, function(resp) {
            $("#fue-email-details .inside").html( resp.html );

            $("#fue-email-details").unblock();

            $("#fue-email-details-content").show();

            $( 'ul.fue-tabs li:visible' ).eq( 0 ).find( 'a' ).trigger( 'click' );

            fue_toggle_elements();

            bind_tooltips();

            $( 'body').trigger( 'updated_email_details' );

            init_select2_fields();
        });
    }

    function fue_refresh_email_variables() {
        $("#fue-email-variables").block({ message: null, overlayCSS: { background: '#fff url('+ FUE.ajax_loader +') no-repeat center', opacity: 0.6 } });
        var args = {
            "action":   "fue_get_email_variables_list",
            "id":       $("#email_id").val()
        }
        $.getJSON( ajaxurl, args, function( resp ) {
            $("#fue-email-variables .inside").html( resp.html );

            $('body').trigger('updated_variables_list');

            // Hide variables that rely on #interval_type's value
            $( '#interval_type' ).trigger( 'change' );

            $("#fue-email-variables").unblock();

            $( '#send_coupon' ).trigger( 'change' );

            bind_tooltips();
        });

    }

    function fue_get_editor_content() {
        var content;
        var input_id    = "content";
        var editor      = tinyMCE.get(input_id);
        var textArea    = jQuery('textarea#' + input_id);

        if (textArea.length>0 && textArea.is(':visible')) {
            content = textArea.val();
        } else {
            content = editor.getContent();
        }

        return content;
    }

} );

function init_select2_fields() {
    // select boxes
    jQuery( '#post select.select2-nostd' ).filter( ':not(.enhanced)' ).each( function() {
        var select2_args = {
            minimumResultsForSearch: 10,
            allowClear:  true,
            placeholder: jQuery( this ).data( 'placeholder' )
        };

        jQuery( this ).select2( select2_args).addClass( 'enhanced' );
    });

    jQuery( '#post select.select2' ).filter( ':not(.enhanced)' ).each( function() {
        var select2_args = {
            minimumResultsForSearch: 10,
            allowClear:  jQuery( this ).data( 'allow_clear' ) ? true : false,
            placeholder: jQuery( this ).data( 'placeholder' )
    };

        jQuery( this ).select2( select2_args).addClass( 'enhanced' );
    });

    jQuery( ':input.ajax_select2_products_and_variations' ).filter( ':not(.enhanced)' ).each( function() {
        var select2_args = {
            allowClear:  jQuery( this ).data( 'allow_clear' ) ? true : false,
			placeholder: jQuery( this ).data( 'placeholder' ),
			width:       '100%',
            minimumInputLength: jQuery( this ).data( 'minimum_input_length' ) ? jQuery( this ).data( 'minimum_input_length' ) : 3,
            escapeMarkup: function( m ) {
                return m;
			},
			ajax: {
				url:         ajaxurl,
				dataType:    'json',
				quietMillis: 250,
				data: function( params ) {
					return {
						term:     params.term,
						action:   jQuery( this ).data( 'action' ) || 'woocommerce_json_search_products_and_variations',
						security: FUE.nonce,
					};
				},
				processResults: function( data ) {
					var terms = [];
					if ( data ) {
						jQuery.each( data, function( id, text ) {
							terms.push( { id: id, text: text } );
						} );
					}
					return { results: terms };
				},
				cache: true
			},
        };

        jQuery( this ).select2( select2_args ).addClass( 'enhanced' );
    } );

	jQuery( ':input.ajax_select2_courses' ).filter( ':not(.enhanced)' ).each( function() {
        var select2_args = {
            allowClear:  jQuery( this ).data( 'allow_clear' ) ? true : false,
			placeholder: jQuery( this ).data( 'placeholder' ),
			width:       '100%',
            minimumInputLength: jQuery( this ).data( 'minimum_input_length' ) ? jQuery( this ).data( 'minimum_input_length' ) : 3,
            escapeMarkup: function( m ) {
                return m;
			},
			ajax: {
				url:         ajaxurl,
				dataType:    'json',
				quietMillis: 250,
				data: function( params ) {
                    return {
                        term:     params.term,
                        filters:  jQuery( this ).data( 'filter' ) || '',
                        action:   jQuery( this ).data( 'action' ) || 'fue_sensei_search_courses',
                        security: jQuery( this ).data( 'nonce' ) || FUE.nonce
                    };

				},
				processResults: function( data ) {
					var terms = [];
					if ( data ) {
						jQuery.each( data, function( id, text ) {
							terms.push( { id: id, text: text } );
						} );
					}
					return { results: terms };
				},
				cache: true
			},
        };

        jQuery( this ).select2( select2_args ).addClass( 'enhanced' );
    } );

	jQuery( ':input.ajax_select2_lessons' ).filter( ':not(.enhanced)' ).each( function() {
        var select2_args = {
            allowClear:  jQuery( this ).data( 'allow_clear' ) ? true : false,
			placeholder: jQuery( this ).data( 'placeholder' ),
			width:       '100%',
            minimumInputLength: jQuery( this ).data( 'minimum_input_length' ) ? jQuery( this ).data( 'minimum_input_length' ) : 3,
            escapeMarkup: function( m ) {
                return m;
			},
			ajax: {
				url:         ajaxurl,
				dataType:    'json',
				quietMillis: 250,
				data: function( params ) {
                    return {
                        term:     params.term,
                        filters:  jQuery( this ).data( 'filter' ) || '',
                        action:   jQuery( this ).data( 'action' ) || 'fue_sensei_search_lessons',
                        security: jQuery( this ).data( 'nonce' ) || FUE.nonce
                    };

				},
				processResults: function( data ) {
					var terms = [];
					if ( data ) {
						jQuery.each( data, function( id, text ) {
							terms.push( { id: id, text: text } );
						} );
					}
					return { results: terms };
				},
				cache: true
			},
        };

        jQuery( this ).select2( select2_args ).addClass( 'enhanced' );
    } );

	if ( typeof init_fue_product_search === "function" ) {
		init_fue_product_search();
	}
	if ( typeof init_fue_customer_search === "function" ) {
		init_fue_customer_search();
	}
	if ( typeof init_fue_select === "function" ) {
		init_fue_select();
	}
	if ( typeof init_fue_coupon_search === "function" ) {
		init_fue_coupon_search();
	}
}

// bind Tips
function bind_tooltips() {
    jQuery(".tips, .help_tip").tipTip({
        'attribute' : 'title',
        'fadeIn' : 50,
        'fadeOut' : 50,
        'delay' : 200
    });
}

function fue_show_elements( elements ) {
    fue_toggle_elements( elements, true );
}

function fue_hide_elements( elements ) {
    fue_toggle_elements( elements, false );
}

function fue_toggle_elements( elements, show ) {
    for ( var x = 0; x < elements.length; x++ ) {
        if ( show ) {
            jQuery(elements[x]).show();
        } else {
            jQuery(elements[x]).hide();
        }
    }
}

jQuery().ready(function() {
    var ifr_body    = jQuery("#content_ifr").contents().find("body");
    var dummy       = jQuery("#email_content_dummy");
    var timer       = null;

    // inject the content dummy inside the #postdivrich div
    jQuery(dummy).prependTo(ifr_body);
    if ( jQuery("#content").is(":visible") ) {
        dummy.remove();
    } else {
        dummy.css( {
            display: "block",
            color: "#999"
        } );
    }

    jQuery( 'button#content-html' ).on( 'click', function() {
        dummy.remove();
    } );

    var content_dummy = function() {
        dummy.on( 'click', function() {
            tinyMCE.get( 'content' ).trigger( 'focus' );
            dummy.remove();
        } );

        if ( !ifr_body.hasClass("has-focus") ) {
            timer = setInterval(function() {
                if ( ifr_body.hasClass('has-focus') ) {
                    clearInterval( timer );
                    dummy.trigger( 'click' );
                }
            }, 500);
        }
    }

    content_dummy();
});
