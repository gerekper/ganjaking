<?php
/**
 * Field: table.
 *
 * @package WC_Instagram/Admin/Fields
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( class_exists( 'WC_Instagram_Admin_Field_Table', false ) ) {
	return;
}

/**
 * WC_Instagram_Admin_Field_Table class.
 */
abstract class WC_Instagram_Admin_Field_Table {

	/**
	 * The field ID.
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
	 * @since 3.0.0
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
	 * @since 3.0.0
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
		 * @since 3.0.0
		 *
		 * @param array  $columns The table columns.
		 * @param string $id      The setting ID.
		 */
		$this->columns = apply_filters( 'wc_instagram_field_table_columns', $columns, $this->id );
	}

	/**
	 * Sets the table data.
	 *
	 * @since 3.0.0
	 *
	 * @param array $data The table data.
	 */
	public function set_data( $data ) {
		/**
		 * Filters the table data.
		 *
		 * @since 3.0.0
		 *
		 * @param array  $data The table data.
		 * @param string $id   The setting ID.
		 */
		$this->data = apply_filters( 'wc_instagram_field_table_data', $data, $this->id );
	}

	/**
	 * Gets the classes used in the table HTML tag.
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	public function get_table_class() {
		$class = array(
			$this->id,
			'wc-instagram-field-table',
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
	 * @since 3.0.0
	 */
	public function output() {
		?>
		<table class="<?php echo esc_attr( join( ' ', $this->get_table_class() ) ); ?>">
			<thead>
				<tr>
					<?php
					foreach ( $this->columns as $key => $column ) :
						$this->output_column_heading( $key );
					endforeach;
					?>
				</tr>
			</thead>
			<tbody>
				<?php
				if ( empty( $this->data ) ) :
					$this->output_blank_row();
				else :
					foreach ( $this->data as $index => $data ) :
						$this->output_row( $index );
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
	 * @since 3.0.0
	 *
	 * @param string $key The column key.
	 */
	public function output_column_heading( $key ) {
		$column = $this->columns[ $key ];

		printf(
			'<th class="%1$s">%2$s</th>',
			esc_attr( $key ),
			esc_html( $column['label'] )
		);
	}

	/**
	 * Outputs the blank row.
	 *
	 * @since 3.0.0
	 */
	public function output_blank_row() {}

	/**
	 * Outputs the row for the specified index.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $index The row index.
	 */
	public function output_row( $index ) {
		echo '<tr>';

		foreach ( $this->columns as $key => $column ) :
			$this->output_column( $index, $key );
		endforeach;

		echo '</tr>';
	}

	/**
	 * Outputs the content for the specified row and column.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed  $row    The row index.
	 * @param string $column The column key.
	 */
	public function output_column( $row, $column ) {
		$method_name = 'output_column_' . $column;
		$column_data = $this->columns[ $column ];
		$attrs       = '';

		if ( isset( $column_data['width'] ) ) {
			$attrs = ' width="' . esc_attr( $column_data['width'] ) . '"';
		}

		printf(
			'<td class="%1$s" %2$s>',
			esc_attr( $column ),
			$attrs // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		);

		if ( isset( $this->data[ $row ] ) ) :

			if ( method_exists( $this, $method_name ) ) :
				call_user_func( array( $this, $method_name ), $row, $this->data[ $row ] );
			elseif ( isset( $this->data[ $row ][ $column ] ) ) :
				echo esc_html( $this->data[ $row ][ $column ] );
			endif;

		endif;

		echo '</td>';
	}

	/**
	 * Outputs the column 'sort'.
	 *
	 * @since 3.0.0
	 *
	 * @param int $row The row index.
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
	 * @since 3.0.0
	 */
	public function output_footer() {}

	/**
	 * Sanitize the field value.
	 *
	 * @since 3.0.0
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

	/**
	 * Outputs the content for a boolean column.
	 *
	 * @since 3.0.0
	 *
	 * @param int   $row   The row index.
	 * @param mixed $value The boolean value.
	 */
	protected function output_column_boolean( $row, $value ) {
		echo esc_html( wc_instagram_bool_to_string( $value ) );
	}
}
