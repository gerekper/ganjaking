<?php
/**
 * Send the gift card code email
 *
 * @author  Yithemes
 * @package yith-woocommerce-gift-cards-premium\templates\emails
 */

if ( !defined ( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * @hooked YITH_WooCommerce_Gift_Cards_Premium::include_css_for_emails() Add CSS style to gift card emails header
 */

do_action ( 'woocommerce_email_header' , $email_heading , $email );

do_action ( 'ywgc_gift_cards_email_before_preview' , $introductory_text , $gift_card );

do_action ( 'ywgc_gift_cards_email_before_preview_gift_card_param' , $gift_card );

YITH_YWGC ()->preview_digital_gift_cards ( $gift_card, 'email', $case );

do_action ( 'ywgc_gift_card_email_after_preview' , $gift_card );

/**
 * @hooked YITH_WooCommerce_Gift_Cards_Premium::add_footer_information() Output the email footer
 */
do_action ( 'woocommerce_email_footer' , $email );