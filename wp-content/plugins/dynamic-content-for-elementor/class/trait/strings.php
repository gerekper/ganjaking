<?php

namespace DynamicContentForElementor;

trait Strings
{
    /**
     * Custom Function for Remove Specific Tag in the string.
     */
    public static function strip_tag($string, $tag)
    {
        $string = \preg_replace('/<' . $tag . '[^>]*>/i', '', $string);
        $string = \preg_replace('/<\\/' . $tag . '>/i', '', $string);
        return $string;
    }
    public static function escape_json_string($value)
    {
        // # list from www.json.org: (\b backspace, \f formfeed)
        $escapers = array('\\', '/', '"', "\n", "\r", "\t", "\x08", "\f");
        $replacements = array('\\\\', '\\/', '\\"', "\\n", "\\r", "\\t", "\\f", "\\b");
        $result = \str_replace($escapers, $replacements, $value);
        return $result;
    }
    public static function str_to_array($delimiter = ',', $string = '', $format = null)
    {
        if (\is_array($string)) {
            return $string;
        }
        $pieces = \explode($delimiter, $string);
        $pieces = \array_map('trim', $pieces);
        $tmp = array();
        foreach ($pieces as $value) {
            if ($value != '') {
                $tmp[] = $value;
            }
        }
        $pieces = $tmp;
        if ($format) {
            $pieces = \array_map($format, $pieces);
        }
        return $pieces;
    }
    public static function to_string($avalue, $listed = \false)
    {
        if (!\is_array($avalue) && !\is_object($avalue)) {
            return $avalue;
        }
        if (\is_object($avalue) && \get_class($avalue) == 'WP_Term') {
            return $avalue->name;
        }
        if (\is_object($avalue) && \get_class($avalue) == 'WP_Post') {
            return $avalue->post_title;
        }
        if (\is_object($avalue) && \get_class($avalue) == 'WP_User') {
            return $avalue->display_name;
        }
        if (\is_object($avalue)) {
            $avalue = (array) $avalue;
        }
        if (\is_array($avalue)) {
            if (isset($avalue['post_title'])) {
                return $avalue['post_title'];
            }
            if (isset($avalue['display_name'])) {
                return $avalue['display_name'];
            }
            if (isset($avalue['name'])) {
                return $avalue['name'];
            }
            if (\count($avalue) == 1) {
                $first = \reset($avalue);
                return self::to_string($first);
            }
            return self::implode_recursive(', ', $avalue, $listed);
        }
    }
    public static function vc_strip_shortcodes($content)
    {
        $tmp = $content;
        $tags = array('[/vc_', '[vc_', '[dt_', '[interactive_banner_2');
        foreach ($tags as $atag) {
            $pezzi = \explode($atag, $tmp);
            if (\count($pezzi) > 1) {
                $content_mod = '';
                foreach ($pezzi as $key => $value) {
                    $altro = \explode(']', $value, 2);
                    $content_mod .= \end($altro);
                }
                $tmp = $content_mod;
            }
        }
        return $tmp;
    }
    public static function text_reduce($text, $length, $length_type, $finish)
    {
        $tokens = array();
        $out = '';
        $w = 0;
        // (<[^>]+>|[^<>\s]+\s*)
        \preg_match_all('/(<[^>]+>|[^<>\\s]+)\\s*/u', $text, $tokens);
        foreach ($tokens[0] as $t) {
            // Parse each token
            if ($w >= $length && 'sentence' != $finish) {
                // Limit reached
                break;
            }
            if ($t[0] != '<') {
                // Token is not a tag
                if ($w >= $length && 'sentence' == $finish && \preg_match('/[\\?\\.\\!]\\s*$/uS', $t) == 1) {
                    // Limit reached, continue until ? . or ! occur at the end
                    $out .= \trim($t);
                    break;
                }
                if ('words' == $length_type) {
                    // Count words
                    $w++;
                } else {
                    // Count/trim characters
                    if ($finish == 'exact_w_spaces') {
                        $chars = $t;
                    } else {
                        $chars = \trim($t);
                    }
                    $c = \mb_strlen($chars);
                    if ($c + $w > $length && 'sentence' != $finish) {
                        // Token is too long
                        $c = 'word' == $finish ? $c : $length - $w;
                        // Keep token to finish word
                        $t = \substr($t, 0, $c);
                    }
                    $w += $c;
                }
            }
            // Append what's left of the token
            $out .= $t;
        }
        return \trim(force_balance_tags($out));
    }
    //+exclude_start
    public static function tablefy($html = '')
    {
        $dom = new \DynamicOOOS\PHPHtmlParser\Dom();
        $dom->loadStr($html);
        foreach ($dom->find('.elementor-container') as $tag) {
            $changeTagTable = function () {
                $this->name = 'table';
            };
            $changeTagTable->call($tag->tag);
        }
        foreach ($dom->find('.elementor-row') as $tag) {
            $changeTagTr = function () {
                $this->name = 'tr';
            };
            $changeTagTr->call($tag->tag);
        }
        foreach ($dom->find('.elementor-column') as $tag) {
            $changeTagTd = function () {
                $this->name = 'td';
            };
            $changeTagTd->call($tag->tag);
        }
        $html_table = (string) $dom;
        return $html_table;
    }
    //+exclude_end
    public static function get_array_value_by_keys($array = array(), $keys = array())
    {
        if (!empty($keys)) {
            $key = \array_shift($keys);
            if (isset($array[$key])) {
                if (\is_array($array[$key])) {
                    return self::get_array_value_by_keys($array[$key], $keys);
                } else {
                    return $array[$key];
                }
            } else {
                return \false;
            }
        }
        return $array;
    }
    public static function set_array_value_by_keys($array = array(), $keys = array(), $value = \false)
    {
        if (!empty($keys)) {
            $key = \array_shift($keys);
            if (!\is_array($array)) {
                $array = array();
            }
            if (!isset($array[$key])) {
                $array[$key] = array();
            }
            $array[$key] = self::set_array_value_by_keys($array[$key], $keys, $value);
            if (\is_null($array[$key]) && \is_null($value)) {
                unset($array[$key]);
            }
        } else {
            $array = $value;
        }
        return $array;
    }
    public static function implode_recursive($separator = ', ', $arrayvar = array(), $listed = \false)
    {
        $output = '';
        if (!empty($arrayvar) && \is_array($arrayvar)) {
            if ($listed) {
                $output .= '<ul>';
            }
            $i = 0;
            foreach ($arrayvar as $av) {
                if ($listed) {
                    $output .= '<li>';
                }
                if (\is_object($av)) {
                    $av = self::to_string($av);
                }
                if (\is_array($av)) {
                    $output .= self::implode_recursive($separator, $av, $listed);
                    // Recursive Use of the Array
                } else {
                    if ($i) {
                        $output .= $separator . $av;
                    } else {
                        $output .= $av;
                    }
                }
                if ($listed) {
                    $output .= '</li>';
                }
                $i++;
            }
            if ($listed) {
                $output .= '</ul>';
            }
        }
        return $output;
    }
}
