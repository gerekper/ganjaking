<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_FAQ_Shortcode_Panel' ) ) {

	/**
	 * Displays the shortcode creation panel in YITH FAQs plugin admin tab
	 *
	 * @class   YITH_FAQ_Shortcode_Panel
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 *
	 * @package Yithemes
	 */
	class YITH_FAQ_Shortcode_Panel {

		/**
		 * @var $post_type string post type name
		 */
		private $post_type = null;

		/**
		 * @var $taxonomy string taxonomy name
		 */
		private $taxonomy = null;

		/**
		 * Constructor
		 *
		 * @param   $post_type string
		 * @param   $taxonomy  string
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function __construct( $post_type, $taxonomy ) {

			$this->post_type = $post_type;
			$this->taxonomy  = $taxonomy;

			add_action( 'init', array( $this, 'add_shortcodes_button' ), 20 );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ), 20 );
			add_filter( 'yith_plugin_fw_icons_field_icons_' . YITH_FWP_SLUG, array( $this, 'filter_icons' ) );

		}

		/**
		 * Add scripts and styles
		 *
		 * @param   $hook string
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function admin_scripts( $hook ) {

			if ( 'yith-plugins_page_yith-faq-plugin-for-wordpress' === $hook ) {
				wp_enqueue_style( 'yith-faq-shortcode-panel', yit_load_css_file( YITH_FWP_ASSETS_URL . '/css/admin-panel.css' ), array(), YITH_FWP_VERSION );
				wp_enqueue_style( 'yith-faq-shortcode-icons', yit_load_css_file( YITH_FWP_ASSETS_URL . '/css/icons.css' ), array(), YITH_FWP_VERSION );
				wp_enqueue_script( 'yith-faq-shortcode-panel', yit_load_js_file( YITH_FWP_ASSETS_URL . '/js/admin-panel.js' ), array( 'jquery' ), YITH_FWP_VERSION, true );
			}

			global $pagenow;

			if ( ( 'post.php' === $pagenow || 'post-new.php' === $pagenow ) && $this->can_show_shortcode_buttons() ) {

				wp_enqueue_style( 'yit-plugin-style' );
				wp_enqueue_style( 'yith-plugin-fw-fields' );
				wp_enqueue_style( 'yith-faq-shortcode-icons', yit_load_css_file( YITH_FWP_ASSETS_URL . '/css/icons.css' ), array(), YITH_FWP_VERSION );
				wp_enqueue_style( 'yith-faq-tinymce', yit_load_css_file( YITH_FWP_ASSETS_URL . '/css/tinymce.css' ), array(), YITH_FWP_VERSION );

				wp_enqueue_script( 'yit-plugin-panel' );
				wp_enqueue_script( 'yith-plugin-fw-fields' );
				wp_enqueue_script( 'yith-enhanced-select' );
				wp_enqueue_script( 'yith-faq-shortcode-panel', yit_load_js_file( YITH_FWP_ASSETS_URL . '/js/admin-panel.js' ), array( 'jquery' ), YITH_FWP_VERSION, true );

				wp_localize_script(
					'yith-faq-shortcode-panel',
					'yfwp_shortcode',
					array(
						'title'           => esc_html__( 'Add FAQ shortcode', 'yith-faq-plugin-for-wordpress' ),
						'insert_btn_text' => esc_html__( 'Insert shortcode', 'yith-faq-plugin-for-wordpress' ),
						'close_btn_text'  => esc_html__( 'Close', 'yith-faq-plugin-for-wordpress' ),
						'content'         => $this->tinymce_output(),
					)
				);

			}

		}

		/**
		 * Removes unnecessary icons
		 *
		 * @return  array
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function filter_icons() {

			return array(
				'yfwp' => array(
					'\e800' => 'plus',
					'\e801' => 'plus-circle',
					'\f0fe' => 'plus-square',
					'\f196' => 'plus-square-o',
					'\e804' => 'chevron-down',
					'\f13a' => 'chevron-circle-down',
					'\e806' => 'arrow-circle-o-down',
					'\e80a' => 'arrow-down',
					'\f0ab' => 'arrow-circle-down',
					'\f103' => 'angle-double-down',
					'\f107' => 'angle-down',
					'\e808' => 'caret-down',
					'\f150' => 'caret-square-o-down',
				),
			);

		}

		/**
		 * Outputs the shortcode creation panel in the lightbox
		 *
		 * @return  string
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function tinymce_output() {

			$options = include( YITH_FWP_DIR . 'plugin-options/shortcode-options.php' );

			ob_start();
			?>
			<div class="wrap yith-plugin-ui">
				<div id="wrap" class="yith-plugin-fw plugin-option yit-admin-panel-container">
					<div class="yit-admin-panel-content-wrap">
						<table class="form-table" role="presentation">
							<?php foreach ( $options['shortcode']['settings'] as $field ) : ?>
								<tr>
									<th scope="row"><label for="<?php echo $field['id']; ?>>"><?php echo $field['name']; ?></label></th>
									<td>
										<?php
										YITH_FWP()->get_panel()->render_field( array( 'option' => $field ) );
										?>
									</td>
								</tr>
							<?php endforeach; ?>
						</table>
					</div>
				</div>
			</div>
			<?php
			return ob_get_clean();
		}

		/**
		 * Add shortcode button to TinyMCE editor, adding filter on mce_external_plugins
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function add_shortcodes_button() {

			//APPLY_FILTER: yith_faq_instantiate_shortcode_button: check if shortcode button can be instantiated
			if ( is_admin() || apply_filters( 'yith_faq_instantiate_shortcode_button', false ) ) {
				add_filter( 'mce_external_plugins', array( $this, 'add_shortcodes_tinymce_plugin' ) );
				add_filter( 'mce_buttons', array( $this, 'register_shortcodes_button' ) );
			}

		}

		/**
		 * Add a script to TinyMCE script list
		 *
		 * @param   $plugin_array array
		 *
		 * @return  array
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function add_shortcodes_tinymce_plugin( $plugin_array ) {

			if ( $this->can_show_shortcode_buttons() ) {

				$plugin_array['yfwp_shortcode'] = yit_load_js_file( YITH_FWP_ASSETS_URL . '/js/tinymce.js' );
			}

			return $plugin_array;

		}

		/**
		 * Make TinyMCE know a new button was included in its toolbar
		 *
		 * @param   $buttons array
		 *
		 * @return  array
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function register_shortcodes_button( $buttons ) {

			if ( $this->can_show_shortcode_buttons() ) {

				array_push( $buttons, '|', 'yfwp_shortcode' );
			}

			return $buttons;

		}

		/**
		 * Check if shortcode buttons can be shown on the edit page
		 *
		 * @return  boolean
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function can_show_shortcode_buttons() {

			global $post;

			return ( $post && ! in_array( $post->post_type, $this->get_disabled_post_types(), true ) );

		}

		/**
		 * Set post types where not show the shortcode
		 *
		 * @return  array
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function get_disabled_post_types() {

			$post_types = array( $this->post_type );

			//APPLY_FILTER: yith_faq_disabled_post_types : post types where not show the FAQ shortcode button
			return apply_filters( 'yith_faq_disabled_post_types', $post_types );
		}

	}

}
