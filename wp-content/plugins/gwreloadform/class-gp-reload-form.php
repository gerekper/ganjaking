<?php

if ( ! class_exists( 'GP_Plugin' ) ) {
	return;
}

class GP_Reload_Form extends GP_Plugin {

	private static $_instance = null;

	protected $_version     = GP_RELOAD_FORM_VERSION;
	protected $_path        = 'gwreloadform/gwreloadform.php';
	protected $_full_path   = __FILE__;
	protected $_slug        = 'gp-reload-form';
	protected $_title       = 'Gravity Forms Reload Form';
	protected $_short_title = 'Reload Form';

	protected $min_gf_version = '1.7.10.1';

	public function minimum_requirements() {
		return array(
			'wordpress'    => array(
				'version' => '4.6.2',
			),
			'php'          => array(
				'version' => '5.3',
			),
			'gravityforms' => array(
				'version' => '1.7.10.1',
			),
		);
	}

	public static function get_instance() {

		if ( self::$_instance === null ) {
			self::$_instance = isset( self::$perk ) ? new self( new self::$perk ) : new self();
		}

		return self::$_instance;
	}

	public function pre_init() {
		parent::pre_init();

		add_filter( 'gform_pre_form_settings_save', array( $this, 'save_our_form_settings' ) );

		add_filter( 'gform_form_settings_fields', array( $this, 'add_form_settings_fields' ) );

	}

	public function init() {
		parent::init();

		// Changed priority from "1" to "5"; not sure why this wasn't set to 10. Best guess is we want our init script
		// to run before any other script has run so the markup has been unchanged by any scripting. This will not be
		// necessary long-term given our move to getting fresh markup from the submission.
		add_filter( 'gform_pre_render', array( $this, 'register_init_scripts' ), 5, 2 ); // use pre render so we can init our functions first
		add_filter( 'gform_admin_pre_render', array( $this, 'queue_merge_tag_support' ) );
		add_filter( 'gform_replace_merge_tags', array( $this, 'reload_form_replace_merge_tag' ), 10, 2 );

		add_filter( 'gform_confirmation', array( $this, 'append_form_markup' ), 9999, 4 );

		/**
		 * Deprecated
		 */
		if ( ! $this->is_gf_version_gte( '2.5-beta-1' ) ) {
			add_filter( 'gform_form_settings', array( $this, 'form_settings_ui' ), 10, 2 );
		}

	}

	/**
	 * Check if installed version of Gravity Forms is greater than or equal to the specified version.
	 *
	 * @param string $version Version to compare with Gravity Forms' version.
	 *
	 * @return bool
	 */
	public function is_gf_version_gte( $version ) {
		return class_exists( 'GFForms' ) && version_compare( GFForms::$version, $version, '>=' );
	}

	public function tooltips( $tooltips ) {

		$tooltips['gwreloadform_enable']       = '<h6>' . __( 'Automatically Reload Form', 'gp-reload-form' ) . '</h6>' . __( 'Automatically reload the form after it has been submitted.', 'gp-reload-form' );
		$tooltips['gwreloadform_refresh_time'] = '<h6>' . __( 'Seconds Until Reload', 'gp-reload-form' ) . '</h6>' . __( 'Specify how many seconds the confirmation message should be displayed before automatically reloading the form.', 'gp-reload-form' );

		return $tooltips;
	}

	public function scripts() {

		$scripts = parent::scripts();

		$scripts[] = array(
			'handle'  => 'gp-reload-form',
			'src'     => $this->get_base_url() . '/scripts/gp-reload-form.js',
			'version' => $this->get_version(),
			'deps'    => array( 'jquery' ),
			'enqueue' => array(
				array( $this, 'should_enqueue_frontend_script' ),
			),
		);

		if ( ! $this->is_gf_version_gte( '2.5-beta-1' ) ) {
			return $scripts;
		}

		$scripts[] = array(
			'handle'  => 'gp-reload-form-admin',
			'src'     => $this->get_base_url() . '/scripts/gp-reload-form-admin.js',
			'version' => $this->get_version(),
			'deps'    => array( 'jquery' ),
			'enqueue' => array(
				array(
					'admin_page' => array( 'form_settings' ),
				),
			),
		);

		return $scripts;
	}

	public function should_enqueue_frontend_script( $form ) {
		return ! GFForms::get_page() && $this->is_applicable_form( $form );
	}

