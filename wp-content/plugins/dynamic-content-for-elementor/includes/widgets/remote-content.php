<?php

namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly
class RemoteContent extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    const CACHE_MAX_AGES = ['1m' => 60, '5m' => 60 * 5, '15m' => 60 * 15, '1h' => 60 * 60, '6h' => 60 * 60 * 6, '12h' => 60 * 60 * 12, '24h' => 60 * 60 * 24];
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $this->start_controls_section('section_remotecontent', ['label' => $this->get_title(), 'tab' => Controls_Manager::TAB_CONTENT]);
        $this->add_control('url', ['label' => __('URL', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '']);
        $this->add_control('incorporate', ['label' => __('Incorporate in the page', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'description' => __('Insert remote content in the HTML page or add it as an iframe', 'dynamic-content-for-elementor')]);
        $this->add_control('method', ['label' => __('Method', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'default' => 'GET', 'options' => ['GET' => 'GET', 'POST' => 'POST'], 'condition' => ['incorporate!' => '']]);
        $this->add_control('headers', ['label' => __('Headers', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXTAREA, 'placeholder' => 'Authorization: <type> <token>', 'description' => __('Please use the format "Key: value", one per line', 'dynamic-content-for-elementor'), 'rows' => '3', 'condition' => ['incorporate!' => '']]);
        $repeater_parameters = new \Elementor\Repeater();
        $repeater_parameters->add_control('key', ['label' => __('Key', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT]);
        $repeater_parameters->add_control('value', ['label' => __('Value', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT]);
        $this->add_control('parameters', ['label' => __('Parameters', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::REPEATER, 'fields' => $repeater_parameters->get_controls(), 'title_field' => '{{{ key }}} = {{{ value }}}', 'prevent_empty' => \false, 'item_actions' => ['add' => \true, 'duplicate' => \false, 'remove' => \true, 'sort' => \true], 'condition' => ['incorporate!' => '']]);
        $this->add_control('parameters_json_encode', ['label' => __('Encode parameters in JSON', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'default ' => 'yes', 'condition' => ['incorporate!' => '', 'method' => 'POST']]);
        $this->add_control('require_authorization', ['label' => __('Require Authorization', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['incorporate!' => '']]);
        $this->add_control('authorization_user', ['label' => __('Basic HTTP User', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'condition' => ['require_authorization' => 'yes', 'incorporate!' => '']]);
        $this->add_control('authorization_pass', ['label' => __('Basic HTTP Password', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'condition' => ['require_authorization' => 'yes', 'incorporate!' => '']]);
        $this->add_control('connect_timeout', ['label' => __('Connection Timeout (s)', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'default' => 5, 'min' => 0, 'max' => 30, 'description' => __('Time period within which the connection between your server and the endpoint must be established', 'dynamic-content-for-elementor'), 'condition' => ['incorporate!' => '']]);
        $this->add_control('data_cache', ['label' => __('Cache', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['incorporate!' => '']]);
        $this->add_control('cache_age', ['label' => __('Store in cache for', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => $this->get_cache_age_options(), 'default' => '5m', 'label_block' => \true, 'condition' => ['data_cache!' => '', 'incorporate!' => '']]);
        $this->add_control('iframe_doc', ['label' => __('Use Google Document preview', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['incorporate' => '']]);
        $this->add_responsive_control('iframe_height', ['label' => __('Height', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => '80', 'unit' => 'vh'], 'range' => ['px' => ['min' => 0, 'max' => 1920, 'step' => 1], '%' => ['min' => 5, 'max' => 100, 'step' => 1], 'vh' => ['min' => 5, 'max' => 100, 'step' => 1]], 'size_units' => ['%', 'px', 'vh'], 'selectors' => ['{{WRAPPER}} iframe' => 'height: {{SIZE}}{{UNIT}};'], 'condition' => ['incorporate' => '']]);
        $this->end_controls_section();
        $this->start_controls_section('section_data', ['label' => __('Data', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_CONTENT, 'condition' => ['incorporate!' => '']]);
        $this->add_control('data_json', ['label' => __('Data is JSON formatted', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'condition' => ['incorporate!' => '']]);
        $this->add_control('tag_id', ['label' => __('Tag, ID or Class', 'dynamic-content-for-elementor'), 'description' => __('To include only subcontent of remote page. Use like jQuery selector (footer, #element, h2.big, etc).', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'placeholder' => 'body', 'default' => 'body', 'condition' => ['incorporate!' => '', 'data_json' => '']]);
        $this->add_control('limit_tags', ['label' => __('Limit elements', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'description' => __('Set -1 for unlimited', 'dynamic-content-for-elementor'), 'default' => -1, 'condition' => ['incorporate!' => '', 'data_json' => '']]);
        $this->add_control('data_template', ['label' => __('Tokens', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CODE, 'default' => '<div class="dce-remote-content"><h3 class="dce-remote-content-title">[DATA:title:rendered]</h3><div class="dce-remote-content-body">[DATA:excerpt:rendered]</div><a class="btn btn-primary" href="[DATA:link]">Read more</a></div>', 'description' => __('Add a specific format to data elements. Use Tokens to represent JSON fields.', 'dynamic-content-for-elementor'), 'condition' => ['incorporate!' => '', 'data_json' => 'yes']]);
        $this->add_control('single_or_archive', ['label' => __('Single or Archive', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'label_off' => __('Archive', 'dynamic-content-for-elementor'), 'label_on' => __('Single', 'dynamic-content-for-elementor'), 'default' => 'yes', 'condition' => ['incorporate!' => '', 'data_json' => 'yes']]);
        $this->add_control('archive_path', ['label' => __('Archive Array path', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXT, 'default' => '', 'description' => __('Leave empty if JSON result is a direct array (like in WP API). For web services usually might use "results". You can browse sub arrays separate them by comma like "data.people"', 'dynamic-content-for-elementor'), 'condition' => ['incorporate!' => '', 'data_json' => 'yes', 'single_or_archive' => '']]);
        $this->add_control('limit_contents', ['label' => __('Limit elements', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'description' => __('Set -1 for unlimited', 'dynamic-content-for-elementor'), 'default' => -1, 'condition' => ['incorporate!' => '', 'single_or_archive' => '']]);
        $this->add_control('offset_contents', ['label' => __('Start from', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'min' => 0, 'description' => __('0 or empty to start from the first', 'dynamic-content-for-elementor'), 'default' => -1, 'condition' => ['incorporate!' => '', 'single_or_archive' => '']]);
        $this->end_controls_section();
        $this->start_controls_section('section_html_manipulation', ['label' => __('HTML Manipulation', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_CONTENT, 'condition' => ['incorporate!' => '']]);
        $this->add_control('fix_links', ['label' => __('Fix Relative links', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => __('Enable if remote page contains relative links', 'dynamic-content-for-elementor')]);
        $this->add_control('blank_links', ['label' => __('Target Blank links', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => __('Open links in a new page', 'dynamic-content-for-elementor')]);
        $this->add_control('lazy_images', ['label' => __('Fix Lazy Images src', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SWITCHER, 'description' => __('Show lazy images without using specific JS', 'dynamic-content-for-elementor')]);
        $this->end_controls_section();
    }
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings['url'])) {
            Helper::notice('', __('Add an URL to begin', 'dynamic-content-for-elementor'));
            return;
        }
        $url = $settings['url'];
        if (!\filter_var($url, \FILTER_VALIDATE_URL)) {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                Helper::notice('', __('URL not valid', 'dynamic-content-for-elementor'));
            }
            return;
        }
        if (!empty($settings['incorporate'])) {
            $args = [];
            // Headers
            if (!empty($settings['headers']) && !\preg_match('/^\\s+$/', $settings['headers'])) {
                if (\Elementor\Plugin::$instance->editor->is_edit_mode() && "'" === \substr($settings['headers'], 0, 1)) {
                    $format = "<br /><pre><code>Key: value\nKey: value</code></pre>";
                    Helper::notice(__('Wrong Headers format', 'dynamic-content-for-elementor'), __('Please use this format', 'dynamic-content-for-elementor') . $format);
                    return;
                }
                $headers = \explode("\n", $settings['headers']);
                foreach ($headers as $header) {
                    $header = \explode(':', $header, 2);
                    if (2 === \count($header)) {
                        $args['headers'][$header[0]] = $header[1];
                    } else {
                        $format = "<br /><pre><code>Key: value\nKey: value</code></pre>";
                        Helper::notice(__('Wrong Headers format', 'dynamic-content-for-elementor'), __('Please use this format', 'dynamic-content-for-elementor') . $format);
                        return;
                    }
                }
            }
            // Parameters
            if (!empty($settings['parameters'])) {
                if ('GET' === $settings['method']) {
                    foreach ($settings['parameters'] as $parameter) {
                        $url = add_query_arg($parameter['key'], $parameter['value'], $url);
                    }
                } elseif ('POST' === $settings['method']) {
                    $parameters = [];
                    foreach ($settings['parameters'] as $parameter) {
                        $parameters[$parameter['key']] = $parameter['value'];
                    }
                    // JSON Encode
                    if (!empty($settings['parameters_json_encode'])) {
                        $args['body'] = wp_json_encode($parameters);
                    } else {
                        $args['body'] = $parameters;
                    }
                }
            }
            // Basic Authentication
            if (!empty($settings['authorization_user']) && !empty($settings['authorization_pass'])) {
                $args['headers']['Authorization'] = 'Basic ' . $settings['authorization_user'] . ' ' . \base64_encode($settings['authorization_pass']);
            }
            // Connection Timeout
            if ($settings['connect_timeout']) {
                $args['timeout'] = \intval($settings['connect_timeout']);
            }
            $cache_age = $settings['cache_age'] ?? '5m';
            if ($settings['data_cache'] !== 'yes') {
                $cache_age = \false;
            }
            $response = $this->get_response($url, $args, $settings['method'], $cache_age);
            if (is_wp_error($response)) {
                if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                    Helper::notice('', __('Can\'t fetch remote content. Please check url', 'dynamic-content-for-elementor'));
                }
                return;
            }
            $page_body = $response;
            if ($settings['data_json']) {
                $jsonData = \json_decode($page_body, \true);
                $page_body = [];
                if ($settings['single_or_archive']) {
                    $page_body[] = $this->replace_template_tokens(Helper::get_dynamic_value($settings['data_template']), $jsonData);
                } else {
                    $json_data_archive = $jsonData;
                    if (isset($settings['archive_path']) && $settings['archive_path']) {
                        $settings['archive_path'] = \str_replace('.', ':', $settings['archive_path']);
                        $pieces = \explode(':', $settings['archive_path']);
                        $tmp_val = \DynamicContentForElementor\Helper::get_array_value_by_keys($jsonData, $pieces);
                        if ($tmp_val) {
                            $json_data_archive = $tmp_val;
                        }
                    }
                    if (!empty($json_data_archive)) {
                        foreach ($json_data_archive as $aJsonData) {
                            $page_body[] = $this->replace_template_tokens(Helper::get_dynamic_value($settings['data_template']), $aJsonData);
                        }
                    }
                }
            } elseif ($settings['tag_id']) {
                $crawler = new \DynamicOOOS\Symfony\Component\DomCrawler\Crawler($response);
                $page_body = [];
                $page_body = $crawler->filter($settings['tag_id'])->each(function (\DynamicOOOS\Symfony\Component\DomCrawler\Crawler $node, $i) {
                    return $node->html();
                });
                if (isset($settings['limit_tags']) && $settings['limit_tags'] > 0 && \count($page_body) > $settings['limit_tags']) {
                    $page_body = \array_slice($page_body, 0, $settings['limit_tags']);
                }
            } else {
                $page_body = array($page_body);
            }
            $host = '';
            if (isset($settings['fix_links']) && $settings['fix_links']) {
                $pieces = \explode('/', $settings['url'], 4);
                \array_pop($pieces);
                $host = \implode('/', $pieces);
            }
            echo '<div class="dynamic-remote-content">';
            $showed = 0;
            foreach ($page_body as $key => $aElem) {
                if ($settings['limit_contents'] <= 0 || $showed <= $settings['limit_contents']) {
                    if ($key >= $settings['offset_contents']) {
                        echo '<div class="dynamic-remote-content-element">';
                        if (isset($settings['fix_links']) && $settings['fix_links']) {
                            $aElem = \str_replace('href="/', 'href="' . $host . '/', $aElem);
                        }
                        if (isset($settings['lazy_images']) && $settings['lazy_images']) {
                            $imgs = \explode('<img ', $aElem);
                            foreach ($imgs as $ikey => $aimg) {
                                if (\strpos($aimg, 'data-lazy-src') !== \false) {
                                    $imgs[$ikey] = \str_replace(' src="', 'data-placeholder-src="', $imgs[$ikey]);
                                    $imgs[$ikey] = \str_replace('data-lazy-src="', 'src="', $imgs[$ikey]);
                                    $imgs[$ikey] = \str_replace('data-lazy-srcset="', 'srcset="', $imgs[$ikey]);
                                    $imgs[$ikey] = \str_replace('data-lazy-sizes="', 'sizes="', $imgs[$ikey]);
                                }
                            }
                            $aElem = \implode('<img ', $imgs);
                        }
                        if (isset($settings['blank_links']) && $settings['blank_links']) {
                            $anchors = \explode('<a ', $aElem);
                            foreach ($anchors as $akey => $anchor) {
                                if (\strpos($anchor, ' target="_') !== \false) {
                                    $anchors[$akey] = 'target="_blank" ' . $anchors[$akey];
                                }
                            }
                            $aElem = \implode('<a ', $anchors);
                        }
                        $aElem = apply_filters('dynamicooo/remote-content/html-element', $aElem);
                        echo $aElem;
                        echo '</div>';
                    }
                    $showed++;
                }
            }
            echo '</div>';
        } else {
            // view as simple iframe
            if ($settings['iframe_doc']) {
                $this->set_render_attribute('iframe', 'src', 'https://docs.google.com/viewer?embedded=true&url=' . \urlencode($url));
            } else {
                $this->set_render_attribute('iframe', 'src', $url);
            }
            $this->set_render_attribute('iframe', 'frameborder', '0');
            $this->set_render_attribute('iframe', 'width', '100%');
            $this->set_render_attribute('iframe', 'height', $settings['iframe_height']['size']);
            $this->set_render_attribute('iframe', 'allowfullscreen', 'true');
            ?>
			<iframe <?php 
            echo $this->get_render_attribute_string('iframe');
            ?>></iframe>
			<?php 
        }
    }
    protected function get_transient_prefix()
    {
        return 'dce_remote_content_';
    }
    protected function get_response($url, $args, $method, $max_age)
    {
        if ($max_age) {
            $md5_url = \md5($url);
            $md5_args = \md5(wp_json_encode($args));
            $transient_key = $this->get_transient_prefix() . "{$max_age}_{$md5_url}_{$md5_args}";
            $transient = get_transient($transient_key);
            if (!\Elementor\Plugin::$instance->editor->is_edit_mode() && $transient !== \false) {
                return \json_decode($transient, \true);
            }
        }
        if ('POST' === $method) {
            $response = wp_remote_retrieve_body(wp_safe_remote_post($url, $args));
        } else {
            $response = wp_remote_retrieve_body(wp_safe_remote_get($url, $args));
        }
        if ($max_age && !\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            set_transient($transient_key, wp_json_encode($response), self::CACHE_MAX_AGES[$max_age]);
        }
        return $response;
    }
    protected function get_cache_age_options()
    {
        return ['1m' => __('1 Minute', 'dynamic-content-for-elementor'), '5m' => __('5 Minutes', 'dynamic-content-for-elementor'), '15m' => __('15 Minutes', 'dynamic-content-for-elementor'), '1h' => __('1 Hour', 'dynamic-content-for-elementor'), '6h' => __('6 Hours', 'dynamic-content-for-elementor'), '12h' => __('12 Hours', 'dynamic-content-for-elementor'), '24h' => __('24 Hours', 'dynamic-content-for-elementor')];
    }
    protected function replace_template_tokens($text, $content)
    {
        $text = \DynamicContentForElementor\Tokens::replace_var_tokens($text, 'DATA', $content);
        $pieces = \explode('[', $text);
        if (\count($pieces) > 1) {
            foreach ($pieces as $key => $avalue) {
                if ($key) {
                    $piece = \explode(']', $avalue);
                    $meta_params = \reset($piece);
                    $option_params = \explode('.', $meta_params);
                    $field_name = $option_params[0];
                    $option_value = isset($content[$field_name]) ? $content[$field_name] : '';
                    $replace_value = $this->check_array_value($option_value, $option_params);
                    $text = \str_replace('[' . $meta_params . ']', $replace_value, $text);
                }
            }
        }
        return $text;
    }
    private function check_array_value($option_value = [], $option_params = [])
    {
        if (\is_array($option_value)) {
            if (1 === \count($option_value)) {
                $tmpValue = \reset($option_value);
                if (!\is_array($tmpValue)) {
                    return $tmpValue;
                }
            }
            if (\is_array($option_params)) {
                $val = $option_value;
                foreach ($option_params as $key => $value) {
                    if (isset($val[$value])) {
                        $val = $val[$value];
                    }
                }
                if (\is_array($val)) {
                    $val = \var_export($val, \true);
                }
                return $val;
            }
            if ($option_params) {
                return $option_value[$option_params];
            }
            return \var_export($option_value, \true);
        }
        return $option_value;
    }
}
