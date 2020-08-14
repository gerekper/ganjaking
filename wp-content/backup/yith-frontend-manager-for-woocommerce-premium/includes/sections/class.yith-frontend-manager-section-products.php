<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined ( 'ABSPATH' ) ) {
    exit( 'Direct access forbidden.' );
}

if( ! class_exists( 'YITH_Frontend_Manager_Section_Products' ) ) {

    class YITH_Frontend_Manager_Section_Products extends YITH_WCFM_Section {

        public $WC_Admin_Post_Types;

        /**
         * Constructor method
         *
         * @return \YITH_Frontend_Manager_Section
         * @since 1.0.0
         */
        public function __construct() {
            $this->id                   = 'products';
            $this->_default_section_name = _x( 'Products', '[Frontend]: Dashboard menu item', 'yith-frontend-manager-for-woocommerce' );

            $this->_subsections = apply_filters( 'yith_wcfm_products_subsections', array(
                    'products' => array(
                        'slug' => $this->get_option( 'slug', $this->id . '_list' , 'list' ),
                        'name' => __( 'All Products', 'yith-frontend-manager-for-woocommerce' ),
                        'add_delete_script' => true
                    ),

                    'product' => array(
                        'slug' => $this->get_option( 'slug', $this->id . '_product', 'product' ),
                        'name' => __( 'Add Product', 'yith-frontend-manager-for-woocommerce' ),
                    ),
                ),
                $this->id
            );

            $this->deps();

            add_action( 'init', array( $this, 'add_support_for_color_and_label_variations' ), 20 );
            add_filter( 'woocommerce_product_title', array( $this, 'default_title' ), 10, 2 );
            add_filter( 'woocommerce_admin_meta_boxes_variations_count', array( $this, 'variations_count' ) );

            parent::__construct();
        }

        /* === SECTION METHODS === */

        /**
         * Required files for this section
         *
         * @author Andrea Grillo    <andrea.grillo@yithemes.com>
         * @return void
         * @since 1.0.0
         */
        public function deps(){
	        if( ! class_exists( 'WP_Posts_List_Table' ) ){
		        require_once( ABSPATH . 'wp-admin/includes/class-wp-posts-list-table.php' );
	        }

	        if( YITH_Frontend_Manager()->is_wc_3_3_or_greather && ! class_exists( 'WC_Admin_List_Table_Products' ) ){
		        require_once( WC()->plugin_path() . '/includes/admin/list-tables/class-wc-admin-list-table-products.php' );
            }

	        if( ! class_exists( 'WC_Admin_Post_Types' ) ){
		        require_once  ( WC()->plugin_path() . '/includes/admin/class-wc-admin-post-types.php' );
	        }

            if( class_exists( 'YITH_WCCL' ) && ! class_exists( 'YITH_WCCL_Admin' ) ){
                require_once ( YITH_WCCL_DIR . 'includes/class.yith-wccl-admin.php' );
            }

	        require_once( YITH_WCFM_LIB_PATH . 'class.yith-frontend-manager-products-list-table.php' );
            require_once( ABSPATH . 'wp-admin/includes/meta-boxes.php' );
        }

        /**
         * Section styles and scripts
         *
         * Override this method in section class to enqueue
         * particular styles and scripts only in correct
         * section
         *
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @return void
         * @since  1.0.0
         */
        public function enqueue_section_scripts() {
            /* === Styles === */
            wp_enqueue_style( 'yith-wcfm-products', YITH_WCFM_URL . 'assets/css/products.css', array( 'woocommerce_admin_styles' ), YITH_WCFM_VERSION );
            wp_enqueue_style( 'wp-edit' );
            wp_enqueue_style( 'jquery-ui-style' );
	        wp_enqueue_style( 'wp-admin' );

            /* === Scripts === */
	        wp_enqueue_script( 'wp-post' );
            wp_enqueue_script( 'wp-postbox' );
            wp_enqueue_script( 'woocommerce_admin' );

	        wp_enqueue_script( 'woocommerce_quick-edit' );
	        wp_enqueue_script( 'wc-admin-product-meta-boxes' );
	        wp_enqueue_script( 'wc-admin-variation-meta-boxes' );

            wp_enqueue_media();

	        wp_enqueue_script( 'wp-color-picker' );
	        wp_enqueue_script( 'jquery-ui-dialog' );

            $suffix     = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) || ! empty( $_GET['yith_debug'] ) ? '' : '.min';
            wp_enqueue_script( 'yith-frontend-manager-product-js', YITH_WCFM_URL . "assets/js/yith-frontend-manager-product{$suffix}.js", array( 'jquery' ), YITH_WCFM_VERSION, true );

            do_action( 'yith_wcfm_products_enqueue_scripts' );
        }

            /**
         * Print shortcode function
         *
         * @author Andrea Grillo    <andrea.grillo@yithemes.com>
         * @return void
         * @since 1.0.0
         */
        public function print_shortcode( $atts = array(), $content = '', $tag ) {
            $section = $subsection = '';
            if ( ! empty( $atts) ) {
                $section = ! empty( $atts['section'] ) ? $atts['section'] : $this->id;
                $subsection = $this->id;
                if( ! empty( $atts['subsection'] ) && 'products' != $atts['subsection'] && ! in_array( $atts['subsection'], $this->_subsections ) ){
                    $subsection = $atts['subsection'];
                }
            }

            $atts = array(
                'section_obj'    => $this,
                'product_status' => YITH_Frontend_Manager_Section_Products::get_product_status(),
                'section'        => $section,
                'subsection'     => $subsection
            );

            if( apply_filters( 'yith_wcfm_print_product_section', true, $subsection, $section, $atts ) ){
                $this->print_section( $subsection, $section, $atts );
            }

            else {
                do_action( 'yith_wcfm_print_section_unauthorized', $this->id );
            }
        }

        /**
         * get the edit post link for frontend
         *
         * @author Andrea Grillo    <andrea.grillo@yithemes.com>
         * @return string post link
         * @since 1.0.0
         */
        public static function get_edit_product_link( $product_id ){
            return add_query_arg( array( 'product_id' => $product_id, ), yith_wcfm_get_section_url( 'products', 'product' ) );
        }

        /**
         * Save a product
         *
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @return void
         * @since 1.0.0
         */
        public static function save_product( $array ) {
	        /* === Check Special Value === */
	        $special_value = array(
		        '_virtual',
		        '_sold_individually',
		        '_manage_stock',
		        'comment_status'
	        );

	        foreach ( $special_value as $k ){
		        $array[ $k ] = isset( $array[ $k ] ) ? true : false;
	        }

	        $default = array(
		        'product-type'           => 'simple',
		        '_visibility'            => 'visible',
		        'menu_order'             => 0,
		        'comment_status'         => 0,
		        'sold_individually'      => 0,
		        '_regular_price'         => 0,
		        '_sale_price'            => 0,
		        '_sku'                   => '',
		        '_weight'                => '',
		        '_length'                => '',
		        '_width'                 => '',
		        '_height'                => '',
		        'product_shipping_class' => false,
		        'upsell_ids'             => array(),
		        'crosssell_ids'          => array(),
		        '_low_stock_amount'      => '',
		        //Grouped Product
		        'grouped_products'       => array(),
		        //External Products
		        '_product_url'           => '',
		        '_button_text'           => '',
	        );

	        $array = wp_parse_args( $array, $default );
	        $product_type = $array['product-type'];
	        $post_id      = isset( $array['id'] ) ? $array['id'] : 0;
	        $is_new       = ! is_wp_error( $post_id ) && $post_id > 0 ? true : false;
	        $product      = null;

	        /**
	         * Set the product type before create the new instance of it.
	         */
	        if( ! is_wp_error( $post_id ) && $post_id > 0 ){
		        //$post_id should not be zero
		        wp_set_object_terms( $post_id, $product_type, 'product_type' );
		        $product = wc_get_product( $post_id );
            }

	        $product = apply_filters( 'yith_wcfm_product_object_save_action', $product, $product_type, $is_new );

	        if( $product instanceof WC_Product ){
		        $product_fields = array(
			        //Post Fields
			        'post_title'             => 'name',
			        'post_content'           => 'description',
			        'post_excerpt'           => 'short_description',
			        'post_status'            => 'status',
			        'comment_status'         => 'reviews_allowed',
			        'menu_order'             => 'menu_order',
			        //General
			        '_visibility'            => 'catalog_visibility',
			        '_virtual'               => 'virtual',
			        '_regular_price'         => 'regular_price',
			        '_sale_price'            => 'sale_price',
			        '_sale_price_dates_from' => 'date_on_sale_from',
			        '_sale_price_dates_to'   => 'date_on_sale_to',
			        '_sku'                   => 'sku',
			        '_sold_individually'     => 'sold_individually',
			        '_tax_status'            => 'tax_status',
			        '_tax_class'             => 'tax_class',
			        //Shipping
			        '_weight'                => 'weight',
			        '_length'                => 'length',
			        '_width'                 => 'width',
			        '_height'                => 'height',
			        //Advanced
			        '_purchase_note'         => 'purchase_note',
			        'upsell_ids'             => 'upsell_ids',
			        'crosssell_ids'          => 'cross_sell_ids',
		        );

		        $apply_callback = array(
			        'date_on_sale_from' => 'strtotime',
			        'date_on_sale_to'   => 'strtotime'
		        );

		        // Product Data
	            foreach( $product_fields as $post_field => $product_field ){
	                if( isset( $apply_callback[ $product_field ] ) ){
	                    $callback = $apply_callback[ $product_field ] ;
		                $array[ $post_field ] = function_exists( $callback ) ? $callback( $array[ $post_field ] ) : $array[ $post_field ];
                    }

	                if( isset( $array[ $post_field ] ) ){
                        $setter = "set_{$product_field}";
                        $product->$setter( $array[ $post_field ] );
                    }
		        }

		        // Stock Management
		        $manage_stock           = $array['_manage_stock'];
		        $stock_quantity         = $array['_stock'];
		        $allow_backorder        = $array['_backorders'];
		        $low_stock_amount       = $array['_low_stock_amount'];
		        $allow_stock_management = 'yes' == $manage_stock;
		        $stock_status           = isset( $array['_stock_status'] ) ? $array['_stock_status'] : 'instock';

		        if ( $allow_stock_management && $stock_quantity <= get_option( 'woocommerce_notify_no_stock_amount', 0 ) && 'no' !== $allow_backorder ) {
			        $stock_status = 'onbackorder';
			        // If we are stock managing and we don't have stock, force out of stock status.
		        }

                elseif ( $allow_stock_management && $stock_quantity <= get_option( 'woocommerce_notify_no_stock_amount', 0 ) && 'no' === $allow_backorder ) {
			        $stock_status = 'outofstock';
			        // If the stock level is changing and we do now have enough, force in stock status.
		        }

                elseif ( $allow_stock_management && $stock_quantity > get_option( 'woocommerce_notify_no_stock_amount', 0 ) ) {
			        $stock_status = 'instock';
		        }

		        else {
			        if( 'variable' == $product_type ){
				        $_product = wc_get_product( $post_id );
				        if( $_product instanceof WC_Product_Variable ){
					        $stock_status = $_product->child_is_in_stock() ? 'instock' : 'outofstock';
				        }
			        }

			        $stock_quantity = '';
			        $allow_backorder = 'no';
		        }

		        $product->set_manage_stock( $manage_stock );
		        $product->set_stock_quantity( $stock_quantity );
		        $product->set_backorders( $allow_backorder );
		        $product->set_stock_status( $stock_status );
		        $product->set_low_stock_amount( $low_stock_amount );

		        // Shipping
		        if( ! empty( $array['product_shipping_class'] ) && is_numeric( $array['product_shipping_class'] ) ){
			        wp_set_object_terms( $post_id, absint( $array['product_shipping_class'] ), 'product_shipping_class', false );
		        }

		        // Update price if on sale
		        $date_from     = (string) isset( $_POST['_sale_price_dates_from'] ) ? wc_clean( $_POST['_sale_price_dates_from'] ) : '';
		        $date_to       = (string) isset( $_POST['_sale_price_dates_to'] ) ? wc_clean( $_POST['_sale_price_dates_to'] ) : '';
		        $regular_price = (string) isset( $_POST['_regular_price'] ) ? wc_clean( $_POST['_regular_price'] ) : '';
		        $sale_price    = (string) isset( $_POST['_sale_price'] ) ? wc_clean( $_POST['_sale_price'] ) : '';

		        if ( '' !== $sale_price && '' === $date_to && '' === $date_from ) {
			        $product->set_price( wc_format_decimal( $sale_price ) );
		        }

                elseif ( '' !== $sale_price && $date_from && strtotime( $date_from ) <= strtotime( 'NOW', current_time( 'timestamp' ) ) ) {
	                $product->set_price( wc_format_decimal( $sale_price ) );
		        }

		        else {
			        $product->set_price( '' === $regular_price ? '' : wc_format_decimal( $regular_price ) );
		        }

		        if ( $date_to && strtotime( $date_to ) < strtotime( 'NOW', current_time( 'timestamp' ) ) ) {
			        $product->set_price( '' === $regular_price ? '' : wc_format_decimal( $regular_price ) );
		        }

		        // Thumbnail
		        if ( isset( $array['attach_id'] ) && $array['attach_id'] > 0 ) {
			        $attach_id = $array['attach_id'];
			        set_post_thumbnail( $post_id, $attach_id );
		        }

		        else {
			        delete_post_thumbnail( $post_id );
		        }
		        // Gallery
		        if( ! empty( $array['product_gallery'] ) ) {
			        $product_gallery_ids = implode( ',', $array['product_gallery'] );
			        $product->set_gallery_image_ids( $product_gallery_ids );
		        }

		        //Save Taxonomy
		        if( ! empty( $_POST['tax_input'] ) ){
			        $tax_input = $_POST['tax_input'];
			        $taxonomies = array_keys( $tax_input );
			        foreach ( $taxonomies as $taxonomy ){
				        if( ! empty( $tax_input[ $taxonomy ] ) && is_array( $tax_input[ $taxonomy ] ) ){
					        $terms = array();
					        foreach( $tax_input[ $taxonomy ] as $tt_id ){
						        if( ! empty( $tt_id )  ){
							        $term = get_term_by( 'id', $tt_id, $taxonomy, ARRAY_A );
							        if( ! is_wp_error( $term ) ){
								        $terms[] = $term['slug'];
							        }
						        }
					        }
					        wp_set_object_terms( $post_id, $terms, $taxonomy, false );
				        }
			        }
		        }

		        //Downlodable
                $downlodable = isset( $array['_downloadable'] ) ? true : false;
		        $product->set_downloadable( $downlodable );

		        if( $downlodable ){
			        $downloads      = array();
			        $file_names     = isset( $_POST['_wc_file_names'] ) ? $_POST['_wc_file_names'] : array();
			        $file_urls      = isset( $_POST['_wc_file_urls'] ) ? $_POST['_wc_file_urls'] : array();
			        $file_hashes    = isset( $_POST['_wc_file_hashes'] ) ? $_POST['_wc_file_hashes'] : array();

			        if ( ! empty( $file_urls ) ) {
				        $file_url_size = sizeof( $file_urls );

				        for ( $i = 0; $i < $file_url_size; $i ++ ) {
					        if ( ! empty( $file_urls[ $i ] ) ) {
						        $downloads[] = array(
							        'name'          => wc_clean( $file_names[ $i ] ),
							        'file'          => wp_unslash( trim( $file_urls[ $i ] ) ),
							        'previous_hash' => wc_clean( $file_hashes[ $i ] ),
						        );
					        }
				        }
			        }

			        $product->set_downloads( $downloads );

			        if( ! empty( $array['_download_limit'] ) ){
			            $product->set_download_limit( $array['_download_limit'] );
			        }

			        if( ! empty( $array['_download_expiry'] ) ){
			            $product->set_download_expiry( $array['_download_expiry'] );
			        }
		        }

		        $attributes = WC_Meta_Box_Product_Data::prepare_attributes();
		        $product->set_attributes( $attributes );

		        //Is External Product
		        if( $product instanceof WC_Product_External ){
			        if( ! empty( $array['_product_url'] ) ){

			        }

			        if( ! empty( $array['_button_text'] ) ){

			        }

			        $product->set_product_url( $array['_product_url'] );
			        $product->set_button_text( $array['_button_text'] );
		        }

		        //Is Grouped Product
		        if( $product instanceof WC_Product_Grouped ){
		            $product->set_children( $array['grouped_products'] );
                }

		        if( ! empty( $_POST['meta'] ) ){
			        $meta = $_POST['meta'];
			        foreach( $meta as $meta_id => $m ) {
				        $has_setter = is_callable( array( $product, 'set_' . $m['key'] ) );
				        if ( $has_setter ) {
					        $setter = 'set_' . $m['key'];
					        $product->$setter( $m['value'] );
				        } else {
					        $product->update_meta_data( $m['key'], $m['value'], $meta_id );
				        }
			        }
		        }

		        /**
		         * Save product properties
		         */
                $product->save();

                $post   = get_post( $post_id );
		        $action = $is_new ? 'created' : 'updated';

		        do_action( "yith_wcfm_product_{$action}", $post_id, $post, $product );
		        do_action( "yith_wcfm_product_save", $post_id, $post, $product );

		        return $post_id;
            }

            return false;
        }

        public static function printProductsIdSelect2( $name , $values ){

            ?>

            <input type="hidden" class="wc-product-search" style="width: 350px;" id="<?php echo $name; ?>" name="<?php echo $name; ?>"
                   data-placeholder="<?php esc_attr_e( 'Applied to...', 'yith-woocommerce-product-add-ons' ); ?>"
                   data-action="woocommerce_json_search_products"
                   data-multiple="true"
                   data-exclude=""
                   data-selected="<?php

                   $product_ids = array_filter( array_map( 'absint', explode( ',', $values ) ) );
                   $json_ids    = array();

                   foreach ( $product_ids as $product_id ) {
                       $product = wc_get_product( $product_id );
                       if ( is_object( $product ) ) {
                           $json_ids[ $product_id ] = wp_kses_post( html_entity_decode( $product->get_formatted_name(), ENT_QUOTES, get_bloginfo( 'charset' ) ) );
                       }
                   }

                   echo esc_attr( json_encode( $json_ids ) );
                   ?>"
                   value="<?php echo implode( ',', array_keys( $json_ids ) ); ?>"
            />

            <?php

        }

        /**
         * Get select for product status
         *
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @since 1.0
         * @return array product allowed status
         */
        public static function get_product_status(){
            $product_status = array(
                'publish'   => __('Published', 'yith-frontend-manager-for-woocommerce'),
                'pending'   => __('Pending review', 'yith-frontend-manager-for-woocommerce'),
                'draft'     => __('Draft', 'yith-frontend-manager-for-woocommerce')
            );
            return apply_filters( 'yith_wcfm_allowed_product_status', $product_status );
        }

        /**
         * Delete product post type
         *
         * @param $product_id
         * @return bool
         *
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @since 1.0.0
         */
        public static function delete( $product_id ){
	        $product      = wc_get_product( $product_id );
	        $force_delete = apply_filters( 'yith_wcfm_force_delete_product', false );
	        $check        = $product instanceof WC_Product ? $product->delete( $force_delete ) : false;

            if( $check ){
                $message = _x( 'Product deleted successfully', '[Frontend]: user message', 'yith-frontend-manager-for-woocommerce' );
                $type    = 'success';
            }

            else {
                $message = _x( 'Product does not exist', '[Frontend]: user message', 'yith-frontend-manager-for-woocommerce' );
                $type = 'error';
            }

            wc_add_notice( $message, $type );
        }

	    /**
	     * Support for YITH WooCommerce color and label variations
	     */
        public function add_support_for_color_and_label_variations(){
	        if( function_exists( 'YITH_WCCL_Admin' ) && ! empty( YITH_Frontend_Manager()->gui ) ){
		        $yith_wccl_admin = YITH_WCCL_Admin();
		        if( ! empty( $yith_wccl_admin ) ){
			        // product attribute taxonomies
			        remove_action( 'init', array( $yith_wccl_admin, 'attribute_taxonomies' ) );
			        add_action( 'init', array( $yith_wccl_admin, 'attribute_taxonomies' ), 25 );

			        // enqueue style and scripts
			        remove_action( 'admin_enqueue_scripts', array( $yith_wccl_admin, 'enqueue_scripts' ) );
			        add_action( 'wp_enqueue_scripts', array( $yith_wccl_admin, 'enqueue_scripts' ), 20 );

			        add_filter( 'yith_wccl_enqueue_admin_scripts', '__return_true' );
			        add_filter( 'yith_wccl_add_product_add_terms_form', '__return_false' );

			        // add term directly from product variation
			        remove_action( 'admin_footer', array( $yith_wccl_admin, 'product_option_add_terms_form' ) );
			        add_action( 'yith_wcmf_section_product', array( $yith_wccl_admin, 'product_option_add_terms_form' ) );
		        }
	        }
        }

	    /**
	     * Print an admin notice
	     *
	     * @author Andrea Grillo <andrea.grillo@yithemes.com>
	     * @since 1.3.3
	     * @return void
	     * @use admin_notices hooks
	     */
	    public function show_wc_notice( $message = 'success' ) {
            switch( $message ){
                case 'success':
                    $message = __( 'Product Saved', 'yith-frontend-manager-for-woocommerce' );
                    $type = 'success';
                    break;

                case 'error':
                    $message = __( 'Unable to save product', 'yith-frontend-manager-for-woocommerce' );
                    $type = 'error';
                    break;
            }

		    if( ! empty( $message ) ) {
			    wc_print_notice( $message, $type );
		    }
	    }

	    /**
         * Filter the default post title
         *
	     * @param $post_title string default post tile
	     * @param $post mixed current default post object
	     *
	     * @return string Default post title for current default post
	     */
	    public function default_title( $post_title, $post ){
	        $post_title_copy = strtolower( str_replace( '-', ' ', $post_title ) );
	        if( ! is_admin() && ! is_ajax() && ! empty( YITH_Frontend_Manager()->gui ) && YITH_Frontend_Manager()->gui->is_main_page() && $post instanceof WC_Product && 'auto draft' == $post_title_copy ){
	            $post_title = '';
	        }

	        return $post_title;
        }

        /**
		 * Filter the variations count for current product on Front
		 *
		 * @param $variations_count int current count value
		 *
		 * @return int Filtered value
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
        public function variations_count( $variations_count ){
        	if( ! empty( $_GET['product_id'] )  ){
        		global $wpdb;
				$postID = $_GET['product_id'];
				$variations_count       = absint( $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(ID) FROM $wpdb->posts WHERE post_parent = %d AND post_type = 'product_variation' AND post_status IN ('publish', 'private')", $postID ) ) );
			}
        	return $variations_count;
		}
    }
}
