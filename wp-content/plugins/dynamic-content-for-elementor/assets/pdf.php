<?php

namespace DynamicOOOS;

/** Loads the WordPress Environment and Template */
\define('WP_USE_THEMES', \false);
require '../../../../wp-blog-header.php';
if (\version_compare(\phpversion(), '7.2', '<')) {
    echo \printf(__('PDF requires PHP %1$s or greater to works', 'dynamic-content-for-elementor'), '7.2');
    return;
}
$template_id = 0;
if (!empty($_GET['post_id'])) {
    $id = \intval($_GET['post_id']);
} else {
    $id = 0;
}
if (!empty($_GET['element_id'])) {
    $element_id = sanitize_text_field($_GET['element_id']);
} else {
    $element_id = 0;
}
if (!empty($_GET['title'])) {
    $pdf_title = sanitize_text_field($_GET['title']) . '.pdf';
} else {
    $pdf_title = \time() . '.pdf';
}
if ($element_id && $id) {
    status_header(200);
    global $wp_query, $post;
    $wp_query->is_page = $wp_query->is_singular = \true;
    $wp_query->is_404 = \false;
    $post = get_post($id);
    $wp_query->queried_object = $post;
    $wp_query->queried_object_id = $id;
    $element = \DynamicContentForElementor\Helper::get_elementor_element_by_id($element_id);
    $settings = $element->get_settings_for_display();
    // defaults
    $container = 'body';
    $size = 'a4';
    $dpi = '96';
    $orientation = 'portrait';
    $margin = 0;
    $dest = 'I';
    $styles = 'only-elementor';
    $converter = 'dompdf';
    if ($settings['dce_pdf_button_body'] === 'template') {
        $template_id = $settings['dce_pdf_button_template'];
    } else {
        $container = $settings['dce_pdf_button_container'];
    }
    $styles = $settings['dce_pdf_button_styles'];
    $rtl = $settings['dce_pdf_rtl'];
    $pdf_title = $settings['dce_pdf_button_title'];
    $size = $settings['dce_pdf_button_size'];
    $orientation = $settings['dce_pdf_button_orientation'];
    if ($rtl === 'yes') {
        $converter = $settings['dce_pdf_rtl_button_converter'];
    } else {
        $converter = $settings['dce_pdf_button_converter'];
    }
    if ('dompdf' === $converter && isset($settings['dce_pdf_button_dpi'])) {
        $dpi = $settings['dce_pdf_button_dpi'];
    }
    if (isset($settings['dce_pdf_button_margin']['top']) && $settings['dce_pdf_button_margin']['top'] !== '') {
        $margin = $settings['dce_pdf_button_margin']['top'] . $settings['dce_pdf_button_margin']['unit'] . ' ' . $settings['dce_pdf_button_margin']['right'] . $settings['dce_pdf_button_margin']['unit'] . ' ' . $settings['dce_pdf_button_margin']['bottom'] . $settings['dce_pdf_button_margin']['unit'] . ' ' . $settings['dce_pdf_button_margin']['left'] . $settings['dce_pdf_button_margin']['unit'];
    }
    if ($settings['download']) {
        $dest = 'F';
    }
    if ($template_id || $id) {
        if (!empty($_GET['user_id'])) {
            $user_id = \intval($_GET['user_id']);
        } else {
            $user_id = get_current_user_id();
        }
        if ($template_id) {
            $set_post = '';
            $set_author = $set_post;
            if ($id) {
                $set_post = ' post_id="' . $id . '"';
            }
            $pdf_shortcode = '[dce-elementor-template id="' . $template_id . '"' . $set_author . $set_post . ']';
            $pdf_html = do_shortcode($pdf_shortcode);
        } else {
            $cookies = array();
            foreach ($_COOKIE as $name => $value) {
                $cookies[] = new \WP_Http_Cookie(array('name' => $name, 'value' => $value));
            }
            $response = wp_remote_get(get_permalink($id), array('cookies' => $cookies));
            $page_body = wp_remote_retrieve_body($response);
            // may not work for internal calls
            if ($page_body) {
                // full page body
                $tmp = \explode('<body', $page_body);
                $tmp = \explode('>', \end($tmp), 2);
                $tmp = \explode('</body>', \end($tmp));
                $page_body = \reset($tmp);
            } else {
                // fallback to elementor content
                $page_body = \Elementor\Plugin::$instance->frontend->get_builder_content($id);
                $page_body = '<html><body>' . $page_body . '</body></html>';
            }
            $pdf_html = $page_body;
        }
        $pdf_html = \DynamicContentForElementor\Helper::get_dynamic_value($pdf_html);
        if ($styles !== 'unstyled') {
            // add CSS
            $css_id = $template_id ? $template_id : $id;
            $css = \DynamicContentForElementor\Helper::get_post_css($css_id, $styles === 'all');
            // from flex to table
            $css .= '.elementor-section .elementor-container { display: table !important; width: 100% !important; }';
            $css .= '.elementor-row { display: table-row !important; }';
            $css .= '.elementor-column { display: table-cell !important; }';
            $css .= '.elementor-column-wrap, .elementor-widget-wrap { display: block !important; }';
            $css = \str_replace(':not(.elementor-motion-effects-element-type-background) > .elementor-element-populated', ':not(.elementor-motion-effects-element-type-background)', $css);
            $css .= '.elementor-column .elementor-widget-image img { max-width: none !important; }';
            $pdf_html_precss = $pdf_html;
            if ($pdf_html_precss) {
                $cssToInlineStyles = new \DynamicOOOS\TijsVerkoyen\CssToInlineStyles\CssToInlineStyles();
                $pdf_html = $cssToInlineStyles->convert($pdf_html, $css);
            }
            if (!$pdf_html) {
                $pdf_html = $pdf_html_precss;
            }
        }
        if (!$template_id && $pdf_html) {
            $crawler = new \DynamicOOOS\Symfony\Component\DomCrawler\Crawler($pdf_html);
            // Remove download PDF BUTTON
            $crawler->filter('.elementor-widget-dce_pdf_button')->each(function (\DynamicOOOS\Symfony\Component\DomCrawler\Crawler $crawler) {
                foreach ($crawler as $node) {
                    $node->parentNode->removeChild($node);
                }
            });
            $pdf_html = $crawler->html();
            $dom = new \DynamicOOOS\PHPHtmlParser\Dom();
            $dom->loadStr($pdf_html);
            $dom_elements = $dom->find($container);
            $tmp = '';
            $contains_body = \false;
            if (!empty($dom_elements)) {
                foreach ($dom_elements as $a_elem) {
                    if ($container === 'body') {
                        $tmp .= $a_elem->innerHtml;
                        $contains_body = \true;
                    } else {
                        $tmp .= $a_elem->outerHtml;
                    }
                }
            }
            $pdf_html = $tmp;
        }
        if (!$pdf_html) {
            echo __('Content NOT found, please check selector or template', 'dynamic-content-for-elementor');
            die;
        }
        if ($margin) {
            $pdf_html .= '<style>@page { margin: ' . $margin . '; }</style>';
        }
        if (!$contains_body) {
            $pdf_html = \DynamicContentForElementor\Helper::template_unwrap($pdf_html);
        }
        switch ($converter) {
            case 'dompdf':
                $context = \stream_context_create(array('ssl' => array('verify_peer' => \false, 'verify_peer_name' => \false)));
                $options = new \DynamicOOOS\Dompdf\Options();
                $options->set('isRemoteEnabled', \true);
                $options->setIsRemoteEnabled(\true);
                // instantiate and use the dompdf class
                $dompdf = new \DynamicOOOS\Dompdf\Dompdf($options);
                $dompdf->setHttpContext($context);
                $dompdf->loadHtml($pdf_html);
                $dompdf->set_option('isRemoteEnabled', \true);
                $dompdf->set_option('isHtml5ParserEnabled', \true);
                $dompdf->set_option('dpi', $dpi);
                // (Optional) Setup the paper size and orientation
                $dompdf->setPaper($size, $orientation);
                // Render the HTML as PDF
                $dompdf->render();
                // Output the generated PDF to Browser
                \header('HTTP/1.1 200 OK');
                \header('Content-type:application/pdf');
                \header("Content-Disposition:attachment;filename='" . $pdf_title . "'");
                $dompdf->stream($pdf_title);
                break;
            case 'tcpdf':
                // Link image from url to path
                $site_url = site_url();
                $upload = wp_upload_dir();
                // Replace URL with directory path to avoid a http call
                $pdf_html = \str_replace('src="' . $site_url . '/wp-content/uploads', 'src="' . $upload['basedir'], $pdf_html);
                $pdf_html = \str_replace('src="' . $site_url, 'src="' . $upload['basedir'], $pdf_html);
                // from div to table
                $pdf_html = \DynamicContentForElementor\Helper::tablefy($pdf_html);
                $pdf_html .= '<style>table{ page-break-inside: auto; }</style>';
                // create new PDF document
                $orientation = $orientation === 'portrait' ? 'P' : 'L';
                $pdf = new \DynamicOOOS\TCPDF($orientation, 'px', \strtoupper($size), \true, 'UTF-8', \false);
                // set document information
                if ($rtl === 'yes') {
                    $pdf->setRTL(\true);
                    // set Right to left
                }
                $pdf->SetAuthor(get_bloginfo('name'));
                $pdf->SetTitle($pdf_title);
                $pdf->SetPrintHeader(\false);
                $pdf->SetPrintFooter(\false);
                // set margins
                $pdf->SetMargins(\PDF_MARGIN_LEFT, \PDF_MARGIN_TOP, \PDF_MARGIN_RIGHT);
                $pdf->SetHeaderMargin(\PDF_MARGIN_HEADER);
                $pdf->SetFooterMargin(\PDF_MARGIN_FOOTER);
                // set auto page breaks
                $pdf->SetAutoPageBreak(\true, \PDF_MARGIN_BOTTOM);
                // set image scale factor
                $pdf->setImageScale(\PDF_IMAGE_SCALE_RATIO);
                // add a page
                $pdf->AddPage();
                // output the HTML content
                $tagvs = array('img' => array(array('h' => 0, 'n' => 0), array('h' => 0, 'n' => 0)), 'picture' => array(array('h' => 0, 'n' => 0), array('h' => 0, 'n' => 0)), 'section' => array(array('h' => 0, 'n' => 0), array('h' => 0, 'n' => 0)), 'div' => array(array('h' => 0, 'n' => 0), array('h' => 0, 'n' => 0)), 'p' => array(array('h' => 0, 'n' => 0), array('h' => 0, 'n' => 0)), 'h1' => array(array('h' => 0, 'n' => 0), array('h' => 0, 'n' => 0)), 'h2' => array(array('h' => 0, 'n' => 0), array('h' => 0, 'n' => 0)), 'h3' => array(array('h' => 0, 'n' => 0), array('h' => 0, 'n' => 0)), 'h4' => array(array('h' => 0, 'n' => 0), array('h' => 0, 'n' => 0)), 'h5' => array(array('h' => 0, 'n' => 0), array('h' => 0, 'n' => 0)), 'h6' => array(array('h' => 0, 'n' => 0), array('h' => 0, 'n' => 0)), 'ul' => array(array('h' => 0, 'n' => 0), array('h' => 0, 'n' => 0)), 'table' => array(array('h' => 0, 'n' => 0), array('h' => 0, 'n' => 0)), 'tr' => array(array('h' => 0, 'n' => 0), array('h' => 0, 'n' => 0)), 'td' => array(array('h' => 0, 'n' => 0), array('h' => 0, 'n' => 0)), 'th' => array(array('h' => 0, 'n' => 0), array('h' => 0, 'n' => 0)));
                $pdf->setHtmlVSpace($tagvs);
                $pdf->writeHTML($pdf_html, \true, \false, \true, \false, '');
                // Reset pointer to the last page
                $pdf->lastPage();
                // Close and output PDF document
                $pdf->Output($pdf_title . '.pdf', $dest);
                break;
        }
    }
    die;
}
echo \strtoupper(__('Error', 'dynamic-content-for-elementor'));
