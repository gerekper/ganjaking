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

echo "= " . $email_heading . " =\n\n";
echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";
?>

<?php
echo sprintf( __( 'Hi %s,', 'yith-woocommerce-stripe' ), $username )
?>

<?php
echo sprintf(
	$already_expired ?
		__( "This is a friendly reminder that your %s card ending in %s expired on %s. To continue your purchases, please update your card information with %s.\n\n", 'yith-woocommerce-stripe' ) :
		__( "This is a friendly reminder that your %s card ending in %s expires on %s. To continue your purchases, please update your card information with %s.\n\n", 'yith-woocommerce-stripe' ),
	$card_type,
	$last4,
	$expiration_date,
	$site_title
)
?>

<?php echo sprintf( __( 'Update now (%s)', 'yith-woocommerce-stripe' ), $update_card_url ) ?>

<?php
echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";
echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );
