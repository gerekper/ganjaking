<?php
/**
 * Handles the Cron.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'SRP_Cron_Handler' ) ) {

	/**
	 * Class.
	 * */
	class SRP_Cron_Handler {

		/**
		 *  Class initialization.
		 * */
		public static function init() {
			// Maybe set the Cron schedule event.
			add_filter( 'cron_schedules', array( __CLASS__, 'cron_interval' ) );
			// Maybe set the WP schedule event.
			add_action( 'init', array( __CLASS__, 'maybe_set_wp_schedule_event' ), 10 );
			// Handle the delivery emails.
			add_action( 'rscronjob', array( __CLASS__, 'handle_wp_cron' ) );
			// Handle Expiry email before points expired.
			add_action( 'rs_send_mail_before_expiry', array( __CLASS__, 'send_mail_before_expiry' ), 10 );
			// Award Product Purchase Points based on cron.
			add_action( 'rs_restrict_product_purchase_for_time', array( __CLASS__, 'award_product_purchase_points_based_on_cron' ), 10, 1 );
		}

		/**
		 *  Set Cron Interval for Birthday Coupon
		 */
		public static function cron_interval( $schedules ) {
			$interval  = (int) get_option( 'rs_mail_cron_time', '3' );
			$cron_type = get_option( 'rs_mail_cron_type', 'days' );

			if ( 'minutes' == $cron_type ) {
				$interval = $interval * 60;
			} elseif ( 'hours' == $cron_type ) {
				$interval = $interval * 3600;
			} elseif ( 'days' == $cron_type ) {
				$interval = $interval * 86400;
			}

			$schedules['rshourly'] = array(
				'interval' => $interval,
				'display'  => 'RS Hourly',
			);

			$schedules['rs_hourly'] = array(
				'interval' => 3600,
				'display'  => 'RS Hourly',
			);

			$schedules['srp_hourly'] = array(
				'interval' => 3600,
				'display'  => 'SRP Hourly',
			);

			return $schedules;
		}

		/**
		 * Maybe set the WP schedule event.
		 *
		 * @return void.
		 * */
		public static function maybe_set_wp_schedule_event() {

			delete_option( 'rscheckcronsafter' );
			if ( false == wp_next_scheduled( 'rscronjob' ) && 'yes' == get_option( 'rs_email_activated', 'no' ) ) {
				wp_schedule_event( time(), 'rshourly', 'rscronjob' );
			}

			if ( false == wp_next_scheduled( 'rs_send_mail_before_expiry' ) ) {
				if ( 'yes' == get_option( 'rs_email_template_expire_activated' ) ) {
					wp_schedule_event( time(), 'rs_hourly', 'rs_send_mail_before_expiry' );
				} else {
					wp_unschedule_event( time(), 'rs_hourly', 'rs_send_mail_before_expiry' );
				}
			}

			if ( wp_next_scheduled( 'srp_birthday_cron' ) == false ) {
				wp_schedule_event( time(), 'srp_hourly', 'srp_birthday_cron' );
			}

			if ( 'yes' == get_option( 'rs_anniversary_points_activated', 'no' ) && wp_next_scheduled( 'srp_anniversary_cron' ) == false ) {
				wp_schedule_event( time(), 'srp_hourly', 'srp_anniversary_cron' );
			}
		}

		/**
		 * Handles the WP cron.
		 *
		 * @return void.
		 * */
		public static function handle_wp_cron() {

			// Update the WP cron current date.
			update_option( 'srp_update_wp_cron_last_updated_date', SRP_Date_Time::get_mysql_date_time_format( 'now', true ) );

			// May be handle the expired coupon emails.
			self::send_mail_based_on_cron();
		}

		/**
		 * Handle Expiry Remainder Email.
		 */
		public static function send_mail_before_expiry() {

			if ( 'yes' != get_option( 'rs_email_template_expire_activated' ) ) {
				return;
			}

			$TemplateName = get_option( 'rs_select_template' );
			if ( empty( $TemplateName ) ) {
				return;
			}

			$no_of_days = (int) days_from_point_expiry_email();
			if ( ! $no_of_days ) {
				return;
			}

			global $wpdb;
			$templates = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}rs_expiredpoints_email WHERE template_name=%s AND rs_status='ACTIVE'", $TemplateName ), ARRAY_A );
			if ( ! srp_check_is_array( $templates ) ) {
				return;
			}

			$overall_point_data = $wpdb->get_results( $wpdb->prepare( "SELECT * , SUM(earnedpoints-usedpoints) as points FROM {$wpdb->prefix}rspointexpiry WHERE expirydate > %d AND expirydate NOT IN(999999999999) AND expiredpoints IN(0) GROUP BY expirydate", time() ), ARRAY_A );
			if ( ! srp_check_is_array( $overall_point_data ) ) {
				return;
			}

			$expiry_dates = array();
			$current_time = strtotime( gmdate( 'd-m-Y' ) );
			foreach ( $overall_point_data as $value ) {

				$expiry_date = isset( $value['expirydate'] ) ? absint( $value['expirydate'] ) : 0;
				$user_id     = isset( $value['userid'] ) ? absint( $value['userid'] ) : 0;
				$points      = isset( $value['points'] ) ? $value['points'] : 0;

				// Validate if expiry date/userid/points exists.
				if ( ! $expiry_date || ! $user_id || ! $points ) {
					continue;
				}

				// Validate if user is subscribed to email.
				if ( 'yes' == get_user_meta( $user_id, 'unsub_value', true ) ) {
					continue;
				}

				// Datetime.
				$legacy_date_to_send_mail = strtotime( '-' . $no_of_days . 'days', $expiry_date );
				// Backward Compatibility for Expiry Date.
				if ( in_array( $legacy_date_to_send_mail, (array) get_option( 'rs_point_expiry_email_send_based_on_date' ) ) ) {
					continue;
				}

				// Only date.
				$date_to_send_mail = strtotime( gmdate( 'd-m-Y', $legacy_date_to_send_mail ) );
				$user_expiry_dates = get_user_meta( $user_id, 'rs_point_expiry_email_send_based_on_date', true );
				// Validate if email is already sent for the user.
				if ( srp_check_is_array( $user_expiry_dates ) && in_array( $date_to_send_mail, (array) $user_expiry_dates ) ) {
					continue;
				}

				// Validate current date with the expiry date.
				if ( $current_time >= $date_to_send_mail ) {

					$expiry_dates[ $user_id ][] = $date_to_send_mail;
					$user_point_data            = $wpdb->get_results( $wpdb->prepare( "SELECT *,SUM(earnedpoints-usedpoints) as points FROM {$wpdb->prefix}rspointexpiry WHERE expirydate > %d  AND expirydate NOT IN(999999999999) AND expiredpoints IN(0) AND userid = %d GROUP BY expirydate", time(), $user_id ), ARRAY_A );
					// Send email.
					self::send_mail( $user_point_data, $user_id, $templates );
				}

				if ( isset( $expiry_dates[ $user_id ] ) && srp_check_is_array( $expiry_dates[ $user_id ] ) ) {
					// Update expiry dates for the user when the email is triggered.
					update_user_meta( $user_id, 'rs_point_expiry_email_send_based_on_date', array_filter( array_unique( array_merge( $expiry_dates[ $user_id ], (array) $user_expiry_dates ) ) ) );
				}
			}
		}

		public static function send_mail( $newdata, $userid, $Templates ) {

			if ( ! srp_check_is_array( $newdata ) ) {
				return;
			}

			$user              = get_userdata( $userid );
			$user_wmpl_lang    = empty( $user_wmpl_lang ) ? 'en' : get_user_meta( $userid, 'rs_wpml_lang', true );
			$site_referral_url = 'yes' == get_option( 'rs_restrict_referral_points_for_same_ip' ) ? esc_url_raw(
				add_query_arg(
					array(
						'ref' => $user->user_login,
						'ip'  => base64_encode( get_referrer_ip_address() ),
					),
					site_url()
				)
			) : esc_url_raw( add_query_arg( array( 'ref' => $user->user_login ), site_url() ) );
			$subject           = $Templates[0]['subject'];
			$url_to_click      = '<a href=' . site_url() . '>' . site_url() . '</a>';
			$wpnonce           = wp_create_nonce( 'rs_unsubscribe_' . $userid );
			$unsublink         = esc_url_raw(
				add_query_arg(
					array(
						'userid' => $userid,
						'unsub'  => 'yes',
						'nonce'  => $wpnonce,
					),
					site_url()
				)
			);
			$unsublink         = '<a href=' . $unsublink . '>' . $unsublink . '</a>';
			$message           = $Templates[0]['message'];
			$message           = str_replace( array( '{rssitelink}', '{rsfirstname}', '{rslastname}', '{site_referral_url}', '{rs_points_expire}' ), array( $url_to_click, $user->user_firstname, $user->user_lastname, $site_referral_url, self::email_content( $newdata ) ), $message );
			$message           = do_shortcode( $message ); // shortcode feature

			global $unsublink2;
			$unsublink2 = str_replace( '{rssitelinkwithid}', $unsublink, get_option( 'rs_unsubscribe_link_for_email' ) );
			add_filter( 'woocommerce_email_footer_text', 'srp_footer_link' );
			ob_start();
			wc_get_template( 'emails/email-header.php', array( 'email_heading' => $subject ) );
			echo do_shortcode( $message );
			wc_get_template( 'emails/email-footer.php' );
			$woo_temp_msg = ob_get_clean();
			$headers      = "MIME-Version: 1.0\r\n";
			$headers     .= "Content-Type: text/html; charset=UTF-8\r\n";

			if ( 'local' == $Templates[0]['sender_opt'] ) {
				FPRewardSystem::$rs_from_email_address = $Templates[0]['from_email'];
				FPRewardSystem::$rs_from_name          = $Templates[0]['from_name'];
			}
			add_filter( 'woocommerce_email_from_address', 'rs_alter_from_email_of_woocommerce', 10, 2 );
			add_filter( 'woocommerce_email_from_name', 'rs_alter_from_name_of_woocommerce', 10, 2 );

			if ( WC_VERSION <= (float) ( '2.2.0' ) ) {
				wp_mail( $user->user_email, $subject, $woo_temp_msg, $headers = '' );
			} else {
				$mailer = WC()->mailer();
				$mailer->send( $user->user_email, $subject, $woo_temp_msg, $headers );
			}
			remove_filter( 'woocommerce_email_from_address', 'rs_alter_from_email_of_woocommerce', 10, 2 );
			remove_filter( 'woocommerce_email_from_name', 'rs_alter_from_name_of_woocommerce', 10, 2 );
			FPRewardSystem::$rs_from_email_address = false;
			FPRewardSystem::$rs_from_name          = false;

			/**
			 * This hook is used to do extra action when point expiry email is sent.
			 *
			 * @param string $user->user_email User Email Id.
			 * @param string $subject Email Subject.
			 * @param string $woo_temp_msg Email Message.
			 * @since 29.7.0
			 */
			do_action( 'fp_after_point_expiry_email_sent_successfully', $user->user_email, $subject, $woo_temp_msg );
		}

		public static function email_content( $newdata ) {
			$sliced_array = array_slice( $newdata, 0, 50, true );
			ob_start();
			?>
			<table class="fp-srp-email-content">
				<thead class="fp-srp-email-content-title">
					<tr>
						<th><?php esc_html_e( 'S.No', 'rewardsystem' ); ?></th>
						<th><?php esc_html_e( 'Points', 'rewardsystem' ); ?></th>
						<th><?php esc_html_e( 'Expiry Date', 'rewardsystem' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					$i = 1;
					foreach ( $sliced_array as $data ) :

						$points = isset( $data['points'] ) ? $data['points'] : 0;

						if ( ! $points ) :
							continue;
						endif;

												$expiry_date = isset( $data['expirydate'] ) ? gmdate( 'd-m-Y H:i A', $data['expirydate'] ) : '';
						?>
						<tr>
							<td><?php echo esc_html( $i ); ?></td>
							<td><?php echo esc_html( round_off_type( $points ) ); ?></td>
							<td><?php echo esc_html( $expiry_date ); ?></td>
						</tr>
						<?php
						$i++;
					endforeach;
					?>
				</tbody>
			</table>
			<?php
			$content = ob_get_clean();
			ob_end_clean();
			return $content;
		}

		/**
		 *  Award Points for Product Purchase based on Cron Time.
		 *
		 * @param int $order_id Order ID.
		 */
		public static function award_product_purchase_points_based_on_cron( $order_id ) {
			$order = wc_get_order( $order_id );
			if ( 'yes' === $order->get_meta( 'rs_order_status_reached' ) ) {
				award_points_for_product_purchase_based_on_cron( $order_id );
			}
		}

		/**
		 *  Send Mail based on cron.
		 */
		public static function send_mail_based_on_cron() {
			if ( 'yes' !== get_option( 'rs_email_activated' ) ) {
				return;
			}

			global $wpdb;
			$email_templates = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}rs_templates_email" );
			if ( ! srp_check_is_array( $email_templates ) ) {
				return;
			}

			global $wpdb;
			$matched_user_ids = $wpdb->get_col( "SELECT userid FROM {$wpdb->prefix}rspointexpiry WHERE earnedpoints-usedpoints NOT IN(0) and expiredpoints IN(0)" );
			if ( ! srp_check_is_array( $matched_user_ids ) ) {
				return;
			}

			$matched_user_ids = get_users(
				array(
					'include' => $matched_user_ids,
					'fields'  => 'ids',
				)
			);
			if ( ! srp_check_is_array( $matched_user_ids ) ) {
				return;
			}
			/**
			 * Hook:rs_cron_job_email_chunk_value.
			 *
			 * @since 1.0
			 */
			$chunk_value      = apply_filters( 'rs_cron_job_email_chunk_value', 100 );
			$chunked_user_ids = array_chunk( $matched_user_ids, $chunk_value );

			foreach ( $email_templates as $emails ) {

				$template_id = $emails->id;

				if ( 'ACTIVE' !== $emails->rs_status ) {
					continue;
				}

				if ( 3 !== $emails->rsmailsendingoptions ) {
					continue;
				}

				$SiteUrl = '<a href=' . site_url() . '>' . site_url() . '</a>';
				if ( '1' === $emails->mailsendingoptions ) {
					$maindata = (int) get_option( 'rscheckcronsafter' ) + 1;
					update_option( 'rscheckcronsafter', $maindata );

					if ( get_option( 'rscheckcronsafter' ) > 1 ) {
						continue;
					}

					if ( '1' === $emails->sendmail_options ) {
						foreach ( $chunked_user_ids as $user_ids ) {
							foreach ( $user_ids as $user_id ) {

								if ( 'yes' === get_user_meta( $user_id, 'unsub_value', true ) ) {
									continue;
								}

								$useremail_occured_template_ids = get_user_meta( $user_id, 'rs_emails_occured_once_based_on_template', true );
								if ( in_array( $template_id, $useremail_occured_template_ids ) ) {
									continue;
								}

								$PointsData = new RS_Points_data( $user_id );
								$userpoint  = $PointsData->total_available_points();

								if ( empty( $userpoint ) ) {
									continue;
								}

								$minimumuserpoints = empty( $emails->minimum_userpoints ) ? 0 : $emails->minimum_userpoints;
								if ( $minimumuserpoints > $userpoint ) {
									continue;
								}

								self::available_points_mail_based_on_cron( $user_id, $emails, $SiteUrl, $userpoint );

								if ( ! srp_check_is_array( $useremail_occured_template_ids ) ) {
									update_user_meta( $user_id, 'rs_emails_occured_once_based_on_template', array_filter( array( $template_id ) ) );
								} elseif ( ! in_array( $template_id, $useremail_occured_template_ids ) ) {
										update_user_meta( $user_id, 'rs_emails_occured_once_based_on_template', array_unique( array_merge( $useremail_occured_template_ids, array_filter( array( $template_id ) ) ) ) );
								}
							}
						}
					} else { // Send Mail for Selected User
						$selected_users = maybe_unserialize( $emails->sendmail_to );
						if ( ! srp_check_is_array( $selected_users ) ) {
							continue;
						}

						foreach ( $selected_users as $user_id ) {

							if ( 'yes' == get_user_meta( $user_id, 'unsub_value', true ) ) {
								continue;
							}

							$useremail_occured_template_ids = get_user_meta( $user_id, 'rs_emails_occured_once_based_on_template', true );
							if ( in_array( $template_id, $useremail_occured_template_ids ) ) {
								continue;
							}

							$PointsData = new RS_Points_data( $user_id );
							$userpoint  = $PointsData->total_available_points();

							if ( empty( $userpoint ) ) {
								continue;
							}

							$minimumuserpoints = empty( $emails->minimum_userpoints ) ? 0 : $emails->minimum_userpoints;
							if ( $minimumuserpoints > $userpoint ) {
								continue;
							}

							self::available_points_mail_based_on_cron( $user_id, $emails, $SiteUrl, $userpoint );

							if ( ! srp_check_is_array( $useremail_occured_template_ids ) ) {
								update_user_meta( $user_id, 'rs_emails_occured_once_based_on_template', array_filter( array( $template_id ) ) );
							} elseif ( ! in_array( $template_id, $useremail_occured_template_ids ) ) {
									update_user_meta( $user_id, 'rs_emails_occured_once_based_on_template', array_unique( array_merge( $useremail_occured_template_ids, array_filter( array( $template_id ) ) ) ) );
							}
						}
					}
				} elseif ( '1' == $emails->sendmail_options ) { // Send Mail Always
					// Send Mail for All User
					foreach ( $chunked_user_ids as $user_ids ) {
						foreach ( $user_ids as $user_id ) {
							if ( 'yes' == get_user_meta( $user_id, 'unsub_value', true ) ) {
								continue;
							}

							$PointsData = new RS_Points_data( $user_id );
							$userpoint  = $PointsData->total_available_points();

							if ( empty( $userpoint ) ) {
								continue;
							}

							$minimumuserpoints = empty( $emails->minimum_userpoints ) ? 0 : $emails->minimum_userpoints;
							if ( $minimumuserpoints > $userpoint ) {
								continue;
							}

							self::available_points_mail_based_on_cron( $user_id, $emails, $SiteUrl, $userpoint );
						}
					}
				} else { // Send Mail for Selected User
					$selected_users = maybe_unserialize( $emails->sendmail_to );
					if ( ! srp_check_is_array( $selected_users ) ) {
						continue;
					}

					foreach ( $selected_users as $user_id ) {
						if ( 'yes' == get_user_meta( $user_id, 'unsub_value', true ) ) {
							continue;
						}

						$PointsData = new RS_Points_data( $user_id );
						$userpoint  = $PointsData->total_available_points();

						if ( empty( $userpoint ) ) {
							continue;
						}

						$minimumuserpoints = empty( $emails->minimum_userpoints ) ? 0 : $emails->minimum_userpoints;
						if ( $minimumuserpoints > $userpoint ) {
							continue;
						}

						self::available_points_mail_based_on_cron( $user_id, $emails, $SiteUrl, $userpoint );
					}
				}
			}
		}

		public static function available_points_mail_based_on_cron( $userid, $emails, $SiteUrl, $userpoint ) {
			$user              = get_userdata( $userid );
			$user_wmpl_lang    = empty( get_user_meta( $userid, 'rs_wpml_lang', true ) ) ? 'en' : get_user_meta( $userid, 'rs_wpml_lang', true );
			$subject           = RSWPMLSupport::fp_wpml_text( 'rs_template_' . $emails->id . '_subject', $user_wmpl_lang, $emails->subject );
			$PointsValue       = redeem_point_conversion( $userpoint, $userid, 'price' );
			$PointsValue       = srp_formatted_price( round_off_type_for_currency( $PointsValue ) );
			$referral_url      = '' != get_option( 'rs_referral_link_refer_a_friend_form' ) ? get_option( 'rs_referral_link_refer_a_friend_form' ) : site_url();
			$site_referral_url = 'yes' == get_option( 'rs_restrict_referral_points_for_same_ip' ) ? esc_url_raw(
				add_query_arg(
					array(
						'ref' => $user->user_login,
						'ip'  => base64_encode( get_referrer_ip_address() ),
					),
					$referral_url
				)
			) : esc_url_raw( add_query_arg( array( 'ref' => $user->user_login ), $referral_url ) );
			$site_referral_url = 'yes' == get_option( 'rs_referral_activated' ) ? '<a href=' . $site_referral_url . '>' . $site_referral_url . '</a>' : '';
			$message           = RSWPMLSupport::fp_wpml_text( 'rs_template_' . $emails->id . '_message', $user_wmpl_lang, $emails->message );
			$message           = str_replace( array( '{rssitelink}', '{rsfirstname}', '{rslastname}', '{site_referral_url}', '{rspoints}', '{rs_points_in_currency}' ), array( $SiteUrl, $user->user_firstname, $user->user_lastname, $site_referral_url, $userpoint, $PointsValue ), $message );
			$message           = do_shortcode( $message ); // shortcode feature
			if ( 'local' == $emails->sender_opt ) {
				FPRewardSystem::$rs_from_email_address = $emails->from_email;
				FPRewardSystem::$rs_from_name          = $emails->from_name;
			}
			add_filter( 'woocommerce_email_from_address', 'rs_alter_from_email_of_woocommerce', 10, 2 );
			add_filter( 'woocommerce_email_from_name', 'rs_alter_from_name_of_woocommerce', 10, 2 );
			send_mail( $user->user_email, $subject, $message );
			remove_filter( 'woocommerce_email_from_address', 'rs_alter_from_email_of_woocommerce', 10, 2 );
			remove_filter( 'woocommerce_email_from_name', 'rs_alter_from_name_of_woocommerce', 10, 2 );
			FPRewardSystem::$rs_from_email_address = false;
			FPRewardSystem::$rs_from_name          = false;
		}
	}

	SRP_Cron_Handler::init();
}
