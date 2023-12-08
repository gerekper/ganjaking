<?php

namespace ACA\WC\Editing\ProductSubscription;

use AC;
use AC\Helper\Select\Options\Paginated;
use ACA\WC;
use ACA\WC\Editing\EditValue;
use ACA\WC\Editing\StorageModel;
use ACP\Editing\PaginatedOptions;
use ACP\Editing\Service\Basic;
use ACP\Editing\Service\Editability;
use ACP\Editing\Storage\Post\Meta;
use ACP\Editing\View\AjaxSelect;

class Expires extends Basic implements PaginatedOptions, Editability
{

    use WC\Editing\Product\ProductNotSupportedReasonTrait;
    use ProductSubscriptionEditableTrait;

    public function __construct()
    {
        parent::__construct(
            (new AjaxSelect())->set_clear_button(true),
            new Meta('_subscription_length')
        );
    }

    public function get_paginated_options(string $search, int $page, int $id = null): Paginated
    {
        $period = $id
            ? get_post_meta($id, '_subscription_period', true)
            : 'day';

        return new AC\Helper\Select\Options\Paginated(
            new WC\Helper\Select\SinglePage(),
            AC\Helper\Select\Options::create_from_array(wcs_get_subscription_ranges($period))
        );
    }

}