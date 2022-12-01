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
 * Admin Membership Plans handler.
 *
 * This class handles all the admin-related functionality for membership plans, like the list screen, meta boxes, etc.
 *
 * @since 1.0.0
 */
class WC_Memberships_Admin_Membership_Plans {


	/**
	 * Handler constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// plans admin screen columns
		add_filter( 'manage_edit-wc_membership_plan_columns',        array( $this, 'customize_columns' ) );
		add_action( 'manage_wc_membership_plan_posts_custom_column', array( $this, 'custom_column_content' ), 10, 2 );

		// disable some bulk features not applicable
		add_filter( 'bulk_actions-edit-wc_membership_plan', '__return_empty_array' );
		add_filter( 'months_dropdown_results',              '__return_empty_array' );

		// filter row actions
		add_filter( 'post_row_actions', array( $this, 'customize_row_actions' ), 10, 2 );

		// custom admin plan actions
		add_action( 'admin_action_duplicate_plan', array( $this, 'duplicate_membership_plan' ) );

		// add/edit plan screen hooks
		add_action( 'post_submitbox_misc_actions', array( $this, 'post_submitbox_misc_actions' ) );
		add_action( 'post_submitbox_start',        array( $this, 'post_submitbox_start' ) );
		add_action( 'add_meta_boxes',              array( $this, 'customize_meta_boxes' ) );
	}


	/**
	 * Customizes membership plan columns.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param array $columns
	 * @return array
	 */
	public function customize_columns( $columns ) {

		unset( $columns['date'], $columns['cb'] );

		$columns['slug']    = __( 'Slug', 'woocommerce-memberships' );

		if ( defined( 'WP_DEBUG' ) && true === WP_DEBUG ) {
			$columns['rules'] = __( 'Rules', 'woocommerce-memberships' );
		}

		$columns['length']  = __( 'Access length', 'woocommerce-memberships' );
		$columns['access']  = __( 'Access from', 'woocommerce-memberships' );
		$columns['members'] = __( 'Members', 'woocommerce-memberships' );

		return $columns;
	}


	/**
	 * Outputs custom column content.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param string $column
	 * @param int $post_id
	 */
	public function custom_column_content( $column, $post_id ) {
		global $post;

		$membership_plan = wc_memberships_get_membership_plan( $post );

		if ( $membership_plan ) {

			switch ( $column ) {

				case 'slug':
					echo $membership_plan->get_slug();
				break;

				case 'length':

					$has_products = $membership_plan->get_products( true );

					if ( 'purchase' === $membership_plan->get_access_method() && 0 === count( $has_products ) ) {
						echo '';
					} else {
						echo $membership_plan->get_human_access_length();
					}

				break;

				case 'rules':

					if ( defined( 'WP_DEBUG' ) && true === WP_DEBUG ) {

						$content_restriction_rules = $membership_plan->get_content_restriction_rules();
						$product_restriction_rules = $membership_plan->get_product_restriction_rules();
						$purchasing_discount_rules = $membership_plan->get_purchasing_discount_rules();

						$rules = array(
							__( 'Restrict content', 'woocommerce-memberships' )     => ! empty( $content_restriction_rules ),
							__( 'Restrict products', 'woocommerce-memberships' )    => ! empty( $product_restriction_rules ),
							__( 'Purchasing discounts', 'woocommerce-memberships' ) => ! empty( $purchasing_discount_rules ),
						);

						foreach ( $rules as $label => $active ) {

							$label = esc_html( $label );
							$class = $active ? 'has-rules' : 'has-not-rules';

							printf( "<span class='{$class}'>%s {$label}</span><br>", ! $active ? '&#x2717;' : '&#x2713;' );
						}
					}

				break;

				case 'access':

					$access_method = $membership_plan->get_access_method();

					if ( 'manual-only' === $access_method ) {
						esc_html_e( 'Assigned manually', 'woocommerce-memberships' );
					} elseif ( 'signup' === $access_method ) {
						esc_html_e( 'Account registration', 'woocommerce-memberships' );
					} elseif ( 'purchase' === $access_method ) {
						esc_html_e( 'Purchase', 'woocommerce-memberships' );
						$this->list_products_granting_access( $membership_plan );
					}

				break;

				case 'members':

					// TODO add an ajax/javascript control to break down counters and links to members by status {FN 2016-06-06}

					$view_members = admin_url( "edit.php?post_type=wc_user_membership&action=-1&post_parent={$post_id}" );

					echo '<a href="' . esc_url( $view_members ) . '" title="' . esc_html__( 'View Members', 'woocommerce-memberships' ) . '">';
					echo $membership_plan->get_memberships_count();
					echo '</a>';

				break;

			}
		}
	}


