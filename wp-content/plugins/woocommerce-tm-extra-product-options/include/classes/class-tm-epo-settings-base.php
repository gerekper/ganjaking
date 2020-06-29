<?php
/**
 * Extra Product Options Settings class
 *
 * @package Extra Product Options/Classes
 * @version 4.9
 */
defined( 'ABSPATH' ) || exit;

final class THEMECOMPLETE_EPO_SETTINGS_base {

	/**
	 * The single instance of the class
	 *
	 * @since 1.0
	 */
	protected static $_instance = NULL;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Class Constructor
	 *
	 * @since 1.0
	 */
	public function __construct() {
	}

	public function init() {
	}

	/**
	 * Get settings array
	 *
	 * @since 1.0
	 */
	public function get_setting_array( $setting, $label ) {
		$method = "_get_setting_" . $setting;
		if ( is_callable( array( $this, $method ) ) ) {
			// This may return undefined function wp_get_current_user() when it is used without @ on some configurations
			$settings = $this->$method( $setting, $label );
			if ( ! is_array( $settings ) ) {
				$settings = array();
			}
			foreach ( $settings as $key => $setting_array ) {
				if ( isset( $setting_array['desc'] ) && $setting_array['desc'] !== '' ) {
					$settings[ $key ]['desc'] = '<span class="description">' . $setting_array['desc'] . '</span>';
				}
			}
			$settings = apply_filters( 'tm_epo_settings_' . $setting, $settings );

			return $settings;
		}

		return array();

	}

	/**
	 * Set settings categories
	 *
	 * @since 1.0
	 */
	public function settings_options() {
		$settings_options = array(
			"general" => array( "tcfa tcfa-cog", esc_html__( 'General', 'woocommerce-tm-extra-product-options' ) ),
			"display" => array( "tcfa tcfa-tv", esc_html__( 'Display', 'woocommerce-tm-extra-product-options' ) ),
			"cart"    => array( "tcfa tcfa-shopping-cart", esc_html__( 'Cart', 'woocommerce-tm-extra-product-options' ) ),
			"order"   => array( "tcfa tcfa-truck-pickup", esc_html__( 'Order', 'woocommerce-tm-extra-product-options' ) ),
			"string"  => array( "tcfa tcfa-font", esc_html__( 'Strings', 'woocommerce-tm-extra-product-options' ) ),
			"style"   => array( "tcfa tcfa-border-style", esc_html__( 'Style', 'woocommerce-tm-extra-product-options' ) ),
			"global"  => array( "tcfa tcfa-globe", esc_html__( 'Global', 'woocommerce-tm-extra-product-options' ) ),
			"other"   => "other",
			"license" => array( "tcfa tcfa-id-badge", esc_html__( 'License', 'woocommerce-tm-extra-product-options' ) ),
			"upload"  => array( "tcfa tcfa-cloud-upload-alt", esc_html__( 'Upload manager', 'woocommerce-tm-extra-product-options' ) ),
			"code"    => array( "tcfa tcfa-code", esc_html__( 'Custom code', 'woocommerce-tm-extra-product-options' ) ),
		);

		return $settings_options;
	}

