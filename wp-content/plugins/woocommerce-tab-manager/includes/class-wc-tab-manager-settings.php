<?php
/**
 * WooCommerce Tab Manager
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Tab Manager to newer
 * versions in the future. If you wish to customize WooCommerce Tab Manager for your
 * needs please refer to http://docs.woocommerce.com/document/tab-manager/
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2012-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * Manages and displays custom WooCommerce settings.
 *
 * @since 1.4.0
 */
class WC_Tab_Manager_Settings {


	/**
	 * Constructor function.
	 *
	 * @since 1.4.0
	 */
	public function __construct() {

		// Add our settings.
		add_filter( 'woocommerce_get_sections_products', array( $this, 'add_sections' ) );
		add_filter( 'woocommerce_get_settings_products', array( $this, 'add_settings' ), 10, 2 );

		// Create a custom field for the AJAX batch update button.
		add_action( 'woocommerce_admin_field_batch_update_button', array( $this, 'batch_update_button' ) );
	}


	/**
	 * Adds our custom settings section.
	 *
	 * @since  1.4.0
	 * @param  array $sections The default sections list.
	 * @return array An updated sections list that includes our custom section.
	 */
	public function add_sections( $sections ) {

		// Add our custom sections.
		$new_sections = array(
			'wc_tab_manager' => _x( 'Tab Manager', 'Custom WooCommerce settings section', 'woocommerce-tab-manager' ),
		);

		return wp_parse_args( $new_sections, $sections );
	}


	/**
	 * Adds any inputs that should be displayed in the settings section.
	 *
	 * @since 1.4.0
	 * @param  array  $settings        The default settings list.
	 * @param  string $current_section The section that's currently being processed.
	 * @return array  An updated settings list that includes our custom settings.
	 */
	public function add_settings( $settings, $current_section ) {

		// Bail if we're in a different section.
		if ( 'wc_tab_manager' !== $current_section ) {
			return $settings;
		}

		// Add our custom settings.
		$_settings = array(
			array(
				'title' => __( 'Search Settings', 'woocommerce-tab-manager' ),
				'type'  => 'title',
				'id'    => 'search_settings',
			),
			array(
				'type' => 'batch_update_button',
			),
			array(
				'title'   => __( 'Include product tab content in search', 'woocommerce-tab-manager' ),
				'type'    => 'checkbox',
				'id'      => 'wc_tab_manager_enable_search',
				'default' => 'yes',
				'desc'    => __( 'Enable to include custom tab content (and global tab content if enabled) in site search.', 'woocommerce-tab-manager' ),
			),
			array(
				'type' => 'sectionend',
				'id'   => 'search_settings',
			),
		);

		return $_settings;
	}


	/**
	 * Renders the custom batch product update button.
	 *
	 * @since 1.4.0
	 */
	public function batch_update_button() {

		?>
		<tr valign="top" id="wc-tab-manager-batch-update-settings">
			<th scope="row" class="titledesc">
				<?php esc_html_e( 'Update Products & Tabs', 'woocommerce-tab-manager' ); ?>
			</th>
			<td class="forminp forminp-batch-update-button">
				<div class="clearfix">
					<button id="batch_update_button"
						class="button-primary batch-update-button"
						data-action="wc_tab_manager_batch_update_products">
						<?php esc_html_e( 'Update', 'woocommerce-tab-manager' ); ?>
					</button>
					<span class="ajax-spinner blockUI blockOverlay"></span>
				</div>
				<p class="description">
					<?php
					esc_html_e(
						'This update will allow your product tab content to show up in your site’s search results, making it easier for customers to find products whose tabs contain the search query.',
						'woocommerce-tab-manager'
					);
					?>
				</p>
			</td>
		</tr>
		<?php
	}


}
