<?php
/**
 * WooCommerce Memberships
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Memberships to newer
 * versions in the future. If you wish to customize WooCommerce Memberships for your
 * needs please refer to https://docs.woocommerce.com/document/woocommerce-memberships/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2014-2022, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Memberships\Restrictions;

use SkyVerge\WooCommerce\PluginFramework\v5_10_13 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Handler responsible for restricting products access (or ability to purchase).
 *
 * @since 1.9.0
 */
class Products {


	/** @var string set a temporary post password to protect products */
	private $product_restriction_password = '';

	/** @var bool helper to determine whether the thumbnail was removed from a product */
	private $product_thumbnail_restricted = false;

	/** @var array helper to determine whether cart items have member discounts */
	private $cart_items_with_member_discounts = array();

	/** @var int[] memoization of product IDs that have been processed for restriction */
	private $product_restricted = array();

	/** @var array memoization for products purchasable status (associative array of product IDs => purchasable status) */
	private $product_purchasable = array();

	/** @var array memoization of variations parent products (associative array of variation IDs => parent product object */
	private $parent_product = array();

	/** @var array memoized related product IDs by member ID, parent product and query args */
	private $related_products = array();


	/**
	 * Handles product restrictions.
	 *
	 * Products may have restricted access, or just purchase is restricted, to non members/logged out users or members who do not have access yet (delayed).
	 * We might need to display notices to non logged in users that their cart items may have discounts for members.
	 *
	 * @since 1.9.0
	 */
	public function __construct() {

		// restrict product visibility by hijacking WooCommerce product password protection (hide_content restriction mode)
		add_action( 'woocommerce_before_single_product',     array( $this, 'password_protect_restricted_product' ), 999 );
		add_filter( 'protected_title_format',                array( $this, 'restore_restricted_product_title' ), 999, 2 );
		// restrict product visibility of products flagged with a password set through the hook above
		add_filter( 'woocommerce_product_is_visible',        array( $this, 'product_is_visible' ), 999, 2 );
		add_filter( 'woocommerce_variation_is_visible',      array( $this, 'variation_is_visible' ), 999, 3 );
		add_filter( 'woocommerce_hide_invisible_variations', array( $this, 'hide_invisible_variations' ), 999, 3 );
		// restrict product purchasing
		add_filter( 'woocommerce_is_purchasable',            array( $this, 'product_is_purchasable' ), 999, 2 );
		add_filter( 'woocommerce_variation_is_purchasable',  array( $this, 'variation_is_purchasable' ), 999, 2 );

		// hide prices & thumbnails for products restricted from viewing
		add_filter( 'woocommerce_get_price_html',              array( $this, 'hide_restricted_product_price' ), 999, 2 );
		add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'remove_product_thumbnail' ), 5 );
		add_action( 'woocommerce_after_shop_loop_item_title',  array( $this, 'restore_product_thumbnail' ), 5 );

		// display single product purchasing restriction message
		add_action( 'woocommerce_single_product_summary',   array( $this, 'display_product_purchasing_discount_message' ), 30 );

		// display single product purchasing discount message
		add_action( 'woocommerce_single_product_summary',   array( $this, 'display_product_purchasing_restricted_message' ), 31 );

		// display optional member login message on "Cart" page
		add_action( 'wp',                               array( $this, 'display_cart_member_login_notice' ) );
		// display optional member login message on "Checkout" or "Thank you" pages
		add_action( 'woocommerce_before_template_part', array( $this, 'display_checkout_member_login_notice' ) );

		// since WC 3.0 WP_Query is no longer used to get related products for an item, so we need to handle those specifically
		add_filter( 'woocommerce_related_products',              array( $this, 'restrict_related_products' ), 1, 3 );
		add_filter( 'woocommerce_get_related_product_cat_terms', array( $this, 'restrict_related_terms' ), 1 );

		// WooCommerce core shortcodes often use direct SQL queries to retrieve products to display: we need to ensure restricted products are not shown
		add_filter( 'woocommerce_shortcode_products_query', array( $this, 'restrict_shortcode_products_query_results' ), 999, 3 );

		// remove restricted product categories from being listed in product categories widget
		add_filter( 'woocommerce_product_categories_widget_args',    array( $this, 'hide_widget_product_categories' ), 1 );
		add_filter( 'wc_product_dropdown_categories_get_terms_args', array( $this, 'hide_widget_product_categories' ), 1 );

		// the shop page, when restricted, needs special handling as it also happens to be the product post type archive
		add_action( 'wp', array( $this, 'restrict_shop_page' ) );
	}


	/**
	 * Restricts the shop page when "Hide completely" or "Redirect" restriction modes are used.
	 *
	 * Since the shop page is also the product archive, hiding completely the page doesn't remove the archive as well.
	 * Since the query has already been set for us to check if we are on the right page, we manually set the query to 404 and redirect to the 404 template when hiding completely is the restriction mode.
	 *
	 * @internal
	 *
	 * @since 1.9.0
	 */
	public function restrict_shop_page() {
		global $wp_query;

		$shop_page_id = wc_get_page_id( 'shop' );

		if ( $shop_page_id > 0 && ( is_page( $shop_page_id ) || is_post_type_archive( 'product' ) ) ) {

			$restrictions = wc_memberships()->get_restrictions_instance();

			// Restrict the shop page if:
			// - the shop page is directly the target of a plan rule and the user has no access
			// - all products are restricted from catalog and hide completely is the restriction mode OR redirect is the restriction mode but excerpts are not shown
			if (    ! current_user_can( 'wc_memberships_view_restricted_post_content', $shop_page_id )
			     ||   ( ! current_user_can( 'wc_memberships_view_restricted_post_type', 'product' ) && $restrictions->hiding_restricted_products() && ( $restrictions->is_restriction_mode( 'hide' ) || ( $restrictions->is_restriction_mode( 'redirect' ) && ! $restrictions->showing_excerpts() ) ) ) ) {

				switch ( $restrictions->get_restriction_mode() ) {

					case 'hide' :

						if ( $wp_query ) {

							$wp_query->set_404();

							status_header( 404 );

							get_template_part( 404 );

							exit;
						}

					break;

					case 'redirect' :

						// addresses a possible interaction with WC AJAX when the home page is restricted
						if (    isset( $_GET['wc-ajax'] )
						     && defined( 'DOING_AJAX' )
						     && DOING_AJAX
						     && has_action( 'wp_ajax_nopriv_woocommerce_' . $_GET['wc-ajax'] ) ) {

							return;
						}

						wp_redirect( $restrictions->get_restricted_content_redirect_url( $shop_page_id, 'post_type', 'page' ) );
						exit;

					break;
				}
			}
		}
	}


	/**
	 * Hides the price if a product is view-restricted in "hide content" mode.
	 *
	 * @internal
	 *
	 * @since 1.9.0
	 *
	 * @param string $price price label (may contain HTML)
	 * @param \WC_Product $product product being restricted
	 * @return string maybe the price to be shown (may contain HTML) or empty string if user can't see it
	 */
	public function hide_restricted_product_price( $price, \WC_Product $product ) {

		// bail out if user has not capability to view the restricted product (and thus its price)
		if (    ! current_user_can( 'wc_memberships_view_restricted_product', $product->get_id() )
		     &&   wc_memberships()->get_restrictions_instance()->is_restriction_mode( 'hide_content' ) ) {

			$price = '';
		}

		return $price;
	}


	/**
	 * Removes the product thumbnail in "hide content" mode.
	 *
	 * @internal
	 *
	 * @since 1.9.0
	 */
	public function remove_product_thumbnail() {
		global $post;

		$this->product_thumbnail_restricted = false;

		// skip if the product thumbnail is not shown anyway
		if ( ! has_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail' ) ) {
			return;
		}

		// if in hide content mode and current user is not allowed to see the product thumbnail, remove it from output
		if ( ( ! current_user_can( 'wc_memberships_view_restricted_product', $post->ID ) || ! current_user_can( 'wc_memberships_view_delayed_product', $post->ID ) ) && wc_memberships()->get_restrictions_instance()->is_restriction_mode( 'hide_content' ) ) {

			// flag that we removed the product thumbnail
			$this->product_thumbnail_restricted = true;

			// remove the product thumbnail and replace it with a placeholder image
			remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail' );
			add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'template_loop_product_thumbnail_placeholder' ), 10 );
		}
	}


	/**
	 * Re-enables the product thumbnail for the next product in the loop.
	 *
	 * @internal
	 *
	 * @since 1.9.0
	 */
	public function restore_product_thumbnail() {

		if ( $this->product_thumbnail_restricted && ! has_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail' ) ) {
			add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
			remove_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'template_loop_product_thumbnail_placeholder' ) );
		}
	}


	/**
	 * Outputs the product image placeholder in shop loop.
	 *
	 * @internal
	 *
	 * @since 1.9.0
	 */
	public function template_loop_product_thumbnail_placeholder() {

		if ( wc_placeholder_img_src() ) {

			echo wc_placeholder_img( 'shop_catalog' );
		}
	}


	/**
	 * Maybe password-protects a product page.
	 *
	 * WordPress & WooCommerce give us very few opportunities to restrict product viewing, so we hijack the password protection to achieve that.
	 *
	 * @see \WC_Memberships_Products_Restrictions::restrict_product_content()
	 *
	 * @internal
	 *
	 * @since 1.9.0
	 */
	public function password_protect_restricted_product() {
		global $post;

		// if the product is to be restricted, and doesn't already have a password, set a password so as to perform the actions we want
		if ( $post && wc_memberships_is_product_viewing_restricted( $post ) && ! post_password_required() ) {

			if ( ! current_user_can( 'wc_memberships_view_restricted_product', $post->ID ) ) {

				$this->product_restricted[] = $post->ID;
				$post->post_password        = $this->product_restriction_password = uniqid( 'wc_memberships_restricted_', false );

				add_filter( 'the_password_form', array( $this, 'restrict_product_content' ) );

			} elseif ( ! current_user_can( 'wc_memberships_view_delayed_product', $post->ID ) ) {

				$this->product_restricted[] = $post->ID;
				$post->post_password        = $this->product_restriction_password = uniqid( 'wc_memberships_delayed_', false );

				add_filter( 'the_password_form', array( $this, 'restrict_product_content' ) );
			}
		}
	}


	/**
	 * Restores the title of products that have been password protected to handle restriction.
	 *
	 * @internal
	 *
	 * @since 1.9.0
	 *
	 * @param string $title_format the title format used in protected and private post titles
	 * @param \WP_Post $post
	 * @return string
	 */
	public function restore_restricted_product_title( $title_format, $post ) {
		return in_array( $post->ID, $this->product_restricted, false ) ? '%s' : $title_format;
	}


	/**
	 * Restricts the product content.
	 *
	 * @internal
	 * @see \WC_Memberships_Products_Restrictions::password_protect_restricted_product()
	 * @see \WC_Memberships_Posts_Restrictions::restrict_post()
	 *
	 * @since 1.9.0
	 *
	 * @param string $content the content being restricted
	 * @return string HTML
	 */
	public function restrict_product_content( $content ) {
		global $post;

		if ( $post && ! empty( $this->product_restriction_password ) && $this->product_restriction_password === $post->post_password ) {

			$message_code = null;

			// user does not have access, filter the content
			if ( strpos( $post->post_password, 'wc_memberships_restricted_' ) !== false ) {
				$message_code = 'product_viewing_restricted';
			} elseif ( strpos( $post->post_password, 'wc_memberships_delayed_' ) !== false ) {
				$message_code = 'product_access_delayed';
			}

			if ( null !== $message_code ) {

				$args = array(
					'post'         => $post,
					'message_type' => 'product_access_delayed' === $message_code ? 'delayed' : 'restricted',
				);

				if (    'product_access_delayed' === $message_code
				     && ( $access_time = wc_memberships()->get_capabilities_instance()->get_user_access_start_time_for_post( get_current_user_id(), $post->ID ) ) ) {

					$args['access_time'] = $access_time;
				}

				$message_code = \WC_Memberships_User_Messages::get_message_code_shorthand_by_post_type( $post, $args );
				$content      = \WC_Memberships_User_Messages::get_message_html( $message_code, $args );

				$post->post_password = null;
			}
		}

		return $content;
	}


	/**
	 * Checks whether a product can be purchased based on current user capabilities.
	 *
	 * @since 1.9.4
	 *
	 * @param \WC_Product $product the product
	 * @return bool
	 */
	private function product_can_be_purchased( $product ) {

		$product_id  = $product->get_id();
		$purchasable = true;

		if (    ! current_user_can( 'wc_memberships_view_restricted_product',     $product_id )
		     || ! current_user_can( 'wc_memberships_view_delayed_product',        $product_id )
		     || ! current_user_can( 'wc_memberships_purchase_restricted_product', $product_id )
		     || ! current_user_can( 'wc_memberships_purchase_delayed_product',    $product_id ) ) {

			$purchasable = wc_memberships()->get_restrictions_instance()->is_product_public( $product instanceof \WC_Product_Variation ? $product->get_parent_id() : $product_id );
		}

		return $purchasable;
	}


	/**
	 * Checks whether a product is purchasable based on restriction rules.
	 *
	 * @since 1.9.0
	 *
	 * @param bool $purchasable whether the product is purchasable
	 * @param \WC_Product|\WC_Product_Variation $product the product
	 * @return bool
	 */
	public function product_is_purchasable( $purchasable, $product ) {

		if ( $purchasable ) {

			if ( $product->is_type( 'variation' ) ) {

				$purchasable = $this->variation_is_purchasable( $purchasable, $product );

			} else {

				$product_id = $product->get_id();

				if ( array_key_exists( $product_id, $this->product_purchasable ) ) {

					$purchasable = $this->product_purchasable[ $product_id ];

				} else {

					$purchasable = $this->product_can_be_purchased( $product );

					$this->product_purchasable[ $product_id ] = $purchasable;
				}
			}
		}

		return $purchasable;
	}


	/**
	 * Checks whether a product variation is purchasable based on restriction rules.
	 *
	 * Will also check if parent product can be purchased or is forced public.
	 *
	 * @internal
	 *
	 * @since 1.9.4
	 *
	 * @param bool $purchasable whether the variation is purchasable
	 * @param \WC_Product_Variation $product_variation
	 * @return bool
	 */
	public function variation_is_purchasable( $purchasable, $product_variation ) {

		if ( $purchasable ) {

			$product_variation_id = $product_variation->get_id();

			if ( array_key_exists( $product_variation_id, $this->product_purchasable ) ) {

				$purchasable = $this->product_purchasable[ $product_variation_id ];

			} else {

				$purchasable = $this->product_can_be_purchased( $product_variation );

				// we can check if parent is purchasable recursively while removing our filter to avoid loops
				if ( $parent_product = $this->get_parent_product( $product_variation ) ) {

					// if the product isn't purchasable, check if the parent is forced public
					if ( ! $purchasable ) {

						$purchasable = wc_memberships()->get_restrictions_instance()->is_product_public( $parent_product->get_id() );

					// if the product is purchasable, check if the parent is restricted in the first place
					} else {

						// remove our filter so we can get the default purchasable status first
						remove_filter( 'woocommerce_is_purchasable', array( $this, 'product_is_purchasable' ), 999 );

						$parent_is_purchasable = $parent_product->is_purchasable();

						// re-add our filter back
						add_filter( 'woocommerce_is_purchasable', array( $this, 'product_is_purchasable' ), 999, 2 );

						// flag the product variation based on the purchasable status of the parent product
						$purchasable = $parent_is_purchasable && $this->product_is_purchasable( $parent_is_purchasable, $parent_product );
					}
				}

				$this->product_purchasable[ $product_variation_id ] = $purchasable;
			}
		}

		return $purchasable;
	}


	/**
	 * Returns the parent product from a product variation.
	 *
	 * Helper method to optimize performance when looping through parent products and product variations.
	 * @see \WC_Memberships_Products_Restrictions::product_is_purchasable()
	 *
	 * @since 1.9.0
	 *
	 * @param \WC_Product_Variation $product_variation a variable product variation
	 * @return null|\WC_Product_Variable the parent variable product
	 */
	private function get_parent_product( $product_variation ) {

		$parent_product = null;

		if ( $product_variation instanceof \WC_Product_Variation ) {

			$product_variation_id = (int) $product_variation->get_id();

			if ( array_key_exists( $product_variation_id, $this->parent_product ) ) {

				$parent_product = $this->parent_product[ $product_variation_id ];

			} else {

				$parent_product = wc_get_product( $product_variation->get_parent_id( 'edit' ) );

				if ( $parent_product instanceof \WC_Product_Variable ) {
					foreach ( $parent_product->get_children() as $product_variation_id ) {
						$this->parent_product[ (int) $product_variation_id ] = $parent_product;
					}
				}
			}
		}

		return $parent_product;
	}


	/**
	 * Restricts product visibility in catalog based on restriction rules.
	 *
	 * @since 1.9.0
	 *
	 * @param bool $visible whether the product is visible
	 * @param int $product_id the product ID
	 * @return bool
	 */
	public function product_is_visible( $visible, $product_id ) {

		// if we are hiding products from catalog & search and user has no matching capability, then the product shall not be visible
		if (    ! current_user_can( 'wc_memberships_view_restricted_product', $product_id )
		     &&   wc_memberships()->get_restrictions_instance()->hiding_restricted_products() ) {
			$visible = false;
		}

		return $visible;
	}


	/**
	 * Excludes view-restricted variations.
	 *
	 * @internal
	 *
	 * @since 1.9.0
	 *
	 * @param bool $is_visible
	 * @param int $variation_id
	 * @param int $parent_id
	 * @return bool
	 */
	public function variation_is_visible( $is_visible, $variation_id, $parent_id ) {

		// exclude restricted variations
		if (    ! current_user_can( 'wc_memberships_view_restricted_product', $variation_id )
		     && ! current_user_can( 'wc_memberships_view_delayed_product',    $variation_id ) ) {
			$is_visible = false;
		}

		if ( ! $is_visible && ! empty( $parent_id ) ) {
			$is_visible = wc_memberships()->get_restrictions_instance()->is_product_public( $parent_id );
		}

		return $is_visible;
	}


	/**
	 * Excludes view-restricted variations.
	 *
	 * @internal
	 *
	 * @since 1.9.0
	 *
	 * @param bool $is_visible
	 * @param int $product_id
	 * @param \WC_Product_Variation $variation
	 * @return bool
	 */
	public function hide_invisible_variations( $is_visible, $product_id, $variation ) {

		// exclude restricted variations
		if (    ! current_user_can( 'wc_memberships_view_restricted_product', $variation->get_id() )
		     && ! current_user_can( 'wc_memberships_view_delayed_product',    $variation->get_id() ) ) {
			$is_visible = false;
		}

		return $is_visible;
	}


	/**
	 * Displays product purchasing restricted message.
	 *
	 * @internal
	 *
	 * @since 1.9.0
	 */
	public function display_product_purchasing_restricted_message() {
		global $product;

		if ( $product instanceof \WC_Product ) {

			$product_id = $product->get_id();
			$args       = array( 'post_id' => $product_id );

			if ( ! current_user_can( 'wc_memberships_purchase_restricted_product', $product_id ) ) {

				// purchasing is restricted
				echo \WC_Memberships_User_Messages::get_message_html( 'product_purchasing_restricted', $args );

			} elseif ( ! current_user_can( 'wc_memberships_purchase_delayed_product', $product_id ) ) {

				$args['access_time'] = wc_memberships()->get_capabilities_instance()->get_user_access_start_time_for_post( get_current_user_id(), $product_id, 'purchase' );

				// purchasing is delayed
				echo \WC_Memberships_User_Messages::get_message_html( 'product_access_delayed', $args );

			} elseif ( $product->is_type( 'variable' ) && $product->has_child() ) {

				// variation-specific messages
				$variations_restricted = false;

				/* @type \WC_Product_Variable $product */
				foreach ( $product->get_available_variations() as $variation ) {

					if ( ! $variation['is_purchasable'] ) {

						$variation_id    = (int) $variation['variation_id'];
						$args['classes'] = array( 'wc-memberships-variation-message', 'js-variation-' . sanitize_html_class( $variation_id ) );
						$args['post_id'] = $variation_id;

						if ( ! current_user_can( 'wc_memberships_purchase_restricted_product', $variation_id ) ) {

							$variations_restricted = true;

							// purchasing is restricted
							echo \WC_Memberships_User_Messages::get_message_html( 'product_purchasing_restricted', $args );

						} elseif ( ! current_user_can( 'wc_memberships_purchase_delayed_product', $variation['variation_id'] ) ) {

							$args['access_time']   = wc_memberships()->get_capabilities_instance()->get_user_access_start_time_for_post( get_current_user_id(), $product_id, 'purchase' );
							$variations_restricted = true;

							// purchasing is delayed
							echo \WC_Memberships_User_Messages::get_message_html( 'product_access_delayed', $args );
						}
					}
				}

				if ( $variations_restricted ) {
					wc_enqueue_js( "
						$( '.variations_form' )
							.on( 'woocommerce_variation_select_change', function( event ) {
								$( '.wc-memberships-variation-message' ).hide();
							} )
							.on( 'found_variation', function( event, variation ) {
								$( '.wc-memberships-variation-message' ).hide();
								if ( ! variation.is_purchasable ) {
									$( '.wc-memberships-variation-message.js-variation-' + variation.variation_id ).show();
								}
							} )
							.find( '.variations select' ).change();
					" );
				}
			}
		}
	}


	/**
	 * Displays member discount message for a product or product variation.
	 *
	 * @internal
	 *
	 * @since 1.9.0
	 */
	public function display_product_purchasing_discount_message() {
		global $post, $product;

		if ( $product instanceof \WC_Product ) {

			$user_id = get_current_user_id();
			$args    = array(
				'post'      => $post && in_array( $post->post_type, array( 'product', 'product_variation' ), true ) ? $post : get_post( $product ),
				'rule_type' => 'purchasing_discount',
			);

			// if the main/parent product needs the message, just display it normally
			if (      wc_memberships_product_has_member_discount( $product )
			     && ! wc_memberships_user_has_member_discount( $product, $user_id ) ) {

				echo \WC_Memberships_User_Messages::get_message_html( 'product_discount', $args );

			// if this is a variable product, set the messages up for display per-variation
			} elseif ( $product->is_type( 'variable' ) && $product->has_child() ) {

				unset( $args['post'] );

				$variations_discounted = false;

				/* @type \WC_Product_Variable $product */
				foreach ( $product->get_children() as $variation_id ) {

					if (      wc_memberships_product_has_member_discount( $variation_id )
					     && ! wc_memberships_user_has_member_discount( $variation_id, $user_id ) ) {

						$args['post_id']       = (int) $variation_id;
						$args['classes']       = array( 'wc-memberships-variation-message', 'js-variation-' . sanitize_html_class( $variation_id ) );
						$variations_discounted = true;

						echo \WC_Memberships_User_Messages::get_message_html( 'product_discount', $args );
					}
				}

				if ( $variations_discounted ) {
					wc_enqueue_js( "
						$( '.variations_form' )
							.on( 'woocommerce_variation_select_change', function( event ) {
								$( '.wc-memberships-variation-message.wc-memberships-member-discount-message' ).hide();
							} )
							.on( 'found_variation', function( event, variation ) {
								$( '.wc-memberships-variation-message.wc-memberships-member-discount-message' ).hide();
								$( '.wc-memberships-variation-message.wc-memberships-member-discount-message.js-variation-' + variation.variation_id ).show();
							} )
							.find( '.variations select' ).change();
					" );
				}
			}
		}
	}


	/**
	 * Displays an optional login prompt in cart if there are items that have member discounts.
	 *
	 * @internal
	 *
	 * @since 1.9.0
	 */
	public function display_cart_member_login_notice() {

		if (    ! is_user_logged_in()
		     &&   is_cart()
		     &&   in_array( get_option( 'wc_memberships_display_member_login_notice' ), array( 'cart', 'both' ), true )
		     &&   ( $this->cart_has_items_with_member_discounts() && ! wp_doing_ajax() ) ) {

			wc_add_notice( $this->get_member_login_message(), 'notice' );
		}
	}


	/**
	 * Displays an optional login prompt at checkout if there are items that have member discounts.
	 *
	 * @internal
	 *
	 * @since 1.9.0
	 *
	 * @param string $template_name template being loaded by WooCommerce
	 */
	public function display_checkout_member_login_notice( $template_name ) {

		if (      'checkout/form-login.php' === $template_name
		     && ! is_user_logged_in()
		     &&   $this->cart_has_items_with_member_discounts()
		     &&   in_array( get_option( 'wc_memberships_display_member_login_notice' ), array( 'checkout', 'both' ), true ) ) {

			wc_print_notice( $this->get_member_login_message(), 'notice' );
		}
	}


	/**
	 * Returns the member login message.
	 *
	 * @since 1.9.0
	 *
	 * @return string
	 */
	private function get_member_login_message() {

		// check first for a custom message
		$message = \WC_Memberships_User_Messages::get_message( 'member_login_message' );

		if ( empty( $message ) ) {

			if ( count( $this->get_cart_items_with_member_discounts() ) > 1 ) {
				// there are many items in the cart and more than one could have a member discount
				$code = 'cart_items_discount';
			} elseif ( count( WC()->cart->get_cart() ) > 1 ) {
				// there are many items in the cart but one could have a member discount
				$code = 'cart_item_discount';
			} else {
				// there's only one item in cart and it could have a member discount
				$code = 'cart_sole_item_discount';
			}

			// use a dummy product so we get the right message code used
			$message = \WC_Memberships_User_Messages::get_message_html( $code, array( 'context' => 'notice', 'products' => array( 1 ) ) );

		} else {

			// use a dummy product so we get the right message code used
			$message = \WC_Memberships_User_Messages::get_message_html( 'member_login', array( 'context' => 'notice', 'products' => array( 1 ) ) );
		}

		return $message;
	}


	/**
	 * Help method that returns items in cart with member discounts.
	 *
	 * @since 1.9.0
	 *
	 * @return int[] array of product IDs with member discounts
	 */
	private function get_cart_items_with_member_discounts() : array {

		if ( empty( $this->cart_items_with_member_discounts ) ) {

			$this->cart_items_with_member_discounts = array();

			foreach ( WC()->cart->get_cart() as $item_key => $item ) {

				$product_id = isset( $item['variation_id'] ) && $item['variation_id'] ? $item['variation_id'] : $item['product_id'];

				// pass the product instance below so 3rd parties have full access (and context) to the product in cart
				// when filtering the result in `wc_memberships_exclude_product_from_member_discounts`
				$product = $item['data'];

				if (      wc_memberships()->get_rules_instance()->product_has_purchasing_discount_rules( $product_id )
				     && ! wc_memberships()->get_member_discounts_instance()->is_product_excluded_from_member_discounts( $product ) ) {

					$this->cart_items_with_member_discounts[] = $product_id;
				}
			}
		}

		return $this->cart_items_with_member_discounts;
	}


	/**
	 * Checks if cart has any items with member discounts.
	 *
	 * @since 1.9.0
	 *
	 * @return bool
	 */
	private function cart_has_items_with_member_discounts() {

		$cart_items = $this->get_cart_items_with_member_discounts();

		return ! empty( $cart_items );
	}


	/**
	 * Restricts related products in related product queries (WC 3.3+).
	 *
	 * @see \wc_get_related_products()
	 *
	 * @internal
	 *
	 * @since 1.9.6
	 *
	 * @param int[] $related_product_ids array of product IDs
	 * @param int $parent_product_id the parent product ID other $related_product_ids are related to
	 * @param array $args arguments used to query $related_product_ids
	 * @return int[] array of product IDs
	 */
	public function restrict_related_products( $related_product_ids, $parent_product_id, $args ) {

		if ( ! empty( $related_product_ids ) ) {

			$restrictions = wc_memberships()->get_restrictions_instance();

			// process related products only when current user is not an admin and restricting content completely or excluding restricted products from catalog
			if (    ! current_user_can( 'wc_memberships_access_all_restricted_content' )
			     &&   ( $restrictions->is_restriction_mode( 'hide' ) || $restrictions->hiding_restricted_products() ) ) {

				$cache_key = http_build_query( array_merge( $args, array(
					'product_id' => $parent_product_id,
					'member_id'  => get_current_user_id(),
				) ) );

				if ( ! isset( $this->related_products[ $cache_key ] ) ) {

					$transient  = get_transient( 'wc_memberships_related_products' );
					$cached_ids = is_array( $transient ) ? $transient : array();

					if ( isset( $cached_ids[ $cache_key ] ) ) {

						$related_product_ids = $cached_ids[ $cache_key ];

					} else {

						$query_args = array(
							'fields'         => 'ids',
							'post_type'      => 'product',
							'post_status'    => 'publish',
							'posts_per_page' => max( 1, ! empty( $args['limit'] ) ? (int) $args['limit'] + 10 : (int) get_option( 'posts_per_page', 10 ) ),
						);

						$post__not_in = array_merge( array( 0, (int) $parent_product_id ), ! empty( $args['excluded_ids'] ) ? (array) $args['excluded_ids'] : array() );

						/* @see \WC_Product_Data_Store_CPT::get_related_products_query() */
						$product_visibility_term_ids = wc_get_product_visibility_term_ids();

						if ( ! empty( $product_visibility_term_ids['exclude-from-catalog'] ) ) {
							$post__not_in = array_merge( $post__not_in, (array) $product_visibility_term_ids['exclude-from-catalog'] );
						}

						if ( ! empty( $product_visibility_term_ids['outofstock'] ) && 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) ) {
							$post__not_in = array_merge( $post__not_in, (array) $product_visibility_term_ids['outofstock'] );
						}

						$query_args['post__not_in'] = array_unique( $post__not_in );

						/* these are WooCommerce core internal filters */
						$cats_array = apply_filters( 'woocommerce_product_related_posts_relate_by_category', true, $parent_product_id ) ? apply_filters( 'woocommerce_get_related_product_cat_terms', wc_get_product_term_ids( $parent_product_id, 'product_cat' ), $parent_product_id ) : array();
						$tags_array = apply_filters( 'woocommerce_product_related_posts_relate_by_tag',      true, $parent_product_id ) ? apply_filters( 'woocommerce_get_related_product_tag_terms', wc_get_product_term_ids( $parent_product_id, 'product_tag' ), $parent_product_id ) : array();
						$tax_query  = array();

						if ( ! empty( $cats_array ) ) {
							$tax_query[] = array(
								'taxonomy' => 'product_cat',
								'field'    => 'id',
								'terms'    => $cats_array,
							);
						}

						if ( ! empty( $tags_array ) ) {
							$tax_query[] = array(
								'taxonomy' => 'product_tag',
								'field'    => 'id',
								'terms'    => $tags_array,
							);
						}

						if ( count( $tax_query ) > 1 ) {
							$tax_query['relation'] = 'AND';
						}

						// WooCommerce core handling
						if ( empty( $tax_query ) && ! apply_filters( 'woocommerce_product_related_posts_force_display', false, $parent_product_id ) ) {
							$related_product_ids = array();
						// query results will come filtered for the current member access rights
						} else {
							$query_args['tax_query'] = $tax_query;
							$results                 = new \WP_Query( $query_args );
							$related_product_ids     = $results->posts;
						}

						$cached_ids[ $cache_key ] = $related_product_ids;

						set_transient( 'wc_memberships_related_products', $cached_ids, HOUR_IN_SECONDS );
					}

					$this->related_products[ $cache_key ] = $related_product_ids;

				} else {

					$related_product_ids = $this->related_products[ $cache_key ];
				}
			}
		}

		return $related_product_ids;
	}


	/**
	 * Restricts related product taxonomy (category) terms.
	 *
	 * @internal
	 *
	 * @since 1.9.6
	 *
	 * @param int[] $related_category_ids array of term IDs
	 * @return int[]
	 */
	public function restrict_related_terms( $related_category_ids ) {

		if (    ! empty( $related_category_ids )
		     && ! current_user_can( 'wc_memberships_access_all_restricted_content' ) ) {

			foreach ( $related_category_ids as $i => $related_category_id ) {

				if ( ! current_user_can( 'wc_memberships_view_restricted_product_taxonomy_term', 'product_cat', $related_category_id ) ) {
					unset( $related_category_ids[ $i ] );
				}
			}
		}

		return $related_category_ids;
	}


	/**
	 * Handles restrictions for products queried by WooCommerce core shortcodes.
	 *
	 * Shortcode queries in WooCommerce often use direct SQL queries so we need to filter the query arguments to ensure no restricted products or categories are shown to non members.
	 *
	 * @internal
	 *
	 * @since 1.9.0
	 *
	 * @param array $query_args query arguments
	 * @param array $attributes shortcode attributes
	 * @param string $loop_name current shortcode
	 * @return array
	 */
	public function restrict_shortcode_products_query_results( $query_args, $attributes, $loop_name = null ) {

		$restrictions = wc_memberships()->get_restrictions_instance();

		if (      $restrictions
			 && ! empty( $query_args['post_type'] )
		     &&   'product' === $query_args['post_type']
		     && ! current_user_can( 'wc_memberships_access_all_restricted_content' )
		     &&   ( $restrictions->is_restriction_mode( 'hide' ) || ( $restrictions->is_restriction_mode( 'hide_content' ) && $restrictions->hiding_restricted_products() ) ) ) {

			if ( isset( $query_args['post__not_in'] ) ) {
				$post__not_in = array_filter( array_map( 'absint', is_string( $query_args['post__not_in'] ) ? explode( ',', $query_args['post__not_in'] ) : (array) $query_args['post__not_in'] ) );
			} else {
				$post__not_in = array();
			}

			$product_ids = get_posts( array_merge( $query_args, array( 'fields' => 'ids' ) ) );

			if ( ! empty( $product_ids ) ) {

				foreach ( $product_ids as $product_id ) {

					if ( ! current_user_can( 'wc_memberships_view_restricted_product', $product_id ) ) {

						$post__not_in[] = $product_id;
					}
				}
			}

			$post__not_in = array_unique( $post__not_in );

			// either use post__in OR post__not_in
			if ( ! empty( $query_args['post__in'] ) ) {

				$post__in               = array_diff( $query_args['post__in'], $post__not_in );
				$query_args['post__in'] = $post__in;

				if ( empty( $post__in ) ) {

					unset( $query_args['post__in'] );

					if ( ! empty( $post__not_in ) ) {
						$query_args['post__not_in'] = $post__not_in;
					} else {
						unset( $query_args['post__not_in'] );
					}
				}

			} elseif ( ! empty( $post__not_in ) ) {

				$query_args['post__not_in'] = $post__not_in;

			} else {

				unset( $query_args['post__in'], $query_args['post__not_in'] );
			}

			$user_id          = get_current_user_id();
			$user_memberships = wc_memberships_get_user_memberships( $user_id );
			$member_status    = array( $user_id => array() );

			if ( ! empty( $user_memberships ) ) {

				foreach ( $user_memberships as $user_membership ) {

					$member_status[ $user_id ][ $user_membership->get_id() ] = $user_membership->get_status();
				}
			}

			// This should trick WooCommerce to create a transient of cached product IDs for the current user.
			// It doesn't account though for plan rule changes or specific plan access to products, only generic member status.
			/* @see \WC_Shortcode_Products::get_products() */
			$query_args['member_status'] = $member_status;
		}

		return $query_args;
	}


	/**
	 * Filters the WooCommerce product categories widgets.
	 *
	 * @internal
	 *
	 * @since 1.9.0
	 *
	 * @param array $args array of arguments
	 * @return array
	 */
	public function hide_widget_product_categories( $args = array() ) {

		switch ( current_filter() ) {

			case 'woocommerce_product_categories_widget_args' :
				$exclude_tree = $this->get_restricted_product_category_excluded_tree( array( 'fields' => 'ids' ) );
			break;

			case 'wc_product_dropdown_categories_get_terms_args' :
				$ids_only           = $args;
				$ids_only['fields'] = 'ids';
				$exclude_tree       = $this->get_restricted_product_category_excluded_tree( $ids_only );
			break;

			default :
				$exclude_tree = null;
			break;
		}

		if ( ! empty( $args['include'] ) ) {

			$include = array();

			// this might be a comma separated string
			if ( is_string( $args['include'] ) ) {
				$args['include'] = explode( ',', $args['include'] );
			}

			if ( is_array( $args['include'] ) ) {
				$include = $this->filter_restricted_product_categories_array( 'accessible', $args['include'] );
			}

			if ( ! empty( $include ) ) {
				$args['include'] = $include;
			} else {
				unset( $args['include'] );
			}
		}

		if ( ! empty( $exclude_tree ) ) {
			$args['exclude_tree'] = $exclude_tree;
		}

		return $args;
	}


	/**
	 * Returns an array of product categories ids to be excluded.
	 *
	 * @since 1.9.0
	 *
	 * @param array $args arguments to pass to `get_terms()`
	 * @return int[] array of product category IDs to exclude
	 */
	private function get_restricted_product_category_excluded_tree( array $args ) {

		// TODO remove when WordPress 4.5 is the minimum required version {FN 2017-09-19}
		if ( version_compare( get_bloginfo( 'version' ), '4.5', '>=' ) ) {
			$args['taxonomy']   = 'product_cat';
			$product_categories = get_terms( $args );
		} else {
			$product_categories = get_terms( 'product_cat', $args );
		}

		$exclude_tree = array();

		if ( ! empty( $product_categories ) ) {
			$exclude_tree = $this->filter_restricted_product_categories_array( 'restricted', $product_categories );
		}

		return $exclude_tree;
	}


	/**
	 * Helper method that returns restricted or accessible category IDs.
	 *
	 * @see \WC_Memberships_Products_Restrictions::hide_widget_product_categories()
	 * @see \WC_Memberships_Products_Restrictions::get_restricted_product_category_excluded_tree()
	 *
	 * @since 1.9.0
	 *
	 * @param string $which 'accessible' or 'restricted': whether to build an array of excluded categories or categories that are accessible for the current user
	 * @param int[] $product_categories array of product category IDs
	 * @return int[]
	 */
	private function filter_restricted_product_categories_array( $which, array $product_categories ) {

		$filter_categories = array();

		// if we're hiding products, let's return a filtered list
		if (    ! empty( $product_categories )
		     &&   wc_memberships()->get_restrictions_instance()->hiding_restricted_products() ) {

			foreach ( $product_categories as $product_category_id ) {

				if ( is_numeric( $product_category_id ) ) {

					if (       'restricted' === $which && ! current_user_can( 'wc_memberships_view_restricted_product_taxonomy_term', 'product_cat', $product_category_id ) ) {
						$filter_categories[] = (int) $product_category_id;
					} elseif ( 'accessible' === $which &&   current_user_can( 'wc_memberships_view_restricted_product_taxonomy_term', 'product_cat', $product_category_id ) ) {
						$filter_categories[] = (int) $product_category_id;
					}
				}
			}

		// otherwise, change nothing
		} elseif ( 'accessible' === $which ) {
			$filter_categories = $product_categories;
		}

		return $filter_categories;
	}


}
