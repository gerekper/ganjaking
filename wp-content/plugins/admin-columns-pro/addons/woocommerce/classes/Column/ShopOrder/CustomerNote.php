<?php

namespace ACA\WC\Column\ShopOrder;

use ACA\WC\Export;
use ACA\WC\Search;
use ACA\WC\Settings;
use ACA\WC\Sorting;
use ACP;
use ACP\Editing;

class CustomerNote extends ACP\Column\Post\Excerpt
{

    public function __construct()
    {
        parent::__construct();

        $this->set_type('column-wc-order_customer_note')
             ->set_label(__('Customer Note', 'codepress-admin-columns'))
             ->set_group('woocommerce');
    }

    public function get_raw_value($id)
    {
        return ac_helper()->post->get_raw_field('post_excerpt', $id);
    }

    public function register_settings()
    {
        $this->add_setting(new Settings\Product\UseIcon($this));
    }

    public function sorting()
    {
        return new ACP\Sorting\Model\Post\PostField('post_excerpt');
    }

    public function search()
    {
        return new Search\ShopOrder\CustomerMessage();
    }

    public function export()
    {
        return new Export\ShopOrder\CustomerMessage();
    }

    public function editing()
    {
        return new Editing\Service\Basic(
            (new Editing\View\TextArea())->set_placeholder(__('Customer notes about the order', 'woocommerce')),
            new Editing\Storage\Post\Field('post_excerpt')
        );
    }

}