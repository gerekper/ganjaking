<?php
/**
 *
 * @package YITH Desktop Notifications for WooCommerce
 * @since   1.0.0
 * @author  Yithemes
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
$dir_icon = YITH_WCDN_PATH.'assets/yith-notifications/notification-icons';
$dir_sound = YITH_WCDN_PATH.'assets/yith-notifications/notification-sounds';

require_once( YITH_WCDN_PATH . 'includes/yith-wcdn-desktop-notifications-upload-assets-action-premium.php' );
?>
<div id="wrap" class="plugin-option yit-admin-panel-container">
      <form method="post" enctype="multipart/form-data">
            <h3><?php echo esc_html__('Notification icon', 'yith-desktop-notifications-for-woocommerce'); ?></h3>
            <!--<p><?php echo esc_html__('Upload','yith-desktop-notifications-for-woocommerce'); ?> (png, jpg): <input type="file" name="yith-wcdn-icon"/></p>-->
            <input id="upload_image_button" class="button" type="button" value="<?php echo esc_html__('Select image','yith-desktop-notifications-for-woocommerce'); ?>" />
            <input id="upload_image" type="text" size="36" name="ad_image" value="" />
            <input type="submit" name="yith_wcdn_upload_icon" value="<?php echo esc_html__('Upload', 'yith-desktop-notifications-for-woocommerce'); ?>" class='button-primary'/>
            <br/><br/>
            <table class='yith_uploads yith_wcdn_image_uploads'>
                <tbody class="yith_tbody_upload">
                <tr class="yith_uploads_table_head">
                    <td><?php echo esc_html__('Icon', 'yith-desktop-notifications-for-woocommerce'); ?></td>
                    <td><?php echo esc_html__('File name', 'yith-desktop-notifications-for-woocommerce'); ?></td>
                    <td><?php echo esc_html__('Delete', 'yith-desktop-notifications-for-woocommerce'); ?></td>
                </tr>
                <?php
                    $list_of_icons = get_option('yith_wcdn_upload_icon');
                    if($list_of_icons !== false){
                        foreach ($list_of_icons as $icons) {
                            echo '<tr>
                                <td class="yith_icon_image"><img src="'.$icons.'" width="50"/> </td>
                                <td>'.basename($icons).'</td>
                                
                                <td>';
                                    echo '<a href="admin.php?page=yith_wcdn_panel_desktop_notifications&amp;tab=upload&amp;yith_wcdn_delete_icon='.$icons.'" class="button button-default">'.esc_html__('delete','yith-desktop-notifications-for-woocommerce').'</a>';
                                echo '</td>
                            </tr>';
                        }
                    }
                ?>
                <?php
                $dh = opendir($dir_icon);
                while (false !== ($filename = readdir($dh))) {
                    if($filename == '.' || $filename == '..') continue;
                    echo '<tr>
                            <td class="yith_icon_image"><img src="'.YITH_WCDN_ASSETS_URL.'/yith-notifications/notification-icons/'.$filename.'" width="50"/> </td>
                            <td>'.$filename.'</td>
                            
                            <td>';
                                echo 'SYS';
                            echo '</td>
                        </tr>';
                }
                ?>
                </tbody>
            </table>
        </form>

    <form method="post" enctype="multipart/form-data">
        <h3><?php echo esc_html__('Sound Notifications', 'yith-desktop-notifications-for-woocommerce'); ?></h3>
        <!--<?php echo esc_html__('Upload', 'yith-desktop-notifications-for-woocommerce'); ?> (mp3): <input type="file" name="yith-wcdn-sound"/><br/>-->
        <input id="upload_audio_button" class="button" type="button" value="<?php esc_html_e('Select audio', 'yith-desktop-notifications-for-woocommerce'); ?>" />
        <input id="upload_audio" type="text" size="36" name="ad_audio" value="" />
        <input type="submit" name="yith_wcdn_upload_sound" value="<?php echo esc_html__('Upload', 'yith-desktop-notifications-for-woocommerce'); ?>" class='button-primary'/>
        <br/><br/>
        <table class='yith_uploads yith_wcdn_sound_uploads'>
            <tbody class='yith_tbody_upload'>
            <tr class="yith_uploads_table_head">
                <td><?php echo esc_html__('File name', 'yith-desktop-notifications-for-woocommerce'); ?></td>
                <td><?php echo esc_html__('Preview', 'yith-desktop-notifications-for-woocommerce'); ?></td>
                <td><?php echo esc_html__('Delete',  'yith-desktop-notifications-for-woocommerce'); ?></td>
            </tr>
            <?php
            $list_of_audios = get_option('yith_wcdn_upload_audio');
            if($list_of_audios !== false){
                foreach ($list_of_audios as $audios) {
                    echo '<tr>
                            <td>'.basename($audios).'</td>
                            <td>
                                <input class="yith_click_audio_preview button button-default" type="button" value="'. esc_html__('Preview','yith-desktop-notifications-for-woocommerce') .'" />
                                <audio id="yith_audio_preview"><source src="'.$audios.'"type="audio/mp3"></audio>
                            </td>
                            <td>';
                                echo '<a href="admin.php?page=yith_wcdn_panel_desktop_notifications&amp;tab=upload&amp;yith_wcdn_delete_sound='.$audios.'" class="button button-default">'.esc_html__('delete','yith-desktop-notifications-for-woocommerce').'</a>';
                            echo '</td>
                        </tr>';
                }
            }
            ?>
            <?php
            $dh = opendir($dir_sound);
            while (false !== ($filename = readdir($dh))) {
                if($filename == '.' || $filename == '..') continue;
                echo '<tr>
                            <td>'.$filename.'</td>
                            <td>
                                <input class="yith_click_audio_preview button button-default" type="button" value="'. esc_html__('Preview','yith-desktop-notifications-for-woocommerce') .'" />
                                <audio id="yith_audio_preview"><source src="'.YITH_WCDN_ASSETS_URL.'/yith-notifications/notification-sounds/'.$filename.'"type="audio/mp3"></audio>
                            </td>
                            <td>';
                if(strstr($filename, '_'))
                    echo '<a href="admin.php?page=yith_wcdn_panel_desktop_notifications&amp;tab=upload&amp;yith_wcdn_delete_sound='.$filename.'" class="button button-default">'.esc_html__('delete','yith-desktop-notifications-for-woocommerce').'</a>';
                else echo 'SYS';
                echo '</td>
                        </tr>';
            }
            ?>
            </tbody>
        </table>
    </form>


    <div id="random_data"></div>
</div>