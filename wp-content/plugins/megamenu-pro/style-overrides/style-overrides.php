<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // disable direct access
}

if ( ! class_exists('Mega_Menu_Style_Overrides') ) :

/**
 *
 */
class Mega_Menu_Style_Overrides {

	/**
	 * Constructor
	 *
	 * @since 1.0
	 */
	public function __construct() {

		add_filter( 'megamenu_tabs', array( $this, 'add_styling_tab'), 10, 5 );
        add_action( 'wp_ajax_mm_save_menu_item_styles', array( $this, 'ajax_save_menu_item_styles') );
		add_filter( 'megamenu_scss_variables', array( $this, 'add_style_overrides'), 10, 4 );
		add_filter( 'megamenu_load_scss_file_contents', array( $this, 'append_scss'), 10 );

	}


	/**
	 * Append the custom icon SCSS to the main SCSS file
	 *
	 * @since 1.0
	 * @param string $scss
	 * @param string
	 */
	public function append_scss( $scss ) {

		$path = trailingslashit( plugin_dir_path( __FILE__ ) ) . 'scss/style-overrides.scss';

		$contents = file_get_contents( $path );

 		return $scss . $contents;

	}


	/**
	 * Add the Styling tab to the menu item options
	 *
	 * @since 1.0
	 * @param array $tabs
	 * @param int $menu_item_id
	 * @param int $menu_id
	 * @param int $menu_item_depth
	 * @param array $menu_item_meta
	 * @return string
	 */
	public function add_styling_tab( $tabs, $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta ) {

		$html  = "<form id='mm_custom_styles'>";
		$html .= "    <input type='hidden' name='_wpnonce' value='" . wp_create_nonce('megamenu_edit') . "' />";
		$html .= "    <input type='hidden' name='menu_item_id' value='{$menu_item_id}' />";
		$html .= "    <input type='hidden' name='action' value='mm_save_menu_item_settings' />";
		$html .= "    <input type='hidden' name='clear_cache' value='true' />";
		$html .= "    <h4 class='first'>" . __("Styling", "megamenupro") . "</h4>";
		$html .= "    <p class='tab-description'>" . __("Apply custom styling to this menu item only. These values will override the styling set in the menu theme.", "megamenu_pro") . "</p>";
		$html .= "    <table>";
		$html .= "        <tr class='mega-" . $this->get_setting_state('menu_item_background_from', $menu_item_meta) . "'>";
		$html .= "            <td class='mega-enable'>" . $this->print_theme_option_enabled( 'menu_item_background_from', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta ) . "</td>";
		$html .= "            <td class='mega-name'>" . __("Background Color", "megamenupro") . "</td>";
		$html .= "            <td class='mega-value'>";
		$html .=                  $this->print_theme_color_option('menu_item_background_from', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta );
		$html .=                  $this->print_theme_color_option('menu_item_background_to', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta );
		$html .= "            </td>";
		$html .= "        </tr>";
		$html .= "        <tr class='mega-" . $this->get_setting_state('menu_item_background_hover_from', $menu_item_meta) . "'>";
		$html .= "            <td class='mega-enable'>" . $this->print_theme_option_enabled( 'menu_item_background_hover_from', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta ) . "</td>";
		$html .= "            <td class='mega-name'>" . __("Background Color (Hover)", "megamenupro") . "</td>";
		$html .= "            <td class='mega-value'>";
		$html .=                  $this->print_theme_color_option('menu_item_background_hover_from', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta );
		$html .=                  $this->print_theme_color_option('menu_item_background_hover_to', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta );
		$html .= "            </td>";
		$html .= "        </tr>";
		$html .= "        <tr class='mega-" . $this->get_setting_state('menu_item_link_color', $menu_item_meta) . "'>";
		$html .= "            <td class='mega-enable'>" . $this->print_theme_option_enabled( 'menu_item_link_color', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta ) . "</td>";
		$html .= "            <td class='mega-name'>" . __("Font Color", "megamenupro") . "</td>";
		$html .= "            <td class='mega-value'>";
		$html .=                  $this->print_theme_color_option('menu_item_link_color', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta );
		$html .= "            </td>";
		$html .= "        </tr>";
		$html .= "        <tr class='mega-" . $this->get_setting_state('menu_item_link_color_hover', $menu_item_meta) . "'>";
		$html .= "            <td class='mega-enable'>" . $this->print_theme_option_enabled( 'menu_item_link_color_hover', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta ) . "</td>";
		$html .= "            <td class='mega-name'>" . __("Font Color (Hover)", "megamenupro") . "</td>";
		$html .= "            <td class='mega-value'>";
		$html .=                  $this->print_theme_color_option('menu_item_link_color_hover', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta );
		$html .= "            </td>";
		$html .= "        </tr>";

		$html .= "        <tr class='mega-" . $this->get_setting_state('menu_item_link_weight', $menu_item_meta) . "'>";
		$html .= "            <td class='mega-enable'>" . $this->print_theme_option_enabled( 'menu_item_link_weight', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta ) . "</td>";
		$html .= "            <td class='mega-name'>" . __("Font Weight", "megamenupro") . "</td>";
		$html .= "            <td class='mega-value'>";
		$html .=                  $this->print_theme_text_weight_option('menu_item_link_weight', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta );
		$html .= "            </td>";
		$html .= "        </tr>";
		$html .= "        <tr class='mega-" . $this->get_setting_state('menu_item_link_weight_hover', $menu_item_meta) . "'>";
		$html .= "            <td class='mega-enable'>" . $this->print_theme_option_enabled( 'menu_item_link_weight_hover', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta ) . "</td>";
		$html .= "            <td class='mega-name'>" . __("Font Weight (Hover)", "megamenupro") . "</td>";
		$html .= "            <td class='mega-value'>";
		$html .=                  $this->print_theme_text_weight_option('menu_item_link_weight_hover', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta );
		$html .= "            </td>";
		$html .= "        </tr>";
		$html .= "        <tr class='mega-" . $this->get_setting_state('menu_item_font_size', $menu_item_meta) . "'>";
		$html .= "            <td class='mega-enable'>" . $this->print_theme_option_enabled( 'menu_item_font_size', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta ) . "</td>";
		$html .= "            <td class='mega-name'>" . __("Font Size", "megamenupro") . "</td>";
		$html .= "            <td class='mega-value'>";
		$html .=                  $this->print_theme_freetext_option('menu_item_font_size', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta );
		$html .= "            </td>";
		$html .= "        </tr>";
		$html .= "        <tr class='mega-" . $this->get_setting_state('menu_item_link_text_align', $menu_item_meta) . "'>";
		$html .= "            <td class='mega-enable'>" . $this->print_theme_option_enabled( 'menu_item_link_text_align', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta ) . "</td>";
		$html .= "            <td class='mega-name'>" . __("Text Align", "megamenupro") . "</td>";
		$html .= "            <td class='mega-value'>";
		$html .=                  $this->print_theme_align_option('menu_item_link_text_align', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta );
		$html .= "            </td>";
		$html .= "        </tr>";
		$html .= "        <tr class='mega-" . $this->get_setting_state('menu_item_link_text_transform', $menu_item_meta) . "'>";
		$html .= "            <td class='mega-enable'>" . $this->print_theme_option_enabled( 'menu_item_link_text_transform', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta ) . "</td>";
		$html .= "            <td class='mega-name'>" . __("Text Transform", "megamenupro") . "</td>";
		$html .= "            <td class='mega-value'>";
		$html .=                  $this->print_theme_text_transform_option('menu_item_link_text_transform', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta );
		$html .= "            </td>";
		$html .= "        </tr>";
		$html .= "        <tr class='mega-" . $this->get_setting_state('menu_item_link_text_decoration', $menu_item_meta) . "'>";
		$html .= "            <td class='mega-enable'>" . $this->print_theme_option_enabled( 'menu_item_link_text_decoration', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta ) . "</td>";
		$html .= "            <td class='mega-name'>" . __("Text Decoration", "megamenupro") . "</td>";
		$html .= "            <td class='mega-value'>";
		$html .=                  $this->print_theme_text_decoration_option('menu_item_link_text_decoration', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta );
		$html .= "            </td>";
		$html .= "        </tr>";
		$html .= "        <tr class='mega-" . $this->get_setting_state('menu_item_link_text_decoration_hover', $menu_item_meta) . "'>";
		$html .= "            <td class='mega-enable'>" . $this->print_theme_option_enabled( 'menu_item_link_text_decoration_hover', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta ) . "</td>";
		$html .= "            <td class='mega-name'>" . __("Text Decoration (Hover)", "megamenupro") . "</td>";
		$html .= "            <td class='mega-value'>";
		$html .=                  $this->print_theme_text_decoration_option('menu_item_link_text_decoration_hover', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta );
		$html .= "            </td>";
		$html .= "        </tr>";
		$html .= "        <tr class='mega-" . $this->get_setting_state('menu_item_border_color', $menu_item_meta) . "'>";
		$html .= "            <td class='mega-enable'>" . $this->print_theme_option_enabled( 'menu_item_border_color', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta ) . "</td>";
		$html .= "            <td class='mega-name'>" . __("Border Color", "megamenupro") . "</td>";
		$html .= "            <td class='mega-value'>";
		$html .=                  $this->print_theme_color_option('menu_item_border_color', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta );
		$html .= "            </td>";
		$html .= "        </tr>";
		$html .= "        <tr class='mega-" . $this->get_setting_state('menu_item_border_color_hover', $menu_item_meta) . "'>";
		$html .= "            <td class='mega-enable'>" . $this->print_theme_option_enabled( 'menu_item_border_color_hover', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta ) . "</td>";
		$html .= "            <td class='mega-name'>" . __("Border Color (Hover)", "megamenupro") . "</td>";
		$html .= "            <td class='mega-value'>";
		$html .=                  $this->print_theme_color_option('menu_item_border_color_hover', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta );
		$html .= "            </td>";
		$html .= "        </tr>";
		$html .= "        <tr class='mega-" . $this->get_setting_state('menu_item_border_top', $menu_item_meta) . "'>";
		$html .= "            <td class='mega-enable'>" . $this->print_theme_option_enabled( 'menu_item_border_top', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta ) . "</td>";
		$html .= "            <td class='mega-name'>" . __("Border Width (Top)", "megamenupro") . "</td>";
		$html .= "            <td class='mega-value'>";
		$html .=                  $this->print_theme_freetext_option('menu_item_border_top', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta );
		$html .= "            </td>";
		$html .= "        </tr>";
		$html .= "        <tr class='mega-" . $this->get_setting_state('menu_item_border_right', $menu_item_meta) . "'>";
		$html .= "            <td class='mega-enable'>" . $this->print_theme_option_enabled( 'menu_item_border_right', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta ) . "</td>";
		$html .= "            <td class='mega-name'>" . __("Border Width (Right)", "megamenupro") . "</td>";
		$html .= "            <td class='mega-value'>";
		$html .=                  $this->print_theme_freetext_option('menu_item_border_right', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta );
		$html .= "            </td>";
		$html .= "        </tr>";
		$html .= "        <tr class='mega-" . $this->get_setting_state('menu_item_border_bottom', $menu_item_meta) . "'>";
		$html .= "            <td class='mega-enable'>" . $this->print_theme_option_enabled( 'menu_item_border_bottom', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta ) . "</td>";
		$html .= "            <td class='mega-name'>" . __("Border Width (Bottom)", "megamenupro") . "</td>";
		$html .= "            <td class='mega-value'>";
		$html .=                  $this->print_theme_freetext_option('menu_item_border_bottom', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta );
		$html .= "            </td>";
		$html .= "        </tr>";
		$html .= "        <tr class='mega-" . $this->get_setting_state('menu_item_border_left', $menu_item_meta) . "'>";
		$html .= "            <td class='mega-enable'>" . $this->print_theme_option_enabled( 'menu_item_border_left', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta ) . "</td>";
		$html .= "            <td class='mega-name'>" . __("Border Width (Left)", "megamenupro") . "</td>";
		$html .= "            <td class='mega-value'>";
		$html .=                  $this->print_theme_freetext_option('menu_item_border_left', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta );
		$html .= "            </td>";
		$html .= "        </tr>";
		$html .= "        <tr class='mega-" . $this->get_setting_state('menu_item_border_radius_top_left', $menu_item_meta) . "'>";
		$html .= "            <td class='mega-enable'>" . $this->print_theme_option_enabled( 'menu_item_border_radius_top_left', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta ) . "</td>";
		$html .= "            <td class='mega-name'>" . __("Border Radius (Top Left)", "megamenupro") . "</td>";
		$html .= "            <td class='mega-value'>";
		$html .=                  $this->print_theme_freetext_option('menu_item_border_radius_top_left', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta );
		$html .= "            </td>";
		$html .= "        </tr>";
		$html .= "        <tr class='mega-" . $this->get_setting_state('menu_item_border_radius_top_right', $menu_item_meta) . "'>";
		$html .= "            <td class='mega-enable'>" . $this->print_theme_option_enabled( 'menu_item_border_radius_top_right', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta ) . "</td>";
		$html .= "            <td class='mega-name'>" . __("Border Radius (Top Right)", "megamenupro") . "</td>";
		$html .= "            <td class='mega-value'>";
		$html .=                  $this->print_theme_freetext_option('menu_item_border_radius_top_right', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta );
		$html .= "            </td>";
		$html .= "        </tr>";
		$html .= "        <tr class='mega-" . $this->get_setting_state('menu_item_border_radius_bottom_right', $menu_item_meta) . "'>";
		$html .= "            <td class='mega-enable'>" . $this->print_theme_option_enabled( 'menu_item_border_radius_bottom_right', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta ) . "</td>";
		$html .= "            <td class='mega-name'>" . __("Border Radius (Bottom Right)", "megamenupro") . "</td>";
		$html .= "            <td class='mega-value'>";
		$html .=                  $this->print_theme_freetext_option('menu_item_border_radius_bottom_right', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta );
		$html .= "            </td>";
		$html .= "        </tr>";
		$html .= "        <tr class='mega-" . $this->get_setting_state('menu_item_border_radius_bottom_left', $menu_item_meta) . "'>";
		$html .= "            <td class='mega-enable'>" . $this->print_theme_option_enabled( 'menu_item_border_radius_bottom_left', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta ) . "</td>";
		$html .= "            <td class='mega-name'>" . __("Border Radius (Bottom Left)", "megamenupro") . "</td>";
		$html .= "            <td class='mega-value'>";
		$html .=                  $this->print_theme_freetext_option('menu_item_border_radius_bottom_left', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta );
		$html .= "            </td>";
		$html .= "        </tr>";
		$html .= "        <tr class='mega-" . $this->get_setting_state('menu_item_icon_size', $menu_item_meta) . "'>";
		$html .= "            <td class='mega-enable'>" . $this->print_theme_option_enabled( 'menu_item_icon_size', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta ) . "</td>";
		$html .= "            <td class='mega-name'>" . __("Icon Size", "megamenupro") . "</td>";
		$html .= "            <td class='mega-value'>";
		$html .=                  $this->print_theme_freetext_option('menu_item_icon_size', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta );
		$html .= "            </td>";
		$html .= "        </tr>";
		$html .= "        <tr class='mega-" . $this->get_setting_state('menu_item_icon_color', $menu_item_meta) . "'>";
		$html .= "            <td class='mega-enable'>" . $this->print_theme_option_enabled( 'menu_item_icon_color', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta ) . "</td>";
		$html .= "            <td class='mega-name'>" . __("Icon Color", "megamenupro") . "</td>";
		$html .= "            <td class='mega-value'>";
		$html .=                  $this->print_theme_color_option('menu_item_icon_color', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta );
		$html .= "            </td>";
		$html .= "        </tr>";
		$html .= "        <tr class='mega-" . $this->get_setting_state('menu_item_icon_color_hover', $menu_item_meta) . "'>";
		$html .= "            <td class='mega-enable'>" . $this->print_theme_option_enabled( 'menu_item_icon_color_hover', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta ) . "</td>";
		$html .= "            <td class='mega-name'>" . __("Icon Color (Hover)", "megamenupro") . "</td>";
		$html .= "            <td class='mega-value'>";
		$html .=                  $this->print_theme_color_option('menu_item_icon_color_hover', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta );
		$html .= "            </td>";
		$html .= "        </tr>";
		$html .= "        <tr class='mega-" . $this->get_setting_state('menu_item_padding_left', $menu_item_meta) . "'>";
		$html .= "            <td class='mega-enable'>" . $this->print_theme_option_enabled( 'menu_item_padding_left', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta ) . "</td>";
		$html .= "            <td class='mega-name'>" . __("Menu Item Padding (Left)", "megamenupro") . "</td>";
		$html .= "            <td class='mega-value'>";
		$html .=                  $this->print_theme_freetext_option('menu_item_padding_left', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta );
		$html .= "            </td>";
		$html .= "        </tr>";
		$html .= "        <tr class='mega-" . $this->get_setting_state('menu_item_padding_right', $menu_item_meta) . "'>";
		$html .= "            <td class='mega-enable'>" . $this->print_theme_option_enabled( 'menu_item_padding_right', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta ) . "</td>";
		$html .= "            <td class='mega-name'>" . __("Menu Item Padding (Right)", "megamenupro") . "</td>";
		$html .= "            <td class='mega-value'>";
		$html .=                  $this->print_theme_freetext_option('menu_item_padding_right', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta );
		$html .= "            </td>";
		$html .= "        </tr>";
		$html .= "        <tr class='mega-" . $this->get_setting_state('menu_item_padding_top', $menu_item_meta) . "'>";
		$html .= "            <td class='mega-enable'>" . $this->print_theme_option_enabled( 'menu_item_padding_top', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta ) . "</td>";
		$html .= "            <td class='mega-name'>" . __("Menu Item Padding (Top)", "megamenupro") . "</td>";
		$html .= "            <td class='mega-value'>";
		$html .=                  $this->print_theme_freetext_option('menu_item_padding_top', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta );
		$html .= "                 <small><em>Not recommended for top level menu items</em></small>";
		$html .= "            </td>";
		$html .= "        </tr>";
		$html .= "        <tr class='mega-" . $this->get_setting_state('menu_item_padding_bottom', $menu_item_meta) . "'>";
		$html .= "            <td class='mega-enable'>" . $this->print_theme_option_enabled( 'menu_item_padding_bottom', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta ) . "</td>";
		$html .= "            <td class='mega-name'>" . __("Menu Item Padding (Bottom)", "megamenupro") . "</td>";
		$html .= "            <td class='mega-value'>";
		$html .=                  $this->print_theme_freetext_option('menu_item_padding_bottom', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta );
		$html .= "                 <small><em>Not recommended for top level menu items</em></small>";
		$html .= "            </td>";
		$html .= "        </tr>";
		$html .= "        <tr class='mega-" . $this->get_setting_state('menu_item_margin_left', $menu_item_meta) . "'>";
		$html .= "            <td class='mega-enable'>" . $this->print_theme_option_enabled( 'menu_item_margin_left', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta ) . "</td>";
		$html .= "            <td class='mega-name'>" . __("Menu Item Margin (Left)", "megamenupro") . "</td>";
		$html .= "            <td class='mega-value'>";
		$html .=                  $this->print_theme_freetext_option('menu_item_margin_left', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta );
		$html .= "            </td>";
		$html .= "        </tr>";
		$html .= "        <tr class='mega-" . $this->get_setting_state('menu_item_margin_right', $menu_item_meta) . "'>";
		$html .= "            <td class='mega-enable'>" . $this->print_theme_option_enabled( 'menu_item_margin_right', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta ) . "</td>";
		$html .= "            <td class='mega-name'>" . __("Menu Item Margin (Right)", "megamenupro") . "</td>";
		$html .= "            <td class='mega-value'>";
		$html .=                  $this->print_theme_freetext_option('menu_item_margin_right', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta );
		$html .= "            </td>";
		$html .= "        </tr>";
		$html .= "        <tr class='mega-" . $this->get_setting_state('menu_item_margin_top', $menu_item_meta) . "'>";
		$html .= "            <td class='mega-enable'>" . $this->print_theme_option_enabled( 'menu_item_margin_top', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta ) . "</td>";
		$html .= "            <td class='mega-name'>" . __("Menu Item Margin (Top)", "megamenupro") . "</td>";
		$html .= "            <td class='mega-value'>";
		$html .=                  $this->print_theme_freetext_option('menu_item_margin_top', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta );
		$html .= "                 <small><em>Not recommended for top level menu items</em></small>";
		$html .= "            </td>";
		$html .= "        </tr>";
		$html .= "        <tr class='mega-" . $this->get_setting_state('menu_item_margin_bottom', $menu_item_meta) . "'>";
		$html .= "            <td class='mega-enable'>" . $this->print_theme_option_enabled( 'menu_item_margin_bottom', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta ) . "</td>";
		$html .= "            <td class='mega-name'>" . __("Menu Item Margin (Bottom)", "megamenupro") . "</td>";
		$html .= "            <td class='mega-value'>";
		$html .=                  $this->print_theme_freetext_option('menu_item_margin_bottom', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta );
		$html .= "                 <small><em>Not recommended for top level menu items</em></small>";
		$html .= "            </td>";
		$html .= "        </tr>";
		$html .= "        <tr class='mega-" . $this->get_setting_state('menu_item_height', $menu_item_meta) . "'>";
		$html .= "            <td class='mega-enable'>" . $this->print_theme_option_enabled( 'menu_item_height', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta ) . "</td>";
		$html .= "            <td class='mega-name'>" . __("Menu Item Height", "megamenupro") . "</td>";
		$html .= "            <td class='mega-value'>";
		$html .=                  $this->print_theme_freetext_option('menu_item_height', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta );
		$html .= "            </td>";
		$html .= "        </tr>";
		$html .= "        <tr class='mega-" . $this->get_setting_state('panel_width', $menu_item_meta) . "'>";
		$html .= "            <td class='mega-enable'>" . $this->print_theme_option_enabled( 'panel_width', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta ) . "</td>";
		$html .= "            <td class='mega-name'>" . __("Sub Menu - Width", "megamenupro") . "</td>";
		$html .= "            <td class='mega-value'>";
		$html .=                  $this->print_theme_freetext_option('panel_width', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta );
		$html .= "            </td>";
		$html .= "        </tr>";
		$html .= "        <tr class='mega-" . $this->get_setting_state('panel_horizontal_offset', $menu_item_meta) . "'>";
		$html .= "            <td class='mega-enable'>" . $this->print_theme_option_enabled( 'panel_horizontal_offset', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta ) . "</td>";
		$html .= "            <td class='mega-name'>" . __("Sub Menu - Horizontal Offset", "megamenupro") . "</td>";
		$html .= "            <td class='mega-value'>";
		$html .=                  $this->print_theme_freetext_option('panel_horizontal_offset', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta );
		$html .= "            </td>";
		$html .= "        </tr>";
		$html .= "        <tr class='mega-" . $this->get_setting_state('panel_vertical_offset', $menu_item_meta) . "'>";
		$html .= "            <td class='mega-enable'>" . $this->print_theme_option_enabled( 'panel_vertical_offset', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta ) . "</td>";
		$html .= "            <td class='mega-name'>" . __("Sub Menu - Vertical Offset", "megamenupro") . "</td>";
		$html .= "            <td class='mega-value'>";
		$html .=                  $this->print_theme_freetext_option('panel_vertical_offset', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta );
		$html .= "            </td>";
		$html .= "        </tr>";
		$html .= "        <tr class='mega-" . $this->get_setting_state('panel_background_from', $menu_item_meta) . "'>";
		$html .= "            <td class='mega-enable'>" . $this->print_theme_option_enabled( 'panel_background_from', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta ) . "</td>";
		$html .= "            <td class='mega-name'>" . __("Sub Menu - Background Color", "megamenupro") . "</td>";
		$html .= "            <td class='mega-value'>";
		$html .=                  $this->print_theme_color_option('panel_background_from', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta );
		$html .=                  $this->print_theme_color_option('panel_background_to', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta );
		$html .= "            </td>";
		$html .= "        </tr>";
		$html .= "        <tr class='mega-" . $this->get_setting_state('panel_background_image', $menu_item_meta) . "'>";
		$html .= "            <td class='mega-enable'>" . $this->print_theme_option_enabled( 'panel_background_image', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta ) . "</td>";
		$html .= "            <td class='mega-name'>" . __("Sub Menu - Background Image", "megamenupro") . "</td>";
		$html .= "            <td class='mega-value'>";
		$html .=                  $this->print_theme_background_option('panel_background_image', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta );
		$html .= "            </td>";
		$html .= "        </tr>";
		$html .= "        <tr class='mega-" . $this->get_setting_state('panel_background_image_size', $menu_item_meta) . "'>";
		$html .= "            <td class='mega-enable'>" . $this->print_theme_option_enabled( 'panel_background_image_size', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta ) . "</td>";
		$html .= "            <td class='mega-name'>" . __("Sub Menu - Background Image Size", "megamenupro") . "</td>";
		$html .= "            <td class='mega-value'>";
		$html .=                  $this->print_theme_background_size_option('panel_background_image_size', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta );
		$html .= "            </td>";
		$html .= "        </tr>";
		$html .= "        <tr class='mega-" . $this->get_setting_state('panel_background_image_repeat', $menu_item_meta) . "'>";
		$html .= "            <td class='mega-enable'>" . $this->print_theme_option_enabled( 'panel_background_image_repeat', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta ) . "</td>";
		$html .= "            <td class='mega-name'>" . __("Sub Menu - Background Image Repeat", "megamenupro") . "</td>";
		$html .= "            <td class='mega-value'>";
		$html .=                  $this->print_theme_background_repeat_option('panel_background_image_repeat', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta );
		$html .= "            </td>";
		$html .= "        </tr>";
		$html .= "        <tr class='mega-" . $this->get_setting_state('panel_background_image_position', $menu_item_meta) . "'>";
		$html .= "            <td class='mega-enable'>" . $this->print_theme_option_enabled( 'panel_background_image_position', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta ) . "</td>";
		$html .= "            <td class='mega-name'>" . __("Sub Menu - Background Image Position", "megamenupro") . "</td>";
		$html .= "            <td class='mega-value'>";
		$html .=                  $this->print_theme_background_position_option('panel_background_image_position', $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta );
		$html .= "            </td>";
		$html .= "        </tr>";
		$html .= "    </table>";
		$html .= get_submit_button();
		$html .= "</form>";

		$tabs['styling'] = array(
			'title' => __("Styling", "megamenupro"),
			'content' => $html
		);

		return $tabs;
	}


