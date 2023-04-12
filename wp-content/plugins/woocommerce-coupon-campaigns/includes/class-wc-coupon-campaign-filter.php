<?php
/**
 * Coupon Campaigns Filter.
 *
 * @package woocommerce-coupon-campaigns
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Automattic\WooCommerce\Admin\PageController;
use Automattic\WooCommerce\Blocks\Package;
use Automattic\WooCommerce\Blocks\Assets\AssetDataRegistry;

/**
 * The coupon campaign filter class.
 */
class WC_Coupon_Campaign_Filter {

	/**
	 * Hooks the necessary methods into their actions.
	 */
	public function load(): void {
		add_action( 'current_screen', array( $this, 'load_coupon_campaign_settings' ) );

		$this->load_coupon_campaign_query_filters();
	}

	/**
	 * Load coupon campaign query filters.
	 */
	private function load_coupon_campaign_query_filters(): void {

		// Add query joins.
		add_filter( 'woocommerce_analytics_clauses_join_coupons_subquery', array( $this, 'add_join_subquery' ) );
		add_filter( 'woocommerce_analytics_clauses_join_coupons_stats_total', array( $this, 'add_join_subquery' ) );
		add_filter( 'woocommerce_analytics_clauses_join_coupons_stats_interval', array( $this, 'add_join_subquery' ) );

		// Add query where.
		add_filter( 'woocommerce_analytics_clauses_where_coupons_subquery', array( $this, 'add_where_subquery' ) );
		add_filter( 'woocommerce_analytics_clauses_where_coupons_stats_total', array( $this, 'add_where_subquery' ) );
		add_filter( 'woocommerce_analytics_clauses_where_coupons_stats_interval', array( $this, 'add_where_subquery' ) );

		// Update coupon query args.
		add_filter( 'woocommerce_analytics_coupons_query_args', array( $this, 'update_query_args' ) );
		add_filter( 'woocommerce_analytics_coupons_stats_query_args', array( $this, 'update_query_args' ) );
	}

	/**
	 * Add the coupon campaign param to the query args.
	 *
	 * @param array $args The Query parameters.
	 * @return array
	 */
	public function update_query_args( array $args ) {
		$args['coupon_campaign'] = $this->get_current_coupon_campaign_id();
		return $args;
	}

	/**
	 * Check if current page is a WooCommerce Admin report page.
	 *
	 * @return bool
	 */
	public function is_wc_admin_page() : bool {
		return is_admin() && class_exists( PageController::class ) && PageController::is_admin_page();
	}

	/**
	 * Load coupon campaign settings data if on a WooCommerce Admin report page.
	 */
	public function load_coupon_campaign_settings(): void {
		if ( ! $this->is_wc_admin_page() ) {
			return;
		}

		// Load selector script.
		add_action( 'admin_enqueue_scripts', array( $this, 'add_coupon_campaign_settings' ) );
	}

	/**
	 * Add coupon campaign data to WooCommerce Admin JS settings.
	 */
	public function add_coupon_campaign_settings(): void {
		$coupon_campaign_terms = get_terms(
			array(
				'taxonomy' => 'coupon_campaign',
				'fields'   => 'id=>name',
			)
		);

		if ( is_wp_error( $coupon_campaign_terms ) || empty( $coupon_campaign_terms ) ) {
			return;
		}

		$coupon_campaigns = array(
			array(
				'label' => esc_html__( 'All Coupon Campaigns', 'wc_coupon_campaigns' ),
				'value' => 'all',
			),
		);

		foreach ( $coupon_campaign_terms as $key => $coupon_campaign_term ) {
			$coupon_campaigns[] = array(
				'label' => $coupon_campaign_term,
				'value' => (string) $key,
			);
		}

		/**
		 * Data registery to populate the wcSettings global.
		 *
		 * @var AssetDataRegistry
		 */
		$data_registry = Package::container()->get( AssetDataRegistry::class );
		$data_registry->add( 'couponCampaigns', $coupon_campaigns );
	}

	/**
	 * Get current coupon campaign query string data.
	 * If coupon campaign id is not set it returns 0.
	 *
	 * @return int
	 */
	public function get_current_coupon_campaign_id() {
		$coupon_campaign_id = 0;

		if ( current_user_can( 'manage_woocommerce' ) ) {
			$coupon_campaign_id = isset( $_GET['coupon_campaign'] ) ? intval( sanitize_text_field( wp_unslash( $_GET['coupon_campaign'] ) ) ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}

		return $coupon_campaign_id;
	}

	/**
	 * Add JOIN coupon campaign information on WC admin queries.
	 *
	 * @param array $clauses The array of JOIN clauses.
	 * @return array
	 */
	public function add_join_subquery( array $clauses ): array {
		global $wpdb;

		$coupon_campaign = $this->get_current_coupon_campaign_id();
		if ( $coupon_campaign > 0 ) {
			$clauses[] = "INNER JOIN {$wpdb->term_relationships} cc_tr ON {$wpdb->prefix}wc_order_coupon_lookup.coupon_id = cc_tr.object_id";
			$clauses[] = "INNER JOIN {$wpdb->term_taxonomy} cc_tt ON cc_tt.taxonomy='coupon_campaign' AND cc_tr.term_taxonomy_id = cc_tt.term_taxonomy_id";
		}

		return $clauses;
	}

	/**
	 * Add WHERE coupon campaign filter on WC admin queries.
	 *
	 * @param array $clauses The array of WHERE clauses.
	 * @return array
	 */
	public function add_where_subquery( array $clauses ): array {
		$coupon_campaign = $this->get_current_coupon_campaign_id();
		if ( $coupon_campaign > 0 ) {
			$clauses[] = "AND cc_tt.term_id = {$coupon_campaign}";
		}

		return $clauses;
	}
}
