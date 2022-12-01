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

use SkyVerge\WooCommerce\Memberships\Helpers\Strings_Helper;
use SkyVerge\WooCommerce\PluginFramework\v5_10_13 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Memberships User Messages Handler.
 *
 * This static class provides an handler for front end messages as well as an admin helper to edit and update messages.
 *
 * An important note is understanding how message codes work.
 * They're based on a consistent naming convention based on the following components:
 *
 * {content_type}_{restriction_type}_message_{products}
 *
 * The content type is for example "post_content" if a blog post, or "product" if a product.
 * The restriction type is "delayed", "restricted", etc.
 * The "_message_" suffix is appended for completeness and becomes relevant in filtering, providing semantic value.
 * The latest bit is omitted for messages that are relevant to content that is restricted by plans that have one or more products to get access.
 * If a Membership has no products that grant access then the "no_products" suffix is used.
 *
 * The {products} flag the "_message_" suffixes are normally not required when invoking a message as the `get_message_html()` method will attempt to self determine these based on passed arguments and context.
 * Likewise, this class getter methods will try to determine whether a default message, a message saved in the settings option or a custom message override has to be used.
 *
 * It is not the case however when saving a message with `set_message()` in that case the method needs to know the full message code including these suffix variables.
 * It is taken into account of that in admin methods and admin UI that set message options and individual content overrides.
 *
 * @since 1.9.0
 */
class WC_Memberships_User_Messages {


	/** @var string the option key where the array of user messages is stored */
	private static $user_messages_option_key = 'wc_memberships_messages';


	/**
	 * Set a message.
	 *
	 * It is important to remember that the $code to be passed to this method has to be a fully formed code.
	 * It will not auto-determine or adjust the message code and therefore this has to include the '_message' suffix or any '_no_products' ending flag.
	 *
	 * @since 1.9.0
	 *
	 * @param string $code message key code
	 * @param string $message message body
	 * @param null|int|\WP_Post|\WC_Product $object_id optional, if specified will try to set the message override on the given object
	 */
	public static function set_message( $code, $message, $object_id = null ) {

		if ( is_string( $message ) && in_array( $code, self::get_default_messages( false ), true ) ) {

			if ( $object_id instanceof \WP_Post ) {
				$object_id = $object_id->ID;
			} elseif ( $object_id instanceof \WC_Product ) {
				$object_id = $object_id->get_id();
			}

			if ( is_numeric( $object_id ) && $object_id > 0 ) {

				if ( '' === trim( $message ) ) {
					wc_memberships_delete_content_meta( $object_id, "_wc_memberships_{$code}" );
				} else {
					wc_memberships_set_content_meta( $object_id, "_wc_memberships_{$code}", $message );
				}

			} else {

				$messages = get_option( self::$user_messages_option_key, array() );

				if ( ! is_array( $messages ) ) {
					$messages = self::get_default_messages();
				}

				$messages[ $code ] = '' === $message ? self::get_default_message( $code ) : $message;

				update_option( self::$user_messages_option_key, $messages );
			}
		}
	}


	/**
	 * Delete a message (restores the default message).
	 *
	 * @since 1.9.0
	 *
	 * @param string $code the message key code
	 * @param int|null|object $object_id optional, if specified will delete the message override from the object
	 */
	public static function delete_message( $code, $object_id = null ) {

		self::set_message( $code, '', $object_id );
	}


	/**
	 * Get the messages defaults.
	 *
	 * @since 1.9.0
	 *
	 * @param bool $with_labels whether to return only the message key codes (false) or also the labels (true, default)
	 * @return array associative array of codes and text
	 */
	public static function get_default_messages( $with_labels = true ) {

		$messages = [

			// generic content (e.g. any other post type than blog posts, pages and products)
			'content_delayed_message'                                               => __( 'This content is part of your membership, but not yet! You will gain access on {date}.', 'woocommerce-memberships' ),
			'content_restricted_message'                                            => __( 'To access this content, you must purchase {products}.', 'woocommerce-memberships' ),
			'content_restricted_message_no_products'                                => __( 'This content is only available to members.', 'woocommerce-memberships' ),

			// content categories
			'content_category_delayed_message'                                      => __( 'This category is part of your membership, but not yet! You will gain access on {date}.', 'woocommerce-memberships' ),
			'content_category_restricted_message'                                   => __( 'This category can only be viewed by members. To view this category, sign up by purchasing {products}.', 'woocommerce-memberships' ),
			'content_category_restricted_message_no_products'                       => __( 'This category can only be viewed by members.', 'woocommerce-memberships' ),

			// blog posts
			'post_content_delayed_message'                                          => __( 'This post is part of your membership, but not yet! You will gain access on {date}.', 'woocommerce-memberships' ),
			'post_content_restricted_message'                                       => __( 'To access this post, you must purchase {products}.', 'woocommerce-memberships' ),
			'post_content_restricted_message_no_products'                           => __( 'This post is only available to members.', 'woocommerce-memberships' ),

			// pages
			'page_content_delayed_message'                                          => __( 'This page is part of your membership, but not yet! You will gain access on {date}.', 'woocommerce-memberships' ),
			'page_content_restricted_message'                                       => __( 'To access this page, you must purchase {products}.', 'woocommerce-memberships' ),
			'page_content_restricted_message_no_products'                           => __( 'This page is only available to members.', 'woocommerce-memberships' ),

			// products
			'product_access_delayed_message'                                        => __( 'This product is part of your membership, but not yet! It will become available on {date}.', 'woocommerce-memberships' ),
			'product_viewing_restricted_message'                                    => __( 'This product can only be viewed by members. To view or purchase this product, sign up by purchasing {products}.', 'woocommerce-memberships' ),
			'product_viewing_restricted_message_no_products'                        => __( 'This product can only be viewed by members.', 'woocommerce-memberships' ),
			'product_purchasing_restricted_message'                                 => __( 'This product can only be purchased by members. To purchase this product, sign up by purchasing {products}.', 'woocommerce-memberships' ),
			'product_purchasing_restricted_message_no_products'                     => __( 'This product can only be purchased by members.', 'woocommerce-memberships' ),

			// product categories
			'product_category_viewing_delayed_message'                              => __( 'This product category is part of your membership, but not yet! You will gain access on {date}.', 'woocommerce-memberships' ),
			'product_category_viewing_restricted_message'                           => __( 'This product category can only be viewed by members. To view this category, sign up by purchasing {products}.', 'woocommerce-memberships' ),
			'product_category_viewing_restricted_message_no_products'               => __( 'This product category can only be viewed by members.', 'woocommerce-memberships' ),

			// product discount messages (non-restrictions)
			'product_discount_message'                                              => __( 'Want a discount? Become a member by purchasing {products}!', 'woocommerce-memberships' ),
			'product_discount_message_no_products'                                  => __( 'Want a discount? Become a member!', 'woocommerce-memberships' ),

			// product discount cart messages (non-restrictions)
			'cart_sole_item_discount_message'                                       => __( 'This item is discounted for members. {Login} to claim it!', 'woocommerce-memberships' ),
			'cart_item_discount_message'                                            => __( 'An item in your cart is discounted for members. {Login} to claim it!', 'woocommerce-memberships' ),
			'cart_items_discount_message'                                           => __( 'Some items in your cart are discounted for members. {Login} to claim them!', 'woocommerce-memberships' ),

			// messages for non-members with discounting products in their cart
			'product_discounted_by_membership_products_in_cart_message_no_products' => __( 'Discounted by membership plans in the cart', 'woocommerce-memberships' ),
			'product_discounted_by_membership_product_in_cart_message'              => __( 'Discounted by {products} in cart', 'woocommerce-memberships' ),

			// member login message (blank by default)
			'member_login_message'                                                  => '',
		];

		/**
		 * Filter the default user messages.
		 *
		 * The keys are used to store and organize message overrides, the text serves as default message when a override is not found.
		 *
		 * @since 1.9.0
		 *
		 * @param array $messages associative array of message codes and message texts
		 */
		$messages = (array) apply_filters( 'wc_memberships_default_messages', $messages );

		return false === $with_labels ? array_keys( $messages ) : $messages;
	}


