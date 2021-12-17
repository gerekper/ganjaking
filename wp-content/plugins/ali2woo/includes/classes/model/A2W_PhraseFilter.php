<?php

/**
 * Description of A2W_PhraseFilter
 *
 * @author ma_group
 */
class A2W_PhraseFilter
{

    public $id = 0;
    public $phrase = '';
    public $phrase_replace = '';
    private static $_instance_objects = null;
    private static $_instance_array = null;

    public function __construct($data = 0)
    {
        if (is_int($data) && $data) {
            $this->id = $data;
            $this->load($this->id);
        } else if (is_array($data)) {
            foreach ($data as $field => $value) {
                if (property_exists(get_class($this), $field)) {
                    $this->$field = stripslashes(trim($value));
                }
            }
        }
    }

    public function save()
    {
        A2W_PhraseFilter::$_instance_objects = null;
        A2W_PhraseFilter::$_instance_array = null;

        $formula_list = A2W_PhraseFilter::load_phrase_list(false);

        if (!intval($this->id)) {
            $this->id = 1;
            foreach ($formula_list as $key => $formula) {
                if (intval($formula['id']) >= $this->id) {
                    $this->id = intval($formula['id']) + 1;
                }
            }
            $formula_list[] = get_object_vars($this);
        } else {
            $boolean = false;
            foreach ($formula_list as $key => $formula) {
                if (intval($formula['id']) === intval($this->id)) {
                    $formula_list[$key] = get_object_vars($this);
                    $boolean = true;
                }
            }
            if (!$boolean) {
                $formula_list[] = get_object_vars($this);
            }
        }

        a2w_set_setting('phrase_list', array_values($formula_list));
        return $this;
    }

    public function delete()
    {
        $formula_list = A2W_PhraseFilter::load_phrase_list(false);
        foreach ($formula_list as $key => $formula) {
            if (intval($formula['id']) === intval($this->id)) {
                unset($formula_list[$key]);
                a2w_set_setting('phrase_list', array_values($formula_list));
            }
        }
    }

    public static function apply_filter_to_text($text, $phrase_list = array())
    {
        if (empty($text)) {
            return $text;
        }

        if (!$phrase_list) {
            $phrase_list = A2W_PhraseFilter::load_phrase_list(false);
        }

        if (!empty($phrase_list)) {

            if (function_exists('libxml_use_internal_errors')) {libxml_use_internal_errors(true);}

            if ($text && class_exists('DOMDocument')) {
                $urls = array();

                $doc = new DOMDocument();
                $doc->loadHTML($text);

                $elements = $doc->getElementsByTagName('a');
                if ($elements->length >= 1) {
                    foreach ($elements as $element) {
                        $url = $element->getAttribute('href');
                        if (trim($url)) {
                            $urls[md5($url)] = $url;
                        }
                    }
                }

                $elements = $doc->getElementsByTagName('img');
                if ($elements->length >= 1) {
                    foreach ($elements as $element) {
                        $url = $element->getAttribute('src');
                        if (trim($url)) {
                            $urls[md5($url)] = $url;
                        }
                    }
                }

                $search_a = array();
                $replace_a = array();
                foreach ($urls as $key => $url) {
                    $search_a[] = '/' . preg_quote($url, '/') . '/u';
                    $replace_a[] = $key;
                }
                $text = preg_replace($search_a, $replace_a, $text);

                $phrase = array();
                $phrase_replace = array();
                foreach ($phrase_list as $p) {
                    $phrase[] = '/' . preg_quote($p['phrase'], '/') . '/u';
                    $phrase_replace[] = $p['phrase_replace'];
                }
                $text = preg_replace($phrase, $phrase_replace, $text);

                $search_a = array();
                $replace_a = array();
                foreach ($urls as $key => $url) {
                    $search_a[] = '/' . preg_quote($key, '/') . '/u';
                    $replace_a[] = $url;
                }
                $text = preg_replace($search_a, $replace_a, $text);
            }
        }

        return $text;
    }

    /**
     * Apply phrase filter to wc product (title, description, attributes)
     *
     * @param mixed $product
     */
    public static function apply_filter_to_product($product)
    {

        if ($product['title']) {
            $product['title'] = self::apply_filter_to_text($product['title']);
        }

        if ($product['description']) {
            $product['description'] = self::apply_filter_to_text($product['description']);
        }

        if (isset($product['attribute']) && is_array($product['attribute'])) {
            foreach ($product['attribute'] as &$attr) {
                $attr['name'] = self::apply_filter_to_text($attr['name']);
                if (is_array($attr['value'])) {
                    foreach ($attr['value'] as $k => $v) {
                        $attr['value'][$k] = self::apply_filter_to_text($attr['value'][$k]);
                    }
                } else {
                    $attr['value'] = self::apply_filter_to_text($attr['value']);
                }
            }
        }

        return $product;
    }

