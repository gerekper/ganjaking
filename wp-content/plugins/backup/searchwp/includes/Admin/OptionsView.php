<?php

/**
 * SearchWP OptionsView.
 *
 * @package SearchWP
 * @author  Jon Christopher
 */

namespace SearchWP\Admin;

use SearchWP\Utils;
use SearchWP\Settings;
use SearchWP\Statistics;
use SearchWP\Admin\Views\EnginesView;
use SearchWP\Admin\Views\SupportView;
use SearchWP\Admin\Views\SettingsView;
use SearchWP\Admin\Views\AdvancedView;
use SearchWP\Admin\Views\StatisticsView;

/**
 * Class OptionsView is responsible for implementing the options screen into the WordPress Admin area.
 *
 * @since 4.0
 */
class OptionsView {

	/**
	 * Slug for this view.
	 *
	 * @since 4.0
	 * @var string
	 */
	private static $slug;

	private $extensions;

	/**
	 * OptionsView constructor.
	 *
	 * @since 4.0
	 */
	function __construct() {
		self::$slug = Utils::$slug;

		add_action( 'admin_menu', [ __CLASS__, 'add' ] );
		add_action( 'admin_enqueue_scripts', [ __CLASS__, 'assets' ], 999 );

		add_action( 'network_admin_menu', function() {
			add_menu_page(
				'SearchWP',
				'SearchWP',
				Settings::get_capability(),
				self::$slug,
				function() {
					do_action( 'searchwp\debug\log', 'Displaying network options page', 'settings' );
					?>
					<div class="wrap">
						<div style="max-width: 60em;">
							<h1>SearchWP</h1>
							<?php
							echo wp_kses(
								__( '<p>Cross-site searches are possible in SearchWP. Any Engine from any site can be used for a cross-site search.</p><p><strong>Note</strong>: SearchWP\'s Engines control what is indexed on each sub-site. If the Engine you are using to perform the search has different Sources/Attributes/Rules than the Engine(s) on the sub-sites you are searching the <em>results may not be accurate</em>.</p><p>For example: if Posts have been added to the Engine you are using for the search, but a sub-site does not have an Engine with Posts enabled, <strong>that sub-site will not return Posts</strong>.</p><p>For a comprehensive cross-site search, ensure that <em>all sites</em> share a similar configuration and applicable Engine.</p>', 'searchwp' ),
								[
									'p'      => [],
									'strong' => [],
									'em'     => [],
								]
							);
							?>
						</div>
					</div>
					<?php
				}
			);
		} );

		// Add internal tabs.
		do_action( 'searchwp\settings\nav\before' );

		if ( apply_filters( 'searchwp\settings\nav\engines', true ) ) {
			new EnginesView();
			do_action( 'searchwp\settings\nav\engines' );
		}

		if ( apply_filters( 'searchwp\settings\nav\settings', true ) ) {
			new SettingsView();
			do_action( 'searchwp\settings\nav\settings' );
		}

		if ( apply_filters( 'searchwp\settings\nav\advanced', true ) ) {
			new AdvancedView();
			do_action( 'searchwp\settings\nav\advanced' );
		}

		if ( apply_filters( 'searchwp\settings\nav\statistics', true ) ) {
			new StatisticsView();
			do_action( 'searchwp\settings\nav\statistics' );
		}

		if ( apply_filters( 'searchwp\settings\nav\support', true ) ) {
			new SupportView();
			do_action( 'searchwp\settings\nav\support' );
		}

		do_action( 'searchwp\settings\nav\after' );

		// Add Extensions Tab and callbacks.
		// TODO: Extension handling can (should) be its own class.
		$this->init_extensions();

		do_action( 'searchwp\settings\init' );
	}

	/**
	 * Initialize all registered Extensions.
	 *
	 * @since 4.0
	 * @return void
	 */
	private function init_extensions() {
		$this->prime_extensions();

		if ( ! empty( $this->extensions ) ) {
			new NavTab([
				'tab'   => 'extensions',
				'label' => __( 'Extensions', 'searchwp' ),
				'icon'  => 'dashicons dashicons-arrow-down',
			]);
		}

		add_action( 'searchwp\settings\view\extensions', [ $this, 'render_extension_view' ] );
		add_action( 'searchwp\settings\footer',          [ $this, 'render_extensions_dropdown' ] );
	}

