<?php

use WCML\Options\WPML;
use WPML\FP\Fns;
use WPML\FP\Obj;
use WPML\FP\Str;

class WCML_Composite_Products extends WCML_Compatibility_Helper implements \IWPML_Action {

	const META_KEY_DATA     = '_bto_data';
	const META_KEY_SCENARIO = '_bto_scenario_data';

	/**
	 * @var SitePress
	 */
	private $sitepress;

	public function __construct( SitePress $sitepress ) {
		$this->sitepress = $sitepress;
	}

	public function add_hooks(){

		add_filter( 'woocommerce_composite_component_default_option', array($this, 'woocommerce_composite_component_default_option'), 10, 3 );
		add_filter( 'wcml_cart_contents', array($this, 'wpml_composites_compat'), 11, 4 );
		add_filter( 'woocommerce_composite_component_options_query_args', array($this, 'wpml_composites_transients_cache_per_language'), 10, 3 );
		add_action( 'wcml_before_sync_product_data', array( $this, 'sync_composite_data_across_translations' ), 10, 2 );
		add_action( 'wpml_translation_job_saved',   array( $this, 'save_composite_data_translation' ), 10, 3 );

		if( is_admin() ){

			if ( ! WPML::useAte() ) {  // Legacy actions/filters for CTE
				add_action( 'wcml_gui_additional_box_html', [ $this, 'custom_box_html' ], 10, 3 );
				add_filter( 'wcml_gui_additional_box_data', [ $this, 'custom_box_html_data' ], 10, 4 );
				add_action( 'wcml_update_extra_fields', [ $this, 'update_component_strings' ], 10, 4 );
			}

			add_filter( 'woocommerce_json_search_found_products', array( $this, 'woocommerce_json_search_found_products' ) );

			add_filter( 'wpml_tm_translation_job_data', array( $this, 'append_composite_data_translation_package' ), 10, 2 );

			//lock fields on translations pages
			add_filter( 'wcml_js_lock_fields_input_names', array( $this, 'wcml_js_lock_fields_input_names' ) );
			add_filter( 'wcml_js_lock_fields_ids', array( $this, 'wcml_js_lock_fields_ids' ) );
			add_filter( 'wcml_after_load_lock_fields_js', array( $this, 'localize_lock_fields_js' ) );
			add_action( 'init', array( $this, 'load_assets' ) );

			add_filter( 'wcml_do_not_display_custom_fields_for_product', array( $this, 'replace_tm_editor_custom_fields_with_own_sections' ) );
		}

	}

	public function woocommerce_composite_component_default_option($selected_value, $component_id, $object) {

		if( !empty( $selected_value ) )
			$selected_value = apply_filters( 'wpml_object_id', $selected_value, 'product', true );


		return $selected_value;
	}

	public function wpml_composites_compat( $new_cart_data, $cart_contents, $key, $new_key ) {

		if ( isset( $cart_contents[ $key ][ 'composite_children' ] ) || isset( $cart_contents[ $key ][ 'composite_parent' ] ) ) {

			$buff = $new_cart_data[ $new_key ];

			unset( $new_cart_data[ $new_key ] );

			$new_cart_data[ $key ] = $buff;
		}

		return $new_cart_data;
	}

	public function wpml_composites_transients_cache_per_language( $args, $query_args, $component_data ) {

		$args[ 'wpml_lang' ] = apply_filters( 'wpml_current_language', NULL );

		return $args;
	}

