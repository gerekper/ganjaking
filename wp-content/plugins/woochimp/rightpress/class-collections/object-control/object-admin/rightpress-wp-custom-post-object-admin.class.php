<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-wp-post-object-admin.class.php';
require_once 'interfaces/rightpress-wp-custom-post-object-admin-interface.php';

/**
 * WordPress Post Object Admin
 *
 * @class RightPress_WP_Custom_Post_Object_Admin
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_WP_Custom_Post_Object_Admin extends RightPress_WP_Post_Object_Admin implements RightPress_WP_Custom_Post_Object_Admin_Interface
{

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {

        // Call parent constructor
        parent::__construct();

        // Set up admin menu
        // TODO: Isn't there a more elegant way?
        if (!defined(strtoupper($this->get_controller()->get_plugin_private_prefix() . 'ADMIN_MENU_SET_UP'))) {
            add_action('admin_head', array($this, 'set_up_admin_menu'));
            add_filter('menu_order', array($this, 'fix_admin_menu_order'), $this->get_menu_priority());
            define(strtoupper($this->get_controller()->get_plugin_private_prefix() . 'ADMIN_MENU_SET_UP'), true);
        }

        // Maybe remove date filter
        RightPress_Help::add_late_filter('months_dropdown_results', array($this, 'maybe_remove_date_filter'));
    }

    /**
     * Get post type
     *
     * @access public
     * @return string
     */
    public function get_post_type()
    {

        return $this->get_controller()->get_data_store()->get_post_type();
    }


    /**
     * =================================================================================================================
     * ADMIN MENU
     * =================================================================================================================
     */

    /**
     * Get menu items
     *
     * @access public
     * @return array
     */
    public function get_menu_items()
    {

        return apply_filters(($this->get_controller()->get_plugin_private_prefix() . 'menu_items'), array(
            'edit.php?post_type=' . $this->get_controller()->get_main_post_type(),
            // TODO: 'post-new.php?post_type=' . $this->get_controller()->get_main_post_type(),
        ));
    }

    /**
     * Set up admin menu
     *
     * Developers: this method removes all 3rd party menu links, please add them
     * later than action 'admin_head' position 10 so that they are preserved
     *
     * Note: This must work with foreign main_post_type as well, not just one of our post types
     *
     * @access public
     * @return void
     */
    public function set_up_admin_menu()
    {

        global $submenu;

        // Get menu items
        $menu_items = $this->get_menu_items();

        // Get parent item
        $parent = reset($menu_items);

        // Check if parent item can be found
        if (isset($submenu[$parent])) {

            $admin_menu = array();

            // Set all items that are present in our menu items array
            foreach ($menu_items as $submenu_key) {
                foreach ($submenu[$parent] as $item_key => $item) {
                    if ($item[2] === $submenu_key) {
                        $admin_menu[$item_key] = $submenu[$parent][$item_key];
                        break;
                    }
                }
            }

            $submenu[$parent] = $admin_menu;
        }
    }

    /**
     * Fix admin menu order
     *
     * Note: This must work with foreign main_post_type as well, not just one of our post types
     *
     * @access public
     * @param array $menu_order
     * @return array
     */
    public function fix_admin_menu_order($menu_order)
    {

        $anchor = null;

        // Find anchor
        foreach ($menu_order as $index => $item) {

            if ($item === 'woocommerce') {
                $anchor = $index;
            }
            else if ($item !== null && $item === 'edit.php?post_type=product') {
                $anchor = $index;
            }
        }

        // No anchor found
        if ($anchor === null) {
            return $menu_order;
        }

        // Define custom order
        $custom_order = array();

        // Format own item
        $own_item = 'edit.php?post_type=' . $this->get_controller()->get_main_post_type();

        // Iterate over menu items
        foreach ($menu_order as $index => $item) {

            // Add our item immediately after our anchor item
            if ($index === $anchor) {
                $custom_order[] = $item;
                $custom_order[] = $own_item;
            }
            // Add all other items except our own
            else if ($item !== $own_item) {
                $custom_order[] = $item;
            }
        }

        return $custom_order;
    }


    /**
     * =================================================================================================================
     * ADMIN OBJECT LIST
     * =================================================================================================================
     */

    /**
     * Maybe remove date filter
     *
     * @access public
     * @param array $months
     * @return array
     */
    public function maybe_remove_date_filter($months)
    {

        global $typenow;

        // Our post type
        if ($typenow === $this->get_post_type()) {

            // Object is not chronological
            if (!$this->get_controller()->is_chronologic()) {

                // Clear months array
                $months = array();
            }
        }

        return $months;
    }

    /**
     * Add filtering capabilities
     *
     * @access public
     * @return void
     */
    public function add_list_filters()
    {

        global $typenow;
        global $wp_query;

        // Ensure this call is for our post type
        if ($typenow !== $this->get_post_type()) {
            return;
        }

        // Iterate over taxonomies
        foreach ($this->get_controller()->get_taxonomies_with_terms() as $taxonomy => $taxonomy_data) {

            // Prefix taxonomy
            $prefixed_taxonomy = $this->get_controller()->prefix_taxonomy($taxonomy);

            // Get selected option
            $selected = !empty($wp_query->query[$prefixed_taxonomy]) ? $wp_query->query[$prefixed_taxonomy] : false;

            // Check if options should be grouped
            $is_grouped = count($taxonomy_data['grouped_terms']) > 1;

            // Open element
            $html = '<select name="' . $prefixed_taxonomy . '" id="' . $prefixed_taxonomy . '" class="postform">';

            // Add empty option
            $html .= '<optgroup label="' . $taxonomy_data['all'] . '"><option value="0" ' . (!$selected ? 'selected="selected"' : '') . '>' . $taxonomy_data['all'] . '</option></optgroup>';

            // Iterate over term groups
            foreach ($taxonomy_data['grouped_terms'] as $term_group => $term_group_data) {

                // Maybe open group
                if ($is_grouped) {
                    $html .= '<optgroup label="' . $term_group_data['label'] . '">';
                }

                // Iterate over terms
                foreach ($term_group_data['terms'] as $term => $term_data) {

                    // Append option
                    $html .= '<option value="' . $term . '" ' . ($term === $selected ? 'selected="selected"' : '') . '>' . $term_data['label'] . '</option>';
                }

                // Maybe close group
                if ($is_grouped) {
                    $html .= '</optgroup>';
                }
            }

            // Close element
            $html .= '</select>';

            // Print field
            echo $html;
        }
    }


    /**
     * =================================================================================================================
     * ADMIN OBJECT EDITING
     * =================================================================================================================
     */

    /**
     * Register meta boxes whitelist
     *
     * @access public
     * @return array
     */
    public function register_meta_boxes_whitelist()
    {

        return apply_filters($this->get_controller()->prefix_public_hook('meta_boxes_whitelist'), array());
    }





}
