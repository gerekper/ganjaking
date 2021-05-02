<?php

if ( ! class_exists( 'GP_Feed_Plugin' ) ) {
	return;
}

class GP_Limit_Submissions extends GP_Feed_Plugin {

	/**
	 * Contains an instance of this class, if available.
	 *
	 * @since 0.9
	 * @access private
	 * @var GP_Limit_Submissions $_instance If available, contains an instance of this class
	 */
	private static $_instance = null;

	/**
	 * Defines the version of the GP Limit Submissions Add-On.
	 *
	 * @since 0.9
	 * @access protected
	 * @var string $_version Contains the version.
	 */
	protected $_version = GP_LIMIT_SUBMISSIONS_VERSION;
	/**
	 * Defines the plugin slug.
	 *
	 * @since 0.9
	 * @access protected
	 * @var string $_slug The slug used for this plugin.
	 */
	protected $_slug = 'gp-limit-submissions';
	/**
	 * Defines the main plugin file.
	 *
	 * @since 0.9
	 * @access protected
	 * @var string $_path The path to the main plugin file, relative to the plugins folder.
	 */
	protected $_path = 'gp-limit-submissions/gp-limit-submissions.php';
	/**
	 * Defines the full path to this class file.
	 *
	 * @since 0.9
	 * @access protected
	 * @var string $_full_path The full path.
	 */
	protected $_full_path = __FILE__;
	/**
	 * Defines the URL where this add-on can be found.
	 *
	 * @since 0.9
	 * @access protected
	 * @var string
	 */
	protected $_url = 'http://gravitywiz.com/documentation/gravity-forms-limit-submissions/';
	/**
	 * Defines the title of this add-on.
	 *
	 * @since 0.9
	 * @access protected
	 * @var string $_title The title of the add-on.
	 */
	protected $_title = 'GP Limit Submissions';
	/**
	 * Defines the short title of the add-on.
	 *
	 * @since 0.9
	 * @access protected
	 * @var string $_short_title The short title.
	 */
	protected $_short_title = 'Limit Submissions';
	/**
	 * If true, users can configure what order feeds are executed in from the feed list page.
	 * @var bool
	 */
	protected $_supports_feed_ordering = true;

	/**
	 * @var string|array A string or an array of capabilities or roles that have access to the form settings
	 */
	protected $_capabilities_form_settings = 'gp_limit_submissions_form_settings';

	/**
	 * @var GPLS_Enforce;
	 */
	public $enforce;

	/**
	 * Returns an instance of this class, and stores it in the $_instance property.
	 *
	 * @since 0.9
	 * @access public
	 * @static
	 * @return GP_Limit_Submissions $_instance An instance of the GP_Limit_Submissions class
	 */
	public static function get_instance() {

		if ( self::$_instance == null ) {
			self::includes();
			self::$_instance = isset( self::$perk_class ) ? new self( new self::$perk_class ) : new self();
		}

		return self::$_instance;

	}

	public static function includes() {

		require_once( plugin_dir_path( __FILE__ ) . '/includes/GPLS_Interface.php' );
		require_once( plugin_dir_path( __FILE__ ) . '/includes/GPLS_RuleGroup.php' );
		require_once( plugin_dir_path( __FILE__ ) . '/includes/GPLS_Rule.php' );
		require_once( plugin_dir_path( __FILE__ ) . '/includes/GPLS_RuleTest.php' );
		require_once( plugin_dir_path( __FILE__ ) . '/includes/GPLS_Enforce.php' );
		require_once( plugin_dir_path( __FILE__ ) . '/includes/GPLS_Rule_Ip.php' );
		require_once( plugin_dir_path( __FILE__ ) . '/includes/GPLS_Rule_User.php' );
		require_once( plugin_dir_path( __FILE__ ) . '/includes/GPLS_Rule_Role.php' );
		require_once( plugin_dir_path( __FILE__ ) . '/includes/GPLS_Rule_Field.php' );
		require_once( plugin_dir_path( __FILE__ ) . '/includes/GPLS_Rule_Embed_Url.php' );
		require_once( plugin_dir_path( __FILE__ ) . '/includes/GPLS_Shortcode.php' );

	}

	private function __clone() {
		/* do nothing */
	}