	public function sync_composite_data_across_translations( $original_product_id, $current_product_id ){

		if( $this->get_product_type( $original_product_id ) == 'composite' ){

			$composite_data = $this->get_composite_data( $original_product_id );
			$composite_scenarios_meta = $this->get_composite_scenarios_meta( $original_product_id );

			$product_trid = $this->sitepress->get_element_trid( $original_product_id, 'post_product' );
			$product_translations = $this->sitepress->get_element_translations( $product_trid, 'post_product' );

			foreach ( $product_translations as $product_translation ) {

				if ( empty($product_translation->original) ) {

					$translated_composite_data = $this->get_composite_data( $product_translation->element_id );

					foreach ( $composite_data as $component_id => $component ) {

						if( isset( $translated_composite_data[$component_id]['title'] ) ){
							$composite_data[$component_id]['title'] =  $translated_composite_data[$component_id]['title'];
						}

						if( isset( $translated_composite_data[$component_id]['description'] ) ){
							$composite_data[$component_id]['description'] =  $translated_composite_data[$component_id]['description'];
						}

						if ( $component['query_type'] == 'product_ids' ) {

							foreach ( $component['assigned_ids'] as $idx => $assigned_id ) {
								$composite_data[$component_id]['assigned_ids'][$idx] =
									apply_filters( 'wpml_object_id', $assigned_id, 'product', true, $product_translation->language_code );
							}

						} elseif( $component['query_type'] == 'category_ids' ){

							foreach ( $component['assigned_category_ids'] as $idx => $assigned_id ) {
								$composite_data[$component_id]['assigned_category_ids'][$idx] =
									apply_filters( 'wpml_object_id', $assigned_id, 'product_cat', true, $product_translation->language_code );

							}

						}

						//sync default
						if ( isset( $component['default_id'] ) && $component['default_id'] ) {
							$translated_default_id = apply_filters( 'wpml_object_id', $component['default_id'], get_post_type( $component['default_id'] ), false, $product_translation->language_code );
							if ( $translated_default_id ) {
								$composite_data[ $component_id ]['default_id'] = $translated_default_id;
							}
						}

					}

					update_post_meta( $product_translation->element_id, self::META_KEY_DATA, $composite_data );

					if ( $composite_scenarios_meta ) {
						// sync product ids
						$translate_product_ids = function ( $component_data ) use ( $product_translation ) {
							$translate_assigned_product_id = function( $assigned_product_id ) use ( $product_translation ) {
								return apply_filters( 'wpml_object_id', $assigned_product_id, get_post_type( $assigned_product_id ), false, $product_translation->language_code ) ?: $assigned_product_id;
							};

							return wpml_collect( (array) $component_data )
								->map( Fns::map( $translate_assigned_product_id ) )
								->toArray();
						};

						$composite_scenarios_meta = wpml_collect( $composite_scenarios_meta )
							->map( Obj::over( Obj::lensPath( [ 'component_data' ] ), $translate_product_ids ) )
							->map( Obj::over( Obj::lensPath( [ 'scenario_actions', 'conditional_options', 'component_data' ] ), $translate_product_ids ) )
							->toArray();

						update_post_meta( $product_translation->element_id, self::META_KEY_SCENARIO, $composite_scenarios_meta );
					}
				}
			}

		}
	}

