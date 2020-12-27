<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

if ( ! class_exists( 'GroovyMenuSettings' ) ) {

	/**
	 * Class GroovyMenuSettings
	 */
	class GroovyMenuSettings {
		/**
		 * @var GroovyMenuStyle
		 */
		protected $settings;

		protected $remote_child_themes_url = 'https://updates.grooni.com/theme-demos/gm-child-themes/config/';

		public function __construct() {

			if ( class_exists( 'GroovyMenuRoleCapabilities' ) ) {
				GroovyMenuRoleCapabilities::check_capabilities();
			}

			$style = new GroovyMenuStyle();

			add_action( 'wp_ajax_gm_save', array( $this, 'saveSettings' ) );

			add_action( 'wp_ajax_gm_save_styles', array( $this, 'saveStyles' ) );
			add_action( 'wp_ajax_nopriv_gm_save_styles', array( $this, 'saveStyles' ) );

			add_action( 'wp_ajax_gm_save_auto_integration', array( $this, 'saveAutoIntegration' ) );

			add_action( 'wp_ajax_gm_save_single_location_integration', array(
				$this,
				'saveSingleLocationIntegration'
			) );

			add_action( 'wp_ajax_gm_check_current_license', array( $this, 'checkCurrentLicense' ) );

			add_action( 'wp_ajax_gm_get_setting', array( $this, 'getSettings' ) );
			add_action( 'wp_ajax_nopriv_gm_get_setting', array( $this, 'getSettings' ) );

			add_action( 'admin_init', array( $this, 'start_ob' ) );

			add_action( 'admin_menu', array( $this, 'addThemesPage' ) );

			add_action( 'wp_ajax_gm_get_google_fonts', array( $this, 'getGoogleFonts' ) );

			add_image_size(
				'menu-thumb',
				$style->get( 'general', 'preview_width' ),
				$style->get( 'general', 'preview_height' ),
				true
			);

			if ( ! is_admin() ) {
				add_action( 'admin_bar_menu', array( $this, 'addToolbarLink' ), 100001 );
			} else {
				add_action( 'admin_head', array( $this, 'dismiss_notice_msg' ), 7 );
				add_action( 'admin_head', array( $this, 'late_start' ), 8 );
			}

			if ( function_exists( 'wp_doing_ajax' ) && wp_doing_ajax() ) {
				//call our function when initiated from JavaScript.
				add_action( 'wp_ajax_gm_admin_walker_priority_change', array(
					$this,
					'gm_admin_walker_priority_change'
				) );
			}

			add_filter( 'pre_get_posts', array( $this, 'search_filter' ) );

			GroovyMenuUtils::groovy_wpml_register_single_string( $style );

		}

		/**
		 * Dismiss notice message
		 */
		public function dismiss_notice_msg() {
			if ( isset( $_GET['gm-upgrade-theme'] ) && 'yes' === $_GET['gm-upgrade-theme'] ) {
				update_user_meta( get_current_user_id(), 'gm-upgrade-theme', true );
			}
		}

		public function late_start() {
			if ( ! is_admin() ) {
				return;
			}

			global $gm_supported_module;

			if ( 'crane' === $gm_supported_module['theme'] && defined( 'CRANE_THEME_VERSION' ) ) {

				$need_upgrade    = false;
				$minimum_version = '1.4';

				if ( version_compare( $minimum_version, CRANE_THEME_VERSION, '>' ) ) {
					$need_upgrade = true;
				}

				$is_upgrade_dismissed = get_user_meta( get_current_user_id(), 'gm-upgrade-theme', true );

				if ( ! $is_upgrade_dismissed && $need_upgrade ) {
					add_action( 'admin_notices', array( $this, 'show_gm_need_upgrade' ), 9 );
				}
			}


			$screen = get_current_screen();

			if ( 'nav-menus' === $screen->id ) {
				global $wp_filter;
				if ( isset( $wp_filter['wp_edit_nav_menu_walker'] ) ) {

					$other_priorities      = false;
					$admin_walker_priority = false;

					if ( is_object( $wp_filter['wp_edit_nav_menu_walker'] ) && isset( $wp_filter['wp_edit_nav_menu_walker']->callbacks ) ) {

						foreach ( $wp_filter['wp_edit_nav_menu_walker']->callbacks as $priority => $callbacks ) {
							foreach ( $callbacks as $callback => $data ) {
								if ( 'GroovyMenuAdminWalker::get_edit_walker' === $callback ) {
									$other_priorities = false;
								} else {
									$admin_walker_priority = $priority;
									$other_priorities      = true;
								}
							}
						}
					}

					if ( $other_priorities && $admin_walker_priority ) {
						add_action( 'admin_notices', array( $this, 'show_gm_admin_walker_priority_add' ), 9 );
					} elseif ( ! $other_priorities && $admin_walker_priority ) {
						add_action( 'admin_notices', array( $this, 'show_gm_admin_walker_priority_remove' ), 9 );
					}
				}
			}

			$lic_opt = get_option( GROOVY_MENU_DB_VER_OPTION . '__lic' );

			if ( ! $lic_opt && 'toplevel_page_groovy_menu_welcome' !== $screen->id ) {
				add_action( 'admin_notices', array( $this, 'show_gm_admin_need_license' ), 4 );
			}

		}


		public function show_gm_admin_need_license() {
			?>

			<div id="gm-need-license-notice" class="notice-error settings-error notice is-dismissible">
				<div class="gm-notice-column-img">
					<img src="<?php echo esc_url( GROOVY_MENU_URL ); ?>assets/images/gm-notice-logo.png" alt="">
				</div>
				<div class="gm-notice-column-content">
					<h2><?php esc_html_e( 'Need to register your copy of Groovy Menu plugin', 'groovy-menu' ) ?></h2>
					<p class="gm-need-license-text-block"><?php echo sprintf(
							esc_html__( 'This copy of Groovy Menu not registered. Please visit to %s page and activate the plugin.', 'groovy-menu' ), '<a href="' . get_admin_url( null, 'admin.php?page=groovy_menu_welcome', 'relative' ) . '">' . esc_html__( 'Welcome', 'groovy-menu' ) . '</a> ' ); ?>
						<br>
					</p>
					<p class="gm-need-license-text-block">
						<?php esc_html_e( 'Until you activate, features will not be available.', 'groovy-menu' ) ?>
					</p>
					<p class="gm-need-license-text-block gm-need-license-text-block--buttons">
						<a href="<?php echo get_admin_url( null, 'admin.php?page=groovy_menu_welcome', 'relative' ); ?>" class="button button-primary gm-notice-button gm-notice-button--blue"><?php esc_html_e( 'Register your copy of plugin', 'groovy-menu' ); ?></a>
						<a href="https://grooni.ticksy.com/" class="button button-secondary gm-notice-button gm-notice-button--grey" target="_blank"><?php esc_html_e( 'Need help?', 'groovy-menu' ); ?></a>
					</p>
				</div>
			</div>

			<?php
		}


		public function show_gm_need_upgrade() {
			?>

			<div id="gm-upgrade-notice" class="notice-error settings-error notice is-dismissible">
				<p class="gm-install-addons-text-block"><?php echo sprintf(
						esc_html__( 'You need to update %s. There are major improvements related to Groovy Menu settings.', 'groovy-menu' ), '<a href="' . get_admin_url( null, 'themes.php', 'relative' ) . '">' . esc_html__( 'Crane theme', 'groovy-menu' ) . '</a> ' ); ?>
					<br>
				</p>

				<p class="crane-install-addons-buttons-block">
					<a href="<?php echo esc_url( add_query_arg( 'gm-upgrade-theme', 'yes' ) ); ?>"><?php esc_html_e( 'Dismiss this notice', 'groovy-menu' ); ?></a>
				</p>

			</div>

			<?php
		}


		public function show_gm_admin_walker_priority_add() {
			?>

			<div id="gm-upgrade-notice" class="notice-warning settings-warning notice is-dismissible">
				<p class="gm-install-addons-text-block"><?php echo esc_html__( 'The theme or another plugin overrides the visibility of the Groovy menu settings. To display the Groovy menus settings, please click on the button', 'groovy-menu' ); ?>
					<button class="button gm-admin-walker-priority--button" data-do="add">
						<?php echo esc_html__( 'Show Groovy Menu settings', 'groovy-menu' ); ?>
					</button>
				</p>
			</div>

			<?php
		}

		public function show_gm_admin_walker_priority_remove() {
			?>

			<div id="gm-upgrade-notice" class="notice-warning settings-warning notice is-dismissible">
				<p class="gm-install-addons-text-block"><?php echo esc_html__( 'Groovy menu settings are currently displayed. To display the settings from a theme or another plugin, please click on the button', 'groovy-menu' ); ?>
					<button class="button gm-admin-walker-priority--button" data-do="remove">
						<?php echo esc_html__( 'Show Theme/plugin settings', 'groovy-menu' ); ?>
					</button>
				</p>
			</div>

			<?php
		}

		public function gm_admin_walker_priority_change() {

			if ( ! isset( $_POST['do'] ) || ! in_array( $_POST['do'], [ 'add', 'remove' ], true ) ) {
				wp_die( json_encode( array(
					'code'    => 0,
					'message' => esc_html__( 'Error. Undefined post param "do"', 'groovy-menu' ),
				) ) );
			}

			$style           = new GroovyMenuStyle();
			$global_settings = get_option( GroovyMenuStyle::OPTION_NAME );

			if ( 'add' === $_POST['do'] ) {
				$global_settings['tools']['admin_walker_priority'] = '1';
			} else {
				$global_settings['tools']['admin_walker_priority'] = '';
			}

			// Update settings.
			$style->updateGlobal( $global_settings );

			$output = array(
				'message' => '<p><strong>' .
				             esc_html__( 'Setting changed', 'groovy-menu' ) . '</strong> ' .
				             '</p>',
				'code'    => 1
			);
			wp_die( json_encode( $output ) );
		}

		public function start_ob() {
			if ( isset( $_GET['export'] ) ||
			     ( isset( $_FILES['import'] ) && isset( $_FILES['import']['tmp_name'] ) ) ||
			     ( isset( $_GET['action'] ) && in_array( $_GET['action'],
					     array(
						     'create',
						     'delete',
						     'duplicate',
						     'saveDashboardSettings',
						     'defaultSet',
						     'rename',
						     'preview',
						     'importFromLibrary',
						     'import',
						     'deleteFont',
						     'setThumb',
						     'unsetThumb',
					     ), true
				     ) )
			) {
				ob_start();
			}
		}


		/**
		 * @param WP_Admin_Bar $wp_admin_bar
		 */
		public function addToolbarLink( WP_Admin_Bar $wp_admin_bar ) {

			$show_admin_bar_link = apply_filters( 'groovy_menu_show_admin_bar_link', true );
			if ( ! $show_admin_bar_link ) {
				return;
			}

			if ( function_exists( 'is_user_logged_in' ) && is_user_logged_in() && current_user_can( 'groovy_menu_edit_preset' ) ) {

				global $groovyMenuSettings;

				$args = array(
					'id'    => 'groovy-menu-options',
					'title' => '<span class="ab-icon groovy-icon"></span> ' . esc_html__( 'Groovy Menu', 'groovy-menu' ),
					'href'  => get_admin_url() . 'admin.php?page=groovy_menu_settings',
				);
				$wp_admin_bar->add_node( $args );

				if ( isset( $groovyMenuSettings['preset'] ) ) {
					$preset = $groovyMenuSettings['preset'];
				} else {
					$preset = $this->getCurrentPreset();
				}

				$sub = array(
					'id'     => 'menu-preset',
					'title'  => $preset['name'],
					'href'   => get_admin_url() . 'admin.php?page=groovy_menu_settings&action=edit&id=' . $preset['id'],
					'parent' => 'groovy-menu-options',
				);
				$wp_admin_bar->add_node( $sub );


				// Next adds sub-links for used Groovy Menu Blocks.

				if ( ! empty( $groovyMenuSettings['nav_menu_data']['id'] ) ) {
					$current_menu_id = $groovyMenuSettings['nav_menu_data']['id'];
				}

				if ( ! empty( $current_menu_id ) && ! empty( $groovyMenuSettings['nav_menu_data']['data'][ $current_menu_id ] ) ) {
					$nav_menu_items = $groovyMenuSettings['nav_menu_data']['data'][ $current_menu_id ];
				}

				if ( empty( $nav_menu_items ) ) {
					return;
				}


				$menu_blocks = array();

				foreach ( $nav_menu_items as $nav_menu_item ) {
					if ( empty( $nav_menu_item->object ) || 'gm_menu_block' !== $nav_menu_item->object ) {
						continue;
					}

					// the array key eliminates duplicates.
					$menu_blocks[ $nav_menu_item->object_id ] = true;

				}

				if ( empty( $menu_blocks ) ) {
					return;
				}

				// Add title for Groovy Menu Blocks.
				$sub = array(
					'id'     => 'groovy-menu-blocks',
					'title'  => esc_html__( 'Menu Blocks', 'groovy-menu' ),
					'href'   => get_admin_url() . 'edit.php?post_type=gm_menu_block',
					'parent' => 'groovy-menu-options',
				);
				$wp_admin_bar->add_node( $sub );

				// Add links to edit pages of Groovy Menu Block.
				foreach ( $menu_blocks as $block_id => $flag ) {
					$sub_block = array(
						'id'     => 'gm_menu_blocks__' . $block_id,
						'title'  => get_the_title( $block_id ),
						'href'   => get_edit_post_link( $block_id, '' ),
						'parent' => 'groovy-menu-blocks',
					);
					$wp_admin_bar->add_node( $sub_block );
				}

			}

		}


		/**
		 * Get current preset name and id
		 *
		 * @return array
		 */
		public function getCurrentPreset() {
			$preset_id = GroovyMenuUtils::getMasterPreset();

			$post_type = get_post_type();
			if ( ! empty( $post_type ) && $post_type ) {
				$def_val = GroovyMenuUtils::getTaxonomiesPresetByPostType( $post_type );
			}

			if ( ! empty( $def_val['preset'] ) ) {
				$preset_id = $def_val['preset'];
			}
			$current_preset_id = GroovyMenuSingleMetaPreset::get_preset_id_from_meta();
			if ( $current_preset_id ) {
				$preset_id = $current_preset_id;
			}

			$preset_id =
				( empty( $preset_id ) || 'default' === $preset_id )
					?
					GroovyMenuUtils::getMasterPreset()
					:
					$preset_id;


			$category_options = gm_get_current_category_options();
			if ( $category_options && isset( $category_options['custom_options'] ) && '1' === $category_options['custom_options'] ) {
				if ( GroovyMenuCategoryPreset::getCurrentPreset() ) {
					$preset_id = GroovyMenuCategoryPreset::getCurrentPreset();
				}
			}

			if ( 'default' === $preset_id ) {
				$preset_id = null;
			}

			$styles = new GroovyMenuStyle( $preset_id );

			$preset = array(
				'id'   => $styles->getPreset()->getId(),
				'name' => $styles->getPreset()->getName(),
			);


			return $preset;
		}

		/**
		 * Return settings of the current preset
		 *
		 * @param null|integer $menu_id specific preset id.
		 *
		 * @return GroovyMenuStyle
		 */
		public function settings( $menu_id = null ) {
			if ( is_null( $menu_id ) && isset( $_GET['id'] ) ) {
				$menu_id = esc_attr( $_GET['id'] );
			}
			if ( is_null( $this->settings ) ) {
				$this->settings = new GroovyMenuStyle( $menu_id );
			}

			return $this->settings;
		}

		public function addThemesPage() {

			$show_integration = true;

			global $gm_supported_module;
			if ( isset( $gm_supported_module['GroovyMenuShowIntegration'] ) && ! $gm_supported_module['GroovyMenuShowIntegration'] ) {
				$show_integration = false;
			}

			$main_slug = 'groovy_menu_welcome';
			$lic_opt   = get_option( GROOVY_MENU_DB_VER_OPTION . '__lic' );
			$lic_type  = GroovyMenuUtils::get_lic_data( 'type' );
			$welcome_page = 'welcome_full';
			if ( 'extended' === $lic_type ) {
				$welcome_page = 'welcome_ext';
			}

				add_menu_page(
				__( 'Groovy menu', 'groovy-menu' ),
				__( 'Groovy menu', 'groovy-menu' ),
				'edit_theme_options',
				'groovy_menu_welcome',
				'',
				'',
				91
			);

			add_submenu_page(
				$main_slug,
				__( 'Welcome', 'groovy-menu' ),
				__( 'Welcome', 'groovy-menu' ),
				'edit_theme_options',
				'groovy_menu_welcome',
				array( $this, $welcome_page )
			);


			if ( $lic_opt ) {

				add_submenu_page(
					$main_slug,
					__( 'Dashboard', 'groovy-menu' ),
					__( 'Dashboard', 'groovy-menu' ),
					'edit_theme_options',
					'groovy_menu_settings',
					array( $this, 'render' )
				);

				if ( $show_integration ) {
					add_submenu_page(
						$main_slug,
						__( 'Integration', 'groovy-menu' ),
						__( 'Integration', 'groovy-menu' ),
						'edit_theme_options',
						'groovy_menu_integration',
						array( $this, 'integrationDashboard' )
					);
				}

				add_submenu_page(
					$main_slug,
					__( 'Menus', 'groovy-menu' ),
					__( 'Menus', 'groovy-menu' ),
					GroovyMenuRoleCapabilities::presetRead(),
					'groovy_menu_menus',
					array( $this, 'menus' )
				);

			}

		}

		public function menus() {
			?>
			<script>window.location.href = '<?php echo admin_url( 'nav-menus.php' ); ?>';</script>
			<?php
			exit;
		}

		public function premium() {
			?>
			<script>window.location.href = 'https://groovymenu.grooni.com/upgrade/';</script>
			<?php
			exit;
		}

		public function render() {
			$actions = array(
				'edit',
				'create',
				'delete',
				'duplicate',
				'saveDashboardSettings',
				'rename',
				'defaultSet',
				'preview',
				'importFromLibrary',
				'import',
				'deleteFont',
				'setThumb',
				'unsetThumb'
			);

			$action = isset( $_GET['action'] ) ? $_GET['action'] : null;
			if ( in_array( $action, $actions ) ) {
				$this->$action();
			} else {
				$this->dashboard();
			}
		}

		public function create() {
			if ( GroovyMenuRoleCapabilities::presetCreate( true ) ) {
				$id = GroovyMenuPreset::create( 'new' );
				GroovyMenuPreset::rename( $id, 'New #' . $id );
				wp_redirect( '?page=groovy_menu_settings&action=edit&id=' . $id );
			}
			exit;
		}

		public function setThumb() {
			if ( GroovyMenuRoleCapabilities::presetEdit( true ) ) {
				GroovyMenuPreset::setThumb( $_GET['id'], $_GET['image'] );
			}
		}

		public function unsetThumb() {
			if ( GroovyMenuRoleCapabilities::presetEdit( true ) ) {
				GroovyMenuPreset::setThumb( $_GET['id'], null );
			}
		}

		public function rename() {
			if ( GroovyMenuRoleCapabilities::presetEdit( true ) ) {
				GroovyMenuPreset::rename( $_GET['id'], $_GET['name'] );
			}
			exit;
		}


		public function preview() {
			ob_clean();

			wp_enqueue_style( 'groovy-style', get_stylesheet_directory_uri() . '/assets/style/frontend.css', [], GROOVY_MENU_VERSION );
			wp_style_add_data( 'groovy-style', 'rtl', 'replace' );

			include_once GROOVY_MENU_DIR . 'template/Preview.php';
			exit;
		}


		public function savePreviewImage() {
			ob_clean();
			if ( isset( $_POST ) && isset( $_POST['image'] ) ) {
				global $gm_supported_module;
				global $wp_filesystem;
				if ( empty( $wp_filesystem ) ) {
					if ( file_exists( ABSPATH . '/wp-admin/includes/file.php' ) ) {
						require_once ABSPATH . '/wp-admin/includes/file.php';
						WP_Filesystem();
					}
				}

				if ( empty( $wp_filesystem ) ) {

					update_post_meta( intval( esc_attr( $_GET['id'] ) ), 'gm_preset_screenshot', $_POST['image'] );

				} else {

					$upload_dir      = GroovyMenuUtils::getUploadDir();
					$upload_uri      = GroovyMenuUtils::getUploadUri();
					$upload_filename = 'preset_' . esc_attr( $_GET['id'] ) . '.png';
					$data            = base64_decode( preg_replace( '#^data:image/\w+;base64,#i', '', $_POST['image'] ) );

					$wp_filesystem->put_contents( $upload_dir . $upload_filename, $data, FS_CHMOD_FILE );

					update_post_meta( intval( esc_attr( $_GET['id'] ) ), 'gm_preset_screenshot', $upload_uri . $upload_filename );

				}

				exit;
			}

		}


		public function defaultSet() {
			if ( GroovyMenuRoleCapabilities::globalOptions( true ) ) {
				GroovyMenuPreset::setDefaultPreset( $_GET['id'] );
				wp_redirect( '?page=groovy_menu_settings' );
			}
			exit;
		}


		public function checkCurrentLicense() {

			// By default.
			$respond = 'none';

			if ( defined( 'DOING_AJAX' ) && DOING_AJAX && ! empty( $_POST ) && isset( $_POST['action'] ) && $_POST['action'] === 'gm_check_current_license' ) {

				$lic_opt = GroovyMenuUtils::check_lic( true );

				if ( $lic_opt ) {
					$respond = 'true';
				} else {
					$respond = 'false';
				}

			}

			// Send a JSON response back to an AJAX request, and die().
			wp_send_json_success( $respond );
		}

		public function saveDashboardSettings() {

			if ( ! GroovyMenuRoleCapabilities::globalOptions( true ) ) {
				echo 'Fail. Need more Capabilities.';
				exit;
			}

			if ( ! empty( $_POST ) && ! empty( $_POST['menu'] ) ) {
				if ( ! empty( $_POST['icons'] ) ) {
					if ( class_exists( 'ZipArchive' ) ) {

						$filename = get_attached_file( $_POST['icons'] );
						$zip      = new ZipArchive();
						if ( $zip->open( $filename ) ) {
							$fonts = GroovyMenuFieldIcons::getFonts();

							$selection     = $zip->getFromName( 'selection.json' );
							$selectionData = json_decode( $selection, true );
							$name          = 'groovy-' . rand( 10000, 99999 );

							$fontFiles['woff'] = $zip->getFromName( 'fonts/' . $selectionData['metadata']['name'] . '.woff' );
							$fontFiles['ttf']  = $zip->getFromName( 'fonts/' . $selectionData['metadata']['name'] . '.ttf' );
							$fontFiles['svg']  = $zip->getFromName( 'fonts/' . $selectionData['metadata']['name'] . '.svg' );
							$fontFiles['eot']  = $zip->getFromName( 'fonts/' . $selectionData['metadata']['name'] . '.eot' );

							$dir = GroovyMenuUtils::getFontsDir();

							file_put_contents( $dir . $name . '.woff', $fontFiles['woff'] );
							file_put_contents( $dir . $name . '.ttf', $fontFiles['ttf'] );
							file_put_contents( $dir . $name . '.svg', $fontFiles['svg'] );
							file_put_contents( $dir . $name . '.eot', $fontFiles['eot'] );
							file_put_contents( $dir . $name . '.css',
								GroovyMenuUtils::generate_fonts_css( $name, $selectionData ) );


							$icons = array();
							foreach ( $selectionData['icons'] as $icon ) {
								$icons[] = array(
									'name' => $icon['icon']['tags'][0],
									'code' => $icon['properties']['code']
								);
							}
							$fonts[ $name ] = array( 'icons' => $icons, 'name' => $selectionData['metadata']['name'] );
							GroovyMenuFieldIcons::setFonts( $fonts );
						}
					} else {
						die( esc_html__( "Wasn't able to work with Zip Archive. Missing php-zip extension.", 'groovy-menu' ) );
					}
				}
				$this->settings()->updateGlobal( $_POST['menu'] );

				if ( function_exists( 'groovy_menu_check_gfonts_params' ) ) {
					groovy_menu_check_gfonts_params();
				}

				echo 'saved';
				exit;
			}
			exit;
		}

		public function deleteFont() {
			if ( GroovyMenuRoleCapabilities::globalOptions( true ) ) {
				$fonts = GroovyMenuFieldIcons::getFonts();
				unset( $fonts[ $_GET['name'] ] );
				GroovyMenuFieldIcons::setFonts( $fonts );
			}
			exit;
		}

		public function duplicate() {
			if ( GroovyMenuRoleCapabilities::presetCreate( true ) ) {
				$preset    = GroovyMenuPreset::getById( $_GET['id'] );
				$newId     = GroovyMenuPreset::create( $preset->name . ' duplicated' );
				$newPreset = new GroovyMenuPreset( $newId );
				$styles    = new GroovyMenuStyle( $preset->id );
				$styles->setPreset( $newPreset );
				$styles->update();
				wp_redirect( '?page=groovy_menu_settings' );
			}
			exit;
		}


		/**
		 * Import preset.
		 *
		 * @return null
		 */
		public function import() {
			if ( ! GroovyMenuRoleCapabilities::canImport( true ) ) {
				return;
			}

			if ( isset( $_FILES['import'] ) && isset( $_FILES['import']['tmp_name'] ) ) {
				global $wp_filesystem;
				if ( empty( $wp_filesystem ) ) {
					if ( file_exists( ABSPATH . '/wp-admin/includes/file.php' ) ) {
						require_once ABSPATH . '/wp-admin/includes/file.php';
						WP_Filesystem();
					}
				}
				if ( empty( $wp_filesystem ) ) {
					if ( function_exists( 'file_get_contents' ) ) {
						$data = json_decode( file_get_contents( $_FILES['import']['tmp_name'] ), true );
					}
				} else {
					$data = json_decode( $wp_filesystem->get_contents( $_FILES['import']['tmp_name'] ), true );
				}
			}

			// Stop import, if has error.
			if ( empty( $data ) || ! is_array( $data ) ) {
				wp_die( esc_html__( 'Error. When get uploaded file. Or wrong file format.', 'groovy-menu' ) );
			}


			$presetId = GroovyMenuPreset::create( $data['name'] );

			// Disable cache.
			GroovyMenuPreset::getAll( true, true );

			// Get new preset.
			$style = new GroovyMenuStyle( $presetId );

			foreach ( $data['settings'] as $field => $value ) {
				if ( is_array( $value ) && isset( $value['type'] ) && $value['type'] === 'media' ) {
					$uploadDir  = wp_upload_dir();
					$filename   = $uploadDir['path'] . '/' . $field . '_' . $presetId . '.png';
					$tmpFile    = file_put_contents( $filename, base64_decode( $value['data'] ) );
					$attachment = array(
						'guid'           => $uploadDir['url'] . '/' . basename( $filename ),
						'post_mime_type' => $value['post_mime_type'],
						'post_title'     => basename( $filename ),
						'post_content'   => '',
						'post_status'    => 'inherit'
					);

					$value = wp_insert_attachment( $attachment, $filename );
					require_once ABSPATH . 'wp-admin/includes/image.php';

					$attachData = wp_generate_attachment_metadata( $value, $tmpFile );
					wp_update_attachment_metadata( $value, $attachData );

				}
				$style->set( $field, $value );
			}

			$style->update();
			$style = new GroovyMenuStyle( $presetId );
			GroovyMenuPreset::setPreviewById( $presetId, $data['img'] );

			if ( function_exists( 'groovy_menu_check_gfonts_params' ) ) {
				groovy_menu_check_gfonts_params();
			}

			wp_redirect( '?page=groovy_menu_settings' );
		}

		/**
		 * @param $id
		 */
		public function importFromLibraryById( $id ) {
			if ( ! GroovyMenuRoleCapabilities::canImport( true ) ) {
				return;
			}

			$preset = $this->getPresetsFromApiById( $id );
			$data   = $this->getDataFromApi( $preset['url'] );

			if ( empty( $data ) ) {
				return;
			}

			$presetId = GroovyMenuPreset::create( $preset['name'] );
			$style    = new GroovyMenuStyle( $presetId );

			foreach ( $data['settings'] as $field => $value ) {
				$style->set( $field, $value );
			}
			$style->update();
			$style = new GroovyMenuStyle( $presetId );
			GroovyMenuPreset::setPreviewById( $presetId, $data['img'] );
		}

		public function importFromLibrary() {
			if ( ! GroovyMenuRoleCapabilities::canImport( true ) ) {
				return;
			}

			$preset = $this->getPresetsFromApiById( $_GET['id'] );
			$data   = $this->getDataFromApi( $preset['url'] );

			if ( empty( $data ) ) {
				return;
			}

			GroovyMenuStyleStorage::getInstance()->set_disable_storage();

			$presetId   = GroovyMenuPreset::create( $preset['name'] );
			$preset_obj = new GroovyMenuPreset( $presetId );
			$style      = new GroovyMenuStyle( $presetId );
			$style->setPreset( $preset_obj );

			foreach ( $data['settings'] as $field => $value ) {
				$style->set( $field, $value );
			}

			$style->update();
			$style = new GroovyMenuStyle( $presetId );
			GroovyMenuPreset::setPreviewById( $presetId, $data['img'] );

			if ( function_exists( 'groovy_menu_check_gfonts_params' ) ) {
				groovy_menu_check_gfonts_params();
			}

			wp_redirect( '?page=groovy_menu_settings' );
		}

		public function delete() {
			if ( GroovyMenuRoleCapabilities::presetDelete( true ) ) {
				$delete_by_id = GroovyMenuPreset::deleteById( esc_attr( $_GET['id'] ), true );

				wp_redirect( '?page=groovy_menu_settings' );
			}
			exit;
		}


		public function showDashboardHeader() {
			?>
			<div class="gm-dashboard-header">
				<div class="gm-dashboard-header__logo">
					<a
						href="?page=groovy_menu_settings">
						<img
							src="<?php echo GROOVY_MENU_URL; ?>assets/images/groovy_doc_white.svg"
							alt="">
					</a>
				</div>
				<div class="gm-dashboard-header__btn-group">
					<?php if ( GroovyMenuRoleCapabilities::globalOptions( true ) ) : ?>
						<button class="gm-dashboard__global-settings-btn">
							<span class="gm-gui-icon gm-icon-tools"></span>
							<span class="global-settings-btn__txt-group">
		                    <span
			                    class="global-settings-btn-title"><?php esc_html_e( 'Global settings', 'groovy-menu' ); ?></span>
		                    <span
			                    class="global-settings-btn-subtitle"><?php esc_html_e( 'Upload logo here', 'groovy-menu' ); ?></span>
					</span>
						</button>
					<?php endif; ?>
					<a
						target="_blank"
						href="https://grooni.com/docs/groovy-menu/"
						class="gm-dashboard-header__help-link">
						<span class="gm-gui-icon gm-icon-help"></span>
						<span
							class="gm-dashboard-header__help-link__txt"><?php esc_html_e( 'Need help?', 'groovy-menu' ); ?></span>
					</a>
				</div>
			</div>
			<?php
		}


		public function license() {
			// TODO After plugin update - check license again and renew get_option( GROOVY_MENU_DB_VER_OPTION . '__lic' )

			$lic_opt_old = get_option( GROOVY_MENU_DB_VER_OPTION . '__lic' );
			$lic_opt     = GroovyMenuUtils::check_lic();

			?>

			<div class="gm-dashboard-container gm-dashboard__integration">

				<div class="gm-dashboard-header">
					<div class="gm-dashboard-header__logo">
						<a
							href="?page=groovy_menu_settings">
							<img
								src="<?php echo GROOVY_MENU_URL; ?>assets/images/groovy_doc_white.svg"
								alt="">
						</a>
					</div>
				</div>

				<div class="gm-dashboard-body">
					<div class="gm-dashboard-body_inner">

						<div class="gm-dashboard-body-section">
							<h3><?php esc_html_e( 'License', 'groovy-menu' ); ?></h3>
							<?php if ( $lic_opt ) { ?>
								<p><?php esc_html_e( 'License verification was successful.', 'groovy-menu' ); ?></p>
							<?php } ?>
							<?php if ( $lic_opt && ! $lic_opt_old ) { ?>
								<p><?php esc_html_e( 'A new license for current site is registered.', 'groovy-menu' ); ?></p>
								<p><?php esc_html_e( 'Please, refresh the page.', 'groovy-menu' ); ?></p>
							<?php } ?>
							<p><?php esc_html_e( 'To register and manage licenses, visit Grooni License Manager site.', 'groovy-menu' ); ?></p>
							<a class="gm-check-license-btn button-primary" href="https://license.grooni.com/"
								target="_blank">License.grooni.com</a>
							<p><?php esc_html_e( 'After registering and checking the license, make sure that it is registered on the current site.', 'groovy-menu' ); ?>
								<br><?php esc_html_e( 'Current site URL', 'groovy-menu' ); ?>:
								<code><?php echo get_site_url(); ?></code></p>
						</div>

					</div>
				</div>

			</div>
			<?php


		}

		public function welcome_full() {

			$lic_opt_old         = get_option( GROOVY_MENU_DB_VER_OPTION . '__lic' );
			$lic_opt             = GroovyMenuUtils::check_lic();
			$supported_until     = GroovyMenuUtils::check_lic_supported_until();
			$supported_until_txt = $supported_until ? date( "F j, Y", $supported_until ) : '';
			$purchase_key        = GroovyMenuUtils::get_lic_data( 'purchase_key' );

			if ( ! empty( $purchase_key ) ) {
				$half_key_count      = floor( strlen( $purchase_key ) / 2 );
				$purchase_key_hidden = substr( $purchase_key, 0, $half_key_count ) . str_repeat( '*', $half_key_count );
			}

			if ( $lic_opt ) {
				$lic_txt = '<span class="gm-lic-ok dashicons dashicons-yes-alt"></span>' . esc_html__( 'Plugin is registered!', 'groovy-menu' );
			} else {
				$lic_txt = '<span class="gm-lic-lock dashicons dashicons-lock"></span>' . esc_html__( 'Plugin is NOT registered!', 'groovy-menu' );
			}

			/**
			 * Fires before the groovy menu welcome page output.
			 *
			 * @since 1.9.0
			 */
			do_action( 'gm_before_welcome_output' );

			?>

			<div class="gm-welcome-container gm-welcome-full<?php if ( ! $lic_opt ) { ?> gm-welcome-unreg<?php } ?>">
				<div class="gm-welcome-body">
					<div class="gm-welcome-header">
                    <span class="gm-welcome-header__logo">
                      <img src="<?php echo GROOVY_MENU_URL; ?>assets/images/groovy-menu-repsonsive-logo.svg" alt="">
	                    <span class="gm-welcome-header__subversion_full"><?php esc_html_e( 'premium version', 'groovy-menu' ); ?></span>
                    </span>
						<span class="gm-welcome-header__version"><?php echo GROOVY_MENU_VERSION; ?></span>
					</div>
					<div class="gm-welcome-top-block">
						<div class="gm-welcome-top-block__txt">
							<div class="gm-welcome-registered__txt<?php if ( ! $lic_opt ) {
								echo ' gm-welcome-registered__lock';
							} ?>">
								<?php echo GroovyMenuUtils::clean_output( $lic_txt ); ?>
							</div>
							<?php if ( ! $lic_opt ) { ?>
								<p class="gm-welcome-registered__p"><?php esc_html_e( 'You need to register your copy of Groovy Menu to activate the plugin features', 'groovy-menu' ); ?></p>
								<p class="gm-welcome-registered__p"><?php esc_html_e( 'Need to register on URL', 'groovy-menu' ); ?>:<br>
									<code><?php echo esc_url( get_site_url() ); ?></code></p>
								<a class="gm-welcome-big-button gm-welcome-big-button--blue gm-welcome-tile__link--lic"
									href="https://license.grooni.com/"
									target="_blank"><?php esc_html_e( 'License site', 'groovy-menu' ); ?></a>
								<span
									class="gm-welcome-big-button gm-welcome-big-button--green gm-welcome-check--lic"><?php esc_html_e( 'Check license', 'groovy-menu' ); ?></span>
							<?php } elseif ( $lic_opt && ! $lic_opt_old ) { ?>
								<p class="gm-welcome-registered__p"><?php esc_html_e( 'A new license for current site is registered.', 'groovy-menu' ); ?></p>
								<p class="gm-welcome-registered__p">
									<strong><?php esc_html_e( 'Please, refresh the page.', 'groovy-menu' ); ?></strong>
								</p>
							<?php } elseif ( $lic_opt ) { ?>
								<?php if ( ! empty( $purchase_key ) ) { ?>
									<div class="gm-welcome-purchase-key--wrapper gm-welcome-grid-wrapper2-1">
										<div
											class="gm-welcome-purchase-key--hidden gm-welcome-grid"><?php echo GroovyMenuUtils::clean_output( $purchase_key_hidden ); ?></div>
										<div class="gm-welcome-grid">
											<div
												class="gm-welcome-purchase-key gm-welcome-big-button gm-welcome-big-button--blue"><?php esc_html_e( 'Clipboard', 'groovy-menu' ) ?>
												<div class="gm-welcome-purchase-key--container"
													style="display:none;"><?php echo GroovyMenuUtils::clean_output( $purchase_key ); ?></div>
											</div>
										</div>
									</div>
								<?php } ?>
								<a class="gm-welcome-simple-link gm-welcome-tile__link--lic"
									href="https://license.grooni.com/"
									target="_blank"><?php esc_html_e( 'Manage your license(s)', 'groovy-menu' ); ?></a>
							<?php } ?>
						</div>
						<div class="gm-welcome-top-block__img">
							<img src="<?php echo GROOVY_MENU_URL; ?>assets/images/laptop-with-bg.png" alt="">
						</div>
					</div>

					<div class="gm-welcome-tabs">
						<div class="gm-welcome-tab gm-welcome-tab__welcome gm-welcome-grid-wrapper2-1">

							<div class="gm-welcome-grid">
								<div class="gm-welcome-grid-wrapper2">
									<div class="gm-welcome-grid gm-welcome-support__wrapper">
										<div class="gm-welcome-support">
											<?php if ( ! $lic_opt ) { ?>
												<h2><?php esc_html_e( 'Support', 'groovy-menu' ); ?></h2>
												<p class="gm-welcome-support--expired-text">
													<span
														class="dashicons dashicons-warning"></span> <?php esc_html_e( 'Please activate the plugin', 'groovy-menu' ); ?>
												</p>
												<p class="gm-welcome-margintop">
													<a
														class="gm-welcome-big-button gm-welcome-big-button--blue"
														href="https://grooni.com/docs/groovy-menu/faq/how-to-register-the-regular-license/"
														target="_blank"><?php esc_html_e( 'How to activate', 'groovy-menu' ); ?></a>
												</p>

											<?php } else { ?>

												<h2><?php esc_html_e( 'Premium support', 'groovy-menu' ); ?></h2>
												<?php if ( $supported_until ) { ?>
													<p class="gm-welcome-support--active-text">
													<span
														class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( 'Active', 'groovy-menu' ); ?>
													</p>
													<p><?php esc_html_e( 'Support is valid until', 'groovy-menu' ); ?>
														<strong><?php echo GroovyMenuUtils::clean_output( $supported_until_txt ); ?></strong>
													</p>

												<?php } else { ?>

													<p class="gm-welcome-support--expired-text">
													<span
														class="dashicons dashicons-no"></span> <?php esc_html_e( 'Expired', 'groovy-menu' ); ?>
													</p>
													<p><?php esc_html_e( 'Renew support to get help for 6 months', 'groovy-menu' ); ?></p>

												<?php } ?>

												<p class="gm-welcome-margintop">
													<a
														class="gm-welcome-big-button gm-welcome-big-button--blue"
														href="https://grooni.ticksy.com/"
														target="_blank"><?php esc_html_e( 'Get support', 'groovy-menu' ); ?></a>
													<?php if ( ! $supported_until ) { ?>
														<a
															class="gm-welcome-big-button gm-welcome-big-button--green gm-welcome-button--renew"
															href="https://codecanyon.net/checkout/from_item/23049456?license=regular&size=source&support=renew_6month&ref=grooni"
															target="_blank"><?php esc_html_e( 'Renew support', 'groovy-menu' ); ?></a>
													<?php } ?>
												</p>
											<?php } ?>
										</div>
									</div>
									<div class="gm-welcome-grid gm-welcome-integration__wrapper">
										<div class="gm-welcome-integration">
											<h2><?php esc_html_e( 'Help with integration', 'groovy-menu' ); ?></h2>
											<p><?php esc_html_e( 'If automatic integration does not work properly, and for manual integration you do not have enough time and experience.', 'groovy-menu' ); ?></p>
											<p class="gm-welcome-margintop"><a
													class="gm-welcome-big-button gm-welcome-big-button--green"
													href="https://gum.co/groovy-integration"
													target="_blank"><?php esc_html_e( 'Manual integration', 'groovy-menu' ); ?> $35</a>
											</p>
										</div>
									</div>
								</div>
								<div class="gm-welcome-buy-license-wrapper">
									<div class="gm-welcome-notice gm-welcome-notice__important">
										<div class="gm-welcome-tab__big-text">
											<p>
												<strong><?php esc_html_e( 'Buy new license', 'groovy-menu' ); ?></strong> - <?php esc_html_e( 'Starting a new project? Buy another license', 'groovy-menu' ); ?>
											</p>
										</div>
										<p>
											<strong><?php esc_html_e( 'Important!', 'groovy-menu' ); ?></strong> <?php esc_html_e( 'One standard license is valid only for', 'groovy-menu' ); ?>
											<strong><?php esc_html_e( '1 website', 'groovy-menu' ); ?></strong>. <?php esc_html_e( 'Including multiple, you need to have own license on each individual site within your MultiSite installation organized on sub-domains.', 'groovy-menu' ); ?>
										</p>
										<p><?php esc_html_e( 'When moving a site from one domain to another please deregister the plugin first.', 'groovy-menu' ); ?></p>
										<p class="gm-welcome-margintop"><a
												class="gm-welcome-big-button gm-welcome-big-button--green"
												href="https://1.envato.market/regular"
												target="_blank"><?php esc_html_e( 'Purchase new license', 'groovy-menu' ); ?></a>
										</p>
									</div>
								</div>
							</div>

							<div class="gm-welcome-grid gm-welcome-sysinfo__wrapper">
								<div class="gm-welcome-sysinfo">
									<h2><?php esc_html_e( 'System Status', 'groovy-menu' ); ?></h2>
									<?php echo GroovyMenuUtils::get_environment_info( 'html' ); ?>
								</div>
							</div>

						</div>
					</div>

					<div class="gm-welcome-tiles">
						<div class="gm-welcome-tile">
							<h2 class="gm-welcome-tile__title"><?php esc_html_e( 'First Steps', 'groovy-menu' ); ?></h2>
							<p class="gm-welcome-tile__txt"><?php esc_html_e( 'To display the menu on the site, you need to add', 'groovy-menu' ); ?>
								<a href="<?php echo admin_url( 'nav-menus.php' ) ?>"><?php esc_html_e( 'menu items', 'groovy-menu' ); ?></a>,
								<?php esc_html_e( 'do the', 'groovy-menu' ); ?> <a
									href="<?php echo admin_url( 'admin.php?page=groovy_menu_integration' ) ?>"><?php esc_html_e( 'integration', 'groovy-menu' ); ?></a>, <?php esc_html_e( 'and', 'groovy-menu' ); ?>
								<a href="<?php echo admin_url( 'admin.php?page=groovy_menu_settings' ) ?>"><?php esc_html_e( 'upload the logo', 'groovy-menu' ); ?></a>. <?php esc_html_e( 'And', 'groovy-menu' ); ?> <?php esc_html_e( 'customize', 'groovy-menu' ); ?> <?php esc_html_e( 'the menu design for your taste', 'groovy-menu' ); ?>.
							</p>
							<a href="<?php echo admin_url( 'admin.php?page=groovy_menu_settings' ) ?>"
								class="gm-welcome-tile__link"><?php esc_html_e( 'dashboard', 'groovy-menu' ); ?></a>
						</div>
						<div class="gm-welcome-tile">
							<h2><?php esc_html_e( 'Integration', 'groovy-menu' ); ?></h2>
							<p><?php esc_html_e( 'The automatic integration option is the easiest and in most cases the working way to implement Groovy Menu on your website...', 'groovy-menu' ); ?></p>
							<a href="https://grooni.com/docs/groovy-menu/integration/" class="gm-welcome-tile__link"
								target="_blank"><?php esc_html_e( 'READ MORE', 'groovy-menu' ); ?></a>
						</div>
						<div class="gm-welcome-tile">
							<h2 class="gm-welcome-tile__title"><?php esc_html_e( 'Need help?', 'groovy-menu' ); ?></h2>
							<p class="gm-welcome-tile__txt">
								<?php esc_html_e( 'Our online', 'groovy-menu' ); ?>
								<a target="_blank"
									href="http://grooni.com/docs/groovy-menu/"><?php esc_html_e( 'documentation', 'groovy-menu' ); ?></a>
								<?php esc_html_e( 'and', 'groovy-menu' ); ?>
								<a target="_blank"
									href="https://www.youtube.com/channel/UCpbGGAUnqSLwCAoNgm5uAKg"><?php esc_html_e( 'video tutorials', 'groovy-menu' ); ?></a>
								<?php esc_html_e( 'consist of a lot of the most important information about the plugin settings.', 'groovy-menu' ); ?>
							</p>
							<div class="gm-welcome-tile__link-group">
								<a href="https://grooni.com/docs/groovy-menu/"
									class="gm-welcome-tile__link"><?php esc_html_e( 'MANUAL', 'groovy-menu' ); ?></a>
								<a href="https://www.youtube.com/channel/UCpbGGAUnqSLwCAoNgm5uAKg"
									class="gm-welcome-tile__link gm-welcome-tile__link--secondary-color"><?php esc_html_e( 'VIDEO', 'groovy-menu' );
									?></a>
							</div>
						</div>
					</div>

				</div><!-- .gm-welcome-body -->
			</div>


			<?php

			/**
			 * Fires after the groovy menu welcome page output.
			 *
			 * @since 1.9.0
			 */
			do_action( 'gm_after_welcome_output' );

		}


		public function welcome_ext() {

			global $gm_supported_module;

			$lic_opt      = GroovyMenuUtils::check_lic();

			if ( $lic_opt ) {
				$lic_txt = '<span class="gm-lic-ok dashicons dashicons-yes-alt"></span>' . esc_html__( 'Plugin is registered!', 'groovy-menu' );
			} else {
				$lic_txt = '<span class="gm-lic-lock dashicons dashicons-lock"></span>' . esc_html__( 'Plugin is NOT registered!', 'groovy-menu' );
			}

			/**
			 * Fires before the groovy menu welcome page output.
			 *
			 * @since 1.9.0
			 */
			do_action( 'gm_before_welcome_output' );

			?>

			<div class="gm-welcome-container gm-welcome-full gm-welcome-ext">
				<div class="gm-welcome-body">
					<div class="gm-welcome-header">
                    <span class="gm-welcome-header__logo">
                      <img src="<?php echo GROOVY_MENU_URL; ?>assets/images/groovy-menu-repsonsive-logo.svg" alt="">
	                    <span class="gm-welcome-header__subversion_full"><?php esc_html_e( 'premium version', 'groovy-menu' ); ?></span>
                    </span>
						<span class="gm-welcome-header__version"><?php echo GROOVY_MENU_VERSION; ?></span>
					</div>
					<div class="gm-welcome-top-block">
						<div class="gm-welcome-top-block__txt">
							<div class="gm-welcome-registered__txt">
								<?php echo GroovyMenuUtils::clean_output( $lic_txt ); ?>
							</div>
							<?php if ( $lic_opt ) { ?>
								<p class="gm-welcome-simple-text">
									<?php esc_html_e( 'under the extended license for the template:', 'groovy-menu' ); ?>
									<code><?php echo esc_attr( $gm_supported_module['theme'] ); ?></code>
								</p>
							<?php } ?>
						</div>
						<div class="gm-welcome-top-block__img">
							<img src="<?php echo GROOVY_MENU_URL; ?>assets/images/laptop-with-bg.png" alt="">
						</div>
					</div>

					<div class="gm-welcome-tabs">
						<div class="gm-welcome-tab gm-welcome-tab__welcome gm-welcome-grid-wrapper2-1">

							<div class="gm-welcome-grid">
								<div class="gm-welcome-buy-license-wrapper">
									<div class="gm-welcome-notice gm-welcome-notice__important">
										<div class="gm-welcome-tab__big-text">
											<p>
												<strong><?php esc_html_e( 'Buy new license', 'groovy-menu' ); ?></strong> - <?php esc_html_e( 'Starting a new project? Buy another license', 'groovy-menu' ); ?>
											</p>
										</div>
										<p>
											<strong><?php esc_html_e( 'Important!', 'groovy-menu' ); ?></strong> <?php esc_html_e( 'This license is registered for the current template.', 'groovy-menu' ); ?>
										</p>
										<p><?php esc_html_e( 'If you liked the plugin and want to install it on another template and use it separately, then you need to purchase a license.', 'groovy-menu' ); ?></p>
										<p class="gm-welcome-margintop"><a
												class="gm-welcome-big-button gm-welcome-big-button--green"
												href="https://1.envato.market/regular"
												target="_blank"><?php esc_html_e( 'Purchase new license', 'groovy-menu' ); ?></a>
										</p>
									</div>
								</div>
							</div>

							<div class="gm-welcome-grid gm-welcome-sysinfo__wrapper">
								<div class="gm-welcome-sysinfo">
									<h2><?php esc_html_e( 'System Status', 'groovy-menu' ); ?></h2>
									<?php echo GroovyMenuUtils::get_environment_info( 'html' ); ?>
								</div>
							</div>

						</div>
					</div>

				</div><!-- .gm-welcome-body -->
			</div>


			<?php

			/**
			 * Fires after the groovy menu welcome page output.
			 *
			 * @since 1.9.0
			 */
			do_action( 'gm_after_welcome_output' );

		}


		public function dashboard() {
			$presets = GroovyMenuPreset::getAll();
			$default = GroovyMenuPreset::getDefaultPreset();

			/**
			 * Fires before the groovy menu dashboard output.
			 *
			 * @since 1.2.20
			 */
			do_action( 'gm_before_dashboard_output' );

			?>

			<div class="gm-dashboard-container">
				<?php $this->showDashboardHeader(); ?>
				<div class="gm-dashboard-body">
					<div class="gm-infobox gm-infobox-success gm-hidden">
						<p class="gm-infobox__txt"><?php esc_html_e( 'Preset name saved', 'groovy-menu' ); ?></p>
					</div>
					<div class="gm-dashboard-body__title">
						<h3 class="gm-dashboard-body__title__alpha"><?php esc_html_e( 'Menu presets', 'groovy-menu' ); ?></h3>
					</div>
					<div class="gm-dashboard-body_inner">
						<?php foreach ( $presets as $preset ) {

							$needScreenshot = false;
							$preview        = GroovyMenuPreset::getPreviewById( $preset->id );
							if ( ! $preview ) {
								$needScreenshot = true;
								$preview        = GROOVY_MENU_URL . 'assets/images/blank.png';
							}
							$used_count = GroovyMenuUtils::get_preset_used_in_by_id( $preset->id, true );

							?>

							<div
								class="preset <?php echo( $needScreenshot ? 'preset--need-screenshot' : '' ); ?> preset--groovy <?php echo ( intval( $default ) == intval( $preset->id ) ) ? 'preset--default' : ''; ?>"
								data-id="<?php echo esc_attr( $preset->id ); ?>"
								data-name="<?php echo htmlspecialchars( $preset->name ); ?>">

								<div class="preset-inner">
									<a class="preset-placeholder"
										href="?page=groovy_menu_settings&action=edit&id=<?php echo esc_attr( $preset->id ); ?>">
										<img src="<?php echo esc_attr( $preview ); ?>"/>
									</a>

									<div class="preset-info">
										<div class="preset-title">
											<input
												class="preset-title__alpha"
												value="<?php echo esc_attr( $preset->name ); ?>"
												readonly>
										</div>
										<div class="preset-opts__dropdown is-closed">
											<i class="fa fa-chevron-down"></i>
											<ul class="preset-opts__nav">
												<?php if ( GroovyMenuRoleCapabilities::presetEdit( true ) ) : ?>
													<li class="preset-opts__nav__item preset-rename">
														<i class="fa fa-font"></i>
														<span
															class="preset-opts__nav__item__txt"><?php esc_html_e( 'Rename', 'groovy-menu' ); ?></span>
													</li>
												<?php endif; ?>
												<?php if ( GroovyMenuRoleCapabilities::globalOptions( true ) ) : ?>
													<li class="preset-opts__nav__item preset-set-default">
														<i class="fa fa-thumb-tack"></i>
														<span
															class="preset-opts__nav__item__txt"><?php esc_html_e( 'Set as default', 'groovy-menu' ); ?></span>
													</li>
												<?php endif; ?>
												<li class="preset-opts__nav__item preset-preview">
													<i class="fa fa-search"></i>
													<span
														class="preset-opts__nav__item__txt"><?php esc_html_e( 'Preview', 'groovy-menu' ); ?></span>
												</li>
												<?php if ( GroovyMenuRoleCapabilities::presetCreate( true ) ) : ?>
													<li class="preset-opts__nav__item preset-duplicate">
														<i class="fa fa-clone"></i>
														<span
															class="preset-opts__nav__item__txt"><?php esc_html_e( 'Duplicate', 'groovy-menu' ); ?></span>
													</li>
												<?php endif; ?>
												<?php if ( GroovyMenuRoleCapabilities::canExport( true ) ) : ?>
													<li class="preset-opts__nav__item preset-export">
														<i class="fa fa-paper-plane"></i>
														<span
															class="preset-opts__nav__item__txt"><?php esc_html_e( 'Export', 'groovy-menu' ); ?></span>
													</li>
												<?php endif; ?>
												<?php if ( ! GroovyMenuPreset::isPreviewThumb( $preset->id ) ) { ?>
													<?php if ( GroovyMenuRoleCapabilities::presetEdit( true ) ) : ?>
														<li class="preset-opts__nav__item preset-thumbnail">
															<i class="fa fa-plus"></i>
															<span
																class="preset-opts__nav__item__txt"><?php esc_html_e( 'Set thumbnail', 'groovy-menu' ); ?></span>
														</li>
													<?php endif; ?>
												<?php } else { ?>
													<?php if ( GroovyMenuRoleCapabilities::presetEdit( true ) ) : ?>
														<li class="preset-opts__nav__item preset-thumbnail-unset">
															<i class="fa fa-times"></i>
															<span
																class="preset-opts__nav__item__txt"><?php esc_html_e( 'Unset thumbnail', 'groovy-menu' ); ?></span>
														</li>
													<?php endif; ?>
												<?php } ?>
												<?php
												$used_text = '';
												if ( $used_count ) {
													$used_text = ' (' .
													             sprintf( esc_html__( '%d uses', 'groovy-menu' ), $used_count ) .
													             ')';
												}
												?>
												<?php if ( GroovyMenuRoleCapabilities::presetDelete( true ) ) : ?>
													<li class="preset-opts__nav__item preset-delete<?php if ( $used_text ) {
														echo ' has-used-in-objects';
													} ?>">
														<i class="fa fa-times"></i>
														<span
															class="preset-opts__nav__item__txt"><?php esc_html_e( 'Delete', 'groovy-menu' ); ?><?php echo esc_js( $used_text ); ?></span>
													</li>
												<?php endif; ?>
											</ul>
										</div>
									</div>
								</div>
							</div>
						<?php } ?>

						<?php if ( GroovyMenuRoleCapabilities::presetCreate( true ) ) : ?>
							<div class="preset preset--create-new">
								<div class="preset-inner">
									<div class="preset-placeholder">
										<div class="preset-placeholder-inner">
											<span class="gm-gui-icon gm-icon-list"></span>
											<span
												class="preset-title__alpha"><?php esc_html_e( 'New preset', 'groovy-menu' ); ?></span>
										</div>
									</div>
								</div>
							</div>
						<?php endif; ?>

						<?php if ( GroovyMenuRoleCapabilities::canImport( true ) ) : ?>
							<div class="preset preset--import">
								<div class="preset-inner">
									<div class="preset-placeholder">
										<div class="preset-placeholder-inner">
											<span class="gm-gui-icon gm-icon-download"></span>
											<span
												class="preset-title__alpha"><?php esc_html_e( 'Import preset', 'groovy-menu' ); ?></span>
										</div>
									</div>
								</div>
							</div>
						<?php endif; ?>

						<?php if ( GroovyMenuRoleCapabilities::canImport( true ) ) : ?>
							<?php
							$styles        = new GroovyMenuStyle();
							$allow_library = $styles->getGlobal( 'tools', 'allow_import_online_library' ) ? : false;
							?>

							<div class="preset preset--add-template<?php if ( ! $allow_library ) : ?>
 preset--not-allowed<?php endif; ?>">
								<div class="preset-inner">
									<div class="preset-placeholder">
										<div class="preset-placeholder-inner">
											<span class="gm-gui-icon gm-icon-file-box"></span>
											<span class="preset-title__alpha">
										<?php if ( $allow_library ) :
											esc_html_e( 'Add preset from library', 'groovy-menu' );
										else:
											esc_html_e( 'To enable presets from the online library, please enable the option in "Global settings > Tools > Allow fetching presets from online library"
', 'groovy-menu' );
										endif; ?>
									</span>
										</div>
									</div>
								</div>
							</div>
						<?php endif; ?>

					</div>
				</div>
			</div>


			<?php if ( GroovyMenuRoleCapabilities::canImport( true ) ) : ?>
				<!-- Import modal -->
				<div
					class="gm-modal gm-fade modal-centered"
					id="import-modal"
					tabindex="-1">
					<div class="gm-modal-dialog modal-lg">
						<div class="gm-modal-content">
							<form
								method="post"
								action="?page=groovy_menu_settings&action=import"
								enctype="multipart/form-data">
								<?php echo wp_nonce_field(); ?>
								<div class="gm-modal-header">
									<h4 class="modal-title"><?php esc_html_e( 'Import', 'groovy-menu' ); ?></h4>
								</div>
								<div class="gm-modal-body">
									<input
										type="file"
										name="import"/>
								</div>
								<div class="gm-modal-footer">
									<div class="btn-group">
										<button
											type="submit"
											class="btn modal-btn"><?php esc_html_e( 'Import', 'groovy-menu' ); ?>
										</button>
										<button
											type="button"
											class="btn modal-btn"
											data-dismiss="gm-modal"><?php esc_html_e( 'Close', 'groovy-menu' ); ?>
										</button>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			<?php endif; ?>

			<?php
			if ( GroovyMenuRoleCapabilities::globalOptions( true ) ) {

				$this->renderGlobalSettingModal();

				echo GroovyMenuRenderIconsModal();

			}
			?>

			<?php if ( GroovyMenuRoleCapabilities::canImport( true ) ) : ?>
				<!-- Fullscreen modal -->
				<div
					class="gm-modal gm-fade modal-fullscreen"
					id="add-preset-from-library"
					tabindex="-1">
					<div class="gm-modal-dialog">
						<div class="gm-modal-content">
							<div class="gm-modal-header">
								<button
									type="button"
									class="close"
									data-dismiss="gm-modal">
									<span aria-hidden="true">&times;</span>
								</button>
								<h4 class="modal-title"><?php esc_html_e( 'Groovy presets library', 'groovy-menu' ); ?></h4>
							</div>
							<div class="gm-modal-body">
								<div class="modal-row">
									<?php
									foreach ( $this->getPresetsFromApi() as $preset ) { ?>
										<div
											class="preset preset--groovy"
											data-id="<?php echo esc_attr( $preset['id'] ); ?>">
											<div class="preset-inner">
												<div class="preset-placeholder">
													<img
														src="<?php echo esc_attr( $preset['img'] ); ?>"
														alt="">
												</div>
												<div class="preset-info">
													<div class="preset-title">
													<span
														class="preset-title__alpha"><?php echo esc_html( $preset['name'] ); ?></span>
													</div>
													<div class="preset-opts__dropdown">
														<i class="preset-import-preview fa fa-search"></i>
														<i
															data-href="?page=groovy_menu_settings&action=importFromLibrary&id=<?php echo esc_attr( $preset['id'] ); ?>"
															class="preset-import-from-library fa fa-plus"></i>
													</div>
												</div>
											</div>
										</div>
									<?php } ?>

								</div>
							</div>
						</div>
					</div>
				</div>
			<?php endif; ?>

			<?php
			echo GroovyMenuPreviewModal();


			/**
			 * Fires after the groovy menu dashboard output.
			 *
			 * @since 1.2.20
			 */
			do_action( 'gm_after_dashboard_output' );

		}


		public function integrationDashboard() {
			/**
			 * Fires before the groovy menu dashboard output.
			 *
			 * @since 1.4
			 */
			do_action( 'gm_before_integration_dashboard_output' );

			$current_theme  = wp_get_theme()->get_template();
			$child_proposal = null;

			global $wp_version;

			$child_search_data = wp_remote_get(
				add_query_arg(
					array( 'theme' => $current_theme ),
					$this->remote_child_themes_url
				),
				array(
					'timeout'     => 60,
					'httpversion' => '1.1',
					'user-agent'  =>
						'WordPress/' . $wp_version . ';' . $current_theme . ';' . ( is_child_theme() ? 'child' : 'parent' ) . ';' . get_bloginfo( 'url' )
				)
			);


			// Check if returned answer is OK
			if ( ! is_wp_error( $child_search_data ) && wp_remote_retrieve_response_code( $child_search_data ) === 200 ) {
				$child_proposal = json_decode( wp_remote_retrieve_body( $child_search_data ), true );
				if ( ! empty( $child_proposal['error'] ) ) {
					$child_proposal = null;
				}
			}

			$saved_auto_integration     = GroovyMenuUtils::getAutoIntegration();
			$saved_location_integration = GroovyMenuUtils::getSingleLocationIntegration();

			$admin_nav_menu_page = '<a href="' . admin_url( 'nav-menus.php?action=locations' ) . '">' . esc_html__( 'Manage Locations', 'groovy-menu' ) . '</a>';

			?>

			<div class="gm-dashboard-container gm-dashboard__integration">
				<?php $this->showDashboardHeader(); ?>
				<div class="gm-dashboard-body">
					<div class="gm-dashboard-body_inner">

						<div class="gm-dashboard-body-section">
							<h3><?php esc_html_e( 'Automatic integration', 'groovy-menu' ); ?></h3>
							<label>
								<input class="gm-auto-integration-switcher" type="checkbox" class="switch"
									value="1"<?php if ( $saved_auto_integration ) {
									echo ' checked';
								} ?>>
								<?php esc_html_e( 'Enable automatic integration', 'groovy-menu' ); ?>
							</label>
							<button type="button" class="btn gm-integration-button gm-auto-integration-save">
								<?php esc_html_e( 'Save changes', 'groovy-menu' ); ?>
							</button>
							<p><?php esc_html_e( 'If enabled, the Groovy menu markup will be displayed after &lt;body&gt; html tag.', 'groovy-menu' ); ?></p>
						</div>

						<div class="gm-dashboard-body-section">
							<h3><?php esc_html_e( 'Choose the location for the integration menu into pre-defined areas in your theme.', 'groovy-menu' ); ?></h3>
							<p><?php esc_html_e( 'If chosen then the Groovy Menu will display its own markup instead of the standard code from the function wp_nav_menu().', 'groovy-menu' ); ?></p>
							<p>
								<label for="gm-integration-location">
									<?php esc_html_e( 'Theme Location', 'groovy-menu' ); ?><br/>
									<select class="gm-integration-location"
										id="gm-integration-location"
										name="gm-integration-location">
										<option
											value="" <?php echo ( empty( $saved_location_integration ) ) ? ' selected' : '' ?>>--- <?php esc_html_e( 'Select a Location', 'groovy-menu' ); ?> ---
										</option>
										<?php
										foreach ( GroovyMenuUtils::getNavMenuLocations( false, true ) as $location => $name ) {
											// Prevent select Groovy Menu virtual location.
											if ( 'gm_primary' === $location ) {
												continue;
											}

											?>
											<option
												value="<?php echo esc_attr( $location ); ?>"<?php echo ( strval( $saved_location_integration ) === strval( $location ) ) ? ' selected' : '' ?>><?php echo esc_attr( $name ); ?></option>
											<?php
										}
										?>
									</select>
								</label>
							</p>
							<p><?php esc_html_e( 'Note:', 'groovy-menu' ); ?><?php echo sprintf( __( 'Make sure the menu is assigned on the %s page. Otherwise, the location selection list will be empty.', 'groovy-menu' ), $admin_nav_menu_page ); ?><?php esc_html_e( 'Groovy menu Primary location will be ignored.', 'groovy-menu' ); ?><?php esc_html_e( 'This integration successfully works only with those locations that are displayed on the front of the site.', 'groovy-menu' ); ?></p>
							<p>
								<button type="button" class="btn gm-integration-button gm-integration-location-save">
									<?php esc_html_e( 'Save changes', 'groovy-menu' ); ?>
								</button>
						</div>

						<div class="gm-dashboard-body-section">
							<h3><?php esc_html_e( 'Manual integration', 'groovy-menu' ); ?></h3>
							<p><?php esc_html_e( 'Attention! We strongly recommend that you insert the following code into a child theme. Therefore, the changes you make will not affect the files of the parent theme.', 'groovy-menu' ); ?></p>
							<p><?php esc_html_e( 'You can display the Groovy menu directly in the template by adding the following code to the template:', 'groovy-menu' ); ?></p>
							<p>
								<code
									class="gm-integrate-php-sample">&lt;?php if ( function_exists( 'groovy_menu' ) ) { groovy_menu(); } ?&gt;</code>
							</p>
							<p><?php esc_html_e( 'The place where the code should be inserted depends on the theme. The most common place is the', 'groovy-menu' ); ?>
								<code>header.php</code>.
							</p>
						</div>

						<?php
						// Child Theme exists in base
						if ( ! empty( $child_proposal ) && is_array( $child_proposal ) ) { ?>
							<div class="gm-dashboard-body-section">
								<h3><?php esc_html_e( 'Integration through Child Theme', 'groovy-menu' ); ?></h3>
								<?php if ( isset( $child_proposal['auto_integration'] ) && $child_proposal['auto_integration'] ) { ?>
									<p><?php esc_html_e( 'According to the information from the Groovy Menu integration database, your current theme fully supports integration of Groovy Menu.', 'groovy-menu' ) ?></p>
								<?php } ?>

								<?php if ( isset( $child_proposal['integration_type'] ) && in_array( $child_proposal['integration_type'], array(
										'function',
										'header'
									) )
								) { ?>

									<p><?php esc_html_e( 'The currently activated theme is in the Groovy Menu integration database. That means you do not have to manually integrate the Groovy menu and find out how to disable the one that comes with theme. We already found it out and prepared the solution in child theme', 'groovy-menu' ); ?>. <?php
										if ( 'function' === $child_proposal['integration_type'] ) {
											esc_html_e( 'For the correct operation of the plugin, you should create a Child theme or add to existing one the following code to functions.php.', 'groovy-menu' );
										}
										if ( 'header' === $child_proposal['integration_type'] ) {
											esc_html_e( 'For the correct operation of the plugin, you should create a Child theme with header.php and add the support code to it. Following code just example. We suggest download our child theme below.', 'groovy-menu' );
										}

										?></p>
									<pre><?php echo( empty( $child_proposal['function_code'] ) ? '' : $child_proposal['function_code'] ); ?></pre>


									<?php if ( isset( $child_proposal['zip_url'] ) && $child_proposal['zip_url'] ) { ?>
										<p><?php esc_html_e( 'Or we suggest downloading already prepared Child theme from the following link', 'groovy-menu' ); ?>
											:
											<a href="<?php echo esc_attr( $child_proposal['zip_url'] ); ?>"><?php echo esc_html( $child_proposal['child_name'] ); ?></a>
										</p>
									<?php } ?>

								<?php } ?>
							</div>
						<?php } ?>

						<div class="gm-dashboard-body-section">
							<h3><?php esc_html_e( 'Support request', 'groovy-menu' ); ?></h3>
							<p><?php
								echo sprintf( esc_html__( 'If you encounter integration problems, find any bugs, or want to suggest new feature please create a ticket on our %s', 'groovy-menu' ),
									sprintf( '<a href="https://grooni.ticksy.com/" target="_blank">%s</a>', esc_html__( 'Support Portal', 'groovy-menu' ) )
								); ?></p>
						</div>
					</div>
				</div>
			</div>

			<?php
			if ( GroovyMenuRoleCapabilities::globalOptions( true ) ) {

				$this->renderGlobalSettingModal();

				echo GroovyMenuRenderIconsModal();

			}
			?>

			<?php
			/**
			 * Fires after the groovy menu dashboard output.
			 *
			 * @since 1.4
			 */
			do_action( 'gm_after_integration_dashboard_output' );

		}


		public function renderThemeInfoIntegrationRequest() {

			global $wp_version;

			$current_theme      = wp_get_theme();
			$current_theme_data = array(
				'WordPress'    => $wp_version,
				'Name'         => $current_theme->get( 'Name' ),
				'NameInternal' => $current_theme->get_template(),
				'ThemeURI'     => $current_theme->get( 'ThemeURI' ),
				'Description'  => $current_theme->get( 'Description' ),
				'Author'       => $current_theme->get( 'Author' ),
				'AuthorURI'    => $current_theme->get( 'AuthorURI' ),
				'Version'      => $current_theme->get( 'Version' ),
				'siteUrl'      => get_bloginfo( 'url' ),
				'Template'     => $current_theme->get( 'Template' ),
				'ChildStatus'  => ( is_child_theme() ? 'child' : 'parent' ),
				'Status'       => $current_theme->get( 'Status' ),
				'Tags'         => $current_theme->get( 'Tags' ),
			);
			if ( is_array( $current_theme_data['Tags'] ) ) {
				$current_theme_data['Tags'] = implode( ', ', $current_theme_data['Tags'] );
			}

			$request_form_url = add_query_arg(
				array(
					'theme'   => esc_attr( $current_theme_data['NameInternal'] ),
					'request' => 'new',
				),
				$this->remote_child_themes_url
			);

			?>
			<!-- Theme Info Integration Request modal -->
			<div
				class="modal fade modal-centered"
				id="theme-info-integration-request-modal"
				tabindex="-1">
				<div class="modal-dialog modal-lg">
					<div class="gm-modal-content">
						<form
							method="post"
							action="<?php echo esc_url( $request_form_url ); ?>"
							enctype="multipart/form-data"
							id="theme-info-integration-request-form">
							<div class="gm-modal-body">
								<h3>
									<?php esc_html_e( 'After submitting theme integration request the following data will be sent:', 'groovy-menu' ); ?>
								</h3>
								<div class="theme-info-data-send">
									<ul>
										<?php
										foreach ( $current_theme_data as $param => $value ) {
											echo '<li><span class="theme-info-key">' . $param . '</span>: <span class="theme-info-val">' . $value . '</span>';
										}
										?>
									</ul>
								</div>
								<div class="theme-info-field">
									<textarea
										name="theme-info"><?php echo wp_json_encode( $current_theme_data ); ?></textarea>
								</div>
								<div class="theme-info-field">
									<label><?php esc_html_e( 'Your email for contact', 'groovy-menu' ); ?>
										<input
											name="email"
											type="email"
											value=""
											placeholder="<?php esc_html_e( 'add e-mail', 'groovy-menu' ); ?>"></label>
								</div>
								<div class="theme-info-field">
									<label><?php esc_html_e( 'Custom comment for submit', 'groovy-menu' ); ?>
										<input
											name="comment"
											type="text"
											value=""
											placeholder="<?php esc_html_e( 'add comment', 'groovy-menu' ); ?>">
									</label>
								</div>
							</div>
							<div class="gm-modal-footer">
								<div class="btn-group">
									<button
										type="submit"
										class="btn modal-btn"><?php esc_html_e( 'Submit theme integration request', 'groovy-menu' ); ?></button>
									<button
										type="button"
										class="btn modal-btn"
										data-dismiss="gm-modal"><?php esc_html_e( 'Close', 'groovy-menu' ); ?></button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
			<?php
		}


		public function edit() {
			$this->export();
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker-alpha', GROOVY_MENU_URL . 'assets/js/wp-color-picker-alpha.js', array( 'wp-color-picker' ), GROOVY_MENU_VERSION );

			?>
			<div class="gm-gui-container">
				<?php
				$this->renderTabs();
				$this->renderPanes();
				?>
			</div>
			<?php
			echo GroovyMenuRenderIconsModal();
			echo GroovyMenuPreviewModal();
		}


		public function renderTabs() {
			?>
			<div class="gm-gui-nav-wrapper">

				<div class="gm-gui__brand-wrapper">
					<a href="?page=groovy_menu_settings">
						<img
							class="gm-gui__brand-logo"
							src="<?php echo GROOVY_MENU_URL; ?>assets/images/groovy_doc_white.svg"
							alt="">
					</a>
				</div>
				<ul class="gm-gui__nav-tabs">
					<?php
					$first = true;
					foreach ( $this->settings()->getSettings() as $categoryName => $category ) {
						$this->renderTab( $categoryName, $category, $first );
						$first = false;
					}
					?>
					<button class="gm-gui__restore-btn">
						<span
							class="gm-gui__nav-tabs__item__txt"><?php _e( 'Restore <br>Defaults', 'groovy-menu' ); ?></span>
					</button>
				</ul>
			</div>
			<?php
		}


		public function renderGlobalSettingModal() {
			?>
			<!-- Global settings modal -->
			<div
				class="gm-modal gm-fade modal-centered"
				id="global-settings-modal"
				tabindex="-1">
				<div class="gm-modal-dialog modal-lg">
					<div class="gm-modal-content">
						<form
							method="post"
							action="?page=groovy_menu_settings&action=saveDashboardSettings"
							enctype="multipart/form-data"
							id="global-settings-form">
							<?php echo wp_nonce_field(); ?>
							<div class="gm-modal-body">
								<?php
								$this->renderTabsGlobal( $this->settings()->getSettingsGlobal() );
								$first = true;
								foreach ( $this->settings()->getSettingsGlobal() as $categoryName => $category ) {
									$this->renderTabGlobal( $category, $categoryName, $first );
									$first = false;
								}
								?>
							</div>
							<div class="gm-modal-footer">
								<div class="btn-group">
									<button
										type="submit"
										class="btn modal-btn"><?php esc_html_e( 'Save changes', 'groovy-menu' ); ?></button>
									<button
										type="button"
										class="btn modal-btn"
										data-dismiss="gm-modal"><?php esc_html_e( 'Close', 'groovy-menu' ); ?></button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
			<?php
		}


		/**
		 * @param $categoryName
		 * @param $category
		 * @param $isActive
		 */
		public function renderTab( $categoryName, $category, $isActive ) {
			?>
			<li class="gm-gui__nav-tabs__item <?php echo ( $isActive ) ? 'active' : ''; ?>">

				<a
					class="gm-gui__nav-tabs__item__anchor"
					href="#<?php echo esc_attr( $categoryName ); ?>"
					data-tab="<?php echo esc_attr( $categoryName ); ?>">
					<span class="gm-gui-icon <?php echo esc_attr( $category['icon'] ); ?>"></span>
					<span class="gm-gui__nav-tabs__item__txt"><?php echo esc_html( $category['title'] ); ?></span>
				</a>
				<?php
				$this->renderTabSublevel( $categoryName );
				?>
			</li>
			<?php
		}


		/**
		 * @param $categoryName
		 */
		public function renderTabSubLevel( $categoryName ) {
			?>
			<ul class="gm-gui__nav-tabs__sublevel">
				<?php foreach ( $this->settings()->getGroups( $categoryName ) as $sublevelKey => $sublevel ) { ?>
					<li class="gm-gui__nav-tabs__sublevel__item"
						<?php echo ( isset( $sublevel['condition'] ) ) ? ' data-condition=\'' . wp_json_encode( $sublevel['condition'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE ) . '\'' : ''; ?>
						<?php echo ( isset( $sublevel['condition_type'] ) ) ? ' data-condition_type="' . $sublevel['condition_type'] . '" ' : ''; ?>
					>
						<a
							href="#<?php echo esc_attr( $sublevelKey ); ?>"
							data-sublevel="<?php echo esc_attr( $sublevelKey ); ?>"
							class="gm-gui__nav-tabs__sublevel__item__anchor"><?php echo esc_html( $sublevel['title'] ); ?></a>
					</li>
				<?php } ?>
			</ul>
			<?php
		}

		public function renderPanes() {
			$title = $this->settings()->getPreset()->getName();
			?>
			<div class="gm-gui__tab-panes gm-clearfix">
				<form
					action=""
					method="post"
					class="gm-form"
					autocomplete="off"
					enctype="multipart/form-data"
					data-id="<?php echo esc_attr( $this->settings()->getPreset()->getId() ); ?>"
					data-version="<?php echo esc_attr( GROOVY_MENU_VERSION ); ?>">
					<div class="gm-gui__preset-name"><?php echo esc_html( $title ); ?></div>
					<input
						type="hidden"
						name="groovy_menu_save_theme"
						value="save"/>
					<?php
					wp_nonce_field( 'groovy_menu_save_theme' );
					$first = true;
					foreach ( $this->settings()->getSettings() as $categoryName => $category ) {
						$this->renderPane( $categoryName, $category, $first );
						$first = false;
					}
					?>
				</form>
			</div>
			<?php
		}

		/**
		 * @param $categoryName
		 * @param $category
		 * @param $isActive
		 */
		public function renderPane( $categoryName, $category, $isActive ) {
			?>
			<div
				class="tab-pane <?php echo ( $isActive ) ? 'active' : ''; ?>"
				id="<?php echo esc_attr( $categoryName ); ?>">
				<span class="tab-pane__header"><?php echo esc_html( $category['title'] ); ?></span>

				<div class="gm-gui-btn-group">
					<button
						class="gm-gui-btn gm-gui-preview-btn"
						type="button">
						<i class="fa fa-search"></i>
						<?php esc_html_e( 'Preview', 'groovy-menu' ); ?>
					</button>
					<?php if ( GroovyMenuRoleCapabilities::presetEdit( true ) ) : ?>
						<button
							class="gm-gui-btn gm-gui-restore-section-btn"
							type="submit">
							<i class="fa fa-undo"></i>
							<?php esc_html_e( 'Restore', 'groovy-menu' ); ?>
						</button>
						<button
							class="gm-gui-btn gm-gui-save-btn"
							type="submit">
							<i class="fa fa-floppy-o"></i><?php esc_html_e( 'Save', 'groovy-menu' ); ?>
						</button>
					<?php endif; ?>
				</div>
				<div>
					<?php
					foreach ( $category['fields'] as $name => $field ) {
						$this->renderField( $categoryName, $name, $field );
					}
					?>
				</div>
			</div>
			<?php
		}

		/**
		 * @param $categoryName
		 * @param $name
		 * @param $field
		 */
		public function renderField( $categoryName, $name, $field ) {
			$this->settings()->getField( $categoryName, $name )->render();
		}

		function getSettings() {
			if ( defined( 'DOING_AJAX' ) && DOING_AJAX && ! empty( $_POST ) && isset( $_POST['action'] ) && $_POST['action'] === 'gm_get_setting' ) {

				$preset_id = empty( $_POST['preset_id'] ) ? '' : esc_attr( trim( $_POST['preset_id'] ) );

				if ( empty( $preset_id ) ) {
					// Send a JSON response back to an AJAX request, and die().
					wp_send_json_error( esc_html__( 'Error. Missing id of the current menu', 'groovy-menu' ) );
				}

				$styles = new GroovyMenuStyle( $preset_id );

				$groovyMenuSettings           = $styles->serialize();
				$groovyMenuSettings['preset'] = array(
					'id'   => $styles->getPreset()->getId(),
					'name' => $styles->getPreset()->getName()
				);

				wp_send_json_success( $groovyMenuSettings );
			}
		}

		function saveSettings() {
			$cap_can = GroovyMenuRoleCapabilities::presetEdit( true );

			if ( $cap_can && defined( 'DOING_AJAX' ) && DOING_AJAX && ! empty( $_POST ) && isset( $_POST['action'] ) && $_POST['action'] === 'gm_save' ) {

				$ajax_data = [];
				parse_str( $_POST['data'], $ajax_data );
				if ( empty( $ajax_data ) ) {
					// Send a JSON response back to an AJAX request, and die().
					wp_send_json_error( esc_html__( 'Error. Bad form data', 'groovy-menu' ) );
				}

				$referer_url = parse_url( $ajax_data['_wp_http_referer'] );
				parse_str( $referer_url['query'], $referer_url );

				if ( empty( $referer_url ) || ! isset( $referer_url['id'] ) || ! $referer_url['id'] ) {
					// Send a JSON response back to an AJAX request, and die().
					wp_send_json_error( esc_html__( 'Error. Missing id of the current menu', 'groovy-menu' ) );
				}

				if ( isset( $ajax_data['groovy_menu_save_theme'] ) && $ajax_data['groovy_menu_save_theme'] === 'save' ) {

					update_post_meta( intval( esc_attr( $referer_url['id'] ) ), 'gm_preset_screenshot', '' );

					if ( ! empty( $ajax_data['menu'] ) && is_array( $ajax_data['menu'] ) ) {
						$preset_settings = $this->settings( $referer_url['id'] )->getSettings();

						foreach ( $ajax_data['menu'] as $group => $group_data ) {
							foreach ( $group_data as $option => $value ) {

								$option_data = $preset_settings[ $group ]['fields'][ $option ];

								if ( isset( $option_data['type'] ) && 'number' === $option_data['type'] ) {
									$ajax_data['menu'][ $group ][ $option ] = intval( $value );
								} elseif ( isset( $option_data['type'] ) && 'checkbox' === $option_data['type'] ) {
									if ( 'false' === $value || '0' === $value ) {
										$value = '';
									}
									$ajax_data['menu'][ $group ][ $option ] = empty( $value ) ? false : true;
								}
							}
						}
					}

					$this->settings( $referer_url['id'] )->update( $ajax_data['menu'] );

					// Answer by default.
					$respond = esc_html__( 'Save', 'groovy-menu' );
					if ( ! empty( $_POST['sub_action'] ) ) {
						switch ( $_POST['sub_action'] ) {
							case 'save':
								$respond = esc_html__( 'Save', 'groovy-menu' );
								break;
							case 'restore':
								$respond = esc_html__( 'Restore', 'groovy-menu' );
								break;
						}
					}

					if ( function_exists( 'groovy_menu_check_gfonts_params' ) ) {
						groovy_menu_check_gfonts_params();
					}

					// Send a JSON response back to an AJAX request, and die().
					wp_send_json_success( $respond );

				} else {
					// Send a JSON response back to an AJAX request, and die().
					wp_send_json_error( esc_html__( 'Error. Wrong form data for save', 'groovy-menu' ) );
				}
			}
		}

		function saveStyles() {
			$cap_can = true;

			// Still can work from front-end.
			if ( is_admin() && ! GroovyMenuRoleCapabilities::presetEdit( true ) ) {
				$cap_can = false;
			}

			if ( $cap_can && defined( 'DOING_AJAX' ) && DOING_AJAX && ! empty( $_POST ) && isset( $_POST['action'] ) && $_POST['action'] === 'gm_save_styles' ) {

				$ajax_data = empty( $_POST['data'] ) ? '' : trim( $_POST['data'] );
				$direction = empty( $_POST['direction'] ) ? '' : trim( $_POST['direction'] );
				$preset_id = empty( $_POST['preset_id'] ) ? '' : trim( $_POST['preset_id'] );

				if ( empty( $ajax_data ) ) {
					// Send a JSON response back to an AJAX request, and die().
					wp_send_json_error( esc_html__( 'Error. Bad style data', 'groovy-menu' ) );
				}

				if ( empty( $direction ) ) {
					// Send a JSON response back to an AJAX request, and die().
					wp_send_json_error( esc_html__( 'Error. Missing direction of the current menu', 'groovy-menu' ) );
				}

				if ( empty( $preset_id ) ) {
					// Send a JSON response back to an AJAX request, and die().
					wp_send_json_error( esc_html__( 'Error. Missing id of the current menu', 'groovy-menu' ) );
				}


				$preset_key = md5( rand() . uniqid() . time() );

				update_post_meta( intval( $preset_id ), 'gm_compiled_css', $ajax_data );
				update_post_meta( intval( $preset_id ), 'gm_preset_key', $preset_key );
				update_post_meta( intval( $preset_id ), 'gm_direction', $direction );
				update_post_meta( intval( $preset_id ), 'gm_version', GROOVY_MENU_VERSION );

				// Save compiled_css to file
				$this->save_compiled_css( $preset_id, $ajax_data );

				$respond = esc_html__( 'Save', 'groovy-menu' );
				if ( ! empty( $_POST['sub_action'] ) ) {
					switch ( $_POST['sub_action'] ) {
						case 'save':
							$respond = esc_html__( 'Saved', 'groovy-menu' );
							break;
						case 'restore':
							$respond = esc_html__( 'Current section restored to default', 'groovy-menu' );
							break;
						case 'restore_all':
							$respond = esc_html__( 'All Settings restored to default', 'groovy-menu' );
							break;
					}
				}

				// Send a JSON response back to an AJAX request, and die().
				wp_send_json_success( $respond );

			}
		}


		/**
		 * Save preset compiled style to the file
		 *
		 * @param string|integer $preset_id    preset id.
		 * @param string         $compiled_css styles.
		 * @param string         $_tmppath     path for download.
		 *
		 * @return bool
		 */
		private function save_compiled_css( $preset_id, $compiled_css = '' ) {


			$preset_id = intval( $preset_id );

			if ( empty( $preset_id ) ) {
				// if err.
				return false;
			}

			if ( empty( $compiled_css ) ) {
				$compiled_css = '';
			}

			if ( ! defined( 'FS_METHOD' ) ) {
				define( 'FS_METHOD', 'direct' );
			}

			global $wp_filesystem;
			if ( empty( $wp_filesystem ) ) {
				if ( file_exists( ABSPATH . '/wp-admin/includes/file.php' ) ) {
					require_once ABSPATH . '/wp-admin/includes/file.php';
					WP_Filesystem();
				}
			}
			if ( empty( $wp_filesystem ) ) {
				// if err.
				return false;
			}

			$styles_dir = GroovyMenuUtils::getUploadDir();
			if ( ! is_dir( $styles_dir ) ) {
				@mkdir( $styles_dir, 0755 );
			}

			$css_filename = 'preset_' . esc_attr( strval( $preset_id ) ) . '.css';

			$handled_compiled_css = trim( stripcslashes( $compiled_css ) );

			$wp_filesystem->put_contents( $styles_dir . $css_filename, $handled_compiled_css, FS_CHMOD_FILE );
		}


		public function saveAutoIntegration() {
			if ( ! GroovyMenuRoleCapabilities::globalOptions( true ) ) {
				return;
			}

			if ( defined( 'DOING_AJAX' ) && DOING_AJAX && ! empty( $_POST ) && isset( $_POST['action'] ) && $_POST['action'] === 'gm_save_auto_integration' ) {

				$ajax_data = ( empty( $_POST['data'] ) || 'false' === $_POST['data'] ) ? false : true;

				global $gm_supported_module;
				$theme_name = empty( $gm_supported_module['theme'] ) ? wp_get_theme()->get_template() : $gm_supported_module['theme'];

				// Save automatic integrations settings
				update_option( GroovyMenuUtils::getAutoIntegrationOptionName() . $theme_name, $ajax_data, true );

				$respond = esc_html__( 'Save', 'groovy-menu' );

				// Send a JSON response back to an AJAX request, and die().
				wp_send_json_success( $respond );

			}
		}

		public function saveSingleLocationIntegration() {
			if ( ! GroovyMenuRoleCapabilities::globalOptions( true ) ) {
				return;
			}

			if ( defined( 'DOING_AJAX' ) && DOING_AJAX && ! empty( $_POST ) && isset( $_POST['action'] ) && $_POST['action'] === 'gm_save_single_location_integration' ) {

				$ajax_data = ( empty( $_POST['data'] ) || '' === $_POST['data'] ) ? '' : esc_attr( $_POST['data'] );

				global $gm_supported_module;
				$theme_name = empty( $gm_supported_module['theme'] ) ? wp_get_theme()->get_template() : $gm_supported_module['theme'];

				$saved_config = get_option( GroovyMenuUtils::getIntegrationConfigOptionName() . $theme_name );

				if ( empty( $saved_config ) || ! is_array( $saved_config ) ) {
					$saved_config = array(
						'single_location' => '',
					);
				}

				$saved_config['single_location'] = esc_sql( $ajax_data );

				// Save automatic integrations settings
				$saved = update_option( GroovyMenuUtils::getIntegrationConfigOptionName() . $theme_name, $saved_config, true );

				if ( $saved ) {
					$respond = esc_html__( 'Save', 'groovy-menu' );
				} else {
					$respond = esc_html__( 'Save Error', 'groovy-menu' );
				}

				// Send a JSON response back to an AJAX request, and die().
				wp_send_json_success( $respond );

			}
		}

		/**
		 * Function return Google fonts
		 *
		 * @return void
		 */
		public function getGoogleFonts() {
			$fonts = array();

			if ( defined( 'DOING_AJAX' ) && DOING_AJAX && ! empty( $_POST ) && isset( $_POST['action'] ) && $_POST['action'] === 'gm_get_google_fonts' ) {

				$googleFonts = include GROOVY_MENU_DIR . 'includes/fonts-google.php';

				wp_send_json_success( $googleFonts );

			}

		}

		public function export() {
			if ( GroovyMenuRoleCapabilities::canExport( true ) && isset( $_GET['export'] ) ) {
				ob_clean();
				$filename = str_replace( ' ', '_', $this->settings()->getPreset()->getName() );
				header( 'Content-Type: text/json' );
				header( 'Content-Disposition: attachment; filename="' . $filename . '.json"' );

				$export['settings'] = $this->settings()->getSettingsArray( true );
				$export['name']     = $this->settings()->getPreset()->getName();
				$export['img']      = GroovyMenuPreset::getPreviewById( $this->settings()->getPreset()->getId() );
				echo wp_json_encode( $export, JSON_PRETTY_PRINT );
				exit;
			}
		}

		/**
		 * @param $settings
		 */
		protected function renderTabsGlobal( $settings ) {
			$first = true;
			echo '<div class="groovy-tabs">';
			foreach ( $settings as $categoryName => $category ) {
				echo '<a href="#" data-tab="' . $categoryName . '" class="groovy-tab ' . ( $first ? 'groovy-tab-active' : '' ) . '">' . $category['title'] . '</a>';
				$first = false;
			}
			echo '</div>';
		}

		/**
		 * @param $category
		 * @param $categoryName
		 * @param $active
		 */
		protected function renderTabGlobal( $category, $categoryName, $active ) {
			echo '<div class="groovy-tab-pane ' . ( $active ? 'groovy-tab-pane-active' : '' ) . '" id="groovy-tab-' . $categoryName . '">';
			foreach ( $category['fields'] as $name => $field ) {
				$this->renderField( $categoryName, $name, $field );
			}
			echo '</div>';
		}

		/**
		 * @return array
		 */
		protected function getPresetsFromApi() {
			$styles        = new GroovyMenuStyle();
			$allow_library = $styles->getGlobal( 'tools', 'allow_import_online_library' ) ? : false;
			if ( ! $allow_library ) {
				return [];
			}

			$transient_name     = 'groovy_menu_presets_from_api';
			$saved_api_response = get_transient( $transient_name );
			if ( false !== $saved_api_response && is_array( $saved_api_response ) ) {

				return $saved_api_response;
			}

			$remote_content_obj = wp_remote_get( 'https://api.groovy.grooni.com/preset/list.json', array( 'timeout' => 6 ) );
			if ( wp_remote_retrieve_response_code( $remote_content_obj ) === 200 ) {
				$response = json_decode( wp_remote_retrieve_body( $remote_content_obj ), true );
			}

			if ( empty( $response ) || empty( $response['presets'] ) || ! is_array( $response['presets'] ) ) {
				return [];
			}

			set_transient( $transient_name, $response['presets'], DAY_IN_SECONDS );

			return $response['presets'];
		}


		/**
		 * @param $id
		 *
		 * @return array|mixed
		 */
		protected function getPresetsFromApiById( $id ) {
			$presets = $this->getPresetsFromApi();
			$preset  = array();

			$id = $id ? intval( $id ) : false;

			foreach ( $presets as $_preset ) {
				if ( intval( $_preset['id'] ) === intval( $id ) ) {
					$preset = $_preset;
				}
			}

			return $preset;
		}

		/**
		 * @param $url
		 *
		 * @return array
		 */
		protected function getDataFromApi( $url ) {

			$remote_content_obj = wp_remote_get( $url, array( 'timeout' => 6 ) );

			if ( wp_remote_retrieve_response_code( $remote_content_obj ) === 200 ) {
				$response = wp_remote_retrieve_body( $remote_content_obj );
			}

			if ( ! empty( $response ) ) {
				$response = json_decode( $response, true );
			}

			if ( ! empty( $response ) && is_array( $response ) ) {
				return $response;
			}

			return array();
		}


		public function getPresetDataFromApiById( $id ) {
			$preset = $this->getPresetsFromApiById( $id );
			if ( ! empty( $preset['url'] ) ) {
				$data = $this->getDataFromApi( $preset['url'] );
			}
			if ( ! empty( $data ) && is_array( $data ) ) {
				return $data;
			}

			return array();
		}

		public function search_filter( $query ) {
			if ( $query->is_search && ! is_admin() ) {
				if ( ! empty( $_GET['post_type'] ) ) {

					$all_post_types = GroovyMenuUtils::getPostTypes( true );

					$post_type = trim( $_GET['post_type'] );
					switch ( $post_type ) {

						case 'post':
							$query->set( 'post_type', array( 'post' ) );
							break;

						case 'page':
							$query->set( 'post_type', array( 'page' ) );
							break;

						case 'shop':
						case 'product':
							if ( isset( $all_post_types['product'] ) ) {
								$query->set( 'post_type', array( 'product' ) );
							}
							break;

						case '':
							break;

						default:
							if ( isset( $all_post_types[ $post_type ] ) ) {
								$query->set( 'post_type', array( $post_type ) );
							}
							break;

					}
				}
			}

			return $query;
		}

	}

}
