(function($) {
	
    var WidgetCartSingleHandler = function($scope, $) {
			if ($scope.find('.tp-woo-single-pricing.layout-2 .tp-woo-add-to-cart').length > 0) {
				jQuery(".tp-woo-single-pricing .tp-woo-add-to-cart .cart .quantity ").append('<span class="tp-quantity-arrow quantity-arrow-minus qty-nav decrease">-</span>');
				jQuery(".tp-woo-single-pricing .tp-woo-add-to-cart .cart .quantity").prepend('<span class="tp-quantity-arrow quantity-arrow-plus qty-nav increase">+</span>');
				
				$(function tp_woo_cart_qty() {
					if ($(this)[0]) {
						$(this).on("click", (".tp-woo-single-pricing .tp-woo-add-to-cart .cart .quantity  .qty-nav"), function() {
							let qty = jQuery(this).parents('.quantity').find('input.qty');
							let val = parseInt(qty.val());
							if ($(this).hasClass('increase')) {
								qty.val(val + 1);
							} else {
								if ($(this).closest('.woocommerce-grouped-product-list')[0]) {
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
    };
    $(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/tp-woo-single-pricing.default', WidgetCartSingleHandler);
    });
})(jQuery);