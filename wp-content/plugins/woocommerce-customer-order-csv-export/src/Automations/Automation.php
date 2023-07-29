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

namespace SkyVerge\WooCommerce\CSV_Export\Automations;

use SkyVerge\WooCommerce\CSV_Export\Export_Formats\Export_Format_Definition;
use SkyVerge\WooCommerce\PluginFramework\v5_11_6 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * A representation of a single Automated Export.
 *
 * @since 5.0.0
 */
class Automation extends \WC_Data {


	/** @var string ID for this add-on */
	protected $id = '';

	/** @var string the type of object -- used in action and filter names */
	protected $object_type = 'export_automation';

	/** @var array the data for this automated export object */
	protected $data = [
		'name'                 => '',
		'action'               => 'interval',
		'export_type'          => '',
		'output_type'          => '',
		'format_key'           => null,
		'filename'             => '',
		'start'                => null,
		'interval'             => 0,
		'method_type'          => '',
		'method_settings'      => [],
		'last_run'             => null,
		'next_run'             => null,
		'statuses'             => [],
		'product_ids'          => [],
		'product_category_ids' => [],
		'enabled'              => true,
		'mark_as_exported'     => true,
		'new_only'             => true,
		'add_notes'            => true,
	];


	/**
	 * Initializes the automated export.
	 *
	 * @since 5.0.0
	 *
	 * @param string|array|Automation $data automation ID, automation data or instance to initialize the object
	 */
	public function __construct( $data = '' ) {

		parent::__construct( $data );

		if ( is_string( $data ) ) {

			$this->set_id( sanitize_text_field( $data ) );

		} elseif ( is_array( $data ) ) {

			$this->set_props( $data );
			$this->set_object_read( true );

		} elseif ( $data instanceof self ) {

			$this->set_id( $data->get_id() );

		} else {

			$this->set_object_read( true );
		}

		$this->data_store = new Automation_Data_Store_Options();

		if ( $this->get_id() !== '' && ! $this->get_object_read() ) {
			$this->data_store->read( $this );
		}
	}


	/**
	 * Returns the unique ID for this automation.
	 *
	 * @since  5.0.0
	 *
	 * @return string
	 */
	public function get_id() {

		return $this->id;
	}


	/**
	 * Sets the ID for this automation.
	 *
	 * @since  5.0.0
	 *
	 * @return string
	 */
	public function set_id( $id ) {

		$this->id = $id;
	}


	/**
	 * Returns all data for the automation.
	 *
	 * @since 5.0.0
	 *
	 * @return array
	 */
	public function get_data() {

		$data = parent::get_data();

		// return date properties as timestamps
		foreach ( [ 'start', 'last_run', 'next_run' ] as $prop ) {

			if ( isset( $data[ $prop ] ) && is_a( $data[ $prop ], \DateTime::class ) ) {
				$data[ $prop ] = $data[ $prop ]->getTimestamp();
			}
		}

		return $data;
	}


	/**
	 * Returns the name of the automated export.
	 *
	 * @since 5.0.0
	 *
	 * @param string $context the request context - `edit` or `view`
	 * @return string the name of this automated export
	 */
	public function get_name( $context = 'view' ) {

		return $this->get_prop( 'name', $context );
	}


	/**
	 * Sets the name of the automated export.
	 *
	 * TODO: enforce 25 character limit
	 *
	 * @since 5.0.0
	 *
	 * @param string $name the name for the automated export
	 */
	public function set_name( $name ) {

		$this->set_prop( 'name', $name );
	}


	/**
	 * Determines if the automation is interval-based.
	 *
	 * @since 5.0.0
	 *
	 * @return bool
	 */
	public function is_interval_based() {

		return 'interval' === $this->get_action();
	}


	/**
	 * Determine whether the automation should export objects on status change.
	 *
	 * @since 5.0.0
	 *
	 * @return bool
	 */
	public function is_status_based() {

		return 'immediate' === $this->get_action();
	}


	/**
	 * Returns the action that triggers this automated export.
	 *
	 * @since 5.0.0
	 *
	 * @param string $context the request context - `edit` or `view`
	 * @return string 'interval' or 'immediate'
	 */
	public function get_action( $context = 'view' ) {

		return $this->get_prop( 'action', $context );
	}


	/**
	 * Sets the action that triggers this automated export.
	 *
	 * @since 5.0.0
	 *
	 * @param string $action 'interval' or 'immediate'
	 */
	public function set_action( $action ) {

		$this->set_prop( 'action', $action );
	}


