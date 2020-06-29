<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // disable direct access
}

if ( ! class_exists('Mega_Menu_Badge') ) :

/**
 *
 */
class Mega_Menu_Badge {

    /**
     * Constructor
     *
     * @since 1.10
     */
    public function __construct() {
        add_filter( 'megamenu_load_scss_file_contents', array( $this, 'append_badge_scss'), 10 );
        add_filter( 'megamenu_theme_editor_settings', array( $this, 'add_theme_editor_settings' ), 10 );
        add_filter( 'megamenu_default_theme', array($this, 'add_theme_placeholders'), 10 );
        add_filter( 'megamenu_tabs', array( $this, 'add_badge_tab'), 10, 5 );
        add_filter( 'megamenu_walker_nav_menu_start_el', array( $this, 'output_badge'), 10, 4 );
        add_action( 'megamenu_menu_item_submitted_settings', array( $this, 'handle_badge_item_checkboxes'), 10, 2 );
    }


    /**
     * Set the value to false if the post data is missing
     *
     * @since 1.10
     * @param string $item_output
     * @param object $item
     * @param int $depth
     * @param array $args
     * @return string
     */
    public function handle_badge_item_checkboxes( $submitted_settings, $menu_item_id ) {
        if ( isset( $_POST['tab'] ) && $_POST['tab'] == 'badge' ) {
            $checkboxes = array("hide_on_mobile", "hide_on_desktop");

            foreach ( $checkboxes as $checkbox ) {
                if ( ! isset( $submitted_settings['badge'][ $checkbox ] ) ) {
                    $submitted_settings['badge'][ $checkbox ] = 'false';
                }
            }
        }

        return $submitted_settings;
    }


    /**
     * Output the menu item badge HTML
     *
     * @since 1.10
     * @param string $item_output
     * @param object $item
     * @param int $depth
     * @param array $args
     * @return string
     */
    public function output_badge( $item_output, $item, $depth, $args ) {

        if ( isset( $item->megamenu_settings['badge'] ) && $item->megamenu_settings['badge']['style'] != 'disabled' ) {
            $style = $item->megamenu_settings['badge']['style'];
            $text = $item->megamenu_settings['badge']['text'];
            $text = do_shortcode( $text );

            $classes = array(
                'mega-menu-badge',
                'mega-menu-' . $style
            );

            if ( isset( $item->megamenu_settings['badge']['hide_on_mobile'] ) && $item->megamenu_settings['badge']['hide_on_mobile'] == 'true' ) {
                $classes[] = 'mega-hide-on-mobile';
            }

            if ( isset( $item->megamenu_settings['badge']['hide_on_desktop'] ) && $item->megamenu_settings['badge']['hide_on_desktop'] == 'true' ) {
                $classes[] = 'mega-hide-on-desktop';
            }

            $badge = '<span class="' . implode(" ", $classes) . '">' . $text . '</span>';

            if ( strpos( $item_output, '</span><span class="mega-menu-description"' ) !== false ) {
                $item_output = str_replace( '</span><span class="mega-menu-description"', $badge . '</span><span class="mega-menu-description"', $item_output );
            } elseif ( strpos( $item_output, '<span class="mega-indicator"' ) !== false ) {
                $item_output = str_replace( '<span class="mega-indicator"', $badge . '<span class="mega-indicator"', $item_output );
            }  else {
                $item_output = str_replace( "</a>", $badge . "</a>", $item_output );
            }
        }

        return $item_output;
    }


