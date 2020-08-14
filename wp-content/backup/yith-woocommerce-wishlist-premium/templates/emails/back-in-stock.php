<?php
/**
 * Customer "back in stock" email
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
 * @version 3.0.0
 */

/**
 * Template variables:
 *
 * @var $email_heading string Email heading string
 * @var $email \WC_Email Email object
 * @var $email_content string Email content (HTML)
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<?php do_action('woocommerce_email_header', $email_heading, $email ); ?>

	<p><?php echo $email_content ?></p>

<?php do_action( 'woocommerce_email_footer', $email ); ?>