<?php

namespace wpbuddy\rich_snippets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


/**
 * Class YoastSEO_Model.
 *
 * Recognizes Yoast SEO plugin and provides new fields.
 *
 * @package wpbuddy\rich_snippets
 *
 * @since   2.2.0
 */
final class YoastSEO_Model {

	/**
	 * @param $values
	 *
	 * @return mixed
	 */
	public static function internal_subselect( $values ) {

		if ( false === Helper_Model::instance()->is_yoast_seo_active() ) {
			return $values;
		}

		$values['http://schema.org/Text'][] = array(
			'id'     => 'yoast_seo_title',
			'label'  => esc_html_x( 'Yoast SEO: SEO Title', 'subselect field', 'rich-snippets-schema' ),
			'method' => array( 'wpbuddy\rich_snippets\YoastSEO_Model', 'yoast_seo_title' ),
		);

		$values['http://schema.org/Text'][] = array(
			'id'     => 'yoast_seo_meta_desc',
			'label'  => esc_html_x( 'Yoast SEO: Meta description', 'subselect field', 'rich-snippets-schema' ),
			'method' => array( 'wpbuddy\rich_snippets\YoastSEO_Model', 'yoast_seo_meta_desc' ),
		);

		$values['http://schema.org/Text'][] = array(
			'id'     => 'yoast_seo_primary_category',
			'label'  => esc_html_x( 'Yoast SEO: Primary category', 'subselect field', 'rich-snippets-schema' ),
			'method' => array( 'wpbuddy\rich_snippets\YoastSEO_Model', 'yoast_seo_primary_category' ),
		);

		$values['http://schema.org/URL'][] = $values['http://schema.org/Text'][] = array(
			'id'     => 'yoast_seo_content_image',
			'label'  => esc_html_x( 'Yoast SEO: First Image in Content', 'subselect field', 'rich-snippets-schema' ),
			'method' => array( 'wpbuddy\rich_snippets\YoastSEO_Model', 'yoast_seo_content_image' ),
		);

		$values['http://schema.org/ImageObject'][] = array(
			'id'     => 'yoast_seo_content_image',
			'label'  => esc_html_x( 'Yoast SEO: First Image in Content', 'subselect field', 'rich-snippets-schema' ),
			'method' => array( 'wpbuddy\rich_snippets\YoastSEO_Model', 'yoast_seo_content_image' ),
		);

		$values['http://schema.org/URL'][] = $values['http://schema.org/Thing'][] = array(
			'id'     => 'yoast_seo_primary_category_url',
			'label'  => esc_html_x( 'Yoast SEO: Primary category URL', 'subselect field', 'rich-snippets-schema' ),
			'method' => array( 'wpbuddy\rich_snippets\YoastSEO_Model', 'yoast_seo_primary_category_url' ),
		);

		return $values;
	}


	/**
	 * Returns the value of the SEO title.
	 *
	 * @param                                     $val
	 * @param \wpbuddy\rich_snippets\Rich_Snippet $rich_snippet
	 * @param array $meta_info
	 * @param bool $overwritten
	 *
	 * @return string
	 * @since 2.2.0
	 * @since 2.14.25 Added param $overwritten.
	 */
	public static function yoast_seo_title( $val, Rich_Snippet $rich_snippet, array $meta_info, bool $overwritten ) {

		if ( $overwritten ) {
			return $val;
		}

		if ( defined( 'WPSEO_VERSION' ) && version_compare( WPSEO_VERSION, '14.0', '>=' ) ) {
			return YoastSEO()->meta->for_current_page()->title;
		}

		if ( class_exists( '\WPSEO_Frontend' ) ) {
			$instance = \WPSEO_Frontend::get_instance();

			if ( method_exists( $instance, 'title' ) ) {
				return \WPSEO_Frontend::get_instance()->title( '' );
			}

		}

		return '';
	}


