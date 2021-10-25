<?php

namespace MasterAddons\Addons;

// Elementor Classes
use \Elementor\Widget_Base;
use \Elementor\Controls_Manager;

class Blockquote extends Widget_Base
{

	public function get_name()
	{
		return "jltma-blockquote";
	}

	public function get_title()
	{
		return esc_html__('Blockquote', MELA_TD);
	}

	public function get_icon()
	{
		return 'ma-el-icon eicon-blockquote';
	}

	public function get_categories()
	{
		return ['master-addons'];
	}

	public function get_keywords()
	{
		return ['blockquote', 'quotation', 'author said'];
	}

	public function get_help_url()
	{
		return 'https://master-addons.com/demos/blockquote-element/';
	}

	protected function _register_controls()
	{

		//Quote Blockquote
		$this->start_controls_section(
			'jltma_blockquote_display',
			[
				'label' => esc_html__('Blockquote', MELA_TD),
			]
		);

		$this->add_control(
			'jltma_blockquote_text',
			[
				'label' => esc_html__('Quote Text', MELA_TD),
				'type' => Controls_Manager::TEXTAREA,
				'label_block' => true,
				'default' => esc_html__('Architecture should speak of its time and place, but yearn for timelessness', MELA_TD),
			]
		);

		$this->add_control(
			'jltma_blockquote_author',
			[
				'label' => esc_html__('Quote Author', MELA_TD),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'default' => esc_html__('Scott Adams', MELA_TD),
			]
		);


		// $this->add_responsive_control('jltma_blockquote_margin',
		//     [
		//         'label'         => esc_html__('Margin', MELA_TD),
		//         'type'          => Controls_Manager::DIMENSIONS,
		//         'size_units'    => ['px', 'em', '%'],
		//         'selectors'     => [
		//             '{{WRAPPER}} .btm-brder' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
		//         ]
		//     ]
		// );

		$this->end_controls_section();





		/**
		 * Content Tab: Docs Links
		 */
		$this->start_controls_section(
			'jltma_section_help_docs',
			[
				'label' => esc_html__('Help Docs', MELA_TD),
			]
		);


		$this->add_control(
			'help_doc_1',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(esc_html__('%1$s Live Demo %2$s', MELA_TD), '<a href="https://master-addons.com/demos/blockquote-element/" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);

		$this->add_control(
			'help_doc_2',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(esc_html__('%1$s Documentation %2$s', MELA_TD), '<a href="https://master-addons.com/docs/addons/blockquote-element/?utm_source=widget&utm_medium=panel&utm_campaign=dashboard" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);

		$this->add_control(
			'help_doc_3',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(esc_html__('%1$s Watch Video Tutorial %2$s', MELA_TD), '<a href="https://www.youtube.com/watch?v=sSCULgPFSHU" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);
		$this->end_controls_section();




		//Upgrade to Pro
		
	}

	protected function render()
	{
		$settings = $this->get_settings_for_display();
?>

		<blockquote class="wp-block-quote jltma-blockquote">
			<p class="jltma-text">
				<?php echo esc_html($settings['jltma_blockquote_text']); ?>
			</p>
			<cite>
				<?php echo esc_html($settings['jltma_blockquote_author']); ?>
			</cite>
		</blockquote>

<?php }
}
