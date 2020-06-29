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
 * Admin integration class for WooCommerce Subscriptions.
 *
 * @since 1.6.0
 */
class WC_Memberships_Integration_Subscriptions_Admin {


	/**
	 * Adds admin hooks.
	 *
	 * @since 1.6.0
	 */
	public function __construct() {

		// display subscription or membership details accordingly
		add_filter( 'wc_memberships_user_membership_billing_details',          array( $this, 'add_subscription_details' ), 10, 2 );
		add_action( 'wc_membership_plan_options_membership_plan_data_general', array( $this, 'output_subscription_options' ) );
		add_action( 'wc_memberships_restriction_rule_access_schedule_field',   array( $this, 'output_exclude_trial_option' ), 10, 2 );

		// customize membership plans edit screen columns
		add_action( 'manage_wc_membership_plan_posts_custom_column', array( $this, 'membership_plan_screen_columns' ), 40, 2 );

		// customize user memberships edit screen columns
		add_action( 'manage_wc_user_membership_posts_custom_column', array( $this, 'user_membership_screen_columns' ), 40, 2 );

		// add user membership edit screen actions
		add_action( 'wc_memberships_user_membership_actions', array( $this, 'user_membership_meta_box_actions' ), 1, 2 );
		add_filter( 'post_row_actions',                       array( $this, 'user_membership_post_row_actions' ), 20, 2 );

		// meta boxes updates
		add_action( 'wc_memberships_save_meta_box', array( $this, 'update_user_membership_data' ), 10, 3 );
		add_action( 'wc_memberships_save_meta_box', array( $this, 'update_membership_plan_data' ), 10, 3 );

		// adds subscriptions data to system status report output
		add_filter( 'wc_memberships_get_system_status_membership_plan_data', array( $this, 'add_system_status_membership_plan_data' ), 1, 2 );
	}


	/**
	 * Returns a subscription's expiry date.
	 *
	 * @internal
	 *
	 * @since 1.7.0
	 *
	 * @param \WC_Subscription|null $subscription a subscription object
	 * @return string
	 */
	private function get_subscription_expiration( $subscription = null ) {

		return $subscription instanceof \WC_Subscription ? $subscription->get_date_to_display( 'end' ) : '';
	}


