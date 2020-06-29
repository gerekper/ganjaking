<?php
/**
 * List table.
 *
 * @package WC_Store_Credit/Admin/List_Tables
 * @since   3.1.0
 */

defined( 'ABSPATH' ) || exit;

if ( class_exists( 'WC_Store_Credit_Admin_List_Table', false ) ) {
	return;
}

/**
 * Class WC_Store_Credit_Admin_list_Table.
 */
class WC_Store_Credit_Admin_List_Table extends WC_Admin_List_Table {

	/**
	 * The list table filters.
	 *
	 * @var array
	 */
	protected $filters;

	/**
	 * Gets the custom filters.
	 *
	 * @since 3.1.0
	 *
	 * @return array
	 */
	public function get_filters() {
		if ( is_null( $this->filters ) ) {
			$this->register_filters();
		}

		return $this->filters;
	}

	/**
	 * Registers the custom filters.
	 *
	 * @since 3.1.0
	 */
	protected function register_filters() {
		if ( is_null( $this->filters ) ) {
			$this->filters = array();
		}

		if ( ! $this->list_table_type ) {
			return;
		}

		/**
		 * Filters the custom filters for the list table.
		 *
		 * The dynamic portion of the hook name, `$list_table_type`, refers to the post type of the list table.
		 *
		 * @since 3.1.0
		 *
		 * @param array $filters The custom filters.
		 */
		$this->filters = apply_filters( "wc_store_credit_admin_{$this->list_table_type}_filters", $this->filters );
	}

	/**
	 * Render any custom filters and search inputs for the list table.
	 *
	 * @since 3.1.0
	 */
	public function render_filters() {
		$filters = $this->get_filters();

		foreach ( $filters as $filter ) {
			$callback = $this->get_render_filter_callback( $filter );

			if ( is_callable( $callback ) ) {
				call_user_func( $callback, $filter );
			}
		}
	}

	/**
	 * Gets the callback used to render the filter.
	 *
	 * @since 3.1.0
	 *
	 * @param array $filter The filter data.
	 * @return callable
	 */
	protected function get_render_filter_callback( $filter ) {
		$callback = '';

		if ( is_callable( array( $this, "render_{$filter['id']}_filter" ) ) ) {
			$callback = array( $this, "render_{$filter['id']}_filter" );
		} elseif ( ! empty( $filter['type'] ) && is_callable( array( $this, "render_{$filter['type']}_filter" ) ) ) {
			$callback = array( $this, "render_{$filter['type']}_filter" );
		}

		/**
		 * Filters the render filter callback.
		 *
		 * @since 3.1.0
		 *
		 * @param callable $callback The callback used to render the filter.
		 * @param array    $filter   The filter data.
		 */
		return apply_filters( 'wc_store_credit_list_table_render_filter_callback', $callback, $filter );
	}

	/**
	 * Handles any custom filters.
	 *
	 * @since 3.1.0
	 *
	 * @param array $query_vars Query vars.
	 * @return array
	 */
	public function query_filters( $query_vars ) {
		$filters = $this->get_filters();

		foreach ( $filters as $filter ) {
			$query_filter = null;
			$callback     = $this->get_query_filter_callback( $filter );

			if ( is_callable( $callback ) ) {
				$query_vars = call_user_func( $callback, $filter, $query_vars );
			}
		}

		return $query_vars;
	}

	/**
	 * Gets the callback used to query the filter.
	 *
	 * @since 3.1.0
	 *
	 * @param array $filter The filter data.
	 * @return callable
	 */
	protected function get_query_filter_callback( $filter ) {
		$callback = '';

		if ( is_callable( array( $this, "query_{$filter['id']}_filter" ) ) ) {
			$callback = array( $this, "query_{$filter['id']}_filter" );
		} elseif ( ! empty( $filter['type'] ) && is_callable( array( $this, "query_{$filter['type']}_filter" ) ) ) {
			$callback = array( $this, "query_{$filter['type']}_filter" );
		}

		/**
		 * Filters the query filter callback.
		 *
		 * @since 3.1.0
		 *
		 * @param callable $callback The callback used to query the filter.
		 * @param array    $filter   The filter data.
		 */
		return apply_filters( 'wc_store_credit_list_table_query_filter_callback', $callback, $filter );
	}

	/**
	 * Renders a 'select' filter field.
	 *
	 * @since 3.1.0
	 *
	 * @param array $filter Filter data.
	 */
	protected function render_select_filter( $filter ) {
		$filter = wp_parse_args(
			$filter,
			array(
				'class'             => '',
				'style'             => '',
				'value'             => ( ! empty( $_GET[ $filter['id'] ] ) ? wc_clean( wp_unslash( $_GET[ $filter['id'] ] ) ) : '' ), // phpcs:ignore WordPress.Security.NonceVerification
				'options'           => array(),
				'custom_attributes' => array(),
			)
		);

		$field_attributes = array_merge(
			(array) $filter['custom_attributes'],
			array_intersect_key( $filter, array_flip( array( 'id', 'class', 'style' ) ) ),
			array( 'name' => $filter['id'] )
		);
		?>
		<select <?php echo wc_implode_html_attributes( $field_attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<?php
			foreach ( $filter['options'] as $key => $label ) :
				printf(
					'<option value="%1$s"%2$s>%3$s</option>',
					esc_attr( $key ),
					selected( $key, $filter['value'], false ),
					esc_html( $label )
				);
			endforeach;
			?>
		</select>
		<?php
	}
}
