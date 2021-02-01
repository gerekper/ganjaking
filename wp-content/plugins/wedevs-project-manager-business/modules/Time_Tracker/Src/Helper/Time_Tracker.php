<?php
namespace WeDevs\PM_Pro\Modules\Time_Tracker\Src\Helper;


use WP_REST_Request;
// data: {
//  with: '',
//  per_page: '10',
//  select: 'id, title',
//  id: [1,2],
//  title: 'Rocket', 'test'
//  page: 1,
//  orderby: [title=>'asc', 'id'=>desc]
//  time_tracker_meta: 'total_task_times,total_tasks,total_complete_tasks,total_incomplete_tasks,total_times,total_times,total_comments,total_files,total_times'
// },

class Time_Tracker {
    private static $_instance;
    private $query_params;
    private $select;
    private $join;
    private $where;
    private $limit;
    private $orderby;
    private $with = ['user'];
    private $times;
    private $time_tracker_ids;
    private $is_single_query = false;

    public static function getInstance() {
        return new self();
    }

    function __construct() {
        $this->set_table_name();
    }

    public static function get_times( WP_REST_Request $request ) {
        $times = self::get_results( $request->get_params() );

        wp_send_json( $times );
    }

    public static function get_results( $params = [] ) {
        $self = self::getInstance();
        $self->query_params = $params;

        $self->join()
            ->where()
            ->limit()
            ->orderby()
            ->get()
            ->with();
            //->meta();

        $response = $self->format_times( $self->times );

        // if ( pm_is_single_query( $params ) ) {
        //     return ['data' => $response['data'][0]] ;
        // }

        return $response;
    }

    /**
     * Format Tasktime_tracker data
     *
     * @param array $times
     *
     * @return array
     */
    public function format_times( $times ) {
        $response = [
            'data' => [],
            'meta' => []
        ];

        $current_user_id   = get_current_user_id();
        $all_data          = [];
        $current_user_data = [];

        foreach ( $times as $key => $time_tracker ) {
            if ( $time_tracker->total == '0' ) {
                //continue;
            }

            if ( $current_user_id == $time_tracker->user_id ) {
                $time                = $this->fromat_time_tracker( $time_tracker );
                $current_user_data[] = $time;
                $all_data[]          = $time;
            } else {
               $all_data[] = $this->fromat_time_tracker( $time_tracker );
            }
        }

        $response['data']      = $current_user_data;
        $response['all_data']  = $all_data;
        $response['users_data'] = $this->get_user_data( $all_data );
        $response['meta']      = $this->set_times_meta();

        return $response;
    }

    private function get_user_data( $times ) {
        $users = [];

        foreach ( $times as $key => $time ) {
            $users[$time['task_id']][$time['user_id']]['data'][] = $time;
        }

        foreach ( $users as $task_id => $user ) {
            foreach ( $user as $user_id => $times ) {

                $run_status  = wp_list_pluck( $times['data'], 'run_status' );
                $db_time     = wp_list_pluck( $times['data'], 'total' );
                $db_time     = wp_list_pluck( $db_time, 'total_second' );

                $total_time  = array_sum( $db_time );
                $time_fromat = pm_pro_second_to_time( $total_time );

                $users[$task_id][$user_id]['meta']['totalTime'] = $time_fromat;

                if ( in_array( 1, $run_status ) ) {
                    $users[$task_id][$user_id]['meta']['running'] = true;
                } else {
                    $users[$task_id][$user_id]['meta']['running'] = false;
                }

                $users[$task_id][$user_id]['meta']['user'] = $times['data'][0]['user'];
            }
        }

        return $users;
    }

    /**
     * Set meta data
     */
    private function set_times_meta() {

        //$this->times['meta']['totalTaskTime'] = $format_time;
        return [
            'pagination' => [
                'total'   => $this->found_rows,
                'per_page'  => ceil( $this->found_rows/$this->get_per_page() )
            ],
            'tasks_total_time' => $this->total_time(),
            'running'          =>  $this->get_running(), //for support previous code
            'totalTaskTime'    => $this->get_total_task_time(), //for support previous code
            'totalTime'        => $this->get_total_time() ////for support previous code
        ];
    }

