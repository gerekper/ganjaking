<?php
/**

 * Admin Dashboard Reports
 *
 * Functions used for displaying sales and customer reports in admin.
 *
 * @author      OPMC
 * @category    Admin
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WC_Dropshipping_Dashboard', false ) ) :

	class WC_Dropshipping_Dashboard {

		private $wcd_query;

		private $wcd_count_listings;

		public function __construct() {

			$this->init();

		}

		public function init() {
			add_action( 'admin_init', array( $this, 'custom_dashboard_style' ) );
			add_action( 'wp_ajax_pub_ali_draft_prod', array( $this, 'pub_ali_draft_prod' ) );
		}


		public function pub_ali_draft_prod() {


			//Define Array to Store Draft Product's id
			$ali_draft_id = array();

			//Parameters to fetch all draft products
			$params = array(
			'posts_per_page' => -1,
			'post_type' => array('product', 'product_variation'),
			'post_status' => 'draft',
			);

			// Query firing
			$dp_query = new WP_Query($params);
			if ($dp_query->have_posts()) :
			//While loop through all the posts to identify products imported from Aliexpress
				while ($dp_query->have_posts()) :
					  $dp_query->the_post();
				 // Condition to check if Aliexpress Product Url exists in postmeta to confirm product is imported from Aliexpress
					if (get_post_meta(get_the_id(), 'ali_product_url', true)) {

					// Declaring temp array to prepare data for publishing the product.
					$temp_post_data = array();

					// Preparing data in array for publising
					$temp_post_data = [ 'ID' => get_the_id(), 'post_status' => 'publish' ];

					// Publishing Products which is in Draft
					wp_update_post( $temp_post_data );
					}
				// End While
		   endwhile;
		   // Reset Post data
		   wp_reset_postdata();
		   endif;

			wp_die(); // this is required to terminate immediately and return a proper response

		}

		public function custom_dashboard_style() {

			$base_name = explode( '/', plugin_basename( __FILE__ ) );

			wp_register_script( 'add_dropshipping_chart_lib', plugins_url() . '/' . $base_name[0] . '/lib/js/chart.js', array(), '2.8.0' );

			wp_register_style( 'add_custom_dashboard_style', plugins_url() . '/' . $base_name[0] . '/assets/css/dashboard.css', array(), '1.0.0' );

			wp_register_script( 'add_custom_dashboard_script', plugins_url() . '/' . $base_name[0] . '/assets/js/dashboard.js', array(), '1.0.0' );

		}

		public function get_daily_purchases_total() {
			global $wpdb;

			return $wpdb->get_var(
				"
                SELECT SUM(pm.meta_value)
                FROM {$wpdb->prefix}posts as p
                INNER JOIN {$wpdb->prefix}postmeta as pm ON p.ID = pm.post_id
                WHERE p.post_type = 'shop_order'
                AND p.post_status IN ('wc-processing','wc-completed')
                AND UNIX_TIMESTAMP(p.post_date) >= (UNIX_TIMESTAMP(NOW()) - (86400))
                AND pm.meta_key = '_order_total'
            "
			);
		}

		public function get_daily_total_orders() {
			global $wpdb;

			$total_orders = $wpdb->get_results(
				"
                SELECT * FROM {$wpdb->prefix}posts as p INNER JOIN {$wpdb->prefix}postmeta as pm ON p.ID = pm.post_id WHERE p.post_type = 'shop_order' AND p.post_status IN ('wc-processing','wc-completed') AND UNIX_TIMESTAMP(p.post_date) >= (UNIX_TIMESTAMP(NOW()) - (86400)) GROUP BY p.ID
            "
			);

			return $total_orders;
		}

		public function get_one_week_daily_total_orders() {
			 global $wpdb;
			 $orders = array();
			 $date_7 = date( 'Y-m-d 00:00:00', strtotime( '- 6 days' ) );
			 $date_6 = date( 'Y-m-d 00:00:00', strtotime( '- 5 days' ) );
			 $date_5 = date( 'Y-m-d 00:00:00', strtotime( '- 4 days' ) );
			 $date_4 = date( 'Y-m-d 00:00:00', strtotime( '- 3 days' ) );
			 $date_3 = date( 'Y-m-d 00:00:00', strtotime( '- 2 days' ) );
			 $date_2 = date( 'Y-m-d 00:00:00', strtotime( '- 1 days' ) );
			 $date_1 = date( 'Y-m-d 00:00:00' );

			$total_orders_7 = $wpdb->get_results(
				"
               SELECT pm.*, p.post_date,p.ID FROM {$wpdb->prefix}posts p, {$wpdb->prefix}postmeta pm  WHERE p.ID=pm.post_id  AND pm.meta_key LIKE '%dropship_supplier_%'  AND p.post_type = 'shop_order' AND p.post_status IN ('wc-processing','wc-completed') AND DATE(p.post_date) between DATE_SUB(DATE(NOW()), INTERVAL 7 DAY) AND DATE_SUB(DATE(NOW()), INTERVAL 7 DAY)"
			);
			$total_orders_6 = $wpdb->get_results(
				"
               SELECT pm.*, p.post_date,p.ID FROM {$wpdb->prefix}posts p, {$wpdb->prefix}postmeta pm  WHERE p.ID=pm.post_id  AND pm.meta_key LIKE '%dropship_supplier_%'  AND p.post_type = 'shop_order' AND p.post_status IN ('wc-processing','wc-completed') AND DATE(p.post_date) between DATE_SUB(DATE(NOW()), INTERVAL 6 DAY) AND DATE_SUB(DATE(NOW()), INTERVAL 6 DAY)"
			);
			$total_orders_5 = $wpdb->get_results(
				"
               SELECT pm.*, p.post_date,p.ID FROM {$wpdb->prefix}posts p, {$wpdb->prefix}postmeta pm  WHERE p.ID=pm.post_id  AND pm.meta_key LIKE '%dropship_supplier_%'  AND  p.post_type = 'shop_order' AND p.post_status IN ('wc-processing','wc-completed') AND DATE(p.post_date) between DATE_SUB(DATE(NOW()), INTERVAL 5 DAY) AND DATE_SUB(DATE(NOW()), INTERVAL 5 DAY)"
			);
			$total_orders_4 = $wpdb->get_results(
				"
               SELECT pm.*, p.post_date,p.ID FROM {$wpdb->prefix}posts p, {$wpdb->prefix}postmeta pm  WHERE p.ID=pm.post_id  AND pm.meta_key LIKE '%dropship_supplier_%'  AND  p.post_type = 'shop_order' AND p.post_status IN ('wc-processing','wc-completed') AND DATE(p.post_date) between DATE_SUB(DATE(NOW()), INTERVAL 4 DAY) AND DATE_SUB(DATE(NOW()), INTERVAL 4 DAY)"
			);
			$total_orders_3 = $wpdb->get_results(
				"
               SELECT pm.*, p.post_date,p.ID FROM {$wpdb->prefix}posts p, {$wpdb->prefix}postmeta pm  WHERE p.ID=pm.post_id  AND pm.meta_key LIKE '%dropship_supplier_%'  AND  p.post_type = 'shop_order' AND p.post_status IN ('wc-processing','wc-completed') AND DATE(p.post_date) between DATE_SUB(DATE(NOW()), INTERVAL 3 DAY) AND DATE_SUB(DATE(NOW()), INTERVAL 3 DAY)"
			);
			$total_orders_2 = $wpdb->get_results(
				"
               SELECT pm.*, p.post_date,p.ID FROM {$wpdb->prefix}posts p, {$wpdb->prefix}postmeta pm  WHERE p.ID=pm.post_id  AND pm.meta_key LIKE '%dropship_supplier_%'  AND  p.post_type = 'shop_order' AND p.post_status IN ('wc-processing','wc-completed') AND DATE(p.post_date) between DATE_SUB(DATE(NOW()), INTERVAL 2 DAY) AND DATE_SUB(DATE(NOW()), INTERVAL 2 DAY)"
			);

			$total_orders_1 = $wpdb->get_results(
				"
               SELECT pm.*, p.post_date,p.ID FROM {$wpdb->prefix}posts p, {$wpdb->prefix}postmeta pm  WHERE p.ID=pm.post_id  AND pm.meta_key LIKE '%dropship_supplier_%'  AND  p.post_type = 'shop_order' AND p.post_status IN ('wc-processing','wc-completed') AND DATE(p.post_date) between DATE_SUB(DATE(NOW()), INTERVAL 1 DAY) AND DATE_SUB(DATE(NOW()), INTERVAL 1 DAY)"
			);

			array_push( $orders, (object) $this->get_total_orders_supplierr_by_order_id( $total_orders_7, $date_7 ) );
			array_push( $orders, (object) $this->get_total_orders_supplierr_by_order_id( $total_orders_6, $date_6 ) );
			array_push( $orders, (object) $this->get_total_orders_supplierr_by_order_id( $total_orders_5, $date_5 ) );
			array_push( $orders, (object) $this->get_total_orders_supplierr_by_order_id( $total_orders_4, $date_4 ) );
			array_push( $orders, (object) $this->get_total_orders_supplierr_by_order_id( $total_orders_3, $date_3 ) );
			array_push( $orders, (object) $this->get_total_orders_supplierr_by_order_id( $total_orders_2, $date_2 ) );
			array_push( $orders, (object) $this->get_total_orders_supplierr_by_order_id( $total_orders_1, $date_1 ) );

			return $orders;
		}

		public function get_one_week_daily_profit() {
			global $wpdb;

			 $supplier_ids = get_terms( $this->get_dropship_taxo() );
			 $orders = array();

			 $date_7 = date( 'Y-m-d 00:00:00', strtotime( '- 6 days' ) );
			 $date_6 = date( 'Y-m-d 00:00:00', strtotime( '- 5 days' ) );
			 $date_5 = date( 'Y-m-d 00:00:00', strtotime( '- 4 days' ) );
			 $date_4 = date( 'Y-m-d 00:00:00', strtotime( '- 3 days' ) );
			 $date_3 = date( 'Y-m-d 00:00:00', strtotime( '- 2 days' ) );
			 $date_2 = date( 'Y-m-d 00:00:00', strtotime( '- 1 days' ) );
			 $date_1 = date( 'Y-m-d 00:00:00' );

			$total_orders_7 = $wpdb->get_results(
				"
               SELECT pm.*, p.post_date,p.ID FROM {$wpdb->prefix}posts p, {$wpdb->prefix}postmeta pm  WHERE p.ID=pm.post_id  AND pm.meta_key LIKE '%dropship_supplier_%'  AND p.post_type = 'shop_order' AND p.post_status IN ('wc-processing','wc-completed') AND DATE(p.post_date) between DATE_SUB(DATE(NOW()), INTERVAL 7 DAY) AND DATE_SUB(DATE(NOW()), INTERVAL 7 DAY)"
			);
			$total_orders_6 = $wpdb->get_results(
				"
               SELECT pm.*, p.post_date,p.ID FROM {$wpdb->prefix}posts p, {$wpdb->prefix}postmeta pm  WHERE p.ID=pm.post_id  AND pm.meta_key LIKE '%dropship_supplier_%'  AND p.post_type = 'shop_order' AND p.post_status IN ('wc-processing','wc-completed') AND DATE(p.post_date) between DATE_SUB(DATE(NOW()), INTERVAL 6 DAY) AND DATE_SUB(DATE(NOW()), INTERVAL 6 DAY)"
			);
			$total_orders_5 = $wpdb->get_results(
				"
               SELECT pm.*, p.post_date,p.ID FROM {$wpdb->prefix}posts p, {$wpdb->prefix}postmeta pm  WHERE p.ID=pm.post_id  AND pm.meta_key LIKE '%dropship_supplier_%'  AND  p.post_type = 'shop_order' AND p.post_status IN ('wc-processing','wc-completed') AND DATE(p.post_date) between DATE_SUB(DATE(NOW()), INTERVAL 5 DAY) AND DATE_SUB(DATE(NOW()), INTERVAL 5 DAY)"
			);
			$total_orders_4 = $wpdb->get_results(
				"
               SELECT pm.*, p.post_date,p.ID FROM {$wpdb->prefix}posts p, {$wpdb->prefix}postmeta pm  WHERE p.ID=pm.post_id  AND pm.meta_key LIKE '%dropship_supplier_%'  AND  p.post_type = 'shop_order' AND p.post_status IN ('wc-processing','wc-completed') AND DATE(p.post_date) between DATE_SUB(DATE(NOW()), INTERVAL 4 DAY) AND DATE_SUB(DATE(NOW()), INTERVAL 4 DAY)"
			);
			$total_orders_3 = $wpdb->get_results(
				"
               SELECT pm.*, p.post_date,p.ID FROM {$wpdb->prefix}posts p, {$wpdb->prefix}postmeta pm  WHERE p.ID=pm.post_id  AND pm.meta_key LIKE '%dropship_supplier_%'  AND  p.post_type = 'shop_order' AND p.post_status IN ('wc-processing','wc-completed') AND DATE(p.post_date) between DATE_SUB(DATE(NOW()), INTERVAL 3 DAY) AND DATE_SUB(DATE(NOW()), INTERVAL 3 DAY)"
			);
			$total_orders_2 = $wpdb->get_results(
				"
               SELECT pm.*, p.post_date,p.ID FROM {$wpdb->prefix}posts p, {$wpdb->prefix}postmeta pm  WHERE p.ID=pm.post_id  AND pm.meta_key LIKE '%dropship_supplier_%'  AND  p.post_type = 'shop_order' AND p.post_status IN ('wc-processing','wc-completed') AND DATE(p.post_date) between DATE_SUB(DATE(NOW()), INTERVAL 2 DAY) AND DATE_SUB(DATE(NOW()), INTERVAL 2 DAY)"
			);
			$total_orders_1 = $wpdb->get_results(
				"
               SELECT pm.*, p.post_date,p.ID FROM {$wpdb->prefix}posts p, {$wpdb->prefix}postmeta pm  WHERE p.ID=pm.post_id  AND pm.meta_key LIKE '%dropship_supplier_%'  AND  p.post_type = 'shop_order' AND p.post_status IN ('wc-processing','wc-completed') AND DATE(p.post_date) between DATE_SUB(DATE(NOW()), INTERVAL 1 DAY) AND DATE_SUB(DATE(NOW()), INTERVAL 1 DAY)"
			);

			array_push( $orders, (object) $this->get_supplierr_by_order_id( $total_orders_7, $date_7 ) );
			array_push( $orders, (object) $this->get_supplierr_by_order_id( $total_orders_6, $date_6 ) );
			array_push( $orders, (object) $this->get_supplierr_by_order_id( $total_orders_5, $date_5 ) );
			array_push( $orders, (object) $this->get_supplierr_by_order_id( $total_orders_4, $date_4 ) );
			array_push( $orders, (object) $this->get_supplierr_by_order_id( $total_orders_3, $date_3 ) );
			array_push( $orders, (object) $this->get_supplierr_by_order_id( $total_orders_2, $date_2 ) );
			array_push( $orders, (object) $this->get_supplierr_by_order_id( $total_orders_1, $date_1 ) );

			return $orders;
		}

		public function get_per_week_total_orders() {
			global $wpdb;
			$orders = array();

			$date_5 = date( 'Y-m-d 00:00:00', strtotime( '5 weeks ago monday' ) );
			$date_4 = date( 'Y-m-d 00:00:00', strtotime( '4 weeks ago monday' ) );
			$date_3 = date( 'Y-m-d 00:00:00', strtotime( '3 weeks ago monday' ) );
			$date_2 = date( 'Y-m-d 00:00:00', strtotime( 'last week' ) );
			$date_1 = date( 'Y-m-d 00:00:00', strtotime( 'this week' ) );

			$total_orders_5 = $wpdb->get_results(
				"
               SELECT pm.*, p.post_date,p.ID FROM {$wpdb->prefix}posts p, {$wpdb->prefix}postmeta pm  WHERE p.ID=pm.post_id  AND pm.meta_key LIKE '%dropship_supplier_%'  AND  p.post_type = 'shop_order' AND p.post_status IN ('wc-processing','wc-completed') AND WEEKOFYEAR(post_date) = WEEKOFYEAR(NOW())-4"
			);
			$total_orders_4 = $wpdb->get_results(
				"
               SELECT pm.*, p.post_date,p.ID FROM {$wpdb->prefix}posts p, {$wpdb->prefix}postmeta pm  WHERE p.ID=pm.post_id  AND pm.meta_key LIKE '%dropship_supplier_%'  AND  p.post_type = 'shop_order' AND p.post_status IN ('wc-processing','wc-completed') AND WEEKOFYEAR(post_date) = WEEKOFYEAR(NOW())-3"
			);
			$total_orders_3 = $wpdb->get_results(
				"
               SELECT pm.*, p.post_date,p.ID FROM {$wpdb->prefix}posts p, {$wpdb->prefix}postmeta pm  WHERE p.ID=pm.post_id  AND pm.meta_key LIKE '%dropship_supplier_%'  AND  p.post_type = 'shop_order' AND p.post_status IN ('wc-processing','wc-completed') AND WEEKOFYEAR(post_date) = WEEKOFYEAR(NOW())-2"
			);

			$total_orders_2 = $wpdb->get_results(
				"
               SELECT pm.*, p.post_date,p.ID FROM {$wpdb->prefix}posts p, {$wpdb->prefix}postmeta pm  WHERE p.ID=pm.post_id  AND pm.meta_key LIKE '%dropship_supplier_%'  AND  p.post_type = 'shop_order' AND p.post_status IN ('wc-processing','wc-completed') AND YEARWEEK(post_date)= YEARWEEK(DATE_SUB(CURRENT_DATE(), INTERVAL 1 WEEK))"
			);
			$total_orders_1 = $wpdb->get_results(
				"
               SELECT pm.*, p.post_date,p.ID FROM {$wpdb->prefix}posts p, {$wpdb->prefix}postmeta pm  WHERE p.ID=pm.post_id  AND pm.meta_key LIKE '%dropship_supplier_%'  AND  p.post_type = 'shop_order' AND p.post_status IN ('wc-processing','wc-completed') AND WEEKOFYEAR(post_date) = WEEKOFYEAR(NOW())"
			);

			array_push( $orders, (object) $this->get_total_orders_supplierr_by_order_id( $total_orders_5, $date_5 ) );
			array_push( $orders, (object) $this->get_total_orders_supplierr_by_order_id( $total_orders_4, $date_4 ) );
			array_push( $orders, (object) $this->get_total_orders_supplierr_by_order_id( $total_orders_3, $date_3 ) );
			array_push( $orders, (object) $this->get_total_orders_supplierr_by_order_id( $total_orders_2, $date_2 ) );
			array_push( $orders, (object) $this->get_total_orders_supplierr_by_order_id( $total_orders_1, $date_1 ) );

			return $orders;
		}

		public function get_per_week_profit() {
			global $wpdb;

			 $supplier_ids = get_terms( $this->get_dropship_taxo() );
			 $orders = array();

			 $date_5 = date( 'Y-m-d 00:00:00', strtotime( '5 weeks ago monday' ) );
			 $date_4 = date( 'Y-m-d 00:00:00', strtotime( '4 weeks ago monday' ) );
			 $date_3 = date( 'Y-m-d 00:00:00', strtotime( '3 weeks ago monday' ) );
			 $date_2 = date( 'Y-m-d 00:00:00', strtotime( 'last week' ) );
			 $date_1 = date( 'Y-m-d 00:00:00', strtotime( 'this week' ) );

			$total_orders_5 = $wpdb->get_results(
				"
               SELECT pm.*, p.post_date,p.ID FROM {$wpdb->prefix}posts p, {$wpdb->prefix}postmeta pm  WHERE p.ID=pm.post_id  AND pm.meta_key LIKE '%dropship_supplier_%'  AND  p.post_type = 'shop_order' AND p.post_status IN ('wc-processing','wc-completed') AND WEEKOFYEAR(post_date) = WEEKOFYEAR(NOW())-4"
			);
			$total_orders_4 = $wpdb->get_results(
				"
               SELECT pm.*, p.post_date,p.ID FROM {$wpdb->prefix}posts p, {$wpdb->prefix}postmeta pm  WHERE p.ID=pm.post_id  AND pm.meta_key LIKE '%dropship_supplier_%'  AND  p.post_type = 'shop_order' AND p.post_status IN ('wc-processing','wc-completed') AND WEEKOFYEAR(post_date) = WEEKOFYEAR(NOW())-3"
			);
			$total_orders_3 = $wpdb->get_results(
				"
               SELECT pm.*, p.post_date,p.ID FROM {$wpdb->prefix}posts p, {$wpdb->prefix}postmeta pm  WHERE p.ID=pm.post_id  AND pm.meta_key LIKE '%dropship_supplier_%'  AND  p.post_type = 'shop_order' AND p.post_status IN ('wc-processing','wc-completed') AND WEEKOFYEAR(post_date) = WEEKOFYEAR(NOW())-2"
			);
			$total_orders_2 = $wpdb->get_results(
				"
               SELECT pm.*, p.post_date,p.ID FROM {$wpdb->prefix}posts p, {$wpdb->prefix}postmeta pm  WHERE p.ID=pm.post_id  AND pm.meta_key LIKE '%dropship_supplier_%'  AND  p.post_type = 'shop_order' AND p.post_status IN ('wc-processing','wc-completed') AND YEARWEEK(post_date)= YEARWEEK(DATE_SUB(CURRENT_DATE(), INTERVAL 1 WEEK))"
			);
			$total_orders_1 = $wpdb->get_results(
				"
               SELECT pm.*, p.post_date,p.ID FROM {$wpdb->prefix}posts p, {$wpdb->prefix}postmeta pm  WHERE p.ID=pm.post_id  AND pm.meta_key LIKE '%dropship_supplier_%'  AND  p.post_type = 'shop_order' AND p.post_status IN ('wc-processing','wc-completed') AND WEEKOFYEAR(post_date) = WEEKOFYEAR(NOW())"
			);

			array_push( $orders, (object) $this->get_supplierr_by_order_id( $total_orders_5, $date_5 ) );
			array_push( $orders, (object) $this->get_supplierr_by_order_id( $total_orders_4, $date_4 ) );
			array_push( $orders, (object) $this->get_supplierr_by_order_id( $total_orders_3, $date_3 ) );
			array_push( $orders, (object) $this->get_supplierr_by_order_id( $total_orders_2, $date_2 ) );
			array_push( $orders, (object) $this->get_supplierr_by_order_id( $total_orders_1, $date_1 ) );

			return $orders;
		}

		public function get_one_month_total_orders() {
			 global $wpdb;
			 $orders = array();

			 $date_5 = date( 'Y-m-d 00:00:00', strtotime( '- 4 month' ) );
			 $date_4 = date( 'Y-m-d 00:00:00', strtotime( '- 3 month' ) );
			 $date_3 = date( 'Y-m-d 00:00:00', strtotime( '- 2 month' ) );
			 $date_2 = date( 'Y-m-d 00:00:00', strtotime( '- 1 month' ) );
			 $date_1 = date( 'Y-m-d 00:00:00' );

			$total_orders_5 = $wpdb->get_results(
				"
               SELECT pm.*, p.post_date,p.ID FROM {$wpdb->prefix}posts p, {$wpdb->prefix}postmeta pm  WHERE p.ID=pm.post_id  AND pm.meta_key LIKE '%dropship_supplier_%'  AND  p.post_type = 'shop_order' AND p.post_status IN ('wc-processing','wc-completed') AND month(p.post_date) = month(now())-4"
			);
			$total_orders_4 = $wpdb->get_results(
				"
               SELECT pm.*, p.post_date,p.ID FROM {$wpdb->prefix}posts p, {$wpdb->prefix}postmeta pm  WHERE p.ID=pm.post_id  AND pm.meta_key LIKE '%dropship_supplier_%'  AND  p.post_type = 'shop_order' AND p.post_status IN ('wc-processing','wc-completed') AND month(p.post_date) = month(now())-3"
			);
			$total_orders_3 = $wpdb->get_results(
				"
               SELECT pm.*, p.post_date,p.ID FROM {$wpdb->prefix}posts p, {$wpdb->prefix}postmeta pm  WHERE p.ID=pm.post_id  AND pm.meta_key LIKE '%dropship_supplier_%'  AND  p.post_type = 'shop_order' AND p.post_status IN ('wc-processing','wc-completed') AND month(p.post_date) = month(now())-2"
			);

			$total_orders_2 = $wpdb->get_results(
				"
               SELECT pm.*, p.post_date,p.ID FROM {$wpdb->prefix}posts p, {$wpdb->prefix}postmeta pm  WHERE p.ID=pm.post_id  AND pm.meta_key LIKE '%dropship_supplier_%'  AND  p.post_type = 'shop_order' AND p.post_status IN ('wc-processing','wc-completed') AND month(p.post_date) = month(now())-1"
			);
			$total_orders_1 = $wpdb->get_results(
				"
               SELECT pm.*, p.post_date,p.ID FROM {$wpdb->prefix}posts p, {$wpdb->prefix}postmeta pm  WHERE p.ID=pm.post_id  AND pm.meta_key LIKE '%dropship_supplier_%'  AND  p.post_type = 'shop_order' AND p.post_status IN ('wc-processing','wc-completed') AND month(p.post_date) = month(now())"
			);

			array_push( $orders, (object) $this->get_total_orders_supplierr_by_order_id( $total_orders_5, $date_5 ) );
			array_push( $orders, (object) $this->get_total_orders_supplierr_by_order_id( $total_orders_4, $date_4 ) );
			array_push( $orders, (object) $this->get_total_orders_supplierr_by_order_id( $total_orders_3, $date_3 ) );
			array_push( $orders, (object) $this->get_total_orders_supplierr_by_order_id( $total_orders_2, $date_2 ) );
			array_push( $orders, (object) $this->get_total_orders_supplierr_by_order_id( $total_orders_1, $date_1 ) );

			return $orders;
		}

		public function get_per_month_profit() {
			global $wpdb;

			 $supplier_ids = get_terms( $this->get_dropship_taxo() );
			 $orders = array();
			 $date_5 = date( 'Y-m-d 00:00:00', strtotime( '- 4 month' ) );
			 $date_4 = date( 'Y-m-d 00:00:00', strtotime( '- 3 month' ) );
			 $date_3 = date( 'Y-m-d 00:00:00', strtotime( '- 2 month' ) );
			 $date_2 = date( 'Y-m-d 00:00:00', strtotime( '- 1 month' ) );
			 $date_1 = date( 'Y-m-d 00:00:00' );

			$total_orders_5 = $wpdb->get_results(
				"
               SELECT pm.*, p.post_date,p.ID FROM {$wpdb->prefix}posts p, {$wpdb->prefix}postmeta pm  WHERE p.ID=pm.post_id  AND pm.meta_key LIKE '%dropship_supplier_%'  AND  p.post_type = 'shop_order' AND p.post_status IN ('wc-processing','wc-completed') AND month(p.post_date) = month(now())-4"
			);
			$total_orders_4 = $wpdb->get_results(
				"
               SELECT pm.*, p.post_date,p.ID FROM {$wpdb->prefix}posts p, {$wpdb->prefix}postmeta pm  WHERE p.ID=pm.post_id  AND pm.meta_key LIKE '%dropship_supplier_%'  AND  p.post_type = 'shop_order' AND p.post_status IN ('wc-processing','wc-completed') AND month(p.post_date) = month(now())-3"
			);
			$total_orders_3 = $wpdb->get_results(
				"
               SELECT pm.*, p.post_date,p.ID FROM {$wpdb->prefix}posts p, {$wpdb->prefix}postmeta pm  WHERE p.ID=pm.post_id  AND pm.meta_key LIKE '%dropship_supplier_%'  AND  p.post_type = 'shop_order' AND p.post_status IN ('wc-processing','wc-completed') AND month(p.post_date) = month(now())-2"
			);
			$total_orders_2 = $wpdb->get_results(
				"
               SELECT pm.*, p.post_date,p.ID FROM {$wpdb->prefix}posts p, {$wpdb->prefix}postmeta pm  WHERE p.ID=pm.post_id  AND pm.meta_key LIKE '%dropship_supplier_%'  AND  p.post_type = 'shop_order' AND p.post_status IN ('wc-processing','wc-completed') AND month(p.post_date) = month(now())-1"
			);
			$total_orders_1 = $wpdb->get_results(
				"
               SELECT pm.*, p.post_date,p.ID FROM {$wpdb->prefix}posts p, {$wpdb->prefix}postmeta pm  WHERE p.ID=pm.post_id  AND pm.meta_key LIKE '%dropship_supplier_%'  AND  p.post_type = 'shop_order' AND p.post_status IN ('wc-processing','wc-completed') AND month(p.post_date) = month(now())"
			);

			array_push( $orders, (object) $this->get_supplierr_by_order_id( $total_orders_5, $date_5 ) );
			array_push( $orders, (object) $this->get_supplierr_by_order_id( $total_orders_4, $date_4 ) );
			array_push( $orders, (object) $this->get_supplierr_by_order_id( $total_orders_3, $date_3 ) );
			array_push( $orders, (object) $this->get_supplierr_by_order_id( $total_orders_2, $date_2 ) );
			array_push( $orders, (object) $this->get_supplierr_by_order_id( $total_orders_1, $date_1 ) );

			return $orders;
		}

		public function get_per_year_total_orders() {
			global $wpdb;
			$orders = array();

			$date_5 = date( 'Y-m-d 00:00:00', strtotime( '- 4 year' ) );
			$date_4 = date( 'Y-m-d 00:00:00', strtotime( '- 3 year' ) );
			$date_3 = date( 'Y-m-d 00:00:00', strtotime( '- 2 year' ) );
			$date_2 = date( 'Y-m-d 00:00:00', strtotime( '- 1 year' ) );
			$date_1 = date( 'Y-m-d 00:00:00' );

			$total_orders_5 = $wpdb->get_results(
				"
               SELECT pm.*, p.post_date,p.ID FROM {$wpdb->prefix}posts p, {$wpdb->prefix}postmeta pm  WHERE p.ID=pm.post_id  AND pm.meta_key LIKE '%dropship_supplier_%'  AND  p.post_type = 'shop_order' AND p.post_status IN ('wc-processing','wc-completed') AND year(p.post_date) = year(now())-4"
			);
			$total_orders_4 = $wpdb->get_results(
				"
               SELECT pm.*, p.post_date,p.ID FROM {$wpdb->prefix}posts p, {$wpdb->prefix}postmeta pm  WHERE p.ID=pm.post_id  AND pm.meta_key LIKE '%dropship_supplier_%'  AND  p.post_type = 'shop_order' AND p.post_status IN ('wc-processing','wc-completed') AND year(p.post_date) = year(now())-3"
			);
			$total_orders_3 = $wpdb->get_results(
				"
               SELECT pm.*, p.post_date,p.ID FROM {$wpdb->prefix}posts p, {$wpdb->prefix}postmeta pm  WHERE p.ID=pm.post_id  AND pm.meta_key LIKE '%dropship_supplier_%'  AND  p.post_type = 'shop_order' AND p.post_status IN ('wc-processing','wc-completed') AND year(p.post_date) = year(now())-2"
			);

			$total_orders_2 = $wpdb->get_results(
				"
               SELECT pm.*, p.post_date,p.ID FROM {$wpdb->prefix}posts p, {$wpdb->prefix}postmeta pm  WHERE p.ID=pm.post_id  AND pm.meta_key LIKE '%dropship_supplier_%'  AND  p.post_type = 'shop_order' AND p.post_status IN ('wc-processing','wc-completed') AND year(p.post_date) = year(now())-1"
			);
			$total_orders_1 = $wpdb->get_results(
				"
               SELECT pm.*, p.ID,p.post_date FROM {$wpdb->prefix}posts p, {$wpdb->prefix}postmeta pm  WHERE p.ID=pm.post_id  AND pm.meta_key LIKE '%dropship_supplier_%'  AND  p.post_type = 'shop_order' AND p.post_status IN ('wc-processing','wc-completed') AND year(p.post_date) = year(now())"
			);

			array_push( $orders, (object) $this->get_total_orders_supplierr_by_order_id( $total_orders_5, $date_5 ) );
			array_push( $orders, (object) $this->get_total_orders_supplierr_by_order_id( $total_orders_4, $date_4 ) );
			array_push( $orders, (object) $this->get_total_orders_supplierr_by_order_id( $total_orders_3, $date_3 ) );
			array_push( $orders, (object) $this->get_total_orders_supplierr_by_order_id( $total_orders_2, $date_2 ) );
			array_push( $orders, (object) $this->get_total_orders_supplierr_by_order_id( $total_orders_1, $date_1 ) );

			return $orders;
		}



		public function get_per_year_profit() {
			global $wpdb;

			 $supplier_ids = get_terms( $this->get_dropship_taxo() );
			 $orders = array();
			 $date_5 = date( 'Y-m-d 00:00:00', strtotime( '- 4 year' ) );
			 $date_4 = date( 'Y-m-d 00:00:00', strtotime( '- 3 year' ) );
			 $date_3 = date( 'Y-m-d 00:00:00', strtotime( '- 2 year' ) );
			 $date_2 = date( 'Y-m-d 00:00:00', strtotime( '- 1 year' ) );
			 $date_1 = date( 'Y-m-d 00:00:00' );

			$total_orders_5 = $wpdb->get_results(
				"
               SELECT pm.*, p.post_date,p.ID FROM {$wpdb->prefix}posts p, {$wpdb->prefix}postmeta pm  WHERE p.ID=pm.post_id  AND pm.meta_key LIKE '%dropship_supplier_%'  AND  p.post_type = 'shop_order' AND p.post_status IN ('wc-processing','wc-completed') AND year(p.post_date) = year(now())-4"
			);
			$total_orders_4 = $wpdb->get_results(
				"
               SELECT pm.*, p.post_date,p.ID FROM {$wpdb->prefix}posts p, {$wpdb->prefix}postmeta pm  WHERE p.ID=pm.post_id  AND pm.meta_key LIKE '%dropship_supplier_%'  AND  p.post_type = 'shop_order' AND p.post_status IN ('wc-processing','wc-completed') AND year(p.post_date) = year(now())-3"
			);
			$total_orders_3 = $wpdb->get_results(
				"
               SELECT pm.*, p.post_date,p.ID FROM {$wpdb->prefix}posts p, {$wpdb->prefix}postmeta pm  WHERE p.ID=pm.post_id  AND pm.meta_key LIKE '%dropship_supplier_%'  AND  p.post_type = 'shop_order' AND p.post_status IN ('wc-processing','wc-completed') AND year(p.post_date) = year(now())-2"
			);
			$total_orders_2 = $wpdb->get_results(
				"
               SELECT pm.*, p.post_date,p.ID FROM {$wpdb->prefix}posts p, {$wpdb->prefix}postmeta pm  WHERE p.ID=pm.post_id  AND pm.meta_key LIKE '%dropship_supplier_%'  AND  p.post_type = 'shop_order' AND p.post_status IN ('wc-processing','wc-completed') AND year(p.post_date) = year(now())-1"
			);
			$total_orders_1 = $wpdb->get_results(
				"
               SELECT pm.*, p.post_date,p.ID FROM {$wpdb->prefix}posts p, {$wpdb->prefix}postmeta pm  WHERE p.ID=pm.post_id  AND pm.meta_key LIKE '%dropship_supplier_%'  AND  p.post_type = 'shop_order' AND p.post_status IN ('wc-processing','wc-completed') AND year(p.post_date) = year(now())"
			);

			array_push( $orders, (object) $this->get_supplierr_by_order_id( $total_orders_5, $date_5 ) );
			array_push( $orders, (object) $this->get_supplierr_by_order_id( $total_orders_4, $date_4 ) );
			array_push( $orders, (object) $this->get_supplierr_by_order_id( $total_orders_3, $date_3 ) );
			array_push( $orders, (object) $this->get_supplierr_by_order_id( $total_orders_2, $date_2 ) );
			array_push( $orders, (object) $this->get_supplierr_by_order_id( $total_orders_1, $date_1 ) );

			return $orders;
		}

		private function get_total_orders_supplierr_by_order_id( $orders, $date ) {

			if ( ! empty( $orders ) ) {
				 $order_with_supp = array();
				 $supplier_ids = get_terms( $this->get_dropship_taxo() );

				foreach ( $orders as $order_id ) {

					foreach ( $supplier_ids as $supplier_id ) {

						$supplier_name = get_post_meta( $order_id->ID, 'supplier_' . $supplier_id->term_id );

						if ( ! empty( $supplier_name ) ) {

							array_push( $order_with_supp, $order_id->ID );

						}
					}
				}

				 $order_with_supp = array_unique( $order_with_supp );

				 $order_with_supp = array(
					 'post_date' => $date,
					 'total' => count( $order_with_supp ),
				 );
			} else {
				$order_with_supp = array(
					'post_date' => $date,
					'total' => 0,
				);
			}

			  return $order_with_supp;
		}


		private function get_supplierr_by_order_id( $orders, $date ) {

			if ( ! empty( $orders ) ) {
				 $order_with_supp = array();
				 $supplier_ids = get_terms( $this->get_dropship_taxo() );
				 $total_sales = 0;
				 $count_orders = 0;
				 $total_profit = 0;
				foreach ( $orders as $order_id ) {

					 // echo $order_id->ID;
					foreach ( $supplier_ids as $supplier_id ) {

						$supplier_name = get_post_meta( $order_id->ID, 'supplier_' . $supplier_id->term_id );

						if ( ! empty( $supplier_name ) ) {

							array_push( $order_with_supp, $order_id->ID );

						}
					}
				}

				 $order_with_supp = array_unique( $order_with_supp );

				foreach ( $order_with_supp as $order ) {

					   $order_total = wc_get_order( $order );

					   $total_sales += $order_total->get_total();

					   $cost_of_goods += get_post_meta( $order, 'cost_of_goods_total', true ) ? get_post_meta( $order, 'cost_of_goods_total', true ) : 0;

					   $total_tax_price += $order_total->get_total_tax() ? $order_total->get_total_tax() : 0;

					   $total_shipping_price += $order_total->get_shipping_total() ? $order_total->get_shipping_total() : 0;

				}

				$total_profit = abs( $total_sales - ( $cost_of_goods + $total_tax_price + $total_shipping_price ) );

				$order_with_supp = (object) array(
					'post_date' => $date,
					'total' => round( $total_profit ),
				);

			} else {
				$order_with_supp = (object) array(
					'post_date' => $date,
					'total' => 0,
				);
			}

				return $order_with_supp;

		}

		private function get_all_prod_args() {

			$args = array(

				'post_type' => 'product',

				'posts_per_page' => -1,

				'post_status'   => 'publish',

				'fields' => 'ids',

			);

			return $args;

		}



		private function get_prod_args_with_supplier() {

			$args = array(

				'post_type' => 'product',

				'posts_per_page' => -1,

				'meta_query' => array(
					'relation' => 'AND',

					array(

						'key' => 'supplierid',

						'compare' => '>',

						'value' => 0,

					),
					array(

						'key' => 'supplierid',

						'compare' => '!=',

						'value' => '',

					),

				),

				'post_status'   => 'publish',

				'fields' => 'ids',

			);

			return $args;

		}


		private function get_prod_args_without_supplier() {

			$args = array(

				'post_type' => 'product',

				'posts_per_page' => -1,

				'meta_query' => array(
					'relation' => 'OR',
					array(

						'key' => 'supplierid',
						'compare' => 'NOT EXISTS',
					),
					array(

						'key' => 'supplierid',
						'compare' => '=',
						'value' => '',
					),

				),

				'post_status'   => 'publish',

				'fields' => 'ids',

			);

			return $args;

		}


		private function get_order_args() {

			$args = array(

				'return' => 'ids',

				'numberposts' => -1,

			);

			return $args;

		}



		private function get_dropship_taxo() {

			$args = array(

				'taxonomy' => 'dropship_supplier',

			);

			return $args;

		}



		private function get_current_currency() {

			if ( false !== function_exists( 'get_woocommerce_currency' ) ) {

				return get_woocommerce_currency_symbol();

			}

		}



		public function get_prod_ids() {

			$product_ids = get_posts( $this->get_prod_args_with_supplier() );

			return $product_ids;

		}



		public function count_prod_listings() {

			$products = new WP_Query( $this->get_prod_args_with_supplier() );

			return $products->found_posts;

		}

		public function count_untracked_prod_listings() {
			global $wpdb;
			$products = new WP_Query( $this->get_prod_args_without_supplier() );

			return $products->found_posts;

		}




		public function count_prod_out_stock() {

			$count = 0;

			$product_ids = $this->get_prod_ids();

			foreach ( $product_ids as $product_id ) {

				$stock_status = get_post_meta( $product_id, '_stock_status', true );

				if ( 'outofstock' == $stock_status ) {

					$count++;

				}
			}
			return $count;

		}



		public function count_orders() {

			$order_with_supp = array();

			$order_ids = wc_get_orders( $this->get_order_args() );

			$supplier_ids = get_terms( $this->get_dropship_taxo() );

			$total_sales = 0;

			$count_orders = 0;

			$total_profit = 0;

			$projected_profit = 0;

			foreach ( $order_ids as $order_id ) {

				foreach ( $supplier_ids as $supplier_id ) {

					$supplier_name = get_post_meta( $order_id, 'supplier_' . $supplier_id->term_id );

					if ( ! empty( $supplier_name ) ) {

						array_push( $order_with_supp, $order_id );

					}
				}
			}

			$order_with_supp = array_unique( $order_with_supp );

			foreach ( $order_with_supp as $order ) {

				$order_total = wc_get_order( $order );

				$total_sales = $order_total->get_total();

				$cost_of_goods = get_post_meta( $order, 'cost_of_goods_total', true );

				$total_tax_price = $order_total->get_total_tax();

				$total_shipping_price = $order_total->get_shipping_total();

				if ( isset( $cost_of_goods ) && is_numeric( $cost_of_goods ) ) {

					$total_profit = abs( $total_sales - $cost_of_goods );

				}

				if ( isset( $total_tax_price ) && is_numeric( $total_tax_price ) ) {

					$total_profit = abs( $total_profit - $total_tax_price );

				}

				if ( isset( $total_shipping_price ) && is_numeric( $total_shipping_price ) ) {

					$total_profit = abs( $total_profit - $total_shipping_price );

				}

				$projected_profit = $projected_profit + $total_profit;

			}

			$count_orders = count( $order_with_supp );

			return array( $count_orders, $projected_profit, $order_with_supp );

		}



		public function get_currency() {

			$currency = $this->get_current_currency();

			return $currency;

		}



		public function get_ali_orders() {

			$order_with_supp = array();

			$order_ids = wc_get_orders( $this->get_order_args() );

			$total_sales = 0;

			$count_orders = 0;

			foreach ( $order_ids as $order_id ) {

				$is_ali = get_post_meta( $order_id, 'status_of_aliexpress' );

				if ( ! empty( $is_ali ) ) {

					array_push( $order_with_supp, $order_id );

				}
			}

			foreach ( $order_with_supp as $order ) {

				$order_total = wc_get_order( $order );

				$total_sales = $total_sales + $order_total->get_total();

			}

			$count_orders = count( array_unique( $order_with_supp ) );

			return array( $count_orders, $total_sales );

		}



		public function get_inprogress_orders() {

			$args = $this->get_order_args();

			$args['post_status'] = 'wc-processing';

			$orders = wc_get_orders( $args );

			return $orders;

		}



		public function get_completed_orders() {

			$args = $this->get_order_args();

			$args['post_status'] = 'completed';

			$orders = wc_get_orders( $args );

			return count( $orders );

		}



		public function get_completed_dropship_orders() {

			$args = $this->get_order_args();

			$args['post_status'] = 'completed';

			$args['fields'] = 'ids';

			$orders = wc_get_orders( $args );

			$order_with_supp = array();

			$supplier_ids = get_terms( $this->get_dropship_taxo() );

			foreach ( $orders as $order_id ) {

				foreach ( $supplier_ids as $supplier_id ) {

					$supplier_name = get_post_meta( $order_id, 'supplier_' . $supplier_id->term_id );

					if ( ! empty( $supplier_name ) ) {

						array_push( $order_with_supp, $order_id );

					}
				}
			}

			if ( isset( $order_with_supp ) && is_array( $order_with_supp ) ) {

				array_unique( $order_with_supp );

			}

			return count( $order_with_supp );

		}



		public function get_pending_orders() {

			$args = $this->get_order_args();

			$args['post_status'] = array( 'wc-pending', 'wc-on-hold' );

			$orders = wc_get_orders( $args );

			return count( $orders );

		}



		public function get_best_selling_prod() {

			$limit = 0;

			$prod_orders_count = array();

			$product_ids = $this->get_prod_ids();

			foreach ( $product_ids as $product_id ) {

				$total_prod_orders = get_post_meta( $product_id, 'total_sales', true );

				$product = wc_get_product( $product_id );

				$product_thumb = $product->get_image( 'thumb' );

				$product_name = $product->get_name();

				$product_url = get_permalink( $product_id );

				$prod_orders_count[ $product_id ] = array( intval( $total_prod_orders ), $product_name, $product_thumb, $product_url );

				$limit++;

				if ( $limit == 5 ) {

					break;

				}
			}

			arsort( $prod_orders_count );

			return $prod_orders_count;

		}



		public function get_low_on_stocks_prod() {

			$limit = 0;

			$prod_orders_stocks_count = array();

			$product_ids = $this->get_prod_ids();

			foreach ( $product_ids as $product_id ) {

				$total_prod_stocks = get_post_meta( $product_id, '_stock', true );

				$total_prod_stocks_status = get_post_meta( $product_id, '_stock_status', true );

				if ( empty( $total_prod_stocks ) ) {

					if ( 'outofstock' == $total_prod_stocks_status ) {

						$total_prod_stocks = 0;

					} else {

						continue;

					}
				}

				$product = wc_get_product( $product_id );

				$product_thumb = $product->get_image( 'thumb' );

				$product_name = $product->get_name();

				$product_url = get_permalink( $product_id );

				$prod_orders_stocks_count[ $product_id ] = array( intval( $total_prod_stocks ), $product_name, $product_thumb, $product_url );

				$limit++;

				if ( $limit == 5 ) {

					break;

				}
			}

			array_multisort( $prod_orders_stocks_count );

			return $prod_orders_stocks_count;

		}



		public function get_affiliate_prod() {

			$prod_ids = get_posts( $this->get_all_prod_args() );

			$order_with_aff = array();

			foreach ( $prod_ids as $prod_id ) {

				$affiliate_id = get_post_meta( $prod_id, 'product_custom_field_amazon_product_id', true );

				if ( '' !== $affiliate_id ) {

					array_push( $order_with_aff, $prod_id );

				}
			}

			$count_orders = count( array_unique( $order_with_aff ) );

			return $count_orders;

		}



		public function get_week_total_sales() {

			$current_num_days = 7;

			$order_ids = $this->count_orders();

			$order_ids = $order_ids[2];

			$current_date = date( 'd' );

			$current_month = date( 'm' );

			$current_year = date( 'y' );

			$seven_last_days = array();

			$last_month = $current_month - 1;

			$last_month = ( 0 == $last_month ) ? 12 : $last_month;

			$last_month = sprintf( '%02d', $last_month );

			$days_last_month = cal_days_in_month( CAL_GREGORIAN, $last_month, $current_year );

			$seven_last_order_ids = array();

			$order_date = array();

			$order_per_day_total = array();

			$last_day_orders_data = array();

			$active_months = array();

			$profit = array();

			for ( $i = 0; $i < $current_num_days; ++$i ) {

				if ( $current_date == 1 ) {

					array_push( $seven_last_days, intval( $current_date ) );

					array_push( $active_months, intval( $current_month ) );

					$current_date = $days_last_month;

					$current_month = $last_month;

					continue;

				}

				if ( $current_date == $days_last_month ) {

					array_push( $seven_last_days, intval( $current_date ) );

					array_push( $active_months, intval( $current_month ) );

					$current_date--;

					continue;

				}

				array_push( $seven_last_days, intval( $current_date ) );

				array_push( $active_months, intval( $current_month ) );

				$current_date--;

			}

			$order_per_day_total['last7days'] = $seven_last_days;

			$order_per_day_total['active_months'] = $active_months;

			foreach ( $order_ids as $order_id ) {

				$new_current_month = date( 'm' );

				$date_paid = get_post_meta( $order_id, '_date_paid', true );

				$date_paid = intval( $date_paid );

				$date_paid_year = intval( gmdate( 'y', $date_paid ) );

				$date_paid_month = intval( gmdate( 'm', $date_paid ) );

				$date_paid_date = intval( gmdate( 'd', $date_paid ) );

				if ( $current_year == $date_paid_year ) {

					if ( $new_current_month == $date_paid_month || $last_month == $date_paid_month ) {

						if ( in_array( $date_paid_date, $seven_last_days, false ) ) {

							if ( empty( $order_date[ $date_paid_date ] ) ) {

								$order_date[ $date_paid_date ] = array();

							}

							array_push( $order_date[ $date_paid_date ], $order_id );

							array_push( $seven_last_order_ids, $order_date );

						}
					}
				}
			}

			if ( empty( $seven_last_order_ids ) ) {

				return;

			}

			if ( function_exists( 'array_key_last' ) ) {

				$last_key = array_key_last( $seven_last_order_ids );

			} else {

				end( $seven_last_order_ids );

				$last_key = key( $seven_last_order_ids );

			}

			$seven_last_order_ids = $seven_last_order_ids[ $last_key ];

			foreach ( $seven_last_order_ids as $current_date => $current_day_order_id ) {

				$total_sales_per_day = 0;

				$cost_of_goods_total = 0;

				$total_tax = 0;

				$total_shipping = 0;

				$total_profit = 0;

				foreach ( $current_day_order_id as $key => $value ) {

					$cost_of_goods = get_post_meta( $value, 'cost_of_goods_total', true );

					if ( empty( $cost_of_goods ) ) {

						$cost_of_goods = 0;

					}

					$order_total = wc_get_order( $value );

					$total_sales_per_day = $total_sales_per_day + $order_total->get_total();

					$total_tax_price = $order_total->get_total_tax();

					$total_shipping_price = $order_total->get_shipping_total();

					$cost_of_goods_total = $cost_of_goods_total + $cost_of_goods;

					$total_tax = $total_tax + $total_tax_price;

					$total_shipping = $total_shipping + $total_shipping_price;

				}

				$last_day_orders_data[ $current_date ] = $total_sales_per_day;

				$profit[ $current_date ] = abs( $total_sales_per_day - ( $cost_of_goods_total + $total_shipping + $total_tax ) );

				if ( $last_day_orders_data[ $current_date ] == $profit[ $current_date ] ) {

					$profit[ $current_date ] = 0;

				}
			}

			$order_per_day_total['currency'] = $this->get_currency();

			wp_localize_script( 'add_custom_dashboard_script', 'last_day_orders', $order_per_day_total );

			wp_localize_script( 'add_custom_dashboard_script', 'last_day_orders_data', $last_day_orders_data );

			wp_localize_script( 'add_custom_dashboard_script', 'profit', $profit );

			if ( isset( $profit ) && is_array( $profit ) ) {

				$profit = array_sum( $profit );         }

			$order_per_day_total['profit'] = $profit;

			return $order_per_day_total;

		}

		public function get_daily_total_sales() {

			$current_num_days = 1;

			$order_ids = $this->get_daily_total_orders();

			$seven_last_days = array();

			$seven_last_order_ids = array();

			$order_date = array();

			$order_per_day_total = array();

			$last_day_orders_data = array();

			$active_months = array();

			$profit = array();

			$last_date = date( 'd', strtotime( '-1 days' ) );

			foreach ( $order_ids as $key => $value ) {

				$total_sales_per_day = 0;

				$cost_of_goods_total = 0;

				$total_tax = 0;

				$total_shipping = 0;

				$total_profit = 0;

				$current_date = '';

				if ( isset( $last_day_orders_data['current_date'] ) ) {
					$current_date = $last_day_orders_data['current_date'];
				} else {
					$current_date = '';
				}

					$cost_of_goods = get_post_meta( $value->ID, 'cost_of_goods_total', true );

				if ( empty( $cost_of_goods ) ) {

					$cost_of_goods = 0;

				}

					$order_total = wc_get_order( $value->ID );

					$total_sales_per_day = $total_sales_per_day + $order_total->get_total();

					$total_tax_price = $order_total->get_total_tax();

					$total_shipping_price = $order_total->get_shipping_total();

					$cost_of_goods_total = $cost_of_goods_total + $cost_of_goods;

					$total_tax = $total_tax + $total_tax_price;

					$total_shipping = $total_shipping + $total_shipping_price;

				$last_day_orders_data[ $current_date ] = $total_sales_per_day;

				$profit[] = abs( $total_sales_per_day - ( $cost_of_goods_total + $total_shipping + $total_tax ) );

			}

			$order_per_day_total['currency'] = $this->get_currency();

			wp_localize_script( 'add_custom_dashboard_script', 'last_day_orders', $order_per_day_total );

			wp_localize_script( 'add_custom_dashboard_script', 'last_day_orders_data', $last_day_orders_data );

			wp_localize_script( 'add_custom_dashboard_script', 'profit', $profit );

			if ( isset( $profit ) && is_array( $profit ) ) {

				$profit = array_sum( $profit );         }

			$order_per_day_total['profit'] = $profit;

			return $order_per_day_total;

		}

		public function get_products_draft_count() {
			// array of draft products
			$ali_draft_id = array();

			// Parameters to get draft products
			$params = array(
			'posts_per_page' => -1,
			'post_type' => array('product', 'product_variation'),
			'post_status' => 'draft',
			);

			$dp_query = new WP_Query($params);

			if ($dp_query->have_posts()) :

				while ($dp_query->have_posts()) :
					$dp_query->the_post();

				// Checking if Aliexpress Product Url exists in postmeta to confirm that product is imported from Aliexpress
					if (get_post_meta(get_the_id(), 'ali_product_url', true)) {
						array_push($ali_draft_id, get_the_id());
					}
			endwhile;
			wp_reset_postdata();
			endif;
			return count($ali_draft_id);

		}


	}

endif;
