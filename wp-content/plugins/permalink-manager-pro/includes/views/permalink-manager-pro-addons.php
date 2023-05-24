<?php

/**
 * Display the page where the slugs could be regenerated or replaced
 */
class Permalink_Manager_Pro_Addons {

	public function __construct() {
		add_action( 'init', array( $this, 'init' ), 9 );
	}

	/**
	 * Register hooks used to change the Permalink Manager UI
	 */
	public function init() {
		add_filter( 'permalink_manager_sections', array( $this, 'add_admin_section' ), 5 );

		// Stop Words
		add_action( 'admin_init', array( $this, 'save_stop_words' ), 9 );

		add_filter( 'permalink_manager_tools_fields', array( $this, 'filter_tools_fields' ), 9, 2 );
		add_filter( 'permalink_manager_settings_fields', array( $this, 'filter_settings_fields' ), 9 );
	}

	/**
	 * Activate the fields disabled in free version
	 *
	 * @param array $fields
	 * @param string $subsection
	 *
	 * @return array
	 */
	public function filter_tools_fields( $fields, $subsection ) {
		unset( $fields['content_type']['disabled'] );
		unset( $fields['content_type']['pro'] );
		unset( $fields['taxonomies']['pro'] );
		unset( $fields['ids']['disabled'] );
		unset( $fields['ids']['pro'] );

		return $fields;
	}

	/**
	 * Add support for taxonomies in Bulk URI Editor & allow import from "Custom Permalinks" plugin
	 *
	 * @param array $admin_sections
	 *
	 * @return array
	 */
	public function add_admin_section( $admin_sections ) {
		// Add "Stop words" subsection for "Tools"
		$admin_sections['tools']['subsections']['stop_words']['function'] = array( 'class' => 'Permalink_Manager_Pro_Addons', 'method' => 'stop_words_output' );

		// Display Permalinks for all selected taxonomies
		if ( ! empty( $admin_sections['uri_editor']['subsections'] ) ) {
			foreach ( $admin_sections['uri_editor']['subsections'] as &$subsection ) {
				if ( isset( $subsection['pro'] ) ) {
					$subsection['function'] = array( 'class' => 'Permalink_Manager_Tax_Uri_Editor_Table', 'method' => 'display_admin_section' );
					unset( $subsection['html'] );
				}
			}
		}

		// Add "Support" section
		$admin_sections['support'] = array(
			'name'     => __( 'Support', 'permalink-manager' ),
			'function' => array( 'class' => 'Permalink_Manager_Pro_Addons', 'method' => 'support_output' )
		);

		// Import support
		$admin_sections['tools']['subsections']['import']['function'] = array( 'class' => 'Permalink_Manager_Pro_Addons', 'method' => 'import_output' );

		return $admin_sections;
	}

	/**
	 * Filter the fields displayed in the "Settings" tab
	 *
	 * @param array $fields
	 *
	 * @return array
	 */
	public function filter_settings_fields( $fields ) {
		// Network licence key (multisite)
		$license_key     = Permalink_Manager_Pro_Functions::get_license_key();
		$expiration_info = Permalink_Manager_Pro_Functions::get_expiration_date();

		// 1. licence key
		$fields['licence'] = array(
			'section_name' => __( 'Licence', 'permalink-manager' ),
			'container'    => 'row',
			'fields'       => array(
				'licence_key' => array(
					'type'              => 'text',
					'value'             => $license_key,
					'label'             => __( 'Licence key', 'permalink-manager' ),
					'after_description' => sprintf( '<p class="field-description description licence-info">%s</p><p class="field-description description"><a href="%s" id="pm_get_exp_date" class="mute">%s</a> | <a href="https://permalinkmanager.pro/license-info/%s" target="_blank" class="mute">%s</a></p>', $expiration_info, "#", __( 'Reload the expiration date', 'permalink-manager' ), $license_key, __( 'Get license information', 'permalink-manager' ) )
				)
			)
		);

		if ( defined( 'PMP_LICENCE_KEY' ) || defined( 'PMP_LICENSE_KEY' ) ) {
			$fields['licence']['fields']['licence_key']['readonly'] = true;
		}

		// 2. Unblock some fields
		unset( $fields['redirect']['fields']['setup_redirects']['pro'] );
		unset( $fields['redirect']['fields']['setup_redirects']['disabled'] );
		unset( $fields['redirect']['fields']['extra_redirects']['pro'] );
		unset( $fields['redirect']['fields']['extra_redirects']['disabled'] );
		unset( $fields['exclusion']['fields']['exclude_term_ids']['pro'] );
		unset( $fields['exclusion']['fields']['exclude_term_ids']['disabled'] );

		return $fields;
	}