	/**
	 * Returns enabled or disabled depending on the setting state
	 *
	 * @since 1.0
	 * @param string $key
	 * @param array $menu_item_meta
	 * @return string
	 */
	public function get_setting_state( $key, $menu_item_meta ) {

		return isset( $menu_item_meta['styles']['enabled'][$key] ) ? 'enabled' : 'disabled';

	}


	/**
	 * Return the HTML for the 'enabled' checkbox
	 *
	 * @since 1.0
	 * @param string $key
	 * @param int $menu_item_id
	 * @param int $menu_id
	 * @param int $menu_item_depth
	 * @param array $menu_item_meta
	 * @return string
	 */
	public function print_theme_option_enabled( $key, $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta ) {

		$html = "<input type='checkbox' class='override_toggle_enabled' " . checked($this->get_setting_state($key, $menu_item_meta), 'enabled', false) . " />";

		return $html;

	}


	/**
	 * Return the HTML for a textarea option
	 *
	 * @since 1.0
	 * @param string $key
	 * @param int $menu_item_id
	 * @param int $menu_id
	 * @param int $menu_item_depth
	 * @param array $menu_item_meta
	 * @return string
	 */
	public function print_theme_freetext_option( $key, $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta ) {

		$enabled = $this->get_setting_state( $key, $menu_item_meta );

		$value = isset( $menu_item_meta['styles'][$enabled][$key] ) ? $menu_item_meta['styles'][$enabled][$key] : "0px";

		$html  = "<input type='text' name='settings[styles][{$enabled}][{$key}]' value='{$value}' />";

		return $html;

	}


