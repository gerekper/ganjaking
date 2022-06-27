<?php
namespace Perfmatters;

use Sabberworm\CSS\CSSList\AtRuleBlockList;
use Sabberworm\CSS\CSSList\CSSBlockList;
use Sabberworm\CSS\CSSList\Document;
use Sabberworm\CSS\OutputFormat;
use Sabberworm\CSS\Parser as CSSParser;
use Sabberworm\CSS\Property\Charset;
use Sabberworm\CSS\RuleSet\DeclarationBlock;
use Sabberworm\CSS\Settings;
use Sabberworm\CSS\Value\URL;

class CSS
{
    private static $used_selectors;
    private static $excluded_selectors;

    //initialize css functions
    public static function init()
    {
        if(!empty(Config::$options['assets']['remove_unused_css'])) {
            //if(!empty(apply_filters('perfmatters_remove_unused_css', true))) {
                add_action('perfmatters_output_buffer_template_redirect', array('Perfmatters\CSS', 'remove_unused_css'));
            //}
            add_action('wp_ajax_perfmatters_clear_post_used_css', array('Perfmatters\CSS', 'clear_post_used_css'));
        }
    }

    //remove unused css
    public static function remove_unused_css($html)
    {
        if(empty(apply_filters('perfmatters_remove_unused_css', true))) {
            return $html;
        }

        if(Utilities::get_post_meta('perfmatters_exclude_unused_css')) {
            return $html;
        }

        //only logged out
        if(is_user_logged_in()) {
            return $html;
        }

        //only known url types
        $type = self::get_url_type();
        if(empty($type)) {
            return $html;
        }

        //setup file variables
        $used_css_path = PERFMATTERS_CACHE_DIR . 'css/' . $type . '.used.css';
        $used_css_url = PERFMATTERS_CACHE_URL . 'css/' . $type . '.used.css';
        $used_css_exists = file_exists($used_css_path);

        //match all stylesheets
        preg_match_all('#<link\s[^>]*?href=[\'"]([^\'"]+?\.css.*?)[\'"][^>]*?\/?>#i', $html, $stylesheets, PREG_SET_ORDER);

        if(!empty($stylesheets)) {

            //create our css cache directory
            if(!is_dir(PERFMATTERS_CACHE_DIR . 'css/')) {
                @mkdir(PERFMATTERS_CACHE_DIR . 'css/', 0755, true);
            }

            //populate used selectors
            self::get_used_selectors($html);
            self::get_excluded_selectors();

            $used_css_string = '';

            //loop through stylesheets
            foreach($stylesheets as $key => $stylesheet) {

                //stylesheet check
                if(!preg_match('#rel=[\'"]stylesheet[\'"]#is', $stylesheet[0])) {
                    continue;
                }

                //ignore google fonts
                if(stripos($stylesheet[1], '//fonts.googleapis.com/css') !== false || stripos($stylesheet[1], '.google-fonts.css') !== false) {
                    continue;
                }

                //exclude entire stylesheets
                if(!empty(Config::$options['assets']['rucss_excluded_stylesheets'])) {
                    foreach(Config::$options['assets']['rucss_excluded_stylesheets'] as $exclude) {
                        if(strpos($stylesheet[1], $exclude) !== false) {
                            unset($stylesheets[$key]);
                            continue 2;
                        }
                    }
                }

                //need to generate used css
                if(!$used_css_exists) {

                    //get local stylesheet path
                    $url = str_replace(trailingslashit(site_url()), '', explode('?', $stylesheet[1])[0]);
                    $file = str_replace('/wp-content', '/', WP_CONTENT_DIR) . $url;

                    //make sure local file exists
                    if(!file_exists($file)) {
                        continue;
                    }
                   
                    //get used css from stylesheet
                    $used_css = self::clean_stylesheet($stylesheet[1], @file_get_contents($file));

                    //add used stylesheet css to total used
                    $used_css_string.= $used_css;
                }
            
                //delay stylesheets
                if(empty(Config::$options['assets']['rucss_stylesheet_behavior'])) {
                    $new_link = preg_replace('#href=([\'"]).+?\1#', 'data-pmdelayedstyle="' . $stylesheet[1] . '"',$stylesheet[0]);
                    $html = str_replace($stylesheet[0], $new_link, $html);
                }
                //async stylesheets
                elseif(Config::$options['assets']['rucss_stylesheet_behavior'] == 'async') {
                    $new_link = preg_replace(array('#media=([\'"]).+?\1#', '#onload=([\'"]).+?\1#'), '', $stylesheet[0]);
                    $new_link = str_replace('<link', '<link media="print" onload="this.media=\'all\';this.onload=null;"', $new_link);
                    $html = str_replace($stylesheet[0], $new_link, $html);
                }
                //remove stylesheets
                elseif(Config::$options['assets']['rucss_stylesheet_behavior'] == 'remove') {
                    $html = str_replace($stylesheet[0], '', $html);
                }
            }

            //process css to remove unused
            if(!empty($used_css_string)) {
                file_put_contents($used_css_path, apply_filters('perfmatters_used_css', $used_css_string));
            }

            //print used css inline after first title tag
            $pos = strpos($html, '</title>');
            if($pos !== false) {

                //print file
                if(!empty(Config::$options['assets']['rucss_method']) && Config::$options['assets']['rucss_method'] == 'file') {
                    $used_css_output = "<link rel='preload' href='" . $used_css_url . "' as='style' onload=\"this.rel='stylesheet';this.removeAttribute('onload');\">";
                    $used_css_output.= '<link rel="stylesheet" id="perfmatters-used-css" href="' . $used_css_url . '" media="all" />';
                }
                //print inline
                else {
                    $used_css_output = '<style id="perfmatters-used-css">' . file_get_contents($used_css_path) . '</style>';
                }
                
                $html = substr_replace($html, '</title>' . $used_css_output, $pos, 8);
            }

            //delay stylesheet script
            if(empty(Config::$options['assets']['rucss_stylesheet_behavior'])) {
                $script = '<script type="text/javascript" id="perfmatters-delayed-styles-js">!function(){const e=["keydown","mousemove","wheel","touchmove","touchstart","touchend"];function t(){document.querySelectorAll("link[data-pmdelayedstyle]").forEach(function(e){e.setAttribute("href",e.getAttribute("data-pmdelayedstyle"))}),e.forEach(function(e){window.removeEventListener(e,t,{passive:!0})})}e.forEach(function(e){window.addEventListener(e,t,{passive:!0})})}();</script>';
                $html = str_replace('</body>', $script . '</body>', $html);
            }
        }

        return $html;
    } 

