<?php
/**
 * REST API: BetterDocs_REST_Controller class
 *
 */

class BetterDocs_REST_Controller {

    /**
     * REST API namespace
     * @var string
     */
    private $namespace = 'betterdocs';


    public function __construct() {
        add_action('rest_api_init', array($this, 'register_api_endpoint'));
    }

    public function register_api_endpoint() {

        register_rest_route( $this->namespace, '/plugin_info/', array(
            'methods'   => [ 'GET' ],
            'callback'  => function () {
                return array(
                    'betterdocs_dir_url' => BETTERDOCS_PRO_URL,
                    'betterdocs_rest_url' => get_rest_url(),
                    'betterdocs_version' => BETTERDOCS_VERSION,
                    'betterdocs_pro_version' => BETTERDOCS_PRO_VERSION
                );
            },
            'permission_callback' => '__return_true'
        ));

        register_rest_route( $this->namespace, '/settings/', array(
            'methods'   => [ 'GET' ],
            'callback'  => function () {
                return get_option('betterdocs_settings');
            },
            'permission_callback' => '__return_true'
        ));

        register_rest_route( $this->namespace, '/overview/', array(
            'methods'   => [ 'GET' ],
            'callback'  => function () {
                global $wpdb;
                return $wpdb->get_results(
                    $wpdb->prepare("
                        SELECT impressions as views, sum(happy,sad,normal) as reactions 
                        FROM {$wpdb->prefix}betterdocs_analytics
                        GROUP BY created_at
                        ")
                );
            },
            'permission_callback' => '__return_true'
        ));

        register_rest_route( $this->namespace, '/feedbacks', array(
            'methods'   => [ 'GET' ],
            'callback'  => array( $this, 'fetch_feedbacks' ),
            'permission_callback' => '__return_true'
        ));
        register_rest_route( $this->namespace, '/feedbacks/(?P<type>\S+)', array(
            'methods'   => [ 'GET' ],
            'callback'  => array( $this, 'fetch_feedbacks' ),
            'permission_callback' => '__return_true'
        ));
        register_rest_route( $this->namespace, '/search/(?P<type>\S+)', array(
            'methods'   => [ 'GET' ],
            'callback'  => array($this, 'fetch_search_data'),
            'permission_callback' => '__return_true'
        ));
        register_rest_route( $this->namespace, '/leading_docs', array(
            'methods'   => [ 'GET' ],
            'callback'  => array( $this, 'leading_docs' ),
            'permission_callback' => '__return_true'
        ));

        /**
         * Get leading taxonomy by most view
         * usages - http://betterdocs.test/wp-json/betterdocs/leading_category?page_now=1
         */
        //
        register_rest_route( $this->namespace, '/leading(?P<type>\S+)', array(
            'methods'   => [ 'GET' ],
            'callback'  => array( $this, 'get_leading_category' ),
            'permission_callback' => '__return_true'
        ));

    }

    public function get_docs_views($object = '', $field_name = '', $request = array()) {
        global $wpdb;
        $reactions = $wpdb->get_results(
            $wpdb->prepare("
                SELECT sum(impressions) as totalViews
                FROM {$wpdb->prefix}betterdocs_analytics 
                WHERE post_id = %d",
                $object['id']
            )
        );

        return $reactions[0]->totalViews;
    }

    public function get_post_reactions($object = '', $field_name = '', $request = array()) {
        global $wpdb;
        $reactions = $wpdb->get_results(
            $wpdb->prepare("
                SELECT sum(happy + sad + normal) as totalReactions 
                FROM {$wpdb->prefix}betterdocs_analytics 
                WHERE post_id = %d",
                $object['id']
            )
        );
        return $reactions[0]->totalReactions;
    }

    public function get_doc_category($objectID) {
        $terms = get_the_terms( $objectID, 'doc_category' );
        if ($terms != false) {
            return $terms;
        }
        return '';
    }

    public function get_knowledge_base($objectID) {
        $terms = get_the_terms( $objectID, 'knowledge_base' );
        if ($terms != false) {
            return $terms;
        }
        return '';
    }

    public function get_author($objectID) {
        $user_id = get_post_field( 'post_author', $objectID );
        return array(
            'display_name' => get_the_author_meta( 'display_name', $user_id ),
            'avatar' => get_avatar_url( $user_id )
        );
    }

    public function fetch_search_data($params) {
        global $wpdb;
        $type = $params->get_param('type');
        $start_date = ($params->get_param('start_date')) ? $params->get_param('start_date') : '';
        $end_date = ($params->get_param('end_date')) ? $params->get_param('end_date') : '';
        $join = "FROM {$wpdb->prefix}betterdocs_search_keyword as search_keyword 
                JOIN {$wpdb->prefix}betterdocs_search_log as search_log on search_keyword.id = search_log.keyword_id";

        if ($type == 'not_found') {
            $select = "SELECT search_keyword.keyword, SUM(search_log.not_found_count) as not_found_count";
            $orderby = 'not_found_count';
            $count = count($wpdb->get_results(
                $wpdb->prepare("
                    {$select}
                    {$join}
                    WHERE not_found_count > 0
                    GROUP BY search_log.keyword_id
                ")
            ));
        } else {
            $select = "SELECT search_keyword.keyword, SUM(search_log.count) as count";
            $orderby = 'count';
            $count = count($wpdb->get_results(
                $wpdb->prepare("
                    {$select}
                    {$join}
                    WHERE count > 0
                    GROUP BY search_log.keyword_id
                ")
            ));
        }

        $per_page = ($params->get_param('per_page')) ? $params->get_param('per_page') : 10;
        $total_page = ceil($count / $per_page);
        $page_now = ($params->get_param('page_now')) ? $params->get_param('page_now') : 1;
        $offset = ($page_now * $per_page) - $per_page;
        $paging = "ORDER BY ${orderby} DESC LIMIT ${offset}, ${per_page}";

        if ($type == 'not_found') {
            $results = $wpdb->get_results(
                $wpdb->prepare("
                    {$select}
                    {$join}
                    WHERE not_found_count > 0
                    GROUP BY search_log.keyword_id
                    {$paging}
                ")
            );
        } else if ($type == 'all') {
            $results = $wpdb->get_results(
                $wpdb->prepare("
                    {$select}
                    {$join}
                    WHERE count > 0
                    GROUP BY search_log.keyword_id
                    {$paging}
                ")
            );
        } else if ($type == 'date' && $start_date && $end_date) {
            return $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT created_at as search_date, SUM(count + not_found_count) as search_count, SUM(count) as search_found, SUM(not_found_count) as search_not_found_count 
                    FROM {$wpdb->prefix}betterdocs_search_log as search_log
                    WHERE (search_log.created_at BETWEEN '".$start_date."' AND '".$end_date."')
                    GROUP BY search_log.created_at
                    ORDER BY search_log.created_at DESC"
                )
            );
        } else if ($type == 'date') {
            return $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT created_at as search_date, SUM(count + not_found_count) as search_count, SUM(count) as search_found, SUM(not_found_count) as search_not_found_count 
                    FROM {$wpdb->prefix}betterdocs_search_log as search_log
                    GROUP BY search_log.created_at
                    ORDER BY search_log.created_at DESC"
                )
            );
        } else if ($type == 'overview') {
            return $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT SUM(count + not_found_count) as total_search, SUM(count) as total_search_found, SUM(not_found_count) as total_search_not_found 
                    FROM {$wpdb->prefix}betterdocs_search_log as search_log"
                )
            );
        } else if ( !empty( $type ) && $type == 'totalCount') {
            $where = ( $start_date && $end_date ) ? "WHERE (created_at BETWEEN '".$start_date."' AND '".$end_date."')" : "";

            $totalSearch = $wpdb->get_results(
                $wpdb->prepare("SELECT sum(count + not_found_count) as totalSearch,
                    sum(count) as totalFound,
                    sum(not_found_count) as totalNotFound
                    FROM {$wpdb->prefix}betterdocs_search_log 
                    {$where}"
                )
            );

            return [
                'totalSearch' => $totalSearch[0]->totalSearch,
                'totalFound' => $totalSearch[0]->totalFound,
                'totalNotFound' => $totalSearch[0]->totalNotFound
            ];
        }


