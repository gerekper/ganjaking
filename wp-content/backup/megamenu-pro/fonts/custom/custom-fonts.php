<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // disable direct access
}

if ( ! class_exists('Mega_Menu_Custom_Fonts') ) :

/**
 *
 */
class Mega_Menu_Custom_Fonts {

	/**
	 * Array of custom fonts
	 */
	var $enabled_fonts = array();


	/**
	 * Constructor
	 *
	 * @since 1.0
	 */
	public function __construct() {

		$this->set_enabled_fonts();

		add_filter( 'megamenu_fonts', array( $this, 'add_fonts_to_theme_font_list' ), 10 );
		add_action( 'megamenu_general_settings', array( $this, 'add_custom_font_row' ), 21 );


	}


	/**
	 * Retrieve the enabled custom fonts from the settings
	 *
	 * @since 1.0
	 */
	private function set_enabled_fonts() {

		$settings = get_option("megamenu_settings");

		if ( isset( $settings['fonts']['custom'] ) ) {

			$this->enabled_fonts = array_filter( explode( "\n", $settings['fonts']['custom'] ) );

		}

	}



	/**
	 * Add the specified custom fonts to the existing list of Available fonts
	 *
	 * @since 1.0
	 * @param array $fonts
	 * @return array
	 */
	public function add_fonts_to_theme_font_list( $fonts ) {

		if ( count( $this->enabled_fonts ) ) {
			foreach ( $this->enabled_fonts as $font ) {
				$fonts[] = trim( $font );
			}
		}

		return $fonts;
	}


	/**
	 * Add the custom font text area to the General Settings page
	 *
	 * @since 1.0
	 * @param array $settings
	 */
	public function add_custom_font_row( $settings ) {

		?>

        <h3><?php _e("Fonts", "megamenupro"); ?></h3>

        <table>
            <tr>
                <td class='mega-name'>
                    <?php _e("Custom Fonts", "megamenupro"); ?>
                    <div class='mega-description'>
                    	<?php _e("Specify custom fonts to be made available in the Font dropdowns in the Theme Editor (one per line)", "megamenupro"); ?>
                    </div>
                </td>
                <td class='mega-value'>

                	<?php
                	    echo "<textarea name='settings[fonts][custom]'>";

                	    if (isset($settings['fonts']['custom'])) {
                	    	echo esc_textarea(stripslashes($settings['fonts']['custom']));
                	    }

                	    echo "</textarea>";
                	?>

                </td>
            </tr>
        </table>

		<?php

	}

}

endif;