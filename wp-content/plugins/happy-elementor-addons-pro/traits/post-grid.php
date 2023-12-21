<?php
namespace Happy_Addons_Pro\Traits;

use Elementor\Icons_Manager;

defined( 'ABSPATH' ) || die();

trait Post_Grid_Markup {

    public static function render_classic_markup( $settings = [], $post = null ) {
        // $thumb_wraper_cls = 'ha-pg-thumb-area';
        // if ( 'yes'=== $settings['featured_image'] && has_post_thumbnail() ){
        //     $thumb_wraper_cls .= ' has-post-thumb';
        // }
        //$excerpt_length = (int) $settings['excerpt_length'];

        $featured_image = self::get_value( 'featured_image', $settings );
        $featured_image_size = self::get_value( 'featured_image_size', $settings );
        $show_badge = self::get_value( 'show_badge', $settings );
        $taxonomy_badge = self::get_value( 'taxonomy_badge', $settings );
        $show_title = self::get_value( 'show_title', $settings );
        $title_tag = self::get_value( 'title_tag', $settings );
        $active_meta = self::get_value( 'active_meta', $settings );
        $meta_has_icon = self::get_value( 'meta_has_icon', $settings );
        $excerpt_length = self::get_value( 'excerpt_length', $settings );

        // echo '<pre>';
        // var_dump($active_meta);
        // echo '</pre>';

        $readmpre = self::get_value( 'read_more', $settings );
        $new_tab = self::get_value( 'read_more_new_tab', $settings );
        ?>
		<div class="ha-pg-item">
			<?php if ( 'yes' === $featured_image && has_post_thumbnail() ): ?>
				<div class="ha-pg-thumb-area">
					<a href="<?php echo esc_url( get_the_permalink( get_the_ID() ) ); ?>" class="ha-pg-thumb">
						<?php //echo get_the_post_thumbnail( get_the_ID() ); ?>
						<?php the_post_thumbnail( $featured_image_size );?>
					</a>
					<?php self::render_badge( $show_badge, $taxonomy_badge );?>
				</div>
			<?php endif;?>
			<div class="ha-pg-content-area">

				<?php self::render_title( $show_title, $title_tag )?>

				<?php self::render_meta( $active_meta, $meta_has_icon )?>

				<?php self::render_excerpt( $excerpt_length )?>

				<?php self::render_read_more( $readmpre, $new_tab );?>
			</div>
		</div>
	<?php
}

    public static function render_hawai_markup( $settings = [], $post = null ) {
        $featured_image = self::get_value( 'featured_image', $settings );
        $featured_image_size = self::get_value( 'featured_image_size', $settings );
        $show_badge = self::get_value( 'show_badge', $settings );
        $taxonomy_badge = self::get_value( 'taxonomy_badge', $settings );
        $show_title = self::get_value( 'show_title', $settings );
        $title_tag = self::get_value( 'title_tag', $settings );
        $active_meta = self::get_value( 'active_meta', $settings );
        $meta_has_icon = self::get_value( 'meta_has_icon', $settings );
        $excerpt_length = self::get_value( 'excerpt_length', $settings );

        $readmpre = self::get_value( 'read_more', $settings );
        $new_tab = self::get_value( 'read_more_new_tab', $settings );
        ?>
		<div class="ha-pg-item">
			<?php if ( 'yes' === $featured_image && has_post_thumbnail() ): ?>
				<div class="ha-pg-thumb-area">
					<a href="<?php echo esc_url( get_the_permalink( get_the_ID() ) ); ?>" class="ha-pg-thumb">
						<?php //echo get_the_post_thumbnail( get_the_ID() ); ?>
						<?php the_post_thumbnail( $featured_image_size );?>
					</a>
				</div>
			<?php endif;?>
			<div class="ha-pg-content-area">
				<?php self::render_badge( $show_badge, $taxonomy_badge );?>

				<?php self::render_title( $show_title, $title_tag );?>

				<?php self::render_meta( $active_meta, $meta_has_icon );?>

				<?php self::render_excerpt( $excerpt_length );?>

                <?php self::render_read_more( $readmpre, $new_tab );?>
			</div>
		</div>
	<?php
}

