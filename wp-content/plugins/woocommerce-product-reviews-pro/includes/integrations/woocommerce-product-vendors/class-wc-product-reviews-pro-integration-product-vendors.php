<?php
/**
 * WooCommerce Product Reviews Pro
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Product Reviews Pro to newer
 * versions in the future. If you wish to customize WooCommerce Product Reviews Pro for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-product-reviews-pro/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2015-2020, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * WooCommerce Product Vendors integration.
 *
 * @since 1.10.0
 */
class WC_Product_Reviews_Pro_Integration_Product_Vendors {


	/**
	 * Hooks Product Reviews Pro with Product Vendors.
	 *
	 * Product Vendors need to be able to manage reviews on their own products, but have more limited admin rights than shop managers.
	 * We piggyback on the reviews table and other Product Reviews Pro objects and method to output similar admin screens, but listing only pertinent reviews.
	 * The most notable differences are that the the reviews screen is moved from WooCommerce to the Products menu item, and that the individual review screen is replaced with another with fewer editable elements.
	 *
	 * @since 1.10.0
	 */
	public function __construct() {

		// admin handling
		if ( is_admin() ) {

			// redirects requests for product vendors to the edit comments screen to a custom edit screen
			add_action( 'admin_init', array( $this, 'redirect_product_vendors_edit_contribution_screens' ), -1 );

			// add the special reviews edit screen page for product vendors to the WooCommerce screen IDs
			add_filter( 'woocommerce_screen_ids', array( $this, 'add_product_vendors_reviews_screen_id' ) );

			// adds settings in WooCommerce > Settings > Products
			add_filter( 'wc_product_reviews_pro_product_contribution_settings', array( $this, 'add_products_contribution_settings' ) );
			// adds settings to the vendor user profile edit screen
			add_action( 'show_user_profile',                                    array( $this, 'add_vendor_user_profile_settings' ) );
			add_action( 'edit_user_profile',                                    array( $this, 'add_vendor_user_profile_settings' ) );
			add_action( 'personal_options_update',                              array( $this, 'save_vendor_user_profile_settings' ) );
			add_action( 'edit_user_profile_update',                             array( $this, 'save_vendor_user_profile_settings' ) );

			// changes the position of the Reviews submenu page from "WooCommerce" to "Products"
			add_filter( 'wc_product_reviews_pro_reviews_submenu_page_args', array( $this, 'handle_vendor_reviews_submenu_page' ) );

			// changes the reviews screen submenu page url with the one used by the product vendors integration
			add_filter( 'wc_product_reviews_pro_reviews_screen_page_url', array( $this, 'get_product_vendors_reviews_screen_page_url' ) );

			// highlight correct parent when editing a review (priority 20 handles after normal Product Reviews Pro handling)
			add_filter( 'parent_file', array( $this, 'highlight_product_vendor_reviews_menu_item' ), 20 );

			// add reviews columns: piggyback on callbacks defined in \WC_Product_Reviews_Pro_Admin */
			add_filter( 'manage_product_page_reviews_columns',          array( wc_product_reviews_pro()->get_admin_instance(), 'add_custom_contributions_columns' ) );
			add_filter( 'manage_product_page_reviews_custom_column',    array( wc_product_reviews_pro()->get_admin_instance(), 'custom_contribution_column' ) );
			add_filter( 'manage_product_page_reviews_sortable_columns', array( wc_product_reviews_pro()->get_admin_instance(), 'make_custom_contributions_sortable_columns' ) );

			// replaces the contribution status links with one showing the adjusted flagged count for product vendors
			add_filter( 'review_status_links', array( $this, 'handle_contribution_status_links' ), 20 );
			// modifies the URLs of review row actions so product vendors are redirected to a different view than admins when editing a contribution
			add_filter( 'review_row_actions',  array( $this, 'handle_contribution_row_actions' ), 20, 2 );

			// allow updating the status of a contribution in AJAX
			add_filter( 'wc_product_reviews_pro_can_update_contribution_status', array( $this, 'allow_update_contribution_status' ), 10, 2 );

			// allow shop managers to filter contributions by product vendor in the reviews edit screen
			add_filter( 'restrict_manage_reviews', array( $this, 'add_contributions_by_vendor_filter_input' ), 3 );
			// also restricts product vendors from viewing contributions other than those attached to their own products
			add_filter( 'pre_get_comments',        array( $this, 'filter_contributions_by_vendor' ) );
		}

		// frontend handling
		if ( ! is_admin() || is_ajax() ) {

			// adds a vendor badge next to the comment author name for product vendors
			add_filter( 'wc_product_reviews_pro_author_badge', array( $this, 'add_product_vendor_author_badge' ), 20, 2 );
		}

		// handle replies for product vendors for contributions left on their products
		add_filter( 'wc_product_reviews_pro_enabled_contribution_types', array( $this, 'enable_product_vendor_replies' ) );

		// flags handling
		add_filter( 'wc_product_reviews_pro_flagged_contribution_set_to_pending_approval', array( $this, 'skip_set_flagged_contribution_pending_approval' ), 10, 2 );
		add_filter( 'wc_product_reviews_pro_flagged_contribution_user_display_name',       array( $this, 'handle_flagged_contribution_user_display_name' ), 10, 3 );

		// emails handling
		add_filter( 'comment_notification_recipients',                              array( $this, 'add_product_vendor_new_contribution_recipients' ), 10, 2 );
		add_filter( 'comment_moderation_recipients',                                array( $this, 'add_product_vendor_new_contribution_recipients' ), 10, 2 );
		add_filter( 'wc_product_reviews_pro_flagged_contribution_email_recipients', array( $this, 'add_product_vendor_flagged_contribution_recipients' ), 10, 2 );
		add_filter( 'wc_product_reviews_pro_flagged_contribution_email_is_enabled', array( $this, 'force_vendor_flagged_contribution_email_notification' ), 10, 2 );
	}


	/**
	 * Checks whether a contribution's author is a product vendor.
	 *
	 * @since 1.10.0
	 *
	 * @param int|\WP_Comment|\WC_Contribution $contribution a contribution object, ID or related comment
	 * @return bool
	 */
	private function is_contribution_author_product_vendor( $contribution ) {

		$is_product_vendor = false;

		if ( $contribution = wc_product_reviews_pro_get_contribution( $contribution ) ) {

			$contributor_id = $contribution->get_contributor_id();
			$product_id     = $contribution->get_product_id();

			if ( $product_id > 0 && $contributor_id > 0 && \WC_Product_Vendors_Utils::is_vendor( $contributor_id ) ) {

				$product_ids       = $this->get_user_vendor_product_ids( $contributor_id );
				$is_product_vendor = ! empty( $product_ids ) && in_array( $contribution->get_product_id(), $product_ids, false );
			}
		}

		return $is_product_vendor;
	}


