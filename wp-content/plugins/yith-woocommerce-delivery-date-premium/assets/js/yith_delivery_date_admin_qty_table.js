jQuery(document).ready(function ($) {

    $('#ywcdd_table_how_set_table input[type="radio"]').on('change', function (e) {

        var product_row = $('#ywcdd_table_select_product-container').parent(),
            product_cat_row = $('#ywcdd_table_select_product_cat-container').parent(),
            value = $(this).val();

        if ($(this).is(':checked')) {
            if ('product' === value) {
                product_row.show();
                product_cat_row.hide();
            } else {
                product_row.hide();
                product_cat_row.show();
            }
        }
    }).trigger('change');

    $(document).on('change', '.ywcdd_quantity_table_content select.ywcdd_enable_day', function (e) {

        var val = $(this).val(),
            parent = $(this).parents('td'),
            field = parent.find('.ywcdd_product_day_value'),
            select_field = parent.find('.ywcdd_day_value_type');

        field.toggleClass('yith-disabled');
        select_field.toggleClass('yith-disabled');

    });
    $(document).on('click', '.ywcdd_add_new_row button', function (e) {
        e.preventDefault();

        var template = wp.template('ywcdd-product-table-row'),
            table = $(this).parents('.ywcdd_quantity_table_content'),
            tbody = table.find('table tbody'),
            row = tbody.find('tr'),
            counter = row.length;

        template = $(template({row: counter}));

        tbody.append(template);
        $(document.body).trigger('wc-enhanced-select-init');
    });

    $(document).on( 'click', 'a.ywcdd_remove_row', function (e) {
       e.preventDefault();

       var row = $(this).parents('tr');

       row.remove();
    });
});