<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
if ( function_exists( 'vc_is_inline' ) && vc_is_inline() ) {
	$edit_area_width = get_post_meta( ( isset( $_REQUEST['post_id'] ) ? $_REQUEST['post_id'] : $_REQUEST['vc_post_id'] ), 'porto_edit_area_width', true );
	if ( empty( $edit_area_width ) ) {
		$edit_area_width = '100%';
	}
}
$_post_types  = get_post_types(
	array(
		'public'            => true,
		'show_in_nav_menus' => true,
	),
	'objects'
);
$builder_type = '';
if ( PortoBuilders::BUILDER_SLUG == get_post_type() ) {
	$post_types   = array();
	$builder_type = get_post_meta( get_post()->ID, PortoBuilders::BUILDER_TAXONOMY_SLUG, true );
	if ( 'single' == $builder_type ) {
		$post_prefix = 'Single';
		$post_suffix = '';
	} elseif ( 'archive' == $builder_type ) {
		$post_prefix = '';
		$post_suffix = 'Archive';
	}
	if ( 'single' == $builder_type || 'archive' == $builder_type ) {
		foreach ( $_post_types as $post_type => $object ) {
			if ( ! in_array( $post_type, array( 'page', 'product', 'e-landing-page' ) ) ) {
				$post_types[ $post_type ] = sprintf( esc_html__( '%1$s %2$s %3$s', 'porto-functionality' ), $post_prefix, $object->labels->singular_name, $post_suffix );
			}
		}
		$builder_cls = call_user_func( array( 'PortoBuilders' . ucfirst( $builder_type ), 'get_instance' ) );
		$builder_cls->find_preview();
		$cur_post    = ! empty( $builder_cls->edit_post_type ) ? $builder_cls->edit_post_type : 'post';
	}
}
if ( ! get_post_type() ) {
	return;
}
if ( ( ! function_exists( 'vc_is_inline' ) || ! vc_is_inline() ) && 'single' != $builder_type && 'archive' != $builder_type ) {
	return;
}
?>

<div class="vc_ui-font-open-sans vc_ui-panel-window vc_media-xs vc_ui-panel vc_ui-porto-panel" data-vc-panel=".vc_ui-panel-header-header" data-vc-ui-element="panel-porto-edit-area-size" id="vc_ui-panel-porto-edit-area-size" data-builder-type="<?php echo esc_attr( $builder_type ); ?>">
	<div class="vc_ui-panel-window-inner">
		<?php
		vc_include_template(
			'editors/popups/vc_ui-header.tpl.php',
			array(
				'title'            => esc_html__( 'Porto Preview Settings', 'porto-functionality' ),
				'controls'         => array( 'minimize', 'close' ),
				'header_css_class' => 'vc_ui-porto-edit-area-size-header-container',
				'content_template' => '',
			)
		);
		?>
		<div class="vc_ui-panel-content-container">
			<div class="vc_ui-panel-content vc_properties-list vc_edit_form_elements" data-vc-ui-element="panel-content">
				<div class="vc_row">
				<?php
				if ( function_exists( 'vc_is_inline' ) && vc_is_inline() ) {
					?>
					<div class="vc_col-xs-12 vc_column">
						<div class="wpb_element_label"><?php esc_html_e( 'Edit Area Size', 'porto-functionality' ); ?></div>
						<div class="edit_form_line">
							<input name="edit_area_width" class="wpb-textinput" type="text" value="<?php echo esc_attr( $edit_area_width ); ?>" id="vc_edit-area-width-field" placeholder="<?php esc_attr_e( 'Input custom edit area width with unit.', 'porto-functionality' ); ?>">
						</div>
					</div>
					<?php
				}
				?>
					<?php if ( 'single' == $builder_type || 'archive' == $builder_type ) { ?>
					<div class="vc_col-xs-12 vc_column">
						<div class="wpb_element_label"><?php esc_html_e( 'Edit Post Type', 'porto-functionality' ); ?></div>
						<div class="edit_form_line">
						<select class="builder_preview_type" val="<?php echo esc_attr( $cur_post ); ?>">
						<?php foreach ( $post_types as $type => $val ) : ?>
							<option value="<?php echo esc_attr( $type ); ?>" <?php echo ( $type === $cur_post ) ? 'selected' : ''; ?>><?php echo esc_html( $val ); ?></option>
						<?php endforeach; ?>
						</select>
						</div>
					</div>
					<?php } ?>
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
						'name'        => 'update',
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