	/**
	 * Returns all the product IDs for a user that is a vendor.
	 *
	 * @since 1.10.0
	 *
	 * @param \WP_User|int $user_id user object or ID
	 * @return int[] array of product IDs
	 */
	private function get_user_vendor_product_ids( $user_id ) {

		$user_id            = $user_id instanceof \WP_User ? $user_id->ID : (int) $user_id;
		$vendor_data        = $user_id > 0 ? \WC_Product_Vendors_Utils::get_all_vendor_data( $user_id ) : null;
		$product_vendor_ids = ! empty( $vendor_data ) && is_array( $vendor_data ) ? array_keys( $vendor_data ) : array();
		$product_ids        = array();

		if ( ! empty( $product_vendor_ids ) ) {

			foreach ( $product_vendor_ids as $vendor_id ) {
				$product_ids[] = \WC_Product_Vendors_Utils::get_vendor_product_ids( $vendor_id );
			}

			$product_ids = ! empty( $product_ids ) ? call_user_func_array( 'array_merge', $product_ids ) : array();
		}

		return $product_ids;
	}


	/**
	 * Returns users associated with a vendor.
	 *
	 * @since 1.10.0
	 *
	 * @param int $vendor_id a vendor identifier (usually a term ID)
	 * @return \WP_User[] array of vendor users
	 */
	private function get_vendor_product_users( $vendor_id ) {

		$vendor_data  = \WC_Product_Vendors_Utils::get_vendor_data_by_id( $vendor_id );
		$vendor_users = array();

		if ( isset( $vendor_data['admins'] ) && is_array( $vendor_data['admins'] ) ) {

			foreach ( $vendor_data['admins'] as $user_id ) {

				$user = get_user_by( 'id', $user_id );

				if ( $user instanceof \WP_User ) {
					$vendor_users[] = $user;
				}
			}
		}

		return $vendor_users;
	}


	/**
	 * Returns the badge to use for product vendors.
	 *
	 * @since 1.10.0
	 *
	 * @return string
	 */
	private function get_product_vendor_author_badge_text() {

		return trim( get_option( 'wc_product_reviews_pro_contribution_badge_vendor', '' ) );
	}


	/**
	 * Redirects product vendor users to the right edit contribution screens.
	 *
	 * This is necessary as some WordPress or WooCommerce admin links may point to edit-comments.php or comment.php, which product vendors do not or should not have full rights for.
	 *
	 * @see \WC_Product_Reviews_Pro_Integration_Product_Vendors::get_product_vendors_reviews_screen_page_url()
	 * @see \WC_Product_Reviews_Pro_Integration_Product_Vendors::output_review_screens_html()
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 */
	public function redirect_product_vendors_edit_contribution_screens() {
		global $pagenow;

		if (      in_array( $pagenow, array( 'admin.php', 'comment.php', 'edit-comments.php' ), true )
			 && ! current_user_can( 'manage_woocommerce' )
			 &&   \WC_Product_Vendors_Utils::is_vendor() ) {

			switch ( $pagenow ) {

				case 'admin.php' :

					if ( isset( $_GET['page'] ) && ! isset( $_GET['post_type'] ) && 'reviews' === $_GET['page'] ) {
						$redirect_url = $this->get_product_vendors_reviews_screen_page_url();
					} else {
						$redirect_url = null;
					}

				break;

				case 'comment.php' :
				case 'edit-comments.php' :

					$redirect_url = $this->get_product_vendors_reviews_screen_page_url();

					if ( isset( $_GET['action'] ) && in_array( $_GET['action'], array( 'editcomment', 'approve', 'unapprove', 'spam', 'trash', 'delete' ), true ) ) {

						$contribution_id = isset( $_GET['c'] ) ? (int) $_GET['c'] : 0;

						if ( $contribution_id > 0 && ( $contribution = wc_product_reviews_pro_get_contribution( $contribution_id ) ) ) {

							$product_id = $contribution->get_product_id();

							if ( $product_id > 0 && in_array( $product_id, $this->get_user_vendor_product_ids( get_current_user_id() ), false ) ) {

								$redirect_url = add_query_arg(
									array(
										'action' => 'editcomment',
										'c'      => $contribution_id,
									),
									$redirect_url
								);
							}
						}
					}

				break;

				default :
					$redirect_url = null;
				break;
			}

			if ( null !== $redirect_url ) {
				wp_safe_redirect( $redirect_url );
			}
		}
	}


	/**
	 * Adds the product vendors reviews screen to the WooCommerce screen IDs list.
	 *
	 * This will make available some scripts like enhanced select and search products on the reviews screen.
	 *
	 * @see \WC_Reviews::add_review_screen_id()
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 *
	 * @param string[] $screen_ids list of screen IDs
	 * @return string[]
	 */
	public function add_product_vendors_reviews_screen_id( $screen_ids ) {

		$screen_id = 'product_page_reviews';

		if ( ! in_array( $screen_id, $screen_ids, false ) ) {
			$screen_ids[] = $screen_id;
		}

		return $screen_ids;
	}


	/**
	 * Adds product vendors integration settings to product contribution settings.
	 *
	 * @see \WC_Product_Reviews_Pro_Admin::add_contribution_settings()
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 *
	 * @param array $settings associative array of settings
	 * @return array
	 */
	public function add_products_contribution_settings( $settings ) {

		$new_settings = array();

		foreach ( $settings as $setting ) {

			$new_settings[] = $setting;

			if ( isset( $setting['id'] ) && 'wc_product_reviews_pro_contribution_badge' === $setting['id'] ) {

				$new_settings[] = array(
					'title'    => __( 'Vendor badges', 'woocommerce-product-reviews-pro' ),
					'type'     => 'text',
					'desc'     => __( 'Leave blank to disable badges.', 'woocommerce-product-reviews-pro' ),
					'desc_tip' => __( 'Enter the text to use on badges displayed on vendor admin and vendor manager contributions.', 'woocommerce-product-reviews-pro' ),
					'id'       => 'wc_product_reviews_pro_contribution_badge_vendor',
					'default'  => __( 'Vendor', 'woocommerce-product-reviews-pro' ),
				);
			}
		}

		$settings = $new_settings;

		return $settings;
	}