	/**
	 * Add "Stop words" subsection
	 *
	 * @return string
	 */
	public function stop_words_output() {
		global $permalink_manager_options;

		// Fix the escaped quotes
		$words_list = ( ! empty( $permalink_manager_options['stop-words']['stop-words-list'] ) ) ? stripslashes( $permalink_manager_options['stop-words']['stop-words-list'] ) : "";

		// Get stop-words languages
		$languages = array_merge( array( '' => __( '-- Use predefined words list --', 'permalink-manager' ) ), Permalink_Manager_Pro_Functions::load_stop_words_languages() );

		$buttons = "<table class=\"stop-words-buttons\"><tr>";
		$buttons .= sprintf( "<td><a href=\"#\" class=\"clear_all_words button button-small\">%s</a></td>", __( "Remove all words", "permalink-manager" ) );
		$buttons .= sprintf( "<td>%s<td>", Permalink_Manager_Admin_Functions::generate_option_field( "load_stop_words", array( "type" => "select", "input_class" => "widefat small-select load_stop_words", "choices" => $languages ) ) );
		$buttons .= sprintf( "<td>%s</td>", get_submit_button( __( 'Add the words from the list', 'permalink-manager' ), 'button-small button-primary', 'load_stop_words_button', false ) );
		$buttons .= "</tr></table>";

		$fields = apply_filters( 'permalink_manager_tools_fields', array(
			'stop-words' => array(
				'container' => 'row',
				'fields'    => array(
					'stop-words-enable' => array(
						'label'       => __( 'Enable "stop words"', 'permalink-manager' ),
						'type'        => 'single_checkbox',
						'container'   => 'row',
						'input_class' => 'enable_stop_words'
					),
					'stop-words-list'   => array(
						'label'             => __( '"Stop words" list', 'permalink-manager' ),
						'type'              => 'textarea',
						'container'         => 'row',
						'value'             => $words_list,
						'description'       => __( 'Type comma to separate the words.', 'permalink-manager' ),
						'input_class'       => 'widefat stop_words',
						'after_description' => $buttons
					)
				)
			)
		), 'stop_words' );

		$sidebar = '<h3>' . __( 'Instructions', 'permalink-manager' ) . '</h3>';
		$sidebar .= wpautop( __( 'If enabled, all selected "stop words" will be automatically removed from default URIs.', 'permalink-manager' ) );
		$sidebar .= wpautop( __( 'Each of the words can be removed and any new words can be added to the list. You can also use a predefined list (available in 21 languages).', 'permalink-manager' ) );

		return Permalink_Manager_Admin_Functions::get_the_form( $fields, '', array( 'text' => __( 'Save', 'permalink-manager' ), 'class' => 'primary margin-top' ), $sidebar, array( 'action' => 'permalink-manager', 'name' => 'save_stop_words' ), true );
	}

	/**
	 * Saves the user-defined stop words list
	 */
	public function save_stop_words() {
		if ( isset( $_POST['stop-words'] ) && wp_verify_nonce( $_POST['save_stop_words'], 'permalink-manager' ) ) {
			Permalink_Manager_Actions::save_settings( 'stop-words', $_POST['stop-words'] );
		}
	}

