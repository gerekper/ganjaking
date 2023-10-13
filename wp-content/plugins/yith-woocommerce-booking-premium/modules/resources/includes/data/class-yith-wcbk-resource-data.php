<?php
/**
 * Class YITH_WCBK_Resource_Data
 * Handles the Resource Data object.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Modules\Resources
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Resource_Data' ) ) {
	/**
	 * Class YITH_WCBK_Resource_Data
	 */
	class YITH_WCBK_Resource_Data extends YITH_WCBK_Simple_Object {

		/**
		 * The data
		 *
		 * @var array
		 */
		protected $data = array(
			'product_id'                      => 0,
			'resource_id'                     => 0,
			'base_price'                      => '',
			'fixed_price'                     => '',
			'multiply_base_price_per_person'  => false,
			'multiply_fixed_price_per_person' => false,
			'priority'                        => 0,
		);

		/**
		 * The object type
		 *
		 * @var string
		 */
		protected $object_type = 'resource_data';

		/**
		 * Related objects.
		 *
		 * @var array
		 */
		protected $related_objects = array();

		/**
		 * Return the product_id
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return int
		 */
		public function get_product_id( $context = 'view' ) {
			return $this->get_prop( 'product_id', $context );
		}

		/**
		 * Return the resource_id
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return int
		 */
		public function get_resource_id( $context = 'view' ) {
			return $this->get_prop( 'resource_id', $context );
		}

		/**
		 * Return the base_price
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 */
		public function get_base_price( $context = 'view' ) {
			return $this->get_prop( 'base_price', $context );
		}

		/**
		 * Return the fixed_price
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 */
		public function get_fixed_price( $context = 'view' ) {
			return $this->get_prop( 'fixed_price', $context );
		}

		/**
		 * Return the multiply_base_price_per_person
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return bool
		 */
		public function get_multiply_base_price_per_person( $context = 'view' ) {
			return $this->get_prop( 'multiply_base_price_per_person', $context );
		}

		/**
		 * Return the multiply_fixed_price_per_person
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return bool
		 */
		public function get_multiply_fixed_price_per_person( $context = 'view' ) {
			return $this->get_prop( 'multiply_fixed_price_per_person', $context );
		}

		/**
		 * Return the priority
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return int
		 */
		public function get_priority( $context = 'view' ) {
			return $this->get_prop( 'priority', $context );
		}

		/**
		 * Set the product_id
		 *
		 * @param int $value The value to set.
		 */
		public function set_product_id( $value ) {
			$this->set_prop( 'product_id', absint( $value ) );
		}

		/**
		 * Set the resource_id
		 *
		 * @param int $value The value to set.
		 */
		public function set_resource_id( $value ) {
			$this->set_prop( 'resource_id', absint( $value ) );
		}

		/**
		 * Set the base_price
		 *
		 * @param string $value The value to set.
		 */
		public function set_base_price( $value ) {
			$this->set_prop( 'base_price', wc_format_decimal( $value ) );
		}

		/**
		 * Set the fixed_price
		 *
		 * @param string $value The value to set.
		 */
		public function set_fixed_price( $value ) {
			$this->set_prop( 'fixed_price', wc_format_decimal( $value ) );
		}

		/**
		 * Set the multiply_base_price_per_person
		 *
		 * @param string|bool $value The value to set.
		 */
		public function set_multiply_base_price_per_person( $value ) {
			$this->set_prop( 'multiply_base_price_per_person', wc_string_to_bool( $value ) );
		}

		/**
		 * Set the multiply_fixed_price_per_person
		 *
		 * @param string|bool $value The value to set.
		 */
		public function set_multiply_fixed_price_per_person( $value ) {
			$this->set_prop( 'multiply_fixed_price_per_person', wc_string_to_bool( $value ) );
		}

		/**
		 * Set the priority
		 *
		 * @param int $value The value to set.
		 */
		public function set_priority( $value ) {
			$this->set_prop( 'priority', absint( $value ) );
		}

		/**
		 * Get the resource.
		 *
		 * @return YITH_WCBK_Resource|false
		 */
		public function get_resource() {
			if ( ! isset( $this->related_objects['resource'] ) ) {
				$this->related_objects['resource'] = yith_wcbk_get_resource( $this->get_resource_id() );
			}

			return $this->related_objects['resource'];
		}

		/**
		 * Get the resource.
		 *
		 * @return WC_Product_Booking|false
		 */
		public function get_product() {
			if ( ! isset( $this->related_objects['product'] ) ) {
				$this->related_objects['product'] = yith_wcbk_get_booking_product( $this->get_product_id() );
			}

			return $this->related_objects['product'];
		}

		/**
		 * Get the resource name.
		 *
		 * @return string
		 */
		public function get_resource_name(): string {
			$resource      = $this->get_resource();
			$fallback_name = sprintf(
			// translators: %d is the resource ID.
				__( 'Resource #%d', 'yith-booking-for-woocommerce' ),
				$this->get_resource_id()
			);

			return ! ! $resource ? $resource->get_name() : $fallback_name;
		}

		/**
		 * Get pricing
		 *
		 * @return string
		 */
		public function get_pricing_html(): string {
			$product = $this->get_product();
			$prices  = array();

			if ( $this->get_fixed_price() ) {
				$price_html = wc_price( yith_wcbk_get_price_to_display( $product, $this->get_fixed_price() ) );

				if ( $this->get_multiply_fixed_price_per_person() ) {
					// translators: %s is the price per person.
					$price_html = sprintf( _x( '%s per person', 'pricing', 'yith-booking-for-woocommerce' ), $price_html );
				}

				$prices[] = $price_html;
			}

			if ( $this->get_base_price() ) {
				$price_html = wc_price( yith_wcbk_get_price_to_display( $product, $this->get_base_price() ) );

				if ( apply_filters( 'yith_wcbk_resource_include_duration_period', true, $product ) && ! $product->is_type_fixed_blocks() ) {
					$duration_period = yith_wcbk_format_duration( $product->get_duration(), $product->get_duration_unit(), 'period' );

					$price_html .= ' / ' . $duration_period;
				}

				if ( $this->get_multiply_base_price_per_person() ) {
					// translators: %s is the price per person.
					$price_html = sprintf( _x( '%s per person', 'pricing', 'yith-booking-for-woocommerce' ), $price_html );
				}

				$prices[] = $price_html;
			}

			return implode( ' + ', $prices );
		}

	}
}