	public function init() {

		parent::init();

		// Enforce limits
		$this->enforce = new GPLS_Enforce();

		// Create shortcode
		new GPLS_Shortcode();

		add_filter( 'admin_body_class', array( $this, 'add_helper_body_classes' ) );

	}

	public function minimum_requirements() {
		return array(
			'gravityforms' => array(
				'version' => '2.3-beta-1',
			),
			'wordpress'    => array(
				'version' => '4.9',
			),
			'plugins'      => array(
				'gravityperks/gravityperks.php' => array(
					'name'    => 'Gravity Perks',
					'version' => '2.2.3',
				),
			),
		);
	}

	public function feed_settings_fields() {
		return array(
			array(
				'title'  => 'General Settings',
				'fields' => array(
					array(
						'label'    => __( 'Limit Feed Name', 'gp-limit-submissions' ),
						'type'     => 'text',
						'name'     => 'rule_group_name',
						'class'    => 'medium',
						'tooltip'  => __( 'Enter a name for this limit feed.', 'gp-limit-submissions' ),
						'required' => true,
					),
					array(
						'label'    => __( 'Submission Limit', 'gp-limit-submissions' ),
						'type'     => 'text',
						'class'    => 'small', /* Not needed in GF 2.5+ */
						'name'     => 'rule_submission_limit',
						'tooltip'  => __( 'Specify the number of entries that may be submitted if this limit feed applies.', 'gp-limit-submissions' ),
						'required' => true,
						'style'    => 'width:auto;',
					),
					array(
						'label'   => __( 'Time Period', 'gp-limit-submissions' ),
						'type'    => 'rule_time_period_field',
						'name'    => 'rule_limit_time_period',
						'tooltip' => $this->get_time_period_setting_description(),
					),
					array(
						'label'         => __( 'Limit Message', 'gp-limit-submissions' ),
						'type'          => 'textarea',
						'name'          => 'rule_limit_message',
						'tooltip'       => __( 'Specify a message that will be displayed to users if their submission is limited or if the form\'s submission limit is reached.', 'gp-limit-submissions' ),
						'class'         => 'large merge-tag-support mt-prepopulate mt-position-right',
						'default_value' => __( 'The submission limit has been reached for this form.', 'gp-limit-submissions' ),
					),
				),
			),
			array(
				'title'  => 'Rules',
				'fields' => array(
					array(
						'label'   => __( 'Rule Groups', 'gp-limit-submissions' ),
						'type'    => 'rule_rules_field',
						'name'    => 'rule_rules',
						'tooltip' => __( 'Create groups of rules that determine whether this limit feed applies. Add a rule to your group with the (+)/(-) icons to the right. All rules in a group must be true for the group to match. Add another group by clicking the "Add Rule Group" button below. If any group matches, this limit feed will apply.', 'gp-limit-submissions' ),
					),
				),
			),

		);
	}

	public function get_time_period_setting_description() {
		$generic   = __( 'Specify a time period for which the this limit feed applies. Only entries submitted during this time period will count towards the submission limit.', 'gp-limit-submissions' );
		$specifics = array(
			'Forever'         => __( 'All existing entries that match the rules for this limit feed will count towards the submission limit.', 'gp-limit-submissions' ),
			'Time Period'     => __( 'Only entries that fall within the specified time period (counting back from the current time) will count towards the submission limit.', 'gp-limit-submissions' ),
			'Calendar Period' => __( 'Only entries that fall with the specified calendar period will count towards the submission limit.', 'gp-limit-submissions' ),
			'Form Schedule'   => __( 'Only entires that fall within the specified form schedule will count towards the submission limit. You can configure your form schedule on the Form Settings page via the "Schedule form" setting.', 'gp-limit-submissions' ),
		);
		foreach ( $specifics as $label => &$description ) {
			$description = "<br><strong>{$label}</strong>{$description}";
		}
		return sprintf( '%s<br><ul><li>%s</li></ul>', $generic, implode( '</li><li>', $specifics ) );
	}

