<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // disable direct access
}

if ( ! class_exists('Mega_Menu_Sticky') ) :

/**
 *
 */
class Mega_Menu_Sticky {


    /**
     * Constructor
     *
     * @since 1.0
     */
    public function __construct() {

        add_filter( 'megamenu_wrap_attributes', array( $this, 'add_sticky_attribute' ), 10, 5 );
        add_filter( 'megamenu_scss_variables', array( $this, 'add_sticky_scss_vars'), 10, 4 );
        add_filter( 'megamenu_load_scss_file_contents', array( $this, 'append_sticky_scss'), 10 );
        add_action( 'megamenu_settings_table', array( $this, 'add_sticky_setting'), 20, 2);
        add_filter( 'megamenu_after_menu_item_settings', array( $this, 'add_menu_item_sticky_options'), 10, 6 );
        add_filter( 'megamenu_submitted_settings_meta', array( $this, 'filter_submitted_settings'), 10);
        add_action( 'wp_ajax_mm_get_sticky_notes', array( $this, 'ajax_get_sticky_notes' ) );
        add_filter( 'megamenu_default_theme', array($this, 'add_theme_placeholders'), 10 );
        add_filter( 'megamenu_theme_editor_settings', array( $this, 'add_theme_editor_settings' ), 10 );

    }

    /**
     * Insert theme placeholder values.
     *
     * @since 1.6
     * @param array $theme
     * @return array
     */
    public function add_theme_placeholders( $theme ) {

        $theme['sticky_menu_height'] = 'off';
        $theme['sticky_menu_transition'] = 'off';
        $theme['sticky_menu_item_link_height'] = 'menu_item_link_height';

        return $theme;
    }


    /**
     * Add sticky menu height option to theme editor
     *
     * @since 1.6
     * @param array $settings
     * @return array
     */
    public function add_theme_editor_settings( $settings ) {

        $new_settings = array(
            'sticky_menu_item_link_height' => array(
                'priority' => 06,
                'title' => __( "Menu Height (Sticky)", "megamenu" ),
                'description' => __( "The height of the menu when sticky.", "megamenu" ),
                'settings' => array(
                    array(
                        'title' => __( "Enabled", "megamenu" ),
                        'type' => 'checkbox',
                        'key' => 'sticky_menu_height'
                    ),
                    array(
                        'title' => __( "Height", "megamenu" ),
                        'type' => 'freetext',
                        'key' => 'sticky_menu_item_link_height',
                        'validation' => 'px'
                    ),
                    array(
                        'title' => __( "Transition", "megamenu" ),
                        'type' => 'checkbox',
                        'key' => 'sticky_menu_transition'
                    ),
                )
            )
        );

        $settings['menu_bar']['settings'] = array_merge($settings['menu_bar']['settings'], $new_settings);

        return $settings;
    }


    /**
     * Return the HTML to display in the Lightbox
     *
     * @since 1.6.2.2
     * @return string
     */
    public function ajax_get_sticky_notes() {

        check_ajax_referer( 'megamenu_edit' );

        if ( ob_get_contents() ) ob_clean(); // remove any warnings or output from other plugins which may corrupt the response

        $response = "<h2>Sticky Menu</h2>";
        $response .= "<p><b>Is your theme already sticking/fixing the header?</b></p>";
        $response .= "<p>Only enable this option if your theme is not already sticking the theme header and menu.</p>";
        $response .= "<o>If your theme is already sticking/fixing your header and menu, then enabling this sticky option will cause conflicts. Therefore, if your theme is already sticking the header, you should leave this option unchecked.</p>";
        
        wp_send_json_success( json_encode( $response ) );
    }

    /**
     * Makr sure 'sticky enabled' really is set to false if the checkbox is unchecked.
     */
    public function filter_submitted_settings($settings) {
        if ( is_array( $settings ) ) {
            foreach ( $settings as $location => $vars ) {
                if ( ! isset( $vars['sticky_enabled'] ) ) {
                    $settings[$location]['sticky_enabled'] = 'false';
                }

                if ( ! isset( $vars['sticky_mobile'] ) ) {
                    $settings[$location]['sticky_mobile'] = 'false';
                }

                if ( ! isset( $vars['sticky_desktop'] ) ) {
                    $settings[$location]['sticky_desktop'] = 'false';
                }

                if ( ! isset( $vars['sticky_expand_mobile'] ) ) {
                    $settings[$location]['sticky_expand_mobile'] = 'false';
                }
            }
        }

        return $settings;
    }
    
