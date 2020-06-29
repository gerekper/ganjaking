<?php
/**
 * Commission paid successfully email
 *
 * @author WooThemes
 * @package WooCommerce/Templates/Emails/HTML
 * @version 2.0.0
 *
 * @var string $email_heading
 * @var YITH_Commission $commission
 * @var bool $sent_to_admin
 * @var bool $plain_text
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
$email_content = __( 'The product {product_name} has been edited by vendor {vendor}. Please <a href="{post_link}" target="_blank">click here</a> to take a look the changes', 'yith-woocommerce-product-vendors' );
?>

<?php do_action( 'woocommerce_email_header', $email_heading, $yith_wc_email ); ?>
    <p>
        <?php echo apply_filters( "yith_wcmv_email_{$yith_wc_email->id}_content", $email_content, $yith_wc_email ) ?>
    </p>
<?php do_action( 'woocommerce_email_footer', $yith_wc_email ); ?>