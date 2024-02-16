jQuery(window).on("elementor/frontend/init", function () {
    var wooCrossSells = function ($scope, $) {
        if ($scope.find('.eael-cs-products-container.style-1').length) {
            $(document).ajaxComplete(function (event, xhr, settings) {
                if (settings.url === '/?wc-ajax=add_to_cart') {
                    let add_to_cart_btn = $scope.find('.ajax_add_to_cart.added');

                    if (add_to_cart_btn.length) {
                        add_to_cart_btn.each(function () {
                            if ($(this).next().length < 1) {
                                $(this).closest('.eael-cs-purchasable').removeClass('eael-cs-purchasable');
                            }
                        });
                    }
                }
            });
        } else if ($scope.find('.eael-cs-products-container.style-2.eael-custom-image-area').length) {
            let productInfoHeight = 0,
                wrapperHeight = 0;

            $('.eael-cs-product-info', $scope).each(function () {
                let localHeight = parseInt($(this).css('height'));
                productInfoHeight = productInfoHeight < localHeight ? localHeight : productInfoHeight;
            });

            $('.eael-cs-single-product', $scope).each(function () {
                let localHeight = parseInt($(this).css('height'));
                wrapperHeight = wrapperHeight < localHeight ? localHeight : wrapperHeight;
            });

            $('.eael-cs-products-container.style-2 .eael-cs-product-image', $scope).css('max-height', `calc(100% - ${productInfoHeight}px)`);
            $('.eael-cs-products-container.style-2 .eael-cs-single-product', $scope).css('height', `${wrapperHeight}px`);
        }
    };

    if (ea.elementStatusCheck('eaelWooCrossSells')) {
        return false;
    }

    elementorFrontend.hooks.addAction("frontend/element_ready/eael-woo-cross-sells.default", wooCrossSells);
});