    //get url type
    private static function get_url_type()
    {
        global $wp_query;

        $type = '';

        if($wp_query->is_page) {
            $type = is_front_page() ? 'front' : 'page-' . $wp_query->post->ID;
        }
        elseif($wp_query->is_home) {
            $type = 'home';
        }
        elseif($wp_query->is_single) {
            $type = get_post_type() !== false ? get_post_type() : 'single';
        }
        elseif($wp_query->is_category) {
            $type = 'category';
        }
        elseif($wp_query->is_tag) {
            $type = 'tag';
        } 
        elseif($wp_query->is_tax) {
            $type = 'tax';
        }
        elseif($wp_query->is_archive) {
            $type = $wp_query->is_day ? 'day' : ($wp_query->is_month ? 'month' : ($wp_query->is_year ? 'year' : ($wp_query->is_author ? 'author' : 'archive')));
        } 
        elseif($wp_query->is_search) {
            $type = 'search';
        }
        elseif($wp_query->is_404) {
            $type = '404';
        }

        return $type;
    }

    //get used selectors in html
    private static function get_used_selectors($html) {

        if(!$html) {
            return;
        }

        //get dom document
        $libxml_previous = libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $result = $dom->loadHTML($html);
        libxml_clear_errors();
        libxml_use_internal_errors($libxml_previous);

        if($result) {
            $dom->xpath = new \DOMXPath($dom);

            //setup used selectors array
            self::$used_selectors = array('tags' => array(), 'ids' => array(), 'classes' => array());

            //search for used selectors in dom
            $classes = array();
            foreach($dom->getElementsByTagName('*') as $tag) {

                //add tag
                self::$used_selectors['tags'][$tag->tagName] = 1;

                //add add tag id
                if($tag->hasAttribute('id')) {
                    self::$used_selectors['ids'][$tag->getAttribute('id')] = 1;
                }

                //store tag classes
                if($tag->hasAttribute('class')) {
                    $class = $tag->getAttribute('class');
                    $tag_classes = preg_split('/\s+/', $class);
                    array_push($classes, ...$tag_classes);
                }
            }

            //add classes
            $classes = array_filter(array_unique($classes));
            if($classes) {
                self::$used_selectors['classes'] = array_fill_keys($classes, 1);
            }
        }
    }

