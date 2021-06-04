<?php

namespace Yoast\WP\SEO\Premium\Integrations\Admin\Social_Templates;

use WP_Taxonomy;
use WPSEO_Admin_Editor_Specific_Replace_Vars;
use WPSEO_Admin_Recommended_Replace_Vars;
use WPSEO_Replacevar_Editor;
use Yoast\WP\SEO\Premium\Config\Badge_Group_Names;
use Yoast\WP\SEO\Integrations\Integration_Interface;
use Yoast\WP\SEO\Premium\Conditionals\Social_Templates_Conditional;
use Yoast_Form;

/**
 * Class Social_Templates_Integration.
 *
 * Adds the social fields to the meta tabs for post types, taxonomies and archives.
 */
class Social_Templates_Integration implements Integration_Interface {

	/**
	 * Service that can be used to recommend a set of variables for a WPSEO_Replacevar_Editor.
	 *
	 * @var WPSEO_Admin_Recommended_Replace_Vars
	 */
	private $recommended_replace_vars;

	/**
	 * Service that can be used to recommend an editor specific set of variables for a WPSEO_Replacevar_Editor.
	 *
	 * @var WPSEO_Admin_Editor_Specific_Replace_Vars
	 */
	private $editor_specific_replace_vars;

	/**
	 * Group to which the 'New' badges belong to.
	 *
	 * @var string
	 */
	private $group;

	/**
	 * Returns the conditionals based on which this loadable should be active.
	 *
	 * @return array The conditionals that must be met to load this.
	 */
	public static function get_conditionals() {
		return [
			Social_Templates_Conditional::class,
		];
	}

	/**
	 * Social_Templates_Integration constructor.
	 */
	public function __construct() {
		$this->recommended_replace_vars     = new WPSEO_Admin_Recommended_Replace_Vars();
		$this->editor_specific_replace_vars = new WPSEO_Admin_Editor_Specific_Replace_Vars();
		$this->group                        = 'global-templates';
	}

	/**
	 * Initializes the integration.
	 */
	public function register_hooks() {
		\add_action( 'Yoast\WP\SEO\admin_author_archives_meta', [ $this, 'social_author_archives' ] );
		\add_action( 'Yoast\WP\SEO\admin_date_archives_meta', [ $this, 'social_date_archives' ] );
		\add_action( 'Yoast\WP\SEO\admin_post_types_meta', [ $this, 'social_post_type' ], 8, 2 );
		\add_action( 'Yoast\WP\SEO\admin_post_types_archive', [ $this, 'social_post_types_archive' ], 10, 2 );
		\add_action( 'Yoast\WP\SEO\admin_taxonomies_meta', [ $this, 'social_taxonomies' ], 10, 2 );
	}

	/**
	 * Build a set of social fields for the author archives in the Search Appearance section.
	 *
	 * @param Yoast_Form $yform The form builder.
	 */
	public function social_author_archives( $yform ) {
		$identifier            = 'author-wpseo';
		$page_type_recommended = $this->recommended_replace_vars->determine_for_archive( 'author' );
		$page_type_specific    = $this->editor_specific_replace_vars->determine_for_archive( 'author' );

		$this->build_social_fields( $yform, $identifier, $page_type_recommended, $page_type_specific );
	}

	/**
	 * Build a set of social fields for the date archives in the Search Appearance section.
	 *
	 * @param Yoast_Form $yform The form builder.
	 */
	public function social_date_archives( $yform ) {
		$identifier            = 'archive-wpseo';
		$page_type_recommended = $this->recommended_replace_vars->determine_for_archive( 'date' );
		$page_type_specific    = $this->editor_specific_replace_vars->determine_for_archive( 'date' );

		$this->build_social_fields( $yform, $identifier, $page_type_recommended, $page_type_specific );
	}

