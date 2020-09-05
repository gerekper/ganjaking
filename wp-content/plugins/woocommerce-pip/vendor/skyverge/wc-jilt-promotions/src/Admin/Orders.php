<?php
/**
 * Jilt for WooCommerce Promotions
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Jilt_Promotions\Admin;

use SkyVerge\WooCommerce\Jilt_Promotions\Handlers\Installation;
use SkyVerge\WooCommerce\Jilt_Promotions\Handlers\Prompt;
use SkyVerge\WooCommerce\Jilt_Promotions\Messages;
use SkyVerge\WooCommerce\Jilt_Promotions\Package;

defined( 'ABSPATH' ) or exit;

/**
 * Handles the modal shown when merchants select the Abandoned Carts filter view on the Orders page.
 *
 * @since 1.1.0
 */
final class Orders extends Prompt {


	/** @var string the id associated with the message */
	private $abandoned_carts_filter_message_id = 'orders-abandoned-carts-filter';


	/**
	 * Callback for the views_edit-shop_order filter.
	 *
	 * @internal
	 *
	 * @since 1.1.0
	 *
	 * @param array $views
	 */
	public function add_abandoned_carts_view( $views ) {

		$views[ $this->abandoned_carts_filter_message_id ] = sprintf( '<a href="#">%s</a>', __( 'Abandoned Carts', 'sv-wc-jilt-promotions' ) );

		return $views;
	}


	/**
	 * Adds the necessary action & filter hooks.
	 *
	 * @since 1.1.0
	 */
	protected function add_prompt_hooks() {

		add_action( 'admin_init', [ $this, 'admin_init' ] );
	}


