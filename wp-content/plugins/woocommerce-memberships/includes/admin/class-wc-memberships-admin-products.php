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
 * @copyright Copyright (c) 2014-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

use SkyVerge\WooCommerce\PluginFramework\v5_7_1 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Handle memberships product data in admin screens.
 *
 * @since 1.9.0
 */
class WC_Memberships_Admin_Products {


	/**
	 * Products memberships data admin handler constructor.
	 *
	 * @since 1.9.0
	 */
	public function __construct() {

		// display a notice when a product that grants access is trashed, unlink from plan when permanently deleted
		add_action( 'trashed_post', [ $this, 'handle_trashed_product_that_grants_access' ], 20 );
		add_action( 'delete_post',  [ $this, 'handle_deleted_product_that_grants_access' ], 20 );

		// duplicate memberships settings for products
		add_action( 'woocommerce_product_duplicate', array( $this, 'duplicate_product_memberships_data' ), 10, 2 );

		// add additional bulk actions to bulk exclude products from restriction rules or member discounts
		// TODO when WordPress 4.7 is the minimum required version, this may be updated to use new hooks {FN 2018-11-05}
		add_action( 'admin_footer-edit.php', array( $this, 'add_membership_bulk_actions' ), 100 );
		add_action( 'load-edit.php',         array( $this, 'process_membership_bulk_actions' ), 100 );
		add_action( 'admin_notices',         array( $this, 'display_membership_bulk_actions_notices' ), 100 );
	}


	/**
	 * Alerts the admin with a notice when a product that may grant access to plans is trashed.
	 *
	 * @internal
	 *
	 * @since 1.15.0
	 *
	 * @param int $post_id the ID of the product that is being trashed
	 */
	public function handle_trashed_product_that_grants_access( $post_id ) {

		if ( ! in_array( get_post_type( $post_id ), [ 'product', 'product_variation' ], true ) ) {
			return;
		}

		$affected_plans = [];
		$plan_statuses  = [
			'draft',
			'pending',
			'publish',
			'future',
		];

		foreach ( wc_memberships_get_membership_plans( [ 'post_status' => $plan_statuses ] ) as $plan ) {

			if ( $plan->has_product( $post_id ) ) {

				$affected_plans[] = '<a href="' . esc_url( get_edit_post_link( $plan->get_id() ) ) . '">' . $plan->get_name() . '</a>';
			}
		}

		if ( ! empty( $affected_plans ) && ( $product = wc_get_product( $post_id ) ) ) {

			wc_memberships()->get_admin_instance()->get_message_handler()->add_warning(
				sprintf(
					/* translators: Placeholder: %1$s - product name linked to edit screen, %2$s list of membership plan names linked to edit screens */
					__( 'The product %1$s is currently set to grant membership access to %2$s. When permanently deleted, it will be removed from the list of products that grant access.', 'woocommerce-memberships' ),
					'<a href="' . get_edit_post_link( $product->is_type( 'variation' ) ? $product->get_parent_id() : $product->get_id() ) . '">' . $product->get_name() . '</a>',
					wc_memberships_list_items( $affected_plans, 'and' )
				)
			);
		}
	}


	/**
	 * Removes a product from the list of products that grant access when the product is permanently deleted.
	 *
	 * @internal
	 *
	 * @since 1.15.0
	 *
	 * @param int $post_id ID of the product that has been set for deletion
	 */
	public function handle_deleted_product_that_grants_access( $post_id ) {

		if ( ! in_array( get_post_type( $post_id ), [ 'product', 'product_variation' ], true ) ) {
			return;
		}

		foreach ( wc_memberships_get_membership_plans() as $plan ) {

			if ( $plan->has_product( $post_id ) ) {

				$plan->delete_product_ids( $post_id );
			}
		}
	}


