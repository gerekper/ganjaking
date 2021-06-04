<?php
/**
 * WC_CP_Component class
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
 * Component abstraction. Contains data and maintains view state.
 *
 * @class    WC_CP_Component
 * @version  8.1.0
 */
class WC_CP_Component implements ArrayAccess {

	/**
	 * The view state of the component.
	 *
	 * @var WC_CP_Component_View
	 */
	public $view;

	/**
	 * The component ID.
	 *
	 * @var string
	 */
	private $id;

	/**
	 * The component data.
	 *
	 * @var array
	 */
	private $data = array();

	/**
	 * The composite product that the component belongs to.
	 *
	 * @var WC_Product_Composite
	 */
	private $composite;

	/**
	 * Constructor.
	 *
	 * @param  WC_Product_Composite  $composite
	 */
	public function __construct( $id, $composite ) {

		$this->id        = strval( $id );
		$this->composite = $composite;
		$this->view      = new WC_CP_Component_View( $this );

		$data = $composite->get_component_meta( $this->id );

		$data[ 'component_id' ] = $this->id;
		$data[ 'composite_id' ] = $this->get_composite_id();

		if ( ! isset( $data[ 'shipped_individually' ] ) ) {
			$data[ 'shipped_individually' ] = 'no';
		}

		if ( ! isset( $data[ 'priced_individually' ] ) ) {
			$data[ 'priced_individually' ] = 'no';
		}

		if ( ! isset( $data[ 'optional' ] ) ) {
			$data[ 'optional' ] = 'no';
		}

		if ( ! isset( $data[ 'display_prices' ] ) || ! in_array( $data[ 'display_prices' ], wp_list_pluck( self::get_price_display_options(), 'id' ) ) ) {
			$data[ 'display_prices' ] = 'absolute';
		}

		if ( ! isset( $data[ 'pagination_style' ] ) || ! in_array( $data[ 'pagination_style' ], wp_list_pluck( self::get_pagination_style_options(), 'id' ) ) ) {
			$data[ 'pagination_style' ] = 'classic';
		}

		if ( ! isset( $data[ 'select_action' ] ) || ! in_array( $data[ 'select_action' ], wp_list_pluck( self::get_select_action_options(), 'id' ) ) ) {
			$data[ 'select_action' ] = 'view';
		}

		if ( is_array( $data ) ) {

			/**
			 * Filter the raw metadata of a single component.
			 *
			 * @param  array                 $component_meta
			 * @param  string                $component_id
			 * @param  WC_Product_Composite  $product
			 */
			$this->data = apply_filters( 'woocommerce_composite_component_data', $data, $this->id, $composite );
		}
	}

	/**
	 * Composite product getter.
	 *
	 * @return WC_Product_Composite
	 */
	public function get_composite() {
		return $this->composite;
	}

	/**
	 * Composite product getter.
	 *
	 * @return WC_Product_Composite
	 */
	public function get_composite_id() {
		return $this->composite->get_id();
	}

	/**
	 * Component ID getter.
	 *
	 * @return string
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Component data getter.
	 *
	 * @return array
	 */
	public function get_data() {
		return $this->data;
	}

	/**
	 * Component options getter. Returns all product IDs added in this component.
	 *
	 * @return array
	 */
	public function get_options() {
		if ( ! isset( $this->options ) ) {
			$this->options = array_map( 'absint', self::query_component_options( $this->get_data() ) );
		}
		return $this->options;
	}

	/**
	 * Get the component title.
	 *
	 * @return string
	 */
	public function get_title() {
		$data  = $this->get_data();
		$title = '';
		if ( ! empty( $data[ 'title' ] ) ) {
			$title = apply_filters( 'woocommerce_composite_component_title', esc_html( $data[ 'title' ] ), $this->id, $this->get_composite_id() );
		}
		return $title;
	}

	/**
	 * Get the component slug.
	 *
	 * @since  4.0.0
	 *
	 * @return string
	 */
	public function get_slug() {
		$slugs = $this->get_composite()->get_component_slugs();
		return $slugs[ $this->get_id() ];
	}

	/**
	 * Get the component description.
	 *
	 * @return string
	 */
	public function get_description() {
		$data        = $this->get_data();
		$description = '';
		if ( ! empty( $data[ 'description' ] ) ) {
			$description = apply_filters( 'woocommerce_composite_component_description', wpautop( do_shortcode( wp_kses_post( $data[ 'description' ] ) ) ), $this->id, $this->get_composite_id() );
		}
		return $description;
	}

	/**
	 * Get the component discount, if applicable.
	 *
	 * @return boolean
	 */
	public function get_discount() {
		$data = $this->get_data();
		return apply_filters( 'woocommerce_composite_component_discount', ! empty( $data[ 'discount' ] ) ? floatval( $data[ 'discount' ] ) : '', $this );
	}