	/**
	 * Renders the Extensions dropdown on the Options screen.
	 *
	 * @since 4.0
	 * @return void
	 */
	public function render_extensions_dropdown() {
		if ( empty( $this->extensions ) ) {
			return;
		}

		?>
		<div id="searchwp-extensions-dropdown">
			<ul class="swp-dropdown-menu">
				<?php foreach ( $this->extensions as $extension ) : ?>
					<?php if ( ! empty( $extension->public ) && isset( $extension->slug ) && isset( $extension->name ) ) : ?>
						<?php
						$the_link = add_query_arg(
							array(
								'page'      => 'searchwp',
								'tab'       => 'extensions',
								'extension' => $extension->slug,
							),
							admin_url( 'options-general.php' )
						);
						?>
						<li><a href="<?php echo esc_url( $the_link ); ?>"><?php echo esc_html( $extension->name ); ?></a></li>
					<?php endif; ?>
				<?php endforeach; ?>
			</ul>
		</div>
		<script type="text/javascript">
		jQuery(document).ready(function($){
			$('.searchwp-settings-nav-tab-extensions').after($('#searchwp-extensions-dropdown').clone());
		});
			jQuery(document).ready(function($){
				var $extensions_toggler = $('.searchwp-settings-nav-tab-extensions');
				var $extensions_dropdown = $('#searchwp-extensions-dropdown');

				$extensions_dropdown.hide();
				$extensions_toggler.data('showing',false);

				// Bind the click.
				$extensions_toggler.click(function(e){
					e.preventDefault();
					if ($extensions_toggler.data('showing')){
						$extensions_dropdown.hide();
						$extensions_toggler.data('showing',false);
						$extensions_toggler.removeClass('searchwp-showing-dropdown');
						$extensions_dropdown.removeClass('searchwp-sub-menu-active');
					} else {
						if ($extensions_toggler.hasClass('nav-tab-active')) {
							$extensions_dropdown.addClass('searchwp-sub-menu-active');
						} else {
							$extensions_dropdown.removeClass('searchwp-sub-menu-active');
						}
						$extensions_dropdown.show();
						$extensions_toggler.data('showing',true);
						$extensions_toggler.addClass('searchwp-showing-dropdown');
					}
				});
			});
		</script>
		<?php
	}

	/**
	 * Renders the view for an Extension.
	 *
	 * @since 4.0
	 * @return void
	 */
	public function render_extension_view() {
		if ( empty( $this->extensions ) || ! isset( $_GET['extension'] ) ) {
			return;
		}

		// Find out which extension we're working with.
		$extension = array_filter( $this->extensions, function( $attributes, $extension ) {
			return isset( $attributes->slug ) && $attributes->slug === $_GET['extension'] && method_exists( $this->extensions[ $extension ], 'view' );
		}, ARRAY_FILTER_USE_BOTH );

		if ( empty( $extension ) ) {
			return;
		}

		reset( $extension );
		$extension = key( $extension );
		$attributes = $this->extensions[ $extension ];
		?>
		<div class="wrap" id="searchwp-<?php echo esc_attr( $attributes->slug ); ?>-wrapper">
			<div id="icon-options-general" class="icon32"><br /></div>
			<div class="<?php echo esc_attr( $attributes->slug ); ?>-container">
				<h2>SearchWP <?php echo esc_html( $attributes->name ); ?></h2>
				<?php $this->extensions[ $extension ]->view(); ?>
			</div>
			<p class="searchwp-extension-back">
				<a href="<?php echo esc_url( admin_url( 'options-general.php?page=searchwp' ) ); ?>"><?php esc_html_e( 'Back to SearchWP Settings', 'searchwp' ); ?></a>
			</p>
		</div>
		<?php
	}

	/**
	 * Prime and prepare registered Extensions.
	 *
	 * @since 4.0
	 * @return void
	 */
	private function prime_extensions() {
		$extensions = apply_filters( 'searchwp\extensions', [] );

		if ( ! is_array( $extensions ) || empty( $extensions ) ) {
			return;
		}

		foreach ( $extensions as $extension => $path ) {
			$class_name = 'SearchWP' . $extension;

			if ( ! class_exists( $class_name ) && file_exists( $path ) ) {
				include_once( $path );
			}

			$this->extensions[ $extension ] = new $class_name( $this->extensions );

			// Add plugin row action.
			if ( isset( $this->extensions[ $extension ]->min_searchwp_version )
				&& version_compare( SEARCHWP_VERSION, $this->extensions[ $extension ]->min_searchwp_version, '<' ) ) {
				do_action( 'searchwp\debug\log', 'after_plugin_row_' . plugin_basename( $path ) );
				add_action( 'after_plugin_row_' . plugin_basename( $path ), [ $this, 'plugin_row' ], 11, 3 );
			}
		}
	}