	/**
	 * @deprecated This method is used by CTE only.
	 *
	 * @param object     $obj
	 * @param string|int $product_id
	 * @param mixed      $data
	 */
	public function custom_box_html( $obj, $product_id, $data ){

		if( $this->get_product_type( $product_id ) == 'composite' ){

			$composite_data = $this->get_composite_data( $product_id );

			$composite_section = new WPML_Editor_UI_Field_Section( __( 'Composite Products ( Components )', 'woocommerce-multilingual' ) );
			end( $composite_data );
			$last_key = key( $composite_data );
			$divider = true;
			foreach( $composite_data as $component_id => $component ) {
				if( $component_id ==  $last_key ){
					$divider = false;
				}
				$group = new WPML_Editor_UI_Field_Group( '', $divider );
				$composite_field = new WPML_Editor_UI_Single_Line_Field( 'composite_'.$component_id.'_title', __( 'Name', 'woocommerce-multilingual' ), $data, false );
				$group->add_field( $composite_field );
				$composite_field = new WPML_Editor_UI_Single_Line_Field( 'composite_'.$component_id.'_description' , __( 'Description', 'woocommerce-multilingual' ), $data, false );
				$group->add_field( $composite_field );
				$composite_section->add_field( $group );

			}

			if( $composite_data ){
				$obj->add_field( $composite_section );
			}

			$composite_scenarios_meta = $this->get_composite_scenarios_meta( $product_id );
			if( $composite_scenarios_meta ){

				$composite_scenarios = new WPML_Editor_UI_Field_Section( __( 'Composite Products ( Scenarios )', 'woocommerce-multilingual' ) );
				end( $composite_scenarios_meta );
				$last_key = key( $composite_scenarios_meta );
				$divider = true;
				foreach( $composite_scenarios_meta as $scenario_key => $scenario_meta ) {
					if( $scenario_key ==  $last_key ){
						$divider = false;
					}
					$group = new WPML_Editor_UI_Field_Group( '', $divider );
					$composite_scenario_field = new WPML_Editor_UI_Single_Line_Field( 'composite_scenario_'.$scenario_key.'_title', __( 'Name', 'woocommerce-multilingual' ), $data, false );
					$group->add_field( $composite_scenario_field );
					$composite_scenario_field = new WPML_Editor_UI_Single_Line_Field( 'composite_scenario_'.$scenario_key.'_description' , __( 'Description', 'woocommerce-multilingual' ), $data, false );
					$group->add_field( $composite_scenario_field );
					$composite_scenarios->add_field( $group );

				}

				$obj->add_field( $composite_scenarios );

			}

		}

	}

	/**
	 * @deprecated This method is used by CTE only.
	 *
	 * @param array      $data
	 * @param string|int $product_id
	 * @param object     $translation
	 * @param string     $lang
	 *
	 * @return array
	 */
	public function custom_box_html_data( $data, $product_id, $translation, $lang ){

		if( $this->get_product_type( $product_id ) == 'composite' ){

			$composite_data = $this->get_composite_data( $product_id );

			foreach( $composite_data as $component_id => $component ) {

				$data['composite_'.$component_id.'_title'] = array( 'original' =>
					isset( $composite_data[$component_id]['title'] ) ? $composite_data[$component_id]['title'] : '' );

				$data['composite_'.$component_id.'_description'] = array( 'original' =>
					isset( $composite_data[$component_id]['description'] ) ? $composite_data[$component_id]['description'] : '' );

			}

			$composite_scenarios_meta = $this->get_composite_scenarios_meta( $product_id );
			if( $composite_scenarios_meta ){
				foreach( $composite_scenarios_meta as $scenario_key => $scenario_meta ){
					$data[ 'composite_scenario_'.$scenario_key.'_title' ] = array(
						'original' => isset( $scenario_meta['title'] ) ? $scenario_meta['title'] : '',
						'translation' => ''
					);

					$data[ 'composite_scenario_'.$scenario_key.'_description' ] = array(
						'original' => isset( $scenario_meta['description'] ) ? $scenario_meta['description'] : '',
						'translation' => ''
						);
				}
			}

			if( $translation ){
				$translated_composite_data = $this->get_composite_data( $translation->ID );

				foreach( $composite_data as $component_id => $component ){

					$data['composite_'.$component_id.'_title'][ 'translation' ] =
						isset( $translated_composite_data[$component_id]['title'] ) ? $translated_composite_data[$component_id]['title'] : '';

					$data['composite_'.$component_id.'_description'][ 'translation' ] =
						isset( $translated_composite_data[$component_id]['description'] ) ? $translated_composite_data[$component_id]['description'] : '';

				}

				$translated_composite_scenarios_meta = $this->get_composite_scenarios_meta( $translation->ID );
				if( $translated_composite_scenarios_meta ){
					foreach( $translated_composite_scenarios_meta as $scenario_key => $translated_scenario_meta ){
						$data[ 'composite_scenario_'.$scenario_key.'_title' ][ 'translation' ] =
							isset( $translated_scenario_meta['title'] ) ? $translated_scenario_meta['title'] : '';

						$data[ 'composite_scenario_'.$scenario_key.'_description' ][ 'translation' ] =
							isset( $translated_scenario_meta['description'] ) ? $translated_scenario_meta['description'] : '';
					}
				}

			}

		}

		return $data;
	}