	/**
	 * Returns message by type, defaults to content_restricted_message_no_products.
	 *
	 * @since 1.9.0
	 *
	 * @param string $code message key code
	 * @return string
	 */
	public static function get_default_message( $code ) {

		$messages = self::get_default_messages();

		return isset( $messages[ $code ] ) ? $messages[ $code ] : '';
	}


	/**
	 * Get the message code shorthand by post type.
	 *
	 * This method tries to self determine the right message that should returned based on content context.
	 * It will return the message code shorthand, which is useful to `get_message_html()` to retrieve the actual message.
	 *
	 * @since 1.9.0
	 *
	 * @param null|int|\WP_Post|\WC_Product|string $post the post type, post object, post ID or product
	 * @param array $args arguments to determine the requested message type or optional view/purchase type for products
	 * @return string the message code key
	 */
	public static function get_message_code_shorthand_by_post_type( $post = null, $args = array() ) {

		if ( is_string( $post ) ) {
			$post_type = $post;
		} elseif ( is_numeric( $post ) || $post instanceof \WP_Post ) {
			$post_type = get_post_type( $post );
		} elseif ( $post instanceof \WC_Product ) {
			$post_type = $post->post_type;
		} else {
			global $post;
			$post_type = $post ? get_post_type( $post ) : '';
		}

		$access_type   = isset( $args['access_type'] )  ? $args['access_type']  : 'view';       // either 'view' or 'purchase'
		$message_type  = isset( $args['message_type'] ) ? $args['message_type'] : 'restricted'; // either 'restricted' or 'delayed'
		$access_status = in_array( $message_type, array( 'delayed', 'restricted' ), true ) ? $message_type : 'restricted';

		switch ( $post_type ) {
			case 'product':
			case 'product_variation':
				if ( 'view' === $access_type ) {
					$message_code = 'delayed' === $access_status ? 'product_access_delayed' : "product_viewing_{$access_status}";
				} else {
					$message_code = "product_purchasing_{$access_status}";
				}
			break;
			case 'page':
				$message_code = "page_content_{$access_status}";
			break;
			case 'post':
				$message_code = "post_content_{$access_status}";
			break;
			// other post types
			default:
				$message_code = "content_{$access_status}";
			break;
		}

		/**
		 * Filter the message code shorthand to be used to store a message.
		 *
		 * @since 1.9.0
		 *
		 * @param string $message_code the message code being used
		 * @param string $post_type the related post type
		 */
		return apply_filters( 'wc_memberships_post_type_message_code_shorthand', $message_code, $post_type );
	}


