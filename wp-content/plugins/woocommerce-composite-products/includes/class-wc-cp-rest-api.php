<?php
/**
 * WC_CP_REST_API class
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Composite Products
 * @since    3.7.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add custom REST API fields.
 *
 * @class    WC_CP_REST_API
 * @version  7.0.6
 */
class WC_CP_REST_API {

	/**
	 * Custom REST API product field names, indicating support for getting/updating.
	 * @var array
	 */
	private static $product_fields = array(
		'composite_layout'                    => array( 'get', 'update' ),
		'composite_add_to_cart_form_location' => array( 'get', 'update' ),
		'composite_editable_in_cart'          => array( 'get', 'update' ),
		'composite_sold_individually_context' => array( 'get', 'update' ),
		'composite_shop_price_calc'           => array( 'get', 'update' ),
		'composite_components'                => array( 'get', 'update' ),
		'composite_scenarios'                 => array( 'get', 'update' )
	);

	/**
	 * Setup order class.
	 */
	public static function init() {

		// Register WP REST API custom product fields.
		add_action( 'rest_api_init', array( __CLASS__, 'register_product_fields' ), 0 );

		// Filter WP REST API order line item fields.
		add_action( 'rest_api_init', array( __CLASS__, 'filter_order_fields' ), 0 );

		// Hooks to add WC v1-v3 API custom order fields.
		self::add_legacy_hooks();
	}

	/**
	 * Filters REST API order responses to add custom data.
	 */
	public static function filter_order_fields() {

		// Modify Composite product responses to return prices with the string data type.
		add_filter( 'woocommerce_rest_prepare_product_object', array( __CLASS__, 'filter_product_response' ), 10, 2 );

		// Schema.
		add_filter( 'woocommerce_rest_shop_order_schema', array( __CLASS__, 'filter_order_schema' ) );
		add_filter( 'woocommerce_rest_shop_subscription_schema', array( __CLASS__, 'filter_order_schema' ) );

		// Modify order responses to include extra line item parent/child relationships and data.
		// v1.
		add_filter( 'woocommerce_rest_prepare_shop_order', array( __CLASS__, 'filter_order_response' ), 10, 3 );
		add_filter( 'woocommerce_rest_prepare_shop_subscription', array( __CLASS__, 'filter_order_response' ), 10, 3 );
		// v2.
		add_filter( 'woocommerce_rest_prepare_shop_order_object', array( __CLASS__, 'filter_order_response' ), 10, 3 );

		// Add composite configuration data as meta for later post-processing.
		add_action( 'woocommerce_rest_set_order_item', array( __CLASS__, 'set_order_item' ), 10, 2 );

		// Make it possible to add entire Composites to orders via the REST API.
		// v1.
		add_filter( 'woocommerce_rest_pre_insert_shop_order', array( __CLASS__, 'add_composite_to_order' ), 10, 2 );
		add_filter( 'woocommerce_rest_pre_insert_shop_subscription', array( __CLASS__, 'add_composite_to_order' ), 10, 2 );
		// v2.
		add_filter( 'woocommerce_rest_pre_insert_shop_order_object', array( __CLASS__, 'add_composite_to_order' ), 10, 2 );
	}

	/*
	|--------------------------------------------------------------------------
	| Products.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Filters WC REST API product responses to cast prices to string, as per the schema.
	 *
	 * @since  6.1.1
	 *
	 * @param  WP_REST_Response   $response
	 * @param  WC_Data            $product
	 * @return WP_REST_Response
	 */
	public static function filter_product_response( $response, $product ) {

		if ( $product->is_type( 'composite' ) ) {

			$data = $response->get_data();

			$data[ 'price' ]         = strval( $data[ 'price' ] );
			$data[ 'regular_price' ] = strval( $data[ 'regular_price' ] );
			$data[ 'sale_price' ]    = strval( $data[ 'sale_price' ] );

			$response->set_data( $data );
		}

		return $response;
	}

	/**
	 * Register custom REST API fields for product requests.
	 */
	public static function register_product_fields() {

		/**
		 * 'woocommerce_rest_api_extended_composite_fields' filter.
		 *
		 * @since  3.15.2
		 *
		 * @param  array  $product_fields
		 */
		$product_fields = apply_filters( 'woocommerce_rest_api_extended_composite_fields', self::$product_fields );

		foreach ( $product_fields as $field_name => $field_supports ) {

			$args = array(
				'schema' => self::get_product_field_schema( $field_name )
			);

			if ( in_array( 'get', $field_supports ) ) {
				$args[ 'get_callback' ] = array( __CLASS__, 'get_product_field_value' );
			}
			if ( in_array( 'update', $field_supports ) ) {
				$args[ 'update_callback' ] = array( __CLASS__, 'update_product_field_value' );
			}

			/**
			 * Extended composite product field args filter.
			 * Use it to handle additional extended composite product fields.
			 *
			 * @since  3.15.2
			 *
			 * @param  array  $args
			 */
			$args = apply_filters( 'woocommerce_rest_api_extended_' . $field_name . '_field_args', $args );

			register_rest_field( 'product', $field_name, $args );
		}
	}

