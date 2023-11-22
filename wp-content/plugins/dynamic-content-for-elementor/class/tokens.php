<?php

// Example Option Tokens Grammar:
// [option:optName:arrayKey1:arrayKey2...|filter1|filter2...?fallbackString]
namespace DynamicContentForElementor;

if (!\defined('ABSPATH')) {
    exit;
}
class FiltersParser
{
    private $str;
    private $current = 0;
    private $captures = [];
    private $filters = [];
    public function __construct($str)
    {
        $this->str = $str;
    }
    private function peek()
    {
        if ($this->is_at_end()) {
            return 'EOF';
        }
        return $this->str[$this->current];
    }
    private function match($c)
    {
        if ($this->is_at_end()) {
            return \false;
        }
        if ($this->str[$this->current] === $c) {
            $this->current++;
            return \true;
        }
        return \false;
    }
    private function match_regex($regex)
    {
        $matches = [];
        // regex should always be anchored at the beginning of the string
        $regex = $regex[0] . '\\G' . \substr($regex, 1);
        if (!\preg_match($regex, $this->str, $matches, 0, $this->current)) {
            return \false;
        }
        $this->current += \strlen($matches[0]);
        if (isset($matches[1])) {
            return $matches[1];
        }
        return \true;
    }
    private function is_at_end()
    {
        return $this->current >= \strlen($this->str);
    }
    private function advance()
    {
        return $this->str[$this->current++];
    }
    private function quoted_string($quote_ch)
    {
        $esc = \false;
        $qstr = '';
        while (1) {
            if ($this->is_at_end()) {
                return \false;
            }
            $c = $this->advance();
            if ($esc) {
                $qstr .= $c;
                $esc = \false;
            } else {
                if ('\\' === $c) {
                    $esc = \true;
                } elseif ($c === $quote_ch) {
                    return $qstr;
                } else {
                    $qstr .= $c;
                }
            }
        }
    }
    private function fn_name()
    {
        // The following is the precise regex for a function name.
        $name = $this->match_regex('/\\s*([a-zA-Z_\\x80-\\xff][a-zA-Z0-9_\\x80-\\xff]*)\\s*/');
        if ($name === \false) {
            return 'error';
        }
        if ($this->match('|')) {
            $this->filters[] = [$name, []];
            // no arguments.
            return 'fn_name';
        } elseif ($this->match('(')) {
            $this->captures[] = $name;
            return 'fn_arg';
        } elseif ($this->is_at_end()) {
            $this->filters[] = [$name, []];
            // no arguments.
            return 'done';
        } else {
            return 'error';
        }
    }
    private function skip_spaces()
    {
        $this->match_regex('/\\s*/');
    }
    private function fn_arg()
    {
        $this->skip_spaces();
        $quote_ch = \false;
        if ($this->match('"')) {
            $quote_ch = '"';
        } elseif ($this->match("'")) {
            $quote_ch = "'";
        }
        if ($quote_ch) {
            $qstr = $this->quoted_string($quote_ch);
            if ($qstr === \false) {
                return 'error';
            }
            $this->captures[] = $qstr;
        } else {
            $arg = $this->match_regex('/([^\\s,\\)]+)\\s*/');
            if ($arg === \false) {
                return 'error';
            }
            $this->captures[] = $arg;
        }
        if ($this->match(',')) {
            return 'fn_arg';
        } elseif ($this->match(')')) {
            $fn_name = \array_shift($this->captures);
            $this->filters[] = [$fn_name, $this->captures];
            $this->captures = [];
            if ($this->match('|')) {
                return 'fn_name';
            } elseif ($this->is_at_end()) {
                return 'done';
            } else {
                return 'error';
            }
        } else {
            return 'error';
        }
    }
    public function parse()
    {
        $nf = 'fn_name';
        while (\true) {
            $nf = $this->{$nf}();
            if (!$nf) {
                return \false;
            }
            if ('error' === $nf) {
                return \false;
            }
            if ('done' === $nf) {
                return $this->filters;
            }
        }
    }
}
/**
 * Tokens Class
 */
