<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WC_Additional_Variation_Images_Frontend {
	private static $_this;

	/**
	 * init
	 *
	 * @since 1.0.0
	 * @version 1.7.9
	 * @return bool
	 */
	public function __construct() {
		self::$_this = $this;

		if ( ! is_admin() ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );
		}

		$method = 'ajax_get_images' . ( version_compare( WC_VERSION, '3.0', '<' ) ? '_pre30' : '' );
		add_action( 'wc_ajax_wc_additional_variation_images_get_images', array( $this, $method ) );

		return true;
	}

	/**
	 * public function to get instance
	 *
	 * @since 1.1.1
	 * @version 1.7.9
	 * @return instance object
	 */
	public static function get_instance() {
		return self::$_this;
	}

	/**
	 * load frontend scripts
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function load_scripts() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_script( 'wc_additional_variation_images_script', plugins_url( 'assets/js/variation-images-frontend' . $suffix . '.js', dirname( __FILE__ ) ), array( 'jquery' ), WC_ADDITIONAL_VARIATION_IMAGES_VERSION, true );

		$bwc = version_compare( WC_VERSION, '3.0', '<' );

		$localized_vars = array(
			'ajax_url'             => WC_AJAX::get_endpoint( '%%endpoint%%' ),
			'ajaxImageSwapNonce'   => wp_create_nonce( '_wc_additional_variation_images_nonce' ),
			'gallery_images_class' => apply_filters( 'wc_additional_variation_images_gallery_images_class', '.product .images .flex-control-nav, .product .images .thumbnails' ),
			'main_images_class'    => apply_filters( 'wc_additional_variation_images_main_images_class', $bwc ? '.product .images > a' : '.woocommerce-product-gallery' ),
			'lightbox_images'      => apply_filters( 'wc_additional_variation_images_main_lightbox_images_class', '.product .images a.zoom' ),
			'custom_swap'          => apply_filters( 'wc_additional_variation_images_custom_swap', false ),
			'custom_original_swap' => apply_filters( 'wc_additional_variation_images_custom_original_swap', false ),
			'custom_reset_swap'    => apply_filters( 'wc_additional_variation_images_custom_reset_swap', false ),
			'bwc'                  => $bwc,
		);

		wp_localize_script( 'wc_additional_variation_images_script', 'wc_additional_variation_images_local', $localized_vars );

		return true;
	}

	/**
	 * checks if cloud zoom plugin exists
	 *
	 * @since 1.3.0
	 * @return boolean
	 */
	public function cloud_zoom_exists() {
		if ( class_exists( 'woocommerce_professor_cloud' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Load variation images frontend ajax.
	 *
	 * @since 1.0.0
	 * @version 1.7.9
	 *
	 * @return html
	 */
	public function ajax_get_images() {
		check_ajax_referer( '_wc_additional_variation_images_nonce', 'security' );

		// Sanitize.
		$post_id = absint( $_POST['post_id'] );

		$variation_id = '';

		if ( ! isset( $_POST['variation_id'] ) ) {
			$image_ids = get_post_thumbnail_id( $post_id ) . ',' . get_post_meta( $post_id, '_product_image_gallery', true );
		} else {
			$variation_id = absint( $_POST['variation_id'] );

			$image_ids = get_post_meta( $variation_id, '_wc_additional_variation_images', true );
		}

		$image_ids = array_filter( explode( ',', $image_ids ) );

		$product = wc_get_product( $variation_id );

		$variation_main_image = get_post_meta( $variation_id, '_thumbnail_id', true );
		if ( ! empty( $variation_main_image ) ) {
				array_unshift( $image_ids, $variation_main_image );
		}

		// If there are still no image IDs set, fallback to original main image
		if ( $product && empty( $image_ids ) ) {
 			$main_image_id = $product->get_image_id();
 
 			if ( ! empty( $main_image_id ) ) {
 				array_unshift( $image_ids, $main_image_id );
 			}
		}


		$main_images = '<div class="woocommerce-product-gallery woocommerce-product-gallery--with-images woocommerce-product-gallery--columns-' . apply_filters( 'woocommerce_product_thumbnails_columns', 4 ) . ' images" data-columns="' . apply_filters( 'woocommerce_product_thumbnails_columns', 4 ) . '"><figure class="woocommerce-product-gallery__wrapper">';

		$loop = 0;

		if ( 0 < count( $image_ids ) ) {
			/*
			 * When there is no support for gallery zoom, we need to add
			 * an image link so that the lightbox can be triggered.
			 */
			$add_image_link = current_theme_supports( 'wc-product-gallery-zoom' ) ? false : true;

			// We need to also check if theme supports lightbox.
			if ( ! current_theme_supports( 'wc-product-gallery-lightbox' ) ) {
				$add_image_link = false;
			}

			// Build html.
			foreach ( $image_ids as $id ) {
				$image_title     = esc_attr( get_the_title( $id ) );
				$full_size_image = wp_get_attachment_image_src( $id, 'full' );
				$thumbnail       = wp_get_attachment_image_src( $id, 'shop_thumbnail' );

				$attributes = array(
					'title'                   => $image_title,
					'data-large_image'        => $full_size_image[0],
					'data-large_image_width'  => $full_size_image[1],
					'data-large_image_height' => $full_size_image[2],
				);

				// See if we need to get the first image of the variation
				// only run one time.
				if ( ( apply_filters( 'wc_additional_variation_images_get_first_image', false ) || $this->cloud_zoom_exists() ) && 0 === $loop ) {

					$html  = '<div data-thumb="' . esc_url( $thumbnail[0] ) . '" class="woocommerce-product-gallery__image flex-active-slide">';

					if ( $add_image_link ) {
						$html .= '<a href="' . wp_get_attachment_url( $id ) . '">';
					}

					$html .= wp_get_attachment_image( $id, 'shop_single', false, $attributes );

					if ( $add_image_link ) {
						$html .= '</a>';
					}

					$html .= '</div>';

					$main_images .= apply_filters( 'woocommerce_single_product_image_thumbnail_html', $html, $id );

					$loop++;
					continue;
				}

				if ( $add_image_link ) {
					$attach_image = '<a href="' . wp_get_attachment_url( $id ) . '">' . wp_get_attachment_image( $id, 'shop_single', false, $attributes ) . '</a>';
				} else {
					$attach_image = wp_get_attachment_image( $id, 'shop_single', false, $attributes );
				}

				// Build the list of variations as main images in case a custom
				// theme has flexslider type lightbox.
				$main_images .= apply_filters( 'woocommerce_single_product_image_html', sprintf( '<div data-thumb="%s" class="woocommerce-product-gallery__image flex-active-slide">%s</div>', esc_url( $thumbnail[0] ), $attach_image ), $post_id );

				$loop++;
			}
		} else {
			$main_images .= '<div class="woocommerce-product-gallery__image--placeholder">';
			$main_images .= wc_placeholder_img();
			$main_images .= '</div>';
		}

		$main_images .= '</figure></div>';

		wp_send_json( array( 'main_images' => $main_images ) );
	}

	/**
	 * load variation images frontend ajax (for WC versions below 3.0)
	 * @since 1.7.4
	 * @version 1.7.9
	 */
	public function ajax_get_images_pre30() {
		check_ajax_referer( '_wc_additional_variation_images_nonce', 'security' );

		// bail if no ids submitted
		if ( ! isset( $_POST['variation_id'] ) ) {
			die( 'error' );
		}

		// sanitize
		$variation_id = absint( $_POST['variation_id'] );
		$post_id = absint( $_POST['post_id'] );

		// get post meta
		$image_ids = get_post_meta( $variation_id, '_wc_additional_variation_images', true );

		$image_ids = explode( ',', $image_ids );

		$main_images = '';
		$gallery_images = '';

		$loop = 0;
		$columns = (int) apply_filters( 'woocommerce_product_thumbnails_columns', 3 );

		if ( 0 < count( $image_ids ) ) {

			if ( apply_filters( 'wc_additional_variation_images_get_first_image', false ) || $this->cloud_zoom_exists() ) {
				$variation_main_image = get_post_meta( $variation_id, '_thumbnail_id', true );
				if ( $variation_main_image ) {
					array_unshift( $image_ids, $variation_main_image );
				}
			}

			// build html
			foreach ( $image_ids as $id ) {
				$attachment = wp_get_attachment_image_src( $id );

				$classes = array( 'zoom' );

				if ( 0 == $loop || 0 == $loop % $columns ) {
					$classes[] = 'first';
				}

				if ( 0 == ( $loop + 1 ) % $columns ) {
					$classes[] = 'last';
				}

				$image_link = wp_get_attachment_url( $id );

				if ( ! apply_filters( 'wc_additional_variation_images_get_first_image', false ) || $this->cloud_zoom_exists() ) {
					if ( ! $image_link ) {
						continue;
					}
				}

				$gallery_image = wp_get_attachment_image( $id, apply_filters( 'single_product_small_thumbnail_size', 'shop_thumbnail' ) );
				$main_image    = wp_get_attachment_image( $id, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ) );

				$image_title = esc_attr( get_the_title( $id ) );

				// support for cloud zoom plugin
				if ( $this->cloud_zoom_exists() ) {
					$image_class      = esc_attr( implode( ' ', $classes ) . ' ' . 'cloud-zoom-gallery' );
					$pretty_photo     = 'rel="prettyPhoto"';
					$cloudmediumimage = wp_get_attachment_image_src( $id, 'shop_single' );
					$cloudzoom        = 'cloud="useZoom:\'zoom1\',smallImage:\'' . $cloudmediumimage[0] . '\'"';
				} else {
					$image_class = esc_attr( implode( ' ', $classes ) );
					$pretty_photo = 'data-rel="prettyPhoto[product-gallery]"';
					$cloudzoom   = '';
				}

				// see if we need to get the first image of the variation
				// only run one time
				if ( ( apply_filters( 'wc_additional_variation_images_get_first_image', false ) || $this->cloud_zoom_exists() ) && 0 === $loop ) {
					$main_image_title = esc_attr( get_the_title( $id ) );
					$main_image_link  = wp_get_attachment_url( $id );
					$main_image       = wp_get_attachment_image( $id, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ), false, array(
						'title' => $main_image_title,
					) );

					$main_images .= apply_filters( 'woocommerce_single_product_image_html', sprintf( '<a href="%s" itemprop="image" class="woocommerce-main-image zoom" title="%s">%s</a>', $main_image_link, $main_image_title, $main_image ), $id );

					$gallery_image_title = esc_attr( get_the_title( $id ) );
					$gallery_image_link  = wp_get_attachment_url( $id );
					$gallery_image       = wp_get_attachment_image( $id, apply_filters( 'single_product_large_thumbnail_size', 'shop_thumbnail' ), false, array(
						'title' => $gallery_image_title,
					) );

					$gallery_images .= apply_filters( 'woocommerce_single_product_image_thumbnail_html', sprintf( '<a href="%s" class="%s" title="%s" %s %s>%s</a>', $gallery_image_link, $image_class, $gallery_image_title, $pretty_photo, $cloudzoom, $gallery_image ), $id, $post_id, $image_class );

					$loop++;
					continue;
				}

				// build the list of variations as main images in case a custom theme has flexslider type lightbox
				$main_images .= apply_filters( 'woocommerce_single_product_image_html', sprintf( '<a href="%s" itemprop="image" class="woocommerce-main-image zoom" title="%s">%s</a>', $image_link, $image_title, $main_image ), $post_id );

				$gallery_images .= apply_filters( 'woocommerce_single_product_image_thumbnail_html', sprintf( '<a href="%s" class="%s" title="%s" %s %s>%s</a>', $image_link, $image_class, $image_title, $pretty_photo, $cloudzoom, $gallery_image ), $id, $post_id, $image_class );

				$loop++;
			} // End foreach().
		} // End if().

		wp_send_json(
			array(
				'main_images'    => $main_images,
				'gallery_images' => $gallery_images,
			)
		);
	}
}

new WC_Additional_Variation_Images_Frontend();
