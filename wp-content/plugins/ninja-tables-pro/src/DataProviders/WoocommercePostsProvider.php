<?php

namespace NinjaTablesPro\DataProviders;


class WoocommercePostsProvider
{
    use WooDataSourceTrait;

    public function boot()
    {
        if (!defined('WC_PLUGIN_FILE')) {
            return;
        }

        add_filter('ninja_table_activated_features', function ($features) {
            $features['woocommerce_table'] = true;
            return $features;
        });

        add_filter('ninja_tables_get_table_wp_woo', array($this, 'getTableSettings'));
        add_filter('ninja_tables_get_table_data_wp_woo', array($this, 'getTableData'), 10, 4);
        add_filter('ninja_tables_fetching_table_rows_wp_woo', array($this, 'data'), 10, 5);

        add_action('wp_ajax_ninja_table_woocommerece_create_table', array($this, 'createTable'));
        add_action('wp_ajax_ninja_table_woocommerece_get_custom_field_options', array($this, 'getTableDataOptions'));
        add_action('wp_ajax_ninja_table_woocommerece_get_options', array($this, 'getWooSettings'));
        add_action('wp_ajax_ninja_table_save_query_settings_woo_table', array($this, 'saveQuerySettings'));
        add_action('wp_ajax_ninja_table_wp_woo_get_custom_field_options', array($this, 'getCustomFieldOptions'));


        add_action('ninja_rendering_table_wp_woo', array($this, 'addFrontendAsset'), 10, 1);

        add_filter('woocommerce_add_to_cart_fragments', array($this, 'pushCartFragment'), 10, 1);
    }

    public function getWooSettings()
    {
        if (!current_user_can(ninja_table_admin_role())) {
            return;
        }

	    ninjaTablesValidateNonce();

        $quertTerms = $this->getWooQueryTerms();
        wp_send_json_success([
            'query_terms' => $quertTerms
        ], 200);
    }

    public function saveQuerySettings()
    {
        if (!current_user_can(ninja_table_admin_role())) {
            return;
        }
	    ninjaTablesValidateNonce();

        $tableId = intval($_REQUEST['table_id']);
        $query_selections = $_REQUEST['query_selections'];


        //$query_selections = wp_unslash($query_selections);
        update_post_meta($tableId, '_ninja_table_woo_query_selections', $query_selections);

        $query_conditions = $_REQUEST['query_conditions'];
        //$query_conditions = wp_unslash($query_conditions);
        update_post_meta($tableId, '_ninja_table_woo_query_conditions', $query_conditions);


        if (isset($_REQUEST['appearance_settings'])) {
            $appearance_settings = $_REQUEST['appearance_settings'];
            update_post_meta($tableId, '_ninja_table_woo_appearance_settings', $appearance_settings);
        }

        wp_send_json_success([
            'message' => 'Settings successfully updated'
        ], 200);
    }

    public function getWooQueryTerms()
    {
        $settings = [
            'product_cat'  => [
                'title'       => 'Select Products By Category',
                'description' => 'Select the categories from where you want to show the products. Leave empty if you want to show from all categories',
                'terms'       => get_terms([
                    'taxonomy'   => 'product_cat',
                    'hide_empty' => false,
                ])
            ],
            'product_tag'  => [
                'title'       => 'Select Products By Product Tags',
                'description' => 'Select the product tags from where you want to show the products. Leave empty if you want to show from all tags',
                'terms'       => get_terms([
                    'taxonomy'   => 'product_tag',
                    'hide_empty' => false,
                ])
            ],
            'product_type' => [
                'title'       => 'Select Products By Product Type',
                'description' => 'Select the product types from where you want to show the products. Leave empty if you want to show from all types',
                'terms'       => get_terms([
                    'taxonomy'   => 'product_type',
                    'hide_empty' => false,
                ])
            ]
        ];

        return apply_filters('ninja_table_woo_table_query_terms', $settings);
    }

