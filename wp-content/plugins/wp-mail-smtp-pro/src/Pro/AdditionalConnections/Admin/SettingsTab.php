<?php

namespace WPMailSMTP\Pro\AdditionalConnections\Admin;

use WPMailSMTP\Admin\ConnectionSettings;
use WPMailSMTP\Admin\Pages\AdditionalConnectionsTab;
use WPMailSMTP\Options;
use WPMailSMTP\Pro\AdditionalConnections\AdditionalConnections;
use WPMailSMTP\Pro\AdditionalConnections\Connection;
use WPMailSMTP\Pro\Emails\Logs\Admin\PageAbstract;
use WPMailSMTP\WP;

/**
 * Class SettingsTab.
 *
 * Additional connections settings tab.
 *
 * @since 3.7.0
 */
class SettingsTab extends AdditionalConnectionsTab {

	/**
	 * Additional Connections object.
	 *
	 * @since 3.7.0
	 *
	 * @var AdditionalConnections
	 */
	private $additional_connections;

	/**
	 * Constructor.
	 *
	 * @since 3.7.0
	 *
	 * @param PageAbstract $parent_page Parent page object.
	 */
	public function __construct( $parent_page = null ) {

		parent::__construct( $parent_page );

		if ( wp_mail_smtp()->get_admin()->get_current_tab() === $this->slug ) {
			$this->hooks();
		}

		$this->additional_connections = wp_mail_smtp()->get_pro()->get_additional_connections();
	}

	/**
	 * Register hooks.
	 *
	 * @since 3.7.0
	 */
	public function hooks() {

		add_action( 'admin_init', [ $this, 'process_actions' ] );

		add_action( 'wp_mail_smtp_admin_area_enqueue_assets', [ $this, 'enqueue_assets' ] );
	}

	/**
	 * Enqueue required JS and CSS.
	 *
	 * @since 3.7.0
	 */
	public function enqueue_assets() {

		// Enqueue JS and CSS from education page.
		if ( ! $this->additional_connections->has_connections() ) {
			parent::enqueue_assets();
		}
	}

	/**
	 * Process actions.
	 *
	 * @since 3.7.0
	 */
	public function process_actions() {

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( isset( $_REQUEST['mode'] ) && isset( $_REQUEST['connection_id'] ) && $_REQUEST['mode'] === 'delete' ) {
			$this->process_connection_delete();
		}

		$this->display_notices();
	}

	/**
	 * Delete the connection.
	 *
	 * @since 3.7.0
	 */
	private function process_connection_delete() {

		// Nonce verification.
		if (
			! isset( $_REQUEST['_wpnonce'] ) ||
			! wp_verify_nonce( sanitize_key( $_REQUEST['_wpnonce'] ), 'wp_mail_smtp_pro_additional_connection_delete' )
		) {
			wp_die( esc_html__( 'Access rejected.', 'wp-mail-smtp-pro' ) );
		}

		if ( ! current_user_can( $this->additional_connections->get_manage_capability() ) ) {
			wp_die( esc_html__( 'You don\'t have the capability to perform this action.', 'wp-mail-smtp-pro' ) );
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! isset( $_REQUEST['connection_id'] ) ) {
			wp_die( esc_html__( 'Required parameters are missing.', 'wp-mail-smtp-pro' ) );
		}

		$connection_id = sanitize_key( $_REQUEST['connection_id'] );

		$is_removed = $this->additional_connections->remove_connection( $connection_id );

		if ( $is_removed ) {
			$url = add_query_arg( 'message', 'deleted', $this->get_connections_list_url() );
		} else {
			$url = add_query_arg( 'message', 'delete_failed', $this->get_connections_list_url() );
		}

		wp_safe_redirect( $url );
		exit;
	}

