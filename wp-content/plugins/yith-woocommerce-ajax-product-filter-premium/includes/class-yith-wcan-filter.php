<?php
/**
 * Filter object
 *
 * Offers method to read and set properties of the single filter
 * Subclasses may define methods specific to a certain type of filter, and output for the filter
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\AjaxProductFilter\Classes
 * @version 4.16.0
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAN_Filter' ) ) {
	/**
	 * Filter Handling
	 *
	 * @since 1.0.0
	 */
	abstract class YITH_WCAN_Filter {

		/**
		 * Filter id, if set (not required)
		 *
		 * @since 4.0.0
		 * @var int
		 */
		protected $id = 0;

		/**
		 * Preset id, if set (not required)
		 *
		 * @since 4.0.0
		 * @var int
		 */
		protected $preset_id = 0;

		/**
		 * Preset, if set (not required)
		 *
		 * @since 4.0.0
		 * @var YITH_WCAN_Preset
		 */
		protected $preset = null;

		/**
		 * Filter type
		 *
		 * @var string
		 */
		protected $type = 'tax';

		/**
		 * Name of the template to use for filter rendering
		 *
		 * @var string
		 */
		protected $template = '';

		/**
		 * Core data for this object. Name value pairs (name + default value).
		 *
		 * @since 4.0.0
		 * @var array
		 */
		protected $data = array();

		/**
		 * Construct generic filter class
		 *
		 * @param array $filter Array of settings for the filter, to be merged with defaults.
		 */
		public function __construct( $filter = array() ) {
			$filter = wp_parse_args(
				$filter,
				$this->get_defaults()
			);

			// use setters to configure filter parameters; this will assure data sanitization.
			foreach ( $filter as $field => $value ) {
				$method = "set_{$field}";

				if ( ! method_exists( $this, $method ) ) {
					continue;
				}

				$this->{$method}( $value );
			}
		}

		/**
		 * Return data array
		 *
		 * @return array
		 */
		public function __sleep() {
			return array( 'data' );
		}

		/**
		 * Construct the object passing stored data
		 *
		 * If the object no longer exists, remove the ID.
		 */
		public function __wakeup() {
			$this->__construct( $this->data );
		}

		/**
		 * Get filter defaults
		 *
		 * @return array Array of filter defaults.
		 */
		protected function get_defaults() {
			return apply_filters(
				'yith_wcan_filter_defaults',
				array(
					'id'                           => '',
					'preset_id'                    => '',
					'title'                        => _x( 'New filter', '[Admin] Default filter title', 'yith-woocommerce-ajax-navigation' ),
					'customize_terms'              => 'yes',
					'terms'                        => array(),
					'price_ranges'                 => array(),
					'filter_design'                => 'checkbox',
					'label_position'               => 'below',
					'column_number'                => 4,
					'show_toggle'                  => 'no',
					'show_search'                  => 'yes',
					'toggle_style'                 => 'opened',
					'order_by'                     => 'name',
					'order'                        => 'asc',
					'show_count'                   => 'no',
					'hierarchical'                 => 'no',
					'multiple'                     => 'no',
					'relation'                     => 'and',
					'adoptive'                     => 'hide',
					'enabled'                      => 'yes',
					'price_slider_design'          => 'slider',
					'price_slider_adaptive_limits' => 'no',
					'price_slider_min'             => 0,
					'price_slider_max'             => 100,
					'price_slider_step'            => 1,
					'order_options'                => array( 'menu_order' ),
					'show_stock_filter'            => 'yes',
					'show_sale_filter'             => 'yes',
					'show_featured_filter'         => 'no',
				)
			);
		}

		/* === GETTERS === */

		/**
		 * Get id of the filter
		 *
		 * @return int Id of the filter; 0 when empty.
		 */
		public function get_id() {
			return $this->id;
		}

		/**
		 * Get id of the filter
		 *
		 * @return int Id of the preset; 0 when empty.
		 */
		public function get_preset_id() {
			return $this->preset_id;
		}

		/**
		 * Get preset of the filter
		 *
		 * @return YITH_WCAN_Preset|bool Preset when set, false otherwise.
		 */
		public function get_preset() {
			if ( ! $this->get_preset_id() ) {
				return false;
			}

			if ( ! $this->preset ) {
				try {
					$this->preset = new YITH_WCAN_Preset( $this->get_preset_id() );
				} catch ( Exception $e ) {
					wc_caught_exception( $e, __FUNCTION__, func_get_args() );
					return false;
				}
			}

			return $this->preset;
		}

		/**
		 * Get title for the filter
		 *
		 * @param string $context Context of the operation.
		 * @return string Title of the filter.
		 */
		public function get_title( $context = 'view' ) {
			return $this->get_prop( 'title', $context );
		}

		/**
		 * Get type for the filter
		 *
		 * @param string $context Context of the operation.
		 * @return string Type of the filter.
		 */
		public function get_type( $context = 'view' ) {
			return $this->type;
		}

		/**
		 * Checks the product type.
		 *
		 * @param  string|array $type Array or string of types.
		 * @return bool
		 */
		public function is_type( $type ) {
			return ( $this->get_type() === $type || ( is_array( $type ) && in_array( $this->get_type(), $type, true ) ) );
		}

		/**
		 * Checks whether current filter is active
		 *
		 * @return bool Whether current filter is active.
		 */
		public function is_active() {
			return apply_filters( 'yith_wcan_is_filter_active', YITH_WCAN_Query()->is_filtered_by( $this->type ), $this );
		}

		/**
		 * Checks if filter is relevant to current product selection
		 *
		 * @return bool Whether filter is relevant or not.
		 */
		public function is_relevant() {
			return apply_filters( 'yith_wcan_is_filter_relevant', $this->is_enabled(), $this );
		}

		/**
		 * Get taxonomy for the filter
		 *
		 * @param string $context Context of the operation.
		 * @return string Taxonomy of the filter.
		 */
		public function get_taxonomy( $context = 'view' ) {
			return $this->get_prop( 'taxonomy', $context );
		}

		/**
		 * Get taxonomy for the filter
		 *
		 * @param string $context Context of the operation.
		 * @return string Taxonomy of the filter.
		 */
		public function get_formatted_taxonomy( $context = 'view' ) {
			$formatted_taxonomy = $this->get_taxonomy( $context );

			if ( in_array( $formatted_taxonomy, wc_get_attribute_taxonomy_names(), true ) ) {
				$formatted_taxonomy = str_replace( 'pa_', 'filter_', $formatted_taxonomy );
			}

			return $formatted_taxonomy;
		}

		/**
		 * Get use_all_terms property for the filter
		 *
		 * @param string $context Context of the operation.
		 * @return string Yes or no.
		 */
		public function get_use_all_terms( $context = 'view' ) {
			return $this->get_prop( 'use_all_terms', $context );
		}

		/**
		 * Check if we should use all existing terms for this filter
		 *
		 * @param string $context Context of the operation.
		 * @return bool Whether all terms should be retrieved or not.
		 */
		public function use_all_terms( $context = 'view' ) {
			return 'yes' === $this->get_use_all_terms( $context );
		}

		/**
		 * Get customize_terms property
		 *
		 * @param string $context Context of the operation.
		 * @return string Yes or no.
		 */
		public function get_customize_terms( $context = 'view' ) {
			return $this->get_prop( 'customize_terms', $context );
		}

		/**
		 * Should use custom terms options?
		 *
		 * @param string $context Context of the operation.
		 * @return bool Whether to use terms options or not.
		 */
		public function customize_terms( $context = 'view' ) {
			return apply_filters( 'yith_wcan_filter_customize_terms_options', 'yes' === $this->get_prop( 'customize_terms', $context ), $this );
		}

		/**
		 * Check whether filter has any term
		 *
		 * @param string $context Context of the operation.
		 * @return bool Whether filter has any term
		 */
		public function has_terms( $context = 'view' ) {
			return ! ! $this->get_terms( 'ids', $context );
		}

		/**
		 * Check whether filter has any term with an image
		 *
		 * @param string $context Context of the operation.
		 * @return bool Whether filter has any term with an image
		 */
		public function has_terms_with_images( $context = 'view' ) {
			$terms = $this->get_terms_options( $context );

			if ( empty( $terms ) ) {
				return false;
			}

			$has_image = false;

			foreach ( $terms as $term_options ) {
				if ( $term_options['image'] && 'image' === $term_options['mode'] ) {
					$has_image = true;
					break;
				}
			}

			return $has_image;
		}

		/**
		 * Return default term options
		 *
		 * @return array Default term options:
		 */
		public function get_default_term_options() {
			return array(
				'label'   => '',
				'tooltip' => '',
				'color_1' => '',
				'color_2' => '',
				'image'   => '',
				'mode'    => 'color',
			);
		}

		/**
		 * Get terms for the filter
		 *
		 * @param string $fields Type of item to return.
		 * @param string $context Context of the operation.
		 * @return array Array of term ids for current filter.
		 */
		public function get_terms( $fields = 'ids', $context = 'view' ) {
			$terms    = $this->get_prop( 'terms', $context );
			$term_ids = array_keys( $terms ? $terms : array() );
			$taxonomy = $this->get_taxonomy( $context );

			if ( ! $taxonomy ) {
				return array();
			}

			switch ( $fields ) {
				default:
					if ( ! $this->use_all_terms() ) {
						return $term_ids;
					}

					$fields = 'ids';

					// explicitly fallback to next cases.
				case 'all':
				case 'all_with_object_id':
				case 'tt_ids':
				case 'names':
				case 'slugs':
				case 'count':
					if ( ! $term_ids && ! ( 'view' === $context && $this->use_all_terms() ) ) {
						return array();
					}

					$terms = get_terms(
						array_merge(
							array(
								'taxonomy'   => $taxonomy,
								'fields'     => $fields,
								'hide_empty' => false,
							),
							'view' === $context && $this->use_all_terms() ? array() : array(
								'include' => $term_ids,
							)
						)
					);

					if ( is_wp_error( $terms ) ) {
						return array();
					}

					return $terms;
				case 'id=>parent':
				case 'id=>name':
				case 'id=>slug':
					if ( ! $term_ids && ! ( 'view' === $context && $this->use_all_terms() ) ) {
						return array();
					}

					$terms = get_terms(
						array_merge(
							array(
								'taxonomy'   => $taxonomy,
								'fields'     => $fields,
								'hide_empty' => false,
							),
							'view' === $context && $this->use_all_terms() ? array() : array(
								'include' => $term_ids,
							)
						)
					);

					if ( is_wp_error( $terms ) ) {
						return array();
					}

					if ( ! $this->use_all_terms() && $term_ids ) {
						$sorted_terms = array();
						foreach ( $term_ids as $term_id ) {
							if ( ! isset( $terms[ $term_id ] ) ) {
								continue;
							}

							$sorted_terms[ $term_id ] = $terms[ $term_id ];
						}

						$terms = $sorted_terms;
					}

					return $terms;
				case 'id=>options':
					if ( ! $this->use_all_terms() ) {
						return $terms;
					} else {
						$term_ids = get_terms(
							array(
								'taxonomy'   => $taxonomy,
								'fields'     => 'ids',
								'hide_empty' => false,
							)
						);

						if ( ! $term_ids || is_wp_error( $term_ids ) ) {
							return array();
						}

						$terms                = array();
						$default_term_options = $this->get_default_term_options();

						foreach ( $term_ids as $term_id ) {
							$terms[ $term_id ] = $default_term_options;
						}

						return $terms;
					}
			}
		}

		/**
		 * Returns an array of IDs for the selected terms
		 *
		 * @param string $context Context of the operation.
		 * @return array Array of term ids for current filter.
		 */
		public function get_term_ids( $context = 'view' ) {
			return $this->get_terms( 'ids', $context );
		}

		/**
		 * Get specific options for each term
		 *
		 * @param string $context Context of the operation.
		 * @return array Array term options for this filter.
		 */
		public function get_terms_options( $context = 'view' ) {
			return $this->get_prop( 'terms', $context );
		}

		/**
		 * Get design for the filter
		 *
		 * @param string $context Context of the operation.
		 * @return string Design of the filter.
		 */
		public function get_filter_design( $context = 'view' ) {
			return $this->get_prop( 'filter_design', $context );
		}

		/**
		 * Get label position
		 *
		 * @param string $context Context of the operation.
		 * @return string Position of the label (below/right/hide).
		 */
		public function get_label_position( $context = 'view' ) {
			return $this->get_prop( 'label_position', $context );
		}

		/**
		 * Return number of columns for currend design
		 *
		 * @param string $context Context of the operation.
		 * @return int NUmber of items per row.
		 */
		public function get_column_number( $context = 'view' ) {
			return (int) $this->get_prop( 'column_number', $context );
		}

		/**
		 * Should show filter as a toggle?
		 *
		 * @param string $context Context of the operation.
		 * @return string Yes or no.
		 */
		public function get_show_toggle( $context = 'view' ) {
			return $this->get_prop( 'show_toggle', $context );
		}

		/**
		 * Should show filter as a toggle?
		 *
		 * @param string $context Context of the operation.
		 * @return string Whether filter is collapsable or not.
		 */
		public function is_collapsable( $context = 'view' ) {
			return 'yes' === $this->get_prop( 'show_toggle', $context ) || 'horizontal' === $this->get_preset()->get_layout();
		}

		/**
		 * Should show search field in dropdown?
		 *
		 * @param string $context Context of the operation.
		 * @return string Yes or no.
		 */
		public function get_show_search( $context = 'view' ) {
			return $this->get_prop( 'show_search', $context );
		}

		/**
		 * Should show search field in dropdown?
		 *
		 * @param string $context Context of the operation.
		 * @return string Yes or no.
		 */
		public function is_search_enabled( $context = 'view' ) {
			return 'yes' === $this->get_prop( 'show_search', $context );
		}

		/**
		 * Get toggle style for the filter
		 *
		 * @param string $context Context of the operation.
		 * @return string Toggle style.
		 */
		public function get_toggle_style( $context = 'view' ) {
			$preset = $this->get_preset();

			if ( $preset && 'horizontal' === $preset->get_layout() ) {
				return 'closed';
			}

			return $this->get_prop( 'toggle_style', $context );
		}

		/**
		 * Get order by for the filter
		 *
		 * @param string $context Context of the operation.
		 * @return string Which field should be used to order by.
		 */
		public function get_order_by( $context = 'view' ) {
			return $this->get_prop( 'order_by', $context );
		}

		/**
		 * Get order of items in the filter
		 *
		 * @param string $context Context of the operation.
		 * @return string Asc or desc.
		 */
		public function get_order( $context = 'view' ) {
			return $this->get_prop( 'order', $context );
		}

		/**
		 * Should show count of items?
		 *
		 * @param string $context Context of the operation.
		 * @return string Yes or no.
		 */
		public function get_show_count( $context = 'view' ) {
			return $this->get_prop( 'show_count', $context );
		}

		/**
		 * Should show count of items?
		 *
		 * @param string $context Context of the operation.
		 * @return bool Whether to show item count or not.
		 */
		public function show_count( $context = 'view' ) {
			return 'yes' === $this->get_prop( 'show_count', $context );
		}

		/**
		 * Get how terms should be shown when support a hierarchy
		 *
		 * @param string $context Context of the operation.
		 * @return string Hierarchy style.
		 */
		public function get_hierarchical( $context = 'view' ) {
			return $this->get_prop( 'hierarchical', $context );
		}

		/**
		 * Checks whether current filter supports terms hierarchy
		 *
		 * @param string $context Context of the operation.
		 * @return bool Whether current filter supports terms hierarchy
		 */
		public function is_hierarchical( $context = 'view' ) {
			$hierarchical = $this->get_hierarchical( $context );

			return in_array( $hierarchical, array( 'collapsed', 'expanded', 'open' ), true );
		}

		/**
		 * Get multiple property
		 *
		 * @param string $context Context of the operation.
		 * @return string Yes or no.
		 */
		public function get_multiple( $context = 'view' ) {
			return $this->get_prop( 'multiple', $context );
		}

		/**
		 * Should allow multiple selection?
		 *
		 * @param string $context Context of the operation.
		 * @return bool Whether multiple is allowed.
		 */
		public function is_multiple_allowed( $context = 'view' ) {
			$multiple = $this->get_prop( 'multiple', $context );

			return 'yes' === $multiple && 'radio' !== $this->get_filter_design();
		}

		/**
		 * How to behave with multiple selection
		 *
		 * @param string $context Context of the operation.
		 * @return string And or or.
		 */
		public function get_relation( $context = 'view' ) {
			return $this->get_prop( 'relation', $context );
		}

		/**
		 * Get how non-pertinent terms should be handled
		 *
		 * @param string $context Context of the operation.
		 * @return string Adoptive style.
		 */
		public function get_adoptive( $context = 'view' ) {
			return $this->get_prop( 'adoptive', $context );
		}

		/**
		 * Checks whether current filter has price ranges or not
		 *
		 * @param string $context Context of the operation.
		 * @return bool Whether current filter has price ranges or not
		 */
		public function has_price_ranges( $context = 'view' ) {
			return ! ! $this->get_price_ranges( $context );
		}

		/**
		 * Retrieves price ranges for current filter
		 *
		 * @param string $context Context of the operation.
		 * @return array Array of ranges
		 */
		public function get_price_ranges( $context = 'view' ) {
			return array_values( $this->get_prop( 'price_ranges', $context ) );
		}

		/**
		 * Returns the slider design
		 *
		 * @param string $context Context of the operation.
		 * @return string Slider/Fields or both.
		 */
		public function get_price_slider_design( $context = 'view' ) {
			return $this->get_prop( 'price_slider_design', $context );
		}

		/**
		 * Get price_slider_adaptive_limits property
		 *
		 * @param string $context Context of the operation.
		 * @return string Yes or no.
		 */
		public function get_price_slider_adaptive_limits( $context = 'view' ) {
			return $this->get_prop( 'price_slider_adaptive_limits', $context );
		}

		/**
		 * Should use adaptive limits for price slider?
		 *
		 * @param string $context Context of the operation.
		 * @return string Whether to use adaptive limits for price slider or not.
		 */
		public function use_price_slider_adaptive_limits( $context = 'view' ) {
			return 'yes' === $this->get_price_slider_adaptive_limits( $context );
		}

		/**
		 * Retrieves minimum value for price slider filter
		 *
		 * @param string $context Context of the operation.
		 * @return float Minimum value for the slider
		 */
		public function get_price_slider_min( $context = 'view' ) {
			return (float) $this->get_prop( 'price_slider_min', $context );
		}

		/**
		 * Retrieves maximum value for price slider filter
		 *
		 * @param string $context Context of the operation.
		 * @return float Maximum value for the slider
		 */
		public function get_price_slider_max( $context = 'view' ) {
			return (float) $this->get_prop( 'price_slider_max', $context );
		}

		/**
		 * Retrieves increase value for price slider filter
		 *
		 * @param string $context Context of the operation.
		 * @return float Step value for the price slider
		 */
		public function get_price_slider_step( $context = 'view' ) {
			return (float) $this->get_prop( 'price_slider_step', $context );
		}

		/**
		 * Retrieve an array of sorting options for Order by filter
		 *
		 * @param string $context Context of the operation.
		 * @return array Array of sorting options
		 */
		public function get_order_options( $context = 'view' ) {
			return $this->get_prop( 'order_options', $context );
		}

		/**
		 * Get show_stock_filter property
		 *
		 * @param string $context Context of the operation.
		 * @return string Yes or no.
		 */
		public function get_show_stock_filter( $context = 'view' ) {
			return $this->get_prop( 'show_stock_filter', $context );
		}

		/**
		 * Get show_sale_filter property
		 *
		 * @param string $context Context of the operation.
		 * @return string Yes or no.
		 */
		public function get_show_sale_filter( $context = 'view' ) {
			return $this->get_prop( 'show_sale_filter', $context );
		}

		/**
		 * Get show_featured_filter property
		 *
		 * @param string $context Context of the operation.
		 * @return string Yes or no.
		 */
		public function get_show_featured_filter( $context = 'view' ) {
			return $this->get_prop( 'show_featured_filter', $context );
		}

		/**
		 * Checks whether In Stock filter needs to be shown
		 *
		 * @param string $context Context of the operation.
		 * @return bool Whether filter needs to be shown.
		 */
		public function show_stock_filter( $context = 'view' ) {
			return 'yes' === $this->get_prop( 'show_stock_filter', $context );
		}

		/**
		 * Checks whether On Sale filter needs to be shown
		 *
		 * @param string $context Context of the operation.
		 * @return bool Whether filter needs to be shown
		 */
		public function show_sale_filter( $context = 'view' ) {
			return 'yes' === $this->get_prop( 'show_sale_filter', $context );
		}

		/**
		 * Checks whether Featured filter needs to be shown
		 *
		 * @param string $context Context of the operation.
		 * @return bool Whether filter needs to be shown
		 */
		public function show_featured_filter( $context = 'view' ) {
			return 'yes' === $this->get_prop( 'show_featured_filter', $context );
		}

		/**
		 * Check if filter is enabled
		 *
		 * @param string $context Context of the operation.
		 * @return bool Preset status
		 */
		public function is_enabled( $context = 'view' ) {
			return yith_plugin_fw_is_true( $this->get_prop( 'enabled', $context ) );
		}

		/**
		 * Returns additional classes for the .yith-wcan-filter element
		 *
		 * @return string List of additional classes.
		 */
		public function get_additional_classes() {
			$additional_classes = array();

			// set type class.
			$additional_classes[] = 'filter-' . str_replace( '_', '-', $this->get_type() );

			if ( 'tax' === $this->get_type() && $this->is_hierarchical() ) {
				$additional_classes[] = 'hierarchical';
			}

			if ( 'tax' === $this->get_type() ) {
				$additional_classes[] = $this->get_filter_design() . '-design';
			}

			if ( ! $this->get_title() ) {
				$additional_classes[] = 'no-title';
			}

			$additional_classes = apply_filters( 'yith_wcan_filter_additional_classes', $additional_classes, $this );

			return implode( ' ', $additional_classes );
		}

		/**
		 * Returns additional classes for .filter-items element
		 *
		 * @return string List of additional classes.
		 */
		public function get_items_container_classes() {
			$additional_classes = array();

			if ( 'tax' === $this->get_type() ) {
				$filter_design = $this->get_filter_design();
				$filter_design = 'select' === $filter_design ? 'dropdown' : $filter_design;

				$additional_classes[] = 'filter-' . $filter_design;
				$additional_classes[] = $this->has_terms_with_images() ? 'with-images' : '';
			}

			$additional_classes = apply_filters( 'yith_wcan_filter_items_container_classes', $additional_classes, $this );

			return implode( ' ', $additional_classes );
		}

		/**
		 * Return filter data
		 *
		 * @return array Data
		 */
		public function get_data() {
			return apply_filters(
				'yith_wcan_get_filter_data',
				array_merge(
					$this->data,
					array(
						'type' => $this->type,
					)
				),
				$this
			);
		}

		/**
		 * Return an array of supported fields
		 *
		 * @return array Array of fields
		 */
		public static function get_fields() {
			return include YITH_WCAN_DIR . 'plugin-options/filter-options.php';
		}

		/**
		 * Gets a prop for a getter method.
		 *
		 * Context controls what happens to the value before it's returned.
		 *
		 * @param  string $prop Name of prop to get.
		 * @param  string $context What the value is for. Valid values are view and edit.
		 * @return mixed
		 */
		protected function get_prop( $prop, $context = 'view' ) {
			$value = null;

			if ( array_key_exists( $prop, $this->data ) ) {
				$value = $this->data[ $prop ];

				if ( 'view' === $context ) {
					$value = apply_filters( "yith_wcan_filter_get_{$prop}", $value, $this );
				}
			}

			return $value;
		}

		/* === SETTERS === */

		/**
		 * Get id of the filter
		 *
		 * @param int $id Id of the filter.
		 */
		public function set_id( $id ) {
			$this->id = $id;
		}

		/**
		 * Set id of the filter
		 *
		 * @param int $preset_id Id of the preset.
		 */
		public function set_preset_id( $preset_id ) {
			$this->preset_id = $preset_id;
		}

		/**
		 * Set title for the filter
		 *
		 * @param string $title Title of the filter.
		 */
		public function set_title( $title ) {
			$this->set_prop( 'title', $title );
		}

		/**
		 * Set taxonomy for the filter
		 *
		 * @param string $taxonomy Taxonomy of the filter.
		 */
		public function set_taxonomy( $taxonomy ) {
			if ( ! taxonomy_exists( $taxonomy ) ) {
				return;
			}

			$this->set_prop( 'taxonomy', $taxonomy );
		}

		/**
		 * Set use_all_terms property for the filter
		 *
		 * @param string $use_all_terms Yes or no.
		 */
		public function set_use_all_terms( $use_all_terms ) {
			$use_all_terms = yith_plugin_fw_is_true( $use_all_terms ) ? 'yes' : 'no';

			$this->set_prop( 'use_all_terms', $use_all_terms );
		}

		/**
		 * Set customize_terms property
		 *
		 * @param string $customize_terms Yes or no.
		 */
		public function set_customize_terms( $customize_terms ) {
			$customize_terms = yith_plugin_fw_is_true( $customize_terms ) ? 'yes' : 'no';

			$this->set_prop( 'customize_terms', $customize_terms );
		}

		/**
		 * Set terms for current filter
		 *
		 * @param array $terms An array of terms options, with the following format: term_id=>options; alternatively an array of term ids can be passed
		 *                     In this case, default options will apply for each term.
		 */
		public function set_terms( $terms ) {
			$new_terms = array();

			$default_term_options = $this->get_default_term_options();

			// sanitize array of options.
			if ( ! empty( $terms ) ) {
				foreach ( $terms as $index => $value ) {
					if ( is_array( $value ) ) {
						$term_id = $index;
						$options = $value;

						$new_terms[ (int) $term_id ] = wp_parse_args(
							$options,
							$default_term_options
						);
					} elseif ( is_numeric( $value ) ) {
						$term_id = $value;

						$new_terms[ (int) $term_id ] = $default_term_options;
					} else {
						continue;
					}
				}
			}

			$this->set_prop( 'terms', $new_terms );
		}

		/**
		 * Set design for the filter
		 *
		 * @param string $filter_design Design of the filter.
		 */
		public function set_filter_design( $filter_design ) {
			$supported_designs = apply_filters(
				'yith_wcan_set_supported_filter_design',
				array(
					'checkbox',
					'radio',
					'select',
					'text',
					'color',
					'label',
				)
			);

			if ( ! in_array( $filter_design, $supported_designs, true ) ) {
				return;
			}

			$this->set_prop( 'filter_design', $filter_design );
		}

		/**
		 * Set label position
		 *
		 * @param string $label_position Lbale position.
		 */
		public function set_label_position( string $label_position ) {
			$supported_positions = array(
				'below',
				'right',
				'hide',
			);

			if ( ! in_array( $label_position, $supported_positions, true ) ) {
				return;
			}

			$this->set_prop( 'label_position', $label_position );
		}

		/**
		 * Set number of items per row for the current design
		 *
		 * @param float $number Number of items per wor.
		 */
		public function set_column_number( $number ) {
			$this->set_prop( 'column_number', (int) $number );
		}

		/**
		 * Set whether we should show filter as a toggle
		 *
		 * @param string $show_toggle Yes or no.
		 */
		public function set_show_toggle( $show_toggle ) {
			$show_toggle = yith_plugin_fw_is_true( $show_toggle ) ? 'yes' : 'no';

			$this->set_prop( 'show_toggle', $show_toggle );
		}

		/**
		 * Set whether we should show search for the dropdown
		 *
		 * @param string $show_search Yes or no.
		 */
		public function set_show_search( $show_search ) {
			$show_search = yith_plugin_fw_is_true( $show_search ) ? 'yes' : 'no';

			$this->set_prop( 'show_search', $show_search );
		}

		/**
		 * Set toggle style for the filter
		 *
		 * @param string $toggle_style Toggle style.
		 */
		public function set_toggle_style( $toggle_style ) {
			$supported_styles = array(
				'closed',
				'opened',
			);

			if ( ! in_array( $toggle_style, $supported_styles, true ) ) {
				return;
			}

			$this->set_prop( 'toggle_style', $toggle_style );
		}

		/**
		 * Set order by for the filter
		 *
		 * @param string $order_by Which field should be used to order by.
		 */
		public function set_order_by( $order_by ) {
			$supported_orders = array(
				'name',
				'slug',
				'count',
				'term_order',
				'include',
			);

			if ( ! in_array( $order_by, $supported_orders, true ) ) {
				return;
			}

			$this->set_prop( 'order_by', $order_by );
		}

		/**
		 * Set order of items in the filter
		 *
		 * @param string $order Asc or desc.
		 */
		public function set_order( $order ) {
			$supported_orders = array(
				'asc',
				'desc',
			);

			if ( ! in_array( $order, $supported_orders, true ) ) {
				return;
			}

			$this->set_prop( 'order', $order );
		}

		/**
		 * Set whether we should show count of items
		 *
		 * @param string $show_count Yes or no.
		 */
		public function set_show_count( $show_count ) {
			$show_count = yith_plugin_fw_is_true( $show_count ) ? 'yes' : 'no';

			$this->set_prop( 'show_count', $show_count );
		}

		/**
		 * Set how terms should be shown when support a hierarchy
		 *
		 * @param string $hierarchical Hierarchy style.
		 */
		public function set_hierarchical( $hierarchical ) {
			$supported_options = array(
				'no',
				'parents_only',
				'collapsed',
				'expanded',
				'open',
			);

			if ( ! in_array( $hierarchical, $supported_options, true ) ) {
				return;
			}

			$this->set_prop( 'hierarchical', $hierarchical );
		}

		/**
		 * Set whether we should allow multiple selection
		 *
		 * @param string $multiple Yes or no.
		 */
		public function set_multiple( $multiple ) {
			$multiple = yith_plugin_fw_is_true( $multiple ) ? 'yes' : 'no';

			$this->set_prop( 'multiple', $multiple );
		}

		/**
		 * Set how to behave with multiple selection
		 *
		 * @param string $relation And or or.
		 */
		public function set_relation( $relation ) {
			$supported_relations = array(
				'and',
				'or',
			);

			if ( ! in_array( $relation, $supported_relations, true ) ) {
				return;
			}

			$this->set_prop( 'relation', $relation );
		}

		/**
		 * Set how non-pertinent terms should be handled
		 *
		 * @param string $adoptive Adoptive style.
		 */
		public function set_adoptive( $adoptive ) {
			$supported_adoptive = array(
				'hide',
				'or',
			);

			if ( ! in_array( $adoptive, $supported_adoptive, true ) ) {
				return;
			}

			$this->set_prop( 'adoptive', $adoptive );
		}

		/**
		 * Set filter status
		 *
		 * @param string $enabled Filter filter status (yes/no for enabled/not enabled).
		 */
		public function set_enabled( $enabled ) {
			$enabled = in_array( $enabled, array( 'yes', 'no' ), true ) ? $enabled : 'yes';

			$this->set_prop( 'enabled', $enabled );
		}

		/**
		 * Set price ranges for the filter
		 *
		 * @param array $ranges Ranges to set.
		 */
		public function set_price_ranges( $ranges ) {
			$new_ranges = array();

			if ( ! empty( $ranges ) && is_array( $ranges ) ) {
				foreach ( $ranges as $range_id => $range ) {
					if ( empty( $range['max'] ) && empty( $range['unlimited'] ) || $range['min'] === $range['max'] ) {
						continue;
					}

					$new_ranges[] = array(
						'min'       => (float) $range['min'],
						'max'       => (float) $range['max'],
						'unlimited' => isset( $range['unlimited'] ) && $range['unlimited'],
					);
				}
			}

			$this->set_prop( 'price_ranges', $new_ranges );
		}

		/**
		 * Set price slider design property
		 *
		 * @param string $design Slider/Fields or both; if none of them, fallbacks to slider.
		 */
		public function set_price_slider_design( $design ) {
			if ( ! in_array( $design, array( 'slider', 'fields', 'both' ), true ) ) {
				$design = 'slider';
			}

			$this->set_prop( 'price_slider_design', $design );
		}

		/**
		 * Set price_slider_adaptive_limits property
		 *
		 * @param string $price_slider_adaptive_limits Yes or no.
		 */
		public function set_price_slider_adaptive_limits( $price_slider_adaptive_limits ) {
			$price_slider_adaptive_limits = yith_plugin_fw_is_true( $price_slider_adaptive_limits ) ? 'yes' : 'no';

			$this->set_prop( 'price_slider_adaptive_limits', $price_slider_adaptive_limits );
		}

		/**
		 * Set minimum value for price slider filter
		 *
		 * @param float $min Minimum slider value.
		 */
		public function set_price_slider_min( $min ) {
			$this->set_prop( 'price_slider_min', (float) $min );
		}

		/**
		 * Set maximum value for price slider filter
		 *
		 * @param float $max Maximum slider value.
		 */
		public function set_price_slider_max( $max ) {
			if ( ! $max ) {
				$max = 100;
			}

			$this->set_prop( 'price_slider_max', (float) $max );
		}

		/**
		 * Set increase value for price slider filter
		 *
		 * @param float $step Increase value of the slider.
		 */
		public function set_price_slider_step( $step ) {
			if ( ! $step ) {
				$step = 0.01;
			}

			$this->set_prop( 'price_slider_step', (float) $step );
		}

		/**
		 * Sets options to show in Order by filter
		 *
		 * @param array $order_options List of order options.
		 */
		public function set_order_options( $order_options ) {
			$supported_orders = array_keys( YITH_WCAN_Filter_Factory::get_supported_orders() );
			$order_options    = array_intersect( $supported_orders, $order_options );

			$this->set_prop( 'order_options', $order_options );
		}

		/**
		 * Set whether we should show In Stock filter
		 *
		 * @param string $show_in_stock Yes or no.
		 */
		public function set_show_stock_filter( $show_in_stock ) {
			$show_in_stock = yith_plugin_fw_is_true( $show_in_stock ) ? 'yes' : 'no';

			$this->set_prop( 'show_stock_filter', $show_in_stock );
		}

		/**
		 * Set whether we should show On Sale filter
		 *
		 * @param string $show_on_sale Yes or no.
		 */
		public function set_show_sale_filter( $show_on_sale ) {
			$show_on_sale = yith_plugin_fw_is_true( $show_on_sale ) ? 'yes' : 'no';

			$this->set_prop( 'show_sale_filter', $show_on_sale );
		}

		/**
		 * Set whether we should show Featured filter
		 *
		 * @param string $show_featured Yes or no.
		 */
		public function set_show_featured_filter( $show_featured ) {
			$show_on_sale = yith_plugin_fw_is_true( $show_featured ) ? 'yes' : 'no';

			$this->set_prop( 'show_featured_filter', $show_featured );
		}

		/**
		 * Set filter as enabled
		 *
		 * @return void
		 */
		public function enable() {
			$this->set_prop( 'enabled', 'yes' );
		}

		/**
		 * Set filter as disabled
		 *
		 * @return void
		 */
		public function disable() {
			$this->set_prop( 'enabled', 'no' );
		}

		/**
		 * Sets a prop for a setter method.
		 *
		 * This stores changes in a data array.
		 *
		 * @param string $prop Name of prop to set.
		 * @param mixed  $value Value of the prop.
		 */
		protected function set_prop( $prop, $value ) {
			$this->data[ $prop ] = $value;
		}

		/* === FRONTEND METHODS === */

		/**
		 * Method that will output content of the filter on frontend
		 *
		 * @return string Filter template.
		 */
		public function render() {
			$atts = array(
				'filter' => $this,
				'preset' => $this->get_preset(),
			);

			if ( ! $this->is_relevant() ) {
				return '';
			}

			if ( ! $this->template ) {
				$formatted_type = str_replace( '_', '-', $this->type );
				$this->template = "filter-{$formatted_type}";
			}

			return yith_wcan_get_template( "filters/{$this->template}.php", $atts, false );
		}

		/**
		 * Render filter title
		 *
		 * @return string Filter title HTML.
		 */
		public function render_title() {
			if ( ! $this->get_title() ) {
				return '';
			}

			$title_tag          = apply_filters( 'yith_wcan_filter_title_tag', 'h4', $this );
			$additional_classes = array( 'filter-title' );

			if ( $this->is_collapsable() ) {
				$additional_classes[] = 'collapsable';
				$additional_classes[] = ( $this->is_active() && 'horizontal' !== $this->get_preset()->get_layout() ) ? 'opened' : $this->get_toggle_style();
			}

			$additional_classes = implode( ' ', apply_filters( 'yith_wcan_filter_title_classes', $additional_classes, $this ) );
			$filter_title_html  = wp_kses_post( sprintf( '<%1$s class="%3$s">%2$s</%1$s>', esc_html( $title_tag ), esc_html( $this->get_title() ), esc_attr( $additional_classes ) ) );

			return apply_filters( 'yith_wcan_filter_title_html', $filter_title_html, $this->get_title(), $this );
		}

		/**
		 * Render count for them each item
		 *
		 * @param int $count Count to print.
		 * @return string Count template
		 */
		public function render_count( $count ) {
			if ( ! $this->show_count() ) {
				return '';
			}

			$atts = array(
				'preset' => $this->get_preset(),
				'filter' => $this,
				'count'  => $count,
			);

			return yith_wcan_get_template( 'filters/global/count.php', $atts, false );
		}
	}
}
