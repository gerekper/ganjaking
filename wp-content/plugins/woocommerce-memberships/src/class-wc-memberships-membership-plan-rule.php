<?php
/**
 * WooCommerce Memberships
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Memberships to newer
 * versions in the future. If you wish to customize WooCommerce Memberships for your
 * needs please refer to https://docs.woocommerce.com/document/woocommerce-memberships/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2014-2022, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

use SkyVerge\WooCommerce\PluginFramework\v5_10_13 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Membership Plan Rule.
 *
 * This class represents an individual Membership Plan rule.
 * Rules can be of content or product restriction type or purchasing discount type.
 *
 * @TODO consider having this object extend {@see \WC_Data} and use a custom option data store {unfulvio 2021-05-03}
 *
 * @since 1.0.0
 */
class WC_Memberships_Membership_Plan_Rule {


	/** @var string the rule unique ID (alphanumerical value) */
	private $id = '';

	/** @var int the ID of the plan the rule belongs to */
	private $membership_plan_id = 0;

	/** @var string (yes/no) whether the rule is active (for example discounts) */
	private $active = '';

	/** @var string the rule type: e.g. `content_restriction`, `product_restriction`, `purchasing_discount` */
	private $rule_type = '';

	/** @var string the content type the rule applies to (normally: `post_type` or `taxonomy`) */
	private $content_type = '';

	/** @var string the name of the content type the rule applies to */
	private $content_type_name = '';

	/** @var int[] array of object ids (e.g. posts, products, terms) the rule applies to */
	private $object_ids = array();

	/** @var int[] memoized array of children ids which aren't directly included in the rule data but gathered hierarchically */
	private $children_ids = array();

	/** @var string discount type ('percentage' or 'amount') */
	private $discount_type = '';

	/** @var string discount amount */
	private $discount_amount = '';

	/** @var string rule access type */
	private $access_type = '';

	/** @var string rule access schedule (immediate or delayed) */
	private $access_schedule = '';

	/** @var string (yes/no) whether the rule excludes subscriptions trial periods */
	private $access_schedule_exclude_trial = '';

	/** @var array associative array of key-values for handling additional rule meta data */
	private $meta_data = [];


	/**
	 * Sets up the rule object when instantiated with arguments to be turned into properties.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data rule data
	 */
	public function __construct( $data = array() ) {

		$data = (array) wp_parse_args( (array) $data, $this->get_default_data() );

		foreach ( $data as $property => $value ) {
			if ( property_exists( $this, $property ) ) {
				$this->$property = $value;
			}
		}
	}


	/**
	 * Gets the rule default data.
	 *
	 * @since 1.9.0
	 *
	 * @return array associative array
	 */
	private function get_default_data() {
		global $post;

		return [
			'id'                            => '',
			'membership_plan_id'            => $post && 'wc_membership_plan' === get_post_type( $post ) ? (int) $post->ID : 0,
			'active'                        => '',
			'rule_type'                     => '',
			'content_type'                  => '',
			'content_type_name'             => '',
			'object_ids'                    => [],
			'discount_type'                 => '',
			'discount_amount'               => '',
			'access_type'                   => '',
			'access_schedule'               => 'immediate',
			'access_schedule_exclude_trial' => '',
			'meta_data'                     => [],
		];
	}


	/**
	 * Gets the the whole rule data as associative array.
	 *
	 * @since 1.0.0
	 *
	 * @return array associative array
	 */
	public function get_raw_data() {

		$defaults = $this->get_default_data();
		$raw_data = [];

		foreach ( array_keys( $defaults ) as $key ) {
			if ( property_exists( $this, $key ) ) {
				$raw_data[ $key ] = $this->$key;
			}
		}

		return $raw_data;
	}


	/**
	 * Returns the rule unique identifier.
	 *
	 * @since 1.9.0
	 *
	 * @return string
	 */
	public function get_id() {
		return $this->id;
	}


	/**
	 * Sets the rule ID.
	 *
	 * @since 1.9.0
	 *
	 * @param string|null $id if not passed, will generate a random new ID
	 */
	public function set_id( $id = null ) {

		if ( null === $id ) {
			$this->id = uniqid( 'rule_', false );
		} elseif ( is_string( $id ) ) {
			$this->id = $id;
		}
	}