	/**
	 * Get the component min/max quantity.
	 *
	 * @param  string  $min_or_max
	 * @return boolean
	 */
	public function get_quantity( $min_or_max ) {

		$data = $this->get_data();
		$qty  = $qty_min = isset( $data[ 'quantity_min' ] ) ? $data[ 'quantity_min' ] : 1;

		if ( 'max' === $min_or_max ) {
			if ( isset( $data[ 'quantity_max' ] ) ) {
				$qty = $data[ 'quantity_max' ] !== '' ? max( $data[ 'quantity_max' ], $qty_min ) : '';
			}
		}

		return $qty !== '' ? absint( $qty ) : '';
	}

	/**
	 * True if the component has only one option and is not optional.
	 *
	 * @return boolean
	 */
	public function is_static() {
		return count( $this->get_options() ) === 1 && ! $this->is_optional();
	}

	/**
	 * True if the component is optional.
	 *
	 * @return boolean
	 */
	public function is_optional() {
		$data = $this->get_data();
		return 'yes' === $data[ 'optional' ];
	}

	/**
	 * True if the component is priced individually.
	 *
	 * @return boolean
	 */
	public function is_priced_individually() {
		$data = $this->get_data();
		return 'yes' === $data[ 'priced_individually' ];
	}

	/**
	 * True if the component is shipped individually.
	 *
	 * @return boolean
	 */
	public function is_shipped_individually() {
		$data = $this->get_data();

		$is_shipped_individually = 'yes' === $data[ 'shipped_individually' ];

		if ( ! is_null( $this->get_composite() ) && $this->get_composite()->is_virtual() ) {
			$is_shipped_individually = true;
		}

		return $is_shipped_individually;
	}

	/**
	 * Controls whether the options of this component will be lazy loaded.
	 *
	 * @since  7.0.0
	 * @return boolean
	 */
	public function is_lazy_loaded() {

		$lazy_load  = false;
		$components = $this->composite->get_components();
		$has_steps  = in_array( $this->composite->get_composite_layout_style(), array( 'paged', 'progressive' ) );

		if ( $has_steps ) {
			// Componentized, or Stepped and this component is not the first?
			if ( 'componentized' === $this->composite->get_composite_layout_style_variation() || $this->get_index() > 0 ) {

				// Check total number of options.
				if ( ! isset( $this->composite->options_count ) ) {

					$this->composite->options_count = 0;
					foreach ( $components as $component ) {
						$this->composite->options_count += count( $component->get_options() );
					}
				}

				// Lazy load, unless the total number of options in the Composite is lte 24.
				$lazy_load = $this->composite->options_count > 24;
			}
		}

		if ( ! $lazy_load ) {
			// Always lazy-load non-paginated option view styles if the component has > 24 options.
			if ( in_array( $this->get_options_style(), array( 'dropdowns', 'radios' ) ) && count( $this->get_options() ) > 24 ) {
				$lazy_load = true;
			}
		}

		/**
		 * Lazy-load component options?
		 *
		 * @param  boolean          $lazy_load
		 * @param  WC_CP_Component  $this
		 */
		return apply_filters( 'woocommerce_composite_component_lazy_load', $lazy_load, $this );
	}

	/**
	 * Get the default option/product ID.
	 *
	 * @param  bool  $check_posted
	 * @return int|''
	 */
	public function get_default_option( $check_posted = false ) {

		$data            = $this->get_data();
		$options         = $this->get_options();
		$selected_option = null;

		if ( $check_posted && isset( $_REQUEST[ 'wccp_component_selection' ][ $this->get_id() ] ) ) {
			if ( in_array( absint( $_REQUEST[ 'wccp_component_selection' ][ $this->get_id() ] ), $options ) ) {
				$selected_option = absint( $_REQUEST[ 'wccp_component_selection' ][ $this->get_id() ] );
			} elseif ( '' === $_REQUEST[ 'wccp_component_selection' ][ $this->get_id() ] ) {
				// Component optional?
				if ( $this->is_optional() ) {
					$selected_option = '';
				// Allow empty selection in composite edit/configure order forms, even if the component is static.
				} elseif ( ! empty( $_REQUEST[ 'action' ] ) && $_REQUEST[ 'action' ] === 'woocommerce_configure_composite_order_item' ) {
					$selected_option = '';
				}
			}
		}

		if ( is_null( $selected_option ) ) {
			if ( $this->is_static() ) {
				$selected_option = $options[0];
			} elseif ( isset( $data[ 'default_id' ] ) && in_array( $data[ 'default_id' ], $options ) ) {
				$selected_option = $data[ 'default_id' ];
			} else {
				$selected_option = '';
			}

			if ( '' === $selected_option && false === $this->is_optional() && 'defaults' === $this->get_composite()->get_shop_price_calc() && ! empty( $options ) ) {
				$selected_option = $options[0];
			}
		}

		/**
		 * Filter the default selection.
		 *
		 * @param  string                $selected_product_id
		 * @param  string                $component_id
		 * @param  WC_Product_Composite  $product
		 */
		return apply_filters( 'woocommerce_composite_component_default_option', $selected_option, $this->get_id(), $this->get_composite() );
	}