	/**
	 * Looks for message string according to context. It may contain HTML.
	 *
	 * Note: unlike `get_message_html()` this method won't determine if a message has products or no products.
	 * A fully formed message code key should be passed to this method to retrieve the appropriate message type.
	 * Normally methods should call `get_message_html()` to obtain the right HTML message, by passing a message code shorthand.
	 *
	 * @see \WC_Memberships_User_Messages::get_message_html()
	 *
	 * @since 1.9.0
	 *
	 * @param string $message_code message code key
	 * @param array $message_args optional arguments
	 * @return string
	 */
	public static function get_message( $message_code, $message_args = array() ) {
		global $post;

		$the_post         = null;
		$message_override = null;
		$message_args     = self::parse_message_args( $message_code, $message_args );

		// determine the post source
		if ( ! empty( $message_args['post'] ) ) {
			$the_post = $message_args['post'];
		} elseif ( ! empty( $message_args['post_id'] ) ) {
			$the_post = get_post( $message_args['post_id'] );
		}

		// determine if there's a custom message override
		if ( $the_post = ! $the_post instanceof \WP_Post ? $post : $the_post ) {

			$use_override_key     = null;
			$message_override_key = null;

			// no_products messages aren't saved as custom messages, so look for a plain custom message first...
			if ( Framework\SV_WC_Helper::str_ends_with( $message_code, 'no_products' ) ) {

				$use_override_key = substr( "_wc_memberships_use_custom_{$message_code}", 0, -12 );

				if ( 'yes' === wc_memberships_get_content_meta( $the_post->ID, $use_override_key ) ) {

					$message_override_key = substr( "_wc_memberships_{$message_code}", 0, -12 );
					$message_override     = wc_memberships_get_content_meta( $the_post->ID, $message_override_key );
				}
			}

			// ...otherwise look for a regular custom override message
			if ( empty( $message_override ) ) {

				$use_override_key = "_wc_memberships_use_custom_{$message_code}";

				if ( 'yes' === wc_memberships_get_content_meta( $the_post->ID, $use_override_key ) ) {

					$message_override_key = "_wc_memberships_{$message_code}";
					$message_override     = wc_memberships_get_content_meta( $the_post->ID, $message_override_key );
				}
			}
		}

		// gather the message content based on the determined source to use
		if ( ! empty( $message_override ) && is_string( $message_override ) ) {
			// use custom message override
			$message = $message_override;
		} elseif ( ( $messages = get_option( self::$user_messages_option_key ) ) && isset( $messages[ $message_code ] ) ) {
			// get message from option
			$message = is_string( $messages[ $message_code ] ) ? $messages[ $message_code ] : '';
		} else {
			// get default message as last resort
			$message = self::get_default_message( $message_code );
		}

		/**
		 * Filter the message.
		 *
		 * @since 1.9.0
		 *
		 * @param string $message the membership message
		 * @param array $message_args array of vars used to construct the message
		 */
		$message = apply_filters( "wc_memberships_{$message_code}", $message, $message_args );

		/**
		 * Filters the message.
		 *
		 * @since 1.10.1
		 *
		 * @param string $message the membership message
		 * @param \WP_Post $the_post the post object being restricted
		 * @param string $message_code the message code being parsed
		 * @param array $message_args array of vars used to construct the message
		 */
		return (string) apply_filters( 'wc_memberships_restricted_message', $message, $the_post, $message_code, $message_args );
	}


	/**
	 * Returns a final restriction message in HTML format.
	 *
	 * Note: a message code in shorthand form must be passed to this method.
	 * The method self determines whether there are products or no products on the $args passed.
	 * Normally other code should call this method rather than the plain `get_message()`.
	 *
	 * @see \WC_Memberships_User_Messages::get_message()
	 *
	 * @since 1.9.0
	 *
	 * @param string $code_shorthand message type code in shorthand version (no `_message` or `_message_no_products` suffix needed)
	 * @param array $args optional array of arguments
	 * @return string HTML
	 */
	public static function get_message_html( $code_shorthand, $args = [] ) {

		$args     = self::parse_message_args( $code_shorthand, $args );
		$the_post = self::parse_post_from_message_args( $args );
		$the_term = self::parse_term_from_message_args( $args );

		/**
		 * Filters whether restricted messages of this type should be shown at all.
		 *
		 * This allows others to override certain message types entirely.
		 *
		 * @since 1.12.3
		 *
		 * @param bool $display whether to display the messages
		 * @param array $args the message args, if any
		 */
		if ( false === apply_filters( "wc_memberships_display_{$code_shorthand}_messages", true, $args ) ) {
			return 'content' === $args['context'] && ! empty( $args['use_excerpt'] ) && self::should_get_content_excerpt( $the_post, $code_shorthand ) ? self::get_restricted_content_excerpt( $the_post, $code_shorthand ) : '';
		}

		if ( empty( $args['rule_type'] ) ) {
			$rule_type = false !== strpos( $code_shorthand, 'discount' ) ? 'purchasing_discount'  : '';
		} else {
			$rule_type = $args['rule_type'];
		}

		$products     = self::get_products_that_grant_access_or_discount( $the_term instanceof \WP_Term ? $the_term : $the_post, $rule_type, $args );
		$access_time  = ! empty( $args['access_time'] ) ? $args['access_time'] : 0;
		$message_args = [
			'code'          => $code_shorthand,
			'context'       => $args['context'],
			'post'          => $the_post,
			'post_id'       => $the_post instanceof \WP_Post ? (int) $the_post->ID : 0,
			'term'          => $the_term,
			'term_id'       => $the_term instanceof \WP_Term ? (int) $the_term->term_id : 0,
			'term_taxonomy' => $the_term instanceof \WP_Term ? (string) $the_term->taxonomy : '',
			'access_time'   => $access_time,
			'products'      => $products,
			'rule_type'     => $rule_type,
			'classes'       => $args['classes'],
		];

		// find message by code key, parse shortcode and merge tags
		$message_code = empty( $products ) && ! in_array( $code_shorthand, [ 'cart_sole_item_discount', 'cart_item_discount', 'cart_items_discount', 'member_login' ], false ) && ! Framework\SV_WC_Helper::str_ends_with( $code_shorthand, 'delayed' ) ? $code_shorthand . '_message_no_products' : $code_shorthand . '_message';
		$message      = do_shortcode( self::get_message( $message_code, $message_args ) );
		$message      = self::parse_message_merge_tags( $message, $message_args );

		if ( 'content' === $message_args['context'] && '' !== trim( $message ) ) {
			$html_message = self::get_notice_html( $message_code, $message, $args );
		} else { // 'notice' === $message_args['context'] : the content is going to be wrapped in a WC notice already, so we don't duplicate HTML (such is the case for cart messages for example)
			$html_message = wp_kses( $message, self::get_message_allowed_html( $message_code ) );
			$html_message = self::filter_message_html( $html_message, $message, $message_code, $message_args );
		}

		return $html_message;
	}


