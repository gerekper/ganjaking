<?php

class WoocommerceProductFeedsFeedImageManager {
	/**
	 * @var WoocommerceGpfDebugService
	 */
	protected $debug;
	/**
	 * @var WoocommerceGpfTemplateLoader
	 */
	protected $template;

	/**
	 * @var WoocommerceGpfCommon
	 */
	private $common;

	/**
	 * WoocommerceProductFeedsFeedImageManager constructor.
	 *
	 * @param WoocommerceGpfCommon $woocommerce_gpf_common
	 * @param WoocommerceGpfDebugService $woocommerce_gpf_debug_service
	 * @param WoocommerceGpfTemplateLoader $woocommerce_gpf_template_loader
	 */
	public function __construct(
		WoocommerceGpfCommon $woocommerce_gpf_common,
		WoocommerceGpfDebugService $woocommerce_gpf_debug_service,
		WoocommerceGpfTemplateLoader $woocommerce_gpf_template_loader
	) {
		$this->common   = $woocommerce_gpf_common;
		$this->debug    = $woocommerce_gpf_debug_service;
		$this->template = $woocommerce_gpf_template_loader;
	}

	/**
	 * Add hooks for AJAX callbacks.
	 */
	public function initialise() {
		add_action( 'wp_ajax_woo_gpf_exclude_media', [ $this, 'exclude_media' ] );
		add_action( 'wp_ajax_woo_gpf_include_media', [ $this, 'include_media' ] );
		add_action( 'wp_ajax_woo_gpf_set_primary_media', [ $this, 'set_primary_media' ] );
	}

	/**
	 * Renders the summary of images calculated for a given post.
	 *
	 * @param $post
	 */
	public function render_summary( $post ) {

		$wc_product = wc_get_product( $post );
		if ( ! $wc_product ) {
			return;
		}

		$excluded_images = $wc_product->get_meta( 'woocommerce_gpf_excluded_media_ids', true );
		if ( '' === $excluded_images ) {
			$excluded_images = [];
		}
		$primary_media_id = $wc_product->get_meta( 'woocommerce_gpf_primary_media_id', true );

		$feed_item          = new WoocommerceGpfFeedItem(
			$wc_product,
			$wc_product,
			'google',
			$this->common,
			$this->debug
		);
		$feed_item          = apply_filters( 'woocommerce_gpf_feed_item', $feed_item, $wc_product );
		$feed_item          = apply_filters( 'woocommerce_gpf_feed_item_google', $feed_item, $wc_product );
		$images_and_sources = $feed_item->get_image_sources_by_url();

		$this->template->output_template_with_variables( 'woo-gpf', 'meta-field-image-info-header', [] );
		foreach ( $feed_item->get_ordered_images() as $image ) {
			$this->output_image_source(
				$wc_product->get_id(),
				$image['url'],
				$images_and_sources,
				$excluded_images,
				$primary_media_id
			);
		}
		$this->template->output_template_with_variables( 'woo-gpf', 'meta-field-image-info-footer', [] );
	}

	/**
	 * Output an image, its sources and actions.
	 *
	 * @param int $product_id
	 * @param string $url
	 * @param array $all_images_and_sources
	 * @param array $excluded_images
	 * @param string $primary_media_id
	 */
	private function output_image_source(
		$product_id,
		$url,
		$all_images_and_sources,
		$excluded_images,
		$primary_media_id
	) {

		if ( isset( $all_images_and_sources[ $url ] ) ) {
			$images_and_sources = $all_images_and_sources[ $url ];
		} else {
			// TODO - Output something to say it's unmanaged by us?
			return;
		}

		$image_actions = $this->template->get_template_with_variables(
			'woo-gpf',
			'meta-field-image-info-include-action',
			[
				'nonce' => wp_create_nonce( 'woo_gpf_include_media' ),
			]
		);

		$image_actions .= $this->template->get_template_with_variables(
			'woo-gpf',
			'meta-field-image-info-exclude-action',
			[
				'nonce' => wp_create_nonce( 'woo_gpf_exclude_media' ),
			]
		);

		$primary_status = '';
		if ( (int) $primary_media_id === (int) $images_and_sources['id'] ) {
			$primary_status = 'woo-gpf-image-source-list-item-primary';
		}
		$image_actions       .= $this->template->get_template_with_variables(
			'woo-gpf',
			'meta-field-image-info-set-primary-action',
			[
				'nonce' => wp_create_nonce( 'woo_gpf_set_primary_media' ),
			]
		);
		$image_source_content = '<ul class="woo-gpf-image-source-source-list">';
		foreach ( $images_and_sources['sources'] as $source ) {
			switch ( $source ) {
				case 'product_image':
					$image_source_content .= '<li>' .
											 __( 'Set as product image', 'woocommerce_gpf' ) .
											 '</li>';
					break;
				case 'product_gallery':
					$image_source_content .= '<li>' .
											 __( 'Added via product gallery', 'woocommerce_gpf' ) .
											 '</li>';
					break;
				case 'attachment':
					$image_source_content .= '<li>' .
											 __( 'Attached as media to product', 'woocommerce_gpf' ) .
											 '</li>';
					break;
				default:
					$image_source_content .= '<li>' .
											 __( 'Added via filters', 'woocommerce_gpf' ) .
											 '</li>';
					break;
			}
		}
		$image_source_content .= '</ul>';

		$image = wp_get_attachment_image(
			$images_and_sources['id'],
			'thumbnail',
			false,
			[
				'class' => 'woo-gpf-image-source-image',
			]
		);

		$list_item_status = 'woo-gpf-image-source-list-item-included';
		if ( in_array( $images_and_sources['id'], $excluded_images, true ) ) {
			$list_item_status = 'woo-gpf-image-source-list-item-excluded';
		}

		$this->template->output_template_with_variables(
			'woo-gpf',
			'meta-field-image-info-item',
			[
				'product_id'       => $product_id,
				'media_id'         => $images_and_sources['id'],
				'image'            => $image,
				'image_sources'    => $image_source_content,
				'image_actions'    => $image_actions,
				'list_item_status' => $list_item_status,
				'primary_status'   => $primary_status,
			]
		);
	}

