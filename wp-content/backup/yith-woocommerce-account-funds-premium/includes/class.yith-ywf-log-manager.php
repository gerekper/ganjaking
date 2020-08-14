<?php
if (! defined ( 'ABSPATH' ))
	exit ();

if (! class_exists ( 'YITH_YWF_Log_Manager' ) ) {
	
	class YITH_YWF_Log_Manager{
		
		protected static $_instance;
		
		public function __construct(){
			
			global $wpdb;
			
			$this->table_name = $wpdb->prefix.'ywf_user_fund_log';
		

			add_action('ywf_add_user_log', array( $this,'add_log'));
		}
		
		/**
		 * @author YITHEMES
		 * @since 1.0.0
		 * @return YITH_YWF_Log_Manager unique access
		 */
		public static function get_instance(){
		
			if( is_null( self::$_instance ) ){
		
				self::$_instance = new self();
			}
		
			return self::$_instance;
		}
		
		public function install(){
			
			$db_version = get_option('ywf_dbversion','0');
			
			if( version_compare($db_version, '1.0.0','<' ) ){

				$this->create_table();

				update_option('ywf_dbversion', '1.0.0' );
			}
		}
		
		/**
		 * check if table exist
		 * @author YITHEMES
		 * @since 1.0.0
		 * @return bool
		 */
		public function table_exist(){
			
			global $wpdb;
			$number_of_tables = $wpdb->query("SHOW TABLES LIKE {$this->table_name}" );
			
			return (bool) ( $number_of_tables == 1 );
		}
		
		/**
		 * create table
		 * @author YITHEMES
		 * @since 1.0.0
		 * 
		 */
		public function create_table(){
			
			global $wpdb;

            $wpdb->hide_errors();
			$sql = "CREATE TABLE IF NOT EXISTS `{$this->table_name}` ( 
								  `ID` int(11) NOT NULL AUTO_INCREMENT,
                                  `order_id` int(11) ,
                                  `user_id` int (11) NOT NULL,
                                  `fund_user` varchar(255) ,   
                                  `type_operation` varchar(255),
                                  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                  `description` VARCHAR(255) NOT NULL DEFAULT '',
                                  PRIMARY KEY (`ID`) ) DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";

                if (! function_exists( 'dbDelta' ) )
                        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

                dbDelta($sql);
		}
		
		/**
		 * get user log
		 * @author YITHEMES
		 * @since 1.0.0
		 * @param array $args
		 */
		public function get_log( $args = array() ){
			
			$default = array(
					'user_id' => '',
					'offset' => '',
					'limit' => '',
					'type_operation' => ''
			);
			
			$default = wp_parse_args($args, $default );
			
			global $wpdb;
			
			$query = "SELECT * FROM {$this->table_name}";
			
			if( isset( $default['user_id'] ) && $default['user_id']!== ''  ){
				$query.=' '.$this->user_query($default['user_id']);
			}

			if( isset( $default['type_operation'] ) && $default['type_operation']!=='' ){

				$query.=' '.$this->type_operation_query( $default['type_operation'] );
			}

			$query.= " ORDER BY {$this->table_name}.date_added DESC";

			if( ( isset( $default['offset'] ) && $default['offset']!=='' ) && ( isset( $default['limit'] ) && $default['limit']!=='' ) ){
				$query.=' '.$this->paged_query($default['offset'], $default['limit']);
			}

			$results = $wpdb->get_results( $query );
			return $results;
				
		}

		public function count_log( $args =array() ){

			$default = array(
				'user_id' => get_current_user_id()
			);

			$default = wp_parse_args( $args, $default );

			global $wpdb;

			$query = $wpdb->prepare( "SELECT COUNT(*) FROM {$this->table_name} WHERE {$this->table_name}.user_id = %s ", $default['user_id'] );

			return $wpdb->get_var( $query );
		}

        public function count_all_log(){
            
            global $wpdb;

            $query = "SELECT COUNT(*) FROM {$this->table_name}";

            return $wpdb->get_var( $query );
        }

		public function get_user_fund_total( $args = array() ){

			$default = array(
					'user_id' => get_current_user_id()
			);

			$default = wp_parse_args( $args, $default );

			global $wpdb;

			$query = $wpdb->prepare( "SELECT SUM( {$this->table_name}.fund_user ) FROM {$this->table_name} WHERE {$this->table_name}.user_id = %s ", $default['user_id'] );

			return $wpdb->get_var( $query );
		}
		
		/**
		 * return where clause to query
		 * @author YITHEMES
		 * @since 1.0.0
		 * @param string $user_id
		 * @return string
		 */
		public function user_query( $user_id ){
			global $wpdb;
			$query = $wpdb->prepare( "WHERE {$this->table_name}.user_id = %s",$user_id );
			
			return $query;
		}

		public function type_operation_query( $type_op ){

			global $wpdb;
			$query = $wpdb->prepare( "AND {$this->table_name}.type_operation = %s", $type_op  );

			return $query;
		}
		
		/**
		 * add limit to query
		 * @author YITHEMES
		 * @since 1.0.0
		 * @param string $offset
		 * @param string $items_per_page
		 * @return string
		 */
		public function paged_query( $offset, $limit ){
			
			$query = "LIMIT {$offset},{$limit}";
			
			return $query;
		}
		
		/**
		 * add new user log
		 * @author YITHEMES
		 * @since 1.0.0
		 * @param array $args
		 */
		public function add_log( $args = array() ){
			
			global $wpdb;
			
			$default = array(
				'user_id' => '',
				'order_id' => '',
				'editor_id' => 0,
				'fund_user'	=> '',
				'type_operation' => ''	,
                'description' => ''
			); 
			
			$default = wp_parse_args($args, $default);
			$negative_type_operation = apply_filters( 'ywf_negative_type_operation', array( 'pay', 'remove') );
			$default['fund_user'] =  in_array( $default['type_operation'], $negative_type_operation ) ? -$default['fund_user'] :  $default['fund_user'] ;
			
			$result = $wpdb->insert( $this->table_name, $default );

		}
		
	}
}
/**
 * return YITH_YWF_Log_Manager
 */
function YWF_Log(){
	
	return YITH_YWF_Log_Manager::get_instance();
}

