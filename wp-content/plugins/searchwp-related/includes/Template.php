<?php

namespace SearchWP_Related;

// exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class SearchWP_Related\Template
 *
 * Template loader class based on Pippin Williamson's guide
 * http://pippinsplugins.com/template-file-loaders-plugins/
 *
 * @since 1.0
 */
class Template {

	private $post;
	private $post_type;
	private $related;

	/**
	 * Template constructor.
	 */
	function __construct() {
		$this->related = new \SearchWP_Related();
	}

	/**
	 * Initialize
	 */
	function init() {
		if ( apply_filters( 'searchwp_related_auto_append', true ) ) {
			add_filter( 'the_content', array( $this, 'the_content' ) );
		}
	}

	/**
	 * Retrieve the template directory within this plugin
	 *
	 * @return string The template directory within this plugin
	 */
	function get_template_directory() {
		return trailingslashit( SEARCHWP_RELATED_PLUGIN_DIR ) . 'templates';
	}

	/**
	 * Callback to auto-append Related content to the_content
	 *
	 * @param $content
	 *
	 * @return mixed
	 */
	function the_content( $content ) {
		global $post;

		if ( ! is_singular() ) {
			return $content;
		}

		// Respect excluded post types
		$excluded_post_types = (array) apply_filters( 'searchwp_related_excluded_post_types', array( 'attachment' ) );

		if ( in_array( get_post_type(), $excluded_post_types ) ) {
			return $content;
		}

		$this->post = $post;

		// If post was force skipped: exit early
		$skipped = get_post_meta( $this->post->ID, $this->related->meta_key . '_skip', true );
		if ( ! empty( $skipped ) ) {
			return $content;
		}

		$this->post_type = get_post_type();

		// Check settings for auto-append post type
		$settings = $this->related->settings->get();
		$skip = ! in_array( $this->post_type, $settings['auto_append'], true );

		// Allow code-based settings override for post type
		$auto_append_this = apply_filters( "searchwp_related_auto_append_{$this->post_type}", true );

		if ( $skip || empty( $auto_append_this ) ) {
			return $content;
		}

		$content .= $this->get_template( $this->post_type );

		return $content;
	}

	/**
	 * Set up the proper template part array and locate it
	 *
	 * @sine 1.0
	 *
	 * @param null $post_type
	 * @param bool $load Whether to load the template part
	 *
	 * @return bool|string The location of the applicable template file
	 * @internal param string $slug The template slug (without file extension)
	 * @internal param null $name The template name (appended to $slug if provided)
	 */
	function get_template( $post_type = null, $load = true ) {

		// Templates must begin with 'related'
		$slug = 'related';

		// Templates can be customized per post type
		if ( empty( $post_type ) || ! post_type_exists( $post_type ) ) {
			$post_type = get_post_type();
		}

		$templates = array();
		$templates[] = $slug . '-' . $post_type . '.php';
		$templates[] = $slug . '.php';

		// allow filtration of template parts
		$templates = apply_filters( 'searchwp_related_get_template', $templates, $slug, $post_type );

		// return what was found
		return $this->locate_template( $templates, $load, false );
	}

	/**
	 * Check for the applicable template in the child theme, then parent theme, and in the plugin dir as a last resort
	 * and output it if it was located
	 *
	 * @since 1.0
	 *
	 * @param array $template_names The potential template names in order of precedence
	 * @param bool $load Whether to load the template file
	 * @param bool $require_once Whether to require the template file once
	 *
	 * @return bool|string The location of the applicable template file
	 */
	function locate_template( $template_names, $load = false, $require_once = true ) {
		global /** @noinspection PhpUnusedLocalVariableInspection */
		$wp_filesystem, $wp_query;

		// Default is not found
		$located = false;

		$template_dir = apply_filters( 'searchwp_related_template_dir', 'searchwp-related' );

		// try to find the template file
		foreach ( (array) $template_names as $template_name ) {
			if ( empty( $template_name ) ) {
				continue;
			}
			$template_name = ltrim( $template_name, '/' );

			// check the child theme first
			$maybe_child_theme = trailingslashit( get_stylesheet_directory() ) . trailingslashit( $template_dir ) . $template_name;
			if ( file_exists( $maybe_child_theme ) ) {
				$located = $maybe_child_theme;
				break;
			}

			if ( ! $located ) {
				// check parent theme
				$maybe_parent_theme = trailingslashit( get_template_directory() ) . trailingslashit( $template_dir ) . $template_name;
				if ( file_exists( $maybe_parent_theme ) ) {
					$located = $maybe_parent_theme;
					break;
				}
			}

			if ( ! $located ) {
				// check theme compat
				$maybe_theme_compat = trailingslashit( $this->get_template_directory() ) . $template_name;
				if ( file_exists( $maybe_theme_compat ) ) {
					$located = $maybe_theme_compat;
					break;
				}
			}
		}

		$located = apply_filters( 'searchwp_related_template', $located, $this );

		if ( ( true === $load ) && ! empty( $located ) && empty( $require_once ) ) {

			$template_details = $this->get_template_details( $located );

			/**
			 * $searchwp_related is used in the actual template so it must be defined here */
			/** @noinspection PhpUnusedLocalVariableInspection */
			$searchwp_related = $this->get_related_for_post_id(
				get_the_ID(),
				$template_details['engine'],
				$template_details['per_page']
			);

			ob_start();
			/** @noinspection PhpIncludeInspection */
			include( $located );
			$markup = ob_get_contents();
			ob_end_clean();
			return $markup;
		} else {
			return $located;
		}
	}

