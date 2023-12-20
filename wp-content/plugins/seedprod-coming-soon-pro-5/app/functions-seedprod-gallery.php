<?php



/**
 * Render Basic Gallery Shorcode
 */
function seedprod_pro_render_basic_gallery_shortcode() {

	if ( check_ajax_referer( 'seedprod_nonce' ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_builder_preview_render_capability', 'edit_others_posts' ) ) ) {
			wp_send_json_error();
		}

		$posted_data = $_POST;
		$items       = $posted_data;

		$galleries_data   = $items['items'];
		$gallery_settings = $items['settings'];
		$images_id_array  = $galleries_data[0]['imagesid'];

		if ( count( $images_id_array ) > 0 && is_array( $images_id_array ) ) {
			$ids = implode( ',', $images_id_array );
		}

		$link     = isset( $gallery_settings['gallery_link'] ) ? sanitize_text_field( wp_unslash( $gallery_settings['gallery_link'] ) ) : 'none';
		$cols     = isset( $gallery_settings['columns'] ) ? sanitize_text_field( wp_unslash( $gallery_settings['columns'] ) ) : '';
		$order_by = isset( $gallery_settings['order_by'] ) ? sanitize_text_field( wp_unslash( $gallery_settings['order_by'] ) ) : '';
		$size     = isset( $gallery_settings['image_size'] ) ? sanitize_text_field( wp_unslash( $gallery_settings['image_size'] ) ) : 'medium';

		echo do_shortcode( "[gallery ids='$ids' size='$size' columns='$cols' link='$link' orderby='$order_by' ]" );

		exit;
	}
}

/**
 * Get SeedProd Gallery Data
 */
function seedprod_pro_render_gallery_shortcode() {

	if ( check_ajax_referer( 'seedprod_nonce' ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_builder_preview_render_capability', 'edit_others_posts' ) ) ) {
			wp_send_json_error();
		}

		$posted_data = $_POST;
		$items       = $posted_data;

		$galleries = array();

		$galleries_data   = $items['items'];
		$gallery_settings = $items['settings'];

		$gallery_link_type = ! empty( sanitize_text_field( wp_unslash( $gallery_settings['gallery_link'] ) ) ) ? 'a' : 'div';
		$thumbnail_size    = sanitize_text_field( wp_unslash( $gallery_settings['image_size'] ) );

		$title_exists       = ! empty( sanitize_text_field( wp_unslash( $gallery_settings['overlay_title'] ) ) );
		$description_exists = ! empty( sanitize_text_field( wp_unslash( $gallery_settings['overlay_desc'] ) ) );

		$default_ratio_per = '75';
		if ( $gallery_settings['aspect_ratio'] ) {
			$image_ratio_array = explode( ':', sanitize_text_field( wp_unslash( $gallery_settings['aspect_ratio'] ) ) );
			$default_ratio_per = ( $image_ratio_array[1] / $image_ratio_array[0] ) * 100;
		}

		foreach ( $galleries_data as $k => $gallery_val ) {
			$gallery_index = $gallery_val['imagesid'];
			foreach ( $gallery_index as $t => $value ) {

				$gallerydata_val        = array();
				$gallerydata_val['id']  = $value;
				$gallerydata_val['url'] = sanitize_text_field( wp_unslash( $gallery_val['imgdata'][ $t ] ) );

				$galleries[ $k ][] = $gallerydata_val;
			}
		}

		$gallery_items = array();
		foreach ( $galleries as $gallery_index => $gallery ) {
			foreach ( $gallery as $index => $item ) {
				if ( in_array( $item['id'], array_keys( $gallery_items ), true ) ) {
					$gallery_items[ $item['id'] ][] = $gallery_index;
				} else {
					$gallery_items[ $item['id'] ][] = $gallery_index;
				}
			}
		}

		if ( 'multiple' === sanitize_text_field( wp_unslash( $gallery_settings['gallery_type'] ) ) ) {
			if ( count( $galleries_data ) > 1 ) {
				?>
				<div class="sp-gallery-tabs">
					<a data-gallery-index="all" class="sp-gallery-tab-title sp-tab-active"><?php echo esc_html( __( 'All', 'seedprod-pro' ) ); ?></a>
					<?php
					foreach ( $galleries as $index => $gallery ) :
						?>
						<a data-gallery-index="<?php echo esc_attr( $index ); ?>" class="sp-gallery-tab-title"><?php echo esc_html( $galleries_data[ $index ]['name'] ); ?></a>
						<?php
					endforeach;
					?>
				</div>
				<?php
			}
		}

		if ( ! empty( $galleries ) ) {
			?>
		<div class="sp-grid sp-custom-grid sp-gallery-block">
			<?php
			foreach ( $gallery_items as $id => $tags ) :
				$unique_index = $id; // $gallery_index . '_' . $index;
				$image_src    = wp_get_attachment_image_src( $id, $thumbnail_size );
				if ( ! $image_src ) {
					continue;
				}
				$attachment = get_post( $id );
				$image_data = array(
					'alt'         => get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true ),
					'media'       => wp_get_attachment_image_src( $id, 'full' )['0'],
					'src'         => $image_src['0'],
					'width'       => $image_src['1'],
					'height'      => $image_src['2'],
					'caption'     => $attachment->post_excerpt,
					'description' => $attachment->post_content,
					'title'       => $attachment->post_title,
				);

				$gallery_link_styles = ' class="sp-gallery-items" ';
				if ( 'a' === $gallery_link_type ) {

					if ( 'media' === $gallery_settings['gallery_link'] ) {
						$href                 = $image_data['media'];
						$gallery_link_styles .= sprintf( 'href="%1$s"', esc_attr( $href ) );

					} elseif ( 'custom' === $gallery_settings['gallery_link'] ) {

						$href                 = $gallery_settings['url'];
						$gallery_link_styles .= sprintf( 'href="%1$s"', esc_attr( $href ) );
					}
				}

				$gallery_link_tags = "data-tags='all," . esc_attr( implode( ',', $tags ) ) . "'";

				?>
				
				<?php
				echo '<';
				echo esc_html( $gallery_link_type );
				echo wp_kses( $gallery_link_styles, 'post' );
				?>
				 <?php
					echo wp_kses( $gallery_link_tags, 'post' );
					echo '>';
					?>
					<div class="sp-gallery-item-img" style="background-image:url('<?php echo esc_attr( $image_data['src'] ); ?>');"></div>
					<?php if ( ! empty( $gallery_settings['overlay_background'] ) ) : ?>
						<?php if ( 'true' == $gallery_settings['overlay_background'] ) : ?>
					<div class="sp-gallery-bg-overlay"></div>
					<?php endif; ?>
					<?php endif; ?>
					
					<?php if ( $title_exists || $description_exists ) : ?>
						<div class="sp-gallery-item-block">
								<?php
								if ( $title_exists ) :
										$title = $image_data[ $gallery_settings['overlay_title'] ];
									if ( ! empty( $title ) ) :
										?>
										<div class="sp-gallery-overlay-title">
												<?php echo esc_html( $title ); ?>
										</div>
										<?php
									endif;
								endif;

								if ( $description_exists ) :
									$description = $image_data[ $gallery_settings['overlay_desc'] ];

									if ( ! empty( $description ) ) :
										?>
										<div class="sp-gallery-overlay-desc">
													<?php echo esc_html( $description ); ?>
										</div>
										<?php
									endif;
								endif;
								?>
						</div>
					<?php endif; ?>
					
				</<?php echo esc_html( $gallery_link_type ); ?>>
				<?php

			 endforeach;
		}
		exit;

	}
}