	/**
	 * Display notices when needed.
	 *
	 * @since 3.7.0
	 */
	private function display_notices() {

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$message = isset( $_GET['message'] ) ? sanitize_key( $_GET['message'] ) : '';

		if (
			empty( $message ) ||
			! current_user_can( $this->additional_connections->get_manage_capability() )
		) {
			return;
		}

		switch ( $message ) {
			case 'deleted':
				WP::add_admin_notice(
					esc_html__( 'Connection was successfully deleted.', 'wp-mail-smtp-pro' ),
					WP::ADMIN_NOTICE_SUCCESS
				);
				break;

			case 'delete_failed':
				WP::add_admin_notice(
					esc_html__( 'There was an error while processing your request, and connection were not deleted. Please try again.', 'wp-mail-smtp-pro' ),
					WP::ADMIN_NOTICE_WARNING
				);
				break;

			case 'saved':
				WP::add_admin_notice(
					esc_html__( 'Settings were successfully saved.', 'wp-mail-smtp-pro' ),
					WP::ADMIN_NOTICE_SUCCESS
				);
				break;
		}
	}

	/**
	 * Output HTML of the additional connections list or single additional connection.
	 *
	 * @since 3.7.0
	 */
	public function display() {

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$mode = isset( $_GET['mode'] ) ? sanitize_key( $_GET['mode'] ) : false;

		if ( in_array( $mode, [ 'new', 'edit' ], true ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$connection_id = isset( $_GET['connection_id'] ) ? sanitize_key( $_GET['connection_id'] ) : false;

			$this->display_single_connection( $connection_id );
		} elseif ( $this->additional_connections->has_connections() ) {
			$this->display_connections_list();
		} else {
			$this->display_no_connections();
		}
	}

	/**
	 * Output HTML of the additional connections settings header.
	 *
	 * @since 3.7.0
	 *
	 * @param bool $is_singular Whether to display single or archive page header.
	 */
	private function display_header( $is_singular = false ) {

		?>
		<div class="wp-mail-smtp-setting-row wp-mail-smtp-setting-row-content section-heading wp-mail-smtp-additional-connections-header">
			<div class="wp-mail-smtp-setting-field">
				<h2 class="wp-mail-smtp-additional-connections-header__heading">
					<?php esc_html_e( 'Additional Connections', 'wp-mail-smtp-pro' ); ?>
					<?php if ( ! $is_singular ) : ?>
						<a href="<?php echo esc_url( $this->get_connection_url( 'new' ) ); ?>">
							<?php esc_html_e( 'Add New', 'wp-mail-smtp-pro' ); ?>
						</a>
					<?php endif; ?>
				</h2>

				<p class="desc">
					<?php
					echo wp_kses(
						sprintf( /* translators: %s - Additional Connections documentation page URL. */
							__( 'Create additional connections to set a backup for your Primary Connection or to configure Smart Routing. <a href="%s" target="_blank" rel="noopener noreferrer">Learn More</a>.', 'wp-mail-smtp-pro' ),
							esc_url(
								wp_mail_smtp()->get_utm_url(
									'https://wpmailsmtp.com/docs/configuring-additional-connections/',
									[
										'content' => 'Additional Connections description',
									]
								)
							)
						),
						[
							'a' => [
								'href'   => [],
								'target' => [],
								'rel'    => [],
							],
						]
					);
					?>
				</p>

				<?php if ( $is_singular ) : ?>
					<a href="<?php echo esc_url( $this->get_connections_list_url() ); ?>" class="wp-mail-smtp-additional-connections-header__back-link">
						<svg width="13" height="12" viewBox="0 0 13 12" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M12.8125 5.1875V6.8125H3.21875L6.40625 10L5.59375 11.5938L0 6L5.59375 0.40625L6.40625 2L3.21875 5.1875H12.8125Z" fill="currentColor"/>
						</svg>
						<?php esc_html_e( 'Back to All Connections', 'wp-mail-smtp-pro' ); ?>
					</a>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Output HTML of the additional connections list.
	 *
	 * @since 3.7.0
	 */
	private function display_connections_list() {

		$this->display_header();

		$connections = $this->additional_connections->get_connections();

		if ( empty( $connections ) ) {
			return;
		}
		?>
		<div class="wp-mail-smtp-additional-connections-list">
			<div class="wp-mail-smtp-additional-connections-list__header">
				<?php esc_html_e( 'Saved Connections', 'wp-mail-smtp-pro' ); ?>
			</div>

			<ul class="wp-mail-smtp-additional-connections-list__items">
				<?php foreach ( array_values( $connections ) as $i => $connection ) : ?>
					<li class="wp-mail-smtp-additional-connections-list__item">
						<?php echo esc_html( $i + 1 ); ?>.
						<a href="<?php echo esc_url( $this->get_connection_url( 'edit', $connection->get_id() ) ); ?>" class="wp-mail-smtp-additional-connections-list__link">
							<?php echo esc_html( $connection->get_title() ); ?>
						</a>
						<a href="<?php echo esc_url( $this->get_connection_url( 'edit', $connection->get_id() ) ); ?>" class="wp-mail-smtp-additional-connections-list__btn wp-mail-smtp-btn wp-mail-smtp-btn-md wp-mail-smtp-btn-grey">
							<i class="dashicons dashicons-edit"></i>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php
	}

	/**
	 * Output HTML of the education elements when there are no additional connections.
	 *
	 * @since 3.7.0
	 */
	private function display_no_connections() {

		?>
		<div class="wp-mail-smtp-additional-connections-no-items">
			<?php
			$this->display_header();
			$this->display_education_screenshots();
			$this->display_education_features_list();
			?>

			<p class="wp-mail-smtp-submit">
				<a href="<?php echo esc_url( $this->get_connection_url( 'new' ) ); ?>" class="wp-mail-smtp-btn wp-mail-smtp-btn-cta wp-mail-smtp-btn-orange">
					<?php esc_html_e( 'Get Started', 'wp-mail-smtp-pro' ); ?>
				</a>
			</p>
		</div>
		<?php
	}

	/**
	 * Output HTML of the single additional connection.
	 *
	 * @since 3.7.0
	 *
	 * @param string $connection_id Connection ID.
	 */
	private function display_single_connection( $connection_id ) {

		$is_new = empty( $connection_id );

		if ( ! $is_new ) {
			$connection = wp_mail_smtp()->get_connections_manager()->get_connection( $connection_id, false );
		} else {
			$connection = new Connection( uniqid() );
		}

		if ( $connection === false ) {
			$this->display_header( true );
			$this->display_connection_not_found();

			return;
		}

		$connection_relation = $this->get_connection_relation( $connection_id );
		$connection_settings = new ConnectionSettings( $connection );
		?>

		<form method="POST" action="" autocomplete="off" class="wp-mail-smtp-connection-settings-form">
			<?php $this->wp_nonce_field(); ?>

			<?php $this->display_header( true ); ?>

			<?php if ( $connection_relation === 'backup' ) : ?>
				<div class="wp-mail-smtp-notice notice-warning notice-inline wp-mail-smtp-additional-connections-notice-top">
					<p>
						<?php
						echo wp_kses(
							__( '<b>Warning!</b> You’re editing your backup mailer.', 'wp-mail-smtp-pro' ),
							[
								'b' => [],
							]
						);
						?>
					</p>
				</div>
			<?php endif; ?>

			<!-- Connection name -->
			<div id="wp-mail-smtp-setting-row-from_email" class="wp-mail-smtp-setting-row wp-mail-smtp-setting-row-email wp-mail-smtp-clear">
				<div class="wp-mail-smtp-setting-label">
					<label for="wp-mail-smtp-setting-from_email"><?php esc_html_e( 'Connection Nickname', 'wp-mail-smtp-pro' ); ?></label>
				</div>
				<div class="wp-mail-smtp-setting-field">
					<input name="wp-mail-smtp[connection][name]" type="text"
								 value="<?php echo esc_attr( $connection->get_options()->get( 'connection', 'name' ) ); ?>"
								 id="wp-mail-smtp-setting-from_email" spellcheck="false"
								 required
					/>
				</div>
			</div>

			<?php $connection_settings->display(); ?>

			<div class="wp-mail-smtp-additional-connection-actions">
				<?php $this->display_save_btn(); ?>

				<?php if ( ! $is_new ) : ?>
					<a href="<?php echo esc_url( $this->get_connection_url( 'delete', $connection_id ) ); ?>" class="js-wp-mail-smtp-delete-additional-connection wp-mail-smtp-additional-connection-actions__delete-link" data-relation="<?php echo esc_attr( $connection_relation ); ?>">
						<?php esc_attr_e( 'Delete Connection', 'wp-mail-smtp-pro' ); ?>
					</a>
				<?php endif; ?>
			</div>
		</form>
		<?php
	}

	/**
	 * Display a connection not found notice.
	 *
	 * @since 3.7.0
	 */
	private function display_connection_not_found() {

		?>
		<h2><?php esc_html_e( 'Something went wrong', 'wp-mail-smtp-pro' ); ?></h2>
		<p>
			<?php esc_html_e( 'You are trying to access an additional connection that is no longer available or never existed.', 'wp-mail-smtp-pro' ); ?>
		</p>
		<p>
			<?php esc_html_e( 'Please use the "Back to All Connections" button to return to the list of all available connections.', 'wp-mail-smtp-pro' ); ?>
		</p>
		<?php
	}

	/**
	 * Process tab form submission ($_POST).
	 *
	 * @since 3.7.0
	 *
	 * @param array $data Post data specific for the plugin.
	 */
	public function process_post( $data ) {

		$this->check_admin_referer();

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$is_new = ! isset( $_GET['connection_id'] );

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$connection_id = isset( $_GET['connection_id'] ) ? sanitize_key( $_GET['connection_id'] ) : uniqid();

		$connection          = new Connection( $connection_id );
		$connection_settings = new ConnectionSettings( $connection );

		$old_data = $connection->get_options()->get_all();

		$data = $connection_settings->process( $data, $old_data );

		// Save connection settings.
		$connection->get_options()->set( $data, false, false );

		$connection_settings->post_process( $data, $old_data );

		if ( $is_new || $connection_settings->get_scroll_to() !== false ) {
			$redirect_url = $this->get_connection_url( 'edit', $connection->get_id() );

			if ( $connection_settings->get_scroll_to() !== false ) {
				$redirect_url .= $connection_settings->get_scroll_to();
			}

			$redirect_url = add_query_arg( 'message', 'saved', $redirect_url );

			wp_safe_redirect( $redirect_url );
			exit;
		}

		WP::add_admin_notice(
			esc_html__( 'Settings were successfully saved.', 'wp-mail-smtp-pro' ),
			WP::ADMIN_NOTICE_SUCCESS
		);
	}

	/**
	 * Get the connections list URL.
	 *
	 * @since 3.7.0
	 *
	 * @return string
	 */
	private function get_connections_list_url() {

		return add_query_arg(
			[
				'tab' => 'connections',
			],
			wp_mail_smtp()->get_admin()->get_admin_page_url()
		);
	}

	/**
	 * Get the connection URL.
	 *
	 * @since 3.7.0
	 *
	 * @param string $mode          URL type that should be returned. Acceptable values "new", "edit" and "delete".
	 * @param string $connection_id Connection ID.
	 *
	 * @return string
	 */
	private function get_connection_url( $mode = 'edit', $connection_id = false ) {

		$url = '';

		switch ( $mode ) {
			case 'new':
				$url = add_query_arg(
					[
						'tab'  => 'connections',
						'mode' => 'new',
					],
					wp_mail_smtp()->get_admin()->get_admin_page_url()
				);
				break;

			case 'edit':
				$url = add_query_arg(
					[
						'tab'           => 'connections',
						'mode'          => 'edit',
						'connection_id' => $connection_id,
					],
					wp_mail_smtp()->get_admin()->get_admin_page_url()
				);
				break;

			case 'delete':
				$url = wp_nonce_url(
					add_query_arg(
						[
							'tab'           => 'connections',
							'mode'          => 'delete',
							'connection_id' => $connection_id,
						],
						wp_mail_smtp()->get_admin()->get_admin_page_url()
					),
					'wp_mail_smtp_pro_additional_connection_delete'
				);
				break;
		}

		return $url;
	}

	/**
	 * Get connection relation/dependency. Backup relation prioritized.
	 *
	 * @since 3.7.0
	 *
	 * @param string $connection_id Connection ID.
	 *
	 * @return string
	 */
	private function get_connection_relation( $connection_id ) {

		$backup_connection_id = Options::init()->get( 'backup_connection', 'connection_id' );

		if ( ! empty( $backup_connection_id ) && $connection_id === $backup_connection_id ) {
			return 'backup';
		}

		$routes = Options::init()->get( 'smart_routing', 'routes' );

		if ( ! empty( $routes ) && in_array( $connection_id, array_column( $routes, 'connection_id' ), true ) ) {
			return 'routing';
		}

		return 'none';
	}
}
