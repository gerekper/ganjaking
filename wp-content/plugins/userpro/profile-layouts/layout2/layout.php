<?php
global $userpro_social;
global $userpro;
$background_pic = userpro_profile_data('custom_profile_bg', $user_id);
if (empty($background_pic)) {
    $background_pic = userpro_url . 'profile-layouts/layout' . $layout . '/images/cover.png';
}
?>

<div class="container" id="main_section">

    <div class="row upl-overlay">

        <div class="col-lg-12 col-xs-12" id="upl-header-bg"
             style="background-image:url(<?php echo $background_pic; ?>);">
            <div class="upl-right-button">
                <?php if (userpro_can_edit_user($user_id) || userpro_get_edit_userrole()) { ?>
                    <a href="<?php echo $userpro->permalink($user_id, 'edit') ?>" class="up-edit userpro-tip"
                       original-title="<?php _e('Edit', 'userpro'); ?>"><i class="userpro-icon-edit"></i></a>
                <?php } ?>
            </div>
            <div class="upl-profilepic">
                <?php if (userpro_get_option('lightbox') && userpro_get_option('profile_lightbox')) { ?>
                    <div class="userpro-profile-img img-circle" data-key="profilepicture"><a
                                href="<?php echo $userpro->profile_photo_url($user_id); ?>"
                                class="userpro-tip-fade lightview"
                                data-lightview-caption="<?php echo $userpro->profile_photo_title($user_id); ?>"
                                title="<?php _e('View member photo', 'userpro'); ?>"><?php echo get_avatar($user_id,
                                320); ?></a></div>
                <?php } ?>
            </div>
            <div class='upl-badges'>
                <?php
                if (userpro_get_option('show_badges_profile') == '1') {
                    echo userpro_show_badges($user_id);
                } else {

                    if (userpro_show_badges($user_id) != '<div class="userpro-badges"></div>') { ?>

                        <span class="badges"></span>
                        <i onclick="userpro_show_user_badges(<?php echo $user_id; ?>);"
                           class="fa fa-arrow-circle-right display_badges"></i>
                    <?php }
                }
                do_action('userpro_after_profile_img', $user_id);
                ?>
            </div>
        </div>
        <!--column ends-->
        <div class="col-lg-12 col-xs-12 bottom-stroke"></div>


    </div><!--row1 ends-->


</div>
<?php if (userpro_get_option('modstate_social')) { ?>
    <div class="row upl-count-bg">

        <div class="col-lg-4 col-lg-offset-4 col-sm-4 col-xs-4 connect">

            <span class="upl-number-conn"><?php echo str_ireplace('following', '',
                    $userpro_social->following_count($user_id)); ?></span><br>
            <a href="<?php echo $userpro->permalink($user_id, 'following', 'userpro_sc_pages'); ?>"
               class="userpro-count-link" style="color:white !important"><?php _e('FOLLOWING', 'userpro') ?></a>
        </div>

        <div class="col-lg-4 col-lg-offset-4 col-sm-4 col-xs-4 connect">

            <span class="upl-number-conn"><?php echo str_ireplace('followers', '',
                    $userpro_social->followers_count($user_id)); ?></span><br>

            <a href="<?php echo $userpro->permalink($user_id, 'followers', 'userpro_sc_pages'); ?>"
               class="userpro-count-link" style="color:white !important"><?php _e('FOLLOWERS', 'userpro') ?></a>
        </div>
        <div class="col-lg-4 col-lg-offset-4 col-sm-4 col-xs-4 connect">
            <span class="upl-number-conn"><?php echo str_ireplace('connections', '',
                    $userpro->connetions_count($user_id)); ?></span><br>
            <!--                         <a href="< ?php echo $userpro->permalink($user_id, 'connection', 'userpro_sc_pages'); ?>" class="userpro-count-link" style="color:white !important">< ?php _e('CONNECTIONS','userpro');?></a>-->
            <a href="<?php echo $userpro->permalink($user_id, 'connections', 'userpro_connections'); ?>"
               class="userpro-count-link" style="color:white !important"><?php _e('CONNECTIONS', 'userpro'); ?></a>
        </div>
    </div>
<?php } ?>
<div class="clearfix"></div>