    //is current user time running
    public function get_running() {
        $current_user_id = get_current_user_id();
        $times = [];

        foreach ( $this->times as $key => $time) {
            if ( $time->user_id == $current_user_id ) {
                $times[] = $time;
            }
        }

        $run_status = wp_list_pluck( $times, 'run_status' );

        if ( in_array( 1, $run_status ) ) {
            return true;
        }

        return false;
    }

    // all users total task time
    private function get_total_task_time() {
        $tasks = [];
        $total = [];

        foreach ( $this->times as $key => $time ) {
            $tasks[$time->task_id][] = $time;
        }

        foreach ( $tasks as $task_id => $task ) {
            $times       = wp_list_pluck( $task, 'total' );
            $times       = array_sum( $times );
            $format_time = pm_pro_second_to_time( $times );
            $total[$task_id]['total_time'] = $format_time;
        }

        return $total;
    }

    //current user total task time
    private function get_total_time() {
        $current_user_id = get_current_user_id();
        $times = [];

        foreach ( $this->times as $key => $time ) {
            if ( $time->user_id == $current_user_id ) {
                if ( $time->stop == '0' ) {
                    $time->total = strtotime( current_time( 'mysql' ) ) - $time->start;
                }

                $times[] = $time;
            }
        }

        $total = wp_list_pluck( $times, 'total' );
        $total = array_sum( $total );

        return pm_pro_second_to_time( $total );
    }

    public function fromat_time_tracker( $time_tracker ) {

        $items = [
            'id'         => $time_tracker->id,
            'user_id'    => $time_tracker->user_id,
            'project_id' => $time_tracker->project_id,
            'list_id'    => $time_tracker->list_id,
            'task_id'    => $time_tracker->task_id,
            'start'      => format_date( make_carbon_date( date( 'Y-m-d H:i:s', $time_tracker->start ) ) ),
            'stop'       => format_date( make_carbon_date( date( 'Y-m-d H:i:s', $time_tracker->stop ) ) ),
            'total'      => pm_pro_second_to_time( $time_tracker->total ),
            'run_status' => $time_tracker->run_status,
            'created_by' => $time_tracker->created_by,
            'updated_by' => $time_tracker->updated_by,
            'updated_at' => $time_tracker->updated_at,
            'created_at' => format_date( $time_tracker->created_at ),
        ];

        $items = $this->item_with( $items, $time_tracker );

        return apply_filters( 'pm_time_tracker_transform', $items, $time_tracker );
    }

    private function item_with( $items, $time_tracker ) {
        $with = empty( $this->query_params['with'] ) ? [] : $this->query_params['with'];

        if ( ! is_array( $with ) ) {
            $with = explode( ',', str_replace(' ', '', $with ) );
        }

        $with = array_merge( $this->with, $with );

        $time_tracker_with_items =  array_intersect_key( (array) $time_tracker, array_flip( $with ) );

        $items = array_merge( $items, $time_tracker_with_items );

        return $items;
    }


    // private function meta() {

    //     $this->total_time();

    //     return $this;
    // }

    private function total_time() {
        $tasks = [];
        $total = [];

        foreach ( $this->times as $key => $time ) {
            $tasks[$time->task_id][] = $time;
        }

        foreach ( $tasks as $task_id => $task ) {
            $times       = wp_list_pluck( $task, 'total' );
            $times       = array_sum( $times );
            $format_time = pm_pro_second_to_time( $times );
            $total[$task_id]['total_time'] = $format_time;
        }

        return $total;
    }


    private function with() {

        $this->include_user();

        $this->times = apply_filters( 'pm_time_tracker_with',$this->times, $this->time_tracker_ids, $this->query_params );

        return $this;
    }

    private function include_user() {

        if ( empty( $this->times ) ) {
            return $this;
        }

        $user_ids = wp_list_pluck( $this->times, 'user_id' );
        $user_ids = array_unique( $user_ids );

        $users = pm_get_users( [ 'id' => $user_ids ] );
        $items = [];

        foreach ( $users['data'] as $key => $user ) {
            $items[$user['id']] = $user;
        }

        foreach ( $this->times as $key => $time ) {
            $c_user = empty( $items[$time->user_id] ) ? [] : $items[$time->user_id];
            $time->user = [ 'data' => $c_user ];
        }

        return $this;
    }