	/**
	 * Duplicates memberships data for a product.
	 *
	 * @internal
	 *
	 * @since 1.9.0
	 *
	 * @param \WC_Product $new_product new product being created
	 * @param \WP_Post|\WC_Product $old_product old product being cloned
	 */
	public function duplicate_product_memberships_data( $new_product, $old_product ) {

		$new_product_id        = $new_product->get_id();
		$old_product_id        = $old_product->get_id();
		$old_product_post_type = get_post_type( $old_product );

		// get product restriction rules
		$product_restriction_rules = wc_memberships()->get_rules_instance()->get_rules( array(
			'rule_type'         => 'product_restriction',
			'object_id'         => $old_product_id,
			'content_type'      => 'post_type',
			'content_type_name' => $old_product_post_type,
			'exclude_inherited' => true,
			'plan_status'       => 'any',
		) );

		// get purchasing discount rules
		$purchasing_discount_rules = wc_memberships()->get_rules_instance()->get_rules( array(
			'rule_type'         => 'purchasing_discount',
			'object_id'         => $old_product_id,
			'content_type'      => 'post_type',
			'content_type_name' => $old_product_post_type,
			'exclude_inherited' => true,
			'plan_status'       => 'any',
		) );

		$product_rules = array_merge( $product_restriction_rules, $purchasing_discount_rules );

		// duplicate rules
		if ( ! empty( $product_rules ) ) {

			$all_rules = get_option( 'wc_memberships_rules' );

			/* @type $product_rules \WC_Memberships_Membership_Plan_Rule[] */
			foreach ( $product_rules as $rule ) {

				$new_rule               = $rule->get_raw_data();
				$new_rule['object_ids'] = array( $new_product_id );
				$all_rules[]            = $new_rule;
			}

			update_option( 'wc_memberships_rules', $all_rules );
		}

		// duplicate custom messages
		foreach ( array( 'product_viewing_restricted', 'product_purchasing_restricted' ) as $message_type ) {

			if ( $use_custom = wc_memberships_get_content_meta( $old_product, "_wc_memberships_use_custom_{$message_type}_message", true ) ) {
				wc_memberships_set_content_meta( $new_product, "_wc_memberships_use_custom_{$message_type}_message", $use_custom );
			}

			if ( $message = wc_memberships_get_content_meta( $old_product, "_wc_memberships_{$message_type}_message", true ) ) {
				wc_memberships_set_content_meta( $new_product, "_wc_memberships_{$message_type}_message", $message );
			}
		}

		$plans = wc_memberships_get_membership_plans();

		if ( ! empty( $plans ) ) {

			// duplicate 'grants access to'
			foreach ( $plans as $plan ) {

				if ( $plan->has_product( $old_product_id ) ) {
					// add new product id to product ids
					$plan->set_product_ids( $new_product_id, true );
				}
			}
		}

		// duplicate restrictions exclusion setting
		if ( wc_memberships()->get_restrictions_instance()->is_product_public( $old_product ) ) {
			wc_memberships()->get_restrictions_instance()->set_product_public( $new_product );
		}

		// duplicate member discount exclusion setting
		if ( in_array( $old_product_id, wc_memberships()->get_member_discounts_instance()->get_products_excluded_from_member_discounts(), false ) ) {
			wc_memberships()->get_member_discounts_instance()->set_product_excluded_from_member_discounts( $new_product );
		}

		// prune public content caches
		wc_memberships()->get_restrictions_instance()->delete_public_content_cache();
		wc_memberships()->get_member_discounts_instance()->delete_excluded_member_discounts_products_cache();
	}


	/**
	 * Gets a list of membership-related bulk actions applicable to products.
	 *
	 * @since 1.12.0
	 *
	 * @param bool $with_labels whether to return only ID keys (false) or include labels (true)
	 * @return string[]|array list of IDs or associative array of IDs and labels
	 */
	private function get_membership_bulk_actions( $with_labels = false ) {

		$bulk_actions = array(
			'wc_memberships_force_product_public'        => __( 'Disallow restrictions rules', 'woocommerce-memberships' ),
			'wc_memberships_dont_force_product_public'   => __( 'Allow restriction rules', 'woocommerce-memberships' ),
			'wc_memberships_exclude_from_discounts'      => __( 'Disallow member discounts', 'woocommerce-memberships' ),
			'wc_memberships_dont_exclude_from_discounts' => __( 'Allow member discounts', 'woocommerce-memberships' ),
		);

		return true === $with_labels ? $bulk_actions : array_keys( $bulk_actions );
	}


	/**
	 * Adds membership-related bulk actions to products edit screen.
	 *
	 * TODO update this deprecated handling when WordPress 4.7 is the minimum required version {FN 2018-11-05}
	 *
	 * @internal
	 *
	 * @since 1.12.0
	 */
	public function add_membership_bulk_actions() {
		global $post_type;

		if ( 'product' === $post_type && current_user_can( 'manage_woocommerce' ) ) :

			?>
			<script type="text/javascript">
				jQuery( document ).ready( function( $ ) {
					<?php foreach ( $this->get_membership_bulk_actions( true ) as $id => $label ) : ?>
						$( '<option>' ).val( '<?php echo esc_js( $id ); ?>' ).text( '<?php echo esc_js( $label ); ?>' ).appendTo( 'select[name="action"]' );
						$( '<option>' ).val( '<?php echo esc_js( $id ); ?>' ).text( '<?php echo esc_js( $label ); ?>' ).appendTo( 'select[name="action2"]' );
					<?php endforeach; ?>
				} );
			</script>
			<?php

		endif;
	}


