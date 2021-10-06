<?php
/**
 * WooCommerce Cost of Goods
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Cost of Goods to newer
 * versions in the future. If you wish to customize WooCommerce Cost of Goods for your
 * needs please refer to http://docs.woocommerce.com/document/cost-of-goods/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2013-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_2 as Framework;

/**
 * Cost of Goods Abstract Admin Report Class
 *
 * Enhances the default WC Admin Report base class with some COG goodness
 *
 * @since 2.0.0
 */
abstract class WC_COG_Admin_Report extends \WC_Admin_Report {


	/** @var array chart colors */
	protected $chart_colors;

	/** @var stdClass|array for caching multiple calls to get_report_data() */
	protected $report_data;


	/**
	 * Render the report data, including legend and chart
	 *
	 * @since 2.0.0
	 */
	public function output_report() {

		$current_range = $this->get_current_range();

		if ( ! in_array( $current_range, array( 'custom', 'year', 'last_month', 'month', '7day' ), true ) ) {
			$current_range = '7day';
		}

		$this->calculate_current_range( $current_range );

		// used in view
		$ranges = array(
			'year'         => __( 'Year', 'woocommerce-cost-of-goods' ),
			'last_month'   => __( 'Last Month', 'woocommerce-cost-of-goods' ),
			'month'        => __( 'This Month', 'woocommerce-cost-of-goods' ),
			'7day'         => __( 'Last 7 Days', 'woocommerce-cost-of-goods' )
		);

		include( WC()->plugin_path() . '/includes/admin/views/html-report-by-date.php' );
	}


	/**
	 * Render the export CSV button
	 *
	 * @since 2.0.0
	 * @param array $args optional arguments for adjusting the exported CSV
	 */
	public function output_export_button( $args = array() ) {

		$defaults = array(
			'filename'       => sprintf(
				'%1$s-report-%2$s-%3$s.csv',
				strtolower( str_replace( array( '\WC_COG_Admin_Report_', 'WC_COG_Admin_Report_', '_' ), array( '', '', '-' ), get_class( $this ) ) ),
				$this->get_current_range(), date_i18n( 'Y-m-d', current_time( 'timestamp' ) )
			),
			'xaxes'          => __( 'Date', 'woocommerce-cost-of-goods' ),
			'exclude_series' => '',
			'groupby'        => $this->chart_groupby,
		);

		$args = wp_parse_args( $args, $defaults );

		?>
		<a
			href="#"
			download="<?php echo esc_attr( $args['filename'] ); ?>"
			class="export_csv"
			data-export="chart"
			data-xaxes="<?php echo esc_attr( $args['xaxes'] ); ?>"
			data-exclude_series="<?php echo esc_attr( $args['exclude_series'] ); ?>"
			data-groupby="<?php echo esc_attr( $args['groupby'] ); ?>"
		>
			<?php esc_html_e( 'Export CSV', 'woocommerce-cost-of-goods' ); ?>
		</a>
		<?php
	}


	/**
	 * Return the currently selected date range for the report
	 *
	 * @since 2.0.0
	 * @return string
	 */
	protected function get_current_range() {

		return ! empty( $_GET['range'] ) ? sanitize_text_field( $_GET['range'] ) : '7day';
	}


	/**
	 * Return true if fees should be excluded from net sales/profit calculations
	 *
	 * Note that taxes on fees are already included in the order tax amount.
	 *
	 * @since 2.0.0
	 * @return bool
	 */
	public function exclude_fees() {

		return 'yes' === get_option( 'wc_cog_profit_report_exclude_gateway_fees' );
	}


	/**
	 * Return true if taxes should be excluded from net sales/profit calculations
	 *
	 * @since 2.0.0
	 * @return bool
	 */
	public function exclude_taxes() {

		return 'yes' === get_option( 'wc_cog_profit_report_exclude_taxes' );
	}


	/**
	 * Return true if shipping should be excluded from net sales/profit calculations
	 *
	 * @since 2.0.0
	 * @return bool
	 */
	public function exclude_shipping() {

		return 'yes' === get_option( 'wc_cog_profit_report_exclude_shipping_costs' );
	}


	/**
	 * Helper to format an amount using wc_format_decimal() for both strings/floats
	 * and arrays
	 *
	 * @since 2.0.0
	 * @param string|float|array $amount
	 * @return array|string
	 */
	protected function format_decimal( $amount ) {
		if ( is_array( $amount ) ) {
			return array( $amount[0], wc_format_decimal( $amount[1], wc_get_price_decimals() ) );
		} else {
			return wc_format_decimal( $amount, wc_get_price_decimals() );
		}
	}


}
