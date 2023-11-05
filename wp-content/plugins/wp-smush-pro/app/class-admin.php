<?php
/**
 * Admin class.
 *
 * @package Smush\App
 */

namespace Smush\App;

use Smush\Core\Core;
use Smush\Core\Error_Handler;
use Smush\Core\Helper;
use Smush\Core\Settings;
use Smush\Core\Stats\Global_Stats;
use WP_Smush;

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class Admin
 */
class Admin {

	/**
	 * Plugin pages.
	 *
	 * @var array
	 */
	public $pages = array();

	/**
	 * AJAX module.
	 *
	 * @var Ajax
	 */
	public $ajax;

	/**
	 * List of smush settings pages.
	 *
	 * @var array $plugin_pages
	 */
	public static $plugin_pages = array(
		'gallery_page_wp-smush-nextgen-bulk',
		'nextgen-gallery_page_wp-smush-nextgen-bulk', // Different since NextGen 3.3.6.
		'toplevel_page_smush',
		'toplevel_page_smush-network',
	);

	/**
	 * Admin constructor.
	 *
	 * @param Media_Library $media_lib  Media uploads library.
	 */
	public function __construct( Media_Library $media_lib ) {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_action( 'admin_menu', array( $this, 'add_menu_pages' ) );
		add_action( 'network_admin_menu', array( $this, 'add_menu_pages' ) );

		add_action( 'admin_init', array( $this, 'smush_i18n' ) );
		// Add information to privacy policy page (only during creation).
		add_action( 'admin_init', array( $this, 'add_policy' ) );

		if ( wp_doing_ajax() ) {
			$this->ajax = new Ajax();
		}

		// Init media library UI.
		$media_lib->init_ui();

		add_filter( 'plugin_action_links_' . WP_SMUSH_BASENAME, array( $this, 'settings_link' ) );
		add_filter( 'network_admin_plugin_action_links_' . WP_SMUSH_BASENAME, array( $this, 'settings_link' ) );
		add_filter( 'plugin_row_meta', array( $this, 'add_plugin_meta_links' ), 10, 2 );

		// Prints a membership validation issue notice in Media Library.
		

		// Plugin conflict notice.
		add_action( 'admin_notices', array( $this, 'show_plugin_conflict_notice' ) );
		add_action( 'admin_notices', array( $this, 'show_parallel_unavailability_notice' ) );
		add_action( 'admin_notices', array( $this, 'show_background_unavailability_notice' ) );
		add_action( 'smush_check_for_conflicts', array( $this, 'check_for_conflicts_cron' ) );
		add_action( 'activated_plugin', array( $this, 'check_for_conflicts_cron' ) );
		add_action( 'deactivated_plugin', array( $this, 'check_for_conflicts_cron' ) );

		// Filter built-in wpmudev branding script.
		add_filter( 'wpmudev_whitelabel_plugin_pages', array( $this, 'builtin_wpmudev_branding' ) );

		add_action( 'wp_smush_header_notices', array( $this, 'show_php_deprecated_notice' ) );
	}

	/**
	 * Load translation files.
	 */
	public function smush_i18n() {
		load_plugin_textdomain(
			'wp-smushit',
			false,
			dirname( WP_SMUSH_BASENAME ) . '/languages'
		);
	}

	/**
	 * Register JS and CSS.
	 */
	private function register_scripts() {
		global $wp_version;
		/**
		 * Queue clipboard.js from your plugin if WP's version is below 5.2.0
		 * since it's only included from 5.2.0 on.
		 *
		 * Use 'clipboard' as the handle so it matches WordPress' handle for the script.
		 *
		 * @since 3.8.0
		 */
		if ( version_compare( $wp_version, '5.2', '<' ) ) {
			wp_register_script( 'clipboard', WP_SMUSH_URL . 'app/assets/js/smush-clipboard.min.js', array(), WP_SMUSH_VERSION, true );
		}

		/**
		 * Share UI JS.
		 *
		 * @since 3.8.0 added 'clipboard' dependency.
		 */
		wp_register_script( 'smush-sui', WP_SMUSH_URL . 'app/assets/js/smush-sui.min.js', array( 'jquery', 'clipboard' ), WP_SHARED_UI_VERSION, true );

		// Main JS.
		wp_register_script( 'smush-admin', WP_SMUSH_URL . 'app/assets/js/smush-admin.min.js', array( 'jquery', 'smush-sui', 'underscore', 'wp-color-picker' ), WP_SMUSH_VERSION, true );

		// JS that can be used on all pages in the WP backend.
		wp_register_script( 'smush-admin-common', WP_SMUSH_URL . 'app/assets/js/smush-admin-common.min.js', array( 'jquery' ), WP_SMUSH_VERSION, true );

		// Main CSS.
		wp_register_style( 'smush-admin', WP_SMUSH_URL . 'app/assets/css/smush-admin.min.css', array(), WP_SMUSH_VERSION );

		// Styles that can be used on all pages in the WP backend.
		wp_register_style( 'smush-admin-common', WP_SMUSH_URL . 'app/assets/css/smush-global.min.css', array(), WP_SMUSH_VERSION );

		// Dismiss update info.
		WP_Smush::get_instance()->core()->mod->smush->dismiss_update_info();
	}

