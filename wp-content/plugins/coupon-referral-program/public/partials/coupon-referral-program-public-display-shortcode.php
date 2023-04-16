<?php
/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://woocommerce.com/
 * @since      1.0.0
 *
 * @package    class-coupon-referral-program
 * @subpackage class-coupon-referral-program/public/partials
 */

$user_id         = get_current_user_ID();
$mwb_crp_revenue = $this->get_revenue( $user_id );
$html            = '';
if ( $this->is_social_sharing_enabled() || $this->check_share_vai_referal_code() ) {
	$html .= '<div class="mwb_crp_referal_section_wrap">
			<fieldset class="mwb_crp_referal_section">
			<p class="mwb_cpr_heading">';
		$referral_text = get_option( 'referral_tab_text' ) ? esc_html( get_option( 'referral_tab_text' ) ) : esc_html__( 'Refer your friends and you’ll earn discounts on their purchases', 'coupon-referral-program' );

		$html .= $referral_text . '</p>';
		$enable = get_option( 'mwb_crp_referal_via_code', false );
	if ( isset( $enable ) && 'yes' === $enable ) {
		$referral_code = '';
		$referral_key  = get_user_meta( $user_id, 'referral_key', true );
		if ( isset( $referral_key ) && ! empty( $referral_key ) ) {
			$coupon = new WC_Coupon( $referral_key );
			if ( isset( $coupon ) && ! empty( $coupon ) ) {
				$coupon_id = $coupon->get_id();
				if ( isset( $coupon_id ) && ! empty( $coupon_id ) ) {
					$coupon_user_id = get_post_meta( $coupon_id, 'mwb_crp_coupon_user_id', true );
					if ( $user_id == $coupon_user_id ) {
						$referral_code = $referral_key;
					}
				} else {
					$referral_code = $this->mwb_crp_generate_referral_coupon_callback( $referral_key, $user_id );
				}
			}
		}
		if ( isset( $referral_code ) && ! empty( $referral_code ) ) {
			$html .= '<div class="mwb_crp_referal_code_wrap">
				<div class="mwb_cpr_logged_wrapper">
					<span class="mwb_crp_addon_referral_code">' . esc_html__( 'Referral Code: ', 'coupon-referral-program' ) . '</span>
					<div class="mwb_cpr_refrral_code_copy">
						<p id="mwb_cpr_referal_code_copy">
							<code id="mwb_cpr_referal_copyy_code" class="mwb_crp_referl_code">' . esc_html( $referral_code ) . '</code>
							<span class="mwb_cpr_copy_btn_wrap">
								<button class="mwb_cpr_btn_copy mwb_tooltip" data-clipboard-target="#mwb_cpr_referal_copyy_code" aria-label="copied">
								<span class="mwb_tooltiptext">' . esc_html__( 'Copy', 'coupon-referral-program' ) . '</span>
								<span class="mwb_tooltiptext_copied mwb_tooltiptext">' . esc_html__( 'Copied', 'coupon-referral-program' ) . '</span>
								<img src="' . esc_html( COUPON_REFERRAL_PROGRAM_DIR_URL ) . 'admin/images/copy.png" alt="copy icon">
								</button>
							</span>
						</p>
					</div>
					<div class="clear">
					</div>
					<small>' . esc_html__( 'Your friend can use this referral code as discount coupon', 'coupon-referral-program' ) . '</small>
				</div>
			</div>';
		}
	}
	if ( $this->is_social_sharing_enabled() ) {
		$html .= '<span class="mwb_crp_referral_link">' . esc_html__( 'Referral Link: ', 'coupon-referral-program' ) . '</span>
                <div class="mwb_cpr_logged_wrapper">
                    <div class="mwb_cpr_refrral_code_copy">
                        <p id="mwb_cpr_copy_link">
                            <code id="mwb_cpr_copyy_link">' . esc_html( $this->get_referral_link( $user_id ) ) . '</code>
                            <span class="mwb_cpr_copy_btn_wrap">
                                <button class="mwb_cpr_btn_copy mwb_tooltip" data-clipboard-target="#mwb_cpr_copyy_link" aria-label="copied">
                                <span class="mwb_tooltiptext">' . esc_html__( 'Copy', 'coupon-referral-program' ) . '</span>
                                <span class="mwb_tooltiptext_copied mwb_tooltiptext">' . esc_html__( 'Copied', 'coupon-referral-program' ) . '</span>
                                <img src="' . esc_html( COUPON_REFERRAL_PROGRAM_DIR_URL ) . 'admin/images/copy.png" alt="copy icon">
                                </button>
                            </span>
                        </p>
                    </div>
                    <div class="clear">
                    </div>
                </div>';
	}
	if ( $this->is_social_sharing_enabled() ) {
		$html .= $this->get_social_sharing_html( $user_id );
		// phpcs:ignore WordPress.Security.EscapeOutput
		$html .= '<script>(function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v2.9";
        fjs.parentNode.insertBefore(js, fjs);
        }(document, "script", "facebook-jssdk"));</script>';
		$html .= '<div class="mwb_crp_email_wrap">
            <p id="mwb_crp_notice"></p>
            <input type="email" name="mwb_crp_email_id" id="mwb_crp_email_id" placeholder="Enter Email Id.." />
            <button id="mwb_crp_email_send" class="button alt">' . esc_html__( 'Send', 'coupon-referral-program' ) . '</button>
        </div>';
	}
	$html .= '</fieldset></div>';
	$html .= '<style type="text/css">' . wp_kses_post( self::get_custom_style_popup_btn() ) . '</style>';
	/*Hide coupon section if points and rewards is enable.*/
	if ( self::mwb_crp_points_rewards_hide_referal() ) {
		return $html;
	}

	$html .= '<div class="mwb-crp-referral-wrapper">
	<div class="mwb-crp-referral-column">
		<div class="mwb-crp-referral-column-inner">
			<div class="mwb-crp-referral-icon">' . wp_kses_post( get_woocommerce_currency_symbol() ) . '</div>
			<span>' . esc_html__( 'Total Utilization', 'coupon-referral-program' ) . '</span>
			<h4>' . wp_kses_post( wc_price( $this->get_utilize_coupon_amount( $user_id ) ) ) . '</h4>
		</div>	
	</div>
	<div class="mwb-crp-referral-column">
		<div class="mwb-crp-referral-column-inner">
			<div class="mwb-crp-referral-icon"><i class="fas fa-users"></i></div>
			<span>' . esc_html__( 'Total Referred Users', 'coupon-referral-program' ) . '</span>
			<h4>' . esc_html( $mwb_crp_revenue['referred_users'] ) . '</h4>
		</div>	
	</div>
	<div class="mwb-crp-referral-column">
		<div class="mwb-crp-referral-column-inner">
		<div class="mwb-crp-referral-icon"><i class="fas fa-credit-card"></i></div>
			<span>' . esc_html__( 'Total Coupons', 'coupon-referral-program' ) . '</span>
			<h4>' . esc_html( $mwb_crp_revenue['total_coupon'] ) . '</h4>
		</div>	
	</div>