	/**
	 * Return the HTML for a background size select
	 *
	 * @since 1.3.6
	 * @param string $key
	 * @param int $menu_item_id
	 * @param int $menu_id
	 * @param int $menu_item_depth
	 * @param array $menu_item_meta
	 * @return string
	 */
	public function print_theme_background_size_option( $key, $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta ) {

		$enabled = $this->get_setting_state( $key, $menu_item_meta );

		$value = isset( $menu_item_meta['styles'][$enabled][$key] ) ? $menu_item_meta['styles'][$enabled][$key] : "auto";

		$html  = "<select name='settings[styles][{$enabled}][{$key}]' >";
		$html .= "    <option value='auto' " . selected( $value, 'auto', false ) . ">" . __("Auto", "megamenupro") . "</option>";
		$html .= "    <option value='cover' " . selected( $value, 'cover', false ) . ">" . __("Cover", "megamenupro") . "</option>";
		$html .= "    <option value='contain' " . selected( $value, 'contain', false ) . ">" . __("Contain", "megamenupro") . "</option>";
		$html .= "</select>";

		return $html;

	}


	/**
	 * Return the HTML for a text transform selector
	 *
	 * @since 1.4.3
	 * @param string $key
	 * @param int $menu_item_id
	 * @param int $menu_id
	 * @param int $menu_item_depth
	 * @param array $menu_item_meta
	 * @return string
	 */
	public function print_theme_text_transform_option( $key, $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta ) {

		$enabled = $this->get_setting_state( $key, $menu_item_meta );

		$value = isset( $menu_item_meta['styles'][$enabled][$key] ) ? $menu_item_meta['styles'][$enabled][$key] : "none";

        $html  = "<select name='settings[styles][{$enabled}][{$key}]'>";
        $html .= "    <option value='none' "      . selected( $value, 'none', false ) . ">" . __("Normal", "megamenu") . "</option>";
        $html .= "    <option value='capitalize'" . selected( $value, 'capitalize', false ) . ">" . __("Capitalize", "megamenu") . "</option>";
        $html .= "    <option value='uppercase'"  . selected( $value, 'uppercase', false ) . ">" . __("Uppercase", "megamenu") . "</option>";
        $html .= "    <option value='lowercase'"  . selected( $value, 'lowercase', false ) . ">" . __("Lowercase", "megamenu") . "</option>";
        $html .= "</select>";

		return $html;

	}

