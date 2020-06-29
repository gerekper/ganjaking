<?php
// Exit if accessed directly
!defined( 'YITH_POS' ) && exit();
$is_update    = isset( $_GET[ 'post' ] );
$html_buttons = '';
if ( $is_update ) {
    $html_buttons = '<button id="update_receipt" class="button-primary button-xl" >' . __( 'Update Receipt', 'yith-point-of-sale-for-woocommerce' ) . '</button>';
    $html_buttons .= sprintf( '<a href="%s" class="button-secondary button-xl" id="delete_receipt">%s</a>', get_delete_post_link( $_GET[ 'post' ] ), __( 'Move to trash', 'yith-point-of-sale-for-woocommerce' ) );
} else {
    $html_buttons = '<input type="submit" name="publish" id="save_receipt" class="button-primary button-xl" value="' . __( 'Save Receipt', 'yith-point-of-sale-for-woocommerce' ) . '">';
}
$args = array(
    'yith-pos-receipt'         => array(
        'label'    => __( 'Edit', 'yith-point-of-sale-for-woocommerce' ),
        'pages'    => YITH_POS_Post_Types::$receipt,
        'context'  => 'normal',
        'priority' => 'high',
        'class'    => yith_set_wrapper_class(),
        'tabs'     => array(
            'receipt_template' => array(
                'label'  => __( 'General', 'yith-point-of-sale-for-woocommerce' ),
                'fields' => array(
                    'general_toggle_open'         => array(
                        'type' => 'html',
                        'html' => sprintf( '<div class="yith-open-toggle" data-target="yith_pos_general_toggle_item"><h1>%s</h1></div>', __( 'General Settings', 'yith-point-of-sale-for-woocommerce' ) ),
                    ),
                    'name'                        => array(
                        'type'              => 'text',
                        'label'             => __( 'Receipt name', 'yith-point-of-sale-for-woocommerce' ) . '<span class="yith-pos-red">*</span>',
                        'desc'              => __( 'Enter a name to identify this receipt template', 'yith-point-of-sale-for-woocommerce' ),
                        'class'             => 'yith-required-field yith_pos_general_toggle_item',
                        'custom_attributes' => 'required data-message="' . __( 'The receipt name is required.', 'yith-point-of-sale-for-woocommerce' ) . '"',
                    ),
                    'header_toggle_open'          => array(
                        'type' => 'html',
                        'html' => sprintf( '<div class="yith-open-toggle" data-target="yith_pos_header_toggle_open_item"><h1>%s</h1></div>', __( 'Receipt Header', 'yith-point-of-sale-for-woocommerce' ) ),
                    ),
                    'logo'                        => array(
                        'type'  => 'upload',
                        'label' => __( 'Logo', 'yith-point-of-sale-for-woocommerce' ),
                        'class' => 'yith_pos_header_toggle_open_item',
                        'std'   => YITH_POS_ASSETS_URL . '/images/logo-receipt.png',
                        'desc'  => __( 'Upload your logo to customize the receipt. Supported image formats : gif, jpg, jpeg, png.', 'yith-point-of-sale-for-woocommerce' )
                    ),
                    'show_store_name'             => array(
                        'type'  => 'onoff',
                        'label' => __( 'Name', 'yith-point-of-sale-for-woocommerce' ),
                        'class' => 'yith_pos_header_toggle_open_item',
                        'desc'  => __( 'Show or hide the Store name in this receipt', 'yith-point-of-sale-for-woocommerce' ),
                        'std'   => 'yes',
                    ),
                    'show_vat'                    => array(
                        'class' => 'vat-container',
                        'type'  => 'onoff',
                        'label' => __( 'Show VAT', 'yith-point-of-sale-for-woocommerce' ),
                        'class' => 'yith_pos_header_toggle_open_item',
                        'desc'  => __( 'Show or hide the VAT number in this receipt', 'yith-point-of-sale-for-woocommerce' ),
                        'std'   => 'yes',
                    ),
                    'vat_label'                   => array(
                        'type'  => 'text',
                        'label' => __( 'VAT label', 'yith-point-of-sale-for-woocommerce' ) . '<span class="yith-pos-red">*</span>',
                        'desc'  => __( 'Enter the label for VAT field. Default: VAT', 'yith-point-of-sale-for-woocommerce' ),
                        'class' => 'yith_pos_header_toggle_open_item',
                        'std'   => __( 'VAT:', 'yith-point-of-sale-for-woocommerce' ),
                        'deps'  => array(
                            'id'    => '_show_vat',
                            'value' => 'yes',
                            'type'  => 'disable',
                        ),
                    ),
                    'show_address'                => array(
                        'type'  => 'onoff',
                        'label' => __( 'Address', 'yith-point-of-sale-for-woocommerce' ),
                        'desc'  => __( 'Show or hide the Address of the store in this receipt', 'yith-point-of-sale-for-woocommerce' ),
                        'class' => 'yith_pos_header_toggle_open_item',
                        'std'   => 'yes',
                    ),
                    'show_contact_info'           => array(
                        'type'  => 'onoff',
                        'label' => __( 'Contact Info', 'yith-point-of-sale-for-woocommerce' ),
                        'desc'  => __( 'Show or hide the Contact Info of the store in this receipt', 'yith-point-of-sale-for-woocommerce' ),
                        'class' => 'yith_pos_header_toggle_open_item no-bottom',
                        'std'   => 'yes',
                    ),
                    'show_phone'                  => array(
                        'type'        => 'checkbox',
                        'label'       => '',
                        'desc-inline' => __( 'Phone', 'yith-point-of-sale-for-woocommerce' ),
                        'class'       => 'yith_pos_header_toggle_open_item no-bottom',
                        'std'         => '1',
                        'deps'        => array(
                            'id'    => '_show_contact_info',
                            'value' => 'yes',
                            'type'  => 'disable',
                        ),
                    ),
                    'show_email'                  => array(
                        'type'        => 'checkbox',
                        'label'       => '',
                        'desc-inline' => __( 'E-mail', 'yith-point-of-sale-for-woocommerce' ),
                        'class'       => 'yith_pos_header_toggle_open_item no-bottom',
                        'std'         => '1',
                        'deps'        => array(
                            'id'    => '_show_contact_info',
                            'value' => 'yes',
                            'type'  => 'disable',
                        ),
                    ),
                    'show_fax'                    => array(
                        'type'        => 'checkbox',
                        'label'       => '',
                        'desc-inline' => __( 'Fax', 'yith-point-of-sale-for-woocommerce' ),
                        'class'       => 'yith_pos_header_toggle_open_item no-bottom',
                        'std'         => '1',
                        'deps'        => array(
                            'id'    => '_show_contact_info',
                            'value' => 'yes',
                            'type'  => 'disable',
                        ),
                    ),
                    'show_website'                => array(
                        'type'        => 'checkbox',
                        'label'       => '',
                        'class'       => 'yith_pos_header_toggle_open_item no-bottom',
                        'desc-inline' => __( 'Website', 'yith-point-of-sale-for-woocommerce' ),
                        'std'         => '1',
                        'deps'        => array(
                            'id'    => '_show_contact_info',
                            'value' => 'yes',
                            'type'  => 'disable',
                        ),
                    ),
                    'show_social_info'            => array(
                        'type'  => 'onoff',
                        'label' => __( 'Socials', 'yith-point-of-sale-for-woocommerce' ),
                        'desc'  => __( 'Show or hide the socials of the store in this receipt', 'yith-point-of-sale-for-woocommerce' ),
                        'class' => 'yith_pos_header_toggle_open_item',
                        'std'   => 'yes',
                    ),
                    'show_facebook'               => array(
                        'type'        => 'checkbox',
                        'label'       => '',
                        'desc-inline' => __( 'Facebook', 'yith-point-of-sale-for-woocommerce' ),
                        'class'       => 'yith_pos_header_toggle_open_item no-bottom',
                        'std'         => '1',
                        'deps'        => array(
                            'id'    => '_show_social_info',
                            'value' => 'yes',
                            'type'  => 'disable',
                        ),
                    ),
                    'show_twitter'                => array(
                        'type'        => 'checkbox',
                        'label'       => '',
                        'desc-inline' => __( 'Twitter', 'yith-point-of-sale-for-woocommerce' ),
                        'class'       => 'yith_pos_header_toggle_open_item no-bottom',
                        'std'         => '1',
                        'deps'        => array(
                            'id'    => '_show_social_info',
                            'value' => 'yes',
                            'type'  => 'disable',
                        ),
                    ),
                    'show_instagram'              => array(
                        'type'        => 'checkbox',
                        'label'       => '',
                        'desc-inline' => __( 'Instagram', 'yith-point-of-sale-for-woocommerce' ),
                        'class'       => 'yith_pos_header_toggle_open_item no-bottom',
                        'std'         => '1',
                        'deps'        => array(
                            'id'    => '_show_social_info',
                            'value' => 'yes',
                            'type'  => 'disable',
                        ),
                    ),
                    'show_youtube'                => array(
                        'type'        => 'checkbox',
                        'label'       => '',
                        'class'       => 'yith_pos_header_toggle_open_item no-bottom',
                        'desc-inline' => __( 'Youtube', 'yith-point-of-sale-for-woocommerce' ),
                        'std'         => '1',
                        'deps'        => array(
                            'id'    => '_show_social_info',
                            'value' => 'yes',
                            'type'  => 'disable',
                        ),
                    ),
                    'order_toggle_open'           => array(
                        'id'   => '_yith_order_toggle_open',
                        'type' => 'html',
                        'html' => sprintf( '<div class="yith-open-toggle" data-target="yith_pos_order_toggle_item"><h1>%s</h1></div>', __( 'Order Info', 'yith-point-of-sale-for-woocommerce' ) ),
                    ),
                    'show_order_date'             => array(
                        'type'  => 'onoff',
                        'label' => __( 'Show Order Date', 'yith-point-of-sale-for-woocommerce' ),
                        'desc'  => __( 'Show or hide the order date in this receipt', 'yith-point-of-sale-for-woocommerce' ),
                        'class' => 'yith_pos_order_toggle_item no-bottom',
                        'std'   => 'yes',
                    ),
                    'order_date_label'            => array(
                        'type'  => 'text',
                        'label' => __( 'Order Date label', 'yith-point-of-sale-for-woocommerce' ),
                        'desc'  => __( 'Enter the label for Order Date field', 'yith-point-of-sale-for-woocommerce' ),
                        'class' => 'yith_pos_order_toggle_item yith-required-field',
                        'std'   => __( 'Date:', 'yith-point-of-sale-for-woocommerce' ),
                        'deps'  => array(
                            'id'    => '_show_order_date',
                            'value' => 'yes',
                            'type'  => 'disable',
                        ),
                    ),
                    'show_order_number'           => array(
                        'type'  => 'onoff',
                        'label' => __( 'Show Order Number', 'yith-point-of-sale-for-woocommerce' ),
                        'desc'  => __( 'Show or hide the order number in this receipt', 'yith-point-of-sale-for-woocommerce' ),
                        'class' => 'yith_pos_order_toggle_item no-bottom order-number-container',
                        'std'   => 'yes',
                    ),
                    'order_number_label'          => array(
                        'type'  => 'text',
                        'label' => __( 'Order Number label', 'yith-point-of-sale-for-woocommerce' ),
                        'desc'  => __( 'Enter the label for Order Number field', 'yith-point-of-sale-for-woocommerce' ),
                        'class' => 'yith_pos_order_toggle_item yith-required-field',
                        'std'   => __( 'Order:', 'yith-point-of-sale-for-woocommerce' ),
                        'deps'  => array(
                            'id'    => '_show_order_number',
                            'value' => 'yes',
                            'type'  => 'disable',
                        ),
                    ),
                    'show_order_customer'         => array(
                        'type'  => 'onoff',
                        'label' => __( 'Show Customer Name', 'yith-point-of-sale-for-woocommerce' ),
                        'desc'  => __( 'Show or hide the customer name in this receipt (not available in guest register)', 'yith-point-of-sale-for-woocommerce' ),
                        'class' => 'yith_pos_order_toggle_item no-bottom',
                        'std'   => 'yes',
                    ),
                    'order_customer_label'        => array(
                        'type'  => 'text',
                        'label' => __( 'Customer label', 'yith-point-of-sale-for-woocommerce' ),
                        'desc'  => __( 'Enter the label for Customer field', 'yith-point-of-sale-for-woocommerce' ),
                        'class' => 'yith_pos_order_toggle_item yith-required-field',
                        'std'   => __( 'Customer:', 'yith-point-of-sale-for-woocommerce' ),
                        'deps'  => array(
                            'id'    => '_show_order_customer',
                            'value' => 'yes',
                            'type'  => 'disable',
                        ),
                    ),
                    'show_order_register'         => array(
                        'type'  => 'onoff',
                        'label' => __( 'Show Register Name', 'yith-point-of-sale-for-woocommerce' ),
                        'desc'  => __( 'Show or hide the cashiers name in this receipt (not available in guest register)', 'yith-point-of-sale-for-woocommerce' ),
                        'class' => 'yith_pos_order_toggle_item no-bottom',
                        'std'   => 'yes',
                    ),
                    'order_register_label'        => array(
                        'type'  => 'text',
                        'label' => __( 'Register label', 'yith-point-of-sale-for-woocommerce' ),
                        'desc'  => __( 'Enter the label for Register field', 'yith-point-of-sale-for-woocommerce' ),
                        'class' => 'yith_pos_order_toggle_item yith-required-field',
                        'std'   => __( 'Register:', 'yith-point-of-sale-for-woocommerce' ),
                        'deps'  => array(
                            'id'    => '_show_order_register',
                            'value' => 'yes',
                            'type'  => 'disable',
                        ),
                    ),
                    'show_cashier'                => array(
                        'type'  => 'onoff',
                        'label' => __( 'Show Cashier Name', 'yith-point-of-sale-for-woocommerce' ),
                        'desc'  => __( 'Show or hide the cashier name in this receipt (not available in guest register)', 'yith-point-of-sale-for-woocommerce' ),
                        'class' => 'yith_pos_order_toggle_item no-bottom',
                        'std'   => 'yes',
                    ),
                    'cashier_label'               => array(
                        'type'  => 'text',
                        'label' => __( 'Cashier label', 'yith-point-of-sale-for-woocommerce' ),
                        'desc'  => __( 'Enter the label for Cashier field', 'yith-point-of-sale-for-woocommerce' ),
                        'class' => 'yith_pos_order_toggle_item',
                        'std'   => __( 'Cashier:', 'yith-point-of-sale-for-woocommerce' ),
                        'deps'  => array(
                            'id'    => '_show_cashier',
                            'value' => 'yes',
                            'type'  => 'disable',
                        ),
                    ),
                    'yith_pos_footer_toggle_open' => array(
                        'id'   => '_yith_footer_toggle_open',
                        'type' => 'html',
                        'html' => sprintf( '<div class="yith-open-toggle" data-target="yith_pos_footer_toggle_item"><h1>%s</h1></div>', __( 'Receipt footer', 'yith-point-of-sale-for-woocommerce' ) ),
                    ),
                    'receipt_footer'              => array(
                        'type'  => 'textarea',
                        'label' => __( 'Footer text', 'yith-point-of-sale-for-woocommerce' ),
                        'desc'  => __( 'Enter optional text for footer area in receipt template', 'yith-point-of-sale-for-woocommerce' ),
                        'std'   => __( 'Thanks for your purchase', 'yith-point-of-sale-for-woocommerce' ),
                        'class' => 'yith_pos_footer_toggle_item',
                    ),
                    'yith_pos_receipts_save'      => array(
                        'label' => '',
                        'type'  => 'html',
                        'html'  => $html_buttons,
                    ),
                ),
            ),
        ),
    ),
    'yith-pos-receipt-preview' => array(
        'label'    => __( 'Receipt Preview', 'yith-point-of-sale-for-woocommerce' ),
        'pages'    => YITH_POS_Post_Types::$receipt,
        'context'  => 'normal',
        'priority' => 'default',
        'class'    => yith_set_wrapper_class(),
        'tabs'     => array(
            'preview' => array(
                'label'  => __( 'Print Preview', 'yith-point-of-sale-for-woocommerce' ),
                'fields' => array(
                    'receipt_preview_title'   => array(
                        'type' => 'title',
                        'desc' => '',
                    ),
                    'receipt_preview_content' => array(
                        'label'  => '',
                        'type'   => 'custom',
                        'action' => 'yith_pos_preview_receipt',
                    ),
                    'receipt_preview_print'   => array(
                        'label' => '',
                        'type'  => 'html',
                        'html'  => "<div style='text-align: center'><span id='print_receipt' class='button-primary'>" . __( 'Print Example', 'yith-point-of-sale-for-woocommerce' ) . "</span></div>",
                    )
                ),
            ),
        ),
    )
);

return $args;