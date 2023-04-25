<?php
/**
 * List table.
 *
 * @package WC_OD/Admin/List_Tables
 * @since   1.4.0
 */

defined( 'ABSPATH' ) || exit;

if ( class_exists( 'WC_OD_Admin_List_Table', false ) ) {
	return;
}

/**
 * WC_OD_Admin_List_Table class.
 */
abstract class WC_OD_Admin_List_Table {

	/**
	 * Post type.
	 *
	 * @var string
	 */
	protected $list_table_type = '';

	/**
	 * The list table filters.
	 *
	 * @var array
	 */
	protected $filters = array();

	/**
	 * Bulk actions.
	 *
	 * @var array
	 */
	protected $actions = array();

	/**
	 * Constructor.
	 *
	 * @since 1.4.0
	 * @since 2.4.0 Accepts an array of arguments.
	 *
	 * @param array $args {
	 *     Array of arguments.
	 *
	 *     @type array $actions An array of bulk actions in pairs [key => label]. Default: empty.
	 */
	public function __construct( $args = array() ) {
		$this->actions = ( ! empty( $args['actions'] ) ? $args['actions'] : array() );

		$this->load_filters();

		if ( $this->list_table_type ) {
			add_action( 'restrict_manage_posts', array( $this, 'render_filters' ), 5 );
			add_filter( 'request', array( $this, 'query_filters' ) );
		}

		$screen_id = wc_od_get_current_screen_id();

		if ( $screen_id && ! empty( $this->actions ) ) {
			add_action( 'admin_notices', array( $this, 'bulk_admin_notices' ) );
			add_filter( 'bulk_actions-' . $screen_id, array( $this, 'define_bulk_actions' ), 20 );
			add_filter( 'handle_bulk_actions-' . $screen_id, array( $this, 'handle_bulk_actions' ), 20, 3 );
		}
	}

	/**
	 * Loads the custom filters.
	 *
	 * @since 1.4.0
	 */
	public function load_filters() {}

	/**
	 * Render any custom filters and search inputs for the list table.
	 *
	 * @since 1.4.0
	 */
	public function render_filters() {
		foreach ( $this->filters as $filter ) {
			$callable = array( $this, 'render_' . $filter['type'] . '_filter' );

			if ( is_callable( $callable ) ) {
				call_user_func( $callable, $filter );
			}
		}
	}

	/**
	 * Handle any custom filters.
	 *
	 * @since 1.4.0
	 *
	 * @param array $query_vars Query vars.
	 * @return array
	 */
	public function query_filters( $query_vars ) {
		$query_filters = array();

		foreach ( $this->filters as $filter ) {
			$callable = array( $this, 'query_' . $filter['type'] . '_filter' );

			if ( is_callable( $callable ) ) {
				$query_filter = call_user_func( $callable, $filter );

				if ( $query_filter ) {
					$query_filters[] = $query_filter;
				}
			}
		}

		if ( ! empty( $query_filters ) ) {
			// phpcs:disable WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			if ( isset( $query_vars['meta_query'] ) && is_array( $query_vars['meta_query'] ) ) {
				$query_vars['meta_query'] = array_merge( $query_vars['meta_query'], $query_filters );
			} else {
				$query_vars['meta_query'] = $query_filters;
			}
			// phpcs:enable WordPress.DB.SlowDBQuery.slow_db_query_meta_query
		}

