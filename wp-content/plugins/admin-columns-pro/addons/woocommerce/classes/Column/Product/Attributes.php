<?php

namespace ACA\WC\Column\Product;

use AC;
use ACA\WC\Editing;
use ACA\WC\Export;
use ACA\WC\Settings;
use ACP;
use ACP\ConditionalFormat\FormattableConfig;
use ACP\ConditionalFormat\Formatter;
use WC_Product_Attribute;

class Attributes extends AC\Column
    implements ACP\Export\Exportable, ACP\Editing\Editable, ACP\Search\Searchable,
               ACP\Sorting\Sortable, ACP\ConditionalFormat\Formattable
{

    public function __construct()
    {
        $this->set_type('column-wc-attributes')
             ->set_label(__('Attributes', 'woocommerce'))
             ->set_group('woocommerce');
    }

    public function conditional_format(): ?FormattableConfig
    {
        if ( ! $this->get_setting_attribute()->get_value()) {
            return new FormattableConfig(new Formatter\FilterHtmlFormatter(new Formatter\StringFormatter()));
        }

        return new FormattableConfig();
    }

    public function get_value($product_id)
    {
        $product_id = (int)$product_id;

        if ($this->is_single_attribute()) {
            return $this->render_single_attribute($product_id);
        }

        return $this->render_multiple_attributes($product_id);
    }

    private function render_single_attribute(int $product_id): string
    {
        $attribute = $this->get_attribute_object($product_id, $this->get_attribute());

        if ( ! $attribute) {
            return $this->get_empty_char();
        }

        if ($attribute->is_taxonomy()) {
            $value = ac_helper()->string->enumeration_list(
                wc_get_product_terms($product_id, $attribute->get_taxonomy(), ['fields' => 'names']),
                'and'
            );
        } else {
            $value = ac_helper()->string->enumeration_list($attribute->get_options(), 'and');
        }

        if ( ! $value) {
            return $this->get_empty_char();
        }

        return $value;
    }

    private function render_multiple_attributes(int $product_id): string
    {
        $rows = [];

        foreach ($this->get_attributes_by_product_id($product_id) as $attribute) {
            if ($attribute->is_taxonomy()) {
                $label = wc_attribute_label($attribute->get_name());
                $options = wc_get_product_terms($product_id, $attribute->get_name(), ['fields' => 'names']);
            } else {
                $label = $attribute->get_name();
                $options = $attribute->get_options();
            }

            $tooltip = $this->get_tooltip($attribute);

            if ($label && $tooltip) {
                $label = '<span ' . ac_helper()->html->get_tooltip_attr($tooltip) . '">' . esc_html($label) . '</span>';
            }

            $rows[] = '
				<div class="attribute">
					<strong class="label">' . $label . ':</strong>
					<span class="values">' . implode($this->get_separator(), $options) . '</span>
				</div>
				';
        }

        $rows = array_filter($rows);

        if ( ! $rows) {
            return $this->get_empty_char();
        }

        return implode($rows);
    }

    private function get_attributes_by_product_id(int $id): array
    {
        return wc_get_product($id)->get_attributes();
    }

    private function get_attribute_object(int $product_id, string $attribute = null): ?WC_Product_Attribute
    {
        $attributes = $this->get_attributes_by_product_id($product_id);

        return $attributes[$attribute] ?? null;
    }

    private function is_single_attribute(): bool
    {
        return (bool)$this->get_attribute();
    }

    private function get_tooltip(WC_Product_Attribute $attribute): string
    {
        // Tooltip
        $tooltip = [];

        if ($attribute->get_visible()) {
            $tooltip[] = __('Visible on the product page', 'woocommerce');
        }

        if ($attribute->get_variation()) {
            $tooltip[] = __('Used for variations', 'woocommerce');
        }

        if ($attribute->is_taxonomy()) {
            $tooltip[] = __('Is a taxonomy', 'codepress-admin-columns');
        }

        return implode('<br/>', $tooltip);
    }

    /**
     * @param int $id
     *
     * @return WC_Product_Attribute[]
     */
    public function get_raw_value($id)
    {
        $attributes = wc_get_product($id)->get_attributes();
        if ( ! $attributes) {
            $attributes = [];
        }

        if ($this->get_attribute()) {
            $value = [];

            if (isset($attributes[$this->get_attribute()])) {
                $value = [
                    $this->get_attribute() => $attributes[$this->get_attribute()],
                ];
            }
        } else {
            $value = $attributes;
        }

        return $value;
    }

    public function register_settings()
    {
        $this->add_setting(new Settings\Product\Attributes($this));

        if ($this->is_taxonomy_attribute()) {
            $this->add_setting((new ACP\Editing\Settings\Factory\Taxonomy($this))->create());
        }
    }

    public function export()
    {
        return new Export\Product\Attributes($this);
    }

    public function editing()
    {
        if ($this->is_taxonomy_attribute()) {
            return new Editing\Product\Attributes\Taxonomy(
                $this->get_attribute(),
                'on' === $this->get_option('enable_term_creation')
            );
        }

        if ($this->is_custom_attribute()) {
            return new ACP\Editing\Service\Basic(
                new ACP\Editing\View\MultiInput(),
                new Editing\Storage\Product\Attributes\Custom(
                    $this->get_attribute(),
                    $this->get_setting_attribute()->get_attributes_custom_labels()
                )
            );
        }

        return false;
    }

    public function search()
    {
        if ($this->is_taxonomy_attribute()) {
            return new ACP\Search\Comparison\Post\Taxonomy($this->get_taxonomy());
        }

        return false;
    }

    public function sorting()
    {
        if ($this->is_taxonomy_attribute()) {
            return new ACP\Sorting\Model\Post\Taxonomy($this->get_taxonomy());
        }

        return null;
    }

    /**
     * @return false|string
     */
    public function get_taxonomy()
    {
        return $this->is_taxonomy_attribute()
            ? $this->get_attribute()
            : false;
    }

    /**
     * @return Settings\Product\Attributes|false
     */
    public function get_setting_attribute()
    {
        $setting = $this->get_setting('product_attributes');

        if ( ! $setting instanceof Settings\Product\Attributes) {
            return false;
        }

        return $setting;
    }

    /**
     * @return string
     */
    public function get_attribute()
    {
        return $this->get_setting_attribute()->get_product_taxonomy_display();
    }

    private function is_taxonomy_attribute(): bool
    {
        $taxonomies = $this->get_setting_attribute()->get_attributes_taxonomy_labels();

        return $this->get_attribute() && isset($taxonomies[$this->get_attribute()]);
    }

    private function is_custom_attribute(): bool
    {
        return $this->get_attribute() && ! $this->is_taxonomy_attribute();
    }

}