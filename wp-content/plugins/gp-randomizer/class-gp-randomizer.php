<?php

if ( ! class_exists( 'GP_Plugin' ) ) {
	return;
}

class GP_Randomizer extends GP_Plugin {

	private static $instance = null;

	protected $_version     = GP_RANDOMIZER_VERSION;
	protected $_path        = 'gp-randomizer/gp-randomizer.php';
	protected $_full_path   = __FILE__;
	protected $_slug        = 'gp-randomizer';
	protected $_title       = 'Gravity Wiz Randomizer';
	protected $_short_title = 'Randomizer';

	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function minimum_requirements() {
		return array(
			'gravityforms' => array(
				'version' => '2.3-rc-1',
			),
			'wordpress'    => array(
				'version' => '4.8',
			),
			'plugins'      => array(
				'gravityperks/gravityperks.php' => array(
					'name'    => 'Gravity Perks',
					'version' => '2.0',
				),
			),
		);
	}

	public function init() {

		parent::init();

		load_plugin_textdomain( 'gp-randomizer', false, basename( dirname( __file__ ) ) . '/languages/' );

		add_action( 'gform_field_standard_settings', array( $this, 'field_settings_ui' ) );
		add_action( 'gform_editor_js', array( $this, 'field_settings_js' ) );
		add_action( 'gform_register_init_scripts', array( $this, 'register_init_scripts' ) );

		add_filter( 'gform_field_css_class', array( $this, 'maybe_add_randomize_class_to_field' ), 10, 3 );
		add_filter( 'gform_field_container', array( $this, 'add_default_order_as_attr_to_rank_fields' ), 10, 6 );

		// The priority has been increased to 11 to avoid other plugins like GF-WPA that filters out the Gravity Form form tag.
		add_filter( 'gform_form_tag', array( $this, 'maybe_add_seed' ), 11, 2 );

	}

	public function init_admin() {
		parent::init_admin();

		GravityPerks::enqueue_field_settings();
	}

	public function is_enabled_in_form( $form ) {
		if ( empty( $form['fields'] ) ) {
			return false;
		}

		foreach ( $form['fields'] as $field ) {
			if ( $this->is_enabled( $field ) ) {
				return true;
			}
		}

		return false;
	}

	public function is_enabled( $field ) {
		return ! ! rgar( $field, 'gprRandomizeChoiceOrder' );
	}

	public function maybe_add_randomize_class_to_field( $css_class, $field, $form ) {
		if ( ! $this->is_enabled( $field ) ) {
			return $css_class;
		}

		return $css_class . ' gpr_randomize_field';
	}

	/**
	 * Add the default order as an attribute to the rank field container so we can know if the order is the default
	 * prior to shuffling to prevent the ranks from being lost if there's a validation issue.
	 *
	 * @param $field_container
	 * @param $field
	 * @param $form
	 * @param $css_class
	 * @param $style
	 * @param $field_content
	 *
	 * @return mixed
	 */
	public function add_default_order_as_attr_to_rank_fields( $field_container, $field, $form, $css_class, $style, $field_content ) {
		if ( $field->get_input_type() !== 'rank' ) {
			return $field_container;
		}

		$choices = wp_list_pluck( $field->choices, 'value' );
		$default_order_attr = ' data-default-order="' . esc_attr( json_encode( $choices ) ) . '"';

		return str_replace( ' class=', $default_order_attr . ' class=', $field_container );
	}

	/**
	 * Add hidden input to the form to seed the JavaScript choice randomizer.
	 *
	 * If the form is submitted and fails validation, the seed remains the same that way choices don't continue to
	 * randomize during validation.
	 *
	 * @param $form_tag string Opening <form> tag to be filtered
	 * @param $form array Current form being rendered
	 *
	 * @return string
	 */
	public function maybe_add_seed( $form_tag, $form ) {
		if ( ! $this->is_enabled_in_form( $form ) ) {
			return $form_tag;
		}

		$seed_value = gf_apply_filters( array( 'gpr_seed', $form['id'] ), rgar( $_POST, 'gpr_seed', rand() ), $form );
		$seed_input = "\n" . '<input type="hidden" name="gpr_seed" value="' . esc_attr( $seed_value ) . '" />' . "\n";

		return $form_tag . $seed_input;
	}

	public function scripts() {

		$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG || isset( $_GET['gform_debug'] ) ? '' : '.min';

		$scripts = array(
			array(
				'handle'    => 'gp-randomizer',
				'src'       => $this->get_base_url() . '/js/gp-randomizer' . $min . '.js',
				'version'   => $this->_version,
				'deps'      => array( 'gform_gravityforms' ),
				'in_footer' => true,
				'enqueue'   => array(
					array( $this, 'should_enqueue_frontend' ),
				),
			),
		);

		return array_merge( parent::scripts(), $scripts );

	}

	public function register_init_scripts( $form ) {
		if ( ! $this->should_enqueue_frontend( $form ) ) {
			return;
		}

		$script = <<<JS
		window['gpRandomizer_{$form['id']}'] = new GPRandomizer({$form['id']});
JS;


		GFFormDisplay::add_init_script( $form['id'], 'gpr_init_script', GFFormDisplay::ON_PAGE_RENDER, $script );
	}

	/**
	 * Determine if frontend scripts/styles should be enqueued.
	 *
	 * @param $form
	 *
	 * @return bool
	 */
	public function should_enqueue_frontend( $form ) {
		if ( GFCommon::is_form_editor() ) {
			return false;
		}

		return $this->is_enabled_in_form( $form );
	}

	## Admin Field Settings

	public function field_settings_ui( $position ) {
		if ( $position !== 1360 ) {
			return;
		}
		?>

		<li class="gpr-field-setting field_setting" style="display:none;">
			<input type="checkbox" value="1" id="gpr-randomize-choice-order"
				   onchange="SetFieldProperty( 'gprRandomizeChoiceOrder', this.checked );"/>
			<label for="gpr-randomize-choice-order" class="inline">
				<?php _e( 'Randomize Choice Order' ); ?>
				<?php gform_tooltip( $this->_slug . '_randomize_choice_order' ); ?>
			</label>
		</li>

		<?php
	}

	public function field_settings_js() {
		?>
		<script type="text/javascript">
			(function ($) {
				var gprSupportedFields = gform.applyFilters('gpr_supported_fields', [
					'select',
					'multiselect',
					'checkbox',
					'radio',
					'rank',
				]);

				$(document).ready(function () {
					for (fieldType in fieldSettings) {
						if (fieldSettings.hasOwnProperty(fieldType) && $.inArray(fieldType, gprSupportedFields) !== -1) {
							fieldSettings[fieldType] += ', .gpr-field-setting';
						}
					}
				});

				$(document).bind('gform_load_field_settings', function (event, field, form) {
					$('#gpr-randomize-choice-order').prop('checked', !!field['gprRandomizeChoiceOrder']);
				});

			})(jQuery);
		</script>
		<?php
	}

	public function tooltips( $tooltips ) {
		$tooltips[ $this->_slug . '_randomize_choice_order' ] = sprintf(
			'<h6>%s</h6> %s',
			__( 'GP Randomizer', 'gp-randomizer' ),
			__( 'Randomize choice order when the form is displayed. Randomized choice order will be preserved if the form submission fails validation.', 'gp-randomizer' )
		);

		return $tooltips;
	}

}

function gp_randomizer() {
	return GP_Randomizer::get_instance();
}

GFAddOn::register( 'GP_Randomizer' );
