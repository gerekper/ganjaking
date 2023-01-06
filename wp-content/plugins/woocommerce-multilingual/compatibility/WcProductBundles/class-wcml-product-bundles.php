<?php

use WCML\Options\WPML;
use WPML\FP\Fns;
use WPML\FP\Obj;
use WPML\FP\Str;

class WCML_Product_Bundles implements \IWPML_Action {

	const META_SELL_IDS       = '_wc_pb_bundle_sell_ids';
	const META_SELLS_TITLE    = '_wc_pb_bundle_sells_title';
	const META_SELLS_DISCOUNT = '_wc_pb_bundle_sells_discount';

	/**
	 * @var WPML_Element_Translation_Package
	 */
	public $tp;

	/**
	 * @var SitePress
	 */
	private $sitepress;

	/**
	 * @var woocommerce_wpml
	 */
	private $woocommerce_wpml;

	/**
	 * @var WCML_WC_Product_Bundles_Items
	 */
	private $product_bundles_items;

	/**
	 * @var wpdb
	 */
	private $wpdb;

	/**
	 * WCML_Product_Bundles constructor.
	 */
	public function __construct( SitePress $sitepress, woocommerce_wpml $woocommerce_wpml, $product_bundles_items, wpdb $wpdb ) {
		$this->sitepress             = $sitepress;
		$this->woocommerce_wpml      = $woocommerce_wpml;
		$this->product_bundles_items = $product_bundles_items;
		$this->wpdb                  = $wpdb;
	}

	public function add_hooks() {
		add_action( 'woocommerce_get_cart_item_from_session', [ $this, 'resync_bundle' ], 5, 3 );
		add_filter( 'woocommerce_cart_loaded_from_session', [ $this, 'resync_bundle_clean' ], 10 );

		if ( WPML::useAte() ) {
			add_action( 'wpml_pro_translation_completed', [ $this, 'save_product_bundles_to_translation' ], 10, 3 );
		} else { // Legacy action for CTE
			add_action( 'wpml_translation_job_saved', [ $this, 'save_bundle_data_translation' ], 10, 3 );
		}

		if ( is_admin() ) {
			$this->tp = new WPML_Element_Translation_Package();

			add_filter(
				'wpml_tm_translation_job_data',
				[
					$this,
					'append_bundle_data_translation_package',
				],
				10,
				2
			);

			if ( ! WPML::useAte() ) {  // Legacy actions/filters for CTE
				add_action( 'wcml_gui_additional_box_html', [ $this, 'custom_box_html' ], 10, 3 );
				add_filter( 'wcml_gui_additional_box_data', [ $this, 'custom_box_html_data' ], 10, 4 );
				add_action( 'wcml_update_extra_fields', [ $this, 'bundle_update' ], 10, 4 );
			}

			add_action( 'wcml_after_duplicate_product_post_meta', [ $this, 'sync_bundled_ids' ], 10, 2 );

			add_action( 'wp_insert_post', [ $this, 'sync_product_bundle_meta_with_translations' ], 10 );

			add_filter( 'woocommerce_json_search_found_products', [ $this, 'woocommerce_json_search_filter_found_products' ] );

			add_action( 'woocommerce_before_delete_bundled_item', [ $this, 'delete_bundled_item_relationship' ] );
		}

		add_action( 'init', [ $this, 'upgrade_bundles_items_relationships' ] );
		add_filter( 'wpml_custom_field_values_for_post_signature', [ __CLASS__, 'adjust_bundle_sells_product_signature' ], 10, 2 );
	}

	/**
	 * @param string|int $bundle_id
	 *
	 * @return array
	 */
	private function get_product_bundle_data( $bundle_id ) {
		$product_bundle_data = [];

		$bundle_items = $this->product_bundles_items->get_items( $bundle_id );
		foreach ( $bundle_items as $key => $bundle_item ) {
			$product_bundle_data[ $bundle_item->item_id ] = $this->product_bundles_items->get_item_data( $bundle_item );
		}

		return $product_bundle_data;
	}

	/**
	 * @param string|int $bundle_id
	 * @param array      $product_bundle_data
	 */
	private function save_product_bundle_data( $bundle_id, $product_bundle_data ) {
		self::flush_bundle_cache( $bundle_id );

		$bundle_items = $this->product_bundles_items->get_items( $bundle_id );

		foreach ( $bundle_items as $item_id => $bundle_item ) {
			$bundled_item_data = $this->product_bundles_items->get_item_data_object( $item_id );

			foreach ( (array) Obj::prop( $item_id, (array) $product_bundle_data ) as $key => $value ) {
				$this->product_bundles_items->update_item_meta( $bundled_item_data, $key, $value );
			}
			$this->product_bundles_items->save_item_meta( $bundled_item_data );
		}

	}

	/**
	 * @see \WC_Product_Bundle::get_bundled_data_items
	 *
	 * @param string|int $bundle_id
	 */
	private static function flush_bundle_cache( $bundle_id ) {
		wp_cache_delete( WC_Cache_Helper::get_cache_prefix( 'bundled_data_items' ) . $bundle_id, 'bundled_data_items' );
	}

