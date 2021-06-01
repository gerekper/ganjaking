<?php
/**
 * Styles for the Store Credit emails.
 *
 * @package WC_Store_Credit/Templates/Emails
 * @version 3.7.0
 */

defined( 'ABSPATH' ) || exit;

// Load colors.
$base      = get_option( 'woocommerce_email_base_color' );
$base_text = wc_light_or_dark( $base, '#202020', '#ffffff' );
?>
.text-center {
	text-align: center;
}

.store-credit-wrapper {
	margin: 40px 0;
}

.store-credit-code {
	display: inline-block;
	font-size: 28px;
	font-weight: bold;
	line-height: 1.2;
}

.store-credit-cta-button {
	display: inline-block;
	padding: 15px 20px;
	font-size: 18px;
	text-decoration: none;
	background-color: <?php echo esc_attr( $base ); ?>;
	color: <?php echo esc_attr( $base_text ); ?>;
}
<?php
