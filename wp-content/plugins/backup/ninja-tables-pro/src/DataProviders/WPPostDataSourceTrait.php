<?php

namespace NinjaTablesPro\DataProviders;

use NinjaTables\Classes\ArrayHelper;

trait WPPostDataSourceTrait
{
    private $__queryable_postColumns__ = array();

    private $acf_installed = false;

    public function buildWPQuery($data)
    {
        $columns = isset($data['formatted_columns']) ? $data['formatted_columns'] : array();
        $tableId = $data['tableId'];
        $whereClauses = isset($data['where']) ? $data['where'] : array();
        $whereClauses = is_array($whereClauses) ? $whereClauses : array();
        $metas = isset($data['metas']) ? $data['metas'] : array();
        $postTypes = isset($data['post_types']) ? $data['post_types'] : array();
        $perPage = $this->get($data, 'per_page', -1);
        $offset = $this->get($data, 'offset', 0);
        $queryExtra = $this->getQueryExtra($tableId);

        $args = array(
            'post_type'      => $postTypes,
            'posts_per_page' => $perPage,
            'offset'         => $offset,
            'order'          => $queryExtra['order_by'],
            'orderby'        => $queryExtra['order_by_column']
        );

        $args = $this->buildQueryArgsForPostFields($args, $whereClauses);

        $args = $this->buildQueryArgsForTaxonomies($args, $whereClauses);

        $args = $this->buildMetaQuery($args, $metas);


        $args = apply_filters('ninja_table_post_table_args', $args, $data);
        $args = apply_filters('ninja_table_post_table_args_' . $tableId, $args, $data);
        $query = (new \WP_Query($args));
        $posts = $query->posts;

        $formattedPosts = array();

        foreach ($posts as $post_index => $post) {
            $data = array();
            foreach ($columns as $column_key => $column) {
                if ($column['type'] == 'post_data') {
                    $data[$column_key] = $this->getPostData($post, $column);
                } else if ($column['type'] == 'tax_data') {
                    $data[$column_key] = $this->getTaxData($post, $column);
                } else if ($column['type'] == 'custom') {
                    $data[$column_key] = $this->getCustomData($post, $column);
                } else if ($column['type'] == 'author_data') {
                    $data[$column_key] = $this->getAuthorData($post, $column);
                }
            }
            $formattedPosts[] = $data;
        }
        return $formattedPosts;
    }

    public function buildQueryArgsForPostFields($args, $whereClauses)
    {
        $this->__queryable_postColumns__ = array_filter($whereClauses, function ($item) {
            return strpos($item['field'], '.') === false;
        });

        foreach ($this->__queryable_postColumns__ as $postColumn) {
            if ($postColumn['field'] == 'post_status') {
                if ($postColumn['operator'] == 'NOT IN') {
                    $postStatuses = array_map(function ($status) {
                        return $status['key'];
                    }, ninjaTablesGetPostStatuses());
                    $postColumn['value'] = array_diff(
                        $postStatuses, $postColumn['value']
                    );
                }
                $postColumn['value'] = array_diff($postColumn['value'], ['any']);
                $args['post_status'] = $postColumn['value'];
            } else if ($postColumn['field'] == 'post_author') {
                $operator = $postColumn['operator'];
                if ($operator == 'IN') {
                    $operator = 'author__in';
                } else if ($operator == 'NOT IN') {
                    $operator = 'author__not_in';
                }
                $args[$operator] = $postColumn['value'];
            } else if ($postColumn['field'] == 'ID') {
                add_filter('posts_where', [$this, 'WPNinjaTablesPostWhereIDFilter']);
            } else if ($postColumn['field'] == 'post_date') {
                add_filter('posts_where', [$this, 'WPNinjaTablesPostWherePostDateFilter']);
            } else if ($postColumn['field'] == 'post_modified') {
                add_filter('posts_where', [$this, 'WPNinjaTablesPostWherePostModifiedFilter']);
            } else if ($postColumn['field'] == 'comment_count') {
                add_filter('posts_where', [$this, 'WPNinjaTablesPostWhereCommentCountFilter']);
            }
        }

        return $args;
    }

    public function WPNinjaTablesPostWhereIDFilter($where)
    {
        global $wpdb;

        remove_filter(current_filter(), [$this, __FUNCTION__]);

        foreach ($this->__queryable_postColumns__ as $column) {
            if ($column['field'] == 'ID') {
                $where .= " AND {$wpdb->posts}.ID {$column['operator']} {$column['value']}";
            }
        }

        return $where;
    }

