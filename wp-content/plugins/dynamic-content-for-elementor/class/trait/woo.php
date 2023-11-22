<?php

namespace DynamicContentForElementor;

trait Woo
{
    public function get_fields()
    {
        $fields = array();
        $fields['product'] = ['_price' => __('Price', 'dynamic-content-for-elementor'), '_sale_price' => __('Sale Price', 'dynamic-content-for-elementor'), '_regular_price' => __('Regular Price', 'dynamic-content-for-elementor'), '_average_rating' => __('Average Rating', 'dynamic-content-for-elementor'), '_stock_status' => __('Stock Status', 'dynamic-content-for-elementor'), '_on_sale' => __('On Sale', 'dynamic-content-for-elementor'), '_featured' => __('Featured', 'dynamic-content-for-elementor'), '_product_type' => __('Product Type', 'dynamic-content-for-elementor')];
        return $fields;
    }
}