	/**
	 * Returns the edit subscription input HTML.
	 *
	 * @internal
	 *
	 * @since 1.7.0
	 *
	 * @param \WC_Memberships_User_Membership $user_membership the membership object
	 * @param \WC_Subscription|null $subscription the subscription object
	 * @return string HTML
	 */
	private function get_edit_subscription_input( $user_membership, $subscription = null ) {

		if ( $subscription && $subscription instanceof \WC_Subscription ) {
			$subscription_id   = $subscription->get_id();
			$subscription_url  = get_edit_post_link( $subscription_id );
			$subscription_link = '<a href="' . esc_url( $subscription_url ) . '">' . esc_html( $subscription_id ) . '</a>';
		} else {
			$subscription_id   = '';
			$subscription_link = esc_html__( 'Membership not linked to a Subscription', 'woocommerce-memberships' );
		}

		/* translators: Placeholders: %1$s - link to a Subscription, %2$s - opening <a> HTML tag, %3%s - closing </a> HTML tag */
		$input = sprintf( __( '%1$s - %2$sEdit Link%3$s', 'woocommerce-memberships' ),
			$subscription_link,
			'<a href="#" class="js-edit-subscription-link-toggle">',
			'</a>'
		);

		ob_start();

		?><br>
		<span class="wc-memberships-edit-subscription-link-field">
			<select
				class="sv-wc-enhanced-search"
				id="_subscription_id"
				name="_subscription_id"
				data-action="wc_memberships_edit_membership_subscription_link"
				data-nonce="<?php echo wp_create_nonce( 'edit-membership-subscription-link' ); ?>"
				data-placeholder="<?php esc_attr_e( 'Link to a Subscription or keep empty to leave unlinked', 'woocommerce-memberships' ); ?>"
				data-allow_clear="true">
				<?php if ( $subscription instanceof \WC_Subscription ) : ?>
					<option value="<?php echo $subscription_id; ?>"><?php echo $subscription_id; ?></option>
				<?php endif; ?>
			</select>
		</span>
		<?php

		Framework\SV_WC_Helper::render_select2_ajax();

		$input .= ob_get_clean();

		// toggle editing of subscription id link
		wc_enqueue_js( '
			$( ".js-edit-subscription-link-toggle" ).on( "click", function( e ) { e.preventDefault(); $( ".wc-memberships-edit-subscription-link-field" ).toggle(); } ).click();
		' );

		return $input;
	}


	/**
	 * Adds subscription details to the edit membership screen billing details section.
	 *
	 * @internal
	 *
	 * @since 1.9.0
	 *
	 * @param array $fields an associative array of billing detail fields, in format label => field html
	 * @param \WC_Memberships_User_Membership $user_membership post object
	 * @return array
	 */
	public function add_subscription_details( $fields, $user_membership ) {

		if ( $user_membership instanceof \WC_Memberships_User_Membership ) {

			$user_membership = new \WC_Memberships_Integration_Subscriptions_User_Membership( $user_membership->post );
			$subscription    = $user_membership->get_subscription();
			$next_payment    = $user_membership->get_next_bill_on_local_date( wc_date_format() );

			$fields[ __( 'Subscription:', 'woocommerce-memberships' ) ] = $this->get_edit_subscription_input( $user_membership, $subscription );
			$fields[ __( 'Next Bill On:', 'woocommerce-memberships' ) ] = null !== $next_payment ? $next_payment : esc_html__( 'N/A', 'woocommerce-memberships' );

			// maybe replace the expiration date input
			if ( $subscription && $user_membership->get_plan_id() && ! wc_memberships()->get_integrations_instance()->get_subscriptions_instance()->get_plans_instance()->grant_access_while_subscription_active( $user_membership->get_plan_id() ) ) {

				$subscription_expires = $this->get_subscription_expiration( $subscription );

				wc_enqueue_js( '
					$( "._end_date_field" ).find( ".js-user-membership-date, .ui-datepicker-trigger, .description" ).hide();
					$( "._end_date_field" ).append( "<span>' . esc_html( $subscription_expires ) . '</span>" );
				' );
			}
		}

		return $fields;
	}


	/**
	 * Displays subscriptions options and JS in the membership plan edit screen.
	 *
	 * @internal
	 *
	 * @since 1.6.0
	 */
	public function output_subscription_options() {
		global $post;

		$integration      = wc_memberships()->get_integrations_instance()->get_subscriptions_instance();
		$has_subscription = $integration->has_membership_plan_subscription( $post->ID );

		if ( ! $integration->get_plans_instance()->grant_access_while_subscription_active( $post->ID ) ) {
			return;
		}

		// get the membership plan tied to membership
		$subscription_plan = new \WC_Memberships_Integration_Subscriptions_Membership_Plan( $post );

		?>
		<p class="form-field plan-subscription-access-length-field js-show-if-has-subscription <?php if ( ! $has_subscription ) : ?>hide<?php endif; ?>">
			<label for="_subscription_access_length"><?php esc_html_e( 'Subscription-tied Membership length', 'woocommerce-memberships' ); ?></label>

			<span class="plan-subscription-access-length-selectors">
				<?php

				// prepare subscription access length type toggler options
				$subscription_access_length_period_toggler_options = array(
					'unlimited'    => __( 'Unlimited', 'woocommerce-memberships' ),
					/* translators: Membership of an unlimited length */
					'subscription' => __( 'Subscription length', 'woocommerce-memberships' ),
					/* translators: Specify the length of a membership */
					'specific'     => __( 'Specific length', 'woocommerce-memberships' ),
					/* translators: Membership set to expire in a specified date */
					'fixed'        => __( 'Fixed dates', 'woocommerce-memberships' )
				);

				// get the default/saved access length type option
				$current_subscription_access_length_type = $subscription_plan->get_access_length_type();

				foreach ( $subscription_access_length_period_toggler_options as $value => $label ) :

					?>
					<label class="label-radio">
						<input
							type="radio"
							name="_subscription_access_length"
							class="js-subscription-access-length-period-selector js-subscription-access-length-type"
							value="<?php echo esc_attr( $value ); ?>"
							<?php checked( $value, $current_subscription_access_length_type ); ?>
						/> <?php echo esc_html( strtolower( $label ) ); ?>
					</label>
					<?php

				endforeach;

				echo wc_help_tip( __( 'When does the membership tied to a subscription expire?', 'woocommerce-memberships' ) );

				?>
			</span>

			<span class="subscription-access-notice description js-show-if-subscription-access-length-unlimited" <?php if ( 'unlimited' !== $current_subscription_access_length_type ) : ?>style="display: none;"<?php endif; ?>>
				<?php esc_html_e( 'The membership will be active indefinitely, even after the subscription billing cycle is complete, as long as it has been fully paid.', 'woocommerce-memberships' ); ?>
					<?php echo wc_help_tip( __( 'When unlimited access is granted via the purchase of a subscription, the membership will be active for the period of the subscription length, and will stay active beyond that as long as the customer successfully completed the subscription billing cycle.', 'woocommerce-memberships' ) ); ?>
			</span>

			<span class="subscription-access-notice description js-show-if-subscription-access-length-subscription" <?php if ( 'subscription' !== $current_subscription_access_length_type ) : ?>style="display: none;"<?php endif; ?>>
				<?php esc_html_e( 'The membership will be active as long as the purchased subscription stays active.', 'woocommerce-memberships' ); ?>
				<?php echo wc_help_tip( __( 'When access is granted via the purchase of a subscription, the membership length becomes tied to the length of the subscription.', 'woocommerce-memberships' ) ); ?>
			</span>

			<span class="plan-access-length-specific plan-subscription-access-length-specific js-show-if-subscription-access-length-specific <?php if ( 'specific' !== $current_subscription_access_length_type ) : ?>hide<?php endif;?>">
				<?php

				$access_length_periods = wc_memberships()->get_plans_instance()->get_membership_plans_access_length_periods( true );
				$access_length_amount  = 'specific' === $current_subscription_access_length_type ? $subscription_plan->get_access_length_amount() : 1;
				$access_length_period  = $subscription_plan->get_access_length_period();

				?>
				<span>
					<input
						type="number"
						name="_subscription_access_length_amount"
						id="_subscription_access_length_amount"
						class="subscription_access_length-amount"
						value="<?php echo esc_attr( max( 1, (int) $access_length_amount ) ); ?>"
						min="1"
						step="1"
					/>
					<?php ?>
					<select
						name="_subscription_access_length_period"
						id="_subscription_access_length_period"
						class="short subscription_access_length-period js-subscription-access-length-period-selector">
						<?php foreach ( $access_length_periods as $key => $label ) : ?>
							<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $access_length_period ); ?>><?php echo esc_html( strtolower( $label ) ); ?></option>
						<?php endforeach; ?>
					</select>
				</span>

				<span class="subscription-access-notice description">
					<?php esc_html_e( 'The membership will be active for the length specified above, regardless of billing dates, so long as the subscription has been fully paid.', 'woocommerce-memberships' ); ?>
					<?php echo wc_help_tip( __( 'When membership access is granted via the purchase of a subscription, then membership length will last for the specified period, regardless of the subscription length, as long as the customer pays for the subscription costs.', 'woocommerce-memberships' ) ); ?>
				</span>

			</span>

			<span class="plan-access-length-fixed plan-subscription-access-length-fixed js-show-if-access-length-fixed <?php if ( 'fixed' !== $current_subscription_access_length_type ) : ?>hide<?php endif;?>">

				<?php

				// get saved/default start and end access dates to populate fields
				$current_subscription_access_start_date = date_i18n( 'Y-m-d', $subscription_plan->get_local_access_start_date( 'timestamp' ) );
				$current_subscription_access_end_time   = $subscription_plan->get_local_access_end_date( 'timestamp' );
				$current_subscription_access_end_date   = empty( $current_subscription_access_end_time ) ? date_i18n( 'Y-m-d', strtotime( 'tomorrow', $subscription_plan->get_local_access_start_date( 'timestamp' ) ) ) : date( 'Y-m-d', $current_subscription_access_end_time );

				?>
				<span>
					<label for="_subscription_access_start_date"><?php esc_html_e( 'Start date', 'woocommerce-memberships' ); ?></label>
					<input
						type="text"
						id="_subscription_access_start_date"
						name="_subscription_access_start_date"
						class="subscription_access_length-start-date js-plan-access-set-date"
						value="<?php echo esc_attr( $current_subscription_access_start_date ); ?>"
					><span class="description"><?php echo 'YYYY-MM-DD'; ?></span>
				</span>
				<span>
					<label for="_subscription_access_end_date"><?php esc_html_e( 'End date', 'woocommerce-memberships' ); ?></label>
					<input
						type="text"
						id="_subscription_access_end_date"
						name="_subscription_access_end_date"
						class="subscription_access_length-end-date js-plan-access-set-date"
						value="<?php echo esc_attr( $current_subscription_access_end_date ); ?>"
					/><span class="description"><?php echo 'YYYY-MM-DD'; ?></span>
				</span>

				<span class="subscription-access-notice description">
					<?php esc_html_e( 'The membership will be active between the selected dates, regardless of billing dates, so long as the subscription has been fully paid.', 'woocommerce-memberships' ); ?>
					<?php echo wc_help_tip( __( 'When membership access is granted via the purchase of a subscription, the membership will last until the specified date, regardless of the subscription sign up date and the subscription length, as long as the customer pays for the subscription costs.', 'woocommerce-memberships' ) ); ?>
				</span>

			</span>

		</p>
		<?php

		// check if a membership plan has subscription(s):
		// if the current membership plan has at least one subscription product that grants access, enable the subscription-specific controls
		wc_enqueue_js( '

			var checkIfPlanHasPurchaseAccess = function() {

				var $access_method_options = $( ".plan-access-method-selectors" ).find( \'input[name="_access_method"]\' );

				$access_method_options.on( "change", function( e ) {

					if ( "purchase" !== $( this ).val() ) {
						$( ".plan-access-length-field" ).show();
						$( ".plan-subscription-access-length-field" ).hide();
						checkIfPlanHasSubscription();
					} else {
						$( ".plan-subscription-access-length-field" ).show();
						checkIfPlanHasSubscription();
					}
				} );
			}

			var checkIfPlanHasSubscription = function() {

				var product_ids = $( "#_product_ids" ).val() || [];
				    product_ids = $.isArray( product_ids ) ? product_ids : product_ids.split( "," );

				$.get( wc_memberships_admin.ajax_url, {
					action:      "wc_memberships_membership_plan_has_subscription_product",
					security:    "' . wp_create_nonce( 'check-subscriptions' ) . '",
					product_ids: product_ids,
				}, function ( subscription_products ) {

					var option = $( ".plan-access-method-selectors" ).find( \'input[name="_access_method"]:checked\' ).val(),
						action = "purchase" == option && subscription_products && subscription_products.length ? "removeClass" : "addClass",
						$field = $( ".plan-access-length-field" );

					$( ".js-show-if-has-subscription" )[ action ]( "hide" );

					if ( subscription_products && subscription_products.length === product_ids.length && "purchase" == option ) {
						$field.hide();
					} else {
						$field.show();
					}
				} );
			}

			checkIfPlanHasSubscription();
			checkIfPlanHasPurchaseAccess();

			$( "#_product_ids" ).on( "change", function() {
				checkIfPlanHasPurchaseAccess();
				checkIfPlanHasSubscription();
			} );

			var $access_length_input     = $( ".plan-subscription-access-length-selectors" ),
			    $access_length_options   = $access_length_input.find( \'input[name="_subscription_access_length"]\' ),
			    $access_length_field     = $access_length_input.closest( "p.form-field" ),
			    $unlimited_length_tip    = $access_length_field.find( ".js-show-if-subscription-access-length-unlimited" ),
			    $subscription_length_tip = $access_length_field.find( ".js-show-if-subscription-access-length-subscription" ),
			    $specific_length_input   = $access_length_field.find( ".plan-subscription-access-length-specific" ),
			    $fixed_length_input      = $access_length_field.find( ".plan-subscription-access-length-fixed" );

			$access_length_options.on( "change", function( e ) {

				var access_length = $( this ).val();

				$subscription_length_tip.hide();
				$unlimited_length_tip.hide();

				$fixed_length_input.addClass( "hide" );
				$specific_length_input.addClass( "hide" );

				switch ( access_length ) {

					case "specific" :
						$specific_length_input.removeClass( "hide" );
					break;

					case "fixed" :
						$fixed_length_input.removeClass( "hide" );
					break;

					case "subscription" :
					default :
						$subscription_length_tip.show();
					break;

					case "unlimited" :
						$unlimited_length_tip.show();
					break;

				}
			} );
		' );

		?>
		<style type="text/css">
			#wc-memberships-membership-plan-data .subscription-access-notice {
				clear: both;
				display: block;
				margin: 0;
			}
		</style>
		<?php
	}


	/**
	 * Displays subscriptions options for a restriction rule.
	 *
	 * This method will be called both in the membership plan screen, as well as on any individual product screens.
	 *
	 * @internal
	 *
	 * @since 1.6.0
	 *
	 * @param \WC_Memberships_Membership_Plan_Rule $rule rule object
	 * @param int|string $index
	 */
	public function output_exclude_trial_option( $rule, $index ) {

		$integration      = wc_memberships()->get_integrations_instance()->get_subscriptions_instance();
		$has_subscription = $rule->get_membership_plan_id() ? $integration->has_membership_plan_subscription( $rule->get_membership_plan_id() ): false;
		$type             = $rule->get_rule_type();

		?>
		<span class="rule-control-group rule-control-group-access-schedule-trial <?php if ( ! $has_subscription ) : ?>hide<?php endif; ?> js-show-if-has-subscription">

			<input type="checkbox"
				   name="_<?php echo esc_attr( $type ); ?>_rules[<?php echo $index; ?>][access_schedule_exclude_trial]"
				   id="_<?php echo esc_attr( $type ); ?>_rules_<?php echo $index; ?>_access_schedule_exclude_trial"
				   value="yes" <?php checked( $rule->is_access_schedule_excluding_trial(), true ); ?>
				   class="access_schedule-exclude-trial"
				   <?php if ( ! $rule->current_user_can_edit() ) : ?>disabled<?php endif; ?> />

			<label for="_<?php echo esc_attr( $type ); ?>_rules_<?php echo $index; ?>_access_schedule_exclude_trial" class="label-checkbox">
				<?php esc_html_e( 'Start after trial', 'woocommerce-memberships' ); ?>
			</label>

		</span>
		<?php
	}


	/**
	 * Adds User Membership admin post row actions.
	 *
	 * Filters the post row actions in the user memberships edit screen.
	 *
	 * @internal
	 *
	 * @since 1.6.0
	 *
	 * @param array $actions
	 * @param \WP_Post $post \WC_Memberships_User_Membership post object
	 * @return array
	 */
	public function user_membership_post_row_actions( $actions, $post ) {

		$post_id = $post instanceof \WP_Post ? $post->ID : $post;

		if ( is_numeric( $post_id ) && current_user_can( 'delete_post', (int) $post_id ) ) {

			$integration     = wc_memberships()->get_integrations_instance()->get_subscriptions_instance();
			$user_membership = wc_memberships_get_user_membership( $post );

			if ( $integration && $user_membership && wc_memberships_is_user_membership_linked_to_subscription( $user_membership ) ) {

				$subscription = $integration->get_subscription_from_membership( $user_membership->get_id() );

				if ( $subscription instanceof \WC_Subscription ) {

					$actions['delete-with-subscription'] = '<a class="delete-membership-and-subscription" title="' . esc_attr__( 'Delete this membership permanently and the subscription associated with it', 'woocommerce-memberships' ) . '" href="#" data-user-membership-id="' . esc_attr( $user_membership->get_id() ) . '" data-subscription-id="' . esc_attr( $subscription->get_id() ) . '">' . esc_html__( 'Delete with subscription', 'woocommerce-memberships' ) . '</a>';
				}
			}
		}

		return $actions;
	}


	/**
	 * Customizes the access length in membership plans edit screen.
	 *
	 * Displays the access length for subscription-based plans.
	 *
	 * @internal
	 *
	 * @since 1.7.0
	 *
	 * @param string $column column slug
	 * @param int $post_id the current post ID
	 */
	public function membership_plan_screen_columns( $column, $post_id ) {
		global $post;

		$membership_plan   = wc_memberships_get_membership_plan( $post_id );
		$subscription_plan = new \WC_Memberships_Integration_Subscriptions_Membership_Plan( $post );

		if (    'length' === $column
		     && $membership_plan
		     && $membership_plan->is_access_method( 'purchase' )
		     && $subscription_plan->has_subscription() ) {

			$human_length      = $membership_plan->get_human_access_length();
			$sub_human_length  = $subscription_plan->get_human_access_length();
			$subscription_tied = $subscription_plan->is_access_length_type( 'subscription' );

			if ( ( $human_length !== $sub_human_length ) || $subscription_tied ) {

				if ( $subscription_tied ) {
					$sub_human_length = __( 'Subscription length', 'woocommerce-memberships' );
				}

				/* translators: Placeholder: %s - length of a Subscription-tied membership plan */
				echo $membership_plan->get_products( true ) ? '<br><small>' . sprintf( __( 'Subscription based: %s', 'woocommerce-memberships' ), $sub_human_length ) . '</small>' : $sub_human_length;
			}
		}
	}


	/**
	 * Customizes the member since column in the user memberships edit screen.
	 *
	 * Displays a link to the Subscription edit screen for Subscription-tied memberships.
	 *
	 * @internal
	 *
	 * @since 1.10.1
	 *
	 * @param string $column current column
	 * @param int $user_membership_id the post ID matching the user membership ID
	 */
	public function user_membership_screen_columns( $column, $user_membership_id ) {

		if (    'member_since' === $column
		     && 'wc_user_membership' === get_post_type( $user_membership_id )
		     && wc_memberships_is_user_membership_linked_to_subscription( $user_membership_id ) ) {

			$user_membership = new \WC_Memberships_Integration_Subscriptions_User_Membership( $user_membership_id );

			if ( $subscription_id = $user_membership->get_subscription_id() ) {
				/* translators: Placeholder: %s - order ID */
				printf( '<span class="member-since-subscription"><small>' . __( 'Subscription: %s', 'woocommerce-memberships' ) . '</small></span>', '<a href="' . esc_url( get_edit_post_link( $subscription_id ) ) . '">#' . esc_html( $subscription_id ) . '</a>' );
			}
		}
	}


	/**
	 * Adds User Membership meta box actions.
	 *
	 * Filters the user membership meta box actions in admin.
	 *
	 * @internal
	 *
	 * @since 1.6.0
	 *
	 * @param array $actions associative array
	 * @param int $user_membership_id \WC_Membership_User_Membership post ID
	 * @return array
	 */
	public function user_membership_meta_box_actions( $actions, $user_membership_id ) {

		if ( current_user_can( 'delete_post', $user_membership_id ) ) {

			$integration  = wc_memberships()->get_integrations_instance()->get_subscriptions_instance();
			$subscription = $integration ? $integration->get_subscription_from_membership( $user_membership_id ) : null;

			if ( $subscription instanceof \WC_Subscription ) {

				$actions = array_merge( [
					'delete-with-subscription' => [
						'class'             => 'submitdelete delete-membership-and-subscription',
						'link'              => '#',
						'text'              => __( 'Delete User Membership with Subscription', 'woocommerce-memberships' ),
						'custom_attributes' => [
							'data-user-membership-id' => $user_membership_id,
							'data-subscription-id'    => $subscription->get_id(),
							'data-tip'                => __( 'Delete this membership permanently and the subscription associated with it', 'woocommerce-memberships' ),
						],
					],
				], $actions );
			}
		}

		return $actions;
	}


	/**
	 * Updates User Membership data when saving.
	 *
	 * @internal
	 *
	 * @since 1.7.0
	 *
	 * @param array $posted_data $_POST data
	 * @param string $meta_box_id the id of the meta box being saved
	 * @param int $post_id the Membership Plan id
	 */
	public function update_user_membership_data( $posted_data, $meta_box_id, $post_id ) {

		// note: we need to instantiate plan object via post object and not simply the id in this metabox context
		$membership_post = get_post( $post_id );
		$integration     = wc_memberships()->get_integrations_instance()->get_subscriptions_instance();

		// bail out if we are in the wrong meta box or for some reason post is invalid
		if ( ! $integration || 'wc-memberships-user-membership-data' !== $meta_box_id || ! $membership_post instanceof \WP_Post ) {
			return;
		}

		$subscription_membership = new \WC_Memberships_Integration_Subscriptions_User_Membership( $membership_post );
		$new_subscription_id     = ! empty( $posted_data['_subscription_id'] ) ? (int) $posted_data['_subscription_id'] : null;
		$subscription            = ! empty( $new_subscription_id ) ? wcs_get_subscription( $new_subscription_id ) : null;

		// the membership is already linked to a subscription
		if ( wc_memberships_is_user_membership_linked_to_subscription( $subscription_membership ) ) {

			$old_subscription_id = $subscription_membership->get_subscription_id();

			if ( empty( $new_subscription_id ) ) {

				// new ID is void, unlink the membership from the subscription
				$integration->unlink_membership( $subscription_membership->get_id(), $old_subscription_id );

			} elseif ( $new_subscription_id !== $old_subscription_id && $subscription ) {

				// the two IDs differ, link the membership to a new subscription
				$subscription_membership->set_subscription_id( $new_subscription_id );

				// maybe update the trial end date
				if ( $trial_end = wc_memberships()->get_integrations_instance()->get_subscriptions_instance()->get_subscription_event_date( $subscription, 'trial_end' ) ) {
					$subscription_membership->set_free_trial_end_date( $trial_end );
				}
			}

		// the membership is not linked to a subscription
		} elseif ( ! empty( $new_subscription_id ) && $subscription ) {

			// link the subscription to the membership
			$subscription_membership->set_subscription_id( $new_subscription_id );

			// maybe update the trial end date
			if ( $trial_end = $integration->get_subscription_event_date( $subscription, 'trial_end' ) ) {
				$subscription_membership->set_free_trial_end_date( $trial_end );
			}
		}
	}


	/**
	 * Updates the Membership Plan data when saving.
	 *
	 * @internal
	 *
	 * @since 1.7.0
	 *
	 * @param array $posted_data $_POST data
	 * @param string $meta_box_id the ID of the meta box being saved
	 * @param int $post_id the Membership Plan ID
	 */
	public function update_membership_plan_data( $posted_data, $meta_box_id, $post_id ) {

		if ( 'wc-memberships-membership-plan-data' !== $meta_box_id ) {
			return;
		}

		// note: instantiate plan object via post object and not simply the id in this metabox context
		$subscription_membership_plan = new \WC_Memberships_Integration_Subscriptions_Membership_Plan( get_post( $post_id ) );

		// update meta for the subscription-tied membership plan
		if ( $subscription_membership_plan ) {

			// reset subscription-tied membership length information
			$subscription_membership_plan->delete_access_length();
			$subscription_membership_plan->delete_access_start_date();
			$subscription_membership_plan->delete_access_end_date();
			$subscription_membership_plan->delete_installment_plan();

			// save subscription-tied membership length when not equal to subscription length
			if (    isset( $posted_data['_access_method'], $posted_data['_subscription_access_length'] )
			     && 'purchase' === $posted_data['_access_method']
			     && $posted_data['_subscription_access_length'] ) {

				// unless tied to the subscription length, all other length options
				// are assumed to use the subscription as an installment plan
				if ( 'subscription' !== $posted_data['_subscription_access_length'] ) {
					$subscription_membership_plan->set_installment_plan();
				}

				if (    'specific' === $posted_data['_subscription_access_length']
				     && isset( $posted_data['_subscription_access_length_amount'], $posted_data['_subscription_access_length_period'] ) ) {

					$access_length = sprintf( '%d %s',
						max( 1, (int) $_POST['_subscription_access_length_amount'] ),
						sanitize_text_field( $_POST['_subscription_access_length_period']
					) );

					// use subscription installments, ends in set period relative from sign up date
					$subscription_membership_plan->set_access_length( $access_length );

				} elseif (    'fixed' === $_POST['_subscription_access_length']
				           && isset( $_POST['_subscription_access_start_date'], $_POST['_subscription_access_end_date'] )
				           && ( $subscription_access_start_date = wc_memberships_parse_date( $_POST['_subscription_access_start_date'], 'mysql' ) )
				           && ( $subscription_access_end_date   = wc_memberships_parse_date( $_POST['_subscription_access_end_date'], 'mysql' ) ) ) {

					$timezone_string                = wc_timezone_string();
					$subscription_access_start_time = strtotime( 'today', strtotime( $subscription_access_start_date ) );
					$subscription_access_end_time   = strtotime( 'today', strtotime( $subscription_access_end_date ) );

					// use subscription installments, fixed start date regardless of membership access
					$subscription_membership_plan->set_access_start_date(
						date( 'Y-m-d H:i:s', wc_memberships_adjust_date_by_timezone( $subscription_access_start_time, 'timestamp', $timezone_string ) )
					);

					// use subscription installments, fixed end date regardless of membership duration
					$subscription_membership_plan->set_access_end_date(
						date( 'Y-m-d H:i:s', wc_memberships_adjust_date_by_timezone( $subscription_access_end_time, 'timestamp', $timezone_string ) )
					);

					// sanity check: start date can't be after end date
					if ( $subscription_access_start_time >= $subscription_access_end_time ) {

						/** @see WC_Memberships_Meta_Box_Membership_Plan_Data::update_data() */
						wc_memberships()->get_admin_instance()->get_message_handler()->add_error(
							/* translators: Placeholder: %s - notice message */
							sprintf( __( 'Subscription-tied membership plan: %s', 'woocommerce-memberships' ),
								__( 'You cannot set an access start date after the access end date, or on the same day. The two dates have been set one day apart from each other.', 'woocommerce-memberships' )
							)
						);
					}

					if ( $subscription_access_end_time < strtotime( 'midnight', current_time( 'timestamp' ) ) ) {

						/** @see WC_Memberships_Meta_Box_Membership_Plan_Data::update_data() */
						wc_memberships()->get_admin_instance()->get_message_handler()->add_error(
							/* translators: Placeholder: %s - notice message */
							sprintf( __( 'Subscription-tied membership plan: %s', 'woocommerce-memberships' ),
								__( 'You have chosen an end date that is set in the past. The selected access dates have been saved, but please make sure that this is correct.', 'woocommerce-memberships' )
							)
						);
					}
				}
			}
		}
	}


	/**
	 * Adds subscription details to the system status report data for a plan.
	 *
	 * @internal
	 *
	 * @since 1.12.0
	 *
	 * @param array $data system status report data
	 * @param \WC_Memberships_Membership_Plan $plan plan object
	 * @return array
	 */
	public function add_system_status_membership_plan_data( $data, $plan ) {

		if ( is_array( $data ) && $plan instanceof \WC_Memberships_Membership_Plan ) {

			$subscription_plan    = new \WC_Memberships_Integration_Subscriptions_Membership_Plan( $plan->post );
			$has_subscription     = $subscription_plan->has_subscription();
			$has_installment      = $has_subscription && $subscription_plan->has_installment_plan();

			$yes  = __( 'Yes', 'woocommerce-memberships' );
			$no   = __( 'No', 'woocommerce-memberships' );

			if ( $has_subscription ) {
				$html = $subscription_plan->is_subscription_only() ? $yes : __( 'Optional', 'woocommerce-memberships' );
			} else {
				$html = $no;
			}

			$data['has_subscription'] = [
				'label' => __( 'Subscription', 'woocommerce-memberships' ),
				'value' => $has_subscription,
				'html'  => $html,
			];

			if ( $has_subscription ) {

				$data['has_installment'] = [
					'label' => __( 'Installment plan', 'woocommerce-memberships' ),
					'value' => $has_installment,
					'html'  => ! $has_installment ? $no : $html,
				];
			}
		}

		return $data;
	}


}
