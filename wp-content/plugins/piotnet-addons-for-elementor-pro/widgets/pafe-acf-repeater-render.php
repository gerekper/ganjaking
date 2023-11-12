<?php

class PAFE_ACF_Repeater_Render extends \Elementor\Widget_Base {

	public function get_name() {
		return 'pafe-acf-repeater-render';
	}

	public function get_title() {
		return __( 'PAFE ACF Repeater Render', 'pafe' );
	}

	public function get_icon() {
		return 'fas fa-redo';
	}

	public function get_categories() {
		return [ 'pafe' ];
	}

	public function get_keywords() {
		return [ 'acf', 'repeater' ];
	}

	public function get_style_depends() {
		return [ 
			'pafe-widget-style'
		];
	}

	protected function _register_controls() {

		$this->start_controls_section(
			'pafe_acf_repeater_render_section',
			[
				'label' => __( 'PAFE ACF Repeater Render', 'pafe' ),
			]
		);

		$this->add_control(
			'pafe_acf_repeater_render_template_shortcode',
			[
				'label' => __( 'Template Shortcode', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
			]
		);

		$this->add_responsive_control(
			'pafe_acf_repeater_render_column_width',
			[
				'label' => __( 'Column Width (%)', 'pafe' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 100,
				'min' => 1,
				'max' => 100,
				'selectors' => [
					'{{WRAPPER}}>.elementor-widget-container>.pafe-acf-repeater>.pafe-acf-repeater-item' => 'width: {{VALUE}}%;',
				],
			]
		);

		$columns_margin = is_rtl() ? '0 0 -{{SIZE}}{{UNIT}} -{{SIZE}}{{UNIT}};' : '0 -{{SIZE}}{{UNIT}} -{{SIZE}}{{UNIT}} 0;';
		$columns_padding = is_rtl() ? '0 0 {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}};' : '0 {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}} 0;';

		$this->add_responsive_control(
			'pafe_acf_repeater_render_column_spacing',
			[
				'label' => __( 'Column Spacing', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}}>.elementor-widget-container>.pafe-acf-repeater>.pafe-acf-repeater-item' => 'padding:' . $columns_padding,
					'{{WRAPPER}}>.elementor-widget-container>.pafe-acf-repeater' => 'margin: ' . $columns_margin,
				],
			]
		);

		$this->add_responsive_control(
			'pafe_acf_repeater_render_row_spacing',
			[
				'label' => __( 'Row Spacing', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}}>.elementor-widget-container>.pafe-acf-repeater>.pafe-acf-repeater-item' => 'margin-bottom:{{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'pafe_acf_repeater_render_item_background',
				'label' => __( 'Item Background', 'pafe' ),
				'types' => [ 'classic', 'gradient', 'video' ],
				'selector' => '{{WRAPPER}}>.elementor-widget-container>.pafe-acf-repeater>.pafe-acf-repeater-item',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'pafe_acf_repeater_render_item_border',
				'label' => __( 'Item Border', 'pafe' ),
				'selector' => '{{WRAPPER}}>.elementor-widget-container>.pafe-acf-repeater>.pafe-acf-repeater-item',
			]
		);

		$this->add_responsive_control(
			'pafe_acf_repeater_render_item_border_radius',
			[
				'label' => __( 'Item Border Radius', 'pafe' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}}>.elementor-widget-container>.pafe-acf-repeater>.pafe-acf-repeater-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'pafe_acf_repeater_render_item_box_shadow',
				'label' => __( 'Item Box Shadow', 'pafe' ),
				'selector' => '{{WRAPPER}}>.elementor-widget-container>.pafe-acf-repeater>.pafe-acf-repeater-item',
			]
		);

		$this->end_controls_section();
	}

	public function pafeDOMinnerHTML(DOMNode $element) { 
	    $innerHTML = ""; 
	    $children  = $element->childNodes;

	    foreach ($children as $child) 
	    { 
	        $innerHTML .= $element->ownerDocument->saveHTML($child);
	    }

	    return $innerHTML; 
	} 

