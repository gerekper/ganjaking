<?php

namespace Essential_Addons_Elementor\Pro\Traits;

use \Essential_Addons_Elementor\Classes\Helper as HelperClass;

trait Instagram_Feed
{

    public function render_next_items($url, $instagram_data){
        
    }

    public function instafeed_render_items()
    {
        // check if ajax request
        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'instafeed_load_more') {
            $ajax = wp_doing_ajax();
            // check ajax referer
            check_ajax_referer('essential-addons-elementor', 'security');

            // init vars
            $page = isset($_POST['page']) ? intval($_REQUEST['page'], 10) : 0;
            if (!empty($_POST['post_id'])) {
                $post_id = intval($_POST['post_id'], 10);
            } else {
                $err_msg = __('Post ID is missing', 'essential-addons-elementor');
                if ($ajax) {
                    wp_send_json_error($err_msg);
                }
                return false;
            }

            if (!empty($_POST['widget_id'])) {
                $widget_id = sanitize_text_field($_POST['widget_id']);
            } else {
                $err_msg = __('Widget ID is missing', 'essential-addons-elementor');
                if ($ajax) {
                    wp_send_json_error($err_msg);
                }
                return false;
            }
            $settings = HelperClass::eael_get_widget_settings($post_id, $widget_id);

	        if ( ! empty ( $_POST['settings'] ) ) {
		        parse_str( $_POST['settings'], $new_settings );
		        $settings = wp_parse_args( $new_settings, $settings );
	        }

        } else {
            // init vars
            $page = 0;
            $settings = !empty($settings) ? $settings : $this->get_settings_for_display();
        }

