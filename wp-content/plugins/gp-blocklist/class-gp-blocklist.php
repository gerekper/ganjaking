<?php

if ( ! class_exists( 'GP_Plugin' ) ) {
	return;
}

class GP_Blocklist extends GP_Plugin {

	public $version = GP_BLOCKLIST_VERSION;

	protected $_version   = GP_BLOCKLIST_VERSION;
	protected $_path      = 'gp-blocklist/gp-blocklist.php';
	protected $_full_path = __FILE__;

	/**
	 * This property should remain "gp-blacklist" to maintain backwards compatibility with older versions of the
	 * Perk known as "GP Comment Blacklist" and "GP Blacklist"
	 */
	protected $_slug = 'gp-blacklist';

	protected $min_gravity_perks_version = '2.0';
	protected $min_gravity_forms_version = '2.3';
	protected $min_wp_version            = '4.9';

	public $form_id;
	public $form_meta;

	private static $_instance;

	public static function get_instance() {

		if ( self::$_instance == null ) {
			self::$_instance = isset( self::$perk_class ) ? new self( new self::$perk_class ) : new self();
		}

		return self::$_instance;
	}

	public function pre_init() {
		parent::pre_init();

		add_filter( 'gform_pre_form_settings_save', array( $this, 'save_form_setting' ) );

		if ( $this->is_gf_version_gte( '2.5-beta-1' ) ) {
			add_filter( 'gform_form_settings_fields', array( $this, 'add_form_settings_fields' ) );
		} else {
			add_filter( 'gform_form_settings', array( $this, 'add_form_setting' ), 10, 2 );
		}

	}

	public function init() {

		parent::init();

		load_plugin_textdomain( 'gp-blocklist', false, basename( dirname( __file__ ) ) . '/languages/' );

		$this->perk->enqueue_field_settings();
		add_action( 'gform_editor_js', array( $this, 'field_settings_js' ) );
		add_action( 'gws_field_settings', array( $this, 'field_settings_ui' ) );

		// Frontend
		add_filter( 'gform_validation', array( $this, 'validate' ) );

	}

	public function init_admin() {
		parent::init_admin();
		// A temporary addition to handle converting old "blacklist" settings to new "blocklist" equivalents.
		add_filter( 'gform_form_post_get_meta', array( $this, 'cleanup_form_meta' ) );
	}

	/**
	 * Replace "blacklist" settings with their "blocklist" equivalents.
	 *
	 * @param $form
	 *
	 * @return mixed
	 */
	public function cleanup_form_meta( $form ) {

		if ( isset( $form['gwcommentblacklist_enable'] ) ) {
			if ( ! rgar( $form, $this->perk->key( 'enable' ) ) ) {
				$form[ $this->perk->key( 'enable' ) ] = $form['gwcommentblacklist_enable'];
			}
			unset( $form['gwcommentblacklist_enable'] );
		}

		$settings_map = array(
			'gwcommentblacklist_enable' => $this->perk->key( 'enable' ),
		);

		foreach ( $form['fields'] as &$field ) {
			foreach ( $settings_map as $old => $new ) {
				if ( $field->$old ) {
					if ( ! $field->$new ) {
						$field->$new = $field->$old;
					}
					unset( $field->{$old} );
				}
			}
		}

		return $form;
	}

	public function tooltips( $tooltips ) {

		$tooltips[ $this->perk->key( 'form_tooltip' ) ]  = sprintf( GravityPerks::$tooltip_template, __( 'Validate against WP Disallowed Comment Keys', 'gp-blocklist' ), __( 'Enable this option to run all supported form fields through <strong>WordPress Disallowed Comment Keys</strong> validation.', 'gp-blocklist' ) );
		$tooltips[ $this->perk->key( 'field_tooltip' ) ] = sprintf( GravityPerks::$tooltip_template, __( 'Validate against WP Disallowed Comment Keys', 'gp-blocklist' ), __( 'Enable this option to run this field through WordPress Disallowed Comment Keys validation.', 'gp-blocklist' ) );

		return $tooltips;
	}