	/**
	 * Return the HTML for a text align selector
	 *
	 * @since 1.6.2.2
	 * @param string $key
	 * @param int $menu_item_id
	 * @param int $menu_id
	 * @param int $menu_item_depth
	 * @param array $menu_item_meta
	 * @return string
	 */
    public function print_theme_align_option( $key, $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta ) {

		$enabled = $this->get_setting_state( $key, $menu_item_meta );

		$value = isset( $menu_item_meta['styles'][$enabled][$key] ) ? $menu_item_meta['styles'][$enabled][$key] : "none";

        $html  = "<select name='settings[styles][{$enabled}][{$key}]'>";
        $html .= "    <option value='left' "  . selected( $value, 'left', false ) . ">" . __("Left", "megamenu") . "</option>";
        $html .= "    <option value='center'" . selected( $value, 'center', false ) . ">" . __("Center", "megamenu") . "</option>";
        $html .= "    <option value='right'"  . selected( $value, 'right', false ) . ">" . __("Right", "megamenu") . "</option>";
        $html .= "</select>";

        return $html;

    }

	/**
	 * Return the HTML for a text decoration selector
	 *
	 * @since 1.6.1
	 * @param string $key
	 * @param int $menu_item_id
	 * @param int $menu_id
	 * @param int $menu_item_depth
	 * @param array $menu_item_meta
	 * @return string
	 */
	public function print_theme_text_decoration_option( $key, $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta ) {

		$enabled = $this->get_setting_state( $key, $menu_item_meta );

		$value = isset( $menu_item_meta['styles'][$enabled][$key] ) ? $menu_item_meta['styles'][$enabled][$key] : "none";

        $html  = "<select name='settings[styles][{$enabled}][{$key}]'>";
        $html .= "    <option value='none' "      . selected( $value, 'none', false ) . ">" . __("None", "megamenu") . "</option>";
        $html .= "    <option value='underline' " . selected( $value, 'underline', false ) . ">" . __("Underline", "megamenu") . "</option>";
        $html .= "</select>";

		return $html;

	}

