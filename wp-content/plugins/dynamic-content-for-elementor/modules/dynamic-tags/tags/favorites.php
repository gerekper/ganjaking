<?php

namespace DynamicContentForElementor\Modules\DynamicTags\Tags;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Controls_Manager;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class Favorites extends Tag
{
    public function get_name()
    {
        return 'dce-favorites';
    }
    public function get_title()
    {
        return __('Favorites', 'dynamic-content-for-elementor');
    }
    public function get_group()
    {
        return 'dce';
    }
    public function get_categories()
    {
        return ['base', 'text'];
    }
    protected function register_controls()
    {
        $this->add_control('favorites_scope', ['label' => __('Favorites from', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['cookie' => ['title' => __('Cookie', 'dynamic-content-for-elementor'), 'icon' => 'icon-dyn-cookie'], 'user' => ['title' => __('User', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-user'], 'global' => ['title' => __('Global', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-globe']], 'toggle' => \false, 'default' => 'user']);
        $this->add_control('favorites_key', ['label' => __('Favorites Key', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => 'my_favorites']);
        $this->add_control('favorites_separator', ['label' => __('Separator', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['new_line' => __('New Line', 'dynamic-content-for-elementor'), 'line_break' => __('Line Break', 'dynamic-content-for-elementor'), 'comma' => __('Comma', 'dynamic-content-for-elementor')], 'default' => 'line_break', 'multiple' => \true]);
        $this->add_control('favorites_link', ['label' => __('Link to Favorite', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['favorites_separator!' => 'new_line']]);
        $this->add_control('favorites_post_type', ['label' => __('Post Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT2, 'options' => Helper::get_public_post_types(), 'multiple' => \true, 'label_block' => \true]);
        $this->add_control('favorites_post_status', ['label' => __('Post Status', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT2, 'options' => get_post_statuses(), 'multiple' => \true, 'label_block' => \true, 'default' => ['publish']]);
        $this->add_control('favorites_orderby', ['label' => __('Order By', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => Helper::get_post_orderby_options(), 'default' => 'date']);
        $this->add_control('favorites_order', ['label' => __('Order', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['ASC' => __('Ascending', 'dynamic-content-for-elementor'), 'DESC' => __('Descending', 'dynamic-content-for-elementor')], 'default' => 'DESC']);
        $this->add_control('favorites_posts', ['label' => __('Results', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => '10']);
        $this->add_control('return_format', ['label' => __('Return Format', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['title' => __('Title', 'dynamic-content-for-elementor'), 'title_id' => __('Title | ID', 'dynamic-content-for-elementor'), 'id' => __('ID', 'dynamic-content-for-elementor')], 'default' => 'title']);
        $this->add_control('favorites_fallback', ['label' => __('Fallback Content', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => __('No favorites found', 'dynamic-content-for-elementor')]);
    }
    /**
     * Get Post By Format
     *
     * @return string|int|false
     */
    protected function get_post_by_format()
    {
        $return_format = $this->get_settings('return_format');
        switch ($return_format) {
            case 'title_id':
                return esc_html(get_the_title()) . '|' . get_the_ID();
            case 'id':
                return get_the_ID();
            default:
                return esc_html(get_the_title());
        }
    }
    public function render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        $favorites_post_in = [];
        if ('user' === $settings['favorites_scope']) {
            $favorites_post_in = get_user_meta(get_current_user_id(), $settings['favorites_key'], \true);
        } elseif ('cookie' === $settings['favorites_scope'] && isset($_COOKIE[$settings['favorites_key']])) {
            // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
            $favorites_post_in = \explode(',', $_COOKIE[$settings['favorites_key']]);
        } elseif ('global' === $settings['favorites_scope']) {
            $favorites_post_in = get_option($settings['favorites_key']);
        }
        if (!empty($favorites_post_in)) {
            if ('dce_wishlist' !== $settings['favorites_key']) {
                // Favorites
                $args = ['post_type' => \DynamicContentForElementor\Helper::validate_post_type($settings['favorites_post_type']), 'post__in' => $favorites_post_in, 'posts_per_page' => $settings['favorites_posts'], 'order' => $settings['favorites_order'], 'orderby' => $settings['favorites_orderby'], 'post_status' => $settings['favorites_post_status']];
            } else {
                // Woo Wishlist
                if (!is_user_logged_in()) {
                    return;
                }
                $wishlist = [];
                foreach ($favorites_post_in as $product) {
                    if ('product' === get_post_type($product) && !wc_customer_bought_product('', get_current_user_id(), get_the_ID())) {
                        $wishlist[] = $product;
                    }
                }
                $args = ['post_type' => 'product', 'post__in' => $wishlist, 'posts_per_page' => $settings['favorites_posts'], 'order' => $settings['favorites_order'], 'orderby' => $settings['favorites_orderby'], 'post_status' => $settings['favorites_post_status']];
            }
            $wp_query = new \WP_Query($args);
            if ($wp_query->have_posts()) {
                while ($wp_query->have_posts()) {
                    $wp_query->the_post();
                    if ('new_line' === $settings['favorites_separator'] || empty($settings['favorites_link'])) {
                        echo $this->get_post_by_format();
                    } else {
                        echo '<a href=' . get_the_permalink() . '>' . $this->get_post_by_format() . '</a>';
                    }
                    if ($wp_query->current_post + 1 !== $wp_query->post_count) {
                        echo self::separator($settings['favorites_separator']);
                    }
                }
                wp_reset_postdata();
            } else {
                self::render_fallback($settings['favorites_fallback']);
            }
        } else {
            self::render_fallback($settings['favorites_fallback']);
        }
    }
    public function render_fallback(string $fallback = '')
    {
        if (!$fallback) {
            return;
        }
        echo wp_kses_post($fallback);
    }
    public function separator(string $choice)
    {
        switch ($choice) {
            case 'line_break':
                return '<br />';
            case 'new_line':
                return "\n";
            case 'comma':
                return ', ';
        }
    }
}
