<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
/**
 * @var array $seo_settings
 * @var WP_Post | null $post
 * @var int $post_id
 * @var Vc_Post_Seo $vc_post_seo
 */
?>

<div id="vc_ui-seo-social">
	<?php
	foreach ( $vc_post_seo->get_social_network_list() as $network_slug => $network_name ) {
		$slug = 'social-image-' . $network_slug;
		$image_id = (int) empty( $seo_settings[ $slug ] ) ? 0 : $seo_settings[ $slug ];
		?>
		<div class="vc_row vc_seo-social-block">
			<div class="vc_col-sm-12 vc_column">
				<?php
				vc_include_template(
					'editors/popups/seo/seo-social-preview.php',
					[
						'seo_settings' => $seo_settings,
						'post' => $post,
						'post_id' => $post_id,
						'vc_post_seo' => $vc_post_seo,
						'network_slug' => $network_slug,
						'network_name' => $network_name,
						'image_id' => $image_id,
					]
				);
				?>
			</div>
			<div class="vc_col-sm-12 vc_column wpb_el_type_attach_image">
				<div class="wpb_element_label"><?php esc_html_e( 'Image', 'js_composer' ); ?></div>
				<div class="edit_form_line" data-social-net-preview-slug="<?php echo esc_attr( 'wpb-' . $network_slug . '-preview' ); ?>">
					<?php
					vc_include_template( 'params/attache_images/template.php', [
						'settings' => [
							'type' => 'attach_image',
							'heading' => 'Image',
							'param_name' => 'social-image-' . $network_slug,
							'value' => '',
							'description' => '',
							'dependency' => [
								'element' => 'source',
								'value' => 'media_library',
							],
							'admin_label' => true,
							'vc_single_param_edit_holder_class' => [],
						],
						'value' => $image_id,
						'tag' => 'vc_single_image',
						'single' => true,
						'param_value' => $image_id,
					] );
					?>
				</div>
				<span class="wpb-social-attr-description">Select image from media library</span>
			</div>
			<div class="vc_col-sm-12 vc_column">
				<div class="wpb_element_label"><?php esc_html_e( 'Social title', 'js_composer' ); ?></div>
				<div class="edit_form_line">
					<?php
					$title_name = 'social-title-' . $network_slug;
					$title_id = $title_name;
					$value = empty( $seo_settings[ $title_name ] ) ? '' : $seo_settings[ $title_name ];

					wpb_add_ai_icon_to_text_field( 'textfield', $title_id );
					?>
					<input name="<?php echo esc_attr( $title_name ); ?>" id="<?php echo esc_attr( $title_id ); ?>" class="wpb-textinput vc_social-title-field" type="text" value="<?php echo esc_attr( $value ); ?>" placeholder="">
				</div>
			</div>
			<div class="vc_col-sm-12 vc_column">
				<div class="wpb_element_label"><?php esc_html_e( 'Social description', 'js_composer' ); ?></div>
				<div class="edit_form_line">
					<?php
					$description_name = 'social-description-' . $network_slug;
					$description_id = $description_name;

					$value = empty( $seo_settings[ $description_name ] ) ? '' : $seo_settings[ $description_name ];
					$count = mb_strlen( $value ) ? mb_strlen( $value ) : 0;

					wpb_add_ai_icon_to_text_field( 'textarea', $description_id );
					?>
					<textarea name="<?php echo esc_attr( $description_name ); ?>" id="<?php echo esc_attr( $description_id ); ?>" class="wpb-textinput vc_social-description-field" maxlength="255"><?php echo esc_html( $value ); ?></textarea>
					<div class="wpb-social-attr-description"><span class="vc_social-description-counter"><?php echo esc_html( $count ) ?></span>/255</div>
				</div>
			</div>
		</div>
		<?php
	}
	?>
</div>
