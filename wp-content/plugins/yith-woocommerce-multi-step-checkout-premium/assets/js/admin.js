(function( $ ){
    /* === Admin: ThankYou Page Tab === */

    var disable_opt = function ( disable ) {
            disable.css('opacity', '0.3');
            disable.css('pointer-events', 'none');
        },

        enable_opt = function ( disable ) {
            disable.css('opacity', '1');
            disable.css('pointer-events', 'auto');
        };

    var style           = $('#yith_wcms_thankyou_style'),
        thankyou_style = style.val(),
        ids             = new Array( 'yith_wcms_highlight_color','yith_wcms_table_header_backgroundcolor', 'yith_wcms_table_header_color', 'yith_wcms_table_row_backgroundcolor', 'yith_wcms_table_details_color', 'yith_wcms_details_background_color' );

    var switch_option = function() {
        for (var k = 0 in ids) {
            var elem = $('#' + ids[k]).parent().parent();
            thankyou_style == 'theme' ? disable_opt(elem) : enable_opt(elem);
        }
    }

    switch_option();

    style.on( 'change', function(){
        thankyou_style = $(this).val();
        switch_option();
    } );

    /* === Admin: Timeline & Button Page Tab === */

    var timeline_style = $( '#yith_wcms_timeline_template'),
    styles = {
        margin: '1em 0',
        paddingLeft: 0
    };

    if( typeof timeline_style != 'undefined' ){
        var current_style = timeline_style.val(),
            title = $( '.yith_wcms_title.' + current_style );
        $('.yith_wcms_title').add('.yith_wcms_table').hide();
        title.add( '.yith_wcms_table.' + current_style ).show();
        title.css( styles );
    }

    timeline_style.on('change', function(){
        var new_style = timeline_style.val(),
        title = $( '.yith_wcms_title.' + new_style );
        $('.yith_wcms_title').add('.yith_wcms_table').hide();
        title.add( '.yith_wcms_table.' + new_style ).show();
        title.css( styles );
    });

    /* === Admin: Navigation Button === */

    var nav_button_check = $('#yith_wcms_nav_buttons_enabled'),
        button_ids       = new Array( 
            'yith_wcms_timeline_options_next',
            'yith_wcms_timeline_options_prev', 
            'yith_wcms_nav_disabled_prev_button',
            'yith_wcms_timeline_options_skip_login',
            'yith_wcms_nav_enable_bakc_to_cart_button',
            'yith_wcms_timeline_options_label_back_to_cart'
            ),
        enable_button    = function(e){
            if (typeof nav_button_check != 'undefined') {
                for (var k = 0 in button_ids) {
                    var elem = $('#' + button_ids[k]);
                    nav_button_check.is(':checked') ? enable_opt(elem) : disable_opt(elem);
                }
            }
        };

    nav_button_check.on( 'change click yith_init_nav_button', function(e){ enable_button(e); } );
    nav_button_check.trigger( 'yith_init_nav_button' );

    /* === Admin: Timeline Icon === */
    var step_count_select = $( '#yith_wcms_timeline_step_count_type');
        enable_icon = function(){
            var icon_option_row = $('.forminp-yith_wcms_media_upload').parent();
            step_count_select.val() == 'icon' ? enable_opt(icon_option_row) : disable_opt(icon_option_row);
        };

    step_count_select.on( 'change yith_wcms_step_count_change', function(){ enable_icon(); });
    step_count_select.trigger('yith_wcms_step_count_change');

    /* === Admin: Payments tab ===  */
    var payment_tab_check = $('#yith_wcms_show_amount_on_payments'),
        payment_deps_ids       = new Array( 'yith_wcms_show_amount_on_payments_text' ),
        enable_payment_text    = function(e){
            if (typeof payment_tab_check != 'undefined') {
                for (var k = 0 in payment_deps_ids) {
                    var elem = $('#' + payment_deps_ids[k]).parent();
                    payment_tab_check.is(':checked') ? enable_opt(elem) : disable_opt(elem);
                }
            }
        };

    payment_tab_check.on( 'change click yith_init_nav_button', function(e){ enable_payment_text(e); } );
    payment_tab_check.trigger( 'yith_init_nav_button' );

    /* === Admin: ScrollTop Option ===  */
    var scroll_top_tab_check = $('#yith_wcms_scroll_top_enabled'),
        scroll_top_deps_ids       = new Array( 'yith_wcms_scroll_top_anchor' ),
        enable_scroll_top_anchor  = function(e){
            if (typeof scroll_top_tab_check != 'undefined') {
                for (var k = 0 in scroll_top_deps_ids) {
                    var elem = $('#' + scroll_top_deps_ids[k]).parent();
                    scroll_top_tab_check.is(':checked') ? enable_opt(elem) : disable_opt(elem);
                }
            }
        };

    scroll_top_tab_check.on( 'change click yith_init_nav_button', function(e){ enable_scroll_top_anchor(e); } );
    scroll_top_tab_check.trigger( 'yith_init_nav_button' );

    /* === Admin: ScrollTop Option ===  */
    var my_account_style_check      = $('#yith_wcms_timeline_use_my_account_in_login_step'),
        my_account_style_deps_ids   = new Array( 'woocommerce_enable_myaccount_registration' ),
        enable_my_account_style  = function(e){
            if (typeof my_account_style_check != 'undefined') {
                for (var k = 0 in my_account_style_deps_ids) {
                    var elem = $('#' + my_account_style_deps_ids[k]).parent();
                    my_account_style_check.is(':checked') ? enable_opt(elem) : disable_opt(elem);
                }
            }
        };

    my_account_style_check.on( 'change click yith_init_nav_button', function(e){ enable_my_account_style(e); } );
    my_account_style_check.trigger( 'yith_init_nav_button' );

    /* === Admin: ScrollTop Option ===  */
    var login_step_style_check      = $('#woocommerce_enable_checkout_login_reminder'),
        login_step_style_deps_ids   = new Array( 'yith_wcms_form_checkout_login_message', 'yith_wcms_timeline_use_my_account_in_login_step', 'woocommerce_enable_myaccount_registration' ),
        enable_login_step  = function(e){
            if (typeof login_step_style_check != 'undefined') {
                for (var k = 0 in login_step_style_deps_ids) {
                    var elem = $('#' + login_step_style_deps_ids[k]).parent();
                    login_step_style_check.is(':checked') ? enable_opt(elem) : disable_opt(elem);
                }
            }
        };

    login_step_style_check.on( 'change click yith_init_nav_button', function(e){ enable_login_step(e); } );
    login_step_style_check.trigger( 'yith_init_nav_button' );
})(jQuery);
