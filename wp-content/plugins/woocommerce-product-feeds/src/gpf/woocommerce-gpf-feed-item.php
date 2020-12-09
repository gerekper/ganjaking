<?php

/**
 * Class WoocommerceGpfFeedItem
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class WoocommerceGpfFeedItem {

	/**
	 * The specific WC_Product that this item represents.
	 *
	 * @var WC_Product|WC_Product_Variation
	 */
	private $specific_product;

	/**
	 * The "general" WC_Product. Either the product, or its parent in the case of a variation.
	 *
	 * @var WC_Product
	 */
	private $general_product;

	/**
	 * The feed format that the item is being prepared for.
	 *
	 * @var string
	 */
	private $feed_format;

	/**
	 * @var WoocommerceGpfCommon
	 */
	private $common;

	/**
	 * @var WoocommerceGpfDebugService
	 */
	protected $debug;

	/**
	 * Image style to be used when generated the image URLs.
	 *
	 * Override by using the filter 'woocommerce_gpf_image_style'
	 *
	 * @var string
	 */
	private $image_style = 'full';

	/**
	 * Which description to use, "full" or "short".
	 *
	 * @var string
	 */
	private $description_type = 'full';

	/**
	 * Unit of measurement that the shipping height/width/length are in
	 *
	 * Defaults to the standard store dimension unit.
	 * Override by using the filter 'woocommerce_gpf_shipping_dimension_unit'.
	 * Valid values are 'in', or 'cm'.
	 *
	 * @var string
	 */
	private $shipping_dimension_unit = '';

	/**
	 * Unit of measure that shipping weights are in.
	 *
	 * Defaults to the standard store weight unit.
	 * Override by using the filter 'woocommerce_gpf_shipping_weight_unit'.
	 *
	 * Valid values are lbs, oz, g, kg
	 *
	 * @var string
	 */
	private $shipping_weight_unit = 'g';

	/**
	 * Whether this item represents a variation.
	 *
	 * @var boolean
	 */
	private $is_variation;

	/**
	 * The specific ID represented by this item.
	 *
	 * For variations, this will be the variation ID. For simple products, it
	 * will be the product ID.
	 *
	 * @var int
	 */
	private $specific_id;

	/**
	 * The post ID of the most general product represented by this item.
	 *
	 * For variations, this will be the parent product ID. For simple products,
	 * it will be the product ID.
	 *
	 * @var int
	 */
	private $general_id;

	/**
	 * Additional elements that apply to this item.
	 *
	 * @var array
	 */
	public $additional_elements;

	/**
	 * The ID of the feed item.
	 *
	 * @var string
	 */
	public $ID;

	/**
	 * The GUID of the feed item.
	 *
	 * @var string
	 */
	public $guid;

	/**
	 * The title of the item.
	 *
	 * @var string
	 */
	public $title;

	/**
	 * The item_group_id of the feed item.
	 *
	 * @var string
	 */
	public $item_group_id;

	/**
	 * The various different descriptions available for the product.
	 */
	public $descriptions;

	/**
	 * The calculated description of the feed item.
	 *
	 * @var string
	 */
	public $description;

	/**
	 * The image link for this feed item.
	 *
	 * @var string
	 */
	public $image_link;

	/**
	 * Array of additional images that apply to this feed item.
	 *
	 * @var array
	 */
	public $additional_images;

	/**
	 * The purchase link for this feed item.
	 *
	 * @var string
	 */
	public $purchase_link;

	/**
	 * The sale price exclusive of taxes.
	 *
	 * @var string
	 */
	public $sale_price_ex_tax;

	/**
	 * The sale price including taxes.
	 *
	 * @var string
	 */
	public $sale_price_inc_tax;

	/**
	 * The regular price exclusive of taxes.
	 *
	 * @var string
	 */
	public $regular_price_ex_tax;

	/**
	 * The regular price including taxes.
	 *
	 * @var string
	 */
	public $regular_price_inc_tax;

	/**
	 * The start date that the sale price applies.
	 *
	 * @var string
	 */
	public $sale_price_start_date;

	/**
	 * The end date that the sale price applies.
	 *
	 * @var string
	 */
	public $sale_price_end_date;

	/**
	 * The current price of the item excluding taxes.
	 *
	 * @var string
	 */
	public $price_ex_tax;

	/**
	 * The current price of the item including taxes.
	 *
	 * @var string
	 */
	public $price_inc_tax;

	/**
	 * The SKU of the feed item.
	 *
	 * @var string
	 */
	public $sku;

	/**
	 * The shipping weight of the item.
	 *
	 * @var float
	 */
	public $shipping_weight;

	/**
	 * Whether the product is in stock.
	 *
	 * @var bool
	 */
	public $is_in_stock;

	/**
	 * The quantity of stock for this item.
	 * @var int
	 */
	public $stock_quantity;

	/**
	 * Whether or not we should calculate prices.
	 *
	 * Optional. Defaults to TRUE.
	 *
	 * @var bool
	 */
	private $calculate_prices;

	/**
	 * @var array
	 */
	private $image_sources = [];

	/**
	 * @var array
	 */
	private $ordered_images = [];


	/**
	 * Constructor.
	 *
	 * Store dependencies.
	 *
	 * @param WoocommerceGpfCommon $woocommerce_gpf_common
	 * @param WC_Product $specific_product The specific product being output.
	 * @param WC_Product $general_product The "general" product being processed.
	 * @param string $feed_format The feed format being output.
	 * @param WoocommerceGpfDebugService $debug
	 * @param [type]     $woocommerce_gpf_common The WoocommerceGpfCommon instance.
	 * @param bool $calculate_prices
	 */
	public function __construct(
		WC_Product $specific_product,
		WC_Product $general_product,
		$feed_format = 'all',
		WoocommerceGpfCommon $woocommerce_gpf_common,
		WoocommerceGpfDebugService $debug,
		$calculate_prices = true
	) {
		$this->specific_product        = $specific_product;
		$this->general_product         = $general_product;
		$this->feed_format             = $feed_format;
		$this->common                  = $woocommerce_gpf_common;
		$this->debug                   = $debug;
		$this->additional_images       = array();
		$this->image_style             = apply_filters(
			'woocommerce_gpf_image_style',
			$this->image_style
		);
		$this->description_type        = apply_filters(
			'woocommerce_gpf_description_type',
			$this->description_type
		);
		$this->shipping_dimension_unit = get_option( 'woocommerce_dimension_unit' );
		$this->shipping_weight_unit    = get_option( 'woocommerce_weight_unit' );
		$this->is_variation            = $this->specific_product instanceof WC_Product_Variation;
		$this->specific_id             = $this->specific_product->get_id();
		$this->general_id              = $this->general_product->get_id();
		$this->calculate_prices        = $calculate_prices;

		// Set taxable address.
		add_filter( 'woocommerce_get_tax_location', array( $this, 'set_taxable_address_to_base' ) );

		// Build the item data.
		$this->build_item();

		// Restore taxable address.
		remove_filter( 'woocommerce_get_tax_location', array( $this, 'set_taxable_address_to_base' ) );
	}

	/**
	 * Work out if a WC Product should be excluded from the feed.
	 *
	 * @param WC_Product|WC_Product_Variation $wc_product The product to check.
	 * @param string $feed_format The feed being produced.
	 *
	 * @return bool True if the product should be excluded. False otherwise.
	 */
	public static function should_exclude( $wc_product, $feed_format ) {
		$excluded = false;
		// Check to see if the product is set as Hidden within WooCommerce.
		if ( 'hidden' === $wc_product->get_catalog_visibility() ) {
			$excluded = true;
		}
		// Check to see if the product has been excluded in the feed config.
		$gpf_data = get_post_meta( $wc_product->get_id(), '_woocommerce_gpf_data', true );
		if ( ! empty( $gpf_data ) ) {
			$gpf_data = maybe_unserialize( $gpf_data );
		}
		if ( ! empty( $gpf_data['exclude_product'] ) ) {
			$excluded = true;
		}
		if ( $wc_product instanceof WC_Product_Variation ) {
			$parent_id = $wc_product->get_parent_id();

			return apply_filters(
				'woocommerce_gpf_exclude_variation',
				apply_filters( 'woocommerce_gpf_exclude_product', $excluded, $parent_id, $feed_format ),
				$wc_product->get_id(),
				$feed_format
			);
		} else {
			$parent_id = $wc_product->get_id();

			return apply_filters( 'woocommerce_gpf_exclude_product', $excluded, $parent_id, $feed_format );
		}
	}

	/**
	 * Work out if a feed item should be excluded from the feed based on.
	 *
	 * @return bool  True if the product should be excluded. False otherwise.
	 */
	public function is_excluded() {
		return self::should_exclude( $this->specific_product, $this->feed_format );
	}

	/**
	 * Allow private properties to be accessed read-only.
	 */
	public function __get( $key ) {
		if ( isset( $this->$key ) ) {
			return $this->$key;
		}

		return null;
	}

	/**
	 * Calculates the data for the feed item.
	 */
	private function build_item() {

		// Calculate the various prices we need.
		$this->get_product_prices();

		// Get main item information
		$this->ID            = $this->specific_id;
		$this->guid          = 'woocommerce_gpf_' . $this->ID;
		$this->item_group_id = 'woocommerce_gpf_' . $this->general_id;
		do_action( 'woocommerce_gpf_before_description_generation', $this->specific_id, $this->general_id );
		$this->description = $this->get_product_description();
		$this->description = apply_filters(
			'woocommerce_gpf_description',
			$this->description,
			$this->general_id,
			$this->is_variation ? $this->specific_id : null
		);
		do_action( 'woocommerce_gpf_after_description_generation', $this->specific_id, $this->general_id );

		$this->purchase_link       = $this->specific_product->get_permalink();
		$this->is_in_stock         = $this->specific_product->is_in_stock();
		$this->stock_quantity      = $this->specific_product->get_stock_quantity();
		$this->sku                 = $this->specific_product->get_sku();
		$this->shipping_weight     = $this->get_shipping_weight();
		$this->additional_elements = array();

		// Add other elements.
		$this->general_elements();
		$this->get_images();

		// Move title out of additional_elements, and back into the main property if set
		// or fall back to the product_title if not populated.
		if ( ! empty( $this->additional_elements['title'][0] ) ) {
			$this->title = $this->additional_elements['title'][0];
		} else {
			$this->title = $this->specific_product->get_title();
		}
		unset( $this->additional_elements['title'] );

		if ( $this->is_variation &&
			 apply_filters( 'woocommerce_gpf_include_variation_attributes_in_title', true )
		) {
			$include_labels = apply_filters( 'woocommerce_gpf_include_attribute_labels_in_title', true );
			$suffix         = wc_get_formatted_variation( $this->specific_product, true, $include_labels );
			if ( ! empty( $suffix ) ) {
				$this->title .= ' (' . $suffix . ')';
			}
		}
		$this->title = apply_filters(
			'woocommerce_gpf_title',
			$this->title,
			$this->specific_id,
			$this->general_id
		);

		if ( 'google' === $this->feed_format ) {
			$this->shipping_dimensions();
			$this->all_or_nothing_shipping_elements();
		}
		$this->force_stock_status();

		// General, or feed-specific items
		$this->additional_elements = apply_filters( 'woocommerce_gpf_elements', $this->additional_elements, $this->general_id, ( $this->specific_id !== $this->general_id ) ? $this->specific_id : null );
		$this->additional_elements = apply_filters( 'woocommerce_gpf_elements_' . $this->feed_format, $this->additional_elements, $this->general_id, ( $this->specific_id !== $this->general_id ) ? $this->specific_id : null );
	}

	/**
	 * Get the description for a product.
	 *
	 * @return string  The product description.
	 */
	private function get_product_description() {
		$description = null;

		// Populate the various required descriptions.
		$this->descriptions['main_product']       = $this->general_product->get_description();
		$this->descriptions['main_product_short'] = $this->general_product->get_short_description();
		$this->descriptions['variation']          = '';
		if ( $this->general_id !== $this->specific_id ) {
			$this->descriptions['variation'] = $this->specific_product->get_description();
		}
		// Work out which description to use.
		$prepopulations     = $this->common->get_prepopulations();
		$description_option = ! empty( $prepopulations['description'] ) ?
			$prepopulations['description'] :
			'description:varfull';

		// Support for legacy woocommerce_gpf_description_type filter.
		if ( 'short' === $this->description_type ) {
			$description_option = 'description:varshort';
		}

		switch ( $description_option ) {
			case 'description:shortvar':
				$description = ! empty( $this->descriptions['main_product_short'] ) ?
					$this->descriptions['main_product_short'] :
					$this->descriptions['main_product'];
				if ( ! empty( $this->descriptions['variation'] ) ) {
					if ( ! empty( $description ) ) {
						$description .= PHP_EOL;
					}
					$description .= $this->descriptions['variation'];
				}
				break;
			case 'description:full':
				$description = ! empty( $this->descriptions['main_product'] ) ?
					$this->descriptions['main_product'] :
					$this->descriptions['main_product_short'];
				break;
			case 'description:short':
				$description = ! empty( $this->descriptions['main_product_short'] ) ?
					$this->descriptions['main_product_short'] :
					$this->descriptions['main_product'];
				break;
			case 'description:varfull':
				$description = ! empty( $this->descriptions['main_product'] ) ?
					$this->descriptions['main_product'] :
					$this->descriptions['main_product_short'];
				if ( ! empty( $this->descriptions['variation'] ) ) {
					$description = $this->descriptions['variation'];
				}
				break;
			case 'description:varshort':
			case 'short':
				$description = ! empty( $this->descriptions['main_product_short'] ) ?
					$this->descriptions['main_product_short'] :
					$this->descriptions['main_product'];
				if ( ! empty( $this->descriptions['variation'] ) ) {
					$description = $this->descriptions['variation'];
				}
				break;
			case 'description:fullvar':
				$description = ! empty( $this->descriptions['main_product'] ) ?
					$this->descriptions['main_product'] :
					$this->descriptions['main_product_short'];
				if ( ! empty( $this->descriptions['variation'] ) ) {
					if ( ! empty( $description ) ) {
						$description .= PHP_EOL;
					}
					$description .= $this->descriptions['variation'];
				}
				break;
			default:
				$product_values = $this->calculate_values_for_product();
				$description    = isset( $product_values['description'][0] ) ? $product_values['description'][0] : '';
				break;
		}

		$description = apply_filters(
			'the_content',
			$description
		);

		// Strip out invalid unicode.
		$description = preg_replace(
			'/[\x00-\x08\x0B\x0C\x0E-\x1F\x80-\x9F]/u',
			'',
			$description
		);

		// Strip SCRIPT and STYLE tags INCLUDING their content. Taken from wp_strip_all_tags().
		$description = preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', $description );

		// Strip out HTML comments.
		$description = preg_replace( '/<!--(.|\s)*?-->/', '', $description );

		return $description;
	}

	/**
	 * Determines the lowest price (inc & ex. VAT) for a product, taking into
	 * account its child products as well as the main product price.
	 */
	private function get_product_prices() {
		if ( ! $this->calculate_prices ) {
			return;
		}
		// Grab the price of the main product.
		$prices = $this->generate_prices_for_product();

		// Adjust the price on variable products if there are cheaper child products.
		if ( $this->specific_product->get_type() === 'variable' ) {
			$prices = $this->adjust_prices_for_children( $prices );
		}

		$prices = apply_filters(
			'woocommerce_gpf_item_prices',
			$prices,
			$this->specific_product,
			$this->general_product
		);

		// phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_var_export
		$this->debug->log( 'Prices calculated for %d are: %s', [ $this->specific_id, var_export( $prices, 1 ) ] );
		// phpcs:enable

		// Set the prices into the object.
		$this->sale_price_ex_tax     = $prices['sale_price_ex_tax'];
		$this->sale_price_inc_tax    = $prices['sale_price_inc_tax'];
		$this->regular_price_ex_tax  = $prices['regular_price_ex_tax'];
		$this->regular_price_inc_tax = $prices['regular_price_inc_tax'];
		$this->sale_price_start_date = $prices['sale_price_start_date'];
		$this->sale_price_end_date   = $prices['sale_price_end_date'];
		$this->price_ex_tax          = $prices['price_ex_tax'];
		$this->price_inc_tax         = $prices['price_inc_tax'];
	}

	/**
	 * Generates the inc, and ex. tax prices for both the regular, and sale
	 * price for a specific product, and returns them.
	 *
	 * @param WC_Product $product Optional product to use. If not provided then
	 *                             $this->specific_product is used.
	 *
	 * @return array
	 */
	private function generate_prices_for_product( $product = null ) {
		// Default to the current product if none passed in.
		if ( is_null( $product ) ) {
			$product = $this->specific_product;
		}
		// Initialise defaults.
		$prices                          = array();
		$prices['sale_price_ex_tax']     = null;
		$prices['sale_price_inc_tax']    = null;
		$prices['regular_price_ex_tax']  = null;
		$prices['regular_price_inc_tax'] = null;
		$prices['sale_price_start_date'] = null;
		$prices['sale_price_end_date']   = null;
		$prices['price_ex_tax']          = null;
		$prices['price_inc_tax']         = null;

		// Find out the product type.
		$product_type = $product->get_type();

		// Allow custom price calculators.
		$price_calculator = apply_filters(
			'woocommerce_gpf_product_price_calculator_callback',
			null,
			$product_type,
			$product
		);
		if ( null !== $price_calculator && is_callable( $price_calculator ) ) {
			return $price_calculator( $product, $prices );
		}

		// Standard price calculator functions.
		if ( 'variable' === $product_type ) {
			// Variable products shouldn't have prices. Works around issue in WC
			// core : https://github.com/woocommerce/woocommerce/issues/16145
			return $prices;
		}

		// Grab the regular price of the base product.
		$regular_price = $product->get_regular_price();
		if ( '' !== $regular_price ) {
			$prices['regular_price_ex_tax']  = wc_get_price_excluding_tax( $product, array( 'price' => $regular_price ) );
			$prices['regular_price_inc_tax'] = wc_get_price_including_tax( $product, array( 'price' => $regular_price ) );
		}
		// Grab the sale price of the base product. Some plugins (Dyanmic
		// pricing as an example) filter the active price, but not the sale
		// price. If the active price < the regular price treat it as a sale
		// price.
		$sale_price   = $product->get_sale_price();
		$active_price = $product->get_price();

		// phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_var_export
		$this->debug->log(
			'get_regular_price() for %d is: %s',
			[
				$product->get_id(),
				var_export( $regular_price, 1 ),
			]
		);
		$this->debug->log( 'get_sale_price() for %d is: %s', [ $product->get_id(), var_export( $sale_price, 1 ) ] );
		$this->debug->log( 'get_active_price() for %d is: %s', [ $product->get_id(), var_export( $active_price, 1 ) ] );
		// phpcs:enable

		if ( ( empty( $sale_price ) && $active_price < $regular_price ) ||
			 ( ! empty( $sale_price ) && $active_price < $sale_price ) ) {
			$sale_price = $active_price;
		}
		if ( '' !== $sale_price ) {
			$prices['sale_price_ex_tax']     = wc_get_price_excluding_tax( $product, array( 'price' => $sale_price ) );
			$prices['sale_price_inc_tax']    = wc_get_price_including_tax( $product, array( 'price' => $sale_price ) );
			$prices['sale_price_start_date'] = $product->get_date_on_sale_from();
			$prices['sale_price_end_date']   = $product->get_date_on_sale_to();
		}

		// If the sale price dates no longer apply, make sure we don't include a sale price.
		$now = new \WC_DateTime();
		if ( ! empty( $prices['sale_price_end_date'] ) && $prices['sale_price_end_date'] < $now ) {
			$prices['sale_price_end_date']   = null;
			$prices['sale_price_start_date'] = null;
			$prices['sale_price_ex_tax']     = null;
			$prices['sale_price_inc_tax']    = null;
		}
		// If we have a sale end date in the future, but no start date, set the start date to now()
		if ( ! empty( $prices['sale_price_end_date'] ) &&
			 $prices['sale_price_end_date'] > $now &&
			 empty( $prices['sale_price_start_date'] )
		) {
			$prices['sale_price_start_date'] = $now;
		}
		// If we have a sale start date in the past, but no end date, do not include the start date.
		if ( ! empty( $prices['sale_price_start_date'] ) &&
			 $prices['sale_price_start_date'] < $now &&
			 empty( $prices['sale_price_end_date'] )
		) {
			$prices['sale_price_start_date'] = null;
		}
		// If we have a start date in the future, but no end date, assume a one-day sale.
		if ( ! empty( $prices['sale_price_start_date'] ) &&
			 $prices['sale_price_start_date'] > $now &&
			 empty( $prices['sale_price_end_date'] )
		) {
			$prices['sale_price_end_date'] = clone $prices['sale_price_start_date'];
			$prices['sale_price_end_date']->add( new DateInterval( 'P1D' ) );
		}

		// Populate a "price", using the sale price if there is one, the actual price if not.
		if ( null !== $prices['sale_price_ex_tax'] ) {
			$prices['price_ex_tax']  = $prices['sale_price_ex_tax'];
			$prices['price_inc_tax'] = $prices['sale_price_inc_tax'];
		} else {
			$prices['price_ex_tax']  = $prices['regular_price_ex_tax'];
			$prices['price_inc_tax'] = $prices['regular_price_inc_tax'];
		}

		return $prices;
	}

	/**
	 * Adjusts the prices of the feed item according to child products.
	 */
	private function adjust_prices_for_children( $current_prices ) {
		if ( ! $this->specific_product->has_child() ) {
			return $current_prices;
		}
		$children = $this->specific_product->get_children();
		foreach ( $children as $child ) {
			$child_product = wc_get_product( $child );
			if ( ! $child_product ) {
				continue;
			}
			$product_type = $child_product->get_type();
			if ( 'variation' === $product_type ) {
				$child_is_visible = $child_product->variation_is_visible();
			} else {
				$child_is_visible = $child_product->is_visible();
			}
			if ( ! $child_is_visible ) {
				continue;
			}
			$child_prices = $this->generate_prices_for_product( $child_product );
			if ( ( 0 === (int) $current_prices['price_inc_tax'] ) && ( (int) $child_prices['price_inc_tax'] > 0 ) ) {
				$current_prices = $child_prices;
			} elseif ( ( $child_prices['price_inc_tax'] > 0 ) && ( $child_prices['price_inc_tax'] < $current_prices['price_inc_tax'] ) ) {
				$current_prices = $child_prices;
			}
		}

		return $current_prices;
	}

	/**
	 * Get the ID of the main product image.
	 *
	 * @param $product
	 *
	 * @return int|false
	 */
	private function get_the_product_thumbnail_id( $product ) {
		$post_thumbnail_id = $product->get_image_id();
		if ( ! $post_thumbnail_id ) {
			return false;
		}

		return $post_thumbnail_id;
	}

	/**
	 * Add the "advanced" information to the field based on either the
	 * per-product settings, category settings, or store defaults.
	 *
	 * @access private
	 */
	private function general_elements() {
		$elements       = array();
		$product_values = $this->calculate_values_for_product();
		if ( ! empty( $product_values ) ) {
			foreach ( $product_values as $key => $value ) {
				// Deal with fields that can have multiple, comma separated values
				if ( isset( $this->common->product_fields[ $key ]['multiple'] ) && $this->common->product_fields[ $key ]['multiple'] && ! is_array( $value ) ) {
					$value = explode( ',', $value );
				}
				$elements[ $key ] = (array) $value;
				// Deal with fields that should be output in the Google feed as a single concatenated set of values
				if ( ! empty( $this->common->product_fields[ $key ]['google_single_output'] ) ) {
					$values           = implode(
						$this->common->product_fields[ $key ]['google_single_output'],
						$elements[ $key ]
					);
					$elements[ $key ] = array( $values );
				}
			}
		}
		$this->additional_elements = $elements;
	}

	/**
	 * Add the measurements for a product in the store base unit.
	 *
	 * @return void
	 */
	private function shipping_dimensions() {
		$length = $this->specific_product->get_length();
		$width  = $this->specific_product->get_width();
		$height = $this->specific_product->get_height();

		// Work out the target UOM.
		$this->shipping_dimension_unit = apply_filters(
			'woocommerce_gpf_shipping_dimension_unit',
			$this->shipping_dimension_unit
		);
		// Use cm if the unit isn't supported.
		if ( ! in_array( $this->shipping_dimension_unit, [ 'in', 'cm' ], true ) ) {
			$this->shipping_dimension_unit = 'cm';
		}
		$length = wc_get_dimension( (float) $length, $this->shipping_dimension_unit );
		$width  = wc_get_dimension( (float) $width, $this->shipping_dimension_unit );
		$height = wc_get_dimension( (float) $height, $this->shipping_dimension_unit );

		if ( $length > 0 ) {
			$this->additional_elements['shipping_length'] = [ $length . ' ' . $this->shipping_dimension_unit ];
		}
		if ( $width > 0 ) {
			$this->additional_elements['shipping_width'] = [ $width . ' ' . $this->shipping_dimension_unit ];
		}
		if ( $height > 0 ) {
			$this->additional_elements['shipping_height'] = [ $height . ' ' . $this->shipping_dimension_unit ];
		}
	}

	/**
	 * Send all shipping measurements, or none.
	 *
	 * Make sure that *if* we have length, width or height, that we send all three. If we're
	 * missing any then we send none of them.
	 *
	 * @param array $elements The current feed item elements.
	 * @param int $product_id The product to get the length of.
	 *
	 * @return void
	 */
	private function all_or_nothing_shipping_elements() {
		if ( empty( $this->additional_elements['shipping_width'] ) &&
			 empty( $this->additional_elements['shipping_length'] ) &&
			 empty( $this->additional_elements['shipping_height'] ) ) {
			return;
		}
		if ( empty( $this->additional_elements['shipping_width'] ) ||
			 empty( $this->additional_elements['shipping_length'] ) ||
			 empty( $this->additional_elements['shipping_height'] ) ) {
			unset( $this->additional_elements['shipping_length'] );
			unset( $this->additional_elements['shipping_width'] );
			unset( $this->additional_elements['shipping_height'] );
		}
	}

	/**
	 * Make sure we always send a stock value.
	 */
	private function force_stock_status() {
		if ( ! $this->is_in_stock && empty( $this->additional_elements['availability'] ) ) {
			$this->additional_elements['availability'] = array( 'out of stock' );
		}
	}

	/**
	 * Add additional images to the feed item.
	 */
	private function get_images() {

		// Grab the variation thumbnail if available.
		if ( $this->is_variation ) {
			$image_id           = $this->get_the_product_thumbnail_id( $this->general_product );
			list( $image_link ) = wp_get_attachment_image_src( $image_id, $this->image_style, false );
			if ( ! empty( $this->image_link ) ) {
				$this->register_image_source( $image_id, $image_link, 'variation_image' );
			}
		}

		// Grab the "product image" from main / parent product.
		$image_id            = $this->get_the_product_thumbnail_id( $this->specific_product );
		list ( $image_link ) = wp_get_attachment_image_src( $image_id, $this->image_style, false );
		if ( ! empty( $image_link ) ) {
			$this->register_image_source( $image_id, $image_link, 'product_image' );
		}

		// Get the product ID to inspect for additional images.
		$product_id = $this->specific_id;

		// Work out whether to include additional images on variations. Bail if not.
		$include_on_variations = apply_filters( 'woocommerce_gpf_include_additional_images_on_variations', true, $product_id );
		if ( ! $this->is_variation || $include_on_variations ) {
			// When processing additional images on variations, grab them from the main product.
			if ( $this->is_variation ) {
				$product_id = $this->general_id;
			}

			// List product gallery images first.
			if ( apply_filters( 'woocommerce_gpf_include_product_gallery_images', true ) ) {
				$product_gallery_images = get_post_meta( $product_id, '_product_image_gallery', true );
				if ( ! empty( $product_gallery_images ) ) {
					$product_gallery_images = explode( ',', $product_gallery_images );
					foreach ( $product_gallery_images as $product_gallery_image_id ) {
						$full_image_src = wp_get_attachment_image_src( $product_gallery_image_id, $this->image_style, false );
						// Skip if invalid / missing.
						if ( ! $full_image_src ) {
							continue;
						}
						$this->register_image_source( $product_gallery_image_id, $full_image_src[0], 'product_gallery' );
					}
				}
			}

			// Then attached media.
			if ( apply_filters( 'woocommerce_gpf_include_attached_images', true ) ) {
				$found = false;
				if ( $this->is_variation ) {
					$images = wp_cache_get( 'children_' . $product_id, 'woocommerce_gpf', false, $found );
				}
				if ( false === $found ) {
					$images = get_children(
						array(
							'post_parent'    => $product_id,
							'post_status'    => 'inherit',
							'post_type'      => 'attachment',
							'post_mime_type' => 'image',
							'order'          => 'ASC',
							'orderby'        => 'menu_order',
						)
					);
					if ( $this->is_variation ) {
						wp_cache_set( 'children_' . $product_id, $images, 'woocommerce_gpf', 10 );
					}
				}

				if ( is_array( $images ) && count( $images ) ) {
					foreach ( $images as $image ) {
						$full_image_src = wp_get_attachment_image_src( $image->ID, $this->image_style, false );
						$this->register_image_source( $image->ID, $full_image_src[0], 'attachment' );
					}
				}
			}
		}

		// Uniquefy the ordered_image_sources array...
		$this->ordered_images = array_unique( $this->ordered_images, SORT_REGULAR );

		// Exclude any excluded images based on ID.
		$excluded_ids = $this->general_product->get_meta( 'woocommerce_gpf_excluded_media_ids', true );
		if ( empty( $excluded_ids ) && ! is_array( $excluded_ids ) ) {
			$excluded_ids = [];
		}

		/**
		 * Move the first found image into the primary image slot, and the
		 * rest into the "additional images" list.
		 */
		$done_primary_image = false;

		// Retrieve requested primary image ID from meta and use it if set.
		$primary_media_id = $this->general_product->get_meta( 'woocommerce_gpf_primary_media_id', true );
		if ( ! empty( $primary_media_id ) && ! empty( $this->image_sources[ $primary_media_id ] ) ) {
			$this->image_link   = $this->image_sources[ $primary_media_id ]['url'];
			$done_primary_image = true;
		}

		foreach ( $this->ordered_images as $image ) {
			// Skip if excluded.
			if ( in_array( $image['id'], $excluded_ids, true ) ) {
				continue;
			}
			// Skip if this is the primary ID as we've already set it outside the loop.
			if ( (int) $image['id'] === (int) $primary_media_id ) {
				continue;
			}
			if ( $done_primary_image ) {
				$this->additional_images[] = $image['url'];
			} else {
				$this->image_link   = $image['url'];
				$done_primary_image = true;
			}
		}
	}

	/**
	 * Calculate the values for a product / variation.
	 *
	 * Takes into account:
	 * - values set specifically against the variation
	 * - Values set specifically against the product
	 * - pre-populations that may apply to the variation
	 * - pre-populations that may apply to the variation
	 * - category defaults
	 * - store wide defaults
	 */
	private function calculate_values_for_product() {
		// Grab the values against the product.
		$product_values         = $this->get_specific_values_for_product( 'general' );
		$product_prepopulations = $this->get_prepopulations_for_product( 'general' );

		// Grab the values against the variation if different from product ID.
		if ( $this->specific_id !== $this->general_id ) {
			$variation_values         = $this->get_specific_values_for_product( 'specific' );
			$variation_prepopulations = $this->get_prepopulations_for_product( 'specific' );
		} else {
			$variation_values         = array();
			$variation_prepopulations = array();
		}

		$category_values = $this->common->get_category_values_for_product( $this->general_id );
		$store_values    = $this->common->get_store_default_values();

		// If child.specific then use that
		// elseif parent.specific then use that
		// elseif child.prepulate then use that
		// elseif parent.prepopulate then use that
		// else use category defaults
		// else use store defaults
		$calculated = array_merge(
			$store_values,
			$category_values,
			$product_prepopulations,
			$variation_prepopulations,
			$product_values,
			$variation_values
		);
		if ( 'all' !== $this->feed_format ) {
			$calculated = $this->common->remove_other_feeds( $calculated, $this->feed_format );
		}

		return $this->common->limit_max_values( $calculated );
	}

	/**
	 * Retrieve specific values set against a product.
	 *
	 * @param $which_product string  Whether to pull info for the 'general' or 'specific' product being generated.
	 *
	 * @return array
	 */
	private function get_specific_values_for_product( $which_product ) {
		if ( 'general' === $which_product ) {
			$product_settings = get_post_meta( $this->general_id, '_woocommerce_gpf_data', true );
		} else {
			$product_settings = get_post_meta( $this->specific_id, '_woocommerce_gpf_data', true );
		}
		if ( ! is_array( $product_settings ) ) {
			return array();
		}

		return $this->common->remove_blanks( $product_settings );
	}

	/**
	 * Get the information that would be pre-populated for a product.
	 *
	 * @param $which_product string  Whether to pull info for the 'general' or 'specific' product being generated.
	 */
	private function get_prepopulations_for_product( $which_product ) {
		$results        = array();
		$prepopulations = $this->common->get_prepopulations();
		if ( empty( $prepopulations ) ) {
			return $results;
		}
		foreach ( $prepopulations as $gpf_key => $prepopulate ) {
			if ( empty( $prepopulate ) ) {
				continue;
			}
			$value = $this->get_prepopulate_value_for_product( $prepopulate, $which_product );
			if ( ! empty( $value ) ) {
				$results[ $gpf_key ] = $value;
			}
		}

		return $this->common->remove_blanks( $results );
	}

	/**
	 * Gets a specific prepopulated value for a product.
	 *
	 * @param string $prepopulate The prepopulation value for a product.
	 * @param string $which_product Whether to pull info for the 'general' or 'specific' product being generated.
	 *
	 * @return array                The prepopulated value for this product.
	 */
	private function get_prepopulate_value_for_product( $prepopulate, $which_product ) {

		list( $type, $value ) = explode( ':', $prepopulate );

		$result = array();
		switch ( $type ) {
			case 'tax':
				$result = $this->get_tax_prepopulate_value_for_product( $value, $which_product );
				break;
			case 'field':
				$result = $this->get_field_prepopulate_value_for_product( $value, $which_product );
				break;
			case 'meta':
				$result = $this->get_meta_prepopulate_value_for_product( $value, $which_product );
				break;
			case 'method':
				$result = $this->get_method_prepopulate_value_for_product(
					$prepopulate,
					$which_product
				);
				break;
		}

		return $result;
	}

	private function get_method_prepopulate_value_for_product( $prepopulate, $which_product ) {

		static $prepopulation_options = null;

		// Grab the correct product.
		if ( 'general' === $which_product ) {
			$product = $this->general_product;
		} else {
			$product = $this->specific_product;
		}

		// Check $fq_method is valid.
		if ( is_null( $prepopulation_options ) ) {
			$prepopulation_options = $this->common->get_prepopulate_options();
		}
		if ( ! in_array( $prepopulate, array_keys( $prepopulation_options ), true ) ) {
			return '';
		}

		// Call the specific method.
		$fq_method = str_replace( 'method:', '', $prepopulate );

		list( $class, $method ) = explode( '::', $fq_method );

		return call_user_func( array( $class, $method ), $product );
	}

	/**
	 * Gets a taxonomy value for a product to prepopulate.
	 *
	 * @param string $value The taxonomy to grab values for.
	 * @param string $which_product Whether to pull info for the 'general' or 'specific' product being generated.
	 *
	 * @return array              Array of values to use.
	 */
	private function get_tax_prepopulate_value_for_product( $value, $which_product ) {
		$result = array();

		if ( 'general' === $which_product ) {
			$product    = $this->general_product;
			$product_id = $this->general_id;
		} else {
			$product    = $this->specific_product;
			$product_id = $this->specific_id;
		}

		$product_type = $product->get_type();
		if ( 'variation' === $product_type ) {
			// Get the attributes.
			$attributes = $product->get_variation_attributes();
			// If the requested taxonomy is used as an attribute, grab it's value for this variation.
			if ( ! empty( $attributes[ 'attribute_' . $value ] ) ) {
				$terms = get_terms(
					array(
						'taxonomy' => $value,
						'slug'     => $attributes[ 'attribute_' . $value ],
					)
				);
				if ( empty( $terms ) || is_wp_error( $terms ) ) {
					$result = array();
				} else {
					$result = array( $terms[0]->name );
				}
			} else {
				// Otherwise grab the values to use direct from the term relationships.
				$terms = get_the_terms( $product_id, $value );
				if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
					$result = wp_list_pluck( $terms, 'name' );
				} else {
					// Couldn't find it against the variation - grab the parent product value.
					$terms = get_the_terms( $product->get_parent_id(), $value );
					if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
						$result = wp_list_pluck( $terms, 'name' );
					}
				}
			}
		} else {
			// Get the term(s) tagged against the main product.
			$terms = get_the_terms( $product_id, $value );
			if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
				$result = wp_list_pluck( $terms, 'name' );
			}
		}

		return $result;
	}

	/**
	 * Get a prepopulate value for a specific field for a product.
	 *
	 * @param string $field Details of the field we want.
	 * @param string $which_product Whether to pull info for the 'general' or 'specific' product being generated.
	 *
	 * @return array                The value for this field on this product.
	 */
	private function get_field_prepopulate_value_for_product( $field, $which_product ) {
		if ( 'general' === $which_product ) {
			$product = $this->general_product;
		} else {
			$product = $this->specific_product;
		}
		if ( ! $product ) {
			return array();
		}
		if ( 'sku' === $field ) {
			$sku = $product->get_sku();
			if ( ! empty( $sku ) ) {
				return array( $sku );
			}
		}
		if ( 'product_title' === $field ) {
			return array( $this->general_product->get_title() );
		}
		if ( 'variation_title' === $field ) {
			return array( $this->title );
		}
		if ( 'stock_qty' === $field ) {
			$qty = $product->get_stock_quantity();
			if ( is_null( $qty ) ) {
				return array();
			}

			return array( $product->get_stock_quantity() );
		}
		if ( 'stock_status' === $field ) {
			return array( $product->get_stock_status() );
		}
		if ( 'tax_class' === $field ) {
			$tax_class = $product->get_tax_class();

			return array( ! empty( $tax_class ) ? $tax_class : 'standard' );
		}

		return array();
	}

	/**
	 * Get a prepopulate value for a specific meta key for a product.
	 *
	 * @param string $meta_key Details of the meta key we're interested in.
	 * @param string $which_product Whether to pull info for the 'general' or 'specific' product being generated.
	 *
	 * @return array                The value for the requested meta key on this product.
	 */
	private function get_meta_prepopulate_value_for_product( $meta_key, $which_product ) {
		if ( 'general' === $which_product ) {
			$product_id = $this->general_id;
		} else {
			$product_id = $this->specific_id;
		}

		$values = get_post_meta( $product_id, $meta_key, false );
		foreach ( $values as $key => $value ) {
			if ( empty( $value ) ) {
				unset( $values[ $key ] );
			}
		}

		return $values;
	}

	/**
	 * @return mixed|void
	 */
	private function get_shipping_weight() {
		$raw_weight = apply_filters(
			'woocommerce_gpf_shipping_weight',
			$this->specific_product->get_weight(),
			$this->ID
		);

		$override_unit = apply_filters(
			'woocommerce_gpf_shipping_weight_unit',
			null
		);

		if ( null !== $override_unit ) {
			$this->shipping_weight_unit = $override_unit;

			return wc_get_weight( $raw_weight, $override_unit );
		}

		return $raw_weight;
	}

	/**
	 * Override the taxable address to the store base location.
	 *
	 * @param $address
	 *
	 * @return array
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function set_taxable_address_to_base( $address ) {
		$wc_class = WC();

		return array(
			$wc_class->countries->get_base_country(),
			$wc_class->countries->get_base_state(),
			$wc_class->countries->get_base_postcode(),
			$wc_class->countries->get_base_city(),
		);
	}

	/**
	 * Generate an array of image URLs keyed by ID, showing how they were retrieved. Options are:
	 * - 'product_image'   - the image is set as the product image on the product
	 * - 'product_gallery' - the image is set in the product gallery
	 * - 'attachment'      - the image is attached to the post via WordPress' media mechanism
	 * @return array
	 */
	public function get_image_sources_by_id() {
		return $this->image_sources;
	}

	/**
	 * Generate an array of image URLs keyed by URL, showing how they were retrieved. Options are:
	 * - 'product_image'   - the image is set as the product image on the product
	 * - 'product_gallery' - the image is set in the product gallery
	 * - 'attachment'      - the image is attached to the post via WordPress' media mechanism
	 * @return array
	 */
	public function get_image_sources_by_url() {
		$results = [];
		foreach ( $this->image_sources as $id => $data ) {
			$results[ $data['url'] ]       = $data;
			$results[ $data['url'] ]['id'] = $id;
		}

		return $results;
	}

	/**
	 * Register an image source for a URL.
	 *
	 * @param $image_id int The media ID
	 * @param $image_link string The URL of the image
	 * @param $source string     The source for that image. @see get_image_sources()
	 */
	private function register_image_source( $image_id, $image_link, $source ) {
		if ( ! isset( $this->image_sources[ $image_id ] ) ) {
			$this->image_sources[ $image_id ] = [
				'url'     => $image_link,
				'sources' => [
					$source,
				],
			];
			$this->ordered_images[]           = [
				'id'  => (int) $image_id,
				'url' => $image_link,
			];

			return;
		}
		$this->image_sources[ $image_id ]['sources'] = array_unique(
			array_merge(
				$this->image_sources[ $image_id ]['sources'],
				[
					$source,
				]
			)
		);
	}

	/**
	 * @return array
	 */
	public function get_ordered_images() {
		return $this->ordered_images;
	}
}
