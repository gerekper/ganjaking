<?php
/**
 * Class YITH_WCBK_Yith_Proteo_Integration
 * YITH Proteo Theme integration
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking
 */

defined( 'YITH_WCBK' ) || exit;

/**
 * Class YITH_WCBK_Yith_Proteo_Integration
 *
 * @since   3.0.0
 */
class YITH_WCBK_Yith_Proteo_Integration extends YITH_WCBK_Integration {
	use YITH_WCBK_Singleton_Trait;

	/**
	 * Init
	 */
	protected function init() {
		if ( $this->is_enabled() ) {
			add_filter( 'yith_wcbk_default_colors', array( $this, 'filter_default_colors' ), 10, 1 );
			add_filter( 'yith_proteo_skins_array', array( $this, 'add_skins' ), 10, 1 );
		}
	}

	/**
	 * Filter Booking default colors
	 *
	 * @param array $colors Default colors.
	 *
	 * @return array
	 */
	public function filter_default_colors( $colors = array() ) {
		$colors['primary']      = get_theme_mod( 'yith_proteo_main_color_shade', '#448a85' );
		$colors['border-color'] = get_theme_mod( 'yith_proteo_inputs_border_color', '#cccccc' );

		return $colors;
	}

	/**
	 * Add skins to Proteo ones.
	 *
	 * @param array $skins Skins list.
	 *
	 * @return array
	 */
	public function add_skins( $skins ) {

		$required_plugins = implode(
			', ',
			array(
				'WooCommerce',
				YITH_WCBK_PLUGIN_NAME,
				'EditorsKit',
				'Blocks Animation: CSS Animations for Gutenberg Blocks',
				'SVG Support',
				'CF7',
			)
		);

		$skins['booking-apartments'] = array(
			'import_file_name'           => 'Booking apartments - Gutenberg',
			'slug'                       => 'booking-apartments',
			'import_file_url'            => 'https://update.yithemes.com/proteo-demo-content/booking-apartments/proteo-booking-apartments-wordpress-export.xml',
			'import_widget_file_url'     => 'https://update.yithemes.com/proteo-demo-content/booking-apartments/proteo-booking-apartments-widgets.wie',
			'import_customizer_file_url' => 'https://update.yithemes.com/proteo-demo-content/booking-apartments/proteo-booking-apartments-customizer-export.json',
			'import_preview_image_url'   => 'https://update.yithemes.com/proteo-demo-content/booking-apartments/booking-apartments-screenshot.png',
			// translators: %s is the comma-separated list of required plugins.
			'import_notice'              => sprintf( __( 'This demo uses the following plugins: %s. Please be sure to enable these plugins prior to proceed.', 'yith-booking-for-woocommerce' ), $required_plugins ),
			'preview_url'                => 'https://proteo.yithemes.com/booking-apartments/',
			'state'                      => 'live',
			'front_page_title'           => 'Apartment homepage',
			'blog_page_title'            => 'Blog',
			'primary_menu_name'          => 'MainMenu',
			'category'                   => 'gutenberg',
		);

		$skins['booking-hotel'] = array(
			'import_file_name'           => 'Booking hotel - Gutenberg',
			'slug'                       => 'booking-hotel',
			'import_file_url'            => 'https://update.yithemes.com/proteo-demo-content/booking-hotel/proteo-booking-hotel-wordpress-export.xml',
			'import_widget_file_url'     => 'https://update.yithemes.com/proteo-demo-content/booking-hotel/proteo-booking-hotel-widgets.wie',
			'import_customizer_file_url' => 'https://update.yithemes.com/proteo-demo-content/booking-hotel/proteo-booking-hotel-customizer-export.json',
			'import_preview_image_url'   => 'https://update.yithemes.com/proteo-demo-content/booking-hotel/booking-hotel-screenshot.png',
			// translators: %s is the comma-separated list of required plugins.
			'import_notice'              => sprintf( __( 'This demo uses the following plugins: %s. Please be sure to enable these plugins prior to proceed.', 'yith-booking-for-woocommerce' ), $required_plugins ),
			'preview_url'                => 'https://proteo.yithemes.com/booking-hotel/',
			'state'                      => 'live',
			'front_page_title'           => 'Hotel homepage',
			'blog_page_title'            => 'Blog',
			'primary_menu_name'          => 'Menu',
			'category'                   => 'gutenberg',
		);

		$skins['booking-travel'] = array(
			'import_file_name'           => 'Booking travel - Gutenberg',
			'slug'                       => 'booking-travel',
			'import_file_url'            => 'https://update.yithemes.com/proteo-demo-content/booking-travel/proteo-booking-travel-wordpress-export.xml',
			'import_widget_file_url'     => 'https://update.yithemes.com/proteo-demo-content/booking-travel/proteo-booking-travel-widgets.wie',
			'import_customizer_file_url' => 'https://update.yithemes.com/proteo-demo-content/booking-travel/proteo-booking-travel-customizer-export.json',
			'import_preview_image_url'   => 'https://update.yithemes.com/proteo-demo-content/booking-travel/booking-travel-screenshot.png',
			// translators: %s is the comma-separated list of required plugins.
			'import_notice'              => sprintf( __( 'This demo uses the following plugins: %s. Please be sure to enable these plugins prior to proceed.', 'yith-booking-for-woocommerce' ), $required_plugins ),
			'preview_url'                => 'https://proteo.yithemes.com/booking-travel/',
			'state'                      => 'live',
			'front_page_title'           => 'Travel home',
			'blog_page_title'            => 'Blog',
			'primary_menu_name'          => 'MainMenu',
			'category'                   => 'gutenberg',
		);

		return $skins;
	}
}