	/**
	 * Enqueue scripts.
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'smush-global', WP_SMUSH_URL . 'app/assets/js/smush-global.min.js', array(), WP_SMUSH_VERSION, true );
		wp_localize_script( 'smush-global', 'smush_global', array(
			'nonce' => wp_create_nonce( 'wp-smush-ajax' ),
		) );

		$current_page   = '';
		$current_screen = '';

		if ( function_exists( 'get_current_screen' ) ) {
			$current_screen = get_current_screen();
			$current_page   = ! empty( $current_screen ) ? $current_screen->base : $current_page;
		}

		if ( ! in_array( $current_page, Core::$external_pages, true ) && false === strpos( $current_page, 'page_smush' ) ) {
			return;
		}

		// Allows to disable enqueuing smush files on a particular page.
		if ( ! apply_filters( 'wp_smush_enqueue', true ) ) {
			return;
		}

		$this->register_scripts();

		// Load on all Smush page only.
		if ( isset( $current_screen->id ) && ( in_array( $current_screen->id, self::$plugin_pages, true ) || false !== strpos( $current_screen->id, 'page_smush' ) ) ) {
			// Smush admin (smush-admin) includes the Shared UI.
			wp_enqueue_style( 'smush-admin' );
			wp_enqueue_script( 'smush-wpmudev-sui' );
		}

		if ( ! in_array( $current_page, array( 'post', 'post-new', 'page', 'edit-page' ), true ) ) {
			// Skip these pages where the script isn't used.
			wp_enqueue_script( 'smush-admin' );
		} else {
			// Otherwise, load only the common JS code.
			wp_enqueue_script( 'smush-admin-common' );
		}

		// We need it on media pages and Smush pages.
		wp_enqueue_style( 'smush-admin-common' );

		// Localize translatable strings for js.
		WP_Smush::get_instance()->core()->localize();
	}

	/**
	 * Adds a Smush pro settings link on plugin page.
	 *
	 * @param array $links  Current links.
	 *
	 * @return array|string
	 */
	public function settings_link( $links ) {
		// Upgrade link.
		if ( ! WP_Smush::is_pro() ) {
			$upgrade_url = add_query_arg(
				array(
					'utm_source'   => 'smush',
					'utm_medium'   => 'plugin',
					'utm_campaign' => 'wp-smush-pro/wp-smush.php' !== WP_SMUSH_BASENAME ? 'smush_pluginlist_upgrade' : 'smush_pluginlist_renew',
				),
				esc_url( 'https://wpmudev.com/project/wp-smush-pro/' )
			);

			$using_free_version = 'wp-smush-pro/wp-smush.php' !== WP_SMUSH_BASENAME;
			if ( $using_free_version ) {
				$label = __( 'Upgrade to Smush Pro', 'wp-smushit' );
				$text  = __( 'Upgrade for 60% off', 'wp-smushit' );
			} else {
				$label = __( 'Renew Membership', 'wp-smushit' );
				$text  = __( 'Renew Membership', 'wp-smushit' );
			}

			if ( isset( $text ) ) {
				$links['smush_upgrade'] = '<a href="' . esc_url( $upgrade_url ) . '" aria-label="' . esc_attr( $label ) . '" target="_blank" style="color: #8D00B1;">' . esc_html( $text ) . '</a>';
			}
		}

		// Documentation link.
		$links['smush_docs'] = '<a href="https://wpmudev.com/docs/wpmu-dev-plugins/smush/?utm_source=smush&utm_medium=plugin&utm_campaign=smush_pluginlist_docs" aria-label="' . esc_attr( __( 'View Smush Documentation', 'wp-smushit' ) ) . '" target="_blank">' . esc_html__( 'Docs', 'wp-smushit' ) . '</a>';

		// Settings link.
		$settings_page            = is_multisite() && is_network_admin() ? network_admin_url( 'admin.php?page=smush-settings' ) : menu_page_url( 'smush-settings', false );
		$links['smush_dashboard'] = '<a href="' . $settings_page . '" aria-label="' . esc_attr( __( 'Go to Smush settings', 'wp-smushit' ) ) . '">' . esc_html__( 'Settings', 'wp-smushit' ) . '</a>';

		$access = get_site_option( 'wp-smush-networkwide' );
		if ( ! is_network_admin() && is_plugin_active_for_network( WP_SMUSH_BASENAME ) && ! $access ) {
			// Remove settings link for subsites if Subsite Controls is not set on network permissions tab.
			unset( $links['smush_dashboard'] );
		}

		return array_reverse( $links );
	}

