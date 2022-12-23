<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * WooCommerce Photography Admin.
 *
 * @package  WC_Photography/Admin
 * @category Class
 * @author   WooThemes
 */
class WC_Photography_Admin {

	/**
	 * Initialize the admin customers actions.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'includes' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 10 );
		add_action( 'admin_menu', array( $this, 'remove_top_level_menu_item' ), 100 );
		add_action( 'parent_file', array( $this, 'fix_collections_menu' ) );
		add_filter( 'woocommerce_screen_ids', array( $this, 'screen_ids' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'add_attachment', array( $this, 'attachment_custom_field' ) );
		add_action( 'woocommerce_product_bulk_edit_save', array( $this, 'bulk_edit_save_meta' ), 10 );
		add_action( 'wc_photography_save_collection_fields', 'wc_photography_clear_collection_cache' );
	}

	/**
	 * Include any classes we need within admin.
	 *
	 * @return void
	 */
	public function includes() {
		include_once 'class-wc-photography-admin-customers.php';
		include_once 'class-wc-photography-admin-collections.php';
		include_once 'class-wc-photography-admin-settings.php';
	}

	/**
	 * Register menus.
	 *
	 * @return void
	 */
	public function admin_menu() {
		add_menu_page(
			__( 'Photography', 'woocommerce-photography' ),
			__( 'Photography', 'woocommerce-photography' ),
			'manage_photography',
			'wc-photography',
			'__return_false',
			'dashicons-camera',
			'55.9'
		);

		add_submenu_page(
			'wc-photography',
			__( 'Collections', 'woocommerce-photography' ),
			__( 'Collections', 'woocommerce-photography' ),
			'manage_photography',
			'edit-tags.php?taxonomy=images_collections&post_type=product'
		);

		add_submenu_page(
			'wc-photography',
			__( 'Add Photographs', 'woocommerce-photography' ),
			__( 'Add Photographs', 'woocommerce-photography' ),
			'manage_photography',
			'wc-photography-batch-upload',
			array( $this, 'page_batch_upload' )
		);

		add_submenu_page(
			'wc-photography',
			__( 'Photography Settings', 'woocommerce-photography' ),
			__( 'Settings', 'woocommerce-photography' ),
			'manage_woocommerce',
			'wc-photography-settings',
			array( $this, 'page_settings' )
		);
	}

	/**
	 * Remove the "Photography" menu item.
	 *
	 * @return void
	 */
	public function remove_top_level_menu_item() {
		global $submenu;

		if ( isset( $submenu['wc-photography'] ) ) {
			foreach ( $submenu['wc-photography'] as $key => $value ) {
				if ( 'wc-photography' == $value[2] ) {
					unset( $submenu['wc-photography'][ $key ] );
					return;
				}
			}
		}
	}

	/**
	 * Fix collections menu.
	 *
	 * @param  string $parent_file
	 *
	 * @return string
	 */
	public function fix_collections_menu( $parent_file ) {
		global $submenu_file;
		$screen = get_current_screen();

		if ( 'images_collections' === $screen->taxonomy && 'edit.php?post_type=product' === $parent_file ) {
			$parent_file  = 'wc-photography';
			$submenu_file = 'edit-tags.php?taxonomy=images_collections&post_type=product';
		}

		return $parent_file;
	}

	/**
	 * Batch Upload page.
	 *
	 * @return string
	 */
	public function page_batch_upload() {
		$max_upload_size = wp_max_upload_size();
		if ( ! $max_upload_size ) {
			$max_upload_size = 0;
		}

		include_once 'views/html-batch-upload.php';
	}

	/**
	 * Settings page.
	 *
	 * @return string
	 */
	public function page_settings() {
		include_once 'views/html-settings.php';
	}

	/**
	 * Add screen ID.
	 *
	 * @param  array $ids
	 *
	 * @return array
	 */
	public function screen_ids( $screen_ids ) {
		$prefix       = sanitize_title( __( 'Photography', 'woocommerce-photography' ) );
		$screen_ids[] = $prefix . '_page_wc-photography-batch-upload';
		$screen_ids[] = $prefix . '_page_wc-photography-settings';

		return $screen_ids;
	}

