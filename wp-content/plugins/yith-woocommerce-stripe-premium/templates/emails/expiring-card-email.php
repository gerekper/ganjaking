<?php
/**
 * Expiring card reminder template
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Stripe
 * @version 1.0.0
 */

/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'YITH_WCSTRIPE' ) ) {
	exit;
} // Exit if accessed directly
?>

<?php do_action( 'woocommerce_email_header', $email_heading ); ?>

<p>
    <?php echo sprintf( __( 'Hi %s,', 'yith-woocommerce-stripe' ), $username ) ?>
</p>
<p>
	<?php
		echo sprintf(
			$already_expired ?
                __( "This is a friendly reminder that your <b>%s</b> card ending in <b>%s</b> expired on <b>%s</b>. To continue your purchases, please update your card information with %s.", 'yith-woocommerce-stripe' ) :
                __( "This is a friendly reminder that your <b>%s</b> card ending in <b>%s</b> expires on <b>%s</b>. To continue your purchases, please update your card information with %s.", 'yith-woocommerce-stripe' ),
			$card_type,
			$last4,
			$expiration_date,
			$site_title
		)
	?>
</p>

<p style="text-align: center;">
    <a class="button alt" href="<?php echo $update_card_url ?>" style="color: <?php echo $update_card_fg ?> !important; font-weight: normal; text-decoration: none !important; display: inline-block; background: <?php echo $update_card_bg ?>; border-radius: 5px; padding: 10px 20px; white-space: nowrap; margin-top: 20px; margin-bottom: 30px;"><?php _e( 'Update now', 'yith-woocommerce-stripe' ) ?></a>
</p>

<?php do_action( 'woocommerce_email_footer' ); ?>
