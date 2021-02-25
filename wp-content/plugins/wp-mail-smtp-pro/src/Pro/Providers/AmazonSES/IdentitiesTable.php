<?php

namespace WPMailSMTP\Pro\Providers\AmazonSES;

use WPMailSMTP\Options as PluginOptions;

if ( ! class_exists( 'WP_List_Table', false ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Class SES Identities Table that displays the list of registered SES Identities.
 *
 * @since 2.4.0
 */
class IdentitiesTable extends \WP_List_Table {

	/**
	 * Plugin options.
	 *
	 * @since 2.4.0
	 *
	 * @var PluginOptions
	 */
	protected $options;

	/**
	 * Set up a constructor that references the parent constructor.
	 * Using the parent reference to set some default configs.
	 *
	 * @since 2.4.0
	 */
	public function __construct() {

		// Set the current screen if doing AJAX to prevent notices.
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			set_current_screen( 'toplevel_page_wp-mail-smtp' );
		}

		$this->options = new PluginOptions();
		$this->screen  = get_current_screen();

		// Set parent defaults.
		parent::__construct(
			array(
				'singular' => 'ses-identity',
				'plural'   => 'ses-identities',
				'ajax'     => true,
				'screen'   => $this->screen,
			)
		);
	}

	/**
	 * Define the table columns.
	 *
	 * @since 2.4.0
	 *
	 * @return array Associate array of slug=>Name columns data.
	 */
	public function get_columns() {

		return [
			'sender' => esc_html__( 'Sender', 'wp-mail-smtp-pro' ),
			'type'   => esc_html__( 'Type', 'wp-mail-smtp-pro' ),
			'status' => esc_html__( 'Status', 'wp-mail-smtp-pro' ),
			'action' => esc_html__( 'Action', 'wp-mail-smtp-pro' ),
		];
	}

	/**
	 * Display identity value (email address or domain name).
	 *
	 * @since 2.4.0
	 *
	 * @param \WPMailSMTP\Pro\Providers\AmazonSES\Identity $item Identity object.
	 *
	 * @return string
	 */
	public function column_sender( $item ) {

		return esc_html( $item->get_value() );
	}

	/**
	 * Display identity type.
	 *
	 * @since 2.4.0
	 *
	 * @param \WPMailSMTP\Pro\Providers\AmazonSES\Identity $item Identity object.
	 *
	 * @return string
	 */
	public function column_type( $item ) {

		if ( $item->get_type() === Identity::EMAIL_TYPE ) {
			return esc_html__( 'Email', 'wp-mail-smtp-pro' );
		}

		return esc_html__( 'Domain', 'wp-mail-smtp-pro' );
	}

	/**
	 * Display identity status.
	 *
	 * @since 2.4.0
	 *
	 * @param \WPMailSMTP\Pro\Providers\AmazonSES\Identity $item Identity object.
	 *
	 * @return string
	 */
	public function column_status( $item ) {

		return esc_html( $item->get_status() );
	}

	/**
	 * Display identity action links.
	 *
	 * @since 2.4.0
	 *
	 * @param \WPMailSMTP\Pro\Providers\AmazonSES\Identity $item Identity object.
	 *
	 * @return string
	 */
	public function column_action( $item ) {

		return $item->get_action_links();
	}

	/**
	 * Get the data, prepare pagination, process bulk actions.
	 * Prepare columns for display.
	 *
	 * @since 2.4.0
	 */
	public function prepare_items() {

		// Define our column headers.
		$this->_column_headers = array( $this->get_columns(), array(), $this->get_sortable_columns() );

		// Get the data from AWS SES API.
		$this->items = $this->get_items();
	}

	/**
	 * Get all identity objects.
	 *
	 * @since 2.4.0
	 *
	 * @return array Array of Identity objects.
	 */
	protected function get_items() {

		$auth = new Auth();

		if ( ! $auth->is_connection_ready() ) {
			return [];
		}

		$domains = $auth->get_registered_domains();
		$emails  = $auth->get_registered_emails();
		$data    = [];

		foreach ( array_merge( $domains, $emails ) as $identity_value => $identity_data ) {
			$type      = empty( $identity_data['VerificationToken'] ) ? Identity::EMAIL_TYPE : Identity::DOMAIN_TYPE;
			$txt_token = empty( $identity_data['VerificationToken'] ) ? null : $identity_data['VerificationToken'];

			$data[] = new Identity( $identity_value, $type, $identity_data['VerificationStatus'], $txt_token );
		}

		return $data;
	}

	/**
	 * Whether the table has items to display or not.
	 *
	 * @since 2.4.0
	 *
	 * @return bool
	 */
	public function has_items() {

		return count( $this->items ) > 0;
	}

	/**
	 * Message to be displayed when there are no items.
	 *
	 * @since 2.4.0
	 */
	public function no_items() {

		esc_html_e( 'No registered SES identities found.', 'wp-mail-smtp-pro' );
	}

	/**
	 * Generates the table navigation above or below the table.
	 * Removed bulk actions code from the parent method.
	 *
	 * @since 2.4.0
	 *
	 * @param string $which Which navigation: 'top' or 'bottom'.
	 */
	protected function display_tablenav( $which ) {

		?>
		<div class="tablenav <?php echo esc_attr( $which ); ?>">
			<?php $this->pagination( $which ); ?>

			<br class="clear" />
		</div>
		<?php
	}

	/**
	 * Define the table columns for JS use.
	 *
	 * @since 2.6.0
	 *
	 * @return array Associate array of slug=>Name columns data.
	 */
	public function get_columns_for_js() {

		$columns          = $this->get_columns();
		$prepared_columns = [];

		foreach ( $columns as $key => $label ) {
			$prepared_columns[] = [
				'label' => $label,
				'key'   => $key,
			];
		}

		return $prepared_columns;
	}

	/**
	 * Get all identity object's data for use in JS.
	 *
	 * @since 2.6.0
	 *
	 * @return array Array of arrays of Identity object's data.
	 */
	public function get_items_for_js() {

		$identities          = $this->get_items();
		$prepared_identities = [];

		foreach ( $identities as $identity ) {
			$prepared_identities[] = $identity->get_all();
		}

		return $prepared_identities;
	}
}
