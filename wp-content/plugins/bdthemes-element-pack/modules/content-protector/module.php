<?php

namespace ElementPack\Modules\ContentProtector;
use Elementor\Controls_Manager;
use Elementor\Plugin;
use ElementPack\Base\Element_Pack_Module_Base;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function __construct() {
		parent::__construct();
		$this->add_actions();
	}

	public function get_name() {
		return 'bdt-content-protector';
	}
 
	public function register_controls($section) {

		$section->start_controls_section(
			'element_pack_content_protector_section',
			[
				'tab'   => Controls_Manager::TAB_SETTINGS,
				'label' => BDTEP_CP . esc_html__('Content Protector', 'bdthemes-element-pack'),
			]
		);

		$section->add_control(
			'ep_content_protector_enable',
			[
				'label'              => esc_html__('Content Protector?', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::SWITCHER,
				'render_type'        => 'template',
			]
		);

		$section->end_controls_section();
	}



	public function footer_script_render() {

		if ( Plugin::instance()->editor->is_edit_mode() || Plugin::instance()->preview->is_preview_mode() ) {
			return;
		}
		
		$document = Plugin::instance()->documents->get( get_the_ID() );
		
		if ( !$document ) {
			return;
		}
		
		$custom_js = $document->get_settings( 'ep_content_protector_enable' );
		
		if ( empty( $custom_js ) ) {
			return;
		} 
		
		?>
		<script src="<?php echo BDTEP_ASSETS_URL; ?>vendor/js/content-protector.min.js"></script>
		<?php
	}

	protected function add_actions() {

		add_action('elementor/documents/register_controls', [$this, 'register_controls'], 1, 1);

		add_action( 'wp_footer', [$this, 'footer_script_render'], 999 );
		
	}
}
