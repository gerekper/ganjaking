<?php

/**
 * Description of A2W_AttachmentController
 *
 * @author Andrey
 * 
 * @autoload: a2w_init
 * 
 * @ajax: true
 * 
 * @cron: false
 */
if (!class_exists('A2W_AttachmentController')) {

    class A2W_AttachmentController {

        public function __construct() {
            add_filter('wp_get_attachment_url', array($this, 'get_attachment_url'), 1000, 2);
            add_filter('wp_calculate_image_srcset', array($this, 'calculate_image_srcset'), 1000, 5);
        }

        function get_attachment_url($url, $id) {
            // if not a2w attached return to default function
            if (!get_post_meta($id, '_wp_a2w_attached_file', true)) {
                return $url;
            }

            if (!$post = get_post((int) $id)) {
                return false;
            }

            if ('attachment' != $post->post_type) {
                return false;
            }

            $new_url = '';
            if ($file = get_post_meta($post->ID, '_wp_attached_file', true)) {
                if (substr($file, 0, 7) === "http://" || substr($file, 0, 8) === "https://") {
                    $new_url = $file;
                }
            }

            if (empty($new_url)) {
                return false;
            }

            return $new_url;
        }

        function calculate_image_srcset($sources, $size_array, $image_src, $image_meta, $attachment_id) {
            // if not a2w attached return to default function
            if (!$sources || !get_post_meta($attachment_id, '_wp_a2w_attached_file', true)) {
                return $sources;
            }
            
            $upload_dir = wp_get_upload_dir();
            $image_baseurl = trailingslashit($upload_dir['baseurl']);
            
            if (is_ssl() && 'https' !== substr($image_baseurl, 0, 5) && parse_url($image_baseurl, PHP_URL_HOST) === $_SERVER['HTTP_HOST']) {
                //TODO in some case, change HTTP to HTTPS not working
                $image_baseurl = set_url_scheme($image_baseurl, 'https');
            }

            foreach ($sources as &$src) {
                $src['url'] = str_replace($image_baseurl, '', $src['url']);
            }

            return $sources;
        }

    }

}
