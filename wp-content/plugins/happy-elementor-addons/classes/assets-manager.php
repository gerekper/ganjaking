<?php

namespace Happy_Addons\Elementor;

use Elementor\Core\Files\CSS\Post as Post_CSS;
use Elementor\Core\Settings\Manager as SettingsManager;

defined('ABSPATH') || die();

class Assets_Manager {

	/**
	 * Bind hook and run internal methods here
	 */
	public static function init() {
		// Frontend scripts
		add_action('wp_enqueue_scripts', [__CLASS__, 'frontend_register']);
		add_action('wp_enqueue_scripts', [__CLASS__, 'frontend_enqueue'], 100);
		add_action('elementor/css-file/post/enqueue', [__CLASS__, 'frontend_enqueue_exceptions']);

		// Edit and preview enqueue
		add_action('elementor/preview/enqueue_styles', [__CLASS__, 'enqueue_preview_styles']);

		// Enqueue editor scripts
		add_action('elementor/editor/after_enqueue_scripts', [__CLASS__, 'editor_enqueue']);

		// Paragraph toolbar registration
		add_filter('elementor/editor/localize_settings', [__CLASS__, 'add_inline_editing_intermediate_toolbar']);

		/**
		 * @see self::fix_pro_assets_loading
		 */
		self::fix_pro_assets_loading();
	}

	/**
	 * Register inline editing paragraph toolbar
	 *
	 * @param array $config
	 * @return array
	 */
	public static function add_inline_editing_intermediate_toolbar($config) {
		if (!isset($config['inlineEditing'])) {
			return $config;
		}

		$tools = [
			'bold',
			'underline',
			'italic',
			'createlink',
		];

		if (isset($config['inlineEditing']['toolbar'])) {
			$config['inlineEditing']['toolbar']['intermediate'] = $tools;
		} else {
			$config['inlineEditing'] = [
				'toolbar' => [
					'intermediate' => $tools,
				],
			];
		}

		return $config;
	}