	/**
	 * Create a product wrapper object from an option/product ID.
	 *
	 * @param  int  $product_id
	 * @return WC_CP_Product|false
	 */
	public function get_option( $product_id ) {

		$option = false;

		$product_id = absint( $product_id );

		if ( $product_id > 0 ) {
			if ( isset( $this->products[ $product_id ] ) ) {
				$option = $this->products[ $product_id ];
			} else {
				$option_obj = new WC_CP_Product( $product_id, $this->id, $this->composite );
				if ( $option_obj->exists() ) {
					$this->products[ $product_id ] = $option = $option_obj;
				}
			}
		}

		/**
		 * Filter the returned object.
		 *
		 * @param  WC_CP_Product         $option
		 * @param  string                $component_id
		 * @param  WC_Product_Composite  $product
		 */
		return apply_filters( 'woocommerce_composite_component_option', $option, $this->get_id(), $this->get_composite() );
	}

	/**
	 * True if add-ons are disabled in this component.
	 *
	 * @return boolean
	 */
	public function disable_addons() {

		$data = $this->get_data();

		if ( ! defined( 'WC_PRODUCT_ADDONS_VERSION' ) || version_compare( WC_PRODUCT_ADDONS_VERSION, WC_CP()->compatibility->get_required_module_version( 'pao' ) ) < 0 ) {
			$data[ 'disable_addons' ] = 'yes';
		}

		return isset( $data[ 'disable_addons' ] ) && 'yes' === $data[ 'disable_addons' ];
	}

	/**
	 * Get the default method to order the options of the component.
	 *
	 * @return string
	 */
	public function get_default_sorting_order() {

		/**
		 * Filter the default order-by method.
		 *
		 * @param  string                $order_by_id
		 * @param  string                $component_id
		 * @param  WC_Product_Composite  $product
		 */
		return apply_filters( 'woocommerce_composite_component_default_orderby', 'default', $this->id, $this->composite );
	}

	/**
	 * Indicates whether component sorting options are enabled.
	 *
	 * @since  3.14.0
	 *
	 * @return array
	 */
	public function show_sorting_options() {
		$data = $this->get_data();
		return isset( $data[ 'show_orderby' ] ) && 'yes' === $data[ 'show_orderby' ] && ! $this->is_static();
	}

	/**
	 * Get component sorting options, if enabled.
	 *
	 * @return array
	 */
	public function get_sorting_options() {

		$data = $this->get_data();

		if ( $this->show_sorting_options() ) {

			$default_orderby      = $this->get_default_sorting_order();
			$show_default_orderby = 'default' === $default_orderby;

			/**
			 * Filter the available sorting drowdown options.
			 *
			 * @param  array                 $order_by_data
			 * @param  string                $component_id
			 * @param  WC_Product_Composite  $product
			 */
			$orderby_options = apply_filters( 'woocommerce_composite_component_orderby', array(
				'default'    => __( 'Default sorting', 'woocommerce' ),
				'popularity' => __( 'Sort by popularity', 'woocommerce' ),
				'rating'     => __( 'Sort by average rating', 'woocommerce' ),
				'date'       => __( 'Sort by newness', 'woocommerce' ),
				'price'      => __( 'Sort by price: low to high', 'woocommerce' ),
				'price-desc' => __( 'Sort by price: high to low', 'woocommerce' )
			), $this->id, $this->composite );

			if ( ! $show_default_orderby ) {
				unset( $orderby_options[ 'default' ] );
			}

			if ( 'no' === get_option( 'woocommerce_enable_review_rating' ) ) {
				unset( $orderby_options[ 'rating' ] );
			}

			if ( ! $this->is_priced_individually() ) {
				unset( $orderby_options[ 'price' ] );
				unset( $orderby_options[ 'price-desc' ] );
			}

			return $orderby_options;
		}

		return false;
	}

	/**
	 * Indicates whether component filtering options are enabled.
	 *
	 * @since  3.14.0
	 *
	 * @return array
	 */
	public function show_filtering_options() {
		$data = $this->get_data();
		return isset( $data[ 'show_filters' ] ) && 'yes' === $data[ 'show_filters' ] && ! $this->is_static();
	}

	/**
	 * Returns all taxonomy IDs used to populate attribute filters.
	 *
	 * @since  3.14.0
	 *
	 * @return array
	 */
	public function get_attribute_filters() {
		$data = $this->get_data();
		return ! empty( $data[ 'attribute_filters' ] ) && is_array( $data[ 'attribute_filters' ] ) ? array_map( 'absint', $data[ 'attribute_filters' ] ) : array();
	}

