<?php

namespace ACA\WC\Column\Product;

use AC;
use ACA\WC\ConditionalFormat\Formatter\Product\SaleFormatter;
use ACA\WC\Editing;
use ACA\WC\Export;
use ACA\WC\Search;
use ACP;
use ACP\ConditionalFormat\FormattableConfig;
use ACP\Sorting\Type\DataType;
use Exception;
use WC_DateTime;
use WC_Product;
use WC_Product_Variable;

class Sale extends AC\Column
    implements ACP\Editing\Editable, ACP\Export\Exportable, ACP\Search\Searchable,
               ACP\Sorting\Sortable, ACP\ConditionalFormat\Formattable
{

    public function __construct()
    {
        $this->set_type('column-wc-product_sale')
             ->set_label(__('Sale Price', 'codepress-admin-columns'))
             ->set_group('woocommerce');
    }

    public function get_value($id)
    {
        $product = wc_get_product($id);

        $is_scheduled = $this->is_scheduled($product);
        $is_future_sale = $this->is_future_sale($product);
        $is_on_sale = $product->is_on_sale();

        if ( ! $is_on_sale && ! $is_future_sale) {
            return $this->get_empty_char();
        }

        switch ($product->get_type()) {
            case 'variable' :

                /**
                 * @var WC_Product_Variable $product
                 */
                $range = array_filter([
                    $product->get_variation_sale_price(),
                    $product->get_variation_sale_price('max'),
                ]);

                $range = array_unique($range);
                $range = array_map('wc_price', $range);
                $price = implode(' - ', $range);
                break;

            default:
                /** @var WC_Product $product */
                $price = wc_price($product->get_sale_price());
        }

        if ($is_scheduled) {
            $icon = ac_helper()->icon->dashicon(['icon' => 'clock']);

            $tooltip_title = __('Scheduled');

            if ($is_on_sale) {
                $icon = ac_helper()->icon->dashicon(['icon' => 'clock', 'class' => 'green']);
                $tooltip_title = sprintf('%s &amp; %s', $tooltip_title, __('Active'));
            }

            return ac_helper()->html->tooltip(
                sprintf('%s %s', $price, $icon),
                sprintf('<strong>%s</strong><br><em>%s</em>', $tooltip_title, $this->get_scheduled_label($product))
            );
        }

        return $price;
    }

    private function format_scheduled_label($label, WC_DateTime $date_time = null)
    {
        if ( ! $date_time) {
            return false;
        }

        return sprintf(
            '%s: %s',
            $label,
            $date_time->format(get_option('date_format'))
        );
    }

    /**
     * Returns a formatted period
     *
     * @param WC_Product $product
     *
     * @return string
     */
    private function get_scheduled_label(WC_Product $product)
    {
        $labels = [
            $this->format_scheduled_label(
                _x('From', 'Product on sale from (date)', 'codepress-admin-columns'),
                $product->get_date_on_sale_from()
            ),
            $this->format_scheduled_label(
                _x('Until', 'Product on sale until (date)', 'codepress-admin-columns'),
                $product->get_date_on_sale_to()
            ),
        ];

        if ( ! array_filter($labels)) {
            return false;
        }

        return implode('<br>', array_filter($labels));
    }

    /**
     * Sales price is scheduled for future
     *
     * @param WC_Product $product
     *
     * @return bool
     */
    public function is_future_sale(WC_Product $product)
    {
        try {
            $date = new WC_DateTime();
        } catch (Exception $e) {
            return false;
        }

        return $product->get_date_on_sale_from() && $product->get_date_on_sale_from() > $date;
    }

    public function is_scheduled(WC_Product $product)
    {
        return $product->get_date_on_sale_from() || $product->get_date_on_sale_to();
    }

    public function get_raw_value($id)
    {
        $product = wc_get_product($id);

        return $product->is_on_sale();
    }

    public function editing()
    {
        return new Editing\Product\Price('sale');
    }

    public function search()
    {
        return new Search\Product\Sale();
    }

    public function export()
    {
        return new Export\Product\Sale($this);
    }

    public function sorting()
    {
        return new ACP\Sorting\Model\Post\Meta('_sale_price', new DataType(DataType::NUMERIC));
    }

    public function conditional_format(): ?FormattableConfig
    {
        return new FormattableConfig(new SaleFormatter());
    }

}