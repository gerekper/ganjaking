<?php

namespace MailPoet\Premium\Newsletter;

if (!defined('ABSPATH')) exit;


use MailPoet\Doctrine\Repository;
use MailPoet\Entities\StatisticsOpenEntity;

/**
 * @extends Repository<StatisticsOpenEntity>
 */
class StatisticsOpensRepository extends Repository {
  protected function getEntityClassName() {
    return StatisticsOpenEntity::class;
  }
}
