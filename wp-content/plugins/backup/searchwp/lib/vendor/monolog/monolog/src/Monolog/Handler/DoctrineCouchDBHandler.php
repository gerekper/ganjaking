<?php

declare (strict_types=1);
/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SearchWP\Dependencies\Monolog\Handler;

use SearchWP\Dependencies\Monolog\Logger;
use SearchWP\Dependencies\Monolog\Formatter\NormalizerFormatter;
use SearchWP\Dependencies\Monolog\Formatter\FormatterInterface;
use SearchWP\Dependencies\Doctrine\CouchDB\CouchDBClient;
/**
 * CouchDB handler for Doctrine CouchDB ODM
 *
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class DoctrineCouchDBHandler extends \SearchWP\Dependencies\Monolog\Handler\AbstractProcessingHandler
{
    private $client;
    public function __construct(\SearchWP\Dependencies\Doctrine\CouchDB\CouchDBClient $client, $level = \SearchWP\Dependencies\Monolog\Logger::DEBUG, bool $bubble = \true)
    {
        $this->client = $client;
        parent::__construct($level, $bubble);
    }
    /**
     * {@inheritDoc}
     */
    protected function write(array $record) : void
    {
        $this->client->postDocument($record['formatted']);
    }
    protected function getDefaultFormatter() : \SearchWP\Dependencies\Monolog\Formatter\FormatterInterface
    {
        return new \SearchWP\Dependencies\Monolog\Formatter\NormalizerFormatter();
    }
}