	/**
	 * Gets the type of object that will be exported with this automation.
	 *
	 * Object types are defined as constants in {@see \WC_Customer_Order_CSV_Export}
	 *
	 * @since 5.0.0
	 *
	 * @param string $context the request context - `edit` or `view`
	 * @return string
	 */
	public function get_export_type( $context = 'view' ) {

		return $this->get_prop( 'export_type', $context );
	}


	/**
	 * Sets the type of object that will be exported with this automation.
	 *
	 * @since 5.0.0
	 *
	 * @see \WC_Customer_Order_CSV_Export::get_export_types()
	 *
	 * @param string $export_type 'orders', 'customers', or 'coupons'
	 */
	public function set_export_type( $export_type ) {

		$this->set_prop( 'export_type', $export_type );
	}


	/**
	 * Gets the output type for this automated export.
	 *
	 * @see \WC_Customer_Order_CSV_Export::get_output_types()
	 *
	 * @since 5.0.0
	 *
	 * @param string $context the request context - `edit` or `view`
	 * @return string
	 */
	public function get_output_type( $context = 'view' ) {

		return $this->get_prop( 'output_type', $context );
	}


	/**
	 * Sets the output type for this automated export.
	 *
	 * @since 5.0.0
	 *
	 * @see \WC_Customer_Order_CSV_Export::get_output_types()
	 *
	 * @param string $output_type 'csv' or 'xml'
	 */
	public function set_output_type( $output_type ) {

		$this->set_prop( 'output_type', $output_type );
	}


	/**
	 * Gets the format key for the format definition associated with this automated export.
	 *
	 * @since 5.0.0
	 *
	 * @param string $context the request context - `edit` or `view`
	 * @return string
	 */
	public function get_format_key( $context = 'view' ) {

		return $this->get_prop( 'format_key', $context );
	}


	/**
	 * Sets the format key for the format defintion associated with this automated export.
	 *
	 * @since 5.0.0
	 *
	 * @param string $format_key
	 */
	public function set_format_key( $format_key ) {

		$this->set_prop( 'format_key', $format_key );
	}


	/**
	 * Returns the format definition object for this automated export.
	 *
	 * @since 5.0.0
	 *
	 * @return Export_Format_Definition|null
	 */
	public function get_format() {

		$export_type = $this->get_export_type();
		$output_type = $this->get_output_type();
		$format_key  = $this->get_format_key();

		return wc_customer_order_csv_export()->get_formats_instance()->get_format_definition( $export_type, $format_key, $output_type );
	}


	/**
	 * Returns the filename for this automated export.
	 *
	 * @since 5.0.0
	 *
	 * @param string $context the request context - `edit` or `view`
	 * @return string
	 */
	public function get_filename( $context = 'view' ) {

		return $this->get_prop( 'filename', $context );
	}


	/**
	 * Sets the filename for this automated export.
	 *
	 * @since 5.0.0
	 *
	 * @param string $filename the export filename
	 */
	public function set_filename( $filename ) {

		$this->set_prop( 'filename', $filename );
	}


	/**
	 * Gets the date and time when this export is going to run.
	 *
	 * @since 5.0.0
	 *
	 * @return \DateTime
	 */
	public function get_start() {

		return $this->get_prop( 'start' );
	}


	/**
	 * Sets the date and time when this export is going to run.
	 *
	 * @since 5.0.0
	 *
	 * @param \DateTime|string|int a DateTime object, a date string, or a timestamp
	 */
	public function set_start( $date ) {

		$this->set_date_prop( 'start', $date );
	}


	/**
	 * Sets a date prop whilst handling formatting and datetime objects.
	 *
	 * @see \WC_Data::set_date_prop()
	 *
	 * @since 5.0.0
	 *
	 * @param string $prop name of prop to set.
	 * @param \DateTime|string|int $value value of the prop.
	 */
	protected function set_date_prop( $prop, $value ) {

		/** {@see \WC_Data::set_date_prop()} supports {@see \WC_DateTime}, string or timestamp */
		if ( is_a( $value, \DateTime::class ) && ! is_a( $value, \WC_DateTime::class ) ) {
			$value = $value->getTimestamp();
		}

		parent::set_date_prop( $prop, $value );
	}


