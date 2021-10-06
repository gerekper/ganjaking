<?php

/**
 * Description of A2W_SearchPage
 *
 * @author andrey
 * 
 * @autoload: a2w_admin_init
 */
if (!class_exists('A2W_SearchPageController')) {

    class A2W_SearchPageController extends A2W_AbstractAdminPage {

        public function __construct() {
            parent::__construct(__('Search Products', 'ali2woo'), __('Search Products', 'ali2woo'), 'import', 'a2w_dashboard', 10);
        }

        public function render($params = array()) {
            $filter = array();
            if (is_array($_GET) && $_GET) {
                $filter = array_merge($filter, $_GET);
                if (isset($filter['cur_page'])) {
                    unset($filter['cur_page']);
                }
                if (isset($filter['page'])) {
                    unset($filter['page']);
                }
            }

            $adv_search_field = array('min_price', 'max_price', 'min_feedback', 'max_feedback', 'volume_from', 'volume_to');
            $adv_search = false;
            foreach ($filter as $key => $val) {
                $new_key = preg_replace('/a2w_/', '', $key, 1);
                unset($filter[$key]);
                $filter[$new_key] = wp_unslash($val);
                if (in_array($new_key, $adv_search_field)) {
                    $adv_search = true;
                }
            }

            if (!isset($filter['sort'])) {
                $filter['sort'] = "volumeDown";
            }

            $page = isset($_GET['cur_page']) && intval($_GET['cur_page']) ? intval($_GET['cur_page']) : 1;
            $per_page = 20;

            if (!empty($_REQUEST['a2w_search'])) {
                $loader = new A2W_Aliexpress();
                $load_products_result = $loader->load_products($filter, $page, $per_page);
            } else {
                $load_products_result = A2W_ResultBuilder::buildError(__('Please enter some search keywords or select item from category list!', 'ali2woo'));
            }

            if ($load_products_result['state'] == 'error' || $load_products_result['state'] == 'warn') {
                add_settings_error('a2w_products_list', esc_attr('settings_updated'), $load_products_result['message'], 'error');
            }

            if ($load_products_result['state'] != 'error') {
                $pages_list = array();
                $links = 4;
                $last = ceil($load_products_result['total'] / $per_page);
                $load_products_result['total_pages'] = $last;
                $start = ( ( $load_products_result['page'] - $links ) > 0 ) ? $load_products_result['page'] - $links : 1;
                $end = ( ( $load_products_result['page'] + $links ) < $last ) ? $load_products_result['page'] + $links : $last;
                if ($start > 1) {
                    $pages_list[] = 1;
                    $pages_list[] = '';
                }
                for ($i = $start; $i <= $end; $i++) {
                    $pages_list[] = $i;
                }
                if ($end < $last) {
                    $pages_list[] = '';
                    $pages_list[] = $last;
                }
                $load_products_result['pages_list'] = $pages_list;

                a2w_set_transient('a2w_search_result', $load_products_result['products']);
            }

            $countryModel = new A2W_Country();
            $localizator = A2W_AliexpressLocalizator::getInstance();

            $this->model_put('filter', $filter);
            $this->model_put('adv_search', $adv_search);
            $this->model_put('categories', $this->get_categories());
            $this->model_put('countries', $countryModel->get_countries());
            $this->model_put('locale', $localizator->getLangCode());
            $this->model_put('currency', $localizator->currency);
            $this->model_put('chrome_ext_import', a2w_check_defined('A2W_CHROME_EXT_IMPORT'));
            
            $this->model_put('load_products_result', $load_products_result);

            $search_version = 'v2';
            $this->include_view('search_'.$search_version.'.php');
        }

        protected function get_categories() {
            if (file_exists(A2W()->plugin_path() . '/assets/data/user_aliexpress_categories.json')) {
                $result = json_decode(file_get_contents(A2W()->plugin_path() . '/assets/data/user_aliexpress_categories.json'), true);
            } else {
                $result = array('categories'=>get_option('a2w_all_categories', array()));
            }
            $result = isset($result["categories"]) && is_array($result["categories"])?$result["categories"]:array();
            array_unshift($result, array("id" => "0", "name" => "All categories", "level" => 1));
            return $result;
        }

    }

}