    public function createTable()
    {
        if (!current_user_can(ninja_table_admin_role())) {
            return;
        }
	    ninjaTablesValidateNonce();
        $messages = array();

        if (empty($_REQUEST['post_title'])) {
            $messages['title'] = __('The title field is required.', 'ninja-tables-pro');
        }

        // If Validation failed
        if (array_filter($messages)) {
            wp_send_json_error(array('message' => $messages), 422);
            wp_die();
        }

        $initalHeaders = [
            [
                'name'                            => 'Image',
                'key'                             => 'woo_product_image',
                'breakpoints'                     => '',
                'data_type'                       => 'html',
                'width'                           => '100',
                'header_html_content'             => null,
                'enable_html_content'             => false,
                'contentAlign'                    => null,
                'textAlign'                       => null,
                'permalinked'                     => "yes",
                'source_type'                     => "custom",
                'wp_post_custom_data_source_type' => 'featured_image',
                'wp_post_custom_data_key'         => 'shop_thumbnail'
            ],
            [
                'name'                            => 'Name',
                'key'                             => 'woo_posttitle',
                'breakpoints'                     => '',
                'data_type'                       => 'html',
                'header_html_content'             => null,
                'enable_html_content'             => false,
                'contentAlign'                    => null,
                'textAlign'                       => null,
                'permalinked'                     => "yes",
                'source_type'                     => "post_data",
                'wp_post_custom_data_source_type' => 'post_data',
                'wp_post_custom_data_key'         => 'post_title'
            ],
            [
                'name'                            => 'Category',
                'key'                             => 'woo_productproductcat',
                'breakpoints'                     => '',
                'data_type'                       => 'html',
                'header_html_content'             => null,
                'enable_html_content'             => false,
                'contentAlign'                    => null,
                'textAlign'                       => null,
                'permalinked'                     => "yes",
                'filter_permalinked'              => "yes",
                'source_type'                     => "tax_data",
                'wp_post_custom_data_source_type' => "tax_data",
                'taxonomy_separator'              => ", ",
                'wp_post_custom_data_key'         => 'product_cat'
            ],
            [
                'name'                            => 'Price',
                'key'                             => 'woo_product_price',
                'breakpoints'                     => '',
                'data_type'                       => 'html',
                'permalinked'                     => "no",
                'header_html_content'             => null,
                'enable_html_content'             => false,
                'contentAlign'                    => null,
                'textAlign'                       => null,
                'source_type'                     => "product_data",
                'wp_post_custom_data_source_type' => "product_data",
                'wp_post_custom_data_key'         => 'product_price'
            ],
            [
                'name'                            => 'Quantity',
                'key'                             => 'woo_product_quantity',
                'breakpoints'                     => '',
                'data_type'                       => 'html',
                'permalinked'                     => "no",
                'header_html_content'             => null,
                'enable_html_content'             => false,
                'contentAlign'                    => null,
                'textAlign'                       => null,
                'source_type'                     => "product_data",
                'wp_post_custom_data_source_type' => "product_data",
                'wp_post_custom_data_key'         => 'product_quantity'
            ],
            [
                'name'                            => 'Buy',
                'key'                             => 'woo_product_buy',
                'breakpoints'                     => '',
                'data_type'                       => 'html',
                'permalinked'                     => "no",
                'header_html_content'             => null,
                'enable_html_content'             => false,
                'contentAlign'                    => null,
                'textAlign'                       => null,
                'source_type'                     => "product_data",
                'wp_post_custom_data_source_type' => "product_data",
                'wp_post_custom_data_key'         => 'buy_now_button',
                'wp_post_custom_data_value'       => 'Add To Cart',
                'show_quantity'                   => 'yes',
                'quantity_type'                   => 'normal',
                'quantity_max_value'              => ''
            ]
        ];

        $tableId = $this->saveTable();

        update_post_meta($tableId, '_ninja_wp_posts_query_extra', $this->getQueryExtra($tableId));

        $message = 'Table created successfully.';

        update_post_meta($tableId, '_ninja_table_woo_query_selections', $_REQUEST['query_selections']);
        update_post_meta($tableId, '_ninja_table_woo_query_conditions', $_REQUEST['query_conditions']);

        $appearanceSettings = [
            'show_cart_before_table' => 'yes',
            'show_cart_after_table' => 'yes',
            'show_cart_button' => 'yes',
            'show_checkout_button' => 'yes'
        ];

        update_post_meta($tableId, '_ninja_table_woo_appearance_settings', $appearanceSettings);

        update_post_meta($tableId, '_ninja_table_columns', $initalHeaders);
        update_post_meta($tableId, '_ninja_tables_data_provider', 'wp_woo');

        wp_send_json_success(array('table_id' => $tableId, 'message' => $message), 200);
    }

