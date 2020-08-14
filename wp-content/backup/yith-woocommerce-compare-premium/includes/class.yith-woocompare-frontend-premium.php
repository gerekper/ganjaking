<?php
/**
 * Frontend class premium
 *
 * @author YITH
 * @package YITH Woocommerce Compare Premium
 * @version 1.1.4
 */

if ( ! defined( 'YITH_WOOCOMPARE' ) ) {
	exit;
} // Exit if accessed directly

if( ! class_exists( 'YITH_Woocompare_Frontend_Premium' ) ) {
    /**
     * YITH Custom Login Frontend
     *
     * @since 1.0.0
     */
    class YITH_Woocompare_Frontend_Premium extends YITH_Woocompare_Frontend {
        /**
         * Plugin version
         *
         * @var string
         * @since 1.0.0
         */
        public $version = YITH_WOOCOMPARE_VERSION;

	    /**
	     * The list of current cat inside the comparison table
	     *
	     * @var array
	     * @since 1.0.0
	     */
	    public $current_cat = array();

	    /**
	     * An array of excluded categories from plugin options
	     *
	     * @var array
	     * @since 2.1.0
	     */
	    public $excluded_categories = array();

	    /**
	     * Check only categories in the exclusion list will have the compare feature
	     *
	     * @var boolean
	     * @since 2.1.0
	     */
	    public $excluded_categories_inverse = false;

	    /**
	     * Option for use page or popup
	     *
	     * @var array
	     * @since 2.1.0
	     */
	    public $page_or_popup = 'popup';

	    /**
	     * Plugin version
	     *
	     * @var string
	     * @since 1.0.0
	     */
	    public $template_file = 'yith-compare-popup.php';

	    /**
	     * Stylesheet file
	     *
	     * @var string
	     * @since 2.1.0
	     */
	    public $stylesheet_file = 'compare-premium.css';

	    /**
	     * The name of categories cookie name
	     *
	     * @var string
	     * @since 1.0.0
	     */
	    public $cat_cookie = 'yith_woocompare_current_cat';

        /**
         * The name of limit reached cookie name
         *
         * @var string
         * @since 1.0.0
         */
        public $limit_cookie = 'yith_woocompare_limit_reached';

        /**
         * If product limits is reached
         *
         * @var boolean
         * @since 1.0.0
         */
        public $limit_reached = false;

	    /**
	     * Check if related is enabled
	     *
	     * @var boolean
	     * @since 2.1.0
	     */
	    public $related_enabled = false;

	    /**
	     * Check if share is enabled
	     *
	     * @var boolean
	     * @since 2.1.0
	     */
	    public $share_enabled = false;

		/**
		 * Check if option for Compare by category is active
		 *
		 * @var boolean
		 * @since 2.1.0
		 */
		public $compare_by_cat = false;

	    /**
	     * The action used to view the comparison table
	     *
	     * @var string
	     * @since 1.0.0
	     */
	    public $action_filter = 'yith_woocompare_filter_by_cat';

	    /**
	     * The compare page id
	     *
	     * @var int
	     * @since 2.1.0
	     */
	    public $page_id = 0;

	    /**
	     * Is or not an iFrame
	     *
	     * @var string
	     * @since 2.2.2
	     */
	    public $is_iframe = 'no';

        /**
         * Constructor
         *
         * @since 1.0.0
         */
        public function __construct() {

	        parent::__construct();

	        add_action( 'init', array( $this, 'init_variables' ), 1 );

	        // populate the list of current categories
	        add_action( 'init', array( $this, 'update_cat_cookie' ), 11 );

	        // before table
	        add_action( 'yith_woocompare_before_main_table', array( $this, 'add_error_limit_message' ), 5 );
	        add_action( 'yith_woocompare_before_main_table', array( $this, 'add_logo_to_compare' ), 10 );
	        add_action( 'yith_woocompare_before_main_table', array( $this, 'print_filter_by_cat' ), 20, 2 );
	        add_action( 'yith_woocompare_before_main_table', array( $this, 'add_clear_all_button' ), 30, 2 );
	        add_action( 'yith_woocompare_before_main_table', array( $this, 'wc_bundle_compatibility' ) );

	        // after table
            add_action( 'yith_woocompare_after_main_table', array( $this, 'social_share' ), 10 );
            add_action( 'yith_woocompare_after_main_table', array( $this, 'related_product' ), 20, 2 );

	        // list action
	        add_action( 'yith_woocompare_after_add_product', array( $this, 'update_cat_cookie_after_add' ), 10, 1 );
	        add_action( 'yith_woocompare_after_remove_product', array( $this, 'update_cat_cookie_after_remove' ), 10, 1 );

	        add_filter( 'yith_woocompare_exclude_products_from_list', array( $this, 'exclude_product_from_list' ), 10, 1 );
	        add_filter( 'yith_woocompare_view_table_url', array( $this, 'filter_table_url' ), 10, 2 );

	        // filter compare link
	        add_filter( 'yith_woocompare_remove_compare_link_by_cat', array( $this, 'exclude_compare_by_cat' ), 10, 2 );

	        // enqueue scripts styles premium
	        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_premium_scripts' ), 5 );
	        // localize args filter
	        add_filter( 'yith_woocompare_main_script_localize_array', array( $this, 'premium_localize_args' ), 10, 1 );

	        // add the shortcode
	        add_shortcode( 'yith_woocompare_table', array( $this, 'compare_table_sc' ) );

	        if( $this->use_wc_ajax ){
		        add_action( 'wc_ajax_' . $this->action_filter, array( $this, $this->action_filter . '_ajax' ) );
	        }
	        else {
		        add_action( 'wp_ajax_' . $this->action_filter, array( $this, $this->action_filter . '_ajax' ) );
	        }
	        add_action( 'wp_ajax_nopriv' . $this->action_filter, array( $this, $this->action_filter . '_ajax' ) );

	        // filter remove response
	        add_action( 'yith_woocompare_remove_product_action_ajax', array( $this, 'remove_product_ajax_premium' ) );
	        // filter added response
	        add_action( 'yith_woocompare_add_product_action_ajax', array( $this, 'added_related_to_compare' ) );

			add_filter( 'yith_woocompare_get_different_fields_value', array( $this, 'add_different_class' ), 99, 1 );

			add_action( 'yith_woocompare_popup_head', array( $this, 'add_styles_themes_and_custom') );
	        
	        add_filter( 'yith_woocompare_add_product_action_json', array( $this, 'premium_add_product_action_json'), 10, 1 );
	        
	        // remove scripts from popup footer
	        add_action( 'yith_woocompare_popup_footer', array( $this, 'dequeue_footer_scripts' ), 10, 1 );

	        // add counter shortcode
            add_shortcode( 'yith_woocompare_counter', array( $this, 'counter_compare_shortcode' ) );

            /* Compatibility with YITH WooCommerce Color and Label Variations (single variations module) */
            add_filter( 'yith_woocompare_single_variation_field_value', array($this,'yith_woocompare_set_single_variation_field_value'),10,3 );
        }

	    /**
	     * Init class variables
	     *
	     * @since 2.1.0
	     * @author Francesco Licandro
	     */
	    public function init_variables(){

	        parent::init_variables();

		    // Get page from option
		    $this->page_id = get_option( 'yith_woocompare_compare_page' );
		    // else get default
		    ! $this->page_id && $this->page_id = get_option( 'yith-woocompare-page-id' );
		    if( function_exists( 'wpml_object_id_filter' ) && $this->page_id ) {
			    $this->page_id = wpml_object_id_filter( $this->page_id, 'page', true );
		    }

		    // page or popup?
		    $this->page_or_popup = get_option( 'yith_woocompare_use_page_popup', 'popup' );

		    // related enabled
		    $this->related_enabled = get_option( 'yith-woocompare-show-related', 'yes' ) == 'yes';

		    // enabled share
		    $this->share_enabled = get_option( 'yith-woocompare-enable-share', 'yes' ) == 'yes';

		    // excluded categories array
		    $this->excluded_categories = $this->get_excluded_categories();
		    // init some options
		    $this->excluded_categories_inverse = ( get_option( 'yith_woocompare_excluded_category_inverse', 'no' ) == 'yes' );

			$this->compare_by_cat = get_option( 'yith_woocompare_use_category', 'no' ) == 'yes';

		    isset( $_REQUEST['iframe'] ) && $this->is_iframe = $_REQUEST['iframe'];

            if( isset( $_COOKIE[ $this->limit_cookie ] ) && $_COOKIE[ $this->limit_cookie ] == 'yes' ) {
                setcookie( $this->limit_cookie, 'no', 0, COOKIEPATH, COOKIE_DOMAIN, false, false );
                $this->limit_reached = true;
            }
	    }

        /**
         * Add a product in the products comparison table
         *
         * @param int $product_id product ID to add in the comparison table
         * @return boolean
         */
        public function add_product_to_compare( $product_id ) {
            if( $this->limit_reached() ) {
                setcookie( $this->limit_cookie, 'yes', 0, COOKIEPATH, COOKIE_DOMAIN, false, false );
                return false;
            }

            $this->products_list[] = $product_id;

            $expiry = apply_filters('yith_woocompare_cookie_expiration',0);

            setcookie( $this->get_cookie_name(), json_encode( $this->products_list ), $expiry, COOKIEPATH, COOKIE_DOMAIN, false, false );

            do_action( 'yith_woocompare_after_add_product', $product_id );

            return true;
        }

        /**
         * Check if compared product limit is reached
         *
         * @since 2.3.2
         * @author Francesco Licandro
         * @return boolean
         */
        public function limit_reached(){
            $limit = intval( get_option( 'yith_woocompare_num_product_compared', 0 ) );
            if( $limit && count( $this->products_list ) >= $limit ) {
                $this->limit_reached = true;
            } else {
                $this->limit_reached = false;
            }

            return $this->limit_reached;
        }

	    /**
	     * Update current categories cookie
	     *
	     * @since 2.0.0
	     * @author Francesco Licandro
	     * @param mixed $categories
	     */
	    public function update_cat_cookie( $categories = array() ){

		    if( isset( $_REQUEST['yith_compare_prod'] ) ) {

				$products = explode( ',', base64_decode( $_REQUEST['yith_compare_prod'] ) );
			    $this->set_variables_prod_cat( $products );
		    }
		    elseif( isset( $_REQUEST['yith_compare_cat'] ) ) {
			    $this->current_cat = array( intval( $_REQUEST['yith_compare_cat'] ) );
		    }
		    elseif( ! empty( $categories ) ) {
			    $this->current_cat = $categories;
		    }
		    elseif( ! empty( $this->products_list ) ) {
			    $products = $this->products_list;
			    $product = array_pop( $products );
				$categories = array_keys( $this->get_product_categories( $product ) );

			    $this->current_cat = $categories;
		    }
			else {
				$this->current_cat = $categories;
			}
	    }

		/**
		 * Set products_list and current_cat variables if $_REQUEST['yith_compare_prod'] is set
		 *
		 * @param array $products
		 * @since 2.0.0
		 * @author Francesco Licandro
		 */
		public function set_variables_prod_cat( $products ) {

			$product = array_pop( $products );
			if( ! isset( $_REQUEST['yith_compare_cat'] ) )
				$categories = array_keys( $this->get_product_categories( $product ) );
			else
				$categories = array( intval( $_REQUEST['yith_compare_cat'] ) );

			$this->current_cat = $categories;
		}

	    /**
	     * Update cookie after add to compare table
	     *
	     * @since 2.0.0
	     * @author Francesco Licandro
	     * @param $id
	     */
	    public function update_cat_cookie_after_add( $id ) {
		    $categories = array_keys( $this->get_product_categories( $id ) );

		    if( ! empty( $categories ) ){
			    $this->update_cat_cookie( $categories );
	        }
	    }

	    /**
	     * Update cookie after remove product from compare table
	     *
	     * @since 2.0.0
	     * @author Francesco Licandro
	     * @param $id
	     */
	    public function update_cat_cookie_after_remove( $id ) {
		    // get categories
		    $categories = $this->get_product_categories( $id );
			$exist      = array();

		    if( ! empty( $categories ) ) {
				foreach( $this->products_list as $product ) {
					$cat    = $this->get_product_categories( $product );
					foreach( $cat as $id => $name ) {
						if( array_key_exists( $id, $categories ) && ! in_array( $id, $exist ) ){
							$exist[] = $id;
						}
					}
				}
	        }

		    //remove old cookie
		    if( isset( $_COOKIE[ $this->cat_cookie ] ) )
			    unset( $_COOKIE[ $this->cat_cookie ] );

		    $this->update_cat_cookie( $exist );
	    }

	    /**
	     * Enqueue premium scripts and styles
	     *
	     * @since 2.0.0
	     * @author Francesco Licandro <francesco.licandro@yithemes.com>
	     */
	    public function enqueue_premium_scripts(){

		    global $sitepress, $post;

		    wp_register_style( 'yith_woocompare_page', $this->stylesheet_url(), array(), false, 'all' );
		    wp_register_script( 'yith_woocompare_owl', YITH_WOOCOMPARE_ASSETS_URL . '/js/owl.carousel.min.js', array( 'jquery' ), '2.0.0', true );
		    wp_register_style( 'yith_woocompare_owl_style', YITH_WOOCOMPARE_ASSETS_URL . '/css/owl.carousel.css', array(), false, 'all' );

		    // dataTables
		    wp_register_script( 'jquery-fixedheadertable', YITH_WOOCOMPARE_ASSETS_URL . '/js/jquery.dataTables.min.js', array('jquery'), '1.10.18', true );
		    wp_register_style( 'jquery-fixedheadertable-style', YITH_WOOCOMPARE_ASSETS_URL . '/css/jquery.dataTables.css', array(), '1.10.18', 'all' );
		    wp_register_script( 'jquery-fixedcolumns', YITH_WOOCOMPARE_ASSETS_URL . '/js/FixedColumns.min.js', array('jquery', 'jquery-fixedheadertable' ), '3.2.6', true );
		    wp_register_script( 'jquery-imagesloaded', YITH_WOOCOMPARE_ASSETS_URL . '/js/imagesloaded.pkgd.min.js', array('jquery'), '3.1.8', true );

		    // if page remove colorbox
		    if( $this->page_or_popup == 'page' ) {
			    wp_dequeue_style( 'jquery-colorbox' );
			    wp_dequeue_script( 'jquery-colorbox' );
		    }

		    // get custom style
		    $inline_css = yith_woocompare_user_style();
			wp_add_inline_style( 'yith_woocompare_page', $inline_css );

			wp_enqueue_style( 'yith_woocompare_page' );
			wp_enqueue_style( 'jquery-fixedheadertable-style' );

			if( apply_filters( 'yith_woocompare_enqueue_frontend_scripts_always',false) || is_page( $this->page_id ) || ( ! is_null( $post ) && strpos( $post->post_content, '[yith_woocompare_table' ) !== false ) ) {

			    wp_enqueue_script( 'jquery-fixedheadertable' );
			    wp_enqueue_script( 'jquery-fixedcolumns' );
			    wp_enqueue_script( 'jquery-imagesloaded' );

			    if( ! empty( $this->products_list ) || $this->related_enabled || ! isset( $_REQUEST['yith_compare_prod'] ) ) {
				    wp_enqueue_script( 'yith_woocompare_owl' );
				    wp_enqueue_style( 'yith_woocompare_owl_style' );
			    }
		    }
	    }

	    /**
	     * Set custom style based on user options
	     *
	     * @since 2.0.0
	     * @author Francesco Licandro
	     * @return string
	     */
	    public function custom_user_style() {
		    return yith_woocompare_user_style();
	    }

	    /**
	     * Add premium args to localize script
	     * 
	     * @author Francesco Licandro
	     * @since 2.1.0
	     * @param array $args
	     * @return array
	     */
	    public function premium_localize_args( $args ){
		    return array_merge( $args, array(
			    'is_page' => $this->page_or_popup == 'page',
			    'page_url' => $this->get_compare_page_url(),
			    'im_in_page' => is_page( $this->page_id ),
			    'view_label'    => get_option( 'yith_woocompare_button_text_added', __( 'View Compare', 'yith-woocommerce-compare' ) ),
			    'actionfilter' => $this->action_filter,
			    'num_related'   => get_option( 'yith-woocompare-related-visible-num', 4 ),
			    'autoplay_related'  => get_option( 'yith-woocompare-related-autoplay', 'no' ) == 'yes',
			    'loader'    => YITH_WOOCOMPARE_ASSETS_URL . '/images/loader.gif',
			    'button_text'	=> get_option( 'yith_woocompare_button_text', __( 'Compare', 'yith-woocommerce-compare' ) ),
			    'fixedcolumns'	=> get_option( 'yith_woocompare_num_fixedcolumns', 1 )
		    ) );
	    }

		/**
		 * Add scripts for shortcodes
		 *
		 * @since 2.0.3
		 * @author Francesco Licandro
		 */
		public function add_scripts() {
			wp_enqueue_script( 'jquery-fixedheadertable' );
			wp_enqueue_script( 'jquery-fixedcolumns' );
			wp_enqueue_script( 'jquery-imagesloaded' );
		}

	    /**
	     * The fields to show in the table
	     *
	     * @param array $products
	     * @return mixed
	     * @since 1.0.0
	     */
	    public function fields( $products = array() ) {

		    // get options
		    $fields  = get_option( 'yith_woocompare_fields', array() );
		    $dynamic = get_option( 'yith_woocompare_dynamic_attributes', 'no' ) == 'yes';
		    $custom  = get_option( 'yith_woocompare_custom_attributes', 'no' ) == 'yes';

		    // remove disabled attributes
		    $fields = array_filter( $fields );

		    foreach ( $fields as $key => $value ) {
			    // add if default
			    if ( isset( $this->default_fields[$key] ) ) {
				    $fields[ $key ] = $this->default_fields[$key];
			    }
			    // add if in options
			    elseif ( taxonomy_exists( $key ) && ! $dynamic ) {
				    $fields[ $key ] = wc_attribute_label( $key );
			    }
		    }

		    if( ! empty( $products ) && ( $dynamic || $custom ) ) {

			    foreach( $products as $product_id ) {
				    $product = wc_get_product( $product_id );

				    if( ! $product ) {
					    continue;
				    }

                    if( $product instanceof WC_Product_Variation){

                        $parent_product = wc_get_product($product->get_parent_id());

                        $attrs = $parent_product->get_attributes();

                    }else{
                        $attrs = $product->get_attributes();
                    }



				    foreach( $attrs as $key => $value ) {

					    // decode
					    $key = urldecode( $key );

					    if( ! isset( $value['is_taxonomy'] ) ||
					        ( $value['is_taxonomy'] && ! array_key_exists( $key, $fields ) ) ||
					        ( $value['is_taxonomy'] && ! $dynamic ) ||
					        ( ! $value['is_taxonomy'] && ! $custom ) ) {
						    continue;
					    }
					    // add field
					    $fields[ $key ] = wc_attribute_label( $key, $product );
				    }
			    }



			    if( $dynamic ) {
				    foreach( $fields as $key => $value ) {
					    if( $value === true ) {
						    unset( $fields[$key] );
					    }
				    }
			    }
		    }

		    return apply_filters( 'yith_woocompare_filter_table_fields', $fields, $products );
	    }

	    /**
	     * Return the array with all products and all attributes values
	     *
	     * @param mixed $products
	     * @return array The complete list of products with all attributes value
	     */
	    public function get_products_list( $products = array() ) {
		    $list = array();

			empty( $products ) && $products = $this->products_list;
			$products = apply_filters( 'yith_woocompare_exclude_products_from_list', $products );

		    $fields = $this->fields( $products );

		    foreach ( $products as $product_id ) {

			    /**
			     * @type object $product /WC_Product
			     */
			    $product = $this->wc_get_product( $product_id );

			    if ( ! $product )
				    continue;

			    $product->fields = array();
			    $attributes = $product->get_attributes();

			    // custom attributes
			    foreach ( $fields as $field => $name )  {

				    switch( $field ) {
					    case 'price':
						    $product->fields[$field] = $product->get_price_html();
						    break;
					    case 'description':
						    // get description
						    $description = ( get_option( 'yith_woocompare_use_full_description', 'no' ) == 'yes' ) ? yit_get_prop( $product, 'post_content' ) : '';
						    ! $description && $description = apply_filters( 'woocommerce_short_description', yit_get_prop( $product, 'post_excerpt' ) );

						    $product->fields[$field] = apply_filters( 'yith_woocompare_products_description', $description, $product );
						    break;
					    case 'stock':
						    $availability = $product->get_availability();
						    if ( empty( $availability['availability'] ) ) {
							    $availability['availability'] = __( 'In stock', 'yith-woocommerce-compare' );
						    }
						    $product->fields[$field] = sprintf( '<span>%s</span>', esc_html( $availability['availability'] ) );
						    break;
					    case 'sku':
							$sku = $product->get_sku();
							! $sku && $sku = '-';
						    $product->fields[$field] = $sku;
						    break;
					    case 'weight':
						    if( $weight = $product->get_weight() ){
							    $weight = wc_format_localized_decimal( $weight ) . ' ' . esc_attr( get_option( 'woocommerce_weight_unit' ) );
						    }
						    else {
							    $weight = '-';
						    }
						    $product->fields[$field] = sprintf( '<span>%s</span>', esc_html( $weight ) );
						    break;
					    case 'dimensions':
						    $dimensions = function_exists( 'wc_format_dimensions' ) ? wc_format_dimensions( $product->get_dimensions(false) ) : $product->get_dimensions();
						    ! $dimensions && $dimensions = '-';

						    $product->fields[$field] = sprintf( '<span>%s</span>', esc_html( $dimensions ) );
						    break;
					    default:

                            $sfield = strtolower( urlencode( $field ) );

                            if ( taxonomy_exists( $field ) ) {
							    $product->fields[$field] = array();
                                $the_product_id = $product->is_type('variation') ? $product->get_parent_id() : $product_id; // get always parent attributes for variations
							    $terms          = get_the_terms( $the_product_id, $field );

							    if ( ! empty( $terms ) ) {
								    foreach ( $terms as $term ) {
									    $term = sanitize_term( $term, $field );
									    $product->fields[$field][] = $term->name;
								    }
							    }
							    $product->fields[$field] = implode( ', ', $product->fields[$field] );
						    }
						    elseif( ! empty( $attributes ) && isset( $attributes[$sfield] ) ) {

                                $current_attribute = $attributes[$sfield];
                                
							    if( ! empty( $current_attribute['options'] ) ) {
								    $product->fields[$field] = implode( ', ', $current_attribute['options'] );
							    }
						    }
						    else {
							    do_action_ref_array( 'yith_woocompare_field_' . $field, array( $product, &$product->fields ) );
						    }
						    break;
				    }
			    }

			    $list[ $product_id ] = $product;
		    }

		    return $list;
	    }

	    /**
	     *  Add the link to compare
	     */
	    public function add_compare_link( $product_id = false, $args = array() ) {
		    extract( $args );

		    if ( ! $product_id ) {
			    global $product;
			    $product_id = ! is_null( $product ) ? yit_get_prop( $product, 'id', true ) : 0;
		    }

		    // return if product doesn't exist
		    if ( empty( $product_id ) || apply_filters( 'yith_woocompare_remove_compare_link_by_cat', false, $product_id ) )
			    return;

		    $is_button = ! isset( $button_or_link ) || ! $button_or_link ? get_option( 'yith_woocompare_is_button', 'button' ) : $button_or_link;

		    if ( ! isset( $button_text ) || $button_text == 'default' ) {
			    $button_text = get_option( 'yith_woocompare_button_text', __( 'Compare', 'yith-woocommerce-compare' ) );
		    }

		    // set class
		    $class =  '';

		    if( in_array( $product_id, $this->products_list ) ) {

			    $categories = get_the_terms( $product_id, 'product_cat' );
			    $cat = array();

			    if( ! empty( $categories ) ) {
				    foreach( $categories as $category ) {
					    $cat[] = $category->term_id;
				    }
			    }

			    $link = $this->view_table_url( $product_id );
			    $class = 'added';
			    $button_text = get_option( 'yith_woocompare_button_text_added', __( 'View Compare', 'yith-woocommerce-compare' ) );
		    }
		    else {
		        if( apply_filters( 'yith_woocompare_skip_display_button', false ) ) return;
			    $link = $this->add_product_url( $product_id );
		    }

		    // add button class
		    $class = $is_button == 'button' ? $class . ' ' . 'button' : $class;

            $button_text = apply_filters( 'yith_woocompare_compare_button_text',$button_text,$product_id );

		    $button_target = apply_filters('yith_woocompare_compare_button_target','_self');

		    printf( '<a href="%s" class="compare %s" data-product_id="%s" rel="nofollow" target="%s">%s</a>', esc_attr( $link ), esc_attr( $class ), esc_attr( $product_id ), esc_attr( $button_target ), esc_html( $button_text ) );
	    }

	    /**
	     * Generate template vars
	     *
	     * @param array $products
	     * @return array
	     * @since 1.0.0
	     * @access protected
	     */
	    protected function _vars( $products = array() ) {

		    // get product list
		    $products = $this->get_products_list( $products );
		    // get field list
		    $fields         = $this->fields( $products );

		    $different = apply_filters( 'yith_woocompare_get_different_fields_value', $products );

		    $show_title     = get_option( 'yith_woocompare_fields_product_info_title', 'yes' ) == 'yes';
		    $show_image     = get_option( 'yith_woocompare_fields_product_info_image', 'yes' ) == 'yes';
		    $show_add_cart  = get_option( 'yith_woocompare_fields_product_info_add_cart', 'yes' ) == 'yes';

		    if( ! $show_title && ! $show_image && ! $show_add_cart ) {
			    unset( $fields['product_info'] );
		    }

		    $vars = array(
			    'products'           => $products,
			    'fields'             => $fields,
			    'show_image'         => $show_image,
			    'show_title'         => $show_title,
			    'show_add_cart'      => $show_add_cart,
			    'show_request_quote' => get_option( 'yith_woocompare_fields_product_info_request_quote', 'no' ),
			    'repeat_price'       => get_option( 'yith_woocompare_price_end', 'no' ),
			    'repeat_add_to_cart' => get_option( 'yith_woocompare_add_to_cart_end', 'no' ),
			    'different'          => $different
		    );

		    return $vars;
	    }

	    /**
	     * Add logo/image to compare table
	     *
	     * @since 2.0.0
	     * @author Francesco Licandro <francesco.licandro@yithemes.com>
	     */
	    public function add_logo_to_compare(){

		    $image    = get_option( 'yith-woocompare-table-image', '' );

		    $toShow  = get_option( 'yith-woocompare-table-image-in-page', 'yes' ) == 'yes' && $this->is_iframe != 'yes';
		    ! $toShow && $toShow = get_option( 'yith-woocompare-table-image-in-popup', 'yes' ) == 'yes';

		    if( ! $image || ! $toShow ) {
			    return;
		    }

		    ob_start();
		    ?>
				<div class="yith_woocompare_table_image">
					<img src="<?php echo esc_url( $image ) ?>" />
				</div>
			<?php

		    echo wp_kses_post( ob_get_clean() );
	    }

	    /**
	     * Get product categories
	     *
	     * @param $product_id
	     * @return mixed
	     * @author Francesco Licandro <francesco.licandro@yithemes.com>
	     */
	    public function get_product_categories( $product_id ) {

		    $cat = $categories = array();

		    if( ! is_array( $product_id ) ) {
			    $categories = get_the_terms( $product_id, 'product_cat' );
		    }
		    else {
			    foreach( $product_id as $id ) {

				    $single_cat = get_the_terms( $id, 'product_cat' );

				    if( empty( $single_cat ) ) {
					    continue;
				    }
				    // get values
				    $single_values = array_values( $single_cat );

				    $categories = array_merge( $categories, $single_values );
			    }
		    }

		    if( empty( $categories ) ) {
			    return $cat;
		    }

		    foreach( $categories as $category ) {
			    if( ! $category ) {
				    continue;
			    }
			    $cat[$category->term_id] = $category->name;
		    }
		    
		    return apply_filters( 'yith_woocompare_get_product_categories', $cat, $categories, $product_id );
	    }

	    /**
	     * Filter vars for compare table
	     *
	     * @since 2.0.0
		 * @param mixed $products
	     * @return mixed
	     * @author Francesco Licandro <francesco.licandro@yithemes.com>
	     */
	    public function exclude_product_from_list( $products ) {

		    // exit if less then 2 products
		    if( count( $products ) < 2 ) {
			    return $products;
		    }

		    $new_products = array();

		    foreach( $products as $product ) {
			    $product_cat = array_keys( $this->get_product_categories( $product ) );

			    // first check for excluded cat
			    if ( ! empty( $this->excluded_categories ) ) {
				    $intersect = array_intersect( $product_cat, $this->excluded_categories );

				    if( ! $this->excluded_categories_inverse && ! empty( $intersect ) ) {
					    continue;
				    }
				    elseif( $this->excluded_categories_inverse && empty( $intersect ) ) {
					    continue;
				    }
			    }

			    // now check for same cat
			    if ( $this->compare_by_cat ) {

				    if ( ! empty( $this->current_cat ) ) {
					    // else intersect array to find same cat
					    $intersect = array_intersect( $product_cat, $this->current_cat );
					    // cat is different
					    if ( empty( $intersect ) ) {
						    continue;
					    }
				    }
			    }

			    $new_products[] = $product;
		    }

		    return $new_products;
	    }

	    /**
	     * Filter view table link
	     *
	     * @param $link
	     * @param $product_id
	     *
	     * @return string
	     * @author Francesco Licandro <francesco.licandro@yithemes.com>
	     */
	    public function filter_table_url( $link, $product_id ) {

		    if( $this->page_or_popup != 'page' ) {
			    return $link;
		    }

		    $page = $this->get_compare_page_url();

		    if( ! $page ) {
			    return $link;
		    }

		    return $page;
	    }


	    /**
	     * Get page url
	     *
	     * @since 2.0.6
	     * @author Francesco Licandro
	     * @return string
	     */
	    public function get_compare_page_url(){
		    return get_permalink( $this->page_id );
	    }

	    /**
	     * Filter compare link
	     *
	     * @since 2.0.0
	     * @param $default
	     * @param $product_id
	     * @return bool
	     * @author Francesco Licandro <francesco.licandro@yithemes.com>
	     */
	    public function exclude_compare_by_cat( $default, $product_id ) {

		    if( empty( $this->excluded_categories ) )
			    return false;

		    // prevent

		    $product_cat = array_keys( $this->get_product_categories( $product_id ) );
		    $intersect = array_intersect( $product_cat, $this->excluded_categories );

		    if( ! $this->excluded_categories_inverse && ! empty( $intersect ) ) {
			    return true;
		    }
		    elseif( $this->excluded_categories_inverse && empty( $intersect ) ) {
			    return true;
		    }

		    return false;
	    }

	    /**
	     * Shortcode to show the compare table
	     *
	     * @since 2.0.0
	     * @author Francesco Licandro <francesco.licandro@yithemes.com>
	     */
	    public function compare_table_sc( $atts, $content = null ) {

		    $atts = shortcode_atts(array(
			    'products' => ''
		    ), $atts );

		    // set products
		    $products = ( is_page( $this->page_id ) && isset( $_REQUEST['yith_compare_prod'] ) ) ? base64_decode( $_REQUEST['yith_compare_prod'] ) : $atts['products'];
		    $products = array_filter( explode( ',', $products ) );

		    // is share or a custom table -> fixed to true
			if( ! empty( $products ) ) {
				$products       = array_unique( $products );
				// then set correct products list and current cat
				$this->set_variables_prod_cat( $products );
			}

		    // add scripts
		    if( ! has_action( 'wp_footer', array( $this, 'add_scripts' ) ) ) {
			    add_action( 'wp_footer', array( $this, 'add_scripts' ) );
		    }
		    
		    // get args
		    $args = $this->_vars( $products );
		    $args['fixed'] = ! empty( $products );
		    // change args
		    $args['iframe'] = $this->is_iframe;

			ob_start();
		        wc_get_template( 'yith-compare-table.php', $args, '', YITH_WOOCOMPARE_TEMPLATE_PATH . '/' );

		    $html = ob_get_clean();

		    // reset query
		    wp_reset_query();
		    wp_reset_postdata();

		    return $html;
	    }

	    /**
	     * Share compare table
	     *
	     * @since 2.0.0
	     * @author Francesco Licandro <francesco.licandro@yithemes.com>
	     */
	    public function social_share() {

		    $social = get_option( 'yith-woocompare-share-socials', array() );

		    if( ! $this->share_enabled || empty( $social ) || empty( $this->products_list ) || isset( $_REQUEST['yith_compare_prod'] ) )
			    return;

		    // check for page/popup
		    $toShow  = get_option( 'yith-woocompare-share-in-page', 'yes' ) == 'yes' && $this->is_iframe != 'yes';
		    ! $toShow && $toShow = get_option( 'yith-woocompare-share-in-popup', 'yes' ) == 'yes';

		    if( ! $toShow ) {
			    return;
		    }

		    $products = implode(',', $this->products_list );
		    $products_base64 = base64_encode( $products );

		    // get facebook image
		    $facebook_image = get_option( 'yith_woocompare_facebook_image', '' );

		    $args = array(
			    'socials'   => $social,
			    'share_title' => get_option( 'yith-woocompare-share-title', __( 'Share on:', 'yith-woocommerce-compare' ) ),
		        'share_link_url' => esc_url_raw( add_query_arg( 'yith_compare_prod', $products_base64, $this->get_compare_page_url() ) ),
		        'share_link_title' => urlencode( get_option( 'yith_woocompare_socials_title', __( 'My Compare', 'yith-woocommerce-compare' ) ) ),
		        'share_summary' => urlencode( str_replace( '%compare_url%', '', get_option( 'yith_woocompare_socials_text', '' ) ) ),
			    'facebook_appid' => get_option( 'yith_woocompare_facebook_appid', '' ),
			    'facebook_image' => $facebook_image
		    );

		    wc_get_template( 'yith-compare-share.php', $args, '', YITH_WOOCOMPARE_TEMPLATE_PATH . '/' );
	    }

	    /**
	     * Add category filter for compare page
	     *
	     * @since 2.0.0
	     * @author Francesco Licandro
		 * @param array $products The products on compare table.
		 * @param boolean $fixed True it is a fixed table, false otherwise.
	     */
	    public function print_filter_by_cat( $products, $fixed ) {

		    if( ! $this->compare_by_cat || $fixed ) {
		        return;
		    }

		    // get all categories
		    $all_cat = $this->get_product_categories( $this->products_list );
		    // let's third part filter categories
		    $all_cat = apply_filters( 'yith_woocompare_product_categories_table_filter', $all_cat, $this->products_list );

		    if( empty( $all_cat ) ) {
			    return;
		    }

		    // set data for compare share
		    $data = isset( $_REQUEST['yith_compare_prod'] ) ? 'data-product_ids="' . $_REQUEST['yith_compare_prod'] . '"' : '';
		    ?>

		    <div id="yith-woocompare-cat-nav">
			    <h3><?php echo esc_html ( get_option( 'yith-woocompare-categories-filter-title', esc_html__( 'Category Filter', 'yith-woocommerce-compare' ) ) ) ?></h3>

		        <ul class="yith-woocompare-nav-list" data-iframe="<?php echo wp_kses_post( $this->is_iframe ); ?>" <?php echo wp_kses_post( $data ); ?>>

		        <?php
		        foreach( $all_cat as $cat_id => $cat_name ) :
		            $active = in_array( $cat_id, (array) $this->current_cat ) ? true : false;
		        ?>
			        <li>
			        <?php if( $active ) : ?>
				        <span class="active"><?php echo esc_html( $cat_name ); ?></span>
			        <?php else : ?>
				        <a href="<?php echo esc_url_raw( add_query_arg( 'yith_compare_cat', $cat_id ) ) ?>" data-cat_id="<?php echo esc_attr( $cat_id ); ?>"><?php echo esc_html( $cat_name ); ?></a>
			        <?php endif; ?>
			        </li>

		        <?php endforeach; ?>

		        </ul>
		    </div>

			<?php
	    }

	    /**
	     * Ajax compare table filter for category
	     *
	     * @since 2.0.0
	     * @author Francesco Licandro
	     */
	    public function yith_woocompare_filter_by_cat_ajax() {

		    if( ! isset( $_REQUEST['yith_compare_cat'] ) )
			    die();


		    if( $this->is_iframe == 'yes' ){
			    header('Content-Type: text/html; charset=utf-8');
			    $this->compare_table_html();
		    }
		    else {
		        echo do_shortcode( '[yith_woocompare_table]' );
	        }

		    die();
	    }

	    /**
	     * Add related product in compare table
	     *
	     * @since 2.0.0
	     * @author Francesco Licandro
		 * @param array $products The products on compare table.
		 * @param boolean $fixed True it is a fixed table, false otherwise.
	     */
        public function related_product( $products, $fixed ) {

	        if( ! $this->related_enabled || empty( $this->products_list ) || $fixed ) {
		        return;
	        }
	        // check for page/popup
            if( ( $this->is_iframe === 'yes' && get_option( 'yith-woocompare-related-in-popup' ) === 'yes' ) || ($this->is_iframe == 'no' && get_option( 'yith-woocompare-related-in-page', 'yes' ) == 'yes') ){
                $toShow = true;
            }else{
                $toShow = false;
            }
            
	        if( ! $toShow ) {
		        return;
	        }

	        // filter product
	        $related = array();

			foreach( $this->products_list as $product_id ) {
				if( function_exists( 'wpml_object_id_filter' ) ) {
					$product_id = wpml_object_id_filter( $product_id, 'product', false );
					if( ! $product_id ) {
						continue;
					}
				}

				$product = wc_get_product( $product_id );
				if( ! $product )
					continue;

				$product_id = yit_get_prop( $product, 'id', true );
				$current_related = function_exists( 'wc_get_related_products' ) ? wc_get_related_products( $product_id ) : $product->get_related();
				$related = array_merge( $current_related, $related );
			}
	        // remove duplicate
	        $related = array_unique( $related );
	        // remove products already in compare
	        $related = array_diff( $related, $this->products_list );
	        // filter related by cat
	        $related = $this->exclude_product_from_list( $related );


	        // exit if related is empty
	        if( empty( $related ) )
		        return;

	        // set args
	        $args = array(
		        'iframe'        => $this->is_iframe,
		        'products'      => $related,
		        'related_title' => get_option( 'yith-woocompare-related-title', __( 'Related Products', 'yith-woocommerce-compare' ) )
	        );

	        // the template
	        wc_get_template( 'yith-compare-related.php', $args, '', YITH_WOOCOMPARE_TEMPLATE_PATH . '/' );
        }

	    /**
	     * Filter remove action for compare page
	     *
	     * @since 2.0.0
	     * @author Francesco Licandro
	     */
	    public function remove_product_ajax_premium() {

		    if( $this->is_iframe != 'yes' && ! ( isset( $_REQUEST['responseType'] ) && $_REQUEST['responseType'] == 'product_list' ) ){

			    echo do_shortcode( '[yith_woocompare_table]' );

			    die();
		    }
	    }

	    /**
	     * Added related product to compare
	     *
	     * @since 2.0.0
	     * @author Francesco Licandro
	     */
        public function added_related_to_compare(){

	        if( isset( $_REQUEST['is_related'] ) && $_REQUEST['is_related'] != 'false' ){

		        if( $this->is_iframe == 'yes' ) {
			        header('Content-Type: text/html; charset=utf-8');
		            $this->compare_table_html();
			    }
		        else {
			        echo do_shortcode( '[yith_woocompare_table]' );
		        }

		        die();
	        }
        }

		/**
		 * Add different class to let's compare products attributes
		 *
		 * @access public
		 * @since 2.0.3
		 * @param array $products_list
		 * @return array
		 * @author Francesco Licandro
		 */
		public function add_different_class( $products_list ) {

			if( get_option( 'yith_woocompare_highlights_different', 'no' ) != 'yes' || empty( $products_list ) || count( $products_list ) < 2 ) {
				return array();
			}

			$prev_value = array();
			$different_value = array();


			foreach( $products_list as $key => $product ) {

				$value = $product->fields;

				// remove unused fields
				unset(
					$value['description'],
					$value['stock']
				);

				$new_value = array();
				foreach( $value as $name => $val ) {

					if( $name == 'price' ) {
						$new_value[$name] = $val;
						continue;
					}

					$val = strtolower( $val );
					$val = explode(',', $val );
					foreach( $val as $index => $elem ){
						$val[ $index ] = trim( $elem );
					}
					natsort( $val );
					$val = implode(',', $val );

					$new_value[$name] = $val;
				}

				// if prev value is not empty compare with current and save difference
				if( ! empty( $prev_value ) ) {
					$diff = array_diff_assoc( $prev_value, $new_value );
					$different_value = array_merge( $different_value, $diff );
				}

				// save current value
				$prev_value = $new_value;
			}

			// if no difference return list
			if( empty( $different_value ) ){
				return array();
			}

			return array_keys( $different_value );
		}

		/**
		 * Add style for YITH Themes Compatibility
		 *
		 * @since 2.0.4
		 * @author Francesco Licandro
		 */
		public function add_styles_themes_and_custom(){

			echo '<style>' . yith_woocompare_user_style() . '</style>'; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped

			// get theme and woocommerce assets
			$assets_yit = class_exists( 'YIT_Asset' ) ? YIT_Asset()->get() : array();
			$assets_yit = isset( $assets_yit['style'] ) ? $assets_yit['style'] : array();
			$assets_wc = class_exists( 'WC_Frontend_Scripts' ) ? WC_Frontend_Scripts::get_styles() : array();
			! is_array( $assets_wc ) && $assets_wc = array();
			$assets = array_merge( $assets_yit, $assets_wc );
			$to_include = apply_filters( 'yith_woocompare_popup_assets', array(
					"google-fonts",
					"font-awesome",
					"theme-stylesheet",
					"woocommerce-general",
					"yit-layout",
					"cache-dynamics",
					"custom",
			) );

			if( function_exists( 'yith_proteo_scripts' ) ) {
				yith_proteo_scripts();
				// add also inline customize style if any
				function_exists( 'yith_proteo_inline_style' ) && yith_proteo_inline_style();
			}

			// first if is child include parent css
			if( is_child_theme() && function_exists( 'yit_enqueue_parent_theme_style' ) ) {
				yit_enqueue_parent_theme_style();
			}

			foreach( $to_include as $css ) {
				if( ! isset( $assets[$css]["src"] ) ){
					continue;
				}
				echo "<link rel=\"stylesheet\" href=" . esc_attr( $assets[$css]["src"] ) . " type=\"text/css\" media=\"all\">";
			}

		}

	    /**
	     * WooCommerce Product Bundle Compatibility
	     *
	     * @since 2.0.6
	     * @author Francesco Licandro
	     */
	    public function wc_bundle_compatibility(){
		    // remove description from bundled items
		    remove_action( 'wc_bundles_bundled_item_details', 'wc_bundles_bundled_item_description', 20 );
		    remove_action( 'wc_bundles_bundled_item_details', 'wc_bundles_bundled_item_product_details', 25 );
		    add_action( 'wc_bundles_bundled_item_details', array( $this, 'print_bundled_item_price' ), 25, 2 );
	    }

	    /**
	     * Print bundled single item price
	     *
	     * @since 2.0.6
	     * @param object $bundled_item
	     * @param object $product
	     * @author Francesco Licandro
	     */
	    public function print_bundled_item_price( $bundled_item, $product ) {

		    if( ! function_exists( 'WC_PB' ) ) {
			    return;
		    }

		    wc_get_template( 'single-product/bundled-item-price.php', array(
				    'bundled_item' => $bundled_item
		    ), false, WC_PB()->woo_bundles_plugin_path() . '/templates/' );
	    }
	    
	    /**
	     * Get excluded categories from plugin option
	     * 
	     * @since 2.1.0
	     * @author Francesco Licandro
	     * @return array
	     */
	    public function get_excluded_categories(){
		    $excluded_cat = get_option( 'yith_woocompare_excluded_category', array() );
		    ! is_array( $excluded_cat ) && $excluded_cat = explode( ',', $excluded_cat );
		    // remove empty values and return
		    return array_filter( $excluded_cat );
	    }
	    
	    /**
	     * Premium add product json args
	     * 
	     * @author Francesco Licandro
	     * @since 2.1.0
	     * @param array $json
	     * @return array
	     */
	    public function premium_add_product_action_json( $json ){
		    $json['only_one'] = ( get_option( 'yith_woocompare_open_after_second', 'no' ) == 'yes' && count( $this->products_list ) <= 1 );
		    
		    return $json;		  
	    }
	    
	    /**
	     * Add clear all button on compare table
	     * 
	     * @since 2.1.0
	     * @author Francesco Licandro
		 * @param array $products The products on compare table.
		 * @param boolean $fixed True it is a fixed table, false otherwise.
	     */
	    public function add_clear_all_button( $products, $fixed ){

	    	if( get_option( 'yith_woocompare_show_clear_all_table', 'no' ) != 'yes' || $fixed ) {
	    		return;
			}

			$label = get_option( 'yith_woocompare_label_clear_all_table', __( 'Clear all', 'yith-woocommerce-compare' ) );

		    $html = '<div class="compare-table-clear">';
			$html .= '<a href="' . $this->remove_product_url( 'all' ) . '" data-product_id="all" class="button yith_woocompare_clear" rel="nofollow">' . esc_html( $label ) . '</a>';
		    $html .= '</div>';

		    echo apply_filters( 'yith_woocompare_table_clear_all', $html ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
		}
	    
	    /**
	     * Dequeue footer scripts from compare popup
	     * 
	     * @since 2.2.0
	     * @author Francesco Licandro
	     * @access public
	     */
	    public function dequeue_footer_scripts(){
		    if( wp_script_is( 'responsive-theme', 'enqueued' ) ) {
			    wp_dequeue_script( 'responsive-theme' );
		    }
            if( wp_script_is( 'woodmart-libraries-base', 'enqueued' ) ) {
                wp_dequeue_script( 'woodmart-libraries-base' );
            }
	    }

	    /**
         * Counter compare items shortcode
         *
         * @since 2.3.2
         * @author Francesco Licandro
         * @return string
         * @param array $atts Array of shortcode attributes
         */
	    public function counter_compare_shortcode( $atts ) {
            $args = shortcode_atts( array(
                'type'      => 'text',
                'show_icon' => 'yes',
                'text'      => '',
                'icon'      => ''
            ), $atts );

            $c = count( $this->products_list );
            // builds template arguments
            $args['items']          = $this->products_list;
            $args['items_count']    = $c;
            ! $args['icon'] && $args['icon'] = YITH_WOOCOMPARE_ASSETS_URL . '/images/compare-icon.png';
            ! $args['text'] && $args['text'] = _n( '{{count}} product in compare', '{{count}} products in compare', $c, 'yith-woocommerce-compare' );
            // add count in text
            $args['text_o']         = $args['text'];
            $args['text']           = str_replace( '{{count}}', $c, $args['text'] );

            $args = apply_filters( 'yith_woocompare_shortcode_counter_args', $args );

            ob_start();
            wc_get_template( 'yith-compare-counter.php', $args, '', YITH_WOOCOMPARE_TEMPLATE_PATH . '/' );

            return ob_get_clean();
        }

        /**
         * Add limit error messages in compare table if any
         *
         * @since 2.3.2
         * @author Francesco Licandro
         */
        public function add_error_limit_message(){

            if( ! $this->limit_reached ) {
                return;
            }

            $message    = apply_filters( 'yith_woocompare_limit_reached_message', __( 'You have reached the maximum number of products for compare table.', 'yith-woocommerce-compare' ) );
            echo '<div class="yith-woocompare-error"><p>'. wp_kses_post( $message ).'</p></div>';
        }


        /**
         * Compatibility with YITH WooCommerce Colors and Labels Variations (show fields for single variations)
         * @param $value
         * @param $product
         * @param $field
         * @return string
         */
        public function yith_woocompare_set_single_variation_field_value( $value, $product, $field ){

            if( $product instanceof WC_Product_Variation ){

                switch ( $field ){
                    case 'price':
                        $value = $product->get_price_html();
                        break;

                    case 'description':
                        $value = $product->get_description();
                        break;

                    default:
                        break;
                }
            }

            return $value;
        }
    }
}