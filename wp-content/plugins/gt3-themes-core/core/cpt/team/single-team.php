<?php
$layout  = gt3_option( 'team_single_sidebar_layout' );
$sidebar = gt3_option( 'team_single_sidebar_def' );

if ( class_exists( 'RWMB_Loader' ) ) {
	$mb_layout = rwmb_meta( 'mb_page_sidebar_layout' );
	if ( ! empty( $mb_layout ) && $mb_layout != 'default' ) {
		$layout  = $mb_layout;
		$sidebar = rwmb_meta( 'mb_page_sidebar_def' );
	}
}
$column = 12;
if ( $layout == 'left' || $layout == 'right' ) {
	$column = apply_filters( 'gt3_column_width', 9 );
} else {
	$sidebar = '';
}

$row_class = ' sidebar_' . $layout;
get_header();
?>
    <div class="container gt3_team_single">
        <div class="row<?php echo esc_attr( $row_class ); ?>">
            <div class="content-container span<?php echo (int) $column; ?>">
                <section id='main_content'>
					<?php
					while ( have_posts() ):
						the_post();
						echo "<div class='gt3_single_team_header'>";
						if ( get_post_thumbnail_id( get_the_id() ) ) {
							$post_img_url = wp_get_attachment_url( get_post_thumbnail_id( get_the_id() ) );
							if ( function_exists( 'gt3_get_image_srcset' ) ) {
								$responsive_dimensions  = apply_filters( 'gt3/core/cpt/team/single-team/responsive_dimensions', array(
									array( '1200', '470' ),
									array( '992', '470' ),
									array( '768', '470' )
								));
								$gt3_featured_image_url = gt3_get_image_srcset( $post_img_url, null, $responsive_dimensions );
							} else {
								$gt3_featured_image_url = 'src="' . esc_attr( aq_resize( $post_img_url, "1170", "", true, true, true ) ) . '"';
							}
							echo '<div class="gt3_single_team_thumbnail__wrapper"><img ' . $gt3_featured_image_url . ' class="gt3_single_team_thumbnail" alt=""></div>';
						}

						$page_title_conditional = ( ( gt3_option( 'page_title_conditional' ) == '1' || gt3_option( 'page_title_conditional' ) == true ) && ( gt3_option( 'team_title_conditional' ) == '1' || gt3_option( 'team_title_conditional' ) == true ) ) ? 'yes' : 'no';

						if ( class_exists( 'RWMB_Loader' ) && get_queried_object_id() !== 0 ) {
							$mb_page_title_conditional = rwmb_meta( 'mb_page_title_conditional' );
							if ( $mb_page_title_conditional == 'yes' ) {
								$page_title_conditional = 'yes';
							} elseif ( $mb_page_title_conditional == 'no' ) {
								$page_title_conditional = 'no';
							}
							$team_info     = rwmb_meta( 'social_url' );
							$team_info_out = '';
							if ( ! empty( $team_info ) && is_array( $team_info ) ) {
								foreach ( $team_info as $team_info_item ) {
									$team_info_out .= '<div class="gt3_single_team_info__item">';
									$team_info_out .= ! empty( $team_info_item['name'] ) ? '<h4>' . esc_html( $team_info_item['name'] ) . '</h4>' : '';
									$team_info_out .= ! empty( $team_info_item['address'] ) ? '<a href="' . esc_url( $team_info_item['address'] ) . '" target="_blank">' : '';
									$team_info_out .= ! empty( $team_info_item['description'] ) ? '<span>' . $team_info_item['description'] . '</span>' : '';
									$team_info_out .= ! empty( $team_info_item['address'] ) ? '</a>' : '';
									$team_info_out .= '</div>';
								}
							}
							$team_info_socials    = rwmb_meta( 'icon_selection' );
							$team_info_social_out = '';
							if ( ! empty( $team_info_socials ) && is_array( $team_info_socials ) ) {
								foreach ( $team_info_socials as $team_info_social ) {
									$team_info_social_out .= '<div class="gt3_single_team_socials__item"' . ( ! empty( $team_info_social['color'] ) ? ' style="color:' . $team_info_social['color'] . ';"' : '' ) . '>';
									$team_info_social_out .= ! empty( $team_info_social['input'] ) ? '<a href="' . $team_info_social['input'] . '" target="_blank">' : '';
									$team_info_social_out .= ! empty( $team_info_social['text'] ) ? '<span>' . $team_info_social['text'] . '</span>' : ( ! empty( $team_info_social['select'] ) ? '<i class="' . $team_info_social['select'] . '"></i>' : '' );
									$team_info_social_out .= ! empty( $team_info_social['input'] ) ? '</a>' : '';
									$team_info_social_out .= '</div>';
								}
							}
						}

						echo "<div class='gt3_single_team_info'>";
						if ( $page_title_conditional != 'yes' ) {
							?>
                            <div class="gt3_team_title"><?php
							echo '<h2>' . get_the_title() . '</h2>';
							?></div><?php
						}
						if ( ! empty( $team_info_out ) ) {
							echo '<div class="gt3_single_team_info">' . $team_info_out . '</div>';
						}
						if ( ! empty( $team_info_social_out ) ) {
							echo '<div class="gt3_single_team_socials">' . $team_info_social_out . '</div>';
						}

						echo "</div>";
						echo "</div>";


					endwhile;
					the_content( esc_html__( 'Read more!', 'qudos' ) );
					wp_reset_postdata();
					?>
                </section>
            </div>
			<?php
			if ( $layout == 'left' || $layout == 'right' ) {
				echo '<div class="sidebar-container span' . ( 12 - (int) $column ) . '">';
				if ( is_active_sidebar( $sidebar ) ) {
					echo "<aside class='sidebar'>";
					dynamic_sidebar( $sidebar );
					echo "</aside>";
				}
				echo "</div>";
			}
			?>
        </div>
    </div>

<?php
get_footer();