	/**
	 * Get plupload args.
	 *
	 * @return array
	 */
	public function get_plupload_args() {
		$args = array(
			'runtimes'            => 'html5,silverlight,flash,html4',
			'browse_button'       => 'wc-photography-uploader-browse-button',
			'container'           => 'wc-photography-uploader-upload-ui',
			'drop_element'        => 'wc-photography-drag-drop-area',
			'file_data_name'      => 'async-upload',
			'multiple_queues'     => true,
			'max_file_size'       => wp_max_upload_size() . 'b',
			'url'                 => admin_url( 'async-upload.php' ),
			'flash_swf_url'       => includes_url( 'js/plupload/plupload.flash.swf' ),
			'silverlight_xap_url' => includes_url( 'js/plupload/plupload.silverlight.xap' ),
			'filters'             => array(
				array(
					'title'      => __( 'Allowed Files', 'woocommerce-photography' ),
					'extensions' => 'jpg,jpeg,gif,png',
				),
			),
			'multipart'           => true,
			'urlstream_upload'    => true,
			'multipart_params'    => array(
				'post_id'  => 0,
				'_wpnonce' => wp_create_nonce( 'media-form' ),
				'type'     => '',
				'tab'      => '',
				'short'    => 3,
			),
			'resize'              => false,
		);

		if ( wp_is_mobile() ) {
			$args['multi_selection'] = false;
		}

		return apply_filters( 'plupload_init', $args );
	}

