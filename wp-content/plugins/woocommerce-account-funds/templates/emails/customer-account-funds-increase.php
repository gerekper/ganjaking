<?php
/**
 * Customer increase account funds email.
 *
 * @package WC_Account_Funds/Templates/Emails
 * @version 2.8.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Template vars.
 *
 * @var WC_Email $email              Email object.
 * @var string   $email_heading      Email heading.
 * @var string   $message            Email message.
 * @var float    $funds_amount       Funds amount.
 * @var string   $additional_content Additional content.
 */

/*
 * @hooked WC_Emails::email_header() Output the email header.
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); // phpcs:ignore WooCommerce.Commenting.CommentHooks ?>

<?php
/**
 * Show email message - this is set in the email's settings.
 */
echo wp_kses_post( wpautop( wptexturize( $message ) ) );
?>

<div class="wc-account-funds-wrapper text-center">
	<span class="wc-account-funds-amount"><?php echo wp_kses_post( wc_price( $funds_amount ) ); ?></span>
</div>

<?php
/**
 * Show user-defined additional content - this is set in each email's settings.
 */
if ( $additional_content ) :
	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
endif;

/*
 * @hooked WC_Emails::email_footer() Output the email footer.
 */
do_action( 'woocommerce_email_footer', $email ); // phpcs:ignore WooCommerce.Commenting.CommentHooks
