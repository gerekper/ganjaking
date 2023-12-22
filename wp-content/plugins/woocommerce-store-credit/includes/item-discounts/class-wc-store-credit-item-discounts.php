<?php
/**
 * Collection of WC_Store_Credit_Item_Discount objects.
 *
 * @package WC_Store_Credit/Item Discounts
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Store_Credit_Item_Discounts class.
 */
class WC_Store_Credit_Item_Discounts {

	/**
	 * Item discounts.
	 *
	 * @var array
	 */
	protected $item_discounts = array();

	/**
	 * Discounts grouped by group and item.
	 *
	 * @var array
	 */
	protected $discounts = array();

	/**
	 * Total discounts.
	 *
	 * @var array
	 */
	protected $total_discounts = array();

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 *
	 * @param array $item_discounts Optional. Initialize with the specified item discounts.
	 */
	public function __construct( $item_discounts = array() ) {
		$this->add( $item_discounts );
	}

	/**
	 * Gets the group for the specified item discount.
	 *
	 * @since 3.0.0
	 *
	 * @param WC_Store_Credit_Item_Discount $item_discount Item discount.
	 * @return string
	 */
	public function get_item_group( $item_discount ) {
		$group = 'cart';

		if ( $item_discount instanceof WC_Store_Credit_Item_Discount_Shipping ) {
			$group = 'shipping';
		}

		/**
		 * Filters the group for the specified item discount.
		 *
		 * @since 3.0.0
		 *
		 * @param string                        $group         The group key.
		 * @param WC_Store_Credit_Item_Discount $item_discount Item discount.
		 */
		return apply_filters( 'wc_store_credit_item_discount_group', $group, $item_discount );
	}

	/**
	 * Gets all the item discounts.
	 *
	 * @since 3.0.0
	 *
	 * @return array An array of `WC_Store_Credit_Item_Discount` objects.
	 */
	public function all() {
		return $this->item_discounts;
	}

	/**
	 * Gets all the item discounts for the specified group.
	 *
	 * @since 3.0.0
	 *
	 * @param string $group Group of items to get.
	 * @return array
	 */
	public function get_by_group( $group ) {
		return ( isset( $this->item_discounts[ $group ] ) ? $this->item_discounts[ $group ] : array() );
	}

	/**
	 * Gets the item discount by group and key.
	 *
	 * @since 3.0.0
	 *
	 * @param string $group Item group.
	 * @param mixed  $key   Item key.
	 * @return WC_Store_Credit_Item_Discount|false The item discount object. False if not found.
	 */
	public function get( $group, $key ) {
		return ( isset( $this->item_discounts[ $group ][ $key ] ) ? $this->item_discounts[ $group ][ $key ] : false );
	}

	/**
	 * Gets if it exists an item for the specified group and key.
	 *
	 * @since 3.0.0
	 *
	 * @param string $group Item group.
	 * @param mixed  $key   Item key.
	 * @return bool
	 */
	public function has( $group, $key ) {
		return ( false !== $this->get( $group, $key ) );
	}

	/**
	 * Adds 'Item discount' objects to the list.
	 *
	 * @since 3.0.0
	 *
	 * @throws InvalidArgumentException If the argument is not a `WC_Store_Credit_Item_Discount` object or an array of them.
	 *
	 * @param mixed $item_discounts An 'Item discount' object or an array of them.
	 */
	public function add( $item_discounts ) {
		if ( ! is_array( $item_discounts ) ) {
			$item_discounts = array( $item_discounts );
		}

		foreach ( $item_discounts as $item_discount ) {
			$this->add_item_discount( $item_discount );
		}
	}

	/**
	 * Gets the discounts.
	 *
	 * @since 3.0.0
	 *
	 * @param string $group Optional. Filter by items group. Default empty.
	 * @return array
	 */
	public function get_discounts( $group = '' ) {
		if ( $group ) {
			return ( isset( $this->discounts[ $group ] ) ? $this->discounts[ $group ] : array() );
		}

		return $this->discounts;
	}

	/**
	 * Gets a single discount.
	 *
	 * @since 3.0.0
	 *
	 * @param string $group The items group.
	 * @param string $key   The discount key. Accepts 'base', 'tax', 'taxes'.
	 * @return array An array with pairs [item_key => discount].
	 */
	public function get_discount( $group, $key ) {
		if ( ! $group || ! in_array( $key, array( 'base', 'tax', 'taxes' ), true ) ) {
			return array();
		}

		$discounts = $this->get_discounts( $group );

		return wp_list_pluck( $discounts, $key );
	}

