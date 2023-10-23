<?php
/**
 * @package Polylang-WC
 */

/**
 * Manages the products (mainly the synchronization of data).
 *
 * @since 1.3
 */
class PLLWC_Products {
	/**
	 * Product language data store.
	 *
	 * @var PLLWC_Product_Language_CPT
	 */
	protected $data_store;

	/**
	 * Temporarily stores data related to a product currently being edited.
	 *
	 * @see PLLWC_Products::store_product_data()
	 * @see PLLWC_Products::get_terms_args()
	 *
	 * @var array {
	 *     @type string|false $lang       The product's language.
	 *     @type string[]     $taxonomies Attribute taxonomies.
	 * }
	 */
	private $product_data = array(
		'lang'       => false,
		'taxonomies' => array(),
	);

	/**
	 * Constructor.
	 *
	 * @since 0.1
	 */
	public function __construct() {
		$this->data_store = PLLWC_Data_Store::load( 'product_language' );

		// Variations synchronization.
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 5, 2 );
		add_action( 'woocommerce_update_product', array( $this, 'save_product' ) );
		add_action( 'woocommerce_new_product_variation', array( $this, 'save_variation' ) );
		add_action( 'woocommerce_update_product_variation', array( $this, 'save_variation' ) );
		add_action( 'woocommerce_after_product_object_save', array( $this, 'copy_product' ) );

		// Filters `get_terms()` in `LookupDataStore::get_term_ids_by_slug_cache()`.
		add_action( 'woocommerce_before_product_object_save', array( $this, 'store_product_data' ) );
		add_filter( 'get_terms_args', array( $this, 'get_terms_args' ), 20 ); // After Polylang and Polylang Pro.
		add_action( 'woocommerce_after_product_object_save', array( $this, 'reset_product_data' ), 5 ); // Before copy_product(), not mandatory though.
		add_action( 'woocommerce_run_product_attribute_lookup_update_callback', array( $this, 'store_product_data' ), 1 ); // To set the language before WC processes the product.

		add_action( 'pll_post_synchronized', array( $this, 'synchronize_product' ), 10, 3 );
		add_action( 'pll_created_sync_post', array( $this, 'copy_variations' ), 5, 3 );

		// Variations deletion.
		add_action( 'woocommerce_before_delete_product_variation', array( $this, 'delete_variation' ) );

		// Unique SKU.
		add_filter( 'wc_product_has_unique_sku', array( $this, 'unique_sku' ), 10, 3 );

