<?php
  global $userpro;
 ?>
<div id="profile-description">
  <div class="up-timeline-preview"><div class="up-timeline-icon-pp">
      <div class="up-icon-profile-pic" style="background: url('<?php echo userpro_profile_data('profilepicture',$user_id); ?>')">
      </div>
    </div>
        <div class="up-timeline-pointer-pp">
          <span class="tl-pointer"></span>
        </div>
        <div class="up-timeline-content">
          <p class="userpro-timeline-description">
            <?php _e("Changed profile picture",'userpro');?>
          </p>
      </div>
      <div class="up-timeline-bar">
        <div class="postdte"><?php echo date('d M Y', $arr['timestamp']); ?></div>
      </div>
  </div>
</div>
