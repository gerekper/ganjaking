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
 * Admin Membership Plan Rules helper static class.
 *
 * This class adds supporting methods to help populating input fields for rules management UI and saving rules from admin screens and panels.
 *
 * @since 1.9.0
 */
class WC_Memberships_Admin_Membership_Plan_Rules {


	/** @var array valid post types for content restriction rules */
	private static $valid_post_types_for_restriction_rules;

	/** @var array valid taxonomies for rule types */
	private static $valid_taxonomies_for_restriction_rules;


	/**
	 * Returns valid post types for content restriction rules.
	 *
	 * @since 1.9.0
	 *
	 * @param bool $exclude_products whether to exclude products from results (default true, exclude them)
	 * @return array associative array of post type names and labels
	 */
	public static function get_valid_post_types_for_content_restriction_rules( $exclude_products = true ) {

		if ( empty( self::$valid_post_types_for_restriction_rules ) ) {

			self::$valid_post_types_for_restriction_rules = array();

			foreach ( get_post_types( array( 'public' => true ), 'objects' ) as $post_type ) {
				self::$valid_post_types_for_restriction_rules[ $post_type->name ] = $post_type;
			}
		}

		$post_types = self::$valid_post_types_for_restriction_rules;

		if ( ! empty( $post_types ) ) {

			/**
			 * Filters the excluded (blacklisted) post types from content restriction content type options.
			 *
			 * For example, post types listed here won't appear among restrictable options in Membership Plans admin UI.
			 *
			 * @since 1.0.0
			 *
			 * @param array $post_types List of post types to exclude
			 */
			$excluded_post_types = apply_filters( 'wc_memberships_content_restriction_excluded_post_types', array(
				'attachment',
				'wc_product_tab',
				'wooframework',
			) );

			// skip excluded custom post types
			if ( ! empty( $excluded_post_types ) ) {
				foreach ( $excluded_post_types as $post_type ) {
					if ( isset( $post_types[ $post_type ] ) ) {
						unset( $post_types[ $post_type ] );
					}
				}
			}

			// skip products - they have their own restriction rules
			if ( $exclude_products && ! empty( $post_types ) ) {
				if ( isset( $post_types['product'] ) ) {
					unset( $post_types['product'] );
				}
				if ( isset( $post_types['product_variation'] ) ) {
					unset( $post_types['product_variation'] );
				}
			}
		}

		return $post_types;
	}


	/**
	 * Returns valid taxonomies for a rule type.
	 *
	 * @since 1.9.0
	 *
	 * @param string $rule_type one of 'content_restriction', 'product_restriction' or 'purchasing_discount'
	 * @return array associative array of taxonomy names and labels
	 */
	private static function get_valid_taxonomies_for_rule_type( $rule_type ) {

		if ( ! isset( self::$valid_taxonomies_for_restriction_rules[ $rule_type ] ) ) {

			$excluded_taxonomies = array( 'product_shipping_class' );

			switch ( $rule_type ) {
				case 'content_restriction':
					$excluded_taxonomies = array_merge( $excluded_taxonomies, array( 'post_format', 'product_cat' ) );
				break;
				case 'product_restriction':
				case 'purchasing_discount':
					$excluded_taxonomies = array_merge( $excluded_taxonomies, array( 'product_tag' ) );
				break;
			}

			/**
			 * Exclude taxonomies from a rule type.
			 *
			 * This filter allows excluding taxonomies from content & product restriction and purchasing discount rules.
			 *
			 * @since 1.0.0
			 *
			 * @param array $taxonomies list of taxonomy names and labels to exclude
			 */
			$excluded_taxonomies = apply_filters( "wc_memberships_{$rule_type}_excluded_taxonomies", $excluded_taxonomies );

			self::$valid_taxonomies_for_restriction_rules[ $rule_type ] = array();

			// $wp_taxonomy global used as some post types (product add-ons) attach themselves to certain product-related taxonomies (like product_cat) and get_taxonomies() provides no way to do an in_array() on the object types.
			// They either must match exactly or the taxonomy isn't returned.
			foreach ( $GLOBALS['wp_taxonomies'] as $taxonomy ) {

				// skip non-public or excluded taxonomies
				if ( ! $taxonomy->public || ( ! empty( $excluded_taxonomies ) && in_array( $taxonomy->name, $excluded_taxonomies, false ) ) ) {
					continue;
				}

				if ( 'content_restriction' === $rule_type ) {
					// skip product-only taxonomies, they are listed in product restriction rules
					if ( count( $taxonomy->object_type ) === 1 && in_array( 'product', $taxonomy->object_type, true ) ) {
						continue;
					}
				}

				if ( in_array( $rule_type, array( 'product_restriction', 'purchasing_discount' ), true ) ) {
					// skip taxonomies not registered for products
					if ( ! in_array( 'product', (array) $taxonomy->object_type, true ) ) {
						continue;
					}
					// skip product attributes
					if ( strpos( $taxonomy->name, 'pa_' ) === 0 ) {
						continue;
					}
				}

				self::$valid_taxonomies_for_restriction_rules[ $rule_type ][ $taxonomy->name ] = $taxonomy;
			}
		}

		return (array) self::$valid_taxonomies_for_restriction_rules[ $rule_type ];
	}