	public function settings_rule_time_period_field() {

		$this->settings_select(
			array(
				'label'   => __( 'Time Period Type', 'gp-limit-submissions' ),
				'name'    => 'rule_time_period_type',
				'class'   => '',
				'tooltip' => __( 'Choose time period settings', 'gp-limit-submissions' ),
				'choices' => array(
					array(
						'label' => __( 'Forever', 'gp-limit-submissions' ),
						'value' => 'forever',
					),
					array(
						'label' => __( 'Time Period', 'gp-limit-submissions' ),
						'value' => 'time_period',
					),
					array(
						'label' => __( 'Calendar Period', 'gp-limit-submissions' ),
						'value' => 'calendar_period',
					),
					array(
						'label' => __( 'Form Schedule', 'gp-limit-submissions' ),
						'value' => 'form_schedule',
					),
				),
			)
		);

		$this->settings_select(
			array(
				'label'   => __( 'Calendar Period', 'gp-limit-submissions' ),
				'name'    => 'rule_calendar_period',
				'tooltip' => __( 'Enter the value for the time period', 'gp-limit-submissions' ),
				'class'   => '',
				'choices' => array(
					array(
						'label' => __( 'Per Day', 'gp-limit-submissions' ),
						'value' => 'day',
					),
					array(
						'label' => __( 'Per Week', 'gp-limit-submissions' ),
						'value' => 'week',
					),
					array(
						'label' => __( 'Per Month', 'gp-limit-submissions' ),
						'value' => 'month',
					),
					array(
						'label' => __( 'Per Quarter', 'gp-limit-submissions' ),
						'value' => 'quarter'
					),
					array(
						'label' => __( 'Per Year', 'gp-limit-submissions' ),
						'value' => 'year',
					),
				),
			)
		);

		$this->settings_text(
			array(
				'label'       => __( 'Time Period Value', 'gp-limit-submissions' ),
				'name'        => 'rule_time_period_value',
				'tooltip'     => __( 'Enter the type of calendar time period', 'gp-limit-submissions' ),
				'class'       => '',
				'placeholder' => __( 'Enter a number (i.e. 3)', 'gp-limit-submissions' ),
			)
		);

		$this->settings_select(
			array(
				'label'         => __( 'Time Period Unit', 'gp-limit-submissions' ),
				'name'          => 'rule_time_period_unit',
				'class'         => '',
				'tooltip'       => __( 'Choose time period', 'gp-limit-submissions' ),
				'default_value' => 'days',
				'choices'       => array(
					array(
						'label' => __( 'second(s)', 'gp-limit-submissions' ),
						'value' => 'seconds',
					),
					array(
						'label' => __( 'minute(s)', 'gp-limit-submissions' ),
						'value' => 'minutes',
					),
					array(
						'label' => __( 'hour(s)', 'gp-limit-submissions' ),
						'value' => 'hours',
					),
					array(
						'label' => __( 'day(s)', 'gp-limit-submissions' ),
						'value' => 'days',
					),
					array(
						'label' => __( 'week(s)', 'gp-limit-submissions' ),
						'value' => 'weeks',
					),
					array(
						'label' => __( 'month(s)', 'gp-limit-submissions' ),
						'value' => 'months',
					),
					array(
						'label' => __( 'year(s)', 'gp-limit-submissions' ),
						'value' => 'years',
					),
				),
			)
		);

	}

	public function feed_list_columns() {
		return array(
			'rule_group_name'       => __( 'Limit Feed', 'gp-limit-submissions' ),
			'rule_submission_limit' => __( 'Submission Limit', 'gp-limit-submissions' ),
		);
	}

	public function feed_settings_title() {
		$settings = $this->get_feed( $this->get_current_feed_id() );
		$name     = $this->get_setting( 'rule_group_name', null, rgar( $settings, 'meta' ) );
		$base     = esc_html__( 'Limit Feed', 'gp-limit-submission' );
		return $name ? sprintf( '%s: %s', $base, $name ) : sprintf( 'New %s', $base );
	}