	/**
	 * Processes membership-related product bulk actions.
	 *
	 * TODO update this deprecated handling when WordPress 4.7 is the minimum required version {FN 2018-11-05}
	 *
	 * @internal
	 *
	 * @since 1.12.0
	 */
	public function process_membership_bulk_actions() {

		if ( $wp_list_table = _get_list_table( 'WP_Posts_List_Table' ) ) {

			$action       = $wp_list_table->current_action();
			$bulk_actions = $this->get_membership_bulk_actions();

			if ( current_user_can( 'manage_woocommerce' ) && in_array( $action, $bulk_actions, true ) ) {

				$product_ids = isset( $_REQUEST['post'] ) ? array_map( 'intval', (array) $_REQUEST['post'] ) : array();
				$handled     = true;

				switch ( $action ) {
					case 'wc_memberships_force_product_public' :
						$processed = wc_memberships()->get_restrictions_instance()->set_product_public( $product_ids );
					break;
					case 'wc_memberships_dont_force_product_public' :
						$processed = wc_memberships()->get_restrictions_instance()->unset_product_public( $product_ids );
					break;
					case 'wc_memberships_exclude_from_discounts' :
						$processed = wc_memberships()->get_member_discounts_instance()->set_product_excluded_from_member_discounts( $product_ids );
					break;
					case 'wc_memberships_dont_exclude_from_discounts' :
						$processed = wc_memberships()->get_member_discounts_instance()->unset_product_excluded_from_member_discounts( $product_ids );
					break;
					default :
						$processed = 0;
						$handled   = false;
					break;
				}

				if ( $handled ) {

					// remove bulk actions set on the request URL
					$clean_original_url = remove_query_arg( array_merge( $bulk_actions, array( 'untrashed', 'deleted', 'ids', 'action', 'action2', 'tags_input', 'post_author', 'comment_status', 'ping_status', '_status',  'post', 'bulk_edit', 'post_view' ) ), wp_get_referer() );
					$processed_url      = ! $clean_original_url ? admin_url( 'edit.php?post_type=product' ) : $clean_original_url;

					if ( $processed_url ) {

						// re-add the processed bulk action and pagination information
						$redirect_url = add_query_arg( array(
							$action => $processed,
							'paged' => $wp_list_table->get_pagenum(),
						), $processed_url );

						// redirect to the products edit screen carrying bulk action results
						wp_redirect( $redirect_url );
						exit;
					}
				}
			}
		}
	}


	/**
	 * Displays an admin notice after a membership-related bulk action has been processed.
	 *
	 * TODO update this deprecated handling when WordPress 4.7 is the minimum required version {FN 2018-11-05}
	 *
	 * @internal
	 *
	 * @since 1.12.0
	 */
	public function display_membership_bulk_actions_notices() {
		global $post_type, $pagenow;

		if ( 'edit.php' === $pagenow && 'product' === $post_type ) {

			$message      = '';
			$bulk_actions = $this->get_membership_bulk_actions();

			foreach ( $bulk_actions as $bulk_action ) {

				if ( isset( $_GET[ $bulk_action ] ) ) {

					$processed = is_numeric( $_GET[ $bulk_action ] ) ? max( 0, $_GET[ $bulk_action ] ) : 0;

					if ( 0 === $processed ) {

						switch ( $bulk_action ) {
							case 'wc_memberships_force_product_public' :
								$message .= __( 'No Products have been marked as public.', 'woocommerce-memberships' );
							break;
							case 'wc_memberships_dont_force_product_public' :
								$message .= __( 'No Products have been unmarked as public.', 'woocommerce-memberships' );
							break;
							case 'wc_memberships_exclude_from_discounts' :
								$message .= __( 'No Products have been excluded from member discounts.', 'woocommerce-memberships' );
							break;
							case 'wc_memberships_dont_exclude_from_discounts' :
								$message .= __( 'No Products have been marked to be eligible for member discounts from applicable membership plan rules.', 'woocommerce-memberships' );
							break;
						}

					} else {

						switch ( $bulk_action ) {
							case 'wc_memberships_force_product_public' :
								/* translators: Placeholder: %d - product count (number) */
								$message .= sprintf( _n( '%d Product has been marked as public and excluded from memberships restriction rules.', '%d Products have been marked as public and excluded from memberships restriction rules.', $processed, 'woocommerce-memberships' ), $processed );
							break;
							case 'wc_memberships_dont_force_product_public' :
								/* translators: Placeholder: %d - product count (number) */
								$message .= sprintf( _n( '%d Product has been unmarked as public and will now follow any membership plan rules that may affect it.', '%d Products have been unmarked as public and will now follow any membership plan rules that may affect them.', $processed, 'woocommerce-memberships' ), $processed );
							break;
							case 'wc_memberships_exclude_from_discounts' :
								/* translators: Placeholder: %d - product count (number) */
								$message .= sprintf( _n( '%d Product has been excluded from member discounts.', '%d Products have been excluded from member discounts.', $processed, 'woocommerce-memberships' ), $processed );
							break;
							case 'wc_memberships_dont_exclude_from_discounts' :
								/* translators: Placeholder: %d - product count (number) */
								$message .= sprintf( _n( '%d Product has been marked to be eligible for member discounts from applicable membership plan rules.', '%d Product have been marked to be eligible for member discounts from applicable membership plan rules.', $processed, 'woocommerce-memberships' ), $processed );
							break;
						}
					}
				}
			}

			if ( '' !== $message ) {
				// duplicate %1$s is intended, to have notice-warning to work properly
				printf( '<div class="notice notice-%1$s %1$s"><p>%2$s</p></div>', ! empty( $processed ) ? 'updated' : 'warning', esc_html( $message ) );
			}
		}
	}


}
