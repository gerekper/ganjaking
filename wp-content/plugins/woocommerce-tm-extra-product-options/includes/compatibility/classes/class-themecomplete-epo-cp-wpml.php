<?php
/**
 * Compatibility class
 *
 * @package Extra Product Options/Compatibility
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Compatibility class
 *
 * This class is responsible for providing compatibility with
 * WPML Multilingual CMS
 * https://wpml.org/
 *
 * @package Extra Product Options/Compatibility
 * @version 6.0
 */
final class THEMECOMPLETE_EPO_CP_WPML {

	/**
	 * Section field array
	 *
	 * @var array
	 * @since 1.0
	 */
	public $wpml_section_fields;

	/**
	 * Element field array
	 *
	 * @var array
	 * @since 1.0
	 */
	public $wpml_element_fields;

	/**
	 * The single instance of the class
	 *
	 * @var THEMECOMPLETE_EPO_CP_WPML|null
	 * @since 1.0
	 */
	protected static $instance = null;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Class Constructor
	 *
	 * @since 1.0
	 */
	public function __construct() {

		add_action( 'wc_epo_add_compatibility', [ $this, 'add_compatibility' ] );
		add_action( 'init', [ $this, 'tm_remove_wcml' ], 3 );

	}

	/**
	 * Add compatibility hooks and filters
	 *
	 * @since 1.0
	 */
	public function add_compatibility() {
		if ( THEMECOMPLETE_EPO_WPML()->is_active() ) {

			$this->wpml_section_fields = [
				'section_header_title',
				'section_header_subtitle',
				'sections_uniqid',
				'sections_class',
				'sections_clogic',
				'sections_logic',
				'sections_popupbuttontext',
			];

			// These are the properties that can have a different value in translated products with WPML.
			$this->wpml_element_fields = [];
			$properties1               = [ 'uniqid', 'clogic', 'logic', 'class' ];
			$properties2               = [ 'header_title', 'header_subtitle', 'container_id', 'text_before_price', 'text_after_price', 'quantity_default_value' ];

			foreach ( THEMECOMPLETE_EPO_BUILDER()->get_elements() as $el => $val ) {
				if ( 'variations' !== $el ) {
					foreach ( $properties1 as $property1 ) {
						if ( 'class' === $property1 && 'product' === $el ) {
							continue;
						}
						$this->wpml_element_fields[] = $el . '_' . $property1;
					}
					if ( 'divider' !== $el && 'header' !== $el && 'product' !== $el ) {
						foreach ( $properties2 as $property2 ) {
							if ( 'quantity_default_value' === $property2 && 'upload' === $el ) {
								continue;
							}
							$this->wpml_element_fields[] = $el . '_' . $property2;
						}
					}

					if ( 'header' === $el ) {
						$this->wpml_element_fields[] = 'header_title';
						$this->wpml_element_fields[] = 'header_subtitle';
					}

					if ( 'product' === $el ) {
						$this->wpml_element_fields[] = 'product_header_title';
						$this->wpml_element_fields[] = 'product_header_subtitle';
					}

					if ( 'range' === $el || 'color' === $el || 'textarea' === $el || 'textfield' === $el || 'date' === $el ) {
						$this->wpml_element_fields[] = $el . '_default_value';
					}

					if ( 'selectbox' === $el || 'textarea' === $el || 'textfield' === $el ) {
						$this->wpml_element_fields[] = $el . '_placeholder';
					}

					if ( 'selectbox' === $el || 'radiobuttons' === $el || 'checkboxes' === $el ) {
						$this->wpml_element_fields[] = $el . '_options';
					}
				} else {
					$this->wpml_element_fields[] = 'variations_header_title';
					$this->wpml_element_fields[] = 'variations_header_subtitle';
					$this->wpml_element_fields[] = 'variations_options';
				}
			}

			$this->wpml_element_fields = apply_filters(
				'wc_epo_wpml_element_fields',
				array_merge(
					$this->wpml_element_fields,
					[
						'upload_button_type',
						'date_format',
						'date_disabled_dates',
						'date_enabled_only_dates',
						'date_tranlation_day',
						'date_tranlation_month',
						'date_tranlation_year',
						'time_time_format',
						'time_tranlation_hour',
						'time_tranlation_minute',
						'time_tranlation_second',
					]
				)
			);

			add_filter( 'tm_cart_contents', [ $this, 'tm_cart_contents' ], 10, 2 );
			add_filter( 'wcml_exception_duplicate_products_in_cart', [ $this, 'tm_wcml_exception_duplicate_products_in_cart' ], 99999, 2 );
			add_filter( 'wcml_filter_cart_item_data', [ $this, 'wcml_filter_cart_item_data' ], 10, 1 );
			add_filter( 'wc_epo_enabled_currencies', [ $this, 'wc_epo_enabled_currencies' ], 10, 1 );
			add_filter( 'wc_epo_use_original_builder', [ $this, 'wc_epo_use_original_builder' ], 10, 5 );
			add_filter( 'wcml_multi_currency_ajax_actions', [ $this, 'wcml_multi_currency_ajax_actions' ], 10, 1 );
		}
	}