	/**
	 * Returns the number of seconds between automatic exports.
	 *
	 * @since 5.0.0
	 *
	 * @param string $context the request context - `edit` or `view`
	 * @return int seconds
	 */
	public function get_interval( $context = 'view' ) {

		return $this->get_prop( 'interval', $context );
	}


	/**
	 * Sets the number of seconds between automatic exports.
	 *
	 * @since 5.0.0
	 *
	 * @param int $seconds
	 */
	public function set_interval( $seconds ) {

		$this->set_prop( 'interval', $seconds );
	}


	/**
	 * Gets the type of export method used in this automated export.
	 *
	 * @since 5.0.0
	 *
	 * @param string $context
	 * @return string
	 */
	public function get_method_type( $context = 'view' ) {

		return $this->get_prop( 'method_type', $context );
	}


	/**
	 * Sets the type of export method used in this automated export.
	 *
	 * @see \WC_Customer_Order_CSV_Export_Methods::get_export_method()
	 *
	 * @since 5.0.0
	 *
	 * @param string $method_type
	 */
	public function set_method_type( $method_type ) {

		$this->set_prop( 'method_type', $method_type );
	}


	/**
	 * Gets the settings for the export method used in this automation.
	 *
	 * @since 5.0.0
	 *
	 * @param string $context the request context - `edit` or `view`
	 * @return array
	 */
	public function get_method_settings( $context = 'view' ) {

		return $this->get_prop( 'method_settings', $context );
	}


	/**
	 * Sets the settings for the export method used in this automation.
	 *
	 * @see \WC_Customer_Order_CSV_Export_Methods::get_export_method()
	 *
	 * @since 5.0.0
	 *
	 * @param array $settings an array of settings for the export method
	 */
	public function set_method_settings( array $method_settings ) {

		$this->set_prop( 'method_settings', $method_settings );
	}


	/**
	 * Returns the export method object configured for this automated export.
	 *
	 * @since 5.0.0
	 *
	 * @param array $extra_settings additional settings for the export method
	 * @return \WC_Customer_Order_CSV_Export_Method the export method object
	 * @throws Framework\SV_WC_Plugin_Exception if the method cannot be instantiated or one hasn't been assigned
	 */
	public function get_method( array $extra_settings = [] ) {

		$export_type     = $this->get_export_type();
		$output_type     = $this->get_output_type();
		$method_type     = $this->get_method_type();
		$method_settings = array_merge( $this->get_method_settings(), $extra_settings );

		if ( ! $method_type ) {
			/* translators: Placeholders: %s - export method */
			throw new Framework\SV_WC_Plugin_Exception( sprintf( esc_html__( 'Export method not set', 'woocommerce-customer-order-csv-export' ) ) );
		}

		$export_method = wc_customer_order_csv_export()->get_methods_instance()->get_export_method( $method_type, $export_type, '', $output_type, $method_settings );

		if ( ! is_object( $export_method ) ) {
			/* translators: Placeholders: %s - export method */
			throw new Framework\SV_WC_Plugin_Exception( sprintf( esc_html__( 'Invalid Export Method: %s', 'woocommerce-customer-order-csv-export' ), $method_type ) );
		}

		return $export_method;
	}


	/**
	 * Returns the date and time of the last export.
	 *
	 * @since 5.0.0
	 *
	 * @param string $context the request context - `edit` or `view`
	 * @return \DateTime|null last export date or null if the export hasn't started
	 */
	public function get_last_run( $context = 'view' ) {

		return $this->get_prop( 'last_run', $context );
	}


	/**
	 * Sets the date and time of the last export.
	 *
	 * @since 5.0.0
	 *
	 * @param \DateTime|string|int a DateTime object, a date string, or a timestamp
	 */
	public function set_last_run( $date ) {

		$this->set_date_prop( 'last_run', $date );
	}


	/**
	 * Gets the date and time when the next export should occur.
	 *
	 * @since 5.0.0
	 *
	 * @param string $context the request context - `edit` or `view`
	 * @return \DateTime|null the date and time when the next export should occur
	 */
	public function get_next_run( $context = 'view' ) {

		$next_run = $this->get_prop( 'next_run', $context );

		if ( $timestamp = Scheduler::get_next_scheduled_action( $this->get_id() ) ) {

			$datetime = new \DateTime();
			$datetime->setTimestamp( $timestamp );
			$datetime->setTimezone( new \DateTimeZone( wc_timezone_string() ) );

			$next_run = $datetime;
		}

		return $next_run;
	}


