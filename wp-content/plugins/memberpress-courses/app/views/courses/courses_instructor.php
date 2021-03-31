<?php
use memberpress\courses\lib\Utils;
use memberpress\courses as base;
do_action(base\SLUG_KEY . '_classroom_start_instructor');
?>

<div class="tile mpcs-instructor">
  <div class="tile-icon">
    <?php echo Utils::get_avatar( get_the_author_meta( 'ID' ), '500', '', '', array('height' => 300, 'width' => 300) ) ?>
    <ul class="tile-socials">
      <?php if(!empty(get_the_author_meta('facebook'))) { ?>
        <li><a href="<?php the_author_meta('facebook'); ?>" title="Facebook" target="_blank" id="facebook"><i class="mpcs-facebook-squared"></i></a></li>
      <?php } ?>

      <?php if(!empty(get_the_author_meta('twitter'))) { ?>
        <li><a href="<?php the_author_meta('twitter'); ?>" title="twitter" target="_blank" id="twitter"><i class="mpcs-twitter-squared"></i></a></li>
      <?php } ?>

      <?php if(!empty(get_the_author_meta('Instagram'))) { ?>
        <li><a href="<?php the_author_meta('Instagram'); ?>" title="instagram" target="_blank" id="instagram"><i class="mpcs-instagram-1"></i></a></li>
      <?php } ?>

      <?php if(!empty(get_the_author_meta('youtube'))) { ?>
        <li><a href="<?php the_author_meta('youtube'); ?>" title="youtube" target="_blank" id="youtube"><i class="mpcs-youtube"></i></a></li>
      <?php } ?>


    </ul>
  </div>
  <div class="tile-content">
    <div class="tile-title"><?php echo get_the_author_meta( 'first_name' ) .' '. get_the_author_meta( 'last_name' ) ?></div>
    <div class="tile-subtitle"><?php echo esc_html__('Course Instructor', 'memberpress-courses') ?></div>
    <div class="tile-description"><?php echo wpautop(get_the_author_meta( 'description' ))  ?></div>

    <div class="tile-meta">
      <?php if(!empty(get_the_author_meta('user_email'))) { ?>
        <p>Email: <a href="mailto:<?php the_author_meta('user_email'); ?>" ><?php the_author_meta('user_email'); ?></a></p>
      <?php } ?>

      <?php if(!empty(get_the_author_meta('user_url'))) { ?>
        <p>Website: <a href="<?php the_author_meta('user_url'); ?>" target="_blank" ><?php the_author_meta('user_url'); ?></a></p>
      <?php } ?>
    </div>

  </div>
</div>

<?php do_action(base\SLUG_KEY . '_classroom_end_instructor'); ?>
