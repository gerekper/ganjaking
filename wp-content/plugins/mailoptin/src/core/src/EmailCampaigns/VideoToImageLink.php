<?php

namespace MailOptin\Core\EmailCampaigns;


class VideoToImageLink
{
    protected $subject;
    protected $output;

    public function __construct($subject)
    {
        $this->subject = $subject;
    }

    public function forge()
    {
        return $this->findYoutube($this->findVimeo($this->subject));
    }

    protected function findYoutube($subject)
    {
        $return = preg_replace_callback(
            [
                '/<iframe.*src="(?:.+)?youtube(?:-nocookie)?.com\/(?:embed|\?)?\/([a-z0-9-_]+).+".+<\/iframe>/i',
                '/[^\"\']https:\/\/(?:www.)?youtube(?:-nocookie)?.com\/\watch\?v=([a-z0-9-_]+)[^\"\']/i'
            ],
            function ($matches) {
                return $this->convertYoutube($matches[1]);
            },
            $subject
        );

        if ( ! $return) return $subject;

        return $return;
    }

    public function findVimeo($subject)
    {
        $return = preg_replace_callback(
            [
                '/<iframe.*src="(?:.+)?player.vimeo.com\/video\/(\d+).+".+<\/iframe>/',
                '/[^\"\']https:\/\/(?:www .)?vimeo.com\/([\d]+)[^\"]/'
            ],
            function ($matches) {
                return $this->convertVimeo($matches[1]);
            },
            $subject
        );

        if ( ! $return) return $subject;

        return $return;
    }

    protected function convertVimeo($id)
    {
        $result = wp_remote_get(sprintf('https://vimeo.com/api/oembed.json?url=https://vimeo.com/%d&width=640', $id));

        $image_url = MAILOPTIN_ASSETS_URL . 'images/video-placeholder.png';

        if (is_wp_error($result)) {
            $response = json_decode(wp_remote_retrieve_body($result), true);

            $thumbnail = isset($response['thumbnail_url_with_play_button']) ? $response['thumbnail_url_with_play_button'] : $response['thumbnail_url'];

            $image_url = $this->url_upload_to_media($thumbnail, basename($response['thumbnail_url']));
        }

        return sprintf('<div style="margin-top:5px;margin-bottom:5px"><a href="https://vimeo.com/%d" target="_blank"><img src="%s"></a></div>', $id, $image_url);
    }

    protected function convertYoutube($id)
    {
        $youtube_play_button_overlay = MAILOPTIN_ASSETS_URL . 'images/youtube-play-button-overlay.png';
        if (defined('W3GUY_LOCAL')) $youtube_play_button_overlay = 'https://cl.ly/2c9f6a11fc29/download/Image%2525202019-03-12%252520at%25252010.56.57%252520AM.png';

        $result = wp_remote_get(sprintf('https://www.youtube.com/oembed?format=json&url=https://youtube.com/watch?v=%s', $id));

        $image_url = MAILOPTIN_ASSETS_URL . 'images/video-placeholder.png';

        if ( ! is_wp_error($result)) {
            $response = json_decode(wp_remote_retrieve_body($result), true);

            if (isset($response['thumbnail_url'])) {
                // you could use https://web-extract.constantcontact.com/v1/thumbnail?url=https://i.ytimg.com/vi/K4ubA4Ucij4/hqdefault.jpg
                // if vimeo start failing tomorrow
                $thumbnail = sprintf('https://i.vimeocdn.com/filter/overlay?src0=%s&src1=%s', $response['thumbnail_url'], $youtube_play_button_overlay);


                $image_url = $this->url_upload_to_media($thumbnail, basename($thumbnail));
            }
        }

        return sprintf('<div style="margin-top:5px;margin-bottom:5px"><a href="https://www.youtube.com/watch?v=%s" target="_blank"><img src="%s"></a></div>', $id, $image_url);
    }

    /**
     * @source https://wordpress.stackexchange.com/a/251512/59917
     *
     * @param $url
     *
     * @return bool|string
     */
    protected function url_upload_to_media($url, $name = '')
    {
        require_once(ABSPATH . 'wp-admin/includes/file.php');

        $timeout_seconds = 5;

        $temp_file = download_url($url, $timeout_seconds);

        if ( ! is_wp_error($temp_file)) {

            // Array based on $_FILE as seen in PHP file uploads
            $file = array(
                'name'     => ! empty($name) ? $name : basename($url), // ex: wp-header-logo.png
                'type'     => 'image/png',
                'tmp_name' => $temp_file,
                'error'    => 0,
                'size'     => filesize($temp_file),
            );

            $overrides = array(
                'test_form' => false,
                'test_size' => true,
            );

            $results = wp_handle_sideload($file, $overrides);

            if ( ! empty($results['error'])) {
                return false;
            }

            return $results['url'];

        }
    }
}