	/**
	 * Register frontend assets.
	 *
	 * Frontend assets handler will be used in widgets map
	 * to load widgets assets on demand.
	 *
	 * @return void
	 */
	public static function frontend_register() {
		$suffix = ha_is_script_debug_enabled() ? '.' : '.min.';

		wp_register_style(
			'happy-icons',
			HAPPY_ADDONS_ASSETS . 'fonts/style.min.css',
			null,
			HAPPY_ADDONS_VERSION
		);

		// Image comparasion
		wp_register_style(
			'twentytwenty',
			HAPPY_ADDONS_ASSETS . 'vendor/twentytwenty/css/twentytwenty.css',
			null,
			HAPPY_ADDONS_VERSION
		);

		wp_register_script(
			'jquery-event-move',
			HAPPY_ADDONS_ASSETS . 'vendor/twentytwenty/js/jquery.event.move.js',
			['jquery'],
			HAPPY_ADDONS_VERSION,
			true
		);

		wp_register_script(
			'jquery-twentytwenty',
			HAPPY_ADDONS_ASSETS . 'vendor/twentytwenty/js/jquery.twentytwenty.js',
			['jquery-event-move'],
			HAPPY_ADDONS_VERSION,
			true
		);

		// Justified Grid
		wp_register_style(
			'justifiedGallery',
			HAPPY_ADDONS_ASSETS . 'vendor/justifiedGallery/css/justifiedGallery.min.css',
			null,
			HAPPY_ADDONS_VERSION
		);

		wp_register_script(
			'jquery-justifiedGallery',
			HAPPY_ADDONS_ASSETS . 'vendor/justifiedGallery/js/jquery.justifiedGallery.min.js',
			['jquery'],
			HAPPY_ADDONS_VERSION,
			true
		);

		// Carousel and Slider
		wp_register_style(
			'slick',
			HAPPY_ADDONS_ASSETS . 'vendor/slick/slick.css',
			null,
			HAPPY_ADDONS_VERSION
		);

		wp_register_style(
			'slick-theme',
			HAPPY_ADDONS_ASSETS . 'vendor/slick/slick-theme.css',
			null,
			HAPPY_ADDONS_VERSION
		);

		wp_register_script(
			'jquery-slick',
			HAPPY_ADDONS_ASSETS . 'vendor/slick/slick.min.js',
			['jquery'],
			HAPPY_ADDONS_VERSION,
			true
		);

		// Masonry grid
		wp_register_script(
			'jquery-isotope',
			HAPPY_ADDONS_ASSETS . 'vendor/jquery.isotope.js',
			['jquery'],
			HAPPY_ADDONS_VERSION,
			true
		);

		// Number animation
		wp_register_script(
			'jquery-numerator',
			HAPPY_ADDONS_ASSETS . 'vendor/jquery-numerator/jquery-numerator.min.js',
			['jquery'],
			HAPPY_ADDONS_VERSION,
			true
		);

		// Magnific popup
		wp_register_style(
			'magnific-popup',
			HAPPY_ADDONS_ASSETS . 'vendor/magnific-popup/magnific-popup.css',
			null,
			HAPPY_ADDONS_VERSION
		);

		wp_register_script(
			'jquery-magnific-popup',
			HAPPY_ADDONS_ASSETS . 'vendor/magnific-popup/jquery.magnific-popup.min.js',
			null,
			HAPPY_ADDONS_VERSION,
			true
		);

		// keyframes
		wp_register_script(
			'jquery-keyframes',
			HAPPY_ADDONS_ASSETS . 'vendor/keyframes/jquery.keyframes.min.js',
			['jquery'],
			HAPPY_ADDONS_VERSION,
			true
		);

		// Chart.js
		wp_register_script(
			'chart-js',
			HAPPY_ADDONS_ASSETS . 'vendor/chart/chart.min.js',
			['jquery'],
			HAPPY_ADDONS_VERSION,
			true
		);

		// Threesixty Rotation js
		wp_register_script(
			'circlr',
			HAPPY_ADDONS_ASSETS . 'vendor/threesixty-rotation/circlr.min.js',
			['jquery'],
			HAPPY_ADDONS_VERSION,
			true
		);

		// happy magnify js
		wp_register_script(
			'ha-simple-magnify',
			HAPPY_ADDONS_ASSETS . 'vendor/threesixty-rotation/happy-simple-magnify.js',
			['jquery'],
			HAPPY_ADDONS_VERSION,
			true
		);

		// fullcalendar js
		wp_register_script(
			'ha-fullcalendar',
			HAPPY_ADDONS_ASSETS . 'vendor/fullcalendar/fullcalendar.min.js',
			['jquery'],
			HAPPY_ADDONS_VERSION,
			true
		);

		// fullcalendar language js
		wp_register_script(
			'ha-fullcalendar-locales',
			HAPPY_ADDONS_ASSETS . 'vendor/fullcalendar/locales-all.min.js',
			['jquery'],
			HAPPY_ADDONS_VERSION,
			true
		);

		// fullcalendar css
		wp_register_style(
			'ha-fullcalendar',
			HAPPY_ADDONS_ASSETS . 'vendor/fullcalendar/fullcalendar.min.css',
			null,
			HAPPY_ADDONS_VERSION
		);

		// Hover css
		wp_register_style(
			'hover-css',
			HAPPY_ADDONS_ASSETS . 'vendor/hover-css/hover-css.css',
			null,
			HAPPY_ADDONS_VERSION
		);

		// Sharer JS
		wp_register_script(
			'sharer-js',
			HAPPY_ADDONS_ASSETS . 'vendor/sharer-js/sharer.min.js',
			['jquery'],
			HAPPY_ADDONS_VERSION,
			true
		);


		// Happy addons PDF JS
		wp_register_script(
			'pdf-js',
			'//cdnjs.cloudflare.com/ajax/libs/pdfobject/2.2.7/pdfobject.min.js',
			[],
			HAPPY_ADDONS_VERSION,
			false
		);
		// Happy addons LordIcon JS
		wp_register_script(
			'lord-icon',
			HAPPY_ADDONS_ASSETS . 'vendor/lord-icon/lord-icon-2.1.0.js',
			[],
			HAPPY_ADDONS_VERSION,
			false
		);
		// Main assets
		wp_register_style(
			'happy-elementor-addons',
			HAPPY_ADDONS_ASSETS . 'css/main' . $suffix . 'css',
			['elementor-frontend'],
			HAPPY_ADDONS_VERSION
		);

		// Happy addons script
		wp_register_script(
			'happy-elementor-addons',
			HAPPY_ADDONS_ASSETS . 'js/happy-addons' . $suffix . 'js',
			['jquery'],
			HAPPY_ADDONS_VERSION,
			true
		);

		//Localize scripts
		wp_localize_script(
			'happy-elementor-addons',
			'HappyLocalize',
			[
				'ajax_url' => admin_url('admin-ajax.php'),
				'nonce'    => wp_create_nonce('happy_addons_nonce'),
				'pdf_js_lib' => HAPPY_ADDONS_ASSETS . 'vendor/pdfjs/lib'
			]
		);
	}