	/**
	 * Add our AJAX actions that need to use multi-currency filters.
	 *
	 * @param array $ajax_actions Array of ajax actions.
	 * @return array
	 * @since 6.2
	 */
	public function wcml_multi_currency_ajax_actions( $ajax_actions = [] ) {
		$ajax_actions[] = 'wc_epo_get_associated_product_html';
		return $ajax_actions;
	}

	/**
	 * Alter enabled currencies
	 *
	 * @param array  $array Array in the form of [ $use_original_builder, $index ].
	 * @param array  $element The element array.
	 * @param array  $builder The element builder.
	 * @param array  $current_builder The current element builder.
	 * @param string $identifier 'sections' or the current element.
	 * @since 5.0
	 */
	public function wc_epo_use_original_builder( $array, $element, $builder, $current_builder, $identifier = 'sections' ) {

		$use_original_builder = $array[0];
		$index                = $array[1];

		if ( THEMECOMPLETE_EPO_WPML()->is_active() && false !== $index ) {
			$use_original_builder = false;
			$use_wpml             = false;

			if ( isset( $current_builder[ $identifier . '_uniqid' ] )
				&& isset( $builder[ $identifier . '_uniqid' ] )
				&& isset( $builder[ $identifier . '_uniqid' ][ $index ] )
			) {
				// Get index of element id in internal array.
				$get_current_builder_uniqid_index = array_search( $builder[ $identifier . '_uniqid' ][ $index ], $current_builder[ $identifier . '_uniqid' ], true );
				if ( null !== $get_current_builder_uniqid_index && false !== $get_current_builder_uniqid_index ) {
					$index    = $get_current_builder_uniqid_index;
					$use_wpml = true;
				} else {
					$use_original_builder = true;
				}
			} elseif ( 'variations' === $identifier ) {
				$index    = 0;
				$use_wpml = true;
			}

			$wpml_fields = ( 'sections' === $identifier ) ? $this->wpml_section_fields : $this->wpml_element_fields;

			// always use translated value for multiple_ settings.
			if ( THEMECOMPLETE_EPO_HELPER()->str_startswith( $element, 'multiple_' ) ) {
				$wpml_fields = true;
			}
			if ( isset( $current_builder[ $element ] ) &&
				! $use_original_builder &&
				$use_wpml &&
				( ( is_array( $wpml_fields ) && in_array( $element, $wpml_fields, true ) ) || true === $wpml_fields ) ) {
				$use_original_builder = false;
			} else {
				$use_original_builder = true;
			}
		}

		return [ $use_original_builder, $index ];

	}

	/**
	 * Alter enabled currencies
	 *
	 * @param array $currencies Array of currencies.
	 * @since 5.0
	 */
	public function wc_epo_enabled_currencies( $currencies = [] ) {
		global $woocommerce_wpml;
		if ( $woocommerce_wpml ) {

			if ( THEMECOMPLETE_EPO_WPML()->is_multi_currency() ) {
				$currencies = array_keys( $woocommerce_wpml->settings['currency_options'] );
			}
		}

		return $currencies;

	}

	/**
	 * Remove conflictiong filters used by WooCommerce Multilingual
	 *
	 * @since 1.0
	 */
	public function tm_remove_wcml() {
		global $woocommerce_wpml;
		if ( THEMECOMPLETE_EPO_WPML()->is_active()
			&& $woocommerce_wpml
			&& property_exists( $woocommerce_wpml, 'compatibility' )
			&& isset( $woocommerce_wpml->compatibility )
			&& isset( $woocommerce_wpml->compatibility->extra_product_options ) ) {
			remove_filter( 'get_tm_product_terms', [ $woocommerce_wpml->compatibility->extra_product_options, 'filter_product_terms' ] );
			remove_filter( 'get_post_metadata', [ $woocommerce_wpml->compatibility->extra_product_options, 'product_options_filter' ], 100 );
			remove_action( 'updated_post_meta', [ $woocommerce_wpml->compatibility->extra_product_options, 'register_options_strings' ], 10 );
			unset( $woocommerce_wpml->compatibility->extra_product_options );
		}
	}