	/**
	 * Display "Import" subsection (Custom Permalinks)
	 *
	 * @return string
	 */
	public function import_output() {
		// Count custom permalinks URIs
		$count_custom_permalinks = count( Permalink_Manager_Third_Parties::custom_permalinks_uris() );

		$fields = apply_filters( 'permalink_manager_tools_fields', array(
			'disable_custom_permalinks' => array(
				'label'          => __( 'Custom Permalinks', 'permalink-manager' ),
				'checkbox_label' => __( 'Deactivate after import', 'permalink-manager' ),
				'type'           => 'single_checkbox',
				'container'      => 'row',
				'description'    => __( 'If selected, "Custom Permalinks" plugin will be deactivated after its custom URIs are imported.', 'permalink-manager' ),
				'input_class'    => ''
			)
		), 'regenerate' );

		$sidebar = '<h3>' . __( 'Instructions', 'permalink-manager' ) . '</h3>';
		$sidebar .= wpautop( __( 'Please note that "Custom Permalinks" (if activated) may break the behavior of this plugin.', 'permalink-manager' ) );
		$sidebar .= wpautop( __( 'Therefore, it is recommended to disable "Custom Permalink" and import old permalinks before using Permalink Manager Pro.', 'permalink-manager' ) );

		// Show some additional info data
		if ( $count_custom_permalinks > 0 ) {
			$button = array(
				'text'  => sprintf( __( 'Import %d URIs', 'permalink-manager' ), $count_custom_permalinks ),
				'class' => 'primary margin-top'
			);
		} else {
			$button = array(
				'text'       => __( 'No custom URIs to import', 'permalink-manager' ),
				'class'      => 'secondary margin-top',
				'attributes' => array( 'disabled' => 'disabled' )
			);
		}

		return Permalink_Manager_Admin_Functions::get_the_form( $fields, 'columns-3', $button, $sidebar, array( 'action' => 'permalink-manager', 'name' => 'import' ), true );
	}

	/**
	 * Add "Support" section
	 *
	 * @return string
	 */
	public function support_output() {
		$output = sprintf( "<h3>%s</h3>", __( "Technical support", "permalink-manager" ) );
		$output .= wpautop( sprintf( __( 'To find the answers on frequently asked questions and information about how to deal with the most common issues please go to the <strong>Knowledge Base</strong> using <a target="_blank" href="%s">this link</a>.', 'permalink-manager' ), 'https://permalinkmanager.pro/knowledge-base/' ) );
		$output .= wpautop( __( 'If you still did not find the answer to your question, please send us your question or a detailed description of your problem/issue to <a href="mailto:support@permalinkmanager.pro">support@permalinkmanager.pro</a>.', 'permalink-manager' ) );
		$output .= wpautop( __( 'To reduce the response time, please attach your licence key and if possible also: URL address of your website and screenshots explaining the issue.', 'permalink-manager' ) );

		$output .= sprintf( "<h3>%s</h3>", __( "Suggestions/feedback", "permalink-manager" ) );
		$output .= wpautop( __( 'If you would like to suggest a new functionality or leave us feedback, we are open to all new ideas and would be grateful for all your comments!', 'permalink-manager' ) );
		$output .= wpautop( __( ' Please send your remarks to <a href="mailto:contact@permalinkmanager.pro">contact@permalinkmanager.pro</a>.', 'permalink-manager' ) );

		return $output;
	}