	/**
	 * Returns valid taxonomies for content restriction rules.
	 *
	 * @since 1.9.0
	 *
	 * @return array associative array of taxonomy names and labels
	 */
	public static function get_valid_taxonomies_for_content_restriction_rules() {
		return self::get_valid_taxonomies_for_rule_type( 'content_restriction' );
	}


	/**
	 * Returns valid taxonomies for product restriction rules.
	 *
	 * @since 1.9.0
	 *
	 * @return array associative array of taxonomy names and labels
	 */
	public static function get_valid_taxonomies_for_product_restriction_rules() {
		return self::get_valid_taxonomies_for_rule_type( 'product_restriction' );
	}


	/**
	 * Returns valid taxonomies for purchasing discount rules.
	 *
	 * @since 1.9.0
	 *
	 * @return array associative array of taxonomy names and labels
	 */
	public static function get_valid_taxonomies_for_purchasing_discounts_rules() {
		return self::get_valid_taxonomies_for_rule_type( 'purchasing_discount' );
	}


	/**
	 * Returns a rule object label.
	 *
	 * Utility method for getting the label for an object ID from this rule in admin screens.
	 *
	 * @since 1.9.0
	 *
	 * @param \WC_Memberships_Membership_Plan_Rule $rule rule object
	 * @param int $object_id the object ID (e.g. post ID, product ID, term ID...)
	 * @param bool $include_id whether to append the ID to the content name (default false)
	 * @return string|null object label or null, if could not find object label
	 */
	public static function get_rule_object_label( $rule, $object_id, $include_id = false ) {

		$label = null;

		if ( $rule instanceof \WC_Memberships_Membership_Plan_Rule ) {

			if ( in_array( $object_id, $rule->get_object_ids(), false ) ) {

				switch ( $rule->get_content_type() ) {

					// get a post title
					case 'post_type':

						if ( 'product' === $rule->get_content_type_name() ) {

							$product = wc_get_product( $object_id );

							if ( $product ) {

								if ( $product->is_type( 'variation' ) ) {
									$post_data = get_post( $product->get_parent_id() );
								} else {
									$post_data = get_post( $product->get_id() );
								}

								if ( isset( $post_data->post_type ) && in_array( $post_data->post_type, array( 'product', 'product_variation' ), true ) ) {
									$label = strip_tags( $include_id ? $product->get_formatted_name() : $product->get_name() );
								}
							}

						} else {

							$label = $include_id ? sprintf( '%1$s (#%2$s)', get_the_title( $object_id ), $object_id ) : get_the_title( $object_id );
						}

					break;

					// get a taxonomy name
					case 'taxonomy':

						$term = get_term( $object_id, $rule->get_content_type_name() );

						if ( $term  && ! is_wp_error( $term ) ) {
							$label = $include_id ? sprintf( '%1$s (#%2$s)', $term->name, $object_id ) : $term->name;
						}

					break;
				}
			}
		}

		return $label;
	}


