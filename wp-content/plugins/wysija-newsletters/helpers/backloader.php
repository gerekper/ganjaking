<?php
defined( 'WYSIJA' ) or die( 'Restricted access' );

/**
 * class managing the admin vital part to integrate wordpress
 */
class WYSIJA_help_backloader extends WYSIJA_help{

	var $jsVariables = '';

	function __construct() {
		parent::__construct();
	}

	/**
	 *
	 * @param type $controller
	 */
	function init( &$controller ) {
		wp_enqueue_style( 'wysija-admin-css', WYSIJA_URL . 'css/admin.css', array(), WYSIJA::get_version() );
		wp_enqueue_script( 'wysija-admin', WYSIJA_URL . 'js/admin.js', array( 'jquery' ), true, WYSIJA::get_version() );

		/* default script on all wysija interfaces in admin */
		wp_enqueue_script( 'wysija-admin-if ', WYSIJA_URL . 'js/admin-wysija.js', array( 'jquery' ), WYSIJA::get_version() );

		// TO IMPROVE: This has NOTHING TO DO HERE. It has to be moved to the subscribers controller
		if ( ! $controller->jsTrans ) {
			$controller->jsTrans['selecmiss']  = __( 'Please make a selection first!', WYSIJA );
			$controller->jsTrans['suredelete'] = __( 'Deleting a list will not delete any subscribers.', WYSIJA );
		}

		$controller->jsTrans['sure_to_switch_package'] = __( 'Do you want to install that version?', WYSIJA );
		$controller->js[] = 'wysija-admin-ajax';
		$controller->js[] = 'thickbox';
		wp_enqueue_style( 'thickbox' );
	}

	/**
	 * help to automatically loads scripts and stylesheets based on the request
	 * @param type $pagename
	 * @param type $dirname
	 * @param type $urlname
	 * @param type $controller
	 * @param type $extension
	 * @return type
	 */
	function load_assets( $pagename, $dirname, $urlname, &$controller, $extension = 'newsletter' ) {

		if ( isset( $_REQUEST['action'] ) ) {
			$action = $_REQUEST['action'];

			//add form validators script for add and edit
			if ( ( $action == 'edit' || $action == 'add' ) && is_object( $controller ) ) {
				$controller->js[] = 'wysija-validator';
			}
		} else {
			$action = 'default';

			//load the listing script
			if ( $pagename != 'config' ) {
				wp_enqueue_script( 'wysija-admin-list' );
			}
		}
		//check for files based on this combinations of parameters pagename or pagename and action
		$possibleParameters = array( array( $pagename ), array( $pagename, $action ) );
		$enqueueFileTypes   = array( 'wp_enqueue_script' => array( 'js' => 'js', 'php' => 'js' ), 'wp_enqueue_style' => array( 'css' => 'css' ) );

		// Files that we have, don't use file_exists if we know which files we have
		$files = (object) array(
			'css' => array(
				'add-ons',
				'admin-campaigns-articles',
				'admin-campaigns-autopost',
				'admin-campaigns-bookmarks',
				'admin-campaigns-dividers',
				'admin-campaigns-editDetails',
				'admin-campaigns-editTemplate',
				'admin-campaigns-medias',
				'admin-campaigns-themes',
				'admin-campaigns-viewstats',
				'admin-campaigns-welcome_new',
				'admin-campaigns',
				'admin-global',
				'admin-premium',
				'admin-statistics',
				'admin-subscribers-addlist',
				'admin-subscribers-edit',
				'admin-subscribers-export',
				'admin-subscribers-exportlist',
				'admin-subscribers-import',
				'admin-subscribers-importmatch',
				'admin-subscribers-lists',
				'admin-widget',
				'admin-config',
				'admin-config-form_widget_settings',
				'wordpress-about',
			),
			'js' => array(
				'admin-ajax-proto',
				'admin-ajax',
				'admin-campaigns-articles',
				'admin-campaigns-autopost',
				'admin-campaigns-bookmarks',
				'admin-campaigns-default',
				'admin-campaigns-dividers',
				'admin-campaigns-edit',
				'admin-campaigns-editAutonl',
				'admin-campaigns-editDetails',
				'admin-campaigns-editTemplate',
				'admin-campaigns-image_data',
				'admin-campaigns-medias',
				'admin-campaigns-themes',
				'admin-campaigns-viewstats',
				'admin-campaigns-welcome_new',
				'admin-config-form_widget_settings',
				'admin-config-settings',
				'admin-global',
				'admin-listing',
				'admin-statistics-filter',
				'admin-statistics',
				'admin-subscribers-export',
				'admin-subscribers-import',
				'admin-subscribers-importmatch',
				'admin-subscribers',
				'admin-tmce',
				'admin-wysija-global',
				'admin-wysija',
			)
		);

		foreach ( $possibleParameters as $params ) {
			foreach ( $enqueueFileTypes as $method => $types ) {
				foreach ( $types as $file_type => $file_ext ){
					$file_slug = 'admin-' . implode( '-', $params );
					if ( in_array( $file_slug, $files->{ $file_ext } ) ) {
						$file_id = "wysija-autoinc-{$extension}-{$file_slug}-{$file_ext}";
						$file_url = "{$urlname}{$file_ext}/{$file_slug}.{$file_ext}";
						call_user_func_array( $method, array( $file_id, $file_url, array(), WYSIJA::get_version() ) );
					}
				}
			}
		}

		return true;
	}

