<?php
/**
 * Main class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Zoom Magnifier
 * @version 1.1.2
 */

if ( ! defined( 'YITH_WCMG' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WooCommerce_Zoom_Magnifier_Premium' ) ) {
	/**
	 * YITH WooCommerce Zoom Magnifier Premium
	 *
	 * @since 1.0.0
	 */
	class YITH_WooCommerce_Zoom_Magnifier_Premium extends YITH_WooCommerce_Zoom_Magnifier {

		/**
		 * Constructor
		 *
		 * @return mixed|YITH_WCMG_Admin|YITH_WCMG_Frontend
		 * @since 1.0.0
		 */
		public function __construct() {

            add_action( 'wp_ajax_nopriv_yith_wc_zoom_magnifier_get_main_image', array(
                $this,
                'yith_wc_zoom_magnifier_get_main_image_call_back'
            ), 10 );

            add_action( 'wp_ajax_yith_wc_zoom_magnifier_get_main_image', array(
                $this,
                'yith_wc_zoom_magnifier_get_main_image_call_back'
            ), 10 );

			// actions
			add_action( 'init', array( $this, 'init' ) );

			if ( is_admin() && ( ! isset( $_REQUEST['action'] ) || ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] != 'yith_load_product_quick_view' ) ) ) {

				$this->obj = new YITH_WCMG_Admin();
			} else {

				/** Stop the plugin on mobile devices */
				if ( ( 'yes' != get_option( 'yith_wcmg_enable_mobile' ) ) && wp_is_mobile() ) {

					return;
				}

				$this->obj = new YITH_WCMG_Frontend_Premium();
			}

			$this->set_plugin_options();

			add_action( 'ywzm_products_exclusion', array( $this, 'show_products_exclusion_table' ) );

			add_action( 'woocommerce_admin_field_ywzm_category_exclusion', array(
				$this,
				'show_product_category_exclusion_table',
			) );

			return $this->obj;
		}

        /**
         * Ajax method to retrieve the product main imavge
         *
         * @access public
         * @author Daniel Sanchez Saez
         * @since  1.3.4
         */
        public function yith_wc_zoom_magnifier_get_main_image_call_back(){

            // set the main wp query for the product
            global $post, $product;

            $product_id         = isset( $_POST[ 'product_id' ] ) ? $_POST[ 'product_id' ] : 0;
            $post               = get_post( $product_id ); // to fix junk theme compatibility
            $product            = wc_get_product( $product_id );

            if( empty( $product ) ) {
                wp_send_json_error();
            }


            $url	            = wp_get_attachment_image_src( get_post_thumbnail_id( $product_id ), "full" );

            if( function_exists( 'YITH_WCCL_Frontend' ) && function_exists( 'yith_wccl_get_variation_gallery' ) ) {

                $gallery            = yith_wccl_get_variation_gallery( $product );
                // filter gallery based on current variation
                if( ! empty( $gallery ) ) {

                    add_filter( 'woocommerce_product_variation_get_gallery_image_ids', [ YITH_WCCL_Frontend(), 'filter_gallery_ids' ], 10, 2 );
                }
            }

            ob_start();
            wc_get_template( 'single-product/product-thumbnails-magnifier.php', [], '', YITH_YWZM_DIR . 'templates/' );
            $gallery_html = ob_get_clean();

            wp_send_json( [
                'url'       => isset( $url[ 0 ] ) ? $url[ 0 ] : '',
                'gallery'   => $gallery_html
            ] );

        }

		public function show_product_category_exclusion_table( $args = array() ) {
			if ( ! empty( $args ) ) {
				$args['value'] = ( get_option( $args['id'] ) ) ? get_option( $args['id'] ) : $args['default'];
				extract( $args );

				$exclusion_list = get_option( 'ywzm_category_exclusion' );

				?>
				<tr valign="top">
					<th scope="row" class="image_upload">
						<label for="<?php echo $id ?>"><?php echo $name ?></label>
					</th>
					<td class="forminp forminp-color plugin-option">
						<div class="categorydiv">
							<div class="tabs-panel">
								<ul id="product_catchecklist" data-wp-lists="list:product_cat"
								    class="categorychecklist form-no-clear">
									<input value="-1" type="hidden" name="ywzm_category_exclusion[]">
									<?php


									/** Check the WP version for calling get_terms in the right way
									 *
									 * Prior to 4.5.0, the first parameter of `get_terms()` was a taxonomy or list of taxonomies:
									 *
									 *     $terms = get_terms( 'post_tag', array(
									 *         'hide_empty' => false,
									 *     ) );
									 *
									 * Since 4.5.0, taxonomies should be passed via the 'taxonomy' argument in the `$args` array:
									 *
									 *     $terms = get_terms( array(
									 *         'taxonomy' => 'post_tag',
									 *         'hide_empty' => false,
									 *     ) ); */
									$terms = $this->wp_prior_4_5
										? get_terms( 'product_cat', array(
											'hide_empty' => false,
										) )
										: get_terms( array(
											'taxonomy'   => 'product_cat',
											'hide_empty' => false,
										) );

									if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
										foreach ( $terms as $term ) {

											/* Retrieve the correct term id to search for*/
											$term_id        = $this->wc_prior_2_6 ? $term->woocommerce_term_id : $term->term_id;
											$checked_status = is_array( $exclusion_list ) && in_array( $term_id, $exclusion_list ) ? 'checked = checked' : '';

											echo '<li><label class="selectit"><input value="' . $term_id . '" type="checkbox" ' . $checked_status . ' name="ywzm_category_exclusion[]" id="in-product_cat-' . $term_id . '">' . $term->name . '</label></li>';
										}
									}

									?>
								</ul>
							</div>
						</div>
					</td>
				</tr>
				<?php
			}
		}

		public function show_products_exclusion_table() {


			YWZM_Products_Exclusion::output();
		}

		public function set_plugin_options() {
			add_filter( 'yith_ywzm_general_settings', array( $this, 'add_product_category_exclusion_list' ) );
			add_filter( 'yith_ywzm_magnifier_settings', array( $this, 'set_zoom_box_options' ) );
		}

		public function add_product_category_exclusion_list( $args ) {
			$new_item = array(
				'id'   => 'ywzm_category_exclusion',
				'type' => 'ywzm_category_exclusion',
				'name' => apply_filters( 'yith_ywzm_exclude_or_include_categories_option_name', esc_html__( 'Exclude product categories', 'yith-woocommerce-zoom-magnifier' ) ),
			);

			$args = array_slice( $args, 0, count( $args ) - 1, true ) +
			        array( 'category_exclusion' => $new_item ) +
			        array_slice( $args, 3, count( $args ) - 1, true );

			return $args;
		}

		public function set_zoom_box_options( $args ) {
			if ( isset( $args['zoom_box_position'] ) ) {
				$box_position = &$args['zoom_box_position'];

				$box_position['options'] = array(
					'top'    => esc_html__( 'Top', 'yith-woocommerce-zoom-magnifier' ),
					'right'  => esc_html__( 'Right', 'yith-woocommerce-zoom-magnifier' ),
					'bottom' => esc_html__( 'Bottom', 'yith-woocommerce-zoom-magnifier' ),
					'left'   => esc_html__( 'Left', 'yith-woocommerce-zoom-magnifier' ),
					'inside' => esc_html__( 'Inside', 'yith-woocommerce-zoom-magnifier' ),
				);

			}

			return $args;
		}

		/**
		 * Check if current product have to be ignored by the plugin.
		 * We want to be alerted only if we are working on a valid product on which a product rule or catefory rule is active.
		 *
		 * @return bool product should be ignored
		 */
		public function is_product_excluded() {
			global $post;

			//  if current post is not a product, there is nothing to report.
			if ( ! is_product() ) {
				return false;
			}

			//  Check single product exclusion rule
			$is_excluded = yit_get_prop( wc_get_product($post->ID), '_ywzm_exclude', true );

			if ( 'yes' != $is_excluded ) {
                $is_excluded = $this->is_product_category_excluded();
			}

			return $is_excluded;
		}

		/**
		 * Check if current product is associated with a product category excluded by plugin option
		 */
		public function is_product_category_excluded() {
			global $post;

			//  if current post is not a product, there is nothing to report.
			if ( ! is_product() ) {
				return false;
			}

			$exclusion_list = get_option( 'ywzm_category_exclusion' );
			if ( ! $exclusion_list ) {
				return false;
			}

			$terms = get_the_terms( $post->ID, 'product_cat' );

			if ( $terms && ! is_wp_error( $terms ) ) {

				foreach ( $terms as $term ) {

					if ( apply_filters( 'yith_ywzm_exclude_or_include_categories', in_array( $term->term_id, $exclusion_list ), $term->term_id, $exclusion_list  ) ) {

						return true;
					}
				}
			}

			return false;
		}

        /**
         * Plugin Row Meta
         *
         *
         * @return void
         * @since    1.4.1
         * @author   Carlos Rodríguez <carlos.rodriguez@youirinspiration.it>
         */
        public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YITH_YWZM_INIT' ) {
            $new_row_meta_args = parent::plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file );

            if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ){
                $new_row_meta_args['is_premium'] = true;
            }

            return $new_row_meta_args;
        }
        /**
         * Regenerate auction prices
         *
         * Action Links
         *
         * @return void
         * @since    1.4.1
         * @author   Carlos Rodríguez <carlos.rodriguez@youirinspiration.it>
         */
        public function action_links( $links ) {
            $links = yith_add_action_links( $links, 'yith_woocommerce_zoom-magnifier_panel', true );
            return $links;
        }
	}
}