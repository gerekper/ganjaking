<?php

namespace WPMailSMTP\Pro\Emails\Logs\Admin;

use WPMailSMTP\Admin\Area;

/**
 * Class ArchivePage displays a Email Log page content.
 *
 * @since 1.5.0
 */
class ArchivePage extends PageAbstract {

	/**
	 * Email logs list table.
	 *
	 * @since 2.8.0
	 *
	 * @var Table
	 */
	protected $table = null;

	/**
	 * ArchivePage class constructor.
	 *
	 * @since 2.8.0
	 */
	public function __construct() {
		parent::__construct();

		// Remove unnecessary $_GET parameters and prevent url duplications in _wp_http_referer input.
		$this->remove_get_parameters();
	}

	/**
	 * Link label of a tab.
	 *
	 * @since 1.5.0
	 *
	 * @return string
	 */
	public function get_label() {
		return esc_html__( 'Email Log', 'wp-mail-smtp-pro' );
	}

	/**
	 * Tab content.
	 *
	 * @since 1.5.0
	 * @since 1.7.0 Added search functionality.
	 */
	public function display() {

		$page_url = wp_mail_smtp()->get_admin()->get_admin_page_url( Area::SLUG . '-logs' );
		?>

		<div class="wp-mail-smtp-page-title">
			<span class="page-title">
				<?php echo esc_html( $this->get_label() ); ?>
			</span>

			<?php
			/**
			 * Fires after email logs archive page title.
			 *
			 * @since 2.9.0
			 */
			do_action( 'wp_mail_smtp_pro_emails_logs_admin_archive_page_display_header' );
			?>
		</div>

		<h1 class="screen-reader-text">
			<?php echo esc_html( $this->get_label() ); ?>
		</h1>

		<div class="wp-mail-smtp-page-content">
			<?php
			/**
			 * Fires before email logs archive page content.
			 *
			 * @since 2.3.1
			 */
			do_action( 'wp_mail_smtp_admin_pages_before_content' );
			?>
			<form action="<?php echo esc_url( $page_url ); ?>" method="get">
				<input type="hidden" name="page" value="<?php echo esc_attr( Area::SLUG . '-logs' ); ?>" />

				<?php
				if ( wp_mail_smtp()->pro->get_logs()->is_enabled() ) {
					if ( ! wp_mail_smtp()->pro->get_logs()->is_valid_db() ) {
						$this->display_logging_not_installed();
					} else {
						$table = $this->get_table();
						$table->prepare_items();

						// State of status filter for submission with other filters.
						if ( $table->get_filtered_status() !== false ) {
							printf( '<input type="hidden" name="status" value="%s">', esc_attr( $table->get_filtered_status() ) );
						}

						if ( $this->get_filters_html() ) {
							?>
							<div id="wp-mail-smtp-reset-filter">
								<?php
								$status = $table->get_filtered_status();
								echo wp_kses(
									sprintf( /* translators: %1$s - number of email logs found; %2$s - filtered status. */
										_n(
											'Found <strong>%1$s %2$s email log</strong>',
											'Found <strong>%1$s %2$s email logs</strong>',
											absint( $table->get_pagination_arg( 'total_items' ) ),
											'wp-mail-smtp-pro'
										),
										absint( $table->get_pagination_arg( 'total_items' ) ),
										$status !== false && isset( $table->get_statuses()[ $status ] ) ? $table->get_statuses()[ $status ] : ''
									),
									[
										'strong' => [],
									]
								);
								?>

								<?php foreach ( $this->get_filters_html() as $id => $html ) : ?>
									<?php
									echo wp_kses(
										$html,
										[ 'em' => [] ]
									);
									?>
									<i class="reset dashicons dashicons-dismiss" data-scope="<?php echo esc_attr( $id ); ?>"></i>
								<?php endforeach; ?>
							</div>
							<?php
						}

						$table->search_box(
							esc_html__( 'Search Emails', 'wp-mail-smtp-pro' ),
							Area::SLUG . '-logs-archive-search-input'
						);

						$table->views();
						$table->display();
					}
				} else {
					$this->display_logging_disabled();
				}
				?>
			</form>
		</div>

		<?php
	}

