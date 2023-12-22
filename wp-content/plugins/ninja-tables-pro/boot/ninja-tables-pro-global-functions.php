<?php

function nt_parse_image_column($data, $column)
{
    if ( ! $data) {
        return '';
    }
    if ( ! empty($column['original'])) {
        $column = $column['original'];
    }

    $linkType = \NinjaTables\Framework\Support\Arr::get($column, 'link_type');
    if (empty($data['image_thumb'])) {
        return '';
    }
    $suffix = '';
    $prefix = '';
    if ($linkType == 'hyperlinked') {
        if ( ! empty($data['permalink'])) {
            $atts = '';
            if (isset($column['link_target'])) {
                $atts = 'target="' . $column['link_target'] . '"';
            }
            $prefix = '<a ' . $atts . ' href="' . $data['permalink'] . '">';
            $suffix = '</a>';
        }
    } elseif ($linkType == 'image_light_box') {
        if ($data['image_full']) {
            $prefix = '<a class="nt_lightbox" href="' . $data['image_full'] . '">';
            $suffix = '</a>';
        }

    } elseif ($linkType == 'iframe_ligtbox') {
        if ( ! empty($data['permalink'])) {
            $prefix = '<a class="nt_lightbox" href="' . $data['permalink'] . '">';
            $suffix = '</a>';
        }

    } else {

    }

    $altText = \NinjaTables\Framework\Support\Arr::get($data, 'alt_text');

    return $prefix . '<img alt="' . $altText . '" class="nt_image_type_thumb" src="' . $data['image_thumb'] . '"/>' . $suffix;
}

function nt_parse_button_column($url, $column)
{
    if ( ! empty($column['original'])) {
        $column = $column['original'];
    }
    if ( ! $url) {
        return '';
    }
    $atts = '';
    if (isset($column['link_target'])) {
        $atts = 'target="' . $column['link_target'] . '"';
    }

    $extraClass = '';
    if (isset($column['btn_extra_class']) && $column['btn_extra_class']) {
        $extraClass = $column['btn_extra_class'];
    }

    $styles = '';
    if (isset($column['btn_text_color']) && $column['btn_text_color']) {
        $styles .= 'color: ' . $column['btn_text_color'] . ';';
    }

    if (isset($column['btn_bg_color']) && $column['btn_bg_color']) {
        $styles .= 'background-color: ' . $column['btn_bg_color'] . ';';
    }

    if (isset($column['btn_border_color']) && $column['btn_border_color']) {
        $styles .= 'border-color: ' . $column['btn_border_color'] . ';';
    }

    $btnText = '';
    if (isset($column['button_text'])) {
        $btnText = $column['button_text'];
    }
    $relAttributes = '';
    if (isset($column['relAttributes'])) {
        $relAttributes = implode(" ", $column['relAttributes']);
    }
    $forceDownload = '';
    if (isset($column['force_download']) && $column['force_download']) {
        $forceDownload = 'download';
        $siteUrl       = site_url();
        if (strpos($url, $siteUrl) === false) {
            $extraClass .= ' nt_force_download';
        }
    }

    return '<a ' . $atts . ' ' . $forceDownload . ' class="nt_btn ' . $extraClass . '" style="' . $styles . '" rel="' . $relAttributes . '" href="' . $url . '">' . $btnText . '</a>';
}
