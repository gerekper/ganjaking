<?php
/**
 * Copyright (C) 2016  Agbonghama Collins <me@w3guy.com>
 */

namespace MailOptin\Tests\Core\Repositories;

use MailOptin\Core\Repositories\EmailCampaignRepository;
use WP_UnitTestCase;

class EmailCampaignRepositoryTest extends WP_UnitTestCase
{
    public $insert_data;

    public function setUp()
    {
        parent::setUp();

        $this->insert_data = [
            'name' => 'Instant New Post Campaign',
            'campaign_type' => 'new_publish_post',
            'template_class' => 'Lucid'
        ];
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    public function testAddEmailCampaign()
    {
        $response = EmailCampaignRepository::add_email_campaign(
            $this->insert_data['name'],
            $this->insert_data['campaign_type'],
            $this->insert_data['template_class']
        );

        $this->assertInternalType('integer', $response);
    }

    public function testGetEmailCampaignById()
    {
        $id = EmailCampaignRepository::add_email_campaign(
            $this->insert_data['name'],
            $this->insert_data['campaign_type'],
            $this->insert_data['template_class']
        );

        $result = EmailCampaignRepository::get_email_campaign_by_id($id);

        $this->assertInternalType('array', $result);
        $this->assertSame($id, absint($result['id']));
    }

    public function test_update_campaign_name()
    {
        $id = EmailCampaignRepository::add_email_campaign(
            $this->insert_data['name'],
            $this->insert_data['campaign_type'],
            $this->insert_data['template_class']
        );

        $result = EmailCampaignRepository::update_campaign_name(
            'ProfilePress new latest post',
            $id
        );

        $this->assertInternalType('integer', $result);
        $this->assertSame(1, $result);
    }

    public function testDeleteCampaignById()
    {
        $id = EmailCampaignRepository::add_email_campaign(
            $this->insert_data['name'],
            $this->insert_data['campaign_type'],
            $this->insert_data['template_class']
        );

        $result = EmailCampaignRepository::delete_campaign_by_id($id);

        $this->assertInternalType('integer', $result);
        $this->assertSame(1, $result);
    }

    public function testGetByEmailCampaignType()
    {
        EmailCampaignRepository::add_email_campaign(
            $this->insert_data['name'],
            $this->insert_data['campaign_type'],
            $this->insert_data['template_class']
        );

        $result = EmailCampaignRepository::get_by_email_campaign_type('new_publish_post');

        $this->assertInternalType('array', $result);
        $this->assertSame($this->insert_data['name'], $result[0]['name']);
    }
}