	/**
	 * Adds input fields to the user profile edit screen of product vendors.
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 *
	 * @param \WP_User $user
	 */
	public function add_vendor_user_profile_settings( $user ) {

		if ( $user instanceof \WP_User && ( current_user_can( 'edit_user', $user->ID ) && \WC_Product_Vendors_Utils::is_vendor( $user->ID ) ) ) :

			$flagged_contribution_email = wc_product_reviews_pro()->get_emails_instance()->get_email( 'WC_Product_Reviews_Pro_Emails_Flagged_Contribution' );
			$notify_new_flags           = $flagged_contribution_email && $flagged_contribution_email->is_enabled();
			$notify_new_contributions   = 1 === (int) get_option( 'comments_notify' ) || 1 === (int) get_option( 'moderation_notify');

			if ( $notify_new_contributions || $notify_new_flags ) :

				?>
				<h2><?php esc_html_e( 'Product Reviews', 'woocommerce-product-reviews-pro' ); ?></h2>
				<table class="form-table">
					<tbody>

						<?php if ( $notify_new_contributions ) : ?>

							<tr>
								<th><?php esc_html_e( 'New Contributions', 'woocommerce-product-reviews-pro' ); ?></th>
								<td>
									<label>
										<input
											type="checkbox"
											id="_wc_product_reviews_pro_product_vendor_notify_new_contributions"
											name="_wc_product_reviews_pro_product_vendor_notify_new_contributions"
											value="yes"
											<?php checked( true, in_array( get_user_meta( $user->ID, '_wc_product_reviews_pro_product_vendor_notify_new_contributions', true ), array( false, 'yes' ), true ), true ); ?>
										/><?php esc_html_e( 'Enable notifications', 'woocommerce-product-reviews-pro' ); ?>
									</label>
									<p class="description">
										<?php esc_html_e( 'Receive email notifications whenever an administrator is notified of new contributions placed for one of the products you manage.', 'woocommerce-product-reviews-pro' ); ?>
									</p>
								</td>
							</tr>

						<?php endif; ?>

						<?php if ( $notify_new_flags ) : ?>

							<tr>
								<th><?php esc_html_e( 'Flagged Contributions', 'woocommerce-product-reviews-pro' ); ?></th>
								<td>
									<label>
										<input
											type="checkbox"
											id="_wc_product_reviews_pro_product_vendor_notify_flagged_contributions"
											name="_wc_product_reviews_pro_product_vendor_notify_flagged_contributions"
											value="yes"
											<?php checked( true, in_array( get_user_meta( $user->ID, '_wc_product_reviews_pro_product_vendor_notify_flagged_contributions', true ), array( false, 'yes' ), true ), true ); ?>
										/><?php esc_html_e( 'Enable notifications', 'woocommerce-product-reviews-pro' ); ?>
									</label>
									<p class="description">
										<?php esc_html_e( 'Receive email notifications whenever someone flags a contribution to one of the products you manage as inappropriate.', 'woocommerce-product-reviews-pro' ); ?>
									</p>
								</td>
							</tr>

						<?php endif; ?>

					</tbody>
				</table>
				<?php

			endif;

		endif;
	}


	/**
	 * Saves additional product vendor user profile data.
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 *
	 * @param int \$user_id user ID
	 */
	public function save_vendor_user_profile_settings( $user_id ) {

		if ( $user_id > 0 && ( current_user_can( 'edit_user', $user_id ) && \WC_Product_Vendors_Utils::is_vendor( $user_id ) ) ) {

			$flagged_contribution_email = wc_product_reviews_pro()->get_emails_instance()->get_email( 'WC_Product_Reviews_Pro_Emails_Flagged_Contribution' );

			if ( $flagged_contribution_email && $flagged_contribution_email->is_enabled() ) {
				update_user_meta( $user_id, '_wc_product_reviews_pro_product_vendor_notify_flagged_contributions', ! empty( $_POST['_wc_product_reviews_pro_product_vendor_notify_flagged_contributions'] ) ? 'yes' : 'no' );
			}

			if ( 1 === (int) get_option( 'comments_notify' ) || 1 === (int) get_option( 'moderation_notify') ) {
				update_user_meta( $user_id, '_wc_product_reviews_pro_product_vendor_notify_new_contributions', ! empty( $_POST['_wc_product_reviews_pro_product_vendor_notify_new_contributions'] ) ? 'yes' : 'no' );
			}
		}
	}


	/**
	 * Checks whether a product vendor should be notified with one Product Reviews Pro email.
	 *
	 * Note: defaults to true when a setting is not explicitly set for the given user.
	 *
	 * @since 1.10.0
	 *
	 * @param int $user_id a product vendor user ID
	 * @param string $which notification type, either 'flagged_contribution' or 'new_contribution'
	 * @return bool
	 */
	private function should_product_vendor_be_notified( $user_id, $which ) {

		$notify = false;

		if ( $user_id > 0 && \WC_Product_Vendors_Utils::is_vendor( $user_id ) ) {

			$meta_key = null;

			if ( 'flagged_contribution' === $which ) {
				$meta_key = '_wc_product_reviews_pro_product_vendor_notify_flagged_contributions';
			} elseif ( 'new_contribution' === $which ) {
				$meta_key = '_wc_product_reviews_pro_product_vendor_notify_new_contributions';
			}

			// defaults to 'yes' if the user meta is not set
			$notify = $meta_key && in_array( get_user_meta( $user_id, $meta_key, true ), array( false, 'yes' ), true );
		}

		return $notify;
	}


	/**
	 * Adds an author badge for product vendors before the author name in review meta.
	 *
	 * @see \wc_product_reviews_pro_author_badge()
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 *
	 * @param string $badge the badge markup
	 * @param \WP_Comment $comment the contribution comment object
	 * @return string
	 */
	public function add_product_vendor_author_badge( $badge, $comment ) {

		if ( $this->is_contribution_author_product_vendor( $comment ) ) {

			$badge_text = $this->get_product_vendor_author_badge_text();

			if ( '' !== $badge_text && is_string( $badge_text ) ) {

				$badge = '<span class="contribution-badge contribution-badge-vendor">' . esc_html( $badge_text ) . '</span>';
			}
		}

		return $badge;
	}


