<?php
global $porto_settings, $post, $porto_layout;

$options                = array();
$options['themeConfig'] = true;
$options['lg']          = $porto_settings['portfolio-related-cols'];
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

$portfolio_name = empty( $porto_settings['portfolio-name'] ) ? __( 'Portfolios', 'porto' ) : $porto_settings['portfolio-name'];

if ( $porto_settings['portfolio-related'] ) :
	$related_portfolios = porto_get_related_portfolios( $post->ID );
	if ( $related_portfolios->have_posts() ) : ?>
		<div class="related-portfolios <?php echo esc_attr( $porto_settings['portfolio-related-style'] ); ?>">
			<div class="container">
				<?php /* translators: %s: Portfolio name */ ?>
				<h4 class="sub-title"><?php printf( esc_html__( 'Related %s', 'porto' ), '<b>' . esc_html( $portfolio_name ) . '</b>' ); ?></h4>
				<div class="row">
					<div class="portfolio-carousel porto-carousel owl-carousel show-nav-title" data-plugin-options="<?php echo esc_attr( $options ); ?>">
					<?php
					while ( $related_portfolios->have_posts() ) {
						$related_portfolios->the_post();
						get_template_part( 'content', 'portfolio-item' );
					}
					?>
					</div>
				</div>
			</div>
		</div>
		<?php
	endif;
endif;