    public function WPNinjaTablesPostWherePostDateFilter($where)
    {
        global $wpdb;

        remove_filter(current_filter(), [$this, __FUNCTION__]);

        foreach ($this->__queryable_postColumns__ as $column) {
            if ($column['field'] == 'post_date') {
                $where .= " AND {$wpdb->posts}.post_date {$column['operator']} '{$column['value']}'";
            }
        }

        return $where;
    }

    public function WPNinjaTablesPostWherePostModifiedFilter($where)
    {
        global $wpdb;

        remove_filter(current_filter(), [$this, __FUNCTION__]);

        foreach ($this->__queryable_postColumns__ as $column) {
            if ($column['field'] == 'post_modified') {
                $where .= " AND {$wpdb->posts}.post_modified {$column['operator']} '{$column['value']}'";
            }
        }

        return $where;
    }

    public function WPNinjaTablesPostWhereCommentCountFilter($where)
    {
        global $wpdb;

        remove_filter(current_filter(), [$this, __FUNCTION__]);

        foreach ($this->__queryable_postColumns__ as $column) {
            if ($column['field'] == 'comment_count') {
                $where .= " AND {$wpdb->posts}.comment_count {$column['operator']} {$column['value']}";
            }
        }

        return $where;
    }

    public function buildQueryArgsForTaxonomies($args, $whereClauses)
    {
        $taxonomies = array_filter($whereClauses, function ($item) {
            return strpos($item['field'], '.') !== false;
        });

        global $ninja_table_current_rendering_table;
        $manualTaxonomies = false;
        if ($ninja_table_current_rendering_table && $ninja_table_current_rendering_table['shortCodeData']['post_tax']) {
            $postTaxes = $ninja_table_current_rendering_table['shortCodeData']['post_tax'];
            $postTaxes = explode('|', $postTaxes);
            foreach ($postTaxes as $postTax) {
                $postTax = explode('=', $postTax);
                if (count($postTax) == 2) {
                    $manualTaxonomies[$postTax[0]] = explode(',', $postTax[1]);
                }
            }
        }

        if (!$taxonomies && !$manualTaxonomies) {
            return $args;
        }

        $taxQueryItems = [];
        foreach ($taxonomies as $taxQuery) {
            $taxonomy = substr(
                $taxQuery['field'],
                strpos($taxQuery['field'], '.') + 1
            );

            if($manualTaxonomies && $manualTaxonomies[$taxonomy]) {
                continue;
            }

            $taxQueryItems[] = array(
                'field'    => 'slug',
                'taxonomy' => $taxonomy,
                'terms'    => $taxQuery['value'],
                'operator' => $taxQuery['operator']
            );
        }

        if($manualTaxonomies) {
            $manualTaxField = 'slug';
            if(
                isset($ninja_table_current_rendering_table['shortCodeData']['post_tax_field']) &&
                $ninja_table_current_rendering_table['shortCodeData']['post_tax_field']
            ) {
                $manualTaxField = $ninja_table_current_rendering_table['shortCodeData']['post_tax_field'];
            }

            foreach ($manualTaxonomies as $manualTax => $taxValue) {
                $taxQueryItems[] = array(
                    'field' => $manualTaxField,
                    'taxonomy' => $manualTax,
                    'terms' => $taxValue,
                    'operator' => 'IN'
                );
            }
        }

        $taxQueryItems['relation'] = 'AND';
        $args['tax_query'] = $taxQueryItems;

        return $args;
    }

    public function getType($column)
    {
        $numericColumnsMap = array(
            'ID',
            'comment_count',
            'menu_order',
            'post_parent'
        );
        $dateColumnsMap = array(
            'post_date',
            'post_date_gmt',
            'post_modified',
            'post_modified_gmt'
        );

        if (in_array($column, $numericColumnsMap)) {
            return 'number';
        }

        if (in_array($column, $dateColumnsMap)) {
            return 'date';
        }

        return 'text';
    }

    public function getHumanName($column)
    {
        $trans = array(
            'post_author'    => __('Author', 'ninja-tables-pro'),
            'post_date'      => __('Create Date', 'ninja-tables-pro'),
            'post_content'   => __('Content', 'ninja-tables-pro'),
            'post_title'     => __('Title', 'ninja-tables-pro'),
            'post_excerpt'   => __('Excerpt', 'ninja-tables-pro'),
            'post_status'    => __('Status', 'ninja-tables-pro'),
            'comment_status' => __('Comment Status', 'ninja-tables-pro'),
            'post_type'      => __('Post Type', 'ninja-tables-pro'),
            'comment_count'  => __('Total Comments', 'ninja-tables-pro')
        );

        if (isset($trans[$column])) {
            return $trans[$column];
        } else if (($pos = strpos($column, '.')) !== false) {
            return ucfirst(substr($column, $pos + 1));
        }
        return $column;
    }