	/**
	 * Moves the "Reviews" sub-page from "WooCommerce" to "Products" in the admin menus.
	 *
	 * This is needed because Product Vendor users do not normally see the WooCommerce menu item.
	 *
	 * @see \WC_Reviews::add_menu_items()
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 *
	 * @param array $args non-associative array of arguments passed to `add_submenu_page()`
	 * @return array
	 */
	public function handle_vendor_reviews_submenu_page( $args ) {

		// if filtering by vendor, alter the page name
		if ( isset( $_GET['vendor'] ) && is_numeric( $_GET['vendor'] ) && \WC_Product_Vendors_Utils::is_valid_vendor( $_GET['vendor'] ) ) {

			$vendor = get_term_by( 'id', $_GET['vendor'], WC_PRODUCT_VENDORS_TAXONOMY );

			/* translators: Placeholders: %1$s - reviews screen title (usually "Reviews"), %2$s vendor name */
			$args['page_title'] = sprintf( __( '%1$s (Vendor: "%2$s")', 'woocommerce-product-reviews-pro' ), $args['page_title'], esc_html( $vendor->name ) );
		}

		// if current user is a vendor, redirect to reviews submenu in products screens
		if ( \WC_Product_Vendors_Utils::is_vendor( get_current_user_id() ) ) {

			$args['parent_slug'] = 'edit.php?post_type=product';
			$args['capability']  = 'read';
			// also tells to use product vendors integration own callback for the HTML output
			$args['callback']    = array( $this, 'output_review_screens_html' );
		}

		return $args;
	}


