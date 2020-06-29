<?php

namespace MailOptin\Tests\Core\Repositories;

use MailOptin\Core\Repositories\OptinThemesRepository;
use WP_UnitTestCase;


class OptinThemesRepositoryTest extends WP_UnitTestCase
{
    public $kick_theme;

    public function setUp()
    {
        parent::setUp();

        $this->kick_theme = array(
            'name' => 'Kick',
            'optin_class' => 'kick',
            'optin_type' => 'kick',
            'screenshot' => ''
        );
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    public function testGetAll()
    {
        $data = OptinThemesRepository::get_all();

        $this->assertEquals(
            array(
                'name' => 'BareMetal',
                'optin_class' => 'BareMetal',
                'optin_type' => 'lightbox',
                'screenshot' => MAILOPTIN_ASSETS_URL . 'images/optin-themes/baremetal-lightbox.png'
            ),
            $data[0]
        );
    }

    public function testGetByType()
    {
        // add kick optin theme to theme repo
        OptinThemesRepository::add($this->kick_theme);

        $data = OptinThemesRepository::get_by_type('kick');

        $this->assertEquals([$this->kick_theme], $data);
    }

    public function testGetByName()
    {
        // add kick optin theme to theme repo
        OptinThemesRepository::add($this->kick_theme);

        $data = OptinThemesRepository::get_by_name('Kick');

        $this->assertEquals($this->kick_theme, $data);
    }

    public function testDeleteByName()
    {
        OptinThemesRepository::add($this->kick_theme);

        OptinThemesRepository::delete_by_name('Kick');

        $this->assertNull(OptinThemesRepository::get_by_type('kick'));
    }
}