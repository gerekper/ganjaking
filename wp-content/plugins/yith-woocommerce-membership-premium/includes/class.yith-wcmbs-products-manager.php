<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Members Class
 *
 * @class   YITH_WCMBS_Products_Manager
 * @author  Yithemes
 * @since   1.0.0
 * @package Yithemes
 */
class YITH_WCMBS_Products_Manager {

	/**
	 * Single instance of the class
	 *
	 * @var \YITH_WCMBS_Products_Manager
	 * @since 1.0.0
	 */
	private static $_instance;

	private $_manage_products;

	/**
	 * Returns single instance of the class
	 *
	 * @return \YITH_WCMBS_Products_Manager
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
	private function __construct() {
		$this->_manage_products = yith_wcmbs_settings()->get_option( 'yith-wcmbs-products-in-membership-management' );
		if ( $this->is_allowed_download() ) {

			$this->handle_download_link_position();

			if ( isset( $_GET['protected_file'] ) && isset( $_GET['product_id'] ) ) {
				add_action( 'init', array( $this, 'download_protected_file' ), 999 );
			}

			add_action( 'yith_wcmbs_before_product_download', array( $this, 'check_if_has_credits_to_download' ), 10, 2 );

			if ( ! is_admin() ) {
				add_action( 'woocommerce_before_single_product_summary', array( $this, 'hide_price_and_add_to_cart' ) );
			}
		}
	}

	/**
	 * Handle the download link position
	 *
	 * @since 1.4.0
	 */
	private function handle_download_link_position() {
		$download_link_position = yith_wcmbs_settings()->get_option( 'yith-wcmbs-download-link-position' );
		switch ( $download_link_position ) {
			case 'tab':
				add_filter( 'woocommerce_product_tabs', array( $this, 'product_tabs' ) );
				break;
			case 'before_summary':
				add_action( 'woocommerce_before_single_product_summary', array( $this, 'print_download_link_html' ), 25 );
				break;
			case 'before_description':
				add_action( 'woocommerce_single_product_summary', array( $this, 'print_download_link_html' ), 15 );
				break;
			case 'after_description':
				add_action( 'woocommerce_single_product_summary', array( $this, 'print_download_link_html' ), 25 );
				break;
			case 'after_add_to_cart':
				add_action( 'woocommerce_single_product_summary', array( $this, 'print_download_link_html' ), 35 );
				break;
			case 'after_summary':
				add_action( 'woocommerce_after_single_product_summary', array( $this, 'print_download_link_html' ), 9 );
				break;
		}
	}

	public function hide_price_and_add_to_cart() {
		$hide_price_and_add_to_cart = 'yes' === yith_wcmbs_settings()->get_option( 'yith-wcmbs-hide-price-and-add-to-cart' );
		if ( $hide_price_and_add_to_cart ) {
			global $product;

			if ( $product ) {
				$downloadable    = yith_wcmbs_is_downloadable_product( $product );
				$base_product_id = yit_get_base_product_id( $product );

				if ( $downloadable && $this->user_has_access_to_product( get_current_user_id(), $base_product_id ) && ( apply_filters( 'yith_wcmb_skip_check_product_needs_credits_to_download', false ) || ! $this->product_needs_credits_to_download( get_current_user_id(), $base_product_id ) ) ) {

					$hook      = 'woocommerce_single_product_summary';
					$to_remove = array(
						'woocommerce_template_single_add_to_cart',
						'woocommerce_template_single_price',
					);

					foreach ( $to_remove as $callback ) {
						$priority = has_action( $hook, $callback );
						if ( false !== $priority ) {
							remove_action( $hook, $callback, $priority );
						}
					}

					do_action( 'yith_wcmbs_hide_price_and_add_to_cart' );
				}
			}
		}
	}


