<?php
/**
 * Field: Delivery Ranges.
 *
 * @package WC_OD/Admin/Fields
 * @since   1.7.0
 */

defined( 'ABSPATH' ) || exit;

if ( class_exists( 'WC_OD_Admin_Field_Delivery_Ranges', false ) ) {
	return;
}

if ( ! class_exists( 'WC_OD_Admin_Field_Table', false ) ) {
	include_once 'abstract-class-wc-od-admin-field-table.php';
}

/**
 * WC_OD_Admin_Field_Delivery_Ranges class.
 */
class WC_OD_Admin_Field_Delivery_Ranges extends WC_OD_Admin_Field_Table {

	/**
	 * Define if the table is sortable or not.
	 *
	 * @var bool
	 */
	protected $sortable = true;

	/**
	 * Constructor.
	 *
	 * @since 1.7.0
	 *
	 * @param array $field The field arguments.
	 */
	public function __construct( $field ) {
		$columns = array(
			'title'            => array(
				'label' => __( 'Title', 'woocommerce-order-delivery' ),
			),
			'range'            => array(
				'label' => __( 'Range (days)', 'woocommerce-order-delivery' ),
			),
			'shipping_methods' => array(
				'label' => __( 'Shipping method(s)', 'woocommerce-order-delivery' ),
			),
		);

		parent::__construct(
			$field,
			$columns,
			( isset( $field['value'] ) ? $field['value'] : array() )
		);
	}

	/**
	 * Sets the table data.
	 *
	 * @since 1.7.0
	 *
	 * @param array $data The table data.
	 */
	public function set_data( $data ) {
		// Add the default delivery range to the list.
		$default_range = WC_OD_Delivery_Ranges::get_range( 0 );

		if ( $default_range ) {
			$default_range_data = $default_range->get_data();

			unset( $default_range_data['id'], $default_range_data['meta_data'] );

			$data[0] = $default_range_data;
		}

		parent::set_data( $data );
	}

	/**
	 * Gets the column by key.
	 *
	 * @since 1.7.0
	 *
	 * @param string $column The column key.
	 * @return array
	 */
	public function get_column( $column ) {
		// Use the same configuration than the 'sort' column.
		if ( 'worldwide' === $column ) {
			$column = 'sort';
		}

		return parent::get_column( $column );
	}

	/**
	 * Gets the HTML attributes for a specific row.
	 *
	 * @since 1.7.0
	 *
	 * @param mixed $row The row index.
	 * @return array
	 */
	public function get_row_attrs( $row ) {
		$attrs = parent::get_row_attrs( $row );

		if ( 0 === $row ) {
			$class   = ( isset( $attrs['class'] ) ? $attrs['class'] : array() );
			$class[] = 'unsortable';

			$attrs['class'] = $class;
		}

		return $attrs;
	}

	/**
	 * Gets the columns for a specific row.
	 *
	 * @since 1.7.0
	 *
	 * @param mixed $row The row index.
	 * @return array
	 */
	public function get_row_columns( $row ) {
		if ( 0 !== $row ) {
			return parent::get_row_columns( $row );
		}

		// Rename the column 'sort' to 'worldwide' for the default row.
		$columns = array_merge(
			array(
				'worldwide' => $this->columns['sort'],
			),
			$this->columns
		);

		unset( $columns['sort'] );

		return $columns;
	}

	/**
	 * Gets the row URL.
	 *
	 * @since 1.7.0
	 *
	 * @param int $row The row index.
	 * @return string
	 */
	public function get_row_url( $row ) {
		return wc_od_get_settings_url( 'delivery_range', array( 'range_id' => $row ) );
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
		$actions = array(
			'settings' => array(
				'label' => __( 'Edit', 'woocommerce-order-delivery' ),
				'url'   => $this->get_row_url( $row ),
			),
		);

		if ( 0 !== $row ) {
			$actions['delete'] = array(
				'label' => __( 'Delete', 'woocommerce-order-delivery' ),
				'url'   => '#',
			);
		}

		return $actions;
	}

	/**
	 * Outputs the column 'title'.
	 *
	 * @since 1.7.0
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
	 * Outputs the column 'range'.
	 *
	 * @since 1.7.0
	 *
	 * @param int   $row  The row index.
	 * @param array $data The row data.
	 */
	public function output_column_range( $row, $data ) {
		echo esc_html( sprintf( '%1$s - %2$s', $data['from'], $data['to'] ) );
	}

	/**
	 * Outputs the column 'shipping_methods'.
	 *
	 * @since 1.7.0
	 *
	 * @param int   $row  The row index.
	 * @param array $data The row data.
	 */
	public function output_column_shipping_methods( $row, $data ) {
		if ( 0 === $row ) {
			echo esc_html__( 'The shipping methods that are not included in any other delivery range.', 'woocommerce-order-delivery' );
			return;
		}

		if ( empty( $data['shipping_methods'] ) || empty( $data['shipping_methods_option'] ) ) {
			echo '-';
			return;
		}

		echo '<p>';
		if ( 'all_except' === $data['shipping_methods_option'] ) :
			echo '<strong>' . esc_html( __( 'All, except:', 'woocommerce-order-delivery' ) ) . '</strong> ';
		endif;
		echo esc_html( join( ' | ', array_map( 'wc_od_shipping_method_choice_label', $data['shipping_methods'] ) ) );
		echo '</p>';
	}

	/**
	 * Outputs the table footer.
	 *
	 * @since 1.7.0
	 */
	public function output_footer() {
		?>
		<tfoot>
			<tr>
				<td colspan="<?php echo esc_attr( count( $this->columns ) ); ?>">
					<?php
					printf(
						'<a class="button" href="%1$s">%2$s</a>',
						esc_url( $this->get_row_url( 'new' ) ),
						esc_html__( 'Add delivery range', 'woocommerce-order-delivery' )
					);
					?>
				</td>
			</tr>
		</tfoot>
		<?php
	}
}
