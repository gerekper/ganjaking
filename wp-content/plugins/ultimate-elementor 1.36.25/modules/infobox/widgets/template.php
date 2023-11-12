<?php
/**
 * UAEL Info Box Module Template.
 *
 * @package UAEL
 */

use UltimateElementor\Classes\UAEL_Helper;
?>
<?php
$dynamic_settings = $this->get_settings_for_display();

$this->add_render_attribute( 'classname', 'class', 'uael-module-content uael-infobox' );

if ( 'icon' === $settings['uael_infobox_image_type'] || 'photo' === $settings['uael_infobox_image_type'] ) {

	$this->add_render_attribute( 'classname', 'class', 'uael-imgicon-style-' . $settings['infobox_imgicon_style'] );

	if ( 'above-title' === $settings['infobox_image_position'] || 'below-title' === $settings['infobox_image_position'] ) {
		$this->add_render_attribute( 'classname', 'class', ' uael-infobox-' . $settings['infobox_align'] );
	}
	if ( 'left-title' === $settings['infobox_image_position'] || 'left' === $settings['infobox_image_position'] ) {
		$this->add_render_attribute( 'classname', 'class', ' uael-infobox-left' );
	}
	if ( 'right-title' === $settings['infobox_image_position'] || 'right' === $settings['infobox_image_position'] ) {
		$this->add_render_attribute( 'classname', 'class', ' uael-infobox-right' );
	}
	if ( 'icon' === $settings['uael_infobox_image_type'] ) {
		$this->add_render_attribute( 'classname', 'class', ' infobox-has-icon uael-infobox-icon-' . $settings['infobox_image_position'] );
	}
	if ( 'photo' === $settings['uael_infobox_image_type'] ) {
		$this->add_render_attribute( 'classname', 'class', ' infobox-has-photo uael-infobox-photo-' . $settings['infobox_image_position'] );
	}
	if ( 'above-title' !== $settings['infobox_image_position'] && 'below-title' !== $settings['infobox_image_position'] ) {

		if ( 'middle' === $settings['infobox_image_valign'] ) {
			$this->add_render_attribute( 'classname', 'class', ' uael-infobox-image-valign-middle' );
		} else {
			$this->add_render_attribute( 'classname', 'class', ' uael-infobox-image-valign-top' );
		}
	}
	if ( 'left' === $settings['infobox_image_position'] || 'right' === $settings['infobox_image_position'] ) {
		if ( 'tablet' === $settings['infobox_img_mob_view'] ) {
			$this->add_render_attribute( 'classname', 'class', ' uael-infobox-stacked-tablet' );
		}
		if ( 'mobile' === $settings['infobox_img_mob_view'] ) {
			$this->add_render_attribute( 'classname', 'class', ' uael-infobox-stacked-mobile' );
		}
	}
	if ( 'right' === $settings['infobox_image_position'] ) {
		if ( 'tablet' === $settings['infobox_img_mob_view'] ) {
			$this->add_render_attribute( 'classname', 'class', ' uael-reverse-order-tablet' );
		}
		if ( 'mobile' === $settings['infobox_img_mob_view'] ) {
			$this->add_render_attribute( 'classname', 'class', ' uael-reverse-order-mobile' );
		}
	}
} else {
	if ( 'left' === $settings['infobox_overall_align'] || 'center' === $settings['infobox_overall_align'] || 'right' === $settings['infobox_overall_align'] ) {
		$classname = 'uael-infobox-' . $settings['infobox_overall_align'];
		$this->add_render_attribute( 'classname', 'class', ' uael-infobox-' . $settings['infobox_overall_align'] );
	}
}

$this->add_render_attribute( 'classname', 'class', ' uael-infobox-link-type-' . $settings['infobox_cta_type'] );

?>

<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'classname' ) ); ?>>
	<div class="uael-infobox-left-right-wrap">
		<?php
		if ( 'module' === $settings['infobox_cta_type'] && '' !== $settings['infobox_text_link'] ) {
			if ( ! empty( $dynamic_settings['infobox_text_link']['url'] ) ) {
				$this->add_link_attributes( 'module_link', $dynamic_settings['infobox_text_link'] );
			}
			$this->add_render_attribute( 'module_link', 'class', 'uael-infobox-module-link' );
			?>
			<a <?php echo wp_kses_post( $this->get_render_attribute_string( 'module_link' ) ); ?>></a><?php //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>  
			<?php
		}
		?>
		<?php $this->render_image( 'left', $settings ); ?>
		<div class="uael-infobox-content">
			<?php $this->render_image( 'above-title', $settings ); ?>
			<?php $this->render_title( $settings ); ?>
			<?php
			if ( 'after_heading' === $settings['infobox_separator_position'] ) {
					$this->render_separator( $settings );
			}
			?>
			<?php $this->render_image( 'below-title', $settings ); ?>
			<div class="uael-infobox-text-wrap">
				<div class="uael-infobox-text elementor-inline-editing" data-elementor-setting-key="infobox_description" data-elementor-inline-editing-toolbar="advanced">
					<?php echo wp_kses_post( $this->get_settings_for_display( 'infobox_description' ) ); ?>
				</div>
				<?php
				if ( 'after_description' === $settings['infobox_separator_position'] ) {
					$this->render_separator( $settings );
					?>
				<?php } ?>	
				<?php $this->render_link( $settings ); ?>
			</div>
		</div>
		<?php $this->render_image( 'right', $settings ); ?>
	</div>
</div>