        return [
            'pagination' => [
                'total_page' => $total_page,
                'page_now' => $page_now,
            ],
            'search' => $results
        ];
    }

    public function fetch_feedbacks($params) {
        global $wpdb;
        $type = $params->get_param('type');
        $start_date = ($params->get_param('start_date')) ? $params->get_param('start_date') : '';
        $end_date = ($params->get_param('end_date')) ? $params->get_param('end_date') : '';
        $post_id = ($params->get_param('post_id')) ? $params->get_param('post_id') : '';

        $select = "SELECT analytics.post_id, docs.post_title, SUM(analytics.happy) as happy, SUM(analytics.sad) as sad, SUM(analytics.normal) as normal, analytics.created_at";
        $from = "FROM {$wpdb->prefix}betterdocs_analytics AS analytics";
        $left_join = "LEFT JOIN {$wpdb->prefix}posts AS docs ON docs.ID = analytics.post_id";
        $where = "WHERE docs.post_type = 'docs' AND docs.post_status = 'publish'";

        $results = $wpdb->get_results(
            $wpdb->prepare(
                "{$select}
                {$from}
                {$left_join}
                {$where}
                GROUP BY analytics.created_at
                ORDER BY analytics.created_at DESC"
            )
        );

        if (!empty($type) && $type == 'docs') {
            $orderby = ($params->get_param('orderby')) ? $params->get_param('orderby') : 'most_helpful';
            if ($orderby == 'least_helpful') {
                $where = "WHERE docs.post_type = 'docs' AND docs.post_status = 'publish' AND sad > 0";
            } else {
                $where = "WHERE docs.post_type = 'docs' AND docs.post_status = 'publish' AND happy > 0";
            }

            $count = count($wpdb->get_results(
                "{$select}
                {$from}
                {$left_join}
                {$where}
                GROUP BY analytics.post_id"
            ));

            $per_page = ($params->get_param('per_page')) ? $params->get_param('per_page') : 10;
            $total_page = ceil($count / $per_page);
            $page_now = ($params->get_param('page_now')) ? $params->get_param('page_now') : 1;
            $offset = ($page_now * $per_page) - $per_page;

            if ($orderby == 'least_helpful') {
                $paging = "ORDER BY sad DESC LIMIT ${offset}, ${per_page}";
            } else {
                $paging = "ORDER BY happy DESC LIMIT ${offset}, ${per_page}";
            }

            $select = "SELECT analytics.post_id, docs.post_title, sum(analytics.happy) as happy, sum(analytics.sad) as sad, sum(analytics.normal) as normal";

            $docs = $wpdb->get_results(
                $wpdb->prepare("{$select}
                {$from}
                {$left_join}
                {$where}
                GROUP BY analytics.post_id
                {$paging}")
            );

            $docs_arr = [];
            foreach ($docs as $key=>$value) {
                $docs_arr[$key] = [
                    'post_id' => $value->post_id,
                    'post_title' => wp_kses($value->post_title, BETTERDOCS_PRO_KSES_ALLOWED_HTML),
                    'happy' => $value->happy,
                    'normal' => $value->normal,
                    'sad' => $value->sad,
                    'link' => get_permalink($value->post_id)
                ];
            }

            return [
                'pagination' => [
                    'total_page' => $total_page,
                    'page_now' => $page_now,
                ],
                'docs' => $docs_arr
            ];
        } else if (!empty($type) && $type == 'overview' && $start_date && $end_date && $post_id) {
            return $wpdb->get_results(
                $wpdb->prepare("
                    SELECT post_id, sum(impressions) as views, sum(unique_visit) as unique_visit, sum(happy + sad + normal) as reactions, created_at as date 
                    FROM {$wpdb->prefix}betterdocs_analytics
                    WHERE (post_id='".$post_id."' AND created_at BETWEEN '".$start_date."' AND '".$end_date."')
                    GROUP BY created_at
                    ORDER BY created_at DESC
                ")
            );
        } else if (!empty($type) && $type == 'overview' && $start_date && $end_date) {
            return $wpdb->get_results(
                $wpdb->prepare("
                    SELECT post_id, sum(impressions) as views, sum(unique_visit) as unique_visit, sum(happy + sad + normal) as reactions, created_at as date 
                    FROM {$wpdb->prefix}betterdocs_analytics
                    WHERE (created_at BETWEEN '".$start_date."' AND '".$end_date."')
                    GROUP BY created_at
                    ORDER BY created_at DESC
                ")
            );
        } else if (!empty($type) && $type == 'overview' && $post_id) {
            return $wpdb->get_results(
                $wpdb->prepare("
                    SELECT post_id, sum(impressions) as views, sum(unique_visit) as unique_visit, sum(happy + sad + normal) as reactions, created_at as date 
                    FROM {$wpdb->prefix}betterdocs_analytics
                    WHERE post_id='".$post_id."'
                    GROUP BY created_at
                    ORDER BY created_at DESC
                ")
            );
        } else if (!empty($type) && $type == 'overview') {
            return $wpdb->get_results(
                $wpdb->prepare("
                    SELECT post_id, sum(impressions) as views, sum(unique_visit) as unique_visit, sum(happy + sad + normal) as reactions, created_at as date 
                    FROM {$wpdb->prefix}betterdocs_analytics
                    GROUP BY created_at
                    ORDER BY created_at DESC
                ")
            );
        } else if (!empty($type) && $type == 'totalCount') {
            $where = '';
            if ( $start_date && $end_date && $post_id ) {
                $where = "WHERE post_id='".$post_id."' AND (created_at BETWEEN '".$start_date."' AND '".$end_date."')";
            } else if ( $start_date && $end_date ) {
                $where = "WHERE (created_at BETWEEN '".$start_date."' AND '".$end_date."')";
            } else if ( $post_id ) {
                $where = "WHERE post_id='".$post_id."'";
            }

            $analytics = $wpdb->get_results(
                $wpdb->prepare("SELECT sum(impressions) as totalViews,
                    sum(unique_visit) as totalUniqueViews,
                    sum(happy + sad + normal) as totalReactions,
                    sum(happy) as totalHappy,
                    sum(normal) as totalNormal,
                    sum(sad) as totalSad
                    FROM {$wpdb->prefix}betterdocs_analytics
                    {$where}"
                )
            );

            $totalCount = [
                'totalViews' => $analytics[0]->totalViews,
                'totalUniqueViews' => $analytics[0]->totalUniqueViews,
                'totalReactions' => $analytics[0]->totalReactions,
                'totalHappy' => $analytics[0]->totalHappy,
                'totalNormal' => $analytics[0]->totalNormal,
                'totalSad' => $analytics[0]->totalSad
            ];

            if ( empty( $post_id ) ) {
                $where = ( $start_date && $end_date ) ? "WHERE (created_at BETWEEN '".$start_date."' AND '".$end_date."')" : "";

                $totalSearch = $wpdb->get_results(
                    $wpdb->prepare("SELECT sum(count + not_found_count) as totalSearch,
                        sum(count) as totalFound,
                        sum(not_found_count) as totalNotFound
                        FROM {$wpdb->prefix}betterdocs_search_log 
                        {$where}"
                    )
                );

                $totalCount['totalSearch'] = $totalSearch[0]->totalSearch;
                $totalCount['totalFound'] = $totalSearch[0]->totalFound;
                $totalCount['totalNotFound'] = $totalSearch[0]->totalNotFound;
            }

            return $totalCount;

        } else if ( $start_date && $end_date ) {
            $select = "SELECT post_id, sum(analytics.impressions) as impressions, sum(analytics.unique_visit) as unique_visit, sum(analytics.happy) as happy, sum(analytics.sad) as sad, sum(analytics.normal) as normal, analytics.created_at";
            return $wpdb->get_results(
                $wpdb->prepare(
                    "{$select}
                    {$from}
                    {$left_join}
                    WHERE (analytics.created_at BETWEEN '".$start_date."' AND '".$end_date."')
                    GROUP BY analytics.created_at
                    ORDER BY analytics.created_at DESC"
                )
            );
        }

        return $results;
    }

    public function get_leading_category($params) {
        $type = $params->get_param('type');

        if (!empty($type) && $type == '_category') {
            $term = 'doc_category';
        } else if (!empty($type) && $type == '_kb') {
            $term = 'knowledge_base';
        } else {
            return;
        }

        global $wpdb;

        $select = "SELECT DISTINCT {$wpdb->prefix}terms.name, {$wpdb->prefix}terms.slug, SUM({$wpdb->prefix}betterdocs_analytics.impressions) as total_view, SUM({$wpdb->prefix}betterdocs_analytics.unique_visit) as total_unique_visit, SUM({$wpdb->prefix}betterdocs_analytics.happy + {$wpdb->prefix}betterdocs_analytics.sad + {$wpdb->prefix}betterdocs_analytics.normal) as total_reactions";
        $join = "JOIN {$wpdb->prefix}term_relationships on {$wpdb->prefix}postmeta.post_id = {$wpdb->prefix}term_relationships.object_id
                JOIN {$wpdb->prefix}betterdocs_analytics on {$wpdb->prefix}term_relationships.object_id = {$wpdb->prefix}betterdocs_analytics.post_id
                JOIN {$wpdb->prefix}terms on {$wpdb->prefix}term_relationships.term_taxonomy_id = {$wpdb->prefix}terms.term_id
                JOIN {$wpdb->prefix}term_taxonomy on {$wpdb->prefix}terms.term_id = {$wpdb->prefix}term_taxonomy.term_id
                JOIN {$wpdb->prefix}posts on {$wpdb->prefix}posts.ID = {$wpdb->prefix}betterdocs_analytics.post_id
                WHERE {$wpdb->prefix}postmeta.meta_key = '_betterdocs_meta_views' && {$wpdb->prefix}posts.post_status = 'publish'
                AND {$wpdb->prefix}term_taxonomy.taxonomy = '".$term."'";

        $count = count($wpdb->get_results(
            $wpdb->prepare("{$select}
                FROM {$wpdb->prefix}postmeta
                {$join}
                GROUP BY {$wpdb->prefix}terms.slug")
        ));

        $per_page = ($params->get_param('per_page')) ? $params->get_param('per_page') : 10;
        $total_page = ceil($count / $per_page);
        $page_now = ($params->get_param('page_now')) ? $params->get_param('page_now') : 1;
        $offset = ($page_now * $per_page) - $per_page;
        $paging = "ORDER BY total_view DESC LIMIT ${offset}, ${per_page}";

        $results = $wpdb->get_results(
            $wpdb->prepare("{$select}
                FROM {$wpdb->prefix}postmeta
                {$join}
                GROUP BY {$wpdb->prefix}terms.slug
                {$paging}")
        );

        return [
            'pagination' => [
                'total_page' => $total_page,
                'page_now' => $page_now,
            ],
            'doc_category' => $results
        ];
    }

    public function leading_docs( $params ) {
        global $wpdb;
        $select = "SELECT docs.ID, docs.post_author, docs.post_title, SUM(analytics.impressions) as total_views, SUM(analytics.unique_visit) as total_unique_visit, SUM(analytics.happy + analytics.sad + analytics.normal) as total_reactions";
        $join = "FROM {$wpdb->prefix}posts as docs 
                JOIN {$wpdb->prefix}betterdocs_analytics as analytics on docs.ID = analytics.post_id";

        $count = count($wpdb->get_results(
            $wpdb->prepare("
                    {$select}
                    {$join}
                    WHERE post_type = 'docs' AND post_status = 'publish'
                    GROUP BY analytics.post_id
                ")
        ));

        $per_page = ($params->get_param('per_page')) ? $params->get_param('per_page') : 10;
        $total_page = ceil($count / $per_page);
        $page_now = ($params->get_param('page_now')) ? $params->get_param('page_now') : 1;
        $offset = ($page_now * $per_page) - $per_page;
        $paging = "ORDER BY total_views DESC LIMIT ${offset}, ${per_page}";

        $results = $wpdb->get_results(
            $wpdb->prepare("
                    {$select}
                    {$join}
                    WHERE post_type = 'docs' AND post_status = 'publish'
                    GROUP BY analytics.post_id
                    {$paging}
                ")
        );

        $docs = array();

        foreach ($results as $key=>$value) {
            $docs[$key] = $this->get_docs_items( $value );
        }
        return [
            'pagination' => [
                'total_page' => $total_page,
                'page_now' => $page_now,
            ],
            'docs' => $docs
        ];
    }

    /**
     * Prepares a docs items
     */
    protected function get_docs_items( $request ) {
        $prepared_post  = new stdClass();

        if ( isset( $request->ID ) ) {
            $prepared_post->ID = $request->ID;
        }

        if ( isset( $request->post_title ) ) {
            $prepared_post->title = wp_kses($request->post_title, BETTERDOCS_PRO_KSES_ALLOWED_HTML);
        }

        if ( isset( $request->total_views ) ) {
            $prepared_post->total_views = $request->total_views;
        }

        if ( isset( $request->total_unique_visit ) ) {
            $prepared_post->total_unique_visit = $request->total_unique_visit;
        }

        if ( isset( $request->total_reactions ) ) {
            $prepared_post->total_reactions = $request->total_reactions;
        }

        if ( isset( $request->ID ) ) {
            $prepared_post->doc_category_terms = $this->get_doc_category($request->ID);
        }
        if (BetterDocs_Multiple_Kb::$enable == 1 && isset( $request->ID )) {
            $prepared_post->knowledge_base_terms = $this->get_knowledge_base($request->ID);
        }

        if ( isset( $request->ID ) ) {
            $prepared_post->link = get_permalink($request->ID);
        }

        if ( isset( $request->ID ) ) {
            $prepared_post->author = $this->get_author($request->ID);
        }

        return $prepared_post;
    }
}

if( class_exists('BetterDocs') ) {
    new BetterDocs_REST_Controller;
}