	/**
	 * Gets extended (unprefixed) schema properties for products.
	 *
	 * @return array
	 */
	private static function get_extended_product_schema() {

		return array(
			'composite_layout'                    => array(
				'description' => __( 'Single-product template layout. Applicable to composite-type products.', 'woocommerce-composite-products' ),
				'type'        => 'string',
				'context'     => array( 'view', 'edit' ),
				'enum'        => array_keys( WC_Product_Composite::get_layout_options() )
			),
			'composite_add_to_cart_form_location' => array(
				'description' => __( 'Controls the form location of the product in the single-product page. Applicable to composite-type products.', 'woocommerce-composite-products' ),
				'type'        => 'string',
				'context'     => array( 'view', 'edit' ),
				'enum'        => array_keys( WC_Product_Composite::get_add_to_cart_form_location_options() )
			),
			'composite_editable_in_cart'          => array(
				'description' => __( 'Controls whether the configuration of this product can be modified from the cart page. Applicable to composite-type products.', 'woocommerce-composite-products' ),
				'type'        => 'boolean',
				'context'     => array( 'view', 'edit' )
			),
			'composite_sold_individually_context' => array(
				'description' => __( 'Sold Individually option context. Applicable to composite-type products.', 'woocommerce-composite-products' ),
				'type'        => 'string',
				'context'     => array( 'view', 'edit' ),
				'enum'        => array( 'product', 'configuration' )
			),
			'composite_shop_price_calc'           => array(
				'description' => __( 'Composite catalog price calculation. Applicable to composite-type products.', 'woocommerce-composite-products' ),
				'type'        => 'string',
				'context'     => array( 'view', 'edit' ),
				'enum'        => array_keys( WC_Product_Composite::get_shop_price_calc_options() )
			),
			'composite_components'                => array(
				'description' => __( 'List of components that this product consists of. Applicable to composite-type products.', 'woocommerce-composite-products' ),
				'type'        => 'array',
				'context'     => array( 'view', 'edit' ),
				'items'       => array(
					'type'       => 'object',
					'properties' => array(
						'id'                    => array(
							'description' => __( 'Component ID.', 'woocommerce-composite-products' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' ),
							'readonly'    => true
						),
						'title'                 => array(
							'description' => __( 'Title of the component.', 'woocommerce-composite-products' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' )
						),
						'description'           => array(
							'description' => __( 'Description of the component.', 'woocommerce-composite-products' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' )
						),
						'query_type'            => array(
							'description' => __( 'Query type associated with component options.', 'woocommerce-composite-products' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' ),
							'enum'        => array( 'product_ids', 'category_ids' )
						),
						'query_ids'             => array(
							'description' => __( 'Product IDs or category IDs to use for populating component options.', 'woocommerce-composite-products' ),
							'type'        => 'array',
							'items'       => array(
								'type'       => 'integer'
							),
							'context'     => array( 'view', 'edit' )
						),
						'default_option_id'     => array(
							'description' => __( 'The product ID of the default/pre-selected component option.', 'woocommerce-composite-products' ),
							'type'        => 'integer',
							'context'     => array( 'view', 'edit' )
						),
						'thumbnail_id'          => array(
							'description' => __( 'Attachment ID of the thumbnail associated with this Component.', 'woocommerce-composite-products' ),
							'type'        => 'integer',
							'context'     => array( 'view', 'edit' )
						),
						'thumbnail_src'         => array(
							'description' => __( 'URL of the thumbnail associated with this Component.', 'woocommerce-composite-products' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' )
						),
						'quantity_min'          => array(
							'description' => __( 'Minimum component quantity.', 'woocommerce-composite-products' ),
							'type'        => 'integer',
							'context'     => array( 'view', 'edit' )
						),
						'quantity_max'          => array(
							'description' => __( 'Maximum component quantity.', 'woocommerce-composite-products' ),
							'type'        => WC_CP_Core_Compatibility::is_wp_version_gte( '5.5' ) ? array( 'integer', 'string' ) : '',
							'context'     => array( 'view', 'edit' )
						),
						'priced_individually'   => array(
							'description' => __( 'Indicates whether the price of this component is added to the base price of the composite.', 'woocommerce-composite-products' ),
							'type'        => 'boolean',
							'context'     => array( 'view', 'edit' )
						),
						'shipped_individually'  => array(
							'description' => __( 'Indicates whether this component is shipped separately from the composite.', 'woocommerce-composite-products' ),
							'type'        => 'boolean',
							'context'     => array( 'view', 'edit' )
						),
						'optional'              => array(
							'description' => __( 'Indicates whether the component is optional.', 'woocommerce-composite-products' ),
							'type'        => 'boolean',
							'context'     => array( 'view', 'edit' )
						),
						'discount'              => array(
							'description' => __( 'Discount applied to the component, applicable when Priced Individually is enabled.', 'woocommerce-composite-products' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' )
						),
						'options_style'         => array(
							'description' => __( 'Indicates which template to use for displaying component options.', 'woocommerce-composite-products' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' ),
							'enum'        => wp_list_pluck( WC_CP_Component::get_options_styles(), 'id' )
						),
						'pagination_style'      => array(
							'description' => __( 'Controls how new Thumbnails are loaded into the Component Options view. Applicable when the Options Style of this Component is set to Thumbnails.', 'woocommerce-composite-products' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' ),
							'enum'        => wp_list_pluck( WC_CP_Component::get_pagination_style_options(), 'id' )
						),
						'display_prices'        => array(
							'description' => __( 'Controls how Component Option prices are displayed. Applicable when Priced Individually is enabled for this Component.', 'woocommerce-composite-products' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' ),
							'enum'        => wp_list_pluck( WC_CP_Component::get_price_display_options(), 'id' )
						),
						'show_sorting_options'  => array(
							'description' => __( 'Whether to display sorting options in this Component.', 'woocommerce-composite-products' ),
							'type'        => 'boolean',
							'context'     => array( 'view', 'edit' )
						),
						'show_filtering_options'  => array(
							'description' => __( 'Whether to display filtering options in this Component.', 'woocommerce-composite-products' ),
							'type'        => 'boolean',
							'context'     => array( 'view', 'edit' )
						),
						'attribute_filter_ids'  => array(
							'description' => __( 'Attribute IDs to use for creating Component Option filters.', 'woocommerce-composite-products' ),
							'type'        => 'array',
							'items'       => array(
								'type'       => 'integer'
							),
							'context'     => array( 'view', 'edit' )
						),
						'product_title_visible' => array(
							'description' => __( 'Controls the visibility of product titles in the Component Selection view.', 'woocommerce-composite-products' ),
							'type'        => 'boolean',
							'context'     => array( 'view', 'edit' )
						),
						'product_descr_visible' => array(
							'description' => __( 'Controls the visibility of product short descriptions in the Component Selection view.', 'woocommerce-composite-products' ),
							'type'        => 'boolean',
							'context'     => array( 'view', 'edit' )
						),
						'product_price_visible' => array(
							'description' => __( 'Controls the visibility of product prices in the Component Selection view.', 'woocommerce-composite-products' ),
							'type'        => 'boolean',
							'context'     => array( 'view', 'edit' )
						),
						'product_thumb_visible' => array(
							'description' => __( 'Controls the visibility of product thumbnails in the Component Selection view.', 'woocommerce-composite-products' ),
							'type'        => 'boolean',
							'context'     => array( 'view', 'edit' )
						),
						'subtotal_visible_product' => array(
							'description' => __( 'Controls the visibility of the subtotal associated with this Component in the single-product page.', 'woocommerce-composite-products' ),
							'type'        => 'boolean',
							'context'     => array( 'view', 'edit' )
						),
						'subtotal_visible_cart'    => array(
							'description' => __( 'Controls the visibility of the subtotal associated with this Component in the cart page.', 'woocommerce-composite-products' ),
							'type'        => 'boolean',
							'context'     => array( 'view', 'edit' )
						),
						'subtotal_visible_orders'  => array(
							'description' => __( 'Controls the visibility of the subtotal associated with this Component in order-related pages and e-mail notifications.', 'woocommerce-composite-products' ),
							'type'        => 'boolean',
							'context'     => array( 'view', 'edit' )
						)
					)
				)
			),
			'composite_scenarios'                 => array(
				'description' => __( 'Scenarios data. Applicable to composite-type products.', 'woocommerce-composite-products' ),
				'type'        => 'array',
				'context'     => array( 'view', 'edit' ),
				'items'       => array(
					'type'       => 'object',
					'properties' => array(
						'id'            => array(
							'description' => __( 'Scenario ID.', 'woocommerce-composite-products' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' ),
							'readonly'    => true
						),
						'name'          => array(
							'description' => __( 'Name of the scenario.', 'woocommerce-composite-products' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' )
						),
						'description'   => array(
							'description' => __( 'Optional short description of the scenario.', 'woocommerce-composite-products' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit' )
						),
						'configuration' => array(
							'description' => __( 'Scenario matching conditions.', 'woocommerce-composite-products' ),
							'type'        => 'array',
							'context'     => array( 'view', 'edit' ),
							'items'       => array(
								'type'       => 'object',
								'properties'  => array(
									'component_id'      => array(
										'description' => __( 'Component ID.', 'woocommerce-composite-products' ),
										'type'        => 'string',
										'context'     => array( 'view', 'edit' )
									),
									'component_options' => array(
										'description' => __( 'Product/variation IDs in component targeted by the scenario (0 = any product or variation, -1 = no selection)', 'woocommerce-composite-products' ),
										'type'        => 'array',
										'items'       => array(
											'type'       => 'integer'
										),
										'context'     => array( 'view', 'edit' )
									),
									'options_modifier'  => array(
										'description' => __( 'Comparison modifier for the referenced product/variation IDs.', 'woocommerce-composite-products' ),
										'type'        => 'string',
										'context'     => array( 'view', 'edit' ),
										'enum'        => array( 'in', 'not-in', 'masked' )
									)
								)
							)
						),
						'actions' => array(
							'description' => __( 'Scenario actions.', 'woocommerce-composite-products' ),
							'type'        => 'array',
							'context'     => array( 'view', 'edit' ),
							'items'       => array(
								'type'       => 'object',
								'properties' => array(
									'action_id'   => array(
										'description' => __( 'Scenario action ID (by default \'compat_group\' or \'conditional_components\').', 'woocommerce-composite-products' ),
										'type'        => 'string',
										'enum'        => array( 'compat_group', 'conditional_components' ),
										'context'     => array( 'view', 'edit' )
									),
									'is_active'   => array(
										'description' => __( 'Indicates whether the scenario action is active.', 'woocommerce-composite-products' ),
										'type'        => 'boolean',
										'context'     => array( 'view', 'edit' )
									),
									'action_data' => array(
										'description' => __( 'Scenario action data.', 'woocommerce-composite-products' ),
										'type'        => 'object',
										'context'     => array( 'view', 'edit' )
									)
								)
							)
						),
						'enabled' => array(
							'description' => __( 'Controls whether the Scenario will be enabled or disabled.', 'woocommerce-composite-products' ),
							'type'        => 'boolean',
							'context'     => array( 'view', 'edit' )
						)
					)
				)
			)
		);
	}

	/**
	 * Gets schema properties for CP product fields.
	 *
	 * @param  string  $field_name
	 * @return array
	 */
	public static function get_product_field_schema( $field_name ) {

		$extended_schema = self::get_extended_product_schema();
		$field_schema    = isset( $extended_schema[ $field_name ] ) ? $extended_schema[ $field_name ] : null;

		return $field_schema;
	}

	/**
	 * Gets values for CP product fields.
	 *
	 * @param  array            $response
	 * @param  string           $field_name
	 * @param  WP_REST_Request  $request
	 * @return array
	 */
	public static function get_product_field_value( $response, $field_name, $request ) {

		$data = null;

		if ( isset( $response[ 'id' ] ) ) {
			$product = wc_get_product( $response[ 'id' ] );
			$data    = self::get_product_field( $field_name, $product );
		}

		return $data;
	}

	/**
	 * Updates values for CP product fields.
	 *
	 * @param  mixed   $value
	 * @param  mixed   $response
	 * @param  string  $field_name
	 * @return array
	 */
	public static function update_product_field_value( $field_value, $response, $field_name ) {

		$product_id = false;

		if ( $response instanceof WP_Post ) {
			$product_id   = absint( $response->ID );
			$product      = wc_get_product( $product_id );
			$product_type = $product->get_type();
		} elseif ( $response instanceof WC_Product ) {
			$product      = $response;
			$product_id   = $response->get_id();
			$product_type = $response->get_type();
		}

		// Only possible to set fields of 'composite' type products.
		if ( $product_id && 'composite' === $product_type ) {
			switch ( $field_name ) {

				case 'composite_layout' :

					$product->set_layout( wc_clean( $field_value ) );
					$product->save();

				break;

				case 'composite_add_to_cart_form_location' :

					$product->set_add_to_cart_form_location( wc_clean( $field_value ) );
					$product->save();

				break;

				case 'composite_editable_in_cart' :

					$product->set_editable_in_cart( wc_string_to_bool( $field_value ) );
					$product->save();

				break;

				case 'composite_sold_individually_context' :

					$product->set_sold_individually_context( wc_clean( $field_value ) );
					$product->save();

				break;

				case 'composite_shop_price_calc' :

					$product->set_shop_price_calc( wc_clean( $field_value ) );
					$product->save();

				break;

				case 'composite_components' :

					$timestamp = current_time( 'timestamp' );
					$loop      = 0;

					$new     = array();
					$updated = array();
					$deleted = array();

					if ( ! empty( $field_value ) && is_array( $field_value ) ) {
						foreach ( $field_value as $data ) {

							$action = empty( $data[ 'id' ] ) ? 'create' : '';
							$delete = isset( $data[ 'delete' ] ) && true === $data[ 'delete' ];

							// Creating component.
							if ( 'create' === $action ) {

								$component_id = strval( $timestamp + $loop );
								$loop++;

								// Sanitize source data.
								$data = self::sanitize_rest_api_component_data( $data );

								// Upload/set thumbnail.
								if ( ! empty( $data[ 'thumbnail_id' ] ) || ! empty( $data[ 'thumbnail_src' ] ) ) {
									$data[ 'thumbnail_id' ] = WC_CP_Component::set_thumbnail( ! empty( $data[ 'thumbnail_id' ] ) ? $data[ 'thumbnail_id' ] : '', ! empty( $data[ 'thumbnail_src' ] ) ? $data[ 'thumbnail_src' ] : '', $product );
								}

								// Convert to internal format.
								$component_data = self::convert_rest_api_component_data( $data );

								// Validate.
								$component_data = self::validate_internal_component_data( $component_data );

								// Add component to 'new' array.
								$new[ $component_id ] = $component_data;

							// Updating or deleting component.
							} else {

								$component_id = strval( $data[ 'id' ] );
								$action       = 'update';

								if ( ! $product->has_component( $component_id ) ) {
									throw new WC_REST_Exception( 'woocommerce_rest_invalid_component_id', sprintf( __( 'Component ID #%s does not exist.', 'woocommerce-composite-products' ), $component_id ), 400 );
								}

								// Deleting component.
								if ( $delete ) {
									// Add component to 'deleted' array.
									$deleted[] = $component_id;
									continue;
								}

								// Sanitize source data.
								$updated_data = self::sanitize_rest_api_component_data( $data );

								// Upload/set thumbnail.
								if ( ! empty( $updated_data[ 'thumbnail_id' ] ) || ! empty( $updated_data[ 'thumbnail_src' ] ) ) {
									$updated_data[ 'thumbnail_id' ] = WC_CP_Component::set_thumbnail( ! empty( $updated_data[ 'thumbnail_id' ] ) ? $updated_data[ 'thumbnail_id' ] : '', ! empty( $updated_data[ 'thumbnail_src' ] ) ? $updated_data[ 'thumbnail_src' ] : '', $product );
								}

								// Updating component.
								$components_data = $product->get_composite_data( 'rest' );

								// Merge into component data.
								$component_data = array_merge( $components_data[ $component_id ], $updated_data );

								// Convert to internal format.
								$component_data = self::convert_rest_api_component_data( $component_data );

								// Validate.
								$component_data = self::validate_internal_component_data( $component_data );

								// Add component to 'updated' array.
								$updated[ $component_id ] = $component_data;
							}
						}

						$components     = $product->get_components();
						$composite_data = array();

						if ( ! empty( $components ) ) {
							foreach ( $components as $component ) {

								$component_id = $component->get_id();

								// Omit component data if component deleted.
								if ( in_array( $component_id, $deleted ) ) {
									continue;
								// Add modified component data if component updated.
								} elseif ( isset( $updated[ $component_id ] ) ) {
									$composite_data[ $component_id ] = $updated[ $component_id ];
								// Preserve component unless updated/deleted.
								} else {
									$composite_data[ $component_id ] = array_diff_key( $component->get_data(), array( 'composite_id' => 1, 'component_id' => 1 ) );
								}
							}
						}

						// Add new components.
						$composite_data = $composite_data + $new;
					}

					$product->set_composite_data( $composite_data );
					$product->save();

				break;

				case 'composite_scenarios' :

					$timestamp = current_time( 'timestamp' );
					$loop      = 0;

					$new     = array();
					$updated = array();
					$deleted = array();

					$scenarios_rest_api_data = self::get_rest_api_scenario_data( $product );

					if ( ! empty( $field_value ) && is_array( $field_value ) ) {
						foreach ( $field_value as $data ) {

							$action = empty( $data[ 'id' ] ) ? 'create' : '';
							$delete = isset( $data[ 'delete' ] ) && true === $data[ 'delete' ];

							// Creating scenario.
							if ( 'create' === $action ) {

								$scenario_id = strval( $timestamp + $loop );
								$loop++;

								// Validate.
								$scenario_data = self::validate_rest_api_scenario_data( self::sanitize_rest_api_scenario_data( $data ) );

								// Add scenario to 'new' array.
								$new[ $scenario_id ] = $scenario_data;

							// Updating or deleting scenario.
							} else {

								$scenario_id = strval( $data[ 'id' ] );
								$action      = 'update';

								if ( ! isset( $scenarios_rest_api_data[ $scenario_id ] ) ) {
									throw new WC_REST_Exception( 'woocommerce_rest_invalid_scenario_id', sprintf( __( 'Scenario ID #%s does not exist.', 'woocommerce-composite-products' ), $scenario_id ), 400 );
								}

								// Deleting scenario.
								if ( $delete ) {
									// Add scenario to 'deleted' array.
									$deleted[] = $scenario_id;
									continue;
								}

								$scenario_data = $scenarios_rest_api_data[ $scenario_id ];

								// Clean up source data.
								$data = array_diff_key( $data, array( 'id' => 1, 'delete' => 1 ) );

								// Merge into scenario data.
								$scenario_data = array_merge( $scenario_data, $data );

								// Validate.
								$scenario_data = self::validate_rest_api_scenario_data( self::sanitize_rest_api_scenario_data( $scenario_data ) );

								// Add scenario to 'updated' array.
								$updated[ $scenario_id ] = $scenario_data;
							}
						}

						$scenarios_rest_api_data_array = array();

						if ( ! empty( $scenarios_rest_api_data ) ) {
							foreach ( $scenarios_rest_api_data as $scenario_id => $scenario_rest_api_data ) {

								// Omit scenario data if scenario deleted.
								if ( in_array( $scenario_id, $deleted ) ) {
									continue;
								// Add modified scenario data if scenario updated.
								} elseif ( isset( $updated[ $scenario_id ] ) ) {
									$scenarios_rest_api_data_array[ $scenario_id ] = $updated[ $scenario_id ];
								// Preserve scenario unless updated/deleted.
								} else {
									$scenarios_rest_api_data_array[ $scenario_id ] = $scenario_rest_api_data;
								}
							}
						}

						// Add new scenarios.
						$scenarios_rest_api_data_array = $scenarios_rest_api_data_array + $new;
						$scenarios_internal_data_array = array();

						/*
						 * Convert REST API schema to internal schema.
						 */

						foreach ( $scenarios_rest_api_data_array as $scenario_id => $scenario_rest_api_data ) {
							$scenarios_internal_data_array[ $scenario_id ] = self::convert_rest_api_scenario_data( $scenario_rest_api_data );
						}
					}

					$product->set_scenario_data( $scenarios_internal_data_array );
					$product->save();

				break;
			}
		}

		return true;
	}

	/**
	 * Gets composite-specific product data.
	 *
	 * @since  3.7.0
	 *
	 * @param  string      $key
	 * @param  WC_Product  $product
	 * @return array
	 */
	private static function get_product_field( $key, $product ) {

		$product_type = $product->get_type();
		$product_id   = $product->get_id();

		switch ( $key ) {

			case 'composite_layout' :

				$value = '';

				if ( 'composite' === $product_type ) {
					$value = $product->get_layout( 'edit' );
				}

			break;

			case 'composite_add_to_cart_form_location' :

				$value = '';

				if ( 'composite' === $product_type ) {
					$value = $product->get_add_to_cart_form_location( 'edit' );
				}

			break;

			case 'composite_editable_in_cart' :

				$value = false;

				if ( 'composite' === $product_type ) {
					$value = $product->get_editable_in_cart( 'edit' );
				}

			break;

			case 'composite_sold_individually_context' :

				$value = '';

				if ( 'composite' === $product_type ) {
					$value = $product->get_sold_individually_context( 'edit' );
				}

			break;

			case 'composite_shop_price_calc' :

				$value = '';

				if ( 'composite' === $product_type ) {
					$value = $product->get_shop_price_calc( 'edit' );
				}

			break;

			case 'composite_components' :

				$value = array();

				if ( 'composite' === $product_type ) {
					$value = array_values( self::get_rest_api_component_data( $product ) );
				}

			break;

			case 'composite_scenarios' :

				$value = array();

				if ( 'composite' === $product_type ) {
					$value = array_values( self::get_rest_api_scenario_data( $product ) );
				}

			break;
		}

		return $value;
	}

	/**
	 * Sanitizes component data supplied using the api schema.
	 *
	 * @param  array  $component_data
	 * @return array
	 */
	public static function sanitize_rest_api_component_data( $component_data ) {

		$sanitized_data = array();

		if ( ! empty( $component_data ) ) {
			foreach ( $component_data as $key => $value ) {

				// String.
				if ( in_array( $key, array( 'id', 'title', 'description', 'query_type', 'options_style', 'thumbnail_src', 'pagination_style', 'display_prices' ) ) ) {
					if ( 'title' === $key ) {
						$sanitized_data[ $key ] = strip_tags( strval( $value ) );
					} elseif ( 'description' ===  $key ) {
						$sanitized_data[ $key ] = wp_kses_post( $value );
					} else {
						$sanitized_data[ $key ] = strval( $value );
					}
				// Boolean.
				} elseif ( in_array( $key, array( 'optional', 'priced_individually', 'shipped_individually', 'show_sorting_options', 'show_filtering_options', 'product_title_visible', 'product_descr_visible', 'product_price_visible', 'product_thumb_visible', 'subtotal_visible_product', 'subtotal_visible_cart', 'subtotal_visible_orders' ) ) ) {
					$sanitized_data[ $key ] = wc_string_to_bool( $value );
				// Integer or empty.
				} elseif ( in_array( $key, array( 'default_option_id', 'thumbnail_id', 'quantity_max' ) ) ) {
					$sanitized_data[ $key ] = '' !== $value ? absint( $value ) : '';
				// Integer.
				} elseif ( 'quantity_min' === $key ) {
					$sanitized_data[ $key ] = absint( $value );
				// Array of integers.
				} elseif ( in_array( $key, array( 'query_ids', 'attribute_filter_ids' ) ) ) {
					$sanitized_data[ $key ] = array_map( 'absint', $value );
				// Decimal, 0-100.
				} elseif ( 'discount' === $key ) {
					$value = wc_format_decimal( $value );
					$value = $value <= 0 ? '' : $value;
					$value = $value > 100.0 ? '' : $value;
					$sanitized_data[ $key ] = $value;
				// Everything else.
				} else {
					$sanitized_data[ $key ] = $value;
				}
			}
		}

		return $sanitized_data;
	}

	/**
	 * Converts component data supplied using the api schema to the internal schema.
	 *
	 * @param  array  $data
	 * @return array
	 */
	public static function convert_rest_api_component_data( $data ) {

		$internal_data = array();

		if ( ! empty( $data ) ) {
			foreach ( $data as $key => $value ) {

				$query_type = isset( $data[ 'query_type' ] ) ? $data[ 'query_type' ] : 'product_ids';

				$product_title_visible = isset( $data[ 'product_title_visible' ] ) && false === $data[ 'product_title_visible' ] ? false : true;
				$product_descr_visible = isset( $data[ 'product_descr_visible' ] ) && false === $data[ 'product_descr_visible' ] ? false : true;
				$product_price_visible = isset( $data[ 'product_price_visible' ] ) && false === $data[ 'product_price_visible' ] ? false : true;
				$product_thumb_visible = isset( $data[ 'product_thumb_visible' ] ) && false === $data[ 'product_thumb_visible' ] ? false : true;

				$subtotal_visible_product = isset( $data[ 'subtotal_visible_product' ] ) && false === $data[ 'subtotal_visible_product' ] ? false : true;
				$subtotal_visible_cart    = isset( $data[ 'subtotal_visible_cart' ] ) && false === $data[ 'subtotal_visible_cart' ] ? false : true;
				$subtotal_visible_orders  = isset( $data[ 'subtotal_visible_orders' ] ) && false === $data[ 'subtotal_visible_orders' ] ? false : true;


				$internal_data = array(
					'query_type'               => $query_type,

					'assigned_ids'             => 'product_ids' === $query_type && isset( $data[ 'query_ids' ] ) ? $data[ 'query_ids' ] : array(),
					'assigned_category_ids'    => 'category_ids' === $query_type && isset( $data[ 'query_ids' ] ) ? $data[ 'query_ids' ] : array(),

					'default_id'               => isset( $data[ 'default_option_id' ] ) ? $data[ 'default_option_id' ] : '',
					'priced_individually'      => isset( $data[ 'priced_individually' ] ) ? wc_bool_to_string( $data[ 'priced_individually' ] ) : 'no',
					'shipped_individually'     => isset( $data[ 'shipped_individually' ] ) ? wc_bool_to_string( $data[ 'shipped_individually' ] ) : 'no',
					'optional'                 => isset( $data[ 'optional' ] ) ? wc_bool_to_string( $data[ 'optional' ] ) : 'no',
					'selection_mode'           => isset( $data[ 'options_style' ] ) ? strval( $data[ 'options_style' ] ) : 'dropdowns',

					'hide_product_title'       => $product_title_visible ? 'no' : 'yes',
					'hide_product_description' => $product_descr_visible ? 'no' : 'yes',
					'hide_product_price'       => $product_price_visible ? 'no' : 'yes',
					'hide_product_thumbnail'   => $product_thumb_visible ? 'no' : 'yes',

					'hide_subtotal_product'    => $subtotal_visible_product ? 'no' : 'yes',
					'hide_subtotal_cart'       => $subtotal_visible_cart ? 'no' : 'yes',
					'hide_subtotal_orders'     => $subtotal_visible_orders ? 'no' : 'yes',

					'show_orderby'             => isset( $data[ 'show_sorting_options' ] ) ? wc_bool_to_string( $data[ 'show_sorting_options' ] ) : 'no',
					'show_filters'             => isset( $data[ 'show_filtering_options' ] ) ? wc_bool_to_string( $data[ 'show_filtering_options' ] ) : 'no',

					'attribute_filters'        => isset( $data[ 'attribute_filter_ids' ] ) && is_array( $data[ 'attribute_filter_ids' ] ) ? $data[ 'attribute_filter_ids' ] : array()
				);
			}

			// Clean up source data.
			$data = array_diff_key( $data, array(
				'id'                       => 1,
				'query_ids'                => 1,
				'default_option_id'        => 1,
				'options_style'            => 1,
				'show_sorting_options'     => 1,
				'show_filtering_options'   => 1,
				'attribute_filter_ids'     => 1,
				'product_title_visible'    => 1,
				'product_descr_visible'    => 1,
				'product_price_visible'    => 1,
				'product_thumb_visible'    => 1,
				'subtotal_visible_product' => 1,
				'subtotal_visible_cart'    => 1,
				'subtotal_visible_orders'  => 1
			) );

			// Merge.
			$internal_data = array_merge( $data, $internal_data );
		}

		return $internal_data;
	}

	/**
	 * Validates internal component data before saving.
	 *
	 * @param  array  $component_data
	 * @return array
	 */
	public static function validate_internal_component_data( $component_data, $context = 'rest' ) {

		if ( ! empty( $component_data ) ) {

			$component_data = wp_parse_args( $component_data, array(
				'priced_individually'  => 'no',
				'shipped_individually' => 'no',
				'optional'             => 'no',
				'selection_mode'       => 'dropdowns'
			) );

			// Clean up.
			$component_data = array_diff_key( $component_data, array( 'delete' => 1, 'thumbnail_src' => 1 ) );

			// Validate quantities.
			if ( isset( $component_data[ 'quantity_min' ] ) && ! empty( $component_data[ 'quantity_max' ] ) ) {
				if ( $component_data[ 'quantity_min' ] > $component_data[ 'quantity_max' ] ) {
					throw new WC_REST_Exception( 'woocommerce_rest_invalid_component_data', __( 'Invalid data - \'quantity_min\' cannot be greater than \'quantity_max\'.', 'woocommerce-composite-products' ), 400 );
				}
			}

			// Validate component options.
			if ( 'rest' === $context ) {

				$component_options = WC_CP_Component::query_component_options( $component_data );

				if ( empty( $component_options ) ) {
					throw new WC_REST_Exception( 'woocommerce_rest_invalid_component_data', __( 'Invalid data - No component options found.', 'woocommerce-composite-products' ), 400 );
				}

				if ( ! empty( $component_data[ 'default_id' ] ) && count( $component_options ) > 0 ) {

					if ( ! in_array( $component_data[ 'default_id' ], $component_options ) ) {
						$component_data[ 'default_id' ] = '';
					}

				} else {

					// If the component option is only one, set it as default.
					if ( count( $component_options ) === 1 && 'no' === $component_data[ 'optional' ] ) {
						$component_data[ 'default_id' ] = $component_options[0];
					} else {
						$component_data[ 'default_id' ] = '';
					}
				}
			}

			// Validate title.
			if ( empty( $component_data[ 'title' ] ) ) {
				throw new WC_REST_Exception( 'woocommerce_rest_invalid_component_data', __( 'Invalid data - Undefined title.', 'woocommerce-composite-products' ), 400 );
			}

			// Validate thumbnail.
			if ( isset( $component_data[ 'thumbnail_id' ] ) && false === $component_data[ 'thumbnail_id' ] ) {
				throw new WC_REST_Exception( 'woocommerce_rest_invalid_component_data', __( 'Invalid data - Bad thumbnail ID/URL.', 'woocommerce-composite-products' ), 400 );
			}
		}

		return $component_data;
	}

	/**
	 * Validates scenario data with REST API schema.
	 *
	 * @param  array  $scenario_data
	 * @return array
	 */
	public static function sanitize_rest_api_scenario_data( $data ) {

		$validated_configuration = array();
		$validated_actions       = array();

		if ( ! empty( $data[ 'configuration' ] ) && is_array( $data[ 'configuration' ] ) ) {
			foreach ( $data[ 'configuration' ] as $component_config_data ) {

				if ( empty( $component_config_data[ 'component_id' ] ) ) {
					continue;
				}

				$component_id = strval( $component_config_data[ 'component_id' ] );

				if ( in_array( $component_id, wp_list_pluck( $validated_configuration, 'component_id' ) ) ) {
					continue;
				}

				if ( empty( $component_config_data[ 'component_options' ] ) || ! is_array( $component_config_data[ 'component_options' ] ) ) {
					$component_config_data[ 'component_options' ] = array( 0 );
				}

				$validated_configuration[] = array(
					'component_id'      => $component_id,
					'component_options' => array_map( 'intval', $component_config_data[ 'component_options' ] ),
					'options_modifier'  => isset( $component_config_data[ 'options_modifier' ] ) && in_array( $component_config_data[ 'options_modifier' ], array( 'in', 'not-in', 'masked' ) ) ? $component_config_data[ 'options_modifier' ] : 'in'
				);
			}
		}

		if ( ! empty( $data[ 'actions' ] ) && is_array( $data[ 'actions' ] ) ) {
			foreach ( $data[ 'actions' ] as $action_data ) {

				if ( empty( $action_data[ 'action_id' ] ) ) {
					continue;
				}

				$action_id = strval( $action_data[ 'action_id' ] );

				if ( in_array( $action_id, wp_list_pluck( $validated_actions, 'action_id' ) ) ) {
					continue;
				}

				$validated_actions[] = array(
					'action_id'   => $action_id,
					'action_data' => ! empty( $action_data[ 'action_data' ] ) && is_array( $action_data[ 'action_data' ] ) ? $action_data[ 'action_data' ] : array(),
					'is_active'   => isset( $action_data[ 'is_active' ] ) && wc_string_to_bool( $action_data[ 'is_active' ] )
				);
			}
		}

		return array(
			'name'          => ! empty( $data[ 'name' ] ) ? strip_tags( $data[ 'name' ] ) : '',
			'description'   => isset( $data[ 'description' ] ) ? wp_kses_post( $data[ 'description' ] ) : '',
			'configuration' => $validated_configuration,
			'actions'       => $validated_actions
		);
	}

	/**
	 * Validates scenario data with REST API schema.
	 *
	 * @param  array  $scenario_data
	 * @return array
	 */
	public static function validate_rest_api_scenario_data( $data ) {

		if ( empty( $data[ 'name' ] ) ) {
			throw new WC_REST_Exception( 'woocommerce_rest_invalid_scenario_data', __( 'Invalid data - Undefined name.', 'woocommerce-composite-products' ), 400 );
		}

		return $data;
	}

	/**
	 * Converts scenario data supplied using the api schema to the internal schema.
	 *
	 * @param  array  $scenario_rest_api_data
	 * @return array
	 */
	public static function convert_rest_api_scenario_data( $data ) {

		$component_data = array();
		$modifier_data  = array();
		$actions_data   = array();

		if ( ! empty( $data[ 'configuration' ] ) ) {
			foreach ( $data[ 'configuration' ] as $component_config_data ) {

				$component_id                    = $component_config_data[ 'component_id' ];
				$component_data[ $component_id ] = $component_config_data[ 'component_options' ];
				$modifier_data[ $component_id ]  = $component_config_data[ 'options_modifier' ];
			}
		}

		if ( ! empty( $data[ 'actions' ] ) ) {
			foreach ( $data[ 'actions' ] as $action_data ) {

				$action_id = $action_data[ 'action_id' ];

				$actions_data[ $action_id ] = array_merge( $action_data[ 'action_data' ], array( 'is_active' => wc_bool_to_string( $action_data[ 'is_active' ] ) ) );
			}
		}

		return array(
			'component_data'   => $component_data,
			'modifier'         => $modifier_data,
			'scenario_actions' => $actions_data,
			'description'      => isset( $data[ 'description' ] ) ? $data[ 'description' ] : '',
			'title'            => isset( $data[ 'name' ] ) ? $data[ 'name' ] : ''
		);
	}

	/**
	 * Converts scenario data with internal schema to REST API schema.
	 *
	 * @param  WC_Product_Composite  $product
	 * @return array
	 */
	private static function get_rest_api_scenario_data( $product ) {
		return $product->get_scenario_data( 'rest' );
	}

	/**
	 * Converts component data with internal schema to REST API schema.
	 *
	 * @param  WC_Product_Composite  $product
	 * @return array
	 */
	private static function get_rest_api_component_data( $product ) {
		return $product->get_composite_data( 'rest' );
	}

	/*
	|--------------------------------------------------------------------------
	| Orders.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Gets extended (unprefixed) schema properties for order line items.
	 *
	 * @return array
	 */
	private static function get_extended_order_line_item_schema() {

		return array(
			'composite_parent'   => array(
				'description' => __( 'ID of parent line item, applicable if the item is part of a composite.', 'woocommerce-composite-products' ),
				'type'        => 'string',
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
			),
			'composite_children' => array(
				'description' => __( 'IDs of composited line items, applicable if the item is a composite container.', 'woocommerce-composite-products' ),
				'type'        => 'array',
				'context'     => array( 'view', 'edit' ),
				'items'       => array(
					'type'       => 'integer'
				),
				'readonly'    => true,
			),
			'composite_configuration' => array(
				'description' => __( 'Composite product configuration array. Must be defined when adding a composite-type line item to an order, to ensure components are added to the order as well.', 'woocommerce-composite-products' ),
				'type'        => 'array',
				'context'     => array( 'edit' ),
				'items'       => array(
					'type'       => 'object',
					'properties' => array(
						'component_id'   => array(
							'description' => __( 'Component ID.', 'woocommerce-composite-products' ),
							'type'        => 'integer',
							'context'     => array( 'edit' )
						),
						'product_id'        => array(
							'description' => __( 'Chosen product ID.', 'woocommerce-composite-products' ),
							'type'        => 'integer',
							'context'     => array( 'edit' )
						),
						'quantity'          => array(
							'description' => __( 'Chosen product quantity.', 'woocommerce-composite-products' ),
							'type'        => 'integer',
							'context'     => array( 'edit' )
						),
						'variation_id'      => array(
							'description' => __( 'Chosen variation ID, if applicable.', 'woocommerce-composite-products' ),
							'type'        => 'integer',
							'context'     => array( 'edit' )
						),
						'attributes'        => array(
							'description' => __( 'Chosen variation data to pass into \'WC_Order::add_product\', if applicable.', 'woocommerce-composite-products' ),
							'type'        => 'object',
							'context'     => array( 'edit' )
						),
						'args'              => array(
							'description' => __( 'Additional arguments to pass into \'WC_Order::add_product\', if applicable.', 'woocommerce-composite-products' ),
							'type'        => 'object',
							'context'     => array( 'edit' )
						)
					)
				)
			)
		);
	}


	/**
	 * Modify order contents to include composite product components.
	 *
	 * @param  WC_Order  $order
	 * @param  array     $request
	 */
	public static function add_composite_to_order( $order, $request ) {

		$items_to_remove = array();

		foreach ( $order->get_items( 'line_item' ) as $item_id => $item ) {
			if ( $apply_configuration = $item->get_meta( '_composite_configuration', true ) ) {

				$composite = $item->get_product();
				$quantity  = $item->get_quantity();

				// Preserve props and meta.

				$args = array(
					'configuration' => $apply_configuration,
					'meta_data'     => $item->get_meta_data()
				);

				foreach ( $item->get_data_keys() as $key ) {

					$fn = 'get_' . $key;

					if ( in_array( $key, array( 'name', 'tax_class', 'subtotal', 'subtotal_tax', 'total', 'total_tax', 'taxes' ) ) && is_callable( array( $item, $fn ) ) ) {
						$args[ $key ] = $item->$fn( 'edit' );
					}
				}

				// Add new configuration.

				$result = WC_CP()->order->add_composite_to_order( $composite, $order, $quantity, $args );

				$item->delete_meta_data( '_composite_configuration' );

				if ( ! is_wp_error( $result ) ) {
					$items_to_remove[] = $item;
				}

			}
		}

		$order->save();

		foreach ( $items_to_remove as $remove_item ) {
			$order->remove_item( $remove_item->get_id() );
			$remove_item->delete();
		}

		return $order;
	}


	/**
	 * Converts a posted composite configuration to a format understood by 'WC_CP_Cart::validate_composite_configuration'.
	 *
	 * @since  3.14.0
	 *
	 * @param  array                  $posted_configuration
	 * @param  WC_Product_Composite   $composite
	 * @param  WC_Order_Item_Product  $item
	 * @return array
	 */
	public static function parse_posted_composite_configuration( $posted_configuration, $composite, $item ) {

		$configuration = array();

		foreach ( $posted_configuration as $component_configuration ) {

			// WC_CP_Cart::validate_composite_configuration expects the array to be indexed by component ID.
			if ( ! empty( $component_configuration[ 'component_id' ] ) ) {
				$component_id                   = $component_configuration[ 'component_id' ];
				$configuration[ $component_id ] = array_diff_key( $component_configuration, array( 'component_id' => 1 ) );
			} else {
				continue;
			}

			// 'WC_CP_Cart::validate_composite_configuration' expects posted attributes in 'WC_Cart::add_to_cart' format.
			if ( ! empty( $component_configuration[ 'attributes' ] ) && is_array( $component_configuration[ 'attributes' ] ) ) {

				if ( empty( $component_configuration[ 'component_id' ] ) || empty( $component_configuration[ 'product_id' ] ) ) {
					continue;
				}

				$component_option = $composite->get_component_option( $component_configuration[ 'component_id' ], absint( $component_configuration[ 'product_id' ] ) );

				if ( ! $component_option ) {
					continue;
				}

				$parent_attributes = $component_option->get_product()->get_attributes();
				$posted_attributes = $component_configuration[ 'attributes' ];
				$attributes        = array();

				foreach ( $parent_attributes as $parent_attribute ) {

					if ( ! $parent_attribute->get_variation() ) {
						continue;
					}

					$attribute_label = wc_attribute_label( $parent_attribute->get_name() );
					$attribute_name  = $parent_attribute->get_name();

					$variation_attribute_name = wc_variation_attribute_name( $attribute_name );

					foreach ( $posted_attributes as $posted_attribute ) {

						$found_attribute = false;

						// Locate attribute.
						if ( isset( $posted_attribute[ 'name' ] ) ) {
							if ( stripslashes( $posted_attribute[ 'name' ] ) === $attribute_label ) {
								$found_attribute = true;
							} elseif ( wc_sanitize_taxonomy_name( stripslashes( $posted_attribute[ 'name' ] ) ) === str_replace( 'pa_', '', wc_sanitize_taxonomy_name( $attribute_name ) ) ) {
								$found_attribute = true;
							}
						}

						if ( $found_attribute ) {

							// Get the slug from the name posted to the API.
							if ( $parent_attribute->is_taxonomy() ) {

								$attribute_value = isset( $posted_attribute[ 'option' ] ) ? wc_clean( stripslashes( $posted_attribute[ 'option' ] ) ) : '';

								// First, attempt to get the attribute value term by name.
								$term = get_term_by( 'name', $attribute_value, $attribute_name );

								// Then, attempt to get it by slug.
								if ( ! $term || is_wp_error( $term ) ) {
									$term = get_term_by( 'slug', $attribute_value, $attribute_name );
								}

								// We should have a result at this point.
								if ( $term && ! is_wp_error( $term ) ) {
									$value = $term->slug;
								// If not, just quit and store a sanitized version of whatever was posted.
								} else {
									$value = sanitize_title( $attribute_value );
								}

							} else {
								$value = html_entity_decode( wc_clean( stripslashes( $posted_attribute[ 'option' ] ) ), ENT_QUOTES, get_bloginfo( 'charset' ) );
							}

							$attributes[ $variation_attribute_name ] = $value;
							break;
						}
					}
				}

				$configuration[ $component_id ][ 'attributes' ] = $attributes;
			}
		}

		/**
		 * 'woocommerce_parsed_rest_composite_order_item_configuration' filter.
		 *
		 * @since  3.14.0
		 *
		 * @param  array                  $configuration
		 * @param  WC_Product_Composite   $composite
		 * @param  WC_Order_Item_Product  $item
		 */
		return apply_filters( 'woocommerce_parsed_rest_composite_order_item_configuration', $configuration, $composite, $item );
	}

	/**
	 * Save composite configuration data on item for later processing.
	 *
	 * @param  WC_Order_Item  $item
	 * @param  array          $posted_item_data
	 */
	public static function set_order_item( $item, $posted_item_data ) {

		$action = ! empty( $posted_item_data[ 'id' ] ) ? 'update' : 'create';

		if ( 'create' === $action && ! empty( $posted_item_data[ 'composite_configuration' ] ) ) {

			$composite = $item->get_product();
			$quantity  = $item->get_quantity();

			if ( $composite && $composite->is_type( 'composite' ) ) {

				// Load required frontend functions.
				include_once WC_ABSPATH . 'includes/wc-cart-functions.php';

				$configuration = self::parse_posted_composite_configuration( $posted_item_data[ 'composite_configuration' ], $composite, $item );

				if ( WC_CP()->cart->validate_composite_configuration( $composite, $quantity, $configuration, 'add-to-order' ) ) {

					$item->update_meta_data( '_composite_configuration', WC_CP()->cart->parse_composite_configuration( $composite, $configuration ) );

				} else {

					$message = __( 'The submitted composite configuration could not be added to this order.', 'woocommerce-composite-products' );
					throw new WC_REST_Exception( 'woocommerce_rest_invalid_composite_configuration', $message, 400 );
				}

			} else {
				$message = __( 'A composite with this ID does not exist.', 'woocommerce-composite-products' );
				throw new WC_REST_Exception( 'woocommerce_rest_invalid_composite', $message, 400 );
			}
		}
	}

	/**
	 * Adds 'composite_parent' and 'composite_children' schema properties to line items.
	 *
	 * @param  array  $schema
	 * @return array
	 */
	public static function filter_order_schema( $schema ) {

		foreach ( self::get_extended_order_line_item_schema() as $field_name => $field_content ) {
			$schema[ 'line_items' ][ 'properties' ][ $field_name ] = $field_content;
		}

		return $schema;
	}

	/**
	 * Filters WC REST API order responses to add references between composite container/children items. Also modifies expanded product data based on the pricing and shipping settings.
	 *
	 * @since  3.7.0
	 *
	 * @param  WP_REST_Response   $response
	 * @param  WP_Post | WC_Data  $object
	 * @param  WP_REST_Request    $request
	 * @return WP_REST_Response
	 */
	public static function filter_order_response( $response, $object, $request ) {

		if ( $response instanceof WP_HTTP_Response ) {

			if ( $object instanceof WP_Post ) {
				$object = wc_get_order( $object );
			}

			$order_data = $response->get_data();
			$order_data = self::get_extended_order_data( $order_data, $object );

			$response->set_data( $order_data );
		}

		return $response;
	}

	/**
	 * Append CP data to order data.
	 *
	 * @param  array     $order_data
	 * @param  WC_Order  $order
	 * @return array
	 */
	private static function get_extended_order_data( $order_data, $order ) {

		if ( ! empty( $order_data[ 'line_items' ] ) ) {

			$order_items = $order->get_items();

			foreach ( $order_data[ 'line_items' ] as $order_data_item_index => $order_data_item ) {

				// Default values.
				$order_data[ 'line_items' ][ $order_data_item_index ][ 'composite_parent' ]   = '';
				$order_data[ 'line_items' ][ $order_data_item_index ][ 'composite_children' ] = array();

				$order_data_item_id = $order_data_item[ 'id' ];

				// Add relationship references.
				if ( ! isset( $order_items[ $order_data_item_id ] ) ) {
					continue;
				}

				$parent_id    = wc_cp_get_composited_order_item_container( $order_items[ $order_data_item_id ], $order, true );
				$children_ids = wc_cp_get_composited_order_items( $order_items[ $order_data_item_id ], $order, true );

				if ( false !== $parent_id ) {
					$order_data[ 'line_items' ][ $order_data_item_index ][ 'composite_parent' ] = $parent_id;
				} elseif ( ! empty( $children_ids ) ) {
					$order_data[ 'line_items' ][ $order_data_item_index ][ 'composite_children' ] = $children_ids;
				} else {
					continue;
				}

				// Modify product data.
				if ( ! isset( $order_data_item[ 'product_data' ] ) ) {
					continue;
				}

				add_filter( 'woocommerce_get_product_from_item', array( WC_CP()->order, 'get_product_from_item' ), 10, 3 );
				$product = $order->get_product_from_item( $order_items[ $order_data_item_id ] );
				remove_filter( 'woocommerce_get_product_from_item', array( WC_CP()->order, 'get_product_from_item' ), 10, 3 );

				$order_data[ 'line_items' ][ $order_data_item_index ][ 'product_data' ][ 'price' ]                  = $product->get_price();
				$order_data[ 'line_items' ][ $order_data_item_index ][ 'product_data' ][ 'sale_price' ]             = $product->get_sale_price() ? $product->get_sale_price() : null;
				$order_data[ 'line_items' ][ $order_data_item_index ][ 'product_data' ][ 'regular_price' ]          = $product->get_regular_price();

				$order_data[ 'line_items' ][ $order_data_item_index ][ 'product_data' ][ 'shipping_required' ]      = $product->needs_shipping();

				$order_data[ 'line_items' ][ $order_data_item_index ][ 'product_data' ][ 'weight' ]                 = $product->get_weight() ? $product->get_weight() : null;
				$order_data[ 'line_items' ][ $order_data_item_index ][ 'product_data' ][ 'dimensions' ][ 'length' ] = $product->length;
				$order_data[ 'line_items' ][ $order_data_item_index ][ 'product_data' ][ 'dimensions' ][ 'width' ]  = $product->width;
				$order_data[ 'line_items' ][ $order_data_item_index ][ 'product_data' ][ 'dimensions' ][ 'height' ] = $product->height;
			}
		}

		return $order_data;
	}

	/**
	 * Filters WC v1-v3 REST API order response content to add composite container/children item references.
	 */
	private static function add_legacy_hooks() {
		add_filter( 'woocommerce_api_order_response', array( __CLASS__, 'legacy_order_response' ), 10, 4 );
	}

	/**
	 * Filters WC v1-v3 REST API order responses to add references between composite container/children items. Also modifies expanded product data based on the pricing and shipping settings.
	 *
	 * @param  array          $order_data
	 * @param  WC_Order       $order
	 * @param  array          $fields
	 * @param  WC_API_Server  $server
	 * @return array
	 */
	public static function legacy_order_response( $order_data, $order, $fields, $server ) {

		$order_data = self::get_extended_order_data( $order_data, $order );

		return $order_data;
	}
}

WC_CP_REST_API::init();
