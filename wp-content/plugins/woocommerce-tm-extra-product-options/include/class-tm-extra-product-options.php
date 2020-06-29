<?php
/**
 * Extra Product Options main class
 *
 * This class is responsible for displaying the Extra Product Options on the frontend.
 *
 * @package Extra Product Options/Classes
 * @version 5.0
 */

defined( 'ABSPATH' ) || exit;

final class THEMECOMPLETE_Extra_Product_Options {

	// Holds the current post id 
	private $postid_pre = FALSE;

	// Helper for determining various conditionals 
	public $wc_vars = array(
		"is_product"             => FALSE,
		"is_shop"                => FALSE,
		"is_product_category"    => FALSE,
		"is_product_tag"         => FALSE,
		"is_cart"                => FALSE,
		"is_checkout"            => FALSE,
		"is_account_page"        => FALSE,
		"is_ajax"                => FALSE,
		"is_page"                => FALSE,
		"is_order_received_page" => FALSE,
	);

	// Product custom settings 
	public $tm_meta_cpf = array();

	// Product custom settings options 
	public $meta_fields = array(
		'exclude'                  => '',
		'price_override'           => '',
		'override_display'         => '',
		'override_final_total_box' => '',
		'override_enabled_roles'   => '',
		'override_disabled_roles'  => '',
	);

	// Cache for all the extra options 
	private $cpf = array();

	// Options cache 
	private $cpf_single = array();
	private $cpf_single_epos_prices = array();
	private $cpf_single_variation_element_id = array();
	private $cpf_single_variation_section_id = array();

	// Holds the upload directory for the upload element 
	public $upload_dir = "/extra_product_options/";
	// Holds the upload files objects 
	private $upload_object = array();

	// Replacement name for cart fee fields 
	public $cart_fee_name = "tmcartfee_";
	public $cart_fee_class = "tmcp-fee-field";

	// Array of element types that get posted 
	public $element_post_types = array();

	// Holds builder element attributes 
	private $tm_original_builder_elements = array();

	// Holds modified builder element attributes 
	public $tm_builder_elements = array();

	// Holds the cart key when editing a product in the cart 
	// This isn't in our cart class becuase we needed to be initialized 
	// before the plugins_loaded hook.
	public $cart_edit_key = NULL;

	// Containes current option features 
	public $current_option_features = array();

	// Holds all of the plugin settings 
	private $tm_plugin_settings = array();

	// Enable/disable flag for outputing plugin specific classes to the post_class filter  
	private $tm_related_products_output = TRUE;

	// Enable/disable flag for outputing plugin specific classes to the post_class filter  
	private $in_related_upsells = FALSE;

	// Cart edit key
	public $cart_edit_key_var = 'tm_cart_item_key';
	public $cart_edit_key_var_alt = 'tc_cart_edit_key';

	// Contains min/man product infomation 
	public $product_minmax = array();

	// Current free text replacement 
	public $current_free_text = '';

	// Flag to check if we are in the product shortcode
	public $is_in_product_shortcode;

	// Flag to fix several issues when the woocommerce_get_price hook isn't being used correct by themes or other plugins.
	private $tm_woocommerce_get_price_flag = 0;

	// Visible elements cache;
	private $visible_elements = array();
	// Current element that is being checked if it is visible
	private $current_element_to_check = array();

	// If the current product is a composite product
	public $is_bto = FALSE;

	// If we are in the associated product options.
	public $is_inline_epo = FALSE;

	public $noactiondisplay = FALSE;

	public $is_associated = FALSE;

	/**
	 * The single instance of the class
	 *
	 * @since 1.0
	 */
	protected static $_instance = NULL;

	/**
	 * Main Extra Product Options Instance
	 *
	 * Ensures only one instance of Extra Product Options is loaded or can be loaded
	 *
	 * @since 1.0
	 * @static
	 * @see   THEMECOMPLETE_EPO()
	 * @return THEMECOMPLETE_Extra_Product_Options - Main instance
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

		$this->is_bto          = FALSE;
		$this->noactiondisplay = FALSE;

		$this->cart_edit_key_var     = apply_filters( 'wc_epo_cart_edit_key_var', 'tm_cart_item_key' );
		$this->cart_edit_key_var_alt = apply_filters( 'wc_epo_cart_edit_key_var_alt', 'tc_cart_edit_key' );
		$this->cart_edit_key         = NULL;

		if ( isset( $_REQUEST[ $this->cart_edit_key_var ] ) ) {

			$this->cart_edit_key = $_REQUEST[ $this->cart_edit_key_var ];

		} else {

			if ( isset( $_REQUEST[ $this->cart_edit_key_var_alt ] ) ) {

				$this->cart_edit_key = $_REQUEST[ $this->cart_edit_key_var_alt ];

			} else {

				if ( isset( $_REQUEST['update-composite'] ) ) {

					$this->cart_edit_key = $_REQUEST['update-composite'];


				}
			}
		}

		// Add compatibility actions and filters with other plugins and themes 
		THEMECOMPLETE_EPO_COMPATIBILITY();

		add_action( 'plugins_loaded', array( $this, 'plugin_loaded' ), 3 );
		add_action( 'plugins_loaded', array( $this, 'tm_epo_add_elements' ), 12 );

	}

	/**
	 * Handles the display of builder sections.
	 *
	 * @since 5.0
	 */
	public function set_inline_epo( $bool = FALSE ) {
		$this->is_inline_epo = $bool;
	}

	/**
	 * Handles the display of builder sections.
	 * (Backwards compatibility)
	 *
	 * @see   THEMECOMPLETE_EPO_DISPLAY()->get_builder_display
	 * @since 1.0
	 */
	public function get_builder_display( $field, $where, $args, $form_prefix = "", $product_id = 0, $dummy_prefix = FALSE ) {
		return THEMECOMPLETE_EPO_DISPLAY()->get_builder_display( $field, $where, $args, $form_prefix, $dummy_prefix );
	}

	/**
	 * Handles any extra styling associated with the fields
	 * (Backwards compatibility)
	 *
	 * @see   THEMECOMPLETE_EPO_DISPLAY()->tm_add_inline_style
	 * @since 1.0
	 */
	public function tm_add_inline_style() {
		THEMECOMPLETE_EPO_DISPLAY()->tm_add_inline_style();
	}

	/**
	 * Calculates the fee price
	 * (Backwards compatibility)
	 *
	 * @see   THEMECOMPLETE_EPO_CART()->cacl_fee_price
	 * @since 1.0
	 */
	public function cacl_fee_price( $price = "", $product_id = "", $element = FALSE, $attribute = "" ) {
		return THEMECOMPLETE_EPO_CART()->cacl_fee_price( $price, $product_id, $element, $attribute );
	}

	/**
	 * Display meta value
	 * Mainly used for hiding uploaded file path
	 * (Backwards compatibility)
	 *
	 * @see   THEMECOMPLETE_EPO_ORDER()->display_meta_value
	 * @since 1.0
	 */
	public function tm_order_item_display_meta_value( $value = "", $override = 0 ) {
		return THEMECOMPLETE_EPO_ORDER()->display_meta_value( $value, $override, 'always' );
	}

	/**
	 * Translate $attributes to post names
	 * (Backwards compatibility)
	 *
	 * @see get_post_names
	 * @return array
	 */
	public function translate_fields( $attributes, $type, $section, $form_prefix = "", $name_prefix = "" ) {
		return $this->get_post_names( $attributes, $type, $section, $form_prefix, $name_prefix );
	}

	/**
	 * Adds additional builder elements from 3rd party plugins
	 *
	 * @since 1.0
	 */
	public final function tm_epo_add_elements() {

		do_action( 'tm_epo_register_addons' );
		do_action( 'tm_epo_register_extra_multiple_choices' );

		$this->tm_original_builder_elements = THEMECOMPLETE_EPO_BUILDER()->get_elements();

		if ( is_array( $this->tm_original_builder_elements ) ) {

			foreach ( $this->tm_original_builder_elements as $key => $value ) {

				if ( $value["is_post"] == "post" ) {
					$this->element_post_types[] = $value["post_name_prefix"];
				}

				if ( $value["is_post"] == "post" || $value["is_post"] == "display" ) {
					$this->tm_builder_elements[ $value["post_name_prefix"] ] = $value;
				}

			}
		}

	}

	/**
	 * Setup the plugin
	 *
	 * @since 1.0
	 */
	public function plugin_loaded() {

		$this->tm_plugin_settings = THEMECOMPLETE_EPO_SETTINGS()->plugin_settings();
		$this->get_plugin_settings();
		$this->get_override_settings();
		$this->add_plugin_actions();

		THEMECOMPLETE_EPO_SCRIPTS();
		THEMECOMPLETE_EPO_DISPLAY();
		THEMECOMPLETE_EPO_CART();
		THEMECOMPLETE_EPO_ORDER();
		THEMECOMPLETE_EPO_ASSOCIATED_PRODUCTS();

	}

	/**
	 * Gets all of the plugin settings
	 *
	 * @since 1.0
	 */
	public function get_plugin_settings() {

		foreach ( apply_filters( 'wc_epo_get_settings', $this->tm_plugin_settings ) as $key => $value ) {
			if ( is_array( $value ) ) {
				$method    = $value[2];
				$classname = $value[1];
				if ( call_user_func( array( $classname, $method ) ) ) {
					$this->$key = get_option( $key );
					if ( $this->$key === FALSE ) {
						$this->$key = $value[0];
					}
				} else {
					$this->$key = $value[0];
				}
			} else {
				$this->$key = get_option( $key );
				if ( $this->$key === FALSE ) {
					$this->$key = $value;
				}
			}
		}

		if ( $this->tm_epo_options_placement == "custom" ) {
			$this->tm_epo_options_placement = $this->tm_epo_options_placement_custom_hook;
		}

		if ( $this->tm_epo_totals_box_placement == "custom" ) {
			$this->tm_epo_totals_box_placement = $this->tm_epo_totals_box_placement_custom_hook;
		}

		$this->upload_dir = $this->tm_epo_upload_folder;
		$this->upload_dir = str_replace( "/", "", $this->upload_dir );
		$this->upload_dir = sanitize_file_name( $this->upload_dir );
		$this->upload_dir = "/" . $this->upload_dir . "/";

		if ( $this->is_quick_view() ) {
			$this->tm_epo_options_placement_hook_priority    = 50;
			$this->tm_epo_totals_box_placement_hook_priority = 50;
			$this->tm_epo_options_placement                  = 'woocommerce_before_add_to_cart_button';
			$this->tm_epo_totals_box_placement               = 'woocommerce_before_add_to_cart_button';
		}

	}

	/**
	 * Gets custom settings for the current product
	 *
	 * @since 1.0
	 */
	public function get_override_settings() {
		foreach ( $this->meta_fields as $key => $value ) {
			$this->tm_meta_cpf[ $key ] = $value;
		}
	}

	/**
	 * Add required actions and filters
	 *
	 * @since 1.0
	 */
	public function add_plugin_actions() {

		// Initialize custom product settings 
		if ( $this->is_quick_view() ) {
			add_action( 'init', array( $this, 'init_settings' ) );
		} else {
			if ( $this->is_enabled_shortcodes() ) {
				add_action( 'init', array( $this, 'init_settings_pre' ) );
			} else {
				add_action( 'template_redirect', array( $this, 'init_settings' ) );
			}
		}

		add_action( 'template_redirect', array( $this, 'init_vars' ), 1 );

		// Force Select Options 
		add_filter( 'woocommerce_add_to_cart_url', array( $this, 'add_to_cart_url' ), 50, 1 );
		add_filter( 'woocommerce_product_add_to_cart_url', array( $this, 'add_to_cart_url' ), 50, 1 );
		add_action( 'woocommerce_product_add_to_cart_text', array( $this, 'add_to_cart_text' ), 10, 1 );
		add_filter( 'woocommerce_cart_redirect_after_error', array( $this, 'woocommerce_cart_redirect_after_error' ), 50, 2 );

		// Enable shortcodes for element labels 
		add_filter( 'woocommerce_tm_epo_option_name', array( $this, 'tm_epo_option_name' ), 10, 5 );

		// Add custom class to product div used to initialize the plugin JavaScript 
		add_filter( 'post_class', array( $this, 'tm_post_class' ) );

		// Helper to flag various page positions
		add_filter( 'woocommerce_related_products_columns', array( $this, 'tm_woocommerce_related_products_args' ), 10, 1 );
		add_action( 'woocommerce_before_single_product', array( $this, 'tm_enable_post_class' ), 1 );
		add_action( 'woocommerce_after_single_product', array( $this, 'tm_enable_post_class' ), 1 );
		add_action( 'woocommerce_upsells_orderby', array( $this, 'tm_woocommerce_related_products_args' ), 10, 1 );
		add_action( 'woocommerce_after_single_product_summary', array( $this, 'tm_woocommerce_after_single_product_summary' ), 99999 );

		// Image filter 
		add_filter( 'tm_image_url', array( $this, 'tm_image_url' ) );

		// Alter the price filter
		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.7.0', '<' ) ) {
			add_filter( 'woocommerce_get_price', array( $this, 'woocommerce_product_get_price' ), 1, 2 );
		} else {
			add_filter( 'woocommerce_product_get_price', array( $this, 'woocommerce_product_get_price' ), 1, 2 );
		}

