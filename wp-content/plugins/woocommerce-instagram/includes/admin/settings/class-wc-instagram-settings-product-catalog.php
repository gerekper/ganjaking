<?php
/**
 * Settings: Product Catalog.
 *
 * @package WC_Instagram/Admin/Settings
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_Instagram_Settings_API', false ) ) {
	include_once 'abstract-class-wc-instagram-settings-api.php';
}

if ( ! class_exists( 'WC_Instagram_Settings_Product_Catalog', false ) ) {
	/**
	 * WC_Instagram_Settings_Product_Catalog class.
	 */
	class WC_Instagram_Settings_Product_Catalog extends WC_Instagram_Settings_API {

		/**
		 * The catalog ID.
		 *
		 * @var mixed
		 */
		public $catalog_id;

		/**
		 * Constructor.
		 *
		 * @since 3.0.0
		 *
		 * @param mixed $catalog_id Optional. The catalog ID.
		 */
		public function __construct( $catalog_id = 'new' ) {
			$this->id               = 'product_catalogs';
			$this->form_title       = _x( 'Instagram > Product Catalog', 'settings page title', 'woocommerce-instagram' );
			$this->form_description = _x( 'A catalog to import the products to a Facebook Catalog.', 'settings page description', 'woocommerce-instagram' );

			$this->catalog_id = $catalog_id;

			parent::__construct();
		}

		/**
		 * Gets if it's a new catalog or not.
		 *
		 * @since 3.0.0
		 *
		 * @return bool
		 */
		public function is_new() {
			return ( 'new' === $this->catalog_id );
		}

		/**
		 * Gets the product catalogs.
		 *
		 * @since 3.2.0
		 *
		 * @return array
		 */
		public function get_product_catalogs() {
			$settings = get_option( $this->get_option_key(), array() );

			return ( is_array( $settings ) ? $settings : array() );
		}

		/**
		 * Gets the settings from the option stored in the WP DB.
		 *
		 * @since 3.0.0
		 *
		 * @return array
		 */
		public function get_option_settings() {
			$catalogs = $this->get_product_catalogs();

			return ( ! $this->is_new() && ! empty( $catalogs[ $this->catalog_id ] ) ? $catalogs[ $this->catalog_id ] : array() );
		}

		/**
		 * Enqueues the settings scripts.
		 *
		 * @since 3.0.0
		 */
		public function enqueue_scripts() {
			$suffix = wc_instagram_get_scripts_suffix();

			wp_enqueue_script( 'wc-instagram-product-catalog', WC_INSTAGRAM_URL . "assets/js/admin/product-catalog{$suffix}.js", array( 'wc-instagram-subset-fields', 'wc-instagram-editable-url', 'selectWoo' ), WC_INSTAGRAM_VERSION, true );
			wp_localize_script(
				'wc-instagram-product-catalog',
				'wc_instagram_product_catalog_params',
				array(
					'ajax_url'     => admin_url( 'admin-ajax.php' ),
					'catalog_id'   => $this->catalog_id,
					'catalog_url'  => wc_instagram_get_product_catalog_url( '{slug}' ),
					'tax_based_on' => get_option( 'woocommerce_tax_based_on' ),
					'delete_link'  => $this->get_delete_link_html(),
					'nonce'        => wp_create_nonce( 'refresh_google_product_category_field' ),
				)
			);
		}

		/**
		 * Gets the HTML content for the link used to delete the catalog.
		 *
		 * @since 3.0.0
		 *
		 * @return string
		 */
		protected function get_delete_link_html() {
			if ( $this->is_new() ) {
				return '';
			}

			$delete_url = wp_nonce_url(
				wc_instagram_get_settings_url(
					array(
						'catalog_id' => $this->catalog_id,
						'action'     => 'delete',
					)
				),
				'wc_instagram_delete_product_catalog'
			);

			return sprintf(
				'<a class="wc-instagram-product-catalog-delete" href="%1$s" data-confirm="%2$s">%3$s</a>',
				esc_url( $delete_url ),
				esc_html__( 'Are you sure you want to delete this catalog?', 'woocommerce-instagram' ),
				esc_html__( 'Delete catalog', 'woocommerce-instagram' )
			);
		}

		/**
		 * Outputs the settings notices.
		 *
		 * @since 3.0.0
		 */
		public function output_notices() {
			if (
				isset( $_GET['action'] ) && 'created' === $_GET['action'] && // phpcs:ignore WordPress.Security.NonceVerification
				wc_instagram_get_settings_url( array( 'catalog_id' => 'new' ) ) === wp_get_referer()
			) {
				echo '<div id="message" class="updated inline"><p><strong>' . esc_html_x( 'Product catalog created successfully.', 'settings notice', 'woocommerce-instagram' ) . '</strong></p></div>';
			}
		}

		/**
		 * Initialise form fields.
		 *
		 * @since 3.0.0
		 */
		public function init_form_fields() {
			$countries = array( '' => __( 'Select a country&hellip;', 'woocommerce-instagram' ) ) + WC()->countries->get_allowed_countries();

			$slug_description = sprintf(
				/* translators: 1: documentation link, 2: arial-label */
				_x( 'Check the <a href="%1$s" aria-label="%2$s" target="_blank">documentation</a> to learn how to use this URL.', 'setting desc', 'woocommerce-instagram' ),
				esc_url( 'https://docs.woocommerce.com/document/woocommerce-instagram/shoppable/' ),
				esc_attr_x( 'View WooCommerce Instagram Shopping documentation', 'aria-label: documentation link', 'woocommerce-instagram' )
			);

			$this->form_fields = array_merge(
				array(
					'title'           => array(
						'title'             => _x( 'Title', 'setting title', 'woocommerce-instagram' ),
						'desc_tip'          => _x( 'The catalog title.', 'setting desc', 'woocommerce-instagram' ),
						'type'              => 'text',
						'custom_attributes' => array(
							'required' => 'required',
						),
					),
					'slug'            => array(
						'title'             => _x( 'Feed URL', 'setting title', 'woocommerce-instagram' ),
						'type'              => 'text',
						'desc_tip'          => _x( 'The data feed URL.', 'setting desc', 'woocommerce-instagram' ),
						'description'       => $slug_description,
						'class'             => 'wc-instagram-field-editable-url',
						'custom_attributes' => array(
							'data-url' => wc_instagram_get_product_catalog_url( '{editable}' ),
						),
					),
					'product_filters' => array(
						'title'       => _x( 'Product filters', 'settings section title', 'woocommerce-instagram' ),
						'description' => _x( 'Filter the products that will be included in the catalog.', 'settings section desc', 'woocommerce-instagram' ),
						'type'        => 'title',
					),
				),
				$this->generate_subset_fields(
					'product_cats',
					array(
						'title'    => _x( 'Product categories', 'setting title', 'woocommerce-instagram' ),
						'desc_tip' => _x( 'Choose the product categories to include in the catalog.', 'setting desc', 'woocommerce-instagram' ),
						'options'  => array(
							''           => _x( 'All product categories', 'setting option', 'woocommerce-instagram' ),
							'all_except' => _x( 'All product categories, except&hellip;', 'setting option', 'woocommerce-instagram' ),
							'specific'   => _x( 'Only specific product categories', 'setting option', 'woocommerce-instagram' ),
						),
					),
					'product_categories'
				),
				$this->generate_subset_fields(
					'product_types',
					array(
						'title'    => _x( 'Product types', 'setting title', 'woocommerce-instagram' ),
						'desc_tip' => _x( 'Choose the product types to include in the catalog.', 'setting desc', 'woocommerce-instagram' ),
						'options'  => array(
							''           => _x( 'All product types', 'setting option', 'woocommerce-instagram' ),
							'all_except' => _x( 'All product types, except&hellip;', 'setting option', 'woocommerce-instagram' ),
							'specific'   => _x( 'Only specific product types', 'setting option', 'woocommerce-instagram' ),
						),
					),
					array(
						'type'              => 'multiselect',
						'class'             => 'wc-enhanced-select-nostd',
						'options'           => wc_get_product_types(),
						'custom_attributes' => array(
							'data-placeholder' => _x( 'Select product types', 'setting placeholder', 'woocommerce-instagram' ),
						),
					)
				),
				array(
					'virtual_products'            => array(
						'title'       => _x( 'Virtual products', 'setting title', 'woocommerce-instagram' ),
						'desc_tip'    => _x( 'Filter virtual products.', 'setting desc', 'woocommerce-instagram' ),
						'description' => _x( 'This filter only applies to simple products.', 'setting desc', 'woocommerce-instagram' ),
						'type'        => 'select',
						'options'     => array(
							''    => _x( 'All products', 'setting option', 'woocommerce-instagram' ),
							'yes' => _x( 'Only virtual products', 'setting option', 'woocommerce-instagram' ),
							'no'  => _x( 'Only non-virtual products', 'setting option', 'woocommerce-instagram' ),
						),
					),
					'downloadable_products'       => array(
						'title'       => _x( 'Downloadable products', 'setting title', 'woocommerce-instagram' ),
						'desc_tip'    => _x( 'Filter downloadable products.', 'setting desc', 'woocommerce-instagram' ),
						'description' => _x( 'This filter only applies to simple products.', 'setting desc', 'woocommerce-instagram' ),
						'type'        => 'select',
						'options'     => array(
							''    => _x( 'All products', 'setting option', 'woocommerce-instagram' ),
							'yes' => _x( 'Only downloadable products', 'setting option', 'woocommerce-instagram' ),
							'no'  => _x( 'Only non-downloadable products', 'setting option', 'woocommerce-instagram' ),
						),
					),
					'stock_status'                => array(
						'title'    => _x( 'Stock status', 'setting title', 'woocommerce-instagram' ),
						'desc_tip' => _x( 'Filter the products by the stock status.', 'setting desc', 'woocommerce-instagram' ),
						'type'     => 'select',
						'options'  => array(
							''           => _x( 'All products', 'setting option', 'woocommerce-instagram' ),
							'instock'    => _x( 'Only in-stock products', 'setting option', 'woocommerce-instagram' ),
							'outofstock' => _x( 'Only out of stock products', 'setting option', 'woocommerce-instagram' ),
						),
					),
					'include_product_ids'         => array(
						'title'    => _x( 'Include products', 'setting title', 'woocommerce-instagram' ),
						'type'     => 'product_search',
						'desc_tip' => _x( 'Choose the products to include in the catalog.', 'setting desc', 'woocommerce-instagram' ),
						'multiple' => true,
					),
					'exclude_product_ids'         => array(
						'title'    => _x( 'Exclude products', 'setting title', 'woocommerce-instagram' ),
						'type'     => 'product_search',
						'desc_tip' => _x( 'Choose the products to exclude from the catalog.', 'setting desc', 'woocommerce-instagram' ),
						'multiple' => true,
					),
					'product_data'                => array(
						'title'       => _x( 'Product data', 'settings section title', 'woocommerce-instagram' ),
						'description' => _x( 'Customize the product data for this catalog.', 'settings section desc', 'woocommerce-instagram' ),
						'type'        => 'title',
					),
					'include_variations'          => array(
						'title'       => _x( 'Product variations', 'setting title', 'woocommerce-instagram' ),
						'label'       => _x( 'Include product variations.', 'setting desc', 'woocommerce-instagram' ),
						'description' => _x( 'Replaces the variable products by their variations.', 'setting desc', 'woocommerce-instagram' ),
						'type'        => 'checkbox',
						'default'     => 'yes',
					),
					'include_currency'            => array(
						'title'       => _x( 'Currency code', 'setting title', 'woocommerce-instagram' ),
						'label'       => _x( 'Include the currency code in prices.', 'setting desc', 'woocommerce-instagram' ),
						'description' => _x( 'Uncheck this setting to create a catalog with generic prices.', 'setting desc', 'woocommerce-instagram' ),
						'type'        => 'checkbox',
						'default'     => 'yes',
					),
					'description_field'           => array(
						'title'    => _x( 'Description field', 'setting title', 'woocommerce-instagram' ),
						'desc_tip' => _x( 'Choose the field that will be used as the product description.', 'setting desc', 'woocommerce-instagram' ),
						'type'     => 'select',
						'default'  => 'description',
						'options'  => array(
							'description'       => _x( 'Description', 'setting option', 'woocommerce-instagram' ),
							'short_description' => _x( 'Short description', 'setting option', 'woocommerce-instagram' ),
						),
					),
					'variation_description_field' => array(
						'title'    => _x( 'Variation description field', 'setting title', 'woocommerce-instagram' ),
						'desc_tip' => _x( 'Choose the field that will be used as the variation description.', 'setting desc', 'woocommerce-instagram' ),
						'type'     => 'select',
						'default'  => 'description',
						'options'  => array(
							'description'              => _x( 'Description', 'setting option', 'woocommerce-instagram' ),
							'parent_description'       => _x( 'Parent description', 'setting option', 'woocommerce-instagram' ),
							'parent_short_description' => _x( 'Parent short description', 'setting option', 'woocommerce-instagram' ),
						),
					),
					'include_tax'                 => array(
						'title'       => _x( 'Include tax', 'setting title', 'woocommerce-instagram' ),
						'label'       => _x( 'Include tax in prices.', 'setting desc', 'woocommerce-instagram' ),
						'description' => _x( 'Prices with tax included are mandatory in some countries.', 'setting desc', 'woocommerce-instagram' ),
						'type'        => 'checkbox',
					),
					'tax_country'                 => array(
						'title'       => _x( 'Tax country', 'setting title', 'woocommerce-instagram' ),
						'desc_tip'    => _x( 'The country where the taxes will be based on.', 'setting desc', 'woocommerce-instagram' ),
						'description' => _x( 'Calculate tax based on the shop base address by default.', 'setting desc', 'woocommerce-instagram' ),
						'type'        => 'select',
						'class'       => 'wc-enhanced-select',
						'options'     => $countries,
					),
					'product_id'                  => array(
						'title'       => _x( 'Product ID', 'setting title', 'woocommerce-instagram' ),
						'desc_tip'    => _x( 'Product ID format.', 'setting desc', 'woocommerce-instagram' ) . ' ' . $this->get_placeholder_text( array( '{product_id}' ) ),
						'description' => _x( 'Placeholders will be replaced by the product data.', 'setting desc', 'woocommerce-instagram' ),
						'type'        => 'text',
						'placeholder' => '{product_id}',
					),
					'product_group_id'            => array(
						'title'       => _x( 'Group ID', 'setting title', 'woocommerce-instagram' ),
						'desc_tip'    => _x( 'Group ID format to group the variations of a product.', 'setting desc', 'woocommerce-instagram' ) . ' ' . $this->get_placeholder_text( array( '{parent_id}' ) ),
						'description' => _x( 'Placeholders will be replaced by the product data.', 'setting desc', 'woocommerce-instagram' ),
						'type'        => 'text',
						'placeholder' => '{parent_id}',
					),
					'product_mpn'                 => array(
						'title'       => _x( 'Product MPN', 'setting title', 'woocommerce-instagram' ),
						'desc_tip'    => _x( 'Choose the product field that will be used as the MPN number.', 'setting desc', 'woocommerce-instagram' ),
						'description' => _x( 'The MPN (Manufacturer Part Number) is a unique number that identifies the products.', 'setting desc', 'woocommerce-instagram' ),
						'type'        => 'select',
						'default'     => 'id',
						'options'     => array(
							'id'     => _x( 'Product ID', 'setting option', 'woocommerce-instagram' ),
							'sku'    => _x( 'Product SKU', 'setting option', 'woocommerce-instagram' ),
							'custom' => _x( 'Custom', 'setting option', 'woocommerce-instagram' ),
						),
					),
					'custom_mpn'                  => array(
						'title'       => _x( 'Custom MPN', 'setting title', 'woocommerce-instagram' ),
						'desc_tip'    => _x( 'Custom MPN format.', 'setting desc', 'woocommerce-instagram' ) . ' ' . $this->get_placeholder_text( array( '{product_id}', '{product_sku}' ) ),
						'description' => _x( 'Placeholders will be replaced by the product data.', 'setting desc', 'woocommerce-instagram' ),
						'type'        => 'text',
						'placeholder' => '{product_id}',
					),
					'product_brand'               => array(
						'title'       => _x( 'Brand', 'setting title', 'woocommerce-instagram' ),
						'desc_tip'    => _x( 'The brand of the products.', 'setting desc', 'woocommerce-instagram' ),
						'description' => _x( 'This option can be set per product.', 'setting desc', 'woocommerce-instagram' ),
						'type'        => 'text',
						'placeholder' => _x( 'N/A', 'setting desc', 'woocommerce-instagram' ),
					),
					'product_condition'           => array(
						'title'       => _x( 'Condition', 'setting title', 'woocommerce-instagram' ),
						'desc_tip'    => _x( 'The condition of the products.', 'setting desc', 'woocommerce-instagram' ),
						'description' => _x( 'This option can be set per product.', 'setting desc', 'woocommerce-instagram' ),
						'type'        => 'select',
						'default'     => 'new',
						'options'     => wc_instagram_get_product_conditions(),
					),
					'product_images_option'       => array(
						'title'    => _x( 'Images', 'setting title', 'woocommerce-instagram' ),
						'desc_tip' => _x( 'The product images to include in the catalog.', 'setting desc', 'woocommerce-instagram' ),
						'type'     => 'select',
						'default'  => 'all',
						'options'  => array(
							'all'      => _x( 'All the images', 'setting option', 'woocommerce-instagram' ),
							'featured' => _x( 'Featured image', 'setting option', 'woocommerce-instagram' ),
						),
					),
					'product_google_category'     => array(
						'title'       => _x( 'Google Product Category', 'setting title', 'woocommerce-instagram' ),
						'desc_tip'    => _x( 'Default Google Product Category.', 'setting desc', 'woocommerce-instagram' ),
						'description' => _x( 'This option can be set per product.', 'setting desc', 'woocommerce-instagram' ),
						'type'        => 'google_product_category',
					),
				)
			);
		}

		/**
		 * Gets the placeholder text.
		 *
		 * @since 3.0.0
		 *
		 * @param array $placeholders A list of available placeholders.
		 * @return string
		 */
		public function get_placeholder_text( $placeholders = array() ) {
			$text = '';

			if ( ! empty( $placeholders ) ) {
				/* translators: %s: list of placeholders */
				$text = sprintf( _x( 'Available placeholders: %s', 'setting desc', 'woocommerce-instagram' ), join( ', ', $placeholders ) );
			}

			return $text;
		}

		/**
		 * Generates the HTML for a 'product_categories' field.
		 *
		 * @since 3.0.0
		 *
		 * @param string $key  The field key.
		 * @param mixed  $data The field data.
		 * @return string
		 */
		public function generate_product_categories_html( $key, $data ) {
			$defaults = array(
				'type'              => 'multiselect',
				'class'             => 'wc-enhanced-select-nostd',
				'options'           => wc_instagram_get_product_categories_choices(),
				'custom_attributes' => array(
					'data-placeholder' => _x( 'Select product categories', 'setting placeholder', 'woocommerce-instagram' ),
				),
			);

			$data = wp_parse_args( $data, $defaults );

			return $this->generate_multiselect_html( $key, $data );
		}

		/**
		 * Generates the HTML for a 'product_search' field.
		 *
		 * @since 1.0.0
		 *
		 * @param string $key  The field key.
		 * @param mixed  $data The field data.
		 * @return string
		 */
		public function generate_product_search_html( $key, $data ) {
			$options = array();

			if ( ! empty( $this->settings[ $key ] ) ) {
				$product_ids = array_values( $this->settings[ $key ] );
				$options     = array_combine( $product_ids, array_map( 'wc_instagram_get_product_choice_label', $product_ids ) );
			}

			$multiple   = ( isset( $data['multiple'] ) && $data['multiple'] );
			$variations = ( isset( $data['variations'] ) && $data['variations'] );

			// Exclude these parameters from the merge.
			unset( $data['multiple'], $data['variations'] );

			$data = wp_parse_args(
				$data,
				array(
					'class'             => 'wc-product-search',
					'css'               => '',
					'options'           => $options,
					'custom_attributes' => array(
						'data-placeholder' => _x( 'Search for a product&hellip;', 'setting placeholder', 'woocommerce-instagram' ),
						'data-allow_clear' => true,
					),
				)
			);

			$data['custom_attributes']['data-multiple'] = ( $multiple ? 'true' : 'false' );
			$data['custom_attributes']['data-action']   = 'woocommerce_json_search_products' . ( $variations ? '_and_variations' : '' );

			return $this->generate_multiselect_html( $key, $data );
		}

		/**
		 * Generates the HTML for a 'google_product_category' field.
		 *
		 * @since 3.3.0
		 *
		 * @param string $key  Key of the field in the settings array.
		 * @param array  $data The prebuilt data to construct the selects.
		 * @return false|string
		 */
		public function generate_google_product_category_html( $key, $data ) {
			$field_key = $this->get_field_key( $key );
			$defaults  = array(
				'title'             => '',
				'class'             => '',
				'css'               => '',
				'placeholder'       => '',
				'desc_tip'          => false,
				'description'       => '',
				'custom_attributes' => array(),
				'value'             => $this->get_option( $key, 0 ),
			);

			$data      = wp_parse_args( $data, $defaults );
			$selects   = WC_Instagram_Google_Product_Categories::get_parents( $data['value'] );
			$selects[] = $data['value'];

			ob_start();
			$this->output_field_start( $key, $data );

			foreach ( $selects as $index => $select ) :
				$placeholder = ( $index ? __( 'Select a subcategory &hellip;', 'woocommerce-instagram' ) : __( 'Select a category &hellip;', 'woocommerce-instagram' ) );
				$options     = WC_Instagram_Google_Product_Categories::get_sibling_titles( $select );
				$options     = array( '' => $placeholder ) + $options;

				echo '<span class="wc-instagram-gpc-select-wrapper">';
				$this->output_select_field(
					array(
						'class'   => 'wc-enhanced-select wc-instagram-gpc-select ' . $data['class'],
						'css'     => $data['css'],
						'options' => $options,
						'value'   => $select,
					)
				);
				echo '</span>';
			endforeach;

			$selected_value_children = WC_Instagram_Google_Product_Categories::get_children( $data['value'] );

			if ( ! empty( $selected_value_children ) ) :
				$options = array( '' => __( 'Select a subcategory &hellip;', 'woocommerce-instagram' ) ) + WC_Instagram_Google_Product_Categories::get_titles( $selected_value_children );

				echo '<span class="wc-instagram-gpc-select-wrapper">';
				$this->output_select_field(
					array(
						'class'   => 'wc-enhanced-select wc-instagram-gpc-select ' . $data['class'],
						'css'     => $data['css'],
						'options' => $options,
					)
				);
				echo '</span>';
			endif;

			printf(
				'<input type="hidden" id="%1$s" name="%1$s" value="%2$s" />',
				esc_attr( $field_key ),
				esc_attr( $data['value'] )
			);

			$this->output_field_end( $key, $data );

			return ob_get_clean();
		}

		/**
		 * Outputs a select field.
		 *
		 * @since 3.3.0
		 *
		 * @param array $data The select data.
		 */
		protected function output_select_field( $data ) {
			$data = wp_parse_args(
				$data,
				array(
					'class'   => '',
					'css'     => '',
					'value'   => '',
					'options' => array(),
				)
			);

			printf(
				'<select class="select %1$s" style="%2$s" %3$s>',
				esc_attr( $data['class'] ),
				esc_attr( $data['css'] ),
				$this->get_custom_attribute_html( $data ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			);
			?>
			<?php foreach ( $data['options'] as $option_key => $option_value ) : ?>
				<option value="<?php echo esc_attr( $option_key ); ?>" <?php selected( (string) $option_key, esc_attr( $data['value'] ) ); ?>><?php echo esc_html( $option_value ); ?></option>
			<?php endforeach; ?>
			</select>
			<?php
		}

		/**
		 * Validates a 'product_categories' field.
		 *
		 * @since 3.0.0
		 *
		 * @param string $key   Field key.
		 * @param mixed  $value Posted Value.
		 * @return array An array with the shipping methods.
		 */
		public function validate_product_categories_field( $key, $value ) {
			return $this->validate_array_field( $key, $value );
		}

		/**
		 * Validates a 'product_search' field.
		 *
		 * @since 3.0.0
		 *
		 * @param string $key   Field key.
		 * @param mixed  $value Posted Value.
		 * @return array An array with the product IDs.
		 */
		public function validate_product_search_field( $key, $value ) {
			return $this->validate_array_field( $key, $value );
		}

		/**
		 * Merge the settings of the current catalog with the rest of catalogs before save the option.
		 *
		 * @since 3.0.0
		 *
		 * @param array $settings The sanitized settings.
		 * @return array
		 */
		public function sanitized_fields( $settings ) {
			$product_catalogs = $this->get_product_catalogs();

			$settings = parent::sanitized_fields( $settings );
			$settings = $this->sanitize_slug_field( $settings );
			$settings = $this->sanitize_mpn_fields( $settings );

			if ( $this->is_new() ) {
				$product_catalogs[] = $settings;
			} else {
				$product_catalogs[ $this->catalog_id ] = $settings;
			}

			return $product_catalogs;
		}

		/**
		 * Sanitizes the 'slug' field.
		 *
		 * @since 3.0.0
		 *
		 * @param array $settings The sanitized settings.
		 * @return array
		 */
		protected function sanitize_slug_field( $settings ) {
			// The catalog slug must be unique.
			$string  = ( ( ! empty( $settings['slug'] ) ? $settings['slug'] : $settings['title'] ) );
			$exclude = ( $this->is_new() ? array() : array( $this->catalog_id ) );

			$settings['slug'] = wc_instagram_generate_product_catalog_slug( $string, $exclude );

			return $settings;
		}

		/**
		 * Sanitizes the fields related to the product MPN.
		 *
		 * @since 3.0.0
		 *
		 * @param array $settings The sanitized settings.
		 * @return array
		 */
		protected function sanitize_mpn_fields( $settings ) {
			if ( 'custom' === $settings['product_mpn'] && empty( $settings['custom_mpn'] ) ) {
				$settings['product_mpn'] = 'id';
			}

			return $settings;
		}

		/**
		 * After saving the form.
		 *
		 * @since 3.0.0
		 *
		 * @param bool $saved Was anything saved?.
		 */
		public function after_save( $saved ) {
			if ( $this->is_new() ) {
				$params           = array();
				$product_catalogs = $this->get_product_catalogs();

				if ( ! empty( $product_catalogs ) ) {
					end( $product_catalogs );

					$params = array(
						'catalog_id' => key( $product_catalogs ),
						'action'     => 'created',
					);
				}

				wp_safe_redirect( wc_instagram_get_settings_url( $params ) );
				exit;
			}
		}

		/**
		 * Refreshes the content for the 'google_product_category' field.
		 *
		 * @since 3.3.0
		 * @deprecated 3.4.6
		 */
		public function refresh_google_product_category_field() {
			wc_deprecated_function( __FUNCTION__, '3.4.6', 'WC_Instagram_AJAX::refresh_google_product_category_field()' );

			WC_Instagram_AJAX::refresh_google_product_category_field();
		}
	}
}