	public function sync_product_bundle_meta( $bundle_id, $translated_bundle_id ) {

		$bundle_items   = $this->product_bundles_items->get_items( $bundle_id );
		$fields_to_sync = [
			'optional',
			'stock_status',
			'max_stock',
			'quantity_min',
			'quantity_max',
			'shipped_individually',
			'priced_individually',
			'single_product_visibility',
			'cart_visibility',
			'order_visibility',
			'single_product_price_visibility',
			'cart_price_visibility',
			'order_price_visibility',
			'discount',
			'override_variations',
			'override_default_variation_attributes',
			'hide_filtered_variations',
		];

		$target_lang         = $this->sitepress->get_language_for_element( $translated_bundle_id, 'post_product' );
		$translated_item_ids = [];
		foreach ( $bundle_items as $item_id => $bundle_item ) {

			$item_meta             = $this->product_bundles_items->get_item_data( $bundle_item );
			$translated_product_id = apply_filters( 'translate_object_id', $item_meta['product_id'], get_post_type( $item_meta['product_id'] ), false, $target_lang );

			if ( $translated_product_id ) {
				$translated_item_id    = $this->get_item_id_for_language( $item_id, $target_lang );
				$translated_item_ids[] = $translated_item_id;

				$translated_item = $this->product_bundles_items->get_item_data_object( $translated_item_id );
				foreach ( $fields_to_sync as $key ) {
					if ( isset( $item_meta[ $key ] ) ) {
						$this->product_bundles_items->update_item_meta( $translated_item, $key, $item_meta[ $key ] );
					}
				}

				if ( isset( $item_meta['allowed_variations'] ) ) {
					if ( is_array( $item_meta['allowed_variations'] ) ) {
						$allowed_variations =
							$this->translate_allowed_variations( $item_meta['allowed_variations'], $target_lang );
						$this->product_bundles_items->update_item_meta( $translated_item, 'allowed_variations', $allowed_variations );
					} else {
						$this->product_bundles_items->update_item_meta( $translated_item, 'allowed_variations', $item_meta['allowed_variations'] );
					}
				}

				if ( isset( $item_meta['default_variation_attributes'] ) ) {
					$default_variation_attributes = $this->translate_default_variation_attributes( $item_meta['default_variation_attributes'], $target_lang, $item_meta['product_id'], $translated_product_id );
					$this->product_bundles_items->update_item_meta( $translated_item, 'default_variation_attributes', $default_variation_attributes );
				}

				$this->product_bundles_items->save_item_meta( $translated_item );

			}
		}

		// Delete removed items.
		$translated_bundle_items = $this->product_bundles_items->get_items( $translated_bundle_id );
		foreach ( $translated_bundle_items as $item_id => $bundle_item ) {
			if ( ! in_array( $item_id, $translated_item_ids ) ) {
				$bundled_item_data = $this->product_bundles_items->get_item_data_object( $item_id );
				$bundled_item_data->delete();
			}
		}

	}

	public function sync_product_bundle_meta_with_translations( $bundle_id ) {
		$get_original_id_and_translations = Fns::memorize(
			/**
			 * @return array {
			 * @var int                              $original_bundle_id
			 * @var \WPML\Collect\Support\Collection $translations_only
			 * }
			 */
			function() use ( $bundle_id ) {
				$trid               = $this->sitepress->get_element_trid( $bundle_id, 'post_product' );
				$translations       = wpml_collect( $this->sitepress->get_element_translations( $trid, 'post_product' ) );
				$original_bundle_id = (int) $translations->filter( Obj::prop( 'original' ) )
			                                             ->map( Obj::prop( 'element_id' ) )
			                                             ->first();

				$translations_only = $translations
					->reject( function( $translation ) use ( $original_bundle_id ) {
						return (int) $translation->element_id === $original_bundle_id;
					} );

				return [ $original_bundle_id, $translations_only ];
			}
		);

		if ( $this->is_bundle_product( $bundle_id ) ) {
			list( $original_bundle_id, $translations_only ) = $get_original_id_and_translations();

			$translations_only->each( function( $translation ) use ( $original_bundle_id ) {
				$this->sync_product_bundle_meta( $original_bundle_id, $translation->element_id );
			} );
		}

		$bundle_sell_ids = get_post_meta( $bundle_id, self::META_SELL_IDS, true );

		if ( $bundle_sell_ids ) {
			list( $original_bundle_id, $translations_only ) = $get_original_id_and_translations();

			$translations_only->each( function( $translation ) use ( $original_bundle_id ) {
				$this->sync_bundle_sells( $original_bundle_id, $translation->element_id, $translation->language_code );
			} );
		}
	}

	/**
	 * @param array  $allowed_variations
	 * @param string $lang
	 *
	 * @return array
	 */
	public function translate_allowed_variations( $allowed_variations, $lang ) {

		foreach ( $allowed_variations as $k => $variation_id ) {
			$allowed_variations[ $k ] =
				apply_filters( 'translate_object_id', $variation_id, 'product_variation', true, $lang );
		}

		return $allowed_variations;
	}

