<?php

namespace ElementPack\Modules\WcMiniCart\WcTemplates\Cart;

defined('ABSPATH') || exit;


$cart_items = WC()->cart->get_cart();

if (empty($cart_items)) {
	$shop_page_url = get_permalink(wc_get_page_id('shop'));
?>
	<div class="wc-empty-mini-cart">
		<svg id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 96 96">
			<rect width="96" height="96" style="fill:none" />
			<path d="M36.29,40.52l5.27,5.35L53.34,34.26,65.13,45.87l5.27-5.35L58.69,29,70.4,17.48l-5.27-5.34L53.34,23.74,41.56,12.14l-5.27,5.34L48,29ZM78.51,72.45c-.22,0-.43,0-.64,0v-.16h-47L30.14,68H84.27L92,30.72l-22.6-3-1,7.44,14.61,2L78.17,60.54H28.81L23.19,29,37.26,31l1.1-7.42L21.8,21.17,18.5,2.61H2.33v7.5h9.89L23.34,72.63A10.41,10.41,0,1,0,35.66,82.86a10.27,10.27,0,0,0-.46-3H68.55a10.27,10.27,0,0,0-.46,3A10.42,10.42,0,1,0,78.51,72.45ZM25.24,85.78a2.92,2.92,0,1,1,2.92-2.92A2.92,2.92,0,0,1,25.24,85.78Zm53.27,0a2.92,2.92,0,1,1,2.91-2.92A2.92,2.92,0,0,1,78.51,85.78Z" />
		</svg>
		<p class="wc-empty-message"><?php echo esc_html_x('No products in the cart.', 'Frontend', 'bdthemes-element-pack'); ?></p>
		<a class="bdt-button bdt-button-primary bdt-button-small" href="<?php echo esc_url($shop_page_url); ?>"><?php echo esc_html_x('Return To Shop', 'Frontend', 'bdthemes-element-pack'); ?></a>
	</div>
<?php } else { ?>
	<div class="bdt-mini-cart-products woocommerce-mini-cart cart woocommerce-cart-form__contents">
		<?php
		do_action('woocommerce_before_mini_cart_contents');
		foreach ($cart_items as $cart_item_key => $cart_item) {
			element_pack_render_mini_cart_item($cart_item_key, $cart_item);
		}
		do_action('woocommerce_mini_cart_contents');
		?>
	</div>

	<div>
		<div class="bdt-mini-cart-subtotal bdt-flex bdt-flex-between">
			<div>
				<strong><?php echo __('Subtotal', 'bdthemes-element-pack'); ?>:</strong>
			</div>
			<div>
				<?php echo WC()->cart->get_cart_subtotal(); ?>
			</div>
		</div>
		<div class="bdt-mini-cart-footer-buttons">
			<a href="<?php echo esc_url(wc_get_cart_url()); ?>" class="bdt-button bdt-button-view-cart bdt-size-md">
				<span class="bdt-button-text"><?php echo __('View cart', 'bdthemes-element-pack'); ?></span>
			</a>
			<a href="<?php echo esc_url(wc_get_checkout_url()); ?>" class="bdt-button bdt-button-checkout bdt-size-md">
				<span class="bdt-button-text"><?php echo __('Checkout', 'bdthemes-element-pack'); ?></span>
			</a>
		</div>
	</div>
<?php
}
?>