	/**
	 * Enqueue scripts.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		$screen        = get_current_screen();
		$screen_prefix = sanitize_title( __( 'Photography', 'woocommerce-photography' ) );
		$suffix        = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		// Products list screen
		if ( 'edit-product' == $screen->id ) {
			wp_enqueue_style( 'wc-photography-admin', WC_Photography::get_assets_url() . 'css/admin.css', array(), WC_PHOTOGRAPHY_VERSION, 'all' );
		}

		// Uploader screen
		if ( $screen->id === $screen_prefix . '_page_wc-photography-batch-upload' ) {
			// Media libs.
			wp_enqueue_media();

			// Accounting.
			wp_enqueue_script( 'accounting' );

			// Batch upload.
			wp_enqueue_style( 'wc-photography-collections-field-styles', WC_Photography::get_assets_url() . 'css/collections-field.css', array(), WC_PHOTOGRAPHY_VERSION );
			wp_enqueue_script( 'wc-photography-batch-upload', WC_Photography::get_assets_url() . 'js/admin/batch-upload' . $suffix . '.js', array( 'jquery', 'plupload-handlers', 'jquery-ui-sortable', 'accounting', 'underscore', 'select2' ), WC_PHOTOGRAPHY_VERSION, true );
			wp_enqueue_style( 'wc-photography-batch-upload', WC_Photography::get_assets_url() . 'css/batch-upload.css', array(), WC_PHOTOGRAPHY_VERSION, 'all' );

			wp_localize_script(
				'wc-photography-batch-upload',
				'WCPhotographyBatchUploadParams',
				array(
					'ajax_url'                 => admin_url( 'admin-ajax.php' ),
					'plupload'                 => $this->get_plupload_args(),
					'batch_upload_nonce'       => wp_create_nonce( 'wc_photography_batch_upload_nonce' ),
					'search_collections_nonce' => wp_create_nonce( 'wc_photography_search_collections_nonce' ),
					'add_collection_nonce'     => wp_create_nonce( 'wc_photography_add_collection_nonce' ),
					'delete_image_nonce'       => wp_create_nonce( 'wc_photography_delete_image_nonce' ),
					'save_images_nonce'        => wp_create_nonce( 'wc_photography_save_images_nonce' ),
					'search_placeholder'       => __( 'Search for a collection&hellip;', 'woocommerce-photography' ),
					'loading'                  => __( 'Loading&hellip;', 'woocommerce-photography' ),
					'collection_error'         => __( 'An error occurred while creating the collection! Please try again.', 'woocommerce-photography' ),
					'edit_success_message'     => __( 'Photographs edited successfully!', 'woocommerce-photography' ),
					'ajax_loading_image'       => '', // Deprecated.
					'isLessThanWC30'           => false, // Deprecated.
				)
			);
		} // End if().

		// User screen
		if ( 'user' == $screen->id && 'add' == $screen->action || 'profile' == $screen->id || 'user-edit' == $screen->id ) {
			wp_enqueue_style( 'wc-photography-collections-field-styles', WC_Photography::get_assets_url() . 'css/collections-field.css', array(), WC_PHOTOGRAPHY_VERSION );
			wp_enqueue_script( 'wc-photography-customers', WC_Photography::get_assets_url() . 'js/admin/customers' . $suffix . '.js', array( 'jquery', 'select2' ), WC_PHOTOGRAPHY_VERSION, true );
			wp_enqueue_style( 'woocommerce-admin-styles', WC()->plugin_url() . '/assets/css/admin.css' );

			wp_localize_script(
				'wc-photography-customers',
				'WCPhotographyCustomerParams',
				array(
					'ajax_url'                 => admin_url( 'admin-ajax.php' ),
					'search_collections_nonce' => wp_create_nonce( 'wc_photography_search_collections_nonce' ),
					'add_collection_nonce'     => wp_create_nonce( 'wc_photography_add_collection_nonce' ),
					'search_placeholder'       => __( 'Search for a collection&hellip;', 'woocommerce-photography' ),
					'loading'                  => __( 'Loading&hellip;', 'woocommerce-photography' ),
					'collection_error'         => __( 'An error occurred while creating the collection! Please try again.', 'woocommerce-photography' ),
					'isLessThanWC30'           => false, // Deprecated.
				)
			);
		}

		// Product screen
		if ( 'product' == $screen->id ) {
			wp_enqueue_script( 'wc-photography-admin-products', WC_Photography::get_assets_url() . 'js/admin/product' . $suffix . '.js', array( 'jquery' ), WC_PHOTOGRAPHY_VERSION, true );
		}

		if ( 'images_collections' == $screen->taxonomy ) {
			wp_enqueue_media();
			wp_enqueue_style( 'wc-photography-admin-collections', WC_Photography::get_assets_url() . 'css/collections.css', array(), WC_PHOTOGRAPHY_VERSION, 'all' );
			wp_enqueue_script( 'wc-photography-admin-collections', WC_Photography::get_assets_url() . 'js/admin/collections' . $suffix . '.js', array( 'jquery' ), WC_PHOTOGRAPHY_VERSION, true );
			wp_localize_script(
				'wc-photography-admin-collections',
				'WCPhotographyAdminCollectionsParams',
				array(
					'upload_title' => __( 'Choose an image', 'woocommerce-photography' ),
					'upload_use'   => __( 'Use image', 'woocommerce-photography' ),
					'placeholder'  => wc_placeholder_img_src(),
				)
			);
		}
	}

	/**
	 * Attachment custom field.
	 *
	 * @param  string $attachment_id
	 *
	 * @return void
	 */
	public function attachment_custom_field( $attachment_id ) {
		$attachment = get_post( $attachment_id );
		$post       = get_post( $attachment->post_parent );

		// Chec if has a parent.
		if ( is_wp_error( $post ) || ! $post ) {
			return;
		}

		$product_type   = get_the_terms( $post->ID, 'product_type' );
		$is_photography = false;

		// Check if is a photography.
		if ( is_wp_error( $product_type ) || ! $product_type ) {
			return;
		}

		foreach ( $product_type as $key => $value ) {
			if ( 'photography' == $value->slug ) {
				$is_photography = true;
				break;
			}
		}

		if ( ! $is_photography ) {
			return;
		}

		update_post_meta( $attachment->ID, '_is_photography_attachment', true );
	}


