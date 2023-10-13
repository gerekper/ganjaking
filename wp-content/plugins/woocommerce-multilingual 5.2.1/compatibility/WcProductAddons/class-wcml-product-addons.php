<?php

use WCML\Compatibility\WcProductAddons\SharedHooks;
use WCML\Options\WPML;
use WPML\FP\Obj;
use WPML\FP\Str;

/**
 * Class WCML_Product_Addons
 */
class WCML_Product_Addons implements \IWPML_Action {

	const ADDONS_OPTION_KEY = SharedHooks::ADDONS_OPTION_KEY;

	/**
	 * @var SitePress
	 */
	public $sitepress;

	/**
	 * WCML_Product_Addons constructor.
	 *
	 * @param SitePress        $sitepress
	 */
	public function __construct( SitePress $sitepress ) {
		$this->sitepress = $sitepress;
	}

	public function add_hooks() {
		add_filter( 'get_product_addons_product_terms', [ $this, 'addons_product_terms' ] );
		add_action( 'wcml_product_addons_global_updated', [ $this, 'register_addons_strings' ], 10, 4 );
		add_action( 'woocommerce-product-addons_panel_start', [ $this, 'show_pointer_info' ] );

		if ( WPML::useAte() ) {
			add_action( 'wpml_pro_translation_completed', [ $this, 'save_addons_to_translation' ], 10, 3 );
		}

		if ( is_admin() ) {

			if ( SharedHooks::isGlobalAddonEditPage() ) {
				if ( ! isset( $_GET['edit'] ) ) {
					add_action( 'admin_notices', [ $this, 'inf_translate_strings' ] );
				}
			}

			if ( WPML::useAte() ) {
				add_filter( 'wpml_tm_translation_job_data', [ $this, 'append_addons_to_translation_package' ], 10, 2 );
			} else {
				add_action( 'wcml_gui_additional_box_html', [ $this, 'custom_box_html' ], 10, 3 );
				add_filter( 'wcml_gui_additional_box_data', [ $this, 'custom_box_html_data' ], 10, 3 );
				add_action( 'wcml_update_extra_fields', [ $this, 'addons_update' ], 10, 3 );
			}

			add_action( 'woocommerce_product_data_panels', [ $this, 'show_pointer_info' ] );

			add_filter( 'wcml_do_not_display_custom_fields_for_product', [ $this, 'replace_tm_editor_custom_fields_with_own_sections' ] );
		} else {
			add_filter( 'get_post_metadata', [ $this, 'translate_addons_strings' ], 10, 4 );
		}

		add_filter(
			'get_product_addons_global_query_args',
			[
				$this,
				'set_global_ids_in_query_args',
			]
		);
	}

	/**
	 * @param int      $meta_id
	 * @param int      $id
	 * @param string   $meta_key
	 * @param string[] $addons
	 */
	public function register_addons_strings( $meta_id, $id, $meta_key, $addons ) {
		if ( self::ADDONS_OPTION_KEY === $meta_key && 'global_product_addon' === get_post_type( $id ) ) {
			foreach ( $addons as $addon ) {
				$addon_data     = wpml_collect( $addon );
				$addon_type     = $addon_data->get( 'type' );
				$addon_position = $addon_data->get( 'position' );
				// register name
				do_action( 'wpml_register_single_string', 'wc_product_addons_strings', $id . '_addon_' . $addon_type . '_' . $addon_position . '_name', $addon_data->get( 'name' ) );
				// register description
				do_action( 'wpml_register_single_string', 'wc_product_addons_strings', $id . '_addon_' . $addon_type . '_' . $addon_position . '_description', $addon_data->get( 'description' ) );
				// register options labels
				if ( $addon_data->offsetExists( 'options' ) ) {
					foreach ( $addon_data->get( 'options' ) as $key => $option ) {
						do_action( 'wpml_register_single_string', 'wc_product_addons_strings', $id . '_addon_' . $addon_type . '_' . $addon_position . '_option_label_' . $key, wpml_collect( $option )->get( 'label' ) );
					}
				}
			}
		}
	}