    //get excluded selectors
    private static function get_excluded_selectors() {
        self::$excluded_selectors = array();
        if(!empty(Config::$options['assets']['rucss_excluded_selectors'])) {
            self::$excluded_selectors = Config::$options['assets']['rucss_excluded_selectors'];
        }
        self::$excluded_selectors = apply_filters('perfmatters_rucss_excluded_selectors', self::$excluded_selectors);
    }

    //remove unusde css from stylesheet
    private static function clean_stylesheet($url, $css) 
    {
        //https://github.com/sabberworm/PHP-CSS-Parser/issues/150
        $css = preg_replace('/^\xEF\xBB\xBF/', '', $css);

        //setup css parser
        $settings = Settings::create()->withMultibyteSupport(false);
        $parser = new CSSParser($css, $settings);
        $parsed_css = $parser->parse();

        //convert relative urls to full urls
        self::fix_relative_urls($url, $parsed_css);

        $css_data = self::prep_css_data($parsed_css);

        return self::remove_unused_selectors($css_data);
    }

    //convert relative urls to full urls
    private static function fix_relative_urls($stylesheet_url, Document $data)
    {
        //get base url from stylesheet
        $base_url = preg_replace('#[^/]+(\?.*)?$#', '', $stylesheet_url);

        //search css for urls
        $values = $data->getAllValues();
        foreach($values as $value) {

            if(!($value instanceof URL)) {
                continue;
            }

            $url = $value->getURL()->getString();

            //not relative
            if(preg_match('/^(https?|data):/', $url)) {
                continue;
            }

            $parsed_url = parse_url($url);

            //final checks
            if(!empty($parsed_url['host']) || empty($parsed_url['path']) || $parsed_url['path'][0] === '/') {
                continue;
            }

            //create full url and replace
            $new_url = $base_url . $url;
            $value->getUrl()->setString($new_url);
        }
    }

    //prep parsed css for cleaning
    private static function prep_css_data(CSSBlockList $data)
    {
        $items = array();

        foreach($data->getContents() as $content) {

            //remove charset objects since were printing inline
            if($content instanceof Charset) {
                continue;
            }

            if($content instanceof AtRuleBlockList) {
                $items[] = array(
                    'rulesets' => self::prep_css_data($content),
                    'at_rule' => "@{$content->atRuleName()} {$content->atRuleArgs()}",
                );
            }
            else {
                $item = array('css' => $content->render(OutputFormat::createCompact()));

                if($content instanceof DeclarationBlock) {
                    $item['selectors'] = self::sort_selectors($content->getSelectors());
                }

                $items[] = $item;
            }
        }

        return $items;
    }