	/**
	 * Sets the date and time when the next export should occur.
	 *
	 * @since 5.0.0
	 *
	 * @param \DateTime|string|int a DateTime object, a date string, or a timestamp
	 */
	public function set_next_run( $date ) {

		$this->set_date_prop( 'next_run', $date );
	}


	/**
	 * Returns the statuses that objects must have to be included in this automated export.
	 *
	 * @since 5.0.0
	 *
	 * @param string $context the request context - `edit` or `view`
	 * @return array an array of object statuses (like order statuses)
	 */
	public function get_statuses( $context = 'view' ) {

		return $this->get_prop( 'statuses', $context );
	}


	/**
	 * Sets the statustes that objects must have to be included in this automated export.
	 *
	 * @since 5.0.0
	 *
	 * @param array $statuses a list of object statuses
	 */
	public function set_statuses( array $statuses ) {

		$this->set_prop( 'statuses', $statuses );
	}


	/**
	 * Returns the IDs of selected products for this automated export.
	 *
	 * @since 5.0.0
	 *
	 * @param string $context the request context - `edit` or `view`
	 * @return array
	 */
	public function get_product_ids( $context = 'view' ) {

		return $this->get_prop( 'product_ids', $context );
	}


	/**
	 * Sets the IDs of selected products for this automated export.
	 *
	 * If set, only objects associated with the selected products should be exported.
	 *
	 * @since 5.0.0
	 *
	 * @param array $product_ids the IDs of products that should be used to determine which objects to export
	 */
	public function set_product_ids( array $product_ids ) {

		$this->set_prop( 'product_ids', $product_ids );
	}


	/**
	 * Returns the IDs of selected product categories for this automated export.
	 *
	 * @since 5.0.0
	 *
	 * @param string $context the request context - `edit` or `view`
	 * @return int[]
	 */
	public function get_product_category_ids( $context = 'view' ) {

		return $this->get_prop( 'product_category_ids', $context );
	}


	/**
	 * Sets the IDs of selected product categories for this automated export.
	 *
	 * If set, only objects associated with the selected categories should be exported.
	 *
	 * @since 5.0.0
	 *
	 * @param array $product_category_ids the IDs of product categories that should be used to determine which objects to export
	 */
	public function set_product_category_ids( array $product_category_ids ) {

		$this->set_prop( 'product_category_ids', $product_category_ids );
	}


	/**
	 * Sets whether this automated export is enabled or not.
	 *
	 * @since 5.0.0
	 *
	 * @param bool $enabled true if this automated export is enabled
	 */
	public function set_enabled( $enabled ) {

		$this->set_prop( 'enabled', $enabled );
	}


	/**
	 * Sets whether this automated export should mark objects as exported or not.
	 *
	 * @since 5.0.0
	 *
	 * @param bool $mark_as_exported true if this automated should mark objects as exported
	 */
	public function set_mark_as_exported( $mark_as_exported ) {

		$this->set_prop( 'mark_as_exported', $mark_as_exported );
	}


	/**
	 * Sets whether only new objects should be exported or not.
	 *
	 * @since 5.0.0
	 *
	 * @param bool $new_only true if only new objects should be exported
	 */
	public function set_new_only( $new_only ) {

		$this->set_prop( 'new_only', $new_only );
	}


	/**
	 * Sets whether notes should be added to exported objects.
	 *
	 * @since 5.0.0
	 *
	 * @param bool $add_notes true if notes should be added to exported objects
	 */
	public function set_add_notes( $add_notes ) {

		$this->set_prop( 'add_notes', $add_notes );
	}


