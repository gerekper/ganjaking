<?php

namespace ACA\WC\Column\ProductVariation;

use ACA\WC;
use ACA\WC\Search;

class TaxClass extends WC\Column\Product\TaxClass
{

    public function get_value($post_id)
    {
        $value = $this->get_raw_value($post_id);

        $classes = $this->get_tax_classes();

        if (isset($classes[$value])) {
            $value = $classes[$value];
        }

        if ( ! $value) {
            return $this->get_empty_char();
        }

        $icon = '';

        if ('parent' === get_post_meta($post_id, $this->get_meta_key(), true)) {
            $icon = ac_helper()->html->tooltip(
                '<span class="woocommerce-help-tip"></span>',
                __('Tax Class managed by product', 'codepress-admin-columns')
            );
        }

        return sprintf('%s %s', $value, $icon);
    }

    public function get_raw_value($post_id)
    {
        return wc_get_product($post_id)->get_tax_class();
    }

    public function search()
    {
        return false;
    }

    public function sorting()
    {
        return null;
    }

    public function editing()
    {
        return new WC\Editing\ProductVariation\TaxClass($this->get_tax_classes());
    }

}