<?php
/**
 * Class YITH_WCBK_Service
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Modules\Services
 */

defined( 'YITH_WCBK' ) || exit();

if ( ! class_exists( 'YITH_WCBK_Service' ) ) {
	/**
	 * Class YITH_WCBK_Service
	 */
	class YITH_WCBK_Service extends YITH_WCBK_Data {

		/**
		 * The data
		 *
		 * @var array
		 */
		protected $data = array(
			'name'                   => '',
			'description'            => '',
			'slug'                   => '',
			'base_price'             => '',
			'optional'               => 'no',
			'hidden'                 => 'no',
			'hidden_in_search_forms' => 'no',
			'multiply_per_blocks'    => 'no',
			'multiply_per_persons'   => 'no',
			'price_for_person_types' => array(),
			'quantity_enabled'       => 'no',
			'min_quantity'           => 0,
			'max_quantity'           => 0,
		);

		/**
		 * The object type
		 *
		 * @var string
		 */
		protected $object_type = 'service';

		/**
		 * The data store.
		 *
		 * @var YITH_WCBK_Service_Data_Store
		 */
		protected $data_store;

		/**
		 * Boolean props
		 *
		 * @var string[]
		 */
		private static $boolean_props = array( 'quantity_enabled', 'optional', 'multiply_per_persons', 'multiply_per_blocks', 'hidden_in_search_forms' );

		/**
		 * The term ID.
		 *
		 * @var int
		 */
		protected $id;

		/**
		 * The constructor.
		 *
		 * @param int|YITH_WCBK_Service|WP_Term $service The object.
		 *
		 * @throws Exception If passed service is invalid.
		 */
		public function __construct( $service = 0 ) {
			parent::__construct( $service );

			$this->data_store = WC_Data_Store::load( 'yith-booking-service' );

			if ( is_numeric( $service ) && $service > 0 ) {
				$this->set_id( $service );
			} elseif ( $service instanceof self ) {
				$this->set_id( absint( $service->get_id() ) );
			} elseif ( ! empty( $service->term_id ) ) {
				$this->set_id( absint( $service->term_id ) );
			} else {
				$this->set_object_read( true );
			}

			if ( $this->get_id() > 0 ) {
				$this->data_store->read( $this );
			}
		}

		/**
		 * __get function.
		 * Handle backward compatibility.
		 *
		 * @param string $key The key.
		 *
		 * @return mixed
		 */
		public function __get( $key ) {
			$getter = 'get_' . $key;

			if ( is_callable( array( $this, $getter ) ) ) {
				yith_wcbk_doing_it_wrong( __CLASS__ . '::' . $key, 'Service should not be accessed directly', '4.0.0' );

				return $this->$getter();
			}

			return null;
		}

		/**
		 * __isset function.
		 *
		 * @param string $key The key.
		 *
		 * @return bool
		 */
		public function __isset( $key ) {
			$valid = array( 'id', 'description', 'slug' );

			return in_array( $key, array_merge( $valid, array_keys( $this->data ) ), true ) || metadata_exists( 'term', $this->id, $key );
		}

		/**
		 * Set function.
		 *
		 * @param string $prop  Property.
		 * @param mixed  $value Value.
		 *
		 * @return bool|int
		 * @deprecated 4.0.0 | use CRUD setters instead.
		 */
		public function set( $prop, $value ) {
			yith_wcbk_deprecated_function( 'YITH_WCBK_Service::set', '4.0.0', 'CRUD setters' );
			$setter = 'set_' . $prop;
			if ( is_callable( array( $this, $setter ) ) ) {
				$this->$setter( $value );

				return true;
			}

			return false;
		}

		/**
		 * Get data of the service
		 *
		 * @param WP_Term $term The term.
		 */
		private function populate( $term = null ) {
			if ( empty( $term ) ) {
				$this->term = get_term( $this->id, $this->taxonomy_name );
			} else {
				$this->term = $term;
			}
			if ( $this->is_valid() ) {
				$this->name        = $this->term->name;
				$this->description = $this->term->description;
				$this->slug        = $this->term->slug;

				$meta_values = get_term_meta( $this->get_id() );

				foreach ( self::get_default_meta_data() as $key => $default ) {
					$value = $meta_values[ $key ][0] ?? $default;
					$value = maybe_unserialize( $value ); // get_term_meta only un-serializes single values.

					if ( in_array( $key, self::$boolean_props, true ) ) {
						$value = wc_bool_to_string( $value );
					}

					$this->$key = $value;
				}

				/**
				 * DO_ACTION: yith_wcbk_booking_service_loaded
				 * Hook to perform any action when a Service is loaded.
				 *
				 * @param YITH_WCBK_Service $this The service.
				 */
				do_action( 'yith_wcbk_booking_service_loaded', $this );
			}
		}

		/*
		|--------------------------------------------------------------------------
		| CRUD Getters
		|--------------------------------------------------------------------------
		|
		| Functions for getting data.
		*/

		/**
		 * Return the name
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 */
		public function get_name( $context = 'view' ) {
			return $this->get_prop( 'name', $context );
		}

		/**
		 * Return the description
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 * @since 2.0.0
		 */
		public function get_description( $context = 'view' ) {
			return $this->get_prop( 'description', $context );
		}

		/**
		 * Return the slug
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 * @since 4.0.0
		 */
		public function get_slug( $context = 'view' ) {
			return $this->get_prop( 'slug', $context );
		}

		/**
		 * Return the base_price
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 * @since 4.0.0
		 */
		public function get_base_price( $context = 'view' ) {
			return $this->get_prop( 'base_price', $context );
		}

		/**
		 * Return the optional
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 * @since 4.0.0
		 */
		public function get_optional( $context = 'view' ) {
			return $this->get_prop( 'optional', $context );
		}

		/**
		 * Return the hidden
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 * @since 4.0.0
		 */
		public function get_hidden( $context = 'view' ) {
			return $this->get_prop( 'hidden', $context );
		}

		/**
		 * Return the hidden_in_search_forms
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 * @since 4.0.0
		 */
		public function get_hidden_in_search_forms( $context = 'view' ) {
			return $this->get_prop( 'hidden_in_search_forms', $context );
		}

		/**
		 * Return the multiply_per_blocks
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 * @since 4.0.0
		 */
		public function get_multiply_per_blocks( $context = 'view' ) {
			return $this->get_prop( 'multiply_per_blocks', $context );
		}

		/**
		 * Return the multiply_per_persons
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 * @since 4.0.0
		 */
		public function get_multiply_per_persons( $context = 'view' ) {
			return $this->get_prop( 'multiply_per_persons', $context );
		}

		/**
		 * Return the price_for_person_types
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return array
		 * @since 4.0.0
		 */
		public function get_price_for_person_types( $context = 'view' ) {
			return $this->get_prop( 'price_for_person_types', $context );
		}

		/**
		 * Return the quantity_enabled
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 * @since 4.0.0
		 */
		public function get_quantity_enabled( $context = 'view' ) {
			return $this->get_prop( 'quantity_enabled', $context );
		}

		/**
		 * Return the min_quantity
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return int
		 */
		public function get_min_quantity( $context = 'view' ) {
			return $this->get_prop( 'min_quantity', $context );
		}

		/**
		 * Return the max_quantity
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return int
		 */
		public function get_max_quantity( $context = 'view' ) {
			return $this->get_prop( 'max_quantity', $context );
		}

		/*
		|--------------------------------------------------------------------------
		| CRUD Setters
		|--------------------------------------------------------------------------
		|
		| Functions for setting data.
		*/

		/**
		 * Set the name
		 *
		 * @param string $value The value to set.
		 */
		public function set_name( $value ) {
			$this->set_prop( 'name', $value );
		}

		/**
		 * Set the slug
		 *
		 * @param string $value The value to set.
		 */
		public function set_slug( $value ) {
			$this->set_prop( 'slug', $value );
		}

		/**
		 * Set the description
		 *
		 * @param string $value The value to set.
		 */
		public function set_description( $value ) {
			$this->set_prop( 'description', $value );
		}

		/**
		 * Set the base_price
		 *
		 * @param string|int|float $value The value to set.
		 */
		public function set_base_price( $value ) {
			$this->set_prop( 'base_price', wc_format_decimal( $value ) );
		}

		/**
		 * Set the optional
		 *
		 * @param string|bool $value The value to set.
		 */
		public function set_optional( $value ) {
			$this->set_prop( 'optional', wc_bool_to_string( $value ) );
		}

		/**
		 * Set the hidden
		 *
		 * @param string|bool $value The value to set.
		 */
		public function set_hidden( $value ) {
			$this->set_prop( 'hidden', wc_bool_to_string( $value ) );
		}

		/**
		 * Set the hidden_in_search_forms
		 *
		 * @param string|bool $value The value to set.
		 */
		public function set_hidden_in_search_forms( $value ) {
			$this->set_prop( 'hidden_in_search_forms', wc_bool_to_string( $value ) );
		}

		/**
		 * Set the multiply_per_blocks
		 *
		 * @param string|bool $value The value to set.
		 */
		public function set_multiply_per_blocks( $value ) {
			$this->set_prop( 'multiply_per_blocks', wc_bool_to_string( $value ) );
		}

		/**
		 * Set the multiply_per_persons
		 *
		 * @param string|bool $value The value to set.
		 */
		public function set_multiply_per_persons( $value ) {
			$this->set_prop( 'multiply_per_persons', wc_bool_to_string( $value ) );
		}

		/**
		 * Set the price_for_person_types
		 *
		 * @param array $value The value to set.
		 */
		public function set_price_for_person_types( $value ) {
			$value = is_array( $value ) ? $value : array();
			$value = array_map( 'wc_format_decimal', $value );

			$this->set_prop( 'price_for_person_types', $value );
		}

		/**
		 * Set the quantity_enabled
		 *
		 * @param string|bool $value The value to set.
		 */
		public function set_quantity_enabled( $value ) {
			$this->set_prop( 'quantity_enabled', wc_bool_to_string( $value ) );
		}

		/**
		 * Set the min_quantity
		 *
		 * @param int $value The value to set.
		 */
		public function set_min_quantity( $value ) {
			$value = max( 0, absint( $value ) );
			$this->set_prop( 'min_quantity', $value );
		}

		/**
		 * Set the max_quantity
		 *
		 * @param int $value The value to set.
		 */
		public function set_max_quantity( $value ) {
			$this->set_prop( 'max_quantity', absint( $value ) );
		}

		/**
		 * Check if the service is valid.
		 *
		 * @return bool
		 * @deprecated 4.0.0
		 */
		public function is_valid() {
			yith_wcbk_deprecated_function( 'YITH_WCBK_Service::is_valid', '4.0.0' );

			return true;
		}


		/**
		 * Check if the service is hidden
		 *
		 * @return bool
		 */
		public function is_hidden() {
			return 'yes' === $this->get_hidden();
		}

		/**
		 * Check if the service is hidden in search forms
		 *
		 * @return bool
		 */
		public function is_hidden_in_search_forms() {
			return 'yes' === $this->get_hidden_in_search_forms() || $this->is_hidden();
		}

		/**
		 * Check if the service has multiply per blocks enabled
		 *
		 * @return bool
		 */
		public function is_multiply_per_blocks() {
			return 'yes' === $this->get_multiply_per_blocks();
		}

		/**
		 * Check if the service has multiply per persons enabled
		 *
		 * @return bool
		 */
		public function is_multiply_per_persons() {
			return 'yes' === $this->get_multiply_per_persons() && yith_wcbk_is_people_module_active();
		}

		/**
		 * Check if the service is optional
		 *
		 * @return bool
		 */
		public function is_optional() {
			return 'yes' === $this->get_optional();
		}

		/**
		 * Check if the service has quantity enabled
		 *
		 * @return bool
		 * @since 2.0.5
		 */
		public function is_quantity_enabled() {
			return 'yes' === $this->get_quantity_enabled();
		}


		/**
		 * Get the price of the current service
		 *
		 * @param int $person_type Person type ID.
		 *
		 * @return string
		 */
		public function get_price_for_person_type( $person_type ) {
			$price = '';
			if ( $person_type && yith_wcbk_is_people_module_active() ) {
				$prices = $this->get_price_for_person_types();
				if ( isset( $prices[ $person_type ] ) ) {
					$price = $prices[ $person_type ];
				}
			}

			return $price;
		}

		/**
		 * Get the price of the current service
		 *
		 * @param int $person_type Person type ID.
		 *
		 * @return float|string
		 */
		public function get_price( $person_type = 0 ) {
			$price = $this->get_base_price();
			if ( $person_type ) {
				$price_for_person_type = $this->get_price_for_person_type( $person_type );
				if ( '' !== $price_for_person_type ) {
					$price = $price_for_person_type;
				}
			}

			return apply_filters( 'yith_wcbk_service_price', floatval( $price ) );
		}

		/**
		 * Get the service name including quantity
		 *
		 * @param bool|int $quantity Quantity.
		 *
		 * @return string
		 * @since 2.0.5
		 */
		public function get_name_with_quantity( $quantity = false ) {
			if ( $this->is_quantity_enabled() && false !== $quantity ) {
				$name = sprintf( '%s (x %s)', $this->get_name(), $quantity );
			} else {
				$name = $this->get_name();
			}

			return apply_filters( 'yith_wcbk_service_get_name_with_quantity', $name, $this );
		}

		/**
		 * Get the price HTML of the current service
		 *
		 * @param int $person_type Person type ID.
		 *
		 * @return string
		 */
		public function get_price_html( $person_type = 0 ) {
			return wc_price( $this->get_price( $person_type ) );
		}

		/**
		 * Return an array of all custom fields
		 *
		 * @return array
		 * @deprecated 4.0.0
		 */
		public static function get_default_meta_data() {
			yith_wcbk_deprecated_function( 'YITH_WCBK_Service::get_default_meta_data', '4.0.0' );

			return array(
				'price'                  => '',
				'optional'               => 'no',
				'hidden'                 => 'no',
				'hidden_in_search_forms' => 'no',
				'multiply_per_blocks'    => 'no',
				'multiply_per_persons'   => 'no',
				'quantity_enabled'       => 'no',
				'min_quantity'           => '',
				'max_quantity'           => '',
				'price_for_person_types' => array(),
			);
		}

		/**
		 * Get pricing for the service
		 *
		 * @param WC_Product_Booking $product The product.
		 *
		 * @return array
		 */
		public function get_pricing( $product ) {
			$pricing         = array();
			$duration_period = yith_wcbk_format_duration( $product->get_duration(), $product->get_duration_unit(), 'period' );
			if ( yith_wcbk_is_people_module_active() && $this->is_multiply_per_persons() && $product->has_people_types_enabled() ) {
				foreach ( $product->get_enabled_people_types() as $person_type ) {
					$person_type_id = absint( $person_type['id'] );
					$price          = apply_filters( 'yith_wcbk_booking_service_get_pricing_html_price', $this->get_price( $person_type_id ), $this, $product );
					if ( ! $price ) {
						$price_html = apply_filters( 'yith_wcbk_service_free_text', __( 'Free', 'yith-booking-for-woocommerce' ) );
					} else {
						$price_html = wc_price( yith_wcbk_get_price_to_display( $product, $price ) );
						if ( apply_filters( 'yith_wcbk_booking_service_get_pricing_show_duration_period', true ) && $this->is_multiply_per_blocks() ) {
							$price_html .= ' / ' . $duration_period;
						}
					}

					$label = yith_wcbk()->person_type_helper()->get_person_type_title( $person_type_id );

					$pricing[ 'person-type-' . $person_type_id ] = array(
						'price'      => $price,
						'price_html' => $price_html,
						'display'    => $label . ' ' . $price_html,
					);
				}

				$html_prices        = wp_list_pluck( $pricing, 'price_html' );
				$unique_html_prices = array_unique( $html_prices );
				if ( 1 === count( $unique_html_prices ) ) {
					$singe_pricing = current( $pricing );
					$pricing       = array(
						'price' => array(
							'price'      => $singe_pricing['price'],
							'price_html' => $singe_pricing['price_html'],
							'display'    => $singe_pricing['price_html'],
						),
					);
				}
			} else {
				$price = apply_filters( 'yith_wcbk_booking_service_get_pricing_html_price', $this->get_price(), $this, $product );
				if ( ! $price ) {
					$price_html = apply_filters( 'yith_wcbk_service_free_text', __( 'Free', 'yith-booking-for-woocommerce' ) );
				} else {
					$price_html = wc_price( yith_wcbk_get_price_to_display( $product, $price ) );
					if ( apply_filters( 'yith_wcbk_booking_service_get_pricing_show_duration_period', true ) && $this->is_multiply_per_blocks() ) {
						$price_html .= ' / ' . $duration_period;
					}
				}
				$pricing['price'] = array(
					'price'      => $price,
					'price_html' => $price_html,
					'display'    => $price_html,
				);
			}

			return $pricing;
		}

		/**
		 * Get the pricing for the services
		 *
		 * @param WC_Product_Booking $product Booking product.
		 *
		 * @return string
		 */
		public function get_pricing_html( $product ) {
			$pricing         = $this->get_pricing( $product );
			$pricing_display = wp_list_pluck( $pricing, 'display' );

			return implode( '<br />', $pricing_display );
		}

		/**
		 * Get the service description HTML.
		 *
		 * @return string
		 * @since 2.1.27
		 */
		public function get_description_html() {
			return apply_filters( 'yith_wcbk_booking_service_get_description_html', wp_kses_post( wpautop( wptexturize( $this->get_description() ) ) ), $this );
		}


		/**
		 * Get information to show in help_tip
		 *
		 * @param WC_Product_Booking $product Booking product.
		 *
		 * @return string
		 * @since 2.0.0
		 */
		public function get_info( $product ) {
			$info = '';

			if ( yith_wcbk()->settings->get( 'show-service-descriptions', 'no' ) === 'yes' ) {
				$description = $this->get_description_html();
				if ( $description ) {
					$info .= "<div class='yith-wcbk-booking-service__description'>{$description}</div>";
				}
			}

			if ( yith_wcbk()->settings->get( 'show-service-prices', 'no' ) === 'yes' ) {
				$pricing = $this->get_pricing_html( $product );

				$info .= "<div class='yith-wcbk-booking-service__pricing'>{$pricing}</div>";
			}

			return apply_filters( 'yith_wcbk_booking_service_get_info', $info, $this, $product );
		}

		/**
		 * Get info html.
		 *
		 * @param array $args Arguments.
		 *
		 * @return string
		 * @since 3.0.0
		 */
		public function get_info_html( $args ) {
			$defaults        = array(
				'product'          => false,
				'show_description' => yith_wcbk()->settings->get( 'show-service-descriptions', 'no' ) === 'yes',
				'show_price'       => yith_wcbk()->settings->get( 'show-service-prices', 'no' ) === 'yes',
				'layout'           => yith_wcbk()->settings->get( 'service-info-layout', 'tooltip' ),
			);
			$args            = wp_parse_args( $args, $defaults );
			$args['service'] = $this;
			$info_html       = '';

			if ( $args['product'] ) {
				$template  = 'booking-form/services/service-info-' . $args['layout'] . '.php';
				$info_html = yith_wcbk_get_module_template_html( 'services', $template, $args, 'single-product/add-to-cart/' );
			}

			return trim( $info_html );
		}

		/**
		 * Return a valid quantity
		 *
		 * @param int $qty Quantity.
		 *
		 * @return int
		 */
		public function validate_quantity( $qty ) {
			$qty = absint( $qty );
			$qty = max( $qty, $this->get_min_quantity() );
			if ( $this->get_max_quantity() ) {
				$qty = min( $qty, $this->get_max_quantity() );
			}

			return $qty;
		}
	}
}