	/**
	 * Enqueue the assets related to the OptionsView.
	 *
	 * @since 4.0
	 * @param $hook
	 */
	public static function assets( $hook ) {
		if ( 'settings_page_' . self::$slug !== $hook ) {
			return;
		}

		wp_enqueue_style(
			self::$slug . '_admin_settings',
			SEARCHWP_PLUGIN_URL . 'assets/styles/settings.css',
			false,
			SEARCHWP_VERSION
		);

		wp_enqueue_script( 'jquery' );
	}

	/**
	 * Adds SearchWP entry to Settings menu. Implements Settings UI.
	 *
	 * @since 4.0
	 * @return void
	 */
	public static function add() {
		global $submenu;

		if ( apply_filters( 'searchwp\options\settings_screen', true ) ) {
			add_options_page(
				'SearchWP',
				'SearchWP',
				Settings::get_capability(),
				self::$slug,
				function() {
					do_action( 'searchwp\settings\page' );
					do_action( 'searchwp\debug\log', 'Displaying options page', 'settings' );
					?>
					<div class="wrap">
						<?php
							self::header();
							self::view();
							self::footer();
						?>
					</div>
					<?php
				}
			);
		}

		if ( apply_filters( 'searchwp\options\dashboard_stats_link', true ) ) {
			add_dashboard_page(
				__( 'Search Statistics', 'searchwp' ),
				__( 'Search Stats', 'searchwp' ),
				Statistics::$capability,
				self::$slug . '-stats',
				function() {}
			);

			// Override the link for the Search Stats Admin Menu entry.
			if ( is_array( $submenu ) && array_key_exists( 'index.php', $submenu ) ) {
				foreach ( $submenu['index.php'] as $index => $dashboard_submenu ) {
					if ( 'searchwp-stats' !== $dashboard_submenu[2] ) {
						continue;
					}

					$submenu['index.php'][ $index ][2] = esc_url_raw( add_query_arg( [
						'page' => self::$slug,
						'tab'  => 'statistics',
					], admin_url( 'options-general.php' ) ) );

					break;
				}
			}
		}

	}

	/**
	 * Renders the main view.
	 *
	 * @since 4.0
	 * @return void
	 */
	public static function view() {
		$view = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'default';

		do_action( 'searchwp\settings\before\\' . $view );
		do_action( 'searchwp\settings\view\\' . $view );
		do_action( 'searchwp\settings\after\\' . $view );
	}

	/**
	 * Renders the footer.
	 *
	 * @since 4.0
	 * @return void
	 */
	public static function footer() {
		?>
		</div> <!-- /.searchwp-settings-view -->
		<?php
		do_action( 'searchwp\settings\footer' );
	}

