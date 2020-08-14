<?php

/**
 * Class YWPAR_Unit_Test_Case_With_Store.
 *
 * This helper class should ONLY be used for unit tests!.
 */
class YWPAR_Unit_Test_Case_With_Store extends WP_UnitTestCase {
	/**
	 * @var WC_Product[] Array of products to clean up.
	 */
	protected $products = array();


	/**
	 * @var WP_Post[] Array of posts to clean up.
	 */
	protected $posts = array();
	static $currency = 'GBP';
	static $user = false;

	/**
	 * Helper function to hold a reference to created product objects so they
	 * can be cleaned up properly at the end of each test.
	 *
	 * @param WC_Product $product The product object to store.
	 */
	protected function store_product( $product ) {
		$this->products[] = $product;
	}

	/**
	 * Helper function to hold a reference to created product objects so they
	 * can be cleaned up properly at the end of each test.
	 *
	 * @return WC_Product
	 */
	protected function create_and_store_points_product( $args = array() ) {
		$product = YWSBS_Helper_Points_Product::create_points_product( $args );
		$this->store_product( $product );

		return $product;
	}

	protected function create_and_store_create_points_single_product_with_category( $args = array(), $categories = array() ) {
		$product = YWSBS_Helper_Points_Product::create_points_single_product_with_category( $args, $categories );
		$this->store_product( $product );

		return $product;
	}

	/**
	 * Helper function to hold a reference to created product objects so they
	 * can be cleaned up properly at the end of each test.
	 *
	 * @return WC_Product
	 */
	protected function create_and_store_points_variation_product( $args = array() ) {
		$product = YWSBS_Helper_Points_Product::create_points_variation_product( $args );
		$this->store_product( $product );
		return $product;
	}

	protected function create_and_store_create_points_variation_product_with_category( $args = array(), $categories = array() ) {
		$product = YWSBS_Helper_Points_Product::create_points_variation_product_with_category( $args, $categories );
		$this->store_product( $product );

		return $product;
	}

	public function setUp() {
		parent::setUp();
		$this->set_options();
		wp_set_current_user( $this->create_user()->get_id());
		$this->products = array();
	}

	public function add_points_to_user( $points = 1000 ){
		$user = wp_get_current_user();
		update_user_meta( $user->ID, '_ywpar_user_total_points', $points );
	}