	/**
	 * Get plugin settings
	 *
	 * @since 1.0
	 */
	public function plugin_settings() {
		$settings = array();
		$o        = $this->settings_options();
		$ids      = array();
		foreach ( $o as $key => $value ) {
			$settings[ $key ] = $this->get_setting_array( $key, $value );
		}

		foreach ( $settings as $key => $value ) {
			foreach ( $value as $key2 => $value2 ) {
				if ( isset( $value2['id'] ) && isset( $value2['default'] ) && $value2['id'] !== 'epo_page_options' ) {
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
		$headers = array();

		return apply_filters( 'tm_epo_settings_headers', $headers );
	}

	/**
	 * Get "other" settings
	 *
	 * @since 1.0
	 */
	public function get_other_settings() {
		$settings = array();

		return apply_filters( 'tm_epo_settings_settings', $settings );
	}

	/**
	 * General settings
	 *
	 * @since 1.0
	 */
	public function _get_setting_general( $setting, $label ) {
		return array(
			array(
				'type'  => 'tm_title',
				'id'    => 'epo_page_options',
				'desc'  => '<span tabindex="0" data-menu="tcinit" class="tm-section-menu-item">' . esc_html__( 'Initialization', 'woocommerce-tm-extra-product-options' ) . '</span>' .
				           '<span tabindex="0" data-menu="tcftb" class="tm-section-menu-item">' . esc_html__( 'Final total box', 'woocommerce-tm-extra-product-options' ) . '</span>' .
				           '<span tabindex="0" data-menu="tcvarious" class="tm-section-menu-item">' . esc_html__( 'Various', 'woocommerce-tm-extra-product-options' ) . '</span>',
				'title' => $label,
			),
			array(
				'title'   => esc_html__( 'Enable front-end for roles', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Select the roles that will have access to the extra options.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_roles_enabled',
				'class'   => 'tcinit chosen_select',
				'css'     => 'min-width:300px;',
				'default' => '@everyone',
				'type'    => 'multiselect',
				'options' => themecomplete_get_roles(),
			),
			array(
				'title'   => esc_html__( 'Disable front-end for roles', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Select the roles that will not have access to the extra options.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_roles_disabled',
				'class'   => 'tcinit chosen_select',
				'css'     => 'min-width:300px;',
				'default' => '',
				'type'    => 'multiselect',
				'options' => themecomplete_get_roles(),
			),
			array(
				'title'   => esc_html__( 'Final total box', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Select when to show the final total box', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_final_total_box',
				'class'   => 'tcftb chosen_select',
				'css'     => 'min-width:300px;',
				'default' => 'normal',
				'type'    => 'select',
				'options' => array(
					'normal'                => esc_html__( 'Show Both Final and Options total box', 'woocommerce-tm-extra-product-options' ),
					'options'               => esc_html__( 'Show only Options total', 'woocommerce-tm-extra-product-options' ),
					'optionsiftotalnotzero' => esc_html__( 'Show only Options total if total is not zero', 'woocommerce-tm-extra-product-options' ),
					'final'                 => esc_html__( 'Show only Final total', 'woocommerce-tm-extra-product-options' ),
					'hideoptionsifzero'     => esc_html__( 'Show Final total and hide Options total if zero', 'woocommerce-tm-extra-product-options' ),
					'hideifoptionsiszero'   => esc_html__( 'Hide Final total box if Options total is zero', 'woocommerce-tm-extra-product-options' ),
					'hide'                  => esc_html__( 'Hide Final total box', 'woocommerce-tm-extra-product-options' ),
					'pxq'                   => esc_html__( 'Always show only Final total (Price x Quantity)', 'woocommerce-tm-extra-product-options' ),
					'disable_change'        => esc_html__( 'Disable but change product prices', 'woocommerce-tm-extra-product-options' ),
					'disable'               => esc_html__( 'Disable', 'woocommerce-tm-extra-product-options' ),
				),
			),
			array(
				'title'   => esc_html__( 'Enable Final total box for all products', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Show the Final total box even when the product has no extra options', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_enable_final_total_box_all',
				'class'   => 'tcftb',
				'default' => 'no',
				'type'    => 'checkbox',
			),
			array(
				'title'   => esc_html__( 'Show Unit price on totals box', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enable this to display the unit price when the totals box is visible', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_show_unit_price',
				'class'   => 'tcftb',
				'default' => 'no',
				'type'    => 'checkbox',
			),
			array(
				'title'   => esc_html__( 'Include Fees on unit price', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enable this to add any Fees to the unit price', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_fees_on_unit_price',
				'class'   => 'tcftb',
				'default' => 'no',
				'type'    => 'checkbox',
			),
			array(
				'title'   => esc_html__( 'Total price as Unit Price', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Make the total price not being multiplied by the product quantity', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_total_price_as_unit_price',
				'class'   => 'tcftb',
				'default' => 'no',
				'type'    => 'checkbox',
			),
			array(
				'title'   => esc_html__( 'Disable lazy load images', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enable this to disable lazy loading images.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_no_lazy_load',
				'default' => 'yes',
				'class'   => 'tcvarious',
				'type'    => 'checkbox',
			),
			array(
				'title'   => esc_html__( 'Enable plugin for WooCommerce shortcodes', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enabling this will load the plugin files to all WordPress pages. Use with caution.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_enable_shortcodes',
				'default' => 'no',
				'class'   => 'tcvarious',
				'type'    => 'checkbox',
			),
			array(
				'title'   => esc_html__( 'Enable shortcodes in options strings', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enabling this will allow the use of shortcodes and HTML code in the options label and dscription text.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_enable_data_shortcodes',
				'default' => 'yes',
				'class'   => 'tcvarious',
				'type'    => 'checkbox',
			),
			array( 'type' => 'tm_sectionend', 'id' => 'epo_page_options' ),
		);
	}

	/**
	 * Display settings
	 *
	 * @since 1.0
	 */
	public function _get_setting_display( $setting, $label ) {
		return array(
			array(
				'type' => 'tm_title',
				'id'   => 'epo_page_options',
				'desc' => '<span tabindex="0" data-menu="tcdisplay" class="tm-section-menu-item">' . esc_html__( 'Display', 'woocommerce-tm-extra-product-options' ) . '</span>' .
				          '<span tabindex="0" data-menu="tcplacement" class="tm-section-menu-item">' . esc_html__( 'Placement', 'woocommerce-tm-extra-product-options' ) . '</span>' .
				          '<span tabindex="0" data-menu="tcprice" class="tm-section-menu-item">' . esc_html__( 'Price', 'woocommerce-tm-extra-product-options' ) . '</span>' .
				          '<span tabindex="0" data-menu="tcftbox" class="tm-section-menu-item">' . esc_html__( 'Floating Totals box', 'woocommerce-tm-extra-product-options' ) . '</span>' .
				          '<span tabindex="0" data-menu="tcanimation" class="tm-section-menu-item">' . esc_html__( 'Animation', 'woocommerce-tm-extra-product-options' ) . '</span>' .
				          '<span tabindex="0" data-menu="tcvarious2" class="tm-section-menu-item">' . esc_html__( 'Various', 'woocommerce-tm-extra-product-options' ) . '</span>',

				'title' => $label,
			),
			array(
				'title'   => esc_html__( 'Display', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'This controls how your fields are displayed on the front-end. If you choose "Show using action hooks" you have to manually write the code to your theme or plugin to display the fields and the placement settings below will not work. If you use Composite Products extension you must leave this setting to "Normal" otherwise the extra options cannot be displayed on the composite product bundles. See more at the documentation.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_display',
				'class'   => 'tcdisplay chosen_select',
				'css'     => 'min-width:300px;',
				'default' => 'normal',
				'type'    => 'select',
				'options' => array(
					'normal' => esc_html__( 'Normal', 'woocommerce-tm-extra-product-options' ),
					'action' => esc_html__( 'Show using action hooks', 'woocommerce-tm-extra-product-options' ),
				),
			),
			array(
				'title'   => esc_html__( 'Extra Options placement', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Select where you want the extra options to appear.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_options_placement',
				'class'   => 'tcplacement chosen_select',
				'css'     => 'min-width:300px;',
				'default' => 'woocommerce_before_add_to_cart_button',
				'type'    => 'select',
				'options' => array(
					'woocommerce_before_add_to_cart_button' => esc_html__( 'Before add to cart button', 'woocommerce-tm-extra-product-options' ),
					'woocommerce_after_add_to_cart_button'  => esc_html__( 'After add to cart button', 'woocommerce-tm-extra-product-options' ),

					'woocommerce_before_add_to_cart_form' => esc_html__( 'Before cart form', 'woocommerce-tm-extra-product-options' ),
					'woocommerce_after_add_to_cart_form'  => esc_html__( 'After cart form', 'woocommerce-tm-extra-product-options' ),

					'woocommerce_before_single_product' => esc_html__( 'Before product', 'woocommerce-tm-extra-product-options' ),
					'woocommerce_after_single_product'  => esc_html__( 'After product', 'woocommerce-tm-extra-product-options' ),

					'woocommerce_before_single_product_summary' => esc_html__( 'Before product summary', 'woocommerce-tm-extra-product-options' ),
					'woocommerce_after_single_product_summary'  => esc_html__( 'After product summary', 'woocommerce-tm-extra-product-options' ),

					'woocommerce_product_thumbnails' => esc_html__( 'After product image', 'woocommerce-tm-extra-product-options' ),

					'custom' => esc_html__( 'Custom hook', 'woocommerce-tm-extra-product-options' ),
				),
			),
			array(
				'title'   => esc_html__( 'Extra Options placement custom hook', 'woocommerce-tm-extra-product-options' ),
				'desc'    => '',
				'class'   => 'tcplacement',
				'id'      => 'tm_epo_options_placement_custom_hook',
				'default' => '',
				'type'    => 'text',
			),
			array(
				'title'   => esc_html__( 'Extra Options placement hook priority', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Select the Extra Options placement hook priority', 'woocommerce-tm-extra-product-options' ),
				'class'   => 'tcplacement',
				'id'      => 'tm_epo_options_placement_hook_priority',
				'default' => '50',
				'type'    => 'number',
			),
			array(
				'title'   => esc_html__( 'Totals box placement', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Select where you want the Totals box to appear.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_totals_box_placement',
				'class'   => 'tcplacement chosen_select',
				'css'     => 'min-width:300px;',
				'default' => 'woocommerce_before_add_to_cart_button',
				'type'    => 'select',
				'options' => array(
					'woocommerce_before_add_to_cart_button' => esc_html__( 'Before add to cart button', 'woocommerce-tm-extra-product-options' ),
					'woocommerce_after_add_to_cart_button'  => esc_html__( 'After add to cart button', 'woocommerce-tm-extra-product-options' ),

					'woocommerce_before_add_to_cart_form' => esc_html__( 'Before cart form', 'woocommerce-tm-extra-product-options' ),
					'woocommerce_after_add_to_cart_form'  => esc_html__( 'After cart form', 'woocommerce-tm-extra-product-options' ),

					'woocommerce_before_single_product' => esc_html__( 'Before product', 'woocommerce-tm-extra-product-options' ),
					'woocommerce_after_single_product'  => esc_html__( 'After product', 'woocommerce-tm-extra-product-options' ),

					'woocommerce_before_single_product_summary' => esc_html__( 'Before product summary', 'woocommerce-tm-extra-product-options' ),
					'woocommerce_after_single_product_summary'  => esc_html__( 'After product summary', 'woocommerce-tm-extra-product-options' ),

					'woocommerce_product_thumbnails' => esc_html__( 'After product image', 'woocommerce-tm-extra-product-options' ),

					'custom' => esc_html__( 'Custom hook', 'woocommerce-tm-extra-product-options' ),
				),
			),
			array(
				'title'   => esc_html__( 'Totals box placement custom hook', 'woocommerce-tm-extra-product-options' ),
				'desc'    => '',
				'class'   => 'tcplacement',
				'id'      => 'tm_epo_totals_box_placement_custom_hook',
				'default' => '',
				'type'    => 'text',
			),
			array(
				'title'   => esc_html__( 'Totals box placement hook priority', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Select the Totals box placement hook priority', 'woocommerce-tm-extra-product-options' ),
				'class'   => 'tcplacement',
				'id'      => 'tm_epo_totals_box_placement_hook_priority',
				'default' => '50',
				'type'    => 'number',
			),
			array(
				'title'   => esc_html__( 'Floating Totals box', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'This will enable a floating box to display your totals box.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_floating_totals_box',
				'class'   => 'tcftbox chosen_select',
				'css'     => 'min-width:300px;',
				'default' => 'disable',
				'type'    => 'select',
				'options' => array(
					'disable'      => esc_html__( 'Disable', 'woocommerce-tm-extra-product-options' ),
					'bottom right' => esc_html__( 'Bottom right', 'woocommerce-tm-extra-product-options' ),
					'bottom left'  => esc_html__( 'Bottom left', 'woocommerce-tm-extra-product-options' ),
					'top right'    => esc_html__( 'Top right', 'woocommerce-tm-extra-product-options' ),
					'top left'     => esc_html__( 'Top left', 'woocommerce-tm-extra-product-options' ),
				),
			),
			array(
				'title'   => esc_html__( 'Floating Totals box visibility', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'This determine the floating totals box visibility.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_floating_totals_box_visibility',
				'class'   => 'tcftbox chosen_select',
				'css'     => 'min-width:300px;',
				'default' => 'always',
				'type'    => 'select',
				'options' => array(
					'always'      => esc_html__( 'Always visible', 'woocommerce-tm-extra-product-options' ),
					'afterscroll' => esc_html__( 'Visble after scrolling the page', 'woocommerce-tm-extra-product-options' ),
				),
			),
			array(
				'title'   => esc_html__( 'Pixels amount needed to scroll', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Select the number of pixels the page needs to scroll for the floating totals to become visible.', 'woocommerce-tm-extra-product-options' ),
				'class'   => 'tcftbox',
				'id'      => 'tm_epo_totals_box_pixels',
				'default' => '100',
				'type'    => 'number',
			),
			array(
				'title'   => esc_html__( 'Add to cart button on floating totals box', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Display the add to cart button on floating box.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_floating_totals_box_add_button',
				'default' => 'no',
				'class'   => 'tcftbox',
				'type'    => 'checkbox',
			),
			array(
				'title'   => esc_html__( 'Change original product price', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Check to overwrite the original product price when the price is changing.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_change_original_price',
				'default' => 'no',
				'class'   => 'tcprice',
				'type'    => 'checkbox',
			),
			array(
				'title'   => esc_html__( 'Change variation price', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Check to overwrite the variation price when the price is changing.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_change_variation_price',
				'default' => 'no',
				'class'   => 'tcprice',
				'type'    => 'checkbox',
			),
			array(
				'title'   => esc_html__( 'Force Select Options', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'This changes the add to cart button on shop and archive pages to display select options when the product has extra product options. Enabling this will remove the ajax functionality.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_force_select_options',
				'class'   => 'tcdisplay chosen_select',
				'css'     => 'min-width:300px;',
				'default' => 'normal',
				'type'    => 'select',
				'options' => array(
					'normal'  => esc_html__( 'Disable', 'woocommerce-tm-extra-product-options' ),
					'display' => esc_html__( 'Enable', 'woocommerce-tm-extra-product-options' ),
				),
			),
			array(
				'title'   => esc_html__( 'Enable extra options in shop and category view', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Check to enable the display of extra options on the shop page and category view. This setting is theme dependent and some aspects may not work as expected.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_enable_in_shop',
				'default' => 'no',
				'class'   => 'tcdisplay',
				'type'    => 'checkbox',
			),
			array(
				'title'   => esc_html__( 'Remove Free price label', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Check to remove Free price label when product has extra options', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_remove_free_price_label',
				'class'   => 'tcprice',
				'default' => 'no',
				'type'    => 'checkbox',
			),
			array(
				'title'   => esc_html__( 'Hide uploaded file path', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Check to hide the uploaded file path from users (in the Order).', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_hide_upload_file_path',
				'class'   => 'tcvarious2',
				'default' => 'yes',
				'type'    => 'checkbox',
			),
			array(
				'title'   => esc_html__( 'Use progressive display on options', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enabling this will hide the options on the product page until JavaScript is initialized. This is a fail-safe setting and we recommend to be active.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_progressive_display',
				'class'   => 'tcanimation',
				'default' => 'yes',
				'type'    => 'checkbox',
			),
			array(
				'title'   => esc_html__( 'Animation delay', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'How long the animation will take in milliseconds', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_animation_delay',
				'class'   => 'tcanimation',
				'default' => '100',
				'type'    => 'text',
			),
			array(
				'title'   => esc_html__( 'Start Animation delay', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'The delay until the animation starts in milliseconds', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_start_animation_delay',
				'class'   => 'tcanimation',
				'default' => '0',
				'type'    => 'text',
			),
			array(
				'title'   => esc_html__( 'Show quantity selector only for elements with a value', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Check show quantity selector only for elements with a value.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_show_only_active_quantities',
				'class'   => 'tcdisplay',
				'default' => 'yes',
				'type'    => 'checkbox',
			),
			array(
				'title'   => esc_html__( 'Hide add-to-cart button until an option is chosen', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Check this to show the add to cart button only when at least one option is filled.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_hide_add_cart_button',
				'class'   => 'tcdisplay',
				'default' => 'no',
				'type'    => 'checkbox',
			),
			array(
				'title'   => esc_html__( 'Show full width label for select boxes.', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Check this to force select boxes to be full width.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_select_fullwidth',
				'class'   => 'tcdisplay',
				'default' => 'yes',
				'type'    => 'checkbox',
			),
			array(
				'title'   => esc_html__( 'Show description for radio buttons and checkboxes inline.', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Check this to disable showing descirption as a tooltip and show it inline instead.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_description_inline',
				'class'   => 'tcdisplay',
				'default' => 'no',
				'type'    => 'checkbox',
			),
			array(
				'title'   => esc_html__( 'Hide choice label when using the swatch mode for radio buttons and checkboxes.', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Check this to hdie the choice label when using the swatch mode for radio buttons and checkboxes.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_swatch_hide_label',
				'class'   => 'tcdisplay',
				'default' => 'yes',
				'type'    => 'checkbox',
			),
			array(
				'title'   => esc_html__( 'Auto hide price if zero', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Check this to globally hide the price display if it is zero.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_auto_hide_price_if_zero',
				'class'   => 'tcprice',
				'default' => 'no',
				'type'    => 'checkbox',
			),
			array(
				'title'   => esc_html__( 'Show prices inside select box choices', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Check this to show the price of the select box options if the price type is fixed.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_show_price_inside_option',
				'class'   => 'tcprice',
				'default' => 'no',
				'type'    => 'checkbox',
			),
			array(
				'title'   => esc_html__( 'Show prices inside select box choices even if the prices are hidden', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Check this to show the price of the select box options if the price type is fixed and even if the element hides the price.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_show_price_inside_option_hidden_even',
				'class'   => 'tcprice',
				'default' => 'no',
				'type'    => 'checkbox',
			),
			array(
				'title'   => esc_html__( 'Multiply prices inside select box choices with its quantity selector', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Check this to multiply the prices of the select box options with its quantity selector if any.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_multiply_price_inside_option',
				'class'   => 'tcprice',
				'default' => 'yes',
				'type'    => 'checkbox',
			),
			( THEMECOMPLETE_EPO_WPML()->is_active() )
				?
				array(
					'title'   => esc_html__( 'Use translated values when possible on admin Order', 'woocommerce-tm-extra-product-options' ),
					'desc'    => esc_html__( 'Please note that if the options on the Order change or get deleted you will get wrong results by enabling this!', 'woocommerce-tm-extra-product-options' ),
					'id'      => 'tm_epo_wpml_order_translate',
					'class'   => 'tcdisplay',
					'default' => 'no',
					'type'    => 'checkbox',

				)
				: array(),
			array(
				'title'   => esc_html__( 'Include option pricing in product price', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Check this to include the pricing of the options to the product price.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_include_possible_option_pricing',
				'class'   => 'tcprice',
				'default' => 'no',
				'type'    => 'checkbox',
			),
			array(
				'title'   => esc_html__( 'Use the "From" string on displayed product prices', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Check this to alter the price display of a product when it has extra options with prices.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_use_from_on_price',
				'class'   => 'tcvarious2',
				'default' => 'no',
				'type'    => 'checkbox',
			),
			array(
				'title'   => esc_html__( 'Alter generated product structured data', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Alters the generated product structured data. This may produce wrong results if the options use conditional logic!', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_alter_structured_data',
				'class'   => 'tcvarious2',
				'default' => 'no',
				'type'    => 'checkbox',
			),
			array( 'type' => 'tm_sectionend', 'id' => 'epo_page_options' ),
		);
	}

	/**
	 * Cart settings
	 *
	 * @since 1.0
	 */
	public function _get_setting_cart( $setting, $label ) {
		return array(
			array(
				'type'  => 'tm_title',
				'id'    => 'epo_page_options',
				'title' => $label,
			),
			array(
				'title'   => esc_html__( 'Turn off persistent cart', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enable this if the product has a lot of options.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_turn_off_persi_cart',
				'default' => 'no',
				'type'    => 'checkbox',
			),
			array(
				'title'   => esc_html__( 'Clear cart button', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enables or disables the clear cart button', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_clear_cart_button',
				'class'   => 'chosen_select',
				'css'     => 'min-width:300px;',
				'default' => 'normal',
				'type'    => 'select',
				'options' => array(
					'normal' => esc_html__( 'Hide', 'woocommerce-tm-extra-product-options' ),
					'show'   => esc_html__( 'Show', 'woocommerce-tm-extra-product-options' ),

				),
			),
			array(
				'title'   => esc_html__( 'Cart Field Display', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Select how to display your fields in the cart', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_cart_field_display',
				'class'   => 'chosen_select',
				'css'     => 'min-width:300px;',
				'default' => 'normal',
				'type'    => 'select',
				'options' => array(
					'normal'   => esc_html__( 'Normal display', 'woocommerce-tm-extra-product-options' ),
					'link'     => esc_html__( 'Display a pop-up link', 'woocommerce-tm-extra-product-options' ),
					'advanced' => esc_html__( 'Advanced display', 'woocommerce-tm-extra-product-options' ),
				),
			),
			array(
				'title'   => esc_html__( 'Hide extra options in cart', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enables or disables the display of options in the cart.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_hide_options_in_cart',
				'class'   => 'chosen_select',
				'css'     => 'min-width:300px;',
				'default' => 'normal',
				'type'    => 'select',
				'options' => array(
					'normal' => esc_html__( 'Show', 'woocommerce-tm-extra-product-options' ),
					'hide'   => esc_html__( 'Hide', 'woocommerce-tm-extra-product-options' ),

				),
			),
			array(
				'title'   => esc_html__( 'Hide extra options prices in cart', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enables or disables the display of prices of options in the cart.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_hide_options_prices_in_cart',
				'class'   => 'chosen_select',
				'css'     => 'min-width:300px;',
				'default' => 'normal',
				'type'    => 'select',
				'options' => array(
					'normal' => esc_html__( 'Show', 'woocommerce-tm-extra-product-options' ),
					'hide'   => esc_html__( 'Hide', 'woocommerce-tm-extra-product-options' ),

				),
			),
			version_compare( get_option( 'woocommerce_db_version' ), '2.3', '<' ) ?
				array() :
				array(
					'title'   => esc_html__( 'Prevent negative priced products', 'woocommerce-tm-extra-product-options' ),
					'desc'    => esc_html__( 'Prevent adding to the cart negative priced products.', 'woocommerce-tm-extra-product-options' ),
					'id'      => 'tm_epo_no_negative_priced_products',
					'default' => 'no',
					'type'    => 'checkbox',
				),
			array(
				'title'   => esc_html__( 'Prevent zero priced products', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Prevent adding to the cart zero priced products.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_no_zero_priced_products',
				'default' => 'no',
				'type'    => 'checkbox',
			),
			array(
				'title'   => esc_html__( 'Hide checkbox element average price', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'This will hide the average price display on the cart for checkboxes.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_hide_cart_average_price',
				'default' => 'yes',
				'type'    => 'checkbox',
			),
			array(
				'title'   => esc_html__( 'Show image replacement in cart and checkout', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enabling this will show the images of elements that have an image replacement.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_show_image_replacement',
				'default' => 'no',
				'type'    => 'checkbox',
			),
			array(
				'title'   => esc_html__( 'Hide upload file URL in cart and checkout', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enabling this will hide the URL of any uploaded file while in cart and checkout.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_show_hide_uploaded_file_url_cart',
				'default' => 'no',
				'type'    => 'checkbox',
			),
			array(
				'title'   => esc_html__( 'Show uploaded image in cart and checkout', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enabling this will show the uploaded images in cart and checkout.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_show_upload_image_replacement',
				'default' => 'yes',
				'type'    => 'checkbox',
			),
			array(
				'title'   => esc_html__( 'Always use unique values on cart for elements', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enabling this will separate comma separated values for elements. This is mainly used for multiple checkbox choices.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_always_unique_values',
				'default' => 'no',
				'type'    => 'checkbox',
			),
			array( 'type' => 'tm_sectionend', 'id' => 'epo_page_options' ),

		);
	}

	/**
	 * Order settings
	 *
	 * @since 4.8
	 */
	public function _get_setting_order( $setting, $label ) {
		return array(
			array(
				'type'  => 'tm_title',
				'id'    => 'epo_page_options',
				'title' => $label,
			),
			array(
				'title'   => esc_html__( 'Strip html from emails', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Check to strip the html tags from emails', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_strip_html_from_emails',
				'default' => 'yes',
				'type'    => 'checkbox',
			),
			array(
				'title'   => esc_html__( 'Unique meta values', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Check to split items with multiple values to unique lines.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_unique_meta_values',
				'default' => 'no',
				'type'    => 'checkbox',
			),
			array(
				'title'   => esc_html__( 'Prevent options from being sent to emails', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Check to disable options from being sent to emails.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_prevent_options_from_emails',
				'default' => 'no',
				'type'    => 'checkbox',
			),
			array(
				'title'   => esc_html__( 'Disable sending the options upon saving the order', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enable this if you are getting a 500 error when trying to complete the order in the checkout.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_disable_sending_options_in_order',
				'default' => 'yes',
				'type'    => 'checkbox',
			),
			array(
				'title'   => esc_html__( 'Attach upload files to emails', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Check to Attach upload files to emails.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_attach_uploaded_to_emails',
				'default' => 'yes',
				'type'    => 'checkbox',
			),
			array(
				'title'   => esc_html__( 'Disable Options on Order status change', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Check this only if you are getting server errors on checkout.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_disable_options_on_order_status',
				'default' => 'no',
				'type'    => 'checkbox',
			),
			array(
				'title'   => esc_html__( 'Hide upload file URL in order', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enabling this will hide the URL of any uploaded file while in order.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_show_hide_uploaded_file_url_order',
				'default' => 'no',
				'type'    => 'checkbox',
			),
			array( 'type' => 'tm_sectionend', 'id' => 'epo_page_options' ),

		);
	}

	/**
	 * String settings
	 *
	 * @since 1.0
	 */
	public function _get_setting_string( $setting, $label ) {
		return array(
			array(
				'type'  => 'tm_title',
				'id'    => 'epo_page_options',
				'title' => $label,
			),
			array(
				'title'   => esc_html__( 'Cart field/value separator', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enter the field/value separator for the cart.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_separator_cart_text',
				'default' => ':',
				'type'    => 'text',
			),
			array(
				'title'   => esc_html__( 'Option multiple value separator in cart', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enter the value separator for the option that have multiple values like checkboxes.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_multiple_separator_cart_text',
				'default' => ' ',
				'type'    => 'text',
			),			
			array(
				'title'   => esc_html__( 'Update cart text', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enter the Update cart text when you edit a product.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_update_cart_text',
				'default' => '',
				'type'    => 'text',
			),
			array(
				'title'   => esc_html__( 'Final total text', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enter the Final total text or leave blank for default.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_final_total_text',
				'default' => '',
				'type'    => 'text',
			),
			array(
				'title'   => esc_html__( 'Unit price text', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enter the Unit price text or leave blank for default.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_options_unit_price_text',
				'default' => '',
				'type'    => 'text',
			),
			array(
				'title'   => esc_html__( 'Options total text', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enter the Options total text or leave blank for default.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_options_total_text',
				'default' => '',
				'type'    => 'text',
			),
			array(
				'title'   => esc_html__( 'Fees total text', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enter the Fees total text or leave blank for default.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_fees_total_text',
				'default' => '',
				'type'    => 'text',
			),

			array(
				'title'   => esc_html__( 'Free Price text replacement', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enter a text to replace the Free price label when product has extra options.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_replacement_free_price_text',
				'default' => '',
				'type'    => 'text',
			),
			array(
				'title'   => esc_html__( 'Reset Options text replacement', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enter a text to replace the Reset options text when using custom variations.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_reset_variation_text',
				'default' => '',
				'type'    => 'text',
			),
			array(
				'title'   => esc_html__( 'Edit Options text replacement', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enter a text to replace the Edit options text on the cart.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_edit_options_text',
				'default' => '',
				'type'    => 'text',
			),
			array(
				'title'   => esc_html__( 'Additional Options text replacement', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enter a text to replace the Additional options text when using the pop up setting on the cart.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_additional_options_text',
				'default' => '',
				'type'    => 'text',
			),
			array(
				'title'   => esc_html__( 'Close button text replacement', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enter a text to replace the Close button text when using the pop up setting on the cart.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_close_button_text',
				'default' => '',
				'type'    => 'text',
			),
			array(
				'title'   => esc_html__( 'Calendar close button text replacement', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enter a text to replace the Close button text on the calendar.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_closetext',
				'default' => '',
				'type'    => 'text',
			),
			array(
				'title'   => esc_html__( 'Calendar today button text replacement', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enter a text to replace the Today button text on the calendar.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_currenttext',
				'default' => '',
				'type'    => 'text',
			),
			array(
				'title'   => esc_html__( 'Slider previous text', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enter a text to replace the previous button text for slider.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_slider_prev_text',
				'default' => '',
				'type'    => 'text',
			),
			array(
				'title'   => esc_html__( 'Slider next text', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enter a text to replace the next button text for slider.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_slider_next_text',
				'default' => '',
				'type'    => 'text',
			),
			array(
				'title'   => esc_html__( 'Force Select options text', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enter a text to replace the add to cart button text when using the Force select option.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_force_select_text',
				'default' => '',
				'type'    => 'text',
			),
			array(
				'title'   => esc_html__( 'Empty cart text', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enter a text to replace the empty cart button text.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_empty_cart_text',
				'default' => '',
				'type'    => 'text',
			),
			array(
				'title'   => esc_html__( 'This field is required text', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enter a text indicate that a field is required.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_this_field_is_required_text',
				'default' => '',
				'type'    => 'text',
			),
			array(
				'title'   => esc_html__( 'Characters remaining text', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enter a text to replace the Characters remaining text when using maximum characters on a text field or a textarea.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_characters_remaining_text',
				'default' => '',
				'type'    => 'text',
			),
			array(
				'title'   => esc_html__( 'Uploading files text', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enter a text to replace the Uploading files text used in the pop-up after clicking the add to cart button  when there are upload fields.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_uploading_files_text',
				'default' => '',
				'type'    => 'text',
			),
			array(
				'title'   => esc_html__( 'Uploading message text', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enter a message to be used in the pop-up after clicking the add to cart button when there are upload fields.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_uploading_message_text',
				'default' => '',
				'type'    => 'text',
			),
			array(
				'title'   => esc_html__( 'Select file text', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enter a text replace the Select file text used in the styled upload button.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_select_file_text',
				'default' => '',
				'type'    => 'text',
			),
			array(
				'title'   => esc_html__( 'No zero priced products text', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enter a text replace the message when trying to add a zero priced product to othe cart.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_no_zero_priced_products_text',
				'default' => '',
				'type'    => 'text',
			),
			array(
				'title'   => esc_html__( 'No negative priced products text', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enter a text replace the message when trying to add a negative priced product to othe cart.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_no_negative_priced_products_text',
				'default' => '',
				'type'    => 'text',
			),

			array( 'type' => 'tm_sectionend', 'id' => 'epo_page_options' ),
		);
	}

	/**
	 * Style settings
	 *
	 * @since 1.0
	 */
	public function _get_setting_style( $setting, $label ) {
		return array(
			array(
				'type'  => 'tm_title',
				'id'    => 'epo_page_options',
				'title' => $label,
			),

			array(
				'title'   => esc_html__( 'Enable checkbox and radio styles', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enables or disables extra styling for checkboxes and radio buttons.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_css_styles',
				'class'   => 'chosen_select',
				'css'     => 'min-width:300px;',
				'default' => '',
				'type'    => 'select',
				'options' => array(
					''   => esc_html__( 'Disable', 'woocommerce-tm-extra-product-options' ),
					'on' => esc_html__( 'Enable', 'woocommerce-tm-extra-product-options' ),

				),
			),
			array(
				'title'   => esc_html__( 'Style', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Select a style for the checkboxes and radio buttons', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_css_styles_style',
				'class'   => 'chosen_select',
				'css'     => 'min-width:300px;',
				'default' => 'round',
				'type'    => 'select',
				'options' => array(
					'round'   => esc_html__( 'Round', 'woocommerce-tm-extra-product-options' ),
					'round2'  => esc_html__( 'Round 2', 'woocommerce-tm-extra-product-options' ),
					'square'  => esc_html__( 'Square', 'woocommerce-tm-extra-product-options' ),
					'square2' => esc_html__( 'Square 2', 'woocommerce-tm-extra-product-options' ),

				),
			),
			array(
				'title'   => esc_html__( 'Select item border type', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Select a style for the selected border when using image replacements or swatches.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_css_selected_border',
				'class'   => 'chosen_select',
				'css'     => 'min-width:300px;',
				'default' => '',
				'type'    => 'select',
				'options' => array(
					''         => esc_html__( 'Default', 'woocommerce-tm-extra-product-options' ),
					'square'   => esc_html__( 'Square', 'woocommerce-tm-extra-product-options' ),
					'round'    => esc_html__( 'Round', 'woocommerce-tm-extra-product-options' ),
					'shadow'   => esc_html__( 'Shadow', 'woocommerce-tm-extra-product-options' ),
					'thinline' => esc_html__( 'Thin line', 'woocommerce-tm-extra-product-options' ),
				),
			),

			array( 'type' => 'tm_sectionend', 'id' => 'epo_page_options' ),

		);
	}

	/**
	 * Global settings
	 *
	 * @since 1.0
	 */
	public function _get_setting_global( $setting, $label ) {
		return array(
			array(
				'type'  => 'tm_title',
				'id'    => 'epo_page_options',
				'desc'  => '<span tabindex="0" data-menu="tcglobal1" class="tm-section-menu-item">' . esc_html__( 'General', 'woocommerce-tm-extra-product-options' ) . '</span>' .
				           '<span tabindex="0" data-menu="tcglobal2" class="tm-section-menu-item">' . esc_html__( 'Visual', 'woocommerce-tm-extra-product-options' ) . '</span>' .
				           '<span tabindex="0" data-menu="tcglobal3" class="tm-section-menu-item">' . esc_html__( 'Product page', 'woocommerce-tm-extra-product-options' ) . '</span>' .
				           '<span tabindex="0" data-menu="tcglobal4" class="tm-section-menu-item">' . esc_html__( 'Elements', 'woocommerce-tm-extra-product-options' ) . '</span>' .
				           '<span tabindex="0" data-menu="tcglobal5" class="tm-section-menu-item">' . esc_html__( 'Locale', 'woocommerce-tm-extra-product-options' ) . '</span>' .
				           '<span tabindex="0" data-menu="tcglobal6" class="tm-section-menu-item">' . esc_html__( 'Pricing', 'woocommerce-tm-extra-product-options' ) . '</span>' .
				           '<span tabindex="0" data-menu="tcglobal7" class="tm-section-menu-item">' . esc_html__( 'Strings', 'woocommerce-tm-extra-product-options' ) . '</span>' .
				           '<span tabindex="0" data-menu="tcglobal8" class="tm-section-menu-item">' . esc_html__( 'Various', 'woocommerce-tm-extra-product-options' ) . '</span>',
				'title' => $label,
			),
			array(
				'title'   => esc_html__( 'Enable validation', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Check to enable validation feature for builder elements', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_enable_validation',
				'default' => 'yes',
				'class'   => 'tcglobal1',
				'type'    => 'checkbox',
			),
			array(
				'title'   => esc_html__( 'Disable error scrolling', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Check to disable scrolling to the element with an error', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_disable_error_scroll',
				'default' => 'no',
				'class'   => 'tcglobal1',
				'type'    => 'checkbox',
			),
			array(
				'title'   => esc_html__( 'Use options cache', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Use options caching for boosting perfromance. Disable if you have options that share the same unique ID.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_options_cache',
				'default' => 'no',
				'class'   => 'tcglobal1',
				'type'    => 'checkbox',
			),
			array(
				'title'   => esc_html__( 'Javascript and CSS inclusion mode', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Select how to include JS and CSS files', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_js_css_mode',
				'class'   => 'tcglobal1 chosen_select',
				'css'     => 'min-width:300px;',
				'default' => '',
				'type'    => 'select',
				'options' => array(
					''         => esc_html__( 'Single minified file', 'woocommerce-tm-extra-product-options' ),
					'multiple' => esc_html__( 'Multiple minified files', 'woocommerce-tm-extra-product-options' ),
					'dev'      => esc_html__( 'DEV - multiple files', 'woocommerce-tm-extra-product-options' ),
				),
			),
			array(
				'title'   => esc_html__( 'Disable PNG convert security', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Check to disable the convertion to png for image uploads.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_no_upload_to_png',
				'default' => 'no',
				'class'   => 'tcglobal8',
				'type'    => 'checkbox',
			),
			array(
				'title'   => esc_html__( 'Override product price', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'This will globally override the product price with the price from the options if the total options price is greater then zero.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_override_product_price',
				'class'   => 'tcglobal6 chosen_select',
				'css'     => 'min-width:300px;',
				'default' => '',
				'type'    => 'select',
				'options' => array(
					''    => esc_html__( 'Use setting on each product', 'woocommerce-tm-extra-product-options' ),
					'no'  => esc_html__( 'No', 'woocommerce-tm-extra-product-options' ),
					'yes' => esc_html__( 'Yes', 'woocommerce-tm-extra-product-options' ),
				),
			),
			array(
				'title'   => esc_html__( 'Reset option values after the product is added to the cart', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'This will revert the option values to the default ones after adding the product to the cart', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_reset_options_after_add',
				'default' => 'no',
				'class'   => 'tcglobal3',
				'type'    => 'checkbox',
			),
			array(
				'title'   => esc_html__( 'Use plus and minus signs on prices in cart and checkout', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Choose how you want the sign of options prices to be displayed in cart and checkout.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_price_sign',
				'class'   => 'tcglobal8 chosen_select',
				'css'     => 'min-width:300px;',
				'default' => '',
				'type'    => 'select',
				'options' => array(
					''      => esc_html__( 'Display both signs', 'woocommerce-tm-extra-product-options' ),
					'minus' => esc_html__( 'Display only minus sign', 'woocommerce-tm-extra-product-options' ),

				),
			),
			array(
				'title'   => esc_html__( 'Use plus and minus signs on option prices', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Choose how you want the sign of options prices to be displayed at the product page.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_options_price_sign',
				'class'   => 'tcglobal8 chosen_select',
				'css'     => 'min-width:300px;',
				'default' => 'minus',
				'type'    => 'select',
				'options' => array(
					''      => esc_html__( 'Display both signs', 'woocommerce-tm-extra-product-options' ),
					'minus' => esc_html__( 'Display only minus sign', 'woocommerce-tm-extra-product-options' ),

				),
			),
			array(
				'title'   => esc_html__( 'Input decimal separator', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Choose how to determine the decimal separator for user inputs', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_input_decimal_separator',
				'class'   => 'tcglobal5 chosen_select',
				'css'     => 'min-width:300px;',
				'default' => '',
				'type'    => 'select',
				'options' => array(
					''        => esc_html__( 'Use WooCommerce value', 'woocommerce-tm-extra-product-options' ),
					'browser' => esc_html__( 'Determine by browser local', 'woocommerce-tm-extra-product-options' ),

				),
			),
			array(
				'title'   => esc_html__( 'Displayed decimal separator', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Choose which decimal separator to display on currency prices', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_displayed_decimal_separator',
				'class'   => 'tcglobal5 chosen_select',
				'css'     => 'min-width:300px;',
				'default' => '',
				'type'    => 'select',
				'options' => array(
					''        => esc_html__( 'Use WooCommerce value', 'woocommerce-tm-extra-product-options' ),
					'browser' => esc_html__( 'Determine by browser local', 'woocommerce-tm-extra-product-options' ),

				),
			),

			array(
				'title'   => esc_html__( 'Error label placement', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Set the placement for the validation error notification label', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_error_label_placement',
				'class'   => 'tcglobal4 chosen_select',
				'css'     => 'min-width:300px;',
				'default' => '',
				'type'    => 'select',
				'options' => array(
					''       => esc_html__( 'After the element', 'woocommerce-tm-extra-product-options' ),
					'before' => esc_html__( 'Before the element', 'woocommerce-tm-extra-product-options' ),
				),
			),
			array(
				'title'   => esc_html__( 'Radio button undo button', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Globally override the undo button for radio buttons', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_radio_undo_button',
				'class'   => 'tcglobal4 chosen_select',
				'css'     => 'min-width:300px;',
				'default' => '',
				'type'    => 'select',
				'options' => array(
					''        => esc_html__( 'Use field value', 'woocommerce-tm-extra-product-options' ),
					'enable'  => esc_html__( 'Enable', 'woocommerce-tm-extra-product-options' ),
					'disable' => esc_html__( 'Disable', 'woocommerce-tm-extra-product-options' ),

				),
			),
			array(
				'title'   => esc_html__( 'Required state indicator', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enter a string to indicate the required state of a field.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_required_indicator',
				'default' => '*',
				'class'   => 'tcglobal7',
				'type'    => 'text',
			),
			array(
				'title'   => esc_html__( 'Required state indicator position', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Select the placement of the Required state indicator', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_required_indicator_position',
				'class'   => 'tcglobal7 chosen_select',
				'css'     => 'min-width:300px;',
				'default' => 'left',
				'type'    => 'select',
				'options' => array(
					'left'  => esc_html__( 'Left of the label', 'woocommerce-tm-extra-product-options' ),
					'right' => esc_html__( 'Right of the label', 'woocommerce-tm-extra-product-options' ),

				),
			),
			array(
				'title'   => esc_html__( 'Include tax string suffix on totals box', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enable this to add the WooCommerce tax suffix on the totals box', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_tax_string_suffix',
				'default' => 'no',
				'class'   => 'tcglobal3',
				'type'    => 'checkbox',
			),
			array(
				'title'   => esc_html__( 'Datepicker theme', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Select the theme for the datepicker.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_datepicker_theme',
				'class'   => 'tcglobal4 chosen_select',
				'css'     => 'min-width:300px;',
				'default' => '',
				'type'    => 'select',
				'options' => array(
					''          => esc_html__( 'Use field value', 'woocommerce-tm-extra-product-options' ),
					'epo'       => esc_html__( 'Epo White', 'woocommerce-tm-extra-product-options' ),
					'epo-black' => esc_html__( 'Epo Black', 'woocommerce-tm-extra-product-options' ),
				),
			),
			array(
				'title'   => esc_html__( 'Datepicker size', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Select the size of the datepicker.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_datepicker_size',
				'class'   => 'tcglobal4 chosen_select',
				'css'     => 'min-width:300px;',
				'default' => '',
				'type'    => 'select',
				'options' => array(
					''       => esc_html__( 'Use field value', 'woocommerce-tm-extra-product-options' ),
					'small'  => esc_html__( 'Small', 'woocommerce-tm-extra-product-options' ),
					'medium' => esc_html__( 'Medium', 'woocommerce-tm-extra-product-options' ),
					'large'  => esc_html__( 'Large', 'woocommerce-tm-extra-product-options' ),
				),
			),
			array(
				'title'   => esc_html__( 'Datepicker position', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Select the position of the datepicker.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_datepicker_position',
				'class'   => 'tcglobal4 chosen_select',
				'css'     => 'min-width:300px;',
				'default' => '',
				'type'    => 'select',
				'options' => array(
					''       => esc_html__( 'Use field value', 'woocommerce-tm-extra-product-options' ),
					'normal' => esc_html__( 'Normal', 'woocommerce-tm-extra-product-options' ),
					'top'    => esc_html__( 'Top of screen', 'woocommerce-tm-extra-product-options' ),
					'bottom' => esc_html__( 'Bottom of screen', 'woocommerce-tm-extra-product-options' ),
				),
			),
			array(
				'title'   => esc_html__( 'Minimum characters for text-field and text-areas', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enter a value for the minimum characters the user must enter.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_min_chars',
				'default' => '',
				'class'   => 'tcglobal4',
				'type'    => 'number',
			),
			array(
				'title'   => esc_html__( 'Maximum characters for text-field and text-areas', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enter a value for the minimum characters the user must enter.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_max_chars',
				'default' => '',
				'class'   => 'tcglobal4',
				'type'    => 'number',
			),
			array(
				'title'   => esc_html__( 'Upload element inline Image preview', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enable inline preview of the image that will be uploaded.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_upload_inline_image_preview',
				'default' => 'no',
				'class'   => 'tcglobal4',
				'type'    => 'checkbox',
			),
			array(
				'title'   => esc_html__( 'Product element scroll offset', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enter a value for the scroll offset then selecting a choice for the product element.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_product_element_scroll_offset',
				'default' => '-100',
				'class'   => 'tcglobal4',
				'type'    => 'number',
			),			
			array(
				'title'   => esc_html__( 'jQuery selector for main product image', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'This is used to change the product image.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_product_image_selector',
				'default' => '',
				'class'   => 'tcglobal3',
				'type'    => 'text',
			),
			array(
				'title'   => esc_html__( 'Product image replacement mode', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Self mode replaces the actual image and Inline appends new image elements', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_product_image_mode',
				'class'   => 'tcglobal3 chosen_select',
				'css'     => 'min-width:300px;',
				'default' => 'self',
				'type'    => 'select',
				'options' => array(
					'self'   => esc_html__( 'Self mode', 'woocommerce-tm-extra-product-options' ),
					'inline' => esc_html__( 'Inline mode', 'woocommerce-tm-extra-product-options' ),
				),
			),
			array(
				'title'   => esc_html__( 'Move out of stock message', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'This is moves the out of stok message when styled variations are used just below them.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_move_out_of_stock',
				'default' => 'no',
				'class'   => 'tcglobal3',
				'type'    => 'checkbox',
			),
			array(
				'title'   => esc_html__( 'Use internal variation price', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Use this if your variable products have a lot of options to improve performance. Note that this may cause issues with discount or currency plugins.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_no_variation_prices_array',
				'default' => 'no',
				'class'   => 'tcglobal3',
				'type'    => 'checkbox',
			),

			array(
				'title'   => esc_html__( 'Hide override settings on products', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enable this to hide the settings tab on the product edit screen', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_hide_product_settings',
				'default' => 'no',
				'class'   => 'tcglobal2',
				'type'    => 'checkbox',
			),
			array(
				'title'   => esc_html__( 'Hide Builder mode on products', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enable this to hide the builder tab on the product edit screen', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_hide_product_builder_mode',
				'default' => 'no',
				'class'   => 'tcglobal2',
				'type'    => 'checkbox',
			),
			array(
				'title'   => esc_html__( 'Hide Normal mode on products', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enable this to hide the normal tab on the product edit screen', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_global_hide_product_normal_mode',
				'default' => 'no',
				'class'   => 'tcglobal2',
				'type'    => 'checkbox',
			),

			array( 'type' => 'tm_sectionend', 'id' => 'epo_page_options' ),

		);
	}

	/**
	 * Other settings
	 *
	 * @since 1.0
	 */
	public function _get_setting_other( $setting, $label ) {
		$settings = array();
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
				'&', array_map(
					function ( $val ) {
						return $val . '=t';
					}, array_keys( apply_filters(
						'envato_market_required_permissions', array(
							'default'           => 'View and search Envato sites',
							'purchase:download' => 'Download your purchased items',
							'purchase:list'     => 'List purchases you\'ve made',
						)
					) )
				)
			);
	}

	/**
	 * License settings
	 *
	 * @since 1.0
	 */
	public function _get_setting_license( $setting, $label ) {
		$is_active         = THEMECOMPLETE_EPO_LICENSE()->get_license();
		$is_hidden         = defined( 'TC_CLIENT_MODE' );
		$_license_settings = ( ! defined( 'TM_DISABLE_LICENSE' ) ) ?
			array(
				array(
					'type'  => 'tm_title',
					'id'    => 'epo_page_options',
					'title' => $label,
				),
				array(
					'title'   => esc_html__( 'Username', 'woocommerce-tm-extra-product-options' ),
					'desc'    => esc_html__( 'Your Envato username.', 'woocommerce-tm-extra-product-options' ),
					'id'      => 'tm_epo_envato_username',
					'default' => '',
					'class'   => ( $is_hidden ? 'hidden' : '' ),
					'type'    => ( $is_hidden ? 'password' : 'text' ),
				),
				array(
					'title'   => esc_html__( 'Envato Personal Token', 'woocommerce-tm-extra-product-options' ),
					'desc'    => esc_html__( 'Your Envato Personal Token.', 'woocommerce-tm-extra-product-options' ),
					'desc'    => '<p>' . sprintf( esc_html__( 'You can generate an Envato Personal Token by %s', 'woocommerce-tm-extra-product-options' ), '<a href="' . esc_url( $this->get_generate_token_url() ) . '" target="_blank">' . esc_html__( 'clicking this link', 'woocommerce-tm-extra-product-options' ) . '</a>' ) . '</p>',
					'id'      => 'tm_epo_envato_apikey',
					'default' => '',
					'class'   => ( $is_hidden ? 'hidden' : '' ),
					'type'    => ( $is_hidden ? 'password' : 'text' ),
				),
				array(
					'title'   => esc_html__( 'Purchase code', 'woocommerce-tm-extra-product-options' ),
					'desc'    => '<p>' . sprintf( esc_html__( 'To find out how to access your purchase code you can %s', 'woocommerce-tm-extra-product-options' ), '<a href="' . esc_url( 'https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-' ) . '" target="_blank">' . esc_html__( 'click this link', 'woocommerce-tm-extra-product-options' ) . '</a>' ) . '</p>'
					             . '<span class="tm-license-button">'

					             . '<button type="button" class="' . ( THEMECOMPLETE_EPO_LICENSE()->get_license() ? "" : "tm-hidden " ) . 'tc tc-button tm-deactivate-license" id="tm_deactivate_license">' . esc_html__( 'Deactivate License', 'woocommerce-tm-extra-product-options' ) . '</button>'
					             . '<button type="button" class="' . ( THEMECOMPLETE_EPO_LICENSE()->get_license() ? "tm-hidden " : "" ) . 'tc tc-button tm-activate-license" id="tm_activate_license">' . esc_html__( 'Activate License', 'woocommerce-tm-extra-product-options' ) . '</button>'

					             . '</span>'
					             . '<span class="tm-license-result">'
					             . ( ( THEMECOMPLETE_EPO_LICENSE()->get_license() ) ?
							"<div class='activated'><p>" . esc_html__( "License activated.", 'woocommerce-tm-extra-product-options' ) . "</p></div>"
							: ""
					             )
					             . '</span>',
					'id'      => 'tm_epo_envato_purchasecode',
					'default' => '',
					'class'   => ( $is_hidden ? 'hidden' : '' ),
					'type'    => ( $is_hidden ? 'password' : 'text' ),
				),
				array(
					'title'   => esc_html__( 'Consent', 'woocommerce-tm-extra-product-options' ),
					'desc'    => esc_html__( 'I agree that the license data will be transmitted to the license server.', 'woocommerce-tm-extra-product-options' ),
					'id'      => 'tm_epo_consent_for_transmit',
					'class'   => ( $is_hidden ? 'hidden' : '' ),
					'default' => 'no',
					'type'    => 'checkbox',
				),
				array( 'type' => 'tm_sectionend', 'id' => 'epo_page_options' ),
			) : array();

		return $_license_settings;
	}

	/**
	 * Get allowed file types
	 *
	 * @since 1.0
	 */
	public function get_allowed_types() {
		$types            = array();
		$wp_get_ext_types = wp_get_ext_types();
		$types["@"]       = esc_html__( 'Use allowed file types from WordPress', 'woocommerce-tm-extra-product-options' );
		foreach ( $wp_get_ext_types as $key => $value ) {
			$types[ "@" . $key ] = $key . " " . esc_html__( 'files', 'woocommerce-tm-extra-product-options' );
			foreach ( $value as $key2 => $value2 ) {
				$types[ $value2 ] = $value2;
			}
		}

		return $types;
	}

	/**
	 * Upload settings
	 *
	 * @since 1.0
	 */
	public function _get_setting_upload( $setting, $label ) {

		$html = '<div class="tm-mn-header"><div class="tm-mn-path">'
		        . '<a class="tm-mn-movetodir tc tc-button" data-tm-dir="" href="#">' . esc_html__( 'Enable File manager', 'woocommerce-tm-extra-product-options' ) . '</a>'
		        . '</div></div>';

		$_upload_settings =
			array(
				array(
					'type'  => 'tm_title',
					'id'    => 'epo_page_options',
					'title' => $label,
				),
				array(
					'title'   => esc_html__( 'Upload folder', 'woocommerce-tm-extra-product-options' ),
					'desc'    => esc_html__( 'Changing this will only affect future uploads.', 'woocommerce-tm-extra-product-options' ),
					'id'      => 'tm_epo_upload_folder',
					'default' => 'extra_product_options',
					'type'    => 'text',
				),
				array(
					'title'   => esc_html__( 'Enable pop-up message on uploads', 'woocommerce-tm-extra-product-options' ),
					'desc'    => esc_html__( 'Enables a pop-up when uploads are made.', 'woocommerce-tm-extra-product-options' ),
					'id'      => 'tm_epo_upload_popup',
					'default' => 'no',
					'type'    => 'checkbox',
				),
				array(
					'title'             => esc_html__( 'Allowed file types', 'woocommerce-tm-extra-product-options' ),
					'desc'              => esc_html__( 'Select which file types the user will be allowed to upload.', 'woocommerce-tm-extra-product-options' ),
					'id'                => 'tm_epo_allowed_file_types',
					'type'              => 'multiselect',
					'class'             => 'wc-enhanced-select',
					'css'               => 'width: 450px;',
					'default'           => '@',
					'options'           => $this->get_allowed_types(),
					'custom_attributes' => array(
						'data-placeholder' => esc_html__( 'Select file types', 'woocommerce-tm-extra-product-options' ),
					),
				),
				array(
					'title'   => esc_html__( 'Custom types', 'woocommerce-tm-extra-product-options' ),
					'desc'    => esc_html__( 'Select custom file types the user will be allowed to upload separated by commas.', 'woocommerce-tm-extra-product-options' ),
					'id'      => 'tm_epo_custom_file_types',
					'default' => '',
					'type'    => 'text',
				),
				array(
					'type'  => 'tm_html',
					'id'    => 'epo_page_options_html',
					'title' => esc_html__( 'File manager', 'woocommerce-tm-extra-product-options' ),
					'html'  => $html,
				),
				array( 'type' => 'tm_sectionend', 'id' => 'epo_page_options' ),
			);

		return $_upload_settings;
	}

	/**
	 * Code settings
	 *
	 * @since 1.0
	 */
	public function _get_setting_code( $setting, $label ) {
		return array(
			array(
				'type'  => 'tm_title',
				'id'    => 'epo_page_options',
				'title' => $label,
			),
			array(
				'title'   => esc_html__( 'CSS code', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Only enter pure CSS code without and style tags', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_css_code',
				'default' => '',
				'type'    => 'textarea',
				'class'   => 'tc-admin-textarea',
			),
			array(
				'title'   => esc_html__( 'JavaScript code', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Only enter pure JavaScript code without and script tags', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_js_code',
				'default' => '',
				'type'    => 'textarea',
				'class'   => 'tc-admin-textarea',
			),
			array( 'type' => 'tm_sectionend', 'id' => 'epo_page_options' ),
		);
	}
}

