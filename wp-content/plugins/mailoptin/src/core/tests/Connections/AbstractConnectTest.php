<?php

namespace MailOptin\Tests\Core\Repositories;

use MailOptin\Core\Connections\AbstractConnect;
use WP_UnitTestCase;

class AbstractConnectTest extends WP_UnitTestCase
{
    public $instance;

    public function setUp()
    {
        parent::setUp();
    }

    public function tearDown()
    {
        $this->unlink(MAILOPTIN_OPTIN_ERROR_LOG.'mailchimp.log');
        parent::tearDown();
    }


    public function testSaveOptinErrorLog()
    {
        AbstractConnect::save_optin_error_log('hello', 'mailchimp');
        AbstractConnect::save_optin_error_log('hi', 'mailchimp');

        $content = file_get_contents(MAILOPTIN_OPTIN_ERROR_LOG.'mailchimp.log');

        $this->assertSame('hello'. "\r\n" . 'hi'. "\r\n", $content);
    }
}