	/**
	 * enqueue and load dif ferent scripts and style based on one script being requested in the controller
	 * @param type $controller
	 * @param type $pagename
	 * @param string $urlbase
	 */
	function parse_js( &$controller, $pagename, $urlbase = WYSIJA_URL ){

		// find out the name of the plugin based on the urlbase parameter
		$plugin = substr( strrchr( substr( $urlbase, 0, strlen( $urlbase ) - 1 ), '/' ), 1 );

		/* enqueue all the scripts that have been declared in the controller */
		if ( $controller->js ) {
			foreach ( $controller->js as $kjs => $js ) {
				switch ( $js ) {
					case 'jquery-ui-tabs':
						wp_enqueue_script( $js );
						wp_enqueue_style( 'wysija-tabs-css', WYSIJA_URL . 'css/smoothness/jquery-ui-1.8.20.custom.css', array(), WYSIJA::get_version() );
						break;

					case 'wysija-validator':
						wp_enqueue_script( 'wysija-validator-lang' );
						wp_enqueue_script( $js );
						wp_enqueue_script( 'wysija-form' );
						wp_enqueue_style( 'validate-engine-css' );
						break;

					case 'wysija-admin-ajax':
						if ( $plugin != 'wysija-newsletters' ){
							$ajaxvarname = $plugin;
						} else {
							$ajaxvarname = 'wysija';
						}

						$dataajaxxx = array(
							'action' => 'wysija_ajax',
							'controller' => $pagename,
							'wysijaplugin' => $plugin,
							'dataType' => 'json',
							'ajaxurl' => admin_url( 'admin-ajax.php', 'relative' ),
							// 'ajaxurl' => plugins_url( 'wysija-newsletters' ).'/core/ajax.php',
							'pluginurl' => plugins_url( 'wysija-newsletters' ),
							'loadingTrans' => __( 'Loading...', WYSIJA )
						);

						if ( is_user_logged_in() ){
							$dataajaxxx['adminurl'] = admin_url( 'admin.php' );
						}

						wp_localize_script( 'wysija-admin-ajax', $ajaxvarname.'AJAX',$dataajaxxx );
						wp_enqueue_script( 'jquery-ui-dialog' );
						wp_enqueue_script( $js );
						wp_enqueue_style( 'wysija-tabs-css', WYSIJA_URL . 'css/smoothness/jquery-ui-1.8.20.custom.css', array(), WYSIJA::get_version() );
						break;

					case 'wysija-admin-ajax-proto':
						wp_enqueue_script( $js );
						break;

					case 'wysija-edit-autonl':
						wp_enqueue_script( 'wysija-edit-autonl', WYSIJA_URL . 'js/admin-campaigns-editAutonl.js', array( 'jquery' ), WYSIJA::get_version() );
						break;

					case 'wysija-scriptaculous':
						// include prototypeJS + scriptaculous & addons
						wp_enqueue_script( 'wysija-prototype', WYSIJA_URL . 'js/prototype/prototype.js', array(), WYSIJA::get_version() );
						wp_enqueue_script( 'wysija-proto-scriptaculous', WYSIJA_URL . 'js/prototype/scriptaculous.js', array( 'wysija-prototype' ), WYSIJA::get_version() );
						wp_enqueue_script( 'wysija-proto-dragdrop', WYSIJA_URL . 'js/prototype/dragdrop.js', array( 'wysija-proto-scriptaculous' ), WYSIJA::get_version() );
						wp_enqueue_script( 'wysija-proto-controls', WYSIJA_URL . 'js/prototype/controls.js', array( 'wysija-proto-scriptaculous' ), WYSIJA::get_version() );
						wp_enqueue_script( 'wysija-proto-slider', WYSIJA_URL . 'js/prototype/slider.js', array( 'wysija-proto-scriptaculous' ), WYSIJA::get_version() );
						break;

					case 'mailpoet-select2':

						wp_enqueue_script( 'mailpoet-select2', WYSIJA_URL . 'js/select2/select2.min.js', array( 'jquery' ), WYSIJA::get_version() );
						wp_enqueue_script( 'mailpoet-select2-l10n', WYSIJA_URL . 'js/select2/select2-l10n.js', array( 'mailpoet-select2', 'underscore' ), WYSIJA::get_version() );

						wp_enqueue_style( 'mailpoet-select2', WYSIJA_URL . 'css/select2/select2.css', array(), WYSIJA::get_version() );

						wp_localize_script(
							'mailpoet-select2-l10n',
							'mailpoet_l10n_select2',
							array(
								'noMatches' => __( 'No Results were found', WYSIJA ),
								'inputTooShort' => __( 'Please enter <%= chars %> more character<%= plural %>', WYSIJA ),
								'inputTooLong' => __( 'Please delete <%= chars %> character<%= plural %>', WYSIJA ),
								'selectionTooBig' => __( 'You can only select <%= chars %> item<%= plural %>', WYSIJA ),
								'loadMore' => __( 'Loading more Results...', WYSIJA ),
								'searching' => __( 'Searching...', WYSIJA ),
							)
						);

						break;

					case 'mailpoet-field-select2-terms':
						wp_enqueue_script( 'mailpoet-field-select2-terms', WYSIJA_URL . 'js/fields/select2-terms.js', array( 'jquery', 'underscore', 'mailpoet-select2' ), WYSIJA::get_version() );

						break;

					case 'mailpoet-field-select2-simple':
						wp_enqueue_script( 'mailpoet-field-select2-simple', WYSIJA_URL . 'js/fields/select2-simple.js', array( 'jquery', 'underscore', 'mailpoet-select2' ), WYSIJA::get_version() );

						break;

					case 'wysija-form-editor':
						wp_enqueue_script( 'wysija-prototype', WYSIJA_URL . 'js/prototype/prototype.js', array(), WYSIJA::get_version() );
						wp_enqueue_script( 'wysija-proto-scriptaculous', WYSIJA_URL . 'js/prototype/scriptaculous.js', array( 'wysija-prototype' ), WYSIJA::get_version() );
						wp_enqueue_script( 'wysija-proto-dragdrop', WYSIJA_URL . 'js/prototype/dragdrop.js', array( 'wysija-proto-scriptaculous' ), WYSIJA::get_version() );
						wp_enqueue_script( 'wysija-proto-controls', WYSIJA_URL . 'js/prototype/controls.js', array( 'wysija-proto-scriptaculous' ), WYSIJA::get_version() );
						wp_enqueue_script( 'wysija-proto-slider', WYSIJA_URL . 'js/prototype/slider.js', array( 'wysija-proto-scriptaculous' ), WYSIJA::get_version() );

						// include form editor
						wp_enqueue_script( $js, WYSIJA_URL . 'js/' . $js . '.js', array(), WYSIJA::get_version() );

						/* MailPoet form editor i18n */
						wp_localize_script( 'wysija-form-editor', 'Wysija_i18n', $controller->jsTrans );

						// form editor css
						wp_enqueue_style( 'wysija-form-editor-css', WYSIJA_URL . 'css/wysija-form-editor.css', array(), WYSIJA::get_version() );
						break;

					case 'wysija-amcharts':
						// MailPoet chart
						wp_enqueue_script( 'amcharts', WYSIJA_URL . 'js/amcharts/amcharts.js', array(), WYSIJA::get_version() );
						wp_enqueue_script( 'wysija-amcharts', WYSIJA_URL . 'js/wysija-charts.js', array(), WYSIJA::get_version() );
						break;

					case 'wysija-editor':

						wp_enqueue_script( 'wysija-prototype', WYSIJA_URL . 'js/prototype/prototype.js', array(), WYSIJA::get_version() );
						wp_deregister_script( 'thickbox' );

						wp_register_script( 'thickbox', WYSIJA_URL . 'js/thickbox/thickbox.js', array( 'jquery' ), WYSIJA::get_version() );

						wp_localize_script(
							'thickbox',
							'thickboxL10n',
							array(
								'next' => __( 'Next &gt;' ),
								'prev' => __( '&lt; Prev' ),
								'image' => __( 'Image' ),
								'of' => __( 'of' ),
								'close' => __( 'Close' ),
								'noif rames' => __( 'This feature requires inline frames. You have iframes disabled or your browser does not support them.' ),
								'l10n_print_after' => 'try{convertEntities( thickboxL10n );}catch( e ){};',
							)
						);

						wp_enqueue_script( 'wysija-proto-scriptaculous', WYSIJA_URL . 'js/prototype/scriptaculous.js' , array( 'wysija-prototype' ), WYSIJA::get_version() );
						wp_enqueue_script( 'wysija-proto-dragdrop', WYSIJA_URL . 'js/prototype/dragdrop.js', array( 'wysija-proto-scriptaculous' ), WYSIJA::get_version() );
						wp_enqueue_script( 'wysija-proto-controls', WYSIJA_URL . 'js/prototype/controls.js', array( 'wysija-proto-scriptaculous' ), WYSIJA::get_version() );
						wp_enqueue_script( 'wysija-timer', WYSIJA_URL . 'js/timer.js', array(), WYSIJA::get_version() );
						wp_enqueue_script( $js, WYSIJA_URL . 'js/' . $js . '.js', array(), WYSIJA::get_version() );
						wp_enqueue_script( 'wysija-konami', WYSIJA_URL . 'js/konami.js', array(), WYSIJA::get_version() );
						wp_enqueue_script( 'wysija-tinymce', WYSIJA_URL . 'js/tinymce/tiny_mce.js', array(), WYSIJA::get_version() );

						wp_enqueue_script( 'wysija-tinymce-init', WYSIJA_URL . 'js/tinymce_init.js', array(), WYSIJA::get_version() );
						wp_enqueue_style( 'wysija-editor-css', WYSIJA_URL . 'css/wysija-editor.css', array(), WYSIJA::get_version() );
						wp_enqueue_script( 'wysija-colorpicker', WYSIJA_URL . 'js/excolor/jquery.modcoder.excolor.js', array(), WYSIJA::get_version() );

						if ( version_compare( $GLOBALS['wp_version'], '3.9', '>=' ) ){
							wp_enqueue_style( 'mailpoet-tinymce', WYSIJA_URL . 'css/tmce/editor.css', array(), WYSIJA::get_version() );
						}

						/* MailPoet editor i18n */
						wp_localize_script( 'wysija-editor', 'Wysija_i18n', $controller->jsTrans );
						break;

					case 'wysija-colorpicker':
						wp_enqueue_script( 'wysija-colorpicker', WYSIJA_URL . 'js/excolor/jquery.modcoder.excolor.js', array(), WYSIJA::get_version() );
						break;

					case 'wysija-tooltip':
						wp_enqueue_script( 'mailpoet.tooltip', WYSIJA_URL . 'js/vendor/bootstrap.tooltip.js', array( 'jquery' ), WYSIJA::get_version(), true );
						wp_enqueue_style( 'mailpoet.tooltip', WYSIJA_URL . 'css/vendor/bootstrap.tooltip.css', array(), WYSIJA::get_version(), 'screen' );
						break;

					case 'wysija-import-match':
						wp_enqueue_script('jquery-matchColumn', WYSIJA_URL.'js/jquery/jquery.matchColumn.js', array('jquery'), WYSIJA::get_version());
						wp_enqueue_script('jquery-userStatusMapping', WYSIJA_URL.'js/jquery/jquery.userStatusMapping.js', array('jquery'), WYSIJA::get_version());
						break;

					default:
						if ( is_string( $kjs ) ) {
							// check if there's a trailing slash in the urlbase
							if ( substr( $urlbase, -1 ) !== '/' ){
								$urlbase .= '/';
							}
							// check if there's already an extension specif ied for the file
							if ( substr( $urlbase, -3 ) !== '.js' ){
								$js .= '.js';
							}
							// enqueue script

							wp_enqueue_script( $kjs, $urlbase . 'js/' . $js, array(), WYSIJA::get_version() );
						} else {
							wp_enqueue_script( $js );
						}
					}
				}
			}
	}

