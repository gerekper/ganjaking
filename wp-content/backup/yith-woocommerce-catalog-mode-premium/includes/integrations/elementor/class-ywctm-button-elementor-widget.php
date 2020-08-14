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
	exit; // Exit if accessed directly
}

use Elementor\Controls_Manager;

if ( ! class_exists( 'YWCTM_Button_Elementor_Widget' ) ) {

	/**
	 * Elementor widget class
	 *
	 * @class   YWCTM_Button_Elementor_Widget
	 * @since   2.0.0
	 * @author  Your Inspiration Themes
	 *
	 * @package Yithemes
	 */
	class YWCTM_Button_Elementor_Widget extends \Elementor\Widget_Base {

		/**
		 * Get widget name.
		 *
		 * @return  string
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function get_name() {
			return 'yith-catalog-mode-button';
		}

		/**
		 * Get widget title.
		 *
		 * @return  string
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function get_title() {
			return esc_html__( 'YITH Catalog Mode Button', 'yith-woocommerce-catalog-mode' );
		}

		/**
		 * Get widget icon.
		 *
		 * @return  string
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function get_icon() {
			return 'eicon-button';
		}

		/**
		 * Get widget categories.
		 *
		 * @return  array
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function get_categories() {
			return array( 'yith', 'woocommerce-elements-single' );
		}

		/**
		 * Get widget keywords.
		 *
		 * @return  array
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function get_keywords() {
			return array( 'woocommerce', 'shop', 'store', 'catalog', 'button', 'add to cart' );
		}

		/**
		 * Register widget controls.
		 *
		 * @return  void
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		protected function _register_controls() {
			$this->start_controls_section(
				'section_button',
				array(
					'label' => esc_html__( 'YITH Catalog Mode Button', 'yith-woocommerce-catalog-mode' ),
				)
			);

			$this->add_control(
				'wc_style_warning1',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s: open <b> tag - %2$s: close </b> tag - %3$s: open link tag - %4$s: close link tag */
					'raw'             => sprintf( esc_html__( 'This widget inherits the style from the settings of %1$sYITH Catalog Mode%2$s plugin that you can edit %3$shere%4$s', 'yith-woocommerce-catalog-mode' ), '<b>', '</b>', '[<a href="' . get_admin_url( null, 'edit.php?post_type=ywctm-button-label' ) . '">', '</a>]' ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				)
			);

			$this->end_controls_section();

		}

		/**
		 * Render widget.
		 *
		 * @return  void
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		protected function render() {
			echo do_shortcode( '[ywctm-button]' );
		}

	}

}
