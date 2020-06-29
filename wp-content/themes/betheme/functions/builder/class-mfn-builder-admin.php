<?php
/**
 * Muffin Builder | Admin
 *
 * @package Betheme
 * @author Muffin group
 * @link https://muffingroup.com
 */

if( ! defined( 'ABSPATH' ) ){
	exit; // Exit if accessed directly
}

if( ! class_exists('Mfn_Builder_Admin') )
{
  class Mfn_Builder_Admin {

		private $fields;
    private $sizes = array(
      '1/6' => 0.1666,
      '1/5' => 0.2,
      '1/4' => 0.25,
      '1/3' => 0.3333,
      '2/5' => 0.4,
      '1/2' => 0.5,
      '3/5' => 0.6,
      '2/3' => 0.6667,
      '3/4' => 0.75,
      '4/5' => 0.8,
      '5/6' => 0.8333,
      '1/1' => 1,
      'divider' => 1
    );

    /**
     * Constructor
     */

    public function __construct() {

      // first action hooked into the admin scripts actions
  		add_action('admin_enqueue_scripts', array( $this, 'enqueue' ));

    }

		/**
		 * SET builder fields
		 */

		public function set_fields(){

			$this->fields = new Mfn_Builder_Fields();

		}

    /**
  	 * Enqueue styles and scripts
  	 */

    public function enqueue()
  	{
      wp_enqueue_style('mfn-builder', get_theme_file_uri('/functions/builder/assets/builder.css'), false, time(), 'all');
  		wp_enqueue_script('mfn-builder', get_theme_file_uri('/functions/builder/assets/builder.js'), array('jquery'), time(), true);
  	}

    /**
  	 * PRINT single FIELD
  	 */

    public static function field($field, $meta, $type = 'meta')
  	{

      if( empty($field['type']) ){
        return false;
      }

			// class for single option table row

			if (isset($field['class'])) {
				$class = $field['class'];
			} else {
				$class = '';
			}

			// output -----

			echo '<tr class="'. esc_attr($class) .'">';

				// label

				echo '<th>';

					if (key_exists('title', $field)) {
						echo esc_html($field['title']);
					}
					if (key_exists('sub_desc', $field)) {
						echo '<span class="description">'. wp_kses($field['sub_desc'], mfn_allowed_html('desc')) .'</span>';
					}

				echo '</th>';

				// field

				echo '<td>';

					$field_class = 'MFN_Options_'. $field['type'];
					require_once(get_template_directory() .'/muffin-options/fields/'. $field['type'] .'/field_'. $field['type'] .'.php');

					if (class_exists($field_class)) {
						$field_object = new $field_class($field, $meta);
						$field_object->render($type);
					}

				echo '</td>';

			echo '</tr>';

  	}

    /**
  	 * PRINT single SECTION
  	 */

    public function section($section = false, $uids = false)
  	{

  		// change section visibility

  		if ($section && key_exists('attr', $section) && key_exists('hide', $section['attr']) && $section['attr']['hide']) {
  			$hide = 'hide';
  			$icon = 'hidden';
  		} else {
  			$hide = false;
  			$icon = 'visibility';
  		}

  		// attributes

  		if( $section && key_exists('attr', $section) && key_exists('title', $section['attr']) ){
  			$section_label = $section['attr']['title'];
  		} else {
  			$section_label = '';
  		}

  		// section ID

  		if( $section ){

  			// section exists

  			if( ! empty($section['uid']) ){
  				// has unique ID
  				$section_id = $section['uid'];
  			} else {
  				// without unique ID
  				$section_id = Mfn_Builder_Helper::unique_ID( $uids );
  			}

  			$uids[] = $section_id;

  		} else {

  			// default empty section

  			$section_id = false;

  		}

  		// form fields names - only for existing sections, NOT for new sections

  		$n_row_id = $section ? 'mfn-row-id[]' : '';

  		// output -----

  		echo '<div class="mfn-element mfn-row '. esc_attr($hide) .'" data-title="'. esc_html__('Section', 'mfn-opts') .'">';

  			// section | content

  			echo '<div class="mfn-element-content">';

  				echo '<input type="hidden" class="mfn-row-id" name="'. esc_attr($n_row_id) .'" value="'. esc_attr($section_id) .'" />';

  				// section | header

  				echo '<div class="mfn-element-header mfn-row-header">';

  					echo '<div class="mfn-element-btns">';
  						echo '<a class="mfn-element-btn mfn-add-wrap" href="javascript:void(0);">'. esc_html__('Add Wrap', 'mfn-opts') .'</a>';
  						echo '<a class="mfn-element-btn mfn-add-divider" href="javascript:void(0);">'. esc_html__('Add Divider', 'mfn-opts') .'</a>';
  					echo '</div>';

  					echo '<span class="mfn-item-label">'. esc_html($section_label) .'</span>';

  					echo '<div class="mfn-element-tools">';
  						echo '<a class="mfn-element-btn mfn-element-edit dashicons dashicons-edit" title="'. esc_html__('Edit', 'mfn-opts') .'" href="javascript:void(0);"></a>';
  						echo '<a class="mfn-element-btn mfn-element-clone mfn-row-clone dashicons dashicons-share-alt2" title="'. esc_html__('Clone', 'mfn-opts') .'" href="javascript:void(0);"></a>';
  						echo '<a class="mfn-element-btn mfn-element-hide dashicons dashicons-'. $icon .'" title="'. esc_html__('Hide', 'mfn-opts') .'" href="javascript:void(0);"></a>';
  						echo '<a class="mfn-element-btn mfn-element-delete dashicons dashicons-no" title="'. esc_html__('Delete', 'mfn-opts') .'" href="javascript:void(0);"></a>';
  					echo '</div>';

  				echo '</div>';

  				// section | sortable

  				echo '<div class="mfn-sortable mfn-sortable-row clearfix">';

  					// section | existing wraps

  					if ($section) {

  						// FIX | Muffin Builder 2 compatibility
  						// there were no wraps inside section in Muffin Builder 2

  						if (! key_exists('wraps', $section) && is_array($section['items'])) {
  							$fix_wrap = array(
  								'size' => '1/1',
  								'items' => $section['items'],
  							);
  							$section['wraps'] = array( $fix_wrap );
  						}

  						// print inside wraps

  						if (key_exists('wraps', $section) && is_array($section['wraps'])) {
  							foreach ($section['wraps'] as $wrap) {
  								$uids = $this->wrap($wrap, $section_id, $uids);
  							}
  						}
  					}

  				echo '</div>';

  			echo '</div>';

  			// section | meta data

  			echo '<div class="mfn-element-meta">';

  				echo '<table class="form-table">';
  					echo '<tbody>';

  					// section | meta fields

  					$section_fields = $this->fields->get_section();

  					foreach ($section_fields as $field) {

  						// values for existing sections

  						if ($section && key_exists($field['id'], $section['attr'])) {
  							$meta = $section['attr'][$field['id']];
  						} else {
  							$meta = false;
  						}

  						// default values

  						if (! key_exists('std', $field)) {
  							$field['std'] = false;
  						}

  						if (( ! $meta ) && ( '0' !== $meta )) {
  							$meta = stripslashes(htmlspecialchars($field['std'], ENT_QUOTES));
  						}

  						// field ID

  						$field['id'] = 'mfn-rows['. $field['id'] .']';

  						// field ID except accordion, faq & tabs

  						if ($field['type'] != 'tabs') {
  							$field['id'] .= '[]';
  						}

  						// PRINT single FIELD

  						if ($section) {
  							$input_type = 'existing';
  						} else {
  							$input_type = 'new';
  						}

  						self::field($field, $meta, $input_type);
  					}

  					echo '</tbody>';
  				echo '</table>';

  			echo '</div>';

  		echo '</div>';

  		return $uids;
  	}

    /**
  	 * PRINT single WRAP
  	 */

  	private function wrap($wrap = false, $parent_id = false, $uids = false)
  	{
  		$size = $wrap ? $wrap['size'] : '1/1';

  		// form fields names - only for existing wraps, NOT for new wrap

  		$n_wrap_id = $wrap ? 'mfn-wrap-id[]' : '';
  		$n_wrap_parent = $wrap ? 'mfn-wrap-parent[]' : '';
  		$n_wrap_size = $wrap ? 'mfn-wrap-size[]' : '';

  		// wrap ID

  		if( $wrap ){

  			// wrap exists

  			if( ! empty($wrap['uid']) ){
  				// has unique ID
  				$wrap_id = $wrap['uid'];
  			} else {
  				// without unique ID
  				$wrap_id = Mfn_Builder_Helper::unique_ID( $uids );
  			}

  			$uids[] = $wrap_id;

  		} else {

  			// default empty wrap

  			$wrap_id = false;

  		}

  		// attributes

  		$class = '';
  		if ($wrap && ($wrap['size'] == 'divider')) {
  			$class .= ' divider';
  		}

  		// output -----

  		echo '<div class="mfn-element mfn-wrap '. esc_attr($class) .'" data-size="'. esc_attr($this->sizes[$size]) .'" data-title="'. esc_html__('Wrap', 'mfn-opts') .'">';

  			// wrap | content

  			echo '<div class="mfn-element-content">';

  				echo '<input type="hidden" class="mfn-wrap-id" name="'. esc_attr($n_wrap_id) .'" value="'. esc_attr($wrap_id) .'" />';
  				echo '<input type="hidden" class="mfn-wrap-parent" name="'. esc_attr($n_wrap_parent) .'" value="'. esc_attr($parent_id) .'" />';
  				echo '<input type="hidden" class="mfn-wrap-size" name="'. esc_attr($n_wrap_size) .'" value="'. esc_attr($size) .'" />';

  				// wrap | header

  				echo '<div class="mfn-element-header mfn-wrap-header">';

  					echo '<div class="mfn-item-size">';
  						echo '<a class="mfn-element-btn mfn-item-size-dec" href="javascript:void(0);">-</a>';
  						echo '<a class="mfn-element-btn mfn-item-size-inc" href="javascript:void(0);">+</a>';
  						echo '<a class="mfn-element-btn mfn-add-item" href="javascript:void(0);">Add Item</a>';
  						echo '<span class="mfn-element-btn mfn-item-desc">'. esc_attr($size) .'</span>';
  					echo '</div>';

  					echo '<div class="mfn-element-tools">';
  						echo '<a class="mfn-element-btn mfn-element-edit mfn-wrap-edit dashicons dashicons-edit" title="'. esc_html__('Edit', 'mfn-opts') .'" href="javascript:void(0);"></a>';
  						echo '<a class="mfn-element-btn mfn-element-clone mfn-wrap-clone dashicons dashicons-share-alt2" title="'. esc_html__('Clone', 'mfn-opts') .'" href="javascript:void(0);"></a>';
  						echo '<a class="mfn-element-btn mfn-element-delete dashicons dashicons-no" title="'. esc_html__('Delete', 'mfn-opts') .'" href="javascript:void(0);"></a>';
  					echo '</div>';

  				echo '</div>';

  				// wrap | sortable

  				echo '<div class="mfn-sortable mfn-sortable-wrap clearfix">';

  					if ($wrap && key_exists('items', $wrap) && is_array($wrap['items'])) {
  						foreach ($wrap['items'] as $item) {
  							$uids = $this->item($item['type'], $item, $wrap_id, $uids);
  						}
  					}

  				echo '</div>';

  			echo '</div>';

  			// wrap | meta

  			echo '<div class="mfn-element-meta">';

  				echo '<table class="form-table">';
  					echo '<tbody>';

  						// wrap | meta fields

  						$wrap_fields = $this->fields->get_wrap();

  						foreach ($wrap_fields as $field) {

  							// values for existing wraps

  							if ($wrap && key_exists('attr', $wrap) && key_exists($field['id'], $wrap['attr'])) {
  								$meta = $wrap['attr'][$field['id']];
  							} else {
  								$meta = false;
  							}

  							// default values

  							if (! key_exists('std', $field)) {
  								$field['std'] = false;
  							}

  							if (( ! $meta ) && ( '0' !== $meta )) {
  								$meta = stripslashes(htmlspecialchars($field['std'], ENT_QUOTES));
  							}

  							// field ID

  							$field['id'] = 'mfn-wraps['. $field['id'] .']';

  							// field ID except accordion, faq & tabs

  							if ($field['type'] != 'tabs') {
  								$field['id'] .= '[]';
  							}

  							// PRINT single FIELD

  							if ($wrap) {
  								$input_type = 'existing';
  							} else {
  								$input_type = 'new';
  							}

  							self::field($field, $meta, $input_type);
  						}

  					echo '</tbody>';
  				echo '</table>';

  			echo '</div>';

  		echo '</div>';

  		return $uids;
  	}

    /**
  	 * PRINT single ITEM
  	 */

  	private function item($item_type, $item = false, $parent_id = false, $uids = false)
  	{

  		$item_std = $this->fields->get_item_fields($item_type);

  		$item_std['size'] = $item['size'] ? $item['size'] : $item_std['size'];

  		// form fields names - only for existing items, NOT for new items

  		$n_item_type = $item ? 'mfn-item-type[]' : '';
  		$n_item_id = $item ? 'mfn-item-id[]' : '';
  		$n_item_size = $item ? 'mfn-item-size[]' : '';
  		$n_item_parent = $item ? 'mfn-item-parent[]' : '';

  		// item ID

  		if( $item ){

  			// item exists
  			if( ! empty($item['uid']) ){
  				// has unique ID
  				$item_id = $item['uid'];
  			} else {
  				// without unique ID
  				$item_id = Mfn_Builder_Helper::unique_ID( $uids );
  			}

  			$uids[] = $item_id;

  		} else {

  			// default empty item
  			$item_id = false;

  		}

  		// output -----

  		echo '<div class="mfn-element mfn-item mfn-item-'. esc_attr($item_std['type']) .'" data-size="'. esc_attr($this->sizes[$item_std['size']]) .'" data-title="'. esc_attr($item_std['title']) .'">';

  			echo '<div class="mfn-element-content">';

  				echo '<input type="hidden" class="mfn-item-type" name="'. esc_attr($n_item_type) .'" value="'. esc_attr($item_std['type']) .'">';
  				echo '<input type="hidden" class="mfn-item-id" name="'. esc_attr($n_item_id) .'" value="'. esc_attr($item_id) .'" />';
  				echo '<input type="hidden" class="mfn-item-parent" name="'. esc_attr($n_item_parent) .'" value="'. esc_attr($parent_id) .'" />';
  				echo '<input type="hidden" class="mfn-item-size" name="'. esc_attr($n_item_size) .'" value="'. esc_attr($item_std['size']) .'">';

  				echo '<div class="mfn-element-header">';

  					echo '<div class="mfn-item-size">';
  						echo '<a class="mfn-element-btn mfn-item-size-dec" href="javascript:void(0);">-</a>';
  						echo '<a class="mfn-element-btn mfn-item-size-inc" href="javascript:void(0);">+</a>';
  						echo '<span class="mfn-element-btn mfn-item-desc">'. esc_attr($item_std['size']) .'</span>';
  					echo '</div>';

  					echo '<div class="mfn-element-tools">';
  						echo '<a class="mfn-element-btn mfn-fr mfn-element-edit dashicons dashicons-edit" title="'. esc_html__('Edit', 'mfn-opts') .'" href="javascript:void(0);"></a>';
  						echo '<a class="mfn-element-btn mfn-fr mfn-element-clone mfn-item-clone dashicons dashicons-share-alt2" title="'. esc_html__('Clone', 'mfn-opts') .'" href="javascript:void(0);"></a>';
  						echo '<a class="mfn-element-btn mfn-fr mfn-element-delete dashicons dashicons-no" title="'. esc_html__('Delete', 'mfn-opts') .'" href="javascript:void(0);"></a>';
  					echo '</div>';

  				echo '</div>';

  				echo '<div class="mfn-item-content">';

  					echo '<div class="mfn-item-inside">';

  						echo '<div class="mfn-item-icon"></div>';
  						echo '<div class="mfn-item-inside-desc">';

  							echo '<span class="mfn-item-title">'. esc_html($item_std['title']) .'</span>';

  							$item_label = ($item && key_exists('fields', $item) && key_exists('title', $item['fields'])) ? $item['fields']['title'] : '';
  							echo '<span class="mfn-item-label">'. wp_kses($item_label, array()) .'</span>';

  						echo '</div>';

  					echo '</div>';

  					if ($item && key_exists('fields', $item) && key_exists('content', $item['fields'])) {

  						$item_excerpt = strip_shortcodes(strip_tags($item['fields']['content']));

  						$item_excerpt = preg_split('/\b/', $item_excerpt, 16 * 2 + 1);
  						$item_excerpt_waste = array_pop($item_excerpt);
  						$item_excerpt = implode($item_excerpt);

  						echo '<p class="mfn-item-excerpt">'. esc_html($item_excerpt) .'</p>';
  					}

  				echo '</div>';

  			echo '</div>';

  			echo '<div class="mfn-element-meta">';

  				echo '<table class="form-table">';
  					echo '<tbody>';

  						// fields

  						foreach ($item_std['fields'] as $field) {

  							// values for existing items

  							if ($item && key_exists('fields', $item) && key_exists($field['id'], $item['fields'])) {
  								$meta = $item['fields'][$field['id']];
  							} else {
  								if (! key_exists('std', $field)) {
  									$field['std'] = false;
  								}
  								$meta = stripslashes(htmlspecialchars($field['std'], ENT_QUOTES));
  							}

  							// field ID

  							$field['id'] = 'mfn-items['. $item_std['type'] .']['. $field['id'] .']';

  							// field ID except accordion, faq & tabs

  							if ($field['type'] != 'tabs') {
  								$field['id'] .= '[]';
  							}

  							// PRINT single FIELD

  							if ($item) {
  								$input_type = 'existing';
  							} else {
  								$input_type = 'new';
  							}

  							self::field($field, $meta, $input_type);
  						}

  					echo '</tbody>';
  				echo '</table>';

  			echo '</div>';

  		echo '</div>';

  		return $uids;
  	}

    /**
     * PRINT Muffin Builder
     */

    public function show()
    {
      global $post;

      $uids = array();
			$items = $this->fields->get_items(); // default items

      // hide builder if current user does not have a specific capability

      if ($visibility = mfn_opts_get('builder-visibility')) {
        if ($visibility == 'hide' || (! current_user_can($visibility))) {
          return false;
        }
      }

      // GET items

      $mfn_items = get_post_meta($post->ID, 'mfn-page-items', true);

      // FIX | Muffin Builder 2 compatibility

      if ($mfn_items && ! is_array($mfn_items)) {
        $mfn_items = unserialize(call_user_func('base'.'64_decode', $mfn_items));
      }

      // debug
      // print_r( $mfn_items );

      ?>

      <div id="mfn-builder">

        <input type="hidden" name="mfn-items-save" value="1"/>
        <a id="mfn-go-to-top" class="dashicons dashicons-arrow-up-alt" href="javascript:void(0);"></a>

        <div id="mfn-content">

          <!-- add section | first -->

          <div class="mfn-row-add">
            <table class="form-table">
              <tbody>
                <tr valign="top">
                  <td>
                    <a class="mfn-row-add-btn add-first" href="javascript:void(0);">
                      <span class="dashicons dashicons-align-center"></span>
                      <?php echo '<strong>'. esc_html__('Add Section', 'mfn-opts') .'</strong> '. esc_html__('as the first section', 'mfn-opts'); ?>
                    </a>
                    <div class="logo">Muffin Group | Muffin Builder</div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- builder desktop -->

          <div id="mfn-desk" class="clearfix">

            <?php
              $class_add_row = 'hide';

              // print_r($mfn_items);

              if (is_array($mfn_items)) {
                foreach ($mfn_items as $section) {
                  $uids = $this->section($section, $uids);
                }

                if(count($mfn_items)) {
                  $class_add_row = false;
                }

              }
            ?>

          </div>

          <!-- add section | last -->

          <div class="mfn-row-add last <?php echo esc_attr($class_add_row); ?>">
            <table class="form-table">
              <tbody>
                <tr valign="top">
                  <td>
                    <a class="mfn-row-add-btn add-last" href="javascript:void(0);">
                      <span class="dashicons dashicons-align-center"></span>
                      <?php echo '<strong>'. esc_html__('Add Section', 'mfn-opts') .'</strong> '. esc_html__('as the last section', 'mfn-opts'); ?>
                    </a>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- section | default new -->

          <div id="mfn-rows" class="clearfix">
            <?php $this->section(); ?>
          </div>

          <!-- wrap | default new -->

          <div id="mfn-wraps" class="clearfix">
            <?php $this->wrap(); ?>
          </div>

          <!-- items | default new -->

          <div id="mfn-items" class="clearfix">
            <?php
              foreach ($items as $item) {
                $this->item($item['type']);
                echo "\n";
              }
            ?>
          </div>

          <!-- add new item popup -->

          <div id="mfn-item-add" class="mfn-popup mfn-popup-item-add">
            <div class="mfn-popup-inside">

              <div class="mfn-popup-header">

                <div class="mfn-ph-left">
                  <span class="mfn-ph-btn mfn-ph-desc"><?php esc_html_e('Add Item', 'mfn-opts'); ?></span>
                </div>

                <div class="mfn-ph-right">
                  <a class="mfn-ph-btn mfn-ph-close dashicons dashicons-no" title="<?php esc_html_e('Close', 'mfn-opts'); ?>" href="javascript:void(0);"></a>
                </div>

              </div>

              <div class="mfn-popup-content">

                <div class="mfn-popup-subheader">

                  <ul class="mfn-popup-tabs">
                    <li data-filter="*" class="active"><a href="javascript:void(0);"><?php esc_html_e('All', 'mfn-opts'); ?></a></li>
                    <li data-filter="typography"><a href="javascript:void(0);"><?php esc_html_e('Typography', 'mfn-opts'); ?></a></li>
                    <li data-filter="boxes"><a href="javascript:void(0);"><?php esc_html_e('Boxes & Infographics', 'mfn-opts'); ?></a></li>
                    <li data-filter="blocks"><a href="javascript:void(0);"><?php esc_html_e('Content Blocks', 'mfn-opts'); ?></a></li>
                    <li data-filter="elements"><a href="javascript:void(0);"><?php esc_html_e('Content Elements', 'mfn-opts'); ?></a></li>
                    <li data-filter="loops"><a href="javascript:void(0);"><?php esc_html_e('Loops', 'mfn-opts'); ?></a></li>
                    <li data-filter="other"><a href="javascript:void(0);"><?php esc_html_e('Other', 'mfn-opts'); ?></a></li>
                  </ul>

                  <div class="mfn-popup-search">
                    <span class="dashicons dashicons-search"></span>
                    <input class="mfn-search-item" placeholder="<?php esc_html_e('Search Item', 'mfn-opts'); ?>" />
                  </div>

                </div>

                <ul class="mfn-popup-items clear">
                  <?php
                    foreach ($items as $item) {

                      $category = isset($item['cat']) ? 'category-'. $item['cat'] : '' ;

                      echo '<li class="mfn-item-'. esc_attr($item['type']) .' '. esc_attr($category) .'" data-type="'. esc_attr($item['type']) .'">';
                        echo '<a data-type="'. esc_attr($item['type']) .'" href="javascript:void(0);">';
                          echo '<span class="title">'. esc_html($item['title']) .'</span>';
                          echo '<div class="mfn-item-icon"></div>';
                        echo '</a>';
                      echo '</li>';

                    }
                  ?>
                </ul>

              </div>

            </div>
          </div>

          <!-- migrate -->

          <div id="mfn-migrate">

            <a href="javascript:void(0);" class="mfn-btn-migrate btn-seo"><?php esc_html_e('Builder &raquo; SEO', 'mfn-opts'); ?></a>

            <div class="btn-wrapper">
              <a href="javascript:void(0);" class="mfn-btn-migrate btn-exp"><?php esc_html_e('Export', 'mfn-opts'); ?></a>
              <a href="javascript:void(0);" class="mfn-btn-migrate btn-imp"><?php esc_html_e('Import', 'mfn-opts'); ?></a>
              <a href="javascript:void(0);" class="mfn-btn-migrate btn-tem btn-primary"><?php esc_html_e('Templates', 'mfn-opts'); ?></a>
            </div>

            <div class="migrate-wrapper export-wrapper hide">
              <textarea id="mfn-items-export" placeholder="Muffin Builder data processing..."></textarea>
              <span class="description"><?php esc_html_e('Copy to clipboard: Ctrl+C (Cmd+C for Mac)', 'mfn-opts'); ?></span>
            </div>

            <div class="migrate-wrapper import-wrapper hide">

              <textarea id="mfn-items-import" placeholder="Paste import data here."></textarea>
              <a href="javascript:void(0);" class="mfn-btn-migrate btn-primary btn-import"><?php esc_html_e('Import', 'mfn-opts'); ?></a>

              <select id="mfn-items-import-type">
                <option value="before"><?php esc_html_e('Insert BEFORE current builder content', 'mfn-opts'); ?></option>
                <option value="after"><?php esc_html_e('Insert AFTER current builder content', 'mfn-opts'); ?></option>
                <option value="replace"><?php esc_html_e('REPLACE current builder content', 'mfn-opts'); ?></option>
              </select>

            </div>

            <div class="migrate-wrapper templates-wrapper hide">

              <a href="javascript:void(0);" class="mfn-btn-migrate btn-primary btn-template"><?php esc_html_e('Use Template', 'mfn-opts'); ?></a>

              <select id="mfn-items-import-template-type">
                <option value="before"><?php esc_html_e('Insert BEFORE current builder content', 'mfn-opts'); ?></option>
                <option value="after"><?php esc_html_e('Insert AFTER current builder content', 'mfn-opts'); ?></option>
                <option value="replace"><?php esc_html_e('REPLACE current builder content', 'mfn-opts'); ?></option>
              </select>

              <select id="mfn-items-import-template">
                <option value=""><?php esc_html_e('-- Select --', 'mfn-opts'); ?></option>
                <?php
                  $args = array(
                    'post_type' => 'template',
                    'posts_per_page'=> -1,
                  );
                  $templates = get_posts($args);

                  if (is_array($templates)) {
                    foreach ($templates as $v) {
                      echo '<option value="'. esc_attr($v->ID) .'">'. esc_html($v->post_title) .'</options>';
                    }
                  }
                ?>
              </select>
            </div>

          </div>

        </div>

        <!-- builder to SEO -->

        <div id="mfn-items-seo">
          <?php
            $mfn_items_seo = get_post_meta($post->ID, 'mfn-page-items-seo', true);
            echo '<textarea id="mfn-items-seo-data">'. esc_attr($mfn_items_seo) .'</textarea>';
          ?>
        </div>

      </div>

      <?php
    }

    /**
  	 * SAVE Muffin Builder
  	 */

  	public function save( $post_id )
  	{

  		// FIX | Visual Composer Frontend

  		if (isset($_POST['vc_inline'])) {
  			return false;
  		}

  		// variables

  		$mfn_items = array();
  		$mfn_wraps = array();

  		// LOOP sections

  		if (key_exists('mfn-row-id', $_POST) && is_array($_POST['mfn-row-id'])) {

  			foreach ($_POST['mfn-row-id'] as $sectionID_k => $sectionID) {

  				$section = array();

  				$section['uid'] = $_POST['mfn-row-id'][$sectionID_k];

  				// $section['attr'] - section attributes

  				if (key_exists('mfn-rows', $_POST) && is_array($_POST['mfn-rows'])) {
  					foreach ($_POST['mfn-rows'] as $section_attr_k => $section_attr) {
  						$section['attr'][$section_attr_k] = stripslashes($section_attr[$sectionID_k]);
  					}
  				}

  				$section['wraps'] = ''; // $section['wraps'] - section wraps will be added in next loop

  				$mfn_items[] = $section;
  			}

  			$row_IDs = $_POST['mfn-row-id'];
  			$row_IDs_key = array_flip($row_IDs);
  		}

  		// LOOP wraps

  		if (key_exists('mfn-wrap-id', $_POST) && is_array($_POST['mfn-wrap-id'])) {

  			foreach ($_POST['mfn-wrap-id'] as $wrapID_k => $wrapID) {

  				$wrap = array();

  				$wrap['uid'] = $_POST['mfn-wrap-id'][$wrapID_k];
  				$wrap['size'] = $_POST['mfn-wrap-size'][$wrapID_k];
  				$wrap['items'] = ''; // $wrap['items'] - items will be added in the next loop

  				// $wrap['attr'] - wrap attributes

  				if (key_exists('mfn-wraps', $_POST) && is_array($_POST['mfn-wraps'])) {
  					foreach ($_POST['mfn-wraps'] as $wrap_attr_k => $wrap_attr) {
  						$wrap['attr'][$wrap_attr_k] = $wrap_attr[$wrapID_k];
  					}
  				}

  				$mfn_wraps[$wrapID] = $wrap;
  			}

  			$wrap_IDs = $_POST['mfn-wrap-id'];
  			$wrap_IDs_key = array_flip($wrap_IDs);
  			$wrap_parents = $_POST['mfn-wrap-parent'];
  		}

  		// LOOP items

  		if (key_exists('mfn-item-type', $_POST) && is_array($_POST['mfn-item-type'])) {

  			$count = array();
  			$tabs_count = array();

  			$seo_content = '';

  			foreach ($_POST['mfn-item-type'] as $type_k => $type) {

  				$item = array();
  				$item['type'] = $type;
  				$item['uid'] = $_POST['mfn-item-id'][$type_k];
  				$item['size'] = $_POST['mfn-item-size'][$type_k];

  				// init count for specified item type

  				if (! key_exists($type, $count)) {
  					$count[$type] = 0;
  				}

  				// init count for specified tab type

  				if (! key_exists($type, $tabs_count)) {
  					$tabs_count[$type] = 0;
  				}

  				if (key_exists($type, $_POST['mfn-items'])) {
  					foreach ((array) $_POST['mfn-items'][$type] as $attr_k => $attr) {

  						if ($attr_k == 'tabs') {

  							// accordion, FAQ & tabs

  							$item['fields']['count'] = $attr['count'][$count[$type]];

  							if ($item['fields']['count']) {
  								for ($i = 0; $i < $item['fields']['count']; $i++) {
  									$tab = array();
  									$tab['title'] 	= stripslashes($attr['title'][$tabs_count[$type]]);

  									if (mfn_opts_get('builder-storage')) {
  										$tab['content'] = stripslashes($attr['content'][$tabs_count[$type]]);
  									} else {
  										// core.trac.wordpress.org/ticket/34845
  										$tab['content'] = preg_replace('~\R~u', "\n", stripslashes($attr['content'][$tabs_count[$type]]));
  									}

  									$item['fields']['tabs'][] = $tab;

  									// FIX | Yoast SEO

  									$seo_val = trim($attr['title'][$tabs_count[$type]]);
  									if ($seo_val && $seo_val != 1) {
  										$seo_content .= $attr['title'][$tabs_count[$type]] .": ";
  									}
  									$seo_val = trim($attr['content'][$tabs_count[$type]]);
  									if ($seo_val && $seo_val != 1) {
  										$seo_content .= $attr['content'][$tabs_count[$type]] ."\n\n";
  									}

  									$tabs_count[$type]++;
  								}
  							}

  						} else {

  							// all other items

  							if (mfn_opts_get('builder-storage')) {
  								$item['fields'][$attr_k] = stripslashes($attr[$count[$type]]);
  							} else {
  								// core.trac.wordpress.org/ticket/34845
  								$item['fields'][$attr_k] = preg_replace('~\R~u', "\n", stripslashes($attr[$count[$type]]));
  							}

  							// FIX | Yoast SEO

  							$seo_val = trim($attr[$count[$type]]);

  							if ($seo_val && $seo_val != 1) {
  								if (in_array($attr_k, array( 'image', 'src' ))) {
  									// image
  									$seo_content .= '<img src="'. $seo_val .'" alt="'. mfn_get_attachment_data($seo_val, 'alt') .'"/>'."\n";
  								} elseif ($attr_k == 'link') {
  									// link
  									$seo_content .= '<a href="'. $seo_val .'">'. $seo_val .'</a>'."\n";
  								} else {
  									$seo_content .= $seo_val ."\n";
  								}
  							}

  						}
  					}

  					$seo_content .= "\n";
  				}

  				// increase count for specified item type

  				$count[$type] ++;

  				// parent wrap

  				$parent_wrap_ID = $_POST['mfn-item-parent'][$type_k];

  				if (! isset($mfn_wraps[ $parent_wrap_ID ]['items']) || ! is_array($mfn_wraps[ $parent_wrap_ID ]['items'])) {
  					$mfn_wraps[ $parent_wrap_ID ]['items'] = array();
  				}
  				$mfn_wraps[ $parent_wrap_ID ]['items'][] = $item;
  			}
  		}

  		// assign wraps with items to sections

  		foreach ($mfn_wraps as $wrap_ID => $wrap) {

  			$wrap_key = $wrap_IDs_key[ $wrap_ID ];
  			$section_ID = $wrap_parents[ $wrap_key ];
  			$section_key = $row_IDs_key[ $section_ID ];

  			if (! isset($mfn_items[ $section_key ]['wraps']) || ! is_array($mfn_items[ $section_key ]['wraps'])) {
  				$mfn_items[ $section_key ]['wraps'] = array();
  			}
  			$mfn_items[ $section_key ]['wraps'][] = $wrap;

  		}

  		// debug
  		// print_r($mfn_items);
  		// exit;

  		// prepare data to save

  		if ($mfn_items) {
  			if (mfn_opts_get('builder-storage') == 'encode') {
  				$new = call_user_func('base'.'64_encode', serialize($mfn_items));
  			} else {
  				// codex.wordpress.org/Function_Reference/update_post_meta
  				$new = wp_slash($mfn_items);
  			}
  		}

  		// SAVE data

  		if (key_exists('mfn-items-save', $_POST)) {

  			$field['id'] = 'mfn-page-items';
  			$old = get_post_meta($post_id, $field['id'], true);

  			if (isset($new) && $new != $old) {

  				// update post meta if there is at least one builder section
  				update_post_meta($post_id, $field['id'], $new);

  			} elseif ($old && (! isset($new) || ! $new)) {

  				// delete post meta if builder is empty
  				delete_post_meta($post_id, $field['id'], $old);

  			}

  			// FIX | Yoast SEO

  			if (isset($new)) {
  				update_post_meta($post_id, 'mfn-page-items-seo', $seo_content);
  			}

  		}
  	}

  }
}
