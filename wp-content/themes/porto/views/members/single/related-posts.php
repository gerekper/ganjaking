<?php
global $porto_settings, $porto_layout;

$post_ids     = get_post_meta( get_the_ID(), 'member_posts', true );
$member_posts = porto_get_posts_by_ids( $post_ids );

$options                = array();
$options['themeConfig'] = true;
$options['lg']          = $porto_settings['post-related-cols'];
if ( in_array( $porto_layout, porto_options_sidebars() ) ) {
	$options['lg']--;
}
if ( $options['lg'] < 1 ) {
	$options['lg'] = 1;
}
$options['md'] = $porto_settings['post-related-cols'] - 1;
if ( $options['md'] < 1 ) {
	$options['md'] = 1;
}
$options['sm'] = $porto_settings['post-related-cols'] - 2;
if ( $options['sm'] < 1 ) {
	$options['sm'] = 1;
}
$options = json_encode( $options );

if ( $member_posts->have_posts() ) : ?>
	<div class="post-gap"></div>

	<div class="related-posts <?php echo esc_attr( $porto_settings['post-related-style'] ); ?>">
		<?php /* translators: $1 and $2 opening and closing strong tags respectively */ ?>
		<h4 class="sub-title"><?php printf( esc_html__( 'My %1$sPosts%2$s', 'porto' ), '<strong>', '</strong>' ); ?></h4>
		<div class="row">
			<div class="post-carousel porto-carousel owl-carousel show-nav-title" data-plugin-options="<?php echo esc_attr( $options ); ?>">
				<?php
				while ( $member_posts->have_posts() ) {
					$member_posts->the_post();

					get_template_part( 'content', 'post-item' );
				}
				?>
			</div>
		</div>
	</div>
<?php endif;
wp_reset_postdata();
?>