	/**
	 * Lists products that grant access to a Membership Plan
	 *
	 * @since 1.7.0
	 *
	 * @param WC_Memberships_Membership_Plan $membership_plan The membership plan
	 */
	private function list_products_granting_access( $membership_plan ) {

		$product_ids = $membership_plan->get_product_ids();

		if ( ! empty( $product_ids ) ) {

			echo '<ul class="access-from-list">';

			foreach ( $product_ids as $product_id ) {

				if ( $product = wc_get_product( $product_id ) )  {

					// by using Subscriptions method we can account for custom subscription product types
					if ( is_callable( 'WC_Subscriptions_Product::is_subscription' ) ) {
						$is_subscription = \WC_Subscriptions_Product::is_subscription( $product );
					} else {
						$is_subscription = $product->is_type( array( 'subscription', 'variable-subscription', 'subscription_variation' ) );
					}

					printf(
						'<li>%1$s%2$s</li>',
						$this->get_edit_product_link( $product ),
						$is_subscription ? ' <small>(' . strtolower( __( 'Subscription', 'woocommerce-memberships' ) ) . ')</small> ' : ''
					);
				}
			}

			echo '</ul>';
		}
	}


	/**
	 * Outputs a link to edit a product in admin.
	 *
	 * @since 1.7.0
	 *
	 * @param \WC_Product|\WC_Product_Variation $product a product or variation
	 * @return string
	 */
	private function get_edit_product_link( $product ) {

		$product_link = '';

		if ( $product instanceof \WC_Product ) {

			if ( $product->is_type( 'variation' ) ) {
				$product_link = get_edit_post_link( $product->get_parent_id( 'edit' ) );
			} else {
				$product_link = get_edit_post_link( $product->get_id() );
			}

			$product_link = sprintf( '<a href="%1$s">%2$s</a>', $product_link, $product->get_formatted_name() );
		}

		return $product_link;
	}


	/**
	 * Customizes membership plan row actions.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param array $actions associative array of action links
	 * @param \WP_Post $post the membership plan post object
	 * @return array
	 */
	public function customize_row_actions( $actions, WP_Post $post ) {

		if ( 'wc_membership_plan' === $post->post_type ) {

			// remove quick edit, permanently delete actions
			unset( $actions['inline hide-if-no-js'], $actions['delete'] );

			if ( $plan = wc_memberships_get_membership_plan( $post ) ) {

				if ( isset( $actions['trash'] ) && $plan->has_active_memberships() ) {

					$tip = '';

					if ( 'trash' === $post->post_status ) {
						$tip = esc_attr__( 'This membership plan cannot be restored because it has active members.', 'woocommerce-memberships' );
					} elseif ( EMPTY_TRASH_DAYS ) {
						$tip = esc_attr__( 'This membership plan cannot be moved to trash because it has active members.', 'woocommerce-memberships' );
					}

					if ( 'trash' === $post->post_status || ! EMPTY_TRASH_DAYS ) {
						$tip = esc_attr__( 'This membership plan cannot be permanently deleted because it has active members.', 'woocommerce-memberships' );
					}

					$actions['trash'] = '<span title="' . $tip . '" style="cursor: help;">' . strip_tags( $actions['trash'] ) . '</span>';
				}

				// add duplicate plan action
				$actions['duplicate'] = sprintf(
					'%1$s' . esc_html_x( 'Duplicate', 'Duplicate a Membership Plan', 'woocommerce-memberships' ) . '%2$s',
					'<a href="' . wp_nonce_url( admin_url( 'edit.php?post_type=wc_membership_plan&action=duplicate_plan&amp;post=' . $post->ID ), 'wc-memberships-duplicate-plan_' . $post->ID ) . '" title="' . __( 'Make a duplicate from this membership plan', 'woocommerce-memberships' ) . '" rel="permalink">',
					'</a>'
				);
			}
		}

		return $actions;
	}


