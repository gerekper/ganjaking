<style>
    .yith-wcms-pro-myaccount table.shop_table ins,
    .yith-wcms-pro-myaccount .order-info mark {
        background-color: <?php echo get_option( 'yith_wcms_highlight_color' ) ?>;
    }

    .yith-wcms-pro-myaccount .woocommerce table.shop_table thead th {
        background-color: <?php echo get_option( 'yith_wcms_table_header_backgroundcolor' ) ?>;
        color: <?php echo get_option( 'yith_wcms_table_header_color' ) ?>;
    }

    .yith-wcms-pro-myaccount .woocommerce table.shop_table tbody tr:nth-child(2n) {
        background-color: <?php echo get_option( 'yith_wcms_table_row_backgroundcolor' ) ?>;
    }

    .yith-wcms-pro-myaccount .woocommerce .col2-set address,
    .yith-wcms-pro-myaccount .woocommerce-page .col2-set address,
    .yith-wcms-pro-myaccount .woocommerce table.shop_table.customer_details tbody tr th,
    .yith-wcms-pro-myaccount .woocommerce table.shop_table.customer_details tbody tr td {
        color: <?php echo get_option( 'yith_wcms_table_details_color' ) ?>;
    }

    .yith-wcms-pro-myaccount .order_details.yith-order-info {
        background-color: <?php echo get_option( 'yith_wcms_details_background_color' ) ?>;
    }
</style>