	/**
	 * Gets a user message wrapped in a notice HTML.
	 *
	 * @since 1.15.0
	 *
	 * @param string $message_code the message code
	 * @param string $message_body may include HTML, shortcodes, merge tags
	 * @param array $message_args associative array of message arguments
	 * @return string HTML
	 */
	public static function get_notice_html( $message_code, $message_body, $message_args ) {

		ob_start();

		// TODO may consider turning the following into a template file, passing the same arguments as in this method {FN:2019-30-08}
		?>
		<div class="woocommerce">
			<div class="<?php echo implode( ' ', self::get_message_classes( $message_code, $message_args ) ); ?>">
				<?php echo wp_kses( $message_body, self::get_message_allowed_html( $message_code ) ); ?>
		    </div>
		</div>
		<?php

		/**
		 * Filters a Memberships notice HTML.
		 *
		 * @since 1.15.0
		 *
		 * @param string $notice_html HTML content
		 * @param string $message_body original message content
		 * @param string $message_code message code
		 * @param array $message_args associative array of message arguments
		 */
		$html = $notice = (string) apply_filters( 'wc_memberships_notice_html', ob_get_clean(), $message_body, $message_code, $message_args );

		if ( ! empty( $message_args['post'] ) && ! empty( $message_args['use_excerpt'] ) && self::should_get_content_excerpt( $message_args['post'], $message_code ) ) {
			$excerpt = self::get_restricted_content_excerpt( $message_args['post'], $message_code );
			$html = $excerpt . ' ' . $notice;
		}

		// TODO the output of a possible template file can still pass through the same filtering with no BC change {FN:2019-08-30}
		return self::filter_message_html( $html, $message_body, $message_code, $message_args );
	}


	/**
	 * Applies filters to the final message HTML.
	 *
	 * Helper method, do not open to public.
	 *
	 * @since 1.15.0
	 *
	 * @param string $message_html fully formed HTML message
	 * @param string $message message string for legacy filters
	 * @param string $message_code message code
	 * @param array $message_args message arguments
	 * @return string HTML
	 */
	private static function filter_message_html( $message_html, $message, $message_code, $message_args ) {

		$the_post = isset( $message_args['post'] ) ? $message_args['post'] : null;

		/**
		 * Filter whether to process shortcodes on user messages.
		 *
		 * @since 1.9.0
		 *
		 * @param bool $apply_shortcodes default true
		 * @param string $message_code the message code being parsed
		 * @param array $message_args optional arguments being processed
		 */
		if ( true === (bool) apply_filters( 'wc_memberships_message_process_shortcodes', true, $message_code, $message_args ) ) {
			$message_html = do_shortcode( $message_html );
		}

		/**
		 * Filters the restricted content (legacy filter).
		 *
		 * This is a legacy filter from the time (before 1.9.0) when Memberships was filtering the_content instead of the post object.
		 * Ideally, though, customizations should try filtering the specific restriction message.
		 *
		 * @since 1.6.0
		 *
		 * @param string $html_message the message that restricts the content (may contain HTML)
		 * @param true $restricted whether the content is restricted: since version 1.9.1 this is always true, the filter no longer runs when the content is not restricted (kept for backwards compatibility reasons)
		 * @param string $message the message that has replaced the content or appended to an excerpt
		 * @param \WP_Post $the_post the post object being restricted
		 */
		$message_html = (string) apply_filters( 'wc_memberships_the_restricted_content', $message_html, true, $message, $the_post );

		/**
		 * Filter the message HTML.
		 *
		 * @since 1.9.0
		 *
		 * @param string $html_message the HTML message
		 * @param array $message_args array of arguments used to build the message
		 */
		$message_html = (string) apply_filters( "wc_memberships_{$message_code}_html", $message_html, $message_args );

		/**
		 * Filters the message HTML.
		 *
		 * @since 1.10.1
		 *
		 * @param string $html_message the HTML message
		 * @param \WP_Post $the_post the post object being restricted
		 * @param string $message_code the message code being parsed
		 * @param array $message_args array of arguments used to build the message
		 */
		return (string) apply_filters( 'wc_memberships_restricted_message_html', $message_html, $the_post, $message_code, $message_args );
	}


	/**
	 * Determines if an excerpt should be used based on the given content object and message code.
	 *
	 * @since 1.12.3
	 *
	 * @param \WP_Post $the_post post object being restricted
	 * @param string $message_code message code or code shorthand
	 * @return bool
	 */
	private static function should_get_content_excerpt( $the_post, $message_code ) {
		global $post;

		$should_get_excerpt = false;

		// maybe define the excerpt to prepend to the message HTML
		if ( $the_post && ! Framework\SV_WC_Helper::str_exists( $message_code, 'category' ) ) {

			$restrictions = wc_memberships()->get_restrictions_instance();

			// show excerpt only if option is enabled and the restriction mode is not redirect (redirection content restricted page is always fully visible)
			if ( $restrictions->showing_excerpts() ) {

				$is_redirect = $restrictions->is_restriction_mode( 'redirect' );

				if ( ! $is_redirect ) {

					$should_get_excerpt = true;

				} else {

					$redirected_page_id          = $restrictions->get_restricted_content_redirect_page_id();
					$is_post_redirected_page     = $post && $post->ID === $redirected_page_id;
					$is_the_post_redirected_page = $the_post->ID === $redirected_page_id;

					$should_get_excerpt = $redirected_page_id > 0 && ! $is_post_redirected_page && ! $is_the_post_redirected_page;
				}
			}
		}

		return $should_get_excerpt;
	}