	/**
	 * Returns the value of a SEO meta description.
	 *
	 * @param                                     $val
	 * @param \wpbuddy\rich_snippets\Rich_Snippet $rich_snippet
	 * @param array $meta_info
	 * @param bool $overwritten
	 *
	 * @return string
	 * @since 2.2.0
	 * @since 2.14.25 Added param $overwritten.
	 */
	public static function yoast_seo_meta_desc( $val, Rich_Snippet $rich_snippet, array $meta_info, bool $overwritten ) {

		if ( $overwritten ) {
			return $val;
		}

		# YoastSEO > 14.x
		if ( defined( 'WPSEO_VERSION' ) && version_compare( WPSEO_VERSION, '14.0', '>=' ) ) {
			return (string) YoastSEO()->meta->for_current_page()->description;
		}

		# YoastSEO < 14.x
		if ( class_exists( '\WPSEO_Frontend' ) ) {
			$instance = \WPSEO_Frontend::get_instance();

			if ( method_exists( $instance, 'metadesc' ) ) {

				return $instance->metadesc( false );
			}
		}

		return '';
	}


	/**
	 * Returns the primary category name.
	 *
	 * @param                                     $val
	 * @param \wpbuddy\rich_snippets\Rich_Snippet $rich_snippet
	 * @param array $meta_info
	 * @param bool $overwritten
	 *
	 * @return string
	 * @since 2.2.0
	 * @since 2.14.25 Added param $overwritten.
	 */
	public static function yoast_seo_primary_category( $val, Rich_Snippet $rich_snippet, array $meta_info, bool $overwritten ) {

		if ( $overwritten ) {
			return $val;
		}

		$primary_category_id = Helper_Model::instance()->get_primary_category( $meta_info['current_post_id'] );

		if ( empty( $primary_category_id ) ) {
			return '';
		}

		$category_name = get_the_category_by_ID( $primary_category_id );

		if ( is_wp_error( $category_name ) ) {
			return '';
		}

		return $category_name;
	}


	/**
	 * Returns the primary category URL.
	 *
	 * @param                                     $val
	 * @param \wpbuddy\rich_snippets\Rich_Snippet $rich_snippet
	 * @param array $meta_info
	 * @param bool $overwritten
	 *
	 * @return string
	 * @since 2.7.0
	 * @since 2.14.25 Added param $overwritten.
	 */
	public static function yoast_seo_primary_category_url( $val, Rich_Snippet $rich_snippet, array $meta_info, bool $overwritten ) {

		if ( $overwritten ) {
			return $val;
		}

		$primary_category_id = Helper_Model::instance()->get_primary_category( $meta_info['current_post_id'] );

		if ( empty( $primary_category_id ) ) {
			return '';
		}

		$category_url = get_term_link( $primary_category_id );

		if ( is_wp_error( $category_url ) ) {
			return '';
		}

		return $category_url;
	}


	/**
	 * Returns the first image found in a post.
	 *
	 * @param string $val
	 * @param Rich_Snippet $rich_snippet
	 * @param array $meta_info
	 * @param bool $overwritten
	 *
	 * @return string
	 * @since 2.10.0
	 * @since 2.14.25 Added param $overwritten.
	 */
	public static function yoast_seo_content_image( $val, Rich_Snippet $rich_snippet, array $meta_info, bool $overwritten ): string {

		if ( $overwritten ) {
			return (string) $val;
		}

		if ( ! class_exists( '\WPSEO_Content_Images' ) ) {
			return '';
		}

		$image_finder = new \WPSEO_Content_Images();

		if ( ! method_exists( $image_finder, 'get_images' ) ) {
			return '';
		}

		$images = $image_finder->get_images( $meta_info['current_post_id'] );

		if ( ! is_array( $images ) || array() === $images ) {
			return '';
		}

		$image_url = reset( $images );

		if ( ! $image_url ) {
			return '';
		}

		return esc_url( $image_url );
	}

}