	/**
	 * Return the HTML for a font weight selector
	 *
	 * @since 1.4.3
	 * @param string $key
	 * @param int $menu_item_id
	 * @param int $menu_id
	 * @param int $menu_item_depth
	 * @param array $menu_item_meta
	 * @return string
	 */
	public function print_theme_text_weight_option( $key, $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta ) {

		$enabled = $this->get_setting_state( $key, $menu_item_meta );

		$value = isset( $menu_item_meta['styles'][$enabled][$key] ) ? $menu_item_meta['styles'][$enabled][$key] : "inherit";

        $options = apply_filters( "megamenu_font_weights", array(
            'inherit' => __("Theme Default", "megamenu"),
            '300' => __("Light (300)", "megamenu"),
            'normal' => __("Normal (400)", "megamenu"),
            'bold' => __("Bold (700)", "megamenu"),
        ) );

        /**
         *   '100' => __("Thin (100)", "megamenu"),
         *   '200' => __("Extra Light (200)", "megamenu"),
         *   '300' => __("Light (300)", "megamenu"),
         *   'normal' => __("Normal (400)", "megamenu"),
         *   '500' => __("Medium (500)", "megamenu"),
         *   '600' => __("Semi Bold (600)", "megamenu"),
         *   'bold' => __("Bold (700)", "megamenu"),
         *   '800' => __("Extra Bold (800)", "megamenu"),
         *   '900' => __("Black (900)", "megamenu")
        */

        $html = "<select name='settings[styles][{$enabled}][{$key}]'>";

        foreach ( $options as $weight => $name ) {
            $html .= "<option value='{$weight}' " . selected( $value, $weight, false ) . ">{$name}</option>";
        }

        $html .= "</select>";

		return $html;

	}


