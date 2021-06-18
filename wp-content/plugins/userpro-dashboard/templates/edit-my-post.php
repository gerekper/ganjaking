<div class="updb-display-post">
    <?php
        $updb_status = array('publish'=>'published','draft'=>'draft','pending'=>'pending');
     ?>   
    <div class='updb-featured-image'><?php if( has_post_thumbnail( $single_post->ID )) {
        $url = wp_get_attachment_url( get_post_thumbnail_id($single_post->ID, 'thumbnail') );
        if(in_array($single_post->post_status,array('pending','draft')) && !current_user_can('administrator')) {
            echo '<img src= '.$url.' />';
        }
        else {
            echo '<a href='.get_post_permalink($single_post->ID).'><img src= '.$url.' /></a>';
        }
    }
    else{
        if(in_array($single_post->post_status,array('pending','draft')) && !current_user_can('administrator')) {
            echo '<img src="'.userpro_url . 'img/placeholder.jpg" width="" height="" class="modified no_feature" />';
        }
        else {
            echo '<a href='.get_post_permalink($single_post->ID).'><img src="'.userpro_url . 'img/placeholder.jpg" width="" height="" class="modified no_feature" /></a>';
        }
    }
        ?></div>
    <div class='updb-post-contents'>
        <div class='updb-post-title'>
            <?php if(in_array($single_post->post_status,array('pending','draft')) && !current_user_can('administrator')) {
             echo $single_post->post_title; 
             } else { ?>
            <a href='<?php if(in_array($single_post->post_status,array('pending','draft')) && !current_user_can('administrator')) echo '#'; else echo get_post_permalink($single_post->ID) ?>'><?php echo $single_post->post_title; ?></a>
            <?php } ?>
        </div>
        <div class='updb-post-content'><?php echo substr($single_post->post_content,0,25);  if(strlen($single_post->post_content) > strlen(substr($single_post->post_content,0,25))) { echo '...'; }?>
            <div class="updb-post-status"><?php echo 'Status : '.$updb_status[$single_post->post_status] ?></div></div>
    </div>
    <div class='updb-delete-edit'><a class="updb-edit-post" href="#" data-postid = "<?php echo $single_post->ID?>" ><i class="fa fa-pencil"></i></a> <?php if( current_user_can('administrator') ) {?><a onclick="return confirm('Are you SURE you want to delete this post?')" href="<?php echo get_delete_post_link( $single_post->ID ) ?>"><i class="fa fa-trash"></i></a><?php } ?></div>
</div>