	public function add_form_settings_fields( $form_settings ) {

		foreach ( $form_settings as &$section ) {

			if ( rgar( $section, 'title' ) !== esc_html__( 'Restrictions', 'gravityforms' ) ) {
				continue;
			}

			$section['fields'][] = array(
				'name'    => $this->perk->key( 'enable' ),
				'type'    => 'checkbox',
				'label'   => __( 'Blocklist', 'gp-blocklist' ),
				'tooltip' =>
					sprintf(
						GravityPerks::$tooltip_template,
						__( 'Validate against WordPress Disallowed Comment Keys', 'gp-blocklist' ),
						sprintf(
							// Translators: %1$s is replaced with an opening anchor and %2$s is replaced with a closing anchor.
							__( 'Enable this option to run all supported form fields through WordPress %1$sDisallowed Comment Keys%2$s validation.', 'gp-blocklist' ),
							'<a href="' . admin_url( 'options-discussion.php' ) . '#:~:text=' . __( 'Disallowed Comment Keys' ) . '">',
							'</a>'
						)
					),
				'choices' => array(
					array(
						'name'  => $this->perk->key( 'enable' ),
						'label' => __( 'Validate against the WordPress Disallowed Comment Keys', 'gp-blocklist' ),
					),
				),
			);

		}

		return $form_settings;
	}

	public function add_form_setting( $settings, $form ) {

		if ( ! isset( $settings['Restrictions'] ) ) {
			$settings['Restrictions'] = array();
		}

		$settings['Restrictions']['blacklist_validation'] = sprintf(
			'<tr>
	            <th><label for="%3$s">%1$s %2$s</label></th>
	            <td>
	            	<input value="1" type="checkbox" name="%3$s" id="%3$s" %4$s>
	            	<label for="%3$s">%5$s</label>
	            </td>
	        </tr>',
			__( 'Blocklist', 'gp-blocklist' ),
			gform_tooltip( $this->perk->key( 'form_tooltip' ), '', true ),
			$this->perk->key( 'enable' ),
			checked( rgar( $form, $this->perk->key( 'enable' ) ), true, false ),
			// Translators: %1$s is replaced with an opening anchor and %2$s is replaced with a closing anchor.
			sprintf( __( 'Validate against WordPress %1$sDisallowed Comment Keys%2$s', 'gp-blocklist' ), '<a href="' . admin_url( 'options-discussion.php' ) . '#:~:text=' . urlencode( __( 'Disallowed Comment Keys' ) ) . '">', '</a>' )
		);

		return $settings;
	}

	public function save_form_setting( $form ) {

		$form[ $this->perk->key( 'enable' ) ] = rgpost( '_gform_setting_' . $this->perk->key( 'enable' ) );

		// Check for version of GF pre 2.5.
		if ( empty( $form[ $this->perk->key( 'enable' ) ] ) ) {
			$form[ $this->perk->key( 'enable' ) ] = rgpost( $this->perk->key( 'enable' ) );
		}

		return $form;
	}

	public function validate( $result ) {

		$form       = $result['form'];
		$ip         = GFFormsModel::get_ip();
		$user_agent = ( gf_apply_filters( array( 'gpcb_validate_user_agent', $form['id'] ), false ) ) ? $_SERVER['HTTP_USER_AGENT'] : '';

		foreach ( $form['fields'] as &$field ) {

			// if comment key blocking is not enabled form wide AND if the current field does not have comment key blocking enabled, skip to next field
			if ( ! $this->is_form_comment_key_blocking_enabled( $form ) && ! $this->is_comment_key_blocking_enabled( $field ) ) {
				continue;
			}

			$field_value = GFFormsModel::get_field_value( $field );

			if ( is_array( $field_value ) ) {
				$field_value = implode( ' ', $field_value );
			}

			// `wp_blacklist_check()` was deprecated in WordPress 5.5
			$fn_comment_key_blocklist = ( function_exists( 'wp_check_comment_disallowed_list' ) ) ? 'wp_check_comment_disallowed_list' : 'wp_blacklist_check';

			// if the field comes back false then it means that it passed the validation, continue to the next field.
			if ( ! $fn_comment_key_blocklist( '', '', '', $field_value, $ip, $user_agent ) ) {
				continue;
			}

			/**
			 * @deprecated
			 */
			$enable_honeypot = apply_filters( 'gpcb_enable_honeypot', false, $form, $result );

			/**
			 * Enable the Blocklist Honeypot.
			 *
			 * This feature will make it appear that the submission was successful to the
			 * submitting user; however, the submission will not actually be processed.
			 *
			 * @since 1.2.1
			 *
			 * @param bool  $enable_honeypot Defaults to false.
			 * @param array $form            The current form object.
			 * @param array $result          The current validation result (prior to Blocklist validation).
			 */
			$enable_honeypot = gf_apply_filters( array( 'gpb_enable_honeypot', $form['id'], $field->id ), $enable_honeypot, $form, $result );

			if ( $enable_honeypot ) {
				$honeypot_field_id                     = GFFormDisplay::get_max_field_id( $form ) + 1;
				$_POST[ "input_{$honeypot_field_id}" ] = true;
				$form['enableHoneypot']                = true;
				$result['is_valid']                    = true;
			} else {
				$field['failed_validation'] = true;
				/**
				 * Filter the message returned when a field fails the blocklist validation.
				 *
				 * @since 1.2.5
				 *
				 * @param string $message The default validation messsage.
				 */
				$field['validation_message'] = gf_apply_filters( array( 'gpb_validation_message', $form['id'], $field->id ), __( 'We\'re sorry, the text you entered for this field contains blocked words.', 'gp-blocklist' ) );
				$result['is_valid']          = false;
			}
		}

		$result['form'] = $form;

		return $result;
	}



