<?php
/**
 * Ultimate Tab Backend
 *
 *  @package Ultimate Tab Backend
 */

?>
<div class="vc_controls<?php echo ! empty( $extended_css ) ? ' ' . esc_attr( $extended_css ) : ''; ?>">
	<div class="vc_controls-<?php echo $position; // @codingStandardsIgnoreStart ?>">
		<a class="vc_element-name">
				<span class="vc_btn-content"><?php echo __( $name, 'js_composer' ); ?></span>
		</a>
		<?php foreach ( $controls as $control ) : ?>
			<?php if ( 'add' === $control ) : ?>
			<a class="vc_control-btn vc_control-btn-prepend vc_edit" href="#"
			title="<?php printf( __( 'Prepend to %s', 'js_composer' ), $name ); ?>"><span
			class="vc_btn-content"><span class="icon"></span></span></a>
			<?php elseif ( 'edit' === $control ) : ?>
			<a class="vc_control-btn vc_control-btn-edit" href="#"
			title="<?php printf( __( 'Edit %s', 'js_composer' ), $name ); ?>"><span
			class="vc_btn-content"><span class="icon"></span></span></a>
			<?php elseif ( 'clone' === $control ) : ?>
			<a class="vc_control-btn vc_control-btn-clone" href="#"
			title="<?php printf( __( 'Clone %s', 'js_composer' ), $name ); ?>"><span
			class="vc_btn-content"><span class="icon"></span></span></a>
			<?php elseif ( 'delete' === $control ) : ?>
			<a class="vc_control-btn vc_control-btn-delete" href="#"
			title="<?php printf( __( 'Delete %s', 'js_composer' ), $name ); // @codingStandardsIgnoreEnd ?>"><span
			class="vc_btn-content"><span class="icon"></span></span></a>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
</div>