	/**
	 * check if user has credits to download product
	 *
	 * @param int        $user_id
	 * @param WC_Product $product
	 */
	public function check_if_has_credits_to_download( $user_id, $product ) {
		if ( yith_wcmbs_has_full_access( $user_id ) ) {
			return;
		}

		$member          = YITH_WCMBS_Members()->get_member( $user_id );
		$base_product_id = yit_get_base_product_id( $product );

		// If the member has already downloaded the product, he doesn't need credits to download the product!
		if ( $member->has_just_downloaded_product( $base_product_id ) ) {
			return;
		}

		$credits_for_product = yith_wcmbs_get_product_credits( $base_product_id );
		// If product has credits set to 0, the product doesn't need credits to be downloaded!
		if ( ! $credits_for_product ) {
			return;
		}

		$user_plans    = $member->get_membership_plans( array( 'return' => 'complete' ) );
		$product_plans = $this->product_is_in_plans( $base_product_id );

		$need_credits  = true;
		$credits_array = array();
		foreach ( $user_plans as $membership ) {
			$plan_ids = array_intersect( array_merge( array( $membership->plan_id ), $membership->get_linked_plans() ), $product_plans );
			if ( ! empty( $plan_ids ) ) {
				if ( ! $membership->has_credit_management() ) {
					$need_credits = false;
				} else {
					$credits_array[] = array(
						'membership' => $membership,
						'credits'    => $membership->get_remaining_credits(),
					);
				}
			}
		}

		if ( $need_credits ) {
			$has_credits = false;
			$max_credits = array();
			foreach ( $credits_array as $credit_array ) {
				$max_credits[] = absint( $credit_array['credits'] );
				if ( $credit_array['credits'] >= $credits_for_product ) {
					$has_credits = true;
					/** @var YITH_WCMBS_Membership $current_membership */
					$current_membership = $credit_array['membership'];
					$current_membership->remove_credit( $credits_for_product );
					break;
				}
			}
			if ( ! $has_credits ) {
				$max_credits = max( $max_credits );
				$alert       = esc_html__( 'You can\'t access to this content. You don\'t have enough credits.', 'yith-woocommerce-membership' );
				$alert       .= '<br />';
				$alert       .= esc_html( sprintf( _n( 'This product needs one credit,', 'This product needs %s credits,', $credits_for_product, 'yith-woocommerce-membership' ), $credits_for_product ) );
				$alert       .= esc_html( sprintf( _n( 'but you have only one credit!', 'but you have only %s credits!', $max_credits, 'yith-woocommerce-membership' ), $max_credits ) );
				$alert       = apply_filters( 'yith_wcmbs_not_enough_credits_message', $alert, $max_credits, $credits_for_product, $user_id, $product );
				wp_die( $alert, __( 'Restricted Access.', 'yith-woocommerce-membership' ) );
			}
		}
	}

	/**
	 * check if product needs credits to be downloaded by user
	 *
	 * @param $user_id
	 * @param $product_id
	 *
	 * @return bool
	 */
	public function product_needs_credits_to_download( $user_id, $product_id ) {
		$info = $this->get_user_download_product_info( $product_id, $user_id );

		return ! $info['can_download_without_credits'];
	}

	/**
	 * Get info about product download related to user memberships
	 *
	 * @param int      $product_id
	 * @param int|bool $user_id
	 *
	 * @return array
	 */
	public function get_user_download_product_info( $product_id, $user_id = false ) {
		$user_id = ! $user_id ? get_current_user_id() : $user_id;
		$info    = array(
			'can_download_without_credits' => false,
			'credits_before'               => 0,
			'credits_after'                => 0,
			'credits'                      => yith_wcmbs_get_product_credits( $product_id ),
		);

		if ( yith_wcmbs_has_full_access( $user_id ) || $info['credits'] <= 0 ) {
			$info['can_download_without_credits'] = true;
		}

		if ( ! $info['can_download_without_credits'] ) {
			$member = YITH_WCMBS_Members()->get_member( $user_id );
			if ( $member->has_just_downloaded_product( $product_id ) ) {
				$info['can_download_without_credits'] = true;
			} else {
				$product_plans = $this->product_is_in_plans( $product_id );
				$memberships   = $member->get_membership_plans( array( 'return' => 'complete' ) );

				$total_credits = 0;
				foreach ( $memberships as $membership ) {
					$plan_ids = array_intersect( array_merge( array( $membership->plan_id ), $membership->get_linked_plans() ), $product_plans );
					if ( ! empty( $plan_ids ) ) {
						if ( ! $membership->has_credit_management() ) {
							$info['can_download_without_credits'] = true;
							break;
						} else {
							$total_credits += $membership->get_remaining_credits();
						}
					}
				}

				if ( ! $info['can_download_without_credits'] ) {
					$info['credits_before'] = $total_credits;
					$info['credits_after']  = $info['credits_before'] - $info['credits'];
				}
			}
		}

		return $info;
	}

