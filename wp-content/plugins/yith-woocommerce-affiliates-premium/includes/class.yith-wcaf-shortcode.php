<?php
/**
 * Shortcode class
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

if ( ! class_exists( 'YITH_WCAF_Shortcode' ) ) {
	/**
	 * WooCommerce Affiliate Shortcode
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAF_Shortcode {

		/**
		 * True while printing affiliate dashboard
		 *
		 * @var bool Whether we're currently printing affiliate dashboard
		 */
		public static $is_affiliate_dashboard = false;

		/**
		 * True while printing affiliate registration form
		 *
		 * @var bool Whether we're currently printing affiliate registration form
		 */
		public static $is_registration_form = false;

		/**
		 * Performs all required add_shortcode
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public static function init() {
			add_shortcode( 'yith_wcaf_registration_form', array( 'YITH_WCAF_Shortcode', 'registration_form' ) );
			add_shortcode( 'yith_wcaf_affiliate_dashboard', array( 'YITH_WCAF_Shortcode', 'affiliate_dashboard' ) );
			add_shortcode( 'yith_wcaf_link_generator', array( 'YITH_WCAF_Shortcode', 'link_generator' ) );
			add_shortcode( 'yith_wcaf_show_if_affiliate', array( 'YITH_WCAF_Shortcode', 'show_if_affiliate' ) );
			add_shortcode( 'yith_wcaf_show_clicks', array( 'YITH_WCAF_Shortcode', 'show_clicks' ) );
			add_shortcode( 'yith_wcaf_show_commissions', array( 'YITH_WCAF_Shortcode', 'show_commissions' ) );
			add_shortcode( 'yith_wcaf_show_payments', array( 'YITH_WCAF_Shortcode', 'show_payments' ) );
			add_shortcode( 'yith_wcaf_show_settings', array( 'YITH_WCAF_Shortcode', 'show_settings' ) );

			// register gutenberg blocks.
			self::register_gutenberg_blocks();

			add_action( 'yith_plugin_fw_gutenberg_before_do_shortcode', array( 'YITH_WCAF_Shortcode', 'fix_for_gutenberg_blocks' ), 10, 1 );

			// register elementor widgets.
			add_action( 'init', array( 'YITH_WCAF_Shortcode', 'init_elementor_widgets' ) );
		}

		/**
		 * Register gutenberg blocks
		 *
		 * @return void
		 * @since 1.5.0
		 */
		public static function register_gutenberg_blocks() {
			$blocks = array(
				'yith-wcaf-registration-form'   => array(
					'style'          => 'yith-wcaf',
					'script'         => 'yith-wcaf',
					'title'          => _x( 'YITH Affiliates Registration Form', '[gutenberg]: block name', 'yith-woocommerce-affiliates' ),
					'description'    => _x( 'Show registration form for your affiliates', '[gutenberg]: block description', 'yith-woocommerce-affiliates' ),
					'shortcode_name' => 'yith_wcaf_registration_form',
					'attributes'     => array(
						'show_login_form'        => array(
							'type'    => 'select',
							'label'   => _x( 'Show Login form', '[gutenberg]: attribute description', 'yith-woocommerce-affiliates' ),
							'options' => array(
								'no'  => _x( 'Hide login form', '[gutenberg]: inspector description', 'yith-woocommerce-affiliates' ),
								'yes' => _x( 'Show login form', '[gutenberg]: inspector description', 'yith-woocommerce-affiliates' ),
							),
							'default' => 'no',
						),
						'show_name_field'        => array(
							'type'    => 'select',
							'label'   => _x( 'Show First name field', '[gutenberg]: attribute description', 'yith-woocommerce-affiliates' ),
							'default' => 'no',
							'options' => array(
								'no'  => _x( 'Hide first name field', '[gutenberg]: inspector description', 'yith-woocommerce-affiliates' ),
								'yes' => _x( 'Show first name field', '[gutenberg]: inspector description', 'yith-woocommerce-affiliates' ),
							),
						),
						'show_surname_field'     => array(
							'type'    => 'select',
							'label'   => _x( 'Show Last name field', '[gutenberg]: attribute description', 'yith-woocommerce-affiliates' ),
							'default' => 'no',
							'options' => array(
								'no'  => _x( 'Hide last name field', '[gutenberg]: Help text', 'yith-woocommerce-affiliates' ),
								'yes' => _x( 'Show last name field', '[gutenberg]: Help text', 'yith-woocommerce-affiliates' ),
							),
						),
						'show_additional_fields' => array(
							'type'    => 'select',
							'label'   => _x( 'Show Additional fields', '[gutenberg]: attribute description', 'yith-woocommerce-affiliates' ),
							'default' => 'no',
							'options' => array(
								'no'  => _x( 'Hide additional fields', '[gutenberg]: Help text', 'yith-woocommerce-affiliates' ),
								'yes' => _x( 'Show additional field', '[gutenberg]: Help text', 'yith-woocommerce-affiliates' ),
							),
						)
					),
				),
				'yith-wcaf-affiliate-dashboard' => array(
					'style'          => 'yith-wcaf',
					'script'         => 'yith-wcaf',
					'title'          => _x( 'YITH Affiliates Dasbharod', '[gutenberg]: block name', 'yith-woocommerce-affiliates' ),
					'description'    => _x( 'Show affiliate dashboard to your affiliates', '[gutenberg]: block description', 'yith-woocommerce-affiliates' ),
					'shortcode_name' => 'yith_wcaf_affiliate_dashboard',
					'attributes'     => array(
						'show_dashboard_links' => array(
							'type'    => 'select',
							'label'   => _x( 'Show navigation menu', '[gutenberg]: attribute description', 'yith-woocommerce-affiliates' ),
							'options' => array(
								'no'  => _x( 'Hide menu', '[gutenberg]: inspector description', 'yith-woocommerce-affiliates' ),
								'yes' => _x( 'Show menu', '[gutenberg]: inspector description', 'yith-woocommerce-affiliates' ),
							),
							'default' => 'yes',
						),
					),
				),
				'yith-wcaf-link-generator'      => array(
					'style'          => 'yith-wcaf',
					'script'         => 'yith-wcaf',
					'title'          => _x( 'YITH Affiliates Link Generator', '[gutenberg]: block name', 'yith-woocommerce-affiliates' ),
					'description'    => _x( 'Show referral link generation form for your affiliates', '[gutenberg]: block description', 'yith-woocommerce-affiliates' ),
					'shortcode_name' => 'yith_wcaf_link_generator',
					'attributes'     => array(
						'show_dashboard_links' => array(
							'type'    => 'select',
							'label'   => _x( 'Show navigation menu', '[gutenberg]: attribute description', 'yith-woocommerce-affiliates' ),
							'options' => array(
								'no'  => _x( 'Hide menu', '[gutenberg]: inspector description', 'yith-woocommerce-affiliates' ),
								'yes' => _x( 'Show menu', '[gutenberg]: inspector description', 'yith-woocommerce-affiliates' ),
							),
							'default' => 'no',
						),
					),
				),
			);
			yith_plugin_fw_gutenberg_add_blocks( $blocks );
		}

		/**
		 * Apply required fixes before rendering gutenberg blocks
		 *
		 * @return void
		 * @since 1.5.0
		 */
		public static function fix_for_gutenberg_blocks( $shortcode ) {
			if ( strpos( $shortcode, '[yith_wcaf_registration_form' ) !== false ) {
				global $current_user;
				$current_user = null;

				define( 'XMLRPC_REQUEST', true );
			} elseif ( strpos( $shortcode, '[yith_wcaf_affiliate_dashboard' ) !== false ) {
				add_filter( 'yith_wcaf_is_user_enabled_affiliate', '__return_true' );
			}
		}

		/**
		 * Register custom widgets for Elementor
		 *
		 * @return void
		 */
		public static function init_elementor_widgets() {
			// check if elementor is active.
			if ( ! defined( 'ELEMENTOR_VERSION' ) ) {
				return;
			}

			// include widgets.
			include_once YITH_WCAF_INC . 'widgets/elementor/class.yith-wcaf-elementor-registration-form.php';
			include_once YITH_WCAF_INC . 'widgets/elementor/class.yith-wcaf-elementor-affiliate-dashboard.php';
			include_once YITH_WCAF_INC . 'widgets/elementor/class.yith-wcaf-elementor-link-generator.php';

			// register widgets.
			add_action( 'elementor/widgets/widgets_registered', array( 'YITH_WCAF_Shortcode', 'register_elementor_widgets' ) );
		}

		/**
		 * Register Elementor Widgets
		 *
		 * @return void
		 */
		public static function register_elementor_widgets() {
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new YITH_WCAF_Elementor_Registration_Form() );
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new YITH_WCAF_Elementor_Affiliate_Dashboard() );
			\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new YITH_WCAF_Elementor_Link_Generator() );
		}

		/**
		 * Returns output for affiliates registration form
		 *
		 * @param $atts mixed Array of shortcodes attributes
		 *
		 * @return string Shortcode content
		 * @since 1.0.0
		 */
		public static function registration_form( $atts = array() ) {
			self::$is_registration_form = true;

			$defaults = apply_filters( 'yith_wcaf_registration_form_defaults', array(
				'show_login_form'        => get_option( 'yith_wcaf_referral_registration_show_login_form' ),
				'show_name_field'        => get_option( 'yith_wcaf_referral_registration_show_name_field' ),
				'show_surname_field'     => get_option( 'yith_wcaf_referral_registration_show_surname_field' ),
				'show_additional_fields' => get_option( 'yith_wcaf_referral_show_fields_on_become_an_affiliate', 'no' )
			) );

			$atts = shortcode_atts( $defaults, $atts );
			extract( $atts );

			$template_name = 'registration-form.php';

			ob_start();

			yith_wcaf_get_template( $template_name, apply_filters( 'yith_wcaf_affiliate_registration_form_atts', $atts ), 'shortcodes' );

			$return = ob_get_clean();

			self::$is_registration_form = false;

			return $return;
		}

		/**
		 * Returns output for link generator form
		 *
		 * @param $atts mixed Array of shortcodes attributes
		 *
		 * @return string Shortcode content
		 * @since 1.0.0
		 */
		public static function link_generator( $atts = array() ) {
			/**
			 * @var $show_dashboard_links string (yes/no)
			 */
			$defaults = array(
				'show_dashboard_links' => 'no'
			);

			$atts = shortcode_atts( $defaults, $atts );
			extract( $atts );

			if ( ! YITH_WCAF_Affiliate_Handler()->can_user_see_section( false, 'generate-link', true ) ) {
				return '';
			}

			$original_url = isset( $_REQUEST['original_url'] ) ? esc_url( $_REQUEST['original_url'] ) : false;

			// check if original url is a local url
			if ( $original_url ) {
				$parsed_original_url = parse_url( $original_url );
				$original_host       = str_replace( 'www.', '', $parsed_original_url['host'] );
				$server_name         = str_replace( 'www.', '', $_SERVER['SERVER_NAME'] );

				$is_hosted = $original_host == $server_name;

				if ( ! apply_filters( 'yith_wcaf_is_hosted', $is_hosted, $original_host, $server_name ) ) {
					$original_url = false;
				}
			}

			// generate referral url
			$request_token     = false;
			$request_user_name = isset( $_REQUEST['username'] ) ? sanitize_text_field( $_REQUEST['username'] ) : false;
			$request_user      = get_user_by( 'login', $request_user_name );

			if ( $request_user ) {
				$request_affiliate = YITH_WCAF_Affiliate_Handler()->get_affiliate_by_user_id( $request_user->ID );

				if ( $request_affiliate ) {
					$request_token = $request_affiliate['token'];
				}
			}

			$generated_url = YITH_WCAF()->get_referral_url( $request_token, $original_url );
			if ( is_user_logged_in() ) {
				$user_id         = get_current_user_id();
				$user            = get_user_by( 'id', $user_id );
				$affiliate       = YITH_WCAF_Affiliate_Handler()->get_affiliate_by_user_id( $user_id );
				$affiliate_id    = isset( $affiliate['ID'] ) ? $affiliate['ID'] : false;
				$affiliate_token = isset( $affiliate['token'] ) ? $affiliate['token'] : false;
				$referral_link   = ! empty( $affiliate_token ) ? YITH_WCAF()->get_referral_url() : '';
			}

			$atts = array_merge(
				$atts,
				array(
					'user_id'           => isset( $user_id ) ? $user_id : false,
					'user'              => isset( $user ) ? $user : false,
					'affiliate'         => isset( $affiliate ) ? $affiliate : false,
					'affiliate_id'      => isset( $affiliate_id ) ? $affiliate_id : false,
					'affiliate_token'   => isset( $affiliate_token ) ? $affiliate_token : false,
					'referral_link'     => ! empty( $referral_link ) ? $referral_link : false,
					'username'          => $request_user_name,
					'original_url'      => $original_url,
					'generated_url'     => apply_filters( 'yith_wcaf_link_generator_generated_url', $generated_url ),
					'show_right_column' => apply_filters( 'yith_wcaf_show_dashboard_links', 'yes' == $show_dashboard_links, 'link_generator' ),
					'dashboard_links'   => yith_wcaf_get_dashboard_navigation_menu(),
				)
			);

			// retrieve share options
			$share_facebook_enabled  = get_option( 'yith_wcaf_share_fb' ) == 'yes';
			$share_twitter_enabled   = get_option( 'yith_wcaf_share_twitter' ) == 'yes';
			$share_pinterest_enabled = get_option( 'yith_wcaf_share_pinterest' ) == 'yes';
			$share_email_enabled     = get_option( 'yith_wcaf_share_email' ) == 'yes';
			$share_whatsapp_enabled  = get_option( 'yith_wcaf_share_whatsapp' ) == 'yes';

			$share_enabled     = $share_facebook_enabled || $share_twitter_enabled || $share_pinterest_enabled || $share_email_enabled || $share_whatsapp_enabled;
			$additional_params = array(
				// share data
				'share_enabled' => $share_enabled,
			);
			if ( $share_enabled ) {
				$share_title           = apply_filters( 'yith_wcaf_socials_share_title', __( 'Share on:', 'yith-woocommerce-wishlist' ) );
				$share_link_url        = $generated_url;
				$share_links_title     = apply_filters( 'plugin_text', urlencode( get_option( 'yith_wcaf_socials_title' ) ) );
				$share_twitter_summary = urlencode( str_replace( '%referral_url%', '', get_option( 'yith_wcaf_socials_text' ) ) );
				$share_summary         = urlencode( str_replace( '%referral_url%', $share_link_url, get_option( 'yith_wcaf_socials_text' ) ) );
				$share_image_url       = urlencode( get_option( 'yith_wcaf_socials_image_url' ) );

				$share_atts = array(
					'share_facebook_enabled'  => $share_facebook_enabled,
					'share_twitter_enabled'   => $share_twitter_enabled,
					'share_pinterest_enabled' => $share_pinterest_enabled,
					'share_email_enabled'     => $share_email_enabled,
					'share_whatsapp_enabled'  => $share_whatsapp_enabled,
					'share_title'             => $share_title,
					'share_link_url'          => $share_link_url,
					'share_link_title'        => $share_links_title,
					'share_twitter_summary'   => $share_twitter_summary,
					'share_summary'           => $share_summary,
					'share_image_url'         => $share_image_url
				);

				$additional_params['share_atts'] = $share_atts;
			}

			$additional_params = apply_filters( 'yith_wcaf_wishlist_params', $additional_params );

			$atts = array_merge(
				$atts,
				$additional_params
			);

			// adds attributes list to params to extract in template, so it can be passed through a new get_template()
			$atts['atts'] = $atts;

			$template_name = 'link-generator.php';

			ob_start();

			yith_wcaf_get_template( $template_name, $atts, 'shortcodes' );

			return ob_get_clean();
		}

		/**
		 * Returns content of the shortcode, only if current user is logged in and he/she is an affiliate
		 *
		 * @param $atts    mixed Array of shortcode attributes
		 * @param $content string Content to show, if conditions are matched
		 *
		 * @return string Shortcode content
		 * @since 1.0.0
		 */
		public static function show_if_affiliate( $atts = array(), $content = '' ) {
			/**
			 * @var $show_to string (valid_affiliates / enabled_affiliates / all_affiliates / {user role} / logged_in_users / anyone)
			 */
			$defaults = array(
				'show_to' => 'enabled_affiliates'
				// valid_affiliates/ enabled_affiliates / all_affiliates / {user role} / logged_in_users / anyone
			);

			$atts = shortcode_atts( $defaults, $atts );
			extract( $atts );

			$render_content = false;

			switch ( $show_to ) {
				case 'valid_affiliates':
					if ( is_user_logged_in() ) {
						$affiliate      = YITH_WCAF_Affiliate_Handler()->get_affiliate_by_user_id( get_current_user_id(), true, true );
						$render_content = ! empty( $affiliate );
					}
					break;
				case 'enabled_affiliates':
					if ( is_user_logged_in() ) {
						$affiliate      = YITH_WCAF_Affiliate_Handler()->get_affiliate_by_user_id( get_current_user_id(), true );
						$render_content = ! empty( $affiliate );
					}
					break;
				case 'all_affiliates':
					if ( is_user_logged_in() ) {
						$affiliate      = YITH_WCAF_Affiliate_Handler()->get_affiliate_by_user_id( get_current_user_id() );
						$render_content = ! empty( $affiliate );
					}
					break;
				case 'logged_in_users':
					$render_content = is_user_logged_in();
					break;
				case 'anyone':
					$render_content = true;
					break;
				default:
					$roles = explode( ',', $show_to );
					if ( is_user_logged_in() ) {
						$user = wp_get_current_user();

						if ( array_intersect( $roles, $user->roles ) ) {
							$render_content = true;
						}
					}
					break;
			}

			$callbacks = apply_filters( 'yith_wcaf_show_if_affiliate_content_callbacks', array(
				'do_shortcode'
			) );

			ob_start();

			if ( $render_content ) {

				if ( ! empty( $callbacks ) ) {
					foreach ( $callbacks as $callback ) {
						$content = $callback( $content );
					}
				}

				echo $content;
			}

			return ob_get_clean();
		}

		/**
		 * Returns output for affiliates dashboard
		 *
		 * @param $atts mixed Array of shortcode attributes
		 *
		 * @return string Shortcode content
		 * @since 1.0.0
		 */
		public static function affiliate_dashboard( $atts = array() ) {
			global $wp;
			$atts = (array) $atts;

			self::$is_affiliate_dashboard = true;

			// if user is not an enabled affiliate, show registration form
			if ( ! YITH_WCAF_Affiliate_Handler()->is_user_enabled_affiliate() ) {
				$show_name    = get_option( 'yith_wcaf_referral_registration_show_name_field' );
				$show_surname = get_option( 'yith_wcaf_referral_registration_show_surname_field' );

				$return = self::registration_form( array_merge( $atts, array(
					'show_name_field'    => $show_name,
					'show_surname_field' => $show_surname
				) ) );
			} // if set "commissions" query var, show commissions table
			elseif ( isset( $wp->query_vars['commissions'] ) ) {
				$return = self::affiliate_dashboard_commissions( $atts );
			} // if set "clicks" query var, show clicks table
			elseif ( isset( $wp->query_vars['clicks'] ) ) {
				$return = self::affiliate_dashboard_clicks( $atts );
			} // if set "payments" query var, show payments table
			elseif ( isset( $wp->query_vars['payments'] ) ) {
				$return = self::affiliate_dashboard_payments( $atts );
			} // if set "generate-link" query var, show generate click shortcode
			elseif ( isset( $wp->query_vars['generate-link'] ) ) {
				$return = self::link_generator( $atts );
			} // if set "settings" query var, show settings
			elseif ( isset( $wp->query_vars['settings'] ) ) {
				$return = self::affiliate_dashboard_settings( $atts );
			} // otherwise, show summary
			else {
				if ( ! $return = apply_filters( 'yith_wcaf_custom_dashboard_sections', '', $wp->query_vars, $atts ) ) {
					$return = self::affiliate_dashboard_summary( $atts );
				}
			}

			self::$is_affiliate_dashboard = false;

			return $return;
		}

		/**
		 * Print commissions section of the dashboard
		 *
		 * @param $atts mixed Array of shortcode attributes
		 *
		 * @return string Shortcode content
		 * @since 1.0.0
		 */
		public static function affiliate_dashboard_commissions( $atts = array() ) {
			/**
			 * @var $pagination           string (yes/no)
			 * @var $per_page             int (number of items to show in each page)
			 * @var $current_page         int (current page number)
			 * @var $show_dashboard_links string (yes/no)
			 */
			$defaults = array(
				'pagination'           => 'yes',
				'per_page'             => isset( $_REQUEST['per_page'] ) ? intval( wc_clean( $_REQUEST['per_page'] ) ) : 10,
				'current_page'         => max( 1, get_query_var( 'commissions' ) ),
				'show_dashboard_links' => 'no'
			);

			$atts = shortcode_atts( $defaults, $atts );
			extract( $atts );

			if ( ! YITH_WCAF_Affiliate_Handler()->can_user_see_section( false, 'commissions' ) ) {
				return '';
			}

			$user_id   = get_current_user_id();
			$user      = get_user_by( 'id', $user_id );
			$affiliate = YITH_WCAF_Affiliate_Handler()->get_affiliate_by_user_id( $user_id );

			// sets filters from query string params
			$filters_set = false;
			$query_args  = array();

			// filter by stauts
			if ( isset( $_REQUEST['status'] ) && in_array( $_REQUEST['status'], YITH_WCAF_Commission_Handler()->get_available_status() ) ) {
				$status               = sanitize_text_field( $_REQUEST['status'] );
				$query_args['status'] = $status;
				$filters_set          = true;
			}

			// filter by product
			if ( isset( $_REQUEST['product_id'] ) && ! empty( $_REQUEST['product_id'] ) ) {
				$product_id               = intval( $_REQUEST['product_id'] );
				$query_args['product_id'] = $product_id;
				$filters_set              = true;
			}

			// filter by date
			if ( ( isset( $_REQUEST['to'] ) && ! empty( $_REQUEST['to'] ) ) || ( isset( $_REQUEST['from'] ) && ! empty( $_REQUEST['from'] ) ) ) {
				$from       = ! empty( $_REQUEST['from'] ) ? sanitize_text_field( $_REQUEST['from'] ) : '';
				$from_query = ! empty( $from ) ? date( 'Y-m-d 00:00:00', strtotime( $from ) ) : '';
				$to         = ! empty( $_REQUEST['to'] ) ? sanitize_text_field( $_REQUEST['to'] ) : '';
				$to_query   = ! empty( $to ) ? date( 'Y-m-d 23:59:59', strtotime( $to ) ) : '';
				$interval   = array();

				if ( $from_query ) {
					$interval['start_date'] = $from_query;
				}

				if ( $to_query ) {
					$interval['end_date'] = $to_query;
				}

				$query_args['interval'] = $interval;
				$filters_set            = true;
			}

			// count commissions, with filter, if any
			$commissions_count = YITH_WCAF_Commission_Handler()->count_commission( array_merge(
				array(
					'user_id'        => $user_id,
					'status__not_in' => 'trash'
				),
				$query_args
			) );

			// sets pagination filters
			$page_links = '';
			if ( $pagination == 'yes' && $commissions_count > 1 ) {
				$pages = ceil( $commissions_count / $per_page );

				if ( $current_page > $pages ) {
					$current_page = $pages;
				}

				$offset = ( $current_page - 1 ) * $per_page;

				if ( $pages > 1 ) {
					$page_links = paginate_links( array(
						'base'      => YITH_WCAF()->get_affiliate_dashboard_url( 'commissions', '%#%' ),
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

			$orderby = isset( $_REQUEST['orderby'] ) ? sanitize_text_field( $_REQUEST['orderby'] ) : 'created_at';
			$order   = isset( $_REQUEST['order'] ) ? sanitize_text_field( $_REQUEST['order'] ) : 'DESC';

			// retrieve commissions
			$commissions = YITH_WCAF_Commission_Handler()->get_commissions( array_merge(
				array(
					'user_id'        => $user_id,
					'status__not_in' => 'trash'
				),
				$query_args,
				array(
					'orderby' => $orderby,
					'order'   => $order
				)
			) );

			$atts = array_merge(
				$atts,
				array(
					'user_id'                    => $user_id,
					'user'                       => $user,
					'affiliate_id'               => $affiliate['ID'],
					'affiliate'                  => $affiliate,
					'commissions'                => $commissions,
					'filter_set'                 => $filters_set,
					'page_links'                 => $page_links,
					'status'                     => isset( $status ) ? $status : false,
					'product_id'                 => isset( $product_id ) ? $product_id : false,
					'product_name'               => isset( $product_id ) ? sprintf( '#%d – %s', $product_id, get_the_title( $product_id ) ) : '',
					'from'                       => isset( $from ) ? $from : false,
					'to'                         => isset( $to ) ? $to : false,
					'dashboard_commissions_link' => YITH_WCAF()->get_affiliate_dashboard_url( 'commissions', 1 ),
					'ordered'                    => $orderby,
					'to_order'                   => $order == 'DESC' ? 'ASC' : 'DESC',
					'show_right_column'          => apply_filters( 'yith_wcaf_show_dashboard_links', 'yes' == $show_dashboard_links, 'dashboard_commissions' ),
					'dashboard_links'            => yith_wcaf_get_dashboard_navigation_menu(),
				)
			);

			$template_name = 'dashboard-commissions.php';

			ob_start();

			yith_wcaf_get_template( $template_name, $atts, 'shortcodes' );

			return ob_get_clean();
		}

		/**
		 * Print clicks section of the dashboard
		 *
		 * @param $atts mixed Array of shortcode attributes
		 *
		 * @return string Shortcode content
		 * @since 1.0.0
		 */
		public static function affiliate_dashboard_clicks( $atts = array() ) {
			/**
			 * @var $pagination           string (yes/no)
			 * @var $per_page             int (number of items to show in each page)
			 * @var $current_page         int (current page number)
			 * @var $show_dashboard_links string (yes/no)
			 */
			$defaults = array(
				'pagination'           => 'yes',
				'per_page'             => isset( $_REQUEST['per_page'] ) ? intval( wc_clean( $_REQUEST['per_page'] ) ) : 10,
				'current_page'         => max( 1, get_query_var( 'clicks' ) ),
				'show_dashboard_links' => 'no'
			);

			$atts = shortcode_atts( $defaults, $atts );
			extract( $atts );

			if ( ! YITH_WCAF_Affiliate_Handler()->can_user_see_section( false, 'clicks' ) ) {
				return '';
			}

			$user_id   = get_current_user_id();
			$user      = get_user_by( 'id', $user_id );
			$affiliate = YITH_WCAF_Affiliate_Handler()->get_affiliate_by_user_id( $user_id );

			// sets filters from query string params
			$filters_set = false;
			$query_args  = array();

			// filter by status
			if ( isset( $_REQUEST['status'] ) && in_array( $_REQUEST['status'], array(
					'converted',
					'not-converted'
				) ) ) {
				$status                  = $_REQUEST['status'];
				$status_query            = ( $_REQUEST['status'] == 'converted' ) ? 'yes' : 'no';
				$query_args['converted'] = $status_query;
			}

			// filter by date
			if ( ( isset( $_REQUEST['to'] ) && ! empty( $_REQUEST['to'] ) ) || ( isset( $_REQUEST['from'] ) && ! empty( $_REQUEST['from'] ) ) ) {
				$from       = ! empty( $_REQUEST['from'] ) ? sanitize_text_field( $_REQUEST['from'] ) : '';
				$from_query = ! empty( $from ) ? date( 'Y-m-d 00:00:00', strtotime( $from ) ) : '';
				$to         = ! empty( $_REQUEST['to'] ) ? sanitize_text_field( $_REQUEST['to'] ) : '';
				$to_query   = ! empty( $to ) ? date( 'Y-m-d 23:59:59', strtotime( $to ) ) : '';
				$interval   = array();

				if ( $from_query ) {
					$interval['start_date'] = $from_query;
				}

				if ( $to_query ) {
					$interval['end_date'] = $to_query;
				}

				$query_args['interval'] = $interval;
				$filters_set            = true;
			}

			// count commissions, with filter, if any
			$clicks_count = YITH_WCAF_Click_Handler()->count_hits( array_merge(
				array(
					'user_id' => $user_id
				),
				$query_args
			) );

			// sets pagination filters
			$page_links = '';
			if ( $pagination == 'yes' && $clicks_count > 1 ) {

				$pages = ceil( $clicks_count / $per_page );

				if ( $current_page > $pages ) {
					$current_page = $pages;
				}

				$offset = ( $current_page - 1 ) * $per_page;

				if ( $pages > 1 ) {
					$page_links = paginate_links( array(
						'base'      => YITH_WCAF()->get_affiliate_dashboard_url( 'clicks', '%#%' ),
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

			$orderby = isset( $_REQUEST['orderby'] ) ? sanitize_text_field( $_REQUEST['orderby'] ) : 'click_date';
			$order   = isset( $_REQUEST['order'] ) ? sanitize_text_field( $_REQUEST['order'] ) : 'DESC';

			// retrieve clicks
			$clicks = YITH_WCAF_Click_Handler()->get_hits( array_merge(
				array(
					'user_id' => $user_id
				),
				$query_args,
				array(
					'orderby' => $orderby,
					'order'   => $order
				)
			) );

			$atts = array_merge(
				$atts,
				array(
					'user_id'               => $user_id,
					'user'                  => $user,
					'affiliate_id'          => $affiliate['ID'],
					'affiliate'             => $affiliate,
					'clicks'                => $clicks,
					'filter_set'            => $filters_set,
					'page_links'            => $page_links,
					'status'                => isset( $status ) ? $status : false,
					'from'                  => isset( $from ) ? $from : false,
					'to'                    => isset( $to ) ? $to : false,
					'dashboard_clicks_link' => YITH_WCAF()->get_affiliate_dashboard_url( 'clicks', 1 ),
					'ordered'               => $orderby,
					'to_order'              => $order == 'DESC' ? 'ASC' : 'DESC',
					'show_right_column'     => apply_filters( 'yith_wcaf_show_dashboard_links', 'yes' == $show_dashboard_links, 'dashboard_clicks' ),
					'dashboard_links'       => yith_wcaf_get_dashboard_navigation_menu(),
				)
			);

			$template_name = 'dashboard-clicks.php';

			ob_start();

			yith_wcaf_get_template( $template_name, $atts, 'shortcodes' );

			return ob_get_clean();
		}

		/**
		 * Print payments section of the dashboard
		 *
		 * @param $atts mixed Array of shortcode attributes
		 *
		 * @return string Shortcode content
		 * @since 1.0.0
		 */
		public static function affiliate_dashboard_payments( $atts = array() ) {
			/**
			 * @var $pagination           string (yes/no)
			 * @var $per_page             int (number of items to show in each page)
			 * @var $current_page         int (current page number)
			 * @var $show_dashboard_links string (yes/no)
			 */
			$defaults = array(
				'pagination'           => 'yes',
				'per_page'             => isset( $_REQUEST['per_page'] ) ? intval( wc_clean( $_REQUEST['per_page'] ) ) : 10,
				'current_page'         => max( 1, get_query_var( 'payments' ) ),
				'show_dashboard_links' => 'no'
			);

			$atts = shortcode_atts( $defaults, $atts );
			extract( $atts );

			if ( ! YITH_WCAF_Affiliate_Handler()->can_user_see_section( false, 'payments' ) ) {
				return '';
			}

			$user_id   = get_current_user_id();
			$user      = get_user_by( 'id', $user_id );
			$affiliate = YITH_WCAF_Affiliate_Handler()->get_affiliate_by_user_id( $user_id );

			// sets filters from query string params
			$filters_set = false;
			$query_args  = array();

			// filter by stauts
			if ( isset( $_REQUEST['status'] ) && in_array( $_REQUEST['status'], array(
					'on-hold',
					'pending',
					'completed'
				) ) ) {
				$status               = sanitize_text_field( $_REQUEST['status'] );
				$query_args['status'] = $status;
			}

			// filter by date
			if ( ( isset( $_REQUEST['to'] ) && ! empty( $_REQUEST['to'] ) ) || ( isset( $_REQUEST['from'] ) && ! empty( $_REQUEST['from'] ) ) ) {
				$from       = ! empty( $_REQUEST['from'] ) ? sanitize_text_field( $_REQUEST['from'] ) : '';
				$from_query = ! empty( $from ) ? date( 'Y-m-d 00:00:00', strtotime( $from ) ) : '';
				$to         = ! empty( $_REQUEST['to'] ) ? sanitize_text_field( $_REQUEST['to'] ) : '';
				$to_query   = ! empty( $to ) ? date( 'Y-m-d 23:59:59', strtotime( $to ) ) : '';
				$interval   = array();

				if ( $from_query ) {
					$interval['start_date'] = $from_query;
				}

				if ( $to_query ) {
					$interval['end_date'] = $to_query;
				}

				$query_args['interval'] = $interval;
				$filters_set            = true;
			}

			// count commissions, with filter, if any
			$payments_count = YITH_WCAF_Payment_Handler()->count_payments( array_merge(
				array(
					'user_id' => $user_id
				),
				$query_args
			) );

			// sets pagination filters
			$page_links = '';
			if ( $pagination == 'yes' && $payments_count > 1 ) {
				$pages = ceil( $payments_count / $per_page );

				if ( $current_page > $pages ) {
					$current_page = $pages;
				}

				$offset = ( $current_page - 1 ) * $per_page;

				if ( $pages > 1 ) {
					$page_links = paginate_links( array(
						'base'      => YITH_WCAF()->get_affiliate_dashboard_url( 'payments', '%#%' ),
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

			$orderby = isset( $_REQUEST['orderby'] ) ? sanitize_text_field( $_REQUEST['orderby'] ) : 'created_at';
			$order   = isset( $_REQUEST['order'] ) ? sanitize_text_field( $_REQUEST['order'] ) : 'DESC';

			// retrieve commissions
			$payments = YITH_WCAF_Payment_Handler()->get_payments( array_merge(
				array(
					'user_id' => $user_id
				),
				$query_args,
				array(
					'orderby' => $orderby,
					'order'   => $order
				)
			) );

			$template_name = 'dashboard-payments.php';

			$atts = array_merge(
				$atts,
				array(
					'user_id'                 => $user_id,
					'user'                    => $user,
					'affiliate_id'            => $affiliate['ID'],
					'affiliate'               => $affiliate,
					'payments'                => $payments,
					'filter_set'              => $filters_set,
					'page_links'              => $page_links,
					'status'                  => isset( $status ) ? $status : false,
					'from'                    => isset( $from ) ? $from : false,
					'to'                      => isset( $to ) ? $to : false,
					'show_invoice'            => get_option( 'yith_wcaf_payment_require_invoice', 'no' ),
					'dashboard_payments_link' => YITH_WCAF()->get_affiliate_dashboard_url( 'payments', 1 ),
					'ordered'                 => $orderby,
					'to_order'                => $order == 'DESC' ? 'ASC' : 'DESC',
					'show_right_column'       => apply_filters( 'yith_wcaf_show_dashboard_links', 'yes' == $show_dashboard_links, 'dashboard_payments' ),
					'dashboard_links'         => yith_wcaf_get_dashboard_navigation_menu(),
				)
			);

			ob_start();

			yith_wcaf_get_template( $template_name, $atts, 'shortcodes' );

			return ob_get_clean();
		}

		/**
		 * Print settings section of the dashboard
		 *
		 * @param $atts mixed Array of shortcode attributes
		 *
		 * @return string Shortcode content
		 * @since 1.0.0
		 */
		public static function affiliate_dashboard_settings( $atts = array() ) {
			/**
			 * @var $show_dashboard_links   string (yes/no)
			 * @var $show_additional_fields string (yes/no)
			 * @var $show_name_field        string (yes/no)
			 * @var $show_surname_field     string (yes/no)
			 */
			$defaults = array(
				'show_dashboard_links'   => 'no',
				'show_additional_fields' => get_option( 'yith_wcaf_referral_show_fields_on_settings', 'no' ),
				'show_name_field'        => get_option( 'yith_wcaf_referral_registration_show_name_field', 'no' ),
				'show_surname_field'     => get_option( 'yith_wcaf_referral_registration_show_surname_field', 'no' )
			);

			$atts = shortcode_atts( $defaults, $atts );
			extract( $atts );

			if ( ! YITH_WCAF_Affiliate_Handler()->can_user_see_section( false, 'settings' ) ) {
				return '';
			}

			$change        = false;
			$user_id       = get_current_user_id();
			$user          = get_user_by( 'id', $user_id );
			$affiliate     = YITH_WCAF_Affiliate_Handler()->get_affiliate_by_user_id( $user_id );
			$first_name    = ! empty( $_POST['first_name'] ) ? sanitize_text_field( $_POST['first_name'] ) : '';
			$last_name     = ! empty( $_POST['last_name'] ) ? sanitize_text_field( $_POST['last_name'] ) : '';
			$payment_email = isset( $affiliate['payment_email'] ) ? $affiliate['payment_email'] : false;

			if ( $show_additional_fields && ( $first_name || $last_name ) ) {
				update_user_meta( $user_id, 'first_name', $first_name );
				update_user_meta( $user_id, 'last_name', $last_name );

				$change = true;
			}

			if ( ! empty( $_REQUEST['payment_email'] ) && apply_filters( 'yith_wcaf_is_payment_email', is_email( $_REQUEST['payment_email'] ), $_REQUEST['payment_email'] ) ) {
				$payment_email = apply_filters( 'yith_wcaf_sanitized_payment_email', sanitize_email( $_REQUEST['payment_email'] ), $_REQUEST['payment_email'] );

				YITH_WCAF_Affiliate_Handler()->update( $affiliate['ID'], array( 'payment_email' => $payment_email ) );
				$change = true;
			}

			$change = apply_filters( 'yith_wcaf_save_affiliate_settings', $change, $user_id );

			if ( $change ) {
				wc_add_notice( __( 'Changes correctly saved!', 'yith-woocommerce-affiliates' ) );
			}

			$atts = apply_filters( 'yith_wcaf_affiliate_dashboard_settings_atts', array_merge(
				$atts,
				array(
					'user_id'           => $user_id,
					'user'              => $user,
					'affiliate_id'      => $affiliate['ID'],
					'affiliate'         => $affiliate,
					'payment_email'     => $payment_email,
					'show_right_column' => apply_filters( 'yith_wcaf_show_dashboard_links', 'yes' == $show_dashboard_links, 'dashboard_settings' ),
					'dashboard_links'   => yith_wcaf_get_dashboard_navigation_menu(),
				),
				$show_additional_fields ? array(
					'affiliate_name'    => $user->first_name,
					'affiliate_surname' => $user->last_name
				) : array()
			), $user );

			$template_name = 'dashboard-settings.php';

			ob_start();

			yith_wcaf_get_template( $template_name, $atts, 'shortcodes' );

			return ob_get_clean();
		}

		/**
		 * Print dashboard summary
		 *
		 * @param $atts mixed Array of shortcode attributes
		 *
		 * @return string Shortcode content
		 * @since 1.0.0
		 */
		public static function affiliate_dashboard_summary( $atts = array() ) {
			/**
			 * @var $show_commissions_summary string (yes/no)
			 * @var $number_of_commissions    int (how many commissions to show as preview)
			 * @var $show_clicks_summary      string (yes/no)
			 * @var $number_of_clicks         int (how many clicks to show as preview)
			 * @var $show_referral_stats      string (yes/no)
			 * @var $show_dashboard_links     string (yes/no)
			 */
			$defaults = array(
				'show_commissions_summary' => 'yes',
				'number_of_commissions'    => 3,
				'show_clicks_summary'      => 'yes',
				'number_of_clicks'         => 3,
				'show_referral_stats'      => 'yes',
				'show_dashboard_links'     => 'yes'
			);

			$atts = shortcode_atts( $defaults, $atts );
			extract( $atts );

			if ( ! is_user_logged_in() ) {
				return '';
			}

			$user_id   = get_current_user_id();
			$user      = get_user_by( 'id', $user_id );
			$affiliate = YITH_WCAF_Affiliate_Handler()->get_affiliate_by_user_id( $user_id );

			$commissions              = array();
			$show_commissions_summary = $show_commissions_summary == 'yes';
			if ( $show_commissions_summary ) {
				$commissions = YITH_WCAF_Commission_Handler()->get_commissions( array(
					'user_id'        => $user_id,
					'status__not_in' => 'trash',
					'order_by'       => 'created_at',
					'order'          => 'DESC',
					'limit'          => $number_of_commissions
				) );
			}

			$clicks              = array();
			$show_clicks_summary = $show_clicks_summary == 'yes' && YITH_WCAF_Click_Handler()->are_hits_registered();
			if ( $show_clicks_summary ) {
				$clicks = YITH_WCAF_Click_Handler()->get_hits( array(
					'user_id'  => $user_id,
					'order_by' => 'click_date',
					'order'    => 'DESC',
					'limit'    => $number_of_clicks
				) );
			}

			$referral_stats = array();
			if ( $show_referral_stats == 'yes' ) {
				$paid_commissions_number = YITH_WCAF_Commission_Handler()->count_commission( array(
					'user_id' => $user_id,
					'status'  => 'paid'
				) );
				$commissions_number      = YITH_WCAF_Commission_Handler()->count_commission( array( 'user_id' => $user_id ) );

				$referral_stats = array(
					'earnings'     => $affiliate['earnings'],
					'paid'         => $affiliate['paid'],
					'balance'      => $affiliate['balance'],
					'refunds'      => $affiliate['refunds'],
					'click'        => $affiliate['click'],
					'conv_rate'    => $affiliate['conv_rate'],
					'rate'         => YITH_WCAF_Rate_Handler()->get_rate( $affiliate['ID'] ),
					'paid_count'   => $paid_commissions_number,
					'unpaid_count' => $commissions_number - $paid_commissions_number
				);
			}

			$greeting_message = sprintf(
				__( 'Hello <strong>%1$s</strong> (not %1$s? <a href="%2$s">Sign out</a>).', 'yith-woocommerce-affiliates' ) . ' ',
				$user->display_name,
				wc_get_endpoint_url( 'customer-logout', '', wc_get_page_permalink( 'myaccount' ) )
			);

			$greeting_message .= apply_filters( 'yith_wcaf_dashboard_affiliate_message', sprintf( __( 'From your affiliate dashboard you can view your recent commissions and visits, consult your affiliate stats and <a href="%1$s">manage settings</a> for your profile', 'yith-woocommerce-affiliates' ),
				YITH_WCAF()->get_affiliate_dashboard_url( 'settings' )
			), $greeting_message );


			$greeting_message = apply_filters( 'yith_wcaf_dashboard_greeting_message', $greeting_message );

			$atts = array_merge(
				$atts,
				array(
					'user_id'                  => $user_id,
					'user'                     => $user,
					'affiliate_id'             => $affiliate['ID'],
					'affiliate'                => $affiliate,
					'commissions'              => $commissions,
					'clicks'                   => $clicks,
					'referral_stats'           => $referral_stats,
					'dashboard_links'          => yith_wcaf_get_dashboard_navigation_menu(),
					'greeting_message'         => $greeting_message,
					'show_commissions_summary' => $show_commissions_summary,
					'show_clicks_summary'      => $show_clicks_summary,
					'show_dashboard_links'     => $show_dashboard_links == 'yes',
					'show_left_column'         => $show_referral_stats == 'yes',
					'show_right_column'        => $show_dashboard_links == 'yes'
				)
			);

			$template_name = 'dashboard-summary.php';

			ob_start();

			yith_wcaf_get_template( $template_name, $atts, 'shortcodes' );

			return ob_get_clean();
		}

		/**
		 * Returns output for clicks shortcode
		 *
		 * @param $atts mixed Array of shortcodes attributes
		 *
		 * @return string Shortcode content
		 * @since 1.0.0
		 */
		public static function show_clicks( $atts = array() ) {
			return self::affiliate_dashboard_clicks( $atts );
		}

		/**
		 * Returns output for commisions shortcode
		 *
		 * @param $atts mixed Array of shortcodes attributes
		 *
		 * @return string Shortcode content
		 * @since 1.0.0
		 */
		public static function show_commissions( $atts = array() ) {
			return self::affiliate_dashboard_commissions( $atts );
		}

		/**
		 * Returns output for payments shortcode
		 *
		 * @param $atts mixed Array of shortcodes attributes
		 *
		 * @return string Shortcode content
		 * @since 1.0.0
		 */
		public static function show_payments( $atts = array() ) {
			return self::affiliate_dashboard_payments( $atts );
		}

		/**
		 * Returns output for settings shortcode
		 *
		 * @param $atts mixed Array of shortcodes attributes
		 *
		 * @return string Shortcode content
		 * @since 1.0.0
		 */
		public static function show_settings( $atts = array() ) {
			return self::affiliate_dashboard_settings( $atts );
		}

	}
}