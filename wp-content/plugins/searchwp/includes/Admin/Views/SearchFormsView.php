<?php
/**
 * SearchWP SearchFormsView.
 *
 * @since 4.3.2
 */

namespace SearchWP\Admin\Views;

use SearchWP\Engine;
use SearchWP\Forms\Storage;
use SearchWP\Settings;
use SearchWP\Statistics;
use SearchWP\Utils;
use SearchWP\Admin\NavTab;

/**
 * Class SearchFormsView is responsible for providing the UI for Search Forms.
 *
 * @since 4.3.2
 */
class SearchFormsView {

	private static $slug = 'forms';

	/**
	 * SearchFormsView constructor.
	 *
	 * @since 4.3.2
	 */
	function __construct() {

		if ( Utils::is_swp_admin_page( 'forms' ) ) {
			new NavTab( [
				'page'       => 'forms',
				'tab'        => self::$slug,
				'label'      => __( 'Search Forms', 'searchwp' ),
				'is_default' => true,
			] );
		}

		if ( Utils::is_swp_admin_page( 'forms', 'default' ) ) {
			add_action( 'searchwp\settings\view',  [ __CLASS__, 'render' ] );
			add_action( 'admin_enqueue_scripts', [ __CLASS__, 'assets' ] );
			add_action( 'admin_init', [ __CLASS__, 'process_list_actions' ] );
		}

		add_filter( 'default_title', [ __CLASS__, 'embed_page_title' ], 10, 2 );
		add_filter( 'default_content', [ __CLASS__, 'embed_page_content' ], 10, 2 );

		add_action( 'wp_ajax_' . SEARCHWP_PREFIX . 'save_form_settings',  [ __CLASS__, 'save_form_settings_ajax' ] );

		add_action( 'wp_ajax_' . SEARCHWP_PREFIX . 'admin_form_embed_wizard_embed_page_url',  [ __CLASS__, 'get_embed_page_url_ajax' ] );
		add_action( 'wp_ajax_' . SEARCHWP_PREFIX . 'admin_form_embed_wizard_search_pages_choicesjs',  [ __CLASS__, 'get_search_result_pages_ajax' ] );
	}

	/**
	 * Outputs the assets needed for the Settings UI.
	 *
	 * @since 4.3.2
	 */
	public static function assets() {

		if ( ! current_user_can( Settings::get_capability() ) ) {
			return;
		}

		$handle = SEARCHWP_PREFIX . self::$slug;

		wp_enqueue_script( 'iris' );

		wp_enqueue_script(
			SEARCHWP_PREFIX . 'choicesjs',
			SEARCHWP_PLUGIN_URL . 'assets/vendor/choicesjs/js/choices-10.2.0.min.js',
			[],
			'10.2.0',
			true
		);

		wp_enqueue_style(
			SEARCHWP_PREFIX . 'choicesjs',
			SEARCHWP_PLUGIN_URL . 'assets/vendor/choicesjs/css/choices-10.2.0.min.css',
			[],
			'10.2.0'
		);

		wp_enqueue_style(
			$handle,
			SEARCHWP_PLUGIN_URL . 'assets/css/admin/pages/search-forms.css',
			[
				Utils::$slug . 'choicesjs',
				Utils::$slug . 'collapse-layout',
				Utils::$slug . 'color-picker',
				Utils::$slug . 'input',
				Utils::$slug . 'modal',
				Utils::$slug . 'toggle-switch',
				Utils::$slug . 'radio-img',
				Utils::$slug . 'style',
			],
			SEARCHWP_VERSION
		);

		wp_enqueue_script(
			$handle,
			SEARCHWP_PLUGIN_URL . 'assets/js/admin/pages/search-forms.js',
			[
				'underscore',
				Utils::$slug . 'choices',
				Utils::$slug . 'collapse',
				Utils::$slug . 'color-picker',
				Utils::$slug . 'copy-input-text',
				Utils::$slug . 'modal',
            ],
			SEARCHWP_VERSION,
			true
		);

		Utils::localize_script( $handle );
	}

	/**
	 * Callback for the render of this view.
	 *
	 * @since 4.3.2
	 */
	public static function render() {

		if ( ! current_user_can( Settings::get_capability() ) ) {
			return;
		}

		echo '<div class="swp-content-container">';

		if ( isset( $_GET['form_id'] ) ) {
			self::render_form_settings();
        } else {
	        self::render_forms_list();
        }

		echo '</div>';
	}