    public function getSourceType($column)
    {
        if (strpos($column, '.')) {
            return 'tax_data';
        }
        return 'post_data';
    }

    private function getPostData($post, $column)
    {
        $original_name = $column['original_name'];
        $value = '';
        if (property_exists($post, $original_name)) {
            $value = $post->{$original_name};
        }
        if (!$value) {
            return '';
        }

        if (
            ArrayHelper::get($column, 'column_settings.data_type') == 'date' &&
            $format = ArrayHelper::get($column, 'column_settings.dateFormat')
        ) {
            $value = date($this->convertMomentFormatToPhp($format), strtotime($value));
        }

        // Check if linkable
        if ($column['permalinked'] == 'yes') {
            $atts = '';
            if ($column['permalink_target'] == '_blank') {
                $atts = 'target="_blank"';
            }
            return '<a ' . $atts . ' title="' . $post->post_title . '" class="ninja_table_permalink" href="' . get_the_permalink($post) . '">' . $value . '</a>';
        }
        return $value;
    }

    private function getCustomData($post, $column)
    {
        $type = $column['wp_post_custom_data_type'];
        $value = $column['wp_post_custom_data_value'];
        if (!$value) {
            return '';
        }
        if ($type == 'acf_field') {
            if ($this->acf_installed || function_exists('the_field')) {
                $this->acf_installed = true;
                ob_start();
                $this->getAcfValue($value, $post->ID);
                return ob_get_clean();
            }
        } else if ($type == 'post_meta') {
            return get_post_meta($post->ID, $value, true);
        } else if ($type == 'shortcode') {
            // check for data types
            $codes = $this->getShortCodes($value, $post);
            if ($codes) {
                $value = str_replace(array_keys($codes), array_values($codes), $value);
                return do_shortcode($value);
            } else {
                return do_shortcode($value);
            }
        } else if ($type == 'featured_image') {
            $value = $this->getFeaturedImage($post, $column);
        } else if ($type == 'product_price') {

        }
        return $value;
    }

    private function getAcfValue($selector, $posId)
    {
        $value = get_field($selector, $posId);

        if (is_array($value)) {
            $formatAcfArray = apply_filters('ninja_table_format_acf_array_enabled', true);

            if ($formatAcfArray) {
                if (is_array($value[0])) {
                    $value = @implode('; ', array_map(function ($entry) {
                        return @implode(', ', $entry);
                    }, $value));
                } else {
                    $value = @implode(', ', $value);
                }
            } else {
                $value = apply_filters('ninja_table_format_acf_value', $value);
            }
        }

        echo $value;
    }

    private function getShortCodes($string, $post)
    {
        $matches = array();
        $regex = "/\{([^\}]*)\}/";
        preg_match_all($regex, $string, $matches);
        if (count($matches) != 2) {
            return false;
        }
        $formats = array();

        $acceptedPrefixes = array(
            'acf',
            'post',
            'post_mata'
        );

        foreach ($matches[1] as $match) {
            $group = substr($match, 0, strpos($match, '.'));
            $fieldName = str_replace($group . '.', '', $match);
            $parseValue = '';
            if ($group && $fieldName) {
                if ($group == 'post') {
                    if (property_exists($post, $fieldName)) {
                        $parseValue = $post->{$fieldName};
                    } else if ($fieldName == 'permalink') {
                        $parseValue = get_the_permalink($post);
                    } else if ($fieldName == 'featured_image_url') {
                        $parseValue = get_the_post_thumbnail_url($post);
                    }
                } else if ($group == 'postmeta') {
                    $parseValue = get_post_meta($post->ID, $fieldName, true);
                } else if ($group == 'acf' && function_exists('get_field')) {
                    $parseValue = get_field($fieldName, $post->ID);
                }
            }
            $formats['{' . $match . '}'] = $parseValue;
        }
        return $formats;
    }

    private function getTaxData($post, $column)
    {
        if (($pos = strpos($column['original_name'], '.')) !== false) {
            $atts = '';
            if ($column['permalinked'] == 'yes') {
                if ($column['filter_permalinked'] == 'yes') {
                    $atts = ' data-target_column=' . $column['key'] . ' class="ninja_table_permalink ninja_table_do_column_filter" ';
                } else if ($column['permalink_target'] == '_blank') {
                    $atts = ' class="ninja_table_tax_permalink" target="_blank" ';
                } else {
                    $atts = ' class="ninja_table_tax_permalink" ';
                }
            }

            $terms = array_map(function ($term) use ($atts) {
                if ($atts) {
                    $link = get_term_link($term);
                    return "<a " . $atts . " href='{$link}'>{$term->name}</a>";
                }
                return $term->name;
            }, wp_get_post_terms($post->ID, substr($column['original_name'], $pos + 1)));
            return implode($column['taxonomy_separator'], $terms);
        }
    }

