<?php
global $porto_settings, $post, $porto_layout;

$portfolio_name = empty( $porto_settings['portfolio-name'] ) ? __( 'Portfolios', 'porto' ) : $porto_settings['portfolio-name'];

if ( ! empty( $porto_settings['portfolio-related'] ) ) :
	$related_portfolios = porto_get_related_portfolios( $post->ID );
	if ( $related_portfolios->have_posts() ) :
		$options                = array();
		$options['themeConfig'] = true;
		$portfolio_related_cols = isset( $porto_settings['portfolio-related-cols'] ) ? $porto_settings['portfolio-related-cols'] : 4;
		$options['lg']          = $portfolio_related_cols;
		if ( $options['lg'] < 2 ) {
			$options['lg'] = 2;
		}
		$options['md'] = $portfolio_related_cols - 1;
		if ( $options['md'] < 2 ) {
			$options['md'] = 2;
		}
		$options['sm'] = $portfolio_related_cols - 2;
		if ( $options['sm'] < 1 ) {
			$options['sm'] = 1;
		}

		$carousel_class  = 'portfolio-carousel porto-carousel owl-carousel show-nav-title has-ccols has-ccols-spacing ccols-1';
		$carousel_class .= ' ccols-lg-' . (int) $options['lg'];
		if ( $options['md'] > 1 ) {
			$carousel_class .= ' ccols-md-' . (int) $options['md'];
		}
		if ( $options['sm'] > 1 ) {
			$carousel_class .= ' ccols-sm-' . (int) $options['sm'];
		}

		$options = json_encode( $options );

		?>
		<div class="related-portfolios <?php echo esc_attr( isset( $porto_settings['portfolio-related-style'] ) ? $porto_settings['portfolio-related-style'] : '' ); ?>">
			<div class="container">
				<?php /* translators: %s: Portfolio name */ ?>
				<h4 class="sub-title"><?php printf( esc_html__( 'Related %s', 'porto' ), '<b>' . esc_html( $portfolio_name ) . '</b>' ); ?></h4>
				<div class="<?php echo esc_attr( $carousel_class ); ?>" data-plugin-options="<?php echo esc_attr( $options ); ?>">
				<?php
				while ( $related_portfolios->have_posts() ) {
					$related_portfolios->the_post();
					get_template_part( 'content', 'portfolio-item' );
				}
				?>
				</div>
			</div>
		</div>
		<?php
	endif;
endif;