<div class="row" style="margin-top: 4%">

    <div class="col-lg-4"></div>

    <div class="col-lg-4 col-sm-8 col-xs-12 profile-name">
        <h3><a href="<?php echo $userpro->permalink($user_id); ?>"><?php echo userpro_profile_data('display_name',
                    $user_id); ?></a></h3>
        <hr class="h_line">

    </div>

    <div class="col-lg-4 col-sm-4 text-center text-right">
        <?php
        if (!userpro_get_option('modstate_social')) {
            echo upl_follow_text($user_id, get_current_user_id());
        } ?>
    </div><!--follow-button-->
    <div class="col-lg-4 col-sm-4 text-center text-right">
        <?php do_action('userpro_social_buttons', $user_id); ?>
    </div>


</div><!--row-->


<div class="row">

    <div class="col-lg-4"></div>

    <div class="col-lg-4 col-xs-12 view-reviews">
        <?php
        $activated_plugins = apply_filters('active_plugins', get_option('active_plugins'));
        ?>
        <?php if (in_array('userpro-rating/user-pro_rating.php', $activated_plugins)) {

            $page_id = get_option('userpro_review_page_link');
            if ($page_id) {
                $link = get_review_page_link($user_id);
                echo '<img src="' . userpro_url . 'profile-layouts/layout' . $layout . '/images/review.png" /><a href="' . $link . '">View Reviews</a>';
            }

            ?>


            <!--                       <img src="< ?php echo userpro_url.'profile-layouts/layout'.$layout.'/images/review.png' ?>" /><a href="<?php echo $userpro->permalink($user_id,
                'rating',
                'userpro_rating'); ?>" class="userpro-count-link" ><span>View Reviews</span></a> <a href="<?php echo $userpro->permalink($user_id,
                'rating', 'userpro_sc_pages'); ?>" class="userpro-count-link" ><span>View Reviews</span></a>-->
        <?php } ?>

    </div>


</div><!--row-->

<div class="clearfix"></div>
<?php
$res = '';
foreach (userpro_fields_group_by_template('social', $args["social_group"]) as $key => $array) {
    $icon = $userpro->field_icon($key);
    if (userpro_profile_data($key, $user_id) && userpro_field_is_viewable($key, $user_id, $args) && $icon) {
        if ($key == "user_email") {
            continue;
        };
        $res .= '<a href="' . userpro_link_filter(userpro_profile_data($key, $user_id),
                $key) . '" class="userpro-profile-icon-upl userpro-tip" title="' . $array['label'] . '" target="' . $args['social_target'] . '" ><img src="' . userpro_url . 'profile-layouts/layout' . $layout . '/images/' . $key . '_icon.png" /></a>';
    }
}

?>
<?php if (!empty($res)) { ?>
    <div class="row">

        <div class="col-lg-4 col-sm-4 text-center social-profile text-social">
            SOCIAL PROFILES :
            <hr class="h_line_title">
        </div>


        <div class="col-lg-5 col-sm-8 text-left social-mediaic">
            <div class="social-media">
                <?php echo $res; ?>
            </div>
        </div>


    </div><!--row-->
<?php } ?>
<div class="row">

    <div class="col-lg-4 col-sm-4 text-center  social-profile text-social">
        PERSONAL INFO
        <hr class="h_line_title">
    </div>


    <div class="col-lg-5 col-sm-7 text-left social-form">
        <div class="arrow-left"></div>
        <form class="form-horizontal" role="form">
            <?php // Hook into fields $args, $user_id
            if (!isset($user_id)) {
                $user_id = 0;
            }
            $hook_args = array_merge($args, array('user_id' => $user_id, 'unique_id' => $i));
            ?>

            <?php foreach (userpro_fields_group_by_template($template,
                $args["{$template}_group"]) as $key => $array) { ?>

                <?php if ($array) echo userpro_show_field($key, $array, $i, $args, $layout, $user_id) ?>

            <?php }
            //do_action('userpro_after_fields', $hook_args);
            ?>
        </form> <!-- /form -->


    </div>