	/**
	 * Return the HTML for a background size select
	 *
	 * @since 1.3.6
	 * @param string $key
	 * @param int $menu_item_id
	 * @param int $menu_id
	 * @param int $menu_item_depth
	 * @param array $menu_item_meta
	 * @return string
	 */
	public function print_theme_background_position_option( $key, $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta ) {

		$enabled = $this->get_setting_state( $key, $menu_item_meta );

		$value = isset( $menu_item_meta['styles'][$enabled][$key] ) ? $menu_item_meta['styles'][$enabled][$key] : "auto";

		$html  = "<select name='settings[styles][{$enabled}][{$key}]' >";
		$html .= "    <option value='left top' " . selected( $value, 'left top', false ) . ">" . __("Left Top", "megamenupro") . "</option>";
		$html .= "    <option value='left center' " . selected( $value, 'left center', false ) . ">" . __("Left Center", "megamenupro") . "</option>";
		$html .= "    <option value='left bottom' " . selected( $value, 'left bottom', false ) . ">" . __("Left Bottom", "megamenupro") . "</option>";
		$html .= "    <option value='right top' " . selected( $value, 'right top', false ) . ">" . __("Right Top", "megamenupro") . "</option>";
		$html .= "    <option value='right center' " . selected( $value, 'right center', false ) . ">" . __("Right Center", "megamenupro") . "</option>";
		$html .= "    <option value='right bottom' " . selected( $value, 'right bottom', false ) . ">" . __("Right Bottom", "megamenupro") . "</option>";
		$html .= "    <option value='center top' " . selected( $value, 'center top', false ) . ">" . __("Center Top", "megamenupro") . "</option>";
		$html .= "    <option value='center center' " . selected( $value, 'center center', false ) . ">" . __("Center Center", "megamenupro") . "</option>";
		$html .= "    <option value='center bottom' " . selected( $value, 'center bottom', false ) . ">" . __("Center Bottom", "megamenupro") . "</option>";
		$html .= "</select>";

		return $html;

	}

	/**
	 * Return the HTML for a background repeat select
	 *
	 * @since 1.3.6
	 * @param string $key
	 * @param int $menu_item_id
	 * @param int $menu_id
	 * @param int $menu_item_depth
	 * @param array $menu_item_meta
	 * @return string
	 */
	public function print_theme_background_repeat_option( $key, $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta ) {

		$enabled = $this->get_setting_state( $key, $menu_item_meta );

		$value = isset( $menu_item_meta['styles'][$enabled][$key] ) ? $menu_item_meta['styles'][$enabled][$key] : "auto";

		$html  = "<select name='settings[styles][{$enabled}][{$key}]' >";
		$html .= "    <option value='no-repeat' " . selected( $value, 'no-repeat', false ) . ">" . __("No Repeat", "megamenupro") . "</option>";
		$html .= "    <option value='repeat' " . selected( $value, 'repeat', false ) . ">" . __("Repeat (Tiled)", "megamenupro") . "</option>";
		$html .= "    <option value='repeat-x' " . selected( $value, 'repeat-x', false ) . ">" . __("Repeat X (Horizontally)", "megamenupro") . "</option>";
		$html .= "    <option value='repeat-y' " . selected( $value, 'repeat-y', false ) . ">" . __("Repeat X (Vertically)", "megamenupro") . "</option>";
		$html .= "</select>";

		return $html;

	}

