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

use SkyVerge\WooCommerce\PluginFramework\v5_10_13 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Memberships settings class.
 *
 * @since 1.0.0
 */
class WC_Settings_Memberships extends \WC_Settings_Page {


	/**
	 * Constructs the "Memberships" settings tab.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->id    = 'memberships';
		$this->label = __( 'Memberships', 'woocommerce-memberships' );

		parent::__construct();

		// set the endpoint slug for Members Area in My Account
		add_filter( 'woocommerce_settings_pages', [ $this, 'add_my_account_endpoints_options' ] );

		add_action( 'woocommerce_admin_field_redirect_members_upon_login', [ $this, 'output_redirect_members_upon_login_setting_field' ] );
	}


	/**
	 * Filters WooCommerce Settings sections to add new sections for the Memberships tab.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_sections() {

		$sections = array(
			''         => __( 'General', 'woocommerce-memberships' ),  // handles general content settings
			'products' => __( 'Products', 'woocommerce-memberships' ), // handles products settings
			'messages' => __( 'Messages', 'woocommerce-memberships' ), // handles messages
		);

		return apply_filters( 'woocommerce_get_sections_' . $this->id, $sections );
	}


	/**
	 * Returns the settings array.
	 *
	 * @since 1.0.0
	 *
	 * @param string $current_section optional, defaults to empty string
	 * @return array array of settings
	 */
	public function get_settings( $current_section = '' ) {

		if ( 'products' === $current_section ) {

			/**
			 * Filter Memberships products settings.
			 *
			 * @since 1.0.0
			 *
			 * @param array $settings associative array of the plugin settings
			 */
			$settings = (array) apply_filters( 'wc_memberships_products_settings', [

				[
					'name' => __( 'Products', 'woocommerce-memberships' ),
					'type' => 'title',
				],

				[
					'type'    => 'checkbox',
					'id'      => 'wc_memberships_apply_member_discounts_when_purchasing_membership',
					'name'    => __( 'Apply discounts when purchasing membership', 'woocommerce-memberships' ),
					'desc'    => __( 'Apply plan discounts to eligible products when the cart contains a product that grants access to the membership plan.', 'woocommerce-memberships' ),
					'default' => 'no'
				],

				[
					'type'    => 'checkbox',
					'id'      => 'wc_memberships_allow_cumulative_access_granting_orders',
					'name'    => __( 'Allow cumulative purchases', 'woocommerce-memberships' ),
					'desc'    => __( 'Purchasing products that grant access to a membership in the same order extends the length of the membership.', 'woocommerce-memberships' ),
					'default' => 'no',
				],

				[
					'type'    => 'checkbox',
					'id'      => 'wc_memberships_exclude_on_sale_products_from_member_discounts',
					'name'    => __( 'Exclude products on sale from member discounts', 'woocommerce-memberships' ),
					'desc'    => __( 'Do not apply member discounts from any membership plan discount rules to products that are currently on sale.', 'woocommerce-memberships' ),
					'default' => 'no',
				],

				[
					'type'    => 'checkbox',
					'id'      => 'wc_memberships_hide_restricted_products',
					'name'    => __( 'Hide restricted products', 'woocommerce-memberships' ),
					'desc'    => __( 'If enabled, products with viewing restricted will be hidden from the shop catalog. Products will still be accessible directly, unless Content Restriction Mode is "Hide completely".', 'woocommerce-memberships' ),
					'default' => 'no',
				],

				[
					'type' => 'sectionend',
				],

			] );

		} elseif ( 'messages' === $current_section ) {

			$legend  = '<p>' . __( 'Customize restriction and discount messages displayed to non-members and members. Basic HTML is allowed. You can also use the following merge tags:', 'woocommerce-memberships' ) . '</p>';

			$legend .= '<ul>';

			foreach ( \WC_Memberships_User_Messages::get_available_merge_tags( true ) as $merge_tag => $help_text ) {

				$merge_tag = "<strong><code>{{$merge_tag}}</code></strong>";

				$legend .= '<li>' . sprintf( $help_text, $merge_tag ) . '</li>';
			}

			$legend .= '</ul>';

			/**
			 * Filters Memberships products settings.
			 *
			 * @since 1.0.0
			 *
			 * @param array $settings associative array of the plugin settings
			 */
			$settings = (array) apply_filters( 'wc_memberships_messages_settings', [

				[
					'title' => __( 'Messages', 'woocommerce-memberships' ),
					'type'  => 'title',
					'desc'  => $legend,
				],

				[
					'type'    => 'select',
					'class'   => 'wc-enhanced-select js-select-edit-message-group',
					'name'    => __( 'Edit messages for:', 'woocommerce-memberships' ),
					'options' => [
						'.messages-group-posts'     => __( 'Blog posts restriction', 'woocommerce-memberships' ),
						'.messages-group-pages'     => __( 'Pages restriction', 'woocommerce-memberships' ),
						'.messages-group-content'   => __( 'Content restriction', 'woocommerce-memberships' ),
						'.messages-group-products'  => __( 'Products restriction', 'woocommerce-memberships' ),
						'.messages-group-discounts' => __( 'Purchasing discount', 'woocommerce-memberships' ),
					],
					'default' => 'posts',
				],

				[
					'type' => 'sectionend',
				],

				// =====================
				//  Blog Posts Messages
				// ============-========

				[
					'name'  => __( 'Post restricted messages', 'woocommerce-memberships' ),
					'type'  => 'title',
					'class' => 'messages-group-posts',
					'desc'  => __( 'The following messages may be shown to members and non-members when trying to access a blog post.', 'woocommerce-memberships' ),
				],

				[
					'type'     => 'textarea',
					'id'       => 'wc_memberships_messages[post_content_restricted_message]',
					'class'    => 'input-text wide-input messages-group-posts',
					'name'     => __( 'Post restricted (product purchase required)', 'woocommerce-memberships' ),
					'desc'     => __( 'Displays when purchase is required to view the blog post.', 'woocommerce-memberships' ),
					'default'  => \WC_Memberships_User_Messages::get_message( 'post_content_restricted_message' ),
					'desc_tip' => __( 'Message displayed if visitor does not have access to the post, but can purchase it.', 'woocommerce-memberships' ),
				],

				[
					'type'     => 'textarea',
					'id'       => 'wc_memberships_messages[post_content_restricted_message_no_products]',
					'class'    => 'input-text wide-input messages-group-posts',
					'name'     => __( 'Post restricted (membership required)', 'woocommerce-memberships' ),
					'desc'     => __( 'Displays if the blog post is restricted to a membership that cannot be purchased.', 'woocommerce-memberships' ),
					'default'  => \WC_Memberships_User_Messages::get_message( 'post_content_restricted_message_no_products' ),
					'desc_tip' => __( 'Message displayed if visitor does not have access to the post and no products can grant access.', 'woocommerce-memberships' ),
				],

				[
					'type'     => 'textarea',
					'id'       => 'wc_memberships_messages[post_content_delayed_message]',
					'class'    => 'input-text wide-input messages-group-posts',
					'name'     => __( 'Post delayed (members)', 'woocommerce-memberships' ),
					'desc'     => __( 'Displays if access to blog post is not available yet.', 'woocommerce-memberships' ),
					'default'  => \WC_Memberships_User_Messages::get_message( 'post_content_delayed_message' ),
					'desc_tip' => __( 'Message displayed if the current user is a member but does not have access to the post yet.', 'woocommerce-memberships' ),
				],

				[
					'type'  => 'sectionend',
					'class' => 'messages-group-posts',
				],

				// ================
				//  Pages Messages
				// ================

				[
					'name'  => __( 'Page restricted messages', 'woocommerce-memberships' ),
					'type'  => 'title',
					'class' => 'messages-group-pages',
					'desc'  => __( 'The following messages may be shown to members and non-members when trying to access a page.', 'woocommerce-memberships' ),
				],

				[
					'type'     => 'textarea',
					'id'       => 'wc_memberships_messages[page_content_restricted_message]',
					'class'    => 'input-text wide-input messages-group-pages',
					'name'     => __( 'Page restricted (product purchase required)', 'woocommerce-memberships' ),
					'desc'     => __( 'Displays when purchase is required to view the page.', 'woocommerce-memberships' ),
					'default'  => \WC_Memberships_User_Messages::get_message( 'page_content_restricted_message' ),
					'desc_tip' => __( 'Message displayed if visitor does not have access to the page, but can purchase it.', 'woocommerce-memberships' ),
				],

				[
					'type'     => 'textarea',
					'id'       => 'wc_memberships_messages[page_content_restricted_message_no_products]',
					'class'    => 'input-text wide-input messages-group-pages',
					'name'     => __( 'Page restricted (membership required)', 'woocommerce-memberships' ),
					'desc'     => __( 'Displays if the page is restricted to a membership that cannot be purchased.', 'woocommerce-memberships' ),
					'default'  => \WC_Memberships_User_Messages::get_message( 'page_content_restricted_message_no_products' ),
					'desc_tip' => __( 'Message displayed if visitor does not have access to the page and no products can grant access.', 'woocommerce-memberships' ),
				],

				[
					'type'     => 'textarea',
					'id'       => 'wc_memberships_messages[page_content_delayed_message]',
					'class'    => 'input-text wide-input messages-group-pages',
					'name'     => __( 'Page delayed (members)', 'woocommerce-memberships' ),
					'desc'     => __( 'Displays if access to page is not available yet.', 'woocommerce-memberships' ),
					'default'  => \WC_Memberships_User_Messages::get_message( 'page_content_delayed_message' ),
					'desc_tip' => __( 'Message displayed if the current user is a member but does not have access to the page yet.', 'woocommerce-memberships' ),
				],

				[
					'type'  => 'sectionend',
					'class' => 'messages-group-pages',
				],

				// ==========================
				//  Generic Content Messages
				// ==========================

				[
					'title' => __( 'Content restricted messages', 'woocommerce-memberships' ),
					'class' => 'messages-group-content',
					'type'  => 'title',
					'desc'  => __( 'The following messages may be shown to members and non-members when trying to access content that is not a product, blog post, or page (such as a custom content type).', 'woocommerce-memberships' ),
				],

				[
					'type'     => 'textarea',
					'id'       => 'wc_memberships_messages[content_restricted_message]',
					'class'    => 'input-text wide-input messages-group-content',
					'name'     => __( 'Content restricted (product purchase required)', 'woocommerce-memberships' ),
					'desc'     => __( 'Displays when purchase is required to view the content.', 'woocommerce-memberships' ),
					'default'  => \WC_Memberships_User_Messages::get_message( 'content_restricted_message' ),
					'desc_tip' => __( 'Message displayed if visitor does not have access to the content, but can purchase it.', 'woocommerce-memberships' ),
				],

				[
					'type'     => 'textarea',
					'id'       => 'wc_memberships_messages[content_restricted_message_no_products]',
					'class'    => 'input-text wide-input messages-group-content',
					'name'     => __( 'Content restricted (membership required)', 'woocommerce-memberships' ),
					'desc'     => __( 'Displays if the content is restricted to a membership that cannot be purchased.', 'woocommerce-memberships' ),
					'default'  => \WC_Memberships_User_Messages::get_message( 'content_restricted_message_no_products' ),
					'desc_tip' => __( 'Message displayed if visitor does not have access to the content and no products can grant access.', 'woocommerce-memberships' ),
				],

				[
					'type'     => 'textarea',
					'id'       => 'wc_memberships_messages[content_delayed_message]',
					'class'    => 'input-text wide-input messages-group-content',
					'name'     => __( 'Content delayed (members)', 'woocommerce-memberships' ),
					'desc'     => __( 'Displays if access to content is not available yet.', 'woocommerce-memberships' ),
					'default'  => \WC_Memberships_User_Messages::get_message( 'content_delayed_message' ),
					'desc_tip' => __( 'Message displayed if the current user is a member but does not have access to content yet.', 'woocommerce-memberships' ),
				],

				[
					'type'  => 'sectionend',
					'class' => 'messages-group-content',
				],

				// ===================
				//  Products Messages
				// ===================

				[
					'name'  => __( 'Product restriction messages', 'woocommerce-memberships' ),
					'type'  => 'title',
					'class' => 'messages-group-products',
					'desc'  =>  __( 'The following messages may be shown to members and non-members when trying to view or purchase products.', 'woocommerce-memberships' ),
				],

				[
					'type'     => 'textarea',
					'id'       => 'wc_memberships_messages[product_access_delayed_message]',
					'class'    => 'input-text wide-input messages-group-products',
					'name'     => __( 'Product viewing or purchasing delayed (members)', 'woocommerce-memberships' ),
					'desc'     => __( 'Displays if access for viewing or purchasing a product is not available yet.', 'woocommerce-memberships' ),
					'default'  => \WC_Memberships_User_Messages::get_message( 'product_access_delayed_message' ),
					'desc_tip' => __( 'Message displayed if the current user is a member but does not have access yet to view or purchase the product.', 'woocommerce-memberships' ),
				],

				[
					'type'     => 'textarea',
					'id'       => 'wc_memberships_messages[product_viewing_restricted_message]',
					'class'    => 'input-text wide-input messages-group-products',
					'name'     => __( 'Product viewing restricted (purchase required)', 'woocommerce-memberships' ),
					'desc'     => __( 'Displays when purchase is required to view the product.', 'woocommerce-memberships' ),
					'default'  => \WC_Memberships_User_Messages::get_message( 'product_viewing_restricted_message' ),
					'desc_tip' => __( 'Message displayed if viewing is restricted to members but access can be purchased.', 'woocommerce-memberships' ),
				],

				[
					'type'     => 'textarea',
					'id'       => 'wc_memberships_messages[product_viewing_restricted_message_no_products]',
					'class'    => 'input-text wide-input messages-group-products',
					'name'     => __( 'Product viewing restricted (membership required)', 'woocommerce-memberships' ),
					'desc'     => __( 'Displays if viewing is restricted to a membership that cannot be purchased.', 'woocommerce-memberships' ),
					'default'  => WC_Memberships_User_Messages::get_message( 'product_viewing_restricted_message_no_products' ),
					'desc_tip' => __( 'Message displayed if viewing is restricted to members and no products can grant access.', 'woocommerce-memberships' ),
				],

				[
					'type'     => 'textarea',
					'id'       => 'wc_memberships_messages[product_purchasing_restricted_message]',
					'class'    => 'input-text wide-input messages-group-products',
					'name'     => __( 'Product buying restricted (purchase required)', 'woocommerce-memberships' ),
					'desc'     => __( 'Displays when purchase is required to buy the product.', 'woocommerce-memberships' ),
					'default'  => \WC_Memberships_User_Messages::get_message( 'product_purchasing_restricted_message' ),
					'desc_tip' => __( 'Message displayed if purchasing is restricted to members but access can be purchased.', 'woocommerce-memberships' ),
				],

				[
					'type'     => 'textarea',
					'id'       => 'wc_memberships_messages[product_purchasing_restricted_message_no_products]',
					'class'    => 'input-text wide-input messages-group-products',
					'name'     => __( 'Product buying restricted (membership required)', 'woocommerce-memberships' ),
					'desc'     => __( 'Displays if purchasing is restricted to a membership that cannot be purchased.', 'woocommerce-memberships' ),
					'default'  => \WC_Memberships_User_Messages::get_message( 'product_purchasing_restricted_message_no_products' ),
					'desc_tip' => __( 'Message displayed if purchasing is restricted to members and no products can grant access.', 'woocommerce-memberships' ),
				],

				[
					'type'  => 'sectionend',
					'class' => 'messages-group-products',
				],

				// ================
				//  Other Messages
				// ================

				[
					'name'  => __( 'Purchasing discount', 'woocommerce-memberships' ),
					'type'  => 'title',
					'desc'  => __( 'The following messages may be used to inform non-members of discounts.', 'woocommerce-memberships' ),
					'class' => 'messages-group-discounts',
				],

				[
					'type'     => 'textarea',
					'id'       => 'wc_memberships_messages[product_discount_message]',
					'class'    => 'input-text wide-input messages-group-discounts',
					'name'     => __( 'Product discounted (purchase required)', 'woocommerce-memberships' ),
					'desc'     => __( 'Message displayed to non-members if the product has a member discount.', 'woocommerce-memberships' ),
					'default'  => \WC_Memberships_User_Messages::get_message( 'product_discount_message' ),
					'desc_tip' => __( 'Displays below add to cart buttons. Leave blank to disable.', 'woocommerce-memberships' ),
				],

				[
					'type'     => 'textarea',
					'id'       => 'wc_memberships_messages[product_discount_message_no_products]',
					'class'    => 'input-text wide-input messages-group-discounts',
					'name'     => __( 'Product discounted (membership required)', 'woocommerce-memberships' ),
					'desc'     => __( 'Message displayed to non-members if the product has a member discount, but no products can grant access.', 'woocommerce-memberships' ),
					'default'  => \WC_Memberships_User_Messages::get_message( 'product_discount_message_no_products' ),
					'desc_tip' => __( 'Displays below add to cart buttons. Leave blank to disable.', 'woocommerce-memberships' ),
				],

				[
					'type'     => 'select',
					'id'       => 'wc_memberships_display_member_login_notice',
					'name'     => __( 'Member discount login reminder', 'woocommerce-memberships' ),
					'options'  => [
						'never'    => __( 'Do not show', 'woocommerce-memberships' ),
						'cart'     => __( 'Show on cart page', 'woocommerce-memberships' ),
						'checkout' => __( 'Show on checkout page', 'woocommerce-memberships' ),
						'both'     => __( 'Show on both cart & checkout page', 'woocommerce-memberships' ),
					],
					'class'    => 'wc-enhanced-select messages-group-discounts',
					'desc_tip' => __( 'Select when & where to display login reminder notice for guests if products in cart have member discounts.', 'woocommerce-memberships' ),
					'default'  => 'both',
				],

				[
					'type'        => 'textarea',
					'id'          => 'wc_memberships_messages[member_login_message]',
					'class'       => 'input-text wide-input messages-group-discounts',
					'name'        => __( 'Member discount login message', 'woocommerce-memberships' ),
					'desc'        => __( 'Message to remind members to log in to claim a discount. Leave blank to use the default log in message.', 'woocommerce-memberships' ),
					/* translators: Placeholder: %s - a message text example */
					'placeholder' => sprintf( __( 'for example: "%s"', 'woocommerce-memberships' ), \WC_Memberships_User_Messages::get_message( 'cart_items_discount_message' ) ),
					'default'     => \WC_Memberships_User_Messages::get_message( 'member_login_message' ),
				],

				[
					'type'  => 'sectionend',
					'class' => 'messages-group-discounts',
				],

			] );

		} else { // general section

			$redirect_login_options =  [
				'no_redirect'  => [
					'label'    => __( 'No redirect', 'woocommerce-memberships' ),
					'desc'     => '',
					'disabled' => false,
				],
				'members_area' => [
					'label'    => __( 'Members area', 'woocommerce-memberships' ),
					'desc'     => __( "Redirect members upon login to the \"My Account - Members Area\" page. If the Members Area is not enabled on a member's plan, the member will not be redirected.", 'woocommerce-memberships' ),
					'disabled' => true,
				],
				'site_page'    => [
					'label'    => __( 'Site page', 'woocommerce-memberships' ),
					'desc'     => __( "Redirect members upon login to a site page. If this page is restricted from a member's plan the member won't be redirected.", 'woocommerce-memberships' ),
					'disabled' => false,
				],
			];

			foreach ( wc_memberships_get_membership_plans() as $plan ) {

				if ( count( $plan->get_members_area_sections() ) > 0 ) {

					$redirect_login_options['members_area']['disabled'] = false;
					break;
				}
			}

			/**
			 * Filters Memberships general settings.
			 *
			 * @since 1.0.0
			 *
			 * @param array $settings associative array of the plugin settings
			 */
			$settings = (array) apply_filters( 'wc_memberships_general_settings', array_merge( [

				[
					'name' => __( 'General', 'woocommerce-memberships' ),
					'type' => 'title',
				],

				[
					'type'     => 'select',
					'id'       => 'wc_memberships_restriction_mode',
					'name'     => __( 'Content restriction mode', 'woocommerce-memberships' ),
					'options'  => [
						'hide'         => __( 'Hide completely', 'woocommerce-memberships' ),
						'hide_content' => __( 'Hide content only', 'woocommerce-memberships' ),
						'redirect'     => __( 'Redirect to page', 'woocommerce-memberships' ),
					],
					'class'    => 'wc-enhanced-select',
					'desc_tip' => __( 'Specifies the way content is restricted: whether to show nothing, excerpts, or send to a landing page.', 'woocommerce-memberships' ),
					'desc'     => '
						<ul id="wc_memberships_restriction_mode_desc">
							<li class="hide" style="display:none;">' . __( '"Hide completely" removes all traces of content for non-members, search engines and 404s restricted pages.', 'woocommerce-memberships' ) . '</li>
							<li class="hide_content" style="display:none;">' . __( '"Hide content only" will show items in archives, but protect page or post content and comments.', 'woocommerce-memberships' ) . '</li>
							<li class="redirect" style="display:none;"> </li>
						</ul>
					',
					'default'  => 'hide_content',
				],

				[
					'title'    => __( 'Redirect page', 'woocommerce-memberships' ),
					'desc'     => __( 'Select the page to redirect non-members to - should contain the [wcm_content_restricted] shortcode.', 'woocommerce-memberships' ),
					'id'       => 'wc_memberships_redirect_page_id',
					'type'     => 'single_select_page',
					'class'    => 'wc-enhanced-select-nostd js-redirect-page',
					'css'      => 'min-width:300px;',
					'desc_tip' => true,
				],

				[
					'type'    => 'checkbox',
					'id'      => 'wc_memberships_show_excerpts',
					'name'    => __( 'Show excerpts', 'woocommerce-memberships' ),
					'desc'    =>
						'<span class="show-if-hide-content-only-restriction-mode">' . __( 'If enabled, an excerpt of the protected content will be displayed to non-members & search engines.', 'woocommerce-memberships' ) . '</span>' .
						'<span class="show-if-redirect-restriction-mode" style="display:none;">' . __( 'If enabled, an excerpt of the protected content will be displayed to search engines. Non-members can view excerpts on archive pages only.', 'woocommerce-memberships' ) . '</span>',
					'default' => 'yes',
				],

				[
					'name'              => __( 'Excerpt length (words)', 'woocommerce-memberships' ),
					'desc'              => __( 'Number of words shown to non-members and search engines.', 'woocommerce-memberships' ),
					'desc_tip'          => true,
					'id'                => 'wc_memberships_excerpt_length',
					'default'           => 55, // WP default
					'placeholder'       => '55',
					'type'              => 'number',
					'css'               => 'width:100px;',
					'class'             => 'wc_memberships_excerpt_length inline-description',
					'custom_attributes' => [
						'min'  => 0,
						'step' => 1,
					],
				],

				[
					'type'    => 'checkbox',
					'id'      => 'wc_memberships_inherit_restrictions',
					'name'    => __( 'Inherit parent restrictions', 'woocommerce-memberships' ),
					'desc'    => __( 'If enabled, hierarchical post types such as pages will apply restriction rules to their children.', 'woocommerce-memberships' ),
					'default' => 'no',
				],

				[
					'type'     => 'redirect_members_upon_login',
					'id'       => 'wc_memberships_redirect_upon_member_login',
					'name'     => __( 'Redirect members upon login', 'woocommerce-memberships' ),
					'desc'     => sprintf(
						/* translators: Placeholders: %1$s - emphasis HTML opening tags, %2$s - emphasis HTML closing tags */
						esc_html__( 'Choose a page to redirect members to when they log into your site. This will %1$snot%2$s redirect members who log in from the Cart or Checkout, or login prompts on a restricted content message / redirect page (if applicable based on the Content restriction mode).', 'woocommerce-memberships' ),
						'<em><strong>', '</strong></em>'
					),
					'options'  => $redirect_login_options,
					'default'  => 'no_redirect',
				],

				[
					'type'  => 'single_select_page',
					'id'    => 'wc_memberships_member_login_redirect_page_id',
					'name'  => __( 'Login redirect page', 'woocommerce-memberships' ),
					'class' => 'wc-enhanced-select-nostd js-redirect-page',
					'css'   => 'min-width:300px;',
				],

				[
					'type'     => 'sectionend',
				],

			], $this->get_roles_settings(), $this->get_privacy_settings() ) );
		}

		/**
		 * Filters Memberships Settings.
		 *
		 * @since 1.0.0
		 *
		 * @param array $settings array of the plugin settings
		 * @param string $current_section the current section being output
		 */
		return apply_filters( "woocommerce_get_settings_{$this->id}", $settings, $current_section );
	}


