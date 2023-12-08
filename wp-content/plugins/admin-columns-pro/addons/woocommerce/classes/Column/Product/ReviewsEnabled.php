<?php

namespace ACA\WC\Column\Product;

use AC;
use ACA\WC\Editing;
use ACA\WC\Search;
use ACA\WC\Sorting;
use ACP;

/**
 * @since 1.0
 */
class ReviewsEnabled extends AC\Column
    implements ACP\Sorting\Sortable, ACP\Editing\Editable, ACP\Search\Searchable
{

    public function __construct()
    {
        $this->set_type('column-wc-reviews_enabled')
             ->set_label(__('Reviews Enabled', 'codepress-admin-columns'))
             ->set_group('woocommerce');
    }

    public function get_value($id)
    {
        return ac_helper()->icon->yes_or_no('open' === $this->get_raw_value($id));
    }

    public function get_raw_value($id)
    {
        return $this->get_comment_status($id);
    }

    public function editing()
    {
        return new Editing\Product\ReviewsEnabled();
    }

    public function sorting()
    {
        return new ACP\Sorting\Model\Post\PostField('comment_status');
    }

    public function search()
    {
        return new Search\Product\ReviewsEnabled();
    }

    public function is_valid()
    {
        return post_type_supports($this->get_post_type(), 'comments');
    }

    private function get_comment_status($id)
    {
        return ac_helper()->post->get_raw_field('comment_status', $id);
    }

}