<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WC_Additional_Variation_Images_Admin {
	private static $_this;

	/**
	 * init
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function __construct() {
		self::$_this = $this;

		add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_scripts' ) );

		add_action( 'wp_ajax_wc_additional_variation_images_load_images_ajax', array( $this, 'load_images_ajax' ) );

		add_action( 'save_post', array( $this, 'wc_additional_variation_image_save' ), 1, 2 );

		add_action( 'woocommerce_save_product_variation', array( $this, 'save_product_variation' ), 10, 2 );

    	return true;
	}

	/**
	 * public function to get instance
	 *
	 * @since 1.1.1
	 * @return instance object
	 */
	public function get_instance() {
		return self::$_this;
	}
		
	/**
	 * load admin scripts
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function load_admin_scripts() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		if ( 'product' === get_post_type() ) {
			wp_enqueue_script( 'wc_additional_variation_images_admin_script', plugins_url( 'assets/js/admin-settings' . $suffix . '.js' , dirname( __FILE__ ) ), array( 'jquery' ), WC_ADDITIONAL_VARIATION_IMAGES_VERSION, true  );

			$localized_vars = array(
				'ajaxurl'                   => admin_url( 'admin-ajax.php' ),
				'ajaxAdminLoadImageNonce'   => wp_create_nonce( '_wc_additional_variation_images_nonce' ),
				'adminTitleText'            => __( 'Additional Images', 'woocommerce-additional-variation-images' ),
				'adminAddImagesText'        => __( 'Add Additional Images', 'woocommerce-additional-variation-images' ),
				'adminMediaAddImageText'    => __( 'Add to Variation', 'woocommerce-additional-variation-images' ),
				'adminMediaTitle'           => __( 'Variation Images', 'woocommerce-additional-variation-images' ),
				'adminAddImagesTip'         => wc_sanitize_tooltip( __( 'Click on link below to add additional images. Click on image itself to remove the image. Click and drag image to re-order the image position.', 'woocommerce-additional-variation-images' ) ),
			);

			wp_localize_script( 'wc_additional_variation_images_admin_script', 'wc_additional_variation_images_local', $localized_vars );

			wp_enqueue_style( 'wc_additional_variation_images_admin_style', plugins_url( 'assets/css/admin.css', dirname( __FILE__ ) ), array(), WC_ADDITIONAL_VARIATION_IMAGES_VERSION );
		}

		return true;
	}

	/**
	 * load variation images
	 *
	 * @since 1.0.0
	 * @return json
	 */
	public function load_images_ajax() {
		$nonce = $_POST['ajaxAdminLoadImageNonce'];

		// bail if nonce don't check out
		if ( ! wp_verify_nonce( $nonce, '_wc_additional_variation_images_nonce' ) ) {
		     die ( 'error' );	
		}

		// bail if no ids submitted
		if ( ! isset( $_POST['variation_ids'] ) ) {
			echo 'error';
			exit;
		}

		// sanitize
		$variation_ids = array_map( 'absint', $_POST['variation_ids'] );

		$variation_images = array();

		if ( 0 < count( $variation_ids ) ) {
			foreach( $variation_ids as $id ) {
				$ids = $this->get_images( $id );

				$html = '';
				$html .= '<input type="hidden" class="wc-additional-variations-images-thumbs-save" name="wc_additional_variations_images_thumbs[' . esc_attr( $id ) . ']" value="' . esc_attr( $ids ) . '">';
				$html .= '<ul class="wc-additional-variations-images-list">';

				foreach( explode( ',', $ids ) as $attach_id ) {
					$attachment = wp_get_attachment_image_src( $attach_id, array( 40, 40 ) );

					if ( $attachment ) {		
						$html .= '<li><a href="#" class="wc-additional-variations-images-thumb" data-id="' . esc_attr( $attach_id ) . '"><img src="' . esc_attr( $attachment[0] ) . '" width="40" height="40" /><span class="overlay"></span></a></li>';
					}
				}

				$html .= '</ul>';

				$variation_images[ $id ] = $html;
			}
		}

		wp_send_json( array( 'images' => $variation_images ) );
	}

	/**
	 * get images
	 *
	 * @since 1.0.0
	 * @return array $media_ids
	 */
	public function get_images( $id = 0 ) {
		$media_ids = get_post_meta( $id, '_wc_additional_variation_images', true );

		return $media_ids;
	}

	/**
	 * hooks into save post
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function wc_additional_variation_image_save( $post_id, $post ) {
		// $post_id and $post are required
		if ( empty( $post_id ) || empty( $post ) || ! isset( $_POST['wc_additional_variations_images_thumbs'] ) ) {
			return;
		}

		// Dont' save meta boxes for revisions or autosaves
		if ( defined( 'DOING_AUTOSAVE' ) || is_int( wp_is_post_revision( $post ) ) || is_int( wp_is_post_autosave( $post ) ) ) {
			return;
		}
		
		// Check the nonce
		if ( empty( $_POST['woocommerce_meta_nonce'] ) || ! wp_verify_nonce( $_POST['woocommerce_meta_nonce'], 'woocommerce_save_data' ) ) {
			return;
		} 

		// Check the post being saved == the $post_id to prevent triggering this call for other save_post events
		if ( empty( $_POST['post_ID'] ) || $_POST['post_ID'] != $post_id ) {
			return;
		}

		// Check user has permission to edit
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Check the post type
		if ( ! in_array( $post->post_type, array( 'product' ) ) ) {
			return;
		}

		$ids = $_POST['wc_additional_variations_images_thumbs'];

		// sanitize
		array_walk_recursive( $ids, 'sanitize_text_field' );

		if ( 0 < count( $ids ) ) {
			foreach( $ids as $parent_id => $attachment_ids ) {
				if ( isset( $attachment_ids ) ) {
					update_post_meta( $parent_id, '_wc_additional_variation_images', $attachment_ids );
				} else {
					update_post_meta( $parent_id, '_wc_additional_variation_images', '' );
				}	
			}
		}

		return true;
	}

	/**
	 * Saves images on ajax save
	 *
	 * @since 1.7.0
	 * @param int $variation_id
	 * @param int $i loop count
	 * @return bool
	 */
	public function save_product_variation( $variation_id, $i ) {

		if ( ! isset( $_POST['wc_additional_variations_images_thumbs'] ) ) {
			return;
		}

		$ids = sanitize_text_field( $_POST['wc_additional_variations_images_thumbs'][ $variation_id ] );

		update_post_meta( $variation_id, '_wc_additional_variation_images', $ids );

		return true;
	}
}

new WC_Additional_Variation_Images_Admin();