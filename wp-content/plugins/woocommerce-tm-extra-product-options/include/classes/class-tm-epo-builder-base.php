<?php
/**
 * Extra Product Options Builder class
 *
 * @package Extra Product Options/Classes
 * @version 4.9
 */

 // phpcs: disable Squiz.Strings.DoubleQuoteUsage.NotRequired

defined( 'ABSPATH' ) || exit;

final class THEMECOMPLETE_EPO_BUILDER_base {

	/**
	 * The single instance of the class
	 *
	 * @since 1.0
	 */
	protected static $_instance = null;

	public $elements_namespace = 'TM Extra Product Options';

	public $all_elements;

	// element options
	public $elements_array;
	public $elements_array_addons = array();

	private $addons_array = array();

	private $addons_attributes = array();

	public $extra_multiple_options = array();

	private $default_attributes = array();

	// sections options
	public $_section_elements = array();

	// sizes display
	public $sizer;

	public $jsbuilder = array();

	public $noecho = false;
	public $wpml_section_fields;
	public $wpml_element_fields;

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

		// extra multiple type options
		$this->extra_multiple_options = apply_filters( 'wc_epo_extra_multiple_choices', array() );
		add_action( 'tm_epo_register_extra_multiple_choices', array( $this, 'add_extra_choices' ), 50 );

		// element available sizes
		$this->element_available_sizes();

		$this->_elements();
		add_action( 'admin_footer', array( $this, 'admin_footer' ), 9 );
		add_action( 'admin_init', array( $this, 'admin_init' ), 9 );

		$this->wpml_section_fields = array(
			"section_header_title",
			"section_header_subtitle",
			"sections_uniqid",
			"sections_class",
			"sections_clogic",
			"sections_logic",
		);
		$this->wpml_element_fields = array(
			"divider_uniqid",
			"divider_clogic",
			"divider_logic",
			"divider_class",
			"header_title",
			"header_subtitle",
			"header_uniqid",
			"header_clogic",
			"header_logic",
			"header_class",
			"textarea_header_title",
			"textarea_header_subtitle",
			"textarea_text_before_price",
			"textarea_text_after_price",
			"textarea_quantity_default_value",
			"textarea_placeholder",
			"textarea_default_value",
			"textarea_uniqid",
			"textarea_clogic",
			"textarea_logic",
			"textarea_class",
			"textarea_container_id",
			"textfield_header_title",
			"textfield_header_subtitle",
			"textfield_text_before_price",
			"textfield_text_after_price",
			"textfield_quantity_default_value",
			"textfield_placeholder",
			"textfield_default_value",
			"textfield_uniqid",
			"textfield_clogic",
			"textfield_logic",
			"textfield_class",
			"textfield_container_id",
			"selectbox_header_title",
			"selectbox_header_subtitle",
			"selectbox_text_before_price",
			"selectbox_text_after_price",
			"selectbox_quantity_default_value",
			"selectbox_placeholder",
			"selectbox_options",
			"selectbox_uniqid",
			"selectbox_clogic",
			"selectbox_logic",
			"selectbox_class",
			"selectbox_container_id",
			"radiobuttons_header_title",
			"radiobuttons_header_subtitle",
			"radiobuttons_text_before_price",
			"radiobuttons_text_after_price",
			"radiobuttons_quantity_default_value",
			"radiobuttons_options",
			"radiobuttons_uniqid",
			"radiobuttons_clogic",
			"radiobuttons_logic",
			"radiobuttons_class",
			"radiobuttons_container_id",
			"checkboxes_header_title",
			"checkboxes_header_subtitle",
			"checkboxes_text_before_price",
			"checkboxes_text_after_price",
			"checkboxes_quantity_default_value",
			"checkboxes_options",
			"checkboxes_uniqid",
			"checkboxes_clogic",
			"checkboxes_logic",
			"checkboxes_class",
			"checkboxes_container_id",
			"upload_header_title",
			"upload_header_subtitle",
			"upload_text_before_price",
			"upload_text_after_price",
			"upload_button_type",
			"upload_uniqid",
			"upload_clogic",
			"upload_logic",
			"upload_class",
			"upload_container_id",
			"date_header_title",
			"date_header_subtitle",
			"date_text_before_price",
			"date_text_after_price",
			"date_quantity_default_value",
			"date_format",
			"date_default_value",
			"date_disabled_dates",
			"date_enabled_only_dates",
			"date_tranlation_day",
			"date_tranlation_month",
			"date_tranlation_year",
			"date_uniqid",
			"date_clogic",
			"date_logic",
			"date_class",
			"date_container_id",
			"time_header_title",
			"time_header_subtitle",
			"time_text_before_price",
			"time_text_after_price",
			"time_quantity_default_value",
			"time_time_format",
			"time_tranlation_hour",
			"time_tranlation_minute",
			"time_tranlation_second",
			"time_uniqid",
			"time_clogic",
			"time_logic",
			"time_class",
			"time_container_id",
			"range_header_title",
			"range_header_subtitle",
			"range_text_before_price",
			"range_text_after_price",
			"range_quantity_default_value",
			"range_default_value",
			"range_uniqid",
			"range_clogic",
			"range_logic",
			"range_class",
			"range_container_id",
			"color_header_title",
			"color_header_subtitle",
			"color_text_before_price",
			"color_text_after_price",
			"color_quantity_default_value",
			"color_default_value",
			"color_uniqid",
			"color_clogic",
			"color_logic",
			"color_class",
			"color_container_id",
			"variations_header_title",
			"variations_header_subtitle",
			"variations_options",
			"product_header_title",
			"product_header_subtitle",
			"product_placeholder",
			"product_uniqid",
			"product_clogic",
			"product_logic",
		);

	}

	/**
	 * Init elements
	 *
	 * @since 5.0
	 */
	public function admin_init() {

		// init section elements
		$this->init_section_elements();

		// init elements
		$this->init_elements();

	}

	/**
	 * Add footer script
	 *
	 * @since 4.9.12
	 */
	public function admin_footer() {
		wp_register_script( 'themecomplete-footer-admin-js', false, array(), false, true );
		wp_localize_script( 'themecomplete-footer-admin-js', 'TMEPOOPTIONSJS', wp_json_encode( $this->jsbuilder ) );
		wp_enqueue_script( 'themecomplete-footer-admin-js' );
	}

	/**
	 * Get extra setting for multiple choice options
	 *
	 * @since 1.0
	 */
	public function add_extra_choices() {
		$this->extra_multiple_options = apply_filters( 'wc_epo_extra_multiple_choices', array() );
	}

	/**
	 * Holds all the elements types.
	 *
	 * [name]                  = Displayed name
	 * [width]                 = Initial width
	 * [width_display]         = Initial width display
	 * [icon]                  = icon
	 * [is_post]               = if it is post enabled field
	 * [type]                  = if it can hold multiple or single options (for post enabled fields)
	 * [post_name_prefix]      = name for post purposes
	 * [fee_type]              = can set cart fees
	 * [tage]                  = cartegory on the add element screen
	 * [show_on_backend]       = if it can be shown on the add element screen
	 *
	 * @since  1.0
	 * @access private
	 */
	private function _elements() {

		$this->all_elements = apply_filters( 'wc_epo_builder_element_settings', array(
				"header"       => array(
					"_is_addon"        => false,
					"namespace"        => $this->elements_namespace,
					"name"             => esc_html__( "Heading", 'woocommerce-tm-extra-product-options' ),
					"description"      => "",
					"width"            => "w100",
					"width_display"    => "100%",
					"icon"             => "tcfa-heading",
					"is_post"          => "display",
					"type"             => "",
					"post_name_prefix" => "header",
					"fee_type"         => "",
					"tags"             => "content",
					"show_on_backend"  => true,
				),
				"divider"      => array(
					"_is_addon"        => false,
					"namespace"        => $this->elements_namespace,
					"name"             => esc_html__( "Divider", 'woocommerce-tm-extra-product-options' ),
					"description"      => "",
					"width"            => "w100",
					"width_display"    => "100%",
					"icon"             => "tcfa-divide",
					"is_post"          => "display",
					"type"             => "",
					"post_name_prefix" => "divider",
					"fee_type"         => "",
					"tags"             => "content",
					"show_on_backend"  => true,
				),
				"date"         => array(
					"_is_addon"        => false,
					"namespace"        => $this->elements_namespace,
					"name"             => esc_html__( "Date", 'woocommerce-tm-extra-product-options' ),
					"description"      => "",
					"width"            => "w100",
					"width_display"    => "100%",
					"icon"             => "tcfa-calendar-alt",
					"is_post"          => "post",
					"type"             => "single",
					"post_name_prefix" => "date",
					"fee_type"         => "single",
					"tags"             => "price content",
					"show_on_backend"  => true,
				),
				"time"         => array(
					"_is_addon"        => false,
					"namespace"        => $this->elements_namespace,
					"name"             => esc_html__( "Time", 'woocommerce-tm-extra-product-options' ),
					"description"      => "",
					"width"            => "w100",
					"width_display"    => "100%",
					"icon"             => "tcfar tcfa-clock",
					"is_post"          => "post",
					"type"             => "single",
					"post_name_prefix" => "time",
					"fee_type"         => "single",
					"tags"             => "price content",
					"show_on_backend"  => true,
				),
				"range"        => array(
					"_is_addon"        => false,
					"namespace"        => $this->elements_namespace,
					"name"             => esc_html__( "Range picker", 'woocommerce-tm-extra-product-options' ),
					"description"      => "",
					"width"            => "w100",
					"width_display"    => "100%",
					"icon"             => "tcfa-arrows-alt-h",
					"is_post"          => "post",
					"type"             => "single",
					"post_name_prefix" => "range",
					"fee_type"         => "single",
					"tags"             => "price content",
					"show_on_backend"  => true,
				),
				"color"        => array(
					"_is_addon"        => false,
					"namespace"        => $this->elements_namespace,
					"name"             => esc_html__( "Color picker", 'woocommerce-tm-extra-product-options' ),
					"description"      => "",
					"width"            => "w100",
					"width_display"    => "100%",
					"icon"             => "tcfa-palette",
					"is_post"          => "post",
					"type"             => "single",
					"post_name_prefix" => "color",
					"fee_type"         => "single",
					"tags"             => "price content",
					"show_on_backend"  => true,
				),
				"textarea"     => array(
					"_is_addon"        => false,
					"namespace"        => $this->elements_namespace,
					"name"             => esc_html__( "Text Area", 'woocommerce-tm-extra-product-options' ),
					"description"      => "",
					"width"            => "w100",
					"width_display"    => "100%",
					"icon"             => "tcfa-file tcfa-rotate-90",
					"is_post"          => "post",
					"type"             => "single",
					"post_name_prefix" => "textarea",
					"fee_type"         => "single",
					"tags"             => "price content",
					"show_on_backend"  => true,
				),
				"textfield"    => array(
					"_is_addon"        => false,
					"namespace"        => $this->elements_namespace,
					"name"             => esc_html__( "Text Field", 'woocommerce-tm-extra-product-options' ),
					"description"      => "",
					"width"            => "w100",
					"width_display"    => "100%",
					"icon"             => "tcfa-i-cursor",
					"is_post"          => "post",
					"type"             => "single",
					"post_name_prefix" => "textfield",
					"fee_type"         => "single",
					"tags"             => "price content",
					"show_on_backend"  => true,
				),
				"upload"       => array(
					"_is_addon"        => false,
					"namespace"        => $this->elements_namespace,
					"name"             => esc_html__( "Upload", 'woocommerce-tm-extra-product-options' ),
					"description"      => "",
					"width"            => "w100",
					"width_display"    => "100%",
					"icon"             => "tcfa-upload",
					"is_post"          => "post",
					"type"             => "single",
					"post_name_prefix" => "upload",
					"fee_type"         => "single",
					"tags"             => "price content",
					"show_on_backend"  => true,
				),
				"selectbox"    => array(
					"_is_addon"        => false,
					"namespace"        => $this->elements_namespace,
					"name"             => esc_html__( "Select Box", 'woocommerce-tm-extra-product-options' ),
					"description"      => "",
					"width"            => "w100",
					"width_display"    => "100%",
					"icon"             => "tcfa-caret-square-down",
					"is_post"          => "post",
					"type"             => "multiplesingle",
					"post_name_prefix" => "select",
					"fee_type"         => "multiple",
					"tags"             => "price content",
					"show_on_backend"  => true,
				),
				"radiobuttons" => array(
					"_is_addon"        => false,
					"namespace"        => $this->elements_namespace,
					"name"             => esc_html__( "Radio buttons", 'woocommerce-tm-extra-product-options' ),
					"description"      => "",
					"width"            => "w100",
					"width_display"    => "100%",
					"icon"             => "tcfa-dot-circle tcfar",
					"is_post"          => "post",
					"type"             => "multiple",
					"post_name_prefix" => "radio",
					"fee_type"         => "multiple",
					"tags"             => "price content",
					"show_on_backend"  => true,
				),
				"checkboxes"   => array(
					"_is_addon"        => false,
					"namespace"        => $this->elements_namespace,
					"name"             => esc_html__( "Checkboxes", 'woocommerce-tm-extra-product-options' ),
					"description"      => "",
					"width"            => "w100",
					"width_display"    => "100%",
					"icon"             => "tcfa-check-square",
					"is_post"          => "post",
					"type"             => "multipleall",
					"post_name_prefix" => "checkbox",
					"fee_type"         => "multiple",
					"tags"             => "price content",
					"show_on_backend"  => true,
				),
				"variations"   => array(
					"_is_addon"        => false,
					"namespace"        => $this->elements_namespace,
					"name"             => esc_html__( "Variations", 'woocommerce-tm-extra-product-options' ),
					"description"      => "",
					"width"            => "w100",
					"width_display"    => "100%",
					"icon"             => "tcfa-layer-group",
					"is_post"          => "display",
					"type"             => "multiplesingle",
					"post_name_prefix" => "variations",
					"fee_type"         => "",
					"one_time_field"   => true,
					"no_selection"     => true,
					"tags"             => "",
					"show_on_backend"  => false,
				),
				"product"      => array(
					"_is_addon"        => false,
					"namespace"        => $this->elements_namespace,
					"name"             => esc_html__( "Product", 'woocommerce-tm-extra-product-options' ),
					"description"      => "",
					"width"            => "w100",
					"width_display"    => "100%",
					"icon"             => "tcfa-cube",
					"is_post"          => "post",
					"type"             => "single",
					"post_name_prefix" => "product",
					"fee_type"         => "",
					"tags"             => "price content product",
					"show_on_backend"  => true,
				),
			)
		);


		do_action( 'wc_epo_builder_after_element_settings', $this->all_elements );
	}

	/**
	 * Get all elements
	 *
	 * @since 1.0
	 */
	public final function get_elements() {
		return $this->all_elements;
	}

	/**
	 * Set elements
	 *
	 * @since 1.0
	 */
	private function set_elements( $args = array() ) {

		$element = $args["name"];
		$options = apply_filters( 'wc_epo_set_elements_options', $args["options"], $args );

		if ( ! empty( $element ) && is_array( $options ) ) {
			$options["_is_addon"] = true;

			if ( ! isset( $args["namespace"] ) ) {
				$options["namespace"] = "EPD addon " . $element;
			} else {
				$options["namespace"] = $args["namespace"];
			}
			if ( $options["namespace"] == $this->elements_namespace ) {
				$options["namespace"] = $this->elements_namespace . " addon";
			}

			if ( ! isset( $options["name"] ) ) {
				$options["name"] = "";
			}
			if ( ! isset( $options["description"] ) ) {
				$options["description"] = "";
			}
			if ( ! isset( $options["type"] ) ) {
				$options["type"] = "";
			}
			if ( ! isset( $options["width"] ) ) {
				$options["width"] = "";
			}
			if ( ! isset( $options["width_display"] ) ) {
				$options["width_display"] = "";
			}
			if ( ! isset( $options["icon"] ) ) {
				$options["icon"] = "";
			}
			if ( ! isset( $options["is_post"] ) ) {
				$options["is_post"] = "";
			}
			if ( ! isset( $options["post_name_prefix"] ) ) {
				$options["post_name_prefix"] = "";
			}
			if ( ! isset( $options["fee_type"] ) ) {
				$options["fee_type"] = "";
			}

			$options["tags"] = $options["name"];

			$options["show_on_backend"] = true;

			$this->all_elements = array_merge( array( $element => $options ), $this->all_elements );
		}
	}

	/**
	 * Get custom properties
	 *
	 * @since 1.0
	 */
	public final function get_custom_properties( $builder, $_prefix, $_counter, $_elements, $k0, $current_builder, $current_counter, $wpml_element_fields, $current_element ) {
		$p = array();
		foreach ( $this->addons_attributes as $key => $value ) {
			$p[ $value ] = THEMECOMPLETE_EPO()->get_builder_element( $_prefix . $value, $builder, $current_builder, $current_counter, "", $wpml_element_fields, $current_element );
		}

		return $p;
	}

	/**
	 * Get default properties
	 *
	 * @since 1.0
	 */
	public final function get_default_properties( $builder, $_prefix, $_counter, $_elements, $k0 ) {
		$p = array();
		foreach ( $this->default_attributes as $key => $value ) {
			$p[ $value ] = isset( $builder[ $_prefix . $value ][ $_counter[ $_elements[ $k0 ] ] ] )
				? $builder[ $_prefix . $value ][ $_counter[ $_elements[ $k0 ] ] ]
				: "";
		}

		return $p;
	}

	/**
	 * Register addons
	 *
	 * @since 1.0
	 */
	public final function register_addon( $args = array() ) {
		if ( isset( $args["namespace"] )
		     && isset( $args["name"] )
		     && isset( $args["options"] )
		     && isset( $args["settings"] ) ) {
			$this->elements_array_addons = array_merge(
				array(
					$args["name"] => $this->add_element(
						$args["name"],
						$args["settings"], true,
						isset( $args["tabs_override"] ) ? $args["tabs_override"] : array() ),
				), $this->elements_array_addons );

			$this->set_elements( $args );

			$this->addons_array[] = $args["name"];
		}
	}

	/**
	 * Available element sizes
	 *
	 * @since 1.0
	 */
	private function element_available_sizes() {
		$this->sizer = array(
			"w1"  => "1%",
			"w2"  => "2%",
			"w3"  => "3%",
			"w4"  => "4%",
			"w5"  => "5%",
			"w6"  => "6%",
			"w7"  => "7%",
			"w8"  => "8%",
			"w9"  => "9%",
			"w10" => "10%",
			"w11" => "11%",
			"w12" => "12%",

			"w12-5" => "12.5%",

			"w13" => "13%",
			"w14" => "14%",
			"w15" => "15%",
			"w16" => "16%",
			"w17" => "17%",
			"w18" => "18%",
			"w19" => "19%",
			"w20" => "20%",
			"w21" => "21%",
			"w22" => "22%",
			"w23" => "23%",
			"w24" => "24%",
			"w25" => "25%",
			"w26" => "26%",
			"w27" => "27%",
			"w28" => "28%",
			"w29" => "29%",
			"w30" => "30%",
			"w31" => "31%",
			"w32" => "32%",
			"w33" => "33%",
			"w34" => "34%",
			"w35" => "35%",
			"w36" => "36%",
			"w37" => "37%",

			"w37-5" => "37.5%",

			"w38" => "38%",
			"w39" => "39%",
			"w40" => "40%",
			"w41" => "41%",
			"w42" => "42%",
			"w43" => "43%",
			"w44" => "44%",
			"w45" => "45%",
			"w46" => "46%",
			"w47" => "47%",
			"w48" => "48%",
			"w49" => "49%",
			"w50" => "50%",
			"w51" => "51%",
			"w52" => "52%",
			"w53" => "53%",
			"w54" => "54%",
			"w55" => "55%",
			"w56" => "56%",
			"w57" => "57%",
			"w58" => "58%",
			"w59" => "59%",
			"w60" => "60%",
			"w61" => "61%",
			"w62" => "62%",

			"w62-5" => "62.5%",

			"w63" => "63%",
			"w64" => "64%",
			"w65" => "65%",
			"w66" => "66%",
			"w67" => "67%",
			"w68" => "68%",
			"w69" => "69%",
			"w70" => "70%",
			"w71" => "71%",
			"w72" => "72%",
			"w73" => "73%",
			"w74" => "74%",
			"w75" => "75%",
			"w76" => "76%",
			"w77" => "77%",
			"w78" => "78%",
			"w79" => "79%",
			"w80" => "80%",
			"w81" => "81%",
			"w82" => "82%",
			"w83" => "83%",
			"w84" => "84%",
			"w85" => "85%",
			"w86" => "86%",
			"w87" => "87%",

			"w87-5" => "87.5%",

			"w88"  => "88%",
			"w89"  => "89%",
			"w90"  => "90%",
			"w91"  => "91%",
			"w92"  => "92%",
			"w93"  => "93%",
			"w94"  => "94%",
			"w95"  => "95%",
			"w96"  => "96%",
			"w97"  => "97%",
			"w98"  => "98%",
			"w99"  => "99%",
			"w100" => "100%",
		);
	}

	/**
	 * Init section elements
	 *
	 * @since 1.0
	 */
	private function init_section_elements() {
		$this->_section_elements = array_merge(
			$this->_prepend_div( "", "tm-tabs" ),

			$this->_prepend_div( "section", "tm-tab-headers" ),
			$this->_prepend_tab( "section0", array(
				"tcfa tcfa-heading",
				esc_html__( "Title options" ),
				'woocommerce-tm-extra-product-options',
			), "", "tma-tab-title" ),
			$this->_prepend_tab( "section1", array(
				"tcfa tcfa-cog",
				esc_html__( "General options", 'woocommerce-tm-extra-product-options' ),
			), "open", "tma-tab-general" ),
			$this->_prepend_tab( "section2", array(
				"tcfa tcfa-lightbulb",
				esc_html__( "Conditional Logic", 'woocommerce-tm-extra-product-options' ),
			), "", "tma-tab-logic" ),
			$this->_append_div( "section" ),

			$this->_prepend_div( "section0" ),
			$this->_get_header_array( "section" . "_header", "section" ),
			$this->_get_divider_array( "section" . "_divider", 0 ),
			$this->_append_div( "section0" ),

			$this->_prepend_div( "section1" ),
			apply_filters( 'tc_builder_section_settings',
				array(
					"sectionnum"       => array(
						"id"          => "sections",
						"wpmldisable" => 1,
						"default"     => 0,
						"nodiv"       => 1,
						"type"        => "hidden",
						"tags"        => array(
							"class" => "tm_builder_sections",
							"name"  => "tm_meta[tmfbuilder][sections][]",
							"value" => 0,
						),
						"label"       => "",
						"desc"        => "",
					),
					"sections_slides"  => array(
						"id"          => "sections_slides",
						"wpmldisable" => 1,
						"default"     => "",
						"nodiv"       => 1,
						"type"        => "hidden",
						"tags"        => array(
							"class" => "tm_builder_section_slides",
							"name"  => "tm_meta[tmfbuilder][sections_slides][]",
							"value" => 0,
						),
						"label"       => "",
						"desc"        => "",
					),
					"sectionsize"      => array(
						"id"          => "sections_size",
						"wpmldisable" => 1,
						"default"     => "w100",
						"nodiv"       => 1,
						"type"        => "hidden",
						"tags"        => array(
							"class" => "tm_builder_sections_size",
							"name"  => "tm_meta[tmfbuilder][sections_size][]",
							"value" => "w100",
						),
						"label"       => "",
						"desc"        => "",
					),
					"sectionuniqid"    => array(
						"id"      => "sections_uniqid",
						"default" => "",
						"nodiv"   => 1,
						"type"    => "hidden",
						"tags"    => array(
							"class" => "tm-builder-sections-uniqid",
							"name"  => "tm_meta[tmfbuilder][sections_uniqid][]",
							"value" => "",
						),
						"label"   => "",
						"desc"    => "",
					),
					"sectionstyle"     => array(
						"id"          => "sections_style",
						"wpmldisable" => 1,
						"default"     => "",
						"type"        => "select",
						"tags"        => array(
							"class" => "sections_style",
							"id"    => "tm_sections_style",
							"name"  => "tm_meta[tmfbuilder][sections_style][]",
						),
						"options"     => array(
							array(
								"text"  => esc_html__( "Normal (clear)", 'woocommerce-tm-extra-product-options' ),
								"value" => "",
							),
							array(
								"text"  => esc_html__( "Box", 'woocommerce-tm-extra-product-options' ),
								"value" => "box",
							),
							array(
								"text"  => esc_html__( "Expand and Collapse (start opened)", 'woocommerce-tm-extra-product-options' ),
								"value" => "collapse",
								"class" => "builder_hide_for_variation-reset",
							),
							array(
								"text"  => esc_html__( "Expand and Collapse (start closed)", 'woocommerce-tm-extra-product-options' ),
								"value" => "collapseclosed",
								"class" => "builder_hide_for_variation-reset",
							),
							array(
								"text"  => esc_html__( "Accordion", 'woocommerce-tm-extra-product-options' ),
								"value" => "accordion",
								"class" => "builder_hide_for_variation-reset",
							),
						),
						"label"       => esc_html__( "Section style", 'woocommerce-tm-extra-product-options' ),
						"desc"        => esc_html__( "Select this section's display style.", 'woocommerce-tm-extra-product-options' ),
					),
					"sectionplacement" => array(
						"id"               => "sections_placement",
						"message0x0_class" => "builder_hide_for_variation",
						"wpmldisable"      => 1,
						"default"          => "before",
						"type"             => "select",
						"tags"             => array(
							"id"   => "sections_placement",
							"name" => "tm_meta[tmfbuilder][sections_placement][]",
						),
						"options"          => array(
							array(
								"text"  => esc_html__( "Before Local Options", 'woocommerce-tm-extra-product-options' ),
								"value" => "before",
							),
							array(
								"text"  => esc_html__( "After Local Options", 'woocommerce-tm-extra-product-options' ),
								"value" => "after",
							),
						),
						"label"            => esc_html__( "Section placement", 'woocommerce-tm-extra-product-options' ),
						"desc"             => esc_html__( "Select where this section will appear compare to local Options.", 'woocommerce-tm-extra-product-options' ),
					),
					"sectiontype"      => array(
						"id"          => "sections_type",
						"wpmldisable" => 1,
						"default"     => "",
						"type"        => "select",
						"tags"        => array(
							"class" => "sections_type",
							"id"    => "sections_type",
							"name"  => "tm_meta[tmfbuilder][sections_type][]",
						),
						"options"     => array(
							array(
								"text"  => esc_html__( "Normal", 'woocommerce-tm-extra-product-options' ),
								"value" => "",
							),
							array(
								"text"  => esc_html__( "Pop up", 'woocommerce-tm-extra-product-options' ),
								"value" => "popup",
							),
							array(
								"text"  => esc_html__( "Slider (wizard)", 'woocommerce-tm-extra-product-options' ),
								"value" => "slider",
								"class" => "builder-remove-for-variations",
							),
						),
						"label"       => esc_html__( "Section type", 'woocommerce-tm-extra-product-options' ),
						"desc"        => esc_html__( "Select this section's display type.", 'woocommerce-tm-extra-product-options' ),
					),

					"sectionsclass" => array(
						"id"      => "sections_class",
						"default" => "",
						"type"    => "text",
						"tags"    => array(
							"class" => "t",
							"id"    => "sections_class",
							"name"  => "tm_meta[tmfbuilder][sections_class][]",
							"value" => "",
						),
						"label"   => esc_html__( 'Section class name', 'woocommerce-tm-extra-product-options' ),
						"desc"    => esc_html__( 'Enter an extra class name to add to this section', 'woocommerce-tm-extra-product-options' ),
					),
				)
			),

			$this->_append_div( "section1" ),

			$this->_prepend_div( "section2" ),
			array(
				"sectionclogic" => array(
					"id"      => "sections_clogic",
					"default" => "",
					"nodiv"   => 1,
					"type"    => "hidden",
					"tags"    => array(
						"class" => "tm-builder-clogic",
						"name"  => "tm_meta[tmfbuilder][sections_clogic][]",
						"value" => "",
					),
					"label"   => "",
					"desc"    => "",
				),
				"sectionlogic"  => array(
					"id"        => "sections_logic",
					"default"   => "",
					"leftclass" => "align-self-start",
					"type"      => "checkbox",
					"tags"      => array(
						"class" => "activate-sections-logic",
						"id"    => "sections_logic",
						"name"  => "tm_meta[tmfbuilder][sections_logic][]",
						"value" => "1",
					),
					"extra"     => array( array( $this, "builder_showlogic" ), array() ),
					"label"     => esc_html__( "Section Conditional Logic", 'woocommerce-tm-extra-product-options' ),
					"desc"      => esc_html__( "Enable conditional logic for showing or hiding this section.", 'woocommerce-tm-extra-product-options' ),
				),
			),
			$this->_append_div( "section2" ),

			$this->_append_div( "" )
		);
	}

	/**
	 * Init elements
	 *
	 * @since 1.0
	 */
	private function init_elements() {

		$this->elements_array = array(
			"divider" => array_merge(
				$this->_prepend_div( "", "tm-tabs" ),

				$this->_prepend_div( "divider", "tm-tab-headers" ),
				$this->_prepend_tab( "divider2", esc_html__( "General options", 'woocommerce-tm-extra-product-options' ), "open" ),
				$this->_prepend_tab( "divider3", esc_html__( "Conditional Logic", 'woocommerce-tm-extra-product-options' ) ),
				$this->_prepend_tab( "divider4", esc_html__( "CSS settings", 'woocommerce-tm-extra-product-options' ) ),
				$this->_append_div( "divider" ),

				$this->_prepend_div( "divider2" ),
				$this->_get_divider_array(),

				$this->_append_div( "divider2" ),

				$this->_prepend_div( "divider3" ),
				$this->_prepend_logic( "divider" ),
				$this->_append_div( "divider3" ),

				$this->_prepend_div( "divider4" ),
				array(
					array(
						"id"      => "divider_class",
						"default" => "",
						"type"    => "text",
						"tags"    => array(
							"class" => "t",
							"id"    => "builder_divider_class",
							"name"  => "tm_meta[tmfbuilder][divider_class][]",
							"value" => "",
						),
						"label"   => esc_html__( 'Element class name', 'woocommerce-tm-extra-product-options' ),
						"desc"    => esc_html__( 'Enter an extra class name to add to this element', 'woocommerce-tm-extra-product-options' ),
					),
				),
				$this->_append_div( "divider4" ),

				$this->_append_div( "" )
			),

			"header" => array_merge(
				$this->_prepend_div( "", "tm-tabs" ),

				$this->_prepend_div( "header", "tm-tab-headers" ),
				$this->_prepend_tab( "header2", esc_html__( "General options", 'woocommerce-tm-extra-product-options' ), "open" ),
				$this->_prepend_tab( "header3", esc_html__( "Conditional Logic", 'woocommerce-tm-extra-product-options' ) ),
				$this->_prepend_tab( "header4", esc_html__( "CSS settings", 'woocommerce-tm-extra-product-options' ) ),
				$this->_append_div( "header" ),

				$this->_prepend_div( "header2" ),
				array(
					array(
						"id"          => "header_size",
						"wpmldisable" => 1,
						"default"     => "3",
						"type"        => "select",
						"tags"        => array(
							"id"   => "builder_header_size",
							"name" => "tm_meta[tmfbuilder][header_size][]",
						),
						"options"     => array(
							array(
								"text"  => esc_html__( "H1", 'woocommerce-tm-extra-product-options' ),
								"value" => "1",
							),
							array(
								"text"  => esc_html__( "H2", 'woocommerce-tm-extra-product-options' ),
								"value" => "2",
							),
							array(
								"text"  => esc_html__( "H3", 'woocommerce-tm-extra-product-options' ),
								"value" => "3",
							),
							array(
								"text"  => esc_html__( "H4", 'woocommerce-tm-extra-product-options' ),
								"value" => "4",
							),
							array(
								"text"  => esc_html__( "H5", 'woocommerce-tm-extra-product-options' ),
								"value" => "5",
							),
							array(
								"text"  => esc_html__( "H6", 'woocommerce-tm-extra-product-options' ),
								"value" => "6",
							),
							array(
								"text"  => esc_html__( "p", 'woocommerce-tm-extra-product-options' ),
								"value" => "7",
							),
							array(
								"text"  => esc_html__( "div", 'woocommerce-tm-extra-product-options' ),
								"value" => "8",
							),
							array(
								"text"  => esc_html__( "span", 'woocommerce-tm-extra-product-options' ),
								"value" => "9",
							),
						),
						"label"       => esc_html__( "Header type", 'woocommerce-tm-extra-product-options' ),
						"desc"        => "",
					),
					array(
						"id"      => "header_title",
						"default" => "",
						"type"    => "text",
						"tags"    => array(
							"class" => "t tm-header-title",
							"id"    => "builder_header_title",
							"name"  => "tm_meta[tmfbuilder][header_title][]",
							"value" => "",
						),
						"label"   => esc_html__( 'Header title', 'woocommerce-tm-extra-product-options' ),
						"desc"    => "",
					),
					array(
						"id"          => "header_title_position",
						"wpmldisable" => 1,
						"default"     => "",
						"type"        => "select",
						"tags"        => array(
							"id"   => "builder_header_title_position",
							"name" => "tm_meta[tmfbuilder][header_title_position][]",
						),
						"options"     => array(
							array(
								"text"  => esc_html__( "Above field", 'woocommerce-tm-extra-product-options' ),
								"value" => "",
							),
							array(
								"text"  => esc_html__( "Left of the field", 'woocommerce-tm-extra-product-options' ),
								"value" => "left",
							),
							array(
								"text"  => esc_html__( "Right of the field", 'woocommerce-tm-extra-product-options' ),
								"value" => "right",
							),
							array(
								"text"  => esc_html__( "Disable", 'woocommerce-tm-extra-product-options' ),
								"value" => "disable",
							),
						),
						"label"       => esc_html__( "Header position", 'woocommerce-tm-extra-product-options' ),
						"desc"        => "",
					),
					array(
						"id"          => "header_title_color",
						"wpmldisable" => 1,
						"default"     => "",
						"type"        => "text",
						"tags"        => array(
							"class" => "tm-color-picker",
							"id"    => "builder_header_title_color",
							"name"  => "tm_meta[tmfbuilder][header_title_color][]",
							"value" => "",
						),
						"label"       => esc_html__( 'Header color', 'woocommerce-tm-extra-product-options' ),
						"desc"        => esc_html__( 'Leave empty for default value', 'woocommerce-tm-extra-product-options' ),
					),
					array(
						"id"      => "header_subtitle",
						"default" => "",
						"type"    => "textarea",
						"tags"    => array(
							"id"   => "builder_header_subtitle",
							"name" => "tm_meta[tmfbuilder][header_subtitle][]",
						),
						"label"   => esc_html__( "Content", 'woocommerce-tm-extra-product-options' ),
						"desc"    => "",
					),
					array(
						"id"          => "header_subtitle_color",
						"wpmldisable" => 1,
						"default"     => "",
						"type"        => "text",
						"tags"        => array(
							"class" => "tm-color-picker",
							"id"    => "builder_header_subtitle_color",
							"name"  => "tm_meta[tmfbuilder][header_subtitle_color][]",
							"value" => "",
						),
						"label"       => esc_html__( 'Content color', 'woocommerce-tm-extra-product-options' ),
						"desc"        => esc_html__( 'Leave empty for default value', 'woocommerce-tm-extra-product-options' ),
					),
					array(
						"id"          => "header_subtitle_position",
						"wpmldisable" => 1,
						"default"     => "",
						"type"        => "select",
						"tags"        => array(
							"id"   => "builder_header_subtitle_position",
							"name" => "tm_meta[tmfbuilder][header_subtitle_position][]",
						),
						"options"     => array(
							array(
								"text"  => esc_html__( "Above field", 'woocommerce-tm-extra-product-options' ),
								"value" => "",
							),
							array(
								"text"  => esc_html__( "Below field", 'woocommerce-tm-extra-product-options' ),
								"value" => "below",
							),
							array(
								"text"  => esc_html__( "Tooltip", 'woocommerce-tm-extra-product-options' ),
								"value" => "tooltip",
							),
							array(
								"text"  => esc_html__( "Icon tooltip left", 'woocommerce-tm-extra-product-options' ),
								"value" => "icontooltipleft",
							),
							array(
								"text"  => esc_html__( "Icon tooltip right", 'woocommerce-tm-extra-product-options' ),
								"value" => "icontooltipright",
							),
						),
						"label"       => esc_html__( "Content position", 'woocommerce-tm-extra-product-options' ),
						"desc"        => "",
					),
				),

				$this->_append_div( "header2" ),

				$this->_prepend_div( "header3" ),
				$this->_prepend_logic( "header" ),
				$this->_append_div( "header3" ),

				$this->_prepend_div( "header4" ),
				array(
					array(
						"id"      => "header_class",
						"default" => "",
						"type"    => "text",
						"tags"    => array(
							"class" => "t",
							"id"    => "builder_header_class",
							"name"  => "tm_meta[tmfbuilder][header_class][]",
							"value" => "",
						),
						"label"   => esc_html__( 'Element class name', 'woocommerce-tm-extra-product-options' ),
						"desc"    => esc_html__( 'Enter an extra class name to add to this element', 'woocommerce-tm-extra-product-options' ),
					),
				),
				$this->_append_div( "header4" ),

				$this->_append_div( "" )
			),

			"textarea" => $this->add_element(
				"textarea",
				array(
					"enabled",
					"required",
					"price_type",
					"freechars",
					"price",
					"sale_price",
					"fee",
					"hide_amount",
					"text_before_price",
					"text_after_price",
					"quantity",
					"placeholder",
					"min_chars",
					"max_chars",
					"default_value_multiple",
					"validation1",
				)
			),

			"textfield" => $this->add_element(
				"textfield",
				array(
					"enabled",
					"required",
					"price_type2",
					"freechars",
					"price",
					"sale_price",
					"fee",
					"hide_amount",
					"text_before_price",
					"text_after_price",
					"quantity",
					"placeholder",
					"min_chars",
					"max_chars",
					"default_value",
					"min",
					"max",
					"validation1",
				)
			),

			"selectbox" => $this->add_element(
				"selectbox",
				array(
					"enabled",
					"required",
					"fee",
					"hide_amount",
					"text_before_price",
					"text_after_price",
					"quantity",
					"use_url",
					"changes_product_image",
					array(
						"placeholder",
						array( "desc" => esc_html__( 'This will count as an empty value in case the element is required.', 'woocommerce-tm-extra-product-options' ) ),
					),
					"options",
				)
			),

			"radiobuttons" => $this->add_element(
				"radiobuttons",
				array(
					"enabled",
					"required",
					"fee",
					"hide_amount",
					"text_before_price",
					"text_after_price",
					"quantity",
					"use_url",
					"use_images",
					"use_lightbox",
					"swatchmode",
					"use_colors",
					"changes_product_image",
					"items_per_row",
					"clear_options",
					"options",
				)
			),

			"checkboxes" => $this->add_element(
				"checkboxes",
				array(
					"enabled",
					"required",
					"hide_amount",
					"text_before_price",
					"text_after_price",
					"quantity",
					"limit_choices",
					"exactlimit_choices",
					"minimumlimit_choices",
					"use_images",
					"use_lightbox",
					"swatchmode",
					"use_colors",
					"changes_product_image",
					"items_per_row",
					"options",
				)
			),

			"upload" => $this->add_element(
				"upload",
				array(
					"enabled",
					"required",
					"price_type5",
					"price",
					"sale_price",
					"fee",
					"hide_amount",
					"text_before_price",
					"text_after_price",
					"button_type",
				)
			),

			"date" => $this->add_element(
				"date",
				array(
					"enabled",
					"required",
					"price_type6",
					"price",
					"sale_price",
					"fee",
					"hide_amount",
					"text_before_price",
					"text_after_price",
					"quantity",
					"button_type2",
					"date_format",
					"start_year",
					"end_year",
					array(
						"id"      => "date_default_value",
						"default" => "",
						"type"    => "text",
						"tags"    => array(
							"class" => "t",
							"id"    => "builder_date_default_value",
							"name"  => "tm_meta[tmfbuilder][date_default_value][]",
							"value" => "",
						),
						"label"   => esc_html__( 'Default value', 'woocommerce-tm-extra-product-options' ),
						"desc"    => esc_html__( 'Enter a value to be applied to the field automatically according to your selected date format. (Two digits for day, two digits for month and four digits for year).', 'woocommerce-tm-extra-product-options' ),
					),
					array(
						"id"          => "date_min_date",
						"wpmldisable" => 1,
						"default"     => "",
						"type"        => "text",
						"tags"        => array(
							"class" => "t",
							"id"    => "builder_date_min_date",
							"name"  => "tm_meta[tmfbuilder][date_min_date][]",
							"value" => "",
						),
						"label"       => esc_html__( 'Minimum selectable date', 'woocommerce-tm-extra-product-options' ),
						"desc"        => esc_html__( 'A number of days from today.', 'woocommerce-tm-extra-product-options' ),
					),
					array(
						"id"          => "date_max_date",
						"wpmldisable" => 1,
						"default"     => "",
						"type"        => "text",
						"tags"        => array(
							"class" => "t",
							"id"    => "builder_date_max_date",
							"name"  => "tm_meta[tmfbuilder][date_max_date][]",
							"value" => "",
						),
						"label"       => esc_html__( 'Maximum selectable date', 'woocommerce-tm-extra-product-options' ),
						"desc"        => esc_html__( 'A number of days from today.', 'woocommerce-tm-extra-product-options' ),
					),
					array(
						"id"      => "date_disabled_dates",
						"default" => "",
						"type"    => "text",
						"tags"    => array(
							"class" => "t",
							"id"    => "builder_date_disabled_dates",
							"name"  => "tm_meta[tmfbuilder][date_disabled_dates][]",
							"value" => "",
						),
						"label"   => esc_html__( 'Disabled dates', 'woocommerce-tm-extra-product-options' ),
						"desc"    => esc_html__( 'Comma separated dates according to your selected date format. (Two digits for day, two digits for month and four digits for year)', 'woocommerce-tm-extra-product-options' ),
					),
					array(
						"id"      => "date_enabled_only_dates",
						"default" => "",
						"type"    => "text",
						"tags"    => array(
							"class" => "t",
							"id"    => "builder_date_enabled_only_dates",
							"name"  => "tm_meta[tmfbuilder][date_enabled_only_dates][]",
							"value" => "",
						),
						"label"   => esc_html__( 'Enabled dates', 'woocommerce-tm-extra-product-options' ),
						"desc"    => esc_html__( 'Comma separated dates according to your selected date format. (Two digits for day, two digits for month and four digits for year). Please note that this will override any other setting!', 'woocommerce-tm-extra-product-options' ),
					),
					array(
						"id"          => "date_theme",
						"wpmldisable" => 1,
						"default"     => "epo",
						"type"        => "select",
						"tags"        => array(
							"id"   => "builder_date_theme",
							"name" => "tm_meta[tmfbuilder][date_theme][]",
						),
						"options"     => array(
							array(
								"text"  => esc_html__( "Epo White", 'woocommerce-tm-extra-product-options' ),
								"value" => "epo",
							),
							array(
								"text"  => esc_html__( "Epo Black", 'woocommerce-tm-extra-product-options' ),
								"value" => "epo-black",
							),
						),
						"label"       => esc_html__( "Theme", 'woocommerce-tm-extra-product-options' ),
						"desc"        => esc_html__( "Select the theme for the datepicker.", 'woocommerce-tm-extra-product-options' ),
					),
					array(
						"id"          => "date_theme_size",
						"wpmldisable" => 1,
						"default"     => "medium",
						"type"        => "select",
						"tags"        => array(
							"id"   => "builder_date_theme_size",
							"name" => "tm_meta[tmfbuilder][date_theme_size][]",
						),
						"options"     => array(
							array(
								"text"  => esc_html__( "Small", 'woocommerce-tm-extra-product-options' ),
								"value" => "small",
							),
							array(
								"text"  => esc_html__( "Medium", 'woocommerce-tm-extra-product-options' ),
								"value" => "medium",
							),
							array(
								"text"  => esc_html__( "Large", 'woocommerce-tm-extra-product-options' ),
								"value" => "large",
							),
						),
						"label"       => esc_html__( "Size", 'woocommerce-tm-extra-product-options' ),
						"desc"        => esc_html__( "Select the size of the datepicker.", 'woocommerce-tm-extra-product-options' ),
					),
					array(
						"id"          => "date_theme_position",
						"wpmldisable" => 1,
						"default"     => "normal",
						"type"        => "select",
						"tags"        => array(
							"id"   => "builder_date_theme_position",
							"name" => "tm_meta[tmfbuilder][date_theme_position][]",
						),
						"options"     => array(
							array(
								"text"  => esc_html__( "Normal", 'woocommerce-tm-extra-product-options' ),
								"value" => "normal",
							),
							array(
								"text"  => esc_html__( "Top of screen", 'woocommerce-tm-extra-product-options' ),
								"value" => "top",
							),
							array(
								"text"  => esc_html__( "Bottom of screen", 'woocommerce-tm-extra-product-options' ),
								"value" => "bottom",
							),
						),
						"label"       => esc_html__( "Position", 'woocommerce-tm-extra-product-options' ),
						"desc"        => esc_html__( "Select the position of the datepicker.", 'woocommerce-tm-extra-product-options' ),
					),
					array(
						"id"          => "date_exlude_disabled",
						"wpmldisable" => 1,
						"default"     => "",
						"type"        => "checkbox",
						"tags"        => array(
							"value" => "1",
							"id"    => "builder_date_exlude_disabled",
							"name"  => "tm_meta[tmfbuilder][date_exlude_disabled][]",
						),
						"label"       => esc_html__( 'Exclude disabled', 'woocommerce-tm-extra-product-options' ),
						"desc"        => esc_html__( 'Enabling this will make it so that the Minimum and Maximum selectable date will not count the selected disabled weekdays.', 'woocommerce-tm-extra-product-options' ),
					),
					array(
						"id"          => "date_disabled_weekdays",
						"wpmldisable" => 1,
						"default"     => "",
						"type"        => "hidden",
						"tags"        => array(
							"class" => "tm-weekdays",
							"id"    => "builder_date_disabled_weekdays",
							"name"  => "tm_meta[tmfbuilder][date_disabled_weekdays][]",
							"value" => "",
						),
						"label"       => esc_html__( "Disable weekdays", 'woocommerce-tm-extra-product-options' ),
						"desc"        => esc_html__( "This allows you to disable all selected weekdays.", 'woocommerce-tm-extra-product-options' ),
						"extra"       => array( array( $this, "get_weekdays" ), array() ),
					),
					array(
						"id"          => "date_disabled_months",
						"wpmldisable" => 1,
						"default"     => "",
						"type"        => "hidden",
						"tags"        => array(
							"class" => "tm-months",
							"id"    => "builder_date_disabled_months",
							"name"  => "tm_meta[tmfbuilder][date_disabled_months][]",
							"value" => "",
						),
						"label"       => esc_html__( "Disable months", 'woocommerce-tm-extra-product-options' ),
						"desc"        => esc_html__( "This allows you to disable all selected months.", 'woocommerce-tm-extra-product-options' ),
						"extra"       => array( array( $this, "get_months" ), array() ),
					),
					array(
						"id"               => "date_tranlation_custom",
						"type"             => "custom",
						"label"            => esc_html__( 'Translations', 'woocommerce-tm-extra-product-options' ),
						"desc"             => "",
						"nowrap_end"       => 1,
						"noclear"          => 1,
						"message0x0_class" => "justify-content-flex-end",
					),
					array(
						"id"                   => "date_tranlation_day",
						"default"              => "",
						"type"                 => "text",
						"tags"                 => array(
							"class" => "t",
							"id"    => "builder_date_tranlation_day",
							"name"  => "tm_meta[tmfbuilder][date_tranlation_day][]",
							"value" => "",
						),
						"label"                => "",
						"desc"                 => "",
						"prepend_element_html" => '<span class="prepend_span">' . esc_html__( 'Day', 'woocommerce-tm-extra-product-options' ) . '</span> ',
						"nowrap_start"         => 1,
						"nowrap_end"           => 1,
					),
					array(
						"id"                   => "date_tranlation_month",
						"default"              => "",
						"type"                 => "text",
						"nowrap_start"         => 1,
						"nowrap_end"           => 1,
						"tags"                 => array(
							"class" => "t",
							"id"    => "builder_date_tranlation_month",
							"name"  => "tm_meta[tmfbuilder][date_tranlation_month][]",
							"value" => "",
						),
						"label"                => "",
						"desc"                 => "",
						"prepend_element_html" => '<span class="prepend_span">' . esc_html__( 'Month', 'woocommerce-tm-extra-product-options' ) . '</span> ',
					),
					array(
						"id"                   => "date_tranlation_year",
						"default"              => "",
						"type"                 => "text",
						"tags"                 => array(
							"class" => "t",
							"id"    => "builder_date_tranlation_year",
							"name"  => "tm_meta[tmfbuilder][date_tranlation_year][]",
							"value" => "",
						),
						"label"                => "",
						"desc"                 => "",
						"prepend_element_html" => '<span class="prepend_span">' . esc_html__( 'Year', 'woocommerce-tm-extra-product-options' ) . '</span> ',
						"nowrap_start"         => 1,
					),
				)
			),

			"time" => $this->add_element(
				"time",
				array(
					"enabled",
					"required",
					"price_type6",
					"price",
					"sale_price",
					"fee",
					"hide_amount",
					"text_before_price",
					"text_after_price",
					"quantity",
					$this->add_setting_button_type(
						"time",
						array(
							"message0x0_class" => "tm-epo-switch-wrapper",
							"type"             => "radio",
							"tags"             => array(
								"class" => "time-button-type",
								"id"    => "builder_time_button_type",
								"name"  => "tm_meta[tmfbuilder][time_button_type][]",
							),
							"default"          => "custom",
							"options"          => array(
								array(
									"text"  => esc_html__( 'System style', 'woocommerce-tm-extra-product-options' ),
									"value" => "system",
								),
								array(
									"text"  => esc_html__( 'Custom style', 'woocommerce-tm-extra-product-options' ),
									"value" => "custom",
								),
							),
							"label"            => esc_html__( 'Time picker style', 'woocommerce-tm-extra-product-options' ),
						)
					),
					$this->add_setting_time_format(
						"time",
						array(
							"required" => array(
								".time-button-type" => array(
									"operator" => "is",
									"value"    => "custom",
								),
							),
						)
					),
					$this->add_setting_custom_time_format(
						"time",
						array(
							"required" => array(
								".time-button-type" => array(
									"operator" => "is",
									"value"    => "custom",
								),
							),
						)
					),

					array(
						"id"          => "time_min_time",
						"wpmldisable" => 1,
						"default"     => "",
						"type"        => "text",
						"tags"        => array(
							"class" => "t",
							"id"    => "builder_time_min_time",
							"name"  => "tm_meta[tmfbuilder][time_min_time][]",
							"value" => "",
						),
						"label"       => esc_html__( 'Minimum selectable time', 'woocommerce-tm-extra-product-options' ),
						"desc"        => esc_html__( 'Enter the time the following format: 8:00 am', 'woocommerce-tm-extra-product-options' ),
						"required"    => array(
							".time-button-type" => array(
								"operator" => "is",
								"value"    => "custom",
							),
						),
					),
					array(
						"id"          => "time_max_time",
						"wpmldisable" => 1,
						"default"     => "",
						"type"        => "text",
						"tags"        => array(
							"class" => "t",
							"id"    => "builder_time_max_time",
							"name"  => "tm_meta[tmfbuilder][time_max_time][]",
							"value" => "",
						),
						"label"       => esc_html__( 'Maximum selectable time', 'woocommerce-tm-extra-product-options' ),
						"desc"        => esc_html__( 'Enter the time the following format: 8:00 am', 'woocommerce-tm-extra-product-options' ),
						"required"    => array(
							".time-button-type" => array(
								"operator" => "is",
								"value"    => "custom",
							),
						),
					),
					array(
						"id"          => "time_theme",
						"wpmldisable" => 1,
						"default"     => "epo",
						"type"        => "select",
						"tags"        => array(
							"id"   => "builder_time_theme",
							"name" => "tm_meta[tmfbuilder][time_theme][]",
						),
						"options"     => array(
							array(
								"text"  => esc_html__( "Epo White", 'woocommerce-tm-extra-product-options' ),
								"value" => "epo",
							),
							array(
								"text"  => esc_html__( "Epo Black", 'woocommerce-tm-extra-product-options' ),
								"value" => "epo-black",
							),
						),
						"label"       => esc_html__( "Theme", 'woocommerce-tm-extra-product-options' ),
						"desc"        => esc_html__( "Select the theme for the timepicker.", 'woocommerce-tm-extra-product-options' ),
						"required"    => array(
							".time-button-type" => array(
								"operator" => "is",
								"value"    => "custom",
							),
						),
					),
					array(
						"id"          => "time_theme_size",
						"wpmldisable" => 1,
						"default"     => "medium",
						"type"        => "select",
						"tags"        => array(
							"id"   => "builder_time_theme_size",
							"name" => "tm_meta[tmfbuilder][time_theme_size][]",
						),
						"options"     => array(
							array(
								"text"  => esc_html__( "Small", 'woocommerce-tm-extra-product-options' ),
								"value" => "small",
							),
							array(
								"text"  => esc_html__( "Medium", 'woocommerce-tm-extra-product-options' ),
								"value" => "medium",
							),
							array(
								"text"  => esc_html__( "Large", 'woocommerce-tm-extra-product-options' ),
								"value" => "large",
							),
						),
						"label"       => esc_html__( "Size", 'woocommerce-tm-extra-product-options' ),
						"desc"        => esc_html__( "Select the size of the timepicker.", 'woocommerce-tm-extra-product-options' ),
						"required"    => array(
							".time-button-type" => array(
								"operator" => "is",
								"value"    => "custom",
							),
						),
					),
					array(
						"id"          => "time_theme_position",
						"wpmldisable" => 1,
						"default"     => "normal",
						"type"        => "select",
						"tags"        => array(
							"id"   => "builder_time_theme_position",
							"name" => "tm_meta[tmfbuilder][time_theme_position][]",
						),
						"options"     => array(
							array(
								"text"  => esc_html__( "Normal", 'woocommerce-tm-extra-product-options' ),
								"value" => "normal",
							),
							array(
								"text"  => esc_html__( "Top of screen", 'woocommerce-tm-extra-product-options' ),
								"value" => "top",
							),
							array(
								"text"  => esc_html__( "Bottom of screen", 'woocommerce-tm-extra-product-options' ),
								"value" => "bottom",
							),
						),
						"label"       => esc_html__( "Position", 'woocommerce-tm-extra-product-options' ),
						"desc"        => esc_html__( "Select the position of the timepicker.", 'woocommerce-tm-extra-product-options' ),
						"required"    => array(
							".time-button-type" => array(
								"operator" => "is",
								"value"    => "custom",
							),
						),
					),
					array(
						"id"               => "time_tranlation_custom",
						"type"             => "custom",
						"label"            => esc_html__( 'Translations', 'woocommerce-tm-extra-product-options' ),
						"desc"             => "",
						"nowrap_end"       => 1,
						"noclear"          => 1,
						"message0x0_class" => "justify-content-flex-end",
						"required"         => array(
							".time-button-type" => array(
								"operator" => "is",
								"value"    => "custom",
							),
						),
					),
					array(
						"id"                   => "time_tranlation_hour",
						"default"              => "",
						"type"                 => "text",
						"tags"                 => array(
							"class" => "t",
							"id"    => "builder_time_tranlation_hour",
							"name"  => "tm_meta[tmfbuilder][time_tranlation_hour][]",
							"value" => "",
						),
						"label"                => "",
						"desc"                 => "",
						"prepend_element_html" => '<span class="prepend_span">' . esc_html__( 'Hour', 'woocommerce-tm-extra-product-options' ) . '</span> ',
						"nowrap_start"         => 1,
						"nowrap_end"           => 1,
						"required"             => array(
							".time-button-type" => array(
								"operator" => "is",
								"value"    => "custom",
							),
						),
					),
					array(
						"id"                   => "time_tranlation_minute",
						"default"              => "",
						"type"                 => "text",
						"nowrap_start"         => 1,
						"nowrap_end"           => 1,
						"tags"                 => array(
							"class" => "t",
							"id"    => "builder_time_tranlation_month",
							"name"  => "tm_meta[tmfbuilder][time_tranlation_minute][]",
							"value" => "",
						),
						"label"                => "",
						"desc"                 => "",
						"prepend_element_html" => '<span class="prepend_span">' . esc_html__( 'Minute', 'woocommerce-tm-extra-product-options' ) . '</span> ',
						"required"             => array(
							".time-button-type" => array(
								"operator" => "is",
								"value"    => "custom",
							),
						),
					),
					array(
						"id"                   => "time_tranlation_second",
						"default"              => "",
						"type"                 => "text",
						"tags"                 => array(
							"class" => "t",
							"id"    => "builder_time_tranlation_second",
							"name"  => "tm_meta[tmfbuilder][time_tranlation_second][]",
							"value" => "",
						),
						"label"                => "",
						"desc"                 => "",
						"prepend_element_html" => '<span class="prepend_span">' . esc_html__( 'Second', 'woocommerce-tm-extra-product-options' ) . '</span> ',
						"nowrap_start"         => 1,
						"required"             => array(
							".time-button-type" => array(
								"operator" => "is",
								"value"    => "custom",
							),
						),
					),
				)
			),

			"range" => $this->add_element(
				"range",
				array(
					"enabled",
					"required",
					"price_type7",
					"price",
					"sale_price",
					"fee",
					"hide_amount",
					"text_before_price",
					"text_after_price",
					"quantity",
					"rangemin",
					"rangemax",
					"rangestep",
					"show_picker_value",
					"pips",
					"noofpips",
					"default_value",
				)
			),

			"color" => $this->add_element(
				"color",
				array(
					"enabled",
					"required",
					"price_type6",
					"price",
					"sale_price",
					"fee",
					"hide_amount",
					"text_before_price",
					"text_after_price",
					"quantity",
					"default_value",
				)
			),

			"variations" => $this->add_element(
				"variations",
				array( "variations_disabled", "variations_options" )
			),
			"product"    => $this->add_element(
				"product",
				array(
					"_tabs" =>
						array(
							"general_options"  =>
								array(
									"enabled",
									"required",
									"hide_amount",

									array(
										"id"               => "product_mode",
										"wpmldisable"      => 1,
										"default"          => "products",
										"message0x0_class" => "tm-epo-switch-wrapper",
										"type"             => "radio",
										"tags"             => array(
											"class" => "product-mode",
											"id"    => "builder_product_mode",
											"name"  => "tm_meta[tmfbuilder][product_mode][]",
										),
										"options"          => array(
											array(
												"text"  => esc_html__( "Products", 'woocommerce-tm-extra-product-options' ),
												"value" => "products",
											),
											array(
												"text"  => esc_html__( "Single Product", 'woocommerce-tm-extra-product-options' ),
												"value" => "product",
											),
											array(
												"text"  => esc_html__( "Categories", 'woocommerce-tm-extra-product-options' ),
												"value" => "categories",
											),
										),
										"label"            => esc_html__( "Select mode", 'woocommerce-tm-extra-product-options' ),
										"desc"             => esc_html__( "Whether to include specific products or categories.", 'woocommerce-tm-extra-product-options' ),
									),

									array(
										"id"          => "product_categoryids",
										"wpmldisable" => 1,
										"default"     => "",
										"type"        => "select",
										"multiple"    => "multiple",
										"fill"        => "category",
										"tags"        => array(
											"data-placeholder" => esc_attr( 'Search for a category ...', 'woocommerce-tm-extra-product-options' ),
											"data-action"      => "woocommerce_json_search_categories",
											"class"            => "wc-category-search product-categories-selector",
											"id"               => "builder_product_categoryids",
											"name"             => "tm_meta[tmfbuilder][product_categoryids][]",
										),
										"options"     => $this->fetch_product_categories_array(),
										"label"       => esc_html__( "Select categories", 'woocommerce-tm-extra-product-options' ),
										"desc"        => esc_html__( "Add categories to include all their associated products.", 'woocommerce-tm-extra-product-options' ),
										"required"    => array(
											".product-mode" => array(
												"operator" => "is",
												"value"    => "categories",
											),
										),
									),

									array(
										"id"          => "product_productids",
										"wpmldisable" => 1,
										"default"     => "",
										"type"        => "select",
										"multiple"    => "multiple",
										"fill"        => "product",
										"tags"        => array(
											"data-placeholder" => esc_attr( 'Search for a product ...', 'woocommerce-tm-extra-product-options' ),
											"data-action"      => "woocommerce_json_search_products_and_variations",
											"data-sortable"    => "true",
											"class"            => "wc-product-search product-products-selector",
											"id"               => "builder_product_productids",
											"name"             => "tm_meta[tmfbuilder][product_productids][]",
										),
										"options"     => array(),
										"label"       => esc_html__( "Select products", 'woocommerce-tm-extra-product-options' ),
										"desc"        => esc_html__( "Add the products individually.", 'woocommerce-tm-extra-product-options' ),
										"required"    => array(
											".product-mode" => array(
												"operator" => "is",
												"value"    => array( "products", "product" ),
											),
										),
									),

									array(
										"id"          => "product_orderby",
										"wpmldisable" => 1,
										"default"     => "ID",
										"type"        => "select",
										"tags"        => array(
											"data-placeholder" => esc_attr( 'Choose a value', 'woocommerce-tm-extra-product-options' ),
											"class"            => "fullwidth",
											"id"               => "builder_product_orderby",
											"name"             => "tm_meta[tmfbuilder][product_orderby][]",
										),
										"options"     => array(
											array(
												"text"  => esc_html__( "Default", 'woocommerce-tm-extra-product-options' ),
												"value" => "none",
											),
											array(
												"text"  => esc_html__( "Base price", 'woocommerce-tm-extra-product-options' ),
												"value" => "baseprice",
											),
											array(
												"text"  => esc_html__( "ID", 'woocommerce-tm-extra-product-options' ),
												"value" => "ID",
											),
											array(
												"text"  => esc_html__( "Title", 'woocommerce-tm-extra-product-options' ),
												"value" => "title",
											),
											array(
												"text"  => esc_html__( "Date", 'woocommerce-tm-extra-product-options' ),
												"value" => "date",
											),
											array(
												"text"  => esc_html__( "Name", 'woocommerce-tm-extra-product-options' ),
												"value" => "name",
											),
											array(
												"text"  => esc_html__( "Menu Order", 'woocommerce-tm-extra-product-options' ),
												"value" => "menu_order",
											),
											array(
												"text"  => esc_html__( "Random", 'woocommerce-tm-extra-product-options' ),
												"value" => "rand",
											),
										),
										"label"       => esc_html__( "Order by", 'woocommerce-tm-extra-product-options' ),
										"desc"        => esc_html__( "Select the parameter which the products will be sorted by.", 'woocommerce-tm-extra-product-options' ),
										"required"    => array(
											".product-mode" => array(
												"operator" => "isnot",
												"value"    => "product",
											),
										),
									),

									array(
										"id"               => "product_order",
										"wpmldisable"      => 1,
										"default"          => "asc",
										"message0x0_class" => "tm-epo-switch-wrapper",
										"type"             => "radio",
										"tags"             => array(
											"class" => "product-order",
											"id"    => "builder_product_order",
											"name"  => "tm_meta[tmfbuilder][product_order][]",
										),
										"options"          => array(
											array(
												"text"  => esc_html__( "Ascending", 'woocommerce-tm-extra-product-options' ),
												"value" => "asc",
											),
											array(
												"text"  => esc_html__( "Descending", 'woocommerce-tm-extra-product-options' ),
												"value" => "desc",
											),
										),
										"label"            => esc_html__( "Order", 'woocommerce-tm-extra-product-options' ),
										"desc"             => esc_html__( "Select the sorting order of the products.", 'woocommerce-tm-extra-product-options' ),
										"required"         => array(
											".product-mode"    => array(
												"operator" => "isnot",
												"value"    => "product",
											),
											".product-orderby" => array(
												"operator" => "isnot",
												"value"    => "none",
											),
										),
									),

									array(
										"id"          => "product_default_value",
										"wpmldisable" => 1,
										"default"     => "",
										"type"        => "select",
										"tags"        => array(
											"data-placeholder" => esc_attr( 'Select a product', 'woocommerce-tm-extra-product-options' ),
											"class"            => "wc-product-search product-default-value-search",
											"id"               => "builder_product_productids",
											"name"             => "tm_meta[tmfbuilder][product_default_value][]",
										),
										"options"     => array(),
										"label"       => esc_html__( "Default product", 'woocommerce-tm-extra-product-options' ),
										"desc"        => esc_html__( "Select the product that will be pre-selected.", 'woocommerce-tm-extra-product-options' ),
									),

									array(
										"id"               => "product_layout_mode",
										"wpmldisable"      => 1,
										"default"          => "dropdown",
										"message0x0_class" => "tm-epo-switch-wrapper",
										"type"             => "radio",
										"tags"             => array(
											"class" => "product-layout-mode",
											"id"    => "builder_product_layout_mode",
											"name"  => "tm_meta[tmfbuilder][product_layout_mode][]",
										),
										"options"          => array(
											array(
												"text"  => esc_html__( "Dropdown", 'woocommerce-tm-extra-product-options' ),
												"value" => "dropdown",
											),
											array(
												"text"  => esc_html__( "Radio buttons", 'woocommerce-tm-extra-product-options' ),
												"value" => "radio",
											),
											array(
												"text"  => esc_html__( "Thumbnails", 'woocommerce-tm-extra-product-options' ),
												"value" => "thumbnail",
											),
										),
										"label"            => esc_html__( "Layout mode", 'woocommerce-tm-extra-product-options' ),
										"desc"             => esc_html__( "Select how the products will be presented.", 'woocommerce-tm-extra-product-options' ),
										"required"         => array(
											".product-mode" => array(
												"operator" => "isnot",
												"value"    => "product",
											),
										),
									),

									$this->add_setting_items_per_row(
										"product",
										array(),
										array(
											".product-layout-mode" => array(
												"operator" => "is",
												"value"    => "thumbnail",
											),
											".product-mode"        => array(
												"operator" => "isnot",
												"value"    => "product",
											),
										)
									),

									array(
										"placeholder",
										array(
											"label"    => esc_html__( 'Dropdown Placeholder', 'woocommerce-tm-extra-product-options' ),
											"desc"     => esc_html__( 'Enter the placeholder for the dropdown.', 'woocommerce-tm-extra-product-options' ),
											"required" => array(
												".product-layout-mode" => array(
													"operator" => "is",
													"value"    => "dropdown",
												),
											),
										),
									),

									$this->add_setting_min(
										"product_quantity",
										array(
											"extra_tags" => array( "min" => 0 ),
											"default"    => "",
											"label"      => esc_html__( 'Minimum quantity', 'woocommerce-tm-extra-product-options' ),
										),
										false
									),
									$this->add_setting_max(
										"product_quantity",
										array(
											"extra_tags" => array( "min" => 0 ),
											"default"    => "",
											"label"      => esc_html__( 'Maximum quantity', 'woocommerce-tm-extra-product-options' ),
										),
										false
									),
									array(
										"id"          => "product_shipped_individually",
										"wpmldisable" => 1,
										"default"     => "",
										"type"        => "checkbox",
										"tags"        => array(
											"value" => "1",
											"id"    => "builder_product_shipped_individually",
											"name"  => "tm_meta[tmfbuilder][product_shipped_individually][]",
										),
										"label"       => esc_html__( 'Shipped individually', 'woocommerce-tm-extra-product-options' ),
										"desc"        => esc_html__( 'If this is enabled the included product is not shipped with the main product.', 'woocommerce-tm-extra-product-options' ),
									),
									array(
										"id"          => "product_maintain_weight",
										"wpmldisable" => 1,
										"default"     => "",
										"type"        => "checkbox",
										"tags"        => array(
											"value" => "1",
											"id"    => "builder_product_maintain_weight",
											"name"  => "tm_meta[tmfbuilder][product_maintain_weight][]",
										),
										"label"       => esc_html__( 'Maintain weight', 'woocommerce-tm-extra-product-options' ),
										"desc"        => esc_html__( 'If this is enabled the included product will add its weight to the main product.', 'woocommerce-tm-extra-product-options' ),
										"required"    => array(
											"#builder_product_shipped_individually" => array(
												"operator" => "isnot",
												"value"    => "1",
											),
										),
									),
									array(
										"id"          => "product_priced_individually",
										"wpmldisable" => 1,
										"default"     => "",
										"type"        => "checkbox",
										"tags"        => array(
											"value" => "1",
											"id"    => "builder_product_priced_individually",
											"name"  => "tm_meta[tmfbuilder][product_priced_individually][]",
										),
										"label"       => esc_html__( 'Priced individually', 'woocommerce-tm-extra-product-options' ),
										"desc"        => esc_html__( 'If this is enabled the included product will maintain its own price.', 'woocommerce-tm-extra-product-options' ),
									),
									array(
										"id"               => "product_discount_type",
										"wpmldisable"      => 1,
										"default"          => "percent",
										"message0x0_class" => "tm-epo-switch-wrapper",
										"type"             => "radio",
										"tags"             => array(
											"id"   => "builder_product_discount_type",
											"name" => "tm_meta[tmfbuilder][product_discount_type][]",
										),
										"options"          => array(
											array(
												"text"  => esc_html__( "Percentage", 'woocommerce-tm-extra-product-options' ),
												"value" => "percent",
											),
											array(
												"text"  => esc_html__( "Fixed amount", 'woocommerce-tm-extra-product-options' ),
												"value" => "fixed",
											),
										),
										"label"            => esc_html__( "Discount type", 'woocommerce-tm-extra-product-options' ),
										"desc"             => esc_html__( "Select the discount type. The discount applies to the final product price. If the product has extra options then the discount applies to the amount after the options have been added to the product price.", 'woocommerce-tm-extra-product-options' ),
										"required"         => array(
											"#builder_product_priced_individually" => array(
												"operator" => "is",
												"value"    => "1",
											),
										),
									),
									array(
										"id"       => "product_discount",
										"default"  => "",
										"type"     => "text",
										"tags"     => array(
											"class" => "t",
											"id"    => "builder_product_discount",
											"name"  => "tm_meta[tmfbuilder][product_discount][]",
											"value" => "",
										),
										"label"    => esc_html__( 'Discount', 'woocommerce-tm-extra-product-options' ),
										"desc"     => esc_html__( 'Enter the discount amount.', 'woocommerce-tm-extra-product-options' ),
										"required" => array(
											"#builder_product_priced_individually" => array(
												"operator" => "is",
												"value"    => "1",
											),
										),
									),

								),
							"advanced_options" =>
								array(
									array(
										"id"          => "product_show_image",
										"wpmldisable" => 1,
										"default"     => "1",
										"type"        => "checkbox",
										"tags"        => array(
											"value" => "1",
											"id"    => "builder_product_show_image",
											"name"  => "tm_meta[tmfbuilder][product_show_image][]",
										),
										"label"       => esc_html__( 'Show image', 'woocommerce-tm-extra-product-options' ),
										"desc"        => esc_html__( 'Enable to show the image of the associated product.', 'woocommerce-tm-extra-product-options' ),
									),
									array(
										"id"          => "product_show_title",
										"wpmldisable" => 1,
										"default"     => "1",
										"type"        => "checkbox",
										"tags"        => array(
											"value" => "1",
											"id"    => "builder_product_show_title",
											"name"  => "tm_meta[tmfbuilder][product_show_title][]",
										),
										"label"       => esc_html__( 'Show title', 'woocommerce-tm-extra-product-options' ),
										"desc"        => esc_html__( 'Enable to show the title of the associated product.', 'woocommerce-tm-extra-product-options' ),
									),
									array(
										"id"          => "product_show_price",
										"wpmldisable" => 1,
										"default"     => "1",
										"type"        => "checkbox",
										"tags"        => array(
											"value" => "1",
											"id"    => "builder_product_show_price",
											"name"  => "tm_meta[tmfbuilder][product_show_price][]",
										),
										"label"       => esc_html__( 'Show price', 'woocommerce-tm-extra-product-options' ),
										"desc"        => esc_html__( 'Enable to show the price of the associated product.', 'woocommerce-tm-extra-product-options' ),
									),
									array(
										"id"          => "product_show_description",
										"wpmldisable" => 1,
										"default"     => "1",
										"type"        => "checkbox",
										"tags"        => array(
											"value" => "1",
											"id"    => "builder_product_show_description",
											"name"  => "tm_meta[tmfbuilder][product_show_description][]",
										),
										"label"       => esc_html__( 'Show description', 'woocommerce-tm-extra-product-options' ),
										"desc"        => esc_html__( 'Enable to show the description of the associated product.', 'woocommerce-tm-extra-product-options' ),
									),
									array(
										"id"          => "product_show_meta",
										"wpmldisable" => 1,
										"default"     => "1",
										"type"        => "checkbox",
										"tags"        => array(
											"value" => "1",
											"id"    => "builder_product_show_meta",
											"name"  => "tm_meta[tmfbuilder][product_show_meta][]",
										),
										"label"       => esc_html__( 'Show meta', 'woocommerce-tm-extra-product-options' ),
										"desc"        => esc_html__( 'Enable to show the meta of the associated product.', 'woocommerce-tm-extra-product-options' ),
									),
								),
						),
				),
				false,
				array(
					"label_options"        => 1,
					"general_options"      => 1,
					"advanced_options"     => 1,
					"conditional_logic"    => 1,
					"css_settings"         => 0,
					"woocommerce_settings" => 0,
				),
				array(
					"advanced_options" => array(
						"name" => esc_html__( "Advanced options", 'woocommerce-tm-extra-product-options' ),
						"icon" => "tcfa tcfa-cog",
						"slug" => "tma-tab-advanced",
					),
				)
			),

		);

		$this->elements_array = array_merge(
			$this->elements_array,
			$this->elements_array_addons );

		$this->elements_array = apply_filters( 'wc_epo_builder_after_element_array', $this->elements_array );

	}

	/**
	 * Fetch product categories
	 * for use in a select box
	 *
	 * @since  5.0
	 * @access public
	 */
	public function fetch_product_categories_array() {
		$list               = array();
		$product_categories = ( array ) get_terms( 'product_cat', array( 'get' => 'all' ) );

		foreach ( $product_categories as $product_category ) {
			$list[] = array(
				"text"  => $product_category->name,
				"value" => $product_category->term_id,
			);
		}

		return $list;
	}

	/**
	 * Variation disabled setting
	 *
	 * @since 1.0
	 */
	public final function add_setting_variations_disabled( $name = "" ) {
		// this field must be unique, no multiples allowed or have sense
		return array(
			"id"          => $name . "_disabled",
			"wpmldisable" => 1,
			"nodiv"       => 1,
			"default"     => "5",
			"type"        => "hidden",
			"tags"        => array(
				"class" => "tm-variations-disabled",
				"id"    => "builder_" . $name . "_disabled",
				"name"  => "tm_meta[tmfbuilder][" . $name . "_disabled]",
				"value" => "",
			),
			"label"       => "",
			"desc"        => "",
		);
	}

	/**
	 * Pips setting
	 *
	 * @since 1.0
	 */
	public final function add_setting_pips( $name = "" ) {
		return array(
			"id"          => $name . "_pips",
			"wpmldisable" => 1,
			"default"     => "",
			"type"        => "select",
			"tags"        => array(
				"id"   => "builder_" . $name . "_pips",
				"name" => "tm_meta[tmfbuilder][" . $name . "_pips][]",
			),
			"options"     => array(
				array( "text" => esc_html__( "No", 'woocommerce-tm-extra-product-options' ), "value" => "" ),
				array( "text" => esc_html__( "Yes", 'woocommerce-tm-extra-product-options' ), "value" => "yes" ),
			),
			"label"       => esc_html__( "Enable points display?", 'woocommerce-tm-extra-product-options' ),
			"desc"        => esc_html__( "This allows you to generate points along the range picker.", 'woocommerce-tm-extra-product-options' ),
		);
	}

	/**
	 * Number of points setting
	 *
	 * @since 1.0
	 */
	public final function add_setting_noofpips( $name = "" ) {
		return array(
			"id"          => $name . "_noofpips",
			"wpmldisable" => 1,
			"default"     => "10",
			"type"        => "number",
			"tags"        => array(
				"class" => "n",
				"id"    => "builder_" . $name . "_noofpips",
				"name"  => "tm_meta[tmfbuilder][" . $name . "_noofpips][]",
				"value" => "",
			),
			"label"       => esc_html__( 'Number of points', 'woocommerce-tm-extra-product-options' ),
			"desc"        => esc_html__( 'Enter the number of values for the points display.', 'woocommerce-tm-extra-product-options' ),
			"required"    => array(
				"#builder_range_pips" => array(
					"operator" => "is",
					"value"    => "yes",
				),
			),
		);
	}

	/**
	 * Show value on setting
	 *
	 * @since 1.0
	 */
	public final function add_setting_show_picker_value( $name = "" ) {
		return array(
			"id"          => $name . "_show_picker_value",
			"wpmldisable" => 1,
			"default"     => "",
			"type"        => "select",
			"tags"        => array(
				"id"   => "builder_" . $name . "_show_picker_value",
				"name" => "tm_meta[tmfbuilder][" . $name . "_show_picker_value][]",
			),
			"options"     => array(
				array( "text" => esc_html__( "Tooltip", 'woocommerce-tm-extra-product-options' ), "value" => "" ),
				array( "text" => esc_html__( "Left side", 'woocommerce-tm-extra-product-options' ), "value" => "left" ),
				array(
					"text"  => esc_html__( "Right side", 'woocommerce-tm-extra-product-options' ),
					"value" => "right",
				),
				array(
					"text"  => esc_html__( "Tooltip and Left side", 'woocommerce-tm-extra-product-options' ),
					"value" => "tleft",
				),
				array(
					"text"  => esc_html__( "Tooltip and Right side", 'woocommerce-tm-extra-product-options' ),
					"value" => "tright",
				),
			),
			"label"       => esc_html__( "Show value on", 'woocommerce-tm-extra-product-options' ),
			"desc"        => esc_html__( "Select how to show the value of the range picker.", 'woocommerce-tm-extra-product-options' ),
		);
	}

	/**
	 * Range picker Min value setting
	 *
	 * @since 5.0
	 */
	public final function add_setting_rangemin( $name = "" ) {

		return $this->add_setting_min( $name, array(), false );

	}

	/**
	 * Range picker Max value setting
	 *
	 * @since 5.0
	 */
	public final function add_setting_rangemax( $name = "" ) {

		return $this->add_setting_max( $name, array(), false );

	}

	/**
	 * Step value setting
	 *
	 * @since 1.0
	 */
	public final function add_setting_rangestep( $name = "" ) {
		return array(
			"id"          => $name . "_step",
			"wpmldisable" => 1,
			"default"     => "1",
			"type"        => "text",
			"tags"        => array(
				"class" => "n",
				"id"    => "builder_" . $name . "_step",
				"name"  => "tm_meta[tmfbuilder][" . $name . "_step][]",
				"value" => "",
			),
			"label"       => esc_html__( 'Step value', 'woocommerce-tm-extra-product-options' ),
			"desc"        => esc_html__( 'Enter the step for the handle.', 'woocommerce-tm-extra-product-options' ),
		);
	}

	/**
	 * Validate as setting
	 *
	 * @since 1.0
	 */
	public final function add_setting_validation1( $name = "" ) {
		return array(
			"id"          => $name . "_validation1",
			"wpmldisable" => 1,
			"default"     => "",
			"type"        => "select",
			"tags"        => array(
				"class" => "tc-validateas",
				"id"    => "builder_" . $name . "_validation1",
				"name"  => "tm_meta[tmfbuilder][" . $name . "_validation1][]",
			),
			"options"     => array(
				array( "text" => esc_html__( 'No validation', 'woocommerce-tm-extra-product-options' ), "value" => '' ),
				array( "text" => esc_html__( 'Email', 'woocommerce-tm-extra-product-options' ), "value" => 'email' ),
				array( "text" => esc_html__( 'Url', 'woocommerce-tm-extra-product-options' ), "value" => 'url' ),
				array( "text" => esc_html__( 'Number', 'woocommerce-tm-extra-product-options' ), "value" => 'number' ),
				array( "text" => esc_html__( 'Digits', 'woocommerce-tm-extra-product-options' ), "value" => 'digits' ),
				array(
					"text"  => esc_html__( 'Letters only', 'woocommerce-tm-extra-product-options' ),
					"value" => 'lettersonly',
				),
				array(
					"text"  => esc_html__( 'Letters or Space only', 'woocommerce-tm-extra-product-options' ),
					"value" => 'lettersspaceonly',
				),
				array(
					"text"  => esc_html__( 'Alphanumeric', 'woocommerce-tm-extra-product-options' ),
					"value" => 'alphanumeric',
				),
				array(
					"text"  => esc_html__( 'Alphanumeric Unicode', 'woocommerce-tm-extra-product-options' ),
					"value" => 'alphanumericunicode',
				),
				array(
					"text"  => esc_html__( 'Alphanumeric Unicode or Space', 'woocommerce-tm-extra-product-options' ),
					"value" => 'alphanumericunicodespace',
				),
			),
			"label"       => esc_html__( 'Validate as', 'woocommerce-tm-extra-product-options' ),
			"desc"        => esc_html__( 'Choose whether the field will be validated against the choosen method.', 'woocommerce-tm-extra-product-options' ),
		);
	}

	/**
	 * Required setting
	 *
	 * @since 1.0
	 */
	public final function add_setting_required( $name = "" ) {
		return array(
			"id"          => $name . "_required",
			"wpmldisable" => 1,
			"default"     => "",
			"type"        => "checkbox",
			"tags"        => array(
				"value" => "1",
				"id"    => "builder_" . $name . "_required",
				"name"  => "tm_meta[tmfbuilder][" . $name . "_required][]",
			),
			"label"       => esc_html__( 'Required', 'woocommerce-tm-extra-product-options' ),
			"desc"        => esc_html__( 'Choose whether the user must fill out this field or not.', 'woocommerce-tm-extra-product-options' ),
		);
	}

	/**
	 * Enabled setting
	 *
	 * @since 1.0
	 */
	public final function add_setting_enabled( $name = "" ) {
		return array(
			"id"          => $name . "_enabled",
			"wpmldisable" => 1,
			"default"     => "1",
			"type"        => "checkbox",
			"tags"        => array(
				"value" => "1",
				"class" => "is_enabled",
				"id"    => "builder_" . $name . "_required",
				"name"  => "tm_meta[tmfbuilder][" . $name . "_enabled][]",
			),
			"label"       => esc_html__( 'Enabled', 'woocommerce-tm-extra-product-options' ),
			"desc"        => esc_html__( 'Choose whether the option is enabled or not.', 'woocommerce-tm-extra-product-options' ),
		);
	}

	/**
	 * Price setting
	 *
	 * @since 1.0
	 */
	public final function add_setting_price( $name = "" ) {
		return array(
			"id"       => $name . "_price",
			"default"  => "",
			"type"     => "text",
			"tags"     => array(
				"class" => "t tc-element-setting-price",
				"id"    => "builder_" . $name . "_price",
				"name"  => "tm_meta[tmfbuilder][" . $name . "_price][]",
				"value" => "",
				"step"  => "any",
			),
			"label"    => esc_html__( 'Price', 'woocommerce-tm-extra-product-options' ),
			"desc"     => esc_html__( 'Enter the price for this field or leave it blank for no price.', 'woocommerce-tm-extra-product-options' ),
			"required" => array(
				".tm-pricetype-selector" => array(
					"operator" => "isnot",
					"value"    => "currentstep",
				),
			),
		);
	}

	/**
	 * Sale price setting
	 *
	 * @since 1.0
	 */
	public final function add_setting_sale_price( $name = "" ) {
		return array(
			"id"       => $name . "_sale_price",
			"default"  => "",
			"type"     => "text",
			"tags"     => array(
				"class" => "t tc-element-setting-sale-price",
				"id"    => "builder_" . $name . "_sale_price",
				"name"  => "tm_meta[tmfbuilder][" . $name . "_sale_price][]",
				"value" => "",
				"step"  => "any",
			),
			"label"    => esc_html__( 'Sale Price', 'woocommerce-tm-extra-product-options' ),
			"desc"     => esc_html__( 'Enter the sale price for this field or leave it blankto use the default price.', 'woocommerce-tm-extra-product-options' ),
			"required" => array(
				".tm-pricetype-selector" => array(
					"operator" => "isnot",
					"value"    => "currentstep",
				),
			),
		);
	}

	/**
	 * Text after price setting
	 *
	 * @since 1.0
	 */
	public final function add_setting_text_after_price( $name = "" ) {
		return array(
			"id"      => $name . "_text_after_price",
			"default" => "",
			"type"    => "text",
			"tags"    => array(
				"class" => "t",
				"id"    => "builder_" . $name . "_text_after_price",
				"name"  => "tm_meta[tmfbuilder][" . $name . "_text_after_price][]",
				"value" => "",
			),
			"label"   => esc_html__( 'Text after Price', 'woocommerce-tm-extra-product-options' ),
			"desc"    => esc_html__( 'Enter a text to display after the price for this field or leave it blank for no text.', 'woocommerce-tm-extra-product-options' ),
		);
	}

	/**
	 * Text before price setting
	 *
	 * @since 1.0
	 */
	public final function add_setting_text_before_price( $name = "" ) {
		return array(
			"id"      => $name . "_text_before_price",
			"default" => "",
			"type"    => "text",
			"tags"    => array(
				"class" => "t",
				"id"    => "builder_" . $name . "_text_before_price",
				"name"  => "tm_meta[tmfbuilder][" . $name . "_text_before_price][]",
				"value" => "",
			),
			"label"   => esc_html__( 'Text before Price', 'woocommerce-tm-extra-product-options' ),
			"desc"    => esc_html__( 'Enter a text to display before the price for this field or leave it blank for no text.', 'woocommerce-tm-extra-product-options' ),
		);
	}

	/**
	 * Textarea price type setting
	 *
	 * @since 1.0
	 */
	public final function add_setting_price_type( $name = "" ) {

		$options = array(
			array( "text" => esc_html__( 'Fixed amount', 'woocommerce-tm-extra-product-options' ), "value" => "" ),
			array(
				"text"  => esc_html__( 'Percent of the original price', 'woocommerce-tm-extra-product-options' ),
				"value" => "percent",
			),
			array(
				"text"  => esc_html__( 'Percent of the original price + options', 'woocommerce-tm-extra-product-options' ),
				"value" => "percentcurrenttotal",
			),
			array(
				"text"  => esc_html__( 'Price per word', 'woocommerce-tm-extra-product-options' ),
				"value" => "word",
			),
			array(
				"text"  => esc_html__( "Percent of the original price per word", 'woocommerce-tm-extra-product-options' ),
				"value" => "wordpercent",
			),
			array(
				"text"  => esc_html__( 'Price per word (no n-th char)', 'woocommerce-tm-extra-product-options' ),
				"value" => "wordnon",
			),
			array(
				"text"  => esc_html__( "Percent of the original price per word (no n-th char)", 'woocommerce-tm-extra-product-options' ),
				"value" => "wordpercentnon",
			),
			array(
				"text"  => esc_html__( 'Price per char', 'woocommerce-tm-extra-product-options' ),
				"value" => "char",
			),
			array(
				"text"  => esc_html__( "Percent of the original price per char", 'woocommerce-tm-extra-product-options' ),
				"value" => "charpercent",
			),
			array(
				"text"  => esc_html__( 'Price per char (no first char)', 'woocommerce-tm-extra-product-options' ),
				"value" => "charnofirst",
			),
			array(
				"text"  => esc_html__( 'Price per char (no n-th char)', 'woocommerce-tm-extra-product-options' ),
				"value" => "charnon",
			),
			array(
				"text"  => esc_html__( 'Price per char (no n-th char and no spaces)', 'woocommerce-tm-extra-product-options' ),
				"value" => "charnonnospaces",
			),
			array(
				"text"  => esc_html__( "Percent of the original price per char (no first char)", 'woocommerce-tm-extra-product-options' ),
				"value" => "charpercentnofirst",
			),
			array(
				"text"  => esc_html__( "Percent of the original price per char (no n-th char)", 'woocommerce-tm-extra-product-options' ),
				"value" => "charpercentnon",
			),
			array(
				"text"  => esc_html__( "Percent of the original price per char (no n-th char and no spaces)", 'woocommerce-tm-extra-product-options' ),
				"value" => "charpercentnonnospaces",
			),
			array(
				"text"  => esc_html__( 'Price per char (no spaces)', 'woocommerce-tm-extra-product-options' ),
				"value" => "charnospaces",
			),
			array( "text" => esc_html__( 'Price per row', 'woocommerce-tm-extra-product-options' ), "value" => "row" ),
			array( "text" => esc_html__( 'Math formula', 'woocommerce-tm-extra-product-options' ), "value" => "math" ),
			array(
				"text"  => esc_html__( 'Fixed amount + options', 'woocommerce-tm-extra-product-options' ),
				"value" => "fixedcurrenttotal",
			),
		);

		$options = apply_filters( 'wc_epo_add_setting_price_type', $options, $name );

		return array(
			"id"          => $name . "_price_type",
			"wpmldisable" => 1,
			"default"     => "",
			"type"        => "select",
			"tags"        => array(
				"class" => "tm-pricetype-selector",
				"id"    => "builder_" . $name . "_price_type",
				"name"  => "tm_meta[tmfbuilder][" . $name . "_price_type][]",
			),
			"options"     => $options,
			"label"       => esc_html__( 'Price type', 'woocommerce-tm-extra-product-options' ),
		);
	}

	/**
	 * Textfield price type setting
	 *
	 * @since 1.0
	 */
	public final function add_setting_price_type2( $name = "" ) {

		$options = array(
			array( "text" => esc_html__( "Fixed amount", 'woocommerce-tm-extra-product-options' ), "value" => "" ),
			array( "text" => esc_html__( "Quantity", 'woocommerce-tm-extra-product-options' ), "value" => "step" ),
			array(
				"text"  => esc_html__( "Current value", 'woocommerce-tm-extra-product-options' ),
				"value" => "currentstep",
			),
			array(
				"text"  => esc_html__( "Percent of the original price", 'woocommerce-tm-extra-product-options' ),
				"value" => "percent",
			),
			array(
				"text"  => esc_html__( "Percent of the original price + options", 'woocommerce-tm-extra-product-options' ),
				"value" => "percentcurrenttotal",
			),
			array(
				"text"  => esc_html__( 'Price per word', 'woocommerce-tm-extra-product-options' ),
				"value" => "word",
			),
			array(
				"text"  => esc_html__( "Percent of the original price per word", 'woocommerce-tm-extra-product-options' ),
				"value" => "wordpercent",
			),
			array(
				"text"  => esc_html__( 'Price per word (no n-th char)', 'woocommerce-tm-extra-product-options' ),
				"value" => "wordnon",
			),
			array(
				"text"  => esc_html__( "Percent of the original price per word (no n-th char)", 'woocommerce-tm-extra-product-options' ),
				"value" => "wordpercentnon",
			),
			array(
				"text"  => esc_html__( "Price per char", 'woocommerce-tm-extra-product-options' ),
				"value" => "char",
			),
			array(
				"text"  => esc_html__( "Percent of the original price per char", 'woocommerce-tm-extra-product-options' ),
				"value" => "charpercent",
			),
			array(
				"text"  => esc_html__( 'Price per char (no first char)', 'woocommerce-tm-extra-product-options' ),
				"value" => "charnofirst",
			),
			array(
				"text"  => esc_html__( 'Price per char (no n-th char)', 'woocommerce-tm-extra-product-options' ),
				"value" => "charnon",
			),
			array(
				"text"  => esc_html__( 'Price per char (no n-th char and no spaces)', 'woocommerce-tm-extra-product-options' ),
				"value" => "charnonnospaces",
			),
			array(
				"text"  => esc_html__( "Percent of the original price per char (no first char)", 'woocommerce-tm-extra-product-options' ),
				"value" => "charpercentnofirst",
			),
			array(
				"text"  => esc_html__( "Percent of the original price per char (no n-th char)", 'woocommerce-tm-extra-product-options' ),
				"value" => "charpercentnon",
			),
			array(
				"text"  => esc_html__( "Percent of the original price per char (no n-th char and no spaces)", 'woocommerce-tm-extra-product-options' ),
				"value" => "charpercentnonnospaces",
			),
			array(
				"text"  => esc_html__( 'Price per char (no spaces)', 'woocommerce-tm-extra-product-options' ),
				"value" => "charnospaces",
			),
			array( "text" => esc_html__( 'Math formula', 'woocommerce-tm-extra-product-options' ), "value" => "math" ),
			array(
				"text"  => esc_html__( 'Fixed amount + options', 'woocommerce-tm-extra-product-options' ),
				"value" => "fixedcurrenttotal",
			),
		);

		$options = apply_filters( 'wc_epo_add_setting_price_type', $options, $name );

		return array(
			"id"          => $name . "_price_type",
			"wpmldisable" => 1,
			"default"     => "",
			"type"        => "select",
			"tags"        => array(
				"class" => "tm-pricetype-selector",
				"id"    => "builder_" . $name . "_price_type",
				"name"  => "tm_meta[tmfbuilder][" . $name . "_price_type][]",
			),
			"options"     => $options,
			"label"       => esc_html__( 'Price type', 'woocommerce-tm-extra-product-options' ),
		);
	}

	/**
	 * Fee setting
	 *
	 * @since 5.0
	 */
	public final function add_setting_fee( $name = "", $args = array() ) {
		$setting = array_merge( array(
			"id"          => $name . "_fee",
			"wpmldisable" => 1,
			"default"     => "",
			"type"        => "checkbox",
			"tags"        => array(
				"class" => "c tc-element-setting-fee",
				"id"    => "builder_" . $name . "_fee",
				"name"  => "tm_meta[tmfbuilder][" . $name . "_fee][]",
				"value" => "1",
			),
			"label"       => esc_html__( 'Set to Fee', 'woocommerce-tm-extra-product-options' ),
			"desc"        => esc_html__( 'Attach the price to the order making it independent of the product quantity.', 'woocommerce-tm-extra-product-options' ),
		), $args );

		$setting = apply_filters( 'wc_epo_add_setting_fee', $setting, $name, $args );

		return $setting;
	}

	/**
	 * Free chars setting
	 *
	 * @since 1.0
	 */
	public final function add_setting_freechars( $name = "", $args = array() ) {
		return array_merge( array(
			"id"          => $name . "_freechars",
			"wpmldisable" => 1,
			"default"     => "",
			"type"        => "number",
			"tags"        => array(
				"class" => "n",
				"id"    => "builder_" . $name . "_freechars",
				"name"  => "tm_meta[tmfbuilder][" . $name . "_freechars][]",
				"value" => "",
				"step"  => "1",
			),
			"label"       => esc_html__( 'Free chars', 'woocommerce-tm-extra-product-options' ),
			"desc"        => esc_html__( 'Enter the number of free chars.', 'woocommerce-tm-extra-product-options' ),
			"required"    => array(
				".tm-pricetype-selector" => array(
					"operator" => "is",
					"value"    => array(
						"wordnon",
						"wordpercentnon",
						"charnon",
						"charnonnospaces",
						"charpercentnon",
						"charpercentnonnospaces",
					),
				),
			),
		), $args );
	}

	/**
	 * Upload price type setting
	 *
	 * @since 1.0
	 */
	public final function add_setting_price_type5( $name = "" ) {

		$options = array(
			array( "text" => esc_html__( "Fixed amount", 'woocommerce-tm-extra-product-options' ), "value" => "" ),
			array(
				"text"  => esc_html__( "Percent of the original price", 'woocommerce-tm-extra-product-options' ),
				"value" => "percent",
			),
			array(
				"text"  => esc_html__( "Percent of the original price + options", 'woocommerce-tm-extra-product-options' ),
				"value" => "percentcurrenttotal",
			),
			array( "text" => esc_html__( 'Math formula', 'woocommerce-tm-extra-product-options' ), "value" => "math" ),
			array(
				"text"  => esc_html__( 'Fixed amount + options', 'woocommerce-tm-extra-product-options' ),
				"value" => "fixedcurrenttotal",
			),
		);

		$options = apply_filters( 'wc_epo_add_setting_price_type', $options, $name );

		return array(
			"id"          => $name . "_price_type",
			"wpmldisable" => 1,
			"default"     => "",
			"type"        => "select",
			"tags"        => array(
				"class" => "tm-pricetype-selector",
				"id"    => "builder_" . $name . "_price_type",
				"name"  => "tm_meta[tmfbuilder][" . $name . "_price_type][]",
			),
			"options"     => $options,
			"label"       => esc_html__( 'Price type', 'woocommerce-tm-extra-product-options' ),
		);
	}

	/**
	 * Date and time price type setting
	 *
	 * @since 1.0
	 */
	public final function add_setting_price_type6( $name = "" ) {

		$options = array(
			array( "text" => esc_html__( "Fixed amount", 'woocommerce-tm-extra-product-options' ), "value" => "" ),
			array(
				"text"  => esc_html__( "Percent of the original price", 'woocommerce-tm-extra-product-options' ),
				"value" => "percent",
			),
			array(
				"text"  => esc_html__( "Percent of the original price + options", 'woocommerce-tm-extra-product-options' ),
				"value" => "percentcurrenttotal",
			),
			array( "text" => esc_html__( 'Math formula', 'woocommerce-tm-extra-product-options' ), "value" => "math" ),
			array(
				"text"  => esc_html__( 'Fixed amount + options', 'woocommerce-tm-extra-product-options' ),
				"value" => "fixedcurrenttotal",
			),
		);

		$options = apply_filters( 'wc_epo_add_setting_price_type', $options, $name );

		return array(
			"id"          => $name . "_price_type",
			"wpmldisable" => 1,
			"default"     => "",
			"type"        => "select",
			"tags"        => array(
				"class" => "tm-pricetype-selector",
				"id"    => "builder_" . $name . "_price_type",
				"name"  => "tm_meta[tmfbuilder][" . $name . "_price_type][]",
			),
			"options"     => $options,
			"label"       => esc_html__( 'Price type', 'woocommerce-tm-extra-product-options' ),
		);
	}

	/**
	 * Range picker price type setting
	 *
	 * @since 1.0
	 */
	public final function add_setting_price_type7( $name = "" ) {

		$options = array(
			array( "text" => esc_html__( "Fixed amount", 'woocommerce-tm-extra-product-options' ), "value" => "" ),
			array( "text" => esc_html__( "Step * price", 'woocommerce-tm-extra-product-options' ), "value" => "step" ),
			array(
				"text"  => esc_html__( "Current value", 'woocommerce-tm-extra-product-options' ),
				"value" => "currentstep",
			),
			array(
				"text"  => esc_html__( "Price per Interval", 'woocommerce-tm-extra-product-options' ),
				"value" => "intervalstep",
			),
			array(
				"text"  => esc_html__( "Percent of the original price", 'woocommerce-tm-extra-product-options' ),
				"value" => "percent",
			),
			array(
				"text"  => esc_html__( "Percent of the original price + options", 'woocommerce-tm-extra-product-options' ),
				"value" => "percentcurrenttotal",
			),
			array( "text" => esc_html__( 'Math formula', 'woocommerce-tm-extra-product-options' ), "value" => "math" ),
			array(
				"text"  => esc_html__( 'Fixed amount + options', 'woocommerce-tm-extra-product-options' ),
				"value" => "fixedcurrenttotal",
			),
		);

		$options = apply_filters( 'wc_epo_add_setting_price_type', $options, $name );

		return array(
			"id"          => $name . "_price_type",
			"wpmldisable" => 1,
			"default"     => "",
			"type"        => "select",
			"tags"        => array(
				"class" => "tm-pricetype-selector",
				"id"    => "builder_" . $name . "_price_type",
				"name"  => "tm_meta[tmfbuilder][" . $name . "_price_type][]",
			),
			"options"     => $options,
			"label"       => esc_html__( 'Price type', 'woocommerce-tm-extra-product-options' ),
		);
	}

	/**
	 * Min value setting
	 *
	 * @since 1.0
	 */
	public final function add_setting_min( $name = "", $args = array(), $required = true ) {
		$min = array_merge( array(
			"id"          => $name . "_min",
			"wpmldisable" => 1,
			"default"     => "",
			"type"        => "number",
			"extra_tags"  => array(),
			"tags"        => array(
				"class" => "n",
				"id"    => "builder_" . $name . "_min",
				"name"  => "tm_meta[tmfbuilder][" . $name . "_min][]",
				"value" => "",
				"step"  => "any",
			),
			"label"       => esc_html__( 'Min value', 'woocommerce-tm-extra-product-options' ),
			"desc"        => esc_html__( 'Enter the minimum value.', 'woocommerce-tm-extra-product-options' ),
		), $args );

		$min["tags"] = array_merge( $min["tags"], $min["extra_tags"] );

		if ( $required ) {
			$min["required"] = array(
				".tm-pricetype-selector" => array(
					"operator" => "is",
					"value"    => array( "step", "currentstep" ),
				),
				".tc-validateas"         => array(
					"operator" => "is",
					"value"    => array( "number", "digits" ),
				),
			);
		}

		return $min;

	}

	/**
	 * Max value setting
	 *
	 * @since 1.0
	 */
	public final function add_setting_max( $name = "", $args = array(), $required = true ) {
		$max = array_merge( array(
			"id"          => $name . "_max",
			"wpmldisable" => 1,
			"default"     => "",
			"type"        => "number",
			"extra_tags"  => array(),
			"tags"        => array(
				"class" => "n",
				"id"    => "builder_" . $name . "_max",
				"name"  => "tm_meta[tmfbuilder][" . $name . "_max][]",
				"value" => "",
				"step"  => "any",
			),
			"label"       => esc_html__( 'Max value', 'woocommerce-tm-extra-product-options' ),
			"desc"        => esc_html__( 'Enter the maximum value.', 'woocommerce-tm-extra-product-options' ),
		), $args );

		$max["tags"] = array_merge( $max["tags"], $max["extra_tags"] );

		if ( $required ) {
			$max["required"] = array(
				".tm-pricetype-selector" => array(
					"operator" => "is",
					"value"    => array( "step", "currentstep" ),
				),
				".tc-validateas"         => array(
					"operator" => "is",
					"value"    => array( "number", "digits" ),
				),
			);
		}

		return $max;

	}

	/**
	 * Date format setting
	 *
	 * @since 1.0
	 */
	public final function add_setting_date_format( $name = "" ) {
		return array(
			"id"      => $name . "_format",
			"default" => "0",
			"type"    => "select",
			"tags"    => array(
				"id"   => "builder_" . $name . "_format",
				"name" => "tm_meta[tmfbuilder][" . $name . "_format][]",
			),
			"options" => array(
				array(
					"text"  => esc_html__( "Day / Month / Year", 'woocommerce-tm-extra-product-options' ),
					"value" => "0",
				),
				array(
					"text"  => esc_html__( "Month / Day / Year", 'woocommerce-tm-extra-product-options' ),
					"value" => "1",
				),
				array(
					"text"  => esc_html__( "Day . Month . Year", 'woocommerce-tm-extra-product-options' ),
					"value" => "2",
				),
				array(
					"text"  => esc_html__( "Month . Day . Year", 'woocommerce-tm-extra-product-options' ),
					"value" => "3",
				),
				array(
					"text"  => esc_html__( "Day - Month - Year", 'woocommerce-tm-extra-product-options' ),
					"value" => "4",
				),
				array(
					"text"  => esc_html__( "Month - Day - Year", 'woocommerce-tm-extra-product-options' ),
					"value" => "5",
				),

				array(
					"text"  => esc_html__( "Year / Month / Day", 'woocommerce-tm-extra-product-options' ),
					"value" => "6",
				),
				array(
					"text"  => esc_html__( "Year / Day / Month", 'woocommerce-tm-extra-product-options' ),
					"value" => "7",
				),
				array(
					"text"  => esc_html__( "Year . Month . Day", 'woocommerce-tm-extra-product-options' ),
					"value" => "8",
				),
				array(
					"text"  => esc_html__( "Year . Day . Month", 'woocommerce-tm-extra-product-options' ),
					"value" => "9",
				),
				array(
					"text"  => esc_html__( "Year - Month - Day", 'woocommerce-tm-extra-product-options' ),
					"value" => "10",
				),
				array(
					"text"  => esc_html__( "Year - Day - Month", 'woocommerce-tm-extra-product-options' ),
					"value" => "11",
				),

			),
			"label"   => esc_html__( "Date format", 'woocommerce-tm-extra-product-options' ),
		);
	}

	/**
	 * Time format setting
	 *
	 * @since 1.0
	 */
	public final function add_setting_time_format( $name = "", $args = array() ) {
		$time_format = array(
			"id"      => $name . "_time_format",
			"default" => "0",
			"type"    => "select",
			"tags"    => array(
				"id"   => "builder_" . $name . "_format",
				"name" => "tm_meta[tmfbuilder][" . $name . "_time_format][]",
			),
			"options" => array(
				array( "text" => esc_html__( "HH:mm", 'woocommerce-tm-extra-product-options' ), "value" => "HH:mm" ),
				array( "text" => esc_html__( "HH:m", 'woocommerce-tm-extra-product-options' ), "value" => "HH:m" ),
				array( "text" => esc_html__( "H:mm", 'woocommerce-tm-extra-product-options' ), "value" => "H:mm" ),
				array( "text" => esc_html__( "H:m", 'woocommerce-tm-extra-product-options' ), "value" => "H:m" ),
				array(
					"text"  => esc_html__( "HH:mm:ss", 'woocommerce-tm-extra-product-options' ),
					"value" => "HH:mm:ss",
				),
				array(
					"text"  => esc_html__( "HH:m:ss", 'woocommerce-tm-extra-product-options' ),
					"value" => "HH:m:ss",
				),
				array(
					"text"  => esc_html__( "H:mm:ss", 'woocommerce-tm-extra-product-options' ),
					"value" => "H:mm:ss",
				),
				array( "text" => esc_html__( "H:m:ss", 'woocommerce-tm-extra-product-options' ), "value" => "H:m:ss" ),
				array(
					"text"  => esc_html__( "HH:mm:s", 'woocommerce-tm-extra-product-options' ),
					"value" => "HH:mm:s",
				),
				array( "text" => esc_html__( "HH:m:s", 'woocommerce-tm-extra-product-options' ), "value" => "HH:m:s" ),
				array( "text" => esc_html__( "H:mm:s", 'woocommerce-tm-extra-product-options' ), "value" => "H:mm:s" ),
				array( "text" => esc_html__( "H:m:s", 'woocommerce-tm-extra-product-options' ), "value" => "H:m:s" ),

				array( "text" => esc_html__( "hh:mm", 'woocommerce-tm-extra-product-options' ), "value" => "hh:mm" ),
				array( "text" => esc_html__( "hh:m", 'woocommerce-tm-extra-product-options' ), "value" => "hh:m" ),
				array( "text" => esc_html__( "h:mm", 'woocommerce-tm-extra-product-options' ), "value" => "h:mm" ),
				array( "text" => esc_html__( "h:m", 'woocommerce-tm-extra-product-options' ), "value" => "h:m" ),
				array(
					"text"  => esc_html__( "hh:mm:ss", 'woocommerce-tm-extra-product-options' ),
					"value" => "hh:mm:ss",
				),
				array(
					"text"  => esc_html__( "hh:m:ss", 'woocommerce-tm-extra-product-options' ),
					"value" => "hh:m:ss",
				),
				array(
					"text"  => esc_html__( "h:mm:ss", 'woocommerce-tm-extra-product-options' ),
					"value" => "h:mm:ss",
				),
				array( "text" => esc_html__( "h:m:ss", 'woocommerce-tm-extra-product-options' ), "value" => "h:m:ss" ),
				array(
					"text"  => esc_html__( "hh:mm:s", 'woocommerce-tm-extra-product-options' ),
					"value" => "hh:mm:s",
				),
				array( "text" => esc_html__( "hh:m:s", 'woocommerce-tm-extra-product-options' ), "value" => "hh:m:s" ),
				array( "text" => esc_html__( "h:mm:s", 'woocommerce-tm-extra-product-options' ), "value" => "h:mm:s" ),
				array( "text" => esc_html__( "h:m:s", 'woocommerce-tm-extra-product-options' ), "value" => "h:m:s" ),
			),
			"label"   => esc_html__( "Time format", 'woocommerce-tm-extra-product-options' ),
		);

		$time_format = array_merge( $time_format, $args );

		return $time_format;
	}

	/**
	 * Custom Time format setting
	 *
	 * @since 1.0
	 */
	public final function add_setting_custom_time_format( $name = "", $args = array() ) {
		$custom_time_format = array(
			"id"          => $name . "_custom_time_format",
			"wpmldisable" => 1,
			"default"     => "",
			"type"        => "text",
			"tags"        => array(
				"class" => "t",
				"id"    => "builder_" . $name . "_custom_time_format",
				"name"  => "tm_meta[tmfbuilder][" . $name . "_custom_time_format][]",
				"value" => "",
			),
			"label"       => esc_html__( 'Custom Time format', 'woocommerce-tm-extra-product-options' ),
			"desc"        => esc_html__( 'This will override the time format above.', 'woocommerce-tm-extra-product-options' ),
		);
		$custom_time_format = array_merge( $custom_time_format, $args );

		return $custom_time_format;
	}

	/**
	 * Start year setting
	 *
	 * @since 1.0
	 */
	public final function add_setting_start_year( $name = "" ) {
		return array(
			"id"          => $name . "_start_year",
			"wpmldisable" => 1,
			"default"     => "1900",
			"type"        => "number",
			"tags"        => array(
				"class" => "n",
				"id"    => "builder_" . $name . "_start_year",
				"name"  => "tm_meta[tmfbuilder][" . $name . "_start_year][]",
				"value" => "",
			),
			"label"       => esc_html__( 'Start year', 'woocommerce-tm-extra-product-options' ),
			"desc"        => esc_html__( 'Enter starting year.', 'woocommerce-tm-extra-product-options' ),
		);
	}

	/**
	 * End year setting
	 *
	 * @since 1.0
	 */
	public final function add_setting_end_year( $name = "" ) {
		return array(
			"id"          => $name . "_end_year",
			"wpmldisable" => 1,
			"default"     => ( date( "Y" ) + 10 ),
			"type"        => "number",
			"tags"        => array(
				"class" => "n",
				"id"    => "builder_" . $name . "_end_year",
				"name"  => "tm_meta[tmfbuilder][" . $name . "_end_year][]",
				"value" => "",
			),
			"label"       => esc_html__( 'End year', 'woocommerce-tm-extra-product-options' ),
			"desc"        => esc_html__( 'Enter ending year.', 'woocommerce-tm-extra-product-options' ),
		);
	}

	/**
	 * Use URL replacements setting
	 *
	 * @since 1.0
	 */
	public final function add_setting_use_url( $name = "" ) {
		return array(
			"id"          => $name . "_use_url",
			"wpmldisable" => 1,
			"default"     => "",
			"type"        => "select",
			"tags"        => array(
				"class" => "use_url",
				"id"    => "builder_" . $name . "_use_url",
				"name"  => "tm_meta[tmfbuilder][" . $name . "_use_url][]",
			),
			"options"     => array(
				array( "text" => esc_html__( 'No', 'woocommerce-tm-extra-product-options' ), "value" => "" ),
				array( "text" => esc_html__( 'Yes', 'woocommerce-tm-extra-product-options' ), "value" => "url" ),
			),
			"label"       => esc_html__( 'Use URL replacements', 'woocommerce-tm-extra-product-options' ),
			"desc"        => esc_html__( 'Choose whether to redirect to a URL if the option is click.', 'woocommerce-tm-extra-product-options' ),
		);
	}

	/**
	 * Populate options setting
	 *
	 * @since 1.0
	 */
	public final function add_setting_options( $name = "" ) {
		return array(
			"id"         => $name . "_options",
			"tmid"       => "populate",
			"default"    => "",
			"type"       => "custom_multiple",
			"leftclass"  => "onerow",
			"rightclass" => "onerow",
			"html"       => array(
				array( $this, "builder_sub_options" ),
				array( array( "name" => 'multiple_' . $name . '_options' ) ),
			),
			"label"      => esc_html__( 'Populate options', 'woocommerce-tm-extra-product-options' ),
			"desc"       => ( $name == 'checkboxes' ) ? '' : esc_html__( 'Double click the radio button to remove its selected attribute.', 'woocommerce-tm-extra-product-options' ),
		);
	}

	/**
	 * Variation options setting
	 *
	 * @since 1.0
	 */
	public final function add_setting_variations_options( $name = "" ) {
		return array(
			"id"         => $name . "_options",
			"default"    => "",
			"type"       => "custom_multiple",
			"leftclass"  => "onerow",
			"rightclass" => "onerow2 tm-all-attributes",
			"html"       => array( array( $this, "builder_sub_variations_options" ), array( array() ) ),
			"label"      => esc_html__( 'Variation options', 'woocommerce-tm-extra-product-options' ),
			"desc"       => "",
		);
	}

	/**
	 * Use image replacements setting
	 *
	 * @since 1.0
	 */
	public final function add_setting_use_images( $name = "" ) {
		return array(
			"id"               => $name . "_use_images",
			"message0x0_class" => "tm-use-images",
			"wpmldisable"      => 1,
			"default"          => "",
			"type"             => "select",
			"tags"             => array(
				"class" => "use_images",
				"id"    => "builder_" . $name . "_use_images",
				"name"  => "tm_meta[tmfbuilder][" . $name . "_use_images][]",
			),
			"options"          => array(
				array( "text" => esc_html__( 'No', 'woocommerce-tm-extra-product-options' ), "value" => "" ),
				array( "text" => esc_html__( 'Yes', 'woocommerce-tm-extra-product-options' ), "value" => "images" ),
				array(
					"text"  => esc_html__( 'Start of the label', 'woocommerce-tm-extra-product-options' ),
					"value" => "start",
				),
				array(
					"text"  => esc_html__( 'End of the label', 'woocommerce-tm-extra-product-options' ),
					"value" => "end",
				),
			),
			"label"            => esc_html__( 'Use image replacements', 'woocommerce-tm-extra-product-options' ),
			"desc"             => esc_html__( 'Choose whether to use images in place of the element choices.', 'woocommerce-tm-extra-product-options' ),
		);
	}

	/**
	 * Use color replacements setting
	 *
	 * @since 1.0
	 */
	public final function add_setting_use_colors( $name = "" ) {
		return array(
			"id"               => $name . "_use_colors",
			"message0x0_class" => "tm-use-colors",
			"wpmldisable"      => 1,
			"default"          => "",
			"type"             => "select",
			"tags"             => array(
				"class" => "use_colors",
				"id"    => "builder_" . $name . "_use_colors",
				"name"  => "tm_meta[tmfbuilder][" . $name . "_use_colors][]",
			),
			"options"          => array(
				array( "text" => esc_html__( 'No', 'woocommerce-tm-extra-product-options' ), "value" => "" ),
				array( "text" => esc_html__( 'Yes', 'woocommerce-tm-extra-product-options' ), "value" => "color" ),
				array(
					"text"  => esc_html__( 'Start of the label', 'woocommerce-tm-extra-product-options' ),
					"value" => "start",
				),
				array(
					"text"  => esc_html__( 'End of the label', 'woocommerce-tm-extra-product-options' ),
					"value" => "end",
				),
			),
			"label"            => esc_html__( 'Use color replacements', 'woocommerce-tm-extra-product-options' ),
			"desc"             => esc_html__( 'Choose whether to use a color swatch in place of the element choices.', 'woocommerce-tm-extra-product-options' ),
		);
	}

	/**
	 * Use image lightbox setting
	 *
	 * @since 1.0
	 */
	public final function add_setting_use_lightbox( $name = "" ) {
		return array(
			"id"               => $name . "_use_lightbox",
			"message0x0_class" => "tm-show-when-use-images",
			"wpmldisable"      => 1,
			"default"          => "",
			"type"             => "checkbox",
			"tags"             => array(
				"value" => "lightbox",
				"class" => "use_lightbox tm-use-lightbox",
				"id"    => "builder_" . $name . "_use_lightbox",
				"name"  => "tm_meta[tmfbuilder][" . $name . "_use_lightbox][]",
			),
			"label"            => esc_html__( 'Use image lightbox', 'woocommerce-tm-extra-product-options' ),
			"desc"             => esc_html__( 'Choose whether to enable the lightbox on the thumbnail.', 'woocommerce-tm-extra-product-options' ),
		);
	}

	/**
	 * Changes product image setting
	 *
	 * @since 1.0
	 */
	public final function add_setting_changes_product_image( $name = "" ) {
		return array(
			"id"          => $name . "_changes_product_image",
			"wpmldisable" => 1,
			"default"     => "",
			"type"        => "select",
			"tags"        => array(
				"class" => "use_images tm-changes-product-image",
				"id"    => "builder_" . $name . "_changes_product_image",
				"name"  => "tm_meta[tmfbuilder][" . $name . "_changes_product_image][]",
			),
			"options"     => array(
				array( "text" => esc_html__( 'No', 'woocommerce-tm-extra-product-options' ), "value" => "" ),
				array(
					"text"  => esc_html__( 'Use the image replacements', 'woocommerce-tm-extra-product-options' ),
					"value" => "images",
				),
				array(
					"text"  => esc_html__( 'Use custom image', 'woocommerce-tm-extra-product-options' ),
					"value" => "custom",
				),
			),
			"label"       => esc_html__( 'Changes product image', 'woocommerce-tm-extra-product-options' ),
			"desc"        => esc_html__( 'Choose whether to change the product image.', 'woocommerce-tm-extra-product-options' ),
		);
	}

	/**
	 * Enable Swatch mode setting
	 *
	 * @since 1.0
	 */
	public final function add_setting_swatchmode( $name = "" ) {
		return array(
			"id"               => $name . "_swatchmode",
			"message0x0_class" => "tm-show-when-use-images tm-show-when-use-color",
			"wpmldisable"      => 1,
			"default"          => "",
			"type"             => "select",
			"tags"             => array(
				"class" => "swatchmode",
				"id"    => "builder_" . $name . "_swatchmode",
				"name"  => "tm_meta[tmfbuilder][" . $name . "_swatchmode][]",
			),
			"options"          => apply_filters( "wc_epo_add_setting_swatchmode", array(
					array( "text" => esc_html__( 'No', 'woocommerce-tm-extra-product-options' ), "value" => "" ),
					array(
						"text"  => esc_html__( 'Show label', 'woocommerce-tm-extra-product-options' ),
						"value" => "swatch",
					),
					array(
						"text"  => esc_html__( 'Show description', 'woocommerce-tm-extra-product-options' ),
						"value" => "swatch_desc",
					),
					array(
						"text"  => esc_html__( 'Show label and description', 'woocommerce-tm-extra-product-options' ),
						"value" => "swatch_lbl_desc",
					),
					array(
						"text"  => esc_html__( 'Show image', 'woocommerce-tm-extra-product-options' ),
						"value" => "swatch_img",
					),
					array(
						"text"  => esc_html__( 'Show image and label', 'woocommerce-tm-extra-product-options' ),
						"value" => "swatch_img_lbl",
					),
					array(
						"text"  => esc_html__( 'Show image and description', 'woocommerce-tm-extra-product-options' ),
						"value" => "swatch_img_desc",
					),
					array(
						"text"  => esc_html__( 'Show image, label and description', 'woocommerce-tm-extra-product-options' ),
						"value" => "swatch_img_lbl_desc",
					),
				)
			),
			"label"            => esc_html__( 'Enable Swatch mode', 'woocommerce-tm-extra-product-options' ),
			"desc"             => esc_html__( 'Swatch mode will show a tooltip over the choice.', 'woocommerce-tm-extra-product-options' ),
		);
	}

	/**
	 * Enable clear options button setting
	 *
	 * @since 1.0
	 */
	public final function add_setting_clear_options( $name = "" ) {
		return array(
			"id"          => $name . "_clear_options",
			"wpmldisable" => 1,
			"default"     => "",
			"type"        => "select",
			"tags"        => array(
				"class" => "clear_options",
				"id"    => "builder_" . $name . "_clear_options",
				"name"  => "tm_meta[tmfbuilder][" . $name . "_clear_options][]",
			),
			"options"     => array(
				array( "text" => esc_html__( 'No', 'woocommerce-tm-extra-product-options' ), "value" => "" ),
				array( "text" => esc_html__( 'Yes', 'woocommerce-tm-extra-product-options' ), "value" => "clear" ),
			),
			"label"       => esc_html__( 'Enable clear options button', 'woocommerce-tm-extra-product-options' ),
			"desc"        => esc_html__( 'This will add a button to clear the selected option.', 'woocommerce-tm-extra-product-options' ),
		);
	}

	/**
	 * Items per row setting helper
	 *
	 * @since 4.8.5
	 */
	public final function add_setting_items_per_row_helper() {

		echo "<span class='tc-enable-responsive'>" . esc_html__( 'Show responsive values', 'woocommerce-tm-extra-product-options' ) . " <span class='off tcfa tcfa-desktop'></span><span class='on tcfa tcfa-tablet-alt tm-hidden'></span></span>";

	}

	/**
	 * Items per row setting
	 *
	 * @since 1.0
	 */
	public final function add_setting_items_per_row( $name = "", $args = array(), $required = false ) {

		$per_row = array(
			'_multiple_values' => array(
				array(
					"id"          => $name . "_items_per_row",
					"wpmldisable" => 1,
					"default"     => "",
					"type"        => "number",
					"extra"       => array( array( $this, "add_setting_items_per_row_helper" ), array() ),
					"tags"        => array(
						"class" => "n",
						"id"    => "builder_" . $name . "_items_per_row",
						"name"  => "tm_meta[tmfbuilder][" . $name . "_items_per_row][]",
					),
					"label"       => esc_html__( 'Items per row (Desktops and laptops)', 'woocommerce-tm-extra-product-options' ),
					"desc"        => esc_html__( 'Use this field to make a grid display. Enter how many items per row for the grid or leave blank for normal display.', 'woocommerce-tm-extra-product-options' ),
				),
				//@media only screen and (min-device-width : 768px) and (max-device-width : 1024px) {
				array(
					"id"               => $name . "_items_per_row_tablets",
					"message0x0_class" => "builder_responsive_div",
					"wpmldisable"      => 1,
					"default"          => "",
					"type"             => "number",
					"tags"             => array(
						"class" => "n",
						"id"    => "builder_" . $name . "_items_per_row_tablets",
						"name"  => "tm_meta[tmfbuilder][" . $name . "_items_per_row_tablets][]",
					),
					"label"            => esc_html__( 'Items per row (Tablets landscape)', 'woocommerce-tm-extra-product-options' ),
					"desc"             => esc_html__( 'Use this field to make a grid display. Enter how many items per row for the grid or leave blank for normal display.', 'woocommerce-tm-extra-product-options' ),
				),
				//@media only screen and (min-device-width : 481px) and (max-device-width : 767px) {
				array(
					"id"               => $name . "_items_per_row_tablets_small",
					"message0x0_class" => "builder_responsive_div",
					"wpmldisable"      => 1,
					"default"          => "",
					"type"             => "number",
					"tags"             => array(
						"class" => "n",
						"id"    => "builder_" . $name . "_items_per_row_tablets_small",
						"name"  => "tm_meta[tmfbuilder][" . $name . "_items_per_row_tablets_small][]",
					),
					"label"            => esc_html__( 'Items per row (Tablets portrait)', 'woocommerce-tm-extra-product-options' ),
					"desc"             => esc_html__( 'Use this field to make a grid display. Enter how many items per row for the grid or leave blank for normal display.', 'woocommerce-tm-extra-product-options' ),
				),
				//@media only screen and (min-device-width : 320px) and (max-device-width : 480px) {
				array(
					"id"               => $name . "_items_per_row_smartphones",
					"message0x0_class" => "builder_responsive_div",
					"wpmldisable"      => 1,
					"default"          => "",
					"type"             => "number",
					"tags"             => array(
						"class" => "n",
						"id"    => "builder_" . $name . "_items_per_row_smartphones",
						"name"  => "tm_meta[tmfbuilder][" . $name . "_items_per_row_smartphones][]",
					),
					"label"            => esc_html__( 'Items per row (Smartphones)', 'woocommerce-tm-extra-product-options' ),
					"desc"             => esc_html__( 'Use this field to make a grid display. Enter how many items per row for the grid or leave blank for normal display.', 'woocommerce-tm-extra-product-options' ),
				),
				//@media only screen and (min-device-width: 320px) and (max-device-width: 568px) and (-webkit-min-device-pixel-ratio: 2) {
				array(
					"id"               => $name . "_items_per_row_iphone5",
					"message0x0_class" => "builder_responsive_div",
					"wpmldisable"      => 1,
					"default"          => "",
					"type"             => "number",
					"tags"             => array(
						"class" => "n",
						"id"    => "builder_" . $name . "_items_per_row_iphone5",
						"name"  => "tm_meta[tmfbuilder][" . $name . "_items_per_row_iphone5][]",
					),
					"label"            => esc_html__( 'Items per row (iPhone 5)', 'woocommerce-tm-extra-product-options' ),
					"desc"             => esc_html__( 'Use this field to make a grid display. Enter how many items per row for the grid or leave blank for normal display.', 'woocommerce-tm-extra-product-options' ),
				),
				//@media only screen and (min-device-width: 375px) and (max-device-width: 667px) and (-webkit-min-device-pixel-ratio: 2) {
				array(
					"id"               => $name . "_items_per_row_iphone6",
					"message0x0_class" => "builder_responsive_div",
					"wpmldisable"      => 1,
					"default"          => "",
					"type"             => "number",
					"tags"             => array(
						"class" => "n",
						"id"    => "builder_" . $name . "_items_per_row_iphone6",
						"name"  => "tm_meta[tmfbuilder][" . $name . "_items_per_row_iphone6][]",
					),
					"label"            => esc_html__( 'Items per row (iPhone 6)', 'woocommerce-tm-extra-product-options' ),
					"desc"             => esc_html__( 'Use this field to make a grid display. Enter how many items per row for the grid or leave blank for normal display.', 'woocommerce-tm-extra-product-options' ),
				),
				//@media only screen and (min-device-width: 414px) and (max-device-width: 736px) and (-webkit-min-device-pixel-ratio: 2) {
				array(
					"id"               => $name . "_items_per_row_iphone6_plus",
					"message0x0_class" => "builder_responsive_div",
					"wpmldisable"      => 1,
					"default"          => "",
					"type"             => "number",
					"tags"             => array(
						"class" => "n",
						"id"    => "builder_" . $name . "_items_per_row_iphone6_plus",
						"name"  => "tm_meta[tmfbuilder][" . $name . "_items_per_row_iphone6_plus][]",
					),
					"label"            => esc_html__( 'Items per row (iPhone 6 +)', 'woocommerce-tm-extra-product-options' ),
					"desc"             => esc_html__( 'Use this field to make a grid display. Enter how many items per row for the grid or leave blank for normal display.', 'woocommerce-tm-extra-product-options' ),
				),
				//@media only screen and (device-width: 320px) and (device-height: 640px) and (-webkit-min-device-pixel-ratio: 2) {
				array(
					"id"               => $name . "_items_per_row_samsung_galaxy",
					"message0x0_class" => "builder_responsive_div",
					"wpmldisable"      => 1,
					"default"          => "",
					"type"             => "number",
					"tags"             => array(
						"class" => "n",
						"id"    => "builder_" . $name . "_items_per_row_samsung_galaxy",
						"name"  => "tm_meta[tmfbuilder][" . $name . "_items_per_row_samsung_galaxy][]",
					),
					"label"            => esc_html__( 'Items per row (Samnsung Galaxy)', 'woocommerce-tm-extra-product-options' ),
					"desc"             => esc_html__( 'Use this field to make a grid display. Enter how many items per row for the grid or leave blank for normal display.', 'woocommerce-tm-extra-product-options' ),
				),
				//@media only screen and (min-device-width : 800px) and (max-device-width : 1280px) {
				array(
					"id"               => $name . "_items_per_row_tablets_galaxy",
					"message0x0_class" => "builder_responsive_div",
					"wpmldisable"      => 1,
					"default"          => "",
					"type"             => "number",
					"tags"             => array(
						"class" => "n",
						"id"    => "builder_" . $name . "_items_per_row_tablets_galaxy",
						"name"  => "tm_meta[tmfbuilder][" . $name . "_items_per_row_tablets_galaxy][]",
					),
					"label"            => esc_html__( 'Items per row (Galaxy Tablets landscape)', 'woocommerce-tm-extra-product-options' ),
					"desc"             => esc_html__( 'Use this field to make a grid display. Enter how many items per row for the grid or leave blank for normal display.', 'woocommerce-tm-extra-product-options' ),
				),

			),
		);

		$per_row = array_merge( $per_row, $args );

		if ( $required ) {
			$per_row["_multiple_values"][0]["required"] = $required;
			$per_row["_multiple_values"][1]["required"] = $required;
			$per_row["_multiple_values"][2]["required"] = $required;
			$per_row["_multiple_values"][3]["required"] = $required;
			$per_row["_multiple_values"][4]["required"] = $required;
			$per_row["_multiple_values"][5]["required"] = $required;
			$per_row["_multiple_values"][6]["required"] = $required;
			$per_row["_multiple_values"][7]["required"] = $required;
			$per_row["_multiple_values"][8]["required"] = $required;
		}

		return $per_row;
	}

	/**
	 * Limit selection setting
	 *
	 * @since 1.0
	 */
	public final function add_setting_limit_choices( $name = "" ) {
		return array(
			"id"          => $name . "_limit_choices",
			"wpmldisable" => 1,
			"default"     => "",
			"type"        => "number",
			"tags"        => array(
				"class" => "n",
				"id"    => "builder_" . $name . "_limit_choices",
				"name"  => "tm_meta[tmfbuilder][" . $name . "_limit_choices][]",
				"min"   => 0,
			),
			"label"       => esc_html__( 'Limit selection', 'woocommerce-tm-extra-product-options' ),
			"desc"        => esc_html__( 'Enter a number above 0 to limit the checkbox selection or leave blank for default behaviour.', 'woocommerce-tm-extra-product-options' ),
		);
	}

	/**
	 * Exact selection setting
	 *
	 * @since 1.0
	 */
	public final function add_setting_exactlimit_choices( $name = "" ) {
		return array(
			"id"          => $name . "_exactlimit_choices",
			"wpmldisable" => 1,
			"default"     => "",
			"type"        => "number",
			"tags"        => array(
				"class" => "n",
				"id"    => "builder_" . $name . "_exactlimit_choices",
				"name"  => "tm_meta[tmfbuilder][" . $name . "_exactlimit_choices][]",
				"min"   => 0,
			),
			"label"       => esc_html__( 'Exact selection', 'woocommerce-tm-extra-product-options' ),
			"desc"        => esc_html__( 'Enter a number above 0 to have the user select the exact number of checkboxes or leave blank for default behaviour.', 'woocommerce-tm-extra-product-options' ),
		);
	}

	/**
	 * Minimum selection setting
	 *
	 * @since 1.0
	 */
	public final function add_setting_minimumlimit_choices( $name = "" ) {
		return array(
			"id"          => $name . "_minimumlimit_choices",
			"wpmldisable" => 1,
			"default"     => "",
			"type"        => "number",
			"tags"        => array(
				"class" => "n",
				"id"    => "builder_" . $name . "_minimumlimit_choices",
				"name"  => "tm_meta[tmfbuilder][" . $name . "_minimumlimit_choices][]",
				"min"   => 0,
			),
			"label"       => esc_html__( 'Minimum selection', 'woocommerce-tm-extra-product-options' ),
			"desc"        => esc_html__( 'Enter a number above 0 to have the user select at least that number of checkboxes or leave blank for default behaviour.', 'woocommerce-tm-extra-product-options' ),
		);
	}

	/**
	 * Upload button style setting
	 *
	 * @since 1.0
	 */
	public final function add_setting_button_type( $name = "", $args = array() ) {
		$button_type = array(
			"id"          => $name . "_button_type",
			"wpmldisable" => 1,
			"default"     => "",
			"type"        => "select",
			"tags"        => array(
				"id"   => "builder_" . $name . "_button_type",
				"name" => "tm_meta[tmfbuilder][" . $name . "_button_type][]",
			),
			"options"     => array(
				array(
					"text"  => esc_html__( 'Normal browser button', 'woocommerce-tm-extra-product-options' ),
					"value" => "",
				),
				array(
					"text"  => esc_html__( 'Styled button', 'woocommerce-tm-extra-product-options' ),
					"value" => "button",
				),
			),
			"label"       => esc_html__( 'Upload button style', 'woocommerce-tm-extra-product-options' ),
		);

		$button_type = array_merge( $button_type, $args );

		return $button_type;
	}

	/**
	 * Date picker style setting
	 *
	 * @since 1.0
	 */
	public final function add_setting_button_type2( $name = "" ) {
		return array(
			"id"          => $name . "_button_type",
			"wpmldisable" => 1,
			"default"     => "picker",
			"type"        => "select",
			"tags"        => array(
				"id"   => "builder_" . $name . "_button_type",
				"name" => "tm_meta[tmfbuilder][" . $name . "_button_type][]",
			),
			"options"     => array(
				array( "text" => esc_html__( "Date field", 'woocommerce-tm-extra-product-options' ), "value" => "" ),
				array(
					"text"  => esc_html__( "Date picker", 'woocommerce-tm-extra-product-options' ),
					"value" => "picker",
				),
				array(
					"text"  => esc_html__( "Date field and picker", 'woocommerce-tm-extra-product-options' ),
					"value" => "fieldpicker",
				),
			),
			"label"       => esc_html__( "Date picker style", 'woocommerce-tm-extra-product-options' ),
		);
	}

	/**
	 * Hide price setting
	 *
	 * @since 1.0
	 */
	public final function add_setting_hide_amount( $name = "" ) {
		return array(
			"id"               => $name . "_hide_amount",
			"message0x0_class" => "builder_" . $name . "_hide_amount_div",
			"wpmldisable"      => 1,
			"default"          => "",
			"type"             => "checkbox",
			"tags"             => array(
				"value" => "hidden",
				"id"    => "builder_" . $name . "_hide_amount",
				"name"  => "tm_meta[tmfbuilder][" . $name . "_hide_amount][]",
			),
			"label"            => esc_html__( 'Hide price', 'woocommerce-tm-extra-product-options' ),
			"desc"             => esc_html__( 'Choose whether to hide the price or not.', 'woocommerce-tm-extra-product-options' ),
		);
	}

	/**
	 * Quantity selector setting
	 *
	 * @since 1.0
	 */
	public final function add_setting_quantity( $name = "" ) {
		return array(
			'_multiple_values' => array(
				array(
					"id"               => $name . "_quantity",
					"message0x0_class" => "builder_" . $name . "_quantity_div",
					"wpmldisable"      => 1,
					"default"          => "",
					"type"             => "select",
					"tags"             => array(
						"id"    => "builder_" . $name . "_quantity",
						"class" => "tm-qty-selector",
						"name"  => "tm_meta[tmfbuilder][" . $name . "_quantity][]",
					),
					"options"          => array(
						array(
							"text"  => esc_html__( 'Disable', 'woocommerce-tm-extra-product-options' ),
							"value" => "",
						),
						array(
							"text"  => esc_html__( 'Right', 'woocommerce-tm-extra-product-options' ),
							"value" => "right",
						),
						array(
							"text"  => esc_html__( 'Left', 'woocommerce-tm-extra-product-options' ),
							"value" => "left",
						),
						array(
							"text"  => esc_html__( 'Top', 'woocommerce-tm-extra-product-options' ),
							"value" => "top",
						),
						array(
							"text"  => esc_html__( 'Bottom', 'woocommerce-tm-extra-product-options' ),
							"value" => "bottom",
						),
					),
					"label"            => esc_html__( 'Quantity selector', 'woocommerce-tm-extra-product-options' ),
					"desc"             => esc_html__( 'This will show a quantity selector for this option.', 'woocommerce-tm-extra-product-options' ),
				),
				$this->add_setting_min( $name . "_quantity",
					array(
						"label"            => esc_html__( 'Quantity min value', 'woocommerce-tm-extra-product-options' ),
						"message0x0_class" => "tm-qty-min",
						"required"         => array(
							".tm-qty-selector" => array(
								"operator" => "isnot",
								"value"    => "",
							),
						),
					), false
				),
				$this->add_setting_max( $name . "_quantity",
					array(
						"label"            => esc_html__( 'Quantity max value', 'woocommerce-tm-extra-product-options' ),
						"message0x0_class" => "tm-qty-max",
						"required"         => array(
							".tm-qty-selector" => array(
								"operator" => "isnot",
								"value"    => "",
							),
						),
					), false
				),
				array(
					"id"               => $name . "_quantity_step",
					"message0x0_class" => "tm-qty-step",
					"wpmldisable"      => 1,
					"default"          => "",
					"type"             => "number",
					"tags"             => array(
						"class" => "n",
						"id"    => "builder_" . $name . "_min",
						"name"  => "tm_meta[tmfbuilder][" . $name . "_quantity_step][]",
						"value" => "",
						"step"  => "any",
						"min"   => 0,
					),
					"label"            => esc_html__( 'Quantity step', 'woocommerce-tm-extra-product-options' ),
					"desc"             => esc_html__( 'Enter the quantity step.', 'woocommerce-tm-extra-product-options' ),
					"required"         => array(
						".tm-qty-selector" => array(
							"operator" => "isnot",
							"value"    => "",
						),
					),
				),


				$this->add_setting_default_value( $name . "_quantity",
					array(
						"type"             => "number",
						"tags"             => array(
							"class" => "n",
							"id"    => "builder_" . $name . "_quantity" . "_default_value",
							"name"  => "tm_meta[tmfbuilder][" . $name . "_quantity" . "_default_value][]",
							"value" => "",
						),
						"label"            => esc_html__( 'Quantity Default value', 'woocommerce-tm-extra-product-options' ),
						"message0x0_class" => "tm-qty-default",
						"desc"             => esc_html__( 'Enter a value to be applied to the Quantity field automatically.', 'woocommerce-tm-extra-product-options' ),
						"required"         => array(
							".tm-qty-selector" => array(
								"operator" => "isnot",
								"value"    => "",
							),
						),
					)
				),
			),
		);
	}

	/**
	 * Placeholder setting
	 *
	 * @since 1.0
	 */
	public final function add_setting_placeholder( $name = "", $args = array() ) {
		return array_merge( array(
			"id"      => $name . "_placeholder",
			"default" => "",
			"type"    => "text",
			"tags"    => array(
				"class" => "t",
				"id"    => "builder_" . $name . "_placeholder",
				"name"  => "tm_meta[tmfbuilder][" . $name . "_placeholder][]",
				"value" => "",
			),
			"label"   => esc_html__( 'Placeholder', 'woocommerce-tm-extra-product-options' ),
			"desc"    => "",
		), $args );
	}

	/**
	 * Minimum characters setting
	 *
	 * @since 1.0
	 */
	public final function add_setting_min_chars( $name = "" ) {
		return array(
			"id"          => $name . "_min_chars",
			"wpmldisable" => 1,
			"default"     => "",
			"type"        => "number",
			"tags"        => array(
				"class" => "n",
				"id"    => "builder_" . $name . "_min_chars",
				"name"  => "tm_meta[tmfbuilder][" . $name . "_min_chars][]",
				"value" => "",
				"min"   => 0,
			),
			"label"       => esc_html__( 'Minimum characters', 'woocommerce-tm-extra-product-options' ),
			"desc"        => esc_html__( 'Enter a value for the minimum characters the user must enter.', 'woocommerce-tm-extra-product-options' ),
		);
	}

	/**
	 * Maximum characters setting
	 *
	 * @since 1.0
	 */
	public final function add_setting_max_chars( $name = "" ) {
		return array(
			"id"          => $name . "_max_chars",
			"wpmldisable" => 1,
			"default"     => "",
			"type"        => "number",
			"tags"        => array(
				"class" => "n",
				"id"    => "builder_" . $name . "_max_chars",
				"name"  => "tm_meta[tmfbuilder][" . $name . "_max_chars][]",
				"value" => "",
				"min"   => 0,
			),
			"label"       => esc_html__( 'Maximum characters', 'woocommerce-tm-extra-product-options' ),
			"desc"        => esc_html__( 'Enter a value to limit the maximum characters the user can enter.', 'woocommerce-tm-extra-product-options' ),
		);
	}

	/**
	 * Default value setting
	 *
	 * @since 1.0
	 */
	public final function add_setting_default_value( $name = "", $args = array() ) {
		return array_merge( array(
			"id"      => $name . "_default_value",
			"default" => "",
			"type"    => "text",
			"tags"    => array(
				"class" => "t",
				"id"    => "builder_" . $name . "_default_value",
				"name"  => "tm_meta[tmfbuilder][" . $name . "_default_value][]",
				"value" => "",
			),
			"label"   => esc_html__( 'Default value', 'woocommerce-tm-extra-product-options' ),
			"desc"    => esc_html__( 'Enter a value to be applied to the field automatically.', 'woocommerce-tm-extra-product-options' ),
		), $args );
	}

	/**
	 * Default value setting (for textarea)
	 *
	 * @since 1.0
	 */
	public final function add_setting_default_value_multiple( $name = "" ) {
		return array(
			"id"      => $name . "_default_value",
			"default" => "",
			"type"    => "textarea",
			"tags"    => array(
				"class" => "t tm-no-editor",
				"id"    => "builder_" . $name . "_default_value",
				"name"  => "tm_meta[tmfbuilder][" . $name . "_default_value][]",
				"value" => "",
			),
			"label"   => esc_html__( 'Default value', 'woocommerce-tm-extra-product-options' ),
			"desc"    => esc_html__( 'Enter a value to be applied to the field automatically.', 'woocommerce-tm-extra-product-options' ),
		);
	}

	/**
	 * Get weekdays
	 *
	 * @since  1.0
	 * @access public
	 */
	public function get_weekdays() {

		echo '<div class="tm-weekdays-picker-wrap">';
		// load wp translations
		if ( function_exists( 'wp_load_translations_early' ) ) {
			wp_load_translations_early();
			global $wp_locale;
			for ( $day_index = 0; $day_index <= 6; $day_index ++ ) {
				echo '<span class="tm-weekdays-picker"><label><input class="tm-weekday-picker" type="checkbox" value="' . esc_attr( $day_index ) . '"><span>' . esc_html( $wp_locale->get_weekday( $day_index ) ) . '</span></label></span>';
			}
			// in case something goes wrong
		} else {
			$weekday[0] = /* translators: weekday Sunday */
				esc_html__( 'Sunday', 'default' );
			$weekday[1] = /* translators: weekday Monday */
				esc_html__( 'Monday', 'default' );
			$weekday[2] = /* translators: weekday Tuesday */
				esc_html__( 'Tuesday', 'default' );
			$weekday[3] = /* translators: weekday Wednesday */
				esc_html__( 'Wednesday', 'default' );
			$weekday[4] = /* translators: weekday Thursday */
				esc_html__( 'Thursday', 'default' );
			$weekday[5] = /* translators: weekday Friday */
				esc_html__( 'Friday', 'default' );
			$weekday[6] = /* translators: weekday Saturday */
				esc_html__( 'Saturday', 'default' );
			for ( $day_index = 0; $day_index <= 6; $day_index ++ ) {
				echo '<span class="tm-weekdays-picker"><label><input class="tm-weekday-picker" type="checkbox" value="' . esc_attr( $day_index ) . '"><span>' . esc_html( $weekday[ $day_index ] ) . '</span></label></span>';
			}
		}
		echo '</div>';

	}

	/**
	 * Get weekdays
	 *
	 * @since  4.9.11
	 * @access public
	 */
	public function get_months() {

		echo '<div class="tm-months-picker-wrap">';
		// load wp translations
		if ( function_exists( 'wp_load_translations_early' ) ) {
			wp_load_translations_early();
			global $wp_locale;
			for ( $month_index = 1; $month_index <= 12; $month_index ++ ) {
				echo '<span class="tm-months-picker"><label><input class="tm-month-picker" type="checkbox" value="' . esc_attr( $month_index ) . '"><span>' . esc_html( $wp_locale->get_month( $month_index ) ) . '</span></label></span>';
			}
			// in case something goes wrong
		} else {
			$month[0]  = /* translators: month January */
				esc_html__( 'January ', 'default' );
			$month[1]  = /* translators: month February */
				esc_html__( 'February', 'default' );
			$month[2]  = /* translators: month March */
				esc_html__( 'March', 'default' );
			$month[3]  = /* translators: month April */
				esc_html__( 'April', 'default' );
			$month[4]  = /* translators: month May */
				esc_html__( 'May', 'default' );
			$month[5]  = /* translators: month June */
				esc_html__( 'June', 'default' );
			$month[6]  = /* translators: month July */
				esc_html__( 'July', 'default' );
			$month[7]  = /* translators: month August */
				esc_html__( 'August', 'default' );
			$month[8]  = /* translators: month September */
				esc_html__( 'September', 'default' );
			$month[9]  = /* translators: month October */
				esc_html__( 'October', 'default' );
			$month[10] = /* translators: month November */
				esc_html__( 'November', 'default' );
			$month[11] = /* translators: month December */
				esc_html__( 'December', 'default' );
			for ( $month_index = 1; $month_index <= 12; $month_index ++ ) {
				echo '<span class="tm-months-picker"><label><input class="tm-month-picker" type="checkbox" value="' . esc_attr( $month_index ) . '"><span>' . esc_html( $month[ $month_index ] ) . '</span></label></span>';
			}
		}
		echo '</div>';

	}

	/**
	 * Remove prefix
	 *
	 * @since  1.0
	 * @access private
	 */
	private function remove_prefix( $str = "", $prefix = "" ) {
		if ( substr( $str, 0, strlen( $prefix ) ) == $prefix ) {
			$str = substr( $str, strlen( $prefix ) );
		}

		return $str;
	}

	/**
	 * Add element helper
	 *
	 * @since  1.0
	 * @access private
	 */
	private function _add_element_helper( $name = "", $value = "", $_value = array(), $additional_currencies = false, $is_addon = false ) {

		$return = array();

		if ( $value == "price" ) {

			if ( ! empty( $additional_currencies ) && is_array( $additional_currencies ) ) {
				$_copy_value     = $_value;
				$_value["label"] .= ' <span class="tm-choice-currency">' . THEMECOMPLETE_EPO_HELPER()->wc_base_currency() . '</span>';
				$return[]        = $_value;
				foreach ( $additional_currencies as $ckey => $currency ) {
					$copy_value                 = $_copy_value;
					$copy_value["id"]           .= "_" . $currency;
					$copy_value["label"]        .= ' <span class="tm-choice-currency">' . $currency . '</span>';
					$copy_value["desc"]         = sprintf( esc_html__( 'Leave it blank to calculate it automatically from the %s price', 'woocommerce-tm-extra-product-options' ), THEMECOMPLETE_EPO_HELPER()->wc_base_currency() );
					$copy_value["tags"]["id"]   = "builder_" . $name . "_price" . "_" . $currency;
					$copy_value["tags"]["name"] = "tm_meta[tmfbuilder][" . $name . "_price_" . $currency . "][]";
					$return[]                   = $copy_value;
				}
			} else {
				$return[] = $_value;
			}
		} elseif ( $value == "sale_price" ) {

			if ( ! empty( $additional_currencies ) && is_array( $additional_currencies ) ) {
				$_copy_value     = $_value;
				$_value["label"] .= ' <span class="tm-choice-currency">' . THEMECOMPLETE_EPO_HELPER()->wc_base_currency() . '</span>';
				$return[]        = $_value;
				foreach ( $additional_currencies as $ckey => $currency ) {
					$copy_value                 = $_copy_value;
					$copy_value["id"]           .= "_" . $currency;
					$copy_value["label"]        .= ' <span class="tm-choice-currency">' . $currency . '</span>';
					$copy_value["desc"]         = sprintf( esc_html__( 'Leave it blank to calculate it automatically from the %s sale price', 'woocommerce-tm-extra-product-options' ), THEMECOMPLETE_EPO_HELPER()->wc_base_currency() );
					$copy_value["tags"]["id"]   = "builder_" . $name . "_sale_price" . "_" . $currency;
					$copy_value["tags"]["name"] = "tm_meta[tmfbuilder][" . $name . "_sale_price_" . $currency . "][]";
					$return[]                   = $copy_value;
				}
			} else {
				$return[] = $_value;
			}
		} else {
			$return[] = $_value;
		}

		if ( isset( $_value["id"] ) ) {
			if ( $is_addon ) {
				$this->addons_attributes[] = $this->remove_prefix( $_value["id"], $name . "_" );
			}
			$this->default_attributes[] = $this->remove_prefix( $_value["id"], $name . "_" );
		}

		return $return;
	}

	/**
	 * Add element
	 *
	 * @since 1.0
	 */
	public final function add_element( $name = "", $settings_array = array(), $is_addon = false, $tabs_override = array(), $header_names = array() ) {

		$settings_array        = apply_filters( 'tc_element_settings_override', $settings_array, $name );
		$tabs_override         = apply_filters( 'tc_element_tabs_override', $tabs_override, $name, $settings_array, $is_addon );
		$options               = array();
		$additional_currencies = THEMECOMPLETE_EPO_HELPER()->get_additional_currencies();

		if ( ! isset( $settings_array["_tabs"] ) ) {
			$tabs_array = array( "general_options" => $settings_array );
		} else {
			$tabs_array = $settings_array["_tabs"];
		}
		$tabs = array();
		foreach ( $tabs_array as $tab => $settings ) {
			$tabs[] = $tab;
			foreach ( $settings as $key => $value ) {
				if ( is_array( $value ) && count( $value ) > 2 ) {
					if ( isset( $value["id"] ) ) {
						$this->default_attributes[] = $value["id"];
						if ( $is_addon ) {
							$value["id"] = $this->remove_prefix( $value["id"], $name . "_" );

							$this->addons_attributes[] = $value["id"];

							$value["id"] = $name . "_" . $value["id"];

							if ( ! isset( $value["tags"] ) ) {
								$value["tags"] = array();
							}
							$value["tags"] = array_merge( $value["tags"], array(
									"id"    => "builder_" . $value["id"],
									"name"  => "tm_meta[tmfbuilder][" . $value["id"] . "][]",
									"value" => "",
								)
							);
						}

					}
					$options[ $tab ][] = $value;
				} else {
					$args = false;

					if ( is_array( $value ) && count( $value ) == 1 && isset( $value['_multiple_values'] ) ) {

						foreach ( $value['_multiple_values'] as $mkey => $mvalue ) {
							$r = $this->_add_element_helper( $name, $value, $mvalue, $additional_currencies, $is_addon );
							foreach ( $r as $rkey => $rvalue ) {
								$options[ $tab ][] = $rvalue;
							}
						}

					} else {

						if ( is_array( $value ) && count( $value ) == 2 ) {
							$args  = $value[1];
							$value = $value[0];
						}

						$method = apply_filters( 'wc_epo_add_element_method', "add_setting_" . $value, $key, $value, $name, $settings, $is_addon, $tabs_override );

						$class_to_use = apply_filters( 'wc_epo_add_element_class', $this, $key, $value, $name, $settings, $is_addon, $tabs_override );

						if ( is_callable( array( $class_to_use, $method ) ) ) {
							if ( $args ) {
								$_value = $class_to_use->$method( $name, $args );
							} else {
								$_value = $class_to_use->$method( $name );
							}

							if ( isset( $_value['_multiple_values'] ) ) {
								foreach ( $_value['_multiple_values'] as $mkey => $mvalue ) {
									$r = $this->_add_element_helper( $name, $value, $mvalue, $additional_currencies, $is_addon );
									foreach ( $r as $rkey => $rvalue ) {
										$options[ $tab ][] = $rvalue;
									}
								}
							} else {
								$r = $this->_add_element_helper( $name, $value, $_value, $additional_currencies, $is_addon );
								foreach ( $r as $rkey => $rvalue ) {
									$options[ $tab ][] = $rvalue;
								}
							}

						}

					}

				}
			}
		}

		if ( ! empty( $tabs_override ) ) {
			if ( ! isset( $tabs_override["label_options"] ) ) {
				$tabs_override["label_options"] = 0;
			}
			if ( ! isset( $tabs_override["conditional_logic"] ) ) {
				$tabs_override["conditional_logic"] = 0;
			}
			if ( ! isset( $tabs_override["css_settings"] ) ) {
				$tabs_override["css_settings"] = 0;
			}
			if ( ! isset( $tabs_override["woocommerce_settings"] ) ) {
				$tabs_override["woocommerce_settings"] = 0;
			}
			foreach ( $tabs as $tab ) {
				if ( ! isset( $tabs_override[ $tab ] ) ) {
					$tabs_override[ $tab ] = 0;
				}
			}
		} else {
			$tabs_override["label_options"]        = 1;
			$tabs_override["conditional_logic"]    = 1;
			$tabs_override["css_settings"]         = 1;
			$tabs_override["woocommerce_settings"] = 1;
			foreach ( $tabs as $tab ) {
				$tabs_override[ $tab ] = 1;
			}
		}

		$counter       = 1;
		$options_array = array();
		$header_array  = array();
		foreach ( $options as $tab => $option ) {

			if ( ! empty( $tabs_override[ $tab ] ) ) {
				$counter ++;
				$options_array = array_merge(
					$options_array,
					// add $tab options
					$this->_prepend_div( $name . $counter ),
					apply_filters( 'wc_epo_admin_element_general_options', $option ),
					$this->_append_div( $name . $counter )
				);
				if ( $tab === "general_options" ) {
					$header_name = esc_html__( "General options", 'woocommerce-tm-extra-product-options' );
					$header_icon = "tcfa tcfa-cog";
					$header_slug = "tma-tab-general";
				} elseif ( isset( $header_names[ $tab ] ) ) {
					$header_name = isset( $header_names[ $tab ]['name'] ) ? $header_names[ $tab ]['name'] : esc_html__( "General Settings", 'woocommerce-tm-extra-product-options' );
					$header_icon = isset( $header_names[ $tab ]['icon'] ) ? $header_names[ $tab ]['icon'] : "tcfa tcfa-cog";
					$header_slug = isset( $header_names[ $tab ]['slug'] ) ? $header_names[ $tab ]['slug'] . " tma-tab-extra" : "tma-tab-general tma-tab-extra";
				}
				$header_array = array_merge(
					$header_array,
					// add $tab options
					$this->_prepend_tab( $name . $counter,
						array( $header_icon, $header_name ), "closed", $header_slug
					)
				);
			}

		}

		return array_merge(
			$this->_prepend_div( "", "tm-tabs" ),

			// add headers
			$this->_prepend_div( $name, "tm-tab-headers" ),
			! empty( $tabs_override["label_options"] ) ? $this->_prepend_tab( $name . "1", array(
				"tcfa tcfa-tag",
				esc_html__( "Label options", 'woocommerce-tm-extra-product-options' ),
			), "open", "tma-tab-label" ) : array(),
			$header_array,
			! empty( $tabs_override["conditional_logic"] ) ? $this->_prepend_tab( $name . ( $counter + 1 ), array(
				"tcfa tcfa-lightbulb",
				esc_html__( "Conditional Logic", 'woocommerce-tm-extra-product-options' ),
			), "closed", "tma-tab-logic" ) : array(),
			! empty( $tabs_override["css_settings"] ) ? $this->_prepend_tab( $name . ( $counter + 2 ), array(
				"tcfa tcfa-file-alt",
				esc_html__( "CSS settings", 'woocommerce-tm-extra-product-options' ),
			), "closed", "tma-tab-css" ) : array(),
			! empty( $tabs_override["woocommerce_settings"] ) ? $this->_prepend_tab( $name . ( $counter + 3 ), array(
				"tcfa tcfa-shopping-bag",
				esc_html__( "WooCommerce settings", 'woocommerce-tm-extra-product-options' ),
			), "closed", "tma-tab-woocommerce" ) : array(),
			$this->_append_div( $name ),

			// add Label options
			! empty( $tabs_override["label_options"] ) ? $this->_prepend_div( $name . "1" ) : array(),
			! empty( $tabs_override["label_options"] ) ? $this->_get_header_array( $name . "_header", "element", $name ) : array(),
			! empty( $tabs_override["label_options"] ) ? $this->_get_divider_array( $name . "_divider", 0 ) : array(),
			! empty( $tabs_override["label_options"] ) ? $this->_append_div( $name . "1" ) : array(),

			// add options
			$options_array,

			// add Contitional logic
			$name !== "variations" && ! empty( $tabs_override["conditional_logic"] ) ? $this->_prepend_div( $name . ( $counter + 1 ) ) : array(),
			$name !== "variations" && ! empty( $tabs_override["conditional_logic"] ) ? $this->_prepend_logic( $name ) : array(),
			$name !== "variations" && ! empty( $tabs_override["conditional_logic"] ) ? $this->_append_div( $name . ( $counter + 1 ) ) : array(),

			// add CSS settings
			$name !== "variations" && ! empty( $tabs_override["css_settings"] ) ? $this->_prepend_div( $name . ( $counter + 2 ) ) : array(),
			$name !== "variations" && ! empty( $tabs_override["css_settings"] ) ? apply_filters( 'wc_epo_admin_element_css_settings', array(
				array(
					"id"      => $name . "_class",
					"default" => "",
					"type"    => "text",
					"tags"    => array(
						"class" => "t",
						"id"    => "builder_" . $name . "_class",
						"name"  => "tm_meta[tmfbuilder][" . $name . "_class][]",
						"value" => "",
					),
					"label"   => esc_html__( 'Element class name', 'woocommerce-tm-extra-product-options' ),
					"desc"    => esc_html__( 'Enter an extra class name to add to this element', 'woocommerce-tm-extra-product-options' ),
				),
				array(
					"id"      => $name . "_container_id",
					"default" => "",
					"type"    => "text",
					"tags"    => array(
						"class" => "t",
						"id"    => "builder_" . $name . "_container_id",
						"name"  => "tm_meta[tmfbuilder][" . $name . "_container_id][]",
						"value" => "",
					),
					"label"   => esc_html__( 'Element container id', 'woocommerce-tm-extra-product-options' ),
					"desc"    => esc_html__( 'Enter an id for the container of the element.', 'woocommerce-tm-extra-product-options' ),
				),
			) ) : array(),
			! empty( $tabs_override["css_settings"] ) ? $this->_append_div( $name . ( $counter + 2 ) ) : array(),

			// add WooCommerce settings
			$name !== "variations" && ! empty( $tabs_override["woocommerce_settings"] ) ? $this->_prepend_div( $name . ( $counter + 3 ) ) : array(),
			$name !== "variations" && ! empty( $tabs_override["woocommerce_settings"] ) ? apply_filters( 'wc_epo_admin_element_woocommerce_settings', array(
				array(
					"id"          => $name . "_include_tax_for_fee_price_type",
					"wpmldisable" => 1,
					"default"     => "",
					"type"        => "select",
					"tags"        => array(
						"id"   => "builder_" . $name . "_include_tax_for_fee_price_type",
						"name" => "tm_meta[tmfbuilder][" . $name . "_include_tax_for_fee_price_type][]",
					),
					"options"     => array(
						array(
							"text"  => esc_html__( 'Inherit product setting', 'woocommerce-tm-extra-product-options' ),
							"value" => "",
						),
						array(
							"text"  => esc_html__( 'Yes', 'woocommerce-tm-extra-product-options' ),
							"value" => "yes",
						),
						array( "text" => esc_html__( 'No', 'woocommerce-tm-extra-product-options' ), "value" => "no" ),
					),
					"label"       => esc_html__( 'Include tax for Fee price type', 'woocommerce-tm-extra-product-options' ),
					"desc"        => esc_html__( 'Choose whether to include tax for Fee price type on this element.', 'woocommerce-tm-extra-product-options' ),
					"required"    => array(
						".tc-element-setting-fee" => array(
							"operator" => "is",
							"value"    => "1",
						),
					),
				),
				array(
					"id"          => $name . "_tax_class_for_fee_price_type",
					"wpmldisable" => 1,
					"default"     => "",
					"type"        => "select",
					"tags"        => array(
						"id"   => "builder_" . $name . "_tax_class_for_fee_price_type",
						"name" => "tm_meta[tmfbuilder][" . $name . "_tax_class_for_fee_price_type][]",
					),
					"options"     => $this->get_tax_classes(),
					"label"       => esc_html__( 'Tax class for Fee price type', 'woocommerce-tm-extra-product-options' ),
					"desc"        => esc_html__( 'Choose the tax class for Fee price type on this element.', 'woocommerce-tm-extra-product-options' ),
					"required"    => array(
						".tc-element-setting-fee" => array(
							"operator" => "is",
							"value"    => "1",
						),
					),
				),
				array(
					"id"          => $name . "_hide_element_label_in_cart",
					"wpmldisable" => 1,
					"default"     => "",
					"type"        => "select",
					"tags"        => array(
						"id"   => "builder_" . $name . "_hide_element_label_in_cart",
						"name" => "tm_meta[tmfbuilder][" . $name . "_hide_element_label_in_cart][]",
					),
					"options"     => array(
						array( "text" => esc_html__( 'No', 'woocommerce-tm-extra-product-options' ), "value" => "" ),
						array(
							"text"  => esc_html__( 'Yes', 'woocommerce-tm-extra-product-options' ),
							"value" => "hidden",
						),
					),
					"label"       => esc_html__( 'Hide element label in cart', 'woocommerce-tm-extra-product-options' ),
					"desc"        => esc_html__( 'Choose whether to hide the element label in the cart or not.', 'woocommerce-tm-extra-product-options' ),
				),
				array(
					"id"          => $name . "_hide_element_value_in_cart",
					"wpmldisable" => 1,
					"default"     => "",
					"type"        => "select",
					"tags"        => array(
						"id"   => "builder_" . $name . "_hide_element_value_in_cart",
						"name" => "tm_meta[tmfbuilder][" . $name . "_hide_element_value_in_cart][]",
					),
					"options"     => array(
						array( "text" => esc_html__( 'No', 'woocommerce-tm-extra-product-options' ), "value" => "" ),
						array(
							"text"  => esc_html__( 'No, but hide price', 'woocommerce-tm-extra-product-options' ),
							"value" => "noprice",
						),
						array(
							"text"  => esc_html__( 'Yes', 'woocommerce-tm-extra-product-options' ),
							"value" => "hidden",
						),
						array(
							"text"  => esc_html__( 'Yes, but show price', 'woocommerce-tm-extra-product-options' ),
							"value" => "price",
						),
					),
					"label"       => esc_html__( 'Hide element value in cart', 'woocommerce-tm-extra-product-options' ),
					"desc"        => esc_html__( 'Choose whether to hide the element value in the cart or not.', 'woocommerce-tm-extra-product-options' ),
				),
				array(
					"id"          => $name . "_hide_element_label_in_order",
					"wpmldisable" => 1,
					"default"     => "",
					"type"        => "select",
					"tags"        => array(
						"id"   => "builder_" . $name . "_hide_element_label_in_order",
						"name" => "tm_meta[tmfbuilder][" . $name . "_hide_element_label_in_order][]",
					),
					"options"     => array(
						array( "text" => esc_html__( 'No', 'woocommerce-tm-extra-product-options' ), "value" => "" ),
						array(
							"text"  => esc_html__( 'Yes', 'woocommerce-tm-extra-product-options' ),
							"value" => "hidden",
						),
					),
					"label"       => esc_html__( 'Hide element label in order', 'woocommerce-tm-extra-product-options' ),
					"desc"        => esc_html__( 'Choose whether to hide the element label in the order or not.', 'woocommerce-tm-extra-product-options' ),
				),
				array(
					"id"          => $name . "_hide_element_value_in_order",
					"wpmldisable" => 1,
					"default"     => "",
					"type"        => "select",
					"tags"        => array(
						"id"   => "builder_" . $name . "_hide_element_value_in_order",
						"name" => "tm_meta[tmfbuilder][" . $name . "_hide_element_value_in_order][]",
					),
					"options"     => array(
						array( "text" => esc_html__( 'No', 'woocommerce-tm-extra-product-options' ), "value" => "" ),
						array(
							"text"  => esc_html__( 'No, but hide price', 'woocommerce-tm-extra-product-options' ),
							"value" => "noprice",
						),
						array(
							"text"  => esc_html__( 'Yes', 'woocommerce-tm-extra-product-options' ),
							"value" => "hidden",
						),
						array(
							"text"  => esc_html__( 'Yes, but show price', 'woocommerce-tm-extra-product-options' ),
							"value" => "price",
						),
					),
					"label"       => esc_html__( 'Hide element value in order', 'woocommerce-tm-extra-product-options' ),
					"desc"        => esc_html__( 'Choose whether to hide the element value in the order or not.', 'woocommerce-tm-extra-product-options' ),
				),
				array(
					"id"          => $name . "_hide_element_label_in_floatbox",
					"wpmldisable" => 1,
					"default"     => "",
					"type"        => "select",
					"tags"        => array(
						"id"   => "builder_" . $name . "_hide_element_label_in_floatbox",
						"name" => "tm_meta[tmfbuilder][" . $name . "_hide_element_label_in_floatbox][]",
					),
					"options"     => array(
						array( "text" => esc_html__( 'No', 'woocommerce-tm-extra-product-options' ), "value" => "" ),
						array(
							"text"  => esc_html__( 'Yes', 'woocommerce-tm-extra-product-options' ),
							"value" => "hidden",
						),
					),
					"label"       => esc_html__( 'Hide element label in floating totals box.', 'woocommerce-tm-extra-product-options' ),
					"desc"        => esc_html__( 'Choose whether to hide the element label in the floating totals box or not.', 'woocommerce-tm-extra-product-options' ),
				),
				array(
					"id"          => $name . "_hide_element_value_in_floatbox",
					"wpmldisable" => 1,
					"default"     => "",
					"type"        => "select",
					"tags"        => array(
						"id"   => "builder_" . $name . "_hide_element_value_in_floatbox",
						"name" => "tm_meta[tmfbuilder][" . $name . "_hide_element_value_in_floatbox][]",
					),
					"options"     => array(
						array( "text" => esc_html__( 'No', 'woocommerce-tm-extra-product-options' ), "value" => "" ),
						array(
							"text"  => esc_html__( 'Yes', 'woocommerce-tm-extra-product-options' ),
							"value" => "hidden",
						),
					),
					"label"       => esc_html__( 'Hide element value in floating totals box', 'woocommerce-tm-extra-product-options' ),
					"desc"        => esc_html__( 'Choose whether to hide the element value in the floating totals box or not.', 'woocommerce-tm-extra-product-options' ),
				),
			) ) : array(),
			! empty( $tabs_override["woocommerce_settings"] ) ? $this->_append_div( $name . ( $counter + 2 ) ) : array(),

			$this->_append_div( "" )
		);
	}

	/**
	 * Add tab callback
	 *
	 * @since  4.8.5
	 * @access public
	 */
	public function _prepend_tab_callback( $id = "", $label = "", $closed = "closed", $boxclass = "" ) {

		$datatab = $label;
		if ( is_array( $label ) ) {
			$datatab = $label[1];
		}
		echo "<div class='tm-box" . esc_attr( $boxclass ) . "'>"
		     . "<h4 tabindex='0' data-id='" . esc_attr( $id ) . "-tab' data-tab='" . esc_attr( sanitize_key( $datatab ) ) . "-tab' class='tab-header" . esc_attr( $closed ) . "'>";
		if ( is_array( $label ) ) {
			echo "<i class=\"tab-header-icon " . $label[0] . "\"></i>" . $label[1];
		} else {
			echo esc_html( $label );
		}
		echo "<span class='tcfa tcfa-angle-down tm-arrow'></span>"
		     . "</h4></div>";
	}

	/**
	 * Add tab
	 *
	 * @since  1.0
	 * @access private
	 */
	private function _prepend_tab( $id = "", $label = "", $closed = "closed", $boxclass = "" ) {
		if ( ! empty( $closed ) ) {
			$closed = " " . $closed;
		}
		if ( ! empty( $boxclass ) ) {
			$boxclass = " " . $boxclass;
		}

		return array(
			array(
				"id"      => $id . "_custom_tabstart",
				"default" => "",
				"type"    => "custom",
				"nodiv"   => 1,
				"html"    => array( array( $this, "_prepend_tab_callback" ), array( $id, $label, $closed, $boxclass ) ),
				"label"   => "",
				"desc"    => "",
			),
		);
	}

	/**
	 * Add div callback
	 *
	 * @since  4.8.5
	 * @access public
	 */
	public function _prepend_div_callback( $id = "", $tmtab = "tm-tab" ) {
		echo "<div class='transition " . esc_attr( $tmtab ) . " " . esc_attr( $id ) . "'>";
	}

	/**
	 * Start div
	 *
	 * @since  1.0
	 * @access private
	 */
	private function _prepend_div( $id = "", $tmtab = "tm-tab" ) {
		if ( ! empty( $id ) ) {
			$id .= "-tab";
		}

		return array(
			array(
				"id"      => $id . "_custom_divstart",
				"default" => "",
				"type"    => "custom",
				"nodiv"   => 1,
				"html"    => array( array( $this, "_prepend_div_callback" ), array( $id, $tmtab ) ),
				"label"   => "",
				"desc"    => "",
			),
		);
	}

	/**
	 * End div callback
	 *
	 * @since  4.8.5
	 * @access public
	 */
	public function _append_div_callback() {
		echo "</div>";
	}

	/**
	 * End div
	 *
	 * @since  1.0
	 * @access private
	 */
	private function _append_div( $id = "" ) {
		return array(
			array(
				"id"      => $id . "_custom_divend",
				"default" => "",
				"type"    => "custom",
				"nodiv"   => 1,
				"html"    => array( array( $this, "_append_div_callback" ), array() ),
				"label"   => "",
				"desc"    => "",
			),
		);
	}

	/**
	 * Show logic select box
	 *
	 * @since  1.0
	 * @access public
	 */
	public function builder_showlogic() {
		?>
        <div class="builder-logic-div">
        <div class="tc-row nopadding">
            <select class="epo-rule-toggle">
                <option value="show"><?php esc_html_e( 'Show', 'woocommerce-tm-extra-product-options' ); ?></option>
                <option value="hide"><?php esc_html_e( 'Hide', 'woocommerce-tm-extra-product-options' ); ?></option>
            </select>
            <span><?php esc_html_e( 'this field if', 'woocommerce-tm-extra-product-options' ); ?></span>
            <select class="epo-rule-what">
                <option value="all"><?php esc_html_e( 'all', 'woocommerce-tm-extra-product-options' ); ?></option>
                <option value="any"><?php esc_html_e( 'any', 'woocommerce-tm-extra-product-options' ); ?></option>
            </select>
            <span><?php esc_html_e( 'of these rules match', 'woocommerce-tm-extra-product-options' ); ?>:</span>
        </div>
        <div class="tm-logic-wrapper"></div>
        </div><?php
	}

	/**
	 * Common element options.
	 *
	 * @param string $id element internal id. (key from $this->elements_array)
	 *
	 * @return array List of common element options adjusted by element internal id.
	 *
	 * @since  1.0.0
	 * @access private
	 */
	private function _get_header_array( $id = "header", $type = "", $name = "" ) {
		return apply_filters( 'wc_epo_admin_element_label_options',
			array(
				array(
					"id"          => $id . "_size",
					"wpmldisable" => 1,
					"default"     => ( $id == "section_header" ) ? "3" : "10",
					"type"        => "select",
					"tags"        => array(
						"id"   => "builder_" . $id . "_size",
						"name" => "tm_meta[tmfbuilder][" . $id . "_size][]",
					),
					"options"     =>
						( $id != "section_header" ) ?
							array(
								array(
									"text"  => esc_html__( "H1", 'woocommerce-tm-extra-product-options' ),
									"value" => "1",
								),
								array(
									"text"  => esc_html__( "H2", 'woocommerce-tm-extra-product-options' ),
									"value" => "2",
								),
								array(
									"text"  => esc_html__( "H3", 'woocommerce-tm-extra-product-options' ),
									"value" => "3",
								),
								array(
									"text"  => esc_html__( "H4", 'woocommerce-tm-extra-product-options' ),
									"value" => "4",
								),
								array(
									"text"  => esc_html__( "H5", 'woocommerce-tm-extra-product-options' ),
									"value" => "5",
								),
								array(
									"text"  => esc_html__( "H6", 'woocommerce-tm-extra-product-options' ),
									"value" => "6",
								),
								array(
									"text"  => esc_html__( "p", 'woocommerce-tm-extra-product-options' ),
									"value" => "7",
								),
								array(
									"text"  => esc_html__( "div", 'woocommerce-tm-extra-product-options' ),
									"value" => "8",
								),
								array(
									"text"  => esc_html__( "span", 'woocommerce-tm-extra-product-options' ),
									"value" => "9",
								),
								array(
									"text"  => esc_html__( "label", 'woocommerce-tm-extra-product-options' ),
									"value" => "10",
								),
							) :
							array(
								array(
									"text"  => esc_html__( "H1", 'woocommerce-tm-extra-product-options' ),
									"value" => "1",
								),
								array(
									"text"  => esc_html__( "H2", 'woocommerce-tm-extra-product-options' ),
									"value" => "2",
								),
								array(
									"text"  => esc_html__( "H3", 'woocommerce-tm-extra-product-options' ),
									"value" => "3",
								),
								array(
									"text"  => esc_html__( "H4", 'woocommerce-tm-extra-product-options' ),
									"value" => "4",
								),
								array(
									"text"  => esc_html__( "H5", 'woocommerce-tm-extra-product-options' ),
									"value" => "5",
								),
								array(
									"text"  => esc_html__( "H6", 'woocommerce-tm-extra-product-options' ),
									"value" => "6",
								),
								array(
									"text"  => esc_html__( "p", 'woocommerce-tm-extra-product-options' ),
									"value" => "7",
								),
								array(
									"text"  => esc_html__( "div", 'woocommerce-tm-extra-product-options' ),
									"value" => "8",
								),
								array(
									"text"  => esc_html__( "span", 'woocommerce-tm-extra-product-options' ),
									"value" => "9",
								),
							),
					"label"       => esc_html__( "Label type", 'woocommerce-tm-extra-product-options' ),
					"desc"        => "",
				),
				array(
					"id"               => $id . "_title",
					"default"          => "",
					"type"             => "text",
					"message0x0_class" => ( $type === "element" && $name === "variations" ) ? "builder_hide_for_variation" : "",
					"tags"             => array(
						"class" => "t tm-header-title",
						"id"    => "builder_" . $id . "_title",
						"name"  => "tm_meta[tmfbuilder][" . $id . "_title][]",
						"value" => "",
					),
					"label"            => esc_html__( 'Label', 'woocommerce-tm-extra-product-options' ),
					"desc"             => "",
				),
				array(
					"id"          => $id . "_title_position",
					"wpmldisable" => 1,
					"default"     => "",
					"type"        => "select",
					"tags"        => array(
						"class" => "tc-label-position",
						"id"    => "builder_" . $id . "_title_position",
						"name"  => "tm_meta[tmfbuilder][" . $id . "_title_position][]",
					),
					"options"     => array(
						array(
							"text"  => esc_html__( "Above field", 'woocommerce-tm-extra-product-options' ),
							"value" => "",
						),
						array(
							"text"  => esc_html__( "Left of the field", 'woocommerce-tm-extra-product-options' ),
							"value" => "left",
						),
						array(
							"text"  => esc_html__( "Right of the field", 'woocommerce-tm-extra-product-options' ),
							"value" => "right",
						),
						array(
							"text"  => esc_html__( "Disable", 'woocommerce-tm-extra-product-options' ),
							"value" => "disable",
						),
					),
					"label"       => esc_html__( "Label position", 'woocommerce-tm-extra-product-options' ),
					"desc"        => "",
					"required"    => ( $type === "section" ) ? array(
						".sections_style" => array(
							"operator" => "is",
							"value"    => "",
						),
					) : array(),
				),
				array(
					"id"          => $id . "_title_color",
					"wpmldisable" => 1,
					"default"     => "",
					"type"        => "text",
					"tags"        => array(
						"data-show-input"            => "true",
						"data-show-initial"          => "true",
						"data-allow-empty"           => "true",
						"data-show-alpha"            => "false",
						"data-show-palette"          => "false",
						"data-clickout-fires-change" => "true",
						"data-show-buttons"          => "false",
						"data-preferred-format"      => "hex",
						"class"                      => "tm-color-picker",
						"id"                         => "builder_" . $id . "_title_color",
						"name"                       => "tm_meta[tmfbuilder][" . $id . "_title_color][]",
						"value"                      => "",
					),
					"label"       => esc_html__( 'Label color', 'woocommerce-tm-extra-product-options' ),
					"desc"        => esc_html__( 'Leave empty for default value', 'woocommerce-tm-extra-product-options' ),
				),
				array(
					"id"      => $id . "_subtitle",
					"default" => "",
					"type"    => "textarea",
					"tags"    => array(
						"id"   => "builder_" . $id . "_subtitle",
						"name" => "tm_meta[tmfbuilder][" . $id . "_subtitle][]",
					),
					"label"   => esc_html__( "Subtitle", 'woocommerce-tm-extra-product-options' ),
					"desc"    => "",
				),
				array(
					"id"          => $id . "_subtitle_position",
					"wpmldisable" => 1,
					"default"     => "",
					"type"        => "select",
					"tags"        => array(
						"id"   => "builder_" . $id . "_subtitle_position",
						"name" => "tm_meta[tmfbuilder][" . $id . "_subtitle_position][]",
					),
					"options"     => array(
						array(
							"text"  => esc_html__( "Above field", 'woocommerce-tm-extra-product-options' ),
							"value" => "",
						),
						array(
							"text"  => esc_html__( "Below field", 'woocommerce-tm-extra-product-options' ),
							"value" => "below",
						),
						array(
							"text"  => esc_html__( "Tooltip", 'woocommerce-tm-extra-product-options' ),
							"value" => "tooltip",
						),
						array(
							"text"  => esc_html__( "Icon tooltip left", 'woocommerce-tm-extra-product-options' ),
							"value" => "icontooltipleft",
						),
						array(
							"text"  => esc_html__( "Icon tooltip right", 'woocommerce-tm-extra-product-options' ),
							"value" => "icontooltipright",
						),
					),
					"label"       => esc_html__( "Subtitle position", 'woocommerce-tm-extra-product-options' ),
					"desc"        => "",
				),
				array(
					"id"          => $id . "_subtitle_color",
					"wpmldisable" => 1,
					"default"     => "",
					"type"        => "text",
					"tags"        => array(
						"class" => "tm-color-picker",
						"id"    => "builder_" . $id . "_subtitle_color",
						"name"  => "tm_meta[tmfbuilder][" . $id . "_subtitle_color][]",
						"value" => "",
					),
					"label"       => esc_html__( 'Subtitle color', 'woocommerce-tm-extra-product-options' ),
					"desc"        => esc_html__( 'Leave empty for default value', 'woocommerce-tm-extra-product-options' ),
				),
			)
		);
	}

	/**
	 * Sets element divider option.
	 *
	 * @param string $id element internal id. (key from $this->elements_array)
	 *
	 * @return array Element divider options adjusted by element internal id.
	 *
	 * @since  1.0.0
	 * @access private
	 */
	private function _get_divider_array( $id = "divider", $noempty = 1 ) {
		$_divider = array(
			array(
				"id"               => $id . "_type",
				"wpmldisable"      => 1,
				"message0x0_class" => "builder_hide_for_variation",
				"default"          => "hr",
				"type"             => "select",
				"tags"             => array(
					"id"   => "builder_" . $id . "_type",
					"name" => "tm_meta[tmfbuilder][" . $id . "_type][]",
				),
				"options"          => array(
					array(
						"text"  => esc_html__( "Horizontal rule", 'woocommerce-tm-extra-product-options' ),
						"value" => "hr",
					),
					array(
						"text"  => esc_html__( "Divider", 'woocommerce-tm-extra-product-options' ),
						"value" => "divider",
					),
					array(
						"text"  => esc_html__( "Padding", 'woocommerce-tm-extra-product-options' ),
						"value" => "padding",
					),
				),
				"label"            => esc_html__( "Divider type", 'woocommerce-tm-extra-product-options' ),
				"desc"             => "",
				"required"         => array(
					".tc-label-position" => array(
						"operator" => "is",
						"value"    => array( "", "disable" ),
					),
				),
			),
		);
		if ( empty( $noempty ) ) {
			$_divider[0]["default"] = "none";
			array_push( $_divider[0]["options"], array(
				"text"  => esc_html__( "None", 'woocommerce-tm-extra-product-options' ),
				"value" => "none",
			) );
		}

		return $_divider;
	}

	/**
	 * Prepend logic elements
	 *
	 * @since  1.0
	 * @access private
	 */
	private function _prepend_logic( $id = "" ) {
		return apply_filters( 'wc_epo_admin_element_conditional_logic', array(
			array(
				"id"      => $id . "_uniqid",
				"default" => "",
				"nodiv"   => 1,
				"type"    => "hidden",
				"tags"    => array(
					"class" => "tm-builder-element-uniqid",
					"name"  => "tm_meta[tmfbuilder][" . $id . "_uniqid][]",
					"value" => "",
				),
				"label"   => "",
				"desc"    => "",
			),
			array(
				"id"      => $id . "_clogic",
				"default" => "",
				"nodiv"   => 1,
				"type"    => "hidden",
				"tags"    => array(
					"class" => "tm-builder-clogic",
					"name"  => "tm_meta[tmfbuilder][" . $id . "_clogic][]",
					"value" => "",
				),
				"label"   => "",
				"desc"    => "",
			),
			array(
				"id"        => $id . "_logic",
				"default"   => "",
				"leftclass" => "align-self-start",
				"type"      => "checkbox",
				"tags"      => array(
					"class" => "c activate-element-logic",
					"id"    => "builder_" . $id . "_logic",
					"name"  => "tm_meta[tmfbuilder][" . $id . "_logic][]",
					"value" => "1",
				),
				"extra"     => array( array( $this, "builder_showlogic" ), array() ),
				"label"     => esc_html__( "Element Conditional Logic", 'woocommerce-tm-extra-product-options' ),
				"desc"      => esc_html__( "Enable conditional logic for showing or hiding this element.", 'woocommerce-tm-extra-product-options' ),
			),
		) );
	}

	/**
	 * Element template
	 *
	 * args(
	 * element
	 * width
	 * width_display
	 * internal_name
	 * fields
	 * label
	 * desc
	 * icon
	 * )
	 *
	 * @since 1.0
	 */
	public function template_bitem( $args = array() ) {

		$is_enabled = isset( $args['is_enabled'] ) ? $args['is_enabled'] : '';
		$is_enabled = $is_enabled == '' ? ' element_is_disabled' : '';
		if ( empty( $this->noecho ) ) {
			?><div class="bitem element-<?php echo esc_attr( $args["element"] );
			echo esc_attr( $is_enabled ); ?> <?php echo esc_attr( $args["width"] ); ?>">
			<?php if ( ! empty( $args['is_template'] ) ) { ?>
                <input class="builder_element_type" name="tm_meta[tmfbuilder][element_type][]" type="hidden"
                       value="<?php echo esc_attr( $args["element"] ); ?>"/>
                <input class="div_size" name="tm_meta[tmfbuilder][div_size][]" type="hidden"
                       value="<?php echo esc_attr( $args["width"] ); ?>"/>
			<?php } ?>
            <div class="hstc2">
            <div class="bitem-inner">
                <ul class="bitem-settings">
                    <li class="bitem-setting size"
                        title="<?php echo esc_attr( "Size", "woocommerce-tm-extra-product-options" ); ?>">
                        <span><?php echo esc_html( $args["width_display"] ); ?></span>
                    </li>
                    <li class="bitem-setting minus"
                        title="<?php echo esc_attr( "Reduce Width", "woocommerce-tm-extra-product-options" ); ?>">
                        <i class="tmicon tcfa tcfa-minus"></i>
                    </li>
                    <li class="bitem-setting plus"
                        title="<?php echo esc_attr( "Increase Width", "woocommerce-tm-extra-product-options" ); ?>">
                        <i class="tmicon tcfa tcfa-plus"></i>
                    </li>
                    <li class="bitem-setting edit"
                        title="<?php echo esc_attr( "Edit Element", "woocommerce-tm-extra-product-options" ); ?>">
                        <i class="tmicon tcfa tcfa-edit"></i>
                    </li>
                    <li class="bitem-setting clone"
                        title="<?php echo esc_attr( "Duplicate Element", "woocommerce-tm-extra-product-options" ); ?>">
                        <i class="tmicon tcfa tcfa-copy"></i>
                    </li>
                </ul>
            </div>
            <div class="bitem-inner-info">
                <div class="tm-label-move">
                    <button type="button" class="tmicon tcfa tcfa-grip-vertical move"></button>
                </div>
                <div class="tm-label-icon">
                    <div class="tm-icon-wrap">
                        <i class="tmfa tcfa <?php echo esc_attr( $args["icon"] ); ?>"></i>
                    </div>
                </div>
                <div class="tm-label-info">
                    <div class="tm-label-desc<?php echo ( $args["internal_name"] !== "" ) ? " tc-has-value" : " tc-empty-value"; ?>">
                        <div class="tm-element-label"><?php echo esc_html( $args["label"] ); ?></div>
                        <div class="tm-internal-label"><?php echo esc_html( $args["internal_name"] ); ?></div>
                    </div>
                    <div class="tm-for-bitem tm-label-desc-edit tm-hidden"
                         data-element="<?php echo esc_attr( $args["element"] ); ?>">
						<?php if ( ! empty( $args['is_template'] ) ) { ?>
                            <input type="text" value="<?php echo esc_attr( $args["internal_name"] ); ?>"
                                   name="tm_meta[tmfbuilder][<?php echo esc_attr( $args["element"] ); ?>_internal_name][]"
                                   class="t tm-internal-name">
						<?php } ?>
                    </div>
                    <div class="tm-label"><?php
						if ( $args["desc"] === "&nbsp;" || $args["desc"] === "" ) {
							esc_html_e( "(No title)", "woocommerce-tm-extra-product-options" );
						} else {
							echo esc_html( $args["desc"] );
						}
						?></div>
                    <div class="tm-label-line">
                        <div class="tm-label-line-inner"></div>
                    </div>
                </div>
                <div class="tm-label-delete">
                    <button type="button" class="tmicon tcfa tcfa-times delete"></button>
                </div>
            </div>
            <div class="inside">
            <div class="manager">
            <div class="builder_element_wrap">
			<?php
		}
		if ( isset( $args["fields"] ) && is_array( $args["fields"] ) ) {
			if ( empty( $args['is_template'] ) ) {
				$pointer = count( $this->jsbuilder ) - 1;

				if ( $pointer >= 0 ) {
					$fields_pointer                                             = count( $this->jsbuilder[ $pointer ]["fields"] );
					$this->jsbuilder[ $pointer ]["fields"][ $fields_pointer ]   = array();
					$this->jsbuilder[ $pointer ]["fields"][ $fields_pointer ][] = array(
						"id"      => "element_type",
						"default" => $args["element"],
						"type"    => "hidden",
						"tags"    => array(
							"class" => "builder_element_type",
							"name"  => "tm_meta[tmfbuilder][element_type][]",
						),
					);
					$this->jsbuilder[ $pointer ]["fields"][ $fields_pointer ][] = array(
						"id"      => "div_size",
						"default" => $args["width"],
						"type"    => "hidden",
						"tags"    => array(
							"class" => "div_size",
							"name"  => "tm_meta[tmfbuilder][div_size][]",
						),
					);
					$this->jsbuilder[ $pointer ]["fields"][ $fields_pointer ][] = array(
						"id"      => $args["element"] . "_internal_name",
						"default" => $args["internal_name"],
						"type"    => "text",
						"tags"    => array(
							"class" => "t tm-internal-name",
							"name"  => "tm_meta[tmfbuilder][" . $args["element"] . "_internal_name][]",
						),
					);
					foreach ( $args["fields"] as $value ) {
						if ( $value['type'] !== 'custom' ) {
							if ( $value['type'] === 'custom_multiple' ) {
								if ( isset( $value["html"] ) ) {
									if ( is_array( $value["html"] ) ) {
										$method                     = $value["html"][0];
										$methodargs                 = $value["html"][1];
										$methodargs[0]["return_js"] = true;

										$returned_js = call_user_func_array( $method, $methodargs );

										if ( is_array( $returned_js ) ) {
											$temp_array = array( "id" => "multiple", "multiple" => array() );
											foreach ( $returned_js as $js_value ) {
												$js_value                 = $this->remove_for_js( $js_value );
												$temp_array["multiple"][] = $js_value;
												if ( $value["id"] === "variations_options" ) {
													foreach ( $js_value as $js_value_array ) {
														$js_value_array                                             = $this->remove_for_js( $js_value_array );
														$js_value_array["id"]                                       = $value["id"];
														$this->jsbuilder[ $pointer ]["fields"][ $fields_pointer ][] = $js_value_array;
													}
												}
											}
											$this->jsbuilder[ $pointer ]["fields"][ $fields_pointer ][] = $temp_array;
										}
									}
								}
							} else {
								if ( isset( $value['fill'] ) ) {
									if ( $value['fill'] === "product" ) {
										$product_ids = isset( $value['default'] ) ? $value['default'] : array();
										if ( ! is_array( $product_ids ) ) {
											if ( $product_ids ) {
												$product_ids = array( $product_ids );
											} else {
												$product_ids = array();
											}
										}
										if ( ! isset( $value['options'] ) || ! is_array( $value['options'] ) ) {
											$value['options'] = array();
										}
										foreach ( $product_ids as $product_id ) {
											$product = wc_get_product( $product_id );
											if ( is_object( $product ) ) {
												$value['options'][] = array(
													"text"  => wp_kses_post( $product->get_formatted_name() ),
													"value" => $product_id,
												);
											}
										}
									} elseif ( $value['fill'] === "category" ) {
										$category_ids = isset( $value['default'] ) ? $value['default'] : array();
										if ( ! is_array( $category_ids ) ) {
											$category_ids = array();
										}
										if ( ! isset( $value['options'] ) || ! is_array( $value['options'] ) ) {
											$value['options'] = array();
										}
										foreach ( $category_ids as $category_id ) {
											$current_category_id = wc_clean( wp_unslash( $category_id ) );
											$current_category    = $current_category_id ? get_term_by( 'id', $current_category_id, 'product_cat' ) : false;
											if ( is_object( $current_category ) ) {
												$value['options'][] = array(
													"text"  => wp_kses_post( $current_category->name ),
													"value" => $current_category_id,
												);
											}
										}
									}
								}
								if ( $value["id"] === "product_default_value" ) {
									$temp_product = wc_get_product( $value["default"] );
									if ( $temp_product ) {
										$value["current_selected_text"] = $temp_product->get_title();
									}
								}
								$this->jsbuilder[ $pointer ]["fields"][ $fields_pointer ][] = $this->remove_for_js( $value );
							}
						}

					}

				}

			} else {
				if ( empty( $this->noecho ) ) {
					foreach ( $args["fields"] as $value ) {
						if ( isset( $value['fill'] ) ) {
							if ( $value['type'] === "product" ) {
								$product_ids = isset( $value['default'] ) ? $value['default'] : array();
								if ( ! is_array( $product_ids ) ) {
									$product_ids = array();
								}
								if ( ! isset( $value['options'] ) || ! is_array( $value['options'] ) ) {
									$value['options'] = array();
								}
								foreach ( $product_ids as $product_id ) {
									$product = wc_get_product( $product_id );
									if ( is_object( $product ) ) {
										$value['options'][] = array(
											"text"  => wp_kses_post( $product->get_formatted_name() ),
											"value" => $product_id,
										);
									}
								}
							} elseif ( $value['fill'] === "category" ) {
								$category_ids = isset( $value['default'] ) ? $value['default'] : array();
								if ( ! is_array( $category_ids ) ) {
									$category_ids = array();

									if ( ! isset( $value['options'] ) || ! is_array( $value['options'] ) ) {
										$value['options'] = array();
									}
									foreach ( $category_ids as $category_id ) {
										$current_category_id = wc_clean( wp_unslash( $category_id ) );
										$current_category    = $current_category_id ? get_term_by( 'id', $current_category_id, 'product_cat' ) : false;
										if ( is_object( $current_category ) ) {
											$value['options'][] = array(
												"text"  => wp_kses_post( $current_category->name ),
												"value" => $current_category_id,
											);
										}
									}
								}
							}
						}
						THEMECOMPLETE_EPO_HTML()->tm_make_field( $value, 1 );
					}
				}
			}
		}
		if ( empty( $this->noecho ) ) {
			?>
            </div>
            </div>
            </div>
            </div>
            </div><?php
		}
	}

	/**
	 * Section elements template
	 *
	 * @since  1.0
	 * @access private
	 */
	private function section_elements_template( $args = array() ) {

		$args = shortcode_atts( array(
			'section_fields'           => '',
			'size'                     => '',
			'wpml_is_original_product' => true,
			'sections_internal_name'   => false,
			'is_template'              => false,
		), $args );

		$section_fields           = $args['section_fields'];
		$size                     = $args['size'];
		$wpml_is_original_product = $args['wpml_is_original_product'];
		$sections_internal_name   = $args['sections_internal_name'];

		if ( empty( $args['is_template'] ) ) {
			$pointer                                                          = count( $this->jsbuilder );
			$this->jsbuilder[ $pointer ]                                      = array();
			$this->jsbuilder[ $pointer ]["fields"]                            = array();
			$this->jsbuilder[ $pointer ]["section"]                           = array();
			$this->jsbuilder[ $pointer ]["size"]                              = $size;
			$this->jsbuilder[ $pointer ]["sections_internal_name"]            = $sections_internal_name;
			$this->jsbuilder[ $pointer ]["section"]["sections_internal_name"] = array(
				"id"      => "sections_internal_name",
				"default" => $sections_internal_name,
				"type"    => "text",
				"tags"    => array(
					"class" => "t tm-internal-name",
					"name"  => "tm_meta[tmfbuilder][sections_internal_name][]",
				),
			);
		}
		if ( empty( $this->noecho ) ) {
			?>
            <div class="section_elements closed"><?php
		}
		foreach ( $section_fields as $section_field ) {
			if ( empty( $args['is_template'] ) ) {
				if ( $section_field['type'] !== 'custom' ) {
					$this->jsbuilder[ $pointer ]["section"][ $section_field['id'] ] = $this->remove_for_js( $section_field );
				}

			} else {
				if ( empty( $this->noecho ) ) {
					THEMECOMPLETE_EPO_HTML()->tm_make_field( $section_field, 1 );
				}
			}
		}
		if ( empty( $this->noecho ) ) {
			?></div>
            <div class="section-inner">
                <ul class="section-settings">
                    <li class="section-setting size"
                        title="<?php echo esc_attr( "Size", "woocommerce-tm-extra-product-options" ); ?>">
                        <span><?php echo esc_html( $size ); ?></span>
                    </li>
					<?php if ( $wpml_is_original_product ) { ?>
                        <li class="section-setting minus"
                            title="<?php echo esc_attr( "Reduce Width", "woocommerce-tm-extra-product-options" ); ?>">
                            <i class="tmicon tcfa tcfa-minus"></i>
                        </li>
                        <li class="section-setting plus"
                            title="<?php echo esc_attr( "Increase Width", "woocommerce-tm-extra-product-options" ); ?>">
                            <i class="tmicon tcfa tcfa-plus"></i>
                        </li>
					<?php } ?>
                    <li class="section-setting edit"
                        title="<?php echo esc_attr( "Edit Section", "woocommerce-tm-extra-product-options" ); ?>">
                        <i class="tmicon tcfa tcfa-edit"></i>
                    </li>
					<?php if ( $wpml_is_original_product ) { ?>
                        <li class="bitem-setting clone"
                            title="<?php echo esc_attr( "Duplicate Section", "woocommerce-tm-extra-product-options" ); ?>">
                            <i class="tmicon tcfa tcfa-copy"></i>
                        </li>
					<?php } ?>
                </ul>
            </div>
            <div class="btitle">
				<?php if ( $wpml_is_original_product ) { ?>
                    <button type="button" class="tmicon tcfa tcfa-grip-vertical move"></button>
                    <button type="button" class="tmicon tcfa tcfa-times delete"></button>
				<?php } ?>
                <button type="button" class="tmicon tcfa tcfa-caret-down fold"></button>
                <div class="tm-label-desc<?php echo( ( $sections_internal_name !== "" ) ? " tc-has-value" : " tc-empty-value" ); ?>">
                    <div class='tm-element-label'><?php esc_html_e( "Section", 'woocommerce-tm-extra-product-options' ); ?></div>
                    <div class='tm-internal-label'><?php echo esc_html( $sections_internal_name ); ?></div>
                </div>
                <div class='tm-for-section tm-label-desc-edit tm-hidden'>
					<?php if ( ! empty( $args['is_template'] ) ) { ?>
                        <input type="text" value="<?php echo esc_attr( $sections_internal_name ); ?>"
                               name="tm_meta[tmfbuilder][sections_internal_name][]" class="t tm-internal-name">
					<?php } ?>
                </div>
            </div>
			<?php
		}
	}

	/**
	 * Section template
	 *
	 * @since  1.0
	 * @access private
	 */
	private function section_template( $args = array() ) {

		$args = shortcode_atts( array(
			'section_fields'           => '',
			'size'                     => '',
			'section_size'             => '',
			'sections_slides'          => '',
			'elements'                 => '',
			'wpml_is_original_product' => true,
			'sections_internal_name'   => false,
			'is_template'              => false,
		), $args );

		$section_fields           = $args["section_fields"];
		$size                     = $args["size"];
		$section_size             = $args["section_size"];
		$sections_slides          = $args["sections_slides"];
		$elements                 = $args["elements"];
		$wpml_is_original_product = $args["wpml_is_original_product"];
		$sections_internal_name   = $args["sections_internal_name"];

		if ( $sections_internal_name === false ) {
			$sections_internal_name = esc_html__( "Section", 'woocommerce-tm-extra-product-options' );
		}

		if ( is_array( $elements ) ) {
			$elements = array_values( $elements );
		}

		if ( $sections_slides !== "" && is_array( $elements ) ) {
			if ( empty( $this->noecho ) ) {
				echo "<div class='builder_wrapper tm-slider-wizard " . esc_attr( $section_size ) . "'><div class='builder-section-wrap'>";
			}
			$this->section_elements_template( array(
				'section_fields'           => $section_fields,
				'size'                     => $size,
				'wpml_is_original_product' => $wpml_is_original_product,
				'sections_internal_name'   => $sections_internal_name,
				'is_template'              => $args["is_template"],
			) );

			if ( empty( $this->noecho ) ) {
				echo '<div class="transition tm-slider-wizard-headers">';
			}

			$sections_slides = explode( ",", $sections_slides );
			if ( empty( $this->noecho ) ) {
				$s = 0;

				foreach ( $sections_slides as $key => $value ) {

					echo '<div class="tm-box"><h4 class="tm-slider-wizard-header" data-id="tm-slide' . esc_attr( $s ) . '">' . esc_html( ( $s + 1 ) ) . '</h4></div>';

					$s ++;

				}

				if ( $wpml_is_original_product ) {
					echo '<div class="tm-box tm-add-box"><h4 class="tm-add-tab"><span class="tcfa tcfa-plus"></span></h4></div>';
				}
				echo '</div>';
				if ( $wpml_is_original_product ) {
					echo '<div class="bitem-add tc-prepend tma-nomove"><div class="tm-add-element-action"><button type="button" title="' . esc_html__( "Add element", 'woocommerce-tm-extra-product-options' ) . '" class="builder-add-element tc-button tc-prepend tmfa tcfa tcfa-plus"></button></div></div>';
				}
			}

			$c = 0;
			$s = 0;

			foreach ( $sections_slides as $key => $value ) {

				$value = intval( $value );

				if ( empty( $this->noecho ) ) {
					echo "<div class='bitem_wrapper tm-slider-wizard-tab tm-slide" . esc_attr( $s ) . "'>";
				}
				for ( $_s = $c; $_s < ( $c + $value ); $_s ++ ) {
					if ( isset( $elements[ $_s ] ) ) {
						$this->template_bitem( $elements[ $_s ] );
					}
				}
				if ( empty( $this->noecho ) ) {
					echo "</div>";
				}

				$c = $c + $value;
				$s ++;

			}
			if ( empty( $this->noecho ) ) {
				if ( $wpml_is_original_product ) {
					echo '<div class="bitem-add tc-append tma-nomove"><div class="tm-add-element-action"><button type="button" title="' . esc_html__( "Add element", 'woocommerce-tm-extra-product-options' ) . '" class="builder-add-element tc-button tc-append tmfa tcfa tcfa-plus"></button></div></div>';
				}
				echo "</div></div>";
			}

		} else {
			if ( empty( $this->noecho ) ) {
				echo "<div class='builder_wrapper " . esc_attr( $section_size ) . "'><div class='builder-section-wrap'>";
			}
			$this->section_elements_template( array(
				'section_fields'           => $section_fields,
				'size'                     => $size,
				'wpml_is_original_product' => $wpml_is_original_product,
				'sections_internal_name'   => $sections_internal_name,
				'is_template'              => $args["is_template"],
			) );
			if ( empty( $this->noecho ) ) {
				if ( $wpml_is_original_product ) {
					echo '<div class="bitem-add tc-prepend tma-nomove"><div class="tm-add-element-action"><button type="button" title="' . esc_html__( "Add element", 'woocommerce-tm-extra-product-options' ) . '" class="builder-add-element tc-button tc-prepend tmfa tcfa tcfa-plus"></button></div></div>';
				}

				echo "<div class='bitem_wrapper'>";
			}

			if ( is_array( $elements ) ) {
				foreach ( $elements as $value ) {
					$this->template_bitem( $value );
				}
			}

			if ( empty( $this->noecho ) ) {
				echo "</div>";
				if ( $wpml_is_original_product ) {
					echo '<div class="bitem-add tc-append tma-nomove"><div class="tm-add-element-action"><button type="button" title="' . esc_html__( "Add element", 'woocommerce-tm-extra-product-options' ) . '" class="builder-add-element tc-button tc-append tmfa tcfa tcfa-plus"></button></div></div>';
				}
				echo "</div></div>";
			}

		}

	}

	/**
	 * Generates all hidden sections for use in jQuery.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function template_section_elements( $wpml_is_original_product = true ) {

		$this->section_template( array(
			'section_fields'           => $this->_section_elements,
			'size'                     => $this->sizer["w100"],
			'section_size'             => '',
			'sections_slides'          => '',
			'elements'                 => '',
			'wpml_is_original_product' => $wpml_is_original_product,
			'sections_internal_name'   => false,
			'is_template'              => true,
		) );

	}

	/**
	 * Generates all hidden elements for use in jQuery.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function template_elements() {

		foreach ( $this->get_elements() as $element => $settings ) {
			if ( isset( $this->elements_array[ $element ] ) ) {

				// double quotes are problematic to json_encode.
				$settings["name"] = str_replace( '"', "'", $settings["name"] );
				$_temp_option     = $this->elements_array[ $element ];
				$fields           = array();

				foreach ( $_temp_option as $key => $value ) {
					// no need to auto fill the product element here
					// since it will always be emptyt
					$fields[] = $value;
				}

				$this->template_bitem( array(
					'element'       => $element,
					'width'         => $settings["width"],
					'width_display' => $settings["width_display"],
					'internal_name' => $settings["name"],
					'label'         => $settings["name"],
					'fields'        => $fields,
					'desc'          => '&nbsp;',
					'icon'          => $settings["icon"],
					'is_enabled'    => "1",
					'is_template'   => true,
				) );

			}
		}

	}

	/**
	 * Clear array values
	 *
	 * @since  1.0
	 * @access private
	 */
	private function _tm_clear_array_values( $val ) {
		if ( is_array( $val ) ) {
			return array_map( array( $this, '_tm_clear_array_values' ), $val );
		} else {
			return "";
		}
	}

	/**
	 * Clear array values
	 *
	 * @since  5.0
	 * @access private
	 */
	private function _tm_false_array_values( $val ) {
		if ( is_array( $val ) ) {
			return array_map( array( $this, '_tm_false_array_values' ), $val );
		} else {
			return false;
		}
	}

	/**
	 * Get current jsbuilder.
	 *
	 * @since  5.0
	 * @access public
	 */
	public function get_current_jsbuilder( $post_id = 0, $current_post_id = 0, $wpml_is_original_product = true ) {
		$this->noecho    = true;
		$this->jsbuilder = array();
		$this->print_saved_elements( $post_id, $current_post_id, $wpml_is_original_product );
		$this->noecho = false;

		return $this->jsbuilder;
	}

	/**
	 * Generates all saved elements.
	 * Used when importing CSV files
	 *
	 * @since  5.0
	 * @access public
	 */
	public function ajax_print_saved_elements( $post_id = 0, $current_post_id = 0, $wpml_is_original_product = true ) {
		$this->admin_init();
		$this->print_saved_elements( $post_id, $current_post_id, $wpml_is_original_product );
	}

	/**
	 * Generates all saved elements.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function print_saved_elements( $post_id = 0, $current_post_id = 0, $wpml_is_original_product = true ) {

		$builder         = themecomplete_get_post_meta( $post_id, 'tm_meta', true );
		$current_builder = themecomplete_get_post_meta( $current_post_id, 'tm_meta_wpml', true );

		if ( ! $current_builder ) {
			$current_builder = array();
		} else {
			if ( ! isset( $current_builder['tmfbuilder'] ) ) {
				$current_builder['tmfbuilder'] = array();
			}
			$current_builder = $current_builder['tmfbuilder'];
		}

		if ( ! isset( $builder['tmfbuilder'] ) ) {
			if ( ! is_array( $builder ) ) {
				$builder = array();
			}
			$builder['tmfbuilder'] = array();
		}
		$builder = $builder['tmfbuilder'];

		// only check for element_type meta as if it exists div_size will exist too unless database has been compromised

		if ( ! empty( $post_id ) && is_array( $builder ) && count( $builder ) > 0 && isset( $builder['sections'] ) && isset( $builder['element_type'] ) && is_array( $builder['element_type'] ) && count( $builder['element_type'] ) > 0 ) {
			// All the elements
			$_elements = $builder['element_type'];
			// All element sizes
			$_div_size = $builder['div_size'];

			// All sections (holds element count for each section)
			$_sections = $builder['sections'];
			// All section sizes
			$_sections_size = $builder['sections_size'];

			$_sections_slides = isset( $builder['sections_slides'] ) ? $builder['sections_slides'] : '';

			$_sections_internal_name = isset( $builder['sections_internal_name'] ) ? $builder['sections_internal_name'] : '';

			if ( ! is_array( $_sections ) ) {
				$_sections = array( count( $_elements ) );
			}
			if ( ! is_array( $_sections_size ) ) {
				$_sections_size = array_fill( 0, count( $_sections ), "w100" );
			}

			if ( ! is_array( $_sections_slides ) ) {
				$_sections_slides = array_fill( 0, count( $_sections ), "" );
			}

			if ( ! is_array( $_sections_internal_name ) ) {
				$_sections_internal_name = array_fill( 0, count( $_sections ), false );
			}

			$_helper_counter = 0;
			$_this_elements  = $this->get_elements();

			$additional_currencies = THEMECOMPLETE_EPO_HELPER()->get_additional_currencies();

			$t = array();

			$_counter   = array();
			$id_counter = array();
			for ( $_s = 0; $_s < count( $_sections ); $_s ++ ) {

				$section_fields = array();
				foreach ( $this->_section_elements as $_sk => $_sv ) {
					$transition_counter = $_s;
					$section_use_wpml   = false;
					if ( isset( $current_builder["sections_uniqid"] )
					     && isset( $builder["sections_uniqid"] )
					     && isset( $builder["sections_uniqid"][ $_s ] )
					) {
						// get index of element id in internal array
						$get_current_builder_uniqid_index = array_search( $builder["sections_uniqid"][ $_s ], $current_builder["sections_uniqid"] );
						if ( $get_current_builder_uniqid_index !== null && $get_current_builder_uniqid_index !== false ) {
							$transition_counter = $get_current_builder_uniqid_index;
							$section_use_wpml   = true;
						}
					}
					if ( isset( $builder[ $_sv['id'] ] ) && isset( $builder[ $_sv['id'] ][ $_s ] ) ) {
						$_sv['default'] = $builder[ $_sv['id'] ][ $_s ];
						if ( $section_use_wpml
						     && isset( $current_builder[ $_sv['id'] ] )
						     && isset( $current_builder[ $_sv['id'] ][ $transition_counter ] )
						) {
							$_sv['default'] = $current_builder[ $_sv['id'] ][ $transition_counter ];
						}
					}
					if ( isset( $_sv['tags']['id'] ) ) {
						// we assume that $_sv['tags']['name'] exists if tag id is set
						$_name             = str_replace( array( "[", "]" ), "", $_sv['tags']['name'] );
						$_sv['tags']['id'] = $_name . $_s;
					}
					if ( $_sk == 'sectionuniqid' && ! isset( $builder[ $_sv['id'] ] ) ) {
						$_sv['default'] = THEMECOMPLETE_EPO_HELPER()->tm_uniqid();
					}
					if ( $post_id != $current_post_id && ! empty( $_sv['wpmldisable'] ) ) {
						$_sv['disabled'] = 1;
					}
					if ( $_sv['id'] === "sections_clogic" ) {
						$_sv['default'] = stripslashes_deep( $_sv['default'] );
					}

					$section_fields[] = $_sv;
				}

				$elements_html       = '';
				$elements_html_array = array();

				for ( $k0 = $_helper_counter; $k0 < intval( $_helper_counter + intval( $_sections[ $_s ] ) ); $k0 ++ ) {
					if ( isset( $_elements[ $k0 ] ) ) {
						if ( isset( $this->elements_array[ $_elements[ $k0 ] ] ) ) {
							$elements_html_array[ $k0 ] = "";
							$_temp_option               = $this->elements_array[ $_elements[ $k0 ] ];
							if ( ! isset( $_counter[ $_elements[ $k0 ] ] ) ) {
								$_counter[ $_elements[ $k0 ] ] = 0;
							} else {
								$_counter[ $_elements[ $k0 ] ] ++;
							}
							$internal_name = $_this_elements[ $_elements[ $k0 ] ]["name"];
							if ( isset( $builder[ $_elements[ $k0 ] . '_internal_name' ] )
							     && isset( $builder[ $_elements[ $k0 ] . '_internal_name' ][ $_counter[ $_elements[ $k0 ] ] ] )
							) {
								$internal_name = $builder[ $_elements[ $k0 ] . '_internal_name' ][ $_counter[ $_elements[ $k0 ] ] ];
								if ( $section_use_wpml
								     && isset( $current_builder[ $_elements[ $k0 ] . '_internal_name' ] )
								     && isset( $current_builder[ $_elements[ $k0 ] . '_internal_name' ][ $_counter[ $_elements[ $k0 ] ] ] )
								) {
									$internal_name = $current_builder[ $_elements[ $k0 ] . '_internal_name' ][ $_counter[ $_elements[ $k0 ] ] ];
								}
							}

							// backwards compatibility
							if ( isset( $builder[ $_elements[ $k0 ] . '_price_type' ][ $_counter[ $_elements[ $k0 ] ] ] ) ) {
								switch ( $builder[ $_elements[ $k0 ] . '_price_type' ][ $_counter[ $_elements[ $k0 ] ] ] ) {
									case 'fee':
										$builder[ $_elements[ $k0 ] . '_price_type' ][ $_counter[ $_elements[ $k0 ] ] ] = "";
										$builder[ $_elements[ $k0 ] . '_fee' ][ $_counter[ $_elements[ $k0 ] ] ]        = "1";
										break;
									case 'stepfee':
										$builder[ $_elements[ $k0 ] . '_price_type' ][ $_counter[ $_elements[ $k0 ] ] ] = "step";
										$builder[ $_elements[ $k0 ] . '_fee' ][ $_counter[ $_elements[ $k0 ] ] ]        = "1";
										break;
									case 'currentstepfee':
										$builder[ $_elements[ $k0 ] . '_price_type' ][ $_counter[ $_elements[ $k0 ] ] ] = "currentstep";
										$builder[ $_elements[ $k0 ] . '_fee' ][ $_counter[ $_elements[ $k0 ] ] ]        = "1";
										break;
								}
							}

							$fields       = array();
							$element_desc = "&nbsp;";
							foreach ( $_temp_option as $key => $value ) {
								$transition_counter = $_counter[ $_elements[ $k0 ] ];
								$use_wpml           = false;

								if ( isset( $value['id'] ) ) {
									$_vid = $value['id'];

									if ( ! isset( $t[ $_vid ] ) ) {
										$t[ $_vid ] = isset( $builder[ $value['id'] ] )
											? $builder[ $value['id'] ]
											: null;
										if ( $t[ $_vid ] !== null ) {
											if ( $post_id != $current_post_id && ! empty( $value['wpmldisable'] ) ) {
												$value['disabled'] = 1;
											}

										}
									} elseif ( $t[ $_vid ] !== null ) {
										if ( $post_id != $current_post_id && ! empty( $value['wpmldisable'] ) ) {
											$value['disabled'] = 1;
										}
									}
									if ( isset( $current_builder[ $_elements[ $k0 ] . "_uniqid" ] )
									     && isset( $builder[ $_elements[ $k0 ] . "_uniqid" ] )
									     && isset( $builder[ $_elements[ $k0 ] . "_uniqid" ][ $_counter[ $_elements[ $k0 ] ] ] )
									) {
										// get index of element id in internal array
										$get_current_builder_uniqid_index = array_search( $builder[ $_elements[ $k0 ] . "_uniqid" ][ $_counter[ $_elements[ $k0 ] ] ], $current_builder[ $_elements[ $k0 ] . "_uniqid" ] );
										if ( $get_current_builder_uniqid_index !== null && $get_current_builder_uniqid_index !== false ) {
											$transition_counter = $get_current_builder_uniqid_index;
											$use_wpml           = true;
										}
									}
									if ( $t[ $_vid ] !== null
									     && is_array( $t[ $_vid ] )
									     && count( $t[ $_vid ] ) > 0
									     && isset( $value['default'] )
									     && isset( $t[ $_vid ][ $_counter[ $_elements[ $k0 ] ] ] ) ) {
										$value['default'] = $t[ $_vid ][ $_counter[ $_elements[ $k0 ] ] ];

										if ( $use_wpml
										     && isset( $current_builder[ $value['id'] ] )
										     && isset( $current_builder[ $value['id'] ][ $transition_counter ] )
										) {
											$value['default'] = $current_builder[ $value['id'] ][ $transition_counter ];

										}
										if ( $value['type'] == 'number' ) {
											$value['default'] = themecomplete_convert_local_numbers( $value['default'] );
										}
									}

									if ( $t[ $_vid ] !== null
									     && is_string( $t[ $_vid ] )
									     && isset( $value['default'] )
									) {
										$value['default'] = $t[ $_vid ];

										if ( $value['type'] == 'number' ) {
											$value['default'] = themecomplete_convert_local_numbers( $value['default'] );
										}
									}

									if ( $_vid == $_elements[ $k0 ] . '_header_title' && $value['default'] !== '' ) {
										$element_desc = $value['default'];
									}

									if ( $_elements[ $k0 ] . '_clogic' === $value['id'] ) {
										$value['default'] = stripslashes_deep( $value['default'] );
									}

									// backwards compatibility
									if ( $_elements[ $k0 ] === "radiobuttons" && ( $value['id'] === "radiobuttons_fee" || $value['id'] === "radiobuttons_subscriptionfee" ) ) {
										$_prices_type_base = isset( $builder[ 'multiple_' . 'radiobuttons_options' . '_price_type' ] )
											? $builder[ 'multiple_' . 'radiobuttons_options' . '_price_type' ]
											: array();

										foreach ( $_prices_type_base as $_extra_key => $_extra_value ) {
											foreach ( $_extra_value as $__key => $__value ) {
												if ( $value['id'] === "radiobuttons_fee" && $__value === "fee" ) {
													$value['default'] = "1";
													break 2;
												}
												if ( $value['id'] === "radiobuttons_subscriptionfee" && $__value === "subscriptionfee" ) {
													$value['default']           = "1";
													$_multiple_prices_type_base = $builder[ 'multiple_' . 'radiobuttons_options' . '_price_type' ][ $_counter[ $_elements[ $k0 ] ] ];
													if ( is_array( $_multiple_prices_type_base ) ) {
														foreach ( $_multiple_prices_type_base as $_xtra_key => $_xtra_value ) {
															$builder[ 'multiple_' . 'radiobuttons_options' . '_price_type' ][ $_counter[ $_elements[ $k0 ] ] ][ $_xtra_key ] = "";
														}
													}
													break 2;
												}
											}
										}
									}

									if ( $_elements[ $k0 ] === "selectbox" && $value['id'] === "selectbox_subscriptionfee" ) {
										$_prices_type_base = isset( $builder['selectbox_price_type'] )
											? $builder['selectbox_price_type']
											: array();

										foreach ( $_prices_type_base as $__key => $__value ) {
											if ( $__value === "subscriptionfee" ) {
												$value['default']           = "1";
												$_multiple_prices_type_base = $builder[ 'multiple_' . 'selectbox_options' . '_price_type' ][ $_counter[ $_elements[ $k0 ] ] ];
												if ( is_array( $_multiple_prices_type_base ) ) {
													foreach ( $_multiple_prices_type_base as $_extra_key => $_extra_value ) {
														$builder[ 'multiple_' . 'selectbox_options' . '_price_type' ][ $_counter[ $_elements[ $k0 ] ] ][ $_extra_key ] = "";
													}
												}
												break;
											}
										}
									}

									if ( $value['id'] === $_elements[ $k0 ] . "_enabled" ) {
										if ( $value['default'] == "0" ) {
											$value['default'] = "";
											if ( isset( $builder[ $_elements[ $k0 ] . '_enabled' ][ $_counter[ $_elements[ $k0 ] ] ] ) ) {
												$builder[ $_elements[ $k0 ] . '_enabled' ][ $_counter[ $_elements[ $k0 ] ] ] = "";
											}
										}
									}

									if ( $value['id'] === $_elements[ $k0 ] . "_price_type" ) {
										if ( $value['default'] == "subscriptionfee" ) {
											$value['default']                                                                    = "";
											$builder[ $_elements[ $k0 ] . '_subscriptionfee' ][ $_counter[ $_elements[ $k0 ] ] ] = "1";
											if ( isset( $builder[ $_elements[ $k0 ] . '_price_type' ][ $_counter[ $_elements[ $k0 ] ] ] ) ) {
												$builder[ $_elements[ $k0 ] . '_price_type' ][ $_counter[ $_elements[ $k0 ] ] ] = "";
											}
										}
									}

									if ( $value['id'] == "variations_options" ) {
										if ( $section_use_wpml
										     && isset( $current_builder[ $value['id'] ] )
										) {
											$value['html'] = array(
												array( $this, "builder_sub_variations_options" ),
												array(
													array(
														"meta"       => isset( $current_builder[ $value['id'] ] ) ? $current_builder[ $value['id'] ] : null,
														"product_id" => $current_post_id,
													),
												),
											);
										} else {
											$value['html'] = array(
												array( $this, "builder_sub_variations_options" ),
												array(
													array(
														"meta"       => isset( $builder[ $value['id'] ] ) ? $builder[ $value['id'] ] : null,
														"product_id" => $current_post_id,
													),
												),
											);
										}

									} elseif ( ( isset( $value["tmid"] ) && $value["tmid"] == "populate" ) &&
									           ( $this->all_elements[ $_elements[ $k0 ] ]["type"] == "multiple"
									             || $this->all_elements[ $_elements[ $k0 ] ]["type"] == "multipleall"
									             || $this->all_elements[ $_elements[ $k0 ] ]["type"] == "multiplesingle" )
									) {


										// holds the default checked values (cannot be cached in $t[$_vid])
										$_default_value = isset( $builder[ 'multiple_' . $value['id'] . '_default_value' ] ) ? $builder[ 'multiple_' . $value['id'] . '_default_value' ] : null;

										if ( is_null( $t[ $_vid ] ) ) {
											// needed for WPML
											$_titles_base = isset( $builder[ 'multiple_' . $value['id'] . '_title' ] )
												? $builder[ 'multiple_' . $value['id'] . '_title' ]
												: null;
											$_titles      = isset( $builder[ 'multiple_' . $value['id'] . '_title' ] )
												? isset( $current_builder[ 'multiple_' . $value['id'] . '_title' ] )
													? $current_builder[ 'multiple_' . $value['id'] . '_title' ]
													: $builder[ 'multiple_' . $value['id'] . '_title' ]
												: null;

											$_values_base = isset( $builder[ 'multiple_' . $value['id'] . '_value' ] )
												? $builder[ 'multiple_' . $value['id'] . '_value' ]
												: null;
											$_values      = isset( $builder[ 'multiple_' . $value['id'] . '_value' ] )
												? isset( $current_builder[ 'multiple_' . $value['id'] . '_value' ] )
													? $current_builder[ 'multiple_' . $value['id'] . '_value' ]
													: $builder[ 'multiple_' . $value['id'] . '_value' ]
												: null;

											$_prices_base = isset( $builder[ 'multiple_' . $value['id'] . '_price' ] )
												? $builder[ 'multiple_' . $value['id'] . '_price' ]
												: null;
											$_prices      = isset( $builder[ 'multiple_' . $value['id'] . '_price' ] )
												? isset( $current_builder[ 'multiple_' . $value['id'] . '_price' ] )
													? $current_builder[ 'multiple_' . $value['id'] . '_price' ]
													: $builder[ 'multiple_' . $value['id'] . '_price' ]
												: null;

											$_images_base = isset( $builder[ 'multiple_' . $value['id'] . '_image' ] )
												? $builder[ 'multiple_' . $value['id'] . '_image' ]
												: null;
											$_images      = isset( $builder[ 'multiple_' . $value['id'] . '_image' ] )
												? isset( $current_builder[ 'multiple_' . $value['id'] . '_image' ] )
													? $current_builder[ 'multiple_' . $value['id'] . '_image' ]
													: $builder[ 'multiple_' . $value['id'] . '_image' ]
												: null;

											$_imagesc_base = isset( $builder[ 'multiple_' . $value['id'] . '_imagec' ] )
												? $builder[ 'multiple_' . $value['id'] . '_imagec' ]
												: null;
											$_imagesc      = isset( $builder[ 'multiple_' . $value['id'] . '_imagec' ] )
												? isset( $current_builder[ 'multiple_' . $value['id'] . '_imagec' ] )
													? $current_builder[ 'multiple_' . $value['id'] . '_imagec' ]
													: $builder[ 'multiple_' . $value['id'] . '_imagec' ]
												: null;

											$_imagesp_base = isset( $builder[ 'multiple_' . $value['id'] . '_imagep' ] )
												? $builder[ 'multiple_' . $value['id'] . '_imagep' ]
												: null;
											$_imagesp      = isset( $builder[ 'multiple_' . $value['id'] . '_imagep' ] )
												? isset( $current_builder[ 'multiple_' . $value['id'] . '_imagep' ] )
													? $current_builder[ 'multiple_' . $value['id'] . '_imagep' ]
													: $builder[ 'multiple_' . $value['id'] . '_imagep' ]
												: null;

											$_imagesl_base = isset( $builder[ 'multiple_' . $value['id'] . '_imagel' ] )
												? $builder[ 'multiple_' . $value['id'] . '_imagel' ]
												: null;
											$_imagesl      = isset( $builder[ 'multiple_' . $value['id'] . '_imagel' ] )
												? isset( $current_builder[ 'multiple_' . $value['id'] . '_imagel' ] )
													? $current_builder[ 'multiple_' . $value['id'] . '_imagel' ]
													: $builder[ 'multiple_' . $value['id'] . '_imagel' ]
												: null;

											$_prices_type_base = isset( $builder[ 'multiple_' . $value['id'] . '_price_type' ] )
												? $builder[ 'multiple_' . $value['id'] . '_price_type' ]
												: null;
											$_prices_type      = isset( $builder[ 'multiple_' . $value['id'] . '_price_type' ] )
												? isset( $current_builder[ 'multiple_' . $value['id'] . '_price_type' ] )
													? $current_builder[ 'multiple_' . $value['id'] . '_price_type' ]
													: $builder[ 'multiple_' . $value['id'] . '_price_type' ]
												: null;

											$_sale_prices_base = isset( $builder[ 'multiple_' . $value['id'] . '_sale_price' ] )
												? $builder[ 'multiple_' . $value['id'] . '_sale_price' ]
												: null;
											$_sale_prices      = isset( $builder[ 'multiple_' . $value['id'] . '_sale_price' ] )
												? isset( $current_builder[ 'multiple_' . $value['id'] . '_sale_price' ] )
													? $current_builder[ 'multiple_' . $value['id'] . '_sale_price' ]
													: $builder[ 'multiple_' . $value['id'] . '_sale_price' ]
												: null;

											$c_prices_base      = array();
											$c_prices           = array();
											$c_sale_prices_base = array();
											$c_sale_prices      = array();
											if ( ! empty( $additional_currencies ) && is_array( $additional_currencies ) ) {
												foreach ( $additional_currencies as $ckey => $currency ) {
													$mt_prefix                       = THEMECOMPLETE_EPO_HELPER()->get_currency_price_prefix( $currency );
													$c_prices_base[ $currency ]      = isset( $builder[ 'multiple_' . $value['id'] . '_price' . $mt_prefix ] )
														? $builder[ 'multiple_' . $value['id'] . '_price' . $mt_prefix ]
														: null;
													$c_prices[ $currency ]           = isset( $builder[ 'multiple_' . $value['id'] . '_price' . $mt_prefix ] )
														? isset( $current_builder[ 'multiple_' . $value['id'] . '_price' . $mt_prefix ] )
															? $current_builder[ 'multiple_' . $value['id'] . '_price' . $mt_prefix ]
															: $builder[ 'multiple_' . $value['id'] . '_price' . $mt_prefix ]
														: null;
													$c_sale_prices_base[ $currency ] = isset( $builder[ 'multiple_' . $value['id'] . '_sale_price' . $mt_prefix ] )
														? $builder[ 'multiple_' . $value['id'] . '_sale_price' . $mt_prefix ]
														: null;
													$c_sale_prices[ $currency ]      = isset( $builder[ 'multiple_' . $value['id'] . '_sale_price' . $mt_prefix ] )
														? isset( $current_builder[ 'multiple_' . $value['id'] . '_sale_price' . $mt_prefix ] )
															? $current_builder[ 'multiple_' . $value['id'] . '_sale_price' . $mt_prefix ]
															: $builder[ 'multiple_' . $value['id'] . '_sale_price' . $mt_prefix ]
														: null;
												}
											}

											$_url_base = isset( $builder[ 'multiple_' . $value['id'] . '_url' ] )
												? $builder[ 'multiple_' . $value['id'] . '_url' ]
												: null;
											$_url      = isset( $builder[ 'multiple_' . $value['id'] . '_url' ] )
												? isset( $current_builder[ 'multiple_' . $value['id'] . '_url' ] )
													? $current_builder[ 'multiple_' . $value['id'] . '_url' ]
													: $builder[ 'multiple_' . $value['id'] . '_url' ]
												: null;

											$_description_base = isset( $builder[ 'multiple_' . $value['id'] . '_description' ] )
												? $builder[ 'multiple_' . $value['id'] . '_description' ]
												: null;
											$_description      = isset( $builder[ 'multiple_' . $value['id'] . '_description' ] )
												? isset( $current_builder[ 'multiple_' . $value['id'] . '_description' ] )
													? $current_builder[ 'multiple_' . $value['id'] . '_description' ]
													: $builder[ 'multiple_' . $value['id'] . '_description' ]
												: null;

											$_enabled_base = isset( $builder[ 'multiple_' . $value['id'] . '_enabled' ] )
												? $builder[ 'multiple_' . $value['id'] . '_enabled' ]
												: null;
											$_enabled      = isset( $builder[ 'multiple_' . $value['id'] . '_enabled' ] )
												? isset( $current_builder[ 'multiple_' . $value['id'] . '_enabled' ] )
													? $current_builder[ 'multiple_' . $value['id'] . '_enabled' ]
													: $builder[ 'multiple_' . $value['id'] . '_enabled' ]
												: null;

											$_color_base = isset( $builder[ 'multiple_' . $value['id'] . '_color' ] )
												? $builder[ 'multiple_' . $value['id'] . '_color' ]
												: null;
											$_color      = isset( $builder[ 'multiple_' . $value['id'] . '_color' ] )
												? isset( $current_builder[ 'multiple_' . $value['id'] . '_color' ] )
													? $current_builder[ 'multiple_' . $value['id'] . '_color' ]
													: $builder[ 'multiple_' . $value['id'] . '_color' ]
												: null;

											$_fee_base = isset( $builder[ 'multiple_' . $value['id'] . '_fee' ] )
												? $builder[ 'multiple_' . $value['id'] . '_fee' ]
												: null;
											$_fee      = isset( $builder[ 'multiple_' . $value['id'] . '_fee' ] )
												? isset( $current_builder[ 'multiple_' . $value['id'] . '_fee' ] )
													? $current_builder[ 'multiple_' . $value['id'] . '_fee' ]
													: $builder[ 'multiple_' . $value['id'] . '_fee' ]
												: null;

											$_extra_options = $this->extra_multiple_options;

											$_extra_base = array();
											$_extra      = array();
											$_extra_keys = array();
											foreach ( $_extra_options as $__key => $__name ) {
												if ( $value['id'] == $__name["type"] . "_options" ) {
													$_extra_name   = $__name["name"];
													$_extra_base[] = isset( $builder[ 'multiple_' . $value['id'] . '_' . $_extra_name ] )
														? $builder[ 'multiple_' . $value['id'] . '_' . $_extra_name ]
														: null;
													$_extra[]      = isset( $builder[ 'multiple_' . $value['id'] . '_' . $_extra_name ] )
														? isset( $current_builder[ 'multiple_' . $value['id'] . '_' . $_extra_name ] )
															? $current_builder[ 'multiple_' . $value['id'] . '_' . $_extra_name ]
															: $builder[ 'multiple_' . $value['id'] . '_' . $_extra_name ]
														: null;
													$_extra_keys[] = $__key;
												}
											}

											if ( ! is_null( $_titles_base ) && ! is_null( $_values_base ) && ! is_null( $_prices_base ) ) {
												$t[ $_vid ] = array();
												// backwards combatility

												if ( is_null( $_titles ) ) {
													$_titles = $_titles_base;
												}
												if ( is_null( $_values ) ) {
													$_values = $_values_base;
												}
												if ( is_null( $_prices ) ) {
													$_prices = $_prices_base;
												}
												if ( is_null( $_sale_prices_base ) ) {
													$_sale_prices_base = array_map( array(
														$this,
														'_tm_clear_array_values',
													), $_titles_base );
												}

												if ( is_null( $_sale_prices ) ) {
													$_sale_prices = $_sale_prices_base;
												}

												foreach ( $c_prices as $ckey => $cvalue ) {
													if ( is_null( $cvalue ) ) {
														$c_prices[ $ckey ] = $c_prices_base[ $ckey ];
													}
												}

												foreach ( $c_sale_prices as $ckey => $cvalue ) {
													if ( is_null( $cvalue ) ) {
														$c_sale_prices[ $ckey ] = $c_sale_prices_base[ $ckey ];
													}
												}

												if ( is_null( $_images_base ) ) {
													$_images_base = array_map( array(
														$this,
														'_tm_clear_array_values',
													), $_titles_base );
												}
												if ( is_null( $_images ) ) {
													$_images = $_images_base;
												}

												if ( is_null( $_imagesc_base ) ) {
													$_imagesp_base = array_map( array(
														$this,
														'_tm_clear_array_values',
													), $_titles_base );
												}
												if ( is_null( $_imagesc ) ) {
													$_imagesc = $_imagesc_base;
												}

												if ( is_null( $_imagesp_base ) ) {
													$_imagesp_base = array_map( array(
														$this,
														'_tm_clear_array_values',
													), $_titles_base );
												}
												if ( is_null( $_imagesp ) ) {
													$_imagesp = $_imagesp_base;
												}

												if ( is_null( $_imagesl_base ) ) {
													$_imagesl_base = array_map( array(
														$this,
														'_tm_clear_array_values',
													), $_titles_base );
												}
												if ( is_null( $_imagesl ) ) {
													$_imagesl = $_imagesl_base;
												}

												if ( is_null( $_prices_type_base ) ) {
													$_prices_type_base = array_map( array(
														$this,
														'_tm_clear_array_values',
													), $_prices_base );
												}
												if ( is_null( $_prices_type ) ) {
													$_prices_type = $_prices_type_base;
												}

												if ( is_null( $_url_base ) ) {
													$_url_base = array_map( array(
														$this,
														'_tm_clear_array_values',
													), $_titles_base );
												}
												if ( is_null( $_url ) ) {
													$_url = $_url_base;
												}
												if ( is_null( $_description_base ) ) {
													$_description_base = array_map( array(
														$this,
														'_tm_clear_array_values',
													), $_titles_base );
												}
												if ( is_null( $_description ) ) {
													$_description = $_description_base;
												}
												if ( is_null( $_enabled_base ) ) {
													$_enabled_base = array_map( array(
														$this,
														'_tm_false_array_values',
													), $_titles_base );
												}
												if ( is_null( $_enabled ) ) {
													$_enabled = $_enabled_base;
												}
												if ( is_null( $_color_base ) ) {
													$_color_base = array_map( array(
														$this,
														'_tm_clear_array_values',
													), $_titles_base );
												}
												if ( is_null( $_color ) ) {
													$_color = $_color_base;
												}

												if ( is_null( $_fee_base ) ) {
													$_fee_base = array_map( array(
														$this,
														'_tm_clear_array_values',
													), $_titles_base );
												}
												if ( is_null( $_fee ) ) {
													$_fee = $_fee_base;
												}

												foreach ( $_extra_base as $_extra_base_key => $_extra_base_value ) {
													if ( is_null( $_extra_base[ $_extra_base_key ] ) ) {
														$_extra_base[ $_extra_base_key ] = array_map( array(
															$this,
															'_tm_clear_array_values',
														), $_titles_base );
													}
												}
												foreach ( $_extra as $_extra_key => $_extra_value ) {
													if ( is_null( $_extra_base[ $_extra_key ] ) ) {
														$_extra_base[ $_extra_key ] = array_map( array(
															$this,
															'_tm_clear_array_values',
														), $_titles_base );
													}
												}

												foreach ( $_titles_base as $option_key => $option_value ) {

													$use_original_builder = false;
													$_option_key          = $option_key;
													if ( isset( $current_builder[ $_elements[ $k0 ] . "_uniqid" ] )
													     && isset( $builder[ $_elements[ $k0 ] . "_uniqid" ] )
													     && isset( $builder[ $_elements[ $k0 ] . "_uniqid" ][ $option_key ] )
													) {
														// get index of element id in internal array
														$get_current_builder_uniqid_index = array_search( $builder[ $_elements[ $k0 ] . "_uniqid" ][ $option_key ], $current_builder[ $_elements[ $k0 ] . "_uniqid" ] );
														if ( $get_current_builder_uniqid_index !== null && $get_current_builder_uniqid_index !== false ) {
															$_option_key = $get_current_builder_uniqid_index;
														} else {
															$use_original_builder = true;
														}
													}

													if ( ! isset( $_titles_base[ $_option_key ] ) ) {
														continue;
													}

													if ( ! isset( $_imagesc[ $_option_key ] ) ) {
														$_imagesc[ $_option_key ] = array_map( array(
															$this,
															'_tm_clear_array_values',
														), $_titles_base[ $_option_key ] );
													}
													if ( ! isset( $_imagesc_base[ $_option_key ] ) ) {
														$_imagesc_base[ $_option_key ] = array_map( array(
															$this,
															'_tm_clear_array_values',
														), $_titles_base[ $_option_key ] );
													}

													if ( ! isset( $_imagesp[ $_option_key ] ) ) {
														$_imagesp[ $_option_key ] = array_map( array(
															$this,
															'_tm_clear_array_values',
														), $_titles_base[ $_option_key ] );
													}
													if ( ! isset( $_imagesp_base[ $_option_key ] ) ) {
														$_imagesp_base[ $_option_key ] = array_map( array(
															$this,
															'_tm_clear_array_values',
														), $_titles_base[ $_option_key ] );
													}

													if ( ! isset( $_imagesl[ $_option_key ] ) ) {
														$_imagesl[ $_option_key ] = array_map( array(
															$this,
															'_tm_clear_array_values',
														), $_titles_base[ $_option_key ] );
													}
													if ( ! isset( $_imagesl_base[ $_option_key ] ) ) {
														$_imagesl_base[ $_option_key ] = array_map( array(
															$this,
															'_tm_clear_array_values',
														), $_titles_base[ $_option_key ] );
													}

													if ( ! isset( $_sale_prices_base[ $_option_key ] ) ) {
														$_sale_prices_base[ $_option_key ] = array_map( array(
															$this,
															'_tm_clear_array_values',
														), $_titles_base[ $_option_key ] );
													}

													if ( ! isset( $_sale_prices[ $_option_key ] ) ) {
														$_sale_prices[ $_option_key ] = array_map( array(
															$this,
															'_tm_clear_array_values',
														), $_titles_base[ $_option_key ] );
													}

													if ( ! isset( $_description_base[ $_option_key ] ) ) {
														$_description_base[ $_option_key ] = array_map( array(
															$this,
															'_tm_clear_array_values',
														), $_titles_base[ $_option_key ] );
													}

													if ( ! isset( $_description[ $_option_key ] ) ) {
														$_description[ $_option_key ] = array_map( array(
															$this,
															'_tm_clear_array_values',
														), $_titles_base[ $_option_key ] );
													}

													if ( ! isset( $_enabled_base[ $_option_key ] ) ) {
														$_enabled_base[ $_option_key ] = array_map( array(
															$this,
															'_tm_false_array_values',
														), $_titles_base[ $_option_key ] );
													}

													if ( ! isset( $_enabled[ $_option_key ] ) ) {
														$_enabled[ $_option_key ] = array_map( array(
															$this,
															'_tm_false_array_values',
														), $_titles_base[ $_option_key ] );
													}

													if ( ! isset( $_color[ $_option_key ] ) ) {
														$_color[ $_option_key ] = array_map( array(
															$this,
															'_tm_clear_array_values',
														), $_titles_base[ $_option_key ] );
													}
													if ( ! isset( $_color_base[ $_option_key ] ) ) {
														$_color_base[ $_option_key ] = array_map( array(
															$this,
															'_tm_clear_array_values',
														), $_titles_base[ $_option_key ] );
													}

													if ( ! isset( $_fee_base[ $_option_key ] ) ) {
														$_fee_base[ $_option_key ] = array_map( array(
															$this,
															'_tm_clear_array_values',
														), $_titles_base[ $_option_key ] );
													}

													if ( ! isset( $_fee[ $_option_key ] ) ) {
														$_fee[ $_option_key ] = array_map( array(
															$this,
															'_tm_clear_array_values',
														), $_titles_base[ $_option_key ] );
													}

													foreach ( $_extra_base as $_extra_base_key => $_extra_base_value ) {
														if ( ! isset( $_extra_base[ $_extra_base_key ][ $_option_key ] ) ) {
															$_extra_base[ $_extra_base_key ][ $_option_key ] = array_map( array(
																$this,
																'_tm_clear_array_values',
															), $_titles_base[ $_option_key ] );
														}
													}
													foreach ( $_extra as $_extra_key => $_extra_value ) {
														if ( ! isset( $_extra[ $_extra_key ][ $_option_key ] ) ) {
															$_extra[ $_extra_key ][ $_option_key ] = array_map( array(
																$this,
																'_tm_clear_array_values',
															), $_titles_base[ $_option_key ] );
														}
													}

													// backwards compatibility
													foreach ( $_enabled_base as $_extra_key => $_extra_value ) {
														foreach ( $_extra_value as $__key => $__value ) {
															if ( $__value === "0" ) {
																$_enabled_base[ $_extra_key ][ $__key ] = "";
															}
														}
													}
													foreach ( $_enabled as $_extra_key => $_extra_value ) {
														foreach ( $_extra_value as $__key => $__value ) {
															if ( $__value === "0" ) {
																$_enabled[ $_extra_key ][ $__key ] = "";
															}
														}
													}
													foreach ( $_prices_type_base as $_extra_key => $_extra_value ) {
														foreach ( $_extra_value as $__key => $__value ) {
															if ( $__value === "fee" ) {
																if ( $_elements[ $k0 ] === "checkboxes" ) {
																	$_fee_base[ $_extra_key ][ $__key ] = "1";
																}

																$_prices_type_base[ $_extra_key ][ $__key ] = "";
															}
														}
													}
													foreach ( $_prices_type as $_extra_key => $_extra_value ) {
														foreach ( $_extra_value as $__key => $__value ) {
															if ( $__value === "fee" ) {
																if ( $_elements[ $k0 ] === "checkboxes" ) {
																	$_fee[ $_extra_key ][ $__key ] = "1";
																}

																$_prices_type[ $_extra_key ][ $__key ] = "";
															}
														}
													}

													if ( $use_original_builder ) {
														$obvalues = array(
															"title"       => $_titles_base[ $_option_key ],
															"value"       => $_values_base[ $_option_key ],
															"price"       => $_prices_base[ $_option_key ],
															"sale_price"  => $_sale_prices_base[ $_option_key ],
															"image"       => $_images_base[ $_option_key ],
															"imagec"      => $_imagesc_base[ $_option_key ],
															"imagep"      => $_imagesp_base[ $_option_key ],
															"imagel"      => $_imagesl_base[ $_option_key ],
															"price_type"  => $_prices_type_base[ $_option_key ],
															"url"         => $_url_base[ $_option_key ],
															"description" => $_description_base[ $_option_key ],
															"enabled"     => $_enabled_base[ $_option_key ],
															"color"       => $_color_base[ $_option_key ],
															"fee"         => $_fee_base[ $_option_key ],
														);
														$obvalues = apply_filters( "wc_epo_obvalues", $obvalues, $builder, $value, $current_builder, $_titles_base, $_option_key );
														foreach ( $c_prices_base as $ckey => $cvalue ) {
															$mt_prefix                        = THEMECOMPLETE_EPO_HELPER()->get_currency_price_prefix( $ckey );
															$obvalues[ "price" . $mt_prefix ] = $cvalue[ $_option_key ];
														}
														foreach ( $c_sale_prices_base as $ckey => $cvalue ) {
															$mt_prefix                             = THEMECOMPLETE_EPO_HELPER()->get_currency_price_prefix( $ckey );
															$obvalues[ "sale_price" . $mt_prefix ] = $cvalue[ $_option_key ];
														}
														foreach ( $_extra_base as $_extra_base_key => $_extra_base_value ) {
															$obvalues[ $_extra_options[ $_extra_keys[ $_extra_base_key ] ]["name"] ] = $_extra_base_value[ $_option_key ];
														}
														$t[ $_vid ][] = $obvalues;
													} else {
														$cbvalues = array(
															"title"       => THEMECOMPLETE_EPO_HELPER()->build_array( $_titles[ $_option_key ], $_titles_base[ $_option_key ] ),
															"value"       => THEMECOMPLETE_EPO_HELPER()->build_array( $_values[ $_option_key ], $_values_base[ $_option_key ] ),
															"price"       => THEMECOMPLETE_EPO_HELPER()->build_array( $_prices[ $_option_key ], $_prices_base[ $_option_key ] ),
															"sale_price"  => THEMECOMPLETE_EPO_HELPER()->build_array( $_sale_prices[ $_option_key ], $_sale_prices_base[ $_option_key ] ),
															"image"       => THEMECOMPLETE_EPO_HELPER()->build_array( $_images[ $_option_key ], $_images_base[ $_option_key ] ),
															"imagec"      => THEMECOMPLETE_EPO_HELPER()->build_array( $_imagesc[ $_option_key ], $_imagesc_base[ $_option_key ] ),
															"imagep"      => THEMECOMPLETE_EPO_HELPER()->build_array( $_imagesp[ $_option_key ], $_imagesp_base[ $_option_key ] ),
															"imagel"      => THEMECOMPLETE_EPO_HELPER()->build_array( $_imagesl[ $_option_key ], $_imagesl_base[ $_option_key ] ),
															"price_type"  => THEMECOMPLETE_EPO_HELPER()->build_array( $_prices_type[ $_option_key ], $_prices_type_base[ $_option_key ] ),
															"url"         => THEMECOMPLETE_EPO_HELPER()->build_array( $_url[ $_option_key ], $_url_base[ $_option_key ] ),
															"description" => THEMECOMPLETE_EPO_HELPER()->build_array( $_description[ $_option_key ], $_description_base[ $_option_key ] ),
															"enabled"     => THEMECOMPLETE_EPO_HELPER()->build_array( $_enabled[ $_option_key ], $_enabled_base[ $_option_key ] ),
															"color"       => THEMECOMPLETE_EPO_HELPER()->build_array( $_color[ $_option_key ], $_color_base[ $_option_key ] ),
															"fee"         => THEMECOMPLETE_EPO_HELPER()->build_array( $_fee[ $_option_key ], $_fee_base[ $_option_key ] ),
														);
														$cbvalues = apply_filters( "wc_epo_cbvalues", $cbvalues, $builder, $value, $current_builder, $_titles_base, $_option_key );
														foreach ( $c_prices as $ckey => $cvalue ) {
															$mt_prefix = THEMECOMPLETE_EPO_HELPER()->get_currency_price_prefix( $ckey );
															if ( ! isset( $cvalue[ $_option_key ] ) ) {
																continue;
															}
															$cbvalues[ "price" . $mt_prefix ] = THEMECOMPLETE_EPO_HELPER()->build_array( $cvalue[ $_option_key ],
																$c_prices_base[ $ckey ][ $_option_key ] );
														}
														foreach ( $c_sale_prices as $ckey => $cvalue ) {
															$mt_prefix = THEMECOMPLETE_EPO_HELPER()->get_currency_price_prefix( $ckey );
															if ( ! isset( $cvalue[ $_option_key ] ) ) {
																continue;
															}
															$cbvalues[ "sale_price" . $mt_prefix ] = THEMECOMPLETE_EPO_HELPER()->build_array(
																$cvalue[ $_option_key ],
																$c_sale_prices_base[ $ckey ][ $_option_key ] );
														}
														foreach ( $_extra_base as $_extra_base_key => $_extra_base_value ) {

															$cbvalues[ $_extra_options[ $_extra_keys[ $_extra_base_key ] ]["name"] ] = $_extra_base_value[ $_option_key ];
														}
														$t[ $_vid ][] = $cbvalues;
													}
												}
											}
										}
										if ( ! is_null( $t[ $_vid ] ) && isset( $t[ $_vid ][ $_counter[ $_elements[ $k0 ] ] ] ) ) {

											$value['html'] = array(
												array( $this, "builder_sub_options" ),
												array(
													array(
														'options'       => $t[ $_vid ][ $_counter[ $_elements[ $k0 ] ] ],
														'name'          => 'multiple_' . $value['id'],
														'counter'       => $_counter[ $_elements[ $k0 ] ],
														'default_value' => $_default_value,
													),
												),
											);

										}
									}
								}
								// we assume that $value['tags']['name'] exists if tag id is set
								if ( isset( $value['tags']['id'] ) ) {
									$_name = str_replace( array( "[", "]" ), "", $value['tags']['name'] );
									if ( ! isset( $id_counter[ $_name ] ) ) {
										$id_counter[ $_name ] = 0;
									} else {
										$id_counter[ $_name ] = $id_counter[ $_name ] + 1;
									}
									$value['tags']['id'] = $_name . $id_counter[ $_name ];
								}

								$fields[] = $value;
							}

							$elements_html_array[ $k0 ] = array(
								'element'       => $_elements[ $k0 ],
								'width'         => $_div_size[ $k0 ],
								'width_display' => $this->sizer[ $_div_size[ $k0 ] ],
								'internal_name' => $internal_name,
								'fields'        => $fields,
								'label'         => $_this_elements[ $_elements[ $k0 ] ]["name"],
								'desc'          => $element_desc,
								'icon'          => $_this_elements[ $_elements[ $k0 ] ]["icon"],
								'is_enabled'    => isset( $builder[ $_elements[ $k0 ] . '_enabled' ][ $_counter[ $_elements[ $k0 ] ] ] ) ? $builder[ $_elements[ $k0 ] . '_enabled' ][ $_counter[ $_elements[ $k0 ] ] ] : '1',
							);
						}
					}
				}

				$this->section_template( array(
					'section_fields'           => $section_fields,
					'size'                     => $this->sizer[ $_sections_size[ $_s ] ],
					'section_size'             => $_sections_size[ $_s ],
					'sections_slides'          => isset( $_sections_slides[ $_s ] ) ? $_sections_slides[ $_s ] : "",
					'elements'                 => $elements_html_array,
					'wpml_is_original_product' => $wpml_is_original_product,
					'sections_internal_name'   => isset( $_sections_internal_name[ $_s ] ) ? $_sections_internal_name[ $_s ] : "",
				) );

				$_helper_counter = intval( $_helper_counter + intval( $_sections[ $_s ] ) );
			}
		}

	}

	/**
	 * Get tax classes
	 *
	 * @since 1.0
	 */
	public function get_tax_classes() {
		// Get tax class options
		$tax_classes = array_filter( array_map( 'trim', explode( "\n", get_option( 'woocommerce_tax_classes' ) ) ) );
		if ( empty( $tax_classes ) && class_exists( 'WC_Tax' ) ) {
			$tax_classes = WC_Tax::get_tax_classes();
		}
		$classes_options      = array();
		$classes_options['']  = esc_html__( 'Inherit product tax class', 'woocommerce-tm-extra-product-options' );
		$classes_options['@'] = esc_html__( 'Standard', 'woocommerce-tm-extra-product-options' );
		if ( $tax_classes ) {
			foreach ( $tax_classes as $class ) {
				$classes_options[ sanitize_title( $class ) ] = esc_html( $class );
			}
		}
		$classes = array();

		foreach ( $classes_options as $value => $label ) {
			$classes[] = array(
				"text"  => esc_html( $label ),
				"value" => esc_attr( $value ),
			);
		}

		return $classes;
	}

	/**
	 * Helper to generate html for Image replacement
	 *
	 * @see    builder_sub_variations_options
	 * @since  4.8.5
	 * @access public
	 */
	public function settings_term_variations_image_helper( $value = "" ) {

		echo '&nbsp;<span data-tm-tooltip-html="' . esc_attr( esc_html__( "Choose the image to use in place of the radio button.", 'woocommerce-tm-extra-product-options' ) ) . '" class="tm_upload_button cp_button tm-tooltip"><i class="tcfa tcfa-upload"></i></span><span data-tm-tooltip-html="' . esc_attr( esc_html__( "Remove the image.", 'woocommerce-tm-extra-product-options' ) ) . '" class="tm-upload-button-remove cp-button tm-tooltip"><i class="tcfa tcfa-times"></i></span>';
		echo '<span class="tm_upload_image tm_upload_imagep"><img class="tm_upload_image_img" alt="&nbsp;" src="' . esc_attr( $value ) . '" /></span>';

	}

	/**
	 * Helper to generate html for Product Image replacement
	 *
	 * @see    builder_sub_variations_options
	 * @since  4.8.5
	 * @access public
	 */
	public function settings_term_variations_imagep_helper( $value = "" ) {

		echo '&nbsp;<span data-tm-tooltip-html="' . esc_attr( esc_html__( "Choose the image to replace the product image with.", 'woocommerce-tm-extra-product-options' ) ) . '" class="tm_upload_button tm_upload_buttonp cp_button tm-tooltip"><i class="tcfa tcfa-upload"></i></span><span data-tm-tooltip-html="' . esc_attr( esc_html__( "Remove the image.", 'woocommerce-tm-extra-product-options' ) ) . '" class="tm-upload-button-remove cp-button tm-tooltip"><i class="tcfa tcfa-times"></i></span>';
		echo '<span class="tm_upload_image"><img class="tm_upload_image_img" alt="&nbsp;" src="' . esc_attr( $value ) . '" /></span>';

	}

	/**
	 * Generates element sub-options for variations.
	 *
	 * @since  3.0.0
	 * @access public
	 */
	public function builder_sub_variations_options( $args = array() ) {

		$args = shortcode_atts( array(
			'meta'       => array(),
			'product_id' => 0,
			'return_js'  => false,
		), $args );

		$meta       = $args['meta'];
		$product_id = $args['product_id'];
		$return_js  = $args['return_js'];

		$js_object = array();

		$o     = array();
		$name  = "tm_builder_variation_options";
		$class = " withupload";

		$settings_attribute = array(
			array(
				"id"      => "variations_display_as",
				"default" => "select",
				"type"    => "select",
				"tags"    => array(
					"class" => "variations-display-as",
					"id"    => "builder_%id%",
					"name"  => "tm_meta[tmfbuilder][variations_options][%attribute_id%][%id%]",
				),
				"options" => array(
					array(
						"text"  => esc_html__( "Select boxes", 'woocommerce-tm-extra-product-options' ),
						"value" => "select",
					),
					array(
						"text"  => esc_html__( "Radio buttons", 'woocommerce-tm-extra-product-options' ),
						"value" => "radio",
					),
					array(
						"text"  => esc_html__( "Radio buttons and image at start of the label", 'woocommerce-tm-extra-product-options' ),
						"value" => "radiostart",
					),
					array(
						"text"  => esc_html__( "Radio buttons and image at end of the label", 'woocommerce-tm-extra-product-options' ),
						"value" => "radioend",
					),
					array(
						"text"  => esc_html__( "Image swatches", 'woocommerce-tm-extra-product-options' ),
						"value" => "image",
					),
					array(
						"text"  => esc_html__( "Color swatches", 'woocommerce-tm-extra-product-options' ),
						"value" => "color",
					),
				),
				"label"   => esc_html__( "Display as", 'woocommerce-tm-extra-product-options' ),
				"desc"    => esc_html__( "Select the display type of this attribute.", 'woocommerce-tm-extra-product-options' ),
			),
			array(
				"id"      => "variations_label",
				"default" => "",
				"type"    => "text",
				"tags"    => array(
					"class" => "t",
					"id"    => "builder_%id%",
					"name"  => "tm_meta[tmfbuilder][variations_options][%attribute_id%][%id%]",
					"value" => "",
				),
				"label"   => esc_html__( 'Attribute Label', 'woocommerce-tm-extra-product-options' ),
				"desc"    => esc_html__( 'Leave blank to use the original attribute label.', 'woocommerce-tm-extra-product-options' ),
			),
			array(
				"id"               => "variations_show_reset_button",
				"message0x0_class" => "tma-hide-for-select-box",
				"default"          => "",
				"type"             => "select",
				"tags"             => array(
					"id"   => "builder_%id%",
					"name" => "tm_meta[tmfbuilder][variations_options][%attribute_id%][%id%]",
				),
				"options"          => array(
					array( "text" => esc_html__( "Disable", 'woocommerce-tm-extra-product-options' ), "value" => "" ),
					array( "text" => esc_html__( "Enable", 'woocommerce-tm-extra-product-options' ), "value" => "yes" ),
				),
				"label"            => esc_html__( 'Show reset button', 'woocommerce-tm-extra-product-options' ),
				"desc"             => esc_html__( 'Enables the display of a reset button for this attribute.', 'woocommerce-tm-extra-product-options' ),
			),
			array(
				"id"      => "variations_class",
				"default" => "",
				"type"    => "text",
				"tags"    => array(
					"class" => "t",
					"id"    => "builder_%id%",
					"name"  => "tm_meta[tmfbuilder][variations_options][%attribute_id%][%id%]",
					"value" => "",
				),
				"label"   => esc_html__( 'Attribute element class name', 'woocommerce-tm-extra-product-options' ),
				"desc"    => esc_html__( 'Enter an extra class name to add to this attribute element', 'woocommerce-tm-extra-product-options' ),
			),
			array(
				"id"               => "variations_items_per_row",
				"message0x0_class" => "tma-hide-for-select-box",
				"default"          => "",
				"type"             => "number",
				"extra"            => array( array( $this, "add_setting_items_per_row_helper" ), array() ),
				"tags"             => array(
					"class" => "n",
					"id"    => "builder_%id%",
					"name"  => "tm_meta[tmfbuilder][variations_options][%attribute_id%][%id%]",
					"value" => "",
				),
				"label"            => esc_html__( 'Items per row (Desktops and laptops)', 'woocommerce-tm-extra-product-options' ),
				"desc"             => esc_html__( 'Use this field to make a grid display. Enter how many items per row for the grid or leave blank for normal display.', 'woocommerce-tm-extra-product-options' ),
			),

			//@media only screen and (min-device-width : 768px) and (max-device-width : 1024px) {
			array(
				"id"               => "variations_items_per_row_tablets",
				"message0x0_class" => "builder_responsive_div",
				"wpmldisable"      => 1,
				"default"          => "",
				"type"             => "number",
				"tags"             => array(
					"class" => "n",
					"id"    => "builder_%id%",
					"name"  => "tm_meta[tmfbuilder][variations_options][%attribute_id%][%id%]",
				),
				"label"            => esc_html__( 'Items per row (Tablets landscape)', 'woocommerce-tm-extra-product-options' ),
				"desc"             => esc_html__( 'Use this field to make a grid display. Enter how many items per row for the grid or leave blank for normal display.', 'woocommerce-tm-extra-product-options' ),
			),
			//@media only screen and (min-device-width : 481px) and (max-device-width : 767px) {
			array(
				"id"               => "variations_items_per_row_tablets_small",
				"message0x0_class" => "builder_responsive_div",
				"wpmldisable"      => 1,
				"default"          => "",
				"type"             => "number",
				"tags"             => array(
					"class" => "n",
					"id"    => "builder_%id%",
					"name"  => "tm_meta[tmfbuilder][variations_options][%attribute_id%][%id%]",
				),
				"label"            => esc_html__( 'Items per row (Tablets portrait)', 'woocommerce-tm-extra-product-options' ),
				"desc"             => esc_html__( 'Use this field to make a grid display. Enter how many items per row for the grid or leave blank for normal display.', 'woocommerce-tm-extra-product-options' ),
			),
			//@media only screen and (min-device-width : 320px) and (max-device-width : 480px) {
			array(
				"id"               => "variations_items_per_row_smartphones",
				"message0x0_class" => "builder_responsive_div",
				"wpmldisable"      => 1,
				"default"          => "",
				"type"             => "number",
				"tags"             => array(
					"class" => "n",
					"id"    => "builder_%id%",
					"name"  => "tm_meta[tmfbuilder][variations_options][%attribute_id%][%id%]",
				),
				"label"            => esc_html__( 'Items per row (Smartphones)', 'woocommerce-tm-extra-product-options' ),
				"desc"             => esc_html__( 'Use this field to make a grid display. Enter how many items per row for the grid or leave blank for normal display.', 'woocommerce-tm-extra-product-options' ),
			),
			//@media only screen and (min-device-width: 320px) and (max-device-width: 568px) and (-webkit-min-device-pixel-ratio: 2) {
			array(
				"id"               => "variations_items_per_row_iphone5",
				"message0x0_class" => "builder_responsive_div",
				"wpmldisable"      => 1,
				"default"          => "",
				"type"             => "number",
				"tags"             => array(
					"class" => "n",
					"id"    => "builder_%id%",
					"name"  => "tm_meta[tmfbuilder][variations_options][%attribute_id%][%id%]",
				),
				"label"            => esc_html__( 'Items per row (iPhone 5)', 'woocommerce-tm-extra-product-options' ),
				"desc"             => esc_html__( 'Use this field to make a grid display. Enter how many items per row for the grid or leave blank for normal display.', 'woocommerce-tm-extra-product-options' ),
			),
			//@media only screen and (min-device-width: 375px) and (max-device-width: 667px) and (-webkit-min-device-pixel-ratio: 2) {
			array(
				"id"               => "variations_items_per_row_iphone6",
				"message0x0_class" => "builder_responsive_div",
				"wpmldisable"      => 1,
				"default"          => "",
				"type"             => "number",
				"tags"             => array(
					"class" => "n",
					"id"    => "builder_%id%",
					"name"  => "tm_meta[tmfbuilder][variations_options][%attribute_id%][%id%]",
				),
				"label"            => esc_html__( 'Items per row (iPhone 6)', 'woocommerce-tm-extra-product-options' ),
				"desc"             => esc_html__( 'Use this field to make a grid display. Enter how many items per row for the grid or leave blank for normal display.', 'woocommerce-tm-extra-product-options' ),
			),
			//@media only screen and (min-device-width: 414px) and (max-device-width: 736px) and (-webkit-min-device-pixel-ratio: 2) {
			array(
				"id"               => "variations_items_per_row_iphone6_plus",
				"message0x0_class" => "builder_responsive_div",
				"wpmldisable"      => 1,
				"default"          => "",
				"type"             => "number",
				"tags"             => array(
					"class" => "n",
					"id"    => "builder_%id%",
					"name"  => "tm_meta[tmfbuilder][variations_options][%attribute_id%][%id%]",
				),
				"label"            => esc_html__( 'Items per row (iPhone 6 +)', 'woocommerce-tm-extra-product-options' ),
				"desc"             => esc_html__( 'Use this field to make a grid display. Enter how many items per row for the grid or leave blank for normal display.', 'woocommerce-tm-extra-product-options' ),
			),
			//@media only screen and (device-width: 320px) and (device-height: 640px) and (-webkit-min-device-pixel-ratio: 2) {
			array(
				"id"               => "variations_items_per_row_samsung_galaxy",
				"message0x0_class" => "builder_responsive_div",
				"wpmldisable"      => 1,
				"default"          => "",
				"type"             => "number",
				"tags"             => array(
					"class" => "n",
					"id"    => "builder_%id%",
					"name"  => "tm_meta[tmfbuilder][variations_options][%attribute_id%][%id%]",
				),
				"label"            => esc_html__( 'Items per row (Samnsung Galaxy)', 'woocommerce-tm-extra-product-options' ),
				"desc"             => esc_html__( 'Use this field to make a grid display. Enter how many items per row for the grid or leave blank for normal display.', 'woocommerce-tm-extra-product-options' ),
			),
			//@media only screen and (min-device-width : 800px) and (max-device-width : 1280px) {
			array(
				"id"               => "variations_items_per_row_tablets_galaxy",
				"message0x0_class" => "builder_responsive_div",
				"wpmldisable"      => 1,
				"default"          => "",
				"type"             => "number",
				"tags"             => array(
					"class" => "n",
					"id"    => "builder_%id%",
					"name"  => "tm_meta[tmfbuilder][variations_options][%attribute_id%][%id%]",
				),
				"label"            => esc_html__( 'Items per row (Galaxy Tablets landscape)', 'woocommerce-tm-extra-product-options' ),
				"desc"             => esc_html__( 'Use this field to make a grid display. Enter how many items per row for the grid or leave blank for normal display.', 'woocommerce-tm-extra-product-options' ),
			),
			array(
				"id"               => "variations_item_width",
				"message0x0_class" => "tma-show-for-swatches tma-hide-for-select-box",
				"default"          => "",
				"type"             => "text",
				"tags"             => array(
					"class" => "t",
					"id"    => "builder_%id%",
					"name"  => "tm_meta[tmfbuilder][variations_options][%attribute_id%][%id%]",
					"value" => "",
				),
				"label"            => esc_html__( 'Width', 'woocommerce-tm-extra-product-options' ),
				"desc"             => esc_html__( 'Enter the width of the displayed item or leave blank for auto width.', 'woocommerce-tm-extra-product-options' ),
			),
			array(
				"id"               => "variations_item_height",
				"message0x0_class" => "tma-show-for-swatches tma-hide-for-select-box",
				"default"          => "",
				"type"             => "text",
				"tags"             => array(
					"class" => "t",
					"id"    => "builder_%id%",
					"name"  => "tm_meta[tmfbuilder][variations_options][%attribute_id%][%id%]",
					"value" => "",
				),
				"label"            => esc_html__( 'Height', 'woocommerce-tm-extra-product-options' ),
				"desc"             => esc_html__( 'Enter the height of the displayed item or leave blank for auto height.', 'woocommerce-tm-extra-product-options' ),
			),
			array(
				"id"      => "variations_changes_product_image",
				"default" => "",
				"type"    => "select",
				"tags"    => array(
					"class" => "tm-changes-product-image",
					"id"    => "builder_%id%",
					"name"  => "tm_meta[tmfbuilder][variations_options][%attribute_id%][%id%]",
				),
				"options" => array(
					array( "text" => esc_html__( 'No', 'woocommerce-tm-extra-product-options' ), "value" => "" ),
					array(
						"text"  => esc_html__( 'Use the image replacements', 'woocommerce-tm-extra-product-options' ),
						"value" => "images",
					),
					array(
						"text"  => esc_html__( 'Use custom image', 'woocommerce-tm-extra-product-options' ),
						"value" => "custom",
					),
				),
				"label"   => esc_html__( 'Changes product image', 'woocommerce-tm-extra-product-options' ),
				"desc"    => esc_html__( 'Choose whether to change the product image.', 'woocommerce-tm-extra-product-options' ),
			),
			array(
				"id"               => "variations_show_name",
				"message0x0_class" => "tma-show-for-swatches",
				"default"          => "hide",
				"type"             => "select",
				"tags"             => array(
					"class" => "variations-show-name",
					"id"    => "builder_%id%",
					"name"  => "tm_meta[tmfbuilder][variations_options][%attribute_id%][%id%]",
				),
				"options"          => array(
					array( "text" => esc_html__( 'Hide', 'woocommerce-tm-extra-product-options' ), "value" => "hide" ),
					array(
						"text"  => esc_html__( 'Show bottom', 'woocommerce-tm-extra-product-options' ),
						"value" => "bottom",
					),
					array(
						"text"  => esc_html__( 'Show inside', 'woocommerce-tm-extra-product-options' ),
						"value" => "inside",
					),
					array(
						"text"  => esc_html__( 'Tooltip', 'woocommerce-tm-extra-product-options' ),
						"value" => "tooltip",
					),
				),
				"label"            => esc_html__( 'Show attribute name', 'woocommerce-tm-extra-product-options' ),
				"desc"             => esc_html__( 'Choose whether to show or hide the attribute name.', 'woocommerce-tm-extra-product-options' ),
			),
		);

		$settings_term = array(
			array(
				"id"               => "variations_color",
				"message0x0_class" => "tma-term-color",
				"default"          => "",
				"type"             => "text",
				"tags"             => array(
					"class" => "tm-color-picker",
					"id"    => "builder_%id%",
					"name"  => "tm_meta[tmfbuilder][variations_options][%attribute_id%][[%id%]][%term_id%]",
					"value" => "",
				),
				"label"            => esc_html__( 'Color', 'woocommerce-tm-extra-product-options' ),
				"desc"             => esc_html__( 'Select the color to use.', 'woocommerce-tm-extra-product-options' ),
			),
			array(
				"id"               => "variations_image",
				"message0x0_class" => "tma-term-image",
				"default"          => "",
				"type"             => "hidden",
				"tags"             => array(
					"class" => "n tm_option_image" . $class,
					"id"    => "builder_%id%",
					"name"  => "tm_meta[tmfbuilder][variations_options][%attribute_id%][[%id%]][%term_id%]",
				),
				"label"            => esc_html__( 'Image replacement', 'woocommerce-tm-extra-product-options' ),
				"desc"             => esc_html__( 'Select an image for this term.', 'woocommerce-tm-extra-product-options' ),
				"extra"            => array( array( $this, "settings_term_variations_image_helper" ), array() ),
				"method"           => "settings_term_variations_image_helper",
			),
			array(
				"id"               => "variations_imagep",
				"message0x0_class" => "tma-term-custom-image",
				"default"          => "",
				"type"             => "hidden",
				"tags"             => array(
					"class" => "n tm_option_image tm_option_imagep" . $class,
					"id"    => "builder_%id%",
					"name"  => "tm_meta[tmfbuilder][variations_options][%attribute_id%][[%id%]][%term_id%]",
				),
				"label"            => esc_html__( 'Product Image replacement', 'woocommerce-tm-extra-product-options' ),
				"desc"             => esc_html__( 'Select the image to replace the product image with.', 'woocommerce-tm-extra-product-options' ),
				"extra"            => array( array( $this, "settings_term_variations_imagep_helper" ), array() ),
				"method"           => "settings_term_variations_imagep_helper",
			),

		);

		$attributes = array();

		if ( ! empty( $product_id ) ) {
			$product = wc_get_product( $product_id );

			if ( $product && is_object( $product ) && is_callable( array( $product, 'get_variation_attributes' ) ) ) {
				$attributes     = $product->get_variation_attributes();
				$all_attributes = $product->get_attributes();
				if ( $attributes ) {
					foreach ( $attributes as $key => $value ) {
						if ( ! $value ) {
							$attributes[ $key ] = array_map( 'trim', explode( "|", $all_attributes[ $key ]['value'] ) );
						}
					}
				}
			}

		}

		if ( empty( $return_js ) ) {
			if ( empty( $attributes ) ) {
				echo '<div class="errortitle"><p><i class="tcfa tcfa-exclamation-triangle"></i> ' . esc_html__( 'No saved variations found.', 'woocommerce-tm-extra-product-options' ) . '</p></div>';
			}
		}
		$d_counter = 0;
		foreach ( $attributes as $name => $options ) {
			$js_object[ $d_counter ] = array();
			if ( empty( $return_js ) ) {
				echo '<div class="tma-handle-wrap tm-attribute">'
				     . '<div class="tma-handle"><div class="tma-attribute_label">'
				     . esc_html( wc_attribute_label( $name ) )
				     . '</div><div class="tmicon tcfa fold tcfa-caret-up"></div></div>'
				     . '<div class="tma-handle-wrapper tm-hidden">'
				     . '<div class="tma-attribute w100">';
			}

			$attribute_id = sanitize_title( $name );
			foreach ( $settings_attribute as $setting ) {
				$setting["tags"]["id"]   = str_replace( "%id%", $setting["id"], $setting["tags"]["id"] );
				$setting["tags"]["name"] = str_replace( "%id%", $setting["id"], $setting["tags"]["name"] );
				$setting["tags"]["name"] = str_replace( "%attribute_id%", $attribute_id, $setting["tags"]["name"] );
				if ( ! empty( $meta ) && isset( $meta[ $attribute_id ] ) && isset( $meta[ $attribute_id ][ $setting["id"] ] ) ) {
					$setting["default"] = $meta[ $attribute_id ][ $setting["id"] ];
				}
				if ( empty( $return_js ) ) {
					THEMECOMPLETE_EPO_HTML()->tm_make_field( $setting, 1 );
				} else {
					$js_object[ $d_counter ][] = $this->remove_for_js( $setting );
				}
			}

			if ( is_array( $options ) ) {
				$taxonomy_name = rawurldecode( sanitize_title( $name ) );
				if ( taxonomy_exists( $taxonomy_name ) ) {

					if ( function_exists( 'wc_get_product_terms' ) ) {
						$terms = wc_get_product_terms( $product_id, $name, array( 'fields' => 'all' ) );
					} else {

						$orderby = themecomplete_attribute_orderby( $taxonomy_name );
						$args    = array();
						switch ( $orderby ) {
							case 'name' :
								$args = array( 'orderby' => 'name', 'hide_empty' => false, 'menu_order' => false );
								break;
							case 'id' :
								$args = array(
									'orderby'    => 'id',
									'order'      => 'ASC',
									'menu_order' => false,
									'hide_empty' => false,
								);
								break;
							case 'menu_order' :
								$args = array( 'menu_order' => 'ASC', 'hide_empty' => false );
								break;
						}
						$terms = get_terms( $taxonomy_name, $args );
					}
					if ( ! empty( $terms ) ) {

						foreach ( $terms as $term ) {
							// Get only selected terms
							if ( ! $has_term = has_term( (int) $term->term_id, $taxonomy_name, $product_id ) ) {
								continue;
							}
							$term_name = THEMECOMPLETE_EPO_HELPER()->html_entity_decode( $term->name );
							$term_id   = THEMECOMPLETE_EPO_HELPER()->sanitize_key( $term->slug );

							if ( empty( $return_js ) ) {
								echo '<div class="tma-handle-wrap tm-term">'
								     . '<div class="tma-handle"><div class="tma-attribute_label">'
								     . esc_html( apply_filters( 'woocommerce_variation_option_name', $term_name ) )
								     . '</div><div class="tmicon tcfa fold tcfa-caret-up"></div></div>'
								     . '<div class="tma-handle-wrapper tm-hidden">'
								     . '<div class="tma-attribute w100">';
							}

							foreach ( $settings_term as $setting ) {
								$setting["tags"]["id"]   = str_replace( "%id%", $setting["id"], $setting["tags"]["id"] );
								$setting["tags"]["name"] = str_replace( "%id%", $setting["id"], $setting["tags"]["name"] );
								$setting["tags"]["name"] = str_replace( "%attribute_id%", sanitize_title( THEMECOMPLETE_EPO_HELPER()->sanitize_key( $name ) ), $setting["tags"]["name"] );
								$setting["tags"]["name"] = str_replace( "%term_id%", esc_attr( $term_id ), $setting["tags"]["name"] );

								if ( ! empty( $meta )
								     && isset( $meta[ $attribute_id ] )
								     && isset( $meta[ $attribute_id ][ $setting["id"] ] )
								     && isset( $meta[ $attribute_id ][ $setting["id"] ][ $term_id ] )
								) {
									$setting["default"] = $meta[ $attribute_id ][ $setting["id"] ][ $term_id ];
									if ( isset( $setting["extra"] ) && isset( $setting["method"] ) ) {
										$setting["extra"] = array(
											array( $this, $setting["method"] ),
											array( $meta[ $attribute_id ][ $setting["id"] ][ $term_id ] ),
										);
									}
								}
								if ( empty( $return_js ) ) {
									THEMECOMPLETE_EPO_HTML()->tm_make_field( $setting, 1 );
								} else {
									$js_object[ $d_counter ][] = $this->remove_for_js( $setting );
								}
							}
							if ( empty( $return_js ) ) {
								echo '</div></div></div>';
							}
						}
					}

				} else {

					foreach ( $options as $option ) {
						$optiont = rawurldecode( THEMECOMPLETE_EPO_HELPER()->html_entity_decode( $option ) );
						$option  = THEMECOMPLETE_EPO_HELPER()->html_entity_decode( THEMECOMPLETE_EPO_HELPER()->sanitize_key( $option ) );
						if ( empty( $return_js ) ) {
							echo '<div class="tma-handle-wrap tm-term">'
							     . '<div class="tma-handle"><div class="tma-attribute_label">'
							     . esc_html( apply_filters( 'woocommerce_variation_option_name', $optiont ) )
							     . '</div><div class="tmicon tcfa fold tcfa-caret-up"></div></div>'
							     . '<div class="tma-handle-wrapper tm-hidden">'
							     . '<div class="tma-attribute w100">';
						}

						foreach ( $settings_term as $setting ) {
							$setting["tags"]["id"]   = str_replace( "%id%", $setting["id"], $setting["tags"]["id"] );
							$setting["tags"]["name"] = str_replace( "%id%", $setting["id"], $setting["tags"]["name"] );
							$setting["tags"]["name"] = str_replace( "%attribute_id%", sanitize_title( THEMECOMPLETE_EPO_HELPER()->sanitize_key( $name ) ), $setting["tags"]["name"] );
							$setting["tags"]["name"] = str_replace( "%term_id%", esc_attr( $option ), $setting["tags"]["name"] );

							if ( ! empty( $meta )
							     && isset( $meta[ $attribute_id ] )
							     && isset( $meta[ $attribute_id ][ $setting["id"] ] )
							     && isset( $meta[ $attribute_id ][ $setting["id"] ][ $option ] )
							) {
								$setting["default"] = $meta[ $attribute_id ][ $setting["id"] ][ $option ];
								if ( isset( $setting["extra"] ) && isset( $setting["method"] ) ) {
									$setting["extra"] = array(
										array( $this, $setting["method"] ),
										array( $meta[ $attribute_id ][ $setting["id"] ][ $option ] ),
									);

								}
							}
							if ( empty( $return_js ) ) {
								THEMECOMPLETE_EPO_HTML()->tm_make_field( $setting, 1 );
							} else {
								$js_object[ $d_counter ][] = $this->remove_for_js( $setting );
							}

						}
						if ( empty( $return_js ) ) {
							echo '</div></div></div>';
						}
					}

				}
			}
			if ( empty( $return_js ) ) {
				echo '</div></div></div>';
			}
			$d_counter ++;
		}

		if ( ! empty( $return_js ) ) {
			return $js_object;
		}

	}

	/**
	 * Helper to print the upload button for Image replacement
	 *
	 * @see    builder_sub_options_image_helper
	 * @since  4.8.5
	 * @access public
	 */
	public function get_builder_sub_options_upload_helper( $name = "" ) {

		if ( $name == "multiple_radiobuttons_options" || $name == "multiple_checkboxes_options" ) {
			if ( $name == "multiple_radiobuttons_options" ) {
				echo '&nbsp;<span data-tm-tooltip-html="' . esc_attr( esc_html__( "Choose the image to use in place of the radio button.", 'woocommerce-tm-extra-product-options' ) ) . '" class="tm_upload_button cp_button tm-tooltip"><i class="tcfa tcfa-upload"></i></span>';
			} elseif ( $name == "multiple_checkboxes_options" ) {
				echo '&nbsp;<span data-tm-tooltip-html="' . esc_attr( esc_html__( "Choose the image to use in place of the checkbox.", 'woocommerce-tm-extra-product-options' ) ) . '" class="tm_upload_button cp_button tm-tooltip"><i class="tcfa tcfa-upload"></i></span>';
			}
		}

	}

	/**
	 * Helper to generate html for Image replacement
	 *
	 * @see    builder_sub_options
	 * @since  4.8.5
	 * @access public
	 */
	public function builder_sub_options_image_helper( $name = "", $image = false ) {

		$this->get_builder_sub_options_upload_helper( $name );

		if ( $image !== false ) {
			if ( $image === "" ) {
				$image = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=";
			}
			echo '<span class="s tm_upload_image"><img class="tm_upload_image_img" alt="&nbsp;" src="' . esc_attr( $image ) . '" />' . '<button rel="tc-option-image" class="tc-button small builder-image-delete" type="button"><i class="tcfa tcfa-times"></i></button>' . '</span>';
		}

	}

	/**
	 * Helper to print the upload button for checked Image replacement
	 *
	 * @see    builder_sub_options_imagec_helper
	 * @since  4.8.5
	 * @access public
	 */
	public function get_builder_sub_options_uploadc_helper( $name = "" ) {

		if ( $name == "multiple_radiobuttons_options" || $name == "multiple_checkboxes_options" ) {
			if ( $name == "multiple_radiobuttons_options" ) {
				echo '&nbsp;<span data-tm-tooltip-html="' . esc_attr( esc_html__( "Choose the image to use in place of the radio button when it is checked.", 'woocommerce-tm-extra-product-options' ) ) . '" class="tm_upload_button cp_button tm-tooltip"><i class="tcfa tcfa-upload"></i></span>';
			} elseif ( $name == "multiple_checkboxes_options" ) {
				echo '&nbsp;<span data-tm-tooltip-html="' . esc_attr( esc_html__( "Choose the image to use in place of the checkbox when it is checked.", 'woocommerce-tm-extra-product-options' ) ) . '" class="tm_upload_button cp_button tm-tooltip"><i class="tcfa tcfa-upload"></i></span>';
			}
		}

	}

	/**
	 * Helper to generate html for checked Image replacement
	 *
	 * @see    builder_sub_options
	 * @since  4.8.5
	 * @access public
	 */
	public function builder_sub_options_imagec_helper( $name = "", $image = false ) {

		$this->get_builder_sub_options_uploadc_helper( $name );

		if ( $image !== false ) {
			if ( $image === "" ) {
				$image = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=";
			}
			echo '<span class="tm_upload_image tm_upload_imagec"><img class="tm_upload_image_img" alt="&nbsp;" src="' . esc_attr( $image ) . '" />' . '<button rel="tc-option-imagec" class="tc-button small builder-image-delete" type="button"><i class="tcfa tcfa-times"></i></button>' . '</span>';
		}

	}

	/**
	 * Helper to print the upload button for Product Image replacement
	 *
	 * @see    builder_sub_options_imagep_helper
	 * @since  4.8.5
	 * @access public
	 */
	public function get_builder_sub_options_uploadp_helper( $name = "" ) {

		if ( $name == "multiple_radiobuttons_options" || $name == "multiple_checkboxes_options" ) {
			echo '&nbsp;<span data-tm-tooltip-html="' . esc_attr( esc_html__( "Choose the image to replace the product image with.", 'woocommerce-tm-extra-product-options' ) ) . '" class="tm_upload_button tm_upload_buttonp cp_button tm-tooltip"><i class="tcfa tcfa-upload"></i></span>';
		} elseif ( $name == "multiple_selectbox_options" ) {
			echo '&nbsp;<span data-tm-tooltip-html="' . esc_attr( esc_html__( "Choose the image to replace the product image with.", 'woocommerce-tm-extra-product-options' ) ) . '" class="tm_upload_button tm_upload_buttonp cp_button tm-tooltip"><i class="tcfa tcfa-upload"></i></span>';
		}

	}

	/**
	 * Helper to generate html for Product Image replacement
	 *
	 * @see    builder_sub_options
	 * @since  4.8.5
	 * @access public
	 */
	public function builder_sub_options_imagep_helper( $name = "", $image = false ) {

		$this->get_builder_sub_options_uploadp_helper( $name );

		if ( $image !== false ) {
			if ( $image === "" ) {
				$image = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=";
			}
			echo '<span class="tm_upload_image tm_upload_imagep"><img class="tm_upload_image_img" alt="&nbsp;" src="' . esc_attr( $image ) . '" />' . '<button rel="tc-option-imagep" class="tc-button small builder-image-delete" type="button"><i class="tcfa tcfa-times"></i></button>' . '</span>';
		}

	}

	/**
	 * Helper to print the upload button for Lightbox Image
	 *
	 * @see    builder_sub_options_imagel_helper
	 * @since  4.8.5
	 * @access public
	 */
	public function get_builder_sub_options_uploadl_helper( $name = "" ) {

		if ( $name == "multiple_radiobuttons_options" || $name == "multiple_checkboxes_options" ) {
			echo '&nbsp;<span data-tm-tooltip-html="' . esc_attr( esc_html__( "Choose the image for the lightbox.", 'woocommerce-tm-extra-product-options' ) ) . '" class="tm_upload_button tm_upload_buttonl cp_button tm-tooltip"><i class="tcfa tcfa-upload"></i></span>';
		} elseif ( $name == "multiple_selectbox_options" ) {
			echo '&nbsp;<span data-tm-tooltip-html="' . esc_attr( esc_html__( "Choose the image for the lightbox.", 'woocommerce-tm-extra-product-options' ) ) . '" class="tm_upload_button tm_upload_buttonl cp_button tm-tooltip"><i class="tcfa tcfa-upload"></i></span>';
		}

	}

	/**
	 * Helper to generate html for Lightbox Image
	 *
	 * @see    builder_sub_options
	 * @since  4.8.5
	 * @access public
	 */
	public function builder_sub_options_imagel_helper( $name = "", $image = false ) {

		$this->get_builder_sub_options_uploadl_helper( $name );

		if ( $image !== false ) {
			if ( $image === "" ) {
				$image = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=";
			}
			echo '<span class="tm_upload_image tm_upload_imagel"><img class="tm_upload_image_img" alt="&nbsp;" src="' . esc_attr( $image ) . '" />' . '<button rel="tc-option-imagel" class="tc-button small builder-image-delete" type="button"><i class="tcfa tcfa-times"></i></button>' . '</span>';
		}

	}

	/**
	 * Remove problematic array keys for js output
	 *
	 * @since  4.9.12
	 * @access public
	 */
	public function remove_for_js( $array = array() ) {

		unset( $array['extra'] );

		if ( isset( $array['type'] ) && $array['type'] === 'checkbox' ) {
			$array['checked'] = checked( ( $array["default"] == $array["tags"]["value"] ), true, false ) !== '';
		}

		return $array;

	}

	/**
	 * Generates element sub-options for selectbox, checkbox and radio buttons.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function builder_sub_options( $args = array() ) {

		$args = shortcode_atts( array(
			'options'       => array(),
			'name'          => 'multiple_selectbox_options',
			'counter'       => null,
			'default_value' => null,
			'return_js'     => false,
		), $args );

		$options       = $args['options'];
		$name          = $args['name'];
		$counter       = $args['counter'];
		$default_value = $args['default_value'];
		$return_js     = $args['return_js'];

		$js_object = array();

		$o                     = array();
		$upload                = "";
		$uploadc               = "";
		$uploadp               = "";
		$uploadl               = "";
		$class                 = "";
		$_extra_options        = $this->extra_multiple_options;
		$additional_currencies = THEMECOMPLETE_EPO_HELPER()->get_additional_currencies();

		if ( ! $options ) {
			$options = array(
				"title"       => array( false ),
				"value"       => array(),
				"price"       => array(),
				"sale_price"  => array(),
				"image"       => array(),
				"imagec"      => array(),
				"imagep"      => array(),
				"imagel"      => array(),
				"price_type"  => array(),
				"url"         => array(),
				"description" => array(),
				"enabled"     => array(),
				"color"       => array(),
				"fee"         => array(),
			);
			foreach ( $_extra_options as $__key => $__name ) {
				if ( "multiple_" . $__name["type"] . "_options" == $name ) {
					$options[ $__name["name"] ] = array( "" );
				}
			}
		}

		if ( $name == "multiple_radiobuttons_options" || $name == "multiple_checkboxes_options" ) {
			if ( $name == "multiple_radiobuttons_options" ) {
				$upload  = '&nbsp;<span data-tm-tooltip-html="' . esc_attr( esc_html__( "Choose the image to use in place of the radio button.", 'woocommerce-tm-extra-product-options' ) ) . '" class="tm_upload_button cp_button tm-tooltip"><i class="tcfa tcfa-upload"></i></span>';
				$uploadc = '&nbsp;<span data-tm-tooltip-html="' . esc_attr( esc_html__( "Choose the image to use in place of the radio button when it is checked.", 'woocommerce-tm-extra-product-options' ) ) . '" class="tm_upload_button cp_button tm-tooltip"><i class="tcfa tcfa-upload"></i></span>';
			} elseif ( $name == "multiple_checkboxes_options" ) {
				$upload  = '&nbsp;<span data-tm-tooltip-html="' . esc_attr( esc_html__( "Choose the image to use in place of the checkbox.", 'woocommerce-tm-extra-product-options' ) ) . '" class="tm_upload_button cp_button tm-tooltip"><i class="tcfa tcfa-upload"></i></span>';
				$uploadc = '&nbsp;<span data-tm-tooltip-html="' . esc_attr( esc_html__( "Choose the image to use in place of the checkbox when it is checked.", 'woocommerce-tm-extra-product-options' ) ) . '" class="tm_upload_button cp_button tm-tooltip"><i class="tcfa tcfa-upload"></i></span>';
			}
			$uploadp = '&nbsp;<span data-tm-tooltip-html="' . esc_attr( esc_html__( "Choose the image to replace the product image with.", 'woocommerce-tm-extra-product-options' ) ) . '" class="tm_upload_button tm_upload_buttonp cp_button tm-tooltip"><i class="tcfa tcfa-upload"></i></span>';
			$uploadl = '&nbsp;<span data-tm-tooltip-html="' . esc_attr( esc_html__( "Choose the image for the lightbox.", 'woocommerce-tm-extra-product-options' ) ) . '" class="tm_upload_button tm_upload_buttonl cp_button tm-tooltip"><i class="tcfa tcfa-upload"></i></span>';
			$class   = " withupload";
		}
		if ( $name == "multiple_selectbox_options" ) {
			$uploadp = '&nbsp;<span data-tm-tooltip-html="' . esc_attr( esc_html__( "Choose the image to replace the product image with.", 'woocommerce-tm-extra-product-options' ) ) . '" class="tm_upload_button tm_upload_buttonp cp_button tm-tooltip"><i class="tcfa tcfa-upload"></i></span>';
			$uploadl = '&nbsp;<span data-tm-tooltip-html="' . esc_attr( esc_html__( "Choose the image for the lightbox.", 'woocommerce-tm-extra-product-options' ) ) . '" class="tm_upload_button tm_upload_buttonl cp_button tm-tooltip"><i class="tcfa tcfa-upload"></i></span>';
			$class   = " withupload";
		}

		$o["title"]       = array(
			"id"      => $name . "_title",
			"default" => "",
			"type"    => "text",
			"nodiv"   => 1,
			"tags"    => array(
				"class" => "t tm_option_title",
				"id"    => $name . "_title",
				"name"  => $name . "_title",
				"value" => "",
			),
		);
		$o["value"]       = array(
			"id"      => $name . "_value",
			"default" => "",
			"type"    => "text",
			"nodiv"   => 1,
			"tags"    => array( "class" => "t tm_option_value", "id" => $name . "_value", "name" => $name . "_value" ),
		);
		$o["price"]       = array(
			"id"      => $name . "_price",
			"default" => "",
			"type"    => "text",
			"nodiv"   => 1,
			"tags"    => array( "class" => "t tm_option_price", "id" => $name . "_price", "name" => $name . "_price" ),
		);
		$o["sale_price"]  = array(
			"id"      => $name . "_sale_price",
			"default" => "",
			"type"    => "text",
			"nodiv"   => 1,
			"tags"    => array(
				"class" => "t tm_option_sale_price",
				"id"    => $name . "_price",
				"name"  => $name . "_price",
			),
		);
		$o["image"]       = array(
			"id"      => $name . "_image",
			"default" => "",
			"type"    => "hidden",
			"nodiv"   => 1,
			"tags"    => array(
				"class" => "t tm_option_image tc-option-image" . $class,
				"id"    => $name . "_image",
				"name"  => $name . "_image",
			),
			"extra"   => array( array( $this, "builder_sub_options_image_helper" ), array( $name ) ),
			"method"  => "builder_sub_options_image_helper",
		);
		$o["imagec"]      = array(
			"id"      => $name . "_imagec",
			"default" => "",
			"type"    => "hidden",
			"nodiv"   => 1,
			"tags"    => array(
				"class" => "t tm_option_image tm_option_imagec tc-option-imagec" . $class,
				"id"    => $name . "_imagec",
				"name"  => $name . "_imagec",
			),
		);
		$o["imagep"]      = array(
			"id"      => $name . "_imagep",
			"default" => "",
			"type"    => "hidden",
			"nodiv"   => 1,
			"tags"    => array(
				"class" => "t tm_option_image tm_option_imagep tc-option-imagep" . $class,
				"id"    => $name . "_imagep",
				"name"  => $name . "_imagep",
			),
		);
		$o["imagel"]      = array(
			"id"      => $name . "_imagel",
			"default" => "",
			"type"    => "hidden",
			"nodiv"   => 1,
			"tags"    => array(
				"class" => "t tm_option_image tm_option_imagel tc-option-imagel" . $class,
				"id"    => $name . "_imagel",
				"name"  => $name . "_imagel",
			),
		);
		$o["price_type"]  = array(
			"id"      => $name . "_price_type",
			"default" => "",
			"type"    => "select",
			"options" => array(
				array( "text" => esc_html__( "Fixed amount", 'woocommerce-tm-extra-product-options' ), "value" => "" ),
				array(
					"text"  => esc_html__( "Percent of the original price", 'woocommerce-tm-extra-product-options' ),
					"value" => "percent",
				),
				array(
					"text"  => esc_html__( "Percent of the original price + options", 'woocommerce-tm-extra-product-options' ),
					"value" => "percentcurrenttotal",
				),
				array(
					"text"  => esc_html__( "Math formula", 'woocommerce-tm-extra-product-options' ),
					"value" => "math",
				),
				array(
					"text"  => esc_html__( 'Fixed amount + options', 'woocommerce-tm-extra-product-options' ),
					"value" => "fixedcurrenttotal",
				),
			),
			"nodiv"   => 1,
			"tags"    => array(
				"class" => "t tm_option_price_type " . $name,
				"id"    => $name . "_price_type",
				"name"  => $name . "_price_type",
			),
		);
		$o["url"]         = array(
			"id"      => $name . "_url",
			"default" => "",
			"type"    => "text",
			"nodiv"   => 1,
			"tags"    => array(
				"class" => "t tm_option_url",
				"id"    => $name . "_url",
				"name"  => $name . "_url",
				"value" => "",
			),
		);
		$o["description"] = array(
			"id"      => $name . "_description",
			"default" => "",
			"type"    => "text",
			"nodiv"   => 1,
			"tags"    => array(
				"class" => "t tm_option_description",
				"id"    => $name . "_description",
				"name"  => $name . "_description",
				"value" => "",
			),
		);
		$o["enabled"]     = array(
			"id"      => $name . "_enabled",
			"default" => "1",
			"type"    => "checkbox",
			"nodiv"   => 1,
			"tags"    => array(
				"class" => "c tm_option_enabled " . $name,
				"id"    => $name . "_enabled",
				"name"  => $name . "_enabled",
				"value" => "1",
			),
		);
		$o["color"]       = array(
			"id"      => $name . "_color",
			"default" => "",
			"type"    => "text",
			"nodiv"   => 1,
			"tags"    => array(
				"class" => "tm-color-picker",
				"id"    => $name . "_color",
				"name"  => $name . "_color",
				"value" => "",
			),
		);
		$o["fee"]         = array(
			"id"      => $name . "_fee",
			"default" => "",
			"type"    => "checkbox",
			"nodiv"   => 1,
			"tags"    => array( "class" => "c", "id" => $name . "_fee", "name" => $name . "_fee", "value" => "1" ),
		);

		foreach ( $_extra_options as $__key => $__name ) {
			$_extra_name = $__name["name"];
			if ( "multiple_" . $__name["type"] . "_options" == $name ) {
				$o[ $_extra_name ]          = $__name["field"];
				$o[ $_extra_name ]["id"]    = $name . "_" . $_extra_name;
				$o[ $_extra_name ]["nodiv"] = 1;
				$o[ $_extra_name ]["tags"]  = array_merge(
					$__name["field"]["tags"],
					array( "id" => $name . "_" . $_extra_name, "name" => $name . "_" . $_extra_name )
				);
			}
		}

		$o          = apply_filters( 'wc_epo_builder_after_multiple_element_array', $o, $name );
		$original_o = $o;

		if ( empty( $return_js ) ) {
			echo "<div class='tc-row nopadding multiple_options tc-clearfix'>"
			     . "<div class='tc-cell tc-col-auto tm_cell_move'>";

			THEMECOMPLETE_EPO_HTML()->tm_make_button( array(
				"text" => "",
				"tags" => array( "href" => "#move", "class" => "tmicon tcfa tcfa-grip-vertical tm-hidden-inline" ),
			), 1 );
			THEMECOMPLETE_EPO_HTML()->tm_make_button( array(
				"text" => "",
				"icon" => "angle-up",
				"tags" => array( "href" => "#move", "class" => "tc tc-button small tm-hidden-inline" ),
			), 1 );
			THEMECOMPLETE_EPO_HTML()->tm_make_button( array(
				"text" => "",
				"icon" => "angle-down",
				"tags" => array( "href" => "#move", "class" => "tc tc-button small tm-hidden-inline" ),
			), 1 );

			echo "</div>"
			     . "<div class='tc-cell tc-col-auto tm_cell_default'>" . ( ( $name == "multiple_checkboxes_options" ) ? esc_html__( "Checked", 'woocommerce-tm-extra-product-options' ) : esc_html__( "Default", 'woocommerce-tm-extra-product-options' ) ) . "</div>"
			     . "<div class='tc-cell tc-col-3 tm_cell_title'>" . esc_html__( "Label", 'woocommerce-tm-extra-product-options' ) . "</div>"
			     . "<div class='tc-cell tc-col-3 tm_cell_images'>" . esc_html__( "Images", 'woocommerce-tm-extra-product-options' ) . "</div>"

			     . "<div class='tc-cell tc-col-0 tm_cell_value'>" . esc_html__( "Value", 'woocommerce-tm-extra-product-options' ) . "</div>"
			     . "<div class='tc-cell tc-col-auto tm_cell_price'>" . esc_html__( "Price", 'woocommerce-tm-extra-product-options' ) . "</div>"
			     . "<div class='tc-cell tc-col-auto tm_cell_delete'><button type='button' class='tc tc-button builder_panel_delete_all'>" . esc_html__( "Delete all options", 'woocommerce-tm-extra-product-options' ) . "</button></div>"
			     . "</div>";
		}
		$total_entries = count( $options["title"] );
		$per_page      = apply_filters( 'tm_choices_shown', 20 );
		if ( $per_page <= 0 ) {
			$per_page = 20;
		}
		if ( $total_entries > $per_page ) {
			$pages = ceil( $total_entries / $per_page );
			if ( empty( $return_js ) ) {
				echo '<div data-perpage="' . esc_attr( $per_page ) . '" data-totalpages="' . esc_attr( $pages ) . '" class="tcpagination tc-clearfix"></div>';
			}
		} else {
			if ( empty( $return_js ) ) {
				echo '<div data-perpage="' . esc_attr( $per_page ) . '" data-totalpages="0" class="tcpagination tc-clearfix"></div>';
			}
		}
		if ( empty( $return_js ) ) {
			echo "<div class='panels_wrap nof_wrapper'>";
		}

		$d_counter    = 0;
		$show_counter = 0;
		foreach ( $options["title"] as $ar => $el ) {
			$js_object[ $d_counter ] = array();
			$hidden_class            = '';
			if ( $show_counter >= $per_page ) {
				$hidden_class = ' tm-hidden ';
			}
			$show_counter ++;

			if ( $options["title"][ $ar ] === false ) {
				$options["title"][ $ar ] = $original_o["title"]["default"];
			}
			if ( ! isset( $options["value"][ $ar ] ) ) {
				$options["value"][ $ar ] = $original_o["value"]["default"];
			}
			if ( ! isset( $options["price"][ $ar ] ) ) {
				$options["price"][ $ar ] = $original_o["price"]["default"];
			}
			if ( ! isset( $options["sale_price"][ $ar ] ) ) {
				$options["sale_price"][ $ar ] = $original_o["sale_price"]["default"];
			}
			if ( ! isset( $options["image"][ $ar ] ) ) {
				$options["image"][ $ar ] = $original_o["image"]["default"];
			}
			if ( ! isset( $options["imagec"][ $ar ] ) ) {
				$options["imagec"][ $ar ] = $original_o["imagec"]["default"];
			}
			if ( ! isset( $options["imagep"][ $ar ] ) ) {
				$options["imagep"][ $ar ] = $original_o["imagep"]["default"];
			}
			if ( ! isset( $options["imagel"][ $ar ] ) ) {
				$options["imagel"][ $ar ] = $original_o["imagel"]["default"];
			}
			if ( ! isset( $options["price_type"][ $ar ] ) ) {
				$options["price_type"][ $ar ] = $original_o["price_type"]["default"];
			}
			if ( ! isset( $options["url"][ $ar ] ) ) {
				$options["url"][ $ar ] = $original_o["url"]["default"];
			}
			if ( ! isset( $options["description"][ $ar ] ) ) {
				$options["description"][ $ar ] = $original_o["description"]["default"];
			}

			// backwards compatibility
			if ( ! isset( $options["enabled"][ $ar ] ) || $options["enabled"][ $ar ] === false ) {
				$options["enabled"][ $ar ] = $original_o["enabled"]["default"];
			}
			if ( $options["enabled"][ $ar ] === "0" || $options["enabled"][ $ar ] === "" ) {
				$options["enabled"][ $ar ] = '';
			}

			if ( ! isset( $options["color"][ $ar ] ) ) {
				$options["color"][ $ar ] = $original_o["color"]["default"];
			}
			if ( ! isset( $options["fee"][ $ar ] ) ) {
				$options["fee"][ $ar ] = $original_o["fee"]["default"];
			}
			foreach ( $_extra_options as $__key => $__name ) {
				if ( "multiple_" . $__name["type"] . "_options" == $name ) {
					$_extra_name = $__name["name"];
					if ( ! isset( $options[ $_extra_name ][ $ar ] ) ) {
						if ( isset( $original_o[ $_extra_name ]["default"] ) ) {
							$options[ $_extra_name ][ $ar ] = $original_o[ $_extra_name ]["default"];
						} else {
							$options[ $_extra_name ][ $ar ] = '';
						}
					}
				}
			}

			$options = apply_filters( 'wc_epo_builder_element_array_in_loop_before', $options, $o, $ar, $name, $counter );

			$o["title"]["default"]      = $options["title"][ $ar ];//label
			$o["title"]["tags"]["name"] = "tm_meta[tmfbuilder][" . $name . "_title][" . ( is_null( $counter ) ? 0 : $counter ) . "][]";
			$o["title"]["tags"]["id"]   = str_replace( array( "[", "]" ), "", $o["title"]["tags"]["name"] ) . "_" . $ar;

			$o["value"]["default"]      = $options["value"][ $ar ];//value
			$o["value"]["tags"]["name"] = "tm_meta[tmfbuilder][" . $name . "_value][" . ( is_null( $counter ) ? 0 : $counter ) . "][]";
			$o["value"]["tags"]["id"]   = str_replace( array( "[", "]" ), "", $o["value"]["tags"]["name"] ) . "_" . $ar;

			$o["price"]["default"]      = themecomplete_convert_local_numbers( $options["price"][ $ar ] );//price
			$o["price"]["tags"]["name"] = "tm_meta[tmfbuilder][" . $name . "_price][" . ( is_null( $counter ) ? 0 : $counter ) . "][]";
			$o["price"]["tags"]["id"]   = str_replace( array( "[", "]" ), "", $o["price"]["tags"]["name"] ) . "_" . $ar;

			$o["sale_price"]["default"]      = themecomplete_convert_local_numbers( $options["sale_price"][ $ar ] );//sale_price
			$o["sale_price"]["tags"]["name"] = "tm_meta[tmfbuilder][" . $name . "_sale_price][" . ( is_null( $counter ) ? 0 : $counter ) . "][]";
			$o["sale_price"]["tags"]["id"]   = str_replace( array(
					"[",
					"]",
				), "", $o["sale_price"]["tags"]["name"] ) . "_" . $ar;

			$o["image"]["default"]      = $options["image"][ $ar ];//image
			$o["image"]["tags"]["name"] = "tm_meta[tmfbuilder][" . $name . "_image][" . ( is_null( $counter ) ? 0 : $counter ) . "][]";
			$o["image"]["tags"]["id"]   = str_replace( array( "[", "]" ), "", $o["image"]["tags"]["name"] ) . "_" . $ar;
			$o["image"]["extra"]        = array(
				array( $this, "builder_sub_options_image_helper" ),
				array( $name, $options["image"][ $ar ] ),
			);

			$o["imagec"]["default"]      = $options["imagec"][ $ar ];//imagec
			$o["imagec"]["tags"]["name"] = "tm_meta[tmfbuilder][" . $name . "_imagec][" . ( is_null( $counter ) ? 0 : $counter ) . "][]";
			$o["imagec"]["tags"]["id"]   = str_replace( array(
					"[",
					"]",
				), "", $o["imagec"]["tags"]["name"] ) . "_" . $ar;
			$o["imagec"]["extra"]        = array(
				array( $this, "builder_sub_options_imagec_helper" ),
				array( $name, $options["imagec"][ $ar ] ),
			);

			$o["imagep"]["default"]      = $options["imagep"][ $ar ];//imagep
			$o["imagep"]["tags"]["name"] = "tm_meta[tmfbuilder][" . $name . "_imagep][" . ( is_null( $counter ) ? 0 : $counter ) . "][]";
			$o["imagep"]["tags"]["id"]   = str_replace( array(
					"[",
					"]",
				), "", $o["imagep"]["tags"]["name"] ) . "_" . $ar;
			$o["imagep"]["extra"]        = array(
				array( $this, "builder_sub_options_imagep_helper" ),
				array( $name, $options["imagep"][ $ar ] ),
			);

			$o["imagel"]["default"]      = $options["imagel"][ $ar ];//imagel
			$o["imagel"]["tags"]["name"] = "tm_meta[tmfbuilder][" . $name . "_imagel][" . ( is_null( $counter ) ? 0 : $counter ) . "][]";
			$o["imagel"]["tags"]["id"]   = str_replace( array(
					"[",
					"]",
				), "", $o["imagel"]["tags"]["name"] ) . "_" . $ar;
			$o["imagel"]["extra"]        = array(
				array( $this, "builder_sub_options_imagel_helper" ),
				array( $name, $options["imagel"][ $ar ] ),
			);

			$o["price_type"]["default"]      = $options["price_type"][ $ar ];//price type
			$o["price_type"]["tags"]["name"] = "tm_meta[tmfbuilder][" . $name . "_price_type][" . ( is_null( $counter ) ? 0 : $counter ) . "][]";
			$o["price_type"]["tags"]["id"]   = str_replace( array(
					"[",
					"]",
				), "", $o["price_type"]["tags"]["name"] ) . "_" . $ar;

			$o["url"]["default"]      = $options["url"][ $ar ];//url
			$o["url"]["tags"]["name"] = "tm_meta[tmfbuilder][" . $name . "_url][" . ( is_null( $counter ) ? 0 : $counter ) . "][]";
			$o["url"]["tags"]["id"]   = str_replace( array( "[", "]" ), "", $o["url"]["tags"]["name"] ) . "_" . $ar;

			$o["description"]["default"]      = $options["description"][ $ar ];//description
			$o["description"]["tags"]["name"] = "tm_meta[tmfbuilder][" . $name . "_description][" . ( is_null( $counter ) ? 0 : $counter ) . "][]";
			$o["description"]["tags"]["id"]   = str_replace( array(
					"[",
					"]",
				), "", $o["description"]["tags"]["name"] ) . "_" . $ar;

			$o["enabled"]["default"]      = $options["enabled"][ $ar ];//enabled
			$o["enabled"]["tags"]["name"] = "tm_meta[tmfbuilder][" . $name . "_enabled][" . ( is_null( $counter ) ? 0 : $counter ) . "][]";
			$o["enabled"]["tags"]["id"]   = str_replace( array(
					"[",
					"]",
				), "", $o["enabled"]["tags"]["name"] ) . "_" . $ar;

			$o["color"]["default"]      = $options["color"][ $ar ];//color
			$o["color"]["tags"]["name"] = "tm_meta[tmfbuilder][" . $name . "_color][" . ( is_null( $counter ) ? 0 : $counter ) . "][]";
			$o["color"]["tags"]["id"]   = str_replace( array( "[", "]" ), "", $o["color"]["tags"]["name"] ) . "_" . $ar;

			$o["fee"]["default"]      = $options["fee"][ $ar ];//fee
			$o["fee"]["tags"]["name"] = "tm_meta[tmfbuilder][" . $name . "_fee][" . ( is_null( $counter ) ? 0 : $counter ) . "][]";
			$o["fee"]["tags"]["id"]   = str_replace( array( "[", "]" ), "", $o["fee"]["tags"]["name"] ) . "_" . $ar;

			foreach ( $_extra_options as $__key => $__name ) {
				if ( "multiple_" . $__name["type"] . "_options" == $name ) {
					$_extra_name                       = $__name["name"];
					$o[ $_extra_name ]["default"]      = $options[ $_extra_name ][ $ar ];
					$o[ $_extra_name ]["tags"]["name"] = "tm_meta[tmfbuilder][" . $name . "_" . $_extra_name . "][" . ( is_null( $counter ) ? 0 : $counter ) . "][]";
					$o[ $_extra_name ]["tags"]["id"]   = str_replace( array(
							"[",
							"]",
						), "", $o[ $_extra_name ]["tags"]["name"] ) . "_" . $ar;
					if ( isset( $o[ $_extra_name ]["admin_class"] ) ) {
						$o[ $_extra_name ]["admin_class"] = "tc-extra-option " . $o[ $_extra_name ]["admin_class"];
					} else {
						$o[ $_extra_name ]["admin_class"] = "tc-extra-option";
					}
				}
			}

			$o = apply_filters( 'wc_epo_builder_element_array_in_loop_after', $o, $options, $ar, $name, $counter );

			$is_enabled = $o["enabled"]["default"] == '' ? ' choice_is_disabled' : '';
			if ( empty( $return_js ) ) {
				echo "<div class='options_wrap" . esc_attr( $hidden_class ) . esc_attr( $is_enabled ) . "'>"
				     . "<div class='tc-row nopadding tc-clearfix'>";

				echo "<div class='tc-cell tc-col-auto tm_cell_move'>";

				// drag
				THEMECOMPLETE_EPO_HTML()->tm_make_button( array(
					"text" => "",
					"tags" => array( "href" => "#move", "class" => "tmicon tcfa tcfa-grip-vertical move" ),
				), 1 );
				THEMECOMPLETE_EPO_HTML()->tm_make_button( array(
					"text" => "",
					"icon" => "angle-up",
					"tags" => array( "href" => "#move", "class" => "tc tc-button small builder_panel_up" ),
				), 1 );
				THEMECOMPLETE_EPO_HTML()->tm_make_button( array(
					"text" => "",
					"icon" => "angle-down",
					"tags" => array( "href" => "#move", "class" => "tc tc-button small builder_panel_down" ),
				), 1 );

				echo "</div>";
				echo "<div class='tc-cell tc-col-auto tm_cell_default'>";

				//default_select
				echo '<span class="tm-hidden-inline">' . ( ( $name == "multiple_checkboxes_options" ) ? esc_html__( "Checked", 'woocommerce-tm-extra-product-options' ) : esc_html__( "Default", 'woocommerce-tm-extra-product-options' ) ) . '</span>';
			}
			if ( $name == "multiple_checkboxes_options" ) {
				if ( empty( $return_js ) ) {
					echo '<input type="checkbox" value="'
					     . esc_attr( $d_counter )
					     . '" name="tm_meta[tmfbuilder]['
					     . esc_attr( $name ) . '_default_value][' . ( is_null( $counter ) ? 0 : $counter ) . '][]" class="tm-default-checkbox" ';
					checked( ( is_null( $counter )
						? ""
						: ( isset( $default_value[ $counter ] )
							? is_array( $default_value[ $counter ] ) && in_array( (string) $d_counter, $default_value[ $counter ] )
							: "" ) ), true, 1 );
					echo '>';
				} else {
					$js_object[ $d_counter ][] = array(
						"id"      => $name . '_default_value',
						"default" => (string) $d_counter,
						"checked" => ( is_null( $counter )
							? ""
							: ( isset( $default_value[ $counter ] )
								? is_array( $default_value[ $counter ] ) && in_array( (string) $d_counter, $default_value[ $counter ] )
								: "" ) ),
						"type"    => "checkbox",
						"tags"    => array(
							"name"  => "tm_meta[tmfbuilder][" . $name . "_default_value][" . ( is_null( $counter ) ? 0 : $counter ) . "][]",
							"class" => "tm-default-checkbox",
						),
					);
				}
			} else {
				if ( empty( $return_js ) ) {
					echo '<input type="radio" value="'
					     . esc_attr( $d_counter )
					     . '" name="tm_meta[tmfbuilder]['
					     . esc_attr( $name ) . '_default_value][' . ( is_null( $counter ) ? 0 : $counter ) . ']" class="tm-default-radio" ';
					checked( ( is_null( $counter )
						? ""
						: ( ( isset( $default_value[ $counter ] ) && ! is_array( $default_value[ $counter ] ) )
							? (string) $default_value[ $counter ]
							: "" ) ), $d_counter, 1 );
					echo '>';
				} else {
					$js_object[ $d_counter ][] = array(
						"id"      => $name . '_default_value',
						"default" => (string) $d_counter,
						"checked" => ( is_null( $counter )
							? ""
							: ( ( isset( $default_value[ $counter ] ) && ! is_array( $default_value[ $counter ] ) )
								? (string) $default_value[ $counter ] === (string) $d_counter
								: "" ) ),
						"type"    => "radio",
						"tags"    => array(
							"name"  => "tm_meta[tmfbuilder][" . $name . "_default_value][" . ( is_null( $counter ) ? 0 : $counter ) . "]",
							"class" => "tm-default-checkbox",
						),
					);
				}
			}
			if ( empty( $return_js ) ) {
				echo "</div>";
				echo "<div class='tc-cell tc-col-3 tm_cell_title'>";
				THEMECOMPLETE_EPO_HTML()->tm_make_field( $o["title"], 1 );
				echo "</div>";
				echo "<div class='tc-cell tc-col-3 tm_cell_images'>";
				THEMECOMPLETE_EPO_HTML()->tm_make_field( $o["image"], 1 );
				THEMECOMPLETE_EPO_HTML()->tm_make_field( $o["imagec"], 1 );
				THEMECOMPLETE_EPO_HTML()->tm_make_field( $o["imagep"], 1 );
				THEMECOMPLETE_EPO_HTML()->tm_make_field( $o["imagel"], 1 );
				if ( $name !== "multiple_selectbox_options" ) {
					THEMECOMPLETE_EPO_HTML()->tm_make_field( $o["color"], 1 );
				}
				echo "</div>";

				echo "<div class='tc-cell tc-col-0 tm_cell_value'>";
				THEMECOMPLETE_EPO_HTML()->tm_make_field( $o["value"], 1 );
				echo "</div>";
				echo "<div class='tc-cell tc-col-auto tm_cell_price'>";
			} else {
				$js_object[ $d_counter ][] = $this->remove_for_js( $o["title"] );
				$js_object[ $d_counter ][] = $this->remove_for_js( $o["image"] );
				$js_object[ $d_counter ][] = $this->remove_for_js( $o["imagec"] );
				$js_object[ $d_counter ][] = $this->remove_for_js( $o["imagep"] );
				$js_object[ $d_counter ][] = $this->remove_for_js( $o["imagel"] );
				if ( $name !== "multiple_selectbox_options" ) {
					$js_object[ $d_counter ][] = $this->remove_for_js( $o["color"] );
				}
				$js_object[ $d_counter ][] = $this->remove_for_js( $o["value"] );
			}

			if ( ! empty( $additional_currencies ) && is_array( $additional_currencies ) ) {
				$_copy_value                          = $o["price"];
				$_sale_copy_value                     = $o["sale_price"];
				$o["price"]["html_before_field"]      = '<span class="tm-choice-currency">' . THEMECOMPLETE_EPO_HELPER()->wc_base_currency() . '</span>';
				$o["sale_price"]["html_before_field"] = '<span class="tm-choice-currency">' . THEMECOMPLETE_EPO_HELPER()->wc_base_currency() . '</span>' . '<span class="tm-choice-sale">' . esc_html__( "Sale", 'woocommerce-tm-extra-product-options' ) . '</span>';
				if ( empty( $return_js ) ) {
					THEMECOMPLETE_EPO_HTML()->tm_make_field( $o["price"], 1 );
					THEMECOMPLETE_EPO_HTML()->tm_make_field( $o["sale_price"], 1 );
				} else {
					$js_object[ $d_counter ][] = $this->remove_for_js( $o["price"] );
					$js_object[ $d_counter ][] = $this->remove_for_js( $o["sale_price"] );
				}
				foreach ( $additional_currencies as $ckey => $currency ) {
					$mt_prefix             = THEMECOMPLETE_EPO_HELPER()->get_currency_price_prefix( $currency );
					$copy_value            = $_copy_value;
					$copy_value["default"] = isset( $options[ "price_" . $currency ][ $ar ] ) ? $options[ "price" . $mt_prefix ][ $ar ] : "";
					$copy_value["id"]      .= $mt_prefix;

					$copy_value["html_before_field"] = '<span class="tm-choice-currency">' . $currency . '</span>';
					$copy_value["tags"]["name"]      = "tm_meta[tmfbuilder][" . $name . "_price" . $mt_prefix . "][" . ( is_null( $counter ) ? 0 : $counter ) . "][]";
					$copy_value["tags"]["id"]        = str_replace( array(
							"[",
							"]",
						), "", $copy_value["tags"]["name"] ) . "_" . $ar;
					if ( empty( $return_js ) ) {
						THEMECOMPLETE_EPO_HTML()->tm_make_field( $copy_value, 1 );
					} else {
						$js_object[ $d_counter ][] = $copy_value;
					}

					$copy_value            = $_sale_copy_value;
					$copy_value["default"] = isset( $options[ "sale_price_" . $currency ][ $ar ] ) ? $options[ "sale_price" . $mt_prefix ][ $ar ] : "";
					$copy_value["id"]      .= $mt_prefix;

					$copy_value["html_before_field"] = '<span class="tm-choice-currency">' . $currency . '</span>' . '<span class="tm-choice-sale">' . esc_html__( "Sale", 'woocommerce-tm-extra-product-options' ) . '</span>';
					$copy_value["tags"]["name"]      = "tm_meta[tmfbuilder][" . $name . "_sale_price" . $mt_prefix . "][" . ( is_null( $counter ) ? 0 : $counter ) . "][]";
					$copy_value["tags"]["id"]        = str_replace( array(
							"[",
							"]",
						), "", $copy_value["tags"]["name"] ) . "_" . $ar;
					if ( empty( $return_js ) ) {
						THEMECOMPLETE_EPO_HTML()->tm_make_field( $copy_value, 1 );
					} else {
						$js_object[ $d_counter ][] = $copy_value;
					}
				}
			} else {
				$o["price"]["html_before_field"]      = '<span class="tm-choice-regular">' . esc_html__( "Regular", 'woocommerce-tm-extra-product-options' ) . '</span>';
				$o["sale_price"]["html_before_field"] = '<span class="tm-choice-sale">' . esc_html__( "Sale", 'woocommerce-tm-extra-product-options' ) . '</span>';
				if ( empty( $return_js ) ) {
					THEMECOMPLETE_EPO_HTML()->tm_make_field( $o["price"], 1 );
					THEMECOMPLETE_EPO_HTML()->tm_make_field( $o["sale_price"], 1 );
				} else {
					$js_object[ $d_counter ][] = $this->remove_for_js( $o["price"] );
					$js_object[ $d_counter ][] = $this->remove_for_js( $o["sale_price"] );
				}
			}

			if ( empty( $return_js ) ) {
				THEMECOMPLETE_EPO_HTML()->tm_make_field( $o["price_type"], 1 );
				echo "</div>";
				echo "<div class='tc-cell tc-col-auto tm_cell_delete'>";

				// del
				THEMECOMPLETE_EPO_HTML()->tm_make_button( array(
					"text" => "",
					"tags" => array( "class" => "tmicon tcfa tcfa-times delete builder_panel_delete" ),
				), 1 );

				echo "</div>";

				if ( $name === "multiple_checkboxes_options" ) {
					echo "<div class='" . apply_filters( "wc_epo_builder_element_multiple_checkboxes_options_class", "tc-cell tc-col-12 tm_cell_fee", $o ) . "'><span class='tm-inline-label bsbb'>" . esc_html__( "Set to Fee", 'woocommerce-tm-extra-product-options' ) . "</span>";
					THEMECOMPLETE_EPO_HTML()->tm_make_field( $o["fee"], 1 );
					echo "</div>";

					do_action( "wc_epo_builder_element_multiple_checkboxes_options", $o );
				}
				echo "<div class='tc-cell tc-col-12 tm_cell_description'><span class='tm-inline-label bsbb'>" . esc_html__( "Description", 'woocommerce-tm-extra-product-options' ) . "</span>";
				THEMECOMPLETE_EPO_HTML()->tm_make_field( $o["description"], 1 );
				echo "</div>";

				echo "<div class='tc-cell tc-col-12 tm_cell_enabled'><span class='tm-inline-label bsbb'>" . esc_html__( "Enabled", 'woocommerce-tm-extra-product-options' ) . "</span>";
				THEMECOMPLETE_EPO_HTML()->tm_make_field( $o["enabled"], 1 );
				echo "</div>";
			} else {
				if ( $name === "multiple_checkboxes_options" ) {

					$js_object[ $d_counter ][] = array(
						"id"      => $name . '_fee',
						"default" => (string) $o["fee"]["default"],
						"checked" => (string) $o["fee"]["default"] === (string) $o["fee"]["tags"]["value"],
						"type"    => "checkbox",
						"tags"    => array(
							"name"  => "tm_meta[tmfbuilder][" . $name . "_fee][" . ( is_null( $counter ) ? 0 : $counter ) . "][]",
							"value" => $o["fee"]["tags"]["value"],
						),
					);

					$js_object = apply_filters( "wc_epo_builder_element_multiple_checkboxes_options_js_object", $js_object, $d_counter, $o, $name, $counter );

				}
				$js_object[ $d_counter ][] = $this->remove_for_js( $o["price_type"] );
				$js_object[ $d_counter ][] = $this->remove_for_js( $o["description"] );
				$js_object[ $d_counter ][] = $this->remove_for_js( $o["enabled"] );
			}

			foreach ( $_extra_options as $__key => $__name ) {
				if ( "multiple_" . $__name["type"] . "_options" == $name ) {
					$_extra_name = $__name["name"];
					if ( empty( $return_js ) ) {
						echo "<div class='tc-cell tc-col-12 " . esc_attr( $__name["admin_class"] ) . "'>";
						echo "<span class='tm-inline-label bsbb'>" . esc_attr( $__name["label"] ) . "</span>";
						THEMECOMPLETE_EPO_HTML()->tm_make_field( $o[ $_extra_name ], 1 );
						echo "</div>";
					} else {
						$js_object[ $d_counter ][] = $this->remove_for_js( $o[ $_extra_name ] );
					}
				}
			}
			if ( empty( $return_js ) ) {
				echo "<div class='tc-cell tc-col-12 tm_cell_url'><span class='tm-inline-label bsbb'>" . esc_html__( "URL", 'woocommerce-tm-extra-product-options' ) . "</span>";
				THEMECOMPLETE_EPO_HTML()->tm_make_field( $o["url"], 1 );
				echo "</div>";

				echo "</div></div>";
			} else {
				$js_object[ $d_counter ][] = $this->remove_for_js( $o["url"] );
			}
			$d_counter ++;
		}
		if ( empty( $return_js ) ) {
			echo "</div>";
			echo ' <button type="button" class="tc tc-button builder-panel-add">' . esc_html__( "Add item", 'woocommerce-tm-extra-product-options' ) . '</button>';
			echo ' <button type="button" class="tc tc-button builder-panel-mass-add">' . esc_html__( "Mass add", 'woocommerce-tm-extra-product-options' ) . '</button>';
		} else {
			return $js_object;
		}
	}

}
