<?php

if (!defined('ABSPATH'))
    exit;

get_header(); ?>

    <?php
    /**
     * @hook pending_thankyou_message -10
     * pending_order_survey_before_main_content
     *
     */

    do_action( 'pending_order_survey_before_main_content' );

    ?>
    <?php while( have_posts() ) : the_post();?>
        <?php load_template( YITH_WCPO_SURVEY_TEMPLATE_PATH.'content-single-pending-survey.php');?>
    <?php endwhile;?>

    <?php
    /**
     * pending_order_survey_after_main_content
     *
     */
    do_action( 'pending_order_survey_after_main_content' );
    ?>

<?php get_footer(); ?>
