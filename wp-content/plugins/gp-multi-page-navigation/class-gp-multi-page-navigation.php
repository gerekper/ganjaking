<?php

class GP_Multi_Page_Navigation extends GWPerk {

	public $version                      = GP_MULTI_PAGE_NAVIGATION_VERSION;
	protected $min_gravity_perks_version = '2.2.5';
	protected $min_gravity_forms_version = '1.9.1.12';

	public $_args = array();

	private $_default_page = false;

	public function init() {

		load_plugin_textdomain( 'gp-multi-page-navigation', false, basename( dirname( __file__ ) ) . '/languages/' );

		$this->add_tooltip( $this->key( 'enable' ), sprintf( '<h6>%s</h6> %s', __( 'Enable Page Navigation', 'gp-multi-page-navigation' ), __( 'Convert the form\'s page names into clickable links that allow the user to navigation between the pages of the form.', 'gp-multi-page-navigation' ) ) );
		$this->add_tooltip( $this->key( 'activation_type' ), sprintf( '<h6>%s</h6> %s', __( 'Activation Type', 'gp-multi-page-navigation' ), __( 'Specify when the user should be able to navigate between pages and to which pages the user should be able to navigate.', 'gp-multi-page-navigation' ) ) );

		// admin
		add_action( 'gform_editor_js', array( $this, 'form_editor_ui' ) );

		// frontend
		add_action( 'gform_enqueue_scripts', array( $this, 'enqueue_form_scripts' ) );
		add_filter( 'gform_register_init_scripts', array( $this, 'register_init_scripts' ) );

		// priority 11 to avoid our custom inputs being overwritten by WC GF Product Add-ons
		add_filter( 'gform_form_tag', array( $this, 'add_page_status_inputs' ), 11, 2 );

		// run later so plugins using this hook will be bypassed as well
		add_filter( 'gform_validation', array( $this, 'maybe_bypass_validation' ), 20 );
		// add_action( 'gform_pre_process', array( $this, 'force_all_page_validation' ) ); @todo remove completely once we've updated min gf version to 2.0 (?)
		add_action( 'gform_target_page', array( $this, 'adjust_target_page_for_conditional_logic' ), 10, 2 );

		// Add support for "page" attribute on [gravityforms] shortcode.
		add_filter( 'shortcode_atts_gravityforms', array( $this, 'stash_shortcode_page_attr' ), 10, 4 );
		add_filter( 'gform_pre_render', array( $this, 'set_page' ) );

	}

	public function stash_shortcode_page_attr( $out, $pairs, $atts ) {

		// We don't really need to return the field_props attribute but let's do it just to be thorough.
		$defaults = array_merge(
			$pairs,
			array(
				'page' => false,
			)
		);

		$out = shortcode_atts( $defaults, $atts );

		// Now let's parse and stash the field_props attribute for use in the gform_pre_render hook (after which it will be cleared).
		$this->_default_page = $out['page'];

		return $out;
	}

	public function set_page( $form ) {

		// Don't set default page if form is being rendered after a submission.
		if ( rgpost( "is_submit_{$form['id']}" ) ) {
			return $form;
		}

		$page = $this->_default_page;

		// If page is set at all, we will check for the query parameter to allow overriding.
		if ( $page && rgget( 'gpmpn_page' ) ) {
			$page = (int) rgget( 'gpmpn_page' );
		}

		/**
		 * Filter the default form page that should be rendered.
		 *
		 * @since 1.0.1
		 *
		 * @param int $page   The default page number.
		 * @param array $form The current form.
		 */
		$page = (int) gf_apply_filters( array( 'gpmpn_default_page', $form['id'] ), $page, $form );

		// Set the page; Gravity Forms will handle rendering the specified page.
		if ( $page ) {
			GFFormDisplay::$submission[ $form['id'] ]['page_number'] = $page;
		}

		return $form;
	}