	/**
	 * @param array  $original_default_variation_attributes
	 * @param string $target_lang
	 * @param int    $product_id
	 * @param int    $translated_product_id
	 *
	 * @return array
	 */
	public function translate_default_variation_attributes( $original_default_variation_attributes, $target_lang, $product_id, $translated_product_id ) {
		$default_variation_attributes = [];

		if ( is_array( $original_default_variation_attributes ) ) {
			foreach ( $original_default_variation_attributes as $attribute_taxonomy => $attribute_slug ) {
				if ( 'pa_' === substr( $attribute_taxonomy, 0, 3 ) ) {
					$attribute_term_id            = $this->woocommerce_wpml->terms->wcml_get_term_id_by_slug( $attribute_taxonomy, $attribute_slug );
					$translated_attribute_term_id = apply_filters( 'translate_object_id', $attribute_term_id, $attribute_taxonomy, true, $target_lang );
					$translated_term              = $this->woocommerce_wpml->terms->wcml_get_term_by_id( $translated_attribute_term_id, $attribute_taxonomy );

					$default_variation_attributes[ $attribute_taxonomy ] = $translated_term->slug;
				} else {
					$default_variation_attributes[ $attribute_taxonomy ] = $this->woocommerce_wpml->attributes->get_custom_attr_translation( $product_id, $translated_product_id, $attribute_taxonomy, $attribute_slug );
				}
			}
		}

		return $default_variation_attributes;
	}

	private function get_product_id_for_item_id( $item_id ) {

		return $this->wpdb->get_var(
			$this->wpdb->prepare(
				"SELECT product_id FROM {$this->wpdb->prefix}woocommerce_bundled_items WHERE bundled_item_id=%d",
				$item_id
			)
		);
	}

	/**
	 * @param int|string $item_id
	 * @param string     $language
	 *
	 * @return string
	 */
	public function get_item_id_for_language( $item_id, $language ) {

		return $this->wpdb->get_var(
			$this->wpdb->prepare(
				"SELECT meta_value FROM {$this->wpdb->prefix}woocommerce_bundled_itemmeta WHERE bundled_item_id=%d AND meta_key=%s",
				$item_id,
				'translation_item_id_of_' . $language
			)
		);

	}

	/**
	 * @param int    $original_item_id
	 * @param int    $translated_item_id
	 * @param string $language
	 */
	public function set_translated_item_id_relationship( $original_item_id, $translated_item_id, $language ) {

		$this->wpdb->insert(
			$this->wpdb->prefix . 'woocommerce_bundled_itemmeta',
			[
				'bundled_item_id' => $original_item_id,
				'meta_key'        => 'translation_item_id_of_' . $language,
				'meta_value'      => $translated_item_id,
			]
		);

	}

	/**
	 * @deprecated This method is used by CTE only.
	 *
	 * Add Bundles Box to WCML Translation GUI.
	 *
	 * @param object     $obj
	 * @param string|int $bundle_id
	 * @param mixed      $data
	 *
	 * @return false|void
	 */
	public function custom_box_html( $obj, $bundle_id, $data ) {

		$bundle_items = $this->product_bundles_items->get_items( $bundle_id );

		if ( empty( $bundle_items ) ) {
			return false;
		}

		$bundles_section = new WPML_Editor_UI_Field_Section( __( 'Product Bundles', 'woocommerce-multilingual' ) );

		end( $bundle_items );
		$last_item_id = key( $bundle_items );
		$divider      = true;
		$flag         = false;

		foreach ( $bundle_items as $item_id => $bundle_item ) {

			$translated_product = apply_filters( 'translate_object_id', $bundle_item->product_id, get_post_type( $bundle_item->product_id ), false, $obj->get_target_language() );
			if ( ! is_null( $translated_product ) ) {

				$add_group = false;
				if ( $item_id == $last_item_id ) {
					$divider = false;
				}

				$bundle_item_data = $this->product_bundles_items->get_item_data( $bundle_item );

				$group = new WPML_Editor_UI_Field_Group( get_the_title( $bundle_item->product_id ), $divider );

				if ( $bundle_item_data['override_title'] == 'yes' ) {
					$bundle_field = new WPML_Editor_UI_Single_Line_Field(
						'bundle_' . $bundle_item->product_id . '_title',
						__( 'Name', 'woocommerce-multilingual' ),
						$data,
						false
					);
					$group->add_field( $bundle_field );
					$add_group = true;
				}

				if ( $bundle_item_data['override_description'] == 'yes' ) {
					$bundle_field = new WPML_Editor_UI_Single_Line_Field(
						'bundle_' . $bundle_item->product_id . '_desc',
						__( 'Description', 'woocommerce-multilingual' ),
						$data,
						false
					);
					$group->add_field( $bundle_field );
					$add_group = true;
				}

				if ( $add_group ) {
					$bundles_section->add_field( $group );
					$flag = true;
				}
			}
		}

		if ( $flag ) {
			$obj->add_field( $bundles_section );
		}

	}

