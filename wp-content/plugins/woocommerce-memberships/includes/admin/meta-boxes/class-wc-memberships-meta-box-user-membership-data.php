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
 * User Membership Data Meta Box
 *
 * @since 1.0.0
 */
class WC_Memberships_Meta_Box_User_Membership_Data extends \WC_Memberships_Meta_Box {


	/**
	 * Constructor.
	 *
	 * @since 1.7.0
	 */
	public function __construct() {

		$this->id       = 'wc-memberships-user-membership-data';
		$this->priority = 'high';
		$this->screens  = array( 'wc_user_membership' );

		parent::__construct();
	}


	/**
	 * Returns the meta box title.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'User Membership Data', 'woocommerce-memberships' );
	}


	/**
	 * Returns membership plan options.
	 *
	 * @since 1.7.0
	 *
	 * @param \WC_Memberships_User_Membership $user_membership User Membership object
	 * @param int $user_id WP User id
	 * @return array
	 */
	public function get_membership_plan_options( $user_membership = null, $user_id = 0 ) {

		$membership_plan_options = array();

		// get user details
		$user = get_userdata( $user_id );

		if ( $user ) {

			// get the available membership plans
			$membership_plans = $this->get_available_membership_plans();
			// get all the user memberships
			$user_memberships = wc_memberships_get_user_memberships( $user->ID );

			foreach ( $membership_plans as $membership_plan ) {
				$exists = false;

				// each user can only have 1 membership per plan.
				// check if user already has a membership for this plan
				if ( ! empty( $user_memberships ) ) {

					foreach ( $user_memberships as $membership ) {

						if ( $membership->get_plan_id() === $membership_plan->get_id() ) {
							$exists = true;
							break;
						}
					}
				}

				// only add plan to options if user is not a member of this plan or
				// if the current membership has this plan.
				// TODO: instead of removing, disable the option once {FN 2016-07-04}
				// see: https://github.com/woocommerce/woocommerce/pull/8024 lands in stable (maybe WC 3.0?)
				if ( ! $exists || $user_membership->get_plan_id() === $membership_plan->get_id() ) {
					$membership_plan_options[ $membership_plan->get_id() ] = $membership_plan->get_formatted_name();
				}
			}
		}

		return $membership_plan_options;
	}


	/**
	 * Displays the membership data meta box.
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_Post $post
	 */
	public function output( \WP_Post $post ) {

		// prepare variables
		$this->post            = $post;
		$this->user_membership = $user_membership = wc_memberships_get_user_membership( $post->ID );
		$this->order           = $order           = $user_membership->get_order();
		$this->product         = $product         = $user_membership->get_product();
		$this->user            = $user            = $this->get_membership_user( $user_membership );

		// bail out if no user
		if ( ! $user ) {
			return;
		}

		$user_memberships = wc_memberships_get_user_memberships( $user->ID );
		$membership_plans = $this->get_available_membership_plans();

		// prepare options
		$status_options = array();
		foreach ( wc_memberships_get_user_membership_statuses() as $status => $labels ) {
			$status_options[ $status ] = $labels['label'];
		}

		/**
		 * Filters status options that appear in the edit user membership screen.
		 *
		 * @since 1.0.0
		 *
		 * @param array $options Associative array of option value => label pairs
		 * @param int $user_membership_id User membership ID
		 */
		$status_options = apply_filters( 'wc_memberships_edit_user_membership_screen_status_options', $status_options, $user_membership->get_id() );

		$current_membership = null;

		?>
		<h3 class="membership-plans">
			<ul class="sections">

				<?php if ( ! empty( $user_memberships ) ) : ?>

						<?php foreach ( $user_memberships as $membership ) : ?>

							<?php if ( $membership->get_plan() ) : ?>

								<li <?php if ( (int) $membership->get_id() === (int) $post->ID ) : $current_membership = $membership->get_id(); ?>class="active"<?php endif; ?>>
									<a href="<?php echo esc_url( get_edit_post_link( $membership->get_id() ) ); ?>"><?php echo wp_kses_post( $membership->get_plan()->get_formatted_name() ); ?></a>
								</li>

							<?php endif; ?>

						<?php endforeach; ?>

					<?php endif; ?>

				<?php if ( count( $user_memberships ) !== count( $membership_plans ) ) : ?>

					<li <?php if ( ! $current_membership ) : ?>class="active"<?php endif; ?>>
						<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=wc_user_membership&user=' . $user->ID ) ); ?>"><?php esc_html_e( 'Add a plan...', 'woocommerce-memberships' ); ?></a>
					</li>

				<?php endif; ?>

			</ul>
		</h3>

		<?php

		// output panels HTML
		$this->output_plan_details_panel( $user_membership, $status_options );
		$this->output_billing_details_panel( $user_membership, $order );

		echo '<div class="clear"></div>';

		// output actions HTML
		$this->output_membership_actions( $user_membership, $post );
	}


