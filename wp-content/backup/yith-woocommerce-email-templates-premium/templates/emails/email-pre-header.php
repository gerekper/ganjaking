<?php
/**
 * Email Pre-Header
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/email-header.php.
 *
 * @author     YITHEMES
 * @package    YITH WooCommerce Email Templates Premium
 * @since    1.2.7
 */

/**
 * @var string $pre_header
 */

if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
?>
<!--[if !gte mso 9]><!---->
<div class="pre-header" style="visibility:hidden !important; opacity: 0 !important; font-size:0 !important; color: transparent !important; height: 0 !important; width: 0 !important;"><?php echo $pre_header; ?></div>
<!--<![endif]-->