	/**
	 * Returns the restricted content excerpt.
	 *
	 * @since 1.10.1
	 *
	 * @param \WP_Post $post the post object being restricted
	 * @param string $message_code the message code being parsed: this may be the full message code or code shorthand
	 * @return string the restricted content excerpt
	 */
	private static function get_restricted_content_excerpt( $post, $message_code = '' ) {

		$excerpt = '';

		// for products, use WooCommerce template instead of WordPress standard excerpt
		if ( in_array( get_post_type( $post ), array( 'product', 'product_variation' ), true ) ) {

			if ( Framework\SV_WC_Helper::str_exists( $message_code, 'product_viewing_restricted' )
				 || ( Framework\SV_WC_Helper::str_exists( $message_code, 'product_access_delayed' ) && ( ! current_user_can( 'wc_memberships_view_delayed_product', $post->ID ) || ! current_user_can( 'wc_memberships_view_restricted_product', $post->ID ) ) ) ) {

				ob_start();

				?>
				<div class="summary entry-summary">
					<?php wc_get_template( 'single-product/title.php' ); ?>
					<?php wc_get_template( 'single-product/short-description.php' ); ?>
				</div>
				<?php

				$excerpt = ob_get_clean();
			}

		} else {

			$excerpt = empty( $post->post_excerpt ) ? self::trim_excerpt( $post ) : $post->post_excerpt;
		}

		/**
		 * Filters the excerpt displayed instead of the restricted content.
		 *
		 * @since 1.10.1
		 *
		 * @param string $excerpt the excerpt
		 * @param \WP_Post $post the post object being restricted
		 * @param string $message_code the message code being parsed
		 */
		return (string) apply_filters( 'wc_memberships_restricted_content_excerpt', $excerpt, $post, $message_code );
	}


	/**
	 * Gets a post object from arguments (helper method) or global.
	 *
	 * @since 1.11.1
	 *
	 * @param array $args arguments
	 * @return null|\WP_Post
	 */
	private static function parse_post_from_message_args( $args ) {
		global $post;

		if ( ! empty( $args['post'] ) ) {
			$the_post = $args['post'];
		} elseif ( ! empty( $args['post_id'] ) && is_numeric( $args['post_id'] ) ) {
			$the_post = get_post( (int) $args['post_id'] );
		} else {
			$the_post = null;
		}

		return $the_post instanceof \WP_Post ? $the_post : $post;
	}


	/**
	 * Gets a term object from arguments (helper method).
	 *
	 * @since 1.11.1
	 *
	 * @param array $args
	 * @return null|\WP_Term
	 */
	private static function parse_term_from_message_args( $args ) {

		if ( ! empty( $args['term'] ) ) {
			$the_term = $args['term'];
		} elseif ( ! empty( $args['term_id'] ) && ! empty( $args['term_taxonomy'] ) && is_numeric( $args['term_id'] ) && is_string( $args['term_taxonomy'] ) ) {
			$the_term = get_term( (int) $args['term_id'], $args['term_taxonomy'] );
		} else {
			$the_term = null;
		}

		return $the_term instanceof \WP_Term ? $the_term : null;
	}


	/**
	 * Parse message arguments for processing.
	 *
	 * @since 1.9.0
	 *
	 * @param string $message_code related message by code key
	 * @param array $args optional arguments
	 * @return array
	 */
	private static function parse_message_args( $message_code, array $args ) {

		$code = trim( $message_code );
		$args = wp_parse_args( $args, array(
			'code'          => $code,     // (string) code (key) of the message to fetch
			'context'       => 'content', // (string) whether the message will appear in the context of a notice or elsewhere
			'use_excerpt'   => true,      // (bool) display an excerpt when appropriate, pass false to force no excerpt
			'post'          => null,      // (\WP_Post) the post object, used for processing the message variables
			'post_id'       => 0,         // (int) the post ID, used when the post object is not passed directly
			'term'          => null,      // (\WP_Term) the term object, used when viewing a restricted term archive page
			'term_id'       => 0,         // (int) the term ID, used when the term object is not passed directly
			'term_taxonomy' => '',        // (string) the term taxonomy, used when the term object is not passed directly
			'access_time'   => 0,         // (int) the user access time, used mostly for delayed messages
			'products'      => array(),   // (int[]) IDs of products that grant access
			'rule_type'     => '',        // (string) the related rule type
			'classes'       => '',        // (string|array) optional additional classes used in the message HTML
		) );

		if ( ! in_array( $args['context'], array( 'content', 'notice' ), true ) ) {
			$args['context'] = 'content';
		}

		return $args;
	}