	/**
	 * Outputs the membership actions HTML.
	 *
	 * @since 1.7.0
	 *
	 * @param \WC_Memberships_User_Membership $user_membership user membership object
	 * @param \WP_Post $post post object
	 */
	private function output_membership_actions( $user_membership, $post ) {

		?>
		<ul class="user_membership_actions submitbox">
			<?php

			/**
			 * Fires at the start of the user membership actions meta box.
			 *
			 * @since 1.0.0
			 *
			 * @param int $post_id the post id of the wc_user_membership post
			 */
			do_action( 'wc_memberships_user_membership_actions_start', $user_membership->get_id() );

			?>
			<li class="wide">
				<?php

				$user_membership_actions = array();

				if ( current_user_can( 'delete_post', $post->ID ) ) {

					// delete membership
					$user_membership_actions['delete-action'] = array(
						'class' => 'submitdelete deletion delete-membership',
						'link'  => get_delete_post_link( $post->ID, '', true ),
						'text'  => __( 'Delete User Membership', 'woocommerce-memberships' ),
					);

					// can't transfer a newly created post
					if ( 'auto-draft' !== $post->post_status ) {

						// transfer membership
						$user_membership_actions['transfer-action'] = array(
							'class'             => 'button transfer_user_membership',
							'link'              => '#',
							'text'              => __( 'Transfer', 'woocommerce-memberships' ),
							'custom_attributes' => array(
								'data-user-id'       => $user_membership->user_id,
								'data-membership-id' => $post->ID,
							),
						);
					}
				}

				/**
				 * Actions for user membership actions meta box.
				 *
				 * @since 1.4.0
				 *
				 * @param array $user_membership_actions membership admin actions
				 * @param int $post_id the post id of the wc_user_membership post
				 */
				$user_membership_actions = apply_filters( 'wc_memberships_user_membership_actions', $user_membership_actions, $user_membership->get_id() );

				if ( ! empty( $user_membership_actions ) ) :

					foreach( $user_membership_actions as $id => $action ) :

						?>
						<div id="<?php echo $id ?>" class="user-membership-action">
							<?php

							$custom_attr = '';

							if ( ! empty( $action['custom_attributes'] ) ) {

								foreach( $action['custom_attributes'] as $k => $v ) {
									$custom_attr .= esc_attr( $k ) . '="' . esc_attr( $v ) . '" ';
								}
							}

							?>
							<a href="<?php echo esc_url( $action['link'] ); ?>" class="<?php echo esc_attr( $action['class'] ); ?>" <?php echo $custom_attr; ?>>
								<?php echo esc_html( $action['text'] ); ?>
							</a>
						</div>
						<?php

					endforeach;

				endif;

				?>
				<input
					type="submit"
					class="button save_user_membership save_action button-primary tips"
					value="<?php esc_attr_e( 'Save', 'woocommerce-memberships' ); ?>"
					data-tip="<?php esc_attr_e( 'Save/update the membership', 'woocommerce-memberships' ); ?>"
				/>
			</li>
			<?php

			/**
			 * Fires at the end of the user membership actions meta box.
			 *
			 * @since 1.0.0
			 *
			 * @param int $post_id the post id of the wc_user_membership post
			 */
			do_action( 'wc_memberships_user_membership_actions_end', $user_membership->get_id() );

			?>
		</ul>
		<?php
	}


