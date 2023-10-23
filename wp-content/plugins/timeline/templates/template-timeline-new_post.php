<?php

  $current_post = get_post($arr['post_id']);
  $post_link = '<a href='.get_permalink($arr['post_id']).' target="_blank">Read More</a>';
//  setup_postdata( $current_post );
  $excerpt = strip_tags($current_post->post_content,'<p><a><b><strong><i><u><h1><h2><h3><h4><h5><h6><h7>');
  if(strlen( $excerpt )>100){
    $excerpt = substr( $excerpt,0,100);
    $excerpt .= '...'.$post_link;
  }
?>

<div id="profile-description">
  <div class="up-timeline-preview">
    <div class="up-timeline-icon-pp">
      <div class="up-icon-profile up-timeline-new-post"></div>
    </div>
    <div class="up-timeline-pointer-pp">
      <span class="tl-pointer"></span>
    </div>
    <div class="up-timeline-content">

        <div class="up-timeline-post-title">
          <?php _e( "Created new post - {$current_post->post_title}" );?>
        </div>
        <div class="up-timeline-thumb">
          <img src="<?php echo get_the_post_thumbnail_url($arr['post_id'],'medium');?>" alt="">
        </div>
        <div class="up-timeline-post-description">
          <?php
            echo $excerpt;
          ?>
        </div>

    </div>
    <div class="up-timeline-bar">
      <div class="postdte"><?php echo date('d M Y', $arr['timestamp']); ?></div>
    </div>
  </div>
</div>