class Tokens
{
    const OPTIONS_WHITELIST = ['siteurl' => \true, 'home' => \true, 'blogname' => \true, 'blogdescription' => \true, 'start_of_week' => \true, 'posts_per_page' => \true, 'date_format' => \true, 'time_format' => \true, 'timezone_string' => \true, 'sticky_posts' => \true, 'elementor_version' => \true, 'www_user_roles' => \true, 'thumbnail_size_h' => \true, 'thumbnail_size_w' => \true, 'users_can_register' => \true, 'avatar_default' => \true];
    const DEFAULT_FILTERS_WHITELIST = ['var_dump' => \true, 'absint' => \true, 'array_keys' => \true, 'ceil' => \true, 'count' => \true, 'current_time' => \true, 'date' => \true, 'date_i18n' => \true, 'do_shortcode' => \true, 'empty' => \true, 'end' => \true, 'esc_attr' => \true, 'esc_html' => \true, 'esc_js' => \true, 'esc_textarea' => \true, 'esc_url' => \true, 'esc_url_raw' => \true, 'explode' => \true, 'filter_var' => \true, 'floatval' => \true, 'floor' => \true, 'get_avatar' => \true, 'get_avatar_url' => \true, 'get_cat_ID' => \true, 'get_cat_name' => \true, 'get_categories' => \true, 'get_category' => \true, 'get_category_by_path' => \true, 'get_category_by_slug' => \true, 'get_date_from_gmt' => \true, 'get_permalink' => \true, 'get_post_field' => \true, 'get_post_format' => \true, 'get_post_status' => \true, 'get_post_type' => \true, 'get_tag' => \true, 'get_tags' => \true, 'get_taxonomies' => \true, 'get_taxonomy' => \true, 'get_term' => \true, 'get_term_by' => \true, 'get_the_author' => \true, 'get_the_category' => \true, 'get_the_category_by_ID' => \true, 'get_the_content' => \true, 'get_the_date' => \true, 'get_the_excerpt' => \true, 'get_the_ID' => \true, 'get_the_post_thumbnail' => \true, 'get_the_time' => \true, 'get_the_title' => \true, 'htmlentities2' => \true, 'implode' => \true, 'intval' => \true, 'json_decode' => \true, 'json_encode' => \true, 'max' => \true, 'md5' => \true, 'microtime' => \true, 'min' => \true, 'nl2br' => \true, 'rand' => \true, 'reset' => \true, 'round' => \true, 'rtrim' => \true, 'strsub' => \true, 'sanitize_email' => \true, 'sanitize_file_name' => \true, 'sanitize_html_class' => \true, 'sanitize_key' => \true, 'sanitize_mime_type' => \true, 'sanitize_option' => \true, 'sanitize_text_field' => \true, 'sanitize_title' => \true, 'sanitize_user' => \true, 'single_cat_title' => \true, 'single_tag_title' => \true, 'size_format' => \true, 'str_replace' => \true, 'str_shuffle' => \true, 'stripslashes' => \true, 'strlen' => \true, 'strpost' => \true, 'strrev' => \true, 'strtolower' => \true, 'strtotime' => \true, 'strtoupper' => \true, 'strtr' => \true, 'substr' => \true, 'the_category' => \true, 'the_ID' => \true, 'the_permalink' => \true, 'time' => \true, 'trim' => \true, 'ucwords' => \true, 'uniqid' => \true, 'urlencode' => \true, 'wordwrap' => \true, 'wp_count_posts' => \true, 'wp_filter_kses' => \true, 'wp_filter_nohtml_kses' => \true, 'wp_filter_post_kses' => \true, 'wp_get_attachment_image' => \true, 'wp_get_attachment_image_src' => \true, 'wp_get_attachment_link' => \true, 'wp_get_attachment_metadata' => \true, 'wp_get_attachment_thumb_file' => \true, 'wp_get_attachment_thumb_url' => \true, 'wp_get_attachment_url' => \true, 'wp_kses' => \true, 'wp_kses_attr' => \true, 'wp_kses_post' => \true, 'wp_kses_post_deep' => \true, 'wp_list_pluck' => \true, 'wp_logout_url' => \true, 'wp_trim_words' => \true, 'wpautop' => \true];
    /**
     * @var ?array<string, boolean>
     */
    private static $filters_whitelist;
    private static $filters_blacklist = ['__halt_compiler' => \true, 'apache_child_terminate' => \true, 'base64_decode' => \true, 'bzdecompress' => \true, 'call_user_func' => \true, 'call_user_func_array' => \true, 'call_user_method' => \true, 'call_user_method_array' => \true, 'convert_uudecode' => \true, 'file_get_contents' => \true, 'file_put_contents' => \true, 'fsockopen' => \true, 'gzdecode' => \true, 'gzinflate' => \true, 'gzuncompress' => \true, 'include' => \true, 'include_once' => \true, 'invokeargs' => \true, 'pcntl_exec' => \true, 'pcntl_fork' => \true, 'pfsockopen' => \true, 'posix_getcwd' => \true, 'posix_getpwuid' => \true, 'posix_getuid' => \true, 'posix_uname' => \true, 'ReflectionFunction' => \true, 'require' => \true, 'require_once' => \true, 'shell_exec' => \true, 'str_rot13' => \true, 'sys_get_temp_dir' => \true, 'wp_remote_fopen' => \true, 'wp_remote_get' => \true, 'wp_remote_head' => \true, 'wp_remote_post' => \true, 'wp_remote_request' => \true, 'wp_safe_remote_get' => \true, 'wp_safe_remote_head' => \true, 'wp_safe_remote_post' => \true, 'wp_safe_remote_request' => \true, 'zlib_decode' => \true, 'eval' => \true];
    /**
     * @return array<string,mixed>
     */
    public static function get_tokens_list()
    {
        return ['form' => ['callback' => 'replace_form_tokens'], 'system' => ['callback' => 'replace_system_tokens'], 'date' => ['callback' => 'replace_date_tokens'], 'author' => ['callback' => 'replace_author_tokens'], 'user' => ['callback' => 'replace_user_tokens'], 'post' => ['callback' => 'replace_post_tokens'], 'term' => ['callback' => 'replace_term_tokens'], 'option' => ['callback' => 'replace_option_tokens'], 'wp_query' => ['callback' => 'replace_wp_query_tokens'], 'query' => ['callback' => 'replace_query_tokens'], 'acf' => ['callback' => 'replace_acf_tokens'], 'product' => ['callback' => 'replace_product_tokens'], 'expr' => ['callback' => 'replace_expr_tokens']];
    }
    /**
     * @return array<string, string>
     */
    public static function get_tokens_options()
    {
        $options = [];
        foreach (self::get_tokens_list() as $key => $token) {
            $options[$key] = $key === 'post' ? 'post, jet, metabox' : $key;
        }
        return $options;
    }
    public static $data = \false;
    public static function do_tokens($text = '')
    {
        if (get_option('dce_tokens_status') === 'disable') {
            return $text;
        }
        $tokens = self::get_tokens_list();
        $active_tokens = get_option('dce_active_tokens', \array_keys($tokens));
        if (!\is_array($active_tokens)) {
            // not clear how can this end up not being an array at this point,
            // but it has happended.
            $active_tokens = \array_keys($tokens);
        }
        foreach ($tokens as $key => $token) {
            if (!\in_array($key, $active_tokens, \true)) {
                continue;
            }
            if (!\is_string($text)) {
                return $text;
            }
            $text = self::{$token['callback']}($text);
        }
        return $text;
    }
    public static function replace_tokens_with_his_name($text, $var_name)
    {
        $pezzi = \explode('[' . $var_name, $text);
        if (\count($pezzi) > 1) {
            foreach ($pezzi as $dce_key => $avalue) {
                if ($dce_key) {
                    $metaTmp = \explode(']', $avalue);
                    $metaParams = \reset($metaTmp);
                    $subfield = '';
                    if (\substr($metaParams, 0, 1) == ':') {
                        $metaParams = \substr($metaParams, 1);
                        $subfield = ':';
                    }
                    $morePezzi = \explode('?', $metaParams, 2);
                    $pezzoTmp = \reset($morePezzi);
                    $altriPezzi = \explode('|', $pezzoTmp, 2);
                    $metaName = \reset($altriPezzi);
                    $metaKey = \explode(':', $metaName);
                    $field = \reset($metaKey);
                    $replaceValue = $field;
                    $text = \str_replace('[' . $var_name . $subfield . $metaParams . ']', $replaceValue, $text);
                }
            }
        }
        return $text;
    }
    public static function replace_expr_tokens($text)
    {
        $pezzi = \explode('[expr:', $text);
        if (\count($pezzi) > 1) {
            foreach ($pezzi as $dce_key => $avalue) {
                $filters = array();
                if ($dce_key) {
                    $metaTmp = \explode(']', $avalue);
                    $metaParams = \reset($metaTmp);
                    // GET FALLBACK
                    $morePezzi = \explode('?', $metaParams, 2);
                    $fallback = '';
                    if (\count($morePezzi) == 2) {
                        $fallback = \end($morePezzi);
                    }
                    $pezzoTmp = \reset($morePezzi);
                    // GET FILTERS or ID
                    $altriPezzi = \explode('|', $pezzoTmp, 2);
                    $metaName = \reset($altriPezzi);
                    // GET SUB ARRAY
                    $metaKey = \explode(':', $metaName);
                    $metaKey = \reset($metaKey);
                    try {
                        $replaceValue = \DynamicOOOS\jlawrence\eos\Parser::solve($metaKey);
                    } catch (\Throwable $e) {
                        $replaceValue = esc_html__('Expression Token Error', 'dynamic-content-for-elementor');
                    }
                    if (\count($altriPezzi) == 2) {
                        // APPLY FILTERS
                        $replaceValue = self::apply_filters($replaceValue, \end($altriPezzi));
                    }
                    $replaceValue = self::value_or_fallback($replaceValue, $fallback);
                    $text = \str_replace('[expr:' . $metaParams . ']', $replaceValue, $text);
                }
            }
        }
        return $text;
    }
    public static function replace_form_tokens($text)
    {
        global $dce_form;
        $text = \str_replace('[form:pdf]', '<!--[dce_form_pdf:attachment]-->', $text);
        // pdf mail attachment
        if (\is_null($dce_form)) {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                $text = self::replace_tokens_with_his_name($text, 'form');
            }
        } else {
            $text = self::replace_content_shortcodes($text, '[form:all-fields]', $dce_form);
            $text = self::replace_content_shortcodes($text, '[form:all-fields|!empty]', $dce_form, '<br>', \false);
            $text = self::replace_var_tokens($text, 'form', $dce_form);
            $text = \DynamicContentForElementor\Helper::replace_setting_shortcodes($text, $dce_form);
        }
        return $text;
    }
    public static function replace_content_shortcodes($text, $all_fields_shortcode, $record, $line_break = '<br>', $show_empty = \true)
    {
        if (\false !== \strpos($text, $all_fields_shortcode)) {
            $fields = '';
            foreach ($record as $fkey => $fvalue) {
                if (!$show_empty && empty($fvalue)) {
                    continue;
                }
                if (\is_array($fvalue)) {
                    switch ($fkey) {
                        case 'pdf':
                            $fvalue = $fvalue['url'];
                            break;
                        default:
                            $fvalue = \var_export($fvalue, 1);
                    }
                }
                $formatted = '';
                if (!empty($fkey)) {
                    $formatted = \sprintf('<b>%s:</b> %s', $fkey, $fvalue);
                } elseif (!empty($fvalue)) {
                    $formatted = \sprintf('%s', $fvalue);
                }
                $fields .= $formatted . $line_break;
            }
            $text = \str_replace($all_fields_shortcode, $fields, $text);
        }
        return $text;
    }
    public static function replace_author_tokens($text)
    {
        $pezzi = \explode('[author', $text);
        if (\count($pezzi) > 1) {
            $dce_author_ID = \false;
            if (is_author()) {
                $author = get_queried_object();
                $dce_author_ID = $author->ID;
            }
            if (!$dce_author_ID) {
                $dce_author_ID = get_the_author_meta('ID');
            }
            if (!$dce_author_ID) {
                global $authordata;
                if (!$authordata) {
                    $post = get_post();
                    $authordata = get_user_by('ID', $post->post_author);
                }
                $dce_author_ID = $authordata->ID;
            }
            if ($dce_author_ID) {
                foreach ($pezzi as $dce_key => $avalue) {
                    if ($dce_key) {
                        $metaTmp = \explode(']', $avalue);
                        $metaParams = \reset($metaTmp);
                        $metaParamsAuthor = $metaParams . '|' . $dce_author_ID;
                        $text = \str_replace('[author' . $metaParams . ']', '[author' . $metaParamsAuthor . ']', $text);
                    }
                }
            }
            $text = \str_replace('[author', '[user', $text);
            $text = self::replace_user_tokens($text);
        }
        return $text;
    }
    public static function replace_user_tokens($text)
    {
        $text = \str_replace('[user]', '[user:display_name]', $text);
        $text = \str_replace('[user|', '[user:ID|', $text);
        // user field
        $pezzi = \explode('[user:', $text);
        if (\count($pezzi) > 1) {
            $current_user_id = get_current_user_id();
            $user_id = $current_user_id;
            foreach ($pezzi as $dce_key => $avalue) {
                if ($dce_key) {
                    $metaTmp = \explode(']', $avalue);
                    $metaParams = \reset($metaTmp);
                    $morePezzi = \explode('?', $metaParams, 2);
                    $fallback = '';
                    if (\count($morePezzi) == 2) {
                        $fallback = \end($morePezzi);
                    }
                    $pezzoTmp = \reset($morePezzi);
                    $altriPezzi = \explode('|', $pezzoTmp, 2);
                    $single = null;
                    if (\count($altriPezzi) == 2) {
                        $filtersTmp = \explode('|', \end($altriPezzi));
                        $real_filters = [];
                        foreach ($filtersTmp as $afilter) {
                            if (\is_numeric($afilter) && \intval($afilter) > 0) {
                                $user_id = \intval($afilter);
                            } elseif ($afilter === 'author') {
                                $user_id = get_the_author_meta('ID');
                            } elseif ($afilter === 'single') {
                                $single = \true;
                            } elseif ($afilter === 'multiple') {
                                $single = \false;
                            } else {
                                $real_filters[] = $afilter;
                            }
                        }
                    }
                    $metaName = \reset($altriPezzi);
                    $metaKey = \explode(':', $metaName);
                    $field = \array_shift($metaKey);
                    $metaValue = \DynamicContentForElementor\Helper::get_user_value($user_id, $field, $single);
                    $replaceValue = self::check_array_value($metaValue, $metaKey);
                    if (\count($altriPezzi) == 2) {
                        // APPLY FILTERS
                        $replaceValue = self::apply_filters($replaceValue, \implode('|', $real_filters), $user_id, $field);
                    }
                    $replaceValue = self::value_or_fallback($replaceValue, $fallback);
                    $text = \str_replace('[user:' . $metaParams . ']', $replaceValue, $text);
                }
            }
        }
        return $text;
    }
    public static function replace_post_tokens($text)
    {
        if (\DynamicContentForElementor\Helper::is_jetengine_active()) {
            $text = \str_replace('[jet:', '[post:', $text);
        }
        if (\DynamicContentForElementor\Helper::is_metabox_active()) {
            $text = \str_replace('[metabox:', '[post:', $text);
        }
        $text = \str_replace('[post]', '[post:post_title]', $text);
        $text = \str_replace('[post|', '[post:ID|', $text);
        // post field
        $pezzi = \explode('[post:', $text);
        if (\count($pezzi) > 1) {
            $current_post_id = $post_id = get_the_ID();
            $current_post = get_post();
            if ($current_post) {
                $current_post_id = $post_id = $current_post->ID;
            }
            foreach ($pezzi as $dce_key => $avalue) {
                $filters = array();
                if ($dce_key) {
                    $metaTmp = \explode(']', $avalue);
                    $metaParams = \reset($metaTmp);
                    // GET FALLBACK
                    $morePezzi = \explode('?', $metaParams, 2);
                    $fallback = '';
                    if (\count($morePezzi) == 2) {
                        $fallback = \end($morePezzi);
                    }
                    $pezzoTmp = \reset($morePezzi);
                    // GET FILTERS or ID
                    $single = null;
                    $altriPezzi = \explode('|', $pezzoTmp, 2);
                    if (\count($altriPezzi) == 2) {
                        $filtersTmp = \explode('|', \end($altriPezzi));
                        foreach ($filtersTmp as $afilter) {
                            if (\is_numeric($afilter) && \intval($afilter) > 0) {
                                $post_id = \intval($afilter);
                            }
                            if ($afilter == 'single') {
                                $single = \true;
                            }
                            if ($afilter == 'multiple') {
                                $single = \false;
                            }
                        }
                    }
                    $metaName = \reset($altriPezzi);
                    // GET SUB ARRAY
                    $metaKey = \explode(':', $metaName);
                    $field = \array_shift($metaKey);
                    $metaValue = '';
                    if ($post_id) {
                        if ($field == 'type' && !empty($metaKey)) {
                            $metaValue = get_post_type_object(get_post_type(get_post($post_id)));
                        } else {
                            $metaValue = \DynamicContentForElementor\Helper::get_post_value($post_id, $field, $metaKey, $single);
                        }
                    }
                    $replaceValue = self::check_array_value($metaValue, $metaKey);
                    if (\count($altriPezzi) == 2) {
                        // APPLY FILTERS
                        $replaceValue = self::apply_filters($replaceValue, \end($altriPezzi), $post_id, $field);
                    }
                    $replaceValue = self::value_or_fallback($replaceValue, $fallback);
                    $text = \str_replace('[post:' . $metaParams . ']', $replaceValue, $text);
                }
            }
        }
        return $text;
    }
    public static function replace_product_tokens($text)
    {
        if (!\DynamicContentForElementor\Helper::is_woocommerce_active()) {
            return $text;
        }
        global $product;
        $pezzi = \explode('[product:', $text);
        if (\count($pezzi) > 1) {
            $current_post_id = $post_id = get_the_ID();
            $current_post = \wc_get_product();
            foreach ($pezzi as $dce_key => $avalue) {
                $filters = array();
                if ($dce_key) {
                    $metaTmp = \explode(']', $avalue);
                    $metaParams = \reset($metaTmp);
                    // GET FALLBACK
                    $morePezzi = \explode('?', $metaParams, 2);
                    $fallback = '';
                    if (\count($morePezzi) == 2) {
                        $fallback = \end($morePezzi);
                    }
                    $pezzoTmp = \reset($morePezzi);
                    // GET FILTERS or ID
                    $single = null;
                    $altriPezzi = \explode('|', $pezzoTmp, 2);
                    if (\count($altriPezzi) == 2) {
                        $filtersTmp = \explode('|', \end($altriPezzi));
                        foreach ($filtersTmp as $afilter) {
                            if (\is_numeric($afilter) && \intval($afilter) > 0) {
                                $post_id = \intval($afilter);
                            }
                            $tmp = wc_get_product_id_by_sku($afilter);
                            if (\is_numeric($tmp)) {
                                $post_id = $tmp;
                            }
                            if ($afilter == 'single') {
                                $single = \true;
                            }
                            if ($afilter == 'multiple') {
                                $single = \false;
                            }
                        }
                    }
                    $metaName = \reset($altriPezzi);
                    // GET SUB ARRAY
                    $metaKey = \explode(':', $metaName);
                    $field = \array_shift($metaKey);
                    $metaValue = '';
                    if ($post_id) {
                        switch ($field) {
                            case 'variation_attributes':
                                if (\is_object($current_post) && \get_class($current_post) == 'WC_Product_Variable') {
                                    $metaValue = $current_post->get_variation_attributes();
                                    break;
                                }
                            default:
                                $metaValue = \DynamicContentForElementor\Helper::get_post_value($post_id, $field, '', $single);
                        }
                    }
                    $replaceValue = self::check_array_value($metaValue, $metaKey);
                    if (\count($altriPezzi) == 2) {
                        // APPLY FILTERS
                        $replaceValue = self::apply_filters($replaceValue, \end($altriPezzi), $post_id, $field);
                    }
                    $replaceValue = self::value_or_fallback($replaceValue, $fallback);
                    $text = \str_replace('[product:' . $metaParams . ']', $replaceValue, $text);
                }
            }
        }
        return $text;
    }
    public static function replace_acf_tokens($text)
    {
        if (!\DynamicContentForElementor\Helper::is_acf_active() && !\DynamicContentForElementor\Helper::is_acfpro_active()) {
            return $text;
        }
        $post_id = null;
        // acf repeater field
        $pezzi = \explode('[acf:', $text);
        if (\count($pezzi) > 1) {
            foreach ($pezzi as $dce_key => $avalue) {
                $filters = array();
                if ($dce_key) {
                    $metaTmp = \explode(']', $avalue);
                    $metaParams = \reset($metaTmp);
                    // GET FALLBACK
                    $morePezzi = \explode('?', $metaParams, 2);
                    $fallback = '';
                    if (\count($morePezzi) == 2) {
                        $fallback = \end($morePezzi);
                    }
                    $pezzoTmp = \reset($morePezzi);
                    // GET FILTERS or ID
                    $altriPezzi = \explode('|', $pezzoTmp, 2);
                    $metaName = \reset($altriPezzi);
                    if (\count($altriPezzi) == 2) {
                        $filtersTmp = \explode('|', \end($altriPezzi));
                        foreach ($filtersTmp as $afilter) {
                            if (\is_numeric($afilter) && \intval($afilter) > 0) {
                                $post_id = \intval($afilter);
                            }
                        }
                    }
                    // GET SUB ARRAY
                    $metaKey = \explode(':', $metaName);
                    $field = \array_shift($metaKey);
                    $metaValue = \DynamicContentForElementor\Helper::get_acf_field_value($field, $post_id);
                    if (!empty($metaKey)) {
                        // check for settings
                        if (\reset($metaKey) == 'settings') {
                            \array_shift($metaKey);
                            $metaValue = \DynamicContentForElementor\Helper::get_acf_field_settings($field);
                        }
                        // check if post field
                        $field_key = \reset($metaKey);
                        if (\substr($field_key, 0, 5) != 'post_') {
                            $field_key = 'post_' . $field_key;
                        }
                        $post_fields = \DynamicContentForElementor\Helper::get_post_fields();
                        if (isset($post_fields[$field_key])) {
                            $metaValue = \DynamicContentForElementor\Helper::get_acf_field_post($field);
                            $metaKey[0] = $field_key;
                        }
                    }
                    $replaceValue = self::check_array_value($metaValue, $metaKey);
                    if (\count($altriPezzi) == 2) {
                        // APPLY FILTERS
                        $replaceValue = self::apply_filters($replaceValue, \end($altriPezzi), $post_id, $field);
                    }
                    if (self::$data && !empty($replaceValue)) {
                        return $replaceValue;
                    }
                    $replaceValue = self::value_or_fallback($replaceValue, $fallback);
                    $text = \str_replace('[acf:' . $metaParams . ']', $replaceValue, $text);
                }
            }
        }
        return $text;
    }
    public static function replace_system_tokens($text)
    {
        $pezzi = \explode('[system:', $text);
        if (\count($pezzi) > 1) {
            foreach ($pezzi as $dce_key => $avalue) {
                $filters = array();
                if ($dce_key) {
                    $metaTmp = \explode(']', $avalue);
                    $metaParams = \reset($metaTmp);
                    // GET FALLBACK
                    $morePezzi = \explode('?', $metaParams, 2);
                    $fallback = '';
                    if (\count($morePezzi) == 2) {
                        $fallback = \end($morePezzi);
                    }
                    $pezzoTmp = \reset($morePezzi);
                    // GET FILTERS or ID
                    $altriPezzi = \explode('|', $pezzoTmp, 2);
                    $metaName = \reset($altriPezzi);
                    // GET SUB ARRAY
                    $metaKey = \explode(':', $metaName);
                    $metaKeyName = \array_shift($metaKey);
                    $metaValue = array();
                    switch ($metaKeyName) {
                        case 'get':
                        case '_get':
                        case '_GET':
                            $metaValue = $_GET;
                            break;
                        case 'post':
                        case '_post':
                        case '_POST':
                            $metaValue = $_POST;
                            break;
                        case '_request':
                        case '_REQUEST':
                        case 'request':
                            $metaValue = $_REQUEST;
                            break;
                        case 'cookie':
                        case '_cookie':
                        case '_COOKIE':
                            $metaValue = $_COOKIE;
                            break;
                        case 'session':
                        case '_session':
                        case '_SESSION':
                            $metaValue = $_SESSION;
                            break;
                        case 'server':
                        case '_server':
                        case '_SERVER':
                            $metaValue = $_SERVER;
                            break;
                        default:
                            break;
                    }
                    $replaceValue = self::check_array_value($metaValue, $metaKey);
                    if (\count($altriPezzi) == 2) {
                        // APPLY FILTERS
                        $replaceValue = self::apply_filters($replaceValue, \end($altriPezzi));
                    }
                    $replaceValue = self::value_or_fallback($replaceValue, $fallback);
                    $text = \str_replace('[system:' . $metaParams . ']', $replaceValue, $text);
                }
            }
        }
        return $text;
    }
    public static function replace_wp_query_tokens($text)
    {
        $pezzi = \explode('[wp_query:', $text);
        if (\count($pezzi) > 1) {
            global $wp_query;
            foreach ($pezzi as $dce_key => $avalue) {
                $filters = array();
                if ($dce_key) {
                    $metaTmp = \explode(']', $avalue);
                    $metaParams = \reset($metaTmp);
                    // GET FALLBACK
                    $morePezzi = \explode('?', $metaParams, 2);
                    $fallback = '';
                    if (\count($morePezzi) == 2) {
                        $fallback = \end($morePezzi);
                    }
                    $pezzoTmp = \reset($morePezzi);
                    // GET FILTERS or ID
                    $altriPezzi = \explode('|', $pezzoTmp, 2);
                    $metaName = \reset($altriPezzi);
                    // GET SUB ARRAY
                    $metaKey = \explode(':', $metaName);
                    $metaValue = $wp_query;
                    switch (\reset($metaKey)) {
                        case 'referer':
                            $replaceValue = wp_get_referer();
                            break;
                        default:
                            $replaceValue = self::check_array_value($metaValue, $metaKey);
                    }
                    if (\count($altriPezzi) == 2) {
                        // APPLY FILTERS
                        $replaceValue = self::apply_filters($replaceValue, \end($altriPezzi));
                    }
                    $replaceValue = self::value_or_fallback($replaceValue, $fallback);
                    $text = \str_replace('[wp_query:' . $metaParams . ']', $replaceValue, $text);
                }
            }
        }
        return $text;
    }
    public static function replace_query_tokens($text)
    {
        $pezzi = \explode('[query:', $text);
        if (\count($pezzi) > 1) {
            global $wpdb;
            $cpt = \DynamicContentForElementor\Helper::get_public_post_types();
            $cpt['attachment'] = 'Media';
            $taxonomies = \DynamicContentForElementor\Helper::get_taxonomies();
            $roles = \DynamicContentForElementor\Helper::get_roles();
            foreach ($pezzi as $dce_key => $avalue) {
                $filters = array();
                if ($dce_key) {
                    $metaTmp = \explode(']', $avalue);
                    $metaParams = \reset($metaTmp);
                    // GET FALLBACK
                    $morePezzi = \explode('?', $metaParams, 2);
                    $fallback = '';
                    if (\count($morePezzi) == 2) {
                        $fallback = \end($morePezzi);
                    }
                    $pezzoTmp = \reset($morePezzi);
                    // GET FILTERS or ID
                    $altriPezzi = \explode('|', $pezzoTmp, 2);
                    $metaName = \reset($altriPezzi);
                    $objects = $field = \false;
                    // GET SUB ARRAY
                    $metaKey = \explode(':', $metaName);
                    if (!empty($metaKey)) {
                        if ($metaKey[0]) {
                            $args = array();
                            switch ($metaKey[0]) {
                                case 'user':
                                    $type = 'user';
                                    $role = '';
                                    // any
                                    // user query
                                    if (isset($metaKey[1])) {
                                        if (isset($roles[$metaKey[1]])) {
                                            $role = $metaKey[1];
                                            if (isset($metaKey[2])) {
                                                $field = $metaKey[2];
                                            }
                                        } else {
                                            $field = $metaKey[1];
                                        }
                                    }
                                    $args = array('role' => $role);
                                    $query = new \WP_User_Query($args);
                                    $objects = $query->get_results();
                                    break;
                                case 'term':
                                    $type = 'term';
                                    $tax = 'category';
                                    // term query
                                    if (isset($metaKey[1])) {
                                        if (isset($taxonomies[$metaKey[1]])) {
                                            $tax = $metaKey[1];
                                            if (isset($metaKey[2])) {
                                                $field = $metaKey[2];
                                            }
                                        } else {
                                            $field = $metaKey[1];
                                        }
                                    }
                                    $args = array('count' => \true, 'taxonomy' => $tax, 'hide_empty' => \false);
                                    $query = new \WP_Term_Query($args);
                                    $objects = $query->get_terms();
                                    break;
                                default:
                                    $pt = $type = 'post';
                                    $tax = '';
                                    // post query
                                    if (isset($cpt[$metaKey[0]])) {
                                        $pt = $metaKey[0];
                                    }
                                    if (isset($metaKey[1])) {
                                        $term = \DynamicContentForElementor\Helper::get_term($metaKey[1]);
                                        /* if (isset($taxonomies[$metaKey[1]])) {
                                        	  $tax = $metaKey[1]; */
                                        if ($term) {
                                            $tax = $term->taxonomy;
                                            if (isset($metaKey[2])) {
                                                $field = $metaKey[2];
                                            }
                                        } else {
                                            if (isset($taxonomies[$metaKey[1]])) {
                                                if (isset($metaKey[2])) {
                                                    $field = $metaKey[2];
                                                }
                                            } else {
                                                $field = $metaKey[1];
                                            }
                                        }
                                    }
                                    $args = array('post_type' => $pt, 'nopaging' => \true);
                                    if ($tax) {
                                        switch ($tax) {
                                            case 'category':
                                                $args['category_name'] = $term->slug;
                                                break;
                                            case 'post_tag':
                                                $args['tag'] = $term->slug;
                                                break;
                                            default:
                                                $args['tax_query'] = array(array('taxonomy' => $tax, 'field' => 'slug', 'terms' => $term->slug));
                                        }
                                    } else {
                                        if (isset($metaKey[1])) {
                                            if (isset($taxonomies[$metaKey[1]])) {
                                                $tax = $metaKey[1];
                                                $terms = get_terms($tax);
                                                if (!empty($terms)) {
                                                    $terms_by_id = array();
                                                    foreach ($terms as $term) {
                                                        $terms_by_id[$term->term_id] = $term;
                                                    }
                                                    $terms_ids = \array_keys($terms_by_id);
                                                    switch ($tax) {
                                                        case 'category':
                                                            $args['category__in'] = $terms_ids;
                                                            break;
                                                        case 'post_tag':
                                                            $args['tag__in'] = $terms_ids;
                                                            break;
                                                        default:
                                                            $args['tax_query'] = array(array('taxonomy' => $tax, 'field' => 'term_id', 'terms' => $terms_ids));
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    if ($pt == 'attachment') {
                                        $args['post_status'] = 'inherit';
                                    }
                                    $query = new \WP_Query($args);
                                    $objects = $query->get_posts();
                            }
                        }
                    }
                    $replaceValue = '';
                    if (!empty($objects)) {
                        if ($field) {
                            $tmp = array();
                            $get_value_fnc = 'get_' . $type . '_value';
                            foreach ($objects as $aobj) {
                                if ($type == 'term') {
                                    $obj_id = $aobj->term_id;
                                } else {
                                    $obj_id = $aobj->ID;
                                }
                                $value = \DynamicContentForElementor\Helper::$get_value_fnc($obj_id, $field);
                                if (!empty($value)) {
                                    $tmp[$obj_id] = $value;
                                }
                            }
                            $objects = $tmp;
                        }
                        $replaceValue = $objects;
                    }
                    if (\count($altriPezzi) == 2) {
                        // APPLY FILTERS
                        $replaceValue = self::apply_filters($replaceValue, \end($altriPezzi));
                    }
                    $replaceValue = self::value_or_fallback($replaceValue, $fallback);
                    $text = \str_replace('[query:' . $metaParams . ']', $replaceValue, $text);
                }
            }
        }
        return $text;
    }
    public static function replace_var_tokens($text, $var_name, $var_value)
    {
        $var_value_original = $var_value;
        if (\is_object($var_value)) {
            $var_value = \get_object_vars($var_value);
        } else {
            $var_value = maybe_unserialize($var_value);
        }
        // simple var field
        $text = \str_replace('[' . $var_name . ']', \DynamicContentForElementor\Helper::to_string($var_value), $text);
        $pezzi = \explode('[' . $var_name, $text);
        if (\count($pezzi) > 1) {
            foreach ($pezzi as $dce_key => $avalue) {
                $filters = array();
                if ($dce_key) {
                    $metaTmp = \explode(']', $avalue);
                    $metaParams = \reset($metaTmp);
                    $subfield = '';
                    if (\substr($metaParams, 0, 1) == ':') {
                        if (\is_object($var_value_original)) {
                            switch (\get_class($var_value_original)) {
                                case 'WP_User':
                                    if ($var_name == 'user' || $var_name == 'object') {
                                        $text = \str_replace('[' . $var_name . $metaParams . ']', '[user' . $metaParams . ']', $text);
                                        return self::replace_user_tokens($text);
                                    }
                                    break;
                                case 'WP_Post':
                                    if ($var_name == 'post' || $var_name == 'object') {
                                        $text = \str_replace('[' . $var_name . $metaParams . ']', '[post' . $metaParams . ']', $text);
                                        return self::replace_post_tokens($text);
                                    }
                                    break;
                                case 'WP_Term':
                                    if ($var_name == 'term' || $var_name == 'object') {
                                        $text = \str_replace('[' . $var_name . $metaParams . ']', '[term' . $metaParams . ']', $text);
                                        return self::replace_term_tokens($text);
                                    }
                                    break;
                            }
                        }
                        $metaParams = \substr($metaParams, 1);
                        $subfield = ':';
                    }
                    // GET FALLBACK
                    $morePezzi = \explode('?', $metaParams, 2);
                    $fallback = '';
                    if (\count($morePezzi) == 2) {
                        $fallback = \end($morePezzi);
                    }
                    $pezzoTmp = \reset($morePezzi);
                    // GET FILTERS or ID
                    $altriPezzi = \explode('|', $pezzoTmp, 2);
                    $post_id = get_the_ID();
                    if (\count($altriPezzi) == 2) {
                        $filtersTmp = \explode('|', \end($altriPezzi));
                        if (\is_numeric(\reset($filtersTmp)) && \intval(\reset($filtersTmp)) > 0) {
                            $post_id = \reset($filtersTmp);
                        }
                    }
                    $metaName = \reset($altriPezzi);
                    // GET SUB ARRAY
                    $metaKey = \explode(':', $metaName);
                    $field = \reset($metaKey);
                    $replaceValue = self::check_array_value($var_value, $metaKey);
                    if (\count($altriPezzi) == 2) {
                        // APPLY FILTERS
                        $replaceValue = self::apply_filters($replaceValue, \end($altriPezzi), $post_id, $field);
                    }
                    $replaceValue = self::value_or_fallback($replaceValue, $fallback);
                    $text = \str_replace('[' . $var_name . $subfield . $metaParams . ']', $replaceValue, $text);
                }
            }
        }
        return $text;
    }
    public static function replace_term_tokens($text)
    {
        $text = \str_replace('[term]', '[term:name]', $text);
        $text = \str_replace('[term|', '[term:term_id|', $text);
        $pezzi = \explode('[term:', $text);
        if (\count($pezzi) > 1) {
            $terms = array();
            $queried_object = get_queried_object();
            if ($queried_object) {
                if (\get_class($queried_object) == 'WP_Term') {
                    $terms = array($queried_object);
                }
                if (\get_class($queried_object) == 'WP_Post' || in_the_loop()) {
                    $terms = \DynamicContentForElementor\Helper::get_post_terms(0, null, array(), array());
                }
            }
            global $term;
            if ($term) {
                if (\is_object($term) && \get_class($term) == 'WP_Term') {
                    $terms = array($term);
                }
            }
            $taxonomies = \DynamicContentForElementor\Helper::get_taxonomies();
            foreach ($pezzi as $dce_key => $avalue) {
                $filters = array();
                if ($dce_key) {
                    $metaTmp = \explode(']', $avalue);
                    $metaParams = \reset($metaTmp);
                    // GET FALLBACK
                    $morePezzi = \explode('?', $metaParams, 2);
                    $fallback = '';
                    if (\count($morePezzi) == 2) {
                        $fallback = \end($morePezzi);
                    }
                    $pezzoTmp = \reset($morePezzi);
                    // GET FILTERS or ID
                    $tterms = $terms;
                    $single = null;
                    $altriPezzi = \explode('|', $pezzoTmp, 2);
                    if (\count($altriPezzi) == 2) {
                        $filtersTmp = \explode('|', \end($altriPezzi));
                        foreach ($filtersTmp as $afilter) {
                            if ($afilter == 'single') {
                                $single = \true;
                            }
                            if ($afilter == 'multiple') {
                                $single = \false;
                            }
                            if (\is_numeric($afilter) && \intval($afilter) > 0) {
                                $term_id = $afilter;
                                if ($term_id) {
                                    $my_term = get_term_by('term_taxonomy_id', $term_id);
                                    $tterms = array($my_term);
                                }
                            } else {
                                if (isset($taxonomies[$afilter])) {
                                    // filter by taxonomy
                                    $terms_tmp = array();
                                    if (!empty($tterms)) {
                                        foreach ($tterms as $aterm) {
                                            if ($aterm->taxonomy == $afilter) {
                                                $terms_tmp[] = $aterm;
                                            }
                                        }
                                        $tterms = $terms_tmp;
                                    }
                                } else {
                                    if ($afilter == 'first' || $afilter == 'reset') {
                                        $tterms = array(\reset($tterms));
                                    }
                                    if ($afilter == 'last' || $afilter == 'end') {
                                        $tterms = array(\end($tterms));
                                    }
                                }
                            }
                        }
                    }
                    $metaName = \reset($altriPezzi);
                    // GET SUB ARRAY
                    $metaKey = \explode(':', $metaName);
                    $field = \array_shift($metaKey);
                    $replaceValue = array();
                    $tterms = \array_filter($tterms);
                    if (!empty($tterms)) {
                        foreach ($tterms as $dce_key => $aterm) {
                            $metaValue = \DynamicContentForElementor\Helper::get_term_value($aterm, $field, $single);
                            $metaValue = self::check_array_value($metaValue, $metaKey);
                            if (\count($altriPezzi) == 2) {
                                // APPLY FILTERS
                                $metaValue = self::apply_filters($metaValue, \end($altriPezzi), $aterm->term_id, $field);
                            }
                            $replaceValue[] = $metaValue;
                        }
                    }
                    $replaceValue = \array_filter($replaceValue);
                    if (!empty($replaceValue)) {
                        $replaceValue = \implode(', ', $replaceValue);
                    }
                    $replaceValue = self::value_or_fallback($replaceValue, $fallback);
                    $text = \str_replace('[term:' . $metaParams . ']', $replaceValue, $text);
                }
            }
        }
        return $text;
    }
    /**
     * @param string $name
     * @return mixed
     */
    public static function get_option_value($name)
    {
        $whitelist = apply_filters('dynamicooo/tokens/options-whitelist', self::OPTIONS_WHITELIST);
        // Whitelist or JetEngine
        if (($whitelist[$name] ?? \false) || \DynamicContentForElementor\Helper::is_jetengine_active() && jet_engine()->options_pages->registered_pages[$name]) {
            return get_option($name);
        }
        // ACF
        if (\strpos($name, 'options_') === 0 && \DynamicContentForElementor\Helper::is_acfpro_active()) {
            $res = \get_field(\substr($name, 8), 'option');
            if ($res !== null) {
                return $res;
            }
        }
        // Meta Box
        if (\DynamicContentForElementor\Helper::is_metabox_active()) {
            $all_meta_box_settings = rwmb_get_registry('field')->get_by_object_type('setting');
            if (\array_key_exists($name, $all_meta_box_settings)) {
                return get_option($name);
            }
        }
    }
    public static function replace_option_tokens($text)
    {
        $pezzi = \explode('[option:', $text);
        if (\count($pezzi) > 1) {
            foreach ($pezzi as $dce_key => $avalue) {
                if ($dce_key) {
                    $pezzo = \explode(']', $avalue);
                    $metaParams = \reset($pezzo);
                    $morePezzi = \explode('?', $metaParams, 2);
                    $fallback = '';
                    if (\count($morePezzi) == 2) {
                        $fallback = \end($morePezzi);
                    }
                    $pezzoTmp = \reset($morePezzi);
                    // GET FILTERS or ID
                    $altriPezzi = \explode('|', $pezzoTmp, 2);
                    $optionParams = \explode(':', \reset($altriPezzi));
                    $optionName = \array_shift($optionParams);
                    $optionValue = self::get_option_value($optionName);
                    $replaceValue = self::check_array_value($optionValue, $optionParams);
                    if (\count($altriPezzi) == 2) {
                        $replaceValue = self::apply_filters($replaceValue, \end($altriPezzi));
                    }
                    $replaceValue = self::value_or_fallback($replaceValue, $fallback);
                    $text = \str_replace('[option:' . $metaParams . ']', $replaceValue, $text);
                }
            }
        }
        return $text;
    }
    public static function replace_date_tokens($text)
    {
        $text = \str_replace('[date]', '[date:now]', $text);
        $text = \str_replace('[date|', '[date:now|', $text);
        // /wp-admin/options.php
        $pezzi = \explode('[date:', $text);
        if (\count($pezzi) > 1) {
            foreach ($pezzi as $dce_key => $avalue) {
                if ($dce_key) {
                    $pezzo = \explode(']', $avalue);
                    $metaParams = \reset($pezzo);
                    $altriPezzi = \explode('|', $metaParams, 2);
                    $filtersTmp = array();
                    if (\count($altriPezzi) == 2) {
                        $filtersTmp = \explode('|', \end($altriPezzi));
                    }
                    // GET TIMESTAMP
                    $timestamp = '';
                    // date format
                    $dateFormat = get_option('date_format');
                    if (!empty($filtersTmp)) {
                        $f = $filtersTmp[0];
                        if ($f && !\is_callable($f) && $f != 'IT') {
                            $dateFormat = $f;
                            \array_shift($filtersTmp);
                        }
                    }
                    $pezzoTmp = \reset($altriPezzi);
                    $dateParams = \explode(':', $pezzoTmp);
                    $altTime = \reset($dateParams);
                    if ($altTime == 'post' || $altTime == 'user') {
                        // from post field
                        $lastParam = \end($dateParams);
                        $timeMod = \false;
                        if (\in_array(\substr(\trim($lastParam), 0, 1), array('-', '+'))) {
                            $timeMod = \array_pop($dateParams);
                            $pezzoTmp = \implode(':', $dateParams);
                        }
                        $altTime = self::do_tokens('[' . $pezzoTmp . ']');
                        $altTime = \strtotime($altTime);
                        if ($timeMod) {
                            $altTime = \strtotime($timeMod, $altTime);
                        }
                    }
                    if (\is_numeric($altTime)) {
                        $timestamp = $altTime;
                    } else {
                        $timestamp = \strtotime($pezzoTmp);
                    }
                    $replaceValue = date_i18n($dateFormat, $timestamp);
                    // translate
                    if (!empty($filtersTmp)) {
                        $replaceValue = self::apply_filters($replaceValue, $filtersTmp);
                    }
                    $text = \str_replace('[date:' . $metaParams . ']', $replaceValue, $text);
                    // custom date
                }
            }
        }
        return $text;
    }
    // This function does more things its name would suggest:
    // It replaces $replacevalue with the dce helper 'to_string' method
    // fallbacks only if not in editor mode.
    // if $replaceValue is a list of posts, $replaceValue becomes a list of post IDs
    public static function value_or_fallback($replaceValue, $fallback = '')
    {
        if (\is_array($replaceValue)) {
            $post_ids = array();
            if (\is_array(\reset($replaceValue))) {
                $first = \reset($replaceValue);
                if (isset($first['ID'])) {
                    foreach ($replaceValue as $apost) {
                        if (isset($apost['ID'])) {
                            $post_ids[] = $apost['ID'];
                        }
                    }
                    $replaceValue = $post_ids;
                }
            }
        }
        $replaceValue = \DynamicContentForElementor\Helper::to_string($replaceValue);
        if (!\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            // Those <pre> checks are probably useless
            if ($replaceValue == '' || \substr($replaceValue, 0, 12) == '<pre>array (' || \substr($replaceValue, 0, 12) == '<pre>object(') {
                $replaceValue = $fallback;
            }
        }
        return $replaceValue;
    }
    /**
     * $optionValue is an array or an object. $optionParams is an array or a
     * string.  in case $optionValue is an array returns
     * $optionValue[$optionParams[0]][$optionParams[1]][...]
     * if one of $optionParams is 'permalink', it gets the permalink.
     * Todo: Document the case $optionValue is an object.
     */
    public static function check_array_value($optionValue = array(), $optionParams = array())
    {
        if (!\is_array($optionParams)) {
            $optionParams = array($optionParams);
        }
        $val = $optionValue;
        if (!empty($optionParams)) {
            foreach ($optionParams as $dce_key => $value) {
                if (\is_array($val)) {
                    if ($value == 'permalink') {
                        $val = \DynamicContentForElementor\Helper::get_permalink($val);
                    } elseif (\array_key_exists($value, $val) || isset($val[$value])) {
                        $val = $val[$value];
                    } else {
                        return \false;
                    }
                } elseif (\is_object($val)) {
                    $val_class = \get_class($val);
                    $tmp = \explode('(', $value);
                    $params = null;
                    if (\count($tmp) > 1) {
                        $value = \reset($tmp);
                        $params = \end($tmp);
                        if (\substr($params, -1) == ')') {
                            $params = \trim(\substr($params, 0, \strlen($params) - 1));
                        }
                    }
                    if ($value == 'permalink') {
                        return \DynamicContentForElementor\Helper::get_permalink($val);
                    } elseif (\property_exists($val, $value)) {
                        $val = $val->{$value};
                    } elseif (\method_exists($val, $value)) {
                        $method_checker = new \ReflectionMethod($val_class, $value);
                        if ($method_checker->isStatic()) {
                            if ($params) {
                                $val = $val::$value($params);
                            } else {
                                $val = $val::$value();
                            }
                        } else {
                            if ($params) {
                                $val = $val->{$value}($params);
                            } else {
                                $val = $val->{$value}();
                            }
                        }
                    } else {
                        return \false;
                    }
                }
            }
        }
        return $val;
    }
    public static function remove_quote($parameters = array())
    {
        if (!empty($parameters)) {
            foreach ($parameters as $pkey => $pvalue) {
                if (\substr($pvalue, 0, 1) == '"' && \substr($pvalue, -1) == '"' || \substr($pvalue, 0, 1) == "'" && \substr($pvalue, -1) == "'") {
                    $parameters[$pkey] = \substr($pvalue, 1, -1);
                    // remove quote
                }
                if (\substr($pvalue, 0, 1) == '"' && \substr($pvalue, -1) != '"' || \substr($pvalue, 0, 1) == "'" && \substr($pvalue, -1) != "'") {
                    $parameters[$pkey] = \substr($pvalue, 1);
                    // remove quote
                }
            }
        }
        return $parameters;
    }
    public static function fix_filters_whitelist()
    {
        $option = get_option('dce_tokens_filters_whitelist');
        $option = \implode("\n", \array_keys($option));
        update_option('dce_tokens_filters_whitelist', $option);
        return $option;
    }
    /**
     * @return array<string, bool>
     */
    public static function get_filters_whitelist()
    {
        if (self::$filters_whitelist === null) {
            $option = get_option('dce_tokens_filters_whitelist');
            $wl = [];
            if (\is_array($option)) {
                $option = self::fix_filters_whitelist();
            }
            if (\is_string($option)) {
                $list = \explode("\n", $option);
                foreach ($list as $f) {
                    $wl[\trim($f)] = \true;
                }
            }
            self::$filters_whitelist = $wl + self::DEFAULT_FILTERS_WHITELIST;
        }
        return self::$filters_whitelist;
    }
    /**
     * @param string $filter
     * @return bool
     */
    public static function is_filter_safe($filter)
    {
        $wl = self::get_filters_whitelist();
        return $wl[$filter] ?? \false;
    }
    public static function apply_filters($replaceValue = \false, $filtersStr = '', $post_id = 0, $field = '')
    {
        $post_id = $post_id ? $post_id : get_the_ID();
        if (\is_array($filtersStr)) {
            $filtersStr = \implode('|', $filtersStr);
        }
        $parser = new \DynamicContentForElementor\FiltersParser($filtersStr);
        $filters = $parser->parse();
        // Apply Filters
        if (!empty($filters)) {
            foreach ($filters as $flt) {
                $afilter = $flt[0];
                $parameters = $flt[1];
                if ($afilter == 'concatenate' || $afilter == 'concat') {
                    $string2 = \reset($parameters);
                    $replaceValue = self::concatenate($replaceValue, $string2, \count($parameters) > 1);
                    continue;
                }
                if ($afilter == 'add' || $afilter == 'sum') {
                    $string2 = \reset($parameters);
                    $replaceValue = \floatval($replaceValue) + \floatval($string2);
                    continue;
                }
                if ($afilter == 'multiply') {
                    $string2 = \reset($parameters);
                    $replaceValue = \floatval($replaceValue) * \floatval($string2);
                    continue;
                }
                if ($afilter == 'divide') {
                    $string2 = \reset($parameters);
                    $replaceValue = \floatval($replaceValue) / \floatval($string2);
                    continue;
                }
                if ($afilter == 'mod' || $afilter == 'modulo') {
                    $string2 = \reset($parameters);
                    $replaceValue = \floatval($replaceValue) % \floatval($string2);
                    continue;
                }
                if ($afilter == 'exp' || $afilter == 'exponentiation') {
                    $string2 = \reset($parameters);
                    $replaceValue = \floatval($replaceValue) ** \floatval($string2);
                    continue;
                }
                if ($afilter == 'rand' || $afilter == 'random') {
                    if (\is_array($replaceValue) && !empty($replaceValue)) {
                        $replaceValue = $replaceValue[\array_rand($replaceValue)];
                    }
                    continue;
                }
                if ($afilter == 'opts' || $afilter == 'options') {
                    $string2 = \reset($parameters);
                    $replaceValue = \DynamicContentForElementor\Helper::array_options($replaceValue, $string2);
                    continue;
                }
                if ($afilter == 'value' || $afilter == 'field_value') {
                    $string2 = \reset($parameters);
                    $replaceValue = \DynamicContentForElementor\Helper::form_field_value($replaceValue, $string2);
                    continue;
                }
                if ($afilter == 'posts') {
                    $term_id = null;
                    if ($post_id) {
                        $term_id = $post_id;
                    }
                    if (\is_numeric($replaceValue)) {
                        $term_id = \intval($replaceValue);
                    }
                    if (\is_array($replaceValue) && isset($replaceValue['term_id'])) {
                        $term_id = $replaceValue['term_id'];
                    }
                    if (\is_object($replaceValue) && \get_class($replaceValue) == 'WP_Term') {
                        $term_id = $replaceValue->term_id;
                    }
                    if ($term_id) {
                        $replaceValue = \DynamicContentForElementor\Helper::get_term_posts($term_id);
                    } else {
                        $replaceValue = \false;
                    }
                    continue;
                }
                // THUMB Custom Size
                if (\in_array($field, array('thumbnail', 'post_thumbnail', 'thumb'))) {
                    $post_thumbnail_id = get_post_thumbnail_id($post_id);
                    if (\strpos($afilter, 'x') !== \false) {
                        list($h, $w) = \explode('x', $afilter, 2);
                        if (\is_numeric($h) && \is_numeric($w)) {
                            $replaceValueThumb = wp_get_attachment_image_src($post_thumbnail_id, array($w, $h));
                            if ($replaceValueThumb) {
                                $replaceValue = \reset($replaceValueThumb);
                            } else {
                                $replaceValue = '';
                            }
                            continue;
                        }
                    }
                    $replaceValueThumb = wp_get_attachment_image_src($post_thumbnail_id, $afilter);
                    if ($replaceValueThumb) {
                        $replaceValue = \reset($replaceValueThumb);
                    } else {
                        $replaceValue = '';
                    }
                    continue;
                }
                if ($afilter == 'gallery') {
                    $ids = \DynamicContentForElementor\Helper::to_string($replaceValue);
                    $args = array('ids' => $ids);
                    if (isset($parameters[0])) {
                        $args['size'] = $parameters[0];
                    }
                    $replaceValue = gallery_shortcode($args);
                    continue;
                }
                if (apply_filters('dce_tokens_filters_whitelist_force_disable', \false)) {
                    if (isset(self::$filters_blacklist[$afilter])) {
                        continue;
                    }
                } else {
                    if (!self::is_filter_safe($afilter)) {
                        $post_id = get_the_ID();
                        \error_log('Dynamic.ooo non-whitelisted tokens filter found, post: ' . ($post_id ? $post_id : '') . ', filter: ' . $afilter);
                        if (\Elementor\Plugin::$instance->editor->is_edit_mode() || current_user_can('administrator')) {
                            $fmt = esc_html__('Filter %1$s is not whitelisted, please add it to the %2$swhitelist%3$s, you can find it in WP Dashboard > Dynamic.ooo > Settings > Tokens', 'dynamic-content-for-elementor');
                            $url = admin_url('admin.php?page=dce-settings#tab-tokens');
                            return \sprintf("[{$fmt}]", '<code>' . $afilter . '</code>', "<a target='_blank' href='{$url}'>", '</a>');
                        } else {
                            continue;
                        }
                    }
                }
                if ($afilter && \is_callable($afilter) && $replaceValue != '') {
                    if (empty($parameters)) {
                        $replaceValue = $afilter($replaceValue);
                    } else {
                        if ($afilter == 'date') {
                            $afilter = 'date_i18n';
                        }
                        if (\in_array($afilter, array('implode', 'explode', 'date', 'date_i18n', 'get_the_author_meta', 'str_replace', 'preg_replace'))) {
                            // these functions require the value in the last position
                            $parameters[] = $replaceValue;
                        } else {
                            \array_unshift($parameters, $replaceValue);
                        }
                        $replaceValue = \call_user_func_array($afilter, $parameters);
                    }
                }
            }
        }
        return $replaceValue;
    }
    public static function user_to_author($txt)
    {
        return \str_replace('[user', '[author', $txt);
    }
    public static function concatenate($string1 = '', $string2 = '', $before = \false)
    {
        if ($before) {
            return $string2 . $string1;
        }
        return $string1 . $string2;
    }
}
