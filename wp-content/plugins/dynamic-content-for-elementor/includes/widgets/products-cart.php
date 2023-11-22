<?php

namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Includes\Skins;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class WooProductsCart extends \DynamicContentForElementor\Widgets\DynamicPostsBase
{
    public function get_name()
    {
        return 'dce-woo-products-cart';
    }
    protected function register_skins()
    {
        $this->add_skin(new Skins\Woo_Products_Cart_Skin_Grid($this));
        $this->add_skin(new Skins\Woo_Products_Cart_Skin_Grid_Filters($this));
        $this->add_skin(new Skins\Woo_Products_Cart_Skin_Carousel($this));
        $this->add_skin(new Skins\Woo_Products_Cart_Skin_DualCarousel($this));
        $this->add_skin(new Skins\Woo_Products_Cart_Skin_Accordion($this));
        $this->add_skin(new Skins\Woo_Products_Cart_Skin_List($this));
        $this->add_skin(new Skins\Woo_Products_Cart_Skin_Table($this));
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        parent::safe_register_controls();
        $this->update_control('query_type', ['type' => Controls_Manager::HIDDEN, 'default' => 'products_cart']);
        $this->update_control('list_items', ['default' => [['item_id' => 'item_image'], ['item_id' => 'item_title'], ['item_id' => 'item_productprice']]]);
        $this->update_control('taxonomy', ['options' => ['' => __('All', 'dynamic-content-for-elementor')] + Helper::get_woocommerce_taxonomies(), 'default' => '']);
        $this->update_control('fallback_text', ['default' => __('The cart is empty.', 'dynamic-content-for-elementor')]);
        $this->update_control('post_status', ['label' => __('Product Status', 'dynamic-content-for-elementor')]);
        $this->update_control('post_offset', ['label' => __('Products Offset', 'dynamic-content-for-elementor'), 'description' => __('Warning: products offset doesn\'t support pagination', 'dynamic-content-for-elementor')]);
        $this->update_control('exclude_io', ['label' => __('Current Product', 'dynamic-content-for-elementor')]);
        $this->update_control('exclude_posts', ['label' => __('Specific Products', 'dynamic-content-for-elementor')]);
        $this->update_control('exclude_page_parent', ['type' => Controls_Manager::HIDDEN]);
        $this->update_control('terms_current_post', ['label' => __('Dynamic Current Product Terms', 'dynamic-content-for-elementor'), 'description' => __('Filter results by taxonomy terms associated to the current product', 'dynamic-content-for-elementor')]);
        $this->update_control('include_author', ['description' => __('Filter products by selected Authors', 'dynamic-content-for-elementor')]);
        $this->update_control('exclude_author', ['description' => __('Filter products by selected Authors', 'dynamic-content-for-elementor')]);
    }
}
