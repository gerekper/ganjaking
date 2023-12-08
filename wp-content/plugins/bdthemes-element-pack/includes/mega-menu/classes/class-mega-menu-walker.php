<?php

namespace ElementPack\Includes\MegaMenu;

use Elementor\Icons_Manager;
use Elementor\Plugin;
use Walker_Nav_Menu;

defined('ABSPATH') || exit;

class Mega_Menu_Walker extends Walker_Nav_Menu {
    /**
     * @var mixed
     */
    public $menu_Settings;

    /**
     * @param $menu_item_id
     */
    public function get_item_meta($menu_item_id) {

        $meta_key = Mega_Menu_Init::$menu_item_settings_key;
        $data     = get_post_meta($menu_item_id, $meta_key, true);
        $data     = (array)json_decode($data);

        $default = [
            "menu_id"               => null,
            "menu_enable"           => 0,
            'menu_width_type'       => 'default',
            'menu_badge_label'      => '',
            'megamenu_badge_color'  => '#f0f0f0',
            'megamenu_icon_color'   => '#f0f0f0',
            'megamenu_icon_library' => 'fa-solid',
            'menu_badge_bgcolor'    => '#ddd',
            'megamenu_icon'         => '',
            'menu_custom_width'     => '750px',
        ];

        return array_merge($default, $data);
    }

    /**
     * @param $menu_slug
     */
    public function is_megamenu($menu_slug) {
        $menu_slug = (((gettype($menu_slug) == 'object') && (isset($menu_slug->slug))) ? $menu_slug->slug : $menu_slug);

        $cache_key = '_ep_megamenu_builder_data_' . $menu_slug;
        $cached    = wp_cache_get($cache_key);

        if (false !== $cached) {
            return $cached;
        }

        $return = 0;

        $settings = $this->get_option(Mega_Menu_Init::$megamenu_settings_key, []);
        $term     = get_term_by('slug', $menu_slug, 'nav_menu');

        if (isset($term->term_id) && isset($settings['menu_location_' . $term->term_id]) && $settings['menu_location_' . $term->term_id]['ep_megamenu_enabled'] == '1') {
            $return = 1;
        }

        wp_cache_set($cache_key, $return);

        return $return;
    }

    public function get_option($key, $default = '') {
        $data_all = get_option(Mega_Menu_Init::$megamenu_options_key);
        return (isset($data_all[$key]) && $data_all[$key] != '') ? $data_all[$key] : $default;
    }

    /**
     * @param $item_meta
     * @param $menu
     */
    public function is_megamenu_item($item_meta, $menu) {

        if ($this->is_megamenu($menu) == 1 && $item_meta['menu_enable'] == 1 && class_exists('Elementor\Plugin')) {
            return true;
        }

        return false;
    }

    /**
     * Starts the list before the elements are added.
     */
    public function start_lvl(&$output, $depth = 0, $args = []) {
        $indent = str_repeat("\t", $depth);

        if ($depth == 0) {
            $output .= "\n$indent<ul class=\"ep-megamenu-panel ep-default-submenu-panel ep-parent-element bdt-drop\">\n";
        } else {
            $output .= "\n$indent<ul class=\"ep-megamenu-panel ep-default-submenu-panel bdt-drop\">\n";
        }
    }

    /**
     * Ends the list of after the elements are added.
     */
    public function end_lvl(&$output, $depth = 0, $args = []) {
        $indent = str_repeat("\t", $depth);
        $output .= "$indent</ul>\n";
    }

