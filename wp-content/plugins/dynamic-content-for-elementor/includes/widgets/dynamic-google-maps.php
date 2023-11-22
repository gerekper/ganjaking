<?php

namespace DynamicContentForElementor\Widgets;

use Elementor\Repeater;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
class DynamicGoogleMaps extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    public function get_script_depends()
    {
        return ['dce-google-maps'];
    }
    public function get_style_depends()
    {
        return ['dce-google-maps'];
    }
    /**
     * Run Once
     *
     * @return void
     */
    public function run_once()
    {
        parent::run_once();
        $save_guard = \DynamicContentForElementor\Plugin::instance()->save_guard;
        $save_guard->register_unsafe_control($this->get_type(), 'infoWindow_query_html');
    }
    protected $positions = [];
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $taxonomies = Helper::get_taxonomies();
        $this->start_controls_section('section_map', ['label' => $this->get_title()]);
        if (!get_option('dce_google_maps_api')) {
            $this->add_control('api_notice', ['type' => Controls_Manager::RAW_HTML, 'raw' => __('In order to use this feature you should set Google Maps API, with Geocoding API enabled, on Integrations section', 'dynamic-content-for-elementor'), 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning']);
        }
        $this->add_control('map_data_type', ['label' => __('Data Type', 'dynamic-content-for-elementor'), 'type' => 'images_selector', 'toggle' => \false, 'type_selector' => 'icon', 'columns_grid' => 5, 'default' => 'address', 'options' => ['address' => ['title' => __('Address', 'dynamic-content-for-elementor'), 'return_val' => 'val', 'icon' => 'fa fa-map-marker-alt'], 'latlng' => ['title' => __('Latitude and longitude', 'dynamic-content-for-elementor'), 'return_val' => 'val', 'icon' => 'fa fa-globe-europe'], 'acfmap' => ['title' => __('ACF Google Map Field', 'dynamic-content-for-elementor'), 'return_val' => 'val', 'icon' => 'fa fa-map'], 'metabox_google_maps' => ['title' => __('Meta Box Google Map Field', 'dynamic-content-for-elementor'), 'return_val' => 'val', 'icon' => 'icon-dce-metabox-google-maps']], 'frontend_available' => \true]);
        $this->add_control('address', ['label' => __('Address', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => 'Venice, Italy', 'label_block' => \true, 'condition' => ['map_data_type' => 'address']]);
        $this->add_control('latitudine', ['label' => __('Latitude', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '45.4371908', 'condition' => ['map_data_type' => 'latlng']]);
        $this->add_control('longitudine', ['label' => __('Longitude', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '12.3345898', 'condition' => ['map_data_type' => 'latlng']]);
        if (Helper::is_acf_active()) {
            $this->add_control('acf_mapfield', ['label' => __('ACF Google Map field', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Select the field...', 'dynamic-content-for-elementor'), 'label_block' => \true, 'dynamic' => ['active' => \false], 'query_type' => 'acf', 'object_type' => 'google_map', 'frontend_available' => \true, 'condition' => ['map_data_type' => 'acfmap']]);
        } else {
            $this->add_control('acf_notice', ['type' => Controls_Manager::RAW_HTML, 'raw' => __('In order to use this feature you need Advanced Custom Fields.', 'dynamic-content-for-elementor'), 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning', 'condition' => ['map_data_type' => 'acfmap']]);
        }
        if (Helper::is_metabox_active()) {
            $this->add_control('metabox_google_maps_field', ['label' => __('Meta Box Google Maps field', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Select the field...', 'dynamic-content-for-elementor'), 'label_block' => \true, 'dynamic' => ['active' => \false], 'query_type' => 'metabox', 'object_type' => 'map', 'frontend_available' => \true, 'condition' => ['map_data_type' => 'metabox_google_maps']]);
        } else {
            $this->add_control('metabox_notice', ['type' => Controls_Manager::RAW_HTML, 'raw' => __('In order to use this feature you need Meta Box.', 'dynamic-content-for-elementor'), 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning', 'condition' => ['map_data_type' => 'metabox_google_maps']]);
        }
        $this->end_controls_section();
        $this->start_controls_section('section_query', ['label' => __('Multiple Locations Query', 'dynamic-content-for-elementor'), 'condition' => ['map_data_type' => ['acfmap', 'metabox_google_maps']]]);
        $this->add_control('use_query', ['label' => __('Multiple Locations Query', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true, 'condition' => ['map_data_type' => ['acfmap', 'metabox_google_maps']]]);
        $query_types_for_acf = [];
        if (Helper::is_acf_active() || Helper::is_acfpro_active()) {
            $query_types_for_acf = ['acf_relations' => ['title' => 'ACF Relationship', 'return_val' => 'val', 'icon' => 'fa fa-american-sign-language-interpreting'], 'acf_repeater' => ['title' => 'ACF Repeater', 'return_val' => 'val', 'icon' => 'fa fa-ellipsis-v']];
        }
        $this->add_control('query_type', ['label' => __('Query Type', 'dynamic-content-for-elementor'), 'type' => 'images_selector', 'toggle' => \false, 'type_selector' => 'icon', 'columns_grid' => 5, 'options' => ['get_cpt' => ['title' => __('Custom Post Type', 'dynamic-content-for-elementor'), 'return_val' => 'val', 'icon' => 'eicon-post-content'], 'dynamic_mode' => ['title' => __('Dynamic - Current Query', 'dynamic-content-for-elementor'), 'return_val' => 'val', 'icon' => 'fa fa-cogs'], 'search_filter' => ['title' => 'Search & Filter Pro', 'return_val' => 'val', 'icon' => 'icon-dyn-search-filter'], 'specific_posts' => ['title' => __('From Specific Posts', 'dynamic-content-for-elementor'), 'return_val' => 'val', 'icon' => 'fa fa-list-ul']] + $query_types_for_acf, 'default' => 'get_cpt', 'condition' => ['use_query' => 'yes', 'map_data_type' => ['acfmap', 'metabox_google_maps']]]);
        $this->add_control('ignore_pagination', ['label' => __('Ignore pagination', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'no', 'frontend_available' => \true, 'condition' => ['map_data_type' => ['acfmap', 'metabox_google_maps'], 'use_query' => 'yes', 'query_type' => ['dynamic_mode']]]);
        if (Helper::is_searchandfilterpro_active()) {
            if (\version_compare(SEARCH_FILTER_VERSION, '2.5.5', '>=')) {
                $this->add_control('search_filter_id', ['label' => __('Filter', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'label_block' => \true, 'placeholder' => __('Select the filter', 'dynamic-content-for-elementor'), 'query_type' => 'posts', 'object_type' => 'search-filter-widget', 'condition' => ['query_type' => 'search_filter', 'map_data_type' => ['acfmap', 'metabox_google_maps']]]);
            } else {
                $this->add_control('search_filter_notice', ['type' => Controls_Manager::RAW_HTML, 'raw' => __('In order to use this feature you need Search & Filter Pro version >= 2.5.5', 'dynamic-content-for-elementor'), 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning', 'condition' => ['query_type' => 'search_filter', 'map_data_type' => ['acfmap', 'metabox_google_maps']]]);
            }
        } else {
            $this->add_control('search_filter_notice', ['type' => Controls_Manager::RAW_HTML, 'raw' => __('Combine the power of Search & Filter Pro front end filters with Dynamic Google Maps! Note: In order to use this feature you need install Search & Filter Pro. Search & Filter Pro is a premium product - you can <a href="https://searchandfilter.com">get it here</a>.', 'dynamic-content-for-elementor'), 'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning', 'condition' => ['query_type' => 'search_filter', 'map_data_type' => ['acfmap', 'metabox_google_maps']]]);
        }
        $this->add_control('post_type', ['label' => __('Post Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT2, 'options' => Helper::get_public_post_types(), 'multiple' => \true, 'label_block' => \true, 'default' => 'post', 'condition' => ['use_query' => 'yes', 'map_data_type' => ['acfmap', 'metabox_google_maps'], 'query_type' => ['get_cpt']]]);
        $this->add_control('taxonomy', ['label' => __('Taxonomy', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['' => __('None', 'dynamic-content-for-elementor')] + get_taxonomies(['public' => \true]), 'condition' => ['use_query' => 'yes', 'map_data_type' => ['acfmap', 'metabox_google_maps'], 'query_type' => ['get_cpt']]]);
        $this->add_control('category', ['label' => __('Terms ID', 'dynamic-content-for-elementor'), 'description' => __('Category IDs separate by comma', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HIDDEN, 'condition' => ['use_query' => 'yes', 'map_data_type' => ['acfmap', 'metabox_google_maps'], 'query_type' => ['get_cpt']]]);
        $this->add_control('terms_current_post', ['label' => __('Dynamic Current Post Terms', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => __('Filter results by taxonomy terms associated to the current post', 'dynamic-content-for-elementor'), 'condition' => ['taxonomy!' => '', 'query_type' => ['get_cpt', 'dynamic_mode'], 'use_query' => 'yes', 'map_data_type' => ['acfmap', 'metabox_google_maps']]]);
        foreach ($taxonomies as $tkey => $atax) {
            if ($tkey) {
                $this->add_control('terms_' . $tkey, ['label' => __('Terms', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT2, 'options' => ['' => __('All', 'dynamic-content-for-elementor')] + \DynamicContentForElementor\Helper::get_taxonomy_terms($tkey), 'description' => __('Filter results by selected taxonomy terms', 'dynamic-content-for-elementor'), 'multiple' => \true, 'label_block' => \true, 'condition' => ['use_query' => 'yes', 'query_type' => ['get_cpt', 'dynamic_mode'], 'taxonomy' => $tkey, 'terms_current_post' => '', 'map_data_type' => ['acfmap', 'metabox_google_maps']], 'render_type' => 'template']);
            }
        }
        $this->add_control('acf_relationship', ['label' => __('ACF Relationship field', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Select the field...', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'acf', 'object_type' => 'relationship', 'condition' => ['use_query' => 'yes', 'query_type' => 'acf_relations', 'map_data_type' => ['acfmap', 'metabox_google_maps']]]);
        $this->add_control('acf_repeater', ['label' => __('ACF Repeater field', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Select the field...', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'acf', 'object_type' => 'repeater', 'condition' => ['use_query' => 'yes', 'query_type' => 'acf_repeater', 'map_data_type' => ['acfmap', 'metabox_google_maps']]]);
        $this->add_control('specific_pages', ['label' => __('Posts', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Post Title', 'dynamic-content-for-elementor'), 'query_type' => 'posts', 'multiple' => \true, 'label_block' => \true, 'condition' => ['use_query' => 'yes', 'query_type' => 'specific_posts', 'map_data_type' => ['acfmap', 'metabox_google_maps']]]);
        $this->end_controls_section();
        $this->start_controls_section('section_fallback', ['label' => __('No Results Behaviour', 'dynamic-content-for-elementor'), 'condition' => ['query_type' => 'search_filter', 'map_data_type' => ['acfmap', 'metabox_google_maps']]]);
        $this->add_control('fallback_type', ['label' => __('Content type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['text' => ['title' => __('Text', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'template' => ['title' => __('Template', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-th-large']], 'toggle' => \false, 'default' => 'text']);
        $this->add_control('fallback_template', ['label' => __('Render Template', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Template Name', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'object_type' => 'elementor_library', 'condition' => ['fallback_type' => 'template']]);
        $this->add_control('fallback_text', ['label' => __('Text Fallback', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::WYSIWYG, 'default' => __('No positions found.', 'dynamic-content-for-elementor'), 'description' => __('Type here your content, you can use HTML and Tokens.', 'dynamic-content-for-elementor'), 'condition' => ['fallback_type' => 'text']]);
        $this->end_controls_section();
        $this->start_controls_section('section_maps_controlling', ['label' => __('Controlling', 'dynamic-content-for-elementor')]);
        $this->add_control('geolocation', ['label' => __('Geolocation', 'dynamic-content-for-elementor'), 'description' => __('Display the geographic location of the user on the map, using browser\'s HTML5 Geolocation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'no', 'frontend_available' => \true]);
        $this->add_control('geolocation_button_text', ['label' => __('Text for Geolocation button', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => __('Pan to Current Location', 'dynamic-content-for-elementor'), 'label_block' => 'true', 'frontend_available' => \true, 'condition' => ['geolocation' => 'yes']]);
        $this->add_control('geolocation_change_zoom', ['label' => __('Change Zoom after Geolocation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true, 'condition' => ['geolocation' => 'yes']]);
        $this->add_control('geolocation_zoom', ['label' => __('Zoom Level after Geolocation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 10], 'range' => ['px' => ['min' => 1, 'max' => 20]], 'frontend_available' => \true, 'condition' => ['geolocation' => 'yes', 'geolocation_change_zoom' => 'yes']]);
        $this->add_control('zoom_heading', ['label' => __('Zoom', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before']);
        $this->add_control('auto_zoom', ['label' => __('Force automatic Zoom', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true, 'separator' => 'before', 'condition' => ['map_data_type' => ['acfmap', 'metabox_google_maps'], 'acf_mapfield!' => ['', null]]]);
        $this->add_control('zoom', ['label' => __('Zoom Level', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 10], 'range' => ['px' => ['min' => 1, 'max' => 20]], 'conditions' => ['relation' => 'or', 'terms' => [['terms' => [['name' => 'map_data_type', 'operator' => 'in', 'value' => ['latlng', 'address']]]], ['terms' => [['name' => 'map_data_type', 'operator' => 'in', 'value' => ['acfmap', 'metabox_google_maps']], ['name' => 'auto_zoom', 'operator' => '==', 'value' => '']]]]]]);
        $this->add_control('zoom_custom', ['label' => __('Set a minimum and maximum zoom level', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true]);
        $this->add_control('zoom_minimum', ['label' => __('Zoom Minimum', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'frontend_available' => \true, 'default' => ['size' => 0], 'range' => ['px' => ['min' => 1, 'max' => 20]], 'condition' => ['zoom_custom!' => '']]);
        $this->add_control('zoom_maximum', ['label' => __('Zoom Maximum', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'frontend_available' => \true, 'default' => ['size' => 20], 'range' => ['px' => ['min' => 1, 'max' => 20]], 'condition' => ['zoom_custom!' => '']]);
        $this->add_control('prevent_scroll', ['label' => __('Scroll', 'dynamic-content-for-elementor'), 'description' => __('When a user scrolls a page that contains a map, the scrolling action can unintentionally cause the map to zoom. This behavior can be controlled using this option.', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'render_type' => 'template', 'separator' => 'before', 'frontend_available' => \true]);
        $this->end_controls_section();
        $this->start_controls_section('section_mapStyles', ['label' => __('Map Type', 'dynamic-content-for-elementor')]);
        $this->add_control('map_type', ['label' => __('Map Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'roadmap', 'options' => ['roadmap' => __('Roadmap', 'dynamic-content-for-elementor'), 'satellite' => __('Satellite', 'dynamic-content-for-elementor'), 'hybrid' => __('Hybrid', 'dynamic-content-for-elementor'), 'terrain' => __('Terrain', 'dynamic-content-for-elementor')], 'frontend_available' => \true]);
        $this->add_control('style_select', ['label' => __('Style', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['' => __('None', 'dynamic-content-for-elementor'), 'custom' => __('Custom', 'dynamic-content-for-elementor'), 'prestyle' => __('Snazzy Style', 'dynamic-content-for-elementor')], 'frontend_available' => \true, 'condition' => ['map_type' => 'roadmap']]);
        $this->add_control('snazzy_select', ['label' => 'Snazzy Maps', 'type' => Controls_Manager::SELECT2, 'options' => $this->get_snazzy_maps_list(), 'frontend_available' => \true, 'condition' => ['map_type' => 'roadmap', 'style_select' => 'prestyle']]);
        $this->add_control('style_map', ['label' => __('Snazzy JSON Style Map', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXTAREA, 'description' => __('To better manage the graphic styles of the map go to: <a href="https://snazzymaps.com/" target="_blank">snazzymaps.com</a>', 'dynamic-content-for-elementor'), 'frontend_available' => \true, 'condition' => ['map_type' => 'roadmap', 'style_select' => 'custom']]);
        $this->end_controls_section();
        $this->start_controls_section('section_mapInfoWIndow', ['label' => __('InfoWindow', 'dynamic-content-for-elementor')]);
        $this->add_control('enable_infoWindow', ['label' => __('InfoWindow', 'dynamic-content-for-elementor'), 'description' => __('The InfoWindow displays content in a popup window above the location', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'separator' => 'before', 'render_type' => 'template', 'frontend_available' => \true]);
        $this->add_control('infoWindow_click_to_post', ['label' => __('Link to post', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true, 'condition' => ['map_data_type' => ['acfmap', 'metabox_google_maps'], 'acf_mapfield!' => ['', null], 'use_query!' => '', 'enable_infoWindow' => 'yes']]);
        $this->add_control('infoWindow_click_to_url', ['label' => __('Link to URL', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true, 'condition' => ['map_data_type!' => ['acfmap', 'metabox_google_maps'], 'enable_infoWindow' => 'yes']]);
        $this->add_control('infoWindow_url', ['label' => __('URL', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::URL, 'frontend_available' => \true, 'condition' => ['map_data_type!' => ['acfmap', 'metabox_google_maps'], 'enable_infoWindow' => 'yes', 'infoWindow_click_to_url!' => '']]);
        $this->add_control('custom_infoWindow_render', ['label' => __('Render', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['simple' => ['title' => __('Simple mode', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'html' => ['title' => __('HTML & Tokens', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-code'], 'template' => ['title' => __('Template', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-th-large']], 'toggle' => \false, 'default' => 'simple', 'condition' => ['infoWindow_click_to_post' => '', 'enable_infoWindow' => 'yes']]);
        $this->add_control('infoWindow_template', ['label' => __('Template', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Template Name', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'object_type' => 'elementor_library', 'condition' => ['infoWindow_click_to_post' => '', 'custom_infoWindow_render' => 'template', 'enable_infoWindow' => 'yes']]);
        $this->add_control('infoWindow_query_html', ['label' => __('HTML & Tokens', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CODE, 'separator' => 'before', 'default' => '[post:ID|get_the_post_thumbnail(thumbnail)]<h4>[post:title|esc_html]</h4>[post:excerpt]<br><a href="[post:permalink]">' . __('Read more', 'dynamic-content-for-elementor') . '</a>', 'condition' => ['infoWindow_click_to_post' => '', 'custom_infoWindow_render' => 'html', 'enable_infoWindow' => 'yes']]);
        $this->add_control('custom_infoWindow_wysiwig', ['label' => __('Custom Text', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::WYSIWYG, 'frontend_available' => \true, 'label_block' => \true, 'condition' => ['custom_infoWindow_render' => 'simple', 'enable_infoWindow' => 'yes']]);
        $this->add_control('acf_repeater_iwimage', ['label' => __('ACF Image for Content Area', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Select the field...', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'acf', 'object_type' => 'image', 'separator' => 'before', 'condition' => ['use_query' => 'yes', 'query_type' => 'acf_repeater', 'enable_infoWindow!' => '', 'custom_infoWindow_render' => 'simple', 'map_data_type' => 'acfmap', 'enable_infoWindow' => 'yes']]);
        $this->add_control('acf_repeater_iwtitle', ['label' => __('ACF Field for Title', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Select the field...', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'acf', 'object_type' => 'text', 'condition' => ['use_query' => 'yes', 'query_type' => 'acf_repeater', 'enable_infoWindow!' => '', 'custom_infoWindow_render' => 'simple', 'map_data_type' => 'acfmap']]);
        $this->add_control('acf_repeater_iwcontent', ['label' => __('ACF Field for Content', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Select the field...', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'acf', 'object_type' => 'textarea,wysiwyg', 'condition' => ['use_query' => 'yes', 'query_type' => 'acf_repeater', 'enable_infoWindow!' => '', 'custom_infoWindow_render' => 'simple', 'map_data_type' => 'acfmap']]);
        $this->add_control('acf_repeater_iwlink', ['label' => __('ACF URL Field for Read More Link', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Select the field...', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'acf', 'object_type' => 'url,link,page_link', 'condition' => ['use_query' => 'yes', 'query_type' => 'acf_repeater', 'enable_infoWindow!' => '', 'custom_infoWindow_render' => 'simple', 'map_data_type' => 'acfmap']]);
        $this->end_controls_section();
        $this->start_controls_section('section_mapMarker', ['label' => __('Marker', 'dynamic-content-for-elementor')]);
        if (Helper::is_acf_active() || Helper::is_acfpro_active()) {
            $this->add_control('acf_markerfield', ['label' => __('Marker from an ACF Image field', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Select the field...', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'acf', 'object_type' => 'image']);
        }
        $this->add_control('imageMarker', ['label' => __('Marker Image', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::MEDIA, 'default' => ['url' => ''], 'frontend_available' => \true, 'condition' => ['acf_markerfield' => ['', null]]]);
        $this->add_control('marker_width', ['label' => __('Force Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'frontend_available' => 'true']);
        $this->add_control('marker_height', ['label' => __('Force Height', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'frontend_available' => 'true']);
        $this->end_controls_section();
        $this->start_controls_section('section_mapControls', ['label' => __('Controls', 'dynamic-content-for-elementor')]);
        $this->add_control('maptypecontrol', ['label' => __('Map Type Control', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true]);
        $this->add_control('pancontrol', ['label' => __('Pan Control', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true]);
        $this->add_control('rotatecontrol', ['label' => __('Rotate Control', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true]);
        $this->add_control('scalecontrol', ['label' => __('Scale Control', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true]);
        $this->add_control('streetviewcontrol', ['label' => __('Street View Control', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true]);
        $this->add_control('zoomcontrol', ['label' => __('Zoom Control', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true]);
        $this->add_control('fullscreenControl', ['label' => __('Full Screen Control', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true]);
        $this->add_control('markerclustererControl', ['label' => __('Marker Clusterer', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true]);
        $this->end_controls_section();
        $this->start_controls_section('section_source', ['label' => __('Source', 'dynamic-content-for-elementor')]);
        $this->add_control('data_source', ['label' => __('Source', 'dynamic-content-for-elementor'), 'description' => __('Select the data source', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'label_on' => __('Same', 'dynamic-content-for-elementor'), 'label_off' => __('Other', 'dynamic-content-for-elementor'), 'return_value' => 'yes']);
        $this->add_control('other_post_source', ['label' => __('Select from other source post', 'dynamic-content-for-elementor'), 'type' => 'ooo_query', 'placeholder' => __('Post Title', 'dynamic-content-for-elementor'), 'label_block' => \true, 'query_type' => 'posts', 'condition' => ['data_source' => '']]);
        $this->end_controls_section();
        $this->start_controls_section('section_style', ['label' => __('Map', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_responsive_control('height', ['label' => __('Height', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['unit' => 'px', 'size' => 300], 'range' => ['px' => ['min' => 40, 'max' => 1440]], 'selectors' => ['{{WRAPPER}} .map' => 'height: {{SIZE}}{{UNIT}};']]);
        $this->end_controls_section();
        $this->start_controls_section('section_infowindow_style', ['label' => __('InfoWindow', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_responsive_control('infoWindow_align', ['label' => __('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right'], 'justify' => ['title' => __('Justified', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-justify']], 'prefix_class' => 'align-dce-', 'selectors' => ['{{WRAPPER}} .gm-style .gm-style-iw-c' => 'text-align: {{VALUE}};'], 'condition' => ['infoWindow_click_to_post' => '']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'infowindow_typography', 'label' => __('Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .gm-style .gm-style-iw-c', 'condition' => ['infoWindow_click_to_post' => '', 'use_query' => '']]);
        $this->add_control('infoWindow_textColor', ['label' => __('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .gm-style .gm-style-iw-c, {{WRAPPER}} .gm-style .gm-style-iw-t::after' => 'color: {{VALUE}};'], 'condition' => ['infoWindow_click_to_post' => '', 'use_query' => '']]);
        $this->add_control('infoWindow_heading_style_image', ['label' => __('Image', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['infoWindow_click_to_post' => '', 'use_query!' => '', 'custom_infoWindow_render' => 'simple']]);
        $this->add_control('infowindow_query_show_image', ['label' => __('Show Image', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true, 'condition' => ['infoWindow_click_to_post' => '', 'use_query!' => '', 'custom_infoWindow_render' => 'simple']]);
        $this->add_control('infowindow_query_extendimage', ['label' => __('Background Mode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'frontend_available' => \true, 'condition' => ['infoWindow_click_to_post' => '', 'use_query!' => '', 'custom_infoWindow_render' => 'simple']]);
        $this->add_responsive_control('infowindow_query_bgimage_height', ['label' => __('Height', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['unit' => 'px', 'size' => 100], 'sizes_unit' => ['px', '%'], 'range' => ['px' => ['min' => 10, 'max' => 360], '%' => ['min' => 1, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-image-bg' => 'height: {{SIZE}}{{UNIT}};'], 'condition' => ['infoWindow_click_to_post' => '', 'use_query!' => '', 'infowindow_query_extendimage!' => '', 'custom_infoWindow_render' => 'simple']]);
        $this->add_control('infoWindow_query_image_float', ['label' => __('Float', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['infoWindow_click_to_post' => '', 'use_query!' => '', 'infowindow_query_show_image!' => '', 'custom_infoWindow_render' => 'simple'], 'selectors' => ['{{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-image, {{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-textzone' => 'float: left;']]);
        $this->add_responsive_control('infowindow_query_image_size', ['label' => __('Distribution Size (%)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 50], 'size_units' => ['%'], 'range' => ['%' => ['min' => 10, 'max' => 100]], 'selectors' => ['{{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-image' => 'width: {{SIZE}}%;', '{{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-textzone' => 'width: calc( 100% - {{SIZE}}% );'], 'condition' => ['infoWindow_click_to_post' => '', 'use_query!' => '', 'infoWindow_query_image_float!' => '', 'infowindow_query_show_image!' => '', 'custom_infoWindow_render' => 'simple']]);
        $this->add_control('infoWindow_heading_style_title', ['label' => __('Title', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['infoWindow_click_to_post' => '', 'use_query!' => '', 'custom_infoWindow_render' => 'simple']]);
        $this->add_control('infowindow_query_show_title', ['label' => __('Show Title', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true, 'condition' => ['infoWindow_click_to_post' => '', 'use_query!' => '', 'custom_infoWindow_render' => 'simple']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'infowindow_query_typography_title', 'label' => __('Title Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-title', 'separator' => 'before', 'condition' => ['infoWindow_click_to_post' => '', 'use_query!' => '', 'infowindow_query_show_title!' => '', 'custom_infoWindow_render' => 'simple']]);
        $this->add_control('infowindow_query_color_title', ['label' => __('Title Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-title' => 'color: {{VALUE}};'], 'condition' => ['infoWindow_click_to_post' => '', 'use_query!' => '', 'infowindow_query_show_title!' => '', 'custom_infoWindow_render' => 'simple']]);
        $this->add_control('infowindow_query_bgcolor_title', ['label' => __('Title BackgroundColor', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-title' => 'background-color: {{VALUE}};'], 'condition' => ['infoWindow_click_to_post' => '', 'use_query!' => '', 'infowindow_query_show_title!' => '', 'custom_infoWindow_render' => 'simple']]);
        $this->add_control('infowindow_query_padding_title', ['label' => __('Title Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em'], 'selectors' => ['{{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'condition' => ['infoWindow_click_to_post' => '', 'use_query!' => '', 'infowindow_query_show_title!' => '', 'custom_infoWindow_render' => 'simple']]);
        // --------- CONTENT
        $this->add_control('infoWindow_heading_style_content', ['label' => __('Content', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['infoWindow_click_to_post' => '', 'use_query!' => '', 'custom_infoWindow_render' => 'simple']]);
        $this->add_control('infowindow_query_show_content', ['label' => __('Show Content', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true, 'condition' => ['infoWindow_click_to_post' => '', 'use_query!' => '', 'custom_infoWindow_render' => 'simple']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'infowindow_query_typography_content', 'label' => __('Content Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-content', 'separator' => 'before', 'condition' => ['infoWindow_click_to_post' => '', 'use_query!' => '', 'infowindow_query_show_content!' => '', 'custom_infoWindow_render' => 'simple']]);
        $this->add_control('infowindow_query_color_content', ['label' => __('Content Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-content' => 'color: {{VALUE}};'], 'condition' => ['infoWindow_click_to_post' => '', 'use_query!' => '', 'infowindow_query_show_content!' => '', 'custom_infoWindow_render' => 'simple']]);
        $this->add_control('infowindow_query_padding_content', ['label' => __('Content Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em'], 'selectors' => ['{{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'condition' => ['infoWindow_click_to_post' => '', 'use_query!' => '', 'infowindow_query_show_content!' => '', 'custom_infoWindow_render' => 'simple']]);
        // --------- READMORE
        $this->add_control('infoWindow_heading_style_readmore', ['label' => __('Read more', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['infoWindow_click_to_post' => '', 'use_query!' => '', 'custom_infoWindow_render' => 'simple']]);
        $this->add_control('infowindow_query_show_readmore', ['label' => __('Show Readmore', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'frontend_available' => \true, 'condition' => ['infoWindow_click_to_post' => '', 'use_query!' => '', 'custom_infoWindow_render' => 'simple']]);
        $this->add_control('infowindow_query_readmore_text', ['label' => __('Text button', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => __('Read More', 'dynamic-content-for-elementor'), 'condition' => ['infowindow_query_show_readmore!' => '', 'infoWindow_click_to_post' => '', 'use_query!' => '', 'custom_infoWindow_render' => 'simple']]);
        $this->start_controls_tabs('readmore_colors');
        $this->start_controls_tab('infowindow_query_readmore_colors_normal', ['label' => __('Normal', 'dynamic-content-for-elementor'), 'condition' => ['infowindow_query_show_readmore!' => '', 'infoWindow_click_to_post' => '', 'use_query!' => '', 'custom_infoWindow_render' => 'simple']]);
        $this->add_control('infowindow_query_readmore_color', ['label' => __('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-readmore-btn' => 'color: {{VALUE}};'], 'condition' => ['infowindow_query_show_readmore!' => '', 'infoWindow_click_to_post' => '', 'use_query!' => '', 'custom_infoWindow_render' => 'simple']]);
        $this->add_control('infowindow_query_readmore_bgcolor', ['label' => __('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-readmore-btn' => 'background-color: {{VALUE}};'], 'condition' => ['infowindow_query_show_readmore!' => '', 'infoWindow_click_to_post' => '', 'use_query!' => '', 'custom_infoWindow_render' => 'simple']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'infowindow_query_readmore_border', 'label' => __('Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-readmore-btn', 'condition' => ['infowindow_query_show_readmore!' => '', 'infoWindow_click_to_post' => '', 'use_query!' => '', 'custom_infoWindow_render' => 'simple']]);
        $this->end_controls_tab();
        $this->start_controls_tab('infowindow_query_readmore_colors_hover', ['label' => __('Hover', 'dynamic-content-for-elementor'), 'condition' => ['infowindow_query_show_readmore!' => '', 'infoWindow_click_to_post' => '', 'use_query!' => '', 'custom_infoWindow_render' => 'simple']]);
        $this->add_control('infowindow_query_readmore_color_hover', ['label' => __('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-readmore-btn:hover' => 'color: {{VALUE}};'], 'condition' => ['infowindow_query_show_readmore!' => '', 'infoWindow_click_to_post' => '', 'use_query!' => '', 'custom_infoWindow_render' => 'simple']]);
        $this->add_control('infowindow_query_readmore_bgcolor_hover', ['label' => __('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-readmore-btn:hover' => 'background-color: {{VALUE}};'], 'condition' => ['infowindow_query_show_readmore!' => '', 'infoWindow_click_to_post' => '', 'use_query!' => '', 'custom_infoWindow_render' => 'simple']]);
        $this->add_control('infowindow_query_readmore_hover_border_color', ['label' => __('Border Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'condition' => ['infowindow_query_show_readmore!' => '', 'infowindow_query_readmore_border_border!' => '', 'infoWindow_click_to_post' => '', 'use_query!' => '', 'custom_infoWindow_render' => 'simple'], 'selectors' => ['{{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-readmore-btn:hover' => 'border-color: {{VALUE}};']]);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'infowindow_query_typography_readmore', 'label' => __('ReadMore Typography', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .dce-iw-readmore-btn', 'condition' => ['infowindow_query_show_readmore!' => '', 'infoWindow_click_to_post' => '', 'use_query!' => '', 'custom_infoWindow_render' => 'simple']]);
        $this->add_responsive_control('infowindow_query_readmore_align', ['label' => __('ReadMore Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'toggle' => \true, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'default' => 'left', 'selectors' => ['{{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-readmore-wrapper' => 'text-align: {{VALUE}};'], 'condition' => ['infowindow_query_show_readmore!' => '', 'infoWindow_click_to_post' => '', 'use_query!' => '', 'custom_infoWindow_render' => 'simple']]);
        $this->add_responsive_control('infowindow_query_readmore_padding', ['label' => __('ReadMore Padding', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-readmore-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'condition' => ['infowindow_query_show_readmore!' => '', 'infoWindow_click_to_post' => '', 'use_query!' => '', 'custom_infoWindow_render' => 'simple']]);
        $this->add_responsive_control('infowindow_query_readmore_margin', ['label' => __('ReadMore Margin', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-readmore-btn' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'condition' => ['infowindow_query_show_readmore!' => '', 'infoWindow_click_to_post' => '', 'use_query!' => '', 'custom_infoWindow_render' => 'simple']]);
        $this->add_control('infowindow_query_readmore_border_radius', ['label' => __('ReadMore Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-readmore-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'condition' => ['infowindow_query_show_readmore!' => '', 'infoWindow_click_to_post' => '', 'use_query!' => '', 'custom_infoWindow_render' => 'simple']]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'infowindow_query_box_shadow_readmore', 'label' => __('ReadMore Box Shadow', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-readmore-btn', 'condition' => ['infowindow_query_show_readmore!' => '', 'infoWindow_click_to_post' => '', 'use_query!' => '', 'custom_infoWindow_render' => 'simple']]);
        // --------- PANEL
        $this->add_control('infoWindow_heading_style_panel', ['label' => __('Panel', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::HEADING, 'separator' => 'before', 'condition' => ['infoWindow_click_to_post' => '']]);
        $this->add_responsive_control('infoWindow_panel_maxwidth', ['label' => __('Max Width', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['unit' => 'px', 'size' => ''], 'range' => ['px' => ['min' => 40, 'max' => 1440]], 'condition' => ['infoWindow_click_to_post' => ''], 'frontend_available' => \true]);
        $this->add_control('infoWindow_bgColor', ['label' => __('Background Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .gm-style .gm-style-iw-c, {{WRAPPER}} .gm-style .gm-style-iw-t::after' => 'background: {{VALUE}} !important;'], 'condition' => ['infoWindow_click_to_post' => '']]);
        $this->add_group_control(Group_Control_Border::get_type(), ['name' => 'infoWindow_border', 'label' => __('Border', 'dynamic-content-for-elementor'), 'selector' => '{{WRAPPER}} .gm-style .gm-style-iw-c, {{WRAPPER}} .gm-style .gm-style-iw-t::after', 'condition' => ['infoWindow_click_to_post' => '']]);
        $this->add_control('infoWindow_padding', ['label' => __('Padding panel', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em'], 'default' => ['unit' => 'px'], 'selectors' => ['{{WRAPPER}} .gm-style .gm-style-iw-c' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'condition' => ['infoWindow_click_to_post' => '']]);
        $this->add_control('infoWindow_padding_wrap', ['label' => __('Padding wrapper', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', 'em'], 'default' => ['unit' => 'px'], 'selectors' => ['{{WRAPPER}} .gm-style .gm-style-iw-d' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'condition' => ['infoWindow_click_to_post' => '']]);
        $this->add_control('infoWindow_border_radius', ['label' => __('Border Radius', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => ['px', '%'], 'selectors' => ['{{WRAPPER}} .gm-style .gm-style-iw-c' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'], 'condition' => ['infoWindow_click_to_post' => '']]);
        $this->add_group_control(Group_Control_Box_Shadow::get_type(), ['name' => 'infoWindow_box_shadow', 'selector' => '{{WRAPPER}} .gm-style .gm-style-iw-c', 'condition' => ['infoWindow_click_to_post' => '']]);
        $this->end_controls_section();
    }
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        if ('acfmap' === $settings['map_data_type'] && empty($settings['acf_mapfield'])) {
            Helper::notice('', __('Select an ACF Google Map Field', 'dynamic-content-for-elementor'));
            return;
        }
        if ('metabox_google_maps' === $settings['map_data_type'] && empty($settings['metabox_google_maps_field'])) {
            Helper::notice('', __('Select a Meta Box Google Maps Field', 'dynamic-content-for-elementor'));
            return;
        }
        // Don't render if ACF is selected but ACF is not active
        if ('acfmap' === $settings['map_data_type'] && !(Helper::is_acf_active() || Helper::is_acfpro_active())) {
            return;
        }
        // Don't render if Meta Box Google Maps is selected but Meta Box is not active
        if ('metabox_google_maps' === $settings['map_data_type'] && !Helper::is_metabox_active()) {
            return;
        }
        $id_page = Helper::get_the_id($settings['other_post_source']);
        $is_repeater = \false;
        // Zoom
        if (isset($settings['zoom'])) {
            $zoom = $settings['zoom']['size'];
        } else {
            $zoom = 10;
        }
        $this->add_render_attribute('map', 'data-zoom', $zoom);
        // Image Marker
        $imageMarker = '';
        if (isset($settings['acf_markerfield'])) {
            $imageMarkerType = Helper::get_acf_field_post($settings['acf_markerfield']);
        }
        if (isset($imageMarkerType->post_parent)) {
            $field_settings = Helper::get_acf_field_settings($imageMarkerType->post_parent);
            if (isset($field_settings['type']) && 'repeater' === $field_settings['type']) {
                $is_repeater = \true;
            }
        }
        if (!$is_repeater) {
            if (isset($settings['acf_markerfield']) && $settings['acf_markerfield']) {
                $imageMarker = Helper::get_acf_field_value($settings['acf_markerfield'], $id_page);
            }
            if (\is_string($imageMarker)) {
                if (\is_numeric($imageMarker)) {
                    $imageSrc = wp_get_attachment_image_src($imageMarker, 'full');
                    $imageMarker = $imageSrc[0];
                } else {
                    $imageSrc = $imageMarker;
                }
            } elseif (\is_numeric($imageMarker)) {
                $imageSrc = wp_get_attachment_image_src($imageMarker, 'full');
                $imageMarker = $imageSrc[0];
            } elseif (\is_array($imageMarker)) {
                $imageSrc = wp_get_attachment_image_src($imageMarker['ID'], 'full');
                $imageMarker = $imageSrc[0];
            }
        }
        if ($imageMarker == '' && isset($settings['imageMarker'])) {
            $imageMarker = $settings['imageMarker']['url'];
        }
        // InfoWindow
        $infoWindow_str = '';
        if ($settings['enable_infoWindow']) {
            if ('html' === $settings['custom_infoWindow_render'] && !empty($settings['infoWindow_query_html'])) {
                $infoWindow_str = '<div class="dce-iw-textzone">' . Helper::get_dynamic_value($settings['infoWindow_query_html']) . '</div>';
                $infoWindow_str = \preg_replace("/\r|\n/", '', $infoWindow_str);
            } elseif ('template' === $settings['custom_infoWindow_render']) {
                $infoWindow_str = '<div class="dce-iw-textzone">' . \Elementor\Plugin::$instance->frontend->get_builder_content_for_display($settings['infoWindow_template'], \false) . '</div>';
            } elseif (!empty($settings['custom_infoWindow_wysiwig'])) {
                $infoWindow_str = '<div class="dce-iw-textzone">' . $settings['custom_infoWindow_wysiwig'] . '</div>';
            }
        }
        if (empty($settings['use_query'])) {
            // Static Address
            if ('address' === $settings['map_data_type']) {
                $map_data_type = $settings['map_data_type'];
                $address = $settings['address'];
                $lat = $settings['latitudine'];
                $lng = $settings['longitudine'];
                $link = $settings['infoWindow_url'] ?? '';
                self::add_position($address, $lat, $lng, $imageMarker, $link, $infoWindow_str);
            } elseif ('latlng' === $settings['map_data_type']) {
                $map_data_type = $settings['map_data_type'];
                $lat = $settings['latitudine'];
                $lng = $settings['longitudine'];
                $link = $settings['infoWindow_url'] ?? '';
                self::add_position('', $lat, $lng, $imageMarker, $link, $infoWindow_str);
            } elseif ('acfmap' === $settings['map_data_type']) {
                if (!empty($settings['acf_mapfield'])) {
                    // Single Address from ACF
                    $location = \get_field($settings['acf_mapfield'], $id_page);
                    if (!empty($location)) {
                        $address = $location['address'];
                        $lat = $location['lat'];
                        $lng = $location['lng'];
                        $link = $settings['infoWindow_url'] ?? '';
                        self::add_position($address, $lat, $lng, $imageMarker, $link, $infoWindow_str);
                    }
                }
            } elseif ('metabox_google_maps' === $settings['map_data_type']) {
                if (!empty($settings['metabox_google_maps_field'])) {
                    // Single Address from Meta Box
                    $location = rwmb_get_value($settings['metabox_google_maps_field'], [], $id_page);
                    if (!empty($location)) {
                        $lat = $location['latitude'];
                        $lng = $location['longitude'];
                        $link = $settings['infoWindow_url'] ?? '';
                        self::add_position('', $lat, $lng, $imageMarker, $link, $infoWindow_str);
                    }
                }
            }
        } else {
            // Query
            if ('specific_posts' === $settings['query_type']) {
                $args = array('post_type' => 'any', 'post__in' => $settings['specific_pages'], 'post_status' => 'publish', 'order_by' => 'post__in');
            } elseif ('dynamic_mode' === $settings['query_type']) {
                $args = $GLOBALS['wp_query']->query_vars;
                if ($settings['ignore_pagination'] && \array_key_exists('nopaging', $args)) {
                    $args['nopaging'] = \true;
                }
            } elseif ('search_filter' === $settings['query_type']) {
                if (Helper::is_searchandfilterpro_active() && \version_compare(SEARCH_FILTER_VERSION, '2.5.5', '>=')) {
                    $sfid = \intval($settings['search_filter_id']);
                    $args = ['search_filter_id' => $sfid];
                }
            } elseif ('acf_relations' === $settings['query_type']) {
                $relations_ids = Helper::get_acf_field_value($settings['acf_relationship'], $id_page, \false);
                if (!empty($relations_ids)) {
                    $args = array('post_type' => 'any', 'posts_per_page' => -1, 'post__in' => $relations_ids, 'post_status' => 'publish', 'orderby' => 'menu_order');
                } else {
                    $args = [];
                }
            } elseif ('acf_repeater' === $settings['query_type']) {
                if (have_rows($settings['acf_repeater'], $id_page)) {
                    $row_id = 0;
                    $sub_fields = Helper::get_acf_repeater_fields($settings['acf_repeater']);
                    $repeaters_ids = [];
                    if (!empty($sub_fields)) {
                        // loop through the rows of data
                        while (have_rows($settings['acf_repeater'], $id_page)) {
                            the_row();
                            $fieldsMap = get_sub_field($settings['acf_mapfield']);
                            $markerImg = get_sub_field($settings['acf_markerfield']);
                            $fieldIwTitle = get_sub_field($settings['acf_repeater_iwtitle']);
                            $fieldIwImage = get_sub_field($settings['acf_repeater_iwimage']);
                            $fieldIwContent = get_sub_field($settings['acf_repeater_iwcontent']);
                            $fieldIwLink = get_sub_field($settings['acf_repeater_iwlink']);
                            // marker
                            if (!empty($markerImg)) {
                                if (\is_string($markerImg)) {
                                    if (\is_numeric($markerImg)) {
                                        $imageSrc = wp_get_attachment_image_src($markerImg, 'full');
                                        $markerImg = $imageSrc[0];
                                    } else {
                                        $imageSrc = $markerImg;
                                    }
                                } elseif (\is_numeric($markerImg)) {
                                    $imageSrc = wp_get_attachment_image_src($markerImg, 'full');
                                    $markerImg = $imageSrc[0];
                                } elseif (\is_array($markerImg)) {
                                    $imageSrc = wp_get_attachment_image_src($markerImg['ID'], 'full');
                                    $markerImg = $imageSrc[0];
                                }
                            }
                            if (empty($markerImg)) {
                                $markerImg = $imageMarker;
                            }
                            foreach ($fieldsMap as $key => $acfvalue) {
                                $repeaters_ids[$row_id][$key] = $acfvalue;
                            }
                            $repeaters_ids[$row_id]['custommarker'] = $markerImg;
                            $repeaters_ids[$row_id]['iwtitle'] = $fieldIwTitle;
                            $repeaters_ids[$row_id]['iwimage'] = $fieldIwImage;
                            $repeaters_ids[$row_id]['iwcontent'] = $fieldIwContent;
                            $repeaters_ids[$row_id]['iwlink'] = $fieldIwLink;
                            $row_id++;
                        }
                    }
                }
            } elseif ($settings['query_type'] == 'get_cpt') {
                $terms_query = 'all';
                $taxquery = array();
                if ($settings['category'] != '') {
                    $terms_query = \explode(',', $settings['category']);
                }
                if ($settings['terms_current_post']) {
                    if (is_single()) {
                        $terms_list = wp_get_post_terms($id_page, $settings['taxonomy'], array('orderby' => 'name', 'order' => 'ASC', 'fields' => 'all', 'hide_empty' => \true));
                        if (!empty($terms_list)) {
                            $terms_query = array();
                            foreach ($terms_list as $akey => $aterm) {
                                if (!\in_array($aterm->term_id, $terms_query)) {
                                    $terms_query[] = $aterm->term_id;
                                }
                            }
                        }
                    }
                    if (is_archive()) {
                        if (is_tax()) {
                            $queried_object = get_queried_object();
                            $terms_query = array($queried_object->term_id);
                        }
                    }
                }
                if (isset($settings['terms_' . $settings['taxonomy']]) && !empty($settings['terms_' . $settings['taxonomy']])) {
                    $terms_query = $settings['terms_' . $settings['taxonomy']];
                    // add current post terms id
                    $dce_key = \array_search('dce_current_post_terms', $terms_query);
                    if ($dce_key !== \false) {
                        unset($terms_query[$dce_key]);
                        $terms_list = wp_get_post_terms($id_page, $settings['taxonomy'], array('orderby' => 'name', 'order' => 'ASC', 'fields' => 'all', 'hide_empty' => \true));
                        if (!empty($terms_list)) {
                            $terms_query = array();
                            foreach ($terms_list as $akey => $aterm) {
                                if (!\in_array($aterm->term_id, $terms_query)) {
                                    $terms_query[] = $aterm->term_id;
                                }
                            }
                        }
                    }
                }
                if ($settings['taxonomy'] != '') {
                    $taxquery = array(array('taxonomy' => $settings['taxonomy'], 'field' => 'id', 'terms' => $terms_query));
                }
                $args = array('post_type' => \DynamicContentForElementor\Helper::validate_post_type($settings['post_type']), 'posts_per_page' => -1, 'post_status' => 'publish', 'tax_query' => $taxquery);
            }
            if ('acf_repeater' === $settings['query_type']) {
                foreach ($repeaters_ids as $row_id => $row_fields) {
                    $indirizzo = $row_fields['address'];
                    $lat = $row_fields['lat'];
                    $lng = $row_fields['lng'];
                    if (!\is_numeric($lat) || !\is_numeric($lng)) {
                        continue;
                    }
                    $postlink = $row_fields['iwlink'];
                    $postInfowindow = '';
                    $marker_img = $row_fields['custommarker'];
                    // infowindow
                    if ('html' === $settings['custom_infoWindow_render']) {
                        $postInfowindow = '<div class="dce-iw-textzone">' . Helper::get_dynamic_value($settings['infoWindow_query_html']) . '</div>';
                        $postInfowindow = \preg_replace("/\r|\n/", '', $postInfowindow);
                    } elseif ('template' === $settings['custom_infoWindow_render']) {
                        $postInfowindow = '<div class="dce-iw-textzone">' . \Elementor\Plugin::$instance->frontend->get_builder_content_for_display($settings['infoWindow_template'], \false) . '</div>';
                    } else {
                        $postTitle = $row_fields['iwtitle'];
                        $postImage = '';
                        $postContent = '';
                        $postReadMore = '';
                        if ($settings['infowindow_query_show_title']) {
                            $postTitle = '<div class="dce-iw-title">' . $postTitle . '</div>';
                        }
                        // to do ... elaborare il size dell'immagine.........
                        if ($settings['infowindow_query_show_image']) {
                            if (!empty($row_fields['iwimage'])) {
                                $postImage = $row_fields['iwimage'];
                                if (\is_string($postImage)) {
                                    if (\is_numeric($postImage)) {
                                        $imageSrc = wp_get_attachment_image_src($postImage, 'full');
                                        $postImage = $imageSrc[0];
                                    } else {
                                        $imageSrc = $postImage;
                                    }
                                } elseif (\is_numeric($postImage)) {
                                    $imageSrc = wp_get_attachment_image_src($postImage, 'full');
                                    $postImage = $imageSrc[0];
                                } elseif (\is_array($postImage)) {
                                    $imageSrc = wp_get_attachment_image_src($postImage['ID'], 'full');
                                    $postImage = $imageSrc[0];
                                }
                                if ($settings['infowindow_query_extendimage']) {
                                    $postImage = '<div class="dce-iw-image dce-iw-image-bg" style="background: url(' . $postImage . ') no-repeat center center; background-size: cover;"></div>';
                                } else {
                                    $postImage = '<div class="dce-iw-image"><img src="' . $postImage . '" /></div>';
                                }
                            }
                        }
                        if ($settings['infowindow_query_show_content']) {
                            if (!empty($row_fields['iwcontent'])) {
                                $postContent = '<div class="dce-iw-content">' . $row_fields['iwcontent'] . '</div>';
                            }
                        }
                        if (!empty($settings['infowindow_query_show_readmore']) && !empty($postlink)) {
                            $postReadMore = '<div class="dce-iw-readmore-wrapper"><a href="' . $postlink . '" class="dce-iw-readmore-btn">' . $settings['infowindow_query_readmore_text'] . '</a></div>';
                        }
                        $postInfowindow = $postImage . '<div class="dce-iw-textzone">' . $postTitle . $postContent . $postReadMore . '</div>';
                    }
                    self::add_position($indirizzo, $lat, $lng, $marker_img, $postlink, $postInfowindow);
                }
            } else {
                // Query
                if (isset($args) && \count($args)) {
                    $p_query = new \WP_Query($args);
                    $counter = 0;
                    if ($p_query->have_posts()) {
                        global $wp_query;
                        $original_queried_object = get_queried_object();
                        $original_queried_object_id = get_queried_object_id();
                        while ($p_query->have_posts()) {
                            $p_query->the_post();
                            $id_page = get_the_ID();
                            $wp_query->queried_object_id = $id_page;
                            $wp_query->queried_object = get_post();
                            $map_field = Helper::get_acf_field_value($settings['acf_mapfield'], get_the_ID());
                            if (\is_array($map_field) && !empty($map_field)) {
                                $indirizzo = $map_field['address'];
                                $lat = $map_field['lat'];
                                $lng = $map_field['lng'];
                                if (!\is_numeric($lat) || !\is_numeric($lng)) {
                                    continue;
                                }
                                // link to post
                                $postlink = get_the_permalink($id_page);
                                $postlink = apply_filters('dynamicooo/google-maps/post-link', $postlink, $id_page);
                                // infowindow
                                $postInfowindow = '';
                                if ($settings['enable_infoWindow']) {
                                    if ('html' === $settings['custom_infoWindow_render']) {
                                        $postInfowindow = '<div class="dce-iw-textzone">' . Helper::get_dynamic_value($settings['infoWindow_query_html']) . '</div>';
                                        $postInfowindow = \preg_replace("/\r|\n/", '', $postInfowindow);
                                    } elseif ('template' === $settings['custom_infoWindow_render']) {
                                        $postInfowindow = '<div class="dce-iw-textzone">' . \Elementor\Plugin::$instance->frontend->get_builder_content_for_display($settings['infoWindow_template'], \false) . '</div>';
                                    } else {
                                        $postTitle = wp_kses_post(get_the_title($id_page));
                                        $postImage = '';
                                        $postContent = '';
                                        $postReadMore = '';
                                        if ($settings['infowindow_query_show_title']) {
                                            $postTitle = '<div class="dce-iw-title">' . wp_kses_post(get_the_title($id_page)) . '</div>';
                                        }
                                        // to do ... elaborare il size dell'immagine.........
                                        if ($settings['infowindow_query_show_image']) {
                                            if (!empty(get_the_post_thumbnail($id_page))) {
                                                if ($settings['infowindow_query_extendimage']) {
                                                    $postImage = '<div class="dce-iw-image dce-iw-image-bg" style="background: url(' . get_the_post_thumbnail_url($id_page) . ') no-repeat center center; background-size: cover;"></div>';
                                                } else {
                                                    $postImage = '<div class="dce-iw-image">' . get_the_post_thumbnail($id_page) . '</div>';
                                                }
                                            }
                                        }
                                        if ($settings['infowindow_query_show_content']) {
                                            $getpost = get_post($id_page);
                                            // specific post
                                            $the_content = apply_filters('the_content', $getpost->post_content);
                                            if (!empty($the_content)) {
                                                $postContent = '<div class="dce-iw-content">' . \preg_replace("/\r|\n/", '', $getpost->post_content) . '</div>';
                                            }
                                        }
                                        if (!empty($settings['infowindow_query_show_readmore']) && !empty($postlink)) {
                                            $postReadMore = '<div class="dce-iw-readmore-wrapper"><a href="' . $postlink . '" class="dce-iw-readmore-btn">' . $settings['infowindow_query_readmore_text'] . '</a></div>';
                                        }
                                        $postInfowindow = $postImage . '<div class="dce-iw-textzone">' . $postTitle . $postContent . $postReadMore . '</div>';
                                    }
                                }
                                // Marker
                                $marker_img = Helper::get_acf_field_value($settings['acf_markerfield'], $id_page);
                                if (\is_string($marker_img)) {
                                    if (\is_numeric($marker_img)) {
                                        $imageSrc = wp_get_attachment_image_src($marker_img, 'full');
                                        $marker_img = $imageSrc[0];
                                    } else {
                                        $imageSrc = $marker_img;
                                    }
                                } elseif (\is_numeric($marker_img)) {
                                    $imageSrc = wp_get_attachment_image_src($marker_img, 'full');
                                    $marker_img = $imageSrc[0];
                                } elseif (\is_array($marker_img)) {
                                    $imageSrc = wp_get_attachment_image_src($marker_img['ID'], 'full');
                                    $marker_img = $imageSrc[0];
                                }
                                if ($marker_img == '') {
                                    $marker_img = $imageMarker;
                                }
                                self::add_position($indirizzo, $lat, $lng, $marker_img, $postlink, $postInfowindow);
                            }
                        }
                        // Reset the post data to prevent conflicts with WP globals
                        $wp_query->queried_object = $original_queried_object;
                        $wp_query->queried_object_id = $original_queried_object_id;
                        wp_reset_postdata();
                    }
                }
            }
        }
        $this->add_search_filter_class();
        if (\count($this->get_positions())) {
            $this->render_map();
        } else {
            $this->render_fallback();
        }
    }
    /**
     * Render the map
     *
     * @return void
     */
    protected function render_map()
    {
        $this->add_render_attribute('map', 'class', 'map');
        $encoded_positions = wp_json_encode($this->get_positions());
        $posattr = '';
        if (\false !== $encoded_positions) {
            $posattr = ' data-positions="' . \htmlspecialchars($encoded_positions, \ENT_QUOTES, 'UTF-8') . '"';
        }
        $this->add_render_attribute('map', 'style', 'width: 100%');
        echo '<div ' . $this->get_render_attribute_string('map') . $posattr . '></div>';
    }
    /**
     * Get Snazzy Maps List
     *
     * @return array<string,string>
     */
    protected function get_snazzy_maps_list()
    {
        $snazzy_list = [];
        $snazzy_styles = \glob(DCE_PATH . 'assets/maps_style/*.json');
        if (!empty($snazzy_styles)) {
            foreach ($snazzy_styles as $key => $value) {
                $snazzy_name = \basename($value);
                $snazzy_name = \str_replace('.json', '', $snazzy_name);
                $snazzy_name = \str_replace('_', ' ', $snazzy_name);
                $snazzy_name = \ucfirst($snazzy_name);
                $snazzy_url = \str_replace('.json', '', $value);
                $snazzy_url = \str_replace(DCE_PATH, DCE_URL, $snazzy_url);
                $snazzy_list[$snazzy_url] = $snazzy_name;
            }
        }
        return $snazzy_list;
    }
    /**
     * Get Positions
     *
     * @return array<int,array<string,mixed>>
     */
    protected function get_positions()
    {
        return $this->positions;
    }
    /**
     * Add a position to positions list
     *
     * @param string $address
     * @param string $lat
     * @param string $lng
     * @param string $marker
     * @param string $link
     * @param string|null $infowindow
     * @return void
     */
    protected function add_position($address = '', $lat = '', $lng = '', $marker = '', $link = '', $infowindow = '')
    {
        if (!$address && (!$lat || !$lng)) {
            return;
        }
        $this->positions[] = ['address' => sanitize_text_field($address), 'lat' => \floatval($lat), 'lng' => \floatval($lng), 'marker' => $marker, 'link' => $link, 'infowindow' => $infowindow];
    }
    /**
     * Render the fallback when the map is without results. Useful when Search and Filter Pro is used
     * to return a message instead of a blank map
     *
     * @return void
     */
    protected function render_fallback()
    {
        $settings = $this->get_settings_for_display();
        if (!isset($settings['query_type']) || 'search_filter' !== $settings['query_type']) {
            return;
        }
        $fallback_type = $settings['fallback_type'];
        $fallback_text = wp_kses_post($settings['fallback_text']);
        $fallback_template = $settings['fallback_template'];
        $this->add_render_attribute('container', 'class', 'dce-dynamic-google-maps-fallback');
        ?>

		<div <?php 
        echo $this->get_render_attribute_string('container');
        ?>>
			<?php 
        if (isset($fallback_type) && $fallback_type === 'template') {
            $fallback_content = '[dce-elementor-template id="' . $fallback_template . '"]';
        } else {
            $fallback_content = '<p>' . $fallback_text . '</p>';
        }
        echo do_shortcode($fallback_content);
        ?>
		</div>

		<?php 
    }
    /**
     * If Search & Filter Pro is used add a CSS class to the wrapper
     *
     * @return void
     */
    protected function add_search_filter_class()
    {
        $search_filter_id = $this->get_settings_for_display('search_filter_id');
        $search_filter_id = apply_filters('wpml_object_id', $search_filter_id, 'search-filter-widget', \true);
        if ($this->get_settings('query_type') === 'search_filter' || isset($search_filter_id)) {
            $sfid = \intval($search_filter_id);
            $element_class = 'search-filter-results-' . $sfid;
            $args = array('class' => array($element_class));
            $this->add_render_attribute('_wrapper', $args);
        }
    }
}
