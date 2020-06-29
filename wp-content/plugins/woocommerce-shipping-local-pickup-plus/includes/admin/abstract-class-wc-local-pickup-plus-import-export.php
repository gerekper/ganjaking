<?php
/**
 * WooCommerce Local Pickup Plus
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Local Pickup Plus to newer
 * versions in the future. If you wish to customize WooCommerce Local Pickup Plus for your
 * needs please refer to http://docs.woocommerce.com/document/local-pickup-plus/
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2012-2020, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * Pickup Locations abstract class for importing and exporting.
 *
 * @since 2.0.0
 */
abstract class WC_Local_Pickup_Plus_Import_Export {


	/** @var string either 'import' or 'export' identifier */
	protected $action_id = '';

	/** @var string action label */
	protected $action_label = '';

	/** @var string admin page title */
	protected $admin_page_title = '';

	/** @var string the delimiter type option name used in settings */
	protected $delimiter_option = '';

	/** @var string pickup locations parent admin URL */
	protected $parent_url = '';


	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		$this->parent_url = admin_url( 'admin.php?page=wc-settings&tab=shipping&section=pickup_locations' );

		add_action( 'admin_init', array( $this, 'admin_init' ) );
	}


	/**
	 * Init admin UI hooks.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 */
	public function admin_init() {

		// set the admin page title
		add_filter( 'admin_title', array( $this, 'set_admin_page_title' ) );

		// render WooCommerce Settings tabs while in the Pickup Locations edit screens
		add_action( 'all_admin_notices', array( $this, 'output_woocommerce_settings_tabs_html' ), 5 );

		// render the current page HTML
		add_action( 'wc_local_pickup_plus_render_import_export_page', array( $this, 'output_page_html' ) );
	}


	/**
	 * Set the admin page title.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param string $admin_title the page title
	 * @return string
	 */
	public function set_admin_page_title( $admin_title ) {
		global $current_screen;

		if ( isset( $current_screen->id ) ) {
			switch ( $current_screen->id ) {
				case 'admin_page_wc_local_pickup_plus_export' :
				case 'admin_page_wc_local_pickup_plus_import' :
					// trim `"admin_page_wc_local_pickup_plus_"` from `$current_screen->id`.
					return $this->action_id === substr( $current_screen->id, 32, 6 ) ? $this->admin_page_title . $admin_title : $admin_title;
				default :
					return $admin_title;
			}
		}

		return $admin_title;
	}


	/**
	 * Get default CSV headers expected by import and export processes.
	 *
	 * @see \WC_Local_Pickup_Plus_Export::get_csv_headers()
	 * @see \WC_Local_Pickup_Plus_Import::get_csv_headers()
	 *
	 * @since 2.0.0
	 *
	 * @return array associative array
	 */
	protected function get_csv_headers() {

		// these will be filtered in the respective import/export classes:
		return array(
			'id'                   => 'id',
			'status'               => 'status',
			'name'                 => 'name',
			'country'              => 'country',
			'postcode'             => 'postcode',
			'state'                => 'state',
			'city'                 => 'city',
			'address_1'            => 'address_1',
			'address_2'            => 'address_2',
			'phone'                => 'phone',
			'latitude'             => 'latitude',
			'longitude'            => 'longitude',
			'products'             => 'products',
			'product_categories'   => 'product_categories',
			'price_adjustment'     => 'price_adjustment',
			'business_hours'       => 'business_hours',
			'public_holidays'      => 'public_holidays',
			'pickup_lead_time'     => 'pickup_lead_time',
			'pickup_deadline'      => 'pickup_deadline',
			'pickup_notifications' => 'pickup_notifications',
			'description'          => 'description',
		);
	}


	/**
	 * Get fields delimiter for CSV import or export file.
	 *
	 * @since 2.0.0
	 *
	 * @return string tab space or comma (default)
	 */
	protected function get_fields_delimiter() {

		// get the delimiter from form submission, defaults to comma otherwise
		$delimiter = ! empty( $this->delimiter_field_name ) && isset( $_POST[ $this->delimiter_field_name ] ) ? $_POST[ $this->delimiter_field_name ] : 'comma';

		switch ( $delimiter ) {
			case 'tab' :
				return "\t";
			case 'comma' :
			default :
				return ',';
		}
	}


	/**
	 * Get the CSV enclosure.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	protected function get_enclosure() {

		/**
		 * Filter the CSV enclosure.
		 *
		 * @since 2.0.0
		 *
		 * @param string $enclosure default double quote `"`
		 * @param \WC_Local_Pickup_Plus_Export $export_instance instance of the export class
		 */
		return apply_filters( 'wc_local_pickup_plus_csv_export_pickup_locations_enclosure', '"', $this );
	}


	/**
	 * Escape sensitive characters with a single quote, to prevent CSV injections.
	 *
	 * @link http://www.contextis.com/resources/blog/comma-separated-vulnerabilities/
	 *
	 * @since 2.0.0
	 *
	 * @param string|mixed $value
	 * @return string|mixed
	 */
	protected function escape_value( $value ) {

		if ( is_string( $value ) ) {

			$first_char = isset( $value[0] ) ? $value[0] : '';

			if ( '' !== $first_char && in_array( $first_char, array( '=', '+', '-', '@' ), true ) ) {
				$value = "'{$value}";
			}
		}

		return $value;
	}


	/**
	 * Unescape a string that may have been escaped with slashes, a single quote or back tick.
	 *
	 * @since 2.0.0
	 *
	 * @param string|mixed $value
	 * @return string|mixed
	 */
	protected function unescape_value( $value ) {

		$first_char = is_string( $value ) && isset( $value[0] ) ? $value[0] : '';

		if ( '' !== $first_char && in_array( $first_char, array( "'", '`', "\'" ), true ) ) {
			$value = substr( $value, 1 );
		}

		return is_string( $value ) ? trim( stripslashes( $value ) ) : $value;
	}


	/**
	 * Render WooCommerce core settings tabs while in import/export pages.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 */
	public function output_woocommerce_settings_tabs_html() {

		if ( $this->action_id === wc_local_pickup_plus()->get_admin_instance()->is_import_export_page() ) {

			wc_local_pickup_plus()->get_admin_instance()->output_woocommerce_tabs_html();
		}
	}


	/**
	 * Get input fields.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @return array associative array with field data
	 */
	abstract protected function get_fields();


	/**
	 * Render the current page HTML.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 */
	public function output_page_html() {

		if ( $this->action_id === wc_local_pickup_plus()->get_admin_instance()->is_import_export_page() ) :

			?>
			<div id="wc-local-pickup-plus-<?php echo sanitize_html_class( $this->action_id ); ?>-pickup-locations" class="wc-local-pickup-plus">
				<form
					method="post"
					action="<?php echo admin_url( 'admin-post.php' ); ?>"
					enctype="multipart/form-data">

					<?php woocommerce_admin_fields( $this->get_fields() ); ?>
					<?php wp_nonce_field( 'wc_local_pickup_plus_csv_' . esc_attr( $this->action_id ) ); ?>

					<input
						type="hidden"
						name="action"
						value="<?php echo 'wc_local_pickup_plus_csv_' . esc_attr( $this->action_id ); ?>"
					/>

					<p class="submit">
						<input
							type="submit"
							class="button button-primary"
							value="<?php echo esc_html( $this->action_label ); ?>"
						/>
					</p>
				</form>
			</div>
			<?php

		endif;
	}


}
