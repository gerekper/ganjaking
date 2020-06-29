<?php

namespace MailOptin\Core\Admin\Customizer\CustomControls;

/**
 * A class to create a dropdown for all google fonts
 */
class WP_Customize_Google_Font_Control extends \WP_Customize_Control
{
    private $count = 40;

    public function __construct($manager, $id, $args = array())
    {
        $this->count = isset($args['count']) ? $args['count'] : $this->count;
        parent::__construct($manager, $id, $args);
    }

    /**
     * Render the content of the category dropdown
     *
     * @return string
     */
    public function render_content()
    {
        $fonts = self::get_fonts($this->count);

        if ( ! empty($fonts)) {
            ?>
            <label>
                <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
                <select <?php $this->link(); ?>>
                    <?php
                    printf('<option value="inherit" %s>%s</option>', selected($this->value(), 'inherit', false), __('Inherit from Theme', 'mailoptin'));

                    foreach ($fonts as $v) {
                        $option_value = str_replace(' ', '+', $v);
                        printf('<option value="%s" %s>%s</option>', $option_value, selected($this->value(), $option_value, false), $v);
                    }
                    ?>
                </select>
                <?php if ( ! empty($this->description)) : ?>
                    <span class="description customize-control-description"><?php echo $this->description; ?></span>
                <?php endif; ?>
            </label>
            <?php
        }
    }

    public static function web_safe_font()
    {
        return \MailOptin\Core\web_safe_font();
    }

    /**
     * Get the google fonts from the API or in the cache
     *
     * @param  integer $amount
     *
     * @return array
     */
    public static function get_fonts($amount = 40)
    {
        $web_safe_font = self::web_safe_font();

        $cache_folder = dirname(__FILE__);

        $fontFile = $cache_folder . '/google-web-fonts.txt';
        //Total time the file will be cached in seconds, set to a month.
        $cachetime = MONTH_IN_SECONDS;
        if (file_exists($fontFile) && $cachetime < filemtime($fontFile)) {
            $content = json_decode(file_get_contents($fontFile));
        } else {
            $googleApi = 'https://www.googleapis.com/webfonts/v1/webfonts?sort=popularity&key=AIzaSyA-iyjXck3LdOsAcGBDBfmlMtXM6jUp1Fk';
            $response  = wp_remote_retrieve_body(wp_remote_get($googleApi, array('sslverify' => false)));
            $fp        = fopen($fontFile, 'w');
            fwrite($fp, $response);
            fclose($fp);
            $content = json_decode($response);
        }
        if ($amount == 'all') {
            $google_fonts = $content->items;
        } else {
            $google_fonts = array_slice($content->items, 0, ($amount - count($web_safe_font)));
        }

        // reduce google font collection to indexed array
        $fonts = array_reduce($google_fonts, function ($carry, $item) {
            $carry[] = $item->family;

            return $carry;
        });

        $custom_font_addition = apply_filters('mo_add_custom_font', []);

        $combined_font = array_merge($web_safe_font, $custom_font_addition, $fonts);

        // sort in alphabetic order.
        natsort($combined_font);

        // combine fonts.
        return $combined_font;
    }
}