	/**
	 * Renders the header.
	 *
	 * @since 4.0
	 * @return void
	 */
	public static function header() {
		do_action( 'searchwp\settings\header\before' );

		?>
			<div class="searchwp-settings-header postbox">
				<p class="searchwp-logo" title="SearchWP">
					<svg width="43" height="66" viewBox="0 0 43 66" xmlns="http://www.w3.org/2000/svg"><title>SearchWP</title><g transform="translate(.6567 .9104)" fill="none" fill-rule="evenodd"><ellipse stroke="#839788" stroke-width=".6687" fill="#FAFAFA" cx="21.0092" cy="34.1409" rx="12.6604" ry="26.8334"/><path d="M8.1347 44.5495s5.625-5.7126 11.822-10.7107c6.6311-5.3483 13.8342-9.982 13.8342-9.982" stroke="#839788" stroke-width="1.4079"/><path d="M34.005 44.5495S28.38 38.837 22.183 33.8388c-6.6312-5.3483-13.8343-9.982-13.8343-9.982" stroke="#839788" stroke-width="1.4079"/><path d="M36.7457 10.3164c.6243 0 1.1713.5848 1.2204 1.3062 0 0 2.8327 10.052 3.5164 20.1006.7785 11.441-.592 22.8786-.592 22.8786.0491.7214-.512 1.3061-1.2531 1.3061H2.244c-.7404 0-1.3011-.5847-1.2507-1.3061 0 0-1.5704-11.3114-.762-22.8786.6934-9.9224 3.7655-20.1006 3.7655-20.1006.0504-.7214.5979-1.3062 1.2227-1.3062h31.5262zM7.1515 15.3825s-1.4238 9.3137-1.8953 17.7297c-.4761 8.4966 0 16.9126 0 16.9126-.0405.7222.5179 1.3077 1.2461 1.3077h28.8853c.7286 0 1.2876-.5853 1.2485-1.3077 0 0 .4603-8.4194 0-16.9126-.4563-8.4194-1.8331-17.7297-1.8331-17.7297-.0392-.7222-.5867-1.3076-1.2222-1.3076H8.376c-.6358 0-1.184.5853-1.2245 1.3076z" stroke="#839788" stroke-width=".6687" fill="#BFCDC2"/><path d="M8.506 55.506l-.7835 4.881c-.1192.7427.4395 1.3447 1.2503 1.3447h23.9817c.8097 0 1.3825-.6035 1.2798-1.3447l-.6761-4.881c-.1029-.7427-.7864-1.3448-1.529-1.3448H10.0645c-.7416 0-1.4397.6035-1.5586 1.3447z" stroke="#839788" stroke-width=".6687" fill="#BFCDC2"/><path d="M3.7322 61.7385l-.4483 1.3487c-.2469.7425.1543 1.3445.894 1.3445h33.7064c.7406 0 1.1222-.6023.8526-1.3445l-.49-1.3487c-.2696-.7425-1.0431-1.3445-1.7257-1.3445H5.4165c-.6834 0-1.4376.6023-1.6843 1.3445zM9.2265 8.223L8.78 14.785c-.0505.7431.5087 1.3455 1.2513 1.3455h21.9645c.7416 0 1.2964-.6035 1.2393-1.3454l-.505-6.562c-.0571-.7431-.671-1.3455-1.3732-1.3455H10.5877c-.7012 0-1.3108.6036-1.3612 1.3455zM15.6424 1.3444l-.4105 1.8535c-.1645.7425.303 1.3444 1.0434 1.3444h9.3913c.7406 0 1.2241-.6032 1.0805-1.3444l-.3593-1.8535C26.2438.602 25.6202 0 24.9956 0h-7.924c-.6249 0-1.265.6032-1.4292 1.3444z" stroke="#839788" stroke-width=".6687" fill="#BFCDC2"/><rect stroke="#839788" stroke-width=".6687" fill="#BFCDC2" x="11.7738" y="4.0836" width="18.7811" height="3.3647" rx="1.6823"/><ellipse fill="#839788" cx="28.7603" cy="5.8778" rx="1" ry="1"/><ellipse fill="#839788" cx="27.2618" cy="5.8778" rx="1" ry="1"/><ellipse fill="#839788" cx="25.3352" cy="5.8778" rx="1" ry="1"/><ellipse fill="#839788" cx="23.8367" cy="5.8778" rx="1" ry="1"/><ellipse fill="#839788" cx="22.1241" cy="5.8778" rx="1" ry="1"/><ellipse fill="#839788" cx="20.4115" cy="5.8778" rx="1" ry="1"/><ellipse fill="#839788" cx="18.699" cy="5.8778" rx="1" ry="1"/><ellipse fill="#839788" cx="16.9864" cy="5.8778" rx="1" ry="1"/><ellipse fill="#839788" cx="15.4879" cy="5.8778" rx="1" ry="1"/><ellipse fill="#839788" cx="13.7754" cy="5.8778" rx="1" ry="1"/><path d="M10.5742 16.2767L9.0282 19.14c-.175.3242.2832.587 1.0258.587h21.9645c.7417 0 1.1952-.2633 1.0137-.587l-1.6055-2.8634c-.1818-.3243-.8211-.5871-1.4299-.5871H11.992c-.608 0-1.243.2633-1.4177.587zM31.489 51.3789l1.5459-2.8634c.175-.3243-.2832-.5871-1.0258-.5871H10.0445c-.7416 0-1.1951.2633-1.0136.587l1.6055 2.8635c.1818.3242.8211.587 1.4299.587h18.0049c.608 0 1.243-.2633 1.4177-.587z" stroke="#839788" stroke-width=".6687" fill="#BFCDC2"/></g></svg>
				</p>
				<nav class="searchwp-settings-header-nav">
					<button class="button searchwp-settings-header-nav-toggle">Menu</button>
					<ul>
						<?php do_action( 'searchwp\settings\nav\tab' ); ?>
					</ul>
				</nav>
				<script>
					jQuery(document).ready(function($){
						$('.searchwp-settings-header-nav-toggle').click(function(e){
							e.preventDefault();
							$('.searchwp-settings-header-nav > ul').toggle();
						});
					});
				</script>
			</div>
			<div class="searchwp-settings-view">
		<?php

		do_action( 'searchwp\settings\header\after' );
	}
}
