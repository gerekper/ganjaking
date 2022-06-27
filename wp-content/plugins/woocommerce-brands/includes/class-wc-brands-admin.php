<?php

/**
 * WC_Brands_Admin class.
 */
class WC_Brands_Admin {

	var $settings_tabs;
	var $current_tab;
	var $fields = array();

	/**
	 * __construct function.
	 */
	public function __construct() {

		$this->current_tab = ( isset($_GET['tab'] ) ) ? $_GET['tab'] : 'general';

		add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );
		add_action( 'product_brand_add_form_fields', array( $this, 'add_thumbnail_field' ) );
		add_action( 'product_brand_edit_form_fields', array( $this, 'edit_thumbnail_field' ), 10, 2 );
		add_action( 'created_term', array( $this, 'thumbnail_field_save' ), 10, 3 );
		add_action( 'edit_term', array( $this, 'thumbnail_field_save' ), 10, 3 );
		add_action( 'product_brand_pre_add_form', array( $this, 'taxonomy_description' ) );
		add_filter( 'woocommerce_sortable_taxonomies', array( $this, 'sort_brands' ) );
		add_filter( 'manage_edit-product_brand_columns', array( $this, 'columns' ) );
		add_filter( 'manage_product_brand_custom_column', array( $this, 'column' ), 10, 3);
		add_filter( 'woocommerce_product_filters', array( $this, 'product_filter' ) );

		$this->settings_tabs = array(
			'brands' => __( 'Brands', 'woocommerce-brands' )
		);

		// Hiding setting for future depreciation. Only users who have touched this settings should see it.
		$setting_value = get_option( 'wc_brands_show_description' );
		if ( is_string( $setting_value ) ) {
			// Add the settings fields to each tab.
			$this->init_form_fields();
			add_action( 'woocommerce_get_sections_products', array( $this, 'add_settings_tab' ) );
			add_action( 'woocommerce_get_settings_products', array( $this, 'add_settings_section' ), null, 2 );
		}

		add_action( 'woocommerce_update_options_catalog', array( $this, 'save_admin_settings' ) );

		/* 2.1 */
		add_action( 'woocommerce_update_options_products', array( $this, 'save_admin_settings' ) );

		// Add brands filtering to the coupon creation screens.
		add_action( 'woocommerce_coupon_options_usage_restriction', array( $this, 'add_coupon_brands_fields' ) );
		add_action( 'woocommerce_coupon_options_save', array( $this, 'save_coupon_brands' ) );

		// Permalinks
		add_filter( 'pre_update_option_woocommerce_permalinks', array( $this, 'validate_product_base' ) );

		add_action( 'current_screen', array( $this, 'add_brand_base_setting' ) );

		// CSV Import/Export Support.
		// https://github.com/woocommerce/woocommerce/wiki/Product-CSV-Importer-&-Exporter
		// Import
		add_filter( 'woocommerce_csv_product_import_mapping_options', array( $this, 'add_column_to_importer_exporter' ), 10 );
		add_filter( 'woocommerce_csv_product_import_mapping_default_columns', array( $this, 'add_default_column_mapping' ), 10 );
		add_filter( 'woocommerce_product_import_inserted_product_object', array( $this, 'process_import' ), 10, 2 );