    public function getCustomFieldOptions()
    {
        $tableId = intval($_REQUEST['table_id']);
        if (!current_user_can(ninja_table_admin_role())) {
            return;
        }
	    ninjaTablesValidateNonce();

        $postCustomAttributes = $this->getPostDynamicColumnAtrributes();
        $postCustomAttributes[] = $this->getWooProductAtrributes();
        $postCustomAttributes[] = $this->getPostColumnAttributes();
        $postCustomAttributes[] = $this->getPostTaxAttributes('product');


        wp_send_json_success([
            'custom_fields' => $postCustomAttributes
        ]);
    }

    public function getTableSettings($table)
    {
        $table->isEditable = false;
        $table->dataSourceType = 'wp_woo';

        $table->isEditableMessage = 'You may edit your table settings here.';

        $table->isExportable = true;
        $table->isImportable = false;
        $table->isSortable = false;
        $table->isCreatedSortable = false;
        $table->hasCacheFeature = false;

        $querySelections = get_post_meta($table->ID, '_ninja_table_woo_query_selections', true);

        if (!$querySelections) {
            $querySelections = (object)[];
        }

        $queryConditions = get_post_meta($table->ID, '_ninja_table_woo_query_conditions', true);
        if (!$queryConditions) {
            $queryConditions = (object)[];
        }

        $appearanceSettings = get_post_meta($table->ID, '_ninja_table_woo_appearance_settings', true);
        if (!$appearanceSettings) {
            $appearanceSettings = (object)[];
        }


        $table->query_selections = $querySelections;
        $table->query_conditions = $queryConditions;
        $table->appearance_settings = $appearanceSettings;
        return $table;
    }

    public function getTableData($data, $tableId, $perPage = -1, $offset = 0)
    {
        if ($perPage == -1) {
            $queryExtra = $this->getQueryExtra($tableId);
            if (isset($queryExtra['query_limit']) && $queryExtra['query_limit']) {
                $perPage = intval($queryExtra['query_limit']);
            }
        }

        $newData = array();
        $posts = $this->getPosts($tableId);
        $total = count($posts);
        $responsePosts = array_slice($posts, $offset, $perPage);
        foreach ($responsePosts as $key => $post) {
            $newData[] = array(
                'id'       => $key + 1,
                'values'   => $post,
                'position' => $key + 1,
            );
        }
        return array(
            $newData,
            $total
        );
    }

    public function data($data, $tableId, $defaultSorting, $limitEntries = false, $skip = false)
    {
        $perPage = -1;
        $queryExtra = $this->getQueryExtra($tableId);
        if ($limitEntries) {
            $perPage = $limitEntries;
        }


        return $this->getPosts($tableId, $perPage, $skip);
    }

    public function getPosts($tableId, $per_page = -1, $offset = 0)
    {
        $columns = get_post_meta($tableId, '_ninja_table_columns', true);

        $formatted_columns = array();
        foreach ($columns as $column) {
            $type = $this->get($column, 'source_type');
            $columnKey = $this->get($column, 'key');
            $dataType = $this->get($column, 'wp_post_custom_data_source_type');
            $dataValue = $this->get($column, 'wp_post_custom_data_key');

            $formatted_columns[$columnKey] = array(
                'type'                            => $type,
                'key'                             => $columnKey,
                'permalinked'                     => $this->get($column, 'permalinked'),
                'permalink_target'                => $this->get($column, 'permalink_target'),
                'filter_permalinked'              => $this->get($column, 'filter_permalinked'),
                'taxonomy_separator'              => $this->get($column, 'taxonomy_separator'),
                'wp_post_custom_data_source_type' => $dataType,
                'wp_post_custom_data_key'         => $dataValue,
                'column_settings'                 => $column
            );
        }

        $terms = get_post_meta($tableId, '_ninja_table_woo_query_selections', true);

        $where = [];

        if($terms) {
            foreach ($terms as $termKey => $term) {
                if ($term) {
                    $where[] = array(
                        'field'    => 'product.' . $termKey,
                        'operator' => 'IN',
                        'value'    => $term
                    );
                }
            }
        }


        $productContions = get_post_meta($tableId, '_ninja_table_woo_query_conditions', true);
        if ($this->get($productContions, 'hide_out_of_stock') == 'yes') {
            $where[] = array(
                'field'    => 'product.product_visibility',
                'value'    => array('outofstock'),
                'operator' => 'NOT IN'
            );
        }

        $order_query = $this->getOrderBy($productContions, $tableId);

        $post_types = ['product'];
        return $this->buildWPQuery(
            compact('tableId', 'formatted_columns', 'order_query', 'where', 'post_types', 'offset', 'per_page')
        );
    }

