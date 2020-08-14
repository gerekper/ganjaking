<?php
/**
 * Shortcode Premium class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Affiliates
 * @version 1.0.0
 */

/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'YITH_WCAF' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAF_Shortcode_Premium' ) ) {
	/**
	 * WooCommerce Affiliate Shortcode Premium
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAF_Shortcode_Premium extends YITH_WCAF_Shortcode {

		/**
		 * Performs all required add_shortcode
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public static function init() {
			parent::init();

			add_shortcode( 'yith_wcaf_set_referrer', array( 'YITH_WCAF_Shortcode_Premium', 'set_referrer' ) );
			add_shortcode( 'yith_wcaf_show_withdraw', array( 'YITH_WCAF_Shortcode_Premium', 'show_withdraw' ) );
			add_shortcode( 'yith_wcaf_show_coupons', array( 'YITH_WCAF_Shortcode_Premium', 'show_coupons' ) );
			add_shortcode( 'yith_wcaf_current_affiliate', array( 'YITH_WCAF_Shortcode_Premium', 'current_affiliate' ) );

			// premium settings
			add_filter( 'yith_wcaf_registration_form_defaults', array( 'YITH_WCAF_Shortcode_Premium', 'add_registration_form_defaults' ) );
			add_filter( 'yith_wcaf_affiliate_dashboard_settings_atts', array( 'YITH_WCAF_Shortcode_Premium', 'add_premium_dashboard_settings_options' ), 10, 2 );
			add_filter( 'yith_wcaf_save_affiliate_settings', array( 'YITH_WCAF_Shortcode_Premium', 'save_premium_dashboard_settings_options' ), 10, 2 );
		}

		/**
		 * Returns output for affiliate code input form
		 *
		 * @param $atts mixed Array of shortcodes attributes
		 *
		 * @return string Shortcode content
		 * @since 1.0.0
		 */
		public static function set_referrer( $atts = array() ) {
			/**
			 * @var $affiliate_token string Affiliate Token
			 */
			$defaults = array(
				'affiliate_token' => false
			);

			$atts = shortcode_atts( $defaults, $atts );
			extract( $atts );

			if ( ! $affiliate_token && $stored_token = YITH_WCAF_Affiliate_Premium()->get_token() ) {
				$affiliate_token = $stored_token;
			}

			$permanent_token = ( get_option( 'yith_wcaf_commission_persistent_calculation' ) == 'yes' ) && ( get_option( 'yith_wcaf_avoid_referral_change' ) == 'yes' );

			$atts = array_merge(
				$atts,
				array(
					'enabled'         => 'checkout' === get_option( 'yith_wcaf_general_referral_cod', 'query_string' ),
					'affiliate'       => $affiliate_token,
					'permanent_token' => $permanent_token
				)
			);

			$template_name = 'form-referrer.php';

			ob_start();

			yith_wcaf_get_template( $template_name, $atts, 'shortcodes' );

			return ob_get_clean();
		}

		/**
		 * Print withdraw section of the dashboard
		 *
		 * @param $atts mixed Array of shortcode attributes
		 *
		 * @return string Shortcode content
		 * @since 1.0.0
		 */
		public static function affiliate_dashboard_withdraw( $atts = array() ) {
			/**
			 * @var $show_dashboard_links string (yes/no)
			 */
			$defaults = array(
				'show_dashboard_links' => 'no'
			);

			$atts = shortcode_atts( $defaults, $atts );
			extract( $atts );

			if ( ! YITH_WCAF_Affiliate_Handler()->can_user_see_section( false, 'withdraw' ) ) {
				return '';
			}

			$user_id      = get_current_user_id();
			$affiliate    = YITH_WCAF_Affiliate_Handler()->get_affiliate_by_user_id( $user_id );
			$min_withdraw = get_option( 'yith_wcaf_payment_threshold', 0 );
			$max_withdraw = YITH_WCAF_Affiliate_Handler()->get_affiliate_balance( $affiliate['ID'] );

			$payment_email = isset( $_POST['payment_email'] ) ? apply_filters( 'yith_wcaf_sanitized_payment_email', sanitize_email( $_POST['payment_email'] ), $_POST['payment_email'] ) : $affiliate['payment_email'];

			$require_invoice   = get_option( 'yith_wcaf_payment_require_invoice', 'yes' );
			$invoice_mode      = get_option( 'yith_wcaf_payment_invoice_mode', 'both' );
			$invoice_example   = get_option( 'yith_wcaf_payment_invoice_example', '' );
			$invoice_fields    = get_option( 'yith_wcaf_payment_invoice_fields', array() );
			$show_terms_field  = get_option( 'yith_wcaf_payment_invoice_show_terms_field' );
			$terms_label       = get_option( 'yith_wcaf_payment_invoice_terms_label' );
			$terms_anchor_url  = get_option( 'yith_wcaf_payment_invoice_terms_anchor_url' );
			$terms_anchor_text = get_option( 'yith_wcaf_payment_invoice_terms_anchor_text' );

			YITH_WCAF_Payment_Handler_Premium()->process_withdraw_request();

			$can_withdraw   = YITH_WCAF_Payment_Handler_Premium()->can_affiliate_withdraw( $affiliate['ID'] );
			$from           = isset( $_POST['withdraw_from'] ) ? sanitize_text_field( $_POST['withdraw_from'] ) : false;
			$to             = isset( $_POST['withdraw_to'] ) ? sanitize_text_field( $_POST['withdraw_to'] ) : false;
			$current_amount = 0;

			if ( $from && $to ) {
				$commissions = YITH_WCAF_Commission_Handler()->get_commissions( array(
					'interval'     => array(
						'start_date' => date( 'Y-m-d 00:00:00', strtotime( $from ) ),
						'end_date'   => date( 'Y-m-d 23:59:59', strtotime( $to ) )
					),
					'affiliate_id' => $affiliate['ID']
				) );

				if ( ! empty( $commissions ) ) {
					$current_amount = array_sum( array_map( 'floatval', wp_list_pluck( $commissions, 'amount' ) ) );
				}
			}

			$atts = array_merge(
				$atts,
				array(
					'current_amount'    => $current_amount,
					'can_withdraw'      => $can_withdraw,
					'payments_endpoint' => YITH_WCAF()->get_affiliate_dashboard_url( 'payments' ),
					'withdraw_from'     => $from,
					'withdraw_to'       => $to,
					'min_withdraw'      => $min_withdraw,
					'max_withdraw'      => $max_withdraw,
					'dashboard_links'   => yith_wcaf_get_dashboard_navigation_menu(),
					'show_right_column' => apply_filters( 'yith_wcaf_show_dashboard_links_withdraw', 'yes' == $show_dashboard_links ),
					'require_invoice'   => $require_invoice == 'yes',
					'invoice_mode'      => $invoice_mode,
					'invoice_example'   => $invoice_example,
					'payment_email'     => $payment_email,
					'invoice_profile'   => YITH_WCAF_Affiliate_Handler_Premium()->get_affiliate_invoice_profile( $affiliate['user_id'] ),
					'invoice_fields'    => apply_filters( 'yith_wcaf_invoice_fields', $invoice_fields ),
					'show_terms_field'  => $show_terms_field,
					'terms_label'       => $terms_label,
					'terms_anchor_url'  => $terms_anchor_url,
					'terms_anchor_text' => $terms_anchor_text
				)
			);

			$template_name = 'dashboard-withdraw.php';

			ob_start();

			yith_wcaf_get_template( $template_name, $atts, 'shortcodes' );

			return ob_get_clean();
		}

		/**
		 * Print coupons section of the dashboard
		 *
		 * @param $atts mixed Array of shortcode attributes
		 *
		 * @return string Shortcode content
		 * @since 1.6.0
		 */
		public static function affiliate_dashboard_coupons( $atts = array() ) {
			/**
			 * @var $pagination           (yes/no)
			 * @var $per_page             (int)
			 * @var $current_page         (int)
			 * @var $show_dashboard_links string (yes/no)
			 */
			$defaults = array(
				'pagination'           => 'yes',
				'per_page'             => isset( $_REQUEST['per_page'] ) ? intval( wc_clean( $_REQUEST['per_page'] ) ) : 10,
				'current_page'         => max( 1, get_query_var( 'coupons' ) ),
				'show_dashboard_links' => 'no'
			);

			$atts = shortcode_atts( $defaults, $atts );
			extract( $atts );

			$user_id    = get_current_user_id();
			$affiliate  = YITH_WCAF_Affiliate_Handler()->get_affiliate_by_user_id( $user_id );
			$query_args = array();

			// count commissions, with filter, if any
			$coupons_count = YITH_WCAF_Coupon_Handler()->count_affiliate_coupons( $affiliate['ID'] );

			// sets pagination filters
			$page_links = '';
			if ( $pagination == 'yes' && $coupons_count > 1 ) {
				$pages = ceil( $coupons_count / $per_page );

				if ( $current_page > $pages ) {
					$current_page = $pages;
				}

				$offset = ( $current_page - 1 ) * $per_page;

				if ( $pages > 1 ) {
					$page_links = paginate_links( array(
						'base'      => YITH_WCAF()->get_affiliate_dashboard_url( 'coupons', '%#%' ),
						'format'    => '%#%',
						'current'   => $current_page,
						'total'     => $pages,
						'show_all'  => false,
						'prev_next' => true
					) );
				}

				$query_args['limit']  = $per_page;
				$query_args['offset'] = $offset;
			}

			$discount_types = wc_get_coupon_types();
			$coupons        = YITH_WCAF_Coupon_Handler()->get_affiliate_coupons( $affiliate['ID'], $query_args );
			$coupons_set    = array();

			if ( ! empty( $coupons ) ) {
				foreach ( $coupons as $coupon_id ) {
					$coupon           = new WC_Coupon( $coupon_id );
					$type             = $coupon->get_discount_type();
					$formatted_type   = isset( $discount_types[ $type ] ) ? $discount_types[ $type ] : $type;
					$amount           = $coupon->get_amount();
					$formatted_amount = 'percentage' == $type ? $amount . '%' : $amount;
					$expire           = $coupon->get_date_expires();
					$formatted_expire = $expire ? date_i18n( wc_date_format(), strtotime( $expire ) ) : __( 'N/A', 'yith-woocommerce-affiliates' );

					// format info box
					$info                = '';
					$free_shipping       = $coupon->get_free_shipping();
					$minimum_spend       = $coupon->get_minimum_amount();
					$maximum_spend       = $coupon->get_maximum_amount();
					$individual_use      = $coupon->get_individual_use();
					$exclude_sale        = $coupon->get_exclude_sale_items();
					$products            = $coupon->get_product_ids();
					$excluded_products   = $coupon->get_excluded_product_ids();
					$categories          = $coupon->get_product_categories();
					$excluded_categories = $coupon->get_excluded_product_categories();
					$limit_per_coupon    = $coupon->get_usage_limit();
					$limit_per_x_items   = $coupon->get_limit_usage_to_x_items();
					$limit_per_user      = $coupon->get_usage_limit_per_user();

					if ( $free_shipping ) {
						$info .= sprintf( '<b>%s</b><br/>', __( 'Free shipping!', 'yith-woocommerce-affiliates' ) );
					}

					if ( $minimum_spend ) {
						$info .= sprintf( '<b>%s</b>: %s<br/>', __( 'Minimum to spend', 'yith-woocommerce-affiliates' ), wc_price( $minimum_spend ) );
					}

					if ( $maximum_spend ) {
						$info .= sprintf( '<b>%s</b>: %s<br/>', __( 'Maximum to spend', 'yith-woocommerce-affiliates' ), wc_price( $maximum_spend ) );
					}

					if ( $individual_use ) {
						$info .= sprintf( '<b>%s</b><br/>', __( 'Individual use!', 'yith-woocommerce-affiliates' ) );
					}

					if ( $exclude_sale ) {
						$info .= sprintf( '<b>%s</b><br/>', __( 'Exclude sale products!', 'yith-woocommerce-affiliates' ) );
					}

					if ( ! empty( $products ) ) {
						$product_names = array();
						foreach ( $products as $product_id ) {
							$product_names[] = get_the_title( $product_id );
						}
						$info .= sprintf( '<b>%s</b>: %s<br/>', __( 'Allowed products', 'yith-woocommerce-affiliates' ), implode( ', ', $product_names ) );
					}

					if ( ! empty( $excluded_products ) ) {
						$product_names = array();
						foreach ( $excluded_products as $product_id ) {
							$product_names[] = get_the_title( $product_id );
						}
						$info .= sprintf( '<b>%s</b>: %s<br/>', __( 'Excluded products', 'yith-woocommerce-affiliates' ), implode( ', ', $product_names ) );
					}

					if ( ! empty( $categories ) ) {
						$categories_names = array();
						foreach ( $categories as $term_id ) {
							$term               = get_term( $term_id );
							$categories_names[] = $term->name;
						}
						$info .= sprintf( '<b>%s</b>: %s<br/>', __( 'Allowed product categories', 'yith-woocommerce-affiliates' ), implode( ', ', $categories_names ) );
					}

					if ( ! empty( $excluded_categories ) ) {
						$categories_names = array();
						foreach ( $excluded_categories as $term_id ) {
							$term               = get_term( $term_id );
							$categories_names[] = $term->name;
						}
						$info .= sprintf( '<b>%s</b>: %s<br/>', __( 'Excluded product categories', 'yith-woocommerce-affiliates' ), implode( ', ', $categories_names ) );
					}

					if ( $limit_per_coupon ) {
						$info .= sprintf( '<b>%s</b>: %d<br/>', __( 'Limit per coupon:', 'yith-woocommerce-affiliates' ), $limit_per_coupon );
					}

					if ( $limit_per_x_items ) {
						$info .= sprintf( '<b>%s</b>: %d<br/>', __( 'Limit per number of items:', 'yith-woocommerce-affiliates' ), $limit_per_x_items );
					}

					if ( $limit_per_user ) {
						$info .= sprintf( '<b>%s</b>: %d<br/>', __( 'Limit per user:', 'yith-woocommerce-affiliates' ), $limit_per_user );
					}

					if ( ! $info ) {
						$info .= _x( 'No additional info', 'Coupon dashboard info tooltip', 'yith-woocommerce-affiliates' );
					}

					$coupons_set[ $coupon_id ] = array(
						'coupon' => $coupon,
						'code'   => $coupon->get_code(),
						'type'   => apply_filters( 'yith_wcaf_coupon_formatted_type', $formatted_type, $coupon ),
						'amount' => apply_filters( 'yith_wcaf_coupon_formatted_amount', $formatted_amount, $coupon ),
						'expire' => apply_filters( 'yith_wcaf_coupon_formatted_expire', $formatted_expire, $coupon ),
						'info'   => apply_filters( 'yith_wcaf_coupon_formatted_info', $info, $coupon )
					);
				}
			}

			$atts = array_merge(
				$atts,
				array(
					'coupons'           => $coupons_set,
					'dashboard_links'   => yith_wcaf_get_dashboard_navigation_menu(),
					'show_right_column' => apply_filters( 'yith_wcaf_show_dashboard_links', 'yes' == $show_dashboard_links, 'coupon_settings' ),
					'page_links'        => $page_links
				)
			);

			$template_name = 'dashboard-coupons.php';
			ob_start();

			yith_wcaf_get_template( $template_name, $atts, 'shortcodes' );

			return ob_get_clean();
		}

		/**
		 * Returns output for withdraw shortcode
		 *
		 * @param $atts mixed Array of shortcodes attributes
		 *
		 * @return string Shortcode content
		 * @since 1.0.0
		 */
		public static function show_withdraw( $atts = array() ) {
			return self::affiliate_dashboard_withdraw( $atts );
		}

		/**
		 * Returns output for coupon shortcode
		 *
		 * @param $atts mixed Array of shortcodes attributes
		 *
		 * @return string Shortcode content
		 * @since 1.0.0
		 */
		public static function show_coupons( $atts = array() ) {
			return self::affiliate_dashboard_coupons( $atts );
		}

		/**
		 * Returns output for section with details about affiliate currently configured for user section
		 *
		 * @param $atts mixed Array of shortcodes attributes
		 *
		 * @return string Shortcode content
		 * @since 1.5.2
		 */
		public static function current_affiliate( $atts = array() ) {
			/**
			 * @var $show_gravatar  string (yes/no)
			 * @var $show_real_name string (yes/no)
			 * @var $show_email     string (yes/no)
			 */
			$defaults = array(
				'show_gravatar'        => 'yes',
				'show_real_name'       => 'yes',
				'show_email'           => 'yes',
				'no_affiliate_message' => __( 'There isn\'t any affiliate selected for current session', 'yith-woocommerce-affiliates' )
			);

			$atts = shortcode_atts( $defaults, $atts );
			extract( $atts );

			$current_affiliate = YITH_WCAF_Affiliate()->get_affiliate();
			$user              = $current_affiliate ? get_userdata( $current_affiliate['user_id'] ) : false;

			$atts = array_merge(
				$atts,
				array(
					'current_affiliate' => $current_affiliate,
					'user'              => $user
				)
			);

			$template_name = 'current-affiliate.php';

			ob_start();

			yith_wcaf_get_template( $template_name, $atts, 'shortcodes' );

			return ob_get_clean();
		}

		/* === UTILITY METHODS === */

		/**
		 * Prints field to be used on affiliates shortcodes
		 *
		 * @param $id string Id of the field to print
		 *
		 * @return array Details of the field to print (you can use it with woocommerce_form_field)
		 */
		public static function get_field( $id ) {
			$fields = WC()->countries->get_address_fields();

			// rename general fields
			$fields['first_name'] = $fields['billing_first_name'];
			$fields['last_name']  = $fields['billing_last_name'];

			// add custom invoice fields
			$fields['number'] = array(
				'label'        => __( 'Invoice Number', 'yith-woocommerce-affiliates' ),
				'required'     => true,
				'type'         => 'text',
				'class'        => array( 'form-row-wide' ),
				'autocomplete' => 'invoice-number',
				'priority'     => 100,
			);

			$fields['vat'] = array(
				'label'        => __( 'VAT Number', 'yith-woocommerce-affiliates' ),
				'required'     => true,
				'type'         => 'text',
				'class'        => array( 'form-row-wide' ),
				'autocomplete' => 'vat-number',
				'priority'     => 100,
			);

			$fields['cif'] = array(
				'label'        => __( 'CIF/SSN', 'yith-woocommerce-affiliates' ),
				'required'     => true,
				'type'         => 'text',
				'class'        => array( 'form-row-wide' ),
				'autocomplete' => 'cif',
				'priority'     => 100,
			);

			$fields['company'] = array(
				'label'        => __( 'Company', 'yith-woocommerce-affiliates' ),
				'required'     => true,
				'type'         => 'text',
				'class'        => array( 'form-row-wide' ),
				'autocomplete' => 'company',
				'priority'     => 100,
			);

			$fields['type'] = array(
				'required' => false,
				'type'     => 'radio',
				'options'  => array(
					'personal' => __( 'Personal', 'yith-woocommerce-affiliates' ),
					'business' => __( 'Business', 'yith-woocommerce-affiliates' ),
				),
				'default'  => 'business',
				'class'    => array( 'form-row-wide', 'radio-checkout' ),
				'priority' => 5
			);

			// check if fields is in current set

			if ( isset( $fields[ $id ] ) ) {
				return $fields[ $id ];
			}

			return apply_filters( 'yith_wcaf_form_field', false, $id, $fields );
		}

		/* === PREMIUM SETTINGS METHODS === */

		/**
		 * Adds additional defaults to registration form shortcode, in order to enable users to relative attributes
		 *
		 * @param $defaults array Shortcode defaults
		 *
		 * @return array Filtered shortcode defaults
		 * @since 1.2.5
		 */
		public static function add_registration_form_defaults( $defaults ) {
			$defaults = array_merge(
				$defaults,
				array(
					'enabled_form'                  => get_option( 'yith_wcaf_referral_registration_form_options' ),
					'show_website_field'            => get_option( 'yith_wcaf_referral_registration_show_website_field' ),
					'show_promotional_method_field' => get_option( 'yith_wcaf_referral_registration_show_promotional_methods_field' ),
					'show_terms_field'              => get_option( 'yith_wcaf_referral_registration_show_terms_field' ),
					'terms_label'                   => get_option( 'yith_wcaf_referral_registration_terms_label' ),
					'terms_anchor_url'              => get_option( 'yith_wcaf_referral_registration_terms_anchor_url' ),
					'terms_anchor_text'             => get_option( 'yith_wcaf_referral_registration_terms_anchor_text' ),
				)
			);

			return $defaults;
		}

		/**
		 * Filters default settings view params, to add premium options
		 *
		 * @param $atts mixed Array of params for settings template
		 * @param $user \WP_User User currently logged in
		 *
		 * @return mixed Filtered set of params for settings template
		 * @Since 1.2.5
		 */
		public static function add_premium_dashboard_settings_options( $atts, $user ) {
			if ( ! $user ) {
				return $atts;
			}
			$user_id = $user->ID;

			$notify_pending_commissions = isset( $user->_yith_wcaf_notify_pending_commission ) ? $user->_yith_wcaf_notify_pending_commission : apply_filters( 'yith_wcaf_default_notify_user_pending_commission', 'no', $user_id );
			$notify_paid_commissions    = isset( $user->_yith_wcaf_notify_paid_commission ) ? $user->_yith_wcaf_notify_paid_commission : apply_filters( 'yith_wcaf_default_notify_user_paid_commission', 'no', $user_id );

			$atts['notify_pending_commissions'] = $notify_pending_commissions;
			$atts['notify_paid_commissions']    = $notify_paid_commissions;

			if ( $atts['show_additional_fields'] == 'yes' ) {
				$atts['show_website_field']             = get_option( 'yith_wcaf_referral_registration_show_website_field', 'no' );
				$atts['show_promotional_methods_field'] = get_option( 'yith_wcaf_referral_registration_show_promotional_methods_field', 'no' );

				if ( 'yes' == $atts['show_website_field'] ) {
					$atts['affiliate_website'] = isset( $user->_yith_wcaf_website ) ? $user->_yith_wcaf_website : '';
				}

				if ( 'yes' == $atts['show_promotional_methods_field'] ) {
					$atts['promotional_method']        = isset( $user->_yith_wcaf_promotional_method ) ? $user->_yith_wcaf_promotional_method : '';
					$atts['custom_promotional_method'] = isset( $user->_yith_wcaf_custom_method ) ? $user->_yith_wcaf_custom_method : '';
				}
			}

			return $atts;
		}

		/**
		 * Save premium settings from settings shortcode
		 *
		 * @param $change  bool Whether options where changed or not
		 * @param $user_id int Currently logged is user
		 *
		 * @return bool Whether options where changed or not
		 * @Since 1.2.5
		 */
		public static function save_premium_dashboard_settings_options( $change, $user_id ) {
			if ( isset( $_REQUEST['settings_submit'] ) ) {
				$notify_pending_commissions = isset( $_REQUEST['notify_pending_commissions'] ) ? 'yes' : 'no';
				$notify_paid_commissions    = isset( $_REQUEST['notify_paid_commissions'] ) ? 'yes' : 'no';
				$website                    = ! empty( $_POST['website'] ) ? esc_url( $_POST['website'] ) : false;
				$promotional_method         = ! empty( $_POST['how_promote'] ) && in_array( $_POST['how_promote'], array_keys( yith_wcaf_get_promote_methods() ) ) ? $_POST['how_promote'] : false;
				$custom_method              = ! empty( $_POST['custom_promote'] ) ? sanitize_text_field( $_POST['custom_promote'] ) : false;

				update_user_meta( $user_id, '_yith_wcaf_notify_pending_commission', $notify_pending_commissions );
				update_user_meta( $user_id, '_yith_wcaf_notify_paid_commission', $notify_paid_commissions );
				update_user_meta( $user_id, '_yith_wcaf_website', $website );
				update_user_meta( $user_id, '_yith_wcaf_promotional_method', $promotional_method );
				update_user_meta( $user_id, '_yith_wcaf_custom_method', $custom_method );

				$change = true;
			}

			return $change;
		}
	}
}