	/**
	 * Get email logs list table.
	 *
	 * @since 2.8.0
	 *
	 * @return Table
	 */
	public function get_table() {

		if ( $this->table === null ) {
			$this->table = new Table();
		}

		return $this->table;
	}

	/**
	 * Return an array with information (HTML and id) for each filter for this current view.
	 *
	 * @since 2.8.0
	 *
	 * @return array
	 */
	public function get_filters_html() {

		$filters = [
			'.search-box'               => $this->get_filter_search_html(),
			'.wp-mail-smtp-filter-date' => $this->get_filter_date_html(),
		];

		return array_filter( $filters );
	}

	/**
	 * Return HTML with information about the search filter.
	 *
	 * @since 2.8.0
	 *
	 * @return string
	 */
	protected function get_filter_search_html() {

		$table        = $this->get_table();
		$search_parts = $table->get_filtered_search_parts();

		if ( $search_parts === false ) {
			return '';
		}

		$place = '';
		$verb  = '';
		$term  = $search_parts['term'];

		switch ( $search_parts['place'] ) {
			case 'people':
				$place = esc_html__( 'Email Address', 'wp-mail-smtp-pro' );
				$verb  = esc_html__( 'is', 'wp-mail-smtp-pro' );
				break;

			case 'headers':
				$place = esc_html__( 'Subject or Headers', 'wp-mail-smtp-pro' );
				$verb  = esc_html__( 'contains', 'wp-mail-smtp-pro' );
				break;

			case 'content':
				$place = esc_html__( 'Content', 'wp-mail-smtp-pro' );
				$verb  = esc_html__( 'contains', 'wp-mail-smtp-pro' );
				break;
		}

		return sprintf( /* translators: %1$s - field name; %2$s - verb; %3$s - term. */
			__( 'where %1$s %2$s "%3$s"', 'wp-mail-smtp-pro' ),
			'<em>' . $place . '</em>',
			$verb,
			'<em>' . esc_html( $term ) . '</em>'
		);
	}

	/**
	 * Return HTML with information about the date filter.
	 *
	 * @since 2.8.0
	 *
	 * @return string
	 */
	public function get_filter_date_html() {

		$table = $this->get_table();
		$dates = $table->get_filtered_dates();

		if ( $dates === false ) {
			return '';
		}

		$dates = array_map(
			function ( $date ) {

				return date_i18n( 'M j, Y', strtotime( $date ) );
			},
			$dates
		);

		$html = '';

		switch ( count( $dates ) ) {
			case 1:
				$html = sprintf( /* translators: %s - Date. */
					esc_html__( 'on %s', 'wp-mail-smtp-pro' ),
					'<em>' . $dates[0] . '</em>'
				);
				break;
			case 2:
				$html = sprintf( /* translators: %1$s - Date. %2$s - Date. */
					esc_html__( 'between %1$s and %2$s', 'wp-mail-smtp-pro' ),
					'<em>' . $dates[0] . '</em>',
					'<em>' . $dates[1] . '</em>'
				);
				break;
		}

		return $html;
	}

	/**
	 * Remove unnecessary $_GET parameters for shorter URL.
	 *
	 * @since 2.8.0
	 */
	protected function remove_get_parameters() {

		if ( isset( $_SERVER['REQUEST_URI'] ) ) {
			$_SERVER['REQUEST_URI'] = remove_query_arg(
				[
					'_wp_http_referer',
					'_wpnonce',
					'wp-mail-smtp-delete-log-entries-nonce',
				],
				$_SERVER['REQUEST_URI'] // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
			);
		}
	}
}