	/**
	 * Adds meta boxes to the membership plan edit page.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function customize_meta_boxes() {

		// remove the slug div
		remove_meta_box( 'slugdiv', 'wc_membership_plan', 'normal' );
	}


	/**
	 * Adds actions to the Membership Plan submit box misc actions.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function post_submitbox_misc_actions() {
		global $post, $pagenow;

		// output on published plans only
		if ( 'post.php' === $pagenow && ( $plan = wc_memberships_get_membership_plan( $post ) ) ) :

			?>
			<div class="misc-pub-section misc-pub-grant-access">
				<span class="grant-access">
					<?php

					$button_data  = 'data-plan-id="'     . esc_attr( $plan->get_id() ) . '" ';
					$button_data .= 'data-plan-name="'   . esc_html( $plan->get_name() ) . '" ';
					$button_data .= 'data-plan-access="' . esc_attr( $plan->get_access_method() ) . '" ';

					$grant_access_button = '<button href="#" id="grant-access-modal-plan-' . $plan->get_id() . '" class="button button-grant-access grant-access-modal" ' . $button_data .'>' . esc_html__( 'Grant Access', 'woocommerce-memberships' ) . '</button>';

					switch ( $plan->get_access_method() ) {

						case 'signup' :

							?>
							<span class="grant-access-signup"><?php
								/* translators: Placeholder: %s - HTML button */
								printf( __( 'Existing users: %s', 'woocommerce-memberships' ), $grant_access_button ); ?></span>
							<?php

						break;

						case 'purchase' :
						default :

							?>
							<span class="grant-access-purchase"><?php
								/* translators: Placeholder: %s - HTML button */
								printf( __( 'Existing purchases: %s', 'woocommerce-memberships' ), $grant_access_button  ); ?></span>
							<?php