	/**
	 * AJAX Callback to handle adding an item to the list of excluded IDs.
	 */
	public function exclude_media() {
		$nonce      = ! empty( $_POST['nonce'] ) ? $_POST['nonce'] : null;
		$media_id   = ! empty( $_POST['media_id'] ) ? $_POST['media_id'] : null;
		$product_id = ! empty( $_POST['product_id'] ) ? $_POST['product_id'] : null;

		// Validate nonce
		if ( ! wp_verify_nonce( $nonce, 'woo_gpf_exclude_media' ) ) {
			die( 'Unauthorised' );
		}

		// Retrieve list of excluded IDs for this post
		$excluded_media_ids = get_post_meta( $product_id, 'woocommerce_gpf_excluded_media_ids', true );
		if ( empty( $excluded_media_ids ) ) {
			$excluded_media_ids = [];
		}
		// Add ID to list & save.
		if ( ! in_array( $media_id, $excluded_media_ids, true ) ) {
			$excluded_media_ids[] = (int) $media_id;
		}

		// Save list
		update_post_meta( $product_id, 'woocommerce_gpf_excluded_media_ids', $excluded_media_ids );

		// Make sure this isn't set as the primary, if so unset it.
		$primary_media_id = get_post_meta( $product_id, 'woocommerce_gpf_primary_media_id', true );
		if ( (int) $primary_media_id === (int) $media_id ) {
			delete_post_meta( $product_id, 'woocommerce_gpf_primary_media_id' );
		}

		// Make sure the cache is bumped.
		do_action( 'woocommerce_gpf_media_ids_updated', $product_id );

		$this->echo_image_config( $product_id );
		die();
	}

	/**
	 * AJAX Callback to handle removing an item from the list of excluded IDs.
	 */
	public function include_media() {
		$nonce      = ! empty( $_POST['nonce'] ) ? $_POST['nonce'] : null;
		$media_id   = ! empty( $_POST['media_id'] ) ? $_POST['media_id'] : null;
		$product_id = ! empty( $_POST['product_id'] ) ? $_POST['product_id'] : null;

		// Validate nonce
		if ( ! wp_verify_nonce( $nonce, 'woo_gpf_include_media' ) ) {
			die( 'Unauthorised' );
		}

		// Retrieve list of excluded IDs for this post
		$excluded_media_ids = get_post_meta( $product_id, 'woocommerce_gpf_excluded_media_ids', true );
		if ( empty( $excluded_media_ids ) ) {
			$excluded_media_ids = [];
		}
		// Remove ID from the list.
		foreach ( array_keys( $excluded_media_ids, (int) $media_id, true ) as $key ) {
			unset( $excluded_media_ids[ $key ] );
		}
		$excluded_media_ids = array_values( $excluded_media_ids );

		// Save list
		update_post_meta( $product_id, 'woocommerce_gpf_excluded_media_ids', $excluded_media_ids );
		do_action( 'woocommerce_gpf_media_ids_updated', $product_id );

		$this->echo_image_config( $product_id );
		die();
	}

	/**
	 * AJAX Callback to handle setting a media item as primary.
	 */
	public function set_primary_media() {
		$nonce      = ! empty( $_POST['nonce'] ) ? $_POST['nonce'] : null;
		$media_id   = ! empty( $_POST['media_id'] ) ? $_POST['media_id'] : null;
		$product_id = ! empty( $_POST['product_id'] ) ? $_POST['product_id'] : null;

		// Validate nonce
		if ( ! wp_verify_nonce( $nonce, 'woo_gpf_set_primary_media' ) ) {
			die( 'Unauthorised' );
		}

		// Save list
		update_post_meta( $product_id, 'woocommerce_gpf_primary_media_id', $media_id );

		do_action( 'woocommerce_gpf_media_ids_updated', $product_id );

		$this->echo_image_config( $product_id );
		die();
	}

	private function echo_image_config( $product_id ) {
		$primary_media_id   = (int) get_post_meta(
			$product_id,
			'woocommerce_gpf_primary_media_id',
			true
		);
		$excluded_media_ids = get_post_meta(
			$product_id,
			'woocommerce_gpf_excluded_media_ids',
			true
		);
		if ( empty( $excluded_media_ids ) ) {
			$excluded_media_ids = [];
		}
		echo wp_json_encode(
			[
				'excluded_media_ids' => $excluded_media_ids,
				'primary_media_id'   => $primary_media_id,
			]
		);
	}
}