	/**
	 * @deprecated This method is used by CTE only.
	 *
	 * @param string|int $original_product_id
	 * @param string|int $product_id
	 * @param array      $data
	 * @param string     $language
	 *
	 * @return array
	 */
	public function update_component_strings( $original_product_id, $product_id, $data, $language ){

		$composite_data = $this->get_composite_data( $product_id );

		foreach( $composite_data as $component_id => $component ) {

			if(!empty($data[ md5( 'composite_'.$component_id.'_title' ) ] ) ){
				$composite_data[$component_id]['title'] = $data[ md5( 'composite_'.$component_id.'_title' ) ];
			}

			if(!empty($data[ md5( 'composite_'.$component_id.'_description' ) ])) {
				$composite_data[$component_id]['description'] = $data[ md5( 'composite_'.$component_id.'_description' ) ];
			}

		}

		update_post_meta( $product_id, self::META_KEY_DATA, $composite_data );

		$composite_scenarios_meta = $this->get_composite_scenarios_meta( $product_id );
		if( $composite_scenarios_meta ){
			foreach( $composite_scenarios_meta as $scenario_key => $scenario_meta ){
				if( !empty( $data[ md5( 'composite_scenario_'.$scenario_key.'_title' ) ] ) ){
					$composite_scenarios_meta[ $scenario_key ][ 'title' ] = $data[ md5( 'composite_scenario_'.$scenario_key.'_title' ) ];
				}

				if( !empty( $data[ md5( 'composite_scenario_'.$scenario_key.'_description' ) ])) {
					$composite_scenarios_meta[ $scenario_key ][ 'description' ] = $data[ md5( 'composite_scenario_'.$scenario_key.'_description' ) ];
				}
			}
		}

		update_post_meta( $product_id, self::META_KEY_SCENARIO, $composite_scenarios_meta );

		return array(
			'components' => $composite_data,
			'scenarios'  => $composite_scenarios_meta,
		);
	}

	/**
	 * @param array              $package
	 * @param \stdClass|\WP_Post $post
	 *
	 * @return array
	 */
	public function append_composite_data_translation_package( $package, $post ){
		if( 'product' === $post->post_type ) {
			// $add_titles_and_descriptions :: (array, string, array) -> void
			$add_fields_to_package = function( $data, $fields, $subtype ) use ( &$package ) {
				if ( $data ) {
					foreach( $data as $key => $meta ){
						foreach( $fields as $field ) {
							if ( ! empty( $meta[ $field ] ) ) {
								$package['contents'][ self::get_field_name( $key, $field, $subtype ) ] = [
									'translate' => 1,
									'data'      => base64_encode( $meta[ $field ] ),
									'format'    => 'base64',
								];
							}
						}
					}
				}
			};

			$add_fields_to_package( $this->get_composite_data( $post->ID ), [ 'title', 'description' ], '' );
			$add_fields_to_package( $this->get_composite_scenarios_meta( $post->ID ), [ 'title', 'description' ], 'scenario' );
		}

		return $package;
	}

	/**
	 * @param string $component_id
	 * @param string $field
	 * @param string $subtype
	 *
	 * @return string
	 */
	private static function get_field_name( $component_id, $field, $subtype ) {
		$subtype = $subtype ? $subtype . ':' : $subtype;
		return 'wc_composite:' . $subtype . $component_id . ':' . $field;
	}