	/**
	 * Replace a message's merge tags with HTML variables.
	 *
	 * @since 1.9.0
	 *
	 * @param string $message the original message that may contain merge tags
	 * @param array $args associative array of optional arguments for strings replacement
	 * @return string HTML
	 */
	public static function parse_message_merge_tags( $message, $args = array() ) {

		$products    = ! empty( $args['products'] )    && is_array( $args['products'] )      ? array_filter( array_unique( $args['products'] ) ) : array();
		$access_time = ! empty( $args['access_time'] ) && is_numeric( $args['access_time'] ) ? max( 0, (int) $args['access_time'] )        : 0;

		// replace {products}
		if ( ! empty( $products ) ) {

			$products_links = array();

			foreach ( $products as $product_id ) {

				if ( $product = wc_get_product( $product_id ) ) {
					$products_links[ $product->get_id() ] = self::get_product_link_html( $product );
				}
			}

			// sanity check: by this point we should have accessible products to link...
			if ( ! empty( $products_links ) ) {

				$products_merge_tag = Strings_Helper::get_human_readable_items_list( $products_links, 'or' );

			// ...however if we don't, then set a fallback:
			} else {

				$message_code       = isset( $args['code'] ) && is_string( $args['code'] ) ? $args['code'] : '';
				$products_merge_tag = strtolower( esc_html__( 'A product that grants access', 'woocommerce-memberships' ) );

				// attempts to replace the message with a '_no_products' equivalent
				if ( '' !== $message_code && ! Framework\SV_WC_Helper::str_ends_with( $message_code, '_no_products' ) ) {

					unset( $args['products'] );

					$message_code = Framework\SV_WC_Helper::str_ends_with( $message_code, '_message' ) ? "{$message_code}_no_products" : "{$message_code}_message_no_products";

					if ( in_array( $message_code, self::get_default_messages( false ), true ) ) {

						/* @see \WC_Memberships_User_Messages::get_message_html() */
						$message = do_shortcode( self::get_message( $message_code, $args ) );
					}
				}
			}

			/**
			 * Filters the replacement string for the {products} merge tag.
			 *
			 * @since 1.10.4
			 *
			 * @param string $products_merge_tag a string of text
			 * @param int[] $products an array of product IDs
			 * @param string $message the current message where {products} is found
			 * @param array $args optional message arguments
			 */
			$products_merge_tag = (string) apply_filters( 'wc_memberships_message_products_merge_tag_replacement', $products_merge_tag, $products, $message, $args );

			$message = str_replace( '{products}', '<span class="wc-memberships-products-grant-access">' . $products_merge_tag . '</span>', $message );
		}

		// replace {discount} tags
		if ( ! empty( $args['post_id'] ) ) {

			// discounts up to...
			if ( false !== strpos( $message, '{discount_max}' ) || false !== strpos( $message, '{discount}' ) ) {
				$message = str_replace( array( '{discount}', '{discount_max}' ), wc_memberships()->get_member_discounts_instance()->get_product_discount_html( (int) $args['post_id'], 'max' ), $message );
			}

			// discounts starting from...
			if ( false !== strpos( $message, '{discount_min}' ) ) {
				$message = str_replace( '{discount_min}', wc_memberships()->get_member_discounts_instance()->get_product_discount_html( (int) $args['post_id'], 'min' ), $message );
			}
		}

		// replace {date}
		if ( $access_time > 0 ) {

			$message = str_replace( '{date}', date_i18n( wc_date_format(), (int) $access_time ), $message );
		}

		// replace {login*} tags
		if ( false !== stripos( $message, '{login' ) ) {
			// replace {login} (check also for capitalized variant)
			/* translators: Placeholders: %1$s - opening HTML <a> link tag, %2$s closing HTML </a> link tag */
			$message = str_replace( '{Login}', sprintf( _x( '%1$sLog in%2$s', 'Capitalized', 'woocommerce-memberships' ), '<a href="{login_url}">', '</a>' ), $message );
			/* translators: Placeholders: %1$s - opening HTML <a> link tag, %2$s closing HTML </a> link tag */
			$message = str_replace( '{login}', sprintf( _x( '%1$slog in%2$s', 'Lowercase',   'woocommerce-memberships' ), '<a href="{login_url}">', '</a>' ), $message );
			// replace {login_url}
			$message = str_replace( '{login_url}', esc_url( self::get_restricted_content_redirect_url( $args ) ), $message );
		}

		return $message;
	}


	/**
	 * Returns an array of allowed HTML elements and their attributes.
	 *
	 * By default, these match the post kses but are extended with iframe to allow embeds, and are further filterable.
	 *
	 * @see \wp_kses()
	 * @see \wp_kses_post()
	 * @see \WC_Memberships_User_Messages::get_message_html()
	 *
	 * @since 1.9.6
	 *
	 * @param string $message_code
	 * @return array associative array of allowed HTML tags and their attributes as attribute name => array values
	 */
	private static function get_message_allowed_html( $message_code ) {
		global $allowedposttags;

		$allowed_tags = ! empty( $allowedposttags ) && is_array( $allowedposttags ) ? $allowedposttags : array();

		// add iframe support
		$allowed_tags['iframe'] = array(
			'src'             => array(),
			'height'          => array(),
			'width'           => array(),
			'frameborder'     => array(),
			'allowfullscreen' => array(),
		);

		// add form inputs support
		$form_input_attributes = array(
			'action'       => array(),
			'autocomplete' => array(),
			'autofocus'    => array(),
			'class'        => array(),
			'checked'      => array(),
			'cols'         => array(),
			'data'         => array(),
			'disabled'     => array(),
			'form'         => array(),
			'height'       => array(),
			'id'           => array(),
			'list'         => array(),
			'max'          => array(),
			'maxlength'    => array(),
			'min'          => array(),
			'multiple'     => array(),
			'name'         => array(),
			'pattern'      => array(),
			'placeholder'  => array(),
			'readonly'     => array(),
			'required'     => array(),
			'rows'         => array(),
			'selected'     => array(),
			'size'         => array(),
			'step'         => array(),
			'type'         => array(),
			'value'        => array(),
			'width'        => array(),
		);
		$allowed_tags['form']     = $form_input_attributes;
		$allowed_tags['input']    = $form_input_attributes;
		$allowed_tags['textarea'] = $form_input_attributes;
		$allowed_tags['select']   = $form_input_attributes;
		$allowed_tags['optgroup'] = $form_input_attributes;
		$allowed_tags['option']   = $form_input_attributes;

		/**
		 * Filters the allowed HTML in user messages.
		 *
		 * @since 1.9.6
		 *
		 * @param array $allowed_tags array of allowed HTML elements and their attributes
		 * @param string $message_code the current message code
		 */
		$allowed_tags = (array) apply_filters( 'wc_memberships_message_allowed_html', $allowed_tags, $message_code );

		// add global attributes to a tag in the allowed html list
		return function_exists( '_wp_add_global_attributes' ) ? array_map( '_wp_add_global_attributes', $allowed_tags ) : $allowed_tags;
	}