	/**
	 * Outputs the review screens HTML for product vendors.
	 *
	 * Normally this outputs the reviews list table HTML as in the WooCommerce > Reviews implementation.
	 * However, it will replace the table with a mock edit comment screen meant for product vendors to replace the standard comment edit screen.
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 */
	public function output_review_screens_html() {

		// check if the vendor wants to view/edit an individual contribution
		if (      isset( $_REQUEST['c'], $_REQUEST['action'] )
			 &&   is_numeric( $_REQUEST['c'] )
			 &&   in_array( $_REQUEST['action'], array( 'editcomment', 'approve', 'unapprove', 'spam', 'trash', 'delete' ), true )
			 && ! current_user_can( 'manage_woocommerce' )
			 &&   \WC_Product_Vendors_Utils::is_vendor() ) {

			$contribution = wc_product_reviews_pro_get_contribution( (int) $_REQUEST['c'] );
			$product_id   = $contribution ? $contribution->get_product_id() : null;

			// check if the contribution is for a product managed by the current vendor before proceeding
			if ( $product_id && in_array( $product_id, $this->get_user_vendor_product_ids( get_current_user_id() ), false ) ) {

				// the following HTML mimics the comment.php template from wp-admin ?>
				<div class="wrap">

					<h1><?php esc_html_e( 'Edit Review', 'woocommerce-product-reviews-pro' ); ?></h1>

					<div id="poststuff">

						<div id="post-body" class="metabox-holder columns-2">

							<div id="post-body-content" class="edit-form-section edit-comment-section">

								<div class="inside">
									<div id="comment-link-box">
										<strong><?php esc_html_e( 'Permalink:', 'woocommerce-product-reviews-pro' ); ?></strong>
										<span id="sample-permalink"><a href="<?php echo esc_url( $contribution->get_permalink() ); ?>"><?php echo esc_html( $contribution->get_permalink() ); ?></a></span>
									</div>
								</div>

								<div id="namediv" class="stuffbox">
									<div class="inside">
										<fieldset>

											<legend class="edit-comment-author"><?php esc_html_e( 'Author', 'woocommerce-product-reviews-pro' ); ?></legend>

											<table class="form-table editcomment">
												<tbody>
												<tr>
													<td class="first">
														<label for="name"><?php echo esc_html_x( 'Name:', 'Comment author name', 'woocommerce-product-reviews-pro' ); ?></label>
													</td>
													<td>
														<input
															type="text"
															name="newcomment_author"
															size="30"
															value="<?php echo esc_attr( $contribution->get_contributor_name() ); ?>"
															id="name"
															disabled
														/>
													</td>
												</tr>
												<tr>
													<td class="first">
														<label for="email"><?php esc_html_e( 'Email:', 'woocommerce-product-reviews-pro' ); ?></label>
													</td>
													<td>
														<input
															type="text"
															name="newcomment_author_email"
															size="30"
															value="<?php echo esc_attr( $contribution->get_contributor_email() ); ?>"
															id="email"
															disabled
														/>
													</td>
												</tr>
												<tr>
													<td class="first">
														<label for="newcomment_author_url"><?php esc_html_e( 'URL:', 'woocommerce-product-reviews-pro' ); ?></label>
													</td>
													<td>
														<input
															type="text"
															id="newcomment_author_url"
															name="newcomment_author_url"
															size="30"
															class="code"
															value="<?php
															$comment_data = $contribution->get_comment_data();
															echo esc_attr( $comment_data && isset( $comment_data->comment_author_url ) ? $comment_data->comment_author_url : '' );
															?>"
															disabled
														/>
													</td>
												</tr>
												</tbody>
											</table>

											<br>

										</fieldset>
									</div>
								</div>

								<div id="postdiv" class="postarea">
									<div id="wp-content-wrap" class="wp-core-ui wp-editor-wrap html-active">
										<link rel="stylesheet" id="editor-buttons-css" href="<?php echo esc_url( includes_url( 'css/editor.min.css' ) ); ?>" type="text/css" media="all" />
										<div id="wp-content-editor-container" class="wp-editor-container">
											<textarea
												class="wp-editor-area"
												rows="20"
												cols="40"
												name="content"
												id="content"
												style="height:200px;"
												disabled><?php echo wp_kses_post( $contribution->get_content() ); ?></textarea>
										</div>
									</div>
								</div>

							</div>

							<div id="postbox-container-1" class="postbox-container">
								<div id="submitdiv" class="stuffbox">

									<h2><?php esc_html_e( 'Status', 'woocommerce-product-reviews-pro' ); ?></h2>

									<div class="inside">
										<div class="submitbox" id="submitcomment">

											<div id="minor-publishing">

												<div id="misc-publishing-actions">

													<fieldset class="misc-pub-section misc-pub-comment-status" id="comment-status-radio">
														<?php

														$status   = $contribution->get_moderation();
														$statuses = array(
															'1'    => _x( 'Approved', 'Set comment to approved status', 'woocommerce-product-reviews-pro' ),
															'0'    => _x( 'Pending', 'Set comment to pending approval status', 'woocommerce-product-reviews-pro' ),
															'spam' => _x( 'Spam', 'Mark comment as spam', 'woocommerce-product-reviews-pro' ),
														);

														?>
														<?php foreach ( $statuses as $status_id => $status_label ) : ?>

															<label>
																<input
																	type="radio"
																	name="comment_status"
																	value="<?php echo esc_attr( $status_id ); ?>"
																	<?php checked( $status, $status_id, true ); ?>
																/><?php echo esc_html( $status_label ); ?>
															</label>
															<br>

														<?php endforeach; ?>
													</fieldset>

													<div class="misc-pub-section curtime misc-pub-curtime">
														<span id="timestamp"><?php
															/* translators: Placeholder: %s - date and time of publication */
															printf( __( 'Submitted on: %s', 'woocommerce-product-reviews-pro' ), '<b>' . date_i18n( 'M j, Y @ H:i', strtotime( $contribution->get_contribution_date() ) ) . '</b>' ); ?></span>
													</div>

													<div class="misc-pub-section misc-pub-response-to">
														<?php

														if ( current_user_can( 'edit_post', $product_id ) ) {
															$post_link = '<a href="' . esc_url( get_edit_post_link( $product_id ) ) . '">' . esc_html( get_the_title( $product_id ) ) . '</a>';
														} else {
															$post_link = esc_html( get_the_title( $product_id ) );
														}

														/* translators: Placeholder: %s - product the contribution comment is placed for */
														printf( __( 'In response to: %s', 'woocommerce-product-reviews-pro' ), '<b>' . $post_link . '</b>' ); ?>
													</div>
												</div>

												<div class="clear"></div>
											</div>

											<div id="major-publishing-actions">

												<div id="publishing-action">
													<input
														type="submit"
														name="save"
														id="save"
														data-contribution-id="<?php echo esc_attr( $contribution->get_id() ); ?>"
														class="button button-primary button-large"
														value="<?php esc_attr_e( 'Update', 'woocommerce-product-reviews-pro' ); ?>"
													/>
												</div>

												<div class="clear"></div>
											</div>

										</div>
									</div>
								</div>
							</div>

							<div id="postbox-container-2" class="postbox-container">

								<?php if ( $contribution->has_title() || ( in_array( $contribution->get_type(), array( 'review', 'photo', 'video' ), false ) ) ) : ?>

									<div id="wc-product-reviews-pro-title" class="postbox">
										<h2 class="hndle"><span><?php esc_html_e( 'Title', 'woocommerce-product-reviews-pro' ); ?></span></h2>
										<div class="inside">
											<input
												type="text"
												name="title"
												id="title"
												style="width:100%;"
												value="<?php echo esc_attr( $contribution->get_title() ); ?>"
												disabled
											/>
										</div>
									</div>

								<?php endif; ?>

								<?php if ( $contribution->is_type( 'review' ) ) : ?>

									<div id="woocommerce-rating" class="postbox">
										<h2 class="hndle"><span><?php esc_html_e( 'Rating', 'woocommerce-product-reviews-pro' ); ?></span></h2>
										<div class="inside">
											<select
												name="rating"
												id="rating"
												disabled>
												<?php $current = $contribution->get_rating(); ?>
												<?php for ( $rating = 1; $rating <= 5; $rating ++ ) : ?>
													<?php printf( '<option value="%1$s"%2$s>%1$s</option>', $rating, selected( $current, $rating, false ) ); ?>
												<?php endfor; ?>
											</select>
										</div>
									</div>

								<?php endif; ?>

								<?php if ( $contribution->has_attachment() || in_array( $contribution->get_type(), array( 'video', 'photo' ), false ) ) : ?>

									<div id="wc-product-reviews-pro-attachment" class="postbox">
										<h2 class="hndle"><span><?php esc_html_e( 'Attached media', 'woocommerce-product-reviews-pro' ); ?></span></h2>
										<div class="inside">
											<?php wc_product_reviews_pro()->get_admin_instance()->contribution_attachment_meta_box( $contribution->get_comment_data() ); ?>
										</div>
									</div>

								<?php endif; ?>

								<div id="wc-product-reviews-pro-stats" class="postbox">
									<h2 class="hndle"><span><?php esc_html_e( 'Stats', 'woocommerce-product-reviews-pro' ); ?></span></h2>
									<div class="inside">
										<?php wc_product_reviews_pro()->get_admin_instance()->contribution_stats_meta_box( $contribution->get_comment_data() ); ?>
									</div>
								</div>

								<div id="wc-product-reviews-pro-flags" class="postbox">
									<h2 class="hndle"><span><?php esc_html_e( 'Flags', 'woocommerce-product-reviews-pro' ); ?></span></h2>
									<div class="inside">
										<?php wc_product_reviews_pro()->get_admin_instance()->contribution_flags_meta_box( $contribution->get_comment_data() ); ?>
									</div>
								</div>

							</div>

						</div>
					</div>
				</div>
				<?php

				wc_enqueue_js( "
					jQuery( document ).ready( function( $ ) {

						jQuery( '#screen-meta' ).remove();
						jQuery( '#screen-meta-links' ).remove();

						$( '#save' ).on( 'click', function( e ) {
							e.preventDefault();
							$.post(
								window.wc_product_reviews_pro_admin_reviews.ajax_url,
								{
									'action'          : 'wc_product_reviews_pro_update_contribution_status',
									'security'        : window.wc_product_reviews_pro_admin_reviews.update_contribution_status_nonce,
									'contribution_id' : $( this ).data( 'contribution-id' ),
									'update_status'   : $( '#comment-status-radio' ).find( 'input:checked' ).val(),
								},
								function ( response ) {
									if ( response && response.success && response.data ) {
										window.location.href = response.data;
									} else {
										location.reload();
									}
								}
							);
						} );
					} );
				" );

			} else {

				// no contribution access rights: redirect to default reviews screen as if nothing happened
				wp_safe_redirect( $this->get_product_vendors_reviews_screen_page_url() );
			}

		} else {

			// render the standard reviews table screen output from Product Reviews Pro
			wc_product_reviews_pro()->get_admin_instance()->get_reviews_instance()->render_reviews_list_table();
		}
	}


	/**
	 * Adds a dropdown to filter contributions by vendor in the reviews edit screen.
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 */
	public function add_contributions_by_vendor_filter_input() {

		// add admins/shop managers specific controls
		if ( current_user_can( 'manage_woocommerce' ) ) :

			$vendor_terms   = WC_Product_Vendors_Utils::get_vendors();
			$current_vendor = Framework\SV_WC_Helper::get_requested_value( 'vendor' ); ?>

			<?php if ( ! empty( $vendor_terms ) && is_array( $vendor_terms ) ) : ?>

				<select
					class="wc-enhanced-select"
					name="vendor"
					style="width: 200px;"
					data-placeholder="<?php esc_attr_e( 'Search for a vendor&hellip;', 'woocommerce-product-reviews-pro' ); ?>"
					data-allow_clear="true">
					<option value=""></option>
					<?php foreach ( $vendor_terms as $vendor_term ) : ?>
						<option value="<?php echo esc_attr( $vendor_term->term_id ); ?>" <?php selected( (int) $vendor_term->term_id, (int) $current_vendor, true );?>><?php echo esc_html( $vendor_term->name ); ?></option>
					<?php endforeach; ?>
				</select>

			<?php endif;

		// extras for vendors
		elseif ( WC_Product_Vendors_Utils::is_vendor() ) :

			// this is necessary so filter/orderby form submissions are redirected to the right page (in vendors this is a sub-page under products) ?>
			<input type="hidden" name="post_type" value="product" /><?php

			// bulk actions aren't available to vendors, so we remove some HTML appendage for UI conformity
			wc_enqueue_js( '
				jQuery( ".bulkactions" ).remove();
				jQuery( "select[name=\'comment_type\']" ).css( "margin-left", 0 );
			' );

			remove_filter( 'wc_product_reviews_pro_enabled_contribution_types', array( $this, 'enable_product_vendor_replies' ), 10 );

			// if vendors are allowed to reply once, we ensure the reply link isn't shown twice for the same contribution
			if ( 'yes' === get_option( 'wc_product_reviews_pro_admins_can_always_reply', 'no' ) && ! in_array( 'contribution_comment', wc_product_reviews_pro_get_enabled_contribution_types(), true ) ) {

				wc_enqueue_js( "
					jQuery( document ).ready( function( $ ) {

						initialComments = $( '#the-comment-list > tr[id!=replyrow]' ).length;

						$( 'table.reviews' ).bind( 'DOMNodeInserted', function() {

							newRows = $( '#the-comment-list > tr[id!=replyrow]' ).length;

							if ( newRows > initialComments ) {
								location.reload();
							}
						} );
					} );
				" );
			}

			add_filter( 'wc_product_reviews_pro_enabled_contribution_types', array( $this, 'enable_product_vendor_replies' ), 10 );

		endif;
	}


	/**
	 * Filters the comment query to return contributions by product vendor.
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 *
	 * @param \WP_Comment_Query $comment_query the comment query
	 * @return \WP_Comment_Query
	 */
	public function filter_contributions_by_vendor( $comment_query ) {

		$current_screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

		if (    ( ! $current_screen && is_admin() )
			 || (   $current_screen && ( in_array( $current_screen->id, array( 'edit-comments', 'woocommerce_page_reviews', 'product_page_reviews' ), true ) || Framework\SV_WC_Helper::str_ends_with( $current_screen->id, 'reviews' ) ) ) ) {

			$is_admin_user   = current_user_can( 'manage_woocommerce' );
			$existing_clause = isset( $comment_query->query_vars['post__in'] ) && is_array( $comment_query->query_vars['post__in'] ) ? $comment_query->query_vars['post__in'] : array();
			$product_ids     = null;

			// product vendors are permanently limited to view only contributions to their products (done by user ID)
			if ( ! $is_admin_user && \WC_Product_Vendors_Utils::is_vendor() ) {
				$product_ids = array_merge( $existing_clause, $this->get_user_vendor_product_ids( get_current_user_id() ) );
			// admins and shop managers can optionally use a search filter to narrow contributions to vendor products (done by term ID)
			} elseif ( $is_admin_user && isset( $_REQUEST['vendor'] ) && is_numeric( $_REQUEST['vendor'] ) ) {
				$product_ids = array_merge( $existing_clause, \WC_Product_Vendors_Utils::get_vendor_product_ids( (int) $_REQUEST['vendor'] ) );
			}

			if ( is_array( $product_ids ) ) {

				// restrict result to specific vendors
				if ( ! empty( $product_ids ) ) {
					$comment_query->query_vars['post__in'] = array_unique( array_map( 'absint', $product_ids ) );
				// ensure no results are produced
				} else {
					$comment_query->query_vars['post__in'] = array( 0 );
				}
			}
		}

		return $comment_query;
	}


	/**
	 * Returns the product vendors reviews edit screen URL.
	 *
	 * @see \WC_Reviews_List_Table::get_views()
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 *
	 * @param string $url edit screen URL (default empty when not used by hook callback)
	 * @return string
	 */
	public function get_product_vendors_reviews_screen_page_url( $url = '' ) {

		if ( ! current_user_can( 'manage_woocommerce' ) && \WC_Product_Vendors_Utils::is_vendor() ) {

			// the main difference is that we need to remind WordPress the reviews page for vendors is under the product post type screen
			$url = add_query_arg(
				array(
					'post_type' => 'product',
					'page'      => 'reviews'
				),
				'edit.php'
			);
		}

		return $url;
	}


	/**
	 * Returns an array defining status links displayed in the reviews page.
	 *
	 * The standard one handled by Product Reviews Pro would include a count of all flagged contributions.
	 * This needs to be adjusted to include only flagged contributions to products managed by the current vendor.
	 *
	 * @see \WC_Product_Reviews_Pro_Admin::contribution_status_links()
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 *
	 * @param array $status_links associative array
	 * @return array
	 */
	public function handle_contribution_status_links( $status_links ) {
		global $post_id, $wpdb;

		$user_id = get_current_user_id();

		if ( \WC_Product_Vendors_Utils::is_vendor( $user_id ) ) {

			// remove current class from "all" when viewing flagged contributions
			if ( ! empty( $_REQUEST['is_flagged'] ) ) {
				$status_links['all'] = str_replace( 'current', '', $status_links['all'] );
			}

			$flagged_count      = 0;
			$vendor_product_ids = $this->get_user_vendor_product_ids( $user_id );

			if ( ! empty( $vendor_product_ids ) ) {

				// fetch number of flagged contributions, optionally filtered by current post_id (product)
				if ( is_numeric( $post_id ) && $post_id > 0 ) {

					// if an ID is specified we need of course to ensure the queried product ID belongs to the vendor
					if ( in_array( $post_id, $vendor_product_ids, false ) ) {

						$post_id       = (int) $post_id;
						$flagged_count = max( 0, (int) $wpdb->get_var( "
							SELECT COUNT(c.comment_ID)
							FROM {$wpdb->comments} c
							LEFT JOIN {$wpdb->commentmeta} m ON c.comment_ID = m.comment_id
							WHERE m.meta_key = 'flag_count'
							AND m.meta_value > 0
							AND c.comment_post_ID = {$post_id}
						" ) );
					}

				} else {

					// we need to limit the count to products that belong to the current vendor
					$vendor_product_ids = implode( ',', $vendor_product_ids );
					$flagged_count      = max( 0, (int) $wpdb->get_var( "
						SELECT COUNT(c.comment_ID)
						FROM {$wpdb->comments} c
						LEFT JOIN {$wpdb->commentmeta} m ON c.comment_ID = m.comment_id
						WHERE m.meta_key = 'flag_count'
						AND m.meta_value > 0
						AND c.comment_post_ID IN ({$vendor_product_ids})
					" ) );
				}
			}

			// format link
			$base_url     = $this->get_product_vendors_reviews_screen_page_url();
			$flagged_link = wc_product_reviews_pro()->get_admin_instance()->get_flagged_contribution_status_link( $flagged_count, $base_url );

			$status_links['is_flagged'] = $flagged_link;
		}

		return $status_links;
	}


	/**
	 * Returns actions available to product vendors to handle individual contributions from their row.
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 *
	 * @param array $actions associative array of actions
	 * @param \WP_Comment $comment the comment object for the corresponding contribution
	 * @return array
	 */
	public function handle_contribution_row_actions( $actions, $comment ) {

		$user_id = get_current_user_id();

		if ( ! current_user_can( 'manage_woocommerce' ) && \WC_Product_Vendors_Utils::is_vendor( $user_id ) ) {

			$contribution = wc_product_reviews_pro_get_contribution( $comment );

			if ( $contribution && ( $contribution_type = wc_product_reviews_pro_get_contribution_type( $contribution->get_type() ) ) ) {

				/* translators: Placeholder: %s - contribution type (e.g. review, photo, video...) */
				$edit_title = sprintf( __( 'Edit %s', 'woocommerce-product-reviews-pro' ), $contribution_type->get_title() );
				$edit_label = sprintf( __( 'Edit', 'woocommerce-product-reviews-pro' ) );

				/* @see \WC_Product_Reviews_Pro_Integration_Product_Vendors::output_review_screens_html() moves the link of the edit action to point to product vendor mock comment.php screen */
				$actions['edit'] = '<a href="' . add_query_arg( array( 'action' => 'editcomment', 'c' => $contribution->get_id() ), $this->get_product_vendors_reviews_screen_page_url() ) . '" title="' . esc_attr( $edit_title ) . '">' . esc_html( $edit_label ) . '</a>';

				// reply action is only allowed once per contribution
				if ( ! $this->is_vendor_allowed_to_reply( $contribution, $user_id ) ) {
					unset( $actions['reply'] );
				}

				// remove disallowed actions
				unset( $actions['delete'], $actions['trash'], $actions['untrash'] );
			}
		}

		return $actions;
	}


	/**
	 * Allows updating a contribution status for product vendors if they handle the parent product.
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 *
	 * @param bool $can_update whether the current user can update the contribution's status
	 * @param \WC_Contribution $contribution contribution object
	 * @return bool
	 */
	public function allow_update_contribution_status( $can_update, $contribution ) {

		$user_id = get_current_user_id();

		if ( ! $can_update && $contribution && WC_Product_Vendors_Utils::is_vendor( $user_id ) ) {
			$product_id = $contribution->get_product_id();
			$can_update = $product_id && in_array( $product_id, $this->get_user_vendor_product_ids( $user_id ), false );
		}

		return $can_update;
	}


	/**
	 * Highlights Product -> Reviews admin menu item when editing a review.
	 *
	 * @see \WC_Reviews::edit_review_parent_file() which does the same thing but for the WooCommerce menu as parent.
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 *
	 * @param string $parent_file parent menu item
	 * @return string
	 */
	public function highlight_product_vendor_reviews_menu_item( $parent_file ) {
		global $submenu_file;

		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

		if ( $screen && 'comment' === $screen->id && ! current_user_can( 'manage_woocommerce' ) && \WC_Product_Vendors_Utils::is_vendor() ) {

			$comment = get_comment( $_GET['c'] );

			if ( 'product' === get_post_type( $comment->comment_post_ID ) ) {

				$parent_file  = 'edit.php?post_type=product';
				$submenu_file = 'reviews';
			}
		}

		return $parent_file;
	}


	/**
	 * Checks whether a product vendor user can reply to a contribution.
	 *
	 * @since 1.10.0
	 *
	 * @param \WC_Contribution $contribution contribution object
	 * @param int|\WP_User $vendor_user user identifier
	 * @return bool
	 */
	private function is_vendor_allowed_to_reply( $contribution, $vendor_user ) {

		$allowed = false;
		$user_id = $vendor_user instanceof \WP_User ? $vendor_user->ID : $vendor_user;

		if ( $contribution instanceof \WC_Contribution && is_numeric( $user_id ) && \WC_Product_Vendors_Utils::is_vendor( (int) $user_id ) ) {

			// vendors can't reply to their own contributions
			$contribution_user_id = (int) $contribution->get_contributor_id();

			if ( $contribution_user_id !== $user_id ) {

				remove_filter( 'wc_product_reviews_pro_enabled_contribution_types', array( $this, 'enable_product_vendor_replies' ), 10 );

				$enabled_contribution_types = wc_product_reviews_pro_get_enabled_contribution_types();

				if ( ! in_array( 'contribution_comment', $enabled_contribution_types, true ) ) {

					$contribution_id = $contribution->get_id();
					// although there shouldn't be threads, still look for the top-most entry in a thread
					$contribution    = $contribution->has_parent() > 0 ? wc_product_reviews_pro_get_contribution( $contribution->get_parent() ) : $contribution;
					$allowed         = false;

					// comments are disabled, but admins can override
					if ( $contribution && 'yes' === get_option( 'wc_product_reviews_pro_admins_can_always_reply', 'no' ) ) {

						$product_id = $contribution->get_product_id();

						if ( $product_id > 0 && in_array( $product_id, $this->get_user_vendor_product_ids( $user_id ), false ) ) {

							$vendor_replies = get_comments( array(
								'post_id'            => $product_id,
								'author__in'         => array( $user_id ),
								'parent'             => $contribution_id,
								'status'             => 'all',
								'post_status'        => 'any',
								'include_unapproved' => true,
								'count'              => true,
							) );

							// vendors can only override once
							$allowed = $vendor_replies < 1;
						}
					}

				} else {

					// everyone can leave comments
					$allowed = true;
				}

				add_filter( 'wc_product_reviews_pro_enabled_contribution_types', array( $this, 'enable_product_vendor_replies' ), 10 );
			}
		}

		return $allowed;
	}


	/**
	 * Maybe enables a product vendor to reply to contribution threads according to settings.
	 *
	 * If contribution comments are disabled, but admins are allowed to leave replies, allow product vendors to leave at least one reply too.
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 *
	 * @param string[] $contribution_types
	 * @return string[]
	 */
	public function enable_product_vendor_replies( $contribution_types ) {
		global $comment;

		// check first if comment replies are disabled in the first place
		if ( $comment instanceof \WP_Comment && is_array( $contribution_types ) && ! in_array( 'contribution_comment', $contribution_types, true ) ) {

			$user_id      = get_current_user_id();
			$contribution = wc_product_reviews_pro_get_contribution( $comment );

			// allow to reply if the user is a vendor and the contribution is for a product they manage
			if ( $contribution && $user_id > 0 && $this->is_vendor_allowed_to_reply( $contribution, $user_id ) ) {

				$contribution_types[] = 'contribution_comment';
			}
		}

		return $contribution_types;
	}


	/**
	 * Marks contributions not to be set to pending when flagged if the author is a product vendor.
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 *
	 * @param bool $set_to_pending whether to set the contribution to pending if flagged by someone
	 * @param \WC_Contribution $contribution
	 * @return bool
	 */
	public function skip_set_flagged_contribution_pending_approval( $set_to_pending, $contribution ) {

		$contribution_author_id = $contribution ? $contribution->get_contributor_id() : null;

		// proceed unless excluded already and the author of the flagged contribution is a vendor
		if ( $set_to_pending && $contribution_author_id > 0 && $this->is_contribution_author_product_vendor( $contribution ) ) {

			// exclude if the author is both the author of the contribution and manages the related product
			$product_id     = $contribution->get_product_id();
			$set_to_pending = ! ( $product_id && in_array( $product_id, $this->get_user_vendor_product_ids( $contribution_author_id ), false ) );
		}

		return $set_to_pending;
	}


	/**
	 * Maybe changes the formatted name of the flagging user marking it as product vendor.
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 *
	 * @param string $display_name the formatted name of the user submitting the flag
	 * @param \WC_Product_Reviews_Pro_Contribution_Flag $flag the flag object
	 * @param bool $html whether the output should contain HTML (default true)
	 * @return string may contain HTML
	 */
	public function handle_flagged_contribution_user_display_name( $display_name, $flag, $html = true ) {

		if ( ! $flag->is_anonymous() && \WC_Product_Vendors_Utils::is_vendor( $flag->get_user_id() ) ) {

			/* @see \get_edit_user_link() it is not safe to use this function straight because if the current method is called while in email context, that function may check for current user ID for capability and necessarily return false */
			$edit_user_url = add_query_arg( 'user_id', $flag->get_user_id(), self_admin_url( 'user-edit.php' ) );

			if ( false !== $html ) {
				$display_name = '<a href="' . esc_url( $edit_user_url ) . '">' . sprintf( __( 'Product Vendor', 'woocommerce-product-reviews-pro' ) ) . '</a>';
			} else {
				/* translators: Placeholder: %s - product vendor's profile edit screen URL in parenthesis*/
				$display_name = sprintf( __( 'Product Vendor %s', 'woocommerce-product-reviews-pro' ), '(' . esc_url( $edit_user_url ) . ')' );
			}
		}

		return $display_name;
	}


	/**
	 * Maybe adds a product vendor's email address to a list of recipients.
	 *
	 * @see \WC_Product_Reviews_Pro_Integration_Product_Vendors::add_product_vendor_new_contribution_recipients()
	 * @see \WC_Product_Reviews_Pro_Integration_Product_Vendors::add_product_vendor_flagged_contribution_recipients()
	 *
	 * @since 1.10.0
	 *
	 * @param string[] $recipients array of email addresses
	 * @param null|int $product_id a valid \WC_Product ID
	 * @param string $which the email type recipients are added to
	 * @param \WC_Contribution|\WC_Product_Reviews_Pro_Contribution_Flag|null $object optional object
	 * @return string[]
	 */
	private function add_product_vendor_to_email_recipients( $recipients, $product_id, $which, $object = null ) {

		if ( $product_id && \WC_Product_Vendors_Utils::is_vendor_product( $product_id ) ) {

			$vendor_id  = \WC_Product_Vendors_Utils::get_vendor_id_from_product( $product_id );
			$users      = $this->get_vendor_product_users( $vendor_id );

			foreach ( $users as $user ) {

				if ( is_email( $user->user_email ) && $this->should_product_vendor_be_notified( $user->ID, $which ) ) {

					if ( $object instanceof \WC_Contribution ) {

						$contributor_email = $object->get_contributor_email();

						// skip if the contributor email match the current vendor email to be notified
						if ( $contributor_email && $contributor_email === $user->user_email ) {
							continue;
						}
					}

					$recipients[] = $user->user_email;
				}
			}
		}

		return $recipients;
	}


	/**
	 * Maybe adds the product vendor's email to the list of emails to notify when a new contribution is submitted.
	 *
	 * @since 1.10.0
	 *
	 * @param string[] $recipients array of emails to notify
	 * @param int $contribution_id the contribution (comment) ID
	 * @return string[]
	 */
	public function add_product_vendor_new_contribution_recipients( $recipients, $contribution_id ) {

		$contribution = wc_product_reviews_pro_get_contribution( $contribution_id );
		$product_id   = $contribution ? $contribution->get_product_id() : null;

		return $this->add_product_vendor_to_email_recipients( $recipients, $product_id, 'new_contribution', $contribution );
	}


	/**
	 * Maybe adds the product vendor's email to the list of emails to notify when a contribution is flagged.
	 *
	 * This happens if the related product of a contribution matches one of the products managed by the vendor and if the vendor has chosen to receive such notifications.
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 *
	 * @param string[] $recipients array of email addresses
	 * @param \WC_Product_Reviews_Pro_Emails_Flagged_Contribution $email email object
	 * @return string[]
	 */
	public function add_product_vendor_flagged_contribution_recipients( $recipients, $email ) {

		$product    = $email->get_product();
		$product_id = $product ? $product->get_id() : null;

		return $this->add_product_vendor_to_email_recipients( $recipients, $product_id, 'flagged_contribution' );
	}


	/**
	 * Force enables a notification email if it's a vendor user to flag a contribution.
	 *
	 * Note: this happens even when the flagged contribution is not for one of the products the vendor manages.
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 *
	 * @param bool $enabled whether the email is enabled
	 * @param \WC_Product_Reviews_Pro_Contribution_Flag $flag flag object
	 * @return bool
	 */
	public function force_vendor_flagged_contribution_email_notification( $enabled, $flag ) {

		if ( ! $enabled && $flag instanceof \WC_Product_Reviews_Pro_Contribution_Flag && ! $flag->is_anonymous() ) {

			$enabled = \WC_Product_Vendors_Utils::is_vendor( $flag->get_user_id() );
		}

		return $enabled;
	}


}
