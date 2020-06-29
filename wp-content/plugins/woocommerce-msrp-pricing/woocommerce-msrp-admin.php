<?php

if ( ! class_exists( 'woocommerce_msrp_admin' ) ) {
	class woocommerce_msrp_admin {

		/**
		 * Add required hooks
		 */
		function __construct() {

			// Register settings to the WooCommerce settings page
			add_filter( 'woocommerce_general_settings', array( $this, 'settings_array' ) );

			// Enqueue CSS / deal with i18n
			add_action( 'admin_init', array( $this, 'admin_init' ) );

			// Add meta box to the product page.
			add_action( 'woocommerce_product_options_pricing', array( $this, 'product_meta_field' ) );

			// Show the fields in the variation data.
			add_action( 'woocommerce_variation_options_pricing', array( $this, 'variation_show_fields' ), 10, 3 );

			// Save the variation data.
			add_action( 'woocommerce_save_product_variation', array( $this, 'variation_save_fields' ), 10, 2 );

			// Save the main MSRP price information.
			add_action( 'save_post_product', array( $this, 'save_product' ), 10, 1 );
			add_action( 'save_post_product_variation', array( $this, 'save_product' ), 10, 1 );

			// Support composite products extension.
			add_action( 'woocommerce_composite_product_options_pricing', array( $this, 'product_meta_field' ) );

			// Support Product Add-ons extension.
			add_action( 'woocommerce_product_addons_panel_option_heading', array(
				$this,
				'product_addon_option_heading'
			), 10, 3 );
			add_action( 'woocommerce_product_addons_panel_option_row', array(
				$this,
				'product_addon_option_row'
			), 10, 4 );
			add_action( 'woocommerce_product_addons_after_adjust_price', array(
				$this,
				'product_addon_price_field'
			), 10, 2 );
			add_filter( 'woocommerce_product_addons_save_data', array( $this, 'product_addon_save' ), 10, 2 );

			// Support for bulk modifying MSRP price on variations
			add_action( 'woocommerce_variable_product_bulk_edit_actions', array(
				$this,
				'variable_product_bulk_edit_actions'
			) );
			add_action( 'woocommerce_bulk_edit_variations', array(
				$this,
				'variable_product_bulk_edit_actions_cb'
			), 10, 4 );
		}

		/**
		 * Set up the plugin for translation
		 */
		function admin_init() {

			$domain = 'woocommerce_msrp';
			$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

			load_textdomain( $domain, WP_LANG_DIR . '/woocommerce_msrp/' . $domain . '-' . $locale . '.mo' );
			load_plugin_textdomain( 'woocommerce_msrp', null, basename( dirname( __FILE__ ) ) . '/languages' );

			$this->enqueue_js();
			$this->enqueue_css();
		}

		/**
		 * Add the settings to the WooCommerce settings page
		 */
		function settings_array( $settings ) {
			// Heading
			$settings[] = array(
				'title' => __( 'MSRP pricing options', 'woocommerce_msrp' ),
				'type'  => 'title',
				'id'    => 'woocommerce_msrp',
				'desc'  => __( 'Options controlling when, and how to display MSRP pricing', 'woocommerce_msrp' ),
			);
			// Show always / only if different / never
			$settings[] = array(
				'name'     => __( 'Show MSRP Pricing?', 'woocommerce_msrp' ),
				'desc'     => __( 'Choose whether to always display MSRP prices (Always), only display the MSRP if it is different from your price (Only if different), or never display the MSRP price (Never).', 'woocommerce_msrp' ),
				'tip'      => '',
				'id'       => 'woocommerce_msrp_status',
				'css'      => '',
				'std'      => 'always',
				'type'     => 'select',
				'options'  => array(
					'always'    => __( 'Always', 'woocommerce_msrp' ),
					'different' => __( 'Only if different', 'woocommerce_msrp' ),
					'never'     => __( 'Never', 'woocommerce_msrp' ),
				),
				'desc_tip' => true,
			);
			// Description - text field
			$settings[] = array(
				'name'     => __( 'MSRP Labelling', 'woocommerce_msrp' ),
				'desc'     => __( 'MSRP prices will be labelled with this description', 'woocommerce_msrp' ),
				'tip'      => '',
				'id'       => 'woocommerce_msrp_description',
				'css'      => '',
				'std'      => __( 'MSRP', 'woocommerce_msrp' ),
				'type'     => 'text',
				'desc_tip' => true,
			);
			// 'Show savings' options
			$settings[] = array(
				'name'     => __( 'Show savings?', 'woocommerce_msrp' ),
				'desc'     => __( 'Whether to show the saving against MSRP', 'woocommerce_msrp' ),
				'tip'      => '',
				'id'       => 'woocommerce_msrp_show_savings',
				'css'      => '',
				'std'      => 'no',
				'type'     => 'select',
				'desc_tip' => true,
				'options'  => array(
					'no'         => __( 'No', 'woocommerce_msrp' ),
					'amount'     => __( 'As a monetary amount', 'woocommerce_msrp' ),
					'percentage' => __( 'As a percentage', 'woocommerce_msrp' ),
				),
			);
			$settings[] = array(
				'type' => 'sectionend',
				'id'   => 'woocommerce_msrp',
			);

			return $settings;
		}

		/**
		 * Display the meta field for MSRP prices on the product page
		 */
		function product_meta_field() {
			woocommerce_wp_text_input(
				array(
					'id'          => '_msrp_price',
					'class'       => 'wc_input_price short',
					'label'       => __( 'MSRP Price', 'woocommerce_msrp' ) . ' (' . get_woocommerce_currency_symbol() . ')',
					'description' => '',
					'data_type'   => 'price',
				)
			);
		}

		/**
		 * Show the fields for editing the MSRP on the variations panel on the post edit screen
		 *
		 * @param  array $variation_data The variation data for this variation
		 * @param  [type] $loop          Unused
		 */
		function variation_show_fields( $loop, $variation_data, $variation ) {

			$variation_product = wc_get_product( $variation->ID );
			$msrp              = $variation_product->get_meta( '_msrp' );
			$msrp              = ! empty( $msrp ) ? $msrp : '';
			?>
            <p class="form-field form-row form-row-first">
                <label><?php echo __( 'MSRP Price', 'woocommerce_msrp' ) . ' (' . get_woocommerce_currency_symbol() . ')'; ?></label><input
                        type="text" size="5" name="variable_msrp[<?php echo $loop; ?>]"
                        value="<?php echo esc_attr( wc_format_localized_price( $msrp ) ); ?>"/>
            </p>
			<?php

		}

		/**
		 * Save MSRP values for variable products
		 *
		 * @param  int $product_id The parent product ID (Unused)
		 */
		function variation_save_fields( $product_id, $idx ) {
			if ( ! isset( $_POST['variable_post_id'] ) ) { // WPCS: csrf ok.
				return;
			}
			$variation_id = (int) $_POST['variable_post_id'][ $idx ];
			$msrp         = $_POST['variable_msrp'][ $idx ]; // WPCS: csrf ok.
			$msrp         = wc_format_decimal( $msrp );
			$variation    = wc_get_product( $variation_id );
			$variation->update_meta_data( '_msrp', $msrp );
			$variation->save();
		}

		/**
		 * Save the product meta information
		 *
		 * @param int $product_id The product ID
		 */
		function save_product( $product_id ) {
			// Verify if this is an auto save routine.
			// If it is our form has not been submitted, so we dont want to do anything
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}

			if ( ! isset( $_POST['_msrp_price'] ) ) { // WPCS: csrf ok
				return;
			}

			$msrp    = wc_format_decimal( $_POST['_msrp_price'] ); // WPCS: csrf ok.
			$product = wc_get_product( $product_id );
			$product->update_meta_data( '_msrp_price', $msrp );
			$product->save();
		}

		/**
		 * Output a heading for the product add-ons table.
		 */
		public function product_addon_option_heading( $post, $addon, $loop ) {
			if ( defined( 'WC_PRODUCT_ADDONS_VERSION' ) && version_compare( WC_PRODUCT_ADDONS_VERSION, '3.0.0', '>=' ) ) {
				?>
                <div class="msrp-product-addon-column-heading"><?php _e( 'MSRP', 'woocommerce-msrp' ); ?></div><?php
			} else {
				?>
                <th class="msrp-product-addon-column-heading"><?php _e( 'MSRP', 'woocommerce-msrp' ); ?></th><?php
			}
		}

		/**
		 * Output the markup for an option in the product add-ons table.
		 */
		public function product_addon_option_row( $post, $addon, $loop, $option ) {
			$msrp = isset( $option['msrp'] ) ? $option['msrp'] : '';
			if ( defined( 'WC_PRODUCT_ADDONS_VERSION' ) && version_compare( WC_PRODUCT_ADDONS_VERSION, '3.0.0', '>=' ) ) {
				$this->product_addon_option_row_v3( $post, $addon, $loop, $option, $msrp );
			} else {
				$this->product_addon_option_row_v2( $post, $addon, $loop, $option, $msrp );
			}
		}

		/**
		 * Output the MSRP field for add-ons which do not have options.
		 *
		 * @param $addon
		 * @param $loop
		 */
		public function product_addon_price_field( $addon, $loop ) {
			if ( is_null( $loop ) ) {
				$loop = 0;
			}
			$msrp = isset( $addon['msrp'] ) ? $addon['msrp'] : '';
			?>
            <div class="msrp-product-addon-option-msrp-label">
                <label><?php _e( 'MSRP: ', 'woocommerce-msrp' ); ?></label>
            </div>
            <div class="msrp-product-addon-option-msrp-input">
                <input type="number" name="product_addon_msrp[<?php esc_attr_e( $loop ); ?>]"
                       value="<?php esc_attr_e( $msrp ) ?>" placeholder="N/A" min="0" step="any"/>
            </div>
			<?php
		}

		/**
		 * Output the MSRP field for add-ons which have options (Product Add-Ons < v3.0)
		 */
		private function product_addon_option_row_v2( $post, $addon, $loop, $option, $msrp ) {
			?>
            <td class="msrp_product_addon_column">
                <input type="number" name="product_addon_option_msrp[<?php esc_attr_e( $loop ); ?>][]"
                       value="<?php esc_attr_e( $msrp ) ?>" placeholder="N/A" min="0" step="any"/>
            </td>
			<?php
		}

		/**
		 * Output the MSRP field for add-ons which have options (Product Add-Ons >= v3.0)
		 */
		private function product_addon_option_row_v3( $post, $addon, $loop, $option, $msrp ) {
			?>
            <div class="msrp_product_addon_column">
                <input type="number" name="product_addon_option_msrp[<?php esc_attr_e( $loop ); ?>][]"
                       value="<?php esc_attr_e( $msrp ) ?>" placeholder="N/A" min="0" step="any"/>
            </div>
			<?php
		}

		/**
		 * Save the MSRP for product addons if they've been passed in.
		 */
		public function product_addon_save( $data, $idx ) {
			if (
				defined( 'WC_PRODUCT_ADDONS_VERSION' ) &&
				version_compare( WC_PRODUCT_ADDONS_VERSION, '3.0.0', '>=' ) &&
				in_array(
					$data['type'],
					array(
						'custom_text',
						'custom_textarea',
						'file_upload',
						'input_multiplier',
					),
					true
				)
			) {
				if ( isset( $_POST['product_addon_msrp'][ $idx ] ) ) { // WPCS: csrf ok.
					$data['msrp'] = $_POST['product_addon_msrp'][ $idx ]; // WPCS: csrf ok.
				}
			} else {
				if ( isset( $_POST['product_addon_option_msrp'][ $idx ] ) ) { // WPCS: csrf ok.
					foreach ( $_POST['product_addon_option_msrp'][ $idx ] as $option_idx => $value ) { // WPCS: csrf ok.
						$data['options'][ $option_idx ]['msrp'] = $value;
					}
				}
			}

			return $data;
		}

		/**
		 * Render the MSRP bulk-action options in the dropdown.
		 */
		public function variable_product_bulk_edit_actions() {
			?>
            <optgroup label="<?php _e( 'MSRP Prices', 'woocommerce-msrp' ); ?>">
                <option value="msrp_set_prices"><?php _e( 'Set prices', 'woocommerce-msrp' ); ?></option>
                <option value="msrp_clear_prices"><?php _e( 'Clear MSRP prices', 'woocommerce-msrp' ); ?></option>
            </optgroup>
			<?php
		}

		/**
		 * Handler a request to perform a bulk action.
		 *
		 * Calls the relevant function depending on the action being requested.
		 */
		public function variable_product_bulk_edit_actions_cb( $bulk_action, $data, $product_id, $variations ) {
			switch ( $bulk_action ) {
				case 'msrp_set_prices':
					return $this->bulk_action_set_prices( $data, $product_id, $variations );
					break;
				case 'msrp_clear_prices':
					return $this->bulk_action_clear_prices( $data, $product_id, $variations );
					break;
				default:
					return;
					break;
			}
		}

		/**
		 * Update a set of variations with a given MSRP price.
		 */
		private function bulk_action_set_prices( $data, $product_id, $variations ) {
			if ( ! isset( $data['value'] ) ) {
				return;
			}
			foreach ( $variations as $variation_id ) {
				$variation = wc_get_product( $variation_id );
				$variation->update_meta_data( '_msrp', $data['value'] );
				$variation->save();
			}
		}

		/**
		 * Clear the MSRP prices off a set of variations.
		 */
		private function bulk_action_clear_prices( $data, $product_id, $variations ) {
			foreach ( $variations as $variation_id ) {
				$variation = wc_get_product( $variation_id );
				$variation->delete_meta_data( '_msrp' );
				$variation->save();
			}
		}

		/**
		 * From:
		 * https://github.com/skyverge/wc-plugin-compatibility/
		 */
		private function get_wc_version() {
			if ( defined( 'WC_VERSION' ) && WC_VERSION ) {
				return WC_VERSION;
			}
			if ( defined( 'WOOCOMMERCE_VERSION' ) && WOOCOMMERCE_VERSION ) {
				return WOOCOMMERCE_VERSION;
			}

			return null;
		}

		/**
		 * Load JS when needed.
		 */
		private function enqueue_js() {
			$suffix = ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? '' : '.min';
			wp_enqueue_script( 'woocommerce_msrp_admin', plugins_url( "js/admin{$suffix}.js", __FILE__ ), array( 'jquery' ) );
		}

		/**
		 * Load CSS when needed.
		 */
		private function enqueue_css() {
			if ( defined( 'WC_PRODUCT_ADDONS_VERSION' ) && version_compare( WC_PRODUCT_ADDONS_VERSION, '3.0.0', '>=' ) ) {
				wp_enqueue_style( 'woocommerce_msrp_admin', plugins_url( "css/admin.css", __FILE__ ) );
			}
		}
	}
}

$woocommerce_msrp_admin = new woocommerce_msrp_admin();