    protected function saveTable($postId = null)
    {
        $attributes = array(
            'post_title'  => sanitize_text_field($this->get($_REQUEST, 'post_title')),
            'post_type'   => 'ninja-table',
            'post_status' => 'publish'
        );

        if (!$postId) {
            $postId = wp_insert_post($attributes);
        } else {
            $attributes['ID'] = $postId;
            wp_update_post($attributes);
        }
        return $postId;
    }

    public function getTableDataOptions()
    {
	    ninjaTablesValidateNonce();
        $data = array(
            'custom_fields' => array(
                array(
                    "key"             => 'acf_field',
                    "label"           => 'Advanced Custom Fields (ACF)',
                    "instruction"     => 'You can populate any ACF fields. Please provide the selector name of the ACF field then your table column values will be populated',
                    "learn_more_url"  => 'https://wpmanageninja.com/docs/ninja-tables/wp-posts-table/acf-field/',
                    "learn_more_text" => 'Learn more about ACF Field integration',
                    "value_type"      => 'text',
                    "placeholder"     => 'Type ACF field selector',
                    "disabled"        => !function_exists('get_field')
                ),
                array(
                    "key"             => 'post_meta',
                    "label"           => 'Post Meta',
                    "placeholder"     => 'Type Post Meta key',
                    "instruction"     => 'You can populate any Post Meta. Please provide the name of the meta key then your table column values will be populated for corresponding row',
                    "learn_more_url"  => 'https://wpmanageninja.com/docs/ninja-tables/wp-posts-table/custom-column-on-wp-posts-table/',
                    "learn_more_text" => 'Learn more about Post Meta integration',
                    "value_type"      => 'text'
                ),
                array(
                    "key"             => 'shortcode',
                    "label"           => 'Shortcode / Computed Value or HTML',
                    "placeholder"     => 'Provide any valid HTML / Computed fields, Please check instruction / documentation for advance usage',
                    "instruction"     => 'You can add any type of HTML or customized dynamic field / shortcode as the column value. You add dynamic post/post meta/acf field like as below: <ul><li>For Post Field: {post.ID} / {post.post_title} / {post.permalink}</li><li>For Post Meta: {postmeta.POSTMETA_KEY_NAME}</li><li>For ACF Field: {acf.acf_field_name}</li><li>For Dynamic Shortcode: [yourshortcode YourParam="{post.ID}"]</li></ul>',
                    "learn_more_url"  => 'https://wpmanageninja.com/docs/ninja-tables/wp-posts-table/shortcode-computed-value-or-html-in-wp-posts-table/',
                    "learn_more_text" => 'Please read the documentation for more details and advanced usage',
                    "value_type"      => 'textarea'
                ),
                array(
                    "key"         => 'featured_image',
                    "label"       => 'Featured Image',
                    "instruction" => 'Show Featured image with post link / without link',
                    "value_type"  => 'options',
                    "placeholder" => 'Select Image Size',
                    "options"     => get_intermediate_image_sizes()
                ),
            )
        );
        wp_send_json_success($data);
    }

    private function getOrderBy($productContions, $tableId)
    {
        $order_query = [];
        if ($orderBy = $this->get($productContions, 'order_by')) {
            $order = $this->get($productContions, 'order_by_type', 'ASC');
            $metaOrders = [
                'price'          => [
                    'orderby'  => 'meta_value_num',
                    'meta_key' => '_price'
                ],
                'average_rating' => [
                    'orderby'  => 'meta_value_num',
                    'meta_key' => '_wc_average_rating'
                ],
                'popularity'     => [
                    'orderby'  => 'meta_value_num',
                    'meta_key' => 'total_sales'
                ]
            ];
            if (isset($metaOrders[$orderBy])) {
                $order_query = [
                    'orderby'  => $metaOrders[$orderBy]['orderby'],
                    'meta_key' => $metaOrders[$orderBy]['meta_key'],
                    'order'    => $order
                ];
            } else if ($orderBy == 'random') {
                $order_query = [
                    'orderby' => 'rand'
                ];
            } else {
                // It's mea post table query
                $order_query = [
                    'orderby' => $orderBy,
                    'order'   => $order
                ];
            }
        }

        return $order_query;
    }

