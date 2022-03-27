<?php
/**
 * Gifting activation notice.
 *
 * @package WooCommerce Subscriptions Gifting/Templates/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<div id="message" class="updated woocommerce-message wc-connect woocommerce-subscriptions-activated">
	<div class="squeezer">
		<h4>
			<?php
			echo wp_kses(
				sprintf(
					/* translators: $1-$2: opening and closing <strong> tags, $3-$4: opening and closing <em> tags */
					__(
						'%1$sWooCommerce Subscriptions Gifting Installed%2$s &#8211; %3$sYour customers can now buy subscriptions for others!%4$s',
						'woocommerce-subscriptions-gifting'
					),
					'<strong>',
					'</strong>',
					'<em>',
					'</em>'
				),
				array(
					'strong' => true,
					'em'     => true,
				)
			);
			?>
		</h4>

		<p class="submit">
			<a href="<?php echo esc_url( $settings_tab_url ); ?>" class="button button-primary"><?php esc_html_e( 'Settings', 'woocommerce-subscriptions-gifting' ); ?></a>
			<a href="https://docs.woocommerce.com/document/subscriptions-gifting/" class="docs button"><?php esc_html_e( 'Documentation', 'woocommerce-subscriptions-gifting' ); ?></a>
			<a href="https://twitter.com/share" class="twitter-share-button" data-url="http://www.woocommerce.com/products/woocommerce-subscriptions/" data-text="Woot! Customers can now buy subscriptions for others with #WooCommerce" data-via="WooCommerce" data-size="large">Tweet</a>
			<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
		</p>
	</div>
</div>
