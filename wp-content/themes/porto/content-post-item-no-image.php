<?php
global $porto_settings, $porto_post_view, $porto_post_btn_style, $porto_post_btn_size, $porto_post_btn_color, $porto_post_image_size, $porto_post_author, $porto_post_excerpt_length;

$post_style     = $porto_post_view ? $porto_post_view : ( isset( $porto_settings['post-related-style'] ) ? $porto_settings['post-related-style'] : '' );
$post_author    = $porto_post_author ? ( 'show' == $porto_post_author ? true : false ) : ( isset( $porto_settings['post-related-author'] ) ? $porto_settings['post-related-author'] : false );
$excerpt_length = isset( $porto_settings['post-related-excerpt-length'] ) ? $porto_settings['post-related-excerpt-length'] : '20';
if ( $porto_post_excerpt_length ) {
	$excerpt_length = (int) $porto_post_excerpt_length;
}
$show_date = isset( $porto_settings['post-metas'] ) && in_array( 'date', $porto_settings['post-metas'] );

if ( $post_style && 'style-3' == $post_style ) {
	?>
	<div class="post-item with-btn<?php echo isset( $porto_settings['post-title-style'] ) && 'without-icon' == $porto_settings['post-title-style'] ? ' post-title-simple' : ''; ?>">
		<?php if ( $show_date ) : ?>
		<div class="post-date">
			<?php
			porto_post_date();
			// porto_post_format();
			?>
		</div>
			<?php
		endif;
		if ( $post_author ) :
			?>
			<h4 class="title-short"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
			<p class="author-name"><?php esc_html_e( 'By', 'porto' ); ?> <?php the_author_posts_link(); ?></p>
		<?php else : ?>
			<h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
		<?php endif; ?>
		<?php echo porto_get_excerpt( $excerpt_length, false ); ?>
		<a href="<?php the_permalink(); ?>" class="btn <?php echo esc_attr( $porto_post_btn_style ? $porto_post_btn_style : ( isset( $porto_settings['post-related-btn-style'] ) ? $porto_settings['post-related-btn-style'] : '' ) ); ?> <?php echo esc_attr( $porto_post_btn_color ? $porto_post_btn_color : ( isset( $porto_settings['post-related-btn-color'] ) ? $porto_settings['post-related-btn-color'] : 'btn-default' ) ); ?> <?php echo esc_attr( $porto_post_btn_size ? $porto_post_btn_size : ( isset( $porto_settings['post-related-btn-size'] ) ? $porto_settings['post-related-btn-size'] : '' ) ); ?> m-b-md"><?php esc_html_e( 'Read More', 'porto' ); ?></a>
	</div>
<?php } elseif ( 'style-2' == $post_style ) { ?>
	<div class="post-item style-2<?php echo isset( $porto_settings['post-title-style'] ) && 'without-icon' == $porto_settings['post-title-style'] ? ' post-title-simple' : ''; ?>">
		<h5>
			<a class="text-<?php echo 'dark' == $porto_settings['css-type'] ? 'light' : 'dark'; ?>" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h5>
		<?php
			echo porto_get_excerpt( $excerpt_length, false );
			get_template_part(
				'views/posts/single/meta',
				null,
				array(
					'show_date' => true,
				)
			);
		?>
	</div>
<?php } elseif ( 'style-4' == $post_style ) { ?>
	<div class="post-item style-4<?php echo isset( $porto_settings['post-title-style'] ) && 'without-icon' == $porto_settings['post-title-style'] ? ' post-title-simple' : ''; ?>">
	<div class="thumb-info">
		<div class="thumb-info-caption">
			<div class="thumb-info-caption-text">
				<a class="post-title" href="<?php the_permalink(); ?>"><h2 class="m-b-sm m-t-xs"><?php the_title(); ?></h2></a>
				<div class="post-meta m-b-sm<?php echo ( empty( $porto_settings['post-metas'] ) ? ' d-none' : '' ); ?>">
					<?php
					$first = true;
					if ( isset( $porto_settings['post-metas'] ) && in_array( 'date', $porto_settings['post-metas'] ) ) :

						?>
						<?php
						if ( $first ) {
							$first = false;
						} else {
							echo ' | ';
						}
						?>
					<?php echo get_the_date(); ?><?php endif; ?>
					<?php
					if ( isset( $porto_settings['post-metas'] ) && in_array( 'author', $porto_settings['post-metas'] ) ) :
						?>
						<?php
						if ( $first ) {
							$first = false;
						} else {
							echo ' | ';
						}
						?>
						<?php the_author_posts_link(); ?><?php endif; ?>
					<?php
					$cats_list = get_the_category_list( ', ' );
					if ( $cats_list && isset( $porto_settings['post-metas'] ) && in_array( 'cats', $porto_settings['post-metas'] ) ) :
						?>
						<?php
						if ( $first ) {
							$first = false;
						} else {
							echo ' | ';}
						?>
						<?php echo porto_filter_output( $cats_list ); ?>
					<?php endif; ?>
					<?php
					$tags_list = get_the_tag_list( '', ', ' );
					if ( $tags_list && isset( $porto_settings['post-metas'] ) && in_array( 'tags', $porto_settings['post-metas'] ) ) :
						?>
						<?php
						if ( $first ) {
							$first = false;
						} else {
							echo ' | ';}
						?>
						<?php echo porto_filter_output( $tags_list ); ?>
					<?php endif; ?>
					<?php
					if ( isset( $porto_settings['post-metas'] ) && in_array( 'comments', $porto_settings['post-metas'] ) ) :
						?>
						<?php
						if ( $first ) {
							$first = false;
						} else {
							echo ' | ';
						}
						?>
						<?php comments_popup_link( __( '0 Comments', 'porto' ), __( '1 Comment', 'porto' ), '% ' . __( 'Comments', 'porto' ) ); ?><?php endif; ?>
					<?php
					if ( function_exists( 'Post_Views_Counter' ) && 'manual' == Post_Views_Counter()->options['display']['position'] && in_array( 'post', (array) Post_Views_Counter()->options['general']['post_types_count'] ) ) {
						$post_count = do_shortcode( '[post-views]' );
						if ( $post_count ) {
							if ( $first ) {
								$first = false;
							} else {
								echo ' | ';
							}
							echo wp_kses_post( $post_count );
						}
					}
					?>
				</div>
				<?php echo porto_get_excerpt( $excerpt_length, true, true ); ?>
			</div>
		</div>
	</div>
	</div>
<?php } elseif ( 'style-5' == $post_style ) { ?>
	<div class="post-item style-5<?php echo isset( $porto_settings['post-title-style'] ) && 'without-icon' == $porto_settings['post-title-style'] ? ' post-title-simple' : ''; ?>">
		<?php
		$cats_list = '';
		$cats      = array();
		foreach ( get_the_category() as $c ) {
			$cat = get_category( $c );
			array_push( $cats, $cat->name );
		}
		if ( sizeof( $cats ) > 0 ) {
			$cats_list = implode( ', ', $cats );
		}
		if ( $cats_list && isset( $porto_settings['post-metas'] ) && in_array( 'cats', $porto_settings['post-metas'] ) ) :
			?>
			<span class="cat-names"><?php echo porto_filter_output( $cats_list ); ?></span>
		<?php endif; ?>
		<h3 class="m-b-lg">
			<a class="text-decoration-none text-<?php echo 'dark' == $porto_settings['css-type'] ? 'light' : 'dark'; ?>" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h3>
		<?php echo porto_get_excerpt( $excerpt_length, false ); ?>
		<div class="post-meta clearfix m-t-lg">
			<?php
			if ( isset( $porto_settings['post-metas'] ) && in_array( 'date', $porto_settings['post-metas'] ) ) :
				?>
				<span class="meta-date"><i class="far fa-calendar-alt"></i> <?php echo get_the_date(); ?></span><?php endif; ?>
			<?php
			if ( isset( $porto_settings['post-metas'] ) && in_array( 'author', $porto_settings['post-metas'] ) ) :
				?>
				<span class="meta-author"><i class="far fa-user"></i> <?php esc_html_e( 'By', 'porto' ); ?> <?php the_author_posts_link(); ?></span><?php endif; ?>
			<?php
			$tags_list = get_the_tag_list( '', ', ' );
			if ( $tags_list && isset( $porto_settings['post-metas'] ) && in_array( 'tags', $porto_settings['post-metas'] ) ) :
				?>
				<span class="meta-tags"><i class="far fa-envelope"></i> <?php echo porto_filter_output( $tags_list ); ?></span>
			<?php endif; ?>
			<?php
			if ( isset( $porto_settings['post-metas'] ) && in_array( 'comments', $porto_settings['post-metas'] ) ) :
				?>
				<span class="meta-comments"><i class="far fa-comments"></i> <?php comments_popup_link( __( '0 Comments', 'porto' ), __( '1 Comment', 'porto' ), '% ' . __( 'Comments', 'porto' ) ); ?></span><?php endif; ?>
			<?php
			if ( function_exists( 'Post_Views_Counter' ) && 'manual' == Post_Views_Counter()->options['display']['position'] && in_array( 'post', (array) Post_Views_Counter()->options['general']['post_types_count'] ) ) {
				$post_count = do_shortcode( '[post-views]' );
				if ( $post_count ) {
					echo wp_kses_post( $post_count );
				}
			}
			?>
		</div>
	</div>
<?php } elseif ( 'style-7' == $post_style ) { ?>
	<div class="post-item style-7<?php echo isset( $porto_settings['post-title-style'] ) && 'without-icon' == $porto_settings['post-title-style'] ? ' post-title-simple' : ''; ?>">
		<h4><a class="text-decoration-none text-<?php echo 'dark' == $porto_settings['css-type'] ? 'light' : 'dark'; ?>" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
		<?php echo porto_get_excerpt( $excerpt_length, false ); ?>
		<div class="post-meta">
			<h6 class="meta-author"><?php echo get_avatar( get_the_author_meta( 'email' ), '40' ); ?> <?php esc_html_e( 'by', 'porto' ); ?> <?php the_author_posts_link(); ?></h6>
		</div>
	</div>
<?php } else { ?>
	<div class="post-item<?php echo isset( $porto_settings['post-title-style'] ) && 'without-icon' == $porto_settings['post-title-style'] ? ' post-title-simple' : ''; ?>">
		<?php if ( $show_date ) : ?>
		<div class="post-date">
			<?php
			porto_post_date();
			// porto_post_format();
			?>
		</div>
			<?php
		endif;
		if ( $post_author ) :
			?>
			<h4 class="title-short"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
			<p class="author-name"><?php esc_html_e( 'By', 'porto' ); ?> <?php the_author_posts_link(); ?></p>
		<?php else : ?>
			<h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
		<?php endif; ?>
		<?php echo porto_get_excerpt( $excerpt_length ); ?>
	</div>
<?php } ?>
