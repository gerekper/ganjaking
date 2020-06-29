<?php
/**
 * Admin View: Notice - Worldpay Online is here!
 */
defined( 'ABSPATH' ) || exit;
?>
<div id="message" class="updated woocommerce-message">
	<a class="woocommerce-message-close notice-dismiss" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'wpol-hide-notice', 'wpol_here' ), 'wpol_hide_notices_nonce', '_wpol_notice_nonce' ) ); ?>"><?php esc_html_e( 'Dismiss', 'woocommerce_worlday' ); ?></a>

	<p>
	<?php
		echo wp_kses_post( sprintf(
			/* translators: %s: documentation URL */
			__( '<strong>The NEW service from Worldpay is here!</strong>
				<br /><br />This new and dynamic Worldpay plugin enables you to take payments quickly and securely from your own website or app, without a customer’s card details ever hitting your servers. <a href="%s">Learn more and sign up for special pricing here.</a>
				<br /><br />Worldpay will be migrating all merchants to Worldpay Online during 2019, more information will be available from Worldpay soon', 'woocommerce_worlday' ),
			'https://woocommerce.com/products/worldpay-online-payments/'
		) );
	?>
	</p>
</div>