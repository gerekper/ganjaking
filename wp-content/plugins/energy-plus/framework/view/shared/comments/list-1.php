<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
?>

<?php if (!$ajax) { ?>
  <?php echo EnergyPlus_View::run('header-energyplus'); ?>
  <?php
  $stars_avg = absint($stars[0]->average);
  $stars_avg_f = sprintf("%.1f", (float)$stars[0]->average);
  $stars_div = '
  <div class="__A__Stars __A__StarsBig">
  <span class="__A__StarsUp">'. str_repeat('★ ', $stars_avg) .'</span>
  <span class="__A__StarsDown">'. str_repeat('★ ', 5-$stars_avg) .'</span>
  </div>
  <div class="__A__StarsInfo">'. sprintf(esc_html__('You have %1$s average in %2$s reviews', "energyplus"), $stars_avg_f, $stars[0]->cnt)."</div>";
  ?>

  <?php echo EnergyPlus_View::run('header-page', array('type'=> 1, 'title' => esc_html__('Comments', 'energyplus'), 'description' => '', 'buttons'=>$stars_div )); ?>
  <?php echo EnergyPlus_View::run('comments/nav', array('count' => $counts)) ?>

  <div id="energyplus-comments-2" >

    <div class="__A__Searching<?php if ('' === EnergyPlus_Helpers::get('s', '')) {
      echo" closed";
    } ?>">
    <div class="__A__Searching_In">
      <input type="text" class="form-control __A__Search_Input" placeholder="<?php esc_html_e('Search in comments...', 'energyplus'); ?>" value="<?php echo esc_attr(EnergyPlus_Helpers::get('s'));  ?>" autofocus></span>
    </div>
  </div>

  <div class=" __A__GP __A__List_M1 __A__Container">
  <?php } ?>

  <div class="__A__List_M1_Bulk __A__Bulk">
    <?php if ('trash' ===  EnergyPlus_Helpers::get('status')) { ?>
      <a class="__A__Button1 __A__Bulk_Do" data-do="restore" href="javascript:;"><?php esc_html_e('Restore comments', 'energyplus'); ?></a> <a class="__A__Button1 __A__Bulk_Do" data-do="deleteforever" href="javascript:;"><?php esc_html_e('Delete forever', 'energyplus'); ?></a>
    <?php } else { ?>
      <a class="__A__Button1 __A__Bulk_Do __A__Bulk_Approve" data-do="approve" href="javascript:;"><?php esc_html_e('Approve selected comments', 'energyplus'); ?></a> <a class="__A__Button1 __A__Bulk_Do __A__Bulk_Unapprove" data-do="unapprove" href="javascript:;"><?php esc_html_e('Unapprove selected comments', 'energyplus'); ?></a> <a class="__A__Button1  __A__Bulk_Do" data-do="trash" href="javascript:;"><?php esc_html_e('Delete them', 'energyplus'); ?></a>
    <?php }?>
    <a class="__A__Select_All float-right" data-state='select' href="javascript:;"><?php esc_html_e('Select All', 'energyplus'); ?></a>
  </div>
  <div class="__A__Comments_Container">

  <?php if (0 === count($comments)) { ?>
    <div class="__A__EmptyTable d-flex align-items-center justify-content-center text-center">
      <div>  <span class="dashicons dashicons-marker"></span><br><?php esc_html_e('No records found', 'energyplus'); ?></div>
    </div>
  <?php } else { ?>

    <?php
    $comment_ids = array(-1);
    foreach ($comments as $comment) {
      $comment_ids[] = $comment->comment_ID;
      ?>
      <div class="btnA __A__Item collapsed"  id="item_<?php echo esc_attr($comment->comment_ID)?>" data-toggle="collapse" data-target="#item_d_<?php echo esc_attr($comment->comment_ID)?>" aria-expanded="false" aria-controls="item_d_<?php echo esc_attr($comment->comment_ID)?>">
        <div class="liste  row d-flex align-items-center">
          <div class="__A__Checkbox_Hidden">
            <input type="checkbox" class="__A__Checkbox __A__StopPropagation"  data-id='<?php echo esc_attr($comment->comment_ID)  ?>' data-state='s<?php echo esc_attr( $comment->comment_approved ) ?>'>
          </div>

          <div class="col col-sm-1 text-center __A__Col_Post">
            <?php if ($thumbnail = get_the_post_thumbnail_url($comment->comment_post_ID)) { ?>
              <img src="<?php echo esc_url(get_the_post_thumbnail_url($comment->comment_post_ID)); ?>" class="__A__Product_Image __A__Product_Image_Com" >
            <?php } ?>
            <div class="__A__Title"><?php echo esc_attr(get_post($comment->comment_post_ID)->post_title); ?></div>
          </div>

          <div class="col col-sm-2  __A__CommentInfo">
            <div class="__A__CommentAuthor"><?php echo esc_html($comment->comment_author)  ?></div>
            <div class=""><?php echo esc_html($comment->comment_author_email)  ?></div>
            <div class="__A__CommentDate"><?php  echo date("d M, Y H:i", strtotime($comment->comment_date))  ?></div>
            <div class="__A__CommentStatus"><?php
            if ("1" === $comment->comment_approved) {
              $status = "success";
            } else {
              $status = "danger";
            }
            ?><span class="badge badge-pill badge-<?php echo esc_attr($status); ?>"><?php echo ("1" === $comment->comment_approved)?esc_html__('APPROVED', 'energyplus'):esc_html__('UNAPPROVED', 'energyplus')?></span></div>
          </div>

          <div class="col-12 col-sm-7 __A__Col_CommentInfo">
            <?php if (0 < $comment->comment_parent) { ?>
              <div class="__A__ThisIsAReply"><?php sprintf(esc_html__('This is a reply to <a href="%1$s" class="trig"></a> Comment #%2$s', "energyplus"), admin_url('comment.php?action=editcomment&c=' . esc_attr($comment->comment_parent), esc_attr($comment->comment_parent)))?></div>
            <?php } ?>

            <?php $stars = intval(get_comment_meta($comment->comment_ID, 'rating', true));?>
            <div class="__A__Stars">
              <span class="__A__StarsUp"><?php echo str_repeat('★ ', $stars); ?></span>
              <span class="__A__StarsDown"><?php echo str_repeat('★ ', 5-$stars); ?></span>
            </div>
            <br>

            <?php echo wp_kses_post($comment->comment_content); ?>
            <?php if (isset($replies[$comment->comment_ID])) { ?>
              <a href="javascript:;" class="__A__Replies" data-id="<?php echo esc_attr($comment->comment_ID); ?>"><span class="dashicons dashicons-format-status __A__Comments_Icon">&nbsp; </span><?php esc_html_e('You replied it &mdash; See', 'energyplus'); ?></a>
              <?php foreach ($replies[$comment->comment_ID] as $reply) { ?>
                <div class="__A__Reply __A__Reply_<?php echo esc_attr($comment->comment_ID); ?>">
                  <div id='item_<?php echo esc_attr( $reply['comment_ID'] ) ?>'>
                    <div class="__A__Reply_Content"><?php echo wp_kses_post($reply['comment_content']) ?></div>
                    <div class="__A__Reply_Author"> &mdash;<br> <?php esc_html_e('Replied by', 'energyplus'); ?> <strong><?php echo esc_html($reply['comment_author']) ?></strong> - <span class="__A__Reply_Date"><?php echo date_i18n("d M, Y H:i", strtotime($reply['comment_date']))?></span></div>
                    <div class="__A__Reply_Actions" >
                      <a href="<?php echo admin_url('comment.php?action=editcomment&c=' . esc_attr($reply['comment_ID'])) ?>" class="trig"><?php esc_html_e('Edit this reply', 'energyplus'); ?></a> -
                      <a href="javascript:;" data-id="<?php echo esc_attr($reply['comment_ID']) ?>" data-do='status' data-state='forcedelete' class="__A__AjaxButton"><?php esc_html_e('Delete this reply forever', 'energyplus'); ?></a>
                    </div>
                  </div>

                </div>
              <?php } ?>
            <?php } ?>
          </div>

          <div class="d-none d-sm-block col col-sm-2 text-right">
            <?php if ('1' === $comment->comment_approved) { ?>
              <a href="javascript:;" data-id="<?php echo esc_attr($comment->comment_ID)?>" data-do='status' data-state='unapprove' class="__A__AjaxButton __A__Button1 __A__MainButton __A__CommentStatusButton __A__NoH __A__CommentStatusButton_Unapprove"><?php esc_html_e('Unapprove', 'energyplus'); ?></a>
            <?php } ?>
            <?php if ('0' === $comment->comment_approved) { ?>
              <a href="javascript:;" data-id="<?php echo esc_attr($comment->comment_ID)?>" data-do='status' data-state='approve' class="__A__AjaxButton __A__Button1 __A__MainButton __A__CommentStatusButton __A__NoH __A__CommentStatusButton_Approve"><?php esc_html_e('Approve', 'energyplus'); ?></a>
            <?php }?>
          </div>

          <div class="col col-sm-1  __A__Actions text-right __A__Display_None">
            <span class="dashicons dashicons-arrow-down-alt2 bthidden1" aria-hidden="true"></span>
            <span class="dashicons dashicons-no-alt bthidden" aria-hidden="true"></span>
          </div>

        </div>
        <div class="collapse col-xs-12 col-sm-12 col-md-12 text-right" id="item_d_<?php echo esc_attr($comment->comment_ID)?>">
          <div class="__A__Item_Details">
            <?php if ('trash' === $comment->comment_approved) { ?>
              <a href="javascript:;" data-id="<?php echo esc_attr($comment->comment_ID)?>" data-do='status' data-state='restore' class="__A__AjaxButton"><?php esc_html_e('Restore', 'energyplus'); ?></a>
              <a href="javascript:;" data-id="<?php echo esc_attr($comment->comment_ID)?>" data-do='status' data-state='forcedelete' class="__A__AjaxButton"><?php esc_html_e('Delete Forever', 'energyplus'); ?></a>
            <?php } ?>

            <?php if ('spam' === $comment->comment_approved) { ?>
              <a href="javascript:;" data-id="<?php echo esc_attr($comment->comment_ID)?>" data-do='status' data-state='restore' class="__A__AjaxButton"><?php esc_html_e('Not Spam', 'energyplus'); ?></a>
              <a href="javascript:;" data-id="<?php echo esc_attr($comment->comment_ID)?>" data-do='status' data-state='forcedelete' class="__A__AjaxButton"><?php esc_html_e('Delete Forever', 'energyplus'); ?></a>
            <?php } ?>

            <?php if ('1' === $comment->comment_approved) { ?>
              <a href="javascript:;" data-id="<?php echo esc_attr($comment->comment_ID)?>" data-do='status' data-state='unapprove' class="d-inline d-md-none  __A__AjaxButton __A__Button1 __A__MainButton __A__CommentStatusButton __A__CommentStatusButton_Red"><?php esc_html_e('Unapprove', 'energyplus'); ?></a>
              <a href="<?php echo EnergyPlus_Helpers::secure_url('comments', esc_attr($comment->comment_ID), array('action' => 'reply', 'id' => esc_attr($comment->comment_ID), 'post' => esc_attr($comment->comment_post_ID))); ?>" class="__A__StopPropagation trig"><?php esc_html_e('Reply', 'energyplus'); ?></a>
              <a href="<?php echo admin_url('comment.php?action=editcomment&c=' . $comment->comment_ID) ?>" class="__A__StopPropagation trig" data-hash="<?php echo esc_attr($comment->comment_ID)?>"><?php esc_html_e('Edit', 'energyplus'); ?></a>
              <a href="javascript:;" data-id="<?php echo esc_attr($comment->comment_ID)?>" data-do='status' data-state='spam' class="__A__HideMe __A__AjaxButton "><?php esc_html_e('Spam', 'energyplus'); ?></a>
              <a href="javascript:;" data-id="<?php echo esc_attr($comment->comment_ID)?>" data-do='status' data-state='trash' class="__A__HideMe __A__AjaxButton text-danger"><?php esc_html_e('Delete', 'energyplus'); ?></a>
            <?php } ?>

            <?php if ('0' === $comment->comment_approved) { ?>
              <a href="javascript:;" data-id="<?php echo esc_attr($comment->comment_ID)?>" data-do='status' data-state='approve' class="d-inline d-md-none __A__AjaxButton __A__Button1 __A__MainButton __A__CommentStatusButton __A__CommentStatusButton_Green"><?php esc_html_e('Approve', 'energyplus'); ?></a>
              <a href="<?php echo EnergyPlus_Helpers::secure_url('comments', $comment->comment_ID, array('action' => 'reply', 'id' => esc_attr($comment->comment_ID), 'post' => esc_attr($comment->comment_post_ID))); ?>" class="__A__StopPropagation  trig"><?php esc_html_e('Reply', 'energyplus'); ?></a>
              <a href="<?php echo admin_url('comment.php?action=editcomment&c=' . $comment->comment_ID) ?>" class="__A__StopPropagation __A__HideMe trig"><?php esc_html_e('Edit', 'energyplus'); ?></a>
              <a href="javascript:;" data-id="<?php echo esc_attr($comment->comment_ID)?>" data-do='status' data-state='spam' class="__A__HideMe __A__AjaxButton"><?php esc_html_e('Spam', 'energyplus'); ?></a>
              <a href="javascript:;" data-id="<?php echo esc_attr($comment->comment_ID)?>" data-do='status' data-state='trash' class="__A__HideMe __A__AjaxButton text-danger"><?php esc_html_e('Delete', 'energyplus'); ?></a>
            <?php } ?>

          </div>
        </div>
      </div>
    <?php } ?>
</div>
  <?php if ($count && $count >0) { ?>
    <?php echo EnergyPlus_View::run('core/pagination', array( 'count' => $count, 'per_page'=> $per_page, 'page' => intval(EnergyPlus_Helpers::get('pg', 0)), 'url' => remove_query_arg('pg', EnergyPlus_Helpers::admin_page('comments', array('status' => EnergyPlus_Helpers::get('status'), 's' => $search  ))) )); ?>
  <?php } ?>
  </div>
<?php } ?>



<?php if (!$ajax) { ?>
</div>
<?php } ?>