	/**
	 * Add additional links next to the plugin version.
	 *
	 * @since 3.5.0
	 *
	 * @param array  $links  Links array.
	 * @param string $file   Plugin basename.
	 *
	 * @return array
	 */
	public function add_plugin_meta_links( $links, $file ) {
		if ( ! defined( 'WP_SMUSH_BASENAME' ) || WP_SMUSH_BASENAME !== $file ) {
			return $links;
		}

		if ( 'wp-smush-pro/wp-smush.php' !== WP_SMUSH_BASENAME ) {
			$links[] = '<a href="https://wordpress.org/support/plugin/wp-smushit/reviews/#new-post" target="_blank" title="' . esc_attr__( 'Rate Smush', 'wp-smushit' ) . '">' . esc_html__( 'Rate Smush', 'wp-smushit' ) . '</a>';
			$links[] = '<a href="https://wordpress.org/support/plugin/wp-smushit/" target="_blank" title="' . esc_attr__( 'Support', 'wp-smushit' ) . '">' . esc_html__( 'Support', 'wp-smushit' ) . '</a>';
		} else {
			if ( isset( $links[2] ) && false !== strpos( $links[2], 'project/wp-smush-pro' ) ) {
				$links[2] = sprintf(
					'<a href="https://wpmudev.com/project/wp-smush-pro/" target="_blank">%s</a>',
					__( 'View details', 'wp-smushit' )
				);
			}

			$links[] = '<a href="https://wpmudev.com/get-support/" target="_blank" title="' . esc_attr__( 'Premium Support', 'wp-smushit' ) . '">' . esc_html__( 'Premium Support', 'wp-smushit' ) . '</a>';
		}

		$links[] = '<a href="https://wpmudev.com/roadmap/" target="_blank" title="' . esc_attr__( 'Roadmap', 'wp-smushit' ) . '">' . esc_html__( 'Roadmap', 'wp-smushit' ) . '</a>';

		return $links;
	}

