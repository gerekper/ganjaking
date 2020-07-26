<?php
/**
 * Field: Delivery Days.
 *
 * @package WC_OD/Admin/Fields
 * @since   1.5.0
 */

defined( 'ABSPATH' ) || exit;

if ( class_exists( 'WC_OD_Admin_Field_Delivery_days', false ) ) {
	return;
}

if ( ! class_exists( 'WC_OD_Admin_Field_Table', false ) ) {
	include_once 'abstract-class-wc-od-admin-field-table.php';
}

/**
 * WC_OD_Admin_Field_Delivery_days Class.
 */
class WC_OD_Admin_Field_Delivery_Days extends WC_OD_Admin_Field_Table {

	/**
	 * Constructor.
	 *
	 * @since 1.5.0
	 *
	 * @param array $field The field arguments.
	 */
	public function __construct( $field ) {
		$columns = array(
			'name'        => array(
				'label' => __( 'Delivery day', 'woocommerce-order-delivery' ),
			),
			'status'      => array(
				'label' => __( 'Enabled', 'woocommerce-order-delivery' ),
				'width' => '1%',
			),
			'description' => array(
				'label' => __( 'Description', 'woocommerce-order-delivery' ),
			),
			'action'      => array(
				'label' => '',
				'width' => '1%',
			),
		);

		parent::__construct(
			$field,
			$columns,
			$field['value']
		);
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
		return wc_od_get_settings_url( 'delivery_day', array( 'day_id' => $row ) );
	}

	/**
	 * Outputs the column 'name'.
	 *
	 * @since 1.5.0
	 *
	 * @param int $row The row index.
	 */
	public function output_column_name( $row ) {
		$week_days = wc_od_get_week_days();

		printf(
			'<a href="%1$s">%2$s</a>',
			esc_url( $this->get_row_url( $row ) ),
			esc_html( $week_days[ $row ] )
		);
	}

	/**
	 * Outputs the column 'status'.
	 *
	 * @since 1.5.0
	 *
	 * @param int   $row  The row index.
	 * @param array $data The row data.
	 */
	public function output_column_status( $row, $data ) {
		$enabled = wc_string_to_bool( $data['enabled'] );
		$label   = ( $enabled ? __( 'Yes', 'woocommerce-order-delivery' ) : __( 'No', 'woocommerce-order-delivery' ) );

		echo '<label class="wc-od-input-toggle">';

		printf(
			'<input type="checkbox" name="%1$s" %2$s />',
			esc_attr( $this->id . "[{$row}][enabled]" ),
			checked( $enabled, true, false )
		);

		$class  = 'woocommerce-input-toggle woocommerce-input-toggle--';
		$class .= ( $enabled ? 'enabled' : 'disabled' );

		printf( '<span class="%1$s">%2$s</span>', esc_attr( $class ), esc_html( $label ) );

		echo '</label>';
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
		if ( ! empty( $data['time_frames'] ) ) {
			$time_frames = array();

			foreach ( $data['time_frames'] as $key => $time_frame ) {
				$params = array(
					'day_id'   => $row,
					'frame_id' => $key,
				);

				$time_frames[] = sprintf(
					'<a href="%1$s">%2$s</a>',
					esc_url( wc_od_get_settings_url( 'time_frame', $params ) ),
					esc_html( $time_frame['title'] )
				);
			}

			printf(
				'<p><strong>%1$s</strong> %2$s</p>',
				esc_html__( 'Time frames:', 'woocommerce-order-delivery' ),
				wp_kses_post( join( ' | ', $time_frames ) )
			);
		} elseif ( ! empty( $data['shipping_methods'] ) ) {
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
		} else {
			echo '-';
		}
	}

	/**
	 * Outputs the column 'action'.
	 *
	 * @since 1.5.0
	 *
	 * @param int   $row  The row index.
	 * @param array $data The row data.
	 */
	public function output_column_action( $row, $data ) {
		$label = ( wc_string_to_bool( $data['enabled'] ) ? __( 'Manage', 'woocommerce-order-delivery' ) : __( 'Set Up', 'woocommerce-order-delivery' ) );

		printf(
			'<a class="button alignright" href="%1$s">%2$s</a>',
			esc_url( $this->get_row_url( $row ) ),
			esc_html( $label )
		);
	}

	/**
	 * Outputs the table footer.
	 *
	 * @since 1.6.0
	 */
	public function output_footer() {
		?>
		<tfoot>
		<tr>
			<td colspan="<?php echo esc_attr( count( $this->columns ) ); ?>">
				<?php
					printf(
						'<a class="button" href="%1$s">%2$s</a>',
						esc_url( wc_od_get_settings_url( 'time_frame' ) ),
						esc_html__( 'Add time frame', 'woocommerce-order-delivery' )
					);
				?>
			</td>
		</tr>
		</tfoot>
		<?php
	}
}