	/**
	 * @param string|int $post_id
	 * @param array      $data
	 * @param object     $job
	 */
	public function save_composite_data_translation( $post_id, $data, $job ){
		if (
			Str::startsWith( 'post_', $job->original_post_type )
			&& 'product' === get_post_type( $job->original_doc_id )
		) {
			$composite_data = $this->get_composite_data( $job->original_doc_id );

			if ( $composite_data ) {
				$get_translation = function ( $name ) use ( $data ) {
					return Obj::path( [ $name, 'data' ], $data );
				};

				// $apply_translations :: (array, array, string) -> array
				$apply_translations = function ( $data, $fields, $subtype ) use ( $get_translation ) {
					foreach ( $data as $key => $meta ) {
						foreach ( $fields as $field ) {
							$translation = $get_translation( self::get_field_name( $key, $field, $subtype ) );

							if ( $translation ) {
								$data[ $key ][ $field ] = $translation;
							}
						}
					}

					return $data;
				};

				// $adjust_ids :: (array, string) -> array
				$adjust_ids = function ( $data, $lang ) {
					foreach ( $data as $key => $meta ) {
						$ids_key = $cpt = null;

						if ( 'product_ids' === $meta['query_type'] ) {
							$ids_key = 'assigned_ids';
							$cpt     = 'product';
						} elseif ( 'category_ids' === $meta['query_type'] ) {
							$ids_key = 'assigned_category_ids';
							$cpt     = 'product_cat';
						}

						if ( $ids_key && $cpt ) {
							foreach ( $meta[ $ids_key ] as $idx => $assigned_id ) {
								$data[ $key ][ $ids_key ][ $idx ] = apply_filters( 'wpml_object_id', $assigned_id, $cpt, true, $lang );
							}
						}
					}

					return $data;
				};

				update_post_meta(
					$post_id,
					self::META_KEY_DATA,
					$adjust_ids( $apply_translations( $composite_data, [ 'title', 'description' ], '' ), $job->language_code )
				);

				update_post_meta(
					$post_id,
					self::META_KEY_SCENARIO,
					$apply_translations( $this->get_composite_scenarios_meta( $job->original_doc_id ), [ 'title', 'description' ], 'scenario' )
				);
			}
		}
	}

	public function wcml_js_lock_fields_input_names( $names ){

		$names[] = '_base_regular_price';
		$names[] = '_base_sale_price';
		$names[] = 'bto_style';

		return $names;
	}

	public function wcml_js_lock_fields_ids( $names ){

		$names[] = '_per_product_pricing_bto';
		$names[] = '_per_product_shipping_bto';
		$names[] = '_bto_hide_shop_price';

		return $names;
	}

	public function localize_lock_fields_js(){
		wp_localize_script( 'wcml-composite-js', 'lock_settings' , array( 'lock_fields' => 1 ) );
	}

	public function load_assets( ){
		global $pagenow;

		$is_composite_edit_page = false;

		if( $pagenow == 'post.php' && isset( $_GET[ 'post' ] ) ){
			$wc_product = wc_get_product( $_GET[ 'post' ] );
			if( $wc_product && $wc_product->get_type() === 'composite' ){
				$is_composite_edit_page = true;
			}
		}

		if( $is_composite_edit_page || $pagenow == 'post-new.php' ){
			wp_register_script( 'wcml-composite-js', WCML_PLUGIN_URL . '/compatibility/res/js/wcml-composite.js', array( 'jquery' ), WCML_VERSION, true );
			wp_enqueue_script( 'wcml-composite-js' );

		}

	}

	public function woocommerce_json_search_found_products( $found_products ){
		global $wpml_post_translations;

		foreach( $found_products as $id => $product_name ){
			if( $wpml_post_translations->get_element_lang_code ( $id ) != $this->sitepress->get_current_language() ){
				unset( $found_products[ $id ] );
			}
		}

		return $found_products;
	}

	public function get_composite_scenarios_meta( $product_id ){
		return get_post_meta( $product_id, self::META_KEY_SCENARIO, true );
	}

	public function get_composite_data( $product_id ){
		return get_post_meta( $product_id, self::META_KEY_DATA, true ) ?: [];
	}

	public function replace_tm_editor_custom_fields_with_own_sections( $fields ){
		$fields[] = self::META_KEY_DATA;
		$fields[] = self::META_KEY_SCENARIO;

		return $fields;
	}
}
