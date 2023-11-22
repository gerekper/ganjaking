<?php
/**
 * Premium Grid Item.
 * A placeholder widget to help the user design his custom grid.
 */

namespace PremiumAddonsPro\Includes\GridBuilder;

use Elementor\Utils;
use Elementor\Plugin;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Core\Base\Document;
use Elementor\TemplateLibrary\Source_Local;
use ElementorPro\Modules\QueryControl\Controls\Template_Query;
use ElementorPro\Modules\QueryControl\Module as QueryControlModule;
use ElementorPro\Modules\LoopBuilder\Documents\Loop as LoopDocument;

// PremiumAddons Classes.
use PremiumAddons\Includes\Helper_Functions;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Premium_Grid_Item.
 */
class Premium_Grid_Item extends Widget_Base {

	/**
	 * Retrieve Widget Name.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_name() {
		return 'premium-grid-item';
	}

	/**
	 * Retrieve Widget Title.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_title() {
		return __( 'Grid Item', 'premium-addons-pro' );
	}

	/**
	 * Retrieve Widget Icon.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_icon() {
		return 'eicon-image-box';
	}

	/**
	 * Retrieve Widget Cagegory.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_categories() {
		return array( 'premium-grid' );
	}

	/**
	 * Retrieve Widget Keywords.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_keywords() {
		return array( 'loop', 'item', 'grid', 'placeholder' );
	}

	/**
	 * Retrieve Widget Dependent CSS.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array CSS style handles.
	 */
	public function get_style_depends() {
		return array(
			'pa-loop-item',
		);
	}

	/**
	 * Register Premium Grid Item controls.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {

		$this->start_controls_section(
			'content_section',
			array(
				'label' => __( 'Content', 'premium-addons-pro' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'custom_grid_item_desc',
			array(
				'raw'             => __( 'Premium Grid Item widget acts as a placeholder for your posts to ease the grid designing process.', 'premium-addons-for-elementor' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
			)
		);

		$add_loop_ctrls = $this->add_loop_temp_controls();

		if ( $add_loop_ctrls ) {

			$this->add_control(
				'live_temp_content',
				array(
					'label'       => __( 'Template Title', 'premium-addons-pro' ),
					'type'        => Controls_Manager::TEXT,
					'classes'     => 'premium-live-temp-title control-hidden ',
					'label_block' => true,
				)
			);

			$this->add_control(
				'temp_content_live',
				array(
					'type'        => Controls_Manager::BUTTON,
					'label_block' => true,
					'button_type' => 'default papro-btn-block loop-temp',
					'text'        => __( 'Create / Edit Template', 'premium-addons-pro' ),
					'event'       => 'createLiveTemp',
				)
			);

			$this->add_control(
				'pa_loop_live_temp_id',
				array(
					'label' => __( 'Live Temp Id', 'premium-addons-pro' ),
					'type'  => Controls_Manager::HIDDEN,
				)
			);

			$this->add_control(
				'pa_loop_template_id',
				array(
					'label'              => esc_html__( 'OR Select Existing Template', 'premium-addons-for-elementor' ),
					'type'               => Template_Query::CONTROL_ID,
					'classes'            => 'premium-live-temp-label',
					'label_block'        => true,
					'autocomplete'       => array(
						'object' => QueryControlModule::QUERY_OBJECT_LIBRARY_TEMPLATE,
						'query'  => array(
							'post_status' => Document::STATUS_PUBLISH,
							'meta_query'  => array(
								array(
									'key'     => Document::TYPE_META_KEY,
									'value'   => LoopDocument::get_type(),
									'compare' => 'IN',
								),
							),
						),
					),
					'actions'            => array(
						'new'  => array(
							'visible'         => true,
							'document_config' => array(
								'type' => LoopDocument::get_type(),
							),
						),
						'edit' => array(
							'visible' => true,
						),
					),
					'frontend_available' => true,
				)
			);

		} else {
			$this->add_control(
				'custom_grid_item_notice',
				array(
					'raw'             => __( 'Custom Post Skin option requires Elementor PRO ( version 3.8.0 or higher ) & Loop Expirement to be activated.', 'premium-addons-for-elementor' ),
					'type'            => Controls_Manager::RAW_HTML,
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
				)
			);
		}

		$this->end_controls_section();
	}

	/**
	 * Render Premium Grid widget output on the frontend.
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {

		$add_loop_ctrls = $this->add_loop_temp_controls();

		if ( $add_loop_ctrls ) {

			$settings = $this->get_settings_for_display();

			$template_id = empty( $settings['pa_loop_template_id'] ) ? $settings['pa_loop_live_temp_id'] : $settings['pa_loop_template_id'];

			$display_placeholder = $this->display_grid_item_placeholder();

			if ( $display_placeholder ) {
				?>
					<div class="premium-grid-item__placeholder-wrapper">
						<div class="e-loop-empty-view__box e-loop-empty-view__box--active">
							<div class="e-loop-empty-view__box-inner"  style="color:#000">
								<img src="<?php echo esc_url( PREMIUM_ADDONS_URL . 'admin/images/pa-logo-symbol.png' ); ?>" />
								<div class="e-loop-empty-view__box-title">
									<?php echo esc_html__( 'Premium Custom Grid starts with a template.', 'elementor-pro' ); ?>
								</div>
								<div class="e-loop-empty-view__box-description">
									<?php
									echo esc_html__( 'Either choose an existing template or create a new one and use it as the template for this grid item.', 'elementor-pro' );
									?>
								</div>
							</div>
						</div>
					</div>
				<?php
			} else {
				echo '[papro_grid_item template_id="' . $template_id . '"]'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		}
	}

	/**
	 * Add loop Template Controls.
	 *
	 * @access private
	 * @since 2.8.20
	 *
	 * @return bool
	 */
	private function add_loop_temp_controls() {
		// this version should be edited to the release version.
		return version_compare( PREMIUM_ADDONS_VERSION, '4.10.0', '>=' ) && Helper_Functions::is_loop_exp_enabled();
	}

	/**
	 * Checks whether to print the placeholder or the shortcode pattern
	 * depending on the widget location.
	 *
	 * @access private
	 * @since 2.8.20
	 *
	 * @return bool  true if we're in "premium_grid" loaction.
	 */
	private function display_grid_item_placeholder() {

		$is_edit_mode = false;

		$document_type = Source_Local::get_template_type( get_the_ID() );

		if ( $document_type && 'premium-grid' === $document_type ) {
			$is_edit_mode = \Elementor\Plugin::$instance->editor->is_edit_mode();
		}

		return $is_edit_mode;
	}
}
