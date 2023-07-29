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

namespace SkyVerge\WooCommerce\CSV_Export\Admin\Automations;

use SkyVerge\WooCommerce\CSV_Export\Admin\Automations;
use SkyVerge\WooCommerce\CSV_Export\Automations\Automation;
use SkyVerge\WooCommerce\CSV_Export\Automations\Automation_Factory;

defined( 'ABSPATH' ) or exit;

/**
 * Automations list table.
 *
 * @since 5.0.0
 */
class List_Table extends \WP_List_Table {


	/** @var string specific export type to list */
	private $export_type;

	/** @var array available valid export types */
	private $export_types;

	/** @var string specific output type to list */
	private $output_type;

	/** @var array available valid output types */
	private $output_types;

	/** @var string specific method type to list */
	private $method_type;

	/** @var array available valid method types */
	private $method_types;


	/**
	 * Setup list table.
	 *
	 * @since 5.0.0
	 */
	public function __construct() {

		parent::__construct( [
			'singular' => 'automation',
			'plural'   => 'automations',
			'ajax'     => false
		] );
	}


	/**
	 * Outputs the filter dropdowns and button.
	 *
	 * @since 5.0.0
	 *
	 * @param string $which the tablenav this is for -- 'top' or 'bottom'
	 */
	protected function extra_tablenav( $which ) {

		if ( 'top' === $which ) {

			?>
			<div class="alignleft actions">

				<label class="screen-reader-text" for="filter-by-export-type"><?php esc_html_e( 'Filter by export type', 'woocommerce-customer-order-csv-export' ); ?></label>
				<select id="filter-by-export-type" name="export_type">
					<option value=""><?php esc_html_e( 'Show all types', 'woocommerce-customer-order-csv-export' ); ?></option>

					<?php foreach ( $this->get_export_types() as $type => $label ) : ?>

						<option value="<?php echo esc_attr( $type ); ?>" <?php selected( $type, $this->get_export_type() ); ?>>
							<?php echo esc_html( $label ); ?>
						</option>

					<?php endforeach; ?>
				</select>

				<label class="screen-reader-text" for="filter-by-method"><?php esc_html_e( 'Filter by method', 'woocommerce-customer-order-csv-export' ); ?></label>
				<select id="filter-by-method" name="method_type">
					<option value=""><?php esc_html_e( 'Show all methods', 'woocommerce-customer-order-csv-export' ); ?></option>

					<?php foreach ( $this->get_method_types() as $type => $label ) : ?>

						<option value="<?php echo esc_attr( $type ); ?>" <?php selected( $type, $this->get_method_type() ); ?>>
							<?php echo esc_html( $label ); ?>
						</option>

					<?php endforeach; ?>
				</select>

				<?php submit_button( _x( 'Filter', 'button text', 'woocommerce-customer-order-csv-export' ), '', 'filter_action', false ); ?>

			</div>
			<?php
		}
	}


	/** Column methods ************************************************************************************************/


	/**
	 * Gets the "Name" column content.
	 *
	 * @since 5.0.0
	 *
	 * @param Automation $automation automation object
	 * @return string
	 */
	protected function column_name( $automation ) {

		ob_start();

		?>
		<a href="<?php echo esc_url( Automations::get_automation_edit_url( $automation->get_id() ) ); ?>"><?php echo esc_html( $automation->get_name() ); ?></a>
		<?php

		return ob_get_clean();
	}


	/**
	 * Gets the "Enabled" column content.
	 *
	 * @since 5.0.0
	 *
	 * @param Automation $automation automation object
	 * @return string
	 */
	protected function column_enabled( $automation ) {

		ob_start();
		?>

		<label class="wc-customer-order-export-automation-switch">
			<input type="checkbox" class="js-automation-switch" <?php checked( $automation->is_enabled() ); ?> />
			<span class="automation-slider" data-automation-id="<?php echo esc_attr( $automation->get_id() ); ?>"></span>
		</label>

		<?php
		return ob_get_clean();
	}


	/**
	 * Gets the "Type" column content.
	 *
	 * @since 5.0.0
	 *
	 * @param Automation $automation automation object
	 * @return string
	 */
	protected function column_export_type( $automation ) {

		$export_type = $automation->get_format() ? $automation->get_format()->get_export_type() : null;

		return esc_html( $this->get_variable_column_content( $export_type, $this->get_export_types() ) );
	}


