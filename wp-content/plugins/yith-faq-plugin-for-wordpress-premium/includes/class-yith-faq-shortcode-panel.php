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
		 * @author  Alberto Ruggiero
		 */
		public function __construct( $post_type, $taxonomy ) {

			$this->post_type = $post_type;
			$this->taxonomy  = $taxonomy;

			add_action( 'yith_faq_shortcode', array( $this, 'output' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ), 20 );
			add_filter( 'yith_plugin_fw_icons_field_icons_' . YITH_FWP_SLUG, array( $this, 'filter_icons' ) );
			add_action( 'admin_action_yfwp_shortcode_panel', array( $this, 'lightbox_output' ) );

		}

		/**
		 * Add scripts and styles
		 *
		 * @param   $hook string
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function admin_scripts( $hook ) {

			if ( $hook != 'yith-plugins_page_yith-faq-plugin-for-wordpress' && $hook != 'yith-faq-plugin-for-wordpress-shortcode' ) {
				return;
			}

			wp_enqueue_style( 'yith-plugin-fw-fields' );
			wp_enqueue_script( 'yith-plugin-fw-fields' );
			wp_enqueue_script( 'yith-enhanced-select' );

			wp_enqueue_style( 'yith-faq-shortcode-panel', yit_load_css_file( YITH_FWP_ASSETS_URL . '/css/yith-faq-shortcode-panel.css' ), array(), YITH_FWP_VERSION );
			wp_enqueue_script( 'yith-faq-shortcode-panel', yit_load_js_file( YITH_FWP_ASSETS_URL . '/js/yith-faq-shortcode-panel.js' ), array( 'jquery' ), YITH_FWP_VERSION );

		}

		/**
		 * Removes unnecessary icons
		 *
		 * @return  array
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function filter_icons() {

			return array(
				'FontAwesome' => array(
					'\f067' => 'plus',
					'\f055' => 'plus-circle',
					'\f0fe' => 'plus-square',
					'\f196' => 'plus-square-o',
					'\f078' => 'chevron-down',
					'\f13a' => 'chevron-circle-down',
					'\f01a' => 'arrow-circle-o-down',
					'\f063' => 'arrow-down',
					'\f0ab' => 'arrow-circle-down',
					'\f103' => 'angle-double-down',
					'\f107' => 'angle-down',
					'\f0d7' => 'caret-down',
					'\f150' => 'caret-square-o-down',
				)
			);

		}

		/**
		 * Outputs the shortcode creation panel
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function output() {

			?>
            <div id="wrap" class="yith-plugin-fw plugin-option yit-admin-panel-container">
                <div class="yit-admin-panel-content-wrap">

                    <h2>
						<?php esc_html_e( 'Shortcode Creation', 'yith-faq-plugin-for-wordpress' ); ?>
                    </h2>
                    <table class="faq-table form-table">
                        <tbody>
                        <tr>
                            <th scope="row"><label for="enable_search_box"><?php esc_html_e( 'Show search box', 'yith-faq-plugin-for-wordpress' ) ?></label></th>
                            <td>
								<?php
								$args = array(
									'id'   => 'enable_search_box',
									'name' => 'enable_search_box',
									'type' => 'onoff',
								);

								yith_plugin_fw_get_field( $args, true );
								?>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="enable_category_filter"><?php esc_html_e( 'Show category filters', 'yith-faq-plugin-for-wordpress' ) ?></label></th>
                            <td>
								<?php
								$args = array(
									'id'   => 'enable_category_filter',
									'name' => 'enable_category_filter',
									'type' => 'onoff',
								);

								yith_plugin_fw_get_field( $args, true );
								?>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="choose_style"><?php esc_html_e( 'Choose the style', 'yith-faq-plugin-for-wordpress' ) ?></label></th>
                            <td>
								<?php
								$args = array(
									'id'      => 'style',
									'name'    => 'style',
									'type'    => 'radio',
									'options' => array(
										'list'      => esc_html__( 'List', 'yith-faq-plugin-for-wordpress' ),
										'accordion' => esc_html__( 'Accordion', 'yith-faq-plugin-for-wordpress' ),
										'toggle'    => esc_html__( 'Toggle', 'yith-faq-plugin-for-wordpress' ),
									),
									'value'   => 'list',
								);

								yith_plugin_fw_get_field( $args, true );
								?>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="page_size"><?php esc_html_e( 'FAQs per page', 'yith-faq-plugin-for-wordpress' ) ?></label></th>
                            <td>
								<?php
								$args = array(
									'id'    => 'page_size',
									'name'  => 'page_size',
									'type'  => 'number',
									//APPLY_FILTER: yith_faq_minimum_page : set minimum number of items in a page
									'min'   => apply_filters( 'yith_faq_minimum_page', 5 ),
									//APPLY_FILTER: yith_faq_maximum_page : set maximum number of items in a page
									'max'   => apply_filters( 'yith_faq_maximum_page', 20 ),
									'value' => '10'
								);

								yith_plugin_fw_get_field( $args, true );
								?>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="categories"><?php esc_html_e( 'Categories to display', 'yith-faq-plugin-for-wordpress' ) ?></label></th>
                            <td>
								<?php
								$args = array(
									'id'       => 'categories',
									'name'     => 'categories',
									'type'     => 'ajax-terms',
									'multiple' => true,
									'data'     => array(
										'placeholder' => esc_html__( 'Search FAQs Categories', 'yith-faq-plugin-for-wordpress' ),
										'taxonomy'    => $this->taxonomy
									)
								);

								yith_plugin_fw_get_field( $args, true );
								?>
                                <span class="description"><?php esc_html_e( 'If left empty all categories will be displayed', 'yith-faq-plugin-for-wordpress' ) ?></span>
                            </td>
                        </tr>
                        <tr id="show_icon_row">
                            <th scope="row"><label for="show_icon"><?php esc_html_e( 'Show icon', 'yith-faq-plugin-for-wordpress' ) ?></label></th>
                            <td>
								<?php
								$args = array(
									'id'      => 'show_icon',
									'name'    => 'show_icon',
									'type'    => 'radio',
									'options' => array(
										'off'   => esc_html__( 'Off', 'yith-faq-plugin-for-wordpress' ),
										'left'  => esc_html__( 'Left', 'yith-faq-plugin-for-wordpress' ),
										'right' => esc_html__( 'Right', 'yith-faq-plugin-for-wordpress' ),
									),
									'value'   => 'right',
								);

								yith_plugin_fw_get_field( $args, true );
								?>
                            </td>
                        </tr>
                        <tr id="icon_size_row">
                            <th scope="row"><label for="icon_size"><?php esc_html_e( 'Icon size (px)', 'yith-faq-plugin-for-wordpress' ) ?></label></th>
                            <td>
								<?php
								$args = array(
									'id'    => 'icon_size',
									'name'  => 'icon_size',
									'type'  => 'number',
									'min'   => '8',
									'max'   => '40',
									'value' => '14'
								);

								yith_plugin_fw_get_field( $args, true );
								?>
                            </td>
                        </tr>
                        <tr id="icon_row">
                            <th scope="row"><label for="choose_icon"><?php esc_html_e( 'Choose the icon', 'yith-faq-plugin-for-wordpress' ) ?></label></th>
                            <td>
								<?php
								$args = array(
									'id'           => 'icon',
									'name'         => 'icon',
									'type'         => 'icons',
									'value'        => 'FontAwesome:plus',
									'filter_icons' => YITH_FWP_SLUG
								);

								yith_plugin_fw_get_field( $args, true );
								?>
                            </td>
                        </tr>
                        <tr id="field_row">
                            <th scope="row"></th>
                            <td>
								<?php
								$args = array(
									'id'                => 'shortcode',
									'name'              => 'shortcode',
									'type'              => 'text',
									'value'             => '[yith_faq]',
									'custom_attributes' => 'readonly',
								);

								yith_plugin_fw_get_field( $args, true );
								?>
                            </td>
                        </tr>
                        <tr id="button_row">
                            <th scope="row"></th>
                            <td>
								<?php
								$args = array(
									'type'    => 'buttons',
									'buttons' => array(
										array(
											'name'  => esc_html__( 'Insert Shortcode', 'yith-faq-plugin-for-wordpress' ),
											'class' => 'button-primary insert-shortcode',
										),
									)
								);

								yith_plugin_fw_get_field( $args, true );
								?>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
			<?php

		}

		/**
		 * Outputs the shortcode creation panel in the lightbox
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function lightbox_output() {

			@header( 'Content-Type: ' . get_option( 'html_type' ) . '; charset=' . get_option( 'blog_charset' ) );

			?>
            <html xmlns="http://www.w3.org/1999/xhtml" <?php do_action( 'admin_xml_ns' ); ?> <?php language_attributes(); ?>>
            <head>
                <meta http-equiv="Content-Type" content="<?php bloginfo( 'html_type' ); ?>; charset=<?php echo get_option( 'blog_charset' ); ?>" />
                <title><?php ?></title>
                <script type="text/javascript">
                    var ajaxurl = '<?php echo admin_url( 'admin-ajax.php' ) ?>';
                </script>
				<?php
				$hook_suffix = 'yith-faq-plugin-for-wordpress-shortcode';

				wp_admin_css( 'wp-admin', true );
				do_action( 'admin_enqueue_scripts', $hook_suffix );
				do_action( 'admin_print_styles' );
				do_action( 'admin_print_scripts' );
				do_action( 'admin_head' );
				?>

            </head>
            <body class="shortcode-lightbox">
			<?php $this->output(); ?>
			<?php do_action( 'admin_print_footer_scripts' ); ?>
            </body>
            </html>
			<?php
		}

	}

}