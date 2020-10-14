<?php

/**
 * Class SearchWP_Redirects_Settings
 *
 * This class powers the settings screen UI within the SearchWP settings screen
 */
class SearchWP_Redirects_Settings {

	public $public                = true;
	public $slug                  = 'redirects';
	public $name                  = 'Redirects';
	public $min_searchwp_version  = '2.8.7';

	private $url;
	private $prefix = 'searchwp_redirects_';
	private $settings = array();

	/**
	 * Settings constructor.
	 */
	function __construct() {
		$this->url = plugins_url( 'searchwp-redirects' );
	}

	/**
	 * Initializer
	 */
	function init() {
		add_filter( 'searchwp\extensions', array( $this, 'register' ), 10 );
		add_filter( 'searchwp_extensions', array( $this, 'register' ), 10 );
		add_action( 'admin_enqueue_scripts', array( $this, 'assets' ), 999 );
	}

	/**
	 * Validate settings array against expected values
	 *
	 * @param $dirty_settings
	 *
	 * @return array
	 */
	function validate( $dirty_settings ) {
		$settings = array();

		if ( ! empty( $dirty_settings ) ) {
			foreach ( $dirty_settings as $group => $values ) {
				switch ( $group ) {
					case 'redirects':
						$settings['redirects'] = $this->validate_redirects( $values );
						break;
				}
			}
		}

		return $settings;
	}

	/**
	 * Validate redirects
	 *
	 * @param $redirects
	 *
	 * @return array
	 */
	function validate_redirects( $redirects ) {
		$clean_redirects = array();

		foreach ( $redirects as $redirect_details ) {
			if ( ! isset( $redirect_details['query'] ) ) {
				continue;
			}

			if ( ! isset( $redirect_details['redirect'] ) ) {
				continue;
			}

			$engines = null;

			if ( isset( $redirect_details['engines'] ) ) {

				// Force an array
				if ( ! is_array( $redirect_details['engines'] ) ) {
					$redirect_details['engines'] = array( $redirect_details['engines'] );
				}

				$engines = array();

				foreach ( $redirect_details['engines'] as $engine ) {
					if ( function_exists( 'SWP' ) ) {
						if ( SWP()->is_valid_engine( $engine ) ) {
							$engines[] = $engine;
						}
					} else if ( class_exists( '\\SearchWP\\Settings' ) ) {
						$engine_config = \SearchWP\Settings::get_engine_settings( $engine );
						if ( $engine_config ) {
							$engines[] = $engine;
						}
					}
				}
			}

			$clean_redirects[] = array(
				'query'     => sanitize_text_field( $redirect_details['query'] ),
				'partial'   => isset( $redirect_details['partial'] ) && ! empty( $redirect_details['partial'] ),
				'engines'   => $engines,
				'redirect'  => $this->normalize_url( $redirect_details['redirect'] ),
			);
		}

		// usort( $clean_redirects, array( $this, 'sort_redirects' ) );

		return $clean_redirects;
	}

	function sort_redirects( $a, $b ) {
		return strcmp( $a['query'], $b['query'] );
	}