	/**
	 * Checks if this rule has an ID.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function has_id() {
		return ! empty( $this->id );
	}


	/**
	 * Checks if this rule is new (has no set ID).
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_new() {
		return ! $this->has_id();
	}


	/**
	 * Checks if the rule belongs to a plan that is in the bin.
	 *
	 * @since 1.9.0
	 *
	 * @return bool
	 */
	public function is_trashed() {

		$is_trashed = false;

		if ( $plan = $this->get_membership_plan() ) {
			$is_trashed = 'trash' === $plan->post->post_status;
		}

		return $is_trashed;
	}



	/**
	 * Returns the current rule type.
	 *
	 * @since 1.9.0
	 *
	 * @return string e.g. 'content_restriction', 'product_restriction', 'purchasing_discount'
	 */
	public function get_rule_type() {
		return $this->rule_type;
	}


	/**
	 * Sets the rule type.
	 *
	 * @since 1.9.0
	 *
	 * @param string $type
	 */
	public function set_rule_type( $type ) {

		$this->rule_type = $type;
	}


	/**
	 * Returns the ID of the membership plan the rule belongs to.
	 *
	 * @since 1.9.0
	 *
	 * @return int
	 */
	public function get_membership_plan_id() {
		return (int) $this->membership_plan_id;
	}


	/**
	 * Sets the plan the rule belongs to.
	 *
	 * @param int $id
	 */
	public function set_membership_plan_id( $id ) {

		if ( is_numeric( $id ) && wc_memberships_get_membership_plan( $id ) ) {
			$this->membership_plan_id = (int) $id;
		}
	}


	/**
	 * Returns the membership plan the rule belongs to.
	 *
	 * @since 1.9.0
	 *
	 * @return false|null|\WC_Memberships_Integration_Subscriptions_Membership_Plan|\WC_Memberships_Membership_Plan
	 */
	public function get_membership_plan() {
		return $this->membership_plan_id > 0 ? wc_memberships_get_membership_plan( $this->membership_plan_id ) : null;
	}


	/**
	 * Checks if the rule has a valid membership plan ID.
	 *
	 * @since 1.9.0
	 *
	 * @return bool
	 */
	public function has_membership_plan_id() {
		return $this->get_membership_plan_id() > 0;
	}


	/**
	 * Checks if the rule is tied a to a valid membership plan.
	 *
	 * @since 1.9.0
	 *
	 * @return bool
	 */
	public function has_membership_plan() {

		return $this->has_membership_plan_id() && $this->get_membership_plan() instanceof \WC_Memberships_Membership_Plan;
	}


	/**
	 * Returns the rule content type and content type name as a composite string target.
	 *
	 * @see \WC_Memberships_Membership_Plan_Rule::get_content_type_key() alias, kept for legacy reasons too
	 *
	 * @since 1.9.0
	 *
	 * @return string|null
	 */
	public function get_target() {
		return $this->get_content_type_key();
	}


	/**
	 * Sets content type and content type name using a shorthand form.
	 *
	 * @since 1.9.0
	 *
	 * @param string|array $content_target
	 */
	public function set_target( $content_target ) {

		if ( is_array( $content_target ) && isset( $content_target['content_type'], $content_target['content_type_name'] ) ) {

			$this->set_content_type( $content_target['content_type'] );
			$this->set_content_type_name( $content_target['content_type_name'] );

		} elseif ( is_string( $content_target ) ) {

			$content_target = explode( '|', $content_target );

			if ( isset( $content_target[0], $content_target[1] ) ) {

				$this->set_content_type( $content_target[0] );
				$this->set_content_type_name( $content_target[1] );
			}
		}
	}


	/**
	 * Returns a content type key, suitable for HTML select option.
	 *
	 * Combines content_type and content type name into a single key so that it can be used as a HTML select option value.
	 *
	 * @see \WC_Memberships_Membership_Plan_Rule::get_target() alias
	 *
	 * @since 1.0.0
	 *
	 * @return string|null pipe `|` separated content type key, for example: "post_type|product"
	 */
	public function get_content_type_key() {

		$content_type      = $this->get_content_type();
		$content_type_name = $this->get_content_type_name();

		return $content_type && $content_type_name ? $content_type . '|' . $content_type_name : null;
	}