	/**
	 * Add menu pages.
	 */
	public function add_menu_pages() {
		$title = 'wp-smush-pro/wp-smush.php' === WP_SMUSH_BASENAME ? esc_html__( 'Smush Pro', 'wp-smushit' ) : esc_html__( 'Smush', 'wp-smushit' );

		if ( Settings::can_access( false, true ) ) {
			$this->pages['smush']     = new Pages\Dashboard( 'smush', $title );
			$this->pages['dashboard'] = new Pages\Dashboard( 'smush', __( 'Dashboard', 'wp-smushit' ), 'smush' );

			if ( Abstract_Page::should_render( 'bulk' ) ) {
				$this->pages['bulk'] = new Pages\Bulk( 'smush-bulk', __( 'Bulk Smush', 'wp-smushit' ), 'smush' );
			}

			if ( Abstract_Page::should_render( 'directory' ) ) {
				$this->pages['directory'] = new Pages\Directory( 'smush-directory', __( 'Directory Smush', 'wp-smushit' ), 'smush' );
			}

			if ( Abstract_Page::should_render( 'lazy_load' ) ) {
				$this->pages['lazy-load'] = new Pages\Lazy( 'smush-lazy-load', __( 'Lazy Load', 'wp-smushit' ), 'smush' );
			}

			if ( Abstract_Page::should_render( 'cdn' ) ) {
				$this->pages['cdn'] = new Pages\CDN( 'smush-cdn', __( 'CDN', 'wp-smushit' ), 'smush' );
			}

			if ( Abstract_Page::should_render( 'webp' ) ) {
				$this->pages['webp'] = new Pages\WebP( 'smush-webp', __( 'Local WebP', 'wp-smushit' ), 'smush' );
			}

			if ( Abstract_Page::should_render( 'integrations' ) ) {
				$this->pages['integrations'] = new Pages\Integrations( 'smush-integrations', __( 'Integrations', 'wp-smushit' ), 'smush' );
			}

			if ( ! is_multisite() || is_network_admin() ) {
				$this->pages['settings'] = new Pages\Settings( 'smush-settings', __( 'Settings', 'wp-smushit' ), 'smush' );
			}

			if ( ! apply_filters( 'wpmudev_branding_hide_doc_link', false ) && Abstract_Page::should_render( 'tutorials' ) ) {
				$this->pages['tutorials'] = new Pages\Tutorials( 'smush-tutorials', __( 'Tutorials', 'wp-smushit' ), 'smush' );
			}

			if ( ! WP_Smush::is_pro() ) {
				$this->pages['smush-upgrade'] = new Pages\Upgrade( 'smush-upgrade', __( 'Smush Pro', 'wp-smushit' ), 'smush' );
			}
		}

		// Add a bulk smush option for NextGen gallery.
		if ( defined( 'NGGFOLDER' ) && WP_Smush::get_instance()->core()->nextgen->is_enabled() && WP_Smush::is_pro() && ! is_network_admin() ) {
			$this->pages['nextgen'] = new Pages\Nextgen( 'wp-smush-nextgen-bulk', $title, NGGFOLDER, true );
		}
	}

	/**
	 * Add Smush Policy to "Privacy Policy" page during creation.
	 *
	 * @since 2.3.0
	 */
	public function add_policy() {
		if ( ! function_exists( 'wp_add_privacy_policy_content' ) ) {
			return;
		}

		$content  = '<h3>' . __( 'Plugin: Smush', 'wp-smushit' ) . '</h3>';
		$content .=
			'<p>' . __( 'Note: Smush does not interact with end users on your website. The only input option Smush has is to a newsletter subscription for site admins only. If you would like to notify your users of this in your privacy policy, you can use the information below.', 'wp-smushit' ) . '</p>';
		$content .=
			'<p>' . __( 'Smush sends images to the WPMU DEV servers to optimize them for web use. This includes the transfer of EXIF data. The EXIF data will either be stripped or returned as it is. It is not stored on the WPMU DEV servers.', 'wp-smushit' ) . '</p>';
		$content .=
			'<p>' . sprintf( /* translators: %1$s - opening <a>, %2$s - closing </a> */
				__( "Smush uses the Stackpath Content Delivery Network (CDN). Stackpath may store web log information of site visitors, including IPs, UA, referrer, Location and ISP info of site visitors for 7 days. Files and images served by the CDN may be stored and served from countries other than your own. Stackpath's privacy policy can be found %1\$shere%2\$s.", 'wp-smushit' ),
				'<a href="https://www.stackpath.com/legal/privacy-statement/" target="_blank">',
				'</a>'
			) . '</p>';

		if ( strpos( WP_SMUSH_DIR, 'wp-smushit' ) !== false ) {
			// Only for wordpress.org members.
			$content .=
				'<p>' . __( 'Smush uses a third-party email service (Drip) to send informational emails to the site administrator. The administrator\'s email address is sent to Drip and a cookie is set by the service. Only administrator information is collected by Drip.', 'wp-smushit' ) . '</p>';
		}

		wp_add_privacy_policy_content(
			__( 'WP Smush', 'wp-smushit' ),
			wp_kses_post( wpautop( $content, false ) )
		);
	}

