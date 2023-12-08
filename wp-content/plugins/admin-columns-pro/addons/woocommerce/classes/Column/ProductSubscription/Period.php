<?php

namespace ACA\WC\Column\ProductSubscription;

use AC;
use ACA\WC\Editing;
use ACA\WC\Search;
use ACP;
use WC_Product_Subscription;

/**
 * @since 3.4
 */
class Period extends AC\Column
    implements ACP\Editing\Editable, ACP\Search\Searchable, ACP\ConditionalFormat\Formattable
{

    use ACP\ConditionalFormat\ConditionalFormatTrait;

    public function __construct()
    {
        $this->set_type('column-wc-subscription-period')
             ->set_label(__('Price Period', 'codepress-admin-columns'))
             ->set_group('woocommerce_subscriptions');
    }

    public function get_value($id)
    {
        $product = wc_get_product($id);

        if ( ! $product instanceof WC_Product_Subscription) {
            return $this->get_empty_char();
        }

        $interval = $this->get_interval_label($product);
        $period = $this->get_period_label($product);

        if ( ! $period || ! $interval) {
            return $this->get_empty_char();
        }

        return sprintf('%s %s', $interval, $period);
    }

    /**
     * @param WC_Product_Subscription $product
     *
     * @return string|null
     */
    protected function get_interval_label(WC_Product_Subscription $product)
    {
        $period_interval = wcs_get_subscription_period_interval_strings(
            $product->get_meta('_subscription_period_interval')
        );

        if (is_array($period_interval)) {
            return null;
        }

        return ucfirst($period_interval);
    }

    /**
     * @param WC_Product_Subscription $product
     *
     * @return string|null
     */
    protected function get_period_label(WC_Product_Subscription $product)
    {
        $period = $product->get_meta('_subscription_period');
        $periods = wcs_get_subscription_period_strings();

        if ( ! array_key_exists($period, $periods)) {
            return null;
        }

        return $periods[$period];
    }

    public function editing()
    {
        return new Editing\ProductSubscription\Period();
    }

    public function search()
    {
        return new Search\ProductSubscription\Period();
    }

}