    private function getAuthorData($post, $column)
    {

        $atts = '';
        if ($column['permalinked'] == 'yes') {
            if ($column['filter_permalinked'] == 'yes') {
                $atts = ' class="ninja_table_author_permalink ninja_table_do_column_filter" ';
            } else if ($column['permalink_target'] == '_blank') {
                $atts .= ' class="ninja_table_author_permalink" target="_blank" ';
            } else {
                $atts .= ' class="ninja_table_author_permalink" ';
            }
        }

        $authorName = get_the_author_meta('display_name', $post->post_author);

        if ($atts && $authorName) {
            $authlink = get_author_posts_url($post->post_author);
            return '<a data-target_column=' . $column['key'] . ' href="' . $authlink . '" ' . $atts . '>' . $authorName . '</a>';
        }
        return $authorName;
    }

    private function getFeaturedImage($post, $column)
    {
        $featuredImageUrl = get_the_post_thumbnail_url($post, $this->get($column, 'wp_post_custom_data_value', 'thumbnail'));
        if (!$featuredImageUrl) {
            return '';
        }
        $postTitle = $post->post_title;
        $value = '<img alt="' . $postTitle . '" src="' . $featuredImageUrl . '" />';
        // Check if linkable
        if ($column['permalinked'] == 'yes') {
            $atts = '';
            if ($column['permalink_target'] == '_blank') {
                $atts = 'target="_blank"';
            }
            return '<a ' . $atts . ' title="' . $postTitle . '" class="ninja_table_permalink" href="' . get_the_permalink($post) . '">' . $value . '</a>';
        }
        return $value;
    }

    protected function get($array, $key, $default = false)
    {
        if (isset($array[$key])) {
            return $array[$key];
        }
        return $default;
    }

    protected function getQueryExtra($tableId)
    {
        $queryExtra = get_post_meta($tableId, '_ninja_wp_posts_query_extra', true);
        if (!$queryExtra || $queryExtra == 'false') {
            $queryExtra = array(
                'query_limit'     => 3000,
                'order_by_column' => 'ID',
                'order_by'        => 'DESC'
            );
        }
        return apply_filters('ninja_table_wp_posts_query_extra', $queryExtra, $tableId);
    }

    protected function convertMomentFormatToPhp($format)
    {
        $replacements = [
            'DD'   => 'd',
            'ddd'  => 'D',
            'D'    => 'j',
            'dddd' => 'l',
            'E'    => 'N',
            'o'    => 'S',
            'e'    => 'w',
            'DDD'  => 'z',
            'W'    => 'W',
            'MMMM' => 'F',
            'MM'   => 'm',
            'MMM'  => 'M',
            'M'    => 'n',
            'YYYY' => 'Y',
            'YY'   => 'y',
            'a'    => 'a',
            'A'    => 'A',
            'h'    => 'g',
            'H'    => 'G',
            'hh'   => 'h',
            'HH'   => 'H',
            'mm'   => 'i',
            'ss'   => 's',
            'SSS'  => 'u',
            'zz'   => 'e',
            'X'    => 'U',
        ];

        $phpFormat = strtr($format, $replacements);

        return $phpFormat;
    }

    public function getWooProductAtrributes()
    {
        $attributes = array(
            'product_price'        => 'Product Price',
            'product_stock_status' => 'Product Stock Status',
            'product_quantity'     => 'Product Quantity input field',
            'buy_now_button'       => 'Buy Now Button'
        );

        return [
            "key"         => 'product_data',
            'source_type' => 'product_data',
            "label"       => 'Product Data',
            "instruction" => 'Show Product Data Attributes',
            "value_type"  => 'options',
            "placeholder" => 'Select Data Attribute',
            "options"     => $attributes
        ];

        return $attributes;
    }