	/**
	 * Retrieve configuration details from the template itself
	 *
	 * @param string $located
	 *
	 * @return array
	 */
	function get_template_details( $located = '' ) {
		// Instantiate filesystem
		include_once ABSPATH . 'wp-admin/includes/file.php';
		WP_Filesystem();

		// Determine if a custom engine is in use
		$file_data = get_file_data( $located, array(
			'engine'   => 'SearchWP Engine',
			'per_page' => 'Maximum Results',
		) );

		if ( function_exists( 'SWP' ) ) {
			$engine = ! empty( $file_data['engine'] ) && SWP()->is_valid_engine( $file_data['engine'] ) ? $file_data['engine'] : apply_filters( 'searchwp_related_default_engine', 'default' );
		} else if ( class_exists( '\\SearchWP\\Settings' ) ) {
			$engine_valid = \SearchWP\Settings::get_engine_settings( $file_data['engine'] );
			$engine = ! empty( $file_data['engine'] ) && $engine_valid ? $file_data['engine'] : apply_filters( 'searchwp_related_default_engine', 'default' );
		} else {
			$engine = apply_filters( 'searchwp_related_default_engine', 'default' );
		}

		// Determine how many results to find
		$per_page = ! empty( $file_data['per_page'] ) && is_numeric( $file_data['per_page'] ) ? $file_data['per_page'] : apply_filters( 'searchwp_related_posts_per_page', 3 );

		$details = array(
			'engine'   => $engine,
			'per_page' => absint( $per_page ),
		);

		return $details;
	}

	/**
	 * Get the related content
	 *
	 * @param $post_id
	 * @param string $engine
	 * @param int $per_page
	 *
	 * @return array
	 */
	function get_related_for_post_id( $post_id, $engine = 'default', $per_page = 3 ) {

		if ( function_exists( 'SWP' ) ) {
			if ( ! SWP()->is_valid_engine( $engine ) ) {
				$engine = 'default';
			}
		} else {
			$engine_valid = \SearchWP\Settings::get_engine_settings( $engine );
			if ( ! $engine_valid ) {
				$engine = 'default';
			}
		}

		$post_id = absint( $post_id );

		// Determine which terms to use
		$terms = get_post_meta( get_the_ID(), $this->related->meta_key, true );
		$terms = apply_filters( 'searchwp_related_keywords', $terms, get_the_ID() );

		// Define arguments
		$args = array(
			'engine'         => $engine,
			's'              => $terms,
			'posts_per_page' => absint( $per_page ),
		);

		$args['posts_per_page'] = absint( apply_filters( "searchwp_related_posts_per_page_{$post_id}", $args['posts_per_page'] ) );

		$settings = $this->related->settings->get();

		// Check for exclusions from settings
		if ( array_key_exists( $this->post_type, $settings['post__not_in'] )
			&& ! empty( $settings['post__not_in'][ $this->post_type ] ) ) {
			$excluded = explode( ',', $settings['post__not_in'][ $this->post_type ] );
			$excluded = array_map( 'trim', $excluded );
			$excluded = array_map( 'absint', $excluded );

			$args['post__not_in'] = $excluded;
		}

		// Check for limitations from settings
		if ( array_key_exists( $this->post_type, $settings['post__in'] )
			&& ! empty( $settings['post__in'][ $this->post_type ] ) ) {
			$limitations = explode( ',', $settings['post__in'][ $this->post_type ] );
			$limitations = array_map( 'trim', $limitations );
			$limitations = array_map( 'absint', $limitations );

			$args['post__in'] = $limitations;
		}

		// Allow late filtration of args for this template call
		$args = apply_filters( 'searchwp_related_template_args', $args, $this->post );

		$related_ids = $this->related->get( $args );

		// If there isn't any Related content, make sure there's no Related content!
		if ( empty( $related_ids ) ) {
			$related_ids = array( 0 );
		}

		// Get objects for IDs
		$final_args = apply_filters( 'searchwp_related_template_cache_args', array(
			'post_type'      => 'any', // Will be limited by the post__in
			'post__in'       => $related_ids,
			'posts_per_page' => $args['posts_per_page'],
			'orderby'        => 'post__in',
		) );

		$related_posts = get_posts( $final_args );

		return $related_posts;
	}

}