	/**
	 * Outputs the membership plan details panel.
	 *
	 * @since 1.7.0
	 *
	 * @param \WC_Memberships_User_Membership $user_membership
	 * @param array $status_options associative array
	 */
	private function output_plan_details_panel( $user_membership, $status_options ) {
		global $post, $pagenow;

		?>
		<div class="plan-details">
			<h4><?php esc_html_e( 'Membership Details', 'woocommerce-memberships' ); ?></h4>
			<div class="woocommerce_options_panel">
				<?php

				/**
				 * Fires before the membership details in edit user membership screen.
				 *
				 * @since 1.0.0
				 *
				 * @param \WC_Memberships_User_Membership
				 */
				do_action( 'wc_memberships_before_user_membership_details', $user_membership );

				// get plan information
				$membership_plan_options = $this->get_membership_plan_options( $user_membership, $user_membership->get_user_id() );

				if ( $user_membership->get_plan_id() ) {
					$membership_plan_id = $user_membership->get_plan_id();
				} else {
					$membership_plan_id = ! empty( $membership_plan_options ) ? key( $membership_plan_options ) : '';
				}

				$membership_plan = is_numeric( $membership_plan_id ) ? wc_memberships_get_membership_plan( $membership_plan_id ) : null;

				// plan
				woocommerce_wp_select( array(
					'id'      => 'post_parent',
					'label'   => __( 'Plan:', 'woocommerce-memberships' ),
					'options' => $membership_plan_options,
					'value'   => $membership_plan_id,
					'class'   => 'wc-enhanced-select wide',
					'wrapper_class' => 'js-membership-plan',
				) );

				// status
				woocommerce_wp_select( array(
					'id'      => 'post_status',
					'label'   => __( 'Status:', 'woocommerce-memberships' ),
					'options' => $status_options,
					'value'   => 'wcm-' . $user_membership->get_status(),
					'class'   => 'wc-enhanced-select wide',
				) );

				if ( 'post.php' === $pagenow ) {
					// existing membership:
					// get the start date saved for this membership
					$start_date = $user_membership->get_local_start_date( 'Y-m-d' );
				} else {
					// new membership:
					// try determining the membership start date by the plan start access date
					$start_date  = $membership_plan ? $membership_plan->get_local_access_start_date() : date_i18n( 'Y-m-d', current_time( 'timestamp' ) );
				}

				// start date
				woocommerce_wp_text_input( array(
					'id'          => '_start_date',
					'label'       => __( 'Member since:', 'woocommerce-memberships' ),
					'class'       => 'js-user-membership-date',
					'description' => '<code>YYYY-MM-DD</code> <small><a href="#" class="js-calc-plan-date js-calc-plan-start-date">' . esc_html__( 'Update start date to plan start access date', 'woocommerce-memberships' ) . '</a></small>',
					'value'       => substr( $start_date, 0, 10 ),
				) );

				// get the end date saved for this membership
				$end_date = $user_membership->get_local_end_date( 'Y-m-d', false );

				if ( null === $end_date ) {

					// membership is unlimited (default for new manual memberships)
					$end_date = '';

					// however, try determining the membership end date
					// according to the plan expiration date
					if ( 'auto-draft' === $post->post_status ) {
						$end_date = $membership_plan ? $membership_plan->get_expiration_date( $start_date ) : $end_date;
					}
				}

				// end date
				woocommerce_wp_text_input( array(
					'id'          => '_end_date',
					'label'       => __( 'Expires:', 'woocommerce-memberships' ),
					'class'       => 'js-user-membership-date',
					'description' => '<code>YYYY-MM-DD</code> <small><a href="#" class="js-calc-plan-date js-calc-plan-end-date">' . esc_html__( 'Update expiration date to plan length', 'woocommerce-memberships' ) . '</a></small>',
					'value'       => substr( $end_date, 0, 10 ),
				) );

				// display additional paused date if membership is paused
				if ( $paused_date = $user_membership->get_local_paused_date( 'timestamp' ) ) :

					?>
					<p class="form-field">
						<span class="description"><?php
							/* translators: Placeholder: %s - date since the membership was paused */
							printf( __( 'Paused since %s', 'woocommerce-memberships' ),
								date_i18n( wc_date_format(), $paused_date ) . ' ' . date_i18n( wc_time_format(), $paused_date )
							); ?></span>
					</p>
					<?php

				endif;

				/**
				 * Fires after the membership details in edit user membership screen.
				 *
				 * @since 1.0.0
				 *
				 * @param \WC_Memberships_User_Membership
				 */
				do_action( 'wc_memberships_after_user_membership_details', $user_membership );

				?>
			</div>
		</div>
		<?php
	}


	/**
	 * Returns the billing details panel.
	 *
	 * @since 1.7.0
	 *
	 * @param \WC_Memberships_User_Membership $user_membership user membership object
	 * @param \WC_Order $order order object
	 */
	private function output_billing_details_panel( $user_membership, $order ) {

		?>
		<div class="billing-details">

			<h4><?php esc_html_e( 'Billing Details', 'woocommerce-memberships' ); ?></h4>

			<div class="woocommerce_options_panel">
				<?php

				/**
				 * Fires before the billing details in edit user membership screen.
				 *
				 * @since 1.0.0
				 *
				 * @param \WC_Memberships_User_Membership
				 */
				do_action( 'wc_memberships_before_user_membership_billing_details', $user_membership );

				if ( $order ) {

					/* translators: Placeholder: %s - order number */
					$order_ref       = '<a href="' . esc_url( get_edit_post_link( $order->get_id() ) ) . '">' . sprintf(  __( 'Order %s', 'woocommerce-memberships' ), $order->get_order_number() ) . '</a>';
					$order_date      = $order->get_date_created( 'edit' );
					$billing_fields  = [
						__( 'Purchased in:', 'woocommerce-memberships' ) => $order_ref,
						__( 'Order Date:', 'woocommerce-memberships' )   => $order_date ? date_i18n( wc_date_format(), $order_date->getTimestamp() ) : '',
						__( 'Order Total:', 'woocommerce-memberships' )  => $order->get_formatted_order_total(),
					];

				} else {

					$billing_fields = array(
						__( 'No billing details:', 'woocommerce-memberships' ) => esc_html__( 'This membership was created manually.', 'woocommerce-memberships' ),
					);
				}

				/**
				 * Filters the User Membership billing details fields.
				 *
				 * @since 1.7.0
				 *
				 * @param array $billing_fields associative array of labels and data or inputs
				 * @param \WC_Memberships_User_Membership $user_membership the user membership
				 */
				$billing_fields = apply_filters( 'wc_memberships_user_membership_billing_details', $billing_fields, $user_membership );

				foreach ( $billing_fields as $label => $field ) :

					?>
					<p class="form-field billing-detail">
						<label><?php echo esc_html( $label ); ?></label>
						<?php echo $field; ?>
					</p>
					<?php

				endforeach;

				/**
				 * Fires after the billing details in edit user membership screen.
				 *
				 * @since 1.0.0
				 *
				 * @param \WC_Memberships_User_Membership
				 */
				do_action( 'wc_memberships_after_user_membership_billing_details', $user_membership );

				?>
			</div>
		</div>
		<?php
	}


