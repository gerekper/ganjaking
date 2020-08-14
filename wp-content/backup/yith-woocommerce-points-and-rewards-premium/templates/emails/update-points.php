<?php
/**
 * HTML Template Email YITH WooCommerce Points and Rewards
 *
 * @package YITH WooCommerce Points and Rewards
 * @since   1.0.0
 * @version 1.0.0
 * @author  YITH
 *
 * @var string $email_heading
 * @var string $email_content
 * @var WC_Email $email
 */

// DO_ACTION : woocommerce_email_header : action to insert the WooCommerce email header.
do_action( 'woocommerce_email_header', $email_heading, $email );
?>
<?php echo wpautop( $email_content ); //phpcs:ignore ?>

<?php
// DO_ACTION : woocommerce_email_footer : action to insert the WooCommerce email footer.
do_action( 'woocommerce_email_footer', $email );