	/**
	 * Prints the Membership Validation issue notice
	 */
	public function media_library_membership_notice() {
		// No need to print it for free version.
		if ( ! WP_Smush::is_pro() ) {
			return;
		}

		// Show it on Media Library page only.
		$screen = get_current_screen();
		if ( ! empty( $screen ) && ( 'upload' === $screen->id || in_array( $screen->id, self::$plugin_pages, true ) || false !== strpos( $screen->id, 'page_smush' ) ) ) {
			?>
			<div id="wp-smush-invalid-member" data-message="<?php esc_attr_e( 'Validating...', 'wp-smushit' ); ?>" class="hidden notice notice-warning is-dismissible">
				<p>
					<?php
					printf(
					/* translators: $1$s: recheck link, $2$s: closing a tag, %3$s; contact link, %4$s: closing a tag */
						esc_html__(
							'It looks like Smush couldn’t verify your WPMU DEV membership so Pro features have been disabled for now. If you think this is an error, run a %1$sre-check%2$s or get in touch with our %3$ssupport team%4$s.',
							'wp-smushit'
						),
						'<a href="#" id="wp-smush-revalidate-member" data-message="%s">',
						'</a>',
						'<a href="https://wpmudev.com/contact" target="_blank">',
						'</a>'
					);
					?>
				</p>
			</div>
			<?php
		}
	}

	/**
	 * Check for plugin conflicts cron.
	 *
	 * @since 3.6.0
	 *
	 * @param string $deactivated  Holds the slug of activated/deactivated plugin.
	 */
	public function check_for_conflicts_cron( $deactivated = '' ) {
		$conflicting_plugins = array(
			'autoptimize/autoptimize.php',
			'ewww-image-optimizer/ewww-image-optimizer.php',
			'imagify/imagify.php',
			'resmushit-image-optimizer/resmushit.php',
			'shortpixel-image-optimiser/wp-shortpixel.php',
			'tiny-compress-images/tiny-compress-images.php',
			'wp-rocket/wp-rocket.php',
			'optimole-wp/optimole-wp.php',
			// lazy load plugins.
			'rocket-lazy-load/rocket-lazy-load.php',
			'a3-lazy-load/a3-lazy-load.php',
			'jetpack/jetpack.php',
			'sg-cachepress/sg-cachepress.php',
			'w3-total-cache/w3-total-cache.php',
			'wp-fastest-cache/wpFastestCache.php',
			'wp-optimize/wp-optimize.php',
			'nitropack/main.php',
		);

		$plugins = get_plugins();

		$active_plugins = array();
		foreach ( $conflicting_plugins as $plugin ) {
			if ( ! array_key_exists( $plugin, $plugins ) ) {
				continue;
			}

			if ( ! is_plugin_active( $plugin ) ) {
				continue;
			}

			// Deactivation of the plugin in process.
			if ( doing_action( 'deactivated_plugin' ) && $deactivated === $plugin ) {
				continue;
			}

			$active_plugins[] = $plugins[ $plugin ]['Name'];
		}

		set_transient( 'wp-smush-conflict_check', $active_plugins, 3600 );
	}

	/**
	 * Display plugin incompatibility notice.
	 *
	 * @since 3.6.0
	 */
	public function show_plugin_conflict_notice() {
		// Do not show on lazy load module, there we show an inline notice.
		if ( false !== strpos( get_current_screen()->id, 'page_smush-lazy-load' ) ) {
			return;
		}

		$dismissed = $this->is_notice_dismissed( 'plugin-conflict' );
		if ( $dismissed ) {
			return;
		}

		$conflict_check = get_transient( 'wp-smush-conflict_check' );

		// Have never checked before.
		if ( false === $conflict_check ) {
			wp_schedule_single_event( time(), 'smush_check_for_conflicts' );
			return;
		}

		// No conflicting plugins detected.
		if ( isset( $conflict_check ) && is_array( $conflict_check ) && empty( $conflict_check ) ) {
			return;
		}

		array_walk(
			$conflict_check,
			function( &$item ) {
				$item = '<strong>' . $item . '</strong>';
			}
		);
		?>
		<div class="notice notice-info is-dismissible smush-dismissible-notice"
			 id="smush-conflict-notice"
			 data-key="plugin-conflict">

			<p><?php esc_html_e( 'You have multiple WordPress image optimization plugins installed. This may cause unpredictable behavior while optimizing your images, inaccurate reporting, or images to not display. For best results use only one image optimizer plugin at a time. These plugins may cause issues with Smush:', 'wp-smushit' ); ?></p>
			<p>
				<?php echo wp_kses_post( join( '<br>', $conflict_check ) ); ?>
			</p>
			<p>
				<a href="<?php echo esc_url( admin_url( 'plugins.php' ) ); ?>" class="button button-primary">
					<?php esc_html_e( 'Manage Plugins', 'wp-smushit' ); ?>
				</a>
				<a href="#"
				   style="margin-left: 15px"
				   id="smush-dismiss-conflict-notice" class="smush-dismiss-notice-button">

					<?php esc_html_e( 'Dismiss', 'wp-smushit' ); ?>
				</a>
			</p>
		</div>
		<?php
	}