	public function set_options(){
		$options = 'a:79:{s:7:"enabled";s:3:"yes";s:20:"enabled_shop_manager";b:0;s:24:"enable_points_upon_sales";s:3:"yes";s:27:"earn_points_conversion_rate";a:1:{s:3:"GBP";a:2:{s:6:"points";s:1:"1";s:5:"money";s:2:"10";}}s:22:"conversion_rate_method";s:5:"fixed";s:34:"rewards_percentual_conversion_rate";a:1:{s:3:"GBP";a:2:{s:6:"points";s:2:"20";s:8:"discount";s:1:"5";}}s:23:"max_percentual_discount";s:1:"0";s:23:"rewards_conversion_rate";a:1:{s:3:"GBP";a:2:{s:6:"points";s:3:"100";s:5:"money";s:1:"1";}}s:19:"max_points_discount";s:0:"";s:33:"minimum_amount_discount_to_redeem";s:0:"";s:27:"max_points_product_discount";s:0:"";s:24:"minimum_amount_to_redeem";s:0:"";s:29:"allow_free_shipping_to_redeem";b:0;s:13:"other_coupons";s:4:"both";s:26:"remove_point_order_deleted";s:3:"yes";s:25:"remove_point_refund_order";s:3:"yes";s:37:"reassing_redeemed_points_refund_order";b:0;s:20:"remove_points_coupon";s:3:"yes";s:26:"hide_point_system_to_guest";s:3:"yes";s:31:"show_point_list_my_account_page";s:3:"yes";s:21:"my_account_page_label";s:9:"My Points";s:24:"my_account_page_endpoint";s:9:"my-points";s:27:"apply_points_previous_order";s:0:"";s:12:"reset_points";s:0:"";s:23:"coupon_delete_after_use";s:3:"yes";s:17:"user_role_enabled";a:1:{i:0;s:3:"all";}s:31:"enable_conversion_rate_for_role";s:2:"no";s:21:"conversion_rate_level";s:4:"high";s:30:"earn_points_role_administrator";a:1:{s:3:"GBP";a:2:{s:6:"points";i:10;s:5:"money";i:1;}}s:23:"earn_points_role_editor";a:1:{s:3:"GBP";a:2:{s:6:"points";i:10;s:5:"money";i:1;}}s:23:"earn_points_role_author";a:1:{s:3:"GBP";a:2:{s:6:"points";i:10;s:5:"money";i:1;}}s:28:"earn_points_role_contributor";a:1:{s:3:"GBP";a:2:{s:6:"points";i:10;s:5:"money";i:1;}}s:27:"earn_points_role_subscriber";a:1:{s:3:"GBP";a:2:{s:6:"points";i:10;s:5:"money";i:1;}}s:25:"earn_points_role_customer";a:1:{s:3:"GBP";a:2:{s:6:"points";i:10;s:5:"money";i:1;}}s:29:"earn_points_role_shop_manager";a:1:{s:3:"GBP";a:2:{s:6:"points";i:10;s:5:"money";i:1;}}s:24:"user_role_redeem_enabled";a:1:{i:0;s:3:"all";}s:23:"rewards_points_for_role";s:2:"no";s:20:"rewards_points_level";s:4:"high";s:33:"rewards_points_role_administrator";a:1:{s:3:"GBP";a:2:{s:6:"points";i:100;s:5:"money";i:1;}}s:26:"rewards_points_role_editor";a:1:{s:3:"GBP";a:2:{s:6:"points";i:100;s:5:"money";i:1;}}s:26:"rewards_points_role_author";a:1:{s:3:"GBP";a:2:{s:6:"points";i:100;s:5:"money";i:1;}}s:31:"rewards_points_role_contributor";a:1:{s:3:"GBP";a:2:{s:6:"points";i:100;s:5:"money";i:1;}}s:30:"rewards_points_role_subscriber";a:1:{s:3:"GBP";a:2:{s:6:"points";i:100;s:5:"money";i:1;}}s:28:"rewards_points_role_customer";a:1:{s:3:"GBP";a:2:{s:6:"points";i:100;s:5:"money";i:1;}}s:32:"rewards_points_role_shop_manager";a:1:{s:3:"GBP";a:2:{s:6:"points";i:100;s:5:"money";i:1;}}s:21:"points_label_singular";s:5:"Point";s:19:"points_label_plural";s:6:"Points";s:21:"label_order_completed";s:15:"Order Completed";s:22:"label_order_processing";s:16:"Order Processing";s:21:"label_order_cancelled";s:15:"Order Cancelled";s:18:"label_admin_action";s:12:"Admin Action";s:17:"label_reviews_exp";s:7:"Reviews";s:22:"label_registration_exp";s:12:"Registration";s:16:"label_points_exp";s:34:"Target achieved - Points collected";s:22:"label_amount_spent_exp";s:36:"Target achieved - Total spend amount";s:23:"label_num_of_orders_exp";s:30:"Target achieved - Total Orders";s:20:"label_expired_points";s:14:"Expired Points";s:18:"label_order_refund";s:12:"Order Refund";s:20:"label_refund_deleted";s:20:"Order Refund Deleted";s:21:"label_redeemed_points";s:25:"Redeemed Points for order";s:21:"label_apply_discounts";s:14:"Apply Discount";s:23:"enable_expiration_point";s:3:"yes";s:22:"days_before_expiration";s:0:"";s:33:"send_email_before_expiration_date";s:3:"yes";s:22:"send_email_days_before";s:0:"";s:24:"expiration_email_content";s:145:"Hi {username},
this email has been seent to remind you you have {expiring_points} {label_points} about to expire.
Expiry date is {expiring_date}.";s:25:"enable_update_point_email";s:2:"no";s:26:"update_point_email_content";s:117:"Hi {username}, you can find below latest updates about your {label_points}. {latest_updates} Total is {total_points}.";s:30:"enabled_single_product_message";s:3:"yes";s:31:"single_product_message_position";s:18:"before_add_to_cart";s:22:"single_product_message";s:125:"If you purchase this product you will earn <strong>{points}</strong> {points_label}! Worth {price_discount_fixed_conversion}!";s:20:"enabled_loop_message";s:2:"no";s:12:"loop_message";s:40:"<strong>{points}</strong> {points_label}";s:20:"enabled_cart_message";s:3:"yes";s:12:"cart_message";s:83:"If you proceed to checkout, you will earn <strong>{points}</strong> {points_label}!";s:24:"enabled_checkout_message";s:3:"yes";s:16:"checkout_message";s:83:"If you proceed to checkout, you will earn <strong>{points}</strong> {points_label}!";s:28:"enabled_rewards_cart_message";s:3:"yes";s:20:"rewards_cart_message";s:106:"Use <strong>{points}</strong> {points_label} for a <strong>{max_discount}</strong> discount on this order!";}';

		add_option('yit_ywpar_options', unserialize( $options ) );
		$this->add_new_options();
		ywpar_options_porting( unserialize( $options ) );

		YITH_WC_Points_Rewards()->set_option( 'reassing_redeemed_points_refund_order', 'yes' );


		YITH_WC_Points_Rewards_Earning()->init();
		YITH_WC_Points_Rewards_Redemption()->init();
	}

	public function create_user(){

		$user = get_user_by('email', 'testing@woo.dev');
		if( $user ){
			wp_delete_user( $user->ID );
		}

		$object = new WC_Mock_WC_Data();

		$object->data_store->set_meta_type( 'user' );
		$object->data_store->set_object_id_field( 'user_id' );
		$object->set_content( 'testing@woo.dev' );
		$object->save();
		return $object;
	}

	/**
	 * Clean up after each test. DB changes are reverted in parent::tearDown().
	 */
	public function tearDown() {

		foreach ( $this->products as $product ) {
			$product->delete( true );
		}

		parent::tearDown();
	}

	public function add_new_options(  ) {
		$option = array('woocommerce_order_status_completed', 'woocommerce_payment_complete', 'woocommerce_order_status_processing');
		YITH_WC_Points_Rewards()->set_option( 'order_status_to_earn_points', $option );

	}
}