	/**
	 * Returns the IDs of the objects that should be exported.
	 *
	 * @see \WC_Customer_Order_CSV_Export_Query_Parser
	 *
	 * @since 5.0.0
	 *
	 * @return array
	 */
	public function get_object_ids() {

		$export_type = $this->get_export_type();
		$output_type = $this->get_output_type();
		$query       = [];

		switch ( $export_type ) {

			case \WC_Customer_Order_CSV_Export::EXPORT_TYPE_ORDERS:

				$export_new_orders_only = $this->is_new_only_enabled();

				/**
				 * Filters whether only new orders should be auto-exported for the given output type.
				 *
				 * @since 5.0.0
				 *
				 * @param bool $new_only defaults to true
				 * @param Automation $automation the Automation object
				 */
				$export_new_orders_only = apply_filters( "wc_customer_order_export_{$output_type}_auto_export_new_orders_only", $export_new_orders_only, $this );

				/**
				 * Filters whether only new orders should be auto-exported.
				 *
				 * @since 5.0.0
				 *
				 * @param bool $new_only defaults to true
				 * @param Automation $automation the Automation object
				 */
				$export_new_orders_only = apply_filters( 'wc_customer_order_export_auto_export_new_orders_only', $export_new_orders_only, $this );

				$query = [
					'statuses'           => $this->get_statuses(),
					'products'           => $this->get_product_ids(),
					'product_categories' => $this->get_product_category_ids(),
					'not_exported'       => $export_new_orders_only,
					'automation_id'      => $this->get_id(),
				];
			break;

			case \WC_Customer_Order_CSV_Export::EXPORT_TYPE_CUSTOMERS:

				$export_new_customers_only = $this->is_new_only_enabled();

				/**
				 * Filters whether only new customers should be auto-exported for the given output type.
				 *
				 * @since 5.0.0
				 *
				 * @param bool $new_only defaults to true
				 * @param Automation $automation the Automation object
				 */
				$export_new_customers_only = apply_filters( "wc_customer_order_export_{$output_type}_auto_export_new_customers_only", $export_new_customers_only, $this );

				/**
				 * Filters whether only new customers should be auto-exported.
				 *
				 * @since 5.0.0
				 *
				 * @param bool $new_only defaults to true
				 * @param Automation $automation the Automation object
				 */
				$export_new_customers_only = apply_filters( 'wc_customer_order_export_auto_export_new_customers_only', $export_new_customers_only, $this );

				$query = [
					'not_exported'  => $export_new_customers_only,
					'automation_id' => $this->get_id(),
				];
			break;

			case \WC_Customer_Order_CSV_Export::EXPORT_TYPE_COUPONS:

				$query = [
					'products'           => $this->get_product_ids(),
					'product_categories' => $this->get_product_category_ids(),
				];
				break;
		}

		require_once( wc_customer_order_csv_export()->get_plugin_path() . '/src/class-wc-customer-order-csv-export-query-parser.php' );

		return \WC_Customer_Order_CSV_Export_Query_Parser::parse_export_query( $query, $export_type, $this->get_output_type() );
	}


	/**
	 * Determines whether this automated export is enabled or not.
	 *
	 * @since 5.0.0
	 *
	 * @param string $context the request context - `edit` or `view`
	 * @return bool
	 */
	public function is_enabled( $context = 'view' ) {

		return $this->get_prop( 'enabled', $context );
	}


	/**
	 * Determines whether objects exported by this automated export should be
	 * marked as exported and excluded from future exports.
	 *
	 * @since 5.0.0
	 *
	 * @param string $context the request context - `edit` or `view`
	 * @return bool
	 */
	public function is_mark_as_exported_enabled( $context = 'view' ) {

		return $this->get_prop( 'mark_as_exported', $context );
	}


	/**
	 * Determines whether only new objects should be inclueded in this automated export.
	 *
	 * @since 5.0.0
	 *
	 * @param string $context the request context - `edit` or `view`
	 * @return bool
	 */
	public function is_new_only_enabled( $context = 'view' ) {

		return $this->get_prop( 'new_only', $context );
	}


	/**
	 * Determines whether notes should be added to objects included in this automated export.
	 *
	 * @since 5.0.0
	 *
	 * @param string $context the request context - `edit` or `view`
	 * @return bool
	 */
	public function is_note_enabled( $context = 'view' ) {

		return $this->get_prop( 'add_notes', $context );
	}


	/**
	 * Apply any changes to the object.
	 *
	 * Overridden to fire an action when the schedule-specific data changes so we can reschedule actions.
	 *
	 * @since 5.0.0
	 */
	public function apply_changes() {

		if ( array_key_exists( 'interval', $this->changes ) || array_key_exists( 'start', $this->changes ) || array_key_exists( 'action', $this->changes ) ) {

			/**
			 * Fires when an automation is updated and the schedule details have changed.
			 *
			 * @since 5.0.0
			 *
			 * @param string $id automation ID
			 * @param Automation $automation automation object
			 */
			do_action( 'wc_customer_order_export_update_automation_schedule', $this->get_id(), $this );
		}

		// replace data with changed values
		foreach ( $this->changes as $changed_key => $changed_value ) {
			$this->data[ $changed_key ] = $changed_value;
		}

		// clean up changes
		$this->changes = [];
	}


}