	/**
	 * @param null   $null
	 * @param int    $object_id
	 * @param string $meta_key
	 * @param bool   $single
	 *
	 * @return array|null
	 */
	public function translate_addons_strings( $null, $object_id, $meta_key, $single ) {

		if ( self::ADDONS_OPTION_KEY === $meta_key && 'global_product_addon' === get_post_type( $object_id ) ) {

			remove_filter( 'get_post_metadata', [ $this, 'translate_addons_strings' ], 10 );
			$addons = get_post_meta( $object_id, $meta_key, true );
			add_filter( 'get_post_metadata', [ $this, 'translate_addons_strings' ], 10, 4 );

			if ( is_array( $addons ) ) {
				foreach ( $addons as $key => $addon ) {
					$addon_data     = wpml_collect( $addon );
					$addon_type     = $addon_data->get( 'type' );
					$addon_position = $addon_data->get( 'position' );
					// register name
					$addons[ $key ]['name'] = apply_filters( 'wpml_translate_single_string', $addon_data->get( 'name' ), 'wc_product_addons_strings', $object_id . '_addon_' . $addon_type . '_' . $addon_position . '_name' );
					// register description
					$addons[ $key ]['description'] = apply_filters( 'wpml_translate_single_string', $addon_data->get( 'description' ), 'wc_product_addons_strings', $object_id . '_addon_' . $addon_type . '_' . $addon_position . '_description' );
					// register options labels
					if ( $addon_data->offsetExists( 'options' ) ) {
						foreach ( $addon['options'] as $opt_key => $option ) {
							$addons[ $key ]['options'][ $opt_key ]['label'] = apply_filters( 'wpml_translate_single_string', wpml_collect( $option )->get( 'label' ), 'wc_product_addons_strings', $object_id . '_addon_' . $addon_type . '_' . $addon_position . '_option_label_' . $opt_key );
						}
					}
				}
			}

			return [ 0 => $addons ];
		}

		return $null;

	}

	/**
	 * @param array $product_terms
	 *
	 * @return array
	 */
	public function addons_product_terms( $product_terms ) {
		foreach ( $product_terms as $key => $product_term ) {
			$product_terms[ $key ] = apply_filters( 'translate_object_id', $product_term, 'product_cat', true, $this->sitepress->get_default_language() );
		}

		return $product_terms;
	}

	public function inf_translate_strings() {

		$pointer_ui = new WCML_Pointer_UI(
			/* translators: %1$s and %2$s are opening and closing HTML link tags */
			sprintf( __( 'You can translate strings related to global add-ons on the %1$sWPML String Translation page%2$s. Use the search on the top of that page to find the strings.', 'woocommerce-multilingual' ), '<a href="' . admin_url( 'admin.php?page=' . WPML_ST_FOLDER . '/menu/string-translation.php&context=wc_product_addons_strings' ) . '">', '</a>' ),
			WCML_Tracking_Link::getWcmlProductAddonsDoc(),
			'wpbody-content .woocommerce>h2'
		);

		$pointer_ui->show();
	}

	/**
	 * @param array    $package
	 * @param \WP_Post $post
	 *
	 * @return array
	 */
	public function append_addons_to_translation_package( $package, $post ) {
		if ( 'product' === $post->post_type ) {
			$add_field = function( $name, $value ) use ( &$package ) {
				if ( $value ) {
					$package['contents'][ $name ] = [
						'translate' => 1,
						'data'      => base64_encode( $value ),
						'format'    => 'base64',
					];
				}
			};

			foreach ( SharedHooks::getProductAddons( $post->ID ) as $addon_id => $addon_data ) {
				$add_field( self::get_job_field_name( $addon_id, 'name' ), Obj::prop( 'name', $addon_data ) );
				$add_field( self::get_job_field_name( $addon_id, 'description' ), Obj::prop( 'description', $addon_data ) );

				foreach ( (array) Obj::prop( 'options', $addon_data ) as $option_id => $option ) {
					$add_field( self::get_job_field_name( $addon_id, $option_id ), Obj::prop( 'label', $option ) );
				}
			}
		}

		return $package;
	}

