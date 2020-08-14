<?php
/**
 * HTML Template Email Pending Order Survey
 *
 * @package YITH WooCommerce Pending Order Survey
 * @since   1.0.0
 * @author  Yithemes
 */

extract($args);


    do_action( 'woocommerce_email_header', $email_heading );

?>

<?php echo $email_content ?>

<?php
    do_action( 'woocommerce_email_footer' );

?>