	/**
	 * Get component filtering options, if enabled.
	 *
	 * @return array
	 */
	public function get_filtering_options() {

		global $wc_product_attributes;

		$data = $this->get_data();

		if ( $this->show_filtering_options() ) {

			$filters           = array();
			$attribute_filters = $this->get_attribute_filters();

			if ( ! empty( $attribute_filters ) ) {

				$tax_filters = array();

				foreach ( $wc_product_attributes as $attribute_taxonomy_name => $attribute_data ) {

					if ( in_array( $attribute_data->attribute_id, $data[ 'attribute_filters' ] ) && taxonomy_exists( $attribute_taxonomy_name ) ) {

						$orderby = $attribute_data->attribute_orderby;

						switch ( $orderby ) {
							case 'name' :
								$args = array( 'orderby' => 'name', 'hide_empty' => false, 'menu_order' => false );
							break;
							case 'id' :
								$args = array( 'orderby' => 'id', 'order' => 'ASC', 'menu_order' => false, 'hide_empty' => false );
							break;
							case 'menu_order' :
								$args = array( 'menu_order' => 'ASC', 'hide_empty' => false );
							break;
						}

						/**
						 * Filter the component attribute filters query arguments.
						 *
						 * @param  array            $args
						 * @param  array            $attribute
						 * @param  WC_CP_Component  $component
						 */
						$taxonomy_terms = get_terms( $attribute_taxonomy_name, apply_filters( 'woocommerce_composite_component_filter_attributes_args', $args, $attribute_data, $this ) );

						if ( $taxonomy_terms ) {

							switch ( $orderby ) {
								case 'name_num' :
									usort( $taxonomy_terms, '_wc_get_product_terms_name_num_usort_callback' );
								break;
								case 'parent' :
									usort( $taxonomy_terms, '_wc_get_product_terms_parent_usort_callback' );
								break;
							}

							// Add to array
							$filter_options = array();

							foreach ( $taxonomy_terms as $term ) {
								$filter_options[ $term->term_id ] = $term->name;
							}

							// Default filter format
							$filter_data = array(
								'filter_type'         => 'attribute_filter',
								'filter_id'           => $attribute_taxonomy_name,
								'filter_name'         => $attribute_data->attribute_label,
								'filter_options'      => $filter_options,
								'filter_toggle_state' => 'closed',
								'is_multiselect'      => true
							);

							$tax_filters[ $attribute_data->attribute_id ] = $filter_data;
						}
					}
				}

				foreach ( $attribute_filters as $tax_id ) {
					foreach ( $tax_filters as $tax_filter_id => $tax_filter_data ) {
						if ( $tax_filter_id === $tax_id ) {
							$filters[] = $tax_filter_data;
						}
					}
				}
			}

			/**
			 * Filter the active filters data.
			 *
			 * @param  array                 $filters
			 * @param  string                $component_id
			 * @param  WC_Product_Composite  $product
			 */
			$component_filtering_options = apply_filters( 'woocommerce_composite_component_filters', $filters, $this->id, $this->composite );

			if ( ! empty( $component_filtering_options ) ) {
				return $component_filtering_options;
			}
		}

		return false;
	}

	/*
	|--------------------------------------------------------------------------
	| Templating methods.
	|--------------------------------------------------------------------------
	*/

	/**
	 * The index of this component.
	 *
	 * @since  7.0.0
	 *
	 * @return int
	 */
	public function get_index() {

		$components = $this->composite->get_components();

		if ( ! isset( $this->index ) ) {
			 $this->index = array_search( $this->id, array_keys( $components ) );
		}

		return $this->index;
	}

	/**
	 * Select action.
	 *
	 * @since  4.0.0
	 *
	 * @return string
	 */
	public function get_select_action() {
		$data = $this->get_data();
		return $data[ 'select_action' ];
	}

	/**
	 * Indicates whether to show an empty placeholder dropdown option. By default a placeholder is displayed when the component has no default option.
	 *
	 * @return boolean
	 */
	public function show_placeholder_option() {

		$data             = $this->get_data();
		$show_placeholder = ! isset( $data[ 'default_id' ] ) || ! in_array( $data[ 'default_id' ], $this->get_options() );

		/**
		 * @param  string                $show_placeholder
		 * @param  string                $component_id
		 * @param  WC_Product_Composite  $product
		 */
		return apply_filters( 'woocommerce_composite_component_show_placeholder_option', $show_placeholder, $this->get_id(), $this->get_composite() );
	}

	/**
	 * Component options selection style.
	 *
	 * @return string
	 */
	public function get_options_style() {

		$data = $this->get_data();

		if ( isset( $data[ 'selection_mode' ] ) ) {
			$options_style = $data[ 'selection_mode' ];
		} elseif ( $this->composite->meta_exists( '_bto_selection_mode' ) ) {
			$options_style = $this->composite->get_meta( '_bto_selection_mode', true );
		} else {
			$options_style = 'dropdowns';
		}

		if ( false === self::get_options_style_data( $options_style ) || $this->is_static() ) {
			$options_style = 'dropdowns';
		}

		return apply_filters( 'woocommerce_composite_component_options_style', $options_style, $this );
	}