		return $query_vars;
	}

	/**
	 * Prints a date filter in the list table.
	 *
	 * @since 1.4.0
	 *
	 * @param array $filter The filter data.
	 */
	public function render_date_filter( $filter ) {
		global $wp_locale;

		$months       = $this->get_date_filter_months( $filter['id'] );
		$months_count = count( $months );

		if ( ! $months_count ) {
			return;
		}

		/**
		 * Filters the additional choices for the date filter.
		 *
		 * @since 1.4.0
		 *
		 * @param array $choices The additional choices.
		 * @param array $filter  The filter data.
		 */
		$additional_choices = apply_filters(
			'wc_od_admin_date_filter_additional_choices',
			array(
				'empty'        => array(
					'value' => 0,
					'label' => $filter['empty'],
				),
				'today'        => array(
					'value' => wc_od_get_local_date( false, 'Ymd' ),
					'label' => _x( 'Today', 'list table date filter', 'woocommerce-order-delivery' ),
				),
				'tomorrow'     => array(
					'value' => date( 'Ymd', strtotime( '1 day', wc_od_get_local_date( true ) ) ),
					'label' => _x( 'Tomorrow', 'list table date filter', 'woocommerce-order-delivery' ),
				),
				'next_week'    => array(
					'value' => 'next_week',
					'label' => _x( 'Next week', 'list table date filter', 'woocommerce-order-delivery' ),
				),
				'next_2_weeks' => array(
					'value' => 'next_2_weeks',
					'label' => _x( 'Next 2 weeks', 'list table date filter', 'woocommerce-order-delivery' ),
				),
			),
			$filter
		);

		$selected = ( isset( $_GET[ $filter['id'] ] ) ? wc_clean( wp_unslash( $_GET[ $filter['id'] ] ) ) : 0 ); // phpcs:ignore CSRF ok, sanitization ok.
		?>
		<label for="filter-by-<?php echo esc_attr( $filter['id'] ); ?>" class="screen-reader-text"><?php echo esc_html( $filter['label'] ); ?></label>
		<select name="<?php echo esc_attr( $filter['id'] ); ?>" id="<?php echo esc_attr( $filter['id'] ); ?>">
			<?php
			foreach ( $additional_choices as $choice ) :
				printf(
					'<option value="%1$s"%2$s>%3$s</option>',
					esc_attr( $choice['value'] ),
					selected( $selected, $choice['value'], false ),
					esc_html( $choice['label'] )
				);
			endforeach;

			foreach ( $months as $option ) :
				if ( 0 == $option->year ) :
					continue;
				endif;

				$month = zeroise( $option->month, 2 );
				$year  = $option->year;

				printf(
					'<option value="%1$s"%2$s>%3$s</option>',
					esc_attr( $year . $month ),
					selected( $selected, $year . $month, false ),
					/* translators: 1: month name, 2: 4-digit year */
					esc_html( sprintf( _x( '%1$s %2$d', 'filter orders by date range', 'woocommerce-order-delivery' ), $wp_locale->get_month( $month ), $year ) )
				);
			endforeach;
			?>
		</select>
		<?php
	}

	/**
	 * Gets the query to filter by date.
	 *
	 * @param array $filter The filter data.
	 * @return array|bool The query parameters. False otherwise.
	 */
	public function query_date_filter( $filter ) {
		if ( empty( $_GET[ $filter['id'] ] ) ) { // phpcs:ignore CSRF ok.
			return false;
		}

		$meta_query = false;
		$value      = wc_clean( wp_unslash( $_GET[ $filter['id'] ] ) ); // phpcs:ignore CSRF ok, sanitization ok.

		if ( is_numeric( $value ) ) {
			if ( 8 === strlen( $value ) ) { // Filter by day.
				$date = sprintf(
					'%1$s-%2$s-%3$s',
					substr( $value, 0, 4 ),
					substr( $value, 4, 2 ),
					substr( $value, 6, 2 )
				);

				$meta_query = array(
					'key'     => "_{$filter['id']}",
					'value'   => $date,
					'compare' => '=',
				);
			} elseif ( 6 === strlen( $value ) ) { // Filter by month.
				$start = sprintf( '%1$s-%2$s-01', substr( $value, 0, 4 ), substr( $value, 4, 2 ) );
				$end   = date( 'Y-m-t', strtotime( $start ) );

				$meta_query = array(
					'key'     => "_{$filter['id']}",
					'value'   => array( $start, $end ),
					'compare' => 'BETWEEN',
					'type'    => 'DATE',
				);
			}
		} elseif ( in_array( $value, array( 'next_week', 'next_2_weeks' ), true ) ) {
			$weeks = ( 'next_2_weeks' === $value ? 2 : 1 );
			$start = wc_od_get_local_date( false );
			$end   = date( 'Y-m-d', strtotime( "+ {$weeks} weeks", strtotime( $start ) ) );

			$meta_query = array(
				'key'     => "_{$filter['id']}",
				'value'   => array( $start, $end ),
				'compare' => 'BETWEEN',
				'type'    => 'DATE',
			);
		}

		/**
		 * Filters the query to filter by date.
		 *
		 * @since 1.5.5
		 *
		 * @param mixed $query The query parameters.
		 * @param array $filter The filter data.
		 */
		return apply_filters( 'wc_od_admin_query_date_filter', $meta_query, $filter );
	}

	/**
	 * Gets the available months for a date filter.
	 *
	 * @since 1.4.0
	 *
	 * @global wpdb $wpdb The WordPress Database Access Abstraction Object.
	 *
	 * @param string $key The meta key.
	 * @return array
	 */
	public function get_date_filter_months( $key ) {
		global $wpdb;

		$extra_checks = '';

		// Filter by status.
		if ( isset( $_GET['post_status'] ) && 'all' !== $_GET['post_status'] ) { // phpcs:ignore CSRF ok.
			$extra_checks .= $wpdb->prepare( " AND {$wpdb->posts}.post_status = %s", wc_clean( wp_unslash( $_GET['post_status'] ) ) ); // phpcs:ignore CSRF ok, sanitization ok.
		} else {
			$extra_checks .= " AND {$wpdb->posts}.post_status != 'trash'";
		}

		$query = $wpdb->prepare(
			"
			SELECT DISTINCT YEAR( meta_value ) AS year, MONTH( meta_value ) AS month
			FROM $wpdb->postmeta
			INNER JOIN $wpdb->posts ON $wpdb->posts.id = $wpdb->postmeta.post_id
			WHERE $wpdb->posts.post_type = %s AND meta_key = %s
			$extra_checks
			ORDER BY meta_value DESC
		",
			$this->list_table_type,
			"_$key"
		);

		return $wpdb->get_results( $query );
	}

	/**
	 * Registers bulk actions.
	 *
	 * @since 1.4.0
	 *
	 * @param array $actions Existing actions.
	 * @return array
	 */
	public function define_bulk_actions( $actions ) {
		return array_merge( $actions, $this->actions );
	}

	/**
	 * Handle bulk actions.
	 *
	 * @since 1.4.0
	 *
	 * @param string $redirect_to URL to redirect to.
	 * @param string $action      Action name.
	 * @param array  $ids         List of ids.
	 * @return string
	 */
	public function handle_bulk_actions( $redirect_to, $action, $ids ) {
		if ( empty( $ids ) || ! array_key_exists( $action, $this->actions ) ) {
			return $redirect_to;
		}

		$callable = array( $this, 'handle_bulk_action_' . $action );

		if ( is_callable( $callable ) ) {
			$ids         = array_map( 'absint', $ids );
			$redirect_to = call_user_func( $callable, $redirect_to, $ids );
		}

		return esc_url_raw( $redirect_to );
	}

	/**
	 * Show bulk notices.
	 *
	 * @since 1.4.0
	 */
	public function bulk_admin_notices() {
		$action  = ( isset( $_REQUEST['bulk_action'] ) ? wc_clean( wp_unslash( $_REQUEST['bulk_action'] ) ) : '' ); // phpcs:ignore WordPress.Security.NonceVerification
		$changed = ( isset( $_REQUEST['changed'] ) ? absint( $_REQUEST['changed'] ) : 0 ); // phpcs:ignore WordPress.Security.NonceVerification

		$message = $this->get_bulk_action_message( $action, $changed );

		if ( $message ) {
			printf( '<div class="updated"><p>%s</p></div>', esc_html( $message ) );
		}
	}

	/**
	 * Gets the message for specified bulk action.
	 *
	 * @since 2.4.0
	 *
	 * @param string $action  Action name.
	 * @param int    $changed The number of changed items.
	 * @return string
	 */
	protected function get_bulk_action_message( $action, $changed ) {
		return '';
	}
}
