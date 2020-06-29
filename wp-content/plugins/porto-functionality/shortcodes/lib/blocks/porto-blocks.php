<?php
/**
 * define porto blocks
 *
 * @since 4.8.4
 */

if ( ! class_exists( 'PortoBlocksClass' ) ) :
	class PortoBlocksClass {
		function __construct() {
			add_action( 'rest_api_init', array( $this, 'registerRestAPI' ) );

			add_filter( 'woocommerce_rest_prepare_product_cat', array( $this, 'add_cat_icon_field' ), 10, 3 );
		}

		function registerRestAPI() {
			// Register router to get data for Woocommerce Products block
			if ( class_exists( 'WC_REST_Products_Controller' ) ) {
				include_once( 'class-products-controller.php' );
				$controller = new PortoBlocksProductsController();
				$controller->register_routes();
			}

			register_rest_field(
				'post',
				'featured_image_src',
				array(
					'get_callback'    => array( $this, 'featuredImageSrc' ),
					'update_callback' => null,
					'schema'          => null,
				)
			);
		}

		public function add_cat_icon_field( $response, $item, $request ) {
			if ( ! isset( $_REQUEST['porto'] ) ) {
				return $response;
			}
			$data             = $response->get_data();
			$data['cat_icon'] = esc_html( get_metadata( 'product_cat', $item->term_id, 'category_icon', true ) );
			if ( ! empty( $data['image'] ) ) {
				if ( ! empty( $_REQUEST['image_size'] ) ) {
					$image_size = $_REQUEST['image_size'];
				} else {
					$image_size = 'shop_catalog';
				}
				$image = wp_get_attachment_image_src( $data['image']['id'], $image_size, false );
				if ( is_array( $image ) ) {
					$data['image']['catalog_src'] = $image[0];
				}
			}
			$response->set_data( $data );
			return $response;
		}

		/**
		 * Get featured image link for REST API
		 *
		 * @param array $object API Object
		 *
		 * @return mixed
		 */
		public function featuredImageSrc( $object ) {
			$featured_img_full   = wp_get_attachment_image_src(
				$object['featured_media'],
				'full',
				false
			);
			$featured_img_large  = wp_get_attachment_image_src(
				$object['featured_media'],
				'blog-large',
				false
			);
			$featured_img_list   = wp_get_attachment_image_src(
				$object['featured_media'],
				'blog-medium',
				false
			);
			$featured_img_medium = wp_get_attachment_image_src(
				$object['featured_media'],
				'medium',
				false
			);

			return array(
				'landsacpe' => $featured_img_large,
				'list'      => $featured_img_list,
				'medium'    => $featured_img_medium,
				'full'      => $featured_img_full,
			);
		}
	}
endif;

new PortoBlocksClass();
