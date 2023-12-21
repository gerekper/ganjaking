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
use Elementor\Group_Control_Text_Shadow;

defined( 'ABSPATH' ) || die();

class EDD_Purchase extends Base {

	/**
	 * Retrieve toggle widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'EDD Purchase', 'happy-addons-pro' );
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
		return 'hm hm-checkout-2';
	}

	public function get_keywords() {
		return [ 'edd', 'commerce', 'ecommerce', 'purchase', 'register', 'shop' ];
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
				'raw'             => __( '<strong>Note:</strong> EDD Purchase widget doesn\'t have any useful content control.', 'happy-addons-pro' ),
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
					'{{WRAPPER}} #edd_login_form' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'sections_bg',
				'types'    => [ 'classic', 'gradient' ],
				'exclude'	=> [ 'image' ],
				'selector' => '{{WRAPPER}} #edd_user_history',
			]
		);

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
				'label' => __( 'Purchase Table', 'happy-addons-pro' ),
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
					'{{WRAPPER}} #edd_user_history' => 'overflow: hidden;border-collapse: inherit;border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
			'section_purchase_table_head_padding',
			[
				'label' => esc_html__( 'Padding', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} #edd_user_history th' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
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
				'label'     => __( 'Purchase Items', 'happy-addons-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'purchase_row_padding',
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
					'{{WRAPPER}} #edd_user_history .edd_purchase_row:nth-child(even) td' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'purchase_items_even_row_links_color',
			[
				'label'     => __( 'Links Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_user_history .edd_purchase_row:nth-child(even) a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'purchase_items_even_row_background_color',
			[
				'label'     => __( 'Background Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_user_history .edd_purchase_row:nth-child(even) td' => 'background-color: {{VALUE}};',
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
					'{{WRAPPER}} #edd_user_history .edd_purchase_row:nth-child(odd) td' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'purchase_items_odd_row_links_color',
			[
				'label'     => __( 'Links Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_user_history .edd_purchase_row:nth-child(odd) a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'purchase_items_odd_row_background_color',
			[
				'label'     => __( 'Background Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #edd_user_history .edd_purchase_row:nth-child(odd) td' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

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

		if( ha_elementor()->editor->is_edit_mode() ){
			$this->editor_mode_preview_history();
		}else{
			echo ha_do_shortcode( 'purchase_history' );
		}


	}

	protected function editor_mode_preview_history(){
		$payments = edd_get_users_purchases( get_current_user_id(), 20, true, 'any' );
		// if ( $payments ) :
		do_action( 'edd_before_purchase_history', $payments ); ?>
		<table id="edd_user_history" class="edd-table">
			<thead>
				<tr class="edd_purchase_row">
					<?php do_action('edd_purchase_history_header_before'); ?>
					<th class="edd_purchase_id"><?php _e('ID','happy-addons-pro' ); ?></th>
					<th class="edd_purchase_date"><?php _e('Date','happy-addons-pro' ); ?></th>
					<th class="edd_purchase_amount"><?php _e('Amount','happy-addons-pro' ); ?></th>
					<th class="edd_purchase_details"><?php _e('Details','happy-addons-pro' ); ?></th>
					<?php do_action('edd_purchase_history_header_after'); ?>
				</tr>
			</thead>
			<tbody>
				<tr class="edd_purchase_row">
					<td class="edd_purchase_id">#544</td>
					<td class="edd_purchase_date">April 17, 2022</td>
					<td class="edd_purchase_amount">
						<span class="edd_purchase_amount">$30.00</span>
					</td>
					<td class="edd_purchase_details">
						<a href="#">View Details and Downloads</a>
					</td>
				</tr>
				<tr class="edd_purchase_row">
					<td class="edd_purchase_id">#543</td>
					<td class="edd_purchase_date">April 18, 2022</td>
					<td class="edd_purchase_amount">
						<span class="edd_purchase_amount">$42.00</span>
					</td>
					<td class="edd_purchase_details">
						<a href="#">View Details and Downloads</a>
					</td>
				</tr>
			</tbody>
		</table>
		<?php
			echo edd_pagination( 
				array(
					'type'  => 'purchase_history',
					'total' => ceil( edd_count_purchases_of_customer() / 20 ) // 20 items per page
				)
			);
		?>
		<?php do_action( 'edd_after_purchase_history', $payments ); ?>
		<?php wp_reset_postdata(); ?>
		<!-- <p class="edd-no-purchases"><?php _e('You have not made any purchases','happy-addons-pro' ); ?></p> -->
	<?php 
	// endif;
	}
}
