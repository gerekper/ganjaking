<?php
if (! class_exists('MFN_Options')) {

	if (! defined('MFN_OPTIONS_DIR')) {
		define('MFN_OPTIONS_DIR', get_template_directory() .'/muffin-options/');
	}

	if (! defined('MFN_OPTIONS_URI')) {
		define('MFN_OPTIONS_URI', get_template_directory_uri() .'/muffin-options/');
	}

	class MFN_Options
	{
		public $dir = MFN_OPTIONS_DIR;
		public $url = MFN_OPTIONS_URI;
		public $page = '';

		public $args = array();
		public $sections = array();

		public $errors = array();
		public $warnings = array();

		public $options = array();

		public $menu = array();

		/**
		 * Class Constructor. Defines the args for the theme options class
		 */

		public function __construct($menu = array(), $sections = array())
		{
			$this->menu = apply_filters('mfn-opts-menu', $menu);

			$defaults = array();

			$defaults['opt_name'] = 'betheme';

			$defaults['menu_icon'] = MFN_OPTIONS_URI .'/img/menu_icon.png';
			$defaults['menu_title'] = __('Theme Options', 'mfn-opts');
			$defaults['page_title'] = __('Theme Options', 'mfn-opts');
			$defaults['page_slug'] = 'be-options';
			$defaults['page_cap'] = 'edit_theme_options';
			$defaults['page_type'] = 'menu';
			$defaults['page_parent'] = '';
			$defaults['page_position'] = 100;

			// get args

			$this->args = $defaults;
			$this->args = apply_filters('mfn-opts-args', $this->args);
			$this->args = apply_filters('mfn-opts-args-'. $this->args['opt_name'], $this->args);

			// get sections

			$this->sections = apply_filters('mfn-opts-sections', $sections);
			$this->sections = apply_filters('mfn-opts-sections-'. $this->args['opt_name'], $this->sections);

			// set option with defaults
			add_action('init', array( $this, '_set_default_options' ));

			// options page
			add_action('admin_menu', array( $this, '_options_page' ), 13);

			// register setting
			add_action('admin_init', array( $this, '_register_setting' ));

			// first action hooked into the admin scripts actions
			add_action('admin_enqueue_scripts', array( $this, '_enqueue' ));

			// hook into the wp feeds for downloading the exported settings
			add_action('do_feed_mfn-opts-'.$this->args['opt_name'], array( $this, '_download_options' ), 1, 1);

			// add static CSS generation before form
			add_action('mfn-opts-page-before-form', array( $this, '_static_CSS' ), 10);

			// add the js for the error handling before the form
			// add_action('mfn-opts-page-before-form', array( $this, '_errors_js' ), 11);

			// add the js for the warning handling before the form
			// add_action('mfn-opts-page-before-form', array( $this, '_warnings_js' ), 12);

			// get the options for use later on
			$this->options = get_option($this->args['opt_name']);
		}

		/**
		 * This is used to return and option value from the options array
		 */

		public function get($opt_name, $default = null)
		{
			if ((! is_array($this->options)) || (! key_exists($opt_name, $this->options))) {
				return $default;
			}

			return ((! empty($this->options[$opt_name])) || ($this->options[$opt_name]==='0')) ? $this->options[$opt_name] : $default;
		}

		/**
		 * Get default options into an array suitable for the settings API
		 */

		public function _default_values()
		{
			$defaults = array();

			foreach ($this->sections as $k => $section) {
				if (isset($section['fields'])) {
					foreach ($section['fields'] as $fieldk => $field) {
						if (!isset($field['std'])) {
							$field['std'] = '';
						}
						$defaults[$field['id']] = $field['std'];
					}
				}
			}

			$defaults['last_tab'] = false;
			return $defaults;
		}

		/**
		 * Set default options on admin_init if option doesnt exist (theme activation hook caused problems, so admin_init it is)
		 */

		public function _set_default_options()
		{
			if (!get_option($this->args['opt_name'])) {
				add_option($this->args['opt_name'], $this->_default_values());
			}
			$this->options = get_option($this->args['opt_name']);
		}

		/**
		 * Class Theme Options Page Function, creates main options page.
		 */

		public function _options_page()
		{
			$this->page = add_submenu_page(
				'betheme',
				$this->args['page_title'],
				$this->args['page_title'],
				$this->args['page_cap'],
				$this->args['page_slug'],
				array( $this, '_options_page_html' )
			);
		}


		/**
		 * Enqueue styles/js for theme page
		 */

		public function _enqueue()
		{

			// styles

			wp_enqueue_style('mfn-opts-font', 'https://fonts.googleapis.com/css?family=Open+Sans:400,400italic,600', false, MFN_THEME_VERSION, 'all');
			wp_enqueue_style('mfn-opts-icons', get_theme_file_uri('/fonts/mfn-icons.css'), false, MFN_THEME_VERSION, 'all');
			wp_enqueue_style('mfn-opts', $this->url .'css/options.css', false, MFN_THEME_VERSION, 'all');

			if (is_rtl()) {
				wp_enqueue_style('mfn-opts-rtl', $this->url .'css/options-rtl.css', false, MFN_THEME_VERSION, 'all');
			}

			// scripts

			wp_enqueue_script('mfn-opts-js', $this->url .'js/options.js', array('jquery'), MFN_THEME_VERSION, true);

		}

		/**
		 * Download the options file, or display it
		 */

		public function _download_options()
		{
			if (! isset($_GET['secret']) || $_GET['secret'] != md5(AUTH_KEY.SECURE_AUTH_KEY)) {
				wp_die('Invalid Secret for options use');
				exit;
			}
			if (! isset($_GET['feed'])) {
				wp_die('No Feed Defined');
				exit;
			}

			$backup_options = get_option(str_replace('mfn-opts-', '', $_GET['feed']));
			$backup_options['mfn-opts-backup'] = '1';

			if (isset($_GET['action']) && $_GET['action'] == 'download_options') {
				header('Content-Description: File Transfer');
				header('Content-type: application/txt');
				header('Content-Disposition: attachment; filename="'. str_replace('mfn-opts-', '', $_GET['feed']) .'_options_'. date('d-m-Y') .'.txt"');
				header('Content-Transfer-Encoding: binary');
				header('Expires: 0');
				header('Cache-Control: must-revalidate');
				header('Pragma: public');
				echo '###'. serialize($backup_options) .'###';
				exit;
			} else {
				echo '###'. serialize($backup_options) .'###';
				exit;
			}
		}

		/**
		 * Register Option for use
		 */

		public function _register_setting()
		{
			register_setting($this->args['opt_name'].'_group', $this->args['opt_name'], array($this, '_validate_options'));

			foreach ($this->sections as $k => $section) {
				add_settings_section($k.'_section', $section['title'], array($this, '_section_desc'), $k.'_section_group');

				if (isset($section['fields'])) {
					foreach ($section['fields'] as $fieldk => $field) {
						if (isset($field['title'])) {
							$th = (isset($field['sub_desc']))?$field['title'].'<span class="description">'.$field['sub_desc'].'</span>':$field['title'];
						} else {
							$th = '';
						}

						add_settings_field($fieldk.'_field', $th, array($this,'_field_input'), $k.'_section_group', $k.'_section', $field); // checkbox
					}
				}
			}

			do_action('mfn-opts-register-settings');
			do_action('mfn-opts-register-settings-'.$this->args['opt_name']);
		}

		/**
		 * Static CSS
		 */

		public function _static_CSS(){

			if( empty($_GET['settings-updated']) || empty($this->options['static-css']) ){
				return false;
			};

			$wp_filesystem = Mfn_Helper::filesystem();

			$upload_dir = wp_upload_dir();
			$path_be = wp_normalize_path( $upload_dir['basedir'] .'/betheme' );
			$path_css = wp_normalize_path( $path_be .'/css' );
			$path = wp_normalize_path( $path_css .'/static.css' );

			if( ! file_exists( $path_be ) ){
				wp_mkdir_p( $path_be );
			}

			if( ! file_exists( $path_css ) ){
				wp_mkdir_p( $path_css );
			}

			$css = "/* theme options */\n";
			$css .= mfn_styles_dynamic();

			$wp_filesystem->put_contents( $path, $css, FS_CHMOD_FILE );
		}

		/**
		 * Validate the Options options before insertion
		 */

		public function _validate_options($plugin_options)
		{
			set_transient('mfn-opts-saved', '1', 1000);

			// options | import

			if (! empty($plugin_options['import'])) {

				if ($plugin_options['import_code'] != '') {

					// import from file
					$import = $plugin_options['import_code'];

				} elseif ($plugin_options['import_link'] != '') {

					// import from URL
					$import = wp_remote_retrieve_body(wp_remote_get($plugin_options['import_link']));

				}

				$imported_options = @unserialize(trim($import, '###'));

				// FIX | Import 1-click Demo Data encoded options file

				if ($imported_options === false) {
					$import_tmp_fn = 'base'.'64_decode'; // it will return FALSE if NOT base64
					$import = call_user_func($import_tmp_fn, trim($import));
					$imported_options = unserialize($import);
				}

				if (is_array($imported_options)) {
					$imported_options['imported'] = 1;
					$imported_options['last_tab'] = false;
					return $imported_options;
				}

			}

			// options | defaults

			if (isset($plugin_options['defaults']) && ($plugin_options['defaults'] == 'Resetting...')) {
				$plugin_options = $this->_default_values();
				return $plugin_options;
			}

			// validate fields (if needed)

			$plugin_options = $this->_validate_values($plugin_options, $this->options);

			// JS error handling

			if ($this->errors) {
				set_transient('mfn-opts-errors', $this->errors, 1000);
			}

			if ($this->warnings) {
				set_transient('mfn-opts-warnings', $this->warnings, 1000);
			}

			// after validate hooks

			do_action('mfn-opts-options-validate', $plugin_options, $this->options);
			do_action('mfn-opts-options-validate-'.$this->args['opt_name'], $plugin_options, $this->options);

			// unset unwanted attributes

			unset($plugin_options['defaults']);
			unset($plugin_options['import']);
			unset($plugin_options['import_code']);
			unset($plugin_options['import_link']);

			return $plugin_options;
		}

		/**
		 * Validate values from options form (used in settings api validate function)
		 * calls the custom validation class for the field so authors can override with custom classes
		 */

		public function _validate_values($plugin_options, $options)
		{
			foreach ($this->sections as $k => $section) {
				if (isset($section['fields'])) {
					foreach ($section['fields'] as $fieldk => $field) {

						$field['section_id'] = $k;

						if ( empty( $plugin_options[$field['id']] ) ) {
							continue;
						}

						// force validate of custom filed types

						if (isset($field[ 'type' ]) && ! isset($field[ 'validate' ])) {
							if ($field[ 'type' ] == 'color' || $field[ 'type' ] == 'color_gradient') {
								$field[ 'validate' ] = 'color';
							}
						}

						if (isset($field[ 'validate' ])) {

							$validate = 'MFN_Validation_'.$field[ 'validate' ];

							if (! class_exists($validate)) {
								require_once($this->dir .'validation/'. $field[ 'validate' ] .'/validation_'. $field[ 'validate' ] .'.php');
							}

							if (class_exists($validate)) {

								$validation = new $validate($field, $plugin_options[ $field['id'] ], $options[ $field['id'] ]);

								$plugin_options[ $field['id'] ] = $validation->value;

								if (isset($validation->error)) {
									$this->errors[] = $validation->error;
								}

								if (isset($validation->warning)) {
									$this->warnings[] = $validation->warning;
								}

								continue;
							}
						}

						if (isset($field['validate_callback']) && function_exists($field['validate_callback'])) {
							$callbackvalues = call_user_func($field['validate_callback'], $field, $plugin_options[$field['id']], $options[$field['id']]);

							$plugin_options[$field['id']] = $callbackvalues['value'];

							if (isset($callbackvalues['error'])) {
								$this->errors[] = $callbackvalues['error'];
							}

							if (isset($callbackvalues['warning'])) {
								$this->warnings[] = $callbackvalues['warning'];
							}
						}

					}
				}
			}

			return $plugin_options;
		}

		/**
		 * HTML OUTPUT
		 */

		function _options_page_html(){

			$form_class = '';

			// Plugin: Muffin Header Builder

			if( class_exists( 'Mfn_HB_Admin' ) && get_site_option( 'mfn_header_builder' ) ){
				$form_class = 'mhb-active';
			}

			echo '<div id="mfn-wrapper">';

				do_action('mfn-opts-page-before-form');

				echo '<form id="mfn-form-wrapper" class="'. esc_attr( $form_class ) .'" method="post" action="options.php" enctype="multipart/form-data" >';

					settings_fields( $this->args['opt_name'].'_group' );

					$this->options['last_tab'] = isset( $this->options['last_tab'] ) ? $this->options['last_tab'] : false;
					echo '<input type="hidden" id="last_tab" name="'.$this->args['opt_name'].'[last_tab]" value="'.$this->options['last_tab'].'" />';

					echo '<div id="mfn-aside">';
						echo '<div class="mfn-logo">Theme Options - Powered by Muffin Group</div>';

						// menu items

						echo '<ul class="mfn-menu">';

							foreach($this->menu as $k => $menu_item)
							{
								echo '<li class="mfn-menu-li mfn-menu-li-'. $k .'">';
									echo '<a href="javascript:void(0);" class="mfn-menu-a"><span class="icon"></span>'. $menu_item['title']. '</a>';

									if( is_array( $menu_item['sections'] ) )
									{
										echo '<ul class="mfn-submenu">';
										foreach( $menu_item['sections'] as $sub_item ){
											echo '<li id="'.$sub_item.'-mfn-submenu-li" class="mfn-submenu-li">';
												echo '<a href="javascript:void(0);" class="mfn-submenu-a" data-rel="'.$sub_item.'"><span>'. $this->sections[$sub_item]['title'] .'</span></a>';
											echo '</li>';
										}
										echo '</ul>';
									}

								echo '</li>';
							}

							// import
							echo '<li class="mfn-menu-li mfn-menu-li-import">';
								echo '<a href="javascript:void(0);" class="mfn-menu-a"><span class="icon"></span>'. __('Backup & Reset', 'mfn-opts'). '</a>';
								echo '<ul class="mfn-submenu">';
									echo '<li id="import-mfn-submenu-li" class="mfn-submenu-li">';
										echo '<a href="javascript:void(0);" class="mfn-submenu-a" data-rel="import"><span>'. __('Backup & Reset', 'mfn-opts'). '</span></a>';
									echo '</li>';
								echo '</ul>';
							echo '</li>';

						echo '</ul>';

						echo '<div class="mfn-theme-version">'. __('Theme version', 'mfn-opts') .' <span>'. MFN_THEME_VERSION .'</span></div>';
						echo '<div class="mfn-link"><a href="admin.php?page=be-support">'. __('Manual & Support', 'mfn-opts') .'</a></div>';

					echo '</div>';

					echo '<div id="mfn-main">';

						echo '<div class="mfn-header">';
							echo '<input type="submit" name="submit" value="'.__('Save Changes', 'mfn-opts').'" class="mfn-popup-save" />';
						echo '</div>';

						// sections

						echo '<div class="mfn-sections">';

							foreach($this->sections as $k => $section){
								echo '<div id="'.$k.'-mfn-section'.'" class="mfn-section">';

									if( $form_class ){
										echo '<div class="mfn-message plugin first">';
											echo 'BeTheme <a target="_blank" href="admin.php?page=be-header">Header Builder plugin is active</a>. Header related options are hidden.';
										echo '</div>';
									}

									do_settings_sections($k.'_section_group');

									echo '<div class="mfn-sections-footer">';
										echo '<input type="submit" name="submit" value="'.__('Save Changes', 'mfn-opts').'" class="mfn-popup-save" tabindex="-1"/>';
									echo '</div>';

								echo '</div>';
							}

							// import

							echo '<div id="import-mfn-section" class="mfn-section">';
								echo '<h3>'. __('Import & Export', 'mfn-opts'). '</h3>';

								echo '<div class="mfn-import-wrapper">';

									// export

									echo '<div class="mfn-import-box mfn-import-exp">';
										echo '<h4>'.__('Export Options', 'mfn-opts').'</h4>';
										echo '<p class="description">'. __('Here you can copy/download your themes current option settings. Keep this safe as you can use it as a backup should anything go wrong. Or you can use it to restore your settings on this site (or any other site). You also have the handy option to copy the link to yours sites settings. Which you can then use to duplicate on another site.', 'mfn-opts') .'</p>';

										echo '<p>';
											echo '<a href="javascript:void(0);" class="mfn-btn mfn-import-exp-code-btn">'.__('Copy', 'mfn-opts').'</a>&nbsp;';
											echo '<a href="'. esc_url( add_query_arg(array('feed' => 'mfn-opts-'.$this->args['opt_name'], 'action' => 'download_options', 'secret' => md5(AUTH_KEY.SECURE_AUTH_KEY)), site_url()) ) .'" class="mfn-btn mfn-btn-primary mfn-import-exp-download-btn">'.__('Download', 'mfn-opts').'</a>&nbsp;';
											echo '<a href="javascript:void(0);" class="mfn-btn mfn-import-exp-link-btn">'.__('Copy Link', 'mfn-opts').'</a>';
										echo '</p>';

										$backup_options = $this->options;
										$backup_options['mfn-opts-backup'] = '1';
										$encoded_options = '###'.serialize($backup_options).'###';

										echo '<textarea class="large-text mfn-import-exp-code" rows="8">';
											print_r($encoded_options);
										echo '</textarea>';
										echo '<input type="text" class="large-text mfn-import-exp-link" value="'. esc_url( add_query_arg(array('feed' => 'mfn-opts-'.$this->args['opt_name'], 'secret' => md5(AUTH_KEY.SECURE_AUTH_KEY)), site_url()) ) .'" />';

									echo '</div>';

									// import

									echo '<div class="mfn-import-box mfn-import-imp">';
										echo '<h4>'.__('Import Options', 'mfn-opts').'</h4>';

										echo '<p>';
											echo '<a href="javascript:void(0);" class="mfn-btn mfn-import-imp-code-btn">'.__('Import from file', 'mfn-opts').'</a>&nbsp;';
											echo '<a href="javascript:void(0);" class="mfn-btn mfn-import-imp-link-btn">'.__('Import from URL', 'mfn-opts').'</a>';
										echo '</p>';

										echo '<div class="mfn-import-imp-code-wrapper">';
											echo '<p class="description">'.__('Input your backup file below and hit Import to restore your sites options from a backup.', 'mfn-opts').'</p>';
											echo '<textarea name="'.$this->args['opt_name'].'[import_code]" class="large-text" rows="8"></textarea>';
										echo '</div>';

										echo '<div class="mfn-import-imp-link-wrapper">';
											echo '<p class="description">'.__('Input the URL to another sites options set and hit Import to load the options from that site.', 'mfn-opts').'</p>';
											echo '<input type="text" name="'.$this->args['opt_name'].'[import_link]" class="large-text" value="" />';
										echo '</div>';

										echo '<p class="mfn-import-imp-action">';
											echo '<input type="submit" id="mfn-opts-import" name="'.$this->args['opt_name'].'[import]" class="mfn-btn mfn-btn-primary" value="'.__('Import', 'mfn-opts').'">';
											echo '<span>'.__('WARNING! This will overwrite all existing options, please proceed with caution!', 'mfn-opts').'</span>';
										echo '</p>';

									echo '</div>';

									// reset

									echo '<div class="mfn-import-box mfn-import-res">';
										echo '<h4>'.__('Reset Options', 'mfn-opts').'</h4>';

										echo '<p class="mfn-import-imp-action step-1">';
											echo '<a href="javascript:void(0);" class="mfn-btn mfn-btn-primary reset-pre-confirm">'. __( 'Reset to Defaults', 'mfn-opts' ) .'</a>';
											echo '<span class="reset-warning">'.__('WARNING! This will overwrite all existing options, please proceed with caution!', 'mfn-opts').'</span>';
										echo '</p>';

										echo '<p class="mfn-import-imp-action step-2">';
											echo 'Insert security code: <b>r3s3t</b> <input type="text" value="" class="reset-security-code" />';
											echo '<input type="submit" name="'. $this->args['opt_name'] .'[defaults]" value="'. __( 'Confirm reset ALL options', 'mfn-opts' ). '" class="mfn-btn mfn-btn-primary mfn-popup-reset" />';
										echo '</p>';

									echo '</div>';

								echo '</div>';

								echo '<div class="mfn-sections-footer">';
									echo '<input type="submit" name="submit" value="'.__('Save Changes', 'mfn-opts').'" class="mfn-popup-save" />';
								echo '</div>';
							echo '</div>';

						echo '</div>';

					echo '</div>';

					echo '<div class="clear">&nbsp;</div>';
				echo '</form>';
			echo '</div>';
		}

		/**
		 * JS to display the errors on the page
		 */
		function _errors_js(){

			if( isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true' && get_transient('mfn-opts-errors') ){

				$errors = get_transient('mfn-opts-errors');
				$section_errors = array();

				foreach($errors as $error){
					$section_errors[$error['section_id']] = isset($section_errors[$error['section_id']]) ? $section_errors[$error['section_id']] : 0;
					$section_errors[$error['section_id']]++;
				}

				echo '<script type="text/javascript">';
					echo 'jQuery(document).ready(function(){';

						echo 'jQuery("#mfn-opts-field-errors span").html("'.count($errors).'");';
						echo 'jQuery("#mfn-opts-field-errors").show();';

						foreach($section_errors as $sectionkey => $section_error){
							echo 'jQuery("#'.$sectionkey.'_section_group_li_a").append("<span class=\"mfn-opts-menu-error\">'.$section_error.'</span>");';
						}

						foreach($errors as $error){
							echo 'jQuery("#'.$error['id'].'").addClass("mfn-opts-field-error");';
							echo 'jQuery("#'.$error['id'].'").closest("td").append("<span class=\"mfn-opts-th-error\">'.$error['msg'].'</span>");';
						}

					echo '});';
				echo '</script>';

				delete_transient('mfn-opts-errors');
			}
		}

		/**
		 * JS to display the warnings on the page
		 */
		function _warnings_js(){

			if( isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true' && get_transient('mfn-opts-warnings') ){

				$warnings = get_transient('mfn-opts-warnings');
				$section_warnings = array();

				foreach($warnings as $warning){
					$section_warnings[$warning['section_id']] = isset($section_warnings[$warning['section_id']]) ? $section_warnings[$warning['section_id']] : 0;
					$section_warnings[$warning['section_id']]++;
				}

				echo '<script type="text/javascript">';
					echo 'jQuery(document).ready(function(){';
						echo 'jQuery("#mfn-opts-field-warnings span").html("'.count($warnings).'");';
						echo 'jQuery("#mfn-opts-field-warnings").show();';

						foreach($section_warnings as $sectionkey => $section_warning){
							echo 'jQuery("#'.$sectionkey.'_section_group_li_a").append("<span class=\"mfn-opts-menu-warning\">'.$section_warning.'</span>");';
						}

						foreach($warnings as $warning){
							echo 'jQuery("#'.$warning['id'].'").addClass("mfn-opts-field-warning");';
							echo 'jQuery("#'.$warning['id'].'").closest("td").append("<span class=\"mfn-opts-th-warning\">'.$warning['msg'].'</span>");';
						}

					echo '});';
				echo '</script>';

				delete_transient('mfn-opts-warnings');
			}
		}

		/**
		 * Section HTML OUTPUT
		 */

		function _section_desc( $section ){

			$id = str_replace( '_section', '', $section['id'] );

			if( isset( $this->sections[$id]['desc'] ) ){
				echo '<div class="mfn-opts-section-desc">'. $this->sections[$id]['desc'] .'</div>';
			}

		}

		/**
		 * Field HTML OUTPUT
		 */

		function _field_input( $field ){

			if( isset( $field['type'] ) ){

				$field_class = 'MFN_Options_'. $field['type'];

				if ( ! class_exists( $field_class ) ) {
					require_once( $this->dir .'fields/'. $field['type'] .'/field_'. $field['type'] .'.php' );
				}

				if( class_exists( $field_class ) ){

					if( isset( $this->options[$field['id']] ) ){
						$value = $this->options[$field['id']];
					} else {
						$value = '';
					}

					$render = new $field_class( $field, $value, $this->args['opt_name'] );
					$render->render();

				}

			}

		}

	}

}
