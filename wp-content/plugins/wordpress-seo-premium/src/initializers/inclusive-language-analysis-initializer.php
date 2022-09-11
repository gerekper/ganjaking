<?php
// phpcs:disable Yoast.NamingConventions.ObjectNameDepth.MaxExceeded
namespace Yoast\WP\SEO\Premium\Initializers;

use WPSEO_Language_Utils;
use WPSEO_Utils;
use Yoast\WP\SEO\Conditionals\Admin_Conditional;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Initializers\Initializer_Interface;
use Yoast\WP\SEO\Presenters\Admin\Beta_Badge_Presenter;

/**
 * Inclusive_Language_Analysis_Initializer class.
 */
class Inclusive_Language_Analysis_Initializer implements Initializer_Interface {

	/**
	 * Name of the section, used as an identifier in the HTML.
	 *
	 * @var string
	 */
	public $name = 'inclusive-language';

	/**
	 * Holds the Options_Helper instance.
	 *
	 * @var Options_Helper
	 */
	protected $options_helper;

	/**
	 * The languages with inclusive language analysis support.
	 *
	 * @var string[]
	 */
	public static $languages_with_inclusive_language_support = [ 'en' ];

	/**
	 * Constructs Inclusive_Language_Analysis_Initializer.
	 *
	 * @param Options_Helper $options_helper The options helper.
	 */
	public function __construct( Options_Helper $options_helper ) {
		$this->options_helper = $options_helper;
	}

	/**
	 * Returns the conditionals based in which this loadable should be active.
	 *
	 * In this case: when on an admin page.
	 */
	public static function get_conditionals() {
		return [ Admin_Conditional::class ];
	}

	/**
	 * Registers hooks.
	 */
	public function initialize() {
		\add_filter( 'yoast_free_additional_metabox_sections', [ $this, 'add_metabox_section' ] );
		\add_filter( 'yoast_free_additional_taxonomy_metabox_sections', [ $this, 'add_metabox_section' ] );

		// Update user profile options.
		\add_action( 'personal_options_update', [ $this, 'process_user_option_update' ] );
		\add_action( 'edit_user_profile_update', [ $this, 'process_user_option_update' ] );
		\add_action( 'wpseo_render_user_profile', [ $this, 'render_user_profile_settings' ] );

		// Add meta field for the score.
		\add_filter( 'add_extra_wpseo_meta_fields', [ $this, 'add_inclusive_language_meta_field' ] );
		\add_filter( 'wpseo_taxonomy_content_fields', [ $this, 'add_inclusive_language_meta_field_for_taxonomies' ] );
	}

	/**
	 * Adds the inclusive language field to the meta fields.
	 *
	 * @param array $extra_fields The extra meta fields to add.
	 *
	 * @return array The extra fields to add, with the inclusive language field added.
	 */
	public function add_inclusive_language_meta_field( $extra_fields ) {
		$extra_fields['general']['inclusive_language_score'] = [
			'type'          => 'hidden',
			'title'         => 'inclusive_language_score',
			'default_value' => '0',
			'description'   => '',
		];

		return $extra_fields;
	}

	/**
	 * Adds the inclusive language field to the meta fields for taxonomies.
	 *
	 * @param array $additional_fields The extra meta fields to add.
	 *
	 * @return array The extra fields to add, with the inclusive language field added.
	 */
	public function add_inclusive_language_meta_field_for_taxonomies( $additional_fields ) {
		$additional_fields['inclusive_language_score'] = [
			'label'       => '',
			'description' => '',
			'type'        => 'hidden',
			'options'     => '',
			'hide'        => false,
		];

		return $additional_fields;
	}

	/**
	 * Adds a metabox section.
	 *
	 * @param array $sections The sections to add.
	 *
	 * @return array
	 */
	public function add_metabox_section( $sections ) {
		if ( ! $this->is_enabled() || \version_compare( $this->get_wordpress_seo_version(), '19.6', '==' ) ) {
			return $sections;
		}

		$sections[] = [
			'name'         => $this->name,
			'link_content' => \sprintf(
				'<div class="wpseo-score-icon-container" id="wpseo-inclusive-language-score-icon"></div><span>%1$s</span>&nbsp;%2$s',
				\esc_html__( 'Inclusive language', 'wordpress-seo-premium' ),
				new Beta_Badge_Presenter( 'inclusive-language-beta-badge' )
			),
			'content'      => \sprintf(
				'<div id="wpseo-metabox-%1$s-root" class="wpseo-metabox-root"></div>',
				\esc_attr( $this->name )
			),
		];

		return $sections;
	}