	/**
	 * Thumbnail loop columns count.
	 *
	 * @return int
	 */
	public function get_columns() {

		/**
		 * Filter count of thumbnail loop columns.
		 *
		 * @param  int                   $columns_count
		 * @param  string                $component_id
		 * @param  WC_Product_Composite  $product
		 */
		return apply_filters( 'woocommerce_composite_component_loop_columns', 3, $this->id, $this->composite );
	}

	/**
	 * Thumbnail loop results per page.
	 *
	 * @return int
	 */
	public function get_results_per_page() {

		$thumbnail_columns = $this->get_columns();
		$layout            = $this->get_composite()->get_layout();
		$rpp               = in_array( $layout, array( 'single', 'progressive' ) ) ? $thumbnail_columns : $thumbnail_columns * 3;

		/**
		 * Filter count of thumbnails loop items per page.
		 * By default displays 3 rows of options in paged layouts, and 1 row in non-paged.
		 *
		 * @param  int                   $per_page_count
		 * @param  string                $component_id
		 * @param  WC_Product_Composite  $product
		 */
		return apply_filters( 'woocommerce_component_options_per_page', $rpp, $this->id, $this->composite );
	}

	/**
	 * Options pagination style.
	 *
	 * @since  3.12.0
	 *
	 * @return string
	 */
	public function get_pagination_style() {
		$data = $this->get_data();
		return $data[ 'pagination_style' ];
	}

	/**
	 * Controls whether component options loaded via ajax will be appended or paginated.
	 * When incompatible component options are set to be hidden, pagination cannot be used for simplicity.
	 *
	 * @return boolean
	 */
	public function paginate_options() {

		$options_style = $this->get_options_style();

		if ( self::options_style_supports( $options_style, 'pagination' ) ) {

			/**
			 * Last chance to disable pagination and show a "Load More" button instead.
			 *
			 * @param  boolean               $paginate
			 * @param  string                $component_id
			 * @param  WC_Product_Composite  $product
			 */
			$paginate = apply_filters( 'woocommerce_component_options_paginate_results', 'classic' === $this->get_pagination_style(), $this->id, $this->composite );

		} else {
			$paginate = false;
		}

		return $paginate;
	}

