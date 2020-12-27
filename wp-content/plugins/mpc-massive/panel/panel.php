<?php
/*----------------------------------------------------------------------------*\
	PANEL
\*----------------------------------------------------------------------------*/

if ( ! class_exists( 'MPC_Panel' ) ) {
	class MPC_Panel {
		public $presets_list;
		public $contents_list;
		private $defaults = array(
			'easy_mode'            => '1',
			'animations_on_mobile' => '0',
			'parallax_on_mobile'   => '0',
			'single_js_css'        => '1',
			'vc_row_addons'        => '1',
			'magnific_popup'       => '1',
			'purchase_code'        => '',
			'google_api'           => '',
			'scroll_to_id'         => '1',
			'use_decoder'          => '0',
			'disable_google_fonts' => '0',
			'enabled_shortcodes'   => array(
				// Shortcodes
				'all'                        => '1',
				'mpc_accordion'              => '1',
				'mpc_alert'                  => '1',
				'mpc_animated_text'          => '1',
				'mpc_button'                 => '1',
				'mpc_button_set'             => '1',
				'mpc_callout'                => '1',
				'mpc_carousel_anything'      => '1',
				'mpc_carousel_image'         => '1',
				'mpc_carousel_posts'         => '1',
				'mpc_carousel_slider'        => '1',
				'mpc_carousel_testimonial'   => '1',
				'mpc_chart'                  => '1',
				'mpc_circle_icons'           => '1',
				'mpc_connected_icons'        => '1',
				'mpc_countdown'              => '1',
				'mpc_counter'                => '1',
				'mpc_cubebox'                => '1',
				'mpc_divider'                => '1',
				'mpc_dropcap'                => '1',
				'mpc_flipbox'                => '1',
				'mpc_grid_anything'          => '1',
				'mpc_grid_images'            => '1',
				'mpc_grid_posts'             => '1',
				'mpc_icon'                   => '1',
				'mpc_icon_list'              => '1',
				'mpc_icon_column'            => '1',
				'mpc_ihover'                 => '1',
				'mpc_image'                  => '1',
				'mpc_interactive_image'      => '1',
				'mpc_lightbox'               => '1',
				'mpc_mailchimp'              => '1',
				'mpc_map'                    => '1',
				'mpc_modal'                  => '1',
				'mpc_pricing_box'            => '1',
				'mpc_progress'               => '1',
				'mpc_qrcode'                 => '1',
				'mpc_quote'                  => '1',
				'mpc_single_post'            => '1',
				'mpc_tabs'                   => '1',
				'mpc_testimonial'            => '1',
				'mpc_textblock'              => '1',
				'mpc_timeline_basic'         => '1',
				'mpc_timeline_item'          => '1',
				'mpc_wc_add_to_cart'         => '1',
				'mpc_wc_category'            => '1',
				'mpc_wc_product'             => '1',
				'mpc_wc_carousel_categories' => '1',
				'mpc_wc_carousel_products'   => '1',
				'mpc_wc_grid_categories'     => '1',
				'mpc_wc_grid_products'       => '1',

				// Sub-shortcodes
				'mpc_ihover_item'            => '1',
				'mpc_hotspot'                => '1',
				'mpc_marker'                 => '1',
				'mpc_pricing_column'         => '1',
				'mpc_pricing_legend'         => '1',
				'mpc_navigation'             => '1',
				'mpc_pagination'             => '1',
				'mpc_ribbon'                 => '1',
				'mpc_tooltip'                => '1',
			),
		);

		function __construct() {
			$this->presets_list = array(
				'mpc_accordion'       => __( 'Accordion', 'mpc' ),
				'mpc_alert'           => __( 'Alert', 'mpc' ),
				'mpc_animated_text'   => __( 'Animated Text', 'mpc' ),
				'mpc_button'          => __( 'Button', 'mpc' ),
				'mpc_button_set'      => __( 'Button Set', 'mpc' ),
				'mpc_callout'         => __( 'Callout', 'mpc' ),
				'mpc_carousel_image'  => __( 'Carousel Image', 'mpc' ),
				'mpc_carousel_posts'  => __( 'Carousel Posts', 'mpc' ),
				'mpc_carousel_slider' => __( 'Carousel Slider', 'mpc' ),
				'mpc_chart'           => __( 'Chart', 'mpc' ),
				'mpc_countdown'       => __( 'Countdown', 'mpc' ),
				'mpc_counter'         => __( 'Counter', 'mpc' ),
				'mpc_divider'         => __( 'Divider', 'mpc' ),
				'mpc_dropcap'         => __( 'Dropcap', 'mpc' ),
				'mpc_grid_images'     => __( 'Grid Images', 'mpc' ),
				'mpc_grid_posts'      => __( 'Grid Posts', 'mpc' ),
				'mpc_hotspot'         => __( 'Hotspot', 'mpc' ),
				'mpc_icon'            => __( 'Icon', 'mpc' ),
				'mpc_icon_list'       => __( 'Icon List', 'mpc' ),
				'mpc_icon_column'     => __( 'Info Box', 'mpc' ),
				'mpc_connected_icons' => __( 'Info List', 'mpc' ),
				'mpc_ihover'          => __( 'iHover', 'mpc' ),
				'mpc_image'           => __( 'Image', 'mpc' ),
				'mpc_lightbox'        => __( 'Lightbox', 'mpc' ),
				'mpc_modal'           => __( 'Modal', 'mpc' ),
				'mpc_mailchimp'       => __( 'MailChimp', 'mpc' ),
				'mpc_pricing_box'     => __( 'Pricing Box', 'mpc' ),
				'mpc_pricing_column'  => __( 'Pricing Column', 'mpc' ),
				'mpc_pricing_legend'  => __( 'Pricing Legend', 'mpc' ),
				'mpc_progress'        => __( 'Progress', 'mpc' ),
				'mpc_quote'           => __( 'Quote', 'mpc' ),
				'mpc_ribbon'          => __( 'Ribbon', 'mpc' ),
				'mpc_single_post'     => __( 'Single Post', 'mpc' ),
				'mpc_tabs'            => __( 'Tabs', 'mpc' ),
				'mpc_testimonial'     => __( 'Testimonial', 'mpc' ),
//				'mpc_textblock'       => __( 'Text Block', 'mpc' ),
				'mpc_timeline_basic'  => __( 'Timeline Basic', 'mpc' ),
				'mpc_timeline_item'   => __( 'Timeline Item', 'mpc' ),
				'mpc_tooltip'         => __( 'Tooltip', 'mpc' ),

				'mpc_wc_add_to_cart'         => __( 'WC Add To Cart', 'mpc' ),
				'mpc_wc_category'            => __( 'WC Category', 'mpc' ),
				'mpc_wc_product'             => __( 'WC Product', 'mpc' ),
				'mpc_wc_carousel_categories' => __( 'WC Carousel Categories', 'mpc' ),
				'mpc_wc_grid_categories'     => __( 'WC Grid Categories', 'mpc' ),
				'mpc_wc_carousel_products'   => __( 'WC Carousel Products', 'mpc' ),
				'mpc_wc_grid_products'       => __( 'WC Grid Products', 'mpc' ),

				'mpc_navigation' => __( 'Navigation', 'mpc' ),
				'mpc_pagination' => __( 'Pagination', 'mpc' ),

				'typography' => __( 'Typography', 'mpc' ),
			);

			$this->contents_list = array(
				'mpc_accordion'         => __( 'Accordion', 'mpc' ),
				'mpc_button_set'        => __( 'Button Set', 'mpc' ),
				'vc_column'             => __( 'Column', 'mpc' ),
				'mpc_cubebox'           => __( 'Cubebox', 'mpc' ),
				'mpc_flipbox'           => __( 'Flipbox', 'mpc' ),
				'mpc_interactive_image' => __( 'Image with Hotspots', 'mpc' ),
				'mpc_circle_icons'      => __( 'Info Circle', 'mpc' ),
				'mpc_connected_icons'   => __( 'Info List', 'mpc' ),
				'mpc_modal'             => __( 'Modal', 'mpc' ),
				'mpc_pricing_box'       => __( 'Pricing Box', 'mpc' ),
				'vc_row'                => __( 'Row', 'mpc' ),
				'mpc_tabs'              => __( 'Tabs', 'mpc' ),
			);

			global $mpc_paths;
			$pack_list = array();
			foreach ( $this->presets_list as $shortcode => $name ) {
				foreach ( $mpc_paths as $plugin => $paths ) {
					if ( file_exists( $paths[ 'dir' ] . '/assets/presets/' . $shortcode . '.json' ) ) {
						$pack_list[ $shortcode ] = 1;
					}
				}
			}

			$this->presets_list = array_intersect_key( $this->presets_list, $pack_list );

			$pack_list = array();
			foreach ( $this->contents_list as $shortcode => $name ) {
				foreach ( $mpc_paths as $plugin => $paths ) {
					if ( file_exists( $paths[ 'dir' ] . '/assets/contents/' . $shortcode . '.json' ) ) {
						$pack_list[ $shortcode ] = 1;
					}
				}
			}

			$this->contents_list = array_intersect_key( $this->contents_list, $pack_list );

			add_action( 'admin_menu', array( $this, 'register_panel_page' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'panel_scripts' ) );
			add_action( 'visual-composer_page_vc-roles', array( $this, 'role_manager_scripts' ) );

			add_action( 'wp_ajax_mpc_get_presets', array( $this, 'get_presets' ) );
			add_action( 'mpc_install_template_presets', array( $this, 'install_selected_template_presets' ) );
			add_action( 'wp_ajax_mpc_install_presets', array( $this, 'install_selected_presets' ) );
			add_action( 'wp_ajax_mpc_install_all_presets', array( $this, 'install_all_presets' ) );
			add_action( 'wp_ajax_mpc_delete_presets', array( $this, 'delete_selected_presets' ) );

			add_action( 'wp_ajax_mpc_get_contents', array( $this, 'get_contents' ) );
			add_action( 'wp_ajax_mpc_install_contents', array( $this, 'install_selected_contents' ) );
			add_action( 'wp_ajax_mpc_install_all_contents', array( $this, 'install_all_contents' ) );
			add_action( 'wp_ajax_mpc_delete_contents', array( $this, 'delete_selected_contents' ) );

			add_action( 'wp_ajax_mpc_save_panel', array( $this, 'save_panel' ) );
			add_action( 'wp_ajax_mpc_download_images', array( $this, 'download_images' ) );
			add_action( 'wp_ajax_mpc_export_presets', array( $this, 'export_presets' ) );

			if ( get_transient( 'mpc_setup_wizard' ) ) {
				$this->setup_defaults();
			}
		}

		/* PANEL PAGE - REGISTER */
		function register_panel_page() {
			add_menu_page( __( 'Massive Panel', 'mpc' ), __( 'Massive Panel', 'mpc' ), 'manage_options', 'ma-panel', array( $this, 'panel_page' ), 'dashicons-lightbulb', '3.11' );
		}

		/* PANEL PAGE - SCRIPTS */
		function panel_scripts( $hook ) {
			if ( $hook != 'toplevel_page_ma-panel' ) {
				return;
			}

			wp_enqueue_style( 'mpc-panel-css', mpc_get_plugin_path( __FILE__ ) . '/assets/css/mpc-panel.css' );

			wp_enqueue_script( 'mpc-panel-waypoints-js', mpc_get_plugin_path( __FILE__ ) . '/assets/js/vendor/waypoints.base.min.js', array( 'jquery' ), MPC_MASSIVE_VERSION, true );
			wp_enqueue_script( 'mpc-panel-js', mpc_get_plugin_path( __FILE__ ) . '/assets/js/mpc-panel.js', array( 'jquery', 'underscore', 'mpc-panel-waypoints-js' ), MPC_MASSIVE_VERSION, true );
		}
		function role_manager_scripts() {
			wp_enqueue_style( 'mpc-panel-shicons', mpc_get_plugin_path( __FILE__ ) . '/assets/css/mpc-icons-admin.css' );
		}

		/* PANEL PAGE - MARKUP */
		function panel_page() {
			$local_url = wp_upload_dir();
			$local_url = $local_url[ 'baseurl' ] . '/';

			?>

			<div id="mpc_panel" class="mpc-panel">
				<!-- HEADER -->
				<header class="mpc-panel__header">
					<img class="mpc-panel__logo" src="<?php echo mpc_get_plugin_path( __FILE__ ); ?>/assets/images/logo_dark.png" alt="Logo" width="56" height="56">
					<h1 class="mpc-panel__name">
						<?php _e( 'Massive Panel', 'mpc' ); ?>
						<?php if ( defined( 'MPC_MASSIVE_FULL' ) ) : ?>
						<span class="mpc-panel__version">v<?php echo MPC_MASSIVE_VERSION; ?></span>
						<?php endif; ?>
					</h1>
				</header>

				<!-- MAIN SETTINGS -->
				<div class="mpc-section mpc-section--main">
					<h2 class="mpc-section__title"><?php _e( 'Settings', 'mpc' ); ?></h2>
					<div class="mpc-section__content">

						<?php $this->panel_section__options(); ?>

					</div>
				</div>

				<!-- SHORTCODES SELECT -->
				<?php if ( defined( 'MPC_MASSIVE_FULL' ) ) : ?>
				<div class="mpc-section mpc-section--shortcodes">
					<h2 class="mpc-section__title"><?php _e( 'Shortcodes', 'mpc' ); ?></h2>
					<div class="mpc-section__content">

						<?php $this->panel_section__shortcodes(); ?>

					</div>
				</div>
				<?php endif; ?>

				<!-- PURCHASE VERIFICATION -->
				<?php if ( defined( 'MPC_MASSIVE_FULL' ) ) : ?>
				<div class="mpc-section mpc-section--purchase-verification" data-type="style">
					<h2 class="mpc-section__title"><?php _e( 'Purchase Verification', 'mpc' ); ?></h2>
					<div class="mpc-section__content">

						<?php $this->panel_section__purchase_verification(); ?>

					</div>
				</div>
				<?php endif; ?>

				<!-- GOOGLE API -->
				<div class="mpc-section mpc-section--google-api" data-type="style">
					<h2 class="mpc-section__title"><?php _e( 'Google API', 'mpc' ); ?></h2>
					<div class="mpc-section__content">

						<?php $this->panel_section__google_api(); ?>

					</div>
				</div>

				<!-- PRESETS INSTALLER -->
				<div class="mpc-section mpc-section--presets" data-type="style">
					<h2 class="mpc-section__title"><?php _e( 'Style Presets', 'mpc' ); ?></h2>
					<div class="mpc-section__content">

						<?php $this->panel_section__style_presets(); ?>

					</div>
				</div>

				<!-- PRESETS INSTALLER -->
				<div class="mpc-section mpc-section--presets" data-type="content">
					<h2 class="mpc-section__title"><?php _e( 'Content Presets', 'mpc' ); ?></h2>
					<div class="mpc-section__content">

						<?php $this->panel_section__content_presets(); ?>

					</div>
				</div>

				<!-- PRESETS INSTALLER -->
				<div class="mpc-section mpc-section--download">
					<h2 class="mpc-section__title"><?php _e( 'Preset Previews Images', 'mpc' ); ?></h2>
					<div class="mpc-section__content">

						<?php $this->panel_section__preset_previews(); ?>

					</div>
				</div>

				<!-- FOOTER -->
				<footer class="mpc-panel__footer">
					<a href="#save" id="mpc_panel__save" class="mpc-panel__save mpc-panel__primary">
						<span class="mpc-default"><?php _e( 'Save panel', 'mpc' ); ?></span>
						<span class="mpc-working"><?php _e( 'Saving...', 'mpc' ); ?></span>
						<span class="mpc-finished"><?php _e( 'Saved :)', 'mpc' ); ?></span>
						<span class="mpc-save__progress mpc-progress"></span>
					</a>
				</footer>

				<div id="mpc_panel__error" class="mpc-panel__error">
					<i class="dashicons dashicons-warning"></i>
					<span><?php _e( 'Something went wrong. Please try again :)', 'mpc' ); ?></span>
				</div>
			</div>

			<script id="mpc_templates__preset" type="text/template" >
				<div class="mpc-preset<% if ( is_installed ) { %> mpc-installed<% } %>" data-preset="<%= preset %>">
					<% if ( url ) { %><img src="<?php echo mpc_get_plugin_path( __FILE__ ) . '/assets/images/mpc-image-placeholder.png'; ?>" data-src="<%= url %>" width="240" height="100" alt="<?php _e( 'Preset', 'mpc' ); ?>"><% } %>
					<p><%= title %></p>
					<div class="mpc-installed-badge"><i class="dashicons dashicons-yes"></i></div>
				</div>
			</script>
			<input id="mpc_previews_source" type="hidden" value="<?php echo get_option( 'mpc_previews_source' ) ? get_option( 'mpc_previews_source' ) : 'https://products.mpcthemes.net/ma/'; ?>" data-local="<?php echo esc_url( $local_url ); ?>">

			<?php wp_nonce_field( 'mpc-ma-panel' );

			do_action( 'mpc_panel_exports' );
		}

		/* PANEL SECTION - OPTIONS */
		function panel_section__options() {
			$options = get_option( 'mpc_ma_options' );

			if ( is_array( $options ) ) {
				$options = array_replace_recursive( $this->defaults, $options );
			} else {
				$options = $this->defaults;
			}

			?>

			<div class="mpc-option">
				<label class="mpc-option-state">
					<input type="checkbox" class="checkbox mpc-easy_mode" id="easy_mode" name="easy_mode" value="1" <?php checked( $options[ 'easy_mode' ], '1' ); ?>>
					<span class="mpc-switch"><span class="mpc-state--on"><?php _e( 'on', 'mpc' ); ?></span><span class="mpc-thumb"></span><span class="mpc-state--off"><?php _e( 'off', 'mpc' ); ?></span></span>
				</label>
				<span class="mpc-option-name"><?php _e( 'Easy Mode', 'mpc' ); ?></span>
			</div>

			<div class="mpc-option">
				<label class="mpc-option-state">
					<input type="checkbox" class="checkbox mpc-animations_on_mobile" id="animations_on_mobile" name="animations_on_mobile" value="1" <?php checked( $options[ 'animations_on_mobile' ], '1' ); ?>>
					<span class="mpc-switch"><span class="mpc-state--on"><?php _e( 'on', 'mpc' ); ?></span><span class="mpc-thumb"></span><span class="mpc-state--off"><?php _e( 'off', 'mpc' ); ?></span></span>
				</label>
				<span class="mpc-option-name"><?php _e( 'Animations on Mobile', 'mpc' ); ?></span>
			</div>

			<div class="mpc-option">
				<label class="mpc-option-state">
					<input type="checkbox" class="checkbox mpc-parallax_on_mobile" id="parallax_on_mobile" name="parallax_on_mobile" value="1" <?php checked( $options[ 'parallax_on_mobile' ], '1' ); ?>>
					<span class="mpc-switch"><span class="mpc-state--on"><?php _e( 'on', 'mpc' ); ?></span><span class="mpc-thumb"></span><span class="mpc-state--off"><?php _e( 'off', 'mpc' ); ?></span></span>
				</label>
				<span class="mpc-option-name"><?php _e( 'Parallax on Mobile', 'mpc' ); ?></span>
			</div>

			<?php if ( defined( 'MPC_MASSIVE_FULL' ) ) : ?>
			<div class="mpc-option">
				<label class="mpc-option-state">
					<input type="checkbox" class="checkbox mpc-single_js_css" id="single_js_css" name="single_js_css" value="1" <?php checked( $options[ 'single_js_css' ], '1' ); ?>>
					<span class="mpc-switch"><span class="mpc-state--on"><?php _e( 'on', 'mpc' ); ?></span><span class="mpc-thumb"></span><span class="mpc-state--off"><?php _e( 'off', 'mpc' ); ?></span></span>
				</label>
				<span class="mpc-option-name"><?php _e( 'Single JS/CSS file for All Shortcodes', 'mpc' ); ?></span>
			</div>
			<?php endif; ?>

			<div class="mpc-option">
				<label class="mpc-option-state">
					<input type="checkbox" class="checkbox mpc-vc_row_addons" id="vc_row_addons" name="vc_row_addons" value="1" <?php checked( $options[ 'vc_row_addons' ], '1' ); ?>>
					<span class="mpc-switch"><span class="mpc-state--on"><?php _e( 'on', 'mpc' ); ?></span><span class="mpc-thumb"></span><span class="mpc-state--off"><?php _e( 'off', 'mpc' ); ?></span></span>
				</label>
				<span class="mpc-option-name"><?php _e( 'Row/Column Addons (separator, overlay, toggle, animation)', 'mpc' ); ?></span>
			</div>

			<div class="mpc-option">
				<label class="mpc-option-state">
					<input type="checkbox" class="checkbox mpc-magnific_popup" id="magnific_popup" name="magnific_popup" value="1" <?php checked( $options[ 'magnific_popup' ], '1' ); ?>>
					<span class="mpc-switch"><span class="mpc-state--on"><?php _e( 'on', 'mpc' ); ?></span><span class="mpc-thumb"></span><span class="mpc-state--off"><?php _e( 'off', 'mpc' ); ?></span></span>
				</label>
				<span class="mpc-option-name"><?php _e( 'Use Magnific Popup instead of old PrettyPhoto', 'mpc' ); ?></span>
			</div>

			<div class="mpc-option">
				<label class="mpc-option-state">
					<input type="checkbox" class="checkbox mpc-scroll_to_id" id="scroll_to_id" name="scroll_to_id" value="1" <?php checked( $options[ 'scroll_to_id' ], '1' ); ?>>
					<span class="mpc-switch"><span class="mpc-state--on"><?php _e( 'on', 'mpc' ); ?></span><span class="mpc-thumb"></span><span class="mpc-state--off"><?php _e( 'off', 'mpc' ); ?></span></span>
				</label>
				<span class="mpc-option-name"><?php _e( 'Scroll to ID', 'mpc' ); ?></span>
			</div>

			<div class="mpc-option">
				<label class="mpc-option-state">
					<input type="checkbox" class="checkbox mpc-use_decoder" id="use_decoder" name="use_decoder" value="1" <?php checked( $options[ 'use_decoder' ], '1' ); ?>>
					<span class="mpc-switch"><span class="mpc-state--on"><?php _e( 'on', 'mpc' ); ?></span><span class="mpc-thumb"></span><span class="mpc-state--off"><?php _e( 'off', 'mpc' ); ?></span></span>
				</label>
				<span class="mpc-option-name"><?php _e( 'Characters Decoding - enable to fix problems with language diacritical marks', 'mpc' ); ?></span>
			</div>

			<div class="mpc-option">
				<label class="mpc-option-state">
					<input type="checkbox" class="checkbox mpc-disable_google_fonts" id="disable_google_fonts" name="disable_google_fonts" value="1" <?php checked( $options[ 'disable_google_fonts' ], '1' ); ?>>
					<span class="mpc-switch"><span class="mpc-state--on"><?php _e( 'on', 'mpc' ); ?></span><span class="mpc-thumb"></span><span class="mpc-state--off"><?php _e( 'off', 'mpc' ); ?></span></span>
				</label>
				<span class="mpc-option-name"><?php _e( 'Remove Google Fonts - enable to cut reference for Google Fonts, you will no longer be able to use them with a <a href="https://hub.mpcthemes.net/knowledgebase/typography-preset/" target="_blank" >Typography Presets</a>', 'mpc' ); ?></span>
			</div>

			<?php
		}

		/* PANEL SECTION - SHORTCODES */
		function panel_section__shortcodes() {
			$options = get_option( 'mpc_ma_options' );

			if ( is_array( $options ) ) {
				$options = array_replace_recursive( $this->defaults, $options );
			} else {
				$options = $this->defaults;
			}

			$values = $options[ 'enabled_shortcodes' ];

			$all_enabled = $values[ 'all' ] == '1';

			$shortcodes_list = array(
				'mpc_accordion'              => __( 'Accordion', 'mpc' ),
				'mpc_alert'                  => __( 'Alert', 'mpc' ),
				'mpc_animated_text'          => __( 'Animated Text', 'mpc' ),
				'mpc_wc_add_to_cart'         => __( 'Add To Cart', 'mpc' ),
				'mpc_button'                 => __( 'Button', 'mpc' ),
				'mpc_button_set'             => __( 'Button Set', 'mpc' ),
				'mpc_callout'                => __( 'Callout', 'mpc' ),
				'mpc_carousel_anything'      => __( 'Carousel Anything', 'mpc' ),
				'mpc_carousel_image'         => __( 'Carousel Image', 'mpc' ),
				'mpc_carousel_posts'         => __( 'Carousel Posts', 'mpc' ),
				'mpc_carousel_slider'        => __( 'Carousel Slider', 'mpc' ),
				'mpc_carousel_testimonial'   => __( 'Carousel Testimonial', 'mpc' ),
				'mpc_wc_carousel_categories' => __( 'WC Carousel Categories', 'mpc' ),
				'mpc_wc_category'            => __( 'WC Category', 'mpc' ),
				'mpc_chart'                  => __( 'Chart', 'mpc' ),
				'mpc_countdown'              => __( 'Countdown', 'mpc' ),
				'mpc_counter'                => __( 'Counter', 'mpc' ),
				'mpc_cubebox'                => __( 'Cubebox', 'mpc' ),
				'mpc_divider'                => __( 'Divider', 'mpc' ),
				'mpc_dropcap'                => __( 'Dropcap', 'mpc' ),
				'mpc_flipbox'                => __( 'Flipbox', 'mpc' ),
				'mpc_grid_anything'          => __( 'Grid Anything', 'mpc' ),
				'mpc_grid_images'            => __( 'Grid Images', 'mpc' ),
				'mpc_grid_posts'             => __( 'Grid Posts', 'mpc' ),
				'mpc_wc_grid_categories'     => __( 'WC Grid Categories', 'mpc' ),
				'mpc_icon'                   => __( 'Icon', 'mpc' ),
				'mpc_icon_list'              => __( 'Icon List', 'mpc' ),
				'mpc_icon_column'            => __( 'Info Box', 'mpc' ),
				'mpc_circle_icons'           => __( 'Info Circle', 'mpc' ),
				'mpc_connected_icons'        => __( 'Info List', 'mpc' ),
				'mpc_ihover'                 => __( 'iHover', 'mpc' ),
				'mpc_image'                  => __( 'Image', 'mpc' ),
				'mpc_interactive_image'      => __( 'Image with Hotspots', 'mpc' ),
				'mpc_lightbox'               => __( 'Lightbox', 'mpc' ),
				'mpc_mailchimp'              => __( 'MailChimp', 'mpc' ),
				'mpc_map'                    => __( 'Map', 'mpc' ),
				'mpc_modal'                  => __( 'Modal', 'mpc' ),
				'mpc_pricing_box'            => __( 'Pricing Box', 'mpc' ),
				'mpc_progress'               => __( 'Progress', 'mpc' ),
				'mpc_qrcode'                 => __( 'QR Code', 'mpc' ),
				'mpc_quote'                  => __( 'Quote', 'mpc' ),
				'mpc_single_post'            => __( 'Single Post', 'mpc' ),
				'mpc_tabs'                   => __( 'Tabs', 'mpc' ),
				'mpc_testimonial'            => __( 'Testimonial', 'mpc' ),
				'mpc_textblock'              => __( 'Text Block', 'mpc' ),
				'mpc_timeline_basic'         => __( 'Timeline Basic', 'mpc' ),
				'mpc_timeline_item'          => __( 'Timeline Item', 'mpc' ),
			);

			$sub_shortcodes_list = array(
				'mpc_ihover_item'          => __( 'iHover Item', 'mpc' ), // Child shortcode
				'mpc_hotspot'              => __( 'Hotspot', 'mpc' ), // Child shortcode
				'mpc_marker'               => __( 'Marker', 'mpc' ), // Child shortcode
				'mpc_pricing_column'       => __( 'Pricing Column', 'mpc' ), // Child shortcode
				'mpc_pricing_legend'       => __( 'Pricing Legend', 'mpc' ), // Child shortcode
				'mpc_navigation'           => __( 'Navigation', 'mpc' ), // Integrated
				'mpc_pagination'           => __( 'Pagination', 'mpc' ), // Integrated
				'mpc_ribbon'               => __( 'Ribbon', 'mpc' ), // Integrated
				'mpc_tooltip'              => __( 'Tooltip', 'mpc' ), // Integrated
			);

			?>

			<div class="mpc-shortcodes">
				<label class="mpc-shortcode-wrap mpc-shortcodes-all button <?php echo $values[ 'all' ] == '1' ? 'mpc-active' : ''; ?>">
					<input type="checkbox" class="checkbox mpc-shortcode mpc-all" id="shortcodes-all" value="1" <?php checked( $values[ 'all' ], '1' ); ?>>
					<input type="hidden" class="checkbox-check mpc-shortcode-value" name="enabled_shortcodes[all]" value="<?php esc_attr_e( $values[ 'all' ] ); ?>">
					<?php _e( 'All', 'mpc' ); ?>
				</label>

				<?php foreach ( $shortcodes_list as $shortcode => $name ) : ?>
					<label class="mpc-shortcode-wrap button <?php echo $values[ $shortcode ] == '1' ? 'mpc-active' : ''; ?> <?php echo $all_enabled ? 'mpc-disabled' : ''; ?>">
						<input type="checkbox" class="checkbox mpc-shortcode" id="<?php esc_attr_e( 'shortcodes-' . $shortcode ); ?>" value="1" <?php checked( $values[ $shortcode ], '1' ); ?> <?php echo $all_enabled ? 'disabled="disabled"' : ''; ?> data-shortcode="<?php esc_attr_e( $shortcode ); ?>">
						<input type="hidden" class="checkbox-check mpc-shortcode-value" name="<?php esc_attr_e( 'enabled_shortcodes[' . $shortcode . ']' ); ?>" value="<?php esc_attr_e( $values[ $shortcode ] ); ?>">
						<?php echo $name; ?>
					</label>
				<?php endforeach; ?>

				<?php foreach ( $sub_shortcodes_list as $shortcode => $name ) : ?>
					<label class="mpc-shortcode-wrap button mpc-hidden <?php echo $values[ $shortcode ] == '1' ? 'mpc-active' : ''; ?> <?php echo $all_enabled ? 'mpc-disabled' : ''; ?>">
						<input type="checkbox" class="checkbox mpc-shortcode" id="<?php esc_attr_e( 'shortcodes-' . $shortcode ); ?>" value="1" <?php checked( $values[ $shortcode ], '1' ); ?> <?php echo $all_enabled ? 'disabled="disabled"' : ''; ?> data-shortcode="<?php esc_attr_e( $shortcode ); ?>">
						<input type="hidden" class="checkbox-check mpc-shortcode-value" name="<?php esc_attr_e( 'enabled_shortcodes[' . $shortcode . ']' ); ?>" value="<?php esc_attr_e( $values[ $shortcode ] ); ?>">
						<?php echo $name; ?>
					</label>
				<?php endforeach; ?>
			</div>

			<div class="mpc-description">
				<?php _e( '<p>After hovering on shortcodes you can see a blue and orange highlights.</p><p>Shortcodes highlighted in <span class="mpc-shortcode-wrap button mpc-dependent--enable mpc-mini">blue</span> are necessary for hovered shortcode to work (they will be turned on with hovered shortcode).</p><p>Shortcodes highlighted in <span class="mpc-shortcode-wrap button mpc-dependent--disable mpc-mini">orange</span> are dependent from hovered shortcode (they will be turned off with the hovered shortcode).</p>', 'mpc' ); ?>
			</div>

			<?php
		}

		/* PANEL SECTION - PURCHASE VERIFICATION */
		function panel_section__purchase_verification() {
			$options = get_option( 'mpc_ma_options' );

			if ( is_array( $options ) ) {
				$options = array_replace_recursive( $this->defaults, $options );
			} else {
				$options = $this->defaults;
			}

			?>

			<div class="mpc-option">
				<span class="mpc-option-name"><?php _e( 'Purchase Code', 'mpc' ); ?></span>
				<label class="mpc-option-state mpc-text-input">
					<input type="text" class="text mpc-purchase_code" id="purchase_code" name="purchase_code" value="<?php esc_attr_e( $options[ 'purchase_code' ] ); ?>">
				</label>
				<div class="mpc-description">
					<p><em>Please notice that purchase code is required to enable automatic updates notification and download.</em></p>
				</div>
			</div>

			<?php
		}

		/* PANEL SECTION - GOOGLE API */
		function panel_section__google_api() {
			$options = get_option( 'mpc_ma_options' );

			if ( is_array( $options ) ) {
				$options = array_replace_recursive( $this->defaults, $options );
			} else {
				$options = $this->defaults;
			}

			?>

			<div class="mpc-option">
				<span class="mpc-option-name"><?php _e( 'Google API', 'mpc' ); ?></span>
				<label class="mpc-option-state mpc-text-input">
					<input type="text" class="text mpc-google_api" id="google_api" name="google_api" value="<?php esc_attr_e( $options[ 'google_api' ] ); ?>">
				</label>
				<div class="mpc-description">
					<p><em>Please notice that Google API is required to enable maps shortcode. Check <a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank" title="Google Maps API Guide">this guide</a> for more details.</em></p>
				</div>
			</div>

			<?php
		}

		/* PANEL SECTION - STYLE PRESETS */
		function panel_section__style_presets() {
			?>

			<div class="mpc-presets">
				<div class="mpc-presets__header">
					<p><?php _e( '<strong>Install all shortcodes presets at once (recommended)</strong> or select a shortcode and install only the presets you like.', 'mpc' ); ?></p>
					<a href="#batch" id="mpc_presets__batch" class="mpc-presets__batch mpc-panel__primary" data-message="<?php _e( 'Any previously installed preset will be overwritten. Are you sure you want to continue?', 'mpc' ); ?>">
						<span class="mpc-default"><?php _e( 'Install all', 'mpc' ); ?></span>
						<span class="mpc-working"><?php _e( 'Installing...', 'mpc' ); ?></span>
						<span class="mpc-finished"><?php _e( 'Installed :)', 'mpc' ); ?></span>
						<span class="mpc-batch__progress mpc-progress"></span>
					</a>
					<span class="mpc-separator">or</span>
					<select id="mpc_presets__select" class="mpc-presets-select">
						<option value=""><?php _e( 'Select Shortcode', 'mpc' ); ?></option>
						<?php foreach ( $this->presets_list as $shortcode => $name ) : ?>
							<option value="<?php esc_attr_e( $shortcode ); ?>"><?php echo $name; ?></option>
						<?php endforeach; ?>
					</select>
					<div id="mpc_presets__ajax" class="mpc-presets__ajax mpc-ajax"><div><span></span><span></span><span></span></div></div>
					<div id="mpc_presets__controls" class="mpc-presets__controls mpc-hidden">
						<a href="#all" id="mpc_presets__all" class="mpc-presets__all button"><?php _e( 'Select all', 'mpc' ); ?></a>
						<a href="#none" id="mpc_presets__none" class="mpc-presets__none button"><?php _e( 'Select none', 'mpc' ); ?></a>
						<a href="#install" id="mpc_presets__install" class="mpc-presets__install mpc-panel__primary mpc-disabled" data-message="<?php _e( 'Re-installing presets will overwrite their settings. Are you sure you want to continue?', 'mpc' ); ?>">
							<span class="mpc-default"><?php _e( 'Install selected', 'mpc' ); ?></span>
							<span class="mpc-working"><?php _e( 'Installing...', 'mpc' ); ?></span>
							<span class="mpc-finished"><?php _e( 'Installed :)', 'mpc' ); ?></span>
							<span class="mpc-install__progress mpc-progress"></span>
						</a>
						<a href="#delete" id="mpc_presets__delete" class="mpc-presets__delete mpc-panel__primary mpc-disabled" data-message="<?php _e( 'Are you sure you want to delete selected presets?', 'mpc' ); ?>">
							<span class="mpc-default"><?php _e( 'Delete selected', 'mpc' ); ?></span>
							<span class="mpc-working"><?php _e( 'Deleting...', 'mpc' ); ?></span>
							<span class="mpc-finished"><?php _e( 'Deleted :)', 'mpc' ); ?></span>
							<span class="mpc-delete__progress mpc-progress"></span>
						</a>
					</div>
				</div>
				<div id="mpc_presets__list" class="mpc-presets__list"></div>
			</div>

			<div class="mpc-description">
				<p><?php _e( 'Presets with <span class="mpc-installed-badge mpc-mini"><i class="dashicons dashicons-yes"></i></span> badge are already installed. Please note that re-installing presets will overwrite their options.', 'mpc' ); ?></p>
				<p><?php _e( '<em>Please be aware that installation make take a while. It automatically downloads images used in presets.</em>', 'mpc' ); ?></p>
			</div>

			<?php
		}

		/* PANEL SECTION - CONTENT PRESETS */
		function panel_section__content_presets() {
			?>

			<div class="mpc-presets">
				<div class="mpc-presets__header">
					<p><?php _e( '<strong>Install all shortcodes content presets at once (recommended)</strong> or select a shortcode and install only the content presets you like.', 'mpc' ); ?></p>
					<a href="#batch" id="mpc_contents__batch" class="mpc-presets__batch mpc-panel__primary" data-message="<?php _e( 'Any previously installed preset will be overwritten. Are you sure you want to continue?', 'mpc' ); ?>">
						<span class="mpc-default"><?php _e( 'Install all', 'mpc' ); ?></span>
						<span class="mpc-working"><?php _e( 'Installing...', 'mpc' ); ?></span>
						<span class="mpc-finished"><?php _e( 'Installed :)', 'mpc' ); ?></span>
						<span class="mpc-batch__progress mpc-progress"></span>
					</a>
					<span class="mpc-separator">or</span>
					<select id="mpc_contents__select" class="mpc-presets-select">
						<option value=""><?php _e( 'Select Shortcode', 'mpc' ); ?></option>
						<?php foreach ( $this->contents_list as $shortcode => $name ) : ?>
							<option value="<?php esc_attr_e( $shortcode ); ?>"><?php echo $name; ?></option>
						<?php endforeach; ?>
					</select>
					<div id="mpc_contents__ajax" class="mpc-presets__ajax mpc-ajax"><div><span></span><span></span><span></span></div></div>
					<div id="mpc_contents__controls" class="mpc-presets__controls mpc-hidden">
						<a href="#all" id="mpc_contents__all" class="mpc-presets__all button"><?php _e( 'Select all', 'mpc' ); ?></a>
						<a href="#none" id="mpc_contents__none" class="mpc-presets__none button"><?php _e( 'Select none', 'mpc' ); ?></a>
						<a href="#install" id="mpc_contents__install" class="mpc-presets__install mpc-panel__primary mpc-disabled" data-message="<?php _e( 'Re-installing content presets will overwrite their settings. Are you sure you want to continue?', 'mpc' ); ?>">
							<span class="mpc-default"><?php _e( 'Install selected', 'mpc' ); ?></span>
							<span class="mpc-working"><?php _e( 'Installing...', 'mpc' ); ?></span>
							<span class="mpc-finished"><?php _e( 'Installed :)', 'mpc' ); ?></span>
							<span class="mpc-install__progress mpc-progress"></span>
						</a>
						<a href="#delete" id="mpc_contents__delete" class="mpc-presets__delete mpc-panel__primary mpc-disabled" data-message="<?php _e( 'Are you sure you want to delete selected content presets?', 'mpc' ); ?>">
							<span class="mpc-default"><?php _e( 'Delete selected', 'mpc' ); ?></span>
							<span class="mpc-working"><?php _e( 'Deleting...', 'mpc' ); ?></span>
							<span class="mpc-finished"><?php _e( 'Deleted :)', 'mpc' ); ?></span>
							<span class="mpc-delete__progress mpc-progress"></span>
						</a>
					</div>
				</div>
				<div id="mpc_contents__list" class="mpc-presets__list"></div>
			</div>

			<div class="mpc-description">
				<p><?php _e( 'Content presets with <span class="mpc-installed-badge mpc-mini"><i class="dashicons dashicons-yes"></i></span> badge are already installed. Please note that re-installing content presets will overwrite their options.', 'mpc' ); ?></p>
				<p><?php _e( '<em>Please be aware that installation make take a while. It automatically downloads images used in content presets.</em>', 'mpc' ); ?></p>
			</div>

			<?php
		}

		/* PANEL SECTION - PRESET PREVIEWS */
		function panel_section__preset_previews() {
			?>

			<div class="mpc-description">
				<p><?php _e( 'Download all preset previews images. We <strong>strongly recommend downloading</strong> preview images to your local server. Thanks to that preset installation and preset preview popup will work <strong>MUCH faster</strong>.', 'mpc' ); ?></p>
				<p><?php _e( '<em>If you don\'t download preview images we will load them from our remote server.</em>', 'mpc' ); ?></p>
			</div>

			<a href="#download" id="mpc_presets__download" class="mpc-presets__download mpc-panel__primary" data-message="<?php _e( 'Are you sure you want to download all presets preview images?', 'mpc' ); ?>">
				<span class="mpc-default"><?php _e( 'Download', 'mpc' ); ?></span>
				<span class="mpc-working"><?php _e( 'Downloading...', 'mpc' ); ?></span>
				<span class="mpc-finished"><?php _e( 'Downloaded :)', 'mpc' ); ?></span>
				<span class="mpc-download__progress mpc-progress"></span>
			</a>

			<div class="mpc-description">
				<p><?php _e( '<em>Download process may take a few minutes. Please leave this page as is until the download is completed :)</em>', 'mpc' ); ?></p>
			</div>

			<?php
		}

		/* AJAX - SAVE PANEL */
		function save_panel() {
			if ( ! isset( $_POST[ 'options' ] )  || ! isset( $_POST[ '_wpnonce' ] ) ) {
				wp_send_json_error();
			}

			check_ajax_referer( 'mpc-ma-panel' );

			$options = array();
			parse_str( $_POST[ 'options' ], $options );

			if ( ! empty( $options ) ) {
				if ( ! isset( $options[ 'easy_mode' ] ) ) {
					$options[ 'easy_mode' ] = '0';
				}
				if ( ! isset( $options[ 'animations_on_mobile' ] ) ) {
					$options[ 'animations_on_mobile' ] = '0';
				}
				if ( ! isset( $options[ 'parallax_on_mobile' ] ) ) {
					$options[ 'parallax_on_mobile' ] = '0';
				}
				if ( ! isset( $options[ 'single_js_css' ] ) ) {
					$options[ 'single_js_css' ] = '0';
				}
				if ( ! isset( $options[ 'vc_row_addons' ] ) ) {
					$options[ 'vc_row_addons' ] = '0';
				}
				if ( ! isset( $options[ 'magnific_popup' ] ) ) {
					$options[ 'magnific_popup' ] = '0';
				}
				if ( ! isset( $options[ 'purchase_code' ] ) ) {
					$options[ 'purchase_code' ] = '';
				}
				if ( ! isset( $options[ 'google_api' ] ) ) {
					$options[ 'google_api' ] = '';
				}
				if ( ! isset( $options[ 'scroll_to_id' ] ) ) {
					$options[ 'scroll_to_id' ] = '0';
				}
				if ( ! isset( $options[ 'use_decoder' ] ) ) {
					$options[ 'use_decoder' ] = '0';
				}
				if ( ! isset( $options[ 'disable_google_fonts' ] ) ) {
					$options[ 'disable_google_fonts' ] = '0';
				}

				$options = array_merge( $this->defaults, $options );

				update_option( 'mpc_ma_options', $options );
			}

			wp_send_json_success();
		}

		/* AJAX - GET PRESETS */
		function get_presets() {
			if ( ! isset( $_POST[ 'shortcode' ] ) || ! isset( $_POST[ '_wpnonce' ] ) ) {
				wp_send_json_error();
			}

			check_ajax_referer( 'mpc-ma-panel' );

			try {
				$presets = file_get_contents( mpc_get_preset_path( $_POST[ 'shortcode' ], 'presets' ) );
//				$presets = file_get_contents( mpc_get_plugin_path( __FILE__, 'dir' ) . '/assets/presets/' . $_POST[ 'shortcode' ] . '.json' );
				$presets = json_decode( $presets, true );

				$installed_presets = get_option( 'mpc_presets_' . $_POST[ 'shortcode' ] );
				if ( $installed_presets === false ) {
					$installed_presets = array();
				} else {
					$installed_presets = json_decode( $installed_presets, true );
				}

				$json = array();
				foreach ( $presets as $name => $values ) {
					$json[ $name ] = array(
						'preset'       => $name,
						'title'        => $values[ '__name' ],
						'url'          => isset( $values[ '__image' ] ) ? $values[ '__image' ] : '',
						'is_installed' => isset( $installed_presets[ $name ] ),
					);
				}

				wp_send_json( $json );
			} catch( Exception $error ) {
				wp_send_json_error();
			}
		}

		/* INSTALL TEMPLATE PRESETS */
		function install_selected_template_presets() {
			$this->install_sub_presets();
		}

		/* AJAX - INSTALL ALL PRESETS */
		function install_all_presets() {
			if ( ! isset( $_POST[ '_wpnonce' ] ) ) {
				wp_send_json_error();
			}

			check_ajax_referer( 'mpc-ma-panel' );

			foreach ( $this->presets_list as $shortcode_name => $display_name ) {
				$this->install_preset( 'all', $shortcode_name );
			}

			wp_send_json_success();
		}

		/* AJAX - INSTALL PRESET */
		function install_selected_presets() {
			if ( ! isset( $_POST[ 'shortcode' ] ) || ! isset( $_POST[ 'presets' ] ) || ! isset( $_POST[ '_wpnonce' ] ) ) {
				wp_send_json_error();
			}

			check_ajax_referer( 'mpc-ma-panel' );

			global $mpc_sub_presets;
			$mpc_sub_presets = array(
				'mpc_pagination' => array(),
				'mpc_navigation' => array(),
				'typography'     => array(),
			);

			$this->install_preset( $_POST[ 'presets' ], $_POST[ 'shortcode' ] );

			$this->install_sub_presets();

			wp_send_json_success();
		}

		function install_preset( $presets, $shortcode ) {
			try {
				$import_presets = file_get_contents( mpc_get_preset_path( $shortcode, 'presets' ) );
//				$import_presets = file_get_contents( mpc_get_plugin_path( __FILE__, 'dir' ) . '/assets/presets/' . $shortcode . '.json' );
				if ( $import_presets == null ) {
					wp_send_json_error();
				} else {
					$import_presets = json_decode( $import_presets, true );
				}

				$this->remove_image_sizes();

				$installed_presets = get_option( 'mpc_presets_' . $shortcode );
				if ( $installed_presets === false ) {
					$installed_presets = array(
						'__index' => 0,
					);
				} else {
					$installed_presets = json_decode( $installed_presets, true );
				}

				if ( $presets != 'all' ) {
					$selected_presets = array_intersect_key( $import_presets, array_flip( $presets ) );
				} else {
					$selected_presets = $import_presets;
				}

				$selected_presets = $this->replace_images( $selected_presets, $shortcode, 'presets' );

				if ( $presets != 'all' ) {
					$selected_presets = $this->find_sub_presets( $selected_presets, $shortcode );
				}

				$installed_presets = array_merge( $installed_presets, $selected_presets );

				uasort( $installed_presets, 'mpc_sort_presets' );

				$installed_presets = mpc_after_sort_presets( $installed_presets );

				$installed_presets = json_encode( $installed_presets );

				if ( $installed_presets !== false ) {
					if ( ! add_option( 'mpc_presets_' . $shortcode, $installed_presets, '', 'no' ) ) {
						update_option( 'mpc_presets_' . $shortcode, $installed_presets );
					}
				} else {
					wp_send_json_error();
				}
			} catch( Exception $error ) {
				wp_send_json_error();
			}
		}

		function install_sub_presets() {
			global $mpc_sub_presets;

			if ( ! empty( $mpc_sub_presets[ 'mpc_navigation' ] ) ) {
				$this->install_preset( array_keys( $mpc_sub_presets[ 'mpc_navigation' ] ), 'mpc_navigation' );
			}

			if ( ! empty( $mpc_sub_presets[ 'mpc_pagination' ] ) ) {
				$this->install_preset( array_keys( $mpc_sub_presets[ 'mpc_pagination' ] ), 'mpc_pagination' );
			}

			if ( ! empty( $mpc_sub_presets[ 'typography' ] ) ) {
				$this->install_preset( array_keys( $mpc_sub_presets[ 'typography' ] ), 'typography' );
			}

			if ( ! empty( $mpc_sub_presets[ 'vc_row' ] ) ) {
				$this->install_preset( array_keys( $mpc_sub_presets[ 'vc_row' ] ), 'vc_row' );
			}

			if ( ! empty( $mpc_sub_presets[ 'vc_column' ] ) ) {
				$this->install_preset( array_keys( $mpc_sub_presets[ 'vc_column' ] ), 'vc_column' );
			}
		}

		function find_sub_presets( $presets, $shortcode ) {
			if ( $shortcode != 'typography' ) {
				global $mpc_sub_presets;

				foreach ( $presets as $preset ) {
					$results = preg_grep( "/(font_preset|icon_preset|mpc_navigation__preset|mpc_pagination__preset)$/", array_keys( $preset ) );

					foreach( $results as $result ) {
						if ( strpos( $result, 'font_preset' ) !== false || strpos( $result, 'icon_preset' ) !== false ) {
							$mpc_sub_presets[ 'typography' ][ $preset[ $result ] ] = true;
						} elseif ( strpos( $result, 'mpc_navigation' ) !== false ) {
							$mpc_sub_presets[ 'mpc_navigation' ][ $preset[ $result ] ] = true;
						} elseif ( strpos( $result, 'mpc_pagination' ) !== false ) {
							$mpc_sub_presets[ 'mpc_pagination' ][ $preset[ $result ] ] = true;
						}
					}
				}
			}

			return $presets;
		}

		function replace_images( $presets, $shortcode, $type = 'presets' ) {
			ini_set( 'max_execution_time', 0 );

			$cached_images = get_option( 'mpc_cached_images' );
			if ( $cached_images === false ) {
				$cached_images = array();
			} else {
				if ( ! is_array( $cached_images ) ) {
					$cached_images = array();
				}
			}

			if ( $type == 'presets' ) {
				$images = array();

				foreach( $presets as $name => $preset ) {
					if ( $shortcode == 'mpc_ihover_item' ) {
						if ( isset( $preset[ 'thumbnail' ] ) ) { $images[ 'thumbnail' ] = $preset[ 'thumbnail' ]; }
					} elseif( $shortcode == 'mpc_image' ) {
						if ( isset( $preset[ 'image' ] ) ) { $images[ 'image' ] = $preset[ 'image' ]; }
					} elseif( $shortcode == 'mpc_lightbox' ) {
						if ( isset( $preset[ 'lightbox_url' ] ) ) { $images[ 'lightbox_url' ] = $preset[ 'lightbox_url' ]; }
					} elseif( $shortcode == 'mpc_interactive_image' ) {
						if ( isset( $preset[ 'background_image' ] ) ) { $images[ 'background_image' ] = $preset[ 'background_image' ]; }
					} elseif( $shortcode == 'mpc_marker' ) {
						if ( isset( $preset[ 'icon' ] ) ) { $images[ 'icon' ] = $preset[ 'icon' ]; }
					} elseif( $shortcode == 'vc_row' ) {
						if ( isset( $preset[ 'parallax_background' ] ) ) { $images[ 'parallax_background' ] = $preset[ 'parallax_background' ]; }
					} elseif( $shortcode == 'mpc_testimonial' ) {
						if ( isset( $preset[ 'thumbnail' ] ) ) { $images[ 'thumbnail' ] = $preset[ 'thumbnail' ]; }
					}

					$results = preg_grep( "/(background_image|icon_image)$/", array_keys( $preset ) );

					foreach( $results as $result ) {
						$images[ $result ] = $preset[ $result ];
					}

					foreach( $images as $key => $path ) {
						if ( isset( $cached_images[ $path ] ) && get_post_status( $cached_images[ $path ] ) !== false ) {
							$presets[ $name ][ $key ] = $cached_images[ $path ];
						} else {
							$image_id = $this->import_image( $path );

							if ( $image_id != '' ) {
								$cached_images[ $path ]   = $image_id;
								$presets[ $name ][ $key ] = $image_id;
							}
						}
					}
				}
			} elseif ( $type == 'contents' ) {
				foreach( $presets as $name => $preset ) {
					$content = $preset[ 'content' ];
					$pattern = '/(background_image="|icon_image="|thumbnail="|image="|icon="|lightbox_url="|parallax_background=")(https?:\/\/[^"]*)"/';

					$self = $this;

					$content = preg_replace_callback( $pattern, function( $matches ) use ( &$cached_images, &$self ) {
						if ( isset( $cached_images[ $matches[ 2 ] ] ) && get_post_status( $cached_images[ $matches[ 2 ] ] ) !== false ) {
							return $matches[ 1 ] . $cached_images[ $matches[ 2 ] ] . '"';
						} else {
							$image_id = $self->import_image( $matches[ 2 ] );

							if ( $image_id != '' ) {
								$cached_images[ $matches[ 2 ] ] = $image_id;

								return $matches[ 1 ] . $image_id . '"';
							}
						}
					}, $content );

					$presets[ $name ][ 'content' ] = $content;
				}
			}

			update_option( 'mpc_cached_images', $cached_images, false );

			return $presets;
		}

		static function import_image( $image_path ) {
			if ( ! ini_get( 'allow_url_fopen' ) ) {
				return '';
			}

			$image_path = mpc_get_url( $image_path );
			if( $image_path == '' ) {
				return '';
			}

			require_once( ABSPATH . 'wp-admin/includes/image.php' );

			$uploaded_file  = wp_upload_bits( basename( $image_path ), null, file_get_contents( $image_path ) );
			$wp_upload_dir  = wp_upload_dir();
			$file_path      = $wp_upload_dir[ 'basedir' ] . str_replace( $wp_upload_dir[ 'baseurl' ], '', $uploaded_file[ 'url' ] );
			$parent_post_id = 0;
			$filetype       = wp_check_filetype( basename( $file_path ), null );
			$file_data      = array(
				'guid'           => $wp_upload_dir[ 'url' ] . '/' . basename( $file_path ),
				'post_mime_type' => $filetype[ 'type' ],
				'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $file_path ) ),
				'post_content'   => '',
				'post_status'    => 'inherit'
			);

			$file_id       = wp_insert_attachment( $file_data, $file_path, $parent_post_id );
			$file_metadata = wp_generate_attachment_metadata( $file_id, $file_path );
			wp_update_attachment_metadata( $file_id, $file_metadata );

			return (string)$file_id;
		}

		function remove_image_sizes() {
			foreach ( get_intermediate_image_sizes() as $size ) {
				if ( !in_array( $size, array( 'thumbnail', 'medium', 'medium_large', 'large' ) ) ) {
					remove_image_size( $size );
				}
			}
		}

		/* AJAX - DELETE PRESET */
		function delete_selected_presets() {
			if ( ! isset( $_POST[ 'shortcode' ] ) || ! isset( $_POST[ 'presets' ] ) || ! isset( $_POST[ '_wpnonce' ] ) ) {
				wp_send_json_error();
			}

			check_ajax_referer( 'mpc-ma-panel' );

			$this->delete_preset( $_POST[ 'presets' ], $_POST[ 'shortcode' ] );

			wp_send_json_success();
		}

		function delete_preset( $presets, $shortcode ) {
			try {
				$installed_presets = get_option( 'mpc_presets_' . $shortcode );
				if ( $installed_presets === false ) {
					wp_send_json_error();
				} else {
					$installed_presets = json_decode( $installed_presets, true );
				}

				$installed_presets = array_diff_key( $installed_presets, array_flip( $presets ) );

//				uasort( $installed_presets, 'mpc_sort_presets' );

//				$installed_presets = mpc_after_sort_presets( $installed_presets );

				$installed_presets = json_encode( $installed_presets );

				if ( $installed_presets !== false ) {
					if ( ! add_option( 'mpc_presets_' . $shortcode, $installed_presets, '', 'no' ) ) {
						update_option( 'mpc_presets_' . $shortcode, $installed_presets );
					}

					wp_send_json_success();
				} else {
					wp_send_json_error();
				}
			} catch( Exception $error ) {
				wp_send_json_error();
			}
		}

		/* AJAX - GET CONTENTS */
		function get_contents() {
			if ( ! isset( $_POST[ 'shortcode' ] ) || ! isset( $_POST[ '_wpnonce' ] ) ) {
				wp_send_json_error();
			}

			check_ajax_referer( 'mpc-ma-panel' );

			try {
				$presets = file_get_contents( mpc_get_preset_path( $_POST[ 'shortcode' ], 'contents' ) );
//				$presets = file_get_contents( mpc_get_plugin_path( __FILE__, 'dir' ) . '/assets/contents/' . $_POST[ 'shortcode' ] . '.json' );
				$presets = json_decode( $presets, true );

				$preset_post_id = mpc_get_content_preset_post_id( $_POST[ 'shortcode' ], false );

				if ( ! $preset_post_id ) {
					$installed_presets_meta = array();
				} else {
					$installed_presets_meta = get_post_meta( $preset_post_id );
				}

				$json = array();
				foreach ( $presets as $name => $values ) {
					$json[ $name ] = array(
						'preset'       => $name,
						'title'        => $values[ 'name' ],
						'url'          => isset( $values[ 'image' ] ) ? $values[ 'image' ] : '',
						'is_installed' => isset( $installed_presets_meta[ $name ] ),
					);
				}

				wp_send_json( $json );
			} catch( Exception $error ) {
				wp_send_json_error();
			}
		}

		/* AJAX - INSTALL ALL CONTENT */
		function install_all_contents() {
			if ( ! isset( $_POST[ '_wpnonce' ] ) ) {
				wp_send_json_error();
			}

			check_ajax_referer( 'mpc-ma-panel' );

			global $mpc_sub_presets;
			$mpc_sub_presets = array(
				'mpc_pagination' => array(),
				'mpc_navigation' => array(),
				'typography'     => array(),
				'vc_row'         => array(),
				'vc_column'      => array(),
			);

			foreach ( $this->contents_list as $shortcode_name => $display_name ) {
				$this->install_content( 'all', $shortcode_name );
			}

			$this->install_sub_presets();

			wp_send_json_success();
		}

		/* AJAX - INSTALL CONTENT */
		function install_selected_contents() {
			if ( ! isset( $_POST[ 'shortcode' ] ) || ! isset( $_POST[ 'contents' ] ) || ! isset( $_POST[ '_wpnonce' ] ) ) {
				wp_send_json_error();
			}

			check_ajax_referer( 'mpc-ma-panel' );

			global $mpc_sub_presets;
			$mpc_sub_presets = array(
				'mpc_pagination' => array(),
				'mpc_navigation' => array(),
				'typography'     => array(),
				'vc_row'         => array(),
				'vc_column'      => array(),
			);

			$this->install_content( $_POST[ 'contents' ], $_POST[ 'shortcode' ] );

			$this->install_sub_presets();

			wp_send_json_success();
		}

		function install_content( $contents, $shortcode ) {
			try {
				$import_contents = file_get_contents( mpc_get_preset_path( $shortcode, 'contents' ) );
//				$import_contents = file_get_contents( mpc_get_plugin_path( __FILE__, 'dir' ) . '/assets/contents/' . $shortcode . '.json' );
				if ( $import_contents == null ) {
					wp_send_json_error();
				} else {
					$import_contents = json_decode( $import_contents, true );
				}

				$this->remove_image_sizes();

				$preset_post_id = mpc_get_content_preset_post_id( $shortcode, false );

				if ( ! $preset_post_id ) {
					$preset_post_id = wp_insert_post( array(
						'post_title'     => $shortcode,
						'post_content'   => '',
						'post_status'    => 'publish',
						'post_type'      => 'mpc_content_preset',
						'post_mime_type' => 'mpc-content-preset/' . str_replace( '_', '-', $shortcode ),
						'meta_input'     => array(
							'_mpc_presets_index' => 0,
						),
					) );

					delete_post_meta( $preset_post_id, '_vc_post_settings' );
				}

				if ( ! $preset_post_id ) {
					wp_send_json_error();
				}

				if ( $contents != 'all' ) {
					$selected_contents = array_intersect_key( $import_contents, array_flip( $contents ) );
				} else {
					$selected_contents = $import_contents;
				}

				$selected_contents = $this->replace_images( $selected_contents, $shortcode, 'contents' );

				$this->find_inner_presets( $selected_contents );

				foreach ( $selected_contents as $name => $content ) {
					$content = json_encode( $content );

					update_post_meta( $preset_post_id, $name, wp_slash( $content ) );
				}

				mpc_sort_content_presets( $shortcode );
			} catch( Exception $error ) {
				wp_send_json_error();
			}
		}

		function find_inner_presets( $presets ) {
			global $mpc_sub_presets;

			foreach( $presets as $name => $preset ) {
				$content = $preset[ 'content' ];
				$pattern = '/(font_preset="|mpc_navigation__preset="|mpc_pagination__preset=")([^"]*)"/';

				$matches = array();
				preg_match_all( $pattern, $content, $matches );

				if ( ! empty( $matches[ 2 ] ) ) {
					foreach( $matches[ 2 ] as $index => $preset_name ) {
						if ( strpos( $matches[ 1 ][ $index ], 'font_preset' ) !== false ) {
							$mpc_sub_presets[ 'typography' ][ $preset_name ] = true;
						}

						if ( strpos( $matches[ 1 ][ $index ], 'mpc_navigation' ) !== false ) {
							$mpc_sub_presets[ 'mpc_navigation' ][ $preset_name ] = true;
						}

						if ( strpos( $matches[ 1 ][ $index ], 'mpc_pagination' ) !== false ) {
							$mpc_sub_presets[ 'mpc_pagination' ][ $preset_name ] = true;
						}
					}
				}

				if( isset( $_POST[ 'shortcode' ] ) ) {
					if ( $_POST[ 'shortcode' ] == 'vc_row' ) {
						$mpc_sub_presets[ 'vc_row' ][ substr( $name, 1 ) ] = true;
					} else if ( $_POST[ 'shortcode' ] == 'vc_column' ) {
						$mpc_sub_presets[ 'vc_column' ][ substr( $name, 1 ) ] = true;
					}
				}
			}
		}

		/* AJAX - DELETE CONTENT */
		function delete_selected_contents() {
			if ( ! isset( $_POST[ 'shortcode' ] ) || ! isset( $_POST[ 'contents' ] ) || ! isset( $_POST[ '_wpnonce' ] ) ) {
				wp_send_json_error();
			}

			check_ajax_referer( 'mpc-ma-panel' );

			$this->delete_content( $_POST[ 'contents' ], $_POST[ 'shortcode' ] );

			wp_send_json_success();
		}

		function delete_content( $contents, $shortcode ) {
			try {
				$preset_post_id = mpc_get_content_preset_post_id( $shortcode, false );

				if ( $preset_post_id != 0 ) {
					foreach ( $contents as $index => $name ) {
						delete_post_meta( $preset_post_id, $name );
					}

					mpc_sort_content_presets( $shortcode );
				}
			} catch( Exception $error ) {
				wp_send_json_error();
			}
		}

		/* DOWNLOAD IMAGES */
		function download_images() {
			if ( ! isset( $_POST[ '_wpnonce' ] ) ) {
				wp_send_json_error();
			}

			check_ajax_referer( 'mpc-ma-panel' );

			ini_set( 'max_execution_time', 0 );

			$wp_upload_dir = wp_upload_dir();
			$presets_path  = $wp_upload_dir[ 'basedir' ];
			$presets_file  = $presets_path . '/mpc_presets.zip';
			$presets_url   = 'https://products.mpcthemes.net/ma/mpc_presets.zip';

			if ( file_exists( $presets_file ) ) {
				unlink( $presets_file );
			}

			$downloaded_file = download_url( $presets_url, HOUR_IN_SECONDS );
			copy( $downloaded_file, $presets_file );
			unlink( $downloaded_file );

			WP_Filesystem();
			$unzipped_file = unzip_file( $presets_file, $presets_path );

			unlink( $presets_file );

			if ( ! $unzipped_file ) {
				wp_send_json_error();
			}

			update_option( 'mpc_previews_source', $wp_upload_dir[ 'baseurl' ] . '/' );

			wp_send_json_success();
		}

		/* DEFAULTS - SETUP */
		function setup_defaults() {
			$options = get_option( 'mpc_ma_options' );

			if ( is_array( $options ) ) {
				$options = array_replace_recursive( $this->defaults, $options );
			} else {
				$options = $this->defaults;
			}

			update_option( 'mpc_ma_options', $options );

			$shortcodes = $this->defaults[ 'enabled_shortcodes' ];
			$shortcodes[ 'typography' ] = '1';
			unset( $shortcodes[ 'all' ] );

			$defaults_loaded = true;
			try {
				$import_presets = file_get_contents( mpc_get_plugin_path( __FILE__, 'dir' ) . '/assets/presets/defaults.json' );
				if ( $import_presets == null ) {
					$defaults_loaded = false;
				} else {
					$import_presets = json_decode( $import_presets, true );
				}
			} catch( Exception $error ) {
				$defaults_loaded = false;
			}

			foreach ( $shortcodes as $shortcode => $state ) {
				$current = get_option( 'mpc_presets_' . $shortcode );
				if ( $current === false ) {
					$current = array();
				} else {
					$current = json_decode( $current, true );
				}

				$defaults = array(
					'__index' => 0,
					'default' => array(
						'__name' => __( 'Default', 'mpc' ),
					),
				);

				if ( $defaults_loaded && ! empty( $import_presets[ $shortcode ] ) ) {
					$defaults[ 'default' ] = $import_presets[ $shortcode ];

					$defaults[ 'default' ][ '__name' ] = __( 'Default', 'mpc' );
				}

				if ( is_array( $current ) ) {
					if ( ! isset( $current[ '__index' ] ) ) {
						$current[ '__index' ] = 0;
					}
					if ( ! isset( $current[ 'default' ] ) ) {
						$current[ 'default' ] = $defaults[ 'default' ];
					}
				} else {
					$current = $defaults;
				}

				foreach ( $current as $preset => $values ) {
					if ( isset( $current[ $preset ][ '__image' ] ) ) {
						$current[ $preset ][ '__image' ] = str_replace( 'https://products.mpcthemes.net/ma/presets/', '', $current[ $preset ][ '__image' ] );
					}
				}

				$current = json_encode( $current );

				update_option( 'mpc_presets_' . $shortcode, $current, 'no' );
			}
		}
	}
}

if ( class_exists( 'MPC_Panel' ) ) {
	global $MPC_Panel;
	$MPC_Panel = new MPC_Panel;
}

/*----------------------------------------------------------------------------*\
	TOOLBOX
\*----------------------------------------------------------------------------*/

/* Decode URL string */
function mpc_get_url( $url ) {
	$pattern     = '/https?:\/\/[^"\/]*\/{1}/';
	$preview_url = '';

	if ( defined( 'MPC_MASSIVE_PREVIEW_URL' ) ) {
		$preview_url = MPC_MASSIVE_PREVIEW_URL;
	}

	if ( filter_var( $url, FILTER_VALIDATE_URL ) !== false ) {
		$url = preg_replace( $pattern, $preview_url, $url );

		return $url;
	}

	$url = explode( '|', $url );
	if ( count( $url ) != 3 ) {
		return '';
	} else {
		$url = explode( ':', $url[ 0 ], 2 );
		$url = isset( $url[ 1 ] ) ? urldecode( $url[ 1 ] ) : '';
	}

	if ( filter_var( $url, FILTER_VALIDATE_URL ) !== false ) {
		$url = preg_replace( $pattern, $preview_url, $url );

		return $url;
	}

	return '';
}