    public static function render_standard_markup( $settings = [], $post = null ) {
        $featured_image = self::get_value( 'featured_image', $settings );
        $featured_image_size = self::get_value( 'featured_image_size', $settings );
        $show_badge = self::get_value( 'show_badge', $settings );
        $taxonomy_badge = self::get_value( 'taxonomy_badge', $settings );
        $show_title = self::get_value( 'show_title', $settings );
        $title_tag = self::get_value( 'title_tag', $settings );
        $active_meta = self::get_value( 'active_meta', $settings );
        $meta_has_icon = self::get_value( 'meta_has_icon', $settings );
        $meta_position = self::get_value( 'meta_position', $settings );
        $excerpt_length = self::get_value( 'excerpt_length', $settings );

        $readmpre = self::get_value( 'read_more', $settings );
		$new_tab = self::get_value( 'read_more_new_tab', $settings );

        ?>
		<div class="ha-pg-item">
			<?php if ( 'yes' === $featured_image && has_post_thumbnail() ): ?>
				<div class="ha-pg-thumb-area">
					<a href="<?php echo esc_url( get_the_permalink( get_the_ID() ) ); ?>" class="ha-pg-thumb">
						<?php the_post_thumbnail( $featured_image_size );?>
                    </a>
                    <?php self::render_read_more( $readmpre, $new_tab );?>
                </div>
			<?php endif;?>
			<div class="ha-pg-content-area">
				<?php self::render_badge( $show_badge, $taxonomy_badge );?>

				<?php
					if ($meta_position == 'before') {
						self::render_meta( $active_meta, $meta_has_icon );
					}
					self::render_title( $show_title, $title_tag );

					if ($meta_position == 'after') {
						self::render_meta( $active_meta, $meta_has_icon );
					}
				?>

				<?php self::render_excerpt( $excerpt_length );?>
			</div>
        </div>
	<?php
}

    public static function render_monastic_markup( $settings = [], $post = null ) {
        $featured_image = self::get_value( 'featured_image', $settings );
        $featured_image_size = self::get_value( 'featured_image_size', $settings );
        $show_badge = self::get_value( 'show_badge', $settings );
        $taxonomy_badge = self::get_value( 'taxonomy_badge', $settings );
        $show_title = self::get_value( 'show_title', $settings );
        $title_tag = self::get_value( 'title_tag', $settings );
        $active_meta = self::get_value( 'active_meta', $settings );
        $meta_has_icon = self::get_value( 'meta_has_icon', $settings );
        $excerpt_length = self::get_value( 'excerpt_length', $settings );
        ?>
		<div class="ha-pg-item">
			<div class="ha-pg-monastic-top">
				<?php if ( 'yes' === $featured_image && has_post_thumbnail() ): ?>
					<div class="ha-pg-thumb-area">
						<a href="<?php echo esc_url( get_the_permalink( get_the_ID() ) ); ?>" class="ha-pg-thumb">
							<?php the_post_thumbnail( $featured_image_size );?>
						</a>
					</div>
				<?php endif;?>
				<div class="ha-pg-content-area">
					<?php self::render_badge( $show_badge, $taxonomy_badge );?>

					<?php self::render_title( $show_title, $title_tag );?>

					<?php self::render_excerpt( $excerpt_length );?>
				</div>
			</div>
			<?php self::render_meta( $active_meta, $meta_has_icon ); ?>
        </div>
	<?php
}

    public static function render_stylica_markup( $settings = [], $post = null ) {
        $featured_image = self::get_value( 'featured_image', $settings );
        $featured_image_size = self::get_value( 'featured_image_size', $settings );
        $show_badge = self::get_value( 'show_badge', $settings );
        $taxonomy_badge = self::get_value( 'taxonomy_badge', $settings );
        $show_title = self::get_value( 'show_title', $settings );
        $title_tag = self::get_value( 'title_tag', $settings );
        $active_meta = self::get_value( 'active_meta', $settings );
        $meta_has_icon = self::get_value( 'meta_has_icon', $settings );
        $excerpt_length = self::get_value( 'excerpt_length', $settings );
        $excerpt_length = self::get_value( 'excerpt_length', $settings );
        $devider_shape = self::get_value( 'devider_shape', $settings );
        ?>
		<div class="ha-pg-item">
			<?php if ( 'yes' === $featured_image && has_post_thumbnail() ): ?>
				<div class="ha-pg-thumb-area">
					<a href="<?php echo esc_url( get_the_permalink( get_the_ID() ) ); ?>" class="ha-pg-thumb">
						<?php the_post_thumbnail( $featured_image_size );?>
						<?php echo hapro_devider_shape( $devider_shape );?>
						<?php //echo file_get_contents( HAPPY_ADDONS_PRO_ASSETS . 'imgs/masking-shape/shape1.svg' ); ?>
                    </a>
                </div>
			<?php endif;?>
			<div class="ha-pg-content-area">
				<?php self::render_badge( $show_badge, $taxonomy_badge );?>

				<?php self::render_title( $show_title, $title_tag );?>

				<?php self::render_excerpt( $excerpt_length );?>
			</div>
			<?php self::render_meta( $active_meta, $meta_has_icon ); ?>
        </div>
	<?php
}