	/**
	 * Gets the "Output" column content.
	 *
	 * @since 5.0.0
	 *
	 * @param Automation $automation automation object
	 * @return string
	 */
	protected function column_output_type( $automation ) {

		$output_type = $automation->get_format() ? $automation->get_format()->get_output_type() : null;

		return esc_html( $this->get_variable_column_content( $output_type, $this->get_output_types() ) );
	}


	/**
	 * Gets the "Method" column content.
	 *
	 * @since 5.0.0
	 *
	 * @param Automation $automation automation object
	 * @return string
	 */
	protected function column_method( $automation ) {

		return esc_html( $this->get_variable_column_content( $automation->get_method_type(), $this->get_method_types() ) );
	}


	/**
	 * Gets the "Last Export" column content.
	 *
	 * @since 5.0.0
	 *
	 * @param Automation $automation automation object
	 * @return string
	 */
	protected function column_last_run( $automation ) {

		$last_run = $automation->get_last_run() ? $automation->get_last_run()->format( wc_date_format() . ' \a\t ' . wc_time_format() ) : '';

		return esc_html( $this->get_variable_column_content( $last_run ) );
	}


	/**
	 * Gets the "Next Export" column content.
	 *
	 * @since 5.0.0
	 *
	 * @param Automation $automation automation object
	 * @return string
	 */
	protected function column_next_run( $automation ) {

		$next_run = $automation->is_enabled() && $automation->get_next_run() ? $automation->get_next_run()->format( wc_date_format() . ' \a\t ' . wc_time_format() ) : '';

		return esc_html( $this->get_variable_column_content( $next_run ) );
	}


	/**
	 * Gets the "Actions" column content.
	 *
	 * @since 5.0.0
	 *
	 * @param Automation $automation automation object
	 * @return string
	 */
	protected function column_actions( $automation ) {

		$actions = [
			'edit' => [
				'url'     => Automations::get_automation_edit_url( $automation->get_id() ),
				'label'   => __( 'Manage', 'woocommerce-customer-order-csv-export' ),
				'primary' => true,
			],
			'delete' => [
				'url'   => Automations::get_automation_delete_url( $automation->get_id() ),
				'label' => __( 'Delete', 'woocommerce-customer-order-csv-export' ),
			],
		];

		/**
		 * Filters the action button data in the automations list table.
		 *
		 * @since 5.0.0
		 *
		 * @param array $actions action data
		 * @param Automation $automation automation object
		 */
		$actions = apply_filters( 'wc_customer_order_export_admin_automations_list_actions', $actions, $automation );

		ob_start();

		foreach ( $actions as $action => $args ) {

			$classes = [];

			if ( ! empty( $args['classes'] ) ) {
				$classes = (array) $args['classes'];
			}

			$classes[] = 'button';
			$classes[] = $action;

			if ( ! empty( $args['primary'] ) ) {
				$classes[] = 'button-primary';
			}

			?>

			<a href="<?php echo esc_url( $args['url'] ); ?>" class="<?php echo implode( ' ', array_map( 'sanitize_html_class', $classes ) ); ?>">
				<?php echo esc_html( $args['label'] ); ?>
			</a>

			<?php
		}

		return ob_get_clean();
	}


	/**
	 * Gets the column content for a variable value that has a limited set of options, or no value at all.
	 *
	 * @since 5.0.0
	 *
	 * @param string $value content value
	 * @param array $options content options
	 * @return string
	 */
	protected function get_variable_column_content( $value, array $options = [] ) {

		$content = $value;

		if ( is_array( $options ) && ! empty( $options[ $value ] ) ) {
			$content = $options[ $value ];
		} elseif ( ! $value ) {
			$content = __( 'N/A', 'woocommerce-customer-order-csv-export' );
		}

		return $content;
	}


