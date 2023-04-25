<?php

namespace WPMailSMTP\Pro\Emails\Logs\Reports;

use WPMailSMTP\Admin\Area;
use WPMailSMTP\Helpers\Helpers;

if ( ! class_exists( 'WP_List_Table', false ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Class Table that displays the list of email stats.
 *
 * @since 3.0.0
 */
class Table extends \WP_List_Table {

	/**
	 * Emails stats report object.
	 *
	 * @since 3.0.0
	 *
	 * @var Report
	 */
	protected $report;

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 *
	 * @param Report $report Emails stats report object.
	 */
	public function __construct( $report ) {

		$this->report = $report;

		// Set parent defaults.
		parent::__construct(
			[
				'singular' => 'report-item',
				'plural'   => 'report-items',
				'screen'   => 'wp-mail-smtp_page_wp-mail-smtp-reports',
			]
		);
	}

	/**
	 * Define the table columns.
	 *
	 * @since 3.0.0
	 *
	 * @return array Associative array of slug=>name columns data.
	 */
	public function get_columns() {

		$columns = [];

		$columns['subject']     = esc_html__( 'Subject', 'wp-mail-smtp-pro' );
		$columns['total_count'] = esc_html__( 'Total', 'wp-mail-smtp-pro' );

		if ( Helpers::mailer_without_send_confirmation() ) {
			$columns['sent_count'] = esc_html__( 'Sent', 'wp-mail-smtp-pro' );
		} else {
			$columns['confirmed_count']   = esc_html__( 'Confirmed', 'wp-mail-smtp-pro' );
			$columns['unconfirmed_count'] = esc_html__( 'Unconfirmed', 'wp-mail-smtp-pro' );
		}

		$columns['unsent_count'] = esc_html__( 'Failed', 'wp-mail-smtp-pro' );

		if ( wp_mail_smtp()->get_pro()->get_logs()->is_enabled_open_email_tracking() ) {
			$columns['open_count'] = esc_html__( 'Open Count', 'wp-mail-smtp-pro' );
		}

		if ( wp_mail_smtp()->get_pro()->get_logs()->is_enabled_click_link_tracking() ) {
			$columns['click_count'] = esc_html__( 'Click Count', 'wp-mail-smtp-pro' );
		}

		$columns['graph'] = esc_html__( 'Graph', 'wp-mail-smtp-pro' );

		return $columns;
	}

	/**
	 * Define columns that are sortable.
	 *
	 * @since 3.0.0
	 *
	 * @return array List of columns that should be sortable.
	 */
	public function get_sortable_columns() {

		return [
			'subject'           => [ 'subject', false ],
			'total_count'       => [ 'total', false ],
			'sent_count'        => [ 'sent', false ],
			'confirmed_count'   => [ 'delivered', false ],
			'unconfirmed_count' => [ 'sent', false ],
			'unsent_count'      => [ 'unsent', false ],
			'open_count'        => [ 'open_count', false ],
			'click_count'       => [ 'click_count', false ],
		];
	}

	/**
	 * Display Email subject.
	 *
	 * @since 3.0.0
	 *
	 * @param array $item Stats row.
	 *
	 * @return string
	 */
	public function column_subject( $item ) {

		return sprintf(
			'<a href="#" class="subject-toggle-single-stats" data-subject="%1$s">%2$s</a>',
			esc_attr( $item['subject'] ),
			esc_html( $item['subject'] )
		);
	}

	/**
	 * Display emails total count.
	 *
	 * @since 3.0.0
	 *
	 * @param array $item Stats row.
	 *
	 * @return string
	 */
	public function column_total_count( $item ) {

		return $this->report->get_total_count( $item );
	}

	/**
	 * Display emails sent count.
	 *
	 * @since 3.0.0
	 *
	 * @param array $item Stats row.
	 *
	 * @return string
	 */
	public function column_sent_count( $item ) {

		return sprintf(
			'%1$d <span>(%2$d%%)</span>',
			$this->report->get_sent_count( $item ),
			$this->report->get_sent_percent_count( $item )
		);
	}

	/**
	 * Display emails confirmed count.
	 *
	 * @since 3.0.0
	 *
	 * @param array $item Stats row.
	 *
	 * @return string
	 */
	public function column_confirmed_count( $item ) {

		return sprintf(
			'%1$d <span>(%2$d%%)</span>',
			$this->report->get_confirmed_count( $item ),
			$this->report->get_confirmed_percent_count( $item )
		);
	}

	/**
	 * Display emails unconfirmed count.
	 *
	 * @since 3.0.0
	 *
	 * @param array $item Stats row.
	 *
	 * @return string
	 */
	public function column_unconfirmed_count( $item ) {

		return sprintf(
			'%1$d <span>(%2$d%%)</span>',
			$this->report->get_unconfirmed_count( $item ),
			$this->report->get_unconfirmed_percent_count( $item )
		);
	}

	/**
	 * Display emails failed count.
	 *
	 * @since 3.0.0
	 *
	 * @param array $item Stats row.
	 *
	 * @return string
	 */
	public function column_unsent_count( $item ) {

		return sprintf(
			'%1$d <span>(%2$d%%)</span>',
			$this->report->get_unsent_count( $item ),
			$this->report->get_unsent_percent_count( $item )
		);
	}

	/**
	 * Display emails open count.
	 *
	 * @since 3.0.0
	 *
	 * @param array $item Stats row.
	 *
	 * @return string
	 */
	public function column_open_count( $item ) {

		return sprintf(
			'%1$d <span>(%2$d%%)</span>',
			$this->report->get_open_count( $item ),
			$this->report->get_open_percent_count( $item )
		);
	}

	/**
	 * Display emails click link count.
	 *
	 * @since 3.0.0
	 *
	 * @param array $item Stats row.
	 *
	 * @return string
	 */
	public function column_click_count( $item ) {

		return sprintf(
			'%1$d <span>(%2$d%%)</span>',
			$this->report->get_click_count( $item ),
			$this->report->get_click_percent_count( $item )
		);
	}

	/**
	 * Display graph icon.
	 * Used for enable/disable single stat.
	 *
	 * @since 3.0.0
	 *
	 * @param array $item Stats row.
	 *
	 * @return string
	 */
	public function column_graph( $item ) {

		return sprintf(
			'<div class="wp-mail-smtp-toggle-single-stats-btn-container"><button type="button" class="js-wp-mail-smtp-toggle-single-stats" data-subject="%s"><i class="dashicons dashicons-chart-line"></i></button></div>',
			esc_attr( $item['subject'] )
		);
	}

	/**
	 * Get the data.
	 *
	 * @since 3.0.0
	 */
	public function prepare_items() {

		$items = $this->report->get_stats_by_subject();

		// Pagination options.
		$per_page = $this->get_items_per_page( 'wp_mail_smtp_report_items_per_page' );
		$page     = isset( $_GET['paged'] ) && $_GET['paged'] > 0 ? intval( $_GET['paged'] ) : 1; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$offset   = $per_page * ( $page - 1 );

		$this->items = array_slice( $items, $offset, $per_page );

		$this->set_pagination_args(
			[
				'total_items' => count( $items ),
				'per_page'    => $per_page,
			]
		);
	}

	/**
	 * Whether the table has items to display or not.
	 *
	 * @since 3.0.0
	 *
	 * @return bool
	 */
	public function has_items() {

		return count( $this->items ) > 0;
	}

	/**
	 * Message to be displayed when there are no items.
	 *
	 * @since 3.0.0
	 */
	public function no_items() {

		esc_html_e( 'No email reports are available.', 'wp-mail-smtp-pro' );
	}

	/**
	 * Get the name of the primary column.
	 * Important for the mobile view.
	 *
	 * @since 3.0.0
	 *
	 * @return string The name of the primary column.
	 */
	protected function get_primary_column_name() {

		return 'subject';
	}

	/**
	 * Generates the table navigation above the table.
	 *
	 * @since 3.0.0
	 *
	 * @param string $which Navigation location.
	 */
	protected function display_tablenav( $which ) {

		?>
		<div class="tablenav <?php echo esc_attr( $which ); ?>">
			<?php
			if ( $which === 'top' ) {

				// Display filters.
				$this->filters();

				// Display search box.
				$this->search_box(
					esc_html__( 'Search Emails', 'wp-mail-smtp-pro' ),
					Area::SLUG . '-reports-search-input'
				);
			} else {
				$this->pagination( $which );
			}
			?>
		</div>
		<?php
	}

	/**
	 * Filters HTML.
	 *
	 * @since 3.0.0
	 */
	protected function filters() {

		$timespan = '';
		$date     = '';

		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if ( ! empty( $_REQUEST['timespan'] ) ) {
			$timespan = sanitize_text_field( wp_unslash( $_REQUEST['timespan'] ) );
		}

		if ( ! empty( $_REQUEST['date'] ) ) {
			$date = sanitize_text_field( wp_unslash( $_REQUEST['date'] ) );
		}
		// phpcs:enable

		$timespans = [
			'7'      => esc_html__( 'Last 7 days', 'wp-mail-smtp-pro' ),
			'14'     => esc_html__( 'Last 14 days', 'wp-mail-smtp-pro' ),
			'30'     => esc_html__( 'Last 30 days', 'wp-mail-smtp-pro' ),
			'custom' => esc_html__( 'Custom Date Range', 'wp-mail-smtp-pro' ),
		];

		?>
		<div class="alignleft actions wp-mail-smtp-filter-date">
			<select name="timespan" class="wp-mail-smtp-filter-date__control">
				<?php
				foreach ( $timespans as $value => $label ) {
					printf(
						'<option value="%s" %s>%s</option>',
						esc_attr( $value ),
						selected( $timespan, $value, false ),
						esc_html( $label )
					);
				}
				?>
			</select>

			<input type="text" name="date" class="regular-text wp-mail-smtp-filter-date-selector wp-mail-smtp-filter-date__control"
						 placeholder="<?php esc_attr_e( 'Select a date range', 'wp-mail-smtp-pro' ); ?>"
						 value="<?php echo esc_attr( $date ); ?>">

			<button type="submit" class="button wp-mail-smtp-filter-date__btn">
				<?php esc_html_e( 'Filter', 'wp-mail-smtp-pro' ); ?>
			</button>
		</div>
		<?php
	}
}