	public function form_editor_ui() {
		?>

		<li id="gpmpn-settings" style="display:none;">

			<div class="gpmpn-setting-row">

				<div class="gp-row">
					<input type="checkbox" name="<?php echo $this->key( 'enable' ); ?>" id="<?php echo $this->key( 'enable' ); ?>" value="1" onclick="gpmpn.toggleSettings( this.checked, true );" />
					<label for="<?php echo $this->key( 'enable' ); ?>"><?php _e( 'Enable Page Navigation', 'gp-multi-page-navigation' ); ?> <?php gform_tooltip( $this->key( 'enable' ) ); ?></label>
				</div>

				<div class="gp-child-settings gws-child-settings" style="display:none;">

					<div id="gpmpn-activation-type-setting" class="gws-setting-row">
						<label for="<?php echo $this->key( 'activation_type' ); ?>" class="section_label"><?php _e( 'Activation Type', 'gp-multi-page-navigation' ); ?> <?php gform_tooltip( $this->key( 'activation_type' ) ); ?></label>
						<div>
							<label for="<?php echo $this->key( 'activation_type' ); ?>">
								<?php if ( GravityPerks::is_gf_version_lte( '2.5' ) ) : ?>
									<?php _e( 'User can navigate to any form page', 'gp-multi-page-navigation' ); ?>
								<?php else : ?>
									<?php _e( 'User can navigate to any form page...', 'gp-multi-page-navigation' ); ?>
								<?php endif; ?>
							</label>
							<select id="<?php echo $this->key( 'activation_type' ); ?>" name="<?php echo $this->key( 'activation_type' ); ?>" onchange="gpmpn.setPaginationProp( '<?php echo $this->key( 'activation_type' ); ?>', this.value );">
								<option value="progression"><?php _e( 'they have completed', 'gp-multi-page-navigation' ); ?></option>
								<option value="first_page"><?php _e( 'from the start', 'gp-multi-page-navigation' ); ?></option>
								<option value="last_page"><?php _e( 'after reaching the last page', 'gp-multi-page-navigation' ); ?></option>
							</select>
						</div>
					</div>

					<div id="<?php echo $this->key( 'indicator_message' ); ?>" class="gws-setting-row">
						<div class="gws-setting-message">
							Page navigation will automatically work with the "Steps" progress indicator. When enabled with the "Progress Bar" or no progress indicator,
								you must create custom page links to navigate between pages. <a href="http://gravitywiz.com/gravity-forms-multi-page-form-navigation/#custom-page-links">Learn More</a>
						</div>
					</div>

				</div>

			</div>

		</li>

		<style type="text/css">

			.gws-setting-message {
				border: 1.5px solid #ffbe03;
				border-radius: 3px
				line-height: 1em;
				padding: 0.875rem;
			}

			.gf-legacy-ui .gws-setting-message {
				background-color: #FFFFE0;
				border: 1px solid #F4EFB8;
				padding: 10px;
			}

			.gf-legacy-ui #gp-multi-page-navigation_activation_type {
				width: 170px;
			}

			label[for="gp-multi-page-navigation_activation_type"] {
				display: block;
				margin-bottom: 0.5rem;
			}

			.gf-legacy-ui label[for="gp-multi-page-navigation_activation_type"] {
				display: inline;
			}

		</style>

