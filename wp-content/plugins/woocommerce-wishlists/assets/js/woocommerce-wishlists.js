;
(function ($, window, document, undefined) {

    $.fn.wc_wishlists_form = function () {
        var $form = this;

        if ($form.hasClass('variations_form')) {
            $form.find('.wl-add-to').addClass('disabled');
        }

        $form
            .on('hide_variation', function () {
                $form.find('.wl-add-to').addClass('disabled');
            })
            .on('show_variation', function () {
                $form.find('.wl-add-to').removeClass('disabled');
            });


        setTimeout(function () {
            $form.trigger('check_variations');
        }, 50);

    };

    $(document).on('wc_variation_form', function (e) {
        var $form = $(e.target);
        $form.wc_wishlists_form();
    });

})(jQuery, window, document);


(function ($) {

    var WCWL = {
        current_product_form: 0
    };


    jQuery(document).ready(function ($) {
        //it seems composite products has changed their class name.

        var composite_class = $('.composite_wrap').length ? '.composite_wrap' : false;
        if (!composite_class) {
            composite_class = $('.bundle_wrap').length === 1 ? '.bundle_wrap' : ($('.composite_wrap').length ? '.composite_wrap' : false);
        }


        var bundles = $(composite_class);
        if (bundles.length) {
            $('.wl-button-wrap').first().appendTo($(composite_class)).removeClass('hide');
        } else {
            var variations = $('.variations_button');
            if (variations.length) {
                $('.wl-button-wrap').removeClass('hide');
            }
        }


        //When page loads...
        $(".wl-panel").hide(); //Hide all content
        $("ul.wl-tabs li:first").addClass("active").show(); //Activate first tab
        $(".wl-panel:first").show(); //Show first tab content

        //On Click Event
        $("ul.wl-tabs li").click(function () {
            $("ul.wl-tabs li").removeClass("active"); //Remove any "active" class
            $(this).addClass("active"); //Add "active" class to selected tab
            $(".wl-panel").hide(); //Hide all tab content
            var activeTab = $(this).find("a").attr("href"); //Find the href attribute value to identify the active tab + content
            $(activeTab).fadeIn(); //Fade in the active ID content
            return false;
        });

        /////////////////////////////////
        // add to wishlist button effects
        /////////////////////////////////	

        // basic wishlist popup stuff	
        $('#wl-list-pop-wrap').hide(); // hide background click-off on load	
        $('.wl-list-pop').hide(); // hide modal on load	
        $('#wl-list-pop-wrap').click(function () {

            WCWL.current_product_form = null;
            WCWL.current_product_id = 0;

            $('.wl-list-pop').hide(); // hide modal when click in background	
            $('#wl-list-pop-wrap').hide(); // hide background click-off
            $(window).unbind('scroll', adjust_scroll);
        });

        _productlink = null;
        // position popup at button click 

        $('body').on('click', '.wl-add-to', function (e) {
            if ($(this).hasClass('disabled')) {
                e.preventDefault();
                return false;
            }

            WCWL.current_product_form = $(this).closest('form.cart').eq(0);
            if (!WCWL.current_product_form || WCWL.current_product_form.length === 0) {
                if ($(this).closest('form.composite_form').length) {
                    console.log('composite_form');
                    WCWL.current_product_form = $(this).closest('form.composite_form').eq(0);
                } else if ($(this).closest('form.bundle_form').length) {
                    console.log('bundle_form');
                    WCWL.current_product_form = $(this).closest('form.bundle_form').eq(0);
                }
            }

            WCWL.current_product_id = $(this).data('productid');

            _productlink = $(this);

            if ($(this).hasClass('wl-add-to-single')) {
                return;
            }

            $('#wl-list-pop-wrap').show(); // show background click-off on click
            $('.wl-list-pop').show(); // show modal on click

            var wlx = $(this).offset().left;
            var wly = $(this).offset().top;
            // need to add some code to adjust in case the user is logged in. WHen user is logged in with admin bar, it messes up the CSS since the body/html tags have the margin top on it 
            // need a way to check if admin bar is present, and if so, adjustt the coords below to subtract 28
            if ($('#wpadminbar ').length) { // if admin bar exists, adjust numbers to compensate for bar
                $(".wl-list-pop").css({
                    top: wly - 28,
                    left: wlx
                }).show();
            } else { // if not logged in, just display in regular position
                $(".wl-list-pop").css({
                    top: wly,
                    left: wlx
                }).show();
            }

            $(window).bind('scroll', adjust_scroll);

            return false;
        });

        function adjust_scroll() {
            var buttontop = _productlink.offset().top;
            if ($('#wpadminbar ').length) {
                buttontop = buttontop - 28;
            }

            $(".wl-list-pop").css({
                top: buttontop
            });
        }

        // close wishlist on esc key press
        $(document).keyup(function (e) {
            if (e.keyCode == 27) {
                $('.wl-list-pop-wrap').hide();
            }
        });

        $('body').on('click', '.wl-add-to-single', function (event) {
            event.preventDefault();
            var wlid = $(this).data('listid');

            var $form = WCWL.current_product_form;
            $form.find("input#wlid").val(wlid);


            var sep = wishlist_params.current_url.indexOf('?') >= 0 ? '&' : '?';
            const form_action = wishlist_params.current_url + sep + 'add-to-wishlist-itemid=' + WCWL.current_product_id;
            $form.attr('action', form_action);

            //console.log($form.find("input#wlid").eq(0).val());
            //console.log($form.attr('action'));
            $form.attr('method', 'post');
            $form.submit();

            return false;
        });


        $('.wl-shop-add-to-single').click(function (event) {
            event.preventDefault();
            window.location.href = _productlink.attr('href') + "&wlid=" + $(this).data('listid');
            return false;
        });


        $('.wlconfirm').click(function () {
            var message = $(this).data('message');

            var answer = confirm(message ? message : wishlist_params.are_you_sure);
            return answer;
        });

        $('input[type=checkbox]', '.wl-table thead tr th').click(function () {
            $(this).closest('table').find(':checkbox').attr('checked', this.checked);
        });


        $('.share-via-email-button').click(function (event) {
            var form_id = $(this).data('form');
            $('#' + form_id).trigger('submit', []);
            return true;
        });


        $('.move-list-sel').change(function (event) {

            $('.move-list-sel').val($(this).val());

        });

        $('.btn-apply').click(function (event) {
            event.preventDefault();

            $("#wlupdateaction").val('bulk');
            $('#wl-items-form').submit();

            return false;
        });

        $('#wleditaction1').change(function () {
            $('#wleditaction2').val($(this).val());
        });

        $('#wleditaction2').change(function () {
            $('#wleditaction1').val($(this).val());
        });


        $('.wishlist-add-to-cart-button').click(function (e) {
            e.preventDefault();

            var href = ($(this).attr('href'));
            var qty = $(this).closest('tr').find('input.qty').val();

            var url = href + '&quantity=' + qty;

            window.location = url;

            return false;

        });

        $('.wishlist-add-to-cart-button-view').click(function (e) {
            e.preventDefault();

            var href = ($(this).attr('href'));
            var qty = prompt(wishlist_params.quantity_prompt, $(this).data('qty'));

            if (qty == null || qty == "") {
                return false;
            } else {
                var url = href + '&quantity=' + qty;
                window.location = url;
                return false;
            }

        });


    });

})(jQuery);
