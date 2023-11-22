<?php

namespace DynamicContentForElementor;

use Elementor\Controls_Manager;
use Elementor\Core\Common\Modules\Ajax\Module as Elementor_Ajax;
use Elementor\Widget_Base;
use Elementor\TemplateLibrary\Source_Local;
if (!\defined('ABSPATH')) {
    exit;
}
class Ajax
{
    public $query_control;
    public function __construct()
    {
        add_action('wp_ajax_wpa_update_postmetas', array($this, 'wpa_update_postmetas'));
        add_action('wp_ajax_wpa_update_options', array($this, 'wpa_update_options'));
        add_action('wp_ajax_dce_file_browser_hits', array($this, 'dce_file_browser_hits'));
        add_action('wp_ajax_nopriv_dce_file_browser_hits', array($this, 'dce_file_browser_hits'));
        add_action('wp_ajax_dce_get_next_post', array($this, 'dce_get_next_post'));
        add_action('wp_ajax_nopriv_dce_get_next_post', array($this, 'dce_get_next_post'));
        add_action('wp_ajax_dce_visibility_is_hidden', array($this, 'dce_visibility_is_hidden'));
        // Ajax page open Actions
        add_action('wp_ajax_modale_action', array($this, 'dce_ajax_action'));
        add_action('wp_ajax_nopriv_modale_action', array($this, 'dce_ajax_action'));
        add_action('wp_ajax_dualview_action', array($this, 'dce_dual_view_ajax_action'));
        add_action('wp_ajax_nopriv_dualview_action', array($this, 'dce_dual_view_ajax_action'));
        add_action('wp_ajax_dce_elementor_template', array($this, 'dce_elementor_template'));
        add_action('wp_ajax_nopriv_dce_elementor_template', array($this, 'dce_elementor_template'));
        add_action('wp_ajax_dce_add_to_favorites', [$this, 'add_to_favorites']);
        add_action('wp_ajax_nopriv_dce_add_to_favorites', [$this, 'add_to_favorites']);
        // Ajax Select2 autocomplete
        $this->query_control = new \DynamicContentForElementor\Modules\QueryControl\Module();
    }
    public function add_to_favorites()
    {
        if (!wp_verify_nonce($_REQUEST['nonce'], 'dce_add_to_favorites')) {
            exit('Nonce verification error');
        }
        $element_id = empty($_GET['eid']) ? 0 : sanitize_text_field($_GET['eid']);
        $favorite_post_id = empty($_GET['dce_post_id']) ? 0 : \intval($_GET['dce_post_id']);
        $favorite_post_id = apply_filters('wpml_object_id', $favorite_post_id, get_post_type($favorite_post_id), \true);
        $list_key = empty($_GET['dce_list']) ? 0 : sanitize_text_field($_GET['dce_list']);
        if ($element_id && $favorite_post_id && $list_key) {
            status_header(200);
            global $wp_query;
            $wp_query->is_singular = \true;
            $wp_query->is_page = $wp_query->is_singular;
            $wp_query->is_404 = \false;
            $element = \DynamicContentForElementor\Helper::get_elementor_element_by_id($element_id);
            if ($element) {
                $element->update_list($element_id, $favorite_post_id, $list_key);
                $settings = $element->get_settings_for_display();
                $favorite = $element->get_favorite_value($list_key, $settings['dce_favorite_scope']);
                echo \implode(', ', $favorite);
            }
        }
        die;
    }
    public function wpa_update_postmetas()
    {
        if (!current_user_can('administrator')) {
            wp_die();
        }
        // The $_REQUEST contains all the data sent via ajax
        $post_id = 0;
        if (isset($_REQUEST['post_id'])) {
            $post_id = \intval($_REQUEST['post_id']);
        }
        if ($post_id) {
            foreach ($_REQUEST as $key => $value) {
                if ($key != 'action' && $key != 'post_id') {
                    if ($value) {
                        $tmp = get_post_meta($post_id, $key, \true);
                        if (\is_array($value)) {
                            if (!empty($tmp)) {
                                $value = \array_merge($tmp, $value);
                            }
                        }
                        update_post_meta($post_id, $key, $value);
                    } else {
                        delete_post_meta($post_id, $key);
                    }
                }
            }
        } else {
            return \false;
        }
        echo wp_json_encode($_REQUEST);
        // Always die in functions echoing ajax content
        wp_die();
        // this is required to terminate immediately and return a proper response
    }
    public function wpa_update_options()
    {
        if (!current_user_can('administrator')) {
            wp_die();
        }
        // The $_REQUEST contains all the data sent via ajax
        foreach ($_REQUEST as $key => $value) {
            if ($key != 'action') {
                if ($value) {
                    if (\is_array($value)) {
                        $tmp = get_option($key);
                        if (!empty($tmp)) {
                            $value = \array_merge($tmp, $value);
                        }
                    }
                    update_option($key, $value);
                } else {
                    delete_option($key);
                }
            }
        }
        echo wp_json_encode($_REQUEST);
        // Always die in functions echoing ajax content
        wp_die();
        // this is required to terminate immediately and return a proper response
    }
    public function dce_file_browser_hits()
    {
        // The $_REQUEST contains all the data sent via ajax
        if (isset($_REQUEST)) {
            if (isset($_REQUEST['post_id'])) {
                $post_id = \intval($_REQUEST['post_id']);
                $key = 'dce-file';
                $tmp = get_post_meta($post_id, $key, \true);
                $value = array('hits' => 1);
                if (!empty($tmp)) {
                    if (\is_array($tmp)) {
                        if (isset($tmp['hits'])) {
                            $tmp['hits'] = \intval($tmp['hits']) + 1;
                        } else {
                            $tmp['hits'] = 1;
                        }
                    }
                    $value = $tmp;
                }
                update_post_meta($post_id, $key, $value);
            } elseif (isset($_REQUEST['md5'])) {
                $md5 = sanitize_text_field($_REQUEST['md5']);
                $key = 'dce-file-' . $md5;
                $tmp = get_option($key);
                $value = array('hits' => 1);
                if (!empty($tmp)) {
                    if (\is_array($tmp)) {
                        if (isset($tmp['hits'])) {
                            $tmp['hits'] = \intval($tmp['hits']) + 1;
                        } else {
                            $tmp['hits'] = 1;
                        }
                    }
                    $value = $tmp;
                }
                update_option($key, $value);
            }
        }
        echo wp_json_encode($_REQUEST);
        // Always die in functions echoing ajax content
        wp_die();
        // this is required to terminate immediately and return a proper response
    }
    public function dce_visibility_is_hidden()
    {
        // The $_REQUEST contains all the data sent via ajax
        if (isset($_REQUEST['element_id']) && isset($_REQUEST['post_id'])) {
            $element_id = sanitize_text_field($_REQUEST['element_id']);
            $post_id = \intval($_REQUEST['post_id']);
            $settings = \DynamicContentForElementor\Helper::get_settings_by_id($element_id, $post_id);
            if (!empty($settings['enabled_visibility']) && \DynamicContentForElementor\Extensions\DynamicVisibility::is_hidden($settings)) {
                echo $element_id;
                wp_die();
            }
        }
        echo '0';
        // Always die in functions echoing ajax content
        wp_die();
        // this is required to terminate immediately and return a proper response
    }
    /**
     * Get Next Post
     *
     * @param string $id
     * @return void
     */
    public function dce_get_next_post($id = null)
    {
        $ret = array();
        // The $_REQUEST contains all the data sent via ajax
        if (isset($_REQUEST)) {
            $next = \DynamicContentForElementor\Helper::get_adjacent_post_by_id(null, null, \true, null, \intval($_REQUEST['post_id']));
            $next_url = get_permalink($next->ID);
            $ret['ID'] = $next->ID;
            $ret['permalink'] = $next_url;
            $ret['title'] = wp_kses_post(get_the_title($next->ID));
            $ret['thumbnail'] = get_the_post_thumbnail($next->ID);
        }
        echo wp_json_encode($ret);
        // Always die in functions echoing ajax content
        wp_die();
        // this is required to terminate immediately and return a proper response
    }
    public static function dce_ajax_action()
    {
        $postid = url_to_postid(sanitize_text_field($_POST['post_href']));
        $template_id = 0;
        if (isset($_POST['template_id']) && \is_numeric($_POST['template_id'])) {
            $template_id = sanitize_text_field($_POST['template_id']);
        }
        $titolo_seo = get_post_meta($postid, '_yoast_wpseo_title', \true);
        $titolo_nativo = wp_kses_post(get_the_title($postid)) . ' - ' . get_bloginfo('name');
        if (!empty($titolo_seo)) {
            $titolo_nativo = $titolo_seo;
        }
        echo '<div class="content-p">';
        self::get_template_for_ajax($template_id, $postid);
        echo '<div class="titolo-nativo">' . $titolo_nativo . '</div>';
        echo '</div>';
        wp_die();
    }
    public static function dce_dual_view_ajax_action()
    {
        $postid = url_to_postid(sanitize_text_field($_POST['post_href']));
        $template_id = sanitize_text_field($_POST['template_id']);
        $featuredImage = get_the_post_thumbnail($postid);
        ?>
		<div class="cd-contenuto">
			<div class="cd-slider-wrapper">
				<ul class="cd-slider">
					<li class="selected"><?php 
        echo $featuredImage;
        ?></li>
				</ul> <!-- cd-slider -->

				<ul class="cd-slider-navigation">
					<li><a class="cd-next" href="#0">Prev</a></li>
					<li><a class="cd-prev" href="#0">Next</a></li>
				</ul> <!-- cd-slider-navigation -->
			</div> <!-- cd-slider-wrapper -->

			<div class="cd-item-info">
		<?php 
        self::get_template_for_ajax($template_id, $postid);
        ?>
			</div> <!-- cd-item-info -->
			<a href="#0" class="cd-close">Close</a>
		</div>
		<?php 
    }
    public static function get_template_for_ajax($t_id, $p_id, $type = 'post')
    {
        if ($t_id > 0) {
            echo do_shortcode('[dce-elementor-template id="' . $t_id . '" ' . $type . '_id="' . $p_id . '" inlinecss="true" ajax="true"]');
        } else {
            // Check if the template is created with Elementor
            $elementor = get_post_meta($p_id, '_elementor_edit_mode', \true);
            if ($elementor) {
                echo do_shortcode('[dce-elementor-template id="' . $p_id . '" ' . $type . '_id="' . $p_id . '" inlinecss="true" ajax="true"]');
            } else {
                $post_template = get_post($p_id);
                $contenuto = $post_template->post_content;
                echo $contenuto;
            }
        }
    }
    public function dce_elementor_template()
    {
        if (isset($_POST['template_id']) && \is_numeric($_POST['template_id'])) {
            $template_id = sanitize_text_field($_POST['template_id']);
            $obj_id = 0;
            $type = null;
            if (!empty($_POST['post_id']) && \is_numeric($_POST['post_id'])) {
                $obj_id = \intval($_POST['post_id']);
            }
            if (!empty($_POST['user_id']) && \is_numeric($_POST['user_id'])) {
                $obj_id = \intval($_POST['user_id']);
            }
            if (!empty($_POST['term_id']) && \is_numeric($_POST['term_id'])) {
                $obj_id = \intval($_POST['term_id']);
            }
            if (!empty($_POST['object'])) {
                $type = sanitize_text_field($_POST['object']);
            }
            if ($type == 'post' && !$obj_id && !empty($_SERVER['HTTP_REFERER'])) {
                $obj_id = url_to_postid(sanitize_text_field($_SERVER['HTTP_REFERER']));
            }
            self::get_template_for_ajax($template_id, $obj_id, $type);
        }
        wp_die();
    }
}