    public static function render_outbox_markup( $settings = [], $post = null ) {
        $featured_image = self::get_value( 'featured_image', $settings );
        $featured_image_size = self::get_value( 'featured_image_size', $settings );
        $show_badge = self::get_value( 'show_badge', $settings );
        $taxonomy_badge = self::get_value( 'taxonomy_badge', $settings );
        $show_title = self::get_value( 'show_title', $settings );
        $title_tag = self::get_value( 'title_tag', $settings );
        $active_meta = self::get_value( 'active_meta', $settings );
        $meta_has_icon = self::get_value( 'meta_has_icon', $settings );
        $excerpt_length = self::get_value( 'excerpt_length', $settings );
        $excerpt_length = self::get_value( 'excerpt_length', $settings );
        ?>
		<div class="ha-pg-item">
			<div class="ha-pg-outbox-top">
				<?php if ( 'yes' === $featured_image && has_post_thumbnail() ): ?>
					<div class="ha-pg-thumb-area">
						<a href="<?php echo esc_url( get_the_permalink( get_the_ID() ) ); ?>" class="ha-pg-thumb">
							<?php the_post_thumbnail( $featured_image_size );?>
						</a>

						<?php self::render_badge( $show_badge, $taxonomy_badge );?>
						<?php self::render_avater();?>
					</div>
				<?php endif;?>
				<div class="ha-pg-content-area">
					<?php self::render_title( $show_title, $title_tag );?>

					<?php self::render_excerpt( $excerpt_length );?>
				</div>
			</div>
            <?php self::render_meta( $active_meta, $meta_has_icon ); ?>
		</div>
	<?php
}

    public static function render_crossroad_markup( $settings = [], $post = null ) {
        $featured_image = self::get_value( 'featured_image', $settings );
        $featured_image_size = self::get_value( 'featured_image_size', $settings );
        $show_badge = self::get_value( 'show_badge', $settings );
        $taxonomy_badge = self::get_value( 'taxonomy_badge', $settings );
        $show_title = self::get_value( 'show_title', $settings );
        $title_tag = self::get_value( 'title_tag', $settings );
        $active_meta = self::get_value( 'active_meta', $settings );
        $meta_has_icon = self::get_value( 'meta_has_icon', $settings );
        $excerpt_length = self::get_value( 'excerpt_length', $settings );
        $excerpt_length = self::get_value( 'excerpt_length', $settings );
        ?>
		<div class="ha-pg-item">
			<?php if ( 'yes' === $featured_image && has_post_thumbnail() ): ?>
				<div class="ha-pg-thumb-area">
					<a href="<?php echo esc_url( get_the_permalink( get_the_ID() ) ); ?>" class="ha-pg-thumb">
						<?php the_post_thumbnail( $featured_image_size );?>
                    </a>
                </div>
			<?php endif;?>
			<div class="ha-pg-content-area">
				<?php self::render_badge( $show_badge, $taxonomy_badge );?>

				<?php self::render_title( $show_title, $title_tag );?>

				<?php self::render_excerpt( $excerpt_length );?>

				<?php self::render_meta( $active_meta, $meta_has_icon ); ?>
			</div>
		</div>
	<?php
}

    protected static function get_value( $control_id, $settings ) {
        if ( $settings['_skin'] ) {
            $skin_id = str_replace( '-', '_', $settings['_skin'] );
            $id = $skin_id . '_' . $control_id;
        } else {
            $id = $control_id;
        }

        return $settings[$id];
    }

    protected static function render_badge( $show_badge, $taxonomy_id, $post_id = null ) {

        if ( 'yes' !== $show_badge || !$taxonomy_id || !ha_pro_the_first_taxonomy( $post_id, $taxonomy_id ) ) {
            return;
        }
        ?>
			<div class="ha-pg-badge">
				<?php echo ha_pro_the_first_taxonomy( $post_id, $taxonomy_id ); ?>
			</div>
		<?php
}

    protected static function render_title( $show_title, $title_tag ) {

        if ( 'yes' === $show_title && get_the_title() ) {
            printf( '<%1$s %2$s><a href="%3$s">%4$s</a></%1$s>',
                tag_escape( $title_tag ),
                'class="ha-pg-title"',
                esc_url( get_the_permalink( get_the_ID() ) ),
                esc_html( get_the_title() )
                // esc_html( 'Best travel plan for your next vacation plan for your next vacation' )
            );
        }
        // echo '<pre>';
        // var_dump( array_keys($settings['active_meta'], 'comments' ,true) );
        // echo '</pre>';
    }

