<?php

namespace MailPoet\Newsletter\Options;

if (!defined('ABSPATH')) exit;


use MailPoet\Doctrine\Repository;
use MailPoet\Entities\NewsletterOptionEntity;

/**
 * @extends Repository<NewsletterOptionEntity>
 */
class NewsletterOptionsRepository extends Repository {
  protected function getEntityClassName() {
    return NewsletterOptionEntity::class;
  }
}