	/**
	 * Returns the content type targeted by this rule (e.g. 'post_type').
	 *
	 * @since 1.9.0
	 *
	 * @return string
	 */
	public function get_content_type() {
		return $this->content_type;
	}


	/**
	 * Sets the rule content type target.
	 *
	 * @since 1.9.0
	 *
	 * @param $type
	 */
	public function set_content_type( $type ) {

		$this->content_type = wc_memberships()->get_rules_instance()->is_valid_rule_content_type( $type ) ? $type : '';
	}


	/**
	 * Checks which is the rule target content type.
	 *
	 * @since 1.9.0
	 *
	 * @param string|array $type the content type as one or more known kinds
	 * @return bool
	 */
	public function is_content_type( $type ) {
		return $this->is_type( $type, 'content' );
	}


	/**
	 * Gets the rule content type object.
	 *
	 * @since 1.12.0
	 *
	 * @return null|\WP_Post_Type|\WP_Taxonomy
	 */
	public function get_content_type_object() {

		$object = null;

		if ( $this->is_content_type( 'taxonomy' ) ) {
			$object = get_taxonomy( $this->content_type_name );
		} elseif ( $this->is_content_type( 'post_type' ) ) {
			$object = get_post_type_object( $this->content_type_name );
		}

		return is_object( $object ) ? $object : null;
	}


	/**
	 * Gets the rule content type labels.
	 *
	 * @see get_post_type_labels()
	 * @see get_taxonomy_labels()
	 *
	 * @since 1.12.0
	 *
	 * @return \stdClass
	 */
	public function get_content_type_labels() {

		$labels = null;
		$object = $this->get_content_type_object();

		if ( $object instanceof \WP_Post_Type ) {
			$labels = get_post_type_labels( $object );
		} elseif ( $object instanceof \WP_Taxonomy ) {
			$labels = get_taxonomy_labels( $object );
		}

		return is_object( $labels ) ? $labels : null;
	}


	/**
	 * Returns the content type name targeted by the rule (e.g. 'post').
	 *
	 * @since 1.9.0
	 *
	 * @return string
	 */
	public function get_content_type_name() {
		return $this->content_type_name;
	}


	/**
	 * Sets the content type name targeted by the rule.
	 *
	 * @since 1.9.0
	 *
	 * @param string $name
	 */
	public function set_content_type_name( $name ) {

		$this->content_type_name = wc_memberships()->get_rules_instance()->is_valid_rule_content_type_name( $name, $this->rule_type ) ? $name : '';
	}


	/**
	 * Checks which is the content type name.
	 *
	 * @since 1.9.0
	 *
	 * @param string|array $name one or more known content type names (e.g. 'product', 'post'...)
	 * @return bool
	 */
	public function is_content_type_name( $name ) {
		return $this->is_type( $name, 'content_name' );
	}


	/**
	 * Returns the rule target object IDs.
	 *
	 * @since 1.9.0
	 *
	 * @return int[]
	 */
	public function get_object_ids() {

		$object_ids = ! empty( $this->object_ids ) && is_array( $this->object_ids ) ? array_map( 'absint', $this->object_ids ) : array();

		/**
		* Filter object IDs array.
		*
		* @since 1.24.0
		*
		* @param int[] $object_ids array of all unique object IDs targeted by this rule
		* @param \WC_Memberships_Membership_Plan_Rule $rule the current rule
		*/
		return apply_filters( 'wc_memberships_rule_object_ids', array_unique( $object_ids ), $this );
	}


	/**
	 * Sets the rule target object IDS.
	 *
	 * @since 1.9.0
	 *
	 * @param int[] $object_ids
	 */
	public function set_object_ids( array $object_ids ) {

		$this->object_ids = array();

		if ( ! empty( $object_ids ) && is_array( $object_ids ) ) {
			$this->object_ids = array_unique( array_map( 'absint', $object_ids ) );
		}
	}


	/**
	 * Checks if the rule has any object IDs attached to it.
	 *
	 * Alias for `has_objects()`.
	 *
	 * @since 1.9.0
	 *
	 * @return bool
	 */
	public function has_object_ids() {
		return $this->has_objects();
	}


