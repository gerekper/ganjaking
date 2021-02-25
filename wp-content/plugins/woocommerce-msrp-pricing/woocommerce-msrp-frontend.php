<?php

class woocommerce_msrp_frontend {

	/**
	 * Script arguments to be added to JS via wp_localize_script()
	 *
	 * @var array
	 */
	private $script_args = array();

	/**
	 * Add hooks for the default services.
	 */
	public function __construct() {

		if ( ! is_admin() ) {
			// Add hooks for JS and CSS
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'wp_footer', array( $this, 'footer' ) );
		}

		// Make sure the information is available to JS.
		add_action( 'woocommerce_available_variation', [ $this, 'add_msrp_to_js' ], 10, 3 );

		// Single Product Page.
		add_action( 'woocommerce_single_product_summary', [ $this, 'show_msrp' ], 7 );

		// Loop
		add_action( 'woocommerce_after_shop_loop_item_title', [ $this, 'show_msrp' ], 9 );

		// Composite products extension.
		add_action( 'woocommerce_composite_before_price', [ $this, 'show_msrp' ], 7 );

		// Grouped product table
		add_action( 'woocommerce_grouped_product_list_before_price', [ $this, 'show_grouped_msrp' ], 9 );

		// REST API support for WooCommerce 3.0+
		add_filter( 'woocommerce_rest_prepare_product_object', [ $this, 'rest_api_price_output_v2_simple' ], 10, 3 );
		add_filter(
			'woocommerce_rest_prepare_product_variation_object',
			[ $this, 'rest_api_price_output_v2_variation' ],
			10,
			3
		);
		add_filter( 'woocommerce_rest_pre_insert_product_object', [ $this, 'rest_api_maybe_update_msrp_v2' ], 10, 3 );
		add_filter(
			'woocommerce_rest_pre_insert_product_variation_object',
			[ $this, 'rest_api_maybe_update_msrp_v2' ],
			10,
			3
		);
		// Note: Schema details are output by 2.6.x filter below.

		// REST API support for WooCommerce 2.6+
		add_filter( 'woocommerce_rest_prepare_product', [ $this, 'rest_api_price_output' ], 10, 2 );
		add_action( 'woocommerce_rest_insert_product', [ $this, 'rest_api_maybe_update_msrp' ], 10, 2 );
		add_action(
			'woocommerce_rest_save_product_variation',
			[ $this, 'rest_api_maybe_update_msrp_variation' ],
			10,
			3
		);
		add_filter( 'woocommerce_rest_product_schema', [ $this, 'rest_api_product_schema' ], 99 );

