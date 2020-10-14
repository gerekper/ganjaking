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
        <div id="mpcs-lesson-<?php echo $lesson->ID; ?>" class="mpcs-lesson">
          <a href="<?php echo get_permalink($lesson->ID); ?>" class="mpcs-lesson-row-link">
            <div class="mpcs-lesson-progress">
              <?php if(is_user_logged_in() && models\UserProgress::has_completed_lesson($current_user->ID, $lesson->ID)): ?>
                <span class="mpcs-lesson-complete"><i class="mpcs-ok-circled"></i></span>
              <?php else: ?>
                <span class="mpcs-lesson-not-complete"><i class="mpcs-circle-regular"></i></span>
              <?php endif; ?>
            </div>
            <div class="mpcs-lesson-link"><?php echo $lesson->post_title; ?></div>
            <div class="mpcs-lesson-button">
            <span class="mpcs-button" href="<?php echo get_permalink($lesson->ID); ?>">
              <?php if(is_user_logged_in() && models\UserProgress::has_completed_lesson($current_user->ID, $lesson->ID)): ?>
                <span class="mpcs-button is-outline" href="<?php echo get_permalink($lesson->ID); ?>">
                  <?php esc_html_e('View', 'memberpress-courses') ?>
                </span>
              <?php else: ?>
                <span class="mpcs-button is-purple" href="<?php echo get_permalink($lesson->ID); ?>">
                  <?php esc_html_e('Start', 'memberpress-courses') ?>
              </span>
              <?php endif; ?>
            </span>
            </div>
          </a>
        </div>
      <?php endforeach; ?>
    </div> <!-- mpcs-lessons -->
  </div> <!-- mpcs-section -->
<?php endforeach; ?>
