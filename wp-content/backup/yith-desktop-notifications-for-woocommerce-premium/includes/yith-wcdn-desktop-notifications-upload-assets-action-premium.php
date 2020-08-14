<?php
/**
 * Notes class
 *
 * @author  Yithemes
 * @package YITH Desktop Notifications for WooCommerce
 * @version 1.0.0
 */

if(isset($_POST['yith_wcdn_upload_icon'])) {

    $list_of_icons = get_option('yith_wcdn_upload_icon');
    if(!empty($_POST['ad_image'])) {
        $type = getimagesize($_POST['ad_image']);
        if ($type['mime'] == "image/gif" || $type['mime'] == "image/jpeg" || $type['mime'] == "image/png") {
            $list_of_icons[] = esc_url($_POST['ad_image']);
            update_option('yith_wcdn_upload_icon', $list_of_icons);
        }
    }
}

if(isset($_GET['yith_wcdn_delete_icon'])) {
    $list_of_icons = get_option('yith_wcdn_upload_icon');
    if(($key = array_search($_GET['yith_wcdn_delete_icon'], $list_of_icons)) !== false) {
        unset($list_of_icons[$key]);
        update_option('yith_wcdn_upload_icon', $list_of_icons);
    }
}


if(isset($_POST['yith_wcdn_upload_sound'])) {

        $list_of_audios = get_option('yith_wcdn_upload_audio');
        if(!empty($_POST['ad_audio'])) {
            if(in_array(@end(explode(".", $_POST['ad_audio'])), array("mp3"))) {
                $list_of_audios[] = esc_url($_POST['ad_audio']);
                update_option('yith_wcdn_upload_audio', $list_of_audios);
            }
        }
}

if(isset($_GET['yith_wcdn_delete_sound'])) {

    $list_of_audio = get_option('yith_wcdn_upload_audio');
    if(($key = array_search($_GET['yith_wcdn_delete_sound'], $list_of_audio)) !== false) {
        unset($list_of_audio[$key]);
        update_option('yith_wcdn_upload_audio', $list_of_audio);
    }
}