</div>
<div class="mwb-crp-referral-table-wrapper">
	<table id="mwb-crp-referral-table" class="mwb-crp-referral-table">
		<thead >
			<tr >
				<th class="mwb_crp_reporting_heading">' . esc_html__( 'Coupon', 'coupon-referral-program' ) . ' </th>
				<th class="mwb_crp_reporting_heading">' . esc_html__( 'Coupon Created', 'coupon-referral-program' ) . '</th>
				<th class="mwb_crp_reporting_heading">' . esc_html__( 'Expiry Date', 'coupon-referral-program' ) . '</th>
				<th class="mwb_crp_reporting_heading">' . esc_html__( 'Event', 'coupon-referral-program' ) . '</th>
				<th class="mwb_crp_reporting_heading">' . esc_html__( 'Referred Users', 'coupon-referral-program' ) . '</th>
				<th class="mwb_crp_reporting_heading">' . esc_html__( 'Usage Count', 'coupon-referral-program' ) . '</th>
			</tr>
		</thead>
		<tbody>';
	if ( ! empty( $this->get_signup_coupon( $user_id ) ) && is_array( $this->get_signup_coupon( $user_id ) ) ) :
		$signup_coupon = $this->get_signup_coupon( $user_id );
		$coupon        = new WC_Coupon( $signup_coupon['singup'] );
		if ( 'publish' === get_post_status( $signup_coupon['singup'] ) && $this->mwb_crp_validate_coupon( $coupon ) ) :
			$html .=
			'<tr>
				<td data-th="' . esc_html__( 'Coupon ', 'coupon-referral-program' ) . '">
					<div class="mwb-crp-coupon-code">
						<p id="' . 'mwb' . esc_html( $signup_coupon['singup'] ) . '">'
					. esc_html( $coupon->get_code() ) .
				'</p>
						<span class="mwb-crp-coupon-amount">';
			if ( 'fixed_cart' === $coupon->get_discount_type() ) {
					$html .= wp_kses_post( wc_price( $coupon->get_amount() ) );
			} else {
						$html .= esc_html( $coupon->get_amount() ) . '%';
			}
							$html .= '</span> 
						<img class="mwb-crp-coupon-scissors" src="' . esc_html( COUPON_REFERRAL_PROGRAM_DIR_URL ) . 'public/images/scissors.png" alt="scissor icon"> 
						<span class="mwb-crp-coupon-wrap">
							<button class="mwb-crp-coupon-btn-copy" data-clipboard-target="#mwb' . esc_html( $signup_coupon['singup'] ) . '" aria-label="copied">
								<span class="mwb-crp-coupon-tooltiptext">' . esc_html__( 'Copy', 'coupon-referral-program' ) . '</span>
								<span class="mwb-crp-coupon-tooltiptext-copied mwb-crp-coupon-tooltiptext">' . esc_html__( 'Copied', 'coupon-referral-program' ) . '</span>
								<img src="' . esc_html( COUPON_REFERRAL_PROGRAM_DIR_URL ) . 'admin/images/copy.png" alt="copy icon">
							</button>
						</span>
					</div>
				</td>
				<td data-th="' . esc_html__( 'Coupon Created', 'coupon-referral-program' ) . ' ">' . esc_html( $this->mwb_crp_get_transalted_coupon_created_date( $coupon ) ) . '</td>
				<td data-th="' . esc_html__( 'Expiry Date', 'coupon-referral-program' ) . '">' . esc_html( $this->mwb_crp_get_transalted_coupon_exp_date( $coupon ) ) . '</td>
				<td data-th="' . esc_html__( 'Event', 'coupon-referral-program' ) . '">' . esc_html__( 'Signup Coupon', 'coupon-referral-program' ) . '</td>
				<td data-th="' . esc_html__( 'Referred Users', 'coupon-referral-program' ) . '">----</td>
				<td data-th="' . esc_html__( 'Usage Count', 'coupon-referral-program' ) . '">' . esc_html( $coupon->get_usage_count() ) . '</td>	
			</tr>';
			endif;
			endif;
			// Refferal sigup.
	if ( ! empty( $this->mwb_crp_get_referal_signup_coupon( $user_id ) ) ) :
		foreach ( $this->mwb_crp_get_referal_signup_coupon( $user_id ) as $coupon_code => $crp_user_id ) :
			$coupon = new WC_Coupon( $coupon_code );
			if ( 'publish' === get_post_status( $coupon_code ) && $this->mwb_crp_validate_coupon( $coupon ) ) :
				$html .= '<tr>
				<td data-th="' . esc_html__( 'Coupon ', 'coupon-referral-program' ) . '">
					<div class="mwb-crp-coupon-code"><p id="mwb' . esc_html( $coupon_code ) . '">' . esc_html( $coupon->get_code() ) . '</p>
						<span class="mwb-crp-coupon-amount">';

				if ( 'fixed_cart' === $coupon->get_discount_type() ) {
					$html .= wp_kses_post( wc_price( $coupon->get_amount() ) );
				} else {
					$html .= esc_html( $coupon->get_amount() ) . '%';
				}
					$html .= '</span> <img class="mwb-crp-coupon-scissors" src="' . esc_html( COUPON_REFERRAL_PROGRAM_DIR_URL ) . 'public/images/scissors.png" alt="scissor icon"><span class="mwb-crp-coupon-wrap">
							<button class="mwb-crp-coupon-btn-copy" data-clipboard-target="#mwb' . esc_html( $coupon_code ) . '" aria-label="copied">
								<span class="mwb-crp-coupon-tooltiptext">' . esc_html__( 'Copy', 'coupon-referral-program' ) . '</span>
								<span class="mwb-crp-coupon-tooltiptext-copied mwb-crp-coupon-tooltiptext">' . esc_html__( 'Copied', 'coupon-referral-program' ) . '</span>
								<img src="' . esc_html( COUPON_REFERRAL_PROGRAM_DIR_URL ) . 'admin/images/copy.png" alt="copy icon">
							</button>
						</span>
					</div>
				</td>
				<td data-th="' . esc_html__( 'Coupon Created', 'coupon-referral-program' ) . '">' . esc_html( $this->mwb_crp_get_transalted_coupon_created_date( $coupon ) ) . '</td>
				<td data-th="' . esc_html__( 'Expiry Date', 'coupon-referral-program' ) . '">' . esc_html( $this->mwb_crp_get_transalted_coupon_exp_date( $coupon ) ) . '</td>
				<td data-th="' . esc_html__( 'Event', 'coupon-referral-program' ) . '">' . esc_html__( 'Referral Signup', 'coupon-referral-program' ) . '</td>
				<td data-th="' . esc_html__( 'Referred Users', 'coupon-referral-program' ) . '">';
				if ( get_userdata( $crp_user_id ) ) {
					$display_name = get_userdata( $crp_user_id )->data->display_name;
				} else {
					$display_name = esc_html__( 'User has been deleted', 'coupon-referral-program' );
				}
				$html .= $display_name . '</td>
				<td data-th="' . esc_html__( 'Usage Count', 'coupon-referral-program' ) . '">' . esc_html( $coupon->get_usage_count() ) . '</td>
			</tr>';
			endif;
			endforeach;
			endif;
			// End Refferal sigup
			// Start referal purchase coupon.
	if ( ! empty( $this->get_referral_purchase_coupons( $user_id ) ) ) :
		foreach ( $this->get_referral_purchase_coupons( $user_id ) as $coupon_code => $crp_user_id ) :
			$coupon   = new WC_Coupon( $coupon_code );
			$order_id = get_post_meta( $coupon->get_id(), 'coupon_created_to', true );
			if ( 'publish' === get_post_status( $coupon_code ) && $this->mwb_crp_validate_coupon( $coupon ) ) :
				$html .= '<tr>
				<td data-th="' . esc_html__( 'Coupon ', 'coupon-referral-program' ) . '">
					<div class="mwb-crp-coupon-code"><p id="mwb' . esc_html( $coupon_code ) . '">' . esc_html( $coupon->get_code() ) . '</p>
						<span class="mwb-crp-coupon-amount">';
				if ( 'fixed_cart' === $coupon->get_discount_type() ) {
					$html .= wp_kses_post( wc_price( $coupon->get_amount() ) );
				} else {
					$html .= esc_html( $coupon->get_amount() ) . '%';
				}
				$html .= '</span> <img class="mwb-crp-coupon-scissors" src="' . esc_html( COUPON_REFERRAL_PROGRAM_DIR_URL ) . 'public/images/scissors.png" alt="scissor icon"> <span class="mwb-crp-coupon-wrap">
							<button class="mwb-crp-coupon-btn-copy" data-clipboard-target="#mwb' . esc_html( $coupon_code ) . '" aria-label="copied">
								<span class="mwb-crp-coupon-tooltiptext">' . esc_html__( 'Copy', 'coupon-referral-program' ) . '</span>
								<span class="mwb-crp-coupon-tooltiptext-copied mwb-crp-coupon-tooltiptext">' . esc_html__( 'Copied', 'coupon-referral-program' ) . '</span>
								<img src="' . esc_html( COUPON_REFERRAL_PROGRAM_DIR_URL ) . 'admin/images/copy.png" alt="copy icon">
							</button>
						</span>
					</div>
				</td>
				<td data-th="' . esc_html__( 'Coupon Created', 'coupon-referral-program' ) . '">' . esc_html( $this->mwb_crp_get_transalted_coupon_created_date( $coupon ) ) . '</td>
				<td data-th="' . esc_html__( 'Expiry Date', 'coupon-referral-program' ) . '">' . esc_html( $this->mwb_crp_get_transalted_coupon_exp_date( $coupon ) ) . '</td>
				<td data-th="' . esc_html__( 'Event', 'coupon-referral-program' ) . '">' . esc_html__( 'Referral Purchase For', 'coupon-referral-program' ) . ' #' . esc_html( $order_id ) . '</td>
				<td data-th="' . esc_html__( 'Referred Users', 'coupon-referral-program' ) . '">';
				if ( get_userdata( $crp_user_id ) ) {
					$display_name = get_userdata( $crp_user_id )->data->display_name;
				} else {
					$display_name = esc_html__( 'User has been deleted', 'coupon-referral-program' );
				}
				$html .= $display_name . '</td>
				<td data-th="' . esc_html__( 'Usage Count', 'coupon-referral-program' ) . '">' . esc_html( $coupon->get_usage_count() ) . '</td>
			</tr>';
			endif;
			endforeach;
			endif;
			// end referal purchase coupon
			// start referal purchase coupon on guest user via referal code.
	if ( ! empty( $this->get_referral_purchase_coupons_on_guest( $user_id ) ) ) :
		foreach ( $this->get_referral_purchase_coupons_on_guest( $user_id ) as $coupon_code => $email ) :
			$coupon   = new WC_Coupon( $coupon_code );
			$order_id = get_post_meta( $coupon->get_id(), 'coupon_created_to', true );
			if ( 'publish' === get_post_status( $coupon_code ) && $this->mwb_crp_validate_coupon( $coupon ) ) :
				$html .= '<tr>
				<td data-th="' . esc_html__( 'Coupon ', 'coupon-referral-program' ) . '">
					<div class="mwb-crp-coupon-code"><p id="mwb' . esc_html( $coupon_code ) . '">' . esc_html( $coupon->get_code() ) . '</p>
						<span class="mwb-crp-coupon-amount">';
				if ( 'fixed_cart' === $coupon->get_discount_type() ) {
					$html .= wp_kses_post( wc_price( $coupon->get_amount() ) );
				} else {
					$html .= esc_html( $coupon->get_amount() ) . '%';
				}
				$html .= '</span> <img class="mwb-crp-coupon-scissors" src="' . esc_html( COUPON_REFERRAL_PROGRAM_DIR_URL ) . 'public/images/scissors.png" alt="scissor icon"> <span class="mwb-crp-coupon-wrap">
							<button class="mwb-crp-coupon-btn-copy" data-clipboard-target="#mwb' . esc_html( $coupon_code ) . '" aria-label="copied">
								<span class="mwb-crp-coupon-tooltiptext">' . esc_html__( 'Copy', 'coupon-referral-program' ) . '</span>
								<span class="mwb-crp-coupon-tooltiptext-copied mwb-crp-coupon-tooltiptext">' . esc_html__( 'Copied', 'coupon-referral-program' ) . '</span>
								<img src="' . esc_html( COUPON_REFERRAL_PROGRAM_DIR_URL ) . 'admin/images/copy.png" alt="copy icon">
							</button>
						</span>
					</div>
				</td>
				<td data-th="' . esc_html__( 'Coupon Created', 'coupon-referral-program' ) . '">' . esc_html( $this->mwb_crp_get_transalted_coupon_created_date( $coupon ) ) . '</td>
				<td data-th="' . esc_html__( 'Expiry Date', 'coupon-referral-program' ) . '">' . esc_html( $this->mwb_crp_get_transalted_coupon_exp_date( $coupon ) ) . '</td>
				<td data-th="' . esc_html__( 'Event', 'coupon-referral-program' ) . '">' . esc_html__( 'Referral Purchase Via Guest User For', 'coupon-referral-program' ) . ' #' . esc_html( $order_id ) . '</td>
				<td data-th="' . esc_html__( 'Referred Users', 'coupon-referral-program' ) . '">' . $email ? esc_html( $email ) : esc_html__( 'Email not found', 'coupon-referral-program' ) . '</td>
				<td data-th="' . esc_html__( 'Usage Status', 'coupon-referral-program' ) . '">' . esc_html( $coupon->get_usage_count() ) . '</td>
			</tr>';
			endif;
			endforeach;
			endif;
			// End referal purchase coupon on guest user via referal code.
		$html .= '</tbody>
	</table>
</div>';
}
return $html;
// This file should primarily consist of HTML with a little bit of PHP.
