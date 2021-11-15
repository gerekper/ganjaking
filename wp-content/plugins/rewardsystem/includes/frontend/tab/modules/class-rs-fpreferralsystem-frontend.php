<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}

if ( ! class_exists( 'RSFunctionForReferralSystem' ) ) {

	class RSFunctionForReferralSystem {

		public static function init() {
			if ( 'yes' == get_option( 'rs_reward_content' )  ) {
				add_action( 'woocommerce_before_my_account' , array( __CLASS__ , 'referral_list_table_in_my_account' ) ) ;
			}

			add_action( 'wp_head' , array( __CLASS__ , 'set_cookie_for_referral' ) ) ;

			add_action( 'wp_head' , array( __CLASS__ , 'unset_cookie_based_on_referral_registration_date' ) ) ;

			add_action( 'wp_head' , array( __CLASS__ , 'link_referral_for_lifetime' ) ) ;

			add_action( 'user_register' , array( __CLASS__ , 'award_points_for_referral_account_signup' ) , 10 , 1 ) ;

			if ( '2' == get_option( 'rs_display_generate_referral' )  ) {
				if ( '1' == get_option( 'rs_show_hide_generate_referral_link_type' ) ) {
					add_action( 'woocommerce_after_my_account' , array( __CLASS__ , 'list_of_generated_link_and_field_in_myaccount' ) ) ;
				} else {
					add_action( 'woocommerce_after_my_account' , array( __CLASS__ , 'static_referral_link_in_my_account' ) ) ;
				}
			} else {
				if ( '1' == get_option( 'rs_show_hide_generate_referral_link_type' ) ) {
					add_action( 'woocommerce_before_my_account' , array( __CLASS__ , 'list_of_generated_link_and_field_in_myaccount' ) ) ;
				} else {
					add_action( 'woocommerce_before_my_account' , array( __CLASS__ , 'static_referral_link_in_my_account' ) ) ;
				}
			}

			if ( '1' == get_option( 'rs_troubleshoot_referral_link_landing_page' ) ) {
				add_action( 'wp' , array( __CLASS__ , 'referrer_name' ) ) ;
			} else {
				add_action( 'wp_head' , array( __CLASS__ , 'referrer_name' ) ) ;
			}

			if ( '1' == get_option( 'rs_message_before_after_cart_table' ) ) {
				if ( '1' == get_option( 'rs_reward_point_troubleshoot_before_cart' )) {
					add_action( 'woocommerce_before_cart' , array( __CLASS__ , 'message_for_referral_product_purchase' ) ) ;
				} else {
					add_action( 'woocommerce_before_cart_table' , array( __CLASS__ , 'message_for_referral_product_purchase' ) ) ;
				}
			} else {
				add_action( 'woocommerce_after_cart_table' , array( __CLASS__ , 'message_for_referral_product_purchase' ) ) ;
			}
			add_action( 'woocommerce_before_checkout_form' , array( __CLASS__ , 'message_for_referral_product_purchase' ) ) ;

			add_action( 'woocommerce_removed_coupon' , array( __CLASS__ , 'message_for_referral_product_purchase' ) , 10 , 1 ) ;
		}

		/* Display Referral List in My Account */

		public static function referral_list_table_in_my_account() {
			$TableData = array(
				'show_table'           => get_option( 'rs_show_hide_referal_table' ) ,
				'sno_label'            => get_option( 'rs_my_referal_sno_label' ) ,
				'userid_or_email'      => get_option( 'rs_select_option_for_referral' ) ,
				'userid_label'         => get_option( 'rs_my_referal_userid_label' ) ,
				'email_id'             => get_option( 'rs_referral_email_ids' ) ,
				'total_referral_label' => get_option( 'rs_my_total_referal_points_label' ) ,
				'title_table'          => get_option( 'rs_referal_table_title' ) ,
					) ;

			echo wp_kses_post(self::referral_list_table( $TableData )) ;
		}

		/* Display Referral List in Menu */

		public static function referral_list_table_in_menu() {
			$TableData = array(
				'show_table'           => get_option( 'rs_show_hide_referal_table_menu_page' ) ,
				'sno_label'            => get_option( 'rs_my_referal_sno_label' ) ,
				'userid_or_email'      => get_option( 'rs_select_option_for_referral' ) ,
				'userid_label'         => get_option( 'rs_my_referal_userid_label' ) ,
				'email_id'             => get_option( 'rs_referral_email_ids' ) ,
				'total_referral_label' => get_option( 'rs_my_total_referal_points_label' ) ,
				'title_table'          => get_option( 'rs_referal_table_title' ) ,
					) ;
			echo wp_kses_post(self::referral_list_table( $TableData ) );
		}

		/* HTML Elements of Referral List Table */

		public static function referral_list_table( $TableData, $echo = false ) {
			if ( ! is_user_logged_in() ) {
				return ;
			}

			$UserId  = get_current_user_id() ;
			$BanType = check_banning_type( $UserId ) ;
			if ( 'earningonly' == $BanType || 'both' == $BanType ) {
				return ;
			}

			if ( ! check_if_referral_is_restricted() ) {
				return ;
			}

			if ( ! check_if_referral_is_restricted_based_on_history() ) {
				return ;
			}

			if ( 2 == $TableData[ 'show_table' ] ) {
				return ;
			}

			ob_start() ;
			?>
			<h2 class=rs_my_referral_table><?php echo wp_kses_post($TableData[ 'title_table' ]) ; ?></h2>
			<table class = "referrallog demo shop_table my_account_referal table-bordered"  data-page-size="5" data-page-previous-text = "prev" >
				<thead>
					<tr>
						<th><?php echo wp_kses_post($TableData[ 'sno_label' ]) ; ?></th>
						<th><?php echo wp_kses_post(( isset( $TableData[ 'userid_or_email' ] ) && '1' == $TableData[ 'userid_or_email' ] ) ? $TableData[ 'userid_label' ] : $TableData[ 'email_id' ]) ; ?></th>
						<th><?php echo wp_kses_post($TableData[ 'total_referral_label' ]) ; ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					$ReferralLog = RS_Referral_Log::corresponding_referral_log( get_current_user_id() ) ;
					if ( srp_check_is_array( $ReferralLog ) ) {
						if ( '1' == get_option( 'rs_points_log_sorting' ) ) {
							krsort( $ReferralLog , SORT_NUMERIC ) ;
						}

						$i = 1 ;
						foreach ( $ReferralLog as $Key => $values ) {
							$UserInfo = get_user_by( 'id' , $Key ) ;
							if ( ! is_object( $UserInfo ) ) {
								continue ;
							}
							?>
							<tr>
								<td data-value="<?php echo esc_attr($i) ; ?>"><?php echo esc_attr($i) ; ?></td>
								<td><?php echo wp_kses_post(( isset( $TableData[ 'userid_or_email' ] ) && '1' == $TableData[ 'userid_or_email' ] ) ? $UserInfo->user_login : $UserInfo->user_email ); ?></td>
								<td><?php echo wp_kses_post(RSMemberFunction::earn_points_percentage( get_current_user_id() , ( float ) $values )) ; ?></td>
							</tr>
							<?php
							$i ++ ;
						}
					}
					?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="7">
							<div class="pagination pagination-centered"></div>
						</td>
					</tr>
				</tfoot>
			</table>
			<?php
			$content = ob_get_contents() ;
			ob_end_clean() ;
			if ( ! $echo ) {
				return $content ;
			}

			echo wp_kses_post($content) ;
		}

		/* Display the field to generate link and list of generated link in both Menu and My Account */

		public static function list_of_generated_link_and_field() {
			if ( ! check_if_referral_is_restricted() ) {
				return ;
			}

			if ( is_user_logged_in() ) {
				$UserId  = get_current_user_id() ;
				$BanType = check_banning_type( $UserId ) ;
				if ( 'earningonly' == $BanType || 'both'  == $BanType ) {
					return ;
				}

				if ( ! check_referral_count_if_exist( get_current_user_id() ) ) {
					echo wp_kses_post(__( "<p>Since you have reached the referral link usage, you don't have the access to refer anymore</p>" , 'rewardsystem' )) ;
				} else {
					ob_start() ;
					self::field_to_generate_referral_link() ;
					self::list_of_generated_link() ;
					$content = ob_get_contents() ;
					ob_end_flush() ;
										
					return $content ;
				}
			} else {
				echo wp_kses_post(__( 'Please Login to View the Content of  this Page <a href=' . get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) . '> Login </a>' , 'rewardsystem' ) );
			}
		}

		/* Display the field to generate link and list of generated link in My Account */

		public static function list_of_generated_link_and_field_in_myaccount() {
			if ( 'yes' != get_option( 'rs_reward_content' ) ) {
				return ;
			}

			if ( '2' == get_option( 'rs_show_hide_generate_referral' ) ) {
				return ;
			}

			if ( ! check_if_referral_is_restricted_based_on_history() ) {
				return ;
			}

			self::list_of_generated_link_and_field();
		}

		/* Display the Static Referral link in My Account */

		public static function static_referral_link_in_my_account() {
			if ( 'yes' != get_option( 'rs_reward_content' ) ) {
				return ;
			}

			if ( '2' == get_option( 'rs_show_hide_generate_referral' ) ) {
				return ;
			}

			if ( ! check_if_referral_is_restricted_based_on_history() ) {
				return ;
			}

			echo wp_kses_post(self::static_referral_link()) ;
		}

		/* Display the Static Referral link in both Menu and My Account */

		public static function static_referral_link() {
			if ( ! is_user_logged_in() ) {
				return ;
			}

			$UserId  = get_current_user_id() ;
			$BanType = check_banning_type( $UserId ) ;
			if ( 'earningonly' == $BanType || 'both' == $BanType ) {
				return ;
			}

			if ( ! check_if_referral_is_restricted() ) {
				return ;
			}

			if ( ! check_if_referral_is_restricted_based_on_history() ) {
				return ;
			}

			if ( ! check_referral_count_if_exist( $UserId ) ) {
				echo wp_kses_post(__( "<p>Since you have reached the referral link usage, you don't have the access to refer anymore</p>" , 'rewardsystem' ) );
			} else {
				ob_start() ;
				self::static_url() ;
				$content = ob_get_contents() ;
				ob_end_clean() ;
				return $content ;
			}
		}

		/* Display the input field and button for Generate Referral Link */

		public static function field_to_generate_referral_link() {
			?>
			<div class="referral_field1">
				<input type="text" 
					   size="50" 
					   name="generate_referral_field" 
					   id="generate_referral_field" 
					   required="required" 
					   value="<?php echo esc_url( get_option( 'rs_prefill_generate_link' ) ) ; ?>">

				<input type="submit"  
					   title="<?php echo esc_attr( get_option( 'rs_generate_link_hover_label' , 'Click this button to generate the referral link' ) ) ; ?>" 
					   class="button <?php echo esc_attr( get_option( 'rs_extra_class_name_generate_referral_link' ) ) ; ?>"
					   name="refgeneratenow" 
					   id="refgeneratenow" 
					   value="<?php echo wp_kses_post( get_option( 'rs_generate_link_button_label' ) ) ; ?>"/>

			</div>                
			<?php
		}

		/* Display the list of generated link */

		public static function list_of_generated_link() {
			wp_enqueue_script( 'fp_referral_frontend' , SRP_PLUGIN_DIR_URL . 'includes/frontend/js/modules/fp-referral-frontend.js' , array( 'jquery' ) , SRP_VERSION ) ;
			$LocalizedScript = array(
				'ajaxurl'          => SRP_ADMIN_AJAX_URL ,
				'buttonlanguage'   => get_option( 'rs_language_selection_for_button' ) ,
				'wplanguage'       => get_option( 'WPLANG' ) ,
				'fbappid'          => get_option( 'rs_facebook_application_id' ) ,
				'enqueue_footable' => get_option( 'rs_enable_footable_js' , '1' ) ,
					) ;
			wp_localize_script( 'fp_referral_frontend' , 'fp_referral_frontend_params' , $LocalizedScript ) ;
			?>
			<h3  class=rs_my_referral_link_title><?php echo wp_kses_post(get_option( 'rs_generate_link_label' )) ; ?></h3>
			<table class="referral_link shop_table my_account_referral_link" id="my_account_referral_link">
				<thead>
					<tr>
						<th class="referral-number"><span class="nobr"><?php echo wp_kses_post(get_option( 'rs_generate_link_sno_label' ) ); ?></span></th>
						<th class="referral-date"><span class="nobr"><?php echo wp_kses_post(get_option( 'rs_generate_link_date_label' ) ); ?></span></th>
						<th class="referral-link"><span class="nobr"><?php echo wp_kses_post(get_option( 'rs_generate_link_referrallink_label' ) ); ?></span></th>
						<?php if ('1' == get_option('rs_account_show_hide_social_share_button', 1)) : ?>
						   <th data-hide='phone,tablet' class="referral-social"><span class="nobr"><?php echo wp_kses_post(get_option( 'rs_generate_link_social_label' ) ); ?></span></th>
						<?php endif; ?>
						<th data-hide='phone,tablet' class="referral-actions"><span class="nobr"><?php echo wp_kses_post(get_option( 'rs_generate_link_action_label' )) ; ?></span></th>
					</tr>
				</thead>
				<tbody>
					<?php
					$UserId          = get_current_user_id() ;
					if ( srp_check_is_array( get_option( 'arrayref' . $UserId ) ) ) {
						$i = 1 ;
						foreach ( get_option( 'arrayref' . $UserId ) as $key => $array ) {
							$mainkey = explode( ',' , $array ) ;
							?>
							<tr class="referrals" data-url="<?php echo esc_url($mainkey[ 0 ]) ; ?>">
								<td><?php echo esc_attr($i) ; ?></td>
								<td><?php echo esc_html($mainkey[ 1 ]) ; ?></td>
								<td class="copy_clip_icon">
									<?php if ('yes' == get_option( 'rs_enable_copy_to_clipboard' ) ) { ?>
										<img data-referralurl="<?php echo esc_url($mainkey[ 0 ]) ; ?>"
											title="<?php esc_html_e( 'Click to copy the link' , 'rewardsystem' ) ; ?>" 
											alt="<?php esc_html_e( 'Click to copy the link' , 'rewardsystem' ) ; ?>" 
											src="<?php echo esc_url(SRP_PLUGIN_URL) ; ?>/assets/images/copy_link.png" 
											id="rs_copy_clipboard_image" class="rs_copy_clipboard_image"/>
																				
										<div class="rs_alert_div_for_copy">
											<div class="rs_alert_div_for_copy_content">
												<p><?php esc_html_e( 'Referral Link Copied' , 'rewardsystem' ) ; ?></p>
											</div>
										</div>
									<?php } ?>
									<?php echo esc_html($mainkey[ 0 ]) ; ?>  
								</td>
								<?php if ('1' == get_option('rs_account_show_hide_social_share_button', 1)) : ?>
								<td>
									<div class="rs_social_buttons">      
										<?php
										if ( '1' == get_option( 'rs_account_show_hide_facebook_share_button' ) ) {
											?>
											<div class="share_wrapper_default_url" 
												id="share_wrapper_default_url" href="<?php echo esc_url($mainkey[ 0 ] ); ?>" 
												data-image="<?php echo esc_url(get_option( 'rs_fbshare_image_url_upload' )); ?>"
												data-title="<?php echo wp_kses_post(get_option( 'rs_facebook_title' )); ?>" 
												data-description="<?php echo wp_kses_post(get_option( 'rs_facebook_description' )); ?>">
												<img class='fb_share_img'
													src="<?php echo esc_url(SRP_PLUGIN_URL) ; ?>/assets/images/icon1.png"> <span class="label"><?php echo wp_kses_post(get_option( 'rs_fbshare_button_label' )) ; ?> </span>
											</div> 
											<?php
										}
										if ( '1' == get_option( 'rs_account_show_hide_twitter_tweet_button' ) ) {
											?>
											<a href="https://twitter.com/share" 
												class="twitter-share-button" 
												data-text = "<?php echo wp_kses_post(get_option('rs_twitter_share_text', get_option( 'blogdescription' ))); ?>"
												data-count="none" data-url="<?php echo esc_url($mainkey[ 0 ]) ; ?>"><?php esc_html_e('Tweet', 'rewardsystem'); ?></a><br>
											<?php
										}
										if ( '1' == get_option( 'rs_acount_show_hide_google_plus_button' ) ) {
											?>
											<div class="g-plusone" 
												data-action="share" 
												data-annotation="none" 
												data-href="<?php echo esc_url($mainkey[ 0 ]) ; ?>">
												<g:plusone></g:plusone>
											</div>
											<?php
										}
										if ( '1' == get_option( 'rs_acount_show_hide_whatsapp_button' , '1' ) ) {
											$ref_link_key = isset( $mainkey[ 0 ] ) ? $mainkey[ 0 ] : '' ;
											?>
																		
											<a class="rs-whatsapp-share-button" 
													href="<?php echo esc_url( "https://web.whatsapp.com://send?text=$ref_link_key" ) ; ?>" 
														target="_blank">
												<img class='whatsapp_share_img' 
													src="<?php echo esc_url(SRP_PLUGIN_URL) ; ?>/assets/images/whatsapp-icon.png"> 
														<span class="rs_whatsapp_label">
															<?php esc_html_e( 'Share' , 'rewardsystem' ) ; ?> 
														</span>
											</a>
											<?php
										}
										?>
									</div>
								</td>
								<?php endif; ?>
								<td>
								<span data-array="<?php echo esc_attr($key) ; ?>" 
									class="referralclick">x</span>
								</td>
							</tr>
							<?php
							$i ++ ;
						}
					}
					?>
				</tbody>
			</table>
			<?php
		}

		public static function static_url() {
			$UserId         = get_current_user_id() ;
			$UserInfo       = get_userdata( $UserId ) ;
			$referralperson = ( '1' == get_option( 'rs_generate_referral_link_based_on_user' ) ) ? $UserInfo->user_login : $UserId ;
			if ( is_account_page() ) {
				if ( '2' == get_option( 'rs_show_hide_generate_referral_link_type' ) ) {
					self::static_url_table( $referralperson ) ;
				}
			} else {
				if ( '1' == get_option( '_rs_static_referral_link' ) ) {
					self::static_url_table( $referralperson ) ;
				}
			}
		}

		/* HTML Element for Static URL */

		public static function static_url_table( $referralperson ) {
			wp_enqueue_script( 'fp_referral_frontend' , SRP_PLUGIN_DIR_URL . 'includes/frontend/js/modules/fp-referral-frontend.js' , array( 'jquery' ) , SRP_VERSION ) ;
			$LocalizedScript = array(
				'ajaxurl'          => SRP_ADMIN_AJAX_URL ,
				'buttonlanguage'   => get_option( 'rs_language_selection_for_button' ) ,
				'wplanguage'       => get_option( 'WPLANG' ) ,
				'fbappid'          => get_option( 'rs_facebook_application_id' ) ,
				'enqueue_footable' => get_option( 'rs_enable_footable_js' , '1' ) ,
					) ;
			wp_localize_script( 'fp_referral_frontend' , 'fp_referral_frontend_params' , $LocalizedScript ) ;
			$query           = ( 'yes' == get_option( 'rs_restrict_referral_points_for_same_ip' ) ) ? array( 'ref' => $referralperson , 'ip' => base64_encode( get_referrer_ip_address() ) ) : array( 'ref' => $referralperson ) ;
			$refurl          = add_query_arg( $query , get_option( 'rs_static_generate_link' ) ) ;
			wp_enqueue_script('fp_gplus_social_icon', 'https://apis.google.com/js/plusone.js', array(), SRP_VERSION);
			?>
			<h3 class=rs_my_referral_link_title><?php echo wp_kses_post(get_option( 'rs_my_referral_link_button_label' )); ?></h3>
			<table class="shop_table my_account_referral_link_static" id="my_account_referral_link_static">
				<thead>
					<tr>
						<th class="referral-number_static"><span class="nobr"><?php echo wp_kses_post(get_option( 'rs_generate_link_sno_label' )) ; ?></span></th>                        
						<th class="referral-link_static"><span class="nobr"><?php echo wp_kses_post(get_option( 'rs_generate_link_referrallink_label' )) ; ?></span></th>
						<th class="referral-social_static"><span class="nobr"><?php echo wp_kses_post(get_option( 'rs_generate_link_social_label' ) ); ?></span></th>
					</tr>
				</thead>
				<tbody>
					<tr class="referrals_static">
						<td><?php echo esc_attr(1) ; ?></td>
						<td class="copy_clip_icon">
							<?php echo esc_url($refurl) ; ?>
							<?php if ( 'yes' == get_option( 'rs_enable_copy_to_clipboard' ) ) { ?>
								<img data-referralurl="<?php echo esc_url($refurl) ; ?>" 
									title="<?php esc_html_e( 'Click to copy the link' , 'rewardsystem' ) ; ?>" 
									alt="<?php esc_html_e( 'Click to copy the link' , 'rewardsystem' ) ; ?>" 
									src="<?php echo esc_url(SRP_PLUGIN_URL) ; ?>/assets/images/copy_link.png" 
									id="rs_copy_clipboard_image" 
									class="rs_copy_clipboard_image"/>
																
								<div class="rs_alert_div_for_copy">
									<div class="rs_alert_div_for_copy_content">
										<p><?php esc_html_e( 'Referral Link Copied' , 'rewardsystem' ) ; ?></p>
									</div>
								</div>
							<?php } ?>
						</td>
						<td>
							<?php if ( '1' == get_option( 'rs_account_show_hide_facebook_share_button' ) ) { ?>
								<div class="share_wrapper_static_url" id="share_wrapper_static_url" 
									href="<?php echo esc_url($refurl) ; ?>" 
									data-image="<?php echo esc_url(get_option( 'rs_fbshare_image_url_upload' )); ?>" 
									data-title="<?php echo wp_kses_post(get_option( 'rs_facebook_title' )); ?>" 
									data-description="<?php echo wp_kses_post(get_option( 'rs_facebook_description' )); ?>">
									<img class='fb_share_img' 
									src="<?php echo esc_url(SRP_PLUGIN_URL) ; ?>/assets/images/icon1.png"> 
									<span class="label"><?php echo wp_kses_post(get_option( 'rs_fbshare_button_label' )) ; ?> </span>
								</div>
							<?php } ?>
													
							<?php if ( '1' == get_option( 'rs_account_show_hide_twitter_tweet_button' ) ) { ?>
								<a href="https://twitter.com/share"
									class="twitter-share-button" 
									data-count="none"
									data-text = "<?php echo wp_kses_post(get_option('rs_twitter_share_text', get_option( 'blogdescription' ))); ?>"
									data-url="<?php echo esc_url($refurl) ; ?>"><?php esc_html_e('Tweet', 'rewardsystem'); ?></a>
							<?php } ?><br>
														
							<?php if ( '1' == get_option( 'rs_acount_show_hide_google_plus_button' ) ) { ?>
								<div class="g-plusone" 
									data-action="share"
									data-annotation="none" 
									data-href="<?php echo esc_url($refurl) ; ?>"><g:plusone></g:plusone></div>
							<?php } ?>
							<?php if ( '1' == get_option( 'rs_acount_show_hide_whatsapp_button' , '1' ) ) { ?>
								<a class="rs-whatsapp-share-button" 
									href="<?php echo esc_url( "https://web.whatsapp.com://send?text=$refurl" ) ; ?>">
									<img class='whatsapp_share_img' 
									src="<?php echo esc_url(SRP_PLUGIN_URL) ; ?>/assets/images/whatsapp-icon.png"> 
									<span class="rs_whatsapp_label"><?php esc_html_e( 'Share' , 'rewardsystem' ) ; ?> </span>
								</a>
							<?php } ?> 
						</td>
					</tr>                    
				</tbody>
			</table>
			<?php
		}

		public static function check_limit_for_referral_link() {
			if ( ! isset( $_GET[ 'ref' ] ) ) {
				return true ;
			}
						
						$ref = wc_clean(wp_unslash($_GET[ 'ref' ]));
			$UserInfo = get_user_by( 'login' , $ref ) ;
			$RefId    = is_object( $UserInfo ) ? $UserInfo->ID : $ref ;
			if ( get_current_user_id() == $RefId ) {
				return true ;
			}

			if ( check_referral_count_if_exist( $RefId ) ) {
				return true ;
			}

			setcookie( 'rsreferredusername' , null , -1 , COOKIEPATH ? COOKIEPATH : '/' , COOKIE_DOMAIN , is_ssl() , true ) ;
			setcookie( 'referrerip' , null , -1 , COOKIEPATH ? COOKIEPATH : '/' , COOKIE_DOMAIN , is_ssl() , true ) ;
			return false ;
		}

		/* Set Cookie */

		public static function set_cookie_for_referral() {
			if ( ! check_if_referral_is_restricted() ) {
				return ;
			}

			if ( isset( $_GET[ 'ref' ] ) && ! is_user_logged_in() && self::check_limit_for_referral_link() ) {
								$ref = wc_clean(wp_unslash($_GET[ 'ref' ]));
				if ( '1' == get_option( 'rs_referral_cookies_expiry' ) ) {
					$min = '' == get_option( 'rs_referral_cookies_expiry_in_min' ) ? '1' : get_option( 'rs_referral_cookies_expiry_in_min' ) ;
					setcookie( 'rsreferredusername' , $ref , time() + 60 * $min , COOKIEPATH ? COOKIEPATH : '/' , COOKIE_DOMAIN , is_ssl() , true ) ;
					if ( isset( $_GET[ 'ip' ] ) ) {
						setcookie( 'referrerip' , wc_clean(wp_unslash($_GET[ 'ip' ])) , time() + 60 * $min , COOKIEPATH ? COOKIEPATH : '/' , COOKIE_DOMAIN , is_ssl() , true ) ;
					}
				} elseif ( '2' == get_option( 'rs_referral_cookies_expiry' ) ) {
					$hour = '' == get_option( 'rs_referral_cookies_expiry_in_hours' ) ? '1' : get_option( 'rs_referral_cookies_expiry_in_hours' ) ;
					setcookie( 'rsreferredusername' , $ref , time() + 60 * 60 * $hour , COOKIEPATH ? COOKIEPATH : '/' , COOKIE_DOMAIN , is_ssl() , true ) ;
					if ( isset( $_GET[ 'ip' ] ) ) {
						setcookie( 'referrerip' , wc_clean(wp_unslash($_GET[ 'ip' ])) , time() + 60 * 60 * $hour , COOKIEPATH ? COOKIEPATH : '/' , COOKIE_DOMAIN , is_ssl() , true ) ;
					}
				} else {
					$day = '' == get_option( 'rs_referral_cookies_expiry_in_days' ) ? '1' : get_option( 'rs_referral_cookies_expiry_in_days' ) ;
					setcookie( 'rsreferredusername' , $ref , time() + 60 * 60 * 24 * $day , COOKIEPATH ? COOKIEPATH : '/' , COOKIE_DOMAIN , is_ssl() , true ) ;
					if ( isset( $_GET[ 'ip' ] ) ) {
						setcookie( 'referrerip' , wc_clean(wp_unslash($_GET[ 'ip' ])) , time() + 60 * 60 * 24 * $day , COOKIEPATH ? COOKIEPATH : '/' , COOKIE_DOMAIN , is_ssl() , true ) ;
					}
				}
				$UserInfo = get_user_by( 'login' , $ref ) ;
				$UserId   = is_object( $UserInfo ) ? $UserInfo->ID : $ref ;
				if ( isset( $_COOKIE[ 'rsreferredusername' ] ) ) {
					$previouscount = get_user_meta( $UserId , 'rsreferredusernameclickthrough' , true ) ;
					update_user_meta( $UserId , 'rsreferredusernameclickthrough' , ( float ) $previouscount + 1 ) ;
				}
			}
		}

		/*
		 * Unset Cookie based on referral registration date.
		 * 
		 * @return void. 
		 */

		public static function unset_cookie_based_on_referral_registration_date() {

			if ( ! check_if_referral_is_restricted() ) {
				return ;
			}

			if ( ! isset( $_COOKIE[ 'rsreferredusername' ] ) || ! is_user_logged_in() || ! self::check_limit_for_referral_link() ) {
				return ;
			}
						
						$cookie_name = wc_clean(wp_unslash($_COOKIE[ 'rsreferredusername' ]));
			// Referrer user object.
			$referrer_user = ( '1' == get_option( 'rs_generate_referral_link_based_on_user' ) ) ? get_user_by( 'login' , $cookie_name ) : get_user_by( 'ID' , $cookie_name ) ;
			if ( ! is_object( $referrer_user ) || ! $referrer_user->exists() ) {
				return ;
			}

			// Referred user object.
			$referred_user = get_user_by( 'ID' , get_current_user_id() ) ;
			if ( ! is_object( $referred_user ) || ! $referred_user->exists() ) {
				return ;
			}

			$referrer_registered_date = ! empty( $referrer_user->user_registered ) ? strtotime( $referrer_user->user_registered ) : 0 ;
			$referred_registered_date = ! empty( $referred_user->user_registered ) ? strtotime( $referred_user->user_registered ) : 0 ;
			// Return if referrer registered date less than referred registered date.
			if ( ! $referrer_registered_date || ! $referred_registered_date || $referrer_registered_date < $referred_registered_date ) {
				return ;
			}

			// Unset cookie.
			setcookie( 'rsreferredusername' , null , -1 , COOKIEPATH ? COOKIEPATH : '/' , COOKIE_DOMAIN , is_ssl() , true ) ;
			wc_add_notice( esc_html__( 'You cannot use this referral link.' , 'rewardsystem' ) , 'error' ) ;
		}

		public static function referrer_name() {
			if ( 2 == get_option( 'rs_show_hide_generate_referral_message' ) ) {
				return ;
			}

			if ( is_user_logged_in() ) {
				return ;
			}

			if ( ! isset( $_GET[ 'ref' ] ) ) {
				return ;
			}

			if ( ! check_if_referral_is_restricted() ) {
				return ;
			}

			if ( '1' == get_option( 'rs_enable_get_header' ) ) {
				get_header() ;
			}
						
			?>
			<div class="referral_field">
				<h4 class="referral_field_title"><?php echo wp_kses_post(do_shortcode( get_option( 'rs_show_hide_generate_referral_message_text' ) ) ); ?></h4>
			</div>
			<?php
		}

		/* Link Referral for Lifetime */

		public static function link_referral_for_lifetime() {
			if ( ! is_user_logged_in() ) {
				return ;
			}

			if ( ! isset( $_COOKIE[ 'rsreferredusername' ] ) ) {
				return ;
			}

			if ( 'yes' != get_option( 'rs_enable_referral_link_for_life_time' ) ) {
				return ;
			}

			$UserId  = get_current_user_id() ;
			$BanType = check_banning_type( $UserId ) ;
			if ( 'earningonly' == $BanType || 'both' == $BanType ) {
				return ;
			}

			if ( 'yes' == get_post_meta( $UserId , 'reward_manuall_referral_link' , true )) {
				return ;
			}
						
						$cookie_name = wc_clean(wp_unslash($_COOKIE[ 'rsreferredusername' ]));
			$RefUserName       = '1' == get_option( 'rs_generate_referral_link_based_on_user' ) ? get_user_by( 'login' , $cookie_name ) : get_userdata( $cookie_name ) ;
			$RefUserId         = $RefUserName->ID ;
			$ManualRefLinkRule = get_option( 'rewards_dynamic_rule_manual' ) ;
			if ( $UserId == $RefUserId ) {
				return ;
			}

			if ( srp_check_is_array( $ManualRefLinkRule ) ) {
				$boolvalue = self::check_if_user_and_referrer_are_same( $ManualRefLinkRule , $RefUserId , $UserId ) ;
				if ( $boolvalue ) {
					$merge[]  = array( 'referer' => esc_html( $RefUserName->ID ) , 'refferal' => esc_html( $UserId ) , 'type' => 'Automatic' ) ;
					$logmerge = array_merge( ( array ) $ManualRefLinkRule , $merge ) ;
					update_option( 'rewards_dynamic_rule_manual' , $logmerge ) ;
				}
			} else {
				$merge[] = array( 'referer' => esc_html( $RefUserName->ID ) , 'refferal' => esc_html( $UserId ) , 'type' => 'Automatic' ) ;
				update_option( 'rewards_dynamic_rule_manual' , $merge ) ;
			}
			update_post_meta( $UserId , 'reward_manuall_referral_link' , 'yes' ) ;
		}

		public static function check_if_user_and_referrer_are_same( $ManualRefLinkRule, $RefUserId, $UserId ) {

			foreach ( $ManualRefLinkRule as $EachRule ) {
				if ( ( $EachRule[ 'referer' ] == $RefUserId ) && ( $EachRule[ 'refferal' ] == $UserId ) ) {
					if ( $EachRule[ 'referer' ] == $UserId ) {
						return false ;
					}
				}
			}

			return true ;
		}

		public static function message_for_referral_product_purchase() {
			if ( ! is_user_logged_in() && 'yes' != get_option( 'rs_referrer_earn_point_purchase_by_guest_users' ) ) {
				return ;
			}

			if ( '1' == get_option( 'rs_award_points_for_cart_or_product_total_for_refferal_system' , 1 ) ) {
				$ShowReferralMsg = is_cart() ? get_option( 'rs_show_hide_message_for_total_points_referrel' ) : get_option( 'rs_show_hide_message_for_total_points_referrel_checkout' ) ;
				echo wp_kses_post(self::referral_product_purchase_msg_for_payment_plan_product()) ;
				echo wp_kses_post(self::referral_product_purchase_msg_for_each_product( $ShowReferralMsg )) ;
				echo wp_kses_post(self::display_referred_product_purchase_msg_based_on_product_total()) ;
			} else {
				self::display_referer_product_purchase_msg_based_on_cart_total() ;
				self::display_referred_product_purchase_msg_based_on_cart_total() ;
			}
		}

		/* Display Referred Product Purchase message */

		public static function display_referred_product_purchase_msg_based_on_product_total() {
			if ( isset( $_COOKIE[ 'rsreferredusername' ] ) ) {
				$cookie_name = wc_clean(wp_unslash($_COOKIE[ 'rsreferredusername' ]));
				$referrer = ( 1 == get_option( 'rs_generate_referral_link_based_on_user' ) ) ? get_user_by( 'login', $cookie_name ) : get_user_by( 'id', $cookie_name ) ;
				if ( ! is_object( $referrer ) ) {
					return ;
				}

				$referrer_id = $referrer->ID ;
			} else {
				$referrer_id = check_if_referrer_has_manual_link( get_current_user_id() ) ;
			}

			if ( ! $referrer_id ) {
				return ;
			}
			
			$referred_user_msg = is_cart() ? get_option( 'rs_show_or_hide_product_total_referred_msg_in_cart', '1' ) : get_option( 'rs_show_or_hide_product_total_referred_msg_in_checkout', '1' ) ;
			if ( '2' == $referred_user_msg ) {
				return ;
			}

			global $producttitle ;
			$product = srp_product_object( $producttitle ) ;

			if ( ! srp_check_is_array( WC()->cart->cart_contents ) ) {
				return ;
			}

			if ( ! rs_restrict_referral_system_purchase_point_for_free_shipping() ) {
				return ;
			}
			
			$referred_points = 0 ;
			foreach ( WC()->cart->cart_contents as $value ) {
				$args            = array(
					'productid'        => isset( $value[ 'product_id' ] ) ? $value[ 'product_id' ] : 0,
					'variationid'      => isset( $value[ 'variation_id' ] ) ? $value[ 'variation_id' ] : 0,
					'item'             => $value,
					'getting_referrer' => 'yes',
					'referred_user'    => get_current_user_id(),
						) ;
				$referred_points = check_level_of_enable_reward_point( $args ) ;
			}
			
			if (!$referred_points) {
				return;
			}

			$cart_msg     = get_option( 'rs_product_total_referred_msg_in_cart', 'Purchase this product <strong>[titleofproduct]</strong> & earn <strong>[referredpoints]</strong> for getting referred' ) ;
			$checkout_msg = get_option( 'rs_product_total_referred_msg_in_checkout', 'Purchase this product <strong>[titleofproduct]</strong> & earn <strong>[referredpoints]</strong> for getting referred' ) ;
			$referred_msg = is_cart() ? $cart_msg : $checkout_msg ;
			$title        = is_object($product) ? $product->get_title() : '';
			$referred_msg = str_replace( array( '[titleofproduct]', '[referredpoints]' ), array( $title, round_off_type( $referred_points ) ), $referred_msg ) ;

			wc_print_notice( $referred_msg, 'notice' ) ;
		}

		/* Display Referrer Product Purchase message Based on Cart Total */

		public static function display_referer_product_purchase_msg_based_on_cart_total() {
			
			if ( isset( $_COOKIE[ 'rsreferredusername' ] ) ) {
				$cookie_name = wc_clean(wp_unslash($_COOKIE[ 'rsreferredusername' ]));
				$referrer = ( get_option( 'rs_generate_referral_link_based_on_user' ) == 1 ) ? get_user_by( 'login' , $cookie_name ) : get_user_by( 'id' , $cookie_name ) ;
				if ( ! is_object( $referrer ) ) {
					return ;
				}

				$referrer_id = $referrer->ID ;
			} else {
				$referrer_id = check_if_referrer_has_manual_link( get_current_user_id() ) ;
			}

			if ( ! $referrer_id ) {
				return ;
			}
			
			$showreferralmsg = is_cart() ? get_option( 'rs_show_hide_message_for_cart_total_points_referrel_cart' , 1 ) : get_option( 'rs_show_hide_message_for_cart_total_points_referrel_checkout' , 1 ) ;
			if ( '2' == $showreferralmsg ) {
				return ;
			}

			$user_info = get_user_by( 'id', $referrer_id ) ;
			if ( ! is_object( $user_info ) ) {
				return ;
			}

			if ( ! rs_restrict_referral_system_purchase_point_for_free_shipping() ) {
				return ;
			}
		   
			$referrer_points = rs_get_reward_points_based_on_cart_total_for_referrer() ;
			if ( empty( $referrer_points ) ) {
				return ;
			}
			$cart_msg     = get_option( 'rs_referer_point_message_cart_total_based_in_cart_page', 'By completing this order, Referrer([rsreferredusername]) will earn <strong>[referrerpoints]</strong> reward points' ) ;
			$checkout_msg = get_option( 'rs_referer_point_message_cart_total_based_in_checkout_page', 'By completing this order, Referrer([rsreferredusername]) will earn <strong>[referrerpoints]</strong> reward points' ) ;
			$referrer_msg = is_cart() ? $cart_msg : $checkout_msg ;
			$referrer_msg = str_replace( array( '[rsreferredusername]', '[referrerpoints]' ), array( $user_info->user_login, $referrer_points ), $referrer_msg ) ;

			wc_print_notice( $referrer_msg, 'notice' ) ;
		}

		/* Display Referred User Product Purchase message Based on Cart Total */

		public static function display_referred_product_purchase_msg_based_on_cart_total() {

			if ( isset( $_COOKIE[ 'rsreferredusername' ] ) ) {
				$cookie_name = wc_clean(wp_unslash($_COOKIE[ 'rsreferredusername' ]));
				$referrer = ( 1 == get_option( 'rs_generate_referral_link_based_on_user' ) ) ? get_user_by( 'login', $cookie_name ) : get_user_by( 'id', $cookie_name ) ;
				if ( ! is_object( $referrer ) ) {
					return ;
				}

				$referrer_id = $referrer->ID ;
			} else {
				$referrer_id = check_if_referrer_has_manual_link( get_current_user_id() ) ;
			}

			if ( ! $referrer_id ) {
				return ;
			}
		   
			$referred_user_msg = is_cart() ? get_option( 'rs_show_or_hide_cart_total_referred_msg_in_cart' ) : get_option( 'rs_show_or_hide_cart_total_referred_msg_in_checkout' ) ;
			if ( '2' == $referred_user_msg ) {
				return ;
			}

			if ( ! rs_restrict_referral_system_purchase_point_for_free_shipping() ) {
				return ;
			}

			$referred_points = rs_get_reward_points_based_on_cart_total_for_referred() ;
			if ( ! $referred_points ) {
				return ;
			}
			
			$cart_msg     = get_option( 'rs_cart_total_referred_msg_in_cart', 'By Purchasing this order & earn <strong>[referredpoints]</strong> points for getting referred' ) ;
			$checkout_msg = get_option( 'rs_cart_total_referred_msg_in_checkout', 'By Purchasing this order & earn <strong>[referredpoints]</strong> points for getting referred' ) ;
			$referred_msg = is_cart() ? $cart_msg : $checkout_msg ;
			$referred_msg = str_replace( '[referredpoints]', $referred_points, $referred_msg ) ;

			wc_print_notice( $referred_msg, 'notice' ) ;
		}

		/* Display Referral Product Purchase message in Cart for SUMO Payment Plan */

		public static function referral_product_purchase_msg_for_payment_plan_product() {
			if ( isset( $_COOKIE[ 'rsreferredusername' ] ) ) {
				$cookie_name = wc_clean(wp_unslash($_COOKIE[ 'rsreferredusername' ]));
				$refuser = ( 1 == get_option( 'rs_generate_referral_link_based_on_user' ) ) ? get_user_by( 'login' , $cookie_name ) : get_user_by( 'id' , $cookie_name ) ;
				if ( ! $refuser ) {
					return ;
				}

				$myid = $refuser->ID ;
			} else {
				$myid = check_if_referrer_has_manual_link( get_current_user_id() ) ;
			}

			if ( ! $myid ) {
				return ;
			}
						
			if ( ! rs_restrict_referral_system_purchase_point_for_free_shipping() ) {
					return ;
			}

			$username      = get_user_by( 'id' , $myid )->user_login ;
			$ReferralPoint = self::referrel_points_for_product_in_cart( $myid ) ;
			if ( ! srp_check_is_array( $ReferralPoint ) ) {
				return ;
			}

			global $referralmsg_global ;
			global $referral_pointsnew ;
			global $ref_pdt_plan ;
			global $producttitle ;
			$referral_pointsnew = $ReferralPoint ;
			foreach ( $ReferralPoint as $ProductId => $Points ) {
				if ( empty( $Points ) ) {
					continue ;
				}

				$ProductObj = srp_product_object( $ProductId ) ;
				if ( ! is_object( $ProductObj ) ) {
					continue ;
				}

				if ( 'booking' == srp_product_type( $ProductId ) ) {
					continue ;
				}

				$producttitle = $ProductId ;
				if ( is_initial_payment( $ProductId ) ) {
					$ref_pdt_plan    = array( $Points ) ;
					$ShowReferralMsg = is_cart() ? get_option( 'rs_show_hide_message_for_total_payment_plan_points_referral' ) : get_option( 'rs_show_hide_message_for_total_payment_plan_points_referrel_checkout' ) ;
					$RefMsg          = is_cart() ? get_option( 'rs_referral_point_message_payment_plan_product_in_cart' ) : get_option( 'rs_referral_point_message_payment_plan_product_in_checkout' ) ;
					if ( 1 == $ShowReferralMsg ) {
						$RefMsg = str_replace( '[rsreferredusername]' , $username , $RefMsg ) ;
						?>
						<div class="woocommerce-info rs_referral_payment_plan_message_cart rs_cart_message"> <?php echo wp_kses_post(do_shortcode( $RefMsg )) ; ?>  </div>
						<?php
					}
				} else {
					$ReferralMsg                      = is_cart() ? get_option( 'rs_referral_point_message_product_in_cart' ) : get_option( 'rs_referral_point_message_product_in_checkout' ) ;
					$ReferralMsg                      = str_replace( '[rsreferredusername]' , $username , $ReferralMsg ) ;
					$referralmsg_global[ $ProductId ] = do_shortcode( $ReferralMsg ) . '<br>' ;
				}
			}
		}

		/* Assign Global Value($referral_pointsnew) */

		public static function referrel_points_for_product_in_cart( $UserId, $member_level = true ) {
			$referral_pointsnew = array() ;
			$BanType            = check_banning_type( $UserId ) ;
			if ( 'earningonly' == $BanType || 'both' == $BanType ) {
				return $referral_pointsnew ;
			}

			global $referral_pointsnew ;
			foreach ( WC()->cart->cart_contents as $value ) {
				$CheckIfSalePrice = block_points_for_salepriced_product( $value[ 'product_id' ] , $value[ 'variation_id' ] ) ;
				if ( 'yes' == $CheckIfSalePrice ) {
					continue ;
				}

				$args      = array(
					'productid'     => $value[ 'product_id' ] ,
					'variationid'   => $value[ 'variation_id' ] ,
					'item'          => $value ,
					'referred_user' => $UserId ,
						) ;
				$Points    = check_level_of_enable_reward_point( $args ) ;
				$Points    = $member_level ? RSMemberFunction::earn_points_percentage( $UserId , ( float ) $Points ) : ( float ) $Points ;
				$ProductId = ! empty( $value[ 'variation_id' ] ) ? $value[ 'variation_id' ] : $value[ 'product_id' ] ;

				$referral_pointsnew[ $ProductId ] = $Points ;
			}

			$referral_pointsnew = self::get_referrer_points_after_coupon_applied( $referral_pointsnew , array( 'referred_user' => $UserId ) ) ;

			return $referral_pointsnew ;
		}

		/* Get Referrer Points After Coupon Applied */

		public static function get_referrer_points_after_coupon_applied( $referrer_points, $args ) {

			if ( 'no' === get_option( 'rs_referral_points_after_discounts' ) || ! get_option( 'rs_referral_points_after_discounts' ) ) {
				return $referrer_points ;
			}

			if ( ! srp_check_is_array( $referrer_points ) || ! array_filter( ( array ) $referrer_points ) ) {
				return $referrer_points ;
			}

			$ModifiedPoints = array() ;

			foreach ( $referrer_points as $ProductId => $Point ) {
				$ModifiedPoints[ $ProductId ] = ( float ) RSFrontendAssets::coupon_points_conversion( $ProductId , $Point , $args ) ;
			}

			return $ModifiedPoints ;
		}

		/* Display Referral Product Purchase message in Cart/Checkout for Product */

		public static function referral_product_purchase_msg_for_each_product( $ShowReferralMsg ) {
			if ( isset( $_COOKIE[ 'rsreferredusername' ] ) ) {
				$cookie_name = wc_clean(wp_unslash($_COOKIE[ 'rsreferredusername' ]));
				$refuser = ( 1 == get_option( 'rs_generate_referral_link_based_on_user' ) ) ? get_user_by( 'login' , $cookie_name ) : get_user_by( 'id' , $cookie_name ) ;

				if ( ! $refuser ) {
					return ;
				}

				$myid = $refuser->ID ;
			} else {
				$myid = check_if_referrer_has_manual_link( get_current_user_id() ) ;
			}

			if ( ! $myid ) {
				return ;
			}

			if ( 2 == $ShowReferralMsg ) {
				return ;
			}

			if ( ! rs_restrict_referral_system_purchase_point_for_free_shipping() ) {
					return ;
			}

			global $referralmsg_global ;
			global $producttitle ;
			if ( ! srp_check_is_array( $referralmsg_global ) ) {
				return ;
			}
			?>
			<div class="woocommerce-info">
			<?php
			foreach ( $referralmsg_global as $ProductId => $msg ) {
				$producttitle = $ProductId ;
				echo wp_kses_post($msg) ;
			}
			?>
			</div>
			<?php
		}

		public static function award_points_for_referral_account_signup( $user_id ) {
			if ( ''!= get_post_meta( $user_id , 'rs_registered_user' , true ) ) {
				return ;
			}

			if ( 'yes' != get_option( '_rs_referral_enable_signups' ) ) {
				return ;
			}

			if ( ! isset( $_COOKIE[ 'rsreferredusername' ] ) ) {
				return ;
			}

			$user_info     = new WP_User( $user_id ) ;
			$user_reg_date = gmdate( 'Y-m-d h:i:sa' , strtotime( $user_info->user_registered ) ) ;
			$reg_date      = gmdate( 'Y-m-d h:i:sa' , strtotime( $user_reg_date . ' + ' . get_option( '_rs_select_referral_points_referee_time_content' ) . ' days ' ) ) ;
			$reg_date      = strtotime( $reg_date ) ;
			$current_date  = gmdate( 'Y-m-d h:i:sa' ) ;
			$current_date  = strtotime( $current_date ) ;
			//Is for Immediatly
			if ( '1' == get_option( '_rs_select_referral_points_referee_time' ) ) {
				$limitation = true ;
			} else {
				// Is for Limited Time with Number of Days
				$limitation = ( $current_date > $reg_date ) ? true : false ;
			}
			if ( false == $limitation ) {
				return ;
			}
						
			$cookie_name  = wc_clean(wp_unslash($_COOKIE[ 'rsreferredusername' ] ));
			$referreduser = get_user_by( 'login' , $cookie_name ) ;
			$refuserid    = ( false != $referreduser ) ? $referreduser->ID : $cookie_name ;
			$banning_type = check_banning_type( $refuserid ) ;
			if ( 'earningonly' == $banning_type || 'both' == $banning_type ) {
				return ;
			}

			// Instant Referral Registration Points
			if ( '1' ==  get_option( 'rs_select_referral_points_award' )) {
				if ( 'yes' != get_option( 'rs_referral_reward_signup_after_first_purchase' ) ) {
					self::award_referral_registration_points_instantly( $user_id , $refuserid ) ;
				} else {
					self::award_referral_registration_points_after_first_purchase( $user_id , $refuserid ) ;
				}
			} else {
				self::award_referral_registration_points_after_first_purchase( $user_id , $refuserid ) ;
			}

			if ( '1' == get_option( 'rs_referral_reward_signup_getting_refer' ) ) {
				if ( 'yes' == get_option( 'rs_referral_reward_getting_refer_after_first_purchase' ) ) {
					self::award_getting_referred_points_after_first_purchase( $user_id , $refuserid ) ;
				} else {
					self::award_getting_referred_points_instantly( $user_id , $refuserid ) ;
				}
			}

			if ( isset( $_COOKIE[ 'rsreferredusername' ] ) && allow_reward_points_for_user( $user_id ) ) {
				$UserInfo = get_user_by( 'login' , $cookie_name ) ;
				$RefId    = ( $UserInfo ) ? $UserInfo->ID : $cookie_name ;
				if ( $user_id != $RefId ) {
					$ReferralCount = ( int ) get_user_meta( $RefId , 'referral_link_count_value' , true ) ;
					update_user_meta( $RefId , 'referral_link_count_value' , $ReferralCount + 1 ) ;
				}
			}
		}

		/* Instant Referral Registration Points */

		public static function award_referral_registration_points_instantly( $user_id, $refuserid ) {
			if ( '1'  == get_user_meta( $user_id , 'rs_referrer_regpoints_awarded' , true ) ) {
				return ;
			}

			$Points  = get_option( 'rs_referral_reward_signup' ) ;
			$new_obj = new RewardPointsOrder( 0 , 'no' ) ;
			if ( 'yes' == get_option( 'rs_enable_disable_max_earning_points_for_user' )  ) {
				$new_obj->check_point_restriction( $Points , 0 , 'RRRP' , $refuserid , '' , $user_id , '' , '' , '' ) ;
			} else {
				$valuestoinsert = array( 'pointstoinsert' => $Points , 'event_slug' => 'RRRP' , 'user_id' => $refuserid , 'referred_id' => $user_id , 'totalearnedpoints' => $Points ) ;
				$new_obj->total_points_management( $valuestoinsert ) ;
				$previouslog    = get_option( 'rs_referral_log' ) ;
				RS_Referral_Log::update_referral_log( $refuserid , $user_id , $Points , array_filter( ( array ) $previouslog ) ) ;
				update_user_meta( $user_id , '_rs_i_referred_by' , $refuserid ) ;
			}

			do_action( 'fp_signup_points_for_referrer' , $refuserid , $user_id , $Points ) ;

			add_user_meta( $user_id , 'rs_referrer_regpoints_awarded' , '1' ) ;
		}

		/* After First Purchase Referral Registration Points */

		public static function award_referral_registration_points_after_first_purchase( $user_id, $refuserid ) {
			$mainpoints             = array() ;
			$mainpoints[ $user_id ] = array( 'userid' => $user_id , 'refuserid' => $refuserid , 'refpoints' => ( float ) get_option( 'rs_referral_reward_signup' ) ) ;
			update_user_meta( $user_id , 'srp_data_for_reg_points' , $mainpoints ) ;
		}

		/* After First Purchase Getting Referred Referral Registration Points */

		public static function award_getting_referred_points_after_first_purchase( $user_id, $refuserid ) {
			$mainpoints             = array() ;
			$mainpoints[ $user_id ] = array( 'userid' => $user_id , 'refpoints' => ( float ) get_option( 'rs_referral_reward_getting_refer' ) ) ;
			update_user_meta( $user_id , 'srp_data_for_get_referred_reg_points' , $mainpoints ) ;
		}

		/* Instant Getting Referred Referral Registration Points */

		public static function award_getting_referred_points_instantly( $user_id, $refuserid ) {
			if ( '1' == get_user_meta( $user_id , '_points_awarded_get_refer' , true )) {
				return ;
			}

			$RegPoints          = RSMemberFunction::earn_points_percentage( $user_id , ( float ) get_option( 'rs_referral_reward_getting_refer' ) ) ;
			$restrictuserpoints = get_option( 'rs_max_earning_points_for_user' ) ;
			$PointsData         = new RS_Points_Data( $user_id ) ;
			$Points             = $PointsData->total_available_points() ;
			if ( 'yes' == get_option( 'rs_enable_disable_max_earning_points_for_user' )) {
				if ( $Points <= $restrictuserpoints ) {
					$RegPoints = ( ( $Points + $RegPoints ) <= $restrictuserpoints ) ? $RegPoints : ( $restrictuserpoints - $Points ) ;
				} else {
					$RegPoints = 0 ;
				}
			}
			$table_args = array(
				'user_id'           => $user_id ,
				'pointstoinsert'    => $RegPoints ,
				'checkpoints'       => 'RRPGR' ,
				'totalearnedpoints' => $RegPoints ,
					) ;
			RSPointExpiry::insert_earning_points( $table_args ) ;
			RSPointExpiry::record_the_points( $table_args ) ;

			do_action( 'fp_signup_points_for_getting_referred' , $refuserid , $user_id , $RegPoints ) ;

			add_user_meta( $user_id , '_points_awarded_get_refer' , '1' ) ;
		}

	}

	RSFunctionForReferralSystem::init() ;
}
