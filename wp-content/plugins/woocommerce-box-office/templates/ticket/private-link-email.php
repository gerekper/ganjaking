<?php
/**
 * Private link email template
 *
 * @package woocommerce-box-office
 */

echo esc_html_x( 'Hi there!', 'private-link-email', 'woocommerce-box-office' ); ?>

<?php
/* translators: private link title */
echo sprintf( esc_html_x( "Here's the link to view %s:", 'private-link-email', 'woocommerce-box-office' ), esc_html( $private_title ) );
?>

<?php
echo esc_url( $private_link );
