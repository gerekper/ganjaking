<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct access forbidden.' );
}

if ( ! class_exists( 'YWQA_Question' ) ) {
	/**
	 *
	 * @class      class.ywqa-question.php
	 * @package    Yithemes
	 * @since      Version 1.0.0
	 * @author     Your Inspiration Themes
	 *
	 */
	class YWQA_Question extends YWQA_Discussion {
		
		/**
		 * Initialize a question object
		 *
		 * @param int|array $args the question id or an array for initializing the object
		 */
		public function __construct( $args = null ) {
			parent::__construct( $args );
			
			$this->type = "question";
		}
		
		public function get_answers_count() {
			global $wpdb;
			
			$query = $wpdb->prepare( "select count(ID)
				from {$wpdb->prefix}posts
				where post_status = 'publish' and post_type = %s and post_parent = %s",
				YWQA_CUSTOM_POST_TYPE_NAME,
				$this->ID
			);
			
			$items = $wpdb->get_row( $query, ARRAY_N );
			
			return $items[0];
		}
		
		
		/**
		 * Get answers for the current question
		 *
		 * @param int    $count how much answer to return
		 * @param string $order change order of visualization ("recent", "oldest", "useful")
		 *
		 * @return array
		 */
		public function get_answers( $count = - 1, $page = 1, $order = "recent", $published_only = true ) {
			global $wpdb;
			
			$query_limit = '';
			if ( $count > 0 ) {
				$query_limit = sprintf( " limit %d,%d ", ( $page - 1 ) * $count, $count );
			}
			
			$query_from  = "from {$wpdb->prefix}posts as p";
			$query_where = '';
			
			if ( "useful" == $order ) {
				$query_from .= " left join {$wpdb->prefix}postmeta as m2 on p.ID = m2.post_id";
				$query_where .= ' and m2.meta_key = "' . YWQA_METAKEY_DISCUSSION_UPVOTES . '"';
			}
			
			$query_order = 'order by post_date DESC';
			if ( "oldest" == $order ) {
				$query_order = 'order by post_date ASC';
			} else if ( "useful" == $order ) {
				$query_order = 'order by m2.meta_value DESC, post_date DESC';
			}
			
			$query_post_status = ' and (post_status <> "trash")';
			if ( $published_only ) {
				$query_post_status = " and (post_status = 'publish') ";
			}
			
			$query = $wpdb->prepare( "select ID, post_date, post_content, post_title, post_parent, post_status " .
			                         $query_from .
			                         " where (post_type = %s) and (post_parent = %d) " . $query_post_status .
			                         $query_where . " " .
			                         $query_order . " " .
			                         $query_limit,
				YWQA_CUSTOM_POST_TYPE_NAME,
				$this->ID
			);
			
			$items = $wpdb->get_results( $query, ARRAY_A );
			
			$answers = array();
			
			foreach ( $items as $item ) {
				
				$item_id = $item["ID"];
				
				$params = array(
					"content"                 => $item["post_content"],
					"discussion_author_id"    => get_post_meta( $item_id, YWQA_METAKEY_DISCUSSION_AUTHOR_ID, true ),
					"discussion_author_name"  => get_post_meta( $item_id, YWQA_METAKEY_DISCUSSION_AUTHOR_NAME, true ),
					"discussion_author_email" => get_post_meta( $item_id, YWQA_METAKEY_DISCUSSION_AUTHOR_EMAIL, true ),
					"product_id"              => get_post_meta( $item_id, YWQA_METAKEY_PRODUCT_ID, true ),
					"ID"                      => $item_id,
					"parent_id"               => $item["post_parent"],
					"date"                    => $item["post_date"],
					"status"                  => $item["post_status"],
				);
				
				$answers[] = new YWQA_Answer( $params );
			}
			
			return $answers;
		}
	}
}