    public function getPostDynamicColumnAtrributes()
    {
        $fields = [];
        if (function_exists('get_field')) {
            $fields[] = array(
                "key"             => 'acf_field',
                "label"           => 'Advanced Custom Fields (ACF)',
                "instruction"     => 'You can populate any ACF fields. Please provide the selector name of the ACF field then your table column values will be populated',
                "learn_more_url"  => 'https://wpmanageninja.com/docs/ninja-tables/wp-posts-table/acf-field/',
                "learn_more_text" => 'Learn more about ACF Field integration',
                "value_type"      => 'text',
                "placeholder"     => 'Type ACF field selector'
            );
        }

        $fields[] = array(
            "key"             => 'post_meta',
            'source_type'     => 'custom',
            "label"           => 'Post Meta',
            "placeholder"     => 'Type Post Meta key',
            "instruction"     => 'You can populate any Post Meta. Please provide the name of the meta key then your table column values will be populated for corresponding row',
            "learn_more_url"  => 'https://wpmanageninja.com/docs/ninja-tables/wp-posts-table/custom-column-on-wp-posts-table/',
            "learn_more_text" => 'Learn more about Post Meta integration',
            "value_type"      => 'text'
        );
        $fields[] = array(
            "key"             => 'shortcode',
            'source_type'     => 'custom',
            "label"           => 'Shortcode / Computed Value or HTML',
            "placeholder"     => 'Provide any valid HTML / Computed fields, Please check instruction / documentation for advance usage',
            "instruction"     => 'You can add any type of HTML or customized dynamic field / shortcode as the column value. You add dynamic post/post meta/acf field like as below: <ul><li>For Post Field: {post.ID} / {post.post_title} / {post.permalink}</li><li>For Post Meta: {postmeta.POSTMETA_KEY_NAME}</li><li>For ACF Field: {acf.acf_field_name}</li><li>For Dynamic Shortcode: [yourshortcode YourParam="{post.ID}"]</li></ul>',
            "learn_more_url"  => 'https://wpmanageninja.com/docs/ninja-tables/wp-posts-table/shortcode-computed-value-or-html-in-wp-posts-table/',
            "learn_more_text" => 'Please read the documentation for more details and advanced usage',
            "value_type"      => 'textarea'
        );

        $imageSizes = get_intermediate_image_sizes();
        $formattedImageSizes = [];
        foreach ($imageSizes as $imageSize) {
            $formattedImageSizes[$imageSize] = $imageSize;
        }

        $fields[] = array(
            "key"         => 'featured_image',
            'source_type' => 'custom',
            "label"       => 'Featured Image',
            "instruction" => 'Show Featured image with post link / without link',
            "value_type"  => 'options',
            "placeholder" => 'Select Image Size',
            "options"     => $formattedImageSizes
        );

        return $fields;
    }

    public function getPostColumnAttributes()
    {
        $attributes = array(
            'post_author'   => 'Post Author',
            'post_date'     => 'Post Date',
            'post_title'    => 'Product Title',
            'post_excerpt'  => 'Product Short Description',
            'post_content'  => 'Product Long Description',
            'post_status'   => 'Post Status',
            'comment_count' => 'Post Comment Count'
        );

        return [
            "key"         => 'post_data',
            'source_type' => 'author_data',
            "label"       => 'Post Data',
            "instruction" => 'Show Post Data Attributes',
            "value_type"  => 'options',
            "placeholder" => 'Select Data Attribute',
            "options"     => $attributes
        ];
    }

    public function getPostTaxAttributes($postType)
    {
        $taxonomies = get_object_taxonomies($postType);

        $attributes = [];
        foreach ($taxonomies as $taxonomy) {
            $attributes[$taxonomy] = ucfirst(str_replace('_', ' ', $taxonomy));
        }

        return [
            "key"         => 'tax_data',
            'source_type' => 'tax_data',
            "label"       => 'Product Taxonomy',
            "instruction" => 'Show Product Categories/Tags/Type/Other Terms',
            "value_type"  => 'options',
            "placeholder" => 'Select Data Attribute',
            "options"     => $attributes
        ];

    }

    /**
     * Build meta query to filter out post table data.
     *
     * @param  array $args
     * @param  array $metas
     * @return array
     */
    public function buildMetaQuery($args, $metas)
    {
        $metaQuery = array();

        if (!$metas || !is_array($metas)) {
            return $args;
        }

        foreach ($metas as $meta) {
            $value = $meta['value'];

            $type = null;
            if (in_array($meta['operator'], ['IN', 'NOT IN'])) {
                $value = array_map('trim', explode(',', $value));
            } elseif (in_array($meta['operator'], ['>', '>=', '<', '<='])) {
                $type = 'NUMERIC';
            }

            $query = array(
                'key'     => $meta['field'],
                'value'   => $value,
                'compare' => $meta['operator']
            );

            if ($type) {
                $query['type'] = $type;
            }

            $metaQuery[] = $query;
        }

        $args['meta_query'] = $metaQuery;

        return $args;
    }
}