	/**
	 * Check if user has access to product. If user have access forces the file download
	 *
	 * @since 1.0.0
	 */
	public function download_protected_file() {
		$product_id         = $_GET['product_id'];
		$protected_file_key = $_GET['protected_file'];

		$user_id = get_current_user_id();

		$product = wc_get_product( $product_id );

		if ( $product && $product->is_type( 'variable' ) ) {
			$children = $product->get_children();
			if ( ! empty( $children ) ) {
				foreach ( $children as $child_id ) {
					$child = wc_get_product( $child_id );
					if ( $child && $child->has_file( $protected_file_key ) ) {
						$product = $child;
						break;
					}
				}
			}
		}

		if ( $product && $product->has_file( $protected_file_key ) && $this->user_has_access_to_product( $user_id, $product_id ) ) {
			do_action( 'yith_wcmbs_before_product_download', $user_id, $product );

			$file      = $product->get_file( $protected_file_key );
			$file_path = $file['file'];
			if ( strpos( $file_path, '[yith_wc_amazon_s3_storage' ) !== false ) {
				/**
				 * integration with amazon s3
				 *
				 * @author Daniel Sanchez
				 */
				$content = do_shortcode( $file['file'] );

				if ( $content != 'by_php' ) {
					echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}

				do_action( 'yith_wcmbs_add_download_report', $product->get_id(), $user_id );
				setcookie( "yith_wcmbs_downloading_{$protected_file_key}", 'yes', time() + 300 );

				exit();

			}
			// check if the file exist to prevent displaying the link of the file
			$check_externals = apply_filters( 'yith_wcmbs_check_if_external_file_exists', false );
			if ( ! $check_externals || $this->check_external_file_exists( $file_path ) ) {
				$base_product_id = yit_get_base_product_id( $product );

				do_action( 'yith_wcmbs_add_download_report', $base_product_id, $user_id );
				setcookie( "yith_wcmbs_downloading_{$protected_file_key}", 'yes', time() + 300 );
				WC_Download_Handler::download( $file_path, $base_product_id );
			} else {
				wp_die( esc_html__( 'Error while downloading file', 'yith-woocommerce-membership' ), esc_html__( 'Error', 'yith-woocommerce-membership' ) );
			}
		} else {
			wp_die( esc_html__( 'You can\'t access to this content', 'yith-woocommerce-membership' ), esc_html__( 'Restricted Access.', 'yith-woocommerce-membership' ) );
		}
	}

	/**
	 * check if an external file exist
	 *
	 * @param $file_path
	 *
	 * @return bool
	 */
	public function check_external_file_exists( $file_path ) {
		// curl_init requires PHP 4.0.2 or greater
		if ( ! function_exists( 'curl_init' ) ) {
			return true;
		}

		$ch = curl_init( $file_path );
		curl_setopt( $ch, CURLOPT_NOBODY, true );
		curl_exec( $ch );
		$retCode = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
		curl_close( $ch );

		return $retCode === 200;
	}

	/**
	 * return true if the option for Manage Products is allow_download
	 *
	 * @return string|void
	 */
	public function is_allowed_download() {
		return $this->_manage_products == 'allow_download';
	}

	/**
	 * add tabs to product
	 *
	 * @access   public
	 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
	 * @since    1.0.0
	 */
	public function product_tabs( $tabs ) {
		global $post;

		$product_plans = $this->product_is_in_plans( $post->ID );
		$product       = wc_get_product( $post->ID );

		if ( $product && ! empty( $product_plans ) ) {
			$downloadable = yith_wcmbs_is_downloadable_product( $product );

			if ( $downloadable && $this->user_has_access_to_product( get_current_user_id(), $post->ID ) ) {
				$tabs['yith-wcmbs-download'] = array(
					'title'      => __( 'Downloads', 'yith-woocommerce-membership' ),
					'priority'   => 99,
					'callback'   => array( $this, 'create_tab_content' ),
					'product_id' => $post->ID,
				);
			}
		}

		return $tabs;
	}


