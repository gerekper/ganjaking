<?php
use Ali2Woo\Aliexpress;
use Ali2Woo\Attachment;
use Ali2Woo\Helper;
use Ali2Woo\ImportAjaxController;
use Ali2Woo\Override;
use Ali2Woo\ProductChange;
use Ali2Woo\ProductImport;
use Ali2Woo\Review;
use Ali2Woo\Woocommerce;
use function DI\create;
use function DI\get;

return [
    /* models */
    'Ali2Woo\Attachment' => create(Attachment::class),
    'Ali2Woo\Helper' => create(Helper::class),
    'Ali2Woo\ProductChange' => create(ProductChange::class),
    'Ali2Woo\ProductImport' => create(ProductImport::class),
    'Ali2Woo\Woocommerce' => create(Woocommerce::class)
        ->constructor(
            get(Attachment::class), get(Helper::class), get(ProductChange::class)
        ),
    'Ali2Woo\Review' => create(Review::class),
    'Ali2Woo\Override' => create(Override::class),
    'Ali2Woo\Aliexpress' => create(Aliexpress::class),

    /* controllers */
    'Ali2Woo\ImportAjaxController' => create(ImportAjaxController::class)
        ->constructor(
            get(ProductImport::class), get(Woocommerce::class), get(Review::class),
            get(Override::class), get(Aliexpress::class)
        ),
];
