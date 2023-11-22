<?php

namespace DynamicContentForElementor\Widgets;

use Elementor\Icons_Manager;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly
class AddToWooWishlist extends \DynamicContentForElementor\Widgets\AddToFavorites
{
    public function get_name()
    {
        return 'dce-dynamic-woo-wishlist';
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        parent::safe_register_controls();
        $this->update_control('dce_favorite_scope', ['type' => Controls_Manager::HIDDEN, 'default' => 'user']);
        $this->update_control('dce_favorite_counter', ['type' => Controls_Manager::HIDDEN, 'default' => '']);
        $this->update_control('dce_favorite_title_add', ['default' => __('Add to my Wishlist', 'dynamic-content-for-elementor')]);
        $this->update_control('dce_favorite_title_remove', ['default' => __('Remove from my Wishlist', 'dynamic-content-for-elementor')]);
        $this->update_control('dce_favorite_remove', ['type' => Controls_Manager::HIDDEN, 'default' => 'yes']);
        $this->update_control('dce_favorite_key', ['type' => Controls_Manager::HIDDEN, 'default' => 'dce_wishlist']);
        $this->update_control('dce_favorite_visitor_hide', ['type' => Controls_Manager::HIDDEN, 'default' => 'yes']);
        $this->update_control('dce_favorite_msg_add', ['default' => __('Product added to your wishlist', 'dynamic-content-for-elementor')]);
        $this->update_control('dce_favorite_msg_remove', ['default' => __('Product removed from your wishlist', 'dynamic-content-for-elementor')]);
    }
}