	/**
	 * Handle exception cases where regular enqueue won't work
	 *
	 * @param Post_CSS $file
	 *
	 * @return void
	 */
	public static function frontend_enqueue_exceptions(Post_CSS $file) {
		$post_id = $file->get_post_id();

		if (get_queried_object_id() === $post_id) {
			return;
		}

		$template_type = get_post_meta($post_id, '_elementor_template_type', true);

		if ($template_type === 'kit') {
			return;
		}

		self::enqueue($post_id);
	}

	/**
	 * Enqueue fontend assets
	 *
	 * @return void
	 */
	public static function frontend_enqueue() {
		if (!is_singular()) {
			return;
		}

		self::enqueue(get_the_ID());
	}

	/**
	 * Just enqueue the assets
	 *
	 * It just processes the assets from cache if avilable
	 * otherwise raw assets
	 *
	 * @param int $post_id
	 *
	 * @return void
	 */
	public static function enqueue($post_id) {
		if (Cache_Manager::should_enqueue($post_id)) {
			Cache_Manager::enqueue($post_id);
		}

		if (Cache_Manager::should_enqueue_raw($post_id)) {
			Cache_Manager::enqueue_raw($post_id);
		}
	}

	public static function get_dark_stylesheet_url() {
		return HAPPY_ADDONS_ASSETS . 'admin/css/editor-dark.min.css';
	}

	public static function enqueue_dark_stylesheet() {
		$theme = SettingsManager::get_settings_managers('editorPreferences')->get_model()->get_settings('ui_theme');

		if ('light' !== $theme) {
			$media_queries = 'all';

			if ('auto' === $theme) {
				$media_queries = '(prefers-color-scheme: dark)';
			}

			wp_enqueue_style(
				'happy-addons-editor-dark',
				self::get_dark_stylesheet_url(),
				[
					'elementor-editor',
				],
				HAPPY_ADDONS_VERSION,
				$media_queries
			);
		}
	}

	/**
	 * Enqueue editor assets
	 *
	 * @return void
	 */
	public static function editor_enqueue() {

		wp_enqueue_style(
			'happy-icons',
			HAPPY_ADDONS_ASSETS . 'fonts/style.min.css',
			null,
			HAPPY_ADDONS_VERSION
		);

		wp_enqueue_style(
			'happy-elementor-addons-editor',
			HAPPY_ADDONS_ASSETS . 'admin/css/editor.min.css',
			null,
			HAPPY_ADDONS_VERSION
		);

		wp_enqueue_script(
			'happy-elementor-addons-editor',
			HAPPY_ADDONS_ASSETS . 'admin/js/editor.min.js',
			['elementor-editor', 'jquery'],
			HAPPY_ADDONS_VERSION,
			true
		);

		Library_Manager::enqueue_assets();

		/**
		 * Make sure to enqueue this at the end
		 * otherwise it may not work properly
		 */
		self::enqueue_dark_stylesheet();

		$localize_data = [
			'placeholder_widgets' => [],
			'hasPro'                  => ha_has_pro(),
			'editor_nonce'            => wp_create_nonce('ha_editor_nonce'),
			'dark_stylesheet_url'     => self::get_dark_stylesheet_url(),
			'i18n' => [
				'promotionDialogHeader'     => esc_html__('%s Widget', 'happy-elementor-addons'),
				'promotionDialogMessage'    => esc_html__('Use %s widget with other exclusive pro widgets and 100% unique features to extend your toolbox and build sites faster and better.', 'happy-elementor-addons'),
				'templatesEmptyTitle'       => esc_html__('No Templates Found', 'happy-elementor-addons'),
				'templatesEmptyMessage'     => esc_html__('Try different category or sync for new templates.', 'happy-elementor-addons'),
				'templatesNoResultsTitle'   => esc_html__('No Results Found', 'happy-elementor-addons'),
				'templatesNoResultsMessage' => esc_html__('Please make sure your search is spelled correctly or try a different words.', 'happy-elementor-addons'),
			],
		];

		if (!ha_has_pro() && ha_is_elementor_version('>=', '2.9.0')) {
			$localize_data['placeholder_widgets'] = Widgets_Manager::get_pro_widget_map();
		}

		wp_localize_script(
			'happy-elementor-addons-editor',
			'HappyAddonsEditor',
			$localize_data
		);
	}

