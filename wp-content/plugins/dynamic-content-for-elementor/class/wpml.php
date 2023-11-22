<?php

namespace DynamicContentForElementor;

if (!\defined('ABSPATH')) {
    exit;
}
class Wpml
{
    /**
     * Extensions Fields
     *
     * @var array<string,mixed>
     */
    protected $extensions_fields = [];
    /**
     * Form Fields
     *
     * @var array<string,mixed>
     */
    protected $form_fields = [];
    public function __construct()
    {
        // Translate Extensions
        add_filter('wpml_elementor_widgets_to_translate', [$this, 'translate_extensions'], 10, 1);
        // TODO: Translate Extensions for Elementor Pro Form
        // add_filter( 'wpml_elementor_widgets_to_translate', [ $this, 'translate_form_extensions' ], 30, 1 ); // Set priority to 30 so we don't override the configuration of 'form' widget
    }
    /**
     * Add Fields for Extensions
     *
     * @param array<string,mixed> $fields
     * @return void
     */
    public function add_extensions_fields(array $fields)
    {
        if (empty($fields)) {
            return;
        }
        $this->extensions_fields += $fields;
    }
    /**
     * Add Fields for Elementor Pro Form widget
     *
     * @param array<string,mixed> $fields
     * @return void
     */
    public function add_form_fields(array $fields)
    {
        if (empty($fields)) {
            return;
        }
        $this->extensions_fields += $fields;
    }
    /**
     * Get Extensions Fields
     *
     * @return array<string,mixed>
     */
    protected function get_extensions_fields()
    {
        return $this->extensions_fields;
    }
    /**
     * Translate Extensions
     *
     * @param array<string,mixed> $widgets
     * @return array<string,mixed>
     */
    public function translate_extensions(array $widgets)
    {
        foreach ($widgets as &$widget) {
            if (!\array_key_exists('fields', $widget)) {
                $widget['fields'] = [];
            }
            $widget['fields'] += $this->get_extensions_fields();
        }
        return $widgets;
    }
    /**
     * Translate Form Extensions
     *
     * @param array<string,mixed> $widgets
     * @return array<string,mixed>
     */
    // public function translate_form_extensions( $widgets ) {
    // 	if ( isset( $widgets['form'] ) ) {
    // 		$widgets['form']['fields'] += $this->get_form_fields();
    // 	}
    // 	return $widgets;
    // }
}