	/**
	 * Return the HTML output of "Custom Redirects" panel in URI Editor
	 *
	 * @return string
	 */
	public static function display_redirect_form( $element_id ) {
		global $permalink_manager_redirects, $permalink_manager_options, $permalink_manager_external_redirects;

		// Do not trigger if "Extra redirects" option is turned off
		if ( empty( $permalink_manager_options['general']['redirect'] ) || empty( $permalink_manager_options['general']['extra_redirects'] ) ) {
			return __( 'Turn on "<strong>Extra redirects (aliases)</strong>" in Permalink Manager settings to enable this feature.', 'permalink-manager' );
		}

		// 1. Extra redirects
		$html = "<div class=\"single-section\">";

		$html .= sprintf( "<p><label for=\"auto_auri\" class=\"strong\">%s %s</label></p>", __( "Extra redirects (aliases)", "permalink-manager" ), Permalink_Manager_Admin_Functions::help_tooltip( __( "All URIs specified below will redirect the visitors to the custom URI defined above in \"Current URI\" field.", "permalink-manager" ) ) );

		$html .= "<table>";
		// 1A. Sample row
		$html .= sprintf( "<tr class=\"sample-row\"><td>%s</td><td>%s</td></tr>", Permalink_Manager_Admin_Functions::generate_option_field( "permalink-manager-redirects", array( "input_class" => "widefat", "value" => "", 'extra_atts' => "data-index=\"\"", "placeholder" => __( 'sample/custom-uri', 'permalink-manager' ) ) ), "<a href=\"#\" class=\"remove-redirect\"><span class=\"dashicons dashicons-no\"></span></a>" );

		// 1B. Rows with redirects
		if ( ! empty( $permalink_manager_redirects[ $element_id ] ) && is_array( $permalink_manager_redirects[ $element_id ] ) ) {
			foreach ( $permalink_manager_redirects[ $element_id ] as $index => $redirect ) {
				$html .= sprintf( "<tr><td>%s</td><td>%s</td></tr>", Permalink_Manager_Admin_Functions::generate_option_field( "permalink-manager-redirects[{$index}]", array( "input_class" => "widefat", "value" => $redirect, 'extra_atts' => "data-index=\"{$index}\"" ) ), "<a href=\"#\" class=\"remove-redirect\"><span class=\"dashicons dashicons-no\"></span></a>" );
			}
		}
		$html .= "</table>";

		// 1C. Add new redirect button
		$html .= sprintf( "<button type=\"button\" class=\"button button-small hide-if-no-js\" id=\"permalink-manager-new-redirect\">%s</button>", __( "Add new redirect", "permalink-manager" ) );

		// 1D. Description
		$html .= "<div class=\"redirects-panel-description\">";
		$html .= sprintf( wpautop( __( "<strong>Please use URIs only!</strong><br />For instance, to set-up a redirect for <code>%s/old-uri</code> please use <code>old-uri</code>.", "permalink-manager" ) ), home_url() );
		$html .= "</div>";

		$html .= "</div>";

		// 2. Extra redirects
		$html .= "<div class=\"single-section\">";

		$html .= sprintf( "<p><label for=\"auto_auri\" class=\"strong\">%s %s</label></p>", __( "Redirect this page to external URL", "permalink-manager" ), Permalink_Manager_Admin_Functions::help_tooltip( __( "If not empty, the visitors trying to access this page will be redirected to the URL specified below.", "permalink-manager" ) ) );

		$external_redirect_url = ( ! empty( $permalink_manager_external_redirects[ $element_id ] ) ) ? $permalink_manager_external_redirects[ $element_id ] : "";
		$html                  .= Permalink_Manager_Admin_Functions::generate_option_field( "permalink-manager-external-redirect", array( "input_class" => "widefat", "value" => urldecode( $external_redirect_url ), "placeholder" => __( "http://another-website.com/final-target-url", "permalink-manager" ) ) );

		// 2B. Description
		$html .= "<div class=\"redirects-panel-description\">";
		$html .= wpautop( __( "<strong>Please use full URLs!</strong><br />For instance, <code>http://another-website.com/final-target-url</code>.", "permalink-manager" ) );
		$html .= "</div>";

		$html .= "</div>";

		return $html;
	}

}
