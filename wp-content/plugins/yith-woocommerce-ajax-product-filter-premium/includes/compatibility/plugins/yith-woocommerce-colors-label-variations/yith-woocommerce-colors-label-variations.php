<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * YITH WooCommerce Color Label Variations plugin support
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\AjaxProductFilter\Classes\Compatibility
 * @version 4.1.1
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAN_Color_Label_Compatibility' ) ) {
	/**
	 * Class that implements methods required to integrate single variations of YITH WC Color and Labels in YITH WCAN Filters.
	 *
	 * @since 4.1.1
	 */
	class YITH_WCAN_Color_Label_Compatibility {

		/**
		 * Main instance
		 *
		 * @var YITH_WCAN_Color_Label_Compatibility
		 * @since 4.1.1
		 */
		protected static $instance = null;

		/**
		 * Init integration, hooking all required methods
		 *
		 * @return void
		 */
		public function init() {
			// clear transients when C&L options change.
			add_action( 'update_option_yith-wccl-show-single-variations-loop', array( 'YITH_WCAN_Cache_Helper', 'delete_transients' ) );
			add_action( 'update_option_yith-wccl-hide-parent-products-loop', array( 'YITH_WCAN_Cache_Helper', 'delete_transients' ) );

			// add support for variations in query, when C&L asks for it.
			if ( 'yes' === get_option( 'yith-wccl-show-single-variations-loop', 'no' ) ) {
				add_filter( 'yith_wcan_product_ids_in_stock_args', array( $this, 'add_variations_ids_in_stock' ) );
				add_filter( 'yith_wcan_filtered_products_query', array( $this, 'add_variation_query_to_filtered_products' ) );
			}

            // add support for C&L terms on filters.
            add_filter( 'yith_wcan_attribute_filter_item_args', array( $this, 'filter_items_args' ), 10, 2 );

        }

		/**
		 * Filters post in for YITH WCAN queries, including post_in parameter as computed by Booking plugin
		 *
		 * @param array $query_args Array of products types.
		 * @return array Query arguments.
		 */
		public function add_variations_ids_in_stock( $query_args ) {
			// add variation among valid product types.
			$product_types = array_merge(
				array_keys( wc_get_product_types() ),
				array(
					'variation',
				)
			);

			// remove variable type if necessary.
			$variable_index = array_search( 'variable', $product_types, true );

			if ( 'yes' === get_option( 'yith-wccl-hide-parent-products-loop', 'yes' ) && false !== $variable_index ) {
				unset( $product_types[ $variable_index ] );
			}

			$query_args['type'] = $product_types;

			return $query_args;
		}

		/**
		 * Add_variation_query_to_filtered_products
		 *
		 * @param array $query_args Array of products types.
		 * @return array Product query arguments.
		 */
		public function add_variation_query_to_filtered_products( $query_args ) {
			$query_args['post_type'] = array(
				'product',
				'product_variation',
			);

			return $query_args;
		}
        /**
         * add support for C&L terms on filters.
         *
         * @param array   $data The data array to filter.
         * @param integer $term_id The term id to process.
         * @return array
         */
        public function filter_items_args( $data, $term_id ) {

            $term = get_term( $term_id );
            if ( ! $term instanceof WP_Term ) {
                return $data;
            }

            $attribute_id = wc_attribute_taxonomy_id_by_name( str_replace( 'pa_', '', $term->taxonomy ) );
            if ( empty( $attribute_id ) ) {
                return $data;
            }

            $tooltip = ywccl_get_term_meta( $term_id, '_yith_wccl_tooltip' );
            if ( ! empty( $tooltip ) ) {
                $data['tooltip'] = $tooltip;
            }

            $value = ywccl_get_term_meta( $term_id, '_yith_wccl_value' );
            if ( ! empty( $value ) ) {
                $attribute = wc_get_attribute( $attribute_id );
                switch ( $attribute->type ) {
                    case 'colorpicker':

                        //ColorPicker is an image.
                        if( filter_var( $value, FILTER_VALIDATE_URL) ) {
                            $media_id = attachment_url_to_postid( $value );
                            $data['image'] = $media_id;
                            $data['mode']  = 'image';
                        } else {
                            $colors          = ! is_array( $value ) ? explode( ',', $value ) : $value;
                            $data['color_1'] = $colors[0];
                            if ( isset( $colors[1] ) ) {
                                $data['color_2'] = $colors[1];
                            }
                        }
                        break;
                    case 'image':
                        $media_id = attachment_url_to_postid( $value );
                        if ( $media_id ) {
                            $data['image'] = $media_id;
                            $data['mode']  = 'image';

                            // Replace tooltip placeholder if any.
                            $thumb_src       = wp_get_attachment_image_url( $media_id, 'thumbnail' );
                            $image           = '<img src="' . $thumb_src . '" />';
                            $data['tooltip'] = str_replace( '{show_image}', $image, $data['tooltip'] );
                        }
                        break;
                    default:
                        $data['label'] = $value;
                        break;
                }
            }

            return $data;
        }

		/**
		 * Compatibility class instance
		 *
		 * @return YITH_WCAN_Color_Label_Compatibility Class unique instance
		 */
		public static function instance() {

			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}
	}
}

// init compatibility.
if ( defined( 'YITH_WCCL_PREMIUM' ) ) {
	YITH_WCAN_Color_Label_Compatibility::instance()->init();
}
