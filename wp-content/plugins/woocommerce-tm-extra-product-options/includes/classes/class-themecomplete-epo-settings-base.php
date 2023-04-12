<?php
/**
 * Extra Product Options Settings class
 *
 * @package Extra Product Options/Classes
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Extra Product Options Settings class
 *
 * @package Extra Product Options/Classes
 * @version 6.0
 */
final class THEMECOMPLETE_EPO_SETTINGS_Base {

	/**
	 * The single instance of the class
	 *
	 * @var THEMECOMPLETE_EPO_SETTINGS_Base|null
	 * @since 1.0
	 */
	protected static $instance = null;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Class Constructor
	 *
	 * @since 1.0
	 */
	public function __construct() {
	}

	/**
	 * Get settings array
	 *
	 * @param string $setting The name of the setting.
	 * @param string $label The label for the section.
	 * @since 1.0
	 */
	public function create_setting( $setting, $label ) {
		$method = 'get_setting_' . $setting;
		if ( is_callable( [ $this, $method ] ) ) {
			// This may return undefined function wp_get_current_user() when it is used without @ on some configurations.
			$settings = $this->$method( $setting, $label );
			if ( ! is_array( $settings ) ) {
				$settings = [];
			}
			foreach ( $settings as $key => $setting_array ) {
				if ( isset( $setting_array['desc'] ) && '' !== $setting_array['desc'] ) {
					$settings[ $key ]['desc'] = '<span class="description">' . $setting_array['desc'] . '</span>';
				}
			}
			$settings = apply_filters( 'tm_epo_settings_' . $setting, $settings );

			return $settings;
		}

		return [];

	}

	/**
	 * Set settings categories
	 *
	 * @since 1.0
	 */
	public function settings_options() {
		$settings_options = [
			'general'  => [ 'tcfa tcfa-cog', esc_html__( 'General', 'woocommerce-tm-extra-product-options' ) ],
			'display'  => [ 'tcfa tcfa-tv', esc_html__( 'Display', 'woocommerce-tm-extra-product-options' ) ],
			'cart'     => [ 'tcfa tcfa-shopping-cart', esc_html__( 'Cart', 'woocommerce-tm-extra-product-options' ) ],
			'order'    => [ 'tcfa tcfa-truck-pickup', esc_html__( 'Order', 'woocommerce-tm-extra-product-options' ) ],
			'string'   => [ 'tcfa tcfa-font', esc_html__( 'Strings', 'woocommerce-tm-extra-product-options' ) ],
			'style'    => [ 'tcfa tcfa-border-style', esc_html__( 'Style', 'woocommerce-tm-extra-product-options' ) ],
			'global'   => [ 'tcfa tcfa-globe', esc_html__( 'Global', 'woocommerce-tm-extra-product-options' ) ],
			'elements' => [ 'tcfa tcfa-shapes', esc_html__( 'Elements', 'woocommerce-tm-extra-product-options' ) ],
			'upload'   => [ 'tcfa tcfa-cloud-upload-alt', esc_html__( 'Upload manager', 'woocommerce-tm-extra-product-options' ) ],
			'code'     => [ 'tcfa tcfa-code', esc_html__( 'Custom code', 'woocommerce-tm-extra-product-options' ) ],
			'math'     => [ 'tcfa tcfa-square-root-alt', esc_html__( 'Math Formula Constants', 'woocommerce-tm-extra-product-options' ) ],
			'other'    => 'other',
			'license'  => [ 'tcfa tcfa-id-badge', esc_html__( 'License', 'woocommerce-tm-extra-product-options' ) ],
		];

		return $settings_options;
	}

	/**
	 * Get plugin settings
	 *
	 * @since 1.0
	 */
	public function plugin_settings() {
		$settings = [];
		$o        = $this->settings_options();
		$ids      = [];
		foreach ( $o as $key => $value ) {
			$settings[ $key ] = $this->create_setting( $key, $value );
		}

		foreach ( $settings as $key => $value ) {
			foreach ( $value as $key2 => $value2 ) {
				if ( isset( $value2['id'] ) && isset( $value2['default'] ) && 'epo_page_options' !== $value2['id'] ) {
					$ids[ $value2['id'] ] = $value2['default'];
				}
			}
		}

		return $ids;
	}

	/**
	 * Get "other" settings header
	 *
	 * @since 1.0
	 */
	public function get_other_settings_headers() {
		$headers = [];

		return apply_filters( 'tm_epo_settings_headers', $headers );
	}

	/**
	 * Get "other" settings
	 *
	 * @since 1.0
	 */
	public function get_other_settings() {
		$settings = [];

		return apply_filters( 'tm_epo_settings_settings', $settings );
	}

	/**
	 * Populate order post types setting
	 *
	 * @since 1.0
	 */
	private function get_order_post_types() {
		$tm_epo_order_post_types_default = [ 'shop_order' => esc_html__( 'Shop order', 'woocommerce-tm-extra-product-options' ) ];
		$tm_epo_order_post_types         = get_option( 'tm_epo_order_post_types' );
		if ( ! is_array( $tm_epo_order_post_types ) ) {
			$tm_epo_order_post_types = $tm_epo_order_post_types_default;
		} else {
			$tm_epo_order_post_types = array_combine( $tm_epo_order_post_types, $tm_epo_order_post_types );
		}

		$tm_epo_order_post_types = apply_filters( 'tm_epo_order_post_types', $tm_epo_order_post_types );

		$tm_epo_order_post_types = array_unique( array_merge( $tm_epo_order_post_types, $tm_epo_order_post_types_default ) );
		return $tm_epo_order_post_types;
	}

