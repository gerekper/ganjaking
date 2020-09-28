<?php 
/**
 * This class is responsible for making stats for each BetterDocs
 * 
 * @since 1.0.2
 */
class BetterDocsPro_Analytics {
    /**
     * Get a single Instance of Analytics
     * @var 
     */
    private static $_instance = null;
    /**
     * List of BetterDocs
     * @var arrau
     */
    private static $betterdocs = array();
    private $impressions = [];
    private $results = null;
    /**
     * Colors for Bar
     */
    private $colors = array(
        '#1abc9c',
        '#27ae60',
        '#3498db',
        '#8e44ad',
        '#e67e22',
        '#e74c3c',
        '#f39c12',
        '#34495e',
        '#9b59b6',
        '#16a085'
    );
    /**
     * View Options
     */
    private $views_options = array(
        'analytics_from' => 'everyone',
        'exclude_bot_analytics' => 1
    );

    public function __construct() {
        add_action( 'wp_head', array( $this, 'analytics_data' ) );
        add_action( 'admin_init', array( $this, 'betterdocs' ) );
        add_action( 'betterdocs_admin_menu', array( $this, 'add_analytics_menu' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueues' ) );
        add_action( 'betterdocs_before_settings_load', array( $this, 'add_settings' ) );
        add_action( 'wp_ajax_betterdocs_pro_analytics', array( $this, 'clicked_analytics_data' ) );
        add_action( 'wp_ajax_nopriv_betterdocs_pro_analytics', array( $this, 'clicked_analytics_data' ) );
        add_action( 'wp_ajax_betterdocs_analytics_calc', array( $this, 'analytics_calc' ) );
        // add_action( 'notificationx_settings_header', array( $this, 'stats_counter' ), 11 );
        // add_action( 'notificationx_admin_header', array( $this, 'stats_counter' ), 11 );
        // add_action( 'notificationx_after_analytics_header', array( $this, 'stats_counter' ), 11 );
        add_action( 'manage_docs_posts_columns', array( $this, 'custom_columns' ) );
        add_action( 'manage_docs_posts_custom_column', array( $this, 'manage_custom_columns' ), 10, 2 );
    }

	public function custom_columns( $columns ) {
		$date_column = $columns['date'];
		unset( $columns['date'] );
		$columns['betterdocs_views']   = __('Views', 'betterdocs-pro');
		$columns['date'] = $date_column;
		return apply_filters('betterdocs_post_columns', $columns );
	}

    public function manage_custom_columns( $column, $post_id ){
		switch ( $column ) {
            case 'betterdocs_views':
                $views = get_post_meta( $post_id, '_betterdocs_meta_views', true );
                $analytics_url = admin_url( 'admin.php?page=betterdocs-analytics&betterdocs=' . $post_id . '&comparison_factor=views,feelings' );
                echo ! empty( $views ) ? '<a href="'. $analytics_url .'">'. $views .'</a>' : 0;
				break;
		}
		do_action( 'betterdocs_post_columns_content', $column, $post_id );
	}

    public static function betterdocs(){
        $betterdocs = new WP_Query(array(
            'post_type'      => 'docs',
            'posts_per_page' => -1,
        ));

        return self::$betterdocs = $betterdocs->posts;
    }

    /**
     * Get || Making a Single Instance of Analytics
     * @return self
     */
    public static function get_instance(){
        if( self::$_instance === null ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    /**
     * This method is responsible for adding analytics in Menu
     * @return void
     */
    public function add_analytics_menu( $pages ){

        $betterdocs_analytics_caps = apply_filters( 'betterdocs_analytics_caps', 'administrator', 'analytics_roles' );

        $pages['betterdocs-analytics'] = array(
            'title'      => __('Analytics', 'betterdocs-pro'),
            'capability' => $betterdocs_analytics_caps,
            'callback'   => array( $this, 'page_outputs' )
        );

        return $pages;
    }
    /**
     * This method is responsible for adding analytics page frontend.
     * @return void
     */
    public function page_outputs(){
        $comparison_factor_list = array(
            'views' => 'Views',
            'feelings' => 'Feelings',
            // 'clicks' => 'Clicks',
            // 'ctr' => 'CTR',
        );

        if( file_exists( BETTERDOCS_PRO_ADMIN_DIR_PATH . 'partials/betterdocs-pro-admin-analytics-display.php' ) ) {
            return include_once BETTERDOCS_PRO_ADMIN_DIR_PATH . 'partials/betterdocs-pro-admin-analytics-display.php';
        }
    }
    /**
     * This method is responsible for output the stats value in admin table
     * @return void
     */
    public function stats_output( $output, $idd ){
        if( empty( $idd ) ) {
            return 0;
        }
        $output = get_post_meta( $idd, '_betterdocs_meta_views', true );
        $analytics_url = admin_url( 'admin.php?page=betterdocs-analytics&betterdocs=' . $idd . '&comparison_factor=views,feelings' );
        $format = '<a href="'. esc_url( $analytics_url ) .'">%s</a>';

        if( empty( $output ) ) {
            return sprintf( $format, '0 views');
        }
        return sprintf( $format, $output . __(' views', 'betterdocs-pro') );
    }

    public function stats_counter(){
        global $pagenow;
        $class = '';
        if( ! empty( $pagenow ) ) {
            $class = 'nx-header-for-' . str_replace('.php', '', $pagenow);
        }

        global $wpdb;
        $ids = false;

        $inner_sql = "SELECT DISTINCT INNER_POSTS.ID, INNER_POSTS.post_title FROM $wpdb->posts AS INNER_POSTS INNER JOIN $wpdb->postmeta AS INNER_META ON INNER_POSTS.ID = INNER_META.post_id WHERE INNER_POSTS.post_type = '%s'";

        $query = $wpdb->prepare(
            "SELECT META.meta_key as `key`, SUM( META.meta_value ) as `value` FROM ( $inner_sql ) as POSTS INNER JOIN $wpdb->postmeta as META ON POSTS.ID = META.post_id WHERE META.meta_key IN ( '_betterdocs_meta_views', '_betterdocs_meta_clicks' ) GROUP BY META.meta_key", 
            array(
                'docs',
            )
        );
        $results = $wpdb->get_results( $query );

        $views = $clicks = $ctr = 0;
        if( ! empty( $results ) ) { 
            foreach( $results as $result ) {
                if( isset( $result->key ) && $result->key === '_betterdocs_meta_views' ) {
                    $views = $result->value;
                }
                if( isset( $result->key ) && $result->key === '_betterdocs_meta_clicks' ) {
                    $clicks = $result->value;
                }
            }
        }

        $ctr = $views > 0 ? number_format( ( intval( $clicks ) / intval( $views ) ) * 100, 2) : 0;

        $views = self::nice_number( $views );
        $clicks = self::nice_number( $clicks );

        $views_link = admin_url( 'admin.php?page=betterdocs-analytics&comparison_factor=views' );
        $clicks_link = admin_url( 'admin.php?page=betterdocs-analytics&comparison_factor=clicks' );
        $ctr_link = admin_url( 'admin.php?page=betterdocs-analytics&comparison_factor=ctr' );
        
        if( file_exists( BETTERDOCS_PRO_ADMIN_DIR_PATH . 'partials/betterdocs-pro-admin-analytics-counter.php' ) ) {
            return include_once BETTERDOCS_PRO_ADMIN_DIR_PATH . 'partials/betterdocs-pro-admin-analytics-counter.php';
        }
    }

	public function enqueues( $hook ) {
		if( $hook !== 'betterdocs_page_betterdocs-analytics' ) {
			return;
        }
        wp_enqueue_style( 
			'betterdocs', 
			BETTERDOCS_ADMIN_URL . 'assets/css/betterdocs-admin.min.css', 
			array(), '1.0.1', 'all' 
        );
        wp_enqueue_style( 
			'betterdocs-select2', 
			BETTERDOCS_ADMIN_URL . 'assets/css/select2.min.css', 
			array(), '1.0.1', 'all' 
		);
        wp_enqueue_style( 
			'betterdocs-pro-chart', 
			BETTERDOCS_PRO_ADMIN_URL . 'css/Chart.css', 
			array(), '1.0.1', 'all' 
        );
		wp_enqueue_style( 
			'betterdocs-pro-analytics', 
			BETTERDOCS_PRO_ADMIN_URL . 'css/betterdocs-analytics.css', 
			array(), '1.0.1', 'all' 
        );
        wp_enqueue_script( 'jquery-ui-datepicker' );
        wp_enqueue_script( 
			'betterdocs-select2', 
			BETTERDOCS_ADMIN_URL . 'assets/js/select2.min.js', 
			array( 'jquery' ), '1.0.1', true 
		);
		wp_enqueue_script( 
			'betterdocs-chartjs', 
			BETTERDOCS_PRO_ADMIN_URL . 'js/Chart.min.js', 
			array( 'jquery' ), '1.0.1', true 
		);
		wp_enqueue_script( 
			'betterdocs-pro-analytics', 
			BETTERDOCS_PRO_ADMIN_URL . 'js/betterdocs-analytics.js', 
			array( 'jquery', 'jquery-ui-datepicker', 'betterdocs-chartjs' ), '1.0.1', true 
        );
    }
    protected function labels( $query_vars = array() ){
        $current_date = date('d-m-Y', current_time('timestamp'));
        $start_date = date('d-m-Y', strtotime( $current_date . ' -7days' ));
        if( isset( $query_vars['start_date'] ) && ! empty( $query_vars['start_date'] ) ) {
            $start_date = $query_vars['start_date'];
        }

        if( isset( $query_vars['end_date'] ) && ! empty( $query_vars['end_date'] ) ) {
            $current_date = $query_vars['end_date'];
        }

        $dates = array();
        $start_date_diff = new DateTime( $start_date );
        $current_date_diff = new DateTime( $current_date );
        $diff = $current_date_diff->diff($start_date_diff);
        $counter = isset( $diff->days ) ? $diff->days : 0;
        for( $i = 0; $i <= $counter; $i++ ) {
            $date = $i === 0 ? $start_date : $start_date . " +$i days";
            $dates[] = date( 'M d', strtotime( $date ) );
        }

        $this->dates = $dates;

        return $dates;
    }
    protected function datasets( $query_vars = array() ){
        global $wpdb;

        $ids = $betterdocs_all = false;
        $extra_sql_input = $extra_sql = $xTra_SQL = '';
        if( ! isset( $query_vars['betterdocs'] ) ) {
            $ids = true;
            $betterdocs_all = true;
        }


        if( isset( $query_vars['betterdocs'] ) ) {
            $betterdocs = trim($query_vars['betterdocs']);
            if( strpos( $betterdocs, 'all' ) === false ) {
                $ids = false;
            } else {
                $betterdocs_all = true;
                $ids = true;
            }
        }

        if( ! $ids ) {
            $extra_sql_input = $betterdocs;
            $extra_sql = "AND POSTS.ID IN ( $extra_sql_input )";
            $xTra_SQL = "WHERE D_POSTS.ID IN ( $extra_sql_input )";
        }

        $sql = "SELECT D_POSTS.ID, D_POSTS.post_title, FEELINGS_IMPRESSIONS.meta_key, FEELINGS_IMPRESSIONS.meta_value  FROM ( SELECT POSTS.ID, POSTS.post_title, META.meta_key, META.meta_value FROM $wpdb->posts AS POSTS LEFT JOIN $wpdb->postmeta AS META ON ( POSTS.ID = META.post_id ) WHERE 1 = 1 AND ( META.meta_key = %s OR META.meta_key = %s ) AND POSTS.post_type = %s AND ( ( POSTS.post_status = %s ) ) ) AS FEELINGS_IMPRESSIONS RIGHT JOIN $wpdb->posts AS D_POSTS ON ( FEELINGS_IMPRESSIONS.ID = D_POSTS.ID ) $xTra_SQL";

        $query = $wpdb->prepare(
            $sql,
            array(
                '',
                '_betterdocs_meta_impression_per_day',
                'docs',
                'publish',
            )
        );
        $results = $wpdb->get_results( $query, ARRAY_A );

        $default_value = array(
            "fill" => false,
        );

        $datasets = $views = $data = $impressions = $comaprison_factor = $available_data = array();
        $this->impressions['views'] = $this->impressions['normal'] = $this->impressions['sad'] = $this->impressions['happy'] = $impressions = $clicks = $ctr = $sad = $happy = $normal = array_fill_keys( $this->dates, 0 );

        if( isset( $query_vars['comparison_factor'] ) && ! empty( $query_vars['comparison_factor'] ) && $query_vars['comparison_factor'] != null ) {
            if( strpos( $query_vars['comparison_factor'], ',' ) !== false && strpos( $query_vars['comparison_factor'], ',' ) >= 0 ) {
                $comaprison_factor = explode( ',', $query_vars['comparison_factor'] );
            } else {
                if( $query_vars['comparison_factor'] != 'undefined' ) {
                    $comaprison_factor = [ $query_vars['comparison_factor'] ];
                }
            }
        }

        if( empty( $comaprison_factor ) ) {
            $comaprison_factor = array( 'views', 'feelings' );
        }
        $number_of_impressions = $number_of_clicks = $number_of_happy = $number_of_sad = $number_of_normal = $max_stepped_size = 0;

        if( ! empty( $results ) ) {
            $index = 0;
            $all_available_data = [];

            foreach( $results as $value ) {
                $unserialize = unserialize( $value['meta_value'] );
                if( ! $unserialize ) {
                    continue;
                }

                if( ! empty( $unserialize ) ) {
                    foreach( $unserialize as $date => $single ) {
                        $temp_date = date('M d', strtotime( $date ));
                        if( isset( $impressions[ $temp_date ] ) ) {
                            $impressions[ $temp_date ] = $number_of_impressions = isset( $single['impressions'] ) ? $single['impressions'] : 0;
                        }
                        if( in_array( 'views', $comaprison_factor ) ) {
                            $available_data[ 'views' ] = $impressions;
                            if( $max_stepped_size < $number_of_impressions ) {
                                $max_stepped_size = $number_of_impressions;
                            }
                        }

                        // HAPPY FEELINGS
                        if( isset( $happy[ $temp_date ] ) ) {
                            $happy[ $temp_date ] = $number_of_happy = isset( $single['happy'] ) ? $single['happy'] : 0;
                        }
                        if( in_array( 'feelings', $comaprison_factor ) ) { 
                            $available_data[ 'happy' ] = $happy;
                            if( $max_stepped_size < $number_of_happy ) {
                                $max_stepped_size = $number_of_happy;
                            }
                        }
                        // SAD FEELINGS
                        if( isset( $sad[ $temp_date ] ) ) {
                            $sad[ $temp_date ] = $number_of_sad = isset( $single['sad'] ) ? $single['sad'] : 0;
                        }
                        if( in_array( 'feelings', $comaprison_factor ) ) { 
                            $available_data[ 'sad' ] = $sad;
                            if( $max_stepped_size < $number_of_sad ) {
                                $max_stepped_size = $number_of_sad;
                            }
                        }
                        // NORMAL FEELINGS
                        if( isset( $normal[ $temp_date ] ) ) {
                            $normal[ $temp_date ] = $number_of_normal = isset( $single['normal'] ) ? $single['normal'] : 0;
                        }
                        if( in_array( 'feelings', $comaprison_factor ) ) { 
                            $available_data[ 'normal' ] = $normal;
                            if( $max_stepped_size < $number_of_normal ) {
                                $max_stepped_size = $number_of_normal;
                            }
                        }

                        // if( isset( $clicks[ $temp_date ] ) ) {
                        //     $clicks[ $temp_date ] = $number_of_clicks = isset( $single['clicks'] ) ? $single['clicks'] : 0;
                        // }
                        // if( in_array( 'clicks', $comaprison_factor ) ) { 
                        //     $available_data[ 'clicks' ] = $clicks;
                        //     if( $max_stepped_size < $number_of_clicks ) {
                        //         $max_stepped_size = $number_of_clicks;
                        //     }
                        // }
                        // if( in_array( 'ctr', $comaprison_factor ) ) { 
                        //     $ctr[ $temp_date ] = $number_of_ctr = $number_of_impressions > 0 ? number_format( ( intval( $number_of_clicks ) / intval( $number_of_impressions ) ) * 100, 2) : 0;
                        //     $available_data[ 'ctr' ] = $ctr;
                        //     if( $max_stepped_size < $number_of_ctr ) {
                        //         $max_stepped_size = $number_of_ctr;
                        //     }
                        // }

                        $number_of_impressions = $number_of_clicks = $number_of_happy = $number_of_sad = $number_of_normal  = 0;
                    }
                    //TODO: has to check again and again.
                    if( $available_data && ! $betterdocs_all ) {
                        foreach( $available_data as $factor => $factor_data ){
                            $data['data'] = array_values( $factor_data );
                            $data = array_merge( $default_value, $data );
                            $color = $this->random_color( ++$index );
                            $data['backgroundColor'] = $color;
                            $data['borderColor'] = $color;
                            $factor_label = $factor == 'normal' ? 'Neutral' : ucwords( $factor );
                            $data['label'] = $value['post_title'] . ' - ' . $factor_label;
                            $data['labelString'] = 'Impressions';
                            $this->results[ $value['ID'] . '_' . $factor ] = $data;
                            $this->results[ 'stepped_size' ] = $max_stepped_size;
                        }
                    }
                }
            }
            // FOR ALL VIEWS, FEELINGS
            if( $betterdocs_all ) {
                array_walk_recursive( $results, function( $value, $key, $userdata ){
                    if( $key === 'meta_value' ) {
                        $unserialize = unserialize( $value );
                        if( is_array( $unserialize ) ) {
                            array_walk( $unserialize, function( $value, $date, $userdata ){
                                $temp_date = date('M d', strtotime( $date ));
                                $comaprison_factor = $userdata[ 'comaprison_factor' ];
                                if( in_array( 'views', $comaprison_factor ) ) {
                                    if( isset( $this->impressions['views'][ $temp_date ] ) ) {
                                        $this->impressions['views'][ $temp_date ] += isset( $value['impressions'] ) ? $value['impressions'] : 0;
                                    }
                                }
                                if( in_array( 'feelings', $comaprison_factor ) ) {
                                    if( isset( $this->impressions['happy'][ $temp_date ] ) ) {
                                        $this->impressions['happy'][ $temp_date ] += isset( $value['happy'] ) ? $value['happy'] : 0;
                                    }
                                    if( isset( $this->impressions['normal'][ $temp_date ] ) ) {
                                        $this->impressions['normal'][ $temp_date ] += isset( $value['normal'] ) ? $value['normal'] : 0;
                                    }
                                    if( isset( $this->impressions['sad'][ $temp_date ] ) ) {
                                        $this->impressions['sad'][ $temp_date ] += isset( $value['sad'] ) ? $value['sad'] : 0;
                                    }
                                }
                            }, $userdata );
                        }
                    }
                }, array( 'comaprison_factor' => $comaprison_factor ) );
                $data = [];
                foreach( $this->impressions as $factor => $single_factor_data ){
                    $feelings = [ 'happy', 'sad', 'normal' ];
                    $kFactor = in_array( $factor , $feelings ) ? 'feelings' : $factor;
                    if( ! in_array( $kFactor, $comaprison_factor ) ) {
                        unset( $this->impressions [ $factor ] );
                        continue;
                    }
                    foreach( $single_factor_data as $single ){
                        if( $max_stepped_size < $single ) {
                            $max_stepped_size = $single;
                        }
                    }
                }
                // FIXME: need to check lower php version
                $max_stepped_size = round( $max_stepped_size,  -( strlen( $max_stepped_size ) - 1 ) );

                foreach( $this->impressions as $factor => $factor_data ){
                    $data['data'] = array_values( $factor_data );
                    $data = array_merge( $default_value, $data );
                    $color = $this->random_color( ++$index );
                    $data['backgroundColor'] = $color;
                    $data['borderColor'] = $color;
                    $factor_label = $factor == 'normal' ? 'Neutral' : ucwords( $factor );
                    $data['labelString'] = 'Impressions';
                    $data['label'] =  $factor_label;
                    $this->results[ $factor ] = $data;
                    $this->results[ 'stepped_size' ] = $max_stepped_size;
                }
                $this->impressions = [];
            }

            return $this->results;
        }
        return array();
    }

    public function array_sum_assoc( $defaul, $new_array ) {        
        if( empty( $new_array ) || ! is_array( $new_array ) ) {
            return $new_array;
        }
        $new = [];
        foreach( $new_array as $key => $value ) {
            $new[ $key ] = $value + ( isset( $defaul[ $key ] ) ? $defaul[ $key ] : 0 );
        }

        return $new;
    }

    public function analytics_calc(){
        if ( empty( $_POST ) || ! check_admin_referer( '_betterdocs_analytics_nonce', 'nonce' ) ) {
            return;
        }
        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], '_betterdocs_analytics_nonce' ) ) {
            return;
        }

        $dates = $this->labels( $_POST['query_vars'] );
        $datasets = $this->datasets( $_POST['query_vars'] );

        echo json_encode( array(
            'labels'   => $dates,
            'datasets' => $datasets,
        ));

        wp_die();
    }

    private function random_color_part() {
        return str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
    }
    
    private function random_color( $index = '' ) {
        if( ! empty( $index ) ) {
            if( isset( $this->colors[ $index ] ) ) {
                return $this->colors[ $index ];
            } else {
                return '#' . $this->random_color_part() . $this->random_color_part() . $this->random_color_part();
            }
        }
    }

    protected function clicks(){

    }
    /**
     * Add Settings Options
     * @return void
     */
    public function add_settings(){
        add_filter( 'betterdocs_settings_tab', array( $this, 'add_settings_tab' ) );
    }

    public function add_settings_tab( $options ){
        $general = $options['general'];

        $general['sections']['analytics'] = array(
            'priority' => 20,
            'title'    => __('Analytics', 'betterdocs-pro'),
            'fields'   => array(
                'analytics_from' => array(
                    'type'    => 'select',
                    'label'   => __( 'Analytics From', 'betterdocs-pro' ),
                    'options' => array( 
                        'everyone'         => __( 'Everyone', 'betterdocs-pro' ),
                        'guests'           => __( 'Guests Only', 'betterdocs-pro' ),
                        'registered_users' => __( 'Registered Users Only', 'betterdocs-pro' ),
                    ),
                    'default'  => 'everyone',
                    'priority' => 0,
                ),
                'exclude_bot_analytics' => array(
                    'type'        => 'checkbox',
                    'label'       => __( 'Exclude Bot Analytics', 'betterdocs-pro' ),
                    'default'     => 1,
                    'priority'    => 1,
                    'help' => __( 'Select if you want to exclude bot analytics.', 'betterdocs-pro' ),
                ),
            ),
        );

        $options['general'] = $general;
        return $options;
    }


    public function add_nonce( $output, $settings ){
        $nonce = wp_create_nonce( '_betterdocs_pro_analytics_nonce' );
        $output .= '<input class="notificationx-pro-analytics" name="_betterdocs_pro_analytics_nonce" type="hidden" value="' . $nonce . '"/>';
        return $output;
    }

    public function analytics_data() {

        global $user_ID, $post, $post_type;

        $get_docs = get_posts( array( 'post_type' => 'docs', 'post_status' => 'publish') );
        
        if( $post_type != 'docs' || count( $get_docs ) == 0 ) {
            return;
        }
        
        if ( is_int( $post ) ) {
            $post = get_post( $post );
        }
        $post_id = $post->ID; // Get Post ID;
        $todays_date = date( 'd-m-Y', time() );

        if ( ! wp_is_post_revision( $post ) && ! is_preview() ) {
            if ( is_single() ) {
                $analytics_from =  BetterDocs_DB::get_settings( 'analytics_from' );
                $analytics_from = empty( $analytics_from ) ? 'everyone' : $analytics_from;
        
                $should_count = false;
                /**
                 * Inspired from WP-Postviews for 
                 * this pece of code. 
                 */
                switch( $analytics_from ) {
                    case 'everyone':
                        $should_count = true;
                        break;
                    case 'guests':
                        if( empty( $_COOKIE[ USER_COOKIE ] ) && (int) $user_ID === 0 ) {
                            $should_count = true;
                        }
                        break;
                    case 'registered_users':
                        if( (int) $user_ID > 0 ) {
                            $should_count = true;
                        }
                        break;
                }
        
                if( $should_count === false ) {
                    return;
                }

                // BOT check

                $exclude_bot_analytics =  BetterDocs_DB::get_settings( 'exclude_bot_analytics' );

                if ( $exclude_bot_analytics == 1 ) {
                    /**
                     * Inspired from WP-Postviews for 
                     * this piece of code. 
                     */
                    $bots = array(
                        'Google Bot' => 'google',
                        'MSN' => 'msnbot',
                        'Alex' => 'ia_archiver',
                        'Lycos' => 'lycos',
                        'Ask Jeeves' => 'jeeves',
                        'Altavista' => 'scooter',
                        'AllTheWeb' => 'fast-webcrawler',
                        'Inktomi' => 'slurp@inktomi',
                        'Turnitin.com' => 'turnitinbot',
                        'Technorati' => 'technorati',
                        'Yahoo' => 'yahoo',
                        'Findexa' => 'findexa',
                        'NextLinks' => 'findlinks',
                        'Gais' => 'gaisbo',
                        'WiseNut' => 'zyborg',
                        'WhoisSource' => 'surveybot',
                        'Bloglines' => 'bloglines',
                        'BlogSearch' => 'blogsearch',
                        'PubSub' => 'pubsub',
                        'Syndic8' => 'syndic8',
                        'RadioUserland' => 'userland',
                        'Gigabot' => 'gigabot',
                        'Become.com' => 'become.com',
                        'Baidu' => 'baiduspider',
                        'so.com' => '360spider',
                        'Sogou' => 'spider',
                        'soso.com' => 'sosospider',
                        'Yandex' => 'yandex'
                    );
                    $useragent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '';
                    foreach ( $bots as $name => $lookfor ) {
                        if ( ! empty( $useragent ) && ( false !== stripos( $useragent, $lookfor ) ) ) {
                            $should_count = false;
                            break;
                        }
                    }
                }
                
                if( $should_count === false ) {
                    return;
                }
                
                $views = get_post_meta( $post_id, '_betterdocs_meta_views', true );
                if( $views === null ) {
                    add_post_meta( $post_id, '_betterdocs_meta_views', 1 );
                } else {
                    update_post_meta( $post_id, '_betterdocs_meta_views', ++$views );
                }
                
                /**
                 * For Per Pop Up
                 */
                $impressions = get_post_meta( $post_id, '_betterdocs_meta_impression_per_day', true );
                if( empty( $impressions )  ) {
                    $impressions = [];
                    $impressions[ $todays_date ]['impressions'] = 1;
                    add_post_meta( $post_id, '_betterdocs_meta_impression_per_day', $impressions );
                } else {
                    if( isset( $impressions[ $todays_date ] ) ) {
                        $impressions_data = isset( $impressions[ $todays_date ]['impressions'] ) ? ++$impressions[ $todays_date ]['impressions'] : 1;
                        $impressions[ $todays_date ]['impressions'] = $impressions_data;
                    } else {
                        $impressions[ $todays_date ]['impressions'] = 1;
                    }
                    update_post_meta( $post_id, '_betterdocs_meta_impression_per_day', $impressions );
                }
                return true;
            } // is_single
        } // ! is not revision
    }

    public function clicked_analytics_data(){
        /**
         * Verify the Nonce
         */
        if ( ! isset( $_POST['nonce'] ) || ! isset( $_POST['id'] ) || ! wp_verify_nonce( $_POST['nonce'], 'betterdocs_pro_analytics_clicked' ) ) {
            return;
        }

        /**
         * Save Impressions
         */
        $post_id = intval( $_POST['id'] );
        /**
         * For Per Click Data
         */
        $todays_date = date( 'd-m-Y', time() );
        if( isset( $_POST['clicked'] ) && $_POST['clicked'] == 'true' ) {
            $clicks = get_post_meta( $post_id, '_nx_meta_clicks', true );
            if( $clicks === null ) {
                add_post_meta( $post_id, '_nx_meta_clicks', 1 );
            } else {
                update_post_meta( $post_id, '_nx_meta_clicks', ++$clicks );
            }
            /**
             * For Per Pop Up Click
             */
            $impressions = get_post_meta( $post_id, '_betterdocs_meta_impression_per_day', true );
            if( empty( $impressions ) ) {
                $impressions = [];
                $impressions[ $todays_date ][ 'clicks' ] = 1;
                add_post_meta( $post_id, '_betterdocs_meta_impression_per_day', $impressions );
            } else {
                if( isset( $impressions[ $todays_date ] ) ) {
                    $clicks_data = isset( $impressions[ $todays_date ]['clicks'] ) ? ++$impressions[ $todays_date ]['clicks'] : 1;
                    $impressions[ $todays_date ][ 'clicks' ] = $clicks_data;
                } else {
                    $impressions[ $todays_date ][ 'clicks' ] = 1;
                }
                update_post_meta( $post_id, '_betterdocs_meta_impression_per_day', $impressions );
            }
            wp_die(); // die here
        }

        $views = get_post_meta( $post_id, '_betterdocs_meta_views', true );
        if( $views === null ) {
            add_post_meta( $post_id, '_betterdocs_meta_views', 1 );
        } else {
            update_post_meta( $post_id, '_betterdocs_meta_views', ++$views );
        }

    }

    /**
     * Formating Number in a Nice way
     * @since 1.2.1
     * @param int|string $n
     * @return string
     */
    public static function nice_number( $n ) {
        $temp_number = str_replace(",", "", $n );
        if( ! empty( $temp_number ) ) {
            $n = ( 0 + $temp_number );
        } else {
            $n = $n;
        }
        if( ! is_numeric( $n ) ) return 0;
        $number = 0;
        $suffix = '';
        switch( true ) {
            case $n >= 1000000000000 : 
                $number = round( ( $n / 1000000000000 ), 1 );
                $suffix = $n > 1000000000000 ? 'T+' : 'T';
                break;
            case $n >= 1000000000 : 
                $number = round( ( $n / 1000000000 ), 1 );
                $suffix = $n > 1000000000 ? 'B+' : 'B';
                break;
            case $n >= 1000000 : 
                $number = round( ( $n / 1000000 ), 1 );
                $suffix = $n > 1000000 ? 'M+' : 'M';
                break;
            case $n >= 1000 : 
                $number = round( ( $n / 1000 ), 1 );
                $suffix = $n > 1000 ? 'K+' : 'K';
                break;
            default: 
                $number = $n;
                break;
        }
        $number = number_format($number);
        if( strpos( $number, '.') !== false && strpos( $number, '.') >= 0 ) {
            $number = number_format($number, 1 );
        }
        return $number . $suffix;
    }
}

BetterDocsPro_Analytics::get_instance();