	/**
	 * Prepares the automations for display.
	 *
	 * @since 5.0.0
	 */
	public function prepare_items() {

		$this->_column_headers = [
			$this->get_columns(),
			[],
			$this->get_sortable_columns()
		];

		$automations = Automation_Factory::get_automations();

		// filter out by export type, if set
		if ( $this->get_export_type() ) {

			foreach ( $automations as $key => $automation ) {

				if ( $automation->get_format() && ( $automation->get_format()->get_export_type() !== $this->get_export_type() ) ) {
					unset( $automations[ $key ] );
				}
			}
		}

		// filter out by method type, if set
		if ( $this->get_method_type() ) {

			foreach ( $automations as $key => $automation ) {

				if ( $automation->get_method_type() !== $this->get_method_type() ) {
					unset( $automations[ $key ] );
				}
			}
		}

		$this->set_pagination_args( [
			'total_items' => count( $automations ),
			'per_page'    => $this->get_items_per_page( 'wc_customer_order_export_admin_automations_per_page' ),
		] );

		if ( $page_number = $this->get_pagenum() ) {

			$per_page = $this->get_pagination_arg( 'per_page' );

			$automations = array_splice( $automations, $per_page * ( $page_number - 1 ), $per_page );
		}

		$this->items = $automations;
	}


	/**
	 * Outputs the HTML to display when there are no custom formats.
	 *
	 * @see WP_List_Table::no_items()
	 *
	 * @since 5.0.0
	 */
	public function no_items() {

		?>
		<p><?php esc_html_e( 'No automated exports.', 'woocommerce-customer-order-csv-export' ); ?></p>
		<?php
	}


	/** Getter methods ************************************************************************************************/


	/**
	 * Gets the list table columns.
	 *
	 * @since 5.5.0
	 *
	 * @return array
	 */
	public function get_columns() {

		$columns = [
			'name'        => esc_html__( 'Name', 'woocommerce-customer-order-csv-export' ),
			'enabled'     => esc_html__( 'Enabled', 'woocommerce-customer-order-csv-export' ),
			'export_type' => esc_html__( 'Type', 'woocommerce-customer-order-csv-export' ),
			'output_type' => esc_html__( 'Output', 'woocommerce-customer-order-csv-export' ),
			'method'      => esc_html__( 'Method', 'woocommerce-customer-order-csv-export' ),
			'last_run'    => esc_html__( 'Last Export', 'woocommerce-customer-order-csv-export' ),
			'next_run'    => esc_html__( 'Next Export', 'woocommerce-customer-order-csv-export' ),
			'actions'     => esc_html__( 'Actions', 'woocommerce-customer-order-csv-export' ),
		];

		/**
		 * Filters the automations list table columns.
		 *
		 * @since 5.5.0
		 *
		 * @param array $columns list table columns
		 */
		return apply_filters( 'wc_customer_order_export_admin_automations_list_columns', $columns );
	}


	/**
	 * Gets the specific export type to list.
	 *
	 * @since 5.0.0
	 *
	 * @return string|null
	 */
	private function get_export_type() {

		if ( null === $this->export_type && ! empty( $_POST['export_type'] ) ) {

			$export_types = $this->get_export_types();

			if ( isset( $export_types[ $_POST['export_type'] ] ) ) {
				$this->export_type = $_POST['export_type'];
			}
		}

		return $this->export_type;
	}


	/**
	 * Gets the available export types.
	 *
	 * @since 5.0.0
	 *
	 * @return array
	 */
	private function get_export_types() {

		if ( ! is_array( $this->export_types ) ) {
			$this->export_types = wc_customer_order_csv_export()->get_export_types();
		}

		return $this->export_types;
	}


	/**
	 * Gets the available output types.
	 *
	 * @since 5.0.0
	 *
	 * @return array
	 */
	private function get_output_types() {

		if ( ! is_array( $this->output_types ) ) {
			$this->output_types = wc_customer_order_csv_export()->get_output_types();
		}

		return $this->output_types;
	}


	/**
	 * Gets the specific method type to list.
	 *
	 * @since 5.0.0
	 *
	 * @return string|null
	 */
	private function get_method_type() {

		if ( null === $this->method_type && ! empty( $_POST['method_type'] ) ) {

			$method_types = $this->get_method_types();

			if ( isset( $method_types[ $_POST['method_type'] ] ) ) {
				$this->method_type = $_POST['method_type'];
			}
		}

		return $this->method_type;
	}


	/**
	 * Gets the available method types.
	 *
	 * @since 5.0.0
	 *
	 * @return array
	 */
	private function get_method_types() {

		if ( ! is_array( $this->method_types ) ) {
			$this->method_types = wc_customer_order_csv_export()->get_methods_instance()->get_export_method_labels();
		}

		return $this->method_types;
	}


}