	/**
	 * Checks if this rule has any object IDs attached to it.
	 *
	 * Alias for `has_object_ids()`.
	 *
	 * @since 1.9.0
	 *
	 * @return bool
	 */
	public function has_objects() {

		$object_ids = $this->get_object_ids();

		return is_array( $object_ids ) && ! empty( $object_ids );
	}


	/**
	 * Wipes the rule object IDs.
	 *
	 * @since 1.9.0
	 */
	public function delete_object_ids() {

		$this->set_object_ids( array() );
	}


	/**
	 * Gets any children IDs of the rule object IDs.
	 *
	 * Only works if the object is hierarchical (like a taxonomy or the page post type).
	 *
	 * @since 1.12.0
	 *
	 * @return int[] array of post IDs or term IDs
	 */
	public function get_object_children_ids() {

		if ( empty( $this->children_ids ) && $this->has_object_ids() ) {

			$children = array( array() );

			switch ( $this->get_content_type() ) {

				case 'post_type' :

					foreach ( $this->get_object_ids() as $post_id ) {
						$children[] = $this->get_grandchildren( $post_id );
					}

				break;

				case 'taxonomy' :

					foreach ( $this->get_object_ids() as $term_id ) {

						$children_ids = get_term_children( $term_id, $this->get_content_type_name() );

						if ( is_array( $children_ids ) ) {
							$children[] = $children_ids;
						}
					}

				break;
			}

			$this->children_ids = array_unique( array_map( 'absint', call_user_func_array( 'array_merge', $children ) ) );
		}

		return $this->children_ids;
	}


	/**
	 * Gets all grandchildren of a given post.
	 *
	 * Helper method, do not open to public:
	 * @see \get_children() only retrieves direct children, but we want the whole line of descendants.
	 *
	 * @since 1.12.0
	 *
	 * @param int $ancestor_id ancestor post ID
	 * @return int[] array of descendant post IDs (won't include the ancestor ID)
	 */
	private function get_grandchildren( $ancestor_id ) {

		$descendants = array( array() );
		$children    = get_posts( array(
			'nopaging'    => true,
			'fields'      => 'ids',
			'post_status' => 'publish',
			'post_type'   => $this->get_content_type_name(),
			'post_parent' => $ancestor_id,
		) );

		foreach ( $children as $child_id ) {

			// recursion to catch all grandchildren
			$grandchildren = $this->get_grandchildren( $child_id );

			if ( ! empty( $grandchildren ) ) {
				$descendants[] = $grandchildren;
			}
		}

		return array_unique( array_merge( $children, array_merge( ...$descendants ) ) );
	}


	/**
	 * Returns the rule access schedule.
	 *
	 * @since 1.9.0
	 *
	 * @return string
	 */
	public function get_access_schedule() {
		return $this->access_schedule;
	}


	/**
	 * Sets the rule access schedule.
	 *
	 * @since 1.9.0
	 *
	 * @param string $schedule
	 */
	public function set_access_schedule( $schedule ) {
		$this->access_schedule = $schedule;
	}


	/**
	 * Checks if the access schedule does not apply for subscription trials.
	 *
	 * @TODO this method should be removed in favor of {@see get_meta()} within the Subscriptions integration {unfulvio 2021-04-29}
	 *
	 * @since 1.9.0
	 *
	 * @return null|bool
	 */
	public function is_access_schedule_excluding_trial() {
		return 'yes' === $this->access_schedule_exclude_trial;
	}


	/**
	 * Set the rule access schedule TO NOT APPLY during subscriptions trials.
	 *
	 * @TODO this method should be removed in favor of {@see set_meta()} within the Subscriptions integration {unfulvio 2021-04-29}
	 *
	 * @since 1.9.0
	 */
	public function set_access_schedule_exclude_trial() {

		$this->access_schedule_exclude_trial = 'yes';
	}


	/**
	 * Checks if the access schedule does apply for subscription trials.
	 *
	 * @TODO this method should be removed in favor of {@see get_meta()} within the Subscriptions integration {unfulvio 2021-04-29}
	 *
	 * @since 1.9.0
	 *
	 * @return null|bool
	 */
	public function is_access_schedule_including_trial() {
		return ! $this->is_access_schedule_excluding_trial();
	}


