<?php
  global $userpro;
  $t_id = $arr['target_user_id'];
  $t_display_name = userpro_profile_data( 'display_name', $t_id );
  $t_link = $userpro->permalink($t_id);
  $t_link = '<a href='.$t_link.'>'.ucfirst( $t_display_name ).'</a>';
?>
<div id="profile-description">
  <div class="up-timeline-preview"><div class="up-timeline-icon-pp">
      <div class="up-icon-profile up-timeline-stopped-following"></div></div>
        <div class="up-timeline-pointer-pp">
          <span class="tl-pointer"></span>
        </div>
        <div class="up-timeline-content">
          <p class="userpro-timeline-description">
            <?php _e("Stopped following {$t_link}",'userpro');?>
          </p>
      </div>
      <div class="up-timeline-bar">
        <div class="postdte"><?php echo date('d M Y', $arr['timestamp']); ?></div>
      </div>
  </div>
</div>
