<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // disable direct access
}

if ( ! class_exists('Mega_Menu_Vertical') ) :

/**
 *
 */
class Mega_Menu_Vertical {

	/**
	 * Constructor
	 *
	 * @since 1.1
	 */
	public function __construct() {

        add_action( 'megamenu_settings_table', array( $this, 'add_orientation_setting'), 10, 2);
		add_filter( 'megamenu_load_scss_file_contents', array( $this, 'append_scss'), 10 );
		add_filter( 'megamenu_nav_menu_args', array( $this, 'apply_vertical_class'), 10, 3 );
        add_filter( 'megamenu_wrap_attributes', array( $this, 'apply_accordion_attributes'), 9, 5);
        add_filter( 'megamenu_nav_menu_css_class', array( $this, 'accordion_keep_parents_open' ), 10, 3 );

	}


    /**
     * Disable auto closing of the menu when clicking somewhere on the document
     *
     * @since 1.3.7
     * @param array $attributes
     * @param int $menu_id
     * @param array $menu_settings
     * @param array $settings
     * @param string $current_theme_location
     * @return array
     */
    public function apply_accordion_attributes( $attributes, $menu_id, $menu_settings, $settings, $current_theme_location ) {

        if ( isset( $menu_settings['orientation'] ) && $menu_settings['orientation'] == 'accordion' ) {
            $attributes['data-document-click'] = 'disabled';
            $attributes['data-vertical-behaviour'] = isset ( $settings['mobile_behaviour'] ) ? $settings['mobile_behaviour'] : 'accordion';
        }

        return $attributes;
    }


    /**
     * Apply mega-toggle-on class to ancestors of the current menu item
     *
     * @since 1.3.9
     * @param array $classes
     * @param object $item
     * @param array $args
     * @return array
     */
    public function accordion_keep_parents_open( $classes, $item, $args ) {

        if ( is_object( $args ) && strpos( $args->menu_class, 'mega-menu-accordion' ) !== false ) {

            $settings = get_option('megamenu_settings');

            $location = $args->theme_location;

            if ( isset( $settings[$location]['accordion_behaviour'] ) && $settings[$location]['accordion_behaviour'] == 'open_parents' || ! isset( $settings[$location]['accordion_behaviour'] ) ) {

                if ( in_array( 'mega-menu-item-has-children', $classes ) ) {
                    $needles = apply_filters('megamenu_accordion_parent_classes', array(
                        'mega-current_page_ancestor',
                        'mega-current_page_item',
                        'mega-current-menu-ancestor',
                        'mega-current-menu-item',
                        'mega-current-menu-parent'
                    ));

                    if ( ! empty( array_intersect( $needles, $classes ) ) ) {
                        $classes[] = 'mega-toggle-on';
                    }
                } 
            }

        }

        return $classes;
    }
    

	/**
	 * Change the orientation class to 'mega-menu-vertical'
	 *
	 * @since 1.1
	 * @param array $args
	 * @param int $menu_id
	 * @param string $location
	 */
	public function apply_vertical_class( $args, $menu_id, $location ) {

		$settings = get_option('megamenu_settings');

		if ( isset( $settings[$location]['orientation'] ) ) {
			$args['menu_class'] = str_replace( 'horizontal', $settings[$location]['orientation'], $args['menu_class'] );
		}

		return $args;

	}


	/**
	 * Add Orientation setting to menu options
	 *
	 * @since 1.1
	 * @param string $location
	 * @param array $settings
	 */
	public function add_orientation_setting( $location, $settings ) {
		?>

            <tr class='megamenu_orientation'>
                <td><?php _e("Orientation", "megamenupro"); ?></td>
                <td>
                    <select class='megamenu_orientation_select' name='megamenu_meta[<?php echo $location ?>][orientation]'>
                        <option value='horizontal'>Horizontal</option>
                        <option value='vertical' <?php selected( isset($settings[$location]['orientation']) && $settings[$location]['orientation'] == 'vertical' ) ?>><?php _e("Vertical", "megamenupro"); ?></option>
                        <option value='accordion' <?php selected( isset($settings[$location]['orientation']) && $settings[$location]['orientation'] == 'accordion' ) ?>><?php _e("Accordion", "megamenupro"); ?></option>
                    </select>
                </td>
            </tr>

            <?php
                if ( isset( $settings[$location]['orientation']) && $settings[$location]['orientation'] == 'accordion' ) {
                    $display = 'table-row';
                } else {
                    $display = 'none';
                }
            ?>

            <tr class='megamenu_accordion_behaviour' style='display: <?php echo $display; ?>;'>
                <td><?php _e("Accordion Behaviour", "megamenupro"); ?></td>
                <td>
                    <select name='megamenu_meta[<?php echo $location ?>][accordion_behaviour]'>
                        <option value='open_parents' <?php selected( isset($settings[$location]['accordion_behaviour']) && $settings[$location]['accordion_behaviour'] == 'open_parents' ) ?>><?php _e("Expand active sub menus", "megamenupro"); ?></option>
                        <option value='collapse_parents' <?php selected( isset($settings[$location]['accordion_behaviour']) && $settings[$location]['accordion_behaviour'] == 'collapse_parents' ) ?>><?php _e("Always collapse submenus", "megamenupro"); ?></option>
                    </select>
                </td>
            </tr>

		<?php

	}

	/**
	 * Append the vertical menu SCSS to the main SCSS file
	 *
	 * @since 1.1
	 * @param string $scss
	 * @param string
	 */
	public function append_scss( $scss ) {

		$path = trailingslashit( plugin_dir_path( __FILE__ ) ) . 'scss/vertical.scss';

		$contents = file_get_contents( $path );

 		return $scss . $contents;

	}

}

endif;