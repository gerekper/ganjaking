<?php

namespace DynamicContentForElementor\Modules\DynamicTags\Tags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Controls_Manager;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Modules\DynamicTags\Tags\Favorites;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class WooWishlist extends Favorites
{
    public function get_name()
    {
        return 'dce-wishlist';
    }
    public function get_title()
    {
        return __('Woo Wishlist', 'dynamic-content-for-elementor');
    }
    protected function register_controls()
    {
        parent::register_controls();
        $this->update_control('favorites_scope', ['type' => Controls_Manager::HIDDEN, 'default' => 'user']);
        $this->update_control('favorites_key', ['type' => Controls_Manager::HIDDEN, 'default' => 'dce_wishlist']);
        $this->update_control('favorites_link', ['label' => __('Link to product', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['favorites_separator!' => 'new_line']]);
        $this->update_control('favorites_post_type', ['type' => Controls_Manager::HIDDEN]);
        $this->update_control('favorites_fallback', ['default' => __('No products in the wishlist', 'dynamic-content-for-elementor')]);
    }
}