	/**
	 * Sets the rule access schedule TO APPLY during subscriptions trials.
	 *
	 * @TODO this method should be removed in favor of {@see set_meta()} within the Subscriptions integration {unfulvio 2021-04-29}
	 *
	 * @since 1.9.0
	 */
	public function set_access_schedule_include_trial() {

		$this->access_schedule_exclude_trial = 'no';
	}


	/**
	 * Returns the rule access type (products).
	 *
	 * @return string 'view' or 'purchase'
	 */
	public function get_access_type() {
		return $this->access_type;
	}


	/**
	 * Sets the rule access type (for products).
	 *
	 * @since 1.9.0
	 *
	 * @param string $type
	 */
	public function set_access_type( $type ) {
		$this->access_type = $type;
	}


	/**
	 * Checks which is the product access type.
	 *
	 * @since 1.9.0
	 *
	 * @param string 'view' or 'purchase'
	 * @return bool
	 */
	public function is_access_type( $type ) {
		return $this->is_type( $type, 'access' );
	}


	/**
	 * Sets the discount.
	 *
	 * @since 1.9.0
	 *
	 * @param float|int|string $discount
	 */
	public function set_discount( $discount ) {

		if ( Framework\SV_WC_Helper::str_ends_with( $discount, '%' ) ) {
			$discount_type   = 'percentage';
			$discount_amount = preg_replace( '/[^0-9,.]/', '', trim( str_replace( ',', '.', $discount ) ) );
			$discount_amount = is_numeric( $discount_amount ) ? (float) $discount_amount : '';
		} else {
			$discount_type   = 'amount';
			$discount_amount = is_numeric( $discount ) ? (float) $discount : '';
		}

		if ( is_numeric( $discount_amount ) ) {
			$this->set_discount_type( $discount_type );
			$this->set_discount_amount( $discount_amount );
		} else {
			$this->delete_discount();
		}
	}


	/**
	 * Deletes the rule discount data.
	 *
	 * @since 1.9.0
	 */
	public function delete_discount() {

		$this->set_discount_type( '' );
		$this->delete_discount_amount();
	}


	/**
	 * Returns the discount.
	 *
	 * @since 1.9.0
	 *
	 * @return float|int|string numerical if fixed amount or string if percentage or no discount
	 */
	public function get_discount() {

		$discount = '';

		if ( $this->has_discount() ) {
			$discount = $this->get_discount_amount() . ( $this->is_discount_type( 'percentage' ) ? '%' : '' );
		}

		return $discount;
	}


	/**
	 * Checks if the rule has a discount set.
	 *
	 * @since 1.9.0
	 *
	 * @return bool
	 */
	public function has_discount() {

		$amount = $this->get_discount_amount();

		return $this->is_type( 'purchasing_discount' ) && is_numeric( $amount ) && 0 !== $amount;
	}


	/**
	 * Returns the rule product discount type.
	 *
	 * @since 1.9.0
	 *
	 * @return string
	 */
	public function get_discount_type() {
		return $this->discount_type;
	}


	/**
	 * Sets the rule product discount type.
	 *
	 * @since 1.9.0
	 *
	 * @param string $type
	 */
	public function set_discount_type( $type ) {

		$this->discount_type = wc_memberships()->get_rules_instance()->is_valid_discount_type( $type ) ? $type : '';
	}


	/**
	 * Checks if the rule discount is of a known type.
	 *
	 * @param string|array $type
	 * @return bool
	 */
	public function is_discount_type( $type ) {
		return $this->is_type( $type, 'discount' );
	}


	/**
	 * Returns the rule discount amount.
	 *
	 * @since 1.9.0
	 *
	 * @return string
	 */
	public function get_discount_amount() {
		return $this->discount_amount;
	}


	/**
	 * Sets the rule discount amount.
	 *
	 * @since 1.9.0
	 *
	 * @param $amount
	 */
	public function set_discount_amount( $amount ) {

		$this->discount_amount = is_numeric( $amount ) ? $amount : '';
	}


