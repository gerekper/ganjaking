<?php
/**
 * Frontend class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\AjaxProductFilter\Classes
 * @version 1.3.2
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAN_Frontend_Premium' ) ) {
	/**
	 * Frontend class.
	 * The class manage all the frontend behaviors.
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAN_Frontend_Premium extends YITH_WCAN_Frontend_Extended {

		/**
		 * Constructor method
		 *
		 * @return void
		 */
		public function __construct() {
			parent::__construct();

			// Frontend methods.
			add_filter( 'yith_wcan_body_class', array( $this, 'premium_body_class' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'add_dropdown_styles' ), 20 );

			// Template methods.
			add_action( 'init', array( $this, 'add_active_filters_list' ) );
			add_action( 'init', array( $this, 'add_mobile_modal_opener' ) );

			add_action( 'yith_wcan_before_preset_filters', array( $this, 'filters_title' ), 10, 1 );
			add_action( 'yith_wcan_after_preset_filters', array( $this, 'apply_filters_button' ), 10, 1 );
		}

		/* === FRONTEND METHODS === */

		/**
		 * Enqueue Script for Premium version
		 *
		 * @return void
		 * @since  2.0
		 */
		public function enqueue_styles_scripts() {
			parent::enqueue_styles_scripts();

			if ( yith_wcan_can_be_displayed() ) {
				$suffix     = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
				$loader_url = YITH_WCAN_URL . 'assets/images/ajax-loader.gif';

				$options = array(
					'ajax_wc_price_filter'           => yith_wcan_get_option( 'yith_wcan_enable_ajax_price_filter' ),
					'wc_price_filter_slider'         => yith_wcan_get_option( 'yith_wcan_enable_ajax_price_filter_slider' ),
					'wc_price_filter_slider_in_ajax' => yith_wcan_get_option( 'yith_wcan_enable_slider_in_ajax' ),
					'wc_price_filter_dropdown'       => yith_wcan_get_option( 'yith_wcan_enable_dropdown_price_filter' ),
					'wc_price_filter_dropdown_style' => apply_filters( 'yith_wcan_dropdown_type', yith_wcan_get_option( 'yith_wcan_dropdown_style' ) ),
					'wc_price_filter_dropdown_widget_class' => yith_wcan_get_option( 'yith_wcan_ajax_widget_title_class', 'h3.widget-title' ),
					'widget_wrapper_class'           => yith_wcan_get_option( 'yith_wcan_ajax_widget_wrapper_class', '.widget' ),
					'price_filter_dropdown_class'    => apply_filters( 'yith_wcan_dropdown_class', 'widget-dropdown' ),
					'ajax_pagination_enabled'        => yith_wcan_get_option( 'yith_wcan_enable_ajax_shop_pagination', 'no' ),
					'pagination_anchor'              => yith_wcan_get_option( 'yith_wcan_ajax_shop_pagination', 'nav.woocommerce-pagination' ) . ' ' . yith_wcan_get_option( 'yith_wcan_ajax_shop_pagination_anchor_class', 'a.page-numbers' ),
					'force_widget_init'              => apply_filters( 'yith_wcan_force_widget_init', false ),
				);

				wp_enqueue_script( 'yith_wcan_frontend-premium', YITH_WCAN_URL . 'assets/js/yith-wcan-frontend-premium' . $suffix . '.js', array( 'jquery' ), YITH_WCAN_VERSION, true );
				wp_localize_script( 'yith-wcan-script', 'yith_wcan_frontend', array( 'loader_url' => yith_wcan_get_option( 'yith_wcan_ajax_loader', $loader_url ) ) );
				wp_localize_script( 'yith_wcan_frontend-premium', 'yith_wcan_frontend_premium', $options );
			}
		}

		/**
		 * Add a body class(es)
		 *
		 * @param array $classes The classes array.
		 *
		 * @return array
		 * @since  1.0
		 */
		public function body_class( $classes ) {
			$classes = parent::body_class( $classes );

			$modal_on_mobile = 'yes' === yith_wcan_get_option( 'yith_wcan_modal_on_mobile', 'no' );

			if ( $modal_on_mobile && apply_filters( 'yith_wcan_show_mobile_modal_opener', true ) ) {
				$classes[] = 'filters-in-modal';
			}

			return $classes;
		}

		/**
		 * Returns an array of parameters to use to localize shortcodes script
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 * @return array Array of parameters.
		 */
		protected function get_shortcodes_localize( $context = 'view' ) {
			$params = array_merge(
				parent::get_shortcodes_localize( 'edit' ),
				array(
					'instant_horizontal_filter' => 'yes' === yith_wcan_get_option( 'yith_wcan_instant_horizontal_filter', 'no' ),
					'show_clear_filter'         => 'yes' === yith_wcan_get_option( 'yith_wcan_show_clear_filter', 'no' ),
					'modal_on_mobile'           => 'yes' === yith_wcan_get_option( 'yith_wcan_modal_on_mobile', 'yes' ),
					'loader'                    => 'custom' === yith_wcan_get_option( 'yith_wcan_ajax_loader_style', 'default' ) ? yith_wcan_get_option( 'yith_wcan_ajax_loader_custom_icon', '' ) : false,
				)
			);

			if ( 'view' === $context ) {
				return apply_filters( 'yith_wcan_shortcodes_script_args', $params );
			}

			return $params;
		}

		/* === TEMPLATE METHODS === */

		/**
		 * Print preset title template
		 *
		 * @param YITH_WCAN_Preset|bool $preset Current preset, when applicable; false otherwise.
		 *
		 * @return void
		 */
		public function filters_title( $preset = false ) {
			$title = yith_wcan_get_option( 'yith_wcan_filters_title', '' );

			/**
			 * Print title template when:
			 * 1. Admin set a title
			 * 2. Filters will be shown as modal on mobile (title will be shown on mobile only, default will apply if no filter is configured).
			 */
			if ( empty( $title ) && 'yes' !== yith_wcan_get_option( 'yith_wcan_modal_on_mobile' ) ) {
				return;
			}

			$additional_classes_array = array();

			// apply default title when required.
			if ( empty( $title ) ) {
				$title                      = apply_filters( 'yith_wcan_default_modal_title', _x( 'Filter products', '[FRONTEND] Default modal title - mobile only', 'yith-woocommerce-ajax-navigation' ) );
				$additional_classes_array[] = 'mobile-only';
			}

			$title_tag          = apply_filters( 'yith_wcan_preset_title_tag', 'h3' );
			$additional_classes = implode( ' ', apply_filters( 'yith_wcan_preset_title_classes', $additional_classes_array, $this ) );

			echo wp_kses_post( sprintf( '<%1$s class="%3$s">%2$s</%1$s>', esc_html( $title_tag ), esc_html( $title ), esc_attr( $additional_classes ) ) );
		}

		/**
		 * Hooks callback that will print list fo active filters
		 *
		 * @return void
		 */
		public function add_active_filters_list() {
			$show_active_filters     = 'yes' === yith_wcan_get_option( 'yith_wcan_show_active_labels', 'yes' );
			$active_filters_position = yith_wcan_get_option( 'yith_wcan_active_labels_position', 'before_filters' );

			if ( ! $show_active_filters ) {
				return;
			}

			switch ( $active_filters_position ) {
				case 'before_filters':
					add_action( 'yith_wcan_before_preset_filters', array( $this, 'active_filters_list' ) );
					break;
				case 'after_filters':
					add_action( 'yith_wcan_after_preset_filters', array( $this, 'active_filters_list' ) );
					break;
				case 'before_products':
					$locations = $this->get_before_product_locations();

					if ( ! $locations ) {
						return;
					}

					foreach ( $locations as $location ) {
						add_action( $location['hook'], array( $this, 'active_filters_list' ), $location['priority'] );
					}
					break;
			}
		}

		/**
		 * Print list of active filters
		 *
		 * @param YITH_WCAN_Preset|bool $preset Current preset, when applicable; false otherwise.
		 *
		 * @return void
		 */
		public function active_filters_list( $preset = false ) {
			$active_filters = $this->query->get_active_filters( 'view' );
			$show_titles    = 'yes' === yith_wcan_get_option( 'yith_wcan_active_labels_with_titles', 'yes' );
			$labels_heading = apply_filters( 'yith_wcan_active_filters_title', _x( 'Active filters', '[FRONTEND] Active filters title', 'yith-woocommerce-ajax-navigation' ) );

			yith_wcan_get_template( 'filters/global/active-filters.php', compact( 'preset', 'active_filters', 'show_titles', 'labels_heading' ) );
		}

		/**
		 * Adds Mobile Modal Opener button, before product sections when possible
		 *
		 * @return void
		 */
		public function add_mobile_modal_opener() {
			$modal_on_mobile = 'yes' === yith_wcan_get_option( 'yith_wcan_modal_on_mobile', 'no' );

			if ( ! $modal_on_mobile ) {
				return;
			}

			$locations = $this->get_before_product_locations( -2 );

			if ( ! $locations ) {
				return;
			}

			foreach ( $locations as $location ) {
				add_action( $location['hook'], array( $this, 'mobile_modal_opener' ), $location['priority'] );
			}
		}

		/**
		 * Print Mobile Modal Opener button
		 *
		 * @param YITH_WCAN_Preset|bool $preset Current preset, when applicable; false otherwise.
		 *
		 * @return void
		 */
		public function mobile_modal_opener( $preset = false ) {
			$preset = $preset instanceof YITH_WCAN_Preset ? $preset : false;
			$label  = apply_filters( 'yith_wcan_mobile_modal_opener_label', _x( 'Filters', '[FRONTEND] Label for the Filters button on mobile', 'yith-woocommerce-ajax-navigation' ) );

			if ( ! apply_filters( 'yith_wcan_show_mobile_modal_opener', true ) ) {
				return;
			}

			yith_wcan_get_template( 'filters/global/mobile-filters.php', compact( 'label', 'preset' ) );
		}

		/**
		 * Remove duplicated templates before products shortcode
		 *
		 * When paginating shortcode, WC will execute both woocommerce_shortcode_before_products_loop and
		 * woocommerce_before_shop_loop; in order to avoid to print filter templates twice, we listeb for first event
		 * and remove_action from the second, when pagination is enabled
		 *
		 * @param array $shortcode_settings Array of shortcode configuration.
		 * @return void
		 */
		public function remove_duplicated_templates( $shortcode_settings = array() ) {
			if ( ! wc_string_to_bool( $shortcode_settings['paginate'] ) ) {
				return;
			}

			$locations = $this->get_before_product_locations();

			if ( ! isset( $locations['before_shop'] ) ) {
				return;
			}

			remove_action( $locations['before_shop']['hook'], array( $this, 'active_filters_list' ), $locations['before_shop']['priority'] );
			remove_action( $locations['before_shop']['hook'], array( $this, 'mobile_modal_opener' ), $locations['before_shop']['priority'] - 2 );

			parent::remove_duplicated_templates( $shortcode_settings );
		}

		/**
		 * Enqueue Script for Widget Dropdown
		 *
		 * @return void
		 * @since  2.0
		 */
		public function add_dropdown_styles() {
			// Dropdown Options.
			$widget_title   = yith_wcan_get_option( 'yith_wcan_ajax_widget_title_class', 'h3.widget-title' );
			$widget_wrapper = yith_wcan_get_option( 'yith_wcan_ajax_widget_wrapper_class', '.widget' );
			$background_url = YITH_WCAN_URL . 'assets/images/select-arrow.png';

			$css = "{$widget_wrapper} {$widget_title}.with-dropdown {position: relative; cursor: pointer;}
                    {$widget_wrapper} {$widget_title}.with-dropdown .widget-dropdown { border-width: 0; width: 22px; height: 22px; background: url({$background_url}) top 0px right no-repeat; background-size: 95% !important; position: absolute; top: 0; right: 0;}
                    {$widget_wrapper} {$widget_title}.with-dropdown.open .widget-dropdown {background-position: bottom 15px right;}";

			wp_add_inline_style( 'yith-wcan-frontend', $css );
		}

		/**
		 * Add a body class(es)
		 *
		 * @param string $classes Body classes added by the plugin.
		 *
		 * @return string Filtered list of classes added by the plugin to the body.
		 * @since  1.0
		 */
		public function premium_body_class( $classes ) {
			return 'yith-wcan-pro';
		}
	}
}