    private function creator() {

        if ( empty( $this->times ) ) {
            return $this;
        }

        $creator_ids = wp_list_pluck( $this->times, 'created_by' );
        $creator_ids = array_unique( $creator_ids );

        $creators = pm_get_users( [ 'id' => $creator_ids ] );

        $creators = $creators['data'];

        $items = [];

        foreach ( $creators as $key => $creator ) {
            $items[$creator['id']] = $creator;
        }

        foreach ( $this->times as $key => $time ) {
            $c_creator = empty( $items[$time->created_by] ) ? [] : $items[$time->created_by];

            $time->creator = [ 'data' => $c_creator ];
        }

        return $this;
    }

    private function updater() {

        if ( empty( $this->times ) ) {
            return $this;
        }

        $updater_ids = wp_list_pluck( $this->times, 'updated_by' );
        $updater_ids = array_unique( $updater_ids );

        $updaters = pm_get_users( [ 'id' => $updater_ids ] );
        $updaters = $updaters['data'];

        $items = [];

        foreach ( $updaters as $key => $updater ) {
            $items[$updater['id']] = $updater;
        }

        foreach ( $this->times as $key => $time_tracker ) {
            $c_updater = empty( $items[$time_tracker->updated_by] ) ? [] : $items[$time_tracker->updated_by];

            $time_tracker->updater = [ 'data' => $c_updater ];
        }

        return $this;
    }



    private function join() {
        return $this;
    }

    private function where() {

        $this->where_id()
            ->where_project_id()
            ->where_task_id()
            ->where_list_id()
            ->where_user_id();

        return $this;
    }

    /**
     * Filter time_tracker by ID
     *
     * @return class object
     */
    private function where_id() {
        global $wpdb;
        $id = isset( $this->query_params['id'] ) ? $this->query_params['id'] : false;

        if ( empty( $id ) ) {
            return $this;
        }

        $id = pm_get_prepare_data( $id );

        if ( is_array( $id ) ) {
            $query_format = pm_get_prepare_format( $id );
            $this->where .= $wpdb->prepare( " AND {$this->tb_time_tracker}.id IN ($query_format)", $id );
        }

        if ( !is_array( $id ) ) {
            $this->where .= $wpdb->prepare( " AND {$this->tb_time_tracker}.id IN (%d)", $id );
        }

        return $this;
    }

    /**
     * Filter task by title
     *
     * @return class object
     */
    private function where_user_id() {
        global $wpdb;
        $user_id = isset( $this->query_params['user_id'] ) ? $this->query_params['user_id'] : false;

        if ( empty( $user_id ) ) {
            return $this;
        }

        $user_id = pm_get_prepare_data( $user_id );

        if ( is_array( $user_id ) ) {
            $query_format = pm_get_prepare_format( $user_id );
            $this->where .= $wpdb->prepare( " AND {$this->tb_time_tracker}.user_id IN ($query_format)", $user_id );
        }

        if ( !is_array( $user_id ) ) {
            $this->where .= $wpdb->prepare( " AND {$this->tb_time_tracker}.user_id IN (%d)", $user_id );
        }

        return $this;
    }

    /**
     * Filter task by title
     *
     * @return class object
     */
    private function where_list_id() {
        global $wpdb;
        $list_id = isset( $this->query_params['list_id'] ) ? $this->query_params['list_id'] : false;

        if ( empty( $list_id ) ) {
            return $this;
        }

        $list_id = pm_get_prepare_data( $list_id );

        if ( is_array( $list_id ) ) {
            $query_format = pm_get_prepare_format( $list_id );
            $this->where .= $wpdb->prepare( " AND {$this->tb_time_tracker}.list_id IN ($query_format)", $list_id );
        }

        if ( !is_array( $list_id ) ) {
            $this->where .= $wpdb->prepare( " AND {$this->tb_time_tracker}.list_id IN (%d)", $list_id );
        }

        return $this;
    }

    /**
     * Filter task by title
     *
     * @return class object
     */
    private function where_task_id() {
        global $wpdb;
        $task_id = isset( $this->query_params['task_id'] ) ? $this->query_params['task_id'] : false;

        if ( empty( $task_id ) ) {
            return $this;
        }

        $task_id = pm_get_prepare_data( $task_id );

        if ( is_array( $task_id ) ) {
            $query_format = pm_get_prepare_format( $task_id );
            $this->where .= $wpdb->prepare( " AND {$this->tb_time_tracker}.task_id IN ($query_format)", $task_id );
        }

        if ( !is_array( $task_id ) ) {
            $this->where .= $wpdb->prepare( " AND {$this->tb_time_tracker}.task_id IN (%d)", $task_id );
        }

        return $this;
    }