	/**
	 * @deprecated This method is used by CTE only.
	 *
	 * @param array      $data
	 * @param string|int $bundle_id
	 * @param object     $translation
	 * @param string     $lang
	 *
	 * @return mixed
	 */
	public function custom_box_html_data( $data, $bundle_id, $translation, $lang ) {

		$bundle_data = $this->get_product_bundle_data( $bundle_id );

		if ( $translation ) {
			$translated_bundle_id   = $translation->ID;
			$translated_bundle_data = $this->get_product_bundle_data( $translated_bundle_id );
		}

		if ( empty( $bundle_data ) || $bundle_data == false ) {
			return $data;
		}

		$product_bundles = array_keys( $bundle_data );

		foreach ( $product_bundles as $item_id ) {

			$product_id         = $this->get_product_id_for_item_id( $item_id );
			$translated_item_id = null;

			if ( $translation ) {
				$translated_item_id = $this->get_item_id_for_language( $item_id, $lang );
			}

			if ( $bundle_data[ $item_id ]['override_title'] == 'yes' ) {
				$data[ 'bundle_' . $product_id . '_title' ] = [ 'original' => $bundle_data[ $item_id ]['title'] ];
				if ( $translated_item_id && isset( $translated_bundle_data[ $translated_item_id ]['override_title'] ) ) {
					$data[ 'bundle_' . $product_id . '_title' ]['translation'] = $translated_bundle_data[ $translated_item_id ]['title'];
				} else {
					$data[ 'bundle_' . $product_id . '_title' ]['translation'] = '';
				}
			}

			if ( $bundle_data[ $item_id ]['override_description'] == 'yes' ) {
				$data[ 'bundle_' . $product_id . '_desc' ] = [ 'original' => $bundle_data[ $item_id ]['description'] ];
				if ( $translated_item_id && isset( $translated_bundle_data[ $translated_item_id ]['override_description'] ) ) {
					$data[ 'bundle_' . $product_id . '_desc' ]['translation'] = $translated_bundle_data[ $translated_item_id ]['description'];
				} else {
					$data[ 'bundle_' . $product_id . '_desc' ]['translation'] = '';
				}
			}
		}

		return $data;
	}

	/**
	 * @param int|string $product_id
	 * @param int|string $item_id
	 * @param string     $field
	 *
	 * @return string
	 */
	private static function get_job_field_name( $product_id, $item_id, $field ) {
		return 'product_bundles:' . $product_id . ':' . $item_id . ':' . $field;
	}

	public function append_bundle_data_translation_package( $package, $post ) {

		if ( $post->post_type == 'product' ) {

			$bundle_data = $this->get_product_bundle_data( $post->ID );

			if ( $bundle_data ) {

				$fields = [ 'title', 'description' ];

				foreach ( $bundle_data as $item_id => $product_data ) {

					$product_id = $this->get_product_id_for_item_id( $item_id );
					foreach ( $fields as $field ) {
						if ( $product_data[ 'override_' . $field ] == 'yes' && ! empty( $product_data[ $field ] ) ) {
							$package['contents'][ self::get_job_field_name( $product_id, $item_id, $field ) ] = [
								'translate' => 1,
								'data'      => $this->tp->encode_field_data( $product_data[ $field ] ),
								'format'    => 'base64',
							];
						}
					}
				}
			}

			$bundle_sell = get_post_meta( $post->ID, self::META_SELLS_TITLE, true );

			if ( $bundle_sell ) {
				$package['contents'][ self::META_SELLS_TITLE ] = [
					'translate' => 1,
					'data'      => $this->tp->encode_field_data( $bundle_sell ),
					'format'    => 'base64',
				];
			}
		}

		return $package;

	}

	/**
	 * @param string|int $translated_bundle_id
	 * @param array      $fields
	 * @param object     $job
	 */
	public function save_product_bundles_to_translation( $translated_bundle_id, $fields, $job ) {
		$bundle_id = $job->original_doc_id;

		if (
			Str::startsWith( 'post_', $job->original_post_type )
			&& 'product' === get_post_type( $bundle_id )
		) {
			// $get_field_translation :: (string, string, string) -> string
			$get_field_translation = function( $product_id, $item_id, $field ) use ( $fields ) {
				return Obj::path( [ self::get_job_field_name( $product_id, $item_id, $field ), 'data' ], $fields );
			};

			$this->apply_translation( $bundle_id, $translated_bundle_id, $get_field_translation, $job->language_code );
			$this->sync_bundle_sells( $bundle_id, $translated_bundle_id, $job->language_code, Obj::path( [ self::META_SELLS_TITLE, 'data' ], $fields ) );
		}
	}

	private function sync_bundle_sells( $bundle_id, $translated_bundle_id, $language_code, $translated_title = null ) {
		// $transpose_post_meta :: (string, callable) -> void
		$transpose_post_meta = function( $meta_name, callable $transpose ) use ( $bundle_id, $translated_bundle_id ) {
			$value = get_post_meta( $bundle_id, $meta_name, true );

			if ( $value ) {
				update_post_meta( $translated_bundle_id, $meta_name, $transpose( $value ) );
			} else {
				delete_post_meta( $translated_bundle_id, $meta_name );
			}
		};

		// $convert_product_ids :: array -> array
		$convert_product_ids = Fns::map( function( $id ) use ( $language_code ) {
			return $this->sitepress->get_object_id( $id, 'product', true, $language_code );
		} );

		$transpose_post_meta( self::META_SELL_IDS, $convert_product_ids );
		$transpose_post_meta( self::META_SELLS_DISCOUNT, Fns::identity() );

		if ( $translated_title ) {
			$transpose_post_meta( self::META_SELLS_TITLE, Fns::always( $translated_title ) );
		}
	}