	/**
	 * General settings
	 *
	 * @param string $setting The name of the setting.
	 * @param string $label The label for the section.
	 * @since 1.0
	 */
	public function get_setting_general( $setting, $label ) {
		return [
			[
				'type'  => 'tm_title',
				'id'    => 'epo_page_options',
				'desc'  => '<span tabindex="0" data-menu="tcinit" class="tm-section-menu-item">' . esc_html__( 'Initialization', 'woocommerce-tm-extra-product-options' ) . '</span>' .
						'<span tabindex="0" data-menu="tcftb" class="tm-section-menu-item">' . esc_html__( 'Final total box', 'woocommerce-tm-extra-product-options' ) . '</span>' .
						'<span tabindex="0" data-menu="tcvarious" class="tm-section-menu-item">' . esc_html__( 'Various', 'woocommerce-tm-extra-product-options' ) . '</span>',
				'title' => $label,
			],
			[
				'title'   => esc_html__( 'Enable front-end for roles', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Select the roles that will have access to the extra options.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_roles_enabled',
				'class'   => 'tcinit chosen_select',
				'css'     => 'min-width:300px;',
				'default' => '@everyone',
				'type'    => 'multiselect',
				'options' => themecomplete_get_roles(),
			],
			[
				'title'   => esc_html__( 'Disable front-end for roles', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Select the roles that will not have access to the extra options.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_roles_disabled',
				'class'   => 'tcinit chosen_select',
				'css'     => 'min-width:300px;',
				'default' => '',
				'type'    => 'multiselect',
				'options' => themecomplete_get_roles(),
			],
			[
				'title'   => esc_html__( 'Enable translations', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'This will enable the default plugin translation using the pot files.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_enable_translations',
				'class'   => 'tcinit',
				'default' => 'yes',
				'type'    => 'checkbox',
			],
			[
				'title'       => esc_html__( 'Post type hook priority', 'woocommerce-tm-extra-product-options' ),
				'desc'        => esc_html__( 'Do not change this unless you know how it will affect your site! This is the priority which the post types are loaded by the plugin.', 'woocommerce-tm-extra-product-options' ),
				'id'          => 'tm_epo_post_type_hook_priority',
				'placeholder' => esc_html__( 'Default value is 100', 'woocommerce-tm-extra-product-options' ),
				'default'     => '',
				'class'       => 'tcinit',
				'type'        => 'text',
			],
			[
				'title'   => esc_html__( 'Final total box', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Select when to show the final total box', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_final_total_box',
				'class'   => 'tcftb chosen_select',
				'css'     => 'min-width:300px;',
				'default' => 'normal',
				'type'    => 'select',
				'options' => [
					'normal'                => esc_html__( 'Show Both Final and Options total box', 'woocommerce-tm-extra-product-options' ),
					'options'               => esc_html__( 'Show only Options total', 'woocommerce-tm-extra-product-options' ),
					'optionsiftotalnotzero' => esc_html__( 'Show only Options total if total is not zero', 'woocommerce-tm-extra-product-options' ),
					'final'                 => esc_html__( 'Show only Final total', 'woocommerce-tm-extra-product-options' ),
					'hideoptionsifzero'     => esc_html__( 'Show Final total and hide Options total if zero', 'woocommerce-tm-extra-product-options' ),
					'hideifoptionsiszero'   => esc_html__( 'Hide Final total box if Options total is zero', 'woocommerce-tm-extra-product-options' ),
					'hideiftotaliszero'     => esc_html__( 'Hide Final total box if total is zero', 'woocommerce-tm-extra-product-options' ),
					'hide'                  => esc_html__( 'Hide Final total box', 'woocommerce-tm-extra-product-options' ),
					'pxq'                   => esc_html__( 'Always show only Final total (Price x Quantity)', 'woocommerce-tm-extra-product-options' ),
					'disable_change'        => esc_html__( 'Disable but change product prices', 'woocommerce-tm-extra-product-options' ),
					'disable'               => esc_html__( 'Disable', 'woocommerce-tm-extra-product-options' ),
				],
			],
			[
				'title'   => esc_html__( 'Enable Final total box for all products', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Show the Final total box even when the product has no extra options', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_enable_final_total_box_all',
				'class'   => 'tcftb',
				'default' => 'no',
				'type'    => 'checkbox',
			],
			[
				'title'   => esc_html__( 'Enable original final total display', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Check to enable the display of the undiscounted final total', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_enable_original_final_total',
				'class'   => 'tcftb',
				'default' => 'no',
				'type'    => 'checkbox',
			],
			( 'yes' === get_option( 'woocommerce_calc_taxes' ) ) ?
			[
				'title'   => esc_html__( 'Enable options VAT display', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Check to display the options VAT amount above the options total', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_enable_vat_options_total',
				'class'   => 'tcftb',
				'default' => 'no',
				'type'    => 'checkbox',
			] : [],
			[
				'title'   => esc_html__( 'Show Unit price on totals box', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enable this to display the unit price when the totals box is visible', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_show_unit_price',
				'class'   => 'tcftb',
				'default' => 'no',
				'type'    => 'checkbox',
			],
			[
				'title'   => esc_html__( 'Include Fees on unit price', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enable this to add any Fees to the unit price', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_fees_on_unit_price',
				'class'   => 'tcftb',
				'default' => 'no',
				'type'    => 'checkbox',
			],
			[
				'title'   => esc_html__( 'Total price as Unit Price', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Make the total price not being multiplied by the product quantity', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_total_price_as_unit_price',
				'class'   => 'tcftb',
				'default' => 'no',
				'type'    => 'checkbox',
			],
			[
				'title'   => esc_html__( 'Disable lazy load images', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enable this to disable lazy loading images.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_no_lazy_load',
				'default' => 'yes',
				'class'   => 'tcvarious',
				'type'    => 'checkbox',
			],
			[
				'title'   => esc_html__( 'Preload lightbox images', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enable this to preload the image when using the lightbox feature.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_preload_lightbox_image',
				'default' => 'no',
				'class'   => 'tcvarious',
				'type'    => 'checkbox',
			],
			[
				'title'   => esc_html__( 'Enable plugin for WooCommerce shortcodes', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enabling this will load the plugin files to all WordPress pages. Use with caution.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_enable_shortcodes',
				'default' => 'no',
				'class'   => 'tcvarious',
				'type'    => 'checkbox',
			],
			[
				'title'   => esc_html__( 'Enable shortcodes in options strings', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enabling this will allow the use of shortcodes and HTML code in the options label and description text.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_enable_data_shortcodes',
				'default' => 'yes',
				'class'   => 'tcvarious',
				'type'    => 'checkbox',
			],
			[
				'type' => 'tm_sectionend',
				'id'   => 'epo_page_options',
			],
		];
	}

	/**
	 * Display settings
	 *
	 * @param string $setting The name of the setting.
	 * @param string $label The label for the section.
	 * @since 1.0
	 */
	public function get_setting_display( $setting, $label ) {
		return [
			[
				'type'  => 'tm_title',
				'id'    => 'epo_page_options',
				'desc'  => '<span tabindex="0" data-menu="tcdisplay" class="tm-section-menu-item">' . esc_html__( 'Display', 'woocommerce-tm-extra-product-options' ) . '</span>' .
						'<span tabindex="0" data-menu="tcplacement" class="tm-section-menu-item">' . esc_html__( 'Placement', 'woocommerce-tm-extra-product-options' ) . '</span>' .
						'<span tabindex="0" data-menu="tcprice" class="tm-section-menu-item">' . esc_html__( 'Price', 'woocommerce-tm-extra-product-options' ) . '</span>' .
						'<span tabindex="0" data-menu="tcftbox" class="tm-section-menu-item">' . esc_html__( 'Floating Totals box', 'woocommerce-tm-extra-product-options' ) . '</span>' .
						'<span tabindex="0" data-menu="tcanimation" class="tm-section-menu-item">' . esc_html__( 'Animation', 'woocommerce-tm-extra-product-options' ) . '</span>' .
						'<span tabindex="0" data-menu="tcvarious2" class="tm-section-menu-item">' . esc_html__( 'Various', 'woocommerce-tm-extra-product-options' ) . '</span>',

				'title' => $label,
			],
			[
				'title'   => esc_html__( 'Display', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'This controls how your fields are displayed on the front-end. If you choose "Show using action hooks" you have to manually write the code to your theme or plugin to display the fields and the placement settings below will not work. If you use the Composite Products extension you must leave this setting to "Normal" otherwise the extra options cannot be displayed on the composite product bundles. See more at the documentation.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_display',
				'class'   => 'tcdisplay chosen_select',
				'css'     => 'min-width:300px;',
				'default' => 'normal',
				'type'    => 'select',
				'options' => [
					'normal' => esc_html__( 'Normal', 'woocommerce-tm-extra-product-options' ),
					'action' => esc_html__( 'Show using action hooks', 'woocommerce-tm-extra-product-options' ),
				],
			],
			[
				'title'   => esc_html__( 'Extra Options placement', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Select where you want the extra options to appear.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_options_placement',
				'class'   => 'tcplacement chosen_select',
				'css'     => 'min-width:300px;',
				'default' => 'woocommerce_before_add_to_cart_button',
				'type'    => 'select',
				'options' => [
					'woocommerce_before_add_to_cart_button' => esc_html__( 'Before add to cart button', 'woocommerce-tm-extra-product-options' ),
					'woocommerce_after_add_to_cart_button' => esc_html__( 'After add to cart button', 'woocommerce-tm-extra-product-options' ),

					'woocommerce_before_add_to_cart_form'  => esc_html__( 'Before cart form', 'woocommerce-tm-extra-product-options' ),
					'woocommerce_after_add_to_cart_form'   => esc_html__( 'After cart form', 'woocommerce-tm-extra-product-options' ),

					'woocommerce_before_single_product'    => esc_html__( 'Before product', 'woocommerce-tm-extra-product-options' ),
					'woocommerce_after_single_product'     => esc_html__( 'After product', 'woocommerce-tm-extra-product-options' ),

					'woocommerce_before_single_product_summary' => esc_html__( 'Before product summary', 'woocommerce-tm-extra-product-options' ),
					'woocommerce_after_single_product_summary' => esc_html__( 'After product summary', 'woocommerce-tm-extra-product-options' ),

					'woocommerce_product_thumbnails'       => esc_html__( 'After product image', 'woocommerce-tm-extra-product-options' ),

					'custom'                               => esc_html__( 'Custom hook', 'woocommerce-tm-extra-product-options' ),
				],
			],
			[
				'title'   => esc_html__( 'Extra Options placement custom hook', 'woocommerce-tm-extra-product-options' ),
				'desc'    => '',
				'class'   => 'tcplacement',
				'id'      => 'tm_epo_options_placement_custom_hook',
				'default' => '',
				'type'    => 'text',
			],
			[
				'title'   => esc_html__( 'Extra Options placement hook priority', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Select the Extra Options placement hook priority', 'woocommerce-tm-extra-product-options' ),
				'class'   => 'tcplacement',
				'id'      => 'tm_epo_options_placement_hook_priority',
				'default' => '50',
				'type'    => 'number',
			],
			[
				'title'   => esc_html__( 'Totals box placement', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Select where you want the Totals box to appear.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_totals_box_placement',
				'class'   => 'tcplacement chosen_select',
				'css'     => 'min-width:300px;',
				'default' => 'woocommerce_before_add_to_cart_button',
				'type'    => 'select',
				'options' => [
					'woocommerce_before_add_to_cart_button' => esc_html__( 'Before add to cart button', 'woocommerce-tm-extra-product-options' ),
					'woocommerce_after_add_to_cart_button' => esc_html__( 'After add to cart button', 'woocommerce-tm-extra-product-options' ),

					'woocommerce_before_add_to_cart_form'  => esc_html__( 'Before cart form', 'woocommerce-tm-extra-product-options' ),
					'woocommerce_after_add_to_cart_form'   => esc_html__( 'After cart form', 'woocommerce-tm-extra-product-options' ),

					'woocommerce_before_single_product'    => esc_html__( 'Before product', 'woocommerce-tm-extra-product-options' ),
					'woocommerce_after_single_product'     => esc_html__( 'After product', 'woocommerce-tm-extra-product-options' ),

					'woocommerce_before_single_product_summary' => esc_html__( 'Before product summary', 'woocommerce-tm-extra-product-options' ),
					'woocommerce_after_single_product_summary' => esc_html__( 'After product summary', 'woocommerce-tm-extra-product-options' ),

					'woocommerce_product_thumbnails'       => esc_html__( 'After product image', 'woocommerce-tm-extra-product-options' ),

					'custom'                               => esc_html__( 'Custom hook', 'woocommerce-tm-extra-product-options' ),
				],
			],
			[
				'title'   => esc_html__( 'Totals box placement custom hook', 'woocommerce-tm-extra-product-options' ),
				'desc'    => '',
				'class'   => 'tcplacement',
				'id'      => 'tm_epo_totals_box_placement_custom_hook',
				'default' => '',
				'type'    => 'text',
			],
			[
				'title'   => esc_html__( 'Totals box placement hook priority', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Select the Totals box placement hook priority', 'woocommerce-tm-extra-product-options' ),
				'class'   => 'tcplacement',
				'id'      => 'tm_epo_totals_box_placement_hook_priority',
				'default' => '50',
				'type'    => 'number',
			],
			[
				'title'   => esc_html__( 'Floating Totals box', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'This will enable a floating box to display your totals box.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_floating_totals_box',
				'class'   => 'tcftbox chosen_select',
				'css'     => 'min-width:300px;',
				'default' => 'disable',
				'type'    => 'select',
				'options' => [
					'disable'      => esc_html__( 'Disable', 'woocommerce-tm-extra-product-options' ),
					'bottom right' => esc_html__( 'Bottom right', 'woocommerce-tm-extra-product-options' ),
					'bottom left'  => esc_html__( 'Bottom left', 'woocommerce-tm-extra-product-options' ),
					'top right'    => esc_html__( 'Top right', 'woocommerce-tm-extra-product-options' ),
					'top left'     => esc_html__( 'Top left', 'woocommerce-tm-extra-product-options' ),
				],
			],
			[
				'title'   => esc_html__( 'Floating Totals box visibility', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'This determines the floating totals box visibility.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_floating_totals_box_visibility',
				'class'   => 'tcftbox chosen_select',
				'css'     => 'min-width:300px;',
				'default' => 'always',
				'type'    => 'select',
				'options' => [
					'always'          => esc_html__( 'Always visible', 'woocommerce-tm-extra-product-options' ),
					'afterscroll'     => esc_html__( 'Visble after scrolling the page', 'woocommerce-tm-extra-product-options' ),
					'hideafterscroll' => esc_html__( 'Hide after scrolling the page', 'woocommerce-tm-extra-product-options' ),
				],
			],
			[
				'title'   => esc_html__( 'Pixels amount needed to scroll', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Select the number of pixels the page needs to scroll for the floating totals to become visible.', 'woocommerce-tm-extra-product-options' ),
				'class'   => 'tcftbox',
				'id'      => 'tm_epo_floating_totals_box_pixels',
				'default' => '100',
				'type'    => 'number',
			],
			[
				'title'   => esc_html__( 'Add to cart button on floating totals box', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Display the add to cart button on floating box.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_floating_totals_box_add_button',
				'default' => 'no',
				'class'   => 'tcftbox',
				'type'    => 'checkbox',
			],
			[
				'title'   => esc_html__( 'Change original product price', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Check to overwrite the original product price when the price is changing.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_change_original_price',
				'default' => 'no',
				'class'   => 'tcprice',
				'type'    => 'checkbox',
			],
			[
				'title'   => esc_html__( 'Change variation price', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Check to overwrite the variation price when the price is changing.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_change_variation_price',
				'default' => 'no',
				'class'   => 'tcprice',
				'type'    => 'checkbox',
			],
			[
				'title'   => esc_html__( 'Force Select Options', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'This changes the add to cart button on shop and archive pages to display select options when the product has extra product options. Enabling this will remove the ajax functionality.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_force_select_options',
				'class'   => 'tcdisplay',
				'css'     => 'min-width:300px;',
				'default' => 'no',
				'type'    => 'checkbox',
				'value'   => ( get_option( 'tm_epo_force_select_options' ) === 'display' ? 'yes' : get_option( 'tm_epo_force_select_options' ) ),
			],
			[
				'title'   => esc_html__( 'Enable extra options in shop and category view', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Check to enable the display of extra options on the shop page and category view. This setting is theme dependent and some aspects may not work as expected.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_enable_in_shop',
				'default' => 'no',
				'class'   => 'tcdisplay',
				'type'    => 'checkbox',
			],
			[
				'title'   => esc_html__( 'Remove Free price label', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Check to remove Free price label when product has extra options', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_remove_free_price_label',
				'class'   => 'tcprice',
				'default' => 'no',
				'type'    => 'checkbox',
			],
			[
				'title'   => esc_html__( 'Use progressive display on options', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enabling this will hide the options on the product page until JavaScript is initialized. This is a fail-safe setting and we recommend to be active.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_progressive_display',
				'class'   => 'tcanimation',
				'default' => 'yes',
				'type'    => 'checkbox',
			],
			[
				'title'   => esc_html__( 'Animation delay', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'How long the animation will take in milliseconds', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_animation_delay',
				'class'   => 'tcanimation',
				'default' => '100',
				'type'    => 'text',
			],
			[
				'title'   => esc_html__( 'Start Animation delay', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'The delay until the animation starts in milliseconds', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_start_animation_delay',
				'class'   => 'tcanimation',
				'default' => '0',
				'type'    => 'text',
			],
			[
				'title'   => esc_html__( 'Show quantity selector only for elements with a value', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Check show quantity selector only for elements with a value.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_show_only_active_quantities',
				'class'   => 'tcdisplay',
				'default' => 'yes',
				'type'    => 'checkbox',
			],
			[
				'title'   => esc_html__( 'Hide add-to-cart button until an element is chosen', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Check this to show the add to cart button only when at least one option is filled.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_hide_add_cart_button',
				'class'   => 'tcdisplay',
				'default' => 'no',
				'type'    => 'checkbox',
			],
			[
				'title'   => esc_html__( 'Hide add-to-cart button until all required elements are chosen', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Check this to show the add to cart button only when all required visible options are filled.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_hide_required_add_cart_button',
				'class'   => 'tcdisplay',
				'default' => 'no',
				'type'    => 'checkbox',
			],
			[
				'title'   => esc_html__( 'Hide add-to-cart button until all elements are chosen', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Check this to show the add to cart button only when all visible options are filled.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_hide_all_add_cart_button',
				'class'   => 'tcdisplay',
				'default' => 'no',
				'type'    => 'checkbox',
			],
			[
				'title'   => esc_html__( 'Show full width label for elements.', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Check this to force elements to be full width instead of auto.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_select_fullwidth',
				'class'   => 'tcdisplay',
				'default' => 'yes',
				'type'    => 'checkbox',
			],
			[
				'title'   => esc_html__( 'Show choice description inline.', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Check this to disable showing description as a tooltip and show it inline instead.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_description_inline',
				'class'   => 'tcdisplay',
				'default' => 'no',
				'type'    => 'checkbox',
			],
			[
				'title'   => esc_html__( 'Hide choice label when using the Show tooltip setting for radio buttons and checkboxes', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Check this to hide the choice label when using the Show tooltip setting for radio buttons and checkboxes.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_swatch_hide_label',
				'class'   => 'tcdisplay',
				'default' => 'yes',
				'type'    => 'checkbox',
			],
			[
				'title'   => esc_html__( 'Auto hide price if zero', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Check this to globally hide the price display if it is zero.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_auto_hide_price_if_zero',
				'class'   => 'tcprice',
				'default' => 'no',
				'type'    => 'checkbox',
			],
			[
				'title'   => esc_html__( 'Trim zeros in prices', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Check this to globally trim zero in prices. This will be applied to native WooCommerce prices as well.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_trim_zeros',
				'class'   => 'tcprice',
				'default' => 'no',
				'type'    => 'checkbox',
			],
			[
				'title'   => esc_html__( 'Hide element price html when hide price setting is enabled', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Check this if you use Google Merchant Center. It will hide the price html of the element when you enable its hide price setting.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_hide_price_html',
				'class'   => 'tcprice',
				'default' => 'yes',
				'type'    => 'checkbox',
			],
			[
				'title'   => esc_html__( 'Show prices inside select box choices', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Check this to show the price of the select box options if the price type is fixed.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_show_price_inside_option',
				'class'   => 'tcprice',
				'default' => 'no',
				'type'    => 'checkbox',
			],
			[
				'title'   => esc_html__( 'Show prices inside select box choices even if the prices are hidden', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Check this to show the price of the select box options if the price type is fixed and even if the element hides the price.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_show_price_inside_option_hidden_even',
				'class'   => 'tcprice',
				'default' => 'no',
				'type'    => 'checkbox',
			],
			[
				'title'   => esc_html__( 'Multiply prices inside select box choices with its quantity selector', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Check this to multiply the prices of the select box options with its quantity selector if any.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_multiply_price_inside_option',
				'class'   => 'tcprice',
				'default' => 'yes',
				'type'    => 'checkbox',
			],
			( THEMECOMPLETE_EPO_WPML()->is_active() )
				?
				[
					'title'   => esc_html__( 'Use translated values when possible on admin Order', 'woocommerce-tm-extra-product-options' ),
					'desc'    => esc_html__( 'Please note that if the options on the Order change or get deleted you will get wrong results by enabling this!', 'woocommerce-tm-extra-product-options' ),
					'id'      => 'tm_epo_wpml_order_translate',
					'class'   => 'tcdisplay',
					'default' => 'no',
					'type'    => 'checkbox',

				]
				: [],
			[
				'title'   => esc_html__( 'Include option pricing in product price', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Check this to include the pricing of the options to the product price.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_include_possible_option_pricing',
				'class'   => 'tcprice',
				'default' => 'no',
				'type'    => 'checkbox',
			],
			[
				'title'   => esc_html__( 'Check for empty product price', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Check this to have the plugin set to zero the product price when it is empty.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_add_product_price_check',
				'class'   => 'tcprice',
				'default' => 'yes',
				'type'    => 'checkbox',
			],
			[
				'title'   => esc_html__( 'Use the "From" string on displayed product prices', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Check this to alter the price display of a product when it has extra options with prices.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_use_from_on_price',
				'class'   => 'tcvarious2',
				'default' => 'no',
				'type'    => 'checkbox',
			],
			[
				'title'   => esc_html__( 'Alter generated product structured data', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Alters the generated product structured data. This may produce wrong results if the options use conditional logic!', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_alter_structured_data',
				'class'   => 'tcvarious2',
				'default' => 'no',
				'type'    => 'checkbox',
			],
			[
				'title'   => esc_html__( 'Responsive options structure', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enable this if you want the options to have responsive display.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_responsive_display',
				'class'   => 'tcvarious2',
				'default' => 'yes',
				'type'    => 'checkbox',
			],
			[
				'type' => 'tm_sectionend',
				'id'   => 'epo_page_options',
			],
		];
	}

	/**
	 * Cart settings
	 *
	 * @param string $setting The name of the setting.
	 * @param string $label The label for the section.
	 * @since 1.0
	 */
	public function get_setting_cart( $setting, $label ) {
		return [
			[
				'type'  => 'tm_title',
				'id'    => 'epo_page_options',
				'title' => $label,
			],
			[
				'title'   => esc_html__( 'Turn off persistent cart', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enable this if the product has a lot of options.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_turn_off_persi_cart',
				'default' => 'no',
				'type'    => 'checkbox',
			],
			[
				'title'   => esc_html__( 'Clear cart button', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enables or disables the clear cart button', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_clear_cart_button',
				'default' => 'no',
				'type'    => 'checkbox',
				'value'   => ( get_option( 'tm_epo_clear_cart_button' ) === 'show' ? 'yes' : get_option( 'tm_epo_clear_cart_button' ) ),
			],
			[
				'title'   => esc_html__( 'Cart Field Display', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Select how to display your fields in the cart', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_cart_field_display',
				'class'   => 'chosen_select',
				'css'     => 'min-width:300px;',
				'default' => 'normal',
				'type'    => 'select',
				'options' => [
					'normal'   => esc_html__( 'Normal display', 'woocommerce-tm-extra-product-options' ),
					'link'     => esc_html__( 'Display a pop-up link', 'woocommerce-tm-extra-product-options' ),
					'advanced' => esc_html__( 'Advanced display', 'woocommerce-tm-extra-product-options' ),
				],
			],
			[
				'title'   => esc_html__( 'Hide extra options in cart', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enables or disables the display of options in the cart.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_hide_options_in_cart',
				'default' => 'no',
				'type'    => 'checkbox',
				'value'   => ( get_option( 'tm_epo_hide_options_in_cart' ) === 'hide' ? 'yes' : get_option( 'tm_epo_hide_options_in_cart' ) ),
			],
			[
				'title'   => esc_html__( 'Hide extra options prices in cart', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enables or disables the display of prices of options in the cart.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_hide_options_prices_in_cart',
				'default' => 'no',
				'type'    => 'checkbox',
				'value'   => ( get_option( 'tm_epo_hide_options_prices_in_cart' ) === 'hide' ? 'yes' : get_option( 'tm_epo_hide_options_prices_in_cart' ) ),
			],
			version_compare( get_option( 'woocommerce_db_version' ), '2.3', '<' ) ?
				[] :
				[
					'title'   => esc_html__( 'Prevent negative priced products', 'woocommerce-tm-extra-product-options' ),
					'desc'    => esc_html__( 'Prevent adding to the cart negative priced products.', 'woocommerce-tm-extra-product-options' ),
					'id'      => 'tm_epo_no_negative_priced_products',
					'default' => 'no',
					'type'    => 'checkbox',
				],
			[
				'title'   => esc_html__( 'Prevent zero priced products', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Prevent adding to the cart zero priced products.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_no_zero_priced_products',
				'default' => 'no',
				'type'    => 'checkbox',
			],
			[
				'title'   => esc_html__( 'Hide checkbox element average price', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'This will hide the average price display on the cart for checkboxes.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_hide_cart_average_price',
				'default' => 'yes',
				'type'    => 'checkbox',
			],
			[
				'title'   => esc_html__( 'Show image replacement in cart and checkout', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enabling this will show the images of elements that have an image replacement.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_show_image_replacement',
				'default' => 'no',
				'type'    => 'checkbox',
			],
			[
				'title'   => esc_html__( 'Hide upload file URL in cart and checkout', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enabling this will hide the URL of any uploaded file while in cart and checkout.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_show_hide_uploaded_file_url_cart',
				'default' => 'no',
				'type'    => 'checkbox',
			],
			[
				'title'   => esc_html__( 'Show uploaded image in cart and checkout', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enabling this will show the uploaded images in cart and checkout.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_show_upload_image_replacement',
				'default' => 'yes',
				'type'    => 'checkbox',
			],
			[
				'title'   => esc_html__( 'Maximum image width', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Set the maximum width of the images that appear on cart.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_image_max_width',
				'default' => '70%',
				'type'    => 'text',
			],
			[
				'title'   => esc_html__( 'Maximum image height', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Set the maximum height of the images that appear on cart.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_image_max_height',
				'default' => 'none',
				'type'    => 'text',
			],
			[
				'title'   => esc_html__( 'Always use unique values on cart for elements', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enabling this will separate comma separated values for elements. This is mainly used for multiple checkbox choices.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_always_unique_values',
				'default' => 'no',
				'type'    => 'checkbox',
			],
			[
				'type' => 'tm_sectionend',
				'id'   => 'epo_page_options',
			],

		];
	}

	/**
	 * Order settings
	 *
	 * @param string $setting The name of the setting.
	 * @param string $label The label for the section.
	 * @since 4.8
	 */
	public function get_setting_order( $setting, $label ) {
		return [
			[
				'type'  => 'tm_title',
				'id'    => 'epo_page_options',
				'title' => $label,
			],
			[
				'title'             => esc_html__( 'Post types to show the saved options', 'woocommerce-tm-extra-product-options' ),
				'desc'              => esc_html__( 'Select the post types where the plugin will modify the edit order screen to show the saved options. You can type in your custom post type.', 'woocommerce-tm-extra-product-options' ),
				'id'                => 'tm_epo_order_post_types',
				'class'             => 'chosen_select',
				'css'               => 'min-width:300px;',
				'default'           => 'shop_order',
				'type'              => 'multiselect',
				'custom_attributes' => [ 'data-tags' => true ],
				'options'           => $this->get_order_post_types(),
			],
			[
				'title'   => esc_html__( 'Strip html from emails', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Check to strip the html tags from emails', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_strip_html_from_emails',
				'default' => 'yes',
				'type'    => 'checkbox',
			],
			[
				'title'   => esc_html__( 'Hide uploaded file path', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Check to hide the uploaded file path from users (in the Order).', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_hide_upload_file_path',
				'default' => 'yes',
				'type'    => 'checkbox',
			],
			[
				'title'   => esc_html__( 'Legacy meta data', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Check to enable legacy meta data functionality.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_legacy_meta_data',
				'default' => 'no',
				'type'    => 'checkbox',
			],
			[
				'title'   => esc_html__( 'Unique meta values', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Check to split items with multiple values to unique lines.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_unique_meta_values',
				'default' => 'no',
				'type'    => 'checkbox',
			],
			[
				'title'   => esc_html__( 'Prevent options from being sent to emails', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Check to disable options from being sent to emails.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_prevent_options_from_emails',
				'default' => 'no',
				'type'    => 'checkbox',
			],
			[
				'title'   => esc_html__( 'Disable sending the options upon saving the order', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enable this if you are getting a 500 error when trying to complete the order in the checkout.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_disable_sending_options_in_order',
				'default' => 'no',
				'type'    => 'checkbox',
			],
			[
				'title'   => esc_html__( 'Attach upload files to emails', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Check to Attach upload files to emails.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_attach_uploaded_to_emails',
				'default' => 'yes',
				'type'    => 'checkbox',
			],
			[
				'title'   => esc_html__( 'Disable Options on Order status change', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Check this only if you are getting server errors on checkout.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_disable_options_on_order_status',
				'default' => 'no',
				'type'    => 'checkbox',
			],
			[
				'title'   => esc_html__( 'Hide upload file URL in order', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enabling this will hide the URL of any uploaded file while in order.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_show_hide_uploaded_file_url_order',
				'default' => 'no',
				'type'    => 'checkbox',
			],
			[
				'type' => 'tm_sectionend',
				'id'   => 'epo_page_options',
			],

		];
	}

	/**
	 * String settings
	 *
	 * @param string $setting The name of the setting.
	 * @param string $label The label for the section.
	 * @since 1.0
	 */
	public function get_setting_string( $setting, $label ) {
		return [
			[
				'type'  => 'tm_title',
				'id'    => 'epo_page_options',
				'desc'  => '<span tabindex="0" data-menu="tcsgeneral" class="tm-section-menu-item">' . esc_html__( 'General', 'woocommerce-tm-extra-product-options' ) . '</span>' .
						'<span tabindex="0" data-menu="tcscart" class="tm-section-menu-item">' . esc_html__( 'Cart', 'woocommerce-tm-extra-product-options' ) . '</span>' .
						'<span tabindex="0" data-menu="tcsfinaltotalbox" class="tm-section-menu-item">' . esc_html__( 'Final total box', 'woocommerce-tm-extra-product-options' ) . '</span>' .
						'<span tabindex="0" data-menu="tcselements" class="tm-section-menu-item">' . esc_html__( 'Elements', 'woocommerce-tm-extra-product-options' ) . '</span>',

				'title' => $label,
			],
			[
				'title'   => esc_html__( 'Cart field/value separator', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enter the field/value separator for the cart.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_separator_cart_text',
				'default' => ':',
				'class'   => 'tcscart',
				'type'    => 'text',
			],
			[
				'title'   => esc_html__( 'Option multiple value separator in cart', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enter the value separator for the option that have multiple values like checkboxes.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_multiple_separator_cart_text',
				'default' => ' ',
				'class'   => 'tcscart',
				'type'    => 'text',
			],
			[
				'title'       => esc_html__( 'Update cart text', 'woocommerce-tm-extra-product-options' ),
				'desc'        => esc_html__( 'Enter the Update cart text when you edit a product.', 'woocommerce-tm-extra-product-options' ),
				'id'          => 'tm_epo_update_cart_text',
				'placeholder' => esc_html__( 'Update cart', 'woocommerce-tm-extra-product-options' ),
				'default'     => '',
				'class'       => 'tcscart',
				'type'        => 'text',
			],
			[
				'title'       => esc_html__( 'Edit Options text replacement', 'woocommerce-tm-extra-product-options' ),
				'desc'        => esc_html__( 'Enter a text to replace the Edit options text on the cart.', 'woocommerce-tm-extra-product-options' ),
				'id'          => 'tm_epo_edit_options_text',
				'placeholder' => esc_html__( 'Edit options', 'woocommerce-tm-extra-product-options' ),
				'default'     => '',
				'class'       => 'tcscart',
				'type'        => 'text',
			],
			[
				'title'       => esc_html__( 'Additional Options text replacement', 'woocommerce-tm-extra-product-options' ),
				'desc'        => esc_html__( 'Enter a text to replace the Additional options text when using the pop up setting on the cart.', 'woocommerce-tm-extra-product-options' ),
				'id'          => 'tm_epo_additional_options_text',
				'placeholder' => esc_html__( 'Additional options', 'woocommerce-tm-extra-product-options' ),
				'default'     => '',
				'class'       => 'tcscart',
				'type'        => 'text',
			],
			[
				'title'       => esc_html__( 'Close button text replacement', 'woocommerce-tm-extra-product-options' ),
				'desc'        => esc_html__( 'Enter a text to replace the Close button text when using the pop up setting on the cart.', 'woocommerce-tm-extra-product-options' ),
				'id'          => 'tm_epo_close_button_text',
				'placeholder' => esc_html__( 'Close', 'woocommerce-tm-extra-product-options' ),
				'default'     => '',
				'class'       => 'tcscart',
				'type'        => 'text',
			],
			[
				'title'       => esc_html__( 'Empty cart text', 'woocommerce-tm-extra-product-options' ),
				'desc'        => esc_html__( 'Enter a text to replace the empty cart button text.', 'woocommerce-tm-extra-product-options' ),
				'id'          => 'tm_epo_empty_cart_text',
				'placeholder' => esc_html__( 'Empty cart', 'woocommerce-tm-extra-product-options' ),
				'default'     => '',
				'class'       => 'tcscart',
				'type'        => 'text',
			],
			[
				'title'       => esc_html__( 'Final total text', 'woocommerce-tm-extra-product-options' ),
				'desc'        => esc_html__( 'Enter the Final total text or leave blank for default.', 'woocommerce-tm-extra-product-options' ),
				'id'          => 'tm_epo_final_total_text',
				'placeholder' => esc_html__( 'Final total', 'woocommerce-tm-extra-product-options' ),
				'default'     => '',
				'class'       => 'tcsfinaltotalbox',
				'type'        => 'text',
			],
			[
				'title'       => esc_html__( 'Unit price text', 'woocommerce-tm-extra-product-options' ),
				'desc'        => esc_html__( 'Enter the Unit price text or leave blank for default.', 'woocommerce-tm-extra-product-options' ),
				'id'          => 'tm_epo_options_unit_price_text',
				'placeholder' => esc_html__( 'Unit price', 'woocommerce-tm-extra-product-options' ),
				'default'     => '',
				'class'       => 'tcsfinaltotalbox',
				'type'        => 'text',
			],
			[
				'title'       => esc_html__( 'Options total text', 'woocommerce-tm-extra-product-options' ),
				'desc'        => esc_html__( 'Enter the Options total text or leave blank for default.', 'woocommerce-tm-extra-product-options' ),
				'id'          => 'tm_epo_options_total_text',
				'placeholder' => esc_html__( 'Options amount', 'woocommerce-tm-extra-product-options' ),
				'default'     => '',
				'class'       => 'tcsfinaltotalbox',
				'type'        => 'text',
			],
			[
				'title'       => esc_html__( 'Options VAT total text', 'woocommerce-tm-extra-product-options' ),
				'desc'        => esc_html__( 'Enter the Options VAT total text or leave blank for default.', 'woocommerce-tm-extra-product-options' ),
				'id'          => 'tm_epo_vat_options_total_text',
				'placeholder' => esc_html__( 'Options VAT amount', 'woocommerce-tm-extra-product-options' ),
				'default'     => '',
				'class'       => 'tcsfinaltotalbox',
				'type'        => 'text',
			],
			[
				'title'       => esc_html__( 'Fees total text', 'woocommerce-tm-extra-product-options' ),
				'desc'        => esc_html__( 'Enter the Fees total text or leave blank for default.', 'woocommerce-tm-extra-product-options' ),
				'id'          => 'tm_epo_fees_total_text',
				'placeholder' => esc_html__( 'Fees amount', 'woocommerce-tm-extra-product-options' ),
				'default'     => '',
				'class'       => 'tcsfinaltotalbox',
				'type'        => 'text',
			],

			[
				'title'   => esc_html__( 'Free Price text replacement', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enter a text to replace the Free price label when product has extra options.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_replacement_free_price_text',
				'default' => '',
				'class'   => 'tcsgeneral',
				'type'    => 'text',
			],
			[
				'title'       => esc_html__( 'Force Select options text', 'woocommerce-tm-extra-product-options' ),
				'desc'        => esc_html__( 'Enter a text to replace the add to cart button text when using the Force select option.', 'woocommerce-tm-extra-product-options' ),
				'id'          => 'tm_epo_force_select_text',
				'placeholder' => esc_html__( 'Select options', 'woocommerce-tm-extra-product-options' ),
				'default'     => '',
				'class'       => 'tcsgeneral',
				'type'        => 'text',
			],
			[
				'title'       => esc_html__( 'No zero priced products text', 'woocommerce-tm-extra-product-options' ),
				'desc'        => esc_html__( 'Enter a text to replace the message when trying to add a zero priced product to the cart.', 'woocommerce-tm-extra-product-options' ),
				'id'          => 'tm_epo_no_zero_priced_products_text',
				'placeholder' => esc_html__( 'You cannot add zero priced products to the cart.', 'woocommerce-tm-extra-product-options' ),
				'default'     => '',
				'class'       => 'tcsgeneral',
				'type'        => 'text',
			],
			[
				'title'       => esc_html__( 'No negative priced products text', 'woocommerce-tm-extra-product-options' ),
				'desc'        => esc_html__( 'Enter a text to replace the message when trying to add a negative priced product to the cart.', 'woocommerce-tm-extra-product-options' ),
				'id'          => 'tm_epo_no_negative_priced_products_text',
				'placeholder' => esc_html__( 'You cannot add negative priced products to the cart.', 'woocommerce-tm-extra-product-options' ),
				'default'     => '',
				'class'       => 'tcsgeneral',
				'type'        => 'text',
			],
			[
				'title'       => esc_html__( 'Popup section button text replacement', 'woocommerce-tm-extra-product-options' ),
				'desc'        => esc_html__( 'Enter a text to replace the topup section button text.', 'woocommerce-tm-extra-product-options' ),
				'id'          => 'tm_epo_popup_section_button_text',
				'placeholder' => esc_html__( 'Open', 'woocommerce-tm-extra-product-options' ),
				'default'     => '',
				'class'       => 'tcselements',
				'type'        => 'text',
			],
			[
				'title'       => esc_html__( 'Reset Options text replacement', 'woocommerce-tm-extra-product-options' ),
				'desc'        => esc_html__( 'Enter a text to replace the Reset options text when using custom variations.', 'woocommerce-tm-extra-product-options' ),
				'id'          => 'tm_epo_reset_variation_text',
				'placeholder' => esc_html__( 'Reset options', 'woocommerce-tm-extra-product-options' ),
				'default'     => '',
				'class'       => 'tcselements',
				'type'        => 'text',
			],
			[
				'title'       => esc_html__( 'Calendar close button text replacement', 'woocommerce-tm-extra-product-options' ),
				'desc'        => esc_html__( 'Enter a text to replace the Close button text on the calendar.', 'woocommerce-tm-extra-product-options' ),
				'id'          => 'tm_epo_closetext',
				'placeholder' => esc_html__( 'Done', 'woocommerce-tm-extra-product-options' ),
				'default'     => '',
				'class'       => 'tcselements',
				'type'        => 'text',
			],
			[
				'title'       => esc_html__( 'Calendar today button text replacement', 'woocommerce-tm-extra-product-options' ),
				'desc'        => esc_html__( 'Enter a text to replace the Today button text on the calendar.', 'woocommerce-tm-extra-product-options' ),
				'id'          => 'tm_epo_currenttext',
				'placeholder' => esc_html__( 'Today', 'woocommerce-tm-extra-product-options' ),
				'default'     => '',
				'class'       => 'tcselements',
				'type'        => 'text',
			],
			[
				'title'       => esc_html__( 'Slider previous text', 'woocommerce-tm-extra-product-options' ),
				'desc'        => esc_html__( 'Enter a text to replace the previous button text for slider.', 'woocommerce-tm-extra-product-options' ),
				'id'          => 'tm_epo_slider_prev_text',
				'placeholder' => esc_html__( 'Prev', 'woocommerce-tm-extra-product-options' ),
				'default'     => '',
				'class'       => 'tcselements',
				'type'        => 'text',
			],
			[
				'title'       => esc_html__( 'Slider next text', 'woocommerce-tm-extra-product-options' ),
				'desc'        => esc_html__( 'Enter a text to replace the next button text for slider.', 'woocommerce-tm-extra-product-options' ),
				'id'          => 'tm_epo_slider_next_text',
				'placeholder' => esc_html__( 'Next', 'woocommerce-tm-extra-product-options' ),
				'default'     => '',
				'class'       => 'tcselements',
				'type'        => 'text',
			],
			[
				'title'       => esc_html__( 'This field is required text', 'woocommerce-tm-extra-product-options' ),
				'desc'        => esc_html__( 'Enter a text to indicate that a field is required.', 'woocommerce-tm-extra-product-options' ),
				'id'          => 'tm_epo_this_field_is_required_text',
				'placeholder' => esc_html__( 'This field is required.', 'woocommerce-tm-extra-product-options' ),
				'default'     => '',
				'class'       => 'tcselements',
				'type'        => 'text',
			],
			[
				'title'       => esc_html__( 'Characters remaining text', 'woocommerce-tm-extra-product-options' ),
				'desc'        => esc_html__( 'Enter a text to replace the Characters remaining text when using maximum characters on a text field or a textarea.', 'woocommerce-tm-extra-product-options' ),
				'id'          => 'tm_epo_characters_remaining_text',
				'placeholder' => esc_html__( 'characters remaining', 'woocommerce-tm-extra-product-options' ),
				'default'     => '',
				'class'       => 'tcselements',
				'type'        => 'text',
			],
			[
				'title'       => esc_html__( 'Uploading files text', 'woocommerce-tm-extra-product-options' ),
				'desc'        => esc_html__( 'Enter a text to replace the Uploading files text used in the pop-up after clicking the add to cart button  when there are upload fields.', 'woocommerce-tm-extra-product-options' ),
				'id'          => 'tm_epo_uploading_files_text',
				'placeholder' => esc_html__( 'Uploading files', 'woocommerce-tm-extra-product-options' ),
				'default'     => '',
				'class'       => 'tcselements',
				'type'        => 'text',
			],
			[
				'title'       => esc_html__( 'Uploading message text', 'woocommerce-tm-extra-product-options' ),
				'desc'        => esc_html__( 'Enter a message to be used in the pop-up after clicking the add to cart button when there are upload fields.', 'woocommerce-tm-extra-product-options' ),
				'id'          => 'tm_epo_uploading_message_text',
				'placeholder' => esc_html__( 'Your files are being uploaded', 'woocommerce-tm-extra-product-options' ),
				'default'     => '',
				'class'       => 'tcselements',
				'type'        => 'text',
			],
			[
				'title'       => esc_html__( 'Select file text', 'woocommerce-tm-extra-product-options' ),
				'desc'        => esc_html__( 'Enter a text to replace the Select file text used in the styled upload button.', 'woocommerce-tm-extra-product-options' ),
				'id'          => 'tm_epo_select_file_text',
				'placeholder' => esc_html__( 'Select file', 'woocommerce-tm-extra-product-options' ),
				'default'     => '',
				'class'       => 'tcselements',
				'type'        => 'text',
			],
			[
				'title'       => esc_html__( 'Single file text', 'woocommerce-tm-extra-product-options' ),
				'desc'        => esc_html__( 'Enter a text to replace the file text used in the styled upload button.', 'woocommerce-tm-extra-product-options' ),
				'id'          => 'tm_epo_uploading_num_file',
				'placeholder' => esc_html__( 'file', 'woocommerce-tm-extra-product-options' ),
				'default'     => '',
				'class'       => 'tcselements',
				'type'        => 'text',
			],
			[
				'title'       => esc_html__( 'Multiple files text', 'woocommerce-tm-extra-product-options' ),
				'desc'        => esc_html__( 'Enter a text to replace the files text used in the styled upload button.', 'woocommerce-tm-extra-product-options' ),
				'id'          => 'tm_epo_uploading_num_files',
				'placeholder' => esc_html__( 'files', 'woocommerce-tm-extra-product-options' ),
				'default'     => '',
				'class'       => 'tcselements',
				'type'        => 'text',
			],
			[
				'title'       => esc_html__( 'Add button text on associated products', 'woocommerce-tm-extra-product-options' ),
				'desc'        => esc_html__( 'Enter a text to replace the add button text on associated products.', 'woocommerce-tm-extra-product-options' ),
				'id'          => 'tm_epo_add_button_text_associated_products',
				'placeholder' => esc_html__( 'Add', 'woocommerce-tm-extra-product-options' ),
				'default'     => '',
				'class'       => 'tcselements',
				'type'        => 'text',
			],
			[
				'title'       => esc_html__( 'Remove button text on associated products', 'woocommerce-tm-extra-product-options' ),
				'desc'        => esc_html__( 'Enter a text to replace the remove button text on associated products.', 'woocommerce-tm-extra-product-options' ),
				'id'          => 'tm_epo_remove_button_text_associated_products',
				'placeholder' => esc_html__( 'Remove', 'woocommerce-tm-extra-product-options' ),
				'default'     => '',
				'class'       => 'tcselements',
				'type'        => 'text',
			],
			[
				'title'       => esc_html__( 'Repeater add text', 'woocommerce-tm-extra-product-options' ),
				'desc'        => esc_html__( 'Enter a text to replace the add text button for repeater fields.', 'woocommerce-tm-extra-product-options' ),
				'id'          => 'tm_epo_add_button_text_repeater',
				'placeholder' => esc_html__( 'Add', 'woocommerce-tm-extra-product-options' ),
				'default'     => '',
				'class'       => 'tcselements',
				'type'        => 'text',
			],

			[
				'type' => 'tm_sectionend',
				'id'   => 'epo_page_options',
			],
		];
	}

	/**
	 * Style settings
	 *
	 * @param string $setting The name of the setting.
	 * @param string $label The label for the section.
	 * @since 1.0
	 */
	public function get_setting_style( $setting, $label ) {
		return [
			[
				'type'  => 'tm_title',
				'id'    => 'epo_page_options',
				'title' => $label,
			],

			[
				'title'   => esc_html__( 'Enable checkbox and radio styles', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enables or disables extra styling for checkboxes and radio buttons.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_css_styles',
				'default' => 'no',
				'type'    => 'checkbox',
				'value'   => ( get_option( 'tm_epo_css_styles' ) === 'on' ? 'yes' : get_option( 'tm_epo_css_styles' ) ),
			],
			[
				'title'   => esc_html__( 'Style', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Select a style for the checkboxes and radio buttons', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_css_styles_style',
				'class'   => 'chosen_select',
				'css'     => 'min-width:300px;',
				'default' => 'round',
				'type'    => 'select',
				'options' => [
					'round'   => esc_html__( 'Round', 'woocommerce-tm-extra-product-options' ),
					'round2'  => esc_html__( 'Round 2', 'woocommerce-tm-extra-product-options' ),
					'square'  => esc_html__( 'Square', 'woocommerce-tm-extra-product-options' ),
					'square2' => esc_html__( 'Square 2', 'woocommerce-tm-extra-product-options' ),

				],
			],
			[
				'title'   => esc_html__( 'Select item border type', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Select a style for the selected border when using image replacements or swatches.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_css_selected_border',
				'class'   => 'chosen_select',
				'css'     => 'min-width:300px;',
				'default' => '',
				'type'    => 'select',
				'options' => [
					''         => esc_html__( 'Default', 'woocommerce-tm-extra-product-options' ),
					'square'   => esc_html__( 'Square', 'woocommerce-tm-extra-product-options' ),
					'round'    => esc_html__( 'Round', 'woocommerce-tm-extra-product-options' ),
					'shadow'   => esc_html__( 'Shadow', 'woocommerce-tm-extra-product-options' ),
					'thinline' => esc_html__( 'Thin line', 'woocommerce-tm-extra-product-options' ),
				],
			],

			[
				'type' => 'tm_sectionend',
				'id'   => 'epo_page_options',
			],

		];
	}

	/**
	 * Global settings
	 *
	 * @param string $setting The name of the setting.
	 * @param string $label The label for the section.
	 * @since 1.0
	 */
	public function get_setting_global( $setting, $label ) {
		return [
			[
				'type'  => 'tm_title',
				'id'    => 'epo_page_options',
				'desc'  => '<span tabindex="0" data-menu="tcglobal1" class="tm-section-menu-item">' . esc_html__( 'General', 'woocommerce-tm-extra-product-options' ) . '</span>' .
						'<span tabindex="0" data-menu="tcglobal2" class="tm-section-menu-item">' . esc_html__( 'Visual', 'woocommerce-tm-extra-product-options' ) . '</span>' .
						'<span tabindex="0" data-menu="tcglobal3" class="tm-section-menu-item">' . esc_html__( 'Product page', 'woocommerce-tm-extra-product-options' ) . '</span>' .
						'<span tabindex="0" data-menu="tcglobal5" class="tm-section-menu-item">' . esc_html__( 'Locale', 'woocommerce-tm-extra-product-options' ) . '</span>' .
						'<span tabindex="0" data-menu="tcglobal6" class="tm-section-menu-item">' . esc_html__( 'Pricing', 'woocommerce-tm-extra-product-options' ) . '</span>' .
						'<span tabindex="0" data-menu="tcglobal7" class="tm-section-menu-item">' . esc_html__( 'Strings', 'woocommerce-tm-extra-product-options' ) . '</span>' .
						'<span tabindex="0" data-menu="tcglobal9" class="tm-section-menu-item">' . esc_html__( 'CDN', 'woocommerce-tm-extra-product-options' ) . '</span>' .
						'<span tabindex="0" data-menu="tcglobal8" class="tm-section-menu-item">' . esc_html__( 'Various', 'woocommerce-tm-extra-product-options' ) . '</span>',
				'title' => $label,
			],
			[
				'title'   => esc_html__( 'Enable validation', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Check to enable validation feature for builder elements', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_enable_validation',
				'default' => 'yes',
				'class'   => 'tcglobal1',
				'type'    => 'checkbox',
			],
			[
				'title'   => esc_html__( 'Disable error scrolling', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Check to disable scrolling to the element with an error', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_disable_error_scroll',
				'default' => 'no',
				'class'   => 'tcglobal1',
				'type'    => 'checkbox',
			],
			[
				'title'   => esc_html__( 'Error label placement', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Set the placement for the validation error notification label', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_error_label_placement',
				'class'   => 'tcglobal1 chosen_select',
				'css'     => 'min-width:300px;',
				'default' => '',
				'type'    => 'select',
				'options' => [
					''       => esc_html__( 'After the element', 'woocommerce-tm-extra-product-options' ),
					'before' => esc_html__( 'Before the element', 'woocommerce-tm-extra-product-options' ),
				],
			],
			[
				'title'   => esc_html__( 'Use options cache', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Use options caching for boosting performance. Disable if you have options that share the same unique ID.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_options_cache',
				'default' => 'no',
				'class'   => 'tcglobal1',
				'type'    => 'checkbox',
			],
			[
				'title'   => esc_html__( 'Javascript and CSS inclusion mode', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Select how to include JS and CSS files', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_js_css_mode',
				'class'   => 'tcglobal1 chosen_select',
				'css'     => 'min-width:300px;',
				'default' => 'dev',
				'type'    => 'select',
				'options' => [
					''         => esc_html__( 'Single minified file', 'woocommerce-tm-extra-product-options' ),
					'multiple' => esc_html__( 'Multiple minified files', 'woocommerce-tm-extra-product-options' ),
					'dev'      => esc_html__( 'DEV - multiple files', 'woocommerce-tm-extra-product-options' ),
				],
			],
			[
				'title'   => esc_html__( 'Disable PNG convert security', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Check to disable the conversion to png for image uploads.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_no_upload_to_png',
				'default' => 'no',
				'class'   => 'tcglobal8',
				'type'    => 'checkbox',
			],
			[
				'title'   => esc_html__( 'Override product price', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'This will globally override the product price with the price from the options if the total options price is greater then zero.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_override_product_price',
				'class'   => 'tcglobal6 chosen_select',
				'css'     => 'min-width:300px;',
				'default' => '',
				'type'    => 'select',
				'options' => [
					''    => esc_html__( 'Use setting on each product', 'woocommerce-tm-extra-product-options' ),
					'no'  => esc_html__( 'No', 'woocommerce-tm-extra-product-options' ),
					'yes' => esc_html__( 'Yes', 'woocommerce-tm-extra-product-options' ),
				],
			],
			[
				'title'   => esc_html__( 'Options price mode', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Select the price mode for the options.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_options_price_mode',
				'class'   => 'tcglobal6 chosen_select',
				'css'     => 'min-width:300px;',
				'default' => 'sale',
				'type'    => 'select',
				'options' => [
					'sale'    => esc_html__( 'Use sale price', 'woocommerce-tm-extra-product-options' ),
					'regular' => esc_html__( 'Use regular price', 'woocommerce-tm-extra-product-options' ),
				],
			],
			[
				'title'   => esc_html__( 'Reset option values after the product is added to the cart', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'This will revert the option values to the default ones after adding the product to the cart', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_reset_options_after_add',
				'default' => 'no',
				'class'   => 'tcglobal3',
				'type'    => 'checkbox',
			],
			[
				'title'   => esc_html__( 'Use plus and minus signs on prices in cart and checkout', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Choose how you want the sign of options prices to be displayed in cart and checkout.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_price_sign',
				'class'   => 'tcglobal8 chosen_select',
				'css'     => 'min-width:300px;',
				'default' => '',
				'type'    => 'select',
				'options' => [
					''      => esc_html__( 'Display both signs', 'woocommerce-tm-extra-product-options' ),
					'minus' => esc_html__( 'Display only minus sign', 'woocommerce-tm-extra-product-options' ),

				],
			],
			[
				'title'   => esc_html__( 'Use plus and minus signs on option prices', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Choose how you want the sign of options prices to be displayed at the product page.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_options_price_sign',
				'class'   => 'tcglobal8 chosen_select',
				'css'     => 'min-width:300px;',
				'default' => 'minus',
				'type'    => 'select',
				'options' => [
					''      => esc_html__( 'Display both signs', 'woocommerce-tm-extra-product-options' ),
					'minus' => esc_html__( 'Display only minus sign', 'woocommerce-tm-extra-product-options' ),

				],
			],
			[
				'title'   => esc_html__( 'Input decimal separator', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Choose how to determine the decimal separator for user inputs', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_input_decimal_separator',
				'class'   => 'tcglobal5 chosen_select',
				'css'     => 'min-width:300px;',
				'default' => 'browser',
				'type'    => 'select',
				'options' => [
					''        => esc_html__( 'Use WooCommerce value', 'woocommerce-tm-extra-product-options' ),
					'browser' => esc_html__( 'Determine by browser local', 'woocommerce-tm-extra-product-options' ),

				],
			],
			[
				'title'   => esc_html__( 'Displayed decimal separator', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Choose which decimal separator to display on currency prices', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_displayed_decimal_separator',
				'class'   => 'tcglobal5 chosen_select',
				'css'     => 'min-width:300px;',
				'default' => '',
				'type'    => 'select',
				'options' => [
					''        => esc_html__( 'Use WooCommerce value', 'woocommerce-tm-extra-product-options' ),
					'browser' => esc_html__( 'Determine by browser local', 'woocommerce-tm-extra-product-options' ),
				],
			],
			[
				'title'   => esc_html__( 'Timezone override for Date element', 'woocommerce-tm-extra-product-options' ),
				/* translators: %s "timezone" */
				'desc'    => sprintf( esc_html__( 'Choose which %s the date element will use on the backend calculations or leave blank for server timezone.', 'woocommerce-tm-extra-product-options' ), '<a href="' . esc_url( 'https://www.php.net/manual/en/timezones.php' ) . '" target="_blank">' . esc_html__( 'timezone', 'woocommerce-tm-extra-product-options' ) . '</a>' ),
				'id'      => 'tm_epo_global_date_timezone',
				'default' => '',
				'class'   => 'tcglobal5',
				'type'    => 'text',
			],
			[
				'title'   => esc_html__( 'Required state indicator', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enter a string to indicate the required state of a field.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_required_indicator',
				'default' => '*',
				'class'   => 'tcglobal7',
				'type'    => 'text',
			],
			[
				'title'   => esc_html__( 'Required state indicator position', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Select the placement of the Required state indicator', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_required_indicator_position',
				'class'   => 'tcglobal7 chosen_select',
				'css'     => 'min-width:300px;',
				'default' => 'left',
				'type'    => 'select',
				'options' => [
					'left'  => esc_html__( 'Left of the label', 'woocommerce-tm-extra-product-options' ),
					'right' => esc_html__( 'Right of the label', 'woocommerce-tm-extra-product-options' ),
				],
			],
			[
				'title'   => esc_html__( 'Include tax string suffix on totals box', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enable this to add the WooCommerce tax suffix on the totals box', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_tax_string_suffix',
				'default' => 'no',
				'class'   => 'tcglobal3',
				'type'    => 'checkbox',
			],
			[
				'title'   => esc_html__( 'Include the WooCommerce Price display suffix on totals box', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enable this to add the WooCommerce Price display suffix on the totals box.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_wc_price_suffix',
				'default' => 'no',
				'class'   => 'tcglobal3',
				'type'    => 'checkbox',
			],
			[
				'title'   => esc_html__( 'jQuery selector for main product image', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'This is used to change the product image.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_product_image_selector',
				'default' => '',
				'class'   => 'tcglobal3',
				'type'    => 'text',
			],
			[
				'title'   => esc_html__( 'Product image replacement mode', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Self mode replaces the actual image and Inline appends new image elements.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_product_image_mode',
				'class'   => 'tcglobal3 chosen_select',
				'css'     => 'min-width:300px;',
				'default' => 'self',
				'type'    => 'select',
				'options' => [
					'self'   => esc_html__( 'Self mode', 'woocommerce-tm-extra-product-options' ),
					'inline' => esc_html__( 'Inline mode', 'woocommerce-tm-extra-product-options' ),
				],
			],
			[
				'title'   => esc_html__( 'Move out of stock message', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'This is moves the out of stock message when styled variations are used just below them.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_move_out_of_stock',
				'default' => 'no',
				'class'   => 'tcglobal3',
				'type'    => 'checkbox',
			],
			[
				'title'   => esc_html__( 'Use internal variation price', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Use this if your variable products have a lot of options to improve performance. Note that this may cause issues with discount or currency plugins.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_no_variation_prices_array',
				'default' => 'no',
				'class'   => 'tcglobal3',
				'type'    => 'checkbox',
			],
			[
				'title'   => esc_html__( 'Enable plugin interface on product edit page for roles', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Select the roles that will have access to the plugin interface while on the edit product page. The Admininstrator role always has access.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_hide_product_enabled',
				'class'   => 'tcglobal2 chosen_select',
				'css'     => 'min-width:300px;',
				'default' => '',
				'type'    => 'multiselect',
				'options' => themecomplete_get_roles( [ 'administrator', '@everyone', '@loggedin' ] ),
			],
			[
				'title'   => esc_html__( 'Hide override settings on products', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enable this to hide the settings tab on the product edit screen', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_hide_product_settings',
				'default' => 'no',
				'class'   => 'tcglobal2',
				'type'    => 'checkbox',
			],
			[
				'title'   => esc_html__( 'Hide Builder mode on products', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enable this to hide the builder tab on the product edit screen', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_hide_product_builder_mode',
				'default' => 'no',
				'class'   => 'tcglobal2',
				'type'    => 'checkbox',
			],
			[
				'title'   => esc_html__( 'Hide Normal mode on products', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enable this to hide the normal tab on the product edit screen', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_hide_product_normal_mode',
				'default' => 'no',
				'class'   => 'tcglobal2',
				'type'    => 'checkbox',
			],

			[
				'title'   => esc_html__( 'Enable WP Rocket CDN', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Check to enable the use of WP Rocket cdn for the plugin images if it is active.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_cdn_rocket',
				'default' => 'yes',
				'class'   => 'tcglobal9',
				'type'    => 'checkbox',
			],
			[
				'title'   => esc_html__( 'Enable Jetpack CDN', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Check to enable the use of Jetpack cdn for the plugin images if it is active.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_cdn_jetpack',
				'default' => 'no',
				'class'   => 'tcglobal9',
				'type'    => 'checkbox',
			],

			[
				'type' => 'tm_sectionend',
				'id'   => 'epo_page_options',
			],

		];
	}

	/**
	 * Elements settings
	 *
	 * @param string $setting The name of the setting.
	 * @param string $label The label for the section.
	 * @since 1.0
	 */
	public function get_setting_elements( $setting, $label ) {
		return [
			[
				'type'  => 'tm_title',
				'id'    => 'epo_page_options',
				'desc'  => '<span tabindex="0" data-menu="tcelements1" class="tm-section-menu-item">' . esc_html__( 'General', 'woocommerce-tm-extra-product-options' ) . '</span>' .
						'<span tabindex="0" data-menu="tcelements2" class="tm-section-menu-item">' . esc_html__( 'Radio buttons', 'woocommerce-tm-extra-product-options' ) . '</span>' .
						'<span tabindex="0" data-menu="tcelements3" class="tm-section-menu-item">' . esc_html__( 'Datepicker', 'woocommerce-tm-extra-product-options' ) . '</span>' .
						'<span tabindex="0" data-menu="tcelements4" class="tm-section-menu-item">' . esc_html__( 'Text', 'woocommerce-tm-extra-product-options' ) . '</span>' .
						'<span tabindex="0" data-menu="tcelements5" class="tm-section-menu-item">' . esc_html__( 'Upload', 'woocommerce-tm-extra-product-options' ) . '</span>' .
						'<span tabindex="0" data-menu="tcelements6" class="tm-section-menu-item">' . esc_html__( 'Product', 'woocommerce-tm-extra-product-options' ) . '</span>',
				'title' => $label,
			],
			[
				'title'   => esc_html__( 'Tooltip max width', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Set the max width of the tooltip that appears on the elements.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_tooltip_max_width',
				'class'   => 'tcelements1',
				'default' => '340px',
				'type'    => 'text',
			],
			[
				'title'   => esc_html__( 'Image mode', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Set the image mode that will be used for various image related functionality.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_image_mode',
				'class'   => 'tcelements1 chosen_select',
				'css'     => 'min-width:300px;',
				'default' => 'relative',
				'type'    => 'select',
				'options' => [
					''         => esc_html__( 'Absolute URL', 'woocommerce-tm-extra-product-options' ),
					'relative' => esc_html__( 'Relative URL', 'woocommerce-tm-extra-product-options' ),
				],
			],
			[
				'title'   => esc_html__( 'Retrieve image sizes for image replacements', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Disable this for slow servers or large amounts of images.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_retrieve_image_sizes',
				'default' => 'no',
				'class'   => 'tcelements1',
				'type'    => 'checkbox',
			],
			[
				'title'   => esc_html__( 'Radio button undo button', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Globally override the undo button for radio buttons', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_radio_undo_button',
				'class'   => 'tcelements2 chosen_select',
				'css'     => 'min-width:300px;',
				'default' => '',
				'type'    => 'select',
				'options' => [
					''        => esc_html__( 'Use field value', 'woocommerce-tm-extra-product-options' ),
					'enable'  => esc_html__( 'Enable', 'woocommerce-tm-extra-product-options' ),
					'disable' => esc_html__( 'Disable', 'woocommerce-tm-extra-product-options' ),

				],
			],
			[
				'title'   => esc_html__( 'Datepicker theme', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Select the theme for the datepicker.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_datepicker_theme',
				'class'   => 'tcelements3 chosen_select',
				'css'     => 'min-width:300px;',
				'default' => '',
				'type'    => 'select',
				'options' => [
					''          => esc_html__( 'Use field value', 'woocommerce-tm-extra-product-options' ),
					'epo'       => esc_html__( 'Epo White', 'woocommerce-tm-extra-product-options' ),
					'epo-black' => esc_html__( 'Epo Black', 'woocommerce-tm-extra-product-options' ),
				],
			],
			[
				'title'   => esc_html__( 'Datepicker size', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Select the size of the datepicker.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_datepicker_size',
				'class'   => 'tcglobtcelements3al4 chosen_select',
				'css'     => 'min-width:300px;',
				'default' => '',
				'type'    => 'select',
				'options' => [
					''       => esc_html__( 'Use field value', 'woocommerce-tm-extra-product-options' ),
					'small'  => esc_html__( 'Small', 'woocommerce-tm-extra-product-options' ),
					'medium' => esc_html__( 'Medium', 'woocommerce-tm-extra-product-options' ),
					'large'  => esc_html__( 'Large', 'woocommerce-tm-extra-product-options' ),
				],
			],
			[
				'title'   => esc_html__( 'Datepicker position', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Select the position of the datepicker.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_datepicker_position',
				'class'   => 'tcelements3 chosen_select',
				'css'     => 'min-width:300px;',
				'default' => '',
				'type'    => 'select',
				'options' => [
					''       => esc_html__( 'Use field value', 'woocommerce-tm-extra-product-options' ),
					'normal' => esc_html__( 'Normal', 'woocommerce-tm-extra-product-options' ),
					'top'    => esc_html__( 'Top of screen', 'woocommerce-tm-extra-product-options' ),
					'bottom' => esc_html__( 'Bottom of screen', 'woocommerce-tm-extra-product-options' ),
				],
			],
			[
				'title'   => esc_html__( 'Minimum characters for text-field and text-areas', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enter a value for the minimum characters the user must enter.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_min_chars',
				'default' => '',
				'class'   => 'tcelements4',
				'type'    => 'number',
			],
			[
				'title'   => esc_html__( 'Maximum characters for text-field and text-areas', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enter a value for the minimum characters the user must enter.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_max_chars',
				'default' => '',
				'class'   => 'tcelements4',
				'type'    => 'number',
			],
			[
				'title'   => esc_html__( 'Upload element inline Image preview', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enable inline preview of the image that will be uploaded.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_upload_inline_image_preview',
				'default' => 'no',
				'class'   => 'tcelements5',
				'type'    => 'checkbox',
			],
			[
				'title'   => esc_html__( 'Scroll to the product element upon selection', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enable to scroll the viewport to the product element.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_product_element_scroll',
				'default' => 'yes',
				'class'   => 'tcelements6',
				'type'    => 'checkbox',
			],
			[
				'title'   => esc_html__( 'Product element scroll offset', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enter a value for the scroll offset when selecting a choice for the product element.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_product_element_scroll_offset',
				'default' => '-100',
				'class'   => 'tcelements6',
				'type'    => 'number',
			],
			[
				'title'   => esc_html__( 'Sync associated product quantity with main product quantity', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enable to have the quantities of the associated products to be a multiple of the main product quantity.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_product_element_quantity_sync',
				'default' => 'yes',
				'class'   => 'tcelements6',
				'type'    => 'checkbox',
			],

			[
				'type' => 'tm_sectionend',
				'id'   => 'epo_page_options',
			],

		];
	}

	/**
	 * Other settings
	 *
	 * @param string $setting The name of the setting.
	 * @param string $label The label for the section.
	 * @since 1.0
	 */
	public function get_setting_other( $setting, $label ) {
		$settings = [];
		$other    = $this->get_other_settings();
		foreach ( $other as $key => $setting ) {
			$settings = array_merge( $settings, $setting );
		}

		return $settings;
	}

	/**
	 * Envato token url
	 *
	 * @since 1.0
	 */
	private function get_generate_token_url() {
		return 'https://build.envato.com/create-token/?' . implode(
			'&',
			array_map(
				function ( $val ) {
					return $val . '=t';
				},
				array_keys(
					apply_filters(
						'envato_market_required_permissions',
						[
							'default'           => 'View and search Envato sites',
							'purchase:download' => 'Download your purchased items',
							'purchase:list'     => 'List purchases you\'ve made',
						]
					)
				)
			)
		);
	}

	/**
	 * License settings
	 *
	 * @param string $setting The name of the setting.
	 * @param string $label The label for the section.
	 * @since 1.0
	 */
	public function get_setting_license( $setting, $label ) {
		$is_active         = THEMECOMPLETE_EPO_LICENSE()->get_license();
		$is_hidden         = defined( 'TC_CLIENT_MODE' );
		$_license_settings = ( ! defined( 'TM_DISABLE_LICENSE' ) ) ?
			[
				[
					'type'  => 'tm_title',
					'id'    => 'epo_page_options',
					'title' => $label,
				],
				[
					'title'   => esc_html__( 'Username', 'woocommerce-tm-extra-product-options' ),
					'desc'    => esc_html__( 'Your Envato username.', 'woocommerce-tm-extra-product-options' ),
					'id'      => 'tm_epo_envato_username',
					'default' => '',
					'class'   => ( $is_hidden ? 'hidden' : '' ),
					'type'    => ( $is_hidden ? 'password' : 'text' ),
				],
				[
					'title'   => esc_html__( 'Envato Personal Token', 'woocommerce-tm-extra-product-options' ),
					/* translators: %s "clicking this link" */
					'desc'    => '<p>' . sprintf( esc_html__( 'You can generate an Envato Personal Token by %s', 'woocommerce-tm-extra-product-options' ), '<a href="' . esc_url( $this->get_generate_token_url() ) . '" target="_blank">' . esc_html__( 'clicking this link', 'woocommerce-tm-extra-product-options' ) . '</a>' ) . '</p>',
					'id'      => 'tm_epo_envato_apikey',
					'default' => '',
					'class'   => ( $is_hidden ? 'hidden' : '' ),
					'type'    => ( $is_hidden ? 'password' : 'text' ),
				],
				[
					'title'   => esc_html__( 'Purchase code', 'woocommerce-tm-extra-product-options' ),
					/* translators: %s "click this link" */
					'desc'    => '<p>' . sprintf( esc_html__( 'To find out how to access your purchase code you can %s', 'woocommerce-tm-extra-product-options' ), '<a href="' . esc_url( 'https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-' ) . '" target="_blank">' . esc_html__( 'click this link', 'woocommerce-tm-extra-product-options' ) . '</a>' ) . '</p>'
								. '<span class="tm-license-button">'

								. '<button type="button" class="' . ( THEMECOMPLETE_EPO_LICENSE()->get_license() ? '' : 'tm-hidden ' ) . 'tc tc-button tm-deactivate-license" id="tm_deactivate_license">' . esc_html__( 'Deactivate License', 'woocommerce-tm-extra-product-options' ) . '</button>'
								. '<button type="button" class="' . ( THEMECOMPLETE_EPO_LICENSE()->get_license() ? 'tm-hidden ' : '' ) . 'tc tc-button tm-activate-license" id="tm_activate_license">' . esc_html__( 'Activate License', 'woocommerce-tm-extra-product-options' ) . '</button>'

								. '</span>'
								. '<span class="tm-license-result">'
								. ( ( THEMECOMPLETE_EPO_LICENSE()->get_license() ) ?
							"<div class='activated'><p>" . esc_html__( 'License activated.', 'woocommerce-tm-extra-product-options' ) . '</p></div>'
							: ''
								)
								. '</span>',
					'id'      => 'tm_epo_envato_purchasecode',
					'default' => '',
					'class'   => ( $is_hidden ? 'hidden' : '' ),
					'type'    => ( $is_hidden ? 'password' : 'text' ),
				],
				[
					'title'   => esc_html__( 'Consent', 'woocommerce-tm-extra-product-options' ),
					'desc'    => esc_html__( 'I agree that the license data will be transmitted to the license server.', 'woocommerce-tm-extra-product-options' ),
					'id'      => 'tm_epo_consent_for_transmit',
					'class'   => ( $is_hidden ? 'hidden' : '' ),
					'default' => 'no',
					'type'    => 'checkbox',
				],
				[
					'type' => 'tm_sectionend',
					'id'   => 'epo_page_options',
				],
			] : [];

		return $_license_settings;
	}

	/**
	 * Get allowed file types
	 *
	 * @since 1.0
	 */
	public function get_allowed_types() {
		$types            = [];
		$wp_get_ext_types = wp_get_ext_types();
		$types['@']       = esc_html__( 'Use allowed file types from WordPress', 'woocommerce-tm-extra-product-options' );
		foreach ( $wp_get_ext_types as $key => $value ) {
			$types[ '@' . $key ] = $key . ' ' . esc_html__( 'files', 'woocommerce-tm-extra-product-options' );
			foreach ( $value as $key2 => $value2 ) {
				$types[ $value2 ] = $value2;
			}
		}

		return $types;
	}

	/**
	 * Upload settings
	 *
	 * @param string $setting The name of the setting.
	 * @param string $label The label for the section.
	 * @since 1.0
	 */
	public function get_setting_upload( $setting, $label ) {

		$html = '<div class="tm-mn-header"><div class="tm-mn-path">'
				. '<a class="tm-mn-movetodir tc tc-button" data-tm-dir="" href="#">' . esc_html__( 'Enable File manager', 'woocommerce-tm-extra-product-options' ) . '</a>'
				. '</div></div>';

		$_upload_settings =
			[
				[
					'type'  => 'tm_title',
					'id'    => 'epo_page_options',
					'title' => $label,
				],
				[
					'title'   => esc_html__( 'Upload folder', 'woocommerce-tm-extra-product-options' ),
					'desc'    => esc_html__( 'Changing this will only affect future uploads.', 'woocommerce-tm-extra-product-options' ),
					'id'      => 'tm_epo_upload_folder',
					'default' => 'extra_product_options',
					'type'    => 'text',
				],
				[
					'title'   => esc_html__( 'Enable pop-up message on uploads', 'woocommerce-tm-extra-product-options' ),
					'desc'    => esc_html__( 'Enables a pop-up when uploads are made.', 'woocommerce-tm-extra-product-options' ),
					'id'      => 'tm_epo_upload_popup',
					'default' => 'no',
					'type'    => 'checkbox',
				],
				[
					'title'   => esc_html__( 'Enable upload success message', 'woocommerce-tm-extra-product-options' ),
					'desc'    => esc_html__( 'Indicates if the upload was successful with a message.', 'woocommerce-tm-extra-product-options' ),
					'id'      => 'tm_epo_upload_success_message',
					'default' => 'yes',
					'type'    => 'checkbox',
				],
				[
					'title'             => esc_html__( 'Allowed file types', 'woocommerce-tm-extra-product-options' ),
					'desc'              => esc_html__( 'Select which file types the user will be allowed to upload.', 'woocommerce-tm-extra-product-options' ),
					'id'                => 'tm_epo_allowed_file_types',
					'type'              => 'multiselect',
					'class'             => 'wc-enhanced-select',
					'css'               => 'width: 450px;',
					'default'           => '@',
					'options'           => $this->get_allowed_types(),
					'custom_attributes' => [
						'data-placeholder' => esc_html__( 'Select file types', 'woocommerce-tm-extra-product-options' ),
					],
				],
				[
					'title'   => esc_html__( 'Custom types', 'woocommerce-tm-extra-product-options' ),
					'desc'    => esc_html__( 'Select custom file types the user will be allowed to upload separated by commas.', 'woocommerce-tm-extra-product-options' ),
					'id'      => 'tm_epo_custom_file_types',
					'default' => '',
					'type'    => 'text',
				],
				[
					'type'  => 'tm_html',
					'id'    => 'epo_page_options_html',
					'title' => esc_html__( 'File manager', 'woocommerce-tm-extra-product-options' ),
					'html'  => $html,
				],
				[
					'type' => 'tm_sectionend',
					'id'   => 'epo_page_options',
				],
			];

		return $_upload_settings;
	}

	/**
	 * Code settings
	 *
	 * @param string $setting The name of the setting.
	 * @param string $label The label for the section.
	 * @since 1.0
	 */
	public function get_setting_code( $setting, $label ) {
		return [
			[
				'type'  => 'tm_title',
				'id'    => 'epo_page_options',
				'title' => $label,
			],
			[
				'title'   => esc_html__( 'CSS code', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Only enter pure CSS code without and style tags', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_css_code',
				'default' => '',
				'type'    => 'textarea',
				'class'   => 'tc-admin-textarea',
			],
			[
				'title'   => esc_html__( 'JavaScript code', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Only enter pure JavaScript code without and script tags', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_js_code',
				'default' => '',
				'type'    => 'textarea',
				'class'   => 'tc-admin-textarea',
			],
			[
				'type' => 'tm_sectionend',
				'id'   => 'epo_page_options',
			],
		];
	}

	/**
	 * Math Formula Constants settings
	 *
	 * @param string $setting The name of the setting.
	 * @param string $label The label for the section.
	 * @since 6.0
	 */
	public function get_setting_math( $setting, $label ) {
		$html = '<div class="tm-mn-header"><div class="tm-mn-path">'
				. '<button type="button" class="tc-add-constant tc tc-button">' . esc_html__( 'Add Constant', 'woocommerce-tm-extra-product-options' ) . '</button>'
				. '</div></div>';
		return [
			[
				'type'  => 'tm_title',
				'id'    => 'epo_page_options',
				'title' => $label,
			],
			[
				'id'      => 'tm_epo_math',
				'default' => '',
				'type'    => 'textarea',
				'class'   => 'tc-epo-math tc-admin-textarea tm-hidden',
			],
			[
				'type'  => 'tm_html',
				'id'    => 'epo_page_options_math_html',
				'title' => esc_html__( 'Math Formula Constants', 'woocommerce-tm-extra-product-options' ),
				'html'  => $html,
			],
			[
				'type' => 'tm_sectionend',
				'id'   => 'epo_page_options',
			],
		];
	}
}
