<?php
/**
 * Storefront Powerpack Frontend Header Class
 *
 * @author   WooThemes
 * @package  Storefront_Powerpack
 * @since    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SP_Frontend_Header' ) ) :

	/**
	 * The Frontend class.
	 */
	class SP_Frontend_Header extends SP_Frontend {

		/**
		 * Setup class.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			add_action( 'wp', array( $this, 'add_custom_header' ), 50 );
			add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ), 99 );
			add_action( 'body_class', array( $this, 'body_classes' ) );
		}

		/**
		 * Enqueue scrips and styles.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function scripts() {
			wp_enqueue_style( 'sp-header-frontend', SP_PLUGIN_URL . 'includes/customizer/header/assets/css/sp-header-frontend.css', '', storefront_powerpack()->version );

			if ( true === get_theme_mod( 'sp_header_sticky' ) ) {
				$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
				wp_enqueue_script( 'sp-sticky-script', SP_PLUGIN_URL . 'includes/customizer/header/assets/js/sp-sticky-header' . $suffix . '.js', array( 'jquery' ), storefront_powerpack()->version, true );

				wp_enqueue_style( 'sp-sticky-header', SP_PLUGIN_URL . 'includes/customizer/header/assets/css/sp-sticky-header.css', '', storefront_powerpack()->version );
			}
		}

		/**
		 * Initialize custom header.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function add_custom_header() {
			global $storefront_version;

			$setting = get_theme_mod( 'sp_header_setting' );

			if ( ! $setting || empty( $setting ) ) {
				return;
			}

			remove_all_actions( 'storefront_header' );

			add_action( 'storefront_header', array( $this, 'custom_header' ), 10 );

			if ( version_compare( $storefront_version, '2.3.0', '>=' ) ) {
				add_action( 'storefront_header', 'storefront_header_container', 5 );
				add_action( 'storefront_header', 'storefront_header_container_close', 15 );
			}
		}

		/**
		 * Custom body class added when the custom header is active.
		 *
		 * @since 1.0.0
		 * @return array Body classes
		 */
		public function body_classes( $classes ) {
			$header_customizer = get_theme_mod( 'sp_header_setting' );

			if ( $header_customizer && ! empty( $header_customizer ) ) {
				$classes[] = 'sp-header-active';
			}

			return $classes;
		}

		/**
		 * Build custom header output.
		 * @access  public
		 * @since   1.0.0
		 * @return  string
		 */
		public function custom_header() {
			$html      = '';
			$rows_html = '';
			$rows      = $this->_get_rows( get_theme_mod( 'sp_header_setting' ) );

			foreach ( $rows as $row ) {
				if ( empty( $row ) ) {
					$rows_html .= '<div class="sp-header-empty"></div>';
					continue;
				}

				$row_html     = '';
				$count        = 0;
				$columns      = 0;
				$max_columns  = 12;
				$widget_count = count( $row );
				$widgets      = $this->_sort_wigets_by_position( $row );

				foreach ( $widgets as $key => $widget ) {
					$widget_content = $this->_do_storefront_function( $key );

					if ( $widget_content ) {
						$count++;
					} else {
						$widget_count--;
						continue;
					}

					// Used to calculate empty columns between widgets.
					$empty = 0;

					// Init array for widget row classes.
					$classes = array();

					// Calculate empty space between columns.
					if ( $columns < intval( $widget['x'] ) ) {
						$empty = intval( $widget['x'] ) - $columns;
					}

					// Add pre class and add empty columns to $columns var.
					if ( 0 < $empty ) {
						$classes[] = 'sp-header-pre-' . $empty;
						$columns   = $columns + $empty;
					}

					$columns = $columns + intval( $widget['w'] );
					$classes[] = 'sp-header-span-' . intval( $widget['w'] );

					if ( $widget_count === $count ) {
						if ( $max_columns === $columns ) {
							$classes[] = 'sp-header-last';
						} else {
							$classes[] = 'sp-header-post-' . ( $max_columns - $columns );
						}

						$count = 0;
					}

					$row_html .= '<div class="' . implode( ' ', $classes ) . '">' . $widget_content . '</div>';
				}

				if ( '' !== $row_html ) {
					$rows_html .= '<div class="sp-header-row">' . $row_html . '</div>';
				}
			}

			if ( '' !== $rows_html ) {
				$html = $rows_html;
			}

			echo $html;
		}

		/**
		 * Sort widgets by row.
		 * @access  private
		 * @since   1.0.0
		 * @return  array
		 */
		private function _get_rows( $widgets ) {
			$widget_rows = array();

			foreach ( $widgets as $key => $widget ) {
				$widget_rows[ $widget['y'] ][ $key ] = $widget;
			}

			ksort( $widget_rows );

			// Add empty rows.
			$row_keys = array_keys( $widget_rows );

			$last_key = end( $row_keys );

			for ( $i = 0; $i < $last_key; $i++ ) {
				if ( ! array_key_exists( $i, $widget_rows ) ) {
					$widget_rows[ $i ] = array();
				}
			}

			ksort( $widget_rows );

			return $widget_rows;
		}

		/**
		 * Sort widgets by their position on the grid.
		 * @access  private
		 * @since   1.0.0
		 * @return  array
		 */
		private function _sort_wigets_by_position( $widgets = array() ) {
			$ordered_widgets = array();

			foreach ( $widgets as $key => $widget ) {
				$ordered_widgets[ $key ] = $widget['x'];
			}

			array_multisort( $ordered_widgets, SORT_ASC, $widgets );

			return $widgets;
		}

		/**
		 * Render component content.
		 * @access  private
		 * @since   1.0.0
		 * @param   string $component The name of the component to render
		 * @return  array
		 */
		private function _do_storefront_function( $component = '' ) {
			$components = SP_Customizer_Header::components();

			$component_function = '';

			if ( $component && array_key_exists( $component, $components ) ) {
				$component_function = $components[ $component ]['hook'];
			}

			if ( '' !== $component_function && function_exists( $component_function ) ) {
				// Render the function.
				ob_start();
				call_user_func( $component_function );
				$content = ob_get_clean();

				return $content;
			}

			return false;
		}
	}

endif;

return new SP_Frontend_Header();