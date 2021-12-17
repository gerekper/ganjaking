<?php
/**
 * Plugin Name: WooCommerce Review for Discount
 * Plugin URI: https://woocommerce.com/products/review-for-discount/
 * Description: Provide discounts to incentivize users who submit reviews for specific or any products
 * Version: 1.6.22
 * Author: WooCommerce
 * Author URI: https://woocommerce.com/
 * Text domain: wc_review_discount
 * Requires at least: 4.4
 * Tested up to: 5.9
 * WC tested up to: 6.0
 * WC requires at least: 3.0
 *
 * Woo: 18671:67ae2070dd8d3f3624925857efda6117

 * Copyright: © 2021 WooCommerce
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * WooCommerce fallback notice.
 *
 * @since 1.6.14
 * @return void
 */
function woocommerce_review_for_discount_missing_wc_notice() {
	/* translators: %s WC download URL link. */
	echo '<div class="error"><p><strong>' . sprintf( esc_html__( 'Review for Discount requires WooCommerce to be installed and active. You can download %s here.', 'wc_review_discount' ), '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>' ) . '</strong></p></div>';
}

/*
 * Database version.
 * NOTE: Do not bump this version for main version change.
 * This is only for the database. Only if you make a change
 * to the database schema is when you would bump this
 * version.
 */
define( 'WC_REVIEW_FOR_DISCOUNT_DB_VERSION', '1.5.6' );

define( 'WC_REVIEW_FOR_DISCOUNT_VERSION', '1.6.22' ); // WRCS: DEFINED_VERSION.

register_activation_hook( __FILE__, 'woocommerce_review_for_discount_install' );

function woocommerce_review_for_discount_install() {
	global $wpdb;
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

	// default email settings
	$email = get_option('wrd_email_settings', array());

	if (empty($email)) {
			$email = array(
			'subject'   => __('Your store coupon', 'wc_review_discount'),
			'message'   => __("Thank you for your review. Please use the coupon code below to get discounts from our store:", 'wc_review_discount') . "\n\n{code}"
		);
		update_option('wrd_email_settings', $email);

	}

	$wpdb->hide_errors();

	$table = $wpdb->prefix . 'wrd_discounts';
	$sql = "CREATE TABLE $table (
id bigint(20) NOT NULL AUTO_INCREMENT,
type varchar(25) NOT NULL,
amount double(12,2) NOT NULL DEFAULT 0.00,
individual INT(1) NOT NULL DEFAULT 0,
free_shipping INT(1) NOT NULL DEFAULT 0,
send_mode varchar(25) NOT NULL,
send_to_verified INT (1) NOT NULL DEFAULT 0,
product_ids TEXT NOT NULL,
category_ids TEXT NOT NULL,
usage_limit INT(1) NOT NULL DEFAULT 0,
expiry_value INT(3) NOT NULL DEFAULT 0,
expiry_type varchar(25) NOT NULL DEFAULT '',
usage_count BIGINT(20) NOT NULL DEFAULT 0,
exclude_sale_items varchar(20) NOT NULL DEFAULT 'no',
all_products varchar(20) NOT NULL DEFAULT 'no',
unique_email INT(1) NOT NULL DEFAULT 0,
date_added DATETIME NOT NULL,
KEY type (type),
PRIMARY KEY  (id)
)";
	dbDelta($sql);

	$table = $wpdb->prefix . 'wrd_sent_coupons';
	$sql = "CREATE TABLE $table (
comment_id bigint(20) NOT NULL,
discount_id bigint(20) NOT NULL,
coupon_id bigint(20) NOT NULL,
author_email varchar(255) NOT NULL,
KEY comment_id (comment_id),
KEY discount_id (discount_id),
KEY coupon_id (coupon_id)
)";
	dbDelta($sql);

	if ( get_option('wrd_db_update_usage_limit', false) === false &&
		in_array( 'limit', $wpdb->get_col( "DESC {$wpdb->prefix}wrd_discounts" ) ) ) {
		$wpdb->query("UPDATE {$wpdb->prefix}wrd_discounts SET `usage_limit` = `limit`");
	}
	update_option('wrd_db_update_usage_limit', true);

	update_option( 'wrd_db_version', WC_REVIEW_FOR_DISCOUNT_DB_VERSION );
}

