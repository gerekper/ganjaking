<?php

namespace WPMailSMTP\Pro\Emails\Logs\Admin;

use WPMailSMTP\Options;
use WPMailSMTP\Admin\Area;
use WPMailSMTP\Pro\Emails\Logs\Email;
use WPMailSMTP\Pro\Emails\Logs\EmailsCollection;
use WPMailSMTP\Pro\Emails\Logs\Tracking\Events\Injectable\ClickLinkEvent;
use WPMailSMTP\Pro\Emails\Logs\Tracking\Events\Injectable\OpenEmailEvent;
use WPMailSMTP\WP;

if ( ! class_exists( 'WP_List_Table', false ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Class Table that displays the list of email log.
 *
 * @since 1.5.0
 */
class Table extends \WP_List_Table {

	/**
	 * Saved credentials for certain mailers, gmail only for now, to not retrieve them for all rows in a table.
	 *
	 * @since 1.7.1
	 *
	 * @var array
	 */
	private $cached_creds = array();

	/**
	 * Plugin options.
	 *
	 * @since 1.7.1
	 *
	 * @var Options
	 */
	protected $options;

	/**
	 * Number of email logs by different statuses.
	 *
	 * @since 2.7.0
	 *
	 * @var array
	 */
	public $counts;

	/**
	 * Set up a constructor that references the parent constructor.
	 * Using the parent reference to set some default configs.
	 *
	 * @since 1.5.0
	 */
	public function __construct() {

		$this->options = new Options();

		// Set parent defaults.
		parent::__construct(
			array(
				'singular' => 'email',
				'plural'   => 'emails',
				'ajax'     => false,
				'screen'   => 'wp-mail-smtp_page_wp-mail-smtp-logs',
			)
		);
	}

	/**
	 * Get the email log statuses for filtering purpose.
	 *
	 * @since 2.7.0
	 *
	 * @return array Associative array of email log statuses StatusCode=>Name.
	 */
	public function get_statuses() {

		$mailer = $this->options->get( 'mail', 'mailer' );

		// In this order statuses will appear in filters bar.
		$statuses = [
			Email::STATUS_DELIVERED => __( 'Delivered', 'wp-mail-smtp-pro' ),
			Email::STATUS_SENT      => __( 'Sent', 'wp-mail-smtp-pro' ),
			Email::STATUS_WAITING   => __( 'Pending', 'wp-mail-smtp-pro' ),
			Email::STATUS_UNSENT    => __( 'Failed', 'wp-mail-smtp-pro' ),
		];

		// Exclude Delivered and Pending statuses for mailers without verification API.
		if ( ! in_array( $mailer, [ 'mailgun', 'sendinblue', 'smtpcom' ], true ) ) {
			unset( $statuses[ Email::STATUS_DELIVERED ] );
			unset( $statuses[ Email::STATUS_WAITING ] );
		}

		return $statuses;
	}

	/**
	 * Get the items counts for various statuses of email log.
	 *
	 * @since 2.7.0
	 */
	public function get_counts() {

		$this->counts = [];

		// Base params with applied filters.
		$base_params = $this->get_filters_query_params();

		$total_params = $base_params;
		unset( $total_params['status'] );
		$this->counts['total'] = ( new EmailsCollection( $total_params ) )->get_count();

		foreach ( $this->get_statuses() as $status => $name ) {
			$collection = new EmailsCollection( array_merge( $base_params, [ 'status' => $status ] ) );

			$this->counts[ 'status_' . $status ] = $collection->get_count();
		}

		/**
		 * Filters items counts by various statuses of email log.
		 *
		 * @since 2.7.0
		 *
		 * @param array $counts {
		 *     Items counts by statuses.
		 *
		 *     @type integer $total Total items count.
		 *     @type integer $status_{$status_key} Items count by status.
		 * }
		 */
		$this->counts = apply_filters( 'wp_mail_smtp_pro_emails_logs_admin_table_get_counts', $this->counts );
	}

	/**
	 * Retrieve the view statuses.
	 *
	 * @since 2.7.0
	 */
	public function get_views() {

		$base_url       = $this->get_filters_base_url();
		$current_status = $this->get_filtered_status();

		$views = [];

		$views['all'] = sprintf(
			'<a href="%1$s" %2$s>%3$s&nbsp;<span class="count">(%4$d)</span></a>',
			esc_url( remove_query_arg( 'status', $base_url ) ),
			$current_status === false ? 'class="current"' : '',
			esc_html__( 'All', 'wp-mail-smtp-pro' ),
			intval( $this->counts['total'] )
		);

		foreach ( $this->get_statuses() as $status => $status_label ) {

			$count = intval( $this->counts[ 'status_' . $status ] );

			// Skipping status with no emails.
			if ( $count === 0 && $current_status !== $status ) {
				continue;
			}

			$views[ $status ] = sprintf(
				'<a href="%1$s" %2$s>%3$s&nbsp;<span class="count">(%4$d)</span></a>',
				esc_url( add_query_arg( 'status', $status, $base_url ) ),
				$current_status === $status ? 'class="current"' : '',
				esc_html( $status_label ),
				$count
			);

		}

		/**
		 * Filters items views.
		 *
		 * @since 2.7.0
		 *
		 * @param array $views {
		 *     Items views by statuses.
		 *
		 *     @type string $all Total items view.
		 *     @type integer $status_key Items views by status.
		 * }
		 * @param array $counts {
		 *     Items counts by statuses.
		 *
		 *     @type integer $total Total items count.
		 *     @type integer $status_{$status_key} Items count by status.
		 * }
		 */
		return apply_filters( 'wp_mail_smtp_pro_emails_logs_admin_table_get_views', $views, $this->counts );
	}

	/**
	 * Define the table columns.
	 *
	 * @since 1.5.0
	 *
	 * @return array Associative array of slug=>Name columns data.
	 */
	public function get_columns() {

		$columns = [];

		$columns['cb']        = '<input type="checkbox" />';
		$columns['status']    = '';
		$columns['subject']   = esc_html__( 'Subject', 'wp-mail-smtp-pro' );
		$columns['from']      = esc_html__( 'From', 'wp-mail-smtp-pro' );
		$columns['to']        = esc_html__( 'To', 'wp-mail-smtp-pro' );
		$columns['initiator'] = esc_html__( 'Source', 'wp-mail-smtp-pro' );
		$columns['cc']        = esc_html__( 'CC', 'wp-mail-smtp-pro' );
		$columns['bcc']       = esc_html__( 'BCC', 'wp-mail-smtp-pro' );

		if ( wp_mail_smtp()->get_pro()->get_logs()->is_enabled_open_email_tracking() ) {
			$columns['opened'] = esc_html__( 'Opened', 'wp-mail-smtp-pro' );
		}

		if ( wp_mail_smtp()->get_pro()->get_logs()->is_enabled_click_link_tracking() ) {
			$columns['clicked'] = esc_html__( 'Clicked', 'wp-mail-smtp-pro' );
		}

		$columns['date_sent'] = esc_html__( 'Date Sent', 'wp-mail-smtp-pro' );

		return $columns;
	}

	/**
	 * Allow users to select multiple emails at once (to perform a bulk action, for example).
	 *
	 * @since 1.5.0
	 *
	 * @param Email $item Email object.
	 *
	 * @return string Checkbox for bulk selection.
	 */
	protected function column_cb( $item ) {

		return sprintf(
			'<input type="checkbox" name="email_id[]" value="%d" />',
			$item->get_id()
		);
	}

	/**
	 * Display a nice email status: sent or not.
	 *
	 * @since 1.5.0
	 *
	 * @param Email $item Email object.
	 *
	 * @return string Email status as a dot.
	 */
	public function column_status( $item ) {

		switch ( $item->get_status() ) {
			case Email::STATUS_DELIVERED:
				return '<span title="' . esc_attr__( 'Delivered', 'wp-mail-smtp-pro' ) . '" class="wp-mail-smtp-dashicons-yes-alt-green delivered"></span>';
			case Email::STATUS_SENT:
				return '<span title="' . esc_attr__( 'Sent', 'wp-mail-smtp-pro' ) . '" class="dot sent"></span>';
			case Email::STATUS_WAITING:
				return '<span title="' . esc_attr__( 'Waiting for confirmation', 'wp-mail-smtp-pro' ) . '" class="circle waiting"></span>';
			default:
				return '<span title="' . esc_attr__( 'Not Sent', 'wp-mail-smtp-pro' ) . '" class="dot notsent"></span>';
		}
	}

	/**
	 * Display Email subject.
	 *
	 * @since 1.5.0
	 *
	 * @param Email $item Email object.
	 *
	 * @return string Email subject.
	 */
	public function column_subject( $item ) {

		$subject = '<strong>' .
						'<a href="' . esc_url( $this->get_item_link( $item, 'edit' ) ) . '" class="row-title">' .
							esc_html( $item->get_subject() ) .
						'</a>' .
					'</strong>';

		// View log action.
		$actions[] = '<span class="view">
						<a href="' . esc_url( $this->get_item_link( $item, 'edit' ) ) . '">' .
							esc_html__( 'View Log', 'wp-mail-smtp-pro' ) .
						'</a>
					</span>';

		// View email action.
		if ( ! empty( $item->get_content() ) ) {
			$actions[] = '<span class="view">
							<a href="' . esc_url( $this->get_item_link( $item, 'view' ) ) . '"
								class="thickbox email-preview"
								title="' . esc_attr( $item->get_subject() ) . '">' .
								esc_html__( 'View Email', 'wp-mail-smtp-pro' ) .
							'</a>
						</span>';
		}

		// Delete action.
		if ( current_user_can( wp_mail_smtp()->get_pro()->get_logs()->get_manage_capability() ) ) {
			$actions[] = '<span class="delete">
							<a href="' . esc_url( $this->get_item_link( $item, 'delete' ) ) . '">' .
								esc_html__( 'Delete', 'wp-mail-smtp-pro' ) .
							'</a>
						</span>';
		}

		return $subject . '<div class="row-actions">' . implode( ' | ', $actions ) . '</div>';
	}

	/**
	 * Get the link to a certain action: "edit" or "delete" for now.
	 *
	 * @since 1.5.0
	 *
	 * @param Email  $item Email object.
	 * @param string $link The link type to create.
	 *
	 * @return string
	 */
	protected function get_item_link( $item, $link = 'edit' ) {

		$url  = '';
		$link = sanitize_key( $link );

		switch ( $link ) {
			case 'edit':
				$url = add_query_arg(
					[
						'email_id' => $item->get_id(),
						'mode'     => 'view',
					],
					wp_mail_smtp()->get_admin()->get_admin_page_url( Area::SLUG . '-logs' )
				);
				break;

			case 'view':
				$url = add_query_arg(
					[
						'email_id'  => $item->get_id(),
						'mode'      => 'preview',
						'TB_iframe' => true,
						'width'     => 600,
						'height'    => '',
					],
					wp_nonce_url( wp_mail_smtp()->get_admin()->get_admin_page_url( Area::SLUG . '-logs' ), 'wp_mail_smtp_pro_logs_log_preview' )
				);
				break;

			case 'delete':
				$url = wp_nonce_url(
					add_query_arg(
						[
							'email_id' => $item->get_id(),
							'mode'     => 'delete',
						],
						wp_mail_smtp()->get_admin()->get_admin_page_url( Area::SLUG . '-logs' )
					),
					'wp_mail_smtp_pro_logs_log_delete'
				);
				break;
		}

		/**
		 * Filters email log link.
		 *
		 * @since 2.9.0
		 *
		 * @param string $url  Item link.
		 * @param Email  $item Email instance.
		 * @param string $link Link type.
		 */
		return apply_filters( 'wp_mail_smtp_pro_emails_logs_admin_table_get_item_link', $url, $item, $link );
	}

	/**
	 * Display FROM email address.
	 *
	 * @since 1.5.0
	 * @since 1.7.1 Added special processing for Gmail/Outlook mailers.
	 *
	 * @param Email $item Email object.
	 *
	 * @return string Email recipient(s).
	 */
	public function column_from( $item ) {

		$from_email = $this->generate_email_search_link( $item->get_people( 'from' ) );

		if ( empty( $from_email ) ) {
			$from_email = esc_html__( 'N/A', 'wp-mail-smtp-pro' );
		}

		return $from_email;
	}

	/**
	 * Display TO email addresses.
	 *
	 * @since 1.5.0
	 *
	 * @param Email $item Email object.
	 *
	 * @return string Email recipient(s), comma separated.
	 */
	public function column_to( $item ) {

		$to_emails = $item->get_people( 'to' );

		foreach ( $to_emails as $key => $email ) {
			$to_emails[ $key ] = $this->generate_email_search_link( $email );
		}

		if ( ! empty( $to_emails ) ) {
			$to_emails = implode( ', ', $to_emails );
		} else {
			$to_emails = esc_html__( 'N/A', 'wp-mail-smtp-pro' );
		}

		return $to_emails;
	}

	/**
	 * Display name of the plugin/theme (or WP core) that initiated/called the `wp_mail` function.
	 *
	 * @since 3.0.0
	 *
	 * @param Email $item Email object.
	 *
	 * @return string
	 */
	public function column_initiator( $item ) {

		return esc_html( $item->get_initiator_name() );
	}

	/**
	 * Display CC email addresses.
	 *
	 * @since 3.1.0
	 *
	 * @param Email $item Email object.
	 *
	 * @return string Email CC recipient(s), comma separated.
	 */
	public function column_cc( $item ) {

		$cc_emails = $item->get_people( 'cc' );

		foreach ( $cc_emails as $key => $email ) {
			$cc_emails[ $key ] = $this->generate_email_search_link( $email );
		}

		if ( ! empty( $cc_emails ) ) {
			$cc_emails = implode( ', ', $cc_emails );
		} else {
			$cc_emails = esc_html__( 'N/A', 'wp-mail-smtp-pro' );
		}

		return $cc_emails;
	}

	/**
	 * Display BCC email addresses.
	 *
	 * @since 3.1.0
	 *
	 * @param Email $item Email object.
	 *
	 * @return string Email BCC recipient(s), comma separated.
	 */
	public function column_bcc( $item ) {

		$bcc_emails = $item->get_people( 'bcc' );

		foreach ( $bcc_emails as $key => $email ) {
			$bcc_emails[ $key ] = $this->generate_email_search_link( $email );
		}

		if ( ! empty( $bcc_emails ) ) {
			$bcc_emails = implode( ', ', $bcc_emails );
		} else {
			$bcc_emails = esc_html__( 'N/A', 'wp-mail-smtp-pro' );
		}

		return $bcc_emails;
	}

	/**
	 * Display whether email was opened.
	 *
	 * @since 2.9.0
	 *
	 * @param Email $item Email object.
	 *
	 * @return string
	 */
	public function column_opened( $item ) {

		if ( ! $item->is_content_type_html_based() ) {
			return esc_html__( 'N/A', 'wp-mail-smtp-pro' );
		}

		return ( new OpenEmailEvent( $item->get_id() ) )->was_event_already_triggered() ?
			esc_html__( 'Yes', 'wp-mail-smtp-pro' ) :
			esc_html__( 'No', 'wp-mail-smtp-pro' );
	}

	/**
	 * Display whether one of email links was clicked.
	 *
	 * @since 2.9.0
	 *
	 * @param Email $item Email object.
	 *
	 * @return string
	 */
	public function column_clicked( $item ) {

		if ( ! $item->is_content_type_html_based() ) {
			return esc_html__( 'N/A', 'wp-mail-smtp-pro' );
		}

		return ( new ClickLinkEvent( $item->get_id() ) )->was_event_already_triggered() ?
			esc_html__( 'Yes', 'wp-mail-smtp-pro' ) :
			esc_html__( 'No', 'wp-mail-smtp-pro' );
	}

	/**
	 * Display Email date sent.
	 *
	 * @since 1.5.0
	 *
	 * @param Email $item Email object.
	 *
	 * @return string
	 * @throws \Exception Date manipulation can throw an exception.
	 */
	public function column_date_sent( $item ) {

		$date = null;

		try {
			$date = $item->get_date_sent();
		} catch ( \Exception $e ) {
			// We don't handle this exception as we define a default value above.
		}

		if ( empty( $date ) ) {
			return esc_html__( 'N/A', 'wp-mail-smtp-pro' );
		}

		return esc_html( date_i18n( WP::datetime_format(), strtotime( get_date_from_gmt( $date->format( WP::datetime_mysql_format() ) ) ) ) );
	}

	/**
	 * Define columns that are sortable.
	 *
	 * @since 1.5.0
	 *
	 * @return array List of columns that should be sortable.
	 */
	protected function get_sortable_columns() {

		return array(
			'subject'   => array( 'subject', false ),
			'date_sent' => array( 'date_sent', false ),
		);
	}

	/**
	 * Define a list of available bulk actions.
	 *
	 * @since 1.5.0
	 *
	 * @return array List of actions: slug=>Name.
	 */
	protected function get_bulk_actions() {

		$actions = [];

		if ( current_user_can( wp_mail_smtp()->get_pro()->get_logs()->get_manage_capability() ) ) {
			$actions['delete'] = esc_html__( 'Delete', 'wp-mail-smtp-pro' );
			$actions['resend'] = esc_html__( 'Resend', 'wp-mail-smtp-pro' );
		}

		return $actions;
	}

	/**
	 * Process the bulk actions.
	 *
	 * @since 1.5.0
	 *
	 * @see $this->prepare_items()
	 */
	public function process_bulk_action() {

		switch ( $this->current_action() ) {
			case 'delete':
				// This case is handled in \WPMailSMTP\Pro\Emails\Logs\Logs::process_email_delete().
				break;
		}
	}

	/**
	 * Return status filter value or FALSE.
	 *
	 * @since 2.8.0
	 *
	 * @return bool|integer
	 */
	public function get_filtered_status() {

		if ( ! isset( $_REQUEST['status'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return false;
		}

		return intval( $_REQUEST['status'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}

	/**
	 * Return date filter value or FALSE.
	 *
	 * @since 2.8.0
	 *
	 * @return bool|array
	 */
	public function get_filtered_dates() {

		if ( empty( $_REQUEST['date'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return false;
		}

		$dates = (array) explode( ' - ', sanitize_text_field( wp_unslash( $_REQUEST['date'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		return array_map( 'sanitize_text_field', $dates );
	}

	/**
	 * Return search filter values or FALSE.
	 *
	 * @since 2.8.0
	 *
	 * @return bool|array
	 */
	public function get_filtered_search_parts() {

		if ( empty( $_REQUEST['search']['place'] ) || empty( $_REQUEST['search']['term'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return false;
		}

		return array_map( 'sanitize_text_field', $_REQUEST['search'] ); // phpcs:ignore
	}

	/**
	 * Whether the emails log is filtered or not.
	 *
	 * @since 2.8.0
	 *
	 * @return bool
	 */
	public function is_filtered() {

		$is_filtered = false;

		if (
			$this->get_filtered_search_parts() !== false ||
			$this->get_filtered_dates() !== false ||
			$this->get_filtered_status() !== false
		) {
			$is_filtered = true;
		}

		return $is_filtered;
	}

	/**
	 * Get current filters query parameters.
	 *
	 * @since 2.7.0
	 *
	 * @return array
	 */
	public function get_filters_query_params() {

		$params = [
			'search' => $this->get_filtered_search_parts(),
			'status' => $this->get_filtered_status(),
			'date'   => $this->get_filtered_dates(),
		];

		return array_filter(
			$params,
			function ( $v ) {

				return $v !== false;
			}
		);
	}

	/**
	 * Get current filters base url.
	 *
	 * @since 2.7.0
	 *
	 * @return string
	 */
	public function get_filters_base_url() {

		$base_url       = wp_mail_smtp()->pro->get_logs()->get_admin_page_url();
		$filters_params = $this->get_filters_query_params();

		if ( isset( $filters_params['search'] ) ) {
			$base_url = add_query_arg(
				[
					'search' => [
						'place' => $filters_params['search']['place'],
						'term'  => $filters_params['search']['term'],
					],
				],
				$base_url
			);
		}

		if ( isset( $filters_params['status'] ) ) {
			$base_url = add_query_arg( 'status', $filters_params['status'], $base_url );
		}

		if ( isset( $filters_params['date'] ) ) {
			$base_url = add_query_arg( 'date', implode( ' - ', $filters_params['date'] ), $base_url );
		}

		return $base_url;
	}

	/**
	 * Get the data, prepare pagination, process bulk actions.
	 * Prepare columns for display.
	 *
	 * @since 1.5.0
	 * @since 1.7.0 Added search support.
	 */
	public function prepare_items() {

		// Retrieve count.
		$this->get_counts();

		/**
		 * TODO: implement.
		 */
		$this->process_bulk_action();

		/*
		 * Prepare all the params to pass to our Collection.
		 * All sanitization is done in that class.
		 */
		$params = $this->get_filters_query_params();

		// Total amount for pagination with WHERE clause - super quick count DB request.
		$total_items = ( new EmailsCollection( $params ) )->get_count();

		if ( ! empty( $_REQUEST['orderby'] ) ) { // phpcs:ignore
			$params['orderby'] = $_REQUEST['orderby']; // phpcs:ignore
		}

		if ( ! empty( $_REQUEST['order'] ) ) { // phpcs:ignore
			$params['order'] = $_REQUEST['order']; // phpcs:ignore
		}

		$params['offset'] = ( $this->get_pagenum() - 1 ) * EmailsCollection::$per_page;

		// Get the data from the DB using parameters defined above.
		$collection  = new EmailsCollection( $params );
		$this->items = $collection->get();

		/*
		 * Register our pagination options & calculations.
		 */
		$this->set_pagination_args(
			[
				'total_items' => $total_items,
				'per_page'    => EmailsCollection::$per_page,
			]
		);
	}

	/**
	 * Display the search box.
	 *
	 * @since 1.7.0
	 *
	 * @param string $text     The 'submit' button label.
	 * @param string $input_id ID attribute value for the search input field.
	 */
	public function search_box( $text, $input_id ) {

		if ( ! $this->is_filtered() && ! $this->has_items() ) {
			return;
		}

		$search_place = ! empty( $_REQUEST['search']['place'] ) ? sanitize_key( $_REQUEST['search']['place'] ) : 'people'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$search_term  = ! empty( $_REQUEST['search']['term'] ) ? wp_unslash( $_REQUEST['search']['term'] ) : ''; // phpcs:ignore WordPress.Security

		if ( ! empty( $_REQUEST['orderby'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			echo '<input type="hidden" name="orderby" value="' . esc_attr( $_REQUEST['orderby'] ) . '" />'; // phpcs:ignore WordPress.Security
		}

		if ( ! empty( $_REQUEST['order'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			echo '<input type="hidden" name="order" value="' . esc_attr( $_REQUEST['order'] ) . '" />'; // phpcs:ignore WordPress.Security
		}
		?>

		<p class="search-box">
			<label class="screen-reader-text" for="<?php echo esc_attr( $input_id ); ?>"><?php echo esc_html( $text ); ?>:</label>
			<select name="search[place]">
				<option value="people" <?php selected( 'people', $search_place ); ?>><?php esc_html_e( 'Email Addresses', 'wp-mail-smtp-pro' ); ?></option>
				<option value="headers" <?php selected( 'headers', $search_place ); ?>><?php esc_html_e( 'Subject & Headers', 'wp-mail-smtp-pro' ); ?></option>
				<option value="content" <?php selected( 'content', $search_place ); ?>><?php esc_html_e( 'Content', 'wp-mail-smtp-pro' ); ?></option>
			</select>
			<input type="search" id="<?php echo esc_attr( $input_id ); ?>" name="search[term]" value="<?php echo esc_attr( $search_term ); ?>" />
			<?php submit_button( $text, '', '', false, array( 'id' => 'search-submit' ) ); ?>
		</p>

		<?php
	}

	/**
	 * Whether the table has items to display or not.
	 *
	 * @since 1.5.0
	 *
	 * @return bool
	 */
	public function has_items() {

		return count( $this->items ) > 0;
	}

	/**
	 * Message to be displayed when there are no items.
	 *
	 * @since 1.5.0
	 * @since 1.7.0 Added a custom message for empty search results.
	 */
	public function no_items() {

		if ( $this->is_filtered() ) {
			esc_html_e( 'No emails found.', 'wp-mail-smtp-pro' );
		} else {
			esc_html_e( 'No emails have been logged for now.', 'wp-mail-smtp-pro' );
		}
	}

	/**
	 * Displays the table and register the WP Thickbox.
	 *
	 * @since 2.3.0
	 */
	public function display() {

		// Register WP built-in Thickbox for popup.
		add_thickbox();

		parent::display();
	}

	/**
	 * Extra controls to be displayed between bulk actions and pagination.
	 *
	 * @since 2.5.0
	 *
	 * @param string $which Which tablenav: top or bottom.
	 */
	protected function extra_tablenav( $which ) {

		if ( $which !== 'top' ) {
			return;
		}

		$date = $this->get_filtered_dates() !== false ? implode( ' - ', $this->get_filtered_dates() ) : '';
		?>
		<div class="alignleft actions wp-mail-smtp-filter-date">

			<input type="text" name="date" class="regular-text wp-mail-smtp-filter-date-selector"
						 placeholder="<?php esc_attr_e( 'Select a date range', 'wp-mail-smtp-pro' ); ?>"
						 value="<?php echo esc_attr( $date ); ?>">

			<button type="submit" name="action" value="filter_date" class="button">
				<?php esc_html_e( 'Filter', 'wp-mail-smtp-pro' ); ?>
			</button>

		</div>
		<?php
		if (
			$this->has_items() &&
			current_user_can( wp_mail_smtp()->get_pro()->get_logs()->get_manage_capability() )
		) {
			wp_nonce_field( 'wp_mail_smtp_pro_delete_log_entries', 'wp-mail-smtp-delete-log-entries-nonce', false );
			printf(
				'<button id="wp-mail-smtp-delete-all-logs-button" type="button" class="button">%s</button>',
				esc_html__( 'Delete All Logs', 'wp-mail-smtp-pro' )
			);
		}
	}

	/**
	 * Get the name of the primary column.
	 * Important for the mobile view.
	 *
	 * @since 2.5.0
	 *
	 * @return string The name of the primary column.
	 */
	protected function get_primary_column_name() {

		return 'subject';
	}

	/**
	 * Generate a HTML link for searching/filtering table items by provided email.
	 *
	 * @since 1.9.0
	 *
	 * @param string $email The email address for which to search for.
	 *
	 * @return string A HTML link with the href pointing to the table email search for the provided email.
	 *                Or an empty string, if $email is not defined.
	 */
	private function generate_email_search_link( $email ) {

		if ( empty( $email ) ) {
			return '';
		}

		$url = add_query_arg(
			array(
				'search' => array(
					'place' => 'people',
					'term'  => rawurlencode( $email ),
				),
			),
			wp_mail_smtp()->get_admin()->get_admin_page_url( Area::SLUG . '-logs' )
		);

		return '<a href="' . esc_url( $url ) . '">' . esc_html( $email ) . '</a>';
	}
}