	/**
	 * Adds the render_recover_carts_modal filters and hooks in the init stage since WooCommerce uses wc_get_order_types()
	 * to generate the reports and they're not ready before that.
	 *
	 * @internal
	 *
	 * @since 1.1.0
	 */
	public function admin_init() {

		$is_message_enabled = Messages::is_message_enabled( $this->abandoned_carts_filter_message_id );
		$orders_count       = $this->get_orders_count();

		if( $orders_count > 10 && ! $is_message_enabled ) {

			add_filter( 'views_edit-shop_order', [ $this, 'add_abandoned_carts_view' ] );

			add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );

			add_action( 'admin_footer', [ $this, 'render_recover_carts_modal' ] );
		}
	}


	/**
	 * Enqueues the assets.
	 *
	 * @internal
	 *
	 * @since 1.1.0
	 */
	public function enqueue_assets() {

		wp_enqueue_style( Installation::INSTALL_SCRIPT_HANDLE );
		wp_enqueue_script( 'sv-wc-jilt-prompt-orders', Package::get_assets_url() . '/js/admin/orders.min.js', [ Installation::INSTALL_SCRIPT_HANDLE ], Package::VERSION, true );

		wp_localize_script( 'sv-wc-jilt-prompt-orders', 'sv_wc_jilt_prompt_orders', [
			'abandoned_carts_id' => $this->abandoned_carts_filter_message_id,
		] );
	}


	/**
	 * Returns the estimated number of abandoned carts for the last 30 days.
	 *
	 * @since 1.1.0
	 *
	 * @retun int
	 */
	private function get_abandoned_carts_count() {

		$orders_count = $this->get_orders_count();

		// 70% of carts are abandoned
		$carts_abandoned_rate = 0.7;

		// 15% of carts are recovered
		$carts_recovered_rate = 0.15;

		return ceil( ( $orders_count / ( 1 - $carts_abandoned_rate ) ) * $carts_abandoned_rate * $carts_recovered_rate );
	}


	/**
	 * Gets the connection redirect args to attribute the plugin installation to this prompt.
	 *
	 * @since 1.1.0
	 */
	protected function get_connection_redirect_args() {

		$redirect_args = [];

		if( Installation::get_jilt_installed_from() === $this->abandoned_carts_filter_message_id ) {
			$redirect_args['utm_term'] = $this->abandoned_carts_filter_message_id;
		}

		return $redirect_args;
	}


	/**
	 * Returns the number of orders placed in the last 30 days.
	 *
	 * @since 1.1.0
	 *
	 * @return int
	 */
	private function get_orders_count() {

		$sales_data = $this->get_sales_data();

		return $sales_data['number_of_orders'];
	}


	/**
	 * Returns the gross sales for the last 30 days.
	 *
	 * @since 1.1.0
	 *
	 * @return float
	 */
	private function get_orders_revenue() {

		$sales_data = $this->get_sales_data();

		return $sales_data['gross_sales'];
	}


	/**
	 * Returns the estimated recovered revenue from abandoned carts fort the last 30 days.
	 *
	 * @since 1.1.0
	 *
	 * @return float
	 */
	private function get_recovered_revenue() {

		$abandoned_carts_count = $this->get_abandoned_carts_count();
		$orders_revenue        = $this->get_orders_revenue();
		$orders_count          = $this->get_orders_count();

		return ( $orders_revenue / $orders_count ) * $abandoned_carts_count;
	}


	/**
	 * Returns the number of orders placed in the last 30 days and the gross sales for that period.
	 *
	 * @since 1.1.0
	 *
	 * @return array
	 */
	private function get_sales_data() {

		$transient_key = sprintf( '%s_sales_data', $this->abandoned_carts_filter_message_id );

		$sales_data = get_transient( $transient_key );

		if( ! $sales_data ) {

			$sales_data = $this->get_woocommerce_report_data();

			set_transient( $transient_key, $sales_data, 24 * 60 * 60 );
		}

		return $sales_data;
	}


	/**
	 * Returns the result of a queried sales report from WooCommerce.
	 *
	 * @since 1.1.0
	 *
	 * @see https://github.com/woocommerce/woocommerce/blob/9eae43c0d4dde8916608c45b7bf44b893bca1107/includes/admin/class-wc-admin-dashboard.php#L92-L102
	 *
	 * @return array
	 */
	private function get_woocommerce_report_data() {

		$report_data = [
			'number_of_orders' => 0,
			'gross_sales'      => 0,
		];

		$woocommerce_plugin_path = WC()->plugin_path();

		$admin_report_file = "{$woocommerce_plugin_path}/includes/admin/reports/class-wc-admin-report.php";
		$report_file       = "{$woocommerce_plugin_path}/includes/admin/reports/class-wc-report-sales-by-date.php";

		if ( is_readable( $admin_report_file ) && is_readable( $report_file ) ) {

			include_once $admin_report_file;
			include_once $report_file;

			$sales_by_date                 = new \WC_Report_Sales_By_Date();
			$sales_by_date->start_date     = strtotime( '-30 days' );
			$sales_by_date->end_date       = strtotime( 'now' );
			$sales_by_date->chart_groupby  = 'day';
			$sales_by_date->group_by_query = 'YEAR(posts.post_date), MONTH(posts.post_date), DAY(posts.post_date)';

			$query_result = $sales_by_date->get_report_data();

			$report_data['number_of_orders'] = $query_result->total_orders;
			$report_data['gross_sales']      = $query_result->total_sales;
		}

		return $report_data;
	}


	/**
	 * Retrieves the abandoned carts count and the recovered revenue to render the recover carts modal.
	 *
	 * @since 1.1.0
	 */
	public function render_recover_carts_modal() {

		$abandoned_carts_count = $this->get_abandoned_carts_count();
		$recovered_revenue     = $this->get_recovered_revenue();

		/* translators: Placeholders: %1$s - <strong> tag, %2$s - abandoned cart estimate, %3$s - </strong> tag, %4$s - <strong> tag, %5$s - recovered revenue estimate, %6$s - </strong> tag */
		$modal_message = sprintf( esc_html__( 'Stores like yours recover an average of %1$s%2$s%3$s carts for %4$s%5$s recovered revenue%6$s per month. Do you want to install Jilt for WooCommerce to start recovering carts? You can then connect to Jilt with one click!', 'sv-wc-jilt-promotions' ),
			'<strong>',
			$abandoned_carts_count,
			'</strong>',
			'<strong>',
			wc_price( $recovered_revenue ),
			'</strong>'
		);

		?>
		<script type="text/template" id="tmpl-sv-wc-jilt-promotions-<?php esc_attr_e( $this->abandoned_carts_filter_message_id ); ?>-modal">
			<div id="sv-wc-jilt-install-modal" class="wc-backbone-modal">
				<div class="wc-backbone-modal-content">
					<section class="wc-backbone-modal-main" role="main">
						<header class="wc-backbone-modal-header">
							<h1><?php esc_html_e( 'Start recovering abandoned carts with Jilt!', 'sv-wc-jilt-promotions' ); ?></h1>
							<button class="modal-close modal-close-link dashicons dashicons-no-alt">
								<span class="screen-reader-text"><?php esc_html_e( 'Close modal panel', 'sv-wc-jilt-promotions' ); ?></span>
							</button>
						</header>
						<article><?php echo $modal_message; ?></article>
						<footer>
							<div class="sv-wc-jilt-prompt-actions inner">
								<a class="sv-wc-jilt-prompt-action" href="https://www.skyverge.com/go/abandoned-cart-recovery" target="_blank"><?php esc_html_e( 'Learn more', 'sv-wc-jilt-promotions' ); ?></a>
								<button id="sv-wc-jilt-install-button-install" class="sv-wc-jilt-prompt-action button button-large button-primary"><?php esc_html_e( 'I want to recover carts', 'sv-wc-jilt-promotions' ); ?></button>
							</div>
						</footer>
					</section>
				</div>
			</div>
			<div class="wc-backbone-modal-backdrop modal-close"></div>
		</script>
		<?php
	}


}