        $key = 'eael_instafeed_'.md5(str_replace('.', '_', $settings['eael_instafeed_access_token']).$settings['eael_instafeed_data_cache_limit']);
        $html = '';

        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'instafeed_load_more') {
            if($instagram_data = get_transient($key)){
                $instagram_data = json_decode($instagram_data, true);
                if ( ($page * $settings['eael_instafeed_image_count']['size'] >= count($instagram_data['data'])) && !empty($instagram_data['paging']['next']) ) {
                    $request_args = array(
                        'timeout' => 60,
                    );
                    $instagram_data_new = wp_remote_retrieve_body(wp_remote_get($instagram_data['paging']['next'],
                        $request_args));
                    $instagram_data_new = json_decode($instagram_data_new, true);
                    if (!empty($instagram_data_new['data'])) {
                        $instagram_data['data'] = array_merge($instagram_data['data'], $instagram_data_new['data']);
                        $new_paging['paging'] = !empty($instagram_data_new['paging']['next']) ? $instagram_data_new['paging']: '';
                        $instagram_data = array_merge($instagram_data, $new_paging);
                        $instagram_data = json_encode($instagram_data);
                        set_transient($key, $instagram_data, 1800);
                    }
                }
            }
        }

        if (get_transient($key) === false) {
            $request_args = array(
                'timeout' => 60,
            );
            $instagram_data = wp_remote_retrieve_body(wp_remote_get('https://graph.instagram.com/me/media/?fields=username,id,caption,media_type,media_url,permalink,thumbnail_url,timestamp&limit=500&access_token=' . $settings['eael_instafeed_access_token'],
                $request_args));
            $data_check = json_decode($instagram_data, true);
            if (!empty($data_check['data'])) {
                set_transient($key, $instagram_data, ($settings['eael_instafeed_data_cache_limit'] * MINUTE_IN_SECONDS));
            }
        } else {
            $instagram_data = get_transient($key);
        }

        $instagram_data = json_decode($instagram_data, true);
        
        if (empty($instagram_data['data'])) {
            return;
        }

        if (empty($settings['eael_instafeed_image_count']['size'])) {
            return;
        }

        switch ($settings['eael_instafeed_sort_by']) {
            case 'most-recent':
                usort($instagram_data['data'], function ($a, $b) {
                    return (int)(strtotime($a['timestamp']) < strtotime($b['timestamp']));
                });
                break;

            case 'least-recent':
                usort($instagram_data['data'], function ($a, $b) {
                    return (int)(strtotime($a['timestamp']) > strtotime($b['timestamp']));
                });
                break;
        }

        if ($items = $instagram_data['data']) {
            $items = array_splice($items, ($page * $settings['eael_instafeed_image_count']['size']),
                $settings['eael_instafeed_image_count']['size']);

            foreach ($items as $item) {
                $img_alt_posted_by = !empty($item['username']) ? $item['username'] : '-';
//                $img_alt_posted_on = !empty($item['timestamp']) ? date('F j, Y, g:i a', $item['timestamp']) : '-';
//                $img_alt_content = __('Photo by', 'essential-addons-elementor') . $img_alt_posted_by . __(' on ', 'essential-addons-elementor') . $img_alt_posted_on;
                $img_alt_content = __('Photo by ', 'essential-addons-elementor') . $img_alt_posted_by;

                if ('yes' === $settings['eael_instafeed_link']) {
                    $target = ($settings['eael_instafeed_link_target']) ? 'target=_blank' : 'target=_self';
                } else {
                    $item['permalink'] = '#';
                    $target = '';
                }

                $image_src = ($item['media_type'] == 'VIDEO') ? $item['thumbnail_url'] : $item['media_url'];
                $caption_length = ( ! empty( $settings['eael_instafeed_caption_length'] ) & $settings['eael_instafeed_caption_length'] > 0 )  ? $settings['eael_instafeed_caption_length'] : 60;
                
                if ($settings['eael_instafeed_layout'] == 'overlay') {
                    $html .= '<a href="' . $item['permalink'] . '" ' . esc_attr($target) . ' class="eael-instafeed-item">
                        <div class="eael-instafeed-item-inner">
                            <img alt="' . $img_alt_content . '" class="eael-instafeed-img" src="' . $image_src . '">

                            <div class="eael-instafeed-caption">
                                <div class="eael-instafeed-caption-inner">';
                    if ($settings['eael_instafeed_overlay_style'] == 'simple' || $settings['eael_instafeed_overlay_style'] == 'standard') {
                        $html .= '<div class="eael-instafeed-icon">
                                            <i class="fab fa-instagram" aria-hidden="true"></i>
                                        </div>';
                    } else {
                        if ($settings['eael_instafeed_overlay_style'] == 'basic') {
                            if ($settings['eael_instafeed_caption'] && !empty($item['caption'])) {
                                $html .= '<p class="eael-instafeed-caption-text">' . substr( $item['caption'], 0, intval( $caption_length ) ) . '...</p>';
                            }
                        }
                    }

                    $html .= '<div class="eael-instafeed-meta">';
                    if ($settings['eael_instafeed_overlay_style'] == 'basic' && $settings['eael_instafeed_date']) {
                        $html .= '<span class="eael-instafeed-post-time"><i class="far fa-clock" aria-hidden="true"></i> ' . date("d M Y",
                            strtotime($item['timestamp'])) . '</span>';
                    }
                    if ($settings['eael_instafeed_overlay_style'] == 'standard') {
                        if ($settings['eael_instafeed_caption'] && !empty($item['caption'])) {
                            $html .= '<p class="eael-instafeed-caption-text">' . substr( $item['caption'], 0, intval( $caption_length ) ) . '...</p>';
                        }
                    }
                    $html .= '</div>';
                    $html .= '</div>
                            </div>
                        </div>
                    </a>';
                } else {

                    $html .= '<div class="eael-instafeed-item">
                        <div class="eael-instafeed-item-inner">
                            <header class="eael-instafeed-item-header clearfix">
                               <div class="eael-instafeed-item-user clearfix">';
                    if ($settings['eael_instafeed_show_profile_image'] == 'yes' && !empty($settings['eael_instafeed_profile_image']['url'])) {
                        $html .= '<a href="//www.instagram.com/' . $item['username'] . '"><img alt="' . $img_alt_content . '" src="' . $settings['eael_instafeed_profile_image']['url'] . '" alt="' . $item['username'] . '" class="eael-instafeed-avatar"></a>';
                    }
                    if ($settings['eael_instafeed_show_username'] == 'yes' && !empty($settings['eael_instafeed_username'])) {
                        $html .= '<a href="//www.instagram.com/' . $item['username'] . '"><p class="eael-instafeed-username">' . $settings['eael_instafeed_username'] . '</p></a>';
                    }

                    $html .= '</div>';
                    $html .= '<span class="eael-instafeed-icon"><i class="fab fa-instagram" aria-hidden="true"></i></span>';

                    if ($settings['eael_instafeed_date'] && $settings['eael_instafeed_card_style'] == 'outer') {
                        $html .= '<span class="eael-instafeed-post-time"><i class="far fa-clock" aria-hidden="true"></i> ' . date("d M Y",
                            strtotime($item['timestamp'])) . '</span>';
                    }
                    $html .= '</header>
                            <a href="' . $item['permalink'] . '" ' . esc_attr($target) . ' class="eael-instafeed-item-content">
                                <img alt="' . $img_alt_content . '" class="eael-instafeed-img" src="' . $image_src . '">';

                    if ($settings['eael_instafeed_card_style'] == 'inner' && $settings['eael_instafeed_caption'] && !empty($item['caption'])) {
                        $html .= '<div class="eael-instafeed-caption">
                                        <div class="eael-instafeed-caption-inner">
                                            <div class="eael-instafeed-meta">
                                                <p class="eael-instafeed-caption-text">' . substr( $item['caption'], 0, intval( $caption_length ) ) . '...</p>
                                            </div>
                                        </div>
                                    </div>';
                    }
                    $html .= '</a>
                            <footer class="eael-instafeed-item-footer">
                                <div class="clearfix">';
                    if ($settings['eael_instafeed_card_style'] == 'inner' && $settings['eael_instafeed_date']) {
                        $html .= '<span class="eael-instafeed-post-time"><i class="far fa-clock" aria-hidden="true"></i> ' . date("d M Y",
                            strtotime($item['timestamp'])) . '</span>';
                    }
                    $html .= '</div>';

                    if ($settings['eael_instafeed_card_style'] == 'outer' && $settings['eael_instafeed_caption'] && !empty($item['caption'])) {
                        $html .= '<p class="eael-instafeed-caption-text">' . substr( $item['caption'], 0, intval( $caption_length ) ) . '...</p>';
                    }
                    $html .= '</footer>
                        </div>
                    </div>';
                }
            }
        }

        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'instafeed_load_more') {
            $data = [
                'num_pages' => ceil(count($instagram_data['data']) / $settings['eael_instafeed_image_count']['size']),
                'html' => $html,
            ];
            while (ob_get_status()) {
                ob_end_clean();
            }
            if (function_exists('gzencode')) {
                $response = gzencode(wp_json_encode($data));
                header('Content-Type: application/json; charset=utf-8');
                header('Content-Encoding: gzip');
                header('Content-Length: ' . strlen($response));

                echo $response;
            } else {
                wp_send_json($data);
            }
            wp_die();

        }

        return $html;
    }
}
