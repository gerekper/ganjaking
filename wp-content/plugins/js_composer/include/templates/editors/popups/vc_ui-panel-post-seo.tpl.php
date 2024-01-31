<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
/**
 * @var array $template_variables
 * @var bool $can_unfiltered_html_cap
 * @var WP_Post | null $post
 * @var int $post_id
 * @var Vc_Post_Seo $vc_post_seo
 * @var string $permalink_structure
 */
?>
<div class="vc_ui-font-open-sans vc_ui-panel-window vc_media-xs vc_ui-panel" data-vc-panel=".vc_ui-panel-header-header" data-vc-ui-element="panel-post-seo" id="vc_ui-panel-post-seo">
	<div class="vc_ui-panel-window-inner">
		<?php
		vc_include_template('editors/popups/vc_ui-header.tpl.php', array(
			'title' => esc_html__( 'WPBakery SEO', 'js_composer' ),
			'controls' => array( 'minimize', 'close' ),
			'header_css_class' => 'vc_ui-post-settings-header-container',
			'content_template' => 'editors/partials/add_element_tabs.tpl.php',
			'template_variables' => $template_variables,
			'is_default_tab' => true,
		));
		?>
		<div class="vc_ui-panel-content-container">
			<div class="vc_ui-panel-content" data-vc-ui-element="panel-content">
				<form id="vc_setting-seo-form" action method="post">
					<div class="vc_panel-tabs">
						<?php
						foreach ( $template_variables['templates'] as $key => $template_name ) {
							$active_class = 0 === $key ? ' vc_active' : '';
							echo '<div id="vc_seo-tab-' . esc_attr( $key ) .'" class="vc_panel-tab vc_row' . esc_attr( $active_class ) . '" data-tab-index="' . esc_attr( $key ) .'">';
								$seo_settings = get_post_meta( $post_id, '_wpb_post_custom_seo_settings', true );
								vc_include_template(
									$template_name,
									[
										'is_active' => 0 === $key,
										'seo_settings' => json_decode( $seo_settings, true ),
										'post' => $post,
										'post_id' => $post_id,
										'vc_post_seo' => $vc_post_seo,
										'permalink_structure' => $permalink_structure,
									]
								);
							echo '</div>';
						}
						?>
					</div>
				</form>
			</div>
		</div>
		<!-- param window footer-->
		<?php
		vc_include_template('editors/popups/vc_ui-footer.tpl.php', array(
			'controls' => array(
				array(
					'name' => 'close',
					'label' => esc_html__( 'Close', 'js_composer' ),
				),
				array(
					'name' => 'save',
					'label' => esc_html__( 'Save changes', 'js_composer' ),
					'css_classes' => 'vc_ui-button-fw',
					'style' => 'action',
				),
			),
		));
		?>
	</div>
</div>
