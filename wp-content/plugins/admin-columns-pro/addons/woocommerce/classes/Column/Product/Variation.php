<?php

namespace ACA\WC\Column\Product;

use AC;
use AC\View;
use ACA\WC\Export;
use ACA\WC\Sorting;
use ACP;
use WC_Product_Attribute;
use WC_Product_Variable;
use WC_Product_Variation;

class Variation extends AC\Column
    implements AC\Column\AjaxValue, ACP\Sorting\Sortable, ACP\Export\Exportable, ACP\ConditionalFormat\Formattable
{

    use ACP\ConditionalFormat\FilteredHtmlFormatTrait;

    public function __construct()
    {
        $this->set_type('column-wc-variation')
             ->set_label(__('Variations', 'woocommerce'))
             ->set_group('woocommerce');
    }

    public function get_value($id)
    {
        $id = (int)$id;

        $count = count($this->get_variations($id));

        if ($count < 1) {
            return $this->get_empty_char();
        }

        return ac_helper()->html->get_ajax_modal_link(
            sprintf(_n('%d variation', '%d variations', $count, 'codepress-admin-columns'), $count),
            [
                'title'     => strip_tags(get_the_title($id)) ?: $id,
                'edit_link' => get_edit_post_link($id),
                'id'        => $id,
                'class'     => '-w-large -nopadding',
            ]
        );
    }

    public function get_raw_value($post_id)
    {
        return $this->get_variations($post_id);
    }

    private function get_variation_items(int $id): array
    {
        $items = [];

        foreach ($this->get_variations($id) as $variation) {
            $name = $variation->get_name();
            $edit = get_edit_post_link($id);

            if ($edit) {
                $name = sprintf('<a target="_blank" href="%s#variation_%d">%s</a>', $edit, $variation->get_id(), $name);
            }

            $items[] = [
                'name'       => $name,
                'sku'        => $variation->get_sku(),
                'attributes' => implode(
                    '&nbsp;&nbsp;-&nbsp;&nbsp;',
                    $this->get_attributes($id, $variation->get_attributes())
                ),
                'stock'      => $this->get_stock($variation),
            ];
        }

        return $items;
    }

    private function get_attributes(int $product_id, array $attributes): array
    {
        $labels = [];

        $attribute_objects = wc_get_product($product_id)->get_attributes();

        foreach ($attributes as $name => $value) {
            $attribute = $attribute_objects[$name] ?? null;

            if ( ! $attribute instanceof WC_Product_Attribute) {
                continue;
            }

            $label = $attribute->get_name();

            if ($attribute->is_taxonomy()) {
                $term = get_term_by('slug', $value, $attribute->get_taxonomy());
                $label = $attribute->get_taxonomy_object()->attribute_label;

                if ($term) {
                    $value = $term->name;
                }
            }

            $labels[] = sprintf('<strong>%s</strong>: %s', $label, $value);
        }

        return $labels;
    }

    public function get_ajax_value($id): string
    {
        $id = (int)$id;

        $view = new View([
            'items' => $this->get_variation_items($id),
        ]);

        return $view->set_template('modal-value/variations')
                    ->render();
    }

    private function get_stock(WC_Product_Variation $variation): string
    {
        if ( ! $variation->managing_stock()) {
            return sprintf('<mark class="instock">%s</mark>', __('In stock', 'woocommerce'));
        }

        $qty = $variation->get_stock_quantity();

        if ( ! $variation->is_in_stock() || $qty < 1) {
            return sprintf('<mark class="outofstock">%s</mark>', __('Out of stock', 'woocommerce'));
        }

        return sprintf('<mark class="instock">%s</mark> (%d)', __('In stock', 'woocommerce'), $qty);
    }

    private function get_variation_ids(int $product_id): array
    {
        $product = wc_get_product($product_id);

        if ( ! $product instanceof WC_Product_Variable) {
            return [];
        }

        return $product->get_children();
    }

    /**
     * @param int $product_id
     *
     * @return WC_Product_Variation[]
     */
    private function get_variations(int $product_id): array
    {
        $variations = [];

        foreach ($this->get_variation_ids($product_id) as $variation_id) {
            $variation = wc_get_product($variation_id);

            if ($variation instanceof WC_Product_Variation && $variation->exists()) {
                $variations[] = $variation;
            }
        }

        return $variations;
    }

    public function sorting()
    {
        return new Sorting\Product\Variation();
    }

    public function export()
    {
        return new Export\Product\Variation($this);
    }

}