		// Add support for product add-ons extension (add-ons with options)
		add_filter( 'woocommerce_product_addons_option_price', [ $this, 'product_addons_show_options_msrp' ], 10, 4 );
		// Add support for product add-ons extension (add-ons without options)
		add_filter( 'woocommerce_product_addons_price', [ $this, 'product_addons_show_msrp' ], 10, 4 );
	}

	/**
	 * add_msrp_to_js function.
	 *
	 * @access public
	 *
	 * @param mixed $variation_data
	 * @param mixed $product
	 * @param mixed $variation
	 *
	 * @return void
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function add_msrp_to_js( $variation_data, $product, $variation ) {
		$msrp = $variation->get_meta( '_msrp' );
		if ( empty( $msrp ) ) {
			return $variation_data;
		}
		// Adjust for tax input/display settings.
		$msrp = wc_get_price_to_display( $variation, [ 'price' => $msrp ] );
		// Add the data to the JS data array.
		$variation_data['msrp']           = $msrp;
		$variation_data['msrp_html']      = wc_price( $msrp );
		$variation_data['non_msrp_price'] = wc_get_price_to_display( $variation, [ 'price' => $variation->get_price() ] );
		$variation_data['msrp_saving']    = $this->msrp_saving_html( $variation_data['msrp'], $variation_data['non_msrp_price'] );

		return $variation_data;
	}

	/**
	 * Generate the markup to show the single price / msrp value pair.
	 *
	 * @param float $msrp The MSRP price.
	 * @param float $price The actual price.
	 *
	 * @return string        The saving as markup.
	 */
	private function msrp_saving_html( $msrp, $price ) {
		$type = get_option( 'woocommerce_msrp_show_savings', 'no' );
		if ( 'no' === $type ) {
			return '';
		}
		if ( $price > $msrp ||
			 empty( $price ) ||
			 empty( $msrp ) ||
			 $msrp === $price ) {
			return '';
		}
		if ( 'percentage' === $type ) {
			$percentage = ( $msrp - $price ) / $msrp * 100;
			$saving     = apply_filters(
				'woocommerce_msrp_saving_percentage_html',
				floor( $percentage ),
				$percentage,
				$msrp,
				$price
			);

			return sprintf(
			// translators: %s is the saving amount which may be a financial amount, or a percentage.
				__( 'You save %s', 'woocommerce_msrp' ),
				$saving . '%'
			);
		}
		if ( 'amount' === $type ) {
			$amount = $msrp - $price;
			$saving = apply_filters(
				'woocommerce_msrp_saving_amount_html',
				wc_price( $amount ),
				$amount,
				$msrp,
				$price
			);

			return sprintf(
			// translators: %s is the saving amount which may be a financial amount, or a percentage.
				__( 'You save %s', 'woocommerce_msrp' ),
				$saving
			);
		}

		return '';
	}

	/**
	 * Generate the markup to show the saving range for a variable product.
	 *
	 * @param WC_Product $current_product The variable product.
	 *
	 * @return string                       The saving as markup.
	 */
	private function msrp_saving_html_variable( $current_product ) {
		$type = get_option( 'woocommerce_msrp_show_savings', 'no' );
		if ( 'no' === $type ) {
			return '';
		}
		$savings = $this->get_savings_for_variable_product( $current_product, $type );
		if ( empty( $savings ) ) {
			return '';
		}
		if ( 'percentage' === $type ) {
			if ( $savings[0] === $savings[1] ) {
				return sprintf(
				// translators: %1$d%% is the saving amount which may be a financial amount, or a percentage.
					__( 'You save %1$d%%', 'woocommerce_msrp' ),
					$savings[0]
				);
			} else {
				return sprintf(
				// translators: %1$d%% is the saving amount which may be a financial amount, or a percentage.
					__( 'You save up to %1$d%%', 'woocommerce_msrp' ),
					$savings[1]
				);
			}
		}
		if ( 'amount' === $type ) {
			if ( $savings[0] === $savings[1] ) {
				return sprintf(
				// translators: %s is the saving amount which may be a financial amount, or a percentage.
					__( 'You save %s', 'woocommerce_msrp' ),
					wc_price( $savings[0] )
				);
			} else {
				return sprintf(
				// translators: %1$s is the saving amount which may be a financial amount, or a percentage.
					__( 'You save up to %1$s', 'woocommerce_msrp' ),
					wc_price( $savings[1] )
				);
			}
		}

		return '';
	}

	/**
	 * Enqueue javascript required to show MSRPs on variable products.
	 */
	public function enqueue_scripts() {
		$suffix = ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? '' : '.min';
		wp_register_script(
			'woocommerce_msrp',
			plugins_url( "js/frontend{$suffix}.js", __FILE__ ),
			[ 'jquery' ],
			WOOCOMMERCE_MSRP_VERSION,
			true
		);
		$this->script_args = array(
			'msrp_status'      => get_option( 'woocommerce_msrp_status' ),
			'msrp_description' => get_option( 'woocommerce_msrp_description' ),
		);
	}

	/**
	 * Output our Javascript.
	 */
	public function footer() {
		wp_localize_script( 'woocommerce_msrp', 'woocommerce_msrp', $this->script_args );
		wp_enqueue_script( 'woocommerce_msrp' );
	}


	/**
	 * Enqueue frontend stylesheets
	 */
	public function enqueue_styles() {
		wp_enqueue_style(
			'woocommerce_msrp',
			plugins_url( 'css/frontend.css', __FILE__ ),
			[],
			WOOCOMMERCE_MSRP_VERSION
		);
	}


	/**
	 * Wrapper function to add markup required when showing the MSRP in a
	 * grouped product table list
	 */
	public function show_grouped_msrp( $current_product ) {
		echo '<td class="woocommerce_msrp_price">';
		$this->show_msrp( $current_product );
		echo '</td>';
	}

	/**
	 * Get the MSRP for a non-variable product
	 *
	 * @param object $current_product The product the MSRP is required for
	 *
	 * @return string                  The MSRP, or empty string
	 */
	private function get_msrp_for_single_product( $current_product ) {
		return $current_product->get_meta( '_msrp_price' );
	}

	/**
	 * Get the MSRP for a single variation.
	 *
	 * @param object $current_product The product the MSRP is required for
	 *
	 * @return string                   The MSRP, or empty string
	 */
	private function get_msrp_for_variation( $current_product ) {
		return $current_product->get_meta( '_msrp' );
	}

	/**
	 * Get the MSRP for a variable product. This will be the highest and
	 * lowest MSRP across all variations.
	 *
	 * @param object $current_product The product the MSRP is required for
	 *
	 * @return array                  The MSRP range info, or empty array.
	 */
	private function get_msrp_for_variable_product( $current_product ) {
		$children = $current_product->get_children();
		if ( ! $children ) {
			return $this->get_msrp_for_single_product( $current_product );
		}
		$lowest_msrp  = '';
		$highest_msrp = '';
		foreach ( $children as $child_id ) {
			// FIXME - "CRUD" way of doing this is ....?
			$child_msrp = get_post_meta( $child_id, '_msrp', true );
			if ( false === $child_msrp || '' === $child_msrp ) {
				continue;
			}
			$child_msrp = wc_get_price_to_display( $current_product, [ 'price' => $child_msrp ] );
			if ( empty( $lowest_msrp ) || $child_msrp < $lowest_msrp ) {
				$lowest_msrp = $child_msrp;
			}
			if ( empty( $highest_msrp ) || $child_msrp > $highest_msrp ) {
				$highest_msrp = $child_msrp;
			}
		}
		if ( '' === $lowest_msrp ) {
			return array();
		}

		return array( $lowest_msrp, $highest_msrp );
	}

	/**
	 * Get the higher / lower savings bound for a variable product.
	 *
	 * Compares the actual price and MSRP price for each variation and
	 * returns the highest / lowest savings found.
	 *
	 * @param WC_Product $current_product The variable product.
	 * @param string $type Whether to compare actual
	 *                                       savings, or percentage savings.
	 *
	 * @return array                         Array containing the lowest and
	 *                                       highest saving amounts.
	 */
	private function get_savings_for_variable_product( $current_product, $type = 'amount' ) {
		$children       = $current_product->get_children();
		$lowest_saving  = '';
		$highest_saving = '';
		foreach ( $children as $child_id ) {
			// Grab the child's MSRP price.
			// FIXME - "CRUD" way of doing this is ....?
			$msrp = get_post_meta( $child_id, '_msrp', true );
			// Skip it if it doesn't have one.
			if ( false === $msrp || '' === $msrp ) {
				continue;
			}

			$child_product = wc_get_product( $child_id );
			// Adjust MSRP for input/display preferences
			$msrp = wc_get_price_to_display( $child_product, [ 'price' => $msrp ] );
			// Grab the child's price.
			$selling_price = wc_get_price_to_display(
				$child_product,
				[ 'price' => $child_product->get_price() ]
			);

			// Calculate the saving.
			if ( 'amount' === $type ) {
				$saving = round( $msrp - $selling_price, 2, PHP_ROUND_HALF_UP );
				if ( $saving <= 0 ) {
					continue;
				}
			} else {
				$saving = $msrp - $selling_price;
				if ( $saving <= 0 ) {
					continue;
				}
				$saving = floor( $saving / $msrp * 100 );
			}
			if ( empty( $lowest_saving ) || $saving < $lowest_saving ) {
				$lowest_saving = $saving;
			}
			if ( empty( $highest_saving ) || $saving > $highest_saving ) {
				$highest_saving = $saving;
			}
		}
		if ( '' === $lowest_saving ) {
			return [];
		}

		return [ $lowest_saving, $highest_saving ];
	}

	/**
	 * Show the MSRP on the frontend
	 *
	 * @param WC_Product|null $current_product
	 */
	public function show_msrp( $current_product = null ) {

		global $product;

		if ( ! $current_product ) {
			$current_product = $product;
		}

		$msrp_status = get_option( 'woocommerce_msrp_status' );

		$msrp_description = get_option( 'woocommerce_msrp_description' );
		$product_type     = $current_product->get_type();

		if ( 'variable' === $product_type ) {
			$msrp = $this->get_msrp_for_variable_product( $current_product );
			if ( empty( $msrp ) ) {
				return;
			}
			$current_product_price = $current_product->get_price();

			$show_msrp = false;
			if ( 'always' === $msrp_status ) {
				$show_msrp = true;
			}
			if ( 'different' === $msrp_status &&
				 ( $current_product_price !== $msrp[0] ||
				   $current_product_price !== $msrp[1] )
			) {
				$show_msrp = true;
			}
			if ( 'higher' === $msrp_status &&
				 $current_product_price < $msrp[0]
			) {
				$show_msrp = true;
			}
			if ( apply_filters( 'woocommerce_msrp_hide_variation_price_ranges', false ) ) {
				$show_msrp = false;
			}

			if ( ! apply_filters( 'woocommerce_msrp_show_msrp_pricing', $show_msrp, $current_product_price, $msrp, $current_product ) ) {
				return;
			}

			if ( $msrp[0] === $msrp[1] ) {
				// translators: %1$s is the MSRP price string.
				$price_string                     = sprintf( _x( '%1$s', 'The MSRP price string', 'woocommerce_msrp' ), wc_price( $msrp[0] ) );
				$this->script_args['msrp_ranged'] = false;
			} else {
				// translators: %1$s and %2$s are the lower and higher MSRP prices respectively.
				$template                         = _x(
					'%1$s - %2$s',
					'First arg is lowest MSRP price for variables, second is the highest',
					'woocommerce_msrp'
				);
				$price_string                     = sprintf( $template, wc_price( $msrp[0] ), wc_price( $msrp[1] ) );
				$this->script_args['msrp_ranged'] = true;
			}
			echo '<div class="woocommerce_msrp">';
			echo esc_html( $msrp_description );
			echo ': ';
			echo '<span class="woocommerce_msrp_price">' . $price_string . '</span>';
			echo ' <div class="woocommerce_msrp_saving">' . $this->msrp_saving_html_variable( $current_product ) . '</div>';
			echo '</div>';
		} else {
			$msrp = $this->get_msrp_for_single_product( $current_product );
			if ( empty( $msrp ) ) {
				return;
			}
			// Adjust MSRP for input/display preferences
			$msrp = wc_get_price_to_display( $current_product, [ 'price' => $msrp ] );

			$selling_price = wc_get_price_to_display(
				$current_product,
				[ ' price' => $current_product->get_price() ]
			);

			$show_msrp = false;
			if ( 'always' === $msrp_status ) {
				$show_msrp = true;
			}
			if ( $selling_price !== $msrp && 'different' === $msrp_status ) {
				$show_msrp = true;
			}
			if ( $selling_price < $msrp && 'higher' === $msrp_status ) {
				$show_msrp = true;
			}

			if ( ! apply_filters(
				'woocommerce_msrp_show_msrp_pricing',
				$show_msrp,
				$selling_price,
				[ $msrp, $msrp ],
				$current_product
			) ) {
				return;
			}

			echo '<div class="woocommerce_msrp">';
			echo esc_html( $msrp_description );
			echo ': ';
			echo '<span class="woocommerce_msrp_price">' . wc_price( $msrp ) . '</span>';
			echo ' <div class="woocommerce_msrp_saving">' . $this->msrp_saving_html( $msrp, $selling_price ) . '</div>';
			echo '</div>';
		}
	}

	/**
	 * Include MSRP prices in REST API for products/xx
	 *
	 * REST API v2 - WooCommerce 3.x
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function rest_api_price_output_v2_simple( $response, $product, $request ) {
		// Remove MSRP entries from the meta_data element.
		if ( isset( $response->data['meta_data'] ) ) {
			foreach ( $response->data['meta_data'] as $key => $val ) {
				if ( in_array( $val->key, array( '_msrp', '_msrp_price' ), true ) ) {
					unset( $response->data['meta_data'][ $key ] );
				}
			}
			$response->data['meta_data'] = array_values( $response->data['meta_data'] );
		}

		// Do nothing else if we already have the data.
		if ( isset( $response->data['msrp_price'] ) ) {
			return $response;
		}

		// Add the MSRP price into the response.
		if ( 'variation' === $product->get_type() ) {
			$response->data['msrp_price'] = $this->get_msrp_for_variation( $product );
		} else {
			$response->data['msrp_price'] = $this->get_msrp_for_single_product( $product );
		}

		return $response;
	}

	/**
	 * Include MSRP prices in REST API for products/xx/variation/yy
	 *
	 * REST API v2 - WooCommerce 3.x
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function rest_api_price_output_v2_variation( $response, $product, $request ) {
		// Remove MSRP entries from the meta_data element.
		foreach ( $response->data['meta_data'] as $key => $val ) {
			if ( in_array( $val->key, array( '_msrp', '_msrp_price' ), true ) ) {
				unset( $response->data['meta_data'][ $key ] );
			}
		}
		$response->data['meta_data'] = array_values( $response->data['meta_data'] );

		// Do nothing else if we already have the data.
		if ( isset( $response->data['msrp_price'] ) ) {
			return $response;
		}

		// Add the MSRP price into the response.
		$response->data['msrp_price'] = $this->get_msrp_for_variation( $product );

		return $response;
	}

	/**
	 * Include MSRP Prices in REST API GET responses on products.
	 *
	 * REST API v1 - WooCommerce 2.6.x
	 */
	public function rest_api_price_output( $response, $post ) {
		$product = wc_get_product( $post );
		if ( 'variable' === $product->product_type ) {
			if ( ! count( $response->data['variations'] ) ) {
				$response->data['msrp_price'] = $this->get_msrp_for_single_product( $product );

				return $response;
			}
			foreach ( $response->data['variations'] as $idx => $variation ) {
				if ( isset( $response->data['variations'][ $idx ]['msrp_price'] ) ) {
					continue;
				}
				$product = wc_get_product( $variation['id'] );

				$response->data['variations'][ $idx ]['msrp_price'] = $this->get_msrp_for_single_product( $product );
			}

			return $response;
		} else {
			// Do nothing if we already have the data.
			if ( isset( $response->data['msrp_price'] ) ) {
				return $response;
			}
			if ( 'variation' === $product->product_type ) {
				$response->data['msrp_price'] = $this->get_msrp_for_variation( $product );
			} else {
				$response->data['msrp_price'] = $this->get_msrp_for_single_product( $product );
			}

			return $response;
		}
	}

	/**
	 * Update the MSRP for a product via REST API v2.
	 *
	 * REST API v2 - WooCommerce 3.0.x
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function rest_api_maybe_update_msrp_v2( $product, $request, $creating ) {
		if ( ! isset( $request['msrp_price'] ) ) {
			return $product;
		}
		if ( 'variation' === $product->get_type() ) {
			$key = '_msrp';
		} else {
			$key = '_msrp_price';
		}
		$product->update_meta_data( $key, $request['msrp_price'] );
		$product->save();

		return $product;
	}

	/**
	 * Allow MSRP prices to be updated via REST API.
	 *
	 * REST API v1 - WooCommerce 2.6.x
	 */
	public function rest_api_maybe_update_msrp( $post, $request ) {
		if ( ! isset( $request['msrp_price'] ) ) {
			return;
		}
		$product = wc_get_product( $post );
		if ( 'variation' === $product->product_type ) {
			$key = '_msrp';
		} else {
			$key = '_msrp_price';
		}
		$product = wc_get_product( $post->ID );
		$product->update_meta_data( $key, $request['msrp_price'] );
		$product->save();
	}

	/**
	 * @param $variation_id
	 * @param $menu_order
	 * @param $data
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function rest_api_maybe_update_msrp_variation( $variation_id, $menu_order, $data ) {
		$this->rest_api_maybe_update_msrp( get_post( $variation_id ), $data );
	}

	/**
	 * Include a description of the msrp_price element in the REST schema.
	 *
	 * REST API v1 - WooCommerce 2.6.x
	 * REST API v2 - WooCommerce 3.0.x
	 */
	public function rest_api_product_schema( $schema ) {
		// Add the MSRP price for products.
		$schema['msrp_price'] = array(
			'description' => 'The MSRP price for the product.',
			'type'        => 'string',
			'context'     => array(
				'view',
				'edit',
			),
		);
		// Cater for the bulk product endpoint.
		if ( isset( $schema['variations'] ) &&
			 'object' === $schema['variations']['items']['type'] ) {
			$schema['variations']['items']['properties']['msrp_price'] = array(
				'description' => 'The MSRP price for the variation.',
				'type'        => 'string',
				'context'     => array(
					'view',
					'edit',
				),
			);
		}

		return $schema;
	}

	/**
	 * Render the MSRP for add-ons WITHOUT options.
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function product_addons_show_msrp( $html, $addon, $idx, $addon_type ) {

		// User has chosen not to show MSRP, don't show it
		$msrp_status = get_option( 'woocommerce_msrp_status' );
		if ( empty( $msrp_status ) || 'never' === $msrp_status ) {
			return $html;
		}
		// Get the info we need.
		// translators: %1$s is the MSRP label, %2$s is the MSRP price value.
		$template = '<span class="woocommerce_msrp">%1$s: <span class="woocommerce_msrp_price">%2$s</span></span>';

		$raw_price = isset( $addon['price'] ) ?
			$addon['price'] :
			( isset( $addon['options'][0]['price'] ) ? isset( $addon['options'][0]['price'] ) : null );
		$raw_msrp  = isset( $addon['msrp'] ) ?
			$addon['msrp'] :
			( isset( $addon['options'][0]['msrp'] ) ? isset( $addon['options'][0]['msrp'] ) : null );
		if ( empty( $raw_msrp ) ) {
			return $html;
		}

		$raw_price = WC_Product_Addons_Helper::get_product_addon_price_for_display( $raw_price );
		$raw_msrp  = WC_Product_Addons_Helper::get_product_addon_price_for_display( $raw_msrp );

		$msrp_description = get_option( 'woocommerce_msrp_description' );
		$msrp_price_html  = ! empty( $raw_msrp ) ? wc_price( $raw_msrp ) : '';

		// Return the original markup if the settings preclude showing the MSRP.
		if ( 'different' === $msrp_status && $raw_price === $raw_msrp ) {
			return $html;
		}
		if ( 'higher' === $msrp_status && $raw_price >= $raw_msrp ) {
			return $html;
		}

		// Return the modified markup including MSRP.
		return $html . ' ' . sprintf( $template, $msrp_description, $msrp_price_html );
	}

	/**
	 * Render the MSRP for add-ons WITH options.
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function product_addons_show_options_msrp( $html, $option, $idx, $type ) {
		// User has chosen not to show MSRP, don't show it
		$msrp_status = get_option( 'woocommerce_msrp_status' );
		if ( empty( $msrp_status ) || 'never' === $msrp_status ) {
			return $html;
		}

		// Get the info we need.
		// translators: %1$s is the MSRP label, and %2$s is the MSRP value.
		$template = '<span class="woocommerce_msrp">%1$s: <span class="woocommerce_msrp_price">%2$s</span></span>';
		if ( 'select' === $type ) {
			// translators: %1$s is the MSRP label, and %2$s is the MSRP value.
			$template = __(
				'%1$s: %2$s',
				'woocommerce_msrp'
			);
		}
		$raw_price = isset( $option['price'] ) ? $option['price'] : null;
		$raw_msrp  = isset( $option['msrp'] ) ? $option['msrp'] : null;
		if ( empty( $raw_msrp ) ) {
			return $html;
		}

		$raw_price = WC_Product_Addons_Helper::get_product_addon_price_for_display( $raw_price );
		$raw_msrp  = WC_Product_Addons_Helper::get_product_addon_price_for_display( $raw_msrp );

		$msrp_description = get_option( 'woocommerce_msrp_description' );
		$msrp_price_html  = ! empty( $option['msrp'] ) ? wc_price( $raw_msrp ) : '';

		// Check whether we should show the MSRP, and either return the original markup
		// if not, or the MSRP included markup.
		if ( 'different' === $msrp_status && $raw_price === $raw_msrp ) {
			return $html;
		}
		if ( 'higher' === $msrp_status && $raw_price >= $raw_msrp ) {
			return $html;
		}

		return $html . ' ' . sprintf( $template, $msrp_description, $msrp_price_html );
	}
}

$woocommerce_msrp_frontend = new woocommerce_msrp_frontend();