	/**
	 * Skip wcml_check_on_duplicate_products_in_cart
	 *
	 * @param boolean $flag true or false.
	 * @param array   $cart_item The cart item array.
	 */
	public function tm_wcml_exception_duplicate_products_in_cart( $flag, $cart_item ) {
		if ( isset( $cart_item['tmcartepo'] ) ) {
			return true;
		}

		return $flag;
	}

	/**
	 * Unset the Cart edit key
	 *
	 * @param array $cart_contents The cart contents.
	 */
	public function wcml_filter_cart_item_data( $cart_contents = [] ) {
		unset( $cart_contents[ THEMECOMPLETE_EPO()->cart_edit_key_var ] );

		return $cart_contents;
	}

	/**
	 * Alter cart contents
	 *
	 * @param array $cart_item The cart item.
	 * @param array $values Array values.
	 * @since 1.0
	 */
	public function tm_cart_contents( $cart_item = [], $values = '' ) {
		if ( ! THEMECOMPLETE_EPO_WPML()->is_active() ) {
			return $cart_item;
		}

		if ( isset( $cart_item['tmcartepo'] ) && is_array( $cart_item['tmcartepo'] ) ) {
			$current_product_id     = THEMECOMPLETE_EPO_WPML()->get_current_id( $cart_item['product_id'] );
			$wpml_translation_by_id = THEMECOMPLETE_EPO_WPML()->get_wpml_translation_by_id( $current_product_id );

			foreach ( $cart_item['tmcartepo'] as $k => $epo ) {
				if ( isset( $epo['mode'] ) && 'local' === $epo['mode'] ) {
					if ( isset( $epo['is_taxonomy'] ) ) {
						if ( '1' === (string) $epo['is_taxonomy'] ) {
							$term        = get_term_by( 'slug', $epo['key'], $epo['section'] );
							$value_label = '';
							if ( $term ) {
								$wpml_term_id = icl_object_id( $term->term_id, $epo['section'], false );
								if ( $wpml_term_id ) {
									$wpml_term = get_term( $wpml_term_id, $epo['section'] );
								} else {
									$wpml_term = $term;
								}
								$value_label = $wpml_term->name;
							}
							$cart_item['tmcartepo'][ $k ]['section_label'] = urldecode( wc_attribute_label( $epo['section'] ) );
							$cart_item['tmcartepo'][ $k ]['value']         = wc_attribute_label( $value_label );
						} elseif ( '0' === (string) $epo['is_taxonomy'] ) {
							$attributes      = themecomplete_get_attributes( floatval( THEMECOMPLETE_EPO_WPML()->get_original_id( $cart_item['product_id'] ) ) );
							$wpml_attributes = themecomplete_get_attributes( floatval( THEMECOMPLETE_EPO_WPML()->get_current_id( $cart_item['product_id'] ) ) );

							$options      = array_map( 'trim', explode( WC_DELIMITER, $attributes[ $epo['section'] ]['value'] ) );
							$options      = array_map( 'sanitize_title', explode( WC_DELIMITER, $attributes[ $epo['section'] ]['value'] ) );
							$wpml_options = array_map( 'trim', explode( WC_DELIMITER, $wpml_attributes[ $epo['section'] ]['value'] ) );

							$cart_item['tmcartepo'][ $k ]['section_label'] = urldecode( wc_attribute_label( $epo['section'] ) );
							$cart_item['tmcartepo'][ $k ]['value']         =
								wc_attribute_label(
									array_search( $epo['key'], $options, true ) !== false &&
									isset(
										$wpml_options[ array_search( $epo['key'], $options, true ) ]
									)
										? $wpml_options[ array_search( $epo['key'], $options, true ) ]
										: $epo['value']
								);
						}
					}
				} elseif ( isset( $epo['mode'] ) && 'builder' === $epo['mode'] ) {
					if ( isset( $wpml_translation_by_id[ $epo['section'] ] ) ) {
						$cart_item['tmcartepo'][ $k ]['section_label'] = $wpml_translation_by_id[ $epo['section'] ];
						if ( ! empty( $epo['multiple'] ) && ! empty( $epo['key'] ) ) {
							$pos = strrpos( $epo['key'], '_' );
							if ( false !== $pos && isset( $wpml_translation_by_id[ 'options_' . $epo['section'] ] ) && is_array( $wpml_translation_by_id[ 'options_' . $epo['section'] ] ) ) {
								$av = array_values( $wpml_translation_by_id[ 'options_' . $epo['section'] ] );

								if ( isset( $av[ substr( $epo['key'], $pos + 1 ) ] ) ) {
									$cart_item['tmcartepo'][ $k ]['value'] = $av[ substr( $epo['key'], $pos + 1 ) ];
								}
							}
						}
					}
				}
			}
		}

		return $cart_item;
	}

}