	/**
	 * Saves user membership data.
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id the post id of the corresponding user membership post
	 * @param \WP_Post $post the user membership post object
	 */
	public function update_data( $post_id, \WP_Post $post ) {

		$user_membership = wc_memberships_get_user_membership( $post );

		if ( $user_membership ) {

			$membership_plan   = $user_membership->get_plan();
			$timezone          = wc_timezone_string();
			$mysql_date_format = 'Y-m-d H:i:s';
			$raw_start_date    = ! empty( $_POST['_start_date'] ) ? trim( $_POST['_start_date'] ) : null;
			$raw_end_date      = ! empty( $_POST['_end_date'] )   ? trim( $_POST['_end_date'] )   : null;

			// process the start date
			if ( $raw_start_date && ( $start_date_mysql = wc_memberships_parse_date( $raw_start_date, $mysql_date_format ) ) ) {

				$new_start_date = date( $mysql_date_format, wc_memberships_adjust_date_by_timezone( strtotime( $start_date_mysql ), 'timestamp', $timezone ) );

				// previous start date to compare with incoming raw input format
				$previous_start_date = $user_membership->get_local_start_date( 'Y-m-d' );

				// ensure there's a date change to avoid updating the time to midnight
				if ( empty( $previous_start_date ) || $raw_start_date !== $previous_start_date ) {
					// update the start date (UTC)
					$user_membership->set_start_date( $new_start_date );
				}
			}

			// parse the end date
			if ( $raw_end_date && ( $end_date_mysql = wc_memberships_parse_date( $raw_end_date, 'mysql' ) ) ) {
				$end_date = date( $mysql_date_format, wc_memberships_adjust_date_by_timezone( strtotime( $end_date_mysql ), 'timestamp', $timezone ) );
			} else {
				$end_date = '';
			}

			// get previous end date (UTC)
			$previous_end_date = $user_membership->get_end_date( $mysql_date_format );

			if ( ! empty( $end_date ) && strtotime( $end_date ) <= current_time( 'timestamp', true ) ) {

				// loose check if new and old dates mismatch (end date has been updated)
				if ( $previous_end_date != $end_date ) {

					// if end date is now set to a past date,
					// automatically set status to expired, unless cancelled
					if ( ! $user_membership->is_cancelled() ) {
						$user_membership->update_status( 'expired' );
					}

				} elseif ( $user_membership->has_status( [ 'active', 'free_trial', 'complimentary' ] ) ) {

					// if the end date has not changed compared to previous,
					// but status has been changed to one of the active statuses,
					// remove the end date, so that it does not conflict with the status
					$end_date = '';
				}

			} elseif (    ( ( isset( $_POST['post_status'] ) && 'wcm-expired' === $_POST['post_status'] ) || $user_membership->has_status( 'expired' ) )
			           && ( '' === $end_date || strtotime( $end_date ) > current_time( 'timestamp' ) ) ) {

				// if the status was set to expired, but the new date is in the future,
				// reactivate the membership
				$user_membership->update_status( 'active' );
			}

			// grab the previous end date again to compare with incoming raw input format
			$previous_end_date = $user_membership->get_local_end_date( 'Y-m-d' );

			// update the end date if the day was changed or there is no end date, to avoid updating the end time of the day to midnight
			if ( empty( $end_date ) || empty( $previous_end_date ) || $previous_end_date !== $raw_end_date ) {

				// finally set the end date (UTC)
				$user_membership->set_end_date( $membership_plan && $membership_plan->is_access_length_type( 'fixed' ) ? wc_memberships_adjust_date_by_timezone( $end_date, 'mysql', 'UTC' ) : $end_date );
			}
		}
	}


}