	/**
	 * add some js defined variable per script
	 * @param type $pagename
	 * @param type $dirname
	 * @param type $urlname
	 * @param type $controller
	 * @param type $extension
	 */
	function localize( $pagename, $dirname, $urlname, &$controller, $extension = 'newsletter' ){
		if ( $controller->jsLoc ){
			foreach ( $controller->jsLoc as $key => $value ){
				foreach ( $value as $kf => $local ){

					//this function accepts multidimensional array some version like wp3.2.1 couldn't do that
					$this->localizeme( $key, $kf, $local );
				}
			}
		}
	}

	/**
	 * multidimensional array are possible here
	 * @param type $handle
	 * @param type $object_name
	 * @param type $l10n
	 */
	function localizeme( $handle, $object_name, $l10n ) {

		foreach ( ( array ) $l10n as $key => $value ) {
			if ( ! is_scalar( $value ) ){
				continue;
			}
			$l10n[$key] = html_entity_decode( (string) $value, ENT_QUOTES, 'UTF-8' );
		}

		$this->jsVariables .= "var $object_name = " . json_encode( $l10n ) . ';';
		add_action( 'admin_head', array( $this, 'localize_print' ) );
	}

	/**
	 * load the variables in the html
	 */
	function localize_print(){
		echo "<script type='text/javascript' id='mailpoet-localized'>\n"; // CDATA and type='text/javascript' is not needed for HTML 5
		echo "/* <![CDATA[ */\n";
		echo esc_attr( '' ) . $this->jsVariables . "\n"; // To comply with PHP Code Sniffer WordPress Standards before we "hack" the echo
		echo "/* ]]> */\n";
		echo "</script>\n";
	}



	/**
	 * this is for backward compatibility and avoid blank screen on older version of the premium plugin
	 */
	function loadScriptsStyles( $pagename, $dirname, $urlname, &$controller, $extension = 'newsletter' ) {
		return $this->load_assets( $pagename, $dirname, $urlname, $controller, $extension );
	}

	/**
	 * this is for backward compatibility and avoid blank screen on older version of the premium plugin
	 */
	function initLoad( &$controller ){
		return $this->init( $controller );
	}

	function jsParse( &$controller, $pagename, $urlbase = WYSIJA_URL ){
		$this->parse_js( $controller, $pagename, $urlbase );
	}

}
