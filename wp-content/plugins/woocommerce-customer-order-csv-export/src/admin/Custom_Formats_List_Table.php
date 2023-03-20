<?php
/**
 * WooCommerce Customer/Order/Coupon Export
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Customer/Order/Coupon Export to newer
 * versions in the future. If you wish to customize WooCommerce Customer/Order/Coupon Export for your
 * needs please refer to http://docs.woocommerce.com/document/ordercustomer-csv-exporter/
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2015-2023, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\CSV_Export\Admin;

use SkyVerge\WooCommerce\CSV_Export\Automations\Automation_Factory;
use SkyVerge\WooCommerce\CSV_Export\Export_Formats;
use WC_Customer_Order_CSV_Export;

defined( 'ABSPATH' ) or exit;

if ( ! class_exists( '\WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Customer/Order CSV Export Custom Formats List Table
 *
 * Lists custom export formats
 *
 * @since 4.7.0
 */
class Custom_Formats_List_Table extends \WP_List_Table {


	/** @var string the export type, `orders`, `customers` or `coupons` */
	private $export_type;


	/**
	 * Setups list table.
	 *
	 * @since 4.7.0
	 */
	public function __construct() {

		parent::__construct( [
			'singular' => 'custom format',
			'plural'   => 'custom formats',
			'ajax'     => false
		] );
	}


	/**
	 * Sets the export type.
	 *
	 * @since 4.7.0
	 *
	 * @param string $export_type
	 */
	public function set_export_type( $export_type ) {

		$this->export_type = $export_type;
	}


	/**
	 * Outputs the filter dropdowns and button.
	 *
	 * @since 5.1.1
	 *
	 * @param string $which the tablenav this is for -- 'top' or 'bottom'
	 */
	protected function extra_tablenav( $which ) {

		if ( 'top' !== $which ) {
			return;
		}

		?>
		<div class="alignleft actions">

			<label class="screen-reader-text" for="filter-by-export-type"><?php esc_html_e( 'Filter by export type', 'woocommerce-customer-order-csv-export' ); ?></label>
			<select id="filter-by-export-type" name="export_type">
				<option value=""><?php esc_html_e( 'Show all types', 'woocommerce-customer-order-csv-export' ); ?></option>

				<?php foreach ( wc_customer_order_csv_export()->get_export_types() as $type => $label ) : ?>

					<option value="<?php echo esc_attr( $type ); ?>" <?php selected( $type, $this->export_type ); ?>>
						<?php echo esc_html( $label ); ?>
					</option>

				<?php endforeach; ?>
			</select>

			<?php submit_button( _x( 'Filter', 'button text', 'woocommerce-customer-order-csv-export' ), '', 'filter_action', false ); ?>

		</div>
		<?php
	}


	/**
	 * Returns column titles.
	 *
	 * @since 4.7.0
	 *
	 * @return array
	 */
	public function get_columns() {

		$columns = [
			'name'           => esc_html__( 'Format name', 'woocommerce-customer-order-csv-export' ),
			'export_type'    => esc_html__( 'Type', 'woocommerce-customer-order-csv-export' ),
			'output_type'    => esc_html__( 'Output Type', 'woocommerce-customer-order-csv-export' ),
			'row_type'       => esc_html__( 'Rows represent', 'woocommerce-customer-order-csv-export' ),
			'items_format'   => esc_html__( 'Cell format', 'woocommerce-customer-order-csv-export' ),
			'delimiter'      => esc_html__( 'Delimiter', 'woocommerce-customer-order-csv-export' ),
			'indent'         => esc_html__( 'Indent Output', 'woocommerce-customer-order-csv-export' ),
			'format_actions' => esc_html__( 'Actions', 'woocommerce-customer-order-csv-export' ),
		];

		/**
		 * Filters the columns in the custom formats list table.
		 *
		 * @since 4.7.0
		 *
		 * @param array $columns the custom formats list columns
		 */
		return apply_filters( 'wc_customer_order_export_admin_export_custom_formats_list_columns', $columns );
	}