	// Form Editor Settings

	public function field_settings_ui() {
		?>

		<li class="<?php echo $this->perk->key( 'field_setting' ); ?> field_setting">

			<input type="checkbox" id="<?php echo $this->perk->key( 'field_checkbox' ); ?>" value="1" onclick="SetFieldProperty('<?php echo $this->perk->key( 'enable' ); ?>', this.checked)">

			<label class="inline" for="<?php echo $this->perk->key( 'field_checkbox' ); ?>">
				<?php
				// Translators: %1$s is replaced with an opening anchor and %2$s is replaced with a closing anchor.
				printf( __( 'Validate against %1$sDisallowed Comment Keys%2$s', 'gp-blocklist' ), '<a href="' . admin_url( 'options-discussion.php' ) . '#:~:text=' . __( 'Disallowed Comment Keys' ) . '">', '</a>' );
				gform_tooltip( $this->perk->key( 'field_tooltip' ) );
				?>
			</label>

		</li>

		<?php
	}

	public function field_settings_js() {
		?>

		<script type="text/javascript">

			(function($) {

				$(document).bind('gform_load_field_settings', function(event, field, form) {

					var formEnabled = form["<?php echo $this->perk->key( 'enable' ); ?>"] == true;
					var commentBlocklistSetting = $('li.<?php echo $this->perk->key( 'field_setting' ); ?>');


					if(!formEnabled && isBlocklistField(field)) {
						commentBlocklistSetting.show();
						$("#<?php echo $this->perk->key( 'field_checkbox' ); ?>").prop('checked', field["<?php echo $this->perk->key( 'enable' ); ?>"] == true);
					} else {
						commentBlocklistSetting.hide();
					}

				});

				function isBlocklistField(field) {
					var commentBlocklistFields = ['text', 'textarea', 'name', 'address', 'email', 'website', 'post_title', 'post_content', 'post_excerpt', 'post_tags', 'post_category'];
					if($.inArray(field.type, commentBlocklistFields) != -1)
						return true;
					if(field['inputType'] && $.inArray(field.inputType, commentBlocklistFields) != -1)
						return true;
					return false;
				}

			})(jQuery);

		</script>

		<?php
	}



	// Helper Method

	public function is_form_comment_key_blocking_enabled( $form ) {
		return rgar( $form, $this->perk->key( 'enable' ) ) || rgar( $form, 'gwcommentblacklist_enable' );
	}

	public function is_comment_key_blocking_enabled( $field ) {
		return $field->{$this->perk->key( 'enable' )} || $field->{'gwcommentblacklist_enable'};
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

}

class GWCommentBlocklist extends GP_Blocklist { }

class GP_Comment_Blocklist extends GP_Blocklist { }

function gp_blocklist() {
	return GP_Blocklist::get_instance();
}

GFAddOn::register( 'GP_Blocklist' );