	/**
	 * @deprecated This method is used by CTE only.
	 *
	 * Update Bundled products title and description after saving the translation.
	 *
	 * @param string|int $bundle_id
	 * @param string|int $translated_bundle_id
	 * @param array      $data
	 * @param string     $lang
	 *
	 * @return array|void
	 */
	public function bundle_update( $bundle_id, $translated_bundle_id, $data, $lang ) {
		// $get_field_translation :: (string, string, string, string) -> string
		$get_field_translation = function( $product_id, $item_id, $field, $field_alias ) use ( $data ) {
			return Obj::prop( md5( 'bundle_' . $product_id . '_' . $field_alias ), $data );
		};

		return $this->apply_translation( $bundle_id, $translated_bundle_id, $get_field_translation, $lang );
	}

	/**
	 * @param string|int $bundle_id
	 * @param string|int $translated_bundle_id
	 * @param callable   $get_field_translation (int, int, string) -> string
	 * @param string     $target_lang
	 *
	 * @return array|void
	 */
	private function apply_translation( $bundle_id, $translated_bundle_id, callable $get_field_translation, $target_lang ) {
		$bundle_data            = $this->get_product_bundle_data( $bundle_id );
		$translated_bundle_data = $this->get_product_bundle_data( $translated_bundle_id );

		if ( empty( $bundle_data ) ) {
			return;
		}

		$translate_bundled_item_ids = $this->wpdb->get_col(
			$this->wpdb->prepare(
				"SELECT product_id FROM {$this->wpdb->prefix}woocommerce_bundled_items WHERE bundle_id = %d",
				$translated_bundle_id
			)
		);

		foreach ( $bundle_data as $item_id => $bundle_item_data ) {

			$product_id            = $this->get_product_id_for_item_id( $item_id );
			$translated_product_id = apply_filters( 'translate_object_id', $product_id, get_post_type( $product_id ), false, $target_lang );

			if ( $translated_product_id ) {

				if ( ! in_array( $translated_product_id, $translate_bundled_item_ids ) ) {

					$menu_order = $this->wpdb->get_var(
						$this->wpdb->prepare(
							" 
	                    SELECT menu_order FROM {$this->wpdb->prefix}woocommerce_bundled_items
	                    WHERE bundle_id=%d AND product_id=%d
	                ",
							$bundle_id,
							$bundle_item_data['product_id']
						)
					);

					$this->wpdb->insert(
						$this->wpdb->prefix . 'woocommerce_bundled_items',
						[
							'product_id' => $translated_product_id,
							'bundle_id'  => $translated_bundle_id,
							'menu_order' => $menu_order,
						]
					);

					$translated_item_id = $this->wpdb->insert_id;
					$this->set_translated_item_id_relationship( $item_id, $translated_item_id, $target_lang );
				}

				$translated_item_id = $this->get_item_id_for_language( $item_id, $target_lang );

				foreach ( [
					'title'       => 'title',
					'description' => 'desc',
				] as $field => $field_alias ) {
					$field_translation = $get_field_translation( $product_id, $item_id, $field, $field_alias );

					if ( $field_translation ) {
						$translated_bundle_data[ $translated_item_id ][ $field ]            = $field_translation;
						$translated_bundle_data[ $translated_item_id ][ "override_$field" ] = $bundle_item_data[ "override_$field" ];
					}
				}

				if ( isset( $bundle_item_data['allowed_variations'] ) ) {
					if ( is_array( $bundle_item_data['allowed_variations'] ) ) {
						$translated_bundle_data[ $translated_item_id ]['allowed_variations'] = $this->translate_allowed_variations( $bundle_item_data['allowed_variations'], $target_lang );
					} else {
						$translated_bundle_data[ $translated_item_id ]['allowed_variations'] = $bundle_item_data['allowed_variations'];
					}
				}
			}
		}

		$this->save_product_bundle_data( $translated_bundle_id, $translated_bundle_data );
		$this->sync_product_bundle_meta( $bundle_id, $translated_bundle_id );

		$this->sitepress->copy_custom_fields( $bundle_id, $translated_bundle_id );

		return $translated_bundle_data;
	}

