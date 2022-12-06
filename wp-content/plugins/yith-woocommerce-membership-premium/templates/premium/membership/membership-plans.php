<?php
/*
 * Template for Membership Plans in frontend
 *
 * @var YITH_WCMBS_Membership $membership
 */

wp_enqueue_script( 'yith_wcmbs_frontend_js' );

yith_wcmbs_late_enqueue_assets( 'membership-history' );
?>

<?php
if ( ! empty( $title ) ) {
	echo '<h2>' . wp_kses_post( $title ) . '</h2>';
}
if ( ! empty( $user_plans ) ) {
	?>
	<div class="yith-wcmbs-my-account-accordion">
		<?php
		foreach ( $user_plans as $membership ) {
			$plan                    = $membership->get_plan();
			$display_content_in_plan = $plan ? $plan->show_contents_in_membership_details() : false;

			$key   = 'yith_wcmbs_membership_plans[' . $membership->plan_id . ']';
			$label = $membership->get_plan_title();

			$expiration = ( $membership->end_date == 'unlimited' ) ? __( 'Unlimited', 'yith-woocommerce-membership' ) : $membership->get_formatted_date( 'end_date' );
			$expiration = apply_filters( 'yith_wcmbs_my_account_membership_status_expiration_date', $expiration, $membership );

			$details = array(
				'starting-date'   => array(
					'title' => __( 'Starting Date', 'yith-woocommerce-membership' ),
					'value' => $membership->get_formatted_date( 'start_date' ),
				),
				'expiration-date' => array(
					'title' => __( 'Expiration Date', 'yith-woocommerce-membership' ),
					'value' => $expiration,
				),
				'status'          => array(
					'title' => __( 'Status', 'yith-woocommerce-membership' ),
					'value' => $membership->get_status_text(),
					'type'  => 'status--' . $membership->status,
				),
			);

			if ( $membership->has_credit_management() ) {
				$details['remaining-credits'] = array(
					'title' => __( 'Remaining Credits', 'yith-woocommerce-membership' ),
					'value' => $membership->get_remaining_credits(),
				);

				if ( $membership->next_credits_update ) {
					$details['next-credits-update'] = array(
						'title' => __( 'Next credits update', 'yith-woocommerce-membership' ),
						'value' => apply_filters( 'yith_wcmbs_next_credits_update_date', $membership->get_formatted_date( 'next_credits_update' ), $membership->next_credits_update ),
					);
				}
			}

			if ( $membership->has_discount() ) {
				$details['discount'] = array(
					'title' => __( 'Shop Discount', 'yith-woocommerce-membership' ),
					'value' => $membership->get_discount_html(),
				);
			}

			?>
			<h3><?php echo esc_html( $label ); ?></h3>
			<div class="yith-wcmbs-my-account-membership-container">

				<div class="yith-wcmbs-membership-details">
					<?php foreach ( $details as $detail_key => $detail ): ?>
						<?php
						$class = "yith-wcmbs-membership-detail--$detail_key";
						if ( isset( $detail['type'] ) ) {
							$class .= " yith-wcmbs-membership-detail--{$detail['type']}";
						}
						?>
						<div class="yith-wcmbs-membership-detail <?php echo esc_attr( $class ); ?>">
							<div class="yith-wcmbs-membership-detail__title"><?php echo wp_kses_post( $detail['title'] ); ?></div>
							<div class="yith-wcmbs-membership-detail__value"><?php echo wp_kses_post( $detail['value'] ); ?></div>
						</div>
					<?php endforeach; ?>
				</div>

				<?php do_action( 'yith_wcmbs_after_membership_details', $membership ); ?>

				<div class="yith-wcmbs-tabs">
					<ul>
						<li>
							<a href="#yith-wcmbs-tab-history-<?php echo esc_attr( $membership->id ); ?>"><?php esc_html_e( 'History', 'yith-woocommerce-membership' ); ?></a>
						</li>
						<?php if ( apply_filters( 'yith_wcmb_force_showing_of_tab_contents', false ) || $display_content_in_plan && $membership->is_active() ) : ?>
							<li>
								<a href="#yith-wcmbs-tab-contents-<?php echo esc_attr( $membership->id ); ?>"><?php echo wp_kses_post( apply_filters( 'yith_wcms_tab_contents_label', __( 'Contents', 'yith-woocommerce-membership' ) ) ); ?></a>
							</li>
						<?php endif; ?>
					</ul>

					<div id="yith-wcmbs-tab-history-<?php echo esc_attr( $membership->id ); ?>" class="yith-wcmbs-membership-history-table-container">

						<?php
						/**
						 * @var YITH_WCMBS_Activity[] $activities
						 */
						$activities = $membership->activities;

						$history_titles = array(
							'status' => __( 'Status', 'yith-woocommerce-membership' ),
							'update' => __( 'Update', 'yith-woocommerce-membership' ),
							'note'   => __( 'Note', 'yith-woocommerce-membership' ),
						);

						if ( ! empty( $activities ) ) : ?>
							<div class="yith-wcmbs-membership-history-activities">
								<?php foreach ( $activities as $activity ) : ?>
									<div class="yith-wcmbs-membership-history-activity">
										<div class="yith-wcmbs-membership-history-activity__date">
											<?php echo wp_kses_post( $activity->get_formatted_date( true, wc_date_format() ) ); ?>
										</div>
										<div class="yith-wcmbs-membership-history-activity__note">
											<?php echo wp_kses_post( $activity->get_i18n_note() ); ?>
										</div>
									</div>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
					</div>

					<?php if ( apply_filters( 'yith_wcmb_force_showing_of_tab_contents', false ) || $display_content_in_plan && $membership->is_active() ) : ?>
						<div id="yith-wcmbs-tab-contents-<?php echo esc_attr( $membership->id ); ?>" class="yith-wcmbs-my-account-list-plan-items-container">
							<?php
							if ( $plan ) {
								$post_types = apply_filters( 'yith_wcmbs_membership_restricted_post_types', array( 'post', 'page', 'product' ) );
								foreach ( $post_types as $post_type ) {
									$page = 1;
									wc_get_template( '/membership/membership-plan-post-type-items.php', compact( 'plan', 'membership', 'post_type', 'page' ), '', YITH_WCMBS_TEMPLATE_PATH );
								}
							}
							?>
						</div>
					<?php endif; ?>
				</div>
			</div>
			<?php
		}

		?>
	</div>
	<?php
} else {
	echo "<p class='yith-wcmbs-my-account-membership-plans__no-membership'>" . wp_kses_post( $no_membership_message ) . "</p>";
}
?>