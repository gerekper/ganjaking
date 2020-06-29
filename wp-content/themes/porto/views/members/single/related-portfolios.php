<?php
global $porto_settings, $porto_layout;

$portfolio_ids = get_post_meta( get_the_ID(), 'member_portfolios', true );
$portfolios    = porto_get_portfolios_by_ids( $portfolio_ids );

$options                = array();
$options['themeConfig'] = true;
$options['lg']          = $porto_settings['portfolio-related-cols'];
if ( in_array( $porto_layout, porto_options_sidebars() ) ) {
	$options['lg']--;
}
if ( $options['lg'] < 2 ) {
	$options['lg'] = 2;
}
$options['md'] = $porto_settings['portfolio-related-cols'] - 1;
if ( $options['md'] < 2 ) {
	$options['md'] = 2;
}
$options['sm'] = $porto_settings['portfolio-related-cols'] - 2;
if ( $options['sm'] < 1 ) {
	$options['sm'] = 1;
}
$options = json_encode( $options );

if ( $portfolios->have_posts() ) : ?>
	<div class="post-gap"></div>

	<div class="related-portfolios <?php echo esc_attr( $porto_settings['portfolio-related-style'] ); ?>">
		<?php /* translators: $1 and $2 opening and closing strong tags respectively */ ?>
		<h4 class="sub-title"><?php printf( esc_html__( 'My %1$sWork%2$s', 'porto' ), '<strong>', '</strong>' ); ?></h4>
		<div class="row">
			<div class="portfolio-carousel porto-carousel owl-carousel show-nav-title" data-plugin-options="<?php echo esc_attr( $options ); ?>">
				<?php
				while ( $portfolios->have_posts() ) {
					$portfolios->the_post();

					get_template_part( 'content', 'portfolio-item' );
				}
				?>
			</div>
		</div>
	</div>
<?php endif;
wp_reset_postdata();
?>
