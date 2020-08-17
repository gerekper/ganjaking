/**
 * Admin
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Ajax Navigation
 * @version 1.3.2
 */
jQuery(function ($) {
    $.add_new_range = function (t) {
        var range_filter = t.parents('.widget-content').find('.range-filter'),
            input_field = range_filter.find('input:last-child'),
            field_name = range_filter.data('field_name'),
            position = parseInt(input_field.data('position')) + 1,
            html = '<input type="text" placeholder="min" name="' + field_name + '[' + position + '][min]" value="" class="yith-wcan-price-filter-input widefat" data-position="' + position + '"/>' +
                   '<input type="text" placeholder="max" name="' + field_name + '[' + position + '][max]" value="" class="yith-wcan-price-filter-input widefat" data-position="' + position + '"/>';

        range_filter.append(html);
    };

    $.select_dropdown = function( elem ) { console.log( elem );
        var t = elem,
           select = t.parents('p').next('p');

        t.is(':checked') ? select.fadeIn('slow') : select.fadeOut('slow');
    }

    $(document).on('change', '.yith_wcan_type, .yith_wcan_attributes', function (e) {
        var t = this,
            container       = $(this).parents('.widget-content').find('.yith_wcan_placeholder').html(''),
            spinner         = container.next('.spinner').show(),
            display         = $(this).parents('.widget-content').find('#yit-wcan-display'),
            style           = $(this).parents('.widget-content').find('#yit-wcan-style'),
            show_count      = $(this).parents('.widget-content').find('#yit-wcan-show-count'),
            attributes      = $(this).parents('.widget-content').find('.yith-wcan-attribute-list'),
            tag_list        = $(this).parents('.widget-content').find('.yit-wcan-widget-tag-list'),
            see_all_text    = $(this).parents('.widget-content').find('.yit-wcan-see-all-taxonomies-text');

        var data = {
            action   : 'yith_wcan_select_type',
            id       : $('input[name=widget_id]', $(t).parents('.widget-content')).val(),
            name     : $('input[name=widget_name]', $(t).parents('.widget-content')).val(),
            attribute: $('.yith_wcan_attributes', $(t).parents('.widget-content')).val(),
            value    : $('.yith_wcan_type', $(t).parents('.widget-content')).val()
        };

        /* Hierarchical hide/show */
        if (data.value == 'list' || data.value == 'select' || data.value == 'brands' || data.value == 'tags' ) {
            display.show();
            style.hide();
        } else if (data.value == 'label' || data.value == 'color' || data.value == 'multicolor' ) {
            display.hide();
        }

        if( data.value == 'color' || data.value == 'multicolor' ){
            style.show();
        } else {
            style.hide();
        }

        if( data.value == 'list' || data.value == 'tags' || data.value == 'brands' || data.value == 'categories' || data.value == 'select' ){
            show_count.show();
        } else {
            show_count.hide();
        }

        if( data.value == 'tags' || data.value == 'brands' || data.value == 'categories' ){
            attributes.hide();
        } else {
            attributes.show();
        }

        if( data.value == 'tags' ){
            tag_list.show();
        } else {
            tag_list.hide();
        }

        if( data.value == 'tags' || data.value == 'categories' ){
            see_all_text.show();
        } else {
            see_all_text.hide();
        }

       $.post(ajaxurl, data, function (response) {
            spinner.hide();
            container.html(response.content);
            $(document).trigger('yith_colorpicker');
        }, 'json');
    });

    //color-picker
    $(document).on('yith_colorpicker',function () {
        $('.yith-colorpicker').each(function () {
            $(this).wpColorPicker();
        });
    }).trigger('yith_colorpicker');

    $(document).on('change', '.yith-wcan-enable-custom-style', function(){
        var t = $(this),
            enable_custom_style = t.parents('.widget-content').find('.yith-wcan-reset-custom-style'),
            checked             = t.find('.yith-wcan-enable-custom-style-check').is(':checked');

        checked ? enable_custom_style.fadeIn('slow') : enable_custom_style.fadeOut('slow');
    });

    $(document).on('change', '.yith-wcan-dropdown-check', function(){
        $.select_dropdown( $(this) );
    });

    //Filter By Tag tab
    var select_all      = $('.yith-wcan-select-option .select-all'),
        unselect_all    = $('.yith-wcan-select-option .unselect-all'),
        checklist       = $('.yith_wcan_select_tag'),
        widget_select   = $('#yith-wcan-tag-widget-select');

    select_all.on('click', function(e){
        e.preventDefault();
        $(this).parents( '.yith-wcan-select-option').next('.yith_wcan_select_tag_wrapper').find('.yith_wcan_tag_list_checkbox').attr( 'checked', true );
    });

    unselect_all.on('click', function(e){
        e.preventDefault();
        $(this).parents( '.yith-wcan-select-option').next('.yith_wcan_select_tag_wrapper').find('.yith_wcan_tag_list_checkbox').attr( 'checked', false );
    });
});
