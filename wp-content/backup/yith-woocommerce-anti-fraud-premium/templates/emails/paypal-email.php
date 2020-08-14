<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

/**
 * Implements PayPal verify Mail for YWAF plugin (HTML)
 *
 * @class   YWAF_PayPal_Verify
 * @package Yithemes
 * @since   1.0.0
 * @author  Your Inspiration Themes
 */

$admin_email = get_option( 'admin_email' );
$find        = array(
    '{site_title}',
    '{site_email}',
);
$replace     = array(
    sprintf( '<a target="_blank" href="%s">%s</a>', get_option( 'siteurl' ), get_option( 'blogname' ) ),
    sprintf( '<a href="mailto:%s">%s</a>', $admin_email, $admin_email ),
);
$mail_body   = str_replace( $find, $replace, nl2br( $mail_body ) );
$verify_arg  = array( 'ywaf_pvk' => $verify_key );
$verify_link = add_query_arg( $verify_arg, wc_get_page_permalink( 'myaccount' ) );

?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<p>
    <?php echo $mail_body ?>
</p>
<p>
    <?php _e( 'In order to complete the verification process, click on the following link:', 'yith-woocommerce-anti-fraud' ) ?>
</p>
<h3>
    <a target="_blank" href="<?php echo $verify_link ?>"><?php _e( 'Verify PayPal email', 'yith-woocommerce-anti-fraud' ); ?> </a>
</h3>

<?php do_action( 'woocommerce_email_footer', $email ); ?>
