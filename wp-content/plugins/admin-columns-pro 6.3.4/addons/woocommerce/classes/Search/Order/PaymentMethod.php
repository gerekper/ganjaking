<?php

namespace ACA\WC\Search\Order;

use AC\Helper\Select\Options;
use ACA\WC\Search;
use ACP;
use ACP\Search\Operators;
use ACP\Search\Value;
use WC_Payment_Gateway;

class PaymentMethod extends ACP\Search\Comparison implements ACP\Search\Comparison\Values
{

    public function __construct()
    {
        parent::__construct(
            new Operators([
                Operators::EQ,
                Operators::NEQ,
            ])
        );
    }

    protected function create_query_bindings($operator, Value $value): ACP\Search\Query\Bindings
    {
        $bindings = new ACP\Search\Query\Bindings\QueryArguments();

        $bindings->query_arguments([
            'field_query' => [
                [
                    'field'   => 'payment_method',
                    'value'   => $value->get_value(),
                    'compare' => $operator,
                ],
            ],
        ]);

        return $bindings;
    }

    public function get_values(): Options
    {
        $enabled = [];
        $disabled = [];

        foreach (WC()->payment_gateways()->payment_gateways() as $gateway) {
            if ( ! $gateway instanceof WC_Payment_Gateway) {
                continue;
            }

            if ('yes' === $gateway->enabled) {
                $enabled[$gateway->id] = $gateway->get_title();
            } else {
                $disabled[$gateway->id] = sprintf(
                    '%s (%s)',
                    $gateway->get_title(),
                    __('disabled', 'codepress-admin-columns')
                );
            }
        }

        natcasesort($enabled);
        natcasesort($disabled);

        $options = array_merge($enabled, $disabled);

        return Options::create_from_array($options);
    }

}