    public function addFrontendAsset($tableArray)
    {
        wp_enqueue_script('ninjatable_woo_script', NINJAPROPLUGIN_URL . 'assets/woo_table_frontend.js', array('jquery'), NINJAPROPLUGIN_VERSION, true);

        $appreanceSettings = get_post_meta($tableArray['table_id'], '_ninja_table_woo_appearance_settings', true);

        if ($appreanceSettings && $appreanceSettings['show_cart_before_table'] == 'yes') {
            add_action('ninja_tables_before_table_print', array($this, 'maybeAddCartDom'), 10, 2);
        }
        if ($appreanceSettings && $appreanceSettings['show_cart_after_table'] == 'yes') {
            add_action('ninja_tables_after_table_print', array($this, 'maybeAddCartDom'), 10, 2);
        }
    }

    public function maybeAddCartDom($table, $tableArray)
    {
        if ($tableArray['provider'] != 'wp_woo') {
            return '';
        }
        echo $this->getCartFragmentHtml($tableArray['table_id']);
    }

    private function getCartFragmentHtml($tableId)
    {
        if (!defined('WC_PLUGIN_FILE')) {
            return;
        }

        $appreanceSettings = get_post_meta($tableId, '_ninja_table_woo_appearance_settings', true);

        if(!is_array($appreanceSettings)) {
            return;
        }

        $showCheckoutBtn = $this->get($appreanceSettings, 'show_checkout_button') == 'yes';
        $showCartBtn = $this->get($appreanceSettings, 'show_cart_button') == 'yes';


        if(!function_exists('WC') || !WC()->cart || !method_exists(WC()->cart, 'get_cart_contents_count') || !method_exists(WC()->cart, 'get_cart_total') ) {
            return '';
        }

        $itemCount = WC()->cart->get_cart_contents_count();
        $totalAmount = WC()->cart->get_cart_total();

        $style = '';
        if (!$itemCount) {
            $style = 'display: none;';
        }

        if ($itemCount > 1) {
            $itemText = __('Items', 'ninja-tables-pro');
        } else {
            $itemText = __('Item', 'ninja-tables-pro');
        }
        $cartUrl = wc_get_cart_url();
        $chekoutUrl = wc_get_checkout_url();

        $checkoutText = __($this->get($appreanceSettings, 'checkoutBtnText', 'Checkout'), 'ninja-tables-pro');

        $cartUrl = apply_filters('ninja_table_woo_cart_url', $cartUrl);
        $cartText = __($this->get($appreanceSettings, 'cartBtnText', 'View cart'), 'ninja-tables-pro');
        $cartText = apply_filters('ninja_table_woo_cart_text', $cartText);

        ob_start();
        ?>
        <div style="<?php echo $style; ?>" class="ninjatable_cart_wrapper woocommerce widget_shopping_cart">
            <div class="cart_details">
                <div class="nt_woo_items">
                    <span class="nt_woo_item_count"><?php echo $itemCount . ' ' . $itemText; ?> </span> <span
                        class="nt_woo_separator">|</span> <span class="nt_woo_amount"><?php echo $totalAmount; ?></span>
                </div>
                <div class="nt_woo_cart_checkout_bttons">
                    <?php if ($showCartBtn): ?>
                        <a class="button wc-forward" href="<?php echo $cartUrl; ?>">
                            <span class="nt_woo_view_cart"><i class="fooicon fooicon-bag"></i> <?php echo $cartText; ?></span>
                        </a>
                    <?php endif; ?>
                    <?php if ($showCheckoutBtn): ?>
                        <a class="button checkout wc-forward" href="<?php echo $chekoutUrl; ?>">
                            <span class="nt_woo_view_cart"><i
                                    class="fooicon fooicon-basket"></i> <?php echo $checkoutText; ?></span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }

    public function pushCartFragment($fragments)
    {
        if (!isset($_REQUEST['ninja_table']) || apply_filters('nt_woo_always_cart_fragment', false)) {
            return $fragments;
        }
        $content = $this->getCartFragmentHtml($_REQUEST['ninja_table']);
        $fragments['div.ninjatable_cart_wrapper'] = $content;
        return $fragments;
    }
}
