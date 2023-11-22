<?php
namespace ElementPack\Modules\DocumentViewer\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Document_Viewer extends Module_Base {

	public function get_name() {
		return 'bdt-document-viewer';
	}

	public function get_title() {
		return BDTEP . esc_html__( 'Document Viewer', 'bdthemes-element-pack' );
	}

	public function get_icon() {
		return 'bdt-wi-document-viewer';
	}

	public function get_categories() {
		return [ 'element-pack' ];
	}

	public function get_keywords() {
		return [ 'document', 'viewer', 'record', 'file' ];
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/8Ar9NQe93vg';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content_layout',
			[
				'label' => esc_html__( 'Layout', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'file_source',
			[
				'label'         => esc_html__( 'File Source', 'bdthemes-element-pack' ),
				'description'   => esc_html__( 'any type of document file: pdf, xls, docx, ppt etc', 'bdthemes-element-pack' ),
				'type'          => Controls_Manager::URL,
				'dynamic'       => [ 'active' => true ],
				'placeholder'   => esc_html__( 'https://example.com/sample.pdf', 'bdthemes-element-pack' ),
				'label_block'   => true,
				'show_external' => false,
			]
		);

		$this->add_responsive_control(
			'document_height',
			[
				'label' => esc_html__( 'Document Height', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 800,
				],
				'range' => [
					'px' => [
						'min'  => 200,
						'max'  => 1500,
						'step' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-document-viewer iframe' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

	}

	public function render() {
		$settings  = $this->get_settings_for_display();
		// old
		// $final_url = ($settings['file_source']['url']) ? '//docs.google.com/viewer?url='. esc_url($settings['file_source']['url']) : false;

		// fixed 19 Dec 2020
		$final_url = ($settings['file_source']['url']) ? '//docs.google.com/viewer?url='. esc_url($settings['file_source']['url']) .'&embedded=true' : false;
		?>

		<?php if ($final_url) : ?>
        <div class="bdt-document-viewer">
        	<iframe src="<?php echo esc_url($final_url); ?>" class="bdt-document"></iframe>
        </div>
        <?php else : ?>
        	<div class="bdt-alert-warning" bdt-alert>
        	    <a class="bdt-alert-close" bdt-close></a>
        	    <p><?php esc_html_e( 'Please enter correct URL of your document.', 'bdthemes-element-pack' ); ?></p>
        	</div>
        <?php endif; ?>

		<?php
	}
}
