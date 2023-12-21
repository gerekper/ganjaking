<?php
/**
 * HANav Menu Walker Class
 */
namespace Happy_Addons\Elementor\Extension;


class HANav_Menu_Walker extends \Walker_Nav_Menu {

    public function start_lvl(&$output, $depth = 0, $args = array()) {
        $indent = str_repeat("\t", $depth);
        $output .= "\n$indent<ul class=\"sub-menu\">\n";
        // if ($depth == 0) {
        // } else if ($depth == 1) {
        //     $output .= "\n$indent<ul>\n";
        // } else if ($depth == 2) {
        //     $output .= "\n$indent<ul class=\"mega-menu-2\">\n";
        // }
    }

    public function end_lvl(&$output, $depth = 0, $args = array()) {
        $indent = str_repeat("\t", $depth);
        $output .= "$indent</ul>\n";
    }

    public function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
        $indent = ($depth) ? str_repeat("\t", $depth) : '';
        $classes = empty($item->classes) ? array() : (array) $item->classes;
        // $classes[] = 'menu-item-' . $item->ID;
        /**
         * Filter the CSS class(es) applied to a menu item's list item element.
         *
         * @since 3.0.0
         * @since 4.1.0 The `$depth` parameter was added.
         *
         * @param array  $classes The CSS classes that are applied to the menu item's `<li>` element.
         * @param object $item    The current menu item.
         * @param array  $args    An array of {@see wp_nav_menu()} arguments.
         * @param int    $depth   Depth of menu item. Used for padding.
         */
        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args, $depth));
        // New

        $mega_menu_li_class = '';

        // if ($depth == 0) {
        //     $mega_menu_li_class = " mega-menu-wrap";
        // } else if ($depth == 1) {
        //     // $mega_menu_li_class = " mega-menu-wrap-1";
        // } else if ($depth == 2) {
        //     // $mega_menu_li_class = " mega-menu-wrap-2";
        // }

        if (in_array('menu-item-has-children', $classes)) {
            $class_names .= $mega_menu_li_class;
        }
        if (in_array('current-menu-item', $classes)) {
            $class_names .= ' active';
        }
        $submenu_indicator = '';
        if (in_array('menu-item-has-children', $classes)) {
			$submenu_indicator .= '<span class="ha-submenu-indicator-wrap fas fa-angle-down"></span>';
		}

        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

        /**
         * Filter the ID applied to a menu item's list item element.
         *
         * @since 3.0.1
         * @since 4.1.0 The `$depth` parameter was added.
         *
         * @param string $menu_id The ID that is applied to the menu item's `<li>` element.
         * @param object $item    The current menu item.
         * @param array  $args    An array of {@see wp_nav_menu()} arguments.
         * @param int    $depth   Depth of menu item. Used for padding.
         */
        $id = apply_filters('nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth);
        $id = $id ? ' id="' . esc_attr($id) . '"' : '';
        // New
        // if ($depth === 0) {
        //     $output .= $indent . '<li' . $id . $class_names .'>';
        // }
        //
        $output .= $indent . '<li' . $id . $class_names . '>';
        $atts = array();
        $atts['title']  = !empty($item->attr_title) ? $item->attr_title : '';
        $atts['target'] = !empty($item->target)     ? $item->target     : '';
        $atts['rel']    = !empty($item->xfn)        ? $item->xfn        : '';
        $atts['href']   = !empty($item->url)        ? $item->url        : '';
        // New
        if ($depth === 0) {
            // $atts['class'] = 'nav-link';
        }
        if ($depth === 0 && in_array('menu-item-has-children', $classes)) {
            // $atts['class']       .= ' dropdown-toggle';
            // $atts['data-toggle']  = 'dropdown';
        }
        if ($depth > 0) {
            // $manual_class = array_values($classes)[0] . ' ' . 'dropdown-item';
            // $atts['class'] = $manual_class;
        }
        if (is_array($item->classes) && in_array('current-menu-item', $item->classes) && array_key_exists('class', $atts)) {
            $atts['class'] .= ' active';
        }

        //
        /**
         * Filter the HTML attributes applied to a menu item's anchor element.
         *
         * @since 3.6.0
         * @since 4.1.0 The `$depth` parameter was added.
         *
         * @param array $atts {
         *     The HTML attributes applied to the menu item's `<a>` element, empty strings are ignored.
         *
         *     @type string $title  Title attribute.
         *     @type string $target Target attribute.
         *     @type string $rel    The rel attribute.
         *     @type string $href   The href attribute.
         * }
         * @param object $item  The current menu item.
         * @param array  $args  An array of {@see wp_nav_menu()} arguments.
         * @param int    $depth Depth of menu item. Used for padding.
         */
        $atts = apply_filters('nav_menu_link_attributes', $atts, $item, $args, $depth);
        $attributes = '';
        foreach ($atts as $attr => $value) {
            if (!empty($value)) {
                $value = ('href' === $attr) ? esc_url($value) : esc_attr($value);
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }
        $item_output = $args->before;
        // New
        // if ($depth === 0 && in_array('menu-item-has-children', $classes)) {
        // 	$item_output .= '<a class="nav-link dropdown-toggle"' . $attributes . 'data-toggle="dropdown">';
        // } elseif ($depth === 0) {
        // 	$item_output .= '<a class="nav-link"' . $attributes . '>';
        // } else {
        // 	$item_output .= '<a class="dropdown-item"' . $attributes . '>';
        // }
        //
        $item_output .= '<a' . $attributes . '>';
        /** This filter is documented in wp-includes/post-template.php */
        $item_output .= $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after;
        $item_output .= '</a>';
        $item_output .= $submenu_indicator;
        $item_output .= $args->after;
        /**
         * Filter a menu item's starting output.
         *
         * The menu item's starting output only includes `$args->before`, the opening `<a>`,
         * the menu item's title, the closing `</a>`, and `$args->after`. Currently, there is
         * no filter for modifying the opening and closing `<li>` for a menu item.
         *
         * @since 3.0.0
         *
         * @param string $item_output The menu item's starting HTML output.
         * @param object $item        Menu item data object.
         * @param int    $depth       Depth of menu item. Used for padding.
         * @param array  $args        An array of {@see wp_nav_menu()} arguments.
         */
        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }

    public function end_el(&$output, $item, $depth = 0, $args = array()) {
        if ($depth === 0) {
            $output .= "</li>\n";
        }
    }

    public static function fallback($args) {
        if (current_user_can('manage_options')) {
            extract($args);
            $fb_output = null;
            if ($container) {
                $fb_output = '<' . $container;
                if ($container_id) {
                    $fb_output .= ' id="' . $container_id . '"';
                }
                if ($container_class) {
                    $fb_output .= ' class="menu-fallback ' . $container_class . '"';
                }
                $fb_output .= '>';
            }
            $fb_output .= '<ul';
            if ($menu_id) {
                $fb_output .= ' id="' . $menu_id . '"';
            }
            if ($menu_class) {
                $fb_output .= ' class="' . $menu_class . '"';
            }
            $fb_output .= '>';
            $fb_output .= '<li><a href="' . admin_url('nav-menus.php') . '">Add a menu</a></li>';
            $fb_output .= '</ul>';
            if ($container) {
                $fb_output .= '</' . $container . '>';
            }
            echo $fb_output;
        }
    }
}