	/**
	 * Gets settings for users role handling.
	 *
	 * @since 1.21.0
	 *
	 * @return array
	 */
	private function get_roles_settings() : array {

		if ( ! function_exists( 'get_editable_roles' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/user.php' );
		}

		foreach ( get_editable_roles() as $role => $details ) {
			$roles_options[ $role ] = translate_user_role( $details['name'] );
		}

		asort( $roles_options );

		$roles_options = array_merge( [ '' => _x( 'WordPress default', 'Default member user role', 'woocommerce-memberships' ) ], $roles_options );

		$roles_settings = [

				[
					'title' => __( 'Member Roles', 'woocommerce-memberships' ),
					'type'  => 'title',
				],

				[
					'title'    => __( 'Enable member roles', 'woocommerce-memberships' ),
					'desc'     => __( 'Choose the roles to assign to active and inactive members.', 'woocommerce-memberships' ),
					/* translators: Placeholders %1$s - opening <em> HTML tag, %2$s - closing </em> HTML tag, %3$s - opening <em> HTML tag, %4$s - closing </em> HTML tag */
					'desc_tip' => sprintf( __( 'Users with the %1$sadministrator%2$s or %3$sshop manager%4$s role will never be allocated these roles to prevent locking out admin users.', 'woocommerce-memberships' ), '<em>', '</em>', '<em>', '</em>' ),
					'id'       => 'wc_memberships_assign_user_roles_to_members',
					'type'     => 'checkbox',
					'default'  => 'no',
				],

				[
					'name'     => __( 'Member default role', 'woocommerce-memberships' ),
					'desc'     => __( 'When a membership is activated, new members will be assigned this role.', 'woocommerce-memberships' ),
					'id'       => 'wc_memberships_active_member_user_role',
					'css'      => 'min-width:150px;',
					'default'  => 'customer',
					'type'     => 'select',
					'options'  => $roles_options,
					'desc_tip' => true,
				],

				[
					'name'     => __( 'Inactive member role', 'woocommerce-memberships' ),
					'desc'     => __( 'If a member becomes inactive (e.g., the membership is cancelled or expired), the member will be assigned this role.', 'woocommerce-memberships' ),
					'id'       => 'wc_memberships_inactive_member_user_role',
					'css'      => 'min-width:150px;',
					'default'  => 'customer',
					'type'     => 'select',
					'options'  => $roles_options,
					'desc_tip' => true,
				],

				[
					'type' => 'sectionend',
				],
		];

		/**
		 * Filters the roles settings.
		 *
		 * @since 1.21.0
		 *
		 * @param array $roles_settings associative array
		 */
		return (array) apply_filters( 'wc_memberships_roles_settings', $roles_settings );
	}


	/**
	 * Gets privacy settings.
	 *
	 * @since 1.21.0
	 *
	 * @return array
	 */
	private function get_privacy_settings() : array {

		// add this only if GDPR handling is available in WordPress
		return version_compare( get_bloginfo( 'version' ), '4.9.5', '>' ) ? [
			[
				'name'    => __( 'Privacy', 'woocommerce-memberships' ),
				'type'    => 'title',
			],
			[
				'type'    => 'checkbox',
				'id'      => 'wc_memberships_privacy_erasure_request_delete_user_memberships',
				'name'    => __( 'Account erasure requests', 'woocommerce-memberships' ),
				/* translators: Placeholders: %1$s - opening HTML <a> link tag , %2$s - closing HTML </a> link tag */
				'desc'    => sprintf( __( 'Delete all matching memberships when %1$susers request to erase their personal data%2$s.', 'woocommerce-memberships' ), '<a href="' . admin_url( 'tools.php?page=remove_personal_data' ) . '">', '</a>' ),
				'default' => 'no',
			],
			[
				'type'    => 'sectionend',
			],
		] : [];
	}


	/**
	 * Outputs the input field for "Redirect members upon login".
	 *
	 * @internal
	 *
	 * @since 1.16.0
	 *
	 * @param array $field field data
	 */
	public function output_redirect_members_upon_login_setting_field( $field ) {

		if ( empty( $field['value'] ) ) {
			$value = $field['default'];
		} else {
			$value = $field['value'];
		}

		// if the chosen option has become unavailable, revert to default
		if ( isset( $field['options'][ $value ]['disabled'] ) && (bool) $field['options'][ $value ]['disabled'] ) {
			$value = $field['default'];
		}

		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_html( $field['title'] ); ?></label>
			</th>
			<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $field['type'] ) ); ?>">
				<p><?php echo wp_kses_post( $field['desc'] ); ?></p>
				<fieldset>
					<ul>
						<?php foreach ( $field['options'] as $key => $val ) : ?>
							<li>
								<label>
									<input
										type="radio"
										name="<?php echo esc_attr( $field['id'] ); ?>"
										value="<?php echo esc_attr( $key ); ?>"
										style="<?php echo esc_attr( $field['css'] ); ?>"
										class="<?php echo esc_attr( $field['class'] ); ?>"
										<?php checked( $key, $value ); ?>
										<?php disabled( true, (bool) $val['disabled'] ); ?>
									/> <?php echo esc_html( $val['label'] ); ?>
								</label>
								<br>
								<span class="description"><?php echo esc_html( $val['desc'] ); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>
				</fieldset>
			</td>
		</tr>
		<?php
	}


	/**
	 * Outputs the settings fields.
	 *
	 * @since 1.0.0
	 */
	public function output() {
		global $current_section;

		\WC_Admin_Settings::output_fields( $this->get_settings( $current_section ) );
	}


	/**
	 * Saves the settings.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function save() {
		global $current_section;

		\WC_Admin_Settings::save_fields( $this->get_settings( $current_section ) );
	}


	/**
	 * Adds custom slugs for endpoints in My Account page.
	 *
	 * Filter callback for woocommerce_account_settings.
	 *
	 * @internal
	 *
	 * @since 1.4.0
	 *
	 * @param array $settings
	 * @return array $settings
	 */
	public function add_my_account_endpoints_options( $settings ) {

		$new_settings = array();

		foreach ( $settings as $setting ) {

			$new_settings[] = $setting;

			if ( isset( $setting['id'] ) && 'woocommerce_logout_endpoint' === $setting['id'] ) {

				$new_settings[] = [
					'title'    => __( 'My Membership', 'woocommerce-memberships' ),
					'desc'     => __( 'Endpoint for the My Account &rarr; My Membership page', 'woocommerce-memberships' ),
					'id'       => 'woocommerce_myaccount_members_area_endpoint',
					'type'     => 'text',
					'default'  => 'members-area',
					'desc_tip' => true,
				];

				$new_settings[] = [
					'title'    => __( 'My Profile', 'woocommerce-memberships' ),
					'desc'     => __( 'Endpoint for the My Account &rarr; My Profile page', 'woocommerce-memberships' ),
					'id'       => 'woocommerce_myaccount_profile_fields_area_endpoint',
					'type'     => 'text',
					'default'  => 'profile',
					'desc_tip' => true,
				];
			}
		}

		return $new_settings;
	}


}