    /**
     * Add sticky menu item visibility option
     *
     * @since 1.5.2
     */
    public function add_menu_item_sticky_options( $html, $tabs, $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta ) {

        if ( !isset( $menu_item_meta['sticky_visibility'] ) ) {
            $menu_item_meta['sticky_visibility'] = 'always';
        }

        $return  = '        <tr>';
        $return .= '            <td class="mega-name">';
        $return .=                  __("Visibility in Sticky Menu", "megamenupro");
        $return .= '            </td>';
        $return .= '            <td class="mega-value">';
        $return .= '                <select name="settings[sticky_visibility]">';
        $return .= '                    <option value="always" ' . selected( $menu_item_meta['sticky_visibility'], 'always', false ) . '>' . __("Always show", "megamenupro") . '</option>';
        $return .= '                    <option value="show" ' . selected( $menu_item_meta['sticky_visibility'], 'show', false ) . '>' . __("Show only when menu is stuck", "megamenupro") . '</option>';
        $return .= '                    <option value="hide" ' . selected( $menu_item_meta['sticky_visibility'], 'hide', false ) . '>' . __("Hide when menu is stuck", "megamenupro") . '</option>';
        $return .= '                </select>';
        $return .= '            </td>';
        $return .= '        </tr>';

        $html .= $return;

        return $html;
    }