if ( ! class_exists( 'SFN_ReviewDiscount' ) ) :
	/**
	 * Localisation
	 **/
	load_plugin_textdomain( 'wc_review_discount', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	class SFN_ReviewDiscount {
		public function __construct() {
			// menu
			add_action('admin_menu', array($this, 'menu'), 20);

			// settings styles and scripts
			add_action( 'admin_enqueue_scripts', array($this, 'admin_scripts'), 20);

			// process settings
			add_action( 'admin_post_sfn_rd_new', array($this, 'create'));
			add_action( 'admin_post_sfn_rd_edit', array($this, 'edit'));
			add_action( 'admin_post_sfn_rd_email', array($this, 'update_email'));

			// comment posted
			add_action('comment_post', array($this, 'comment_posted'));
			add_action('comment_unapproved_to_approved', array($this, 'comment_updated'));

			// Upgrade
			if ( get_option( 'wrd_db_version', 0 ) < WC_REVIEW_FOR_DISCOUNT_DB_VERSION ) {
				woocommerce_review_for_discount_install();
			}

			if ( ! get_option( 'wrd_expiry_fix', false ) ) {
				add_action( 'plugins_loaded', array( $this, 'fix_expiration' ), 25 );
			}

			add_action( 'init', array( $this, 'includes' ) );
		}

		/**
		 * Include necessary files.
		 */
		public function includes() {
			require_once( dirname( __FILE__ ) . '/woocommerce-review-for-discount-privacy.php' );
		}

		public function menu() {
			add_submenu_page('woocommerce', __('Review for Discount', 'wc_review_discount'),  __('Review for Discount', 'wc_review_discount') , 'manage_woocommerce', 'wc-review-discount', array($this, 'settings'));
		}

		public function settings() {
			global $wpdb, $woocommerce;

			$tab = (isset($_GET['tab'])) ? $_GET['tab'] : 'discounts';

			if ($tab == 'discounts') {
				// load the discounts
				$discounts = $this->getDiscounts();

				include dirname(__FILE__) .'/settings.php';
			} elseif ($tab == 'new') {
				$products = $wpdb->get_results("SELECT * FROM `". $wpdb->prefix ."posts` WHERE `post_type` = 'product' AND `post_status` = 'publish' ORDER BY `post_title` ASC");
				$cats       = get_terms('product_cat');

				$discount = array(
					'id'                    => 0,
					'type'                  => 'fixed_cart',
					'amount'                => '',
					'send_mode'             => 'immediately',
					'individual'            => 0,
					'free_shipping'         => 0,
					'limit'                 => 0,
					'verified'              => 0,
					'exclude_sale_items'    => '',
					'all_products'          => '',
					'products'              => array(),
					'categories'            => array(),
					'expiry_value'          => 0,
					'expiry_type'           => '',
					'unique_email'          => 0
				);

				$wrd_new = sfn_session_get( 'wrd_new' );
				if ( $wrd_new ) {
					$discount = array_merge( $discount, $wrd_new );
				}

				include dirname(__FILE__) .'/form.php';
			} elseif ($tab == 'email') {
				$emailSettings = get_option('wrd_email_settings', array());

				include dirname(__FILE__) .'/email-settings.php';
			} elseif ($tab == 'delete') {
				$id = (int)$_GET['id'];

				// delete
				$wpdb->query( $wpdb->prepare("DELETE FROM `{$wpdb->prefix}wrd_sent_coupons` WHERE `discount_id` = %d", $id) );
				$wpdb->query( $wpdb->prepare("DELETE FROM `{$wpdb->prefix}wrd_discounts` WHERE `id` = %d", $id) );

				wp_redirect('admin.php?page=wc-review-discount&tab=discounts&deleted=true');
				exit;
			} elseif ($tab == 'edit') {
				$products = $wpdb->get_results("SELECT * FROM `". $wpdb->prefix ."posts` WHERE `post_type` = 'product' AND `post_status` = 'publish' ORDER BY `post_title` ASC");
				$cats       = get_terms('product_cat');

				$row        = $wpdb->get_row( $wpdb->prepare("SELECT * FROM `{$wpdb->prefix}wrd_discounts` WHERE `id` = %d", $_GET['id']) );

				$discount = array(
					'id'                    => $row->id,
					'type'                  => $row->type,
					'amount'                => $row->amount,
					'send_mode'             => $row->send_mode,
					'exclude_sale_items'    => $row->exclude_sale_items,
					'all_products'          => $row->all_products,
					'verified'              => $row->send_to_verified,
					'individual'            => $row->individual,
					'free_shipping'         => $row->free_shipping,
					'limit'                 => $row->usage_limit,
					'products'              => (!empty($row->product_ids)) ? unserialize($row->product_ids) : array(),
					'categories'            => (!empty($row->category_ids)) ? unserialize($row->category_ids) : array(),
					'expiry_value'          => $row->expiry_value,
					'expiry_type'           => $row->expiry_type,
					'unique_email'          => $row->unique_email
				);

				$wrd_edit = sfn_session_get( 'wrd_edit' );

				if ( $wrd_edit ) {
					$discount = array_merge( $discount, $wrd_edit );
				}

				include dirname(__FILE__) .'/form.php';
			}


		}

		public function admin_scripts() {

			if ( empty( $_GET['page'] ) || 'wc-review-discount' !== $_GET['page'] ) {
				return;
			}

			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			wp_enqueue_script( 'selectWoo' );
			wp_enqueue_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css', array(), WC_VERSION );
			wp_enqueue_style( 'select2', WC()->plugin_url() . '/assets/css/select2.css', array(), WC_VERSION );

			if ( ! wp_script_is( 'sfn-select', 'registered' ) ) {
				wp_register_script( 'sfn-select', plugins_url( 'assets/js/select' . $suffix . '.js', __FILE__ ), array( 'jquery' ), WC_REVIEW_FOR_DISCOUNT_VERSION, true );
			}

			wp_enqueue_script( 'sfn-select' );
			wp_enqueue_script( 'sfn-product-search' );
			wp_localize_script(
				'sfn-select',
				'sfn_select',
				array(
					'security' => wp_create_nonce( 'search-products' ),
				)
			);

			wp_enqueue_script( 'jquery-ui-sortable' );
		}

		public function update_email() {
			$default = get_option('wrd_email_settings', array());

			$post = array_map('stripslashes_deep', $_POST);

			$email = array(
				'subject'   => (isset($post['subject'])) ? $post['subject'] : null,
				'message'   => (isset($post['message'])) ? $post['message'] : null
			);

			$settings = array_merge($default, $email);

			update_option('wrd_email_settings', $settings);
			wp_redirect('admin.php?page=wc-review-discount&tab=email&updated=true');
			exit;
		}

		public function create() {
			$data = array(
				'type'              => $_POST['type'],
				'amount'            => $_POST['amount'],
				'send_mode'         => $_POST['sending_mode'],
				'send_to_verified'  => 0,
				'all_products'      => (isset($_POST['all_products'])) ? $_POST['all_products'] : 'no'
			);

			if (isset($_POST['send_to_verified'])) {
				$data['send_to_verified']  = ($_POST['send_to_verified'] == 1) ? 1 : 0;
			}

			if (isset($_POST['individual_use']) && $_POST['individual_use'] == 'yes') {
				$data['individual'] = 1;
			} else {
				$data['individual'] = 0;
			}

			if (isset($_POST['free_shipping']) && $_POST['free_shipping'] == 'yes') {
				$data['free_shipping'] = 1;
			} else {
				$data['free_shipping'] = 0;
			}

			if (isset($_POST['limit']) && $_POST['limit'] == 1) {
				$data['usage_limit'] = 1;
			} else {
				$data['usage_limit'] = 0;
			}

			if (isset($_POST['unique_email']) && $_POST['unique_email'] == 1) {
				$data['unique_email'] = 1;
			} else {
				$data['unique_email'] = 0;
			}

			if (isset($_POST['product_ids']) && !empty($_POST['product_ids'])) {
				$data['products'] = array();

				$posted_products = $_POST['product_ids'];

				if ( !is_array( $posted_products ) ) {
					if ( strpos( $posted_products, ',' ) !== false ) {
						$posted_products = array_filter( array_map( 'absint', explode( ',', $posted_products ) ) );
					} else {
						$posted_products = array( $posted_products );
					}
				}

				foreach ($posted_products as $pId) {
					$data['products'][] = $pId;
				}
			}

			if (isset($_POST['product_cats']) && !empty($_POST['product_cats'])) {
				$data['categories'] = array();
				foreach ($_POST['product_cats'] as $cId) {
					$data['categories'][] = $cId;
				}
			}

			if (!empty($_POST['expiry_value']) && !empty($_POST['expiry_type'])) {
				$data['expiry_value']   = $_POST['expiry_value'];
				$data['expiry_type']    = $_POST['expiry_type'];
			} else {
				$data['expiry_value']   = 0;
				$data['expiry_type']    = '';
			}

			if ( $data['all_products'] == 'yes' ) {
				$data['products'] = array();
				$data['categories'] = array();
			}

			if ( empty( $_POST['exclude_sale_items'] ) ) {
				$data['exclude_sale_items'] = 'no';
			} else {
				$data['exclude_sale_items'] = $_POST['exclude_sale_items'];
			}

			$stat = $this->newDiscount($data);

			if (is_wp_error($stat)) {
				sfn_session_set( 'wrd_new', $data );
				wp_redirect( 'admin.php?page=wc-review-discount&tab=new&error='. urlencode($stat->get_error_message()) );
			} else {
				wp_redirect('admin.php?page=wc-review-discount&created=1');
			}

			exit;
		}

		public function edit() {
			$id = $_POST['id'];
			$data = array(
				'type'              => $_POST['type'],
				'amount'            => $_POST['amount'],
				'send_mode'         => $_POST['sending_mode'],
				'send_to_verified'  => 0,
				'all_products'      => (isset($_POST['all_products'])) ? $_POST['all_products'] : 'no'
			);

			if (isset($_POST['send_to_verified'])) {
				$data['send_to_verified']  = ($_POST['send_to_verified'] == 1) ? 1 : 0;
			}

			if (isset($_POST['individual_use']) && $_POST['individual_use'] == 'yes') {
				$data['individual'] = 1;
			} else {
				$data['individual'] = 0;
			}

			if (isset($_POST['free_shipping']) && $_POST['free_shipping'] == 'yes') {
				$data['free_shipping'] = 1;
			} else {
				$data['free_shipping'] = 0;
			}

			if (isset($_POST['limit']) && $_POST['limit'] == 1) {
				$data['usage_limit'] = 1;
			} else {
				$data['usage_limit'] = 0;
			}

			if (isset($_POST['unique_email']) && $_POST['unique_email'] == 1) {
				$data['unique_email'] = 1;
			} else {
				$data['unique_email'] = 0;
			}

			$data['product_ids'] = array();
			if (isset($_POST['product_ids']) && !empty($_POST['product_ids'])) {
				$posted_products = $_POST['product_ids'];

				if ( !is_array( $posted_products ) ) {
					if ( strpos( $posted_products, ',' ) !== false ) {
						$posted_products = array_filter( array_map( 'absint', explode( ',', $posted_products ) ) );
					} else {
						$posted_products = array( $posted_products );
					}
				}

				foreach ($posted_products as $pId) {
					$data['product_ids'][] = $pId;
				}
			}

			if (!empty($data['product_ids'])) {
				$data['product_ids'] = serialize($data['product_ids']);
			} else {
				$data['product_ids'] = '';
			}

			$data['category_ids'] = array();
			if (isset($_POST['product_cats']) && !empty($_POST['product_cats'])) {

				foreach ($_POST['product_cats'] as $cId) {
					$data['category_ids'][] = $cId;
				}
			}

			if (!empty($data['category_ids'])) {
				$data['category_ids'] = serialize($data['category_ids']);
			} else {
				$data['category_ids'] = '';
			}

			if (!empty($_POST['expiry_value']) && !empty($_POST['expiry_type'])) {
				$data['expiry_value']   = $_POST['expiry_value'];
				$data['expiry_type']    = $_POST['expiry_type'];
			} else {
				$data['expiry_value']   = 0;
				$data['expiry_type']    = '';
			}

			if ( $data['all_products'] == 'yes' ) {
				$data['product_ids'] = '';
				$data['category_ids'] = '';
			}

			if ( empty( $_POST['exclude_sale_items'] ) ) {
				$data['exclude_sale_items'] = 'no';
			} else {
				$data['exclude_sale_items'] = $_POST['exclude_sale_items'];
			}

			$stat = $this->editDiscount($id, $data);

			if (is_wp_error($stat)) {
				$data['products'] = (isset($data['product_ids']) && !empty($data['product_ids'])) ? unserialize($data['product_ids']) : array();
				unset($data['product_ids']);

				$data['categories'] = (isset($data['product_cats']) && !empty($data['product_cats'])) ? unserialize($data['product_cats']) : array();
				unset($data['product_cats']);

				sfn_session_set( 'wrd_edit', $data );
				wp_redirect( 'admin.php?page=wc-review-discount&tab=edit&id='. $id .'&error='. urlencode($stat->get_error_message()) );
			} else {
				wp_redirect('admin.php?page=wc-review-discount&updated=1');
			}

			exit;
		}

		public function comment_updated($id) {
			$this->comment_posted($id, true);
		}

		public function comment_posted($commentId, $approved = null) {
			global $wpdb, $woocommerce;

			// we first load the comment to see if it has been approved or not
			$comment = get_comment($commentId);

			$postId         = $comment->comment_post_ID;
			$product        = sfn_get_product($postId);
			$categories     = get_the_terms($postId, 'product_cat');
			$discount       = false;
			$found          = array();
			$sent           = false;

			if (! $product || !$product->exists()) return;

			$post = get_post( $product->get_id() );

			if ( $post->post_type != 'product' ) return;

			$cats = array();
			// get the category IDs
			if ($categories) foreach ($categories as $category) {
				$cats[] = $category->term_id;
			}

			// check if the product id matches any of our discounts
			$discounts = $this->getDiscounts();

			foreach ($discounts as $disc) {
				$disc = apply_filters( 'wc_review_discount_discount_object', $disc, $comment );

				if ( $disc->exclude_sale_items == 'yes' && $product->is_on_sale() ) {
					continue;
				}

				if ( $disc->all_products == 'yes' || ( empty( $disc->category_ids ) && empty( $disc->product_ids ) ) ) {
					if (
						$disc->send_mode == 'immediately' ||
						$disc->send_mode == 'approved' &&
						($comment->comment_approved == 1 || $approved === true)
					) {
						$found[] = $disc;
						break;
					}
				} else {
					// if the category matches, we create and send the coupon
					$discCats = (empty($disc->category_ids)) ? array() : unserialize($disc->category_ids);
					foreach ($cats as $catId) {
						if (in_array($catId, $discCats)) {
							// check if comment has been approved or if we do not require comments to be approved
							if ($disc->send_mode == 'immediately' || $disc->send_mode == 'approved' && ($comment->comment_approved == 1 || $approved === true) ) {
								$found[] = $disc;
								break 2;
							}
						}
					}

					// search for product IDs
					if ( self::product_matches_discount_products( $postId, $disc ) ) {
						// check if comment has been approved or if we do not require comments to be approved
						if ($disc->send_mode == 'immediately' || $disc->send_mode == 'approved' && ($comment->comment_approved == 1 || $approved === true) ) {
							$found[] = $disc;
							break;
						}
					}
				}
			}

			// if valid, create coupon and send it to the registered email address
			if (! empty($found) ) {
				foreach ( $found as $disc ) {
					// check if coupon is set to only be sent once per email
					$sent_num = $wpdb->get_var( $wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}wrd_sent_coupons WHERE discount_id = %d AND author_email = %s", $disc->id, $comment->comment_author_email) );

					if ($disc->unique_email && $sent_num > 0) continue;

					// if coupon for the same product has already been sent to this customer, do nothing.
					if ($this->isSent($comment->comment_ID, $disc->id, $comment->comment_author_email))
						continue;

					if ($this->isSentToEmail($comment->comment_ID, $comment->comment_author_email))
						continue;

					// check if this requires that the reviewer be a verified owner of the product
					if ( 1 === intval( $disc->send_to_verified ) ) {
						if ( ! wc_customer_bought_product( $comment->comment_author_email, $comment->user_id, $postId ) ) {
							continue;
						}
					}

					// create the coupon
					$now    = time();
					$code   = $this->generateCouponCode();
					$coupon_array = array(
						'post_title'    => $code,
						'post_author'   => 1,
						'post_date'     => date("Y-m-d H:i:s", $now),
						'post_status'   => 'publish',
						'comment_status'=> 'closed',
						'ping_status'   => 'closed',
						'post_name'     => $code,
						'post_parent'   => 0,
						'menu_order'    => 0,
						'post_type'     => 'shop_coupon'
					);
					$coupon_id = wp_insert_post($coupon_array);
					$wpdb->query("UPDATE {$wpdb->prefix}posts SET post_status = 'publish' WHERE ID = $coupon_id");

					$product_ids = '';
					if (!empty($disc->product_ids)) {
						$pids = unserialize($disc->product_ids);
						foreach ($pids as $pid) {
							$product_ids .= $pid .',';
						}
						$product_ids = rtrim($product_ids, ',');
					}

					$category_ids = '';
					if (!empty($disc->category_ids)) {
						$cids = unserialize($disc->category_ids);
						foreach ($cids as $cid) {
							$category_ids .= $cid.',';
						}
						$category_ids = rtrim($category_ids, ',');
					}

					$expiry = '';
					if ($disc->expiry_value > 0 && !empty($disc->expiry_type)) {
						$exp = $disc->expiry_value .' '. $disc->expiry_type;
						$ts = strtotime(current_time('mysql') . " +$exp");

						if ($ts !== false) {
							$expiry = date('Y-m-d', $ts);
						}
					}

					$validProducts = '';

					if ($disc->all_products == 'no' && $disc->usage_limit == 1) {
						if ( !empty($disc->product_ids) ) {
							$validProducts = implode(',', unserialize($disc->product_ids));
						}

					}

					$email  = $comment->comment_author_email;

					update_post_meta($coupon_id, 'discount_type', $disc->type);
					update_post_meta($coupon_id, 'coupon_amount', $disc->amount);
					update_post_meta($coupon_id, 'individual_use', ($disc->individual == 0) ? 'no' : 'yes');
					update_post_meta($coupon_id, 'product_ids', $disc->usage_limit ? $validProducts : '' );
					update_post_meta($coupon_id, 'exclude_product_ids', '');
					update_post_meta($coupon_id, 'exclude_sale_items', $disc->exclude_sale_items);
					update_post_meta($coupon_id, 'usage_limit', 1);
					update_post_meta($coupon_id, 'expiry_date', $expiry);
					update_post_meta($coupon_id, 'free_shipping', ($disc->free_shipping == 0) ? 'no' : 'yes');
					update_post_meta($coupon_id, 'product_categories', $disc->usage_limit ? maybe_unserialize( $disc->category_ids ) : '' );
					update_post_meta($coupon_id, 'exclude_product_categories', '');
					update_post_meta($coupon_id, 'minimum_amount', '');
					update_post_meta($coupon_id, 'customer_email', array($email) );

					$insert = array(
						'comment_id'    => $comment->comment_ID,
						'discount_id'   => $disc->id,
						'coupon_id'     => $coupon_id,
						'author_email'  => $email
					);
					$wpdb->insert( $wpdb->prefix .'wrd_sent_coupons', $insert );

					$mailer = $woocommerce->mailer();

					$emailSettings  = get_option('wrd_email_settings');
					$email_heading  = $emailSettings['subject'];
					$message        = wpautop($emailSettings['message']);

					// replace variables
					$product_name   = $product->get_title();
					$category_ids   = maybe_unserialize( $disc->category_ids ) ?: array();
					$product_ids    = maybe_unserialize( $disc->product_ids ) ?: array();

					$categories = array_map( function( $category_id ) {
						$category      = get_term( $category_id, 'product_cat' );
						return ( ! $category || is_wp_error( $category ) ) ? '' : $category->name;
					}, $category_ids );

					$products = array_map( function( $product_id ) {
						return get_the_title( $product_id );
					}, $product_ids );

					$valid = array_filter( array_merge( $categories, $products ) );
					$valid = implode( ',', $valid );

					$amount         = $disc->amount;

					if (floor($disc->amount) == $disc->amount) {
						// remove 0s
						$amount = number_format($disc->amount);
					}

					switch ($disc->type) {
						case 'fixed_cart':
						case 'fixed_product':
							$amount = wc_price($amount);
							break;

						case 'percent':
						case 'percent_product':
							$amount = $amount .'%';
							break;
					}

					$vars   = array('{store_name}', '{code}', '{discount_amount}', '{product_name}', '{valid_products}');
					$reps   = array(get_bloginfo('name'), $code, $amount, $product_name, $valid);
					$subject= str_replace($vars, $reps, $emailSettings['subject']);
					$message= str_replace($vars, $reps, $message);

					$message = $mailer->wrap_message( $subject, $message );
					$mailer->send($email, $subject, $message);

					break;
				}
			}
		}

		public function generateCouponCode() {
			global $wpdb;

			$chars = 'abcdefghijklmnopqrstuvwxyz01234567890';
			do {
				$code = '';
				for ($x = 0; $x < 8; $x++) {
					$code .= $chars[ rand(0, strlen($chars)-1) ];
				}

				$check = $wpdb->get_var( $wpdb->prepare("SELECT COUNT(*) FROM `{$wpdb->prefix}posts` WHERE `post_title` = %s AND `post_type` = 'shop_coupon'", $code) );

				if ($check == 0) break;
			} while (true);

			return $code;
		}

		public function isSent($commentId, $discountId, $authorEmail) {
			global $wpdb;

			return (bool)$wpdb->get_var( $wpdb->prepare("SELECT COUNT(*) FROM `{$wpdb->prefix}wrd_sent_coupons` WHERE `comment_id` = %d AND `discount_id` = %d AND `author_email` = %s", $commentId, $discountId, $authorEmail) );
		}

		public function isSentToEmail($commentId, $authorEmail) {
			global $wpdb;

			$comment = get_comment($commentId);
			$postId  = $comment->comment_post_ID;

			// get all comments for this post
			$comments   = get_comments('post_id='. $postId);

			if ( is_array($comments) ) {
				foreach ( $comments as $cmt ) {
					if ( $cmt->comment_ID != $commentId && $cmt->comment_author_email == $authorEmail ) {
						return true;
					}
				}
			}

			return false;
		}

		public function newDiscount($data) {
			global $wpdb;

			$all = $wpdb->get_results("SELECT `product_ids`, `category_ids` FROM {$wpdb->prefix}wrd_discounts");
			foreach ($all as $discount) {
				if (!empty($discount->product_ids)) {
					$ids = unserialize($discount->product_ids);

					if (isset($data['products']) && !empty($data['products'])) {
						foreach ($data['products'] as $pid) {
							if (in_array($pid, $ids)) {
								return new WP_Error('wrd_error', sprintf(__('You already have a discount for %s.', 'wc_review_discount'), get_the_title($pid)));
							}
						}
					}
				}

				if (!empty($discount->category_ids)) {
					$ids = unserialize($discount->category_ids);

					if (isset($data['categories']) && !empty($data['categories'])) {
						foreach ($data['categories'] as $cid) {
							if (in_array($cid, $ids)) {
								$term = get_term_by( 'id', $cid, 'product_cat' );
								return new WP_Error('wrd_error', sprintf(__('You already have a discount for the category: %s.', 'wc_review_discount'), $term->name));
							}
						}
					}
				}
			}

			if (isset($data['products'])) {
				if (empty($data['products'])) {
					$data['products'] = '';
				} else {
					$data['products'] = serialize($data['products']);
				}
			} else {
				$data['products'] = '';
			}

			if (isset($data['categories'])) {
				if (empty($data['categories'])) {
					$data['categories'] = '';
				} else {
					$data['categories'] = serialize($data['categories']);
				}
			} else {
				$data['categories'] = '';
			}

			$now = time();
			$wpdb->query("INSERT INTO `{$wpdb->prefix}wrd_discounts` (
				`type`, `amount`, `individual`, `send_mode`, `send_to_verified`, `product_ids`, `category_ids`, `usage_limit`, `expiry_value`, `expiry_type`, `unique_email`, `date_added`
			) VALUES (
				'". $data['type'] ."', '". $data['amount'] ."', '". $data['individual'] ."', '". $data['send_mode'] ."', '". $data['send_to_verified'] ."',
				'". $data['products'] ."', '". $data['categories'] ."', '". $data['usage_limit'] ."', '". $data['expiry_value'] ."', '". $data['expiry_type'] ."', '". $data['unique_email'] ."', '". date("Y-m-d H:i:s", $now) ."'
			)");

			return true;
		}

		public function editDiscount($id, $data) {
			global $wpdb;

			$all = $wpdb->get_results("SELECT `product_ids`, `category_ids` FROM {$wpdb->prefix}wrd_discounts WHERE `id` != $id");
			foreach ($all as $discount) {
				if (!empty($discount->product_ids)) {
					$ids = unserialize($discount->product_ids);

					if (isset($data['product_ids']) && !empty($data['product_ids'])) {
						$products = unserialize($data['product_ids']);
						foreach ($products as $pid) {
							if (in_array($pid, $ids)) {
								return new WP_Error('wrd_error', sprintf(__('You already have a discount for %s.', 'wc_review_discount'), get_the_title($pid)));
							}
						}
					}
				}

				if (!empty($discount->category_ids)) {
					$ids = unserialize($discount->category_ids);

					if (isset($data['category_ids']) && !empty($data['category_ids'])) {
						$categories = unserialize($data['category_ids']);
						foreach ($categories as $cid) {
							if (in_array($cid, $ids)) {
								$term = get_term_by( 'id', $cid, 'product_cat' );
								return new WP_Error('wrd_error', sprintf(__('You already have a discount for the category: %s.', 'wc_review_discount'), $term->name));
							}
						}
					}
				}
			}

			return $wpdb->update("{$wpdb->prefix}wrd_discounts", $data, array('id' => $id));
		}

		public function getDiscounts() {
			global $wpdb;

			$rows = array();
			$results = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}wrd_discounts` ORDER BY `date_added` ASC");

			foreach ($results as $row) {
				$row->sent = $wpdb->get_var( $wpdb->prepare("SELECT COUNT(*) FROM `{$wpdb->prefix}wrd_sent_coupons` WHERE `discount_id` = %d", $row->id) );
				$rows[] = $row;
			}

			return $rows;
		}

		/**
		 * Check if the product matches any of products assigned to the discount.
		 * It checks for the discount's parent products in case the selected product
		 * is a variation.
		 *
		 * @param int       $product_id
		 * @param object    $discount
		 * @return bool
		 */
		public static function product_matches_discount_products( $product_id, $discount ) {
			$products   = (empty( $discount->product_ids ) ) ? array() : maybe_unserialize( $discount->product_ids );
			$all_ids    = array();

			foreach ( $products as $product_id ) {
				$all_ids[]  = $product_id;
				$product    = sfn_get_product( $product_id );

				if ( 'variation' === $product->get_type() ) {
					$all_ids[] = $product->id;
				}
			}

			return in_array( $product_id, $all_ids );
		}

		public function fix_expiration() {
			global $wpdb;

			$discounts = $wpdb->get_results("SELECT `s`.`coupon_id`, `d`.`expiry_value`, `d`.`expiry_type` FROM {$wpdb->prefix}wrd_sent_coupons `s`, {$wpdb->prefix}wrd_discounts `d` WHERE `s`.`discount_id` = `d`.`id`");

			if ( count($discounts) > 0 ) {
				foreach ( $discounts as $disc ) {
					if ($disc->expiry_value > 0 && !empty($disc->expiry_type)) {
						$coupon = get_post( $disc->coupon_id );
						$exp = $disc->expiry_value .' '. $disc->expiry_type;
						$ts = strtotime( $coupon->post_date . " +$exp");

						if ($ts !== false) {
							$expiry = date('Y-m-d', $ts);
							update_post_meta( $disc->coupon_id, 'expiry_date', $expiry );
						}
					}
				}
			}

			update_option( 'wrd_expiry_fix', true);
		}
	}

	if (! function_exists('sfn_get_product') ) {
		function sfn_get_product( $id ) {
			if ( function_exists('wc_get_product') )
				return wc_get_product( $id );
			elseif ( function_exists('get_product') )
				return get_product( $id );
			else
				return new WC_Product( $id );

		}
	}

	if (! function_exists('sfn_session_set') ) {
		function sfn_session_set( $name, $value ) {
			global $woocommerce;

			if ( isset($woocommerce->session) ) {
				$woocommerce->session->$name = $value;
			} else {
				$_SESSION[ $name ] = $value;
			}
		}
	}

	if (! function_exists('sfn_session_get') ) {
		function sfn_session_get( $name ) {
			global $woocommerce;

			if ( isset($woocommerce->session) ) {
				return (isset($woocommerce->session->$name)) ? $woocommerce->session->$name : null;
			} else {
				return (isset($_SESSION[ $name ])) ? $_SESSION[ $name ] : null;
			}
		}
	}

	if (! function_exists('sfn_session_unset') ) {
		function sfn_session_unset( $name ) {
			global $woocommerce;

			if ( isset($woocommerce->session) ) {
				unset($woocommerce->session->$name);
			} else {
				unset($_SESSION[$name]);
			}
		}
	}
endif;

add_action( 'plugins_loaded', 'woocommerce_review_for_discount_init' );

/**
 * Initialize the extension.
 *
 * @since 1.6.14
 * @return void
 */
function woocommerce_review_for_discount_init() {
	load_plugin_textdomain( 'wc_review_discount', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );

	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', 'woocommerce_review_for_discount_missing_wc_notice' );
		return;
	}

	new SFN_ReviewDiscount();
}
