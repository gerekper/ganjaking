<?php
/**
 * Mix and Match Products compatibility class.
 *
 * @version 5.0.0
 */
class WCML_Mix_And_Match_Products implements \IWPML_Action {

	/**
	 * @var SitePress
	 */
	private $sitepress;

	/**
	 * @param SitePress $sitepress
	 */
	public function __construct( SitePress $sitepress ) {
		$this->sitepress = $sitepress;
	}

	/**
	 * Attach callbacks.
	 *
	 * @since 5.0.0
	 */
	public function add_hooks() {
		// Support MNM 2.0 custom tables, cart syncing.
		if ( is_callable( [ 'WC_MNM_Compatibility', 'is_db_version_gte' ] ) && WC_MNM_Compatibility::is_db_version_gte( '2.0' ) ) {
			add_action( 'wcml_after_sync_product_data', [ $this, 'sync_allowed_contents' ], 10, 2 );
			add_filter( 'wcml_cart_contents', [ $this, 'sync_cart' ], 10, 4 );
		} else {
			add_action( 'updated_post_meta', [ $this, 'sync_mnm_data' ], 10, 4 );
		}
	}

	/**
	 * Translate container data with translated values when the product is duplicated.
	 *
	 * Handles translating source products to the MNM custom table.
	 * Handles translating source categories as meta.
	 *
	 * @param int $container_id
	 * @param int $translated_container_id
	 */
	public function sync_allowed_contents( $container_id, $translated_container_id ) {

		if ( has_term( 'mix-and-match', 'product_type', $container_id ) ) {

			$translated_child_items = [];
			$lang                   = $this->sitepress->get_language_for_element( $translated_container_id, 'post_product' );

			/** @var WC_Product_Mix_and_Match */
			$original_product   = wc_get_product( $container_id );

			/** @var WC_Product_Mix_and_Match */
			$translated_product = wc_get_product( $translated_container_id );

			if ( $original_product ) {

				$original_child_items = $original_product->get_child_items( 'edit' );

				$translated_child_items = [];

				// Translate child items.
				if ( ! empty( $original_child_items ) ) {

					foreach ( $original_child_items as $item_key => $original_child_item ) {

						$translated_child_items[] = [
							'product_id'   => apply_filters( 'wpml_object_id', $original_child_item->get_product_id(), 'product', false, $lang ),
							'variation_id' => apply_filters( 'wpml_object_id', $original_child_item->get_variation_id(), 'product_variation', false, $lang ),
						];

					}
				}

				if ( $translated_product && ! empty( $translated_child_items ) ) {
					$translated_product->set_child_items( $translated_child_items );
				}

				// Translate child source categories.
				$original_child_cat_ids   = $original_product->get_child_category_ids( 'edit' );
				$translated_child_cat_ids = [];

				foreach ( $original_child_cat_ids as $original_cat_id ) {
					$translated_child_cat_ids[] = apply_filters( 'wpml_object_id', $original_cat_id, 'product_cat', true, $lang );
				}

				if ( $translated_product && ! empty( $translated_child_cat_ids ) ) {
					$translated_product->set_child_category_ids( $translated_child_cat_ids );
				}

				// Save product.
				$translated_product->save();

			}
		}

	}

