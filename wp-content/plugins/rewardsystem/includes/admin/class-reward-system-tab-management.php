<?php
/*
 * Reward System Tab Management
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}

if ( ! class_exists( 'RSTabManagement' ) ) {

	class RSTabManagement {

		public static function init() {
			add_action( 'admin_menu' , array( __CLASS__ , 'add_submenu_woocommerce' ) ) ;

			if ( isset( $_GET[ 'page' ] ) && 'rewardsystem_callback' == sanitize_text_field($_GET[ 'page' ]) ) {

				// Filter works for WP Version <= 5.4.1.
				add_filter( 'set-screen-option' , array( __CLASS__ , 'rs_set_screen_option_value' ) , 10 , 3 ) ;

				// Filter works for WP Version >= 5.4.2.
				$option_names = rs_get_screen_option_names() ;
				foreach ( $option_names as $option_name ) {
					add_filter( 'set_screen_option_' . $option_name , array( __CLASS__ , 'rs_set_screen_option_value' ) , 10 , 3 ) ;
				}
			}

			add_filter( 'plugin_action_links_' . SRP_PLUGIN_BASENAME , array( __CLASS__ , 'rs_plugin_action' ) ) ;
			add_filter( 'plugin_row_meta' , array( __CLASS__ , 'rs_plugin_row_meta' ) , 10 , 2 ) ;
			add_action( 'woocommerce_sections_fprsmodules' , array( __CLASS__ , 'rs_function_to_get_subtab' ) ) ;
			add_filter( 'woocommerce_rs_settings_tabs_array' , array( __CLASS__ , 'rs_settings_tabs_name' ) , 10 , 1) ;

			add_action( 'woocommerce_admin_field_rs_wrapper_start' , array( __CLASS__ , 'rs_wrapper_section_start' ) ) ;
			add_action( 'woocommerce_admin_field_rs_wrapper_end' , array( __CLASS__ , 'rs_wrapper_section_end' ) ) ;
			add_action( 'woocommerce_admin_field_rs_modulecheck_start' , array( __CLASS__ , 'rs_wrapper_modulecheck_start' ) ) ;
			add_action( 'woocommerce_admin_field_rs_modulecheck_end' , array( __CLASS__ , 'rs_wrapper_modulecheck_close' ) ) ;
			add_action( 'woocommerce_admin_field_rs_membership_compatible_start' , array( __CLASS__ , 'rs_wrapper_membership_compatible_start' ) ) ;
			add_action( 'woocommerce_admin_field_rs_membership_compatible_end' , array( __CLASS__ , 'rs_wrapper_membership_compatible_close' ) ) ;
			add_action( 'woocommerce_admin_field_rs_subscription_compatible_start' , array( __CLASS__ , 'rs_wrapper_subscription_compatible_start' ) ) ;
			add_action( 'woocommerce_admin_field_rs_subscription_compatible_end' , array( __CLASS__ , 'rs_wrapper_subscription_compatible_close' ) ) ;
			add_action( 'woocommerce_admin_field_rs_coupon_compatible_start' , array( __CLASS__ , 'rs_wrapper_coupon_compatible_start' ) ) ;
			add_action( 'woocommerce_admin_field_rs_coupon_compatible_end' , array( __CLASS__ , 'rs_wrapper_coupon_compatible_close' ) ) ;
			add_action( 'woocommerce_admin_field_rs_hide_bulk_update_for_product_purchase_start' , array( __CLASS__ , 'rs_hide_bulk_update_for_product_purchase_start' ) ) ;
			add_action( 'woocommerce_admin_field_rs_hide_bulk_update_for_product_purchase_end' , array( __CLASS__ , 'rs_hide_bulk_update_for_product_purchase_end' ) ) ;
			add_action( 'woocommerce_admin_field_rs_bsn_compatible_start' , array( __CLASS__ , 'rs_wrapper_bsn_compatible_start' ) ) ;
			add_action( 'woocommerce_admin_field_rs_bsn_compatible_end' , array( __CLASS__ , 'rs_wrapper_bsn_compatible_close' ) ) ;
			add_action( 'woocommerce_admin_field_rs_fpwcrs_compatible_start' , array( __CLASS__ , 'rs_wrapper_fpwcrs_compatible_start' ) ) ;
			add_action( 'woocommerce_admin_field_rs_fpwcrs_compatible_end' , array( __CLASS__ , 'rs_wrapper_fpwcrs_compatible_close' ) ) ;
			add_action( 'woocommerce_admin_field_rs_affs_compatible_start' , array( __CLASS__ , 'rs_wrapper_affs_compatible_start' ) ) ;
			add_action( 'woocommerce_admin_field_rs_affs_compatible_end' , array( __CLASS__ , 'rs_wrapper_affs_compatible_close' ) ) ;
			add_action( 'woocommerce_admin_field_rs_payment_plan_compatible_start' , array( __CLASS__ , 'rs_wrapper_payment_plan_compatible_start' ) ) ;
			add_action( 'woocommerce_admin_field_rs_payment_plan_compatible_close' , array( __CLASS__ , 'rs_wrapper_payment_plan_compatible_close' ) ) ;
		}

		/*
		 * Initializing the Tabs.
		 */

		public static function rs_settings_tabs_name( $tabs ) {
			$tabs = is_array($tabs) ? $tabs : array() ;

			$tabs[ 'fprsgeneral' ]          = __( 'General' , 'rewardsystem' ) ;
			$tabs[ 'fprsmodules' ]          = __( 'Modules' , 'rewardsystem' ) ;
			$tabs[ 'fprsaddremovepoints' ]  = __( 'Add/Remove Reward Points' , 'rewardsystem' ) ;
			$tabs[ 'fprsmessage' ]          = __( 'Messages' , 'rewardsystem' ) ;
			$tabs[ 'fprslocalization' ]     = __( 'Localization' , 'rewardsystem' ) ;
			$tabs[ 'fprsuserrewardpoints' ] = __( 'User Reward Points' , 'rewardsystem' ) ;
			$tabs[ 'fprsmasterlog' ]        = __( 'Master Log' , 'rewardsystem' ) ;
			$tabs[ 'fprsshortcodes' ]       = __( 'Shortcodes' , 'rewardsystem' ) ;
			$tabs[ 'fprsadvanced' ]         = __( 'Advanced' , 'rewardsystem' ) ;
			$tabs[ 'fprssupport' ]          = __( 'Support' , 'rewardsystem' ) ;
			return array_filter( $tabs ) ;
		}

		public static function add_submenu_woocommerce() {
			global $my_admin_page ;
			$name = ( '' == get_option( 'rs_brand_name' ) ) ? __( 'SUMO Reward Points' , 'rewardsystem' ) : get_option( 'rs_brand_name' ) ;

			$my_admin_page = add_submenu_page( 'woocommerce' , $name , $name , 'manage_woocommerce' , 'rewardsystem_callback' , array( 'RSTabManagement' , 'rewardsystem_tab_management' ) ) ;
			add_action( 'load-' . $my_admin_page , array( 'RSTabManagement' , 'rs_function_to_display_screen_option' ) ) ;
		}

		public static function rewardsystem_tab_management() {
			$tabs = array() ;
			global $woocommerce , $woocommerce_settings , $current_section , $current_tab ;
			do_action( 'woocommerce_rs_settings_start' ) ;
			if ( 'yes' == get_option( 'rs_menu_restriction_based_on_user_role' ) ) {
				$tabtoshow = RSAdminAssets::menu_restriction_based_on_user_role() ;
				if ( ! isset( $_GET[ 'tab' ] ) && isset( $_GET[ 'page' ] ) && ( 'rewardsystem_callback' == sanitize_text_field($_GET[ 'page' ]) ) ) {
					$_GET[ 'tab' ] = rs_get_next_menu() ;
				}
			} else {
				$tabtoshow = array( 'fprsgeneral' , 'fprsmodules' , 'fprsaddremovepoints' , 'fprsmessage' , 'fprslocalization' , 'fprsuserrewardpoints' , 'fprsmasterlog' , 'fprsshortcodes' , 'fprssupport' , 'fprsadvanced' ) ;
			}
			$tab             = reset( $tabtoshow ) ;
						$tabs             = isset( $_GET[ 'tab' ] ) ? wc_clean(wp_unslash( ( $_GET[ 'tab' ] ) )):'';
			$current_tab     = ( empty( $tabs ) ) ? $tab : $tabs ;
			$current_section = ( empty( $_REQUEST[ 'section' ] ) ) ? '' : wc_clean(wp_unslash(( $_REQUEST[ 'section' ] ) ) ) ;

			include_once SRP_PLUGIN_PATH . '/includes/admin/tabs/class-rs-' . $current_tab . '-tab.php' ;

			if ( '' != $current_section  ) {
				include_once SRP_PLUGIN_PATH . '/includes/admin/tabs/modules/class-rs-' . $current_section . '-module-tab.php' ;
			}

			if ( ! empty( $_POST[ 'save' ] ) ) {
				if ( empty( $_REQUEST[ '_wpnonce' ] ) || ! wp_verify_nonce( sanitize_text_field($_REQUEST[ '_wpnonce' ] ), 'woocommerce-settings' ) ) {
					die( esc_html__( 'Action failed. Please refresh the page and retry.' , 'rewardsystem' ) ) ;
				}

				if ( ! $current_section ) {
					switch ( $current_tab ) {
						default:
							if ( isset( $woocommerce_settings[ $current_tab ] ) ) {
								woocommerce_update_options( $woocommerce_settings[ $current_tab ] ) ;
							}
							// Trigger action for tab
							do_action( 'woocommerce_update_options_' . $current_tab ) ;
							break ;
					}
					do_action( 'woocommerce_update_options' ) ;
				} else {
					// Save section onlys
					do_action( 'woocommerce_update_options_' . $current_tab . '_' . $current_section ) ;
				}

				// Clear any unwanted data
				delete_transient( 'woocommerce_cache_excluded_uris' ) ;

				// Redirect back to the settings page
				$redirect = esc_url_raw( add_query_arg( array( 'saved' => 'true' ) ) ) ;
				if ( isset( $_POST[ 'subtab' ] ) ) {
					wp_safe_redirect( $redirect ) ;
					exit ;
				}
			}
			/* Initialize Background Process - Start */
			self::rs_initialize_bg_process( 'rs_background_process' , 'update_products' ) ;
			self::rs_initialize_bg_process( 'fp_bg_process_to_apply_points' , 'apply_points' ) ;
			self::rs_initialize_bg_process( 'fp_bg_process_to_add_points' , 'add_points' ) ;
			self::rs_initialize_bg_process( 'fp_bg_process_to_remove_points' , 'remove_points' ) ;
			self::rs_initialize_bg_process( 'fp_bg_process_to_refresh_points' , 'refresh_points' ) ;
			self::rs_initialize_bg_process( 'fp_bg_process_to_export_points' , 'export_points' ) ;
			self::rs_initialize_bg_process( 'fp_bg_process_to_export_report' , 'export_report' ) ;
			self::rs_initialize_bg_process( 'fp_bg_process_to_old_points' , 'old_points' ) ;
			self::rs_initialize_bg_process( 'fp_bg_process_to_bulk_update' , 'bulk_update' ) ;
			self::rs_initialize_bg_process( 'fp_bulk_update_for_social_reward' , 'bulk_update_for_social' ) ;
			self::rs_initialize_bg_process( 'fp_bg_process_to_buying_points_bulk_update' , 'bulk_update_buying_points' ) ;
			self::rs_initialize_bg_process( 'fp_bg_process_to_bulk_update_point_price' , 'bulk_update_point_price' ) ;
			self::rs_initialize_bg_process( 'fp_bg_process_to_generate_voucher_code' , 'generate_voucher_code' ) ;
			self::rs_initialize_bg_process( 'fp_bg_process_to_export_log' , 'export_log' ) ;
			self::rs_initialize_bg_process( 'fp_bg_process_to_update_earned_points' , 'update_earned_points' ) ;
			/* Initialize Background Process - End */

			// Reset Settings
			if ( ! empty( $_POST[ 'reset' ] ) ) {
				do_action( 'fp_action_to_reset_module_settings_' . $current_section ) ;
				do_action( 'fp_action_to_reset_settings_' . $current_tab ) ;
				if ( '' == $current_section ) {
					$reset_true_url = esc_url_raw( add_query_arg( array( 'page' => 'rewardsystem_callback' , 'tab' => $current_tab , 'resetted' => 'true' ) , SRP_ADMIN_URL ) ) ;
				} else {
					$reset_true_url = esc_url_raw( add_query_arg( array( 'page' => 'rewardsystem_callback' , 'tab' => $current_tab , 'section' => $current_section , 'resetted' => 'true' ) , SRP_ADMIN_URL ) ) ;
				}
				wp_redirect( $reset_true_url ) ;
				exit ;
			}

			//display any warning, success or error message.
			echo wp_kses_post(self::rs_display_tab_message() );
			?>
			<div class="wrap woocommerce rs_main_wrapper">
				<form method="post" id="mainform" action="" enctype="multipart/form-data" class="rs_main">
					<div class="icon32 icon32-woocommerce-settings" id="icon-woocommerce"><br /></div>
					<h2 class="nav-tab-wrapper woo-nav-tab-wrapper rs_tab_design">
						
						<?php if (apply_filters('rs_display_welcome_header', true)) : ?>
						<div class="fp-srp-page-header" >
							<div class="fp-srp-page-title" >
								<h1> <strong><?php esc_html_e('SUMO Reward Points', 'rewardsystem'); ?></strong></h1>
							</div>
							<div class="fp-srp-branding-logo" >
								<a href="http://fantasticplugins.com/" target="_blank" ><img src="<?php echo esc_url(SRP_PLUGIN_DIR_URL) ; ?>/assets/images/Fantastic-Plugins-final-Logo.png" /></a>
							</div>
						</div>
						<?php endif; ?>
						
						<ul>
							<?php
							$tabs = apply_filters( 'woocommerce_rs_settings_tabs_array' , $tabs ) ;
							if ( srp_check_is_array( $tabs ) ) {
								foreach ( $tabs as $name => $label ) {
									if ( in_array( $name , $tabtoshow ) ) {
										echo wp_kses_post('<a href="' . admin_url( 'admin.php?page=rewardsystem_callback&tab=' . $name ) . '" class="nav-tab ' . ( $current_tab == $name ? 'nav-tab-active' : '' ) . '">' . $label . '</a>' );
									}
								}
							}
							do_action( 'woocommerce_rs_settings_tabs' ) ;
							?>
							</ul>
							<?php
							do_action( 'woocommerce_sections_' . $current_tab ) ;
							?>
					</h2>
					<?php
					switch ( $current_tab ) :
						default:
							$tabtoshow = array( 'fprsaddremovepoints' , 'fprsuserrewardpoints' , 'fprsmasterlog' , 'fprssupport' , 'fprsshortcodes' ) ;
							if ( ! in_array( $current_tab , $tabtoshow ) ) {
								if ( isset( $_GET[ 'section' ] ) ) {
									self::rs_function_to_display_expand_collapse_button() ;
								} else {
									if ( isset( $_GET[ 'tab' ] ) && 'fprsmodules' != sanitize_text_field($_GET[ 'tab' ]) ) {
										self::rs_function_to_display_expand_collapse_button() ;
									} else {
										if ( isset( $_GET[ 'page' ] ) && 'rewardsystem_callback' == sanitize_text_field($_GET[ 'page' ]) && !isset($_GET[ 'tab' ]) ) {
											self::rs_function_to_display_expand_collapse_button();
										}
									}
								}
							}
							do_action( 'woocommerce_rs_settings_tabs_' . $current_tab ) ;
							break ;
					endswitch ;
					do_action( 'rs_display_save_button_' . $current_tab ) ;
					do_action( 'rs_display_save_button_' . $current_section ) ;
					?>
				</form>
				<?php
				if ( '1' == get_option( 'rs_show_hide_reset_all' ) ) {
					do_action( 'rs_display_reset_button_' . $current_tab ) ;
					do_action( 'rs_display_reset_button_' . $current_section ) ;
				}
				?>
			</div> 
			<?php
		}

		public static function rs_display_tab_message() {
			$error   = ( !empty( $_GET[ 'wc_error' ] ) )? stripslashes( wc_clean(wp_unslash($_GET[ 'wc_error' ] ) ) ):'';
			$message = ( !empty( $_GET[ 'wc_message' ] ) )? stripslashes( wc_clean(wp_unslash($_GET[ 'wc_message' ] ) )) :'';

			if ( $error || $message ) {
				if ( $error ) {
					return '<div id="message" class="error fade"><p><strong>' . esc_html( $error ) . '</strong></p></div>' ;
				} else {
					return '<div id="message" class="updated fade"><p><strong>' . esc_html( $message ) . '</strong></p></div>' ;
				}
			} elseif ( ! empty( $_GET[ 'saved' ] ) ) {
				return '<div id="message" class="updated fade"><p><strong>' . __( 'Your settings have been saved.' , 'rewardsystem' ) . '</strong></p></div>' ;
			} elseif ( ! empty( $_GET[ 'resetted' ] ) ) {
				return '<div id="message" class="updated fade"><p><strong>' . __( 'Your settings have been Restored.' , 'recoverabandoncart' ) . '</strong></p></div>' ;
			}
		}

		public static function rs_initialize_bg_process( $key, $progressbarkey ) {
			if ( isset( $_GET[ $key ] ) && 'yes' == sanitize_text_field($_GET[ $key ]) ) {
				$obj = new SRP_Updating_Process() ;
				$obj->fp_display_progress_bar( $progressbarkey ) ;
				exit() ;
			}
		}

		public static function rs_function_to_display_expand_collapse_button() {
			?>
			<div class="rs_exp_col">
				<label><?php esc_html_e('Expand all/Collapse all', 'rewardsystem'); ?>
					<input type="checkbox" value="Expand/Collapse" id="rs_expand">
				</label>
			</div>
			<?php
		}

		public static function rs_function_to_get_subtab() {
			global $current_section ;
			$sections   = get_list_of_modules( 'name' ) ;

			?>
			<ul class="subsubsub rs_sub_tab_design">
							<?php
								$array_keys = array_keys( $sections ) ;
							foreach ( $sections as $id => $label ) {
									$subtabs = get_list_of_modules() ;
									$class_name = ( 'yes' == $subtabs[ $id ] ) ? 'fp-srp-show' : 'fp-srp-hide';
								?>
										<li class="rs_sub_tab_li <?php echo esc_attr( $class_name ); ?>" id="<?php echo esc_attr($id); ?>">
												<a href="<?php echo esc_url(admin_url( 'admin.php?page=rewardsystem_callback&tab=fprsmodules&section=' . sanitize_title( $id ) ) ); ?>"
												   class="<?php echo esc_attr( $current_section === $id ? 'current' : '' ); ?>">
													<?php echo wp_kses_post($label . ( end( $array_keys ) == $id ? '' : ' |' )); ?>
												</a>
										</li>
										<?php
							}
							?>
						</ul><br class="clear" />
						<?php 
		}

		public static function rs_function_to_display_screen_option() {
			if ( isset( $_GET[ 'tab' ] ) ) {
								$tab = sanitize_text_field($_GET[ 'tab' ]);
				$array = array(
					'fpgiftvoucher'        => 'fpgiftvoucher' == $tab ,
					'fprsmasterlog'        => 'fprsmasterlog' == $tab ,
					'fpnominee'            => 'fpnominee' == $tab  ,
					'fpreferralsystem'     => 'fpreferralsystem' == $tab  ,
					'fprsuserrewardpoints' => 'fprsuserrewardpoints'== $tab ,
					'fppointurl'           => 'fppointurl'== $tab  ,
					'fpsendpoints'         => 'fpsendpoints'== $tab ,
					'fprsmodules'          => 'fprsmodules'== $tab  ,
						) ;
				if ( is_array( $array ) && ! empty( $array ) ) {
					foreach ( $array as $option_name => $tab_name ) {
						if ( $tab_name ) {
							$screen = get_current_screen() ;
							$args   = array(
								'label'   => __( 'Number Of Items Per Page' , 'rewardsystem' ) ,
								'default' => 10 ,
								'option'  => $option_name
									) ;
							add_screen_option( 'per_page' , $args ) ;
						}
					}
				}
			}
		}

		public static function rs_set_screen_option_value( $status, $option, $value ) {
			if ( 'fpgiftvoucher' == $option ) {
				return $value ;
			}

			if ( 'fprsmasterlog' == $option ) {
				return $value ;
			}

			if ( 'fpnominee' == $option ) {
				return $value ;
			}

			if ( 'fpreferralsystem' == $option ) {
				return $value ;
			}

			if ( 'fprsuserrewardpoints' == $option ) {
				return $value ;
			}

			if ( 'fppointurl' == $option ) {
				return $value ;
			}

			if ( 'fpsendpoints' == $option ) {
				return $value ;
			}

			if ( 'fprsmodules' == $option ) {
				return $value ;
			}
		}

		public static function rs_get_value_for_no_of_item_perpage( $user, $screen ) {
			$screen_option = $screen->get_option( 'per_page' , 'option' ) ;
			$per_page      = get_user_meta( $user , $screen_option , true ) ;
			if ( empty( $per_page ) || $per_page < 1 ) {
				$per_page = $screen->get_option( 'per_page' , 'default' ) ;
			}
			return $per_page ;
		}

		//common function to check field ids
		public static function rs_function_stop_mail_when_reset( $field_id, $setting_array ) {
			if ( "$field_id" == $setting_array[ 'newids' ] && get_option( "$field_id" ) != $setting_array[ 'default' ] ) {
				return true ;
			}
			return false ;
		}

		public static function reset_settings( $settings, $module_flag = '' ) {
			$x = 0 ;
			foreach ( $settings as $setting ) {
				if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
					//check only for email module
					if ( 'rsemailmodule' == $module_flag ) {
						if ( self::rs_function_stop_mail_when_reset( 'rs_mail_cron_type' , $setting ) ) {
							$x ++ ;
						}
						if ( self::rs_function_stop_mail_when_reset( 'rs_mail_cron_time' , $setting ) ) {
							$x ++ ;
						}
					}
					delete_option( $setting[ 'newids' ] ) ;
					add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
				}
				//resetting a cron values when tab Reset.
				if ( 'rsemailmodule' == $module_flag && $x > 0 ) {
					FPRewardSystem::create_cron_job() ;
				}
			}
		}

		/**
		 * Show action links on the plugin screen.
		 *
		 * @param	mixed $links Plugin Action links
		 * @return	array
		 */
		public static function rs_plugin_action( $links ) {
			$action_links = array(
				'rsaboutpage' => '<a href="' . admin_url( 'admin.php?page=rewardsystem_callback' ) . '" aria-label="' . esc_attr__( 'Settings' , 'rewardsystem' ) . '">' . esc_attr__( 'Settings' , 'rewardsystem' ) . '</a>' ,
					) ;
			return array_merge( $action_links , $links ) ;
		}

		/**
		 * Show row meta on the plugin screen.
		 *
		 * @param	mixed $links Plugin Row Meta
		 * @param	mixed $file  Plugin Base file
		 * @return	array
		 */
		public static function rs_plugin_row_meta( $links, $file ) {
			if ( SRP_PLUGIN_BASENAME == $file ) {
				$redirect_url = add_query_arg( array( 'page' => 'sumo-reward-points-welcome-page' ) , SRP_ADMIN_URL ) ;
				$row_meta     = array(
					'rs_about'   => '<a href="' . $redirect_url . '" aria-label="' . esc_attr__( 'About' , 'rewardsystem' ) . '">' . esc_html__( 'About' , 'rewardsystem' ) . '</a>' ,
					'rs_support' => '<a href="http://fantasticplugins.com/support/" aria-label="' . esc_attr__( 'Support' , 'rewardsystem' ) . '">' . esc_html__( 'Support' , 'rewardsystem' ) . '</a>' ,
						) ;

				return array_merge( $links , $row_meta ) ;
			}
			return ( array ) $links ;
		}

		public static function rs_display_save_button() {
			?>
			<p class="submit sumo_reward_points">
				<?php if ( ! isset( $GLOBALS[ 'hide_save_button' ] ) ) : ?>
					<input name="save" class="button-primary rs_save_btn" type="submit" value="<?php esc_html_e( 'Save changes' , 'rewardsystem' ) ; ?>" />
				<?php endif ; ?>
				<input type="hidden" name="subtab" id="last_tab" />
				<?php wp_nonce_field( 'woocommerce-settings' , '_wpnonce' , true , true ) ; ?>
			</p>
			<?php
		}

		public static function rs_display_reset_button() {
			?>
			<form method="post" id="mainforms" action="" enctype="multipart/form-data">
				<input id="resettab" name="reset" class="button-secondary rs_reset" type="submit" value="<?php esc_html_e( 'Reset' , 'rewardsystem' ) ; ?>"/>
				<?php wp_nonce_field( 'woocommerce-reset_settings' , '_wpnonce' , true , true ) ; ?>             
			</form>
			<?php
		}

		public static function rs_hide_bulk_update_for_product_purchase_start() {
			?>
			<div class="rs_hide_bulk_update_for_product_purchase_start">
				<?php
		}

		public static function rs_hide_bulk_update_for_product_purchase_end() {
			?>
			</div>
			<?php
		}

		public static function rs_wrapper_section_start() {
			?>
			<div class="rs_section_wrapper">
				<?php
		}

		public static function rs_wrapper_section_end() {
			?>
			</div>
			<?php
		}

		public static function rs_wrapper_modulecheck_start() {
			?>
			<div class="rs_modulecheck_wrapper">
				<?php
		}

		public static function rs_wrapper_modulecheck_close() {
			?>
			</div>
			<?php
		}

		public static function rs_wrapper_membership_compatible_start() {
			?>
			<div class="rs_membership_compatible_wrapper">
				<?php
		}

		public static function rs_wrapper_membership_compatible_close() {
			?>
			</div>
			<?php
		}

		public static function rs_wrapper_subscription_compatible_start() {
			?>
			<div class="rs_subscription_compatible_wrapper">
				<?php
		}

		public static function rs_wrapper_subscription_compatible_close() {
			?>
			</div>
			<?php
		}

		public static function rs_wrapper_coupon_compatible_start() {
			?>
			<div class="rs_coupon_compatible_wrapper">
				<?php
		}

		public static function rs_wrapper_coupon_compatible_close() {
			?>
			</div>
			<?php
		}

		public static function rs_wrapper_bsn_compatible_start() {
			?>
			<div class="rs_bsn_compatible_wrapper">
				<?php
		}

		public static function rs_wrapper_bsn_compatible_close() {
			?>
			</div>
			<?php
		}

		public static function rs_wrapper_fpwcrs_compatible_start() {
			?>
			<div class="rs_fpwcrs_compatible_wrapper">
				<?php
		}

		public static function rs_wrapper_fpwcrs_compatible_close() {
			?>
			</div>
			<?php
		}

		public static function rs_wrapper_affs_compatible_start() {
			?>
			<div class="rs_affs_compatible_wrapper">
				<?php
		}

		public static function rs_wrapper_affs_compatible_close() {
			?>
			</div>
			<?php
		}

		public static function rs_wrapper_payment_plan_compatible_start() {
			?>
			<div class="rs_payment_plan_compatible_wrapper">
				<?php
		}

		public static function rs_wrapper_payment_plan_compatible_close() {
			?>
			</div>
			<?php
		}

	}

	RSTabManagement::init() ;
}
