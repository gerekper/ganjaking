<?php
use Essential_Addons_Elementor\Pro\Classes\Helper;
use Essential_Addons_Elementor\Classes\Helper as HelperClass;
?>

<div class="eael-learn-dash-course eael-course-layout-3 card-style <?php echo !empty($tags_as_string) ? esc_attr($tags_as_string) : ' '; ?>  <?php echo !empty($cats_as_string) ? esc_attr($cats_as_string) : ' '; ?> <?php echo esc_attr( $pagination_class ) ?> ">
    <div class="eael-learn-dash-course-inner">
        <?php if ( ! empty( $ribbon_atts['ribbon_text'] ) ) : ?>
            <div class="<?php echo !empty( $ribbon_atts['class'] ) ? esc_attr( $ribbon_atts['class'] ) : ''; ?>">
                <?php echo wp_kses_post( $ribbon_atts['ribbon_text'] ); ?>
            </div>
        <?php endif; ?>

<!--        --><?php //if($image): ?>
        <a class="card-thumb" href="<?php echo esc_url(get_permalink($course->ID)); ?>">
            <?php if( 1 == $ld_course_grid_enable_video_preview && ! empty( $ld_course_grid_video_embed_code ) ) : ?>
                <!-- .ld_course_grid_video_embed helps to load default css and js from learndash -->
                <div class="ld_course_grid_video_embed">
                    <?php echo HelperClass::eael_wp_kses( $ld_course_grid_video_embed_code ); ?>
                </div>
            <?php elseif( $image ) :?>
                <img src="<?php echo esc_url($image[0]); ?>" alt="<?php echo esc_attr( $image_alt ); ?>" />
            <?php else : ?>
                <img alt="" src="<?php echo esc_url( \Elementor\Utils::get_placeholder_image_src() ); ?>"/>
            <?php endif; ?>
        </a>
<!--        --><?php //endif; ?>

        <?php if($settings['show_price'] == 'true') : ?>
            <div class="card-price">
	            <?php
	            if( isset( $legacy_meta['sfwd-courses_course_price'] ) ){
		            echo esc_html( $legacy_meta['sfwd-courses_course_price'] );
	            } elseif($settings['change_free_price_text'] == 'true' && !empty($settings['free_price_text'])) {
		            echo HelperClass::eael_wp_kses( $settings['free_price_text'] );
	            } else {
		            echo __('Free', 'essential-addons-elementor');
	            }
	            ?>
            </div>
        <?php endif; ?>
        <div class="card-body">

            <<?php echo Helper::eael_pro_validate_html_tag($settings['title_tag']); ?> class="course-card-title">
            <a href="<?php echo esc_url(get_permalink($course->ID)); ?>"><?php echo HelperClass::eael_wp_kses( $course->post_title ); ?></a>
            </<?php echo Helper::eael_pro_validate_html_tag($settings['title_tag']); ?>>

            <?php if($settings['show_course_duration'] === 'true') : ?>
                <?php if( !empty( $duration_hours ) || !empty( $duration_minutes ) ) : ?>
                <div class="course-author-meta-inline course-duration-meta-inline">
                    <span><?php esc_html_e($duration_hours . 'Hrs ' . $duration_minutes . 'Mins', 'essential-addons-elementor'); ?></span>
                </div>
                <?php endif; ?>
            <?php endif; ?>

            <?php if($settings['show_course_meta'] === 'true') : ?>
            <div class="eael-learn-dash-course-meta-card">
                <?php if($access_list) : ?><span class="enrolled-count"><i class="far fa-user" aria-hidden="true"></i><?php echo esc_html( $access_list ); ?></span><?php endif; ?>

                <?php if( $settings['show_date'] === 'true' ) : ?>
                    <span class="course-date"><i class="far fa-clock" aria-hidden="true"></i><?php echo get_the_date('j M y', $course->ID); ?></span>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php if($settings['show_content'] === 'true' && !empty($short_desc)) : ?> 
            <div class="eael-learn-dash-course-short-desc">
                <?php echo wpautop($this->get_controlled_short_desc($short_desc, $excerpt_length)); ?>
            </div><?php endif; ?>

            <?php
                if($settings['show_progress_bar'] === 'true') {
                    echo do_shortcode( '[learndash_course_progress course_id="' . $course->ID . '" user_id="' . get_current_user_id() . '"]' );
                }
            ?>

            <?php if($settings['show_button'] === 'true') : ?>
                <div class="layout-button-wrap">
                    <a href="<?php echo esc_url(get_permalink($course->ID)); ?>" class="eael-course-button">
	                    <?php
	                    if($settings['change_button_text'] === 'true' && !empty($settings['button_text'])) {
		                    echo HelperClass::eael_wp_kses( $settings['button_text'] );
	                    } else {
		                    echo empty($button_text) ? __( 'See More', 'essential-addons-elementor' ) : HelperClass::eael_wp_kses( $button_text );
	                    }
	                    ?>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