    private function where_project_id() {
        global $wpdb;
        $project_id = isset( $this->query_params['project_id'] ) ? $this->query_params['project_id'] : false;

        if ( empty( $project_id ) ) {
            return $this;
        }

        $project_id = pm_get_prepare_data( $project_id );

        if ( is_array( $project_id ) ) {
            $query_format = pm_get_prepare_format( $project_id );
            $this->where .= $wpdb->prepare( " AND {$this->tb_time_tracker}.project_id IN ($query_format)", $project_id );
        }

        if ( !is_array( $project_id ) ) {
            $this->where .= $wpdb->prepare( " AND {$this->tb_time_tracker}.project_id IN (%d)", $project_id );
        }

        return $this;
    }

    private function limit() {
        global $wpdb;
        $per_page = isset( $this->query_params['per_page'] ) ? $this->query_params['per_page'] : false;

        if ( $per_page === false || $per_page == '-1' ) {
            return $this;
        }

        // $this->limit = " LIMIT {$this->get_offset()},{$this->get_per_page()}";
        $this->limit = $wpdb->prepare( " LIMIT %d,%d", $this->get_offset(), $this->get_per_page() );

        return $this;
    }

    private function orderby() {
        global $wpdb;

        $tb_pj    = $wpdb->prefix . 'pm_boards';
        $odr_prms = isset( $this->query_params['orderby'] ) ? $this->query_params['orderby'] : false;

        if ( $odr_prms === false && !is_array( $odr_prms ) ) {
            return $this;
        }

        $orders = [];

        $odr_prms = str_replace( ' ', '', $odr_prms );
        $odr_prms = explode( ',', $odr_prms );

        foreach ( $odr_prms as $key => $orderStr ) {
            $orderStr         = str_replace( ' ', '', $orderStr );
            $orderStr         = explode( ':', $orderStr );
            $orderby          = $orderStr[0];
            $order            = empty( $orderStr[1] ) ? 'asc' : $orderStr[1];
            $orders[$orderby] = $order;
        }

        $order = [];

        foreach ( $orders as $key => $value ) {
            $order[] =  $tb_pj .'.'. $key . ' ' . $value;
        }

        $this->orderby = "ORDER BY " . implode( ', ', $order);

        return $this;
    }

    private function get_offset() {
        $page = isset( $this->query_params['page'] ) ? $this->query_params['page'] : false;

        $page   = empty( $page ) ? 1 : absint( $page );
        $limit  = $this->get_per_page();
        $offset = ( $page - 1 ) * $limit;

        return $offset;
    }

    private function get_per_page() {

        $per_page = isset( $this->query_params['per_page'] ) ? $this->query_params['per_page'] : false;

        if ( ! empty( $per_page ) && intval( $per_page ) ) {
            return intval( $per_page );
        }

        return 20000;
    }

    private function get() {
        global $wpdb;
        $id = isset( $this->query_params['id'] ) ? $this->query_params['id'] : false;

        $query = "SELECT SQL_CALC_FOUND_ROWS DISTINCT {$this->tb_time_tracker}.*
            FROM {$this->tb_time_tracker}
            {$this->join}
            WHERE %d=%d {$this->where}
            {$this->orderby} {$this->limit} ";

        $results = $wpdb->get_results( $wpdb->prepare( $query, 1, 1 ) );

        $this->found_rows = $wpdb->get_var( "SELECT FOUND_ROWS()" );
        $this->times = $results;

        if ( ! empty( $results ) && is_array( $results ) ) {
            $this->time_tracker_ids = wp_list_pluck( $results, 'id' );
        }

        if ( ! empty( $results ) && !is_array( $results ) ) {
            $this->time_tracker_ids = [$results->id];
        }

        return $this;
    }

    private function set_table_name() {
        $this->tb_project  = pm_tb_prefix() . 'pm_projects';
        $this->tb_time_tracker = pm_tb_prefix() . 'pm_time_tracker';
    }
}
