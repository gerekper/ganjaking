jQuery(document).ready(function ($) {

    var set_hidden_field = function( date_selected, last_date, price, quantity ){

        var qty_field = $(document).find('.quantity .qty'),
            price_field = $('#ywcdd_new_price'),
            date_field = $('#ywcdd_date_selected'),
            last_shipping_date_field = $('#ywcdd_last_shipping_date');

        qty_field.val( quantity );
        price_field.val( price);
        date_field.val( date_selected);
        last_shipping_date_field.val( last_date);
    },
        set_recap_row = function ( date_selected,  price, quantity ){

            var recap_date = $('#ywcdd_delivery_recap_date'),
                recap_qty  = $('#ywcdd_delivery_recap_quantity'),
                recap_total = $('#ywcdd_delivery_recap_total');

            recap_date.find('p.ywcdd_recap_value').html( date_selected );
            recap_qty.find('p.ywcdd_recap_value').html( quantity );
            recap_total.find('p.ywcdd_recap_value').html( price );
        };
  var first_day = $(document).find('#ywcdd_quantity_table_wrap table th.ywcdd_day:not(.ywcdd_disable_all_day)').get(0);

    $(document).on('click', '#ywcdd_quantity_table_wrap table#ywcdd_quantity_table th.ywcdd_day:not(.ywcdd_disable_all_day)', function (e) {

        var th_day = $(this),
            date_selected = th_day.find('div').data('delivery_date'),
            last_date_selected = th_day.find('div').data('last_shipping_date'),
            day_column = th_day.data('day_column'),
            classes = 'day_' + day_column + ':not(.ywcdd_disable_day)',
            table = th_day.parents('table');

        classes = classes.replace(' ', '');
        var td = table.find('tbody td.' + classes).get(0),
            row = $(td).parent(),
            qty_td = row.find('.ywcdd_quantity'),
            qty_value = qty_td.data('qty'),
            price = $(td).data('price');

        if( $(td).length ) {
            table.find('tbody td').removeClass('ywcdd_day_selected');
            table.find('thead th').removeClass('ywcdd_day_selected');
            $(td).addClass('ywcdd_day_selected');
            qty_td.addClass('ywcdd_day_selected');
            th_day.addClass('ywcdd_day_selected');

            set_hidden_field(date_selected, last_date_selected, price, qty_value);
            set_recap_row(th_day.find('div').html(), $(td).html(), qty_value);
        }
    });
    $(document).on('click', '#ywcdd_quantity_table_wrap table td.ywcdd_quantity', function(e){

        var qty_td = $(this),
            row = qty_td.parent(),
            price_td = $( row.find('td.ywcdd_day:not(.ywcdd_disable_day)').get(0) ),
            column = price_td.data('day_column'),
            table = qty_td.parents('table#ywcdd_quantity_table'),
            day_column = $(document).find('#ywcdd_quantity_table_wrap table th.ywcdd_day.day_'+column);


       var price = price_td.data('price'),
           qty_value = qty_td.data('qty'),
           date_selected = day_column.find('div').data('delivery_date'),
           last_shipping_date = day_column.find('div').data('last_shipping_date');


       if( price_td.length) {
           table.find('tbody td').removeClass('ywcdd_day_selected');
           table.find('thead th').removeClass('ywcdd_day_selected');

           qty_td.addClass('ywcdd_day_selected');
           price_td.addClass('ywcdd_day_selected');
           day_column.addClass('ywcdd_day_selected');


           set_hidden_field(date_selected, last_shipping_date, price, qty_value);
           set_recap_row(day_column.find('div').html(), price_td.html(), qty_value);
       }
    });
    $(document).on('click', '#ywcdd_quantity_table_wrap table td.ywcdd_day:not(.ywcdd_disable_day)', function(e){

        var price_td = $(this),
            row = price_td.parent(),
            qty_td = row.find('td.ywcdd_quantity'),
            column = price_td.data('day_column'),
            table = qty_td.parents('table#ywcdd_quantity_table'),
            day_column = $(document).find('#ywcdd_quantity_table_wrap table th.ywcdd_day.day_'+column);


        var price = price_td.data('price'),
            qty_value = qty_td.data('qty'),
            date_selected = day_column.find('div').data('delivery_date'),
            last_shipping_date = day_column.find('div').data('last_shipping_date');



        table.find( 'tbody td').removeClass( 'ywcdd_day_selected');
        table.find( 'thead th').removeClass( 'ywcdd_day_selected');

        qty_td.addClass('ywcdd_day_selected');
        price_td.addClass('ywcdd_day_selected');
        day_column.addClass('ywcdd_day_selected');


        set_hidden_field( date_selected,last_shipping_date,price,qty_value );
        set_recap_row( day_column.find('div').html(),price_td.html(),qty_value );

    });

    $(first_day).click();

    if( $(document).find('form.variations_form').length ){

        var original_table = $('#ywcdd_quantity_table_container');

        $('.variations_form.cart').on('found_variation',function(e, variation_data ){

            var variation_table =  typeof  variation_data.ywcdd_variation_table !== 'undefined' ? variation_data.ywcdd_variation_table : false;
            if( variation_table) {
                $(document).find('#ywcdd_quantity_table_container').replaceWith(variation_table);
                var first_day = $(document).find('#ywcdd_quantity_table_wrap table th.ywcdd_day:not(.ywcdd_disable_all_day)').get(0);
                $(first_day).click();
            }

        }).on('reset_data',function(e){
            $(document).find('#ywcdd_quantity_table_container').replaceWith(original_table);
        });
    }
});