		// On sale products block.
		add_filter( 'pll_filter_query_excluded_query_vars', array( $this, 'fix_on_sale_products_block_query' ), 10, 2 );
	}

	/**
	 * Copy (create) or synchronize a variation.
	 *
	 * @since 1.0
	 *
	 * @param int    $id        Source variation product id.
	 * @param int    $tr_parent Target variable product id.
	 * @param string $lang      Target language.
	 * @return void
	 *
	 * @phpstan-param non-empty-string $lang
	 */
	protected function copy_variation( $id, $tr_parent, $lang ) {
		static $avoid_recursion = false;

		if ( $avoid_recursion ) {
			return;
		}

		$tr_id = $this->data_store->get( $id, $lang );

		if ( $tr_id === $id ) {
			return;
		}

		$variation = wc_get_product( $id );
		if ( empty( $variation ) ) {
			return;
		}

		if ( ! $tr_id ) {
			// If the product variation is untranslated, attempt to find a translation based on the attribute.
			$tr_product = wc_get_product( $tr_parent );

			if ( $tr_product instanceof WC_Product_Variable ) {
				$tr_attributes = $tr_product->get_variation_attributes();

				if ( ! empty( $tr_attributes ) ) {
					// At least one translated variation was manually created.
					$attributes = $variation->get_attributes();
					if ( ! in_array( '', $attributes ) ) {
						$attributes = $this->maybe_translate_attributes( $attributes, $lang );
						foreach ( $tr_product->get_children() as $_tr_id ) {
							$tr_variation = wc_get_product( $_tr_id );
							if ( $tr_variation && $attributes === $tr_variation->get_attributes() && empty( $this->data_store->get( $tr_variation->get_id(), $this->data_store->get_language( $id ) ) ) ) {
								$tr_id = $tr_variation->get_id();
								break;
							}
						}
					}
				}
			}

			if ( ! $tr_id ) {
				// Creates the translated product variation if it does not exist yet.
				$avoid_recursion = true;

				$tr_variation = new WC_Product_Variation();
				$tr_variation->set_parent_id( $tr_parent );
				$tr_id = $this->copy_product_props( $variation, $tr_variation, $lang );

				$avoid_recursion = false;
			}

			$this->data_store->copy( $id, $tr_id, $lang );
			$this->data_store->set_language( $tr_id, $lang );
			$translations = $this->data_store->get_translations( $id );
			$translations[ $this->data_store->get_language( $id ) ] = $id; // In case this is the first translation created.
			$translations[ $lang ] = $tr_id;
			$this->data_store->save_translations( $translations );
		} else {
			// Make sure the parent product is correct. Fixes edge case reported in #153.
			$tr_variation = new WC_Product_Variation( $tr_id );
			if ( $tr_variation->get_parent_id() !== $tr_parent ) {
				$avoid_recursion = true;

				$tr_variation->set_parent_id( $tr_parent );
				$tr_id = $tr_variation->save();

				$avoid_recursion = false;
			}

			// Synchronize.
			$this->data_store->copy( $id, $tr_id, $lang, true );
		}
	}

	/**
	 * Copy or synchronize variations.
	 *
	 * @since 0.1
	 *
	 * @param int    $from Product id from which we copy informations.
	 * @param int    $to   Product id to which we paste informations.
	 * @param string $lang Language code.
	 * @return void
	 *
	 * @phpstan-param non-empty-string $lang
	 */
	public function copy_variations( $from, $to, $lang ) {
		$product = wc_get_product( $from );

		if ( $product instanceof WC_Product_Variable ) {
			$language = $this->data_store->get_language( $from );
			if ( $language ) {
				$variations = $product->get_children(); // Note: it does not return disabled variations in WC < 3.3.

				remove_action( 'woocommerce_new_product_variation', array( $this, 'save_variation' ) ); // Avoid reverse sync.
				foreach ( $variations as $id ) {
					$this->data_store->set_language( $id, $language );
					$this->copy_variation( $id, $to, $lang );
				}
				add_action( 'woocommerce_new_product_variation', array( $this, 'save_variation' ) );
			}
		}
	}

	/**
	 * Copy variations and metas when using "Add new" ( translation )
	 * Hooked to the action 'add_meta_boxes'.
	 *
	 * @since 0.1
	 *
	 * @param string  $post_type Post type.
	 * @param WP_Post $post      Current post object.
	 * @return void
	 */
	public function add_meta_boxes( $post_type, $post ) {
		if ( 'post-new.php' === $GLOBALS['pagenow'] && isset( $_GET['from_post'], $_GET['new_lang'] ) && 'product' === $post_type ) {
			check_admin_referer( 'new-post-translation' );

			// Capability check already done in post-new.php.
			$lang = PLL()->model->get_language( sanitize_key( $_GET['new_lang'] ) ); // Make sure we have a valid language.

			if ( $lang ) {
				$this->copy_variations( (int) $_GET['from_post'], $post->ID, $lang->slug );

				/**
				 * Fires after metas and variations have been copied from a product to a translation.
				 *
				 * @since 0.5
				 *
				 * @param int    $from Original product ID.
				 * @param int    $to   Target product ID.
				 * @param string $lang Language code of the target product.
				 * @param bool   $sync True when synchronizing products, empty when creating a new translation.
				 */
				do_action( 'pllwc_copy_product', (int) $_GET['from_post'], $post->ID, $lang->slug );
			}
		}
	}

	/**
	 * Fires an action that can be used to synchronize data when a product is saved.
	 * Hooked to the action 'woocommerce_update_product'.
	 *
	 * @since 1.0
	 *
	 * @param int $id Product ID.
	 * @return void
	 */
	public function save_product( $id ) {
		$translations = $this->data_store->get_translations( $id );
		foreach ( $translations as $lang => $tr_id ) {
			if ( $id !== $tr_id ) {
				// It's useless to copy variations if we already did it by saving variations before.
				if ( ! did_action( 'woocommerce_update_product_variation' ) && ! did_action( 'woocommerce_new_product_variation' ) ) {
					$this->copy_variations( $id, $tr_id, $lang );
				}

				/** This action is documented in admin/admin-products.php */
				do_action( 'pllwc_copy_product', $id, $tr_id, $lang, true );
			}
		}
	}

	/**
	 * Sets the variation language and synchronizes it with its translations.
	 * Hooked to the action 'woocommerce_new_product_variation' and 'woocommerce_update_product_variation'.
	 *
	 * @since 1.0
	 *
	 * @param int $id Variation product id.
	 * @return void
	 */
	public function save_variation( $id ) {
		static $avoid_recursion = false;

		if ( ! doing_action( 'woocommerce_product_duplicate' ) && ! doing_action( 'wp_ajax_woocommerce_do_ajax_product_import' ) && ! $avoid_recursion ) {
			$avoid_recursion = true;

			if ( $variation = wc_get_product( $id ) ) {
				$pid = $variation->get_parent_id();
				$language = $this->data_store->get_language( $pid );

				if ( $language ) {
					$this->data_store->set_language( $id, $language );

					foreach ( $this->data_store->get_translations( $pid ) as $lang => $tr_pid ) {
						if ( $tr_pid !== $pid ) {
							$this->copy_variation( $id, $tr_pid, $lang );
						}
					}
				}
			}
		}
		$avoid_recursion = false;
	}

	/**
	 * Synchronizes variations deletion.
	 * Hooked to the action 'woocommerce_before_delete_product_variation'.
	 *
	 * @since 1.0
	 *
	 * @param int $id Variation product id.
	 * @return void
	 */
	public function delete_variation( $id ) {
		static $avoid_delete = array();

		if ( ! doing_action( 'delete_post' ) && ! in_array( $id, $avoid_delete ) ) {
			$tr_ids = $this->data_store->get_translations( $id );
			$avoid_delete = array_merge( $avoid_delete, array_values( $tr_ids ) ); // To avoid deleting a variation two times.
			foreach ( $tr_ids as $tr_id ) {
				if ( $variation = wc_get_product( $tr_id ) ) {
					$variation->delete( true );
				}
			}
		}
	}

	/**
	 * Checks whether two products are synchronized.
	 *
	 * @since 1.2
	 *
	 * @param int $id       ID of the first product to compare.
	 * @param int $other_id ID of the second product to compare.
	 * @return bool
	 */
	protected static function are_synchronized( $id, $other_id ) {
		return isset( PLL()->sync_post->sync_model ) && PLL()->sync_post->sync_model->are_synchronized( $id, $other_id );
	}

	/**
	 * Determines whether texts should be copied depending on duplicate and synchronization options.
	 *
	 * @since 1.0
	 *
	 * @param int  $from Product id from which we copy informations.
	 * @param int  $to   Product id which we paste informations.
	 * @param bool $sync True if it is synchronization, false if it is a copy.
	 * @return bool
	 */
	public static function should_copy_texts( $from, $to, $sync ) {
		if ( ! $sync ) {
			$duplicate_options = get_user_meta( get_current_user_id(), 'pll_duplicate_content', true );
			if ( ! empty( $duplicate_options ) && ! empty( $duplicate_options['product'] ) ) {
				return true;
			}
		}

		if ( isset( PLL()->sync_post ) ) {
			$from = wc_get_product( $from );
			$to   = wc_get_product( $to );

			if ( ! empty( $from ) && ! empty( $to ) ) {
				if ( 'variation' === $from->get_type() ) {
					return self::are_synchronized( $from->get_parent_id(), $to->get_parent_id() );
				} else {
					return self::are_synchronized( $from->get_id(), $to->get_id() );
				}
			}
		}

		return false;
	}

	/**
	 * Maybe translates a product property.
	 *
	 * @since 1.0
	 * @since 1.8 Type-hinted parameters `$prop` and `$lang`.
	 *
	 * @param mixed  $value Property value.
	 * @param string $prop  Property name.
	 * @param string $lang  Language code.
	 * @return mixed Property value, possibly translated.
	 */
	public static function maybe_translate_property( $value, string $prop, string $lang ) {
		$tr_value = $value;

		switch ( $prop ) {
			case 'image_id':
				if ( ! PLL()->options['media_support'] || empty( $value ) ) {
					break;
				}

				$tr_value = pll_get_post( $value, $lang );

				if ( empty( $tr_value ) ) {
					$tr_value = PLL()->posts->create_media_translation( $value, $lang );
				}
				break;

			case 'gallery_image_ids':
				if ( ! PLL()->options['media_support'] ) {
					break;
				}

				$tr_value = array();

				foreach ( array_map( 'absint', explode( ',', $value ) ) as $post_id ) {
					if ( empty( $post_id ) ) {
						continue;
					}

					$tr_id = pll_get_post( $post_id, $lang );

					if ( empty( $tr_id ) ) {
						$tr_id = PLL()->posts->create_media_translation( $post_id, $lang );
					}

					$tr_value[] = $tr_id;
				}

				$tr_value = implode( ',', $tr_value );
				break;

			case 'children':
			case 'upsell_ids':
			case 'cross_sell_ids':
				/** @var PLLWC_Product_Language_CPT */
				$data_store = PLLWC_Data_Store::load( 'product_language' );
				$tr_value   = array();

				foreach ( $value as $id ) {
					$tr_id = $data_store->get( $id, $lang );

					if ( empty( $tr_id ) ) {
						continue;
					}
					$tr_value[] = $tr_id;
				}
				break;

			case 'default_attributes':
			case 'attributes':
				if ( is_array( $value ) ) {
					$tr_value = self::maybe_translate_attributes( $value, $lang );
				}
				break;
		}

		/**
		 * Filter a property value before it is copied or synchronized
		 * but after it has been maybe translated.
		 *
		 * @since 1.0
		 *
		 * @param mixed  $value Product property value.
		 * @param string $prop  Product property name.
		 * @param string $lang  Language code.
		 */
		return apply_filters( 'pllwc_translate_product_prop', $tr_value, $prop, $lang );
	}

	/**
	 * Translates taxonomy attributes.
	 *
	 * @since 1.0
	 * @since 1.8 Now public and static.
	 * @since 1.8 Accepts both an array of attributes terms slugs and `WC_Product_Attribute` objects.
	 *
	 * @param array  $attributes Product attributes. Could be pairs of attribute taxonomies and term slugs (from `WC_Product_Variation`)
	 *                           or a list of `WC_Product_Attribute` objects (from other kind of `WC_Product`).
	 * @param string $lang       Language code.
	 * @return array Array of translated attributes with preserved keys.
	 *
	 * @phpstan-param array<string|WC_Product_Attribute> $attributes
	 * @phpstan-return array<string|WC_Product_Attribute>
	 */
	public static function maybe_translate_attributes( $attributes, $lang ) {
		foreach ( $attributes as $k => $v ) {
			switch ( gettype( $v ) ) {
				/*
				 * Current attribute refers to `WC_Product_Variation::$data['attributes']`.
				 * See: {https://github.com/woocommerce/woocommerce/blob/7.3.0/plugins/woocommerce/includes/class-wc-product-variation.php#L505-L523}
				 */
				case 'string':
					$attributes_taxonomies = wc_get_attribute_taxonomy_names();
					if ( ! in_array( $k, $attributes_taxonomies ) ) {
						break;
					}

					if ( '' === $v ) { // Correspond to attribute value "Any".
						$attributes[ $k ] = '';
						break;
					}

					$terms = get_terms( array( 'taxonomy' => $k, 'slug' => $v, 'hide_empty' => false, 'lang' => '' ) ); // Don't use get_term_by filtered by language since WP 4.7.

					if ( ! is_array( $terms ) ) {
						break;
					}

					$term = reset( $terms );

					if ( ! $term instanceof WP_Term ) {
						break;
					}

					$tr_id = pll_get_term( $term->term_id, $lang );

					if ( empty( $tr_id ) ) {
						break;
					}

					$tr_term = get_term( $tr_id, $k );

					if ( ! $tr_term instanceof WP_Term ) {
						break;
					}

					$attributes[ $k ] = $tr_term->slug;
					break;

				/*
				 * Current attribute refers to `WC_Product::$data['attributes']`.
				 * See: {https://github.com/woocommerce/woocommerce/blob/7.3.0/plugins/woocommerce/includes/abstracts/abstract-wc-product.php#L1095-L1120}
				 */
				case 'object':
					if ( ! $v instanceof WC_Product_Attribute || ! $v->is_taxonomy() ) {
						break;
					}

					$terms = $v->get_terms();

					if ( empty( $terms ) ) {
						break;
					}

					$tr_ids = array();

					foreach ( $terms as $term ) {
						$tr_id = pll_get_term( $term->term_id, $lang );

						if ( empty( $tr_id ) ) {
							continue;
						}

						$tr_ids[] = $tr_id;
					}

					$v->set_options( $tr_ids );
					break;
			}
		}

		return $attributes;
	}

	/**
	 * Filters wc_product_has_unique_sku.
	 *
	 * @since 0.7
	 *
	 * @param bool   $sku_found  True if the SKU is already associated to an existing product, false otherwise.
	 * @param int    $product_id Product ID.
	 * @param string $sku        Product SKU.
	 * @return bool
	 */
	public function unique_sku( $sku_found, $product_id, $sku ) {
		if ( $sku_found ) {
			$language = $this->data_store->get_language( $product_id );

			/**
			 * Filter the language used to filter wc_product_has_unique_sku
			 *
			 * @since 0.9
			 *
			 * @param PLL_Language $language   Language.
			 * @param int          $product_id Product ID.
			 */
			$language = apply_filters( 'pllwc_language_for_unique_sku', $language, $product_id );

			if ( $language ) {
				return $this->data_store->is_existing_sku( $product_id, $sku, $language );
			}
		}
		return $sku_found;
	}

	/**
	 * Make sure that the on sale products block is filtered by the current language.
	 *
	 * @since 1.3
	 *
	 * @param string[] $excludes Query vars excluded from the language filter.
	 * @param WP_Query $query    WP Query object.
	 * @return string[]
	 */
	public function fix_on_sale_products_block_query( $excludes, $query ) {
		$q = &$query->query;

		if ( isset( $q['post_type'], $q['post__in'] ) && 'product' === $q['post_type'] && array_merge( array( 0 ), wc_get_product_ids_on_sale() ) === $q['post__in'] ) {
			$excludes = array_diff( $excludes, array( 'post__in' ) );
		}
		return $excludes;
	}

	/**
	 * Temporarily stores data related to a product currently being saved.
	 * The aim is to use this data to identify the corresponding arguments of the `get_terms()` used in
	 * `\Automattic\WooCommerce\Internal\ProductAttributesLookup\LookupDataStore::get_term_ids_by_slug_cache()`, and add
	 * the product's language to it.
	 * Hooked to `woocommerce_before_product_object_save`.
	 *
	 * @since 1.8
	 * @see PLLWC_Products::get_terms_args()
	 *
	 * @param WC_Product|int $product A product being saved.
	 * @return void
	 */
	public function store_product_data( $product ) {
		if ( ! $product instanceof WC_Product ) {
			$product = wc_get_product( $product );
		}

		if ( ! $product instanceof WC_Product ) {
			return;
		}

		$lang = $this->data_store->get_language( $product->get_id() );

		if ( empty( $lang ) ) {
			$lang = $this->data_store->get_language( $product->get_parent_id() );

			if ( empty( $lang ) ) {
				$this->reset_product_data();
				return;
			}
		}

		$attributes = $product->get_attributes();

		if ( empty( $attributes ) ) {
			$this->reset_product_data();
			return;
		}

		$this->product_data = array(
			'lang'       => $lang,
			'taxonomies' => array_map( 'wc_sanitize_taxonomy_name', array_keys( $attributes ) ),
		);
	}

	/**
	 * Filters the product attributes per language.
	 * The target is the `get_terms()` used in `LookupDataStore::get_term_ids_by_slug_cache()`.
	 * Hooked to `get_terms_args`.
	 *
	 * @since 1.8
	 * @see \Automattic\WooCommerce\Internal\ProductAttributesLookup\LookupDataStore::get_term_ids_by_slug_cache()
	 *
	 * @param array $args Arguments passed to WP_Term_Query.
	 * @return array Modified arguments.
	 */
	public function get_terms_args( $args ) {
		if ( empty( $this->product_data['lang'] ) ) {
			// No language to add.
			return $args;
		}
		if ( empty( $args['fields'] ) || 'id=>slug' !== $args['fields'] ) {
			// Not the arguments we're looking for.
			return $args;
		}
		if ( empty( $args['taxonomy'] ) || ! is_array( $args['taxonomy'] ) || 1 !== count( $args['taxonomy'] ) ) {
			// Not the arguments we're looking for.
			return $args;
		}
		if ( 0 !== strpos( reset( $args['taxonomy'] ), 'pa_' ) ) {
			// Not the taxonomy we're looking for.
			return $args;
		}
		if ( empty( array_intersect( $this->product_data['taxonomies'], $args['taxonomy'] ) ) ) {
			// Not the arguments we're looking for.
			return $args;
		}

		$args['lang'] = $this->product_data['lang'];

		return $args;
	}

	/**
	 * Resets the data related to a product after it has been saved.
	 * Hooked to `woocommerce_after_product_object_save`.
	 *
	 * @since 1.8
	 * @see PLLWC_Products::store_product_data()
	 *
	 * @return void
	 */
	public function reset_product_data() {
		$this->product_data = array(
			'lang'       => false,
			'taxonomies' => array(),
		);
	}

	/**
	 * Synchronizes product properties through all its translations.
	 * The goal is also to trigger the product attributes lookup table update.
	 * See https://github.com/woocommerce/woocommerce/blob/7.4.1/plugins/woocommerce/includes/abstracts/abstract-wc-product.php#L1428-L1431
	 *
	 * @since 1.8
	 *
	 * @param WC_Product $product The product that has been just saved.
	 * @return void
	 */
	public function copy_product( $product ) {
		static $avoid_recursion = false;

		if ( $avoid_recursion ) {
			return;
		}

		$avoid_recursion = true;

		$translations = $this->data_store->get_translations( $product->get_id() );

		foreach ( $translations as $lang => $translation ) {
			if ( $product->get_id() === $translation ) {
				continue;
			}
			$translated_product = wc_get_product( $translation );
			if ( empty( $translated_product ) ) {
				continue;
			}
			$this->copy_product_props( $product, $translated_product, $lang );
		}
		$avoid_recursion = false;
	}

	/**
	 * Copies product properties in its translation after it is duplicated or synchronized and save changes.
	 *
	 * @since 1.8
	 *
	 * @param WC_Product $from The product we copy information from.
	 * @param WC_Product $to   The target product.
	 * @param string     $lang Language of the target product.
	 * @return int The id of the target product.
	 */
	public function copy_product_props( $from, $to, $lang ) {
		$stock_status = $from->get_stock_status();
		$attributes   = $from->get_attributes();

		$target_attributes = $this->maybe_translate_attributes( $attributes, $lang );
		$to->set_stock_status( $stock_status );
		$to->set_attributes( $target_attributes );
		$to->save();

		return $to->get_id();
	}

	/**
	 * Synchronizes product properties in its translation after it is duplicated or synchronized.
	 *
	 * @since 1.8
	 *
	 * @param int    $from Id of the product from which we copy informations.
	 * @param int    $to   Id of the target.
	 * @param string $lang Language of the target post.
	 * @return void
	 */
	public function synchronize_product( $from, $to, $lang ) {
		$product = wc_get_product( $from );
		$target_product = wc_get_product( $to );

		if ( empty( $product ) || empty( $target_product ) ) {
			return;
		}
		$this->copy_product_props( $product, $target_product, $lang );
	}
}
