<?php

namespace ElementPack\Modules\AdblockDetector;
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
		return 'bdt-adblock-detector';
	}
 
	public function register_controls($section) {

		$section->start_controls_section(
			'element_pack_adblock_detector_section',
			[
				'tab'   => Controls_Manager::TAB_SETTINGS,
				'label' => BDTEP_CP . esc_html__('AdBlock Detector', 'bdthemes-element-pack'),
			]
		);

		$section->add_control(
			'ep_adblock_detector_enable',
			[
				'label'              => esc_html__('AdBlock Detector?', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::SWITCHER,
				'render_type'        => 'template',
			]
		);

		$section->end_controls_section();
	}



	public function header_script_render() {

		if ( Plugin::instance()->editor->is_edit_mode() || Plugin::instance()->preview->is_preview_mode() ) {
			return;
		}
		
		$document = Plugin::instance()->documents->get( get_the_ID() );
		
		if ( !$document ) {
			return;
		}
		
		$custom_js = $document->get_settings( 'ep_adblock_detector_enable' );
		
		if ( empty( $custom_js ) ) {
			return;
		} 
		
		?>
		<link rel="stylesheet" href="<?php echo BDTEP_ASSETS_URL; ?>vendor/css/abdetector.style.css">
		<?php
	}


	public function footer_script_render() {

		if ( Plugin::instance()->editor->is_edit_mode() || Plugin::instance()->preview->is_preview_mode() ) {
			return;
		}
		
		$document = Plugin::instance()->documents->get( get_the_ID() );
		
		if ( !$document ) {
			return;
		}
		
		$custom_js = $document->get_settings( 'ep_adblock_detector_enable' );
		
		if ( empty( $custom_js ) ) {
			return;
		} 
		
		?>
		<script src="<?php echo BDTEP_ASSETS_URL; ?>vendor/js/abdetector.script.min.js"></script>

		<script>
		window.addEventListener('load', function (event) {
			abDetectorPro.init();
		});
		</script>
		<?php
	}

	protected function add_actions() {

		add_action('elementor/documents/register_controls', [$this, 'register_controls'], 1, 1);

		add_action( 'wp_head', [$this, 'header_script_render'], 999 );
		add_action( 'wp_footer', [$this, 'footer_script_render'], 999 );
		
	}
}