	/**
	 * Gets column content.
	 *
	 * @since 4.7.0
	 *
	 * @param Export_Formats\Export_Format_Definition $custom_format custom format definition
	 * @param string $column_name the column name
	 * @return string the column content
	 */
	public function column_default( $custom_format, $column_name ) {

		$content = null;

		switch ( $column_name ) {

			case 'name':

				$edit_url = wp_nonce_url( add_query_arg( [
					'format_action' => Admin_Custom_Formats::ACTION_EDIT,
					'format_key'    => $custom_format->get_key(),
					'export_type'   => $custom_format->get_export_type(),
				] ) );

				$content = '<a href="' . esc_url( $edit_url ) . '">' . esc_html( $custom_format->get_name() ) . '</a>';

			break;

			case 'export_type':

				$type_options = wc_customer_order_csv_export()->get_export_types();

				if ( $custom_format->get_export_type() ) {

					$type = ! empty( $type_options[ $custom_format->get_export_type() ] ) ? $type_options[ $custom_format->get_export_type() ] : $custom_format->get_export_type();

					$content = esc_html( $type );
				}

			break;

			case 'output_type':

				$type_options = wc_customer_order_csv_export()->get_output_types();

				if ( $custom_format->get_output_type() ) {

					$type = ! empty( $type_options[ $custom_format->get_output_type() ] ) ? $type_options[ $custom_format->get_output_type() ] : $custom_format->get_output_type();

					$content = esc_html( $type );
				}

			break;

			case 'row_type':

				$row_type_options = wc_customer_order_csv_export()->get_admin_instance()->get_custom_formats_admin_instance()->get_row_type_options();

				// only CSV order formats have a row type
				if ( $custom_format instanceof Export_Formats\CSV\Orders_Export_Format_Definition && $custom_format->get_row_type() ) {

					$row_type = ! empty( $row_type_options[ $custom_format->get_row_type() ] ) ? $row_type_options[ $custom_format->get_row_type() ] : $custom_format->get_row_type();

					$content = esc_html( $row_type );
				}

			break;

			case 'items_format':

				$items_format_options = wc_customer_order_csv_export()->get_admin_instance()->get_custom_formats_admin_instance()->get_items_format_options();

				// only CSV order formats have an items format
				if ( $custom_format instanceof Export_Formats\CSV\Orders_Export_Format_Definition && $custom_format->get_items_format() ) {

					$items_format = ! empty( $items_format_options[ $custom_format->get_items_format() ] ) ? $items_format_options[ $custom_format->get_items_format() ] : $custom_format->get_items_format();

					$content = esc_html( $items_format );
				}

			break;

			case 'delimiter':

				$delimiter_options = wc_customer_order_csv_export()->get_admin_instance()->get_custom_formats_admin_instance()->get_delimiter_options();

				// only CSV formats have a delimiter
				if ( $custom_format instanceof Export_Formats\CSV\CSV_Export_Format_Definition && $custom_format->get_delimiter() ) {

					$delimiter = ! empty( $delimiter_options[ $custom_format->get_delimiter() ] ) ? $delimiter_options[ $custom_format->get_delimiter() ] : $custom_format->get_delimiter();

					$content = esc_html( $delimiter );
				}

			break;

			case 'indent':

				// only XML formats have an indent setting
				if ( $custom_format instanceof Export_Formats\XML\XML_Export_Format_Definition ) {

					$content = sprintf(
						'<mark class="%1$s">%2$s</mark>',
						$custom_format->get_indent() ? 'indent_enabled' : 'indent_disabled',
						$custom_format->get_indent() ? esc_html__( 'Indent XML output', 'woocommerce-customer-order-csv-export' ) : esc_html__( 'Do not indent XML output', 'woocommerce-customer-order-csv-export' )
					);
				}

			break;

			default:

				/**
				 * Allows actors adding custom columns to include their own column data.
				 *
				 * @since 4.7.0
				 *
				 * @param string $column_name the column name
				 * @param Export_Formats\Export_Format_Definition $custom_format the custom format
				 *
				 * @param string $content the column content
				 */
				$content = apply_filters( 'wc_customer_order_export_admin_custom_formats_list_custom_column', '', $column_name, $custom_format );
		}

		if ( null === $content ) {
			$content = esc_html__( '-', 'woocommerce-customer-order-csv-export' );
		}

		return $content;
	}