		<script type="text/javascript">
			( function( $ ) {

				window.gpmpn = {

					$pageOptionsTabProps: $( '.editor-sidebar #general_tab' ).length
						? $( '.editor-sidebar #general_tab' )
						: $( '#gform_pagination_settings_tab_1 ul' ), // GF <=2.4 form editor selector

					$settingsElem:        $( '#gpmpn-settings' ),

					init: function() {

						// add our custom pagination settings to the "Start Paging" settings
						gpmpn.$pageOptionsTabProps.append( gpmpn.$settingsElem );

						// take control of GFs InitPaginationOptions() function so we can mark our inputs as checked (if applicable)
						gpmpn.overrideInitPaginationOptionsFunction();

						// save our custom pagination settings when the form is saved (otherwise they will be lost)
						gform.addFilter( 'gform_pre_form_editor_save', function( form ) {

							if( ! form.pagination ) {
								return form;
							}

							form.pagination[ gpmpn.key( 'enable' ) ] = $( '#' + gpmpn.key( 'enable' ) ).is( ':checked' );
							form.pagination[ gpmpn.key( 'activation_type' ) ] = form.pagination.type == 'steps' ? $( '#' + gpmpn.key( 'activation_type' ) ).val() : 'manual';

							return form;
						} );

						/**
						 * Hide GP Multi Page Navigation settings anytime a field is loaded.
						 */
						$(document).on('gform_load_field_settings', function() {
							gpmpn.$settingsElem.hide();
						});

					},

					toggleSettings: function( isChecked ) {

						var $childSettings    = gpmpn.$settingsElem.find( '.gws-child-settings' ),
							$activationType   = $( '#' + gpmpn.key( 'activation_type' ) ),
							$indicatorMessage = $( '#' + gpmpn.key( 'indicator_message' ) ),
							isStepsSelected   = $( '#pagination_type_steps' ).is( ':checked' );

						gpmpn.setPaginationProp( gpmpn.key( 'enable' ), isChecked );
						gpmpn.setPaginationProp( gpmpn.key( 'activation_type' ), $activationType.val() ? $activationType.val() : 'progression' );

						if( isChecked ) {

							$childSettings.slideDown();

							if( ! isStepsSelected ) {
								$indicatorMessage.show();
								$( '#gpmpn-activation-type-setting' ).hide();
							} else {
								$indicatorMessage.hide();
								$( '#gpmpn-activation-type-setting' ).show();
							}

						} else {
							$childSettings.slideUp();
						}

					},

					setPaginationProp: function( prop, value ) {
						form.pagination[ prop ] = value;
					},

					getPaginationProp: function( prop ) {

						if( ! form.pagination ) {
							return false;
						}

						return form.pagination[ prop ];
					},

					overrideInitPaginationOptionsFunction: function() {

						var initPaginationOptions = window.InitPaginationOptions;

						window.InitPaginationOptions = function() {

							initPaginationOptions();

							gpmpn.$settingsElem.show();

							if( ! form.pagination ) {
								return;
							}

							var isStepsSelected = gpmpn.getPaginationProp( 'type' ),
								type            = gpmpn.getPaginationProp( gpmpn.key( 'activation_type' ) ),
								type            = ! isStepsSelected ? 'manual' : type && type != 'manual' ? type : 'progression';

							$( '#' + gpmpn.key( 'enable' ) ).prop( 'checked', gpmpn.getPaginationProp( gpmpn.key( 'enable' ) ) );
							$( '#' + gpmpn.key( 'activation_type' ) ).val( type );

							gpmpn.toggleSettings( gpmpn.getPaginationProp( gpmpn.key( 'enable' ) ) );

						};

					},

					key: function( value ) {
						var key = '<?php echo $this->key( '' ); ?>';
						return key + value;
					}

				};

				gpmpn.init();

			} )( jQuery );
		</script>