    /**
     * Add Orientation setting to menu options
     *
     * @since 1.1
     * @param string $location
     * @param array $settings
     */
    public function add_sticky_setting( $location, $settings ) {
        ?>
            </table>
            <table class='sticky_settings'>
                <tr>
                    <td>
                        <?php _e("Sticky", "megamenupro"); ?>
                    </td>
                    <td>
                        <input type='checkbox' class='megamenu_sticky_enabled' name='megamenu_meta[<?php echo $location ?>][sticky_enabled]' value='true' <?php checked( $this->get_sticky_setting($settings, $location, 'sticky_enabled') == 'true' ); ?> />
                    </td>
                </tr>

                <?php

                    if ( $this->get_sticky_setting($settings, $location, 'sticky_hide_until_scroll_up') == 'true' ) {
                        $sticky_husu_display = 'table-row';
                    } else {
                        $sticky_husu_display = 'none';
                    }

                    if ( $this->get_sticky_setting($settings, $location, 'sticky_enabled') == 'true' ) {
                        $sticky_display = 'table-row';
                    } else {
                        $sticky_display = 'none';
                        $sticky_husu_display = 'none';
                    }
                ?>

                <tr class='megamenu_sticky_behaviour' style='display: <?php echo $sticky_display; ?>;'>
                    <td>
                        <?php _e("Stick On Desktop", "megamenupro"); ?><i class='mmm_tooltip dashicons dashicons-info' title="<?php _e("IMPORTANT: Only enable this if your menu is not already within a sticky container.", "megamenupro"); ?>"></i>
                        </div>
                    </td>
                    <td>
                        <input type='checkbox' name='megamenu_meta[<?php echo $location ?>][sticky_desktop]' value='true' <?php checked( $this->get_sticky_setting($settings, $location, 'sticky_desktop') == 'true' ); ?> />
                    </td>
                </tr>
                <tr class='megamenu_sticky_behaviour' style='display: <?php echo $sticky_display; ?>;'>
                    <td>
                        <?php _e("Stick On Mobile", "megamenupro"); ?><i class='mmm_tooltip dashicons dashicons-info' title="<?php _e("IMPORTANT: Only enable this if your menu is small enough to fully fit on the screen without completely covering the page content.", "megamenupro"); ?>"></i>
                        </div>
                    </td>
                    <td>
                        <input type='checkbox' name='megamenu_meta[<?php echo $location ?>][sticky_mobile]' value='true' <?php checked( $this->get_sticky_setting($settings, $location, 'sticky_mobile') == 'true' ); ?> />
                    </td>
                </tr>
                <tr class='megamenu_sticky_behaviour' style='display: <?php echo $sticky_display; ?>;'>
                    <td class='mega-name'>
                        <?php _e("Sticky Opacity", "megamenupro"); ?><i class='mmm_tooltip dashicons dashicons-info' title='<?php _e("Set the transparency of the menu when sticky (values 0.2 - 1.0). Default: 1.", "megamenupro"); ?>'></i>
                    </td>
                    <td>
                        <input type='number' step='0.1' min='0.2' max='1' name='megamenu_meta[<?php echo $location; ?>][sticky_opacity]' value='<?php echo $this->get_sticky_setting($settings, $location, 'sticky_opacity'); ?>' />
                    </td>
                </tr>
                <tr class='megamenu_sticky_behaviour' style='display: <?php echo $sticky_display; ?>;'>
                    <td>
                        <?php _e("Sticky Offset", "megamenupro"); ?><i class='mmm_tooltip dashicons dashicons-info' title='<?php _e("Set the distance between top of window and top of menu when the menu is stuck. Default: 0.", "megamenupro"); ?>'></i>
                    </td>
                    <td>
                        <input type='number' name='megamenu_meta[<?php echo $location; ?>][sticky_offset]' value='<?php echo $this->get_sticky_setting($settings, $location, 'sticky_offset'); ?>' /><span class='mega-after'>px</span>
                    </td>
                </tr>
                <tr class='megamenu_sticky_behaviour' style='display: <?php echo $sticky_display; ?>;'>
                    <td>
                        <?php _e("Expand Background Desktop", "megamenupro"); ?><i class='mmm_tooltip dashicons dashicons-info' title='<?php _e("Expand the background of the menu to fill the page width once the menu becomes sticky. Only compatible with Horizontal menus", "megamenupro"); ?>'></i>
                    </td>
                    <td>
                        <input type='checkbox' name='megamenu_meta[<?php echo $location ?>][sticky_expand]' value='true' <?php checked( $this->get_sticky_setting($settings, $location, 'sticky_expand') == 'true' ); ?> />
                    </td>
                </tr>
                <tr class='megamenu_sticky_behaviour' style='display: <?php echo $sticky_display; ?>;'>
                    <td>
                        <?php _e("Expand Mobile Menu", "megamenupro"); ?><i class='mmm_tooltip dashicons dashicons-info' title='<?php _e("Expand the width of the mobile menu to fill the page width once the menu becomes sticky. Only compatible with Horizontal menus", "megamenupro"); ?>'></i>
                    </td>
                    <td>
                        <input type='checkbox' name='megamenu_meta[<?php echo $location ?>][sticky_expand_mobile]' value='true' <?php checked( $this->get_sticky_setting($settings, $location, 'sticky_expand_mobile') == 'true' ); ?> />
                    </td>
                </tr>
                <tr class='megamenu_sticky_behaviour' style='display: <?php echo $sticky_display; ?>;'>
                    <td>
                        <?php _e("Hide until scroll up", "megamenupro"); ?><i class='mmm_tooltip dashicons dashicons-info' title='<?php _e("Hide the menu as the user scrolls down the page, and reveal the menu when the user scrolls up. Only compatible with Horizontal menus", "megamenupro"); ?>'></i>
                    </td>
                    <td>
                        <input type='checkbox' class='megamenu_sticky_husu_enabled' name='megamenu_meta[<?php echo $location ?>][sticky_hide_until_scroll_up]' value='true' <?php checked( $this->get_sticky_setting($settings, $location, 'sticky_hide_until_scroll_up') == 'true' ); ?> />
                    </td>
                </tr>
                <tr class='megamenu_sticky_husu' style='display: <?php echo $sticky_husu_display; ?>;'>
                    <td>
                        <?php _e("Scroll tolerance (0-50)", "megamenupro"); ?><i class='mmm_tooltip dashicons dashicons-info' title='<?php _e("Prevent the menu from being rapidly hidden and revealed due to small mouse movements. Default: 10", "megamenupro"); ?>'></i>
                    </td>
                    <td>
                        <input type='number' step='1' min='0' max='50' name='megamenu_meta[<?php echo $location; ?>][sticky_hide_until_scroll_up_tolerance]' value='<?php echo $this->get_sticky_setting($settings, $location, 'sticky_hide_until_scroll_up_tolerance'); ?>' /><span class='mega-after'>px</span>
                    </td>
                </tr>
                <tr class='megamenu_sticky_husu' style='display: <?php echo $sticky_husu_display; ?>;'>
                    <td>
                        <?php _e("Hide until scroll up offset", "megamenupro"); ?><i class='mmm_tooltip dashicons dashicons-info' title='<?php _e("Initiate the Hide Until Scroll Up functionality once the page has been scrolled down this distance.", "megamenupro"); ?>'></i>
                    </td>
                    <td>
                        <input type='number' step='1' min='0' name='megamenu_meta[<?php echo $location; ?>][sticky_hide_until_scroll_up_offset]' value='<?php echo $this->get_sticky_setting($settings, $location, 'sticky_hide_until_scroll_up_offset'); ?>' /><span class='mega-after'>px</span>
                    </td>
                </tr>
            </table>
            <table>

        <?php

    }


