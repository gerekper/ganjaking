<?php
/**
 * Field: table.
 *
 * @package WC_OD/Admin/Fields
 * @since   1.5.0
 */

defined( 'ABSPATH' ) || exit;

if ( class_exists( 'WC_OD_Admin_Field_Table', false ) ) {
	return;
}

/**
 * WC_OD_Admin_Field_Table class.
 */
abstract class WC_OD_Admin_Field_Table {

	/**
	 * The field Id.
	 *
	 * @var string
	 */
	protected $id;

	/**
	 * The field arguments.
	 *
	 * @var array
	 */
	protected $field = array();

	/**
	 * The table columns.
	 *
	 * @var array
	 */
	protected $columns = array();

	/**
	 * The table data.
	 *
	 * @var array
	 */
	protected $data = array();

	/**
	 * Define if the table is sortable or not.
	 *
	 * @var bool
	 */
	protected $sortable = false;

	/**
	 * Constructor.
	 *
	 * @since 1.5.0
	 *
	 * @param array $field   The field arguments.
	 * @param array $columns The table columns.
	 * @param array $data    The table data.
	 */
	public function __construct( $field, $columns = array(), $data = array() ) {
		$this->field = $field;
		$this->id    = ( is_array( $field ) && isset( $field['id'] ) ? $field['id'] : '' );

		$this->set_columns( $columns );
		$this->set_data( $data );
	}

	/**
	 * Sets the table columns.
	 *
	 * @since 1.5.0
	 *
	 * @param array $columns The columns data.
	 */
	public function set_columns( $columns ) {
		if ( $this->sortable ) {
			$columns = array_merge(
				array(
					'sort' => array(
						'label' => '',
						'width' => '1%',
					),
				),
				$columns
			);
		}

		/**
		 * Filters the table columns.
		 *
		 * @since 1.5.0
		 *
		 * @param array  $columns The table columns.
		 * @param string $id      The setting ID.
		 */
		$this->columns = apply_filters( 'wc_od_field_table_columns', $columns, $this->id );
	}

	/**
	 * Sets the table data.
	 *
	 * @since 1.5.0
	 *
	 * @param array $data The table data.
	 */
	public function set_data( $data ) {
		/**
		 * Filters the table data.
		 *
		 * @since 1.5.0
		 *
		 * @param array  $data The table data.
		 * @param string $id   The setting ID.
		 */
		$this->data = apply_filters( 'wc_od_field_table_data', $data, $this->id );
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
		return ( isset( $this->columns[ $column ] ) ? $this->columns[ $column ] : array() );
	}

	/**
	 * Gets the HTML attributes for a specific column.
	 *
	 * @since 1.7.0
	 *
	 * @param string $column The column key.
	 * @return array
	 */
	public function get_column_attrs( $column ) {
		$data  = $this->get_column( $column );
		$attrs = array(
			'class' => array( $column ),
		);

		if ( isset( $data['width'] ) ) {
			$attrs['width'] = $data['width'];
		}

		return $attrs;
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
		return array();
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
		return $this->columns;
	}

	/**
	 * Gets the row data.
	 *
	 * @since 1.7.0
	 *
	 * @param mixed $row The row index.
	 * @return array
	 */
	public function get_row_data( $row ) {
		return ( isset( $this->data[ $row ] ) ? $this->data[ $row ] : array() );
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
		return array();
	}

	/**
	 * Gets the classes used in the table HTML tag.
	 *
	 * @since 1.5.0
	 *
	 * @return array
	 */
	public function get_table_class() {
		$class = array(
			$this->id,
			'wc-od-field-table',
			'widefat',
		);

		if ( $this->sortable ) {
			$class[] = 'sortable';
		}

		return $class;
	}

