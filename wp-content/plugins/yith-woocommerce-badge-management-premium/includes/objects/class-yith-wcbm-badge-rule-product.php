<?php
/**
 * Product Badge Rule class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\BadgeManagementPremium\Objects
 * @since   2.0
 */

if ( ! class_exists( 'YITH_WCBM_Badge_Rule_Product' ) ) {
	/**
	 * Badge Rule Class
	 */
	class YITH_WCBM_Badge_Rule_Product extends YITH_WCBM_Badge_Rule {

		/**
		 * Badge rule object type
		 *
		 * @var string
		 */
		protected $badge_rule_type = 'product';

		/**
		 * Stores Badge Rule data.
		 *
		 * @var array
		 */
		protected $data = array(
			'title'                => '',
			'status'               => 'publish',
			'enabled'              => 'yes',
			'type'                 => '',
			'schedule'             => 'no',
			'schedule_dates_from'  => 0,
			'schedule_dates_to'    => 0,
			'exclude_products'     => 'no',
			'excluded_products'    => array(),
			'show_badge_to'        => 'all-users',
			'users'                => array(),
			'user_roles'           => array(),
			'badge'                => 0,
			'assign_to'            => '',
			'newer_then'           => 5,
			'low_stock_quantity'   => 3,
			'bestsellers_quantity' => 5,
		);

		/**
		 * Meta to props.
		 *
		 * @var array
		 */
		protected $meta_key_to_props = array(
			'_enabled'              => 'enabled',
			'_type'                 => 'type',
			'_schedule'             => 'schedule',
			'_schedule_dates_from'  => 'schedule_dates_from',
			'_schedule_dates_to'    => 'schedule_dates_to',
			'_exclude_products'     => 'exclude_products',
			'_excluded_products'    => 'excluded_products',
			'_show_badge_to'        => 'show_badge_to',
			'_users'                => 'users',
			'_user_roles'           => 'user_roles',
			'_badge'                => 'badge',
			'_assign_to'            => 'assign_to',
			'_newer_then'           => 'newer_then',
			'_low_stock_quantity'   => 'low_stock_quantity',
			'_bestsellers_quantity' => 'bestsellers_quantity',
		);

		/**
		 * Data Store object type.
		 *
		 * @var string
		 */
		protected $data_store_object_type = 'badge_rule_product';

		/*
		|--------------------------------------------------------------------------
		| Getters
		|--------------------------------------------------------------------------
		|
		| Methods for getting data from object.
		|
		*/

		/**
		 * Get badge
		 *
		 * @param string $context Context.
		 *
		 * @return string
		 */
		public function get_badge( $context = 'view' ) {
			return $this->get_prop( 'badge', $context );
		}

		/**
		 * Get assign_to
		 *
		 * @param string $context Context.
		 *
		 * @return string
		 */
		public function get_assign_to( $context = 'view' ) {
			return $this->get_prop( 'assign_to', $context );
		}

		/**
		 * Get newer_then
		 *
		 * @param string $context Context.
		 *
		 * @return int
		 */
		public function get_newer_then( $context = 'view' ) {
			return $this->get_prop( 'newer_then', $context );
		}

		/**
		 * Get low_stock_quantity
		 *
		 * @param string $context Context.
		 *
		 * @return int
		 */
		public function get_low_stock_quantity( $context = 'view' ) {
			return $this->get_prop( 'low_stock_quantity', $context );
		}

		/**
		 * Get best_sellers_quantity
		 *
		 * @param string $context Context.
		 *
		 * @return int
		 */
		public function get_bestsellers_quantity( $context = 'view' ) {
			return $this->get_prop( 'bestsellers_quantity', $context );
		}

		/*
		|--------------------------------------------------------------------------
		| Setters
		|--------------------------------------------------------------------------
		|
		| Methods for setting data from object.
		|
		*/

		/**
		 * Set the badge property value
		 *
		 * @param int $value badge value.
		 */
		public function set_badge( $value ) {
			$this->set_prop( 'badge', absint( $value ) );
		}

		/**
		 * Set the assign_to property value
		 *
		 * @param string $value assign_to value.
		 */
		public function set_assign_to( $value ) {
			$this->set_prop( 'assign_to', sanitize_text_field( $value ) );
		}

		/**
		 * Set the newer_then property value
		 *
		 * @param string $value newer_then value.
		 */
		public function set_newer_then( $value ) {
			$this->set_prop( 'newer_then', absint( $value ) );
		}

		/**
		 * Set the low_stock_quantity property value
		 *
		 * @param string $value low_stock_quantity value.
		 */
		public function set_low_stock_quantity( $value ) {
			$this->set_prop( 'low_stock_quantity', absint( $value ) );
		}

		/**
		 * Set best_sellers_quantity
		 *
		 * @param int $value best_sellers_quantity value.
		 */
		public function set_bestsellers_quantity( $value ) {
			$this->set_prop( 'bestsellers_quantity', max( 1, $value ) );
		}

		/*
		|--------------------------------------------------------------------------
		| Conditionals
		|--------------------------------------------------------------------------
		|
		| Checks if a condition is true or false.
		|
		*/

		/**
		 * Check if the badge rule is valid for the Product
		 *
		 * @param int $product_id User ID.
		 *
		 * @return bool
		 */
		public function is_valid_for_product( $product_id = 0 ) {

			$valid   = false;
			$product = $product_id ? wc_get_product( $product_id ) : wc_get_product();

			if ( $product ) {
				if ( ! $this->is_product_excluded( $product->get_id() ) ) {
					switch ( $this->get_assign_to() ) {
						case 'all':
							$valid = true;
							break;
						case 'recent':
							$newer_then              = $this->get_newer_then() * DAY_IN_SECONDS;
							$product_publishing_date = get_post_timestamp( $product->get_id(), 'published' );
							$valid                   = $product_publishing_date && time() - $product_publishing_date < $newer_then;
							break;
						case 'on-sale':
							$valid = yith_wcbm_product_is_on_sale( $product );
							break;
						case 'featured':
							$valid = $product->is_featured();
							break;
						case 'in-stock':
							$valid = $product->is_in_stock() && ! $product->is_on_backorder();
							break;
						case 'out-of-stock':
							$valid = ! $product->is_in_stock();
							break;
						case 'back-order':
							$valid = $product->is_on_backorder();
							break;
						case 'low-stock':
							$valid = ! $product->has_enough_stock( $this->get_low_stock_quantity() ) && $product->is_in_stock();
							break;
						case 'bestsellers':
							$valid = yith_wcbm_is_bestsellers( $product, $this->get_bestsellers_quantity() );
							break;
					}
				}
			}

			return apply_filters( 'yith_wcbm_badge_rule_is_valid_for_product', $valid, $this, $product_id );
		}

		/**
		 * Get Data to store in Associations table
		 *
		 * @return array
		 */
		public function get_associations_rows() {
			return array(
				array(
					'rule_id'  => $this->get_id(),
					'type'     => $this->get_type(),
					'value'    => $this->get_assign_to(),
					'badge_id' => $this->get_badge(),
					'enabled'  => absint( $this->is_enabled() ),
				),
			);
		}
	}
}
