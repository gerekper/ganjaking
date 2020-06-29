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

if( ! class_exists( 'YITH_Frontend_Manager_Section_Products_Premium' ) ) {

    class YITH_Frontend_Manager_Section_Products_Premium extends YITH_Frontend_Manager_Section_Products {

        /**
         * Construct
         */
        public function __construct() {
            //Section init
            /**
             * Normally Multi Vendor plugin are ready on init hook at priority 15.
             * This method are fired at init priority 50
             * In this wya I can customize each section for vendors
             */
            add_filter( 'yith_wcfm_products_subsections', array( $this, 'premium_products_subsections' ) );

            add_action( 'created_term', array( $this, 'save_category_fields' ), 10, 3 );
            add_action( 'edit_term', array( $this, 'save_category_fields' ), 10, 3 );

            add_action( 'yith_wcfm_products_enqueue_scripts', array( $this, 'enqueue_premium_section_scripts' ) );

            add_action( 'yith_wcfm_delete_product_taxonomy_terms', 'YITH_Frontend_Manager_Section_Products_Premium::delete_term_taxonomy', 10, 3 );

            parent::__construct();
        }
        /**
         * Add product premium subsections
         * Like product taxonomies
         *
         * @since 1.0
         * @author Andrea Grillo <andrea.Grillo@yithemes.com>
         * @return array products subsections
         */
        public function premium_products_subsections( $free_subsections ){
            $premium_subsections =  array(
                'categories' => array(
                    'slug' => $this->get_option( 'slug', $this->id . '_categories', 'categories' ),
                    'name' => __( 'Categories', 'yith-frontend-manager-for-woocommerce' ),
                    'add_delete_script' => true
                ),

                'tags' => array(
                    'slug' => $this->get_option( 'slug', $this->id . '_tags', 'tags' ),
                    'name' => __( 'Tags', 'yith-frontend-manager-for-woocommerce' ),
                    'add_delete_script' => true
                ),

                'attributes' => array(
                    'slug' => $this->get_option( 'slug', $this->id . '_attributes', 'attributes' ),
                    'name' => __( 'Attributes', 'yith-frontend-manager-for-woocommerce' ),
                    'add_delete_script' => true
                ),
            );
            return apply_filters( 'yith_wcfm_premium_products_subsections', array_merge( $free_subsections, $premium_subsections ), $free_subsections, $premium_subsections, $this );
        }

        /**
         * WP Enqueue Scripts
         *
         * @author Corrado Porzio <corradoporzio@gmail.com>
         * @return void
         * @since  1.0.0
         */
        public function enqueue_premium_section_scripts() {
            wp_enqueue_style( 'yith-wcfm-taxonomies', YITH_WCFM_URL . 'assets/css/taxonomies.css', array(), YITH_WCFM_VERSION );
        }

        /**
         * When a term is deleted, delete its meta.
         *
         * @param mixed $term_id
         */
        public static function delete_term_meta( $term_id ) {
            global $wpdb;

            $term_id = absint( $term_id );

            if ( $term_id && get_option( 'db_version' ) < 34370 ) {
                $wpdb->delete( $wpdb->woocommerce_termmeta, array( 'woocommerce_term_id' => $term_id ), array( '%d' ) );
            }
        }

        public static function add_category( $array ) {

            $category_name = isset( $array['category_name'] ) ? $array['category_name'] : '';
            $category_slug = isset( $array['category_slug'] ) ? $array['category_slug'] : '';
            $category_parent = isset( $array['category_parent'] ) ? $array['category_parent'] : '';
            $category_description = isset( $array['category_description'] ) ? $array['category_description'] : '';

            $term_id = wp_insert_term( $category_name, 'product_cat', array(
                'slug'          => $category_slug,
                'parent'        => $category_parent,
                'description'   => $category_description,
            ));

            return $term_id;

        }

        public static function edit_category( $array ) {

            $category_id = isset( $array['id'] ) ? $array['id'] : '';
            $category_name = isset( $array['category_name'] ) ? $array['category_name'] : '';
            $category_slug = isset( $array['category_slug'] ) ? $array['category_slug'] : '';
            $category_parent = isset( $array['category_parent'] ) ? $array['category_parent'] : '';
            $category_description = isset( $array['category_description'] ) ? $array['category_description'] : '';

            $term_id = wp_update_term( $category_id, 'product_cat', array(
                'name'          => $category_name,
                'slug'          => $category_slug,
                'parent'        => $category_parent,
                'description'   => $category_description,
            ));

            return $term_id;

        }

        /**
         * Delete product taxonomies term
         *
         * @param $term_id
         * @param $taxonomy
         * @return bool|int|WP_Error
         *
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @since 1.0.0
         */
        public static function delete_term_taxonomy( $term_id, $taxonomy, $action ) {

            $check = false;

            if( 'product_attribute' == $taxonomy ){
                $check = self::process_delete_attribute( $term_id );
            }

            else {

                $check = wp_delete_term( $term_id, $taxonomy );
            }

            $message = $type = '';

            /**
             * $check value:
             *
             * (bool)   True on success,
             * (bool)   False if term does not exist.
             * (int)    Zero on attempted deletion of default Category.
             * (object) WP_Error if the taxonomy does not exist.
             */
            if( $check ){
                //Success
                self::delete_term_meta( $term_id );

                $message = _x( 'Term deleted successfully', '[Frontend]: user message', 'yith-frontend-manager-for-woocommerce' );
                $type    = 'success';
            }

            else {
                //Failed
                if( $check === false ){
                    $message = _x( 'Term does not exist', '[Frontend]: user message', 'yith-frontend-manager-for-woocommerce' );
                }

                elseif( is_wp_error( $check ) ){
                    $message = _x( 'Taxonomy does not exist', '[Frontend]: user message', 'yith-frontend-manager-for-woocommerce' );
                }

                else {
                    $message = _x( 'Attempted deletion of default Category', '[Frontend]: user message', 'yith-frontend-manager-for-woocommerce' );

                }

                $type = 'error';
            }

            wc_add_notice( $message, $type );

            return $check;
        }

        /**
         * Delete an attribute.
         * @return bool
         */
        public static function process_delete_attribute( $attribute_id ) {
            global $wpdb;

            $check = false;

            if( ! empty( $attribute_id ) ){
                $attribute_name = $wpdb->get_var( "SELECT attribute_name FROM {$wpdb->prefix}woocommerce_attribute_taxonomies WHERE attribute_id = $attribute_id" );
                $taxonomy       = wc_attribute_taxonomy_name( $attribute_name );
                do_action( 'woocommerce_before_attribute_delete', $attribute_id, $attribute_name, $taxonomy );

                if ( $attribute_name && $wpdb->query( "DELETE FROM {$wpdb->prefix}woocommerce_attribute_taxonomies WHERE attribute_id = $attribute_id" ) ) {
                    if ( taxonomy_exists( $taxonomy ) ) {
                        $terms = get_terms( $taxonomy, 'orderby=name&hide_empty=0' );
                        foreach ( $terms as $term ) {
                            wp_delete_term( $term->term_id, $taxonomy );
                        }
                    }

                    do_action( 'woocommerce_attribute_deleted', $attribute_id, $attribute_name, $taxonomy );
                    delete_transient( 'wc_attribute_taxonomies' );

                    $check = true;
                }
            }

            return $check;
        }


        /**
         * save_category_fields function.
         *
         * @param mixed $term_id Term ID being saved
         * @param mixed $tt_id
         * @param string $taxonomy
         *
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @since 1.0.0
         */
        public function save_category_fields( $term_id, $tt_id = '', $taxonomy = '' ) {
            if ( isset( $_POST['display_type'] ) && 'product_cat' === $taxonomy ) {
	            update_term_meta( $term_id, 'display_type', esc_attr( $_POST['display_type'] ) );
            }
            if ( isset( $_POST['attach_id'] ) && 'product_cat' === $taxonomy ) {
	            update_term_meta( $term_id, 'thumbnail_id', absint( $_POST['attach_id'] ) );
            }
        }

        public static function add_tag( $array ) {

            $tag_name = isset( $array['tag_name'] ) ? $array['tag_name'] : '';
            $tag_slug = isset( $array['tag_slug'] ) ? $array['tag_slug'] : '';
            $tag_description = isset( $array['tag_description'] ) ? $array['tag_description'] : '';

            return wp_insert_term( $tag_name, 'product_tag', array(
                'slug'          => $tag_slug,
                'description'   => $tag_description,
            ));

        }

        public static function edit_tag( $array ) {

            $tag_id = isset( $array['id'] ) ? $array['id'] : '';
            $tag_name = isset( $array['tag_name'] ) ? $array['tag_name'] : '';
            $tag_slug = isset( $array['tag_slug'] ) ? $array['tag_slug'] : '';
            $tag_description = isset( $array['tag_description'] ) ? $array['tag_description'] : '';

            return wp_update_term( $tag_id, 'product_tag', array(
                'name'          => $tag_name,
                'slug'          => $tag_slug,
                'description'   => $tag_description,
            ));

        }

        public static function add_attribute( $array ) {

            global $wpdb;

            $attribute['attribute_id']              = isset( $array['attribute_id'] ) ? $array['attribute_id'] : '';
	        $attribute['attribute_label']           = isset( $array['attribute_label'] ) ? $array['attribute_label'] : '';
	        $attribute['attribute_name']            = isset( $array['attribute_name'] ) ? $array['attribute_name'] : '';
            $attribute['attribute_public']          = isset( $array['attribute_public'] ) ? $array['attribute_public'] : 0;
            $attribute['attribute_type']            = isset( $array['attribute_type'] ) ? $array['attribute_type'] : 'text';
            $attribute['attribute_orderby']         = isset( $array['attribute_orderby'] ) ? $array['attribute_orderby'] : 'menu_order';

            $message = $type = '';
	        $format = array( '%s', '%s', '%s', '%s', '%d' );

            if ( empty( $attribute['attribute_name'] ) ) {
                $attribute['attribute_name'] = sanitize_title( strtolower( str_replace( ' ', '-', $attribute['attribute_label'] ) ) );
            }

	        if( strlen( $attribute['attribute_name'] ) > 28 ){
		        $attribute['attribute_name'] = trim( substr( $attribute['attribute_name'], 0, 28 ), '-' );
	        }

            if ( empty( $attribute['attribute_label'] ) ) {
                $message = __( 'Please, provide an attribute name and slug.', 'yith-frontend-manager-for-woocommerce' );
                $type = 'error';
            }

            elseif ( taxonomy_exists( wc_attribute_taxonomy_name( $attribute['attribute_name'] ) ) ) {
                $message = sprintf( __( 'Slug <strong>"%s"</strong> is already being used. Please, choose a new one.', 'yith-frontend-manager-for-woocommerce' ), sanitize_title( $attribute['attribute_name'] ) );
                $type    = 'error';
            }

            else {
                $wpdb->insert( $wpdb->prefix . 'woocommerce_attribute_taxonomies', $attribute, $format );
                do_action( 'woocommerce_attribute_added', $wpdb->insert_id, $attribute );
                flush_rewrite_rules();
                delete_transient( 'wc_attribute_taxonomies' );

                $message = $message = _x( 'Attribute added successfully', '[Frontend]: user message', 'yith-frontend-manager-for-woocommerce' );
                $type    = 'success';
            }

            if( ! empty( $message ) ){
                wc_add_notice( $message, $type );
            }


	        $id = $wpdb->insert_id;

	        /**
	         * Attribute added.
	         *
	         * @param int   $id   Added attribute ID.
	         * @param array $data Attribute data.
	         */
	        do_action( 'woocommerce_attribute_added', $id, $attribute );

        }

        public static function edit_attribute( $array ) {

            global $wpdb;

	        $attribute['attribute_id']      = isset( $array['id'] ) ? $array['id'] : '';
	        $attribute['attribute_name']    = isset( $array['attribute_name'] ) ? $array['attribute_name'] : '';
	        $attribute['attribute_label']   = isset( $array['attribute_label'] ) ? $array['attribute_label'] : '';
	        $attribute['attribute_public']  = isset( $array['attribute_public'] ) ? $array['attribute_public'] : 0;
	        $attribute['attribute_type']    = isset( $array['attribute_type'] ) ? $array['attribute_type'] : 'text';
	        $attribute['attribute_orderby'] = isset( $array['attribute_orderby'] ) ? $array['attribute_orderby'] : 'menu_order';
	        $format                         = array( '%s', '%s', '%s', '%s', '%d' );

            if ( isset( $attribute['attribute_name'] ) && empty( $attribute['attribute_label'] ) ) {
                $attribute['attribute_label'] = sanitize_title( $attribute['attribute_name'] );
            } elseif ( empty( $attribute['attribute_name'] ) && empty( $attribute['attribute_label'] ) ) {
                return new WP_Error( 'error', __( 'Please, provide an attribute name and slug.', 'yith-frontend-manager-for-woocommerce' ) );
            }

            $wpdb->update( $wpdb->prefix . 'woocommerce_attribute_taxonomies',
	            $attribute,
	            array( 'attribute_id' => $attribute['attribute_id'] ),
	            $format,
	            array( '%d' )
            );

            flush_rewrite_rules();
            delete_transient( 'wc_attribute_taxonomies' );

	        // Set old_slug to check for database changes.
	        $array['old_slug'] = ! empty( $array['attribute_name_old'] ) ? $array['attribute_name_old'] : $array['slug'];
	        $id                = ! empty( $array['attribute_id'] ) ? intval( $array['attribute_id'] ) : 0;
	        /**
	         * Attribute updated.
	         *
	         * @param int    $id        Added attribute ID.
	         * @param array  $data      Attribute data.
	         * @param string $old_slug  Attribute old name.
	         */
	        do_action( 'woocommerce_attribute_updated', $id, $attribute, $array['old_slug'] );

            return isset( $array['attribute_name_old'] ) ? $array['attribute_name_old'] : true;

        }

        public static function add_attribute_term( $array, $attribute ) {

            $term_name = isset( $array['term_name'] ) ? $array['term_name'] : '';
            $term_slug = isset( $array['term_slug'] ) ? $array['term_slug'] : '';
            $term_parent = isset( $array['term_parent'] ) ? $array['term_parent'] : '';
            $term_description = isset( $array['term_description'] ) ? $array['term_description'] : '';

            return wp_insert_term( $term_name, 'pa_' . $attribute, array(
                'slug'          => $term_slug,
                'parent'        => $term_parent,
                'description'   => $term_description,
            ));

        }

        public static function edit_attribute_term( $array, $attribute ) {

            $term_id = isset( $array['id'] ) ? $array['id'] : '';
            $term_name = isset( $array['term_name'] ) ? $array['term_name'] : '';
            $term_slug = isset( $array['term_slug'] ) ? $array['term_slug'] : '';
            $term_parent = isset( $array['term_parent'] ) ? $array['term_parent'] : '';
            $term_description = isset( $array['term_description'] ) ? $array['term_description'] : '';

            return wp_update_term( $term_id, 'pa_' . $attribute, array(
                'name'          => $term_name,
                'slug'          => $term_slug,
                'parent'        => $term_parent,
                'description'   => $term_description,
            ));

        }

    }
}