var last_id = '';
var _multi_shipping = false;
var _ms_reloaded = false;

function supports_html5_storage() {
    try {
        $supports_html5_storage = ( 'sessionStorage' in window && window.sessionStorage !== null );

        window.sessionStorage.setItem( 'mship', 'test' );
        window.sessionStorage.removeItem( 'mship' );
    } catch (e) {
        $supports_html5_storage = false;
    }

    return $supports_html5_storage;
}

jQuery(document).ready(function($) {
    // store entered billing and shipping addresses
    // so they can be imported later if the customer decides to use multishipping
    if ( supports_html5_storage() ) {
        var billing_fields = {},
            shipping_fields = {};
        $("form.checkout").on("blur", ".woocommerce-billing-fields :input", function() {
            var id      = $(this).attr("id");
            var value   = $(this).val();

            billing_fields[ id ] = value;
            localStorage.setItem( 'wcms_billing_fields', JSON.stringify( billing_fields ) );
        });

        $("form.checkout").on("blur", ".woocommerce-shipping-fields :input", function() {
            var id      = $(this).attr("id");
            var value   = $(this).val();

            shipping_fields[ id ] = value;
            localStorage.setItem( 'wcms_shipping_fields', JSON.stringify( shipping_fields ) );
        });

        $("form.checkout :input").trigger("blur");
    }

    $("a.ms_delete_address").click(function(e) {
        e.preventDefault();

        var idx = $(this).data("idx");
        var block = $(this).parents(".address-block");

        $(block).block({
            message: null,
            overlayCSS: {
                background: '#fff',
                opacity: 0.6
            }
        });

        $.post( WCMS.ajaxurl, {action: "wc_ms_delete_address", idx: idx}, function(resp) {
            $(block).unblock();
            $(block).remove();
        });
    });

    $("select.shipping_methods").on("change", function() {
        $('body').trigger('update_checkout');
    });

    $("[name=shipping_method]").on("change", function() {
        if ($(this).val() == 'local_pickup' || $(this).val() == 'local_pickup_plus') {
            $("#wcms_message").hide();
        } else {
            if ( $("#wcms_message").data("allowed") == 1 )
                $("#wcms_message").show();
        }
    }).change();

    function disableDays(date) {
        if ( $.inArray(date.getDay(), WCMS.datepicker_valid_days) == -1 ) {
            return [false];
        }

        var m = date.getMonth(), d = date.getDate(), y = date.getFullYear();
        var mdy = (m+1) + '-' + d +'-'+ y;

         // Remove leading zeros from excluded dates
        var excluded_dates = WCMS.datepicker_excluded_dates.map(function(excluded_date) {
            return excluded_date.replace( /(^|-)0+/g, "$1" );
        });

        if ( $.inArray( mdy, excluded_dates ) != -1 ) {
            return [false];
        }

        return [true];
    }

    $('body').on('updated_checkout', function() {

        if ( _multi_shipping ) {
            $("tr.shipping").remove();

            if ( !_ms_reloaded ) {
                $('body').trigger('update_checkout');
                _ms_reloaded = true;
            }

            $(".ms_shipping_date").datepicker({
                beforeShowDay: disableDays,
                minDate: 0
            });

            // Don't allow user's with javascript enabled to manually edit the date
            $(".ms_shipping_date").attr( 'readOnly', 'true' );
        }
    });

    $(".user-add-address").click(function(e) {
        e.preventDefault();
        var id  = '';
        var tpl = $("#address_form_template").html();

        do {
            id  = Math.floor(Math.random()*99999999) + 1;
            tpl = tpl.replace(/(\|sig\|)/gi, id);
        } while ( $("#shipping_address_"+ id).length >= 1 );

        tb_show('New Address', "#TB_inline?height=520&width=400&inlineId=address_form_template&sig="+ id);
        wcms_resize_thickbox();
    });

    $(".add-address").click(function(e) {
        e.preventDefault();

        var id  = '';
        var tpl = $("#address_form_template").html();

        do {
            id  = Math.floor(Math.random()*99999999) + 1;
            $("#address_id").val(id);
            //tpl = tpl.replace(/(\|sig\|)/gi, id);
        } while ( $("#shipping_address_"+ id).length >= 1 );

        tb_show('New Address', "#TB_inline?height=520&width=400&inlineId=address_form_template&sig="+ id);

        wcms_resize_thickbox();
    });

    $(".user-duplicate-cart").click(function(e) {
        e.preventDefault();

        tb_show('Shipping Address', "#TB_inline?height=520&width=400&inlineId=duplicate_address_form_template");
        wcms_resize_thickbox()
    });

    $("#add_address_form").submit(function() {
        var frm = this;
        jQuery(frm).block({ message: null, overlayCSS: { background: '#fff url('+ WCMS.wc_url +'/assets/images/ajax-loader.gif) no-repeat center', opacity: 0.6 } });

        $("p.woocommerce-invalid").removeClass("woocommerce-invalid");

        var data = $(this).serialize() + "&action=wc_save_to_address_book";

        $.post(WCMS.ajaxurl, data, function(resp) {
            var data = JSON.parse(resp);

            if (data.ack == "OK") {
                // reset form
                $('#add_address_form')[0].reset();

                window.location.href = data.next;

            } else if (data.ack == "ERR") {
                if ( data.errors && data.errors.length > 0 ) {

                    for ( var x = 0; x < data.errors.length; x++ ) {
                        var field_key = data.errors[x];

                        $("input[name='address["+ field_key +"]']").parent("p").addClass("woocommerce-invalid");
                    }

                }
                jQuery(frm).unblock();
                alert(data.message);
            }

        });

        return false;
    });

    $("#duplicate_cart_form").submit(function() {
        var data = $(this).serialize() + "&action=wc_duplicate_cart";

        $.post(WCMS.ajaxurl, data, function(resp) {
            var data = JSON.parse(resp);

            if (data.ack == "OK") {
                tb_remove();
                var id      = data.id;
                var html    = data.html;

                $("#addresses_container").prepend(html);
                $("#items_column_"+ id).droppable({
                    activeClass: "ui-state-default",
                    hoverClass: "ui-state-hover",
                    tolerance: "pointer",
                    //accept: ":not(.ui-sortable-helper)",
                    drop: function( event, ui ) {
                        var qty         = $(ui.draggable).data("quantity");
                        var select_qty  = 1;

                        if (qty > 1) {
                            select_qty = prompt("Enter the quantity (1-"+ qty +"):", "1");

                            if (select_qty == null) {
                                return false;
                            }

                            if (select_qty < 1) select_qty = 1;

                            select_qty = Math.floor(select_qty);

                            if ( select_qty > qty ) select_qty = qty;
                        }

                        $( this ).find( ".placeholder" ).remove();

                        qty -= select_qty;
                        //var qty     = $(ui.draggable).data("quantity") - 1;
                        var key     = $(ui.draggable).attr("id");
                        var pid     = $(ui.draggable).data("product-id");
                        var addr_qty= $(".address-item-key-"+ key, this).length;
                        var addr_key= $(this).attr("id").split("_")[2];

                        $( ui.draggable ).data("quantity", qty);
                        $( ".qty", ui.draggable ).text(qty);

                        if ( addr_qty == 0 ) {
                            var classes = $(ui.draggable).attr("class").replace(/(cart\-item)/gi, "address-item");

                            var li = $("<li></li>")
                                .attr("class", classes)
                                .addClass("address-item-key-"+ key )
                                .data("key", key)
                                .data("product-id", pid)
                                .html( ui.draggable.html() ).appendTo( this );

                            for ( var x = 0; x < select_qty; x++ ) {
                                $(li).append('<input type="hidden" name="items_'+addr_key+'[]" value="'+ key +'" />');
                            }

                            $(li)
                                .append('<a class="remove" href="#"><img style="width: 16px; height: 16px;" src="'+ WCMS.base_url +'/images/delete.png" title="Remove" /></a>')
                                .find(".qty").text(select_qty);

                            $(li).find("div.quantity").remove();
                        } else {
                            var item_qty = parseInt($(".address-item-key-"+key, this).find(".qty").text());
                            $(".address-item-key-"+key, this).find(".qty").text(item_qty+select_qty);

                            for ( var x = 0; x < select_qty; x++ ) {
                                $(".address-item-key-"+key, this).append('<input type="hidden" name="items_'+addr_key+'[]" value="'+ key +'" />');
                            }
                        }

                        if ( qty == 0 ) {
                            $( ui.draggable ).remove();
                        }

                        $('#addresses_container').masonry("reload");
                    }
                });

                $('#addresses_container').masonry("reload");
            } else if (data.ack == "ERR") {
                alert(data.message);
            }
        });

        return false;
    });

    $('.address-use').click(function() {
        var address = JSON.parse($(this).parents('p').prev('textarea').val());
        $(this).prop('disabled', true);

        $(".add-address").click();

        setAddress(address, last_id);
    });

    $( "#cart_items ul li" ).draggable({
        appendTo: "body",
        helper: "clone",
        revert: false,
        cursorAt: {left:50, top:30}
    });

    $("li.address-item > a.remove").on("click", function(e) {
        e.preventDefault();

        var parent  = $(this).parents("li.address-item");
        var ul      = $(parent).parents(".items-column");
        var qty     = parseInt($(parent).find(".qty").text());
        var key     = $(parent).data("key");
        var pid     = $(parent).data("product-id");

        if ( $("#"+key).length > 0 ) {
            var orig_qty = parseInt($("#"+key).find(".qty").text());
            var new_qty = orig_qty + qty;
            $("#"+key)
                .data("quantity", new_qty)
                .find(".qty").text(new_qty);
        } else {
            var li = $("<li></li>")
                .html($(parent).html())
                .data("quantity", qty)
                .data("product-id", pid)
                .attr("class", $(parent).attr("class").replace(/(address\-item)/gi, "cart-item"))
                .attr("id", $(parent).data("key"));

            $(li).find("a.remove").remove();
            $(li).find("input").remove();
            $(li).appendTo("ul.cart-items");

            $(li).draggable({
                appendTo: "body",
                helper: "clone",
                revert: false,
                cursorAt: {left:0, top:0}
            });
        }

        $(parent).remove();

        if ( $(ul).children("li").length == 0 ) {
            $(ul).append('<li class="placeholder">Drag items here</li>');
        }

        $('#addresses_container').masonry("reload");
    });

    $("#address_form").submit(function() {
        if ($(".cart-items li").length > 0) {
            alert("Please assign a shipping address for all your cart items.");
            return false;
        }
    });

    $(".items-column").droppable({
        activeClass: "ui-state-default",
        hoverClass: "ui-state-hover",
        tolerance: "pointer",
        //accept: ":not(.ui-sortable-helper)",
        drop: function( event, ui ) {
            var qty         = $(ui.draggable).data("quantity");
            var select_qty  = 1;

            if (qty > 1) {
                select_qty = prompt("Enter the quantity (1-"+ qty +"):", "1");

                if (select_qty == null) {
                    return false;
                }

                if (select_qty < 1) select_qty = 1;

                select_qty = Math.floor(select_qty);

                if ( select_qty > qty ) select_qty = qty;
            }

            $( this ).find( ".placeholder" ).remove();

            qty -= select_qty;

            //var qty     = $(ui.draggable).data("quantity") - 1;
            var key     = $(ui.draggable).attr("id");
            var pid     = $(ui.draggable).data("product-id");
            var addr_qty= $(".address-item-key-"+ key, this).length;
            var addr_key= $(this).attr("id").split("_")[2];

            $( ui.draggable ).data("quantity", qty);
            $( ".qty", ui.draggable ).text(qty);

            if ( addr_qty == 0 ) {
                var classes = $(ui.draggable).attr("class").replace(/(cart\-item)/gi, "address-item");

                var li = $("<li></li>")
                    .attr("class", classes)
                    .addClass("address-item-key-"+ key )
                    .data("key", key)
                    .data("product-id", pid)
                    .html( ui.draggable.html() ).appendTo( this );

                for ( var x = 0; x < select_qty; x++ ) {
                    $(li).append('<input type="hidden" name="items_'+addr_key+'[]" value="'+ key +'" />');
                }

                $(li)
                    .append('<a class="remove" href="#"><img style="width: 16px; height: 16px;" src="'+ WCMS.base_url +'/images/delete.png" title="Remove" /></a>')
                    .find(".qty").text(select_qty);

                $(li).find("div.quantity").remove();
            } else {
                var item_qty = parseInt($(".address-item-key-"+key, this).find(".qty").text());
                $(".address-item-key-"+key, this).find(".qty").text(item_qty+select_qty);

                for ( var x = 0; x < select_qty; x++ ) {
                    $(".address-item-key-"+key, this).append('<input type="hidden" name="items_'+addr_key+'[]" value="'+ key +'" />');
                }

            }

            if ( qty == 0 ) {
                $( ui.draggable ).remove();
            }

            jQuery('#addresses_container').masonry("reload");
        }
    });

    /* State/Country select boxes */
    var states_json = WCMS.countries.replace(/&quot;/g, '"');
    var states = JSON.parse( states_json );

    jQuery('select.country_to_state').on("change", function(){

        var country = $(this).val();

        var $statebox = $(this).closest('div').find('select[id^="address[shipping_state]"], input[id^="address[shipping_state]"]');

        var $parent = $statebox.parent();

        var input_name = $statebox.attr('name');
        var input_id = $statebox.attr('id');
        var value = $statebox.val();

        if (states[country]) {
            if (states[country].length == 0) {

                // Empty array means state field is not used
                $parent.fadeOut(200, function() {
                    $statebox.parent().find('.chzn-container').remove();
                    $statebox.replaceWith('<input type="hidden" class="hidden" name="' + input_name + '" id="' + input_id + '" value="" />');

                    $('body').trigger('country_to_state_changed', [country, $(this).closest('div')]);
                });

            } else {

                $parent.fadeOut(200, function() {
                    var options = '';
                    var state = states[country];

                    for(var index in state) {
                        options = options + '<option value="' + index + '">' + state[index] + '</option>';
                    }
                    if ($statebox.is('input')) {
                        // Change for select
                        $statebox.replaceWith('<select name="' + input_name + '" id="' + input_id + '" class="state_select"></select>');
                        $statebox = $(this).closest('div').find('select[id^="address[shipping_state]"], input[id^="address[shipping_state]"]');
                    }
                    $statebox.html( '<option value="">' + WCMS.select_state_text + '</option>' + options);

                    $statebox.val(value);

                    $('body').trigger('country_to_state_changed', [country, $(this).closest('div')]);

                    $parent.fadeIn(500);
                });

            }
        } else {
            if ($statebox.is('select')) {

                $parent.fadeOut(200, function() {
                    $parent.find('.chzn-container').remove();
                    $statebox.replaceWith('<input type="text" class="input-text" name="' + input_name + '" id="' + input_id + '" />');

                    $('body').trigger('country_to_state_changed', [country, $(this).closest('div')]);
                    $parent.fadeIn(500);
                });

            } else if ($statebox.is('.hidden')) {

                $parent.find('.chzn-container').remove();
                $statebox.replaceWith('<input type="text" class="input-text" name="' + input_name + '" id="' + input_id + '" />');

                $('body').trigger('country_to_state_changed', [country, $(this).closest('div')]);
                $parent.delay(200).fadeIn(500);

            }
        }

        $('body').delay(200).trigger('country_to_state_changing', [country, $(this).closest('div')]);

    }).change();

	$( '#ms_addresses' ).change( function() {
		var selected = $( '#ms_addresses option:selected' );
		var data     = selected.data();
		var state    = '';

		if ( selected.val() == '' ) {
			return;
		}

		for ( var prop in data ) {
			if ( 'shipping_' == prop.substr( 0, 9 ) ) {

				// Save state value for changing later.
				if ( 'shipping_state' == prop ) {
					state = data[ prop ];
				} else {
					if ( $( '.woocommerce-shipping-fields #' + prop ).length ) {
						$( '.woocommerce-shipping-fields #' + prop ).val( data[ prop ] ).change();
					}

					if ( $( '.wcms-address-form .shipping_address #' + prop ).length ) {
						$( '.wcms-address-form .shipping_address #' + prop ).val( data[ prop ] ).change();
					}
				}
			}
		}

		// Register state change after country changes.
		$( 'body' ).one( 'country_to_state_changed', function() {
			$( '.woocommerce-shipping-fields #shipping_state' ).val( state ).change();
			$( '.wcms-address-form .shipping_address #shipping_state' ).val( state ).change();
		} );
		$( '.woocommerce-shipping-fields #shipping_country, .wcms-address-form .shipping_address #shipping_country' ).change();

	} ).change();

    if ( $('#addresses_container').length ) {
        $('#addresses_container').masonry({
            itemSelector: '.account-address',
            isFitWidth: true
        });
    }

    if ( Modernizr && Modernizr.touch ) {
        $("li.cart-item").on("click", function() {

            if ( $(this).hasClass("cart-item-active") ) {

                $(this).removeClass("cart-item-active");
                $("#addresses_container .account-address").removeClass("drop-ready");

            } else {

                $(this).addClass("cart-item-active");
                $("#addresses_container .account-address").addClass("drop-ready");

            }

        });

        $(".items-column").on("click", function() {
            // get all the active items
            var dropzone = this;
            $("li.cart-item-active").each(function() {
                var item        = this;
                var qty         = $(item).data("quantity");
                var select_qty  = 1;

                $(dropzone).find( ".placeholder" ).remove();

                qty -= select_qty;

                var key     = $(item).attr("id");
                var pid     = $(item).data("product-id");
                var addr_qty= $(".address-item-key-"+ key, dropzone).length;
                var addr_key= $(dropzone).attr("id").split("_")[2];

                $( item ).data("quantity", qty);
                $( ".qty", item ).text(qty);

                if ( addr_qty == 0 ) {
                    var classes = $(item).attr("class").replace(/(cart\-item)/gi, "address-item");

                    var li = $("<li></li>")
                        .attr("class", classes)
                        .addClass("address-item-key-"+ key )
                        .data("key", key)
                        .data("product-id", pid)
                        .html( $(item).html() );

                    $(dropzone).append( li );

                    for ( var x = 0; x < select_qty; x++ ) {
                        $(li).append('<input type="hidden" name="items_'+addr_key+'[]" value="'+ key +'" />');
                    }

                    $(li)
                        .append('<a class="remove" href="#"><img style="width: 16px; height: 16px;" src="'+ WCMS.base_url +'/images/delete.png" title="Remove" /></a>')
                        .find(".qty").text(select_qty);

                    $(li).find("div.quantity").remove();
                } else {
                    var item_qty = parseInt($(".address-item-key-"+key, dropzone).find(".qty").text());
                    $(".address-item-key-"+key, dropzone).find(".qty").text(item_qty+select_qty);

                    for ( var x = 0; x < select_qty; x++ ) {
                        $(".address-item-key-"+key, dropzone).append('<input type="hidden" name="items_'+addr_key+'[]" value="'+ key +'" />');
                    }

                }

                if ( qty == 0 ) {
                    $( item ).remove();
                }

                jQuery('#addresses_container').masonry("reload");
            });
        });
    }

    $( '.delete-line-item').on('click', function() {
        var key     = $(this).data("key");
        var index   = $(this).data("index");

        $("#delete_index").val( index );
        $("#delete_key").val( key );
    });
});

jQuery(window).resize( function() {
    wcms_resize_thickbox();
});

function wcms_resize_thickbox() {
    var width = jQuery(window).width(); // 80% of the screen size, max of 400

    if ( width < 400 ) {
        width = 300;
        var win = jQuery(document).find('#TB_window')

        win.css({
            'width': width +'px',
            marginLeft: '-' + parseInt((width / 2), 10) +'px'
        });

        jQuery(document).find('#TB_ajaxContent').css('width', 'auto');
    }

}
