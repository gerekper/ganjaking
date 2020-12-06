<?php

namespace NinjaTablesPro;

class ExtraShortcodes
{
    public function register()
    {
        add_shortcode('nt_ratings', array($this, 'ratings'));
        add_shortcode('nt_icon', array($this, 'icon'));
    }

    public function ratings($atts)
    {
        $data = shortcode_atts( array(
            'value' => 5,
            'color' => '#ffcc00',
            'alt_color' => '#dddddd',
            'max' => 5
        ), $atts );
        if($data['value'] > $data['max']) {
            $data['max'] = $data['value'];
        }
        $reminder = $data['max'] - $data['value'];
        return '<span class="nt_review_icon">'.$this->getIcons($data['value'], 'fooicon-star', $data['color']).$this->getIcons($reminder, 'fooicon-star-o', $data['alt_color']).'<span style="display: none !important;">'.$data['value'].'</span></span>';
    }

    public function icon($atts)
    {
        $data = shortcode_atts( array(
            'number' => 1,
            'color' => 'black',
            'icon' => 'star'
        ), $atts );

        if(!$data['icon']) {
            return '';
        }
        $icon = 'fooicon-'.$data['icon'];
        return '<span class="nt_icon">'.$this->getIcons($data['number'], $icon, $data['color']).'<span style="display: none !important;">'.$data['icon'].'</span></span>';
    }

    private function getIcons($number, $icon_class, $color)
    {
        $html = '';
        $i = 0;
        for ($i; $i < $number; $i++) {
            $html .= '<i style="color: '.$color.'" class="'.$icon_class.'"></i>';
        }
        return $html;
    }
}