	public function add_form_settings_fields( $form_settings ) {

		$form_settings[] = array(
			'title'  => esc_html__( 'Reload Form', 'gp-reload-form' ),
			'fields' => array(
				array(
					'name'    => 'gwreloadform_enable',
					'tooltip' => '<h6>' . __( 'Automatically Reload Form', 'gp-reload-form' ) . '</h6>' . __( 'Automatically reload the form after it has been submitted.', 'gp-reload-form' ),
					'type'    => 'checkbox',
					'label'   => __( 'Automatically reload the form', 'gp-reload-form' ),
					'choices' => array(
						array(
							'name'  => 'gwreloadform_enable',
							'label' => __( 'Automatically reload the form', 'gravityforms' ),
						),
					),
					'fields'  => array(
						array(
							'name'       => 'gwreloadform_refresh_time',
							'tooltip'    => '<h6>' . __( 'Seconds until reload', 'gp-reload-form' ) . '</h6>' . __( 'Specify how many seconds the confirmation message should be displayed before automatically reloading the form.', 'gp-reload-form' ),
							'type'       => 'text',
							'label'      => __( 'Seconds until reload', 'gp-reload-form' ),
							'style'      => 'width:50%;',
							'dependency' => array(
								'live'   => true,
								'fields' => array(
									array(
										'field' => 'gwreloadform_enable',
									),
								),
							),
						),
					),
				),
			),
		);

		return $form_settings;
	}

	public function form_settings_ui( $form_settings, $form ) {
		if ( $this->is_gf_version_gte( '2.5-beta-1' ) ) {
			return $form_settings;
		}

		$display = ! rgar( $form, 'gwreloadform_enable' ) ? 'display:none;' : '';

		ob_start();
		?>

		<tr class="gp-form-setting">

			<th>
				<label for="gwreloadform_enable">
					<?php _e( 'Automatically Reload Form', 'gp-reload-form' ); ?>
					<?php gform_tooltip( 'gwreloadform_enable' ); ?>
				</label>
			</th>
			<td>

				<input type="checkbox" id="gwreloadform_enable" name="gwreloadform_enable" value="1" <?php checked( rgar( $form, 'gwreloadform_enable' ), true ); ?> />
				<label for="gwreloadform_enable">
					<?php _e( 'Automatically Reload Form', 'gp-reload-form' ); ?>
				</label>

				<div id="gwreloadform_settings" style="margin-top:10px;<?php echo $display; ?>">

					<label for="gwreloadform_refresh_time" style="display:block;">
						<?php _e( 'Seconds Until Reload', 'g-reload-form' ); ?><?php gform_tooltip( 'gwreloadform_refresh_time' ); ?>
					</label>
					<input type="number" id="gwreloadform_refresh_time" name="gwreloadform_refresh_time" value="<?php echo rgar( $form, 'gwreloadform_refresh_time' ); ?>">

				</div>

				<?php $this->form_settings_js(); ?>

			</td>

		</tr>

		<?php

		$section_label                   = __( 'GP Reload Form', 'gp-reload-form' );
		$form_settings[ $section_label ] = array( $this->get_slug() => ob_get_clean() );

		return $form_settings;
	}

	public function form_settings_js() {
		?>

		<script type="text/javascript">

			(function ($) {

				// # UI EVENTS

				$('#gwreloadform_enable').click(function () {
					toggleSettings($(this).is(':checked'));
				});

				// # HELPERS

				function toggleSettings(isChecked) {

					var enableCheckbox = jQuery('#gwreloadform_enable');
					var settingsContainer = jQuery('#gwreloadform_settings');

					if (isChecked) {
						enableCheckbox.prop('checked', true);
						settingsContainer.slideDown();
					} else {
						enableCheckbox.prop('checked', false);
						settingsContainer.slideUp();
					}

				}

			})(jQuery);

		</script>

		<?php
	}

	public function save_our_form_settings( $form ) {
		if ( ! $this->is_gf_version_gte( '2.5-beta-1' ) ) {
			$form['gwreloadform_enable']       = rgpost( 'gwreloadform_enable' );
			$form['gwreloadform_refresh_time'] = $form['gwreloadform_enable'] ? rgpost( 'gwreloadform_refresh_time' ) : '';

			return $form;
		}

		$form['gwreloadform_enable']       = rgpost( '_gform_setting_gwreloadform_enable' );
		$form['gwreloadform_refresh_time'] = $form['gwreloadform_enable'] ? rgpost( '_gform_setting_gwreloadform_refresh_time' ) : '';

		return $form;
	}

	public function register_init_scripts( $form ) {

		if ( ! $this->is_applicable_form( $form ) ) {
			return $form;
		}

		if ( ! class_exists( 'GFFormDisplay' ) ) {
			require_once( GFCommon::get_base_path() . '/form_display.php' );
		}

		// GF 2.5's spinner was changed to an svg
		$spinner_ext  = version_compare( GFForms::$version, '2.5.0', '>=' ) ? 'svg' : 'gif';
		$spinner_url  = gf_apply_filters( array( 'gform_ajax_spinner_url', $form['id'] ), sprintf( '%s/images/spinner.%s', GFCommon::get_base_url(), $spinner_ext ), $form );
		$refresh_time = rgar( $form, 'gwreloadform_refresh_time' );

		$args = array(
			'formId'      => $form['id'],
			'spinnerUrl'  => $spinner_url,
			'refreshTime' => $refresh_time ? $refresh_time : 0,
		);

		$script = 'window.gwrf_' . $form['id'] . ' = new gwrf( ' . json_encode( $args ) . ' );';
		$slug   = sprintf( 'gwreloadform_%d', $form['id'] );

		GFFormDisplay::add_init_script( $form['id'], $slug, GFFormDisplay::ON_PAGE_RENDER, $script );

		return $form;
	}

