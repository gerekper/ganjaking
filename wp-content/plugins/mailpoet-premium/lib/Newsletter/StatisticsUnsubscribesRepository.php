<?php

namespace MailPoet\Premium\Newsletter;

if (!defined('ABSPATH')) exit;


use MailPoet\Doctrine\Repository;
use MailPoet\Entities\StatisticsUnsubscribeEntity;

/**
 * @extends Repository<StatisticsUnsubscribeEntity>
 */
class StatisticsUnsubscribesRepository extends Repository {
  protected function getEntityClassName() {
    return StatisticsUnsubscribeEntity::class;
  }
}
