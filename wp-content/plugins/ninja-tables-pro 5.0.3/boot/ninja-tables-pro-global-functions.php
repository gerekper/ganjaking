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
        $url = '?nt_download_file='.$url;
    }

    if ($forceDownload && isset($_GET['nt_download_file']) && !empty($_GET['nt_download_file'])) {
        $url = sanitize_url($_GET['nt_download_file']);
        nt_make_force_download($url);
    }

    return '<a ' . $atts . ' ' . $forceDownload . ' class="nt_btn ' . $extraClass . '" style="' . $styles . '" rel="' . $relAttributes . '" href="' . $url . '">' . $btnText . '</a>';
}

function nt_make_force_download($fileUrl)
{
    $fileName = basename($fileUrl);
    header('Content-Type: application/octet-stream');
    header("Content-Transfer-Encoding: Binary");
    header("Content-disposition: attachment; filename=\"".$fileName."\"");
    readfile($fileUrl);
    exit;
}
