<?php
/**
 * Notice - Sell credit
 *
 * @package WC_Store_Credit/Admin/Notices
 * @since   3.2.0
 */

defined( 'ABSPATH' ) || exit;

?>
<div id="message" class="updated woocommerce-message">
	<?php echo wp_kses_post( wc_store_credit_get_notice_dismiss_link( 'wc_store_credit_sell_credit' ) ); ?>
	<p>
		<?php
		printf(
			'<strong>%1$s</strong> &#8211; %2$s',
			esc_html__( 'WooCommerce Store Credit', 'woocommerce-store-credit' ),
			esc_html_x( 'Now you can sell Store Credit coupons to your customers for their own usage or as gift cards.', 'admin notice', 'woocommerce-store-credit' )
		);
		?>
	</p>
	<p class="submit">
		<a href="<?php echo esc_url( wc_store_credit_get_notice_dismiss_url( 'wc_store_credit_sell_credit', admin_url( 'post-new.php?post_type=product' ) ) ); ?>" class="button-primary">
			<?php esc_html_e( 'Add product', 'woocommerce-store-credit' ); ?>
		</a>

		<?php
		printf(
			'<a class="button-secondary docs" href="%1$s" aria-label="%2$s" target="_blank">%3$s</a>',
			esc_url( 'https://docs.woocommerce.com/document/woocommerce-store-credit/' ),
			esc_attr_x( 'View WooCommerce Store Credit documentation', 'aria-label: documentation link', 'woocommerce-store-credit' ),
			esc_html__( 'Read more', 'woocommerce-store-credit' )
		);
		?>
	</p>
</div>
