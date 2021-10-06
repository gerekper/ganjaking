<?php
/**
 * Description of A2W_Helper
 *
 * @author Andrey
 */
if (!class_exists('A2W_Helper')) {

    class A2W_Helper {
        public function image_http_to_https($image_url) {
            //return preg_replace("/http:\/\/g(\d+)\.a\./i", "https://ae$1.", strval($image_url));
            return str_replace("http://", "https://", $image_url);
        }

        public function image_https_to_http($image_url) {
            return preg_replace("/https:\/\/ae(\d+)\./i", "http://g$1.a.", strval($image_url));
        }

        public function clear_html($in_html) {
            if (!$in_html)
                return "";
            $html = preg_replace('~<(?:!DOCTYPE|/?(?:html|body))[^>]*>\s*~i', '', $in_html);
            $html = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $html);
            $html = preg_replace('/(<[^>]+) class=".*?"/i', '$1', $html);
            $html = preg_replace('/(<[^>]+) width=".*?"/i', '$1', $html);
            $html = preg_replace('/(<[^>]+) height=".*?"/i', '$1', $html);
            $html = preg_replace('/(<[^>]+) alt=".*?"/i', '$1', $html);
            $html = preg_replace('/^<!DOCTYPE.+?>/', '$1', str_replace(array('<html>', '</html>', '<body>', '</body>'), '', $html));
            $html = preg_replace("/<\/?div[^>]*\>/i", "", $html);

            $html = preg_replace('#(<a.*?>).*?(</a>)#', '$1$2', $html);
            $html = preg_replace('/<a[^>]*>(.*)<\/a>/iU', '', $html);
            $html = preg_replace("/<\/?h1[^>]*\>/i", "", $html);
            $html = preg_replace("/<\/?strong[^>]*\>/i", "", $html);
            $html = preg_replace("/<\/?span[^>]*\>/i", "", $html);

            //$html = str_replace(' &nbsp; ', '', $html);
            $html = str_replace('&nbsp;', ' ', $html);
            $html = str_replace('\t', ' ', $html);
            $html = str_replace('  ', ' ', $html);


            $html = preg_replace("/http:\/\/g(\d+)\.a\./i", "https://ae$1.", $html);

            $pattern = "/<[^\/>]*>([\s]?)*<\/[^>]*>/";
            $html = preg_replace($pattern, '', $html);

            $html = str_replace(array('<img', '<table'), array('<img class="img-responsive"', '<table class="table table-bordered'), $html);
            $html = force_balance_tags($html);

            return $html;
        }

        public function clean_woocommerce_product_attributes($product_id, $used_attributes) {
            global $wpdb;
            $escAttrs = array();
            foreach($used_attributes as $a){
                $escAttrs[] = "'".esc_sql($a)."'";
            }

            if(!empty($escAttrs)){
                $sql = "DELETE tr FROM {$wpdb->term_relationships} tr INNER JOIN {$wpdb->term_taxonomy} tt ON (tt.term_taxonomy_id=tr.term_taxonomy_id) INNER JOIN {$wpdb->prefix}woocommerce_attribute_taxonomies wat on(CONCAT('pa_',wat.attribute_name)=tt.taxonomy) WHERE tr.object_id = %s and tt.taxonomy not in (". implode(',', $escAttrs) .")";
                $wpdb->query($wpdb->prepare($sql, $product_id));
            }
        }

        public function set_woocommerce_attributes($post_id, $attributes = array()) {
            global $wpdb;
            global $woocommerce;

            // convert Amazon attributes into woocommerce attributes
            $_product_attributes = array();
            $position = 0;

            foreach ($attributes as $attr) {
                $key = $attr['name'];
                $value = $attr['value'];


                if (!is_object($value)) {
                    // change dimension name as woocommerce attribute name
                    $attribute_name = $this->cleanTaxonomyName(strtolower($key));

                    // Clean
                    if (is_array($value)) {
                        foreach($value as $k=>$v){
                            $value[$k] = $this->cleanValue($v);
                        }
                    }else{
                        $value = $this->cleanValue($value);
                    }

                    // if is empty attribute don't import
                    if (empty($value)){
                        continue;
                    }

                    $_product_attributes[$attribute_name] = array(
                        'name' => $attribute_name,
                        'value' => is_array($value)?implode(", ", $value):$value,
                        'position' => $position++,
                        'is_visible' => 1,
                        'is_variation' => 0,
                        'is_taxonomy' => 1
                    );

                    $this->add_attribute($post_id, $key, $value);
                }
            }

            // update product attribute
            update_post_meta($post_id, '_product_attributes', $_product_attributes);

            $this->attrclean_clean_all('array'); // delete duplicate attributes
            // refresh attribute cache
            //$dmtransient_name = 'wc_attribute_taxonomies';
            //$dmattribute_taxonomies = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "woocommerce_attribute_taxonomies");
            //set_transient($dmtransient_name, $dmattribute_taxonomies);

            flush_rewrite_rules();
            a2w_delete_transient('wc_attribute_taxonomies');
        }

        // add woocommrce attribute values
        public function add_attribute($post_id, $key, $value) {
            global $wpdb;

            // avoid object to be inserted in terms
            if (is_object($value)) {
                return;
            }

            // get attribute name, label
            $attribute_label = $key;

            $attribute_name = $this->cleanTaxonomyName($key, false);

            // set attribute type
            $attribute_type = 'select';

            // check for duplicates
            $attribute_taxonomies = $wpdb->get_var("SELECT * FROM {$wpdb->prefix}woocommerce_attribute_taxonomies WHERE attribute_name = '" . esc_sql($attribute_name) . "'");

            if ($attribute_taxonomies) {
                // update existing attribute
                $wpdb->update(
                        $wpdb->prefix . 'woocommerce_attribute_taxonomies', array(
                            'attribute_name' => $attribute_name
                        ), array('attribute_name' => $attribute_name)
                );
            } else {
                // add new attribute
                $wpdb->insert(
                        $wpdb->prefix . 'woocommerce_attribute_taxonomies', array(
                            'attribute_label' => $attribute_label,
                            'attribute_name' => $attribute_name,
                            'attribute_type' => $attribute_type,
                            'attribute_orderby' => 'name'
                        )
                );
            }

            // add attribute values if not exist
            $taxonomy = $this->cleanTaxonomyName($attribute_name);

            $values = is_array($value)?$value:array($value);

            // check taxonomy
            if (!taxonomy_exists($taxonomy)) {
                // add attribute value
                foreach ($values as $attribute_value) {
                    $attribute_value = (string) $attribute_value;

                    if (is_string($attribute_value)) {
                        // add term
                        $name = $this->cleanValue($attribute_value);
                        $slug = sanitize_title($name);
                        
                        if (!term_exists($name)) {
                            if (trim($slug) != '' && trim($name) != '') {
                                $this->db_custom_insert($wpdb->terms, array('values' => array('name' => $name, 'slug' => $slug), 'format' => array('%s', '%s')), true);

                                // add term taxonomy
                                $term_id = $wpdb->insert_id;
                                $this->db_custom_insert($wpdb->term_taxonomy, array('values' => array('term_id' => $term_id, 'taxonomy' => $taxonomy), 'format' => array('%d', '%s')), true);

                                $term_taxonomy_id = $wpdb->insert_id;
                            }
                        } else {
                            // add term taxonomy
                            $term_id = $wpdb->get_var("SELECT term_id FROM {$wpdb->terms} WHERE name = '" . esc_sql($name) . "'");
                            $this->db_custom_insert($wpdb->term_taxonomy, array('values' => array('term_id' => $term_id, 'taxonomy' => $taxonomy), 'format' => array('%d', '%s')), true);
                            $term_taxonomy_id = $wpdb->insert_id;
                        }
                    }
                }
            }

            if (!empty($values)) {
                $attribute_slugs = array();
                $terms = $this->load_terms($taxonomy);
                foreach ($terms as $term) {
                    $attribute_slugs[] = strval($term->slug);
                }
                // Add terms
                foreach ($values as $attribute_value) {
                    $attribute_value = $this->cleanValue((string) $attribute_value);
                    $attribute_slug = sanitize_title(htmlspecialchars($attribute_value, ENT_NOQUOTES));
                    if (!in_array(strval($attribute_slug), $attribute_slugs, true)) {
                        wp_insert_term($attribute_value, $taxonomy, array('slug'=>$attribute_slug));
                    }
                }
            }
            // wp_term_relationships (object_id to term_taxonomy_id)
            if (!empty($values)) {
                foreach ($values as $term) {

                    if (!is_array($term) && !is_object($term)) {
                        $term = sanitize_title(htmlspecialchars($term, ENT_NOQUOTES));

                        $term_taxonomy_id = $wpdb->get_var("SELECT tt.term_taxonomy_id FROM {$wpdb->terms} AS t INNER JOIN {$wpdb->term_taxonomy} as tt ON tt.term_id = t.term_id WHERE t.slug = '" . esc_sql($term) . "' AND tt.taxonomy = '" . esc_sql($taxonomy) . "'");

                        if ($term_taxonomy_id) {
                            $checkSql = "SELECT * FROM {$wpdb->term_relationships} WHERE object_id = {$post_id} AND term_taxonomy_id = {$term_taxonomy_id}";
                            if (!$wpdb->get_var($checkSql)) {
                                $wpdb->insert($wpdb->term_relationships, array('object_id' => $post_id, 'term_taxonomy_id' => $term_taxonomy_id));
                            }
                        }
                    }
                }
            }

            // Update WPML translates
            global $woocommerce_wpml;
            if (isset($woocommerce_wpml) && property_exists($woocommerce_wpml, 'attributes') && $woocommerce_wpml->attributes) {
                $woocommerce_wpml->attributes->set_attribute_config_in_settings( $taxonomy, 1 );
            }
        }

        public function attrclean_clean_all($retType = 'die') {
            // :: get duplicates list
            $duplicates = $this->attrclean_getDuplicateList();

            if (empty($duplicates) || !is_array($duplicates)) {
                $ret['status'] = 'valid';
                $ret['msg_html'] = 'no duplicate terms found!';
                if ($retType == 'die')
                    die(json_encode($ret));
                else
                    return $ret;
            }
            // html message
            $__duplicates = array();
            $__duplicates[] = '0 : name, slug, term_taxonomy_id, taxonomy, count';
            foreach ($duplicates as $key => $value) {
                $__duplicates[] = $value->name . ' : ' . implode(', ', (array) $value);
            }
            $ret['status'] = 'valid';
            $ret['msg_html'] = implode('<br />', $__duplicates);
            // if ( $retType == 'die' ) die(json_encode($ret));
            // else return $ret;
            // :: get terms per duplicate
            $__removeStat = array();
            $__terms = array();
            $__terms[] = '0 : term_id, name, slug, term_taxonomy_id, taxonomy, count';
            foreach ($duplicates as $key => $value) {
                $terms = $this->attrclean_getTermPerDuplicate($value->name, $value->taxonomy);
                if (empty($terms) || !is_array($terms) || count($terms) < 2)
                    continue 1;

                $first_term = array_shift($terms);

                // html message
                foreach ($terms as $k => $v) {
                    $__terms[] = $key . ' : ' . implode(', ', (array) $v);
                }

                // :: remove duplicate term
                $removeStat = $this->attrclean_removeDuplicate($first_term->term_id, $terms, false);

                // html message
                $__removeStat[] = '-------------------------------------- ' . $key;
                $__removeStat[] = '---- term kept';
                $__removeStat[] = 'term_id, term_taxonomy_id';
                $__removeStat[] = $first_term->term_id . ', ' . $first_term->term_taxonomy_id;
                foreach ($removeStat as $k => $v) {
                    $__removeStat[] = '---- ' . $k;
                    if (!empty($v) && is_array($v)) {
                        foreach ($v as $k2 => $v2) {
                            $__removeStat[] = implode(', ', (array) $v2);
                        }
                    } else if (!is_array($v)) {
                        $__removeStat[] = (int) $v;
                    } else {
                        $__removeStat[] = 'empty!';
                    }
                }
            }

            $ret['status'] = 'valid';
            $ret['msg_html'] = implode('<br />', $__removeStat);
            if ($retType == 'die')
                die(json_encode($ret));
            else
                return $ret;
        }

        /**
         * Attributes clean duplicate
         */
        public function attrclean_getDuplicateList() {
            global $wpdb;

            // $q = "SELECT COUNT(a.term_id) AS nb, a.name, a.slug FROM {$wpdb->terms} AS a WHERE 1=1 GROUP BY a.name HAVING nb > 1;";
            $q = "SELECT COUNT(a.term_id) AS nb, a.name, a.slug, b.term_taxonomy_id, b.taxonomy, b.count FROM {$wpdb->terms} AS a
 LEFT JOIN {$wpdb->term_taxonomy} AS b ON a.term_id = b.term_id
 WHERE 1=1 AND b.taxonomy REGEXP '^pa_' GROUP BY a.name, b.taxonomy HAVING nb > 1
 ORDER BY a.name ASC
;";
            $res = $wpdb->get_results($q);
            if (!$res || !is_array($res))
                return false;

            $ret = array();
            foreach ($res as $key => $value) {
                $name = $value->name;
                $taxonomy = $value->taxonomy;
                $ret["$name@@$taxonomy"] = $value;
            }
            return $ret;
        }

        public function attrclean_getTermPerDuplicate($term_name, $taxonomy) {
            global $wpdb;

            $q = "SELECT a.term_id, a.name, a.slug, b.term_taxonomy_id, b.taxonomy, b.count FROM {$wpdb->terms} AS a
 LEFT JOIN {$wpdb->term_taxonomy} AS b ON a.term_id = b.term_id
 WHERE 1=1 AND a.name=%s AND b.taxonomy=%s ORDER BY a.slug ASC;";
            $q = $wpdb->prepare($q, $term_name, $taxonomy);
            $res = $wpdb->get_results($q);
            if (!$res || !is_array($res))
                return false;

            $ret = array();
            foreach ($res as $key => $value) {
                $ret[$value->term_taxonomy_id] = $value;
            }
            return $ret;
        }

        public function attrclean_removeDuplicate($first_term, $terms = array(), $debug = false) {
            if (empty($terms) || !is_array($terms))
                return false;

            $term_id = array();
            $term_taxonomy_id = array();
            foreach ($terms as $k => $v) {
                $term_id[] = $v->term_id;
                $term_taxonomy_id[] = $v->term_taxonomy_id;
                $taxonomy = $v->taxonomy;
            }
            // var_dump('<pre>',$first_term, $term_id, $term_taxonomy_id, $taxonomy,'</pre>');  

            $ret = array();
            $ret['term_relationships'] = $this->attrclean_remove_term_relationships($first_term, $term_taxonomy_id, $debug);
            $ret['terms'] = $this->attrclean_remove_terms($term_id, $debug);
            $ret['term_taxonomy'] = $this->attrclean_remove_term_taxonomy($term_taxonomy_id, $taxonomy, $debug);
            // var_dump('<pre>',$ret,'</pre>');  
            return $ret;
        }

        private function attrclean_remove_term_relationships($first_term, $term_taxonomy_id, $debug = false) {
            global $wpdb;

            $idList = (is_array($term_taxonomy_id) && count($term_taxonomy_id) > 0 ? implode(', ', array_map(array($this, 'prepareForInList'), $term_taxonomy_id)) : 0);

            if ($debug) {
                $q = "SELECT a.object_id, a.term_taxonomy_id FROM {$wpdb->term_relationships} AS a
 WHERE 1=1 AND a.term_taxonomy_id IN (%s) ORDER BY a.object_id ASC, a.term_taxonomy_id;";
                $q = sprintf($q, $idList);
                $res = $wpdb->get_results($q);
                if (!$res || !is_array($res))
                    return false;

                $ret = array();
                $ret[] = 'object_id, term_taxonomy_id';
                foreach ($res as $key => $value) {
                    $term_taxonomy_id = $value->term_taxonomy_id;
                    $ret["$term_taxonomy_id"] = $value;
                }
                return $ret;
            }

            // execution/ update
            $q = "UPDATE {$wpdb->term_relationships} AS a SET a.term_taxonomy_id = '%s' 
 WHERE 1=1 AND a.term_taxonomy_id IN (%s);";
            $q = sprintf($q, $first_term, $idList);
            $res = $wpdb->query($q);
            $ret = $res;
            return $ret;
        }

        private function attrclean_remove_terms($term_id, $debug = false) {
            global $wpdb;

            $idList = (is_array($term_id) && count($term_id) > 0 ? implode(', ', array_map(array($this, 'prepareForInList'), $term_id)) : 0);

            if ($debug) {
                $q = "SELECT a.term_id, a.name FROM {$wpdb->terms} AS a
 WHERE 1=1 AND a.term_id IN (%s) ORDER BY a.name ASC;";
                $q = sprintf($q, $idList);
                $res = $wpdb->get_results($q);
                if (!$res || !is_array($res))
                    return false;

                $ret = array();
                $ret[] = 'term_id, name';
                foreach ($res as $key => $value) {
                    $term_id = $value->term_id;
                    $ret["$term_id"] = $value;
                }
                return $ret;
            }

            // execution/ update
            $q = "DELETE FROM a USING {$wpdb->terms} as a WHERE 1=1 AND a.term_id IN (%s);";
            $q = sprintf($q, $idList);
            $res = $wpdb->query($q);
            $ret = $res;
            return $ret;
        }

        private function attrclean_remove_term_taxonomy($term_taxonomy_id, $taxonomy, $debug = false) {
            global $wpdb;

            $idList = (is_array($term_taxonomy_id) && count($term_taxonomy_id) > 0 ? implode(', ', array_map(array($this, 'prepareForInList'), $term_taxonomy_id)) : 0);

            if ($debug) {
                $q = "SELECT a.term_id, a.taxonomy, a.term_taxonomy_id FROM {$wpdb->term_taxonomy} AS a
 WHERE 1=1 AND a.term_taxonomy_id IN (%s) AND a.taxonomy = '%s' ORDER BY a.term_taxonomy_id ASC;";
                $q = sprintf($q, $idList, esc_sql($taxonomy));
                $res = $wpdb->get_results($q);
                if (!$res || !is_array($res))
                    return false;

                $ret = array();
                $ret[] = 'term_id, taxonomy, term_taxonomy_id';
                foreach ($res as $key => $value) {
                    $term_taxonomy_id = $value->term_taxonomy_id;
                    $ret["$term_taxonomy_id"] = $value;
                }
                return $ret;
            }

            // execution/ update
            $q = "DELETE FROM a USING {$wpdb->term_taxonomy} as a WHERE 1=1 AND a.term_taxonomy_id IN (%s) AND a.taxonomy = '%s';";
            $q = sprintf($q, $idList, $taxonomy);
            $res = $wpdb->query($q);
            $ret = $res;
            return $ret;
        }

        public function load_terms($taxonomy) {
            global $wpdb;
            $query = "SELECT DISTINCT t.name, t.slug FROM {$wpdb->terms} AS t INNER JOIN {$wpdb->term_taxonomy} as tt ON tt.term_id = t.term_id WHERE 1=1 AND tt.taxonomy = '" . esc_sql($taxonomy) . "'";
            $result = $wpdb->get_results($query, OBJECT);
            return $result;
        }

        public function db_custom_insert($table, $fields, $ignore = false, $wp_way = false) {
            global $wpdb;
            if ($wp_way && !$ignore) {
                $wpdb->insert($table, $fields['values'], $fields['format']);
            } else {
                $formatVals = implode(', ', array_map(array($this, 'prepareForInList'), $fields['format']));
                $theVals = array();
                foreach ($fields['values'] as $k => $v)
                    $theVals[] = $k;

                $q = "INSERT " . ($ignore ? "IGNORE" : "") . " INTO $table (" . implode(', ', $theVals) . ") VALUES (" . $formatVals . ");";
                foreach ($fields['values'] as $kk => $vv)
                    $fields['values']["$kk"] = esc_sql($vv);

                $q = vsprintf($q, $fields['values']);
                $r = $wpdb->query($q);
            }
            return $wpdb->insert_id;
        }

        public function prepareForInList($v) {
            return "'" . $v . "'";
        }

        public function cleanTaxonomyName($value, $withPrefix = true, $checkSize = true) {
            $ret = $value;

            // Sanitize taxonomy names. Slug format (no spaces, lowercase) - uses sanitize_title
            if ($withPrefix) {
                $ret = wc_attribute_taxonomy_name($value); // return 'pa_' . $value
            } else {
                $ret = wc_sanitize_taxonomy_name($value); // return $value
            }

            if($checkSize){
                // limit to 32 characters (database/ table wp_term_taxonomy/ field taxonomy/ is limited to varchar(32) )
                if (seems_utf8($ret)) {
                    $limit_max = $withPrefix ? 18 : 15; // utf8: 3 + 29/2
                    if (function_exists('mb_substr')) {
                        $ret = mb_substr($ret, 0, $limit_max);
                    }
                }else{
                    $limit_max = $withPrefix ? 32 : 29; // 29 = 32 - strlen('pa_')
                    $ret = substr($ret, 0, $limit_max);
                }
            }
            
            // IMPORTANT, if not sure do not need sanitize_title!
            return $ret;
        }

        public function cleanValue($value) {
            // Format Camel Case
            //$value = trim( preg_replace('/([A-Z])/', ' $1', $value) );
            // Clean / from value
            $value = trim(preg_replace('/(\/)/', '-', $value));
            return $value;
        }

        public function multi_implode($array, $glue) {
            $ret = '';
            foreach ($array as $item) {
                if (is_array($item)) {
                    $ret .= $this->multi_implode($item, $glue) . $glue;
                } else {
                    $ret .= $item . $glue;
                }
            }
            $ret = substr($ret, 0, 0 - strlen($glue));
            return $ret;
        }

    }

}
