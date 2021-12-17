<?php

/**
 * Description of A2W_ReviewFrontendController
 * 
 * @author MA_GROUP
 * 
 * @autoload: init
 */
if (!class_exists('A2W_ReviewFrontendController')) {

    class A2W_ReviewFrontendController {

        function __construct() {
            add_action('wp_enqueue_scripts', array($this, 'assets'));
            add_filter('comment_text', array($this, 'comment_text'), 10, 2);
            add_filter('get_avatar_url', array($this, 'get_avatar_url'), 1, 3);
        }

        function assets() {
            if(function_exists('is_product') && is_product()) {
                wp_enqueue_style('a2w-review--frontend-style', A2W()->plugin_url() . '/assets/css/review/frontend_style.css', array(), A2W()->version);

                wp_enqueue_script('a2w-review-mousewheel', A2W()->plugin_url() . '/assets/js/review/fancybox/lib/jquery.mousewheel-3.0.6.pack.js', array(), A2W()->version, true);

                wp_enqueue_style('a2w-review-frontend-fancybox', A2W()->plugin_url() . '/assets/css/review/fancybox/jquery.fancybox.css', array(), A2W()->version);

                wp_enqueue_style('a2w-review-fancybox-buttons', A2W()->plugin_url() . '/assets/css/review/fancybox/source/helpers/jquery.fancybox-buttons.css?v=1.0.5', array(), A2W()->version);

                wp_enqueue_style('a2w-review-fancybox-thumbs', A2W()->plugin_url() . '/assets/css/review/fancybox/source/helpers/jquery.fancybox-thumbs.css?v=1.0.7', array(), A2W()->version);

                wp_enqueue_script('a2w-review-frontend-fancybox-pack', A2W()->plugin_url() . '/assets/js/review/fancybox/jquery.fancybox.pack.js', array(), A2W()->version, true);

                wp_enqueue_script('a2w-review-fancybox-buttons-script', A2W()->plugin_url() . '/assets/js/review/fancybox/source/helpers/jquery.fancybox-buttons.js?v=1.0.5', array(), A2W()->version, true);

                wp_enqueue_script('a2w-review-fancybox-media-script', A2W()->plugin_url() . '/assets/js/review/fancybox/source/helpers/jquery.fancybox-media.js?v=1.0.6', array(), A2W()->version, true);

                wp_enqueue_script('a2w-review-fancybox-thumbs-script', A2W()->plugin_url() . '/assets/js/review/fancybox/source/helpers/jquery.fancybox-thumbs.js?v=1.0.7', array(), A2W()->version, true);

                wp_enqueue_script('a2w-review-frontend-script', A2W()->plugin_url() . '/assets/js/review/frontend_script.js', array(), A2W()->version, true);
            }
        }

        function comment_text($comment_text, $comment = null) {

            if (a2w_get_setting('review_show_image_list')) {
                $thumb_width = a2w_get_setting('review_thumb_width') . "px";
                $image_list = A2W_Review::get_comment_photos($comment->comment_ID);

                if ($image_list) {
                    $comment_text .= "<div class='a2w_review_images'>";
                    foreach ($image_list as $img) {
                        if(is_array($img)){
                            $comment_text .= "<a class='fancybox' rel='group{$comment->comment_ID}' href='{$img['image']}'><img width='{$thumb_width}' src='{$img['thumb']}'/></a>";  
                        }else{
                            $comment_text .= "<a class='fancybox' rel='group{$comment->comment_ID}' href='{$img}'><img width='{$thumb_width}' src='{$img}'/></a>";    
                        }
                    }
                    $comment_text .= "</div>";
                }
            }
            return $comment_text;
        }

        function get_avatar_url($url, $id_or_email, $args) {

            if ($id_or_email instanceof WP_Comment) {
                $comment = $id_or_email;
                $comment_id = $comment->comment_ID;
                $image_path = get_comment_meta($comment_id, 'a2w_avatar', true);
                
                $no_avatar_image = a2w_get_setting('review_noavatar_photo', A2W()->plugin_url() . '/assets/img/noavatar.png');
                
                if (empty($image_path) || is_null($image_path)) {
                    $image_path = $no_avatar_image;
                }else{
                    
                    if (is_numeric($image_path)){
                        //todo: maybe check url scheme too?
                        $photo_id = $image_path;
                        $image_path = wp_get_attachment_image_src($photo_id, 'thumbnail');
                        $image_path = $image_path ? $image_path[0] : $no_avatar_image;
                    } else {
                        $image_path = set_url_scheme($image_path, 'https');    
                    }
                
                }

                return $image_path;
            } 
            
            return $url;
        }

    }

}
