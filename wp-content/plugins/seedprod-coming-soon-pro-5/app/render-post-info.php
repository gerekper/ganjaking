<?php
/**
 * Post Info Template Tag Routes & Preview Rendering.
 */

/**
 * Render Post Info Template Tag Code.
 *
 * @return void
 */
function seedprod_pro_render_postinfo_templatetags() {
	if ( check_ajax_referer( 'seedprod_nonce' ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_builder_preview_render_capability', 'edit_others_posts' ) ) ) {
			wp_send_json_error();
		}

		$items      = isset( $_POST['items'] ) ? wp_unslash( filter_input( INPUT_POST, 'items', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY ) ) : array();
		$divider    = isset( $_POST['divider'] ) ? sanitize_text_field( wp_unslash( $_POST['divider'] ) ) : '';
		$show_icons = isset( $_POST['showIcons'] ) ? filter_var( wp_unslash( $_POST['showIcons'] ), FILTER_VALIDATE_BOOLEAN ) : true;

		$args = array(
			'posts_per_page' => 1,
			'post_type'      => 'post',
		);

		$the_query = new WP_Query( $args );

		if ( $the_query->have_posts() ) {
			while ( $the_query->have_posts() ) {
				$the_query->the_post();

				$items_count = count( $items );

				$render     = '<ul class="sp-postinfo-list-items">';
				$items_html = array();

				for ( $i = 0; $i < $items_count; $i++ ) {
					$items_html[] = render_item( $i, $show_icons );
				}

				$render .= implode( '<span class="sp-postitem-list-items-divider">' . esc_html( $divider ) . '</span>', $items_html );
				$render .= '</ul>';
			}
		}

		/* Restore original Post Data */
		wp_reset_postdata();

		echo wp_kses( $render, 'post' );
		exit;
	}
	exit;
}

/**
 * Render single Post Info item.
 *
 * @param integer $item_number Item number.
 * @param boolean $show_icons  Show icons or not.
 * @return string $html        Post meta item HTML.
 */