    protected static function render_meta( $active_meta, $has_icon ) {
        if ( empty( $active_meta ) ) {
            return;
        }
        ?>
			<div class="ha-pg-meta-wrap">
				<ul>
					<?php if ( in_array( 'author', $active_meta ) ): ?>
						<li class="ha-pg-author">
						<?php self::render_author( $has_icon );?>
						</li>
					<?php endif;?>
					<?php if ( in_array( 'date', $active_meta ) ): ?>
						<li class="ha-pg-date">
							<?php self::render_date( $has_icon ); //echo get_the_date( get_option( 'date_format' ) ) ?>
						</li>
					<?php endif;?>
					<?php if ( in_array( 'comments', $active_meta ) ): ?>
						<li class="ha-pg-comment">
							<?php self::render_comments( $has_icon );?>
						</li>
					<?php endif;?>
				</ul>
			</div>
		<?php
}

    protected static function render_author( $has_icon = false ) {
        $link = get_author_posts_url( get_the_author_meta( 'ID' ) );
        /* <i class="fa fa-user"></i> */
        ?>
		<a class="ha-pg-author-text" href="<?php echo esc_url( $link ); ?>">
			<?php if ( $has_icon ): ?>
				<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32"><path d="M30 26.4V29c0 1.7-1.3 3-3 3H5c-1.7 0-3-1.3-3-3v-2.6c0-4.6 3.8-8.4 8.4-8.4h1c1.4 0.6 2.9 1 4.6 1s3.2-0.4 4.6-1h1C26.2 18 30 21.8 30 26.4zM8 8c0-4.4 3.6-8 8-8s8 3.6 8 8 -3.6 8-8 8S8 12.4 8 8z"/></svg>
			<?php endif;?>
			<?php the_author();?>
		</a>
		<?php
}

    protected static function render_avater() {
        ?>
		<div class="ha-pg-avatar">
			<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" x="0px" y="0px" width="300px" height="105px" viewBox="0 0 300 105" xml:space="preserve">
				<path d="M0,104.9h300V79.8h-17.9c-26.1,0-49.8-14.6-62.1-37.6c-13.4-25-39.9-42.1-70.3-42.1s-56.8,17-70.3,42.1  c-12.3,23-36,37.6-62.1,37.6H0V104.9z"></path>
			</svg>
			<?php echo get_avatar( get_the_author_meta( 'ID' ), '60' );?>
		</div>
		<?php
}

    protected static function render_date( $has_icon = false ) {
        $link = ha_pro_get_date_link();
        /* <i class="fa fa-clock-o"></i> */
        ?>
		<a class="ha-pg-date-text" href="<?php echo esc_url( $link ); ?>">
			<?php if ( $has_icon ): ?>
				<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32"><path d="M32 16c0 8.8-7.2 16-16 16S0 24.8 0 16 7.2 0 16 0 32 7.2 32 16zM22.2 19.5c0-0.3-0.2-0.6-0.4-0.8L18.1 16V6.7c0-0.6-0.5-1-1-1H15c-0.6 0-1 0.5-1 1v10 0c0 0.7 0.4 1.6 1 2l4.3 3.2c0.2 0.1 0.4 0.2 0.6 0.2 0.3 0 0.6-0.2 0.8-0.4l1.3-1.6C22.1 20 22.2 19.7 22.2 19.5z"/></svg>
			<?php endif;?>
			<?php echo esc_html( get_the_date( get_option( 'date_format' ) ) ); ?>
		</a>
		<?php
}

    protected static function render_comments( $has_icon = false ) {
        /* <i class="fa fa-comment"></i> */
        ?>
		<span class="ha-pg-comment-text">
			<?php if ( $has_icon ): ?>
			<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32"><path d="M32 4v18c0 2.2-1.8 4-4 4h-9l-7.8 5.9c-0.5 0.4-1.2 0-1.2-0.6V26H4c-2.2 0-4-1.8-4-4V4c0-2.2 1.8-4 4-4h24C30.2 0 32 1.8 32 4z"/></svg>
			<?php endif;?>
			<?php comments_number();?>
		</span>
		<?php
}

    protected static function render_excerpt( $excerpt_length = false ) {
        if ( empty( $excerpt_length ) ) {
            return;
        }
        ?>
			<div class="ha-pg-excerpt">
				<?php printf( '<p>%1$s</p>', ha_pro_get_excerpt( get_the_ID(), $excerpt_length ) );?>
			</div>
		<?php
}

    protected static function render_read_more( $read_more_text = false, $new_tab = false ) {
        if ( $read_more_text ) {
            printf(
                '<div class="%1$s"><a href="%2$s" target="%3$s">%4$s</a></div>',
                'ha-pg-readmore',
                esc_url( get_the_permalink( get_the_ID() ) ),
                'yes' === $new_tab ? '_blank' : '_self',
                esc_html( $read_more_text )
                // 'Continue Reading »'
            );
        }
    }

}