	public function settings_rule_rules_field() {

		$ruleObj = new GPLS_Interface( $this );

		// stored limit rules
		$rules_data = $this->get_setting( 'limit_rules_data' );

		// get existing rules
		$existing_rules = $ruleObj->existing_rules( $rules_data );

		// localize repeater items
		wp_localize_script( 'gpls-script', 'gpls_repeater_default_rule', $ruleObj->default_rule() );
		wp_localize_script( 'gpls-script', 'gpls_repeater_items', $existing_rules );

		?>

		<div class="ruleset">

			<?php
				$ruleObj->render_data_storage_field();
				$ruleObj->render_rule_groups( $existing_rules );
			?>

		</div><!-- / end ruleset -->

		 <!-- Add Rule Group -->
		 <div class="gpls-add-rule-group">
			<button id="add_rule_group" class="button button-secondary"><i class="gficon-add"></i> Add Rule Group</button>
		 </div>

		 <!-- Debug -->
		 <div id="debug"></div>

		<?php

	}

	public function scripts() {

		// Don't include select2 on non-GF pages. Solves issues with ACF where our version of select2 is registered but it is expecting it's own.
		if ( ! GFForms::is_gravity_page() ) {
			return parent::scripts();
		}

		$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG || isset( $_GET['gform_debug'] ) ? '' : '.min';

		$scripts   = array();
		$scripts[] = array(
			'handle'  => 'gpls-validate',
			'src'     => gp_limit_submissions()->get_base_url() . "/js/validation{$min}.js",
			'version' => gp_limit_submissions()->_version,
			'deps'    => array( 'jquery' ),
			'enqueue' => array(
				array(
					'admin_page' => 'form_settings',
					'tab'        => $this->_slug,
				),
			),
		);

		$deps = array( 'jquery', 'gaddon_repeater', 'gform_gravityforms', 'gpls-validate' );

		if ( ! $this->is_gf_version_gte( '2.5-beta-1' ) ) {
			$deps[]    = 'select2';
			$scripts[] = array(
				'handle'    => 'select2',
				'src'       => gp_limit_submissions()->get_base_url() . "/js/select2{$min}.js",
				'version'   => gp_limit_submissions()->_version,
				'deps'      => array(),
				'enqueue'   => array(
					array(
						'admin_page' => 'form_settings',
						'tab'        => $this->_slug,
					),
				),
				'in_footer' => true,
			);
		} else {
			$deps[] = 'gform_selectwoo';
		}

		$scripts[] = array(
			'handle'    => 'gpls-script',
			'src'       => gp_limit_submissions()->get_base_url() . "/js/gpls{$min}.js",
			'version'   => gp_limit_submissions()->_version,
			'deps'      => $deps,
			'enqueue'   => array(
				array(
					'admin_page' => 'form_settings',
					'tab'        => $this->_slug,
				),
			),
			'in_footer' => true,
		);

		return array_merge( parent::scripts(), $scripts );
	}

	public function styles() {

		// Don't include select2 on non-GF pages. Solves issues with ACF where our version of select2 is registered but it is expecting it's own.
		if ( ! GFForms::is_gravity_page() ) {
			return parent::styles();
		}

		$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG || isset( $_GET['gform_debug'] ) ? '' : '.min';

		$styles = array(
			array(
				'handle'  => 'gpls-style',
				'src'     => gp_limit_submissions()->get_base_url() . "/css/gpls{$min}.css",
				'version' => gp_limit_submissions()->_version,
				'enqueue' => array(
					array(
						'admin_page' => 'form_settings',
						'tab'        => $this->_slug,
					),
				),
			),
			array(
				'handle'  => 'select2',
				'src'     => gp_limit_submissions()->get_base_url() . "/css/select2{$min}.css",
				'version' => gp_limit_submissions()->_version,
				'enqueue' => array(
					array(
						'admin_page' => 'form_settings',
						'tab'        => $this->_slug,
					),
				),
			),
		);

		return array_merge( parent::styles(), $styles );
	}

	public function add_helper_body_classes( $body_class ) {
		if ( isset( $_GET['subview'] ) && $_GET['subview'] === $this->_slug ) {
			$body_class .= ' gpls-loading';
		}
		return $body_class;
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

/**
 * Returns an instance of the GP_Limit_Submissions class
 *
 * @since 0.9
 * @return GP_Limit_Submissions An instance of the GP_Limit_Submissions class
 */
function gp_limit_submissions() {
	return GP_Limit_Submissions::get_instance();
}

GFFeedAddOn::register( 'GP_Limit_Submissions' );
