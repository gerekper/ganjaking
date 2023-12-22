<?php

namespace NinjaTablesPro\App\Hooks\Handlers;

class ExtraShortCodeHandler
{
    public function register()
    {
        add_shortcode('nt_ratings', array($this, 'ratings'));
        add_shortcode('nt_icon', array($this, 'icon'));
    }

    public function ratings($atts)
    {
        $data = shortcode_atts(array(
            'value'     => 5,
            'color'     => '#ffcc00',
            'alt_color' => '#dddddd',
            'max'       => 5
        ), $atts);
        if ($data['value'] > $data['max']) {
            $data['max'] = $data['value'];
        }
        $reminder = $data['max'] - $data['value'];

        return '<span class="nt_review_icon">' . $this->getIcons($data['value'], 'fooicon-star',
                $data['color']) . $this->getIcons($reminder, 'fooicon-star-o',
                $data['alt_color']) . '<span style="display: none !important;">' . $data['value'] . '</span></span>';
    }

    public function icon($atts)
    {
        $data = shortcode_atts(array(
            'number' => 1,
            'color'  => 'black',
            'icon'   => 'star'
        ), $atts);

        if ( ! $data['icon']) {
            return '';
        }
        $icon = $data['icon'];

        return '<span class="nt_icon">' . $this->getIcons($data['number'], $icon,
                $data['color']) . '<span style="display: none !important;">' . $data['icon'] . '</span></span>';
    }

    private function getIcons($number, $icon_class, $color)
    {

        $html = '';
        $i    = 0;
        // I need to use img tag and src attribute to get the image from the server
        for ($i; $i < $number; $i++) {
            $icon_dir = NINJA_TABLES_DIR_PATH . 'assets/libs/icons/'.$icon_class.'.svg';
            if (file_exists($icon_dir)) {
                $icon_url = NINJA_TABLES_DIR_URL . 'assets/libs/icons/'.$icon_class.'.svg';

                $html .= '<span class="'.$icon_class.'" style="
            display: inline-block;
  width: 20px;
  height: 20px;
  background-color: ' . $color . ';
  -webkit-mask-image: url(' . $icon_url . ') !important;
  mask-image: url(' . $icon_url . ') !important;
"> </span>';
            }
            }

        return $html;
    }
}
