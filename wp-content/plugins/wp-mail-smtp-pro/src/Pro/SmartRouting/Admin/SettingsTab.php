<?php

namespace WPMailSMTP\Pro\SmartRouting\Admin;

use WPMailSMTP\Admin\Pages\SmartRoutingTab;
use WPMailSMTP\Options;
use WPMailSMTP\Pro\AdditionalConnections\AdditionalConnections;
use WPMailSMTP\Pro\ConditionalLogic\ConditionalLogicSettings;
use WPMailSMTP\Pro\Emails\Logs\Admin\PageAbstract;
use WPMailSMTP\WP;

/**
 * Class SettingsTab.
 *
 * Smart routing settings tab.
 *
 * @since 3.7.0
 */
class SettingsTab extends SmartRoutingTab {

	/**
	 * Additional Connections object.
	 *
	 * @since 3.7.0
	 *
	 * @var AdditionalConnections
	 */
	private $additional_connections;

	/**
	 * Conditional Logic Settings object.
	 *
	 * @since 3.7.0
	 *
	 * @var ConditionalLogicSettings
	 */
	private $conditional_logic_settings;

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
			$this->init();
			$this->hooks();
		}
	}

	/**
	 * Initialize object.
	 *
	 * @since 3.7.0
	 */
	private function init() {

		$conditional_logic_properties = [
			'subject'      => [
				'label' => esc_html__( 'Subject', 'wp-mail-smtp-pro' ),
				'type'  => 'text',
			],
			'message'      => [
				'label' => esc_html__( 'Message', 'wp-mail-smtp-pro' ),
				'type'  => 'text',
			],
			'from_email'   => [
				'label' => esc_html__( 'From Email', 'wp-mail-smtp-pro' ),
				'type'  => 'text',
			],
			'from_name'    => [
				'label' => esc_html__( 'From Name', 'wp-mail-smtp-pro' ),
				'type'  => 'text',
			],
			'to_email'     => [
				'label' => esc_html__( 'To', 'wp-mail-smtp-pro' ),
				'type'  => 'text',
			],
			'cc'           => [
				'label' => esc_html__( 'CC', 'wp-mail-smtp-pro' ),
				'type'  => 'text',
			],
			'bcc'          => [
				'label' => esc_html__( 'BCC', 'wp-mail-smtp-pro' ),
				'type'  => 'text',
			],
			'reply_to'     => [
				'label' => esc_html__( 'Reply-To', 'wp-mail-smtp-pro' ),
				'type'  => 'text',
			],
			'header_name'  => [
				'label' => esc_html__( 'Header Name', 'wp-mail-smtp-pro' ),
				'type'  => 'text',
			],
			'header_value' => [
				'label' => esc_html__( 'Header Value', 'wp-mail-smtp-pro' ),
				'type'  => 'text',
			],
			'initiator'    => [
				'label'   => esc_html__( 'Initiator', 'wp-mail-smtp-pro' ),
				'type'    => 'select',
				'choices' => $this->get_initiators_choices(),
			],
		];

		$this->additional_connections     = wp_mail_smtp()->get_pro()->get_additional_connections();
		$this->conditional_logic_settings = new ConditionalLogicSettings( $conditional_logic_properties );
	}

	/**
	 * Register hooks.
	 *
	 * @since 3.7.0
	 */
	public function hooks() {

		add_action( 'wp_mail_smtp_admin_area_enqueue_assets', [ $this, 'enqueue_assets' ] );

		add_action( 'admin_footer', [ $this, 'footer_scripts' ] );

		$this->conditional_logic_settings->hooks();
	}

	/**
	 * Enqueue required JS and CSS.
	 *
	 * @since 3.7.0
	 */
	public function enqueue_assets() {

		$min = WP::asset_min();

		wp_enqueue_script( 'wp-util' );

		wp_enqueue_style(
			'wp-mail-smtp-smart-routing',
			wp_mail_smtp()->plugin_url . '/assets/css/smtp-smart-routing.min.css',
			[],
			WPMS_PLUGIN_VER
		);
		wp_enqueue_script(
			'wp-mail-smtp-smart-routing',
			wp_mail_smtp()->plugin_url . "/assets/pro/js/smtp-pro-smart-routing{$min}.js",
			[ 'jquery' ],
			WPMS_PLUGIN_VER,
			true
		);
	}

	/**
	 * Output footer scripts.
	 *
	 * @since 3.7.0
	 */
	public function footer_scripts() {

		?>
		<script type="text/html" id="tmpl-wp-mail-smtp-smart-route">
			<?php $this->display_route( '{{ data.routeIndex }}' ); ?>
		</script>
		<?php
	}

	/**
	 * Output HTML of the smart routing settings.
	 *
	 * @since 3.7.0
	 */
	public function display() {

		$is_enabled = (bool) Options::init()->get( 'smart_routing', 'enabled' );
		$routes     = Options::init()->get( 'smart_routing', 'routes' );

		if ( empty( $routes ) ) {
			// Default value.
			$routes = [ 'route-0' => [] ];
		}
		?>
		<form method="POST" action="">
			<?php $this->wp_nonce_field(); ?>

			<div class="wp-mail-smtp-setting-row wp-mail-smtp-setting-row-content section-heading wp-mail-smtp-smart-routing-header">
				<div class="wp-mail-smtp-setting-field">
					<h2 class="wp-mail-smtp-smart-routing-header__heading">
						<?php esc_html_e( 'Smart Routing', 'wp-mail-smtp-pro' ); ?>
						<?php if ( $this->additional_connections->has_connections() ) : ?>
							<a href="#" class="wp-mail-smtp-smart-routing-route-add">
								<?php esc_html_e( 'Add New', 'wp-mail-smtp-pro' ); ?>
							</a>
						<?php endif; ?>
					</h2>
					<p class="desc">
						<?php
						echo wp_kses(
							sprintf( /* translators: %s - Smart Routing documentation page URL. */
								__( 'Send emails from different additional connections based on your configured conditions. Emails that do not match any of the conditions below will be sent via your Primary Connection. <a href="%s" target="_blank" rel="noopener noreferrer">Learn More</a>.', 'wp-mail-smtp-pro' ),
								esc_url(
									wp_mail_smtp()->get_utm_url(
										'https://wpmailsmtp.com/docs/setting-up-smart-email-routing/',
										[
											'content' => 'Smart Routing description',
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
				</div>
			</div>

			<?php if ( ! $this->additional_connections->has_connections() ) : ?>
				<div class="wp-mail-smtp-notice notice-info notice-inline wp-mail-smtp-smart-routing-notice-top wp-mail-smtp-smart-routing-notice-top--no-connections">
					<p>
						<?php
						echo wp_kses(
							sprintf( /* translators: %s - Additional connections settings page url. */
								__( 'You need to configure at least one <a href="%s">additional connection</a> before you can use Smart Routing.', 'wp-mail-smtp-pro' ),
								add_query_arg(
									[
										'tab' => 'connections',
									],
									wp_mail_smtp()->get_admin()->get_admin_page_url()
								)
							),
							[
								'a' => [
									'href' => [],
								],
							]
						);
						?>
					</p>
				</div>
			<?php endif; ?>

			<div class="wp-mail-smtp-setting-row wp-mail-smtp-setting-row-no-border">
				<div class="wp-mail-smtp-smart-routing-enable-toggle">
					<label for="wp-mail-smtp-smart-routing-enabled" class="wp-mail-smtp-setting-toggle">
						<input type="checkbox" id="wp-mail-smtp-smart-routing-enabled" class="wp-mail-smtp-smart-routing-enabled"
									 name="wp-mail-smtp[smart_routing][enabled]" value="yes" <?php checked( $is_enabled ); ?>
						/>
						<span class="wp-mail-smtp-setting-toggle__switch"></span>
					</label>
					<label for="wp-mail-smtp-smart-routing-enabled" class="wp-mail-smtp-smart-routing-enable-toggle__label">
						<?php esc_html_e( 'Enable Smart Routing', 'wp-mail-smtp-pro' ); ?>
					</label>
				</div>
			</div>

			<div class="wp-mail-smtp-setting-row wp-mail-smtp-setting-row-no-border wp-mail-smtp-setting-row-no-padding">
				<div class="wp-mail-smtp-smart-routing-routes">
					<?php foreach ( $routes as $route_index => $route ) : ?>
						<?php $this->display_route( $route_index, $route ); ?>
					<?php endforeach; ?>
				</div>

				<div class="wp-mail-smtp-smart-routing-routes-note">
					<img src="<?php echo esc_url( wp_mail_smtp()->assets_url . '/images/icons/lightbulb.svg' ); ?>" alt="<?php esc_attr_e( 'Light bulb icon', 'wp-mail-smtp-pro' ); ?>">
					<p>
						<?php
						echo wp_kses(
							sprintf(
								/* translators: %s - Primary Connection settings page URL. */
								__( 'Friendly reminder, your <u><b><a href="%s">Primary Connection</a></b></u> will be used for all emails that do not match the conditions above.', 'wp-mail-smtp-pro' ),
								esc_url( wp_mail_smtp()->get_admin()->get_admin_page_url() )
							),
							[
								'b' => [],
								'u' => [],
								'a' => [
									'href' => [],
								],
							]
						);
						?>
					</p>
				</div>
			</div>

			<?php $this->display_save_btn(); ?>
		</form>
		<?php
	}

	/**
	 * Output HTML of the route settings.
	 *
	 * @since 3.7.0
	 *
	 * @param string $route_index Route index.
	 * @param array  $route       Route data.
	 */
	private function display_route( $route_index = 'route-0', $route = [] ) {

		$connection_id = ! empty( $route['connection_id'] ) ? $route['connection_id'] : false;
		?>
		<div class="wp-mail-smtp-smart-routing-route">
			<div class="wp-mail-smtp-smart-routing-route__header">
				<span><?php esc_html_e( 'Send with', 'wp-mail-smtp-pro' ); ?></span>
				<?php $this->display_connection_select( $route_index, $connection_id ); ?>
				<span><?php esc_html_e( 'if the following conditions are met...', 'wp-mail-smtp-pro' ); ?></span>

				<div class="wp-mail-smtp-smart-routing-route__actions">
					<div class="wp-mail-smtp-smart-routing-route__order">
						<button class="wp-mail-smtp-smart-routing-route__order-btn wp-mail-smtp-smart-routing-route__order-btn--up">
							<img src="<?php echo esc_url( wp_mail_smtp()->assets_url . '/images/icons/arrow-up.svg' ); ?>" alt="<?php esc_attr_e( 'Arrow Up', 'wp-mail-smtp-pro' ); ?>">
						</button>
						<button class="wp-mail-smtp-smart-routing-route__order-btn wp-mail-smtp-smart-routing-route__order-btn--down">
							<img src="<?php echo esc_url( wp_mail_smtp()->assets_url . '/images/icons/arrow-up.svg' ); ?>" alt="<?php esc_attr_e( 'Arrow Down', 'wp-mail-smtp-pro' ); ?>">
						</button>
					</div>

					<button class="wp-mail-smtp-smart-routing-route__delete" <?php disabled( empty( $route ) ); ?>>
						<i class="dashicons dashicons-trash"></i>
					</button>
				</div>
			</div>
			<div class="wp-mail-smtp-smart-routing-route__main">

				<?php if ( ! empty( $connection_id ) && ! $this->additional_connections->connection_exists( $connection_id ) ) : ?>
					<div class="wp-mail-smtp-notice notice-error notice-inline wp-mail-smtp-smart-routing-route__notice wp-mail-smtp-smart-routing-route__notice--invalid">
						<p>
							<?php
							esc_html_e( 'These conditions will not be met because the connection no longer exists. Please select another connection.', 'wp-mail-smtp-pro' );
							?>
						</p>
					</div>
				<?php endif; ?>

				<?php
				$this->conditional_logic_settings->block(
					[
						'field_name'   => 'wp-mail-smtp[smart_routing][routes][' . $route_index . ']',
						'conditionals' => ! empty( $route['conditionals'] ) ? $route['conditionals'] : '',
					]
				);
				?>
			</div>
		</div>
		<?php
	}

	/**
	 * Output HTML of the connection select.
	 *
	 * @since 3.7.0
	 *
	 * @param string $route_index   Route index.
	 * @param string $connection_id Selected connection ID.
	 */
	private function display_connection_select( $route_index, $connection_id = false ) {

		static $connections = null;

		if ( is_null( $connections ) ) {
			$connections = $this->additional_connections->get_configured_connections();
		}

		$invalid_connection = ! empty( $connection_id ) && ! $this->additional_connections->connection_exists( $connection_id );
		?>
		<select name="wp-mail-smtp[smart_routing][routes][<?php echo esc_attr( $route_index ); ?>][connection_id]"
						id="wp-mail-smtp-setting-smart_routing_routes_<?php echo esc_attr( $route_index ); ?>_connection_id"
						class="wp-mail-smtp-smart-routing-route__connection<?php echo $invalid_connection ? ' wp-mail-smtp-smart-routing-route__connection--invalid' : ''; ?>"
						required>
			<option value=""><?php esc_html_e( '-- Select a Connection --', 'wp-mail-smtp-pro' ); ?></option>
			<?php foreach ( $connections as $connection ) : ?>
				<option value="<?php echo esc_attr( $connection->get_id() ); ?>" <?php selected( $connection_id, $connection->get_id() ); ?>>
					<?php echo esc_html( $connection->get_title() ); ?>
				</option>
			<?php endforeach; ?>
		</select>
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

		$options = Options::init();
		$all_opt = $options->get_all_raw();

		// Unchecked checkboxes doesn't exist in $_POST, so we need to ensure we actually have them in data to save.
		if ( empty( $data['smart_routing']['enabled'] ) ) {
			$data['smart_routing']['enabled'] = false;
		}

		// All options should be overwritten to prevent duplications.
		$all_opt['smart_routing'] = isset( $data['smart_routing'] ) ? $data['smart_routing'] : [];

		// All the sanitization is done there.
		$options->set( $all_opt );

		WP::add_admin_notice(
			esc_html__( 'Settings were successfully saved.', 'wp-mail-smtp-pro' ),
			WP::ADMIN_NOTICE_SUCCESS
		);
	}

	/**
	 * Get initiators choices for conditional.
	 *
	 * @since 3.7.0
	 *
	 * @return array
	 */
	private function get_initiators_choices() {

		if ( ! function_exists( 'get_plugins' ) ) {
			include ABSPATH . '/wp-admin/includes/plugin.php';
		}

		// WP Core.
		$initiators['wp-core'] = esc_html__( 'WP Core', 'wp-mail-smtp-pro' );

		// Theme.
		$theme = wp_get_theme();

		$initiators[ $theme->get_stylesheet() ] = sprintf(
			/* translators: %s - theme name. */
			esc_html__( 'Theme (%s)', 'wp-mail-smtp-pro' ),
			$theme->name
		);

		// Plugins.
		$plugins = wp_list_pluck(
			array_intersect_key( get_plugins(), array_flip( (array) get_option( 'active_plugins' ) ) ),
			'Name'
		);

		// Must use plugins.
		$mu_plugins = wp_list_pluck(
			get_mu_plugins(),
			'Name'
		);

		$initiators = array_merge( $initiators, $plugins, $mu_plugins );

		return $initiators;
	}
}
