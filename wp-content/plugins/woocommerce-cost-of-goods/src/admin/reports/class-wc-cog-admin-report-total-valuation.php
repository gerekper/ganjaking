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
 * Cost of Goods Total Valuation Admin Report Class
 *
 * Handles generating and rendering the Total Valuation report
 *
 * @since 2.1.0
 */
class WC_COG_Admin_Report_Total_Valuation {


	/**
	 * Render the totals
	 *
	 * @since 2.1.0
	 */
	public function output_report() {

		$valuation = array( 'at_cost' => 0, 'at_retail' => 0 );

		// loader image
		$loader_image = wc_cog()->get_framework_assets_url() . '/images/ajax-loader.gif';

		wp_enqueue_style( 'wc-cog-valuation', wc_cog()->get_plugin_url() . '/assets/css/admin/wc-cog-valuation.min.css', array(), \WC_COG::VERSION );
		?>
		<div id="poststuff" class="woocommerce-reports-wide wc-cogs-total-valuation">
			<div class="wrapper wc-cog-cost">
				<span class="title"><?php esc_html_e( 'at cost', 'woocommerce-cost-of-goods' ); ?></span>
				<h3 class="amount"><?php echo wc_price( $valuation['at_cost'] ); ?></h3>
				<div class="loader">
					<img src="<?php echo esc_url( $loader_image ); ?>" />
				</div>
			</div>
			<div class="wrapper wc-cog-retail">
				<span class="title"><?php esc_html_e( 'at retail', 'woocommerce-cost-of-goods' ); ?></span>
				<h3 class="amount"><?php echo wc_price( $valuation['at_retail'] ); ?></h3>
				<div class="loader">
					<img src="<?php echo esc_url( $loader_image ); ?>" />
				</div>
			</div>
		</div>
		<?php
		if ( 'yes' === get_option( 'woocommerce_manage_stock' ) ) :

			?>
			<section class="wc-cog-progressbar-section">
				<progress class="wc-cog-progress" max="100" value="0"></progress>
			</section>
			<?php
			wp_enqueue_script( 'wc-cog-total-valuation', wc_cog()->get_plugin_url() . '/assets/js/admin/wc-cog-total-valuation.min.js', array( 'jquery' ), \WC_COG::VERSION );
			wp_localize_script( 'wc-cog-total-valuation', 'wc_cog_total_valuation', array(
				'total_valuation_nonce' => wp_create_nonce( 'wc-cog-total-valuation' ),
			) );

		endif;
	}


}