		// Alter product display price to include possible option pricing 
		if ( ! is_admin() && $this->tm_epo_include_possible_option_pricing == "yes" ) {
			if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.7.0', '<' ) ) {
				add_filter( 'woocommerce_get_price', array( $this, 'tm_woocommerce_get_price' ), 2, 2 );
			} else {
				add_filter( 'woocommerce_product_get_price', array( $this, 'tm_woocommerce_get_price' ), 2, 2 );
			}
		}
		if ( ! is_admin() && $this->tm_epo_use_from_on_price == "yes" ) {
			add_filter( 'woocommerce_show_variation_price', array( $this, 'tm_woocommerce_show_variation_price' ), 50, 3 );
			if ( $this->tm_epo_include_possible_option_pricing == "no" ) {
				add_filter( 'woocommerce_get_variation_price_html', array( $this, 'tm_get_price_html' ), 11, 2 );
				add_filter( 'woocommerce_get_price_html', array( $this, 'tm_get_price_html' ), 11, 2 );
			}
		}

		// Override the minimum characters of text fields globally 
		add_filter( 'wc_epo_global_min_chars', array( $this, 'wc_epo_global_min_chars' ), 10, 3 );
		// Override the maximum characters of text fields globally 
		add_filter( 'wc_epo_global_max_chars', array( $this, 'wc_epo_global_max_chars' ), 10, 3 );

		if ( $this->tm_epo_global_no_upload_to_png === 'yes' ) {
			add_filter( 'wc_epo_no_upload_to_png', '__return_false' );
		}

		// Alter generated Product structured data.
		add_filter( 'woocommerce_structured_data_product_offer', array( $this, 'woocommerce_structured_data_product_offer' ), 10, 2 );

		// Enable shortcodes in options strings
		if ( $this->tm_epo_enable_data_shortcodes === 'yes' ) {
			add_filter( 'wc_epo_kses', array( $this, 'wc_epo_kses' ), 10, 3 );
			add_filter( 'wc_epo_label_in_cart', array( $this, 'wc_epo_label_in_cart' ), 10, 1 );
		}

	}

	/**
	 * Enable shortcodes in options strings
	 *
	 * @since 4.9.2
	 */
	public function wc_epo_kses( $text = "", $original_text = "", $shortcode = TRUE ) {

		$text = $original_text;

		if ( $shortcode ) {
			$text = do_shortcode( $text );
		}

		return $text;

	}

	/**
	 * Enable shortcodes in cart option strings
	 *
	 * @since 4.9.2
	 */
	public function wc_epo_label_in_cart( $text = "" ) {

		return $this->wc_epo_kses( $text, $text );

	}

	/**
	 * Get product min/max prices
	 *
	 * @since 4.8.1
	 */
	public function get_product_min_max_prices( $product ) {

		$id   = themecomplete_get_id( $product );
		$type = themecomplete_get_product_type( $product );

		$has_epo = THEMECOMPLETE_EPO_API()->has_options( $id );
		if ( ! THEMECOMPLETE_EPO_API()->is_valid_options( $has_epo ) || $type == 'variation' ) {
			return array();
		}

		$override_id = floatval( THEMECOMPLETE_EPO_WPML()->get_original_id( $id, 'product' ) );
		$tm_meta_cpf = themecomplete_get_post_meta( $override_id, 'tm_meta_cpf', TRUE );

		$price_override = ( $this->tm_epo_global_override_product_price == 'no' )
			? 0
			: ( ( $this->tm_epo_global_override_product_price == 'yes' )
				? 1
				: ( ! empty( $tm_meta_cpf['price_override'] ) ? 1 : 0 ) );

		if ( ! isset( $this->product_minmax[ $id ] ) ) {
			$this->product_minmax[ $id ] = $this->add_product_tc_prices( $product );
		}

		$minmax = $this->product_minmax[ $id ];

		if ( $type == 'variable' || $type == 'variable-subscription' ) {
			$prices = $product->get_variation_prices( FALSE );

			// Calculate min price
			$min_price       = current( $prices['price'] );
			$tc_min_variable = isset( $minmax['tc_min_variable'][ key( $prices['price'] ) ] )
				? $minmax['tc_min_variable'][ key( $prices['price'] ) ]
				: ( isset( $minmax['tc_min_variable'] )
					? $minmax['tc_min_variable']
					: 0
				);

			if ( is_array( $tc_min_variable ) ) {
				$tc_min_variable = min( $tc_min_variable );
			}

			$min_raw = floatval( apply_filters( 'wc_epo_options_min_price', $tc_min_variable, $product, FALSE ) );

			$min_price = $min_price + $min_raw;

			// include taxes
			$min_price = $this->tc_get_display_price( $product, $min_price );

			if ( $price_override ) {
				if ( ! empty( $min_raw ) ) {
					$min_price = $min_raw;
				}
				$this->product_minmax[ $id ]['is_override'] = 1;
			}

			$min = $this->tc_get_display_price( $product, $min_raw );

			// Calculate max price
			$copy_prices = $prices['price'];
			$added_max   = array();
			foreach ( $copy_prices as $vkey => $vprice ) {
				$added_price_max = is_array( $this->product_minmax[ $id ]['tc_max_variable'] )
					? isset( $this->product_minmax[ $id ]['tc_max_variable'][ $vkey ] )
						? $this->product_minmax[ $id ]['tc_max_variable'][ $vkey ]
						: 0
					: $this->product_minmax[ $id ]['tc_max_variable'];

				$added_price          = floatval( apply_filters( 'wc_epo_options_max_price_raw', $added_price_max, $product, FALSE ) );
				$added_max[]          = $added_price;
				$copy_prices[ $vkey ] = $vprice + $added_price;
			}
			asort( $copy_prices );
			$max_price = end( $copy_prices );

			asort( $added_max );

			$max_raw = floatval( apply_filters( 'wc_epo_options_max_price', end( $added_max ), $product, FALSE ) );

			$max_price = $this->tc_get_display_price( $product, $max_price );

			if ( $price_override && ! ( empty( $this->product_minmax[ $id ]['tc_min_variable'] ) && empty( $this->product_minmax[ $id ]['tc_max_variable'] ) ) ) {
				$max_price = $max_price - $this->tc_get_display_price( $product, floatval( $prices['price'][ key( $copy_prices ) ] ) );
			}

			$max = $this->tc_get_display_price( $product, $max_raw );

			$min_regular_price = floatval( current( $prices['regular_price'] ) ) + $min_raw;
			$max_regular_price = floatval( end( $prices['regular_price'] ) ) + $max_raw;

		} else {

			// Calculate min price
			$min_raw = floatval( apply_filters( 'wc_epo_options_min_price', $minmax['tc_min_price'], $product, FALSE ) );

			if ( $price_override ) {

				if ( ! empty( $min_raw ) ) {
					$new_min = $min_raw;
				} else {
					$new_min = $product->get_price();
				}

				$min_raw = $new_min;


				$this->product_minmax[ $id ]['is_override'] = 1;
			}

			$this->product_minmax[ $id ]['tc_min_price'] = $min_raw;

			$display_price         = $this->tc_get_display_price( $product );
			$display_regular_price = $this->tc_get_display_price( $product, $this->tc_get_regular_price( $product ) );

			if ( $price_override && $min_raw <= 0 ) {
				$display_price = $display_regular_price;
			}

			$min       = $this->tc_get_display_price( $product, $min_raw );
			$min_price = $display_price;

			// Calculate max price
			$max_raw                                     = floatval( apply_filters( 'wc_epo_options_max_price', $this->product_minmax[ $id ]['tc_max_price'], $product, FALSE ) );
			$this->product_minmax[ $id ]['tc_max_price'] = $max_raw;
			$max                                         = $this->tc_get_display_price( $product, $max_raw );
			$max_price                                   = $this->tc_get_display_price( $product, (float) apply_filters( 'wc_epo_product_price', $product->get_price(), "", FALSE ) + $max_raw );

			$min_regular_price = floatval( $display_regular_price );
			$max_regular_price = floatval( $this->tc_get_display_price( $product, $product->get_regular_price() ) ) + $max;

		}

		return array(
			'min_raw'   => $min_raw,
			'max_raw'   => $max_raw,
			'min'       => $min,
			'max'       => $max,
			'min_price' => $min_price,
			'max_price' => $max_price,

			'min_regular_price' => isset( $min_regular_price ) ? $min_regular_price : 0,
			'max_regular_price' => isset( $max_regular_price ) ? $max_regular_price : 0,

			'formatted_min'       => wc_format_decimal( $min, wc_get_price_decimals() ),
			'formatted_max'       => wc_format_decimal( $max, wc_get_price_decimals() ),
			'formatted_min_price' => wc_format_decimal( $min_price, wc_get_price_decimals() ),
			'formatted_max_price' => wc_format_decimal( $max_price, wc_get_price_decimals() ),

		);

	}

	/**
	 * Alter generated product structured data
	 *
	 * @since 4.8.1
	 */
	public function woocommerce_structured_data_product_offer( $markup, $product ) {

		if ( $this->tm_epo_alter_structured_data === "no" ) {
			return $markup;
		}

		$min_max = $this->get_product_min_max_prices( $product );

		if ( empty( $min_max ) ) {
			return $markup;
		}

		$min_price = $min_max['formatted_min_price'];
		$max_price = $min_max['formatted_max_price'];

		if ( isset( $markup['priceSpecification'] ) && is_array( $markup['priceSpecification'] ) && isset( $markup['priceSpecification']['price'] ) ) {
			$markup['priceSpecification']['price'] = $min_price;
			$markup['price']                       = $min_price;
		}
		if ( isset( $max_price ) && isset( $markup['lowPrice'] ) && isset( $markup['highPrice'] ) ) {
			$markup['lowPrice']  = $min_price;
			$markup['highPrice'] = $max_price;
		}

		return $markup;

	}

	/**
	 * Override the minimum characters of text fields globally
	 *
	 * @since 1.0
	 */
	public function wc_epo_global_min_chars( $min = "", $element = "", $element_uniqueid = "" ) {
		$element = str_replace( "_min_chars", "", $element );

		if ( ( $element === "textfield" || $element === "textarea" ) && $this->tm_epo_global_min_chars !== '' && $min === '' ) {
			$min = $this->tm_epo_global_min_chars;
		}

		return $min;
	}

	/**
	 * Override the maximum characters of text fields globally
	 *
	 * @since 1.0
	 */
	public function wc_epo_global_max_chars( $max = "", $element = "", $element_uniqueid = "" ) {
		$element = str_replace( "_min_chars", "", $element );
		if ( ( $element === "textfield" || $element === "textarea" ) && $this->tm_epo_global_max_chars !== '' && $max === '' ) {
			$max = $this->tm_epo_global_max_chars;
		}

		return $max;
	}

	/**
	 * Initialize custom product settings
	 *
	 * @since 1.0
	 */
	public function init_settings_pre() {

		$postid = FALSE;
		if ( function_exists( 'ux_builder_is_iframe' ) && ux_builder_is_iframe() ) {
			if ( isset( $_GET['post_id'] ) ) {
				$postid = $_GET['post_id'];
			}
		} else {
			if ( ! isset( $_SERVER["HTTP_HOST"] ) || ! isset( $_SERVER["REQUEST_URI"] ) ) {
				$postid = 0;
			} else {
				$url    = 'http://' . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
				$postid = THEMECOMPLETE_EPO_HELPER()->get_url_to_postid( $url );
			}
		}

		$this->postid_pre = $postid;
		$product          = wc_get_product( $postid );

		$check1 = ( $postid === 0 );
		$check2 = ( $product
		            && is_object( $product )
		            && property_exists( $product, 'post' )
		            && property_exists( $product->post, 'post_type' )
		            && ( in_array( $product->post->post_type, array( 'product', 'product_variation' ) ) ) );
		$check3 = ( $product
		            && is_object( $product )
		            && property_exists( $product, 'post_type' )
		            && ( in_array( $product->post_type, array( 'product', 'product_variation' ) ) ) );

		if ( $check1 || $check2 || $check3 ) {
			add_action( 'template_redirect', array( $this, 'init_settings' ) );
		} else {
			$this->init_settings();
		}

	}

	/**
	 * Initialize variables
	 *
	 * @since 1.0
	 */
	public function init_vars() {
		$this->wc_vars = array(
			"is_product"             => is_product(),
			"is_shop"                => is_shop(),
			"is_product_category"    => is_product_category(),
			"is_product_tag"         => is_product_tag(),
			"is_cart"                => is_cart(),
			"is_checkout"            => is_checkout(),
			"is_account_page"        => is_account_page(),
			"is_ajax"                => is_ajax(),
			"is_page"                => is_page(),
			"is_order_received_page" => is_order_received_page(),
		);
	}

	/**
	 * Initialize custom product settings
	 *
	 * @since 1.0
	 */
	public function init_settings() {

		if ( is_admin() && ! $this->is_quick_view() ) {
			return;
		}

		// Re populate options for WPML
		if ( THEMECOMPLETE_EPO_WPML()->is_active() ) {
			//todo:Find another place to re init settings for WPML
			$this->get_plugin_settings();
		}

		if ( class_exists( 'WOOCS' ) ) {
			global $WOOCS;
			remove_filter( 'woocommerce_order_amount_total', array( $WOOCS, 'woocommerce_order_amount_total' ), 999 );
		}

		$postMax = ini_get( 'post_max_size' );

		// post_max_size debug
		if ( empty( $_FILES )
		     && empty( $_POST )
		     && isset( $_SERVER['REQUEST_METHOD'] )
		     && strtolower( $_SERVER['REQUEST_METHOD'] ) === 'post'
		     && isset( $_SERVER['CONTENT_LENGTH'] )
		     && (float) $_SERVER['CONTENT_LENGTH'] > $postMax
		) {

			wc_add_notice( sprintf( esc_html__( 'Trying to upload files larger than %s is not allowed!', 'woocommerce-tm-extra-product-options' ), $postMax ), 'error' );

		}

		global $post, $product;
		$this->set_tm_meta();
		$this->init_settings_after();

	}

	/**
	 * Initialize custom product settings
	 *
	 * @since 1.0
	 */
	public function init_settings_after() {

		global $post, $product;
		// Check if the plugin is active for the user
		if ( $this->check_enable() ) {
			if ( ( $this->is_enabled_shortcodes() || is_product() || $this->is_quick_view() )
			     && ( $this->tm_epo_display == 'normal' || $this->tm_meta_cpf['override_display'] == 'normal' )
			     && $this->tm_meta_cpf['override_display'] != 'action'
			) {
				$this->noactiondisplay = TRUE;
				// Add options to the page
				$this->tm_epo_options_placement_hook_priority = floatval( $this->tm_epo_options_placement_hook_priority );
				if ( ! is_numeric( $this->tm_epo_options_placement_hook_priority ) ) {
					$this->tm_epo_options_placement_hook_priority = 50;
				}
				$this->tm_epo_totals_box_placement_hook_priority = floatval( $this->tm_epo_totals_box_placement_hook_priority );
				if ( ! is_numeric( $this->tm_epo_totals_box_placement_hook_priority ) ) {
					$this->tm_epo_totals_box_placement_hook_priority = 50;
				}

				add_action( $this->tm_epo_options_placement, array( THEMECOMPLETE_EPO_DISPLAY(), 'tm_epo_fields' ), $this->tm_epo_options_placement_hook_priority );
				add_action( $this->tm_epo_options_placement, array( THEMECOMPLETE_EPO_DISPLAY(), 'tm_add_inline_style' ), $this->tm_epo_options_placement_hook_priority + 99999 );
				add_action( $this->tm_epo_totals_box_placement, array( THEMECOMPLETE_EPO_DISPLAY(), 'tm_epo_totals' ), $this->tm_epo_totals_box_placement_hook_priority );
			}
		}

		if ( $this->tm_epo_enable_in_shop == "yes" && ( is_shop() || is_product_category() || is_product_tag() ) ) {
			add_action( 'woocommerce_after_shop_loop_item', array( $this, 'tm_woocommerce_after_shop_loop_item' ), 9 );
		}

		add_action( 'woocommerce_shortcode_before_product_loop', array( $this, 'woocommerce_shortcode_before_product_loop' ) );
		add_action( 'woocommerce_shortcode_after_product_loop', array( $this, 'woocommerce_shortcode_after_product_loop' ) );
		if ( $this->is_enabled_shortcodes() ) {
			add_action( 'woocommerce_after_shop_loop_item', array( $this, 'tm_enable_options_on_product_shortcode' ), 1 );
		}

		$this->current_free_text = esc_attr__( 'Free!', 'woocommerce' );
		if ( $this->tm_epo_remove_free_price_label == 'yes' && $this->tm_epo_include_possible_option_pricing == "no" ) {
			if ( $post || $this->postid_pre ) {

				if ( $post ) {
					$thiscpf = $this->get_product_tm_epos( $post->ID, "", FALSE, TRUE );
				}

				if ( is_product() && is_array( $thiscpf ) && ( ! empty( $thiscpf['global'] ) || ! empty( $thiscpf['local'] ) ) ) {
					if ( $product &&
					     ( is_object( $product ) && ! is_callable( array( $product, "get_price" ) ) ) ||
					     ( ! is_object( $product ) )
					) {
						$product = wc_get_product( $post->ID );
					}
					if ( $product &&
					     is_object( $product ) && is_callable( array( $product, "get_price" ) )
					) {

						if ( ! (float) $product->get_price() > 0 ) {
							if ( $this->tm_epo_replacement_free_price_text ) {
								$this->current_free_text = $this->tm_epo_replacement_free_price_text;
								add_filter( 'woocommerce_get_price_html', array( $this, 'get_price_html' ), 10, 2 );
							} else {
								if ( $this->tm_epo_use_from_on_price == "no" ) {
									$this->current_free_text = '';
									remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
								}
							}
						}

						add_filter( 'woocommerce_get_price_html', array( $this, 'related_get_price_html' ), 10, 2 );

					}
				} else {
					if ( is_shop() || is_product_category() || is_product_tag() ) {
						add_filter( 'woocommerce_get_price_html', array( $this, 'get_price_html_shop' ), 10, 2 );
					} elseif ( ! is_product() && $this->is_enabled_shortcodes() ) {
						if ( $this->tm_epo_replacement_free_price_text ) {
							$this->current_free_text = $this->tm_epo_replacement_free_price_text;
							add_filter( 'woocommerce_get_price_html', array( $this, 'get_price_html' ), 10, 2 );
						} else {
							if ( $this->tm_epo_use_from_on_price == "no" ) {
								$this->current_free_text = '';
								remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
							}
							add_filter( 'woocommerce_get_price_html', array( $this, 'get_price_html' ), 10, 2 );
						}
					} elseif ( is_product() ) {
						add_filter( 'woocommerce_get_price_html', array( $this, 'related_get_price_html2' ), 10, 2 );
					}
				}
			} else {
				if ( $this->is_quick_view() ) {
					if ( $this->tm_epo_replacement_free_price_text ) {
						$this->current_free_text = $this->tm_epo_replacement_free_price_text;
						add_filter( 'woocommerce_get_price_html', array( $this, 'get_price_html' ), 10, 2 );
					} else {
						add_filter( 'woocommerce_get_price_html', array( $this, 'get_price_html' ), 10, 2 );
						if ( $this->tm_epo_use_from_on_price == "no" ) {
							$this->current_free_text = '';
							remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
						}
					}
				}
			}
		} elseif ( $this->tm_epo_replacement_free_price_text ) {
			$this->current_free_text = $this->tm_epo_replacement_free_price_text;
			add_filter( 'woocommerce_get_price_html', array( $this, 'get_price_html' ), 10, 2 );
		}

		if ( $this->tm_epo_use_from_on_price == "yes" && is_product() && $post ) {
			if ( $product &&
			     ( is_object( $product ) && ! is_callable( array( $product, "get_price" ) ) ) ||
			     ( ! is_object( $product ) )
			) {
				$product = wc_get_product( $post->ID );
			}
			if ( $product && is_object( $product ) && is_callable( array( $product, "get_price" ) ) ) {
				$this->current_free_text = $this->tm_get_price_html( $product->get_price(), $product );
			}
		}

	}

	/**
	 * Get the theme name
	 *
	 * @param string $var
	 *
	 * @return false|string
	 */
	public function get_theme( $var = '' ) {

		$out = '';
		if ( function_exists( 'wp_get_theme' ) ) {
			$theme = wp_get_theme();
			if ( $theme ) {
				$out = $theme->get( $var );
			}
		}

		return $out;

	}

	/**
	 * Check if we have a support theme quickview
	 *
	 * @return bool
	 */
	public function is_supported_quick_view() {

		$theme_name = strtolower( $this->get_theme( 'Name' ) );
		$theme      = explode( " ", $theme_name );
		if ( isset( $theme[0] ) && isset( $theme[1] ) ) {
			$theme = $theme[0];
		} else {
			$theme = explode( "-", $theme_name );
			if ( isset( $theme[0] ) && isset( $theme[1] ) ) {
				$theme = $theme[0];
			}
		}

		if ( is_array( $theme ) ) {
			$theme = $theme_name;
		}

		if (
			$theme == 'flatsome' // https://themeforest.net/item/flatsome-multipurpose-responsive-woocommerce-theme/5484319
			|| $theme == "kleo" // https://themeforest.net/item/kleo-pro-community-focused-multipurpose-buddypress-theme/6776630
			|| $theme == "venedor" // https://themeforest.net/item/venedor-responsive-prestashop-theme/8743123
			|| $theme == "elise" // https://themeforest.net/item/elise-modern-multipurpose-wordpress-theme/10768925
			|| $theme == "minshop" // https://themify.me/themes/minshop
			|| $theme == "porto" // https://themeforest.net/item/porto-responsive-wordpress-ecommerce-theme/9207399
			|| $theme == "grace" // https://demo.themedelights.com/Wordpress/WP001/
			|| $theme == "woodmart" // https://themeforest.net/item/woodmart-woocommerce-wordpress-theme/20264492
		) {
			return TRUE;
		}

		return FALSE;

	}

	/**
	 * Check if plugin scripts can be loaded
	 *
	 * @since 1.0
	 */
	public function can_load_scripts() {

		if (
			(
				( class_exists( 'WC_Quick_View' ) || $this->is_supported_quick_view() )
				&& (
					THEMECOMPLETE_EPO()->wc_vars["is_shop"]
					|| THEMECOMPLETE_EPO()->wc_vars["is_product_category"]
					|| THEMECOMPLETE_EPO()->wc_vars["is_product_tag"] ) )
			|| $this->is_enabled_shortcodes()
			|| THEMECOMPLETE_EPO()->wc_vars["is_product"]
			|| THEMECOMPLETE_EPO()->wc_vars["is_cart"]
			|| THEMECOMPLETE_EPO()->wc_vars["is_checkout"]
			|| THEMECOMPLETE_EPO()->wc_vars["is_order_received_page"]
			|| ( $this->tm_epo_enable_in_shop == "yes"
			     && (
				     THEMECOMPLETE_EPO()->wc_vars["is_shop"]
				     || THEMECOMPLETE_EPO()->wc_vars["is_product_category"]
				     || THEMECOMPLETE_EPO()->wc_vars["is_product_tag"] ) )
		) {

			return TRUE;

		}

		return FALSE;

	}

	/**
	 * Flag to check if we are in the product shortcode
	 *
	 * @since 1.0
	 */
	public function woocommerce_shortcode_before_product_loop() {

		$this->is_in_product_shortcode = TRUE;

	}

	/**
	 * Flag to check if we are in the product shortcode
	 *
	 * @since 1.0
	 */
	public function woocommerce_shortcode_after_product_loop() {

		$this->is_in_product_shortcode = FALSE;

	}

	/**
	 * Displays options in [product] shortcode
	 *
	 * @since 1.0
	 */
	public function tm_enable_options_on_product_shortcode() {

		if ( $this->is_in_product_shortcode ) {
			$this->tm_woocommerce_after_shop_loop_item();
		}

	}

	/**
	 * Displays options in shop page
	 *
	 * @since 1.0
	 */
	public function tm_woocommerce_after_shop_loop_item() {

		$post_id = get_the_ID();
		$has_epo = THEMECOMPLETE_EPO_API()->has_options( $post_id );
		if ( THEMECOMPLETE_EPO_API()->is_valid_options( $has_epo ) ) {
			echo '<div class="tm-has-options"><form class="cart">';
			THEMECOMPLETE_EPO_DISPLAY()->frontend_display( $post_id, "tc_" . $post_id, FALSE );
			echo '</form></div>';
		}

	}

	/**
	 * Generate min/max prices for the $product
	 *
	 * @since 1.0
	 */
	public function add_product_tc_prices( $product = FALSE ) {

		if ( $product ) {
			$id = themecomplete_get_id( $product );

			if ( isset( $this->product_minmax[ $id ] ) ) {
				return $this->product_minmax[ $id ];
			}

			$this->product_minmax[ $id ] = array(
				'tc_min_price'    => 0,
				'tc_max_price'    => 0,
				'tc_min_variable' => 0,
				'tc_max_variable' => 0,
				'tc_min_max'      => FALSE,
			);

			$epos = $this->get_product_tm_epos( $id, "", FALSE, TRUE );

			if ( is_array( $epos ) && ( ! empty( $epos['global'] ) || ! empty( $epos['local'] ) ) ) {
				if ( ! empty( $epos['price'] ) ) {

					$minmax = THEMECOMPLETE_EPO_HELPER()->sum_array_values( $epos, TRUE );

					if ( ! isset( $minmax['min'] ) ) {
						$minmax['min'] = 0;
					}
					if ( ! isset( $minmax['max'] ) ) {
						$minmax['max'] = 0;
					}
					$min                    = $minmax['min'];
					$max                    = $minmax['max'];
					$minmax['tc_min_price'] = $min;
					$minmax['tc_max_price'] = $max;

					$minmax['tc_min_variable'] = $min;
					$minmax['tc_max_variable'] = $max;

					$minmax['tc_min_max']        = TRUE;
					$this->product_minmax[ $id ] = array(
						'tc_min_price' => $min,
						'tc_max_price' => $max,

						'tc_min_variable' => $min,
						'tc_max_variable' => $max,

						'tc_min_max' => TRUE,
					);

					if ( is_array( $min ) && is_array( $max ) ) {
						$this->product_minmax[ $id ] = array(
							'tc_min_price'    => min( $min ),
							'tc_max_price'    => max( $max ),
							'tc_min_variable' => $min,
							'tc_max_variable' => $max,
							'tc_min_max'      => TRUE,
						);
						$minmax['tc_min_price']      = min( $min );
						$minmax['tc_max_price']      = max( $max );
						$minmax['tc_min_variable']   = $min;
						$minmax['tc_max_variable']   = $max;
					}

					return $minmax;
				} else {
					return $this->product_minmax[ $id ];
				}
			} else {
				$this->product_minmax[ $id ] = FALSE;
			}

		}

		return FALSE;

	}

	/**
	 * Alter the price filter
	 *
	 * @since 4.8.4
	 */
	public function woocommerce_product_get_price( $price = 0, $product = FALSE ) {

		if ( $price === '' ) {

			$minmax = $this->add_product_tc_prices( $product );
			if ( $minmax !== FALSE ) {
				$price = 0;
			}

		}

		return $price;

	}

	/**
	 * Alter product display price to include possible option pricing
	 *
	 * @since 1.0
	 */
	public function tm_woocommerce_get_price( $price = 0, $product = FALSE ) {

		$this->tm_woocommerce_get_price_flag ++;

		if ( $this->tm_woocommerce_get_price_flag === 1 ) {
			if ( ! is_admin() && ! $this->wc_vars['is_product'] && $this->tm_epo_use_from_on_price == "no" ) {

				add_filter( 'woocommerce_get_price_html', array( $this, 'tm_get_price_html' ), 11, 2 );
				add_filter( 'woocommerce_get_variation_price_html', array( $this, 'tm_get_price_html' ), 11, 2 );

			} elseif ( $minmax = $this->add_product_tc_prices( $product ) ) {

				add_filter( 'woocommerce_get_price_html', array( $this, 'tm_get_price_html' ), 11, 2 );
				add_filter( 'woocommerce_get_variation_price_html', array( $this, 'tm_get_price_html' ), 11, 2 );

			}
		}

		return $price;

	}

	/**
	 * Alter product display price to include possible option pricing
	 *
	 * @since 1.0
	 */
	public function tm_woocommerce_show_variation_price( $show = TRUE, $product = FALSE, $variation = FALSE ) {

		if ( $product && $variation ) {
			$epos = $this->get_product_tm_epos( themecomplete_get_id( $product ), "", FALSE, TRUE );
			if ( is_array( $epos ) && ( ! empty( $epos['global'] ) || ! empty( $epos['local'] ) ) ) {
				if ( ! empty( $epos['price'] ) ) {
					$minmax = THEMECOMPLETE_EPO_HELPER()->sum_array_values( $epos );
					if ( ! empty( $minmax['max'] ) ) {
						$show = TRUE;
					}
				}
			}
		}

		return $show;

	}

	/**
	 * Returns the product's active price
	 *
	 * @since 1.0
	 */
	public function tc_get_price( $product = FALSE ) {

		$tc_min_price = 0;
		$id           = themecomplete_get_id( $product );
		if ( isset( $this->product_minmax[ $id ] ) ) {
			$tc_min_price = $this->product_minmax[ $id ]['tc_min_price'];
		}

		if ( empty( $this->product_minmax[ $id ]['is_override'] ) ) {
			$price = (float) apply_filters( 'wc_epo_product_price', $product->get_price(), "", FALSE ) + (float) $tc_min_price;
		} else {
			$price = (float) $tc_min_price;
		}
		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.7.0', '<' ) ) {
			return apply_filters( 'tc_woocommerce_get_price', $price, $product );
		} else {
			return apply_filters( 'tc_woocommerce_product_get_price', $price, $product );
		}

	}

	/**
	 * Returns the price including or excluding tax, based on the 'woocommerce_tax_display_shop' setting.
	 *
	 * @since 1.0
	 */
	public function tc_get_display_price( $product = FALSE, $price = '', $qty = 1 ) {

		if ( $price === '' ) {
			$price = $this->tc_get_price( $product );
		}

		$tax_display_mode = get_option( 'woocommerce_tax_display_shop' );
		$display_price    = $tax_display_mode == 'incl' ? themecomplete_get_price_including_tax( $product, array( 'qty' => $qty, 'price' => $price ) ) : themecomplete_get_price_excluding_tax( $product, array( 'qty' => $qty, 'price' => $price ) );

		return $display_price;

	}

	/**
	 * Returns the product's regular price.
	 *
	 * @since 1.0
	 */
	public function tc_get_regular_price( $product = FALSE ) {

		$tc_min_price = 0;
		$id           = themecomplete_get_id( $product );
		if ( isset( $this->product_minmax[ $id ] ) ) {
			$tc_min_price = $this->product_minmax[ $id ]['tc_min_price'];
		}
		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.7.0', '<' ) ) {
			return apply_filters( 'tc_woocommerce_get_regular_price', (float) apply_filters( 'wc_epo_product_price', $product->get_regular_price(), "", FALSE ) + (float) $tc_min_price, $product );
		} else {
			return apply_filters( 'tc_woocommerce_product_get_regular_price', (float) apply_filters( 'wc_epo_product_price', $product->get_regular_price(), "", FALSE ) + (float) $tc_min_price, $product );
		}


	}

	/**
	 * Alter product display price to include possible option pricing
	 *
	 * @since 1.0
	 */
	public function tm_get_price_html( $price = '', $product = FALSE ) {

		$original_price = $price;

		$min_max = $this->get_product_min_max_prices( $product );
		$type    = themecomplete_get_product_type( $product );

		if ( empty( $min_max ) || $type == 'variation' ) {
			$check_filter_1 = has_filter( 'woocommerce_get_price_html', array( $this, 'tm_get_price_html' ) );
			$check_filter_2 = has_filter( 'woocommerce_get_variation_price_html', array( $this, 'tm_get_price_html' ) );
			if ( $check_filter_1 ) {
				remove_filter( 'woocommerce_get_price_html', array( $this, 'tm_get_price_html' ), 11 );
			}
			if ( $check_filter_2 ) {
				remove_filter( 'woocommerce_get_variation_price_html', array( $this, 'tm_get_price_html' ), 11 );
			}
			$price = $product->get_price_html();
			if ( $check_filter_1 ) {
				add_filter( 'woocommerce_get_price_html', array( $this, 'tm_get_price_html' ), 11, 2 );
			}
			if ( $check_filter_2 ) {
				add_filter( 'woocommerce_get_variation_price_html', array( $this, 'tm_get_price_html' ), 11, 2 );
			}

			return $price;
		}

		$use_from  = ( $this->tm_epo_use_from_on_price == "yes" );
		$free_text = ( $this->tm_epo_remove_free_price_label == 'yes' )
			?
			( $this->tm_epo_replacement_free_price_text )
				? $this->tm_epo_replacement_free_price_text
				: ''
			: esc_attr__( 'Free!', 'woocommerce' );

		$min               = $min_max['min_raw'];
		$max               = $min_max['max_raw'];
		$min_price         = $min_max['min_price'];
		$max_price         = $min_max['max_price'];
		$min_regular_price = $min_max['min_regular_price'];
		$max_regular_price = $min_max['max_regular_price'];

		if ( $type == 'variable' || $type == 'variable-subscription' ) {
			$is_free = $min_price == 0 && $max_price == 0;

			if ( $product->is_on_sale() ) {

				$displayed_price = ( function_exists( 'wc_get_price_to_display' )
					? wc_format_sale_price( $min_regular_price, $min_price )
					: '<del>' . ( is_numeric( $min_regular_price ) ? wc_price( $min_regular_price ) : $min_regular_price ) . '</del> <ins>' . ( is_numeric( $min_price ) ? wc_price( $min_price ) : $min_price ) . '</ins>'
				);
				$price           = $min_price !== $max_price
					? ! $use_from
						? sprintf( esc_html_x( '%1$s&ndash;%2$s', 'Price range: from-to', 'woocommerce' ), themecomplete_price( $min_price ), themecomplete_price( $max_price ) )
						: ( function_exists( 'wc_get_price_html_from_text' ) ? wc_get_price_html_from_text() : $product->get_price_html_from_text() ) . $displayed_price
					: $displayed_price;

				$regular_price = $min_regular_price !== $max_regular_price
					? ! $use_from
						? sprintf( esc_html_x( '%1$s&ndash;%2$s', 'Price range: from-to', 'woocommerce' ), themecomplete_price( $min_regular_price ), themecomplete_price( $max_regular_price ) )
						: ( function_exists( 'wc_get_price_html_from_text' ) ? wc_get_price_html_from_text() : $product->get_price_html_from_text() ) . themecomplete_price( $min_regular_price )
					: themecomplete_price( $min_regular_price );
				$regular_price = '<del>' . $regular_price . '</del>';
				$price         = ( ! $use_from
						? ( $regular_price . ' <ins>' . $price . '</ins>' )
						: $price )
				                 . $product->get_price_suffix();

			} elseif ( $is_free ) {
				$price = apply_filters( 'woocommerce_variable_free_price_html', $free_text, $product );
			} else {
				$price = $min_price !== $max_price
					? ! $use_from
						? sprintf( esc_html_x( '%1$s&ndash;%2$s', 'Price range: from-to', 'woocommerce' ), themecomplete_price( $min_price ), themecomplete_price( $max_price ) )
						: ( function_exists( 'wc_get_price_html_from_text' ) ? wc_get_price_html_from_text() : $product->get_price_html_from_text() ) . themecomplete_price( $min_price )
					: themecomplete_price( $min_price );
				$price = $price . $product->get_price_suffix();
			}
		} else {

			$display_price         = $min_price;
			$display_regular_price = $min_regular_price;

			$price = '';
			if ( $this->tc_get_price( $product ) > 0 ) {

				if ( $product->is_on_sale() && $this->tc_get_regular_price( $product ) ) {
					if ( $use_from && ( $max > 0 || $max > $min ) ) {

						$displayed_price = ( function_exists( 'wc_get_price_to_display' )
							? wc_format_sale_price( $display_regular_price, $display_price )
							: '<del>' . ( is_numeric( $display_regular_price ) ? wc_price( $display_regular_price ) : $display_regular_price ) . '</del> <ins>' . ( is_numeric( $display_price ) ? wc_price( $display_price ) : $display_price ) . '</ins>'
						);
						$price           .= ( function_exists( 'wc_get_price_html_from_text' )
								? wc_get_price_html_from_text()
								: $product->get_price_html_from_text() )
						                    . $displayed_price;
					} else {
						$price .= $original_price;
					}
					$price .= $product->get_price_suffix();

				} else {
					if ( $use_from && ( $max > 0 || $max > $min ) ) {
						$price .= ( function_exists( 'wc_get_price_html_from_text' ) ? wc_get_price_html_from_text() : $product->get_price_html_from_text() );
					}
					$price .= themecomplete_price( $display_price ) . $product->get_price_suffix();

				}
			} elseif ( $this->tc_get_price( $product ) === '' ) {

				$price = apply_filters( 'woocommerce_empty_price_html', '', $product );

			} elseif ( $this->tc_get_price( $product ) == 0 ) {
				if ( $product->is_on_sale() && $this->tc_get_regular_price( $product ) ) {
					if ( $use_from && ( $max > 0 || $max > $min ) ) {
						$price .= ( function_exists( 'wc_get_price_html_from_text' ) ? wc_get_price_html_from_text() : $product->get_price_html_from_text() ) . themecomplete_price( ( $min > 0 ) ? $min : 0 );
					} else {

						$price .= $original_price;

						$price = apply_filters( 'woocommerce_free_sale_price_html', $price, $product );
					}

				} else {
					if ( $use_from && ( $max > 0 || $max > $min ) ) {
						$price .= ( function_exists( 'wc_get_price_html_from_text' ) ? wc_get_price_html_from_text() : $product->get_price_html_from_text() ) . themecomplete_price( ( $min > 0 ) ? $min : 0 );
					} else {

						$price = '<span class="amount">' . $free_text . '</span>';

						$price = apply_filters( 'woocommerce_free_price_html', $price, $product );
					}

				}
			}
		}

		return apply_filters( 'wc_epo_get_price_html', $price, $product );

	}

	/**
	 * Image filter
	 *
	 * @since 1.0
	 */
	public function tm_image_url( $url = "" ) {

		// WP Rocket cdn
		if ( defined( 'WP_ROCKET_VERSION' ) && function_exists( 'get_rocket_cdn_cnames' ) && function_exists( 'get_rocket_cdn_url' ) ) {
			$zone = array( 'all', 'images' );
			if ( is_array( $url ) ) {
				foreach ( $url as $key => $value ) {
					$ext = pathinfo( $value, PATHINFO_EXTENSION );
					if ( is_admin() && $ext != 'php' ) {
						continue;
					}
					if ( $cnames = get_rocket_cdn_cnames( $zone ) ) {
						$url[ $key ] = get_rocket_cdn_url( $value, $zone );
					}
				}

			} else {
				$ext = pathinfo( $url, PATHINFO_EXTENSION );

				if ( ! ( is_admin() && $ext != 'php' ) && $cnames = get_rocket_cdn_cnames( $zone ) ) {
					$url = get_rocket_cdn_url( $url, $zone );
				}

			}

		}
		// SSL support
		if ( is_ssl() ) {
			$url = preg_replace( "/^http:/i", "https:", $url );
		}

		return $url;

	}

	/**
	 * Flag related products start
	 *
	 * @since 1.0
	 */
	public function tm_enable_post_class() {

		$this->tm_related_products_output = TRUE;

	}

	/**
	 * Flag related products end
	 *
	 * @since 1.0
	 */
	public function tm_disable_post_class() {

		$this->tm_related_products_output = FALSE;

	}

	/**
	 * Flag related upsells start
	 *
	 * @since 1.0
	 */
	public function tm_woocommerce_related_products_args( $args ) {

		$this->tm_disable_post_class();
		$this->in_related_upsells = TRUE;

		return $args;

	}

	/**
	 * Flag related upsells end
	 *
	 * @since 1.0
	 */
	public function tm_woocommerce_after_single_product_summary() {

		$this->in_related_upsells = FALSE;

	}

	/**
	 * Add custom class to product div used to initialize the plugin JavaScript
	 *
	 * @since 1.0
	 */
	public function tm_post_class( $classes = "" ) {

		$post_id = get_the_ID();

		if (
			// disable in admin interface
			is_admin() ||

			// disable if not in the product div
			! $this->tm_related_products_output ||

			// disable if not in a product page, shop or product archive page
			! (
				'product' == get_post_type( $post_id ) ||
				$this->wc_vars['is_product'] ||
				$this->wc_vars['is_shop'] ||
				$this->wc_vars['is_product_category'] ||
				$this->wc_vars['is_product_tag']
			) ||

			// disable if options are not visible in shop/archive pages
			( (
				  $this->wc_vars['is_shop'] ||
				  $this->wc_vars['is_product_category'] ||
				  $this->wc_vars['is_product_tag']
			  )
			  &&
			  $this->tm_epo_enable_in_shop == "no"
			)

		) {
			return $classes;
		}

		// enabling "global $post;" here will cause issues on certain Visual composer shortcodes.

		if ( $post_id && ( $this->wc_vars['is_product'] || 'product' == get_post_type( $post_id ) ) ) {

			$has_epo = THEMECOMPLETE_EPO_API()->has_options( $post_id );


			// Product has styled variations
			if ( ! empty( $has_epo['variations'] ) && empty( $has_epo['variations_disabled'] ) ) {
				$classes[] = 'tm-has-styled-variations';
			}

			// Product has extra options
			if ( THEMECOMPLETE_EPO_API()->is_valid_options( $has_epo ) ) {
				$classes[] = 'tm-has-options';

				// Product doens't have extra options but the final total box is enabled for all products
			} elseif ( $this->tm_epo_enable_final_total_box_all == "yes" ) {

				$classes[] = 'tm-no-options-pxq';

				// Search for composite products extra options
			} else {

				$terms        = get_the_terms( $post_id, 'product_type' );
				$product_type = ! empty( $terms ) && isset( current( $terms )->name ) ? sanitize_title( current( $terms )->name ) : 'simple';

				if ( ( $product_type == 'bto' || $product_type == 'composite' )
				     && ! THEMECOMPLETE_EPO_API()->is_valid_options( $has_epo )
				     && $this->tm_epo_enable_final_total_box_all != "yes"
				) {

					// search components for options
					$product = wc_get_product( $post_id );
					if ( is_callable( array( $product, 'get_composite_data' ) ) ) {
						$composite_data = $product->get_composite_data();

						foreach ( $composite_data as $component_id => $component_data ) {

							$component_options = array();

							if ( class_exists( 'WC_CP_Component' ) && method_exists( 'WC_CP_Component', 'query_component_options' ) ) {
								$component_options = WC_CP_Component::query_component_options( $component_data );
							} elseif ( function_exists( 'WC_CP' ) ) {
								$component_options = WC_CP()->api->get_component_options( $component_data );
							} else {
								global $woocommerce_composite_products;
								if ( is_object( $woocommerce_composite_products ) && function_exists( 'WC_CP' ) ) {
									$component_options = WC_CP()->api->get_component_options( $component_data );
								} else {
									if ( isset( $component_data['assigned_ids'] ) && is_array( $component_data['assigned_ids'] ) ) {
										$component_options = $component_data['assigned_ids'];
									}
								}
							}

							foreach ( $component_options as $key => $pid ) {
								$has_options = THEMECOMPLETE_EPO_API()->has_options( $pid );
								if ( THEMECOMPLETE_EPO_API()->is_valid_options_or_variations( $has_options ) ) {
									$classes[] = 'tm-no-options-composite';

									return $classes;
								}
							}

						}
					}

				}

				$classes[] = 'tm-no-options';

			}
		}

		return $classes;

	}

	/**
	 * Check if we are in edit mode
	 *
	 * @since 1.0
	 */
	public function is_edit_mode() {

		return ! empty( $this->cart_edit_key ) && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'tm-edit' );

	}

	/**
	 * Check if the plugin is active for the user
	 *
	 * @since 1.0
	 */
	public function check_enable() {

		$enable         = FALSE;
		$enabled_roles  = $this->tm_epo_roles_enabled;
		$disabled_roles = $this->tm_epo_roles_disabled;

		if ( isset( $this->tm_meta_cpf['override_enabled_roles'] ) && $this->tm_meta_cpf['override_enabled_roles'] !== "" ) {
			$enabled_roles = $this->tm_meta_cpf['override_enabled_roles'];
		}
		if ( isset( $this->tm_meta_cpf['override_disabled_roles'] ) && $this->tm_meta_cpf['override_disabled_roles'] !== "" ) {
			$disabled_roles = $this->tm_meta_cpf['override_disabled_roles'];
		}
		// Get all roles
		$current_user = wp_get_current_user();

		if ( ! is_array( $enabled_roles ) ) {
			$enabled_roles = array( $enabled_roles );
		}
		if ( ! is_array( $disabled_roles ) ) {
			$disabled_roles = array( $disabled_roles );
		}

		// Check if plugin is enabled for everyone
		foreach ( $enabled_roles as $key => $value ) {
			if ( $value == "@everyone" ) {
				$enable = TRUE;
			}
			if ( $value == "@loggedin" && is_user_logged_in() ) {
				$enable = TRUE;
			}
		}

		if ( $current_user instanceof WP_User ) {
			$roles = $current_user->roles;
			// Check if plugin is enabled for current user
			if ( is_array( $roles ) ) {

				foreach ( $roles as $key => $value ) {
					if ( in_array( $value, $enabled_roles ) ) {
						$enable = TRUE;
						break;
					}
				}

				foreach ( $roles as $key => $value ) {
					if ( in_array( $value, $disabled_roles ) ) {
						$enable = FALSE;
						break;
					}
				}

			}
		}

		return $enable;

	}

	/**
	 * Check if we are on a supported quickview mode
	 *
	 * @since 1.0
	 */
	public function is_quick_view() {

		return apply_filters( 'woocommerce_tm_quick_view', FALSE );

	}

	/**
	 * Check if the setting "Enable plugin for WooCommerce shortcodes" is active
	 *
	 * @since 1.0
	 */
	public function is_enabled_shortcodes() {
		return ( $this->tm_epo_enable_shortcodes == "yes" );

	}

	/**
	 * Apply wc_epo_get_current_currency_price filter to prices
	 *
	 * @since 1.0
	 */
	public function tm_epo_price_filtered( $price = "", $type = "" ) {

		return apply_filters( 'wc_epo_get_current_currency_price', $price, $type );

	}

	/**
	 * Enable shortcodes for labels
	 *
	 * @since 1.0
	 */
	public function tm_epo_option_name( $label = "", $args = NULL, $counter = NULL, $value = NULL, $vlabel = NULL ) {

		if ( $this->tm_epo_show_price_inside_option == 'yes' &&
		     ( empty( $args['hide_amount'] ) || $this->tm_epo_show_price_inside_option_hidden_even == 'yes' ) &&
		     $value !== NULL &&
		     $vlabel !== NULL &&
		     isset( $args['rules_type'] ) &&
		     isset( $args['rules_type'][ $value ] ) &&
		     isset( $args['rules_type'][ $value ][0] ) &&
		     empty( $args['rules_type'][ $value ][0] )
		) {
			$display_price = ( isset( $args['rules_filtered'][ $value ][0] ) ) ? $args['rules_filtered'][ $value ][0] : '';
			$qty           = 1;

			if ( $this->tm_epo_multiply_price_inside_option == 'yes' ) {
				if ( ! empty( $args['quantity'] ) && ! empty( $args['quantity_default_value'] ) ) {
					$qty = floatval( $args['quantity_default_value'] );
				}
			}
			$display_price = floatval( $display_price ) * $qty;

			if ( ( $this->tm_epo_auto_hide_price_if_zero == "yes" && ! empty( $display_price ) ) || ( $this->tm_epo_auto_hide_price_if_zero != "yes" && $display_price !== '' ) ) {
				$symbol = '';
				if ( $this->tm_epo_global_options_price_sign == '' ) {
					$symbol = apply_filters( 'wc_epo_price_in_dropdown_plus_sign', "+" );
				}

				global $product;
				if ( $product && wc_tax_enabled() ) {
					$tax_display_mode = get_option( 'woocommerce_tax_display_shop' );

					if ( $tax_display_mode == 'excl' ) {
						$display_price = themecomplete_get_price_excluding_tax( $product, array( 'price' => $display_price ) );
					} else {
						$display_price = themecomplete_get_price_including_tax( $product, array( 'price' => $display_price ) );
					}
				}

				if ( floatval( $display_price ) == 0 ) {
					$symbol = '';
				} elseif ( floatval( $display_price ) < 0 ) {
					$symbol = apply_filters( 'wc_epo_price_in_dropdown_minus_sign', "-" );
				}
				$display_price = apply_filters( 'wc_epo_price_in_dropdown', ' (' . $symbol . wc_price( abs( $display_price ) ) . ')', $display_price );

				$label .= $display_price;

			}

		}

		return apply_filters( 'wc_epo_label', apply_filters( 'wc_epo_kses', $label, $label ) );

	}

	/**
	 * Alters the Free label html
	 *
	 * @since 1.0
	 */
	public function get_price_html( $price = "", $product = "" ) {

		if ( $product && is_object( $product ) && is_callable( array( $product, "get_price" ) ) ) {
			if ( (float) $product->get_price() > 0 ) {
				return $price;
			} else {
				return sprintf( $this->tm_epo_replacement_free_price_text, $price );
			}
		} else {
			return sprintf( $this->tm_epo_replacement_free_price_text, $price );
		}

	}

	/**
	 * Fix for related products when replacing free label
	 *
	 * @since 1.0
	 */
	public function related_get_price_html( $price = "", $product = "" ) {

		if ( $product && is_object( $product ) && is_callable( array( $product, "get_price" ) ) ) {
			if ( (float) $product->get_price() > 0 ) {
				return $price;
			} else {
				if ( $this->tm_epo_replacement_free_price_text ) {
					return sprintf( $this->tm_epo_replacement_free_price_text, $price );
				} else {
					$price = '';
				}
			}
		} else {
			if ( $this->tm_epo_replacement_free_price_text ) {
				return sprintf( $this->tm_epo_replacement_free_price_text, $price );
			} else {
				$price = '';
			}
		}

		return $price;

	}

	/**
	 * Fix for related products when replacing free label
	 *
	 * @since 1.0
	 */
	public function related_get_price_html2( $price = "", $product = "" ) {

		if ( $product && is_object( $product ) && is_callable( array( $product, "get_price" ) ) ) {

			if ( (float) $product->get_price() > 0 ) {
				return $price;
			} else {

				$thiscpf = $this->get_product_tm_epos( themecomplete_get_id( $product ), "", FALSE, TRUE );

				if ( is_array( $thiscpf ) && ( ! empty( $thiscpf['global'] ) || ! empty( $thiscpf['local'] ) ) ) {
					if ( $this->tm_epo_replacement_free_price_text ) {
						return sprintf( $this->tm_epo_replacement_free_price_text, $price );
					} else {
						$price = '';
					}
				}
			}
		}

		return $price;

	}

	/**
	 * Free label text replacement
	 *
	 * @since 1.0
	 */
	public function get_price_html_shop( $price = "", $product = "" ) {

		if ( $product &&
		     is_object( $product ) && is_callable( array( $product, "get_price" ) )
		     && ! (float) $product->get_price() > 0
		) {

			if ( $this->tm_epo_replacement_free_price_text ) {
				$price = sprintf( $this->tm_epo_replacement_free_price_text, $price );
			} else {
				$price = '';
			}
		}

		return $price;

	}

	/**
	 * Replaces add to cart text when the force select setting is enabled
	 *
	 * @since 1.0
	 */
	public function add_to_cart_text( $text = "" ) {

		global $product;

		if ( ( is_product() && ! $this->in_related_upsells ) || $this->is_in_product_shortcode ) {
			return $text;
		}
		if ( $this->tm_epo_enable_in_shop == "no"
		     && $this->tm_epo_force_select_options == "display"
		     && is_object( $product )
		     && property_exists( $product, 'id' )
		) {
			$has_epo = THEMECOMPLETE_EPO_API()->has_options( themecomplete_get_id( $product ) );
			if ( THEMECOMPLETE_EPO_API()->is_valid_options( $has_epo ) ) {
				$text = ( ! empty( $this->tm_epo_force_select_text ) ) ? esc_html( $this->tm_epo_force_select_text ) : esc_html__( 'Select options', 'woocommerce-tm-extra-product-options' );
			}
		}
		if ( $this->tm_epo_enable_in_shop == "yes" && ! $this->in_related_upsells ) {
			$text = esc_html__( 'Add to cart', 'woocommerce' );
		}

		return $text;

	}

	/**
	 * Prevenets ajax add to cart when product has extra options and the force select setting is enabled
	 *
	 * @since 1.0
	 */
	public function add_to_cart_url( $url = "" ) {

		global $product;

		if ( ! is_product()
		     && $this->tm_epo_force_select_options == "display"
		     && is_object( $product )
		     && property_exists( $product, 'id' )
		) {
			$has_epo = THEMECOMPLETE_EPO_API()->has_options( themecomplete_get_id( $product ) );
			if ( THEMECOMPLETE_EPO_API()->is_valid_options( $has_epo ) ) {
				$url = get_permalink( themecomplete_get_id( $product ) );
			}
		}

		return $url;

	}

	/**
	 * Redirect to product URL
	 * THis is used when using the forced select setting
	 *
	 * @since 1.0
	 */
	public function woocommerce_cart_redirect_after_error( $url = "", $product_id = "" ) {

		$product = wc_get_product( $product_id );

		if ( $this->tm_epo_force_select_options == "display"
		     && is_object( $product )
		     && property_exists( $product, 'id' )
		) {
			$has_epo = THEMECOMPLETE_EPO_API()->has_options( themecomplete_get_id( $product ) );
			if ( THEMECOMPLETE_EPO_API()->is_valid_options( $has_epo ) ) {
				$url = get_permalink( themecomplete_get_id( $product ) );
			}
		}

		return $url;

	}

	/**
	 * Sets current product settings
	 *
	 * @since 1.0
	 */
	public function set_tm_meta( $override_id = 0 ) {

		if ( empty( $override_id ) ) {
			if ( isset( $_REQUEST['add-to-cart'] ) ) {
				$override_id = $_REQUEST['add-to-cart'];
			} else {
				global $post;
				if ( ! is_null( $post ) && property_exists( $post, 'ID' ) && property_exists( $post, 'post_type' ) ) {
					if ( $post->post_type != "product" ) {
						return;
					}
					$override_id = $post->ID;
				}
			}
		}
		if ( empty( $override_id ) ) {
			return;
		}

		// Translated products inherit original product meta overrides
		$override_id = floatval( THEMECOMPLETE_EPO_WPML()->get_original_id( $override_id, 'product' ) );

		$this->tm_meta_cpf = themecomplete_get_post_meta( $override_id, 'tm_meta_cpf', TRUE );
		if ( ! is_array( $this->tm_meta_cpf ) ) {
			$this->tm_meta_cpf = array();
		}
		foreach ( $this->meta_fields as $key => $value ) {
			$this->tm_meta_cpf[ $key ] = isset( $this->tm_meta_cpf[ $key ] ) ? $this->tm_meta_cpf[ $key ] : $value;
		}
		$this->tm_meta_cpf['metainit'] = 1;

	}

	/**
	 * Calculates the formula price
	 *
	 * @since 1.0
	 */
	public function calculate_math_price( $_price, $post_data = array(), $element, $key, $attribute, $per_product_pricing, $cpf_product_price = FALSE, $variation_id, $price_default_value = 0, $currency = FALSE, $current_currency = FALSE, $price_per_currencies = NULL ) {

		$formula = $_price;

		$current_id         = $element['uniqid'];
		$current_attributes = THEMECOMPLETE_EPO_CART()->element_id_array[ $current_id ]['name_inc'];
		if ( ! is_array( $current_attributes ) ) {
			$current_attributes = array( $current_attributes );
		}

		// the number of options the user has selected
		$formula = str_replace( "{this.count}", floatval( count( array_intersect_key( $post_data, array_flip( $current_attributes ) ) ) ), $formula );

		// the total option quantity of this element
		$current_attributes_quantity = array_map( function ( $y ) {
			return $y . '_quantity';
		}, $current_attributes );
		$quantity_intersect          = array_intersect_key( $post_data, array_flip( $current_attributes_quantity ) );
		$formula                     = str_replace( "{this.count.quantity}", floatval( array_sum( $quantity_intersect ) ), $formula );

		// the option quantity of this element
		$current_quantity = "";
		if ( isset( $post_data[ $attribute . "_quantity" ] ) ) {
			$current_quantity = $post_data[ $attribute . "_quantity" ];
		}

		// the option/element quantity
		$formula = str_replace( "{this.quantity}", floatval( $current_quantity ), $formula );

		if ( isset( $element['options'] ) && isset( $element['options'][ $key ] ) ) {
			// the option/element value
			$formula = str_replace( "{this.value}", floatval( $element['options'][ $key ] ), $formula );
			// the option/element value length
			$formula = str_replace( "{this.value.length}", floatval( strlen( $element['options'][ $key ] ) ), $formula );
		} else {
			// the option/element value
			$formula = str_replace( "{this.value}", floatval( $post_data[ $attribute ] ), $formula );
			// the option/element value length
			$formula = str_replace( "{this.value.length}", floatval( strlen( $post_data[ $attribute ] ) ), $formula );
		}

		// product quantity 
		$formula = str_replace( "{quantity}", floatval( $post_data['quantity'] ), $formula );

		// original product price
		$product_price = $cpf_product_price;
		if ( ! $product_price ) {
			$product_price = 0;
		}

		$formula = str_replace( "{product_price}", floatval( $product_price ), $formula );

		preg_match_all( '/\{(\s)*?field\.([^}]*)}/', $_price, $matches );

		if ( is_array( $matches ) && isset( $matches[2] ) && is_array( $matches[2] ) ) {

			foreach ( $matches[2] as $matchkey => $match ) {
				$val = 0;

				$pos = strrpos( $match, "." );

				if ( $pos !== FALSE ) {

					$id   = substr( $match, 0, $pos );
					$type = substr( $match, $pos + 1 );

					$thiselement           = THEMECOMPLETE_EPO_CART()->element_id_array[ $id ];
					$priority              = $thiselement['priority'];
					$pid                   = $thiselement['pid'];
					$section_id            = $thiselement['section_id'];
					$element_key           = $thiselement['element_key'];
					$thiselement           = THEMECOMPLETE_EPO_CART()->global_price_array[ $priority ][ $pid ]['sections'][ $section_id ]['elements'][ $element_key ];
					$_price_per_currencies = isset( $thiselement['price_per_currencies'] ) ? $thiselement['price_per_currencies'] : array();

					$thisattributes = THEMECOMPLETE_EPO_CART()->element_id_array[ $id ]['name_inc'];
					if ( ! is_array( $thisattributes ) ) {
						$thisattributes = array( $thisattributes );
					}

					$thisattributes = array_unique($thisattributes);

					if ( is_array( $thisattributes ) ) {
						foreach ( $thisattributes as $thisattribute ) {
							if ( ! isset( $post_data[ $thisattribute ] ) ) {
								continue;
							}
							$thiskey = $post_data[ $thisattribute ];

							if ( in_array( $type, array( "price", "value", "quantity" ) ) ) {
								switch ( $type ) {
									case 'price':
										$val += floatval( $this->calculate_price( $post_data, $thiselement, $thiskey, $thisattribute, $per_product_pricing, $cpf_product_price, $variation_id, $price_default_value, $currency, $current_currency, $_price_per_currencies ) );
										break;
									case 'value':
										if ( isset( $thiselement['options'] ) && isset( $thiselement['options'][ $thiskey ] ) ) {
											$val += floatval( $thiselement['options'][ $thiskey ] );
										} else {
											$val += floatval( $post_data[ $thisattribute ] );
										}
										break;
									case 'quantity':
										if ( isset( $post_data[ $thisattribute . "_quantity" ] ) ) {
											$val += floatval( $post_data[ $thisattribute . "_quantity" ] );
										}
										break;
								}
							}
						}

						if ( $type === 'count' ) {
							$val = floatval( count( array_intersect_key( $post_data, array_flip( $thisattributes ) ) ) );
						}

					}

				}

				$formula = str_replace( $matches[0][ $matchkey ], $val, $formula );

			}

		}

		$locale   = localeconv();
		$decimals = array( wc_get_price_decimal_separator(), $locale['decimal_point'], $locale['mon_decimal_point'], ',' );

		// Remove whitespace from string.
		$formula = preg_replace( '/\s+/', '', $formula );

		// Remove locale from string.
		$formula = str_replace( $decimals, '.', $formula );

		// Trim invalid start/end characters.
		$formula = rtrim( ltrim( $formula, "\t\n\r\0\x0B+*/" ), "\t\n\r\0\x0B+-*/" );

		// Do the math.
		return $formula ? THEMECOMPLETE_EPO_MATH::evaluate( $formula ) : 0;

	}

	/**
	 * Get element's price type
	 *
	 * @param $tmcp
	 *
	 * @return string
	 */
	public function get_saved_element_price_type( $tmcp ) {
		$price_type = "";
		$key        = isset( $tmcp['key'] ) ? $tmcp['key'] : 0;

		if ( ! isset( $tmcp['element']['rules_type'][ $key ] ) ) {// field price rule
			if ( isset( $tmcp['element']['rules_type'][0][0] ) ) {// general rule
				$price_type = $tmcp['element']['rules_type'][0][0];
			}
		} else {
			if ( isset( $tmcp['element']['rules_type'][ $key ][0] ) ) {// general field variation rule
				$price_type = $tmcp['element']['rules_type'][ $key ][0];
			} elseif ( isset( $tmcp['element']['rules_type'][0][0] ) ) {// general rule
				$price_type = $tmcp['element']['rules_type'][0][0];
			}
		}

		return $price_type;
	}

	/**
	 * Get the element price type
	 *
	 * @since 5.0.11
	 */
	public function get_element_price_type( $price_type_default_value = "", $element, $key, $per_product_pricing, $variation_id ) {

		$_price_type = $price_type_default_value;
		// This currently happens for multiple file uploads
		if ( is_array( $key ) ) {
			$key = 0;
		}
		$key = esc_attr( $key );
		if ( $per_product_pricing ) {

			if ( ! isset( $element['price_rules_type'][ $key ] ) ) {// field price rule
				if ( $variation_id && isset( $element['price_rules_type'][0][ $variation_id ] ) ) {// general variation rule
					$_price_type = $element['price_rules_type'][0][ $variation_id ];
				} elseif ( isset( $element['price_rules_type'][0][0] ) ) {// general rule
					$_price_type = $element['price_rules_type'][0][0];
				}
			} else {
				if ( $variation_id && isset( $element['price_rules_type'][ $key ][ $variation_id ] ) ) {// field price rule
					$_price_type = $element['price_rules_type'][ $key ][ $variation_id ];
				} elseif ( isset( $element['price_rules_type'][ $key ][0] ) ) {// general field variation rule
					$_price_type = $element['price_rules_type'][ $key ][0];
				} elseif ( $variation_id && isset( $element['price_rules_type'][0][ $variation_id ] ) ) {// general variation rule
					$_price_type = $element['price_rules_type'][0][ $variation_id ];
				} elseif ( isset( $element['price_rules_type'][0][0] ) ) {// general rule
					$_price_type = $element['price_rules_type'][0][0];
				}
			}

		}

		return $_price_type;
	}

	/**
	 * Get the element price
	 *
	 * @since 5.0.11
	 */
	public function get_element_price( $price_default_value = 0, $_price_type = "", $element, $key, $per_product_pricing, $variation_id ) {

		$_price = $price_default_value;
		// This currently happens for multiple file uploads
		if ( is_array( $key ) ) {
			$key = 0;
		}
		$key = esc_attr( $key );
		if ( $per_product_pricing ) {

			if ( ! isset( $element['price_rules'][ $key ] ) ) {// field price rule
				if ( $variation_id && isset( $element['price_rules'][0][ $variation_id ] ) ) {// general variation rule
					$_price = $element['price_rules'][0][ $variation_id ];
				} elseif ( isset( $element['price_rules'][0][0] ) ) {// general rule
					$_price = $element['price_rules'][0][0];
				}
			} else {
				if ( $variation_id && isset( $element['price_rules'][ $key ][ $variation_id ] ) ) {// field price rule
					$_price = $element['price_rules'][ $key ][ $variation_id ];
				} elseif ( isset( $element['price_rules'][ $key ][0] ) ) {// general field variation rule
					$_price = $element['price_rules'][ $key ][0];
				} elseif ( $variation_id && isset( $element['price_rules'][0][ $variation_id ] ) ) {// general variation rule
					$_price = $element['price_rules'][0][ $variation_id ];
				} elseif ( isset( $element['price_rules'][0][0] ) ) {// general rule
					$_price = $element['price_rules'][0][0];
				}
			}

			if ( ( $_price_type == "percent" || $_price_type == "percentcurrenttotal" ) && $_price == "" && isset( $element['price_rules_original'] ) ) {
				if ( ! isset( $element['price_rules_original'][ $key ] ) ) {// field price rule
					if ( $variation_id && isset( $element['price_rules_original'][0][ $variation_id ] ) ) {// general variation rule
						$_price = $element['price_rules_original'][0][ $variation_id ];
					} elseif ( isset( $element['price_rules_original'][0][0] ) ) {// general rule
						$_price = $element['price_rules_original'][0][0];
					}
				} else {
					if ( $variation_id && isset( $element['price_rules_original'][ $key ][ $variation_id ] ) ) {// field price rule
						$_price = $element['price_rules_original'][ $key ][ $variation_id ];
					} elseif ( isset( $element['price_rules_original'][ $key ][0] ) ) {// general field variation rule
						$_price = $element['price_rules_original'][ $key ][0];
					} elseif ( $variation_id && isset( $element['price_rules_original'][0][ $variation_id ] ) ) {// general variation rule
						$_price = $element['price_rules_original'][0][ $variation_id ];
					} elseif ( isset( $element['price_rules_original'][0][0] ) ) {// general rule
						$_price = $element['price_rules_original'][0][0];
					}
				}
			}
			if ( $_price_type !== "math" ) {
				$_price = floatval( wc_format_decimal( $_price, FALSE, TRUE ) );
			}

		}

		return $_price;
	}


	/**
	 * Calculates the correct option price
	 *
	 * @since 1.0
	 */
	public function calculate_price( $post_data = NULL, $element, $key, $attribute, $per_product_pricing, $cpf_product_price = FALSE, $variation_id, $price_default_value = 0, $currency = FALSE, $current_currency = FALSE, $price_per_currencies = NULL ) {

		$element = apply_filters( 'wc_epo_get_element_for_display', $element );

		if ( is_null( $post_data ) && isset( $_POST ) ) {
			$post_data = $_POST;
		}
		if ( empty( $post_data ) && isset( $_REQUEST['tcajax'] ) ) {
			$post_data = $_REQUEST;
		}
		$_price_type = $this->get_element_price_type("", $element, $key, $per_product_pricing, $variation_id);
		$_price      = $this->get_element_price($price_default_value, $_price_type, $element, $key, $per_product_pricing, $variation_id);

		// This currently happens for multiple file uploads
		if ( is_array( $key ) ) {
			$key = 0;
		}
		$key = esc_attr( $key );
		if ( $per_product_pricing ) {
			
			if ( $cpf_product_price !== FALSE ) {
				$cpf_product_price = apply_filters( 'wc_epo_original_price_type_mode', $cpf_product_price, $post_data );
			}
			switch ( $_price_type ) {
				case 'percent_cart_total':
					$_price = ( floatval( $_price ) / 100 ) * floatval( WC()->cart->get_cart_contents_total() );
					if ( $currency ) {
						$_price = apply_filters( 'wc_epo_get_currency_price', $_price, $currency, $_price_type, FALSE, $current_currency, $price_per_currencies, $key, $attribute );
					}
					break;

				case 'percent':
					if ( $cpf_product_price !== FALSE ) {						
						if ( $currency ) {
							$cpf_product_price = apply_filters( 'wc_epo_convert_to_currency', $cpf_product_price, $current_currency, $currency );
						}
						$_price = ( floatval( $_price ) / 100 ) * floatval( $cpf_product_price );
					}
					break;
				case 'percentcurrenttotal':
					$_original_price = $_price;
					if ( $_price != '' && isset( $post_data[ $attribute . '_hidden' ] ) ) {
						$_price = floatval( $post_data[ $attribute . '_hidden' ] );

						if ( isset( $post_data['tm_epo_options_static_prices'] ) ) {
							$_price = ( floatval( $post_data['tm_epo_options_static_prices'] ) + floatval( $cpf_product_price ) ) * ( $_original_price / 100 );
							if ( isset( $post_data[ $attribute . '_quantity' ] ) && $post_data[ $attribute . '_quantity' ] > 0 ) {
								$_price = $_price * floatval( $post_data[ $attribute . '_quantity' ] );
							}
						}
						if ( $currency ) {
							$_price = apply_filters( 'wc_epo_get_currency_price', $_price, $currency, "", TRUE, $current_currency, $price_per_currencies, $key, $attribute );
						}

						if ( isset( $post_data[ $attribute . '_quantity' ] ) && $post_data[ $attribute . '_quantity' ] > 0 ) {
							$_price = $_price / floatval( $post_data[ $attribute . '_quantity' ] );
						}
					}
					break;
				case 'fixedcurrenttotal':
					$_original_price = $_price;
					if ( $_price != '' && isset( $post_data[ $attribute . '_hiddenfixed' ] ) ) {
						$_price = floatval( $post_data[ $attribute . '_hiddenfixed' ] );

						if ( isset( $post_data['tm_epo_options_static_prices'] ) ) {
							$_price = ( floatval( $post_data['tm_epo_options_static_prices'] ) + floatval( $_original_price ) );
							if ( isset( $post_data[ $attribute . '_quantity' ] ) && $post_data[ $attribute . '_quantity' ] > 0 ) {
								$_price = $_price * floatval( $post_data[ $attribute . '_quantity' ] );
							}
						}
						if ( $currency ) {
							$_price = apply_filters( 'wc_epo_get_currency_price', $_price, $currency, "", TRUE, $current_currency, $price_per_currencies, $key, $attribute );
						}

						if ( isset( $post_data[ $attribute . '_quantity' ] ) && $post_data[ $attribute . '_quantity' ] > 0 ) {
							$_price = $_price / floatval( $post_data[ $attribute . '_quantity' ] );
						}
					}
					break;
				case 'word':
					$_price = floatval( $_price * THEMECOMPLETE_EPO_HELPER()->count_words( $post_data[ $attribute ] ) );
					if ( $currency ) {
						$_price = apply_filters( 'wc_epo_get_currency_price', $_price, $currency, $_price_type, FALSE, $current_currency, $price_per_currencies, $key, $attribute );
					}
					break;
				case 'wordpercent':
					if ( $cpf_product_price !== FALSE ) {
						if ( $currency ) {
							$cpf_product_price = apply_filters( 'wc_epo_get_currency_price', $cpf_product_price, $currency, $_price_type, FALSE, $current_currency, $price_per_currencies, $key, $attribute );
						}
						$_price = floatval( THEMECOMPLETE_EPO_HELPER()->count_words( $post_data[ $attribute ] ) ) * ( ( $_price / 100 ) * floatval( $cpf_product_price ) );
					}
					break;
				case 'wordnon':
					$freechars   = absint( $element['freechars'] );
					$_textlength = floatval( THEMECOMPLETE_EPO_HELPER()->count_words( $post_data[ $attribute ] ) ) - $freechars;
					if ( $_textlength < 0 ) {
						$_textlength = 0;
					}
					$_price = floatval( $_price * $_textlength );
					if ( $currency ) {
						$_price = apply_filters( 'wc_epo_get_currency_price', $_price, $currency, $_price_type, FALSE, $current_currency, $price_per_currencies, $key, $attribute );
					}
					break;
				case 'wordpercentnon':
					if ( $cpf_product_price !== FALSE ) {
						$freechars = absint( $element['freechars'] );
						if ( $currency ) {
							$cpf_product_price = apply_filters( 'wc_epo_get_currency_price', $cpf_product_price, $currency, $_price_type, FALSE, $current_currency, $price_per_currencies, $key, $attribute );
						}
						$_textlength = floatval( THEMECOMPLETE_EPO_HELPER()->count_words( $post_data[ $attribute ] ) ) - $freechars;
						if ( $_textlength < 0 ) {
							$_textlength = 0;
						}
						$_price = floatval( $_textlength ) * ( ( $_price / 100 ) * floatval( $cpf_product_price ) );
					}
					break;

				case 'char':
					$_price = floatval( $_price * strlen( stripcslashes( utf8_decode( $post_data[ $attribute ] ) ) ) );
					if ( $currency ) {
						$_price = apply_filters( 'wc_epo_get_currency_price', $_price, $currency, $_price_type, FALSE, $current_currency, $price_per_currencies, $key, $attribute );
					}
					break;
				case 'charpercent':
					if ( $cpf_product_price !== FALSE ) {
						if ( $currency ) {
							$cpf_product_price = apply_filters( 'wc_epo_get_currency_price', $cpf_product_price, $currency, $_price_type, FALSE, $current_currency, $price_per_currencies, $key, $attribute );
						}
						$_price = floatval( strlen( stripcslashes( utf8_decode( $post_data[ $attribute ] ) ) ) ) * ( ( $_price / 100 ) * floatval( $cpf_product_price ) );
					}
					break;
				case 'charnofirst':
					$_textlength = floatval( strlen( stripcslashes( utf8_decode( $post_data[ $attribute ] ) ) ) ) - 1;
					if ( $_textlength < 0 ) {
						$_textlength = 0;
					}
					$_price = floatval( $_price * $_textlength );
					if ( $currency ) {
						$_price = apply_filters( 'wc_epo_get_currency_price', $_price, $currency, $_price_type, FALSE, $current_currency, $price_per_currencies, $key, $attribute );
					}
					break;

				case 'charnon':
					$freechars   = absint( $element['freechars'] );
					$_textlength = floatval( strlen( stripcslashes( utf8_decode( $post_data[ $attribute ] ) ) ) ) - $freechars;
					if ( $_textlength < 0 ) {
						$_textlength = 0;
					}
					$_price = floatval( $_price * $_textlength );
					if ( $currency ) {
						$_price = apply_filters( 'wc_epo_get_currency_price', $_price, $currency, $_price_type, FALSE, $current_currency, $price_per_currencies, $key, $attribute );
					}
					break;
				case 'charpercentnon':
					if ( $cpf_product_price !== FALSE ) {
						$freechars = absint( $element['freechars'] );
						if ( $currency ) {
							$cpf_product_price = apply_filters( 'wc_epo_get_currency_price', $cpf_product_price, $currency, $_price_type, FALSE, $current_currency, $price_per_currencies, $key, $attribute );
						}
						$_textlength = floatval( strlen( stripcslashes( utf8_decode( $post_data[ $attribute ] ) ) ) ) - $freechars;
						if ( $_textlength < 0 ) {
							$_textlength = 0;
						}
						$_price = floatval( $_textlength ) * ( ( $_price / 100 ) * floatval( $cpf_product_price ) );
					}
					break;
				case 'charnonnospaces':
					$freechars   = absint( $element['freechars'] );
					$_textlength = floatval( strlen( preg_replace( "/\s+/", "", stripcslashes( utf8_decode( $post_data[ $attribute ] ) ) ) ) ) - $freechars;
					if ( $_textlength < 0 ) {
						$_textlength = 0;
					}
					$_price = floatval( $_price * $_textlength );
					if ( $currency ) {
						$_price = apply_filters( 'wc_epo_get_currency_price', $_price, $currency, $_price_type, FALSE, $current_currency, $price_per_currencies, $key, $attribute );
					}
					break;
				case 'charpercentnonnospaces':
					if ( $cpf_product_price !== FALSE ) {
						$freechars = absint( $element['freechars'] );
						if ( $currency ) {
							$cpf_product_price = apply_filters( 'wc_epo_get_currency_price', $cpf_product_price, $currency, $_price_type, FALSE, $current_currency, $price_per_currencies, $key, $attribute );
						}
						$_textlength = floatval( strlen( preg_replace( "/\s+/", "", stripcslashes( utf8_decode( $post_data[ $attribute ] ) ) ) ) ) - $freechars;
						if ( $_textlength < 0 ) {
							$_textlength = 0;
						}
						$_price = floatval( $_textlength ) * ( ( $_price / 100 ) * floatval( $cpf_product_price ) );
					}
					break;

				case 'charnospaces':
					$_price = floatval( $_price * strlen( preg_replace( "/\s+/", "", stripcslashes( utf8_decode( $post_data[ $attribute ] ) ) ) ) );
					if ( $currency ) {
						$_price = apply_filters( 'wc_epo_get_currency_price', $_price, $currency, $_price_type, FALSE, $current_currency, $price_per_currencies, $key, $attribute );
					}
					break;
				case 'charpercentnofirst':
					if ( $cpf_product_price !== FALSE ) {
						if ( $currency ) {
							$cpf_product_price = apply_filters( 'wc_epo_get_currency_price', $cpf_product_price, $currency, $_price_type, FALSE, $current_currency, $price_per_currencies, $key, $attribute );
						}
						$_textlength = floatval( strlen( stripcslashes( utf8_decode( $post_data[ $attribute ] ) ) ) ) - 1;
						if ( $_textlength < 0 ) {
							$_textlength = 0;
						}
						$_price = floatval( $_textlength ) * ( ( $_price / 100 ) * floatval( $cpf_product_price ) );
					}
					break;
				case 'step':
					$_price = floatval( $_price * floatval( stripcslashes( $post_data[ $attribute ] ) ) );
					if ( $currency ) {
						$_price = apply_filters( 'wc_epo_get_currency_price', $_price, $currency, $_price_type, FALSE, $current_currency, $price_per_currencies, $key, $attribute );
					}
					break;
				case 'currentstep':
					$_price = floatval( stripcslashes( $post_data[ $attribute ] ) );
					if ( $currency ) {
						$_price = apply_filters( 'wc_epo_get_currency_price', $_price, $currency, $_price_type, FALSE, $current_currency, $price_per_currencies, $key, $attribute );
					}
					break;
				case 'intervalstep':
					if ( isset( $element["min"] ) ) {
						$_min   = floatval( $element["min"] );
						$_price = floatval( $_price * ( floatval( stripcslashes( $post_data[ $attribute ] ) ) - $_min ) );
						if ( $currency ) {
							$_price = apply_filters( 'wc_epo_get_currency_price', $_price, $currency, $_price_type, FALSE, $current_currency, $price_per_currencies, $key, $attribute );
						}
					}
					break;
				case 'row':
					$_price = floatval( $_price * ( substr_count( stripcslashes( utf8_decode( $post_data[ $attribute ] ) ), "\r\n" ) + 1 ) );
					if ( $currency ) {
						$_price = apply_filters( 'wc_epo_get_currency_price', $_price, $currency, $_price_type, FALSE, $current_currency, $price_per_currencies, $key, $attribute );
					}
					break;
				case 'math':
					$_price = $this->calculate_math_price( $_price, $post_data, $element, $key, $attribute, $per_product_pricing, $cpf_product_price, $variation_id, $price_default_value, $currency, $current_currency, $price_per_currencies );

					if ( $currency ) {
						$_price = apply_filters( 'wc_epo_get_currency_price', $_price, $currency, $_price_type, FALSE, $current_currency, $price_per_currencies, $key, $attribute );
					}
					break;
				default:
					// fixed price
					if ( $currency ) {
						$_price = apply_filters( 'wc_epo_get_currency_price', $_price, $currency, $_price_type, FALSE, $current_currency, $price_per_currencies, $key, $attribute );
					}
					break;
			}

			// quantity button
			if ( isset( $post_data[ $attribute . '_quantity' ] ) ) {
				$_price = floatval( $_price ) * floatval( $post_data[ $attribute . '_quantity' ] );
			}

			if ( $price_default_value === '' && $_price == 0 ) {
				$_price = '';
			}

		}

		$_price = apply_filters( 'wc_epo_calculate_price', $_price, $post_data, $element, $key, $attribute, $per_product_pricing, $cpf_product_price, $variation_id, $price_default_value, $currency, $current_currency, $price_per_currencies );

		return apply_filters( 'tm_wcml_raw_price_amount', $_price );

	}


	/**
	 * Conditional logic (checks if an element is visible)
	 *
	 * @since 1.0
	 */
	public function is_visible( $element = array(), $section = array(), $sections = array(), $form_prefix = "" ) {
		
		$id = uniqid();
		$this->current_element_to_check[ $id ] = array();

		return $this->is_visible_do( $id, $element, $section, $sections, $form_prefix );

	}

	/**
	 * Conditional logic (checks if an element is visible)
	 *
	 * @since 1.0
	 */
	private function is_visible_do( $id = "0", $element = array(), $section = array(), $sections = array(), $form_prefix = "" ) {

		$is_element = FALSE;
		$is_section = FALSE;

		$array_prefix = $form_prefix;
		if ( $form_prefix === "" ) {
			$array_prefix = "_";
		}

		$uniqid = isset( $element['uniqid'] ) ? $element['uniqid'] : FALSE;

		if ( ! $uniqid ) {
			$uniqid     = isset( $element['sections_uniqid'] ) ? $element['sections_uniqid'] : FALSE;
			$is_section = TRUE;
		} else {
			$is_element = TRUE;
		}

		if ( ! $uniqid ) {
			return FALSE;
		}

		if ( isset( $this->visible_elements[ $array_prefix ][ $uniqid ] ) ) {
			return $this->visible_elements[ $array_prefix ][ $uniqid ];
		}

		$logic = FALSE;

		if ( $is_element ) {

			// Element
			if ( ! $this->is_visible_do( $id, $section, array(), $sections, $form_prefix ) ) {
				$this->visible_elements[ $array_prefix ][ $uniqid ] = FALSE;

				return FALSE;
			}
			if ( ! isset( $element['logic'] ) || empty( $element['logic'] ) ) {
				$this->visible_elements[ $array_prefix ][ $uniqid ] = TRUE;

				return TRUE;
			}
			$logic = (array) json_decode( $element['clogic'] );
		} elseif ( $is_section ) {
			// Section
			if ( ! isset( $element['sections_logic'] ) || empty( $element['sections_logic'] ) ) {
				$this->visible_elements[ $array_prefix ][ $uniqid ] = TRUE;

				return TRUE;
			}
			$logic = (array) json_decode( $element['sections_clogic'] );
		} else {
			$this->visible_elements[ $array_prefix ][ $uniqid ] = TRUE;

			return TRUE;
		}

		if ( $logic ) {

			$rule_toggle = $logic['toggle'];
			$rule_what   = $logic['what'];
			$matches     = 0;
			$checked     = 0;
			$show        = TRUE;

			switch ( $rule_toggle ) {
				case "show":
					$show = FALSE;
					break;
				case "hide":
					$show = TRUE;
					break;
			}

			if (!isset($this->current_element_to_check[ $id ])){
				$this->current_element_to_check[ $id ] = array();
			}

			if ( in_array( $uniqid, $this->current_element_to_check[ $id ] ) ) {
				return TRUE;
			}

			$this->current_element_to_check[ $id ][] = $uniqid;

			foreach ( $logic['rules'] as $key => $rule ) {
				$matches ++;

				if ( $this->tm_check_field_match( $id, $rule, $sections, $form_prefix ) ) {
					$checked ++;
				}

			}

			$this->current_element_to_check[ $id ] = array();

			if ( $rule_what == "all" ) {
				if ( $checked > 0 && $checked == $matches ) {
					$show = ! $show;
				}
			} else {
				if ( $checked > 0 ) {
					$show = ! $show;
				}
			}
			$this->visible_elements[ $array_prefix ][ $uniqid ] = $show;

			return $show;

		}

		$this->visible_elements[ $array_prefix ][ $uniqid ] = FALSE;

		return FALSE;
	}

	/**
	 * Conditional logic (checks element conditions)
	 *
	 * @since 1.0
	 */
	public function tm_check_field_match( $id = "0", $rule = FALSE, $sections = FALSE, $form_prefix = "" ) {

		if ( empty( $rule ) || empty( $sections ) ) {
			return FALSE;
		}

		$array_prefix = $form_prefix;
		if ( $form_prefix === "" ) {
			$array_prefix = "_";
		}

		$section_id = $rule->section;
		$element_id = $rule->element;
		$operator   = $rule->operator;
		$value      = $rule->value;

		if ( (string) $section_id == (string) $element_id ) {
			return $this->tm_check_section_match( $element_id, $operator, $rule, $sections, $form_prefix );
		}
		if ( ! isset( $sections[ $section_id ] )
		     || ! isset( $sections[ $section_id ]['elements'] )
		     || ! isset( $sections[ $section_id ]['elements'][ $element_id ] )
		     || ! isset( $sections[ $section_id ]['elements'][ $element_id ]['type'] )
		) {
			return FALSE;
		}

		// variations logic
		if ( $sections[ $section_id ]['elements'][ $element_id ]['type'] == "variations" ) {
			return $this->tm_variation_check_match( $form_prefix, $value, $operator );
		}

		if ( ! isset( $sections[ $section_id ]['elements'][ $element_id ]['name_inc'] ) ) {
			return FALSE;
		}

		$element_uniqueid = $sections[ $section_id ]['elements'][ $element_id ]['uniqid'];

		if ( isset( $this->visible_elements[ $array_prefix ][ $element_uniqueid ] ) ) {
			if ( ! $this->visible_elements[ $array_prefix ][ $element_uniqueid ] ) {
				return FALSE;
			}
		} else {
			if ( in_array( $element_uniqueid, $this->current_element_to_check[ $id ] ) ) {
				// Getting here means that two elements depend on each other
				// This is a logical error when creating the conditional logic in the builder
				//return FALSE;
			} else if ( ! $this->is_visible_do( $id, $sections[ $section_id ]['elements'][ $element_id ], $sections[ $section_id ], $sections, $form_prefix ) ) {
				return FALSE;
			}
		}

		// Element array cannot hold the form_prefix for bto support, so we append manually
		$element_to_check = $sections[ $section_id ]['elements'][ $element_id ]['name_inc'];

		$element_type = $sections[ $section_id ]['elements'][ $element_id ]['type'];
		$posted_value = NULL;

		if ( $element_type === "product"){
			$element_type = "select";
		}

		switch ( $element_type ) {
			case "radio":
				$radio_checked_length = 0;
				$element_to_check     = array_unique( $element_to_check );

				$element_to_check = $element_to_check[0] . $form_prefix;

				if ( isset( $_POST[ $element_to_check ] ) ) {
					$radio_checked_length ++;
					$posted_value = $_POST[ $element_to_check ];
					$posted_value = stripslashes( $posted_value );
					$posted_value = THEMECOMPLETE_EPO_HELPER()->encodeURIComponent( $posted_value );
					$posted_value = THEMECOMPLETE_EPO_HELPER()->reverse_strrchr( $posted_value, "_" );
				}
				if ( $operator == 'is' || $operator == 'isnot' ) {
					if ( $radio_checked_length == 0 ) {
						return FALSE;
					}
				} elseif ( $operator == 'isnotempty' ) {
					return $radio_checked_length > 0;
				} elseif ( $operator == 'isempty' ) {
					return $radio_checked_length == 0;
				}
				break;
			case "checkbox":
				$checkbox_checked_length = 0;
				$ret                     = FALSE;
				$element_to_check        = array_unique( $element_to_check );
				foreach ( $element_to_check as $key => $name_value ) {
					$element_to_check[ $key ] = $name_value . $form_prefix;
					$posted_value             = NULL;
					if ( isset( $_POST[ $element_to_check[ $key ] ] ) ) {
						$checkbox_checked_length ++;
						$posted_value = $_POST[ $element_to_check[ $key ] ];
						$posted_value = stripslashes( $posted_value );
						$posted_value = THEMECOMPLETE_EPO_HELPER()->encodeURIComponent( $posted_value );
						$posted_value = THEMECOMPLETE_EPO_HELPER()->reverse_strrchr( $posted_value, "_" );

						if ( $this->tm_check_match( $posted_value, $value, $operator ) ) {
							$ret = TRUE;
						} else {
							if ( $operator == 'isnot' ) {
								$ret = FALSE;
								break;
							}
						}
					}

				}
				if ( $operator == 'is' || $operator == 'isnot' ) {
					if ( $checkbox_checked_length == 0 ) {
						return FALSE;
					}

					return $ret;
				} elseif ( $operator == 'isnotempty' ) {
					return $checkbox_checked_length > 0;
				} elseif ( $operator == 'isempty' ) {
					return $checkbox_checked_length == 0;
				}
				break;
			case "select":
			case "textarea":
			case "textfield":
			case "color":
			case "range":
				$element_to_check .= $form_prefix;
				if ( isset( $_POST[ $element_to_check ] ) ) {
					$posted_value = $_POST[ $element_to_check ];
					$posted_value = stripslashes( $posted_value );
					if ( $element_type == "select" ) {
						$posted_value = THEMECOMPLETE_EPO_HELPER()->encodeURIComponent( $posted_value );
						$posted_value = THEMECOMPLETE_EPO_HELPER()->reverse_strrchr( $posted_value, "_" );
					}
				}
				break;
		}

		return $this->tm_check_match( $posted_value, $value, $operator );

	}

	/**
	 * Conditional logic (checks section conditions)
	 *
	 * @since 1.0
	 */
	public function tm_check_section_match( $element_id, $operator, $rule = FALSE, $sections = FALSE, $form_prefix = "" ) {

		$array_prefix = $form_prefix;
		if ( $form_prefix === "" ) {
			$array_prefix = "_";
		}

		if ( isset( $this->visible_elements ) && isset( $this->visible_elements[ $array_prefix ] ) && isset( $this->visible_elements[ $array_prefix ][ $element_id ] ) ){

			if ($this->visible_elements[ $array_prefix ][ $element_id ] === FALSE){
				if ($operator === "isnotempty") {
	                return false;
	            } else if (operator === "isempty") {
	                return true;
	            }
	        }

		}

		$all_checked = TRUE;
		$section_id  = $element_id;
		if ( isset( $sections[ $section_id ] ) && isset( $sections[ $section_id ]['elements'] ) ) {
			foreach ( $sections[ $section_id ]['elements'] as $id => $element ) {
				if ( $this->is_visible_do( $id, $element, $sections[ $section_id ], $sections, $form_prefix ) ) {
					$element_to_check = $sections[ $section_id ]['elements'][ $id ]['name_inc'];
					$element_type     = $sections[ $section_id ]['elements'][ $id ]['type'];
					$posted_value     = NULL;
					if ( $element_type === "product"){
						$element_type = "select";
					}
					switch ( $element_type ) {
						case "radio":
							$radio_checked_length = 0;
							$element_to_check     = array_unique( $element_to_check );

							$element_to_check = $element_to_check[0] . $form_prefix;

							if ( isset( $_POST[ $element_to_check ] ) ) {
								$radio_checked_length ++;
								//$posted_value = $_POST[ $element_to_check ];
								//$posted_value = stripslashes( $posted_value );
								//$posted_value = THEMECOMPLETE_EPO_HELPER()->encodeURIComponent( $posted_value );
								//$posted_value = THEMECOMPLETE_EPO_HELPER()->reverse_strrchr( $posted_value, "_" );
							}
							if ( $operator == 'isnotempty' ) {
								$all_checked = $all_checked && $radio_checked_length > 0;
								if ($radio_checked_length > 0){
									$posted_value = $radio_checked_length;
								}
							} elseif ( $operator == 'isempty' ) {
								$all_checked = $all_checked && $radio_checked_length == 0;
							}
							break;
						case "checkbox":
							$checkbox_checked_length = 0;

							$element_to_check = array_unique( $element_to_check );
							foreach ( $element_to_check as $key => $name_value ) {
								$element_to_check[ $key ] = $name_value . $form_prefix;
								if ( isset( $_POST[ $element_to_check[ $key ] ] ) ) {
									$checkbox_checked_length ++;
									//$posted_value = $_POST[ $element_to_check[ $key ] ];
									//$posted_value = stripslashes( $posted_value );
									//$posted_value = THEMECOMPLETE_EPO_HELPER()->encodeURIComponent( $posted_value );
									//$posted_value = THEMECOMPLETE_EPO_HELPER()->reverse_strrchr( $posted_value, "_" );
								}

							}
							if ( $operator == 'isnotempty' ) {
								$all_checked = $all_checked && $checkbox_checked_length > 0;
								if ($checkbox_checked_length > 0){
									$posted_value = $checkbox_checked_length;
								}
							} elseif ( $operator == 'isempty' ) {
								$all_checked = $all_checked && $checkbox_checked_length == 0;
							}
							break;
						case "select":
						case "textarea":
						case "textfield":
						case "color":
							$element_to_check .= $form_prefix;
							if ( isset( $_POST[ $element_to_check ] ) ) {
								$posted_value = $_POST[ $element_to_check ];
								$posted_value = stripslashes( $posted_value );
								if ( $element_type == "select" ) {
									$posted_value = THEMECOMPLETE_EPO_HELPER()->encodeURIComponent( $posted_value );
									$posted_value = THEMECOMPLETE_EPO_HELPER()->reverse_strrchr( $posted_value, "_" );
								}
							}
							break;
					}
					$all_checked = $all_checked && $this->tm_check_match( $posted_value, '', $operator );
				}
			}
		}

		return $all_checked;

	}

	/**
	 * Conditional logic (checks variation conditions)
	 *
	 * @since 1.0
	 */
	public function tm_variation_check_match( $form_prefix, $value, $operator ) {

		$posted_value = $this->get_posted_variation_id( $form_prefix );

		return $this->tm_check_match( $posted_value, $value, $operator, TRUE );

	}

	/**
	 * Conditional logic (checks conditions)
	 *
	 * @since 1.0
	 */
	public function tm_check_match( $posted_value, $value, $operator, $include_zero = FALSE ) {

		$posted_value = rawurlencode( apply_filters( 'tm_translate', rawurldecode( $posted_value ) ) );
		$value        = rawurlencode( apply_filters( 'tm_translate', rawurldecode( $value ) ) );
		switch ( $operator ) {
			case "is":
				return ( $posted_value !== NULL && $value == $posted_value );
				break;
			case "isnot":
				return ( $posted_value !== NULL && $value != $posted_value );
				break;
			case "isempty":
				if ( $include_zero ) {
					return ( ! ( ( $posted_value !== NULL && $posted_value !== '' && $posted_value !== '0' && $posted_value !== 0 ) ) );
				}

				return ( ! ( ( $posted_value !== NULL && $posted_value !== '' ) ) );
				break;
			case "isnotempty":
				if ( $include_zero ) {
					return ( ( $posted_value !== NULL && $posted_value !== '' && $posted_value !== '0' && $posted_value !== 0 ) );
				}

				return ( ( $posted_value !== NULL && $posted_value !== '' ) );
				break;
			case "startswith" :
				return THEMECOMPLETE_EPO_HELPER()->str_startswith( $posted_value, $value );
				break;
			case "endswith" :
				return THEMECOMPLETE_EPO_HELPER()->str_endsswith( $posted_value, $value );
				break;
			case "greaterthan" :
				return floatval( $posted_value ) > floatval( $value );
				break;
			case "lessthan" :
				return floatval( $posted_value ) < floatval( $value );
				break;
		}

		return FALSE;

	}

	/**
	 * Upload file
	 *
	 * @param $file
	 *
	 * @return array|mixed
	 */
	public function upload_file( $file ) {
		if ( is_array( $file ) && ! empty( $file['tmp_name'] ) && isset( $this->upload_object[ $file['tmp_name'] ] ) ) {
			$this->upload_object[ $file['tmp_name'] ]['tc'] = TRUE;

			return $this->upload_object[ $file['tmp_name'] ];
		}
		if ( ! defined( 'ALLOW_UNFILTERED_UPLOADS' ) ) {
			define( 'ALLOW_UNFILTERED_UPLOADS', TRUE );
		}
		include_once( ABSPATH . 'wp-admin/includes/file.php' );
		include_once( ABSPATH . 'wp-admin/includes/media.php' );
		add_filter( 'upload_dir', array( $this, 'upload_dir_trick' ) );
		add_filter( 'upload_mimes', array( $this, 'upload_mimes_trick' ) );
		$upload = wp_handle_upload( $file, array( 'test_form' => FALSE, 'test_type' => FALSE ) );
		remove_filter( 'upload_dir', array( $this, 'upload_dir_trick' ) );
		remove_filter( 'upload_mimes', array( $this, 'upload_mimes_trick' ) );

		if ( is_array( $file ) && ! empty( $file['tmp_name'] ) ) {
			$this->upload_object[ $file['tmp_name'] ] = $upload;
		}

		return $upload;

	}

	/**
	 * Alter allowed file mime and type
	 *
	 * @param array $existing_mimes
	 *
	 * @return mixed|void
	 */
	public function upload_mimes_trick( $existing_mimes = array() ) {

		$mimes = array();

		$tm_epo_custom_file_types  = $this->tm_epo_custom_file_types;
		$tm_epo_allowed_file_types = $this->tm_epo_allowed_file_types;

		$tm_epo_custom_file_types = explode( ",", $tm_epo_custom_file_types );
		if ( ! is_array( $tm_epo_custom_file_types ) ) {
			$tm_epo_custom_file_types = array();
		}
		if ( ! is_array( $tm_epo_allowed_file_types ) ) {
			$tm_epo_allowed_file_types = array( "@" );
		}
		$tm_epo_allowed_file_types = array_merge( $tm_epo_allowed_file_types, $tm_epo_custom_file_types );
		$tm_epo_allowed_file_types = array_unique( $tm_epo_allowed_file_types );

		$wp_get_ext_types  = wp_get_ext_types();
		$wp_get_mime_types = wp_get_mime_types();

		foreach ( $tm_epo_allowed_file_types as $key => $value ) {
			if ( $value == "@" ) {
				$mimes = $existing_mimes;
			} else {
				$value = ltrim( $value, "@" );
				switch ( $value ) {
					case 'image':
					case 'audio':
					case 'video':
					case 'document':
					case 'spreadsheet':
					case 'interactive':
					case 'text':
					case 'archive':
					case 'code':
						if ( isset( $wp_get_ext_types[ $value ] ) && is_array( $wp_get_ext_types[ $value ] ) ) {
							foreach ( $wp_get_ext_types[ $value ] as $k => $extension ) {
								$type = FALSE;
								foreach ( $wp_get_mime_types as $exts => $_mime ) {
									if ( preg_match( '!^(' . $exts . ')$!i', $extension ) ) {
										$type = $_mime;
										break;
									}
								}
								if ( $type ) {
									$mimes[ $extension ] = $type;
								}
							}
						}
						break;

					default:
						$type = FALSE;
						foreach ( $wp_get_mime_types as $exts => $_mime ) {
							if ( preg_match( '!^(' . $exts . ')$!i', $value ) ) {
								$type = $_mime;
								break;
							}
						}
						if ( $type ) {
							$mimes[ $value ] = $type;
						} else {
							$mimes[ $value ] = "application/octet-stream";
						}
						break;
				}
			}
		}

		return apply_filters( 'wc_epo_upload_mimes', $mimes );

	}

	/**
	 * Alter upload directory
	 *
	 * @param $param
	 *
	 * @return mixed
	 */
	public function upload_dir_trick( $param ) {

		global $woocommerce;
		$this->unique_dir = apply_filters( 'wc_epo_upload_unique_dir', md5( $woocommerce->session->get_customer_id() ) );
		$subdir           = $this->upload_dir . $this->unique_dir;
		if ( empty( $param['subdir'] ) ) {
			$param['path']   = $param['path'] . $subdir;
			$param['url']    = $param['url'] . $subdir;
			$param['subdir'] = $subdir;
		} else {
			$param['path']   = str_replace( $param['subdir'], $subdir, $param['path'] );
			$param['url']    = str_replace( $param['subdir'], $subdir, $param['url'] );
			$param['subdir'] = str_replace( $param['subdir'], $subdir, $param['subdir'] );
		}

		return $param;

	}

	/**
	 * Apply custom filter
	 *
	 * @param string $value
	 * @param string $filter
	 *
	 * @return mixed|string|void
	 */
	private function tm_apply_filter( $value = "", $filter = "", $element = "", $element_uniqueid = "" ) {

		// Normalize posted strings
		if ( class_exists( 'Normalizer' ) ) {
			if ( is_array( $value ) ) {
				foreach ( $value as $value_key => $value_value ) {
					if ( is_array( $value_value ) ) {
						$value_value = THEMECOMPLETE_EPO_HELPER()->recursive_implode( $value_value, "" );
					}
					$value[ $value_key ] = Normalizer::normalize( $value_value );
				}
			} else {
				$value = Normalizer::normalize( $value );
			}
		}

		if ( ! empty( $filter ) ) {
			$value = apply_filters( $filter, $value, $element, $element_uniqueid );
		}

		return apply_filters( "wc_epo_setting", apply_filters( 'tm_translate', $value ), $element, $element_uniqueid );

	}

	/**
	 * Get builder element
	 *
	 * @param        $element
	 * @param        $builder
	 * @param        $current_builder
	 * @param bool   $index
	 * @param string $alt
	 * @param array  $wpml_section_fields
	 * @param string $identifier
	 * @param string $apply_filters
	 * @param string $element_uniqueid
	 *
	 * @return mixed|string|void
	 */
	public function get_builder_element( $element, $builder, $current_builder, $index = FALSE, $alt = "", $wpml_section_fields = array(), $identifier = "sections", $apply_filters = "", $element_uniqueid = "" ) {

		$use_wpml             = FALSE;
		$use_original_builder = FALSE;
		if ( THEMECOMPLETE_EPO_WPML()->is_active() && $index !== FALSE ) {
			if ( isset( $current_builder[ $identifier . "_uniqid" ] )
			     && isset( $builder[ $identifier . "_uniqid" ] )
			     && isset( $builder[ $identifier . "_uniqid" ][ $index ] )
			) {
				// Get index of element id in internal array
				$get_current_builder_uniqid_index = array_search( $builder[ $identifier . "_uniqid" ][ $index ], $current_builder[ $identifier . "_uniqid" ] );
				if ( $get_current_builder_uniqid_index !== NULL && $get_current_builder_uniqid_index !== FALSE ) {
					$index    = $get_current_builder_uniqid_index;
					$use_wpml = TRUE;
				} else {
					$use_original_builder = TRUE;
				}
			}
		}

		if ( isset( $builder[ $element ] ) ) {
			if ( ! $use_original_builder && $use_wpml && ( ( is_array( $wpml_section_fields ) && in_array( $element, $wpml_section_fields ) ) || $wpml_section_fields === TRUE ) ) {
				if ( isset( $current_builder[ $element ] ) ) {
					if ( $index !== FALSE ) {
						if ( isset( $current_builder[ $element ][ $index ] ) ) {
							return $this->tm_apply_filter( THEMECOMPLETE_EPO_HELPER()->build_array( $current_builder[ $element ][ $index ], $builder[ $element ][ $index ] ), $apply_filters, $element, $element_uniqueid );
						} else {
							return $this->tm_apply_filter( $alt, $apply_filters, $element, $element_uniqueid );
						}
					} else {
						return $this->tm_apply_filter( THEMECOMPLETE_EPO_HELPER()->build_array( $current_builder[ $element ], $builder[ $element ] ), $apply_filters, $element, $element_uniqueid );
					}
				}
			}
			if ( $index !== FALSE ) {
				if ( isset( $builder[ $element ][ $index ] ) ) {
					return $this->tm_apply_filter( $builder[ $element ][ $index ], $apply_filters, $element, $element_uniqueid );
				} else {
					return $this->tm_apply_filter( $alt, $apply_filters, $element, $element_uniqueid );
				}
			} else {
				return $this->tm_apply_filter( $builder[ $element ], $apply_filters, $element, $element_uniqueid );
			}
		} else {
			return $this->tm_apply_filter( $alt, $apply_filters, $element, $element_uniqueid );
		}

	}

	/**
	 * Gets a list of all the Extra Product Options (normal and global)
	 * for the specific $post_id.
	 *
	 * @since 1.0
	 */
	public function get_product_tm_epos( $post_id = 0, $form_prefix = "", $no_cache = FALSE, $no_disabled = FALSE ) {

		if ( empty( $post_id ) || apply_filters( 'wc_epo_disable', FALSE, $post_id ) || ! $this->check_enable() ) {
			return array();
		}

		$post_type = get_post_type( $post_id );

		if ( $post_type !== 'product' ) {
			return array();
		}

		$product      = wc_get_product( $post_id );
		$product_type = themecomplete_get_product_type( $product );

		// Yith gift cards are not supported
		if ( $product_type === 'gift-card' ) {
			return array();
		}

		// disable cache for associated products
		// as they may have discounts which will not
		// show up on the product page if the product 
		// is already in the cart
		if ( ! $this->is_inline_epo && isset( $this->cpf[ $post_id ]["{$no_disabled}"] ) ) {
			return $this->cpf[ $post_id ]["{$no_disabled}"];
		}

		if ( $this->tm_epo_global_enable_validation == "yes" ) {
			$this->current_option_features[] = 'validation';
		}

		if ( $this->tm_epo_no_lazy_load == "no" ) {
			$this->current_option_features[] = 'lazyload';
		}

		$this->set_tm_meta( $post_id );

		$in_cat = array();

		$tmglobalprices                   = array();
		$variations_for_conditional_logic = array();

		$terms = get_the_terms( $post_id, 'product_cat' );
		if ( $terms ) {
			foreach ( $terms as $term ) {
				$in_cat[] = $term->term_id;
			}
		}

		// Get all categories (no matter the language)
		$_all_categories = THEMECOMPLETE_EPO_WPML()->get_terms( NULL, 'product_cat', array( 'fields' => "ids", 'hide_empty' => FALSE ) );

		if ( ! $_all_categories ) {
			$_all_categories = array();
		}

		// Get Normal (Local) options 
		$args = array(
			'post_type'   => THEMECOMPLETE_EPO_LOCAL_POST_TYPE,
			'post_status' => array( 'publish' ), // get only enabled extra options
			'numberposts' => - 1,
			'orderby'     => 'menu_order',
			'order'       => 'asc', 'suppress_filters' => TRUE,
			'post_parent' => floatval( THEMECOMPLETE_EPO_WPML()->get_original_id( $post_id ) ),
		);
		THEMECOMPLETE_EPO_WPML()->remove_sql_filter();
		$tmlocalprices = get_posts( $args );
		THEMECOMPLETE_EPO_WPML()->restore_sql_filter();

		$tm_meta_cpf_global_forms = ( isset( $this->tm_meta_cpf['global_forms'] ) && is_array( $this->tm_meta_cpf['global_forms'] ) ) ? $this->tm_meta_cpf['global_forms'] : array();
		foreach ( $tm_meta_cpf_global_forms as $key => $value ) {
			$tm_meta_cpf_global_forms[ $key ] = absint( $value );
		}
		$tm_meta_cpf_global_forms_added = array();

		if ( ! $this->tm_meta_cpf['exclude'] ) {

			/**
			 * Procedure to get global forms
			 * that apply to all products or
			 * specific product categories.
			 */
			$meta_array = THEMECOMPLETE_EPO_HELPER()->build_meta_query( 'OR', 'tm_meta_disable_categories', 1, '!=', 'NOT EXISTS' );

			$meta_array2 = THEMECOMPLETE_EPO_HELPER()->build_meta_query( 'OR', 'tm_meta_product_exclude_ids', '"' . $post_id . '";', 'NOT LIKE', 'NOT EXISTS' );

			$meta_query_args = array(
				'relation' => 'AND', // Optional, defaults to "AND"
				$meta_array,
				$meta_array2
			);

			$args = array(
				'post_type'   => THEMECOMPLETE_EPO_GLOBAL_POST_TYPE,
				'post_status' => array( 'publish' ), // get only enabled global extra options
				'numberposts' => - 1,
				'orderby'     => 'date',
				'order'       => 'asc',
				'meta_query'  => $meta_query_args,
			);

			$args['tax_query'] = array(
				'relation' => 'OR',
				// Get Global options that belong to the product categories 
				array(
					'taxonomy'         => 'product_cat',
					'field'            => 'term_id',
					'terms'            => $in_cat,
					'operator'         => 'IN',
					'include_children' => FALSE,
				),
				// Get Global options that have no catergory set (they apply to all products) 
				array(
					'taxonomy'         => 'product_cat',
					'field'            => 'term_id',
					'terms'            => $_all_categories,
					'operator'         => 'NOT IN',
					'include_children' => FALSE,
				),
				array(
					'taxonomy'         => 'product_cat',
					'field'            => 'term_id',
					'operator'         => 'NOT EXISTS',
					'include_children' => FALSE,
				),
			);

			THEMECOMPLETE_EPO_WPML()->remove_sql_filter();
			THEMECOMPLETE_EPO_WPML()->remove_term_filters();
			$tmp_tmglobalprices = get_posts( $args );
			THEMECOMPLETE_EPO_WPML()->restore_term_filters();
			THEMECOMPLETE_EPO_WPML()->restore_sql_filter();

			if ( $tmp_tmglobalprices ) {
				$wpml_tmp_tmglobalprices       = array();
				$wpml_tmp_tmglobalprices_added = array();
				foreach ( $tmp_tmglobalprices as $price ) {

					if ( THEMECOMPLETE_EPO_WPML()->is_active() ) {
						$price_meta_lang                 = get_post_meta( $price->ID, THEMECOMPLETE_EPO_WPML_LANG_META, TRUE );
						$original_product_id             = floatval( THEMECOMPLETE_EPO_WPML()->get_original_id( $price->ID, $price->post_type ) );
						$double_check_disable_categories = get_post_meta( $original_product_id, "tm_meta_disable_categories", TRUE );
						if ( ! $double_check_disable_categories ) {

							if ( $price_meta_lang == THEMECOMPLETE_EPO_WPML()->get_lang()
							     || ( $price_meta_lang == '' && THEMECOMPLETE_EPO_WPML()->get_lang() == THEMECOMPLETE_EPO_WPML()->get_default_lang() )
							) {
								$tmglobalprices[]                 = $price;
								$tm_meta_cpf_global_forms_added[] = $price->ID;
								if ( $price_meta_lang != THEMECOMPLETE_EPO_WPML()->get_default_lang() && $price_meta_lang != '' ) {
									$wpml_tmp_tmglobalprices_added[ $original_product_id ] = $price;
								}
							} else {
								if ( $price_meta_lang == THEMECOMPLETE_EPO_WPML()->get_default_lang() || $price_meta_lang == '' ) {
									$wpml_tmp_tmglobalprices[ $original_product_id ] = $price;
								}
							}
						}
					} else {
						$tmglobalprices[]                 = $price;
						$tm_meta_cpf_global_forms_added[] = $price->ID;
					}

				}
				// Replace missing translation with original
				if ( THEMECOMPLETE_EPO_WPML()->is_active() ) {
					$wpml_gp_keys = array_keys( $wpml_tmp_tmglobalprices );
					foreach ( $wpml_gp_keys as $key => $value ) {
						if ( ! isset( $wpml_tmp_tmglobalprices_added[ $value ] ) ) {
							$tmglobalprices[]                 = $wpml_tmp_tmglobalprices[ $value ];
							$tm_meta_cpf_global_forms_added[] = $price->ID;
						}
					}
				}

			}

			/**
			 * Get Global options that apply to the product
			 */
			$args = array(
				'post_type'   => THEMECOMPLETE_EPO_GLOBAL_POST_TYPE,
				'post_status' => array( 'publish' ), // get only enabled global extra options
				'numberposts' => - 1,
				'orderby'     => 'date',
				'order'       => 'asc',
				'meta_query'  => array(
					array(
						'key'     => 'tm_meta_product_ids',
						'value'   => '"' . $post_id . '";',
						'compare' => 'LIKE',

					),
				),
			);

			$available_variations = $product->get_children();
			$glue                 = array();

			foreach ( $available_variations as $variation_id ) {
				$variations_for_conditional_logic[] = $variation_id;
				$glue[]                             = array(
					'key'     => 'tm_meta_product_ids',
					'value'   => '"' . $variation_id . '";',
					'compare' => 'LIKE',
				);
			}
			if ( $glue ) {
				$args['meta_query']['relation'] = 'OR';
				$args['meta_query']             = array_merge( $args['meta_query'], $glue );
			}

			$tmglobalprices_products = get_posts( $args );

			if ( $tmglobalprices_products ) {

				$global_id_array = array();
				if ( isset( $tmglobalprices ) ) {
					foreach ( $tmglobalprices as $price ) {
						$global_id_array[] = $price->ID;
					}
				} else {
					$tmglobalprices = array();
				}

				$wpml_tmglobalprices_products       = array();
				$wpml_tmglobalprices_products_added = array();
				foreach ( $tmglobalprices_products as $price ) {

					if ( THEMECOMPLETE_EPO_WPML()->is_active() ) {
						$price_meta_lang     = get_post_meta( $price->ID, THEMECOMPLETE_EPO_WPML_LANG_META, TRUE );
						$original_product_id = floatval( THEMECOMPLETE_EPO_WPML()->get_original_id( $price->ID, $price->post_type ) );

						if ( $price_meta_lang == THEMECOMPLETE_EPO_WPML()->get_lang()
						     || ( $price_meta_lang == '' && THEMECOMPLETE_EPO_WPML()->get_lang() == THEMECOMPLETE_EPO_WPML()->get_default_lang() )
						) {
							if ( ! in_array( $price->ID, $global_id_array ) ) {
								$global_id_array[]                = $price->ID;
								$tmglobalprices[]                 = $price;
								$tm_meta_cpf_global_forms_added[] = $price->ID;
								if ( $price_meta_lang != THEMECOMPLETE_EPO_WPML()->get_default_lang() && $price_meta_lang != '' ) {
									$wpml_tmglobalprices_products_added[ $original_product_id ] = $price;
								}
							}
						} else {
							if ( $price_meta_lang == THEMECOMPLETE_EPO_WPML()->get_default_lang() || $price_meta_lang == '' ) {
								$wpml_tmglobalprices_products[ $original_product_id ] = $price;
							}
						}

					} else {
						if ( ! in_array( $price->ID, $global_id_array ) ) {
							$global_id_array[]                = $price->ID;
							$tmglobalprices[]                 = $price;
							$tm_meta_cpf_global_forms_added[] = $price->ID;
						}
					}

				}
				// Replace missing translation with original
				if ( THEMECOMPLETE_EPO_WPML()->is_active() ) {
					$wpml_gp_keys = array_keys( $wpml_tmglobalprices_products );
					foreach ( $wpml_gp_keys as $key => $value ) {
						if ( ! isset( $wpml_tmglobalprices_products_added[ $value ] ) ) {
							if ( ! in_array( $price->ID, $global_id_array ) ) {
								$global_id_array[]                = $price->ID;
								$tmglobalprices[]                 = $wpml_tmglobalprices_products[ $value ];
								$tm_meta_cpf_global_forms_added[] = $price->ID;
							}
						}
					}
				}

			}

			/**
			 * Get Global options that apply to the product
			 * only for translated products
			 */
			$original_product_id = floatval( THEMECOMPLETE_EPO_WPML()->get_original_id( $post_id ) );
			if ( $original_product_id !== floatval( $post_id ) ) {
				// Get Global options that apply to the product
				$args = array(
					'post_type'   => THEMECOMPLETE_EPO_GLOBAL_POST_TYPE,
					'post_status' => array( 'publish' ), // get only enabled global extra options
					'numberposts' => - 1,
					'orderby'     => 'date',
					'order'       => 'asc',
					'meta_query'  => array(
						array(
							'key'     => 'tm_meta_product_ids',
							'value'   => '"' . $original_product_id . '";',
							'compare' => 'LIKE',

						),
					),

				);

				THEMECOMPLETE_EPO_WPML()->remove_sql_filter();
				THEMECOMPLETE_EPO_WPML()->remove_term_filters();
				$tmglobalprices_products = get_posts( $args );
				THEMECOMPLETE_EPO_WPML()->restore_term_filters();
				THEMECOMPLETE_EPO_WPML()->restore_sql_filter();

				if ( $tmglobalprices_products ) {

					$global_id_array = array();
					if ( isset( $tmglobalprices ) ) {
						foreach ( $tmglobalprices as $price ) {
							$global_id_array[] = $price->ID;
						}
					} else {
						$tmglobalprices = array();
					}

					$wpml_tmglobalprices_products       = array();
					$wpml_tmglobalprices_products_added = array();
					foreach ( $tmglobalprices_products as $price ) {

						if ( THEMECOMPLETE_EPO_WPML()->is_active() ) {
							$price_meta_lang     = get_post_meta( $price->ID, THEMECOMPLETE_EPO_WPML_LANG_META, TRUE );
							$original_product_id = floatval( THEMECOMPLETE_EPO_WPML()->get_original_id( $price->ID, $price->post_type ) );

							if ( $price_meta_lang == THEMECOMPLETE_EPO_WPML()->get_lang()
							     || ( $price_meta_lang == '' && THEMECOMPLETE_EPO_WPML()->get_lang() == THEMECOMPLETE_EPO_WPML()->get_default_lang() )
							) {
								if ( ! in_array( $price->ID, $global_id_array ) ) {
									$global_id_array[]                = $price->ID;
									$tmglobalprices[]                 = $price;
									$tm_meta_cpf_global_forms_added[] = $price->ID;
									if ( $price_meta_lang != THEMECOMPLETE_EPO_WPML()->get_default_lang() && $price_meta_lang != '' ) {
										$wpml_tmglobalprices_products_added[ $original_product_id ] = $price;
									}
								}
							} else {
								if ( $price_meta_lang == THEMECOMPLETE_EPO_WPML()->get_default_lang() || $price_meta_lang == '' ) {
									$wpml_tmglobalprices_products[ $original_product_id ] = $price;
								}
							}

						} else {
							if ( ! in_array( $price->ID, $global_id_array ) ) {
								$global_id_array[]                = $price->ID;
								$tmglobalprices[]                 = $price;
								$tm_meta_cpf_global_forms_added[] = $price->ID;
							}
						}

					}
					// Replace missing translation with original
					if ( THEMECOMPLETE_EPO_WPML()->is_active() ) {
						$wpml_gp_keys = array_keys( $wpml_tmglobalprices_products );
						foreach ( $wpml_gp_keys as $key => $value ) {
							if ( ! isset( $wpml_tmglobalprices_products_added[ $value ] ) ) {
								if ( ! in_array( $price->ID, $global_id_array ) ) {

									$query = new WP_Query(
										array(
											'post_type'   => THEMECOMPLETE_EPO_GLOBAL_POST_TYPE,
											'post_status' => array( 'publish' ),
											'numberposts' => - 1,
											'orderby'     => 'date',
											'order'       => 'asc',
											'meta_query'  => array(
												'relation' => 'AND',
												array(
													'key'     => THEMECOMPLETE_EPO_WPML_LANG_META,
													'value'   => THEMECOMPLETE_EPO_WPML()->get_default_lang(),
													'compare' => '!=',
												),
												array(
													'key'     => THEMECOMPLETE_EPO_WPML_PARENT_POSTID,
													'value'   => $price->ID,
													'compare' => '=',
												),
											),
										) );
									if ( ! empty( $query->posts ) ) {
										if ( ! in_array( $query->post->ID, $global_id_array ) ) {
											$global_id_array[]                = $query->post->ID;
											$tmglobalprices[]                 = $query->post;
											$tm_meta_cpf_global_forms_added[] = $query->post->ID;
										}
									} else {
										$global_id_array[]                = $price->ID;
										$tmglobalprices[]                 = $wpml_tmglobalprices_products[ $value ];
										$tm_meta_cpf_global_forms_added[] = $price->ID;
									}

								}
							}
						}
					}

				}

			}

			/**
			 * Support for conditional logic based on variations
			 */
			$original_product_id = floatval( THEMECOMPLETE_EPO_WPML()->get_original_id( $post_id ) );
			if ( $original_product_id !== floatval( $post_id ) ) {
				// Get Global options that apply to the product
				$args = array(
					'post_type'   => THEMECOMPLETE_EPO_GLOBAL_POST_TYPE,
					'post_status' => array( 'publish' ), // get only enabled global extra options
					'numberposts' => - 1,
					'orderby'     => 'date',
					'order'       => 'asc',
					'meta_query'  => array(
						array(
							'key'     => 'tm_meta_product_ids',
							'value'   => '"' . $original_product_id . '";',
							'compare' => 'LIKE',

						),
					),
				);

				$product              = wc_get_product( $original_product_id );
				$available_variations = $product->get_children();
				$glue                 = array();

				foreach ( $available_variations as $variation_id ) {
					$variations_for_conditional_logic[] = $variation_id;
					$glue[]                             = array(
						'key'     => 'tm_meta_product_ids',
						'value'   => '"' . floatval( THEMECOMPLETE_EPO_WPML()->get_original_id( $variation_id ) ) . '";',
						'compare' => 'LIKE',
					);
				}

				if ( $glue ) {
					$args['meta_query']['relation'] = 'OR';
					$args['meta_query']             = array_merge( $args['meta_query'], $glue );

					$tmglobalprices_products = get_posts( $args );

					// Merge Global options 
					if ( $tmglobalprices_products ) {
						$global_id_array = array();
						if ( isset( $tmglobalprices ) ) {
							foreach ( $tmglobalprices as $price ) {
								$global_id_array[] = $price->ID;
							}
						} else {
							$tmglobalprices = array();
						}
						foreach ( $tmglobalprices_products as $price ) {
							if ( ! in_array( $price->ID, $global_id_array ) ) {
								$tmglobalprices[]                 = $price;
								$tm_meta_cpf_global_forms_added[] = $price->ID;
							}
						}
					}

				}

			}

		}

		$tm_meta_cpf_global_forms_added = array_unique( $tm_meta_cpf_global_forms_added );

		$tm_meta_cpf_global_forms = apply_filters( 'wc_epo_additional_global_forms', $tm_meta_cpf_global_forms, $post_id, $form_prefix, $this );
		$tm_meta_cpf_global_forms = array_unique( $tm_meta_cpf_global_forms );

		foreach ( $tm_meta_cpf_global_forms as $key => $value ) {
			if ( ! in_array( $value, $tm_meta_cpf_global_forms_added ) ) {
				if ( THEMECOMPLETE_EPO_WPML()->is_active() ) {

					$tm_meta_lang = get_post_meta( $value, THEMECOMPLETE_EPO_WPML_LANG_META, TRUE );
					if ( empty( $tm_meta_lang ) ) {
						$tm_meta_lang = THEMECOMPLETE_EPO_WPML()->get_default_lang();
					}
					$meta_query   = THEMECOMPLETE_EPO_HELPER()->build_meta_query( 'AND', THEMECOMPLETE_EPO_WPML_LANG_META, THEMECOMPLETE_EPO_WPML()->get_lang(), '=', 'EXISTS' );
					$meta_query[] = array(
						'key'     => THEMECOMPLETE_EPO_WPML_PARENT_POSTID,
						'value'   => $value,
						'compare' => '=',
					);

					$query = new WP_Query(
						array(
							'post_type'   => THEMECOMPLETE_EPO_GLOBAL_POST_TYPE,
							'post_status' => array( 'publish' ),
							'numberposts' => - 1,
							'orderby'     => 'date',
							'order'       => 'asc',
							'meta_query'  => $meta_query,
						) );

					if ( ! empty( $query->posts ) ) {
						if ( $query->post_count > 1 ) {

							foreach ( $query->posts as $current_post ) {
								$metalang = get_post_meta( $current_post->ID, THEMECOMPLETE_EPO_WPML_LANG_META, TRUE );

								if ( $metalang == THEMECOMPLETE_EPO_WPML()->get_lang() ) {
									$tmglobalprices[] = get_post( $current_post->ID );
									break;
								}
							}
						} else {
							$tmglobalprices[] = get_post( $query->post->ID );
						}
					} elseif ( empty( $query->posts ) ) {
						$tmglobalprices[] = get_post( $value );
					}

				} else {
					$ispostactive = get_post( $value );
					if ( $ispostactive && $ispostactive->post_status == 'publish' ) {
						$tmglobalprices[] = get_post( $value );
					}
				}
			}
		}

		// Add current product to Global options array (has to be last to not conflict)
		$tmglobalprices[] = get_post( $post_id );

		// End of DB init

		$epos                        = $this->generate_global_epos( $tmglobalprices, $post_id, $this->tm_original_builder_elements, $variations_for_conditional_logic, $no_cache, $no_disabled );
		$global_epos                 = $epos['global'];
		$raw_epos                    = $epos['raw_epos'];
		$epos_prices                 = $epos['price'];
		$variation_element_id        = $epos['variation_element_id'];
		$variation_section_id        = $epos['variation_section_id'];
		$variations_disabled         = $epos['variations_disabled'];
		$global_product_epos_uniqids = $epos['product_epos_uniqids'];
		$product_epos_choices        = $epos['product_epos_choices'];

		if ( is_array( $global_epos ) ) {
			ksort( $global_epos );
		}

		$product_epos = $this->generate_local_epos( $tmlocalprices, $post_id );

		$global_epos = $this->tm_fill_element_names( $post_id, $global_epos, $product_epos, $form_prefix, "epo" );

		$epos = array(
			'global'               => $global_epos,
			'raw_epos'             => $raw_epos,
			'global_ids'           => $tmglobalprices,
			'local'                => $product_epos['product_epos'],
			'price'                => $epos_prices,
			'variation_element_id' => $variation_element_id,
			'variation_section_id' => $variation_section_id,
			'variations_disabled'  => $variations_disabled,
			'epos_uniqids'         => array_merge( $product_epos['product_epos_uniqids'], $global_product_epos_uniqids ),
			'product_epos_choices' => $product_epos_choices,
		);

		$this->cpf[ $post_id ]["{$no_disabled}"] = $epos;

		return $epos;

	}

	/**
	 * Generate normal (local) option array
	 *
	 * @param $tmlocalprices
	 * @param $post_id
	 *
	 * @return array
	 */
	public function generate_local_epos( $tmlocalprices, $post_id ) {
		$product_epos         = array();
		$product_epos_uniqids = array();
		if ( $tmlocalprices ) {
			THEMECOMPLETE_EPO_WPML()->remove_sql_filter();
			$attributes      = themecomplete_get_attributes( floatval( THEMECOMPLETE_EPO_WPML()->get_original_id( $post_id ) ) );
			$wpml_attributes = themecomplete_get_attributes( $post_id );

			foreach ( $tmlocalprices as $price ) {

				$tmcp_id = absint( $price->ID );

				$n = get_post_meta( $tmcp_id, 'tmcp_attribute', TRUE );
				if ( ! isset( $attributes[ $n ] ) ) {
					continue;
				}
				$att = $attributes[ $n ];
				if ( $att['is_variation'] || sanitize_title( $att['name'] ) != $n ) {
					continue;
				}

				$tmcp_required                           = get_post_meta( $tmcp_id, 'tmcp_required', TRUE );
				$tmcp_hide_price                         = get_post_meta( $tmcp_id, 'tmcp_hide_price', TRUE );
				$tmcp_limit                              = get_post_meta( $tmcp_id, 'tmcp_limit', TRUE );
				$product_epos[ $tmcp_id ]['is_form']     = 0;
				$product_epos[ $tmcp_id ]['required']    = empty( $tmcp_required ) ? 0 : 1;
				$product_epos[ $tmcp_id ]['hide_price']  = empty( $tmcp_hide_price ) ? 0 : 1;
				$product_epos[ $tmcp_id ]['limit']       = empty( $tmcp_limit ) ? "" : $tmcp_limit;
				$product_epos[ $tmcp_id ]['name']        = get_post_meta( $tmcp_id, 'tmcp_attribute', TRUE );
				$product_epos[ $tmcp_id ]['is_taxonomy'] = get_post_meta( $tmcp_id, 'tmcp_attribute_is_taxonomy', TRUE );
				$product_epos[ $tmcp_id ]['label']       = wc_attribute_label( $product_epos[ $tmcp_id ]['name'] );
				$product_epos[ $tmcp_id ]['type']        = get_post_meta( $tmcp_id, 'tmcp_type', TRUE );
				$product_epos_uniqids[]                  = $product_epos[ $tmcp_id ]['name'];

				// Retrieve attributes
				$product_epos[ $tmcp_id ]['attributes']      = array();
				$product_epos[ $tmcp_id ]['attributes_wpml'] = array();
				if ( $product_epos[ $tmcp_id ]['is_taxonomy'] ) {
					if ( ! ( $attributes[ $product_epos[ $tmcp_id ]['name'] ]['is_variation'] ) ) {
						$orderby = themecomplete_attribute_orderby( $attributes[ $product_epos[ $tmcp_id ]['name'] ]['name'] );
						$args    = 'orderby=name&hide_empty=0';
						switch ( $orderby ) {
							case 'name' :
								$args = array( 'orderby' => 'name', 'hide_empty' => FALSE, 'menu_order' => FALSE );
								break;
							case 'id' :
								$args = array( 'orderby' => 'id', 'order' => 'ASC', 'menu_order' => FALSE, 'hide_empty' => FALSE );
								break;
							case 'menu_order' :
								$args = array( 'menu_order' => 'ASC', 'hide_empty' => FALSE );
								break;
						}

						$all_terms = THEMECOMPLETE_EPO_WPML()->get_terms( NULL, $attributes[ $product_epos[ $tmcp_id ]['name'] ]['name'], $args );

						if ( $all_terms ) {
							foreach ( $all_terms as $term ) {
								$has_term     = has_term( (int) $term->term_id, $attributes[ $product_epos[ $tmcp_id ]['name'] ]['name'], floatval( THEMECOMPLETE_EPO_WPML()->get_original_id( $post_id ) ) ) ? 1 : 0;
								$wpml_term_id = THEMECOMPLETE_EPO_WPML()->is_active() ? icl_object_id( $term->term_id, $attributes[ $product_epos[ $tmcp_id ]['name'] ]['name'], FALSE ) : FALSE;
								if ( $has_term ) {
									$product_epos[ $tmcp_id ]['attributes'][ esc_attr( $term->slug ) ] = apply_filters( 'woocommerce_tm_epo_option_name', esc_html( $term->name ), NULL, NULL );
									if ( $wpml_term_id ) {
										$wpml_term                                                              = get_term( $wpml_term_id, $attributes[ $product_epos[ $tmcp_id ]['name'] ]['name'] );
										$product_epos[ $tmcp_id ]['attributes_wpml'][ esc_attr( $term->slug ) ] = apply_filters( 'woocommerce_tm_epo_option_name', esc_html( $wpml_term->name ), NULL, NULL );
									} else {
										$product_epos[ $tmcp_id ]['attributes_wpml'][ esc_attr( $term->slug ) ] = $product_epos[ $tmcp_id ]['attributes'][ esc_attr( $term->slug ) ];
									}
								}
							}
						}

					}
				} else {
					if ( isset( $attributes[ $product_epos[ $tmcp_id ]['name'] ] ) ) {
						$options      = array_map( 'trim', explode( WC_DELIMITER, $attributes[ $product_epos[ $tmcp_id ]['name'] ]['value'] ) );
						$wpml_options = isset( $wpml_attributes[ $product_epos[ $tmcp_id ]['name'] ]['value'] ) ? array_map( 'trim', explode( WC_DELIMITER, $wpml_attributes[ $product_epos[ $tmcp_id ]['name'] ]['value'] ) ) : $options;
						foreach ( $options as $k => $option ) {
							$product_epos[ $tmcp_id ]['attributes'][ esc_attr( sanitize_title( $option ) ) ]      = esc_html( apply_filters( 'woocommerce_tm_epo_option_name', $option, NULL, NULL ) );
							$product_epos[ $tmcp_id ]['attributes_wpml'][ esc_attr( sanitize_title( $option ) ) ] = esc_html( apply_filters( 'woocommerce_tm_epo_option_name', isset( $wpml_options[ $k ] ) ? $wpml_options[ $k ] : $option, NULL, NULL ) );
						}
					}
				}

				// Retrieve price rules
				$_regular_price                    = get_post_meta( $tmcp_id, '_regular_price', TRUE );
				$_regular_price_type               = get_post_meta( $tmcp_id, '_regular_price_type', TRUE );
				$product_epos[ $tmcp_id ]['rules'] = $_regular_price;

				$_regular_price_filtered                    = THEMECOMPLETE_EPO_HELPER()->array_map_deep( $_regular_price, $_regular_price_type, array( $this, 'tm_epo_price_filtered' ) );
				$product_epos[ $tmcp_id ]['rules_filtered'] = $_regular_price_filtered;

				$product_epos[ $tmcp_id ]['rules_type'] = $_regular_price_type;
				if ( ! is_array( $_regular_price ) ) {
					$_regular_price = array();
				}
				if ( ! is_array( $_regular_price_type ) ) {
					$_regular_price_type = array();
				}
				foreach ( $_regular_price as $key => $value ) {
					foreach ( $value as $k => $v ) {
						$_regular_price[ $key ][ $k ] = wc_format_localized_price( $v );
					}
				}
				foreach ( $_regular_price_type as $key => $value ) {
					foreach ( $value as $k => $v ) {
						$_regular_price_type[ $key ][ $k ] = $v;
					}
				}
				$product_epos[ $tmcp_id ]['price_rules']          = $_regular_price;
				$product_epos[ $tmcp_id ]['price_rules_filtered'] = $_regular_price_filtered;
				$product_epos[ $tmcp_id ]['price_rules_type']     = $_regular_price_type;
			}
			THEMECOMPLETE_EPO_WPML()->restore_sql_filter();
		}

		return array( 'product_epos' => $product_epos, 'product_epos_uniqids' => $product_epos_uniqids );
	}

	/**
	 * Generate global (builder) option array
	 *
	 * @param $tmglobalprices
	 * @param $post_id
	 * @param $tm_original_builder_elements
	 *
	 * @return array
	 */
	public function generate_global_epos( $tmglobalprices, $post_id, $tm_original_builder_elements, $variations_for_conditional_logic = array(), $no_cache = FALSE, $no_disabled = FALSE ) {
		$global_epos              = array();
		$product_epos_uniqids     = array();
		$product_epos_choices     = array();
		$epos_prices              = array();
		$extra_section_logic      = array();
		$extra_section_hide_logic = array();
		$raw_epos                 = array();

		$variation_element_id = FALSE;
		$variation_section_id = FALSE;
		if ( $tmglobalprices ) {
			$wpml_section_fields = THEMECOMPLETE_EPO_BUILDER()->wpml_section_fields;
			$wpml_element_fields = THEMECOMPLETE_EPO_BUILDER()->wpml_element_fields;			

			foreach ( $tmglobalprices as $price ) {
				if ( ! is_object( $price ) ) {
					continue;
				}

				$original_product_id = $price->ID;
				if ( THEMECOMPLETE_EPO_WPML()->is_active() ) {
					$wpml_is_original_product = THEMECOMPLETE_EPO_WPML()->is_original_product( $price->ID, $price->post_type );
					if ( ! $wpml_is_original_product ) {
						$original_product_id = floatval( THEMECOMPLETE_EPO_WPML()->get_original_id( $price->ID, $price->post_type ) );
					}
				}

				$tmcp_id                     = absint( $original_product_id );
				$tmcp_meta                   = themecomplete_get_post_meta( $tmcp_id, 'tm_meta', TRUE );
				$enabled_roles               = themecomplete_get_post_meta( $tmcp_id, 'tm_meta_enabled_roles', TRUE );
				$disabled_roles              = themecomplete_get_post_meta( $tmcp_id, 'tm_meta_disabled_roles', TRUE );
				$tm_meta_product_ids         = themecomplete_get_post_meta( $tmcp_id, 'tm_meta_product_ids', TRUE );
				$tm_meta_product_exclude_ids = themecomplete_get_post_meta( $tmcp_id, 'tm_meta_product_exclude_ids', TRUE );

				if ( ! empty( $enabled_roles ) || ! empty( $disabled_roles ) ) {
					$enable = FALSE;
					if ( ! is_array( $enabled_roles ) ) {
						$enabled_roles = array( $enabled_roles );
					}
					if ( ! is_array( $disabled_roles ) ) {
						$disabled_roles = array( $disabled_roles );
					}
					if (isset($enabled_roles[0]) && $enabled_roles[0] === ""){
						$enabled_roles = array();
					}

					if (isset($disabled_roles[0]) && $disabled_roles[0] === ""){
						$disabled_roles = array();
					}

					if (empty($enabled_roles) && !empty($disabled_roles)){
						$enable = TRUE;
					}

					// Get all roles
					$current_user = wp_get_current_user();

					foreach ( $enabled_roles as $key => $value ) {
						if ( $value == "@everyone" ) {
							$enable = TRUE;
						}
						if ( $value == "@loggedin" && is_user_logged_in() ) {
							$enable = TRUE;
						}
					}

					foreach ( $disabled_roles as $key => $value ) {
						if ( $value == "@everyone" ) {
							$enable = FALSE;
						}
						if ( $value == "@loggedin" && is_user_logged_in() ) {
							$enable = FALSE;
						}
					}

					if ( $current_user instanceof WP_User ) {
						$roles = $current_user->roles;

						if ( is_array( $roles ) ) {

							foreach ( $roles as $key => $value ) {
								if ( in_array( $value, $enabled_roles ) ) {
									$enable = TRUE;
									break;
								}
							}

							foreach ( $roles as $key => $value ) {
								if ( in_array( $value, $disabled_roles ) ) {
									$enable = FALSE;
									break;
								}
							}

						}

					}

					if ( ! $enable ) {
						continue;
					}
				}

				$current_builder = themecomplete_get_post_meta( $price->ID, 'tm_meta_wpml', TRUE );
				if ( ! $current_builder ) {
					$current_builder = array();
				} else {
					if ( ! isset( $current_builder['tmfbuilder'] ) ) {
						$current_builder['tmfbuilder'] = array();
					}
					$current_builder = $current_builder['tmfbuilder'];
				}

				$priority = isset( $tmcp_meta['priority'] ) ? absint( $tmcp_meta['priority'] ) : 1000;

				if ( isset( $tmcp_meta['tmfbuilder'] ) ) {

					$global_epos[ $priority ][ $tmcp_id ]['is_form']     = 1;
					$global_epos[ $priority ][ $tmcp_id ]['is_taxonomy'] = 0;
					$global_epos[ $priority ][ $tmcp_id ]['name']        = $price->post_title;
					$global_epos[ $priority ][ $tmcp_id ]['description'] = $price->post_excerpt;
					$global_epos[ $priority ][ $tmcp_id ]['sections']    = array();

					$builder = $tmcp_meta['tmfbuilder'];
					if ( is_array( $builder ) && count( $builder ) > 0 && isset( $builder['element_type'] ) && is_array( $builder['element_type'] ) && count( $builder['element_type'] ) > 0 ) {
						// All the elements
						$_elements = $builder['element_type'];
						// All element sizes
						$_div_size = $builder['div_size'];

						// All sections (holds element count for each section)
						$_sections = $builder['sections'];
						// All section sizes
						$_sections_size = $builder['sections_size'];
						// All section styles
						$_sections_style = $builder['sections_style'];
						// All section placements
						$_sections_placement = $builder['sections_placement'];

						$_sections_slides = isset( $builder['sections_slides'] ) ? $builder['sections_slides'] : '';

						if ( ! is_array( $_sections ) ) {
							$_sections = array( count( $_elements ) );
						}
						if ( ! is_array( $_sections_size ) ) {
							$_sections_size = array_fill( 0, count( $_sections ), "w100" );
						}
						if ( ! is_array( $_sections_style ) ) {
							$_sections_style = array_fill( 0, count( $_sections ), "" );
						}
						if ( ! is_array( $_sections_placement ) ) {
							$_sections_placement = array_fill( 0, count( $_sections ), "before" );
						}

						if ( ! is_array( $_sections_slides ) ) {
							$_sections_slides = array_fill( 0, count( $_sections ), "" );
						}

						$_helper_counter = 0;
						$_counter        = array();

						for ( $_s = 0; $_s < count( $_sections ); $_s ++ ) {
							$_sections_uniqid = $this->get_builder_element( 'sections_uniqid', $builder, $current_builder, $_s, THEMECOMPLETE_EPO_HELPER()->tm_temp_uniqid( count( $_sections ) ), $wpml_section_fields );

							$global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ] = array(
								'total_elements'     => $_sections[ $_s ],
								'sections_size'      => $_sections_size[ $_s ],
								'sections_slides'    => isset( $_sections_slides[ $_s ] ) ? $_sections_slides[ $_s ] : "",
								'sections_style'     => $_sections_style[ $_s ],
								'sections_placement' => $_sections_placement[ $_s ],
								'sections_uniqid'    => $_sections_uniqid,
								'sections_clogic'    => $this->get_builder_element( 'sections_clogic', $builder, $current_builder, $_s, FALSE, $wpml_section_fields, "sections", "", $_sections_uniqid ),
								'sections_logic'     => $this->get_builder_element( 'sections_logic', $builder, $current_builder, $_s, "", $wpml_section_fields, "sections", "", $_sections_uniqid ),
								'sections_class'     => $this->get_builder_element( 'sections_class', $builder, $current_builder, $_s, "", $wpml_section_fields, "sections", "", $_sections_uniqid ),
								'sections_type'      => $this->get_builder_element( 'sections_type', $builder, $current_builder, $_s, "", $wpml_section_fields, "sections", "", $_sections_uniqid ),

								'label_size'           => $this->get_builder_element( 'section_header_size', $builder, $current_builder, $_s, "", $wpml_section_fields, "sections", "", $_sections_uniqid ),
								'label'                => $this->get_builder_element( 'section_header_title', $builder, $current_builder, $_s, "", $wpml_section_fields, "sections", "wc_epo_label", $_sections_uniqid ),
								'label_color'          => $this->get_builder_element( 'section_header_title_color', $builder, $current_builder, $_s, "", $wpml_section_fields, "sections", "", $_sections_uniqid ),
								'label_position'       => $this->get_builder_element( 'section_header_title_position', $builder, $current_builder, $_s, "", $wpml_section_fields, "sections", "", $_sections_uniqid ),
								'description'          => $this->get_builder_element( 'section_header_subtitle', $builder, $current_builder, $_s, "", $wpml_section_fields, "sections", "", $_sections_uniqid ),
								'description_position' => $this->get_builder_element( 'section_header_subtitle_position', $builder, $current_builder, $_s, "", $wpml_section_fields, "sections", "", $_sections_uniqid ),
								'description_color'    => $this->get_builder_element( 'section_header_subtitle_color', $builder, $current_builder, $_s, "", $wpml_section_fields, "sections", "", $_sections_uniqid ),
								'divider_type'         => $this->get_builder_element( 'section_divider_type', $builder, $current_builder, $_s, "", $wpml_section_fields, "sections", "", $_sections_uniqid ),
							);

							$this->current_option_features[] = "section" . $this->get_builder_element( 'sections_type', $builder, $current_builder, $_s, "", $wpml_section_fields, "sections", "", $_sections_uniqid );

							$element_no_in_section = - 1;
							$section_slides        = $global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]['sections_slides'];
							if ( $section_slides !== "" && $global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]['sections_type'] == "slider" ) {
								$section_slides = explode( ",", $section_slides );

							}
							$section_slides_copy = $section_slides;
							for ( $k0 = $_helper_counter; $k0 < intval( $_helper_counter + intval( $_sections[ $_s ] ) ); $k0 ++ ) {
								if ( ! isset( $_elements[ $k0 ] ) ) {
									continue;
								}

								$element_no_in_section ++;
								$current_element = $_elements[ $k0 ];

								$raw_epos[] = $current_element;

								// Delete logic for variations section - not applicable
								if ( $current_element == "variations" ) {
									$global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]["sections_logic"]  = "";
									$global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]["sections_clogic"] = "";
								}

								if ( isset( $current_element ) && isset( $tm_original_builder_elements[ $current_element ] ) ) {
									if ( ! isset( $_counter[ $current_element ] ) ) {
										$_counter[ $current_element ] = 0;
									} else {
										$_counter[ $current_element ] ++;
									}
									$current_counter = $_counter[ $current_element ];

									$_options                         = array();
									$_options_all                     = array(); // even disabled ones - currently used for WPML translation at get_wpml_translation_by_id
									$_regular_price                   = array();
									$_regular_price_filtered          = array();
									$_original_regular_price_filtered = array();
									$_regular_price_type              = array();
									$_new_type                        = $current_element;
									$_prefix                          = "";
									$_min_price0                      = '';
									$_min_price10                     = '';
									$_min_price                       = '';
									$_max_price                       = '';
									$_regular_currencies              = array();
									$price_per_currencies_original    = array();
									$price_per_currencies             = array();
									$_description                     = FALSE;
									$_extra_multiple_choices          = FALSE;
									$_use_lightbox                    = '';
									$_current_deleted_choices         = array();
									$_is_price_fee                    = "";

									if ( $tm_original_builder_elements[ $current_element ] ) {
										if ( $tm_original_builder_elements[ $current_element ]["_is_addon"] == TRUE && $tm_original_builder_elements[ $current_element ]["is_post"] == "display" ) {
											$_prefix = $current_element . "_";
										}

										if ( $tm_original_builder_elements[ $current_element ]["type"] == "single" || $tm_original_builder_elements[ $current_element ]["type"] == "multipleallsingle" ) {
											$_prefix = $current_element . "_";
										} elseif ( $tm_original_builder_elements[ $current_element ]["type"] == "multiple" || $tm_original_builder_elements[ $current_element ]["type"] == "multipleall" || $tm_original_builder_elements[ $current_element ]["type"] == "multiplesingle" ) {
											$_prefix = $current_element . "_";
										}

										$element_uniqueid = $this->get_builder_element( $_prefix . 'uniqid', $builder, $current_builder, $current_counter, THEMECOMPLETE_EPO_HELPER()->tm_uniqid(), $wpml_element_fields, $current_element );

										$is_enabled  = $this->get_builder_element( $_prefix . 'enabled', $builder, $current_builder, $current_counter, "2", $wpml_element_fields, $current_element, "", $element_uniqueid );
										$is_required = $this->get_builder_element( $_prefix . 'required', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid );

										// Currently $no_disabled is disabled by default
										// to allow the conditional logic
										// to work correctly when there is a disabled element
										if ($no_disabled){
											if ( $is_enabled === "" || $is_enabled === "0" ) {

												if ( is_array( $section_slides ) ) {
													$elements_done = 0;
													foreach ( $section_slides as $section_slides_key => $section_slides_value ) {
														$section_slides_value = intval( $section_slides_value );
														$elements_done        = $elements_done + $section_slides_value;
														$previous_done        = $elements_done - $section_slides_value;

														if ( $element_no_in_section >= $previous_done && $element_no_in_section < $elements_done ) {
															$section_slides_copy[ $section_slides_key ]                                 = (string) ( intval( $section_slides_copy[ $section_slides_key ] ) - 1 );
															$global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]['sections_slides'] = implode( ",", $section_slides_copy );
															break;
														}

													}
												}

												continue;
											}
										}

										$tm_epo_options_cache = ( ! $no_cache && $this->tm_epo_options_cache == 'yes' ) ? TRUE : FALSE;

										if ( isset( $wpml_is_original_product ) && ! empty( $wpml_is_original_product ) && apply_filters( 'wc_epo_use_elements_cache', $tm_epo_options_cache ) && isset( $this->cpf_single[ $element_uniqueid ] ) ) {
											$global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]['elements'][] = $this->cpf_single[ $element_uniqueid ];
											if ( isset( $this->cpf_single_epos_prices[ $element_uniqueid ] ) ) {
												$epos_prices[] = $this->cpf_single_epos_prices[ $element_uniqueid ];
											}
											if ( isset( $this->cpf_single_variation_element_id[ $element_uniqueid ] ) ) {
												$variation_element_id = $this->cpf_single_variation_element_id[ $element_uniqueid ];
											}
											if ( isset( $this->cpf_single_variation_section_id[ $element_uniqueid ] ) ) {
												$variation_section_id = $this->cpf_single_variation_section_id[ $element_uniqueid ];
											}

											continue;
										}

										if ( isset( $builder[ $current_element . '_fee' ] ) && isset( $builder[ $current_element . '_fee' ][ $current_counter ] ) ) {
											$_is_price_fee = $builder[ $current_element . '_fee' ][ $current_counter ];
										}

										if ( $tm_original_builder_elements[ $current_element ]["type"] == "single" || $tm_original_builder_elements[ $current_element ]["type"] == "multipleallsingle" ) {
											$_prefix = $current_element . "_";

											$_is_field_required     = $this->get_builder_element( $_prefix . 'required', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid );
											$_changes_product_image = $this->get_builder_element( $_prefix . 'changes_product_image', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid );
											$_use_images            = $this->get_builder_element( $_prefix . 'use_images', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid );
											$_use_colors            = $this->get_builder_element( $_prefix . 'use_colors', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid );
											$_price = isset( $builder[ $current_element . '_price' ] ) ? $builder[ $current_element . '_price' ][ $current_counter ] : "";
											$_price = $this->get_builder_element( $_prefix . 'price', $builder, $current_builder, $current_counter, $_price, $wpml_element_fields, $current_element, "", $element_uniqueid );
											
											$_original_regular_price_filtered = $_price;
											if ( isset( $builder[ $current_element . '_sale_price' ][ $current_counter ] ) && $builder[ $current_element . '_sale_price' ][ $current_counter ] !== '' ) {
												$_price = $builder[ $current_element . '_sale_price' ][ $current_counter ];
												$_price = $this->get_builder_element( $_prefix . 'sale_price', $builder, $current_builder, $current_counter, $_price, $wpml_element_fields, $current_element, "", $element_uniqueid );
											}

											$_price = apply_filters( "wc_epo_apply_discount", $_price, $_original_regular_price_filtered);

											$this_price_type = "";

											$_regular_price_type    = array( array( "" ) );
											$_for_filter_price_type = "";

											// backwards compatiiblity
											if ( isset( $builder[ $current_element . '_price_type' ][ $current_counter ] ) ) {
												$_regular_price_type = $builder[ $current_element . '_price_type' ][ $current_counter ];
												$this_price_type     = $_regular_price_type;
												switch ( $_regular_price_type ) {
													case 'fee':
														$_regular_price_type = "";
														$_is_price_fee       = "1";
														break;
													case 'stepfee':
														$_regular_price_type = "step";
														$_is_price_fee       = "1";
														break;
													case 'currentstepfee':
														$_regular_price_type = "currentstep";
														$_is_price_fee       = "1";
														break;
												}
												$_for_filter_price_type = $_regular_price_type;
												$_regular_price_type    = array( array( $_regular_price_type ) );

											}

											if ( $this_price_type === "math" ) {
												$_regular_price = array( array( $_price ) );
											} else {
												$_regular_price = array( array( wc_format_decimal( $_price, FALSE, TRUE ) ) );
											}

											if ( THEMECOMPLETE_EPO_WPML()->is_active() && THEMECOMPLETE_EPO_WPML()->is_multi_currency() ) {
												global $woocommerce_wpml;
												global $sitepress;
												if ( $woocommerce_wpml && isset( $original_product_id ) && isset( $wpml_is_original_product ) ) {

													$basetype     = $price->post_type;
													$translations = THEMECOMPLETE_EPO_WPML()->sitepress_instance()->get_element_translations( THEMECOMPLETE_EPO_WPML()->sitepress_instance()->get_element_trid( $original_product_id, 'post_product' ), 'product' );

													$woocommerce_wpml_currencies = $woocommerce_wpml->settings["currency_options"];

													foreach ( $woocommerce_wpml_currencies as $currency => $currency_data ) {

														$thisbuilder = array();

														foreach ( $currency_data["languages"] as $lang => $is_lang_enabled ) {

															if ( $is_lang_enabled && $lang == THEMECOMPLETE_EPO_WPML()->get_lang() ) {

																if ( $basetype === "product" ) {

																	if ( isset( $translations[ $lang ] ) ){

																		$this_wpml_is_original_product = THEMECOMPLETE_EPO_WPML()->is_original_product( $translations[ $lang ]->element_id, "product" );

																		if ( $this_wpml_is_original_product && $lang == THEMECOMPLETE_EPO_WPML()->get_lang() ) {
																			$thisbuilder = $builder;
																		} else {
																			$thisbuilder = themecomplete_get_post_meta( $translations[ $lang ]->element_id, ( $this_wpml_is_original_product ? 'tm_meta' : 'tm_meta_wpml' ), TRUE );
																			if ( isset( $thisbuilder['tmfbuilder'] ) ) {
																				$thisbuilder = $thisbuilder['tmfbuilder'];
																			} else {
																				$thisbuilder = array();
																			}
																		}

																	}

																} else {
																	if ( $wpml_is_original_product && $lang == THEMECOMPLETE_EPO_WPML()->get_lang() ) {
																		$thisbuilder = $builder;
																	} else {
																		$args                 = array(
																			'post_type'   => $basetype,
																			'post_status' => array( 'publish', 'draft' ), // get only enabled global extra options
																			'numberposts' => - 1,
																			'orderby'     => 'date',
																			'order'       => 'asc',
																		);
																		$args['meta_query']   = THEMECOMPLETE_EPO_HELPER()->build_meta_query( 'AND', THEMECOMPLETE_EPO_WPML_LANG_META, $lang, '=', 'EXISTS' );
																		$args['meta_query'][] = array(
																			'key'     => THEMECOMPLETE_EPO_WPML_PARENT_POSTID,
																			'value'   => $original_product_id,
																			'compare' => '=',
																		);
																		$other_translations   = get_posts( $args );

																		if ( ! empty( $other_translations ) && isset( $other_translations[0] ) && is_object( $other_translations[0] ) && property_exists( $other_translations[0], 'ID' ) ) {
																			$this_wpml_is_original_product = THEMECOMPLETE_EPO_WPML()->is_original_product( $other_translations[0]->ID, $basetype );
																			$thisbuilder                   = themecomplete_get_post_meta( $other_translations[0]->ID, ( $this_wpml_is_original_product ? 'tm_meta' : 'tm_meta_wpml' ), TRUE );
																			if ( isset( $thisbuilder['tmfbuilder'] ) ) {
																				$thisbuilder = $thisbuilder['tmfbuilder'];
																			} else {
																				$thisbuilder = array();
																			}
																		}
																	}
																}

																break;
															}
														}
														$_current_currency_price      = 
															( isset( $thisbuilder[ $current_element . '_price_'. $currency ] ) && isset( $thisbuilder[ $current_element . '_price_'. $currency ][ $current_counter ] ) ) ? $thisbuilder[ $current_element . '_price_'. $currency ][ $current_counter ] :
															(isset( $thisbuilder[ $current_element . '_price' ][ $current_counter ] )
																? $thisbuilder[ $current_element . '_price' ][ $current_counter ]
																: '');
														$_current_currency_sale_price      = 
															( isset( $thisbuilder[ $current_element . '_sale_price_'. $currency ] ) && isset( $thisbuilder[ $current_element . '_sale_price_'. $currency ][ $current_counter ] ) ) ? $thisbuilder[ $current_element . '_sale_price_'. $currency ][ $current_counter ] :
															(isset( $thisbuilder[ $current_element . '_sale_price' ][ $current_counter ] )
																? $thisbuilder[ $current_element . '_sale_price' ][ $current_counter ]
																: '');
														if ( $_current_currency_price !== '' ) {
															if ( $this_price_type === "math" ) {
																$price_per_currencies_original[ $currency ] = array( array( $_current_currency_price ) );
															} else {
																$price_per_currencies_original[ $currency ] = array( array( wc_format_decimal( $_current_currency_price, FALSE, TRUE ) ) );
															}
														}

														if ( $_current_currency_sale_price && $_current_currency_sale_price !== '' ) {
															$_current_currency_price = $_current_currency_sale_price;
														}
														if ( $_current_currency_price !== '' ) {
															if ( $this_price_type === "math" ) {
																$price_per_currencies[ $currency ] = array( array( $_current_currency_price ) );
															} else {
																$price_per_currencies[ $currency ] = array( array( wc_format_decimal( $_current_currency_price, FALSE, TRUE ) ) );
															}
														}

													}

												}

											} else {
												foreach ( THEMECOMPLETE_EPO_HELPER()->get_currencies() as $currency ) {
													$mt_prefix                    = THEMECOMPLETE_EPO_HELPER()->get_currency_price_prefix( $currency );
													$_current_currency_price      = isset( $builder[ $current_element . '_price' . $mt_prefix ][ $current_counter ] ) ? $builder[ $current_element . '_price' . $mt_prefix ][ $current_counter ] : '';
													$_current_currency_sale_price = isset( $builder[ $current_element . '_sale_price' . $mt_prefix ][ $current_counter ] ) ? $builder[ $current_element . '_sale_price' . $mt_prefix ][ $current_counter ] : '';

													if ( $_current_currency_price !== '' ) {
														if ( $this_price_type === "math" ) {
															$price_per_currencies_original[ $currency ] = array( array( $_current_currency_price ) );
														} else {
															$price_per_currencies_original[ $currency ] = array( array( wc_format_decimal( $_current_currency_price, FALSE, TRUE ) ) );
														}
													}

													if ( $_current_currency_sale_price && $_current_currency_sale_price !== '' ) {
														$_current_currency_price = $_current_currency_sale_price;
													}
													if ( $_current_currency_price !== '' ) {
														if ( $this_price_type === "math" ) {
															$price_per_currencies[ $currency ] = array( array( $_current_currency_price ) );
														} else {
															$price_per_currencies[ $currency ] = array( array( wc_format_decimal( $_current_currency_price, FALSE, TRUE ) ) );
														}
													}
												}
											}

											$new_currency = FALSE;
											$mt_prefix    = THEMECOMPLETE_EPO_HELPER()->get_currency_price_prefix(NULL, "");

											$_current_currency_original_price = isset( $price_per_currencies_original[ $mt_prefix ] ) ? $price_per_currencies_original[ $mt_prefix ][0][0] : '';
											$_current_currency_price = isset( $price_per_currencies[ $mt_prefix ] ) ? $price_per_currencies[ $mt_prefix ][0][0] : '';
											
											if ( $mt_prefix !== '' && $_current_currency_price !== '' ) {
												$_price                           = $_current_currency_price;
												$_original_regular_price_filtered = $_current_currency_original_price;
												
												$_regular_currencies = array( themecomplete_get_woocommerce_currency() );
												$new_currency        = TRUE;
											}

											if ( ! $new_currency ) {
												$_price                           = apply_filters( 'wc_epo_get_current_currency_price', $_price, $_for_filter_price_type );
												$_original_regular_price_filtered = apply_filters( 'wc_epo_get_current_currency_price', $_original_regular_price_filtered, $_for_filter_price_type );
											}

											$_price                           = apply_filters( 'wc_epo_price', $_price, $_for_filter_price_type, $post_id );
											$_original_regular_price_filtered = apply_filters( 'wc_epo_price', $_original_regular_price_filtered, $_for_filter_price_type, $post_id );

											if ( $_is_price_fee === "" && $_price !== '' && isset( $builder[ $current_element . '_price_type' ][ $current_counter ] ) && $builder[ $current_element . '_price_type' ][ $current_counter ] == '' ) {
												$_min_price = $_max_price = wc_format_decimal( $_price, FALSE, TRUE );
												if ( $_is_field_required ) {
													$_min_price0 = $_min_price;
												} else {
													$_min_price0  = 0;
													$_min_price10 = $_min_price;
												}
											} else {
												$_min_price  = $_max_price = FALSE;
												$_min_price0 = 0;
											}
											if ( $this_price_type === "math" ) {
												$_regular_price_filtered          = array( array( $_price ) );
												$_original_regular_price_filtered = array( array( $_original_regular_price_filtered ) );
											} else {
												$_regular_price_filtered          = array( array( wc_format_decimal( $_price, FALSE, TRUE ) ) );
												$_original_regular_price_filtered = array( array( wc_format_decimal( $_original_regular_price_filtered, FALSE, TRUE ) ) );
											}


										} elseif ( $tm_original_builder_elements[ $current_element ]["type"] == "multiple" || $tm_original_builder_elements[ $current_element ]["type"] == "multipleall" || $tm_original_builder_elements[ $current_element ]["type"] == "multiplesingle" ) {
											$_prefix = $current_element . "_";

											$_is_field_required = $this->get_builder_element( $_prefix . 'required', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid );

											$_changes_product_image = $this->get_builder_element( $_prefix . 'changes_product_image', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid );
											$_use_images            = $this->get_builder_element( $_prefix . 'use_images', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid );
											$_use_colors            = $this->get_builder_element( $_prefix . 'use_colors', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid );
											$_use_lightbox          = $this->get_builder_element( $_prefix . 'use_lightbox', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid );

											if ( isset( $builder[ 'multiple_' . $current_element . '_options_price' ][ $current_counter ] ) ) {

												$_prices = $builder[ 'multiple_' . $current_element . '_options_price' ][ $current_counter ];
												$_prices = $this->get_builder_element( 'multiple_' . $current_element . '_options_price', $builder, $current_builder, $current_counter, $_prices, TRUE, $current_element, "wc_epo_multiple_prices", $element_uniqueid );

												$_original_prices = $_prices;
												$_sale_prices     = $_prices;
												if ( isset( $builder[ 'multiple_' . $current_element . '_options_sale_price' ][ $current_counter ] ) ) {
													$_sale_prices = $builder[ 'multiple_' . $current_element . '_options_sale_price' ][ $current_counter ];
													$_sale_prices = $this->get_builder_element( 'multiple_' . $current_element . '_sale_prices', $builder, $current_builder, $current_counter, $_sale_prices, TRUE, $current_element, "wc_epo_multiple_sale_prices", $element_uniqueid );
												}
												$_prices = THEMECOMPLETE_EPO_HELPER()->merge_price_array( $_prices, $_sale_prices );

												$_prices = apply_filters( "wc_epo_apply_discount", $_prices, $_original_prices);

												$mt_prefix                         = THEMECOMPLETE_EPO_HELPER()->get_currency_price_prefix();
												$_current_currency_prices          = $this->get_builder_element( 'multiple_' . $current_element . '_options_price' . $mt_prefix, $builder, $current_builder, $current_counter, array(), TRUE, $current_element, "", $element_uniqueid );
												$_original_current_currency_prices = $_current_currency_prices;
												$_current_currency_sale_prices     = $this->get_builder_element( 'multiple_' . $current_element . '_options_sale_price' . $mt_prefix, $builder, $current_builder, $current_counter, array(), TRUE, $current_element, "", $element_uniqueid );											
												$_current_currency_prices          = THEMECOMPLETE_EPO_HELPER()->merge_price_array( $_current_currency_prices, $_current_currency_sale_prices );

												$_values      = $this->get_builder_element( 'multiple_' . $current_element . '_options_value', $builder, $current_builder, $current_counter, array(), TRUE, $current_element, "wc_epo_multiple_values", $element_uniqueid );
												$_titles      = $this->get_builder_element( 'multiple_' . $current_element . '_options_title', $builder, $current_builder, $current_counter, array(), TRUE, $current_element, "wc_epo_multiple_titles", $element_uniqueid );
												$_images      = $this->get_builder_element( 'multiple_' . $current_element . '_options_image', $builder, $current_builder, $current_counter, array(), TRUE, $current_element, 'tm_image_url', $element_uniqueid );
												$_imagesc     = $this->get_builder_element( 'multiple_' . $current_element . '_options_imagec', $builder, $current_builder, $current_counter, array(), TRUE, $current_element, 'tm_image_url', $element_uniqueid );
												$_imagesp     = $this->get_builder_element( 'multiple_' . $current_element . '_options_imagep', $builder, $current_builder, $current_counter, array(), TRUE, $current_element, 'tm_image_url', $element_uniqueid );
												$_imagesl     = $this->get_builder_element( 'multiple_' . $current_element . '_options_imagel', $builder, $current_builder, $current_counter, array(), TRUE, $current_element, 'tm_image_url', $element_uniqueid );
												$_color       = $this->get_builder_element( 'multiple_' . $current_element . '_options_color', $builder, $current_builder, $current_counter, array(), TRUE, $current_element, "", $element_uniqueid );
												$_prices_type = $this->get_builder_element( 'multiple_' . $current_element . '_options_price_type', $builder, $current_builder, $current_counter, array(), TRUE, $current_element, "", $element_uniqueid );

												if ( ! is_array( $_values ) ) {
													$_values = array( $_values );
												}
												if ( ! is_array( $_titles ) ) {
													$_titles = array( $_titles );
												}
												if ( ! is_array( $_images ) ) {
													$_images = array( $_images );
												}
												if ( ! is_array( $_imagesc ) ) {
													$_imagesc = array( $_imagesc );
												}
												if ( ! is_array( $_imagesp ) ) {
													$_imagesp = array( $_imagesp );
												}
												if ( ! is_array( $_imagesl ) ) {
													$_imagesl = array( $_imagesl );
												}
												if ( ! is_array( $_color ) ) {
													$_color = array( $_color );
												}
												if ( ! is_array( $_prices_type ) ) {
													$_prices_type = array( $_prices_type );
												}

												if ( THEMECOMPLETE_EPO_WPML()->is_active() && THEMECOMPLETE_EPO_WPML()->is_multi_currency() ) {
													global $woocommerce_wpml;
													global $sitepress;
													if ( $woocommerce_wpml && isset( $original_product_id ) && isset( $wpml_is_original_product ) ) {

														$basetype     = $price->post_type;
														$translations = THEMECOMPLETE_EPO_WPML()->sitepress_instance()->get_element_translations( THEMECOMPLETE_EPO_WPML()->sitepress_instance()->get_element_trid( $original_product_id, 'post_product' ), 'product' );

														$woocommerce_wpml_currencies = $woocommerce_wpml->settings["currency_options"];

														foreach ( $woocommerce_wpml_currencies as $currency => $currency_data ) {

															$thisbuilder = array();

															foreach ( $currency_data["languages"] as $lang => $is_lang_enabled ) {

																if ( $is_lang_enabled && $lang == THEMECOMPLETE_EPO_WPML()->get_lang() ) {

																	if ( $basetype === "product" ) {

																		if ( isset( $translations[ $lang ] ) ){

																			$this_wpml_is_original_product = THEMECOMPLETE_EPO_WPML()->is_original_product( $translations[ $lang ]->element_id, "product" );

																			if ( $this_wpml_is_original_product && $lang == THEMECOMPLETE_EPO_WPML()->get_lang() ) {
																				$thisbuilder = $builder;
																			} else {
																				$thisbuilder = themecomplete_get_post_meta( $translations[ $lang ]->element_id, ( $this_wpml_is_original_product ? 'tm_meta' : 'tm_meta_wpml' ), TRUE );
																				if ( isset( $thisbuilder['tmfbuilder'] ) ) {
																					$thisbuilder = $thisbuilder['tmfbuilder'];
																				} else {
																					$thisbuilder = array();
																				}
																			}

																		}
																	
																	} else {
																		if ( $wpml_is_original_product && $lang == THEMECOMPLETE_EPO_WPML()->get_lang() ) {
																			$thisbuilder = $builder;
																		} else {
																			$args                 = array(
																				'post_type'   => $basetype,
																				'post_status' => array( 'publish', 'draft' ), // get only enabled global extra options
																				'numberposts' => - 1,
																				'orderby'     => 'date',
																				'order'       => 'asc',
																			);
																			$args['meta_query']   = THEMECOMPLETE_EPO_HELPER()->build_meta_query( 'AND', THEMECOMPLETE_EPO_WPML_LANG_META, $lang, '=', 'EXISTS' );
																			$args['meta_query'][] = array(
																				'key'     => THEMECOMPLETE_EPO_WPML_PARENT_POSTID,
																				'value'   => $original_product_id,
																				'compare' => '=',
																			);
																			$other_translations   = get_posts( $args );

																			if ( ! empty( $other_translations ) && isset( $other_translations[0] ) && is_object( $other_translations[0] ) && property_exists( $other_translations[0], 'ID' ) ) {
																				$this_wpml_is_original_product = THEMECOMPLETE_EPO_WPML()->is_original_product( $other_translations[0]->ID, $basetype );
																				$thisbuilder                   = themecomplete_get_post_meta( $other_translations[0]->ID, ( $this_wpml_is_original_product ? 'tm_meta' : 'tm_meta_wpml' ), TRUE );
																				if ( isset( $thisbuilder['tmfbuilder'] ) ) {
																					$thisbuilder = $thisbuilder['tmfbuilder'];
																				} else {
																					$thisbuilder = array();
																				}
																			}
																		}
																	}

																	break;
																}
															}

															$_current_currency_price      = 
																( isset( $thisbuilder[ 'multiple_' . $current_element . '_options_price_'. $currency ] ) && isset( $thisbuilder[ 'multiple_' . $current_element . '_options_price_'. $currency ][ $current_counter ] ) ) ? $thisbuilder[ 'multiple_' . $current_element . '_options_price_'. $currency ][ $current_counter ] :
																(isset( $thisbuilder[ 'multiple_' . $current_element . '_options_price' ][ $current_counter ] )
																	? $thisbuilder[ 'multiple_' . $current_element . '_options_price' ][ $current_counter ]
																	: '');
															$_current_currency_sale_price      = 
																( isset( $thisbuilder[ 'multiple_' . $current_element . '_options_sale_price_'. $currency ] ) && isset( $thisbuilder[ 'multiple_' . $current_element . '_options_sale_price_'. $currency ][ $current_counter ] ) ) ? $thisbuilder[ 'multiple_' . $current_element . '_options_sale_price_'. $currency ][ $current_counter ] :
																(isset( $thisbuilder[ 'multiple_' . $current_element . '_options_sale_price' ][ $current_counter ] )
																	? $thisbuilder[ 'multiple_' . $current_element . '_options_sale_price' ][ $current_counter ]
																	: '');

															$_current_currency_price = THEMECOMPLETE_EPO_HELPER()->merge_price_array( $_current_currency_price, $_current_currency_sale_price );

															$price_per_currencies[ $currency ] = $_current_currency_price;
															if ( ! is_array( $price_per_currencies[ $currency ] ) ) {
																$price_per_currencies[ $currency ] = array();
															}

															foreach ( $_prices as $_n => $_price ) {
																$to_price = '';
																if ( is_array( $_current_currency_price ) && isset( $_current_currency_price[ $_n ] ) ) {
																	$to_price = $_current_currency_price[ $_n ];
																}
																if ( $_prices_type[ $_n ] === "math" ) {
																	$price_per_currencies[ $currency ][ esc_attr( ( $_values[ $_n ] ) ) . "_" . $_n ] = array( $to_price );
																} else {
																	$price_per_currencies[ $currency ][ esc_attr( ( $_values[ $_n ] ) ) . "_" . $_n ] = array( wc_format_decimal( $to_price, FALSE, TRUE ) );
																}
															}

														}

													}

												} else {

													foreach ( THEMECOMPLETE_EPO_HELPER()->get_currencies() as $currency ) {
														$mt_prefix                    = THEMECOMPLETE_EPO_HELPER()->get_currency_price_prefix( $currency );
														$_current_currency_price      = isset( $builder[ 'multiple_' . $current_element . '_options_price' . $mt_prefix ][ $current_counter ] )
															? $builder[ 'multiple_' . $current_element . '_options_price' . $mt_prefix ][ $current_counter ]
															: '';
														$_current_currency_sale_price = isset( $builder[ 'multiple_' . $current_element . '_options_sale_price' . $mt_prefix ][ $current_counter ] )
															? $builder[ 'multiple_' . $current_element . '_options_sale_price' . $mt_prefix ][ $current_counter ]
															: '';

														$_current_currency_price = THEMECOMPLETE_EPO_HELPER()->merge_price_array( $_current_currency_price, $_current_currency_sale_price );

														$price_per_currencies[ $currency ] = $_current_currency_price;
														if ( ! is_array( $price_per_currencies[ $currency ] ) ) {
															$price_per_currencies[ $currency ] = array();
														}
														foreach ( $_prices as $_n => $_price ) {
															$to_price = '';
															if ( is_array( $_current_currency_price ) && isset( $_current_currency_price[ $_n ] ) ) {
																$to_price = $_current_currency_price[ $_n ];
															}
															if ( $_prices_type[ $_n ] === "math" ) {
																$price_per_currencies[ $currency ][ esc_attr( ( $_values[ $_n ] ) ) . "_" . $_n ] = array( $to_price );
															} else {
																$price_per_currencies[ $currency ][ esc_attr( ( $_values[ $_n ] ) ) . "_" . $_n ] = array( wc_format_decimal( $to_price, FALSE, TRUE ) );
															}
														}
													}
												}

												if ( $_changes_product_image == "images" && $_use_images == "" ) {
													$_imagesp               = $_images;
													$_images                = array();
													$_imagesc               = array();
													$_changes_product_image = "custom";
												}
												if ( $_use_images == "" ) {
													$_use_lightbox = "";
												}

												$_url         = $this->get_builder_element( 'multiple_' . $current_element . '_options_url', $builder, $current_builder, $current_counter, array(), TRUE, $current_element, "", $element_uniqueid );
												$_description = $this->get_builder_element( 'multiple_' . $current_element . '_options_description', $builder, $current_builder, $current_counter, array(), TRUE, $current_element, "", $element_uniqueid );
												$_enabled     = $this->get_builder_element( 'multiple_' . $current_element . '_options_enabled', $builder, $current_builder, $current_counter, array(), TRUE, $current_element, "1", $element_uniqueid );
												$_fee         = $this->get_builder_element( 'multiple_' . $current_element . '_options_fee', $builder, $current_builder, $current_counter, array(), TRUE, $current_element, "", $element_uniqueid );

												foreach ( THEMECOMPLETE_EPO_BUILDER()->extra_multiple_options as $__key => $__name ) {
													$_extra_name                             = $__name["name"];
													$_extra_multiple_choices[ $_extra_name ] = $this->get_builder_element( 'multiple_' . $current_element . '_options_' . $_extra_name, $builder, $current_builder, $current_counter, array(), TRUE, $current_element, "", $element_uniqueid );
												}

												$_values_c = $_values;
												$_values_ce = $_values;
												$mt_prefix = THEMECOMPLETE_EPO_HELPER()->get_currency_price_prefix(NULL, "");
												$_nn       = 0;
												foreach ( $_prices as $_n => $_price ) {

													if ( isset( $_enabled[ $_n ] ) && ( $_enabled[ $_n ] === "0" || $_enabled[ $_n ] === "" ) ) {
														$_options_all[ esc_attr( ( $_values[ $_n ] ) ) . "_" . $_n ] = $_titles[ $_n ];
														unset( $_images[ $_n ] );
														unset( $_imagesc[ $_n ] );
														unset( $_imagesp[ $_n ] );
														unset( $_imagesl[ $_n ] );
														unset( $_color[ $_n ] );
														unset( $_url[ $_n ] );
														unset( $_description[ $_n ] );
														unset( $_titles[ $_n ] );
														unset( $_values[ $_n ] );
														unset( $_original_prices[ $_n ] );
														unset( $_prices_type[ $_n ] );
														unset( $_values_ce[ $_n ] );
														if ( isset( $_current_currency_prices ) && is_array( $_current_currency_prices ) ) {
															unset( $_current_currency_prices[ $_n ] );
														}
														if ( isset( $_original_current_currency_prices ) && is_array( $_original_current_currency_prices ) ) {
															unset( $_original_current_currency_prices[ $_n ] );
														}
														if ( isset( $_fee ) && is_array( $_fee ) ) {
															unset( $_fee[ $_n ] );
														}
														unset( $_sale_prices[ $_n ] );

														do_action( 'wc_epo_admin_option_is_disable', $_n );
														$_current_deleted_choices[] = $_n;
														continue;
													}

													// backwards compatibility
													if ( isset( $_prices_type[ $_n ] ) ) {
														if ( $_prices_type[ $_n ] === "fee" ) {
															if ( $current_element === "checkboxes" ) {
																$_fee[ $_n ] = "1";
															} else {
																$_is_price_fee = "1";
															}
															$_prices_type[ $_n ] === "";
														}
													}

													$new_currency = FALSE;
													if ( $mt_prefix !== ''
													     && $_current_currency_prices !== ''
													     && is_array( $_current_currency_prices )
													     && isset( $_current_currency_prices[ $_n ] )
													     && $_current_currency_prices[ $_n ] != ''
													) {
														$new_currency                                                       = TRUE;
														$_price                                                             = $_current_currency_prices[ $_n ];
														$_original_prices[ $_n ]                                            = $_original_current_currency_prices[ $_n ];
														$_regular_currencies[ esc_attr( ( $_values[ $_n ] ) ) . "_" . $_n ] = array( themecomplete_get_woocommerce_currency() );
													}
													if ( $_prices_type[ $_n ] === "math" ) {
														$_f_price = $_price;
													} else {
														$_f_price = wc_format_decimal( $_price, FALSE, TRUE );
													}

													$_regular_price[ esc_attr( ( $_values[ $_n ] ) ) . "_" . $_n ] = array( $_f_price );
													$_for_filter_price_type                                        = isset( $_prices_type[ $_n ] ) ? $_prices_type[ $_n ] : "";

													if ( ! $new_currency ) {
														$_price                  = apply_filters( 'wc_epo_get_current_currency_price', $_price, $_for_filter_price_type );
														$_original_prices[ $_n ] = apply_filters( 'wc_epo_get_current_currency_price', $_original_prices[ $_n ], $_for_filter_price_type );
													} else {

													}
													$_price                  = apply_filters( 'wc_epo_price', $_price, $_for_filter_price_type, $post_id );
													$_original_prices[ $_n ] = apply_filters( 'wc_epo_price', $_original_prices[ $_n ], $_for_filter_price_type, $post_id );

													if ( $_prices_type[ $_n ] === "math" ) {
														$_f_price                                                                         = $_price;
														$_regular_price_filtered[ esc_attr( ( $_values[ $_n ] ) ) . "_" . $_n ]           = array( $_price );
														$_original_regular_price_filtered [ esc_attr( ( $_values[ $_n ] ) ) . "_" . $_n ] = array( $_original_prices[ $_n ] );
													} else {
														$_f_price                                                                         = wc_format_decimal( $_price, FALSE, TRUE );
														$_regular_price_filtered[ esc_attr( ( $_values[ $_n ] ) ) . "_" . $_n ]           = array( wc_format_decimal( $_price, FALSE, TRUE ) );
														$_original_regular_price_filtered [ esc_attr( ( $_values[ $_n ] ) ) . "_" . $_n ] = array( wc_format_decimal( $_original_prices[ $_n ], FALSE, TRUE ) );
													}

													$_regular_price_type[ esc_attr( ( $_values[ $_n ] ) ) . "_" . $_n ] = isset( $_prices_type[ $_n ] ) ? array( ( $_prices_type[ $_n ] ) ) : array( '' );
													$_options_all[ esc_attr( ( $_values[ $_n ] ) ) . "_" . $_n ]        = $_titles[ $_n ];
													$_options[ esc_attr( ( $_values[ $_n ] ) ) . "_" . $_n ]            = $_titles[ $_n ];
													$_values_c[ $_n ]                                                   = $_values[ $_n ] . "_" . $_n;
													$_values_ce[ $_n ] = $_values_c[ $_n ];
													if ( ( ( isset( $_fee[ $_n ] ) && $_fee[ $_n ] !== "1" ) || ! isset( $_fee[ $_n ] ) ) && isset( $_prices_type[ $_n ] ) && $_prices_type[ $_n ] == '' && ( ( isset( $builder[ $current_element . '_price_type' ][ $current_counter ] ) && $builder[ $current_element . '_price_type' ][ $current_counter ] == '' ) || ! isset( $builder[ $current_element . '_price_type' ][ $current_counter ] ) ) ) {
														if ( $_min_price !== FALSE && $_price !== '' ) {
															if ( $_min_price === '' ) {
																$_min_price = $_f_price;
															} else {
																if ( $_min_price > $_f_price ) {
																	$_min_price = $_f_price;
																}
															}
															if ( $_min_price0 === '' ) {
																if ( $_is_field_required ) {
																	$_min_price0 = floatval( $_min_price );
																} else {
																	$_min_price0 = 0;
																}
															} else {
																if ( $_is_field_required && $_min_price0 > floatval( $_min_price ) ) {
																	$_min_price0 = floatval( $_min_price );
																}
															}
															if ( $_min_price10 === '' ) {
																$_min_price10 = floatval( $_min_price );
															} else {
																if ( $_min_price10 > floatval( $_min_price ) ) {
																	$_min_price10 = floatval( $_min_price );
																}
															}
															if ( $_max_price === '' ) {
																$_max_price = $_f_price;
															} else {
																if ( $current_element == 'checkboxes' ) {
																	// needs work for Limit selection/Exact selection/Minimum selection
																	$_max_price = $_max_price + $_f_price;
																} else {
																	if ( $_max_price < $_f_price ) {
																		$_max_price = $_f_price;
																	}
																}
															}
														} else {
															if ( $_price === '' ) {
																$_min_price0  = 0;
																$_min_price10 = 0;
															}
														}
													} else {
														$_min_price = $_max_price = FALSE;
														if ( $_min_price0 === '' ) {
															$_min_price0 = 0;
														} else {
															if ( $_min_price0 > floatval( $_min_price ) ) {
																$_min_price0 = floatval( $_min_price );
															}
														}
														if ( $_min_price10 === '' ) {
															$_min_price10 = 0;
														} else {
															if ( $_min_price10 > floatval( $_min_price ) ) {
																$_min_price10 = floatval( $_min_price );
															}
														}
													}
													$_nn ++;
												}

												$_images          = array_values( $_images );
												$_imagesc         = array_values( $_imagesc );
												$_imagesp         = array_values( $_imagesp );
												$_imagesl         = array_values( $_imagesl );
												$_color           = array_values( $_color );
												$_url             = array_values( $_url );
												$_description     = array_values( $_description );
												$_titles          = array_values( $_titles );
												$_values          = array_values( $_values );
												$_original_prices = array_values( $_original_prices );
												$_prices_type     = array_values( $_prices_type );
												if ( isset( $_current_currency_prices ) && is_array( $_current_currency_prices ) ) {
													$_current_currency_prices = array_values( $_current_currency_prices );
												}
												if ( isset( $_original_current_currency_prices ) && is_array( $_original_current_currency_prices ) ) {
													$_original_current_currency_prices = array_values( $_original_current_currency_prices );
												}
												if ( isset( $_fee ) && is_array( $_fee ) ) {
													$_fee = array_values( $_fee );
												}
												$_sale_prices = array_values( $_sale_prices );
												$_values_c    = array_values( $_values_c );
												$_values_ce   = array_values( $_values_ce );
												$_prices      = array_values( $_prices );

												do_action( 'wc_epo_admin_option_reindex' );
											}
										}
									}
									$default_value = "";
									if ( isset( $builder[ 'multiple_' . $current_element . '_options_default_value' ][ $current_counter ] ) ) {
										$default_value = $builder[ 'multiple_' . $current_element . '_options_default_value' ][ $current_counter ];

										$disabled_count = count( array_filter( $_current_deleted_choices, function ( $n ) use ( $default_value ) {
											return $n <= $default_value;
										} ) );

										if ( is_array( $default_value ) ) {
											foreach ( $default_value as $key => $value ) {
												if ( $value !== "" ) {
													$default_value[ $key ] = intval( $value ) - $disabled_count;
												}
											}
											if ( $current_element === "selectbox" && isset( $default_value[ $current_counter ] ) ) {
												$default_value = $default_value[ $current_counter ];
											}
										} else {
											if ( $default_value !== "" ) {
												$default_value = intval( $default_value ) - $disabled_count;
											}
										}

									} elseif ( isset( $builder[ $_prefix . 'default_value' ] ) && isset( $builder[ $_prefix . 'default_value' ][ $current_counter ] ) ) {
										$default_value = $builder[ $_prefix . 'default_value' ][ $current_counter ];
									}

									switch ( $current_element ) {

										case "selectbox":
											$_new_type = "select";
											if ( isset( $builder[ $current_element . '_price_type' ][ $current_counter ] ) ) {
												// backwards compatibility
												$selectbox_fee = $builder[ $current_element . '_price_type' ][ $current_counter ];
												$_is_price_fee = ( $selectbox_fee === "fee" ) ? "1" : "";

											}

											break;

										case "radiobuttons":
											$_new_type = "radio";
											break;

										case "checkboxes":
											$_new_type = "checkbox";
											break;

									}

									$_rules_type = $_regular_price_type;
									foreach ( $_regular_price_type as $key => $value ) {
										foreach ( $value as $k => $v ) {
											$_regular_price_type[ $key ][ $k ] = $v;
										}
									}

									$_rules          = $_regular_price;
									$_rules_filtered = $_regular_price_filtered;
									foreach ( $_regular_price as $key => $value ) {
										foreach ( $value as $k => $v ) {
											if ( $_regular_price_type[ $key ][ $k ] !== "math" ) {
												$_regular_price[ $key ][ $k ]          = wc_format_localized_price( $v );
												$_regular_price_filtered[ $key ][ $k ] = wc_format_localized_price( $v );
											}
										}
									}

									if ( $current_element != 'variations' ) {
										$epos_prices[] = $this->cpf_single_epos_prices[ $element_uniqueid ] = array(
											"uniqueid"         => $element_uniqueid,
											"required"         => $is_required,
											"element"          => $element_no_in_section,
											'section_uniqueid' => $_sections_uniqid,
											"minall"           => floatval( $_min_price10 ),
											"min"              => floatval( $_min_price0 ),
											"max"              => floatval( $_max_price ),
											"clogic"           => $this->get_builder_element( $_prefix . 'clogic', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
											"section_clogic"   => $global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]['sections_clogic'],
											"logic"            => $this->get_builder_element( $_prefix . 'logic', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
											"section_logic"    => $global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]['sections_logic'],
										);
									}
									if ( $_min_price !== FALSE ) {
										$_min_price = wc_format_localized_price( $_min_price );
									}
									if ( $_max_price !== FALSE ) {
										$_max_price = wc_format_localized_price( $_max_price );
									}

									// Fix for getting right results for dates even if the users enters wrong format 
									$format = $this->get_builder_element( $_prefix . 'format', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid );
									switch ( $format ) {
										case "0":
											$date_format = 'd/m/Y';
											$sep         = "/";
											break;
										case "1":
											$date_format = 'm/d/Y';
											$sep         = "/";
											break;
										case "2":
											$date_format = 'd.m.Y';
											$sep         = ".";
											break;
										case "3":
											$date_format = 'm.d.Y';
											$sep         = ".";
											break;
										case "4":
											$date_format = 'd-m-Y';
											$sep         = "-";
											break;
										case "5":
											$date_format = 'm-d-Y';
											$sep         = "-";
											break;

										case "6":
											$date_format = 'Y/m/d';
											$sep         = "/";
											break;
										case "7":
											$date_format = 'Y/d/m';
											$sep         = "/";
											break;
										case "8":
											$date_format = 'Y.m.d';
											$sep         = ".";
											break;
										case "9":
											$date_format = 'Y.d.m';
											$sep         = ".";
											break;
										case "10":
											$date_format = 'Y-m-d';
											$sep         = "-";
											break;
										case "11":
											$date_format = 'Y-d-m';
											$sep         = "-";
											break;
									}
									$disabled_dates = $this->get_builder_element( $_prefix . 'disabled_dates', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid );
									if ( $disabled_dates ) {
										$disabled_dates = explode( ",", $disabled_dates );
										foreach ( $disabled_dates as $key => $value ) {
											if ( ! $value ) {
												continue;
											}
											$value = str_replace( ".", "-", $value );
											$value = str_replace( "/", "-", $value );
											$value = explode( "-", $value );
											if ( count( $value ) !== 3 ) {
												continue;
											}
											switch ( $format ) {
												case "0":
												case "2":
												case "4":
													$value = $value[2] . "-" . $value[1] . "-" . $value[0];
													break;
												case "1":
												case "3":
												case "5":
													$value = $value[2] . "-" . $value[0] . "-" . $value[1];
													break;
											}
											$value_to_date = date_create( $value );
											if ( ! $value_to_date ) {
												continue;
											}
											$value                  = date_format( $value_to_date, $date_format );
											$disabled_dates[ $key ] = $value;
										}
										$disabled_dates = implode( ",", $disabled_dates );

									}
									$enabled_only_dates = $this->get_builder_element( $_prefix . 'enabled_only_dates', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid );
									if ( $enabled_only_dates ) {
										$enabled_only_dates = explode( ",", $enabled_only_dates );
										foreach ( $enabled_only_dates as $key => $value ) {
											if ( ! $value ) {
												continue;
											}
											$value = str_replace( ".", "-", $value );
											$value = str_replace( "/", "-", $value );
											$value = explode( "-", $value );
											if ( count( $value ) !== 3 ) {
												continue;
											}
											switch ( $format ) {
												case "0":
												case "2":
												case "4":
													$value = $value[2] . "-" . $value[1] . "-" . $value[0];
													break;
												case "1":
												case "3":
												case "5":
													$value = $value[2] . "-" . $value[0] . "-" . $value[1];
													break;
											}
											$value_to_date = date_create( $value );
											if ( ! $value_to_date ) {
												continue;
											}
											$value                      = date_format( $value_to_date, $date_format );
											$enabled_only_dates[ $key ] = $value;
										}
										$enabled_only_dates = implode( ",", $enabled_only_dates );
									}

									if ($is_enabled){
										$this->current_option_features[] = $current_element;
									}

									if ( $current_element != "header" && $current_element != "divider" ) {
										if ( $current_element == "variations" ) {
											$variation_element_id = $this->cpf_single_variation_element_id[ $element_uniqueid ] = $this->get_builder_element( $_prefix . 'uniqid', $builder, $current_builder, $current_counter, THEMECOMPLETE_EPO_HELPER()->tm_uniqid(), $wpml_element_fields, $current_element, "", $element_uniqueid );
											$variation_section_id = $this->cpf_single_variation_section_id[ $element_uniqueid ] = $global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]['sections_uniqid'];
										}

										$product_epos_uniqids[] = $element_uniqueid;
										if ( in_array( $_new_type, array( 'select', 'radio', 'checkbox' ) ) ) {
											$product_epos_choices[ $element_uniqueid ] = array_keys( $_rules_type );
										}

										$_extra_multiple_choices = ( $_extra_multiple_choices !== FALSE ) ? $_extra_multiple_choices : array();

										$global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]['elements'][] = $this->cpf_single[ $element_uniqueid ] =
											array_merge(
												THEMECOMPLETE_EPO_BUILDER()->get_custom_properties( $builder, $_prefix, $_counter, $_elements, $k0, $current_builder, $current_counter, $wpml_element_fields, $current_element ),
												$_extra_multiple_choices,
												array(
													'_'             => THEMECOMPLETE_EPO_BUILDER()->get_default_properties( $builder, $_prefix, $_counter, $_elements, $k0 ),
													'internal_name' => $this->get_builder_element( $_prefix . 'internal_name', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
													'builder'       => ( isset( $wpml_is_original_product ) && empty( $wpml_is_original_product ) ) ? $current_builder : $builder,
													'section'       => $_sections_uniqid,
													'type'          => $_new_type,
													'size'          => $_div_size[ $k0 ],

													'include_tax_for_fee_price_type' => $this->get_builder_element( $_prefix . 'include_tax_for_fee_price_type', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
													'tax_class_for_fee_price_type'   => $this->get_builder_element( $_prefix . 'tax_class_for_fee_price_type', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),

													'hide_element_label_in_cart'     => $this->get_builder_element( $_prefix . 'hide_element_label_in_cart', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
													'hide_element_value_in_cart'     => $this->get_builder_element( $_prefix . 'hide_element_value_in_cart', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
													'hide_element_label_in_order'    => $this->get_builder_element( $_prefix . 'hide_element_label_in_order', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
													'hide_element_value_in_order'    => $this->get_builder_element( $_prefix . 'hide_element_value_in_order', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
													'hide_element_label_in_floatbox' => $this->get_builder_element( $_prefix . 'hide_element_label_in_floatbox', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
													'hide_element_value_in_floatbox' => $this->get_builder_element( $_prefix . 'hide_element_value_in_floatbox', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),

													'enabled'       => $is_enabled,
													'required'      => $is_required,
													'use_images'    => isset( $_use_images ) ? $_use_images : "",
													'use_colors'    => isset( $_use_colors ) ? $_use_colors : "",
													'use_lightbox'  => $_use_lightbox,
													'use_url'       => $this->get_builder_element( $_prefix . 'use_url', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
													'items_per_row' => $this->get_builder_element( $_prefix . 'items_per_row', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),

													'items_per_row_r' => array(
														"desktop"        => $this->get_builder_element( $_prefix . 'items_per_row', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
														"tablets_galaxy" => $this->get_builder_element( $_prefix . 'items_per_row_tablets_galaxy', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
														"tablets"        => $this->get_builder_element( $_prefix . 'items_per_row_tablets', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
														"tablets_small"  => $this->get_builder_element( $_prefix . 'items_per_row_tablets_small', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
														"iphone6_plus"   => $this->get_builder_element( $_prefix . 'items_per_row_iphone6_plus', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
														"iphone6"        => $this->get_builder_element( $_prefix . 'items_per_row_iphone6', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
														"galaxy"         => $this->get_builder_element( $_prefix . 'items_per_row_samsung_galaxy', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
														"iphone5"        => $this->get_builder_element( $_prefix . 'items_per_row_iphone5', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
														"smartphones"    => $this->get_builder_element( $_prefix . 'items_per_row_smartphones', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
													),

													'label_size'              => $this->get_builder_element( $_prefix . 'header_size', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
													'label'                   => $this->get_builder_element( $_prefix . 'header_title', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "wc_epo_label", $element_uniqueid ),
													'label_position'          => $this->get_builder_element( $_prefix . 'header_title_position', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
													'label_color'             => $this->get_builder_element( $_prefix . 'header_title_color', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
													'description'             => $this->get_builder_element( $_prefix . 'header_subtitle', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
													'description_position'    => $this->get_builder_element( $_prefix . 'header_subtitle_position', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
													'description_color'       => $this->get_builder_element( $_prefix . 'header_subtitle_color', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
													'divider_type'            => $this->get_builder_element( $_prefix . 'divider_type', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
													'placeholder'             => $this->get_builder_element( $_prefix . 'placeholder', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
													'min_chars'               => $this->get_builder_element( $_prefix . 'min_chars', $builder, $current_builder, $current_counter, FALSE, $wpml_element_fields, $current_element, "wc_epo_global_min_chars", $element_uniqueid ),
													'max_chars'               => $this->get_builder_element( $_prefix . 'max_chars', $builder, $current_builder, $current_counter, FALSE, $wpml_element_fields, $current_element, "wc_epo_global_max_chars", $element_uniqueid ),
													'hide_amount'             => $this->get_builder_element( $_prefix . 'hide_amount', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
													'text_before_price'       => $this->get_builder_element( $_prefix . 'text_before_price', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
													'text_after_price'        => $this->get_builder_element( $_prefix . 'text_after_price', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
													'options'                 => $_options,
													'options_all'             => $_options_all,
													'min_price'               => $_min_price,
													'max_price'               => $_max_price,
													'rules'                   => $_rules,
													'price_rules'             => $_regular_price,
													'rules_filtered'          => $_rules_filtered,
													'price_rules_filtered'    => $_regular_price_filtered,
													'original_rules_filtered' => $_original_regular_price_filtered,
													'price_rules_type'        => $_regular_price_type,
													'rules_type'              => $_rules_type,
													'currencies'              => $_regular_currencies,
													'price_per_currencies'    => $price_per_currencies,
													'images'                  => isset( $_images ) ? $_images : "",
													'imagesc'                 => isset( $_imagesc ) ? $_imagesc : "",
													'imagesp'                 => isset( $_imagesp ) ? $_imagesp : "",
													'imagesl'                 => isset( $_imagesl ) ? $_imagesl : "",
													'color'                   => isset( $_color ) ? $_color : "",
													'url'                     => isset( $_url ) ? $_url : "",

													'cdescription'           => ( $_description !== FALSE ) ? $_description : "",
													'extra_multiple_choices' => ( $_extra_multiple_choices !== FALSE ) ? $_extra_multiple_choices : array(),
													'limit'                  => $this->get_builder_element( $_prefix . 'limit_choices', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
													'exactlimit'             => $this->get_builder_element( $_prefix . 'exactlimit_choices', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
													'minimumlimit'           => $this->get_builder_element( $_prefix . 'minimumlimit_choices', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
													'clear_options'          => $this->get_builder_element( $_prefix . 'clear_options', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
													'option_values_all'      => isset( $_values_c ) ? $_values_c : array(),
													'option_values'          => isset( $_values_ce ) ? $_values_ce : array(),
													'button_type'            => $this->get_builder_element( $_prefix . 'button_type', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
													'uniqid'                 => $element_uniqueid,
													'clogic'                 => $this->get_builder_element( $_prefix . 'clogic', $builder, $current_builder, $current_counter, FALSE, $wpml_element_fields, $current_element, "", $element_uniqueid ),
													'logic'                  => $this->get_builder_element( $_prefix . 'logic', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
													'format'                 => $format,
													'start_year'             => $this->get_builder_element( $_prefix . 'start_year', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
													'end_year'               => $this->get_builder_element( $_prefix . 'end_year', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
													'min_date'               => $this->get_builder_element( $_prefix . 'min_date', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
													'max_date'               => $this->get_builder_element( $_prefix . 'max_date', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
													'disabled_dates'         => $disabled_dates,
													'enabled_only_dates'     => $enabled_only_dates,
													'disabled_weekdays'      => $this->get_builder_element( $_prefix . 'disabled_weekdays', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
													'disabled_months'        => $this->get_builder_element( $_prefix . 'disabled_months', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),

													'time_format'        => $this->get_builder_element( $_prefix . 'time_format', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
													'custom_time_format' => $this->get_builder_element( $_prefix . 'custom_time_format', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
													'min_time'           => $this->get_builder_element( $_prefix . 'min_time', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
													'max_time'           => $this->get_builder_element( $_prefix . 'max_time', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
													'showhour'           => $this->get_builder_element( $_prefix . 'showhour', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
													'showminute'         => $this->get_builder_element( $_prefix . 'showminute', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
													'showsecond'         => $this->get_builder_element( $_prefix . 'showsecond', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
													'tranlation_hour'    => $this->get_builder_element( $_prefix . 'tranlation_hour', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
													'tranlation_minute'  => $this->get_builder_element( $_prefix . 'tranlation_minute', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
													'tranlation_second'  => $this->get_builder_element( $_prefix . 'tranlation_second', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),

													'theme'          => $this->get_builder_element( $_prefix . 'theme', $builder, $current_builder, $current_counter, "epo", $wpml_element_fields, $current_element, "", $element_uniqueid ),
													'theme_size'     => $this->get_builder_element( $_prefix . 'theme_size', $builder, $current_builder, $current_counter, "medium", $wpml_element_fields, $current_element, "", $element_uniqueid ),
													'theme_position' => $this->get_builder_element( $_prefix . 'theme_position', $builder, $current_builder, $current_counter, "normal", $wpml_element_fields, $current_element, "", $element_uniqueid ),

													'tranlation_day'   => $this->get_builder_element( $_prefix . 'tranlation_day', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
													'tranlation_month' => $this->get_builder_element( $_prefix . 'tranlation_month', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
													'tranlation_year'  => $this->get_builder_element( $_prefix . 'tranlation_year', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
													"default_value"    => $default_value,

													'is_cart_fee'           => $_is_price_fee === "1",
													'is_cart_fee_multiple'  => isset( $_fee ) ? $_fee : array(),
													'class'                 => $this->get_builder_element( $_prefix . 'class', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
													'container_id'          => $this->get_builder_element( $_prefix . 'container_id', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
													'swatchmode'            => $this->get_builder_element( $_prefix . 'swatchmode', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
													'changes_product_image' => isset( $_changes_product_image ) ? $_changes_product_image : "",
													'min'                   => $this->get_builder_element( $_prefix . 'min', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
													'max'                   => $this->get_builder_element( $_prefix . 'max', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
													'freechars'             => $this->get_builder_element( $_prefix . 'freechars', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
													'step'                  => $this->get_builder_element( $_prefix . 'step', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
													'pips'                  => $this->get_builder_element( $_prefix . 'pips', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
													'noofpips'              => $this->get_builder_element( $_prefix . 'noofpips', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
													'show_picker_value'     => $this->get_builder_element( $_prefix . 'show_picker_value', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),

													'quantity'               => $this->get_builder_element( $_prefix . 'quantity', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
													'quantity_min'           => $this->get_builder_element( $_prefix . 'quantity_min', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
													'quantity_max'           => $this->get_builder_element( $_prefix . 'quantity_max', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
													'quantity_step'          => $this->get_builder_element( $_prefix . 'quantity_step', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
													'quantity_default_value' => $this->get_builder_element( $_prefix . 'quantity_default_value', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),

													'mode'                 => $this->get_builder_element( $_prefix . 'mode', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
													'layout_mode'          => $this->get_builder_element( $_prefix . 'layout_mode', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
													'categoryids'          => $this->get_builder_element( $_prefix . 'categoryids', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
													'productids'           => $this->get_builder_element( $_prefix . 'productids', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
													'priced_individually'  => $this->get_builder_element( $_prefix . 'priced_individually', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
													'shipped_individually' => $this->get_builder_element( $_prefix . 'shipped_individually', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
													'maintain_weight'      => $this->get_builder_element( $_prefix . 'maintain_weight', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
													'discount'             => $this->get_builder_element( $_prefix . 'discount', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
													'discount_type'        => $this->get_builder_element( $_prefix . 'discount_type', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),

													'validation1' => $this->get_builder_element( $_prefix . 'validation1', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
												) );

									} elseif ( $current_element == "header" ) {

										$product_epos_uniqids[] = $element_uniqueid;

										$global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]['elements'][] = $this->cpf_single[ $element_uniqueid ] = array(
											'internal_name'         => $this->get_builder_element( $_prefix . 'internal_name', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
											'section'               => $_sections_uniqid,
											'type'                  => $_new_type,
											'size'                  => $_div_size[ $k0 ],
											'required'              => "",
											'enabled'               => $is_enabled,
											'use_images'            => "",
											'use_colors'            => "",
											'use_url'               => "",
											'items_per_row'         => "",
											'label_size'            => $this->get_builder_element( $_prefix . 'header_size', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
											'label'                 => $this->get_builder_element( $_prefix . 'header_title', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "wc_epo_label", $element_uniqueid ),
											'label_position'        => $this->get_builder_element( $_prefix . 'header_title_position', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
											'label_color'           => $this->get_builder_element( $_prefix . 'header_title_color', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
											'description'           => $this->get_builder_element( $_prefix . 'header_subtitle', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
											'description_color'     => $this->get_builder_element( $_prefix . 'header_subtitle_color', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
											'description_position'  => $this->get_builder_element( $_prefix . 'header_subtitle_position', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
											'divider_type'          => "",
											'placeholder'           => "",
											'max_chars'             => "",
											'hide_amount'           => "",
											"options"               => $_options,
											'options_all'           => $_options_all,
											'min_price'             => $_min_price,
											'max_price'             => $_max_price,
											'rules'                 => $_rules,
											'price_rules'           => $_regular_price,
											'rules_filtered'        => $_rules_filtered,
											'price_rules_filtered'  => $_regular_price_filtered,
											'price_rules_type'      => $_regular_price_type,
											'rules_type'            => $_rules_type,
											'images'                => "",
											'limit'                 => "",
											'exactlimit'            => "",
											'minimumlimit'          => "",
											'option_values'         => array(),
											'button_type'           => '',
											'class'                 => $this->get_builder_element( 'header_class', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
											'uniqid'                => $this->get_builder_element( 'header_uniqid', $builder, $current_builder, $current_counter, THEMECOMPLETE_EPO_HELPER()->tm_uniqid(), $wpml_element_fields, $current_element, "", $element_uniqueid ),
											'clogic'                => $this->get_builder_element( 'header_clogic', $builder, $current_builder, $current_counter, FALSE, $wpml_element_fields, $current_element, "", $element_uniqueid ),
											'logic'                 => $this->get_builder_element( 'header_logic', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
											'format'                => '',
											'start_year'            => '',
											'end_year'              => '',
											'tranlation_day'        => '',
											'tranlation_month'      => '',
											'tranlation_year'       => '',
											'swatchmode'            => "",
											'changes_product_image' => "",
											'min'                   => "",
											'max'                   => "",
											'step'                  => "",
											'pips'                  => "",

										);

									} elseif ( $current_element == "divider" ) {

										$product_epos_uniqids[] = $element_uniqueid;

										$global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]['elements'][] = $this->cpf_single[ $element_uniqueid ] = array(
											'internal_name'         => $this->get_builder_element( $_prefix . 'internal_name', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
											'section'               => $_sections_uniqid,
											'type'                  => $_new_type,
											'size'                  => $_div_size[ $k0 ],
											'required'              => "",
											'enabled'               => $is_enabled,
											'use_images'            => "",
											'use_colors'            => "",
											'use_url'               => "",
											'items_per_row'         => "",
											'label_size'            => "",
											'label'                 => "",
											'label_color'           => "",
											'label_position'        => "",
											'description'           => "",
											'description_color'     => "",
											'divider_type'          => $this->get_builder_element( $_prefix . 'divider_type', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
											'placeholder'           => "",
											'max_chars'             => "",
											'hide_amount'           => "",
											"options"               => $_options,
											'options_all'           => $_options_all,
											'min_price'             => $_min_price,
											'max_price'             => $_max_price,
											'rules'                 => $_rules,
											'price_rules'           => $_regular_price,
											'rules_filtered'        => $_rules_filtered,
											'price_rules_filtered'  => $_regular_price_filtered,
											'price_rules_type'      => $_regular_price_type,
											'rules_type'            => $_rules_type,
											'images'                => "",
											'limit'                 => "",
											'exactlimit'            => "",
											'minimumlimit'          => "",
											'option_values'         => array(),
											'button_type'           => '',
											'class'                 => $this->get_builder_element( 'divider_class', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
											'uniqid'                => $this->get_builder_element( 'divider_uniqid', $builder, $current_builder, $current_counter, THEMECOMPLETE_EPO_HELPER()->tm_uniqid(), $wpml_element_fields, $current_element, "", $element_uniqueid ),
											'clogic'                => $this->get_builder_element( 'divider_clogic', $builder, $current_builder, $current_counter, FALSE, $wpml_element_fields, $current_element, "", $element_uniqueid ),
											'logic'                 => $this->get_builder_element( 'divider_logic', $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element, "", $element_uniqueid ),
											'format'                => '',
											'start_year'            => '',
											'end_year'              => '',
											'tranlation_day'        => '',
											'tranlation_month'      => '',
											'tranlation_year'       => '',
											'swatchmode'            => "",
											'changes_product_image' => "",
											'min'                   => "",
											'max'                   => "",
											'step'                  => "",
											'pips'                  => "",
										);

									}
								}
							}

							$_helper_counter = intval( $_helper_counter + intval( $_sections[ $_s ] ) );

							if ( $post_id != $original_product_id ) {

								if ( is_array( $tm_meta_product_ids ) ) {
									foreach ( $variations_for_conditional_logic as $variation_id ) {
										if ( in_array( $variation_id, $tm_meta_product_ids ) ) {
											$extra_logic            = array();
											$extra_logic["section"] = $_sections_uniqid;
											$extra_logic["toggle"]  = "show";
											$extra_logic["what"]    = "any";
											$extra_logic["rules"]   = array();
											$rule                   = array();
											$rule["section"]        = $variation_section_id; // this will be addeed correctly later
											$rule["element"]        = 0;
											$rule["operator"]       = 'is';
											$rule["value"]          = floatval( THEMECOMPLETE_EPO_WPML()->get_current_id( $variation_id, 'product', NULL, 'product_variation' ) );
											$extra_logic["rules"][] = $rule;

											$extra_section_logic[] = array(
												"priority"    => $priority,
												"tmcp_id"     => $tmcp_id,
												"_s"          => $_s,
												"extra_logic" => $extra_logic,
											);

										}

									}
								}

								if ( is_array( $tm_meta_product_exclude_ids ) ) {
									foreach ( $variations_for_conditional_logic as $variation_id ) {
										if ( in_array( $variation_id, $tm_meta_product_exclude_ids ) ) {
											$extra_hide_logic            = array();
											$extra_hide_logic["section"] = $_sections_uniqid;
											$extra_hide_logic["toggle"]  = "hide";
											$extra_hide_logic["what"]    = "any";
											$extra_hide_logic["rules"]   = array();
											$rule                        = array();
											$rule["section"]             = $variation_section_id; // this will be addeed correctly later
											$rule["element"]             = 0;
											$rule["operator"]            = 'is';
											$rule["value"]               = floatval( THEMECOMPLETE_EPO_WPML()->get_current_id( $variation_id, 'product', NULL, 'product_variation' ) );
											$extra_hide_logic["rules"][] = $rule;

											$extra_section_hide_logic[] = array(
												"priority"         => $priority,
												"tmcp_id"          => $tmcp_id,
												"_s"               => $_s,
												"extra_hide_logic" => $extra_hide_logic,
											);

										}

									}
								}

							}

						}
					}
				}
			}
		}

		if ( $variation_section_id ) {
			foreach ( $extra_section_logic as $section_logic ) {
				$section_logic["extra_logic"]["rules"][0]["section"] = $variation_section_id;
				$priority                                            = $section_logic["priority"];
				$tmcp_id                                             = $section_logic["tmcp_id"];
				$_s                                                  = $section_logic["_s"];

				if ( ! empty( $global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]["sections_logic"] ) ) {
					// If the secion already has logic that logic must be changed
					// to ANY in order to accomodate the adding the variations
					// This means that if the current logic is set to ALL then
					// you will get wrong results. This is a limiations of the 
					// current conditional logic system.
					$current_section_logic = json_decode( stripslashes_deep( $global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]["sections_clogic"] ) );
					if ( is_object( $current_section_logic ) ) {
						$current_section_logic->what                                                = "any";
						$current_section_logic->rules[]                                             = $section_logic["extra_logic"]["rules"][0];
						$global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]["sections_clogic"] = wp_json_encode( $current_section_logic );
						$global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]["sections_logic"]  = "1";
					}
				} else {
					$global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]["sections_clogic"] = wp_json_encode( $section_logic["extra_logic"] );
					$global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]["sections_logic"]  = "1";
				}
			}

			foreach ( $extra_section_hide_logic as $section_logic ) {
				$section_logic["extra_hide_logic"]["rules"][0]["section"] = $variation_section_id;
				$priority                                                 = $section_logic["priority"];
				$tmcp_id                                                  = $section_logic["tmcp_id"];
				$_s                                                       = $section_logic["_s"];

				if ( ! empty( $global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]["sections_logic"] ) ) {
					// If the secion already has logic that logic must be changed
					// to ANY in order to accomodate the adding the variations
					// This means that if the current logic is set to ALL then
					// you will get wrong results. This is a limiations of the 
					// current conditional logic system.
					$current_section_logic = json_decode( stripslashes_deep( $global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]["sections_clogic"] ) );
					if ( is_object( $current_section_logic ) ) {
						$current_section_logic->what                                                = "any";
						$current_section_logic->rules[]                                             = $section_logic["extra_hide_logic"]["rules"][0];
						$global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]["sections_clogic"] = wp_json_encode( $current_section_logic );
						$global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]["sections_logic"]  = "1";
					}
				} else {
					$global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]["sections_clogic"] = wp_json_encode( $section_logic["extra_hide_logic"] );
					$global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]["sections_logic"]  = "1";
				}
			}

			if ( empty( $extra_section_logic )
			     && count( $global_epos ) == 1
			     && isset( $global_epos[1000] )
			     && isset( $global_epos[1000][ $post_id ] )
			     && isset( $global_epos[1000][ $post_id ]['sections'] )
			     && count( $global_epos[1000][ $post_id ]['sections'] ) == 1
			     && isset( $global_epos[1000][ $post_id ]['sections'][0] )
			     && isset( $global_epos[1000][ $post_id ]['sections'][0]['elements'] )
			     && isset( $global_epos[1000][ $post_id ]['sections'][0]['total_elements'] )
			     && $global_epos[1000][ $post_id ]['sections'][0]['total_elements'] == "1"
			     && count( $global_epos[1000][ $post_id ]['sections'][0]['elements'] ) == 1
			     && isset( $global_epos[1000][ $post_id ]['sections'][0]['elements'][0] )
			     && isset( $global_epos[1000][ $post_id ]['sections'][0]['elements'][0]['type'] )
			     && $global_epos[1000][ $post_id ]['sections'][0]['elements'][0]['type'] == 'variations'
			     && isset( $global_epos[1000][ $post_id ]['sections'][0]['elements'][0]['builder'] )
			     && isset( $global_epos[1000][ $post_id ]['sections'][0]['elements'][0]['builder']['variations_disabled'] )
			     && $global_epos[1000][ $post_id ]['sections'][0]['elements'][0]['builder']['variations_disabled'] == '1'
			) {
				$global_epos = array();
			}

		}

		$variations_disabled       = FALSE;
		$isset_variations_disabled = ( isset( $global_epos[1000] )
		                               && isset( $global_epos[1000] )
		                               && isset( $global_epos[1000][ $post_id ] )
		                               && isset( $global_epos[1000][ $post_id ]['sections'] )
		                               && isset( $global_epos[1000][ $post_id ]['sections'][0] )
		                               && isset( $global_epos[1000][ $post_id ]['sections'][0]['elements'] )
		                               && isset( $global_epos[1000][ $post_id ]['sections'][0]['elements'][0] )
		                               && isset( $global_epos[1000][ $post_id ]['sections'][0]['elements'][0]['builder'] )
		                               && isset( $global_epos[1000][ $post_id ]['sections'][0]['elements'][0]['builder']['variations_disabled'] ) );

		if ( $isset_variations_disabled ) {
			$variations_disabled = ( isset( $global_epos[1000] )
			                         && isset( $global_epos[1000] )
			                         && isset( $global_epos[1000][ $post_id ] )
			                         && isset( $global_epos[1000][ $post_id ]['sections'] )
			                         && isset( $global_epos[1000][ $post_id ]['sections'][0] )
			                         && isset( $global_epos[1000][ $post_id ]['sections'][0]['elements'] )
			                         && isset( $global_epos[1000][ $post_id ]['sections'][0]['elements'][0] )
			                         && isset( $global_epos[1000][ $post_id ]['sections'][0]['elements'][0]['builder'] )
			                         && isset( $global_epos[1000][ $post_id ]['sections'][0]['elements'][0]['builder']['variations_disabled'] )
			                         && $global_epos[1000][ $post_id ]['sections'][0]['elements'][0]['builder']['variations_disabled'] == '1' );
		}

		return array(
			'global'               => $global_epos,
			'price'                => $epos_prices,
			'variation_element_id' => $variation_element_id,
			'variation_section_id' => $variation_section_id,
			'variations_disabled'  => $variations_disabled,
			'raw_epos'             => $raw_epos,
			'product_epos_uniqids' => $product_epos_uniqids,
			'product_epos_choices' => $product_epos_choices,
		);
	}

	/**
	 * Translate $attributes to post names
	 *
	 * @param array  $attributes
	 * @param array  $type
	 * @param int    $section
	 * @param string $form_prefix should be passed with _ if not empty
	 * @param string $name_prefix
	 *
	 * @return array
	 */
	public function get_post_names( $attributes, $type, $section, $form_prefix = "", $name_prefix = "" ) {

		$fields = array();
		$loop   = 0;

		if ( ! empty( $attributes ) ) {

			foreach ( $attributes as $key => $attribute ) {
				$name_inc = "";
				if ( ! empty( $this->tm_builder_elements[ $type ]["post_name_prefix"] ) ) {
					if ( $this->tm_builder_elements[ $type ]["type"] == "multiple" || $this->tm_builder_elements[ $type ]["type"] == "multiplesingle" ) {
						$name_inc = "tmcp_" . $name_prefix . $this->tm_builder_elements[ $type ]["post_name_prefix"] . "_" . $section . $form_prefix;
					} elseif ( $this->tm_builder_elements[ $type ]["type"] == "multipleall" ) {
						$name_inc = "tmcp_" . $name_prefix . $this->tm_builder_elements[ $type ]["post_name_prefix"] . "_" . $section . "_" . $loop . $form_prefix;
					}
				}
				$fields[] = $name_inc;
				$loop ++;
			}

		} else {
			if ( ! empty( $this->tm_builder_elements[ $type ]["type"] ) && ! empty( $this->tm_builder_elements[ $type ]["post_name_prefix"] ) ) {
				$name_inc = "tmcp_" . $name_prefix . $this->tm_builder_elements[ $type ]["post_name_prefix"] . "_" . $section . $form_prefix;
			}

			if ( ! empty( $name_inc ) ) {
				$fields[] = $name_inc;
			}

		}

		return $fields;

	}

	/**
	 * Get posted variations id
	 *
	 * @param string $form_prefix
	 *
	 * @return null
	 */
	public function get_posted_variation_id( $form_prefix = "" ) {

		$variation_id = NULL;
		if ( isset( $_POST[ 'variation_id' . $form_prefix ] ) ) {
			$variation_id = $_POST[ 'variation_id' . $form_prefix ];
		}

		return $variation_id;

	}

	/**
	 * Append name_inc functions (required for condition logic to check if an element is visible)
	 *
	 * @param int    $post_id
	 * @param array  $global_epos
	 * @param array  $product_epos
	 * @param string $form_prefix
	 * @param string $add_identifier
	 *
	 * @return array
	 */
	public function tm_fill_element_names( $post_id = 0, $global_epos = array(), $product_epos = array(), $form_prefix = "", $add_identifier = "" ) {

		$global_price_array = $global_epos;
		$local_price_array  = $product_epos;

		$global_prices = array( 'before' => array(), 'after' => array() );
		foreach ( $global_price_array as $priority => $priorities ) {
			foreach ( $priorities as $pid => $field ) {
				if ( isset( $field['sections'] ) && is_array( $field['sections'] ) ) {
					foreach ( $field['sections'] as $section_id => $section ) {
						if ( isset( $section['sections_placement'] ) ) {
							$global_prices[ $section['sections_placement'] ][ $priority ][ $pid ]['sections'][ $section_id ] = $section;
						}
					}
				}
			}
		}
		$unit_counter    = 0;
		$field_counter   = 0;
		$element_counter = 0;

		// global options before local
		foreach ( $global_prices['before'] as $priority => $priorities ) {
			foreach ( $priorities as $pid => $field ) {
				$args    = array(
					'priority'        => $priority,
					'pid'             => $pid,
					'unit_counter'    => $unit_counter,
					'field_counter'   => $field_counter,
					'element_counter' => $element_counter,
				);
				$_return = $this->fill_builder_display( $global_epos, $field, 'before', $args, $form_prefix, $add_identifier );

				$global_epos     = $_return['global_epos'];
				$unit_counter    = $_return['unit_counter'];
				$field_counter   = $_return['field_counter'];
				$element_counter = $_return['element_counter'];

			}
		}

		// normal (local) options
		if ( is_array( $local_price_array ) && sizeof( $local_price_array ) > 0 ) {
			$attributes = themecomplete_get_attributes( $post_id );
			if ( is_array( $attributes ) && count( $attributes ) > 0 ) {
				foreach ( $local_price_array as $field ) {
					if ( isset( $field['name'] ) && isset( $attributes[ $field['name'] ] ) && ! $attributes[ $field['name'] ]['is_variation'] ) {
						$attribute     = $attributes[ $field['name'] ];
						$field_counter = 0;
						if ( $attribute['is_taxonomy'] ) {
							switch ( $field['type'] ) {
								case "select":
									$element_counter ++;
									break;
								case "radio":
								case "checkbox":
									$element_counter ++;
									break;
							}
						} else {
							switch ( $field['type'] ) {
								case "select":
									$element_counter ++;
									break;
								case "radio":
								case "checkbox":
									$element_counter ++;
									break;
							}
						}
						$unit_counter ++;
					}
				}
			}
		}

		// global options after normal (local)
		foreach ( $global_prices['after'] as $priority => $priorities ) {
			foreach ( $priorities as $pid => $field ) {
				$args    = array(
					'priority'        => $priority,
					'pid'             => $pid,
					'unit_counter'    => $unit_counter,
					'field_counter'   => $field_counter,
					'element_counter' => $element_counter,
				);
				$_return = $this->fill_builder_display( $global_epos, $field, 'after', $args, $form_prefix, $add_identifier );

				$global_epos     = $_return['global_epos'];
				$unit_counter    = $_return['unit_counter'];
				$field_counter   = $_return['field_counter'];
				$element_counter = $_return['element_counter'];

			}
		}

		return $global_epos;

	}

	/**
	 * Generates correct html names for the builder fields
	 *
	 * @param array  $global_epos
	 * @param array  $field
	 * @param string $where
	 * @param array  $args
	 * @param string $form_prefix shoud be passed with _ if not empty
	 * @param string $add_identifier
	 *
	 * @return array
	 */
	public function fill_builder_display( $global_epos, $field, $where, $args, $form_prefix = "", $add_identifier = "" ) {

		$priority        = $args['priority'];
		$pid             = $args['pid'];
		$unit_counter    = $args['unit_counter'];
		$field_counter   = $args['field_counter'];
		$element_counter = $args['element_counter'];

		$element_type_counter = array();

		if ( isset( $field['sections'] ) && is_array( $field['sections'] ) ) {
			foreach ( $field['sections'] as $_s => $section ) {
				if ( ! isset( $section['sections_placement'] ) || $section['sections_placement'] != $where ) {
					continue;
				}
				if ( isset( $section['elements'] ) && is_array( $section['elements'] ) ) {
					foreach ( $section['elements'] as $arr_element_counter => $element ) {

						$cart_fee_name = $this->cart_fee_name;
						$field_counter = 0;

						if ( ! empty( $add_identifier ) ) {
							$global_epos[ $priority ][ $pid ]['sections'][ $_s ]['elements'][ $arr_element_counter ]['add_identifier'] = $add_identifier;
						}
						if ( isset( $this->tm_builder_elements[ $element['type'] ] ) && $this->tm_builder_elements[ $element['type'] ]["is_post"] == "post" ) {

							if ( $this->tm_builder_elements[ $element['type'] ]["type"] == "multipleall" || $this->tm_builder_elements[ $element['type'] ]["type"] == "multiple" ) {

								$choice_counter = 0;
										
								if (!isset($element_type_counter[ $element['type'] ])){
									$element_type_counter[ $element['type'] ] = 0;
								}

								foreach ( $element['options'] as $value => $label ) {

									if ( $this->tm_builder_elements[ $element['type'] ]["type"] == "multipleall" ) {
										$name_inc = $this->tm_builder_elements[ $element['type'] ]["post_name_prefix"] . "_" . $element_counter . "_" . $field_counter . ( ( $form_prefix !== "" ) ? "_" . str_replace( "_", "", $form_prefix ) : "" );
									} else {
										$name_inc = $this->tm_builder_elements[ $element['type'] ]["post_name_prefix"] . "_" . $element_counter . ( ( $form_prefix !== "" ) ? "_" . str_replace( "_", "", $form_prefix ) : "" );
									}

									$base_name_inc = $name_inc;

									if ( $element['type'] === "checkbox" ) {
										$is_cart_fee = ! empty( $element['is_cart_fee_multiple'][ $field_counter ] );
									} else {
										$is_cart_fee = ! empty( $element['is_cart_fee'] );
									}
									if ( $is_cart_fee ) {
										$name_inc = $cart_fee_name . $name_inc;
									}

									$name_inc = apply_filters( 'wc_epo_name_inc', $name_inc, $base_name_inc, $element, $value, $choice_counter, $element_type_counter[ $element['type'] ] );

									$name_inc                                                                                              = 'tmcp_' . $name_inc . ( ( $form_prefix !== "" ) ? "_" . str_replace( "_", "", $form_prefix ) : "" );
									$global_epos[ $priority ][ $pid ]['sections'][ $_s ]['elements'][ $arr_element_counter ]['name_inc'][] = $name_inc;

									$global_epos[ $priority ][ $pid ]['sections'][ $_s ]['elements'][ $arr_element_counter ]['is_cart_fee_multiple'][ $field_counter ] = $is_cart_fee;

									$global_epos = apply_filters( 'global_epos_fill_builder_display', $global_epos, $priority, $pid, $_s, $arr_element_counter, $element, $value, $choice_counter, $element_type_counter[ $element['type'] ] );

									$choice_counter ++;

									$field_counter ++;

								}

							} elseif ( $this->tm_builder_elements[ $element['type'] ]["type"] == "single" || $this->tm_builder_elements[ $element['type'] ]["type"] == "multipleallsingle" || $this->tm_builder_elements[ $element['type'] ]["type"] == "multiplesingle" ) {

								$name_inc      = $this->tm_builder_elements[ $element['type'] ]["post_name_prefix"] . "_" . $element_counter . ( ( $form_prefix !== "" ) ? "_" . str_replace( "_", "", $form_prefix ) : "" );
								$base_name_inc = $name_inc;

								$is_cart_fee = ! empty( $element['is_cart_fee'] );
								if ( $is_cart_fee ) {
									$name_inc = $cart_fee_name . $name_inc;
								}

								$name_inc = apply_filters( 'wc_epo_name_inc', $name_inc, $base_name_inc, $element, FALSE, FALSE, FALSE);

								$name_inc                                                                                               = 'tmcp_' . $name_inc . ( ( $form_prefix !== "" ) ? "_" . str_replace( "_", "", $form_prefix ) : "" );
								$global_epos[ $priority ][ $pid ]['sections'][ $_s ]['elements'][ $arr_element_counter ]['name_inc']    = $name_inc;
								$global_epos[ $priority ][ $pid ]['sections'][ $_s ]['elements'][ $arr_element_counter ]['is_cart_fee'] = $is_cart_fee;

								$global_epos = apply_filters( 'global_epos_fill_builder_display', $global_epos, $priority, $pid, $_s, $arr_element_counter, $element, FALSE, FALSE, FALSE );

							}
							$element_counter ++;
						}

					}
				}
			}
			$unit_counter ++;
		}

		return array(
			'global_epos'     => $global_epos,
			'unit_counter'    => $unit_counter,
			'field_counter'   => $field_counter,
			'element_counter' => $element_counter,
		);

	}

}

define( 'THEMECOMPLETE_EPO_INCLUDED', 1 );
