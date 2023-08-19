<?php

namespace ACA\WC\PostType;

use AC\Asset\Location\Absolute;
use AC\Registerable;
use ACA\WC\ListTable;

class ProductVariation implements Registerable
{

    public const POST_TYPE = 'product_variation';

    /**
     * @var Absolute
     */
    private $location;

    public function __construct(Absolute $location)
    {
        $this->location = $location;
    }

    public function register(): void
    {
        // Menu
        add_filter('woocommerce_register_post_type_' . self::POST_TYPE, [$this, 'enable_variation_list_table'], 10, 2);
        add_action('admin_menu', [$this, 'admin_menu']);

        // Load correct list table classes for current screen.
        add_action('current_screen', [$this, 'setup_screen']);
        add_action('check_ajax_referer', [$this, 'setup_screen']);

        // add post edit variation link
        add_filter('get_edit_post_link', [$this, 'product_variation_edit_link'], 10, 2);
    }

    public function product_variation_edit_link($link, $product_id)
    {
        $variation_post = get_post($product_id);

        if ($variation_post->post_type === self::POST_TYPE) {
            $product_post = get_post($variation_post->post_parent);

            if ('product' === $product_post->post_type) {
                $link = sprintf('%s#variation_%d', get_edit_post_link($product_post), $variation_post->ID);
            }
        }

        return $link;
    }

    /**
     * @param array $args
     *
     * @return array
     */
    public function enable_variation_list_table($args)
    {
        $args['show_ui'] = true;
        $args['show_in_menu'] = true;

        if ( ! isset($args['capabilities'])) {
            $args['capabilities'] = [];
        }

        $args['capabilities']['create_posts'] = 'do_not_allow';
        $args['capabilities']['delete_posts'] = true;

        if ( ! isset($args['labels'])) {
            $args['labels'] = [
                'name'               => __('Product Variations', 'codepress-admin-columns'),
                'singular_name'      => __('Product Variation', 'codepress-admin-columns'),
                'all_items'          => __('Product Variations', 'codepress-admin-columns'),
                'add_new'            => __('Add New', 'codepress-admin-columns'),
                'add_new_item'       => __('Add new variation', 'codepress-admin-columns'),
                'edit'               => __('Edit', 'codepress-admin-columns'),
                'edit_item'          => __('Edit variation', 'codepress-admin-columns'),
                'new_item'           => __('New variation', 'codepress-admin-columns'),
                'view'               => __('View variation', 'codepress-admin-columns'),
                'view_item'          => __('View variation', 'codepress-admin-columns'),
                'search_items'       => __('Search variations', 'woocommerce'),
                'not_found'          => __('No variations found', 'codepress-admin-columns'),
                'not_found_in_trash' => __('No variations found in trash', 'codepress-admin-columns'),
                'insert_into_item'   => __('Insert into variation', 'codepress-admin-columns'),
            ];
        }

        return $args;
    }

    /**
     * Place variations menu under products
     */
    public function admin_menu()
    {
        global $submenu;

        $slug_variation = 'edit.php?post_type=' . self::POST_TYPE;

        if ( ! isset($submenu[$slug_variation])) {
            return;
        }

        $variation = reset($submenu[$slug_variation]);

        $slug_product = 'edit.php?post_type=product';

        if ( ! isset($submenu[$slug_product])) {
            return;
        }

        // Place on first available position after position x
        for ($pos = 10; $pos < 100; $pos++) {
            if (isset($submenu[$slug_product][$pos])) {
                continue;
            }

            $submenu[$slug_product][$pos] = $variation;
            break;
        }

        ksort($submenu[$slug_product]);

        remove_menu_page($slug_variation);
    }

    /**
     * Load List Table
     */
    public function setup_screen()
    {
        if ('edit-' . self::POST_TYPE === $this->get_current_screen_id()) {
            new ListTable\ProductVariation($this->location);
        }
    }

    /**
     * @return false|string
     */
    private function get_current_screen_id()
    {
        $screen_id = false;

        if (function_exists('get_current_screen')) {
            $screen = get_current_screen();
            $screen_id = isset($screen, $screen->id) ? $screen->id : '';
        }

        if ( ! empty($_REQUEST['screen']) && wp_doing_ajax()) {
            $screen_id = wc_clean(wp_unslash($_REQUEST['screen']));
        }

        return $screen_id;
    }

}