<?php

namespace MailOptin\Core\Logging;

interface PersistenceInterface
{
    public function persist(CampaignLogInterface $data);

    public function retrieve($ids);

    public function retrieveAll();

    public function updateColumn($id, $column, $value);

    public function retrieveColumn($id, $column);
}