    public static function apply_filter_to_products()
    {
        global $wpdb;

        $phrase_list = A2W_PhraseFilter::load_phrase_list(false);

        $phrase_query_array = array();
        foreach ($phrase_list as $phrase) {
            $phrase_query_array[] = $wpdb->prepare("post_title LIKE %s OR post_content LIKE %s", array('%' . $wpdb->esc_like($phrase['phrase']) . '%', '%' . $wpdb->esc_like($phrase['phrase']) . '%'));
        }

        $tmp_product_ids = $wpdb->get_results("SELECT p.ID, p.post_title, p.post_content from {$wpdb->posts} p LEFT JOIN {$wpdb->postmeta} as pm ON pm.post_id = p.ID WHERE (" . implode(" OR ", $phrase_query_array) . ") AND pm.meta_key = '_a2w_external_id'", ARRAY_N);
        foreach ($tmp_product_ids as $row) {
            $new_title = A2W_PhraseFilter::apply_filter_to_text($row[1], $phrase_list);
            $new_content = A2W_PhraseFilter::apply_filter_to_text($row[2], $phrase_list);
            if ($new_title != $row[1] || $new_content != $row[2]) {
                $wpdb->query($wpdb->prepare("UPDATE {$wpdb->posts} p SET p.post_title = %s, p.post_content = %s WHERE p.ID=%s", array($new_title, $new_content, $row[0])));
            }
        }
    }

    /**
     * Apply phrase filter to reviews keeping in database currently (review author, review text)
     */
    public static function apply_filter_to_reviews()
    {
        global $wpdb;

        $phrase_list = A2W_PhraseFilter::load_phrase_list(false);

        $phrase_query_array = array();
        foreach ($phrase_list as $phrase) {
            $phrase_query_array[] = $wpdb->prepare("comment_author LIKE %s OR comment_content LIKE %s", array('%' . $wpdb->esc_like($phrase['phrase']) . '%', '%' . $wpdb->esc_like($phrase['phrase']) . '%'));
        }

        $tmp_comment_ids = $wpdb->get_results("SELECT c.comment_ID, c.comment_author, c.comment_content from {$wpdb->comments} c LEFT JOIN {$wpdb->commentmeta} as cm ON cm.comment_id = c.comment_ID WHERE (" . implode(" OR ", $phrase_query_array) . ") AND cm.meta_key = 'a2w_country'", ARRAY_N);
        foreach ($tmp_comment_ids as $row) {
            $new_author = A2W_PhraseFilter::apply_filter_to_text($row[1], $phrase_list);
            $new_content = A2W_PhraseFilter::apply_filter_to_text($row[2], $phrase_list);
            if ($new_author != $row[1] || $new_content != $row[2]) {
                $wpdb->query($wpdb->prepare("UPDATE {$wpdb->comments} c SET c.comment_author = %s, c.comment_content = %s WHERE c.comment_ID=%d", array($new_author, $new_content, $row[0])));
            }
        }
    }

    public static function deleteAll()
    {
        a2w_del_setting('phrase_list');
    }

    public static function load_phrases()
    {
        return A2W_PhraseFilter::load_phrase_list(true);
    }

    private static function load_phrase_list($asObject = true)
    {
        $result = array();

        if ($asObject && A2W_PhraseFilter::$_instance_objects) {
            return A2W_PhraseFilter::$_instance_objects;
        } else if (!$asObject && A2W_PhraseFilter::$_instance_array) {
            return A2W_PhraseFilter::$_instance_array;
        }

        $formula_list = a2w_get_setting('phrase_list');
        $formula_list = $formula_list && is_array($formula_list) ? $formula_list : array();

        if ($asObject) {
            foreach ($formula_list as $formula) {
                $fo = new A2W_PhraseFilter();
                foreach ($formula as $name => $value) {
                    if (property_exists(get_class($fo), $name)) {
                        $fo->$name = $value;
                    }
                }
                $result[] = $fo;
                A2W_PhraseFilter::$_instance_objects = $result;
            }
        } else {
            $result = $formula_list;
            A2W_PhraseFilter::$_instance_array = $result;
        }

        return $result;
    }

}

if (!function_exists('a2w_phrase_apply_filter_to_text')) {

    function a2w_phrase_apply_filter_to_text($text)
    {
        return A2W_PhraseFilter::apply_filter_to_text($text);
    }

}