	/**
	 * Update the cart contents to the new language
	 *
	 * @since 5.0.0
	 *
	 * @param array  $new_cart_contents
	 * @param array  $cart_contents
	 * @param string $key
	 * @param string $new_key
	 *
	 * @return array
	 */
	public function sync_cart( $new_cart_contents, $cart_contents, $key, $new_key ) {
		if ( ! function_exists( 'wc_mnm_is_container_cart_item' )
			 || ! function_exists( 'wc_mnm_get_child_cart_items' )
			 || ! function_exists( 'wc_mnm_maybe_is_child_cart_item' )
			 || ! function_exists( 'wc_mnm_get_cart_item_container' )
		) {
			return $new_cart_contents;
		}

		$current_language = $this->sitepress->get_current_language();

		// Translate container.
		if ( wc_mnm_is_container_cart_item( $new_cart_contents[ $new_key ] ) ) {

			$new_config = [];

			// Translate config.
			foreach ( $new_cart_contents[ $new_key ]['mnm_config'] as $id => $data ) {

				$tr_product_id   = apply_filters( 'wpml_object_id', $data['product_id'], 'product', false, $current_language );
				$tr_variation_id = 0;

				if ( isset( $data['variation_id'] ) && $data['variation_id'] ) {
					$tr_variation_id = apply_filters( 'wpml_object_id', $data['variation_id'], 'product_variation', false, $current_language );
				}

				$tr_child_id = $tr_variation_id ? intval( $tr_variation_id ) : intval( $tr_product_id );

				$new_config[ $tr_child_id ] = [
					'mnm_child_id' => $tr_child_id,
					'product_id'   => intval( $tr_product_id ),
					'variation_id' => intval( $tr_variation_id ),
					'quantity'     => $data['quantity'],
					'variation'    => $data['variation'], // @todo: translate attributes
				];

			}

			if ( ! empty( $new_config ) ) {
				$new_cart_contents[ $new_key ]['mnm_config'] = $new_config;
			}

			// Find all children and stash new container cart key. Need to direclty manipulate the wc()->cart as $cart_contents isn't persisted.
			foreach ( wc_mnm_get_child_cart_items( $new_cart_contents[ $new_key ] ) as $child_key => $child_item ) {
				WC()->cart->cart_contents[ $child_key ]['translated_mnm_container'] = $new_key;
			}
		}

		// Translate children.
		if ( wc_mnm_maybe_is_child_cart_item( $new_cart_contents[ $new_key ] ) ) {

			// Update the child's container and remove the stashed version.
			$new_cart_contents[ $new_key ]['mnm_container'] = $cart_contents[ $key ]['translated_mnm_container'];
			unset( $cart_contents[ $key ]['translated_mnm_container'] );

			$container_key = wc_mnm_get_cart_item_container( $new_cart_contents[ $new_key ], $new_cart_contents, true );

			if ( $container_key ) {

				// Swap keys in container's content array.
				$remove_key = array_search( $key, $new_cart_contents[ $container_key ]['mnm_contents'] );
				unset( $new_cart_contents[ $container_key ]['mnm_contents'][ $remove_key ] );
				$new_cart_contents[ $container_key ]['mnm_contents'][] = $new_key;

			}
		}

		return $new_cart_contents;
	}

	/**
	 * Translate the _mnm_data meta of child products.
	 *
	 * For Mix and Match 1.x data.
	 *
	 * @param string $meta_id
	 * @param int    $post_id
	 * @param string $meta_key
	 * @param mixed  $meta_value
	 */
	public function sync_mnm_data( $meta_id, $post_id, $meta_key, $meta_value ) {
		if ( '_mnm_data' !== $meta_key ) {
			return;
		}

		global $sitepress, $woocommerce_wpml;

		$post = get_post( $post_id );

		// Skip auto-drafts, skip autosave.
		if ( 'auto-draft' === $post->post_status || isset( $_POST['autosave'] ) ) {
			return;
		}

		if ( 'product' === $post->post_type ) {
			remove_action( 'updated_post_meta', [ $this, 'sync_mnm_data' ], 10 );

			if ( $woocommerce_wpml->products->is_original_product( $post_id ) ) {
				$original_product_id = $post_id;
			} else {
				$original_product_id = $woocommerce_wpml->products->get_original_product_id( $post_id );
			}

			$mnm_data             = maybe_unserialize( get_post_meta( $original_product_id, '_mnm_data', true ) );
			$product_trid         = $sitepress->get_element_trid( $original_product_id, 'post_product' );
			$product_translations = $sitepress->get_element_translations( $product_trid, 'post_product' );

			foreach ( $product_translations as $product_translation ) {
				if ( empty( $product_translation->original ) ) {
					foreach ( $mnm_data as $key => $mnm_element ) {

						$trnsl_prod                = apply_filters( 'translate_object_id', $key, 'product', true, $product_translation->language_code );
						$mnm_element['product_id'] = $trnsl_prod;
						$mnm_data[ $trnsl_prod ]   = $mnm_element;
						unset( $mnm_data[ $key ] );
					}

					update_post_meta( $product_translation->element_id, '_mnm_data', $mnm_data );
				}
			}

			add_action( 'updated_post_meta', [ $this, 'sync_mnm_data' ], 10, 4 );
		}
	}

}
