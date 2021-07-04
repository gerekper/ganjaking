<?php

class woocommerce_msrp_admin {

	/**
	 * Add required hooks
	 */
	public function __construct() {

		// Register settings to the WooCommerce settings page
		add_filter( 'woocommerce_general_settings', [ $this, 'settings_array' ] );

		// Enqueue CSS / deal with i18n
		add_action( 'admin_init', [ $this, 'admin_init' ] );

		// Add meta box to the product page.
		add_action( 'woocommerce_product_options_pricing', [ $this, 'product_meta_field' ] );

		// Show the fields in the variation data.
		add_action( 'woocommerce_variation_options_pricing', [ $this, 'variation_show_fields' ], 10, 3 );

		// Save the variation data.
		add_action( 'woocommerce_save_product_variation', [ $this, 'variation_save_fields' ], 10, 2 );

		// Save the main MSRP price information.
		add_action( 'save_post_product', [ $this, 'save_product' ], 10, 1 );
		add_action( 'save_post_product_variation', [ $this, 'save_product' ], 10, 1 );

		// Support composite products extension.
		add_action( 'woocommerce_composite_product_options_pricing', [ $this, 'product_meta_field' ] );

		// Support Product Add-ons extension.
		add_action(
			'woocommerce_product_addons_panel_option_heading',
			[ $this, 'product_addon_option_heading' ],
			10,
			3
		);
		add_action(
			'woocommerce_product_addons_panel_option_row',
			[ $this, 'product_addon_option_row' ],
			10,
			4
		);
		add_action(
			'woocommerce_product_addons_after_adjust_price',
			[ $this, 'product_addon_price_field' ],
			10,
			2
		);
		add_filter( 'woocommerce_product_addons_save_data', [ $this, 'product_addon_save' ], 10, 2 );

		// Support for bulk modifying MSRP price on variations
		add_action(
			'woocommerce_variable_product_bulk_edit_actions',
			[ $this, 'variable_product_bulk_edit_actions' ]
		);
		add_action(
			'woocommerce_bulk_edit_variations',
			[ $this, 'variable_product_bulk_edit_actions_cb' ],
			10,
			4
		);
	}

	/**
	 * Set up the plugin for translation
	 */
	public function admin_init() {
		$this->enqueue_js();
		$this->enqueue_css();
	}

	/**
	 * Add the settings to the WooCommerce settings page
	 */
	public function settings_array( $settings ) {
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
			'desc'     => __( 'Choose when to display MSRP prices.', 'woocommerce_msrp' ),
			'tip'      => '',
			'id'       => 'woocommerce_msrp_status',
			'css'      => '',
			'std'      => 'always',
			'type'     => 'select',
			'options'  => array(
				'always'    => __( 'Always show MSRP', 'woocommerce_msrp' ),
				'different' => __( 'Show if different from your price', 'woocommerce_msrp' ),
				'higher'    => __( 'Show if your price is lower than the MSRP', 'woocommerce_msrp' ),
				'never'     => __( 'Never show MSRP', 'woocommerce_msrp' ),
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
	public function product_meta_field() {
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
	 * @param int     $loop           Position in the loop.
	 * @param array   $variation_data Variation data.
	 * @param WP_Post $variation      Post data.
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function variation_show_fields( $loop, $variation_data, $variation ) {
		$variation_product = wc_get_product( $variation->ID );
		$msrp              = $variation_product->get_meta( '_msrp' );
		$msrp              = ! empty( $msrp ) ? $msrp : '';
		require $this->get_template_path( 'variation_show_fields' );
	}

	/**
	 * Save MSRP values for variable products
	 *
	 * @param int $product_id The parent product ID (Unused)
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function variation_save_fields( $product_id, $idx ) {
		if ( ! isset( $_POST['variable_post_id'] ) ) {
			return;
		}
		$variation_id = (int) $_POST['variable_post_id'][ $idx ];
		$msrp         = $_POST['variable_msrp'][ $idx ];
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
	public function save_product( $product_id ) {
		// Verify if this is an auto save routine.
		// If it is our form has not been submitted, so we dont want to do anything
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! isset( $_POST['_msrp_price'] ) ) {
			return;
		}

		$msrp    = wc_format_decimal( $_POST['_msrp_price'] );
		$product = wc_get_product( $product_id );
		$product->update_meta_data( '_msrp_price', $msrp );
		$product->save();
	}

	/**
	 * Output a heading for the product add-ons table.
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function product_addon_option_heading( $post, $addon, $loop ) {
		require $this->get_template_path( 'product_addon_option_heading_v3' );
	}

	/**
	 * Output the markup for an option in the product add-ons table.
	 */
	public function product_addon_option_row( $post, $addon, $loop, $option ) {
		$msrp = isset( $option['msrp'] ) ? $option['msrp'] : '';
		$this->product_addon_option_row_v3( $post, $addon, $loop, $option, $msrp );
	}

	/**
	 * Output the MSRP field for add-ons which do not have options.
	 *
	 * @param $addon
	 * @param $loop
	 *
	 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
	 */
	public function product_addon_price_field( $addon, $loop ) {
		if ( is_null( $loop ) ) {
			$loop = 0;
		}

		$msrp = isset( $addon['msrp'] ) ? $addon['msrp'] : '';
		require $this->get_template_path( 'product_addon_price_field' );
	}

	/**
	 * Output the MSRP field for add-ons which have options (Product Add-Ons >= v3.0)
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	private function product_addon_option_row_v3( $post, $addon, $loop, $option, $msrp ) {
		require $this->get_template_path( 'product_addon_option_row_v3' );
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
			if ( isset( $_POST['product_addon_msrp'][ $idx ] ) ) {
				$data['msrp'] = $_POST['product_addon_msrp'][ $idx ];
			}
		} else {
			if ( isset( $_POST['product_addon_option_msrp'][ $idx ] ) ) {
				foreach ( $_POST['product_addon_option_msrp'][ $idx ] as $option_idx => $value ) {
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
		require $this->get_template_path( 'variable_product_bulk_edit_actions' );
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
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
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
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	private function bulk_action_clear_prices( $data, $product_id, $variations ) {
		foreach ( $variations as $variation_id ) {
			$variation = wc_get_product( $variation_id );
			$variation->delete_meta_data( '_msrp' );
			$variation->save();
		}
	}

	/**
	 * Load JS when needed.
	 */
	private function enqueue_js() {
		$suffix = ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? '' : '.min';
		wp_enqueue_script(
			'woocommerce_msrp_admin',
			plugins_url( "js/admin{$suffix}.js", __FILE__ ),
			[ 'jquery' ],
			WOOCOMMERCE_MSRP_VERSION,
			true
		);
	}

	/**
	 * Load CSS when needed.
	 */
	private function enqueue_css() {
		if ( defined( 'WC_PRODUCT_ADDONS_VERSION' ) && version_compare( WC_PRODUCT_ADDONS_VERSION, '3.0.0', '>=' ) ) {
			wp_enqueue_style(
				'woocommerce_msrp_admin',
				plugins_url( 'css/admin.css', __FILE__ ),
				[],
				WOOCOMMERCE_MSRP_VERSION
			);
		}
	}

	private function get_template_path( $template ) {
		return dirname( __FILE__ ) . '/templates/' . $template . '.php';
	}
}
