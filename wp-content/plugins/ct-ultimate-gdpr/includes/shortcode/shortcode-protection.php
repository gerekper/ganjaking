<?php

/**
 * Class CT_Ultimate_GDPR_Shortcode_Protection
 */
class CT_Ultimate_GDPR_Shortcode_Protection {

	/**
	 * @var string
	 */
	private $tag = 'ultimate_gdpr_protection';

	/**
	 * CT_Ultimate_GDPR_Shortcode_Myaccount constructor.
	 */
	public function __construct() {
		add_shortcode( $this->tag, array( $this, 'process' ) );
	}

	/**
	 * Shortcode callback
	 *
	 * @param $atts
	 *
	 * @param string $content
	 *
	 * @return string
	 */
	public function process( $atts, $content = '' ) {

		$atts = shortcode_atts(
			array(
				'level' => CT_Ultimate_GDPR_Model_Group::LEVEL_NECESSARY,
			),
			$atts
		);

		// Block all (1) is in shortcode 0, essentials (2) is in shortcode 1, etc
		$atts['level']++;

		$current_level = CT_Ultimate_GDPR::instance()->get_controller_by_id( CT_Ultimate_GDPR_Controller_Cookie::ID )->get_group_level();

		return $atts['level'] > $current_level ? $this->render( $content, $atts['level'] ) : do_shortcode( $content );

	}

	/**
	 * Render shortcode template
	 *
	 * @param $content
	 *
	 * @param $level
	 *
	 * @return string
	 */
	public function render( $content, $level ) {

		ob_start();
		ct_ultimate_gdpr_locate_template(
			"/shortcode/shortcode-protection",
			true,
			array(
				'level'   => $level,
				'content' => $content,
				'label'   => CT_Ultimate_GDPR::instance()
				                             ->get_admin_controller()
				                             ->get_option_value(
					                             'cookie_protection_shortcode_label',
					                             ct_ultimate_gdpr_get_value(
						                             'cookie_protection_shortcode_label',
						                             CT_Ultimate_GDPR::instance()->get_controller_by_id( CT_Ultimate_GDPR_Controller_Cookie::ID )
						                                             ->get_default_options()
					                             ),
                                                 CT_Ultimate_GDPR_Controller_Cookie::ID
				                             )
			)
		);

		return ob_get_clean();

	}
}