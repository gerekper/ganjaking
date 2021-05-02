<?php
/**
 * @version 1.0
 */
if ( ! class_exists( 'GP_Template' ) ) {

	class GP_Template {

		/**
		 * @var GP_Plugin|GP_Feed_Plugin
		 */
		public $plugin;

		public function __construct( $plugin ) {
			$this->plugin = $plugin;
		}

		/**
		 * Returns the path to the plugin's templates directory
		 *
		 * @since 1.0
		 * @return string
		 */
		public function get_templates_dir() {
			return $this->plugin->get_base_path() . '/templates';
		}

		/**
		 * Returns the URL to the plugin's templates directory
		 *
		 * @since 1.3.2.1
		 * @return string
		 */
		public function get_templates_url() {
			return $this->plugin->get_base_url() . '/templates';
		}

		/**
		 * Retrieves a template part
		 *
		 * @since v1.2
		 *
		 * Taken from bbPress
		 *
		 * @param string $slug
		 * @param string $name Optional. Default null
		 * @param bool   $load
		 *
		 * @return string
		 *
		 * @uses GP_Template::locate_template()
		 * @uses load_template()
		 * @uses get_template_part()
		 */
		public function get_template_part( $slug, $name = null, $load = true ) {
			// Execute code for this part
			do_action( 'get_template_part_' . $slug, $slug, $name );

			$load_template = apply_filters( 'gp_allow_template_part_' . $slug . '_' . $name, true, $this );
			if ( false === $load_template ) {
				return '';
			}

			// Setup possible parts
			$templates = array();
			if ( isset( $name ) ) {
				$templates[] = $slug . '-' . $name . '.php';
			}
			$templates[] = $slug . '.php';

			// Allow template parts to be filtered
			$templates = apply_filters( 'gp_get_template_part', $templates, $slug, $name, $this );

			// Return the part that is found
			return $this->locate_template( $templates, $load, false );
		}

		/**
		 * Retrieve the name of the highest priority template file that exists.
		 *
		 * Searches in the STYLESHEETPATH before TEMPLATEPATH so that themes which
		 * inherit from a parent theme can just overload one file. If the template is
		 * not found in either of those, it looks in the theme-compat folder last.
		 *
		 * Taken from bbPress
		 *
		 * @since 1.0
		 *
		 * @param string|array $template_names Template file(s) to search for, in order.
		 * @param bool $load If true the template file will be loaded if it is found.
		 * @param bool $require_once Whether to require_once or require. Default true.
		 *   Has no effect if $load is false.
		 * @return string The template filename if one is located.
		 */
		public function locate_template( $template_names, $load = false, $require_once = true, $args = array() ) {
			// No file found yet
			$located = false;

			// Try to find a template file
			foreach ( (array) $template_names as $template_name ) {

				// Continue if template is empty
				if ( empty( $template_name ) ) {
					continue;
				}

				// Trim off any slashes from the template name
				$template_name = ltrim( $template_name, '/' );

				// try locating this template file by looping through the template paths
				foreach ( $this->get_theme_template_paths() as $template_path ) {

					if ( file_exists( $template_path . $template_name ) ) {
						$located = $template_path . $template_name;
						break;
					}
				}

				if ( $located ) {
					break;
				}
			}

			if ( ( true == $load ) && ! empty( $located ) ) {
				$this->load_template( $located, $require_once, $args );
			}

			return $located;
		}

		/**
		 * Returns a list of paths to check for template locations
		 *
		 * @since 1.0
		 * @return mixed
		 */
		public function get_theme_template_paths() {

			$template_dir = $this->get_theme_template_dir_name();

			$file_paths = array(
				1   => trailingslashit( get_stylesheet_directory() ) . $template_dir,
				10  => trailingslashit( get_template_directory() ) . $template_dir,
				100 => $this->get_templates_dir(),
			);

			$file_paths = apply_filters( 'gp_template_paths', $file_paths, $this );

			// sort the file paths based on priority
			ksort( $file_paths, SORT_NUMERIC );

			return array_map( 'trailingslashit', $file_paths );
		}

		/**
		 * Returns the template directory name.
		 *
		 * Themes can filter this by using the gp_templates_dir filter.
		 *
		 * @since 1.0
		 * @return string
		 */
		public function get_theme_template_dir_name() {
			return trailingslashit( apply_filters( 'gp_templates_dir', $this->plugin->get_slug() ) );
		}

		/**
		 * Load the template and return as string.
		 *
		 * @since 1.0
		 * @return string
		 */
		public function parse_template( $template_names, $load = true, $require_once = true, $args = array() ) {
			ob_start();
			$this->locate_template( $template_names, $load, $require_once, $args );
			return ob_get_clean();
		}

		/**
		 * Require the template file with WordPress environment.
		 *
		 * The globals are set up for the template file to ensure that the WordPress
		 * environment is available from within the function. The query variables are
		 * also available.
		 *
		 * @since 1.5.0
		 *
		 * @global array      $posts
		 * @global WP_Post    $post
		 * @global bool       $wp_did_header
		 * @global WP_Query   $wp_query
		 * @global WP_Rewrite $wp_rewrite
		 * @global wpdb       $wpdb
		 * @global string     $wp_version
		 * @global WP         $wp
		 * @global int        $id
		 * @global WP_Comment $comment
		 * @global int        $user_ID
		 *
		 * @param string $_template_file Path to template file.
		 * @param bool   $require_once   Whether to require_once or require. Default true.
		 */
		function load_template( $_template_file, $require_once = true, $args = array() ) {
			global $posts, $post, $wp_did_header, $wp_query, $wp_rewrite, $wpdb, $wp_version, $wp, $id, $comment, $user_ID;

			if ( is_array( $wp_query->query_vars ) ) {
				extract( $wp_query->query_vars, EXTR_SKIP );
			}

			if ( isset( $s ) ) {
				$s = esc_attr( $s );
			}

			extract( $args );

			if ( $require_once ) {
				require_once( $_template_file );
			} else {
				require( $_template_file );
			}
		}

	}

}
