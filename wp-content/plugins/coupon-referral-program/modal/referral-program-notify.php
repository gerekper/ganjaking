<?php
/**
 * The fronend-specific functionality of the plugin.
 *
 * @link       https://woocommerce.com/
 * @since      1.0.0
 *
 * @package    Coupon_Referral_Program
 * @subpackage Coupon_Referral_Program/modal
 */

/**
 * Exit if accessed directly
 */
/*  Popup Style */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$user_id    = get_current_user_id();
$public_obj = new Coupon_Referral_Program_Public( 'Coupon Referral Program', '1.0.0' );
?>
	<style type="text/css">
	<?php echo wp_kses_post( Coupon_Referral_Program_Public::get_custom_style_popup_btn() ); ?>
	span.modal__close:after {
		background-image: url('<?php echo esc_html( COUPON_REFERRAL_PROGRAM_DIR_URL ) . 'public/images/cancel.png'; ?>');
	}
	</style>
	<div id="mwb_modal" class="mwb_modal mwb_modal__bg">
		<div class="mwb_modal__dialog mwb-pr-dialog">
			<div class="mwb_modal__content">
				<div class="mwb_modal__header">
					<div class="mwb_modal__title">
					<h2 class="mwb_modal__title-text"><?php esc_html_e( 'Invite & Earn', 'coupon-referral-program' ); ?></h2>
					</div>
					<span class="mwb_modal__close">
					X
					</span>
				</div>
				<div class="mwb_modal__text mwb-pr-popup-text">
					<?php
					if ( is_user_logged_in() ) {
						$mwb_crp_img = COUPON_REFERRAL_PROGRAM_DIR_URL . 'public/images/background.jpg';
						?>
					<div class="mwb-pr-popup-body mwb_cpr_logged_wrapper">
						<?php
						$signup_text = get_option( 'signup_popup_text', false );
						if ( ! empty( $signup_text ) ) {
							$signup_text = str_replace( '{crp_referral_code}', do_shortcode( '[crp_referral_code]' ), $signup_text );
							$signup_text = str_replace( '{crp_referral_link}', do_shortcode( '[crp_referral_link]' ), $signup_text );
							/**
							 * Filter to format the content.
							 *
							 * @since 1.6.5
							 * @param string $signup_text .
							 */
							echo wp_kses_post( apply_filters( 'the_content', $signup_text ) );
							return;
						}
						?>
						<div class="mwb-popup-points-label" style="background-image: url('<?php echo esc_html( $mwb_crp_img ); ?>')">
							<!-- Referal code -->
							<?php $public_obj->get_referral_code_on_popup( $user_id ); ?>
							<?php if ( $public_obj->is_social_sharing_enabled() ) { ?>
						<h6><?php esc_html_e( 'Referral Link: ', 'coupon-referral-program' ); ?></h6>
						<div class="mwb_cpr_refrral_code_copy">
							<p id="mwb_cpr_copy">
								<code id="mwb_cpr_copyy" style="background-color: <?php echo wp_kses_post( Coupon_Referral_Program_Admin::get_selected_color() ); ?>">
								<?php echo esc_html( $public_obj->get_referral_link( $user_id ) ); ?>
								</code>
								<span class="mwb_cpr_copy_btn_wrap">
								<button class="mwb_cpr_btn_copy mwb_tooltip" data-clipboard-target="#mwb_cpr_copyy" aria-label="copied">
									<span class="mwb_tooltiptext"><?php esc_html_e( 'Copy', 'coupon-referral-program' ); ?></span>
									<span class="mwb_tooltiptext_copied mwb_tooltiptext"><?php esc_html_e( 'Copied', 'coupon-referral-program' ); ?></span>
									<img src="<?php echo esc_html( COUPON_REFERRAL_PROGRAM_DIR_URL ) . 'admin/images/copy.png'; ?>" alt="">
								</button>
								</span>
							</p>
						</div>
						<?php } ?>
						</div>
					<div class="mwb-pr-popup-tab-wrapper ">
						<span id="notify_user_gain_tab" class="tab-link active" style="background-color: <?php echo wp_kses_post( Coupon_Referral_Program_Admin::get_selected_color() ); ?>"><?php esc_html_e( 'Steps to Earn More', 'coupon-referral-program' ); ?></span>
					</div>
					<!--mwb-pr-popup- rewards tab-container-->
					<div class="mwb-pr-popup-tab-container active" id="notify_user_earn_more_section">
						<ul class="mwb-pr-popup-tab-listing mwb-pr-popup-rewards-tab-listing">
						<li>
							<div class="mwb-pr-popup-left-rewards">                      
								<div class="mwb-pr-popup-rewards-left-content">
								<p><?php esc_html_e( '1. Copy & Share Your Link', 'coupon-referral-program' ); ?></p>
								<p><?php esc_html_e( '2. Your friends can register via your link', 'coupon-referral-program' ); ?></p>
								<?php if ( ! self::check_is_points_rewards_enable() ) { ?>
								<!-- Fixed Fixed -->
										<?php if ( 'mwb_cpr_referral_fixed' === $public_obj->get_referral_coupon_amount_type() && 'mwb_cpr_fixed' === $public_obj->mwb_get_discount_type() ) : ?>
								<p>
											<?php
											esc_html_e( '3. You will get the fixed discount coupon of  ', 'coupon-referral-program' );
											echo '<span class="mwb_cpr_highlight" style="color:' . wp_kses_post( Coupon_Referral_Program_Admin::get_selected_color() ) . '">' . wp_kses_post( wc_price( $public_obj->get_referral_discount_order( $user_id ) ) ) . '</span>';
											esc_html_e( ' on the referral purchase.', 'coupon-referral-program' );
											?>
									</p>
								<?php endif; ?>
								<!-- Percentage Fixed -->
										<?php if ( 'mwb_cpr_referral_percent' === $public_obj->get_referral_coupon_amount_type() && 'mwb_cpr_fixed' === $public_obj->mwb_get_discount_type() ) : ?>
								<p>
											<?php
											esc_html_e( '3. You will get the fixed discount coupon, ', 'coupon-referral-program' );
											echo '<span class="mwb_cpr_highlight" style="color:' . wp_kses_post( Coupon_Referral_Program_Admin::get_selected_color() ) . '">' . wp_kses_post( $public_obj->get_referral_discount_order( $user_id ) ) . '%</span>';
											esc_html_e( ' of the referral order', 'coupon-referral-program' ) . $public_obj->get_referral_coupon_amount_limit_html();
											?>
								</p>
								<?php endif; ?>
								<!-- Fixed Percentage-->
										<?php if ( 'mwb_cpr_referral_fixed' === $public_obj->get_referral_coupon_amount_type() && 'mwb_cpr_percent' === $public_obj->mwb_get_discount_type() ) : ?>
								<p>
											<?php
											esc_html_e( '3. You will get ', 'coupon-referral-program' );
											echo '<span class="mwb_cpr_highlight" style="color:' . wp_kses_post( Coupon_Referral_Program_Admin::get_selected_color() ) . '">' . wp_kses_post( $public_obj->get_referral_discount_order( $user_id ) ) . '%</span>';
											esc_html_e( ' coupon on the referral order purchase.', 'coupon-referral-program' );
											?>
								</p>
								<?php endif; ?>
								<!-- Percentage Percentage-->
										<?php if ( 'mwb_cpr_referral_percent' === $public_obj->get_referral_coupon_amount_type() && 'mwb_cpr_percent' === $public_obj->mwb_get_discount_type() ) : ?>
								<p>
											<?php
											esc_html_e( '3. You will get percentage coupon, ', 'coupon-referral-program' );
											echo '<span class="mwb_cpr_highlight" style="color:' . wp_kses_post( Coupon_Referral_Program_Admin::get_selected_color() ) . '">' . wp_kses_post( $public_obj->get_referral_discount_order( $user_id ) ) . '%</span>';
											esc_html_e( ' of the referral order', 'coupon-referral-program' ) . $public_obj->get_referral_coupon_amount_limit_html();
											?>
								</p>
								<?php endif; ?>
										<?php if ( $public_obj->check_signup_is_enable() ) { ?>
								<p>
											<?php
											esc_html_e( '4. Your friend will also get a ', 'coupon-referral-program' );
											echo '<span class="mwb_cpr_highlight" style="color:' . wp_kses_post( Coupon_Referral_Program_Admin::get_selected_color() ) . '">' . wp_kses_post( $public_obj->get_signup_discount_html() ) . '</span>';
											esc_html_e( ' discount coupon in return on Signup', 'coupon-referral-program' );
											?>
									</p>
								<?php } ?>
										<?php if ( $public_obj->check_signup_is_enable() ) { ?>
											<?php if ( $public_obj->check_reffre_signup_is_enable() ) { ?>
									<p>
												<?php
												esc_html_e( '5. You will also get a ', 'coupon-referral-program' );
												echo '<span class="mwb_cpr_highlight" style="color:' . wp_kses_post( Coupon_Referral_Program_Admin::get_selected_color() ) . '">' . wp_kses_post( $public_obj->get_refree_signup_discount_html() ) . '</span>';
												esc_html_e( ' discount coupon on referral signup', 'coupon-referral-program' );
												?>
										</p>
								<?php } ?>
								<?php } ?>
										<?php if ( ! $public_obj->check_signup_is_enable() ) { ?>
											<?php if ( $public_obj->check_reffre_signup_is_enable() ) { ?>
								<p>
												<?php
												esc_html_e( '4. You will also get a ', 'coupon-referral-program' );
												echo '<span class="mwb_cpr_highlight" style="color:' . wp_kses_post( Coupon_Referral_Program_Admin::get_selected_color() ) . '">' . wp_kses_post( $public_obj->get_refree_signup_discount_html() ) . '</span>';
												esc_html_e( ' discount coupon on referral signup', 'coupon-referral-program' );
												?>
									</p>
								<?php } ?>
								<?php } ?>
								<?php } ?>
								<?php if ( self::check_is_points_rewards_enable() ) { ?>
									<p>
										<?php
										esc_html_e( '3. You will get the ', 'coupon-referral-program' );
										echo '<span class="mwb_cpr_highlight" style="color:' . wp_kses_post( Coupon_Referral_Program_Admin::get_selected_color() ) . '">' . wp_kses_post( $public_obj->get_points_for_reffral_purchase() ) . esc_html__( 'points', 'coupon-referral-program' ) . '</span>';
										esc_html_e( ' on the referral order.', 'coupon-referral-program' );
										?>
									</p>
									<p>
										<?php
										esc_html_e( '4. Your friend will also get a ', 'coupon-referral-program' );
										echo '<span class="mwb_cpr_highlight" style="color:' . wp_kses_post( Coupon_Referral_Program_Admin::get_selected_color() ) . '">' . wp_kses_post( $public_obj->get_points_for_signup() ) . esc_html__( 'points', 'coupon-referral-program' ) . '</span>';
										esc_html_e( ' bonus in return on Signup', 'coupon-referral-program' );
										?>
									</p>
									<p>
										<?php
										esc_html_e( '5. You will also get the ', 'coupon-referral-program' );
										echo '<span class="mwb_cpr_highlight" style="color:' . wp_kses_post( Coupon_Referral_Program_Admin::get_selected_color() ) . '">' . wp_kses_post( $public_obj->get_points_for_refree_signup() ) . esc_html__( 'points', 'coupon-referral-program' ) . '</span>';
										esc_html_e( ' on referral signup', 'coupon-referral-program' );
										?>
									</p>
								<?php } ?>                
								</div>
							</div>
							<div class="mwb-pr-popup-right-rewards">
							<div class="mwb-pr-popup-rewards-right-content">
							</div>
							</div>
						</li>
						</ul>
					</div>
					</div>
					<!--mwb-pr-popup-body-->
						<?php
					} else {
						$mwb_crp_bg_img = Coupon_Referral_Program_Admin::get_selected_image();
						?>
						<div class="mwb_cpr_guest">

						<div class="mwb_cpr_guest__content">
							<div class="mwb_cpr_guest__content-text">
								<?php esc_html_e( 'Signup to start sharing your link', 'coupon-referral-program' ); ?>
							</div>
							<span class="mwb_cpr_btn_wrapper">
								<a href="<?php echo esc_html( wc_get_page_permalink( 'myaccount' ) ); ?>" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-cpr-btn" style="background-color: <?php echo wp_kses_post( Coupon_Referral_Program_Admin::get_selected_color() ); ?>"><?php esc_html_e( 'Signup', 'coupon-referral-program' ); ?></a>
							</span>
						</div>
						<div class="mwb_cpr_guest_img">
							<img src="<?php echo esc_html( $mwb_crp_bg_img ); ?>" alt="">
						</div>
						</div>
						<?php } ?>
				</div>
				<div class="mwb_modal__footer mwb-pr-footer">
					<a class="mdl-button mdl-button--colored mdl-js-button  mwb_modal__close mwb-pr-close-btn" style="border-color: <?php echo wp_kses_post( Coupon_Referral_Program_Admin::get_selected_color() ); ?> ; color:<?php echo wp_kses_post( Coupon_Referral_Program_Admin::get_selected_color() ); ?>"> <?php esc_html_e( 'Close', 'coupon-referral-program' ); ?>
					</a>
				</div>
			</div>
		</div>
	</div>
<?php