	/**
	 * Controls whether out of stock component options should be hidden.
	 *
	 * @since  8.0.3
	 *
	 * @return boolean
	 */
	public function exclude_out_of_stock_options() {
		return apply_filters( 'woocommerce_component_options_exclude_out_of_stock', 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ), $this );
	}

	/**
	 * Component pagination data.
	 *
	 * @return array
	 */
	public function get_pagination_data() {

		$paginate_options = $this->paginate_options();

		/**
		 * Filter component details relocation mode.
		 *
		 * - 'adaptive': The element containing the current selection details is relocated below its thumbnail if we've appended some results.
		 * - 'forced':   The element containing the current selection details is always relocated.
		 * - 'off':      The element containing the current selection details is never relocated.
		 *
		 * @since  3.12.0
		 *
		 * @param  string           $mode
		 * @param  WC_CP_Component  $component
		 */
		$relocation_mode = $paginate_options ? '' : apply_filters( 'woocommerce_component_option_details_relocation_mode', 'forced', $this );

		/**
		 * Pagination template -- number of elements left/right to current.
		 *
		 * @param  int                   $range
		 * @param  string                $component_id
		 * @param  WC_Product_Composite  $product
		 */
		$pagination_range = $paginate_options ? apply_filters( 'woocommerce_component_options_pagination_range', 3, $this->id, $this->composite ) : '';

		/**
		 * Pagination template -- number of elements at start/end.
		 *
		 * @param  int                   $range
		 * @param  string                $component_id
		 * @param  WC_Product_Composite  $product
		 */
		$pagination_range_end = $paginate_options ? apply_filters( 'woocommerce_component_options_pagination_range_end', 1, $this->id, $this->composite ) : '';

		return array(
			'results_per_page'     => $this->get_results_per_page(),
			'max_results'          => sizeof( $this->get_options() ),
			'append_results'       => $this->paginate_options() ? 'no' : 'yes',
			'relocation_mode'      => $relocation_mode,
			'pagination_range'     => $pagination_range,
			'pagination_range_end' => $pagination_range_end
		);
	}

	/**
	 * Controls whether disabled component options will be hidden instead of greyed-out.
	 *
	 * @return boolean
	 */
	public function hide_disabled_options() {

		/**
		 * Controls whether disabled component options will be hidden or greyed out.
		 *
		 * @param  boolean               $paginate
		 * @param  string                $component_id
		 * @param  WC_Product_Composite  $product
		 */
		return apply_filters( 'woocommerce_component_options_hide_incompatible', true, $this->id, $this->composite );
	}

	/**
	 * Get component placeholder image data.
	 *
	 * @return array
	 */
	public function get_image_data() {

		$data = $this->get_data();

		if ( ! $data ) {
			return '';
		}

		$image_src    = '';
		$image_srcset = '';
		$image_sizes  = '';

		if ( ! empty( $data[ 'thumbnail_id' ] ) ) {
			$image_size     = $this->get_image_size();
			$attachment_id  = $data[ 'thumbnail_id' ];
			$image_src_data = wp_get_attachment_image_src( $attachment_id, $image_size );
			$image_src      = $image_src_data ? current( $image_src_data ) : '';
			$image_srcset   = $image_src_data && function_exists( 'wp_get_attachment_image_srcset' ) ? wp_get_attachment_image_srcset( $attachment_id, $image_size ) : '';
			$image_sizes    = $image_src_data && function_exists( 'wp_get_attachment_image_sizes' ) ? wp_get_attachment_image_sizes( $attachment_id, $image_size ) : '';
			$image_srcset   = $image_srcset ? $image_srcset : '';
			$image_sizes    = $image_sizes ? $image_sizes : '';
		}

		return array(
			'image_src'    => $image_src,
			'image_srcset' => $image_srcset,
			'image_sizes'  => $image_sizes,
			'image_title'  => $this->get_title()
		);
	}

	/**
	 * Image size to use in Thumbnail grid and Summary template.
	 *
	 * @since  3.13.7
	 * @return string
	 */
	public function get_image_size() {
		return apply_filters( 'woocommerce_composite_component_image_size', WC_CP_Core_Compatibility::is_wc_version_gte( '3.3' ) ? 'woocommerce_thumbnail' : 'shop_catalog', $this );
	}

	/**
	 * Create an array of classes to use in the component layout templates.
	 *
	 * @return array
	 */
	public function get_classes() {

		$classes    = array();
		$layout     = $this->composite->get_composite_layout_style();
		$components = $this->composite->get_components();
		$data       = $this->get_data();
		$style      = $this->get_options_style();

		/**
		 * Filter component "toggle box" view, by default enabled when using the "Progressive" layout.
		 *
		 * @param  boolean               $is_toggled
		 * @param  string                $component_id
		 * @param  WC_Product_Composite  $product
		 */
		$toggled    = 'paged' === $layout ? false : apply_filters( 'woocommerce_composite_component_toggled', 'progressive' === $layout, $this->id, $this->composite );

		$classes[]  = 'composite_component component';
		$classes[]  = $layout;
		$classes[]  = 'options-style-' . $style;

		if ( self::options_style_supports( $style, 'pagination' ) ) {
			if ( $this->paginate_options() ) {
				$classes[] = 'paginate-results';
			} else {
				$classes[] = 'append-results';
			}
		}

		// Hide incompatible products/variations?
		if ( $this->hide_disabled_options() ) {
			$classes[] = 'hide-incompatible-products';
			$classes[] = 'hide-incompatible-variations';
		}

		if ( 'paged' === $layout ) {

			$classes[] = 'multistep';

		} elseif ( 'progressive' === $layout ) {

			$classes[] = 'multistep';
			$classes[] = 'autoscrolled';

			/*
			 * To leave open in blocked state, for instance when displaying options as thumbnails, use:
			 *
			 * if ( $toggled && $style === 'thumbnails' ) {
			 *     $classes[] = 'block-open';
			 * }
			 */
		}

		// Center-align selected product markup?
		if ( 'thumbnails' === $this->get_options_style() ) {
			if ( 'paged' === $layout ) {
				if ( 'load-more' === $this->get_pagination_style() ) {
					// $classes[] = 'relocated-selection-align--center';
				}
			}
		}

		if ( $toggled ) {
			$classes[] = 'toggled';
		}

		if ( 0 === $this->get_index() ) {

			$classes[] = 'active';
			$classes[] = 'first';

			if ( $toggled ) {
				$classes[] = 'open';
			}

		} else {

			if ( 'progressive' === $layout ) {
				$classes[] = 'blocked';
			}

			if ( $toggled ) {
				$classes[] = 'closed';
			}
		}

		if ( $this->get_index() === count( $components ) - 1 ) {
			$classes[] = 'last';
		}

		if ( $this->is_static() ) {
			$classes[] = 'static';
		}

		if ( $this->is_lazy_loaded() ) {
			$classes[] = 'lazy-load';
		}

		$hide_product_thumbnail = isset( $data[ 'hide_product_thumbnail' ] ) ? $data[ 'hide_product_thumbnail' ] : 'no';

		if ( 'yes' === $hide_product_thumbnail ) {
			$classes[] = 'selection_thumbnail_hidden';
		}

		if ( 'transition' === $this->get_select_action() ) {
			$classes[] = 'autotransition';
		}

		/**
		 * Filter component classes. Used for JS app initialization.
		 *
		 * @param  array                 $classes
		 * @param  string                $component_id
		 * @param  WC_Product_Composite  $product
		 */
		return apply_filters( 'woocommerce_composite_component_classes', $classes, $this->id, $this->composite );
	}

	/**
	 * True if the selected option title is hidden.
	 *
	 * @return boolean
	 */
	public function hide_selected_option_title() {
		$data = $this->get_data();
		return isset( $data[ 'hide_product_title' ] ) && 'yes' === $data[ 'hide_product_title' ];
	}

	/**
	 * True if the selected option description is hidden.
	 *
	 * @return boolean
	 */
	public function hide_selected_option_description() {
		$data = $this->get_data();
		return isset( $data[ 'hide_product_description' ] ) && 'yes' === $data[ 'hide_product_description' ];
	}

	/**
	 * True if the selected option thumbnail is hidden.
	 *
	 * @return boolean
	 */
	public function hide_selected_option_thumbnail() {
		$data = $this->get_data();
		return isset( $data[ 'hide_product_thumbnail' ] ) && 'yes' === $data[ 'hide_product_thumbnail' ];
	}

	/**
	 * True if the selected option thumbnail is hidden.
	 *
	 * @return boolean
	 */
	public function hide_selected_option_price() {
		$data = $this->get_data();
		return isset( $data[ 'hide_product_price' ] ) && 'yes' === $data[ 'hide_product_price' ];
	}

	/**
	 * True if component option prices need to be hidden.
	 *
	 * @return boolean
	 */
	public function hide_component_option_prices() {
		return apply_filters( 'woocommerce_composite_component_option_prices_hide', 'hidden' === $this->get_price_display_format(), $this );
	}

	/**
	 * Subtotal visibility in the product/cart/order templates.
	 *
	 * @return boolean
	 */
	public function is_subtotal_visible( $where = 'product' ) {
		$data = $this->get_data();
		return false === isset( $data[ 'hide_subtotal_' . $where ] ) || 'no' === $data[ 'hide_subtotal_' . $where ];
	}

	/**
	 * Price display format.
	 *
	 * @since  3.12.0
	 *
	 * @return string
	 */
	public function get_price_display_format() {
		$data = $this->get_data();
		return $data[ 'display_prices' ];
	}

	/**
	 * Price display settings.
	 *
	 * @since  5.0.0
	 *
	 * @return string
	 */
	public function get_price_display_settings() {

		$data     = $this->get_data();
		$settings = array(
			'format' => $data[ 'display_prices' ]
		);

		if ( 'relative' === $settings[ 'format' ] ) {
			$settings[ 'is_relative_to_default' ]   = 'yes';
			$settings[ 'show_absolute_if_invalid' ] = 'yes';
		}

		return $settings;
	}

	/*
	|--------------------------------------------------------------------------
	| Array access methods for back-compat in templates.
	|--------------------------------------------------------------------------
	*/

	public function offsetGet( $offset ) {
		return isset( $this->data[ $offset ] ) ? $this->data[ $offset ] : null;
	}

	public function offsetExists( $offset ) {
		return isset( $this->data[ $offset ] );
	}

	public function offsetSet( $offset, $value ) {
		if ( is_null( $offset ) ) {
			$this->data[] = $value;
		} else {
			$this->data[ $offset ] = $value;
		}
	}

	public function offsetUnset( $offset ) {
		unset( $this->data[ $offset ] );
	}

	/*
	|--------------------------------------------------------------------------
	| Static API methods.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Fetches component options. The query is configured based on the data stored in the 'component_data' array.
	 * Note that the query parameters are filterable - @see 'WC_CP_Query' class for details.
	 *
	 * @param  array  $component_data
	 * @param  array  $query_args
	 * @return array
	 */
	public static function query_component_options( $component_data, $query_args = array() ) {

		$query = new WC_CP_Query( $component_data, $query_args );

		return $query->get_component_options();
	}

	/**
	 * Get composite selection styles.
	 *
	 * @return array
	 */
	public static function get_options_styles() {

		$styles = array(
			array(
				'id'          => 'dropdowns',
				'title' => __( 'Dropdown', 'woocommerce-composite-products' ),
				'description' => __( 'Component Options are listed in a dropdown menu.', 'woocommerce-composite-products' ),
				'supports'    => array()
			),
			array(
				'id'          => 'thumbnails',
				'title'       => __( 'Thumbnails', 'woocommerce-composite-products' ),
				'description' => __( 'Component Options are displayed as thumbnails, paginated and arranged in columns similar to the main shop loop.', 'woocommerce-composite-products' ),
				'supports'    => array( 'pagination' )
			),
			array(
				'id'          => 'radios',
				'title'       => __( 'Radio Buttons', 'woocommerce-composite-products' ),
				'description' => __( 'Component Options are displayed as radio buttons.', 'woocommerce-composite-products' ),
				'supports'    => array()
			)
		);

		/**
		 * Filter the selection styles array to add custom styles or modify the supported features.
		 *
		 * @param  array  $styles
		 */
		return apply_filters( 'woocommerce_composite_product_options_styles', $styles );
	}

	/**
	 * Get composite selection style data.
	 *
	 * @param  string  $style_id
	 * @return array|false
	 */
	public static function get_options_style_data( $style_id ) {

		$styles = self::get_options_styles();
		$found  = false;

		foreach ( $styles as $style ) {
			if ( $style[ 'id' ] ===  $style_id ) {
				$found = $style;
				break;
			}
		}

		return $found;
	}

	/**
	 * True if a selection style supports a given functionality.
	 *
	 * @param  string  $style_id
	 * @param  string  $what
	 * @return bool
	 */
	public static function options_style_supports( $style_id, $what ) {

		$options_style_data = self::get_options_style_data( $style_id );
		$supports           = false;

		if ( $options_style_data && isset( $options_style_data[ 'supports' ] ) && is_array( $options_style_data[ 'supports' ] ) && in_array( $what, $options_style_data[ 'supports' ] ) ) {
			$supports = true;
		}

		return $supports;
	}

	/**
	 * Set/upload component thumbnail.
	 *
	 * @since  3.11.0
	 *
	 * @param  int                   $thumbnail_id
	 * @param  string                $thumbnail_src
	 * @param  WC_Product_Composite  $product
	 * @return integer|false
	 */
	public static function set_thumbnail( $thumbnail_id, $thumbnail_src, $product ) {

		if ( ! $thumbnail_id && $thumbnail_src ) {

			if ( stristr( $thumbnail_src, '://' ) ) {
				$thumbnail_src = esc_url_raw( $thumbnail_src );
			} else {
				$thumbnail_src = sanitize_file_name( $thumbnail_src );
			}

			$upload = wc_rest_upload_image_from_url( $thumbnail_src );

			if ( is_wp_error( $upload ) ) {
				return false;
			}

			$thumbnail_id = wc_rest_set_uploaded_image_as_attachment( $upload, $product->get_id() );
		}

		if ( ! wp_attachment_is_image( $thumbnail_id ) ) {
			return false;
		}

		return $thumbnail_id;
	}

	/**
	 * "Display Price" options:
	 *
	 * - Absolute
	 * - Relative
	 * - Hidden
	 *
	 * @since  3.12.0
	 *
	 * @return array
	 */
	public static function get_price_display_options() {
		return array(
			array(
				'id'          => 'absolute',
				'title'       => __( 'Absolute', 'woocommerce-composite-products' ),
				'description' => sprintf( __( 'Display absolute Component Option prices, e.g. </br>%s.', 'woocommerce-composite-products' ), wc_price( 100 ) )
			),
			array(
				'id'          => 'relative',
				'title'       => __( 'Relative', 'woocommerce-composite-products' ),
				'description' => sprintf( __( 'Display Component Option prices relative to the price of the Default Option, e.g. </br>+%1$s or -%2$s.', 'woocommerce-composite-products' ), wc_price( 100 ), wc_price( 50 ) )
			),
			array(
				'id'          => 'hidden',
				'title'       => __( 'Hidden', 'woocommerce-composite-products' ),
				'description' => __( 'Hide Component Option prices.', 'woocommerce-composite-products' )
			)
		);
	}

	/**
	 * "Pagination Style" options:
	 *
	 * - Absolute
	 * - Relative
	 * - Hidden
	 *
	 * @since  3.12.0
	 *
	 * @return array
	 */
	public static function get_pagination_style_options() {
		return array(
			array(
				'id'          => 'classic',
				'title'       => __( 'Classic', 'woocommerce-composite-products' ),
				'description' => __( 'Component Options are arranged in pages, similar to the main shop loop.', 'woocommerce-composite-products' )
			),
			array(
				'id'          => 'load-more',
				'title'       => __( 'Load more', 'woocommerce-composite-products' ),
				'description' => __( 'Component Options are appended by clicking a "Load more" button.', 'woocommerce-composite-products' )
			)
		);
	}

	/**
	 * "Option Select Action" options:
	 *
	 * - View Selection Details
	 * - View Next Component
	 *
	 * @since  4.0.0
	 *
	 * @return array
	 */
	public static function get_select_action_options() {
		return array(
			array(
				'id'          => 'view',
				'title'       => __( 'View selection details', 'woocommerce-composite-products' ),
				'description' => __( 'Display selected product details without moving to the next Component.', 'woocommerce-composite-products' )
			),
			array(
				'id'          => 'transition',
				'title'       => __( 'View next step', 'woocommerce-composite-products' ),
				'description' => __( 'View the next Component.', 'woocommerce-composite-products' )
			)
		);
	}
}