	/**
	 * Outputs actions column content for the given format.
	 *
	 * @since 4.7.0
	 *
	 * @param Export_Formats\Export_Format_Definition $custom_format custom format definition
	 */
	public function column_format_actions( $custom_format ) {

		?>
		<p>
			<?php
			$actions = [];

			$delete_url = wp_nonce_url( add_query_arg( [
				'format_action' => Admin_Custom_Formats::ACTION_DELETE,
				'format_key'    => $custom_format->get_key(),
				'export_type'   => $custom_format->get_export_type(),
			] ), 'wc_customer_order_coupon_export_delete_custom_format' );

			$actions[ Admin_Custom_Formats::ACTION_DELETE ] = [
				'url'    => $delete_url,
				'name'   => esc_html__( 'Delete', 'woocommerce-customer-order-csv-export' ),
				'action' => Admin_Custom_Formats::ACTION_DELETE,
			];

			$automations = Automation_Factory::get_automations( [ 'format_key' => $custom_format->get_key() ] );

			// check if the custom format is used by any of the existing automations
			if ( ! empty( $automations ) ) {

				$names = [];

				// get the name of the first five automations that are currently using this custom format
				foreach ( array_slice( $automations, 0, 5 ) as $automation ) {
					$names[] = $automation->get_name();
				}

				$tip = sprintf(
					/* translators: Placeholders: %1$s - a comma separated list of automated exports names in double quotes, %2$s - the name of an automated export in double quotes */
					_n( 'This custom format is selected in the %2$s automated export and cannot be deleted. Please switch the export format setting for that automated export to another format first.', 'This custom format is selected in the %1$s and %2$s automated exports and cannot be deleted. Please switch the export formats settings for those automated exports to another format first.', count( $automations ), 'woocommerce-customer-order-csv-export' ),
					esc_html( '"' . implode( '", "', array_slice( $names, 0, -1 ) ) . '"' ),
					esc_html( '"' . end( $names ) . '"' )
				);

				$actions[ Admin_Custom_Formats::ACTION_DELETE ]['class'] = [ 'disabled' ];
				$actions[ Admin_Custom_Formats::ACTION_DELETE ]['tip']   = $tip;
			}

			$edit_url = wp_nonce_url( add_query_arg( [
				'format_action' => Admin_Custom_Formats::ACTION_EDIT,
				'format_key'    => $custom_format->get_key(),
				'export_type'   => $custom_format->get_export_type(),
			] ) );

			$actions['edit'] = [
				'url'    => $edit_url,
				'name'   => esc_html__( 'Manage', 'woocommerce-customer-order-csv-export' ),
				'action' => Admin_Custom_Formats::ACTION_EDIT,
				'class'  => 'button-primary',
			];

			/**
			 * Allows actors to change the available actions for a custom format in Custom Formats List
			 *
			 * @since 4.7.0
			 *
			 * @param array $actions
			 * @param Export_Formats\Export_Format_Definition $custom_format
			 */
			$actions = apply_filters( 'wc_customer_order_export_admin_custom_format_actions', $actions, $custom_format );

			foreach ( $actions as $action ) {

				$classes   = isset( $action['class'] ) ? (array) $action['class'] : [];
				$classes[] = $action['action'];

				$attributes = [];

				// if the action has a tooltip set
				if ( isset( $action['tip'] ) && $action['tip'] ) {
					$classes[]           = 'tip';
					$attributes['title'] = $action['tip'];
				}

				// build the attributes
				foreach ( $attributes as $attribute => $value ) {
					$attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $value ) . '"';
					unset( $attributes[ $attribute ] );
				}

				// print the button
				printf(
					in_array( 'disabled', $classes, true ) ? '<a class="button %2$s" %3$s>%4$s</a>' : '<a href="%1$s" class="button %2$s" %3$s>%4$s</a>',
					esc_url( $action['url'] ),
					implode( ' ', array_map( 'sanitize_html_class', $classes ) ),
					implode( ' ', $attributes ),
					esc_html( $action['name'] )
				);
				// print spacing
				echo '<span>&nbsp;</span>';
			}
			?>
		</p>
		<?php
	}


	/**
	 * Prepares custom formats for display.
	 *
	 * @since 4.7.0
	 */
	public function prepare_items() {

		// set column headers manually, see https://codex.wordpress.org/Class_Reference/WP_List_Table#Extended_Properties
		$columns               = $this->get_columns();
		$hidden                = [];
		$sortable              = [];
		$this->_column_headers = [ $columns, $hidden, $sortable ];

		$formats = wc_customer_order_csv_export()->get_formats_instance();

		if ( $this->export_type ) {

			$format_definitions = $formats->get_custom_format_definitions( $this->export_type );

		} else {

			$format_definitions = [];

			foreach ( wc_customer_order_csv_export()->get_export_types() as $type ) {
				$format_definitions[] = $formats->get_custom_format_definitions( $type );
			}

			$format_definitions = array_merge( [], ...$format_definitions );
		}

		$this->set_pagination_args( [
			'total_items' => count( $format_definitions ),
			'per_page'    => $this->get_items_per_page( 'wc_customer_order_export_admin_custom_formats_per_page' ),
		] );

		if ( $page_number = $this->get_pagenum() ) {

			$per_page = $this->get_pagination_arg( 'per_page' );

			$format_definitions = array_splice( $format_definitions, $per_page * ( $page_number - 1 ), $per_page );
		}

		$this->items = $format_definitions;
	}


	/**
	 * Returns the HTML to display when there are no custom formats.
	 * @see WP_List_Table::no_items()
	 *
	 * @since 4.7.0
	 */
	public function no_items() {
		?>
		<p><?php esc_html_e( 'Custom formats will appear here.', 'woocommerce-customer-order-csv-export' ); ?></p>
		<?php
	}


}
