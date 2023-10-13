<?php
/**
 * Shortcodes class
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\Wishlist\Classes
 * @version 3.0.0
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCWL_Shortcode' ) ) {
	/**
	 * YITH WCWL Shortcodes
	 *
	 * @since 1.0.0
	 */
	class YITH_WCWL_Shortcode {

		/**
		 * Init shortcodes available for the plugin
		 *
		 * @return void
		 */
		public static function init() {
			// register shortcodes.
			add_shortcode( 'yith_wcwl_wishlist', array( 'YITH_WCWL_Shortcode', 'wishlist' ) );
			add_shortcode( 'yith_wcwl_add_to_wishlist', array( 'YITH_WCWL_Shortcode', 'add_to_wishlist' ) );

			// register gutenberg blocks.
			add_action( 'init', array( 'YITH_WCWL_Shortcode', 'register_gutenberg_blocks' ) );
			add_action( 'yith_plugin_fw_gutenberg_before_do_shortcode', array( 'YITH_WCWL_Shortcode', 'fix_for_gutenberg_blocks' ), 10, 1 );

			// register elementor widgets.
			add_action( 'init', array( 'YITH_WCWL_Shortcode', 'init_elementor_widgets' ) );
		}

		/* === GUTENBERG BLOCKS === */

		/**
		 * Register available gutenberg blocks
		 *
		 * @return void
		 */
		public static function register_gutenberg_blocks() {
			$blocks = array(
				'yith-wcwl-add-to-wishlist' => array(
					'style'          => 'yith-wcwl-main',
					'script'         => 'jquery-yith-wcwl',
					'title'          => _x( 'YITH Add to wishlist', '[gutenberg]: block name', 'yith-woocommerce-wishlist' ),
					'description'    => _x( 'Shows Add to wishlist button', '[gutenberg]: block description', 'yith-woocommerce-wishlist' ),
					'shortcode_name' => 'yith_wcwl_add_to_wishlist',
					'attributes'     => array(
						'product_id'                => array(
							'type'    => 'text',
							'label'   => __( 'ID of the product to add to the wishlist (leave empty to use the global product)', 'yith-woocommerce-wishlist' ),
							'default' => '',
						),
						'wishlist_url'              => array(
							'type'    => 'text',
							'label'   => __( 'URL of the wishlist page (leave empty to use the default settings)', 'yith-woocommerce-wishlist' ),
							'default' => '',
						),
						'label'                     => array(
							'type'    => 'text',
							'label'   => __( 'Button label (leave empty to use the default settings)', 'yith-woocommerce-wishlist' ),
							'default' => '',
						),
						'browse_wishlist_text'      => array(
							'type'    => 'text',
							'label'   => __( '"Browse wishlist" label (leave empty to use the default settings)', 'yith-woocommerce-wishlist' ),
							'default' => '',
						),
						'already_in_wishslist_text' => array(
							'type'    => 'text',
							'label'   => __( '"Product already in wishlist" label (leave empty to use the default settings)', 'yith-woocommerce-wishlist' ),
							'default' => '',
						),
						'product_added_text'        => array(
							'type'    => 'text',
							'label'   => __( '"Product added to wishlist" label (leave empty to use the default settings)', 'yith-woocommerce-wishlist' ),
							'default' => '',
						),
						'icon'                      => array(
							'type'    => 'text',
							'label'   => __( 'Icon for the button (use any FontAwesome valid class, or leave empty to use the default settings)', 'yith-woocommerce-wishlist' ),
							'default' => '',
						),
						'link_classes'              => array(
							'type'    => 'text',
							'label'   => __( 'Additional CSS classes for the button (leave empty to use the default settings)', 'yith-woocommerce-wishlist' ),
							'default' => '',
						),
					),
				),
				'yith-wcwl-wishlist'        => array(
					'style'          => 'yith-wcwl-main',
					'script'         => 'jquery-yith-wcwl',
					'title'          => _x( 'YITH Wishlist', '[gutenberg]: block name', 'yith-woocommerce-wishlist' ),
					'description'    => _x( 'Shows a list of products in wishlist', '[gutenberg]: block description', 'yith-woocommerce-wishlist' ),
					'shortcode_name' => 'yith_wcwl_wishlist',
					'attributes'     => array(
						'pagination'  => array(
							'type'    => 'select',
							'label'   => __( 'Choose whether to paginate items in the wishlist or show them all', 'yith-woocommerce-wishlist' ),
							'default' => 'no',
							'options' => array(
								'yes' => __( 'Paginate', 'yith-woocommerce-wishlist' ),
								'no'  => __( 'Do not paginate', 'yith-woocommerce-wishlist' ),
							),
						),
						'per_page'    => array(
							'type'    => 'number',
							'label'   => __( 'Number of items to show per page', 'yith-woocommerce-wishlist' ),
							'default' => '5',
						),
						'wishlist_id' => array(
							'type'    => 'text',
							'label'   => __( 'ID of the wishlist to show (e.g. K6EOWXB888ZD)', 'yith-woocommerce-wishlist' ),
							'default' => '',
						),
					),
				),
			);

			yith_plugin_fw_gutenberg_add_blocks( $blocks );
		}

		/**
		 * Fix preview of Gutenberg blocks at backend
		 *
		 * @param string $shortcode Shortcode to render.
		 * @return void
		 */
		public static function fix_for_gutenberg_blocks( $shortcode ) {
			if ( strpos( $shortcode, '[yith_wcwl_add_to_wishlist' ) !== false ) {
				if ( strpos( $shortcode, 'product_id=""' ) !== false ) {
					$products = wc_get_products(
						array(
							'type'  => 'simple',
							'limit' => 1,
						)
					);

					if ( ! empty( $products ) ) {
						global $product;
						$product = array_shift( $products );
					}
				}
			}
		}

		/* === ELEMENTOR WIDGETS === */

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
			include_once YITH_WCWL_INC . 'widgets/elementor/class-yith-wcwl-elementor-add-to-wishlist.php';
			include_once YITH_WCWL_INC . 'widgets/elementor/class-yith-wcwl-elementor-wishlist.php';

			$register_widget_hook = version_compare( ELEMENTOR_VERSION, '3.5.0', '>=' ) ? 'elementor/widgets/register' : 'elementor/widgets/widgets_registered';

			// register widgets.
			add_action( $register_widget_hook, array( 'YITH_WCWL_Shortcode', 'register_elementor_widgets' ) );
		}

		/**
		 * Register Elementor Widgets
		 *
		 * @return void
		 */
		public static function register_elementor_widgets() {
			$widgets_manager = \Elementor\Plugin::instance()->widgets_manager;

			if ( is_callable( array( $widgets_manager, 'register' ) ) ) {
				$widgets_manager->register( new YITH_WCWL_Elementor_Add_To_Wishlist() );
				$widgets_manager->register( new YITH_WCWL_Elementor_Wishlist() );
			} else {
				$widgets_manager->register_widget_type( new YITH_WCWL_Elementor_Add_To_Wishlist() );
				$widgets_manager->register_widget_type( new YITH_WCWL_Elementor_Wishlist() );
			}
		}

		/* === SHORTCODES == */

		/**
		 * Print the wishlist HTML.
		 *
		 * @param array  $atts    Array of attributes for the shortcode.
		 * @param string $content Shortcode content (none expected).
		 * @return string Rendered shortcode
		 *
		 * @since 1.0.0
		 */
		public static function wishlist( $atts, $content = null ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
			global $yith_wcwl_is_wishlist, $yith_wcwl_wishlist_token;

			$atts = shortcode_atts(
				array(
					'per_page'        => 5,
					'current_page'    => 1,
					'pagination'      => 'no',
					'wishlist_id'     => get_query_var( 'wishlist_id', false ),
					'action_params'   => get_query_var( YITH_WCWL()->wishlist_param, false ),
					'no_interactions' => 'no',
					'layout'          => '',
				),
				$atts
			);

			/**
			 * Extracted variables:
			 *
			 * @var $per_page int
			 * @var $current_page int
			 * @var $pagination string
			 * @var $wishlist_id int
			 * @var $action_params array
			 * @var $no_interactions string
			 * @var $layout string
			 */
			extract( $atts ); // phpcs:ignore

			// retrieve options from query string.
			/**
			 * APPLY_FILTERS: yith_wcwl_current_wishlist_view_params
			 *
			 * Filter the array of parameters to see the current wishlist.
			 *
			 * @param array $params Array of parameters
			 *
			 * @return array
			 */
			$action_params = explode( '/', apply_filters( 'yith_wcwl_current_wishlist_view_params', $action_params ) );
			$action        = ( isset( $action_params[0] ) ) ? $action_params[0] : 'view';

			// retrieve options from db.
			$default_wishlist_title = get_option( 'yith_wcwl_wishlist_title' );
			$show_price             = 'yes' === get_option( 'yith_wcwl_price_show' );
			$show_stock             = 'yes' === get_option( 'yith_wcwl_stock_show' );
			$show_date_added        = 'yes' === get_option( 'yith_wcwl_show_dateadded' );
			$show_add_to_cart       = 'yes' === get_option( 'yith_wcwl_add_to_cart_show' );
			$show_remove_product    = 'yes' === get_option( 'yith_wcwl_show_remove', 'yes' );
			$show_variation         = 'yes' === get_option( 'yith_wcwl_variation_show' );
			$repeat_remove_button   = 'yes' === get_option( 'yith_wcwl_repeat_remove_button' );
			$add_to_cart_label      = get_option( 'yith_wcwl_add_to_cart_text', __( 'Add to cart', 'yith-woocommerce-wishlist' ) );
			$price_excluding_tax    = 'excl' === get_option( 'woocommerce_tax_display_cart' );
			$ajax_loading           = 'yes' === get_option( 'yith_wcwl_ajax_enable', 'no' );

			// icons.
			$icon = get_option( 'yith_wcwl_add_to_wishlist_icon' );

			if ( 'custom' === $icon ) {
				$custom_icon = get_option( 'yith_wcwl_add_to_wishlist_custom_icon' );

				/**
				 * APPLY_FILTERS: yith_wcwl_custom_icon_alt
				 *
				 * Filter the alternative text for the heading icon in the widget.
				 *
				 * @param string $text Alternative text
				 *
				 * @return string
				 */
				$custom_icon_alt = apply_filters( 'yith_wcwl_custom_icon_alt', '' );

				/**
				 * APPLY_FILTERS: yith_wcwl_custom_width
				 *
				 * Filter the width for the heading icon in the widget.
				 *
				 * @param string $width Icon width
				 *
				 * @return string
				 */
				$custom_icon_width = apply_filters( 'yith_wcwl_custom_width', '32' );

				$heading_icon = '<img src="' . esc_url( $custom_icon ) . '" alt="' . esc_attr( $custom_icon_alt ) . '" width="' . esc_attr( $custom_icon_width ) . '" />';
			} else {
				$heading_icon = ! empty( $icon ) ? '<i class="fa ' . esc_attr( $icon ) . '"></i>' : '';
			}

			// init params needed to load correct template.
			$template_part     = 'view';
			$no_interactions   = 'yes' === $no_interactions;
			$additional_params = array(
				// wishlist data.
				'wishlist'                 => false,
				'is_default'               => true, // @deprecated since 3.0.7
				'is_custom_list'           => false,
				'wishlist_token'           => '',
				'wishlist_id'              => false,
				'is_private'               => false,

				// wishlist items.
				'count'                    => 0,
				'wishlist_items'           => array(),

				// page data.
				'page_title'               => $default_wishlist_title,
				'default_wishlsit_title'   => $default_wishlist_title,
				'current_page'             => $current_page,
				'page_links'               => false,
				'layout'                   => $layout,

				// user data.
				'is_user_logged_in'        => is_user_logged_in(),
				'is_user_owner'            => true,
				'can_user_edit_title'      => false,

				// view data.
				'no_interactions'          => $no_interactions,
				'show_price'               => $show_price,
				'show_dateadded'           => $show_date_added,
				'show_stock_status'        => $show_stock,
				'show_add_to_cart'         => $show_add_to_cart && ! $no_interactions,
				'show_remove_product'      => $show_remove_product && ! $no_interactions,
				'add_to_cart_text'         => $add_to_cart_label,
				'show_ask_estimate_button' => false,
				'ask_estimate_url'         => '',
				'price_excl_tax'           => $price_excluding_tax,
				'show_cb'                  => false,
				'show_quantity'            => false,
				'show_variation'           => $show_variation,
				'show_price_variations'    => false,
				'show_update'              => false,
				'enable_drag_n_drop'       => false,
				'enable_add_all_to_cart'   => false,
				'move_to_another_wishlist' => false,
				'repeat_remove_button'     => $repeat_remove_button && ! $no_interactions,
				'show_last_column'         => $show_date_added || ( $show_add_to_cart && ! $no_interactions ) || ( $repeat_remove_button && ! $no_interactions ),

				// wishlist icon.
				'heading_icon'             => $heading_icon,

				// share data.
				'share_enabled'            => false,

				// template data.
				'template_part'            => $template_part,
				'additional_info'          => false,
				'available_multi_wishlist' => false,
				'users_wishlists'          => array(),
				'form_action'              => esc_url( YITH_WCWL()->get_wishlist_url( 'view' ) ),
			);

			$wishlist = YITH_WCWL_Wishlist_Factory::get_current_wishlist( $atts );

			if ( $wishlist && $wishlist->current_user_can( 'view' ) ) {
				// set global wishlist token.
				$yith_wcwl_wishlist_token = $wishlist->get_token();

				// retrieve wishlist params.
				$is_user_owner = $wishlist->is_current_user_owner();
				$count         = $wishlist->count_items();
				$offset        = 0;

				// sets current page, number of pages and element offset.
				$queried_page = get_query_var( 'paged' );
				$current_page = max( 1, $queried_page ? $queried_page : $current_page );

				// sets variables for pagination, if shortcode atts is set to yes.
				if ( 'yes' === $pagination && ! $no_interactions && $count > 1 ) {
					$pages = ceil( $count / $per_page );

					if ( $current_page > $pages ) {
						$current_page = $pages;
					}

					$offset = ( $current_page - 1 ) * $per_page;

					if ( $pages > 1 ) {
						$page_links = paginate_links(
							array(
								'base'     => esc_url( add_query_arg( array( 'paged' => '%#%' ), $wishlist->get_url() ) ),
								'format'   => '?paged=%#%',
								'current'  => $current_page,
								'total'    => $pages,
								'show_all' => true,
							)
						);
					}
				} else {
					$per_page = 0;
				}

				// retrieve items to print.
				$wishlist_items = $wishlist->get_items( $per_page, $offset );

				// retrieve wishlist information.
				$is_default     = $wishlist->get_is_default();
				$wishlist_token = $wishlist->get_token();
				$wishlist_title = $wishlist->get_formatted_name();

				$additional_params = wp_parse_args(
					array(
						// wishlist items.
						'count'                => $count,
						'wishlist_items'       => $wishlist_items,

						// wishlist data.
						'wishlist'             => $wishlist,
						'is_default'           => $is_default,
						'is_custom_list'       => $is_user_owner && ! $no_interactions, // @deprecated since 3.0.7
						'wishlist_token'       => $wishlist_token,
						'wishlist_id'          => $wishlist->get_id(),
						'is_private'           => $wishlist->has_privacy( 'private' ),
						'ajax_loading'         => $ajax_loading,

						// page data.
						'page_title'           => $wishlist_title,
						'current_page'         => $current_page,
						'page_links'           => isset( $page_links ) && ! $no_interactions ? $page_links : false,

						// user data.
						'is_user_owner'        => $is_user_owner,
						'can_user_edit_title'  => $wishlist->current_user_can( 'update_wishlist' ) && ! $no_interactions,

						// view data.
						'show_remove_product'  => $show_remove_product && $wishlist->current_user_can( 'remove_from_wishlist' ) && ! $no_interactions,
						'repeat_remove_button' => $repeat_remove_button && $wishlist->current_user_can( 'remove_from_wishlist' ) && ! $no_interactions,

						// template data.
						'form_action'          => $wishlist->get_url(),
					),
					$additional_params
				);

				// share options.
				$enable_share            = 'yes' === get_option( 'yith_wcwl_enable_share' ) && ! $wishlist->has_privacy( 'private' );
				$share_facebook_enabled  = 'yes' === get_option( 'yith_wcwl_share_fb' );
				$share_twitter_enabled   = 'yes' === get_option( 'yith_wcwl_share_twitter' );
				$share_pinterest_enabled = 'yes' === get_option( 'yith_wcwl_share_pinterest' );
				$share_email_enabled     = 'yes' === get_option( 'yith_wcwl_share_email' );
				$share_whatsapp_enabled  = 'yes' === get_option( 'yith_wcwl_share_whatsapp' );
				$share_url_enabled       = 'yes' === get_option( 'yith_wcwl_share_url' );

				// we want spaces to be encoded as + instead of %20, so we use urlencode instead of rawurlencode.
				// phpcs:disable WordPress.PHP.DiscouragedPHPFunctions.urlencode_urlencode
				if ( ! $no_interactions && $enable_share && ( $share_facebook_enabled || $share_twitter_enabled || $share_pinterest_enabled || $share_email_enabled || $share_whatsapp_enabled || $share_url_enabled ) ) {
					/**
					 * APPLY_FILTERS: yith_wcwl_socials_share_title
					 *
					 * Filter the title to share the wishlist on the different socials.
					 *
					 * @param string $share_title Share title
					 *
					 * @return string
					 */
					$share_title = apply_filters( 'yith_wcwl_socials_share_title', __( 'Share on:', 'yith-woocommerce-wishlist' ) );

					/**
					 * APPLY_FILTERS: yith_wcwl_shortcode_share_link_url
					 *
					 * Filter the wishlist URL to share.
					 *
					 * @param string             $share_link_url Share link URL
					 * @param YITH_WCWL_Wishlist $wishlist       Wishlist object
					 *
					 * @return string
					 */
					$share_link_url = apply_filters( 'yith_wcwl_shortcode_share_link_url', $wishlist->get_url(), $wishlist );

					/**
					 * APPLY_FILTERS: yith_wcwl_share_title
					 *
					 * Filter the title to share the wishlist.
					 *
					 * @param string             $share_title Share title
					 * @param YITH_WCWL_Wishlist $wishlist    Wishlist object
					 *
					 * @return string
					 */
					$share_link_title = apply_filters( 'yith_wcwl_share_title', urlencode( get_option( 'yith_wcwl_socials_title' ) ), $wishlist );
					$share_summary    = urlencode( str_replace( '%wishlist_url%', $share_link_url, get_option( 'yith_wcwl_socials_text' ) ) );

					$share_atts = array(
						'share_facebook_enabled'  => $share_facebook_enabled,
						'share_twitter_enabled'   => $share_twitter_enabled,
						'share_pinterest_enabled' => $share_pinterest_enabled,
						'share_email_enabled'     => $share_email_enabled,
						'share_whatsapp_enabled'  => $share_whatsapp_enabled,
						'share_url_enabled'       => $share_url_enabled,
						'share_title'             => $share_title,
						'share_link_url'          => $share_link_url,
						'share_link_title'        => $share_link_title,
					);

					if ( $share_facebook_enabled ) {
						$share_facebook_icon        = get_option( 'yith_wcwl_fb_button_icon', 'fa-facebook' );
						$share_facebook_custom_icon = get_option( 'yith_wcwl_fb_button_custom_icon' );

						if ( ! in_array( $share_facebook_icon, array( 'none', 'custom' ), true ) ) {
							$share_atts['share_facebook_icon'] = "<i class='fa {$share_facebook_icon}'></i>";
						} elseif ( 'custom' === $share_facebook_icon && $share_facebook_custom_icon ) {
							$alt_text                          = __( 'Share on Facebook', 'yith-woocommerce-wishlist' );
							$share_atts['share_facebook_icon'] = "<img src='{$share_facebook_custom_icon}' alt='{$alt_text}'/>";
						} else {
							$share_atts['share_facebook_icon'] = '';
						}
					}

					if ( $share_twitter_enabled ) {
						$share_twitter_summary     = urlencode( str_replace( '%wishlist_url%', '', get_option( 'yith_wcwl_socials_text' ) ) );
						$share_twitter_icon        = get_option( 'yith_wcwl_tw_button_icon', 'fa-twitter' );
						$share_twitter_custom_icon = get_option( 'yith_wcwl_tw_button_custom_icon' );

						$share_atts['share_twitter_summary'] = $share_twitter_summary;

						if ( ! in_array( $share_twitter_icon, array( 'none', 'custom' ), true ) ) {
							$share_atts['share_twitter_icon'] = "<i class='fa {$share_twitter_icon}'></i>";
						} elseif ( 'custom' === $share_twitter_icon && $share_twitter_custom_icon ) {
							$alt_text                         = __( 'Tweet on Twitter', 'yith-woocommerce-wishlist' );
							$share_atts['share_twitter_icon'] = "<img src='{$share_twitter_custom_icon}' alt='{$alt_text}'/>";
						} else {
							$share_atts['share_twitter_icon'] = '';
						}
					}

					if ( $share_pinterest_enabled ) {
						$share_image_url             = urlencode( get_option( 'yith_wcwl_socials_image_url' ) );
						$share_pinterest_icon        = get_option( 'yith_wcwl_pr_button_icon', 'fa-pinterest' );
						$share_pinterest_custom_icon = get_option( 'yith_wcwl_pr_button_custom_icon' );

						$share_atts['share_summary']   = $share_summary;
						$share_atts['share_image_url'] = $share_image_url;

						if ( ! in_array( $share_pinterest_icon, array( 'none', 'custom' ), true ) ) {
							$share_atts['share_pinterest_icon'] = "<i class='fa {$share_pinterest_icon}'></i>";
						} elseif ( 'custom' === $share_pinterest_icon && $share_pinterest_custom_icon ) {
							$alt_text                           = __( 'Pin on Pinterest', 'yith-woocommerce-wishlist' );
							$share_atts['share_pinterest_icon'] = "<img src='{$share_pinterest_custom_icon}' alt='{$alt_text}'/>";
						} else {
							$share_atts['share_pinterest_icon'] = '';
						}
					}

					if ( $share_email_enabled ) {
						$share_email_icon        = get_option( 'yith_wcwl_em_button_icon', 'fa-email' );
						$share_email_custom_icon = get_option( 'yith_wcwl_em_button_custom_icon' );

						if ( ! in_array( $share_email_icon, array( 'none', 'custom' ), true ) ) {
							$share_atts['share_email_icon'] = "<i class='fa {$share_email_icon}'></i>";
						} elseif ( 'custom' === $share_email_icon && $share_email_custom_icon ) {
							$alt_text                       = __( 'Share via email', 'yith-woocommerce-wishlist' );
							$share_atts['share_email_icon'] = "<img src='{$share_email_custom_icon}' alt='{$alt_text}'/>";
						} else {
							$share_atts['share_email_icon'] = '';
						}
					}

					if ( $share_whatsapp_enabled ) {
						$share_whatsapp_icon        = get_option( 'yith_wcwl_wa_button_icon', 'fa-whatsapp' );
						$share_whatsapp_custom_icon = get_option( 'yith_wcwl_wa_button_custom_icon' );
						$share_whatsapp_url         = '';

						if ( wp_is_mobile() ) {
							$share_whatsapp_url = 'whatsapp://send?text=' . $share_link_title . ' - ' . urlencode( $share_link_url );
						} else {
							$share_whatsapp_url = 'https://web.whatsapp.com/send?text=' . $share_link_title . ' - ' . urlencode( $share_link_url );
						}

						$share_atts['share_whatsapp_url'] = $share_whatsapp_url;

						if ( ! in_array( $share_whatsapp_icon, array( 'none', 'custom' ), true ) ) {
							$share_atts['share_whatsapp_icon'] = "<i class='fa {$share_whatsapp_icon}'></i>";
						} elseif ( 'custom' === $share_whatsapp_icon && $share_whatsapp_custom_icon ) {
							$alt_text                          = __( 'Share on WhatsApp', 'yith-woocommerce-wishlist' );
							$share_atts['share_whatsapp_icon'] = "<img src='{$share_whatsapp_custom_icon}' alt='{$alt_text}'/>";
						} else {
							$share_atts['share_whatsapp_icon'] = '';
						}
					}

					$additional_params['share_enabled'] = true;
					$additional_params['share_atts']    = $share_atts;
				}
				// phpcs:enable WordPress.PHP.DiscouragedPHPFunctions.urlencode_urlencode
			}

			// filter params.

			/**
			 * APPLY_FILTERS: yith_wcwl_wishlist_params
			 *
			 * Filter the array with the paramters in the wishlist page.
			 *
			 * @param array  $additional_params Wishlist parameters
			 * @param string $action            Action
			 * @param array  $action_params     Action parameters
			 * @param string $pagination        Use pagination or not
			 * @param int    $per_page          Number of items per page
			 * @param array  $atts              Array of attributes
			 *
			 * @return array
			 */
			$additional_params = apply_filters( 'yith_wcwl_wishlist_params', $additional_params, $action, $action_params, $pagination, $per_page, $atts );

			$atts = array_merge(
				$atts,
				$additional_params
			);

			$atts['fragment_options'] = YITH_WCWL_Frontend()->format_fragment_options( $atts, 'wishlist' );

			// apply filters for add to cart buttons.
			YITH_WCWL_Frontend()->alter_add_to_cart_button();

			// sets that we're in the wishlist template.
			$yith_wcwl_is_wishlist = true;

			$template = yith_wcwl_get_template( 'wishlist.php', $atts, true );

			// we're not in wishlist template anymore.
			$yith_wcwl_is_wishlist    = false;
			$yith_wcwl_wishlist_token = null;

			// remove filters for add to cart buttons.
			YITH_WCWL_Frontend()->restore_add_to_cart_button();

			// enqueue scripts.
			YITH_WCWL_Frontend()->enqueue_scripts();

			return apply_filters( 'yith_wcwl_wishlisth_html', $template, array(), true );
		}

		/**
		 * Return "Add to Wishlist" button.
		 *
		 * @param array  $atts Array of parameters for the shortcode.
		 * @param string $content Shortcode content (usually empty).
		 *
		 * @return string Template of the shortcode.
		 *
		 * @since 1.0.0
		 */
		public static function add_to_wishlist( $atts, $content = null ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
			global $post, $product;

			// product object.
			$current_product = ( isset( $atts['product_id'] ) ) ? wc_get_product( $atts['product_id'] ) : false;
			$current_product = $current_product ? $current_product : ( $product instanceof WC_Product ? $product : false );
			$current_product = $current_product ? $current_product : ( $post instanceof WP_Post ? wc_get_product( $post->ID ) : false );

			if ( ! $current_product || ! $current_product instanceof WC_Product ) {
				return '';
			}

			$current_product_id = yit_get_product_id( $current_product );

			// product parent.
			$current_product_parent = $current_product->get_parent_id();

			// labels & icons settings.
			$label_option        = get_option( 'yith_wcwl_add_to_wishlist_text' );
			$icon_option         = get_option( 'yith_wcwl_add_to_wishlist_icon' );
			$custom_icon         = 'none' !== $icon_option ? get_option( 'yith_wcwl_add_to_wishlist_custom_icon' ) : '';
			$custom_icon_alt     = apply_filters( 'yith_wcwl_custom_icon_alt', '' );
			$custom_icon_width   = apply_filters( 'yith_wcwl_custom_width', '32' );
			$added_icon_option   = get_option( 'yith_wcwl_added_to_wishlist_icon' );
			$custom_added_icon   = 'none' !== $added_icon_option ? get_option( 'yith_wcwl_added_to_wishlist_custom_icon' ) : '';
			$browse_wishlist     = get_option( 'yith_wcwl_browse_wishlist_text' );
			$already_in_wishlist = get_option( 'yith_wcwl_already_in_wishlist_text' );
			$product_added       = get_option( 'yith_wcwl_product_added_text' );
			$loop_position       = get_option( 'yith_wcwl_loop_position' );

			// button label.
			/**
			 * APPLY_FILTERS: yith_wcwl_button_label
			 *
			 * Filter the label of the 'Add to wishlist' button.
			 *
			 * @param string $label_option Button label
			 *
			 * @return string
			 */
			$label = apply_filters( 'yith_wcwl_button_label', $label_option );

			// button icon.
			/**
			 * APPLY_FILTERS: yith_wcwl_button_icon
			 *
			 * Filter the icon of the 'Add to wishlist' button.
			 *
			 * @param string $icon Button icon
			 *
			 * @return string
			 */
			$icon = apply_filters( 'yith_wcwl_button_icon', 'none' !== $icon_option ? $icon_option : '' );

			/**
			 * APPLY_FILTERS: yith_wcwl_button_added_icon
			 *
			 * Filter the added icon of the 'Add to wishlist' button.
			 *
			 * @param string $icon Button added icon
			 *
			 * @return string
			 */
			$added_icon = apply_filters( 'yith_wcwl_button_added_icon', 'none' !== $added_icon_option ? $added_icon_option : '' );

			// button class.
			$is_single         = isset( $atts['is_single'] ) ? $atts['is_single'] : yith_wcwl_is_single();
			$use_custom_button = get_option( 'yith_wcwl_add_to_wishlist_style' );

			/**
			 * APPLY_FILTERS: yith_wcwl_add_to_wishlist_button_classes
			 *
			 * Filter the CSS classes of the 'Add to wishlist' button.
			 *
			 * @param string $classes Button classes
			 *
			 * @return string
			 */
			$classes = apply_filters( 'yith_wcwl_add_to_wishlist_button_classes', in_array( $use_custom_button, array( 'button_custom', 'button_default' ), true ) ? 'add_to_wishlist single_add_to_wishlist button alt' : 'add_to_wishlist single_add_to_wishlist' );

			// check if product is already in wishlist.
			$exists                      = YITH_WCWL()->is_product_in_wishlist( $current_product_id );
			$added_to_wishlist_behaviour = get_option( 'yith_wcwl_after_add_to_wishlist_behaviour', 'view' );
			$container_classes           = $exists ? 'exists' : false;
			$found_in_list               = $exists ? yith_wcwl_get_wishlist( false ) : false;
			$found_item                  = $found_in_list ? $found_in_list->get_product( $current_product_id ) : false;

			$template_part = $exists && 'add' !== $added_to_wishlist_behaviour ? 'browse' : 'button';
			$template_part = isset( $atts['added_to_wishlist'] ) ? ( $atts['added_to_wishlist'] ? 'added' : 'browse' ) : $template_part;

			if ( $found_in_list && in_array( $template_part, array( 'browse', 'added' ), true ) && 'remove' === $added_to_wishlist_behaviour ) {
				$template_part = 'remove';
			}

			if ( 'remove' === $template_part ) {
				$classes = str_replace( array( 'single_add_to_wishlist', 'add_to_wishlist' ), '', $classes );

				/**
				 * APPLY_FILTERS: yith_wcwl_remove_from_wishlist_label
				 *
				 * Filter the label to remove from the wishlist.
				 *
				 * @param string $label Label
				 *
				 * @return string
				 */
				$label = apply_filters( 'yith_wcwl_remove_from_wishlist_label', __( 'Remove from list', 'yith-woocommerce-wishlist' ) );
			}

			// prevent button appearance when we're in loop, over image.
			if ( ! $is_single && 'before_image' === get_option( 'yith_wcwl_loop_position' ) ) {
				$classes = str_replace( 'button', '', $classes );
			}

			$ajax_loading = 'yes' === get_option( 'yith_wcwl_ajax_enable', 'no' );

			// get wishlist url.
			$wishlist_url = YITH_WCWL()->get_wishlist_url();

			// get product type.
			$product_type = $current_product->get_type();

			$additional_params = array(
				'base_url'                  => yith_wcwl_get_current_url(),
				'wishlist_url'              => $wishlist_url,
				'in_default_wishlist'       => $exists,
				'exists'                    => $exists,
				'container_classes'         => $container_classes,
				'is_single'                 => $is_single,
				'show_exists'               => false,
				'found_in_list'             => $found_in_list,
				'found_item'                => $found_item,
				'product_id'                => $current_product_id,
				'parent_product_id'         => $current_product_parent ? $current_product_parent : $current_product_id,
				'product_type'              => $product_type,
				'label'                     => $label,
				'show_view'                 => yith_wcwl_is_single(),
				/**
				 * APPLY_FILTERS: yith_wcwl_browse_wishlist_label
				 *
				 * Filter the label to browse the wishlist.
				 *
				 * @param string $browse_wishlist Browse wishlist text
				 *
				 * @return string
				 */
				'browse_wishlist_text'      => apply_filters( 'yith_wcwl_browse_wishlist_label', $browse_wishlist ),
				/**
				 * APPLY_FILTERS: yith_wcwl_product_already_in_wishlist_text_button
				 *
				 * Filter the text when the product is already in the wishlist.
				 *
				 * @param string $already_in_wishlist 'Already in wishlist' text
				 *
				 * @return string
				 */
				'already_in_wishslist_text' => apply_filters( 'yith_wcwl_product_already_in_wishlist_text_button', $already_in_wishlist ),
				/**
				 * APPLY_FILTERS: yith_wcwl_product_added_to_wishlist_message_button
				 *
				 * Filter the text when the product has been added to the wishlist.
				 *
				 * @param string $product_added 'Product added to the wishlist' text
				 *
				 * @return string
				 */
				'product_added_text'        => apply_filters( 'yith_wcwl_product_added_to_wishlist_message_button', $product_added ),
				'icon'                      => $icon,
				'heading_icon'              => $icon,
				'link_classes'              => $classes,
				'available_multi_wishlist'  => false,
				'disable_wishlist'          => false,
				'show_count'                => false,
				'ajax_loading'              => $ajax_loading,
				'loop_position'             => $loop_position,
				'template_part'             => $template_part,
			);
			// let third party developer filter options.

			/**
			 * APPLY_FILTERS: yith_wcwl_add_to_wishlist_params
			 *
			 * Filter the array with the paramters for the 'Add to wishlist' action.
			 *
			 * @param array  $additional_params Wishlist parameters
			 * @param array  $atts              Array of attributes
			 *
			 * @return array
			 */
			$additional_params = apply_filters( 'yith_wcwl_add_to_wishlist_params', $additional_params, $atts );

			$atts = shortcode_atts(
				$additional_params,
				is_array( $atts ) ? array_filter(
					$atts,
					function ( $item ) {
						return ! empty( $item ) || is_bool( $item );
					}
				) : array()
			);

			// add no-icon class when item is shown without icon.
			if ( $is_single && empty( $atts['icon'] ) ) {
				$atts['container_classes'] .= ' no-icon';
			}

			// add no-icon class when item is shown without icon.
			if ( $exists && $atts['show_count'] ) {
				$atts['container_classes'] .= ' with-count';
			}

			// set icon when missing, when on top of image (icon only, icon required).
			if ( ! $is_single && 'before_image' === get_option( 'yith_wcwl_loop_position' ) && ( ! $atts['icon'] || 'custom' === $atts['icon'] ) ) {
				if ( ! $atts['icon'] ) {
					$atts['icon'] = 'fa-heart-o';
				} elseif ( 'custom' === $atts['icon'] && ! $custom_icon && ! $custom_added_icon ) {
					$atts['icon'] = 'fa-heart-o';
				}
			}

			// change icon when item exists in wishlist.
			if ( $atts['exists'] ) {
				if ( $added_icon && ( 'custom' !== $added_icon || $custom_added_icon || $custom_icon ) ) {
					$atts['icon'] = $added_icon;
				} elseif ( strpos( $atts['icon'], '-o' ) !== false ) {
					$atts['icon'] = str_replace( '-o', '', $atts['icon'] );
				}
			}

			if ( 'custom' === $atts['icon'] && $atts['exists'] && $custom_added_icon ) {
				$icon_html         = '<img class="yith-wcwl-icon" src="' . esc_url( $custom_added_icon ) . '" alt="' . esc_attr( $custom_icon_alt ) . '" width="' . esc_attr( $custom_icon_width ) . '" />';
				$heading_icon_html = ! empty( $custom_icon ) ? '<img class="yith-wcwl-icon" src="' . esc_url( $custom_icon ) . '" alt="' . esc_attr( $custom_icon_alt ) . '" width="' . esc_attr( $custom_icon_width ) . '" />' : '';
			} elseif ( 'custom' === $atts['icon'] && $custom_icon ) {
				$icon_html         = '<img class="yith-wcwl-icon" src="' . esc_url( $custom_icon ) . '" alt="' . esc_attr( $custom_icon_alt ) . '" width="' . esc_attr( $custom_icon_width ) . '" />';
				$heading_icon_html = $icon_html;
			} elseif ( 'custom' !== $atts['icon'] ) {
				$icon_html         = ! empty( $atts['icon'] ) ? '<i class="yith-wcwl-icon fa ' . $atts['icon'] . '"></i>' : '';
				$heading_icon_html = ! empty( $atts['heading_icon'] ) ? '<i class="yith-wcwl-icon fa ' . $atts['heading_icon'] . '"></i>' : '';
			} else {
				$icon_html         = '';
				$heading_icon_html = '';
			}

			// set fragment options.
			$atts['fragment_options'] = YITH_WCWL_Frontend()->format_fragment_options( $atts, 'add_to_wishlist' );

			/**
			 * APPLY_FILTERS: yith_wcwl_add_to_wishlist_icon_html
			 *
			 * Filter the HTML of the icon for the 'Add to wishlist'.
			 *
			 * @param string $icon_html HTML icon
			 * @param array  $atts      Array of attributes
			 *
			 * @return string
			 */
			$atts['icon'] = apply_filters( 'yith_wcwl_add_to_wishlist_icon_html', $icon_html, $atts );

			/**
			 * APPLY_FILTERS: yith_wcwl_add_to_wishlist_heading_icon_html
			 *
			 * Filter the HTML of the heading icon for the 'Add to wishlist'.
			 *
			 * @param string $icon_html HTML icon
			 * @param array  $atts      Array of attributes
			 *
			 * @return string
			 */
			$atts['heading_icon'] = apply_filters( 'yith_wcwl_add_to_wishlist_heading_icon_html', $heading_icon_html, $atts );

			$template = yith_wcwl_get_template( 'add-to-wishlist.php', $atts, true );

			// enqueue scripts.
			YITH_WCWL_Frontend()->enqueue_scripts();

			/**
			 * APPLY_FILTERS: yith_wcwl_add_to_wishlisth_button_html
			 *
			 * Filter the HTML of the 'Add to wishlist' button.
			 *
			 * @param string $template     Button HTML
			 * @param string $wishlist_url Wishlist URL
			 * @param string $product_type Product type
			 * @param bool   $exists       Whether the product is already in the wishlist
			 * @param array  $atts         Array of attributes
			 *
			 * @return string
			 */
			return apply_filters( 'yith_wcwl_add_to_wishlisth_button_html', $template, $wishlist_url, $product_type, $exists, $atts );
		}
	}
}

YITH_WCWL_Shortcode::init();