    /**
     * Add the Badge tab to the menu item options
     *
     * @since 1.10
     * @param array $tabs
     * @param int $menu_item_id
     * @param int $menu_id
     * @param int $menu_item_depth
     * @param array $menu_item_meta
     * @return string
     */
    public function add_badge_tab( $tabs, $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta ) {

        $badge_style = isset( $menu_item_meta['badge']['style'] ) ? $menu_item_meta['badge']['style'] : 'disabled';
        $badge_text = isset( $menu_item_meta['badge']['text'] ) ? $menu_item_meta['badge']['text'] : '';
        $badge_hide_on_mobile = isset( $menu_item_meta['badge']['hide_on_mobile'] ) ? $menu_item_meta['badge']['hide_on_mobile'] : 'false';
        $badge_hide_on_desktop = isset( $menu_item_meta['badge']['hide_on_desktop'] ) ? $menu_item_meta['badge']['hide_on_desktop'] : 'false';

        $html  = "<form>";
        $html .= "    <input type='hidden' name='_wpnonce' value='" . wp_create_nonce('megamenu_edit') . "' />";
        $html .= "    <input type='hidden' name='menu_item_id' value='{$menu_item_id}' />";
        $html .= "    <input type='hidden' name='action' value='mm_save_menu_item_settings' />";
        $html .= "    <input type='hidden' name='clear_cache' value='false' />";
        $html .= "    <input type='hidden' name='tab' value='badge' />";
        $html .= "    <h4 class='first'>" . __("Menu Item Badge", "megamenupro") . "</h4>";
        $html .= "    <table>";
        $html .= "        <tr>";
        $html .= "            <td class='mega-name'>" . __("Badge Style", "megamenupro") . "</td>";
        $html .= "            <td class='mega-value'>";
        $html .= "                <select name='settings[badge][style]'>";
        $html .= "                    <option value='disabled' " . selected( $badge_style, 'disabled', false ) . ">" . __("Disabled", "megamenupro") . "</option>";
        $html .= "                    <option value='badge-style-one' " . selected( $badge_style, 'badge-style-one', false ) . ">" . __("Style 1", "megamenupro") . "</option>";
        $html .= "                    <option value='badge-style-two' " . selected( $badge_style, 'badge-style-two', false ) . ">" . __("Style 2", "megamenupro") . "</option>";
        $html .= "                    <option value='badge-style-three' " . selected( $badge_style, 'badge-style-three', false ) . ">" . __("Style 3", "megamenupro") . "</option>";
        $html .= "                    <option value='badge-style-four' " . selected( $badge_style, 'badge-style-four', false ) . ">" . __("Style 4", "megamenupro") . "</option>";
        $html .= "                </select>";
        $html .= "            </td>";
        $html .= "        </tr>";
        $html .= "        <tr>";
        $html .= "            <td class='mega-name'>";
        $html .=                  __("Badge Text", "megamenupro");
        $html .= "                <div class='mega-description'>" . __("Shortcodes accepted", "megamenupro") . "</div>";
        $html .=              "</td>";
        $html .= "            <td class='mega-value'>";
        $html .= "                <input type='text' name='settings[badge][text]' value='{$badge_text}' />";
        $html .= "            </td>";
        $html .= "        </tr>";
        $html .= '        <tr>';
        $html .= '            <td class="mega-name">';
        $html .=                  __("Hide on Mobile", "megamenupro");
        $html .= '            </td>';
        $html .= '            <td class="mega-value">';
        $html .= '                <input type="checkbox" name="settings[badge][hide_on_mobile]" value="true" ' . checked( $badge_hide_on_mobile, 'true', false ) . ' />';
        $html .= '            </td>';
        $html .= '        </tr>';
        $html .= '        <tr>';
        $html .= '            <td class="mega-name">';
        $html .=                  __("Hide on Desktop", "megamenupro");
        $html .= '            </td>';
        $html .= '            <td class="mega-value">';
        $html .= '                <input type="checkbox" name="settings[badge][hide_on_desktop]" value="true" ' . checked( $badge_hide_on_desktop, 'true', false ) . ' />';
        $html .= '            </td>';
        $html .= '        </tr>';
        $html .= "    </table>";
        $html .= get_submit_button();
        $html .= "</form>";

        $tabs['badge'] = array(
            'title' => __("Badge", "megamenupro"),
            'content' => $html
        );

        return $tabs;
    }