	/**
	 * Sync product bundle data with translated values when the product is duplicated.
	 *
	 * @param int $bundle_id
	 * @param int $translated_bundle_id
	 *
	 * @return array|null
	 */
	public function sync_bundled_ids( $bundle_id, $translated_bundle_id ) {

		$bundle_data = $this->get_product_bundle_data( $bundle_id );
		if ( $bundle_data ) {
			$translated_bundle_data               = [];
			$lang                                 = $this->sitepress->get_language_for_element( $translated_bundle_id, 'post_product' );
			$translated_bundle_data_before_update = $this->get_product_bundle_data( $translated_bundle_id );

			foreach ( $bundle_data as $item_id => $product_data ) {

				$product_id            = $this->get_product_id_for_item_id( $item_id );
				$translated_product_id = apply_filters( 'translate_object_id', $product_id, get_post_type( $product_id ), false, $lang );

				if ( $translated_product_id ) {

					$translated_item_id = $this->get_item_id_for_language( $item_id, $lang );
					if ( ! $translated_item_id ) {
						$menu_order = $this->wpdb->get_var(
							$this->wpdb->prepare(
								" 
                            SELECT menu_order FROM {$this->wpdb->prefix}woocommerce_bundled_items
	                        WHERE bundle_id=%d AND product_id=%d
	                        ",
								$bundle_id,
								$product_id
							)
						);

						$this->wpdb->insert(
							$this->wpdb->prefix . 'woocommerce_bundled_items',
							[
								'product_id' => $translated_product_id,
								'bundle_id'  => $translated_bundle_id,
								'menu_order' => $menu_order,
							]
						);
						$translated_item_id = $this->wpdb->insert_id;
						$this->set_translated_item_id_relationship( $item_id, $translated_item_id, $lang );
					}

					$translated_bundle_data[ $translated_item_id ]               = $product_data;
					$translated_bundle_data[ $translated_item_id ]['product_id'] = $translated_product_id;

					if ( isset( $product_data['title'] ) ) {
						if ( $product_data['override_title'] !== 'yes' ) {
							$translated_bundle_data[ $translated_item_id ]['title'] = get_the_title( $translated_product_id );
						} else {
							$translated_bundle_data[ $translated_item_id ]['title'] = isset( $translated_bundle_data_before_update[ $translated_item_id ] ) ? $translated_bundle_data_before_update[ $translated_item_id ]['title'] : '';
						}
					}

					if ( isset( $product_data['title'] ) ) {
						if ( $product_data['override_description'] !== 'yes' ) {
							$translated_bundle_data[ $translated_item_id ]['description'] = get_the_title( $translated_product_id );
						} else {
							$translated_bundle_data[ $translated_item_id ]['description'] = isset( $translated_bundle_data_before_update[ $translated_item_id ] ) ? $translated_bundle_data_before_update[ $translated_item_id ]['description'] : '';
						}
					}

					if ( isset( $product_data['filter_variations'] ) && $product_data['filter_variations'] == 'yes' ) {
						$allowed_var = maybe_unserialize( $product_data['allowed_variations'] );
						$translated_bundle_data[ $translated_item_id ]['allowed_variations'] = maybe_unserialize( $translated_bundle_data[ $translated_item_id ]['allowed_variations'] );
						foreach ( $allowed_var as $key => $var_id ) {
							$translated_var_id = apply_filters( 'translate_object_id', $var_id, get_post_type( $var_id ), true, $lang );
							$translated_bundle_data[ $translated_item_id ]['allowed_variations'][ $key ] = $translated_var_id;
						}
						$translated_bundle_data[ $translated_item_id ]['allowed_variations'] = maybe_serialize( $translated_bundle_data[ $translated_item_id ]['allowed_variations'] );
					}

					if ( isset( $product_data['bundle_defaults'] ) && ! empty( $product_data['bundle_defaults'] ) ) {
						$translated_bundle_data[ $translated_item_id ]['bundle_defaults'] = maybe_unserialize( $translated_bundle_data[ $translated_item_id ]['bundle_defaults'] );
						foreach ( maybe_unserialize( $product_data['bundle_defaults'] ) as $tax => $term_slug ) {

							$term_id = $this->woocommerce_wpml->terms->wcml_get_term_id_by_slug( $tax, $term_slug );
							if ( $term_id ) {
								// Global Attribute.
								$tr_def_id = apply_filters( 'translate_object_id', $term_id, $tax, true, $lang );
								$tr_term   = $this->woocommerce_wpml->terms->wcml_get_term_by_id( $tr_def_id, $tax );
								$translated_bundle_data[ $translated_item_id ]['bundle_defaults'][ $tax ] = $tr_term->slug;
							} else {
								// Custom Attribute.
								$args          = [
									'post_type'    => 'product_variation',
									'meta_key'     => 'attribute_' . $tax,
									'meta_value'   => $term_slug,
									'meta_compare' => '=',
								];
								$variationloop = new WP_Query( $args );
								while ( $variationloop->have_posts() ) :
									$variationloop->the_post();
									$tr_var_id = apply_filters( 'translate_object_id', get_the_ID(), 'product_variation', true, $lang );
									$tr_meta   = get_post_meta( $tr_var_id, 'attribute_' . $tax, true );
									$translated_bundle_data[ $translated_item_id ]['bundle_defaults'][ $tax ] = $tr_meta;
								endwhile;
							}
						}
						$translated_bundle_data[ $translated_item_id ]['bundle_defaults'] = maybe_serialize( $translated_bundle_data[ $translated_item_id ]['bundle_defaults'] );
					}
				}
			}

			$this->save_product_bundle_data( $translated_bundle_id, $translated_bundle_data );

			return $translated_bundle_data;
		}

		return null;
	}

