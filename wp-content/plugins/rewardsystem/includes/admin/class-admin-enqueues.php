<?php

/*
 * Admin Side Enqueues
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if ( ! class_exists( 'SRP_Admin_Enqueue' ) ) {

	/**
	 * Class.
	 */
	class SRP_Admin_Enqueue {

		/**
		 * Suffix.
		 *
		 * @var string
		 */
		private static $suffix;

		/**
		 * Class Initialization.
		 */
		public static function init() {
			self::$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'external_js_files' ), 20 );
			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'external_css_files' ) );
		}

		/**
		 * Enqueue external JS files.
		 */
		public static function external_css_files() {
			$screen_ids   = fp_srp_page_screen_ids();
			$newscreenids = get_current_screen();
			$screenid     = str_replace( 'edit-', '', $newscreenids->id );

			if ( ! in_array( $screenid, $screen_ids ) ) {
				return;
			}

			wp_enqueue_style( 'fp-srp-admin', SRP_PLUGIN_URL . '/assets/css/admin.css', array( 'woocommerce_admin_styles', 'wc-admin-layout' ), SRP_VERSION );
			wp_enqueue_style( 'fp-srp-style', SRP_PLUGIN_URL . '/assets/css/style.css', array(), SRP_VERSION );
			wp_enqueue_style( 'footable-core', SRP_PLUGIN_URL . '/assets/css/footable.core.css', array(), SRP_VERSION );
			wp_enqueue_style( 'bootstrap', SRP_PLUGIN_URL . '/assets/css/bootstrap.css', array(), SRP_VERSION );

			if ( '1' == get_option( 'rs_color_scheme' ) ) {
				wp_enqueue_style( 'fp-srp-dark-theme', SRP_PLUGIN_URL . '/assets/css/fp-srp-dark-theme.css', array(), SRP_VERSION );
			} else {
				wp_enqueue_style( 'fp-srp-light-theme', SRP_PLUGIN_URL . '/assets/css/fp-srp-light-theme.css', array(), SRP_VERSION );
			}

			self::add_inline_style();
		}

				/**
				 * Add Inline Style.
				 * */
		public static function add_inline_style() {
			$contents = '.fp-srp-point-price {
				margin-left: ' . get_option( 'rs_pixel_val' ) . 'px;
			}';

			if ( ! class_exists( 'SUMOSubscriptions' ) && ! class_exists( 'WC_Subscriptions' ) ) {
				$contents .= '.rs_subscription_compatible_wrapper{
                                        display:none;
                                }';
			}

			if ( ! class_exists( 'SUMORewardcoupons' ) ) {
					$contents .= '.rs_coupon_compatible_wrapper{
                                        display:none;
                                }';
			}

			if ( ! class_exists( 'FPWaitList' ) ) {
					$contents .= '.rs_bsn_compatible_wrapper{
                                        display:none;
                                }';
			}

			if ( ! class_exists( 'FPWCRS' ) ) {
					$contents .= '.rs_fpwcrs_compatible_wrapper{
                                        display:none;
                                }';
			}

			if ( ! class_exists( 'FS_Affiliates' ) ) {
					$contents .= '.rs_affs_compatible_wrapper{
                                        display:none;
                                }';
			}

			if ( ! class_exists( 'SUMOPaymentPlans' ) ) {
					$contents .= '.rs_payment_plan_compatible_wrapper{
                                        display:none;
                                }';
			}

			if ( ! class_exists( 'SUMOMemberships' ) ) {
					$contents .= '.rs_membership_compatible_wrapper{
                                        display:none;
                                }';
			}

						$contents .= '.srp-inactive-rule {
                            opacity: 0.6;
                            pointer-events:none;
                        }';

			// Add inline style.
			wp_register_style( 'fp-srp-admin-enqueues-style' , false , array() , SRP_VERSION ) ; // phpcs:ignore
			wp_enqueue_style( 'fp-srp-admin-enqueues-style' );
			wp_add_inline_style( 'fp-srp-admin-enqueues-style', $contents );
		}

		public static function external_js_files() {
			$screen_ids   = fp_srp_page_screen_ids();
			$newscreenids = get_current_screen();
			$screenid     = str_replace( 'edit-', '', $newscreenids->id );

			$page    = ( isset( $_GET['page'] ) && 'rewardsystem_callback' == wc_clean( wp_unslash( $_GET['page'] ) ) );
			$tab     = isset( $_GET['tab'] ) ? wc_clean( wp_unslash( $_GET['tab'] ) ) : '';
			$section = isset( $_GET['section'] ) ? wc_clean( wp_unslash( $_GET['section'] ) ) : '';

			$enqueue_array = array(
				'srp-admin'                              => array(
					'callable' => array( 'SRP_Admin_Enqueue', 'admin_script' ),
					'restrict' => true,
				),
				'srp-lightcase-enqueue'                              => array(
					'callable' => array( 'SRP_Admin_Enqueue', 'enqueue_lightcase_script' ),
					'restrict' => in_array( $screenid , array( 'woocommerce_page_wc-orders', 'shop_order' ) ),
				),
				'srp-general-tab'                        => array(
					'callable' => array( 'SRP_Admin_Enqueue', 'enqueue_for_general_tab' ),
					'restrict' => $page,
				),
				'srp-modules-tab'                        => array(
					'callable' => array( 'SRP_Admin_Enqueue', 'enqueue_for_modules_tab' ),
					'restrict' => $page && 'fprsmodules' == $tab,
				),
				'srp-addremovepoints-tab'                => array(
					'callable' => array( 'SRP_Admin_Enqueue', 'enqueue_for_addremovepoints_tab' ),
					'restrict' => $page && ( 'fprsaddremovepoints' == $tab ),
				),
				'srp-message-tab'                        => array(
					'callable' => array( 'SRP_Admin_Enqueue', 'enqueue_for_message_tab' ),
					'restrict' => $page && ( 'fprsmessage' == $tab ),
				),
				'srp-userrewardpoints-tab'               => array(
					'callable' => array( 'SRP_Admin_Enqueue', 'enqueue_for_userrewardpoints_tab' ),
					'restrict' => $page && ( 'fprsuserrewardpoints' == $tab ),
				),
				'srp-masterlog-tab'                      => array(
					'callable' => array( 'SRP_Admin_Enqueue', 'enqueue_for_master_log_tab' ),
					'restrict' => $page && ( 'fprsmasterlog' == $tab ),
				),
				'srp-advance-tab'                        => array(
					'callable' => array( 'SRP_Admin_Enqueue', 'enqueue_for_advance_tab' ),
					'restrict' => $page && ( 'fprsadvanced' == $tab ),
				),
				'srp-product-purchase-modules-tab'       => array(
					'callable' => array( 'SRP_Admin_Enqueue', 'enqueue_for_product_purchase_modules_tab' ),
					'restrict' => 'fpproductpurchase' == $section || 'fpreferralsystem' == $section,
				),
				'srp-buying-points-modules-tab'          => array(
					'callable' => array( 'SRP_Admin_Enqueue', 'enqueue_for_buying_points_modules_tab' ),
					'restrict' => ( 'fpbuyingpoints' == $section ),
				),
				'srp-referral-modules-tab'               => array(
					'callable' => array( 'SRP_Admin_Enqueue', 'enqueue_for_referral_modules_tab' ),
					'restrict' => 'fpreferralsystem' == $section,
				),
				'srp-social-modules-tab'                 => array(
					'callable' => array( 'SRP_Admin_Enqueue', 'enqueue_for_social_modules_tab' ),
					'restrict' => 'fpsocialreward' == $section,
				),
				'srp-action-modules-tab'                 => array(
					'callable' => array( 'SRP_Admin_Enqueue', 'enqueue_for_action_modules_tab' ),
					'restrict' => 'fpactionreward' == $section,
				),
				'srp-redeem-modules-tab'                 => array(
					'callable' => array( 'SRP_Admin_Enqueue', 'enqueue_for_redeem_modules_tab' ),
					'restrict' => 'fpredeeming' == $section,
				),
				'srp-pointprice-modules-tab'             => array(
					'callable' => array( 'SRP_Admin_Enqueue', 'enqueue_for_pointprice_modules_tab' ),
					'restrict' => 'fppointprice' == $section,
				),
				'srp-email-modules-tab'                  => array(
					'callable' => array( 'SRP_Admin_Enqueue', 'enqueue_for_email_modules_tab' ),
					'restrict' => 'fpmail' == $section,
				),
				'srp-emailexpired-modules-tab'           => array(
					'callable' => array( 'SRP_Admin_Enqueue', 'enqueue_for_emailexpired_modules_tab' ),
					'restrict' => 'fpemailexpiredpoints' == $section,
				),
				'srp-giftvoucher-modules-tab'            => array(
					'callable' => array( 'SRP_Admin_Enqueue', 'enqueue_for_giftvoucher_modules_tab' ),
					'restrict' => 'fpgiftvoucher' == $section,
				),
				'srp-sms-modules-tab'                    => array(
					'callable' => array( 'SRP_Admin_Enqueue', 'enqueue_for_sms_modules_tab' ),
					'restrict' => 'fpsms' == $section,
				),
				'srp-coupon-modules-tab'                 => array(
					'callable' => array( 'SRP_Admin_Enqueue', 'enqueue_for_coupon_modules_tab' ),
					'restrict' => 'fpcoupon' == $section,
				),
				'srp-pointurl-modules-tab'               => array(
					'callable' => array( 'SRP_Admin_Enqueue', 'enqueue_for_pointurl_modules_tab' ),
					'restrict' => 'fppointurl' == $section,
				),
				'srp-impexp-modules-tab'                 => array(
					'callable' => array( 'SRP_Admin_Enqueue', 'enqueue_for_impexp_modules_tab' ),
					'restrict' => 'fpimportexport' == $section,
				),
				'srp-bday-modules-tab'                   => array(
					'callable' => array( 'SRP_Admin_Enqueue', 'enqueue_for_bday_modules_tab' ),
					'restrict' => 'fpbirthday' == $section,
				),
				'srp-promotional-modules-tab'            => array(
					'callable' => array( 'SRP_Admin_Enqueue', 'enqueue_for_promotional_modules_tab' ),
					'restrict' => 'fppromotional' == $section,
				),
				'srp-cashback-modules-tab'               => array(
					'callable' => array( 'SRP_Admin_Enqueue', 'enqueue_for_cashback_modules_tab' ),
					'restrict' => 'fpcashback' == $section,
				),
				'srp-rewardgateway-modules-tab'          => array(
					'callable' => array( 'SRP_Admin_Enqueue', 'enqueue_for_reward_gateway_modules_tab' ),
					'restrict' => 'fprewardgateway' == $section,
				),
				'srp-nominee-modules-tab'                => array(
					'callable' => array( 'SRP_Admin_Enqueue', 'enqueue_for_nominee_modules_tab' ),
					'restrict' => 'fpnominee' == $section,
				),
				'srp-sendpoints-modules-tab'             => array(
					'callable' => array( 'SRP_Admin_Enqueue', 'enqueue_for_sendpoints_modules_tab' ),
					'restrict' => 'fpsendpoints' == $section,
				),
				'srp-reportsinscv-modules-tab'           => array(
					'callable' => array( 'SRP_Admin_Enqueue', 'enqueue_for_reportsincsv_modules_tab' ),
					'restrict' => 'fpreportsincsv' == $section,
				),
				'srp-reset-modules-tab'                  => array(
					'callable' => array( 'SRP_Admin_Enqueue', 'enqueue_for_reset_modules_tab' ),
					'restrict' => 'fpreset' == $section,
				),
				'srp-discount-compatibility-modules-tab' => array(
					'callable' => array( 'SRP_Admin_Enqueue', 'enqueue_for_discount_compatibility_modules_tab' ),
					'restrict' => 'fpdiscounts' == $section,
				),
				'srp-bonus-points-modules-tab'           => array(
					'callable' => array( 'SRP_Admin_Enqueue', 'enqueue_for_bonus_points_modules_tab' ),
					'restrict' => 'fpbonuspoints' == $section,
				),
				'srp-anniversary-points-modules-tab'     => array(
					'callable' => array( 'SRP_Admin_Enqueue', 'enqueue_for_anniversary_points_modules_tab' ),
					'restrict' => 'fpanniversarypoints' == $section,
				),
			);
						/**
						 * Hook:fp_srp_admin_assets.
						 *
						 * @since 1.0
						 */
			$enqueue_array = apply_filters( 'fp_srp_admin_assets', $enqueue_array );
			if ( ! srp_check_is_array( $enqueue_array ) ) {
				return;
			}

			foreach ( $enqueue_array as $key => $enqueue ) {
				if ( ! srp_check_is_array( $enqueue ) ) {
					continue;
				}

				if ( $enqueue['restrict'] ) {
					call_user_func_array( $enqueue['callable'], array() );
				}
			}
		}

		public static function admin_script() {
			global $post;
			$sumo_bookings_check = false;
			if ( isset( $post->ID ) && isset( $post->post_type ) ) {
				if ( 'product' == $post->post_type ) {
					$sumo_bookings_check = is_sumo_booking_active( $post->ID );
				}
			}

			$link         = "<a id='rs_display_notice' data-methd='cron' href='#'>Click here</a>";
			$redirect_url = esc_url_raw(
				add_query_arg(
					array(
						'page'                  => 'rewardsystem_callback',
						'rs_background_process' => 'yes',
					),
					SRP_ADMIN_URL
				)
			);

			$localize_script = array(
				'ajaxurl'              => SRP_ADMIN_AJAX_URL,
				'rs_unsubscribe_email' => wp_create_nonce( 'unsubscribe-mail' ),
				'reset_confirm_msg'    => __( 'Are you sure want to Reset?', 'rewardsystem' ),
				'field_ids'            => '#_rewardsystem_assign_buying_points[type=text],#_rewardsystempoints[type=text],#_rewardsystempercent[type=text],'
				. '#_referralrewardsystempoints[type=text],#_referralrewardsystempercent[type=text],#_socialrewardsystempoints_facebook[type=text],'
				. '#_socialrewardsystempercent_facebook[type=text],#_socialrewardsystempoints_twitter[type=text],'
				. '#_socialrewardsystempercent_twitter[type=text],#_socialrewardsystempoints_google[type=text],#_socialrewardsystempercent_google[type=text],'
				. '#_socialrewardsystempoints_vk[type=text],#_socialrewardsystempercent_vk[type=text],#rs_max_earning_points_for_user[type=text],'
				. '#rs_earn_point[type=text],#rs_earn_point_value[type=text],#rs_redeem_point[type=text],#rs_redeem_point_value[type=text],'
				. '#rs_fixed_max_redeem_discount[type=text],#rs_global_reward_points[type=text],#rs_global_referral_reward_point[type=text],'
				. '#rs_global_reward_percent[type=text],#rs_global_referral_reward_percent[type=text],#rs_referral_cookies_expiry_in_days[type=text],'
				. '#rs_referral_link_limit[type=text],'
				. '#rs_referral_cookies_expiry_in_min[type=text],#rs_referral_cookies_expiry_in_hours[type=text],'
				. '#_rs_select_referral_points_referee_time_content[type=text],#rs_percent_max_redeem_discount[type=text],#rs_point_to_be_expire[type=number],'
				. '#rs_fixed_max_earn_points[type=text],#rs_percent_max_earn_points[type=text],#rs_reward_signup[type=text],'
				. '#rs_reward_product_review[type=text],#rs_referral_reward_signup[type=text],#rs_reward_points_for_login[type=text],'
				. '#rs_reward_user_role_administrator[type=text],#rs_reward_user_role_editor[type=text],#rs_reward_user_role_author[type=text],'
				. '#rs_reward_user_role_contributor[type=text],#rs_reward_user_role_subscriber[type=text],#rs_reward_user_role_customer[type=text],'
				. '#rs_reward_user_role_shop_manager[type=text],#rs_reward_addremove_points[type=text],#rs_percentage_cart_total_redeem[type=text],'
				. '#rs_first_time_minimum_user_points[type=text],#rs_minimum_user_points_to_redeem[type=text],#rs_minimum_redeeming_points[type=text],'
				. '#rs_maximum_redeeming_points[type=text],#rs_minimum_cart_total_points[type=text],#rs_percentage_cart_total_redeem_checkout[type=text],'
				. '#rs_local_reward_points[type=text],#rs_local_reward_percent[type=text],#rs_local_referral_reward_point[type=text],'
				. '#rs_local_referral_reward_percent[type=text],#rs_local_reward_points_facebook[type=text],#rs_local_reward_percent_facebook[type=text],'
				. '#rs_local_reward_points_twitter[type=text],#rs_local_reward_percent_twitter[type=text],#rs_local_reward_points_google[type=text],'
				. '#rs_local_reward_percent_google[type=text],#rs_local_reward_points_vk[type=text],#rs_local_reward_percent_vk[type=text],'
				. '#rs_global_social_facebook_reward_points[type=text],#rs_global_social_facebook_reward_percent[type=text],'
				. '#rs_global_social_twitter_reward_points[type=text],#rs_global_social_twitter_reward_percent[type=text],'
				. '#rs_global_social_google_reward_points[type=text],#rs_global_social_google_reward_percent[type=text],'
				. '#rs_global_social_vk_reward_points[type=text],#rs_global_social_vk_reward_percent[type=text],'
				. '#rs_global_social_facebook_reward_points_individual[type=text],#rs_global_social_facebook_reward_percent_individual[type=text],'
				. '#rs_global_social_twitter_reward_points_individual[type=text],#rs_global_social_twitter_reward_percent_individual[type=text],'
				. '#rs_global_social_google_reward_points_individual[type=text],#rs_global_social_google_reward_percent_individual[type=text],'
				. '#rs_global_social_vk_reward_points_individual[type=text],#rs_global_social_vk_reward_percent_individual[type=text],'
				. '#earningpoints[type=text],#rs_minimum_edit_userpoints[type=text],#rs_minimum_userpoints[type=text],#redeemingpoints[type=text],'
				. '#rs_mail_cron_time[type=text],#rs_point_voucher_reward_points[type=text],#rs_point_bulk_voucher_points[type=text],'
				. '#rs_minimum_points_encashing_request[type=text],#rs_maximum_points_encashing_request[type=text],#_reward_points[type=text],'
				. '#_reward_percent[type=text],#_referral_reward_points[type=text],#_referral_reward_percent[type=text],#rs_category_points[type=text],'
				. '#rs_category_percent[type=text],#referral_rs_category_points[type=text],#referral_rs_category_percent[type=text],'
				. '#social_facebook_rs_category_points[type=text],#social_facebook_rs_category_percent[type=text],'
				. '#social_twitter_rs_category_points[type=text],#social_twitter_rs_category_percent[type=text],#social_google_rs_category_points[type=text],'
				. '#social_google_rs_category_percent[type=text],#social_vk_rs_category_points[type=text],#social_vk_rs_category_percent[type=text]',
				'sumo_booking'         => $sumo_bookings_check,
				'redirect_url'         => $redirect_url,
				'upgrade_nonce'        => wp_create_nonce( 'fp-srp-upgrade' ),
				'birthday_confirm_msg' => __( 'Are you sure you want to add/update birthday date?', 'rewardsystem' ),
			);

			wp_enqueue_script( 'fp-srp-admin', SRP_PLUGIN_URL . '/assets/js/fp-srp-admin.js', array( 'jquery', 'jquery-ui-datepicker' ), SRP_VERSION );
			wp_localize_script( 'fp-srp-admin', 'fp_admin_params', $localize_script );

			wp_enqueue_script( 'fp-srp-compatability', SRP_PLUGIN_URL . '/assets/js/fp-srp-compatability-settings.js', array(), SRP_VERSION );

			if ( '1' == get_option( 'rs_expand_collapse' ) ) {
				wp_enqueue_script( 'fp-srp-collapse', SRP_PLUGIN_URL . '/assets/js/fp-srp-collapse.js', array(), SRP_VERSION );
			} else {
				wp_enqueue_script( 'fp-srp-expand', SRP_PLUGIN_URL . '/assets/js/fp-srp-expand.js', array(), SRP_VERSION );
			}

			wp_enqueue_script( 'fp-srp-settings', SRP_PLUGIN_URL . '/assets/js/tab/fp-srp-settings.js', array(), SRP_VERSION );
			wp_enqueue_script( 'fp-srp-product-page', SRP_PLUGIN_URL . '/assets/js/tab/fp-edit-product-page.js', array(), SRP_VERSION );
			wp_enqueue_script( 'fp_variable_product', SRP_PLUGIN_DIR_URL . 'assets/js/tab/fp-variation-product.js', array(), SRP_VERSION );

			/* Enqueue Footable JS */
			if ( '1' == get_option( 'rs_enable_footable_js', '1' ) ) {
				wp_enqueue_script( 'footable', SRP_PLUGIN_URL . '/assets/js/footable.js', array(), SRP_VERSION );
				wp_enqueue_script( 'footable_sort', SRP_PLUGIN_URL . '/assets/js/footable.sort.js', array(), SRP_VERSION );
				wp_enqueue_script( 'footable_paging', SRP_PLUGIN_URL . '/assets/js/footable.paginate.js', array(), SRP_VERSION );
				wp_enqueue_script( 'footable_filter', SRP_PLUGIN_URL . '/assets/js/footable.filter.js', array(), SRP_VERSION );
			}

			/* Enhanced JS */
			wp_enqueue_script( 'srp_enhanced', SRP_PLUGIN_URL . '/assets/js/srp-enhanced.js', array( 'jquery', 'select2', 'jquery-ui-datepicker' ), SRP_VERSION );
			wp_localize_script(
				'srp_enhanced',
				'srp_enhanced_params',
				array(
					'srp_wc_version'                  => WC_VERSION,
					'ajax_url'                        => SRP_ADMIN_AJAX_URL,
					'fp_pages_and_posts_search_nonce' => wp_create_nonce( 'fp-pages-and-posts-search-nonce' ),
					'search_nonce'                    => wp_create_nonce( 'srp-search-nonce' ),
					'search_customers'                => wp_create_nonce( 'search-customers' ),
					'search_products'                 => wp_create_nonce( 'search-products' ),
					'i18n_no_matches'                 => esc_html_x( 'No matches found', 'enhanced select', 'rewardsystem' ),
					'i18n_ajax_error'                 => esc_html_x( 'Loading failed', 'enhanced select', 'rewardsystem' ),
					'i18n_input_too_short_1'          => esc_html_x( 'Please enter 1 or more characters', 'enhanced select', 'rewardsystem' ),
					'i18n_input_too_short_n'          => esc_html_x( 'Please enter %qty% or more characters', 'enhanced select', 'rewardsystem' ),
					'i18n_input_too_long_1'           => esc_html_x( 'Please delete 1 character', 'enhanced select', 'rewardsystem' ),
					'i18n_input_too_long_n'           => esc_html_x( 'Please delete %qty% characters', 'enhanced select', 'rewardsystem' ),
					'i18n_selection_too_long_1'       => esc_html_x( 'You can only select 1 item', 'enhanced select', 'rewardsystem' ),
					'i18n_selection_too_long_n'       => esc_html_x( 'You can only select %qty% items', 'enhanced select', 'rewardsystem' ),
					'i18n_load_more'                  => esc_html_x( 'Loading more results&hellip;', 'enhanced select', 'rewardsystem' ),
					'i18n_searching'                  => esc_html_x( 'Searching&hellip;', 'enhanced select', 'rewardsystem' ),
				)
			);
		}

		/**
		 * Enqueue Apply cashback required JS files.
		 */
		public static function enqueue_lightcase_script() {
			wp_enqueue_script( 'srp-apply-points-script' , SRP_PLUGIN_URL . '/assets/js/apply-points.js' , array( 'jquery', 'jquery-blockui', 'wc-backbone-modal' ) , SRP_VERSION ) ;
			wp_localize_script(
					'srp-apply-points-script' , 'srp_redeem_points_params' , array(
				'ajax_url'               => SRP_ADMIN_AJAX_URL,
				'redeem_points_nonce' => wp_create_nonce( 'srp-redeem-point-nonce' ),
					)
			) ;

			// Lightcase CSS.
			wp_enqueue_style( 'lightcase', SRP_PLUGIN_URL . '/assets/css/lightcase' . self::$suffix . '.css', array(), SRP_VERSION );
			// Lightcase.
			wp_register_script( 'lightcase', SRP_PLUGIN_URL . '/assets/js/lightcase' . self::$suffix . '.js', array( 'jquery' ), SRP_VERSION );
			// Enhanced lightcase.
			wp_enqueue_script( 'srp-lightcase-enhanced', SRP_PLUGIN_URL . '/assets/js/srp-lightcase-enhanced.js', array( 'jquery', 'jquery-blockui', 'lightcase' ), SRP_VERSION );
			wp_localize_script(
				'srp-lightcase-enhanced',
				'srp_lightcase_params',
				array(
					'ajaxurl' => SRP_ADMIN_AJAX_URL,
				)
			);
		}

		public static function enqueue_for_general_tab() {
			$redirect_url = esc_url_raw(
				add_query_arg(
					array(
						'page'                            => 'rewardsystem_callback',
						'fp_bg_process_to_refresh_points' => 'yes',
					),
					SRP_ADMIN_URL
				)
			);
			$isadmin      = is_admin() ? 'yes' : 'no';
			wp_enqueue_script( 'fp_general_tab', SRP_PLUGIN_DIR_URL . 'assets/js/tab/fp-general-tab.js', array( 'jquery' ), SRP_VERSION );
			wp_localize_script(
				'fp_general_tab',
				'fp_general_tab_params',
				array(
					'ajaxurl'                         => SRP_ADMIN_AJAX_URL,
					'fp_refresh_points'               => wp_create_nonce( 'fp-refresh-points' ),
					'add_user_purchase_history_nonce' => wp_create_nonce( 'srp-add-user-purchase-history-nonce' ),
					'add_earning_percentage_nonce'    => wp_create_nonce( 'srp-add-earning-percentage-nonce' ),
					'fp_wc_version'                   => WC_VERSION,
					'isadmin'                         => $isadmin,
					'redirect'                        => $redirect_url,
				)
			);
		}

		public static function enqueue_for_modules_tab() {
			wp_enqueue_script( 'wp_jscolor_rewards', SRP_PLUGIN_DIR_URL . 'assets/js/jscolor/jscolor.js', array( 'jquery' ), SRP_VERSION );
			wp_enqueue_script( 'fp_module_tab', SRP_PLUGIN_DIR_URL . 'assets/js/tab/fp-module-tab.js', array( 'jquery' ), SRP_VERSION );
			wp_localize_script(
				'fp_module_tab',
				'fp_module_tab_params',
				array(
					'ajaxurl'            => SRP_ADMIN_AJAX_URL,
					'fp_activate_module' => wp_create_nonce( 'fp-activate-module' ),
					'fp_wc_version'      => WC_VERSION,
					'redirecturl'        => admin_url( 'admin.php?page=rewardsystem_callback&tab=fprsmodules' ),
					'activeclass'        => 'active_rs_box',
					'inactiveclass'      => 'inactive_rs_box',
					'section'            => isset( $_GET['section'] ) ? true : false,
					'image_default_url'  => SRP_PLUGIN_DIR_URL . '/assets/images/modules/',
				)
			);
		}

		public static function enqueue_for_addremovepoints_tab() {
			$redirect_url = esc_url_raw(
				add_query_arg(
					array(
						'page'                        => 'rewardsystem_callback',
						'fp_bg_process_to_add_points' => 'yes',
					),
					SRP_ADMIN_URL
				)
			);
			$isadmin      = is_admin() ? 'yes' : 'no';
			wp_enqueue_script( 'fp_addremovepoints_tab', SRP_PLUGIN_DIR_URL . 'assets/js/tab/fp-addremovepoints-tab.js', array( 'jquery', 'jquery-ui-datepicker' ), SRP_VERSION );
			wp_localize_script(
				'fp_addremovepoints_tab',
				'fp_addremovepoints_tab_params',
				array(
					'ajaxurl'            => SRP_ADMIN_AJAX_URL,
					'pointerrormsg'      => esc_html__( 'Please Enter Points', 'rewardsystem' ),
					'reasomerrormsg'     => esc_html__( 'Please Enter Reason', 'rewardsystem' ),
					'expirydateerrormsg' => esc_html__( 'Please enter the valid expiry date', 'rewardsystem' ),
					'current_date'       => gmdate( 'Y-m-d' ),
					'fp_add_points'      => wp_create_nonce( 'fp-add-points' ),
					'fp_remove_points'   => wp_create_nonce( 'fp-remove-points' ),
					'isadmin'            => $isadmin,
					'redirect'           => $redirect_url,
				)
			);
		}

		public static function enqueue_for_message_tab() {
			if ( function_exists( 'wp_enqueue_media' ) ) {
				wp_enqueue_media();
			} else {
				wp_enqueue_style( 'thickbox' );
				wp_enqueue_script( 'media-upload' );
				wp_enqueue_script( 'thickbox' );
			}
			$localize_script = array(
				'ajaxurl'       => SRP_ADMIN_AJAX_URL,
				'fp_wc_version' => WC_VERSION,
			);
			wp_enqueue_script( 'fp_msg_tab', SRP_PLUGIN_DIR_URL . 'assets/js/tab/fp-msg-tab.js', array( 'jquery' ), SRP_VERSION );
			wp_enqueue_script( 'jquery_ui', SRP_PLUGIN_DIR_URL . 'assets/js/jquery-ui.js', array( 'jquery' ), SRP_VERSION );
			wp_localize_script( 'fp_msg_tab', 'fp_message_params', $localize_script );
		}

		public static function enqueue_for_userrewardpoints_tab() {
			$UserId          = isset( $_GET['edit'] ) ? wc_clean( wp_unslash( $_GET['edit'] ) ) : 0;
			$PointsData      = new RS_Points_Data( $UserId );
			$Points          = $PointsData->total_available_points();
			$localize_script = array(
				'ajaxurl'                              => SRP_ADMIN_AJAX_URL,
				'fp_wc_version'                        => WC_VERSION,
				'available_points'                     => $Points,
				'restrict_user'                        => ( 'yes' == get_option( 'rs_enable_reward_program' ) ) ? get_user_meta( $UserId, 'allow_user_to_earn_reward_points', true ) : '',
				'restrict_add_points_error_message'    => esc_html__( 'As of now, this user is not involved in the reward program. Hence, you cannot add points to this user account.', 'rewardsystem' ),
				'restrict_remove_points_error_message' => esc_html__( 'As of now, this user is not involved in the reward program. Hence, you cannot remove points from this user account.', 'rewardsystem' ),
				'restrict_user_points_nonce'           => wp_create_nonce( 'srp-restrict-user-points-nonce' ),
				'hide_filter'                          => ( isset( $_GET['view'] ) || isset( $_GET['edit'] ) ),
			);
			wp_enqueue_script( 'fp_userrewardpoints_tab', SRP_PLUGIN_DIR_URL . 'assets/js/tab/fp-userrewardpoints-tab.js', array( 'jquery' ), SRP_VERSION );
			wp_localize_script( 'fp_userrewardpoints_tab', 'fp_userrewardpoints_tab_params', $localize_script );
		}

		public static function enqueue_for_master_log_tab() {
			$redirect_url    = esc_url_raw(
				add_query_arg(
					array(
						'page'                        => 'rewardsystem_callback',
						'fp_bg_process_to_export_log' => 'yes',
					),
					SRP_ADMIN_URL
				)
			);
			$localize_script = array(
				'ajaxurl'       => SRP_ADMIN_AJAX_URL,
				'redirecturl'   => $redirect_url,
				'fp_wc_version' => WC_VERSION,
				'fp_export_log' => wp_create_nonce( 'fp-export-log' ),
			);
			wp_enqueue_script( 'fp_masterlog_tab', SRP_PLUGIN_DIR_URL . 'assets/js/tab/fp-masterlog-tab.js', array( 'jquery' ), SRP_VERSION );
			wp_localize_script( 'fp_masterlog_tab', 'fp_masterlog_params', $localize_script );
		}

		public static function enqueue_for_advance_tab() {
			$redirect_url    = esc_url_raw(
				add_query_arg(
					array(
						'page'                          => 'rewardsystem_callback',
						'fp_bg_process_to_apply_points' => 'yes',
					),
					SRP_ADMIN_URL
				)
			);
			$localize_script = array(
				'ajaxurl'                  => SRP_ADMIN_AJAX_URL,
				'redirecturl'              => $redirect_url,
				'fp_wc_version'            => WC_VERSION,
				'fp_apply_points'          => wp_create_nonce( 'fp-apply-points' ),
				'fp_old_points'            => wp_create_nonce( 'fp-old-points' ),
				'from_to_date_range_error' => __( 'From Date and To Date Fields are mandatory', 'rewardsystem' ),
				'fp_unsubscribe_email'     => wp_create_nonce( 'fp-unsubscribe-email' ),
			);
			wp_enqueue_script( 'fp_advance_tab', SRP_PLUGIN_DIR_URL . 'assets/js/tab/fp-advance-tab.js', array( 'jquery', 'jquery-ui-sortable', 'jquery-ui-datepicker' ), SRP_VERSION );
			wp_localize_script( 'fp_advance_tab', 'fp_advance_params', $localize_script );
		}

		public static function enqueue_for_product_purchase_modules_tab() {
			$redirect_url    = esc_url_raw(
				add_query_arg(
					array(
						'page'                         => 'rewardsystem_callback',
						'fp_bg_process_to_bulk_update' => 'yes',
					),
					SRP_ADMIN_URL
				)
			);
			$localize_script = array(
				'ajaxurl'                      => SRP_ADMIN_AJAX_URL,
				'redirecturl'                  => $redirect_url,
				'fp_wc_version'                => WC_VERSION,
				'fp_bulk_update'               => wp_create_nonce( 'fp-bulk-update' ),
				'product_purchase_bulk_update' => wp_create_nonce( 'product-purchase-bulk-update' ),
				'range_based_rule_nonce'       => wp_create_nonce( 'srp-range-based-rule-nonce' ),
			);
			wp_enqueue_script( 'fp_product_purchase_module', SRP_PLUGIN_DIR_URL . 'assets/js/tab/modules/fp-productpurchase-module.js', array( 'jquery', 'jquery-ui-datepicker' ), SRP_VERSION );
			wp_localize_script( 'fp_product_purchase_module', 'fp_product_purchase_module_param', $localize_script );
		}

		public static function enqueue_for_buying_points_modules_tab() {
			$buyingredirect_url = esc_url_raw(
				add_query_arg(
					array(
						'page' => 'rewardsystem_callback',
						'fp_bg_process_to_buying_points_bulk_update' => 'yes',
					),
					SRP_ADMIN_URL
				)
			);
			$localize_script    = array(
				'ajaxurl'                   => SRP_ADMIN_AJAX_URL,
				'buyingredirecturl'         => $buyingredirect_url,
				'fp_wc_version'             => WC_VERSION,
				'buying_reward_bulk_update' => wp_create_nonce( 'buying-reward-bulk-update' ),
			);
			wp_enqueue_script( 'fp_buyingpoints_module', SRP_PLUGIN_DIR_URL . 'assets/js/tab/modules/fp-buyingpoints-module.js', array( 'jquery' ), SRP_VERSION );
			wp_localize_script( 'fp_buyingpoints_module', 'fp_buyingpoints_module_param', $localize_script );
		}

		public static function enqueue_for_referral_modules_tab() {
			if ( function_exists( 'wp_enqueue_media' ) ) {
				wp_enqueue_media();
			} else {
				wp_enqueue_style( 'thickbox' );
				wp_enqueue_script( 'media-upload' );
				wp_enqueue_script( 'thickbox' );
			}
			wp_enqueue_script( 'fp_referral_module', SRP_PLUGIN_DIR_URL . 'assets/js/tab/modules/fp-referral-module.js', array( 'jquery' ), SRP_VERSION );
			wp_localize_script(
				'fp_referral_module',
				'fp_referral_module_params',
				array(
					'ajaxurl'                    => SRP_ADMIN_AJAX_URL,
					'fp_wc_version'              => WC_VERSION,
					'manual_referral_link_nonce' => wp_create_nonce( 'srp-manual-referral-link-nonce' ),
					'rule_count'                 => rs_get_manual_referral_link_rule_count(),
				)
			);
		}

		public static function enqueue_for_social_modules_tab() {
			$redirect_url    = esc_url_raw(
				add_query_arg(
					array(
						'page'                             => 'rewardsystem_callback',
						'fp_bulk_update_for_social_reward' => 'yes',
					),
					SRP_ADMIN_URL
				)
			);
			$localize_script = array(
				'ajaxurl'                   => SRP_ADMIN_AJAX_URL,
				'redirecturl'               => $redirect_url,
				'social_reward_bulk_update' => wp_create_nonce( 'social-reward-bulk-update' ),
				'fp_wc_version'             => WC_VERSION,
			);
			wp_enqueue_script( 'fp_social_module', SRP_PLUGIN_DIR_URL . 'assets/js/tab/modules/fp-social-module.js', array( 'jquery' ), SRP_VERSION );
			wp_localize_script( 'fp_social_module', 'fp_social_params', $localize_script );
		}

		public static function enqueue_for_action_modules_tab() {
			$localize_script = array(
				'ajaxurl'                     => SRP_ADMIN_AJAX_URL,
				'cus_reg_fields_nonce'        => wp_create_nonce( 'srp-cus-reg-fields-nonce' ),
				'add_coupon_usage_rule_nonce' => wp_create_nonce( 'srp-add-coupon-usage-rule-nonce' ),
			);
			wp_enqueue_script( 'fp_action_module', SRP_PLUGIN_DIR_URL . 'assets/js/tab/modules/fp-action-module.js', array( 'jquery', 'jquery-ui-datepicker' ), SRP_VERSION );
			wp_localize_script( 'fp_action_module', 'fp_action_params', $localize_script );
		}

		public static function enqueue_for_redeem_modules_tab() {
			$localize_script = array(
				'ajaxurl'                               => SRP_ADMIN_AJAX_URL,
				'fp_wc_version'                         => WC_VERSION,
				'redeeming_percentage_nonce'            => wp_create_nonce( 'srp-redeeming-percentage-nonce' ),
				'redeeming_user_purchase_history_nonce' => wp_create_nonce( 'srp-redeeming-user-purchase-history-nonce' ),
				'redeeming_points_bulk_update'          => wp_create_nonce( 'redeeming-points-bulk-update' ),
			);
			wp_enqueue_script( 'fp_redeem_module', SRP_PLUGIN_DIR_URL . 'assets/js/tab/modules/fp-redeem-module.js', array( 'jquery' ), SRP_VERSION );
			wp_localize_script( 'fp_redeem_module', 'fp_redeem_module_params', $localize_script );
		}

		public static function enqueue_for_pointprice_modules_tab() {
			$redirect_url    = esc_url_raw(
				add_query_arg(
					array(
						'page' => 'rewardsystem_callback',
						'fp_bg_process_to_bulk_update_point_price' => 'yes',
					),
					SRP_ADMIN_URL
				)
			);
			$localize_script = array(
				'ajaxurl'                 => SRP_ADMIN_AJAX_URL,
				'redirecturl'             => $redirect_url,
				'fp_wc_version'           => WC_VERSION,
				'point_price_bulk_update' => wp_create_nonce( 'points-price-bulk-update' ),
			);
			wp_enqueue_script( 'fp_pointprice_module', SRP_PLUGIN_DIR_URL . 'assets/js/tab/modules/fp-pointprice-module.js', array( 'jquery' ), SRP_VERSION );
			wp_localize_script( 'fp_pointprice_module', 'fp_pointprice_module_param', $localize_script );
		}

		public static function enqueue_for_email_modules_tab() {
			$localize_script = array(
				'ajaxurl'              => SRP_ADMIN_AJAX_URL,
				'fp_wc_version'        => WC_VERSION,
				'fp_update_status'     => wp_create_nonce( 'fp-update-status' ),
				'fp_delete_template'   => wp_create_nonce( 'fp-delete-template' ),
				'fp_new_template'      => wp_create_nonce( 'fp-new-template' ),
				'fp_edit_template'     => wp_create_nonce( 'fp-edit-template' ),
				'template_id'          => isset( $_GET['rs_edit_email'] ) ? wc_clean( wp_unslash( $_GET['rs_edit_email'] ) ) : 0,
				'admin_email'          => get_option( 'admin_email' ),
				'fp_send_mail'         => wp_create_nonce( 'fp-send-mail' ),
				'save_new_template'    => isset( $_GET['rs_new_email'] ),
				'save_edited_template' => isset( $_GET['rs_edit_email'] ),
				'enable_footable'      => get_option( 'rs_enable_footable_js', 1 ),
			);
			wp_enqueue_script( 'fp_email_module', SRP_PLUGIN_DIR_URL . 'assets/js/tab/modules/fp-email-module.js', array( 'jquery' ), SRP_VERSION );
			wp_localize_script( 'fp_email_module', 'fp_email_params', $localize_script );
		}

		public static function enqueue_for_giftvoucher_modules_tab() {
			$redirect_url    = esc_url_raw(
				add_query_arg(
					array(
						'page' => 'rewardsystem_callback',
						'fp_bg_process_to_generate_voucher_code' => 'yes',
					),
					SRP_ADMIN_URL
				)
			);
			$localize_script = array(
				'ajaxurl'        => SRP_ADMIN_AJAX_URL,
				'redirecturl'    => $redirect_url,
				'fp_wc_version'  => WC_VERSION,
				'date'           => gmdate( 'Y - m - d' ),
				'prefix'         => __( 'Prefix value Should not be Empty', 'rewardsystem' ),
				'suffix'         => __( 'Suffix value Should not be Empty', 'rewardsystem' ),
				'character'      => __( 'Number of Characters for Voucher Code Should not be Empty', 'rewardsystem' ),
				'points'         => __( 'Reward Points Value per Voucher Code Generated Should not be Empty', 'rewardsystem' ),
				'noofcodes'      => __( 'Number of Voucher Codes to be Generated Should not be Empty', 'rewardsystem' ),
				'fp_create_code' => wp_create_nonce( 'fp-create-code' ),
			);
			wp_enqueue_script( 'fp_giftvoucher_module', SRP_PLUGIN_DIR_URL . 'assets/js/tab/modules/fp-giftvoucher-module.js', array( 'jquery', 'jquery-ui-datepicker' ), SRP_VERSION );
			wp_localize_script( 'fp_giftvoucher_module', 'fp_giftvoucher_module_param', $localize_script );
			wp_enqueue_script( 'wp_reward_jquery_ui', SRP_PLUGIN_DIR_URL . 'assets/js/jquery-ui.js', array(), SRP_VERSION );
		}

		public static function enqueue_for_sms_modules_tab() {
			wp_enqueue_script( 'fp_sms_module', SRP_PLUGIN_DIR_URL . 'assets/js/tab/modules/fp-sms-module.js', array( 'jquery' ), SRP_VERSION );
		}

		public static function enqueue_for_coupon_modules_tab() {
			wp_enqueue_script( 'fp_coupon_module', SRP_PLUGIN_DIR_URL . 'assets/js/tab/modules/fp-coupon-module.js', array( 'jquery' ), SRP_VERSION );
		}

		public static function enqueue_for_impexp_modules_tab() {
			$redirect_url = esc_url_raw(
				add_query_arg(
					array(
						'page'                           => 'rewardsystem_callback',
						'fp_bg_process_to_export_points' => 'yes',
					),
					SRP_ADMIN_URL
				)
			);
			wp_enqueue_script( 'fp_impexp_module', SRP_PLUGIN_DIR_URL . 'assets/js/tab/modules/fp-importexport-module.js', array( 'jquery', 'jquery-ui-datepicker' ), SRP_VERSION );
			wp_localize_script(
				'fp_impexp_module',
				'fp_impexp_module_params',
				array(
					'ajaxurl'           => SRP_ADMIN_AJAX_URL,
					'fp_export_points'  => wp_create_nonce( 'fp-export-points' ),
					'fp_start_date'     => wp_create_nonce( 'fp-start-date' ),
					'fp_end_date'       => wp_create_nonce( 'fp-end-date' ),
					'fp_user_selection' => wp_create_nonce( 'fp-user-selection' ),
					'fp_date_type'      => wp_create_nonce( 'fp-date-type' ),
					'fp_wc_version'     => WC_VERSION,
					'redirect'          => $redirect_url,
				)
			);
		}

		public static function enqueue_for_bday_modules_tab() {
			wp_enqueue_script( 'fp_birthday_module', SRP_PLUGIN_DIR_URL . 'assets/js/tab/modules/fp-bday-module.js', array( 'jquery' ), SRP_VERSION );
		}

		public static function enqueue_for_promotional_modules_tab() {
			wp_enqueue_script( 'fp_promotional_module', SRP_PLUGIN_DIR_URL . 'assets/js/tab/modules/fp-promotional-module.js', array( 'jquery' ), SRP_VERSION );
			wp_localize_script(
				'fp_promotional_module',
				'fp_promotional_params',
				array(
					'ajaxurl'     => SRP_ADMIN_AJAX_URL,
					'rule_nonce'  => wp_create_nonce( 'srp-rule-nonce' ),
					'delete_rule' => esc_html__( 'Are you sure, do you want to delete this rule?', 'rewardsystem' ),
				)
			);
		}

		public static function enqueue_for_cashback_modules_tab() {
			wp_enqueue_script( 'fp_cashback_module', SRP_PLUGIN_DIR_URL . 'assets/js/tab/modules/fp-cashback-module.js', array( 'jquery' ), SRP_VERSION );
		}

		public static function enqueue_for_reportsincsv_modules_tab() {
			$redirect_url    = esc_url_raw(
				add_query_arg(
					array(
						'page'                           => 'rewardsystem_callback',
						'fp_bg_process_to_export_report' => 'yes',
					),
					SRP_ADMIN_URL
				)
			);
			$localize_script = array(
				'ajaxurl'           => SRP_ADMIN_AJAX_URL,
				'fp_wc_version'     => WC_VERSION,
				'redirecturl'       => $redirect_url,
				'fp_start_date'     => wp_create_nonce( 'fp-start-date' ),
				'fp_end_date'       => wp_create_nonce( 'fp-end-date' ),
				'fp_export_report'  => wp_create_nonce( 'fp-export-report' ),
				'fp_user_type'      => wp_create_nonce( 'fp-user-type' ),
				'fp_selected_user'  => wp_create_nonce( 'fp-selected-user' ),
				'fp_date_type'      => wp_create_nonce( 'fp-date-type' ),
				'fp_points_type'    => wp_create_nonce( 'fp-points-type' ),
				'fp_user_selection' => wp_create_nonce( 'fp-user-selection' ),
			);
			wp_enqueue_script( 'fp_reports_in_csv_module', SRP_PLUGIN_DIR_URL . 'assets/js/tab/modules/fp-reportsincsv-module.js', array( 'jquery', 'jquery-ui-datepicker' ), SRP_VERSION );
			wp_localize_script( 'fp_reports_in_csv_module', 'fp_reports_in_csv_module_params', $localize_script );
		}

		public static function enqueue_for_reset_modules_tab() {
			$localize_script = array(
				'ajaxurl'                      => SRP_ADMIN_AJAX_URL,
				'fp_wc_version'                => WC_VERSION,
				'rs_reset_tab'                 => wp_create_nonce( 'rs-reset-tab' ),
				'rs_reset_data_for_user'       => wp_create_nonce( 'reset-data-for-user' ),
				'rs_reset_previous_order_meta' => wp_create_nonce( 'reset-previous-order-meta' ),
			);
			wp_enqueue_script( 'fp_reset_module', SRP_PLUGIN_DIR_URL . 'assets/js/tab/modules/fp-reset-module.js', array( 'jquery' ), SRP_VERSION );
			wp_localize_script( 'fp_reset_module', 'fp_reset_module_params', $localize_script );
		}

		public static function enqueue_for_reward_gateway_modules_tab() {
			$localize_script = array(
				'ajaxurl'       => SRP_ADMIN_AJAX_URL,
				'fp_wc_version' => WC_VERSION,
			);
			wp_enqueue_script( 'fp_reward_gateway_module', SRP_PLUGIN_DIR_URL . 'assets/js/tab/modules/fp-rewardgateway-module.js', array( 'jquery' ), SRP_VERSION );
			wp_localize_script( 'fp_reward_gateway_module', 'fp_reward_gateway_module_params', $localize_script );
		}

		public static function enqueue_for_nominee_modules_tab() {
			$localize_script = array(
				'ajaxurl'          => SRP_ADMIN_AJAX_URL,
				'fp_wc_version'    => WC_VERSION,
				'fp_nominee_nonce' => wp_create_nonce( 'fp-nominee-nonce' ),
			);
			wp_enqueue_script( 'fp_nominee_module', SRP_PLUGIN_DIR_URL . 'assets/js/tab/modules/fp-nominee-module.js', array( 'jquery' ), SRP_VERSION );
			wp_localize_script( 'fp_nominee_module', 'fp_nominee_module_params', $localize_script );
		}

		public static function enqueue_for_sendpoints_modules_tab() {
			$localize_script = array(
				'ajaxurl'       => SRP_ADMIN_AJAX_URL,
				'fp_wc_version' => WC_VERSION,
			);
			wp_enqueue_script( 'fp_sendpoints_module', SRP_PLUGIN_DIR_URL . 'assets/js/tab/modules/fp-sendpoints-module.js', array( 'jquery' ), SRP_VERSION );
			wp_localize_script( 'fp_sendpoints_module', 'fp_sendpoints_module_params', $localize_script );
		}

		public static function enqueue_for_pointurl_modules_tab() {
			$localize_script = array(
				'ajaxurl'         => SRP_ADMIN_AJAX_URL,
				'fp_wc_version'   => WC_VERSION,
				'fp_generate_url' => wp_create_nonce( 'fp-generate-url' ),
				'fp_remove_url'   => wp_create_nonce( 'fp-remove-url' ),
				'date'            => gmdate( 'Y-m-d' ),
				'enable_footable' => get_option( 'rs_enable_footable_js', 1 ),
			);
			wp_enqueue_script( 'fp_pointurl_module', SRP_PLUGIN_DIR_URL . 'assets/js/tab/modules/fp-pointurl-module.js', array( 'jquery', 'jquery-ui-datepicker' ), SRP_VERSION );
			wp_localize_script( 'fp_pointurl_module', 'fp_pointurl_module_params', $localize_script );
		}

		public static function enqueue_for_emailexpired_modules_tab() {
			global $wpdb;
			$ActiveTemplates = $wpdb->get_results( "SELECT template_name FROM {$wpdb->prefix}rs_expiredpoints_email WHERE rs_status='ACTIVE'", ARRAY_A );
			$localize_script = array(
				'ajaxurl'              => SRP_ADMIN_AJAX_URL,
				'fp_update_status'     => wp_create_nonce( 'fp-update-status' ),
				'fp_delete_template'   => wp_create_nonce( 'fp-delete-template' ),
				'fp_new_template'      => wp_create_nonce( 'fp-new-template' ),
				'fp_edit_template'     => wp_create_nonce( 'fp-edit-template' ),
				'template_id'          => isset( $_GET['rs_edit_email_expired'] ) ? wc_clean( wp_unslash( $_GET['rs_edit_email_expired'] ) ) : 0,
				'admin_email'          => get_option( 'admin_email' ),
				'save_new_template'    => isset( $_GET['rs_new_email_expired'] ),
				'save_edited_template' => isset( $_GET['rs_edit_email_expired'] ),
				'enable_footable'      => get_option( 'rs_enable_footable_js', 1 ),
				'active_template'      => empty( $ActiveTemplates ) ? true : false,
			);
			wp_enqueue_script( 'fp_emailexpired_module', SRP_PLUGIN_DIR_URL . 'assets/js/tab/modules/fp-emailexpired-module.js', array( 'jquery' ), SRP_VERSION );
			wp_localize_script( 'fp_emailexpired_module', 'fp_emailexpired_params', $localize_script );
		}

		public static function enqueue_for_discount_compatibility_modules_tab() {
			wp_enqueue_script( 'fp_discount_compatibility_module', SRP_PLUGIN_DIR_URL . 'assets/js/tab/modules/fp-discount-compatibility.js', array( 'jquery' ), SRP_VERSION );
		}

		public static function enqueue_for_bonus_points_modules_tab() {

				wp_enqueue_script( 'fp_bonus_points_module', SRP_PLUGIN_DIR_URL . 'assets/js/tab/modules/fp-bonus-points-module.js', array( 'jquery', 'jquery-ui-datepicker', 'wc-backbone-modal' ), SRP_VERSION );

				$localize_script = array(
					'ajaxurl'                            => SRP_ADMIN_AJAX_URL,
					'bonus_points_rule_for_orders_nonce' => wp_create_nonce( 'fp-bonus-points-rule-for-orders-nonce' ),
					'view_bonus_point_placed_order_ids_popup_nonce' => wp_create_nonce( 'fp-view-bonus-point-placed-order-ids-popup-nonce' ),
					'checkbox_alert'                     => __( 'Points will be awarded to old orders & orders placed after this module has been configured. Hence, we suggest you please check once on your testing environment with a few sample orders so that you can get a good hold of how this module works.', 'rewardsystem' ),
				);

				wp_localize_script( 'fp_bonus_points_module', 'fp_bonus_points_module_params', $localize_script );
		}

		public static function enqueue_for_anniversary_points_modules_tab() {

			wp_enqueue_script( 'fp_anniversary_points_module', SRP_PLUGIN_DIR_URL . 'assets/js/tab/modules/fp-anniversary-points-module.js', array( 'jquery', 'jquery-ui-datepicker', 'wc-backbone-modal' ), SRP_VERSION );

			$localize_script = array(
				'ajaxurl'                            => SRP_ADMIN_AJAX_URL,
				'add_account_anniversary_rule_nonce' => wp_create_nonce( 'fp-add-account-anniversary-rule-nonce' ),
				'add_custom_anniversary_rule_nonce'  => wp_create_nonce( 'fp-add-custom-anniversary-rule-nonce' ),
				'view_account_anniversary_points_popup_nonce' => wp_create_nonce( 'fp-view-account-anniversary-points-popup-nonce' ),
				'view_single_anniversary_points_popup_nonce' => wp_create_nonce( 'fp-view-single-anniversary-points-popup-nonce' ),
				'view_multiple_anniversary_points_popup_nonce' => wp_create_nonce( 'fp-view-multiple-anniversary-points-popup-nonce' ),
			);

					wp_localize_script( 'fp_anniversary_points_module', 'fp_anniversary_points_module_params', $localize_script );
		}
	}

	SRP_Admin_Enqueue::init();
}