	/**
	 * @param int    $post_id
	 * @param array  $fields
	 * @param object $job
	 */
	public function save_addons_to_translation( $post_id, $fields, $job ) {
		if (
			Str::startsWith( 'post_', $job->original_post_type )
			&& 'product' === get_post_type( $post_id )
		) {
			$get_translation = function( $field_name ) use ( $fields ) {
				return Obj::path( [ $field_name, 'data' ], $fields );
			};

			$addons = SharedHooks::getProductAddons( $post_id );

			foreach ( $addons as $addon_id => $addon_data ) {
				if ( isset( $addons[ $addon_id ]['name'] ) ) {
					$addons[ $addon_id ]['name']        = $get_translation( self::get_job_field_name( $addon_id, 'name' ) );
					$addons[ $addon_id ]['description'] = $get_translation( self::get_job_field_name( $addon_id, 'description' ) );
				}

				foreach ( (array) Obj::prop( 'options', $addon_data ) as $option_id => $option ) {
					if ( isset( $addons[ $addon_id ]['options'][ $option_id ]['label'] ) ) {
						$addons[ $addon_id ]['options'][ $option_id ]['label'] = $get_translation( self::get_job_field_name( $addon_id, $option_id ) );
					}
				}
			}

			update_post_meta( $post_id, self::ADDONS_OPTION_KEY, $addons );
		}
	}

	/**
	 * @param int        $addon_id
	 * @param int|string $name_or_id
	 *
	 * @return string
	 */
	private static function get_job_field_name( $addon_id, $name_or_id ) {
		return is_numeric( $name_or_id )
			? 'addon_' . $addon_id . '_option_' . $name_or_id . '_label'
			: 'addon_' . $addon_id . '_' . $name_or_id;
	}

	/**
	 * @deprecated This method is used by CTE only.
	 *
	 * @param object $obj
	 * @param int    $product_id
	 * @param array  $data
	 */
	public function custom_box_html( $obj, $product_id, $data ) {

		$product_addons = SharedHooks::getProductAddons( $product_id );

		if ( ! empty( $product_addons ) ) {
			foreach ( $product_addons as $addon_id => $product_addon ) {
				$addon_data = wpml_collect( $product_addon );

				/* translators: %s is a product addon name */
				$addons_section = new WPML_Editor_UI_Field_Section( sprintf( __( 'Product Add-ons Group "%s"', 'woocommerce-multilingual' ), $addon_data->get( 'name' ) ) );

				$group       = new WPML_Editor_UI_Field_Group( '', true );
				$addon_field = new WPML_Editor_UI_Single_Line_Field( 'addon_' . $addon_id . '_name', __( 'Name', 'woocommerce-multilingual' ), $data, false );
				$group->add_field( $addon_field );
				$addon_field = new WPML_Editor_UI_Single_Line_Field( 'addon_' . $addon_id . '_description', __( 'Description', 'woocommerce-multilingual' ), $data, false );
				$group->add_field( $addon_field );

				$addons_section->add_field( $group );

				if ( $addon_data->offsetExists( 'options' ) && $addon_data->get( 'options' ) ) {

					$labels_group = new WPML_Editor_UI_Field_Group( __( 'Options', 'woocommerce-multilingual' ), true );

					foreach ( $addon_data->get( 'options' ) as $option_id => $option ) {
						$option_label_field = new WPML_Editor_UI_Single_Line_Field( 'addon_' . $addon_id . '_option_' . $option_id . '_label', __( 'Label', 'woocommerce-multilingual' ), $data, false );
						$labels_group->add_field( $option_label_field );
					}
					$addons_section->add_field( $labels_group );
				}
				$obj->add_field( $addons_section );
			}
		}
	}

