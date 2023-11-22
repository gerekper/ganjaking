<?php

namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Includes\Skins;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class WooWishlist extends \DynamicContentForElementor\Widgets\DynamicPostsBase
{
    public function get_name()
    {
        return 'dce-woo-wishlist';
    }
    protected function register_skins()
    {
        $this->add_skin(new Skins\Show_Woo_Wishlist_Skin_Grid($this));
        $this->add_skin(new Skins\Show_Woo_Wishlist_Skin_Grid_Filters($this));
        $this->add_skin(new Skins\Show_Woo_Wishlist_Skin_Carousel($this));
        $this->add_skin(new Skins\Show_Woo_Wishlist_Skin_DualCarousel($this));
        $this->add_skin(new Skins\Show_Woo_Wishlist_Skin_Accordion($this));
        $this->add_skin(new Skins\Show_Woo_Wishlist_Skin_List($this));
        $this->add_skin(new Skins\Show_Woo_Wishlist_Skin_Table($this));
        $this->add_skin(new Skins\Show_Woo_Wishlist_Skin_Timeline($this));
        $this->add_skin(new Skins\Show_Woo_Wishlist_Skin_3D($this));
        $this->add_skin(new Skins\Show_Woo_Wishlist_Skin_Gridtofullscreen3d($this));
        $this->add_skin(new Skins\Show_Woo_Wishlist_Skin_CrossroadsSlideshow($this));
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        parent::safe_register_controls();
        $this->update_control('list_items', ['default' => [['item_id' => 'item_image'], ['item_id' => 'item_title'], ['item_id' => 'item_productprice'], ['item_id' => 'item_addtocart']]]);
        $this->update_control('query_type', ['type' => Controls_Manager::HIDDEN, 'default' => 'favorites']);
        $this->update_control('favorites_scope', ['type' => Controls_Manager::HIDDEN, 'default' => 'user']);
        $this->update_control('favorites_key', ['type' => Controls_Manager::HIDDEN, 'default' => 'dce_wishlist']);
        $this->update_control('fallback_text', ['default' => __('No products on the wishlist.', 'dynamic-content-for-elementor')]);
    }
}