	public function resync_bundle( $cart_item, $session_values, $cart_item_key ) {

		if ( isset( $cart_item['bundled_items'] ) && $cart_item['data']->product_type === 'bundle' ) {
			$current_bundle_id = apply_filters( 'translate_object_id', $cart_item['product_id'], 'product', true );
			if ( $cart_item['product_id'] != $current_bundle_id ) {
				if ( isset( $cart_item['data']->bundle_data ) && is_array( $cart_item['data']->bundle_data ) ) {
					$old_bundled_item_ids = array_keys( $cart_item['data']->bundle_data );
					$cart_item['data']    = wc_get_product( $current_bundle_id );
					if ( isset( $cart_item['data']->bundle_data ) && is_array( $cart_item['data']->bundle_data ) ) {
						$new_bundled_item_ids      = array_keys( $cart_item['data']->bundle_data );
						$remapped_bundled_item_ids = [];
						foreach ( $old_bundled_item_ids as $old_item_id_index => $old_item_id ) {
							$remapped_bundled_item_ids[ $old_item_id ] = $new_bundled_item_ids[ $old_item_id_index ];
						}
						$cart_item['remapped_bundled_item_ids'] = $remapped_bundled_item_ids;
						if ( isset( $cart_item['stamp'] ) ) {
							$new_stamp = [];
							foreach ( $cart_item['stamp'] as $bundled_item_id => $stamp_data ) {
								$new_stamp[ $remapped_bundled_item_ids[ $bundled_item_id ] ] = $stamp_data;
							}
							$cart_item['stamp'] = $new_stamp;
						}
					}
				}
			}
		}
		if ( isset( $cart_item['bundled_by'] ) && isset( WC()->cart->cart_contents[ $cart_item['bundled_by'] ] ) ) {
			$bundle_cart_item = WC()->cart->cart_contents[ $cart_item['bundled_by'] ];
			if (
				isset( $bundle_cart_item['remapped_bundled_item_ids'] ) &&
				isset( $cart_item['bundled_item_id'] ) &&
				isset( $bundle_cart_item['remapped_bundled_item_ids'][ $cart_item['bundled_item_id'] ] )
			) {
				$old_id                       = $cart_item['bundled_item_id'];
				$remapped_bundled_item_ids    = $bundle_cart_item['remapped_bundled_item_ids'];
				$cart_item['bundled_item_id'] = $remapped_bundled_item_ids[ $cart_item['bundled_item_id'] ];
				if ( isset( $cart_item['stamp'] ) ) {
					$new_stamp = [];
					foreach ( $cart_item['stamp'] as $bundled_item_id => $stamp_data ) {
						$new_stamp[ $remapped_bundled_item_ids[ $bundled_item_id ] ] = $stamp_data;
					}
					$cart_item['stamp'] = $new_stamp;
				}
			}
		}

		return $cart_item;
	}

	public function resync_bundle_clean( $cart ) {
		foreach ( $cart->cart_contents as $cart_item_key => $cart_item ) {
			if ( isset( $cart_item['bundled_items'] ) && $this->is_bundle_product( $cart_item['product_id'] ) ) {
				if ( isset( $cart_item['remapped_bundled_item_ids'] ) ) {
					unset( WC()->cart->cart_contents[ $cart_item_key ]['remapped_bundled_item_ids'] );
				}
			}
		}
	}

	/**
	 * @deprecated This method is used by CTE only.
	 *
	 * @param string|int $translated_bundle_id
	 * @param array      $data
	 * @param object     $job
	 */
	public function save_bundle_data_translation( $translated_bundle_id, $data, $job ) {

		if ( $this->is_bundle_product( $translated_bundle_id ) ) {

			remove_action( 'wcml_after_duplicate_product_post_meta', [ $this, 'sync_bundled_ids' ], 10 );

			$translated_bundle_data = $this->get_product_bundle_data( $translated_bundle_id );

			$bundle_id = $job->original_doc_id;

			$bundle_data = $this->get_product_bundle_data( $bundle_id );

			foreach ( $data as $value ) {

				if ( preg_match( '/product_bundles:([0-9]+):([0-9]+):(.+)/', $value['field_type'], $matches ) ) {

					$product_id = $matches[1];
					$item_id    = $matches[2];
					$field      = $matches[3];

					$translated_product_id = apply_filters( 'translate_object_id', $product_id, get_post_type( $product_id ), false, $job->language_code );
					$translated_item_id    = $this->get_item_id_for_language( $item_id, $job->language_code );
					if ( empty( $translated_item_id ) ) {
						$translated_item_id = $this->add_product_to_bundle( $translated_product_id, $translated_bundle_id, $item_id, $job->language_code );
					}

					if ( ! isset( $translated_bundle_data[ $translated_item_id ] ) ) {
						$translated_bundle_data[ $translated_item_id ] = [
							'product_id'                => $translated_product_id,
							'hide_thumbnail'            => $bundle_data[ $item_id ]['hide_thumbnail'],
							'override_title'            => $bundle_data[ $item_id ]['override_title'],
							'product_title'             => '',
							'override_description'      => $bundle_data[ $item_id ]['override_description'],
							'product_description'       => '',
							'optional'                  => $bundle_data[ $item_id ]['optional'],
							'bundle_quantity'           => $bundle_data[ $item_id ]['bundle_quantity'],
							'bundle_quantity_max'       => $bundle_data[ $item_id ]['bundle_quantity_max'],
							'bundle_discount'           => $bundle_data[ $item_id ]['bundle_discount'],
							'single_product_visibility' => $bundle_data[ $item_id ]['single_product_visibility'],
							'cart_visibility'           => $bundle_data[ $item_id ]['cart_visibility'],
							'order_visibility'          => $bundle_data[ $item_id ]['order_visibility'],
							'stock_status'              => $bundle_data[ $item_id ]['stock_status'],
							'max_stock'                 => $bundle_data[ $item_id ]['max_stock'],
							'quantity_min'              => $bundle_data[ $item_id ]['quantity_min'],
							'quantity_max'              => $bundle_data[ $item_id ]['quantity_max'],
							'shipped_individually'      => $bundle_data[ $item_id ]['shipped_individually'],
							'priced_individually'       => $bundle_data[ $item_id ]['priced_individually'],
							'single_product_price_visibility' => $bundle_data[ $item_id ]['single_product_price_visibility'],
							'cart_price_visibility'     => $bundle_data[ $item_id ]['cart_price_visibility'],
							'order_price_visibility'    => $bundle_data[ $item_id ]['order_price_visibility'],
						];
					}

					$translated_bundle_data[ $translated_item_id ][ $field ] = $value['data'];
				}
			}

			$this->save_product_bundle_data( $translated_bundle_id, $translated_bundle_data );
		}
	}