	/**
	 * @deprecated This method is used by CTE only.
	 *
	 * @param array  $data
	 * @param int    $product_id
	 * @param object $translation
	 *
	 * @return array
	 */
	public function custom_box_html_data( $data, $product_id, $translation ) {

		$product_addons = SharedHooks::getProductAddons( $product_id );

		if ( ! empty( $product_addons ) ) {
			foreach ( $product_addons as $addon_id => $product_addon ) {
				$addon_data                                    = wpml_collect( $product_addon );
				$data[ 'addon_' . $addon_id . '_name' ]        = [ 'original' => $addon_data->get( 'name' ) ];
				$data[ 'addon_' . $addon_id . '_description' ] = [ 'original' => $addon_data->get( 'description' ) ];
				if ( $addon_data->offsetExists( 'options' ) && $addon_data->get( 'options' ) ) {
					foreach ( $addon_data->get( 'options' ) as $option_id => $option ) {
						$data[ 'addon_' . $addon_id . '_option_' . $option_id . '_label' ] = [ 'original' => wpml_collect( $option )->get( 'label' ) ];
					}
				}
			}

			if ( $translation ) {
				$translated_product_addons = SharedHooks::getProductAddons( $translation->ID );
				if ( ! empty( $translated_product_addons ) ) {
					foreach ( $translated_product_addons as $addon_id => $transalted_product_addon ) {
						$translated_addon_data                                        = wpml_collect( $transalted_product_addon );
						$data[ 'addon_' . $addon_id . '_name' ]['translation']        = $translated_addon_data->get( 'name' );
						$data[ 'addon_' . $addon_id . '_description' ]['translation'] = $translated_addon_data->get( 'description' );
						if ( $translated_addon_data->offsetExists( 'options' ) && $translated_addon_data->get( 'options' ) ) {
							foreach ( $translated_addon_data->get( 'options' ) as $option_id => $option ) {
								$data[ 'addon_' . $addon_id . '_option_' . $option_id . '_label' ]['translation'] = wpml_collect( $option )->get( 'label' );
							}
						}
					}
				}
			}
		}

		return $data;
	}

	/**
	 * @deprecated This method is used by CTE only.
	 *
	 * @param int   $original_product_id
	 * @param int   $product_id
	 * @param array $data
	 */
	public function addons_update( $original_product_id, $product_id, $data ) {

		$product_addons = SharedHooks::getProductAddons( $original_product_id );

		if ( ! empty( $product_addons ) ) {

			foreach ( $product_addons as $addon_id => $product_addon ) {
				$addon_data                                 = wpml_collect( $product_addon );
				$product_addons[ $addon_id ]['name']        = $data[ md5( 'addon_' . $addon_id . '_name' ) ];
				$product_addons[ $addon_id ]['description'] = $data[ md5( 'addon_' . $addon_id . '_description' ) ];

				if ( $addon_data->offsetExists( 'options' ) && $addon_data->get( 'options' ) ) {
					foreach ( $addon_data->get( 'options' ) as $option_id => $option ) {
						$product_addons[ $addon_id ]['options'][ $option_id ]['label'] = $data[ md5( 'addon_' . $addon_id . '_option_' . $option_id . '_label' ) ];
					}
				}
			}
		}

		update_post_meta( $product_id, self::ADDONS_OPTION_KEY, $product_addons );
	}

	public function show_pointer_info() {

		$pointer_ui = new WCML_Pointer_UI(
			/* translators: %1$s and %2$s are opening and closing HTML link tags */
			sprintf( __( 'You can translate the Group Name, Group Description and every Option Label of your product add-on on the %1$sWooCommerce product translation page%2$s', 'woocommerce-multilingual' ), '<a href="' . admin_url( 'admin.php?page=wpml-wcml' ) . '">', '</a>' ),
			WCML_Tracking_Link::getWcmlProductAddonsDoc(),
			'product_addons_data>p'
		);

		$pointer_ui->show();
	}

	public function replace_tm_editor_custom_fields_with_own_sections( $fields ) {
		$fields[] = self::ADDONS_OPTION_KEY;

		return $fields;
	}

	public function set_global_ids_in_query_args( $args ) {

		if ( ! is_archive() ) {

			remove_filter( 'get_terms_args', [ $this->sitepress, 'get_terms_args_filter' ], 10 );
			remove_filter( 'get_term', [ $this->sitepress, 'get_term_adjust_id' ], 1 );
			remove_filter( 'terms_clauses', [ $this->sitepress, 'terms_clauses' ], 10 );

			$matched_addons_ids = get_posts( $args );
			if ( is_object( reset( $matched_addons_ids ) ) ) {
				$matched_addons_ids = wp_list_pluck( $matched_addons_ids, 'ID' );
			}

			if ( $matched_addons_ids ) {
				$args['include'] = $matched_addons_ids;
				unset( $args['tax_query'] );
			}

			add_filter( 'get_terms_args', [ $this->sitepress, 'get_terms_args_filter' ], 10, 2 );
			add_filter( 'get_term', [ $this->sitepress, 'get_term_adjust_id' ], 1 );
			add_filter( 'terms_clauses', [ $this->sitepress, 'terms_clauses' ], 10, 3 );
		}

		return $args;
	}
}