	/**
	 * Returns a formatted login url with restricted content redirect URL.
	 *
	 * @since 1.9.0
	 *
	 * @param array $args message args
	 * @return string URL
	 */
	private static function get_restricted_content_redirect_url( $args = [] ) {

		$login_url = wc_get_page_permalink( 'myaccount' );

		$term = $post = $redirect_id = $redirect_to = null;

		// tries to determine the object to redirect to from multiple query strings
		if ( isset( $_GET['wcm_redirect_to'], $_GET['wcm_redirect_id'] ) && is_numeric( $_GET['wcm_redirect_id'] ) ) {

			$redirect_id = absint( $_GET['wcm_redirect_id'] );
			$redirect_to = (string) $_GET['wcm_redirect_to'];

			if ( in_array( $redirect_to, get_post_types(), true ) ) {
				$post = get_post( $redirect_id );
			} else {
				$term = get_term( $redirect_id, $redirect_to );
			}

		// otherwise, try and use the provided message args
		} elseif ( is_array( $args ) ) {

			if ( isset( $args['term'] ) && $args['term'] instanceof \WP_Term ) {

				$term        = $args['term'];
				$redirect_id = $term->term_id;

			} elseif ( isset( $args['post'] ) && $args['post'] instanceof \WP_Post ) {

				$post        = $args['post'];
				$redirect_id = $post->ID;
			}
		}

		// legacy handling when only a simple redirect ID parameter is passed in query strings
		if ( ! $post instanceof \WP_Post && ! $term instanceof \WP_Term ) {

			if ( isset( $_GET['r'] ) && is_numeric( $_GET['r'] ) ) {
				$redirect_id = absint( $_GET['r'] );
			} else {
				$redirect_id = get_queried_object_id();
			}

			$term = get_term( $redirect_id );
			$post = get_post( $redirect_id );
		}

		if ( $term instanceof \WP_Term ) {
			$redirect_to = $term->taxonomy;
		} elseif ( $post instanceof \WP_Post ) {
			$redirect_to = $post->post_type;
		}

		if ( (int) $redirect_id > 0 && is_string( $redirect_to ) ) {

			$login_url = add_query_arg( array(
				'wcm_redirect_to' => $redirect_to,
				'wcm_redirect_id' => $redirect_id,
			), $login_url );
		}

		return ! empty( $login_url ) ? $login_url : '#';
	}


	/**
	 * Takes a product ID and returns formatted title with link.
	 *
	 * @since 1.9.0
	 *
	 * @param \WC_Product $product a product object
	 * @return null|string HTML (the formatted product title)
	 */
	private static function get_product_link_html( WC_Product $product ) {

		$permalink = $product->get_permalink();
		$title     = $product->get_title();

		/* @type \WC_Product_Variation $product special handling for variations */
		if ( $product->is_type( 'variation' ) ) {

			$attributes = $product->get_variation_attributes();

			foreach ( $attributes as $attr_key => $attribute ) {
				$attributes[ $attr_key ] = ucfirst( $attribute );
			}

			$title .= ' &ndash; ' . implode( ', ', $attributes );
		}

		return sprintf( '<a href="%s">%s</a>', esc_url( $permalink ), wp_kses_post( $title ) );
	}


	/**
	 * Returns a list of products that grant access or discount to a piece of content or a product.
	 *
	 * @since 1.9.0
	 *
	 * @param \WP_Post|\WP_Term $object post or term object that user is accessing
	 * @param string $rule_type the rule type for which access granting products are relevant for (e.g. to get a discount or get access)
	 * @param array $args optional arguments that may include specific products
	 * @return int[] array of product IDs
	 */
	private static function get_products_that_grant_access_or_discount( $object, $rule_type = '', $args = array() ) {

		// if passed in arguments, use the specified products
		if ( ! empty( $args['products'] ) && is_array( $args['products'] ) ) {

			$products = wc_memberships()->get_rules_instance()->get_products_to_purchase_from_rules( $args['products'], $object, $rule_type, $args );

		// if no products have been specified, then try to evaluate the restricted object and get products from rules:
		} else {

			if ( 'purchasing_discount' === $rule_type ) {
				$products = wc_memberships()->get_restrictions_instance()->get_products_that_grant_discount( $object, $args );
			} else {
				$products = wc_memberships()->get_restrictions_instance()->get_products_that_grant_access( $object, $args );
			}
		}

		return $products;
	}


	/**
	 * Returns CSS classes for the restriction message.
	 *
	 * @since 1.9.0
	 *
	 * @param string $type message type (underscored)
	 * @param array $args optional arguments
	 * @return string[] array of CSS classes (dashed)
	 */
	private static function get_message_classes( $type, $args = [] ) {

		$classes = array( 'woocommerce-info' );

		switch ( $type ) {

			// product viewing restricted
			case 'product_viewing_restricted_message':
				$classes = array_merge( $classes, array( 'wc-memberships-restriction-message', 'wc-memberships-restricted-content-message' ) );
			break;

			// product purchasing restricted
			case 'product_purchasing_restricted_message':
				$classes = array_merge( $classes, array( 'wc-memberships-restriction-message', 'wc-memberships-product-purchasing-restricted-message' ) );
			break;

			// product viewing / purchasing delayed
			case 'product_access_delayed_message':
				$classes = array_merge( $classes, array( 'wc-memberships-restriction-message', 'wc-memberships-product-access-delayed-message' ) );
			break;

			// product purchasing discount
			case 'product_discount_message':
			case 'product_discount_message_no_products':
				$classes = array_merge( $classes, array( 'wc-memberships-member-discount-message' ) );
			break;

			// product category restricted / delayed
			case 'product_category_viewing_restricted_message':
			case 'product_category_viewing_restricted_message_no_products':
			case 'product_category_viewing_delayed_message':
				$classes = array_merge( $classes, array( 'wc-memberships-restriction-message', 'wc-memberships-product-category-viewing-restricted-message' ) );
			break;

			// content (post, page, post type...) restricted / delayed
			default:
				$classes = array_merge( $classes, array( 'wc-memberships-restriction-message', 'wc-memberships-message', 'wc-memberships-content-restricted-message' ) );
			break;
		}

		// apply any custom classes
		if ( ! empty( $args['classes'] ) ) {
			if ( is_string( $args['classes'] ) ) {
				$classes[] = $args['classes'];
			} elseif ( is_array( $args['classes'] ) ) {
				$classes = array_merge( $classes, $args['classes'] );
			}
		}

		/**
		 * Get restriction message CSS classes.
		 *
		 * @since 1.9.0
		 *
		 * @param string[] $classes CSS classes as a string array
		 * @param string $type the requested message type
		 */
		return apply_filters( 'wc_memberships_message_classes', $classes, $type );
	}