</div><!--row-->  <!--personal-info-->
<?php
$up_media = array();
if (in_array('userpro-mediamanager/index.php', $activated_plugins)) {
    ?>
    <div class="row">

        <div class="col-lg-4 col-sm-6  text-center  social-profile text-social">
            MY UPLOADS
            <hr class="h_line_title">
        </div>


        <div class="col-lg-5 col-sm-7 text-left gallery-form">
            <?php //endif;
            do_shortcode('[media_manager media = "view" user_id="' . $user_id . '"]');
            $up_media = get_option('userpro_media_gallery');
            $upm_flag = false;
            if (!empty($up_media)) {
                foreach ($up_media as $up_inner_media) {
                    if ($up_inner_media['user_id'] == $user_id) {
                        $upm_flag = true;
                    }
                }
            }
            if (empty($upm_flag)) {
                echo 'No media Available';
            }
            ?>

        </div>
        <div class="clearfix"></div>


    </div><!--main_section ends-->
<?php } ?>
<div class="row">
    <div class="footer"></div>
</div><!--footer-->


<?php
function upl_follow_text($to, $from)
{
    $body = '';
    $caption = '';
    $link = '';
    $name = '';
    $description = '';
    if ($to != $from && userpro_is_logged_in()) {
        /** Facebook Auto Post Bring Back , Added By Rahul */
        if (userpro_get_option('facebook_follow_autopost')) {
            if (userpro_get_option('facebook_follow_autopost_name')) {
                $name = userpro_get_option('facebook_follow_autopost_name');  // post title
            } else {
                $name = '';
            }
            if (userpro_get_option('facebook_follow_autopost_body')) {
                $body = userpro_get_option('facebook_follow_autopost_body'); // post body
            } else {
                $body = '';
            }
            if (userpro_get_option('facebook_follow_autopost_caption')) {
                $caption = userpro_get_option('facebook_follow_autopost_caption'); // caption, url, etc.
            } else {
                $caption = '';
            }
            if (userpro_get_option('facebook_follow_autopost_description')) {
                $description = userpro_get_option('facebook_follow_autopost_description'); // full description
            } else {
                $description = '';
            }
            if (userpro_get_option('facebook_follow_autopost_link')) {
                $link = userpro_get_option('facebook_follow_autopost_link'); // link
            } else {
                $link = '';
            }
        }
        $iamfollowing = get_user_meta($from, '_userpro_following_ids', true);
        if (isset($iamfollowing[$to])) {
            return '<div class="upl_follow"><a href="#" class="userpro-button userpro-follow following" data-follow-text="' . __('Follow',
                    'userpro') . '" data-unfollow-text="' . __('Unfollow',
                    'userpro') . '" data-following-text="' . __('Following',
                    'userpro') . '" data-follow-to="' . $to . '">' . __('Following',
                    'userpro') . '</a></div>';
        } else {
            return '<div class="upl_follow"><a href="#" class="userpro-button secondary userpro-follow notfollowing" data-follow-text="' . __('Follow',
                    'userpro') . '" data-unfollow-text="' . __('Unfollow',
                    'userpro') . '" data-following-text="' . __('Following',
                    'userpro') . '" data-follow-to="' . $to . '" 
                    id="fb-post-data" data-fbappid="' . userpro_get_option('facebook_app_id') . '" data-message="' . $body . '" data-caption="' . $caption . '" data-link="' . $link . '" data-name="' . $name . '" data-description="' . $description . '" ><i class="userpro-icon-share"></i>' . __('Follow',
                    'userpro') . '</a></div>';
        }
    }
}

?>
