<?php
namespace WeDevs\PM_Pro\Label\Helper;


use WP_REST_Request;
// data: {
//  with: '',
//  per_page: '10',
//  select: 'id, title',
//  id: [1,2],
//  title: 'Rocket', 'test'
//  page: 1,
//  orderby: [title=>'asc', 'id'=>desc]
//  label_meta: 'total_task_labels,total_tasks,total_complete_tasks,total_incomplete_tasks,total_labels,total_labels,total_comments,total_files,total_labels'
// },

class Label {
    private static $_instance;
    private $query_params;
    private $select;
    private $join;
    private $where;
    private $limit;
    private $orderby;
    private $with = [];
    private $labels;
    private $label_ids;
    private $is_single_query = false;

    public static function getInstance() {
        return new self();
    }

    function __construct() {
        $this->set_table_name();
    }

    public static function get_labels( WP_REST_Request $request ) {
        $labels = self::get_results( $request->get_params() );

        wp_send_json( $labels );
    }

    public static function get_results( $params = [] ) {
        $self = self::getInstance();
        $self->query_params = $params;

        $self->join()
            ->where()
            ->limit()
            ->orderby()
            ->get()
            ->with()
            ->meta();

        $response = $self->format_labels( $self->labels );

        if ( pm_is_single_query( $params ) ) {
            return ['data' => $response['data'][0]] ;
        }

        return $response;
    }

    /**
     * Format Tasklabel data
     *
     * @param array $labels
     *
     * @return array
     */
    public function format_labels( $labels ) {
        $response = [
            'data' => [],
            'meta' => []
        ];

        foreach ( $labels as $key => $label ) {
            $labels[$key] = $this->fromat_label( $label );
        }

        $response['data'] = $labels;
        $response['meta'] = $this->set_labels_meta();

        return $response;
    }

    /**
     * Set meta data
     */
    private function set_labels_meta() {
        return [
            'pagination' => [
                'total'   => $this->found_rows,
                'per_page'  => ceil( $this->found_rows/$this->get_per_page() )
            ]
        ];
    }

    public function fromat_label( $label ) {

        $items = [
            'id'          => (int) $label->id,
            'title'       => $label->title,
            'description' => $label->description,
            'color'       => $label->color,
            'status'      => (int) $label->status,
            'project_id'  => (int) $label->project_id,
            'task_id'     => (int) $label->task_id,
        ];

        $items = $this->item_with( $items, $label );

        return apply_filters( 'pm_label_transform', $items, $label );
    }

    private function item_with( $items, $label ) {
        $with = empty( $this->query_params['with'] ) ? [] : $this->query_params['with'];

        if ( ! is_array( $with ) ) {
            $with = explode( ',', str_replace(' ', '', $with ) );
        }

        $with = array_merge( $this->with, $with );

        $label_with_items =  array_intersect_key( (array) $label, array_flip( $with ) );

        $items = array_merge( $items, $label_with_items );

        return $items;
    }

    private function with() {


        $this->labels = apply_filters( 'pm_label_with',$this->labels, $this->label_ids, $this->query_params );

        return $this;
    }


    private function updater() {

        if ( empty( $this->labels ) ) {
            return $this;
        }

        $updater_ids = wp_list_pluck( $this->labels, 'updated_by' );
        $updater_ids = array_unique( $updater_ids );

        $updaters = pm_get_users( [ 'id' => $updater_ids ] );
        $updaters = $updaters['data'];

        $items = [];

        foreach ( $updaters as $key => $updater ) {
            $items[$updater['id']] = $updater;
        }

        foreach ( $this->labels as $key => $label ) {
            $c_updater = empty( $items[$label->updated_by] ) ? [] : $items[$label->updated_by];

            $label->updater = [ 'data' => $c_updater ];
        }

        return $this;
    }

    private function meta() {
        return $this;
    }

    private function join() {
        $this->join_task_label_task();

        return $this;
    }

    private function join_task_label_task() {
        global $wpdb;

        $this->join .= " LEFT JOIN {$this->tb_task_label_task} as tlt ON tlt.label_id={$this->tb_task_label}.id";
    }

    private function where() {

        $this->where_id()
            ->where_project_id()
            ->where_task_id();

        return $this;
    }

    /**
     * Filter label by ID
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
            $this->where .= $wpdb->prepare( " AND {$this->tb_task_label}.id IN ($query_format)", $id );
        }

        if ( !is_array( $id ) ) {
            $this->where .= $wpdb->prepare( " AND {$this->tb_task_label}.id IN (%d)", $id );
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
            $this->where .= $wpdb->prepare( " AND tlt.task_id IN ($query_format)", $task_id );
        }

        if ( !is_array( $task_id ) ) {
            $this->where .= $wpdb->prepare( " AND tlt.task_id IN (%d)", $task_id );
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
            $this->where .= $wpdb->prepare( " AND {$this->tb_task_label}.project_id IN ($query_format)", $project_id );
        }

        if ( !is_array( $project_id ) ) {
            $this->where .= $wpdb->prepare( " AND {$this->tb_task_label}.project_id IN (%d)", $project_id );
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

        return 20;
    }

    private function get() {
        global $wpdb;
        $id = isset( $this->query_params['id'] ) ? $this->query_params['id'] : false;

        $query = "SELECT SQL_CALC_FOUND_ROWS DISTINCT {$this->tb_task_label}.*, tlt.task_id
            FROM {$this->tb_task_label}
            {$this->join}
            WHERE %d=%d {$this->where}
            {$this->orderby} {$this->limit} ";

        $results = $wpdb->get_results( $wpdb->prepare( $query, 1, 1 ) );

        $this->found_rows = $wpdb->get_var( "SELECT FOUND_ROWS()" );
        $this->labels = $results;

        if ( ! empty( $results ) && is_array( $results ) ) {
            $this->label_ids = wp_list_pluck( $results, 'id' );
        }

        if ( ! empty( $results ) && !is_array( $results ) ) {
            $this->label_ids = [$results->id];
        }

        return $this;
    }

    private function set_table_name() {
        $this->tb_task_label_task  = pm_tb_prefix() . 'pm_task_label_task';
        $this->tb_task_label = pm_tb_prefix() . 'pm_task_label';
    }
}