	/**
	 * Returns the rule object AJAX search action name for admin screens.
	 *
	 * This is intended for enhanced search fields that need to know which callback to interact with.
	 *
	 * @since 1.9.0
	 *
	 * @param \WC_Memberships_Membership_Plan_Rule $rule
	 * @return string
	 */
	public static function get_rule_object_search_action( $rule ) {

		$action = '';

		if ( $rule instanceof \WC_Memberships_Membership_Plan_Rule ) {
			if ( 'taxonomy' === $rule->get_content_type() ) {
				$action = 'wc_memberships_json_search_terms';
			} else {
				if ( 'product' === $rule->get_content_type_name() ) {
					$action = 'woocommerce_json_search_products_and_variations';
				} else {
					$action = 'wc_memberships_json_search_posts';
				}
			}
		}

		return $action;
	}


	/**
	 * Saves and updates rules for each provided rule type.
	 *
	 * This method relies on posted form data and should be used only from admin context, for example from individual meta boxes that are updating rules from the UI input.
	 *
	 * @since 1.9.0
	 *
	 * @param array $posted_data raw data from $_POST
	 * @param int $post_id post ID of the object with rules (e.g. Membership Plan, but could be also a content type)
	 * @param array $rule_types array of rule types to update
	 * @param string $target optional: the context we are updating rules in ('plan' or 'post')
	 */
	public static function save_rules( $posted_data, $post_id, $rule_types, $target = 'plan' ) {

		$added_rules   = array();
		$updated_rules = array();
		$deleted_rules = array();

		$rules_valid_access_length_periods = wc_memberships()->get_plans_instance()->get_membership_plans_access_length_periods();

		foreach ( $rule_types as $rule_type ) {

			$rule_type_post_key = '_' . $rule_type . '_rules';

			if ( empty( $posted_data[ $rule_type_post_key ] ) ) {
				continue;
			}

			// get rules for the current type in loop
			$posted_rules = $posted_data[ $rule_type_post_key ];

			// remove template rule
			if ( isset( $posted_rules['__INDEX__'] ) ) {
				unset( $posted_rules['__INDEX__'] );
			}

			// stop processing rule type if no rules found for the current type
			if ( empty( $posted_rules ) || ! is_array( $posted_rules ) ) {
				continue;
			}

			// Pre-process rules before saving
			foreach ( $posted_rules as $rule_data ) {

				// if not updating rules for a plan, but rather a single post, do not process or update inherited rules or rules that apply to multiple objects
				if ( 'post' === $target && ( empty( $rule_data['object_ids'] ) || count( $rule_data['object_ids'] ) !== 1 || (int) $rule_data['object_ids'][0] !== (int) $post_id ) ) {
					continue;
				}

				$new_rule = new \WC_Memberships_Membership_Plan_Rule( $rule_data );

				// ensure ID and rule type are set
				$new_rule->set_id( empty( $rule_data['id'] ) ? null : $rule_data['id'] );
				$new_rule->set_rule_type( $rule_type );
				$new_rule->set_access_type( '' );

				// extract the content type and content type name from a compound value
				$content_type_parts             = explode( '|', isset( $rule_data['content_type_key'] ) ? $rule_data['content_type_key'] : '' );
				$rule_data['content_type']      = isset( $content_type_parts[0] ) ? $content_type_parts[0] : '';
				$rule_data['content_type_name'] = isset( $content_type_parts[1] ) ? $content_type_parts[1] : '';

				// If updating rules for a single plan, set the plan ID and content type fields:
				if ( 'plan' === $target ) {

					$new_rule->set_membership_plan_id( (int) $post_id );
					$new_rule->set_content_type( $rule_data['content_type'] );
					$new_rule->set_content_type_name( $rule_data['content_type_name'] );

					if ( ! empty( $rule_data['object_ids'] ) ) {
						if ( is_array( $rule_data['object_ids'] ) ) {
							$new_rule->set_object_ids( $rule_data['object_ids'] );
						} elseif ( is_string( $rule_data['object_ids'] ) && $object_ids = explode( ',', $rule_data['object_ids'] ) ) {
							$new_rule->set_object_ids( $object_ids );
						}
					}

				// If updating rules for a single post, rather than a plan, set the object ID and content type explicitly to match the current post:
				} elseif ( isset( $rule_data['membership_plan_id'] ) && is_numeric( $rule_data['membership_plan_id'] ) && $rule_data['membership_plan_id'] > 0 ) {

					$new_rule->set_membership_plan_id( (int) $rule_data['membership_plan_id'] );
					$new_rule->set_content_type( 'post_type' );
					$new_rule->set_content_type_name( get_post_type( $post_id ) );

					if ( empty( $rule_data['object_ids'] ) ) {
						$new_rule->set_object_ids( array( $post_id ) );
					}
				}

				// - Content restriction & product restriction rules:
				if ( in_array( $rule_type, array( 'content_restriction', 'product_restriction' ), true ) ) {

					// set subscription trial handling
					if ( empty( $rule_data['access_schedule_exclude_trial'] ) ) {
						$new_rule->set_access_schedule_include_trial();
					} elseif ( $rule_data['access_schedule_exclude_trial'] ) {
						$new_rule->set_access_schedule_exclude_trial();
					}

					// set access schedule
					if ( ( isset( $rule_data['access_schedule'] ) && 'immediate' === $rule_data['access_schedule'] ) || ( empty( $rule_data['access_schedule_amount'] ) || empty( $rule_data['access_schedule_period'] ) ) || ! in_array( $rule_data['access_schedule_period'], $rules_valid_access_length_periods, true ) ) {
						$new_rule->set_access_schedule( 'immediate' );
					} else {
						$new_rule->set_access_schedule( sprintf( '%d %s', max( 0, (int) $rule_data['access_schedule_amount'] ), $rule_data['access_schedule_period'] ) );
					}

					// set access type (products only)
					if ( 'product_restriction' === $rule_type && $access_type = isset( $rule_data['access_type'] ) && 'purchase' === $rule_data['access_type'] ? 'purchase' : 'view' ) {
						$new_rule->set_access_type( $access_type );
					}

				// - Purchasing discount rules:
				} elseif ( 'purchasing_discount' === $rule_type ) {

					// set active status
					if ( ! empty( $rule_data['active'] ) && 'no' !== $rule_data['active'] ) {
						$new_rule->set_active();
					} else {
						$new_rule->set_inactive();
					}
				}

				// Existing rule: check if rule should be updated or deleted
				if ( wc_memberships()->get_rules_instance()->rule_exists( $new_rule->get_id() ) ) {

					// bail out to the next rule if the current one shouldn't be edited, or if the rule is marked for removal
					if ( ! $new_rule->current_context_allows_editing() || ( wc_memberships()->get_rules_instance()->rule_content_type_exists( $new_rule ) && ! $new_rule->current_user_can_edit() ) ) {
						continue;
					} elseif ( ! empty( $rule_data['remove'] ) && 'no' !== $rule_data['remove'] ) {
						$deleted_rules[] = $new_rule;
					} else {
						$updated_rules[] = $new_rule;
					}

				// New rule: just do a capabilities check before inserting
				} else {

					if ( 'post_type' === $rule_data['content_type']      && $post_type = get_post_type_object( $rule_data['content_type_name'] ) ) {
						// skip if user has no capabilities to edit the associated post type
						if ( $post_type && ! ( current_user_can( $post_type->cap->edit_posts ) && current_user_can( $post_type->cap->edit_others_posts ) ) ) {
							continue;
						}
					} elseif ( 'taxonomy' === $rule_data['content_type'] && $taxonomy = get_taxonomy( $rule_data['content_type_name'] ) ) {
						// skip if user has no capabilities to edit the associated taxonomy
						if ( $taxonomy && ! ( current_user_can( $taxonomy->cap->manage_terms ) && current_user_can( $taxonomy->cap->edit_terms ) ) ) {
							continue;
						}
					}

					$added_rules[] = $new_rule;
				}
			}
		}

		// process all rules to store
		wc_memberships()->get_rules_instance()->set_rules( array(
			'add'    => $added_rules,
			'update' => $updated_rules,
			'delete' => $deleted_rules,
		) );

		/* @type \WC_Memberships_Membership_Plan_Rule[] $plan_rules */
		$plan_rules      = array_merge( $added_rules, $updated_rules );
		$processed_plans = array();

		// compact rules after saving
		foreach ( $plan_rules as $plan_rule ) {

			if ( ( $plan = $plan_rule->get_membership_plan() ) && ! in_array( $plan->get_id(), $processed_plans, true ) ) {

				$plan->compact_rules();

				$processed_plans[] = $plan->get_id();
			}
		}
	}


}
