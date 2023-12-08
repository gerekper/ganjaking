<?php
namespace ElementPack\Modules\Changelog\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use ElementPack\Includes\Parsedown;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Changelog extends Module_Base {

	public function get_name() {
		return 'bdt-changelog';
	}

	public function get_title() {
		return __( 'Changelog', 'bdthemes-core' );
	}

	public function get_icon() {
		return 'bdt-wi-changelog';
	}

	public function get_categories() {
		return [ 'element-pack' ];
	}

	public function get_style_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-styles'];
        } else {
            return [ 'ep-changelog' ];
        }
    }

	public function get_custom_help_url() {
        return 'https://youtu.be/835Fsi2jGRI';
    }

	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Content', 'bdthemes-core' ),
			]
		);

		$this->add_control(
			'chnagelog_file_url',
			[
				'label' => __( 'Changelog File', 'bdthemes-core' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'http://example.com/changelog.md', 'bdthemes-core' ),
			]
		);

		$this->add_control(
			'cache_content',
			[
				'label'   => esc_html__( 'Cache the File', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'cache_time',
			[
				'label'     => esc_html__( 'Cache Time', 'bdthemes-element-pack' ),
				'description'     => esc_html__( 'How much hour(s) you want to cache.', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 6,
				'condition' => [
					'cache_content' => 'yes'
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_changelog',
			[
				'label' => __( 'Changelog', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs( 'tabs_added_style' );

		$this->start_controls_tab(
			'tab_title',
			[
				'label' => esc_html__( 'Title', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'changelog_title_color',
			[
				'label'     => __( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-changelog h2' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'changelog_title_spacing',
			[
				'label' => __( 'Spacing', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-changelog h2' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'label'    => esc_html__( 'Typography', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} .bdt-ep-changelog h2',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_added_text',
			[
				'label' => esc_html__( 'Text', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'changelog_text_color',
			[
				'label'     => __( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-changelog ul li' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'changelog_link_text_color',
			[
				'label'     => __( 'Link Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-changelog ul li a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'changelog_button_spacing',
			[
				'label' => __( 'Spacing', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-changelog ul li .bdt-label' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'added_text_typography',
				'label'    => esc_html__( 'Typography', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} .bdt-ep-changelog ul li',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'changelog_added_list_spacing',
			[
				'label' => __( 'List Spacing', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-changelog ul>li:nth-child(n+2)' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		
		$this->start_controls_section(
			'section_style_changelog_added',
			[
				'label' => __( 'Added', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'changelog_added_button_color',
			[
				'label'     => __( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-changelog ul li .bdt-label.bdt-added' => 'color: {{VALUE}};',
				],
			]
		);
	
		$this->add_control(
			'changelog_added_button_background',
			[
				'label'     => __( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-changelog ul li .bdt-label.bdt-added' => 'background: {{VALUE}};',
				],
			]
		);
	
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'added_button_border',
				'selector'    => '{{WRAPPER}} .bdt-ep-changelog ul li .bdt-label.bdt-added'
			]
		);
	
		$this->add_responsive_control(
			'added_button_radius',
			[
				'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-changelog ul li .bdt-label.bdt-added' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
	
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'added_button_typography',
				'label'    => esc_html__( 'Typography', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} .bdt-ep-changelog ul li .bdt-label.bdt-added',
			]
		);
			
		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_changelog_fixed',
			[
				'label' => __( 'Fixed', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'changelog_fixed_button_color',
			[
				'label'     => __( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-changelog ul li .bdt-label.bdt-fixed' => 'color: {{VALUE}};',
				],
			]
		);
	
		$this->add_control(
			'changelog_fixed_button_background',
			[
				'label'     => __( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-changelog ul li .bdt-label.bdt-fixed' => 'background: {{VALUE}};',
				],
			]
		);
	
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'fixed_button_border',
				'selector'    => '{{WRAPPER}} .bdt-ep-changelog ul li .bdt-label.bdt-fixed'
			]
		);
	
		$this->add_responsive_control(
			'fixed_button_radius',
			[
				'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-changelog ul li .bdt-label.bdt-fixed' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
	
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'fixed_button_typography',
				'label'    => esc_html__( 'Typography', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} .bdt-ep-changelog ul li .bdt-label.bdt-fixed',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_changelog_update',
			[
				'label' => __( 'Updated', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'changelog_update_button_color',
			[
				'label'     => __( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-changelog ul li .bdt-label.bdt-update' => 'color: {{VALUE}};',
				],
			]
		);
	
		$this->add_control(
			'changelog_update_button_background',
			[
				'label'     => __( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-changelog ul li .bdt-label.bdt-update' => 'background: {{VALUE}};',
				],
			]
		);
	
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'update_button_border',
				'selector'    => '{{WRAPPER}} .bdt-ep-changelog ul li .bdt-label.bdt-update'
			]
		);
	
		$this->add_responsive_control(
			'update_button_radius',
			[
				'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-changelog ul li .bdt-label.bdt-update' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
	
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'update_button_typography',
				'label'    => esc_html__( 'Typography', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} .bdt-ep-changelog ul li .bdt-label.bdt-update',
			]
		);

		$this->end_controls_section();
		
		$this->start_controls_section(
			'section_style_changelog_note',
			[
				'label' => __( 'Note', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'changelog_note_button_color',
			[
				'label'     => __( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-changelog ul li .bdt-label.bdt-note' => 'color: {{VALUE}};',
				],
			]
		);
	
		$this->add_control(
			'changelog_note_button_background',
			[
				'label'     => __( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-changelog ul li .bdt-label.bdt-note' => 'background: {{VALUE}};',
				],
			]
		);
	
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'note_button_border',
				'selector'    => '{{WRAPPER}} .bdt-ep-changelog ul li .bdt-label.bdt-note'
			]
		);
	
		$this->add_responsive_control(
			'note_button_radius',
			[
				'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-changelog ul li .bdt-label.bdt-note' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
	
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'note_button_typography',
				'label'    => esc_html__( 'Typography', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} .bdt-ep-changelog ul li .bdt-label.bdt-note',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_changelog_remove',
			[
				'label' => __( 'Removed', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'changelog_remove_button_color',
			[
				'label'     => __( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-changelog ul li .bdt-label.bdt-remove' => 'color: {{VALUE}};',
				],
			]
		);
	
		$this->add_control(
			'changelog_remove_button_background',
			[
				'label'     => __( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-changelog ul li .bdt-label.bdt-remove' => 'background: {{VALUE}};',
				],
			]
		);
	
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'remove_button_border',
				'selector'    => '{{WRAPPER}} .bdt-ep-changelog ul li .bdt-label.bdt-remove'
			]
		);
	
		$this->add_responsive_control(
			'remove_button_radius',
			[
				'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-changelog ul li .bdt-label.bdt-remove' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
	
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'remove_button_typography',
				'label'    => esc_html__( 'Typography', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} .bdt-ep-changelog ul li .bdt-label.bdt-remove',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_changelog_language',
			[
				'label' => __( 'Language', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'changelog_language_button_color',
			[
				'label'     => __( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-changelog ul li .bdt-label.bdt-language' => 'color: {{VALUE}};',
				],
			]
		);
	
		$this->add_control(
			'changelog_language_button_background',
			[
				'label'     => __( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-changelog ul li .bdt-label.bdt-language' => 'background: {{VALUE}};',
				],
			]
		);
	
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'language_button_border',
				'selector'    => '{{WRAPPER}} .bdt-ep-changelog ul li .bdt-label.bdt-language'
			]
		);
	
		$this->add_responsive_control(
			'language_button_radius',
			[
				'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-changelog ul li .bdt-label.bdt-language' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
	
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'language_button_typography',
				'label'    => esc_html__( 'Typography', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} .bdt-ep-changelog ul li .bdt-label.bdt-language',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_changelog_changed',
			[
				'label' => __( 'Changed', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'changelog_changed_button_color',
			[
				'label'     => __( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-changelog ul li .bdt-label.bdt-changed' => 'color: {{VALUE}};',
				],
			]
		);
	
		$this->add_control(
			'changelog_changed_button_background',
			[
				'label'     => __( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-changelog ul li .bdt-label.bdt-changed' => 'background: {{VALUE}};',
				],
			]
		);
	
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'changed_button_border',
				'selector'    => '{{WRAPPER}} .bdt-ep-changelog ul li .bdt-label.bdt-changed'
			]
		);
	
		$this->add_responsive_control(
			'changed_button_radius',
			[
				'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-changelog ul li .bdt-label.bdt-changed' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
	
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'changed_button_typography',
				'label'    => esc_html__( 'Typography', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} .bdt-ep-changelog ul li .bdt-label.bdt-changed',
			]
		);

		$this->end_controls_section();

	}

	protected function render() {

		$settings = $this->get_settings_for_display();
		$id       = $this->get_id();

        echo '<div class="bdt-ep-changelog" data-bdt-scrollspy="target: > ul > li; cls: bdt-animation-slide-right-small; delay: 50">';


			if ( $settings['cache_content'] ) {
				$data = get_transient( 'ep_changelog_widget_' . $id );
			} else {
				$data = false;
			}

    		if ( false === $data ) {

	            $args = [
					'sslverify'   => false,
					'timeout'     => 120,
					'redirection' => 5,
					'cookies'     => array()
				];

	            $response = wp_remote_get( $settings['chnagelog_file_url'], $args );

	            if ( is_array( $response ) && ! is_wp_error( $response ) ) {
					$Parsedown              = new Parsedown();
					$Parsedown->addTag      = '<span class="bdt-label bdt-added">'. esc_html__( 'Added', 'bdthemes-element-pack' ) . '</span>';
					$Parsedown->removeTag   = '<span class="bdt-label bdt-remove">'. esc_html__( 'Removed', 'bdthemes-element-pack' ) . '</span>';
					$Parsedown->updateTag   = '<span class="bdt-label bdt-update">'. esc_html__( 'Updated', 'bdthemes-element-pack' ) . '</span>';
					$Parsedown->changedTag  = '<span class="bdt-label bdt-changed">'. esc_html__( 'Changed', 'bdthemes-element-pack' ) . '</span>';
					$Parsedown->fixedTag    = '<span class="bdt-label bdt-fixed">'. esc_html__( 'Fixed', 'bdthemes-element-pack' ) . '</span>';
					$Parsedown->languageTag = '<span class="bdt-label bdt-language">'. esc_html__( 'Language', 'bdthemes-element-pack' ) . '</span>';
					$Parsedown->noteTag     = '<span class="bdt-label bdt-note">'. esc_html__( 'Note', 'bdthemes-element-pack' ) . '</span>';

					$Parsedown              = $Parsedown->text($response['body']);

					if ( $settings['cache_content'] ) {
						set_transient( 'ep_changelog_widget_' . $id, $Parsedown, HOUR_IN_SECONDS * $settings['cache_time'] );
						$data = get_transient( 'ep_changelog_widget_' . $id );
					} else {
						delete_transient( 'ep_changelog_widget_' . $id );
						$data = $Parsedown;
					}
                } else {
	            	$data = 'Response error file not found!';
	            }


				//echo $Parsedown;
			}

			echo $data;





        echo '</div>';
		
	}

}
