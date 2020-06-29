<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

$email_settings_url = site_url() . '/wp-admin/admin.php?page=wc-settings&tab=email';

$recipient_delivery_options = array(

    'recipient_delivery' => array(
        /**
         *
         * Recipient & Delivery settings for virtual Gift Cards
         *
         */
        array(
            'name' => __( 'Recipient & Delivery settings for virtual gift cards', 'yith-woocommerce-gift-cards' ),
            'type' => 'title',
        ),
        'ywgc_delivery_info_title'          => array(
            'name'    => __( 'Title for “Delivery info” section', 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'text',
            'id'      => 'ywgc_delivery_info_title',
            'desc'    => __( 'Enter a title for the delivery info area on your gift card page. This area will include recipient and sender\'s info, date of delivery and so on.', 'yith-woocommerce-gift-cards' ),
            'custom_attributes' => "placeholder='write the delivery info title'",
            'default' => __( "Delivery info", 'yith-woocommerce-gift-cards'),
        ),
//        'ywgc_delivery_options_1' => array(
//            'name'      => 'Delivery options',
//            'desc'      => __( 'Email', 'yith-woocommerce-gift-cards' ),
//            'type'      => 'checkbox',
//            'id'        => 'ywgc_delivery_options_1',
//            'default'   => 'yes',
//            'checkboxgroup' => 'start'
//        ),
//        'ywgc_delivery_options_2' => array(
//            'name'      => 'Delivery options',
//            'desc'      => __( 'Printable PDF', 'yith-woocommerce-gift-cards' ),
//            'type'      => 'checkbox',
//            'id'        => 'ywgc_delivery_options_2',
//            'default'   => 'yes',
//            'checkboxgroup' => ''
//        ),
//        'ywgc_delivery_options_3' => array(
//            'name'      => 'Delivery options',
//            'desc'      => __( 'SMS', 'yith-woocommerce-gift-cards' ),
//            'type'      => 'checkbox',
//            'id'        => 'ywgc_delivery_options_3',
//            'default'   => 'yes',
//            'checkboxgroup' => ''
//        ),
//        'ywgc_delivery_options_4' => array(
//            'name'      => 'Delivery options',
//            'desc'      => __( 'Whatsapp & Telegram', 'yith-woocommerce-gift-cards' ),
//            'type'      => 'checkbox',
//            'id'        => 'ywgc_delivery_options_4',
//            'default'   => 'yes',
//            'checkboxgroup' => 'end'
//        ),

        'ywgc_enable_send_later'      => array(
            'name'    => __( 'Allow the user to choose the delivery date', 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'onoff',
            'id'      => 'ywgc_enable_send_later',
            'desc'    => __( 'Allow your customers to choose a delivery date for the virtual gift card (option not available for physical gift cards delivered at home).', 'yith-woocommerce-gift-cards' ),
            'default' => 'no',
        ),
        'ywgc_delivery_hour'      => array(
            'name'    => __( 'Choose a default delivery time for gift cards', 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'text',
            'id'      => 'ywgc_delivery_hour',
            'desc'    => __( 'Select the time when the gift card will be sent. It is a 24h format, where the minimum time is 00:00 and the maximum is 24:00.', 'yith-woocommerce-gift-cards' ),
            'custom_attributes' => "placeholder='00:00'",
            'default' => '00:00',
            'deps'      => array(
                'id'    => 'ywgc_enable_send_later',
                'value' => 'yes',
            )
        ),
        'ywgc_recipient_info_title'          => array(
            'name'    => __( 'Title for the “Recipient info” section', 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'text',
            'id'      => 'ywgc_recipient_info_title',
            'desc'    => __( 'Enter a title for the section with the recipient\'s info.', 'yith-woocommerce-gift-cards' ),
            'custom_attributes' => "placeholder='write the recipient info title'",
            'default' => __( 'RECIPIENT\'S INFO', 'yith-woocommerce-gift-cards'),
        ),
        'ywgc_recipient_mandatory'          => array(
            'name'    => __( 'Make recipient\'s info mandatory', 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'onoff',
            'id'      => 'ywgc_recipient_mandatory',
            'desc'    => __( 'If enabled, the recipient\'s name and email fields will be mandatory.', 'yith-woocommerce-gift-cards' ),
            'default' => 'yes'
        ),
        'ywgc_allow_multi_recipients' => array(
            'name'    => __( 'Enable multiple recipients for virtual gift cards', 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'onoff',
            'id'      => 'ywgc_allow_multi_recipients',
            'desc'    => __( 'If enabled, customers can set multiple recipients: one gift card for each of them will be generated.', 'yith-woocommerce-gift-cards' ),
            'default' => 'no',
        ),
        'ywgc_ask_sender_name'          => array(
            'name'    => __( 'Ask sender\'s name', 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'onoff',
            'id'      => 'ywgc_ask_sender_name',
            'default' => 'yes'
        ),
        'ywgc_sender_info_title'          => array(
            'name'    => __( 'Title for “Sender\'s info” section', 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'text',
            'id'      => 'ywgc_sender_info_title',
            'desc'    => __( 'Enter a title for the section with sender\'s info.', 'yith-woocommerce-gift-cards' ),
            'custom_attributes' => "placeholder='write the sender info title'",
            'default'    => __( "YOUR INFO", 'yith-woocommerce-gift-cards'),
            'deps'      => array(
                'id'    => 'ywgc_ask_sender_name',
                'value' => 'yes',
            )
        ),
//        'ywgc_ask_sender_name_mandatory'          => array(
//            'name'    => __( '', 'yith-woocommerce-gift-cards' ),
//            'type'    => 'yith-field',
//            'yith-type' => 'checkbox',
//            'id'      => 'ywgc_ask_sender_name_mandatory',
//            'desc'    => __( 'Make it mandatory', 'yith-woocommerce-gift-cards' ),
//            'default' => 'no'
//        ),
        'ywgc_sender_message_placeholder'          => array(
            'name'    => __( 'Placeholder for Message textarea', 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'text',
            'id'      => 'ywgc_sender_message_placeholder',
            'desc'    => __( 'Enter a placeholder for the sender\'s message field.', 'yith-woocommerce-gift-cards' ),
            'custom_attributes' => "placeholder='write a placeholder for the sender message field'",
            'default'    => __( 'ENTER A MESSAGE FOR THE RECIPIENT', 'yith-woocommerce-gift-cards' ),
        ),
//        'ywgc_permit_modification'          => array(
//            'name'    => __( 'Enable the gift card edit in the cart', 'yith-woocommerce-gift-cards' ),
//            'type'    => 'yith-field',
//            'yith-type' => 'onoff',
//            'id'      => 'ywgc_permit_modification',
//            'default' => 'no'
//        ),


        array(
            'type' => 'sectionend',
        ),


        /**
         *
         * Recipient & Delivery settings for physical Gift Cards delivered at home
         *
         */
        array(
            'name' => __( 'Recipient & Delivery settings for physical Gift Cards delivered at home', 'yith-woocommerce-gift-cards' ),
            'type' => 'title',
        ),
        'ywgc_allow_printed_message'          => array(
            'name'    => __( 'Allow customers to add a printed message to the gift card', 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'onoff',
            'id'      => 'ywgc_allow_printed_message',
            'default' => 'no'
        ),
        'ywgc_ask_sender_name_physical'          => array(
            'name'    => __( 'Ask sender\'s and recipient\'s name', 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'onoff',
            'id'      => 'ywgc_ask_sender_name_physical',
            'default' => 'no'
        ),
        array(
            'type' => 'sectionend',
        ),

        /**
         *
         * E-mail options & customization
         *
         */

        array(
            'name' => __( 'Email options & customization', 'yith-woocommerce-gift-cards' ),
            'type' => 'title',
            'desc' => __( 'You can manage and edit the YITH Gift Card emails in the ', 'yith-woocommerce-gift-cards' ) . '<a href="' . $email_settings_url . '" >' . __( 'WooCommerce emails settings', 'yith-woocommerce-gift-cards' ) . '</a> ',
        ),
        'ywgc_auto_discount_button_activation'          => array(
            'name'    => __( 'Show a button in the gift card email', 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'onoff',
            'id'      => 'ywgc_auto_discount_button_activation',
            'desc'    => __( 'If enabled, the gift card dispatch email will contain a link to redirect your user to your site in one click.', 'yith-woocommerce-gift-cards' ),
            'default' => 'yes',
        ),
        'ywgc_email_button_label'           => array(
            'id'      => 'ywgc_email_button_label',
            'name'    => __( 'Button label', 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'text',
            'default' => __( 'Apply your gift card code', 'yith-woocommerce-gift-cards' ),
            'deps'      => array(
                'id'    => 'ywgc_auto_discount_button_activation',
                'value' => 'yes',
            )
        ),
        'ywgc_redirected_page' => array(
            'name'     => __( 'Button redirect to', 'yith-woocommerce-gift-cards' ),
            'id'       => 'ywgc_redirected_page',
            'type'     => 'single_select_page',
            'default'  => 'home_page',
            'class'    => 'chosen_select_nostd',
            'css'      => 'min-width:300px;',
            'desc'    => __( 'Select the page where the recipient will be redirected after clicking on the discount button.', 'yith-woocommerce-gift-cards' ),
            'desc_tip' => false,
        ),
        'ywgc_auto_discount'          => array(
            'name'    => __( 'Auto-apply the Gift Card code', 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'onoff',
            'id'      => 'ywgc_auto_discount',
            'desc'    => __( 'If enabled, the gift card code will be automatically applied when the user clicks on the button.', 'yith-woocommerce-gift-cards' ),
            'default' => 'yes',
            'deps'      => array(
                'id'    => 'ywgc_auto_discount_button_activation',
                'value' => 'yes',
            )
        ),
        'ywgc_display_description_template'        => array(
            'name'    => __( 'Enter a custom text in the email template', 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'onoff',
            'id'      => 'ywgc_display_description_template',
            'default' => 'no',
            'desc'    => __( 'This text will be displayed in the gift card email with the instructions about how to redeem the gift card. Leave this field empty if you do not want to display any message.', 'yith-woocommerce-gift-cards' ),
        ),
        'ywgc_description_template_email_text'        => array(
            'name'    => __( 'Custom text in the email', 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'textarea',
            'id'      => 'ywgc_description_template_email_text',
            'default' => __( "To use this gift card, you can either enter the code in the gift card field on the cart page or click on the following link to automatically get the discount.", 'yith-woocommerce-gift-cards' ),
            'custom_attributes' => "placeholder='write a message with the instructions to show in the gift card email'",
            'deps'      => array(
                'id'    => 'ywgc_display_description_template',
                'value' => 'yes',
            )
        ),
        'ywgc_description_template_text_pdf'        => array(
            'name'    => __( 'Custom text in the PDF', 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'textarea',
            'id'      => 'ywgc_description_template_text_pdf',
            'default' => __( "You can automatically apply the gift card code by simply reading the QR code with your phone.", 'yith-woocommerce-gift-cards' ),
            'custom_attributes' => "placeholder='write a message with the instructions to show in the gift card PDF'",
            'deps'      => array(
                'id'    => 'ywgc_display_description_template',
                'value' => 'yes',
            )
        ),
        'ywgc_display_expiration_date'        => array(
            'name'    => __( 'Show the gift card’s expiration date', 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'onoff',
            'id'      => 'ywgc_display_expiration_date',
            'default' => 'no',
            'desc'    => __( 'If enabled, the gift card expiration date will show up in the gift card template, if available.', 'yith-woocommerce-gift-cards' ),
        ),
        'ywgc_display_qr_code'        => array(
            'name'    => __( 'Show QR code', 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'onoff',
            'id'      => 'ywgc_display_qr_code',
            'default' => 'no',
            'desc'    => __( 'If enabled, the gift card template will show a QR code with the gift card code, so if customers read the code with their phone, they will be redirected to the Shop page and the gift card will be automatically applied to the cart.', 'yith-woocommerce-gift-cards' ),
        ),
        'ywgc_attach_pdf_to_gift_card_code_email'        => array(
            'name'    => __( 'Attach PDF', 'yith-woocommerce-gift-cards' ),
            'type'    => 'yith-field',
            'yith-type' => 'onoff',
            'id'      => 'ywgc_attach_pdf_to_gift_card_code_email',
            'default' => 'no',
            'desc'    => __( 'If enabled, a PDF with the gift card will be attached to the WooCommerce order processing or order completed emails.', 'yith-woocommerce-gift-cards' ),
        ),


        array(
            'type' => 'sectionend',
        ),

        
    ),
);

return apply_filters( 'yith_ywgc_recipient_delivery_options_array', $recipient_delivery_options );
