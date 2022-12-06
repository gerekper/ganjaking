<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Shortcodes Class
 *
 * @class   YITH_WCMBS_Shortcodes
 * @package Yithemes
 * @since   1.0.0
 * @author  Yithemes
 */
class YITH_WCMBS_Shortcodes {

	/**
	 * Single instance of the class
	 *
	 * @var \YITH_WCMBS_Shortcodes
	 * @since 1.0.0
	 */
	protected static $_instance;

	/**
	 * Returns single instance of the class
	 *
	 * @return \YITH_WCMBS_Manager
	 * @since 1.0.0
	 */
	public static function get_instance() {
		return ! is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
	}

	/**
	 * Constructor
	 *
	 * @access public
	 * @since  1.0.0
	 */
	public function __construct() {
		if ( is_admin() ) {
			add_filter( 'yith_wcmbs_settings_admin_tabs', array( $this, 'add_shortcodes_tab' ) );
			add_action( 'yith_wcmbs_render_admin_shortcodes_tab', array( $this, 'render_shortcodes_tab' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		}

		/* Print WooCommerce Login form*/
		add_shortcode( 'loginform', array( $this, 'render_login_form' ) );

		/* Print link for protected media by ID of the media*/
		add_shortcode( 'protected_media', array( $this, 'render_protected_media_link' ) );

		/* Print membership protected links */
		add_shortcode( 'membership_protected_links', array( $this, 'render_protected_links' ) );

		/* Print content in base of membership */
		add_shortcode( 'membership_protected_content', array( $this, 'render_protected_content' ) );

		/* Print the list of items in a membership plan */
		add_shortcode( 'membership_items', array( $this, 'render_list_items_in_plan' ) );

		/* Print link for product download files */
		add_shortcode( 'membership_download_product_links', array( $this, 'render_membership_download_product_links' ) );

		/* Print membership history */
		add_shortcode( 'membership_history', array( $this, 'print_membership_history' ) );

		/* Print link for just-downloaded product files */
		add_shortcode( 'membership_downloaded_product_links', array( $this, 'render_membership_downloaded_product_links' ) );

		add_shortcode( 'yith_wcmbs_members_only_content_start', array( $this, 'render_yith_wcmbs_members_only_content_start' ) );

	}

	/**
	 * Add shortcode tab in admin tabs
	 *
	 * @param array $admin_tabs
	 *
	 * @return array
	 */
	public function add_shortcodes_tab( $admin_tabs ) {
		$admin_tabs['shortcodes'] = __( 'Shortcodes', 'yith-woocommerce-membership' );

		return $admin_tabs;
	}

	/**
	 * Render "Shortcodes" Tab
	 */
	public function render_shortcodes_tab() {
		wc_get_template( '/tabs/shortcodes.php', array(), YITH_WCMBS_TEMPLATE_PATH, YITH_WCMBS_TEMPLATE_PATH );
	}

	public function admin_enqueue_scripts() {
		$screen    = function_exists( 'get_current_screen' ) ? get_current_screen() : false;
		$screen_id = ! ! $screen ? $screen->id : false;
		wp_register_style( 'yith-wcmbs-admin-shortcodes-tab', YITH_WCMBS_ASSETS_URL . '/css/shortcodes-tab.css', array(), YITH_WCMBS_VERSION );

		if ( 'yith-plugins_page_yith_wcmbs_panel' === $screen_id && isset( $_GET['tab'] ) && 'shortcodes' === $_GET['tab'] ) {
			wp_enqueue_style( 'yith-wcmbs-admin-shortcodes-tab' );
		}

	}

	/**
	 * Render Login Form
	 * EXAMPLE:
	 * <code>
	 *  [loginform]
	 * </code>
	 * this code displays the WooCommerce Login Form
	 *
	 * @access   public
	 *
	 * @param      $atts array the attributes of shortcode
	 * @param null $content
	 *
	 * @return string
	 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
	 * @since    1.0.0
	 */
	public function render_login_form( $atts, $content = null ) {
		ob_start();
		if ( ! is_user_logged_in() ) {
			echo '<div class="woocommerce">';
			wc_get_template( 'myaccount/form-login.php' );
			echo '</div>';
		}

		return ob_get_clean();
	}


	/**
	 * Render Protected Media Link for downloading
	 * EXAMPLE:
	 * <code>
	 *  [protected_media id=237]Link Text[/protected_media]
	 * </code>
	 * this code displays a link for protected media download
	 *
	 * @access   public
	 *
	 * @param      $atts array the attributes of shortcode
	 * @param null $content
	 *
	 * @return string
	 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
	 * @since    1.0.0
	 */
	public function render_protected_media_link( $atts, $content = null ) {
		if ( ! empty( $atts['id'] ) && ! empty( $content ) ) {
			$user_id = get_current_user_id();
			$post_id = $atts['id'];

			$manager = YITH_WCMBS_Manager();
			if ( $manager->user_has_access_to_post( $user_id, $post_id ) ) {

				$link = add_query_arg( array( 'protected_media' => $post_id ), home_url( '/' ) );

				$html = "<a href='{$link}'>";
				$html .= $content;
				$html .= "</a>";

				return $html;
			}
		}

		return '';
	}


	/**
	 * Render Protected Links for downloading
	 *
	 * @access   public
	 *
	 * @param      $atts array the attributes of shortcode
	 * @param null $content
	 *
	 * @return string
	 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
	 * @since    1.0.0
	 */
	public function render_protected_links( $atts, $content = null ) {

		$default_atts = array(
			'post_id'    => 0,
			'link_class' => 'yith-wcmbs-download-button unlocked',
		);
		$atts         = wp_parse_args( $atts, $default_atts );
		$link_class   = $atts['link_class'];
		$post_id      = $atts['post_id'];

		if ( ! $post_id ) {
			global $post;
			if ( ! $post ) {
				return '';
			}

			$post_id = $post->ID;
		}

		$protected_links = yith_wcmbs_get_protected_links( $post_id );
		if ( $protected_links && is_array( $protected_links ) ) {
			$user_id         = get_current_user_id();
			$has_full_access = yith_wcmbs_has_full_access( $user_id );
			$html            = '';

			yith_wcmbs_late_enqueue_assets( 'membership' );

			foreach ( $protected_links as $index => $protected_link ) {
				$name       = $protected_link['name'];
				$membership = $protected_link['membership'];
				$has_access = $has_full_access;

				if ( ! $has_access ) {
					if ( ! ! $membership && is_array( $membership ) ) {
						$has_access = yith_wcmbs_user_has_membership( $user_id, $membership );
					} else {
						$has_access = yith_wcmbs_user_has_membership( $user_id );
					}
				}

				if ( $has_access ) {
					$link = add_query_arg( array( 'protected_link' => $index, 'of_post' => $post_id ), home_url( '/' ) );

					$html .= "<a class='{$link_class}' href='{$link}'>";
					$html .= $name;
					$html .= "</a>";
				}
			}

			return $html;
		}

		return '';
	}


	/**
	 * Render Protected Links for downloading
	 *
	 * @access   public
	 *
	 * @param      $atts array the attributes of shortcode
	 * @param null $content
	 *
	 * @return string
	 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
	 * @since    1.0.0
	 */
	public function render_protected_content( $atts, $content = null ) {
		if ( ! $content || apply_filters( 'yith_wcmbs_skip_render_protected_content_shortcode', false ) ) {
			return '';
		}

		$default_atts     = array(
			'plan_id'             => 0,
			'excluded_plan_id'    => 0,
			'user'                => 'member',
			'alternative_content' => '',
		);
		$atts             = wp_parse_args( $atts, $default_atts );
		$plan_id          = $atts['plan_id'];
		$excluded_plan_id = $atts['excluded_plan_id'];
		$user_type        = $atts['user'];

		switch ( $user_type ) {
			case 'guest':
				$has_access = ! is_user_logged_in();
				break;
			case 'logged':
				$has_access = is_user_logged_in();
				break;
			default:
				$user_id    = get_current_user_id();
				$has_access = yith_wcmbs_has_full_access( $user_id );

				if ( ! $has_access ) {
					if ( ! ! $plan_id ) {
						$ids        = explode( ',', $plan_id );
						$has_access = yith_wcmbs_user_has_membership( $user_id, $ids );
						if ( ! ! $excluded_plan_id ) {
							$ids = explode( ',', $excluded_plan_id );
							if ( yith_wcmbs_user_has_membership( $user_id, $ids ) && is_user_logged_in() ) {
								$has_access = false;
							}
						}
					} else {
						$has_access = yith_wcmbs_user_has_membership( $user_id );
					}
				}
				if ( 'non-member' === $user_type ) {
					$has_access = ! $has_access;
				}
		}

		if ( apply_filters( 'yith_wcmbs_has_access_to_protected_content', false ) || $has_access ) {
			return do_shortcode( $content );
		} elseif ( isset( $atts['alternative_content'] ) ) {
			return do_shortcode( $atts['alternative_content'] );
		}

		return '';
	}

	/**
	 * Render Product Link for downloading
	 * EXAMPLE:
	 * <code>
	 *  [membership_download_product_links class="btn btn-class"]Download[/membership_download_product_links]
	 * </code>
	 * this code displays a link for protected product download files
	 *
	 * @access   public
	 *
	 * @param array  $atts the attributes of shortcode
	 * @param string $content
	 *
	 * @return string
	 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
	 * @since    1.0.0
	 */
	public function render_membership_download_product_links( $atts, $content = null ) {
		wp_enqueue_script( 'yith_wcmbs_frontend_js' );

		$html         = '';
		$default_atts = array(
			'id'     => false,
			'class'  => 'yith-wcmbs-download-button',
			'layout' => 'buttons',
		);
		$atts         = wp_parse_args( $atts, $default_atts );

		// deprecated link_class param
		if ( isset( $atts['link_class'] ) ) {
			$atts['class'] = $atts['link_class'];
		}

		$id         = ! ! $atts['id'] ? absint( $atts['id'] ) : false;
		$class      = $atts['class'];
		$links      = YITH_WCMBS_Products_Manager()->get_download_links( array( 'return' => 'complete', 'id' => $id ) );
		$credits    = yith_wcmbs_get_product_credits( $id );
		$box_layout = 'box' === $atts['layout'];
		// translators: %s the number of credits
		$credits_text = sprintf( _n( '1 credit', '%s credits', $credits, 'yith-woocommerce-membership' ), $credits );

		do_action( 'yith_wcmbs_before_links_list' );

		if ( ! ! $links && ! empty( $links['links'] ) && ! empty( $links['download_info'] ) ) {
			$info                         = $links['download_info'];
			$can_download_without_credits = $info['can_download_without_credits'];
			$links_html                   = '';

			yith_wcmbs_late_enqueue_assets( 'membership' );

			if ( $info['credits_after'] < 0 ) {
				$links_html  = "<div class='yith-wcmbs-product-download-box__non-sufficient-credits'>";
				$links_html .= apply_filters( 'yith_wcmbs_membership_download_non_sufficient_credits_message', esc_html__( "You don't have enough credits to download this product!", 'yith-woocommerce-membership' ) );
				$links_html .= '</div>';
			} else {
				foreach ( $links['links'] as $link ) {
					$url       = $link['link'];
					$name      = ! empty( $content ) ? $content : $link['name'];
					$key       = $link['key'];
					$link_name = str_replace(' ', '', $link['name'] );
					$classes   = array( $class . ' ' . $link_name );
					$classes[] = $can_download_without_credits ? 'unlocked' : 'locked';

					$name = apply_filters( 'yith_wcmbs_shortcode_membership_download_product_links_name', $name, $link, $atts, $content );
					$url  = apply_filters( 'yith_wcmbs_shortcode_membership_download_product_links_link', $url, $link, $atts, $content );

					$name = "<span class='yith-wcmbs-download-button__name'>{$name}</span>";

					if ( ! $can_download_without_credits && ! $box_layout ) {
						$name .= "<span class='yith-wcmbs-download-button__credits'>{$credits_text}</span>";
					}

					$classes = implode( ' ', $classes );

					$links_html .= "<a class='{$classes}' href='{$url}' data-key='{$key}' data-product-id='{$id}'>{$name}</a>";
				}
			}

			if ( $box_layout ) {
				$info['links_html'] = $links_html;
				ob_start();
				wc_get_template( 'frontend/membership-product-download-box.php', $info, '', YITH_WCMBS_TEMPLATE_PATH . '/' );
				$html = ob_get_clean();
			} else {
				$html = $links_html;
			}

		}

		return $html;
	}


	/**
	 * Print the list of items in a membership plan
	 * EXAMPLE:
	 * <code>
	 *  [membership_items plan=237]
	 * </code>
	 * this code displays the list of items in the membership plan with ID = 237
	 *
	 * @access   public
	 *
	 * @param      $atts array the attributes of shortcode
	 * @param null $content
	 *
	 * @return string
	 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
	 * @since    1.0.0
	 */
	public function render_list_items_in_plan( $atts, $content = null ) {
		if ( ! empty( $atts['plan'] ) ) {
			$user_id = get_current_user_id();
			$plan_id = $atts['plan'];

			$plan       = false;
			$membership = false;

			if ( yith_wcmbs_has_full_access() ) {
				$plan = yith_wcmbs_get_plan( $plan_id );
			} else {
				$member = YITH_WCMBS_Members()->get_member( $user_id );
				if ( $member && $member->has_active_plan( $plan_id ) ) {
					$membership = $member->get_oldest_active_plan( $plan_id );
					$plan       = $membership->get_plan();
				}
			}


			if ( $plan ) {
				ob_start();
				$post_types = apply_filters( 'yith_wcmbs_render_list_items_post_type', array( 'post', 'page', 'product' ), $atts );
				foreach ( $post_types as $post_type ) {
					$page = 1;
					wc_get_template( '/membership/membership-plan-post-type-items.php', compact( 'plan', 'membership', 'post_type', 'page' ), '', YITH_WCMBS_TEMPLATE_PATH );
				}

				return ob_get_clean();
			}
		}

		return '';
	}

	/**
	 * Print the list of items in a membership plan
	 * EXAMPLE:
	 * <code>
	 *  [membership_history]
	 * </code>
	 * this code displays the history for all user memberships
	 * EXAMPLE 2:
	 * <code>
	 *  [membership_history id="123" title="Title"]
	 * </code>
	 * this code displays the history user membership with id 123
	 *
	 * @access   public
	 *
	 * @param      $atts array the attributes of shortcode
	 * @param null $content
	 *
	 * @return string
	 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
	 * @since    1.0.0
	 */
	public function print_membership_history( $atts, $content = null ) {
		$user_plans = array();
		$title      = isset( $atts['title'] ) ? $atts['title'] : '';

		$no_membership_message = '';

		if ( empty( $atts['id'] ) ) {
			// ALL MEMBERSHIPS
			$user_id = isset( $atts['user_id'] ) ? $atts['user_id'] : get_current_user_id();

			$member                          = new YITH_WCMBS_Member_Premium( $user_id );
			$membership_plans_status         = apply_filters( 'yith_wcmbs_membership_history_shortcode_membership_plans_status', 'any', $atts );
			$membership_plans_args           = array( 'status' => $membership_plans_status );
			$membership_plans_args           = apply_filters( 'yith_wcmbs_membership_history_shortcode_membership_plans_args', $membership_plans_args, $member );
			$membership_plans_args['return'] = 'complete';
			$user_plans                      = $member->get_membership_plans( $membership_plans_args );

			// filter all user membership in base of type (only memberships, only subscriptions)
			$type = isset( $atts['type'] ) ? $atts['type'] : '';
			switch ( $type ) {
				case 'membership':
					foreach ( $user_plans as $key => $membership ) {
						if ( $membership->has_subscription() ) {
							unset( $user_plans[ $key ] );
						}
					}
					$no_membership_message = __( 'You don\'t have any membership without a subscription plan yet.', 'yith-woocommerce-membership' );
					break;
				case 'subscription':
					foreach ( $user_plans as $key => $membership ) {
						if ( ! $membership->has_subscription() ) {
							unset( $user_plans[ $key ] );
						}
					}
					$no_membership_message = __( 'You don\'t have any membership with a subscription plan yet.', 'yith-woocommerce-membership' );
					break;
				default:
					$no_membership_message = __( 'You don\'t have any membership yet.', 'yith-woocommerce-membership' );
					break;
			}
		} else {
			$membership_id = $atts['id'];
			$membership    = yith_wcmbs_get_membership( $membership_id );
			$user_plans    = ( $membership->is_valid() && $membership->user_id == get_current_user_id() ) ? array( $membership ) : array();

			if ( empty( $user_plans ) ) {
				return '';
			}
		}

		$no_membership_message = apply_filters( 'yith_wcmbs_membership_history_shortcode_no_membership_message', $no_membership_message, $atts );

		$args = array(
			'user_plans'            => $user_plans,
			'title'                 => $title,
			'no_membership_message' => $no_membership_message,
		);
		ob_start();
		wc_get_template( '/membership/membership-plans.php', $args, '', YITH_WCMBS_TEMPLATE_PATH );

		return ob_get_clean();
	}


	/**
	 *
	 */
	public function render_membership_downloaded_product_links() {
		ob_start();
		wc_get_template( '/frontend/downloaded-product-links.php', array(), '', YITH_WCMBS_TEMPLATE_PATH );

		return ob_get_clean();
	}

	public function render_yith_wcmbs_members_only_content_start( $attrs = array() ) {
		$defaults = array(
			'hide-alternative-content' => 'no',
		);
		$attrs    = shortcode_atts( $defaults, $attrs, 'yith_wcmbs_members_only_content_start' );
		$tags     = array();

		if ( 'yes' !== $attrs['hide-alternative-content'] ) {
			$tags[] = '<!--yith_wcmbs_alternative_content-->';
		}

		$tags[] = '<!--yith_wcmbs_members_only_content_start-->';

		return implode( "\n", $tags );
	}

}

/**
 * Unique access to instance of YITH_WCMBS_Shortcodes class
 *
 * @return YITH_WCMBS_Shortcodes
 * @since 1.0.0
 */
function YITH_WCMBS_Shortcodes() {
	return YITH_WCMBS_Shortcodes::get_instance();
}