    //sort selectors into different categories we need
    private static function sort_selectors($selectors)
    {
        $selectors = array_map(
            function($sel) {
                return $sel->__toString();
            },
            $selectors
        );

        $selectors_data = array();
        foreach($selectors as $selector) {

            //setup selector data array
            $data = array(
                'selector' => trim($selector),
                'classes' => array(),
                'ids' => array(),
                'tags' => array(),
                'atts' => array()
            );

            //eliminate false negatives (:not(), pseudo, etc...)
            $selector = preg_replace('/(?<!\\\\)::?[a-zA-Z0-9_-]+(\(.+?\))?/', '', $selector);

            //atts
            $selector = preg_replace_callback(
                '/\[([A-Za-z0-9_:-]+)(\W?=[^\]]+)?\]/', 
                function($matches) use (&$data) {
                    $data['atts'][] = $matches[1];
                    return '';
                },
                $selector
            );

            //classes
            $selector = preg_replace_callback(
                '/\.((?:[a-zA-Z0-9_-]+|\\\\.)+)/',
                function($matches) use (&$data) {
                    $data['classes'][] = stripslashes($matches[1]);
                    return '';
                },
                $selector
            );

            //ids
            $selector = preg_replace_callback(
                '/#([a-zA-Z0-9_-]+)/',
                function($matches) use (&$data) {
                    $data['ids'][] = $matches[1];
                    return '';
                },
                $selector
            );

            //tags
            $selector = preg_replace_callback(
                '/[a-zA-Z0-9_-]+/',
                function($matches) use (&$data) {
                    $data['tags'][] = $matches[0];
                    return '';
                },
                $selector
            );

            //add selector data to main array
            $selectors_data[] = array_filter($data);
        }

        return array_filter($selectors_data);
    }

    //remove unused selectors from css data
    private static function remove_unused_selectors($data)
    {
        $rendered = [];

        foreach($data as $item) {

            //has css
            if(isset($item['css'])) {

                //need at least one selector match
                $should_render = !isset($item['selectors']) || 0 !== count(array_filter($item['selectors'],
                    function($selector) {
                        return self::is_selector_used($selector);
                    }
                ));

                if($should_render) {
                    $rendered[] = $item['css'];
                }

                continue;
            }

            //nested rulesets
            if(!empty($item['rulesets'])) {
                $child_rulesets = self::remove_unused_selectors($item['rulesets']);

                if($child_rulesets) {
                  $rendered[] = sprintf('%s{%s}', $item['at_rule'], $child_rulesets);
                }
            }
        }

        return implode("", $rendered);
    }
  
    //check if selector is used
    private static function is_selector_used($selector)
    {
        //:root selector
        if($selector['selector'] === ':root') {
            return true;
        }

        //lone attribute selector
        if(!empty($selector['atts']) && (empty($selector['classes']) && empty($selector['ids']) && empty($selector['tags']))) {
            return true;
        }

        //search for excluded selector match
        if(!empty(self::$excluded_selectors)) {
            foreach(self::$excluded_selectors as $key => $value) {
                if(preg_match('#(' . preg_quote($value) . ')(?=\s|\.|\:|,|\[|$)#', $selector['selector'])) {
                    return true;
                }
            }
        }

        //is selector used in the dom
        foreach(array('classes', 'ids', 'tags') as $type) {
            if(!empty($selector[$type])) {

                //cast array if needed
                $targets = (array)$selector[$type];
                foreach($targets as $target) {

                    //bail if a target doesn't exist
                    if(!isset(self::$used_selectors[$type][$target])) { 
                        return false;
                    }
                }
            }
        }

        return true;
    }

    //delete all files in the css cache directory
    public static function clear_used_css()
    {      
        $files = glob(PERFMATTERS_CACHE_DIR . 'css/*');
        foreach($files as $file) {
            if(is_file($file)) {
                unlink($file);
            }
        }
    }

    //clear used css file for specific post or post type
    public static function clear_post_used_css() {
        if(empty($_POST['action']) || empty($_POST['nonce']) || empty($_POST['post_id'])) {
            return;
        }

        if($_POST['action'] != 'perfmatters_clear_post_used_css') {
            return;
        }

        if(!wp_verify_nonce($_POST['nonce'], 'perfmatters_clear_post_used_css')) {
            return;
        }

        $post_id = (int)$_POST['post_id'];
        $post_type = get_post_type($post_id);
        $path = $post_type == 'page' ? 'page-' . $post_id : $post_type;

        $file = PERFMATTERS_CACHE_DIR . 'css/' . $path . '.used.css';
        if(is_file($file)) {
            unlink($file);
        }

        wp_send_json_success();
        exit;
    }
}