	/**
	 * Enqueue stylesheets only for preview window
	 * editing mode basically.
	 *
	 * @return void
	 */
	public static function enqueue_preview_styles() {
		if (ha_is_weforms_activated()) {
			wp_enqueue_style(
				'happy-addons-weform',
				plugins_url('/weforms/assets/wpuf/css/frontend-forms.css', 'weforms'),
				null,
				HAPPY_ADDONS_VERSION
			);
		}

		if (ha_is_wpforms_activated() && defined('WPFORMS_PLUGIN_SLUG')) {
			wp_enqueue_style(
				'happy-addons-wpform',
				plugins_url('/' . WPFORMS_PLUGIN_SLUG . '/assets/css/wpforms-full.css', WPFORMS_PLUGIN_SLUG),
				null,
				HAPPY_ADDONS_VERSION
			);
		}

		if (ha_is_calderaforms_activated()) {
			wp_enqueue_style(
				'happy-addons-caldera-forms',
				plugins_url('/caldera-forms/assets/css/caldera-forms-front.css', 'caldera-forms'),
				null,
				HAPPY_ADDONS_VERSION
			);
		}

		if (ha_is_gravityforms_activated()) {
			wp_enqueue_style(
				'happy-addons-gravity-forms',
				plugins_url('/gravityforms/css/formsmain.min.css', 'gravityforms'),
				null,
				HAPPY_ADDONS_VERSION
			);
		}

		$data = '
		.elementor-add-section[data-view=choose-action] .elementor-add-new-section {
			display: inline-flex !important;
			flex-wrap: wrap;
			align-items: center;
			justify-content: center;
		}
		.elementor-add-section-drag-title{
			flex-basis: 100%;
		}
		.elementor-add-new-section .elementor-add-ha-button {
			background-color: #5636d1;
			margin-left: 5px;
			font-size: 20px;
			color: #fff;
			display: flex;
			align-items: center;
			justify-content: center;
		}
		';
		wp_add_inline_style('happy-elementor-addons', $data);

		if (ha_is_fluent_form_activated()) {
			wp_enqueue_style(
				'happy-addons-fluent-forms',
				plugins_url('/fluentform/public/css/fluent-forms-public.css', 'fluentform'),
				null,
				HAPPY_ADDONS_VERSION
			);
		}
	}

	/**
	 * Fix HappyAddons Pro assets loading.
	 *
	 * Assets loading issue casued by free 2.13.2 release
	 * due to a change in hook priority.
	 *
	 * @todo remove in future
	 *
	 * @return void
	 */
	public static function fix_pro_assets_loading() {
		if (ha_has_pro() && version_compare(HAPPY_ADDONS_PRO_VERSION, '1.9.0', '<=')) {
			$callback = ['\Happy_Addons_Pro\Assets_Manager', 'frontend_register'];
			remove_action('wp_enqueue_scripts', $callback);
			add_action('wp_enqueue_scripts', $callback, 0);
		}
	}
}

Assets_Manager::init();