	private function add_product_to_bundle( $product_id, $bundle_id, $item_id, $language ) {

		$menu_order = $this->wpdb->get_var(
			$this->wpdb->prepare(
				" 
                            SELECT menu_order FROM {$this->wpdb->prefix}woocommerce_bundled_items
	                        WHERE bundled_item_id=%d
	                        ",
				$item_id
			)
		);

		$this->wpdb->insert(
			$this->wpdb->prefix . 'woocommerce_bundled_items',
			[
				'product_id' => $product_id,
				'bundle_id'  => $bundle_id,
				'menu_order' => $menu_order,
			]
		);

		$translated_item_id = $this->wpdb->insert_id;
		$this->set_translated_item_id_relationship( $item_id, $translated_item_id, $language );

		return $translated_item_id;
	}

	public function is_bundle_product( $product_id ) {

		$product = wc_get_product( $product_id );

		if ( $product && 'bundle' === $product->get_type() ) {
			return true;
		}

		return false;
	}

	// #wcml-2241.
	public function upgrade_bundles_items_relationships() {

		$table_exists = $this->wpdb->get_var( "SHOW TABLES LIKE '{$this->wpdb->prefix}woocommerce_bundled_items'" );

		if ( $table_exists && ! get_option( 'wcml_upgrade_bundles_items_relationships' ) ) {

			$bundled_items    = $this->wpdb->get_results( "SELECT bundled_item_id, bundle_id, product_id FROM {$this->wpdb->prefix}woocommerce_bundled_items" );
			$active_languages = $this->sitepress->get_active_languages();

			foreach ( $bundled_items as $bundled_item ) {

				if ( $this->woocommerce_wpml->products->is_original_product( $bundled_item->bundle_id ) ) {

					foreach ( $active_languages as $lang ) {

						if ( $lang['code'] !== $this->woocommerce_wpml->products->get_original_product_language( $bundled_item->bundle_id ) ) {

							$translated_bundle_id  = apply_filters( 'translate_object_id', $bundled_item->bundle_id, get_post_type( $bundled_item->bundle_id ), false, $lang['code'] );
							$translated_product_id = apply_filters( 'translate_object_id', $bundled_item->product_id, get_post_type( $bundled_item->product_id ), false, $lang['code'] );

							$translated_item_id = $this->wpdb->get_var(
								$this->wpdb->prepare(
									"SELECT bundled_item_id FROM {$this->wpdb->prefix}woocommerce_bundled_items WHERE product_id=%d AND bundle_id=%d",
									$translated_product_id,
									$translated_bundle_id
								)
							);

							$this->wpdb->insert(
								$this->wpdb->prefix . 'woocommerce_bundled_itemmeta',
								[
									'bundled_item_id' => $bundled_item->bundled_item_id,
									'meta_key'        => 'translation_item_id_of_' . $lang['code'],
									'meta_value'      => $translated_item_id,
								]
							);
						}
					}
				}
			}

			add_option( 'wcml_upgrade_bundles_items_relationships', true );

		}
	}

	public function woocommerce_json_search_filter_found_products( $found_products ) {

		foreach ( $found_products as $id => $product_name ) {
			if ( $this->sitepress->get_language_for_element( $id, 'post_' . get_post_type( $id ) ) != $this->sitepress->get_current_language() ) {
				unset( $found_products[ $id ] );
			}
		}

		return $found_products;
	}

	public function delete_bundled_item_relationship( $bundle_item ) {

		$this->wpdb->query(
			$this->wpdb->prepare(
				"DELETE FROM {$this->wpdb->prefix}woocommerce_bundled_itemmeta WHERE `meta_value` = %d AND `meta_key` LIKE 'translation_item_id_of_%'",
				$bundle_item->get_id()
			)
		);

	}

	/**
	 * @param array $values
	 * @param int   $postId
	 *
	 * @return array
	 */
	public static function adjust_bundle_sells_product_signature( $values, $postId ) {
		return Obj::assoc( self::META_SELLS_TITLE, get_post_meta( $postId, self::META_SELLS_TITLE, true ), $values );
	}
}
