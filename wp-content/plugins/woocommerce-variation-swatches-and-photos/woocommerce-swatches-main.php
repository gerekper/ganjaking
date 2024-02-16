<?php
class WC_SwatchesPlugin {

	public function __construct() {

		define( 'WC_SWATCHES_VERSION', '3.0.6' );

		require 'woocommerce-swatches-template-functions.php';

		require 'classes/class-wc-swatch-term.php';
		require 'classes/class-wc-swatch-product-term.php';
		require 'classes/class-wc-swatches-product-attribute-images.php';
		require 'classes/class-wc-ex-product-data-tab.php';
		require 'classes/class-wc-swatches-product-data-tab.php';
		require 'classes/class-wc-swatch-attribute-configuration.php';

		require 'classes/class-wc-swatches-ajax-handler.php';

		add_action( 'init', array( $this, 'on_init' ) );

		add_action( 'wc_quick_view_enqueue_scripts', array( $this, 'on_enqueue_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'on_enqueue_scripts' ) );


		add_action( 'admin_head', array( $this, 'on_enqueue_scripts' ) );

		WC_Swatches_Product_Attribute_Images::register( 'swatches_id', 'swatches_image_size' );
		WC_Swatches_Product_Data_Tab::register();

		//Swatch Image Size Settings
		add_filter( 'woocommerce_catalog_settings', array(
			$this,
			'swatches_image_size_setting'
		) ); // pre WC 2.1
		add_filter( 'woocommerce_product_settings', array(
			$this,
			'swatches_image_size_setting'
		) ); // WC 2.1+
		add_filter( 'woocommerce_get_image_size_swatches', array( $this, 'get_image_size_swatches' ) );
	}

	public function on_init() {
		$image_size = get_option( 'swatches_image_size', array() );
		$size       = array();

		$size['width']  = ! empty( $image_size['width'] ) ? $image_size['width'] : '32';
		$size['height'] = ! empty( $image_size['height'] ) ? $image_size['height'] : '32';
		$size['crop']   = $image_size['crop'] ?? 1;

		$image_size = apply_filters( 'woocommerce_get_image_size_swatches_image_size', $size );

		add_image_size( 'swatches_image_size', apply_filters( 'woocommerce_swatches_size_width_default', $image_size['width'] ), apply_filters( 'woocommerce_swatches_size_height_default', $image_size['height'] ), $image_size['crop'] );
	}

	public function on_enqueue_scripts() {
		global $pagenow;

		if ( ! is_admin() ) {
			wp_enqueue_style( 'swatches-and-photos', $this->plugin_url() . '/assets/css/swatches-and-photos.css', array(), WC_SWATCHES_VERSION );
			wp_enqueue_script( 'swatches-and-photos', $this->plugin_url() . '/assets/js/swatches-and-photos.js', array( 'jquery' ), WC_SWATCHES_VERSION, true );

			$data = array(
				'ajax_url' => admin_url( 'admin-ajax.php' )
			);

			wp_localize_script( 'swatches-and-photos', 'wc_swatches_params', $data );
		}

		if ( is_admin() && ( $pagenow == 'post-new.php' || $pagenow == 'post.php' || $pagenow == 'edit.php' || 'edit-tags.php' ) ) {
			wp_enqueue_media();
			wp_enqueue_style( 'swatches-and-photos', $this->plugin_url() . '/assets/css/swatches-and-photos.css' );
			wp_enqueue_script( 'swatches-and-photos-admin', $this->plugin_url() . '/assets/js/swatches-and-photos-admin.js', array( 'jquery' ), '1.0', true );

			wp_enqueue_style( 'colourpicker', $this->plugin_url() . '/assets/css/colorpicker.css' );
			wp_enqueue_script( 'colourpicker', $this->plugin_url() . '/assets/js/colorpicker.js', array( 'jquery' ) );


			$data = array(
				'placeholder_img_src' => apply_filters( 'woocommerce_placeholder_img_src', WC()->plugin_url() . '/assets/images/placeholder.png' )
			);

			wp_localize_script( 'swatches-and-photos-admin', 'wc_swatches_params', $data );
		}
	}

	public function plugin_url() {
		return untrailingslashit( plugin_dir_url( __FILE__ ) );
	}

	public function plugin_dir() {
		return plugin_dir_path( __FILE__ );
	}

	public function swatches_image_size_setting( $settings ) {
		$setting = array(
			'name'     => __( 'Swatches and Photos', 'wc_swatches_and_photos' ),
			'desc'     => __( 'The default size for color swatches and photos.', 'wc_swatches_and_photos' ),
			'id'       => 'swatches_image_size',
			'css'      => '',
			'type'     => 'image_width',
			'std'      => '32',
			'desc_tip' => true,
			'default'  => array(
				'crop'   => 1,
				'width'  => 32,
				'height' => 32
			)
		);

		$index = count( $settings ) - 1;

		$settings[ $index + 1 ] = $settings[ $index ];
		$settings[ $index ]     = $setting;

		return $settings;
	}

	public function get_image_size_swatches( $size ) {
		$image_size = get_option( 'swatches_image_size', array() );
		$size       = array();

		$size['width']  = ! empty( $image_size['width'] ) ? $image_size['width'] : '32';
		$size['height'] = ! empty( $image_size['height'] ) ? $image_size['height'] : '32';
		$size['crop']   = isset( $image_size['crop'] ) && $image_size['crop'] ? 1 : 0;

		$image_size = apply_filters( 'woocommerce_get_image_size_swatches_image_size', $size );

		//Need to remove the filter because woocommerce will disable the input field.
		remove_filter( 'woocommerce_get_image_size_swatches', array( $this, 'get_image_size_swatches' ) );

		return $image_size;
	}
}
