<?php

namespace MailPoet\Statistics;

if (!defined('ABSPATH')) exit;


use MailPoet\Doctrine\Repository;
use MailPoet\Entities\StatisticsWooCommercePurchaseEntity;

/**
 * @extends Repository<StatisticsWooCommercePurchaseEntity>
 */
class StatisticsWooCommercePurchasesRepository extends Repository {
  protected function getEntityClassName() {
    return StatisticsWooCommercePurchaseEntity::class;
  }
}
