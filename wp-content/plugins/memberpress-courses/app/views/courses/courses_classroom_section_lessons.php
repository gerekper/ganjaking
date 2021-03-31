<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); } ?>
<?php use memberpress\courses\models as models; ?>

<?php foreach($sections as $section): ?>
  <div id="section<?php echo (string)((int)$section->section_order + 1); ?>" class="mpcs-section">
    <div class="mpcs-section-header">
      <div class="mpcs-section-title">
        <span class="mpcs-section-title-text"><?php echo $section->title; ?></span>
      </div>
      <div class="mpcs-section-description"><?php echo $section->description; ?></div>
    </div> <!-- mpcs-section-header -->
    <div class="mpcs-lessons">
      <?php foreach($section->lessons() as $lesson_index => $lesson): ?>
        <div id="mpcs-lesson-<?php echo $lesson->ID; ?>" class="mpcs-lesson <?php
          if(models\UserProgress::has_completed_lesson($current_user_id, $lesson->ID)) echo "completed ";
          if($is_sidebar && $lesson->ID == get_the_ID()) echo "current ";
          if($show_bookmark && isset($next_lesson->ID) && $next_lesson->ID == $lesson->ID) echo "current ";
          ?>">
          <a href="<?php echo get_permalink($lesson->ID); ?>" class="mpcs-lesson-row-link">
            <div class="mpcs-lesson-progress">
              <?php if(is_user_logged_in() && models\UserProgress::has_completed_lesson($current_user_id, $lesson->ID)): ?>
                <span class="mpcs-lesson-complete"><i class="mpcs-ok-circled"></i></span>
                <?php elseif(($is_sidebar && $lesson->ID == get_the_ID()) || ($show_bookmark && $next_lesson->ID == $lesson->ID)): ?>
                  <span class="mpcs-lesson-current"><i class="mpcs-adjust-solid"></i></span>
                <?php else: ?>
                <span class="mpcs-lesson-not-complete"><i class="mpcs-circle-regular"></i></span>
              <?php endif; ?>
            </div>
            <div class="mpcs-lesson-link"><i class="mpcs-doc-text-inv"></i><?php echo $lesson->post_title; ?></div>
            <div class="mpcs-lesson-button">

            <?php if( is_user_logged_in() && false === $is_sidebar ) : ?>
              <span class="mpcs-button">
                <?php if(is_user_logged_in() && models\UserProgress::has_completed_lesson($current_user_id, $lesson->ID)): ?>
                  <span class="btn is-outline" href="<?php echo get_permalink($lesson->ID); ?>">
                    <?php esc_html_e('View', 'memberpress-courses') ?>
                  </span>
                <?php else: ?>
                  <span class="btn btn-green is-purple" href="<?php echo get_permalink($lesson->ID); ?>">
                    <?php esc_html_e('Start', 'memberpress-courses') ?>
                </span>
                <?php endif; ?>
              </span>
            <?php endif; ?>

            </div>
          </a>
        </div>
      <?php endforeach; ?>
    </div> <!-- mpcs-lessons -->
  </div> <!-- mpcs-section -->
<?php endforeach; ?>
