<?php

/**
 * Package Attrog
 * Author Ir-Tech
 * @since 1.0.0
 * */

if (!defined('ABSPATH')){
	exit(); //exit if access directly
}

if (!class_exists('appside_Post_Column_Customize')){
	class appside_Post_Column_Customize{
		//$instance variable
		private static $instance;
		
		public function __construct() {
			//service admin add table value hook
			add_filter("manage_edit-portfolio_columns", array($this, "edit_portfolio_columns") );
			add_action('manage_portfolio_posts_custom_column', array($this, 'add_thumbnail_columns'), 10,2);

		}
		/**
		 * get Instance
		 * @since 1.0.0
		 * */
		public static function getInstance(){
			if (null == self::$instance){
				self::$instance = new self();
			}
			return self::$instance;
		}


		/**
		 * edit case study
		 * @since 1.0.0
		 * */
		public function edit_portfolio_columns($columns){

			$order = ( 'asc' == $_GET['order'] ) ? 'desc' : 'asc';
			$cat_title = $columns['taxonomy-portfolio-cat'];
			unset($columns);
			$columns['cb'] = '<input type="checkbox" />';
			$columns['title'] = esc_html__('Title','appside-master');
			$columns['thumbnail'] = '<a href="edit.php?post_type=portfolio&orderby=title&order='.urlencode($order).'">'.esc_html__('Thumbnail','appside-master').'</a>';
			$columns['taxonomy-portfolio-cat'] = '<a href="edit.php?post_type=portfolio&orderby=taxonomy&order='.urlencode($order).'">'.$cat_title.'<span class="sorting-indicator"></span></a>';
			$columns['date'] = esc_html__('Date','appside-master');
			return $columns;
		}

		/**
		 * add thumbnail
		 * @since 1.0.0
		 * */
		public function add_thumbnail_columns($column,$post_id) {
			switch ( $column ) {
				case 'thumbnail' :
					echo '<a class="row-thumbnail" href="' . esc_url( admin_url( 'post.php?post=' . $post_id . '&amp;action=edit' ) ) . '">' . get_the_post_thumbnail( $post_id, 'thumbnail' ) . '</a>';
					break;
				default:
					break;
			}
		}

	}//end class
	if ( class_exists('appside_Post_Column_Customize')){
		appside_Post_Column_Customize::getInstance();
	}
}