	function queue_merge_tag_support( $form ) {
		add_action( 'admin_footer', array( $this, 'add_merge_tag_support' ) );

		return $form;
	}

	/**
	 * Adds field merge tags to the merge tag drop downs.
	 */
	function add_merge_tag_support( $form ) {
		?>

		<script type="text/javascript">

			gform.addFilter('gform_merge_tags', 'gprfMergeTags');

			function gprfMergeTags(mergeTags, elementId, hideAllFields, excludeFieldTypes, isPrepop, option) {

				var gf24LegacyElementId = 'form_confirmation_message';

				if (elementId === '_gform_setting_message' || elementId === gf24LegacyElementId) {
					mergeTags.ungrouped.tags.push({
						label: '<?php _e( 'Reload Form Link', 'gp-reload-form' ); ?>',
						tag  : '{reload_form}'
					});
				}

				return mergeTags;
			}

		</script>

		<?php
		return $form;
	}

	public function reload_form_replace_merge_tag( $text, $form ) {

		// Adds support for Cryllic characters.
		preg_match_all( '/{(reload_form):?([\p{L}\s\w.,!?\'"]*)}/miu', $text, $matches, PREG_SET_ORDER );

		if ( empty( $matches ) ) {
			return $text;
		}

		foreach ( $matches as $match ) {
			$link_text   = rgar( $match, 2 ) ? rgar( $match, 2 ) : __( 'Reload Form', 'gp-reload-form' );
			$reload_link = '<a href="" class="gws-reload-form gprl-reload-link" data-formId="' . $form['id'] . '">' . $link_text . '</a>';
			$text        = str_replace( rgar( $match, 0 ), $reload_link, $text );
		}

		return $text;
	}

	public function is_applicable_form( $form ) {

		// 3rd-party error can sometimes result in an invalid $form object
		if ( ! rgar( $form, 'id' ) ) {
			return false;
		}

		if ( rgar( $form, 'gwreloadform_enable' ) ) {
			return true;
		}

		foreach ( $form['confirmations'] as $confirmation ) {
			if ( $this->perk->has_merge_tag( 'reload_form', rgar( $confirmation, 'message' ) ) ) {
				return true;
			}
		}

		return false;
	}

	public function append_form_markup( $confirmation, $form ) {
		/**
		 * Disable regenerating the form markup after each submission. Instead, reload the form by using the form
		 * HTML that's saved during the initial load.
		 *
		 * @param boolean $value Whether or not to disable dynamic reload. Defaults to false.
		 * @param array   $form  Form Settings
		 *
		 * @since 2.0-beta-1.0
		 *
		 */
		if ( gf_apply_filters( array( 'gprf_disable_dynamic_reload', $form['id'] ), false, $form ) ) {
			return $confirmation;
		}

		if ( ! $this->is_applicable_form( $form ) ) {
			return $confirmation;
		}

		parse_str( rgpost( 'gform_ajax' ), $ajax_args );

		$display_title       = ! isset( $ajax_args['title'] ) || ! empty( $ajax_args['title'] ) ? true : false;
		$display_description = ! isset( $ajax_args['description'] ) || ! empty( $ajax_args['description'] ) ? true : false;
		$tabindex            = isset( $ajax_args['tabindex'] ) ? absint( $ajax_args['tabindex'] ) : 0;

		/*
		 * Temporarily unset GFFormDisplay::$submission[ $form['id'] ] otherwise errors will result when calling
		 * gravity_form() during the confirmation.
		 *
		 * As a precaution, this method is set to run at a high priority (later).
		 */
		$submission_backup = GFFormDisplay::$submission[ $form['id'] ];
		unset( GFFormDisplay::$submission[ $form['id'] ] );

		ob_start();
		gravity_form( $form['id'], $display_title, $display_description, false, array(), true, $tabindex );
		$markup = trim( ob_get_clean() );

		GFFormDisplay::$submission[ $form['id'] ] = $submission_backup;

		/**
		 * gformRedirect needs to be escaped due to the load handler of the AJAX iframe doing the following check:
		 *   contents.indexOf('gformRedirect(){')
		 *
		 * @see GFFormDisplay::get_form()
		 *
		 * Previously, we used base64 encoding to get around this. However, PHP base64_encode and decoding using atob()
		 * introduces character encoding issues.
		 */
		$markup = str_replace( 'gformRedirect(){', 'gformGP_RELOAD_FORM_ESCAPEDRedirect(){', $markup );

		// Ensure confirmation is a string and not an array (e.g. when a page redirect is used it's an array)
		if ( is_string( $confirmation ) ) {
			$confirmation .= "<script type='text/javascript'>
				window['RELOAD_FORM_MARKUP_{$form['id']}'] = " . wp_json_encode( $markup ) . ';
			 </script>';
		}

		return $confirmation;
	}

}

class GWReloadForm extends GP_Reload_Form {
}

GFAddOn::register( 'GP_Reload_Form' );
