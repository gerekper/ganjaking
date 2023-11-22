<?php

namespace ElementPack\Includes\TemplateLibrary;

if (!defined('ABSPATH')) exit; // Exit if accessed directly
class ElementPack_Template_Library_Base
{

    protected $perPage = 18;
    protected $totalPage = 1;
    protected $demo_total = 0;
    protected $searchVal;
    protected $termSlug;
    protected $demoType;
    protected $sortByTitle;
    protected $sortByDate;

    protected $table_cat;
    protected $table_post;
    protected $table_cat_post;
    protected $charset_collate;
    protected $wpdb;

    // public $packLicenseActivated = true;
    public $packLicenseActivated = false;


    protected $api_url = 'https://www.elementpack.pro/wp-json/template-manager/v1/';

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->charset_collate = $this->wpdb->get_charset_collate();
        $this->table_cat = $this->wpdb->prefix . 'ep_template_library_cat';
        $this->table_post = $this->wpdb->prefix . 'ep_template_library_post';
        $this->table_cat_post = $this->wpdb->prefix . 'ep_template_library_cat_post';
        $this->packLicenseActivated = element_pack_pro_activated();
    }


    public function createTemplateTables()
    {
        $charset_collate = $this->charset_collate;
        $table_cat_name = $this->table_cat;
        $table_post_name = $this->table_post;
        $table_cat_post_name = $this->table_cat_post;
        ;
        if(defined('BDTEP_TPL_DB_VER') && BDTEP_TPL_DB_VER != get_option('BDTEP_TPL_DB_VER',false)){
            $this->wpdb->query( "DROP TABLE IF EXISTS $table_cat_name" );
            $this->wpdb->query( "DROP TABLE IF EXISTS $table_post_name" );
            $this->wpdb->query( "DROP TABLE IF EXISTS $table_cat_post_name" );
            update_option('BDTEP_TPL_DB_VER',BDTEP_TPL_DB_VER);
        }


        $catsql = "CREATE TABLE IF NOT EXISTS $table_cat_name (
            `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `term_id` mediumint(9) NOT NULL,
            `name` varchar(191) default NULL,
            `slug` varchar(191) default NULL,
            `description` text default NULL,
            `total` mediumint(9) default NULL,
            `image_url` varchar(191) default NULL,
            UNIQUE KEY id (id),
            UNIQUE (slug)
        ) $charset_collate;";

        $catPostsql = "CREATE TABLE IF NOT EXISTS $table_cat_post_name (
            `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `term_id` bigint(20) UNSIGNED NOT NULL,
            `demo_id` bigint(20) UNSIGNED NOT NULL,
            UNIQUE KEY id (id)
        ) $charset_collate;";

        $postsql = "CREATE TABLE IF NOT EXISTS $table_post_name (
            `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `demo_id` bigint(20) UNSIGNED NOT NULL,
            `date` date default NULL,
            `title` varchar(191) default NULL,
            `short_desc` text default NULL,
            `is_pro` int(2) default NULL,
            `type` int(2) default NULL,
            `thumbnail` varchar(191) default NULL,
            `demo_url` varchar(191) default NULL,
            `json_url` varchar(191) default NULL,
            UNIQUE KEY id (id),
            UNIQUE (demo_id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($catsql);
        dbDelta($postsql);
        dbDelta($catPostsql);
        $this->setTemplateDataToDB();
    }

    public function setTemplateDataToDB()
    {

        $demoData = $this->remote_get_demo_data();
        if ($demoData) {
            $this->wpdb->query('TRUNCATE ' . $this->table_cat);
            $this->wpdb->query('TRUNCATE ' . $this->table_post);
            $this->wpdb->query('TRUNCATE ' . $this->table_cat_post);

            $prefixCat = "INSERT INTO `" . $this->table_cat . "` (`term_id`, `name`, `slug`, `description`, `total`, `image_url`) VALUES ";
            $CatQueryString = [];

            $prefixPost = "INSERT INTO `" . $this->table_post . "` (`demo_id`, `date`, `title`, `short_desc`, `is_pro`, `type`, `thumbnail`, `demo_url`, `json_url`) VALUES ";
            $PostQueryString = [];

            $prefixPostCat = "INSERT INTO `" . $this->table_cat_post . "` (`term_id`, `demo_id`) VALUES ";
            $PostCatQueryString = [];

            foreach ($demoData as $demo) {
                $Catstring = [$demo['term_id'], $demo['name'], $demo['slug'],$demo['description'],$demo['total'],$demo['image_url']];
                $Catstring = '("' . implode('", "', $Catstring) . '")';
                array_push($CatQueryString, $Catstring);

                if(isset($demo['data']) && is_array($demo['data'])){
                    $postData = $demo['data'];
                    foreach($postData as $post){
                        $Poststring = [$post['demo_id'], $post['date'], $post['title'],$post['short_desc'],$post['is_pro'],$post['type'],$post['thumbnail'],$post['demo_url'],$post['json_url']];
                        $PostQueryString[$post['demo_id']] = '("' . implode('", "', $Poststring) . '")';

                        $PostCatstring = [$demo['term_id'], $post['demo_id']];
                        $PostCatQueryString[] = '(' . implode(',', $PostCatstring) . ')';
                    }
                }
            }

            $wpdbError = false;

            $query = $prefixCat . implode(',', $CatQueryString);
            $this->wpdb->query($query);
            if($this->wpdb->last_error){
                $wpdbError = true;
            }

            $PostQueryString = array_chunk($PostQueryString, 100, true);
            foreach ($PostQueryString as $chunk) {
                $postQuery = $prefixPost . implode(',', $chunk);
                $this->wpdb->query($postQuery);
            }

            if($this->wpdb->last_error){
                $wpdbError = true;
            }

            $PostCatQueryString = array_chunk($PostCatQueryString, 100, true);
            foreach ($PostCatQueryString as $chunk) {
                $postCatQuery = $prefixPostCat . implode(',', $chunk);
                $this->wpdb->query($postCatQuery);
            }

            if($this->wpdb->last_error){
                $wpdbError = true;
            }

            if(!$wpdbError){
                set_transient( $this->get_transient_key(), 1, DAY_IN_SECONDS * 15 );
            }

        }
    }


    public function get_transient_key(){
        $var = '';
        if(defined('BDTEP_TPL_DB_VER')){
            $var = BDTEP_TPL_DB_VER;
        }
        return 'ep_elements_demo_import_table_data_' . $var;
    }

    public function checkDemoData() {

        $result = $this->wpdb->get_row("SHOW TABLES LIKE '".$this->table_cat."'", ARRAY_A);
        $tableExists = false;
        if (is_array($result)) {
            if(count($result) == 1) {
                $tableExists = true;
            }
        }

        $demoData = get_transient( $this->get_transient_key() );

        if ( ! $demoData || !$tableExists) {
            $this->createTemplateTables();
        }
    }

    /**
     * @return array|mixed
     * retrieve element pack categories from remote server with api route
     */
    public function remote_get_demo_data()
    {
        $final_url = $this->api_url . 'data/';
        $response = wp_remote_get($final_url, ['timeout' => 60, 'sslverify' => false]);
        $body = wp_remote_retrieve_body($response);
        $body = json_decode($body, true);

        return $body;
    }

    public function getNaviationItems() {

        $this->checkDemoData();
        $demoData = $this->wpdb->get_results("SELECT * FROM {$this->table_cat}",ARRAY_A);

        $navItems = array();
        $totalDemo = 0;
        foreach ( $demoData as $data ) {
            $total = intval($data['total']);
            $totalDemo = $totalDemo + $total;
            $navItems[] = array( 'term_slug' => $data['slug'], 'term_name' => $data['name'],'term_id' => $data['term_id'],'count'=> $total);
        }
        $this->demo_total = $totalDemo;
        $firstItem = array( 'term_slug' => 'demo_term_all', 'term_name' => 'All Templates','term_id' => 0,'count'=> $totalDemo);

        return array_merge_recursive([$firstItem], $navItems);
    }

    public function getData($paged=0){

        //Default values
        $per_page   = $this->perPage;
        $slug       = $this->termSlug;
        $search     = $this->searchVal;
        $demo_tab   = $this->demoType;

        // sorting by Title and Date
        $sortingQuery = '';
        $orderbyTitle       = 'title';
        $sortByTitleType    = $this->sortByTitle;
        if($sortByTitleType =='asc' || $sortByTitleType =='desc'){
            $sortingQuery = " ORDER BY ".$orderbyTitle." ".$sortByTitleType;
        }

        $orderbyDate        = 'demo_id';
        $sortDateType       = $this->sortByDate;
        if($sortDateType =='asc' || $sortDateType =='desc'){
            if($sortingQuery){
                $sortingQuery .= ", ".$orderbyDate." ".$sortDateType;
            }else{
                $sortingQuery = " ORDER BY ".$orderbyDate." ".$sortDateType;
            }
        }

        if(!$sortingQuery){
            $sortingQuery = " ORDER BY ".$orderbyDate." desc";
        }


        // Demo Type : Free or Pro
        if($demo_tab){
            if($demo_tab == 'pro'){
                $demo_tab = 1;
            }elseif($demo_tab == 'free'){
                $demo_tab = 0;
            } else{
                $demo_tab = '';
            }
        }


        if($slug == 'demo_term_all'){
            $slug = false;
        }

        // where conditions
        $keywordSearch = '';
        if($search){
            $searchIn = explode(' ',$search);
            $searchIn = array_filter($searchIn);
            $searchIn = array_map('strtolower', $searchIn);
            foreach($searchIn as $item){
                if(!$keywordSearch){
                    $keywordSearch .= " title LIKE '%$search%' ";
                    $keywordSearch .= "OR title LIKE '%$item%' ";
                }else{
                    $keywordSearch .= "OR title LIKE '%$item%' ";
                }
            }
        }
        $keywordSearch = " title LIKE '%$search%' ";


        if($keywordSearch){
            $keywordSearch = " WHERE ( $keywordSearch ) ";
        }

        if($keywordSearch){
            if($slug){
                $keywordSearch .= " AND slug='$slug' ";
            }
        }else{
            if($slug){
                $keywordSearch .= " WHERE slug='$slug' ";
            }
        }

        if($keywordSearch){
            if($demo_tab === 1 || $demo_tab === 0){
                $keywordSearch .= "AND is_pro=$demo_tab ";
            }
        }else{
            if($demo_tab === 1 || $demo_tab === 0){
                $keywordSearch .= "WHERE is_pro=$demo_tab ";
            }
        }


        // Table Info
        $postTable      = $this->table_post;
        $postCatTable   = $this->table_cat_post;
        $catTable       = $this->table_cat;

        // will be used in pagination settings
        $total_items = $this->wpdb->get_var("SELECT COUNT(DISTINCT {$postTable}.demo_id) FROM {$postTable}
 LEFT JOIN {$postCatTable} ON {$postTable}.demo_id = {$postCatTable}.demo_id
LEFT JOIN {$catTable} ON {$catTable}.term_id = {$postCatTable}.term_id
 $keywordSearch");

        $this->totalPage = ceil($total_items / $per_page);
        $offset = ($paged * $per_page);

        // Load all datas
        $allPagesData = $this->wpdb->get_results("SELECT {$postTable}.*,{$postTable}.thumbnail as preview, {$postCatTable}.term_id, {$catTable}.slug as categories,{$catTable}.slug FROM {$postTable}
 LEFT JOIN {$postCatTable} ON {$postTable}.demo_id = {$postCatTable}.demo_id
LEFT JOIN {$catTable} ON {$catTable}.term_id = {$postCatTable}.term_id
$keywordSearch GROUP BY {$postTable}.demo_id $sortingQuery LIMIT $offset, $per_page", ARRAY_A);

        return $allPagesData;
    }


    public function getElementorLibraryData($paged=0){

        //Default values
        $per_page   = $this->perPage;
        $slug       = $this->termSlug;
        $search     = $this->searchVal;
        $demo_tab   = $this->demoType;

        // sorting by Title and Date
        $sortingQuery = '';
        $orderbyTitle       = 'title';
        $sortByTitleType    = $this->sortByTitle;
        if($sortByTitleType =='asc' || $sortByTitleType =='desc'){
            $sortingQuery = " ORDER BY ".$orderbyTitle." ".$sortByTitleType;
        }

        $orderbyDate        = 'demo_id';
        $sortDateType       = $this->sortByDate;
        if($sortDateType =='asc' || $sortDateType =='desc'){
            if($sortingQuery){
                $sortingQuery .= ", ".$orderbyDate." ".$sortDateType;
            }else{
                $sortingQuery = " ORDER BY ".$orderbyDate." ".$sortDateType;
            }
        }

        if(!$sortingQuery){
            $sortingQuery = " ORDER BY ".$orderbyDate." desc";
        }

        if($slug == 'demo_term_all'){
            $slug = false;
        }

        // where conditions
        $keywordSearch = '';
        if($search){
            $searchIn = explode(' ',$search);
            $searchIn = array_filter($searchIn);
            $searchIn = array_map('strtolower', $searchIn);
            foreach($searchIn as $item){
                if(!$keywordSearch){
                    $keywordSearch .= " title LIKE '%$search%' ";
                    $keywordSearch .= "OR title LIKE '%$item%' ";
                }else{
                    $keywordSearch .= "OR title LIKE '%$item%' ";
                }
            }
        }
        $keywordSearch = " title LIKE '%$search%' ";


        if($keywordSearch){
            $keywordSearch = " WHERE ( $keywordSearch ) ";
        }

        if($keywordSearch){
            if($slug){
                $keywordSearch .= " AND slug='$slug' ";
            }
        }else{
            if($slug){
                $keywordSearch .= " WHERE slug='$slug' ";
            }
        }

        if($keywordSearch){
                $keywordSearch .= "AND type=$demo_tab ";

        }else{
                $keywordSearch .= "WHERE type=$demo_tab ";

        }

        // Table Info
        $postTable      = $this->table_post;
        $postCatTable   = $this->table_cat_post;
        $catTable       = $this->table_cat;

        // will be used in pagination settings
        $total_items = $this->wpdb->get_var("SELECT COUNT(DISTINCT {$postTable}.demo_id) FROM {$postTable}
 LEFT JOIN {$postCatTable} ON {$postTable}.demo_id = {$postCatTable}.demo_id
LEFT JOIN {$catTable} ON {$catTable}.term_id = {$postCatTable}.term_id
 $keywordSearch");

        $this->totalPage = ceil($total_items / $per_page);
        $offset = ($paged * $per_page);

        // Load all datas
        $allPagesData = $this->wpdb->get_results("SELECT {$postCatTable}.term_id,{$postCatTable}.id,
{$postTable}.id as template_id,{$postTable}.demo_id,DATE_FORMAT({$postTable}.date, \"%Y%m%d\") as date,{$postTable}.title,{$postTable}.short_desc,
{$postTable}.is_pro,{$postTable}.type,{$postTable}.thumbnail,{$postTable}.demo_url,{$postTable}.json_url,
 {$catTable}.slug as categories, {$postTable}.is_pro as source FROM {$postTable}
 LEFT JOIN {$postCatTable} ON {$postTable}.demo_id = {$postCatTable}.demo_id
LEFT JOIN {$catTable} ON {$catTable}.term_id = {$postCatTable}.term_id
$keywordSearch $sortingQuery LIMIT $offset, $per_page", ARRAY_A);

        return $allPagesData;
    }

    public function findDemo($id){
        return $this->wpdb->get_row("SELECT * FROM {$this->table_post} WHERE id=$id", ARRAY_A);
    }
}