    /**
     * Start the element output.
     */
    public function start_el(&$output, $item, $depth = 0, $args = [], $id = 0) {
        $indent    = ($depth) ? str_repeat("\t", $depth) : '';
        $classes   = empty($item->classes) ? [] : (array)$item->classes;
        $classes[] = 'menu-item-' . $item->ID;
        /**
         * Filter the CSS class(es) applied to a menu item's list item element.
         */
        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args, $depth));
        $class_names .= ' nav-item';
        $item_meta   = $this->get_item_meta($item->ID);

        $is_megamenu_item = $this->is_megamenu_item($item_meta, $args->menu);

        if ($is_megamenu_item == true) {
            $class_names .= ' ep-has-megamenu';
        }

        if (in_array('current-menu-item', $classes)) {
            $class_names .= ' active';
        }

        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

        /**
         * Filter the ID applied to a menu item's list item element.
         */
        $id        = apply_filters('nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth);
        $id        = $id ? ' id="' . esc_attr($id) . '"' : '';
        $data_attr = '';

        if ($is_megamenu_item == true) {

            if ($depth === 0) {

                if ($this->is_megamenu($args->menu) == 1) {
                    $item_meta = $this->get_item_meta($item->ID);

                    switch ($item_meta['menu_width_type']) {
                        case 'full_width':
                            $data_attr .= 'data-width-type="full"';
                            break;
                        case 'custom_width':
                            $custom_menu_width    = isset($item_meta['menu_custom_width']) ? esc_attr($item_meta['menu_custom_width']) : '750px';
                            $custom_menu_position = isset($item_meta['custom_menu_position']) ? esc_attr($item_meta['custom_menu_position']) : 'bottom-left';
                            $data_attr            .= 'data-width-type="custom"';
                            $data_attr            .= 'data-content-width="' . $custom_menu_width . '"';
                            $data_attr            .= 'data-content-pos="' . $custom_menu_position . '"';
                            break;
                        default:
                            $data_attr .= 'data-width-type="default"';
                            break;
                    }
                }
            }
        }

        $output         .= $indent . '<li' . $id . $class_names . $data_attr . '>';
        $atts           = [];
        $atts['class']  = '';
        $atts['title']  = !empty($item->attr_title) ? $item->attr_title : '';
        $atts['target'] = !empty($item->target) ? $item->target : '';
        $atts['rel']    = !empty($item->xfn) ? $item->xfn : '';
        $atts['href']   = !empty($item->url) ? $item->url : '';

        $submenu_indicator = '';

        if ($depth === 0) {
            $atts['class'] .= 'ep-menu-nav-link';
        }

        if (in_array('current-menu-item', $item->classes)) {
            $atts['class'] .= ' active';
        }

        /**
         * Filter the HTML attributes applied to a menu item's anchor element.
         */

        $atts       = apply_filters('nav_menu_link_attributes', $atts, $item, $args, $depth);
        $attributes = '';

        foreach ($atts as $attr => $value) {

            if (!empty($value)) {
                $value      = ('href' === $attr) ? esc_url($value) : esc_attr($value);
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }

        $item_output = $args->before;

        $item_output .= '<a' . $attributes . '>';

        if ($this->is_megamenu($args->menu) == 1) {

            // add menu icon
            if ($item_meta['megamenu_icon'] != '') {
                if (!Plugin::$instance->experiments->is_feature_active('e_font_icon_svg')) {
                    wp_enqueue_style('fontawesome', ELEMENTOR_ASSETS_URL . 'lib/font-awesome/css/all.min.css', false, '5.15.3');
                    $icon_style = 'color:' . $item_meta['megamenu_icon_color'];
                } else {
                    $icon_style = 'fill:' . $item_meta['megamenu_icon_color'];
                }
                ob_start();
                Icons_Manager::render_icon(['value' => $item_meta['megamenu_icon'], 'library' => $item_meta['megamenu_icon_library']], ['aria-hidden' => 'true', 'style' => esc_attr($icon_style)]);
                $item_output .= ob_get_clean();
            }
        }
        if ($this->is_megamenu($args->menu) == 1) {

            // add badge text
            if ($item_meta['menu_badge_label'] != '') {
                $badge_style = '';
                if (($item_meta['megamenu_badge_color'] != '') || ($item_meta['menu_badge_bgcolor'] != '')) {
                    $badge_style = 'style="color:' . $item_meta['megamenu_badge_color'] . '; background:' . $item_meta['menu_badge_bgcolor'] . ';"';
                }
                $item_output .= '<span class="ep-badge-label" ' . $badge_style . ' >' . $item_meta['menu_badge_label'] . '</span>';
            }
            if ($item_meta['menu_enable'] == 1) {
                $submenu_indicator .= '<i class="bdt-megamenu-indicator ep-icon-arrow-down-3"></i>';
            }
        }


        if ($depth === 0 && in_array('menu-item-has-children', $classes)) {
            $submenu_indicator .= '<i class="bdt-megamenu-indicator ep-icon-arrow-down-3"></i>';
        }

        /**
         * This filter is documented in wp-includes/post-template.php
         */
        $item_output .= $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after;
        $item_output .= $submenu_indicator . '</a>';
        $item_output .= $args->after;

        /**
         * Filter a menu item's starting output.
         */
        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }

    /**
     * Ends the element output, if needed.
     */
    public function end_el(&$output, $item, $depth = 0, $args = []) {
        if ($depth === 0) {
            if ($this->is_megamenu($args->menu) == 1) {
                $item_meta = $this->get_item_meta($item->ID);

                if ($item_meta['menu_enable'] == 1) {
                    $builder_post_title = 'bdt-ep-megamenu-content-' . $item->ID;
                    $posts = get_posts(
                        array(
                            'post_type'              => 'ep_megamenu_content',
                            'title'                  => $builder_post_title,
                            'post_status'            => 'all',
                            'numberposts'            => 1,
                            'update_post_term_cache' => false,
                            'update_post_meta_cache' => false,
                            'orderby'                => 'post_date ID',
                            'order'                  => 'ASC',
                        )
                    );

                    if (!empty($posts)) {
                        $page_got_by_title = $posts[0];
                    } else {
                        $page_got_by_title = null;
                    }
                    $builder_post       = $page_got_by_title;
                    $output             .= '<ul class="ep-megamenu-panel bdt-drop">';
                    if ($builder_post != null) {
                        $elementor = Plugin::instance();
                        $output    .= $elementor->frontend->get_builder_content_for_display($builder_post->ID, true);
                    } else {
                        $output .= esc_html__('No content found', 'bdthemes-element-pack');
                    }
                    $output .= '</ul>';
                }
            }
            $output .= "</li>\n";
        }
    }
}
