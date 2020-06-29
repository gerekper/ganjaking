<?php

namespace MailOptin\Core\Logging;


class CampaignLogRepository
{
    private $persistenceGateway;

    public function __construct(PersistenceInterface $persistenceGateway)
    {
        $this->persistenceGateway = $persistenceGateway;
    }

    /**
     * @param CampaignLogInterface $campaign
     *
     * @return mixed
     */
    public function save(CampaignLogInterface $campaign)
    {
        return $this->persistenceGateway->persist($campaign);
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function getById($id)
    {
        return $this->persistenceGateway->retrieve($id);
    }

    public function getAll()
    {
        return $this->persistenceGateway->retrieveAll();
    }

    public function updateStatus($id, $status)
    {
        return $this->persistenceGateway->updateColumn($id, 'status', $status);
    }

    public function updateNote($id, $note)
    {
        return $this->persistenceGateway->updateColumn($id, 'note', $note);
    }

    public function updateStatusTime($id, $time)
    {
        return $this->persistenceGateway->updateColumn($id, 'status_time', $time);
    }

    public function retrieveEmailCampaignId($id)
    {
        return $this->persistenceGateway->retrieveColumn($id, 'email_campaign_id');
    }

    public function retrieveTitle($id)
    {
        return $this->persistenceGateway->retrieveColumn($id, 'title');
    }

    public function retrieveContentHtml($id)
    {
        return $this->persistenceGateway->retrieveColumn($id, 'content_html');
    }

    public function retrieveContentText($id)
    {
        return $this->persistenceGateway->retrieveColumn($id, 'content_text');
    }

    public function retrieveStatus($id)
    {
        return $this->persistenceGateway->retrieveColumn($id, 'status');
    }

    public function retrieveStatusTime($id)
    {
        return $this->persistenceGateway->retrieveColumn($id, 'status_time');
    }

    public function retrieveNote($id)
    {
        return $this->persistenceGateway->retrieveColumn($id, 'note');
    }

    /**
     * Singleton instance of the class
     *
     * @return CampaignLogRepository
     */
    public static function instance()
    {
        static $instance = null;

        if (is_null($instance)) {
            $instance = new self(CampaignLogPersistence::instance());
        }

        return $instance;
    }

}