	/**
	 * Return the HTML for a background image select option
	 *
	 * @since 1.3.6
	 * @param string $key
	 * @param int $menu_item_id
	 * @param int $menu_id
	 * @param int $menu_item_depth
	 * @param array $menu_item_meta
	 * @return string
	 */
	public function print_theme_background_option( $key, $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta ) {

		$enabled = $this->get_setting_state( $key, $menu_item_meta );

		$icon_id = isset( $menu_item_meta['styles'][$enabled][$key] ) ? $menu_item_meta['styles'][$enabled][$key] : "0";

		$icon_url = "";

		if ( $icon_id > 0 ) {

			$icon = wp_get_attachment_image_src( $icon_id, 'thumbnail' );
			$icon_url = $icon[0];

		}

		$html = "<div class='mmm_image_selector' data-src='{$icon_url}' data-field='custom_background_id'></div>";
		$html .= "<input type='hidden' id='custom_background_id' name='settings[styles][{$enabled}][{$key}]' value='{$icon_id}' />";

		return $html;

	}


    /**
	 * Return the HTML for a color picker
	 *
	 * @since 1.0
	 * @param string $key
	 * @param int $menu_item_id
	 * @param int $menu_id
	 * @param int $menu_item_depth
	 * @param array $menu_item_meta
	 * @return string
	 */
    public function print_theme_color_option( $key, $menu_item_id, $menu_id, $menu_item_depth, $menu_item_meta ) {

		$enabled = isset( $menu_item_meta['styles']['enabled'][$key] ) ? 'enabled' : 'disabled';
		$value = isset( $menu_item_meta['styles'][$enabled][$key] ) ? $menu_item_meta['styles'][$enabled][$key] : '#333';

        if ( $value == 'transparent' ) {
            $value = 'rgba(0,0,0,0)';
        }

        if ( $value == 'rgba(0,0,0,0)' ) {
            $value_text = 'transparent';
        } else {
            $value_text = $value;
        }

        $html  = "<div class='mm-picker-container'>";
        $html .= "    <input type='text' class='mm_colorpicker' name='settings[styles][$enabled][$key]' value='{$value}' />";
        $html .= "    <div class='chosen-color'>{$value_text}</div>";
        $html .= "</div>";

		return $html;

    }


