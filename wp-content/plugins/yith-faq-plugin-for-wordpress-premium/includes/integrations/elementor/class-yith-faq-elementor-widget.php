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

if ( ! class_exists( 'YITH_FAQ_Elementor_Widget' ) ) {

	/**
	 * Elementor widget class
	 *
	 * @class   YITH_FAQ_Elementor_Widget
	 * @since   1.1.5
	 * @author  Your Inspiration Themes
	 *
	 * @package Yithemes
	 */
	class YITH_FAQ_Elementor_Widget extends \Elementor\Widget_Base {

		/**
		 * Get widget name.
		 *
		 * @return  string
		 * @since   1.1.5
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function get_name() {
			return 'yith-faq';
		}

		/**
		 * Get widget title.
		 *
		 * @return  string
		 * @since   1.1.5
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function get_title() {
			return esc_html__( 'YITH FAQ', 'yith-faq-plugin-for-wordpress' );
		}

		/**
		 * Get widget icon.
		 *
		 * @return  string
		 * @since   1.1.5
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function get_icon() {
			return 'eicon-toggle';
		}

		/**
		 * Get widget categories.
		 *
		 * @return  array
		 * @since   1.1.5
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function get_categories() {
			return array( 'yith', 'general' );
		}

		/**
		 * Get widget keywords.
		 *
		 * @return  array
		 * @since   1.1.5
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function get_keywords() {
			return array( 'faq', 'frequently', 'asked', 'questions' );
		}

		/**
		 * Register widget controls.
		 *
		 * @return  void
		 * @since   1.1.5
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		protected function _register_controls() {

			$this->start_controls_section(
				'section_faq',
				array(
					'label' => esc_html__( 'YITH FAQ', 'yith-faq-plugin-for-wordpress' ),
				)
			);

			$this->add_control(
				'search_box',
				array(
					'label'   => esc_html_x( 'Show search box', '[elementor]: attribute description', 'yith-faq-plugin-for-wordpress' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => array(
						'on'  => esc_html_x( 'Show', '[elementor]: Help text', 'yith-faq-plugin-for-wordpress' ),
						'off' => esc_html_x( 'Hide', '[elementor]: Help text', 'yith-faq-plugin-for-wordpress' ),
					),
					'default' => 'off',
				)
			);

			$this->add_control(
				'category_filters',
				array(
					'label'   => esc_html_x( 'Show category filters', '[elementor]: attribute description', 'yith-faq-plugin-for-wordpress' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => array(
						'on'  => esc_html_x( 'Show', '[elementor]: Help text', 'yith-faq-plugin-for-wordpress' ),
						'off' => esc_html_x( 'Hide', '[elementor]: Help text', 'yith-faq-plugin-for-wordpress' ),
					),
					'default' => 'off',
				)
			);

			$this->add_control(
				'style',
				array(
					'label'   => esc_html_x( 'Choose the style', '[elementor]: block description', 'yith-faq-plugin-for-wordpress' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => array(
						'list'      => esc_html_x( 'List', '[elementor]: inspector description', 'yith-faq-plugin-for-wordpress' ),
						'accordion' => esc_html_x( 'Accordion', '[elementor]: inspector description', 'yith-faq-plugin-for-wordpress' ),
						'toggle'    => esc_html_x( 'Toggle', '[elementor]: inspector description', 'yith-faq-plugin-for-wordpress' ),
					),
					'default' => 'list',
				)
			);

			$this->add_control(
				'page_size',
				array(
					'label'   => esc_html_x( 'FAQs per page', '[elementor]: attributes description', 'yith-faq-plugin-for-wordpress' ),
					'type'    => \Elementor\Controls_Manager::NUMBER,
					//APPLY_FILTER: yith_faq_minimum_page : set minimum number of items in a page
					'min'     => apply_filters( 'yith_faq_minimum_page', 5 ),
					//APPLY_FILTER: yith_faq_maximum_page : set maximum number of items in a page
					'max'     => apply_filters( 'yith_faq_maximum_page', 20 ),
					'default' => 10,
				)
			);

			$this->add_control(
				'categories',
				array(
					'label'    => esc_html_x( 'Categories to display', '[elementor]: block description', 'yith-faq-plugin-for-wordpress' ),
					'type'     => \Elementor\Controls_Manager::SELECT2,
					'options'  => yfwp_get_categories(),
					'multiple' => true,
					'default'  => array(),
				)
			);

			$this->add_control(
				'show_icon',
				array(
					'label'   => esc_html_x( 'Show icon', '[elementor]: block description', 'yith-faq-plugin-for-wordpress' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => array(
						'off'   => esc_html_x( 'Off', '[elementor]: inspector description', 'yith-faq-plugin-for-wordpress' ),
						'left'  => esc_html_x( 'Left', '[elementor]: inspector description', 'yith-faq-plugin-for-wordpress' ),
						'right' => esc_html_x( 'Right', '[elementor]: inspector description', 'yith-faq-plugin-for-wordpress' ),
					),
					'default' => 'right',
				)
			);

			$this->add_control(
				'icon_size',
				array(
					'label'   => esc_html_x( 'Icon size (px)', '[elementor]: attributes description', 'yith-faq-plugin-for-wordpress' ),
					'type'    => \Elementor\Controls_Manager::NUMBER,
					'default' => 14,
					'min'     => 8,
					'max'     => 40,
				)
			);

			$this->add_control(
				'icon',
				array(
					'label'   => esc_html_x( 'Choose the icon', '[elementor]: block description', 'yith-faq-plugin-for-wordpress' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => array(
						'yfwp:plus'                => 'plus',
						'yfwp:plus-circle'         => 'plus-circle',
						'yfwp:plus-square'         => 'plus-square',
						'yfwp:plus-square-o'       => 'plus-square-o',
						'yfwp:chevron-down'        => 'chevron-down',
						'yfwp:chevron-circle-down' => 'chevron-circle-down',
						'yfwp:arrow-circle-o-down' => 'arrow-circle-o-down',
						'yfwp:arrow-down'          => 'arrow-down',
						'yfwp:arrow-circle-down'   => 'arrow-circle-down',
						'yfwp:angle-double-down'   => 'angle-double-down',
						'yfwp:angle-down'          => 'angle-down',
						'yfwp:caret-down'          => 'caret-down',
						'yfwp:caret-square-o-down' => 'caret-square-o-down',
					),
					'default' => 'yfwp:plus',
				)
			);

			$this->end_controls_section();

		}

		/**
		 * Render widget.
		 *
		 * @return  void
		 * @since   1.1.5
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		protected function render() {
			$attribute_string = '';
			$settings         = $this->get_settings_for_display();

			foreach ( $settings as $key => $value ) {
				if ( empty( $value ) || ! is_scalar( $value ) ) {
					continue;
				}
				$attribute_string .= " {$key}=\"{$value}\"";
			}
			echo do_shortcode( "[yith_faq {$attribute_string}]" );
		}

	}

}