    /**
     * Insert theme placeholder values.
     *
     * @since 1.10
     * @param array $theme
     * @return array
     */
    public function add_theme_placeholders( $theme ) {

        $theme['badge_border_radius_top_left'] = '2px';
        $theme['badge_border_radius_top_right'] = '2px';
        $theme['badge_border_radius_bottom_left'] = '2px';
        $theme['badge_border_radius_bottom_right'] = '2px';
        $theme['badge_padding_top'] = '1px';
        $theme['badge_padding_right'] = '4px';
        $theme['badge_padding_bottom'] = '1px';
        $theme['badge_padding_left'] = '4px';
        $theme['badge_vertical_offset'] = '-7px';

        $theme['badge_one_background_from'] = '#D32F2F';
        $theme['badge_one_background_to'] = '#D32F2F';
        $theme['badge_one_font'] = 'inherit';
        $theme['badge_one_font_size'] = '10px';
        $theme['badge_one_font_color'] = '#fff';
        $theme['badge_one_font_weight'] = 'normal';
        $theme['badge_one_text_transform'] = 'none';
        $theme['badge_one_text_decoration'] = 'none';

        $theme['badge_two_background_from'] = '#00796B';
        $theme['badge_two_background_to'] = '#00796B';
        $theme['badge_two_font'] = 'inherit';
        $theme['badge_two_font_size'] = '10px';
        $theme['badge_two_font_color'] = '#fff';
        $theme['badge_two_font_weight'] = 'normal';
        $theme['badge_two_text_transform'] = 'none';
        $theme['badge_two_text_decoration'] = 'none';

        $theme['badge_three_background_from'] = '#FFC107';
        $theme['badge_three_background_to'] = '#FFC107';
        $theme['badge_three_font'] = 'inherit';
        $theme['badge_three_font_size'] = '10px';
        $theme['badge_three_font_color'] = '#fff';
        $theme['badge_three_font_weight'] = 'normal';
        $theme['badge_three_text_transform'] = 'none';
        $theme['badge_three_text_decoration'] = 'none';

        $theme['badge_four_background_from'] = '#303F9F';
        $theme['badge_four_background_to'] = '#303F9F';
        $theme['badge_four_font'] = 'inherit';
        $theme['badge_four_font_size'] = '10px';
        $theme['badge_four_font_color'] = '#fff';
        $theme['badge_four_font_weight'] = 'normal';
        $theme['badge_four_text_transform'] = 'none';
        $theme['badge_four_text_decoration'] = 'none';


        return $theme;
    }