	/**
	 * Modify the list of SASS variables to include a list (of variables) for each menu item with custom styling applied
	 *
	 * @since 1.0
	 * @param array $vars
	 * @param string $location
	 * @param string $theme
	 * @param int $menu_id
	 * @return string
	 */
	public function add_style_overrides( $vars, $location, $theme, $menu_id ) {

		$menu_items = wp_get_nav_menu_items( $menu_id );

		$custom_vars = array();

		if ( is_array( $menu_items ) ) {

			foreach ( $menu_items as $menu_order => $item ) {

				if ( $settings = get_post_meta($item->ID, "_megamenu", true ) ) {

					if ( isset( $settings['styles']['enabled'] ) ) {

						$styles = array(
							'id' => $item->ID,
							'panel_width' => isset($settings['styles']['enabled']['panel_width']) ? $settings['styles']['enabled']['panel_width'] : 'disabled',
							'menu_item_background_from' => isset($settings['styles']['enabled']['menu_item_background_from']) ? $settings['styles']['enabled']['menu_item_background_from'] : 'disabled',
							'menu_item_background_to' => isset($settings['styles']['enabled']['menu_item_background_to']) ? $settings['styles']['enabled']['menu_item_background_to'] : 'disabled',
							'menu_item_background_hover_from' => isset($settings['styles']['enabled']['menu_item_background_hover_from']) ? $settings['styles']['enabled']['menu_item_background_hover_from'] : 'disabled',
							'menu_item_background_hover_to' => isset($settings['styles']['enabled']['menu_item_background_hover_to']) ? $settings['styles']['enabled']['menu_item_background_hover_to'] : 'disabled',
							'menu_item_link_color' => isset($settings['styles']['enabled']['menu_item_link_color']) ? $settings['styles']['enabled']['menu_item_link_color'] : 'disabled',
							'menu_item_link_color_hover' => isset($settings['styles']['enabled']['menu_item_link_color_hover']) ? $settings['styles']['enabled']['menu_item_link_color_hover'] : 'disabled',
							'menu_item_link_font_size' => isset($settings['styles']['enabled']['menu_item_font_size']) ? $settings['styles']['enabled']['menu_item_font_size'] : 'disabled',
							'menu_item_link_icon_size' => isset($settings['styles']['enabled']['menu_item_icon_size']) ? $settings['styles']['enabled']['menu_item_icon_size'] : 'disabled',
							'menu_item_link_padding_left' => isset($settings['styles']['enabled']['menu_item_padding_left']) ? $settings['styles']['enabled']['menu_item_padding_left'] : 'disabled',
							'menu_item_link_padding_right' => isset($settings['styles']['enabled']['menu_item_padding_right']) ? $settings['styles']['enabled']['menu_item_padding_right'] : 'disabled',
							'menu_item_margin_left' => isset($settings['styles']['enabled']['menu_item_margin_left']) ? $settings['styles']['enabled']['menu_item_margin_left'] : 'disabled',
							'menu_item_margin_right' => isset($settings['styles']['enabled']['menu_item_margin_right']) ? $settings['styles']['enabled']['menu_item_margin_right'] : 'disabled',
							'menu_item_border_color' => isset($settings['styles']['enabled']['menu_item_border_color']) ? $settings['styles']['enabled']['menu_item_border_color'] : 'disabled',
							'menu_item_border_color_hover' => isset($settings['styles']['enabled']['menu_item_border_color_hover']) ? $settings['styles']['enabled']['menu_item_border_color_hover'] : 'disabled',
							'menu_item_border_top' => isset($settings['styles']['enabled']['menu_item_border_top']) ? $settings['styles']['enabled']['menu_item_border_top'] : 'disabled',
							'menu_item_border_right' => isset($settings['styles']['enabled']['menu_item_border_right']) ? $settings['styles']['enabled']['menu_item_border_right'] : 'disabled',
							'menu_item_border_bottom' => isset($settings['styles']['enabled']['menu_item_border_bottom']) ? $settings['styles']['enabled']['menu_item_border_bottom'] : 'disabled',
							'menu_item_border_left' => isset($settings['styles']['enabled']['menu_item_border_left']) ? $settings['styles']['enabled']['menu_item_border_left'] : 'disabled',
							'menu_item_border_radius_top_left' => isset($settings['styles']['enabled']['menu_item_border_radius_top_left']) ? $settings['styles']['enabled']['menu_item_border_radius_top_left'] : 'disabled',
							'menu_item_border_radius_top_right' => isset($settings['styles']['enabled']['menu_item_border_radius_top_right']) ? $settings['styles']['enabled']['menu_item_border_radius_top_right'] : 'disabled',
							'menu_item_border_radius_bottom_right' => isset($settings['styles']['enabled']['menu_item_border_radius_bottom_right']) ? $settings['styles']['enabled']['menu_item_border_radius_bottom_right'] : 'disabled',
							'menu_item_border_radius_bottom_left' => isset($settings['styles']['enabled']['menu_item_border_radius_bottom_left']) ? $settings['styles']['enabled']['menu_item_border_radius_bottom_left'] : 'disabled',
							'panel_background_image' => isset($settings['styles']['enabled']['panel_background_image']) ? "'" . wp_get_attachment_url($settings['styles']['enabled']['panel_background_image']) . "'" : 'disabled',
							'panel_background_image_size' => isset($settings['styles']['enabled']['panel_background_image_size']) ? $settings['styles']['enabled']['panel_background_image_size'] : 'disabled',
							'panel_background_image_repeat' => isset($settings['styles']['enabled']['panel_background_image_repeat']) ? $settings['styles']['enabled']['panel_background_image_repeat'] : 'disabled',
							'panel_background_image_position' => isset($settings['styles']['enabled']['panel_background_image_position']) ? $settings['styles']['enabled']['panel_background_image_position'] : 'disabled',
							'panel_background_from' => isset($settings['styles']['enabled']['panel_background_from']) ? $settings['styles']['enabled']['panel_background_from'] : 'disabled',
							'panel_background_to' => isset($settings['styles']['enabled']['panel_background_to']) ? $settings['styles']['enabled']['panel_background_to'] : 'disabled',
							'panel_horizontal_offset' => isset($settings['styles']['enabled']['panel_horizontal_offset']) ? $settings['styles']['enabled']['panel_horizontal_offset'] : 'disabled',
							'panel_vertical_offset' => isset($settings['styles']['enabled']['panel_vertical_offset']) ? $settings['styles']['enabled']['panel_vertical_offset'] : 'disabled',
							'menu_item_link_text_transform' => isset($settings['styles']['enabled']['menu_item_link_text_transform']) ? $settings['styles']['enabled']['menu_item_link_text_transform'] : 'disabled',
							'menu_item_link_weight' => isset($settings['styles']['enabled']['menu_item_link_weight']) ? $settings['styles']['enabled']['menu_item_link_weight'] : 'disabled',
							'menu_item_icon_color' => isset($settings['styles']['enabled']['menu_item_icon_color']) ? $settings['styles']['enabled']['menu_item_icon_color'] : 'disabled',
							'menu_item_icon_color_hover' => isset($settings['styles']['enabled']['menu_item_icon_color_hover']) ? $settings['styles']['enabled']['menu_item_icon_color_hover'] : 'disabled',
							'menu_item_link_padding_top' => isset($settings['styles']['enabled']['menu_item_padding_top']) ? $settings['styles']['enabled']['menu_item_padding_top'] : 'disabled',
							'menu_item_link_padding_bottom' => isset($settings['styles']['enabled']['menu_item_padding_bottom']) ? $settings['styles']['enabled']['menu_item_padding_bottom'] : 'disabled',
							'menu_item_margin_top' => isset($settings['styles']['enabled']['menu_item_margin_top']) ? $settings['styles']['enabled']['menu_item_margin_top'] : 'disabled',
							'menu_item_margin_bottom' => isset($settings['styles']['enabled']['menu_item_margin_bottom']) ? $settings['styles']['enabled']['menu_item_margin_bottom'] : 'disabled',
							'menu_item_height' => isset($settings['styles']['enabled']['menu_item_height']) ? $settings['styles']['enabled']['menu_item_height'] : 'disabled',
							'menu_item_link_text_decoration' => isset($settings['styles']['enabled']['menu_item_link_text_decoration']) ? $settings['styles']['enabled']['menu_item_link_text_decoration'] : 'disabled',
							'menu_item_link_text_decoration_hover' => isset($settings['styles']['enabled']['menu_item_link_text_decoration_hover']) ? $settings['styles']['enabled']['menu_item_link_text_decoration_hover'] : 'disabled',
							'menu_item_link_weight_hover' => isset($settings['styles']['enabled']['menu_item_link_weight_hover']) ? $settings['styles']['enabled']['menu_item_link_weight_hover'] : 'disabled',
							'menu_item_link_text_align' => isset($settings['styles']['enabled']['menu_item_link_text_align']) ? $settings['styles']['enabled']['menu_item_link_text_align'] : 'disabled',
						);

						$custom_vars[ $item->ID ] = $styles;

					}

				}

			}

		}

		//$custom_styles:(
		// (123, red, 150px),
	    // (456, green, null),
		// (789, blue, 90%),());

		if ( count( $custom_vars ) ) {

			$list = "(";

			foreach ( $custom_vars as $id => $vals ) {
				$list .= "(" . implode( ",", $vals ) . "),";
			}

			// Always add an empty list item to meke sure there are always at least 2 items in the list
			// Lists with a single item are not treated the same way by SASS
			$list .= "());";

			$vars['style_overrides'] = $list;

		} else {

			$vars['style_overrides'] = "()";

		}


		return $vars;

	}


}

endif;