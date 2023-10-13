<?php
/**
 * Class YITH_WCBK_Product_Extra_Cost
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Modules\Costs
 */

defined( 'YITH_WCBK' ) || exit();

if ( ! class_exists( 'YITH_WCBK_Product_Extra_Cost' ) ) {
	/**
	 * Class YITH_WCBK_Product_Extra_Cost
	 *
	 * @version 2.1.0
	 */
	class YITH_WCBK_Product_Extra_Cost extends YITH_WCBK_Simple_Object {

		/**
		 * Data.
		 *
		 * @var array
		 */
		protected $data = array(
			'id'                           => 0,
			'name'                         => '',
			'cost'                         => '',
			'multiply_by_number_of_people' => false,
			'multiply_by_duration'         => false,
		);

		/**
		 * Object type.
		 *
		 * @var string
		 */
		protected $object_type = 'product_extra_cost';

		/**
		 * Return the ID
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return int
		 */
		public function get_id( $context = 'view' ) {
			return $this->get_prop( 'id', $context );
		}

		/**
		 * Get identifier.
		 *
		 * @return string|int
		 */
		public function get_identifier() {
			return $this->get_id();
		}

		/**
		 * Get the slug.
		 *
		 * @return string
		 */
		public function get_slug() {
			return get_post_field( 'post_name', $this->get_id() );
		}

		/**
		 * Return the name of the Extra Cost
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 */
		public function get_name( $context = 'view' ) {
			$name = get_the_title( $this->get_id() );

			return 'view' === $context ? apply_filters( $this->get_hook_prefix() . 'name', $name, $this->get_id() ) : $name;
		}

		/**
		 * Return the cost
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 */
		public function get_cost( $context = 'view' ) {
			return $this->get_prop( 'cost', $context );
		}

		/**
		 * Return multiply by number of people
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return bool
		 */
		public function get_multiply_by_number_of_people( $context = 'view' ) {
			return $this->get_prop( 'multiply_by_number_of_people', $context );
		}

		/**
		 * Return multiply by duration
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return bool
		 */
		public function get_multiply_by_duration( $context = 'view' ) {
			return $this->get_prop( 'multiply_by_duration', $context );
		}

		/**
		 * Set ID
		 *
		 * @param int $id The ID.
		 */
		public function set_id( $id ) {
			$this->set_prop( 'id', absint( $id ) );
		}

		/**
		 * Set the cost
		 *
		 * @param string $cost The cost.
		 */
		public function set_cost( $cost ) {
			$this->set_prop( 'cost', wc_format_decimal( $cost ) );
		}

		/**
		 * Set multiply by number of people
		 *
		 * @param string $enabled The value to be set.
		 */
		public function set_multiply_by_number_of_people( $enabled ) {
			$this->set_prop( 'multiply_by_number_of_people', wc_string_to_bool( $enabled ) );
		}

		/**
		 * Set multiply by duration
		 *
		 * @param string $enabled The value to be set.
		 */
		public function set_multiply_by_duration( $enabled ) {
			$this->set_prop( 'multiply_by_duration', wc_string_to_bool( $enabled ) );
		}

		/**
		 * Set name
		 *
		 * @param string $name The value to be set.
		 */
		public function set_name( $name ) {
			$this->set_prop( 'name', $name );
		}


		/**
		 * Has multiply by number of people enabled?
		 *
		 * @return bool
		 */
		public function has_multiply_by_number_of_people_enabled() {
			return $this->get_multiply_by_number_of_people() && yith_wcbk_is_people_module_active();
		}

		/**
		 * Has multiply by duration enabled?
		 *
		 * @return bool
		 */
		public function has_multiply_by_duration_enabled() {
			return $this->get_multiply_by_duration();
		}

		/**
		 * Is valid?
		 *
		 * @return bool
		 */
		public function is_valid() {
			return ( $this->is_custom() && $this->get_name() ) || ( 'publish' === get_post_status( $this->get_id() ) && $this->get_cost() );
		}

		/**
		 * Is custom?
		 *
		 * @return bool
		 */
		public function is_custom() {
			return ! $this->get_id();
		}

		/**
		 * Calculate the total cost
		 *
		 * @param int $duration Duration.
		 * @param int $people   People number.
		 *
		 * @return float
		 */
		public function calculate_cost( $duration, $people ) {
			$cost = (float) $this->get_cost();
			if ( $this->has_multiply_by_duration_enabled() ) {
				$cost = $cost * $duration;
			}

			if ( $this->has_multiply_by_number_of_people_enabled() ) {
				$cost = $cost * $people;
			}

			return $cost;
		}

	}
}