	/**
	 * Outputs the field content.
	 *
	 * @since 1.5.0
	 */
	public function output() {
		?>
		<table class="<?php echo esc_attr( join( ' ', $this->get_table_class() ) ); ?>">
			<thead>
				<tr>
					<?php
					foreach ( $this->columns as $column => $data ) :
						$this->output_column_heading( $column );
					endforeach;
					?>
				</tr>
			</thead>
			<tbody>
				<?php
				if ( empty( $this->data ) ) :
					$this->output_blank_row();
				else :
					foreach ( $this->data as $row => $data ) :
						$this->output_row( $row );
					endforeach;
				endif;
				?>
			</tbody>
			<?php $this->output_footer(); ?>
		</table>
		<?php
	}

	/**
	 * Outputs the column heading.
	 *
	 * @since 1.5.0
	 *
	 * @param string $column The column key.
	 */
	public function output_column_heading( $column ) {
		$data = $this->columns[ $column ];

		printf(
			'<th class="%1$s">%2$s</th>',
			esc_attr( $column ),
			esc_html( $data['label'] )
		);
	}

	/**
	 * Outputs the blank row.
	 *
	 * @since 1.6.0
	 */
	public function output_blank_row() {}

	/**
	 * Outputs the row for the specified index.
	 *
	 * @since 1.5.0
	 *
	 * @param mixed $row The row index.
	 */
	public function output_row( $row ) {
		$attrs   = $this->get_row_attrs( $row );
		$columns = $this->get_row_columns( $row );

		echo '<tr ' . wc_od_get_attrs_html( $attrs ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		foreach ( $columns as $column => $data ) :
			$this->output_column( $row, $column );
		endforeach;

		echo '</tr>';
	}

	/**
	 * Outputs the row actions.
	 *
	 * @since 1.7.0
	 *
	 * @param mixed $row The row index.
	 */
	public function output_row_actions( $row ) {
		$actions = $this->get_row_actions( $row );

		if ( empty( $actions ) ) {
			return;
		}

		$action_strings = array();

		foreach ( $actions as $key => $action ) {
			$action_strings[] = sprintf(
				'<a class="%1$s" href="%2$s">%3$s</a>',
				esc_attr( $key ),
				esc_url( $action['url'] ),
				esc_html( $action['label'] )
			);
		}

		echo '<div class="row-actions">';
		echo join( ' | ', $action_strings ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</div>';
	}

	/**
	 * Outputs the content for the specified row and column.
	 *
	 * @since 1.5.0
	 *
	 * @param mixed  $row    The row index.
	 * @param string $column The column key.
	 */
	public function output_column( $row, $column ) {
		$attrs = $this->get_column_attrs( $column );
		$data  = $this->get_row_data( $row );

		echo '<td ' . wc_od_get_attrs_html( $attrs ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		if ( ! empty( $data ) ) :
			$method_name = 'output_column_' . $column;

			if ( method_exists( $this, $method_name ) ) :
				call_user_func( array( $this, $method_name ), $row, $data );
			elseif ( isset( $data[ $column ] ) ) :
				echo esc_html( $data[ $column ] );
			endif;
		endif;

		echo '</td>';
	}

	/**
	 * Outputs the column 'sort'.
	 *
	 * @since 1.5.0
	 *
	 * @param mixed $row The row index.
	 */
	public function output_column_sort( $row ) {
		printf(
			'<input type="hidden" name="%1$s[%2$s][order]" value="%2$s" />',
			esc_attr( $this->id ),
			esc_attr( $row )
		);
	}

	/**
	 * Outputs the table footer.
	 *
	 * @since 1.5.0
	 */
	public function output_footer() {}

	/**
	 * Sanitize the field value.
	 *
	 * @since 1.5.0
	 *
	 * @param mixed $value The field value.
	 * @return mixed
	 */
	public function sanitize_field( $value ) {
		$field_value     = ( isset( $this->field['value'] ) ? $this->field['value'] : array() );
		$sanitized_value = array();

		// The table is empty.
		if ( ! is_array( $value ) ) {
			$value = array();
		}

		foreach ( $value as $key => $data ) {
			if ( $this->sortable ) {
				unset( $data['order'] );
			}

			$sanitized_value[ $key ] = array_merge( $field_value[ $key ], $data );
		}

		return $sanitized_value;
	}
}
