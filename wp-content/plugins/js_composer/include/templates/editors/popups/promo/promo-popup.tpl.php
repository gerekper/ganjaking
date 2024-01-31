<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>

<div class="vc_modal modal-backdrop vc_modal-popup-container vc_active" id="vc_ui-helper-promo-popup">
	<div class="vc_ui-font-open-sans vc_media-xs vc_modal-popup-content" >
		<div class="vc_ui-panel-window-inner">
			<?php
			vc_include_template('editors/popups/vc_ui-header.tpl.php', array(
				'title' => esc_html__( 'What\'s new in WPBakery', 'js_composer' ),
				'controls' => array( 'close' ),
				'header_css_class' => 'vc_ui-post-settings-header-container',
				'content_template' => '',
			));
			?>
			<div class="vc_ui-panel-content-container">
				<div class="vc_ui-panel-content">
					<?php
					vc_include_template( 'editors/partials/promo-content.tpl.php', array(
						'is_about_page' => false,
					) );
					?>
				</div>
			</div>
			<!-- param window footer-->
			<?php
			vc_include_template('editors/popups/vc_ui-footer.tpl.php', array(
				'controls' => array(
					array(
						'name' => 'close',
						'label' => esc_html__( 'Got It!', 'js_composer' ),
						'css_classes' => 'vc_ui-button-fw',
						'style' => 'action',
					),
				),
			));
			?>
		</div>
	</div>
</div>