	/**
	 * Process forms list actions.
	 *
	 * @since 4.3.2
	 */
	public static function process_list_actions() {

		if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], SEARCHWP_PREFIX . 'settings' ) ) {
			return;
		}

		if ( ! current_user_can( Settings::get_capability() ) ) {
			return;
		}

		$action = isset( $_GET['action'] ) ? sanitize_key( $_GET['action'] ) : '';

		if ( empty( $action ) ) {
			return;
		}

		if ( $action === 'create' ) {
			$form_id = absint( Storage::add() );
			if ( empty( $form_id ) ) {
				wp_safe_redirect( add_query_arg( 'page', 'searchwp-forms', admin_url( 'admin.php' ) ) );
			} else {
				wp_safe_redirect( add_query_arg( [ 'page' => 'searchwp-forms', 'form_id' => $form_id ], admin_url( 'admin.php' ) ) );
			}
		}

		if ( $action === 'trash' ) {
			$form_id = isset( $_GET['form_id'] ) ? absint( $_GET['form_id'] ) : 0;
			if ( empty( $form_id ) ) {
				return;
			}
			Storage::delete( $form_id );
			wp_safe_redirect( add_query_arg( 'page', 'searchwp-forms', admin_url( 'admin.php' ) ) );
		}
	}

	/**
	 * Callback for the forms list render.
	 *
	 * @since 4.3.2
	 */
	private static function render_forms_list() {

		$forms = Storage::get_all();
		?>

		<div class="swp-page-header">
			<div class="swp-flex--row swp-flex--gap12 swp-flex--align-c">
				<h1 class="swp-h1 swp-page-header--h1">
					<?php esc_html_e( 'Search Forms', 'searchwp' ); ?>
				</h1>
				<a href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'action', 'create' ), SEARCHWP_PREFIX . 'settings' ) ); ?>" class="swp-button swp-button--slim swp-button--green-text">
					<?php esc_html_e( 'Add New', 'searchwp' ); ?>
				</a>
			</div>
		</div>

		<table class="swp-forms-list">
			<thead>
				<tr>
					<th class="swp-forms-list--th-name">
						<?php esc_html_e( 'Name', 'searchwp' ); ?>
					</th>

					<th class="swp-forms-list--th-filter-type">
						<?php esc_html_e( 'Type', 'searchwp' ); ?>
					</th>

					<th class="swp-forms-list--th-shortcode">
						<?php esc_html_e( 'Shortcode', 'searchwp' ); ?>
					</th>

					<th class="swp-forms-list--th-actions">
						<?php esc_html_e( 'Action', 'searchwp' ); ?>
					</th>
				</tr>
			</thead>

			<tbody>

				<?php if ( empty( $forms ) ) : ?>
					<tr class="swp-forms-list--item">
						<?php // TODO: Make "create a link". ?>
						<td colspan="4"><?php esc_html_e( 'No forms found. Let`s create one!', 'searchwp' ); ?></td>
					</tr>
				<?php endif; ?>

				<?php foreach ( $forms as $form_id => $form ) : ?>

					<tr class="swp-forms-list--item">

						<td class="swp-forms-list--item-name">
							<a href="<?php echo esc_url( add_query_arg( 'form_id', absint( $form_id ) ) ); ?>">
								<?php echo esc_html( $form['title'] ); ?>
							</a>
						</td>

						<td class="swp-forms-list--item-filter-type">
							<?php echo isset( $form['swp-layout-theme'] ) ? esc_html( ucfirst( $form['swp-layout-theme'] ) ) : '-'; ?>
						</td>

						<td class="swp-forms-list--item-shortcode">
							[searchwp_form id="<?php echo absint( $form_id ); ?>"]
						</td>

						<td>
							<div class="swp-forms-list--item-actions">
								<a href="<?php echo esc_url( wp_nonce_url( add_query_arg( [ 'form_id' => absint( $form_id ), 'action' => 'trash' ] ), SEARCHWP_PREFIX . 'settings' ) ); ?>" class="swp-button swp-button--trash-sm">
									<svg width="12" height="14" viewBox="0 0 14 18" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.77277 15.6668C1.77277 16.7144 2.57857 17.5716 3.56343 17.5716H10.7261C11.7109 17.5716 12.5167 16.7144 12.5167 15.6668V4.23823H1.77277V15.6668ZM3.56343 6.143H10.7261V15.6668H3.56343V6.143ZM10.2784 1.38109L9.38307 0.428711H4.90642L4.01109 1.38109H0.877441V3.28585H13.4121V1.38109H10.2784Z" fill="#0E2121" fill-opacity="0.7"></path></svg>
								</a>

								<a href="<?php echo esc_url( add_query_arg( 'form_id', absint( $form_id ) ) ); ?>" class="swp-button swp-button--edit">
									<svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M1.33333 11.6665H2.26667L8.01666 5.91654L7.08333 4.98321L1.33333 10.7332V11.6665ZM10.8667 4.94987L8.03333 2.14987L8.96667 1.21654C9.22222 0.960984 9.53622 0.833206 9.90866 0.833206C10.2807 0.833206 10.5944 0.960984 10.85 1.21654L11.7833 2.14987C12.0389 2.40543 12.1722 2.71387 12.1833 3.07521C12.1944 3.4361 12.0722 3.74432 11.8167 3.99987L10.8667 4.94987ZM9.9 5.93321L2.83333 12.9999H0V10.1665L7.06667 3.09987L9.9 5.93321Z" fill="#0E2121" fill-opacity="0.7"/>
									</svg>
								</a>
							</div>
						</td>

					</tr>

				<?php endforeach; ?>

			</tbody>
		</table>

		<?php
	}

	/**
	 * Callback for the single form settings render.
	 *
	 * @since 4.3.2
	 */
	private static function render_form_settings() {

		$form_id = absint( $_GET['form_id'] );
		$form    = Storage::get( $form_id );

		if ( empty( $form ) ) {
			wp_safe_redirect( add_query_arg( 'page', 'searchwp-forms', admin_url( 'admin.php' ) ) );
		}

		$engine = isset( $form['engine'] ) ? $form['engine'] : 'default';

		?>
		<div class="swp-page-header">
			<div class="swp-flex--row sm:swp-flex--col sm:swp-flex--gap30 swp-justify-between swp-flex--align-c sm:swp-flex--align-start">

				<div class="swp-flex--row swp-flex--gap15 swp-flex--align-c">
					<h1 class="swp-h1 swp-page-header--h1">
						<?php echo esc_html( $form['title'] ); ?>
					</h1>

					<div class="swp-sf--edit-header--icon">
						<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
							<mask id="mask0_6_2769" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="16" height="16">
								<rect width="16" height="16" fill="#D9D9D9"/>
							</mask>
							<g mask="url(#mask0_6_2769)">
								<path d="M3.33333 12.6665H4.26667L10.0167 6.91652L9.08333 5.98319L3.33333 11.7332V12.6665ZM12.8667 5.94986L10.0333 3.14986L10.9667 2.21652C11.2222 1.96097 11.5362 1.83319 11.9087 1.83319C12.2807 1.83319 12.5944 1.96097 12.85 2.21652L13.7833 3.14986C14.0389 3.40541 14.1722 3.71386 14.1833 4.07519C14.1944 4.43608 14.0722 4.7443 13.8167 4.99986L12.8667 5.94986ZM11.9 6.93319L4.83333 13.9999H2V11.1665L9.06667 4.09986L11.9 6.93319Z" fill="#0E2121" fill-opacity="0.7"/>
							</g>
						</svg>
					</div>
				</div>

				<input type="text" class="swp-input swp-sf--input-header" name="title" value="<?php echo esc_attr( $form['title'] ); ?>" style="display:none;">

				<div class="swp-flex--row swp-flex--gap15 swp-flex--grow0">

					<button type="button" class="swp-button swp-button--flex-content" data-swp-modal="#swp-search-form-embed-modal">
						<svg width="13" height="8" viewBox="0 0 13 8" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M4 7.84615L0.25 3.99999L4 0.153839L4.89062 1.0673L2.01562 4.01602L4.875 6.94871L4 7.84615ZM9 7.84615L8.10938 6.93269L10.9844 3.98397L8.125 1.05128L9 0.153839L12.75 3.99999L9 7.84615Z" fill="#0E2121" fill-opacity="0.8"/>
						</svg>

						<?php esc_html_e( 'Embed', 'searchwp' ); ?>
					</button>

					<button type="button" id="swp-form-save" class="swp-button swp-button--green" data-form-id="<?php echo absint( $form_id ); ?>">
						<?php esc_html_e( 'Save', 'searchwp' ); ?>
					</button>

				</div>
			</div>
		</div>

		<div class="swp-collapse swp-opened">

			<div class="swp-collapse--header">

				<h2 class="swp-h2">
					<?php esc_html_e( 'Chose a theme', 'searchwp' ); ?>
				</h2>

				<button class="swp-expand--button">
					<svg class="swp-arrow" width="17" height="11" viewBox="0 0 17 11" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M14.2915 0.814362L8.09717 6.95819L1.90283 0.814362L0 2.7058L8.09717 10.7545L16.1943 2.7058L14.2915 0.814362Z" fill="#0E2121" fill-opacity="0.8"/>
					</svg>
				</button>

			</div>

			<div class="swp-collapse--content">

				<div class="swp-row">

					<div class="swp-flex--row sm:swp-flex--col sm:swp-flex--gap30">

						<div class="swp-col swp-col--title-width--sm">

							<h3 class="swp-h3">
								<?php esc_html_e( 'Layout Theme', 'searchwp' ); ?>
							</h3>

						</div>


						<div class="swp-col">

							<div class="swp-flex--row swp-flex--gap20 swp-sf--layout-themes">

								<div class="swp-flex--grow1 swp-input--radio-img">

									<input type="radio" id="swp-basic-theme" name="swp-layout-theme" value="basic"<?php checked( $form['swp-layout-theme'], 'basic' ); ?> />
									
									<label for="swp-basic-theme">
										<img src="<?php echo esc_url( SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/search-forms/swp-sf--basic.svg' ); ?>" alt="" />

										<?php esc_html_e( 'Basic', 'searchwp' ); ?>

									</label>
									
								</div>

								<div class="swp-flex--grow1 swp-input--radio-img">

									<input type="radio" id="swp-cat-search-theme" name="swp-layout-theme" value="category"<?php checked( $form['swp-layout-theme'], 'category' ); ?> />
									
									<label for="swp-cat-search-theme">
										<img src="<?php echo esc_url( SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/search-forms/swp-sf--cat-search.svg' ); ?>" alt="" />

										<?php esc_html_e( 'Category Search', 'searchwp' ); ?>

									</label>
									
								</div>

								<div class="swp-flex--grow1 swp-input--radio-img">

									<input type="radio" id="swp-quick-search-theme" name="swp-layout-theme" value="quick"<?php checked( $form['swp-layout-theme'], 'quick' ); ?> />
									
									<label for="swp-quick-search-theme">
										<img src="<?php echo esc_url( SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/search-forms/swp-sf--quick-search.svg' ); ?>" alt="" />

										<?php esc_html_e( 'Quick Search', 'searchwp' ); ?>

									</label>
									
								</div>

								<div class="swp-flex--grow1 swp-input--radio-img">

									<input type="radio" id="swp-advanced-search-theme" name="swp-layout-theme" value="advanced"<?php checked( $form['swp-layout-theme'], 'advanced' ); ?> />
									
									<label for="swp-advanced-search-theme">
										<img src="<?php echo esc_url( SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/search-forms/swp-sf--advanced-search.svg' ); ?>" alt="" />

										<?php esc_html_e( 'Advanced Search', 'searchwp' ); ?>

									</label>
									
								</div>

								<div class="swp-flex--grow1 swp-input--radio-img">

									<input type="radio" id="swp-combined-theme"  name="swp-layout-theme" value="combined"<?php checked( $form['swp-layout-theme'], 'combined' ); ?> />
									
									<label for="swp-combined-theme">
										<img src="<?php echo esc_url( SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/search-forms/swp-sf--combined.svg' ); ?>" alt="" />

										<?php esc_html_e( 'Combined', 'searchwp' ); ?>

									</label>
									
								</div>
							
							</div>

							<h4 class="swp-h4 swp-margin-t30">
								<?php esc_html_e( 'Theme Preview', 'searchwp' ); ?>
							</h4>

							<div class="swp-sf--theme-preview swp-flex--col swp-flex--gap20">

								<div class="swp-flex--row swp-flex--wrap swp-flex--gap9">

									<div class="swp-sf--search-input-preview">
										
										<select id="swp-sf--theme-preview-category" class="swp-input swp-select" disabled<?php echo empty( $form['category-search'] ) ? ' style="display: none;"' : ''; ?>>
											<option><?php esc_html_e( 'Category', 'searchwp' ); ?></option>
										</select>
										
										<input class="swp-input swp-input--search swp-w-full" type="search" placeholder="<?php esc_html_e( 'Enter keyword and search', 'searchwp' ); ?>" disabled>
									
									</div>

									<button id="swp-sf--theme-preview-button" class="swp-button" type="button" disabled<?php echo empty( $form['search-button'] ) ? ' style="display: none;"' : ''; ?>>
										<?php esc_html_e( 'Search', 'searchwp' ); ?>
									</button>
								
								</div>

								<div id="swp-sf--theme-preview-advanced" class="swp-flex--col swp-flex--gap20"<?php echo empty( $form['advanced-search'] ) ? ' style="display: none;"' : ''; ?>>

									<label class="swp-toggle swp-flex--row swp-flex--gap9 swp-flex--align-c swp-margin-l-auto">

										<input class="swp-toggle-checkbox" type="checkbox" checked disabled>

										<div class="swp-toggle-switch swp-toggle-switch--mini"></div>

										<span class="swp-p">
											<?php esc_html_e( 'Advanced Search', 'searchwp' ); ?>
										</span>

									</label>

									<div class="swp-flex--row swp-flex--gap9">

										<select class="swp-input swp-select swp-flex--grow1" disabled>
											<option value="author">Author</option>
										</select>

										<select class="swp-input swp-select swp-flex--grow1" disabled>
											<option value="post_type">Post Type</option>
										</select>

										<select class="swp-input swp-select swp-flex--grow1" disabled>
											<option value="tag">Tag</option>
										</select>

									</div>

								</div>

								<div id="swp-sf--theme-preview-quick" class="swp-flex--row swp-flex--wrap swp-flex--gap17"<?php echo empty( $form['quick-search'] ) ? ' style="display: none;"' : ''; ?>>

									<p class="swp-p">
										<?php esc_html_e( 'Popular searches:', 'searchwp' ); ?>
									</p>

									<a class="swp-a" role="link" aria-disabled="true">
										<?php esc_html_e( 'Website', 'searchwp' ); ?>
									</a>

									<a class="swp-a" role="link" aria-disabled="true">
										<?php esc_html_e( 'SEO', 'searchwp' ); ?>
									</a>

									<a class="swp-a" role="link" aria-disabled="true">
										<?php esc_html_e( 'Performance', 'searchwp' ); ?>
									</a>

									<a class="swp-a" role="link" aria-disabled="true">
										<?php esc_html_e( 'Networking', 'searchwp' ); ?>
									</a>
							
								</div>

							</div>
						
						</div>

					</div>

				</div>

			</div>

		</div>


		<div class="swp-collapse swp-opened">

			<div class="swp-collapse--header">

				<h2 class="swp-h2">
					<?php esc_html_e( 'Custom Styling', 'searchwp' ); ?>
				</h2>

				<button class="swp-expand--button">
					<svg class="swp-arrow" width="17" height="11" viewBox="0 0 17 11" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M14.2915 0.814362L8.09717 6.95819L1.90283 0.814362L0 2.7058L8.09717 10.7545L16.1943 2.7058L14.2915 0.814362Z" fill="#0E2121" fill-opacity="0.8"/>
					</svg>
				</button>

			</div>

			<div class="swp-collapse--content">

				<div class="swp-row">
					
					<div class="swp-flex--col swp-flex--gap40">

						<div class="swp-flex--row sm:swp-flex--col sm:swp-flex--gap30">

							<div class="swp-col swp-col--title-width--sm">

								<h3 class="swp-h3">
									<?php esc_html_e( 'Category Search', 'searchwp' ); ?>
								</h3>

							</div>


							<div class="swp-col">

								<label class="swp-toggle">
											
									<input class="swp-toggle-checkbox" type="checkbox" name="category-search"<?php checked( $form['category-search'] ); ?>>
									
									<div class="swp-toggle-switch"></div>

								</label>

							</div>

						</div>

						<div class="swp-flex--row sm:swp-flex--col sm:swp-flex--gap30">

							<div class="swp-col swp-col--title-width--sm">

								<h3 class="swp-h3">
									<?php esc_html_e( 'Quick Search', 'searchwp' ); ?>
								</h3>

							</div>


							<div class="swp-col">

								<label class="swp-toggle">
											
									<input class="swp-toggle-checkbox" type="checkbox" name="quick-search"<?php checked( $form['quick-search'] ); ?>>
									
									<div class="swp-toggle-switch"></div>

								</label>

							</div>

						</div>

						<div class="swp-flex--row sm:swp-flex--col sm:swp-flex--gap30">

							<div class="swp-col swp-col--title-width--sm">

								<h3 class="swp-h3">
									<?php esc_html_e( 'Advanced Search', 'searchwp' ); ?>
								</h3>

							</div>


							<div class="swp-col">

								<label class="swp-toggle">
											
									<input class="swp-toggle-checkbox" type="checkbox" name="advanced-search"<?php checked( $form['advanced-search'] ); ?>>
									
									<div class="swp-toggle-switch"></div>

								</label>

							</div>

						</div>
					
					</div>

				</div>

				<div class="swp-row">
					
					<div class="swp-flex--col swp-flex--gap30">

                        <div class="swp-flex--row sm:swp-flex--col sm:swp-flex--gap30">

                            <div class="swp-col swp-col--title-width--sm">

                                <h3 class="swp-h3">
									<?php esc_html_e( 'Engine', 'searchwp' ); ?>
                                </h3>

                            </div>

                            <div class="swp-col">

                                <div class="swp-w-1/4">
									<?php
									$engines = \SearchWP\Settings::get_engines();
									$selected_engine = isset( $form['engine'] ) ? $form['engine'] : 'default';
									?>
                                    <select class="swp-choicesjs-single" name="engine">
										<?php foreach ( $engines as $_engine ) : ?>
											<?php $engine_name = $_engine->get_name(); ?>
                                            <option value="<?php echo esc_attr( $engine_name ); ?>"<?php selected( $selected_engine, $engine_name ); ?>><?php echo esc_html( $_engine->get_label() ); ?></option>
										<?php endforeach; ?>
                                    </select>
                                </div>

                            </div>

                        </div>

                        <div class="swp-flex--row sm:swp-flex--col sm:swp-flex--gap30">

                            <div class="swp-col swp-col--title-width--sm">

                                <h3 class="swp-h3">
									<?php esc_html_e( 'Results Page', 'searchwp' ); ?>
                                </h3>

                            </div>

                            <div class="swp-col">

                                <div class="swp-w-1/4">
                                    <?php $selected_search_page = isset( $form['input_name'] ) ? $form['input_name'] : 's'; ?>
                                    <select class="swp-choicesjs-single" name="input_name">
                                        <?php if ( ! in_array( $selected_search_page, [ 's', 'swps' ], true ) ) : ?>
                                            <option value="<?php echo esc_attr( $selected_search_page ); ?>" selected="selected">
                                                <?php
                                                echo esc_html(
                                                    sprintf(
                                                        /* translators: %s: Custom name of search input. */
                                                        __( 'Custom: %s', 'searchwp' ),
                                                        $selected_search_page
                                                    )
                                                );
                                                ?>
                                            </option>
                                        <?php endif; ?>
                                        <option value="s"<?php selected( $selected_search_page, 'default' ); ?>><?php esc_html_e( 'Default', 'searchwp' ); ?></option>
                                        <option value="swps"<?php selected( $selected_search_page, 'swps' ); ?>><?php esc_html_e( 'SearchWP', 'searchwp' ); ?></option>
                                    </select>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

                <div class="swp-row">

                    <div class="swp-flex--col swp-flex--gap30">

						<div class="swp-flex--row sm:swp-flex--col sm:swp-flex--gap30">

							<div class="swp-col swp-col--title-width--sm">

								<h3 class="swp-h3">
									<?php esc_html_e( 'Type of search', 'searchwp' ); ?>
								</h3>

							</div>

							<div class="swp-col">

								<div class="swp-w-1/2">
									<?php
									$sources          = ( new Engine( $engine ) )->get_sources();
									$selected_sources = isset( $form['post-type'] ) ? $form['post-type'] : [];
									?>
									<select class="swp-choicesjs-multiple" name="post-type" multiple>
										<?php foreach ( $sources as $source ) : ?>
										<?php $source_name = $source->get_name(); ?>
											<option value="<?php echo esc_attr( $source_name ); ?>"<?php selected( in_array( $source_name, $selected_sources, true ) ); ?>><?php echo esc_html( $source->get_label() ); ?></option>
										<?php endforeach; ?>
									</select>
								</div>

							</div>

						</div>

						<div class="swp-flex--row sm:swp-flex--col sm:swp-flex--gap30">

							<div class="swp-col swp-col--title-width--sm">

								<h3 class="swp-h3">
									<?php esc_html_e( 'Select Category', 'searchwp' ); ?>
								</h3>

							</div>

							<div class="swp-col">

								<div class="swp-w-1/2">
									<?php
									$categories          = get_terms( [ 'taxonomy' => 'category' ] );
									$selected_categories = isset( $form['category'] ) ? $form['category'] : [];
									?>
									<select class="swp-choicesjs-multiple" name="category" multiple>
										<?php foreach ( $categories as $category ) : ?>
											<option value="<?php echo absint( $category->term_id ); ?>"<?php selected( in_array( $category->term_id, $selected_categories ) ); ?>><?php echo esc_html( $category->name ); ?></option>
										<?php endforeach; ?>
									</select>
								</div>

							</div>

						</div>

						<div class="swp-flex--row sm:swp-flex--col sm:swp-flex--gap30">

							<div class="swp-col swp-col--title-width--sm">

								<h3 class="swp-h3">
									<?php esc_html_e( 'Field Label', 'searchwp' ); ?>
								</h3>

							</div>

							<div class="swp-col">

								<input class="swp-input swp-w-1/2" type="text" name="field-label" value="<?php echo esc_attr( $form['field-label'] ); ?>" placeholder="<?php esc_html_e( 'Enter keyword and search', 'searchwp' ); ?>">

							</div>

						</div>

						<div class="swp-flex--row sm:swp-flex--col sm:swp-flex--gap30">

							<div class="swp-col swp-col--title-width--sm">

								<h3 class="swp-h3">
									<?php esc_html_e( 'Search Button', 'searchwp' ); ?>
								</h3>

							</div>


							<div class="swp-col">

								<label class="swp-toggle">
											
									<input class="swp-toggle-checkbox" type="checkbox" name="search-button"<?php checked( $form['search-button'] ); ?>>
									
									<div class="swp-toggle-switch"></div>

								</label>

							</div>

						</div>
					
					</div>

				</div>


				<div class="swp-row">

					<div class="swp-flex--row sm:swp-flex--col sm:swp-flex--gap30">

						<div class="swp-col swp-col--title-width--sm">

							<h3 class="swp-h3">
								<?php esc_html_e( 'Quick Search Items', 'searchwp' ); ?>
							</h3>

						</div>

						<div class="swp-col">

							<div class="swp-flex--col swp-flex--gap25">

								<label class="swp-label" for="swp-qs-place">
									<?php esc_html_e( 'Include search items from selecting multiple', 'searchwp' ); ?>
								</label>

								<div class="swp-w-1/2">
									<?php
									// TODO: Needs days number filter.
									if ( class_exists( '\SearchWP_Metrics\QueryPopularQueriesOverTime' ) ) {
										$query = new \SearchWP_Metrics\QueryPopularQueriesOverTime(
											[
												'engine' => $engine,
												'after'  => '30 days ago',
											]
										);

										$popular_searches = $query->get_results();
									} else {
										$popular_searches = Statistics::get_popular_searches(
											[
												'days'    => 30,
												'engine'  => $engine,
												'exclude' => Settings::get( 'ignored_queries', 'array' ),
											]
										);
									}

									$popular_searches  = wp_list_pluck( $popular_searches, 'query' );
									$selected_searches = isset( $form['quick-search-items'] ) ? $form['quick-search-items'] : [];
									$popular_searches  = array_unique( array_merge( $popular_searches, $selected_searches ) );
									?>
									<select class="swp-choicesjs-hybrid" name="quick-search-items" multiple>
										<?php foreach ( $popular_searches as $popular_search ) : ?>
											<option value="<?php echo esc_attr( $popular_search ); ?>"<?php selected( in_array( $popular_search, $selected_searches, true ) ); ?>><?php echo esc_html( $popular_search ); ?></option>
										<?php endforeach; ?>
									</select>
								</div>

							</div>

						</div>

					</div>
									
				</div>


				<div class="swp-row">
					
					<div class="swp-flex--row sm:swp-flex--col sm:swp-flex--gap30">

						<div class="swp-col swp-col--title-width--sm">

							<h3 class="swp-h3">
								<?php esc_html_e( 'Advanced Search Filter/s', 'searchwp' ); ?>
							</h3>

						</div>

						<div class="swp-col">

							<div class="swp-w-1/2">
								<?php
								$filters = [
									'authors'    => __( 'Authors', 'searchwp' ),
									'post_types' => __( 'Post Types', 'searchwp' ),
									'tags'       => __( 'Tags', 'searchwp' ),
								];
								$selected_filters = isset( $form['advanced-search-filters'] ) ? $form['advanced-search-filters'] : [];
								?>
								<select class="swp-choicesjs-multiple" name="advanced-search-filters" multiple>
									<?php foreach ( $filters as $filter_slug => $filter_title ) : ?>
										<option value="<?php echo esc_attr( $filter_slug ); ?>"<?php selected( in_array( $filter_slug, $selected_filters, true ) ); ?>><?php echo esc_html( $filter_title ); ?></option>
									<?php endforeach; ?>
								</select>
							</div>

						</div>

					</div>
				
				</div>


				<div class="swp-row swp-search-box-style">

					<div class="swp-flex--col swp-flex--gap30">

						<div class="swp-flex--row sm:swp-flex--col sm:swp-flex--gap30">

							<div class="swp-col swp-col--title-width--sm">

								<h3 class="swp-h3">
									<?php esc_html_e( 'Form Style', 'searchwp' ); ?>
								</h3>

							</div>

							<div class="swp-col">

								<div class="swp-flex--col swp-flex--gap30 swp-sf--box-style">

									<div class="swp-flex--row swp-flex--gap12">

										<div class="swp-input--radio-img">

											<input type="radio" name="swp-sfinput-shape" id="swp-sfinput-nostyle" value=""<?php checked( $form['swp-sfinput-shape'], '' ); ?> />

											<label for="swp-sfinput-nostyle">
												<img src="<?php echo esc_url( SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/search-forms/swp-sf-nostyle.svg' ); ?>" alt="" />

												<?php esc_html_e( 'No Style', 'searchwp' ); ?>

											</label>

										</div>

										<div class="swp-input--radio-img">

											<input type="radio" name="swp-sfinput-shape" id="swp-sfinput-rectangle" value="rectangle"<?php checked( $form['swp-sfinput-shape'], 'rectangle' ); ?> />

											<label for="swp-sfinput-rectangle">
												<img src="<?php echo esc_url( SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/search-forms/swp-sf-rectangle.svg' ); ?>" alt="" />

												<?php esc_html_e( 'Rectangle', 'searchwp' ); ?>

											</label>

										</div>

										<div class="swp-input--radio-img">

											<input type="radio" name="swp-sfinput-shape" id="swp-sfinput-rounded" value="rounded"<?php checked( $form['swp-sfinput-shape'], 'rounded' ); ?> />

											<label for="swp-sfinput-rounded">
												<img src="<?php echo esc_url( SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/search-forms/swp-sf-rounded.svg' ); ?>" alt="" />

												<?php esc_html_e( 'Rounded', 'searchwp' ); ?>

											</label>

										</div>

										<div class="swp-input--radio-img">

											<input type="radio" name="swp-sfinput-shape" id="swp-sfinput-underlined" value="underlined"<?php checked( $form['swp-sfinput-shape'], 'underlined' ); ?>/>

											<label for="swp-sfinput-underlined">
												<img src="<?php echo esc_url( SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/search-forms/swp-sf-underlined.svg' ); ?>" alt="" />

												<?php esc_html_e( 'Underlined', 'searchwp' ); ?>

											</label>

										</div>

									</div>

									<div class="swp-flex--row swp-flex--gap17">

										<div class="swp-inputbox-vertical">

											<label for="" class="swp-label">
												<?php esc_html_e( 'Border Color', 'searchwp' ); ?>
											</label>

											<span class="swp-input--colorpicker">
											<input type="text" class="swp-input" name="search-form-color" value="<?php echo esc_attr( $form['search-form-color'] ); ?>" placeholder="default" maxlength="7">
											<svg fill="none" height="18" viewBox="0 0 18 18" width="18" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g mask="url(#a)"><path d="m9.74075 15.25c-1.53556 0-2.82666-.5274-3.90225-1.5897-1.07639-1.0631-1.60779-2.3322-1.60779-3.8353 0-.76007.1438-1.45237.42598-2.08339.28739-.64268.68359-1.21661 1.19118-1.72371l3.89288-3.8176 3.89285 3.81755c.5076.50712.9038 1.08106 1.1912 1.72376.2822.63102.426 1.32332.426 2.08339 0 1.5031-.5312 2.7723-1.6071 3.8353-1.0761 1.0623-2.3675 1.5897-3.90295 1.5897z" fill="#fff" stroke="#e1e1e1"/></g></svg>
										</span>

										</div>

										<div class="swp-inputbox-vertical">

											<label for="" class="swp-label">
												<?php esc_html_e( 'Font', 'searchwp' ); ?>
											</label>

											<span class="swp-input--font-input">
											<input type="number" min="0" class="swp-input" name="search-form-font-size"<?php echo ! empty( $form['search-form-font-size'] ) ? ' value="' . absint( $form['search-form-font-size'] ) . '"' : ''; ?> placeholder="-">
										</span>

										</div>

									</div>

								</div>

							</div>

						</div>

					</div>

				</div>

				<div class="swp-row swp-search-box-style">

					<div class="swp-flex--col swp-flex--gap30">
					
						<div class="swp-flex--row sm:swp-flex--col sm:swp-flex--gap30">

							<div class="swp-col swp-col--title-width--sm">

								<h3 class="swp-h3">
									<?php esc_html_e( 'Button Style', 'searchwp' ); ?>
								</h3>

							</div>

							<div class="swp-col">

								<div class="swp-flex--col swp-flex--gap30 swp-sf--box-style">

									<div class="swp-flex--row swp-flex--gap12">

										<div class="swp-input--radio-img">

											<input type="radio" name="swp-sfbutton-filled" id="swp-sfbutton-nostyle" value=""<?php checked( $form['swp-sfbutton-filled'], '' ); ?> />

											<label for="swp-sfbutton-nostyle">
												<img src="<?php echo esc_url( SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/search-forms/swp-sf-btn-nostyle.svg' ); ?>" alt="" />

												<?php esc_html_e( 'No Style', 'searchwp' ); ?>

											</label>

										</div>

										<div class="swp-input--radio-img">

											<input type="radio" name="swp-sfbutton-filled" id="swp-sfbutton-filled" value="filled"<?php checked( $form['swp-sfbutton-filled'], 'filled' ); ?> />
											
											<label for="swp-sfbutton-filled">
												<img src="<?php echo esc_url( SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/search-forms/swp-sf-btn-filled.svg' ); ?>" alt="" />

												<?php esc_html_e( 'Filled', 'searchwp' ); ?>

											</label>
											
										</div>

										<div class="swp-input--radio-img">

											<input type="radio" name="swp-sfbutton-filled" id="swp-sfbutton-stroked" value="stroked"<?php checked( $form['swp-sfbutton-filled'], 'stroked' ); ?> />
											
											<label for="swp-sfbutton-stroked">
												<img src="<?php echo esc_url( SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/search-forms/swp-sf-btn-stroked.svg' ); ?>" alt="" />

												<?php esc_html_e( 'Stroked', 'searchwp' ); ?>

											</label>
											
										</div>

									</div>

                                    <div class="swp-flex--row swp-flex--gap17">

                                        <div class="swp-inputbox-vertical swp-w-1/3">

                                            <label for="" class="swp-label">
												<?php esc_html_e( 'Label', 'searchwp' ); ?>
                                            </label>

                                            <input class="swp-input swp-w-full" type="text" name="button-label" value="<?php echo ! empty( $form['button-label'] ) ? esc_attr( $form['button-label'] ) : ''; ?>" placeholder="<?php esc_html_e( 'Search', 'searchwp' ); ?>">

                                        </div>

                                    </div>

									<div class="swp-flex--row swp-flex--gap17">

										<div class="swp-inputbox-vertical">

											<label for="" class="swp-label">
												<?php esc_html_e( 'Background', 'searchwp' ); ?>
											</label>

											<span class="swp-input--colorpicker">
                                                <input type="text" class="swp-input" name="button-background-color" value="<?php echo esc_attr( $form['button-background-color'] ); ?>" placeholder="default" maxlength="7">
                                                <svg fill="none" height="18" viewBox="0 0 18 18" width="18" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g mask="url(#a)"><path d="m9.74075 15.25c-1.53556 0-2.82666-.5274-3.90225-1.5897-1.07639-1.0631-1.60779-2.3322-1.60779-3.8353 0-.76007.1438-1.45237.42598-2.08339.28739-.64268.68359-1.21661 1.19118-1.72371l3.89288-3.8176 3.89285 3.81755c.5076.50712.9038 1.08106 1.1912 1.72376.2822.63102.426 1.32332.426 2.08339 0 1.5031-.5312 2.7723-1.6071 3.8353-1.0761 1.0623-2.3675 1.5897-3.90295 1.5897z" fill="#fff" stroke="#e1e1e1"/></g></svg>
                                            </span>

										</div>

										<div class="swp-inputbox-vertical">

											<label for="" class="swp-label">
												<?php esc_html_e( 'Font Color', 'searchwp' ); ?>
											</label>

											<span class="swp-input--colorpicker">
											    <input type="text" class="swp-input" name="button-font-color" value="<?php echo esc_attr( $form['button-font-color'] ); ?>" placeholder="default" maxlength="7">
                                                <svg fill="none" height="18" viewBox="0 0 18 18" width="18" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g mask="url(#a)"><path d="m9.74075 15.25c-1.53556 0-2.82666-.5274-3.90225-1.5897-1.07639-1.0631-1.60779-2.3322-1.60779-3.8353 0-.76007.1438-1.45237.42598-2.08339.28739-.64268.68359-1.21661 1.19118-1.72371l3.89288-3.8176 3.89285 3.81755c.5076.50712.9038 1.08106 1.1912 1.72376.2822.63102.426 1.32332.426 2.08339 0 1.5031-.5312 2.7723-1.6071 3.8353-1.0761 1.0623-2.3675 1.5897-3.90295 1.5897z" fill="#fff" stroke="#e1e1e1"/></g></svg>
                                            </span>

										</div>

										<div class="swp-inputbox-vertical">

											<label for="" class="swp-label">
												<?php esc_html_e( 'Font', 'searchwp' ); ?>
											</label>

											<span class="swp-input--font-input">
                                                <input type="number" min="0" class="swp-input" name="button-font-size"<?php echo ! empty( $form['button-font-size'] ) ? ' value="' . absint( $form['button-font-size'] ) . '"' : ''; ?> placeholder="-">
                                            </span>

										</div>

									</div>

								</div>

							</div>

						</div>
					
					</div>
				
				</div>
			
			</div>

		</div>


		<div id="swp-search-form-embed-modal" class="swp-modal swp-modal--centered swp-modal-l" style="display: none;">

			<div class="swp-modal--header swp-bg--gray">

				<div class="swp-flex--row swp-justify-between swp-flex--align-c">

					<h1 class="swp-h1 swp-font-size16">
						<?php esc_html_e( 'Embed in a Page', 'searchwp' ); ?>
					</h1>

					<button class="swp-modal--close">
						<svg width="16" height="15" viewBox="0 0 16 15" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path opacity="0.5" d="M16 1.49633L14.3886 0L8 5.93224L1.61143 0L0 1.49633L6.38857 7.42857L0 13.3608L1.61143 14.8571L8 8.9249L14.3886 14.8571L16 13.3608L9.61143 7.42857L16 1.49633Z" fill="#646970"></path>
						</svg>
					</button>

				</div>

			</div>

			<div class="swp-modal--content swp-margin-b25">

				<p class="swp-p swp-margin-b25">
					<b class="swp-b"><?php esc_html_e( 'We can help embed your search form with just a few clicks!', 'searchwp' ); ?></b></br>
					<?php esc_html_e( 'Would you like to embed your search form in an existing page, or create a new one?', 'searchwp' ); ?>
				</p>

				<hr class="swp-hr swp-margin-b25">

				<div class="swp-flex--row swp-margin-b25">
					
					<div class="swp-col swp-col--title-width">

						<h3 class="swp-h3">
							<?php esc_html_e( 'Embed Search Form', 'searchwp' ); ?>
						</h3>

					</div>

					<div class="swp-col">
								
						<div class="swp-flex--col swp-flex--gap17">

							<div class="swp-flex--row swp-flex--gap9 sm:swp-flex--col sm:swp-flex--gap30">

								<div class="swp-input--radio-embed">

									<input type="radio" name="swp-sfe-embed" id="swp-sfe-gutenberg" value="swp-sfe-gutenberg-desc" />

									<label for="swp-sfe-gutenberg">

										<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
											<mask id="mask0_6_3691" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="20" height="20">
												<rect width="20" height="20" fill="#D9D9D9"/>
											</mask>
											<g mask="url(#mask0_6_3691)">
												<path d="M9.16667 14.1667H10.8333V10.8333H14.1667V9.16667H10.8333V5.83333H9.16667V9.16667H5.83333V10.8333H9.16667V14.1667ZM4.16667 17.5C3.70833 17.5 3.31583 17.3369 2.98917 17.0108C2.66306 16.6842 2.5 16.2917 2.5 15.8333V4.16667C2.5 3.70833 2.66306 3.31583 2.98917 2.98917C3.31583 2.66306 3.70833 2.5 4.16667 2.5H15.8333C16.2917 2.5 16.6842 2.66306 17.0108 2.98917C17.3369 3.31583 17.5 3.70833 17.5 4.16667V15.8333C17.5 16.2917 17.3369 16.6842 17.0108 17.0108C16.6842 17.3369 16.2917 17.5 15.8333 17.5H4.16667ZM4.16667 15.8333H15.8333V4.16667H4.16667V15.8333Z" fill="#0E2121" fill-opacity="0.9"/>
											</g>
										</svg>

										<?php esc_html_e( 'Gutenberg Block', 'searchwp' ); ?>

									</label>

								</div>

								<div class="swp-input--radio-embed">

									<input type="radio" name="swp-sfe-embed" id="swp-sfe-shortcode" value="swp-sfe-shortcode-desc" />

									<label for="swp-sfe-shortcode">

										<svg width="24" height="20" viewBox="0 0 24 12" fill="none" xmlns="http://www.w3.org/2000/svg">
											<path d="M0 0H4.47458V1.32625H2.40664V10.6674H4.47458V12H0V0Z" fill="#0E2121" fill-opacity="0.9"/>
											<path d="M13.885 0H16.2713L10.5019 12H8.13574L13.885 0Z" fill="#0E2121" fill-opacity="0.9"/>
											<path d="M24 0H19.5254V1.32625H21.5934V10.6674H19.5254V12H24V0Z" fill="#0E2121" fill-opacity="0.9"/>
										</svg>

										<?php esc_html_e( 'Shortcode', 'searchwp' ); ?>

									</label>

								</div>

								<div class="swp-input--radio-embed">

									<input type="radio" name="swp-sfe-embed" id="swp-sfe-phpcode" value="swp-sfe-phpcode-desc" />

									<label for="swp-sfe-phpcode">

										<svg width="33" height="20" viewBox="0 0 33 7" fill="none" xmlns="http://www.w3.org/2000/svg">
											<path d="M6.7998 5.83546H8.26923V7H6.7998V5.83546ZM5 2.93418C5.03945 2.16857 5.36654 1.62592 5.98126 1.30625C6.36917 1.10208 6.84583 1 7.41124 1C8.15418 1 8.77055 1.14506 9.26035 1.43519C9.75345 1.72532 10 2.15514 10 2.72465C10 3.07387 9.89316 3.36803 9.67949 3.60712C9.55457 3.75218 9.3146 3.93754 8.95957 4.1632L8.60947 4.38482C8.4188 4.50571 8.29224 4.64674 8.22978 4.80792C8.19034 4.91001 8.16897 5.0685 8.16568 5.28341H6.83432C6.85404 4.82942 6.90664 4.51645 6.99211 4.34453C7.07758 4.16991 7.29783 3.96978 7.65286 3.74412L8.01282 3.51444C8.13116 3.44191 8.2265 3.36266 8.29882 3.2767C8.43031 3.12895 8.49606 2.96642 8.49606 2.78912C8.49606 2.58496 8.42209 2.3996 8.27416 2.23304C8.12952 2.0638 7.86325 1.97918 7.47535 1.97918C7.09402 1.97918 6.82281 2.08261 6.66174 2.28946C6.50394 2.49631 6.42505 2.71122 6.42505 2.93418H5Z" fill="#0E2121" fill-opacity="0.9"/>
											<path d="M14.8993 2.59091C14.8993 2.33085 14.8277 2.14541 14.6844 2.0346C14.5435 1.92379 14.3451 1.86839 14.0891 1.86839H13.078V3.34057H14.0891C14.3451 3.34057 14.5435 3.28064 14.6844 3.16079C14.8277 3.04093 14.8993 2.85097 14.8993 2.59091ZM15.9738 2.58412C15.9738 3.17436 15.8188 3.59159 15.5088 3.83582C15.1988 4.08005 14.7561 4.20217 14.1807 4.20217H13.078V6H12V1H14.2617C14.7831 1 15.1988 1.1289 15.5088 1.3867C15.8188 1.6445 15.9738 2.04365 15.9738 2.58412Z" fill="#0E2121" fill-opacity="0.9"/>
											<path d="M16.7806 6V1H17.8551V2.90638H19.8842V1H20.9623V6H19.8842V3.76798H17.8551V6H16.7806Z" fill="#0E2121" fill-opacity="0.9"/>
											<path d="M24.9255 2.59091C24.9255 2.33085 24.8539 2.14541 24.7106 2.0346C24.5697 1.92379 24.3712 1.86839 24.1152 1.86839H23.1042V3.34057H24.1152C24.3712 3.34057 24.5697 3.28064 24.7106 3.16079C24.8539 3.04093 24.9255 2.85097 24.9255 2.59091ZM26 2.58412C26 3.17436 25.845 3.59159 25.535 3.83582C25.225 4.08005 24.7823 4.20217 24.2068 4.20217H23.1042V6H22.0262V1H24.2879C24.8093 1 25.225 1.1289 25.535 1.3867C25.845 1.6445 26 2.04365 26 2.58412Z" fill="#0E2121" fill-opacity="0.9"/>
											<path d="M4.04858 0L5 0.8225L1.90958 3.5L5 6.1775L4.04858 7L0 3.5L4.04858 0Z" fill="#0E2121" fill-opacity="0.9"/>
											<path d="M29.7611 0L29 0.8225L31.4723 3.5L29 6.1775L29.7611 7L33 3.5L29.7611 0Z" fill="#0E2121" fill-opacity="0.9"/>
										</svg>

										<?php esc_html_e( 'PHP Code', 'searchwp' ); ?>

									</label>

								</div>

							</div>

							<div id="swp-sfe-gutenberg-desc" class="swp-sfe-embed--desc" style="display: none;">

								<p class="swp-desc swp-leading--160">
									<?php esc_html_e( 'To add this block, edit a page or post and search for “SearchWP” block.', 'searchwp' ); ?>
								</p>

							</div>

							<div id="swp-sfe-shortcode-desc" class="swp-sfe-embed--desc" style="display: none;">

								<div class="swp-flex--row swp-flex--gap9 sm:swp-flex--col sm:swp-flex--gap30">

									<input type="text" id="swp-sfe-shortcode-desc-input" class="swp-input swp-input--embed" value="[searchwp_form id=<?php echo absint( $form_id ); ?>]" disabled>

									<button class="swp-button swp-button--flex-content" type="button" data-swp-copy-from="#swp-sfe-shortcode-desc-input">

										<svg width="13" height="15" viewBox="0 0 13 15" fill="none" xmlns="http://www.w3.org/2000/svg">
											<path d="M9.3125 0.625H1.8125C1.125 0.625 0.5625 1.1875 0.5625 1.875V10.625H1.8125V1.875H9.3125V0.625ZM11.1875 3.125H4.3125C3.625 3.125 3.0625 3.6875 3.0625 4.375V13.125C3.0625 13.8125 3.625 14.375 4.3125 14.375H11.1875C11.875 14.375 12.4375 13.8125 12.4375 13.125V4.375C12.4375 3.6875 11.875 3.125 11.1875 3.125ZM11.1875 13.125H4.3125V4.375H11.1875V13.125Z" fill="#0E2121" fill-opacity="0.8"/>
										</svg>

										<?php esc_html_e( 'Copy', 'searchwp' ); ?>

									</button>

								</div>

							</div>

							<div id="swp-sfe-phpcode-desc" class="swp-sfe-embed--desc" style="display: none;">

								<p class="swp-desc swp-leading--160 swp-margin-b15">
									<?php esc_html_e( 'Use the following PHP code anywhere in your post to display created field', 'searchwp' ); ?>
								</p>

								<div class="swp-flex--row swp-flex--gap9 sm:swp-flex--col sm:swp-flex--gap30">

									<input type="text" id="swp-sfe-phpcode-desc-input" class="swp-input swp-input--embed" value="&lt;?php echo \SearchWP\Forms\Frontend::render( [ 'id' => <?php echo absint( $form_id ); ?> ] ); ?&gt;" disabled>

									<button class="swp-button swp-button--flex-content" type="button" data-swp-copy-from="#swp-sfe-phpcode-desc-input">

										<svg width="13" height="15" viewBox="0 0 13 15" fill="none" xmlns="http://www.w3.org/2000/svg">
											<path d="M9.3125 0.625H1.8125C1.125 0.625 0.5625 1.1875 0.5625 1.875V10.625H1.8125V1.875H9.3125V0.625ZM11.1875 3.125H4.3125C3.625 3.125 3.0625 3.6875 3.0625 4.375V13.125C3.0625 13.8125 3.625 14.375 4.3125 14.375H11.1875C11.875 14.375 12.4375 13.8125 12.4375 13.125V4.375C12.4375 3.6875 11.875 3.125 11.1875 3.125ZM11.1875 13.125H4.3125V4.375H11.1875V13.125Z" fill="#0E2121" fill-opacity="0.8"/>
										</svg>

										<?php esc_html_e( 'Copy', 'searchwp' ); ?>

									</button>

								</div>

							</div>

						</div>

					</div>

				</div>

				<div class="swp-flex--row">
					
					<div class="swp-col swp-col--title-width">

						<h3 class="swp-h3">
							<?php esc_html_e( 'Embed Terms', 'searchwp' ); ?>
						</h3>

					</div>

					<div class="swp-col">

						<p class="swp-desc--top">
							<?php esc_html_e( 'Would you like to embed your search form in an existing page, or create a new one?', 'searchwp' ); ?>
						</p>

						<div class="swp-flex--row swp-flex--align-c swp-flex--gap15 sm:swp-flex--col sm:swp-flex--align-start">

							<button class="swp-button" type="button" data-swp-modal="#swp-search-form-embed-existing-page-modal">
								<?php esc_html_e( 'Select Existing Page', 'searchwp' ); ?>
							</button>

							<span class="swp-desc">
								<?php esc_html_e( 'Or', 'searchwp' ); ?>
							</span>

							<button class="swp-button" type="button" data-swp-modal="#swp-search-form-embed-new-page-modal">
								<?php esc_html_e( 'Create New Page', 'searchwp' ); ?>
							</button>

						</div>

					</div>
				
				</div>

			</div>

		</div>


		<div id="swp-search-form-embed-existing-page-modal" class="swp-modal swp-modal--centered swp-modal-l" style="display:none;">

			<div class="swp-modal--header swp-bg--gray">

				<div class="swp-flex--row swp-justify-between swp-flex--align-c">

					<h1 class="swp-h1 swp-font-size16">
						<?php esc_html_e( 'Embed in a Page', 'searchwp' ); ?>
					</h1>

					<button class="swp-modal--close">
						<svg width="16" height="15" viewBox="0 0 16 15" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path opacity="0.5" d="M16 1.49633L14.3886 0L8 5.93224L1.61143 0L0 1.49633L6.38857 7.42857L0 13.3608L1.61143 14.8571L8 8.9249L14.3886 14.8571L16 13.3608L9.61143 7.42857L16 1.49633Z" fill="#646970"></path>
						</svg>
					</button>

				</div>

			</div>

			<div class="swp-modal--content swp-margin-b50">

				<p class="swp-p swp-margin-b25">
					<?php esc_html_e( 'Select the page you would like to embed your search form in.', 'searchwp' ); ?>
				</p>

				<hr class="swp-hr swp-margin-b25">

				<div class="swp-flex--row swp-margin-b60">
					
					<div class="swp-col swp-col--title-width">

						<h3 class="swp-h3">
							<?php esc_html_e( 'Select Existing Page', 'searchwp' ); ?>
						</h3>

					</div>

					<div class="swp-col">

						<div class="swp-flex--row swp-flex--gap9 sm:swp-flex--col sm:swp-flex--gap30">

							<div class="swp-input--embed">
								<?php $embed_pages_data = self::get_embed_pages_data(); ?>
								<select id="swp-search-form-embed-existing-page-modal-select"<?php echo ! empty( $embed_pages_data['use_ajax'] ) ? ' data-use-ajax=1' : ''; ?>>
									<option value=""><?php esc_html_e( 'Select a Page', 'searchwp' ); ?></option>
									<?php foreach ( $embed_pages_data['pages'] as $embed_page ) : ?>
										<option value="<?php echo absint( $embed_page->ID ); ?>"><?php echo esc_html( $embed_page->post_title ); ?></option>
									<?php endforeach; ?>
								</select>
							</div>

							<button class="swp-search-form-embed-modal-go-btn swp-button swp-button--green" type="button" data-action="select-page">
								<?php esc_html_e( 'Let’s Go!', 'searchwp' ); ?>
							</button>
							
						</div>

						<p class="swp-p swp-margin-t30">
							<a href="#" data-swp-modal="#swp-search-form-embed-modal">
								&larr;&nbsp;<?php esc_html_e( 'Go Back', 'searchwp' ); ?>
							</a>
						</p>

					</div>
				</div>

			</div>

		</div>


		<div id="swp-search-form-embed-new-page-modal" class="swp-modal swp-modal--centered swp-modal-l" style="display:none">

			<div class="swp-modal--header swp-bg--gray">

				<div class="swp-flex--row swp-justify-between swp-flex--align-c">

					<h1 class="swp-h1 swp-font-size16">
						<?php esc_html_e( 'Embed in a Page', 'searchwp' ); ?>
					</h1>

					<button class="swp-modal--close">
						<svg width="16" height="15" viewBox="0 0 16 15" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path opacity="0.5" d="M16 1.49633L14.3886 0L8 5.93224L1.61143 0L0 1.49633L6.38857 7.42857L0 13.3608L1.61143 14.8571L8 8.9249L14.3886 14.8571L16 13.3608L9.61143 7.42857L16 1.49633Z" fill="#646970"></path>
						</svg>
					</button>

				</div>

			</div>

			<div class="swp-modal--content swp-margin-b50">

				<p class="swp-p swp-margin-b25">
					<?php esc_html_e( 'What would you like to call the new page?', 'searchwp' ); ?>
				</p>

				<hr class="swp-hr swp-margin-b25">

				<div class="swp-flex--row swp-margin-b60">
					
					<div class="swp-col swp-col--title-width">

						<h3 class="swp-h3">
							<?php esc_html_e( 'Create New Page', 'searchwp' ); ?>
						</h3>

					</div>

					<div class="swp-col">

						<div class="swp-flex--row swp-flex--gap9 sm:swp-flex--col sm:swp-flex--gap30">

							<input id="swp-search-form-embed-new-page-modal-page-title" class="swp-input swp-input--embed" type="text" placeholder="<?php esc_html_e( 'Name your page', 'searchwp' ); ?>">

							<button class="swp-search-form-embed-modal-go-btn swp-button swp-button--green" type="button" data-action="create-page">
								<?php esc_html_e( 'Let’s Go!', 'searchwp' ); ?>
							</button>
							
						</div>

						<p class="swp-p swp-margin-t30">
							<a href="#" data-swp-modal="#swp-search-form-embed-modal">
								&larr;&nbsp;<?php esc_html_e( 'Go Back', 'searchwp' ); ?>
							</a>
						</p>

					</div>
				</div>

			</div>

		</div>

		<div class="swp-modal--bg"></div>
		<?php
	}

	/**
	 * Save form settings AJAX callback.
	 *
	 * @since 4.3.2
	 */
	public static function save_form_settings_ajax() {

		Utils::check_ajax_permissions();

		if ( ! isset( $_POST['form_id'], $_POST['settings'] ) ) {
			wp_send_json_error();
		}

		$settings = json_decode( wp_unslash( $_POST['settings'] ), true );

		$settings['id'] = absint( $_POST['form_id'] );

		Storage::update( $settings['id'], $settings );

		wp_send_json_success();
	}

	/**
	 * Get embed page URL via AJAX.
	 *
	 * @since 4.3.2
	 */
	public static function get_embed_page_url_ajax() {

		Utils::check_ajax_permissions();

		$page_id = ! empty( $_POST['pageId'] ) ? absint( $_POST['pageId'] ) : 0;

		if ( ! empty( $page_id ) ) {
			$url  = get_edit_post_link( $page_id, '' );
			$meta = [
				'embed_page' => $page_id,
			];
		} else {
			$url  = add_query_arg( 'post_type', 'page', admin_url( 'post-new.php' ) );
			$meta = [
				'embed_page'       => 0,
				'embed_page_title' => ! empty( $_POST['pageTitle'] ) ? sanitize_text_field( wp_unslash( $_POST['pageTitle'] ) ) : '',
			];
		}

		$meta['form_id'] = ! empty( $_POST['formId'] ) ? absint( $_POST['formId'] ) : 0;

		update_user_meta( get_current_user_id(), 'searchwp_admin_form_embed_wizard', $meta );

		wp_send_json_success( $url );
	}

	/**
	 * Search pages by search term and return an array containing
	 * `value` and `label` which is the post ID and post title respectively.
	 *
	 * @since 4.3.2
	 *
	 * @param string $search_term The search term.
	 * @param array  $args        Optional. An array of arguments.
	 *
	 * @return array
	 */
	public static function get_search_result_pages_ajax() {

		Utils::check_ajax_permissions();

		if ( ! array_key_exists( 'search', $_GET ) ) {
			wp_send_json_error( [ 'msg' => esc_html__( 'Incorrect usage of this operation.', 'searchwp' ) ] );
		}

		$search_results = self::search_posts(
			sanitize_text_field( wp_unslash( $_GET['search'] ) ),
			[
				'count'       => 20,
				'post_status' => [ 'publish', 'pending' ],
			]
		);
		$result_pages = [];

		// Prepare for ChoicesJS render.
		foreach ( $search_results as $search_result ) {
			$result_pages[] = [
				'value' => absint( $search_result->ID ),
				'label' => esc_html( $search_result->post_title ),
			];
		}

		if ( empty( $result_pages ) ) {
			wp_send_json_error( [] );
		}

		wp_send_json_success( $result_pages );
	}

	/**
	 * Search for posts editable by user.
	 *
	 * @since 4.3.2
	 *
	 * @param string $search_term Optional search term. Default ''.
	 * @param array  $args        Args {
	 *                            Optional. An array of arguments.
	 *
	 * @type string   $post_type   Post type to search for.
	 * @type string[] $post_status Post status to search for.
	 * @type int      $count       Number of results to return. Default 20.
	 * }
	 *
	 * @return array
	 */
	private static function search_posts( $search_term = '', $args = [] ) {

		global $wpdb;

		$default_args = [
			'post_type'   => 'page',
			'post_status' => [ 'publish' ],
			'count'       => 20,
		];
		$args         = wp_parse_args( $args, $default_args );

		// @todo: add trash access capabilities to MySQL.
		// See edit_post/edit_page case in map_meta_cap().
		$args['post_status'] = array_diff( $args['post_status'], [ 'trash' ] );

		$user      = wp_get_current_user();
		$user_id   = $user ? $user->ID : 0;
		$post_type = get_post_type_object( $args['post_type'] );

		if ( ! $user_id || ! $post_type || $args['count'] <= 0 ) {
			return [];
		}

		$last_changed = wp_cache_get_last_changed( 'posts' );
		$key          = __FUNCTION__ . ":$search_term:$last_changed";
		$cache_posts  = wp_cache_get( $key, '', false, $found );

		if ( $found ) {
			return $cache_posts;
		}

		$post_title_where = $search_term ? $wpdb->prepare(
			'post_title LIKE %s AND',
			'%' . $wpdb->esc_like( $search_term ) . '%'
		) :
			'';

		$post_statuses              = array_intersect( array_keys( get_post_statuses() ), $args['post_status'] );
		$post_statuses              = self::wpdb_prepare_in( $post_statuses );
		$policy_id                  = (int) get_option( 'wp_page_for_privacy_policy' );
		$can_delete_published_posts = (int) $user->has_cap( $post_type->cap->delete_published_posts );
		$can_delete_posts           = (int) $user->has_cap( $post_type->cap->delete_posts );
		$can_delete_others_posts    = (int) $user->has_cap( $post_type->cap->delete_others_posts );
		$can_delete_private_posts   = (int) $user->has_cap( $post_type->cap->delete_private_posts );
		$can_edit_policy            = (int) $user->has_cap( map_meta_cap( 'manage_privacy_options', $user_id )[0] );

		// For the case when user is post author.
		$capability_author_where = "post_author = $user_id AND
		( ( post_status IN ( 'publish', 'future' ) AND $can_delete_published_posts ) OR
		( ( post_status NOT IN ( 'publish', 'future', 'trash' ) ) AND $can_delete_posts )
		)";

		// For the case when accessing someone other's post.
		$capability_other_where = "post_author != $user_id AND
		$can_delete_others_posts AND
		( ( post_status IN ( 'publish', 'future' ) AND $can_delete_published_posts ) OR
		( ( post_status IN ( 'private' ) ) AND $can_delete_private_posts )
		)";

		// For privacy policy page.
		$capability_policy_where = "ID = $policy_id AND $can_edit_policy";

		$capability_where = '( ' .
		                    '(' . $capability_author_where . ') OR ' .
		                    '(' . $capability_other_where . ') OR ' .
		                    '(' . $capability_policy_where . ')' .
		                    ' )';

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$posts = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT ID, post_title, post_author
					FROM $wpdb->posts
					WHERE $post_title_where
					post_type = '{$args['post_type']}' AND
					post_status IN ( $post_statuses ) AND
					$capability_where
					ORDER BY post_title LIMIT %d",
				absint( $args['count'] )
			)
		);
		// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		$posts = $posts ? $posts : [];
		$posts = array_map(
			static function ( $post ) {
				$post->post_title = self::get_post_title( $post );

				unset( $post->post_author );

				return $post;
			},
			$posts
		);

		wp_cache_set( $key, $posts );

		return $posts;
	}

	/**
	 * Changes array of items into string of items, separated by comma and sql-escaped.
	 *
	 * @see https://coderwall.com/p/zepnaw
	 *
	 * @since 4.3.2
	 *
	 * @param mixed|array $items  Item(s) to be joined into string.
	 * @param string      $format Can be %s or %d.
	 *
	 * @return string Items separated by comma and sql-escaped.
	 */
	private static function wpdb_prepare_in( $items, $format = '%s' ) {

		global $wpdb;

		$items    = (array) $items;
		$how_many = count( $items );

		if ( $how_many === 0 ) {
			return '';
		}

		$placeholders    = array_fill( 0, $how_many, $format );
		$prepared_format = implode( ',', $placeholders );

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		return $wpdb->prepare( $prepared_format, $items );
	}

	/**
	 * Get sanitized post title or "no title" placeholder.
	 *
	 * The placeholder is prepended with post ID.
	 *
	 * @since 4.3.2
	 *
	 * @param WP_Post|object $post Post object.
	 *
	 * @return string Post title.
	 */
	private static function get_post_title( $post ) {

		/* translators: %d - a post ID. */
		return self::is_empty_string( trim( $post->post_title ) ) ? sprintf( __( '#%d (no title)', 'searchwp' ), absint( $post->ID ) ) : $post->post_title;
	}

	/**
	 * Check if a string is empty.
	 *
	 * @since 4.3.2
	 *
	 * @param string $string String to test.
	 *
	 * @return bool
	 */
	private static function is_empty_string( $string ) {

		return is_string( $string ) && $string === '';
	}

	/**
	 * Get pages data for the "Existing Page" select inside the Embed modal.
	 *
	 * @since 4.3.2
	 */
	private static function get_embed_pages_data() {

		$page_statuses  = [ 'publish', 'pending' ];
		$max_page_count = 20;

		$embed_pages = self::search_posts(
			'',
			[
				'count'       => $max_page_count,
				'post_status' => $page_statuses,
			]
		);

		if ( empty( $embed_pages ) ) {
			return [
				'pages'    => [],
				'use_ajax' => false,
			];
		}

		$total_pages    = 0;
		$wp_count_pages = (array) wp_count_posts( 'page' );

		foreach ( $wp_count_pages as $page_status => $pages_count ) {
			if ( in_array( $page_status, [ 'publish', 'pending' ], true ) ) {
				$total_pages += $pages_count;
			}
		}

		return [
			'pages'    => $embed_pages,
			'use_ajax' => $total_pages > $max_page_count,
		];
	}

	/**
	 * Set default title for the new page.
	 *
	 * @since 4.3.2
	 *
	 * @param string   $post_title Default post title.
	 * @param \WP_Post $post       Post object.
	 *
	 * @return string New default post title.
	 */
	public static function embed_page_title( $post_title, $post ) {

		$meta = get_user_meta( get_current_user_id(), 'searchwp_admin_form_embed_wizard', true );

		delete_user_meta( get_current_user_id(), 'searchwp_admin_form_embed_wizard' );

		return empty( $meta['embed_page_title'] ) ? $post_title : $meta['embed_page_title'];
	}

	/**
	 * Embed the form to the new page.
	 *
	 * @since 4.3.2
	 *
	 * @param string   $post_content Default post content.
	 * @param \WP_Post $post         Post object.
	 *
	 * @return string Embedding string (shortcode or GB component code).
	 */
	public static function embed_page_content( $post_content, $post ) {

		$meta = get_user_meta( get_current_user_id(), 'searchwp_admin_form_embed_wizard', true );

		$form_id = ! empty( $meta['form_id'] ) ? $meta['form_id'] : 0;
		$page_id = ! empty( $meta['embed_page'] ) ? $meta['embed_page'] : 0;

		if ( ! empty( $page_id ) || empty( $form_id ) ) {
			return $post_content;
		}

		if ( self::is_gutenberg_active() ) {
			$pattern = '<!-- wp:searchwp/search-form {"id":%d} /-->';
		} else {
			$pattern = '[searchwp_form id="%d"]';
		}

		return sprintf( $pattern, absint( $form_id ) );
	}

	/**
	 * Check if Gutenberg is active.
	 *
	 * @since 4.3.2
	 *
	 * @return bool True if Gutenberg is active.
	 */
	private static function is_gutenberg_active() {

		$gutenberg    = false;
		$block_editor = false;

		if ( has_filter( 'replace_editor', 'gutenberg_init' ) ) {
			// Gutenberg is installed and activated.
			$gutenberg = true;
		}

		if ( version_compare( $GLOBALS['wp_version'], '5.0-beta', '>' ) ) {
			// Block editor.
			$block_editor = true;
		}

		if ( ! $gutenberg && ! $block_editor ) {
			return false;
		}

		include_once ABSPATH . 'wp-admin/includes/plugin.php';

		if ( is_plugin_active( 'disable-gutenberg/disable-gutenberg.php' ) ) {
			return ! disable_gutenberg();
		}

		if ( is_plugin_active( 'classic-editor/classic-editor.php' ) ) {
			return get_option( 'classic-editor-replace' ) === 'block';
		}

		return true;
	}
}