	/**
	 * Deletes the rule discount amount.
	 *
	 * @since 1.9.0
	 */
	public function delete_discount_amount() {

		$this->set_discount_amount( '' );
	}


	/**
	 * Checks if rule has active status (e.g. if a discount is active).
	 *
	 * @return bool
	 */
	public function is_active() {
		return 'yes' === $this->active;
	}


	/**
	 * Checks if the rule is inactive.
	 *
	 * @since 1.9.0
	 *
	 * @return bool
	 */
	public function is_inactive() {
		return ! $this->is_active();
	}


	/**
	 * Sets the rule as active.
	 *
	 * @since 1.9.0
	 */
	public function set_active() {

		$this->active = 'yes';
	}


	/**
	 * Sets the rule as inactive.
	 *
	 * @since 1.9.0
	 */
	public function set_inactive() {

		$this->active = 'no';
	}


	/**
	 * Gets all meta data for the rule.
	 *
	 * @since 1.22.0
	 *
	 * @return array
	 */
	public function get_meta_data() : array {

		return $this->meta_data;
	}


	/**
	 * Gets a meta data value for the rule.
	 *
	 * @since 1.22.0
	 *
	 * @param string $key meta data key
	 * @param mixed|null $default optional default value to return if meta data key is not present, default null
	 * @return mixed|null
	 */
	public function get_meta( string $key, $default = null ) {

		return $this->meta_data[ $key ] ?? $default;
	}


	/**
	 * Sets a meta data key-value for the rule.
	 *
	 * @since 1.22.0
	 *
	 * @param string $key meta data key
	 * @param mixed $value meta data value
	 */
	public function set_meta( string $key, $value ) {

		$this->meta_data[ $key ] = $value;
	}


	/**
	 * Deletes a meta data key from the rule.
	 *
	 * @since 1.22.0
	 *
	 * @param string $key
	 */
	public function delete_meta( string $key ) {

		unset( $this->meta_data[ $key ] );
	}


	/**
	 * Checks a value type.
	 *
	 * Normally you'd use one of these methods:
	 *
	 * @see \WC_Memberships_Membership_Plan_Rule::is_content_type()
	 * @see \WC_Memberships_Membership_Plan_Rule::is_content_type_name()
	 * @see \WC_Memberships_Membership_Plan_Rule::is_access_type()
	 * @see \WC_Memberships_Membership_Plan_Rule::is_discount_type()
	 *
	 * When used directly, it makes sense to just check the rule type, without using the second argument.
	 *
	 * @since 1.9.0
	 *
	 * @param string|array $type type or name to check
	 * @param string $which entity to check, when left blank (default null) it will check the rule type
	 * @return bool
	 */
	public function is_type( $type, $which = null ) {

		$which = null === $which ? 'rule' : $which;

		switch ( $which ) {
			case 'access' :
				$the_type = $this->access_type;
			break;
			case 'content' :
				$the_type = $this->content_type;
			break;
			case 'content_name' :
				$the_type = $this->content_type_name;
			break;
			case 'discount' :
				$the_type = $this->discount_type;
			break;
			case 'rule' :
				$the_type = $this->rule_type;
			break;
			default :
				$the_type = false;
			break;
		}

		if ( empty( $the_type ) ) {
			$is_type = false;
		} else {
			$is_type = is_array( $type ) ? in_array( $the_type, $type, true ) : $the_type === $type;
		}

		return $is_type;
	}


	/**
	 * Determines and returns the rule priority.
	 *
	 * The priority will be determined by the type of content the rule applies to.
	 *
	 * * 10 = post type
	 * * 20 = taxonomy
	 * * 30 = term
	 * * 40 = post
	 *
	 * A higher number means a higher priority.
	 *
	 * @since 1.1.0
	 *
	 * @return int
	 */
	public function get_priority() {

		$priority     = 0;
		$object_ids   = $this->get_object_ids();
		$content_type = $this->get_content_type();

		if ( 'post_type' === $content_type ) {
			$priority = ! empty( $object_ids ) ? 40 : 10;
		} elseif ( 'taxonomy' === $content_type ) {
			$priority = ! empty( $object_ids ) ? 30 : 20;
		}

		/**
		 * Filter rule priority.
		 *
		 * @since 1.1.0
		 *
		 * @param int $priority a numerical priority similar to WordPress hooks
		 * @param \WC_Memberships_Membership_Plan_Rule $rule the current rule
		 */
		return apply_filters( 'wc_memberships_rule_priority', $priority, $this );
	}


