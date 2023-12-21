<?php
/**
 * Easy Digital Downloads checkout widget class
 *
 * @package Happy_Addons_Pro
 */
namespace Happy_Addons_Pro\Widget;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;


defined( 'ABSPATH' ) || die();

class EDD_Download extends Base {

	/**
	 * Retrieve toggle widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'EDD Download', 'happy-addons-pro' );
	}

	/**
	 * Get widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'hm hm-Download-circle';
	}

	public function get_keywords() {
		return [ 'edd', 'download', 'edd download', 'commerce', 'ecommerce', 'purchase', 'register', 'shop' ];
	}

	public function is_reload_preview_required() {
		return true;
	}

	/**
	 * Register widget content controls
	 */
	protected function register_content_controls() {

		$this->start_controls_section(
			'_section_general',
			[
				'label' => __( 'General', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'important_note',
			[
				'label'           => false,
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( '<strong>Note:</strong> EDD Download widget doesn\'t have any useful content control.', 'happy-addons-pro' ),
				'content_classes' => ' elementor-panel-alert elementor-panel-alert-warning',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register widget style controls
	 */
	protected function register_style_controls() {
		// $this->__sections_style_controls();
		$this->__edd_download_table_style_controls();

	}

	protected function __sections_style_controls() {
		$this->start_controls_section(
			'_section_style_sections',
			[
				'label' => __( 'Sections', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'sections_padding',
			[
				'label'      => __( 'Padding', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} #edd_user_history' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		// $this->add_group_control(
		// 	Group_Control_Background::get_type(),
		// 	[
		// 		'name'     => 'sections_bg',
		// 		'types'    => [ 'classic', 'gradient' ],
		// 		'selector' => '{{WRAPPER}} #edd_user_history',
		// 	]
		// );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'sections_border',
				'selector' => '{{WRAPPER}} #edd_user_history',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'sections_box_shadow',
				'selector' => '{{WRAPPER}} #edd_user_history',
			]
		);

		$this->end_controls_section();
	}
	protected function __edd_download_table_style_controls() {
		$this->start_controls_section(
			'_section_style_purchase_table',
			[
				'label' => __( 'Download Table', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'_heading_purchase_table',
			[
				'label' => __( 'Table', 'happy-addons-pro' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'section_purchase_table_typography',
				'label'    => __( 'Typography', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} #edd_user_history',
			]
		);

		// $this->add_group_control(
		// 	Group_Control_Background::get_type(),
		// 	[
		// 		'name'     => 'section_purchase_table_background',
		// 		'types'    => [ 'classic', 'gradient' ],
		// 		'exclude'	=> [ 'image' ],
		// 		'selector' => '{{WRAPPER}} #edd_user_history',
		// 	]
		// );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'section_purchase_table_border',
				'label'       => __( 'Border', 'happy-addons-pro' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} #edd_user_history',
			]
		);

		$this->add_responsive_control(
			'section_purchase_table_border_radius',
			[
				'label'      => __( 'Border Radius', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} #edd_user_history' => 'border-collapse: inherit;border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => 'section_purchase_table_box_shadow',
				'separator' => 'before',
				'selector'  => '{{WRAPPER}} #edd_user_history',
			]
		);

		$this->add_control(
			'_heading_purchase_table_head',
			[
				'label'     => __( 'Table Head', 'happy-addons-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'section_purchase_table_head_typography',
				'label'    => __( 'Typography', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} #edd_user_history th',
			]
		);

		$this->add_control(
			'section_review_order_table_head_text_color',
			[
				'label'     => __( 'Text Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_user_history th' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'section_review_order_table_head_background_color',
			[
				'label'     => __( 'Background Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_user_history th' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'_heading_purchase_items',
			[
				'label'     => __( 'Download Items', 'happy-addons-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'download_row_padding',
			[
				'label' => esc_html__( 'Padding', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} #edd_user_history td' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'purchase_items_row_separator_type',
			[
				'label'     => __( 'Separator Type', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'solid',
				'options'   => [
					'none'   => __( 'None', 'happy-addons-pro' ),
					'solid'  => __( 'Solid', 'happy-addons-pro' ),
					'dotted' => __( 'Dotted', 'happy-addons-pro' ),
					'dashed' => __( 'Dashed', 'happy-addons-pro' ),
					'double' => __( 'Double', 'happy-addons-pro' ),
				],
				'selectors' => [
					'{{WRAPPER}} #edd_user_history td' => 'border-bottom-style: {{VALUE}};',
					'{{WRAPPER}} #edd_user_history th' => 'border-bottom-style: {{VALUE}};',
					'{{WRAPPER}} #edd_user_history td:not(:last-child)' => 'border-right-style: {{VALUE}};',
					'{{WRAPPER}} #edd_user_history th:not(:last-child)' => 'border-right-style: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'purchase_items_row_separator_color',
			[
				'label'     => __( 'Separator Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_user_history td' => 'border-bottom-color: {{VALUE}};',
					'{{WRAPPER}} #edd_user_history th' => 'border-bottom-color: {{VALUE}};',
					'{{WRAPPER}} #edd_user_history td:not(:last-child)' => 'border-right-color: {{VALUE}};',
					'{{WRAPPER}} #edd_user_history th:not(:last-child)' => 'border-right-color: {{VALUE}};',
				],
				'condition' => [
					'purchase_items_row_separator_type!' => 'none',
				],
			]
		);

		$this->add_responsive_control(
			'purchase_items_row_separator_size',
			[
				'label'     => __( 'Separator Size', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => '',
				],
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 20,
					],
				],
				'selectors' => [
					'{{WRAPPER}} #edd_user_history td' => 'border-bottom-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} #edd_user_history th' => 'border-bottom-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} #edd_user_history td:not(:last-child)' => 'border-right-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} #edd_user_history th:not(:last-child)' => 'border-right-width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'purchase_items_row_separator_type!' => 'none',
				],
			]
		);

		$this->start_controls_tabs( 'purchase_items_rows_tabs_style' );

		$this->start_controls_tab(
			'purchase_items_even_row',
			[
				'label' => __( 'Even Row', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'purchase_items_even_row_text_color',
			[
				'label'     => __( 'Text Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_user_history .edd_download_history_row:nth-child(even) td' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'purchase_items_even_row_links_color',
			[
				'label'     => __( 'Links Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_user_history .edd_download_history_row:nth-child(even) a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'purchase_items_even_row_background_color',
			[
				'label'     => __( 'Background Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_user_history .edd_download_history_row:nth-child(even) td' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'purchase_items_odd_row',
			[
				'label' => __( 'Odd Row', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'purchase_items_odd_row_text_color',
			[
				'label'     => __( 'Text Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_user_history .edd_download_history_row:nth-child(odd) td' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'purchase_items_odd_row_links_color',
			[
				'label'     => __( 'Links Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_user_history .edd_download_history_row:nth-child(odd) a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'purchase_items_odd_row_background_color',
			[
				'label'     => __( 'Background Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_user_history .edd_download_history_row:nth-child(odd) td' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		/* $this->add_control(
			'_heading_purchase_table_image',
			[
				'label'                 => __( 'Image', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'				=> 'before',
			]
		);

		$this->add_responsive_control(
			'purchase_items_image_width',
			[
				'label'                 => __( 'Width', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::SLIDER,
				'size_units'            => [ 'px' ],
				'default'               => [
					'size' => '',
				],
				'range'                => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .woocommerce table.cart .product-thumbnail img' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'purchase_items_quantity_input_heading',
			[
				'label'                 => __( 'Quantity Input', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'				=> 'before',
			]
		);

		$this->add_responsive_control(
			'purchase_items_quantity_input_width',
			[
				'label'                 => __( 'Width', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::SLIDER,
				'size_units'            => [ 'px' ],
				'default'               => [
					'size' => '',
				],
				'range'                => [
					'px' => [
						'min' => 20,
						'max' => 500,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .woocommerce .cart .quantity .input-text' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'purchase_items_quantity_input_padding',
			[
				'label'                 => __( 'Padding', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .woocommerce .cart .quantity .input-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'purchase_items_quantity_input_bg_color',
			[
				'label'                 => __( 'Background Color', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .woocommerce .cart .quantity .input-text' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'purchase_items_quantity_input_border',
				'label'                 => __( 'Border', 'happy-addons-pro' ),
				'selector'              => '{{WRAPPER}} .woocommerce .cart .quantity .input-text',
			]
		);

		$this->add_responsive_control(
			'purchase_items_quantity_input_border_radius',
			[
				'label'                 => __( 'Border Radius', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .woocommerce .cart .quantity .input-text' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'_heading_purchase_table_product_remove',
			[
				'label'                 => __( 'Product Remove', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'				=> 'before',
			]
		);

		$this->add_control(
			'purchase_items_remove_icon_color',
			[
				'label'                 => __( 'Color', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .woocommerce table.cart .remove' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'purchase_items_remove_icon_color_hover',
			[
				'label'                 => __( 'Hover Color', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .woocommerce table.cart .remove:hover' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'purchase_items_remove_icon_bg_hover',
			[
				'label'                 => __( 'Hover Background', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .woocommerce table.cart .remove:hover' => 'background-color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_responsive_control(
			'purchase_items_remove_icon_size',
			[
				'label'                 => __( 'Size', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::SLIDER,
				'size_units'            => [ 'px', 'em' ],
				'default'               => [
					'size' => '',
				],
				'range'                => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .woocommerce table.cart .remove' => 'font-size: {{SIZE}}{{UNIT}}; font-family: arial; display: flex; align-items: center; justify-content: center;',
				],
			]
		);

		$this->add_control(
			'_heading_purchase_table_update_purchase_row',
			[
				'label'                 => __( 'Update Download Row', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'				=> 'before',
			]
		);

		$this->add_control(
			'purchase_table_update_purchase_row_bg',
			[
				'label'                 => __( 'Background', 'happy-addons-pro' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .woocommerce table.cart tr td.actions' => 'background-color: {{VALUE}} !important;',
				],
			]
		); */

		$this->end_controls_section();
	}



	public static function show_edd_missing_alert() {
		if ( current_user_can( 'activate_plugins' ) ) {
			printf(
				'<div %s>%s</div>',
				'style="margin: 1rem;padding: 1rem 1.25rem;border-left: 5px solid #f5c848;color: #856404;background-color: #fff3cd;"',
				__( 'Easy Digital Downloads is missing! Please install and activate Easy Digital Downloads.', 'happy-addons-pro' )
			);
		}
	}

	protected function render() {
		if ( ! function_exists( 'EDD' ) ) {
			self::show_edd_missing_alert();
			return;
		}

		$settings = $this->get_settings_for_display();
		$redirect = isset( $settings['redirect_url']['url'] ) ? $settings['redirect_url']['url'] : '';

		$atts = [
			'redirect' => $redirect,
		];
		if( ha_elementor()->editor->is_edit_mode() ){
			$this->download_editor_preview();
		}else{

			echo ha_do_shortcode( 'download_history' );
		}

	}

	protected function download_editor_preview(){

	do_action( 'edd_before_download_history' ); ?>
	<table id="edd_user_history" class="edd-table">
		<thead>
			<tr class="edd_download_history_row">
				<?php do_action( 'edd_download_history_header_start' ); ?>
				<th class="edd_download_download_name"><?php _e( 'Download Name', 'happy-addons-pro' ); ?></th>
				<?php if ( ! edd_no_redownload() ) : ?>
					<th class="edd_download_download_files"><?php _e( 'Files', 'happy-addons-pro' ); ?></th>
				<?php endif; //End if no redownload?>
				<?php do_action( 'edd_download_history_header_end' ); ?>
			</tr>
		</thead>
		<tbody>
			<tr class="edd_download_history_row">
				<td class="edd_download_download_name">A Music Album</td>
				<td class="edd_download_download_files">
					No downloadable files found.							
				</td>
			</tr>
			<tr class="edd_download_history_row">
				<td class="edd_download_download_name">A Music Album</td>
				<td class="edd_download_download_files">
					No downloadable files found.							
				</td>
			</tr>
			<tr class="edd_download_history_row">
				<td class="edd_download_download_name">A Sample Digital Download</td>
				<td class="edd_download_download_files">
					<div class="edd_download_file">
						<a href="#" class="edd_download_file_link">
						Download Files												</a>
					</div>
				</td>
			</tr>
		</tbody>
	</table>
	<?php
	}
}
