<?php get_header(); ?>

<?php
$builder_id = porto_check_builder_condition( 'single' );
if ( $builder_id && 'publish' == get_post_status( $builder_id ) ) {
	echo do_shortcode( '[porto_block id="' . esc_attr( $builder_id ) . '"]' );
} else {
	wp_reset_postdata();

	global $porto_settings, $porto_layout;

	$member_name = ! empty( $porto_settings['member-name'] ) ? $porto_settings['member-name'] : __( 'Members', 'porto' );
	?>
	<div id="content" role="main" class="porto-single-page">

		<?php
		if ( have_posts() ) :
			the_post();
			global $post;
			?>

			<?php get_template_part( 'content', 'member' ); ?>

			<?php
			if ( 'widewidth' === $porto_layout ) {
				echo '<div class="container m-b-xl">';}
			?>

			<?php porto_get_template_part( 'views/members/single/related', 'portfolios' ); ?>

			<?php
			if ( class_exists( 'WooCommerce' ) ) {
				porto_get_template_part( 'views/members/single/related', 'products' );}
			?>

			<?php porto_get_template_part( 'views/members/single/related', 'posts' ); ?>

			<?php
			if ( ! empty( $porto_settings['member-related'] ) ) :
				$related_members = porto_get_related_members( $post->ID );
				if ( $related_members->have_posts() ) :
					$options                = array();
					$options['themeConfig'] = true;
					$member_related_cols    = isset( $porto_settings['member-related-cols'] ) ? $porto_settings['member-related-cols'] : 4;
					$options['lg']          = $member_related_cols;

					if ( in_array( $porto_layout, porto_options_sidebars() ) ) {
						$options['lg']--;
					}
					if ( $options['lg'] < 2 ) {
						$options['lg'] = 2;
					}
					$options['md'] = $member_related_cols - 1;

					if ( $options['md'] < 2 ) {
						$options['md'] = 2;
					}
					$options['sm'] = $member_related_cols - 2;
					if ( $options['sm'] < 1 ) {
						$options['sm'] = 1;
					}
					$options['margin'] = (int) $porto_settings['grid-gutter-width'];

					$carousel_class  = 'member-carousel porto-carousel owl-carousel show-nav-title has-ccols has-ccols-spacing ccols-1';
					$carousel_class .= ' ccols-lg-' . (int) $options['lg'];
					if ( $options['md'] > 1 ) {
						$carousel_class .= ' ccols-md-' . (int) $options['md'];
					}
					if ( $options['sm'] > 1 ) {
						$carousel_class .= ' ccols-sm-' . (int) $options['sm'];
					}

					$options     = json_encode( $options );
					?>
					<div class="related-members">
						<?php /* translators: %s: Member name */ ?>
						<h4 class="sub-title"><?php printf( porto_strip_script_tags( __( 'Related <strong>%s</strong>', 'porto' ) ), esc_html( $member_name ) ); ?></h4>
						<div class="<?php echo esc_attr( $carousel_class ); ?>" data-plugin-options="<?php echo esc_attr( $options ); ?>">
						<?php
						while ( $related_members->have_posts() ) {
							$related_members->the_post();
							porto_get_template_part( 'content', 'member-item' );
						}
						?>
						</div>
					</div>
					<?php
				endif;
			endif;
			if ( 'widewidth' === $porto_layout ) {
				echo '</div>';
			}
		endif;
		?>
	</div>
	<?php
}
get_footer(); ?>