	/**
	 * Gets the WordPress SEO version if defined, returns null otherwise.
	 *
	 * @return string|null The WordPress SEO version or null when premium version is not defined.
	 */
	public function get_wordpress_seo_version() {
		if ( \defined( 'WPSEO_VERSION' ) ) {
			return \WPSEO_VERSION;
		}

		return null;
	}

	/**
	 * Whether this analysis is enabled.
	 *
	 * @return bool Whether this analysis is enabled.
	 */
	public function is_enabled() {
		return $this->is_globally_enabled()
			&& $this->is_user_enabled()
			&& $this->is_current_version_supported()
			&& $this->has_inclusive_language_support( WPSEO_Language_Utils::get_language( \get_locale() ) );
	}

	/**
	 * Whether this analysis is enabled by the user.
	 *
	 * @return bool Whether this analysis is enabled by the user.
	 */
	public function is_user_enabled() {
		return ! \get_the_author_meta( 'wpseo_inclusive_language_analysis_disable', get_current_user_id() );
	}

	/**
	 * Whether this analysis is enabled globally.
	 *
	 * @return bool Whether this analysis is enabled globally.
	 */
	public function is_globally_enabled() {
		return $this->options_helper->get( 'inclusive_language_analysis_active', false );
	}

	/**
	 * Checks whether the given language has inclusive language support.
	 *
	 * @param string $language The language to check if inclusive language is supported.
	 *
	 * @return bool Whether the language has inclusive language support.
	 */
	public function has_inclusive_language_support( $language ) {
		return \in_array( $language, self::$languages_with_inclusive_language_support, true );
	}

	/**
	 * Whether or not a certain premium version support inclusive language feature.
	 *
	 * @return bool Whether or not a certain premium version support inclusive language feature.
	 */
	public function is_current_version_supported() {
		return \version_compare( \WPSEO_PREMIUM_VERSION, '19.2-RC1', '>=' );
	}

	/**
	 * Renders the user profile settings.
	 *
	 * @param \WP_User $user The WP user.
	 *
	 * @return void
	 */
	public function render_user_profile_settings( $user ) {
		if ( ! $this->is_globally_enabled() ) {
			return;
		}

		$is_user_enabled = \get_the_author_meta( 'wpseo_inclusive_language_analysis_disable', $user->ID ) === 'on';

		\printf(
			'<input class="yoast-settings__checkbox double" type="checkbox" id="wpseo_inclusive_language_analysis_disable" name="wpseo_inclusive_language_analysis_disable" aria-describedby="wpseo_inclusive_language_analysis_disable_desc" value="on" %1$s />
			<label class="yoast-label-strong" for="wpseo_inclusive_language_analysis_disable">%2$s</label>
			<br>
			<p class="description" id="wpseo_inclusive_language_analysis_disable_desc">%3$s</p>',
			( $is_user_enabled ) ? 'checked' : '',
			\esc_html__( 'Disable inclusive language analysis', 'wordpress-seo-premium' ),
			\esc_html__( 'Removes the inclusive language analysis section from the metabox and disables all inclusive language-related suggestions.', 'wordpress-seo-premium' )
		);
	}

	/**
	 * Updates the user metas that (might) have been set on the user profile page.
	 *
	 * @param int $user_id User ID of the updated user.
	 */
	public function process_user_option_update( $user_id ) {
		$nonce_value = $this->filter_input_post( 'wpseo_nonce' );

		if ( empty( $nonce_value ) ) { // Submit from alternate forms.
			return;
		}

		\check_admin_referer( 'wpseo_user_profile_update', 'wpseo_nonce' );

		\update_user_meta( $user_id, 'wpseo_inclusive_language_analysis_disable', $this->filter_input_post( 'wpseo_inclusive_language_analysis_disable' ) );
	}

	/**
	 * Filter POST variables.
	 *
	 * @param string $var_name Name of the variable to filter.
	 *
	 * @return string The value.
	 */
	private function filter_input_post( $var_name ) {
		$val = filter_input( INPUT_POST, $var_name );
		if ( $val ) {
			return WPSEO_Utils::sanitize_text_field( $val );
		}

		return '';
	}
}