		// Export
		add_filter( 'woocommerce_product_export_column_names', array( $this, 'add_column_to_importer_exporter' ), 10 );
		add_filter( 'woocommerce_product_export_product_default_columns', array( $this, 'add_column_to_importer_exporter' ), 10 );
		add_filter( 'woocommerce_product_export_product_column_brand_ids', array( $this, 'get_column_value_brand_ids' ), 10, 2 );
	}

	/**
	 * Add the settings for the new "Brands" subtab.
	 * @access public
	 * @since  1.3.0
	 * @return  void
	 */
	public function add_settings_section ( $settings, $current_section ) {
		if ( 'brands' == $current_section ) {
			$settings = $this->settings;
		}
		return $settings;
	} // End add_settings_section()

	/**
	 * Add a new "Brands" subtab to the "Products" tab.
	 * @access public
	 * @since  1.3.0
	 * @return  void
	 */
	public function add_settings_tab ( $sections ) {
		$sections = array_merge( $sections, $this->settings_tabs );
		return $sections;
	} // End add_settings_tab()

	/**
	 * Display coupon filter fields relating to brands.
	 * @access public
	 * @since  1.3.0
	 * @return  void
	 */
	public function add_coupon_brands_fields () {
		global $post;
		// Brands
		?>
		<p class="form-field"><label for="product_brands"><?php _e( 'Product brands', 'woocommerce-brands' ); ?></label>
		<select id="product_brands" name="product_brands[]" style="width: 50%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php _e( 'Any brand', 'woocommerce-brands' ); ?>">
			<?php
				$category_ids = (array) get_post_meta( $post->ID, 'product_brands', true );
				$categories   = get_terms( 'product_brand', 'orderby=name&hide_empty=0' );

				if ( $categories ) foreach ( $categories as $cat ) {
					echo '<option value="' . esc_attr( $cat->term_id ) . '"' . selected( in_array( $cat->term_id, $category_ids ), true, false ) . '>' . esc_html( $cat->name ) . '</option>';
				}
			?>
		</select> <img class="help_tip" data-tip='<?php echo wc_sanitize_tooltip( __( 'A product must be associated with this brand for the coupon to remain valid or, for "Product Discounts", products with these brands will be discounted.', 'woocommerce-brands' ) ); ?>' src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" /></p>
		<?php

		// Exclude Brands
		?>
		<p class="form-field"><label for="exclude_product_brands"><?php esc_html_e( 'Exclude brands', 'woocommerce-brands' ); ?></label>
		<select id="exclude_product_brands" name="exclude_product_brands[]" style="width: 50%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php esc_attr_e( 'No brands', 'woocommerce-brands' ); ?>">
			<?php
				$category_ids = (array) get_post_meta( $post->ID, 'exclude_product_brands', true );
				$categories   = get_terms( 'product_brand', 'orderby=name&hide_empty=0' );

				if ( $categories ) foreach ( $categories as $cat ) {
					echo '<option value="' . esc_attr( $cat->term_id ) . '"' . selected( in_array( $cat->term_id, $category_ids ), true, false ) . '>' . esc_html( $cat->name ) . '</option>';
				}
			?>
		</select> <img class="help_tip" data-tip='<?php echo wc_sanitize_tooltip( __( 'Product must not be associated with these brands for the coupon to remain valid or, for "Product Discounts", products associated with these brands will not be discounted.', 'woocommerce-brands' ) ); ?>' src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" /></p>
		<?php
	} // End add_coupon_brands_fields()

	/**
	 * Save coupon filter fields relating to brands.
	 * @access public
	 * @since  1.3.0
	 * @return  void
	 */
	public function save_coupon_brands ( $post_id ) {
		$product_brands         = isset( $_POST['product_brands'] ) ? array_map( 'intval', $_POST['product_brands'] ) : array();
		$exclude_product_brands = isset( $_POST['exclude_product_brands'] ) ? array_map( 'intval', $_POST['exclude_product_brands'] ) : array();

		// Save
		update_post_meta( $post_id, 'product_brands', $product_brands );
		update_post_meta( $post_id, 'exclude_product_brands', $exclude_product_brands );
	} // End save_coupon_brands()

	/**
	 * init_form_fields()
	 *
	 * Prepare form fields to be used in the various tabs.
	 */
	function init_form_fields() {

		// Define settings
		$this->settings = apply_filters( 'woocommerce_brands_settings_fields', array(

			array( 'name' => __( 'Brands Archives', 'woocommerce-brands' ), 'type' => 'title','desc' => '', 'id' => 'brands_archives' ),

			array(
				'name' => __( 'Show description', 'woocommerce-brands' ),
				'desc' => __( 'Choose to show the brand description on the archive page. Turn this off if you intend to use the description widget instead. Please note: this is only for themes that do not show the description.', 'woocommerce-brands' ),
				'tip'  => '',
				'id'   => 'wc_brands_show_description',
				'css'  => '',
				'std'  => 'yes',
				'type' => 'checkbox',
			),

			array( 'type' => 'sectionend', 'id' => 'brands_archives' ),

		) ); // End brands settings
	}


	/**
	 * scripts function.
	 *
	 * @access public
	 * @return void
	 */
	function scripts() {
		$screen = get_current_screen();

		if ( in_array( $screen->id, array( 'edit-product_brand' ) ) ) {
			wp_enqueue_media();
			wp_enqueue_style( 'woocommerce_admin_styles' );
		}
	}

	/**
	 * admin_settings function.
	 *
	 * @access public
	 */
	function admin_settings() {
		woocommerce_admin_fields( $this->settings );
	}

	/**
	 * save_admin_settings function.
	 *
	 * @access public
	 */
	function save_admin_settings() {
		if ( isset( $_GET['section'] ) && 'brands' === $_GET['section'] ) {
			woocommerce_update_options( $this->settings );
		}
	}

	/**
	 * Category thumbnails
	 */
	function add_thumbnail_field() {
		global $woocommerce;
		?>
		<div class="form-field">
			<label><?php _e( 'Thumbnail', 'woocommerce-brands' ); ?></label>
			<div id="product_cat_thumbnail" style="float:left;margin-right:10px;"><img src="<?php echo wc_placeholder_img_src(); ?>" width="60px" height="60px" /></div>
			<div style="line-height:60px;">
				<input type="hidden" id="product_cat_thumbnail_id" name="product_cat_thumbnail_id" />
				<button type="button" class="upload_image_button button"><?php _e('Upload/Add image', 'woocommerce-brands'); ?></button>
				<button type="button" class="remove_image_button button"><?php _e('Remove image', 'woocommerce-brands'); ?></button>
			</div>
			<script type="text/javascript">

				jQuery(function(){
					 // Only show the "remove image" button when needed
					 if ( ! jQuery('#product_cat_thumbnail_id').val() ) {
						 jQuery('.remove_image_button').hide();
					 }

					// Uploading files
					var file_frame;

					jQuery(document).on( 'click', '.upload_image_button', function( event ){

						event.preventDefault();

						// If the media frame already exists, reopen it.
						if ( file_frame ) {
							file_frame.open();
							return;
						}

						// Create the media frame.
						file_frame = wp.media.frames.downloadable_file = wp.media({
							title: '<?php _e( 'Choose an image', 'woocommerce-brands' ); ?>',
							button: {
								text: '<?php _e( 'Use image', 'woocommerce-brands' ); ?>',
							},
							multiple: false
						});

						// When an image is selected, run a callback.
						file_frame.on( 'select', function() {
							attachment = file_frame.state().get('selection').first().toJSON();

							jQuery('#product_cat_thumbnail_id').val( attachment.id );
							jQuery('#product_cat_thumbnail img').attr('src', attachment.url );
							jQuery('.remove_image_button').show();
						});

						// Finally, open the modal.
						file_frame.open();
					});

					jQuery(document).on( 'click', '.remove_image_button', function( event ){
						jQuery('#product_cat_thumbnail img').attr('src', '<?php echo wc_placeholder_img_src(); ?>');
						jQuery('#product_cat_thumbnail_id').val('');
						jQuery('.remove_image_button').hide();
						return false;
					});
				});

			</script>
			<div class="clear"></div>
		</div>
		<?php
	}

	function edit_thumbnail_field( $term, $taxonomy ) {
		global $woocommerce;

		$image 			= '';
		$thumbnail_id 	= get_term_meta( $term->term_id, 'thumbnail_id', true );
		if ($thumbnail_id) {
			$image = wp_get_attachment_url( $thumbnail_id );
		}
		if ( empty( $image ) ) {
			$image = wc_placeholder_img_src();
		};
		?>
		<tr class="form-field">
			<th scope="row" valign="top"><label><?php _e('Thumbnail', 'woocommerce-brands'); ?></label></th>
			<td>
				<div id="product_cat_thumbnail" style="float:left;margin-right:10px;"><img src="<?php echo $image; ?>" width="60px" height="60px" /></div>
				<div style="line-height:60px;">
					<input type="hidden" id="product_cat_thumbnail_id" name="product_cat_thumbnail_id" value="<?php echo $thumbnail_id; ?>" />
					<button type="button" class="upload_image_button button"><?php _e('Upload/Add image', 'woocommerce-brands'); ?></button>
					<button type="button" class="remove_image_button button"><?php _e('Remove image', 'woocommerce-brands'); ?></button>
				</div>
				<script type="text/javascript">

					jQuery(function(){

						 // Only show the "remove image" button when needed
						 if ( ! jQuery('#product_cat_thumbnail_id').val() )
							 jQuery('.remove_image_button').hide();

						// Uploading files
						var file_frame;

						jQuery(document).on( 'click', '.upload_image_button', function( event ){

							event.preventDefault();

							// If the media frame already exists, reopen it.
							if ( file_frame ) {
								file_frame.open();
								return;
							}

							// Create the media frame.
							file_frame = wp.media.frames.downloadable_file = wp.media({
								title: '<?php _e( 'Choose an image', 'woocommerce-brands' ); ?>',
								button: {
									text: '<?php _e( 'Use image', 'woocommerce-brands' ); ?>',
								},
								multiple: false
							});

							// When an image is selected, run a callback.
							file_frame.on( 'select', function() {
								attachment = file_frame.state().get('selection').first().toJSON();

								jQuery('#product_cat_thumbnail_id').val( attachment.id );
								jQuery('#product_cat_thumbnail img').attr('src', attachment.url );
								jQuery('.remove_image_button').show();
							});

							// Finally, open the modal.
							file_frame.open();
						});

						jQuery(document).on( 'click', '.remove_image_button', function( event ){
							jQuery('#product_cat_thumbnail img').attr('src', '<?php echo wc_placeholder_img_src(); ?>');
							jQuery('#product_cat_thumbnail_id').val('');
							jQuery('.remove_image_button').hide();
							return false;
						});
					});

				</script>
				<div class="clear"></div>
			</td>
		</tr>
		<?php
	}

	function thumbnail_field_save( $term_id, $tt_id, $taxonomy ) {
		if ( isset( $_POST['product_cat_thumbnail_id'] ) ) {
			update_term_meta( $term_id, 'thumbnail_id', absint( $_POST['product_cat_thumbnail_id'] ) );
		}
	}

	/**
	 * Description for brand page
	 */
	function taxonomy_description() {
		echo wpautop( __( 'Brands be added and managed from this screen. You can optionally upload a brand image to display in brand widgets and on brand archives', 'woocommerce-brands' ) );
	}

	/**
	 * sort_brands function.
	 *
	 * @access public
	 */
	function sort_brands( $sortable ) {
		$sortable[] = 'product_brand';
		return $sortable;
	}

	/**
	 * columns function.
	 *
	 * @access public
	 * @param mixed $columns
	 */
	function columns( $columns ) {
		if ( empty( $columns ) ) {
			return;
		}

		$new_columns = array();
		$new_columns['cb'] = $columns['cb'];
		$new_columns['thumb'] = __('Image', 'woocommerce-brands');
		unset( $columns['cb'] );
		$columns = array_merge( $new_columns, $columns );
		return $columns;
	}

	/**
	 * column function.
	 *
	 * @access public
	 * @param mixed $columns
	 * @param mixed $column
	 * @param mixed $id
	 */
	function column( $columns, $column, $id ) {
		if ( $column == 'thumb' ) {
			global $woocommerce;

			$image        = '';
			$thumbnail_id = get_term_meta( $id, 'thumbnail_id', true );

			if ( $thumbnail_id ) {
				$image = wp_get_attachment_url( $thumbnail_id );
			}
			if ( empty( $image ) ) {
				$image = wc_placeholder_img_src();
			}

			$columns .= '<img src="' . $image . '" alt="Thumbnail" class="wp-post-image" height="48" width="48" />';

		}
		return $columns;
	}

	/**
	 * Filter products by brand
	 */
	public function product_filter( $filters ) {
		global $wp_query;

		ob_start();

		$current_product_brand = isset( $wp_query->query['product_brand'] ) ? $wp_query->query['product_brand'] : '';
		$args                  = array(
			'pad_counts'         => 1,
			'show_count'         => 1,
			'hierarchical'       => 1,
			'hide_empty'         => 0,
			'show_uncategorized' => 1,
			'orderby'            => 'name',
			'selected'           => $current_product_brand,
			'show_option_none'   => __( 'Select a brand', 'woocommerce-brands' ),
			'option_none_value'  => '',
			'value_field'        => 'slug',
			'taxonomy'           => 'product_brand',
			'name'               => 'product_brand',
			'class'              => 'dropdown_product_brand',
		);

		wc_product_dropdown_categories( $args );

		return $filters . PHP_EOL . ob_get_clean();
	}

	/**
	 * Add brand base permalink setting.
	 */
	public function add_brand_base_setting() {
		if ( ! $screen = get_current_screen() || 'options-permalink' !== $screen->id ) {
			return;
		}

		add_settings_field(
			'woocommerce_product_brand_slug',
			__( 'Product brand base', 'woocommerce-brands' ),
			array( $this, 'product_brand_slug_input' ),
			'permalink',
			'optional'
		);

		$this->save_permalink_settings();
	}

	/**
	 * Add a slug input box.
	 */
	public function product_brand_slug_input() {
		$permalink = get_option( 'woocommerce_brand_permalink', '' );
		?>
		<input name="woocommerce_product_brand_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $permalink ); ?>" placeholder="<?php echo esc_attr_x( 'brand', 'slug', 'woocommerce-brands' ) ?>" />
		<?php
	}

	/**
	 * Save permalnks settings.
	 *
	 * We need to save the options ourselves;
	 * settings api does not trigger save for the permalinks page.
	 */
	public function save_permalink_settings() {
		if ( ! is_admin() || ! isset( $_POST['permalink_structure'] ) ) {
			return;
		}

		update_option( 'woocommerce_brand_permalink', wc_sanitize_permalink( trim( $_POST['woocommerce_product_brand_slug'] ) ) );
	}

	/**
	 * Validate the product base.
	 *
	 * Must have an additional slug, not just the brand as the base.
	 */
	public function validate_product_base( $value ) {
		if ( '/%product_brand%/' === trailingslashit( $value['product_base'] ) ) {
			$value['product_base'] = '/' . _x( 'product', 'slug', 'woocommerce' ) . $value['product_base'];
		}

		return $value;
	}

	/**
	 * Add csv column for importing/exporting.
	 *
	 * @param  array $options Mapping options
	 * @return array $options
	 */
	public function add_column_to_importer_exporter( $options ) {
		$options['brand_ids'] = __( 'Brands', 'woocommerce-brands' );
		return $options;
	}

	/**
	 * Add default column mapping.
	 *
	 * @param  array $mappings
	 * @return array $mappings
	 */
	public function add_default_column_mapping( $mappings ) {
		$new_mapping = array( __( 'Brands', 'woocommerce-brands' ) => 'brand_ids' );
		return array_merge( $mappings, $new_mapping );
	}

	/**
	 * Add brands to newly imported product.
	 *
	 * @param WC_Product $product Product being imported.
	 * @param array      $data    Raw CSV data.
	 */
	public function process_import( $product, $data ) {
		if ( empty( $data['brand_ids'] ) ) {
			return;
		}

		$brand_ids = array_map( 'intval', $this->parse_brands_field( $data['brand_ids'] ) );

		wp_set_object_terms( $product->get_id(), $brand_ids, 'product_brand' );
	}

	/**
	 * Parse brands field from a CSV during import.
	 *
	 * Based on WC_Product_CSV_Importer::parse_categories_field()
	 *
	 * @param string $value Field value.
	 * @return array of brand IDs
	 */
	public function parse_brands_field( $value ) {

		// Based on WC_Product_Importer::explode_values()
		$values    = str_replace( '\\,', '::separator::', explode( ',', $value ) );
		$row_terms = array();
		foreach( $values as $row_value ) {
			$row_terms[] = trim( str_replace( '::separator::', ',', $row_value ) );
		}

		$brands = array();
		foreach ( $row_terms as $row_term ) {
			$parent = null;

			// WC Core uses '>', but for some reason it's already escaped at this point.
			$_terms = array_map( 'trim', explode( '&gt;', $row_term ) );
			$total  = count( $_terms );

			foreach ( $_terms as $index => $_term ) {
				$term = term_exists( $_term, 'product_brand', $parent );

				if ( is_array( $term ) ) {
					$term_id = $term['term_id'];
				} else {
					$term = wp_insert_term( $_term, 'product_brand', array( 'parent' => intval( $parent ) ) );

					if ( is_wp_error( $term ) ) {
						break; // We cannot continue if the term cannot be inserted.
					}

					$term_id = $term['term_id'];
				}

				// Only requires assign the last category.
				if ( ( 1 + $index ) === $total ) {
					$brands[] = $term_id;
				} else {
					// Store parent to be able to insert or query brands based in parent ID.
					$parent = $term_id;
				}
			}
		}

		return $brands;
	}

	/**
	 * Get brands column value for csv export.
	 *
	 * @param string     $value   What will be exported.
	 * @param WC_Product $product Product being exported.
	 * @return string    Brands separated by commas and child brands as "parent > child".
	 */
	public function get_column_value_brand_ids( $value, $product ) {
		$brand_ids = wp_parse_id_list( wp_get_post_terms( $product->get_id(), 'product_brand', array( 'fields' => 'ids' ) ) );

		if ( ! count( $brand_ids ) ) {
			return '';
		}

		// Based on WC_CSV_Exporter::format_term_ids()
		$formatted_brands = array();
		foreach ( $brand_ids as $brand_id ) {
			$formatted_term = array();
			$ancestor_ids   = array_reverse( get_ancestors( $brand_id, 'product_brand' ) );

			foreach ( $ancestor_ids as $ancestor_id ) {
				$term = get_term( $ancestor_id, 'product_brand' );
				if ( $term && ! is_wp_error( $term ) ) {
					$formatted_term[] = $term->name;
				}
			}

			$term = get_term( $brand_id, 'product_brand' );

			if ( $term && ! is_wp_error( $term ) ) {
				$formatted_term[] = $term->name;
			}

			$formatted_brands[] = implode( ' > ', $formatted_term );
		}

		// Based on WC_CSV_Exporter::implode_values()
		$values_to_implode  = array();
		foreach ( $formatted_brands as $brand ) {
			$brand               = (string) is_scalar( $brand ) ? $brand : '';
			$values_to_implode[] = str_replace( ',', '\\,', $brand );
		}

		return implode( ', ', $values_to_implode );
	}

}

/**
 * Load the admin class on plugins_loaded.
 * @access public
 * @since  1.3.0
 * @return  void
 */
function _wc_brands_admin_load () {
	$GLOBALS['WC_Brands_Admin'] = new WC_Brands_Admin();
} // End _wc_brands_admin_load()
add_action( 'plugins_loaded', '_wc_brands_admin_load' );
