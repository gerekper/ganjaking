<?php
namespace Perfmatters;

class Preload
{
    private static $preloads = array();

    //initialize preload functions
    public static function init() 
    {
        if(!empty(Config::$options['preload']['preload']) || !empty(Config::$options['preload']['critical_images'])) {
            add_action('perfmatters_output_buffer_template_redirect', array('Perfmatters\Preload', 'add_preloads'));
        }
    }

    //add preloads to html
    public static function add_preloads($html) {

        if(!empty(Config::$options['preload']['critical_images'])) {
            self::add_critical_image_preloads($html);
        }

        if(!empty(Config::$options['preload']['preload']) && is_array(Config::$options['preload']['preload'])) {

            $mime_types = array(
                'svg'   => 'image/svg+xml',
                'ttf'   => 'font/ttf',
                'otf'   => 'font/otf',
                'woff'  => 'font/woff',
                'woff2' => 'font/woff2',
                'eot'   => 'application/vnd.ms-fontobject',
                'sfnt'  => 'font/sfnt'
            );

            $preloads = apply_filters('perfmatters_preloads', Config::$options['preload']['preload']);

            foreach($preloads as $line) {

                //device type check
                if(!empty($line['device'])) {
                    $device_type = wp_is_mobile() ? 'mobile' : 'desktop';
                    if($line['device'] != $device_type) {
                        continue;
                    }
                }

                //location check
                if(!empty($line['locations'])) {

                    $location_match = false;

                    $exploded_locations = explode(',', $line['locations']);
                    $trimmed_locations = array_map('trim', $exploded_locations);

                    //single post exclusion
                    if(is_singular()) {
                        global $post;
                        if(in_array($post->ID, $trimmed_locations)) {
                            $location_match = true;
                        }
                    }
                    //posts page exclusion
                    elseif(is_home() && in_array('blog', $trimmed_locations)) {
                        $location_match = true;
                    }
                    elseif(is_archive()) {
                        //woocommerce shop check
                        if(function_exists('is_shop') && is_shop()) {
                            if(in_array(wc_get_page_id('shop'), $trimmed_locations)) {
                                $location_match = true;
                            }
                        }
                    }

                    if(!$location_match) {
                        continue;
                    }
                }

                $mime_type = "";

                if(!empty($line['as']) && $line['as'] == 'font') {
                    $path_info = pathinfo($line['url']);
                    $mime_type = !empty($path_info['extension']) && isset($mime_types[$path_info['extension']]) ? $mime_types[$path_info['extension']] : "";
                }

                //print script/style handle as preload
                if(!empty($line['as']) && in_array($line['as'], array('script', 'style'))) {
                    if(strpos($line['url'], '.') === false) {

                        global $wp_scripts;
                        global $wp_styles;

                        $scripts_arr = $line['as'] == 'script' ? $wp_scripts : $wp_styles;

                        if(!empty($scripts_arr)) {
                            $scripts_arr = $scripts_arr->registered;

                            if(array_key_exists($line['url'], $scripts_arr)) {

                                $url = $scripts_arr[$line['url']]->src;

                                $parsed_url = parse_url($scripts_arr[$line['url']]->src);
                                if(empty($parsed_url['host'])) {
                                    $url = site_url($url);
                                }

                                $ver = $scripts_arr[$line['url']]->ver;

                                if(empty($ver) && preg_match('/wp-includes|wp-admin/i', $url)) {
                                    $ver = get_bloginfo('version');
                                }

                                $line['url'] = $url . (!empty($ver) ? '?ver=' . $ver : '');
                            }
                        }
                    }
                }

                $preload = "<link rel='preload' href='" . $line['url'] . "'" . (!empty($line['as']) ? " as='" . $line['as'] . "'" : "") . (!empty($mime_type) ? " type='" . $mime_type . "'" : "") . (!empty($line['crossorigin']) ? " crossorigin" : "") . (!empty($line['as']) && $line['as'] == 'style' ? " onload=\"this.rel='stylesheet';this.removeAttribute('onload');\"" : "") . ">";

                if($line['as'] == 'image') {
                    array_unshift(self::$preloads, $preload);
                }
                else {
                    self::$preloads[] = $preload;
                }
            }
        }

        if(!empty(self::$preloads)) {
            $preloads_string = "";
            foreach(self::$preloads as $preload) {
                $preloads_string.= $preload;
            }
            $pos = strpos($html, '</title>');
            if($pos !== false) {
                $html = substr_replace($html, '</title>' . $preloads_string, $pos, 8);
            }
        }
        
        return $html;
    }

    //add critical image preloads
    public static function add_critical_image_preloads(&$html) {

        //match all image formats
        preg_match_all('#(<picture.*?)?<img([^>]+?)\/?>(?><\/picture>)?#is', $html, $matches, PREG_SET_ORDER);

        if(!empty($matches)) {

            $count = 0;
            
            foreach($matches as $match) {

                if($count >= Config::$options['preload']['critical_images']) {
                    break;
                }

                if(strpos($match[0], 'secure.gravatar.com') !== false) {
                    continue;
                }

                $exclusions = apply_filters('perfmatters_critical_image_exclusions', array());
                if(!empty($exclusions) && is_array($exclusions)) {
                    foreach($exclusions as $exclusion) {
                        if(strpos($match[0], $exclusion) !== false) {
                            continue 2;
                        }
                    }
                }

                //picture tag
                if(!empty($match[1])) {
                    preg_match('#<source([^>]+?image\/webp[^>]+?)\/?>#is', $match[0], $source);

                    if(!empty($source)) {
                        self::generate_critical_image_preload($source[1]);
                        $new_picture = str_replace('<picture', '<picture data-perfmatters-preload', $match[0]);
                        $new_picture = str_replace('<img', '<img data-perfmatters-preload', $new_picture);
                        $html = str_replace($match[0], $new_picture, $html);
                        $count++;
                        continue;
                    }
                }

                //img tag
                if(!empty($match[2])) {
                    self::generate_critical_image_preload($match[2]);
                    $new_image = str_replace('<img', '<img data-perfmatters-preload', $match[0]);
                    $html = str_replace($match[0], $new_image, $html);
                    $count++;
                }
            }
        }

        if(!empty(self::$preloads)) {
            ksort(self::$preloads);
        }
    }

    //generate preload link from att string
    private static function generate_critical_image_preload($att_string) {
        if(!empty($att_string)) {
            $atts = perfmatters_lazyload_get_atts_array($att_string);
            $src = $atts['data-src'] ?? $atts['src'] ?? '';
            self::$preloads[] = '<link rel="preload" href="' . $src . '" as="image"' . (!empty($atts['srcset']) ? ' imagesrcset="' . $atts['srcset'] . '"' : '') . (!empty($atts['sizes']) ? ' imagesizes="' . $atts['sizes'] . '"' : '') . ' />';
        }
    }
}