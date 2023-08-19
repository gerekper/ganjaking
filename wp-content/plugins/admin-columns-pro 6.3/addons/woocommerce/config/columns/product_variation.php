<?php

use ACA\WC\Column;

return [
    ACP\Column\Actions::class,
    ACP\Column\CustomField::class,
    ACP\Column\Post\AuthorName::class,
    ACP\Column\Post\DatePublished::class,
    ACP\Column\Post\ID::class,
    ACP\Column\Post\LastModifiedAuthor::class,
    ACP\Column\Post\Slug::class,
    ACP\Column\Post\Status::class,
    ACP\Column\Post\TitleRaw::class,

    Column\Product\ShippingClass::class,

    Column\ProductVariation\Attribute::class,
    Column\ProductVariation\Description::class,
    Column\ProductVariation\Dimensions::class,
    Column\ProductVariation\Downloadable::class,
    Column\ProductVariation\Enabled::class,
    Column\ProductVariation\ID::class,
    Column\ProductVariation\Image::class,
    Column\ProductVariation\Order::class,
    Column\ProductVariation\Price::class,
    Column\ProductVariation\Product::class,
    Column\ProductVariation\ShippingClass::class,
    Column\ProductVariation\SKU::class,
    Column\ProductVariation\Stock::class,
    Column\ProductVariation\TaxClass::class,
    Column\ProductVariation\Taxonomy::class,
    Column\ProductVariation\Thumb::class,
    Column\ProductVariation\Variation::class,
    Column\ProductVariation\Virtual::class,
    Column\ProductVariation\Weight::class,
    Column\Product\Sales::class,
];