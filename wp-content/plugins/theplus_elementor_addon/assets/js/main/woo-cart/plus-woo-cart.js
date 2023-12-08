/*woo cart*/if (jQuery('.tp-cart-page-wrapper.layout-2').length > 0) {
	jQuery(".tp-cart-page-wrapper .woocommerce .woocommerce-cart-form .shop_table tr td .quantity").append('<span class="tp-quantity-arrow quantity-arrow-minus qty-nav decrease">-</span>');
	jQuery(".tp-cart-page-wrapper .woocommerce .woocommerce-cart-form .shop_table tr td .quantity").prepend('<span class="tp-quantity-arrow quantity-arrow-plus qty-nav increase">+</span>');
	
	jQuery(function tp_woo_cart_qty() {
		if (jQuery(this)[0]) {
			jQuery(this).on("click", (".tp-cart-page-wrapper .woocommerce .woocommerce-cart-form .shop_table .quantity .qty-nav"), function() {
				let qty = jQuery(this).parents('.quantity').find('input.qty');
				let val = parseInt(qty.val());
				if (jQuery(this).hasClass('increase')) {
					qty.val(val + 1);
				} else {
					if (jQuery(this).closest('.woocommerce-grouped-product-list')[0]) {
						if (val > 0) {
							qty.val(val - 1);
						}
					} else {
						if (val > 1) {
							qty.val(val - 1);
						}
					}
				}
				qty.trigger('change');
			});
		}
	});
}