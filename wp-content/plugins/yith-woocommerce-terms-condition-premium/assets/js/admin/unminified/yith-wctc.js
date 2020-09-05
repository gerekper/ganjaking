jQuery( document ).ready( function( $ ){
    var terms_type = $( '#yith_wctc_terms_type'),
        hide_checkbox = $( '#yith_wctc_hide_checkboxes'),
        terms_fields = $( '#yith_wctc_terms_fields' ),
        terms_fields_row = terms_fields.parents( 'tr' ),
        terms_page_id = $( '#woocommerce_terms_page_id'),
        terms_page_id_row = terms_page_id.parents( 'tr' ),
        terms_text = $( '#yith_wctc_terms_text'),
        terms_text_row = terms_text.parents( 'tr' ),
        terms_label = $( '#yith_wctc_terms_label'),
        terms_label_row = terms_label.parents( 'tr' ),
        terms_checked = $( '#yith_wctc_terms_checked'),
        terms_checked_row = terms_checked.parents( 'tr' ),
        privacy_page_id = $( '#yith_wctc_privacy_page_id'),
        privacy_page_id_row = privacy_page_id.parents( 'tr' ),
        privacy_text = $( '#yith_wctc_privacy_text'),
        privacy_text_row = privacy_text.parents( 'tr' ),
        privacy_label = $( '#yith_wctc_privacy_label'),
        privacy_label_row = privacy_label.parents( 'tr' ),
        privacy_checked = $( '#yith_wctc_privacy_checked'),
        privacy_checked_row = privacy_checked.parents( 'tr' ),
        common_text = $( '#yith_wctc_common_text'),
        common_text_row = common_text.parents( 'tr' ),
        common_checked = $( '#yith_wctc_common_checked'),
        common_checked_row = common_checked.parents( 'tr' ),
        popup_button = $( '#yith_wctc_popup_button'),
        popup_button_text = $( '#yith_wctc_popup_button_text'),
        popup_button_text_row = popup_button_text.parents( 'tr'),
        scroll_till_end = $( '#yith_wctc_scroll_till_end'),
        scroll_till_end_message = $( '#yith_wctc_scroll_till_end_message' ),
        scroll_till_end_message_row = scroll_till_end_message.parents( 'tr'),
        agree_button_type = $( '#yith_wctc_agree_button_type'),
        agree_button_style = $( '#yith_wctc_agree_button_style'),
        agree_button_style_row = agree_button_style.parents( 'tr' ),
        agree_button_round_corners = $( '#yith_wctc_agree_button_round_corners' ),
        agree_button_round_corners_row = agree_button_round_corners.parents( 'tr'),
        agree_button_background_color = $( '#yith_wctc_agree_button_background_color' ),
        agree_button_background_color_row = agree_button_background_color.parents( 'tr' ),
        agree_button_color = $( '#yith_wctc_agree_button_color' ),
        agree_button_color_row = agree_button_color.parents( 'tr' ),
        agree_button_border_color = $( '#yith_wctc_agree_button_border_color' ),
        agree_button_border_color_row = agree_button_border_color.parents( 'tr' ),
        agree_button_background_hover_color = $( '#yith_wctc_agree_button_background_hover_color' ),
        agree_button_background_hover_color_row = agree_button_background_hover_color.parents( 'tr' ),
        agree_button_hover_color = $( '#yith_wctc_agree_button_hover_color' ),
        agree_button_hover_color_row = agree_button_hover_color.parents( 'tr' ),
        agree_button_border_hover_color = $( '#yith_wctc_agree_button_border_hover_color' ),
        agree_button_border_hover_color_row = agree_button_border_hover_color.parents( 'tr' ),

        dependencies_handler = function(){
            var t = terms_type,
                val = t.val();
            if( 'terms' == val ){
                terms_checked_row
                    .add( terms_page_id_row )
                    .add( terms_text_row )
                    .add( terms_label_row )
                    .show();

                privacy_page_id_row
                    .add( privacy_text_row )
                    .add( privacy_label_row )
                    .add( privacy_checked_row )
                    .add( common_text_row )
                    .add( common_checked_row )
                    .add( terms_fields_row )
                    .hide();
            }
            else if( 'privacy' == val ){
                terms_checked_row
                    .add( terms_page_id_row )
                    .add( terms_text_row )
                    .add( terms_label_row )
                    .add( common_text_row )
                    .add( common_checked_row )
                    .add( terms_fields_row )
                    .hide();

                privacy_page_id_row
                    .add( privacy_text_row )
                    .add( privacy_label_row )
                    .add( privacy_checked_row )
                    .show();
            }
            else {
                terms_fields_row.show();

                privacy_page_id_row
                    .add( terms_page_id_row )
                    .show();

                second_level_dependencies_handler();
            }

            hide_checkbox_dependencies();
        },
        second_level_dependencies_handler = function(){
            var val = terms_fields.val();

            if( ! terms_fields.is( ':visible' ) ){
                return;
            }

            if( 'together' == val ){
                common_text_row
                    .add( common_checked_row )
                    .add( terms_page_id_row )
                    .add( terms_label_row )
                    .add( privacy_page_id_row )
                    .add( privacy_label_row )
                    .show();

                terms_text_row
                    .add( terms_checked_row )
                    .add( privacy_text_row )
                    .add( privacy_checked_row )
                    .hide();
            }
            else if( 'apart' == val ){
                common_text_row
                    .add( common_checked_row )
                    .hide();

                terms_text_row
                    .add( terms_checked_row )
                    .add( privacy_text_row )
                    .add( privacy_checked_row )
                    .show();
            }

            hide_checkbox_dependencies();
        },
        hide_checkbox_dependencies = function(){
            if( hide_checkbox.is( ':checked' ) ){
                common_checked_row
                    .add( privacy_checked_row )
                    .add( terms_checked_row )
                    .hide();
            }
            else{
                if( terms_type.val() == 'terms' || ( terms_type.val() == 'both' && terms_fields.val() == 'apart' ) ){
                    terms_checked_row.show();
                }

                if( terms_type.val() == 'privacy' || ( terms_type.val() == 'both' && terms_fields.val() == 'apart' ) ){
                    privacy_checked_row.show();
                }

                if( terms_type.val() == 'both' && terms_fields.val() == 'together' ){
                    common_checked_row.show();
                }
            }
        },
        show_popup_button = function(){
            if( popup_button.is( ':checked' ) ){
                popup_button_text_row.show();
            }
            else{
                popup_button_text_row.hide();
            }
        },
        show_scroll_till_end_message = function(){
            if( scroll_till_end.is( ':checked' ) ){
                scroll_till_end_message_row.show();
            }
            else{
                scroll_till_end_message_row.hide();
            }
        },
        show_agree_button_style = function(){
            if( agree_button_type.val() == 'button' ){
                agree_button_style_row.show();
            }
            else{
                agree_button_style_row.hide();
            }

            show_agree_button_options();
        },
        show_agree_button_options = function(){
            if( agree_button_style_row.is( ':visible' ) && agree_button_style.val() == 'custom' ){
                agree_button_round_corners_row
                    .add( agree_button_background_color_row )
                    .add( agree_button_color_row )
                    .add( agree_button_border_color_row )
                    .add( agree_button_background_hover_color_row )
                    .add( agree_button_hover_color_row )
                    .add( agree_button_border_hover_color_row )
                    .show();
            }
            else{
                agree_button_round_corners_row
                    .add( agree_button_background_color_row )
                    .add( agree_button_color_row )
                    .add( agree_button_border_color_row )
                    .add( agree_button_background_hover_color_row )
                    .add( agree_button_hover_color_row )
                    .add( agree_button_border_hover_color_row )
                    .hide();
            }
        };

    terms_type.on( 'change', dependencies_handler );
    terms_fields.on( 'change', second_level_dependencies_handler );
    hide_checkbox.on( 'change', hide_checkbox_dependencies );
    popup_button.on( 'change', show_popup_button );
    scroll_till_end.on( 'change', show_scroll_till_end_message );
    agree_button_type.on( 'change', show_agree_button_style );
    agree_button_style.on( 'change', show_agree_button_options );

    dependencies_handler();
    second_level_dependencies_handler();
    hide_checkbox_dependencies();
    show_popup_button();
    show_scroll_till_end_message();
    show_agree_button_style();
} );