/**
 * Admin
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Category Accordion
 * @version 1.0.0
 */

jQuery(function ($) {

    $.toggle_field = function (name, action) {

        switch (action) {

            case 'show' :

                name.show();
                break;
            case 'hide':

                name.hide();
                break;

        }
    },
        $.add_woocommerce_order_option = function (container) {

            var order_select = container.find('.ywcca_type_order');

            if (!order_select.find('option [value="menu_order"]').length) {
                order_select.append('<option value="menu_order">WooCommerce Order</option>');
            }
        },

        $.remove_woocommerce_order_option = function (container) {
            var menu_order = container.find('.ywcca_type_order option[value="menu_order"]');

            if (menu_order.length) {
                menu_order.remove();
            }
        };

    $(document).on('change', '.ywcca_select_howshow', function (e) {

        var t = $(this),
            container = t.parents('#ywcca_widget_content'),
            wc = container.find('.ywcca_wc_field'),
            wp = container.find('.ywcca_wp_field'),
            menu = container.find('.ywcca_menu_field'),
            tag = container.find('.ywcca_tags_field'),
            count = container.find('.ywcc_show_count_field'),
            order = container.find('.ywcca_orderby'),
            value = t.val();


        $.remove_woocommerce_order_option(container);

        switch (value) {

            case 'wc'  :
                $.toggle_field(wc, 'show');
                $.toggle_field(count, 'show');
                $.toggle_field(wp, 'hide');
                $.toggle_field(menu, 'hide');
                $.toggle_field(tag, 'hide');
                $.toggle_field(order, 'show');
                $.add_woocommerce_order_option(container);
                break;

            case 'wp' :
                $.toggle_field(wc, 'hide');
                $.toggle_field(count, 'show');
                $.toggle_field(wp, 'show');
                $.toggle_field(menu, 'hide');
                $.toggle_field(tag, 'hide');
                $.toggle_field(order, 'show');
                break;

            case 'menu' :
                $.toggle_field(wc, 'hide');
                $.toggle_field(count, 'hide');
                $.toggle_field(wp, 'hide');
                $.toggle_field(menu, 'show');
                $.toggle_field(tag, 'hide');
                $.toggle_field(order, 'hide');
                break;

            case 'tag' :
                $.toggle_field(wc, 'hide');
                $.toggle_field(count, 'hide');
                $.toggle_field(wp, 'hide');
                $.toggle_field(menu, 'hide');
                $.toggle_field(tag, 'show');
                $.toggle_field(order, 'show');

                break;

            default:
                $.toggle_field(wc, 'hide');
                $.toggle_field(count, 'hide');
                $.toggle_field(wp, 'hide');
                $.toggle_field(menu, 'hide');
                $.toggle_field(tag, 'hide');
                $.toggle_field(order, 'hide');
                break;
        }
    })

});