		<?php
	}

	public function enqueue_form_scripts( $form ) {

		if ( $this->is_navigation_enabled( $form ) ) {

			wp_enqueue_script( 'gp-multi-page-navigation', $this->get_base_url() . '/js/gp-multi-page-navigation.js', array( 'jquery' ), $this->version );
			wp_enqueue_style( 'gp-multi-page-navigation', $this->get_base_url() . '/css/gp-multi-page-navigation.css', array(), $this->version );

			$this->register_noconflict_script( 'gp-multi-page-navigation' );

			$this->register_noconflict_styles( 'gp-multi-page-navigation' );
			$this->register_preview_style( 'gp-multi-page-navigation' );

		}

	}

	public function add_page_status_inputs( $form_tag, $form ) {

		if ( ! $this->is_navigation_enabled( $form ) ) {
			return $form_tag;
		}

		$inputs = '';

		if ( in_array( $this->get_activation_type( $form ), array( 'last_page', 'first_page', 'manual' ), true ) && $this->is_last_page_reached( $form ) ) {
			$inputs .= '<input id="gw_last_page_reached" name="gw_last_page_reached" value="1" type="hidden" />';
		}

		// primarily required for "progression" nav type but also used by other types to check if a page is being "resubmitted"
		$page_progression = $this->get_page_progression( $form );
		$inputs          .= '<input id="gw_page_progression" name="gw_page_progression" value="' . $page_progression . '" type="hidden" />';

		if ( $this->was_final_submission_attempted( $form ) ) {
			$inputs .= '<input id="gw_final_submission_attempted" name="gw_final_submission_attempted" value="1" type="hidden" />';
		}

		$error_pages_count = count( $this->get_all_pages_with_validation_error( $form ) );
		if ( $error_pages_count > 0 ) {
			$inputs .= sprintf( '<input id="gw_error_pages_count" name="gw_error_pages_count" value="%d" type="hidden" />', $error_pages_count );
		}

		$form_tag .= $inputs;

		return $form_tag;
	}

	public function register_init_scripts( $form ) {

		if ( ! $this->is_navigation_enabled( $form ) ) {
			return;
		}

		$page_number = GFFormDisplay::get_current_page( $form['id'] );
		$last_page   = count( $form['pagination']['pages'] );

		$args = array(
			'formId'                                 => $form['id'],
			'lastPage'                               => $last_page,
			'activationType'                         => $this->get_activation_type( $form ),
			/**
			 * Filter button labels for multi-page navigation plugin.
			 *
			 * @since 1.0.8
			 *
			 * @param array $labels {
			 *  An array of button labels that are used for navigation.
			 *
			 *  @type string $backToLastPage Back to last page button label.
			 *  @type string $submit Submit button label.
			 *  @type string $nextPageWithErrors Next page with errors label.
			 * }
			 */
			'labels'                                 => apply_filters(
				'gpmpn_frontend_labels',
				array(
					'backToLastPage'     => __( 'Back to Last Page', 'gp-multi-page-navigation' ),
					'submit'             => _x( 'Submit', 'Option to submit multi-page form after validation error', 'gp-multi-page-navigation' ),
					'nextPageWithErrors' => __( 'Next Page with Errors', 'gp-multi-page-navigation' ),
				),
				$form
			),
			/**
			 * Enables submission from the last page with errors instead of having to advance through the entire form.
			 *
			 * @since 1.0.8
			 *
			 * @param bool $enable_submission_from_last_page_with_errors Should GPMPN allow submission from the last page with errors? Defaults to true.
			 * @param array $form The current form.
			 */
			'enableSubmissionFromLastPageWithErrors' => apply_filters( 'gpmpn_enable_submission_from_last_page_with_errors', true, $form ),
		);

		$script = 'new GPMultiPageNavigation( ' . json_encode( $args ) . ' );';

		GFFormDisplay::add_init_script( $form['id'], 'gpmpn', GFFormDisplay::ON_PAGE_RENDER, $script );

	}

	public function maybe_bypass_validation( $validation_result ) {

		if ( $validation_result['is_valid'] || ! $this->is_navigation_enabled( $validation_result['form'] ) ) {
			return $validation_result;
		}

		$form = $validation_result['form'];

		if ( $this->is_bypass_validation_enabled( $form ) ) {
			$validation_result['is_valid'] = true;
			foreach ( $form['fields'] as &$field ) {
				$field->failed_validation = false;
			}
		} elseif ( $this->is_activate_on_first_page( $form ) ) {
			$validation_result['failed_validation_page'] = $this->get_first_page_with_validation_error( $form );
			add_filter( 'gform_validation_message_' . $form['id'], array( $this, 'modify_validation_message' ), 10, 2 );
		} elseif ( $this->is_page_resubmission( $form ) ) {
			/*
			 * If the user navigates to an earlier page, in 'progression' or 'last_reached' mode, and then uses the page
			 * navigation to move forward in the form, we need to validate all pages between the page they navigated
			 * back to and the page they are now navigating forward to.
			 */
			$first_page_with_error = $this->get_first_page_with_validation_error( $form );
			if ( $first_page_with_error < $this->get_target_page( $form ) ) {
				$validation_result['failed_validation_page'] = $this->get_first_page_with_validation_error( $form );
				add_filter( 'gform_validation_message_' . $form['id'], array( $this, 'modify_validation_message' ), 10, 2 );
			} else {
				$validation_result['is_valid'] = true;
				$validation_result['form']     = $this->remove_validation_errors( $form );
			}
		}

		return $validation_result;
	}

	public function modify_validation_message( $message, $form ) {

		$pages_with_erors = $this->get_all_pages_with_validation_error( $form );

		$message = array();

		$message[] = __( 'There was a problem with your submission.', 'gravityforms' );

		if ( count( $pages_with_erors ) > 1 ) {
			$message[] = __( 'There are multiple pages with errors.', 'gp-multi-page-navigation' );
			$message[] = __( 'You have been redirected to the first page with errors.', 'gp-multi-page-navigation' );
		}

		if ( GravityPerks::is_gf_version_lte( '2.5-beta-1' ) ) {
			$message[] = __( 'Errors have been highlighted below.', 'gravityforms' );
		} else {
			$message[] = __( 'Please review the fields below.', 'gravityforms' );
		}

		// Apply GF 2.4 error markup if needed
		if ( GravityPerks::is_gf_version_lte( '2.5' ) ) {
			return sprintf( '<div class="validation_error">%s</div>', implode( ' ', $message ) );
		}

		return sprintf( '<h2 class="gform_submission_error"><span class="gform-icon gform-icon--close"></span>%s</h2>',
			implode( ' ', $message )
		);
	}

	public function get_first_page_with_validation_error( $form ) {

		$pages        = $this->get_all_pages_with_validation_error( $form );
		$page_numbers = array_keys( $pages );

		return reset( $page_numbers );
	}

	public function get_all_pages_with_validation_error( $form ) {

		$pages = array();

		foreach ( $form['fields'] as $field ) {
			if ( $field->failed_validation ) {
				if ( ! isset( $pages[ $field->pageNumber ] ) ) {
					$pages[ $field->pageNumber ] = 1;
				} else {
					$pages[ $field->pageNumber ]++;
				}
			}
		}

		return $pages;

	}

	public function add_all_fields_to_last_page( $form ) {

		$last_page = count( $form['pagination']['pages'] );

		foreach ( $form['fields'] as &$field ) {
			$field['origPageNumber'] = $field['pageNumber'];
			$field['pageNumber']     = $last_page;
		}

		return $form;
	}

	public function restore_fields_to_original_pages( $validation_result ) {

		foreach ( $validation_result['form']['fields'] as &$field ) {
			$field['pageNumber'] = $field['origPageNumber'];
		}

		return $validation_result;
	}

	/**
	 * Force ALL pages to be validated (rather than the last page submitted) by setting the source page to 0.
	 *
	 * This should only occur when the activation type is "first_page" and the last form page is being submitted.
	 *
	 * @param $form
	 */
	public function force_all_page_validation( $form ) {

		if ( ! $this->is_navigation_enabled( $form ) ) {
			return $form;
		}

		$is_last_page                            = GFFormDisplay::is_last_page( $form );
		$is_saving_for_later                     = (bool) rgpost( 'gform_save' ) === true;
		$validate_for_first_page_activation_type = $is_last_page && $this->is_activate_on_first_page( $form );
		$force_all_page_validation               = ! $is_saving_for_later && ( $validate_for_first_page_activation_type || $this->is_page_resubmission( $form ) );

		// @todo: GF now supports all page validation on the last page, remove this in future version
		// commented out in 1.0.beta2.11
		/*if( $force_all_page_validation ) {
			$_POST["gform_source_page_number_{$form['id']}"] = 0;
		}*/

		return $form;
	}

	public function adjust_target_page_for_conditional_logic( $modified_target_page, $form ) {

		if ( ! $this->is_navigation_enabled( $form ) ) {
			return $modified_target_page;
		}

		// if target a page greater than source page +1 and not 0
		// and if this is either the official back to last page - or - a custom post to page

		$target_page = (int) rgpost( 'gform_target_page_number_' . $form['id'] );
		$source_page = (int) rgpost( 'gform_source_page_number_' . $form['id'] );

		// if we are not submitting the form ($target_page == 0) and if we are navigating more than one page from the
		// current page in either direction, we're safe.
		$forward_skip = $target_page > $source_page + 1;
		$back_skip    = $target_page < $source_page - 1;
		if ( $target_page === 0 || ( ! $forward_skip && ! $back_skip ) ) {
			return $modified_target_page;
		}

		// GF is trying to submit the form incorrectly; this often occurs when you target the last page of a form and there
		// are one or more conditionally hidden pages between the source page and the target page.
		if ( $forward_skip && $modified_target_page === 0 ) {
			$target_page = GFFormDisplay::get_max_page_number( $form );
		}
		// I wrote this and then realized there is probably never a time where we wouldn't want GF to skip conditionally hidden pages
		// except the above scenario where it incorrectly submits the form.
		// else {
		//    $diff = abs( $modified_target_page - $target_page );
		//    $target_page = $forward_skip ? $target_page - $diff : $target_page + $diff;
		//}

		return $target_page;
	}

	public function remove_validation_errors( $form ) {
		foreach ( $form['fields'] as &$field ) {
			$field->failed_validation = false;
		}
		return $form;
	}



	// # HELPERS

	public function is_last_page_reached( $form ) {
		return rgpost( 'gw_last_page_reached' ) || GFFormDisplay::is_last_page( $form, 'render' );
	}

	public function is_navigation_enabled( $form ) {
		return (bool) rgars( $form, 'pagination/' . $this->key( 'enable' ) ) === true;
	}

	public function get_activation_type( $form ) {
		$type = rgars( $form, 'pagination/' . $this->key( 'activation_type' ) );
		return $type ? $type : 'progression';
	}

	public function is_activate_on_last_page( $form ) {
		return $this->get_activation_type( $form ) === 'last_page';
	}

	public function is_activate_on_first_page( $form ) {
		return $this->get_activation_type( $form ) === 'first_page' || rgars( $form, 'pagination/type' ) !== 'steps';
	}

	public function is_bypass_validation_enabled( $form ) {
		$target_page = (int) GFFormDisplay::get_target_page( $form, GFFormDisplay::get_current_page( $form['id'] ), rgpost( 'gform_field_values' ) );
		return $this->is_activate_on_first_page( $form ) && rgpost( 'gw_bypass_validation' ) && $target_page !== 0;
	}

	public function was_final_submission_attempted( $form ) {
		return rgpost( 'gw_final_submission_attempted' ) || (string) rgpost( sprintf( 'gform_target_page_number_%s', $form['id'] ) ) === '0';
	}

	public function is_page_resubmission( $form ) {
		return GFFormDisplay::get_source_page( $form['id'] ) < $this->get_page_progression( $form );
	}

	public function get_page_progression( $form ) {
		return (int) max( intval( rgpost( 'gw_page_progression' ) ), GFFormDisplay::get_current_page( $form['id'] ) );
	}

	public function get_target_page( $form ) {

		$current_page = GFFormDisplay::get_source_page( $form['id'] );
		$field_values = GFForms::post( 'gform_field_values' );
		$target_page  = (int) GFFormDisplay::get_target_page( $form, $current_page, $field_values );

		return $target_page;
	}

}