	/**
	 * Prints the content for pending images for the Bulk Smush section.
	 *
	 * @param int $remaining_count
	 * @param int $reoptimize_count
	 * @param int $optimize_count
	 *
	 * @since 3.7.2
	 */
	public function print_pending_bulk_smush_content( $remaining_count, $reoptimize_count, $optimize_count ) {
		$optimize_message = '';
		if ( 0 < $optimize_count ) {
			$optimize_message = sprintf(
				/* translators: 1. opening strong tag, 2: unsmushed images count,3. closing strong tag. */
				esc_html( _n( '%1$s%2$d attachment%3$s that needs smushing', '%1$s%2$d attachments%3$s that need smushing', $optimize_count, 'wp-smushit' ) ),
				'<strong>',
				absint( $optimize_count ),
				'</strong>'
			);
		}

		$reoptimize_message = '';
		if ( 0 < $reoptimize_count ) {
			$reoptimize_message = sprintf(
				/* translators: 1. opening strong tag, 2: re-smush images count,3. closing strong tag. */
				esc_html( _n( '%1$s%2$d attachment%3$s that needs re-smushing', '%1$s%2$d attachments%3$s that need re-smushing', $reoptimize_count, 'wp-smushit' ) ),
				'<strong>',
				esc_html( $reoptimize_count ),
				'</strong>'
			);
		}

		$bulk_limit_free_message = $this->generate_bulk_limit_message_for_free( $remaining_count );

		$image_count_description = sprintf(
			/* translators: 1. username, 2. unsmushed images message, 3. 'and' text for when having both unsmushed and re-smush images, 4. re-smush images message. */
			__( '%1$s, you have %2$s%3$s%4$s! %5$s', 'wp-smushit' ),
			esc_html( Helper::get_user_name() ),
			$optimize_message,
			( $optimize_message && $reoptimize_message ? esc_html__( ' and ', 'wp-smushit' ) : '' ),
			$reoptimize_message,
			$bulk_limit_free_message
		);
		?>
		<span id="wp-smush-bulk-image-count"><?php echo esc_html( $remaining_count ); ?></span>
		<p id="wp-smush-bulk-image-count-description">
			<?php echo wp_kses_post( $image_count_description ); ?>
		</p>
		<?php
	}

	public function get_global_stats_with_bulk_smush_content() {
		$core             = WP_Smush::get_instance()->core();
		$stats            = $core->get_global_stats();
		$global_stats     = Global_Stats::get();
		$remaining_count  = $global_stats->get_remaining_count();
		$optimize_count   = $global_stats->get_optimize_list()->get_count();
		$reoptimize_count = $global_stats->get_redo_count();

		$stats['errors']  = Error_Handler::get_last_errors();

		if ( $remaining_count > 0 ) {
			ob_start();
			WP_Smush::get_instance()->admin()->print_pending_bulk_smush_content(
				$remaining_count,
				$reoptimize_count,
				$optimize_count
			);
			$content          = ob_get_clean();
			$stats['content'] = $content;
		}

		return $stats;
	}

