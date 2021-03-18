<?php

/**
 * The template for displaying WooCommerce service view in wp-admin
 *
 * You can overwrite this template by copying it to yourtheme/ct-ultimate-gdpr/service folder
 *
 * @version 1.0
 *
 */

$label = CT_Ultimate_GDPR::instance()->get_admin_controller()->get_option_value( 'services_woocommerce_order_checkout_description', false, CT_Ultimate_GDPR_Controller_Services::ID );

echo esc_html( $label );