	/**
	 * Build a set of social fields for the post types in the Search Appearance section.
	 *
	 * @param Yoast_Form $yform          The form builder.
	 * @param string     $post_type_name The name of the current post_type that gets the social fields added.
	 */
	public function social_post_type( $yform, $post_type_name ) {
		if ( $post_type_name === 'attachment' ) {
			return;
		}

		$page_type_recommended = $this->recommended_replace_vars->determine_for_post_type( $post_type_name );
		$page_type_specific    = $this->editor_specific_replace_vars->determine_for_post_type( $post_type_name );

		$this->build_social_fields( $yform, $post_type_name, $page_type_recommended, $page_type_specific );
	}

	/**
	 * Build a set of social fields for the post types archives in the Search Appearance section.
	 *
	 * @param Yoast_Form $yform          The form builder.
	 * @param string     $post_type_name The name of the current post_type that gets the social fields added.
	 */
	public function social_post_types_archive( $yform, $post_type_name ) {
		$identifier            = 'ptarchive-' . $post_type_name;
		$page_type_recommended = $this->recommended_replace_vars->determine_for_archive( $post_type_name );
		$page_type_specific    = $this->editor_specific_replace_vars->determine_for_archive( $post_type_name );

		$this->build_social_fields( $yform, $identifier, $page_type_recommended, $page_type_specific );
	}

	/**
	 * Build a set of social fields for the taxonomies in the Search Appearance section.
	 *
	 * @param Yoast_Form  $yform    The form builder.
	 * @param WP_Taxonomy $taxonomy The taxonomy that gets the social fields added.
	 */
	public function social_taxonomies( $yform, $taxonomy ) {
		$identifier            = 'tax-' . $taxonomy->name;
		$page_type_recommended = $this->recommended_replace_vars->determine_for_term( $taxonomy->name );
		$page_type_specific    = $this->editor_specific_replace_vars->determine_for_term( $taxonomy->name );

		$this->build_social_fields( $yform, $identifier, $page_type_recommended, $page_type_specific );
	}

	/**
	 * Build a set of social fields for the Search Appearance section.
	 *
	 * @param Yoast_Form $yform                 The form builder.
	 * @param string     $identifier            A page-wide unique identifier for data storage and unique DOM elements.
	 * @param string     $page_type_recommended Recommended type of page for a list of replaceable variables.
	 * @param string     $page_type_specific    Editor specific type of page for a list of replaceable variables.
	 */
	protected function build_social_fields( Yoast_Form $yform, $identifier, $page_type_recommended, $page_type_specific ) {
		$image_url_field_id = 'social-image-url-' . $identifier;
		$image_id_field_id  = 'social-image-id-' . $identifier;
		$badge_group_names  = new Badge_Group_Names();

		echo '<div class="yoast-settings-section">';

		$yform->hidden( $image_url_field_id, $image_url_field_id );
		$yform->hidden( $image_id_field_id, $image_id_field_id );
		\printf(
			'<div
				id="%1$s"
				data-react-image-portal
				data-react-image-portal-target-image="%2$s"
				data-react-image-portal-target-image-id="%3$s"
				data-react-image-portal-has-new-badge="%4$s"
			></div>',
			\esc_attr( 'yoast-social-' . $identifier . '-image-select' ),
			\esc_attr( $image_url_field_id ),
			\esc_attr( $image_id_field_id ),
			\esc_attr( $badge_group_names->is_still_eligible_for_new_badge( $this->group ) )
		);

		$editor = new WPSEO_Replacevar_Editor(
			$yform,
			[
				'title'                   => 'social-title-' . $identifier,
				'description'             => 'social-description-' . $identifier,
				'page_type_recommended'   => $page_type_recommended,
				'page_type_specific'      => $page_type_specific,
				'paper_style'             => false,
				'label_title'             => \__( 'Social title', 'wordpress-seo-premium' ),
				'label_description'       => \__( 'Social description', 'wordpress-seo-premium' ),
				'description_placeholder' => \__( 'Modify your social description by editing it right here.', 'wordpress-seo-premium' ),
				'has_new_badge'           => $badge_group_names->is_still_eligible_for_new_badge( $this->group ),
			]
		);
		$editor->render();

		echo '</div>';
	}
}
