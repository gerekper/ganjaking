<?php

namespace ACA\WC\Column\ProductVariation;

use AC;
use ACA\WC\Search;
use ACP;

/**
 * @since 3.5.1
 */
class Taxonomy extends AC\Column
    implements ACP\Search\Searchable, ACP\ConditionalFormat\Formattable, ACP\Export\Exportable
{

    use ACP\ConditionalFormat\FilteredHtmlFormatTrait;

    public function __construct()
    {
        $this->set_type('variation_product_taxonomy')
             ->set_label(__('Product Taxonomy', 'codepress-admin-columns'))
             ->set_group('woocommerce');
    }

    public function get_taxonomy()
    {
        return $this->get_option('taxonomy');
    }

    public function get_value($id)
    {
        $_terms = $this->get_raw_value($id);

        if (empty($_terms)) {
            return $this->get_empty_char();
        }

        $terms = [];

        foreach ($_terms as $term) {
            $terms[] = ac_helper()->html->link(
                get_edit_term_link($term->term_id),
                $this->get_formatted_value($term->name, $term)
            );
        }

        return implode(', ', $terms);
    }

    public function get_raw_value($post_id)
    {
        $parent_id = get_post_field('post_parent', $post_id);
        $terms = get_the_terms($parent_id, $this->get_taxonomy());

        return ( ! $terms || is_wp_error($terms))
            ? false
            : $terms;
    }

    public function register_settings()
    {
        $this->add_setting(new AC\Settings\Column\Taxonomy($this, 'product'));
    }

    public function search()
    {
        return new Search\ProductVariation\ProductTaxonomy($this->get_taxonomy());
    }

    public function export()
    {
        return new ACP\Export\Model\StrippedValue($this);
    }
}