	public function get_global_stats_with_bulk_smush_content_and_notice() {
		$stats = $this->get_global_stats_with_bulk_smush_content();
		$remaining_count  = Global_Stats::get()->get_remaining_count();
		if ( $remaining_count < 1 ) {
			$stats['notice']     = esc_html__( 'Yay! All images are optimized as per your current settings.', 'wp-smushit' );
			$stats['noticeType'] = 'success';
		} else {
			$stats['noticeType'] = 'warning';
			$stats['notice']     = sprintf(
				/* translators: %1$d - number of images, %2$s - opening a tag, %3$s - closing a tag */
				esc_html__( 'Image check complete, you have %1$d images that need smushing. %2$sBulk smush now!%3$s', 'wp-smushit' ),
				$remaining_count,
				'<a href="#" class="wp-smush-trigger-bulk">',
				'</a>'
			);
		}

		return $stats;
	}

	private function generate_bulk_limit_message_for_free( $remaining_count ) {
		$dont_limit = WP_Smush::get_instance()->core()->mod->bg_optimization->can_use_background();
		if ( $dont_limit || $remaining_count < Core::MAX_FREE_BULK ) {
			return '';
		}

		$upgrade_url   = add_query_arg(
			array(
				'utm_source'   => 'smush',
				'utm_medium'   => 'plugin',
				'utm_campaign' => 'smush_bulk_smush_pre_smush_50_limit',
			),
			'https://wpmudev.com/project/wp-smush-pro/'
		);
		$batches       = ceil( $remaining_count / Core::MAX_FREE_BULK );
		$discount_text = '<strong>'. esc_html__( '60% off welcome discount available.', 'wp-smushit' ) .'</strong>';
		return sprintf(
		/* translators: 1: max free bulk limit, 2: Total batches to smush, 3: opening a tag, 4: closing a tag. */
			esc_html__( 'Free users can only Bulk Smush %1$d images at one time. Smush in %2$d batches or %3$sBulk Smush unlimited images with Pro%4$s. %5$s', 'wp-smushit' ),
			Core::MAX_FREE_BULK,
			$batches,
			'<a class="smush-upsell-link" target="_blank" href="' . $upgrade_url . '">',
			'</a>',
			$discount_text
		);
	}

	/**
	 * Add more pages to builtin wpmudev branding.
	 *
	 * @since 3.0
	 *
	 * @param array $plugin_pages  Nextgen pages is not introduced in built in wpmudev branding.
	 *
	 * @return array
	 */
	public function builtin_wpmudev_branding( $plugin_pages ) {
		$plugin_pages['gallery_page_wp-smush-nextgen-bulk'] = array(
			'wpmudev_whitelabel_sui_plugins_branding',
			'wpmudev_whitelabel_sui_plugins_footer',
			'wpmudev_whitelabel_sui_plugins_doc_links',
		);

		// There's a different page ID since NextGen 3.3.6.
		$plugin_pages['nextgen-gallery_page_wp-smush-nextgen-bulk'] = array(
			'wpmudev_whitelabel_sui_plugins_branding',
			'wpmudev_whitelabel_sui_plugins_footer',
			'wpmudev_whitelabel_sui_plugins_doc_links',
		);

		foreach ( $this->pages as $key => $value ) {
			$plugin_pages[ "smush-pro_page_smush-{$key}" ] = array(
				'wpmudev_whitelabel_sui_plugins_branding',
				'wpmudev_whitelabel_sui_plugins_footer',
				'wpmudev_whitelabel_sui_plugins_doc_links',
			);
		}

		return $plugin_pages;
	}

	public function is_notice_dismissed( $notice ) {
		$dismissed_notices = get_option( 'wp-smush-dismissed-notices', array() );

		return ! empty( $dismissed_notices[ $notice ] );
	}