	/**
	 * Return true if user has access to product
	 *
	 * @param int $user_id    The user ID.
	 * @param int $product_id The product ID.
	 *
	 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
	 * @return bool
	 * @since    1.0.0
	 */
	public function user_has_access_to_product( $user_id, $product_id ) {
		$user_has_access = apply_filters( 'yith_wcmbs_user_has_access_to_product', false, $user_id, $product_id );
		if ( $user_has_access ) {
			return true;
		}

		if ( ! $this->is_allowed_download() ) {
			return YITH_WCMBS_Manager()->user_has_access_to_post( $user_id, $product_id );
		} else {
			$product_plans = $this->product_is_in_plans( $product_id );
			if ( yith_wcmbs_has_any_plan_with_all_posts_included( 'product' ) ) {
				$all_products_plans = yith_wcmbs_get_plans_including_all_posts( 'product' );
				$product_plans      = array_merge( $product_plans, $all_products_plans );
			}

			if ( $product_plans && ( yith_wcmbs_has_full_access( $user_id ) || yith_wcmbs_has_full_access_to_all_posts( 'product', $user_id ) ) ) {
				return true;
			}

			$member      = YITH_WCMBS_Members()->get_member( $user_id );
			$memberships = $member->get_membership_plans( array( 'return' => 'complete' ) );

			foreach ( $memberships as $membership ) {
				$plan_ids = array_intersect( array_merge( array( $membership->plan_id ), $membership->get_linked_plans() ), $product_plans );
				if ( ! ! $plan_ids && $membership->has_access_without_delay( $product_id ) ) {
					return true;
				}
			}

			return false;
		}
	}

	/**
	 * create the content of download tab
	 *
	 * @access   public
	 *
	 * @param string $key the key of the tab
	 * @param array  $tab array that contains info of tab (title, priority, callback, product_id)
	 *
	 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
	 * @since    1.0.0
	 */
	public function create_tab_content( $key, $tab ) {
		$this->print_download_link_html();
	}

	/**
	 * print the download link list for product in membership
	 *
	 * @access   public
	 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
	 * @since    1.0.0
	 *           *
	 */
	public function print_download_link_html() {
		global $post;
		if ( ! $this->user_has_access_to_product( get_current_user_id(), $post->ID ) ) {
			return;
		}

		echo do_shortcode( '[membership_download_product_links layout="box" id="' . $post->ID . '"]' );
	}

	public function get_download_links( $args = array() ) {
		$r          = array();
		$files      = array();
		$product_id = '';

		if ( $this->is_allowed_download() ) {
			global $post;

			$default_args = array(
				'return' => 'links', //link_name
				'id'     => false,
			);
			$args         = wp_parse_args( $args, $default_args );
			$return       = $args['return'];

			$user_id    = get_current_user_id();
			$product_id = $args['id'];

			if ( ! $product_id && $post instanceof WP_Post ) {
				$product_id = $post->ID;
			}

			if ( $product_id && $this->user_has_access_to_product( $user_id, $product_id ) ) {
				$product = wc_get_product( $product_id );
				if ( apply_filters( 'yith_wcmbs_skip_download_for_product', false, $product_id ) ) {
					return;
				}
				if ( $product ) {
					if ( ! $product->is_type( 'variable' ) ) {
						if ( $product->is_downloadable() ) {
							$files = $product->get_downloads();
						}
					} else {
						$variations = $product->get_children();
						if ( ! empty( $variations ) ) {
							foreach ( $variations as $variation ) {
								$p = wc_get_product( $variation );
								if ( $p->is_downloadable() ) {
									$files = array_merge( $files, $p->get_downloads() );
								}
							}
						}
					}
				}

				$download_info = $this->get_user_download_product_info( $product_id, $user_id );
				$unlocked      = $download_info['can_download_without_credits'];
				$links         = array();

				if ( ! empty( $files ) ) {
					foreach ( $files as $key => $file ) {
						$link = add_query_arg( array( 'protected_file' => $key, 'product_id' => $product_id ), home_url( '/' ) );
						$name = ! empty( $link_title ) ? $link_title : $file['name'];
						switch ( $return ) {
							case 'links':
								$links[] = $link;
								break;
							case 'links_names':
							case 'complete':
								$links[] = array(
									'link'     => $link,
									'name'     => $name,
									'unlocked' => $unlocked,
									'key'      => $key,
								);
								break;
						}
					}
				}
				if ( 'complete' === $return ) {
					$r = array(
						'links'         => $links,
						'download_info' => $download_info,
					);
				} else {
					$r = $links;
				}
			}
		}

		return apply_filters( 'yith_wcmbs_get_product_download_links', $r, $args, $files, $product_id );
	}