	/**
	 * Gets the total discounts.
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	public function get_total_discounts() {
		return $this->total_discounts;
	}

	/**
	 * Gets a total discount.
	 *
	 * @since 3.0.0
	 *
	 * @param string $key     The discount key.
	 * @param mixed  $default Optional. Default value if not found.
	 * @return mixed The discount amount.
	 */
	public function get_total_discount( $key, $default = 0.0 ) {
		return ( isset( $this->total_discounts[ $key ] ) ? $this->total_discounts[ $key ] : $default );
	}

	/**
	 * Clears the item discounts.
	 *
	 * @since 3.0.4
	 *
	 * @param string $group Optional. Only clear the specified items group. Default all.
	 */
	public function clear( $group = '' ) {
		if ( ! $group ) {
			$this->item_discounts = array();
		} else {
			unset( $this->item_discounts[ $group ] );
		}

		$this->calculate_discounts();
	}

	/**
	 * Adds an item discount to the list.
	 *
	 * @since 3.0.0
	 *
	 * @throws InvalidArgumentException If the argument is not a `WC_Store_Credit_Item_Discount` object.
	 *
	 * @param WC_Store_Credit_Item_Discount $item_discount Item discount object.
	 * @return bool
	 */
	protected function add_item_discount( $item_discount ) {
		if ( ! $item_discount instanceof WC_Store_Credit_Item_Discount ) {
			/* translators: %s: class name */
			throw new InvalidArgumentException( sprintf( esc_html_x( 'The argument must be an instance of the class %s.', 'exception message', 'woocommerce-store-credit' ), 'WC_Store_Credit_Item_Discount' ) );
		}

		$item_group = $this->get_item_group( $item_discount );

		if ( ! $item_group ) {
			return false;
		}

		if ( ! isset( $this->item_discounts[ $item_group ] ) ) {
			$this->item_discounts[ $item_group ] = array();
		}

		$key    = $item_discount->get_item()->key;
		$exists = $this->has( $item_group, $key );

		$this->item_discounts[ $item_group ][ $key ] = $item_discount;

		if ( $exists ) {
			$this->calculate_discounts();
		} else {
			$this->add_discounts( $item_discount );
			$this->add_total_discounts( $item_discount );
		}

		return true;
	}

	/**
	 * Calculates the discounts.
	 *
	 * @since 3.0.0
	 */
	protected function calculate_discounts() {
		$this->discounts       = array();
		$this->total_discounts = array();

		foreach ( $this->item_discounts as $group => $group_items ) {
			foreach ( $group_items as $key => $item_discount ) {
				$this->add_discounts( $item_discount );
				$this->add_total_discounts( $item_discount );
			}
		}
	}

	/**
	 * Adds the item's discounts to the list.
	 *
	 * @since 3.0.0
	 *
	 * @param WC_Store_Credit_Item_Discount $item_discount Item discount object.
	 */
	protected function add_discounts( $item_discount ) {
		$item_group = $this->get_item_group( $item_discount );
		$key        = $item_discount->get_item()->key;

		$this->discounts[ $item_group ][ $key ] = $item_discount->get_discounts();
	}

	/**
	 * Adds the item's discounts to the total discounts.
	 *
	 * @since 3.0.0
	 *
	 * @param WC_Store_Credit_Item_Discount $item_discount Item discount object.
	 */
	protected function add_total_discounts( $item_discount ) {
		$item_group = $this->get_item_group( $item_discount );

		$this->increase_total_discount( $item_group, $item_discount->get_discount() );
		$this->increase_total_discount( "{$item_group}_tax", $item_discount->get_discount_tax() );
		$this->increase_total_taxes_discount( "{$item_group}_taxes", $item_discount->get_discount_taxes() );
	}

	/**
	 * Increases a total discount with the specified amount.
	 *
	 * @since 3.0.0
	 *
	 * @param string $key    The discount key.
	 * @param float  $amount The amount to increase.
	 */
	protected function increase_total_discount( $key, $amount ) {
		$discount = $this->get_total_discount( $key );

		$this->set_total_discount( $key, ( $discount + $amount ) );
	}

	/**
	 * Increases a total taxes discount with the specified values.
	 *
	 * @since 3.0.0
	 *
	 * @param string $key   The discount key.
	 * @param array  $taxes The taxes to increase.
	 */
	protected function increase_total_taxes_discount( $key, $taxes ) {
		$taxes = wc_store_credit_combine_amounts(
			array(
				'discount' => $this->get_total_discount( $key, array() ),
				'taxes'    => $taxes,
			)
		);

		$this->set_total_discount( $key, $taxes );
	}

	/**
	 * Sets a total discount.
	 *
	 * @since 3.0.0
	 *
	 * @param string $key   The discount key.
	 * @param mixed  $value The discount value.
	 */
	protected function set_total_discount( $key, $value ) {
		$this->total_discounts[ $key ] = $value;
	}
}
