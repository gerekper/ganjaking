<?php

namespace ACA\WC\Column\Product;

use AC;
use ACA\WC\Search;
use ACA\WC\Sorting;
use ACP;
use ACP\ConditionalFormat\FormattableConfig;
use ACP\ConditionalFormat\Formatter;

class Rating extends AC\Column\Meta
    implements ACP\Sorting\Sortable, ACP\Search\Searchable, ACP\ConditionalFormat\Formattable
{

    public function __construct()
    {
        $this->set_type('column-wc-product_rating')
             ->set_label(__('Average Rating'))
             ->set_group('woocommerce');
    }

    public function get_value($id)
    {
        $product = wc_get_product($id);
        $rating = $product->get_average_rating();

        if ( ! $rating) {
            return $this->get_empty_char();
        }

        $link = add_query_arg(['p' => $id, 'status' => 'approved'], get_admin_url(null, 'edit-comments.php'));
        $count = ac_helper()->html->link($link, $product->get_rating_count());
        $stars = ac_helper()->html->tooltip(ac_helper()->html->stars($rating, 5), $rating);

        return sprintf('%s (%s)', $stars, $count);
    }

    public function get_raw_value($post_id)
    {
        return wc_get_product($post_id)->get_average_rating();
    }

    public function get_meta_key()
    {
        return '_wc_average_rating';
    }

    public function sorting()
    {
        return new ACP\Sorting\Model\Post\Meta($this->get_meta_key());
    }

    public function search()
    {
        return new Search\Product\Rating();
    }

    public function conditional_format(): ?FormattableConfig
    {
        return new FormattableConfig(new Formatter\RawValueFormatter(Formatter::FLOAT));
    }

}