	/**
	 * get a list of plan ids that have a product
	 *
	 * @param int $product_id the id of the product
	 *
	 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
	 * @return array
	 * @access   public
	 * @since    1.0.0
	 */
	public function product_is_in_plans( $product_id ) {
		$product = wc_get_product( $product_id );
		if ( ! $product ) {
			return array();
		}

		$plan_ids = array();

		$restrict_access_plan = yith_wcmbs_get_plans_meta_for_post( $product_id );
		if ( ! empty( $restrict_access_plan ) ) {
			$plan_ids = $restrict_access_plan;
		}

		$prod_cats_plans_array = array();
		$prod_tags_plans_array = array();
		$plans_info            = YITH_WCMBS_Manager()->get_plans_info_array();;
		extract( $plans_info );

		// FILTER PRODUCT CATS AND TAGS IN PLANS
		if ( ! empty( $prod_cats_plans_array ) ) {
			//$this_product_cats = wp_get_post_terms( $product_id, 'product_cat', array( 'fields' => 'ids' ) );
			$this_product_cats = yith_wcmbs_get_post_term_ids( $product_id, 'product_cat', array(), true );
			foreach ( $prod_cats_plans_array as $cat_id => $c_plan_ids ) {
				if ( ! empty( $c_plan_ids ) && in_array( $cat_id, (array) $this_product_cats ) ) {
					$plan_ids = array_merge( $plan_ids, $c_plan_ids );
				}
			}
		}
		if ( ! empty( $prod_tags_plans_array ) ) {
			$this_product_tags = wp_get_post_terms( $product_id, 'product_tag', array( 'fields' => 'ids' ) );
			foreach ( $prod_tags_plans_array as $tag_id => $t_plan_ids ) {
				if ( ! empty( $t_plan_ids ) && in_array( $tag_id, (array) $this_product_tags ) ) {
					$plan_ids = array_merge( $plan_ids, $t_plan_ids );
				}
			}
		}

		foreach ( $plan_ids as $key => $plan_id ) {
			$allowed           = YITH_WCMBS_Manager()->exclude_hidden_items( array( $product_id ), $plan_id );
			$is_hidden_in_plan = empty( $allowed );
			if ( $is_hidden_in_plan ) {
				unset( $plan_ids[ $key ] );
			}
		}

		$plan_ids = array_merge( $plan_ids, yith_wcmbs_get_plans_including_all_posts( 'product' ) );

		return apply_filters( 'yith_wcmbs_product_is_in_plans', array_unique( $plan_ids ), $product_id );
	}


	/**
	 * Add Credits field in product options metabox
	 *
	 * @deprecated 1.4.0 | shown in "Membership options" metabox
	 */
	public function add_credits_field_in_products() {
	}

	/**
	 * Save custom Credits for products
	 *
	 * @param $post_id
	 *
	 * @deprecated 1.4.0 | saved through "Membership options" metabox
	 */
	public function save_credits_field_for_products( $post_id ) {
	}

}

/**
 * Unique access to instance of YITH_WCMBS_Products_Manager class
 *
 * @return YITH_WCMBS_Products_Manager
 * @since 1.0.0
 */
function YITH_WCMBS_Products_Manager() {
	return YITH_WCMBS_Products_Manager::get_instance();
}