    /**
     *
     */
    public function add_sticky_scss_vars( $vars, $location, $theme, $menu_id ) {

        $settings = get_option('megamenu_settings');

        $opacity = $this->get_sticky_setting( $settings, $location, 'sticky_opacity');

        $vars['sticky_menu_opacity'] = $opacity;

        $expand = $this->get_sticky_setting( $settings, $location, 'sticky_expand');

        $vars['sticky_menu_expand'] = $expand;

        return $vars;

    }


    /**
     * Add the sticky CSS to the main SCSS file
     *
     * @since 1.0
     * @param string $scss
     * @return string
     */
    public function append_sticky_scss( $scss ) {

        $path = trailingslashit( plugin_dir_path( __FILE__ ) ) . 'scss/sticky.scss';

        $contents = file_get_contents( $path );

        return $scss . $contents;

    }


    /**
     * Add the sticky related attributes to the menu wrapper
     */
    public function add_sticky_attribute( $attributes, $menu_id, $menu_settings, $settings, $current_theme_location ) {

        if ( $this->get_sticky_setting( $settings, $current_theme_location, 'sticky_enabled') == 'true' ) {
            $attributes['data-sticky-enabled'] = 'true';
            $attributes['data-sticky-desktop'] = $this->get_sticky_setting( $settings, $current_theme_location, 'sticky_desktop' );
            $attributes['data-sticky-mobile'] = $this->get_sticky_setting( $settings, $current_theme_location, 'sticky_mobile' );
            $attributes['data-sticky-offset'] = $this->get_sticky_setting( $settings, $current_theme_location, 'sticky_offset' );
            $attributes['data-sticky-expand'] = $this->get_sticky_setting( $settings, $current_theme_location, 'sticky_expand' );
            $attributes['data-sticky-expand-mobile'] = $this->get_sticky_setting( $settings, $current_theme_location, 'sticky_expand_mobile' );

            $menu_theme = mmm_get_theme_for_location( $current_theme_location );

            if ( $menu_theme['sticky_menu_height'] == "on" && $menu_theme['sticky_menu_transition'] === "on" ) {
                $attributes['data-sticky-transition'] = 'true';
            } else {
                $attributes['data-sticky-transition'] = 'false';
            }

            if ($this->get_sticky_setting( $settings, $current_theme_location, 'sticky_hide_until_scroll_up' ) == 'true') {
                $attributes['data-sticky-hide'] = $this->get_sticky_setting( $settings, $current_theme_location, 'sticky_hide_until_scroll_up' );
                $attributes['data-sticky-hide-tolerance'] = $this->get_sticky_setting( $settings, $current_theme_location, 'sticky_hide_until_scroll_up_tolerance' );
                $attributes['data-sticky-hide-offset'] = $this->get_sticky_setting( $settings, $current_theme_location, 'sticky_hide_until_scroll_up_offset' );
            }
        }

        return $attributes;
    }



    /**
     * Return a setting, taking into account backwards compatibility (when it was only possible to make a single location sticky)
     * @since 1.4.6
     */
    private function get_sticky_setting( $saved_settings, $location, $setting ) {

        if ( isset( $saved_settings[$location][$setting] ) ) {
            return $saved_settings[$location][$setting];
        }

        // backwards compatibility from this point onwards
        if ( isset($saved_settings['sticky']['location']) && $setting == 'sticky_enabled' && $location == $saved_settings['sticky']['location'] ) {
            return "true";
        }

        $old_setting_name = substr($setting, 7);

        if ( isset( $saved_settings['sticky'][$old_setting_name]) && $location == $saved_settings['sticky']['location'] ) {
            return $saved_settings['sticky'][$old_setting_name];
        }
        
        if ( $setting == 'sticky_expand_mobile' && ! isset( $saved_settings[$location]['sticky_expand_mobile'] ) && isset( $saved_settings[$location]['sticky_expand'] ) ) {
            return $saved_settings[$location]['sticky_expand'];
        }

        // defaults
        $defaults = array(
            'sticky_location' => 'false',
            'sticky_opacity' => '1.0',
            'sticky_desktop' => 'true',
            'sticky_mobile' => 'false',
            'sticky_offset' => '0',
            'sticky_expand' => 'false',
            'sticky_expand_mobile' => 'false',
            'sticky_hide_until_scroll_up' => 'false',
            'sticky_hide_until_scroll_up_tolerance' => '10',
            'sticky_hide_until_scroll_up_offset' => '0'
        );


        if ( isset( $defaults[$setting] ) ) {
            return $defaults[$setting];
        }

        return 'false';
    }
}

endif;