	/**
	 * Returns the access schedule amount.
	 *
	 * Returns the amount part of the schedule.
	 * For example, returns '5' for the schedule '5 days'.
	 *
	 * @since 1.0.0
	 *
	 * @return int|string amount or empty string if no schedule
	 */
	public function get_access_schedule_amount() {
		return ! $this->grants_immediate_access() ? wc_memberships_parse_period_length( $this->get_access_schedule(), 'amount' ) : '';
	}


	/**
	 * Returns the access schedule period.
	 *
	 * Returns the period part of the access schedule.
	 * For example, returns 'days' for the schedule '5 days'.
	 *
	 * @since 1.0.0
	 *
	 * @return int|string access schedule period
	 */
	public function get_access_schedule_period() {
		return ! $this->grants_immediate_access() ? wc_memberships_parse_period_length( $this->get_access_schedule(), 'period' ) : '';
	}


	/**
	 * Returns the rule access start time.
	 *
	 * Returns the access start time this rule grants or a piece of content, based on the input time.
	 *
	 * @since 1.0.0
	 *
	 * @param int $from_time timestamp for the time the access start time should be calculated from
	 * @return int access start time as a timestamp
	 */
	public function get_access_start_time( $from_time ) {

		$access_time = $from_time;

		if ( ! $this->grants_immediate_access() ) {

			if ( strpos( $this->get_access_schedule(), 'month' ) !== false ) {
				$access_time = wc_memberships_add_months_to_timestamp( $from_time, $this->get_access_schedule_amount() );
			} else {
				$access_time = strtotime( $this->get_access_schedule(), $from_time );
			}
		}

		/**
		 * Filter rule access start time.
		 *
		 * @since 1.0.0
		 *
		 * @param int $access_time access time, as a timestamp
		 * @param int $from_time from time, as a timestamp
		 * @param \WC_Memberships_Membership_Plan_Rule $rule the rule object
		 */
		$access_time = apply_filters( 'wc_memberships_rule_access_start_time', $access_time, $from_time, $this );

		// access always starts at the beginning of the day (midnight)
		return strtotime( 'midnight', $access_time );
	}


	/**
	 * Checks if this rule applies to a key-value combination
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Content type key
	 * @param string $value Optional. Value. Defaults to null.
	 * @return bool True if applies to the specified key-value combination, false otherwise
	 */
	public function applies_to( $key, $value = null ) {

		switch ( $key ) {

			case 'object_id':
			case 'object_ids':
				$object_ids = $this->get_object_ids();
				$applies    = in_array( $value, $object_ids, false );
			break;

			default:
				$raw_data = $this->get_raw_data();
				$applies  = isset( $raw_data[ $key ] ) && $raw_data[ $key ] === $value;
			break;
		}

		return $applies;
	}


	/**
	 * Checks if this rule grants immediate access to restricted content
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function grants_immediate_access() {
		return $this->get_access_schedule() === 'immediate';
	}


	/**
	 * Checks if the current user can edit this rule.
	 *
	 * Evaluates the user's capability for the rule content type.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function current_user_can_edit() {
		// users can always edit a new rule that has no content type key set yet
		return ! $this->get_content_type_key() ? true : current_user_can( 'wc_memberships_edit_rule', $this->get_id() );
	}


	/**
	 * Checks if the current context allows editing this rule
	 *
	 * Context allows editing if the global $post ID matches the rule membership plan ID or if the rule only applies to the global $post ID.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function current_context_allows_editing() {
		global $post;

		$allow_edit = false;

		if ( $post ) {

			$allow_edit = $this->get_membership_plan_id() === (int) $post->ID;

			if ( ! $allow_edit ) {

				$object_ids = $this->get_object_ids();
				$allow_edit = is_array( $object_ids )
				              && count( $object_ids ) === 1
				              && $this->applies_to( 'object_id', $post->ID );
			}
		}

		return $allow_edit;
	}


}