						break;

					}

					?>
				</span>
			</div>
			<?php

			// hides the post visibility option in the publish panel metabox ?>
			<style type="text/css">
				#visibility { display: none !important; }
			</style>
			<?php

			$handler = wc_memberships()->get_utilities_instance()->get_grant_retroactive_access_instance();

			if ( $handler->has_ongoing_job( $plan->get_id() ) ) {

				// opens the modal if there's an ongoing job
				wc_enqueue_js( ' $( "#grant-access-modal-plan-' . $plan->get_id() . '" ).trigger( "click" ); ' );

			} elseif ( $job = $handler->get_job() ) {

				// delete any orphaned or completed job
				$handler->delete_job( $job );
			}

		endif;
	}


	/**
	 * Adds UI elements to the Membership Plan post submit box next to the save/update button.
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 */
	public function post_submitbox_start() {
		global $post;

		if ( is_object( $post ) && isset( $_GET['post'] ) && 'wc_membership_plan' === $post->post_type ) :

			$url = wp_nonce_url( admin_url( 'edit.php?post_type=wc_membership_plan&action=duplicate_plan&post=' . $post->ID ), 'wc-memberships-duplicate-plan_' . $post->ID );

			?>
			<div id="duplicate-action">
				<a class="submitduplicate duplication" href="<?php echo esc_url( $url ); ?>"><?php esc_html_e( 'Make a copy', 'woocommerce-memberships' ); ?></a>
			</div>
			<?php

		endif;
	}


	/**
	 * Returns a membership plan from the database to duplicate.
	 *
	 * @since 1.0.0
	 *
	 * @param int $id
	 * @return \WP_Post|bool
	 */
	private function get_plan_to_duplicate( $id ) {
		global $wpdb;

		$id = absint( $id );

		if ( ! $id ) {
			return false;
		}

		$post = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE ID=%d", $id ) );

		if ( isset( $post->post_type ) && 'revision' === $post->post_type ) {

			$id   = $post->post_parent;
			$post = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE ID=%d", $id ) );
		}

		return $post[0];
	}


	/**
	 * Duplicates a membership plan.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function duplicate_membership_plan() {

		if ( empty( $_REQUEST['post'] ) ) {
			return;
		}

		// get the original post
		$id = isset( $_REQUEST['post'] ) ? absint( $_REQUEST['post'] ) : '';

		check_admin_referer( 'wc-memberships-duplicate-plan_' . $id );

		$post = $this->get_plan_to_duplicate( $id );

		// copy the plan and insert it
		if ( is_object( $post ) ) {

			$new_id = $this->duplicate_plan( $post );

			if ( $new_id > 0 ) {

				/**
				 * Fires after a membership plan has been duplicated.
				 *
				 * If you have written a plugin which uses non-WP database tables to save information about a page you can hook this action to duplicate that data.
				 *
				 * @since 1.0.0
				 *
				 * @param int $new_id new plan ID
				 * @param \WP_Post $post original plan object
				 */
				do_action( 'wc_memberships_duplicate_membership_plan', $new_id, $post );

				wc_memberships()->get_admin_instance()->get_message_handler()->add_message( __( 'Membership plan copied.', 'woocommerce-memberships' ) );

				// redirect to the edit screen for the new draft page
				wp_redirect( admin_url( 'post.php?action=edit&post=' . $new_id ) );
				exit;
			}
		}

		/* translators: Placeholder: %d - membership plan ID */
		wp_die( sprintf( __( 'Membership plan creation failed, could not find original plan to copy: %d', 'woocommerce-memberships' ), (int) $id ) );
	}


	/**
	 * Creates a duplicate membership plan.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param mixed|object $post
	 * @param int $parent (default: 0)
	 * @param string $post_status (default: 'publish')
	 * @return int
	 */
	public function duplicate_plan( $post, $parent = 0, $post_status = 'publish' ) {

		$new_post_id = 0;

		if ( is_object( $post ) ) {

			$new_post_author   = wp_get_current_user();
			$new_post_date     = current_time( 'mysql' );
			$new_post_date_gmt = get_gmt_from_date( $new_post_date );

			if ( $parent > 0 ) {
				$post_parent = $parent;
				$suffix      = '';
			} else {
				$post_parent = $post->post_parent;
				$suffix      = ' ' . __( '(Copy)', 'woocommerce-memberships' );
			}

			// insert the new template in the post table
			$new_post_id = wp_insert_post(
				array(
					'post_author'               => $new_post_author->ID,
					'post_date'                 => $new_post_date,
					'post_date_gmt'             => $new_post_date_gmt,
					'post_content'              => $post->post_content,
					'post_content_filtered'     => $post->post_content_filtered,
					'post_title'                => $post->post_title . $suffix,
					'post_excerpt'              => $post->post_excerpt,
					'post_status'               => $post_status,
					'post_type'                 => $post->post_type,
					'comment_status'            => $post->comment_status,
					'ping_status'               => $post->ping_status,
					'post_password'             => $post->post_password,
					'to_ping'                   => $post->to_ping,
					'pinged'                    => $post->pinged,
					'post_modified'             => $new_post_date,
					'post_modified_gmt'         => $new_post_date_gmt,
					'post_parent'               => $post_parent,
					'menu_order'                => $post->menu_order,
					'post_mime_type'            => $post->post_mime_type
				),
				false
			);

			if ( $new_post_id > 0 ) {

				// copy the meta information
				$this->duplicate_post_meta( $post->ID, $new_post_id );
				// copy rules
				$this->duplicate_plan_rules( $post->ID, $new_post_id );
			}
		}

		return (int) $new_post_id;
	}


	/**
	 * Copies the meta information of a plan to another plan.
	 *
	 * @since 1.0.0
	 *
	 * @param int $id ID of the plan being copied
	 * @param int $new_id the ID of the new plan created
	 */
	private function duplicate_post_meta( $id, $new_id ) {
		global $wpdb;

		$post_meta_infos = $wpdb->get_results( $wpdb->prepare( "
			SELECT meta_key, meta_value
			FROM $wpdb->postmeta
			WHERE post_id=%d
		", absint( $id ) ) );

		if ( count( $post_meta_infos ) > 0 ) {

			$sql_query_sel = array();
			$sql_query     = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";

			foreach ( $post_meta_infos as $meta_info ) {

				$meta_key        = $meta_info->meta_key;
				$meta_value      = $meta_info->meta_value;
				$sql_query_sel[] = $wpdb->prepare( "SELECT %d, '$meta_key', '$meta_value'", $new_id );
			}

			$sql_query .= implode( " UNION ALL ", $sql_query_sel );

			$wpdb->query( $sql_query );
		}
	}


	/**
	 * Copies the plan rules from one plan to another.
	 *
	 * @since 1.0.0
	 *
	 * @param int $id ID of the plan being copied
	 * @param int $new_id the ID of the new plan created
	 */
	private function duplicate_plan_rules( $id, $new_id ) {

		$rules     = wc_memberships()->get_rules_instance()->get_rules_raw();
		$new_rules = array();

		foreach ( $rules as $key => $rule ) {
			// copy rules to new plan
			if ( (int) $id === (int) $rule['membership_plan_id'] ) {
				$new_rule                       = $rule;
				$new_rule['id']                 = uniqid( 'rule_', false );
				$new_rule['membership_plan_id'] = (int) $new_id;
				$new_rules[]                    = $new_rule;
			}
		}

		update_option( 'wc_memberships_rules', array_merge( $rules, $new_rules ) );
	}


}