function render_item( $item_number, $show_icons = true ) {
	if ( check_ajax_referer( 'seedprod_nonce' ) ) {
		// Check type & get html via shortcode.
		$html  = '';
		$html .= '<li class="sp-postinfo-item">';

		$item_type = isset( $_POST['items'][ $item_number ]['type'] ) ? sanitize_key( wp_unslash( $_POST['items'][ $item_number ]['type'] ) ) : '';

		switch ( $item_type ) {
			case 'author':
				// Get avatar.
				$avatar = isset( $_POST['items'][ $item_number ]['avatar'] ) ? sanitize_key( wp_unslash( $_POST['items'][ $item_number ]['avatar'] ) ) : '';
				if ( 'true' === $avatar && true === $show_icons ) {
					$html .= '<span class="sp-postinfo-author-avatar">';
					$html .= do_shortcode( '[seedprod tag="get_avatar" echo="true"]' );
					$html .= '</span>';
				}

				// Get author name.
				$html .= '<span class="sp-postinfo-author-name">';
				$html .= do_shortcode( '[seedprod tag="the_author_meta(display_name)"]' );
				$html .= '</span>';

				break;

			case 'modified_date':
			case 'date':
				// Format Options Array.
				$custom_date_format = isset( $_POST['items'][ $item_number ]['custom_date_format'] ) ? sanitize_text_field( wp_unslash( $_POST['items'][ $item_number ]['custom_date_format'] ) ) : '';

				$format_options = array(
					'default' => 'F j, Y',
					'0'       => 'F j, Y',
					'1'       => 'Y-m-d',
					'2'       => 'm/d/Y',
					'3'       => 'd/m/Y',
					'custom'  => $custom_date_format,
				);

				// Get icon.
				$icon_type  = isset( $_POST['items'][ $item_number ]['iconType'] ) ? sanitize_key( wp_unslash( $_POST['items'][ $item_number ]['iconType'] ) ) : '';
				$icon_value = isset( $_POST['items'][ $item_number ]['icon'] ) ? sanitize_text_field( wp_unslash( $_POST['items'][ $item_number ]['icon'] ) ) : '';

				if ( 'none' !== $icon_type && true === $show_icons ) {
					$icon  = 'default' === $icon_type ? 'fas fa-calendar' : $icon_value;
					$html .= '<span class="sp-postinfo-item-icon">';
					$html .= '<i class="' . esc_attr( $icon ) . '"></i>';
					$html .= '</span>';
				}

				// Get date.
				$tag = 'the_date';

				if ( 'modified_date' === $item_type ) {
					$tag = 'the_modified_date';
				}

				$date_format = isset( $_POST['items'][ $item_number ]['date_format'] ) ? sanitize_text_field( wp_unslash( $_POST['items'][ $item_number ]['date_format'] ) ) : '';

				$html .= '<span class="sp-postinfo-date-text">';
				$html .= do_shortcode( '[seedprod tag="' . $tag . '(' . $format_options[ $date_format ] . ')" echo="true"]' );
				$html .= '</span>';

				break;

			case 'modified_time':
			case 'time':
				// Format Options Array.
				$custom_time_format = isset( $_POST['items'][ $item_number ]['custom_time_format'] ) ? sanitize_text_field( wp_unslash( $_POST['items'][ $item_number ]['custom_time_format'] ) ) : '';

				$format_options = array(
					'default' => 'g:i a',
					'0'       => 'g:i a',
					'1'       => 'g:i A',
					'2'       => 'H:i',
					'custom'  => $custom_time_format,
				);

				// Get icon.
				$icon_type  = isset( $_POST['items'][ $item_number ]['iconType'] ) ? sanitize_key( wp_unslash( $_POST['items'][ $item_number ]['iconType'] ) ) : '';
				$icon_value = isset( $_POST['items'][ $item_number ]['icon'] ) ? sanitize_text_field( wp_unslash( $_POST['items'][ $item_number ]['icon'] ) ) : '';

				if ( 'none' !== $icon_type && true === $show_icons ) {
					$icon  = 'default' === $icon_type ? 'fas fa-clock' : $icon_value;
					$html .= '<span class="sp-postinfo-item-icon">';
					$html .= '<i class="' . esc_attr( $icon ) . '"></i>';
					$html .= '</span>';
				}

				// Get time.
				$tag = 'the_time';

				if ( 'modified_time' === $item_type ) {
					$tag = 'the_modified_time';
				}

				$time_format = isset( $_POST['items'][ $item_number ]['time_format'] ) ? sanitize_text_field( wp_unslash( $_POST['items'][ $item_number ]['time_format'] ) ) : '';

				$html .= '<span class="sp-postinfo-time-text">';
				$html .= do_shortcode( '[seedprod tag="' . $tag . '(' . $format_options[ $time_format ] . ')" echo="true"]' );
				$html .= '</span>';

				break;

			case 'comments':
				// Get icon.
				$icon_type  = isset( $_POST['items'][ $item_number ]['iconType'] ) ? sanitize_key( wp_unslash( $_POST['items'][ $item_number ]['iconType'] ) ) : '';
				$icon_value = isset( $_POST['items'][ $item_number ]['icon'] ) ? sanitize_text_field( wp_unslash( $_POST['items'][ $item_number ]['icon'] ) ) : '';

				if ( 'none' !== $icon_type && true === $show_icons ) {
					$icon  = 'default' === $icon_type ? 'fas fa-comment' : $icon_value;
					$html .= '<span class="sp-postinfo-item-icon">';
					$html .= '<i class="' . esc_attr( $icon ) . '"></i>';
					$html .= '</span>';
				}

				// Get comments count.
				$html .= '<span class="sp-postinfo-comments-text">';

				$comments_number = (int) do_shortcode( '[seedprod tag="get_comments_number" echo="true"]' );
				$comments        = sprintf( '%d Comment(s)', $comments_number );

				$html .= $comments;
				$html .= '</span>';

				break;

			case 'terms':
				// Get icon.
				$icon_type  = isset( $_POST['items'][ $item_number ]['iconType'] ) ? sanitize_key( wp_unslash( $_POST['items'][ $item_number ]['iconType'] ) ) : '';
				$icon_value = isset( $_POST['items'][ $item_number ]['icon'] ) ? sanitize_text_field( wp_unslash( $_POST['items'][ $item_number ]['icon'] ) ) : '';

				if ( 'none' !== $icon_type ) {
					$icon  = 'default' === $icon_type ? 'fas fa-tags' : $icon_value;
					$html .= '<span class="sp-postinfo-item-icon">';
					$html .= '<i class="' . esc_attr( $icon ) . '"></i>';
					$html .= '</span>';
				}

				// Get terms.
				$tag = '[seedprod tag="the_category" echo="true"]';

				$terms_taxonomy = isset( $_POST['items'][ $item_number ]['terms_taxonomy'] ) ? sanitize_key( wp_unslash( $_POST['items'][ $item_number ]['terms_taxonomy'] ) ) : '';

				if ( 'post_tag' === $terms_taxonomy ) {
					$tag = '[seedprod tag="the_tags" echo="true"]';
				}

				$html .= '<span class="sp-postinfo-term-name">';
				$html .= do_shortcode( $tag );
				$html .= '</span>';

				break;

			default:
				break;
		}

		$html .= '</li>';
		return $html;
	}
}

if ( defined( 'DOING_AJAX' ) ) {
	
	add_action( 'wp_ajax_seedprod_pro_render_postinfo_templatetags', 'seedprod_pro_render_postinfo_templatetags' );
	
}
