jQuery( document ).ready( function( $ ){
    var export_list = $( '#yith_wcmc_export_list' ),
        export_user_set = $( '#yith_wcmc_export_user_set' ),
        export_table = export_user_set.parents( 'table'),
        export_users = $( '#yith_wcmc_export_users' ),
        export_products_filter = $( '#yith_wcmc_export_filter_product' ),
        export_category_filter = $( '#yith_wcmc_export_filter_category' ),
        export_tag_filter = $( '#yith_wcmc_export_filter_tag' ),
        export_date_filter = $( '#yith_wcmc_export_filter_date_from'),
        export_field_waiting_products = $( '#yith_wcmc_export_field_waiting_products'),
        csv_user_set = $( '#yith_wcmc_csv_user_set' ),
        csv_users = $( '#yith_wcmc_csv_users' ),
        csv_products_filter = $( '#yith_wcmc_csv_filter_product' ),
        csv_category_filter = $( '#yith_wcmc_csv_filter_category' ),
        csv_tag_filter = $( '#yith_wcmc_csv_filter_tag' ),
        csv_date_filter = $( '#yith_wcmc_csv_filter_date_from'),
        csv_table = csv_date_filter.parents( 'table');

    export_table.after( '<input type="submit" class="button button-primary visible" value="' + yith_wcmc_export_panel.labels.export_users + '" name="export_users">' );
    csv_table.after( '<input type="submit" class="button button-primary visible" value="' + yith_wcmc_export_panel.labels.download_csv + '" name="export_csv">' );

    export_user_set.on( 'change', function(){
        var t = $(this),
            val = t.val();

        if( val == 'all' || val == 'customers' ){
            export_users.parents( 'tr').hide();
            export_products_filter.parents( 'tr').hide();
            export_category_filter.parents( 'tr').hide();
            export_tag_filter.parents( 'tr').hide();
            export_date_filter.parents( 'tr').hide();
            export_field_waiting_products.parents( 'tr').hide();
        }
        else if( val == 'set' ){
            export_users.parents( 'tr').show();
            export_products_filter.parents( 'tr').hide();
            export_category_filter.parents( 'tr').hide();
            export_tag_filter.parents( 'tr').hide();
            export_date_filter.parents( 'tr').hide();
            export_field_waiting_products.parents( 'tr').hide();
        }
        else if( val == 'filter' ){
            export_users.parents( 'tr').hide();
            export_products_filter.parents( 'tr').show();
            export_category_filter.parents( 'tr').show();
            export_tag_filter.parents( 'tr').show();
            export_date_filter.parents( 'tr').show();
            export_field_waiting_products.parents( 'tr').hide();
        }
        else if( val == 'waiting_lists' ){
            export_users.parents( 'tr').hide();
            export_products_filter.parents( 'tr').hide();
            export_category_filter.parents( 'tr').hide();
            export_tag_filter.parents( 'tr').hide();
            export_date_filter.parents( 'tr').hide();
            export_field_waiting_products.parents( 'tr').show();
            export_list.change();
        }

    }).change();

    csv_user_set.on( 'change', function(){
        var t = $(this),
            val = t.val();

        if( val == 'all' || val == 'customers' || val == 'waiting_lists' ){
            csv_users.parents( 'tr').hide();
            csv_products_filter.parents( 'tr').hide();
            csv_category_filter.parents( 'tr').hide();
            csv_tag_filter.parents( 'tr').hide();
            csv_date_filter.parents( 'tr').hide();
        }
        else if( val == 'set' ){
            csv_users.parents( 'tr').show();
            csv_products_filter.parents( 'tr').hide();
            csv_category_filter.parents( 'tr').hide();
            csv_tag_filter.parents( 'tr').hide();
            csv_date_filter.parents( 'tr').hide();
        }
        else if( val == 'filter' ){
            csv_users.parents( 'tr').hide();
            csv_products_filter.parents( 'tr').show();
            csv_category_filter.parents( 'tr').show();
            csv_tag_filter.parents( 'tr').show();
            csv_date_filter.parents( 'tr').show();
        }

    }).change();
} );