    /**
     * Add the badgesettings to the theme editor
     *
     * @since 1.10
     * @param array $settings
     * @return array
     */
    public function add_theme_editor_settings( $settings ) {
        $insert['badge'] = array(
            'title' => __( "Badges", "megamenupro" ),
            'settings' => array(
                'badge_general_title' => array(
                    'priority' => 5,
                    'title' => __( "General Badge Styling", "megamenupro" ),
                    'description' => 'These styles will apply to all badges.',
                ),
                'badge_border_radius' => array(
                    'priority' => 10,
                    'title' => __( "Badge Border Radius", "megamenupro" ),
                    'description' => __( "Set rounded corners for badges.", "megamenupro" ),
                    'settings' => array(
                        array(
                            'title' => __( "Top Left", "megamenupro" ),
                            'type' => 'freetext',
                            'key' => 'badge_border_radius_top_left',
                            'validation' => 'px'
                        ),
                        array(
                            'title' => __( "Top Right", "megamenupro" ),
                            'type' => 'freetext',
                            'key' => 'badge_border_radius_top_right',
                            'validation' => 'px'
                        ),
                        array(
                            'title' => __( "Bottom Right", "megamenupro" ),
                            'type' => 'freetext',
                            'key' => 'badge_border_radius_bottom_right',
                            'validation' => 'px'
                        ),
                        array(
                            'title' => __( "Bottom Left", "megamenupro" ),
                            'type' => 'freetext',
                            'key' => 'badge_border_radius_bottom_left',
                            'validation' => 'px'
                        )
                    )
                ),
                'badge_padding' => array(
                    'priority' => 20,
                    'title' => __( "Badge Padding", "megamenupro" ),
                    'description' => __( "Set the padding around the text within badges.", "megamenupro" ),
                    'settings' => array(
                        array(
                            'title' => __( "Top", "megamenupro" ),
                            'type' => 'freetext',
                            'key' => 'badge_padding_top',
                            'validation' => 'px'
                        ),
                        array(
                            'title' => __( "Right", "megamenupro" ),
                            'type' => 'freetext',
                            'key' => 'badge_padding_right',
                            'validation' => 'px'
                        ),
                        array(
                            'title' => __( "Bottom", "megamenupro" ),
                            'type' => 'freetext',
                            'key' => 'badge_padding_bottom',
                            'validation' => 'px'
                        ),
                        array(
                            'title' => __( "Left", "megamenupro" ),
                            'type' => 'freetext',
                            'key' => 'badge_padding_left',
                            'validation' => 'px'
                        )
                    )
                ),
                'badge_vertical_position' => array(
                    'priority' => 30,
                    'title' => __( "Badge Vertical Offset", "megamenupro" ),
                    'description' => __( "Move badges vertically relative to the menu item text.", "megamenupro" ),
                    'settings' => array(
                        array(
                            'title' => __( "Offset", "megamenupro" ),
                            'type' => 'freetext',
                            'key' => 'badge_vertical_offset',
                            'validation' => 'px'
                        )
                    )
                ),
                'badge_one_title' => array(
                    'priority' => 40,
                    'title' => __( "Badge Style One", "megamenupro" ),
                    'description' => '',
                ),
                'badge_one_background' => array(
                    'priority' => 50,
                    'title' => __( "Background", "megamenupro" ),
                    'description' => __( "Set the background color for badge style one.", "megamenupro" ),
                    'settings' => array(
                        array(
                            'title' => __( "From", "megamenupro" ),
                            'type' => 'color',
                            'key' => 'badge_one_background_from'
                        ),
                        array(
                            'title' => __( "Copy", "megamenupro" ),
                            'type' => 'copy_color',
                            'key' => 'copy_color'
                        ),
                        array(
                            'title' => __( "To", "megamenupro" ),
                            'type' => 'color',
                            'key' => 'badge_one_background_to'
                        )
                    )
                ),
                'badge_one_font' => array(
                    'priority' => 60,
                    'title' => __( "Font", "megamenupro" ),
                    'description' => __( "Set the font for badge style one.", "megamenupro" ),
                    'settings' => array(
                        array(
                            'title' => __( "Color", "megamenupro" ),
                            'type' => 'color',
                            'key' => 'badge_one_font_color'
                        ),
                        array(
                            'title' => __( "Size", "megamenupro" ),
                            'type' => 'freetext',
                            'key' => 'badge_one_font_size',
                            'validation' => 'px'
                        ),
                        array(
                            'title' => __( "Family", "megamenupro" ),
                            'type' => 'font',
                            'key' => 'badge_one_font'
                        ),
                        array(
                            'title' => __( "Transform", "megamenupro" ),
                            'type' => 'transform',
                            'key' => 'badge_one_text_transform'
                        ),
                        array(
                            'title' => __( "Weight", "megamenupro" ),
                            'type' => 'weight',
                            'key' => 'badge_one_font_weight'
                        ),
                        array(
                            'title' => __( "Decoration", "megamenupro" ),
                            'type' => 'decoration',
                            'key' => 'badge_one_text_decoration'
                        )
                    )
                ),

                'badge_two_title' => array(
                    'priority' => 70,
                    'title' => __( "Badge Style Two", "megamenupro" ),
                    'description' => '',
                ),
                'badge_two_background' => array(
                    'priority' => 80,
                    'title' => __( "Background", "megamenupro" ),
                    'description' => __( "Set the background color for badge style two.", "megamenupro" ),
                    'settings' => array(
                        array(
                            'title' => __( "From", "megamenupro" ),
                            'type' => 'color',
                            'key' => 'badge_two_background_from'
                        ),
                        array(
                            'title' => __( "Copy", "megamenupro" ),
                            'type' => 'copy_color',
                            'key' => 'copy_color'
                        ),
                        array(
                            'title' => __( "To", "megamenupro" ),
                            'type' => 'color',
                            'key' => 'badge_two_background_to'
                        )
                    )
                ),
                'badge_two_font' => array(
                    'priority' => 90,
                    'title' => __( "Font", "megamenupro" ),
                    'description' => __( "Set the font for badge style two.", "megamenupro" ),
                    'settings' => array(
                        array(
                            'title' => __( "Color", "megamenupro" ),
                            'type' => 'color',
                            'key' => 'badge_two_font_color'
                        ),
                        array(
                            'title' => __( "Size", "megamenupro" ),
                            'type' => 'freetext',
                            'key' => 'badge_two_font_size',
                            'validation' => 'px'
                        ),
                        array(
                            'title' => __( "Family", "megamenupro" ),
                            'type' => 'font',
                            'key' => 'badge_two_font'
                        ),
                        array(
                            'title' => __( "Transform", "megamenupro" ),
                            'type' => 'transform',
                            'key' => 'badge_two_text_transform'
                        ),
                        array(
                            'title' => __( "Weight", "megamenupro" ),
                            'type' => 'weight',
                            'key' => 'badge_two_font_weight'
                        ),
                        array(
                            'title' => __( "Decoration", "megamenupro" ),
                            'type' => 'decoration',
                            'key' => 'badge_two_text_decoration'
                        )
                    )
                ),
                'badge_three_title' => array(
                    'priority' => 100,
                    'title' => __( "Badge Style Three", "megamenupro" ),
                    'description' => '',
                ),
                'badge_three_background' => array(
                    'priority' => 110,
                    'title' => __( "Background", "megamenupro" ),
                    'description' => __( "Set the background color for badge style three.", "megamenupro" ),
                    'settings' => array(
                        array(
                            'title' => __( "From", "megamenupro" ),
                            'type' => 'color',
                            'key' => 'badge_three_background_from'
                        ),
                        array(
                            'title' => __( "Copy", "megamenupro" ),
                            'type' => 'copy_color',
                            'key' => 'copy_color'
                        ),
                        array(
                            'title' => __( "To", "megamenupro" ),
                            'type' => 'color',
                            'key' => 'badge_three_background_to'
                        )
                    )
                ),
                'badge_three_font' => array(
                    'priority' => 120,
                    'title' => __( "Font", "megamenupro" ),
                    'description' => __( "Set the font for badge style three.", "megamenupro" ),
                    'settings' => array(
                        array(
                            'title' => __( "Color", "megamenupro" ),
                            'type' => 'color',
                            'key' => 'badge_three_font_color'
                        ),
                        array(
                            'title' => __( "Size", "megamenupro" ),
                            'type' => 'freetext',
                            'key' => 'badge_three_font_size',
                            'validation' => 'px'
                        ),
                        array(
                            'title' => __( "Family", "megamenupro" ),
                            'type' => 'font',
                            'key' => 'badge_three_font'
                        ),
                        array(
                            'title' => __( "Transform", "megamenupro" ),
                            'type' => 'transform',
                            'key' => 'badge_three_text_transform'
                        ),
                        array(
                            'title' => __( "Weight", "megamenupro" ),
                            'type' => 'weight',
                            'key' => 'badge_three_font_weight'
                        ),
                        array(
                            'title' => __( "Decoration", "megamenupro" ),
                            'type' => 'decoration',
                            'key' => 'badge_three_text_decoration'
                        )
                    )
                ),
                'badge_four_title' => array(
                    'priority' => 130,
                    'title' => __( "Badge Style Four", "megamenupro" ),
                    'description' => '',
                ),
                'badge_four_background' => array(
                    'priority' => 140,
                    'title' => __( "Background", "megamenupro" ),
                    'description' => __( "Set the background color for badge style four.", "megamenupro" ),
                    'settings' => array(
                        array(
                            'title' => __( "From", "megamenupro" ),
                            'type' => 'color',
                            'key' => 'badge_four_background_from'
                        ),
                        array(
                            'title' => __( "Copy", "megamenupro" ),
                            'type' => 'copy_color',
                            'key' => 'copy_color'
                        ),
                        array(
                            'title' => __( "To", "megamenupro" ),
                            'type' => 'color',
                            'key' => 'badge_four_background_to'
                        )
                    )
                ),
                'badge_four_font' => array(
                    'priority' => 150,
                    'title' => __( "Font", "megamenupro" ),
                    'description' => __( "Set the font for badge style four.", "megamenupro" ),
                    'settings' => array(
                        array(
                            'title' => __( "Color", "megamenupro" ),
                            'type' => 'color',
                            'key' => 'badge_four_font_color'
                        ),
                        array(
                            'title' => __( "Size", "megamenupro" ),
                            'type' => 'freetext',
                            'key' => 'badge_four_font_size',
                            'validation' => 'px'
                        ),
                        array(
                            'title' => __( "Family", "megamenupro" ),
                            'type' => 'font',
                            'key' => 'badge_four_font'
                        ),
                        array(
                            'title' => __( "Transform", "megamenupro" ),
                            'type' => 'transform',
                            'key' => 'badge_four_text_transform'
                        ),
                        array(
                            'title' => __( "Weight", "megamenupro" ),
                            'type' => 'weight',
                            'key' => 'badge_four_font_weight'
                        ),
                        array(
                            'title' => __( "Decoration", "megamenupro" ),
                            'type' => 'decoration',
                            'key' => 'badge_four_text_decoration'
                        )
                    )
                ),
            )
        );
        
        // insert Badge tab just before the Custom Styling tab
        array_splice( $settings, 5, 0, $insert );

        return $settings;
    }


    /**
     * Add the CSS required to style menu item badges
     *
     * @since 1.10
     * @param string $scss
     * @return string
     */
    public function append_badge_scss( $scss ) {

        $path = trailingslashit( plugin_dir_path( __FILE__ ) ) . 'scss/badge.scss';

        $contents = file_get_contents( $path );

        return $scss . $contents;

    }

}

endif;