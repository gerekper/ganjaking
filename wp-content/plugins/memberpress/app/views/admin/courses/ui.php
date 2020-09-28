<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>

<div class="wrap">

  <div class="mepr-sister-plugin mepr-sister-plugin-wp-mail-smtp" data-config="<?php echo esc_attr(wp_json_encode($plugin)); ?>">

    <div class="mepr-sister-plugin-image mp-courses-image">
      <img src="<?php echo esc_url(MEPR_IMAGES_URL . '/courses-logo.svg'); ?>" width="800" height="216" alt="">
    </div>

    <div class="mepr-sister-plugin-title">
      <?php esc_html_e('The Easy Way to Make Courses with MemberPress', 'memberpress'); ?>
    </div>

    <div class="mepr-sister-plugin-description">
      <?php esc_html_e('Use the MemberPress Courses Add-On to build out your online courses. Build your Curriculum visually and provide it to your Members beautifully.', 'memberpress'); ?>
    </div>

    <div class="mepr-sister-plugin-info mepr-clearfix">
      <div class="mepr-sister-plugin-info-image">
        <div>
          <img src="<?php echo esc_url(MEPR_IMAGES_URL . '/memberpress-courses.png'); ?>" alt="<?php esc_attr_e('MemberPress Courses curriculum builder', 'memberpress'); ?>">
        </div>
      </div>
      <div class="mepr-sister-plugin-info-features">
        <ul>
          <li><i class="mp-icon mp-icon-right-big"></i><?php esc_html_e('Build Courses Visually', 'memberpress'); ?></li>
          <li><i class="mp-icon mp-icon-right-big"></i><?php esc_html_e('Showcase your Courses beautifully using Classroom Mode', 'memberpress'); ?></li>
          <li><i class="mp-icon mp-icon-right-big"></i><?php esc_html_e('Customize the front end look & feel with the WordPress Customizer', 'memberpress'); ?></li>
          <li><i class="mp-icon mp-icon-right-big"></i><?php esc_html_e('Track your learners\' progress', 'memberpress'); ?></li>
        </ul>
      </div>
    </div>

    <div class="mepr-sister-plugin-step mepr-sister-plugin-step-no-number mepr-sister-plugin-step-current mepr-clearfix">
      <div class="mepr-sister-plugin-step-detail">
        <div class="mepr-sister-plugin-step-title">
          <?php if( ! empty( $plugins['memberpress-courses/main.php'] ) ) : // Installed but not active ?>
            <?php esc_html_e('Enable Courses', 'memberpress'); ?>
          <?php else : // Not installed ?>
            <?php esc_html_e('Install and Activate MemberPress Courses', 'memberpress'); ?>
          <?php endif; ?>
        </div>
        <div class="mepr-sister-plugin-step-button">
          <?php if( ! empty( $plugins['memberpress-courses/main.php'] ) ) : // Installed but not active ?>
            <button type="button" class="mepr-courses-action button button-primary button-hero" data-action="activate"><?php esc_html_e('Activate Courses Add-On', 'memberpress'); ?></button>
          <?php else : // Not installed ?>
            <button type="button" class="mepr-courses-action button button-primary button-hero" data-action="install-activate"><?php esc_html_e('Install & Activate MemberPress Courses Add-On', 'memberpress'); ?></button>
          <?php endif; ?>
        </div>
        <div id="mepr-courses-action-notice" class="mepr-courses-action-notice notice inline"><p></p></div>
      </div>
    </div>

  </div>
</div>

<script>
  jQuery(document).ready(function($) {
    $('.mepr-courses-action').click(function(event) {
      event.preventDefault();
      var $this = $(this);
      $this.prop('disabled', 'disabled');
      var notice = $('#mepr-courses-action-notice');
      $.ajax({
        url: ajaxurl,
        type: 'POST',
        data: {
          action: 'mepr_courses_action',
          nonce: "<?php echo wp_create_nonce( 'mepr_courses_action' ); ?>",
          type: $this.data('action')
        },
      })
      .done(function(data) {
        $this.remove();
        if ( data.data.redirect.length > 0 ) {
          window.location.href = data.data.redirect;
        } else {
          notice.find('p').html(data.data.message);
          notice.addClass('notice-' + data.data.result);
          notice.show();
          $this.removeProp('disabled');
        }
      })
      .fail(function(data) {
        console.log(data);
        notice.find('p').html(data.data.message);
        notice.addClass('notice-' + data.data.result);
        notice.show();
        $this.removeProp('disabled');
      })
      .always(function(data) {

      });
    });
  });
</script>