	protected function render() {
		$settings = $this->get_settings_for_display();
		$pafe_acf_repeater_render_template_shortcode = $settings['pafe_acf_repeater_render_template_shortcode'];

		if ( !empty( $pafe_acf_repeater_render_template_shortcode ) ) {
			if (function_exists('get_field')) :

				$shortcode_id = 0 - (int) filter_var($pafe_acf_repeater_render_template_shortcode, FILTER_SANITIZE_NUMBER_INT);
				$pafe_acf_repeater_name = get_post_meta( $shortcode_id, '_pafe_acf_repeater_name', true);

				if (!empty($pafe_acf_repeater_name)) :
					$pafe_acf_repeater_name = explode(',', $pafe_acf_repeater_name);
					$pafe_acf_repeater = get_field($pafe_acf_repeater_name[0], get_the_ID());
					if (!empty($pafe_acf_repeater)) :
						if (count($pafe_acf_repeater_name) == 1) {
							$content = '<div class="pafe-acf-repeater">';
						} else {
							$content = '<div class="pafe-acf-repeater" data-pafe-acf-repeater-level-second=' . $pafe_acf_repeater_name[1] . '>';
						}
						foreach ($pafe_acf_repeater as $repeater_index => $repeater_item ) :
							if (count($pafe_acf_repeater_name) == 1) {
								$content .= '<div class="pafe-acf-repeater-item" data-pafe-acf-repeater-item="' . $repeater_index . '">';
								$content .= do_shortcode($pafe_acf_repeater_render_template_shortcode);
								$content .= '</div>';
							} else {
								foreach ($repeater_item[$pafe_acf_repeater_name[1]] as $repeater_index_level_second => $repeater_item_level_second ) {
									$content .= '<div class="pafe-acf-repeater-item" data-pafe-acf-repeater-item-sub="' . $repeater_index . '">';
									$content_level_second = do_shortcode($pafe_acf_repeater_render_template_shortcode);
									$dochtml_level_second = new DOMDocument();
									@$dochtml_level_second->loadHTML($content_level_second);
									$xpath_level_second = new DOMXPath($dochtml_level_second);
									$nodeListLevelSecond = $xpath_level_second->query("//div[@data-pafe-acf-repeater-sub-field]");

									// Level Second
									if (!empty($nodeListLevelSecond)) {
										foreach ($nodeListLevelSecond as $node_index_level_second => $node_item_level_second) {
											$pafe_acf_repeater_sub_field_name_level_second = $node_item_level_second->getAttribute('data-pafe-acf-repeater-sub-field');
											$pafe_acf_repeater_sub_field_type_level_second = $node_item_level_second->getAttribute('data-pafe-acf-repeater-sub-field-type');
											$old_html = '<div class="pafe-acf-repeater-sub-field" data-pafe-acf-repeater-sub-field="' . $pafe_acf_repeater_sub_field_name_level_second . '" data-pafe-acf-repeater-sub-field-type="' . $pafe_acf_repeater_sub_field_type_level_second . '"></div>';
											$inner_html = $repeater_item[$pafe_acf_repeater_name[1]][$repeater_index_level_second][$pafe_acf_repeater_sub_field_name_level_second];
											if ($pafe_acf_repeater_sub_field_type_level_second == 'image') {
						        				$inner_html = '<div class="elementor-image"><img src="' . $inner_html['url'] . '"></div>';
						        			}
											$replace_html = '<div class="pafe-acf-repeater-sub-field-level-second">' . $inner_html . '</div>';
											$content_level_second = str_replace($old_html, $replace_html, $content_level_second);
										}
									}
									$content .= $content_level_second;
									$content .= '</div>';
								}
							}
							
							$dochtml = new DOMDocument('1.0', 'UTF-8');
							@$dochtml->loadHTML('<?xml encoding="UTF-8">' . $content);
							$xpath = new DOMXPath($dochtml);
							$nodeList = $xpath->query("//div[@data-pafe-acf-repeater-sub-field]");

							if (!empty($nodeList)) {
								foreach ($nodeList as $node_index => $node_item) {
									$pafe_acf_repeater_sub_field_name = $node_item->getAttribute('data-pafe-acf-repeater-sub-field');
									$pafe_acf_repeater_sub_field_type = $node_item->getAttribute('data-pafe-acf-repeater-sub-field-type');
									$old_html = '<div class="pafe-acf-repeater-sub-field" data-pafe-acf-repeater-sub-field="' . $pafe_acf_repeater_sub_field_name . '" data-pafe-acf-repeater-sub-field-type="' . $pafe_acf_repeater_sub_field_type . '"></div>';
									$inner_html = $repeater_item[$pafe_acf_repeater_sub_field_name];
									if ($pafe_acf_repeater_sub_field_type == 'image') {
				        				$inner_html = '<div class="elementor-image"><img src="' . $inner_html['url'] . '"></div>';
				        			}
									$replace_html = '<div class="pafe-acf-repeater-sub-field">' . $inner_html . '</div>';
									$content = str_replace($old_html, $replace_html, $content);
								}
							}

        				endforeach;
        				$content .= '</div>';

        				$dochtml = new DOMDocument('1.0', 'UTF-8');
						@$dochtml->loadHTML('<?xml encoding="UTF-8">' . $content);
						$xpath = new DOMXPath($dochtml);
						$nodeList = $xpath->query("//div[@data-pafe-acf-repeater-item]");

						foreach ($nodeList as $node_index => $node_item) {
							$index = $node_item->getAttribute('data-pafe-acf-repeater-item');
							
							$removeNodes = $xpath->query('//div[@data-pafe-acf-repeater-item="' . $index . '"]//div[@data-pafe-acf-repeater-item-sub!="' . $index . '"]');

							foreach ($removeNodes as $removeNode) {
								$removeNode->parentNode->removeChild($removeNode);
							}
						}

						echo $dochtml->saveHTML();

        			endif;
        		endif;
        	endif;
		}

	}
}
