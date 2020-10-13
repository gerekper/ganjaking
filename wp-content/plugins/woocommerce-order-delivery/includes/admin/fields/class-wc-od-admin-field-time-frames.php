<?php
/**
 * Field: Time Frames.
 *
 * @package WC_OD/Admin/Fields
 * @since   1.5.0
 */

defined( 'ABSPATH' ) || exit;

if ( class_exists( 'WC_OD_Admin_Field_Time_Frames', false ) ) {
	return;
}

if ( ! class_exists( 'WC_OD_Admin_Field_Table', false ) ) {
	include_once 'abstract-class-wc-od-admin-field-table.php';
}

/**
 * WC_OD_Admin_Field_Time_Frames class.
 */
class WC_OD_Admin_Field_Time_Frames extends WC_OD_Admin_Field_Table {

	/**
	 * Define if the table is sortable or not.
	 *
	 * @var bool
	 */
	protected $sortable = true;

	/**
	 * The day ID.
	 *
	 * @var mixed A weekday index (0-6). False otherwise.
	 */
	protected $day_id = false;

	/**
	 * Constructor.
	 *
	 * @since 1.5.0
	 *
	 * @param array $field The field arguments.
	 */
	public function __construct( $field ) {
		$this->init_day_id();

		$columns = array(
			'title'            => array(
				'label' => __( 'Title', 'woocommerce-order-delivery' ),
			),
			'description'      => array(
				'label' => __( 'Description', 'woocommerce-order-delivery' ),
			),
			'number_of_orders' => array(
				'label' => __( 'Number of orders', 'woocommerce-order-delivery' ),
			),
		);

		parent::__construct(
			$field,
			$columns,
			( isset( $field['value'] ) ? $field['value'] : array() )
		);
	}

	/**
	 * Initialize the day ID.
	 *
	 * @since 1.5.0
	 */
	public function init_day_id() {
		if ( isset( $_GET['day_id'] ) ) {
			$day_id = (int) wc_clean( wp_unslash( $_GET['day_id'] ) ); // WPCS: CSRF ok.

			$this->day_id = ( $day_id >= 0 && $day_id <= 6 ? $day_id : false );
		}
	}

	/**
	 * Gets the row URL.
	 *
	 * @since 1.5.0
	 *
	 * @param int $row The row index.
	 * @return string
	 */
	public function get_row_url( $row ) {
		$params = array(
			'frame_id' => $row,
		);

		if ( false !== $this->day_id ) {
			$params['day_id'] = $this->day_id;
		}

		return wc_od_get_settings_url( 'time_frame', $params );
	}

	/**
	 * Outputs the 'add time frame' button.
	 *
	 * @since 1.5.0
	 */
	public function add_button() {
		printf(
			'<a class="button" href="%1$s">%2$s</a>',
			esc_url( $this->get_row_url( 'new' ) ),
			esc_html__( 'Add time frame', 'woocommerce-order-delivery' )
		);
	}

	/**
	 * Outputs the blank row.
	 *
	 * @since 1.6.0
	 */
	public function output_blank_row() {
		?>
		<tr class="wc-od-time-frames-blank-row">
			<td colspan="<?php echo esc_attr( count( $this->columns ) ); ?>">
				<p><?php esc_html_e( 'You can split this delivery day into multiple intervals of time called "time frames".', 'woocommerce-order-delivery' ); ?></p>
				<p><?php esc_html_e( 'This allows the customers to select one of these time frames in addition to the delivery date during checkout.', 'woocommerce-order-delivery' ); ?></p>
				<p><?php esc_html_e( 'Each time frame has its own configuration.', 'woocommerce-order-delivery' ); ?></p>
				<p><?php esc_html_e( 'Once you define a time frame, some settings of this delivery day, like the "Shipping methods", will be ignored and the time frame settings will take preference.', 'woocommerce-order-delivery' ); ?></p>
			</td>
		</tr>
		<?php
	}

	/**
	 * Gets the row actions.
	 *
	 * @since 1.7.0
	 *
	 * @param mixed $row The row index.
	 * @return array
	 */
	public function get_row_actions( $row ) {
		return array(
			'settings' => array(
				'label' => __( 'Edit', 'woocommerce-order-delivery' ),
				'url'   => $this->get_row_url( $row ),
			),
			'delete'   => array(
				'label' => __( 'Delete', 'woocommerce-order-delivery' ),
				'url'   => '#',
			),
		);
	}

	/**
	 * Outputs the action links for a row.
	 *
	 * @since 1.5.0
	 * @deprecated 1.7.0
	 *
	 * @param mixed $row The row index.
	 */
	public function row_actions( $row ) {
		wc_deprecated_function( __FUNCTION__, '1.7.0', 'WC_OD_Admin_Field_Table->output_row_actions()' );

		$this->output_row_actions( $row );
	}

	/**
	 * Outputs the column 'title'.
	 *
	 * @since 1.5.0
	 *
	 * @param int   $row  The row index.
	 * @param array $data The row data.
	 */
	public function output_column_title( $row, $data ) {
		printf(
			'<a href="%1$s">%2$s</a>',
			esc_url( $this->get_row_url( $row ) ),
			esc_html( $data['title'] )
		);

		$this->output_row_actions( $row );
	}

	/**
	 * Outputs the column 'description'.
	 *
	 * @since 1.5.0
	 *
	 * @param int   $row  The row index.
	 * @param array $data The row data.
	 */
	public function output_column_description( $row, $data ) {
		echo wp_kses_post( '<strong>' . wc_od_time_frame_to_string( $data ) . '</strong>' );

		if ( ! empty( $data['shipping_methods'] ) ) {
			if ( isset( $data['shipping_methods_option'] ) && 'all_except' === $data['shipping_methods_option'] ) {
				$label = __( 'All shipping methods, except:', 'woocommerce-order-delivery' );
			} else {
				$label = __( 'Shipping methods:', 'woocommerce-order-delivery' );
			}

			printf(
				'<p><strong>%1$s</strong> %2$s</p>',
				esc_html( $label ),
				esc_html( join( ' | ', array_map( 'wc_od_shipping_method_choice_label', $data['shipping_methods'] ) ) )
			);
		}
	}

	/**
	 * Outputs the column 'number_of_orders'.
	 *
	 * @since 1.8.0
	 *
	 * @param int   $row  The row index.
	 * @param array $data The row data.
	 */
	public function output_column_number_of_orders( $row, $data ) {
		$number_of_orders = isset( $data['number_of_orders'] ) ? $data['number_of_orders'] : 0;

		printf( '<p class="text-center">%1$s</p>', esc_html( $number_of_orders ) );
	}

	/**
	 * Outputs the table footer.
	 *
	 * @since 1.5.0
	 */
	public function output_footer() {
		?>
		<tfoot>
			<tr>
				<td colspan="<?php echo esc_attr( count( $this->columns ) ); ?>">
					<?php $this->add_button(); ?>
				</td>
			</tr>
		</tfoot>
		<?php
	}
}