	/**
	 * Trims a post's excerpt (helper method)
	 *
	 * @see \wp_trim_excerpt()
	 * @see \WC_Memberships_User_Messages::get_restricted_content_excerpt()
	 *
	 * @since 1.10.1
	 *
	 * @param \WP_Post $the_post the post object
	 * @return string the trimmed excerpt
	 */
	private static function trim_excerpt( $the_post ) {

		if ( ! $the_post instanceof \WP_Post ) {
			$post = get_post( $the_post );
		} else {
			$post = $the_post;
		}

		if ( ! $post instanceof \WP_Post ) {
			return '';
		}

		$excerpt = $post->post_content;

		/**
		 * Filters whether to strip shortcodes from restricted content excerpts.
		 *
		 * @since 1.10.4
		 *
		 * @param bool $strip_shortcodes default true
		 */
		if ( true === (bool) apply_filters( 'wc_memberships_message_excerpt_strip_shortcodes', true ) ) {
			$excerpt = strip_shortcodes( $excerpt );
		}

		/**
		 * Filters whether to apply `the_content` filter on excerpts.
		 *
		 * @since 1.12.3
		 *
		 * @param bool $apply_the_content_filter determines whether to apply `the_content` filter. Defaults to true
		 */
		if ( true === (bool) apply_filters( 'wc_memberships_message_excerpt_apply_the_content_filter', true ) ) {

			/** This filter is documented in wp-includes/post-template.php */
			$excerpt = apply_filters( 'the_content', $excerpt );
		}

		$excerpt = str_replace( ']]>', ']]&gt;', $excerpt );

		/**
		 * Filters the excerpt length (number of words).
		 *
		 * @since 1.10.1
		 *
		 * @param int $length the excerpt length (number of words)
		 * @param \WP_Post $post the post object
		 */
		$excerpt_length = absint( apply_filters( 'wc_memberships_restricted_excerpt_length', (int) get_option( 'wc_memberships_excerpt_length', 55 ), $post ) );

		/** This filter is documented in wp-includes/formatting.php */
		$excerpt_more = (string) apply_filters( 'excerpt_more', ' ' . '[&hellip;]' );

		/**
		 * Filters the string in the "more" link displayed after a trimmed excerpt.
		 *
		 * @since 1.10.1
		 *
		 * @param string $more_string the string shown within the more link
		 */
		$excerpt_more = (string) apply_filters( 'wc_memberships_restricted_excerpt_more', $excerpt_more );

		/**
		 * Filters the restricted content being trimmed.
		 *
		 * @since 1.15.0
		 *
		 * @param string $trimmed_excerpt trimmed excerpt
		 * @param string $excerpt original $excerpt
		 * @param int $excerpt_length length of the intended trim
		 * @param string $excerpt_more word/symbol used to crop the excerpt at the set length
		 */
		return (string) apply_filters( 'wc_memberships_trimmed_restricted_excerpt', wp_trim_words( $excerpt, $excerpt_length, $excerpt_more ), $excerpt, $excerpt_length, $excerpt_more );
	}


	/**
	 * Gets a list of available merge tags for user messages.
	 *
	 * @since 1.15.0
	 *
	 * @param bool $with_descriptions whether to return some help text related to each table, or an array of merge tag keys only
	 * @return array|string[]
	 */
	public static function get_available_merge_tags( $with_descriptions = false ) {

		$merge_tags = [
			/* translators: Placeholder: %s shows a message merge tag to be used */
			'products'  => __( '%s automatically inserts the product(s) needed to gain access.', 'woocommerce-memberships' ),
			/* translators: Placeholder: %s shows a message merge tag to be used */
			'date'      => __( '%s inserts the date when the member will gain access to delayed content.', 'woocommerce-memberships' ),
			/* translators: Placeholder: %s shows a message merge tag to be used */
			'discount'  => __( '%s inserts the highest product discount obtainable by becoming a member.', 'woocommerce-memberships' ),
			/* translators: Placeholder: %s shows a message merge tag to be used */
			'login_url' => __( '%s inserts the URL to the "My Account" page with the login form.', 'woocommerce-memberships' ),
			/* translators: Placeholder: %s shows a message merge tag to be used */
			'login'     => __( '%s inserts a login link to the "My Account" page with the login form.', 'woocommerce-memberships' ),
		];

		return $with_descriptions ? $merge_tags : array_keys( $merge_tags );
	}


	/**
	 * Returns a restricted content notice for admins.
	 *
	 * Message intended for admins to tell them they are viewing restricted content non-members wouldn't have access to.
	 *
	 * @since 1.10.0
	 *
	 * @return string HTML
	 */
	public static function get_admin_message_html() {

		$message = '';

		if ( self::show_admin_message() ) {

			/* translators: Placeholders: %1$s - <strong>, %2$s - </strong> */
			$text = sprintf( __( '%1$sHeads up!%2$s Restricted content is visible to you as an administrator, but will be restricted for guests and non-members.', 'woocommerce-memberships' ),
				'<strong>', '</strong>'
			);

			$message = '<div class="woocommerce wc-memberships wc-memberships-frontend-banner admin-restricted-content-notice">' . wp_kses_post( $text ) . ' <a href="#" class="dismiss-link">' . __( 'Dismiss', 'woocommerce-memberships' ) . '</a></div>';
		}

		return $message;
	}


	/**
	 * Determines if we should be showing an admin message for this user.
	 *
	 * @since 1.10.4
	 *
	 * @return bool true if we should show the notice to the current user
	 */
	public static function show_admin_message() {

		$current_user    = wp_get_current_user();
		$display_message =    'yes' === get_option( 'wc_memberships_admin_restricted_content_notice' )
		                   && $current_user->ID > 0
		                   && 'no' !== get_user_meta( $current_user->ID, '_wc_memberships_show_admin_restricted_content_notice', true )
		                   && current_user_can( 'wc_memberships_access_all_restricted_content' );

		/**
		 * Toggles whether to display an admin notice when an admin is browsing content restricted to non-members.
		 *
		 * @since 1.10.1
		 *
		 * @param bool $display_message true for users who have not yet seen this on new installations
		 */
		return (bool) apply_filters( 'wc_memberships_display_restricted_content_admin_notice', $display_message );
	}


}