	/**
	 * Calculate and set a photo item's prices when edited via the bulk edit
	 *
	 * @param object $product An instance of a WC_Product_* object.
	 */
	public function bulk_edit_save_meta( $product ) {
		if ( ! $product->is_type( 'photography' ) ) {
			return;
		}

		$price_changed = false;

		$old_regular_price = $product->get_regular_price();
		$old_sale_price    = $product->get_sale_price();

		// copy from subs & wc-admin-post-types
		// see https://github.com/woothemes/woocommerce/pull/9684
		if ( ! empty( $_REQUEST['change_regular_price'] ) && isset( $_REQUEST['_regular_price'] ) ) {

			$change_regular_price = absint( $_REQUEST['change_regular_price'] );
			$regular_price        = esc_attr( stripslashes( $_REQUEST['_regular_price'] ) );

			switch ( $change_regular_price ) {
				case 1:
					$new_price = $regular_price;
					break;
				case 2:
					if ( strstr( $regular_price, '%' ) ) {
						$percent   = str_replace( '%', '', $regular_price ) / 100;
						$new_price = $old_regular_price + ( $old_regular_price * $percent );
					} else {
						$new_price = $old_regular_price + $regular_price;
					}
					break;
				case 3:
					if ( strstr( $regular_price, '%' ) ) {
						$percent   = str_replace( '%', '', $regular_price ) / 100;
						$new_price = $old_regular_price - ( $old_regular_price * $percent );
					} else {
						$new_price = $old_regular_price - $regular_price;
					}
					break;
			}

			if ( isset( $new_price ) && $new_price != $old_regular_price ) {
				$price_changed = true;
				update_post_meta( $product->get_id(), '_regular_price', $new_price );
				update_post_meta( $product->get_id(), '_subscription_price', $new_price );

				$product->set_regular_price( $new_price );
			}
		} // End if().

		if ( ! empty( $_REQUEST['change_sale_price'] ) && isset( $_REQUEST['_sale_price'] ) ) {

			$change_sale_price = absint( $_REQUEST['change_sale_price'] );
			$sale_price        = esc_attr( stripslashes( $_REQUEST['_sale_price'] ) );

			switch ( $change_sale_price ) {
				case 1:
					$new_price = $sale_price;
					break;
				case 2:
					if ( strstr( $sale_price, '%' ) ) {
						$percent   = str_replace( '%', '', $sale_price ) / 100;
						$new_price = $old_sale_price + ( $old_sale_price * $percent );
					} else {
						$new_price = $old_sale_price + $sale_price;
					}
					break;
				case 3:
					if ( strstr( $sale_price, '%' ) ) {
						$percent   = str_replace( '%', '', $sale_price ) / 100;
						$new_price = $old_sale_price - ( $old_sale_price * $percent );
					} else {
						$new_price = $old_sale_price - $sale_price;
					}
					break;
				case 4:
					if ( strstr( $sale_price, '%' ) ) {
						$percent   = str_replace( '%', '', $sale_price ) / 100;
						$new_price = $product->get_regular_price() - ( $product->get_regular_price() * $percent );
					} else {
						$new_price = $product->get_regular_price() - $sale_price;
					}
					break;
			}

			if ( isset( $new_price ) && $new_price != $old_sale_price ) {
				$price_changed = true;
				update_post_meta( $product->get_id(), '_sale_price', $new_price );
				$product->set_sale_price( $new_price );
			}
		} // End if().

		if ( $price_changed ) {
			update_post_meta( $product->get_id(), '_sale_price_dates_from', '' );
			update_post_meta( $product->get_id(), '_sale_price_dates_to', '' );

			if ( $product->get_regular_price() < $product->get_sale_price() ) {
				$product->set_sale_price( '' );

				update_post_meta( $product->get_id(), '_sale_price', '' );
			}

			if ( $product->get_sale_price() ) {
				update_post_meta( $product->get_id(), '_price', $product->get_sale_price() );
			} else {
				update_post_meta( $product->get_id(), '_price', $product->get_regular_price() );
			}
		}

	}

}

new WC_Photography_Admin();