	public function show_parallel_unavailability_notice() {
		$smush                     = WP_Smush::get_instance()->core()->mod->smush;
		$curl_multi_exec_available = $smush->curl_multi_exec_available();
		$is_current_user_not_admin = ! current_user_can( 'manage_options' );
		$is_not_bulk_smush_page    = false === strpos( get_current_screen()->id, 'page_smush-bulk' );
		$notice_hidden             = $this->is_notice_dismissed( 'curl-multi-unavailable' );

		if (
			$curl_multi_exec_available ||
			$is_current_user_not_admin ||
			$is_not_bulk_smush_page ||
			$notice_hidden
		) {
			return;
		}

		$notice_text = sprintf(
			/* translators: %s: <strong>curl_multi_exec()</strong> */
			esc_html__( 'Smush was unable to activate parallel processing on your site as your web hosting provider has disabled the %s function on your server. We highly recommend contacting your hosting provider to enable that function to optimize images on your site faster.', 'wp-smushit' ),
			'<strong>curl_multi_exec()</strong>'
		);

		?>
		<div class="notice notice-warning is-dismissible smush-dismissible-notice"
			 id="smush-parallel-unavailability-notice"
			 data-key="curl-multi-unavailable">

			<strong style="font-size: 15px;line-height: 30px;margin: 8px 0 0 2px;display: inline-block;">
				<?php esc_html_e( 'Smush images faster with parallel image optimization', 'wp-smushit' ); ?>
			</strong>
			<br/>
			<p style="margin-bottom: 13px;margin-top: 0;">
				<?php echo wp_kses_post( $notice_text ); ?><br/>

				<a style="margin-top: 5px;display: inline-block;" href="#" class="smush-dismiss-notice-button">
					<?php esc_html_e( 'Dismiss', 'wp-smushit' ); ?>
				</a>
			</p>
		</div>
		<?php
	}

	public function show_background_unavailability_notice() {
		$bg_optimization           = WP_Smush::get_instance()->core()->mod->bg_optimization;
		$background_supported      = $bg_optimization->is_background_supported();
		$background_disabled       = ! $bg_optimization->is_background_enabled();
		$is_current_user_not_admin = ! current_user_can( 'manage_options' );
		$is_not_bulk_smush_page    = false === strpos( get_current_screen()->id, 'page_smush-bulk' );
		$notice_hidden             = $this->is_notice_dismissed( 'background-smush-unavailable' );

		if (
			$background_supported ||
			$background_disabled ||
			$is_current_user_not_admin ||
			$is_not_bulk_smush_page ||
			$notice_hidden
		) {
			return;
		}

		$notice_text = sprintf(
			/* translators: 1: Current MYSQL version, 2: Required MYSQL version */
			esc_html__( 'Smush was unable to activate background processing on your site as your web hosting provider is using an old version of MySQL on your server (version %1$s). We highly recommend contacting your hosting provider to upgrade MySQL to version %2$s or higher to optimize images in the background.', 'wp-smushit' ),
			$bg_optimization->get_actual_mysql_version(),
			$bg_optimization->get_required_mysql_version()
		);
		?>
		<div class="notice notice-warning is-dismissible smush-dismissible-notice"
		     id="smush-background-unavailability-notice"
		     data-key="background-smush-unavailable">

			<strong style="font-size: 15px;line-height: 30px;margin: 8px 0 0 2px;display: inline-block;">
				<?php esc_html_e( 'Smush images in the background', 'wp-smushit' ); ?>
			</strong>
			<br/>
			<p style="margin-bottom: 13px;margin-top: 0;">
				<?php echo wp_kses_post( $notice_text ); ?><br/>

				<a style="margin-top: 5px;display: inline-block;" href="#" class="smush-dismiss-notice-button">
					<?php esc_html_e( 'Dismiss', 'wp-smushit' ); ?>
				</a>
			</p>
		</div>
		<?php
	}

	public function show_php_deprecated_notice() {
		// Only show the deprecated notice for admin and only network side for MU site.
		if ( ! current_user_can( 'manage_options' ) || ( is_multisite() && ! is_network_admin() )  ) {
			return;
		}

		if ( $this->is_notice_dismissed( 'php_deprecated' ) ) {
			return;
		}

		$php_version          = PHP_VERSION;
		$required_php_version = '7.0';
		if ( version_compare( $php_version, $required_php_version, '>=' ) ) {
			return;
		}
		/* translators: %1$s: Current PHP version */
		$error_message = sprintf( esc_html__( 'We have noticed that you are using PHP %1$s, which has reached its end of life and is now highly insecure. Smush will stop supporting PHP %1$s soon, please contact your hosting provider to switch to a more recent version of PHP.', 'wp-smushit' ), $php_version );
		$error_message = '<p>' . $error_message . '</p>';
		?>
		<div role="alert" id="wp-smush-php-deprecated-notice" class="sui-notice wp-smush-dismissible-header-notice smush-dismissible-notice" data-dismiss-key="php_deprecated" data-message="<?php echo esc_attr( $error_message ); ?>" aria-live="assertive"></div>
		<?php
	}
}