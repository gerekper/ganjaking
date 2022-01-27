<?php

#Support menus
add_action('init', 'register_my_menus');
function register_my_menus()
{
    register_nav_menus(
        array(
            'main_menu' => 'Main menu',
            'column_menu' => 'Column menu',
        )
    );
}

function gt3_course_query_var( $vars ){
  $vars[] = "course_term_id";
  return $vars;
}
add_filter( 'query_vars', 'gt3_course_query_var' );

#ADD localization folder
add_action('init', 'enable_pomo_translation');
function enable_pomo_translation()
{
    load_theme_textdomain('agrosector', get_template_directory() . '/core/languages/');
}


class GT3_Walker_Nav_Menu extends Walker_Nav_Menu {

    protected $megamenu;
    protected $background_image;
    protected $padding_left;
    protected $padding_right;
    protected $sidebar;

    public function start_lvl( &$output, $depth = 0, $args = array() ) {
        if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
            $t = '';
            $n = '';
        } else {
            $t = "\t";
            $n = "\n";
        }
        $sub_menu_class = '';
        $sub_menu_style = '';
        if (!empty($this->background_image)) {
            $sub_menu_class = ' gt3_menu_background_active';
            $sub_menu_style .= "background-image:url(" . esc_url($this->background_image) . ");";
        }
        $sub_menu_style .= !empty($this->padding_left) ? "padding-left:".(int)$this->padding_left."px;" : "";
        $sub_menu_style .= !empty($this->padding_right) ? "padding-right:".(int)$this->padding_right."px;" : "";
        $sub_menu_style = !empty($sub_menu_style) ? ' style="'.$sub_menu_style.'"' : '';
        $triangle = '';
        if ($this->megamenu == 'true') {
            $triangle = '<li class="gt3_megamenu_triangle_container"><div class="gt3_megamenu_triangle"></div></li>';
        }
        $indent = str_repeat( $t, $depth );
        $output .= "{$n}{$indent}<ul class=\"sub-menu".esc_attr($sub_menu_class)."\"".$sub_menu_style.">".$triangle."{$n}";
    }

    public function end_lvl( &$output, $depth = 0, $args = array() ) {
        if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
            $t = '';
            $n = '';
        } else {
            $t = "\t";
            $n = "\n";
        }
        $indent = str_repeat( $t, $depth );
        $output .= "$indent</ul>{$n}";
    }

    public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
        if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
            $t = '';
            $n = '';
        } else {
            $t = "\t";
            $n = "\n";
        }
        $indent = ( $depth ) ? str_repeat( $t, $depth ) : '';

        $classes = empty( $item->classes ) ? array() : (array) $item->classes;
        $this->background_image = '';
        $this->padding_left = '';
        $this->padding_right = '';
        $this->megamenu = '';
        if ($depth == 0 && !empty($item->megamenu) && $item->megamenu == 'true') {
            $classes[] = 'gt3_megamenu_active';
            $this->megamenu = 'true';
            $this->background_image = !empty($item->background_image) ? $item->background_image : '';
            $this->padding_left = !empty($item->padding_left) ? $item->padding_left : '';
            $this->padding_right = !empty($item->padding_right) ? $item->padding_right : '';
        }
        if (!empty($item->sidebar) && $item->sidebar != '' && $depth == 1){
            $this->sidebar = $item->sidebar;
        }elseif($depth == 1){
            $this->sidebar = '';
        }
        $classes[] = 'menu-item-' . $item->ID;

        /**
         * Filters the arguments for a single nav menu item.
         *
         * @since 4.4.0
         *
         * @param stdClass $args  An object of wp_nav_menu() arguments.
         * @param WP_Post  $item  Menu item data object.
         * @param int      $depth Depth of menu item. Used for padding.
         */
        $args = apply_filters( 'nav_menu_item_args', $args, $item, $depth );

        /**
         * Filters the CSS class(es) applied to a menu item's list item element.
         *
         * @since 3.0.0
         * @since 4.1.0 The `$depth` parameter was added.
         *
         * @param array    $classes The CSS classes that are applied to the menu item's `<li>` element.
         * @param WP_Post  $item    The current menu item.
         * @param stdClass $args    An object of wp_nav_menu() arguments.
         * @param int      $depth   Depth of menu item. Used for padding.
         */
        $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
        $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

        /**
         * Filters the ID applied to a menu item's list item element.
         *
         * @since 3.0.1
         * @since 4.1.0 The `$depth` parameter was added.
         *
         * @param string   $menu_id The ID that is applied to the menu item's `<li>` element.
         * @param WP_Post  $item    The current menu item.
         * @param stdClass $args    An object of wp_nav_menu() arguments.
         * @param int      $depth   Depth of menu item. Used for padding.
         */
        $id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args, $depth );
        $id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

        $output .= $indent . '<li' . $id . $class_names .'>';

        $atts = array();
        $atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
        $atts['target'] = ! empty( $item->target )     ? $item->target     : '';
        $atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
        $atts['href']   = ! empty( $item->url )        ? $item->url        : '';

        /**
         * Filters the HTML attributes applied to a menu item's anchor element.
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
         * @param WP_Post  $item  The current menu item.
         * @param stdClass $args  An object of wp_nav_menu() arguments.
         * @param int      $depth Depth of menu item. Used for padding.
         */
        $atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

        $attributes = '';
        foreach ( $atts as $attr => $value ) {
            if ( ! empty( $value ) ) {
                $value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }

        /** This filter is documented in wp-includes/post-template.php */
        $title = apply_filters( 'the_title', $item->title, $item->ID );

        /**
         * Filters a menu item's title.
         *
         * @since 4.4.0
         *
         * @param string   $title The menu item's title.
         * @param WP_Post  $item  The current menu item.
         * @param stdClass $args  An object of wp_nav_menu() arguments.
         * @param int      $depth Depth of menu item. Used for padding.
         */
        $title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );
        $item_output = '';
        if (!empty($args->before)) {
            $item_output = $args->before;
        }
        if ($item->show_title == 'true' && $depth == 1) {
        }else{
            $item_output .= '<a'. $attributes .'>';
            if (!empty($args->link_before)) {
                $item_output .= $args->link_before;
            }
            $item_output .= $title;
            if (!empty($args->link_after)) {
                $item_output .= $args->link_after;
            }
            if (!empty($item->label_text)) {
                $item_output .= '<span class="gt3_menu_label" style="'.(!empty($item->label_color) ? 'color:'.esc_attr($item->label_color).';' : '').(!empty($item->label_bg_color) ? 'background-color:'.esc_attr($item->label_bg_color).';' : '').'">'.esc_html($item->label_text).'</span>';
            }
            $item_output .= '</a>';
        }
        if (!empty($args->after)) {
            $item_output .= $args->after;
        }

        /**
         * Filters a menu item's starting output.
         *
         * The menu item's starting output only includes `$args->before`, the opening `<a>`,
         * the menu item's title, the closing `</a>`, and `$args->after`. Currently, there is
         * no filter for modifying the opening and closing `<li>` for a menu item.
         *
         * @since 3.0.0
         *
         * @param string   $item_output The menu item's starting HTML output.
         * @param WP_Post  $item        Menu item data object.
         * @param int      $depth       Depth of menu item. Used for padding.
         * @param stdClass $args        An object of wp_nav_menu() arguments.
         */
        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
    }

    /**
     * Ends the element output, if needed.
     *
     * @since 3.0.0
     *
     * @see Walker::end_el()
     *
     * @param string   $output Passed by reference. Used to append additional content.
     * @param WP_Post  $item   Page data object. Not used.
     * @param int      $depth  Depth of page. Not Used.
     * @param stdClass $args   An object of wp_nav_menu() arguments.
     */
    public function end_el( &$output, $item, $depth = 0, $args = array() ) {
        if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
            $t = '';
            $n = '';
        } else {
            $t = "\t";
            $n = "\n";
        }
        $sidebar = '';

        if (!empty($this->sidebar) && $this->sidebar != '' && $depth == 1){
            $sidebar = '';
            $sidebar .= '<div class="gt3_menu_sidebar_container">';
                if (is_active_sidebar( $this->sidebar )) {
                    $sidebar .= "<aside class='sidebar'>";
                    ob_start();
                        dynamic_sidebar($this->sidebar);
                    $sidebar_html = ob_get_contents();
                    ob_end_clean();
                    $sidebar .= $sidebar_html;
                    $sidebar .= "</aside>";
                }
            $sidebar .= '</div>';
        }
        $output .= $sidebar."</li>{$n}";
    }

} // Walker_Nav_Menu
