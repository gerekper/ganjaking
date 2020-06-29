<?php


namespace Premmerce\PrimaryCategory;

use Premmerce\PrimaryCategory\Model\Model;

/**
 * Class EventsTracker
 * @package Premmerce\PrimaryCategory
 *
 * This class is responsible for tracking categories and products updates and triggering model to update product-category relations
 */
class EventsTracker
{
    /**
     * @var Model
     */
    private $model;

    /**
     * EventsTracker constructor.
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;

        $this->setEventHandlers();

    }

    /**
     * Track category deleting and changing product categories
     */
    public function setEventHandlers()
    {
        add_action('delete_term', [$this->model, 'cleanDeletedCategoryInProductsMeta'], 10, 2);
        add_action('woocommerce_update_product', [$this->model, 'cleanProductPrimaryCategoryIfDeleted']);
    }
}