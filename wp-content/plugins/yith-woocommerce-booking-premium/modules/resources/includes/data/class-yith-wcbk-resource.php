<?php
/**
 * Class YITH_WCBK_Resource
 *
 * Handles the Resource object.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Modules\Resources
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Resource' ) ) {
	/**
	 * Class YITH_WCBK_Resource
	 */
	class YITH_WCBK_Resource extends YITH_WCBK_Data {

		/**
		 * The data
		 *
		 * @var array
		 */
		protected $data = array(
			'name'                 => '',
			'image_id'             => 0,
			'available_quantity'   => 1,
			'default_availability' => array(),
			'availability_rules'   => array(),
		);

		/**
		 * The object type
		 *
		 * @var string
		 */
		protected $object_type = 'resource';

		/**
		 * The data store.
		 *
		 * @var YITH_WCBK_Resource_Data_Store
		 */
		protected $data_store;

		/**
		 * The availability handler.
		 *
		 * @var YITH_WCBK_Resource_Availability_Handler
		 */
		protected $availability_handler;

		/**
		 * The constructor.
		 *
		 * @param int|YITH_WCBK_Resource|WP_Post $resource The object.
		 *
		 * @throws Exception If passed resource is invalid.
		 */
		public function __construct( $resource = 0 ) {
			parent::__construct( $resource );

			$this->data_store           = WC_Data_Store::load( 'yith-booking-resource' );
			$this->availability_handler = new YITH_WCBK_Resource_Availability_Handler();

			if ( is_numeric( $resource ) && $resource > 0 ) {
				$this->set_id( $resource );
			} elseif ( $resource instanceof self ) {
				$this->set_id( absint( $resource->get_id() ) );
			} elseif ( ! empty( $resource->ID ) ) {
				$this->set_id( absint( $resource->ID ) );
			} else {
				$this->set_object_read( true );
			}

			if ( $this->get_id() > 0 ) {
				$this->data_store->read( $this );
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
		 * Return the image_id
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 */
		public function get_image_id( $context = 'view' ) {
			return $this->get_prop( 'image_id', $context );
		}

		/**
		 * Return the available_quantity
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return int
		 */
		public function get_available_quantity( $context = 'view' ) {
			return $this->get_prop( 'available_quantity', $context );
		}

		/**
		 * Return the default_availability
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return YITH_WCBK_Availability[]
		 */
		public function get_default_availability( $context = 'view' ) {
			return $this->get_prop( 'default_availability', $context );
		}

		/**
		 * Return the availability_rules
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return YITH_WCBK_Availability_Rule[]
		 */
		public function get_availability_rules( $context = 'view' ) {
			return $this->get_prop( 'availability_rules', $context );
		}

		/*
		|--------------------------------------------------------------------------
		| CRUD Setters
		|--------------------------------------------------------------------------
		|
		| Functions for setting object data.
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
		 * Set the image_id
		 *
		 * @param string $value The value to set.
		 */
		public function set_image_id( $value ) {
			$this->set_prop( 'image_id', absint( $value ) );
		}

		/**
		 * Set the available_quantity
		 *
		 * @param int $value The value to set.
		 */
		public function set_available_quantity( $value ) {
			$this->set_prop( 'available_quantity', absint( $value ) );
		}

		/**
		 * Set the default_availability
		 *
		 * @param array $value The value to set.
		 */
		public function set_default_availability( $value ) {
			$value = ! ! $value && is_array( $value ) ? $value : array();
			$value = array_map( 'yith_wcbk_availability', $value );
			$value = yith_wcbk_validate_availabilities(
				$value,
				array(
					'remove_time_slots'   => false,
					'force_first_all_day' => true,
				)
			);

			$this->set_prop( 'default_availability', $value );
		}

		/**
		 * Set availability rules
		 *
		 * @param array|YITH_WCBK_Availability_Rule[] $value The value to set.
		 */
		public function set_availability_rules( $value ) {
			$value = ! ! $value && is_array( $value ) ? $value : array();
			$value = array_map( 'yith_wcbk_availability_rule', $value );

			/**
			 * Availability Rules
			 *
			 * @var YITH_WCBK_Availability_Rule[] $availability_rules
			 */
			$value = array_map(
				function ( $rule ) {
					$availabilities = $rule->get_availabilities( 'edit' );
					$availabilities = yith_wcbk_validate_availabilities(
						$availabilities,
						array(
							'remove_time_slots'   => false,
							'force_first_all_day' => false,
						)
					);
					$rule->set_availabilities( $availabilities );

					return $rule;
				},
				$value
			);

			$this->set_prop( 'availability_rules', $value );
		}

		/**
		 * Get and initialize the availability handler.
		 *
		 * @param WC_Product_Booking $product The booking product.
		 *
		 * @return YITH_WCBK_Resource_Availability_Handler
		 */
		public function availability_handler( WC_Product_Booking $product ): YITH_WCBK_Resource_Availability_Handler {
			$this->availability_handler->init( $this, $product );

			return $this->availability_handler;
		}

		/**
		 * Is available?
		 *
		 * @param WC_Product_Booking $product Booking product.
		 * @param int                $from    From timestamp.
		 * @param int                $to      To timestamp.
		 * @param array              $args    Arguments.
		 *
		 * @return bool
		 */
		public function is_available( WC_Product_Booking $product, int $from, int $to, array $args = array() ): bool {
			return $this->availability_handler( $product )->is_available( $from, $to, $args );
		}

		/**
		 * Check availability.
		 *
		 * @param WC_Product_Booking $product Booking product.
		 * @param int                $from    From timestamp.
		 * @param int                $to      To timestamp.
		 * @param array              $args    Arguments.
		 *
		 * @return array
		 */
		public function check_availability( WC_Product_Booking $product, int $from, int $to, array $args = array() ): array {
			$handler                       = $this->availability_handler( $product );
			$available                     = $handler->is_available( $from, $to, $args );
			$non_available_reasons         = $handler->get_non_available_reasons();
			$non_available_reason_messages = $handler->get_non_available_reason_messages();

			return array(
				'available'                 => $available,
				'non_available_reasons'     => $non_available_reason_messages,
				'non_available_reasons_raw' => $non_available_reasons,
			);
		}

		/**
		 * Return the admin calendar URL.
		 *
		 * @return string
		 */
		public function get_admin_calendar_url(): string {
			$url = yith_wcbk_get_admin_calendar_url( false );

			return add_query_arg( array( 'resource' => $this->get_id() ), $url );
		}

		/**
		 * Returns the main image.
		 *
		 * @param string $size        (default: 'thumbnail').
		 * @param array  $attr        Image attributes.
		 * @param bool   $placeholder True to return $placeholder if no image is found, or false to return an empty string.
		 *
		 * @return string
		 */
		public function get_image( string $size = 'thumbnail', array $attr = array(), bool $placeholder = true ): string {
			$image = '';
			if ( $this->get_image_id() ) {
				$image = wp_get_attachment_image( $this->get_image_id(), $size, false, $attr );
			}

			if ( ! $image && $placeholder ) {
				$image = wc_placeholder_img( $size, $attr );
			}

			return $image;
		}
	}
}