	/**
	 * Check for settings update, validate, save
	 */
	private function maybe_update_settings() {
		if ( ! isset( $_POST['searchwp_redirects_nonce'] ) ) {
			return;
		}

		if ( isset( $_POST['searchwp_redirects_nonce'] ) && ! wp_verify_nonce( $_POST['searchwp_redirects_nonce'], 'searchwp_redirects_settings' ) ) {
			return;
		}

		$settings = ! empty( $_POST['searchwp_redirects_settings'] ) ? stripslashes_deep( $_POST['searchwp_redirects_settings'] ) : array();

		// Did an import take place?
		$import = ! empty( $_POST['searchwp_redirects_import'] ) ? json_decode( stripslashes_deep( $_POST['searchwp_redirects_import'] ), true ) : array();
		if ( is_array( $import ) && count( $import ) ) {

			// You can only save settings or import, so we need to retrieve the existing redirects.
			$settings = $this->get();
			$redirects = isset( $settings['redirects'] ) ? $settings['redirects'] : array();

			$settings['redirects'] = array_merge(
				(array) $redirects,
				(array) $import
			);
		}

		$this->settings = $this->validate( $settings );

		update_option( 'searchwp_redirects', $this->settings );

		?>
		<div class="notice notice-success is-dismissible searchwp-notice-persist">
			<p><?php esc_html_e( 'Saved', 'searchwp_redirects' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Getter for settings
	 *
	 * @return array
	 */
	public function get() {
		if ( empty( $this->settings ) ) {
			$this->settings = $this->validate( searchwp_redirects_get_settings() );
		}

		return $this->settings;
	}

	/**
	 * Normalize URLs to not include a scheme/host
	 *
	 * @param $dirty_url
	 *
	 * @return string
	 */
	function normalize_url( $dirty_url ) {
		if ( false === strpos( $dirty_url, home_url() ) ) {
			$dirty_url = home_url( $dirty_url );
		}

		$parsed_url = wp_parse_url( $dirty_url );

		// If home_url() has a slash in it, then WordPress is in a subdirectory and $parsed_url['path'] is inaccurate
		$parsed_home_url = wp_parse_url( home_url() );
		if ( ! empty( $parsed_url['path'] ) && ! empty( $parsed_home_url['path'] ) ) {
			// Prevent redundant inclusion of path
			$parsed_url['path'] = substr( $parsed_url['path'], strlen( $parsed_home_url['path'] ) );
		}

		$url = untrailingslashit( home_url() ) . $parsed_url['path'];

		if ( ! empty( $parsed_url['query'] ) ) {
			$url .= '?' . $parsed_url['query'];
		}

		if ( ! empty( $parsed_url['fragment'] ) ) {
			$url .= '#' . $parsed_url['fragment'];
		}

		$url = str_replace( home_url(), '', $url );

		return apply_filters( 'searchwp_redirects_url', $url );
	}

	/**
	 * Output the view for the settings screen
	 */
	function view() {
		$action_url = add_query_arg( array(
			'page'      => 'searchwp',
			'tab'       => 'extensions',
			'extension' => 'redirects',
		), admin_url( 'options-general.php' ) );

		$this->maybe_update_settings();

		$this->settings = $this->get();

		$redirects = isset( $this->settings['redirects'] ) ? $this->settings['redirects'] : array();

		$nonce_field = wp_nonce_field( 'searchwp_redirects_settings', 'searchwp_redirects_nonce', true, false );
		?>
		<div class="searchwp-redirects-settings">

			<p><?php echo wp_kses(
				__(
					'Manage Redirects by defining a search query, applicable engine(s), and destination URL.',
					'searchwp_redirects'
				),
				array(
					'strong' => array()
				)
			); ?></p>

			<div class="searchwp-redirects-migration">
				<div class="searchwp-redirects-migrate">
					<div class="searchwp-redirects-export">
						<h4><?php echo esc_html_e( 'Export', 'searchwp_redirects' ); ?></h4>
						<textarea><?php echo esc_textarea( wp_json_encode( $redirects ) ); ?></textarea>
					</div>
					<div class="searchwp-redirects-import">
						<h4><?php echo esc_html_e( 'Import', 'searchwp_redirects' ); ?></h4>
						<form action="<?php echo esc_url( $action_url ); ?>" method="POST">
							<?php echo $nonce_field; ?>
							<div><textarea name="searchwp_redirects_import"></textarea></div>
							<p>
								<button class="button" type="submit"><?php echo esc_html_e( 'Import Redirects', 'searchwp_redirects' ); ?></button>
							</p>
						</form>
					</div>
				</div>
			</div>

			<style type="text/css">
				.searchwp-redirects-migration {
					display: none;
				}

				.searchwp-redirects-migrate {
					display: flex;
					justify-content: space-between;
				}

				.searchwp-redirects-migrate > * {
					width: 48.5%;
				}

				.searchwp-redirects-migrate textarea {
					display: block;
					width: 100%;
					font-family: monospace;
					height: 8em;
					resize: none;
				}
			</style>

			<script type="text/javascript">
				jQuery(document).ready(function(){
					jQuery('.searchwp-redirects-migration').before('<p><button class="button" id="searchwp-redirects-migrate-toggle"><?php echo esc_html_e( 'Toggle Import/Export', 'searchwp_redirects' ); ?></button></p>');

					jQuery('#searchwp-redirects-migrate-toggle').click(function(e){
						e.preventDefault();
						jQuery('.searchwp-redirects-migration').toggle();
						jQuery('#searchwp-redirects-list').toggle();
					});
				});
			</script>

			<form id="searchwp-redirects-list" action="<?php echo esc_url( $action_url ); ?>" method="POST">
				<?php echo $nonce_field; ?>

				<table class="searchwp-redirects">
					<colgroup>
						<col id="searchwp-redirects-col-query" />
						<col id="searchwp-redirects-col-engines" />
						<col id="searchwp-redirects-col-destination" />
					</colgroup>
					<thead>
						<tr>
							<th><?php esc_html_e( 'Query', 'searchwp_redirects' ); ?></th>
							<th><?php esc_html_e( 'Engine(s)', 'searchwp_redirects' ); ?></th>
							<th><?php esc_html_e( 'Redirect', 'searchwp_redirects' ); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php if ( ! empty( $redirects ) && is_array( $redirects ) ) : ?>
						<?php foreach ( $redirects as $redirect ) : $array_flag = uniqid( 'searchwp_redirects' ); ?>
							<tr>
								<td>
									<div class="searchwp-flexible">
										<div><span class="dashicons dashicons-menu searchwp-handle"></span></div>
										<a href="#" class="searchwp-redirect-delete">&times;</a>
										<input type="text" class="searchwp-redirect-input" id="searchwp_redirect_<?php echo esc_attr( $array_flag ); ?>_query" name="<?php echo esc_attr( $this->prefix ); ?>settings[redirects][<?php echo esc_attr( $array_flag ); ?>][query]" value="<?php echo esc_attr( $redirect['query'] ); ?>" />
										<div class="searchwp-redirects-partial searchwp-flexible">
											<input type="checkbox" id="searchwp_redirects_<?php echo esc_attr( $array_flag ); ?>_partial" name="<?php echo esc_attr( $this->prefix ); ?>settings[redirects][<?php echo esc_attr( $array_flag ); ?>][partial]" value="1"<?php if ( ! empty( $redirect['partial'] ) ) : ?>checked="checked"<?php endif; ?> />
											<label for="searchwp_redirects_<?php echo esc_attr( $array_flag ); ?>_partial"><?php esc_html_e( 'Partial Match', 'searchwp_redirects' ); ?></label>
										</div>
									</div>
								</td>
								<td>
									<select style="width: 90%;" name="<?php echo esc_attr( $this->prefix ); ?>settings[redirects][<?php echo esc_attr( $array_flag ); ?>][engines][]" class="searchwp-redirect-engines" multiple="multiple">
										<?php
										if ( function_exists( 'SWP' ) ) {
											$engines = SWP()->settings['engines'];
										} else if ( class_exists( '\\SearchWP\\Settings' ) ) {
											$engines = \SearchWP\Settings::get_engines();
										}

										foreach( $engines as $engine => $engine_settings ) : ?>
											<option value="<?php echo esc_attr( $engine ); ?>"
													<?php if ( is_array( $redirect['engines'] ) && in_array( $engine, $redirect['engines'], true ) ) : ?>
													selected="selected"
													<?php endif; ?>
											>
												<?php
												if ( is_array( $engine_settings ) && isset( $engine_settings['searchwp_engine_label'] ) ) {
													echo esc_html( $engine_settings['searchwp_engine_label'] );
												} else {
													if ( class_exists( '\\SearchWP\\Engine' ) && $engine_settings instanceof SearchWP\Engine ) {
														echo esc_html( $engine_settings->get_label() );
													} else {
														esc_html_e( 'Default', 'searchwp' );
													}
												}
												?>
											</option>
										<?php endforeach; ?>
									</select>
								</td>
								<td>
									<input type="text" class="searchwp-redirect-input" id="searchwp_redirects_<?php echo esc_attr( $array_flag ); ?>_redirect" name="<?php echo esc_attr( $this->prefix ); ?>settings[redirects][<?php echo esc_attr( $array_flag ); ?>][redirect]" value="<?php echo esc_attr( $this->normalize_url( $redirect['redirect'] ) ); ?>" />
								</td>
							</tr>
						<?php endforeach; endif; ?></tbody>
				</table>
				<p>
					<a class="button searchwp-add-redirect" href="#"><?php esc_html_e( 'Add Redirect', 'searchwp_redirects' ); ?></a>
				</p>
				<p class="submit">
					<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_html_e( 'Save Redirects', 'searchwp_redirects' ); ?>" />
				</p>
			</form>

			<script type="text/html" id="tmpl-searchwp-redirects">
				<tr>
					<td>
						<div class="searchwp-flexible">
							<div><span class="dashicons dashicons-menu searchwp-handle"></span></div>
							<a href="#" class="searchwp-redirect-delete">&times;</a>
							<input type="text" class="searchwp-redirect-input" id="searchwp_redirect_{{ searchwp_redirects.arrayFlag }}_query" name="<?php echo esc_attr( $this->prefix ); ?>settings[redirects][{{ searchwp_redirects.arrayFlag }}][query]" value="" />
							<div class="searchwp-redirects-partial searchwp-flexible">
								<input type="checkbox" id="searchwp_redirects_{{ searchwp_redirects.arrayFlag }}_partial" name="<?php echo esc_attr( $this->prefix ); ?>settings[redirects][{{ searchwp_redirects.arrayFlag }}][partial]" value="1" />
								<label for="searchwp_redirects_{{ searchwp_redirects.arrayFlag }}_partial"><?php esc_html_e( 'Partial Match', 'searchwp_redirect' ); ?></label>
							</div>
						</div>
					</td>
					<td>
						<select style="width: 90%;" name="<?php echo esc_attr( $this->prefix ); ?>settings[redirects][{{ searchwp_redirects.arrayFlag }}][engines][]" class="searchwp-redirect-engines" multiple="multiple">
							<?php
							if ( function_exists( 'SWP' ) ) {
								$engines = SWP()->settings['engines'];
							} else if ( class_exists( '\\SearchWP\\Settings' ) ) {
								$engines = \SearchWP\Settings::get_engines();
							}

							foreach( $engines as $engine => $engine_settings ) : ?>
								<option value="<?php echo esc_attr( $engine ); ?>">
									<?php
									if ( is_array( $engine_settings ) && isset( $engine_settings['searchwp_engine_label'] ) ) {
										echo esc_html( $engine_settings['searchwp_engine_label'] );
									} else {
										if ( class_exists( '\\SearchWP\\Engine' ) && $engine_settings instanceof SearchWP\Engine ) {
											echo esc_html( $engine_settings->get_label() );
										} else {
											esc_html_e( 'Default', 'searchwp' );
										}
									}
									?>
								</option>
							<?php endforeach; ?>
						</select>
					</td>
					<td>
						<input type="text" class="searchwp-redirect-input" id="searchwp_redirects_{{ searchwp_redirects.arrayFlag }}_redirect" name="<?php echo esc_attr( $this->prefix ); ?>settings[redirects][{{ searchwp_redirects.arrayFlag }}][redirect]" value="" placeholder="<?php echo esc_attr_e( 'Exclude ', 'searchwp_redirects' ) . esc_attr( home_url() ); ?>" />
					</td>
				</tr>
			</script>

		</div>
		<?php
	}

	/**
	 * Output an <input>
	 *
	 * @param string $section
	 * @param string $name
	 * @param string $label
	 * @param bool $hidden_label
	 * @param string $type
	 * @param string $value
	 * @param bool $checked
	 */
	public function input( $section = '', $name = '', $label = '', $hidden_label = true, $type = 'text', $value = '', $checked = false ) {
		?>
		<input type="<?php echo esc_attr( $type ); ?>"
			   value="<?php echo esc_attr( $value ); ?>"
			   name="searchwp_redirects[<?php echo esc_attr( $section ); ?>][<?php echo esc_attr( $name ); ?>]"
			   id="searchwp_redirects[<?php echo esc_attr( $section ); ?>][<?php echo esc_attr( $name ); ?>]"
			<?php if ( ! empty( $checked ) ) : ?>
				checked="checked"
			<?php endif; ?>
		>
		<?php if ( ! empty( $hidden_label ) ) : ?>
			<div class="screen-reader-text">
		<?php endif; ?>
		<label for="searchwp_redirects[<?php echo esc_attr( $section ); ?>][<?php echo esc_attr( $name ); ?>]"><?php echo esc_html( $label ); ?></label>
		<?php if ( ! empty( $hidden_label ) ) : ?>
			</div>
		<?php endif; ?>
		<?php
	}

	/**
	 * Callback for SearchWP Extension register
	 *
	 * @param $extensions
	 *
	 * @return mixed
	 */
	function register( $extensions ) {

		// When instantiating, SearchWP core forces a prefix of 'SearchWP' and it needs
		// to match the name of this class right here, so we need to get creative :boo:
		$extensions['_Redirects_Settings'] = __FILE__;

		return $extensions;
	}

	/**
	 * Enqueue assets callback
	 *
	 * @param $hook
	 */
	function assets( $hook ) {
		wp_register_script(
			'searchwp_redirects_select2_js',
			trailingslashit( $this->url ) . 'assets/vendor/select2/js/select2.min.js',
			array( 'jquery' ),
			'4.0.3'
		);

		wp_register_script(
			'searchwp_redirects_js',
			trailingslashit( $this->url ) . 'assets/script.js',
			array( 'jquery', 'underscore', 'searchwp_redirects_select2_js' ),
			SEARCHWP_REDIRECTS_VERSION
		);

		wp_register_style(
			'searchwp_redirects_select2_css',
			trailingslashit( $this->url ) . 'assets/vendor/select2/css/select2.min.css',
			false,
			'4.0.3'
		);

		wp_register_style(
			'searchwp_redirects_css',
			trailingslashit( $this->url ) . 'assets/style.css',
			false,
			SEARCHWP_REDIRECTS_VERSION
		);

		if ( 'settings_page_searchwp' === $hook && isset( $_GET['extension'] ) && $_GET['extension'] === $this->slug ) {
			wp_enqueue_script( 'underscore' );
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'jquery-ui-sortable' );
			wp_enqueue_script( 'searchwp_redirects_select2_js' );
			wp_enqueue_script( 'searchwp_redirects_js' );

			wp_enqueue_style( 'searchwp_redirects_select2_css' );
			wp_enqueue_style( 'searchwp_redirects_css' );
		}
	}
}
