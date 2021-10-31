<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$edit_area_width = get_post_meta( ( isset( $_REQUEST['post_id'] ) ? $_REQUEST['post_id'] : $_REQUEST['vc_post_id'] ), 'porto_edit_area_width', true );
if ( empty( $edit_area_width ) ) {
	$edit_area_width = '100%';
}
?>

<div class="vc_ui-font-open-sans vc_ui-panel-window vc_media-xs vc_ui-panel vc_ui-porto-panel" data-vc-panel=".vc_ui-panel-header-header" data-vc-ui-element="panel-porto-edit-area-size" id="vc_ui-panel-porto-edit-area-size">
	<div class="vc_ui-panel-window-inner">
		<?php
		vc_include_template(
			'editors/popups/vc_ui-header.tpl.php',
			array(
				'title'            => esc_html__( 'porto Edit Area Size', 'porto' ),
				'controls'         => array( 'minimize', 'close' ),
				'header_css_class' => 'vc_ui-porto-edit-area-size-header-container',
				'content_template' => '',
			)
		);
		?>
		<div class="vc_ui-panel-content-container">
			<div class="vc_ui-panel-content vc_properties-list vc_edit_form_elements" data-vc-ui-element="panel-content">
				<div class="vc_row">
					<div class="vc_col-xs-12 vc_column">
						<div class="wpb_element_label"><?php esc_html_e( 'Edit Area Size', 'porto' ); ?></div>
						<div class="edit_form_line">
							<input name="edit_area_width" class="wpb-textinput" type="text" value="<?php echo esc_attr( $edit_area_width ); ?>" id="vc_edit-area-width-field" placeholder="<?php esc_attr_e( 'Input custom edit area width with unit.', 'porto' ); ?>">
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- param window footer-->
		<?php
		vc_include_template(
			'editors/popups/vc_ui-footer.tpl.php',
			array(
				'controls' => array(
					array(
						'name'        => 'save',
						'label'       => esc_html__( 'Save changes', 'js_composer' ),
						'css_classes' => 'vc_ui-button-fw',
						'style'       => 'action',
					),
				),
			)
		);
		?>
	</div>
</div>
