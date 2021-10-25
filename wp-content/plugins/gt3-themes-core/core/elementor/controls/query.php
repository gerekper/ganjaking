<?php

namespace Elementor;

if(!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

if(!class_exists('\Elementor\GT3_Core_Elementor_Control_Query')) {

	class GT3_Core_Elementor_Control_Query extends Control_Base_Multiple {
		public static $type = 'gt3-elementor-core-query';

		public function get_type(){
			return 'gt3-elementor-core-query';
		}

		public static function type(){
			return 'gt3-elementor-core-query';
		}

		public function __construct(){
			parent::__construct();

		}


		public function get_default_value(){
			return [
				'posts_per_page'      => 12,
				'orderby'             => '',
				'order'               => '',
				'taxonomy'            => array(),
				'tags'                => array(),
				'author__in'          => array(),
				'post__in'            => array(),
				'ignore_sticky_posts' => 0,
			];
		}

		protected function get_default_settings(){
			return [
				'label_block' => true,
				'placeholder' => '',
			];
		}

		function getSlugById($taxonomy, $ids){
			$slugs = array();

			$terms = get_terms(array(
				'taxonomy' => $taxonomy,
				'include'  => $ids,
			));
			if(!is_wp_error($terms)) {
				if(is_array($terms) && count($terms)) {
					foreach($terms as $term) {
						$slugs[] = $term->slug;
					}
				}
			}

			return $slugs;
		}

		function isIds($ids){
			if(is_array($ids) && count($ids)) {
				foreach($ids as $id) {
					if(!is_numeric($id)) {
						return false;
					}
				}

				return true;
			}

			return false;
		}

		public function get_value($control, $settings){
			$value = parent::get_value($control, $settings);

			$value_args = array(
				'post_status' => array( 'publish' ),
				'post_type'   => $control['settings']['post_type'],
			);
			if(!empty($value['ignore_sticky_posts']) && (bool) $value['ignore_sticky_posts']) {
				$value_args['ignore_sticky_posts'] = '1';
			}
			if(!empty($value['posts_per_page'])) {
				$value_args['posts_per_page'] = $value['posts_per_page'];
			}
			if(!empty($value['orderby'])) {
				$value_args['orderby'] = $value['orderby'];
			}
			if(!empty($value['order'])) {
				$value_args['order'] = $value['order'];
			}
			if(!empty($value['post__in'])) {
				$value_args['post__in'] = $value['post__in'];
			} else {
				if(!empty($value['author__in'])) {
					$value_args['author__in'] = $value['author__in'];
				}

				if(!empty($value['taxonomy']) || !empty($value['tags'])) {
					$value_args['tax_query'] = array(
						'relation' => 'AND',
					);
				}

				if($this->isIds($value['taxonomy'])) {
					$value['taxonomy'] = $this->getSlugById($control['settings']['post_taxonomy'], $value['taxonomy']);
				}
				if(!empty($value['taxonomy'])) {


					$value_args['tax_query'][] = array(
						'field'    => 'slug',
						'taxonomy' => $control['settings']['post_taxonomy'],
						'operator' => 'IN',
						'terms'    => $value['taxonomy'],
					);
				}

				if($this->isIds($value['tags'])) {
					$value['tags'] = $this->getSlugById($control['settings']['post_tag'], $value['tags']);
				}
				if(!empty($value['tags'])) {


					$value_args['tax_query'][] = array(
						'field'    => 'slug',
						'taxonomy' => $control['settings']['post_tag'],
						'operator' => 'IN',
						'terms'    => $value['tags'],
					);
				}
			}
			$value['query'] = $value_args;

			return $value;
		}

		public function content_template(){
			$control_uid = $this->get_control_uid();

			$posts_per_page_uid      = $this->get_control_uid('posts_per_page');
			$orderby_uid             = $this->get_control_uid('orderby');
			$order_uid               = $this->get_control_uid('order');
			$taxonomy_uid            = $this->get_control_uid('taxonomy');
			$tags_uid                = $this->get_control_uid('tags');
			$author__in_uid          = $this->get_control_uid('author__in');
			$post__in_uid            = $this->get_control_uid('post__in');
			$ignore_sticky_posts_uid = $this->get_control_uid('ignore_sticky_posts');

			?>
            <# if (!data.settings.hidePostsCount) { #>
			<div class="elementor-control-field">
				<label for="<?php echo $control_uid; ?>" class="elementor-control-title">{{{ data.label }}}</label>
				<div class="elementor-control-input-wrapper">
					<label for="<?php echo $posts_per_page_uid; ?>" class="elementor-control-title"><?php esc_html_e('Post Count', 'gt3_themes_core') ?></label>
					<input id="<?php echo $posts_per_page_uid; ?>" type="number" min="-1" max="30" data-setting="posts_per_page"
					       class="posts_per_page tooltip-target elementor-control-tag-area"
					       data-tooltip="{{ data.title }}" title="{{ data.title }}" placeholder="{{ data.placeholder }}" />
					<div class="elementor-control-field-description"><?php esc_html_e('How many teasers to show? Enter number, -1 for All.', 'gt3_themes_core') ?></div>
				</div>
			</div>
            <# } #>
			<div class="elementor-control-type-switcher elementor-label-inline ignore_sticky_posts-wrapper">
				<div class="elementor-control-content">
					<div class="elementor-control-field">
						<label for="<?php echo $ignore_sticky_posts_uid; ?>" class="elementor-control-title"><?php esc_html_e('Ignore Sticky Posts', 'gt3_themes_core') ?></label>
						<div class="elementor-control-input-wrapper">
							<label class="elementor-switch">
								<input id="<?php echo $ignore_sticky_posts_uid; ?>" type="checkbox" data-setting="ignore_sticky_posts" class="elementor-switch-input" value="1">
								<span class="elementor-switch-label" data-on="<?php esc_html_e('On', 'gt3_themes_core') ?>"
								      data-off="<?php esc_html_e('Off', 'gt3_themes_core') ?>"></span>
								<span class="elementor-switch-handle"></span>
							</label>
						</div>
					</div>
				</div>
			</div>

			<div class="elementor-control-field">
				<div class="elementor-control-input-wrapper">
					<label for="<?php echo $orderby_uid; ?>" class="elementor-control-title"><?php esc_html_e('Order By', 'gt3_themes_core') ?></label>
					<select data-setting="orderby" class="orderby" id="<?php echo $orderby_uid ?>">
						<option value=''></option>
						<option value='date '><?php esc_html_e('Date', 'gt3_themes_core') ?></option>
						<option value='ID '><?php esc_html_e('ID', 'gt3_themes_core') ?></option>
						<option value='author '><?php esc_html_e('Author', 'gt3_themes_core') ?></option>
						<option value='title '><?php esc_html_e('Title', 'gt3_themes_core') ?></option>
						<option value='modified '><?php esc_html_e('Modified', 'gt3_themes_core') ?></option>
						<option value='rand '><?php esc_html_e('Random', 'gt3_themes_core') ?></option>
						<option value='comment_count '><?php esc_html_e('Comment count', 'gt3_themes_core') ?></option>
						<option value='menu_order '><?php esc_html_e('Menu order', 'gt3_themes_core') ?></option>
					</select>
					<div class="elementor-control-field-description"><?php printf(
							'%s <a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">%s</a>.',
							esc_html__('Select how to sort retrieved posts. More at', 'gt3_themes_core'),
							esc_html__('WordPress codex page', 'gt3_themes_core')) ?></div>
				</div>
			</div>

			<div class="elementor-control-field">
				<div class="elementor-control-input-wrapper">
					<label for="<?php echo $order_uid; ?>" class="elementor-control-title"><?php esc_html_e('Order', 'gt3_themes_core') ?></label>
					<select data-setting="order" class="order" id="<?php echo $order_uid ?>">
						<option value=''></option>
						<option value='ASC'><?php esc_html_e('Ascending', 'gt3_themes_core') ?></option>
						<option value='DESC'><?php esc_html_e('Descending', 'gt3_themes_core') ?></option>
					</select>
					<div class="elementor-control-field-description"><?php esc_html_e('Designates the ascending or descending order', 'gt3_themes_core') ?></div>
				</div>
			</div>

			<# if (data.settings.showPost) { #>
			<div class="elementor-control-field">
				<div class="elementor-control-input-wrapper">
					<label for="<?php echo $post__in_uid; ?>" class="elementor-control-title"><?php esc_html_e('Individual IDs', 'gt3_themes_core') ?></label>
					<select data-setting="post__in" id="<?php echo $post__in_uid ?>" class="post__in elementor-control-url-option-input"></select>
					<div class="elementor-control-field-description"><?php esc_html_e('Select Individual IDs', 'gt3_themes_core') ?></div>
				</div>
			</div>
			<# } #>

			<# if (data.settings.showCategory) { #>
			<div class="elementor-control-field selected_post__in">
				<div class="elementor-control-input-wrapper">
					<label for="<?php echo $taxonomy_uid; ?>" class="elementor-control-title"><?php esc_html_e('Category', 'gt3_themes_core') ?></label>
					<select data-setting="taxonomy" id="<?php echo $taxonomy_uid ?>" class="taxonomy elementor-control-url-option-input"></select>
					<div class="elementor-control-field-description"><?php esc_html_e('Filter output by custom taxonomies categories, enter category names here', 'gt3_themes_core') ?></div>
				</div>
			</div>
			<# } #>

			<# if (data.settings.showTag) { #>
			<div class="elementor-control-field selected_post__in">
				<div class="elementor-control-input-wrapper">
					<label for="<?php echo $tags_uid; ?>" class="elementor-control-title"><?php esc_html_e('Tags', 'gt3_themes_core') ?></label>
					<select data-setting="tags" id="<?php echo $tags_uid ?>" class="tags elementor-control-url-option-input"></select>
					<div class="elementor-control-field-description"><?php esc_html_e('Filter output by posts tags, enter tag names here', 'gt3_themes_core') ?></div>
				</div>
			</div>
			<# } #>

			<# if (data.settings.showUser) { #>
			<div class="elementor-control-field selected_post__in">
				<div class="elementor-control-input-wrapper">
					<label for="<?php echo $author__in_uid; ?>" class="elementor-control-title"><?php esc_html_e('Author', 'gt3_themes_core') ?></label>
					<select data-setting="author__in" id="<?php echo $author__in_uid ?>" class="author__in elementor-control-url-option-input"></select>
					<div class="elementor-control-field-description"><?php esc_html_e('Filter by author name', 'gt3_themes_core') ?></div>
				</div>
			</div>
			<# } #>
			<?php
		}
	}
}
